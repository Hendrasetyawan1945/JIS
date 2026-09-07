<?php

namespace Tests\Feature;

use App\Http\Controllers\ChatController;
use App\Models\ChatSession;
use App\Models\Wisata;
use App\Services\LlmService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatLogicTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    /**
     * Test Masalah 1: Pengguna dengan lokasi di luar Padang (misal Jakarta)
     * tetap mendapatkan rekomendasi wisata dan jarak_km terhitung,
     * tidak diblokir menjadi 0 hasil.
     */
    public function test_user_di_luar_padang_tetap_mendapatkan_rekomendasi_wisata(): void
    {
        $controller = app(ChatController::class);
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('queryWisata');
        $method->setAccessible(true);

        // Koordinat Monas Jakarta: -6.1754, 106.8272 (>900 km dari Padang)
        $latJakarta = '-6.1754';
        $lngJakarta = '106.8272';

        $intent = [
            'kategori' => 'Pantai',
            'radius_km' => 20,
            'nama_wisata' => null,
            'kata_kunci' => null,
            'wilayah' => null,
            'gratis' => false,
            'max_harga' => null,
            'buka_24_jam' => false,
            'jam_sekarang' => false,
            'urutan' => null,
        ];

        $hasil = $method->invoke($controller, $intent, $latJakarta, $lngJakarta, true);

        $this->assertNotEmpty($hasil, 'Rekomendasi pantai tidak boleh kosong meski user di Jakarta');
        $this->assertNotNull($hasil[0]['jarak_km'], 'Jarak km harus tetap terhitung sebagai referensi');
        $this->assertGreaterThan(500, $hasil[0]['jarak_km'], 'Jarak dari Jakarta ke Padang harus > 500 km');
    }

    /**
     * Test Masalah 2: Pencarian nama spesifik yang tidak ada di nama persis
     * tetapi ada di deskripsi (misal: "Pantai Malin Kundang" -> ada di deskripsi Pantai Air Manis).
     */
    public function test_pencarian_nama_dengan_fallback_deskripsi_berhasil(): void
    {
        $controller = app(ChatController::class);
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('queryWisata');
        $method->setAccessible(true);

        $intent = [
            'nama_wisata' => 'Pantai Malin Kundang',
            'kategori' => 'Pantai',
            'radius_km' => 20,
            'nama' => 'Pantai Malin Kundang',
            'query_bebas' => 'pantai malin kundang',
        ];

        $hasil = $method->invoke($controller, $intent, null, null, false);

        $this->assertNotEmpty($hasil, 'Harus menemukan wisata dengan fallback deskripsi');
        $this->assertSame('Pantai Air Manis', $hasil[0]['nama'], 'Harus menemukan Pantai Air Manis');
    }

    /**
     * Test Masalah 2 bagian B: Fallback cariNama untuk teks singkat seperti "batu malin kundang".
     */
    public function test_cari_nama_fallback_deskripsi_berhasil(): void
    {
        $controller = app(ChatController::class);
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('cariNama');
        $method->setAccessible(true);

        $namaCocok = $method->invoke($controller, 'batu malin kundang');

        $this->assertNotEmpty($namaCocok);
        $this->assertContains('Pantai Air Manis', $namaCocok);
    }

    /**
     * Test Filter 24 Jam:
     * Filter buka_24_jam memeriksa jam_buka 00:00:00 dan jam_tutup 23:59:00.
     */
    public function test_filter_buka_24_jam_berhasil(): void
    {
        $controller = app(ChatController::class);
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('queryWisata');
        $method->setAccessible(true);

        $intent = [
            'kategori' => 'Kuliner',
            'buka_24_jam' => true,
            'radius_km' => 20,
            'nama_wisata' => null,
            'kata_kunci' => null,
            'wilayah' => null,
            'gratis' => false,
            'max_harga' => null,
            'jam_sekarang' => false,
            'urutan' => null,
        ];

        $hasil = $method->invoke($controller, $intent, null, null, false);

        $this->assertNotEmpty($hasil);
        $this->assertSame('Rumah Makan Sederhana', $hasil[0]['nama']);
    }

    /**
     * Test Masalah 3: Endpoint POST /chat menyimpan pesan user dan jawaban
     * tanpa error dan mengembalikan payload JSON yang tepat.
     */
    public function test_endpoint_chat_berjalan_dan_menyimpan_pesan(): void
    {
        // Mock LlmService agar deterministik dan tidak bergantung pada kuota API eksternal
        $mockLlm = $this->createMock(LlmService::class);
        $mockLlm->method('ekstrakIntent')->willReturn([
            'kategori' => 'Pantai',
            'radius_km' => 20,
            'query_bebas' => 'pantai',
            'jam_sekarang' => false,
            'buka_24_jam' => false,
            'nama_wisata' => null,
            'wilayah' => null,
            'kata_kunci' => null,
            'gratis' => false,
            'max_harga' => null,
            'urutan' => null,
        ]);
        $mockLlm->method('rangkaiJawaban')->willReturn('Berikut rekomendasi pantai di Kota Padang: **Pantai Air Manis**');
        $this->app->instance(LlmService::class, $mockLlm);

        $sesi = ChatSession::create([
            'session_token' => 'test-token-'.uniqid(),
            'lat' => '-0.9471',
            'lng' => '100.4174',
        ]);

        $response = $this->postJson('/chat', [
            'session_token' => $sesi->session_token,
            'pesan' => 'mau cari pantai terdekat',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'jawaban',
                'wisata',
                'intent',
                'ada_lokasi',
                'di_luar_padang',
            ]);

        $this->assertDatabaseHas('chat_messages', [
            'session_id' => $sesi->id,
            'role' => 'user',
            'pesan' => 'mau cari pantai terdekat',
        ]);

        $this->assertDatabaseHas('chat_messages', [
            'session_id' => $sesi->id,
            'role' => 'assistant',
        ]);
    }
}

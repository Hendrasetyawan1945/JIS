<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LlmService
{
    private string $baseUrl;
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.llm.base_url'), '/');
        $this->apiKey  = config('services.llm.api_key');
        $this->model   = config('services.llm.model', 'deepseek-v4-flash');
    }

    /**
     * Ekstrak intent dari pesan user.
     * Kembalikan array: ['kategori' => string|null, 'radius_km' => int, 'query_bebas' => string, 'jam_sekarang' => bool]
     */
    public function ekstrakIntent(string $pesanUser): array
    {
        $systemPrompt = <<<PROMPT
Kamu adalah parser intent untuk sistem rekomendasi wisata Kota Padang.
Tugasmu: baca pesan user dan kembalikan JSON dengan field berikut:
- "kategori": salah satu dari ["Pantai","Pulau","Alam","Museum","Sejarah","Kuliner"] atau null jika tidak disebutkan
- "radius_km": angka integer radius pencarian dalam km (default 20)
- "query_bebas": ringkasan intent user dalam 1 kalimat
- "jam_sekarang": true jika user menanyakan tempat yang buka sekarang, false jika tidak
- "nama_wisata": nama spesifik wisata yang ditanyakan user atau null

Balas HANYA dengan JSON valid, tanpa penjelasan, tanpa markdown.
PROMPT;

        $response = $this->chat([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $pesanUser],
        ], 200);

        $content = $response['choices'][0]['message']['content'] ?? '{}';

        // Bersihkan jika ada markdown code block
        $content = preg_replace('/```json\s*|\s*```/', '', trim($content));

        $intent = json_decode($content, true);

        if (! is_array($intent)) {
            Log::warning('LlmService: gagal parse intent JSON', ['raw' => $content]);
            $intent = [];
        }

        return array_merge([
            'kategori'    => null,
            'radius_km'   => 20,
            'query_bebas' => $pesanUser,
            'jam_sekarang' => false,
            'nama_wisata' => null,
        ], $intent);
    }

    /**
     * Rangkai jawaban akhir dari data SQL + pesan user.
     * Prinsip grounding: LLM wajib hanya pakai data yang diberikan.
     */
    public function rangkaiJawaban(string $pesanUser, array $dataWisata, bool $adaLokasi = false): string
    {
        if (empty($dataWisata)) {
            return 'Maaf, belum ada data wisata yang sesuai dengan permintaanmu di Kota Padang saat ini.';
        }

        $dataJson = json_encode($dataWisata, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $lokasiInfo = $adaLokasi
            ? 'Data sudah diurutkan berdasarkan jarak dari lokasi user (terdekat dulu).'
            : 'Lokasi user tidak diketahui, data diurutkan berdasarkan rating.';

        $systemPrompt = <<<PROMPT
Kamu adalah asisten wisata Kota Padang yang membantu dan ramah.
ATURAN KETAT:
1. Semua fakta (nama, harga, jam, jarak, rating) WAJIB dari data JSON yang diberikan. DILARANG menambah informasi di luar data.
2. Jika data kosong, katakan jujur belum ada data.
3. Jawab dalam bahasa Indonesia yang natural, singkat dan informatif.
4. Sebutkan maksimal 5 rekomendasi.
5. Format: nama wisata dicetak tebal (**Nama**), sertakan harga tiket dan jam buka jika ada.
6. Jangan sebut bahwa kamu menggunakan data JSON atau database.
$lokasiInfo
PROMPT;

        $userPrompt = "Pertanyaan user: $pesanUser\n\nData wisata:\n$dataJson";

        $response = $this->chat([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ], 600);

        return $response['choices'][0]['message']['content']
            ?? 'Maaf, terjadi kesalahan saat memproses rekomendasi.';
    }

    /**
     * Kirim request chat ke LLM API.
     */
    private function chat(array $messages, int $maxTokens = 400): array
    {
        $response = Http::withToken($this->apiKey)
            ->timeout(30)
            ->post("{$this->baseUrl}/chat/completions", [
                'model'      => $this->model,
                'messages'   => $messages,
                'max_tokens' => $maxTokens,
            ]);

        if ($response->failed()) {
            Log::error('LlmService: request gagal', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \RuntimeException('Gagal menghubungi LLM API (HTTP ' . $response->status() . ')');
        }

        return $response->json();
    }
}

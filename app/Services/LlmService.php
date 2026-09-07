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
        $this->apiKey = config('services.llm.api_key');
        $this->model = config('services.llm.model', 'deepseek-v4-flash');
    }

    /**
     * Ekstrak intent dari pesan user.
     * Riwayat percakapan dikirim agar prompt singkat ("yang paling dekat dari situ") tetap terpahami.
     *
     * @param  array  $riwayat  list of ['role' => 'user'|'assistant', 'pesan' => string]
     */
    public function ekstrakIntent(string $pesanUser, array $riwayat = []): array
    {
        $systemPrompt = <<<'PROMPT'
Kamu adalah parser intent untuk sistem rekomendasi wisata Kota Padang.
Baca pesan user TERBARU (gunakan riwayat untuk rujukan seperti "dari situ", "yang tadi") dan kembalikan JSON:

- "kategori": WAJIB diisi bila user menyebut atau mengindikasikan jenis/aktivitas tempat: pantai/pesisir/laut/ombak/main pasir→"Pantai", pulau/snorkeling/diving/gugusan pulau→"Pulau", museum/koleksi artefak/budaya tempo dulu→"Museum", kuliner/makan/masakan/soto/rendang/restoran/kafe→"Kuliner", air terjun/bukit/hutan/taman alam/tracking→"Alam", situs sejarah/monumen/tugu/jembatan tua/masjid bersejarah→"Sejarah". null hanya bila topik wisata sama sekali umum atau tidak spesifik ke kategori manapun.
- "radius_km": integer km (default 20). "terdekat dari lokasi saya" → 5.
- "query_bebas": ringkasan intent 1 kalimat.
- "jam_sekarang": true hanya jika user menanyakan tempat yang buka saat ini.
- "buka_24_jam": true jika user mencari tempat buka 24 jam.
- "nama_wisata": nama spesifik yang ditanyakan atau null. "Bang Hatta"→"Bung Hatta", "siti nurbaya"→"Siti Nurbaya", "rumah gadang"→"Rumah Gadang". Permintaan umum ("pantai yang bagus") → null.
- "wilayah": wilayah/kecamatan yang disebut ("Bungus", "pusat kota", "Padang Barat") atau null.
- "kata_kunci": frasa yang harus ada di deskripsi ("air terjun", "gazebo", "diving", "pasir putih", "sunset") atau null.
- "gratis": true jika user cari tempat tanpa tiket.
- "max_harga": batas atas harga tiket rupiah (integer) atau null.
- "urutan": "termurah" | "termahal" | "terdekat" | "terbaik" | null.
  "murah/dompet tipis"→"termurah". "paling mahal"→"termahal". "paling dekat"→"terdekat". "rekomendasi/terbaik"→"terbaik".

Contoh:
"pantai yang pasirnya putih" → {"kategori":"Pantai","radius_km":20,"query_bebas":"cari pantai pasir putih","jam_sekarang":false,"buka_24_jam":false,"nama_wisata":null,"wilayah":null,"kata_kunci":"pasir putih","gratis":false,"max_harga":null,"urutan":null}
"Saya mau ke museum yang cocok untuk anak-anak" → {"kategori":"Museum","radius_km":20,"query_bebas":"museum ramah anak","jam_sekarang":false,"buka_24_jam":false,"nama_wisata":null,"wilayah":null,"kata_kunci":null,"gratis":false,"max_harga":null,"urutan":null}
"tempat kuliner khas Padang yang murah" → {"kategori":"Kuliner","radius_km":20,"query_bebas":"kuliner khas murah","jam_sekarang":false,"buka_24_jam":false,"nama_wisata":null,"wilayah":null,"kata_kunci":null,"gratis":false,"max_harga":null,"urutan":"termurah"}

Balas HANYA dengan JSON valid, tanpa penjelasan, tanpa markdown.
PROMPT;

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $this->bungkusPesan($pesanUser, $riwayat)],
        ];

        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'max_tokens' => 500,
            'temperature' => 0,
            'response_format' => ['type' => 'json_object'],
        ];
        $response = $this->postChat($payload);

        $content = $response['choices'][0]['message']['content'] ?? '{}';
        $content = preg_replace('/```json\s*|\s*```/', '', trim($content));
        $intent = json_decode($content, true);

        if (! is_array($intent)) {
            Log::warning('LlmService: gagal parse intent JSON', ['raw' => $content]);
            $intent = [];
        }

        // Normalisasi: kategori harus salah satu yang valid
        $kategoriValid = ['Pantai', 'Pulau', 'Alam', 'Museum', 'Sejarah', 'Kuliner'];
        $kategori = $intent['kategori'] ?? null;
        if ($kategori !== null) {
            $kategori = ucfirst(strtolower(trim((string) $kategori)));
            $intent['kategori'] = in_array($kategori, $kategoriValid) ? $kategori : null;
        }

        return array_merge([
            'kategori' => null,
            'radius_km' => 20,
            'query_bebas' => $pesanUser,
            'jam_sekarang' => false,
            'buka_24_jam' => false,
            'nama_wisata' => null,
            'wilayah' => null,
            'kata_kunci' => null,
            'gratis' => false,
            'max_harga' => null,
            'urutan' => null,
        ], $intent);
    }

    /**
     * Rangkai jawaban akhir dari data SQL + cuaca + status operasional.
     *
     * @param  array  $dataWisata  setiap item sudah berisi key 'cuaca' dan 'status_operasional'
     * @param  bool  $adaMasalah  true jika ada wisata dengan cuaca buruk atau status tidak normal
     * @param  list<string>  $atributTakTersedia  atribut yang diminta user tapi tidak ada di data (untuk disclaimer jujur)
     * @param  bool  $diLuarPadang  true jika posisi user berada di luar radius Kota Padang
     * @param  int|null  $jarakKePadangKm  estimasi jarak user ke pusat Kota Padang dalam km
     */
    public function rangkaiJawaban(
        string $pesanUser,
        array $dataWisata,
        bool $adaLokasi = false,
        bool $adaMasalah = false,
        array $atributTakTersedia = [],
        bool $diLuarPadang = false,
        ?int $jarakKePadangKm = null,
    ): string {
        if (empty($dataWisata)) {
            return 'Maaf, belum ada data wisata yang sesuai dengan permintaanmu di Kota Padang saat ini.';
        }

        $disclaimer = $atributTakTersedia !== []
            ? 'PERHATIAN: atribut berikut TIDAK tersedia di data ('.implode(', ', $atributTakTersedia).'). Sampaikan jujur bahwa informasi itu tidak tersedia, lalu tawarkan hasil terdekat yang ada.'
            : '';

        $dataJson = json_encode($dataWisata, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if ($diLuarPadang) {
            $lokasiInfo = "Pengguna terdeteksi berada di luar Kota Padang (sekitar {$jarakKePadangKm} km dari Padang). Sapa dengan ramah bahwa ia sedang di luar kota dan berikut rekomendasi wisata terbaik di Kota Padang untuk rencana perjalanannya. Jarak ke tiap wisata tetap sebutkan.";
        } elseif ($adaLokasi) {
            $lokasiInfo = 'Data diurutkan berdasarkan jarak dari lokasi user (terdekat dulu).';
        } else {
            $lokasiInfo = 'Lokasi user tidak diketahui, data diurutkan berdasarkan rating.';
        }

        $masalahInfo = $adaMasalah
            ? 'PERHATIAN: ada wisata dengan cuaca buruk atau status tidak normal (banjir/longsor/renovasi/tutup). Wajib beri peringatan dan sarankan alternatif yang aman.'
            : 'Semua wisata saat ini aman dan cuaca mendukung.';

        $statusLabels = implode(', ', [
            'normal = beroperasi normal',
            'tutup_sementara = tutup sementara',
            'renovasi = sedang direnovasi',
            'banjir = terdampak banjir',
            'longsor = terdampak longsor',
            'akses_terbatas = akses jalan terbatas',
        ]);

        $systemPrompt = <<<PROMPT
Kamu adalah asisten wisata Kota Padang yang membantu dan ramah.

ATURAN KETAT:
1. Semua fakta (nama, harga, jam, jarak, rating) WAJIB dari data JSON. DILARANG menambah info di luar data, termasuk legenda, sejarah, atau fakta umum yang tidak tertulis di data.
2. Jawab dalam bahasa Indonesia yang natural, singkat dan informatif. Nada ramah dan konsisten, tanpa guyonan berlebihan.
3. Nama wisata dicetak tebal (**Nama**). Sertakan harga tiket dan jam buka jika ada.
4. Sebutkan kondisi cuaca tiap wisata dari field "cuaca.ringkasan" jika tersedia.
5. Jika field "cuaca.buruk" = true: beri peringatan ⚠️ dan sarankan alternatif wisata indoor atau kategori lain.
6. Jika "status_operasional" BUKAN "normal": wajib sebut statusnya dengan label berikut — $statusLabels.
   Gunakan emoji: 🚧 renovasi, 🌊 banjir, ⛰️ longsor, 🔒 tutup_sementara, ⚠️ akses_terbatas.
   Jika ada "catatan_status", sertakan sebagai informasi tambahan.
7. Jika wisata bermasalah (cuaca buruk ATAU status tidak normal): sarankan minimal 1 wisata alternatif dari data yang tersedia yang kondisinya aman.
8. Maksimal 5 rekomendasi.
9. Jangan sebut bahwa kamu menggunakan data JSON atau database.
$lokasiInfo
$masalahInfo
$disclaimer
PROMPT;

        $userPrompt = "Pertanyaan user: $pesanUser\n\nData wisata (termasuk cuaca & status):\n$dataJson";

        $jawaban = $this->chatWithRetry([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user',   'content' => $userPrompt],
        ], 800);

        return $jawaban !== ''
            ? $jawaban
            : $this->jawabanTemplate($dataWisata);
    }

    /**
     * Kirim chat; jika content kosong (bug intermiten API), retry sekali.
     * Return string kosong jika tetap gagal (pemanggil wajib sediakan fallback).
     */
    private function chatWithRetry(array $messages, int $maxTokens): string
    {
        $content = '';
        for ($i = 0; $i < 2; $i++) {
            $response = $this->chat($messages, $maxTokens);
            $content = trim($response['choices'][0]['message']['content'] ?? '');
            if ($content !== '') {
                return $content;
            }
            Log::warning('LlmService: content kosong, retry', ['attempt' => $i + 1]);
        }

        return '';
    }

    /**
     * Fallback non-LLM: rakit jawaban langsung dari data (grounding penuh).
     * ponytail: format kaku — jika butuh variasi bahasa, tingkatkan di LLM, bukan di sini.
     */
    private function jawabanTemplate(array $dataWisata): string
    {
        $baris = [];
        foreach (array_slice($dataWisata, 0, 5) as $w) {
            $harga = ((int) $w['harga_tiket']) === 0 ? 'gratis' : 'Rp'.number_format($w['harga_tiket'], 0, ',', '.');
            $jarak = isset($w['jarak_km']) ? ", {$w['jarak_km']} km" : '';
            $baris[] = "- **{$w['nama']}** ({$w['kategori']}) — {$harga}, buka ".($w['jam_buka'] ?: '-').'–'.($w['jam_tutup'] ?: '-').", rating {$w['rating']}{$jarak}";
        }

        return "Berikut rekomendasi wisata Kota Padang:\n\n".implode("\n", $baris);
    }

    /**
     * Bungkus pesan user + riwayat ke SATU user message berlabel,
     * agar model tidak menganggapnya percakapan yang harus dijawab.
     */
    private function bungkusPesan(string $pesanUser, array $riwayat): string
    {
        $teks = '';
        if ($riwayat !== []) {
            $teks .= "RIWAYAT PERCAKAPAN (hanya konteks agar kata rujukan seperti \"dari situ\" jelas — JANGAN dijawab):\n";
            foreach ($riwayat as $m) {
                $teks .= ($m['role'] === 'assistant' ? 'asisten' : 'user').': '.$m['pesan']."\n";
            }
            $teks .= "\n";
        }

        return $teks."PESAN BARU YANG HARUS DIPARSE (JANGAN dijawab — keluarkan HANYA JSON intent):\n{$pesanUser}";
    }

    /**
     * Kirim request chat ke LLM API.
     */
    private function chat(array $messages, int $maxTokens = 400): array
    {
        return $this->postChat([
            'model' => $this->model,
            'messages' => $messages,
            'max_tokens' => $maxTokens,
        ]);
    }

    /**
     * POST /chat/completions dengan payload bebas (response_format, temperature, dll).
     */
    private function postChat(array $payload): array
    {
        $response = Http::withToken($this->apiKey)
            ->timeout(30)
            ->post("{$this->baseUrl}/chat/completions", $payload);

        if ($response->failed()) {
            Log::error('LlmService: request gagal', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Gagal menghubungi LLM API (HTTP '.$response->status().')');
        }

        return $response->json();
    }
}

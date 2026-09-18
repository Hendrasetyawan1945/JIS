<?php

namespace App\Services;

use App\Services\SpatialIntent\SirValidator;
use App\Services\SpatialIntent\SpatialIntent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LlmService
{
    protected string $baseUrl = '';

    protected string $apiKey = '';

    protected string $model = 'deepseek-v4-flash';

    protected SirValidator $sirValidator;

    public function __construct(?SirValidator $sirValidator = null)
    {
        $this->baseUrl = rtrim((string) config('services.llm.base_url'), '/');
        $this->apiKey = (string) config('services.llm.api_key');
        $this->model = (string) config('services.llm.model', 'deepseek-v4-flash');
        $this->sirValidator = $sirValidator ?? new SirValidator;
    }

    /**
     * Ekstrak Spatial Intent Representation (SIR) formal dari pesan user.
     * Menggunakan SIR Schema dan ontologi operator spasial.
     *
     * @param  array  $riwayat  list of ['role' => 'user'|'assistant', 'pesan' => string]
     */
    public function ekstrakSIR(string $pesanUser, array $riwayat = []): SpatialIntent
    {
        $systemPrompt = <<<'PROMPT'
Kamu adalah Semantic Parser untuk Sistem Rekomendasi Spasial Wisata Kota Padang.
Tugasmu adalah memetakan pertanyaan pengguna menjadi Spatial Intent Representation (SIR) formal dalam format JSON murni.

SKEMA ATRIBUT SIR:
- "intent": "spatial_recommendation" | "entity_lookup" | "general_inquiry"
- "entity": "tourism_object"
- "category": WAJIB salah satu: "Pantai" | "Pulau" | "Alam" | "Museum" | "Sejarah" | "Kuliner" | null.
  * pantai/pesisir/laut/ombak/main pasir -> "Pantai"
  * pulau/snorkeling/diving/gugusan pulau -> "Pulau"
  * museum/koleksi artefak/budaya tempo dulu -> "Museum"
  * kuliner/makan/masakan/soto/rendang/restoran/kafe -> "Kuliner"
  * air terjun/bukit/hutan/taman alam/tracking -> "Alam"
  * situs sejarah/monumen/tugu/jembatan tua/masjid bersejarah -> "Sejarah"
  * null hanya bila tidak mengarah ke kategori di atas.
- "spatial_operator": "nearest" | "within_radius" | "within_admin_area" | "none"
  * "paling dekat / terdekat" -> "nearest"
  * "dalam radius X km / sekitar X km" -> "within_radius"
  * "di kecamatan / daerah X" -> "within_admin_area"
  * lainnya -> "none"
- "reference_type": "gps" | "city_center" | "poi" | "unknown"
  * Jika menyebut "dari lokasi saya" -> "gps"
  * Jika menyebut "dari pusat kota" -> "city_center"
  * Lainnya -> "unknown"
- "distance": float (dalam km, default 20.0). Jika "terdekat dari lokasi saya" -> 5.0.
- "distance_unit": "km"
- "admin_area": string nama kecamatan/wilayah di Padang (misal "Bungus", "Padang Barat", "Padang Selatan") atau null.
- "target_name": string nama tempat spesifik yang ditanyakan (misal "Pantai Padang", "Bung Hatta", "Siti Nurbaya") atau null jika umum.
- "keyword": frasa atribut penting yang harus dicocokkan (misal "pasir putih", "mie kocok", "air terjun", "snorkeling") atau null.
- "is_free": boolean (true jika mencari wisata gratis/tanpa tiket).
- "max_price": integer rupiah atau null.
- "open_now": boolean (true jika menanyakan tempat yang sedang buka saat ini).
- "open_24h": boolean (true jika mencari tempat yang buka 24 jam).
- "sort": "termurah" | "termahal" | "terdekat" | "terbaik" | null.
- "is_out_of_scope": boolean (true jika user meminta hal yang mustahil ada di Padang seperti ski salju, gunung es, kasino).

Balas HANYA dengan JSON valid, tanpa penjelasan, tanpa format markdown.
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

        try {
            $response = $this->postChat($payload);
            $content = $response['choices'][0]['message']['content'] ?? '{}';
            $content = preg_replace('/```json\s*|\s*```/', '', trim($content));
            $data = json_decode($content, true);

            if (! is_array($data)) {
                Log::warning('LlmService: gagal parse intent JSON', ['raw' => $content]);
                $data = [];
            }
        } catch (\Throwable $e) {
            Log::error('LlmService: ekstrakSIR error', ['error' => $e->getMessage()]);
            $data = [];
        }

        $rawSir = SpatialIntent::fromArray($data, $pesanUser);
        $validationResult = $this->sirValidator->validate($rawSir);

        return $validationResult['sir'];
    }

    /**
     * Ekstrak intent dari pesan user (format array legacy untuk kompatibilitas).
     *
     * @param  array  $riwayat  list of ['role' => 'user'|'assistant', 'pesan' => string]
     */
    public function ekstrakIntent(string $pesanUser, array $riwayat = []): array
    {
        $sir = $this->ekstrakSIR($pesanUser, $riwayat);

        return $sir->toArray();
    }

    /**
     * Rangkai jawaban akhir dari data SQL + cuaca + status operasional dengan Grounding Contract.
     *
     * @param  array  $dataWisata  setiap item sudah berisi key 'cuaca' dan 'status_operasional'
     * @param  bool  $adaMasalah  true jika ada wisata dengan cuaca buruk atau status tidak normal
     * @param  list<string>  $atributTakTersedia  atribut yang diminta user tapi tidak ada di data (untuk preservasi maksud)
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
            return 'Maaf, saat ini belum ada data objek wisata yang sesuai dengan kriteria permintaan Anda di Kota Padang.';
        }

        // Aturan Intent Preservation (Kasus Mie Kocok dsb)
        $disclaimer = $atributTakTersedia !== []
            ? 'ATURAN PRESERVASI MAKSUD (INTENT PRESERVATION): Permintaan spesifik berikut TIDAK ditemukan dalam basis data wisata Padang: ('.implode(', ', $atributTakTersedia).'). Anda WAJIB menyatakan secara jujur dan transparan di kalimat awal: "Menu/permintaan \''.implode(', ', $atributTakTersedia).'\' belum tersedia dalam basis data objek wisata Kota Padang." DILARANG mengaku bahwa rekomendasi pengganti adalah apa yang diminta. Sajikan data berikut murni sebagai alternatif kuliner/wisata lokal terdekat.'
            : '';

        // Aturan Context-Aware Spatial Fallback (Skenario 35 - User di luar Padang)
        if ($diLuarPadang) {
            $lokasiInfo = "ATURAN FALLBACK SPASIAL EKSPLISIT: Pengguna terdeteksi berada di luar area Kota Padang (sekitar {$jarakKePadangKm} km). Anda WAJIB menyertakan notifikasi transparan di pembuka: 'Lokasi Anda terdeteksi berada di luar area Kota Padang (sekitar {$jarakKePadangKm} km). Rekomendasi berikut ditampilkan berdasarkan titik pusat Kota Padang untuk referensi rencana perjalanan Anda.'";
        } elseif ($adaLokasi) {
            $lokasiInfo = 'Data diurutkan secara deterministik berdasarkan jarak dari lokasi pengguna (terdekat dulu).';
        } else {
            $lokasiInfo = 'Lokasi pengguna tidak diketahui, data diurutkan berdasarkan rating terbaik.';
        }

        $masalahInfo = $adaMasalah
            ? 'PERINGATAN OPERASIONAL: Terdapat objek wisata dengan cuaca buruk atau status tidak normal (banjir/longsor/renovasi/tutup). Wajib sertakan peringatan ⚠️ dan sarankan alternatif yang aman.'
            : 'Semua objek wisata saat ini beroperasi normal dan cuaca mendukung.';

        $statusLabels = implode(', ', [
            'normal = beroperasi normal',
            'tutup_sementara = tutup sementara',
            'renovasi = sedang direnovasi',
            'banjir = terdampak banjir',
            'longsor = terdampak longsor',
            'akses_terbatas = akses jalan terbatas',
        ]);

        $dataJson = json_encode($dataWisata, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $systemPrompt = <<<PROMPT
Kamu adalah asisten cerdas sistem informasi pariwisata Kota Padang.

KONTRAK GROUNDING KETAT (GROUNDING CONTRACT):
1. SEMUA FAKTA (nama, harga tiket, jam buka, jarak, rating) WAJIB 100% berasal dari data JSON terlampir.
2. DILARANG KERAS MENGARANG (ZERO HALLUCINATION):
   - Jangan menambahkan tempat wisata yang tidak ada di data JSON.
   - Jangan mengarang harga tiket, jam buka, atau nomor telepon.
   - Jangan menambahkan klaim deskriptif tak terdokumentasi (misal: "banyak penjual jagung bakar di malam hari", "terkenal dengan ombak surfing kelas dunia") jika tidak tertulis di deskripsi data JSON.
3. Cetak tebal nama wisata (**Nama Objek Wisata**). Sertakan harga tiket dan jam buka jika ada.
4. Sebutkan kondisi cuaca tiap wisata dari field "cuaca.ringkasan" jika tersedia.
5. Jika field "cuaca.buruk" = true: beri peringatan ⚠️ dan sarankan alternatif indoor.
6. Jika "status_operasional" bukan "normal", wajib sebut statusnya dengan jelas ($statusLabels).
7. Maksimal tampilkan 5 rekomendasi.
8. Gunakan bahasa Indonesia yang santun, informatif, dan ringkas.
$lokasiInfo
$masalahInfo
$disclaimer
PROMPT;

        $userPrompt = "Pertanyaan user: {$pesanUser}\n\nFakta Terverifikasi dari Basis Data:\n{$dataJson}";

        $jawaban = $this->chatWithRetry([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user',   'content' => $userPrompt],
        ], 800);

        return $jawaban !== ''
            ? $jawaban
            : $this->jawabanTemplate($dataWisata, $atributTakTersedia, $diLuarPadang, $jarakKePadangKm);
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
     */
    private function jawabanTemplate(
        array $dataWisata,
        array $atributTakTersedia = [],
        bool $diLuarPadang = false,
        ?int $jarakKePadangKm = null,
    ): string {
        $header = '';
        if ($atributTakTersedia !== []) {
            $header .= "Menu/permintaan '".implode(', ', $atributTakTersedia)."' belum tersedia dalam basis data objek wisata Kota Padang. Berikut alternatif objek wisata lokal yang tersedia:\n\n";
        }
        if ($diLuarPadang && $jarakKePadangKm !== null) {
            $header .= "Lokasi Anda terdeteksi berada di luar area Kota Padang (sekitar {$jarakKePadangKm} km). Berikut rekomendasi wisata berdasarkan pusat Kota Padang:\n\n";
        }

        $baris = [];
        foreach (array_slice($dataWisata, 0, 5) as $w) {
            $harga = ((int) $w['harga_tiket']) === 0 ? 'gratis' : 'Rp'.number_format($w['harga_tiket'], 0, ',', '.');
            $jarak = isset($w['jarak_km']) ? ", {$w['jarak_km']} km" : '';
            $baris[] = "- **{$w['nama']}** ({$w['kategori']}) — {$harga}, buka ".($w['jam_buka'] ?: '-').'–'.($w['jam_tutup'] ?: '-').", rating {$w['rating']}{$jarak}";
        }

        return $header."Berikut rekomendasi objek wisata Kota Padang:\n\n".implode("\n", $baris);
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

        $raw = trim($response->body());
        $cleaned = preg_replace('/data:\s*\[DONE\].*$/s', '', $raw);
        $data = json_decode($cleaned, true);
        if (is_array($data)) {
            return $data;
        }

        return $response->json();
    }
}

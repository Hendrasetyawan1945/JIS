<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Services\LlmService;
use App\Services\SpatialIntent\SirValidator;
use App\Services\SpatialIntent\SpatialIntent;
use App\Services\SpatialIntent\SpatialQueryCompiler;
use App\Services\WeatherService;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public const PADANG_PUSAT_LAT = SpatialQueryCompiler::PADANG_PUSAT_LAT;

    public const PADANG_PUSAT_LNG = SpatialQueryCompiler::PADANG_PUSAT_LNG;

    public const BATAS_LUAR_PADANG_KM = SpatialQueryCompiler::BATAS_LUAR_PADANG_KM;

    private SpatialQueryCompiler $compiler;

    private SirValidator $validator;

    public function __construct(
        private LlmService $llm,
        private WeatherService $weather,
        ?SpatialQueryCompiler $compiler = null,
        ?SirValidator $validator = null,
    ) {
        $this->compiler = $compiler ?? new SpatialQueryCompiler;
        $this->validator = $validator ?? new SirValidator;
    }

    /**
     * POST /chat/session
     * Buat atau perbarui sesi chat. Simpan posisi GPS user.
     */
    public function session(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_token' => ['nullable', 'string', 'max:64'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $token = $validated['session_token'] ?? null;

        $sesi = $token
            ? ChatSession::firstOrNew(['session_token' => $token])
            : new ChatSession(['session_token' => Str::random(48)]);

        if (isset($validated['lat'], $validated['lng'])) {
            $sesi->lat = $validated['lat'];
            $sesi->lng = $validated['lng'];
        }

        $sesi->save();

        return response()->json([
            'session_token' => $sesi->session_token,
            'lat' => $sesi->lat,
            'lng' => $sesi->lng,
        ]);
    }

    /**
     * POST /chat
     * Alur: pesan user → ekstrak intent → query SQL → cuaca + status → grounding LLM → jawaban
     */
    public function kirim(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_token' => ['required', 'string', 'max:64'],
            'pesan' => ['required', 'string', 'max:1000',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (trim($value) === '') {
                        $fail('Pesan tidak boleh kosong.');
                    }
                },
            ],
        ]);

        $sesi = ChatSession::where('session_token', $validated['session_token'])->first();
        if (! $sesi) {
            return response()->json(['error' => 'Sesi tidak ditemukan. Muat ulang halaman.'], 404);
        }

        $pesanUser = trim($validated['pesan']);

        try {
            // Riwayat untuk konteks multi-turn ("yang paling dekat dari situ")
            $riwayat = ChatMessage::where('session_id', $sesi->id)
                ->orderByDesc('id')
                ->limit(6)
                ->get()
                ->reverse()
                ->map(fn ($m) => ['role' => $m->role, 'pesan' => $m->pesan])
                ->values()
                ->toArray();

            $hasil = $this->prosesPesan(
                pesanUser: $pesanUser,
                lat: $sesi->lat,
                lng: $sesi->lng,
                riwayat: $riwayat,
                sessionId: $sesi->id,
            );

            return response()->json([
                'jawaban' => $hasil['jawaban'],
                'wisata' => $hasil['wisata'],
                'intent' => $hasil['intent'],
                'ada_lokasi' => $hasil['ada_lokasi'],
                'di_luar_padang' => $hasil['di_luar_padang'],
            ]);
        } catch (\Throwable $e) {
            Log::error('ChatController: error', ['error' => $e->getMessage()]);

            return response()->json([
                'error' => 'Maaf, terjadi gangguan teknis. Coba lagi dalam beberapa saat.',
            ], 500);
        }
    }

    /**
     * Memproses pesan chat: intent -> query SQL -> cuaca -> grounding LLM.
     * Mengembalikan hasil lengkap beserta breakdown latensi per tahap dalam milidetik.
     *
     * @param  array<int, array<string, string>>  $riwayat
     * @return array{
     *     jawaban: string,
     *     wisata: array,
     *     intent: array|null,
     *     ada_lokasi: bool,
     *     di_luar_padang: bool,
     *     is_sapaan: bool,
     *     latensi_ms: array{intent: float, sql: float, weather: float, llm: float, total: float}
     * }
     */
    public function prosesPesan(
        string $pesanUser,
        ?string $lat = null,
        ?string $lng = null,
        array $riwayat = [],
        ?int $sessionId = null,
    ): array {
        $tStart = hrtime(true);
        $pesanUser = trim($pesanUser);

        // Sapaan singkat: jawab langsung tanpa query wisata (hemat call LLM + deterministik)
        if ($this->isSapaan($pesanUser)) {
            $jawaban = 'Halo! Saya bisa bantu cari wisata Kota Padang. Coba tanya misalnya: '
                .'"pantai terdekat dari lokasi saya", "tempat kuliner yang murah", atau "museum yang bagus".';

            if ($sessionId !== null) {
                ChatMessage::create(['session_id' => $sessionId, 'role' => 'user',      'pesan' => $pesanUser]);
                ChatMessage::create(['session_id' => $sessionId, 'role' => 'assistant', 'pesan' => $jawaban]);
            }

            $tTotal = (hrtime(true) - $tStart) / 1e6;

            return [
                'jawaban' => $jawaban,
                'wisata' => [],
                'intent' => null,
                'ada_lokasi' => $lat !== null && $lng !== null,
                'di_luar_padang' => false,
                'is_sapaan' => true,
                'latensi_ms' => [
                    'intent' => 0.0,
                    'sql' => 0.0,
                    'weather' => 0.0,
                    'llm' => 0.0,
                    'total' => round($tTotal, 2),
                ],
            ];
        }

        // 1. Simpan pesan user jika sesi tersedia
        if ($sessionId !== null) {
            ChatMessage::create([
                'session_id' => $sessionId,
                'role' => 'user',
                'pesan' => $pesanUser,
            ]);
        }

        // 2. Tahap Ekstrak SIR (Spatial Intent Representation)
        $t0 = hrtime(true);
        $rawIntent = $this->llm->ekstrakIntent($pesanUser, $riwayat);
        $sir = $rawIntent instanceof SpatialIntent
            ? $rawIntent
            : SpatialIntent::fromArray($rawIntent, $pesanUser);

        $validation = $this->validator->validate($sir);
        $validatedSir = $validation['sir'];
        $intent = $validatedSir->toArray();
        $tIntent = (hrtime(true) - $t0) / 1e6;
        Log::debug('ChatController: SIR validated', $intent);

        // 3. Deteksi apakah user memiliki lokasi dan apakah berada di luar Kota Padang
        $adaLokasi = $lat !== null && $lng !== null;
        $diLuarPadang = false;
        $jarakKePadang = null;
        if ($adaLokasi) {
            $jarakKePadang = $this->hitungJarakKm((float) $lat, (float) $lng, self::PADANG_PUSAT_LAT, self::PADANG_PUSAT_LNG);
            if ($jarakKePadang > self::BATAS_LUAR_PADANG_KM) {
                $diLuarPadang = true;
            }
        }

        // Penanganan Out-of-Scope eksplisit (Kejujuran domain & Zero Hallucination)
        if ($validatedSir->isOutOfScope) {
            $jawaban = $validatedSir->outOfScopeReason ?? 'Maaf, permintaan Anda berada di luar domain pariwisata Kota Padang.';
            if ($sessionId !== null) {
                ChatMessage::create(['session_id' => $sessionId, 'role' => 'assistant', 'pesan' => $jawaban, 'intent_json' => $intent]);
            }
            $tTotal = (hrtime(true) - $tStart) / 1e6;

            return [
                'jawaban' => $jawaban,
                'wisata' => [],
                'intent' => $intent,
                'ada_lokasi' => $adaLokasi,
                'di_luar_padang' => false,
                'is_sapaan' => false,
                'latensi_ms' => [
                    'intent' => round($tIntent, 2),
                    'sql' => 0.0,
                    'weather' => 0.0,
                    'llm' => 0.0,
                    'total' => round($tTotal, 2),
                ],
            ];
        }

        // Penanganan SIR Invalid (Prinsip: No Validated SIR -> No SQL Execution)
        if (! $validatedSir->isValid) {
            $pesanError = implode(' ', $validatedSir->validationErrors);
            $jawaban = "Maaf, permintaan Anda tidak dapat diproses karena batasan tidak valid: {$pesanError} Silakan ulangi dengan parameter yang sesuai.";
            if ($sessionId !== null) {
                ChatMessage::create(['session_id' => $sessionId, 'role' => 'assistant', 'pesan' => $jawaban, 'intent_json' => $intent]);
            }
            $tTotal = (hrtime(true) - $tStart) / 1e6;

            return [
                'jawaban' => $jawaban,
                'wisata' => [],
                'intent' => $intent,
                'ada_lokasi' => $adaLokasi,
                'di_luar_padang' => false,
                'is_sapaan' => false,
                'latensi_ms' => [
                    'intent' => round($tIntent, 2),
                    'sql' => 0.0,
                    'weather' => 0.0,
                    'llm' => 0.0,
                    'total' => round($tTotal, 2),
                ],
            ];
        }

        // 4. Tahap Kompilasi & Eksekusi Query Spasial Deterministik
        $t1 = hrtime(true);
        $latF = $lat !== null ? (float) $lat : null;
        $lngF = $lng !== null ? (float) $lng : null;

        $dataWisata = $this->compiler->compileAndExecute($validatedSir, $latF, $lngF, $diLuarPadang);

        // Relaksasi atribut terkontrol dengan preservasi maksud (intent preservation)
        $atributTakTersedia = [];
        if ($dataWisata === [] && $validatedSir->keyword !== null) {
            $atributTakTersedia[] = $validatedSir->keyword;
            $sirRelaksasi = clone $validatedSir;
            $sirRelaksasi->keyword = null;
            $dataWisata = $this->compiler->compileAndExecute($sirRelaksasi, $latF, $lngF, $diLuarPadang);
        }

        if ($dataWisata === [] && $validatedSir->adminArea !== null) {
            $atributTakTersedia[] = 'wilayah '.$validatedSir->adminArea;
            $sirRelaksasi = clone $validatedSir;
            $sirRelaksasi->adminArea = null;
            $sirRelaksasi->keyword = null;
            $dataWisata = $this->compiler->compileAndExecute($sirRelaksasi, $latF, $lngF, $diLuarPadang);
        }

        // Relaksasi radius jika user di dalam Padang namun radius awal terlalu sempit
        if ($dataWisata === [] && $adaLokasi && ! $diLuarPadang && ($validatedSir->distance ?? 20.0) < 35.0) {
            $sirRelaksasi = clone $validatedSir;
            $sirRelaksasi->distance = 35.0;
            $dataWisata = $this->compiler->compileAndExecute($sirRelaksasi, $latF, $lngF, false);
        }
        $tSql = (hrtime(true) - $t1) / 1e6;

        // 5. Tahap Cuaca & Status Operasional
        $t2 = hrtime(true);
        $dataCuaca = [];
        if (! empty($dataWisata)) {
            $dataCuaca = $this->weather->getBatch($dataWisata);
        }
        $dataWisata = $this->gabungkanCuacaStatus($dataWisata, $dataCuaca);
        $adaMasalah = collect($dataWisata)->contains(
            fn ($w) => ($w['cuaca']['buruk'] ?? false) || ($w['status_operasional'] !== 'normal')
        );
        $tWeather = (hrtime(true) - $t2) / 1e6;

        // 6. Tahap Perangkaian Jawaban (LLM Grounding)
        $t3 = hrtime(true);
        $jawaban = $this->llm->rangkaiJawaban(
            $pesanUser,
            $dataWisata,
            $adaLokasi,
            $adaMasalah,
            $atributTakTersedia,
            $diLuarPadang,
            $jarakKePadang !== null ? (int) round($jarakKePadang) : null,
        );
        $tLlm = (hrtime(true) - $t3) / 1e6;

        // 7. Simpan jawaban asisten jika sesi tersedia
        if ($sessionId !== null) {
            ChatMessage::create([
                'session_id' => $sessionId,
                'role' => 'assistant',
                'pesan' => $jawaban,
                'intent_json' => $intent,
            ]);
        }

        $tTotal = (hrtime(true) - $tStart) / 1e6;

        return [
            'jawaban' => $jawaban,
            'wisata' => $dataWisata,
            'intent' => $intent,
            'ada_lokasi' => $adaLokasi,
            'di_luar_padang' => $diLuarPadang,
            'is_sapaan' => false,
            'latensi_ms' => [
                'intent' => round($tIntent, 2),
                'sql' => round($tSql, 2),
                'weather' => round($tWeather, 2),
                'llm' => round($tLlm, 2),
                'total' => round($tTotal, 2),
            ],
        ];
    }

    /** Deteksi sapaan singkat tanpa permintaan wisata. */
    private function isSapaan(string $pesan): bool
    {
        $pesan = preg_replace('/[!.?]+$/u', '', trim($pesan));
        if ($pesan === '') {
            return false;
        }

        $pola = '/^(halo+|hai|hi|hei|hey|assalamu\W*alaikum|salam|oi|p)(\s+(selamat\s+(pagi|siang|sore|malam)|pagi|siang|sore|malam))?(\s+(min|kak|gan|admin|bang|bro))?$/iu';
        $polaWaktu = '/^(selamat\s+(pagi|siang|sore|malam)|pagi|siang|sore|malam)(\s+(min|kak|gan|admin|bang|bro))?$/iu';

        return (bool) (preg_match($pola, $pesan) || preg_match($polaWaktu, $pesan));
    }

    /**
     * Gabungkan data cuaca dan status_operasional ke setiap item wisata.
     */
    private function gabungkanCuacaStatus(array $dataWisata, array $dataCuaca): array
    {
        return array_map(function (array $w) use ($dataCuaca) {
            $w['cuaca'] = $dataCuaca[$w['id']] ?? null;
            $w['status_operasional'] = $w['status_operasional'] ?? 'normal';
            $w['catatan_status'] = $w['catatan_status'] ?? null;

            return $w;
        }, $dataWisata);
    }

    /**
     * Hitung jarak dua koordinat dalam kilometer (Haversine formula).
     */
    private function hitungJarakKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        return $this->compiler->calculateHaversineKm($lat1, $lng1, $lat2, $lng2);
    }

    /**
     * Query wisata dari database berdasarkan intent (mendelegasikan ke compiler).
     */
    private function queryWisata(array $intent, ?string $lat, ?string $lng, bool $diLuarPadang = false): array
    {
        $sir = SpatialIntent::fromArray($intent, $intent['query_bebas'] ?? '');
        $latF = $lat !== null ? (float) $lat : null;
        $lngF = $lng !== null ? (float) $lng : null;

        return $this->compiler->compileAndExecute($sir, $latF, $lngF, $diLuarPadang);
    }

    /** Builder dasar: join kategori, hanya wisata aktif. */
    private function queryBase(): Builder
    {
        return $this->compiler->queryBase();
    }

    private function haversine(float $latF, float $lngF): string
    {
        return $this->compiler->haversineSql($latF, $lngF);
    }

    /**
     * Fallback nama wisata dari teks user, tanpa LLM.
     *
     * @return list<string> nama wisata yang cocok (kosong bila tidak ada)
     */
    private function cariNama(string $teks): array
    {
        $reflection = new \ReflectionClass($this->compiler);
        $method = $reflection->getMethod('cariNamaFuzzy');
        $method->setAccessible(true);

        return $method->invoke($this->compiler, $teks);
    }

    /** Format baris query ke array untuk LLM & response. */
    private function formatRow(object $w): array
    {
        return $this->compiler->formatRow($w);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Services\LlmService;
use App\Services\WeatherService;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public const PADANG_PUSAT_LAT = -0.9471;

    public const PADANG_PUSAT_LNG = 100.4174;

    public const BATAS_LUAR_PADANG_KM = 35.0;

    public function __construct(
        private LlmService $llm,
        private WeatherService $weather,
    ) {}

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

        // 2. Tahap Ekstrak Intent
        $t0 = hrtime(true);
        $intent = $this->llm->ekstrakIntent($pesanUser, $riwayat);
        $tIntent = (hrtime(true) - $t0) / 1e6;
        Log::debug('ChatController: intent', $intent);

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

        // 4. Tahap Query SQL
        $t1 = hrtime(true);
        $dataWisata = $this->queryWisata($intent, $lat, $lng, $diLuarPadang);

        $atributTakTersedia = [];
        if ($dataWisata === [] && ($intent['kata_kunci'] ?? null)) {
            $atributTakTersedia[] = $intent['kata_kunci'];
            $intentRelaksasi = $intent;
            $intentRelaksasi['kata_kunci'] = null;
            $dataWisata = $this->queryWisata($intentRelaksasi, $lat, $lng, $diLuarPadang);
        }
        if ($dataWisata === [] && ($intent['wilayah'] ?? null)) {
            $atributTakTersedia[] = 'wilayah '.$intent['wilayah'];
            $intentRelaksasi = $intent;
            $intentRelaksasi['wilayah'] = null;
            $intentRelaksasi['kata_kunci'] = null;
            $dataWisata = $this->queryWisata($intentRelaksasi, $lat, $lng, $diLuarPadang);
        }

        // Relaksasi radius jika user di dalam Padang namun radius awal terlalu sempit
        if ($dataWisata === [] && $adaLokasi && ! $diLuarPadang && (int) ($intent['radius_km'] ?? 20) < 35) {
            $intentRelaksasi = $intent;
            $intentRelaksasi['radius_km'] = 35;
            $dataWisata = $this->queryWisata($intentRelaksasi, $lat, $lng, false);
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
            // status_operasional sudah ada dari formatWisata / query result
            // pastikan ada default jika kolom belum ada di row lama
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
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 6371 * 2 * atan2(sqrt($a), sqrt(max(0.0, 1 - $a)));
    }

    /**
     * Query wisata dari database berdasarkan intent.
     * Semua filter fakta (kategori, harga, jam, wilayah, kata kunci) dilakukan di SQL —
     * LLM hanya merangkai kalimat, bukan menyaring data (prinsip grounding).
     */
    private function queryWisata(array $intent, ?string $lat, ?string $lng, bool $diLuarPadang = false): array
    {
        $adaLokasi = $lat !== null && $lng !== null;
        $radiusKm = max(1, (int) ($intent['radius_km'] ?? 20));
        $urutan = $intent['urutan'] ?? null;

        // --- Pencarian nama spesifik (nama_wisata dari LLM, fallback SQL dari teks user) ---
        $nama = trim((string) ($intent['nama_wisata'] ?? ''));
        if ($nama !== '') {
            $hasilNama = $this->queryBase()
                ->where('wisata.nama', 'ilike', "%{$nama}%")
                ->orderByDesc('wisata.rating')
                ->limit(3)
                ->get();

            // Fallback 1: Cari di deskripsi jika nama persis tidak cocok (misal: "Malin Kundang" -> ada di deskripsi Pantai Air Manis)
            if ($hasilNama->isEmpty()) {
                $hasilNama = $this->queryBase()
                    ->where('wisata.deskripsi', 'ilike', "%{$nama}%")
                    ->orderByDesc('wisata.rating')
                    ->limit(3)
                    ->get();
            }

            // Fallback 2: Pecah kata kunci utama (> 3 karakter non-kata umum)
            if ($hasilNama->isEmpty()) {
                $kataPenting = array_filter(
                    preg_split('/\s+/u', mb_strtolower($nama)),
                    fn ($k) => mb_strlen($k) >= 4 && ! in_array($k, ['pantai', 'pulau', 'museum', 'taman', 'bukit', 'wisata', 'alam', 'tempat', 'jalan'])
                );
                if ($kataPenting !== []) {
                    $hasilNama = $this->queryBase()
                        ->where(function ($qq) use ($kataPenting) {
                            foreach ($kataPenting as $kp) {
                                $qq->orWhere('wisata.nama', 'ilike', "%{$kp}%")
                                    ->orWhere('wisata.deskripsi', 'ilike', "%{$kp}%");
                            }
                        })
                        ->orderByDesc('wisata.rating')
                        ->limit(3)
                        ->get();
                }
            }

            if ($hasilNama->isNotEmpty()) {
                return $hasilNama->map(function ($w) use ($lat, $lng) {
                    if ($lat !== null && $lng !== null) {
                        $w->jarak_km = round($this->hitungJarakKm((float) $lat, (float) $lng, (float) $w->lat, (float) $w->lng), 1);
                    }

                    return $this->formatRow($w);
                })->all();
            }
        }

        $namaCocok = $this->cariNama($intent['query_bebas'] ?? '');
        if ($namaCocok !== []) {
            return $this->queryBase()
                ->whereIn('wisata.nama', $namaCocok)
                ->orderByDesc('wisata.rating')
                ->limit(3)
                ->get()
                ->map(function ($w) use ($lat, $lng) {
                    if ($lat !== null && $lng !== null) {
                        $w->jarak_km = round($this->hitungJarakKm((float) $lat, (float) $lng, (float) $w->lat, (float) $w->lng), 1);
                    }

                    return $this->formatRow($w);
                })
                ->all();
        }

        // --- Filter intent di SQL ---
        $q = $this->queryBase();

        if ($intent['kategori'] ?? null) {
            $q->where('kategori.nama', $intent['kategori']);
        }
        if ($intent['gratis'] ?? false) {
            $q->where('wisata.harga_tiket', 0);
        }
        if (isset($intent['max_harga']) && $intent['max_harga'] !== null && $intent['max_harga'] !== false) {
            $q->where('wisata.harga_tiket', '<=', (int) $intent['max_harga']);
        }
        if ($intent['buka_24_jam'] ?? false) {
            $q->where('wisata.jam_buka', '00:00:00')
                ->where(function ($qq) {
                    $qq->where('wisata.jam_tutup', '>=', '23:59:00')
                        ->orWhere('wisata.jam_tutup', '00:00:00');
                });
        }
        if ($intent['jam_sekarang'] ?? false) {
            $jam = now()->format('H:i:s');
            $q->where('wisata.jam_buka', '<=', $jam)
                ->where('wisata.jam_tutup', '>=', $jam);
        }
        if ($intent['wilayah'] ?? null) {
            $q->where('wisata.alamat', 'ilike', '%'.$intent['wilayah'].'%');
        }
        if ($intent['kata_kunci'] ?? null) {
            $kw = $intent['kata_kunci'];
            $q->where(fn ($qq) => $qq
                ->where('wisata.deskripsi', 'ilike', "%{$kw}%")
                ->orWhere('wisata.nama', 'ilike', "%{$kw}%"));
        }

        // --- Urutan + jarak (Haversine bila lokasi user diketahui) ---
        if ($adaLokasi) {
            $latF = (float) $lat;
            $lngF = (float) $lng;
            $haversineExpr = $this->haversine($latF, $lngF);
            $q->addSelect(DB::raw("{$haversineExpr} AS jarak_km"));

            // Jangan membatasi dengan radius ketat jika pengguna di luar Padang (> 35 km)
            if (! $diLuarPadang) {
                $q->whereRaw("{$haversineExpr} <= ?", [$radiusKm]);
            }

            $q->orderBy(match ($urutan) {
                'termurah' => 'wisata.harga_tiket',
                'termahal' => 'wisata.harga_tiket',
                'terbaik' => 'wisata.rating',
                default => $diLuarPadang ? 'wisata.rating' : 'jarak_km',
            }, match ($urutan) {
                'termurah' => 'asc',
                'termahal' => 'desc',
                'terbaik' => 'desc',
                default => $diLuarPadang ? 'desc' : 'asc',
            });
        } else {
            $q->orderBy(match ($urutan) {
                'termurah' => 'wisata.harga_tiket',
                'termahal' => 'wisata.harga_tiket',
                default => 'wisata.rating',
            }, match ($urutan) {
                'termurah' => 'asc',
                'termahal' => 'desc',
                default => 'desc',
            });
        }

        return $q->limit(5)->get()
            ->map(fn ($w) => $this->formatRow($w))
            ->all();
    }

    /** Builder dasar: join kategori, hanya wisata aktif. */
    private function queryBase(): Builder
    {
        return DB::table('wisata')
            ->join('kategori', 'wisata.kategori_id', '=', 'kategori.id')
            ->where('wisata.status_aktif', true)
            ->select(
                'wisata.id',
                'wisata.nama',
                'wisata.deskripsi',
                'wisata.alamat',
                'wisata.telepon',
                'wisata.lat',
                'wisata.lng',
                'wisata.harga_tiket',
                'wisata.jam_buka',
                'wisata.jam_tutup',
                'wisata.rating',
                'wisata.foto',
                'wisata.status_operasional',
                'wisata.catatan_status',
                DB::raw('kategori.nama as kategori'),
            );
    }

    private function haversine(float $latF, float $lngF): string
    {
        return "(6371 * ACOS(LEAST(1.0,
            COS(RADIANS({$latF})) * COS(RADIANS(wisata.lat)) *
            COS(RADIANS(wisata.lng) - RADIANS({$lngF})) +
            SIN(RADIANS({$latF})) * SIN(RADIANS(wisata.lat))
        )))";
    }

    /**
     * Fallback nama wisata dari teks user, tanpa LLM.
     * Hanya dicoba bila teks (tanpa kata umum) pendek — hindari false positive.
     * Tahap: frasa persis nama → frasa di deskripsi → rangka konsonan (tahan typo kecil).
     *
     * @return list<string> nama wisata yang cocok (kosong bila tidak ada)
     */
    private function cariNama(string $teks): array
    {
        $stopwords = ['yang', 'di', 'ke', 'ada', 'tempat', 'wisata', 'saya', 'mau', 'untuk', 'dan', 'paling', 'gak', 'ga', 'yg', 'dong', 'wajib', 'khas', 'coba', 'tanya', 'carikan', 'info', 'nama'];
        $kata = array_values(array_filter(
            preg_split('/\s+/u', mb_strtolower(trim($teks))),
            fn ($k) => $k !== '' && ! in_array($k, $stopwords)
        ));
        if ($kata === [] || count($kata) > 3) {
            return [];
        }
        $frasa = implode(' ', $kata);

        $cocok = $this->queryBase()
            ->where('wisata.nama', 'ilike', "%{$frasa}%")
            ->limit(3)
            ->get(['wisata.nama']);
        if ($cocok->isNotEmpty()) {
            return $cocok->pluck('nama')->all();
        }

        // Cek juga di deskripsi jika frasa ada di deskripsi (misal: "batu malin kundang")
        $cocokDesc = $this->queryBase()
            ->where('wisata.deskripsi', 'ilike', "%{$frasa}%")
            ->limit(3)
            ->get(['wisata.nama']);
        if ($cocokDesc->isNotEmpty()) {
            return $cocokDesc->pluck('nama')->all();
        }

        // ponytail: fuzzy konsonan sederhana — kalau butuh edit-distance penuh, pakai pg_trgm similarity()
        $skeleton = preg_replace('/[aiueo\s]/', '', $frasa);
        if (mb_strlen($skeleton) < 4) {
            return [];
        }

        return $this->queryBase()
            ->whereRaw("regexp_replace(lower(wisata.nama), '[aiueo ]', '', 'g') LIKE ?", ["%{$skeleton}%"])
            ->limit(3)
            ->get(['wisata.nama'])
            ->pluck('nama')
            ->all();
    }

    /** Format baris query ke array untuk LLM & response. */
    private function formatRow(object $w): array
    {
        return [
            'id' => $w->id,
            'nama' => $w->nama,
            'kategori' => $w->kategori,
            'alamat' => $w->alamat,
            'telepon' => $w->telepon ?? null,
            'deskripsi' => $w->deskripsi,
            'lat' => (float) $w->lat,
            'lng' => (float) $w->lng,
            'harga_tiket' => (int) $w->harga_tiket,
            'jam_buka' => substr($w->jam_buka ?? '', 0, 5),
            'jam_tutup' => substr($w->jam_tutup ?? '', 0, 5),
            'rating' => (float) $w->rating,
            'foto' => $w->foto,
            'jarak_km' => isset($w->jarak_km) ? round((float) $w->jarak_km, 1) : null,
            'status_operasional' => $w->status_operasional ?? 'normal',
            'catatan_status' => $w->catatan_status ?? null,
        ];
    }
}

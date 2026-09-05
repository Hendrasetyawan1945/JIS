<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Wisata;
use App\Services\LlmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function __construct(private LlmService $llm) {}

    /**
     * POST /chat/session
     * Buat atau perbarui sesi chat. Simpan posisi GPS user.
     */
    public function session(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_token' => ['nullable', 'string', 'max:64'],
            'lat'           => ['nullable', 'numeric', 'between:-90,90'],
            'lng'           => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $token = $validated['session_token'] ?? null;

        // Ambil sesi lama atau buat baru
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
            'lat'           => $sesi->lat,
            'lng'           => $sesi->lng,
        ]);
    }

    /**
     * POST /chat
     * Alur utama: pesan user → ekstrak intent → query SQL → grounding LLM → jawaban
     */
    public function kirim(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_token' => ['required', 'string', 'max:64'],
            'pesan'         => ['required', 'string', 'max:1000'],
        ]);

        // 1. Ambil sesi
        $sesi = ChatSession::where('session_token', $validated['session_token'])->first();
        if (! $sesi) {
            return response()->json(['error' => 'Sesi tidak ditemukan. Muat ulang halaman.'], 404);
        }

        $pesanUser = trim($validated['pesan']);

        // 2. Simpan pesan user
        ChatMessage::create([
            'session_id' => $sesi->id,
            'role'       => 'user',
            'pesan'      => $pesanUser,
        ]);

        try {
            // 3. Ekstrak intent via LLM
            $intent = $this->llm->ekstrakIntent($pesanUser);

            Log::info('ChatController: intent diekstrak', $intent);

            // 4. Query SQL sesuai intent
            $adaLokasi = $sesi->lat !== null && $sesi->lng !== null;
            $dataWisata = $this->queryWisata($intent, $sesi->lat, $sesi->lng);

            // 5. Rangkai jawaban via LLM (grounding)
            $jawaban = $this->llm->rangkaiJawaban($pesanUser, $dataWisata, $adaLokasi);

            // 6. Simpan jawaban asisten + intent_json
            ChatMessage::create([
                'session_id'  => $sesi->id,
                'role'        => 'assistant',
                'pesan'       => $jawaban,
                'intent_json' => $intent,
            ]);

            return response()->json([
                'jawaban'    => $jawaban,
                'wisata'     => $dataWisata,
                'intent'     => $intent,
                'ada_lokasi' => $adaLokasi,
            ]);

        } catch (\Throwable $e) {
            Log::error('ChatController: error saat proses chat', ['error' => $e->getMessage()]);

            // Simpan pesan error sebagai balasan asisten
            $pesanError = 'Maaf, terjadi gangguan teknis. Coba lagi dalam beberapa saat.';
            ChatMessage::create([
                'session_id' => $sesi->id,
                'role'       => 'assistant',
                'pesan'      => $pesanError,
            ]);

            return response()->json(['error' => $pesanError], 500);
        }
    }

    /**
     * Query wisata dari database berdasarkan intent.
     * Pakai Haversine jika ada lokasi user, fallback ke rating.
     */
    private function queryWisata(array $intent, ?string $lat, ?string $lng): array
    {
        $adaLokasi  = $lat !== null && $lng !== null;
        $radiusKm   = (int) ($intent['radius_km'] ?? 20);
        $namaWisata = $intent['nama_wisata'] ?? null;

        // --- Pencarian nama spesifik ---
        if ($namaWisata) {
            return Wisata::with('kategori')
                ->where('status_aktif', true)
                ->where('nama', 'like', '%' . $namaWisata . '%')
                ->limit(5)
                ->get()
                ->map(fn ($w) => $this->formatWisata($w))
                ->toArray();
        }

        // --- Query berbasis lokasi (Haversine) ---
        if ($adaLokasi) {
            $query = DB::table('wisata')
                ->join('kategori', 'wisata.kategori_id', '=', 'kategori.id')
                ->where('wisata.status_aktif', true)
                ->select(
                    'wisata.id',
                    'wisata.nama',
                    'wisata.deskripsi',
                    'wisata.alamat',
                    'wisata.lat',
                    'wisata.lng',
                    'wisata.harga_tiket',
                    'wisata.jam_buka',
                    'wisata.jam_tutup',
                    'wisata.rating',
                    'kategori.nama as kategori',
                    DB::raw("(6371 * ACOS(
                        COS(RADIANS(?)) * COS(RADIANS(wisata.lat)) *
                        COS(RADIANS(wisata.lng) - RADIANS(?)) +
                        SIN(RADIANS(?)) * SIN(RADIANS(wisata.lat))
                    )) AS jarak_km")
                )
                ->addBinding([(float) $lat, (float) $lng, (float) $lat], 'select');

            if ($intent['kategori']) {
                $query->where('kategori.nama', $intent['kategori']);
            }

            if ($intent['jam_sekarang'] ?? false) {
                $jamSekarang = now()->format('H:i:s');
                $query->where('wisata.jam_buka', '<=', $jamSekarang)
                      ->where('wisata.jam_tutup', '>=', $jamSekarang);
            }

            $results = $query
                ->having('jarak_km', '<=', $radiusKm)
                ->orderBy('jarak_km')
                ->limit(5)
                ->get();

            return $results->map(function ($w) {
                return [
                    'id'          => $w->id,
                    'nama'        => $w->nama,
                    'kategori'    => $w->kategori,
                    'alamat'      => $w->alamat,
                    'deskripsi'   => $w->deskripsi,
                    'lat'         => (float) $w->lat,
                    'lng'         => (float) $w->lng,
                    'harga_tiket' => (int) $w->harga_tiket,
                    'jam_buka'    => substr($w->jam_buka ?? '', 0, 5),
                    'jam_tutup'   => substr($w->jam_tutup ?? '', 0, 5),
                    'rating'      => (float) $w->rating,
                    'jarak_km'    => round((float) $w->jarak_km, 1),
                ];
            })->toArray();
        }

        // --- Fallback: tanpa lokasi, urutkan rating ---
        $query = Wisata::with('kategori')
            ->where('status_aktif', true)
            ->orderByDesc('rating');

        if ($intent['kategori']) {
            $query->whereHas('kategori', fn ($q) => $q->where('nama', $intent['kategori']));
        }

        if ($intent['jam_sekarang'] ?? false) {
            $jamSekarang = now()->format('H:i:s');
            $query->where('jam_buka', '<=', $jamSekarang)
                  ->where('jam_tutup', '>=', $jamSekarang);
        }

        return $query->limit(5)->get()
            ->map(fn ($w) => $this->formatWisata($w))
            ->toArray();
    }

    /** Format model Wisata ke array ringkas untuk LLM & response. */
    private function formatWisata(Wisata $w): array
    {
        return [
            'id'          => $w->id,
            'nama'        => $w->nama,
            'kategori'    => $w->kategori->nama ?? '-',
            'alamat'      => $w->alamat,
            'deskripsi'   => $w->deskripsi,
            'lat'         => (float) $w->lat,
            'lng'         => (float) $w->lng,
            'harga_tiket' => (int) $w->harga_tiket,
            'jam_buka'    => substr($w->jam_buka ?? '', 0, 5),
            'jam_tutup'   => substr($w->jam_tutup ?? '', 0, 5),
            'rating'      => (float) $w->rating,
        ];
    }
}

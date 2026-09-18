<?php

namespace App\Services\SpatialIntent;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Deterministic Spatial Query Compiler
 *
 * Mengompilasi Validated SpatialIntent (SIR) menjadi Parameterized SQL Query
 * secara deterministik pada basis data PostgreSQL.
 *
 * Prinsip Keamanan (Safety Invariant):
 * LLM sama sekali tidak memegang kendali atas struktur atau teks kueri SQL.
 * Query disusun secara deterministik menggunakan query builder dengan parameter binding.
 */
class SpatialQueryCompiler
{
    /** Titik Pusat Kota Padang */
    public const PADANG_PUSAT_LAT = -0.9471;

    public const PADANG_PUSAT_LNG = 100.4174;

    public const BATAS_LUAR_PADANG_KM = 35.0;

    /**
     * Kompilasi SIR dan eksekusi query ke database.
     *
     * @return array<int, array<string, mixed>>
     */
    public function compileAndExecute(
        SpatialIntent $sir,
        ?float $lat = null,
        ?float $lng = null,
        bool $diLuarPadang = false,
        int $limit = 5,
    ): array {
        // Jika kueri sudah ditandai out-of-scope oleh validator, tolak secara deterministik (0 baris)
        if ($sir->isOutOfScope) {
            return [];
        }

        $adaLokasi = $lat !== null && $lng !== null;

        // 1. Jalur Target Name (Entity Lookup / Nama Spesifik)
        if ($sir->targetName !== null) {
            $hasilNama = $this->lookupByName($sir->targetName, $lat, $lng);
            if (! empty($hasilNama)) {
                return $hasilNama;
            }
        }

        // 2. Jalur Fuzzy Fallback dari query bebas
        $namaCocok = $this->cariNamaFuzzy($sir->rawQuery);
        if ($namaCocok !== []) {
            $hasilFuzzy = $this->queryBase()
                ->whereIn('wisata.nama', $namaCocok)
                ->orderByDesc('wisata.rating')
                ->limit(3)
                ->get()
                ->map(fn ($w) => $this->formatRow($w, $lat, $lng))
                ->all();

            if (! empty($hasilFuzzy)) {
                return $hasilFuzzy;
            }
        }

        // 3. Jalur Spatial & Attribute Filtering (Standard SIR Compilation)
        $q = $this->queryBase();

        // Predikat Kategori
        if ($sir->category !== null) {
            $q->where('kategori.nama', $sir->category);
        }

        // Predikat Harga & Gratis
        if ($sir->isFree) {
            $q->where('wisata.harga_tiket', 0);
        } elseif ($sir->maxPrice !== null) {
            $q->where('wisata.harga_tiket', '<=', $sir->maxPrice);
        }

        // Predikat Jam Operasional
        if ($sir->open24h) {
            $q->where('wisata.jam_buka', '00:00:00')
                ->where(function ($qq) {
                    $qq->where('wisata.jam_tutup', '>=', '23:59:00')
                        ->orWhere('wisata.jam_tutup', '00:00:00');
                });
        }

        if ($sir->openNow) {
            $jam = now()->format('H:i:s');
            $q->where('wisata.jam_buka', '<=', $jam)
                ->where('wisata.jam_tutup', '>=', $jam);
        }

        // Predikat Wilayah Administratif
        if ($sir->adminArea !== null) {
            $q->where('wisata.alamat', 'ilike', "%{$sir->adminArea}%");
        }

        // Predikat Kata Kunci
        if ($sir->keyword !== null) {
            $kw = $sir->keyword;
            $q->where(fn ($qq) => $qq
                ->where('wisata.deskripsi', 'ilike', "%{$kw}%")
                ->orWhere('wisata.nama', 'ilike', "%{$kw}%")
            );
        }

        // Komputasi Spasial Haversine & Batasan Radius
        if ($adaLokasi) {
            $haversineExpr = $this->haversineSql($lat, $lng);
            $q->addSelect(DB::raw("{$haversineExpr} AS jarak_km"));

            // Filter radius jika operator spasial meminta dan user berada di dalam Padang
            $effectiveRadius = $sir->distance ?? 20.0;
            if (! $diLuarPadang && ($sir->spatialOperator === 'within_radius' || $sir->distance !== null)) {
                $q->whereRaw("{$haversineExpr} <= ?", [$effectiveRadius]);
            }

            // Urutan (Sorting)
            $q->orderBy(match ($sir->sort) {
                'termurah' => 'wisata.harga_tiket',
                'termahal' => 'wisata.harga_tiket',
                'terbaik' => 'wisata.rating',
                default => $diLuarPadang ? 'wisata.rating' : 'jarak_km',
            }, match ($sir->sort) {
                'termurah' => 'asc',
                'termahal' => 'desc',
                'terbaik' => 'desc',
                default => $diLuarPadang ? 'desc' : 'asc',
            });
        } else {
            $q->orderBy(match ($sir->sort) {
                'termurah' => 'wisata.harga_tiket',
                'termahal' => 'wisata.harga_tiket',
                default => 'wisata.rating',
            }, match ($sir->sort) {
                'termurah' => 'asc',
                'termahal' => 'desc',
                default => 'desc',
            });
        }

        return $q->limit($limit)->get()
            ->map(fn ($w) => $this->formatRow($w, $lat, $lng))
            ->all();
    }

    /**
     * Lookup nama spesifik dengan hierarki pencarian (nama persis, nama di deskripsi, token nama).
     *
     * @return array<int, array<string, mixed>>
     */
    private function lookupByName(string $nama, ?float $lat, ?float $lng): array
    {
        $hasil = $this->queryBase()
            ->where('wisata.nama', 'ilike', "%{$nama}%")
            ->orderByDesc('wisata.rating')
            ->limit(3)
            ->get();

        if ($hasil->isEmpty()) {
            $hasil = $this->queryBase()
                ->where('wisata.deskripsi', 'ilike', "%{$nama}%")
                ->orderByDesc('wisata.rating')
                ->limit(3)
                ->get();
        }

        if ($hasil->isEmpty()) {
            $kataPenting = array_filter(
                preg_split('/\s+/u', mb_strtolower($nama)),
                fn ($k) => mb_strlen($k) >= 4 && ! in_array($k, ['pantai', 'pulau', 'museum', 'taman', 'bukit', 'wisata', 'alam', 'tempat', 'jalan'])
            );
            if ($kataPenting !== []) {
                $hasil = $this->queryBase()
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

        if ($hasil->isNotEmpty()) {
            return $hasil->map(fn ($w) => $this->formatRow($w, $lat, $lng))->all();
        }

        return [];
    }

    /**
     * Builder dasar: hanya wisata aktif dengan join kategori.
     */
    public function queryBase(): Builder
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

    /**
     * Komputasi SQL Haversine untuk menghitung jarak dalam kilometer.
     */
    public function haversineSql(float $lat, float $lng): string
    {
        return "(6371 * ACOS(LEAST(1.0,
            COS(RADIANS({$lat})) * COS(RADIANS(wisata.lat)) *
            COS(RADIANS(wisata.lng) - RADIANS({$lng})) +
            SIN(RADIANS({$lat})) * SIN(RADIANS(wisata.lat))
        )))";
    }

    /**
     * Hitung jarak dua titik koordinat via formula Haversine di PHP (satuan km).
     */
    public function calculateHaversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 6371 * 2 * atan2(sqrt($a), sqrt(max(0.0, 1 - $a)));
    }

    /**
     * Format baris query ke array terstruktur.
     */
    public function formatRow(object $w, ?float $lat = null, ?float $lng = null): array
    {
        $jarak = null;
        if (isset($w->jarak_km)) {
            $jarak = round((float) $w->jarak_km, 1);
        } elseif ($lat !== null && $lng !== null && isset($w->lat, $w->lng)) {
            $jarak = round($this->calculateHaversineKm($lat, $lng, (float) $w->lat, (float) $w->lng), 1);
        }

        return [
            'id' => (int) $w->id,
            'nama' => (string) $w->nama,
            'kategori' => (string) $w->kategori,
            'alamat' => (string) $w->alamat,
            'telepon' => $w->telepon ?? null,
            'deskripsi' => (string) $w->deskripsi,
            'lat' => (float) $w->lat,
            'lng' => (float) $w->lng,
            'harga_tiket' => (int) $w->harga_tiket,
            'jam_buka' => substr($w->jam_buka ?? '', 0, 5),
            'jam_tutup' => substr($w->jam_tutup ?? '', 0, 5),
            'rating' => (float) $w->rating,
            'foto' => $w->foto,
            'jarak_km' => $jarak,
            'status_operasional' => $w->status_operasional ?? 'normal',
            'catatan_status' => $w->catatan_status ?? null,
        ];
    }

    /**
     * Fallback fuzzy konsonan untuk pencarian nama tanpa LLM.
     *
     * @return list<string>
     */
    private function cariNamaFuzzy(string $teks): array
    {
        $stopwords = ['yang', 'di', 'ke', 'ada', 'tempat', 'wisata', 'saya', 'mau', 'untuk', 'dan', 'paling', 'gak', 'ga', 'yg', 'dong', 'wajib', 'khas', 'coba', 'tanya', 'carikan', 'info', 'nama'];
        $kata = array_values(array_filter(
            preg_split('/\s+/u', mb_strtolower(trim($teks))),
            fn ($k) => $k !== '' && ! in_array($k, $stopwords, true)
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

        $cocokDesc = $this->queryBase()
            ->where('wisata.deskripsi', 'ilike', "%{$frasa}%")
            ->limit(3)
            ->get(['wisata.nama']);
        if ($cocokDesc->isNotEmpty()) {
            return $cocokDesc->pluck('nama')->all();
        }

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
}

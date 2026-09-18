<?php

namespace App\Services\SpatialIntent;

/**
 * Spatial Intent Representation (SIR)
 *
 * DTO formal yang merepresentasikan maksud spasial pengguna hasil ekstraksi semantik LLM.
 * Berperan sebagai perantara independen (decoupling layer) antara bahasa alami
 * dan eksekusi komputasi spasial deterministik pada database.
 */
class SpatialIntent
{
    /** Kategori wisata Padang yang valid dalam domain sistem */
    public const VALID_CATEGORIES = [
        'Pantai',
        'Pulau',
        'Alam',
        'Museum',
        'Sejarah',
        'Kuliner',
    ];

    /** Operator spasial yang didukung dalam ontologi sistem */
    public const VALID_OPERATORS = [
        'nearest',           // Cari paling dekat dari titik acuan (ORDER BY distance ASC)
        'within_radius',    // Dalam radius tertentu (distance <= ?)
        'within_admin_area', // Di wilayah/kecamatan tertentu (alamat ILIKE %)
        'none',              // Tidak ada batasan spasial eksplisit
    ];

    /** Tipe titik acuan spasial */
    public const VALID_REFERENCES = [
        'gps',              // Koordinat langsung dari perangkat pengguna
        'city_center',      // Titik pusat Kota Padang (-0.9471, 100.4174)
        'poi',              // Mengacu ke objek wisata tertentu
        'unknown',
    ];

    public function __construct(
        public string $intent = 'spatial_recommendation', // spatial_recommendation, entity_lookup, general_inquiry
        public string $entity = 'tourism_object',
        public ?string $category = null,
        public string $spatialOperator = 'none',
        public string $referenceType = 'unknown',
        public ?float $distance = 20.0,
        public string $distanceUnit = 'km',
        public ?string $adminArea = null,
        public ?string $targetName = null,
        public ?string $keyword = null,
        public bool $isFree = false,
        public ?int $maxPrice = null,
        public bool $openNow = false,
        public bool $open24h = false,
        public ?string $sort = null, // termurah, termahal, terdekat, terbaik
        public string $rawQuery = '',
        public bool $isOutOfScope = false,
        public ?string $outOfScopeReason = null,
    ) {}

    /**
     * Instansiasi SIR dari array JSON ekstraksi LLM.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data, string $rawQuery = ''): self
    {
        return new self(
            intent: (string) ($data['intent'] ?? 'spatial_recommendation'),
            entity: (string) ($data['entity'] ?? 'tourism_object'),
            category: (isset($data['category']) && $data['category'] !== '')
                ? (string) $data['category']
                : ((isset($data['kategori']) && $data['kategori'] !== '') ? (string) $data['kategori'] : null),
            spatialOperator: (string) ($data['spatial_operator'] ?? (isset($data['urutan']) && $data['urutan'] === 'terdekat' ? 'nearest' : (isset($data['wilayah']) && $data['wilayah'] ? 'within_admin_area' : 'none'))),
            referenceType: (string) ($data['reference_type'] ?? 'unknown'),
            distance: isset($data['distance']) && is_numeric($data['distance']) ? (float) $data['distance'] : (isset($data['radius_km']) && is_numeric($data['radius_km']) ? (float) $data['radius_km'] : null),
            distanceUnit: (string) ($data['distance_unit'] ?? 'km'),
            adminArea: isset($data['admin_area']) && $data['admin_area'] !== '' ? (string) $data['admin_area'] : ($data['wilayah'] ?? null),
            targetName: isset($data['target_name']) && $data['target_name'] !== '' ? (string) $data['target_name'] : ($data['nama_wisata'] ?? null),
            keyword: isset($data['keyword']) && $data['keyword'] !== '' ? (string) $data['keyword'] : ($data['kata_kunci'] ?? null),
            isFree: (bool) ($data['is_free'] ?? $data['gratis'] ?? false),
            maxPrice: isset($data['max_price']) && is_numeric($data['max_price']) ? (int) $data['max_price'] : (isset($data['max_harga']) && is_numeric($data['max_harga']) ? (int) $data['max_harga'] : null),
            openNow: (bool) ($data['open_now'] ?? $data['jam_sekarang'] ?? false),
            open24h: (bool) ($data['open_24h'] ?? $data['buka_24_jam'] ?? false),
            sort: isset($data['sort']) && $data['sort'] !== '' ? (string) $data['sort'] : ($data['urutan'] ?? null),
            rawQuery: $rawQuery,
            isOutOfScope: (bool) ($data['is_out_of_scope'] ?? false),
            outOfScopeReason: $data['out_of_scope_reason'] ?? null,
        );
    }

    /**
     * Konversi kembali ke array terstruktur (kompatibel dengan format logging dan response).
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'intent' => $this->intent,
            'entity' => $this->entity,
            'category' => $this->category,
            'spatial_operator' => $this->spatialOperator,
            'reference_type' => $this->referenceType,
            'distance' => $this->distance,
            'distance_unit' => $this->distanceUnit,
            'admin_area' => $this->adminArea,
            'target_name' => $this->targetName,
            'keyword' => $this->keyword,
            'is_free' => $this->isFree,
            'max_price' => $this->maxPrice,
            'open_now' => $this->openNow,
            'open_24h' => $this->open24h,
            'sort' => $this->sort,
            'raw_query' => $this->rawQuery,
            'is_out_of_scope' => $this->isOutOfScope,
            'out_of_scope_reason' => $this->outOfScopeReason,

            // Legacy keys untuk kompatibilitas backward view/test
            'kategori' => $this->category,
            'radius_km' => (int) ($this->distance ?? 20),
            'query_bebas' => $this->rawQuery,
            'jam_sekarang' => $this->openNow,
            'buka_24_jam' => $this->open24h,
            'nama_wisata' => $this->targetName,
            'wilayah' => $this->adminArea,
            'kata_kunci' => $this->keyword,
            'gratis' => $this->isFree,
            'max_harga' => $this->maxPrice,
            'urutan' => $this->sort,
        ];
    }
}

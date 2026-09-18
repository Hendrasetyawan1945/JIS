<?php

namespace App\Services\SpatialIntent;

/**
 * SIR Validator
 *
 * Mengimplementasikan 6 lapisan validasi deterministik untuk mengontrol
 * kewenangan LLM sebelum SIR diizinkan masuk ke compiler query spasial:
 * 1. Schema Validation (integritas atribut)
 * 2. Type Validation (normalisasi & type casting)
 * 3. Domain Validation (pengecekan rentang nilai valid)
 * 4. Operator Validation (verifikasi operator ontologi spasial)
 * 5. Entity Validation (verifikasi domain entitas & deteksi out-of-scope)
 * 6. Constraint Consistency (konsistensi logika antar-batasan)
 */
class SirValidator
{
    /** Daftar entitas/kategori luar lingkup (out-of-scope) yang wajib ditolak secara jujur */
    public const OUT_OF_SCOPE_KEYWORDS = [
        'salju',
        'ski',
        'kasino',
        'candi hindu',
        'candi buddha',
        'gunung es',
        'kereta gantung',
        'monorail',
        'disneyland',
    ];

    /**
     * Validasi dan normalisasi SIR.
     * Mengembalikan objek SpatialIntent yang sudah bersih dan tervalidasi.
     *
     * @return array{sir: SpatialIntent, isValid: bool, errors: list<string>}
     */
    public function validate(SpatialIntent $sir): array
    {
        $errors = [];

        // 1. Entity Validation & Out-of-Scope Detection
        $rawLower = mb_strtolower($sir->rawQuery);
        foreach (self::OUT_OF_SCOPE_KEYWORDS as $oos) {
            if (str_contains($rawLower, $oos)) {
                $sir->isOutOfScope = true;
                $sir->outOfScopeReason = "Permintaan '{$oos}' berada di luar domain pariwisata Kota Padang.";
                $sir->category = null;
                $sir->targetName = null;
                break;
            }
        }

        // Kategori normalisasi & validasi
        if ($sir->category !== null) {
            $catNormalized = ucfirst(strtolower(trim($sir->category)));
            if (in_array($catNormalized, SpatialIntent::VALID_CATEGORIES, true)) {
                $sir->category = $catNormalized;
            } else {
                $errors[] = "Kategori '{$sir->category}' tidak dikenali dalam domain wisata Padang.";
                $sir->category = null;
            }
        }

        // 2. Operator Validation
        if (! in_array($sir->spatialOperator, SpatialIntent::VALID_OPERATORS, true)) {
            $errors[] = "Operator spasial '{$sir->spatialOperator}' tidak terdaftar dalam ontologi.";
            $sir->spatialOperator = 'none';
        }

        // 3. Domain Validation & Type Normalization
        if ($sir->distance !== null) {
            if ($sir->distance <= 0) {
                $errors[] = 'Jarak/radius harus bernilai positif (> 0).';
                $sir->distance = 20.0;
            } elseif ($sir->distance > 100) {
                // Di atas 100 km dinormalisasi ke batas maksimum operasional
                $sir->distance = 50.0;
            }
        }

        if ($sir->maxPrice !== null) {
            if ($sir->maxPrice < 0) {
                $errors[] = 'Batas harga tidak boleh bernilai negatif.';
                $sir->maxPrice = null;
            }
        }

        // 4. Constraint Consistency Checking
        // Jika diminta gratis, pastikan maxPrice tidak kontradiktif
        if ($sir->isFree && $sir->maxPrice !== null && $sir->maxPrice > 0) {
            $errors[] = 'Kontradiksi: permintaan wisata gratis namun max_price > 0. Ditetapkan max_price = 0.';
            $sir->maxPrice = 0;
        }

        // Normalisasi sorting
        $validSorts = ['termurah', 'termahal', 'terdekat', 'terbaik'];
        if ($sir->sort !== null && ! in_array(strtolower($sir->sort), $validSorts, true)) {
            $sir->sort = null;
        }

        // Sanitasi teks string
        if ($sir->targetName !== null) {
            $sir->targetName = trim(strip_tags($sir->targetName));
            if ($sir->targetName === '') {
                $sir->targetName = null;
            }
        }

        if ($sir->adminArea !== null) {
            $sir->adminArea = trim(strip_tags($sir->adminArea));
            if ($sir->adminArea === '') {
                $sir->adminArea = null;
            }
        }

        if ($sir->keyword !== null) {
            $sir->keyword = trim(strip_tags($sir->keyword));
            if ($sir->keyword === '') {
                $sir->keyword = null;
            }
        }

        return [
            'sir' => $sir,
            'isValid' => empty($errors),
            'errors' => $errors,
        ];
    }
}

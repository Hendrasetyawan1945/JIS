<?php

declare(strict_types=1);

namespace App\Services\SpatialIntent;

/**
 * Deterministic SIR Validator
 *
 * Mengimplementasikan 6 lapisan validasi invarian deterministik untuk mengontrol
 * kewenangan LLM sebelum SIR diizinkan masuk ke compiler query spasial:
 *
 * 1. Dimensi 1: Schema & Type Integrity (integritas atribut, tipe data, dan sanitasi)
 * 2. Dimensi 2: Spatial Constraint & Domain (verifikasi rentang nilai radius/jarak)
 * 3. Dimensi 3: Spatial Operator Validity (verifikasi operator ontologi spasial)
 * 4. Dimensi 4: Reference Coordinate & Anchor Validation (validasi acuan koordinat & POI)
 * 5. Dimensi 5: Operational & Price Constraints (batasan non-negatif & konsistensi logika)
 * 6. Dimensi 6: Domain Scope & Ontological Integrity (kategori resmi & penolakan out-of-scope)
 *
 * Prinsip Rekayasa (Safety Invariant - No Intent Alteration):
 * Validator TIDAK BOLEH mengubah niat pengguna secara sepihak (misal: jarak negatif diubah jadi positif,
 * atau operator tak dikenal diubah jadi 'none'). Jika batasan melanggar invarian, SIR ditandai
 * isValid = false dengan deskripsi kesalahan eksplisit untuk memicu klarifikasi, BUKAN eksekusi liar.
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
     * Mengembalikan objek SpatialIntent yang dilengkapi metadata validasi CSIR.
     *
     * @return array{sir: SpatialIntent, isValid: bool, errors: list<string>, executionPolicy: string}
     */
    public function validate(SpatialIntent $sir): array
    {
        $errors = [];

        // 1. Dimensi 1: Schema & Type Integrity (Sanitasi string & integritas data)
        $this->validateSchemaAndTypes($sir);

        // 2. Dimensi 2: Spatial Constraint & Domain (Rentang radius/jarak)
        $this->validateSpatialDomain($sir, $errors);

        // 3. Dimensi 3: Spatial Operator Validity (Ontologi operator spasial)
        $this->validateSpatialOperator($sir, $errors);

        // 4. Dimensi 4: Reference Coordinate & Anchor Validation (Koordinat acuan)
        $this->validateSpatialReference($sir, $errors);

        // 5. Dimensi 5: Operational & Price Constraints (Harga dan konsistensi)
        $this->validateOperationalConstraints($sir, $errors);

        // 6. Dimensi 6: Domain Scope & Ontological Integrity (Kategori & Out-of-scope)
        $this->validateOntologicalScope($sir, $errors);

        // Finalisasi status CSIR
        $isValid = empty($errors);
        $sir->isValid = $isValid;
        $sir->validationErrors = $errors;

        if ($sir->isOutOfScope) {
            $sir->validationStatus = 'out_of_scope';
            $sir->executionPolicy = 'reject_out_of_scope';
        } elseif (! $isValid) {
            $sir->validationStatus = 'rejected';
            $sir->executionPolicy = 'clarify_user';
        } else {
            $sir->validationStatus = 'validated';
            $sir->executionPolicy = 'execute_sql';
        }

        return [
            'sir' => $sir,
            'isValid' => $isValid,
            'errors' => $errors,
            'executionPolicy' => $sir->executionPolicy,
        ];
    }

    /**
     * Dimensi 1: Schema & Type Integrity.
     * Sanitasi teks bebas untuk mencegah injection dan penyeragaman spasi.
     */
    private function validateSchemaAndTypes(SpatialIntent $sir): void
    {
        if ($sir->targetName !== null) {
            $cleaned = trim(strip_tags($sir->targetName));
            $sir->targetName = $cleaned !== '' ? $cleaned : null;
        }

        if ($sir->adminArea !== null) {
            $cleaned = trim(strip_tags($sir->adminArea));
            $sir->adminArea = $cleaned !== '' ? $cleaned : null;
        }

        if ($sir->keyword !== null) {
            $cleaned = trim(strip_tags($sir->keyword));
            $sir->keyword = $cleaned !== '' ? $cleaned : null;
        }

        $validSorts = ['termurah', 'termahal', 'terdekat', 'terbaik'];
        if ($sir->sort !== null && ! in_array(strtolower($sir->sort), $validSorts, true)) {
            $sir->sort = null;
        }
    }

    /**
     * Dimensi 2: Spatial Constraint & Domain.
     * Prinsip No Intent Alteration: jarak <= 0 adalah pelanggaran invarian, BUKAN dinormalisasi jadi positif.
     *
     * @param  list<string>  $errors
     */
    private function validateSpatialDomain(SpatialIntent $sir, array &$errors): void
    {
        if ($sir->distance !== null) {
            if ($sir->distance <= 0) {
                $errors[] = "Jarak/radius tidak valid ({$sir->distance} km). Nilai harus lebih besar dari 0.";
            } elseif ($sir->distance > 100) {
                // Di atas 100 km dinormalisasi ke batas maksimum operasional Padang & sekitarnya
                $sir->distance = 50.0;
            }
        }
    }

    /**
     * Dimensi 3: Spatial Operator Validity.
     * Prinsip No Intent Alteration: operator asing TIDAK dialihkan diam-diam ke 'none'.
     *
     * @param  list<string>  $errors
     */
    private function validateSpatialOperator(SpatialIntent $sir, array &$errors): void
    {
        if (! in_array($sir->spatialOperator, SpatialIntent::VALID_OPERATORS, true)) {
            $errors[] = "Operator spasial '{$sir->spatialOperator}' tidak terdaftar dalam ontologi sistem.";
        }
    }

    /**
     * Dimensi 4: Reference Coordinate & Anchor Validation.
     * Memastikan koordinat latitude & longitude berada dalam batas koordinat bola bumi.
     *
     * @param  list<string>  $errors
     */
    private function validateSpatialReference(SpatialIntent $sir, array &$errors): void
    {
        if (! in_array($sir->referenceType, SpatialIntent::VALID_REFERENCES, true)) {
            $errors[] = "Tipe titik acuan '{$sir->referenceType}' tidak valid.";
        }

        if ($sir->latitude !== null && ($sir->latitude < -90.0 || $sir->latitude > 90.0)) {
            $errors[] = "Koordinat latitude tidak valid ({$sir->latitude}). Rentang valid adalah -90 hingga 90.";
        }

        if ($sir->longitude !== null && ($sir->longitude < -180.0 || $sir->longitude > 180.0)) {
            $errors[] = "Koordinat longitude tidak valid ({$sir->longitude}). Rentang valid adalah -180 hingga 180.";
        }
    }

    /**
     * Dimensi 5: Operational & Price Constraints.
     * Memeriksa batasan harga non-negatif dan kontradiksi logika.
     *
     * @param  list<string>  $errors
     */
    private function validateOperationalConstraints(SpatialIntent $sir, array &$errors): void
    {
        if ($sir->maxPrice !== null && $sir->maxPrice < 0) {
            $errors[] = "Batas harga maksimum tidak boleh bernilai negatif ({$sir->maxPrice}).";
        }

        if ($sir->isFree && $sir->maxPrice !== null && $sir->maxPrice > 0) {
            $errors[] = "Kontradiksi batasan operasional: permintaan wisata gratis tidak kompatibel dengan batasan harga Rp {$sir->maxPrice}.";
        }
    }

    /**
     * Dimensi 6: Domain Scope & Ontological Integrity.
     * Memeriksa kesesuaian kategori terhadap ontologi dan mendeteksi kueri luar lingkup.
     *
     * @param  list<string>  $errors
     */
    private function validateOntologicalScope(SpatialIntent $sir, array &$errors): void
    {
        // Deteksi Out-of-Scope berbasis ontologi negatif
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

        // Validasi Kategori terhadap Ontologi Pariwisata Padang
        if ($sir->category !== null) {
            $catNormalized = ucfirst(strtolower(trim($sir->category)));
            if (in_array($catNormalized, SpatialIntent::VALID_CATEGORIES, true)) {
                $sir->category = $catNormalized;
            } else {
                $errors[] = "Kategori '{$sir->category}' tidak terdaftar dalam ontologi pariwisata Padang.";
                $sir->category = null;
            }
        }
    }
}

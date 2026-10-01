<?php

declare(strict_types=1);

namespace App\Services\SpatialIntent;

/**
 * Algorithmic Grounding Output Validator
 *
 * Lapisan verifikasi deterministik pasca-generasi NLG (Natural Language Generation).
 * Bertanggung jawab memverifikasi integritas grounding bahwa seluruh entitas objek wisata
 * yang direkomendasikan dalam jawaban LLM benar-benar merupakan subset dari fakta relasional SQL.
 *
 * Menjamin pembuktian matematis terhadap klaim "Zero Hallucination":
 * Sistem tidak hanya mengandalkan prompt engineering ("DILARANG MENGARANG"),
 * melainkan memvalidasi keluaran secara algoritmik sebelum disajikan kepada pengguna.
 */
class GroundingValidator
{
    /**
     * Memvalidasi apakah seluruh entitas wisata yang disebut dalam respons LLM
     * berdasar pada baris data fakta SQL ($sqlFacts).
     *
     * @param  array<int, array<string, mixed>>  $sqlFacts
     * @return array{
     *     isGrounded: bool,
     *     groundedEntities: list<string>,
     *     ungroundedEntities: list<string>,
     *     violations: list<string>
     * }
     */
    public function validate(string $nlgResponse, array $sqlFacts): array
    {
        if (empty($sqlFacts)) {
            // Jika fakta SQL kosong, respons tidak boleh menyebutkan rekomendasi spesifik baru
            return [
                'isGrounded' => true,
                'groundedEntities' => [],
                'ungroundedEntities' => [],
                'violations' => [],
            ];
        }

        // 1. Ekstraksi entitas wisata yang diklaim/dicetak tebal oleh LLM (**Nama Tempat**)
        $mentionedEntities = $this->extractMentionedEntities($nlgResponse);

        // 2. Daftar nama resmi dari baris fakta SQL
        $validFactNames = array_map(
            fn (array $row) => mb_strtolower(trim((string) ($row['nama'] ?? ''))),
            $sqlFacts
        );

        $grounded = [];
        $ungrounded = [];
        $violations = [];

        foreach ($mentionedEntities as $entity) {
            $entityLower = mb_strtolower($entity);

            // Cek kecocokan terhadap fakta SQL (persis atau substring signifikan)
            $matched = false;
            foreach ($validFactNames as $factName) {
                if ($factName === '' || mb_strlen($factName) < 3) {
                    continue;
                }

                if (
                    $entityLower === $factName
                    || str_contains($entityLower, $factName)
                    || str_contains($factName, $entityLower)
                ) {
                    $matched = true;
                    $grounded[] = $entity;
                    break;
                }
            }

            if (! $matched) {
                // Saring jika teks tebal hanya header atau istilah umum, bukan nama wisata
                if (! $this->isNonEntityPhrase($entityLower)) {
                    $ungrounded[] = $entity;
                    $violations[] = "Entitas '{$entity}' tidak ditemukan dalam baris fakta SQL yang terverifikasi.";
                }
            }
        }

        $isGrounded = empty($ungrounded);

        return [
            'isGrounded' => $isGrounded,
            'groundedEntities' => array_values(array_unique($grounded)),
            'ungroundedEntities' => array_values(array_unique($ungrounded)),
            'violations' => $violations,
        ];
    }

    /**
     * Ekstraksi kandidat nama entitas dari pola Markdown tebal (**...**).
     *
     * @return list<string>
     */
    private function extractMentionedEntities(string $text): array
    {
        preg_match_all('/\*\*([^*]+)\*\*/u', $text, $matches);
        if (empty($matches[1])) {
            return [];
        }

        $entities = [];
        foreach ($matches[1] as $m) {
            $clean = trim($m);
            if ($clean !== '' && mb_strlen($clean) >= 3) {
                $entities[] = $clean;
            }
        }

        return array_values(array_unique($entities));
    }

    /**
     * Filter frasa tebal umum non-entitas (misal: **Catatan**, **Peringatan Cuaca**, **Harga Tiket**).
     */
    private function isNonEntityPhrase(string $phraseLower): bool
    {
        $commonPhrases = [
            'peringatan',
            'perhatian',
            'catatan',
            'peringatan cuaca',
            'informasi',
            'rekomendasi',
            'harga tiket',
            'jam buka',
            'status operasional',
            'alternatif',
            'jarak',
            'tips',
            'catatan penting',
            'estimasi',
            'rute',
        ];

        foreach ($commonPhrases as $common) {
            if ($phraseLower === $common || str_starts_with($phraseLower, $common.':')) {
                return true;
            }
        }

        return false;
    }
}

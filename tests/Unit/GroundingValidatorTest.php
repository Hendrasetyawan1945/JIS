<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\SpatialIntent\GroundingValidator;
use PHPUnit\Framework\TestCase;

class GroundingValidatorTest extends TestCase
{
    private GroundingValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new GroundingValidator;
    }

    public function test_grounded_response_passes_verification(): void
    {
        $sqlFacts = [
            [
                'id' => 1,
                'nama' => 'Pantai Air Manis',
                'harga_tiket' => 10000,
                'kategori' => 'Pantai',
            ],
            [
                'id' => 2,
                'nama' => 'Pantai Padang',
                'harga_tiket' => 0,
                'kategori' => 'Pantai',
            ],
        ];

        $llmResponse = "Berikut rekomendasi pantai untuk Anda:\n"
            ."- **Pantai Air Manis**: Tiket Rp 10.000, terkenal dengan legenda Batu Malin Kundang.\n"
            ."- **Pantai Padang**: Gratis (Rp 0), sangat dekat dengan pusat kota.\n"
            .'**Catatan**: Harap selalu menjaga kebersihan.';

        $result = $this->validator->validate($llmResponse, $sqlFacts);

        $this->assertTrue($result['isGrounded']);
        $this->assertEmpty($result['ungroundedEntities']);
        $this->assertContains('Pantai Air Manis', $result['groundedEntities']);
        $this->assertContains('Pantai Padang', $result['groundedEntities']);
    }

    public function test_hallucinated_unverified_entity_is_flagged(): void
    {
        $sqlFacts = [
            [
                'id' => 1,
                'nama' => 'Pantai Air Manis',
                'harga_tiket' => 10000,
                'kategori' => 'Pantai',
            ],
        ];

        $hallucinatedResponse = "Berikut pantai yang cocok:\n"
            ."- **Pantai Air Manis**: Tempat wisata bersejarah.\n"
            .'- **Disneyland Padang Waterpark**: Wahana seluncuran salju modern.'; // Entitas fiktif/halusinasi

        $result = $this->validator->validate($hallucinatedResponse, $sqlFacts);

        $this->assertFalse($result['isGrounded']);
        $this->assertContains('Disneyland Padang Waterpark', $result['ungroundedEntities']);
        $this->assertNotEmpty($result['violations']);
        $this->assertStringContainsString('Disneyland Padang Waterpark', $result['violations'][0]);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\SpatialIntent\SirValidator;
use App\Services\SpatialIntent\SpatialIntent;
use PHPUnit\Framework\TestCase;

class SirValidatorTest extends TestCase
{
    private SirValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new SirValidator;
    }

    public function test_valid_sir_passes_validation(): void
    {
        $sir = new SpatialIntent(
            intent: 'spatial_recommendation',
            category: 'Pantai',
            spatialOperator: 'within_radius',
            distance: 10.0,
            sort: 'terdekat',
            rawQuery: 'Cari pantai dalam 10 km',
        );

        $res = $this->validator->validate($sir);

        $this->assertTrue($res['isValid']);
        $this->assertTrue($res['sir']->isValid);
        $this->assertSame('validated', $res['sir']->validationStatus);
        $this->assertSame('execute_sql', $res['executionPolicy']);
        $this->assertEmpty($res['errors']);
        $this->assertSame('Pantai', $res['sir']->category);
        $this->assertSame(10.0, $res['sir']->distance);
    }

    public function test_out_of_scope_query_detected_and_marked(): void
    {
        $sir = new SpatialIntent(
            intent: 'spatial_recommendation',
            category: 'Alam',
            rawQuery: 'Tempat main ski salju dan gunung es di Padang',
        );

        $res = $this->validator->validate($sir);

        $this->assertTrue($res['sir']->isOutOfScope);
        $this->assertSame('out_of_scope', $res['sir']->validationStatus);
        $this->assertSame('reject_out_of_scope', $res['executionPolicy']);
        $this->assertNotNull($res['sir']->outOfScopeReason);
        $this->assertNull($res['sir']->category);
    }

    public function test_negative_distance_and_price_are_strictly_rejected(): void
    {
        $sir = new SpatialIntent(
            distance: -15.0,
            maxPrice: -50000,
            rawQuery: 'wisata murah',
        );

        $res = $this->validator->validate($sir);

        // Prinsip No Intent Alteration: tidak diubah diam-diam ke positif, melainkan ditolak
        $this->assertFalse($res['isValid']);
        $this->assertFalse($res['sir']->isValid);
        $this->assertSame('rejected', $res['sir']->validationStatus);
        $this->assertSame('clarify_user', $res['executionPolicy']);
        $this->assertSame(-15.0, $res['sir']->distance);
        $this->assertSame(-50000, $res['sir']->maxPrice);
        $this->assertCount(2, $res['errors']);
    }

    public function test_constraint_contradiction_is_detected_and_rejected(): void
    {
        $sir = new SpatialIntent(
            isFree: true,
            maxPrice: 25000,
            rawQuery: 'wisata gratis tapi budget 25rb',
        );

        $res = $this->validator->validate($sir);

        // Prinsip No Intent Alteration: kontradiksi memicu penolakan validasi untuk klarifikasi
        $this->assertFalse($res['isValid']);
        $this->assertFalse($res['sir']->isValid);
        $this->assertSame('rejected', $res['sir']->validationStatus);
        $this->assertSame('clarify_user', $res['executionPolicy']);
        $this->assertStringContainsString('Kontradiksi batasan operasional', $res['errors'][0]);
    }

    public function test_invalid_spatial_operator_is_strictly_rejected(): void
    {
        $sir = new SpatialIntent(
            spatialOperator: 'teleport_near',
            rawQuery: 'teleportasi dekat pantai',
        );

        $res = $this->validator->validate($sir);

        // Prinsip No Intent Alteration: operator tidak dikenal dilarang fallback diam-diam ke 'none'
        $this->assertFalse($res['isValid']);
        $this->assertFalse($res['sir']->isValid);
        $this->assertSame('rejected', $res['sir']->validationStatus);
        $this->assertSame('clarify_user', $res['executionPolicy']);
        $this->assertSame('teleport_near', $res['sir']->spatialOperator);
        $this->assertStringContainsString('tidak terdaftar dalam ontologi sistem', $res['errors'][0]);
    }

    public function test_invalid_geographic_coordinates_are_rejected(): void
    {
        $sir = new SpatialIntent(
            latitude: 195.5, // melebihi batas 90 derajat
            longitude: -200.0, // melebihi batas -180 derajat
            rawQuery: 'cari wisata di koordinat ini',
        );

        $res = $this->validator->validate($sir);

        $this->assertFalse($res['isValid']);
        $this->assertCount(2, $res['errors']);
        $this->assertStringContainsString('latitude tidak valid', strtolower($res['errors'][0]));
        $this->assertStringContainsString('longitude tidak valid', strtolower($res['errors'][1]));
    }
}

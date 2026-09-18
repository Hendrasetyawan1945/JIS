<?php

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
        $this->assertNotNull($res['sir']->outOfScopeReason);
        $this->assertNull($res['sir']->category);
    }

    public function test_negative_distance_and_price_normalized(): void
    {
        $sir = new SpatialIntent(
            distance: -15.0,
            maxPrice: -50000,
            rawQuery: 'wisata murah',
        );

        $res = $this->validator->validate($sir);

        $this->assertFalse($res['isValid']);
        $this->assertSame(20.0, $res['sir']->distance);
        $this->assertNull($res['sir']->maxPrice);
    }

    public function test_constraint_contradiction_is_corrected(): void
    {
        $sir = new SpatialIntent(
            isFree: true,
            maxPrice: 25000,
            rawQuery: 'wisata gratis tapi budget 25rb',
        );

        $res = $this->validator->validate($sir);

        $this->assertFalse($res['isValid']);
        $this->assertSame(0, $res['sir']->maxPrice);
    }

    public function test_invalid_spatial_operator_fallback_to_none(): void
    {
        $sir = new SpatialIntent(
            spatialOperator: 'teleport_near',
            rawQuery: 'teleportasi dekat pantai',
        );

        $res = $this->validator->validate($sir);

        $this->assertSame('none', $res['sir']->spatialOperator);
    }
}

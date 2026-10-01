<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\SpatialIntent\SpatialIntent;
use App\Services\SpatialIntent\SpatialQueryCompiler;
use Tests\TestCase;

class SpatialQueryCompilerTest extends TestCase
{
    private SpatialQueryCompiler $compiler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->compiler = new SpatialQueryCompiler;
    }

    public function test_haversine_formula_distance_calculation(): void
    {
        // Jarak Monas Jakarta (-6.1754, 106.8272) ke Pusat Padang (-0.9471, 100.4174) > 900 km
        $jarakHaversine = $this->compiler->calculateHaversineKm(-6.1754, 106.8272, -0.9471, 100.4174);
        $this->assertGreaterThan(900, $jarakHaversine);

        // Titik identik harus 0 km
        $jarakNol = $this->compiler->calculateHaversineKm(-0.9471, 100.4174, -0.9471, 100.4174);
        $this->assertEqualsWithDelta(0.0, $jarakNol, 0.001);
    }

    public function test_spherical_law_of_cosines_concordance_with_haversine(): void
    {
        // Uji kesesuaian numerik Spherical Law of Cosines vs Haversine
        $lat1 = -0.9471;
        $lng1 = 100.4174;
        $lat2 = -0.9250;
        $lng2 = 100.3600;

        $distHaversine = $this->compiler->calculateHaversineKm($lat1, $lng1, $lat2, $lng2);
        $distSpherical = $this->compiler->calculateSphericalCosinesKm($lat1, $lng1, $lat2, $lng2);

        // Selisih antara kedua metode pada skala perkotaan (< 100 km) harus berada di bawah deviasi 0.01 km (10 meter)
        $this->assertEqualsWithDelta($distHaversine, $distSpherical, 0.01);
    }

    public function test_out_of_scope_sir_returns_empty_array_immediately(): void
    {
        $sir = new SpatialIntent(
            intent: 'spatial_recommendation',
            rawQuery: 'tempat main ski es di Padang',
            isOutOfScope: true,
        );

        $hasil = $this->compiler->compileAndExecute($sir);
        $this->assertSame([], $hasil);
    }

    public function test_invalid_sir_fails_invariant_and_returns_empty_immediately(): void
    {
        $sir = new SpatialIntent(
            intent: 'spatial_recommendation',
            isValid: false,
            validationStatus: 'rejected',
            rawQuery: 'pantai jarak -50 km',
        );

        // Invarian: No Validated SIR -> No SQL Execution
        $hasil = $this->compiler->compileAndExecute($sir);
        $this->assertSame([], $hasil);
    }
}

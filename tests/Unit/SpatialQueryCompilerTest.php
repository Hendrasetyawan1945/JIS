<?php

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
        // Jarak Monas Jakarta ke Pusat Padang > 900 km
        $jarak = $this->compiler->calculateHaversineKm(-6.1754, 106.8272, -0.9471, 100.4174);
        $this->assertGreaterThan(900, $jarak);

        // Titik identik harus 0 km
        $jarakNol = $this->compiler->calculateHaversineKm(-0.9471, 100.4174, -0.9471, 100.4174);
        $this->assertEqualsWithDelta(0.0, $jarakNol, 0.001);
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
}

<?php

namespace Tests\Feature;

use App\Services\BenchmarkDataset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class JournalEvaluationTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    /**
     * Pastikan dataset benchmark berisi 40 kasus uji yang terdefinisi dengan baik.
     */
    public function test_dataset_benchmark_berisi_40_skenario_valid(): void
    {
        $dataset = BenchmarkDataset::getDataset();

        $this->assertCount(40, $dataset, 'Dataset harus berisi tepat 40 skenario pengujian');

        foreach ($dataset as $item) {
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('grup', $item);
            $this->assertArrayHasKey('prompt', $item);
            $this->assertNotEmpty($item['prompt']);
        }
    }

    /**
     * Pastikan command riset:evaluasi dapat dieksekusi dalam mode mock,
     * menghasilkan metrik evaluasi akurasi, dan mengekspor file laporan jurnal.
     */
    public function test_command_riset_evaluasi_menghasilkan_laporan(): void
    {
        $testOutputDir = storage_path('app/testing_riset');
        File::deleteDirectory($testOutputDir);

        $this->artisan('riset:evaluasi', [
            '--mock' => true,
            '--limit' => 10,
            '--output' => $testOutputDir,
        ])->assertSuccessful();

        // Pastikan berkas CSV & Markdown terbentuk
        $this->assertFileExists("{$testOutputDir}/evaluasi_intent.csv");
        $this->assertFileExists("{$testOutputDir}/evaluasi_latensi.csv");
        $this->assertFileExists("{$testOutputDir}/ringkasan_evaluasi.md");

        // Periksa isi Markdown
        $content = File::get("{$testOutputDir}/ringkasan_evaluasi.md");
        $this->assertStringContainsString('Hasil Evaluasi & Pengujian Empiris Sistem', $content);
        $this->assertStringContainsString('Grounding Fidelity (Anti-Halusinasi)', $content);
        $this->assertStringContainsString('Benchmark Waktu Respons', $content);

        // Bersihkan direktori testing
        File::deleteDirectory($testOutputDir);
    }
}

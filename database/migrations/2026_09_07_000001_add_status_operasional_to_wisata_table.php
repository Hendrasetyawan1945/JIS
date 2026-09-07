<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wisata', function (Blueprint $table) {
            // Status operasional: normal | tutup_sementara | renovasi | banjir | longsor | akses_terbatas
            $table->string('status_operasional', 30)->default('normal')->after('status_aktif');
            // Catatan opsional untuk admin (alasan tutup, perkiraan buka kembali, dll)
            $table->text('catatan_status')->nullable()->after('status_operasional');
        });
    }

    public function down(): void
    {
        Schema::table('wisata', function (Blueprint $table) {
            $table->dropColumn(['status_operasional', 'catatan_status']);
        });
    }
};

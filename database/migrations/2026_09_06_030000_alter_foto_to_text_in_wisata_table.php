<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ubah kolom foto dari VARCHAR(255) → TEXT agar muat inline SVG.
     * SQLite tidak mendukung ALTER COLUMN — sqlite sudah TEXT sejak awal, cukup skip.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }
        DB::statement('ALTER TABLE wisata ALTER COLUMN foto TYPE TEXT');
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }
        DB::statement('ALTER TABLE wisata ALTER COLUMN foto TYPE VARCHAR(255)');
    }
};

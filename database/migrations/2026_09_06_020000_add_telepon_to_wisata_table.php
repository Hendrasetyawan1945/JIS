<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom pelengkap: telepon untuk informasi kontak pengelola wisata.
     * Source: AGENTS.md tidak melarang, dalam scope Fase 1 (info wisata).
     */
    public function up(): void
    {
        Schema::table('wisata', function (Blueprint $table) {
            $table->string('telepon', 30)->nullable()->after('alamat');
        });
    }

    public function down(): void
    {
        Schema::table('wisata', function (Blueprint $table) {
            $table->dropColumn('telepon');
        });
    }
};

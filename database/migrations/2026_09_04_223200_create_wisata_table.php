<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wisata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori')->restrictOnDelete();
            $table->string('nama', 100);
            $table->text('deskripsi')->nullable();
            $table->string('alamat', 255)->nullable();
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->decimal('harga_tiket', 12, 0)->default(0);
            $table->time('jam_buka')->nullable();
            $table->time('jam_tutup')->nullable();
            $table->decimal('rating', 2, 1)->default(0);
            $table->string('foto', 255)->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();

            $table->index('kategori_id');
            $table->index('status_aktif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wisata');
    }
};

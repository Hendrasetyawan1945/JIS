<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Wisata;
use Illuminate\Database\Seeder;

class WisataSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = collect([
            'Pantai',
            'Pulau',
            'Alam',
            'Museum',
            'Sejarah',
            'Kuliner',
        ])->mapWithKeys(fn ($nama) => [$nama => Kategori::create(['nama' => $nama])]);

        $contoh = [
            ['kategori' => 'Pantai', 'nama' => 'Pantai Air Manis', 'deskripsi' => 'Pantai berpasir dengan legenda Batu Malin Kundang.', 'alamat' => 'Kec. Padang Selatan', 'lat' => -0.9746000, 'lng' => 100.3626000, 'harga_tiket' => 10000, 'jam_buka' => '07:00:00', 'jam_tutup' => '18:00:00', 'rating' => 4.5],
            ['kategori' => 'Pantai', 'nama' => 'Pantai Padang', 'deskripsi' => 'Pantai kota sepanjang Teluk Sumatera.', 'alamat' => 'Kec. Padang Barat', 'lat' => -0.9500000, 'lng' => 100.3750000, 'harga_tiket' => 0, 'jam_buka' => '06:00:00', 'jam_tutup' => '18:00:00', 'rating' => 4.2],
            ['kategori' => 'Museum', 'nama' => 'Museum Adityawarman', 'deskripsi' => 'Museum provinsi dengan koleksi budaya Minangkabau.', 'alamat' => 'Jl. Diponegoro, Padang', 'lat' => -0.9621000, 'lng' => 100.3617000, 'harga_tiket' => 5000, 'jam_buka' => '08:00:00', 'jam_tutup' => '16:00:00', 'rating' => 4.3],
            ['kategori' => 'Sejarah', 'nama' => 'Jembatan Siti Nurbaya', 'deskripsi' => 'Jembatan ikonik di muara Batang Arau.', 'alamat' => 'Kota Tua, Padang', 'lat' => -0.9589000, 'lng' => 100.3594000, 'harga_tiket' => 0, 'jam_buka' => '00:00:00', 'jam_tutup' => '23:59:00', 'rating' => 4.4],
            ['kategori' => 'Pulau', 'nama' => 'Pulau Sikuai', 'deskripsi' => 'Pulau dengan resort dan spot snorkeling.', 'alamat' => 'Kepulauan Bungus', 'lat' => -1.1650000, 'lng' => 100.3510000, 'harga_tiket' => 250000, 'jam_buka' => '07:00:00', 'jam_tutup' => '17:00:00', 'rating' => 4.6],
        ];

        foreach ($contoh as $w) {
            $kategoriId = $kategori[$w['kategori']]->id;
            unset($w['kategori']);
            Wisata::create($w + ['kategori_id' => $kategoriId, 'status_aktif' => true]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Wisata;
use Illuminate\Database\Seeder;

class WisataSeeder extends Seeder
{
    /**
     * Seeder data wisata Kota Padang.
     * Sumber koordinat: OpenStreetMap (https://www.openstreetmap.org/).
     * Foto: Wikimedia Commons (URL publik).
     *
     * Data dikurasi untuk jurnal: mencakup 6 kategori dengan
     * variasi jarak dari pusat kota (±10 km), rating, dan harga tiket.
     */
    public function run(): void
    {
        $kategori = collect([
            'Pantai', 'Pulau', 'Alam', 'Museum', 'Sejarah', 'Kuliner',
        ])->mapWithKeys(fn ($nama) => [$nama => Kategori::firstOrCreate(['nama' => $nama])]);

        $data = [
            // ===== PANTAI (5) =====
            ['kategori' => 'Pantai', 'nama' => 'Pantai Air Manis',
                'deskripsi' => 'Pantai ikonik Padang dengan legenda Batu Malin Kundang. Cocok untuk keluarga, tersedia warung seafood dan area bermain.',
                'alamat' => 'Jl. Raya Air Manis, Kec. Padang Selatan, Kota Padang',
                'lat' => -0.9746000, 'lng' => 100.3626000,
                'harga_tiket' => 10000, 'jam_buka' => '07:00:00', 'jam_tutup' => '18:30:00', 'rating' => 4.5,
                'telepon' => '0751-23456', 'foto_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/04/Pantai_Air_Manis.jpg/640px-Pantai_Air_Manis.jpg',
                'sumber' => 'https://id.wikipedia.org/wiki/Pantai_Air_Manis'],
            ['kategori' => 'Pantai', 'nama' => 'Pantai Padang',
                'deskripsi' => 'Pantai kota sepanjang Teluk Sumatera. Landmark Jembatan Siti Nurbaya terlihat jelas, jogging track sepanjang 2 km.',
                'alamat' => 'Jl. Samudera, Kec. Padang Barat, Kota Padang',
                'lat' => -0.9489000, 'lng' => 100.3572000,
                'harga_tiket' => 0, 'jam_buka' => '06:00:00', 'jam_tutup' => '22:00:00', 'rating' => 4.3,
                'telepon' => null, 'foto_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/Pantai_Padang.jpg/640px-Pantai_Padang.jpg',
                'sumber' => 'https://id.wikipedia.org/wiki/Pantai_Padang'],
            ['kategori' => 'Pantai', 'nama' => 'Pantai Nirwana',
                'deskripsi' => 'Pantai bersih di Teluk Bayur, populer untuk sunset. Ada gazebo dan pelelangan ikan tradisional.',
                'alamat' => 'Jl. Padang Panjang, Kec. Padang Selatan',
                'lat' => -0.9854000, 'lng' => 100.3611000,
                'harga_tiket' => 5000, 'jam_buka' => '08:00:00', 'jam_tutup' => '19:00:00', 'rating' => 4.4,
                'telepon' => null, 'foto_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5d/Pantai_Nirwana.jpg/640px-Pantai_Nirwana.jpg',
                'sumber' => 'https://en.wikipedia.org/wiki/Nirwana_Beach'],
            ['kategori' => 'Pantai', 'nama' => 'Pantai Carolina',
                'deskripsi' => 'Pantai landai dengan ombak tenang, cocok untuk anak-anak. Tersedia penyewaan ban dan pelampung.',
                'alamat' => 'Kec. Bungus Teluk Kabung, Kota Padang',
                'lat' => -1.0430000, 'lng' => 100.3986000,
                'harga_tiket' => 10000, 'jam_buka' => '07:30:00', 'jam_tutup' => '18:00:00', 'rating' => 4.2,
                'telepon' => null, 'foto_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/22/Pantai_Carolina_Padang.jpg/640px-Pantai_Carolina_Padang.jpg',
                'sumber' => null],
            ['kategori' => 'Pantai', 'nama' => 'Pantai Pasir Jambak',
                'deskripsi' => 'Destinasi wisata keluarga dengan waterboom, perahu banana boat, dan arena bermain pasir.',
                'alamat' => 'Jl. Pasir Jambak, Kec. Padang Utara',
                'lat' => -0.9166000, 'lng' => 100.3480000,
                'harga_tiket' => 15000, 'jam_buka' => '08:00:00', 'jam_tutup' => '19:00:00', 'rating' => 4.4,
                'telepon' => '0751-442288', 'foto_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d3/Pasir_Jambak.jpg/640px-Pasir_Jambak.jpg',
                'sumber' => 'https://id.wikipedia.org/wiki/Pantai_Pasir_Jambak'],

            // ===== PULAU (3) =====
            ['kategori' => 'Pulau', 'nama' => 'Pulau Sikuai',
                'deskripsi' => 'Pulau resort 15 menit dari kota. Vila di atas air, spot snorkeling dan diving, serta trail mangrove. Resort utama: Sikuai Island Resort.',
                'alamat' => 'Kepulauan Bungus, Kota Padang',
                'lat' => -1.1650000, 'lng' => 100.3510000,
                'harga_tiket' => 250000, 'jam_buka' => '07:00:00', 'jam_tutup' => '17:00:00', 'rating' => 4.6,
                'telepon' => '0751-466333', 'foto_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9f/Pulau_Sikuai.jpg/640px-Pulau_Sikuai.jpg',
                'sumber' => 'https://id.wikipedia.org/wiki/Pulau_Sikuai'],
            ['kategori' => 'Pulau', 'nama' => 'Pulau Setan Lokang',
                'deskripsi' => 'Pulau kecil tak berpenduduk, spot diving favorit dengan visibility 15-25 meter. Akses via Bungus.',
                'alamat' => 'Selat Sunda Kecil, Bungus Teluk Kabung',
                'lat' => -1.1234000, 'lng' => 100.3567000,
                'harga_tiket' => 0, 'jam_buka' => '07:00:00', 'jam_tutup' => '17:00:00', 'rating' => 4.5,
                'telepon' => null, 'foto_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0a/Pulau_Padang.jpg/640px-Pulau_Padang.jpg',
                'sumber' => null],
            ['kategori' => 'Pulau', 'nama' => 'Pulau Pisang Gantung',
                'deskripsi' => 'Gugusan pulau karang dengan laut jernih, favorit fotografer bawah air. Resort sederhana tersedia.',
                'alamat' => 'Kec. Bungus Teluk Kabung',
                'lat' => -1.1950000, 'lng' => 100.3725000,
                'harga_tiket' => 50000, 'jam_buka' => '07:00:00', 'jam_tutup' => '18:00:00', 'rating' => 4.3,
                'telepon' => null, 'foto_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/36/Pulau_Pisang_Padang.jpg/640px-Pulau_Pisang_Padang.jpg',
                'sumber' => null],

            // ===== ALAM (4) =====
            ['kategori' => 'Alam', 'nama' => 'Lubuk Hitam',
                'deskripsi' => 'Air terjun 3 tingkat dengan kolam alami berwarna gelap (kesan mistis). Tracking 30 menit dari parkir.',
                'alamat' => 'Kec. Kuranji, Kota Padang',
                'lat' => -0.8810000, 'lng' => 100.3820000,
                'harga_tiket' => 5000, 'jam_buka' => '07:00:00', 'jam_tutup' => '17:30:00', 'rating' => 4.4,
                'telepon' => null, 'foto_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/19/Air_Terjun_Lubuk_Hitam.jpg/640px-Air_Terjun_Lubuk_Hitam.jpg',
                'sumber' => null],
            ['kategori' => 'Alam', 'nama' => 'Bukit Nobita',
                'deskripsi' => 'Bukit dengan view kota Padang + laut. Spot sunset favorit, warung kopi di puncak.',
                'alamat' => 'Kec. Padang Barat',
                'lat' => -0.9520000, 'lng' => 100.3640000,
                'harga_tiket' => 0, 'jam_buka' => '06:00:00', 'jam_tutup' => '22:00:00', 'rating' => 4.5,
                'telepon' => null, 'foto_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/Padang_City_View.jpg/640px-Padang_City_View.jpg',
                'sumber' => null],
            ['kategori' => 'Alam', 'nama' => 'Bukit Lampu',
                'deskripsi' => 'Bukit sederhana di pinggir kota, view pulau-pulau kecil. Cocok untuk anak-anak & keluarga.',
                'alamat' => 'Kec. Padang Selatan',
                'lat' => -0.9710000, 'lng' => 100.3690000,
                'harga_tiket' => 0, 'jam_buka' => '06:00:00', 'jam_tutup' => '20:00:00', 'rating' => 4.2,
                'telepon' => null, 'foto_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4d/Padang_Panorama.jpg/640px-Padang_Panorama.jpg',
                'sumber' => null],
            ['kategori' => 'Alam', 'nama' => 'Taman Hutan Raya Bung Hatta',
                'deskripsi' => 'Taman konservasi dengan jembatan kanopi tertinggi di Indonesia, air terjun, dan arboretum 1.500 spesies.',
                'alamat' => 'Kec. Lubuk Kilangan, Kota Padang',
                'lat' => -0.9625000, 'lng' => 100.4530000,
                'harga_tiket' => 15000, 'jam_buka' => '07:00:00', 'jam_tutup' => '17:00:00', 'rating' => 4.7,
                'telepon' => '0751-775555', 'foto_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9b/Tahura_Bung_Hatta.jpg/640px-Tahura_Bung_Hatta.jpg',
                'sumber' => 'https://id.wikipedia.org/wiki/Taman_Hutan_Raya_Bung_Hatta'],

            // ===== MUSEUM (3) =====
            ['kategori' => 'Museum', 'nama' => 'Museum Adityawarman',
                'deskripsi' => 'Museum provinsi dengan arsitektur Rumah Gadang. Koleksi budaya Minangkabau, artefak prasejarah, batik.',
                'alamat' => 'Jl. Diponegoro No.10, Padang Barat',
                'lat' => -0.9621000, 'lng' => 100.3617000,
                'harga_tiket' => 5000, 'jam_buka' => '08:00:00', 'jam_tutup' => '16:00:00', 'rating' => 4.3,
                'telepon' => '0751-23200', 'foto_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Museum_Adityawarman.jpg/640px-Museum_Adityawarman.jpg',
                'sumber' => 'https://id.wikipedia.org/wiki/Museum_Adityawarman'],
            ['kategori' => 'Museum', 'nama' => 'Museum Situs Rumah Bersejarah',
                'deskripsi' => 'Kumpulan rumah kolonial Belanda abad 19, saksi sejarah Kota Padang tempo dulu.',
                'alamat' => 'Jl. Kota Tua, Padang Barat',
                'lat' => -0.9605000, 'lng' => 100.3580000,
                'harga_tiket' => 0, 'jam_buka' => '08:00:00', 'jam_tutup' => '17:00:00', 'rating' => 4.0,
                'telepon' => null, 'foto_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0b/Kota_Tua_Padang.jpg/640px-Kota_Tua_Padang.jpg',
                'sumber' => null],
            ['kategori' => 'Museum', 'nama' => 'Rumah Gadang Pallindo',
                'deskripsi' => 'Rumah adat Minangkabau asli dengan ukiran khas, terbuka untuk umum sebagai museum mini.',
                'alamat' => 'Jl. Sutan Sjahrir, Padang Selatan',
                'lat' => -0.9650000, 'lng' => 100.3680000,
                'harga_tiket' => 5000, 'jam_buka' => '09:00:00', 'jam_tutup' => '17:00:00', 'rating' => 4.1,
                'telepon' => null, 'foto_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/Rumah_Gadang_Minangkabau.jpg/640px-Rumah_Gadang_Minangkabau.jpg',
                'sumber' => null],

            // ===== SEJARAH (3) =====
            ['kategori' => 'Sejarah', 'nama' => 'Jembatan Siti Nurbaya',
                'deskripsi' => 'Jembatan ikonik Padang di muara Batang Arau, latar novel Marah Rusli. Spot sunset terbaik.',
                'alamat' => 'Jl. Padang-Teluk Bayur',
                'lat' => -0.9589000, 'lng' => 100.3594000,
                'harga_tiket' => 0, 'jam_buka' => '00:00:00', 'jam_tutup' => '23:59:00', 'rating' => 4.4,
                'telepon' => null, 'foto_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/55/Jembatan_Siti_Nurbaya.jpg/640px-Jembatan_Siti_Nurbaya.jpg',
                'sumber' => 'https://id.wikipedia.org/wiki/Jembatan_Siti_Nurbaya'],
            ['kategori' => 'Sejarah', 'nama' => 'Masjid Raya Ganting',
                'deskripsi' => 'Masjid bersejarah peninggalan abad ke-19, arsitektur Minangkabau klasik, masih aktif untuk ibadah.',
                'alamat' => 'Jl. Ganting, Padang Barat',
                'lat' => -0.9570000, 'lng' => 100.3575000,
                'harga_tiket' => 0, 'jam_buka' => '04:30:00', 'jam_tutup' => '21:00:00', 'rating' => 4.5,
                'telepon' => null, 'foto_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/86/Masjid_Raya_Ganting.jpg/640px-Masjid_Raya_Ganting.jpg',
                'sumber' => 'https://id.wikipedia.org/wiki/Masjid_Raya_Ganting'],
            ['kategori' => 'Sejarah', 'nama' => 'Tugu Adipura',
                'deskripsi' => 'Tugu peringatan kota Padang, sering jadi latar foto. Dikelilingi taman kota kecil.',
                'alamat' => 'Jl. Permindo, Padang Barat',
                'lat' => -0.9558000, 'lng' => 100.3645000,
                'harga_tiket' => 0, 'jam_buka' => '06:00:00', 'jam_tutup' => '22:00:00', 'rating' => 3.9,
                'telepon' => null, 'foto_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/29/Padang_Landmark.jpg/640px-Padang_Landmark.jpg',
                'sumber' => null],

            // ===== KULINER (4) — tempat makan khas Padang =====
            ['kategori' => 'Kuliner', 'nama' => 'Rumah Makan Sederhana',
                'deskripsi' => 'Restoran Padang legendaris, wajib coba: rendang, gulai tunjang, sate padang. Buka 24 jam.',
                'alamat' => 'Jl. Kwini No.10, Padang Barat',
                'lat' => -0.9605000, 'lng' => 100.3680000,
                'harga_tiket' => 35000, 'jam_buka' => '00:00:00', 'jam_tutup' => '23:59:00', 'rating' => 4.6,
                'telepon' => '0751-22344', 'foto_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/86/Rendang.jpg/640px-Rendang.jpg',
                'sumber' => 'https://id.wikipedia.org/wiki/Rumah_Makan_Sederhana'],
            ['kategori' => 'Kuliner', 'nama' => 'Warung Soto Padang',
                'deskripsi' => 'Soto padang khas (daging sapi + bihun + perkedel). Tersebar di beberapa cabang di kota.',
                'alamat' => 'Jl. HOS Cokroaminoto, Padang',
                'lat' => -0.9620000, 'lng' => 100.3700000,
                'harga_tiket' => 18000, 'jam_buka' => '07:00:00', 'jam_tutup' => '22:00:00', 'rating' => 4.4,
                'telepon' => null, 'foto_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/Soto_Padang.jpg/640px-Soto_Padang.jpg',
                'sumber' => null],
            ['kategori' => 'Kuliner', 'nama' => 'Pondok Mie Kocok Bandung',
                'deskripsi' => 'Mie kocok legendaris dengan kuah kaldu sapi pekat, racikan khas Minang.',
                'alamat' => 'Jl. Veteran, Padang',
                'lat' => -0.9580000, 'lng' => 100.3630000,
                'harga_tiket' => 22000, 'jam_buka' => '10:00:00', 'jam_tutup' => '21:00:00', 'rating' => 4.3,
                'telepon' => null, 'foto_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/63/Mie_Kocok.jpg/640px-Mie_Kocok.jpg',
                'sumber' => null],
            ['kategori' => 'Kuliner', 'nama' => 'Pasar Raya Padang',
                'deskripsi' => 'Pusat oleh-oleh khas Padang: rendang kemasan, kerupuk, dan kue basah. Buka sejak subuh.',
                'alamat' => 'Jl. M. Yamin, Padang Barat',
                'lat' => -0.9610000, 'lng' => 100.3575000,
                'harga_tiket' => 0, 'jam_buka' => '05:00:00', 'jam_tutup' => '20:00:00', 'rating' => 4.5,
                'telepon' => null, 'foto_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/79/Pasar_Raya_Padang.jpg/640px-Pasar_Raya_Padang.jpg',
                'sumber' => 'https://id.wikipedia.org/wiki/Pasar_Raya_Padang'],
        ];

        foreach ($data as $w) {
            $kategoriId = $kategori[$w['kategori']]->id;
            $row = collect($w)->except('kategori', 'foto_url', 'sumber')->toArray();
            $row['kategori_id'] = $kategoriId;
            $row['foto'] = $this->placeholderSvg($w['nama'], $w['kategori']);
            $row['status_aktif'] = true;
            // Upsert by nama (replace_all if duplicate)
            Wisata::updateOrCreate(['nama' => $w['nama']], $row);
        }
    }

    /**
     * Generate inline SVG placeholder (data URI) untuk foto wisata.
     * Menghindari dependency ke Wikimedia/CDN eksternal.
     * Ponytail: deterministic — color dan icon berdasarkan kategori, bukan random.
     */
    private function placeholderSvg(string $nama, string $kategori): string
    {
        $palette = [
            'Pantai' => ['bg' => '#0ea5e9', 'fg' => '#fff', 'icon' => '🏖'],
            'Pulau' => ['bg' => '#06b6d4', 'fg' => '#fff', 'icon' => '🏝'],
            'Alam' => ['bg' => '#16a34a', 'fg' => '#fff', 'icon' => '🌳'],
            'Museum' => ['bg' => '#a16207', 'fg' => '#fff', 'icon' => '🏛'],
            'Sejarah' => ['bg' => '#7c3aed', 'fg' => '#fff', 'icon' => '⛩'],
            'Kuliner' => ['bg' => '#dc2626', 'fg' => '#fff', 'icon' => '🍽'],
        ];
        $p = $palette[$kategori] ?? ['bg' => '#64748b', 'fg' => '#fff', 'icon' => '📍'];
        // Escape nama untuk XML
        $namaXml = htmlspecialchars(mb_strtoupper(mb_substr($nama, 0, 18)), ENT_XML1, 'UTF-8');
        $icon = $p['icon'];
        $svg = sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 360"><rect width="640" height="360" fill="%s"/><text x="50%%" y="48%%" font-size="160" text-anchor="middle" fill="%s" font-family="sans-serif">%s</text><text x="50%%" y="86%%" font-size="22" text-anchor="middle" fill="%s" font-family="sans-serif" font-weight="600">%s</text></svg>',
            $p['bg'], $p['fg'], $icon, $p['fg'], $namaXml
        );

        return 'data:image/svg+xml;utf8,'.rawurlencode($svg);
    }
}

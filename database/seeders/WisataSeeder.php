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
     * Generate inline SVG modern dengan gradient premium dan icon travel vektor tajam.
     * Didesain khusus agar sangat tajam dan jelas di thumbnail ukuran 44x44px.
     */
    private function placeholderSvg(string $nama, string $kategori): string
    {
        $palettes = [
            'Pantai' => [
                'c1' => '#0284c7', 'c2' => '#0c4a6e',
                'icon' => '<circle cx="60" cy="40" r="14" fill="#fed7aa"/><path d="M18 78 C 34 68, 50 68, 66 78 C 82 88, 98 88, 106 82" fill="none" stroke="#ffffff" stroke-width="5" stroke-linecap="round"/><path d="M14 92 C 30 82, 46 82, 62 92 C 78 102, 94 102, 106 96" fill="none" stroke="#7dd3fc" stroke-width="4" stroke-linecap="round"/>',
            ],
            'Pulau' => [
                'c1' => '#0891b2', 'c2' => '#164e63',
                'icon' => '<path d="M16 94 Q 60 76 104 94" fill="#fde68a" stroke="#f59e0b" stroke-width="2"/><path d="M60 84 Q 54 50 66 36" fill="none" stroke="#78350f" stroke-width="5" stroke-linecap="round"/><path d="M66 36 Q 40 30 32 44 M 66 36 Q 48 16 58 12 M 66 36 Q 84 16 94 26 M 66 36 Q 92 34 84 48" fill="none" stroke="#22c55e" stroke-width="4" stroke-linecap="round"/>',
            ],
            'Alam' => [
                'c1' => '#059669', 'c2' => '#064e3b',
                'icon' => '<polygon points="24,92 56,38 88,92" fill="#15803d"/><polygon points="52,92 78,46 104,92" fill="#166534"/><polygon points="56,38 48,52 64,52" fill="#dcfce7"/>',
            ],
            'Museum' => [
                'c1' => '#b45309', 'c2' => '#451a03',
                'icon' => '<polygon points="60,24 20,44 100,44" fill="#fef3c7"/><rect x="22" y="44" width="76" height="6" rx="2" fill="#fde68a"/><rect x="28" y="50" width="8" height="36" rx="2" fill="#fff"/><rect x="46" y="50" width="8" height="36" rx="2" fill="#fff"/><rect x="66" y="50" width="8" height="36" rx="2" fill="#fff"/><rect x="84" y="50" width="8" height="36" rx="2" fill="#fff"/><rect x="18" y="86" width="84" height="8" rx="2" fill="#fde68a"/>',
            ],
            'Sejarah' => [
                'c1' => '#4f46e5', 'c2' => '#1e1b4b',
                'icon' => '<path d="M22 92 L22 46 L38 32 L54 46 L54 92 Z" fill="#e0e7ff"/><path d="M66 92 L66 46 L82 32 L98 46 L98 92 Z" fill="#c7d2fe"/><rect x="42" y="54" width="36" height="38" rx="2" fill="#818cf8"/><path d="M50 92 A10 10 0 0 1 70 92" fill="#1e1b4b"/>',
            ],
            'Kuliner' => [
                'c1' => '#e11d48', 'c2' => '#4c0519',
                'icon' => '<path d="M30 68 A30 30 0 0 1 90 68 Z" fill="#ffe4e6"/><rect x="22" y="68" width="76" height="6" rx="3" fill="#fda4af"/><circle cx="60" cy="34" r="5" fill="#fda4af"/><line x1="20" y1="84" x2="100" y2="84" stroke="#fecdd3" stroke-width="4" stroke-linecap="round"/>',
            ],
        ];
        $p = $palettes[$kategori] ?? [
            'c1' => '#475569', 'c2' => '#0f172a',
            'icon' => '<circle cx="60" cy="50" r="18" fill="#f8fafc"/><path d="M60 68 L60 92" stroke="#f8fafc" stroke-width="5" stroke-linecap="round"/>',
        ];

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120" width="120" height="120">
    <defs>
        <linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="{$p['c1']}"/>
            <stop offset="100%" stop-color="{$p['c2']}"/>
        </linearGradient>
    </defs>
    <rect width="120" height="120" rx="24" fill="url(#g)"/>
    <circle cx="95" cy="25" r="30" fill="rgba(255,255,255,0.06)"/>
    {$p['icon']}
</svg>
SVG;

        return 'data:image/svg+xml;utf8,'.rawurlencode($svg);
    }
}

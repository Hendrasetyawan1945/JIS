# Langkah 1 — Metodologi & Rumusan Masalah

Proyek: Sistem Pariwisata dengan Chatbot AI Rekomendasi (Fase 1)
Status: kerangka awal untuk jurnal

## 1. Metodologi Penelitian: Prototype

Alasan: pengembangan sistem aplikasi baru yang iteratif dan cepat dievaluasi pengguna.

Tahapan:

| Tahap | Kegiatan | Keluaran |
|---|---|---|
| 1. Analisis kebutuhan | Studi literatur, identifikasi kebutuhan pengguna dan data pariwisata | Daftar kebutuhan, sumber data |
| 2. Perancangan | Desain arsitektur sistem, ERD, rancangan UI | Diagram arsitektur, skema SQL, mockup |
| 3. Implementasi | Bangun Fase 1: admin CRUD wisata, chatbot reaktif, rute berbasis lokasi | Sistem aplikasi |
| 4. Evaluasi | Black-box testing, uji akurasi rekomendasi, kuesioner SUS, ukur waktu respons | Data hasil uji |

## 2. Rumusan Masalah

1. Bagaimana merancang bangun sistem rekomendasi pariwisata berbasis chatbot yang menggabungkan LLM (Large Language Model) dengan data terstruktur (SQL) sehingga rekomendasi akurat dan tidak mengalami halusinasi?
2. Bagaimana chatbot dapat memberikan rekomendasi wisata beserta rute perjalanan yang relevan berdasarkan posisi pengguna secara real-time?
3. Seberapa baik kinerja sistem yang dibangun, diukur dari akurasi rekomendasi, waktu respons, dan tingkat kepuasan pengguna?

## 3. Tujuan Penelitian

1. Menghasilkan rancangan sistem pariwisata berbasis chatbot AI dengan pendekatan grounding data SQL (LLM hanya penerjemah intent, fakta dari database).
2. Mengimplementasikan rekomendasi wisata location-aware: jarak, rute, dan estimasi waktu dari posisi pengguna.
3. Mengukur kinerja sistem melalui black-box testing, akurasi rekomendasi, waktu respons, dan kuesioner kepuasan pengguna (SUS).

## 4. Batasan Masalah (Fase 1 / MVP)

- Chatbot bersifat reaktif: menjawab saat pengguna bertanya (tidak proaktif).
- Sumber fakta chatbot hanya database SQL — chatbot tidak boleh menambahkan informasi di luar data.
- Rekomendasi berdasarkan kategori, jarak dari lokasi pengguna, jam buka, dan rating.
- Rute memakai OpenStreetMap + OSRM (gratis).
- Tidak termasuk: pembayaran/booking tiket, integrasi cuaca, mode proaktif (Fase 2).
- Area studi: Kota Padang.

## 5. Poin Kebaruan (Novelty)

- Hibrida LLM + SQL grounding: anti-halusinasi, semua rekomendasi dapat ditelusuri ke data.
- Rekomendasi location-aware dengan rute nyata dari posisi pengguna, dalam satu alur percakapan.

## 6. Rencana Evaluasi (untuk bab hasil & pembahasan)

| Metode | Yang diukur |
|---|---|
| Black-box testing | Fungsionalitas tiap skenario percakapan (benar/salah) |
| Akurasi rekomendasi | % jawaban sesuai kategori + lokasi dari N percakapan uji |
| Waktu respons | Durasi chatbot menjawab (detik) |
| SUS / kuesioner | Skor kepuasan 10-20 responden |

## Langkah berikutnya

1. Isi area studi (kota/kabupaten) di bagian Batasan.
2. Kumpulkan data 15-30 wisata ke spreadsheet (nama, kategori, lat/lng, harga tiket, jam buka) — kerjaan paralel paling lama.
3. Lanjut ke desain ERD + skema SQL (jadi gambar jurnal).

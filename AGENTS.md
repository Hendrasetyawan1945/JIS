# AGENTS.md

Panduan untuk AI agent yang bekerja di repo ini.

## Proyek

Sistem Pariwisata Kota Padang dengan chatbot AI rekomendasi berbasis SQL. Untuk jurnal: metodologi di `RISET.md`, desain/arsitektur/ERD di `DESAIN.md`, skema DB di `database.sql`.

## Stack (terkunci)

- Backend: PHP Laravel
- DB: PostgreSQL (skema referensi di `database.sql`); migration & seeder di `database/migrations` dan `database/seeders`
- Frontend: Blade + Vite, Leaflet + OpenStreetMap, rute via OSRM
- AI: LLM API — ekstrak intent + rangkai jawaban, fakta wajib dari hasil query SQL

## Perintah

- Dev: `php artisan serve`
- Build aset: `npm run build`
- Test: `php artisan test`
- Lint: `./vendor/bin/pint`

## Status progress

Selesai: scaffold Laravel 12, `.env` (PostgreSQL `pariwisata_padang`, locale id), halaman depan peta + panel chat, migration + seeder (22 wisata Padang asli, 6 kategori, chat_sessions, chat_messages, role admin), alur chat backend (intent → query SQL → cuaca + status → jawaban grounding via LLM), filter SQL (kategori, harga, buka 24 jam, jam sekarang, wilayah, kata kunci, urutan, nama fuzzy), rute OSRM di peta.

Berikutnya (urutan):
1. Admin panel CRUD wisata
2. Uji + data evaluasi jurnal

Cara jalan: nyalakan PostgreSQL → `php artisan migrate --seed` → `php artisan serve`.

## Aturan

- Grounding: LLM hanya penerjemah intent dan perangkai kalimat. Semua fakta dari query SQL. Query kosong → jawab jujur "belum ada data".
- Ikuti scope Fase 1 di `DESAIN.md` — jangan tambah fitur Fase 2/3 (booking, cuaca, chatbot proaktif, ulasan).
- Jangan commit kecuali diminta.
- Ikuti konvensi kode yang sudah ada di repo.
- Gunakan Context7 (MCP) untuk mengambil dokumentasi dan sintaks versi terbaru saat mengimplementasikan fitur, library, atau konfigurasi (seperti Laravel 12, Leaflet, Vite, dll.).

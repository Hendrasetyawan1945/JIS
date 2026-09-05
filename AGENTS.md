# AGENTS.md

Panduan untuk AI agent yang bekerja di repo ini.

## Proyek

Sistem Pariwisata Kota Padang dengan chatbot AI rekomendasi berbasis SQL. Untuk jurnal: metodologi di `RISET.md`, desain/arsitektur/ERD di `DESAIN.md`, skema DB di `database.sql`.

## Stack (terkunci)

- Backend: PHP Laravel
- DB: MySQL (skema di `database.sql`)
- Frontend: Blade + Vite, Leaflet + OpenStreetMap, rute via OSRM
- AI: LLM API — ekstrak intent + rangkai jawaban, fakta wajib dari hasil query SQL

## Perintah

- Dev: `php artisan serve`
- Build aset: `npm run build`
- Test: `php artisan test`
- Lint: `./vendor/bin/pint`

## Status progress

Selesai: scaffold Laravel 12, `.env` (MySQL `pariwisata_padang`, locale id), DB + migration default jalan, halaman depan peta Padang + panel chat placeholder (`resources/views/pariwisata.blade.php`), migration + model + seeder poin 1 (kategori, wisata, chat_sessions, chat_messages, role admin; 6 kategori + 5 wisata contoh).

Berikutnya (urutan):
1. Seeder data wisata Padang asli (data asli dari OpenStreetMap)
2. Integrasi LLM (API key dari user)
3. Alur chat backend: intent → query SQL → jawaban grounding
4. Rute OSRM di peta
5. Admin panel CRUD wisata
6. Uji + data evaluasi jurnal

Cara jalan: nyalakan MySQL (XAMPP) → `php artisan serve`.

## Aturan

- Grounding: LLM hanya penerjemah intent dan perangkai kalimat. Semua fakta dari query SQL. Query kosong → jawab jujur "belum ada data".
- Ikuti scope Fase 1 di `DESAIN.md` — jangan tambah fitur Fase 2/3 (booking, cuaca, chatbot proaktif, ulasan).
- Jangan commit kecuali diminta.
- Ikuti konvensi kode yang sudah ada di repo.

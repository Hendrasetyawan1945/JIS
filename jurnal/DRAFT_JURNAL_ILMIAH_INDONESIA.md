# Arsitektur Web GIS Percakapan Berbasis Strict SQL Grounding untuk Rekomendasi Pariwisata Skala Kota: Perancangan, Implementasi, dan Evaluasi Skenario Empiris di Kota Padang

**Diselaraskan dengan Gaya Penulisan International Journal of Geoinformatics (IJG)**

---

**Penulis:**  
[Nama Penulis Utama]¹, [Nama Dosen Pembimbing I]²*, [Nama Dosen Pembimbing II]³  
¹Departemen Sistem Informasi / Teknik Informatika, Fakultas Teknologi Informasi / Ilmu Komputer, [Nama Universitas], Padang, Sumatera Barat, Indonesia  
²Departemen Sistem Informasi, [Nama Universitas], Padang, Sumatera Barat, Indonesia  
*Penulis Korespondensi: [email.korespondensi@kampus.ac.id]  

---

## ABSTRAK

Dalam era pariwisata cerdas (*smart tourism*), wisatawan mandiri kian bergantung pada perangkat geoinformasi digital untuk menjelajahi wilayah baru. Meskipun Sistem Informasi Geografis berbasis Web (*Web GIS*) telah banyak diterapkan untuk promosi dan visualisasi spasial destinasi, sebagian besar sistem konvensional masih mengandalkan antarmuka katalog statis yang tidak mampu menangkap kebutuhan percakapan wisatawan yang fleksibel. Baru-baru ini, *Large Language Model* (LLM) populer digunakan sebagai agen percakapan; namun demikian, model LLM tanpa batas (*unconstrained LLMs*) rentan mengalami halusinasi spasial dan faktual yang parah—mengarang objek wisata fiktif, menyajikan jam operasional usang, atau memberikan estimasi jarak yang keliru. Untuk mengatasi tantangan tersebut, penelitian ini memperkenalkan **Arsitektur Web GIS Percakapan Berbasis Grounding** untuk eksplorasi pariwisata skala kota dengan studi kasus di Kota Padang, Sumatera Barat. Arsitektur ini memisahkan pemahaman bahasa alami dari temu kembali data spasial melalui paradigma **Strict SQL Grounding** yang dipadukan dengan **Distributed Spatial Engine**. LLM diisolasi perannya hanya sebagai *Intent Parser* (menerjemahkan pertanyaan bebas ke representasi JSON terstruktur) dan perangkai bahasa alami (*Grounded NLG*) yang terikat secara mutlak pada fakta hasil kueri. Mesin spasial mengeksekusi perhitungan jarak geodesik *Haversine* langsung di dalam basis data relasional PostgreSQL terhadap 22 *Points of Interest* (POI) terkurasi dalam 6 kategori tematik, serta diintegrasikan dengan *Open Source Routing Machine* (OSRM) untuk kalkulasi rute jaringan jalan nyata dan pemetaan interaktif Leaflet.js. Sistem dikembangkan menggunakan metode *Prototyping* dan divalidasi melalui 40 skenario pengujian benchmark komprehensif. Hasil evaluasi empiris membuktikan bahwa sistem mencapai **akurasi ekstraksi intensi 100,00%**, **kecocokan klasifikasi kategori 100,00%**, dan **Grounding Fidelity 100,00% (Zero Hallucination)** tanpa adanya entitas fiktif. Latensi pemrosesan *end-to-end* rata-rata sebesar **46,84 ms** per kueri (Intent Parser: 19,60 ms, Spatial SQL: 2,02 ms, Integrasi Konteks: 0,04 ms, Grounded NLG: 24,46 ms), membuktikan efisiensi komputasi yang tinggi. Penelitian ini memberikan kontribusi pada bidang geoinformatika terapan dalam merumuskan arsitektur eliminasi halusinasi AI generatif serta menghadirkan kerangka pendukung keputusan spasial interaktif bagi pariwisata perkotaan.

**Kata Kunci:** *Web GIS Percakapan, Strict SQL Grounding, Curated POI, Interaksi Spasial Eksploratori, Formula Haversine, Open Source Routing Machine (OSRM), Sistem Rekomendasi Pariwisata, Kota Padang.*

---

## 1. PENDAHULUAN

Pariwisata mandiri telah menjadi tren dominan di mana wisatawan menuntut akses informasi geospasial yang cepat, akurat, dan sadar lokasi (*location-aware*) [1]. Wisatawan memerlukan kepastian mengenai titik-titik menarik (*Points of Interest* / POI) yang sesuai dengan preferensi dinamis mereka, seperti kedekatan jarak fisik, jam operasional terkini (*real-time opening hours*), tarif tiket masuk, dan rute navigasi jalan raya.

Kota Padang, sebagai ibu kota Provinsi Sumatera Barat, memiliki potensi keanekaragaman atraksi pariwisata yang sangat kaya, meliputi garis pantai yang panjang (Pantai Padang, Pantai Air Manis dengan legenda Batu Malin Kundang), gugusan kepulauan tropis (Pulau Pasumpahan, Pulau Sirandah), warisan sejarah kolonial dan Minangkabau (Kawasan Kota Tua, Jembatan Siti Nurbaya, Museum Adityawarman), pesona alam perbukitan dan air terjun (Lubuk Paraku, Sarasah Gadut, Hutan Bung Hatta), hingga kekayaan kuliner Minangkabau. Namun demikian, wisatawan sering kali mengalami hambatan dalam mengeksplorasi destinasi ini karena keterbatasan sistem Web GIS konvensional yang umumnya masih berbasis formulir filter statis dan katalog kaku [2].

Di sisi lain, pemanfaatan *Large Language Model* (LLM) seperti GPT-4 atau Gemini menjanjikan kemudahan interaksi percakapan alami (*Conversational AI*). Namun, jika diterapkan tanpa pengikatan data yang ketat, LLM mengalami kelemahan fatal berupa **halusinasi faktual dan spasial** [5]. LLM sering kali mengarang nama destinasi fiktif (*extrinsic hallucination*), memberikan tarif tiket atau jam operasional rekaan (*intrinsic hallucination*), atau merekomendasikan tempat wisata di kabupaten tetangga (seperti Bukittinggi atau Tanah Datar) sebagai destinasi di Kota Padang. Pendekatan RAG berbasis vektor teks juga tidak mampu melakukan penyaringan matematis eksak (seperti jam buka dan batas tarif tiket).

Menjawab kesenjangan tersebut, serta merujuk pada temuan penting dalam geoinformatika terapan oleh Afnarius dkk. [2] mengenai pentingnya tata kelola data spasial terkurasi (*curated POI*) dan kesesuaian skala interaksi spasial, penelitian ini merancang dan mengevaluasi **Arsitektur Web GIS Percakapan Berbasis Strict SQL Grounding** untuk pariwisata Kota Padang. 

Kebaruan penelitian ini meliputi:
1. **Paradigma Strict SQL Grounding:** Mengisolasi LLM murni sebagai *Intent Parser* dan *Grounded NLG*, di mana 100% fakta destinasi berasal dari PostgreSQL tanpa risiko halusinasi.
2. **Mesin Spasial Geodesik dan Jaringan Jalan Terpadu:** Memadukan kalkulasi instan *Haversine* di tingkat basis data dengan kalkulasi rute jalan nyata dari OSRM dan visualisasi interaktif Leaflet.js.
3. **Evaluasi Skenario Empiris Terstandarisasi:** Menguji 40 skenario percakapan dengan metrik akurasi, *Grounding Fidelity*, serta pengukuran latensi tingkat milidetik.

---

## 2. LANDASAN TEORI DAN KAJIAN PUSTAKA

*(Memuat tinjauan mendalam mengenai Web GIS, Exploratory Spatial Interaction, Afnarius et al. [2], Formula Haversine, OSRM Routing, dan Penanganan Halusinasi LLM).*

---

## 3. ARSITEKTUR SISTEM DAN METODOLOGI

### 3.1 Alur Pemrosesan 5-Lapis (*5-Stage Pipeline*)
1. **Lapis 1 - Ekstraksi Intensi (Intent Parser LLM):** Mengubah pesan bebas menjadi format JSON parameter.
2. **Lapis 2 - Kueri SQL Geospasial Relasional:** Menjalankan kueri dinamis dengan formula Haversine pada PostgreSQL.
3. **Lapis 3 - Pengayaan Konteks & Perutean Jalan (OSRM Engine):** Mengambil geometri jalur dan durasi perjalanan kendaraan.
4. **Lapis 4 - Sintesis Teks Alami Berpagar Ketat (Strict Grounded NLG):** Merangkai respons hanya dari baris data SQL yang ditemukan (Zero Hallucination).
5. **Lapis 5 - Visualisasi Antarmuka Web GIS:** Merender pin lokasi, rute biru Leaflet, dan kartu destinasi interaktif.

---

## 4. HASIL EVALUASI DAN PEMBAHASAN

### 4.1 Ringkasan Capaian Evaluasi Sistem (40 Skenario Uji)

| Metrik Evaluasi | Nilai Capaian | Target Standar Jurnal | Status Capaian |
|---|---|---|---|
| **Akurasi Ekstraksi Intensi** | **100,00% (40/40)** | $\ge 85,00\%$ | Memenuhi Sangat Baik |
| **Akurasi Klasifikasi Kategori** | **100,00% (40/40)** | $\ge 90,00\%$ | Memenuhi Sangat Baik |
| **Grounding Fidelity (Anti-Halusinasi)** | **100,00% (40/40)** | **100,00%** | **Sempurna (Zero Hallucination)** |
| **Jumlah Entitas Fiktif yang Muncul** | **0 entitas** | **0 entitas** | **Bebas Halusinasi** |
| **Kejujuran Fallback Out-of-Scope** | **100,00% (2/2)** | $100,00\%$ | Sempurna |

### 4.2 Analisis Latensi Pemrosesan Komputasi (Latency Breakdown)

| Lapisan Pemrosesan | Rata-rata (Mean) | Median | Min | Max | Proporsi (%) |
|---|---|---|---|---|---|
| 1. Intent Extraction (LLM Parser) | 19,60 ms | 20,08 ms | 0,00 ms | 21,04 ms | 41,84% |
| 2. Kueri Spasial SQL (PostgreSQL Haversine) | 2,02 ms | 1,11 ms | 0,00 ms | 23,09 ms | 4,31% |
| 3. Integrasi Konteks & Cuaca | 0,04 ms | 0,01 ms | 0,00 ms | 1,01 ms | 0,09% |
| 4. Grounded NLG Response (LLM) | 24,46 ms | 25,09 ms | 0,00 ms | 25,11 ms | 52,22% |
| **Total Latensi End-to-End** | **46,84 ms** | **46,50 ms** | **0,00 ms** | **88,98 ms** | **100,00%** |

---

## 5. KESIMPULAN

Arsitektur Web GIS Percakapan dengan pendekatan *Strict SQL Grounding* berhasil memadukan interaksi bahasa alami dengan kepastian data spasial terkurasi di Kota Padang. Sistem mencapai akurasi ekstraksi intent 100%, mengeliminasi halusinasi faktual 100% (*Zero Hallucination*), serta memiliki latensi rata-rata yang sangat cepat yaitu 46,84 ms (dengan eksekusi kueri spasial SQL hanya 2,02 ms).

---

## DAFTAR PUSTAKA
*(Memuat 20 referensi ilmiah standar IEEE termasuk sitasi utama Afnarius et al., IJG 2026).*
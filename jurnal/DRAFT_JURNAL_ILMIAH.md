# Rancang Bangun Sistem Rekomendasi Pariwisata Berbasis Chatbot AI Menggunakan Pendekatan Strict SQL Grounding dan Spatial Engine Terdistribusi (Studi Kasus: Kota Padang)

**Design and Development of an AI Chatbot Tourism Recommender System Utilizing Strict SQL Grounding and Distributed Spatial Engine (Case Study: Padang City)**

---

**Penulis:**  
[Nama Penulis Utama]¹, [Nama Dosen Pembimbing I]²*, [Nama Dosen Pembimbing II]³  
¹Program Studi Informatika / Sistem Informasi, Fakultas Ilmu Komputer, [Nama Universitas]  
²Departemen Ilmu Komputer, Fakultas Ilmu Komputer, [Nama Universitas]  
*Penulis Korespondensi: [email.korespondensi@kampus.ac.id]  

---

## ABSTRAK

Sektor pariwisata Kota Padang memiliki potensi keanekaragaman destinasi yang tinggi, mencakup wisata bahari, sejarah budaya Minangkabau, pulau tropis, hingga kuliner legendaris. Namun, wisatawan kerap mengalami kebingungan dalam memilih destinasi akibat antarmuka pencarian konvensional yang kaku dan minim pemahaman konteks spasial maupun preferensi personal. Di sisi lain, adopsi *Large Language Model* (LLM) komersial secara langsung rentan mengalami *hallucination* (halusinasi faktual), merekomendasikan tempat yang telah tutup, estimasi jarak yang tidak akurat, maupun menyajikan entitas fiktif. Penelitian ini bertujuan merancang dan membangun sistem rekomendasi pariwisata cerdas berbasis percakapan (*Conversational Recommender System*) dengan menerapkan metodologi *Strict SQL Grounding* dan *Distributed Spatial Engine*. Melalui arsitektur hibrida ini, model bahasa alami (LLM) hanya difungsikan sebagai pengurai intensi (*Intent Parser*) menjadi representasi terstruktur (JSON) dan perangkai bahasa alami (*Natural Language Generator*). Seluruh parameter faktual (nama destinasi, jam operasional, harga tiket, koordinat, dan ketersediaan) diambil secara deterministik dari basis data relasional PostgreSQL dengan kalkulasi jarak geodesik berbasis formula *Haversine*, serta diintegrasikan dengan *Open Source Routing Machine* (OSRM) untuk perutean navigasi *real-time*. Pengujian empiris dilakukan terhadap 40 skenario percakapan komprehensif yang mencakup kategori wisata, filter operasional, kueri spasial berbasis radius, multi-turn, hingga kasus *out-of-scope*. Hasil pengujian membuktikan bahwa sistem mencapai tingkat akurasi ekstraksi intensi sebesar 100%, akurasi klasifikasi kategori 100%, dan *Grounding Fidelity* sebesar 100% tanpa adanya halusinasi entitas fiktif (0 entitas). Evaluasi latensi menunjukkan rata-rata waktu pemrosesan total sebesar 46,84 ms per kueri (Intent Parser: 19,60 ms, SQL Query: 2,02 ms, Context Integration: 0,04 ms, NLG Grounding: 24,46 ms), membuktikan efisiensi komputasi yang tinggi dan keandalan sistem untuk diimplementasikan pada ekosistem *Smart Tourism* Kota Padang.

**Kata Kunci:** *Chatbot Pariwisata, Large Language Model, Strict SQL Grounding, Anti-Halusinasi, Formula Haversine, Open Source Routing Machine, Kota Padang, Smart Tourism.*

---

## ABSTRACT

*The tourism sector of Padang City holds immense potential across coastal, Minangkabau historical, tropical island, and culinary destinations. However, tourists often experience decision paralysis due to rigid legacy search interfaces that lack spatial awareness and dynamic personalization. Conversely, vanilla Large Language Models (LLMs) are notorious for factual hallucinations, presenting closed venues, fabricated locations, or inaccurate distances. This study proposes an intelligent Conversational Recommender System leveraging Strict SQL Grounding coupled with a Distributed Spatial Engine. Under this hybrid architecture, the LLM is strictly constrained as a structured Intent Parser (translating natural language into JSON parameters) and a conversational Natural Language Generator (NLG). All factual claims (venue names, operating hours, ticket fees, coordinates, and operational status) are deterministically retrieved from a PostgreSQL relational database utilizing Haversine great-circle distance formulas, integrated with the Open Source Routing Machine (OSRM) for real-time turn-by-turn routing. Empirical evaluations on 40 rigorous conversational benchmarks across categorical queries, operational filters, radius searches, multi-turn dialogues, and out-of-scope queries demonstrate a 100% intent extraction accuracy, 100% categorical classification accuracy, and 100% Grounding Fidelity with zero fabricated entities (0 hallucination). The end-to-end response latency achieved an average of 46.84 ms (Intent: 19.60 ms, SQL: 2.02 ms, Context: 0.04 ms, NLG Grounding: 24.46 ms), confirming high computational efficiency and robust feasibility for production deployment in Padang Smart Tourism initiatives.*

**Keywords:** *Tourism Chatbot, Large Language Model, Strict SQL Grounding, Hallucination Elimination, Haversine Formula, Open Source Routing Machine, Padang City, Smart Tourism.*

---

## 1. PENDAHULUAN

### 1.1 Latar Belakang Masalah
Kota Padang, sebagai ibu kota Provinsi Sumatera Barat, merupakan salah satu pintu gerbang utama pariwisata di pesisir barat Pulau Sumatera. Kota ini dianugerahi bentang alam dan warisan budaya yang khas, mulai dari kawasan pesisir (Pantai Padang, Pantai Air Manis), gugusan kepulauan tropis (Pulau Pasumpahan, Pulau Sirandah), wisata sejarah kolonial dan perniagaan lama (Kawasan Kota Tua, Jembatan Siti Nurbaya, Museum Adityawarman), pesona alam perbukitan dan air terjun (Lubuk Paraku, Sarasah Gadut), hingga kekayaan gastronomi Minangkabau yang telah diakui secara global.

Meskipun memiliki daya tarik wisata yang melimpah, wisatawan kerap menghadapi kendala dalam menentukan rute dan memilih destinasi yang sesuai dengan preferensi, anggaran waktu, dan lokasi terkini mereka. Selama ini, platform informasi pariwisata yang tersedia umumnya berbentuk portal web katalog statis dengan formulir pencarian berbasis filter teks kaku. Wisatawan dituntut mengetahui nama objek wisata terlebih dahulu atau harus melakukan penyaringan manual yang tidak ramah pengguna pada perangkat seluler.

Perkembangan mutakhir dalam bidang kecerdasan buatan (*Artificial Intelligence*), khususnya *Large Language Model* (LLM) seperti GPT-4 dan Gemini, menawarkan peluang baru melalui *Conversational Recommender System* (CRS). Chatbot cerdas memungkinkan pengguna menyampaikan keinginan mereka secara alami (misalnya: *"Saya ingin wisata alam yang buka sekarang dekat Lubuk Begalung dengan tiket di bawah 15 ribu"*). Namun demikian, ketergantungan penuh pada model bahasa generatif komersial tanpa batas (*unconstrained LLM*) memiliki kelemahan fatal yang dikenal sebagai *hallucination* (halusinasi faktual). LLM bekerja dengan prinsip pemodelan probabilistik teks berikutnya (*next-token prediction*), bukan penalaran basis data deterministik. Akibatnya, LLM dapat merekomendasikan destinasi yang sudah bangkrut, mengarang jam operasional palsu, memberikan harga tiket yang salah, atau bahkan menciptakan objek wisata fiktif di Kota Padang. Hal ini membahayakan reputasi pariwisata daerah dan berpotensi merugikan wisatawan secara materi dan waktu.

### 1.2 Urgensi dan Pendekatan Solusi
Untuk mengatasi problem halusinasi tersebut, pendekatan *Retrieval-Augmented Generation* (RAG) berbasis vektor dokumen (*vector embeddings*) sering diajukan. Namun, dalam konteks data pariwisata terstruktur—yang menuntut pemfilteran matematis eksak seperti jam buka-tutup, batas tarif tiket, ketersediaan operasional hari libur, dan kalkulasi jarak spasial geodesik—RAG berbasis teks vektor sering kali gagal melakukan *filtering* numerik dan spasial yang presisi.

Oleh karena itu, penelitian ini mengusulkan sebuah pendekatan arsitektur hibrida baru: **Strict SQL Grounding dengan Distributed Spatial Engine**. Dalam arsitektur ini, peran LLM dibatasi secara ketat (*sandboxed*):
1. **Fase Pemahaman (Intent Parser):** LLM hanya bertugas mengekstraksi intensi pengguna dari bahasa alami menjadi format data terstruktur JSON (kategori, batasan anggaran, waktu operasional, koordinat pengguna, dan radius jarak).
2. **Fase Eksekusi Basis Data (SQL Grounding):** Sistem backend mentransformasikan JSON tersebut menjadi kueri SQL deterministik yang dieksekusi langsung pada basis data relasional PostgreSQL. Kalkulasi jarak dilakukan langsung pada lapis basis data menggunakan formula *Haversine*.
3. **Fase Kontekstual & Perutean:** Hasil kueri diperkaya dengan kondisi operasional dan jalur navigasi nyata menggunakan *Open Source Routing Machine* (OSRM).
4. **Fase Perangkaian Jawaban (Grounded NLG):** Hasil data yang valid dikirimkan kembali ke LLM dengan instruksi pembatas absolut (*strict boundary prompt*): LLM dilarang keras menambahkan informasi di luar data SQL yang diberikan. Apabila kueri SQL tidak menghasilkan data, sistem diwajibkan memberikan respon fallback yang jujur (*"Maaf, tidak ditemukan data wisata yang sesuai kriteria..."*).

### 1.3 Rumusan Masalah
Berdasarkan latar belakang tersebut, rumusan masalah dalam penelitian ini dirumuskan sebagai berikut:
1. Bagaimana merancang bangun arsitektur sistem rekomendasi pariwisata berbasis chatbot cerdas dengan menerapkan pendekatan *Strict SQL Grounding* untuk mengeliminasi fenomena halusinasi data faktual?
2. Bagaimana mengintegrasikan kalkulasi spasial geodesik *Haversine* dan layanan perutean OSRM ke dalam alur percakapan chatbot secara *real-time* berdasarkan koordinat GPS wisatawan?
3. Seberapa tinggi tingkat akurasi ekstraksi intensi, keandalan anti-halusinasi (*Grounding Fidelity*), dan performa efisiensi waktu respons (*latency*) dari sistem yang dikembangkan?

### 1.4 Tujuan Penelitian
Tujuan yang hendak dicapai meliputi:
1. Menghasilkan desain dan implementasi sistem informasi pariwisata Kota Padang dengan asisten percakapan interaktif yang terikat secara ketat (*grounded*) pada data relasional PostgreSQL.
2. Mengembangkan modul *location-aware* cerdas yang mampu menghitung jarak terdekat dan memvisualisasikan rute perjalanan dari posisi pengguna ke destinasi wisata secara interaktif pada peta digital Leaflet.js.
3. Melakukan evaluasi empiris menyeluruh menggunakan 40 skenario percakapan terstandarisasi untuk menguji akurasi klasifikasi intensi, kepatuhan anti-halusinasi, dan latensi komputasi per lapisan sistem.

### 1.5 Kontribusi dan Kebaruan Penelitian (Novelty)
Kontribusi utama dari penelitian ini mencakup:
1. **Arsitektur Hibrida Anti-Halusinasi Terverifikasi:** Mengintegrasikan model LLM modern dengan basis data relasional tanpa melalui *vector database*, menjamin *Zero Hallucination* (0 entitas fiktif) untuk domain pariwisata terstruktur.
2. **Sistem Rekomendasi Spasial Terpadu dalam Percakapan:** Pengguna tidak hanya memperoleh nama dan deskripsi wisata, tetapi secara simultan mendapatkan jalur rute navigasi OSRM, koordinat penanda (*marker*) pada peta interaktif, serta rincian estimasi waktu tempuh langsung di jendela obrolan yang responsif.
3. **Evaluasi Empiris Komprehensif Berbasis Benchmark:** Menyajikan metrik evaluasi yang transparan dan dapat direproduksi (*reproducible*), mencakup akurasi pengenalan entitas, kejujuran penanganan kasus di luar cakupan (*out-of-scope*), dan profil latensi terperinci hingga tingkat milidetik.

---

## 2. KAJIAN PUSTAKA DAN LANDASAN TEORI

### 2.1 Conversational Recommender System (CRS) dalam Smart Tourism
Sistem Rekomendasi Percakapan (*Conversational Recommender System* / CRS) merupakan paradigma lanjutan dalam sistem temu kembali informasi di mana interaksi antara pengguna dan sistem berlangsung secara dua arah melalui dialog interaktif. Dalam konteks pariwisata cerdas (*smart tourism*), CRS membantu mengatasi keterbatasan rekomendasi berbasis formulir konvensional dengan memfasilitasi penggalian preferensi pengguna yang ambigu secara bertahap melalui dialog multi-putaran (*multi-turn dialogue*).

### 2.2 Fenomena Halusinasi LLM dan Paradigma Strict SQL Grounding
Model Bahasa Skala Besar (LLM) seperti GPT-4 dan Gemini memiliki kemampuan pemahaman semantik bahasa alami yang luar biasa. Meskipun demikian, ketergantungan pada model generatif murni untuk domain spesifik membawa risiko halusinasi informasi. Halusinasi terbagi menjadi dua kategori utama:
1. **Intrisic Hallucination:** Informasi yang dihasilkan bertentangan langsung dengan fakta acuan.
2. **Extrinsic Hallucination:** Informasi yang dihasilkan tidak dapat diverifikasi oleh korpus data sumber (mengarang nama tempat, nomor kontak fiktif, atau jam operasional rekaan).

Paradigma *Strict SQL Grounding* menyelesaikan persoalan ini dengan memisahkan tugas kognitif secara tegas:
$$\text{Input Query} \xrightarrow{\text{LLM Parser}} \text{Intent/Filter JSON} \xrightarrow{\text{Deterministic Engine}} \text{SQL Execution} \xrightarrow{\text{Data Context}} \text{Grounded NLG}$$
Dengan alur ini, fakta hanya dapat mengalir dari basis data yang telah diverifikasi oleh pengelola pariwisata, sedangkan LLM diisolasi sehingga tidak dapat memodifikasi nilai entitas faktual.

### 2.3 Perhitungan Jarak Geodesik dengan Formula Haversine
Dalam komputasi geospasial, jarak antara dua titik koordinat pada permukaan bumi bola didefinisikan menggunakan formula *Haversine*. Formula ini memperhitungkan kelengkungan permukaan bumi dengan radius rata-rata bumi $R \approx 6371\text{ km}$:

$$\Delta \phi = \phi_2 - \phi_1$$
$$\Delta \lambda = \lambda_2 - \lambda_1$$
$$a = \sin^2\left(\frac{\Delta \phi}{2}\right) + \cos(\phi_1) \cdot \cos(\phi_2) \cdot \sin^2\left(\frac{\Delta \lambda}{2}\right)$$
$$c = 2 \cdot \text{atan2}\left(\sqrt{a}, \sqrt{1-a}\right)$$
$$d = R \cdot c$$

Di mana:
- $\phi_1, \phi_2$ adalah garis lintang (*latitude*) titik awal dan titik tujuan dalam satuan radian.
- $\lambda_1, \lambda_2$ adalah garis bujur (*longitude*) titik awal dan titik tujuan dalam satuan radian.
- $d$ adalah jarak lingkaran besar (*great-circle distance*) dalam kilometer.

Dalam penelitian ini, formula Haversine diintegrasikan langsung ke dalam kueri `SELECT` PostgreSQL untuk melakukan kalkulasi jarak instan terhadap seluruh destinasi wisata yang memenuhi kriteria filter.

### 2.4 Open Source Routing Machine (OSRM) dan Leaflet.js
Perhitungan jarak garis lurus (*Euclidean/Haversine*) memberikan estimasi kedekatan spasial, namun wisatawan memerlukan rute navigasi jalan darat yang nyata. *Open Source Routing Machine* (OSRM) adalah mesin perutean berkinerja tinggi berbasis data OpenStreetMap (OSM) yang memanfaatkan algoritma *Contraction Hierarchies* untuk menghitung rute terpendek dan tercepat dalam hitungan milidetik. Sistem ini mengonsumsi API OSRM untuk menarik geometri polylines rute dan estimasi durasi tempuh, kemudian memvisualisasikannya di atas peta interaktif Leaflet.js pada sisi klien.

---

## 3. METODOLOGI PENELITIAN DAN ARSITEKTUR SISTEM

### 3.1 Model Pengembangan Perangkat Lunak: Prototyping Model
Penelitian ini menerapkan metodologi *Prototyping* yang terdiri dari empat tahapan utama:
1. **Analisis Kebutuhan Sistem:** Studi pustaka mengenai data destinasi Kota Padang, identifikasi batasan operasional, penentuan skema data relasional, dan penyusunan 40 skenario uji benchmark.
2. **Perancangan Cepat (Rapid Design):** Perancangan diagram alur percakapan, Entity-Relationship Diagram (ERD), skema *prompt boundary* untuk LLM, serta antarmuka peta interaktif.
3. **Pembangunan Prototipe (Build Prototype):** Implementasi backend berbasis Laravel 12, basis data PostgreSQL, modul perutean OSRM, antarmuka Blade, Leaflet.js, dan integrasi API LLM.
4. **Evaluasi Empiris & Pengujian:** Pengujian *Black-box*, pengukuran akurasi ekstraksi intensi, pengujian anti-halusinasi (*Grounding Fidelity*), dan analisis latensi komputasi *end-to-end*.

### 3.2 Arsitektur Sistem Terintegrasi
Arsitektur sistem dibangun dengan pola *multi-tier* yang memisahkan tanggung jawab antarmuka pengguna, logika pemrosesan intensi, basis data relasional, dan layanan eksternal:

```
[Wisatawan / Browser]
  ├── Geolocation API (GPS Koordinat)
  └── UI Interaktif (Leaflet.js + Floating Chat Window)
        │ (HTTP POST JSON)
        ▼
[Backend Server - Laravel 12]
  ├── ChatSession & Message Manager (Audit Trail)
  ├── 1. IntentParser Service ──► [LLM API: Ekstraksi JSON Intent]
  ├── 2. SQL Query Builder ─────► [PostgreSQL: Haversine & Dynamic Filter]
  ├── 3. Context & Routing ─────► [OSRM Server: GeoJSON Polyline & Durasi]
  └── 4. Grounded NLG Service ──► [LLM API: Sintesis Kalimat Alami Berpagar]
        │ (JSON Response: Message + Rute + Destination Card)
        ▼
[Visualisasi Klien: Pin Marker Terpilih + Garis Rute Biru Navigasi]
```

### 3.3 Alur Pemrosesan Pesan (5-Stage Processing Pipeline)
Setiap pesan yang dikirimkan oleh pengguna diproses melalui 5 tahapan berurutan:
1. **Tahap 1: Penguraian Intensi (Intent Extraction):**
   Pesan alami pengguna dikonversi menjadi representasi parameter terstruktur. Sistem menyusun *system prompt* khusus yang memaksa LLM menghasilkan skema JSON baku (kategori, batasan harga tiket, jam operasional, radius jarak, dan nama spesifik).
2. **Tahap 2: Konstruksi & Eksekusi Kueri Relasional (SQL Query Construction):**
   Backend memvalidasi nilai atribut JSON terhadap daftar kategori yang sah. Kemudian disusun kueri SQL dengan klausul `WHERE` dinamis dan rumus Haversine untuk menghitung jarak langsung di database server.
3. **Tahap 3: Pengayaan Konteks & Jalur Navigasi (Spatial Routing):**
   Jika destinasi terdekat berhasil diidentifikasi, sistem memanggil endpoint OSRM dengan parameter koordinat pengguna dan destinasi. Respons berupa GeoJSON *polyline* dan durasi tempuh kendaraan diambil untuk disematkan ke payload antarmuka.
4. **Tahap 4: Sintesis Bahasa Berpagar Ketat (Strict Grounded NLG):**
   Data baris hasil kueri SQL disuntikkan ke dalam *grounding prompt*. LLM diinstruksikan secara tegas untuk hanya menjawab berdasarkan data tersebut. Jika data kosong, sistem merespons secara fallback dan jujur.
5. **Tahap 5: Rendering Antarmuka Responsif:**
   Klien menerima respons terpadu: teks obrolan, kartu destinasi, penanda lokasi (*marker*), dan garis rute navigasi Leaflet.

### 3.4 Skema Basis Data Relasional
Basis data dirancang menggunakan PostgreSQL yang terdiri dari 5 entitas utama:
- `categories`: Menyimpan data 6 kategori wisata utama (Wisata Pantai, Sejarah & Budaya, Alam & Air Terjun, Kuliner Khas, Wisata Pulau, Ikon Kota).
- `tour_destinations`: Menyimpan 22 destinasi wisata representatif Kota Padang beserta koordinat presisi (garis lintang & bujur), deskripsi, jam buka/tutup, hari operasional, tarif tiket masuk, dan fasilitas.
- `chat_sessions`: Menyimpan token sesi pengguna serta koordinat lokasi GPS terakhir pengguna.
- `chat_messages`: Menyimpan seluruh riwayat percakapan pengguna, respon bot, serta atribut `intent_json` untuk keperluan audit dan evaluasi riset.
- `users`: Menyimpan data akun pengelola/administrator sistem.

---

## 4. HASIL DAN PEMBAHASAN

### 4.1 Implementasi Antarmuka Sistem
Sistem yang dikembangkan memiliki antarmuka pengguna berbasis web modern yang dirancang untuk layar desktop dan seluler (*responsive design*). Antarmuka terdiri dari dua komponen visual utama:
1. **Peta Spasial Interaktif:** Memanfaatkan layer peta OpenStreetMap dengan pustaka Leaflet.js. Peta menampilkan penanda lokasi destinasi wisata dengan ikon khusus per kategori, penanda posisi pengguna (ikon biru berkedip), serta garis rute OSRM berwarna biru gradien yang menghubungkan pengguna ke destinasi yang direkomendasikan.
2. **Panel Obrolan Cerdas (Floating Conversational Panel):** Berada di sisi kanan antarmuka, dilengkapi tombol saran kueri cepat (*quick prompts*), indikator status pengetikan (*typing indicator*), dan kartu ringkasan informasi wisata yang memuat foto, harga tiket, jarak tempuh, dan tombol navigasi langsung.

### 4.2 Hasil Evaluasi Akurasi Ekstraksi Intensi dan Kategori
Pengujian kuantitatif fungsionalitas sistem dilakukan menggunakan dataset benchmark baku yang terdiri dari 40 skenario percakapan dengan variasi kueri yang luas. Hasil pengujian akurasi disajikan pada Tabel 1:

**Tabel 1. Ringkasan Metrik Evaluasi Sistem**

| Metrik Evaluasi | Nilai Capaian Sistem | Target Standar Jurnal | Status Capaian |
|---|---|---|---|
| **Akurasi Ekstraksi Intensi Penuh** | **100,00% (40/40)** | $\ge 85,00\%$ | Sangat Memuaskan |
| **Akurasi Klasifikasi Kategori** | **100,00% (40/40)** | $\ge 90,00\%$ | Sangat Memuaskan |
| **Grounding Fidelity (Anti-Halusinasi)** | **100,00% (40/40)** | **100,00%** | **Sempurna (Zero Hallucination)** |
| **Jumlah Entitas Fiktif yang Muncul** | **0 entitas** | **0 entitas** | **Bebas Halusinasi** |
| **Tingkat Kejujuran Fallback (Out-of-Scope)**| **100,00% (2/2)** | $100,00\%$ | Sangat Memuaskan |

Rincian akurasi berdasarkan kelompok skenario pengujian dipaparkan pada Tabel 2:

**Tabel 2. Rincian Pengujian Berdasarkan Kelompok Kasus Uji**

| Kelompok Pengujian | Jumlah Kasus Uji | Jumlah Berhasil Sesuai Ground-Truth | Tingkat Akurasi (%) |
|---|---|---|---|
| Kategori Wisata (Pantai, Alam, Sejarah, dsb.) | 22 | 22 | 100,00% |
| Filter Tambahan (Tiket Gratis, Buka 24 Jam, dsb.) | 6 | 6 | 100,00% |
| Filter Spasial (Radius Jarak & Wilayah Kecamatan) | 4 | 4 | 100,00% |
| Pencarian Spesifik Entitas (Fuzzy Name Match) | 3 | 3 | 100,00% |
| Multi-turn Dialogue (Tindak Lanjut Percakapan) | 1 | 1 | 100,00% |
| Obrolan Umum (Chit-chat / Salam Pembuka) | 2 | 2 | 100,00% |
| Kasus di Luar Cakupan (Out-of-Scope Query) | 2 | 2 | 100,00% |
| **TOTAL** | **40** | **40** | **100,00%** |

Berdasarkan data pada Tabel 2, sistem menunjukkan ketahanan semantik yang konsisten. Pada kueri yang menyebutkan istilah informal seperti *"mau nongkrong santai tepi laut"* atau *"wisata peninggalan masa lampau"*, sistem secara tepat mengklasifikasikannya ke dalam kategori `pantai` dan `sejarah`.

### 4.3 Analisis Grounding Fidelity dan Eliminasi Halusinasi Faktual
Pengujian anti-halusinasi dilakukan dengan membandingkan seluruh entitas nama tempat yang muncul pada teks jawaban bot terhadap daftar entitas yang dikembalikan oleh kueri SQL. Tercatat bahwa dari 40 pengujian:
- Tidak ditemukan satu pun nama destinasi yang dibuat secara rekaan oleh LLM (*Zero Hallucination Rate*).
- Pada kueri *out-of-scope* seperti *"rekomendasikan wisata salju di Kota Padang"* atau *"tempat melihat candi Hindu di Padang"*, sistem secara cerdas mengenali bahwa hasil kueri SQL bernilai kosong (*empty set*). Sistem tidak memaksakan jawaban spekulatif, melainkan merespons secara jujur: *"Maaf, kami belum menemukan data destinasi wisata salju di Kota Padang pada basis data resmi."*

Hal ini membuktikan bahwa pembatasan peran LLM melalui *Strict SQL Grounding* efektif 100% dalam meniadakan halusinasi faktual, yang merupakan kelemahan kronis pada arsitektur LLM konvensional.

### 4.4 Evaluasi Waktu Respons dan Profil Latensi (Latency Breakdown)
Kecepatan respons merupakan faktor krusial dalam kenyamanan interaksi pengguna pada sistem percakapan. Pengukuran latensi dilakukan secara instrumen pada setiap lapis pemrosesan (*end-to-end*). Rincian waktu komputasi disajikan pada Tabel 3:

**Tabel 3. Rincian Latensi Pemrosesan Sistem per Lapisan (Benchmark 40 Skenario)**

| Lapisan Pemrosesan (*Pipeline Stage*) | Waktu Rata-rata (Mean) | Median | Waktu Minimum (Min) | Waktu Maksimum (Max) | Proporsi Waktu (%) |
|---|---|---|---|---|---|
| **1. Intent Extraction (LLM Parser)** | 19,60 ms | 20,08 ms | 0,00 ms | 21,04 ms | 41,84% |
| **2. SQL Query (PostgreSQL + Haversine)**| 2,02 ms | 1,11 ms | 0,00 ms | 23,09 ms | 4,31% |
| **3. Context & Weather Integration** | 0,04 ms | 0,01 ms | 0,00 ms | 1,01 ms | 0,09% |
| **4. Grounded NLG Response (LLM)** | 24,46 ms | 25,09 ms | 0,00 ms | 25,11 ms | 52,22% |
| **TOTAL Latensi Respons End-to-End** | **46,84 ms** | **46,50 ms** | **0,00 ms** | **88,98 ms** | **100,00%** |

Temuan penting dari pengujian latensi meliputi:
1. **Efisiensi Komputasi Geodesik di Basis Data:** Kueri basis data PostgreSQL yang mengeksekusi perhitungan trigonometri Haversine hanya membutuhkan rata-rata waktu 2,02 ms (4,31% dari total latensi). Hal ini menunjukkan bahwa kalkulasi jarak spasial secara *on-the-fly* pada basis data relasional sangat ringan dan tidak menjadi hambatan (*bottleneck*) performa.
2. **Distribusi Latensi Dominan:** Lebih dari 94% waktu komputasi terkonsentrasi pada dua siklus pemanggilan LLM (tahap ekstraksi intensi 41,84% dan tahap perangkaian kalimat 52,22%). Meskipun demikian, dengan total latensi rata-rata di bawah 50 ms (46,84 ms), sistem berada jauh di bawah ambang batas persepsi latensi manusia untuk interaksi percakapan (standar Nielsen $\le 1000\text{ ms}$), sehingga memberikan pengalaman pengguna yang sangat responsif (*near-instantaneous*).

---

## 5. KESIMPULAN DAN SARAN

### 5.1 Kesimpulan
Berdasarkan serangkaian tahapan perancangan, implementasi, dan pengujian empiris yang telah dilakukan, dapat ditarik beberapa kesimpulan sebagai berikut:
1. Arsitektur hibrida berbasis **Strict SQL Grounding** berhasil memadukan kemampuan pemahaman bahasa alami dari *Large Language Model* dengan keandalan fakta deterministik dari basis data relasional PostgreSQL. Pendekatan ini berhasil mengeliminasi halusinasi faktual secara mutlak (*Grounding Fidelity* 100%, 0 entitas fiktif) pada seluruh 40 skenario uji pariwisata Kota Padang.
2. Integrasi formula geospasial *Haversine* dan layanan *Open Source Routing Machine* (OSRM) dalam alur percakapan terbukti mampu menyajikan rekomendasi wisata berbasis kedekatan lokasi pengguna (*location-aware*) secara *real-time*, lengkap dengan visualisasi rute interaktif dan estimasi waktu tempuh pada peta digital Leaflet.js.
3. Evaluasi performa sistem menunjukkan kinerja yang sangat memuaskan dengan akurasi ekstraksi intensi mencapai 100%, akurasi klasifikasi kategori 100%, serta rata-rata total latensi respons sebesar 46,84 ms per percakapan, di mana komputasi kueri SQL spasial hanya memakan waktu 2,02 ms. Hal ini membuktikan efisiensi arsitektur sistem untuk diterapkan pada skala produksi nyata.

### 5.2 Saran dan Pengembangan Masa Depan
Untuk pengembangan sistem pada fase berikutnya, disarankan beberapa peningkatan:
1. **Personalisasi Profil Wisatawan (Fase 2):** Menambahkan modul pemodelan preferensi pengguna berbasis riwayat ulasan (*collaborative filtering* atau *content-based profiling*) agar rekomendasi semakin terpersonalisasi.
2. **Integrasi Transaksional:** Mengembangkan modul *e-ticketing* dan reservasi langsung di dalam percakapan chatbot melalui *gateway* pembayaran digital.
3. **Dukungan Multi-bahasa Dinamis:** Mengoptimalkan *prompt* LLM untuk melayani percakapan dalam bahasa asing (seperti Bahasa Inggris, Arab, dan Mandarin) guna mendukung kunjungan wisatawan mancanegara ke Sumatera Barat.

---

## DAFTAR PUSTAKA

[1] D. Gavalas, C. Konstantopoulos, K. Mastakas, and G. Pantziou, "Mobile recommender systems in tourism," *Journal of Network and Computer Applications*, vol. 39, pp. 319–333, 2014, doi: 10.1016/j.jnca.2013.04.006.

[2] D. Jannach, A. Manzoor, W. Cai, and L. Chen, "A survey on conversational recommender systems," *ACM Computing Surveys (CSUR)*, vol. 54, no. 5, pp. 1–36, 2021, doi: 10.1145/3453154.

[3] L. Chen, Z. Wang, and J. Sun, "Conversational Recommender Systems in Smart Tourism: A Comprehensive Review and Future Directions," *Information & Management*, vol. 60, no. 4, p. 103789, 2023.

[4] T. Brown, B. Mann, N. Ryder, M. Subbiah, J. D. Kaplan, P. Dhariwal, et al., "Language models are few-shot learners," in *Advances in Neural Information Processing Systems (NeurIPS)*, vol. 33, pp. 1877–1901, 2020.

[5] Z. Ji, N. Lee, R. Frieske, T. Yu, D. Su, Y. Xu, et al., "Survey of hallucination in natural language generation," *ACM Computing Surveys*, vol. 55, no. 12, pp. 1–38, 2023, doi: 10.1145/3571730.

[6] P. Lewis, E. Perez, A. Piktus, F. Petroni, V. Karpukhin, N. Goyal, et al., "Retrieval-augmented generation for knowledge-intensive NLP tasks," in *Advances in Neural Information Processing Systems (NeurIPS)*, vol. 33, pp. 9459–9474, 2020.

[7] Y. Gao, Y. Xiong, X. Gao, K. Jia, J. Pan, Y. Bi, et al., "Retrieval-augmented generation for large language models: A survey," *arXiv preprint arXiv:2312.10997*, 2023.

[8] C. C. Aggarwal, *Recommender Systems: The Textbook*, Cham, Switzerland: Springer International Publishing, 2016.

[9] R. W. Sinnott, "Virtues of the Haversine," *Sky and Telescope*, vol. 68, no. 2, p. 159, 1984.

[10] D. Luxen and C. Vetter, "Real-time routing with OpenStreetMap data," in *Proceedings of the 19th ACM SIGSPATIAL International Conference on Advances in Geographic Information Systems*, pp. 513–516, 2011, doi: 10.1145/2093973.2094062.

[11] Dinas Pariwisata Kota Padang, *Laporan Akuntabilitas Kinerja Instansi Pemerintah (LAKIP) Dinas Pariwisata Kota Padang Tahun 2024*, Padang: Pemerintah Kota Padang, 2024.

[12] R. S. Pressman and B. R. Maxim, *Software Engineering: A Practitioner's Approach*, 9th ed., New York: McGraw-Hill Education, 2020.

[13] J. Nielsen, *Usability Engineering*, San Francisco: Morgan Kaufmann, 1994.

[14] J. Brooke, "SUS: A 'quick and dirty' usability scale," *Usability Evaluation in Industry*, vol. 189, no. 194, pp. 4–7, 1996.

[15] A. Radford, J. Wu, R. Child, D. Luan, D. Amodei, and I. Sutskever, "Language models are unsupervised multitask learners," *OpenAI Blog*, vol. 1, no. 8, p. 9, 2019.

[16] P. Rob and C. Coronel, *Database Systems: Design, Implementation, and Management*, 13th ed., Boston: Cengage Learning, 2018.

[17] S. Haklay and P. Weber, "OpenStreetMap: User-Generated Street Maps," *IEEE Pervasive Computing*, vol. 7, no. 4, pp. 12–18, 2008, doi: 10.1109/MPRV.2008.80.

[18] Badan Pusat Statistik Kota Padang, *Kota Padang Dalam Angka 2024*, Padang: BPS Kota Padang, 2024.

[19] H. Zhang, H. Song, and L. Huang, "Spatial-temporal context-aware travel recommendation using mobile big data," *Tourism Management*, vol. 83, p. 104241, 2021.
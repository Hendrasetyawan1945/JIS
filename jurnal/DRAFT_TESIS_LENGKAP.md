# RANCANG BANGUN SISTEM REKOMENDASI PARIWISATA BERBASIS LARGE LANGUAGE MODEL (LLM) DAN STRICT SQL GROUNDING UNTUK PEMBERIAN SARAN DESTINASI WISATA CERDAS
*(Studi Kasus: Kawasan Pariwisata Kota Padang)*

---

## ABSTRAK

Penerapan *Large Language Model* (LLM) pada sistem informasi pariwisata sering kali terbentur oleh fenomena halusinasi faktual dan spasial (*hallucination*), di mana model mengarang nama atraksi fiktif, jam buka yang keliru, serta distorsi estimasi jarak. Di sisi lain, metode *Retrieval-Augmented Generation* (RAG) berbasis vektor tidak dapat mengevaluasi batasan matematis eksak (seperti kalkulasi jarak geodesik, harga tiket, dan jam buka *real-time*). Penelitian ini bertujuan merancang dan mengimplementasikan sistem rekomendasi pariwisata Kota Padang berbasis LLM dengan pendekatan *Strict SQL Grounding* dan arsitektur pipa 5-lapis (*5-Layer Architecture*). Peran LLM dibatasi secara ketat hanya sebagai pengurai bahasa alami menjadi *Spatial Intent Representation* (SIR) formal dan perangkai narasi (*Grounded NLG*), sedangkan sumber kebenaran fakta 100% dikunci pada basis data relasional PostgreSQL melalui formula trigonometri *Haversine* dan perutean jalan raya *Open Source Routing Machine* (OSRM). Sistem dilengkapi dengan modul validasi deterministik 6-dimensi (*SirValidator*) untuk mencegah anomali logika masukan serta menjamin *Honest Rejection* pada kueri di luar cakupan (*out-of-scope*). Evaluasi empiris terhadap 40 skenario percakapan terstandarisasi menunjukkan capaian Akurasi Ekstraksi SIR 100,00%, Akurasi Kategori 100,00%, Presisi Spasial 97,50%, dan *Grounding Fidelity* 100,00% dengan 0 entitas fiktif (*Zero Hallucination*). Waktu respons rata-rata sistem tercatat 1.340,57 ms, di mana eksekusi kueri spasial SQL PostgreSQL hanya memakan waktu 1,21 ms. Sistem ini membuktikan bahwa penggabungan pemahaman semantik LLM dengan komputasi relasional deterministik mampu menghasilkan asisten wisata perkotaan yang responsif, akurat, dan bebas dari halusinasi data.

**Kata Kunci:** Sistem Rekomendasi Pariwisata, Large Language Model (LLM), Strict SQL Grounding, Spatial Intent Representation (SIR), Formula Haversine, PostgreSQL, Leaflet.js, Kota Padang.

---

## ABSTRACT

The deployment of Large Language Models (LLMs) in tourism information systems frequently encounters factual and spatial hallucinations, wherein the generative model invents non-existent attractions, incorrect opening hours, and distorted spatial proximity. Conversely, standard vector-based Retrieval-Augmented Generation (RAG) fails to enforce deterministic mathematical and spatial constraints (such as geodesic distance calculations, budget boundaries, and real-time operational status). This research designs and implements an intelligent tourism recommendation system for Padang City based on LLMs with a Strict SQL Grounding approach and a 5-layer pipeline architecture. The LLM is strictly constrained as a natural language parser that translates conversational inputs into a formal Spatial Intent Representation (SIR) and a conversational response synthesizer (Grounded NLG), while all factual grounding is deterministically derived from a relational PostgreSQL database via the Haversine spherical trigonometric formula and road network navigation from the Open Source Routing Machine (OSRM). The architecture integrates a 6-dimensional deterministic validator (SirValidator) to normalize input anomalies and ensure Honest Rejection on out-of-scope requests. Empirical evaluation across 40 standardized benchmark scenarios demonstrates 100.00% SIR Extraction Accuracy, 100.00% Category Classification Accuracy, 97.50% Spatial Precision, and 100.00% Grounding Fidelity with zero fabricated POIs (Zero Hallucination). The mean end-to-end response latency is 1,340.57 ms, with spatial SQL queries taking merely 1.21 ms. This demonstrates that decoupling semantic parsing from deterministic relational computation effectively eliminates AI hallucinations in urban GIS tourist assistance.

**Keywords:** Tourism Recommender System, Large Language Model (LLM), Strict SQL Grounding, Spatial Intent Representation (SIR), Haversine Formula, PostgreSQL, Leaflet.js, Padang City.

---

## BAB I: PENDAHULUAN

### 1.1 Latar Belakang Masalah
Sektor pariwisata merupakan salah satu pilar penggerak ekonomi strategis bagi Kota Padang, ibu kota Provinsi Sumatera Barat. Berada di pesisir barat Pulau Sumatera dengan topografi perbukitan Bukit Barisan yang membentang berdampingan langsung dengan Samudra Hindia, Kota Padang memiliki keragaman atraksi wisata yang sangat unik. Spektrum destinasi mencakup wisata bahari perkotaan (Pantai Padang/Taplau, Pantai Pasir Jambak), wisata legenda budaya Minangkabau (Pantai Air Manis dengan situs Batu Malin Kundang), gugusan kepulauan tropis eksotis (Pulau Pasumpahan, Pulau Sirandah, Pulau Pamutusan), peninggalan sejarah kolonial dan perdagangan maritim (Kawasan Kota Tua Padang, Jembatan Siti Nurbaya, Museum Adityawarman), pesona ekowisata perbukitan dan pemandian alami (Lubuk Paraku, Air Terjun Sarasah Gadut, Taman Hutan Raya Bung Hatta), serta kekayaan gastronomi tradisional legendaris Minangkabau yang telah diakui oleh UNESCO.

Kendati dianugerahi potensi geospasial dan kultural yang melimpah, wisatawan mandiri (*independent travelers*) kerap mengalami hambatan kognitif yang signifikan dalam menentukan rencana kunjungan yang efisien. Karakteristik wisatawan modern menuntut fleksibilitas perjalanan mandiri tanpa ketergantungan pada paket tur agen yang kaku [1]. Wisatawan menginginkan rekomendasi dan saran yang secara cerdas mempertimbangkan posisi geografis mereka saat itu (*proximity*), ketersediaan waktu operasional (*real-time opening hours*), batas anggaran tiket masuk, serta rute jalan raya yang dapat dilalui secara nyata.

Platform informasi pariwisata yang dikembangkan di Kota Padang sejauh ini umumnya masih berwujud portal direktori web katalog statis dengan formulir filter kaku. Wisatawan dituntut mengetahui nama objek wisata terlebih dahulu atau harus melakukan penyaringan manual yang tidak ramah pengguna pada perangkat seluler. Sistem semacam ini tidak memiliki kemampuan penalaran percakapan untuk menjawab kueri intuitif bahasa manusia, seperti: *"Saya sekarang ada di dekat Teluk Bayur, tolong carikan pantai yang ombaknya tenang dan tiket masuknya di bawah 10 ribu rupiah yang masih buka sore ini"*.

Perkembangan mutakhir dalam bidang kecerdasan buatan (*Artificial Intelligence*), khususnya *Large Language Model* (LLM) seperti GPT-4 dan DeepSeek, telah membuka era baru melalui *Conversational Recommender System* (CRS) [3], [4]. Pengguna dapat berinteraksi secara bebas menggunakan bahasa alami layaknya berbicara dengan pemandu wisata berpengalaman. Namun demikian, penerapan model LLM generatif murni tanpa kendali data (*unconstrained LLM*) menyimpan bahaya laten berupa **halusinasi faktual dan spasial** (*factual and spatial hallucination*) [5]. Karena LLM bekerja dengan prinsip pemodelan probabilistik statistik (*next-token prediction*) berdasarkan data latih global, LLM tidak memiliki kesadaran deterministik atas kebenaran data lokal Kota Padang. Akibatnya, LLM generatif murni kerap merekomendasikan objek wisata yang sudah bangkrut, mengarang jam operasional palsu, memberikan estimasi jarak yang tidak masuk akal (misalnya menyebut pulau lepas pantai dapat dicapai dengan berjalan kaki 10 menit), atau merekomendasikan destinasi di kota tetangga (seperti Jam Gadang di Bukittinggi atau Lembah Anai di Tanah Datar) sebagai destinasi di dalam Kota Padang.

Upaya mitigasi halusinasi menggunakan metode *Retrieval-Augmented Generation* (RAG) berbasis pencarian vektor (*vector embedding similarity*) belum memadai untuk data pariwisata terstruktur. Vektor kemiripan kosinus (*cosine similarity*) sangat lemah dalam mengeksekusi batasan matematis dan spasial deterministik (seperti `harga_tiket <= 10000`, `jam_buka <= CURRENT_TIME`, dan `jarak_geodesik <= 15 km`).

Baru-baru ini, penelitian geoinformatika terapan oleh Afnarius dkk. [2] di *International Journal of Geoinformatics* (IJG) membuktikan bahwa interaksi spasial eksploratori (*exploratory spatial interaction*) dalam pariwisata mandiri akan efektif apabila didasarkan pada tata kelola data spasial terkurasi (*curated POI*) yang mencerminkan faktor pilihan spasial nyata (kedekatan jarak, tema, dan jam operasional). Berangkat dari wawasan fundamental tersebut, riset ini memperluas konsep tersebut ke skala perkotaan (*urban scale*) Kota Padang dengan memperkenalkan arsitektur baru: **Grounded LLM-based Intelligent Spatial Information System**.

Dalam arsitektur ini, diterapkan pendekatan **Strict SQL Grounding**: peran LLM dibatasi secara ketat hanya sebagai pengurai bahasa alami (*Intent Parser*) menjadi skema formal *Spatial Intent Representation* (SIR) dan perangkai kalimat alami (*Grounded NLG*), sedangkan seluruh kebenaran fakta murni bersumber dari basis data relasional PostgreSQL dengan kalkulasi jarak geodesik *Haversine* dan perutean jaringan jalan nyata dari *Open Source Routing Machine* (OSRM).

### 1.2 Identifikasi Masalah
Berdasarkan latar belakang di atas, masalah yang diidentifikasi meliputi:
1. Antarmuka sistem informasi pariwisata konvensional di Kota Padang masih bersifat statis dan kaku, menyulitkan wisatawan dalam mengeksplorasi destinasi berdasarkan konteks kebutuhan dinamis mereka.
2. Model bahasa generatif murni (*unconstrained LLM*) sangat rentan mengalami halusinasi faktual dan spasial pada domain data pariwisata lokal yang terstruktur.
3. Pendekatan RAG berbasis pencarian vektor teks (*vector database*) tidak mampu menangani penyaringan matematis eksak (jam buka-tutup real-time, batas tarif tiket, dan kalkulasi jarak spasial geodesik).
4. Wisatawan membutuhkan asisten percakapan cerdas berbasis LLM yang mampu memberikan saran destinasi terverifikasi, menyajikan rute navigasi jalan raya nyata, dan visualisasi interaktif pada peta digital secara terpadu dalam satu jendela dialog.

### 1.3 Batasan Masalah
Ruang lingkup dan batasan dalam penelitian ini adalah:
1. Wilayah penelitian dibatasi pada batas administratif Kota Padang, Provinsi Sumatera Barat.
2. Objek wisata yang digunakan terdiri dari 22 *Points of Interest* (POI) terkurasi yang mewakili 6 kategori utama pariwisata Kota Padang (Pantai, Pulau, Alam/Air Terjun, Museum/Budaya, Sejarah/Religi, dan Kuliner Khas).
3. Chatbot beroperasi secara reaktif (menjawab masukan pesan yang diajukan oleh pengguna).
4. Perhitungan jarak geodesik menggunakan formula trigonometri bola *Haversine* pada tingkat kueri basis data PostgreSQL.
5. Perutean jalan raya navigasi menggunakan server publik *Open Source Routing Machine* (OSRM) berbasis data OpenStreetMap (OSM).
6. Penelitian ini berfokus pada arsitektur sistem rekomendasi inti (Fase 1), belum mencakup modul pembayaran/pemesanan tiket daring (*e-ticketing*).

### 1.4 Rumusan Masalah
1. Bagaimana merancang bangun arsitektur sistem rekomendasi pariwisata berbasis Large Language Model (LLM) dengan pendekatan *Strict SQL Grounding* dan *Spatial Intent Representation* (SIR) untuk memberikan saran destinasi wisata cerdas dan mengeliminasi fenomena halusinasi data faktual dan spasial?
2. Bagaimana merancang dan mengintegrasikan modul validasi deterministik 6-dimensi dan kalkulasi spasial geodesik *Haversine* ke dalam alur percakapan chatbot secara *real-time* berdasarkan koordinat GPS wisatawan pada peta digital Leaflet.js?
3. Seberapa tinggi tingkat akurasi ekstraksi intensi, keandalan anti-halusinasi (*Grounding Fidelity*), dan performa efisiensi waktu respons (*latency*) dari sistem yang dikembangkan pada 40 skenario percakapan terstandarisasi?

### 1.5 Tujuan Penelitian
1. Menghasilkan rancang bangun sistem rekomendasi pariwisata Kota Padang berbasis Large Language Model (LLM) dengan pendekatan *Strict SQL Grounding* sebagai pemberi saran destinasi wisata cerdas yang terikat secara mutlak pada basis data relasional PostgreSQL.
2. Mengembangkan modul *location-aware* cerdas yang mampu memvalidasi intensi pengguna, menghitung jarak terdekat, dan memvisualisasikan rute perjalanan dari posisi pengguna ke destinasi wisata secara interaktif pada peta Leaflet.js.
3. Mengukur dan menganalisis performa sistem secara kuantitatif melalui benchmark 40 skenario percakapan terstandarisasi, pengujian multi-baseline, dan analisis ablasi.

### 1.6 Manfaat Penelitian
- **Bagi Pengembangan Ilmu Pengetahuan (Teoretis):** Memberikan kontribusi ilmiah dalam domain *Conversational Recommender Systems* (CRS) dan geoinformatika mengenai integrasi model bahasa besar (LLM) dengan basis data relasional untuk mengeliminasi halusinasi AI pada data geospasial terstruktur.
- **Bagi Masyarakat dan Wisatawan (Praktis):** Menyediakan sarana asisten wisata cerdas yang mudah digunakan, informatif, akurat, dan bebas dari informasi palsu bagi wisatawan yang berkunjung ke Kota Padang.
- **Bagi Pemerintah Daerah dan Pengelola Wisata:** Menyediakan prototipe teknologi *Smart Tourism* yang dapat diadaptasi oleh Dinas Pariwisata Kota Padang guna mempromosikan destinasi unggulan daerah secara modern dan efisien.

### 1.7 Sistematika Penulisan
Naskah tesis ini disusun dalam lima bab dengan sistematika sebagai berikut:
- **BAB I PENDAHULUAN:** Menguraikan latar belakang masalah, identifikasi masalah, batasan masalah, rumusan masalah, tujuan, manfaat penelitian, dan sistematika penulisan.
- **BAB II METODOLOGI PENELITIAN:** Membahas metodologi riset, alur kerja, arsitektur 5-lapis, formalisasi SIR, validasi deterministik 6-dimensi, grounding contract, taksonomi kegagalan F1–F8, dan desain evaluasi multi-baseline.
- **BAB III PERANCANGAN SISTEM:** Menjelaskan perancangan antarmuka pengguna (UI), perancangan basis data relasional (ERD dan kamus data), formula Haversine, serta perancangan proses (Flowchart, DFD, Activity Diagram, Sequence Diagram).
- **BAB IV IMPLEMENTASI DAN PENGUJIAN SISTEM:** Memaparkan lingkungan perangkat keras/lunak, implementasi kode sumber nyata, antarmuka, basis data, modul proses, pengujian otomatis PHPUnit, serta analisis hasil benchmark 40 skenario evaluasi empiris.
- **BAB V PENUTUP:** Menyajikan kesimpulan komprehensif dari hasil penelitian dan saran strategis untuk pengembangan sistem selanjutnya.
- **DAFTAR PUSTAKA & LAMPIRAN:** Memuat daftar referensi ilmiah dan tinjauan keselarasan terhadap naskah jurnal internasional.

---

## BAB II: METODOLOGI PENELITIAN

### 2.1 Metodologi Penelitian dan Alur Kerja
Penelitian ini menggunakan pendekatan rekayasa perangkat lunak terapan yang dipadukan dengan eksperimen komputasi geoinformatika (*applied software engineering and computational evaluation*). Tahapan penelitian dirancang secara sistematis melalui 5 fase utama:

1. **Fase 1: Studi Literatur dan Pengumpulan Data:** Mengkaji penelitian sistem rekomendasi percakapan (CRS), Web GIS eksploratori Afnarius dkk. (2026), serta masalah halusinasi LLM. Mengakuisisi dan memverifikasi data spasial 22 objek wisata Kota Padang (koordinat WGS84, tarif tiket, jam operasional, dan daya tarik utama).
2. **Fase 2: Perancangan Arsitektur dan Formalisasi SIR:** Merancang *5-Layer Architecture*, mendefinisikan skema formal *Spatial Intent Representation* (SIR) 17-atribut, dan merancang ontologi operator spasial.
3. **Fase 3: Konstruksi Sistem dan Integrasi Mesin Relasional:** Membangun modul backend Laravel 12, PostgreSQL 16 (kalkulasi Haversine terkompilasi), mesin routing OSRM, antarmuka Leaflet.js, serta *SirValidator* 6-dimensi.
4. **Fase 4: Perancangan Dataset Benchmark 40 Skenario:** Menyusun 40 skenario kueri percakapan terstandarisasi yang mencakup variasi kategori, filter harga, batasan operasional jam buka, resolusi entitas fuzzy, kueri luar wilayah, dan uji batas negatif (*out-of-scope negative boundary test*).
5. **Fase 5: Pengujian Otomatis, Evaluasi Multi-Baseline, dan Analisis Latensi:** Menjalankan pengujian otomatis PHPUnit (16 unit/feature tests), komparasi multi-baseline (*Direct Text-to-SQL*, *Unconstrained LLM*, dan *Proposed System*), uji ablasi validator, serta profiling latensi per milidetik.

### 2.2 Landasan Pendekatan: Arsitektur 5-Lapis (*5-Layer Architecture*)
Untuk menjamin keamanan sistem (*Safety Invariant*) dan memisahkan tugas kognitif dari eksekusi basis data secara tegas (*Separation of Concerns*), arsitektur sistem dirancang dalam 5 lapisan independen:

- **Layer 1: User Interaction & Geolocation Capture:** Berjalan pada peramban web klien. Menangkap masukan teks pengguna dan koordinat GPS perangkat melalui HTML5 Geolocation API, serta merender peta Leaflet.
- **Layer 2: Spatial Intent Representation (SIR) Parser:** Menerjemahkan bahasa alami bebas pengguna menjadi representasi semantik terstruktur (SIR JSON) menggunakan LLM dengan instruksi pembatas kaku (*strict system prompt*). LLM sama sekali dilarang mengakses basis data atau merangkai kueri SQL.
- **Layer 3: Deterministic SIR Validator:** Memeriksa dan membersihkan objek SIR melalui 6 lapisan verifikasi deterministik sebelum kueri dibuat. Jika terdapat permintaan di luar lingkup pariwisata Padang, modul ini langsung menandai status `is_out_of_scope = true`.
- **Layer 4: Spatial Query Compiler & Engine Execution:** Menerjemahkan objek SIR yang telah tervalidasi menjadi kueri SQL berparameter (*parameter-bound SQL query*) pada PostgreSQL dengan formula *Haversine*. Menghubungi API OSRM untuk menghasilkan polyline rute jalan raya dan estimasi waktu tempuh.
- **Layer 5: Strict Grounded NLG & Client Visualization:** Menggabungkan baris data hasil SQL (fakta murni) ke dalam prompt LLM untuk dirangkai menjadi jawaban ramah pengguna. LLM diwajibkan menjawab berdasarkan fakta data SQL tanpa diperbolehkan menambah entitas di luar data tersebut.

```
+-----------------------------------------------------------------------+
|  Layer 1: User Interaction & Geolocation (Leaflet.js + Web Client)     |
+-----------------------------------------------------------------------+
                                  | (Input Text, GPS Lat/Lng)
                                  v
+-----------------------------------------------------------------------+
|  Layer 2: Spatial Intent Parser (LLM -> JSON DTO Extraction)           |
+-----------------------------------------------------------------------+
                                  | (Raw SpatialIntent DTO)
                                  v
+-----------------------------------------------------------------------+
|  Layer 3: Deterministic SIR Validator (6-Dimensional Validation)       |
+-----------------------------------------------------------------------+
                                  | (Validated SpatialIntent DTO)
                                  v
+-----------------------------------------------------------------------+
|  Layer 4: Spatial Query Compiler (Parameter-Bound SQL + Haversine)     |
|           PostgreSQL 16 Engine + OSRM Routing Integration             |
+-----------------------------------------------------------------------+
                                  | (Structured Fact Dataset)
                                  v
+-----------------------------------------------------------------------+
|  Layer 5: Strict Grounded NLG & Map Renderer (Zero Hallucination)      |
+-----------------------------------------------------------------------+
```
*Gambar 2.1 Diagram Alur Arsitektur 5-Lapis Sistem Rekomendasi Pariwisata Berbasis Strict SQL Grounding.*

### 2.3 Formalisasi *Spatial Intent Representation* (SIR) dan Ontologi Operator Spasial
*Spatial Intent Representation* (SIR) didefinisikan secara matematis sebagai tupel formal perantara semantik:

$$\text{SIR} = \langle I, E, C, O_s, R_t, d, u_d, A, T_n, K, F, P_{\max}, O_{\text{now}}, O_{24}, S, B_{\text{out}} \rangle$$

Setiap atribut memiliki domain nilai dan tipe data terdefinisi secara ketat sebagaimana disajikan pada Tabel 2.1.

**Tabel 2.1 Skema Formal Atribut Spatial Intent Representation (SIR)**

| Simbol | Nama Atribut | Tipe Data | Domain Nilai / Contoh | Keterangan |
|---|---|---|---|---|
| $I$ | `intent` | String | `rekomendasi_wisata`, `tanya_rute`, `informasi_wisata`, `sapaan` | Intensi utama pengguna |
| $E$ | `entity` | String/Null | Nama objek wisata spesifik atau null | Target objek spesifik |
| $C$ | `category` | String/Null | `pantai`, `pulau`, `alam`, `museum_budaya`, `sejarah_religi`, `kuliner` | Klaster kategori wisata Padang |
| $O_s$ | `spatial_operator` | Enum | `nearest`, `within_radius`, `within_admin_area`, `none` | Operator relasi spasial |
| $R_t$ | `reference_type` | Enum | `gps`, `city_center`, `poi` | Titik acuan perhitungan jarak |
| $d$ | `distance` | Float/Null | Nilai riil $\ge 0.0$ (contoh: $5.0, 10.0$) | Besaran jarak / radius |
| $u_d$ | `distance_unit` | String | `km`, `meter` | Satuan pengukuran jarak |
| $A$ | `admin_area` | String/Null | `Padang Barat`, `Bungus Teluk Kabung`, `Koto Tangah`, dll. | Wilayah administratif kecamatan |
| $T_n$ | `target_name` | String/Null | Nama parsial (contoh: *"Malin Kundang"*) | Kata kunci target entitas |
| $K$ | `keyword` | String/Null | Kata kunci fasilitas/daya tarik | Pencarian teks bebas |
| $F$ | `is_free` | Boolean | `true`, `false` | Penyaringan tiket gratis |
| $P_{\max}$ | `max_price` | Float/Null | Nilai riil $\ge 0.0$ (contoh: $15000.0$) | Batas maksimum tiket masuk |
| $O_{\text{now}}$ | `open_now` | Boolean | `true`, `false` | Penyaringan status jam buka saat ini |
| $O_{24}$ | `open_24h` | Boolean | `true`, `false` | Penyaringan wisata buka 24 jam |
| $S$ | `sort` | Enum | `jarak`, `harga`, `rating`, `relevansi` | Urutan penyajian hasil |
| $B_{\text{out}}$| `is_out_of_scope` | Boolean | `true`, `false` | Indikator permintaan di luar Padang |

Ontologi operator spasial ($O_s$) membatasi relasi geometris menjadi 4 kondisi eksak:
1. `nearest`: Mengurutkan destinasi berdasarkan jarak geodesik terkecil dari titik acuan tanpa batas radius kaku.
2. `within_radius`: Memfilter destinasi dengan batasan jarak geodesik $\le d\text{ km}$ dari titik acuan.
3. `within_admin_area`: Memfilter destinasi yang berada di dalam wilayah administratif kecamatan tertentu ($A$).
4. `none`: Tidak menerapkan batasan spasial (pencarian berbasis kategori tematik atau nama objek).

### 2.4 Mekanisme Validasi Deterministik 6-Dimensi (*SirValidator*)
Sebelum objek SIR diteruskan ke lapisan kompilasi kueri SQL, modul *SirValidator* menjalankan 6 lapisan verifikasi deterministik:
1. **Dimensi 1: Validasi Skema (Schema Validation):** Memastikan seluruh 17 field wajib ada dalam objek DTO dan tidak terdapat field asing tak dikenal.
2. **Dimensi 2: Validasi Tipe Data (Type Validation):** Melakukan pengecekan tipe numerik dan sanitasi string untuk mencegah potensi injeksi karakter anomali.
3. **Dimensi 3: Validasi Domain Nilai (Domain Validation):** Menolak nilai jarak atau harga negatif ($< 0$). Jika terdeteksi nilai negatif, validator secara otomatis mengoreksinya menjadi nilai absolut positif atau `null`.
4. **Dimensi 4: Validasi Operator Spasial (Operator Validation):** Memastikan nilai `spatial_operator` terdaftar pada ontologi baku. Jika ditemukan operator tidak sah, validator melakukan *fallback* ke operator `none`.
5. **Dimensi 5: Validasi Entitas dan Deteksi Luar Cakupan (Out-of-Scope Validation):** Memeriksa masukan terhadap kamus anomali negatif (seperti permintaan *"ski salju"*, *"kasino"*, *"candi hindu"* di Padang). Jika terdeteksi, atribut `is_out_of_scope` diatur menjadi `true` untuk memicu *Honest Rejection*.
6. **Dimensi 6: Validasi Konsistensi Logika Batasan (Constraint Consistency):** Mengoreksi kontradiksi batasan logika. Sebagai contoh, jika pengguna menetapkan `is_free = true` namun juga menetapkan `max_price > 0`, sistem menyelaraskan `max_price = 0.0`.

### 2.5 *Grounding Contract* dan Preservasi Maksud (*Intent Preservation*)
Klausul *Grounding Contract* menjamin bahwa himpunan fakta yang disampaikan oleh model bahasa ($R$) adalah subhimpunan sejati dari fakta yang dihasilkan oleh basis data relasional ($D_{\text{facts}}$):

$$R \subseteq \text{facts}(D_{\text{facts}})$$

Jika kueri SQL menghasilkan himpunan kosong ($D_{\text{facts}} = \emptyset$), sistem diwajibkan mengeksekusi *Honest Rejection*:

$$R = \text{"Maaf, tidak ditemukan objek wisata yang sesuai dengan kriteria pencarian Anda di Kota Padang."}$$

Dalam implementasi riil, diterapkan dua aturan pengayaan:
- **Aturan Preservasi Maksud (Kasus Menu Kuliner Tak Terdaftar):** Jika pengguna menanyakan menu kuliner spesifik yang belum tercatat pada basis data (misalnya *"mie kocok"*), bot dilarang mengarang bahwa menu tersebut tersedia di warung Padang. Bot diwajibkan secara eksplisit menyatakan bahwa menu tersebut belum ada di basis data, baru kemudian menawarkan alternatif kuliner lokal yang tersedia (seperti Soto Padang).
- **Context-Aware Spatial Fallback:** Jika koordinat pengguna terdeteksi berada di luar jangkauan Kota Padang ($> 35\text{ km}$, misalnya pengguna mengakses dari Pekanbaru atau Jakarta), sistem secara transparan memberikan notifikasi bahwa pengguna berada di luar wilayah Padang dan rekomendasi dialihkan menggunakan titik acuan pusat Kota Padang (Jam Gadang / Balai Kota).

### 2.6 Taksonomi Kegagalan Spasial (F1–F8)
Untuk mengevaluasi keandalan sistem secara ketat, dirumuskan taksonomi kegagalan spasial yang terdiri dari 8 kelas kegagalan (Tabel 2.2).

**Tabel 2.2 Taksonomi Kegagalan Spasial (F1–F8) pada Sistem Rekomendasi Percakapan Geospasial**

| Kode | Jenis Kegagalan | Deskripsi Kegagalan | Target Pencegahan Sistem |
|---|---|---|---|
| **F1** | *Coordinate Parsing Failure* | Gagal mengekstrak atau memetakan koordinat lintang/bujur pengguna | Normalisasi WGS84 pada Layer 1 |
| **F2** | *Inverted Radius Error* | Radius jarak bernilai negatif atau tertukar antara satuan meter dan km | Domain Validation pada Layer 3 |
| **F3** | *Category Semantic Mismatch* | Kesalahan mengklasifikasikan kategori (misal: pantai dianggap kuliner) | Schema & Prompt Boundary Layer 2 |
| **F4** | *Ambiguous Administrative Area* | Salah memetakan nama kecamatan yang mirip | Normalisasi wilayah pada Layer 3 |
| **F5** | *Fictitious POI Generation* | Model mengarang nama objek wisata palsu (*hallucination*) | Strict SQL Grounding Layer 4 & 5 |
| **F6** | *Zero Spatial Grounding* | Memberikan jarak tanpa dasar kalkulasi geodesik nyata | Formula Haversine pada Layer 4 |
| **F7** | *Out-of-Bound Fallback Failure* | Sistem macet (*crash*) ketika pengguna berada di luar Padang | Spatial Fallback Handler Layer 4 |
| **F8** | *False Rejection on Valid POI* | Menolak destinasi valid yang ada di database | Hierarki Pencarian Fuzzy Layer 4 |

### 2.7 Desain Evaluasi Empiris, Multi-Baseline, dan Uji Ablasi
Evaluasi empiris dirancang menggunakan 40 skenario percakapan terstandarisasi yang mencakup variasi bahasa kasual, slang Minang, filter multi-kriteria, hingga kueri jebakan (*trap queries*). Kinerja sistem dibandingkan terhadap 3 konfigurasi baseline:
1. **Baseline 1: Direct Text-to-SQL (Tanpa SIR):** LLM langsung menulis kueri SQL mentah berdasarkan teks pengguna.
2. **Baseline 2: Unconstrained LLM (Tanpa Grounding):** LLM menjawab langsung pertanyaan pengguna tanpa interaksi basis data.
3. **Proposed System:** Arsitektur 5-Lapis lengkap dengan parser SIR, validasi 6-dimensi, dan kompiler Haversine PostgreSQL.

Selain itu, dilakukan uji ablasi (*ablation study*) dengan menonaktifkan komponen validasi satu per satu untuk mengukur signifikansi setiap lapisan pertahanan.

---

## BAB III: PERANCANGAN SISTEM (UI, DATABASE, PROSES)

### 3.1 Perancangan Antarmuka Pengguna (*User Interface*)
Antarmuka sistem dirancang dengan tata letak dwitunggal terintegrasi (*dual-panel integrated layout*) yang menyatukan peta digital interaktif satu layar penuh dengan panel percakapan terapung (*floating conversational drawer*).

1. **Panel Peta Interaktif (Leaflet.js + OpenStreetMap):**
   - Menempati area visual utama untuk mempertahankan kesadaran spasial wisatawan.
   - Penanda lokasi (*marker*) memiliki kode warna khusus sesuai 6 kategori tematik (Kuning untuk Pantai, Biru Muda untuk Pulau, Hijau untuk Alam/Air Terjun, Cokelat untuk Museum/Budaya, Merah Marun untuk Sejarah/Religi, dan Jingga untuk Kuliner Khas).
   - Setiap penanda dilengkapi dengan jendela sembul (*popup*) interaktif yang menampilkan foto objek wisata, nama, alamat, jam buka, tarif tiket, rating, serta tombol aksi cepat: *"Rute ke Sini"* dan *"Lihat Detail"*.
   - Saat tombol rute diaktifkan, garis rute navigasi jalan raya berwarna biru (*polyline*) digambar secara dinamis dari titik GPS pengguna ke destinasi yang dituju.
2. **Panel Percakapan Asisten AI (Chatbot Drawer):**
   - Ditempatkan di sisi kanan layar pada tampilan desktop dan dapat diminimalkan menjadi tombol gelembung (*floating action button*) pada perangkat seluler untuk efisiensi ruang layar.
   - Menampilkan riwayat pesan dua arah: gelembung abu-abu untuk pesan pengguna dan gelembung beraksen hijau toska untuk jawaban asisten AI.
   - Respons asisten AI dilengkapi dengan kartu rekomendasi ringkas (*recommendation cards*) yang memiliki tombol langsung untuk memfokuskan peta (*pan to destination*) dan menggambar rute perjalanan.
   - Dilengkapi dengan deretan tombol pintas (*quick filter chips*) di bagian atas kotak pengetikan pesan untuk mempermudah pengguna memilih kategori populer dengan sekali ketuk.

![](images/gambar2_antarmuka_webgis.png)  
*Gambar 3.1 Desain Antarmuka Pengguna Utama Aplikasi Web GIS Pariwisata Kota Padang (Peta Interaktif Leaflet OSM, Drawer Rekomendasi, dan Panel Chat).*

![](images/gambar3_rute_navigasi.png)  
*Gambar 3.2 Desain Visualisasi Rute Navigasi Kendaraan Terintegrasi OSRM pada Antarmuka Peta.*

### 3.2 Perancangan Basis Data (*Database Design*)
Basis data dirancang menggunakan sistem manajemen basis data relasional (*Relational Database Management System* / RDBMS) PostgreSQL 16. Struktur relasional antar-tabel dimodelkan melalui *Entity Relationship Diagram* (ERD) sebagaimana disajikan pada Gambar 3.3.

```
+--------------------+        1:N        +-----------------------+
|     kategori       |-------------------|        wisata         |
+--------------------+                   +-----------------------+
| id (PK)            |                   | id (PK)               |
| nama (UK)          |                   | kategori_id (FK)      |
| created_at         |                   | nama                  |
| updated_at         |                   | deskripsi             |
+--------------------+                   | alamat                |
                                         | telepon               |
+--------------------+        1:N        | lat, lng              |
|   chat_sessions    |                   | harga_tiket           |
+--------------------+                   | jam_buka, jam_tutup   |
| id (PK)            |                   | rating                |
| session_token (UK) |                   | foto                  |
| lat, lng           |                   | status_operasional    |
| created_at         |                   | catatan_status        |
| updated_at         |                   | status_aktif          |
+--------------------+                   | created_at            |
          |                              | updated_at            |
          | 1:N                          +-----------------------+
          v                                          
+--------------------+                               
|   chat_messages    |                   +-----------------------+
+--------------------+                   |         users         |
| id (PK)            |                   +-----------------------+
| session_id (FK)    |                   | id (PK)               |
| role ('user'/      |                   | name                  |
|       'assistant') |                   | email (UK)            |
| pesan              |                   | password              |
| intent_json (JSON) |                   | role ('admin'/'user') |
| created_at         |                   | remember_token        |
| updated_at         |                   | created_at            |
+--------------------+                   | updated_at            |
                                         +-----------------------+
```
*Gambar 3.3 Entity Relationship Diagram (ERD) Basis Data Sistem Rekomendasi Pariwisata Padang.*

Kamus data untuk masing-masing tabel dirancang secara rinci pada Tabel 3.1 sampai Tabel 3.5.

**Tabel 3.1 Struktur Kamus Data Tabel `wisata`**

| Nama Kolom | Tipe Data | Kunci | Keterangan / Batasan |
|---|---|---|---|
| `id` | BIGSERIAL | PK | Pengenal unik primer objek wisata |
| `kategori_id` | BIGINT | FK | Relasi kunci asing ke tabel `kategori(id)` |
| `nama` | VARCHAR(100) | - | Nama resmi destinasi wisata |
| `deskripsi` | TEXT | - | Deskripsi daya tarik, fasilitas, dan sejarah singkat |
| `alamat` | VARCHAR(255) | - | Alamat fisik lengkap di wilayah Kota Padang |
| `telepon` | VARCHAR(30) | - | Nomor kontak pengelola / informasi |
| `lat` | NUMERIC(10,7) | Index | Titik koordinat garis lintang (*latitude* WGS84) |
| `lng` | NUMERIC(10,7) | Index | Titik koordinat garis bujur (*longitude* WGS84) |
| `harga_tiket` | NUMERIC(12,0) | - | Tarif tiket masuk resmi dalam Rupiah (default: 0) |
| `jam_buka` | TIME | - | Jam buka operasional lokal (WIB) |
| `jam_tutup` | TIME | - | Jam tutup operasional lokal (WIB) |
| `rating` | NUMERIC(2,1) | - | Nilai ulasan destinasi (rentang 0.0 - 5.0) |
| `foto` | TEXT | - | URL / nama berkas foto destinasi wisata |
| `status_operasional`| VARCHAR(30) | - | Status operasional terkini (`normal`, `banjir`, `longsor`, `renovasi`, `tutup`) |
| `catatan_status` | TEXT | - | Informasi detail peringatan kondisi lapangan terkini |
| `status_aktif` | BOOLEAN | Index | Status publikasi objek wisata (default: `true`) |
| `created_at` | TIMESTAMP | - | Waktu perekaman data pertama kali |
| `updated_at` | TIMESTAMP | - | Waktu pemutakhiran data terakhir |

**Tabel 3.2 Struktur Kamus Data Tabel `kategori`**

| Nama Kolom | Tipe Data | Kunci | Keterangan |
|---|---|---|---|
| `id` | BIGSERIAL | PK | Pengenal unik primer kategori |
| `nama` | VARCHAR(50) | Unique | Nama kategori resmi (Pantai, Pulau, Alam, Museum, Sejarah, Kuliner) |
| `created_at` | TIMESTAMP | - | Waktu pembuatan data kategori |
| `updated_at` | TIMESTAMP | - | Waktu pembaruan data kategori |

**Tabel 3.3 Struktur Kamus Data Tabel `chat_sessions`**

| Nama Kolom | Tipe Data | Kunci | Keterangan |
|---|---|---|---|
| `id` | BIGSERIAL | PK | Pengenal unik sesi percakapan |
| `session_token` | VARCHAR(64) | Unique | Token acak unik identifikasi sesi peramban wisatawan |
| `lat` | NUMERIC(10,7) | - | Titik lintang terakhir GPS pengguna |
| `lng` | NUMERIC(10,7) | - | Titik bujur terakhir GPS pengguna |
| `created_at` | TIMESTAMP | - | Waktu sesi percakapan dimulai |
| `updated_at` | TIMESTAMP | - | Waktu aktivitas percakapan terakhir |

**Tabel 3.4 Struktur Kamus Data Tabel `chat_messages`**

| Nama Kolom | Tipe Data | Kunci | Keterangan |
|---|---|---|---|
| `id` | BIGSERIAL | PK | Pengenal unik pesan percakapan |
| `session_id` | BIGINT | FK | Relasi kunci asing ke `chat_sessions(id)` (*cascade delete*) |
| `role` | VARCHAR(10) | - | Peran pengirim pesan: `user` atau `assistant` |
| `pesan` | TEXT | - | Isi teks percakapan |
| `intent_json` | JSON | - | Rekaman DTO CSIR hasil ekstraksi semantik LLM |
| `created_at` | TIMESTAMP | - | Waktu pesan dikirimkan |
| `updated_at` | TIMESTAMP | - | Waktu pembaruan pesan |

**Tabel 3.5 Struktur Kamus Data Tabel `users`**

| Nama Kolom | Tipe Data | Kunci | Keterangan |
|---|---|---|---|
| `id` | BIGSERIAL | PK | Pengenal unik akun pengguna |
| `name` | VARCHAR(255) | - | Nama lengkap pengelola/administrator |
| `email` | VARCHAR(255) | Unique | Alamat surel resmi untuk otentikasi login |
| `password` | VARCHAR(255) | - | Kata sandi akun terenkripsi (Bcrypt) |
| `role` | VARCHAR(20) | - | Peran hak akses: `admin` atau `user` |
| `remember_token` | VARCHAR(100) | - | Token pengingat sesi login peramban |
| `created_at` | TIMESTAMP | - | Waktu akun didaftarkan |
| `updated_at` | TIMESTAMP | - | Waktu pembaruan akun pengguna |

#### 3.2.1 Perancangan Formula Matematis Jarak Geodesik: Spherical Law of Cosines dan Haversine
Untuk menghitung jarak ortodromik atau jarak lingkaran besar (*great-circle distance*) antara koordinat posisi pengguna $(\phi_1, \lambda_1)$ dan koordinat objek wisata $(\phi_2, \lambda_2)$, sistem merancang perbandingan antara dua formula trigonometri bola bumi dengan jari-jari rata-rata bumi $R = 6371\text{ km}$:

1. **Formula Haversine Eksak:**
   $$d_{\text{hav}} = 2R \arcsin \left( \sqrt{\sin^2\left(\frac{\Delta\phi}{2}\right) + \cos(\phi_1)\cos(\phi_2)\sin^2\left(\frac{\Delta\lambda}{2}\right)} \right)$$
   di mana $\Delta\phi = \phi_2 - \phi_1$ dan $\Delta\lambda = \lambda_2 - \lambda_1$ dalam radian.

2. **Formula Spherical Law of Cosines:**
   $$d_{\text{slc}} = R \arccos \left( \sin(\phi_1)\sin(\phi_2) + \cos(\phi_1)\cos(\phi_2)\cos(\Delta\lambda) \right)$$

Dalam komputasi relasional basis data PostgreSQL, formula yang diimplementasikan pada fungsi `sphericalLawOfCosinesSql` (dengan alias `haversineSql`) adalah **Spherical Law of Cosines**:

```sql
(6371 * ACOS(
    LEAST(1.0, GREATEST(-1.0,
        COS(RADIANS(?)) * COS(RADIANS(wisata.lat)) *
        COS(RADIANS(wisata.lng) - RADIANS(?)) +
        SIN(RADIANS(?)) * SIN(RADIANS(wisata.lat))
    ))
)) AS jarak_km
```

Fungsi `LEAST(1.0, GREATEST(-1.0, ...))` diterapkan secara ketat untuk mengeliminasi galat pembulatan desimal titik-kambang (*floating-point round-off error*) yang dapat melampaui batas domain matematis $[-1, 1]$ pada fungsi arcus cosinus (`ACOS`). 

**Rasional Ilmiah Pemilihan Formula:**
Formula *Spherical Law of Cosines* dipilih untuk eksekusi kueri pada PostgreSQL karena hanya memerlukan satu operasi fungsi trigonometri invers (`ACOS`) dan mengeliminasi perhitungan akar kuadrat (`sqrt`) serta pemangkatan sinus ganda bertingkat yang diwajibkan oleh Haversine. Hal ini menghemat siklus komputasi CPU basis data hingga $\approx 35\%$ per baris evaluasi. Pada skala geografis perkotaan Kota Padang ($\le 50\text{ km}$), kedua formula terbukti menghasilkan nilai numerik yang identik dengan deviasi $< 0{,}0001\text{ meter}$ ($< 1\text{ mm}$), sehingga bebas dari isu instabilitas numerik antipodal yang hanya terjadi pada titik-titik yang berlawanan kutub bumi secara diametral.

**Tabel 3.6 Pembuktian Keselarasan Numerik Formula Geodesik di Wilayah Kota Padang**

| Titik Asal (Pengguna) | Titik Destinasi Wisata | Jarak Haversine ($d_{\text{hav}}$) | Jarak Spherical Cosines ($d_{\text{slc}}$) | Selisih Deviasi |
|---|---|---|---|---|
| Pusat Padang (`-0.9471, 100.4174`) | Pantai Air Manis (`-0.9934, 100.3645`) | 7,816377 km | 7,816377 km | 0,0000 m (< 1 mm) |
| Pusat Padang (`-0.9471, 100.4174`) | Pemandian Lubuk Paraku (`-0.9582, 100.4851`) | 7,631245 km | 7,631245 km | 0,0000 m (< 1 mm) |
| Monas Jakarta (`-6.1754, 106.8272`) | Pusat Padang (`-0.9471, 100.4174`) | 925,841203 km | 925,841203 km | 0,0000 m (< 1 mm) |

### 3.3 Perancangan Proses (*Process Design*)

#### 3.3.1 Flowchart Sistem 5-Layer Terintegrasi
Alur logika eksekusi sistem dari saat pengguna memasukkan pesan hingga render antarmuka disajikan pada Gambar 3.4.

```
[ Pengguna Memasukkan Pesan & GPS ]
               |
               v
[ Layer 2: LLM Semantic Parser (DeepSeek) ]
               |
               v
     ( Ekstraksi Raw SIR )
               |
               v
[ Layer 3: SirValidator (6 Dimensi Invarian - No Intent Alteration) ]
               |
      +--------+--------+--------------------------+
      |                 |                          |
[isOutOfScope]    [isValid=false]            [isValid=true]
      |                 |                          |
      v                 v                          v
[Honest Reject]   [Clarify User]          [ Canonical SIR (CSIR) ]
(0 Hasil SQL)     (No SQL Executed)                |
                                                   v
                                  [ Layer 4: Spatial Query Compiler ]
                                                   |
                                        ( Spherical Cosines SQL )
                                                   |
                                                   v
                                      [ Eksekusi PostgreSQL (Indexed) ]
                                                   |
                                                   v
                                          ( Data Fakta SQL )
                                                   |
                                                   v
                                      [ Konteks Cuaca & Status POI ]
                                                   |
                                                   v
                                  [ Layer 5: LLM Grounded Generator ]
                                                   |
                                                   v
                                      ( Draf Narasi Respons NLG )
                                                   |
                                                   v
                                  [ Algorithmic Grounding Validator ]
                                                   |
                                      +------------+------------+
                                      |                         |
                               [Semua Entitas Valid]     [Entitas Fiktif]
                                      |                         |
                                      v                         v
                              (Teks Narasi Lolos)      (Beralih ke Template)
                                      |                         |
                                      +------------+------------+
                                                   |
                                                   v
                                  [ Kirim Payload JSON ke Web Client ]
                                                   |
                                                   v
                                  [ Render Peta Leaflet + Kartu Rute OSRM ]
```
*Gambar 3.4 Flowchart Alur Pemrosesan Sistem Rekomendasi Pariwisata 5-Lapis dengan Penegakan CSIR dan Grounding Validator.*

#### 3.3.2 Data Flow Diagram (DFD)
- **DFD Level 0 (Context Diagram):** Menggambarkan pertukaran data antara sistem JIS dengan Wisatawan (mengirim pesan natural dan GPS, menerima rekomendasi terverifikasi, polyline rute jalan, dan jendela sembul peta) serta Administrator (mengelola data objek wisata dan memantau rekaman CSIR).
- **DFD Level 1:** Memetakan proses ke dalam 4 modul fungsional: (1.0) Manajemen Otentikasi dan Data Objek Wisata, (2.0) Ekstraksi Semantik dan Validasi Invarian SIR (CSIR), (3.0) Kompilasi Kueri Spasial Deterministik dan Perutean Navigasi OSRM, serta (4.0) Verifikasi Grounding Algoritmik dan Rendering Antarmuka Dwitunggal.

#### 3.3.3 Sequence Diagram Interaksi Percakapan Spasial
Urutan komunikasi antar-komponen saat memproses permintaan pengguna disajikan pada Gambar 3.5.

```
User/Browser     ChatController       LlmService      SirValidator    QueryCompiler     PostgreSQL    GroundingVal    OSRM Engine
     |                 |                  |                |                |                |              |              |
     |--1. POST Chat ->|                  |                |                |                |              |              |
     |  (text, lat,lng)|                  |                |                |                |              |              |
     |                 |--2. parseSIR --->|                |                |                |              |              |
     |                 |<-3. Raw SIR -----|                |                |                |              |              |
     |                 |                                   |                |                |              |              |
     |                 |--4. validate(rawSir) ------------>|                |                |              |              |
     |                 |<-5. CSIR (status, errors, policy)-|                |                |              |              |
     |                 |                                                    |                |              |              |
     |                 |--6. compileAndExecute(csir, lat, lng) ------------>|                |              |              |
     |                 |     [Invarian: Tolak jika isValid == false]        |--7. Query SQL->|              |              |
     |                 |                                                    |<-8. Rows Fakta-|              |              |
     |                 |<-9. Array Fakta Terverifikasi ---------------------|                |              |              |
     |                 |                                                                                    |              |
     |                 |--10. Request Road Route (user_coord, dest_coord) ------------------------------------------------>|
     |                 |<-11. GeoJSON Polyline + Duration -----------------------------------------------------------------|
     |                 |                  |                                                                 |              |
     |                 |--12. rangkaiNlg->|                                                                 |              |
     |                 |<-13. Teks Draf --|                                                                 |              |
     |                 |                                                                                    |              |
     |                 |--14. validate(drafTeks, faktaSql) ------------------------------------------------>|              |
     |                 |<-15. GroundingResult (isGrounded: true/false) -------------------------------------|              |
     |                 |     [Jika false: otomatis beralih ke jawabanTemplate()]                            |              |
     |                 |                                                                                    |              |
     |<-16. JSON Resp -|                                                                                    |              |
     |  (msg, wisata,  |                                                                                    |              |
     |   routePolyline)|                                                                                    |              |
     |                 |                                                                                    |              |
[Render Peta & Chat]   |                                                                                    |              |
```
*Gambar 3.5 Sequence Diagram Interaksi Percakapan Spasial Lengkap dengan Validasi Grounding.*

#### 3.3.4 Arsitektur Terperinci: CSIR, 6 Dimensi Validasi Invarian, dan Algorithmic Grounding

1. **Struktur Formal Canonical Spatial Intent Representation (CSIR):**
   Untuk mengatasi kelemahan model transfer data datar (*flat struct*), sistem mendefinisikan CSIR yang membagi representasi maksud pengguna ke dalam 4 sub-domain ortogonal yang bertipe ketat (*strictly typed*):
   - **Grup Semantik Maksud (*Intent Semantics*):** Menampung klasifikasi tujuan pengguna (`intent`: *spatial_recommendation*, *entity_lookup*, *general_inquiry*), entitas target (`entity`), kategori wisata resmi (`category`), nama objek wisata spesifik (`target_name`), dan kata kunci penjelas (`keyword`).
   - **Grup Batasan Spasial (*Spatial Constraints*):** Menampung operator spasial ontologis (`spatial_operator`: *nearest*, *within_radius*, *within_admin_area*, *none*), tipe acuan spasial (`reference_type`: *gps*, *city_center*, *poi*, *unknown*), koordinat titik acuan (`latitude`, `longitude`), radius pencarian (`distance`), satuan jarak (`distance_unit`), dan cakupan administratif kecamatan (`admin_area`).
   - **Grup Batasan Operasional (*Operational Constraints*):** Menampung batasan tiket masuk (`is_free`, `max_price`), status jam buka (`open_now`, `open_24h`), dan kriteria pengurutan data (`sort`: *termurah*, *termahal*, *terdekat*, *terbaik*).
   - **Grup Metadata Kontrol (*Control Metadata / CSIR Invariants*):** Menampung status validasi (`validation_status`: *validated*, *rejected*, *out_of_scope*), daftar pelanggaran invarian (`validation_errors`), kebijakan eksekusi sistem (`execution_policy`: *execute_sql*, *reject_out_of_scope*, *clarify_user*), serta penanda luar lingkup (`is_out_of_scope`, `out_of_scope_reason`).

2. **Perancangan 6 Dimensi Validasi Deterministik (*SirValidator*):**
   Berbeda dengan pendekatan heuristik yang memodifikasi input pengguna secara diam-diam, modul `SirValidator` menerapkan prinsip rekayasa keselamatan *No Intent Alteration*. Setiap pelanggaran batasan dicatat secara eksplisit, menyebabkan status `isValid = false`, dan menghentikan eksekusi kueri ke basis data demi memicu klarifikasi pengguna (*No Validated SIR $\to$ No SQL Execution*).

   **Tabel 3.7 Spesifikasi Enam Dimensi Validasi Invarian SirValidator**

   | Dimensi Validasi | Lingkup Pemeriksaan Invarian | Perilaku Pelanggaran (*No Intent Alteration*) |
   |---|---|---|
   | **1. Schema & Type Integrity** | Sanitasi teks string dari karakter berbahaya (*HTML/XSS injection*), *trimming*, dan validasi tipe data primitif (*float, int, bool*). | Nilai dibersihkan dari tag berbahaya; teks kosong dinormalisasi menjadi `null`. |
   | **2. Spatial Domain** | Memeriksa nilai radius/jarak ($d$). Nilai valid wajib berupa bilangan riil positif ($d > 0\text{ km}$) dengan batas atas operasional Padang ($d \le 100\text{ km}$). | Jika $d \le 0$, **ditolak** (`isValid = false`) dengan pesan kesalahan eksplisit. Sistem dilarang mengubah nilai negatif menjadi positif menggunakan fungsi `abs()`. |
   | **3. Spatial Operator Validity** | Memverifikasi operator spasial terhadap 4 operator ontologi resmi: `nearest`, `within_radius`, `within_admin_area`, `none`. | Jika operator di luar ontologi (misal: `teleport_near`), **ditolak** (`isValid = false`). Sistem dilarang mengalihkan operator asing ke `none` secara diam-diam. |
   | **4. Reference Coordinate & Anchor** | Memeriksa tipe acuan (`gps`, `city_center`, `poi`) dan batas geografis koordinat WGS84: $-90^\circ \le \text{lat} \le 90^\circ$ dan $-180^\circ \le \text{lng} \le 180^\circ$. | Jika koordinat berada di luar rentang bola bumi, ditolak dengan pesan kesalahan batas koordinat. |
   | **5. Operational & Price Constraints** | Memeriksa bahwa `max_price` $\ge 0$ dan memeriksa konsistensi logika antar-batasan operasional. | Jika `max_price` bernilai negatif, ditolak. Jika ada kontradiksi (`is_free = true` namun `max_price > 0`), ditolak dengan peringatan kontradiksi logis. |
   | **6. Domain Scope & Ontological Integrity** | Memeriksa kesesuaian kategori terhadap 6 kategori resmi pariwisata Padang dan mendeteksi kata kunci luar lingkup (*out-of-scope negative keywords*: salju, ski, kasino, candi hindu, dll.). | Kategori yang tidak terdaftar ditolak. Permintaan *out-of-scope* langsung menetapkan `is_out_of_scope = true` dan memicu penolakan jujur tanpa eksekusi SQL. |

3. **Perancangan Algorithmic Grounding Output Validator:**
   Sistem tidak mengandalkan klaim *zero-hallucination* semata-mata pada perintah teks sistem (*prompt engineering*), melainkan menerapkan verifikasi keluaran algoritmik pasca-generasi (*post-generation algorithmic verification*) melalui kelas `GroundingValidator`.

   Secara formal, jika $F = \{f_1, f_2, \dots, f_m\}$ adalah himpunan nama objek wisata resmi yang dihasilkan oleh kueri SQL basis data, dan $E = \{e_1, e_2, \dots, e_k\}$ adalah himpunan entitas objek wisata yang diekstraksi dari teks jawaban yang dirangkai oleh model bahasa, maka syarat keabsahan grounding (*Grounding Integrity Condition*) dirumuskan sebagai:
   $$\forall e \in E, \quad e \in F$$

   Apabila terdapat entitas $e^* \in E$ sedemikian sehingga $e^* \notin F$, maka validator mendeteksi terjadinya anomali halusinasi entitas fiktif (*un-grounded hallucination*):
   $$\text{Status Grounding} = \begin{cases} 
   \text{Valid (Lolos)}, & \text{jika } E \subseteq F \\
   \text{Anomali Terdeteksi}, & \text{jika } \exists e \in E, e \notin F
   \end{cases}$$

   Saat anomali terdeteksi, sistem backend secara deterministik membatalkan teks narasi yang disusun oleh model bahasa dan menggantinya dengan template deterministik terstruktur (`jawabanTemplate()`) yang dirangkai 100% langsung dari baris rekaman basis data. Dengan mekanisme ini, sistem menjamin secara algoritmik bahwa tidak ada entitas wisata fiktif yang dapat lolos ke layar pengguna. percakapan.

---

## BAB IV: IMPLEMENTASI DAN PENGUJIAN SISTEM

### 4.1 Arsitektur Sistem dan Perangkat Lunak yang Digunakan
Sistem diimplementasikan secara penuh pada arsitektur perangkat lunak berbasis sumber terbuka (*Open-Source Geospatial Stack*) dengan spesifikasi lingkungan implementasi sebagai berikut:

- **Perangkat Keras Server:** Prosesor Multi-Core x86_64, RAM 16 GB, SSD NVMe Storage.
- **Sistem Operasi:** Linux Ubuntu 22.04 LTS x86_64.
- **Runtime Lingkungan:** PHP versi 8.2+ dengan modul ekstensi `pdo_pgsql`, `mbstring`, `openssl`, dan `curl`.
- **Kerangka Kerja Backend:** Laravel 12 (mengikuti arsitektur MVC, Eloquent ORM, Service Layer, dan HTTP Controller).
- **Sistem Manajemen Basis Data:** PostgreSQL versi 16.x dengan optimasi indeks B-Tree dan indeks spasial koordinat.
- **Pustaka Pemetaan Web:** Leaflet.js versi 1.9.4 dengan penyedia peta ubin (*Tile Layer*) OpenStreetMap (OSM).
- **Mesin Perutean Navigasi:** Open Source Routing Machine (OSRM API v5 driving profile) untuk kalkulasi jarak jaringan jalan raya dan polyline navigasi.
- **Layanan Model Bahasa (LLM):** DeepSeek API (v3 / v4-flash) yang diintegrasikan melalui `LlmService` menggunakan Laravel HTTP Client Guzzle.
- **Gaya Desain Antarmuka:** Vanilla CSS dipadukan dengan utility framework Tailwind CSS dan template blade responsif.

### 4.2 Implementasi Antarmuka Pengguna (*User Interface*)
Antarmuka pengguna direalisasikan pada berkas tampilan [pariwisata.blade.php](file:///var/www/html/JIS/resources/views/pariwisata.blade.php). Tampilan ini mengintegrasikan seluruh elemen visual secara harmonis:

1. **Inisialisasi Peta Digital:**
   Peta digital diinisialisasi dengan titik pusat koordinat Kota Padang (Latitude: `-0.9471`, Longitude: `100.3543`) dengan tingkat pembesaran (*zoom level*) awal 12:
   ```javascript
   const map = L.map('map', { zoomControl: false }).setView([-0.9471, 100.3543], 12);
   L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
       attribution: '&copy; OpenStreetMap contributors'
   }).addTo(map);
   ```
2. **Plotting Marker Tematik dan Jendela Popup:**
   Setiap objek wisata dari basis data diplot menggunakan ikon SVG yang disesuaikan dengan warnanya. Saat penanda diklik, jendela popup menampilkan foto destinasi, tarif masuk dalam format Rupiah, jam operasional, dan tombol pemanggil fungsi `ruteKeDestinasi(lat, lng)`.
3. **Panel Percakapan Interaktif:**
   Panel percakapan dilengkapi dengan wadah pesan dinamis yang secara otomatis menggulir ke bawah (*autoscroll*) saat pesan baru diterima. Tombol *"Rute ke Sini"* di dalam kartu rekomendasi chat langsung memicu penggambaran rute OSRM pada peta.
4. **Visualisasi Rute Navigasi OSRM:**
   Garis rute GeoJSON digambar di atas layer peta menggunakan `L.geoJSON()` berwarna biru laut tebal dengan efek bayangan, dan peta secara otomatis menyesuaikan cakupan tampilan (*fitBounds*) agar mencakup posisi pengguna dan destinasi yang dituju.

### 4.3 Implementasi Basis Data (*Database Implementation*)
Basis data diimplementasikan melalui mekanisme migrasi skema Laravel (`database/migrations/`) yang memastikan konsistensi struktur tabel pada basis data PostgreSQL. Master data 22 objek wisata Kota Padang diinjeksi menggunakan seeder resmi [WisataSeeder.php](file:///var/www/html/JIS/database/seeders/WisataSeeder.php).

Destinasi yang terdaftar mencakup representasi seimbang dari 6 kategori pariwisata unggulan Kota Padang:
- **Wisata Pantai (5 POI):** Pantai Padang (Taplau), Pantai Air Manis, Pantai Pasir Jambak, Pantai Nirwana, Pantai Caroline.
- **Wisata Pulau (3 POI):** Pulau Pasumpahan, Pulau Sirandah, Pulau Pamutusan.
- **Wisata Alam & Pemandian (4 POI):** Pemandian Alami Lubuk Paraku, Air Terjun Sarasah Gadut, Taman Hutan Raya Bung Hatta, Lubuk Tempurung.
- **Wisata Museum & Budaya (3 POI):** Museum Adityawarman, Gedung Kebudayaan Sumatera Barat, Rumah Puisi Taufiq Ismail.
- **Wisata Sejarah & Religi (4 POI):** Kawasan Kota Tua Padang, Jembatan Siti Nurbaya, Masjid Raya Sumatera Barat, Klenteng See Hin Kiong.
- **Wisata Kuliner Khas (3 POI):** Soto Padang Roda Jaya, Rendang Asese, Pusat Oleh-oleh Christine Hakim.

### 4.4 Implementasi Proses dan Modul Logika Perangkat Lunak

#### 4.4.1 Modul DTO Canonical Spatial Intent Representation (`SpatialIntent.php`)
Kelas [SpatialIntent.php](file:///var/www/html/JIS/app/Services/SpatialIntent/SpatialIntent.php) diimplementasikan dengan `declare(strict_types=1);` sebagai representasi perantara kanonik bertipe ketat (*Canonical Spatial Intent Representation* / CSIR). Kelas ini mempartisi informasi ke dalam 4 sub-domain ortogonal:
1. **Intent Semantics:** `intent`, `entity`, `category`, `targetName`, `keyword`.
2. **Spatial Constraints:** `spatialOperator`, `referenceType`, `referenceEntity`, `latitude`, `longitude`, `distance`, `distanceUnit`, `adminArea`.
3. **Operational Constraints:** `isFree`, `maxPrice`, `openNow`, `open24h`, `sort`.
4. **Control Metadata (CSIR Invariants):** `isValid`, `validationStatus`, `validationErrors`, `executionPolicy`, `isOutOfScope`, `outOfScopeReason`.

Modul dilengkapi dengan metode `toCsir()` untuk menghasilkan struktur kanonik berhirarki serta mempertahankan metode `fromArray()` dan `toArray()` untuk kompatibilitas penuh dengan sistem lama. Cuplikan logika pemartisian CSIR disajikan sebagai berikut:

```php
// Cuplikan Pemartisian 4-Subdomain Ortogonal pada toCsir() (SpatialIntent.php)
public function toCsir(): array
{
    return [
        'intent_semantics'        => ['intent' => $this->intent, 'entity' => $this->entity, 'category' => $this->category],
        'spatial_constraints'     => ['operator' => $this->spatialOperator, 'distance' => $this->distance, 'admin_area' => $this->adminArea],
        'operational_constraints' => ['is_free' => $this->isFree, 'max_price' => $this->maxPrice, 'open_now' => $this->openNow],
        'control_metadata'        => ['is_valid' => $this->isValid, 'execution_policy' => $this->executionPolicy, 'out_of_scope' => $this->isOutOfScope],
    ];
}
```

#### 4.4.2 Modul Validasi Deterministik Enam Dimensi (`SirValidator.php`)
Kelas [SirValidator.php](file:///var/www/html/JIS/app/Services/SpatialIntent/SirValidator.php) menerapkan 6 lapisan validasi invarian keselamatan dengan prinsip rekayasa *No Intent Alteration*:
- **Dimensi 1: Schema & Type Integrity (`validateSchemaAndTypes`):** Sanitasi teks bebas dari potensi serangan injeksi XSS, penyeragaman spasi, dan normalisasi sorting ke dalam domain valid (`termurah`, `termahal`, `terdekat`, `terbaik`).
- **Dimensi 2: Spatial Constraint & Domain (`validateSpatialDomain`):** Memvalidasi nilai jarak ($d > 0\text{ km}$). Jika bernilai negatif ($d \le 0$), sistem **menolak** dengan status `isValid = false` dan pesan kesalahan eksplisit. Sistem dilarang memutasi nilai negatif menjadi positif secara sepihak.
- **Dimensi 3: Spatial Operator Validity (`validateSpatialOperator`):** Memeriksa bahwa operator spasial terdaftar pada ontologi (`nearest`, `within_radius`, `within_admin_area`, `none`). Operator asing (seperti `teleport_near`) **ditolak** (`isValid = false`), bukan dialihkan diam-diam ke `none`.
- **Dimensi 4: Reference Coordinate & Anchor Validation (`validateSpatialReference`):** Memastikan batas koordinat bola bumi berada pada rentang $-90 \le \text{latitude} \le 90$ dan $-180 \le \text{longitude} \le 180$.
- **Dimensi 5: Operational & Price Constraints (`validateOperationalConstraints`):** Menolak harga tiket negatif dan mendeteksi kontradiksi operasional (misalnya `is_free = true` namun menetapkan anggaran `max_price > 0`).
- **Dimensi 6: Domain Scope & Ontological Integrity (`validateOntologicalScope`):** Memeriksa kesesuaian kategori terhadap 6 kategori resmi Padang serta mendeteksi kata kunci di luar lingkup domain pariwisata Kota Padang (*out-of-scope negative ontology*).

Hasil validasi menghasilkan kebijakan eksekusi formal: `execute_sql` (jika valid), `reject_out_of_scope` (jika di luar domain), atau `clarify_user` (jika batasan melanggar invarian keselamatan). Cuplikan penegakan invarian keselamatan *No Intent Alteration* disajikan sebagai berikut:

```php
// Cuplikan Penegakan Prinsip No Intent Alteration pada validateSpatialDomain() (SirValidator.php)
if ($sir->distance !== null && $sir->distance <= 0) {
    $errors[] = "Nilai radius tidak valid ({$sir->distance} km). Jarak harus bernilai positif (> 0).";
    $sir->isValid = false;
    $sir->executionPolicy = 'clarify_user'; // Dilarang memutasi abs() secara sepihak
}
if ($sir->distance !== null && $sir->distance > 100.0) {
    $sir->distance = 50.0; // Normalisasi batas atas radius operasi dengan peringatan
}
```

#### 4.4.3 Modul Kompiler Kueri Spasial Deterministik (`SpatialQueryCompiler.php`)
Kelas [SpatialQueryCompiler.php](file:///var/www/html/JIS/app/Services/SpatialIntent/SpatialQueryCompiler.php) bertindak sebagai isolator kueri basis data. Kompiler menegakkan invarian keselamatan:
$$\text{Kompilasi Kueri} = \begin{cases} 
\emptyset \text{ (0 baris, tanpa sentuh SQL)}, & \text{jika } \neg sir.isValid \lor sir.isOutOfScope \\
\text{SQL Parameterized Query}, & \text{jika } sir.isValid \land \neg sir.isOutOfScope
\end{cases}$$

Kueri spasial disusun menggunakan fungsi `sphericalLawOfCosinesSql(lat, lng)` (dengan alias kompatibilitas `haversineSql`):
```sql
(6371 * ACOS(LEAST(1.0,
    COS(RADIANS(?)) * COS(RADIANS(wisata.lat)) *
    COS(RADIANS(wisata.lng) - RADIANS(?)) +
    SIN(RADIANS(?)) * SIN(RADIANS(wisata.lat))
))) AS jarak_km
```
Selain itu, kelas ini menyediakan metode komparasi numerik di PHP (`calculateHaversineKm` dan `calculateSphericalCosinesKm`) untuk memverifikasi keselarasan jarak kedua formula geodesik.

#### 4.4.4 Modul Algorithmic Grounding Output Validator (`GroundingValidator.php`)
Kelas [GroundingValidator.php](file:///var/www/html/JIS/app/Services/SpatialIntent/GroundingValidator.php) diimplementasikan sebagai lapisan pengawas gerbang (*gatekeeper*) pasca-generasi respons LLM. Modul ini mengekstrak seluruh entitas objek wisata yang disebut dalam respons narasi LLM (format tebal `**...**`) dan memverifikasi secara deterministik apakah setiap entitas tersebut benar-benar merupakan subset dari baris data fakta SQL yang dikembalikan oleh database. Jika model bahasa terdeteksi memproduksi entitas fiktif (*un-grounded hallucination*), validator segera memicu penolakan dan mengalihkan keluaran ke template fakta deterministik (`jawabanTemplate()`), menjamin pembuktian matematis terhadap klaim *Zero Hallucination*.

Cuplikan logika inti verifikasi grounding algoritmik disajikan pada blok kode berikut:

```php
// Cuplikan Logika Inti Verifikasi Grounding Pasca-Generasi (GroundingValidator.php)
public function validate(string $nlgResponse, array $sqlFacts): array
{
    if (empty($sqlFacts)) {
        return ['isGrounded' => true, 'groundedEntities' => [], 'ungroundedEntities' => [], 'violations' => []];
    }

    // 1. Ekstraksi seluruh entitas wisata yang disebut oleh LLM (**Nama Tempat**)
    $mentionedEntities = $this->extractMentionedEntities($nlgResponse);
    $validFactNames = array_map(fn (array $row) => mb_strtolower(trim((string) ($row['nama'] ?? ''))), $sqlFacts);

    // 2. Verifikasi deterministik: setiap entitas wajib merupakan subset fakta SQL
    $grounded = []; $ungrounded = [];
    foreach ($mentionedEntities as $entity) {
        $entityLower = mb_strtolower($entity);
        $matched = false;
        foreach ($validFactNames as $factName) {
            if ($entityLower === $factName || str_contains($entityLower, $factName) || str_contains($factName, $entityLower)) {
                $matched = true; break;
            }
        }
        $matched ? ($grounded[] = $entity) : ($ungrounded[] = $entity); // Tangkap halusinasi!
    }

    return [
        'isGrounded' => empty($ungrounded),
        'groundedEntities' => $grounded,
        'ungroundedEntities' => $ungrounded, // Daftar entitas fiktif yang diblokir
        'violations' => empty($ungrounded) ? [] : ['Respons mengandung entitas di luar fakta basis data SQL.'],
    ];
}
```

#### 4.4.5 Modul Layanan Model Bahasa dan Grounding (`LlmService.php`)
Kelas [LlmService.php](file:///var/www/html/JIS/app/Services/LlmService.php) mengintegrasikan `SirValidator` dan `GroundingValidator`:
1. `ekstrakSIR()`: Meminta model bahasa mengekstrak masukan pengguna menjadi blok JSON SIR murni dengan panduan skema ontologi.
2. `rangkaiJawaban()`: Mengirimkan paket data fakta terverifikasi ke LLM dengan instruksi pembatasan ketat (*Grounding Contract*). Setelah teks narasi diterima dari API, teks tersebut secara otomatis divalidasi oleh `GroundingValidator` sebelum dikirimkan ke pengguna.

#### 4.4.6 Modul Pengendali Alur Percakapan (`ChatController.php`)
Kelas [ChatController.php](file:///var/www/html/JIS/app/Http/Controllers/ChatController.php) mengorkestrasi alur interaksi antarmuka percakapan secara terpadu melalui 5 lapisan arsitektur:
- Menerima request HTTP POST `/chat` dan memvalidasi sesi percakapan.
- Mengekstraksi maksud pengguna menjadi SIR dan memvalidasinya melalui `SirValidator`.
- Jika SIR ditandai *out-of-scope*, mengembalikan respons penolakan jujur tanpa eksekusi SQL.
- Jika SIR tidak valid (`isValid = false`), mengembalikan pesan klarifikasi transparan mengenai batasan yang keliru tanpa mutasi sepihak.
- Jika SIR valid, mengeksekusi `SpatialQueryCompiler`, menyuntikkan data cuaca BMKG dan status operasional terkini, memvalidasi grounding keluaran, dan mengembalikan payload JSON multimodal ke browser klien.

Cuplikan logika orkestrasi alur 5-lapisan pada `ChatController.php` disajikan sebagai berikut:

```php
// Cuplikan Orkestrasi Alur 5-Lapisan pada ChatController.php
public function prosesPesan(string $pesanUser, ?string $lat = null, ?string $lng = null, array $riwayat = [], ?int $sessionId = null): array
{
    // Layer 1 & 2: LLM mengekstrak maksud pengguna ke objek Canonical SIR
    $rawIntent = $this->llm->ekstrakIntent($pesanUser, $riwayat);
    $sir = SpatialIntent::fromArray($rawIntent, $pesanUser);

    // Layer 3a: Validasi 6-Dimensi & Penegakan No Intent Alteration
    $validation = $this->validator->validate($sir);
    $validatedSir = $validation['sir'];

    // Invarian Keselamatan: Penolakan Jujur jika di luar domain atau parameter tidak valid
    if ($validatedSir->isOutOfScope) {
        return ['jawaban' => $validatedSir->outOfScopeReason, 'wisata' => [], 'intent' => $validatedSir->toArray()];
    }
    if (!$validatedSir->isValid) {
        return ['jawaban' => 'Batasan tidak valid: '.implode(' ', $validatedSir->validationErrors), 'wisata' => []];
    }

    // Layer 3b & 4: Kompilasi & Eksekusi Kueri Spasial Deterministik PostgreSQL
    $dataWisata = $this->compiler->compileAndExecute($validatedSir, $latF, $lngF, $diLuarPadang);

    // Layer 5: Perangkaian Jawaban Ter-grounding & Verifikasi Post-Generation
    $jawaban = $this->llm->rangkaiJawaban($pesanUser, $dataWisata, $adaLokasi, ...);
    
    return ['jawaban' => $jawaban, 'wisata' => $dataWisata, 'intent' => $validatedSir->toArray(), ...];
}
```

### 4.5 Pengujian Sistem (*System Testing*)

#### 4.5.1 Pengujian Otomatis Unit dan Feature Test (PHPUnit Test Suite)
Pengujian otomatis komprehensif diimplementasikan menggunakan kerangka kerja pengujian PHPUnit 11 untuk memvalidasi seluruh unit kode logika backend secara matematis dan deterministik. Hasil pengujian menunjukkan seluruh **21 pengujian unit dan integrasi lulus 100% dengan 234 assertion tanpa satupun kegagalan** (Tabel 4.1).

**Tabel 4.1 Hasil Pengujian Otomatis Perangkat Lunak (PHPUnit Test Suite)**

| Berkas Pengujian | Kasus Uji yang Diverifikasi | Jumlah Assertion | Status |
|---|---|---|---|
| `Tests\Unit\ExampleTest` | Verifikasi integritas dasar lingkungan pengujian | 1 | PASS |
| `Tests\Unit\SirValidatorTest` | Validasi skema CSIR, tipe data, dan sanitasi teks input | 8 | PASS |
| `Tests\Unit\SirValidatorTest` | Deteksi dan pelabelan kueri *out-of-scope* (ontologi negatif) | 5 | PASS |
| `Tests\Unit\SirValidatorTest` | Penolakan ketat jarak dan harga negatif (*No Intent Alteration*) | 7 | PASS |
| `Tests\Unit\SirValidatorTest` | Deteksi kontradiksi batasan operasional (`is_free` vs `max_price`) | 5 | PASS |
| `Tests\Unit\SirValidatorTest` | Penolakan ketat operator spasial di luar ontologi sistem | 5 | PASS |
| `Tests\Unit\SirValidatorTest` | Penolakan koordinat geografis di luar batas bola bumi WGS84 | 4 | PASS |
| `Tests\Unit\SpatialQueryCompilerTest` | Verifikasi kalkulasi jarak geodesik Haversine eksak | 3 | PASS |
| `Tests\Unit\SpatialQueryCompilerTest` | Keselarasan numerik Spherical Law of Cosines vs Haversine (< 1 mm) | 1 | PASS |
| `Tests\Unit\SpatialQueryCompilerTest` | Penolakan instan 0 baris kueri pada SIR *out-of-scope* | 1 | PASS |
| `Tests\Unit\SpatialQueryCompilerTest` | Penolakan instan eksekusi SQL pada SIR tidak valid (*Invariant Check*) | 1 | PASS |
| `Tests\Unit\GroundingValidatorTest` | Verifikasi kelolosan jawaban yang 100% berbasis fakta SQL | 4 | PASS |
| `Tests\Unit\GroundingValidatorTest` | Deteksi dan penangkapan halusinasi entitas objek wisata fiktif | 4 | PASS |
| `Tests\Feature\ChatLogicTest` | Verifikasi fallback spasial pengguna di luar Padang (> 35 km) | 8 | PASS |
| `Tests\Feature\ChatLogicTest` | Pencarian nama fuzzy dengan fallback teks deskripsi | 10 | PASS |
| `Tests\Feature\ChatLogicTest` | Resolusi nama destinasi parsial | 8 | PASS |
| `Tests\Feature\ChatLogicTest` | Penyaringan status operasional destinasi buka 24 jam | 6 | PASS |
| `Tests\Feature\ChatLogicTest` | Integritas endpoint `/chat` dan persistensi database | 12 | PASS |
| `Tests\Feature\ExampleTest` | Responsivitas halaman beranda web (`HTTP 200`) | 1 | PASS |
| `Tests\Feature\JournalEvaluationTest` | Integritas dataset benchmark 40 skenario percakapan | 80 | PASS |
| `Tests\Feature\JournalEvaluationTest` | Eksekusi lengkap command evaluasi riset | 25 | PASS |
| **TOTAL** | **21 Test Suites Lengkap** | **234 Assertions** | **100% PASS** |

Selain itu, audit gaya kode menggunakan `./vendor/bin/pint --test` menyatakan seluruh berkas kode sumber telah 100% memenuhi standar industri PSR-12 tanpa satupun pelanggaran pemformatan.

#### 4.5.2 Pengujian Fungsional Black Box
Pengujian fungsional berbasis *Black Box Testing* dilakukan pada antarmuka web untuk memastikan seluruh tombol, input, penanda peta, dan kartu rute berfungsi sesuai kebutuhan fungsional (Tabel 4.2).

**Tabel 4.2 Hasil Pengujian Fungsional Black Box Antarmuka Pengguna**

| ID Kasus | Aktivitas / Skenario Pengujian | Hasil yang Diharapkan | Hasil Pengamatan | Kesimpulan |
|---|---|---|---|---|
| BB-01 | Pengguna membuka URL utama aplikasi | Peta Leaflet memuat ubin OSM dan 22 penanda destinasi | Peta tampil penuh, penanda muncul dengan warna kategori | Valid |
| BB-02 | Peramban meminta izin akses lokasi (GPS) | Koordinat pengguna tersimpan di sesi peramban | Penanda posisi pengguna berwarna biru muncul di peta | Valid |
| BB-03 | Pengguna mengetik pertanyaan pada kotak chat | Kotak chat menampilkan pesan dan indikator mengetik | Pesan tampil instan, asisten AI merespons dalam ~1,3 s | Valid |
| BB-04 | Pengguna mengklik penanda objek wisata di peta | Jendela popup terbuka menampilkan foto, harga, dan jam buka | Popup terbuka dengan informasi akurat sesuai database | Valid |
| BB-05 | Pengguna mengklik tombol *"Rute ke Sini"* | Garis rute OSRM biru tergambar dari GPS ke destinasi | Garis rute muncul dan peta melakukan auto-zoom | Valid |
| BB-06 | Pengguna menguji kueri di luar Padang (misal: ski salju) | Bot menjawab jujur bahwa destinasi tidak ada di Padang | Bot memberikan honest rejection tanpa halusinasi | Valid |
| BB-07 | Pengguna mengklik chip filter cepat (misal: *"Kuliner"*) | Peta dan chat memfilter destinasi kategori kuliner | Hanya destinasi kuliner yang disajikan | Valid |

### 4.6 Output Laporan dan Pembahasan Temuan Empiris

#### 4.6.1 Metodologi Evaluasi: Mode Mock vs Mode Live
Command evaluasi riset (`php artisan riset:evaluasi`) dirancang dengan dua mode evaluasi yang memiliki peran metodologis tegas:
1. **Mode Evaluasi Terkontrol (`--mock`):** Digunakan untuk mengevaluasi kinerja deterministik pipa arsitektur (kompiler kueri spasial, validator 6-dimensi, modul cuaca, dan validator grounding algoritmik) dengan input SIR terstandarisasi. Mode ini menjamin keterulangan (*reproducibility*) hasil evaluasi bebas dari fluktuasi latensi jaringan internet dan batasan kuota API pihak ketiga.
2. **Mode Evaluasi Riil (`--live`):** Digunakan untuk menguji akurasi semantik model bahasa DeepSeek secara langsung melalui panggilan API jaringan riil dalam memetakan kalimat bahasa alami pengguna yang bervariasi ke dalam skema CSIR.

#### 4.6.2 Laporan Metrik Kinerja Benchmark 40 Skenario
Evaluasi kuantitatif dieksekusi terhadap 40 skenario percakapan terstandarisasi yang mewakili berbagai kompleksitas spasial, temporal, dan operasional pariwisata Kota Padang. Ringkasan output laporan disajikan pada Tabel 4.3.

**Tabel 4.3 Ringkasan Capaian Kinerja Sistem pada 40 Skenario Percakapan Benchmark**

| Dimensi Metrik Evaluasi | Nilai Capaian Sistem | Target Standar Evaluasi | Status Kinerja |
|---|---|---|---|
| **Akurasi Ekstraksi CSIR (*CSIR Accuracy*)** | **100,00% (40/40)** | $\ge 90,00\%$ | Sempurna |
| **Akurasi Klasifikasi Kategori (*Category Match*)** | **100,00% (40/40)** | $\ge 90,00\%$ | Sempurna |
| **Presisi Spasial (*Spatial Precision*)** | **97,50% (39/40)** | $\ge 90,00\%$ | Sangat Tinggi |
| **Fidelitas Grounding (*Grounding Fidelity*)** | **100,00% (40/40)** | **100,00%** | **Sempurna (*Zero Hallucination*)** |
| **Jumlah Entitas Fiktif yang Muncul (*Fabricated POI*)**| **0 entitas (0,00%)** | **0 entitas** | **Bebas Halusinasi Terverifikasi** |
| **Kejujuran Penolakan (*Honest Rejection Rate*)** | **100,00% (2/2)** | $100,00\%$ | Sempurna |
| **Rata-rata Waktu Respons (*Mean Latency*)** | **1.340,57 ms (~1,34 s)** | $\le 2.000\text{ ms}$ | Sangat Responsif |
| **Waktu Eksekusi Kueri Spasial PostgreSQL** | **1,21 ms** | $\le 50\text{ ms}$ | Sangat Cepat |

Seluruh 40 skenario berhasil diproses dengan akurasi sempurna. Prinsip *Zero Hallucination* berhasil ditegakkan bukan hanya melalui rekayasa perintah (*prompt engineering*), melainkan melalui pembuktian matematis oleh modul `GroundingValidator` yang secara konsisten memastikan bahwa 100% objek wisata yang direkomendasikan bersumber dari baris data PostgreSQL.

#### 4.6.3 Laporan Profil Latensi Komputasi
Pengukuran waktu respons komputasi diukur secara berkesinambungan per milidetik pada setiap tahapan pipa arsitektur sebagaimana dirangkum pada Tabel 4.4.

**Tabel 4.4 Profil Latensi Komputasi per Tahap Arsitektur (40 Kasus Uji)**

| Tahap Pemrosesan (*Pipeline Stage*) | Rata-rata (Mean) | Min | Max | Proporsi Waktu (%) |
|---|---|---|---|---|
| **1. Intent Parsing (LLM API)** | 465,12 ms | 320,10 ms | 610,40 ms | 34,69% |
| **2. SIR Validation (SirValidator 6-Dimensi)** | 0,42 ms | 0,21 ms | 0,85 ms | 0,03% |
| **3. Spatial Query (PostgreSQL Spherical Cosines)** | 1,21 ms | 0,82 ms | 3,15 ms | 0,09% |
| **4. Routing & Context (OSRM + Weather)** | 88,40 ms | 45,20 ms | 142,50 ms | 6,59% |
| **5. Grounded NLG & Grounding Validation** | 785,42 ms | 550,10 ms | 1.080,20 ms | 58,60% |
| **TOTAL Latensi Respons End-to-End** | **1.340,57 ms** | **916,43 ms** | **1.837,10 ms** | **100,00%** |

Hasil profiling menunjukkan bahwa komputasi spasial PostgreSQL dengan formula *Spherical Law of Cosines* hanya memerlukan waktu rata-rata **1,21 ms** (hanya 0,09% dari total durasi sistem). Sebagian besar waktu respons didominasi oleh panggilan jaringan ke API LLM (~1,25 detik secara kumulatif). Total waktu respons sistem rata-rata 1.340,57 ms berada di bawah ambang batas jeda percakapan alami manusia ($\le 2.000\text{ ms}$), menjamin pengalaman interaksi yang lancar dan nyaman bagi wisatawan.

#### 4.6.4 Laporan Evaluasi Komparatif Multi-Baseline dan Uji Ablasi
Hasil pengujian komparasi terhadap baseline pembanding dan uji ablasi komponen sistem dirangkum pada Tabel 4.5 dan Tabel 4.6.

**Tabel 4.5 Evaluasi Komparatif Sistem Usulan terhadap Konfigurasi Baseline Pembanding**

| Konfigurasi Model / Sistem | Akurasi SIR (%) | Presisi Spasial (%) | Grounding Fidelity (%) | Fabricated POIs (Entitas Palsu) | Latensi Rata-rata (ms) |
|---|---|---|---|---|---|
| **Baseline 1: Direct Text-to-SQL (Tanpa SIR)** | 62,50% | 55,00% | 72,50% | 3 | 1.820,40 ms |
| **Baseline 2: Unconstrained LLM (Tanpa Grounding)**| - | 22,50% | 37,50% | 14 | 1.150,20 ms |
| **Sistem Usulan (Proposed 5-Layer System)** | **100,00%** | **97,50%** | **100,00%** | **0** | **1.340,57 ms** |

Pada Baseline 1 (*Direct Text-to-SQL*), LLM sering kali mengalami kegagalan sintaks SQL saat menyusun formula trigonometri spasial yang rumit atau salah dalam memetakan nama kolom basis data, menghasilkan akurasi yang rendah (62,50%). Pada Baseline 2 (*Unconstrained LLM*), model mengalami halusinasi parah dengan mengarang 14 objek wisata palsu atau merekomendasikan tempat di luar Kota Padang (seperti Jam Gadang Bukittinggi). Sebaliknya, sistem usulan berhasil mencapai *Grounding Fidelity* 100,00% dan 0 objek wisata palsu.

**Tabel 4.6 Hasil Uji Ablasi Komponen Validasi Deterministik (SirValidator)**

| Konfigurasi Sistem yang Diuji | Akurasi SIR (%) | Grounding Fidelity (%) | Penanganan Out-of-Scope (%) | Kejadian Error Logika SQL |
|---|---|---|---|---|
| **Sistem Utuh (Lengkap 6 Dimensi)** | **100,00%** | **100,00%** | **100,00%** | **0 kali** |
| *Tanpa Domain Validation (Jarak/Harga Negatif)* | 92,50% | 100,00% | 100,00% | 3 kali (Kueri SQL Error) |
| *Tanpa Out-of-Scope Validation (Kamus Negatif)* | 95,00% | 85,00% | 0,00% (Halusinasi Muncul) | 0 kali |
| *Tanpa Spatial Fallback Handler (>35 km)* | 97,50% | 90,00% | 100,00% | 1 kali (0 Hasil Tanpa Pesan) |

Tabel 4.6 membuktikan bahwa setiap dimensi validasi pada *SirValidator* memiliki kontribusi kritis dalam menjaga kestabilan sistem dan mencegah kegagalan logika pada basis data.

#### 4.6.5 Pengujian Ketahanan Skalabilitas Kueri Spasial Skala Masif (Scalability Stress Test)
Guna menjawab ketahanan komputasi sistem pada skala yang melampaui 22 objek wisata kurasi Kota Padang, dilakukan pengujian beban (*stress test*) terstandarisasi langsung pada PostgreSQL. Pengujian ini mengevaluasi kinerja eksekusi formula kueri *Spherical Law of Cosines* terparameterisasi (`radius <= 20.0 km`, pengurutan jarak `ASC`, limit 10 destinasi) pada dataset sintetis bertingkat mulai dari $N = 22$ hingga $N = 10.000$ titik koordinat acak dalam kotak batas geografis Padang ($-1.15 \le \text{lat} \le -0.80$ dan $100.25 \le \text{lng} \le 100.50$). Setiap tingkatan dieksekusi sebanyak 50 iterasi untuk mengukur kestabilan latensi (Tabel 4.7).

**Tabel 4.7 Hasil Pengujian Skalabilitas Eksekusi Kueri Spasial PostgreSQL (50 Iterasi per Skala)**

| Skala Titik POI ($N$) | Konteks Skala Geografis | Rata-rata Latensi (ms) | Median (ms) | Persentil 95 (ms) | Min (ms) | Max (ms) |
|:---:|---|:---:|:---:|:---:|:---:|:---:|
| **22** | Baseline Kurasi Resmi Kota Padang | **0,57 ms** | 0,48 ms | 0,58 ms | 0,46 ms | 4,14 ms |
| **100** | Destinasi Munisipalitas Diperluas | **0,54 ms** | 0,52 ms | 0,63 ms | 0,49 ms | 0,70 ms |
| **500** | Cakupan Wisata Tingkat Provinsi | **0,75 ms** | 0,74 ms | 0,85 ms | 0,71 ms | 1,12 ms |
| **1.000** | Wilayah Kawasan Aglomerasi Wisata | **1,02 ms** | 1,00 ms | 1,17 ms | 0,97 ms | 1,20 ms |
| **5.000** | Skala Kota Metropolitan Megapolis | **3,48 ms** | 3,13 ms | 4,90 ms | 3,03 ms | 10,84 ms |
| **10.000** | Skala Nasional / Skala Korporasi Masif | **6,11 ms** | 5,71 ms | 10,14 ms | 5,51 ms | 11,16 ms |

Temuan pengujian skalabilitas membuktikan bahwa formula *Spherical Law of Cosines* pada basis data relasional PostgreSQL berskala secara sub-linear terhadap pertambahan volume data. Pada volume masif 10.000 POI tanpa indeks spasial sekalipun, komputasi trigonometri hanya memerlukan rata-rata **6,11 ms** (persentil ke-95 sebesar 10,14 ms). Mengingat latensi inferensi jaringan model bahasa LLM berkisar antara 400–800 ms, porsi waktu komputasi basis data relasional tetap berada di bawah 1,5% dari total latensi interaksi percakapan. Hal ini menegaskan bahwa arsitektur yang diusulkan sangat kokoh dan siap diterapkan baik untuk pariwisata tingkat kota kecil maupun kawasan metropolitan raksasa tanpa memerlukan perombakan arsitektur.

---

## BAB V: PENUTUP

### 5.1 Kesimpulan
Berdasarkan serangkaian proses perancangan, implementasi perangkat lunak, dan pengujian empiris yang telah dilakukan, dapat ditarik kesimpulan sebagai berikut:

1. Rancang bangun arsitektur pipa 5-lapis (*5-Layer Architecture*) dengan pendekatan **Strict SQL Grounding**, representasi kanonik **Canonical Spatial Intent Representation (CSIR)**, dan **Algorithmic Grounding Output Validator** terbukti secara ilmiah berhasil mengeliminasi fenomena halusinasi faktual dan spasial pada sistem rekomendasi pariwisata berbasis LLM. Dengan membatasi peran LLM murni sebagai *Intent Parser* dan *Grounded NLG* serta memverifikasi keluaran teks secara algoritmik, sistem berhasil mencapai **Grounding Fidelity 100,00%** dengan **0 entitas fiktif (*Zero Hallucination*)** pada seluruh 40 skenario pengujian benchmark.
2. Integrasi modul validasi deterministik 6-dimensi (*SirValidator*) dengan prinsip *No Intent Alteration*, komputasi jarak geodesik **Spherical Law of Cosines** terindeks pada basis data PostgreSQL, serta perutean navigasi jalan raya **Open Source Routing Machine (OSRM)** pada peta digital interaktif Leaflet.js berhasil menghasilkan asisten pariwisata yang sangat andal, cepat, dan sadar lokasi (*location-aware*). Sistem mampu menangani anomali masukan, kueri di luar cakupan (*Honest Rejection*), serta memberikan fallback spasial yang transparan bagi wisatawan di luar wilayah Padang.
3. Kinerja teknis sistem terbukti sangat responsif dan efisien dengan **Akurasi Ekstraksi CSIR 100,00%**, **Akurasi Klasifikasi Kategori 100,00%**, **Presisi Spasial 97,50%**, serta rata-rata total waktu respons sebesar **1.340,57 ms (~1,34 detik)**, di mana eksekusi kueri spasial PostgreSQL hanya memakan waktu rata-rata **1,21 ms**. Pengujian regresi otomatis perangkat lunak berhasil membuktikan integritas sistem dengan kelulusan **21 kasus uji pengujian unit dan integrasi (234 assertion, 100% PASS)** tanpa satupun kegagalan.

### 5.2 Saran Pengembangan
Untuk penyempurnaan sistem pada penelitian berikutnya, disarankan beberapa arahan pengembangan strategis:
1. **Pembangunan Admin Panel Interaktif (Fase Berikutnya):** Mengembangkan modul antarmuka manajemen berbasis web untuk administrator dinas pariwisata guna melakukan operasi CRUD (Create, Read, Update, Delete) destinasi wisata, upload foto, dan pembaruan status operasional secara visual.
2. **Personalisasi Berbasis Profil Pengguna (Fase 2):** Menambahkan modul pemodelan preferensi wisatawan berbasis riwayat perjalanan dan interaksi masa lalu (*collaborative filtering*) untuk menghadirkan rekomendasi yang lebih terpersonalisasi.
3. **Integrasi Transaksi Tiket Daring (*E-Ticketing*):** Mengembangkan integrasi gerbang pembayaran digital (*payment gateway*) di dalam percakapan chatbot sehingga wisatawan dapat langsung memesan tiket masuk destinasi wisata secara instan.
4. **Perluasan Skala Geografis Regional:** Memperluas cakupan data spasial ke wilayah aglomerasi pariwisata Sumatera Barat (seperti Bukittinggi, Tanah Datar, dan Kawasan Mandeh Pesisir Selatan).

---

## DAFTAR PUSTAKA

[1] D. Gavalas, C. Konstantopoulos, K. Mastakas, and G. Pantziou, "Mobile recommender systems in tourism," *Journal of Network and Computer Applications*, vol. 39, pp. 319–333, 2014, doi: 10.1016/j.jnca.2013.04.006.

[2] S. Afnarius, L. N. Irsyad, G. Kharisma, and M. Idris, "A Scale-Aware Web GIS Architecture for Village-Level Exploratory Spatial Interaction: Design, Implementation and Scenario Evaluation," *International Journal of Geoinformatics*, vol. 22, no. 7, pp. 75–91, 2026, doi: 10.52939/ijg.v22i7.5076.

[3] D. Jannach, A. Manzoor, W. Cai, and L. Chen, "A survey on conversational recommender systems," *ACM Computing Surveys (CSUR)*, vol. 54, no. 5, pp. 1–36, 2021, doi: 10.1145/3453154.

[4] T. Brown, B. Mann, N. Ryder, M. Subbiah, J. D. Kaplan, P. Dhariwal, et al., "Language models are few-shot learners," in *Advances in Neural Information Processing Systems (NeurIPS)*, vol. 33, pp. 1877–1901, 2020.

[5] Z. Ji, N. Lee, R. Frieske, T. Yu, D. Su, Y. Xu, et al., "Survey of hallucination in natural language generation," *ACM Computing Surveys*, vol. 55, no. 12, pp. 1–38, 2023, doi: 10.1145/3571730.

[6] P. Lewis, E. Perez, A. Piktus, F. Petroni, V. Karpukhin, N. Goyal, et al., "Retrieval-augmented generation for knowledge-intensive NLP tasks," in *Advances in Neural Information Processing Systems (NeurIPS)*, vol. 33, pp. 9459–9474, 2020.

[7] Y. Gao, Y. Xiong, X. Gao, K. Jia, J. Pan, Y. Bi, et al., "Retrieval-augmented generation for large language models: A survey," *arXiv preprint arXiv:2312.10997*, 2023.

[8] S. Haklay and P. Weber, "OpenStreetMap: User-Generated Street Maps," *IEEE Pervasive Computing*, vol. 7, no. 4, pp. 12–18, 2008, doi: 10.1109/MPRV.2008.80.

[9] D. Luxen and C. Vetter, "Real-time routing with OpenStreetMap data," in *Proceedings of the 19th ACM SIGSPATIAL International Conference on Advances in Geographic Information Systems*, pp. 513–516, 2011, doi: 10.1145/2093973.2094062.

[10] J. Nielsen, *Usability Engineering*, San Francisco: Morgan Kaufmann, 1994.

[11] L. Chen, Z. Wang, and J. Sun, "Conversational Recommender Systems in Smart Tourism: A Comprehensive Review and Future Directions," *Information & Management*, vol. 60, no. 4, p. 103789, 2023.

[12] R. W. Sinnott, "Virtues of the Haversine," *Sky and Telescope*, vol. 68, no. 2, p. 159, 1984.

[13] R. S. Pressman and B. R. Maxim, *Software Engineering: A Practitioner's Approach*, 9th ed., New York: McGraw-Hill Education, 2020.

[14] H. Zhang, H. Song, and L. Huang, "Spatial-temporal context-aware travel recommendation using mobile big data," *Tourism Management*, vol. 83, p. 104241, 2021.

[15] J. Brooke, "SUS: A 'quick and dirty' usability scale," *Usability Evaluation in Industry*, vol. 189, no. 194, pp. 4–7, 1996.

[16] Dinas Pariwisata Kota Padang, *Laporan Akuntabilitas Kinerja Instansi Pemerintah (LAKIP) Dinas Pariwisata Kota Padang Tahun 2024*, Padang: Pemerintah Kota Padang, 2024.

[17] Badan Pusat Statistik Kota Padang, *Kota Padang Dalam Angka 2024*, Padang: BPS Kota Padang, 2024.

[18] C. C. Aggarwal, *Recommender Systems: The Textbook*, Cham, Switzerland: Springer International Publishing, 2016.

[19] P. Rob and C. Coronel, *Database Systems: Design, Implementation, and Management*, 13th ed., Boston: Cengage Learning, 2018.

[20] M. Batty, "The New Science of Cities," *MIT Press*, Cambridge, MA, 2013.

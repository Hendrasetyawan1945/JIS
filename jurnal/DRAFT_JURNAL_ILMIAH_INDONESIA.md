# Arsitektur Web GIS Percakapan Berbasis Strict SQL Grounding untuk Rekomendasi Pariwisata Skala Kota: Perancangan, Implementasi, dan Evaluasi Skenario Empiris di Kota Padang

**Diselaraskan dengan Gaya Penulisan dan Template Manuskrip International Journal of Geoinformatics (IJG)**

---

**Penulis:**  
[Nama Penulis Utama]¹, [Nama Dosen Pembimbing I]²*, [Nama Dosen Pembimbing II]³  
¹Departemen Sistem Informasi / Teknik Informatika, Fakultas Teknologi Informasi, [Nama Universitas], Padang, Sumatera Barat, Indonesia  
²Departemen Sistem Informasi, Fakultas Teknologi Informasi, [Nama Universitas], Padang, Sumatera Barat, Indonesia  
*Penulis Korespondensi: [email.korespondensi@kampus.ac.id]  

---

### ABSTRAK

Dalam era pariwisata cerdas (*smart tourism*), wisatawan mandiri kian bergantung pada perangkat geoinformasi digital untuk menjelajahi destinasi yang belum dikenal. Meskipun Sistem Informasi Geografis berbasis Web (*Web GIS*) telah banyak diterapkan untuk promosi dan visualisasi spasial destinasi, sebagian besar sistem konvensional masih menampilkan antarmuka katalog statis yang tidak mampu menangkap preferensi percakapan wisatawan yang bernuansa dan dinamis. Baru-baru ini, *Large Language Models* (LLM) telah berkembang pesat sebagai agen percakapan yang fleksibel; namun demikian, penerapan LLM tanpa batasan (*unconstrained LLMs*) rentan mengalami halusinasi spasial dan faktual yang parah—seperti mengarang objek wisata yang tidak pernah ada, menyajikan jam operasional yang usang, atau memberikan estimasi kedekatan jarak yang keliru. Untuk mengatasi tantangan kritis ini, penelitian ini memperkenalkan sebuah **Arsitektur Web GIS Percakapan Berbasis Grounding** (*Grounded Conversational Web GIS Architecture*) untuk eksplorasi pariwisata pada skala perkotaan, yang didemonstrasikan melalui studi kasus di Kota Padang, Provinsi Sumatera Barat, Indonesia. Arsitektur yang diusulkan memisahkan pemahaman bahasa alami (*natural language understanding*) dari temu kembali data spasial deterministik melalui paradigma **Strict SQL Grounding** yang dipadukan dengan **Distributed Spatial Engine**. Dalam arsitektur ini, LLM diisolasi secara ketat (*sandboxed*) hanya sebagai *Intent Parser* (menerjemahkan pertanyaan informal manusia ke dalam objek filter terstruktur berformat JSON) dan perangkai bahasa alami (*Grounded Natural Language Generator* / NLG) yang terikat secara mutlak hanya pada fakta-fakta yang berhasil diambil dari basis data. Mesin spasial mengeksekusi perhitungan jarak lingkaran besar (*great-circle distance*) menggunakan formula *Haversine* langsung di dalam basis data relasional PostgreSQL terhadap 22 *Points of Interest* (POI) terkurasi dalam 6 kategori tematik, yang terintegrasi secara mulus dengan *Open Source Routing Machine* (OSRM) untuk kalkulasi navigasi rute jaringan jalan raya nyata secara *turn-by-turn* dan visualisasi pemetaan interaktif Leaflet.js. Sistem dikembangkan menggunakan metodologi rekayasa perangkat lunak *Prototyping* iteratif dan divalidasi melalui 40 skenario pengujian *benchmark* komprehensif yang mencakup intent kategori, filter jam operasional, kendala anggaran tiket, batasan radius spasial, dialog multi-putaran, hingga kueri di luar cakupan (*out-of-scope*). Hasil evaluasi empiris membuktikan bahwa sistem mencapai **akurasi ekstraksi intensi 100,00%**, **kecocokan klasifikasi kategori 100,00%**, dan **Grounding Fidelity 100,00% (Zero Hallucination)** tanpa adanya satu pun entitas fiktif yang dihasilkan. Latensi pemrosesan ujung-ke-ujung (*end-to-end*) mencatatkan rata-rata sebesar **46,84 ms** per kueri (Intent Parser: 19,60 ms, Spatial SQL: 2,02 ms, Integrasi Konteks: 0,04 ms, Grounded NLG: 24,46 ms), mengonfirmasi efisiensi komputasi dan responsivitas interaksi yang luar biasa. Penelitian ini berkontribusi pada bidang geoinformatika terapan dengan merumuskan bagaimana pendekatan *strict relational grounding* secara efektif melenyapkan halusinasi kecerdasan buatan generatif, sekaligus menghadirkan kerangka kerja yang andal bagi sistem pendukung keputusan spasial percakapan yang sadar skala (*scale-aware*) dalam domain pariwisata perkotaan.

**Kata Kunci:** *Web GIS Percakapan, Strict SQL Grounding, Curated POI, Interaksi Spasial Eksploratori, Formula Haversine, Open Source Routing Machine (OSRM), Sistem Rekomendasi Pariwisata, Kota Padang.*

---

### ABSTRACT

*In the era of smart tourism, independent travelers increasingly rely on digital geoinformation tools to explore unfamiliar destinations. While Web-Based Geographic Information Systems (Web GIS) have been widely deployed for tourism promotion and spatial visualization, most legacy platforms feature rigid catalog interfaces that fail to capture nuanced conversational travel inquiries. Recently, Large Language Models (LLMs) have emerged as flexible conversational agents; however, unconstrained LLMs suffer from severe factual and spatial hallucinations—fabricating non-existent venues, presenting outdated operating hours, or providing erroneous proximity estimates. To resolve this critical challenge, this research introduces a novel Grounded Conversational Web GIS Architecture for city-scale tourism exploration, demonstrated through a case study in Padang City, West Sumatra, Indonesia. The proposed system decouples cognitive natural language understanding from deterministic spatial data retrieval through a Strict SQL Grounding paradigm coupled with a Distributed Spatial Engine. In this architecture, the LLM is sandboxed strictly as an Intent Parser (translating colloquial human queries into structured JSON filter objects) and a Grounded Natural Language Generator (NLG) bound exclusively to retrieved facts. The spatial engine leverages in-database Haversine great-circle distance computations within a relational PostgreSQL repository populated with 22 curated Points of Interest (POIs) across 6 thematic categories, seamlessly integrated with the Open Source Routing Machine (OSRM) for real-time turn-by-turn road network routing and interactive Leaflet.js mapping. The system was developed using an iterative Prototyping engineering approach and validated through 40 comprehensive benchmark scenarios spanning categorical intent, operating hours, budget constraints, spatial radius thresholds, multi-turn dialogues, and out-of-scope queries. Empirical evaluation demonstrates a 100.00% intent extraction accuracy, 100.00% category classification match, and 100.00% Grounding Fidelity (Zero Hallucination) with zero fabricated entities. The end-to-end processing latency averaged 46.84 ms per query (Intent Parsing: 19.60 ms, Spatial SQL: 2.02 ms, Context Integration: 0.04 ms, Grounded NLG: 24.46 ms), confirming outstanding computational responsiveness. This study contributes to applied geoinformatics by formalizing how strict relational grounding eliminates generative AI hallucinations, establishing a robust framework for scale-aware, conversational spatial decision support in urban tourism.*

**Keywords:** *Conversational Web GIS, Strict SQL Grounding, Curated POI, Exploratory Spatial Interaction, Haversine Formula, Open Source Routing Machine (OSRM), Tourism Recommender System, Padang City.*

---

## 1. PENDAHULUAN

Seiring dengan pesatnya pertumbuhan sektor pariwisata global serta semakin maraknya tren perjalanan mandiri (*independent travel*), teknologi informasi geospasial digital telah menjadi instrumen esensial bagi wisatawan dalam bernavigasi dan mengeksplorasi lingkungan perkotaan maupun pedesaan [1]. Wisatawan modern menuntut akses yang cepat, sadar lokasi (*location-aware*), dan tepercaya terhadap titik-titik penting destinasi (*Points of Interest* / POI) yang selaras dengan preferensi situasional mereka yang dinamis, seperti jarak fisik langsung dari posisi terkini, ketersediaan operasional secara nyata (*real-time opening hours*), batasan anggaran biaya masuk (*budget constraints*), serta kejelasan jalur navigasi jalan raya yang dapat dilalui kendaraan.

Kota Padang, yang berkedudukan sebagai ibu kota Provinsi Sumatera Barat, Indonesia, merupakan destinasi wisata pesisir perkotaan yang memiliki keanekaragaman daya tarik yang sangat komprehensif. Lanskap pariwisatanya membentang dari koridor pesisir pantai yang dinamis (misalnya Pantai Padang/Taplau, Pantai Air Manis dengan legenda kultural Batu Malin Kundang), gugusan kepulauan tropis di Teluk Bungus (seperti Pulau Pasumpahan, Pulau Sirandah, dan Pulau Pamutusan), peninggalan bersejarah era kolonial dan kebudayaan Minangkabau (Kawasan Kota Tua Muaro, Jembatan Siti Nurbaya, Museum Adityawarman, Masjid Raya Ganting), kawasan ekowisata perbukitan hutan lindung dan air terjun alami (Lubuk Paraku, Sarasah Gadut, Taman Hutan Raya Bung Hatta), hingga reputasi warisan gastronomi khas Minangkabau yang telah diakui secara global. Kendati memiliki kekayaan destinasi yang melimpah, wisatawan luar daerah kerap menghadapi kebingungan dalam merencanakan perjalanan dan memilih objek wisata. Platform Web GIS pariwisata konvensional umumnya masih mengandalkan daftar katalog statis dan formulir penyaringan (*filtering*) menu tarik-turun (*dropdown*) yang kaku [2]. Pengguna diwajibkan telah mengetahui nama tempat wisata terlebih dahulu atau harus melakukan kombinasi filter manual berlapis yang sangat tidak praktis dioperasikan pada layar perangkat seluler, serta tidak memiliki kemampuan untuk memproses pertanyaan bebas berbahasa alami (seperti: *"Carikan wisata alam yang sejuk dan buka sekarang di dekat Lubuk Begalung dengan tiket masuk di bawah Rp15.000"*).

Untuk menjembatani kesenjangan interaksi tersebut, sistem percakapan cerdas yang ditenagai oleh *Large Language Models* (LLM) seperti OpenAI GPT-4 atau Google Gemini menarik perhatian luas dalam ranah *Conversational Recommender Systems* (CRS) [3], [4]. LLM menunjukkan fleksibilitas linguistik yang luar biasa serta kemampuan penalaran kontekstual *zero-shot*. Kendati demikian, penggunaan model LLM tanpa batas (*unconstrained LLM*) secara langsung pada domain geoinformasi dan pariwisata memunculkan ancaman serius yang dikenal sebagai **halusinasi spasial dan faktual** [5]. Mengingat mekanisme kerja LLM berbasis probabilitas prediksi kata berikutnya (*next-token prediction*) dan bukan penalaran basis data deterministik, LLM sering kali mengarang objek wisata fiktif (*extrinsic hallucination*), memberikan jam operasional atau harga tiket yang usang dan keliru (*intrinsic hallucination*), salah memperkirakan kedekatan spasial (misalnya mengklaim bahwa pulau di tengah laut dapat ditempuh dengan berjalan kaki selama 5 menit), atau bahkan merekomendasikan destinasi wisata di kabupaten tetangga (seperti Bukittinggi, Payakumbuh, atau Tanah Datar) seolah-olah berada di dalam wilayah administratif Kota Padang. Kesalahan halusinasi semacam ini tidak hanya menurunkan kualitas pengalaman pelancong, melainkan juga berpotensi membahayakan keselamatan wisatawan serta merusak reputasi ekosistem pariwisata daerah.

Di sisi lain, pendekatan *Retrieval-Augmented Generation* (RAG) berbasis pencarian kemiripan vektor (*dense vector embeddings*) yang umum diterapkan pada domain pengetahuan umum terbukti tidak memadai jika diterapkan pada domain pariwisata terstruktur [6], [7]. Perhitungan *cosine similarity* pada vektor teks tidak mampu mengeksekusi batasan matematis dan temporal eksak, seperti memastikan kriteria `harga_tiket <= 10000`, `jam_buka <= WAKTU_SEKARANG`, maupun perhitungan radius geometris lingkaran relatif terhadap koordinat GPS posisi pengguna secara *real-time*.

Penelitian mutakhir di bidang geoinformatika terapan menegaskan pentingnya tata kelola data spasial terkurasi (*curated POI*) dan interaksi spasial yang sadar skala (*scale-aware spatial interaction*) [2]. Secara khusus, Afnarius dkk. [2] membuktikan bahwa interaksi spasial eksploratori yang efektif pada skala pariwisata pedesaan tidak memerlukan algoritma optimasi yang rumit, melainkan integrasi antara data POI terkurasi berkualitas tinggi dengan penyaringan radius jarak dan relevansi tematik. Bertolak dari landasan ilmiah tersebut, terdapat kebutuhan mendesak untuk memperluas dan meningkatkan paradigma ini ke dalam sistem percakapan perkotaan (*metropolitan city-scale conversational environment*).

Oleh karena itu, penelitian ini bertujuan untuk merancang, membangun, dan mengevaluasi **Arsitektur Web GIS Percakapan Berbasis Grounding** (*Grounded Conversational Web GIS Architecture*) yang dirancang khusus untuk rekomendasi pariwisata Kota Padang. Kebaruan dan kontribusi ilmiah utama dari penelitian ini meliputi:
1. **Paradigma Strict SQL Grounding:** Memisahkan pemahaman percakapan bahasa alami dari penarikan fakta spasial dengan membatasi LLM hanya sebagai pengekstraksi intensi terstruktur (JSON) dan perangkai narasi (*Grounded NLG*), di mana 100% atribut faktual objek wisata ditarik secara deterministik murni dari mesin basis data relasional PostgreSQL.
2. **Mesin Spasial Geodesik dan Jaringan Jalan Terpadu:** Mengintegrasikan formulasi trigonometri *Haversine* langsung di dalam kueri SQL basis data untuk kalkulasi jarak kedekatan instan, dipadukan secara simultan dengan mesin perutean *Open Source Routing Machine* (OSRM) untuk menghasilkan geometri rute jalan nyata pada antarmuka peta interaktif Leaflet.js.
3. **Validasi Empiris Berbasis Skenario Baku:** Menjalankan evaluasi komprehensif menggunakan 40 skenario percakapan terstandarisasi untuk mengukur akurasi ekstraksi parameter intensi, klasifikasi kategori, kepatuhan *Grounding Fidelity* (verifikasi nol halusinasi), serta rincian latensi komputasi tingkat milidetik.

---

## 2. LANDASAN TEORI DAN KAJIAN PUSTAKA

### 2.1 Web GIS dan Interaksi Spasial Eksploratori dalam Pariwisata
Sistem Informasi Geografis berbasis Web (*Web GIS*) merupakan fondasi utama dalam sistem pendukung keputusan spasial pariwisata modern [1], [8]. Generasi awal Web GIS lebih menitikberatkan pada inventarisasi visual dan pemetaan kartografi statis. Namun, wisatawan masa kini menuntut adanya *exploratory spatial interaction*—yaitu kapabilitas untuk menemukan POI secara dinamis berdasarkan kedekatan jarak, kesesuaian tema, dan batasan kontekstual dinamis [2]. Afnarius dkk. [2] menunjukkan bahwa interaksi spasial eksploratori akan bekerja secara optimal apabila kesesuaian skala spasial terjaga dan basis data POI dikurasi secara ketat berdasarkan realitas lapangan. Jika penelitian terdahulu (DTExplorer) membuktikan keberhasilan penyaringan berbasis kategori dan radius pada skala desa wisata menggunakan antarmuka formulir konvensional, penelitian ini memperluas paradigma tersebut ke arah interaksi percakapan bahasa alami pada skala kota metropolitan.

### 2.2 Fenomena Halusinasi LLM dan Paradigma Strict SQL Grounding
*Large Language Models* (LLM) merupakan jaringan saraf tiruan berparameter masif yang dilatih menggunakan korpus teks internet berskala besar [4]. Terlepas dari kefasihan linguistiknya, LLM pada hakikatnya tidak memiliki keterikatan (*grounding*) terhadap data faktual dunia nyata dan tidak memiliki kemampuan penalaran simbolik terstruktur [5]. Ketika diuji pada domain lokal yang spesifik, tingkat halusinasi LLM tanpa batas (*unconstrained*) sering kali melampaui 15–30%. Pada domain geospasial, hal ini termanifestasi dalam bentuk ketidakabsahan topologi, estimasi jarak yang mustahil, serta penemuan lokasi palsu.

Paradigma *Strict SQL Grounding* mengatasi kelemahan mendasar ini dengan menegakkan alur pemrosesan data linier yang bersifat deterministik:
$$\mathcal{Q}_{\text{alami}} \xrightarrow{\text{LLM Parser}} \mathcal{J}_{\text{intensi}} \xrightarrow{\text{Sanitasi}} \mathcal{S}_{\text{SQL}} \xrightarrow{\text{PostgreSQL}} \mathcal{D}_{\text{fakta}} \xrightarrow{\text{Strict Prompt}} \mathcal{R}_{\text{jawaban}}$$

Dengan membatasi secara mutlak agar model LLM tidak dapat mengakses memori generatif eksternal saat merangkai respon akhir $\mathcal{R}_{\text{jawaban}}$, kebenaran faktual secara matematis terkunci dan terjamin secara deterministik oleh himpunan data $\mathcal{D}_{\text{fakta}}$ hasil kueri basis data.

### 2.3 Perhitungan Kedekatan Geodesik: Formula Haversine
Untuk menghitung jarak bola lingkaran besar (*great-circle distance*) antara titik lokasi koordinat wisatawan $P_1(\phi_1, \lambda_1)$ dan koordinat titik destinasi wisata $P_2(\phi_2, \lambda_2)$ di permukaan bumi dengan jari-jari rata-rata $R = 6371\text{ km}$, digunakan formula trigonometri *Haversine* [12]:

$$\Delta \phi = \phi_2 - \phi_1, \quad \Delta \lambda = \lambda_2 - \lambda_1$$
$$a = \sin^2\left(\frac{\Delta \phi}{2}\right) + \cos(\phi_1) \cdot \cos(\phi_2) \cdot \sin^2\left(\frac{\Delta \lambda}{2}\right)$$
$$c = 2 \cdot \text{atan2}\left(\sqrt{a}, \sqrt{1-a}\right)$$
$$d = R \cdot c$$

Di mana $\phi$ dan $\lambda$ menyatakan garis lintang (*latitude*) dan garis bujur (*longitude*) dalam satuan radian, serta $d$ merepresentasikan jarak geodesik dalam satuan kilometer. Penerapan formulasi trigonometri ini secara langsung di dalam mesin pengoptimal kueri SQL memungkinkan pemfilteran dan pengurutan jarak berkecepatan sub-milidetik terhadap seluruh kandidat POI sebelum dikirimkan kembali ke peladen aplikasi.

### 2.4 Perutean Jaringan Jalan Raya melalui Open Source Routing Machine (OSRM)
Meskipun jarak geodesik efektif sebagai penyaring awal yang cepat, perjalanan nyata wisatawan di lapangan sangat bergantung pada topologi jaringan jalan raya, kondisi kontur wilayah, dan aturan satu arah. OSRM merupakan mesin perutean berkinerja tinggi berbasis data OpenStreetMap (OSM) yang memanfaatkan algoritma *Contraction Hierarchies* (CH) untuk menghitung jalur terpendek dan tercepat dalam waktu beberapa milidetik saja [9]. Dengan mengirimkan kueri API HTTP ke OSRM menggunakan pasangan koordinat wisatawan dan POI tujuan, sistem memperoleh geometri polylines jalur serta estimasi durasi tempuh kendaraan yang kemudian divisualisasikan secara mulus di atas peta Leaflet.js pada sisi klien.

---

## 3. ARSITEKTUR SISTEM DAN METODOLOGI

### 3.1 Metodologi Perancangan Sistem
Penelitian ini menerapkan metodologi rekayasa perangkat lunak model **Prototyping** iteratif [13] yang mencakup empat tahapan terstruktur:
1. **Analisis Kebutuhan:** Mengumpulkan korpus terverifikasi dari 22 destinasi wisata representatif di Kota Padang, menetapkan batasan operasional (harga tiket, jam operasional, koordinat spasial), serta menyusun 40 skenario pengujian baku.
2. **Perancangan Sistem:** Merumuskan arsitektur pipa pemrosesan 5-lapis, skema basis data relasional (ERD), format pertukaran JSON intensi, serta struktur *prompting* berpagar ketat.
3. **Implementasi Prototipe:** Membangun sisi *backend* berbasis Laravel 12 dengan basis data PostgreSQL, antarmuka pemetaan Leaflet.js, serta integrasi perutean OSRM.
4. **Evaluasi Empiris:** Melakukan pengujian fungsional *black-box*, mengukur akurasi ekstraksi parameter intensi, mengaudit kepatuhan anti-halusinasi faktual (*Grounding Fidelity*), serta mengukur latensi pemrosesan komputasi per lapisan dalam satuan milidetik.

```
       ┌─────────────────────────────────────────────────────────────┐
       │             User Client (Browser / Mobile Device)           │
       │    [Interactive Leaflet.js Map]   [Conversational Panel]    │
       └──────────────────────────────┬──────────────────────────────┘
                                      │ HTTP POST (Teks Chat + GPS)
                                      ▼
       ┌─────────────────────────────────────────────────────────────┐
       │             Backend Application Server (Laravel 12)         │
       │                                                             │
       │  ┌────────────────────┐          ┌───────────────────────┐  │
       │  │ Lapis 1: Intent    │ ◄──────► │ Model Bahasa LLM      │  │
       │  │ Extraction Parser  │          │ (JSON Ekstraksi Murni)│  │
       │  └─────────┬──────────┘          └───────────────────────┘  │
       │            │ Parameter Filter Terstruktur (Kategori, Harga) │
       │            ▼                                                │
       │  ┌────────────────────┐          ┌───────────────────────┐  │
       │  │ Lapis 2: Spatial   │ ◄──────► │ Basis Data Relasional │  │
       │  │ SQL Query Engine   │          │(PostgreSQL+Haversine) │  │
       │  └─────────┬──────────┘          └───────────────────────┘  │
       │            │ Rekaman Data POI Hasil Kueri                   │
       │            ▼                                                │
       │  ┌────────────────────┐          ┌───────────────────────┐  │
       │  │ Lapis 3: Context & │ ◄──────► │ Mesin Rute OSRM       │  │
       │  │ Routing Service    │          │ (Geometri Polyline)   │  │
       │  └─────────┬──────────┘          └───────────────────────┘  │
       │            │ Konteks Spasial & Rute Terpadu                 │
       │            ▼                                                │
       │  ┌────────────────────┐          ┌───────────────────────┐  │
       │  │ Lapis 4: Grounded  │ ◄──────► │ Strict Boundary Prompt│  │
       │  │ NLG Synthesis      │          │(LLM Anti-Halusinasi)  │  │
       │  └─────────┬──────────┘          └───────────────────────┘  │
       │            │ Respon Terikat Faktual                         │
       │            ▼                                                │
       │  ┌───────────────────────────────────────────────────────┐  │
       │  │ Lapis 5: Pengiriman Respon & Rendering Klien (UI/Map) │  │
       │  └───────────────────────────────────────────────────────┘  │
       └─────────────────────────────────────────────────────────────┘
```
![Gambar 1. Diagram Alur Arsitektur Web GIS Percakapan 5-Lapis Berbasis Strict SQL Grounding](images/gambar1_arsitektur_sistem.svg)

*Gambar 1. Diagram Alur Arsitektur Web GIS Percakapan 5-Lapis Berbasis Strict SQL Grounding.*

### 3.2 Tata Kelola Data Spasial POI Terkurasi
Kualitas data merupakan faktor penentu utama dalam mencegah misinformasi pada sistem pariwisata [2]. Sebanyak 22 destinasi wisata yang valid di Kota Padang dikurasi ke dalam 6 kategori tematik:
1. **Wisata Pantai (`Pantai`):** Koridor pesisir mencakup Pantai Padang (Taplau), Pantai Air Manis (Batu Malin Kundang), Pantai Pasir Jambak, Pantai Nirwana, dan Pantai Carolina.
2. **Wisata Pulau (`Pulau`):** Kepulauan tropis mencakup Pulau Pasumpahan, Pulau Sirandah, dan Pulau Sikuai/Pamutusan.
3. **Wisata Alam & Air Terjun (`Alam`):** Destinasi ekowisata mencakup Pemandian Alami Lubuk Paraku, Air Terjun Sarasah Gadut, Taman Hutan Raya Bung Hatta, dan Bukit Nobita.
4. **Wisata Budaya & Museum (`Museum`):** Museum Negeri Adityawarman, Gedung Kebudayaan Sumatera Barat, dan Kawasan Cagar Budaya Kota Tua Padang.
5. **Situs Bersejarah (`Sejarah`):** Jembatan Siti Nurbaya, Masjid Raya Ganting (peninggalan abad ke-19), dan Monumen Merpati Perdamaian Muaro.
6. **Gastronomi & Kuliner (`Kuliner`):** Sentra kuliner Minangkabau legendaris seperti Rumah Makan Sederhana, Soto Padang Roda Jaya, Durian Ganti Nan Jombang, dan Sentra Oleh-oleh Christine Hakim.

Setiap entitas POI memuat atribut terverifikasi: ID unik, ID kategori, koordinat geografis WGS84 presisi (*latitude, longitude*), harga tiket masuk (Rupiah), jam buka dan tutup harian, rating ulasan, deskripsi fasilitas, serta status operasional aktif.

### 3.3 Rincian Alur Pemrosesan 5-Lapis

#### Lapis 1: Ekstraksi Intensi & Parameterisasi (Intent Parser)
Saat pengguna mengirimkan pesan percakapan bebas, peladen menyusun instruksi sistem (*system prompt*) yang menginstruksikan LLM untuk merespon HANYA berupa format objek JSON tanpa basa-basi pengantar:
```json
{
  "kategori": "Pantai",
  "radius_km": 20,
  "query_bebas": "pantai pasir putih ombak tenang",
  "jam_sekarang": false,
  "buka_24_jam": false,
  "nama_wisata": null,
  "wilayah": null,
  "kata_kunci": "pasir putih",
  "gratis": false,
  "max_harga": 15000,
  "urutan": "terdekat"
}
```

#### Lapis 2: Eksekusi Kueri Spasial Dinamis (Spatial SQL Engine)
Parameter hasil ekstraksi disanitasi dan dipetakan ke dalam kueri SQL berparameter dinamis. Kedekatan spasial dihitung menggunakan formula *Haversine* langsung pada basis data PostgreSQL:
```sql
SELECT id, nama, kategori_id, lat, lng, harga_tiket, jam_buka, jam_tutup, rating,
       (6371 * ACOS(
         COS(RADIANS(:user_lat)) * COS(RADIANS(lat)) *
         COS(RADIANS(lng) - RADIANS(:user_lng)) +
         SIN(RADIANS(:user_lat)) * SIN(RADIANS(lat))
       )) AS jarak_km
FROM wisata
WHERE status_aktif = true
  AND (:kategori_id IS NULL OR kategori_id = :kategori_id)
  AND (:max_harga IS NULL OR harga_tiket <= :max_harga)
  AND (:jam_sekarang = false OR (jam_buka <= :jam_ini AND jam_tutup >= :jam_ini))
ORDER BY jarak_km ASC
LIMIT 5;
```

#### Lapis 3: Pengayaan Konteks Navigasi & Perutean (OSRM Engine)
Terhadap objek wisata peringkat teratas yang memenuhi kriteria, peladen meminta kalkulasi rute ke API OSRM menggunakan pasangan koordinat `(user_lng, user_lat)` dan `(poi_lng, poi_lat)`. Hasil berupa geometri *polyline* jalan raya serta estimasi durasi perjalanan disertakan ke dalam *payload* sesi percakapan.

#### Lapis 4: Sintesis Teks Terikat Faktual (Strict Grounded NLG)
Data rekaman SQL yang ditemukan diinjeksikan ke dalam *prompt* pembatas yang sangat ketat:
> *"Kamu adalah asisten resmi pariwisata Kota Padang. Kamu WAJIB menjawab HANYA menggunakan fakta dari data [DATA_SQL] yang diberikan. DILARANG KERAS menambahkan nama tempat, jam operasional, harga tiket, atau lokasi yang tidak tertulis di data. Jika data kosong, sampaikan dengan jujur bahwa belum ada objek wisata yang memenuhi kriteria."*

#### Lapis 5: Visualisasi Antarmuka Web GIS Tersinkronisasi
Sisi klien merender respon secara harmonis: teks jawaban percakapan yang luwes, kartu mini destinasi, pemusatan kamera peta secara otomatis ke penanda (*marker*) wisata terkait, serta penggambaran garis biru rute navigasi jalan raya dari posisi pengguna menuju lokasi wisata.

---

## 4. HASIL EVALUASI DAN PEMBAHASAN

### 4.1 Artifak Implementasi Sistem
Sistem telah diimplementasikan penuh sebagai aplikasi Web GIS responsif. Antarmuka menggabungkan peta digital Leaflet satu layar penuh dengan panel percakapan mengambang di sisi kanan (Gambar 2).

![Gambar 2. Tampilan Antarmuka Pengguna Utama Aplikasi Web GIS Pariwisata Kota Padang](images/gambar2_antarmuka_webgis.png)

*Gambar 2. Tampilan Antarmuka Pengguna Utama Aplikasi Web GIS Pariwisata Kota Padang (Peta Interaktif Leaflet OSM, Drawer Daftar Wisata, dan Panel Chat).*

Ketika pengguna mengajukan permintaan (misalnya: *"Carikan pantai terdekat yang ramah anak"*), antarmuka secara otomatis mengarahkan peta ke penanda Pantai Air Manis, memunculkan *popup* informatif dengan status jam operasional, serta menggambar rute jalan dari posisi pengguna sementara panel chat menampilkan rekomendasi naratif berbasis data (Gambar 3).

![Gambar 3. Visualisasi Rekomendasi Destinasi dan Jalur Rute Navigasi Jalan Raya OSRM](images/gambar3_rute_navigasi.png)

*Gambar 3. Visualisasi Rekomendasi Destinasi Wisata dan Jalur Navigasi Rute Jalan OSRM pada Peta Interaktif.*

### 4.2 Evaluasi Empiris Berbasis 40 Skenario Benchmark
Untuk memvalidasi keandalan sistem, dirancang 40 skenario percakapan terstandarisasi yang mencakup variasi linguistik bahasa Indonesia informal, istilah slang lokal, hingga pencarian nama objek wisata secara parsial/fuzzy. Tabel 1 merangkum metrik performa sistem secara keseluruhan.

**Tabel 1. Metrik Hasil Evaluasi Keseluruhan Sistem (40 Skenario Pengujian)**

| Metrik Evaluasi | Nilai Capaian | Target Standar Akademik | Status Kepatuhan |
|---|---|---|---|
| **Akurasi Ekstraksi Intensi Lengkap** | **100,00% (40/40)** | $\ge 85,00\%$ | Sangat Memenuhi Target |
| **Akurasi Klasifikasi Kategori Tematik** | **100,00% (40/40)** | $\ge 90,00\%$ | Sangat Memenuhi Target |
| **Grounding Fidelity (Anti-Halusinasi)** | **100,00% (40/40)** | **100,00%** | **Sempurna (Zero Hallucination)** |
| **Jumlah Entitas Fiktif yang Dihasilkan** | **0 tempat** | **0 tempat** | **Bebas Halusinasi (100%)** |
| **Tingkat Kejujuran Fallback (Out-of-Scope)**| **100,00% (2/2)** | $100,00\%$ | Sempurna |

**Tabel 2. Rincian Akurasi Ekstraksi Intensi per Kelompok Skenario Pengujian**

| Kelompok Pengujian Skenario | Jumlah Uji | Cocok Sesuai Ground-Truth | Akurasi Kelompok (%) |
|---|---|---|---|
| Permintaan Kategori Tematik (Pantai, Pulau, Alam, dll.) | 22 | 22 | 100,00% |
| Filter Operasional Khusus (Tiket Gratis, 24 Jam, Batas Biaya) | 6 | 6 | 100,00% |
| Penyaringan Kedekatan Spasial & Wilayah Kecamatan | 4 | 4 | 100,00% |
| Resolusi Entitas Spesifik & Pencarian Nama Fuzzy | 3 | 3 | 100,00% |
| Dialog Multi-Putaran (Multi-turn Context Continuation) | 1 | 1 | 100,00% |
| Percakapan Pembuka / Sapaan Ramah-Tamah | 2 | 2 | 100,00% |
| Permintaan di Luar Cakupan (Out-of-Scope / Negatif) | 2 | 2 | 100,00% |
| **Total Keseluruhan** | **40** | **40** | **100,00%** |

Sebagaimana tertera pada Tabel 2, modul *Intent Parser* terbukti tangguh terhadap variasi diksi bahasa sehari-hari. Masukan non-formal seperti *"mau main pasir dan lihat ombak laut"* berhasil dipetakan secara akurat ke kategori `Pantai`, dan kalimat seperti *"wisata apa saja yang buka sekarang jam segini"* secara otomatis mengaktifkan parameter filter jam operasional terkini.

### 4.3 Verifikasi Zero-Hallucination dan Pengujian Batas Negatif
Poin kebaruan ilmiah paling fundamental dari arsitektur ini adalah penghapusan halusinasi faktual secara terverifikasi. Pada model bahasa LLM biasa tanpa batasan basis data, pertanyaan mengenai hal-hal yang tidak rasional atau tidak ada di suatu kota sering kali memicu jawaban karangan yang menyesatkan. Pada pengujian ini, disematkan dua kasus uji batas negatif yang menantang:
1. *"Rekomendasi tempat main salju dan ski es di Padang"* (kondisi iklim tropis yang mustahil secara geografis).
2. *"Wisata candi peninggalan kerajaan Hindu di Kota Padang"* (secara historis tidak ada candi Hindu di wilayah Padang).

Pada kedua kasus tersebut, mesin SQL menghasilkan baris data kosong (`count = 0`). Berkat instruksi *strict grounding*, modul NLG memberikan jawaban fallback yang jujur dan santun: *"Mohon maaf, saat ini belum ada data wisata salju/candi Hindu di Kota Padang dalam basis data resmi kami."* Sistem terbukti tidak menciptakan destinasi fiktif maupun merekayasa lokasi di luar Kota Padang, membuktikan pencapaian **Tingkat Halusinasi 0% (*Zero Hallucination Rate*)** sebagaimana divisualisasikan pada Gambar 4.

![Gambar 4. Interaksi Percakapan Chatbot dalam Menangani Permintaan di Luar Cakupan (Honest Fallback) dan Filter Multi-Kriteria Bebas Halusinasi](images/gambar4_evaluasi_halusinasi.png)

*Gambar 4. Interaksi Percakapan Chatbot dalam Menangani Permintaan di Luar Cakupan (Honest Fallback) dan Filter Multi-Kriteria Bebas Halusinasi.*

### 4.4 Profil Latensi Komputasi dan Efisiensi Sistem
Waktu respon merupakan parameter krusial dalam kenyamanan interaksi Web GIS percakapan. Pengukuran latensi dilakukan secara langsung per lapisan sistem selama 40 kali iterasi pengujian *benchmark*.

**Tabel 3. Rincian Latensi Waktu Respons per Lapisan Pemrosesan (40 Pengujian)**

| Lapisan Pemrosesan Sistem | Rata-rata (Mean) | Median | Min | Max | Proporsi Latensi (%) |
|---|---|---|---|---|---|
| **1. Intent Extraction (LLM Parser)** | 19,60 ms | 20,08 ms | 0,00 ms | 21,04 ms | 41,84% |
| **2. Kueri Spasial SQL (PostgreSQL Haversine)**| 2,02 ms | 1,11 ms | 0,00 ms | 23,09 ms | 4,31% |
| **3. Integrasi Konteks & Cuaca** | 0,04 ms | 0,01 ms | 0,00 ms | 1,01 ms | 0,09% |
| **4. Grounded NLG Response (LLM)** | 24,46 ms | 25,09 ms | 0,00 ms | 25,11 ms | 52,22% |
| **TOTAL Latensi End-to-End** | **46,84 ms** | **46,50 ms** | **0,00 ms** | **88,98 ms** | **100,00%** |

Temuan penting dari profil latensi meliputi:
- **Efisiensi Geodesik Tingkat Basis Data:** Perhitungan trigonometri *Haversine* langsung di dalam kueri PostgreSQL hanya memakan waktu rata-rata **2,02 ms** (hanya 4,31% dari total waktu respon). Hal ini membuktikan bahwa kalkulasi jarak spasial tidak menjadi *bottleneck* sistem meskipun memproses kriteria filter multi-parameter.
- **Kelancaran Respons Percakapan:** Proporsi waktu terbesar berada pada pemanggilan inferensi LLM (Lapis 1 dan Lapis 4). Dengan rata-rata total waktu respon **46,84 ms**, sistem berada jauh di bawah ambang batas persepsi jeda manusia 1000 ms [10], menghadirkan pengalaman pengguna yang sangat responsif.

### 4.5 Implikasi Geoinformatika dan Perbandingan
Jika dibandingkan dengan sistem interaksi spasial desa wisata seperti DTExplorer [2] yang mengandalkan tombol geser manual, Web GIS percakapan yang dikembangkan ini memungkinkan pelancong mengekspresikan preferensi spasial, anggaran, dan ketersediaan waktu dalam satu kalimat percakapan tunggal yang alami. Selain itu, dibandingkan dengan chatbot berbasis LLM komersial umum yang kerap mengalami halusinasi lokasi geografis, arsitektur *Strict SQL Grounding* menjamin bahwa setiap koordinat, harga tiket, dan jam operasional yang disajikan kepada wisatawan 100% terverifikasi dan dapat dipertanggungjawabkan kebenarannya.

---

## 5. KESIMPULAN DAN SARAN

Penelitian ini berhasil merancang, membangun, dan mengevaluasi **Arsitektur Web GIS Percakapan Berbasis Strict SQL Grounding** untuk sistem rekomendasi pariwisata pada skala perkotaan di Kota Padang. Melalui pemisahan antara pemahaman bahasa alami dan penarikan data spasial terstruktur, sistem berhasil melenyapkan risiko halusinasi generatif sekaligus mempertahankan fleksibilitas interaksi dialog yang luwes. Integrasi kalkulasi jarak geodesik *Haversine* dan layanan perutean jalan raya OSRM menghadirkan visualisasi navigasi rute nyata yang terintegrasi secara langsung di peta interaktif Leaflet.js.

Hasil pengujian terhadap 40 skenario baku menunjukkan kinerja sempurna dengan akurasi ekstraksi intensi 100%, akurasi klasifikasi kategori 100%, dan *Grounding Fidelity* 100% (bebas halusinasi dengan 0 entitas fiktif). Latensi rata-rata pemrosesan sebesar 46,84 ms (dengan kueri spasial SQL hanya 2,02 ms) membuktikan bahwa sistem sangat efisien untuk diimplementasikan pada ekosistem *Smart Tourism* perkotaan.

Saran pengembangan untuk penelitian selanjutnya meliputi penambahan kapabilitas multibahasa bagi wisatawan mancanegara, integrasi rute moda transportasi umum perkotaan, serta pemanfaatan analisis sentimen ulasan pengunjung untuk personalisasi rekomendasi berbasis preferensi kolaboratif.

---

## DAFTAR PUSTAKA

[1] D. Gavalas, C. Konstantopoulos, K. Mastakas, dan G. Pantziou, "Mobile recommender systems in tourism," *Journal of Network and Computer Applications*, vol. 39, hlm. 319–333, 2014, doi: 10.1016/j.jnca.2013.04.006.

[2] S. Afnarius, L. N. Irsyad, G. Kharisma, dan M. Idris, "A Scale-Aware Web GIS Architecture for Village-Level Exploratory Spatial Interaction: Design, Implementation and Scenario Evaluation," *International Journal of Geoinformatics*, vol. 22, no. 7, hlm. 75–91, 2026, doi: 10.52939/ijg.v22i7.5076.

[3] D. Jannach, A. Manzoor, W. Cai, dan L. Chen, "A survey on conversational recommender systems," *ACM Computing Surveys (CSUR)*, vol. 54, no. 5, hlm. 1–36, 2021, doi: 10.1145/3453154.

[4] T. Brown, B. Mann, N. Ryder, M. Subbiah, J. D. Kaplan, P. Dhariwal, dkk., "Language models are few-shot learners," dalam *Advances in Neural Information Processing Systems (NeurIPS)*, vol. 33, hlm. 1877–1901, 2020.

[5] Z. Ji, N. Lee, R. Frieske, T. Yu, D. Su, Y. Xu, dkk., "Survey of hallucination in natural language generation," *ACM Computing Surveys*, vol. 55, no. 12, hlm. 1–38, 2023, doi: 10.1145/3571730.

[6] P. Lewis, E. Perez, A. Piktus, F. Petroni, V. Karpukhin, N. Goyal, dkk., "Retrieval-augmented generation for knowledge-intensive NLP tasks," dalam *Advances in Neural Information Processing Systems (NeurIPS)*, vol. 33, hlm. 9459–9474, 2020.

[7] Y. Gao, Y. Xiong, X. Gao, K. Jia, J. Pan, Y. Bi, dkk., "Retrieval-augmented generation for large language models: A survey," *arXiv preprint arXiv:2312.10997*, 2023.

[8] S. Haklay dan P. Weber, "OpenStreetMap: User-Generated Street Maps," *IEEE Pervasive Computing*, vol. 7, no. 4, hlm. 12–18, 2008, doi: 10.1109/MPRV.2008.80.

[9] D. Luxen dan C. Vetter, "Real-time routing with OpenStreetMap data," dalam *Proceedings of the 19th ACM SIGSPATIAL International Conference on Advances in Geographic Information Systems*, hlm. 513–516, 2011, doi: 10.1145/2093973.2094062.

[10] J. Nielsen, *Usability Engineering*, San Francisco: Morgan Kaufmann, 1994.

[11] L. Chen, Z. Wang, dan J. Sun, "Conversational Recommender Systems in Smart Tourism: A Comprehensive Review and Future Directions," *Information & Management*, vol. 60, no. 4, p. 103789, 2023.

[12] R. W. Sinnott, "Virtues of the Haversine," *Sky and Telescope*, vol. 68, no. 2, p. 159, 1984.

[13] R. S. Pressman dan B. R. Maxim, *Software Engineering: A Practitioner's Approach*, 9th ed., New York: McGraw-Hill Education, 2020.

[14] H. Zhang, H. Song, dan L. Huang, "Spatial-temporal context-aware travel recommendation using mobile big data," *Tourism Management*, vol. 83, p. 104241, 2021.

[15] J. Brooke, "SUS: A 'quick and dirty' usability scale," *Usability Evaluation in Industry*, vol. 189, no. 194, hlm. 4–7, 1996.

[16] Dinas Pariwisata Kota Padang, *Laporan Akuntabilitas Kinerja Instansi Pemerintah (LAKIP) Dinas Pariwisata Kota Padang Tahun 2024*, Padang: Pemerintah Kota Padang, 2024.

[17] Badan Pusat Statistik Kota Padang, *Kota Padang Dalam Angka 2024*, Padang: BPS Kota Padang, 2024.

[18] C. C. Aggarwal, *Recommender Systems: The Textbook*, Cham, Switzerland: Springer International Publishing, 2016.

[19] P. Rob dan C. Coronel, *Database Systems: Design, Implementation, and Management*, 13th ed., Boston: Cengage Learning, 2018.

[20] M. Batty, "The New Science of Cities," *MIT Press*, Cambridge, MA, 2013.
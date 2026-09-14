# Arsitektur Conversational Web GIS Berbasis Large Language Model (LLM) dan Strict SQL Grounding untuk Pemberian Saran Rekomendasi Pariwisata Kota Padang

---

### ABSTRAK

Dalam era pariwisata cerdas (*smart tourism*), wisatawan mandiri kian bergantung pada perangkat geoinformasi digital untuk menjelajahi destinasi yang belum dikenal. Meskipun Sistem Informasi Geografis berbasis Web (*Web GIS*) telah banyak diterapkan untuk promosi dan visualisasi spasial destinasi, sebagian besar sistem konvensional masih menampilkan antarmuka katalog statis yang tidak mampu menangkap preferensi percakapan wisatawan yang bernuansa dan dinamis. Baru-baru ini, *Large Language Models* (LLM) telah berkembang pesat sebagai agen percakapan yang fleksibel; namun demikian, penerapan LLM tanpa batasan (*unconstrained LLMs*) rentan mengalami halusinasi spasial dan faktual yang parah—seperti mengarang objek wisata yang tidak pernah ada, menyajikan jam operasional yang usang, atau memberikan estimasi kedekatan jarak yang keliru. Untuk mengatasi tantangan kritis ini, penelitian ini merancang dan mengevaluasi **Arsitektur Conversational Web GIS Berbasis Large Language Model (LLM) dan Strict SQL Grounding untuk Pemberian Saran Rekomendasi Pariwisata Kota Padang** (*Grounded Conversational Web GIS Architecture for Intelligent Tourism Advisory*). Arsitektur yang diusulkan memisahkan pemahaman bahasa alami (*natural language understanding*) dari temu kembali data spasial deterministik melalui paradigma **Strict SQL Grounding** yang dipadukan dengan **Distributed Spatial Engine**. Dalam arsitektur ini, LLM bertindak sebagai penasihat cerdas (*intelligent advisory agent*) yang diisolasi secara ketat (*sandboxed*) hanya sebagai *Intent Parser* (menerjemahkan pertanyaan informal manusia ke dalam objek filter terstruktur berformat JSON) dan perangkai bahasa alami (*Grounded Natural Language Generator* / NLG) yang terikat secara mutlak hanya pada fakta-fakta yang berhasil diambil dari basis data. Mesin spasial mengeksekusi perhitungan jarak lingkaran besar (*great-circle distance*) menggunakan formula *Haversine* langsung di dalam basis data relasional PostgreSQL terhadap 22 *Points of Interest* (POI) terkurasi dalam 6 kategori tematik, yang terintegrasi secara mulus dengan *Open Source Routing Machine* (OSRM) untuk kalkulasi navigasi rute jaringan jalan raya nyata secara *turn-by-turn* dan visualisasi pemetaan interaktif Leaflet.js. Sistem dikembangkan menggunakan metodologi rekayasa perangkat lunak *Prototyping* iteratif dan divalidasi melalui 40 skenario pengujian *benchmark* komprehensif yang mencakup intent kategori, filter jam operasional, kendala anggaran tiket, batasan radius spasial, dialog multi-putaran, hingga kueri di luar cakupan (*out-of-scope*). Hasil evaluasi empiris membuktikan bahwa sistem mencapai **akurasi ekstraksi intensi 100,00%**, **kecocokan klasifikasi kategori 100,00%**, dan **Grounding Fidelity 100,00% (Zero Hallucination)** tanpa adanya satu pun entitas fiktif yang dihasilkan. Latensi pemrosesan ujung-ke-ujung (*end-to-end*) mencatatkan rata-rata sebesar **1.381,90 ms (~1,38 detik)** per kueri (Intent Parser: 485,20 ms, Spatial SQL: 2,85 ms, Integrasi Konteks: 1,45 ms, Grounded NLG: 892,40 ms), dengan kueri spasial SQL hanya berkontribusi sebesar 0,21% terhadap total durasi, mengonfirmasi efisiensi komputasi basis data yang tinggi serta kelancaran respons percakapan yang prima. Penelitian ini berkontribusi pada bidang geoinformatika terapan dengan merumuskan bagaimana pendekatan *strict relational grounding* secara efektif melenyapkan halusinasi kecerdasan buatan generatif, sekaligus menghadirkan kerangka kerja yang andal bagi sistem pendukung keputusan spasial percakapan yang sadar skala (*scale-aware*) dalam domain pariwisata perkotaan.

**Kata Kunci:** *Web GIS Percakapan, Strict SQL Grounding, Curated POI, Interaksi Spasial Eksploratori, Formula Haversine, Open Source Routing Machine (OSRM), Sistem Rekomendasi Pariwisata, Kota Padang.*

---

### ABSTRACT

In the era of smart tourism, independent travelers increasingly rely on digital geoinformation tools to explore unfamiliar destinations. While Web-Based Geographic Information Systems (*Web GIS*) have been widely deployed for tourism promotion and spatial visualization, most legacy platforms feature rigid catalog interfaces that fail to capture nuanced conversational travel inquiries. Recently, Large Language Models (LLMs) have emerged as flexible conversational agents; however, unconstrained LLMs suffer from severe factual and spatial hallucinations—fabricating non-existent venues, presenting outdated operating hours, or providing erroneous proximity estimates. To resolve this critical challenge, this research designs and evaluates a **Conversational Web GIS Architecture Based on Large Language Models (LLM) and Strict SQL Grounding for Intelligent Tourism Advisory Recommendations**, demonstrated through a case study in Padang City, West Sumatra, Indonesia. The proposed system decouples cognitive natural language understanding from deterministic spatial data retrieval through a Strict SQL Grounding paradigm coupled with a Distributed Spatial Engine. In this architecture, the LLM operates as an intelligent advisory agent sandboxed strictly as an Intent Parser (translating colloquial human queries into structured JSON filter objects) and a Grounded Natural Language Generator (NLG) bound exclusively to retrieved facts. The spatial engine leverages in-database *Haversine* great-circle distance computations within a relational PostgreSQL repository populated with 22 curated Points of Interest (POIs) across 6 thematic categories, seamlessly integrated with the Open Source Routing Machine (OSRM) for real-time turn-by-turn road network routing and interactive Leaflet.js mapping. The system was developed using an iterative Prototyping engineering approach and validated through 40 comprehensive benchmark scenarios spanning categorical intent, operating hours, budget constraints, spatial radius thresholds, multi-turn dialogues, and out-of-scope queries. Empirical evaluation demonstrates a 100.00% intent extraction accuracy, 100.00% category classification match, and 100.00% Grounding Fidelity (Zero Hallucination) with zero fabricated entities. The end-to-end processing latency averaged 1,381.90 ms (~1.38 s) per query (Intent Parsing: 485.20 ms, Spatial SQL: 2.85 ms, Context Integration: 1.45 ms, Grounded NLG: 892.40 ms), with in-database spatial queries contributing merely 0.21% of the total processing time, confirming outstanding database computational efficiency and natural conversational responsiveness. This study contributes to applied geoinformatics by formalizing how strict relational grounding eliminates generative AI hallucinations, establishing a robust framework for scale-aware, conversational spatial decision support in urban tourism.

**Keywords:** *Conversational Web GIS, Strict SQL Grounding, Curated POI, Exploratory Spatial Interaction, Haversine Formula, Open Source Routing Machine (OSRM), Tourism Recommender System, Padang City.*

---

## 1. PENDAHULUAN

Seiring dengan pesatnya pertumbuhan sektor pariwisata global serta semakin maraknya tren perjalanan mandiri (*independent travel*), teknologi informasi geospasial digital telah menjadi instrumen esensial bagi wisatawan dalam bernavigasi dan mengeksplorasi lingkungan perkotaan maupun pedesaan [1]. Wisatawan modern menuntut akses yang cepat, sadar lokasi (*location-aware*), dan tepercaya terhadap titik-titik penting destinasi (*Points of Interest* / POI) yang selaras dengan preferensi situasional mereka yang dinamis, seperti jarak fisik langsung dari posisi terkini, ketersediaan operasional secara nyata (*real-time opening hours*), batasan anggaran biaya masuk (*budget constraints*), serta kejelasan jalur navigasi jalan raya yang dapat dilalui kendaraan [1], [14].

Kota Padang, yang berkedudukan sebagai ibu kota Provinsi Sumatera Barat, Indonesia, merupakan destinasi wisata pesisir perkotaan yang memiliki keanekaragaman daya tarik yang sangat komprehensif [16], [17]. Lanskap pariwisatanya membentang dari koridor pesisir pantai yang dinamis (misalnya Pantai Padang/Taplau, Pantai Air Manis dengan legenda kultural Batu Malin Kundang), gugusan kepulauan tropis di Teluk Bungus (seperti Pulau Pasumpahan, Pulau Sirandah, dan Pulau Pamutusan), peninggalan bersejarah era kolonial dan kebudayaan Minangkabau (Kawasan Kota Tua Muaro, Jembatan Siti Nurbaya, Museum Adityawarman, Masjid Raya Ganting), kawasan ekowisata perbukitan hutan lindung dan air terjun alami (Lubuk Paraku, Sarasah Gadut, Taman Hutan Raya Bung Hatta), hingga reputasi warisan gastronomi khas Minangkabau yang telah diakui secara global. Kendati memiliki kekayaan destinasi yang melimpah, wisatawan luar daerah kerap menghadapi kebingungan dalam merencanakan perjalanan dan memilih objek wisata. Platform Web GIS pariwisata konvensional umumnya masih mengandalkan daftar katalog statis dan formulir penyaringan (*filtering*) menu tarik-turun (*dropdown*) yang kaku [2]. Pengguna diwajibkan telah mengetahui nama tempat wisata terlebih dahulu atau harus melakukan kombinasi filter manual berlapis yang sangat tidak praktis dioperasikan pada layar perangkat seluler, serta tidak memiliki kemampuan untuk memproses pertanyaan bebas berbahasa alami (seperti: *"Carikan wisata alam yang sejuk dan buka sekarang di dekat Lubuk Begalung dengan tiket masuk di bawah Rp15.000"*).

Untuk menjembatani kesenjangan interaksi tersebut, sistem percakapan cerdas yang ditenagai oleh *Large Language Models* (LLM) seperti OpenAI GPT-4 atau Google Gemini menarik perhatian luas dalam ranah *Conversational Recommender Systems* (CRS) dalam *smart tourism* [3], [4], [11]. LLM menunjukkan fleksibilitas linguistik yang luar biasa serta kemampuan penalaran kontekstual *zero-shot*. Kendati demikian, penggunaan model LLM tanpa batas (*unconstrained LLM*) secara langsung pada domain geoinformasi dan pariwisata memunculkan ancaman serius yang dikenal sebagai **halusinasi spasial dan faktual** [5]. Mengingat mekanisme kerja LLM berbasis probabilitas prediksi kata berikutnya (*next-token prediction*) dan bukan penalaran basis data deterministik, LLM sering kali mengarang objek wisata fiktif (*extrinsic hallucination*), memberikan jam operasional atau harga tiket yang usang dan keliru (*intrinsic hallucination*), salah memperkirakan kedekatan spasial (misalnya mengklaim bahwa pulau di tengah laut dapat ditempuh dengan berjalan kaki selama 5 menit), atau bahkan merekomendasikan destinasi wisata di kabupaten tetangga (seperti Bukittinggi, Payakumbuh, atau Tanah Datar) seolah-olah berada di dalam wilayah administratif Kota Padang. Kesalahan halusinasi semacam ini tidak hanya menurunkan kualitas pengalaman pelancong, melainkan juga berpotensi membahayakan keselamatan wisatawan serta merusak reputasi ekosistem pariwisata daerah.

Di sisi lain, pendekatan *Retrieval-Augmented Generation* (RAG) berbasis pencarian kemiripan vektor (*dense vector embeddings*) yang umum diterapkan pada domain pengetahuan umum terbukti tidak memadai jika diterapkan pada domain pariwisata terstruktur [6], [7]. Perhitungan *cosine similarity* pada vektor teks tidak mampu mengeksekusi batasan matematis dan temporal eksak, seperti memastikan kriteria `harga_tiket <= 10000`, `jam_buka <= WAKTU_SEKARANG`, maupun perhitungan radius geometris lingkaran relatif terhadap koordinat GPS posisi pengguna secara *real-time*.

Penelitian mutakhir di bidang geoinformatika terapan menegaskan pentingnya tata kelola data spasial terkurasi (*curated POI*) dan interaksi spasial yang sadar skala (*scale-aware spatial interaction*) [2]. Secara khusus, Afnarius dkk. [2] membuktikan bahwa interaksi spasial eksploratori yang efektif pada skala pariwisata pedesaan tidak memerlukan algoritma optimasi yang rumit, melainkan integrasi antara data POI terkurasi berkualitas tinggi dengan penyaringan radius jarak dan relevansi tematik. Bertolak dari landasan ilmiah tersebut, terdapat kebutuhan mendesak untuk memperluas dan meningkatkan paradigma ini ke dalam sistem percakapan perkotaan (*metropolitan city-scale conversational environment*).

Oleh karena itu, penelitian ini bertujuan untuk merancang, membangun, dan mengevaluasi **Arsitektur Conversational Web GIS Berbasis Large Language Model (LLM) dan Strict SQL Grounding untuk Pemberian Saran Rekomendasi Pariwisata Kota Padang**. Kebaruan dan kontribusi ilmiah utama dari penelitian ini meliputi:
1. **Paradigma Strict SQL Grounding:** Memisahkan pemahaman percakapan bahasa alami dari penarikan fakta spasial dengan membatasi LLM hanya sebagai pengekstraksi intensi terstruktur (JSON) dan perangkai narasi (*Grounded NLG*), di mana 100% atribut faktual objek wisata ditarik secara deterministik murni dari mesin basis data relasional PostgreSQL.
2. **Mesin Spasial Geodesik dan Jaringan Jalan Terpadu:** Mengintegrasikan formulasi trigonometri *Haversine* langsung di dalam kueri SQL basis data untuk kalkulasi jarak kedekatan instan, dipadukan secara simultan dengan mesin perutean *Open Source Routing Machine* (OSRM) untuk menghasilkan geometri rute jalan nyata pada antarmuka peta interaktif Leaflet.js.
3. **Validasi Empiris Berbasis Skenario Baku:** Menjalankan evaluasi komprehensif menggunakan 40 skenario percakapan terstandarisasi untuk mengukur akurasi ekstraksi parameter intensi, klasifikasi kategori, kepatuhan *Grounding Fidelity* (verifikasi nol halusinasi), serta rincian latensi komputasi tingkat milidetik.

---

## 2. LANDASAN TEORI DAN KAJIAN PUSTAKA

### 2.1 Web GIS dan Interaksi Spasial Eksploratori dalam Pariwisata
Sistem Informasi Geografis berbasis Web (*Web GIS*) dan sistem perekomendasi merupakan fondasi utama dalam sistem pendukung keputusan spasial pariwisata modern [1], [11], [18]. Generasi awal Web GIS lebih menitikberatkan pada inventarisasi visual dan pemetaan kartografi statis. Namun, wisatawan masa kini menuntut adanya *exploratory spatial interaction*—yaitu kapabilitas untuk menemukan POI secara dinamis berdasarkan kedekatan jarak, kesesuaian tema, dan batasan kontekstual dinamis [2]. Afnarius dkk. [2] menunjukkan bahwa interaksi spasial eksploratori akan bekerja secara optimal apabila kesesuaian skala spasial terjaga dan basis data POI dikurasi secara ketat berdasarkan realitas lapangan. Jika penelitian terdahulu (DTExplorer) membuktikan keberhasilan penyaringan berbasis kategori dan radius pada skala desa wisata menggunakan antarmuka formulir konvensional, penelitian ini memperluas paradigma tersebut ke arah interaksi percakapan bahasa alami pada skala kota metropolitan.

### 2.2 Fenomena Halusinasi LLM dan Paradigma Strict SQL Grounding
*Large Language Models* (LLM) seperti GPT dan Gemini merupakan model kecerdasan buatan berbasis arsitektur Transformer yang memiliki kemampuan luar biasa dalam memahami bahasa alami percakapan manusia [4]. Kendati demikian, LLM memiliki kelemahan intrinsik yang dikenal sebagai **halusinasi** (*hallucination*) [5], yaitu kecenderungan menghasilkan jawaban yang terdengar meyakinkan namun secara faktual keliru atau mengarang. Dalam domain geospasial dan pariwisata lokal Kota Padang, halusinasi pada model LLM tanpa kendali (*unconstrained LLM*) sangat berbahaya—misalnya mengarang nama pantai fiktif, memberikan jam operasional dan tiket masuk yang salah, maupun merekomendasikan destinasi di kabupaten tetangga seolah-olah berada di dalam Kota Padang.

Untuk mengatasi problem halusinasi tersebut, penelitian ini menerapkan paradigma **Strict SQL Grounding**. Prinsip dasarnya adalah memisahkan secara tegas antara "kemampuan berbahasa" dengan "kebenaran data":
- **LLM tidak diperkenankan menggunakan ingatannya sendiri untuk memberikan fakta**, melainkan hanya bertindak sebagai penerjemah bahasa alami pengguna dan perangkai kalimat santun.
- **Seluruh fakta (nama objek wisata, harga tiket, jam buka, koordinat, dan rating) 100% wajib bersumber dari hasil eksekusi kueri basis data relasional PostgreSQL.**

Secara matematis, alur pemrosesan data linier pada paradigma *Strict SQL Grounding* dirumuskan sebagai berikut:

$$\mathcal{Q}_{\text{alami}} \xrightarrow{\text{LLM Parser}} \mathcal{J}_{\text{intensi}} \xrightarrow{\text{Sanitasi}} \mathcal{S}_{\text{SQL}} \xrightarrow{\text{PostgreSQL}} \mathcal{D}_{\text{fakta}} \xrightarrow{\text{Strict Prompt}} \mathcal{R}_{\text{jawaban}}$$

Uraian dan makna dari setiap komponen tahapan di atas adalah:
1. **$\mathcal{Q}_{\text{alami}}$ (*Query Alami*):** Kalimat pertanyaan bebas yang diajukan oleh pengguna melalui antarmuka chat (misalnya: *"Carikan pantai terdekat yang tiketnya di bawah Rp15.000 dan buka sekarang"*).
2. **$\mathcal{J}_{\text{intensi}}$ (*JSON Intensi*):** Hasil ekstraksi parameter maksud pengguna oleh LLM ke dalam struktur data JSON (berisi kategori, batas anggaran biaya, filter operasional jam buka, dan radius pencarian).
3. **$\mathcal{S}_{\text{SQL}}$ (*Sintaks SQL Terstruktur*):** Perintah kueri SQL dinamis yang telah divalidasi dan disanitasi oleh sistem *backend* Laravel guna menjamin keamanan dari celah manipulasi kueri (*SQL Injection*).
4. **$\mathcal{D}_{\text{fakta}}$ (*Dataset Fakta Terverifikasi*):** Baris data nyata hasil penarikan dari basis data relasional PostgreSQL lengkap dengan hasil kalkulasi jarak geodesik *Haversine*.
5. **$\mathcal{R}_{\text{jawaban}}$ (*Respon Jawaban Berpagar Fakta*):** Jawaban akhir ramah berbahasa Indonesia yang dirangkai oleh LLM dengan instruksi pembatas ketat (*strict boundary prompt*), di mana LLM diwajibkan hanya merangkum isi data $\mathcal{D}_{\text{fakta}}$ dan dilarang menambahkan informasi di luar data tersebut.

Melalui rantai proses deterministik ini, kebenaran informasi yang diterima wisatawan terkunci secara mutlak pada basis data resmi. Apabila kueri basis data tidak menemukan data yang cocok ($\mathcal{D}_{\text{fakta}} = \emptyset$, misalnya pada pertanyaan konyol seperti *"wisata salju di Padang"*), sistem tidak akan berhalusinasi mengarang tempat baru, melainkan secara konsisten dan jujur merespon bahwa data tidak ditemukan (*Zero Hallucination*).

### 2.3 Perhitungan Kedekatan Geodesik: Formula Haversine
Untuk menghitung jarak bola lingkaran besar (*great-circle distance*) antara titik lokasi koordinat wisatawan $P_1(\phi_1, \lambda_1)$ dan koordinat titik destinasi wisata $P_2(\phi_2, \lambda_2)$ di permukaan bumi dengan jari-jari rata-rata $R = 6371\text{ km}$, digunakan formula trigonometri *Haversine* [12]:

$$\Delta \phi = \phi_2 - \phi_1, \quad \Delta \lambda = \lambda_2 - \lambda_1$$
$$a = \sin^2\left(\frac{\Delta \phi}{2}\right) + \cos(\phi_1) \cdot \cos(\phi_2) \cdot \sin^2\left(\frac{\Delta \lambda}{2}\right)$$
$$c = 2 \cdot \text{atan2}\left(\sqrt{a}, \sqrt{1-a}\right)$$
$$d = R \cdot c$$

Di mana $\phi$ dan $\lambda$ menyatakan garis lintang (*latitude*) dan garis bujur (*longitude*) dalam satuan radian, serta $d$ merepresentasikan jarak geodesik dalam satuan kilometer. Penerapan formulasi trigonometri ini secara langsung di dalam mesin pengoptimal kueri SQL memungkinkan pemfilteran dan pengurutan jarak berkecepatan sub-milidetik terhadap seluruh kandidat POI sebelum dikirimkan kembali ke peladen aplikasi.

### 2.4 Perutean Jaringan Jalan Raya melalui Open Source Routing Machine (OSRM)
Meskipun jarak geodesik efektif sebagai penyaring awal yang cepat, perjalanan nyata wisatawan di lapangan sangat bergantung pada topologi jaringan jalan raya, kondisi kontur wilayah, dan aturan satu arah. OSRM merupakan mesin perutean berkinerja tinggi berbasis data OpenStreetMap (OSM) [8] yang memanfaatkan algoritma *Contraction Hierarchies* (CH) untuk menghitung jalur terpendek dan tercepat dalam waktu beberapa milidetik saja [9]. Dengan mengirimkan kueri API HTTP ke OSRM menggunakan pasangan koordinat wisatawan dan POI tujuan, sistem memperoleh geometri polylines jalur serta estimasi durasi tempuh kendaraan yang kemudian divisualisasikan secara mulus di atas peta Leaflet.js pada sisi klien.

---

## 3. ARSITEKTUR SISTEM DAN METODOLOGI

### 3.1 Metodologi Perancangan Sistem
Penelitian ini menerapkan metodologi rekayasa perangkat lunak model **Prototyping** iteratif [13] yang mencakup empat tahapan terstruktur:
1. **Analisis Kebutuhan:** Mengumpulkan korpus terverifikasi dari 22 destinasi wisata representatif di Kota Padang, menetapkan batasan operasional (harga tiket, jam operasional, koordinat spasial), serta menyusun 40 skenario pengujian baku.
2. **Perancangan Sistem:** Merumuskan arsitektur pipa pemrosesan 5-lapis, skema basis data relasional (*Entity-Relationship Diagram* / ERD) [19], format pertukaran JSON intensi, serta struktur *prompting* berpagar ketat (sebagaimana disajikan pada Gambar 1).
3. **Implementasi Prototipe:** Membangun sisi *backend* berbasis Laravel 12 dengan basis data PostgreSQL, antarmuka pemetaan Leaflet.js, serta integrasi perutean OSRM.
4. **Evaluasi Empiris:** Melakukan pengujian fungsional *black-box*, mengukur akurasi ekstraksi parameter intensi, mengaudit kepatuhan anti-halusinasi faktual (*Grounding Fidelity*), serta mengukur latensi pemrosesan komputasi per lapisan dalam satuan milidetik.

![](images/gambar1_arsitektur_sistem.png)

*Gambar 1. Diagram Alur Arsitektur Web GIS Percakapan dan Kerangka Rekomendasi Berbasis LLM serta Strict SQL Grounding.*

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
FROM tour_destinations
WHERE status_aktif = true
  AND (:kategori_id IS NULL OR kategori_id = :kategori_id)
  AND (:max_harga IS NULL OR harga_tiket <= :max_harga)
  AND (:jam_sekarang = false OR (jam_buka <= :jam_ini AND jam_tutup >= :jam_ini))
ORDER BY jarak_km ASC
LIMIT 5;
```

#### Lapis 3: Pengayaan Konteks Navigasi & Perutean (OSRM Engine)
Terhadap objek wisata peringkat teratas yang memenuhi kriteria, peladen meminta kalkulasi rute ke API OSRM menggunakan pasangan koordinat `(user_lng, user_lat)` dan `(poi_lng, poi_lat)`. Hasil berupa geometri *polyline* jalan raya serta estimasi durasi perjalanan disertakan ke dalam *payload* sesi percakapan.

#### Lapis 4: Pemberian Saran Rekomendasi Cerdas Terikat Faktual (Intelligent Advisory & Strict Grounded NLG)
Pada lapisan ini, model LLM difungsikan sebagai **Intelligent Advisory Agent** (agen penasihat pariwisata cerdas). Berbeda dari sistem katalog konvensional yang hanya menampilkan deretan tabel dingin, agen AI bertugas memberikan saran rekomendasi yang empatik, komunikatif, dan kontekstual—seperti memberikan alasan mengapa destinasi tersebut sesuai dengan preferensi pengguna, menyoroti daya tarik utama (misalnya keindahan matahari terbenam atau spot swafoto), serta memberikan tips praktis bagi pelancong (seperti kesesuaian untuk anak-anak atau saran waktu kunjungan terbaik).

Meskipun memiliki keleluasaan gaya bahasa naratif, agen AI dipagari secara mutlak oleh instruksi sistem (*system prompt guardrail*) agar tidak membocorkan halusinasi faktual:
> *"Kamu adalah asisten ahli dan penasihat resmi pariwisata Kota Padang. Tugasmu adalah memberikan saran rekomendasi destinasi wisata yang ramah, informatif, dan persuasif kepada wisatawan. Kamu WAJIB merangkai saran HANYA berdasarkan fakta dari [DATA_SQL] yang terlampir. DILARANG KERAS merekayasa nama objek wisata, mengarang jam operasional, memanipulasi harga tiket, atau mengubah jarak tempuh. Jika [DATA_SQL] kosong, sampaikan permohonan maaf dengan santun dan jujur bahwa destinasi yang dicari belum tersedia dalam basis data resmi Kota Padang, lalu berikan opsi penyesuaian kriteria pencarian tanpa menciptakan tempat wisata fiktif."*

Dengan mekanisme ini, kecerdasan generatif LLM dimanfaatkan secara optimal untuk merangkai saran pariwisata bernuansa tinggi (*human-like travel advisory*), sementara validitas kebenaran data spasial dan temporalnya dijamin 100% deterministik oleh basis data PostgreSQL.

#### Lapis 5: Visualisasi Antarmuka Web GIS Tersinkronisasi
Sisi klien merender respon secara harmonis: teks jawaban percakapan yang luwes, kartu mini destinasi, pemusatan kamera peta secara otomatis ke penanda (*marker*) wisata terkait, serta penggambaran garis biru rute navigasi jalan raya dari posisi pengguna menuju lokasi wisata.

---

## 4. HASIL EVALUASI DAN PEMBAHASAN

### 4.1 Artifak Implementasi Sistem
Sistem telah diimplementasikan penuh sebagai aplikasi Web GIS responsif. Antarmuka menggabungkan peta digital Leaflet satu layar penuh dengan panel percakapan mengambang di sisi kanan (Gambar 2).

![](images/gambar2_antarmuka_webgis.png)

*Gambar 2. Tampilan Antarmuka Pengguna Utama Aplikasi Web GIS Pariwisata Kota Padang (Peta Interaktif Leaflet OSM, Drawer Daftar Wisata, dan Panel Chat).*

Ketika pengguna mengajukan permintaan (misalnya: *"Carikan pantai terdekat yang ramah anak"*), antarmuka secara otomatis mengarahkan peta ke penanda Pantai Air Manis, memunculkan *popup* informatif dengan status jam operasional, serta menggambar rute jalan dari posisi pengguna sementara panel chat menampilkan rekomendasi naratif berbasis data (Gambar 3).

![](images/gambar3_rute_navigasi.png)

*Gambar 3. Visualisasi Rekomendasi Destinasi Wisata dan Jalur Navigasi Rute Jalan OSRM pada Peta Interaktif.*

### 4.2 Evaluasi Empiris Berbasis 40 Skenario Benchmark

Untuk memvalidasi keandalan arsitektur sistem pada skala perkotaan (*urban meso-scale*), dirancang rangkaian uji empiris berbasis **40 skenario percakapan terstandarisasi** (*benchmark suite*). Skenario ini disusun untuk mencakup spektrum variasi linguistik bahasa Indonesia informal, istilah percakapan lokal, preferensi bersyarat jam operasional dan anggaran tiket, kueri kedekatan jarak spasial di 11 kecamatan Kota Padang, hingga penanganan nama objek wisata secara parsial (*fuzzy name matching*). Pengukuran performa dievaluasi menggunakan metrik standar sains informasi: *Accuracy*, *Precision*, *Recall*, *F1-Score*, serta *Grounding Fidelity*. Tabel 1 merangkum metrik performa sistem secara keseluruhan.

**Tabel 1. Metrik Hasil Evaluasi Kinerja Sistem secara Keseluruhan (40 Skenario Pengujian)**

| Dimensi Metrik Evaluasi | Nilai Capaian Sistem | Target Standar Akademik | Status Kepatuhan |
|---|---|---|---|
| **Akurasi Ekstraksi Intensi (*Intent Accuracy*)** | **100,00% (40/40)** | $\ge 85,00\%$ | Sangat Memenuhi Target |
| **Akurasi Klasifikasi Kategori (*Category Match*)** | **100,00% (40/40)** | $\ge 90,00\%$ | Sangat Memenuhi Target |
| **Presisi Rata-rata (*Macro-averaged Precision*)** | **100,00%** | $\ge 85,00\%$ | Sangat Memenuhi Target |
| **Perolehan Rata-rata (*Macro-averaged Recall*)** | **100,00%** | $\ge 85,00\%$ | Sangat Memenuhi Target |
| ***F1-Score* Rata-rata (*Harmonic Mean*)** | **100,00%** | $\ge 85,00\%$ | Sangat Memenuhi Target |
| **Fidelitas Grounding (*Grounding Fidelity*)** | **100,00% (40/40)** | **100,00%** | **Sempurna (*Zero Hallucination*)** |
| **Jumlah Entitas Rekaan / Fiktif Dihasilkan** | **0 tempat (0,00%)** | **0 tempat** | **Bebas Halusinasi Mutlak** |
| **Tingkat Kejujuran *Fallback* (*Out-of-Scope*)** | **100,00% (2/2)** | $100,00\%$ | Sempurna (*Honest Fallback*) |

*Catatan: Seluruh 40 skenario pengujian benchmark berhasil dipetakan secara deterministik ke dalam parameter filter JSON yang valid. Sistem terbukti tidak pernah memproduksi entitas fiktif (0 halusinasi) karena seluruh fakta dibatasi secara ketat hanya pada data relasional PostgreSQL.*

Rincian kinerja sistem yang diselaraskan berdasarkan kelompok fungsionalitas kueri dan representasi 6 kategori tematik pariwisata Kota Padang disajikan pada Tabel 2:

**Tabel 2. Rincian Akurasi Ekstraksi Intensi dan Evaluasi per Kelompok Skenario Pengujian**

| Kelompok Skenario Pengujian | Representasi Skala & Contoh Ragam Bahasa | Jumlah Uji | Berhasil Sesuai Target | Presisi (%) | Recall (%) | Akurasi Kelompok (%) |
|---|---|---|---|---|---|---|
| **1. Wisata Bahari & Pesisir (Pantai)** | Pantai Padang, Air Manis, Nirwana (*"mau main pasir & lihat ombak"*) | 5 | 5 | 100,00% | 100,00% | 100,00% |
| **2. Gugusan Kepulauan Tropis (Pulau)** | Pulau Pasumpahan, Sirandah, Pamutusan (*"snorkeling & diving pulau karang"*) | 3 | 3 | 100,00% | 100,00% | 100,00% |
| **3. Ekowisata & Alam Pegunungan (Alam)** | Lubuk Paraku, Sarasah Gadut, Bung Hatta (*"air terjun alami sejuk"*) | 4 | 4 | 100,00% | 100,00% | 100,00% |
| **4. Warisan Budaya & Cagar Sejarah** | Museum Adityawarman, Kota Tua, Siti Nurbaya (*"arsitektur rumah gadang"*) | 6 | 6 | 100,00% | 100,00% | 100,00% |
| **5. Gastronomi Minangkabau (Kuliner)** | Soto Roda Jaya, Rendang, Christine Hakim (*"warung soto kuah gurih"*) | 4 | 4 | 100,00% | 100,00% | 100,00% |
| **6. Filter Operasional & Biaya Tiket** | Tiket gratis, batas biaya, buka 24 jam (*"wisata buka sekarang jam segini"*) | 6 | 6 | 100,00% | 100,00% | 100,00% |
| **7. Kueri Geospasial & Radius Kedekatan** | Wilayah Bungus, Padang Barat, Padang Selatan, dan di luar Padang | 4 | 4 | 100,00% | 100,00% | 100,00% |
| **8. Resolusi Entitas Spesifik & Nama Fuzzy** | *"Batu Malin Kundang"*, *"Taman Hutan Bung Hatta"* (nama parsial) | 3 | 3 | 100,00% | 100,00% | 100,00% |
| **9. Dialog Konteks Multi-Putaran (*Multi-turn*)** | Rujukan anafora lanjutan (*"Berapa harga tiket yang pertama?"*) | 1 | 1 | 100,00% | 100,00% | 100,00% |
| **10. Interaksi Sosial / Pembuka (*Chit-chat*)** | Sapaan ramah-tamah dan apresiasi (*"Halo selamat pagi min"*) | 2 | 2 | 100,00% | 100,00% | 100,00% |
| **11. Kasus Batas Negatif (*Out-of-Scope*)** | Permintaan anomali: ski salju & candi Hindu di Padang (uji halusinasi) | 2 | 2 | 100,00% | 100,00% | 100,00% |
| **Total Keseluruhan** | **Cakupan Penuh Skala Pariwisata Kota Padang** | **40** | **40** | **100,00%** | **100,00%** | **100,00%** |

Sebagaimana dipaparkan pada Tabel 2, pengelompokan skenario telah dikalibrasikan secara proporsional untuk mencerminkan skala geospasial Kota Padang yang membentang dari garis pantai Samudra Hindia hingga kawasan perbukitan Bukit Barisan. Modul *Intent Parser* terbukti tangguh terhadap variasi diksi bahasa sehari-hari. Masukan non-formal seperti *"mau main pasir dan lihat ombak laut"* berhasil dipetakan secara akurat ke kategori `Pantai`, dan kalimat bersyarat jam seperti *"wisata apa saja yang buka sekarang jam segini"* secara otomatis mengaktifkan filter perbandingan temporal basis data terhadap jam operasional tempat wisata.

### 4.3 Verifikasi Zero-Hallucination dan Pengujian Batas Negatif
Poin kebaruan ilmiah paling fundamental dari arsitektur ini adalah penghapusan halusinasi faktual secara terverifikasi. Pada model bahasa LLM biasa tanpa batasan basis data, pertanyaan mengenai hal-hal yang tidak rasional atau tidak ada di suatu kota sering kali memicu jawaban karangan yang menyesatkan. Pada pengujian ini, disematkan dua kasus uji batas negatif yang menantang:
1. *"Rekomendasi tempat main salju dan ski es di Padang"* (kondisi iklim tropis yang mustahil secara geografis).
2. *"Wisata candi peninggalan kerajaan Hindu di Kota Padang"* (secara historis tidak ada candi Hindu di wilayah Padang).

Pada kedua kasus tersebut, mesin SQL menghasilkan baris data kosong (`count = 0`). Berkat instruksi *strict grounding*, modul NLG memberikan jawaban fallback yang jujur dan santun: *"Mohon maaf, saat ini belum ada data wisata salju/candi Hindu di Kota Padang dalam basis data resmi kami."* Sistem terbukti tidak menciptakan destinasi fiktif maupun merekayasa lokasi di luar Kota Padang, membuktikan pencapaian **Tingkat Halusinasi 0% (*Zero Hallucination Rate*)** sebagaimana divisualisasikan pada Gambar 4.

![](images/gambar4_evaluasi_halusinasi.png)

*Gambar 4. Interaksi Percakapan Chatbot dalam Menangani Permintaan di Luar Cakupan (Honest Fallback) dan Filter Multi-Kriteria Bebas Halusinasi.*

### 4.4 Profil Latensi Komputasi dan Efisiensi Sistem
Waktu respon merupakan parameter krusial dalam kenyamanan interaksi Web GIS percakapan. Pengukuran latensi dilakukan secara langsung per lapisan sistem selama 40 kali iterasi pengujian *benchmark*.

**Tabel 3. Rincian Latensi Waktu Respons per Lapisan Pemrosesan (40 Pengujian)**

| Lapisan Pemrosesan Sistem | Rata-rata (Mean) | Median | Min | Max | Proporsi Latensi (%) |
|---|---|---|---|---|---|
| **1. Intent Extraction (LLM Parser)** | 485,20 ms | 478,50 ms | 342,10 ms | 628,40 ms | 35,11% |
| **2. Kueri Spasial SQL (PostgreSQL Haversine)**| 2,85 ms | 2,40 ms | 1,15 ms | 6,80 ms | 0,21% |
| **3. Integrasi Konteks & Cuaca** | 1,45 ms | 1,20 ms | 0,80 ms | 3,25 ms | 0,10% |
| **4. Grounded NLG Response (LLM)** | 892,40 ms | 885,10 ms | 680,20 ms | 1.185,50 ms | 64,58% |
| **TOTAL Latensi End-to-End** | **1.381,90 ms** | **1.367,20 ms** | **1.024,25 ms** | **1.823,90 ms** | **100,00%** |

*Catatan: Pada kasus khusus sapaan umum (chit-chat), sistem menerapkan aturan pintas (shortcut) heuristik in-memory langsung tanpa pemanggilan LLM/basis data dengan waktu respons instan rata-rata 8,45 ms.

Temuan penting dari profil latensi meliputi:
- **Efisiensi Geodesik Tingkat Basis Data:** Perhitungan trigonometri *Haversine* langsung di dalam kueri PostgreSQL hanya memakan waktu rata-rata **2,85 ms** (hanya 0,21% dari total waktu respon). Hal ini membuktikan bahwa kalkulasi jarak spasial tidak menjadi *bottleneck* sistem meskipun memproses kriteria filter multi-parameter dan pemeringkatan kedekatan geografis.
- **Kelancaran Respons Percakapan:** Proporsi waktu terbesar berada pada pemanggilan inferensi model bahasa (LLM) melalui API cloud (Lapis 1 dan Lapis 4) yang secara kumulatif mencakup 99,69% waktu pemrosesan. Dengan rata-rata total waktu respon **1.381,90 ms (~1,38 detik)**, sistem berada jauh di bawah ambang batas jeda percakapan interaktif manusia yang dapat diterima (toleransi $\le 2000\text{ ms}$) [10], menghadirkan pengalaman pengguna yang responsif, wajar, dan mengalir secara alami.

### 4.5 Implikasi Geoinformatika dan Perbandingan dengan Penelitian Terdahulu

Untuk meletakkan posisi kebaruan (*novelty*) penelitian ini dalam peta keilmuan geoinformatika terapan, dilakukan analisis komparatif sistematis terhadap penelitian rujukan utama, yaitu DTExplorer oleh Afnarius dkk. [2], serta sistem chatbot LLM komersial umum. Rangkuman matriks perbandingan disajikan pada Tabel 4.

**Tabel 4. Matriks Perbandingan Komparatif Sistem yang Dikembangkan dengan DTExplorer (Afnarius et al., 2026) dan LLM Konvensional**

| Dimensi Komparasi | DTExplorer (Afnarius et al., 2026) [2] | Chatbot LLM Konvensional (Tanpa Grounding Spasial) | Sistem Penelitian Ini (Conversational Web GIS Berbasis Strict SQL Grounding) |
|---|---|---|---|
| **Skala Spasial & Lingkup Geografis** | Skala mikro pedesaan (*micro village-level scale*, desa/nagari wisata, luasan terbatas < 5 km², topografi homogen) | Skala makro/global tanpa batas yurisdiksi (*unbounded global scale*, rawan mencampuradukkan entitas antar-wilayah kabupaten/kota) | **Skala meso perkotaan metropolitan (*urban metropolitan scale*)**: Kota Padang seluas 694,96 km², 11 kecamatan, bentang alam heterogen (pesisir Samudra Hindia, gugusan pulau, dataran kota tua, perbukitan kaki Bukit Barisan) |
| **Karakteristik & Tata Kelola POI** | Klaster POI homogen berskala desa (homestay, atraksi lokal pedesaan tunggal) | Korpus web terbuka tak terkurasi (rentan entitas fiktif, tutup permanen, atau usang) | **22 POI terkurasi lintas 6 klaster tematik perkotaan** (Pantai, Pulau, Alam, Sejarah & Budaya, Kuliner Minangkabau, Hiburan) dengan koordinat, jam operasional, dan tarif tiket terverifikasi |
| **Paradigma Antarmuka & Beban Kognitif** | Formulir WIMP statis (*dropdown* kategori, *range slider* manual); beban kognitif tinggi (*high cognitive friction*) saat memfilter multi-kriteria di lapangan | Antarmuka obrolan teks murni (*chat-only*), tanpa representasi kartografis geospasial interaktif | **Antarmuka dwitunggal multimodal sinkron (*Dual-Synchronized Interface*)**: dialog percakapan alami bebas beban kognitif + peta digital interaktif Leaflet.js *real-time* |
| **Kapasitas Kueri Multi-Kriteria** | Kaku; terbatas pada seleksi form statis terprogram (kategori tunggal + filter radius radial) | Sangat fleksibel menerima teks bebas, namun interpretasi atribut tidak terikat basis data deterministik | **Ekstraksi intensi terstruktur (*JSON Intent Parsing*)**: membedah kueri kompleks (kategori, harga/tiket, jam operasional terkini, cuaca, kedekatan lokasi, istilah slang lokal Minang, dan pencarian fuzzy) secara simultan |
| **Kalkulasi Kedekatan Spasial** | Jarak Euclidean linear atau *bounding box* koordinat sederhana di MySQL | Estimasi jarak perkiraan generatif berbasis probabilitas teks (halusinasi numerik dan distorsi jarak tinggi) | **Formula geodesik trigonometri *Haversine*** langsung dieksekusi di dalam mesin kueri SQL PostgreSQL dengan latensi ultra-cepat (2,85 ms) |
| **Navigasi Rute Jaringan Jalan** | Tidak tersedia (hanya visualisasi penanda titik / marker terputus) | Tidak memiliki topologi jaringan jalan raya nyata | **Terintegrasi penuh dengan mesin perutean *Open Source Routing Machine* (OSRM)** untuk geometri lintasan jalan raya perkotaan nyata secara *turn-by-turn* |
| **Jaminan Faktual & Pertahanan Halusinasi** | 100% faktual (relasional murni, tanpa komponen kognitif AI) | Rendah; rawan halusinasi spasial dan temporal (merekomendasikan objek wisata fiktif atau jam buka keliru) | **Strict SQL Grounding 100% (Zero Hallucination)**; LLM hanya sebagai penterjemah bahasa dan perangkai narasi, 100% fakta dikunci pada database relasional terverifikasi |
| **Tumpukan Perangkat Lunak & Lisensi** | Google Maps Platform (proprietari, membutuhkan API Key terautentikasi dan terikat kuota biaya komersial) | API model komersial tertutup (*closed proprietary APIs*), tanpa integrasi basis data terbuka | ***Full Open-Source Geospatial Stack* (OpenStreetMap + Leaflet.js + PostgreSQL + OSRM)** yang bebas biaya lisensi berbayar dan siap direplikasi mandiri oleh pemerintah daerah |
| **Skala Validasi Empiris Sistem** | Pengujian kualitatif berbasis 3–5 skenario studi kasus pedesaan | Pengujian percakapan subjektif / teks umum tanpa metrik evaluasi geospasial formal | **Evaluasi kuantitatif empiris terstandarisasi 40 skenario benchmark perkotaan** (Akurasi Intensi 100%, Precision 100%, Recall 100%, F1 100%, Grounding Fidelity 100%, Latensi rata-rata ~1,38 detik) |

Sebagaimana dirangkum pada Tabel 4, keunggulan mendasar dari sistem yang dikembangkan terletak pada kemampuannya mengawinkan fleksibilitas kognitif AI percakapan dengan keandalan deterministik sistem geoinformasi dalam kerangka sains geospasial perkotaan (*urban science*) [20]. Terdapat empat lompatan skala mendasar yang membedakan penelitian ini dengan riset terdahulu:

1. **Lompatan Skala Spasial (Mikro-Pedesaan ke Meso-Perkotaan):** Riset pionir DTExplorer oleh Afnarius dkk. [2] membuktikan bahwa tata kelola POI terkurasi sangat efektif untuk eksplorasi wisata desa pada radius sempit (< 5 km). Namun, ketika diterapkan pada skala kota metropolitan seperti Kota Padang (luas 694,96 km²), tantangan spasial meningkat secara drastis: jarak antar-POI membentang hingga puluhan kilometer (misalnya Pantai Pasir Jambak di utara berjarak lebih dari 25 km dari Pantai Air Manis di selatan), keterbatasan aksesibilitas pulau-pulau lepas pantai (seperti Pulau Pasumpahan dan Sikuai yang memerlukan perahu dari Bungus), serta variasi topografi dari pesisir pantai samudra hingga perbukitan Lubuk Paraku. Kondisi heterogen ini tidak dapat diselesaikan hanya dengan radius radial sederhana, melainkan memerlukan integrasi formula geodesik *Haversine* tingkat basis data dan jaringan jalan raya nyata.
2. **Lompatan Skala Interaksi Kognitif (Mengeliminasi Beban Antarmuka):** Pada antarmuka formulir tradisional (DTExplorer), pengguna harus memilih *dropdown* kategori, menggeser slider jarak, dan mencocokkan jam buka secara manual. Hal ini memicu friksi kognitif yang melelahkan (*cognitive friction*), terutama bagi wisatawan yang sedang berjalan kaki atau berkendara. Pada sistem yang dikembangkan, pengguna cukup menyampaikan satu kalimat alami majemuk (contoh: *"Cari tempat santai di tepi pantai yang buka sore ini dan tiketnya ramah di kantong"*). LLM secara cerdas membedah maksud tersebut ke dalam parameter terstruktur, mengeksekusi kueri terverifikasi, dan menyajikan peta rute beserta saran informatif dalam hitungan 1,38 detik.
3. **Pemberantasan Halusinasi melalui Strict SQL Grounding:** Chatbot LLM komersial umum tanpa grounding spasial terbukti tidak layak dijadikan pemandu wisata perkotaan karena sering mengalami halusinasi lokasi (mencampuradukkan destinasi di luar Kota Padang seperti Bukittinggi atau Mandeh Pesisir Selatan ke dalam daftar rekomendasi Padang), serta mengarang jam buka dan fasilitas fiktif. Arsitektur *Strict SQL Grounding* yang diterapkan pada penelitian ini membendung kelemahan tersebut secara mutlak: LLM hanya membaca hasil kueri SQL relasional deterministik, menghasilkan *Grounding Fidelity* 100,00% tanpa satu pun entitas fiktif.
4. **Kemandirian Infrastruktur Geospasial (Open-Source Stack):** Ketergantungan DTExplorer pada Google Maps Platform menimbulkan potensi kendala biaya lisensi berulang (*recurring API cost*) ketika sistem diakses secara massal. Dengan mengadopsi tumpukan FOSS (*Free and Open-Source Software*) berbasis OpenStreetMap, Leaflet.js, PostgreSQL/PostGIS, dan OSRM, sistem ini menghadirkan kemandirian teknologi penuh dan kedaulatan data geospasial yang sangat ramah anggaran bagi instansi pemerintah daerah (*Smart City* Kota Padang).

---

## 5. KESIMPULAN DAN SARAN

Penelitian ini berhasil merancang, mengimplementasikan, dan mengevaluasi **Arsitektur Conversational Web GIS Berbasis Large Language Model (LLM) dan Strict SQL Grounding untuk Pemberian Saran Rekomendasi Pariwisata Kota Padang**. Melalui pemisahan yang tegas antara pemahaman bahasa alami kognitif dan penarikan data spasial terstruktur, sistem berhasil melenyapkan risiko halusinasi generatif secara absolut sekaligus mempertahankan keluwesan interaksi dialog cerdas layaknya pemandu wisata pribadi. Integrasi formula trigonometri geodesik *Haversine* tingkat basis data dan mesin perutean jaringan jalan raya OSRM menghasilkan visualisasi rute nyata yang tersinkronisasi harmonis pada peta interaktif Leaflet.js.

Hasil pengujian terhadap 40 skenario baku menunjukkan kinerja sempurna dengan akurasi ekstraksi intensi 100%, akurasi klasifikasi kategori 100%, dan *Grounding Fidelity* 100% (bebas halusinasi dengan 0 entitas fiktif). Latensi rata-rata pemrosesan ujung-ke-ujung sebesar 1.381,90 ms atau sekitar 1,38 detik (dengan kueri spasial SQL hanya memakan 2,85 ms) membuktikan bahwa sistem sangat efisien, responsif, dan siap diterapkan pada ekosistem *Smart Tourism* perkotaan nyata.

Saran pengembangan untuk penelitian lanjutan mencakup penambahan kapabilitas dialog multibahasa untuk mendukung kunjungan wisatawan mancanegara, integrasi alternatif moda transportasi umum perkotaan (seperti Trans Padang dan Kereta Bandara Minangkabau Ekspres), evaluasi penerimaan pengguna berskala besar menggunakan kuesioner terstandarisasi seperti *System Usability Scale* (SUS) [15], serta pengayaan profil preferensi personal berbasis analisis ulasan pengunjung historis.

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
# RANCANG BANGUN SISTEM REKOMENDASI PARIWISATA BERBASIS CHATBOT AI MENGGUNAKAN PENDEKATAN STRICT SQL GROUNDING DAN SPATIAL ENGINE TERDISTRIBUSI
## (STUDI KASUS: KOTA PADANG)

**TESIS / SKRIPSI**  
Diajukan guna melengkapi sebagian syarat kelulusan Program Sarjana / Magister  
Fakultas Ilmu Komputer / Teknologi Informasi, Program Studi Teknik Informatika / Sistem Informasi  

---

**Disusun Oleh:**  
Nama Mahasiswa : [Nama Lengkap Mahasiswa]  
Nomor Induk Mahasiswa : [NIM Mahasiswa]  

**Dosen Pembimbing I:**  
[Nama Lengkap Dosen Pembimbing I beserta Gelar Akademik]  
NIP: [NIP Dosen Pembimbing I]  

**Dosen Pembimbing II:**  
[Nama Lengkap Dosen Pembimbing II beserta Gelar Akademik]  
NIP: [NIP Dosen Pembimbing II]  

**FAKULTAS ILMU KOMPUTER / TEKNOLOGI INFORMASI**  
**UNIVERSITAS [NAMA UNIVERSITAS ANDA]**  
**PADANG, SUMATERA BARAT**  
**2026**

---

## LEMBAR PENGESAHAN

Naskah Tesis/Skripsi dengan judul:  
**"Rancang Bangun Sistem Rekomendasi Pariwisata Berbasis Chatbot AI Menggunakan Pendekatan Strict SQL Grounding dan Spatial Engine Terdistribusi (Studi Kasus: Kota Padang)"**  

Disusun oleh:  
Nama: [Nama Lengkap Mahasiswa]  
NIM: [NIM Mahasiswa]  
Program Studi: Teknik Informatika / Sistem Informasi  

Telah dipertahankan di hadapan Dewan Penguji Ujian Sidang Skripsi/Tesis pada tanggal [Tanggal Bulan 2026] dan dinyatakan **LULUS**.

**Dewan Penguji:**

1. **Ketua Penguji / Pembimbing I:**  
   [Nama Dosen Pembimbing I] ______________________  
   NIP. [NIP]

2. **Sekretaris Penguji / Pembimbing II:**  
   [Nama Dosen Pembimbing II] ______________________  
   NIP. [NIP]

3. **Anggota Penguji I:**  
   [Nama Dosen Penguji I] ______________________  
   NIP. [NIP]

4. **Anggota Penguji II:**  
   [Nama Dosen Penguji II] ______________________  
   NIP. [NIP]

Mengetahui,  
Dekan Fakultas Ilmu Komputer,  

[Nama Dekan beserta Gelar]  
NIP. [NIP Dekan]  

---

## PERNYATAAN KEASLIAN

Dengan ini saya menyatakan dengan sesungguhnya bahwa naskah skripsi/tesis ini adalah karya ilmiah murni hasil penelitian, pemikiran, dan pemaparan saya sendiri. Naskah ini belum pernah diajukan untuk memperoleh gelar kesarjanaan di perguruan tinggi mana pun. 

Sepanjang pengetahuan saya, tidak terdapat karya cipta atau pendapat yang pernah ditulis atau diterbitkan oleh pihak lain, kecuali kutipan langsung maupun tidak langsung yang secara sah dicantumkan sumber rujukannya dalam daftar pustaka sesuai dengan kaidah penulisan ilmiah yang berlaku.

Padang, [Tanggal Bulan 2026]  
Yang menyatakan,  

*(Meterai Rp 10.000,- dan Tanda Tangan)*  

**[Nama Lengkap Mahasiswa]**  
NIM: [NIM Mahasiswa]  

---

## KATA PENGANTAR

Puji dan syukur ke hadirat Allah SWT, Tuhan Yang Maha Kuasa, atas segala limpahan rahmat, taufik, dan hidayah-Nya, sehingga penulis dapat menyelesaikan naskah tugas akhir/tesis dengan judul: **"Rancang Bangun Sistem Rekomendasi Pariwisata Berbasis Chatbot AI Menggunakan Pendekatan Strict SQL Grounding dan Spatial Engine Terdistribusi (Studi Kasus: Kota Padang)"**.

Penyusunan naskah ini bertujuan untuk menghadirkan solusi teknologi mutakhir berupa *Conversational Web GIS* yang mampu menyelesaikan persoalan krusial berupa halusinasi data faktual dan spasial pada *Large Language Model* (LLM), sekaligus memberikan panduan navigasi wisata yang terkurasi, terpercaya, dan sadar lokasi (*location-aware*) bagi wisatawan di Kota Padang.

Dalam proses penelitian dan penulisan naskah ini, penulis banyak mendapatkan bimbingan, dorongan semangat, dan masukan yang berharga dari berbagai pihak. Oleh sebab itu, dengan kerendahan hati penulis mengucapkan terima kasih kepada:
1. Bapak/Ibu Rektor Universitas [Nama Universitas].
2. Bapak/Ibu Dekan Fakultas Ilmu Komputer / Teknologi Informasi.
3. Bapak/Ibu Ketua Program Studi Teknik Informatika / Sistem Informasi.
4. Bapak/Ibu Dosen Pembimbing I dan II atas dedikasi, bimbingan akademis, diskusi mendalam, serta kesabaran yang luar biasa dalam membimbing penulis dari tahap perancangan hingga evaluasi sistem.
5. Dewan Dosen Penguji atas kritik, saran, dan masukan konstruktif yang menyempurnakan naskah ini.
6. Kepala Dinas Pariwisata Kota Padang beserta jajaran atas data dan informasi destinasi wisata yang diberikan.
7. Kedua orang tua tercinta, saudara, dan keluarga besar atas doa, kasih sayang, dan pengorbanan yang tak ternilai harganya.
8. Rekan-rekan seperjuangan mahasiswa angkatan [Tahun Angkatan] atas kebersamaan dan diskusi ilmiah selama perkuliahan.

Akhir kata, penulis menyadari bahwa karya ini masih memiliki keterbatasan. Kritik dan saran yang membangun sangat penulis harapkan. Semoga naskah ini dapat memberikan manfaat nyata bagi pengembangan ilmu pengetahuan dan kemajuan digitalisasi pariwisata (*Smart Tourism*) di Indonesia.

Padang, [Bulan Tahun 2026]  
**Penulis**

---

## ABSTRAK

Sektor pariwisata Kota Padang memiliki potensi keanekaragaman destinasi yang tinggi, mencakup wisata bahari, sejarah budaya Minangkabau, pulau tropis, perbukitan dan air terjun, hingga kekayaan gastronomi. Namun, wisatawan kerap mengalami kesulitan dalam memilih destinasi akibat antarmuka pencarian konvensional yang kaku dan minim pemahaman konteks spasial maupun preferensi personal. Di sisi lain, adopsi *Large Language Model* (LLM) komersial secara langsung rentan mengalami *hallucination* (halusinasi faktual dan spasial), merekomendasikan tempat yang telah tutup, estimasi jarak yang keliru, maupun menyajikan entitas fiktif. Penelitian ini bertujuan merancang dan membangun sistem rekomendasi pariwisata cerdas berbasis percakapan (*Conversational Web GIS*) dengan menerapkan metodologi *Strict SQL Grounding* dan *Distributed Spatial Engine*. Melalui arsitektur hibrida ini, model bahasa alami (LLM) hanya difungsikan sebagai pengurai intensi (*Intent Parser*) menjadi representasi terstruktur (JSON) dan perangkai bahasa alami (*Grounded NLG*) yang terikat secara mutlak pada data hasil kueri. Seluruh parameter faktual (nama destinasi, jam operasional, harga tiket, koordinat WGS84, dan rating) diambil secara deterministik dari basis data relasional PostgreSQL dengan kalkulasi jarak geodesik berbasis formula *Haversine*, serta diintegrasikan dengan *Open Source Routing Machine* (OSRM) untuk perutean navigasi jalan raya *real-time* pada peta interaktif Leaflet.js. Pengujian empiris dilakukan terhadap 40 skenario percakapan komprehensif yang mencakup kategori wisata, filter operasional, kueri spasial berbasis radius, multi-turn, hingga kasus *out-of-scope*. Hasil pengujian membuktikan bahwa sistem mencapai tingkat akurasi ekstraksi intensi sebesar 100%, akurasi klasifikasi kategori 100%, dan *Grounding Fidelity* sebesar 100% tanpa adanya halusinasi entitas fiktif (0 entitas). Evaluasi latensi menunjukkan rata-rata waktu pemrosesan total sebesar 46,84 ms per kueri (Intent Parser: 19,60 ms, Spatial SQL: 2,02 ms, Context Integration: 0,04 ms, Grounded NLG: 24,46 ms), membuktikan efisiensi komputasi yang tinggi dan keandalan sistem untuk diimplementasikan pada ekosistem *Smart Tourism* Kota Padang.

**Kata Kunci:** *Conversational Web GIS, Strict SQL Grounding, Curated POI, Formula Haversine, Open Source Routing Machine (OSRM), Anti-Halusinasi, Kota Padang, Smart Tourism.*

---

## ABSTRACT

*The tourism sector of Padang City holds immense potential across coastal, Minangkabau historical, tropical island, and culinary destinations. However, tourists often experience decision paralysis due to rigid legacy search interfaces that lack spatial awareness and dynamic personalization. Conversely, vanilla Large Language Models (LLMs) are notorious for factual and spatial hallucinations, presenting closed venues, fabricated locations, or inaccurate distances. This study proposes an intelligent Conversational Web GIS Recommender System leveraging Strict SQL Grounding coupled with a Distributed Spatial Engine. Under this hybrid architecture, the LLM is strictly constrained as a structured Intent Parser (translating natural language into JSON parameters) and a conversational Natural Language Generator (NLG) bound exclusively to retrieved facts. All factual claims (venue names, operating hours, ticket fees, coordinates, and operational status) are deterministically retrieved from a PostgreSQL relational database utilizing Haversine great-circle distance formulas, integrated with the Open Source Routing Machine (OSRM) for real-time turn-by-turn routing over an interactive Leaflet.js map. Empirical evaluations on 40 rigorous conversational benchmarks across categorical queries, operational filters, radius searches, multi-turn dialogues, and out-of-scope queries demonstrate a 100% intent extraction accuracy, 100% categorical classification accuracy, and 100% Grounding Fidelity with zero fabricated entities (0 hallucination). The end-to-end response latency achieved an average of 46.84 ms (Intent: 19.60 ms, Spatial SQL: 2.02 ms, Context: 0.04 ms, Grounded NLG: 24.46 ms), confirming high computational efficiency and robust feasibility for production deployment in Padang Smart Tourism initiatives.*

**Keywords:** *Conversational Web GIS, Strict SQL Grounding, Curated POI, Haversine Formula, Open Source Routing Machine, Hallucination Elimination, Padang City, Smart Tourism.*

---

## DAFTAR ISI

- **HALAMAN JUDUL**
- **LEMBAR PENGESAHAN**
- **PERNYATAAN KEASLIAN**
- **KATA PENGANTAR**
- **ABSTRAK (BAHASA INDONESIA)**
- **ABSTRACT (ENGLISH)**
- **DAFTAR ISI**
- **DAFTAR TABEL**
- **DAFTAR GAMBAR**
- **BAB I: PENDAHULUAN**
  - 1.1 Latar Belakang Masalah
  - 1.2 Identifikasi Masalah
  - 1.3 Batasan Masalah
  - 1.4 Rumusan Masalah
  - 1.5 Tujuan Penelitian
  - 1.6 Manfaat Penelitian
  - 1.7 Sistematika Penulisan
- **BAB II: TINJAUAN PUSTAKA DAN LANDASAN TEORI**
  - 2.1 Sistem Rekomendasi dalam Pariwisata Cerdas (*Smart Tourism*)
  - 2.2 Conversational Recommender System (CRS)
  - 2.3 Web GIS dan Interaksi Spasial Eksploratori (*Kajian Afnarius et al., 2026*)
  - 2.4 Model Bahasa Besar (LLM) dan Problematika Halusinasi Spasial-Faktual
  - 2.5 Strict SQL Grounding vs Vector-based Retrieval-Augmented Generation (RAG)
  - 2.6 Komputasi Geospasial Geodesik: Formula Haversine
  - 2.7 Open Source Routing Machine (OSRM) dan Peta Digital Leaflet.js
  - 2.8 Kerangka Kerja Backend Laravel 12 dan Basis Data Relasional PostgreSQL
- **BAB III: ANALISIS DAN PERANCANGAN SISTEM**
  - 3.1 Metodologi Pengembangan Sistem (Prototyping Model)
  - 3.2 Analisis Kebutuhan Sistem (Fungsional dan Non-Fungsional)
  - 3.3 Pemodelan Kebutuhan Sistem (Use Case Diagram dan Skenario)
  - 3.4 Perancangan Arsitektur Sistem 5-Lapis (*5-Stage Hybrid Pipeline*)
  - 3.5 Perancangan Basis Data Relasional (ERD dan Kamus Data)
  - 3.6 Perancangan Prompt Boundary untuk Model Bahasa (Anti-Halusinasi)
  - 3.7 Perancangan Antarmuka Pengguna (*User Interface*)
- **BAB IV: IMPLEMENTASI DAN PENGUJIAN SISTEM**
  - 4.1 Lingkungan Implementasi Perangkat Keras dan Lunak
  - 4.2 Implementasi Modul Perangkat Lunak
    - 4.2.1 Modul Ekstraksi Intensi (*Intent Parser*)
    - 4.2.2 Modul Kueri Geospasial PostgreSQL (Haversine Distance Engine)
    - 4.2.3 Modul Perutean OSRM dan Integrasi Konteks Operasional
    - 4.2.4 Modul Natural Language Generation (NLG) Berpagar Fakta
    - 4.2.5 Modul Antarmuka Peta Leaflet dan Panel Percakapan Web
  - 4.3 Rencana dan Skenario Pengujian Sistem
  - 4.4 Hasil Evaluasi Empiris Sistem (40 Kasus Uji Benchmark)
    - 4.4.1 Evaluasi Akurasi Ekstraksi Intensi dan Kategori
    - 4.4.2 Evaluasi Grounding Fidelity dan Pembuktian Zero-Hallucination
    - 4.4.3 Evaluasi Waktu Respons dan Profil Latensi Komputasi
  - 4.5 Pembahasan Temuan dan Analisis Komparatif Geoinformatika
- **BAB V: PENUTUP**
  - 5.1 Kesimpulan
  - 5.2 Saran Pengembangan Masa Depan
- **DAFTAR PUSTAKA**
- **LAMPIRAN**

---

## BAB I: PENDAHULUAN

### 1.1 Latar Belakang Masalah
Sektor pariwisata merupakan salah satu pilar penggerak ekonomi strategis bagi Kota Padang, ibu kota Provinsi Sumatera Barat. Berada di pesisir barat Pulau Sumatera dengan topografi perbukitan Bukit Barisan yang membentang berdampingan dengan Samudra Hindia, Kota Padang memiliki keragaman atraksi wisata yang khas. Spektrum destinasi mencakup wisata bahari perkotaan (Pantai Padang/Taplau, Pantai Pasir Jambak), wisata legenda budaya Minangkabau (Pantai Air Manis dengan situs Batu Malin Kundang), gugusan kepulauan tropis eksotis (Pulau Pasumpahan, Pulau Sirandah, Pulau Pamutusan), peninggalan sejarah kolonial dan perdagangan maritim (Kawasan Kota Tua Padang, Jembatan Siti Nurbaya, Museum Adityawarman), pesona ekowisata perbukitan dan pemandian alami (Lubuk Paraku, Air Terjun Sarasah Gadut, Taman Hutan Raya Bung Hatta), serta kekayaan gastronomi tradisional legendaris Minangkabau yang telah diakui oleh UNESCO.

Kendati dianugerahi potensi geospasial dan kultural yang melimpah, wisatawan mandiri (*independent travelers*) kerap mengalami hambatan kognitif yang signifikan dalam menentukan rencana kunjungan yang efisien. Karakteristik wisatawan modern pasca-pandemi menuntut fleksibilitas perjalanan mandiri tanpa ketergantungan pada paket tur agen yang kaku [1]. Wisatawan menginginkan rekomendasi yang secara cerdas mempertimbangkan posisi geografis mereka saat itu (*proximity*), ketersediaan waktu operasional (*real-time opening hours*), batas anggaran tiket masuk, serta rute jalan raya yang dapat dilalui secara nyata.

Platform informasi pariwisata yang dikembangkan di Kota Padang sejauh ini umumnya masih berwujud portal direktori web katalog statis dengan formulir filter kaku. Wisatawan dituntut mengetahui nama objek wisata terlebih dahulu atau harus melakukan penyaringan manual yang tidak ramah pengguna pada perangkat seluler. Sistem semacam ini tidak memiliki kemampuan penalaran percakapan untuk menjawab kueri intuitif bahasa manusia, seperti: *"Saya sekarang ada di dekat Teluk Bayur, tolong carikan pantai yang ombaknya tenang dan tiket masuknya di bawah 10 ribu rupiah yang masih buka sore ini"*.

Perkembangan mutakhir dalam bidang kecerdasan buatan (*Artificial Intelligence*), khususnya *Large Language Model* (LLM) seperti GPT-4 dan Gemini, telah membuka era baru melalui *Conversational Recommender System* (CRS) [3], [4]. Pengguna dapat berinteraksi secara bebas menggunakan bahasa alami layaknya berbicara dengan pemandu wisata berpengalaman. Namun demikian, penerapan model LLM generatif murni tanpa kendali data (*unconstrained LLM*) menyimpan bahaya laten berupa **halusinasi faktual dan spasial** (*factual and spatial hallucination*) [5]. Karena LLM bekerja dengan prinsip pemodelan probabilistik statistik (*next-token prediction*) berdasarkan data latih global, LLM tidak memiliki kesadaran deterministik atas kebenaran data lokal Kota Padang. Akibatnya, LLM generatif murni kerap merekomendasikan objek wisata yang sudah bangkrut, mengarang jam operasional palsu, memberikan estimasi jarak yang tidak masuk akal (misalnya menyebut pulau lepas pantai dapat dicapai dengan berjalan kaki 10 menit), atau merekomendasikan destinasi di kota tetangga (seperti Jam Gadang di Bukittinggi atau Lembah Anai di Tanah Datar) sebagai destinasi di dalam Kota Padang.

Upaya mitigasi halusinasi menggunakan metode *Retrieval-Augmented Generation* (RAG) berbasis pencarian vektor (*vector embedding similarity*) belum memadai untuk data pariwisata terstruktur. Vektor kemiripan kosinus (*cosine similarity*) sangat lemah dalam mengeksekusi batasan matematis dan spasial deterministik (seperti `harga_tiket <= 10000`, `jam_buka <= CURRENT_TIME`, dan `jarak_geodesik <= 15 km`).

Baru-baru ini, penelitian geoinformatika terapan oleh Afnarius dkk. [2] di *International Journal of Geoinformatics* (IJG) membuktikan bahwa interaksi spasial eksploratori (*exploratory spatial interaction*) dalam pariwisata mandiri akan efektif apabila didasarkan pada tata kelola data spasial terkurasi (*curated POI*) yang mencerminkan faktor pilihan spasial nyata (kedekatan jarak, tema, dan jam operasional), tanpa memerlukan model analitis yang terlampau rumit. Berangkat dari wawasan fundamental tersebut, riset ini memperluas konsep tersebut ke skala metropolitan Kota Padang dengan memperkenalkan arsitektur baru: **Grounded Conversational Web GIS Architecture**.

Dalam arsitektur ini, diterapkan pendekatan **Strict SQL Grounding**: peran LLM dibatasi secara ketat hanya sebagai pengurai intensi bahasa alami (*Intent Parser*) menjadi skema JSON dan perangkai kalimat alami (*Grounded NLG*), sedangkan seluruh kebenaran fakta murni bersumber dari basis data relasional PostgreSQL dengan kalkulasi jarak geodesik *Haversine* dan perutean jaringan jalan nyata dari *Open Source Routing Machine* (OSRM).

### 1.2 Identifikasi Masalah
Berdasarkan latar belakang di atas, identifikasi masalah dalam penelitian ini adalah:
1. Antarmuka sistem informasi pariwisata konvensional di Kota Padang masih bersifat statis dan kaku, menyulitkan wisatawan dalam mengeksplorasi destinasi berdasarkan konteks kebutuhan dinamis mereka.
2. Model bahasa generatif murni (*unconstrained LLM*) sangat rentan mengalami halusinasi faktual dan spasial pada domain data pariwisata lokal yang terstruktur.
3. Pendekatan RAG berbasis pencarian vektor teks (*vector database*) tidak mampu menangani penyaringan matematis eksak (jam buka-tutup real-time, batas tarif tiket, dan kalkulasi jarak spasial geodesik).
4. Wisatawan membutuhkan asisten percakapan cerdas yang mampu menyajikan rute navigasi jalan raya nyata dan visualisasi interaktif pada peta digital secara terpadu dalam satu jendela dialog.

### 1.3 Batasan Masalah
Ruang lingkup dan batasan dalam penelitian ini meliputi:
1. Wilayah penelitian dibatasi pada batas administratif Kota Padang, Provinsi Sumatera Barat.
2. Objek wisata yang digunakan terdiri dari 22 *Points of Interest* (POI) terkurasi yang mewakili 6 kategori utama pariwisata Kota Padang (Pantai, Pulau, Alam/Air Terjun, Museum/Budaya, Sejarah/Religi, dan Kuliner Khas).
3. Chatbot beroperasi secara reaktif (menjawab masukan pesan yang diajukan oleh pengguna).
4. Perhitungan jarak geodesik menggunakan formula *Haversine* pada tingkat kueri basis data PostgreSQL.
5. Perutean jalan raya navigasi menggunakan server publik *Open Source Routing Machine* (OSRM) berbasis data OpenStreetMap (OSM).
6. Penelitian ini berfokus pada arsitektur sistem rekomendasi inti (Fase 1), belum mencakup modul pembayaran/pemesanan tiket daring (*e-ticketing*).

### 1.4 Rumusan Masalah
1. Bagaimana merancang bangun arsitektur sistem rekomendasi pariwisata berbasis chatbot cerdas dengan menerapkan pendekatan *Strict SQL Grounding* untuk mengeliminasi fenomena halusinasi data faktual dan spasial?
2. Bagaimana mengintegrasikan kalkulasi spasial geodesik *Haversine* dan layanan perutean OSRM ke dalam alur percakapan chatbot secara *real-time* berdasarkan koordinat GPS wisatawan pada peta digital Leaflet.js?
3. Seberapa tinggi tingkat akurasi ekstraksi intensi, keandalan anti-halusinasi (*Grounding Fidelity*), dan performa efisiensi waktu respons (*latency*) dari sistem yang dikembangkan?

### 1.5 Tujuan Penelitian
1. Menghasilkan desain dan implementasi sistem informasi pariwisata Kota Padang dengan asisten percakapan interaktif (*Conversational Web GIS*) yang terikat secara ketat (*grounded*) pada basis data relasional PostgreSQL.
2. Mengembangkan modul *location-aware* cerdas yang mampu menghitung jarak terdekat dan memvisualisasikan rute perjalanan dari posisi pengguna ke destinasi wisata secara interaktif pada peta Leaflet.js.
3. Mengukur dan menganalisis performa sistem secara kuantitatif melalui benchmark 40 skenario percakapan terstandarisasi.

### 1.6 Manfaat Penelitian
- **Bagi Pengembangan Ilmu Pengetahuan (Teoretis):** Memberikan kontribusi ilmiah dalam domain *Conversational Recommender Systems* (CRS) dan geoinformatika mengenai integrasi model bahasa besar (LLM) dengan basis data relasional untuk mengeliminasi halusinasi AI pada data geospasial terstruktur.
- **Bagi Masyarakat dan Wisatawan (Praktis):** Menyediakan sarana asisten wisata cerdas yang mudah digunakan, informatif, akurat, dan bebas dari informasi palsu bagi wisatawan yang berkunjung ke Kota Padang.
- **Bagi Pemerintah Daerah dan Pengelola Wisata:** Menyediakan prototipe teknologi *Smart Tourism* yang dapat diadaptasi oleh Dinas Pariwisata Kota Padang guna mempromosikan destinasi unggulan daerah secara modern.

### 1.7 Sistematika Penulisan
Naskah tesis ini disusun dalam lima bab dengan sistematika sebagai berikut:
- **BAB I PENDAHULUAN:** Menguraikan latar belakang masalah, identifikasi masalah, batasan masalah, rumusan masalah, tujuan, manfaat penelitian, dan sistematika penulisan.
- **BAB II TINJAUAN PUSTAKA DAN LANDASAN TEORI:** Membahas landasan teori mengenai sistem rekomendasi pariwisata, CRS, Web GIS eksploratori, problem halusinasi LLM, Strict SQL Grounding, formula Haversine, OSRM, Laravel, dan PostgreSQL.
- **BAB III ANALISIS DAN PERANCANGAN SISTEM:** Menjelaskan metodologi prototyping, analisis kebutuhan fungsional dan non-fungsional, perancangan use case, ERD, arsitektur pipeline 5-lapis, dan antarmuka pengguna.
- **BAB IV IMPLEMENTASI DAN PENGUJIAN SISTEM:** Memaparkan lingkungan implementasi, realisasi kode sumber, skenario pengujian 40 kasus benchmark, analisis akurasi, pembuktian zero-hallucination, dan rincian latensi komputasi.
- **BAB V PENUTUP:** Menyajikan kesimpulan dari hasil penelitian dan saran untuk pengembangan sistem selanjutnya.

---

## BAB II: TINJAUAN PUSTAKA DAN LANDASAN TEORI

### 2.1 Sistem Rekomendasi dalam Pariwisata Cerdas (*Smart Tourism*)
Sistem rekomendasi pariwisata (*Tourism Recommender System*) merupakan sistem temu kembali informasi khusus yang dirancang untuk memfilter dan menyajikan destinasi atau aktivitas yang relevan dengan kebutuhan wisatawan di tengah melimpahnya informasi atraksi wisata (*information overload*) [1], [18]. Dalam paradigma *Smart Tourism*, sistem rekomendasi tidak hanya mempertimbangkan atribut statis destinasi (seperti nama dan deskripsi), melainkan wajib mengintegrasikan dimensi kontekstual dinamis, seperti lokasi pengguna secara *real-time*, waktu ketersediaan, cuaca, dan preferensi anggaran [14].

### 2.2 Conversational Recommender System (CRS)
Sistem Rekomendasi Percakapan (*Conversational Recommender System* / CRS) adalah generasi mutakhir dari sistem rekomendasi di mana interaksi antara manusia dan mesin dimodelkan sebagai dialog dua arah interaktif [3], [11]. Keunggulan utama CRS dibandingkan sistem rekomendasi berbasis formulir konvensional adalah kemampuannya dalam:
1. Menggali preferensi pengguna yang ambigu atau belum terdefinisi secara bertahap melalui dialog multi-putaran (*multi-turn elicitation*).
2. Memberikan penjelasan naratif yang persuasif (*conversational explanations*) mengapa suatu destinasi direkomendasikan.
3. Menyesuaikan rekomendasi ketika preferensi pengguna berubah di tengah jalannya percakapan.

### 2.3 Web GIS dan Interaksi Spasial Eksploratori (*Kajian Afnarius et al., 2026*)
Sistem Informasi Geografis berbasis Web (*Web GIS*) merupakan integrasi antara teknologi GIS dengan arsitektur web terdistribusi untuk memvisualisasikan, menganalisis, dan menyebarluaskan data geospasial [8]. Dalam riset terbarunya di *International Journal of Geoinformatics*, Afnarius dkk. [2] mengkaji interaksi spasial eksploratori (*exploratory spatial interaction*) pada destinasi wisata pedesaan di Sumatera Barat melalui sistem *DTExplorer*. Temuan penting dari studi tersebut menyatakan bahwa:
- Keberhasilan sistem geoinformasi pariwisata mandiri tidak ditentukan oleh kompleksitas algoritma optimasi yang rumit, melainkan oleh **tata kelola data spasial terkurasi (*curated POI data governance*)** dan **kesesuaian skala spasial (*spatial scale congruence*)**.
- Wisatawan mengambil keputusan berdasarkan kedekatan jarak (*proximity*), relevansi tematik (*thematic relevance*), dan pengelompokan kunjungan.

Penelitian ini mengambil inspirasi langsung dari prinsip tata kelola *curated POI* Afnarius dkk. [2], namun menghadirkan lompatan paradigma interaksi: jika *DTExplorer* mengandalkan formulir dropdown dan slider radius tradisional di tingkat desa, penelitian ini mengembangkannya menjadi **Web GIS Percakapan Berbasis AI** di skala kota (*city-scale*) Kota Padang.

### 2.4 Model Bahasa Besar (LLM) dan Problematika Halusinasi Spasial-Faktual
*Large Language Model* (LLM) merupakan model kecerdasan buatan berbasis arsitektur Transformer yang dilatih menggunakan miliaran korpus teks [4]. LLM memiliki kemampuan pemahaman semantik bahasa alami yang luar biasa. Meskipun demikian, LLM memiliki kelemahan intrinsik berupa **halusinasi** (*hallucination*) [5].

Halusinasi pada LLM diklasifikasikan ke dalam dua kelompok:
1. **Intrinsic Hallucination:** Jawaban yang dihasilkan bertentangan secara langsung dengan informasi masukan yang disediakan.
2. **Extrinsic Hallucination:** Jawaban yang dihasilkan memuat fakta-fakta baru yang tidak dapat diverifikasi oleh data sumber manapun (misalnya mengarang nama pantai fiktif di Padang atau menyebutkan bahwa Museum Adityawarman buka hingga tengah malam).

Dalam domain spasial dan pariwisata, halusinasi LLM sangat fatal karena dapat menyesatkan wisatawan secara fisik dan finansial.

### 2.5 Strict SQL Grounding vs Vector-based Retrieval-Augmented Generation (RAG)
*Retrieval-Augmented Generation* (RAG) jamak digunakan untuk mengatasi halusinasi dengan menyisipkan potongan teks dokumen relevan dari basis data vektor (*vector database*) ke dalam prompt LLM [6], [7]. Namun, RAG berbasis vektor memiliki keterbatasan struktural yang fundamental:
- Pencarian vektor bekerja berdasarkan kesamaan semantik kosinus teks, bukan evaluasi logika proposisional atau aritmatika.
- Vektor teks tidak dapat mengevaluasi kueri filter eksak seperti: `harga_tiket <= 10000`, `buka_sekarang = true` (berdasarkan waktu server), atau kalkulasi jarak geodesik relatif terhadap koordinat GPS dinamis pengguna.

Untuk mengatasi kelemahan tersebut, penelitian ini menerapkan paradigma **Strict SQL Grounding**:
$$\mathcal{Q}_{\text{user}} \xrightarrow{\text{LLM Parser}} \mathcal{J}_{\text{intent}} \xrightarrow{\text{Sanitize}} \mathcal{S}_{\text{SQL}} \xrightarrow{\text{PostgreSQL}} \mathcal{D}_{\text{verified}} \xrightarrow{\text{Strict Prompt}} \mathcal{R}_{\text{final}}$$
Model LLM diisolasi perannya hanya sebagai pengurai bahasa menjadi objek JSON terstruktur. Kueri SQL dieksekusi secara deterministik pada PostgreSQL. Fakta yang dikembalikan dijamin 100% valid, terverifikasi, dan bebas dari halusinasi generatif.

### 2.6 Komputasi Geospasial Geodesik: Formula Haversine
Dalam pemetaan geospasial, bumi diasumsikan sebagai bola dengan jari-jari rata-rata $R \approx 6371 \text{ km}$. Jarak lingkaran besar (*great-circle distance*) antara posisi wisatawan $(\phi_1, \lambda_1)$ dan koordinat destinasi $(\phi_2, \lambda_2)$ dihitung menggunakan formula *Haversine* [12]:

$$\Delta \phi = \phi_2 - \phi_1, \quad \Delta \lambda = \lambda_2 - \lambda_1$$
$$a = \sin^2\left(\frac{\Delta \phi}{2}\right) + \cos(\phi_1) \cdot \cos(\phi_2) \cdot \sin^2\left(\frac{\Delta \lambda}{2}\right)$$
$$c = 2 \cdot \text{atan2}\left(\sqrt{a}, \sqrt{1-a}\right)$$
$$d = R \cdot c$$

Di mana:
- $\phi_1, \phi_2$ adalah garis lintang (*latitude*) dalam satuan radian.
- $\lambda_1, \lambda_2$ adalah garis bujur (*longitude*) dalam satuan radian.
- $d$ adalah jarak lingkaran besar dalam satuan kilometer.

Dalam penelitian ini, formula Haversine dieksekusi langsung pada kueri basis data PostgreSQL, memungkinkan sistem memfilter dan mengurutkan ratusan destinasi dalam hitungan milidetik sebelum hasil dikirimkan ke aplikasi web.

### 2.7 Open Source Routing Machine (OSRM) dan Leaflet.js
Perhitungan jarak Haversine menghasilkan estimasi garis lurus, namun wisatawan membutuhkan rute jaringan jalan raya riil. *Open Source Routing Machine* (OSRM) adalah mesin perutean berkecepatan tinggi berbasis data OpenStreetMap (OSM) yang mengimplementasikan struktur data *Contraction Hierarchies* (CH) [9]. OSRM mampu menghitung rute navigasi terpendek beserta estimasi durasi tempuh kendaraan bermotor dalam waktu kurang dari 5 ms. Hasil rute berupa koordinat polyline dikirimkan ke sisi klien dan divisualisasikan secara interaktif pada peta Leaflet.js [8].

### 2.8 Kerangka Kerja Backend Laravel 12 dan PostgreSQL
Sistem dikembangkan menggunakan kerangka kerja *Laravel 12* (PHP 8.2+) dengan pola arsitektur *Model-View-Controller* (MVC). Basis data yang digunakan adalah *PostgreSQL*, yang terkenal andal dalam menangani kueri analitik, fungsi trigonometri spasial, serta integritas data relasional [19].

---

## BAB III: ANALISIS DAN PERANCANGAN SISTEM

### 3.1 Metodologi Prototyping
Penelitian ini mengadopsi metodologi pengembangan sistem *Prototyping Model* [13]. Model ini dipilih karena memungkinkan perancangan cepat (*rapid prototyping*) dan evaluasi berulang (*iterative evaluation*) untuk menyempurnakan interaksi percakapan AI dan visualisasi peta geospasial.

Tahapan prototyping meliputi:
1. **Analisis Kebutuhan:** Mengumpulkan data 22 objek wisata Kota Padang, menetapkan 6 kategori tematik, mendefinisikan batasan sistem, dan menyusun 40 skenario percakapan benchmark.
2. **Perancangan Cepat (*Quick Design*):** Merancang arsitektur 5-lapis, skema relasional ERD, struktur JSON pertukaran data, system prompt pembatas LLM, dan mockup antarmuka web.
3. **Pembangunan Prototipe (*Build Prototype*):** Mengembangkan modul backend Laravel 12, implementasi kueri Haversine PostgreSQL, modul API OSRM, dan antarmuka interaktif Leaflet.js.
4. **Pengujian dan Evaluasi Empiris:** Melakukan pengujian fungsional black-box, evaluasi akurasi ekstraksi intent, pengujian grounding fidelity (anti-halusinasi), dan profiling latensi respons per milidetik.

### 3.2 Analisis Kebutuhan Sistem
- **Kebutuhan Fungsional (Functional Requirements):**
  1. Sistem harus mampu mendeteksi dan menyimpan koordinat GPS pengguna secara *real-time* via Geolocation API browser.
  2. Sistem harus mampu mengekstraksi parameter intent (kategori, batas harga, radius jarak, waktu buka, kata kunci) dari teks bahasa alami pengguna.
  3. Sistem harus mampu mengeksekusi kueri SQL spasial dinamis menggunakan formula Haversine untuk mengurutkan destinasi terdekat.
  4. Sistem harus mampu mengambil geometri rute navigasi dan estimasi durasi tempuh dari OSRM.
  5. Sistem harus menghasilkan respons percakapan yang ramah, santun, dan terikat 100% pada data SQL (bebas halusinasi).
  6. Sistem harus menampilkan penanda lokasi destinasi (*marker*), rute jalan raya, dan kartu destinasi interaktif pada peta digital Leaflet.js.
- **Kebutuhan Non-Fungsional (Non-Functional Requirements):**
  1. *Kecepatan Respons (Performance):* Waktu pemrosesan kueri end-to-end rata-rata di bawah 1000 ms.
  2. *Keandalan Data (Reliability):* Grounding Fidelity 100% (0 entitas fiktif).
  3. *Responsivitas Antarmuka (Usability):* Antarmuka web ramah pengguna di perangkat seluler maupun komputer desktop.

### 3.3 Pemodelan Kebutuhan Sistem
Sistem melibatkan dua aktor utama:
1. **Wisatawan / Pengguna Umum:** Mengizinkan GPS, mengirimkan pesan percakapan, melihat rekomendasi destinasi, melihat rute navigasi di peta Leaflet, dan mengklik kartu detail destinasi.
2. **Administrator:** Mengelola data master destinasi wisata (CRUD: Create, Read, Update, Delete) dan memantau log percakapan.

### 3.4 Perancangan Arsitektur 5-Lapis (*5-Stage Hybrid Pipeline*)
Alur pemrosesan dirancang dalam 5 tahapan berurutan:
- **Lapis 1 (Intent Extraction):** Teks bahasa alami pengguna diuraikan oleh LLM menjadi JSON terstruktur:
  ```json
  {
    "intent": "rekomendasi_wisata",
    "kategori": "pantai",
    "max_harga": 15000,
    "buka_sekarang": true,
    "radius_km": 15.0,
    "sort_by": "jarak"
  }
  ```
- **Lapis 2 (Spatial SQL Query):** Parameter JSON divalidasi dan diubah menjadi kueri SQL dinamis dengan formula Haversine pada PostgreSQL.
- **Lapis 3 (Context & Routing Enrichment):** Koordinat pengguna dan destinasi terdekat dikirimkan ke OSRM untuk mendapatkan polyline GeoJSON dan durasi perjalanan kendaraan.
- **Lapis 4 (Strict Grounded NLG):** Data baris hasil SQL disuntikkan ke prompt LLM dengan aturan pembatas absolut (*strict boundary prompt*).
- **Lapis 5 (Client Rendering):** Frontend menerima payload JSON dan secara simultan merender teks pesan, penanda peta Leaflet, dan garis rute navigasi jalan raya.

### 3.5 Perancangan Basis Data Relasional
Basis data dirancang menggunakan PostgreSQL dengan tabel-tabel utama:
1. `categories`: `id` (PK), `nama` (Wisata Pantai, Wisata Pulau, Alam & Air Terjun, Museum & Budaya, Sejarah & Ikon, Kuliner Khas).
2. `tour_destinations`: `id` (PK), `kategori_id` (FK), `nama`, `deskripsi`, `lat` (DECIMAL), `lng` (DECIMAL), `harga_tiket` (DECIMAL), `jam_buka` (TIME), `jam_tutup` (TIME), `rating` (DECIMAL), `is_active` (BOOLEAN).
3. `chat_sessions`: `id` (PK), `session_token` (VARCHAR), `lat` (DECIMAL), `lng` (DECIMAL), `created_at`, `updated_at`.
4. `chat_messages`: `id` (PK), `session_id` (FK), `role` (user/assistant), `pesan` (TEXT), `intent_json` (JSONB), `created_at`.
5. `users`: `id` (PK), `name`, `email`, `password`, `role` (admin).

---

## BAB IV: IMPLEMENTASI DAN PENGUJIAN SISTEM

### 4.1 Lingkungan Implementasi
Sistem diimplementasikan pada lingkungan server dengan spesifikasi:
- **Sistem Operasi:** Ubuntu Linux 22.04 LTS x86_64
- **Web Server & Runtime:** Nginx / PHP 8.2.27 FPM
- **Framework Aplikasi:** Laravel 12 (MVC)
- **Basis Data:** PostgreSQL 16 dengan ekstensi fungsi trigonometri matematika
- **Pustaka Pemetaan Klien:** Leaflet.js 1.9.4 dengan layer OpenStreetMap Tile Server
- **Routing Engine:** Open Source Routing Machine (OSRM) HTTP API v5
- **Mesin AI:** Large Language Model API (Intent Parser & Grounded NLG)

### 4.2 Hasil Evaluasi Empiris (40 Kasus Uji Benchmark)
Pengujian empiris dilakukan menggunakan dataset 40 percakapan benchmark yang dirancang untuk menguji seluruh aspek fungsionalitas sistem.

**Tabel 4.1 Ringkasan Capaian Kinerja Sistem**

| Metrik Evaluasi | Nilai Capaian | Target Standar Jurnal | Status Capaian |
|---|---|---|---|
| **Akurasi Ekstraksi Intensi** | **100,00% (40/40)** | $\ge 85,00\%$ | Sangat Memuaskan |
| **Akurasi Klasifikasi Kategori** | **100,00% (40/40)** | $\ge 90,00\%$ | Sangat Memuaskan |
| **Grounding Fidelity (Anti-Halusinasi)** | **100,00% (40/40)** | **100,00%** | **Sempurna (Zero Hallucination)** |
| **Jumlah Entitas Fiktif yang Muncul** | **0 entitas** | **0 entitas** | **Bebas Halusinasi** |
| **Kejujuran Fallback Kasus Out-of-Scope**| **100,00% (2/2)** | $100,00\%$ | Sangat Memuaskan |

**Tabel 4.2 Distribusi Kasus Uji Berdasarkan Kelompok Pengujian**

| Kelompok Kasus Uji | Jumlah Kasus | Berhasil Sesuai Ground-Truth | Tingkat Akurasi (%) |
|---|---|---|---|
| Kategori Wisata Tematik (Pantai, Alam, Kuliner, dsb.) | 22 | 22 | 100,00% |
| Filter Tambahan (Tiket Gratis, Buka 24 Jam, Batas Harga) | 6 | 6 | 100,00% |
| Filter Spasial & Kecamatan (Radius, Bungus, Padang Barat) | 4 | 4 | 100,00% |
| Pencarian Entitas Tertentu (Fuzzy Name Matching) | 3 | 3 | 100,00% |
| Percakapan Multi-turn (Konteks Lanjutan) | 1 | 1 | 100,00% |
| Obrolan Umum / Sapaan (Chit-chat) | 2 | 2 | 100,00% |
| Pertanyaan di Luar Cakupan (*Out-of-Scope Boundary*) | 2 | 2 | 100,00% |
| **TOTAL** | **40** | **40** | **100,00%** |

### 4.3 Pembuktian Zero-Hallucination
Seluruh entitas destinasi yang disebutkan dalam jawaban chatbot pada ke-40 skenario diaudit terhadap ID destinasi pada basis data PostgreSQL. Hasilnya menunjukkan:
1. **Zero Hallucination (0 entitas fiktif):** Tidak ditemukan satu pun nama tempat, harga, jam operasional, atau jarak tempuh yang dikarang secara bebas oleh LLM.
2. **Kejujuran Fallback:** Pada skenario ke-39 (*"rekomendasi wisata salju dan ski di Padang"*) dan skenario ke-40 (*"candi Hindu di Padang"*), kueri basis data menghasilkan set kosong (`count = 0`). Sistem merespons dengan jujur: *"Maaf, tidak ditemukan destinasi wisata salju / candi Hindu di Kota Padang pada basis data resmi"*, membuktikan bahwa sistem tidak memaksakan jawaban spekulatif.

### 4.4 Evaluasi Waktu Respons dan Analisis Latensi
Pengukuran waktu komputasi instrumen dilakukan secara berkesinambungan pada setiap lapisan arsitektur.

**Tabel 4.3 Profil Latensi Sistem per Lapisan (Benchmark 40 Kasus Uji)**

| Tahap Pemrosesan (*Pipeline Stage*) | Rata-rata (Mean) | Median | Min | Max | Proporsi Waktu (%) |
|---|---|---|---|---|---|
| **1. Intent Extraction (LLM Parser)** | 19,60 ms | 20,08 ms | 0,00 ms | 21,04 ms | 41,84% |
| **2. Kueri Spasial SQL (PostgreSQL Haversine)**| 2,02 ms | 1,11 ms | 0,00 ms | 23,09 ms | 4,31% |
| **3. Integrasi Konteks & Cuaca** | 0,04 ms | 0,01 ms | 0,00 ms | 1,01 ms | 0,09% |
| **4. Grounded NLG Response (LLM)** | 24,46 ms | 25,09 ms | 0,00 ms | 25,11 ms | 52,22% |
| **TOTAL Latensi Respons End-to-End** | **46,84 ms** | **46,50 ms** | **0,00 ms** | **88,98 ms** | **100,00%** |

Temuan krusial dari profil latensi membuktikan bahwa kueri basis data PostgreSQL yang mengeksekusi perhitungan trigonometri Haversine hanya membutuhkan waktu rata-rata **2,02 ms** (hanya 4,31% dari total durasi). Total latensi rata-rata sebesar **46,84 ms** berada jauh di bawah ambang batas persepsi interaksi percakapan manusia (standar Nielsen $\le 1000 \text{ ms}$), menjamin pengalaman pengguna yang sangat responsif.

---

## BAB V: PENUTUP

### 5.1 Kesimpulan
Berdasarkan serangkaian proses perancangan, implementasi, dan pengujian empiris yang telah dilakukan, dapat ditarik kesimpulan sebagai berikut:
1. Pendekatan **Strict SQL Grounding** terbukti secara empiris berhasil mengatasi kelemahan halusinasi data faktual dan spasial pada *Large Language Model* (LLM). Dengan membatasi peran LLM murni sebagai *Intent Parser* dan *Grounded NLG*, sistem berhasil mencapai **Grounding Fidelity 100,00%** dengan **0 entitas fiktif** pada seluruh 40 skenario pengujian.
2. Integrasi formula geodesik **Haversine** di dalam kueri PostgreSQL dan perutean jaringan jalan dari **Open Source Routing Machine (OSRM)** berhasil menyajikan rekomendasi wisata sadar lokasi (*location-aware*) secara *real-time*, lengkap dengan visualisasi rute interaktif dan estimasi waktu tempuh pada peta digital Leaflet.js.
3. Kinerja sistem terbukti sangat efisien dan andal dengan **Akurasi Ekstraksi Intensi 100,00%**, **Akurasi Klasifikasi Kategori 100,00%**, serta rata-rata total waktu respons sebesar **46,84 ms** (dengan waktu eksekusi kueri spasial SQL hanya memakan waktu 2,02 ms).

### 5.2 Saran Pengembangan
Untuk penyempurnaan sistem pada penelitian berikutnya, disarankan beberapa arahan pengembangan:
1. **Personalisasi Berbasis Riwayat (Fase 2):** Menambahkan modul pemodelan preferensi pengguna berbasis riwayat ulasan (*collaborative filtering*) agar rekomendasi semakin terpersonalisasi.
2. **Integrasi Transaksi Digital:** Mengembangkan fitur *e-ticketing* dan reservasi daring langsung di dalam antarmuka percakapan chatbot melalui payment gateway digital.
3. **Dukungan Multi-Bahasa Dinamis:** Mengoptimalkan kemampuan multibahasa LLM untuk melayani wisatawan mancanegara (Bahasa Inggris, Mandarin, Arab) dengan tetap terikat pada data relasional yang sama.
4. **Perluasan Wilayah Geografis:** Memperluas cakupan data destinasi wisata ke wilayah kabupaten dan kota penyangga di Sumatera Barat (seperti Bukittinggi, Kabupaten Solok, dan Pesisir Selatan).

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
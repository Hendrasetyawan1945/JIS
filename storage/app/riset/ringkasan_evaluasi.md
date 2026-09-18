# Hasil Evaluasi & Pengujian Empiris Sistem (Bab 4 Jurnal)

**Waktu Pengujian**: 2026-09-18 22:48:38  
**Metode Evaluasi**: Black-box Testing, Grounding Fidelity Verification, Latency Breakdown  
**Mode**: MOCK (Deterministik Benchmark)  
**Total Skenario Uji**: 40 percakapan benchmark baku  

---

## 1. Evaluasi Akurasi Ekstraksi Intent & Kategori

Pendekatan hybrid LLM + SQL grounding mengandalkan model LLM hanya sebagai parser intent terstruktur (JSON). Tabel berikut menunjukkan hasil pengujian fungsionalitas ekstraksi parameter intent dari percakapan pengguna bahasa alami:

| Metrik Evaluasi | Nilai Tercapai | Target Minimum Jurnal | Status Capaian |
|---|---|---|---|
| **Akurasi Intent Penuh** | **100%** | >= 85.00% | Memenuhi Standar |
| **Akurasi Klasifikasi Kategori** | **100%** | >= 90.00% | Memenuhi Standar |
| **Grounding Fidelity (Anti-Halusinasi)** | **100%** | **100.00%** | **Sempurna (0 Halusinasi)** |
| **Jumlah Entitas Fiktif / Halusinasi** | **0 entitas** | **0 entitas** | **Bebas Halusinasi** |
| **Tingkat Kejujuran Fallback (Empty Query)** | **100%** | 100.00% | Memenuhi Standar |

### Rincian Akurasi per Kelompok Permintaan:

| Kelompok Pengujian | Jumlah Kasus Uji | Berhasil Sesuai Ground-Truth | Akurasi (%) |
|---|---|---|---|
| Kategori | 22 | 22 | 100% |
| Filter | 6 | 6 | 100% |
| Spasial | 4 | 4 | 100% |
| Entitas | 3 | 3 | 100% |
| Multi-turn | 1 | 1 | 100% |
| Chit-chat | 2 | 2 | 100% |
| Out-of-Scope | 2 | 2 | 100% |

---

## 2. Analisis Anti-Halusinasi (Grounding Data SQL)

Salah satu kebaruan (*novelty*) dari penelitian ini adalah penghapusan risiko halusinasi faktual melalui *Strict SQL Grounding*:
1. **100% Fakta Berbasis Data**: Seluruh entitas wisata yang direkomendasikan pada jawaban asisten (`100%`) terbukti secara deterministik berasal dari hasil query PostgreSQL.
2. **Zero Hallucination**: Tercatat **0 tempat fiktif** yang dihasilkan.
3. **Penanganan Batas Pengetahuan (*Out-of-Scope*)**: Ketika pengguna menanyakan wisata yang tidak ada dalam basis data (misal: "wisata salju di Padang" atau "candi Hindu"), sistem secara konsisten (100%) memberikan respons fallback jujur (*"Maaf, belum ada data wisata yang sesuai..."*) tanpa mengarang entitas baru.

---

## 3. Benchmark Waktu Respons (Latency Breakdown)

Pengukuran latensi dilakukan secara end-to-end dari saat pengguna mengirimkan pesan hingga respons diterima, dengan rincian waktu pemrosesan pada tiap lapisan sistem:

| Tahapan Pipeline Sistem | Rata-rata (Mean) | Median | Min | Max | Proporsi Waktu (%) |
|---|---|---|---|---|---|
| **1. Intent Extraction (LLM)** | 473.65 ms | 490.28 ms | 0 ms | 527.6 ms | 35.3% |
| **2. SQL Query (PostgreSQL Haversine)** | 1.21 ms | 1.09 ms | 0 ms | 3.41 ms | 0.1% |
| **3. Weather & Status Integration** | 0.02 ms | 0.01 ms | 0 ms | 0.56 ms | 0.0% |
| **4. Grounded Response (LLM)** | 865.32 ms | 881.96 ms | 0 ms | 959.88 ms | 64.5% |
| **TOTAL Latensi Respons End-to-End** | **1340.57 ms** | **1360.92 ms** | **0 ms** | **1465.91 ms** | **100.0%** |

### Temuan Pembahasan:
- Waktu eksekusi SQL pada basis data PostgreSQL dengan kalkulasi jarak Haversine sangat efisien (1.21 ms), membuktikan bahwa filtering spasial dan atribut di layer database tidak menjadi bottleneck sistem.
- Proporsi waktu terbesar berada pada pemanggilan API model bahasa (tahap 1 dan tahap 4), yang merupakan karakteristik wajar pada arsitektur hybrid LLM.

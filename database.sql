-- Sistem Pariwisata Kota Padang — Chatbot AI Rekomendasi (Fase 1)
-- MySQL 8.x

CREATE TABLE kategori (
  id   INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE wisata (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  kategori_id INT NOT NULL,
  nama        VARCHAR(100) NOT NULL,
  deskripsi   TEXT,
  alamat      VARCHAR(255),
  lat         DECIMAL(10,7) NOT NULL,
  lng         DECIMAL(10,7) NOT NULL,
  harga_tiket DECIMAL(12,0) NOT NULL DEFAULT 0,
  jam_buka    TIME,
  jam_tutup   TIME,
  rating      DECIMAL(2,1) NOT NULL DEFAULT 0,
  foto        VARCHAR(255),
  status_aktif TINYINT(1) NOT NULL DEFAULT 1,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_wisata_kategori FOREIGN KEY (kategori_id) REFERENCES kategori(id),
  INDEX idx_wisata_kategori (kategori_id),
  INDEX idx_wisata_aktif (status_aktif)
);

CREATE TABLE users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nama          VARCHAR(100) NOT NULL,
  email         VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('admin') NOT NULL DEFAULT 'admin',
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE chat_sessions (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  session_token VARCHAR(64) NOT NULL UNIQUE,
  lat           DECIMAL(10,7),
  lng           DECIMAL(10,7),
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE chat_messages (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  session_id INT NOT NULL,
  role       ENUM('user','assistant') NOT NULL,
  pesan      TEXT NOT NULL,
  intent_json JSON,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_msg_session FOREIGN KEY (session_id) REFERENCES chat_sessions(id) ON DELETE CASCADE,
  INDEX idx_msg_session (session_id)
);

INSERT INTO kategori (nama) VALUES
  ('Pantai'),
  ('Pulau'),
  ('Alam'),
  ('Museum'),
  ('Sejarah'),
  ('Kuliner');

-- Contoh data dummy (koordinat ilustrasi, ganti dengan data asli dari OpenStreetMap)
INSERT INTO wisata (kategori_id, nama, deskripsi, alamat, lat, lng, harga_tiket, jam_buka, jam_tutup, rating) VALUES
  (1, 'Pantai Air Manis', 'Pantai berpasir dengan legenda Batu Malin Kundang.', 'Kec. Padang Selatan', -0.9746000, 100.3626000, 10000, '07:00:00', '18:00:00', 4.5),
  (4, 'Museum Adityawarman', 'Museum provinsi dengan koleksi budaya Minangkabau.', 'Jl. Diponegoro, Padang', -0.9621000, 100.3617000, 5000, '08:00:00', '16:00:00', 4.3),
  (2, 'Pulau Sikuai', 'Pulau dengan resort dan spot snorkeling.', 'Kepulauan Bungus', -1.1650000, 100.3510000, 250000, '07:00:00', '17:00:00', 4.6);

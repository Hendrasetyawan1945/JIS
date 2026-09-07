-- Sistem Pariwisata Kota Padang — Chatbot AI Rekomendasi (Fase 1)
-- PostgreSQL 15 (jalankan via Laravel migration: php artisan migrate)
-- Data contoh via seeder: php artisan db:seed --class=WisataSeeder

CREATE TABLE kategori (
  id         BIGSERIAL PRIMARY KEY,
  nama       VARCHAR(50) NOT NULL UNIQUE,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

CREATE TABLE wisata (
  id                 BIGSERIAL PRIMARY KEY,
  kategori_id        BIGINT NOT NULL,
  nama               VARCHAR(100) NOT NULL,
  deskripsi          TEXT,
  alamat             VARCHAR(255),
  telepon           VARCHAR(30),
  lat                NUMERIC(10,7) NOT NULL,
  lng                NUMERIC(10,7) NOT NULL,
  harga_tiket        NUMERIC(12,0) NOT NULL DEFAULT 0,
  jam_buka           TIME,
  jam_tutup          TIME,
  rating             NUMERIC(2,1) NOT NULL DEFAULT 0,
  foto               TEXT,
  status_aktif       BOOLEAN NOT NULL DEFAULT TRUE,
  status_operasional VARCHAR(30) NOT NULL DEFAULT 'normal',
  catatan_status     TEXT,
  created_at         TIMESTAMP,
  updated_at         TIMESTAMP,
  CONSTRAINT fk_wisata_kategori FOREIGN KEY (kategori_id) REFERENCES kategori(id)
);

CREATE INDEX idx_wisata_kategori ON wisata (kategori_id);
CREATE INDEX idx_wisata_aktif ON wisata (status_aktif);

CREATE TABLE users (
  id            BIGSERIAL PRIMARY KEY,
  name          VARCHAR(255) NOT NULL,
  email         VARCHAR(255) NOT NULL UNIQUE,
  password      VARCHAR(255) NOT NULL,
  role          VARCHAR(20) NOT NULL DEFAULT 'user',
  remember_token VARCHAR(100),
  created_at    TIMESTAMP,
  updated_at    TIMESTAMP
);

CREATE TABLE chat_sessions (
  id            BIGSERIAL PRIMARY KEY,
  session_token VARCHAR(64) NOT NULL UNIQUE,
  lat           NUMERIC(10,7),
  lng           NUMERIC(10,7),
  created_at    TIMESTAMP,
  updated_at    TIMESTAMP
);

CREATE TABLE chat_messages (
  id         BIGSERIAL PRIMARY KEY,
  session_id BIGINT NOT NULL,
  role       VARCHAR(10) NOT NULL CHECK (role IN ('user', 'assistant')),
  pesan      TEXT NOT NULL,
  intent_json JSONB,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  CONSTRAINT fk_msg_session FOREIGN KEY (session_id) REFERENCES chat_sessions(id) ON DELETE CASCADE
);

CREATE INDEX idx_msg_session ON chat_messages (session_id);

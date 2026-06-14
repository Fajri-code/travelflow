-- Database TravelFlow
CREATE DATABASE IF NOT EXISTS travelflow;
USE travelflow;

CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    no_telp VARCHAR(30) DEFAULT NULL,
    alamat TEXT DEFAULT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE wisata (
    id_wisata INT AUTO_INCREMENT PRIMARY KEY,
    nama_wisata VARCHAR(150) NOT NULL,
    lokasi VARCHAR(150) NOT NULL,
    deskripsi TEXT,
    gambar VARCHAR(255),
    harga DECIMAL(10,2) NOT NULL
);

CREATE TABLE paket (
    id_paket INT AUTO_INCREMENT PRIMARY KEY,
    id_wisata INT NOT NULL,
    nama_paket VARCHAR(100) NOT NULL,
    durasi VARCHAR(50),
    harga DECIMAL(10,2) NOT NULL,
    deskripsi TEXT DEFAULT NULL,
    FOREIGN KEY (id_wisata) REFERENCES wisata(id_wisata)
);

CREATE TABLE kendaraan (
    id_kendaraan INT AUTO_INCREMENT PRIMARY KEY,
    nama_kendaraan VARCHAR(100) NOT NULL,
    kapasitas INT NOT NULL,
    harga DECIMAL(10,2) NOT NULL
);

CREATE TABLE transaksi (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_wisata INT NOT NULL,
    id_paket INT NOT NULL,
    id_kendaraan INT NOT NULL,
    jumlah_orang INT NOT NULL,
    tanggal DATE NOT NULL,
    nama_pemesan VARCHAR(150) DEFAULT NULL,
    no_telp VARCHAR(30) DEFAULT NULL,
    kode_booking VARCHAR(50) DEFAULT NULL,
    total DECIMAL(12,2) NOT NULL,
    status ENUM('pending','confirmed','cancelled') DEFAULT 'confirmed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id_user),
    FOREIGN KEY (id_wisata) REFERENCES wisata(id_wisata),
    FOREIGN KEY (id_paket) REFERENCES paket(id_paket),
    FOREIGN KEY (id_kendaraan) REFERENCES kendaraan(id_kendaraan)
);

CREATE TABLE notifikasi (
    id_notif INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    judul VARCHAR(150) NOT NULL,
    pesan TEXT NOT NULL,
    icon VARCHAR(50) DEFAULT 'bell',
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id_user)
);

CREATE TABLE ulasan (
    id_ulasan INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_wisata INT NOT NULL,
    id_transaksi INT NOT NULL,
    rating TINYINT(1) NOT NULL,
    komentar TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id_user),
    FOREIGN KEY (id_wisata) REFERENCES wisata(id_wisata),
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi)
);

-- Data Sample (removed default non-Jogja sample inserts)
-- NOTE: Sample inserts for Bali, Raja Ampat, and Lombok were removed to keep the database focused on Yogyakarta destinations.
-- If you need them later, re-add appropriate INSERT statements here.

INSERT INTO kendaraan (nama_kendaraan, kapasitas, harga) VALUES
('Motor (2 Orang)', 2, 100000),
('Mobil Pribadi (4 Orang)', 4, 300000),
('Minibus (10 Orang)', 10, 500000),
('Bus Medium (20 Orang)', 20, 800000),
('Bus Besar (40 Orang)', 40, 1200000);

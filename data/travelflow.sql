-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 02 Jul 2026 pada 18.46
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `travelflow`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `hotel`
--

CREATE TABLE `hotel` (
  `id_hotel` int(11) NOT NULL,
  `nama_hotel` varchar(150) NOT NULL,
  `lokasi` varchar(150) NOT NULL,
  `alamat` text DEFAULT NULL,
  `harga_per_malam` int(11) NOT NULL DEFAULT 0,
  `bintang` int(11) DEFAULT 3,
  `gambar` varchar(255) DEFAULT NULL,
  `fasilitas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `hotel`
--

INSERT INTO `hotel` (`id_hotel`, `nama_hotel`, `lokasi`, `alamat`, `harga_per_malam`, `bintang`, `gambar`, `fasilitas`) VALUES
(1, 'Hotel Tentrem', 'Yogyakarta', 'Jl. AM Sangaji No.72A, Yogyakarta', 850000, 5, 'tentrem.jpg', NULL),
(2, 'Grand Mercure Yogyakarta', 'Yogyakarta', 'Jl. Laksda Adisucipto No.38, Yogyakarta', 750000, 5, 'mercure.jpg', NULL),
(3, 'Hyatt Regency Yogyakarta', 'Sleman', 'Jl. Palagan Tentara Pelajar, Sleman', 950000, 5, 'hyatt.jpg', NULL),
(4, 'Hotel Melia Purosani', 'Yogyakarta', 'Jl. Suryotomo No.31, Yogyakarta', 650000, 4, 'melia.jpg', NULL),
(5, 'Ibis Styles Yogyakarta', 'Yogyakarta', 'Jl. Dagen No.57, Yogyakarta', 450000, 3, 'ibis.jpg', NULL),
(6, 'Guest House Malioboro', 'Yogyakarta', 'Jl. Sosrowijayan No.23, Yogyakarta', 250000, 2, 'guesthouse.jpg', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `itinerary`
--

CREATE TABLE `itinerary` (
  `id_itinerary` int(11) NOT NULL,
  `id_perjalanan` int(11) NOT NULL,
  `hari` int(11) NOT NULL,
  `id_wisata` int(11) NOT NULL,
  `urutan` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `itinerary`
--

INSERT INTO `itinerary` (`id_itinerary`, `id_perjalanan`, `hari`, `id_wisata`, `urutan`) VALUES
(1, 1, 1, 15, 1),
(2, 1, 2, 6, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kendaraan`
--

CREATE TABLE `kendaraan` (
  `id_kendaraan` int(11) NOT NULL,
  `nama_kendaraan` varchar(100) NOT NULL,
  `kapasitas` int(11) NOT NULL,
  `harga` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `kendaraan`
--

INSERT INTO `kendaraan` (`id_kendaraan`, `nama_kendaraan`, `kapasitas`, `harga`) VALUES
(1, 'Mobil + Sopir (4 Orang)', 4, 500000.00),
(2, 'Minibus + Sopir (10 Orang)', 10, 800000.00),
(3, 'Bus Medium + Sopir (20 Orang)', 20, 1200000.00),
(4, 'Bus Besar + Sopir (40 Orang)', 40, 300000.00),
(5, 'Motor (2 Orang)', 2, 100000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id_notif` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `judul` varchar(150) NOT NULL,
  `pesan` text NOT NULL,
  `icon` varchar(50) DEFAULT 'bell',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `notifikasi`
--

INSERT INTO `notifikasi` (`id_notif`, `id_user`, `judul`, `pesan`, `icon`, `is_read`, `created_at`) VALUES
(1, 1, 'Booking Berhasil! ðŸŽ‰', 'Booking ke Bali Paradise dengan kode TF-99E64136 telah dikonfirmasi. Total: Rp 2.000.000', 'booking', 1, '2026-05-16 18:14:48'),
(2, 1, 'Booking Berhasil! ðŸŽ‰', 'Booking ke Pantai Drini dengan kode TF-375F710D telah dikonfirmasi. Total: Rp 555.000', 'booking', 1, '2026-05-17 06:19:59'),
(3, 1, 'Booking Berhasil! ðŸŽ‰', 'Booking ke Alun-Alun Kidul dengan kode TF-238583CC telah dikonfirmasi. Total: Rp 330.000', 'booking', 1, '2026-05-17 06:48:06'),
(4, 1, 'Booking Berhasil! ðŸŽ‰', 'Booking ke Taman Sari Water Castle dengan kode TF-5DAD09E7 telah dikonfirmasi. Total: Rp 1.050.000', 'booking', 1, '2026-05-17 06:49:52'),
(5, 2, 'Booking Berhasil! ðŸŽ‰', 'Booking ke Candi Prambanan dengan kode TF-FE135F4C telah dikonfirmasi. Total: Rp 180.000', 'booking', 1, '2026-05-17 07:12:08'),
(6, 3, 'Booking Berhasil! ðŸŽ‰', 'Booking ke Gunung Merapi dengan kode TF-BF3C10B5 telah dikonfirmasi. Total: Rp 1.240.000', 'booking', 0, '2026-06-05 07:34:22'),
(7, 3, 'Booking Berhasil! ðŸŽ‰', 'Booking ke Taman Sari Water Castle dengan kode TF-3B13419C telah dikonfirmasi. Total: Rp 3.500.000', 'booking', 0, '2026-06-05 08:06:15'),
(8, 3, 'Booking Berhasil! ðŸŽ‰', 'Booking ke Taman Sari Water Castle dengan kode TF-ABAB46D6 telah dikonfirmasi. Total: Rp 2.750.000', 'booking', 0, '2026-06-05 08:15:25'),
(9, 3, 'Ulasan Terkirim â­', 'Terima kasih! Ulasan kamu dengan rating 4/5 sudah berhasil dikirim.', 'ulasan', 0, '2026-06-16 07:43:25'),
(10, 4, 'Booking Berhasil! ðŸŽ‰', 'Booking ke Pantai Drini dengan kode TF-AB74AB1A telah dikonfirmasi. Total: Rp 840.000', 'booking', 0, '2026-06-23 02:09:09'),
(11, 5, 'Booking Berhasil! ðŸŽ‰', 'Booking ke Gunung Merapi dengan kode TF-A4314E11 telah dikonfirmasi. Total: Rp 656.000', 'booking', 0, '2026-06-30 01:31:24'),
(12, 5, 'Ulasan Terkirim â­', 'Terima kasih! Ulasan kamu dengan rating 5/5 sudah berhasil dikirim.', 'ulasan', 0, '2026-06-30 01:32:24'),
(13, 5, 'Booking Berhasil! ðŸŽ‰', 'Booking ke Tugu Yogyakarta dengan kode TF-42FB7ADD telah dikonfirmasi. Total: Rp 4.100.000', 'booking', 0, '2026-07-02 11:22:45'),
(14, 5, 'Booking Berhasil! ðŸŽ‰', 'Booking ke Tebing Breksi dengan kode TF-682207FD telah dikonfirmasi. Total: Rp 300.000', 'booking', 0, '2026-07-02 11:22:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `paket`
--

CREATE TABLE `paket` (
  `id_paket` int(11) NOT NULL,
  `id_wisata` int(11) NOT NULL,
  `nama_paket` varchar(100) NOT NULL,
  `durasi` varchar(50) DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `paket`
--

INSERT INTO `paket` (`id_paket`, `id_wisata`, `nama_paket`, `durasi`, `harga`, `deskripsi`) VALUES
(1, 1, 'Paket Heritage Taman Sari', '3 Hari 2 Malam', 750000.00, 'Tur sejarah menuju istana air Taman Sari dengan cerita budaya dan spot foto klasik.'),
(2, 1, 'Paket Royal Taman Sari', '5 Hari 4 Malam', 1500000.00, 'Jelajahi kompleks keraton dan taman air Taman Sari dengan guide berpengalaman.'),
(5, 3, 'Paket Sunset Parangkusumo', '4 Hari 3 Malam', 3000000.00, 'Nikmati panorama gumuk pasir dan sunset Pantai Parangkusumo dengan suasana pantai unik.'),
(6, 4, 'Paket Adventure Kalibiru', '3 Hari 2 Malam', 850000.00, 'Petualangan alam di Kalibiru Nature Park dengan trekking ringan dan pemandangan perbukitan.'),
(7, 5, 'Paket Ekonomis', '1 Hari', 80000.00, NULL),
(8, 5, 'Paket Sunrise', '1 Hari', 150000.00, NULL),
(9, 6, 'Paket Foto', '2 Jam', 30000.00, NULL),
(10, 7, 'Paket Sunset', '3 Jam', 50000.00, NULL),
(11, 13, 'Paket Timang Gondola', '2 Jam', 120000.00, NULL),
(12, 18, 'Paket Candi', '1 Hari', 90000.00, NULL),
(13, 5, 'Paket Jeep Sunrise', '3 Jam', 175000.00, 'Safari jeep pagi hari menuju lereng Merapi, termasuk sunrise viewing dan pemandu lokal.'),
(14, 5, 'Paket Lava Tour', '4 Jam', 125000.00, 'Jelajahi rute lava lawas dengan jeep 4x4 dan spot foto terbaik.'),
(15, 5, 'Paket Camping Merapi', '1 Malam', 325000.00, 'Camping di kaki Merapi lengkap dengan tenda dan api unggun.'),
(16, 14, 'Paket City Tour', '3 Jam', 60000.00, 'Keliling Malioboro bersama guide lokal dan wisata belanja.'),
(17, 14, 'Paket Wisata Kuliner', '3 Jam', 75000.00, 'Menikmati kuliner khas Jogja di area Malioboro.'),
(18, 10, 'Paket ATV', '2 Jam', 95000.00, 'Bermain ATV di area pasir Pantai Parangtritis.'),
(19, 10, 'Paket Sunset Tour', '3 Jam', 70000.00, 'Menikmati sunset dan wisata pantai bersama guide.'),
(20, 18, 'Paket Prambanan Classic', '3 Jam', 125000.00, 'Tour Candi Prambanan lengkap dengan tiket dan guide.'),
(21, 18, 'Paket Ramayana Show', '4 Jam', 175000.00, 'Menonton Sendratari Ramayana di area Prambanan.'),
(22, 5, 'Paket Jeep Sunrise', '3 Jam', 175000.00, 'Safari jeep pagi hari menuju lereng Merapi, termasuk sunrise viewing dan pemandu lokal.'),
(25, 5, 'Paket Lava Tour', '4 Jam', 125000.00, 'Jelajahi rute lava lawas dengan jeep 4x4, berhenti di spot terbaik untuk foto dan cerita geologi.'),
(28, 5, 'Paket Camping Merapi', '1 Malam', 325000.00, 'Nikmati camping di kaki Merapi dengan tenda, api unggun, dan pengalaman malam di alam pegunungan.'),
(31, 6, 'Paket Heritage Breksi', '2 Jam', 45000.00, 'Tur singkat menjelajahi ukiran alam dan instalasi seni di area Tebing Breksi.'),
(34, 6, 'Paket Sunset Breksi', '2 Jam', 55000.00, 'Nikmati sweeping view dan sunset fotografi di kawasan Tebing Breksi.'),
(37, 7, 'Paket Night View', '3 Jam', 60000.00, 'Rasakan suasana malam Bukit Bintang dengan lampu kota dan view langit yang cerah.'),
(40, 7, 'Paket Stargazing', '4 Jam', 85000.00, 'Spot bintang dan sesi foto profesional di Bukit Bintang saat malam tiba.'),
(43, 8, 'Paket Foto Obelix', '2 Jam', 45000.00, 'Spot foto kreatif dan pengalaman panorama bukit di Obelix Hills.'),
(46, 8, 'Paket Cafe View', '3 Jam', 65000.00, 'Nikmati menu ringan dan pemandangan hijau sambil berkeliling spot Instagramable.'),
(49, 9, 'Paket Sky View Reguler', '2 Jam', 55000.00, 'Akses ke sky deck dan pemandangan lembah luas di HeHa Sky View.'),
(52, 9, 'Paket Dinner View', '3 Jam', 95000.00, 'Paket kuliner malam dengan pemandangan tebing dan lampu kota yang spektakuler.'),
(55, 10, 'Paket Reguler', '3 Jam', 40000.00, 'Paket standar kunjungan Pantai Parangtritis dengan fasilitas transportasi dan tiket masuk.'),
(58, 10, 'Paket ATV', '2 Jam', 95000.00, 'Rasakan pengalaman off-road dengan ATV di pasir Parangtritis yang menantang.'),
(61, 10, 'Paket Sunset Tour', '3 Jam', 70000.00, 'Nikmati sunset, photo shoot, dan menu ringan di tepi pantai Parangtritis.'),
(64, 11, 'Paket Pantai Santai', '3 Jam', 45000.00, 'Jelajah pasir putih, berenang, dan makan seafood segar di Pantai Indrayanti.'),
(67, 11, 'Paket Seafood Dinner', '3 Jam', 90000.00, 'Nikmati santap malam seafood favorit di tepi pantai Indrayanti.'),
(70, 12, 'Paket Snorkeling', '2 Jam', 85000.00, 'Snorkeling dan eksplorasi terumbu karang kecil di Pantai Drini.'),
(73, 12, 'Paket Relax Beach', '3 Jam', 50000.00, 'Paket santai dengan spot foto, makanan ringan, dan suasana tenang.'),
(76, 13, 'Paket Gondola Tradisional', '2 Jam', 125000.00, 'Naik gondola tradisional menuju karang Pantai Timang, dilengkapi pemandu profesional.'),
(79, 13, 'Paket Adventure Rock', '3 Jam', 165000.00, 'Paket petualangan ekstra dengan perjalanan ke spot paling eksotis di Timang.'),
(82, 14, 'Paket City Tour', '3 Jam', 60000.00, 'Jelajahi kawasan Malioboro, pasar tradisional, dan landmark kota bersama guide lokal.'),
(85, 14, 'Paket Wisata Kuliner', '3 Jam', 75000.00, 'Coba jajanan khas Jogja dan kuliner legendaris di sepanjang Malioboro.'),
(88, 15, 'Paket History Walk', '1.5 Jam', 30000.00, 'Tour singkat mengenal sejarah Tugu Yogyakarta dan spot foto klasik kota.'),
(91, 15, 'Paket Foto Ikonik', '1 Jam', 25000.00, 'Paket cepat untuk mengambil foto landmark dan kain tradisional di area Tugu.'),
(94, 16, 'Paket Keraton', '2 Jam', 65000.00, 'Tur lengkap Keraton Yogyakarta dengan guide, museum, dan persembahan budaya.'),
(97, 16, 'Paket Budaya Keraton', '3 Jam', 95000.00, 'Jelajahi seni, busana, dan ritual kesultanan dalam satu kunjungan mendalam.'),
(100, 17, 'Paket Malam Kidul', '2 Jam', 30000.00, 'Main kursi keliling, foto lampion, dan suguhan jajanan malam ala Alun-Alun Kidul.'),
(103, 17, 'Paket Lampion', '3 Jam', 55000.00, 'Pengalaman lampion, kuliner, dan permainan tradisional di Alun-Alun Kidul.'),
(106, 18, 'Paket Prambanan Classic', '3 Jam', 125000.00, 'Tour candi Prambanan lengkap dengan guide dan tiket masuk area utama.'),
(109, 18, 'Paket Ramayana Show', '4 Jam', 175000.00, 'Termasuk tiket pertunjukan sendratari Ramayana dan kunjungan sekitar candi.'),
(112, 19, 'Paket Sunset Ratu Boko', '2 Jam', 95000.00, 'Nikmati sunset yang epik di kompleks Ratu Boko dengan guide profesional.'),
(115, 19, 'Paket Fotografi Ratu Boko', '3 Jam', 115000.00, 'Sesi foto landscape dan sejarah di situs purbakala Ratu Boko.'),
(118, 20, 'Paket Guided Tour', '2 Jam', 85000.00, 'Tour museum Ullen Sentalu bersama pemandu untuk koleksi budaya Jawa.'),
(121, 20, 'Paket Edukasi Budaya', '3 Jam', 120000.00, 'Sesi mendalam tentang seni batik, keraton, dan kebudayaan Jawa.'),
(124, 21, 'Paket Skyview Kuliner', '3 Jam', 90000.00, 'Paket kuliner dan spot foto di HeHa Ocean View untuk suasana sunset.'),
(127, 21, 'Paket Foodie Trip', '3 Jam', 95000.00, 'Cicipi aneka kuliner kekinian di area HeHa Ocean View dengan guide lokal.'),
(130, 22, 'Paket Explorer Castle', '2 Jam', 80000.00, 'Jelajah replika kastil dan area foto tematik di Lost World Castle.'),
(133, 22, 'Paket Foto Kastil', '3 Jam', 110000.00, 'Sesi foto bertema di istana mini dan taman bergaya Eropa.'),
(136, 23, 'Paket Syuting Tradisional', '2 Jam', 85000.00, 'Perjalanan ke replika desa tradisional dan spot syuting di Studio Alam Gamplong.'),
(139, 23, 'Paket Edukasi Desa', '3 Jam', 105000.00, 'Pelajari produksi film, set tradisional, dan cerita Jawa di Gamplong.');

-- --------------------------------------------------------

--
-- Struktur dari tabel `perjalanan`
--

CREATE TABLE `perjalanan` (
  `id_perjalanan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `tanggal_berangkat` date NOT NULL,
  `durasi` varchar(50) NOT NULL,
  `jumlah_hari` int(11) NOT NULL,
  `jumlah_malam` int(11) NOT NULL,
  `id_kendaraan` int(11) DEFAULT NULL,
  `jumlah_peserta` int(11) DEFAULT 1,
  `id_hotel` int(11) DEFAULT NULL,
  `titik_jemput` varchar(150) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `perjalanan`
--

INSERT INTO `perjalanan` (`id_perjalanan`, `id_user`, `tanggal_berangkat`, `durasi`, `jumlah_hari`, `jumlah_malam`, `id_kendaraan`, `jumlah_peserta`, `id_hotel`, `titik_jemput`, `status`, `created_at`) VALUES
(1, 5, '2026-07-03', '2H1M', 2, 1, 2, 10, 5, 'Hotel - Ibis Styles Yogyakarta', 'draft', '2026-07-02 11:22:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_wisata` int(11) NOT NULL,
  `id_paket` int(11) NOT NULL,
  `id_kendaraan` int(11) NOT NULL,
  `id_hotel` int(11) DEFAULT NULL,
  `jumlah_hari` int(11) DEFAULT 1,
  `titik_jemput` varchar(150) DEFAULT NULL,
  `jumlah_orang` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `nama_pemesan` varchar(150) DEFAULT NULL,
  `no_telp` varchar(30) DEFAULT NULL,
  `kode_booking` varchar(50) DEFAULT NULL,
  `total` decimal(12,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'confirmed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_user`, `id_wisata`, `id_paket`, `id_kendaraan`, `id_hotel`, `jumlah_hari`, `titik_jemput`, `jumlah_orang`, `tanggal`, `nama_pemesan`, `no_telp`, `kode_booking`, `total`, `status`, `created_at`) VALUES
(1, 1, 1, 2, 1, NULL, 1, NULL, 1, '2026-05-18', 'Fajri', '1', 'TF-99E64136', 2000000.00, 'confirmed', '2026-05-16 18:14:48'),
(2, 1, 12, 70, 4, NULL, 1, NULL, 3, '2026-05-20', 'Fajri', '123', 'TF-375F710D', 555000.00, 'confirmed', '2026-05-17 06:19:59'),
(3, 1, 17, 100, 4, NULL, 1, NULL, 1, '2026-05-18', 'Fajri', '123', 'TF-238583CC', 330000.00, 'confirmed', '2026-05-17 06:48:06'),
(4, 1, 1, 1, 4, NULL, 1, NULL, 1, '2026-05-17', 'Fajri', '1234567', 'TF-5DAD09E7', 1050000.00, 'confirmed', '2026-05-17 06:49:52'),
(5, 2, 18, 20, 5, NULL, 1, NULL, 1, '2026-05-18', 'DAFA WIBOWO', '1234567', 'TF-FE135F4C', 180000.00, 'confirmed', '2026-05-17 07:12:08'),
(6, 3, 5, 8, 1, NULL, 1, NULL, 7, '2026-06-06', 'Ayan', '1234567', 'TF-BF3C10B5', 1240000.00, 'confirmed', '2026-06-05 07:34:22'),
(7, 3, 1, 1, 1, NULL, 1, NULL, 4, '2026-06-06', 'adixfabien', '1', 'TF-3B13419C', 3500000.00, 'confirmed', '2026-06-05 08:06:15'),
(8, 3, 1, 1, 1, NULL, 1, NULL, 3, '2026-06-17', 'adixfabien', '2', 'TF-ABAB46D6', 2750000.00, 'confirmed', '2026-06-05 08:15:25'),
(9, 4, 12, 70, 1, NULL, 1, NULL, 4, '2026-06-24', 'Vemas haref', '34', 'TF-AB74AB1A', 840000.00, 'confirmed', '2026-06-23 02:09:09'),
(10, 5, 5, 7, 1, NULL, 1, NULL, 4, '2026-07-01', 'Vemas', '1234', 'TF-A4314E11', 656000.00, 'confirmed', '2026-06-30 01:31:24'),
(11, 5, 15, 91, 2, NULL, 1, NULL, 10, '2026-07-03', 'Vemas', '123456', 'TF-42FB7ADD', 4100000.00, 'confirmed', '2026-07-02 11:22:45'),
(12, 5, 6, 9, 2, NULL, 1, NULL, 10, '2026-07-03', 'Vemas', '123456', 'TF-682207FD', 300000.00, 'confirmed', '2026-07-02 11:22:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ulasan`
--

CREATE TABLE `ulasan` (
  `id_ulasan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_wisata` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `komentar` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `ulasan`
--

INSERT INTO `ulasan` (`id_ulasan`, `id_user`, `id_wisata`, `id_transaksi`, `rating`, `komentar`, `created_at`) VALUES
(1, 3, 1, 8, 4, 'bagus\r\n', '2026-06-16 07:43:25'),
(2, 5, 5, 10, 5, 'sanggat bagus', '2026-06-30 01:32:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_telp` varchar(30) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `nama`, `email`, `password`, `no_telp`, `alamat`, `foto`, `created_at`) VALUES
(1, 'Fajri', 'refifjrn14@gmail.com', '$2y$10$33WXZMNblAIWEJp6X.Mog..0xlXbGHF3dMBYkLJQHhVaNSGfIlchm', NULL, NULL, NULL, '2026-05-16 18:12:37'),
(2, 'DAFA WIBOWO', 'dafa@gmail.com', '$2y$10$9YCo.9XQ5z7GIM2/0PaidODlagOfqZa0Q74HQ4eJjLRg6GAkytBiO', NULL, NULL, NULL, '2026-05-17 07:10:51'),
(3, 'adixfabien', 'Adiyanto@gmail.com', '$2y$10$PKSuBmSFnhugWxrK1bid5OVWNNJQAzrfbvM9QKxjrCB4rk2a0EcFi', '', 'legok selatan', 'user_3.jpeg', '2026-06-05 07:31:19'),
(4, 'Vemas haref', 'Vemas@gmail.com', '$2y$10$7Rl1ediw2dB5wZ5OIyZ.luZ49Yc0dh10NvxJ2U3tlDQsOmqDerXWm', NULL, NULL, NULL, '2026-06-23 01:35:09'),
(5, 'Vemas', 'Vemasharefa@gmail.com', '$2y$10$qyS3GAXH1Jciuzjuf8T0SOpwl32N/lWrZ0w7LV2bEmP/CDw1Oho9G', NULL, NULL, NULL, '2026-06-30 00:53:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `wisata`
--

CREATE TABLE `wisata` (
  `id_wisata` int(11) NOT NULL,
  `nama_wisata` varchar(150) NOT NULL,
  `lokasi` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `kategori` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `wisata`
--

INSERT INTO `wisata` (`id_wisata`, `nama_wisata`, `lokasi`, `deskripsi`, `gambar`, `harga`, `kategori`) VALUES
(1, 'Taman Sari Water Castle', 'Kota Yogyakarta', 'Istana air klasik Sultan Yogyakarta dengan lorong bawah tanah dan kolam pemandian. Jam Operasional: 09:00-15:30 | Fasilitas: Guide, Parkir, Spot Foto, Toilet | Rating: 4.3', 'Tamansari.jpg', 20000.00, 'Lainnya'),
(3, 'Gumuk Pasir Parangkusumo', 'Kretek, Bantul', 'Bukit pasir pantai dengan pemandangan unik dan spot ATV. Jam Operasional: 06:00-17:00 | Fasilitas: Parkir, ATV, Warung, Toilet | Rating: 4.1', 'Gunungpasir.jpg', 25000.00, 'Lainnya'),
(4, 'Kalibiru Nature Park', 'Kulon Progo', 'Viewpoint hutan pinus dengan spot foto menawan dan jalur tracking. Jam Operasional: 06:00-17:00 | Fasilitas: Parkir, Guide, Spot Foto, Toilet | Rating: 4.4', 'kalibiru.jpg', 15000.00, 'Lainnya'),
(5, 'Gunung Merapi', 'Sleman, Yogyakarta', 'Ikon alam yang spektakuler dengan pemandangan kawah dan sunrise menawan. Jam Operasional: 04:00-17:00 | Fasilitas: Parkir, Pemandu, Toilet, Warung Makan | Rating: 4.7 | Kategori: Wisata Alam', 'merapi.jpg', 50000.00, 'Wisata Alam'),
(6, 'Tebing Breksi', 'Sewu, Sleman', 'Formasi batu kapur unik dengan spot foto kontemporer, cocok untuk sunset pendek. Jam Operasional: 06:00-18:00 | Fasilitas: Parkir, Toilet, Area Foto, Kedai Kopi | Rating: 4.4 | Kategori: Wisata Alam', 'breksi.jpg', 20000.00, 'Wisata Alam'),
(7, 'Bukit Bintang', 'Patuk, Gunungkidul', 'Viewpoint populer untuk melihat langit malam dan kota Yogyakarta. Jam Operasional: 16:00-23:00 | Fasilitas: Parkir, Warung, Spot Foto | Rating: 4.5 | Kategori: Wisata Alam', 'bukit_bintang.jpg', 10000.00, 'Wisata Alam'),
(8, 'Obelix Hills', 'Patuk, Gunungkidul', 'Destinasi foto dengan panorama bukit dan instalasi kreatif. Jam Operasional: 08:00-18:00 | Fasilitas: Parkir, Toilet, Cafe, Spot Foto | Rating: 4.3 | Kategori: Wisata Alam', 'obelix_hills.jpg', 40000.00, 'Wisata Alam'),
(9, 'HeHa Sky View', 'Dlingo, Bantul', 'Kombinasi kuliner dan view tebing dengan pemandangan luas. Jam Operasional: 09:00-21:00 | Fasilitas: Parkir, Restoran, Spot Foto, Toilet | Rating: 4.2 | Kategori: Wisata Alam', 'heha_sky.jpg', 50000.00, 'Wisata Alam'),
(10, 'Pantai Parangtritis', 'Kretek, Bantul', 'Pantai legendaris dengan panorama laut dan gumuk pasir. Jam Operasional: 05:00-18:00 | Fasilitas: Parkir, Warung, Perahu, Ojek Pantai | Rating: 4.1 | Kategori: Pantai', 'parangtritis.jpg', 15000.00, 'Pantai'),
(11, 'Pantai Indrayanti', 'Bantul', 'Pasir putih dan pantai yang populer untuk berenang dan kuliner seafood. Jam Operasional: 06:00-18:00 | Fasilitas: Parkir, Restoran, Toilet, Perahu | Rating: 4.3 | Kategori: Pantai', 'indrayanti.jpg', 20000.00, 'Pantai'),
(12, 'Pantai Drini', 'Gunung Kidul', 'Pantai tenang dengan laguna kecil dan spot snorkeling. Jam Operasional: 06:00-17:00 | Fasilitas: Parkir, Warung, Toilet, Snorkeling | Rating: 4.4 | Kategori: Pantai', 'drini.jpg', 15000.00, 'Pantai'),
(13, 'Pantai Timang', 'Gunung Kidul', 'Terkenal dengan gondola tradisional menuju karang dan panorama eksotis. Jam Operasional: 06:00-17:00 | Fasilitas: Parkir, Pemandu, Warung, Gondola | Rating: 4.6 | Kategori: Pantai', 'timang.jpg', 30000.00, 'Pantai'),
(14, 'Malioboro', 'Kota Yogyakarta', 'Pusat belanja dan budaya jalan kaki dengan suasana khas Malioboro. Jam Operasional: 08:00-22:00 | Fasilitas: Parkir, Kuliner, Suvenir, Jalan Kaki | Rating: 4.0 | Kategori: Kota/Ikonik', 'malioboro.jpg', 25000.00, 'Kota/Ikonik'),
(15, 'Tugu Yogyakarta', 'Kota Yogyakarta', 'Landmark bersejarah yang jadi simbol kota, cocok untuk foto singkat. Jam Operasional: 24 Jam | Fasilitas: Parkir Terdekat, Spot Foto | Rating: 4.2 | Kategori: Kota/Ikonik', 'tugu.jpg', 15000.00, 'Kota/Ikonik'),
(16, 'Keraton Yogyakarta', 'Kota Yogyakarta', 'Kediaman Kesultanan dengan arsitektur dan kultur yang kaya. Jam Operasional: 08:30-14:00 | Fasilitas: Guide, Museum, Parkir, Toilet | Rating: 4.5 | Kategori: Kota/Ikonik', 'keraton.jpg', 25000.00, 'Kota/Ikonik'),
(17, 'Alun-Alun Kidul', 'Kota Yogyakarta', 'Ruang publik dengan tradisi main kursi keliling dan lampion. Jam Operasional: 06:00-23:00 | Fasilitas: Parkir, Kuliner, Permainan | Rating: 4.0 | Kategori: Kota/Ikonik', 'alun_alun_kidul.jpg', 20000.00, 'Kota/Ikonik'),
(18, 'Candi Prambanan', 'Sleman/Prambanan', 'Candi Hindu megah dengan relief dan pertunjukan tari Ramayana. Jam Operasional: 06:00-17:00 | Fasilitas: Museum, Guide, Parkir, Toilet | Rating: 4.8 | Kategori: Budaya & Sejarah', 'prambanan.jpg', 50000.00, 'Budaya & Sejarah'),
(19, 'Candi Ratu Boko', 'Prambanan', 'Kompleks purbakala dengan pemandangan sunset yang dramatis. Jam Operasional: 06:00-17:30 | Fasilitas: Parkir, Guide, Toilet, Spot Foto | Rating: 4.5 | Kategori: Budaya & Sejarah', 'ratu_boko.jpg', 75000.00, 'Budaya & Sejarah'),
(20, 'Museum Ullen Sentalu', 'Pakem, Sleman', 'Museum budaya Jawa dengan koleksi artefak dan pameran mendalam. Jam Operasional: 09:00-16:00 | Fasilitas: Parkir, Guide, Cafe | Rating: 4.4 | Kategori: Budaya & Sejarah', 'ullen_sentalu.jpg', 35000.00, 'Budaya & Sejarah'),
(21, 'HeHa Ocean View', 'Dlingo, Bantul', 'Area kuliner dan viewpoint populer di kalangan anak muda. Jam Operasional: 09:00-22:00 | Fasilitas: Parkir, Cafe, Spot Foto | Rating: 4.3 | Kategori: Viral', 'heha_ocean.jpg', 45000.00, 'Viral'),
(22, 'The Lost World Castle', 'Nglipar, Gunungkidul', 'Replika kastil bergaya Eropa yang hits untuk foto bertema. Jam Operasional: 08:00-18:00 | Fasilitas: Parkir, Spot Foto, Cafe | Rating: 4.2 | Kategori: Viral', 'lost_world.jpg', 30000.00, 'Viral'),
(23, 'Studio Alam Gamplong', 'Sleman', 'Lokasi syuting dan replika desa tradisional; spot foto dan edukasi budaya. Jam Operasional: 08:00-17:00 | Fasilitas: Parkir, Guide, Spot Foto | Rating: 4.4 | Kategori: Viral', 'gamplong.jpg', 30000.00, 'Viral');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `hotel`
--
ALTER TABLE `hotel`
  ADD PRIMARY KEY (`id_hotel`);

--
-- Indeks untuk tabel `itinerary`
--
ALTER TABLE `itinerary`
  ADD PRIMARY KEY (`id_itinerary`);

--
-- Indeks untuk tabel `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD PRIMARY KEY (`id_kendaraan`);

--
-- Indeks untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id_notif`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `paket`
--
ALTER TABLE `paket`
  ADD PRIMARY KEY (`id_paket`),
  ADD KEY `id_wisata` (`id_wisata`);

--
-- Indeks untuk tabel `perjalanan`
--
ALTER TABLE `perjalanan`
  ADD PRIMARY KEY (`id_perjalanan`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_wisata` (`id_wisata`),
  ADD KEY `id_paket` (`id_paket`),
  ADD KEY `id_kendaraan` (`id_kendaraan`);

--
-- Indeks untuk tabel `ulasan`
--
ALTER TABLE `ulasan`
  ADD PRIMARY KEY (`id_ulasan`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_wisata` (`id_wisata`),
  ADD KEY `id_transaksi` (`id_transaksi`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `wisata`
--
ALTER TABLE `wisata`
  ADD PRIMARY KEY (`id_wisata`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `hotel`
--
ALTER TABLE `hotel`
  MODIFY `id_hotel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `itinerary`
--
ALTER TABLE `itinerary`
  MODIFY `id_itinerary` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `kendaraan`
--
ALTER TABLE `kendaraan`
  MODIFY `id_kendaraan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id_notif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `paket`
--
ALTER TABLE `paket`
  MODIFY `id_paket` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT untuk tabel `perjalanan`
--
ALTER TABLE `perjalanan`
  MODIFY `id_perjalanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `ulasan`
--
ALTER TABLE `ulasan`
  MODIFY `id_ulasan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `wisata`
--
ALTER TABLE `wisata`
  MODIFY `id_wisata` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Ketidakleluasaan untuk tabel `paket`
--
ALTER TABLE `paket`
  ADD CONSTRAINT `paket_ibfk_1` FOREIGN KEY (`id_wisata`) REFERENCES `wisata` (`id_wisata`);

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_wisata`) REFERENCES `wisata` (`id_wisata`),
  ADD CONSTRAINT `transaksi_ibfk_3` FOREIGN KEY (`id_paket`) REFERENCES `paket` (`id_paket`),
  ADD CONSTRAINT `transaksi_ibfk_4` FOREIGN KEY (`id_kendaraan`) REFERENCES `kendaraan` (`id_kendaraan`);

--
-- Ketidakleluasaan untuk tabel `ulasan`
--
ALTER TABLE `ulasan`
  ADD CONSTRAINT `ulasan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `ulasan_ibfk_2` FOREIGN KEY (`id_wisata`) REFERENCES `wisata` (`id_wisata`),
  ADD CONSTRAINT `ulasan_ibfk_3` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

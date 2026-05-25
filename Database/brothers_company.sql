-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 25 Bulan Mei 2026 pada 17.58
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
-- Database: `brothers_company`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `accepted_laporan`
--

CREATE TABLE `accepted_laporan` (
  `id` int(11) NOT NULL,
  `source_type` enum('mechanic','farmer') NOT NULL COMMENT 'Sumber laporan asli',
  `source_id` int(11) NOT NULL COMMENT 'ID dari tabel laporan atau laporan_farmer',
  `nama_karyawan` varchar(100) NOT NULL,
  `divisi` enum('mechanic','farmer') NOT NULL,
  `jumlah_used` int(11) NOT NULL DEFAULT 0 COMMENT 'Compo used (mechanic) atau Bibit used (farmer)',
  `jumlah_value` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Money stored ($) atau Panen hasil (kg)',
  `harga_rate` decimal(15,2) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal_laporan` date NOT NULL COMMENT 'Tanggal laporan asli',
  `tanggal_accept` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Tanggal accept',
  `accepted_by` varchar(100) DEFAULT NULL COMMENT 'Admin yang accept',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `pinned` tinyint(1) DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `category`, `pinned`, `created_by`, `created_at`) VALUES
(17, 'Welcome to Brotherscompany.site', 'Ini ada website Brothers Company, enjoy...', 'Company', 1, 1, '2026-05-10 18:04:08'),
(19, 'Pernikahan Marc dengan Anin', 'Diumumkan nanti TBA pastinya kami mengundang seluruh kawan-kawan yang ingin hadir di acara Chief Of HRD kita ini kawan!!!', 'Company', 1, 1, '2026-05-10 18:06:40'),
(20, 'New Website Bortherscompany.site', 'New Website Bortherscompany.site, web baru brothers ni kawan tapi webnya masih tahap pengembangan', 'Company', 0, 1, '2026-05-10 18:07:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `compliment_rules`
--

CREATE TABLE `compliment_rules` (
  `id` int(11) NOT NULL,
  `threshold` int(11) NOT NULL,
  `gift` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `compliment_rules`
--

INSERT INTO `compliment_rules` (`id`, `threshold`, `gift`, `is_active`, `created_at`) VALUES
(1, 5000, 'Makanan dari Restoran', 1, '2026-05-23 15:39:21'),
(2, 1000, 'Snack + Air Minum', 1, '2026-05-23 15:39:21'),
(3, 150, 'Snack', 1, '2026-05-23 15:39:21'),
(4, 0, 'Air Minum', 1, '2026-05-23 15:39:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `delivery_order`
--

CREATE TABLE `delivery_order` (
  `id` int(11) NOT NULL,
  `jenis_delivery` enum('compo','farmer','farmer_beli','farmer_jual') NOT NULL,
  `alamat_tujuan` varchar(255) NOT NULL,
  `nama_penerima` varchar(100) NOT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `jumlah_crate` int(11) NOT NULL DEFAULT 1,
  `harga_snapshot` decimal(15,2) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `status` enum('pending','diambil','selesai','batal') NOT NULL DEFAULT 'pending',
  `driver_id` int(11) DEFAULT NULL,
  `driver_nama` varchar(100) DEFAULT NULL,
  `tanggal_input` datetime NOT NULL DEFAULT current_timestamp(),
  `tanggal_ambil` datetime DEFAULT NULL,
  `tanggal_selesai` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `delivery_order`
--

INSERT INTO `delivery_order` (`id`, `jenis_delivery`, `alamat_tujuan`, `nama_penerima`, `no_telepon`, `jumlah_crate`, `harga_snapshot`, `catatan`, `status`, `driver_id`, `driver_nama`, `tanggal_input`, `tanggal_ambil`, `tanggal_selesai`, `created_at`, `updated_at`) VALUES
(1, 'compo', 'Jl. Merdeka No. 10, Bandung', 'Budi Santoso', '081234567890', 10, 1000.00, 'Komponen mesin produksi', 'pending', NULL, NULL, '2026-05-23 15:39:20', NULL, NULL, '2026-05-23 15:39:20', '2026-05-23 16:02:11'),
(2, 'compo', 'Jl. Asia Afrika No. 25, Bandung', 'Dewi Lestari', '085678901234', 5, 1000.00, 'Suku cadang motor', 'pending', NULL, NULL, '2026-05-23 15:39:20', NULL, NULL, '2026-05-23 15:39:20', '2026-05-23 16:02:11'),
(3, 'farmer', 'Jl. Ganesha No. 5, Bandung', 'Ahmad Rizki', '087812345678', 20, 20.00, 'Bibit cabai organik', 'pending', NULL, NULL, '2026-05-23 15:39:20', NULL, NULL, '2026-05-23 15:39:20', '2026-05-23 16:02:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `no_rekening` varchar(50) DEFAULT NULL,
  `divisi` enum('Mechanic','Farmer','Cargo Driver','Manager') NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `tanggal_masuk` date DEFAULT curdate(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `employees`
--

INSERT INTO `employees` (`id`, `nama`, `telepon`, `no_rekening`, `divisi`, `status`, `tanggal_masuk`, `created_at`, `updated_at`) VALUES
(1, 'Joko Susanto', '081234567890', '1234567890 BCA', 'Mechanic', 'active', '2026-05-23', '2026-05-23 15:39:20', '2026-05-23 15:39:20'),
(2, 'Ahmad Rizki', '085678901234', '0987654321 Mandiri', 'Farmer', 'active', '2026-05-23', '2026-05-23 15:39:20', '2026-05-23 15:39:20'),
(3, 'Dewi Lestari', '087812345678', '5678901234 BNI', 'Cargo Driver', 'active', '2026-05-23', '2026-05-23 15:39:20', '2026-05-23 15:39:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `farm_price_config`
--

CREATE TABLE `farm_price_config` (
  `id` int(11) NOT NULL,
  `config_key` varchar(100) NOT NULL,
  `config_label` varchar(200) NOT NULL,
  `config_value` decimal(15,2) NOT NULL,
  `config_unit` varchar(50) DEFAULT NULL,
  `category` enum('cargo','farm','mechanic') NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `farm_price_config`
--

INSERT INTO `farm_price_config` (`id`, `config_key`, `config_label`, `config_value`, `config_unit`, `category`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'cargo_gaji_per_crate', 'Gaji Cargo per Crate', 63.00, '$/crate', 'cargo', 'Gaji untuk cargo driver per 1 crate yang diantar', '2026-05-23 15:39:21', '2026-05-23 17:30:17'),
(2, 'farm_gaji_per_bibit', 'Gaji Petani per Bibit Ditanam', 22.00, '$/bibit', 'farm', 'Gaji untuk farmer per bibit yang berhasil ditanam', '2026-05-23 15:39:21', '2026-05-23 15:39:21'),
(3, 'farm_harga_jual_buah', 'Harga Jual Buah', 40.00, '$/kg', 'farm', 'Harga jual hasil panen buah per kilogram', '2026-05-23 15:39:21', '2026-05-23 17:24:02'),
(4, 'farm_harga_bibit', 'Harga Bibit', 20.00, '$/kg', 'farm', 'Harga beli bibit untuk penanaman', '2026-05-23 15:39:21', '2026-05-23 15:39:21'),
(5, 'mechanic_gaji_dasar', 'Gaji Pokok Mechanic', 0.00, '$', 'mechanic', 'Gaji pokok mechanic per periode', '2026-05-23 15:39:21', '2026-05-25 07:54:46'),
(6, 'mechanic_harga_component', 'Harga Component Sparepart', 1000.00, '$/crate', 'mechanic', 'Harga sparepart/component mechanic per crate', '2026-05-23 15:39:21', '2026-05-23 15:39:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan`
--

CREATE TABLE `laporan` (
  `id` int(11) NOT NULL,
  `nama_karyawan` varchar(100) NOT NULL,
  `compo_used` int(11) NOT NULL DEFAULT 0,
  `money_stored` decimal(15,2) NOT NULL DEFAULT 0.00,
  `keterangan` text DEFAULT NULL,
  `tanggal` date DEFAULT curdate(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan_farmer`
--

CREATE TABLE `laporan_farmer` (
  `id` int(11) NOT NULL,
  `nama_karyawan` varchar(100) NOT NULL,
  `bibit_used` int(11) NOT NULL DEFAULT 0,
  `panen_hasil` decimal(15,2) NOT NULL DEFAULT 0.00,
  `keterangan` text DEFAULT NULL,
  `tanggal` date DEFAULT curdate(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `marketplace_posts`
--

CREATE TABLE `marketplace_posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_barang` varchar(200) NOT NULL,
  `jenis_barang` varchar(100) DEFAULT NULL,
  `no_telpon` varchar(20) DEFAULT NULL,
  `discord_id` varchar(100) DEFAULT NULL,
  `foto_barang` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_featured` tinyint(1) DEFAULT 0,
  `featured_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `marketplace_posts`
--

INSERT INTO `marketplace_posts` (`id`, `user_id`, `nama_barang`, `jenis_barang`, `no_telpon`, `discord_id`, `foto_barang`, `keterangan`, `created_at`, `is_featured`, `featured_expiry`) VALUES
(15, 10, 'obei A5', 'Mobil', '447294', 'sndoua221', '1778439092_Grand_Theft_Auto_V_Screenshot_2026.04.26_-_13.21.37.05.png', 'Mulus banget Odo kecil velg R17 full up', '2026-05-10 18:51:32', 0, NULL),
(16, 12, 'TERMINUS', 'CAR SUV', '6661', 'earlyfast', '1778439638_2026-5-11 1-57-54 339.png', 'palalo', '2026-05-10 19:00:38', 0, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `mechanic_component_price`
--

CREATE TABLE `mechanic_component_price` (
  `id` int(11) NOT NULL,
  `harga_per_component` decimal(15,2) DEFAULT 1.00,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mechanic_component_price`
--

INSERT INTO `mechanic_component_price` (`id`, `harga_per_component`, `updated_at`) VALUES
(1, 1.00, '2026-05-23 18:56:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `telepon` varchar(20) NOT NULL,
  `compo_used` int(11) DEFAULT 0,
  `last_compo_update` datetime DEFAULT NULL,
  `tanggal_daftar` date DEFAULT curdate(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `members`
--

INSERT INTO `members` (`id`, `nama`, `telepon`, `compo_used`, `last_compo_update`, `tanggal_daftar`, `created_at`, `updated_at`) VALUES
(1, 'Budi Santoso', '081234567890', 0, NULL, '2026-05-23', '2026-05-23 15:39:20', '2026-05-23 15:39:20'),
(2, 'Andi Wijaya', '085678901234', 0, NULL, '2026-05-23', '2026-05-23 15:39:20', '2026-05-23 15:39:20'),
(3, 'Citra Dewi', '087812345678', 0, NULL, '2026-05-23', '2026-05-23 15:39:20', '2026-05-23 15:39:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `price_config`
--

CREATE TABLE `price_config` (
  `id` int(11) NOT NULL,
  `jenis_layanan` varchar(50) NOT NULL,
  `nama_layanan` varchar(100) NOT NULL,
  `multiplier` decimal(5,2) NOT NULL DEFAULT 3.00,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `price_config`
--

INSERT INTO `price_config` (`id`, `jenis_layanan`, `nama_layanan`, `multiplier`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'repair', 'Mechanical Repair', 3.00, 'Perbaikan mekanik standard', '2026-05-23 15:39:20', '2026-05-23 15:39:20'),
(2, 'modif', 'Custom Modification', 3.00, 'Modifikasi custom', '2026-05-23 15:39:20', '2026-05-23 15:39:20'),
(3, 'brother', 'Brotherhood Disc', 2.30, 'Layanan brotherhood disc', '2026-05-23 15:39:20', '2026-05-23 15:39:20'),
(4, 'ws_stored', 'WS Stored', 2.00, 'WS Stored service', '2026-05-23 15:39:20', '2026-05-23 15:39:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sparepart_config`
--

CREATE TABLE `sparepart_config` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL DEFAULT 'Sparepart Standard',
  `harga_per_unit` decimal(10,2) NOT NULL DEFAULT 2.00,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `sparepart_config`
--

INSERT INTO `sparepart_config` (`id`, `nama`, `harga_per_unit`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Sparepart Standard', 2.00, NULL, '2026-05-23 15:39:20', '2026-05-23 15:39:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `jenis_layanan` varchar(50) NOT NULL,
  `compo_count` int(11) NOT NULL DEFAULT 0,
  `multiplier_used` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_harga` decimal(15,2) NOT NULL DEFAULT 0.00,
  `harga_sparepart` decimal(15,2) NOT NULL DEFAULT 0.00,
  `harga_jasa` decimal(15,2) NOT NULL DEFAULT 0.00,
  `compliment` varchar(100) DEFAULT NULL,
  `tanggal` datetime DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tugas_harian`
--

CREATE TABLE `tugas_harian` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `tugas` varchar(255) NOT NULL,
  `status` enum('pending','proses','selesai') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tugas_harian`
--

INSERT INTO `tugas_harian` (`id`, `tanggal`, `jam_mulai`, `jam_selesai`, `tugas`, `status`, `created_at`, `updated_at`) VALUES
(1, '2026-05-23', '08:00:00', '09:00:00', 'Menyiram tanaman Greenhouse A', 'selesai', '2026-05-23 15:39:20', '2026-05-23 15:39:20'),
(2, '2026-05-23', '09:00:00', '10:30:00', 'Pupuk organik cabai', 'selesai', '2026-05-23 15:39:20', '2026-05-23 15:39:24'),
(3, '2026-05-23', '10:00:00', '11:00:00', 'Cek pertumbuhan tomat', 'selesai', '2026-05-23 15:39:20', '2026-05-23 15:39:24'),
(4, '2026-05-23', '11:00:00', '12:30:00', 'Panen sayur mayur', 'selesai', '2026-05-23 15:39:20', '2026-05-23 15:39:24'),
(5, '2026-05-23', '14:00:00', '15:30:00', 'Semprot pestisida Greenhouse B', 'selesai', '2026-05-23 15:39:20', '2026-05-23 15:39:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('user','admin','employee') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `role`, `created_at`) VALUES
(1, 'admin', 'admin@brothers.com', '$2y$12$ip49/Ol5rl69zDl.wlQF3u9qM5vQ5zFj8rs2FgPrt8V1HFvFRDRfK', NULL, NULL, 'admin', '2026-05-10 03:27:51'),
(7, 'Andy Murphy', 'Andy@gmail.com', '$2y$12$TVBNXa/gHp5vcFi8ThGshOMiScob3QHTNSCCWuhppokTGCOhBs61K', NULL, NULL, 'user', '2026-05-10 04:32:20'),
(8, 'Mike Aschroft', 'gguntur688@gmail.com', '$2y$12$zybIV9bY2GMXwJrxXr7Kme8FPzjepNeeKY4ul2rpDhAcU6aiGDKnC', NULL, NULL, 'user', '2026-05-10 15:56:35'),
(9, 'ahahaha', 'ahahaha@gmail.com', '$2y$12$w/Nue3RjGW9STDNNXTTHwu4Je/aUyytpAqalREBvFt44EfxUR.CoG', NULL, NULL, 'user', '2026-05-10 18:04:17'),
(10, 'Fei', 'meifei@gmail.com', '$2y$12$0/LwugfjIrI13CZVeIiLyOWUBZRacaVivQ1HxM4cHP7utg7MbwO2C', NULL, NULL, 'user', '2026-05-10 18:47:51'),
(11, 'ucupjengat', 'tititmeledak@gmail.com', '$2y$12$0ORmD.pCg0QMOcwgjxVyjObOfZKp3FURKxNwYa0S3ajsQOJKzmEuW', NULL, NULL, 'user', '2026-05-10 18:55:51'),
(12, 'tititbernanah', 'atepsamsat@gmail.com', '$2y$12$Fni4Vm7how6nBiqR.pmEGevIdqMeFhFdx2xod2Yq32GI9Hz7jjwoa', NULL, NULL, 'user', '2026-05-10 18:57:00'),
(13, 'Casimiro Stone', 'evanputra689@yahoo.co.id', '$2y$12$3109/WVprTsrIGAp7dbEteT0rfVvS00UbDaqa2BWgwvPfUDA3/Z0W', NULL, NULL, 'admin', '2026-05-11 16:40:40');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `accepted_laporan`
--
ALTER TABLE `accepted_laporan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `source_type` (`source_type`),
  ADD KEY `nama_karyawan` (`nama_karyawan`),
  ADD KEY `divisi` (`divisi`),
  ADD KEY `tanggal_laporan` (`tanggal_laporan`),
  ADD KEY `tanggal_accept` (`tanggal_accept`);

--
-- Indeks untuk tabel `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indeks untuk tabel `compliment_rules`
--
ALTER TABLE `compliment_rules`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `delivery_order`
--
ALTER TABLE `delivery_order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `jenis_delivery` (`jenis_delivery`),
  ADD KEY `status` (`status`);

--
-- Indeks untuk tabel `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `farm_price_config`
--
ALTER TABLE `farm_price_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `config_key` (`config_key`);

--
-- Indeks untuk tabel `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nama_karyawan` (`nama_karyawan`),
  ADD KEY `tanggal` (`tanggal`);

--
-- Indeks untuk tabel `laporan_farmer`
--
ALTER TABLE `laporan_farmer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nama_karyawan` (`nama_karyawan`),
  ADD KEY `tanggal` (`tanggal`);

--
-- Indeks untuk tabel `marketplace_posts`
--
ALTER TABLE `marketplace_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_featured` (`is_featured`,`featured_expiry`),
  ADD KEY `idx_user_featured` (`user_id`,`is_featured`);

--
-- Indeks untuk tabel `mechanic_component_price`
--
ALTER TABLE `mechanic_component_price`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `telepon` (`telepon`);

--
-- Indeks untuk tabel `price_config`
--
ALTER TABLE `price_config`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sparepart_config`
--
ALTER TABLE `sparepart_config`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indeks untuk tabel `tugas_harian`
--
ALTER TABLE `tugas_harian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tanggal` (`tanggal`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `accepted_laporan`
--
ALTER TABLE `accepted_laporan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `compliment_rules`
--
ALTER TABLE `compliment_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `delivery_order`
--
ALTER TABLE `delivery_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `farm_price_config`
--
ALTER TABLE `farm_price_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `laporan_farmer`
--
ALTER TABLE `laporan_farmer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `marketplace_posts`
--
ALTER TABLE `marketplace_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `mechanic_component_price`
--
ALTER TABLE `mechanic_component_price`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `price_config`
--
ALTER TABLE `price_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `sparepart_config`
--
ALTER TABLE `sparepart_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tugas_harian`
--
ALTER TABLE `tugas_harian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `marketplace_posts`
--
ALTER TABLE `marketplace_posts`
  ADD CONSTRAINT `marketplace_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

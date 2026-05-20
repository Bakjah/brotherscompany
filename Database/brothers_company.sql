-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 19 Bulan Mei 2026 pada 13.03
-- Versi server: 10.6.23-MariaDB-cll-lve
-- Versi PHP: 8.3.26

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
-- Struktur dari tabel `members` (MemberApp)
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `telepon` varchar(20) NOT NULL UNIQUE,
  `compo_used` int(11) DEFAULT 0,
  `last_compo_update` datetime DEFAULT NULL,
  `tanggal_daftar` date DEFAULT (CURRENT_DATE),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `members`
--

INSERT INTO `members` (`nama`, `telepon`, `compo_used`) VALUES
('Budi Santoso', '081234567890', 0),
('Andi Wijaya', '085678901234', 0),
('Citra Dewi', '087812345678', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `price_config` (Calculator Config)
--

CREATE TABLE `price_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jenis_layanan` varchar(50) NOT NULL,
  `nama_layanan` varchar(100) NOT NULL,
  `multiplier` decimal(5,2) NOT NULL DEFAULT 3.0,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `price_config`
--

INSERT INTO `price_config` (`jenis_layanan`, `nama_layanan`, `multiplier`, `deskripsi`) VALUES
('repair', 'Mechanical Repair', 3.0, 'Perbaikan mekanik standard'),
('modif', 'Custom Modification', 3.0, 'Modifikasi custom'),
('brother', 'Brotherhood Disc', 2.3, 'Layanan brotherhood disc'),
('ws_stored', 'WS Stored', 2.0, 'WS Stored service');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sparepart_config` (Sparepart Price)
--

CREATE TABLE `sparepart_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL DEFAULT 'Sparepart Standard',
  `harga_per_unit` decimal(10,2) NOT NULL DEFAULT 2.0,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `sparepart_config`
--

INSERT INTO `sparepart_config` (`nama`, `harga_per_unit`) VALUES
('Sparepart Standard', 2.0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi` (Calculator - Transaksi)
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `jenis_layanan` varchar(50) NOT NULL,
  `compo_count` int(11) NOT NULL DEFAULT 0,
  `multiplier_used` decimal(5,2) NOT NULL DEFAULT 0,
  `total_harga` decimal(15,2) NOT NULL DEFAULT 0,
  `harga_sparepart` decimal(15,2) NOT NULL DEFAULT 0,
  `harga_jasa` decimal(15,2) NOT NULL DEFAULT 0,
  `compliment` varchar(100) DEFAULT NULL,
  `tanggal` datetime DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan_mechanic` (LaporanApp)
--

CREATE TABLE `laporan_mechanic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_mechanic` varchar(100) NOT NULL,
  `compo_used` int(11) NOT NULL DEFAULT 0,
  `money_stored` decimal(15,2) NOT NULL DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `tanggal` date DEFAULT (CURRENT_DATE),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `mecharing` (Mecharing - Record Mechanic)
--

CREATE TABLE `mecharing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_mechanic` varchar(100) NOT NULL,
  `tanggal` date DEFAULT (CURRENT_DATE),
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `total_compo` int(11) DEFAULT 0,
  `total_transaksi` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mecharing`
--

INSERT INTO `mecharing` (`nama_mechanic`, `jam_masuk`, `jam_keluar`, `total_compo`, `total_transaksi`, `status`) VALUES
('Joko Susanto', '08:00:00', '17:00:00', 15, 8, 'active'),
('Ahmad Rizki', '09:00:00', '18:00:00', 12, 6, 'active'),
('Dewi Lestari', '08:30:00', '16:30:00', 20, 10, 'active');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mecharing_detail` (Detail Recording Mechanic)
--

CREATE TABLE `mecharing_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mecharing_id` int(11) NOT NULL,
  `jenis_layanan` varchar(50) NOT NULL,
  `compo_count` int(11) NOT NULL DEFAULT 0,
  `harga` decimal(15,2) NOT NULL DEFAULT 0,
  `waktu` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `mecharing_id` (`mecharing_id`),
  CONSTRAINT `mecharing_detail_ibfk_1` FOREIGN KEY (`mecharing_id`) REFERENCES `mecharing` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `compliment_rules` (Calculator Config)
--

CREATE TABLE `compliment_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `threshold` int(11) NOT NULL,
  `gift` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `compliment_rules`
--

INSERT INTO `compliment_rules` (`threshold`, `gift`, `is_active`) VALUES
(5000, 'Makanan dari Restoran', 1),
(1000, 'Snack + Air Minum', 1),
(150, 'Snack', 1),
(0, 'Air Minum', 1);

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
-- Indeks untuk tabel `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indeks untuk tabel `marketplace_posts`
--
ALTER TABLE `marketplace_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_featured` (`is_featured`,`featured_expiry`),
  ADD KEY `idx_user_featured` (`user_id`,`is_featured`);

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
-- AUTO_INCREMENT untuk tabel `laporan_mechanic`
--
ALTER TABLE `laporan_mechanic`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `mecharing`
--
ALTER TABLE `mecharing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `mecharing_detail`
--
ALTER TABLE `mecharing_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `compliment_rules`
--
ALTER TABLE `compliment_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `marketplace_posts`
--
ALTER TABLE `marketplace_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

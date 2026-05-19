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
  `role` enum('user','admin') DEFAULT 'user',
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

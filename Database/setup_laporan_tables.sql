-- =====================================================
-- Brothers Company Database - Laporan Tables Setup
-- =====================================================
-- Run this SQL to create/update the laporan tables
-- =====================================================

-- Create table `laporan` for Mechanic reports
CREATE TABLE IF NOT EXISTS `laporan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_karyawan` varchar(100) NOT NULL,
  `compo_used` int(11) NOT NULL DEFAULT 0,
  `money_stored` decimal(15,2) NOT NULL DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `tanggal` date DEFAULT (CURRENT_DATE),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `nama_karyawan` (`nama_karyawan`),
  KEY `tanggal` (`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create table `laporan_farmer` for Farmer reports
CREATE TABLE IF NOT EXISTS `laporan_farmer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_karyawan` varchar(100) NOT NULL,
  `bibit_used` int(11) NOT NULL DEFAULT 0,
  `panen_hasil` decimal(15,2) NOT NULL DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `tanggal` date DEFAULT (CURRENT_DATE),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `nama_karyawan` (`nama_karyawan`),
  KEY `tanggal` (`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample data for Mechanic
INSERT INTO `laporan` (`nama_karyawan`, `compo_used`, `money_stored`, `keterangan`, `tanggal`) VALUES
('Joko Susanto', 10, 500000, 'Service harian', '2026-05-20'),
('Ahmad Rizki', 8, 400000, 'Perbaikan mesin', '2026-05-19'),
('Joko Susanto', 6, 300000, 'Tune up', '2026-05-18');

-- Insert sample data for Farmer (panen_hasil = jumlah kg)
INSERT INTO `laporan_farmer` (`nama_karyawan`, `bibit_used`, `panen_hasil`, `keterangan`, `tanggal`) VALUES
('Budi Santoso', 15, 50, 'Routine check Greenhouse A - Panen cabai 50 kg', '2026-05-20'),
('Dewi Lestari', 12, 30, 'Panen cabai organik', '2026-05-19'),
('Budi Santoso', 8, 25, 'Pupuk organik cabai', '2026-05-18');
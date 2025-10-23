-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 23, 2025 at 01:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sarpras_ti`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `namaLengkap` varchar(100) NOT NULL,
  `nim` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('mahasiswa','dosen','admin','staff') NOT NULL DEFAULT 'mahasiswa',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`id`, `user_id`, `namaLengkap`, `nim`, `email`, `username`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 25, 'Thor Odinson', '2401301010', 'THOR@POLITALA.AC.ID', 'THOR', '$2y$12$enR3rDM.0iGgQ/5bv.lcyO4GmhpoM0VqvSm8yK.a5DXhmqoaX6rTW', 'mahasiswa', '2025-10-01 17:53:03', '2025-10-17 05:20:50'),
(2, NULL, 'Ahmad Basyir', '2401301120', 'ahmad.basyir@mhs.politala.ac.id', 'Ahmad', '$2y$10$QxfeIRmqPKBgOgAdzGE/wO.QvKFCvjgeBbtjmmvHpsF5B9ZZNYRwy', 'mahasiswa', NULL, NULL),
(3, NULL, 'Basir', '2401301234', 'basyir@mhs.politala.ac.id', 'basir123', '$2y$12$kx3kOxEJYAPwHjl5eEC69.37SvN8l3SHYTLSOK1IRKov3N3MSn/8S', 'mahasiswa', '2025-10-01 21:45:51', '2025-10-01 21:45:51'),
(4, NULL, 'Muhammad Raihan', '2401301021', 'muhammad.raihan1@mhs.politala.ac.id', '2401301021_muhammad_raihan', '$2y$12$7pe.KijK.FouXatjgRSscu8cRI3i5yO7J5F5KQYQ9wCz7VeD9gGIO', 'mahasiswa', '2025-10-06 18:07:14', '2025-10-13 05:01:59');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_10_01_151248_create_mahasiswa_table', 1),
(5, '2025_10_01_151248_create_ruangan_table', 1),
(6, '2025_10_01_151249_create_unit_table', 1),
(7, '2025_10_01_151250_create_peminjaman_table', 1),
(8, '2025_10_01_151300_create_pengembalian_table', 1),
(9, '2025_10_07_095151_add_role_to_users_table', 2),
(10, '2025_10_08_020111_change_status_to_varchar_in_peminjaman_table', 3),
(11, '2025_10_08_023207_add_google_id_to_users_table', 4),
(14, '2025_10_13_134142_add_nim_nidn_to_users_table', 5),
(15, '2025_10_13_171444_update_role_nullable_in_users_table', 6),
(16, '2025_10_14_000406_add_default_role_to_users_table', 6),
(17, '2025_10_14_001818_update_username_nullable_in_users_table', 7),
(18, '2025_10_14_003509_add_id_dosen_to_peminjaman_table', 8),
(19, '2025_10_14_005526_add_avatar_and_is_completed_to_users_table', 9),
(20, '2025_10_14_150359_make_nim_nullable_in_mahasiswa_table', 10),
(21, '2025_10_14_160637_add_nama_lengkap_to_users_table', 11),
(22, '2025_10_14_160858_add_nama_lengkap_to_users_table', 12),
(23, '2025_10_14_161225_add_username_to_users_table', 13),
(24, '2025_10_14_161422_make_name_nullable_in_users_table', 13),
(25, '2025_10_14_163605_add_nim_to_users_table', 14),
(26, '2025_10_17_123931_add_user_id_to_mahasiswa_table', 14),
(27, '2025_10_17_130914_add_user_id_to_mahasiswa_table', 15),
(28, '2025_10_17_131349_add_user_id_to_mahasiswa_table', 16),
(30, '2025_10_18_140008_add_kondisi_catatan_to_peminjaman_table', 17),
(31, '2025_10_18_170708_update_peminjaman_foreign_to_users_table', 18);

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` int(11) UNSIGNED NOT NULL,
  `idPeminjaman` int(11) UNSIGNED NOT NULL,
  `pesan` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `idMahasiswa` bigint(20) UNSIGNED NOT NULL,
  `id_dosen` bigint(20) UNSIGNED DEFAULT NULL,
  `idRuangan` bigint(20) UNSIGNED DEFAULT NULL,
  `idUnit` bigint(20) UNSIGNED DEFAULT NULL,
  `tanggalPinjam` date NOT NULL,
  `jamMulai` time NOT NULL,
  `jamSelesai` time NOT NULL,
  `keperluan` text NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `kondisi_pengembalian` varchar(255) DEFAULT NULL,
  `catatan_pengembalian` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id`, `idMahasiswa`, `id_dosen`, `idRuangan`, `idUnit`, `tanggalPinjam`, `jamMulai`, `jamSelesai`, `keperluan`, `status`, `kondisi_pengembalian`, `catatan_pengembalian`, `created_at`, `updated_at`) VALUES
(18, 1, NULL, 3, NULL, '2025-10-18', '09:00:00', '12:00:00', 'Perkuliahan', 'selesai', NULL, NULL, '2025-10-18 04:46:41', '2025-10-18 05:23:12'),
(19, 1, NULL, NULL, 6, '2025-10-18', '09:00:00', '11:00:00', 'TESSSS', 'menunggu_validasi', NULL, NULL, '2025-10-18 06:23:51', '2025-10-18 06:24:32'),
(21, 1, NULL, 9, NULL, '2025-10-19', '09:00:00', '12:00:00', 'qwerty', 'digunakan', NULL, NULL, '2025-10-18 08:09:30', '2025-10-18 08:27:48'),
(22, 1, NULL, NULL, 7, '2025-10-19', '09:10:00', '10:00:00', 'kjsbuiwebfiuwe', 'ditolak', NULL, NULL, '2025-10-18 08:36:52', '2025-10-18 08:37:17'),
(24, 25, NULL, 2, NULL, '2025-10-19', '07:00:00', '12:00:00', 'poiuyt', 'ditolak', NULL, NULL, '2025-10-18 09:07:49', '2025-10-18 09:16:19'),
(25, 25, NULL, NULL, 7, '2025-10-19', '13:00:00', '16:00:00', 'hghghghghghg', 'digunakan', NULL, NULL, '2025-10-18 09:16:59', '2025-10-18 10:16:56'),
(26, 25, NULL, 10, NULL, '2025-10-19', '10:00:00', '13:00:00', 'klklkl', 'ditolak', NULL, NULL, '2025-10-18 09:21:48', '2025-10-22 00:37:46'),
(27, 25, NULL, 12, NULL, '2025-10-19', '13:00:00', '17:00:00', 'yuyuyuyu', 'ditolak', NULL, NULL, '2025-10-18 09:23:49', '2025-10-22 00:37:43'),
(28, 25, NULL, NULL, 8, '2025-10-19', '14:00:00', '16:30:00', 'popop', 'ditolak', NULL, NULL, '2025-10-18 09:27:25', '2025-10-22 00:37:41'),
(29, 28, NULL, 9, NULL, '2025-10-22', '09:00:00', '12:00:00', '123', 'ditolak', NULL, NULL, '2025-10-22 00:18:37', '2025-10-22 00:37:39');

-- --------------------------------------------------------

--
-- Table structure for table `pengembalian`
--

CREATE TABLE `pengembalian` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `idPeminjaman` bigint(20) UNSIGNED NOT NULL,
  `tanggalKembali` timestamp NOT NULL DEFAULT current_timestamp(),
  `kondisi` varchar(50) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ruangan`
--

CREATE TABLE `ruangan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `namaRuangan` varchar(100) NOT NULL,
  `lokasi` varchar(100) NOT NULL,
  `kapasitas` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ruangan`
--

INSERT INTO `ruangan` (`id`, `namaRuangan`, `lokasi`, `kapasitas`, `created_at`, `updated_at`) VALUES
(1, 'Aula GTI', '', 100, NULL, NULL),
(2, 'Lab Bill Gates', '', 30, NULL, NULL),
(3, 'Lab Kenneth Thompson', '', 30, NULL, NULL),
(4, 'Lab Steve Jobs', '', 30, NULL, NULL),
(5, 'Lab Linus Torvalds', '', 30, NULL, NULL),
(6, 'Lab Guido Van Rossum (Python)', '', 30, NULL, NULL),
(7, 'Lab Rasmus Lerdorf (PHP)', '', 30, NULL, NULL),
(8, 'Lab Dennis Ritchie (C++)', '', 30, NULL, NULL),
(9, 'Lab A', '', 30, NULL, NULL),
(10, 'Lab B', '', 30, NULL, NULL),
(11, 'Lab C', '', 30, NULL, NULL),
(12, 'Lab Komputer Jaringan', '', 30, NULL, NULL),
(13, 'Ruang Kelas 3 - EX Hima', '', 30, NULL, NULL),
(14, 'Lab HTML', '', 30, NULL, NULL),
(15, 'Lab C++', '', 30, NULL, NULL),
(16, 'Lab PHP', '', 30, NULL, NULL),
(17, 'Lab Python', '', 30, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('Whend2E5iL04yjgRtfb8qudq3PaYC5Kjqice7y8K', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUjdScnlaMXBRanZuQk55MTg4V21nMmNDc2V4TW95d0xjYUpyS0pBayI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fX0=', 1761122831);

-- --------------------------------------------------------

--
-- Table structure for table `unit`
--

CREATE TABLE `unit` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kodeUnit` varchar(50) NOT NULL,
  `namaUnit` varchar(100) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `unit`
--

INSERT INTO `unit` (`id`, `kodeUnit`, `namaUnit`, `kategori`, `created_at`, `updated_at`) VALUES
(6, 'PRJ001', 'Proyektor EPSON EB-X41', '', NULL, NULL),
(7, 'PRJ002', 'Proyektor EPSON EB-U05', '', NULL, NULL),
(8, 'PRJ003', 'Proyektor EPSON EB-X06', '', NULL, NULL),
(9, 'PRJ004', 'Proyektor BenQ MX525', '', NULL, NULL),
(10, 'PRJ005', 'Proyektor BenQ MS524', '', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `namaLengkap` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `nim` varchar(255) DEFAULT NULL,
  `nidn` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'mahasiswa',
  `is_completed` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `namaLengkap`, `google_id`, `avatar`, `name`, `nim`, `nidn`, `email`, `username`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`, `is_completed`) VALUES
(1, NULL, NULL, NULL, 'Admin', NULL, NULL, 'admin@politala.ac.id', 'admin', NULL, '$2y$12$Pl4wpk0YN2ZqXruFduuQ7.bwX.FDHAIXW5Ojit3vkDpUJTqDXExwC', NULL, '2025-10-13 06:22:22', '2025-10-13 16:22:22', 'admin', 0),
(2, NULL, NULL, NULL, 'Aditya', NULL, NULL, 'Aditya@gmail.com', '', NULL, '$2y$12$phWp5mdVghYy6TyckdrPLuZCDA3.2Ea6gKqcYBLQhv8bw9A2PX5Ea', NULL, '2025-10-13 07:23:37', '2025-10-13 07:23:37', 'mahasiswa', 0),
(25, NULL, '116173328898848170622', NULL, 'Muhammad Ramadan', '2201301020', NULL, 'mr4554442@gmail.com', 'muhammad_raihan', NULL, '$2y$12$pjtiqi7iqrJTZnW9bmX.3OJGR6JfDoUP1heFv2UqUJqZwVL.uX97G', 'JNn4xiUKvqSSk5aGu0BlHsybtQ8iEefNZCyPMAxFPIxBJUyAXZWZfqOk5vUS', '2025-10-15 20:31:35', '2025-10-15 20:31:53', 'mahasiswa', 1),
(28, NULL, '106719370098441908992', NULL, 'Muhammad Raihan', '10119005', NULL, 'muhammadrraihann@gmail.com', 'muhammad_raihan_1', NULL, '$2y$12$IEiJIs6zhYjLfuhMt7HRiuNXYTwOD4K17gZcDZb1Su/G6BkjJIZ1e', NULL, '2025-10-21 22:08:46', '2025-10-21 22:10:28', 'dosen', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mahasiswa_email_unique` (`email`),
  ADD UNIQUE KEY `mahasiswa_username_unique` (`username`),
  ADD UNIQUE KEY `mahasiswa_nim_unique` (`nim`),
  ADD KEY `mahasiswa_user_id_foreign` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `peminjaman_idmahasiswa_foreign` (`idMahasiswa`),
  ADD KEY `peminjaman_idruangan_foreign` (`idRuangan`),
  ADD KEY `peminjaman_idunit_foreign` (`idUnit`);

--
-- Indexes for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengembalian_idpeminjaman_foreign` (`idPeminjaman`);

--
-- Indexes for table `ruangan`
--
ALTER TABLE `ruangan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `unit`
--
ALTER TABLE `unit`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unit_kodeunit_unique` (`kodeUnit`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `pengembalian`
--
ALTER TABLE `pengembalian`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ruangan`
--
ALTER TABLE `ruangan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `unit`
--
ALTER TABLE `unit`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD CONSTRAINT `mahasiswa_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_idmahasiswa_foreign` FOREIGN KEY (`idMahasiswa`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `peminjaman_idruangan_foreign` FOREIGN KEY (`idRuangan`) REFERENCES `ruangan` (`id`),
  ADD CONSTRAINT `peminjaman_idunit_foreign` FOREIGN KEY (`idUnit`) REFERENCES `unit` (`id`);

--
-- Constraints for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD CONSTRAINT `pengembalian_idpeminjaman_foreign` FOREIGN KEY (`idPeminjaman`) REFERENCES `peminjaman` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

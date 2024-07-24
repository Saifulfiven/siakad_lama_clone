-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 21, 2018 at 04:56 AM
-- Server version: 5.6.24
-- PHP Version: 5.6.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `siakad`
--

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE IF NOT EXISTS `options` (
  `id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`id`, `value`) VALUES
('alamat_kampus', 'Graha STIE Nobel Indonesia, Jl. Sultan Alauddin No. 212 Makassar'),
('kabag_akademik', 'Anugrah, SS'),
('kabag_keuangan', 'Nur Rachma, SE., MM'),
('ketua', 'Dr. Mashur Razak, SE.,MM'),
('ketua_1', 'Dr. Ahmad Firman, SE., M.Si'),
('ketua_nip', '196208101991031002'),
('kunci_edit', '0'),
('kunci_mhs_ekonomi', '0'),
('kunci_mhs_pasca', '0'),
('margin_kertas_kop', '40'),
('nip_kabag_akademik', '0911001110000'),
('nomor', 'Telp: 0411 887978, Email:  nobel@stienobel-indonesia.ac.id');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

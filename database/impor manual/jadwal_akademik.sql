-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 21, 2018 at 05:42 AM
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
-- Table structure for table `jadwal_akademik`
--

CREATE TABLE IF NOT EXISTS `jadwal_akademik` (
  `id` int(10) unsigned NOT NULL,
  `id_fakultas` int(11) NOT NULL,
  `awal_pembayaran` date NOT NULL,
  `akhir_pembayaran` date NOT NULL,
  `awal_krs` date NOT NULL,
  `akhir_krs` date NOT NULL,
  `awal_kuliah` date NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `jadwal_akademik`
--

INSERT INTO `jadwal_akademik` (`id`, `id_fakultas`, `awal_pembayaran`, `akhir_pembayaran`, `awal_krs`, `akhir_krs`, `awal_kuliah`) VALUES
(1, 1, '2018-09-01', '2018-09-05', '2018-09-05', '2018-09-05', '2018-09-06'),
(2, 2, '2018-09-05', '2018-09-05', '2018-09-05', '2018-09-05', '2018-09-05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jadwal_akademik`
--
ALTER TABLE `jadwal_akademik`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jadwal_akademik`
--
ALTER TABLE `jadwal_akademik`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 13, 2018 at 03:01 AM
-- Server version: 5.6.24
-- PHP Version: 5.6.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `siakad_3`
--

-- --------------------------------------------------------

--
-- Table structure for table `sia_jenis_pembayaran`
--

CREATE TABLE IF NOT EXISTS `sia_jenis_pembayaran` (
  `id_jns_pembayaran` int(10) unsigned NOT NULL,
  `id_fakultas` tinyint(4) NOT NULL,
  `ket` varchar(20) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sia_jenis_pembayaran`
--

INSERT INTO `sia_jenis_pembayaran` (`id_jns_pembayaran`, `id_fakultas`, `ket`) VALUES
(1, 1, 'Magang I'),
(2, 1, 'Magang II'),
(3, 1, 'Magang III'),
(4, 1, 'Seminar Proposal'),
(5, 1, 'Seminar Hasil'),
(6, 1, 'Ujian Skripsi'),
(7, 1, 'Wisuda'),
(8, 2, 'Seminar Proposal'),
(9, 2, 'Seminar Hasil'),
(10, 2, 'Ujian Skripsi'),
(11, 2, 'Wisuda');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sia_jenis_pembayaran`
--
ALTER TABLE `sia_jenis_pembayaran`
  ADD PRIMARY KEY (`id_jns_pembayaran`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sia_jenis_pembayaran`
--
ALTER TABLE `sia_jenis_pembayaran`
  MODIFY `id_jns_pembayaran` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

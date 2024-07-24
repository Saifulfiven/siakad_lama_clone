-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 06, 2018 at 01:24 AM
-- Server version: 5.6.24
-- PHP Version: 5.6.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `siakad_4`
--

-- --------------------------------------------------------

--
-- Table structure for table `skala_nilai`
--

CREATE TABLE IF NOT EXISTS `skala_nilai` (
  `id` int(10) unsigned NOT NULL,
  `id_prodi` char(5) COLLATE utf8_unicode_ci NOT NULL,
  `nilai_huruf` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `nilai_indeks` double(3,2) NOT NULL,
  `range_nilai` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `range_atas` double(4,1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `skala_nilai`
--

INSERT INTO `skala_nilai` (`id`, `id_prodi`, `nilai_huruf`, `nilai_indeks`, `range_nilai`, `range_atas`) VALUES
(1, '61201', 'A', 4.00, '85 - 100', 100.0),
(2, '61201', 'B', 3.00, '75 - 84', 84.0),
(3, '61201', 'C', 2.00, '65 - 74', 74.0),
(4, '61201', 'D', 1.00, '55 - 64', 64.0),
(5, '61201', 'E', 0.00, '0 - 54', 54.0),
(6, '62201', 'A', 4.00, '85 - 100', 100.0),
(7, '62201', 'B', 3.00, '75 - 84', 84.0),
(8, '62201', 'C', 2.00, '65 - 74', 74.0),
(9, '62201', 'D', 1.00, '55 - 64', 64.0),
(10, '62201', 'E', 0.00, '0 - 54', 54.0),
(11, '61101', 'A', 4.00, '85 - 100', 100.0),
(12, '61101', 'A-', 3.75, '', 0.0),
(13, '61101', 'B+', 3.50, '', 0.0),
(14, '61101', 'B', 3.00, '75 - 84', 84.0),
(15, '61101', 'B-', 2.75, '', 0.0),
(16, '61101', 'C+', 2.50, '', 0.0),
(17, '61101', 'C', 2.00, '65 - 74', 74.0),
(18, '61101', 'C-', 1.75, '', 0.0),
(19, '61101', 'D', 1.00, '55 - 64', 64.0),
(20, '61101', 'E', 0.00, '0 - 54', 54.0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `skala_nilai`
--
ALTER TABLE `skala_nilai`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `skala_nilai`
--
ALTER TABLE `skala_nilai`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=21;
-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 31, 2018 at 12:32 AM
-- Server version: 10.1.29-MariaDB
-- PHP Version: 7.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `iwm_collections`
--

-- --------------------------------------------------------

--
-- Table structure for table `storymaps`
--

CREATE TABLE `storymaps` (
  `map_name` varchar(100) DEFAULT NULL,
  `objectId` int(10) DEFAULT NULL,
  `title` varchar(500) NOT NULL,
  `description` text NOT NULL,
  `thumb_url` varchar(100) NOT NULL,
  `image_url` varchar(100) NOT NULL,
  `placename` varchar(200) NOT NULL,
  `lat` float(10,7) NOT NULL,
  `lon` float(10,7) NOT NULL,
  `dateText` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `date_granularity` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `storymaps`
--
ALTER TABLE `storymaps`
  ADD UNIQUE KEY `map_name` (`map_name`,`objectId`) USING BTREE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

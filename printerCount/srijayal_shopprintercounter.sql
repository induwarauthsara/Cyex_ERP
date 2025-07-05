-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 11, 2024 at 02:04 PM
-- Server version: 10.6.18-MariaDB-cll-lve
-- PHP Version: 8.1.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `srijayal_shopprintercounter`
--
CREATE DATABASE IF NOT EXISTS `srijayal_shopprintercounter` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `srijayal_shopprintercounter`;

-- --------------------------------------------------------

--
-- Table structure for table `count`
--

CREATE TABLE IF NOT EXISTS `count` (
  `countID` int(2) NOT NULL AUTO_INCREMENT,
  `typeID` int(2) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `time` time NOT NULL DEFAULT current_timestamp(),
  `count` int(10) NOT NULL,
  `cost` int(10) NOT NULL,
  PRIMARY KEY (`countID`),
  KEY `count_ibfk_1` (`typeID`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `count`
--

TRUNCATE TABLE `count`;
--
-- Dumping data for table `count`
--

INSERT INTO `count` (`countID`, `typeID`, `date`, `time`, `count`, `cost`) VALUES
(1, 1, '2024-06-09', '16:07:56', 1227433, 0),
(2, 2, '2024-06-09', '16:07:56', 606111, 0),
(3, 3, '2024-06-09', '16:07:56', 131065, 0),
(4, 4, '2024-06-09', '16:07:56', 127802, 100),
(5, 5, '2024-06-09', '16:07:56', 851, 200),
(6, 8, '2024-06-09', '16:07:56', 3756, 0),
(7, 9, '2024-06-09', '16:07:56', 214325, 0),
(8, 10, '2024-06-09', '16:07:56', 157331, 0),
(9, 1, '2024-06-10', '20:47:18', 1227436, 2454872),
(10, 2, '2024-06-10', '20:47:18', 60501, 121002),
(11, 3, '2024-06-10', '20:47:18', 131243, 656215),
(12, 4, '2024-06-10', '20:47:18', 127736, 3832080),
(13, 5, '2024-06-10', '20:47:18', 742, 3710),
(14, 8, '2024-06-10', '20:47:18', 3648, 109440),
(15, 9, '2024-06-10', '20:47:18', 214225, 6426750),
(16, 10, '2024-06-10', '20:47:18', 157336, 314672);

-- --------------------------------------------------------

--
-- Table structure for table `printers`
--

CREATE TABLE IF NOT EXISTS `printers` (
  `printerID` int(2) NOT NULL AUTO_INCREMENT,
  `printerName` varchar(30) NOT NULL,
  `typeCount` int(2) NOT NULL,
  `active` int(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`printerID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `printers`
--

TRUNCATE TABLE `printers`;
--
-- Dumping data for table `printers`
--

INSERT INTO `printers` (`printerID`, `printerName`, `typeCount`, `active`) VALUES
(1, 'XEROX 5845 (Black)', 2, 1),
(2, 'XEROX 7855 (Color)', 4, 1),
(3, 'Konica Minolta C258', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `types`
--

CREATE TABLE IF NOT EXISTS `types` (
  `typeID` int(2) NOT NULL AUTO_INCREMENT,
  `printerID` int(2) NOT NULL,
  `typeName` varchar(50) NOT NULL,
  `cost` int(2) NOT NULL,
  `totalCount` int(20) NOT NULL,
  UNIQUE KEY `typeID` (`typeID`),
  KEY `printerID` (`printerID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `types`
--

TRUNCATE TABLE `types`;
--
-- Dumping data for table `types`
--

INSERT INTO `types` (`typeID`, `printerID`, `typeName`, `cost`, `totalCount`) VALUES
(1, 1, '5845 Black', 2, 1227546),
(2, 1, '5845 Black Large', 2, 60611),
(3, 2, '7855 Black ', 5, 131353),
(4, 2, '7855 Color ', 30, 127846),
(5, 2, '7855 Black Large', 5, 852),
(8, 2, '7855 Color Large', 30, 3758),
(9, 3, 'Konika C258 Color', 30, 214335),
(10, 3, 'Konika C258 Black', 2, 157446);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `count`
--
ALTER TABLE `count`
  ADD CONSTRAINT `count_ibfk_1` FOREIGN KEY (`typeID`) REFERENCES `types` (`typeID`);

--
-- Constraints for table `types`
--
ALTER TABLE `types`
  ADD CONSTRAINT `types_ibfk_1` FOREIGN KEY (`printerID`) REFERENCES `printers` (`printerID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

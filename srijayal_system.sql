-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 08, 2023 at 07:06 PM
-- Server version: 10.5.19-MariaDB-cll-lve
-- PHP Version: 8.1.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `srijayal_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `account_name` varchar(30) NOT NULL,
  `amount` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `account_name`, `amount`) VALUES
(1, 'Stock Account', -936814.51),
(2, 'Company Profit', 34769.25),
(3, 'Machines Account', 46358.99),
(6, 'Utility Bills', 46358.99),
(7, 'cash_in_hand', 97064.00);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(3) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `customer_type` varchar(10) NOT NULL,
  `customer_mobile` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `customer_name`, `customer_type`, `customer_mobile`) VALUES
(1, 'Walking Customer ', '', 714730996),
(2, 'à¶…à¶­à·”à¶»à·”à¶œà·’à¶»à·’à¶º à¶‘à¶šà·Š', '', 0),
(3, 'Sipla', '', 0),
(4, 'Mayadunna ', '', 0),
(5, 'Cash ', '', 0),
(6, 'Tessting Bill', '', 0),
(7, 'Wedihiti Balamandalaya - Kaduwela', '', 714499882),
(8, 'Sithumina Book Shop ', '', 0),
(9, 'Diseli Products ', '', 766157527),
(10, 'Winsara Sir', '', 719922440),
(11, 'Collection ', '', 112156006),
(12, 'M.SHAHEER', '', 0),
(13, 'Chandupa Dissanayaka', '', 0),
(14, 'Mullegama Uthura Maranadara Samithiya', '', 718774861),
(15, 'Mrs. Hansika ', '', 778733314),
(16, 'D-A ENTERPRISES', '', 771618404),
(17, 'Ruwanthi', '', 777945968),
(18, 'SAVINU INTERNATIONAL (PVT) LTD', '', 767202119),
(19, 'Prototeq Solutions ', '', 768449939),
(20, 'Agrotac Industries (PVT) LTD', '', 112173049),
(21, 'Mrs. Waruni ', '', 716906644),
(22, 'Mrs. DRG Amarasinghe', '', 718392869),
(23, 'BOC - Athurugiriya', '', 0),
(24, 'Samurdhi Bank - Athurugiriya', '', 0),
(25, 'Mithrarathna', '', 0),
(26, 'Jayasinghe Grave Yard', '', 716433344),
(27, 'Orex Englineering Pvt Ltd ', '', 768708800),
(28, 'Califolink Logistics (Pvt) Ltd', '', 759320334),
(29, 'Ticket Book Print ', '', 726676344),
(30, 'Flower Petal', '', 726503465),
(31, 'Sanju', '', 775619549),
(32, 'Test', '', 54551),
(33, 'King Fish ', '', 753841551),
(34, 'Basnayaka', '', 0),
(35, 'Damro', '', 112076639),
(36, 'OMATA WATER MANAGEMENT (PVT) LTD', '', 114444200),
(37, 'Redlions Sports Club ', '', 771445721),
(38, 'maxim impressions lanka (pvt) ltd', '', 715330037),
(39, 'Millennium Super Center', '', 0),
(40, 'Mrs. Anushka', '', 775824411),
(41, 'priyantha wadu karmika', '', 781141603),
(42, 'T. Chandralatha', '', 718137304),
(43, 'Ganesha', '', 773936096),
(44, 'Bakmegahapara Eksat Subasadaka Samithiya', '', 716335527);

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employ_id` int(4) NOT NULL,
  `emp_name` varchar(30) NOT NULL,
  `mobile` int(10) NOT NULL,
  `address` varchar(80) NOT NULL,
  `bank_account` varchar(10) NOT NULL,
  `role` varchar(10) NOT NULL,
  `nic` varchar(15) NOT NULL,
  `salary` decimal(8,2) NOT NULL,
  `password` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employ_id`, `emp_name`, `mobile`, `address`, `bank_account`, `role`, `nic`, `salary`, `password`) VALUES
(1, 'Lochana', 714730996, '55/21, Galabadawatta, Korathota, Kaduwela.', '', 'Admin', '', 1154.03, '456'),
(2, 'Nalani', 763547175, '55/21, Galabadawatta, Korathota, Kaduwela.', '', 'Employee', '', 0.00, ''),
(3, 'Prabudda Lakshith', 0, '', '', 'Employee', '', 0.00, ''),
(4, 'Induwara Uthsara', 786607354, '55/21, Galabadawatta, Korathota, Kaduwela.', '', 'Employee', '200501903425', 4.51, '987'),
(5, 'srijaya', 0, '', '', 'Admin', '', 0.00, '444'),
(6, 'Sanju', 775619549, '', '', 'Employee', '', 0.60, '1129');

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(10) NOT NULL,
  `customer_id` int(3) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `invoice_date` date NOT NULL DEFAULT current_timestamp(),
  `time` time DEFAULT current_timestamp(),
  `customer_mobile` int(10) NOT NULL,
  `biller` varchar(20) NOT NULL,
  `primary_worker` varchar(20) NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) NOT NULL,
  `advance` decimal(15,2) NOT NULL,
  `balance` decimal(15,2) NOT NULL,
  `full_paid` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`id`, `invoice_number`, `customer_id`, `customer_name`, `invoice_date`, `time`, `customer_mobile`, `biller`, `primary_worker`, `total`, `discount`, `advance`, `balance`, `full_paid`) VALUES
(1, '00001', 2, 'à¶…à¶­à·”à¶»à·”à¶œà·', '2023-01-26', '00:00:00', 0, 'Lochana', '', 300.00, 0.00, 300.00, 0.00, 1),
(2, '00002', 3, 'Sipla', '2023-01-26', '00:00:00', 0, 'Lochana', '', 184.00, 0.00, 184.00, 0.00, 1),
(3, '00003', 4, 'Mayadunna ', '2023-01-26', '00:00:00', 0, 'Lochana', '', 800.00, 0.00, 0.00, 800.00, 0),
(4, '00003', 4, 'Mayadunna ', '2023-01-26', '00:00:00', 0, 'Lochana', '', 20.00, 0.00, 0.00, 20.00, 0),
(5, '00005', 5, 'Cash ', '2023-01-27', '00:00:00', 0, 'Lochana', '', 760.00, 0.00, 0.00, 760.00, 0),
(6, '00006', 5, 'Cash ', '2023-01-27', '00:00:00', 0, 'Lochana', '', 180.00, 0.00, 180.00, 0.00, 1),
(7, '00007', 5, 'Cash ', '2023-01-27', '00:00:00', 0, 'Lochana', '', 100.00, 0.00, 0.00, 100.00, 0),
(8, '00008', 5, 'Cash ', '2023-01-27', '00:00:00', 0, 'Lochana', '', 100.00, 0.00, 100.00, 0.00, 1),
(9, '00009', 6, 'Tessting Bill', '2023-01-27', '00:00:00', 0, 'Lochana', '', 180.00, 0.00, 180.00, 0.00, 1),
(10, '000010', 6, 'Tessting Bill', '2023-01-27', '00:00:00', 0, 'Lochana', '', 180.00, 0.00, 180.00, 0.00, 1),
(11, '000011', 7, 'Wedihiti Balamandalaya - Kaduwela', '2023-01-27', '00:00:00', 714499882, 'Lochana', '', 72.00, 0.00, 72.00, 0.00, 1),
(12, '000012', 7, 'Wedihiti Balamandalaya - Kaduwela', '2023-01-27', '00:00:00', 714499882, 'Lochana', '', 0.00, 0.00, 0.00, 0.00, 1),
(13, '000013', 5, 'Cash ', '2023-01-27', '00:00:00', 0, 'Lochana', '', 648.00, 0.00, 648.00, 0.00, 1),
(14, '000014', 8, 'Sithumina Book Shop ', '2023-01-27', '00:00:00', 0, 'Lochana', '', 330.00, 0.00, 330.00, 0.00, 1),
(15, '000015', 9, 'Diseli Products ', '2023-01-27', '00:00:00', 766157527, 'Lochana', '', 540.00, 0.00, 0.00, 540.00, 0),
(16, '000016', 10, 'Winsara Sir', '2023-01-27', '00:00:00', 719922440, 'Lochana', '', 320.00, 0.00, 320.00, 0.00, 1),
(17, '000017', 11, 'Collection ', '2023-01-27', '00:00:00', 112156006, 'Lochana', '', 27500.00, 0.00, 0.00, 27500.00, 0),
(18, '000018', 12, 'M.SHAHEER', '2023-01-28', '00:00:00', 0, 'Induwara Uthsara', '', 104.00, 0.00, 104.00, 0.00, 1),
(19, '000018', 12, 'M.SHAHEER', '2023-01-28', '00:00:00', 0, 'Induwara Uthsara', '', 840.00, 0.00, 840.00, 0.00, 1),
(20, '000020', 13, 'Chandupa Dissanayaka', '2023-01-28', '00:00:00', 0, 'Lochana', '', 1714.00, 0.00, 0.00, 1714.00, 0),
(21, '000021', 5, 'Cash ', '2023-01-28', '00:00:00', 0, 'Lochana', '', 100.00, 0.00, 100.00, 0.00, 1),
(22, '000022', 5, 'Cash ', '2023-01-28', '00:00:00', 0, 'Lochana', '', 24.00, 0.00, 25.00, -1.00, 0),
(23, '000023', 14, 'Mullegama Uthura Maranadara Samithiya', '2023-01-29', '00:00:00', 718774861, 'Lochana', '', 16800.00, 0.00, 16800.00, 0.00, 1),
(24, '000024', 5, 'Cash ', '2023-01-29', '00:00:00', 0, 'Lochana', '', 1000.00, 0.00, 1000.00, 0.00, 1),
(25, '000025', 5, 'Cash ', '2023-01-29', '00:00:00', 0, 'Lochana', '', 70.00, 0.00, 70.00, 0.00, 1),
(26, '000026', 15, 'Mrs. Hansika ', '2023-01-30', '00:00:00', 778733314, 'Lochana', '', 1800.00, 0.00, 1000.00, 800.00, 0),
(27, '000027', 5, 'Cash ', '2023-01-30', '00:00:00', 0, 'Lochana', '', 240.00, 0.00, 240.00, 0.00, 1),
(28, '000028', 5, 'Cash ', '2023-01-30', '00:00:00', 0, 'Lochana', '', 970.00, 0.00, 970.00, 0.00, 1),
(29, '000029', 16, 'D-A ENTERPRISES', '2023-01-30', '00:00:00', 771618404, 'Lochana', '', 1000.00, 0.00, 1000.00, 0.00, 1),
(30, '000030', 17, 'Ruwanthi', '2023-01-31', '00:00:00', 777945968, 'Lochana', '', 1600.00, 0.00, 1600.00, 1600.00, 0),
(31, '000031', 8, 'Sithumina Book Shop ', '2023-01-31', '00:00:00', 0, 'Lochana', '', 400.00, 0.00, 400.00, 400.00, 0),
(32, '000032', 18, 'SAVINU INTERNATIONAL (PVT) LTD', '2023-01-31', '00:00:00', 767202119, 'Lochana', '', 1000.00, 0.00, 1000.00, 1000.00, 0),
(33, '000033', 5, 'Cash ', '2023-01-31', '00:00:00', 0, 'Lochana', '', 500.00, 0.00, 500.00, 500.00, 0),
(34, '000034', 19, 'Prototeq Solutions ', '2023-01-31', '00:00:00', 768449939, 'Lochana', '', 1380.00, 0.00, 1380.00, 1380.00, 0),
(35, '000035', 20, 'Agrotac Industries (PVT) LTD', '2023-02-01', '00:00:00', 112173049, 'Lochana', '', 8500.00, 0.00, 0.00, 8500.00, 0),
(36, '000036', 21, 'Mrs. Waruni ', '2023-02-01', '00:00:00', 716906644, 'Lochana', '', 724.00, 0.00, 724.00, 724.00, 0),
(37, '000037', 8, 'Sithumina Book Shop ', '2023-02-01', '00:00:00', 0, 'Lochana', '', 160.00, 0.00, 0.00, 160.00, 0),
(38, '000038', 5, 'Cash ', '2023-02-02', '00:00:00', 0, 'Lochana', '', 150.00, 0.00, 150.00, 150.00, 0),
(39, '000039', 5, 'Cash ', '2023-02-02', '00:00:00', 0, 'Lochana', '', 30.00, 0.00, 30.00, 30.00, 0),
(40, '000040', 22, 'Mrs. DRG Amarasinghe', '2023-02-02', '00:00:00', 718392869, 'Lochana', '', 7040.00, 0.00, 0.00, 7040.00, 0),
(41, '000041', 0, 'Cash', '2023-02-02', '00:00:00', 0, 'Lochana', '', 490.00, 0.00, 490.00, 490.00, 0),
(42, '000042', 5, 'Cash ', '2023-02-02', '00:00:00', 0, 'Lochana', '', 30.00, 0.00, 30.00, 30.00, 0),
(43, '000043', 8, 'Sithumina Book Shop ', '2023-02-02', '00:00:00', 0, 'Lochana', '', 130.00, 0.00, 0.00, 130.00, 0),
(44, '000044', 5, 'Cash ', '2023-02-02', '00:00:00', 0, 'Lochana', '', 830.00, 0.00, 830.00, 830.00, 0),
(45, '000002', 0, 'Sipla', '2023-02-02', '00:00:00', 0, 'Lochana', '', 180.00, 0.00, 184.00, 0.00, 1),
(46, '000046', 23, 'BOC - Athurugiriya', '2023-02-02', '00:00:00', 0, 'Lochana', '', 360.00, 0.00, 720.00, -360.00, 1),
(47, '000047', 5, 'Cash ', '2023-02-02', '00:00:00', 0, 'Lochana', '', 80.00, 0.00, 160.00, -80.00, 1),
(48, '000048', 24, 'Samurdhi Bank - Athurugiriya', '2023-02-02', '00:00:00', 0, 'Lochana', '', 300.00, 0.00, 300.00, 0.00, 1),
(49, '000049', 5, 'Cash ', '2023-02-02', '00:00:00', 0, 'Lochana', '', 150.00, 0.00, 150.00, 150.00, 0),
(50, '000050', 5, 'Cash ', '2023-02-02', '00:00:00', 0, 'Lochana', '', 288.00, 0.00, 576.00, -288.00, 1),
(51, '000051', 25, 'Mithrarathna', '2023-02-02', '00:00:00', 0, 'Lochana', '', 480.00, 0.00, 480.00, 480.00, 0),
(52, '000052', 5, 'Cash ', '2023-02-03', '00:00:00', 0, 'Lochana', '', 84.00, 0.00, 84.00, 84.00, 0),
(53, '000053', 5, 'Cash ', '2023-02-03', '00:00:00', 0, 'Lochana', '', 40.00, 0.00, 40.00, 40.00, 0),
(54, '000054', 26, 'Jayasinghe Grave Yard', '2023-02-03', '00:00:00', 716433344, 'Lochana', '', 72.00, 0.00, 70.00, 2.00, 0),
(55, '000055', 27, 'Orex Englineering Pvt Ltd ', '2023-02-16', '00:00:00', 768708800, 'Lochana', '', 26860.00, 0.00, 26860.00, 0.00, 1),
(56, '000056', 28, 'Califolink Logistics (Pvt) Ltd', '2023-02-16', '00:00:00', 759320334, 'Lochana', '', 4750.00, 0.00, 4750.00, 0.00, 1),
(57, '000057', 20, 'Agrotac Industries (PVT) LTD', '2023-03-01', '00:00:00', 112173049, 'Lochana', '', 9350.00, 0.00, 9350.00, 0.00, 1),
(58, '000058', 4, 'Mayadunna ', '2023-03-03', '00:00:00', 0, 'Lochana', '', 40.00, 0.00, 150.00, -110.00, 1),
(59, '59', 5, 'Cash ', '2023-03-07', '00:00:00', 0, 'Lochana', '', 30.00, 0.00, 30.00, 30.00, 0),
(60, '60', 5, 'Cash ', '2023-03-07', '00:00:00', 0, 'Lochana', '', 175.00, 0.00, 350.00, -175.00, 1),
(61, '61', 5, 'Cash ', '2023-03-07', '00:00:00', 0, 'Lochana', '', 30.00, 0.00, 30.00, 30.00, 0),
(62, '61', 5, 'Cash ', '2023-03-07', '00:00:00', 0, 'Lochana', '', 560.00, 0.00, 560.00, 560.00, 0),
(63, '62', 5, 'Cash ', '2023-03-07', '00:00:00', 0, 'Lochana', '', 1012.50, 0.00, 1012.50, 1012.50, 0),
(64, '64', 0, 'Cash', '2023-03-07', '00:00:00', 0, 'Lochana', '', 1400.00, 0.00, 1400.00, 1400.00, 0),
(65, '65', 0, 'cash', '2023-03-07', '00:00:00', 0, 'Lochana', '', 30.00, 0.00, 0.00, 30.00, 0),
(66, '66', 5, 'Cash ', '2023-03-07', '00:00:00', 0, 'Lochana', '', 384.00, 0.00, 0.00, 384.00, 0),
(67, '67', 5, 'Cash ', '2023-03-07', '00:00:00', 0, 'Lochana', '', 140.00, 0.00, 140.00, 140.00, 0),
(68, '68', 5, 'Cash ', '2023-03-07', '00:00:00', 0, 'Lochana', '', 100.00, 0.00, 100.00, 100.00, 0),
(69, '69', 5, 'Cash ', '2023-03-07', '00:00:00', 0, 'Lochana', '', 242.00, 0.00, 242.00, 242.00, 0),
(70, '70', 29, 'Ticket Book Print ', '2023-03-07', '00:00:00', 726676344, 'Lochana', '', 2400.00, 0.00, 2400.00, 2400.00, 0),
(71, '71', 10, 'Winsara Sir', '2023-03-07', '00:00:00', 719922440, 'Lochana', '', 1072.00, 0.00, 1072.00, 0.00, 1),
(72, '72', 30, 'Flower Petal', '2023-03-08', '00:00:00', 726503465, 'Lochana', '', 3580.00, 0.00, 3000.00, 580.00, 0),
(73, '73', 5, 'Cash ', '2023-03-08', '00:00:00', 0, 'Lochana', '', 244.00, 0.00, 488.00, -244.00, 1),
(74, '74', 10, 'Winsara Sir', '2023-03-10', '00:00:00', 719922440, 'Lochana', '', 488.00, 0.00, 488.00, 0.00, 1),
(75, '75', 5, 'Cash ', '2023-03-11', '00:00:00', 0, 'Lochana', '', 840.00, 90.00, 1590.00, -750.00, 1),
(76, '76', 5, 'Cash ', '2023-03-11', '00:00:00', 0, 'Lochana', '', 840.00, 90.00, 1590.00, -750.00, 1),
(77, '77', 0, 'cash', '2023-03-11', '00:00:00', 0, 'Lochana', '', 840.00, 0.00, 1590.00, -750.00, 1),
(78, '78', 6, 'Tessting Bill', '2023-03-11', '00:00:00', 0, 'Lochana', '', 130.00, 30.00, 130.00, 0.00, 1),
(79, '79', 5, 'Cash ', '2023-03-14', '00:00:00', 0, 'Lochana', '', 168.00, 0.00, 168.00, 0.00, 1),
(80, '80', 31, 'Sanju', '2023-03-14', '00:00:00', 775619549, 'Lochana', '', 15.00, 0.00, 15.00, 0.00, 1),
(81, '81', 31, 'Sanju', '2023-03-14', '00:00:00', 775619549, 'Lochana', '', 25.00, 0.00, 25.00, 0.00, 1),
(82, '82', 5, 'Cash ', '2023-03-14', '00:00:00', 0, 'Lochana', '', 444.00, 0.00, 444.00, 0.00, 1),
(83, '83', 5, 'Cash ', '2023-03-14', '00:00:00', 0, 'Lochana', '', 336.00, 0.00, 336.00, 0.00, 1),
(84, '84', 5, 'Cash ', '2023-03-14', '00:00:00', 0, 'Lochana', '', 2400.00, 0.00, 2400.00, 0.00, 1),
(86, '86', 5, 'Cash ', '2023-03-14', '00:00:00', 0, 'Lochana', '', 492.00, 0.00, 492.00, 0.00, 1),
(89, '87', 5, 'Cash ', '2023-03-14', '18:49:16', 0, 'Lochana', '', 1035.00, 0.00, 1035.00, 0.00, 1),
(90, '90', 5, 'Cash ', '2023-03-15', '15:27:53', 0, 'Lochana', '', 516.00, 0.00, 516.00, 0.00, 1),
(91, '91', 5, 'Cash ', '2023-03-15', '19:47:15', 0, 'Lochana', '', 250.00, 0.00, 250.00, 0.00, 1),
(92, '92', 33, 'King Fish ', '2023-03-16', '10:12:48', 753841551, 'Lochana', '', 1220.00, 0.00, 1220.00, 0.00, 1),
(93, '93', 5, 'Cash ', '2023-03-22', '14:10:15', 0, 'Lochana', '', 300.00, 0.00, 300.00, 0.00, 1),
(94, '94', 5, 'Cash ', '2023-03-22', '14:10:45', 0, 'Lochana', '', 96.00, 0.00, 96.00, 0.00, 1),
(95, '95', 5, 'Cash ', '2023-03-22', '14:11:50', 0, 'Lochana', '', 150.00, 0.00, 150.00, 0.00, 1),
(96, '96', 5, 'Cash ', '2023-03-22', '14:13:50', 0, 'Lochana', '', 150.00, 0.00, 150.00, 0.00, 1),
(97, '97', 5, 'Cash ', '2023-03-22', '14:15:46', 0, 'Lochana', '', 1265.00, 0.00, 1265.00, 0.00, 1),
(98, '98', 5, 'Cash ', '2023-03-22', '14:37:20', 0, 'Lochana', '', 800.00, 0.00, 800.00, 0.00, 1),
(99, '99', 25, 'Mithrarathna', '2023-03-23', '11:24:14', 0, 'Lochana', '', 720.00, 0.00, 720.00, 0.00, 1),
(100, '100', 34, 'Basnayaka', '2023-03-23', '11:35:55', 0, 'Lochana', '', 500.00, 0.00, 500.00, 0.00, 1),
(101, '101', 5, 'Cash ', '2023-03-23', '11:41:18', 0, 'Lochana', '', 480.00, 0.00, 480.00, 0.00, 1),
(102, '102', 5, 'Cash ', '2023-03-23', '13:06:39', 0, 'Lochana', '', 920.00, 0.00, 920.00, 0.00, 1),
(103, '103', 11, 'Collection ', '2023-04-01', '20:32:22', 112156006, 'Lochana', '', 2300.00, 0.00, 2300.00, 0.00, 1),
(104, '104', 7, 'Wedihiti Balamandalaya - Kaduwela', '2023-04-02', '13:43:39', 714499882, 'Lochana', '', 1480.00, 0.00, 1480.00, 0.00, 1),
(105, '105', 35, 'Damro', '2023-04-02', '14:20:47', 112076639, 'Lochana', '', 132.00, 0.00, 132.00, 0.00, 1),
(106, '106', 11, 'Collection ', '2023-04-02', '15:20:13', 112156006, 'Lochana', '', 210.00, 10.00, 210.00, 0.00, 1),
(107, '107', 36, 'OMATA WATER MANAGEMENT (PVT) LTD', '2023-04-03', '09:40:29', 114444200, 'Lochana', '', 8500.00, 0.00, 8500.00, 0.00, 1),
(108, '108', 5, 'Cash ', '2023-04-03', '12:15:42', 0, 'Lochana', '', 4000.00, 0.00, 4000.00, 0.00, 1),
(109, '109', 37, 'Redlions Sports Club ', '2023-04-03', '12:59:55', 771445721, 'Lochana', '', 12700.00, 1700.00, 5000.00, 6000.00, 0),
(110, '110', 38, 'maxim impressions lanka (pvt) ltd', '2023-04-03', '13:30:28', 715330037, 'Lochana', '', 420.00, 0.00, 420.00, 0.00, 1),
(111, '110', 38, 'maxim impressions lanka (pvt) ltd', '2023-04-03', '13:30:28', 715330037, 'Lochana', '', 420.00, 0.00, 420.00, 0.00, 1),
(112, '112', 38, 'maxim impressions lanka (pvt) ltd', '2023-04-03', '13:32:09', 715330037, 'Lochana', '', 420.00, 0.00, 420.00, 0.00, 1),
(113, '113', 14, 'Mullegama Uthura Maranadara Samithiya', '2023-04-04', '09:55:30', 718774861, 'Lochana', '', 8800.00, 0.00, 8800.00, 0.00, 1),
(114, '114', 39, 'Millennium Super Center', '2023-04-06', '11:54:06', 0, 'Lochana', '', 63720.00, 0.00, 0.00, 63720.00, 0),
(115, '115', 20, 'Agrotac Industries (PVT) LTD', '2023-04-06', '13:17:16', 112173049, 'Lochana', '', 7650.00, 0.00, 7650.00, 0.00, 1),
(116, '116', 40, 'Mrs. Anushka', '2023-04-06', '16:15:16', 775824411, 'Lochana', '', 1600.00, 0.00, 1600.00, 0.00, 1),
(117, '117', 27, 'Orex Englineering Pvt Ltd ', '2023-04-06', '17:18:33', 768708800, 'Lochana', '', 1200.00, 0.00, 1200.00, 0.00, 1),
(118, '118', 5, 'Cash ', '2023-04-06', '18:39:05', 0, 'Lochana', '', 310.00, 0.00, 310.00, 0.00, 1),
(119, '119', 5, 'Cash ', '2023-04-07', '12:36:55', 0, 'Lochana', '', 1200.00, 0.00, 1200.00, 0.00, 1),
(120, '120', 5, 'Cash ', '2023-04-07', '14:30:37', 0, 'Lochana', '', 1850.00, 0.00, 1850.00, 0.00, 1),
(121, '121', 5, 'Cash ', '2023-04-07', '18:05:37', 0, 'Lochana', '', 200.00, 0.00, 200.00, 0.00, 1),
(122, '122', 5, 'Cash ', '2023-04-07', '19:23:10', 0, 'Lochana', '', 3000.00, 0.00, 3000.00, 0.00, 1),
(123, '123', 41, 'priyantha wadu karmika', '2023-04-08', '10:53:32', 781141603, 'Sanju', '', 250.00, 0.00, 250.00, 0.00, 1),
(124, '124', 42, 'T. Chandralatha', '2023-04-08', '15:41:10', 718137304, 'Lochana', '', 1470.00, 0.00, 1470.00, 0.00, 1),
(125, '125', 0, 'Ganesha', '2023-04-08', '16:49:22', 773936096, 'Lochana', '', 5280.00, 0.00, 2640.00, 2640.00, 0),
(126, '126', 0, 'Bakmegahapara Eksat Subasadaka Samithiya', '2023-04-08', '16:56:40', 716335527, 'Lochana', '', 6000.00, 0.00, 6000.00, 0.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(4) NOT NULL,
  `item_name` varchar(30) NOT NULL,
  `description` varchar(50) NOT NULL,
  `cost` double(12,2) NOT NULL,
  `qty` int(5) NOT NULL,
  `supplier` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `item_name`, `description`, `cost`, `qty`, `supplier`) VALUES
(1, 'White Mug ', 'White Mug ', 475.00, 36, 'Softnet '),
(2, 'Magic Mug', 'Magic Mug', 750.00, 10, 'Softnet '),
(3, 'Photocopy Machine ', 'Xerox / Sharp ', 3.00, 196590, ''),
(4, 'Color Copy ', 'Color Copy ', 30.00, 2500, ''),
(5, 'Graphic Design 15 min ', '', 300.00, 50, ''),
(6, 'Graphic Design 05 min ', '', 100.00, 48, ''),
(7, 'A4 Paper ', 'A4 Paper ', 3.00, -1248, ''),
(8, 'A3 Paper ', 'A3 Paper ', 7.00, 533, ''),
(9, 'Scan Machine ', 'Scan Machine ', 3.00, 4928, ''),
(10, '12x18 Sticker', 'Sticker ', 90.00, -20, ''),
(11, 'Color Laser Print A4', '', 30.00, 282, ''),
(12, 'BL 01 - Bervaly Crystal ', 'BL 01 - Bervaly Crystal ', 930.00, 5, 'Softnet '),
(13, 'BL 02 - Bervaly Crystal ', 'BL 02 - Bervaly Crystal ', 0.00, 0, 'Softnet '),
(14, 'BL 04 - Bervaly Crystal ', 'BL 04 - Bervaly Crystal ', 0.00, 0, 'Softnet '),
(15, 'BL 05 - Bervaly Crystal ', 'BL 05 - Bervaly Crystal ', 1850.00, 3, ''),
(16, 'Photo Rock SH 60', 'Photo Rock SH 60', 1500.00, 2, ''),
(17, 'Sublimention Paper', 'Sublimention Paper', 40.00, -30, 'Softnet '),
(18, 'Sublimention Print ', 'Sublimention Print ', 35.00, 4850, 'Sublimention Print '),
(19, 'Binding Tape', '', 20.00, 4, ''),
(20, 'Transparent Paper', '', 20.00, 44, ''),
(21, 'Back Paper', '', 20.00, 44, ''),
(22, 'Stapler', '', 2.00, 14982, ''),
(23, 'A4 Sticker Paper', '', 50.00, 44, ''),
(24, 'Agro Tech Ribbon Roll ', 'One Pice Price ', 1.00, -500, '');

-- --------------------------------------------------------

--
-- Table structure for table `makeProduct`
--

CREATE TABLE `makeProduct` (
  `id` int(4) NOT NULL,
  `item_name` varchar(30) NOT NULL,
  `product_name` varchar(30) NOT NULL,
  `qty` double(10,7) NOT NULL DEFAULT 1.0000000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `makeProduct`
--

INSERT INTO `makeProduct` (`id`, `item_name`, `product_name`, `qty`) VALUES
(1, '12x18 Sticker', '12x18 Sticker Print ', 1.0000000),
(2, 'Color Laser Print A4', '12x18 Sticker Print ', 1.0000000),
(3, 'A4 Paper ', 'Photocopy ', 1.0000000),
(4, 'Photocopy Machine ', 'Photocopy ', 1.0000000),
(5, 'A3 Paper ', 'A3 Photo Copy ', 1.0000000),
(6, 'Photocopy Machine ', 'A3 Photo Copy ', 2.0000000),
(7, 'Scan Machine ', 'Scan', 1.0000000),
(8, 'Color Laser Print A4', 'A4 Color Print Out ', 1.0000000),
(9, 'A4 Paper ', 'A4 Color Print Out ', 1.0000000),
(10, 'BL 01 - Bervaly Crystal ', 'Crystal Glass Photo Print  - B', 1.0000000),
(11, 'Sublimention Paper', 'Crystal Glass Photo Print  - B', 1.0000000),
(12, 'Sublimention Print ', 'Crystal Glass Photo Print  - B', 1.0000000),
(13, 'A4 Paper ', 'Crystal Glass Photo Print  - B', 1.0000000),
(14, 'A4 Paper ', 'Crystal Glass Photo Print  - B', 1.0000000),
(15, 'A4 Paper ', 'Crystal Glass Photo Print  - B', 1.0000000),
(16, 'Binding Tape', 'Tape Binding', 1.0000000),
(17, 'Transparent Paper', 'Tape Binding', 1.0000000),
(18, 'Back Paper', 'Tape Binding', 1.0000000),
(19, 'Stapler', 'Tape Binding', 3.0000000),
(20, 'Color Laser Print A4', 'A4 Sticker Print', 1.0000000),
(21, 'A4 Sticker Paper', 'A4 Sticker Print', 1.0000000),
(22, 'Agro Tech Ribbon Roll ', '1/2 Ribbon Print ', 1.0000000),
(23, 'Sublimention Paper', '1/2 Ribbon Print ', 0.0500000),
(24, 'Sublimention Print ', '1/2 Ribbon Print ', 0.0500000),
(25, 'A4 Paper ', '1/2 Ribbon Print ', 0.1000000),
(26, 'Graphic Design 05 min ', 'TYPE SETTING ', 1.0000000),
(27, 'A4 Paper ', 'TYPE SETTING ', 2.0000000),
(28, 'Photocopy Machine ', 'TYPE SETTING ', 2.0000000),
(29, 'A4 Paper ', 'Print Out ', 1.0000000),
(30, 'Photocopy Machine ', 'Print Out ', 1.0000000),
(31, 'Graphic Design 05 min ', 'Print Out ', 0.0100000),
(32, 'Graphic Design 05 min ', 'A4 Color Print Out ', 0.1000000),
(33, 'Graphic Design 05 min ', 'A3 Color Print Out ', 0.2000000),
(34, 'A3 Paper ', 'A3 Color Print Out ', 1.0000000),
(35, 'Color Laser Print A4', 'A3 Color Print Out ', 2.0000000),
(36, 'Color Laser Print A4', 'Graphic Design 1', 1.0000000),
(37, 'Graphic Design 05 min ', 'Graphic Design 1', 1.0000000),
(38, 'Photocopy Machine ', 'Graphic Design 1', 1.0000000);

-- --------------------------------------------------------

--
-- Table structure for table `pettycash`
--

CREATE TABLE `pettycash` (
  `id` int(4) NOT NULL,
  `perrycash` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `emp_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pettycash`
--

INSERT INTO `pettycash` (`id`, `perrycash`, `amount`, `date`, `emp_name`) VALUES
(1, '', 0.00, '2023-01-26 17:32:51', 'Lochana'),
(2, 'Thatha Colomo ', 1300.00, '2023-01-27 13:19:00', 'Lochana'),
(3, 'Tie', 450.00, '2023-01-28 10:30:07', 'Induwara Uthsara'),
(4, '', 0.00, '2023-01-28 15:11:40', ''),
(5, '', 0.00, '2023-01-28 15:11:40', ''),
(6, 'Full Rice', 750.00, '2023-01-28 20:17:07', 'Lochana'),
(7, 'Surgical Mask', 40.00, '2023-01-29 10:29:21', 'Lochana'),
(8, 'Hadunkuru', 100.00, '2023-01-30 14:21:39', 'Lochana'),
(9, 'Bank', 30561.00, '2023-02-02 09:59:10', 'Lochana'),
(10, 'Foods', 1000.00, '2023-02-03 08:17:28', 'Lochana'),
(11, '', 0.00, '2023-02-16 17:48:57', 'Lochana'),
(12, '', 0.00, '2023-03-05 11:23:31', 'Lochana'),
(13, 'Lochana Exp ', 50000.00, '2023-03-07 12:13:23', 'Lochana'),
(14, 'Salary ', 5000.00, '2023-03-07 12:16:10', 'Lochana'),
(15, 'Dinesh ', 1500.00, '2023-03-07 12:16:32', 'Lochana'),
(16, 'lochana', 21500.00, '2023-03-08 19:26:06', 'Lochana'),
(17, 'Cash', 15000.00, '2023-03-14 10:30:23', 'Lochana'),
(18, '', 0.00, '2023-03-18 16:08:38', 'Lochana'),
(19, '13023.50', 13023.50, '2023-03-20 08:37:50', 'Lochana'),
(20, 'Foods ', 7000.00, '2023-03-22 14:17:56', 'Lochana');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(10) NOT NULL,
  `product_name` varchar(30) NOT NULL,
  `description` varchar(50) NOT NULL,
  `stock_qty` int(5) NOT NULL,
  `rate` decimal(8,2) NOT NULL,
  `cost` decimal(8,2) NOT NULL,
  `profit` double(10,2) NOT NULL,
  `has_stock` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `description`, `stock_qty`, `rate`, `cost`, `profit`, `has_stock`) VALUES
(1, 'Photocopy ', '', -1248, 12.00, 6.00, 6.00, '0'),
(2, 'A3 Photo Copy ', '', 533, 25.00, 13.00, 12.00, '1'),
(3, 'Scan', '', 4928, 20.00, 3.00, 17.00, '1'),
(4, '12x18 Sticker Print ', '12x18 Sticker Print ', -20, 180.00, 120.00, 60.00, '0'),
(5, 'A4 Color Print Out ', 'A4 Color Print Out ', -1248, 40.00, 43.00, -3.00, '0'),
(6, 'Crystal Glass Photo Print  - B', 'Crystal Glass Photo Print  - BL01', -1248, 150.00, 1014.00, -864.00, '0'),
(7, 'Tape Binding', '', 4, 130.00, 66.00, 64.00, '1'),
(8, 'A4 Sticker Print', '', 44, 100.00, 80.00, 20.00, '1'),
(9, '1/2 Ribbon Print ', 'Agrotech', -12480, 8.50, 5.05, 3.45, '0'),
(10, 'TYPE SETTING ', 'TYPE SETTING ', -624, 120.00, 112.00, 8.00, '0'),
(11, 'Print Out ', '', -1248, 15.00, 7.00, 8.00, '0'),
(12, 'Graphic Design ', '', 0, 1000.00, 0.00, 1000.00, '0'),
(13, 'A3 Color Print Out ', '', 141, 80.00, 87.00, -7.00, '1'),
(14, 'Graphic Design 1', '', 48, 150.00, 133.00, 17.00, '1');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sales_id` int(5) NOT NULL,
  `invoice_number` varchar(10) NOT NULL,
  `product` varchar(30) NOT NULL,
  `description` varchar(50) NOT NULL,
  `qty` varchar(8) NOT NULL,
  `rate` decimal(8,2) NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `worker` varchar(30) NOT NULL,
  `todo` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sales_id`, `invoice_number`, `product`, `description`, `qty`, `rate`, `amount`, `worker`, `todo`) VALUES
(1, '00001', 'Photocopy ', '', '30', 10.00, 300.00, '', 'Unchecked'),
(2, '00002', 'Photocopy ', '', '23', 8.00, 184.00, '', 'Unchecked'),
(3, '00003', 'Photocopy ', '', '100', 8.00, 800.00, '', 'Unchecked'),
(4, '00003', 'Scan', '', '1', 20.00, 20.00, '', 'Unchecked'),
(5, '00005', '12x18 Sticker Print ', '', '2', 180.00, 360.00, '', 'Unchecked'),
(6, '00005', 'A4 Color Print Out ', '', '10', 40.00, 400.00, '', 'Unchecked'),
(7, '00006', '12x18 Sticker Print ', '', '1', 180.00, 180.00, '', 'Unchecked'),
(8, '00007', 'loclslddldls', '', '1', 100.00, 100.00, '', 'Unchecked'),
(9, '00008', 'ldsadosad', '', '1', 100.00, 100.00, '', 'Unchecked'),
(10, '00009', '12x18 Sticker Print ', '', '1', 180.00, 180.00, '', 'Unchecked'),
(11, '000010', '12x18 Sticker Print ', '', '1', 180.00, 180.00, '', 'Unchecked'),
(12, '000011', 'Photocopy ', '', '6', 12.00, 72.00, '', 'Unchecked'),
(13, '000012', 'asda', '', '1', 0.00, 0.00, '', 'Unchecked'),
(14, '000013', 'Photocopy ', '', '54', 12.00, 648.00, '', 'Unchecked'),
(15, '000014', 'Photocopy ', '', '30', 8.00, 240.00, '', 'Unchecked'),
(16, '000014', 'Type Setting ', '', '1', 50.00, 50.00, '', 'Unchecked'),
(17, '000014', 'Photocopy ', '', '5', 8.00, 40.00, '', 'Unchecked'),
(18, '000015', '12x18 Sticker Print ', '', '3', 180.00, 540.00, '', 'Unchecked'),
(19, '000016', 'Photocopy ', '', '32', 10.00, 320.00, '', 'Unchecked'),
(20, '000017', 'Tag Print Large Size ', '', '10000', 2.75, 27500.00, '', 'Unchecked'),
(21, '000018', 'Photocopy ', '', '7', 12.00, 84.00, '', 'Unchecked'),
(22, '000018', 'Edit', '', '1', 20.00, 20.00, '', 'Unchecked'),
(23, '000018', 'Type Setting', '', '7', 120.00, 840.00, '', 'Unchecked'),
(24, '000020', 'Photocopy ', '', '132', 12.00, 1584.00, '', 'Unchecked'),
(25, '000020', 'Tape Binding', '', '1', 130.00, 130.00, '', 'Unchecked'),
(26, '000021', 'A4 Sticker Print', '', '1', 100.00, 100.00, '', 'Unchecked'),
(27, '000022', 'Photocopy ', '', '2', 12.00, 24.00, '', 'Unchecked'),
(28, '000023', 'Members Pass Book ', '', '600', 28.00, 16800.00, '', 'Unchecked'),
(29, '000024', 'Seal', '', '1', 1000.00, 1000.00, '', 'Unchecked'),
(30, '000025', 'Type Setting ', '', '1', 70.00, 70.00, '', 'Unchecked'),
(31, '000026', '12x8 Photo Print ', '', '1', 1800.00, 1800.00, '', 'Unchecked'),
(32, '000027', 'CV', '', '1', 200.00, 200.00, '', 'Unchecked'),
(33, '000027', 'A4 Color Print Out ', '', '1', 40.00, 40.00, '', 'Unchecked'),
(34, '000028', 'Photocopy ', '', '20', 10.00, 200.00, '', 'Unchecked'),
(35, '000028', 'Photocopy ', '', '77', 10.00, 770.00, '', 'Unchecked'),
(36, '000029', '2255 Inked Seal ', '', '1', 1000.00, 1000.00, '', 'Unchecked'),
(37, '000030', 'Hard Bainding ', '', '1', 1600.00, 1600.00, '', 'Unchecked'),
(38, '000031', 'Photocopy ', '', '50', 8.00, 400.00, '', 'Unchecked'),
(39, '000032', '2255 Inked Seal ', '', '1', 1000.00, 1000.00, '', 'Unchecked'),
(40, '000033', 'Type Setting', '', '1', 500.00, 500.00, '', 'Unchecked'),
(41, '000034', '12x18 Sticker Print ', '', '8', 160.00, 1280.00, '', 'Unchecked'),
(42, '000034', 'A4 Sticker Print', '', '1', 100.00, 100.00, '', 'Unchecked'),
(43, '000035', '1/2 Ribbon Print ', '', '1000', 8.50, 8500.00, '', 'Unchecked'),
(44, '000036', 'scan ', '', '35', 8.00, 280.00, '', 'Unchecked'),
(45, '000036', 'Reload', '', '1', 444.00, 444.00, '', 'Unchecked'),
(46, '000037', 'Photocopy ', '', '20', 8.00, 160.00, '', 'Unchecked'),
(47, '000038', 'TYPE SETTING ', '', '1', 150.00, 150.00, '', 'Unchecked'),
(48, '000039', 'Photocopy ', '', '3', 10.00, 30.00, '', 'Unchecked'),
(49, '000040', 'A3 Photo Copy ', '', '360', 8.00, 2880.00, '', 'Unchecked'),
(50, '000040', 'A3 Photo Copy ', '', '100', 8.00, 800.00, '', 'Unchecked'),
(51, '000040', 'A3 Photo Copy ', '', '420', 8.00, 3360.00, '', 'Unchecked'),
(52, '000041', 'A4 Color Print Out ', '', '14', 35.00, 490.00, '', 'Unchecked'),
(53, '000042', 'Photocopy ', '', '2', 15.00, 30.00, '', 'Unchecked'),
(54, '000043', 'Tape Binding', '', '1', 130.00, 130.00, '', 'Unchecked'),
(55, '000044', 'A3 Photo Copy ', '', '24', 20.00, 480.00, '', 'Unchecked'),
(56, '000044', 'Photocopy ', '', '25', 10.00, 250.00, '', 'Unchecked'),
(57, '000044', 'TYPE SETTING ', '', '1', 100.00, 100.00, '', 'Unchecked'),
(58, '000046', 'A4 Color Print Out ', '', '3', 40.00, 120.00, '', 'Unchecked'),
(59, '000046', 'A4 Laminate ', '', '3', 80.00, 240.00, '', 'Unchecked'),
(60, '000047', 'A4 Color Print Out ', '', '2', 40.00, 80.00, '', 'Unchecked'),
(61, '000048', 'Photocopy ', '', '30', 10.00, 300.00, '', 'Unchecked'),
(62, '000049', 'TYPE SETTING ', '', '1', 70.00, 70.00, '', 'Unchecked'),
(63, '000049', 'Laminate ', '', '1', 80.00, 80.00, '', 'Unchecked'),
(64, '000050', 'Print Out ', '', '24', 12.00, 288.00, '', 'Unchecked'),
(65, '000051', 'TYPE SETTING ', '', '3', 150.00, 450.00, '', 'Unchecked'),
(66, '000051', 'A4 Color Print Out ', '', '1', 30.00, 30.00, '', 'Unchecked'),
(67, '000052', 'Print Out ', '', '2', 12.00, 24.00, '', 'Unchecked'),
(68, '000052', 'Photocopy ', '', '6', 10.00, 60.00, '', 'Unchecked'),
(69, '000053', 'Scan', '', '2', 20.00, 40.00, '', 'Unchecked'),
(70, '000054', 'Print Out ', '', '6', 12.00, 72.00, '', 'Unchecked'),
(71, '000055', '12x18 Sticker Print ', '', '13', 300.00, 3900.00, '', 'Unchecked'),
(72, '000055', 'PVS Sticker 4x7 ', '', '28', 320.00, 8960.00, '', 'Unchecked'),
(73, '000055', 'Bill book ', '', '10', 1400.00, 14000.00, '', 'Unchecked'),
(74, '000056', 'Inked Seal ', '', '5', 950.00, 4750.00, '', 'Unchecked'),
(75, '000057', '1/2 Ribbon Print ', '', '1100', 8.50, 9350.00, '', 'Unchecked'),
(76, '000058', 'A4 Color Print Out ', '', '1', 40.00, 40.00, '', 'Unchecked'),
(77, '59', 'Photocopy ', '', '2', 15.00, 30.00, '', 'Unchecked'),
(78, '60', 'A4 Color Print Out ', '', '4', 40.00, 160.00, '', 'Unchecked'),
(79, '60', 'Print Out ', '', '1', 15.00, 15.00, '', 'Unchecked'),
(80, '61', 'Print Out ', '', '2', 15.00, 30.00, '', 'Unchecked'),
(81, '61', 'Print Out ', '', '56', 10.00, 560.00, '', 'Unchecked'),
(82, '62', 'Print Out ', '', '135', 7.50, 1012.50, '', 'Unchecked'),
(83, '64', 'Magic Mug ', '', '1', 1400.00, 1400.00, '', 'Unchecked'),
(84, '65', 'Print Out ', '', '3', 10.00, 30.00, '', 'Unchecked'),
(85, '66', 'Print Out ', '', '32', 12.00, 384.00, '', 'Unchecked'),
(86, '67', 'Scan ', '', '14', 10.00, 140.00, '', 'Unchecked'),
(87, '68', 'Print Out ', '', '10', 10.00, 100.00, '', 'Unchecked'),
(88, '69', 'TYPE SETTING ', '', '1', 150.00, 150.00, '', 'Unchecked'),
(89, '69', 'A4 Color Print Out ', '', '2', 40.00, 80.00, '', 'Unchecked'),
(90, '69', 'Photocopy ', '', '1', 12.00, 12.00, '', 'Unchecked'),
(91, '70', 'Ticket Book Print ', '', '1', 2400.00, 2400.00, '', 'Unchecked'),
(92, '71', 'Print Out ', '', '134', 8.00, 1072.00, '', 'Unchecked'),
(93, '72', 'Artwork ', '', '1', 1000.00, 1000.00, '', 'Unchecked'),
(94, '72', 'TYPE SETTING ', '', '2', 120.00, 240.00, '', 'Unchecked'),
(95, '72', '22 55 Inekd Seal ', '', '1', 1100.00, 1100.00, '', 'Unchecked'),
(96, '72', 'Bill Book ', '', '1', 1000.00, 1000.00, '', 'Unchecked'),
(97, '72', 'A4 Color Print Out ', '', '6', 40.00, 240.00, '', 'Unchecked'),
(98, '73', 'TYPE SETTING ', '', '1', 120.00, 120.00, '', 'Unchecked'),
(99, '73', 'A4 Color Print Out ', '', '1', 40.00, 40.00, '', 'Unchecked'),
(100, '73', 'Photocopy ', '', '7', 12.00, 84.00, '', 'Unchecked'),
(101, '74', 'Print Out ', '', '61', 8.00, 488.00, '', 'Unchecked'),
(102, '75', 'TYPE SETTING ', '', '4', 120.00, 480.00, '', 'Unchecked'),
(103, '75', 'Photocopy ', '', '30', 12.00, 360.00, '', 'Unchecked'),
(104, '77', 'TYPE SETTING ', '', '4', 120.00, 480.00, '', 'Unchecked'),
(105, '77', 'Photocopy ', '', '30', 12.00, 360.00, '', 'Unchecked'),
(106, '78', 'Print Out ', '', '2', 15.00, 30.00, '', 'Unchecked'),
(107, '78', 'A4 Sticker Print', '', '1', 100.00, 100.00, '', 'Unchecked'),
(108, '79', 'Print Out ', '', '14', 12.00, 168.00, '', 'Unchecked'),
(109, '80', 'Print Out ', '', '1', 15.00, 15.00, '', 'Unchecked'),
(110, '81', 'A3 Photo Copy ', '', '1', 25.00, 25.00, '', 'Unchecked'),
(111, '82', 'Photocopy ', '', '1', 12.00, 12.00, '', 'Unchecked'),
(112, '82', 'Print Out ', '', '16', 12.00, 192.00, '', 'Unchecked'),
(113, '82', 'A4 Color Print Out ', '', '1', 40.00, 40.00, '', 'Unchecked'),
(114, '82', 'TYPE SETTING ', '', '1', 120.00, 120.00, '', 'Unchecked'),
(115, '82', 'A4 Color Print Out ', '', '2', 40.00, 80.00, '', 'Unchecked'),
(116, '83', 'Photocopy ', '', '28', 12.00, 336.00, '', 'Unchecked'),
(119, '84', 'Magic Mug ', '', '1', 1400.00, 1400.00, '', 'Unchecked'),
(120, '84', 'Mug ', '', '1', 1000.00, 1000.00, '', 'Unchecked'),
(121, '86', 'Print Out ', '', '41', 12.00, 492.00, '', 'Unchecked'),
(122, '87', '12x18 Sticker Print ', '', '1', 180.00, 180.00, '', 'Unchecked'),
(123, '87', '1/2 Ribbon Print ', '', '1', 8.50, 8.50, '', 'Unchecked'),
(124, '87', 'File Cover', '', '1', 35.00, 35.00, '', 'Unchecked'),
(125, '87', 'Photocopy ', '', '35', 12.00, 420.00, '', 'Unchecked'),
(126, '87', 'A4 Color Print Out ', '', '7', 40.00, 280.00, '', 'Unchecked'),
(127, '87', 'Graphic Design ', '', '1', 300.00, 300.00, '', 'Unchecked'),
(128, '90', 'Print Out ', '', '28', 12.00, 336.00, '', 'Unchecked'),
(129, '90', 'Print Out ', '', '15', 12.00, 180.00, '', 'Unchecked'),
(130, '91', 'A4 Color Print Out ', '', '1', 40.00, 40.00, '', 'Unchecked'),
(131, '91', 'bainding ', '', '1', 150.00, 150.00, '', 'Unchecked'),
(132, '91', 'TYPE SETTING ', '', '1', 60.00, 60.00, '', 'Unchecked'),
(133, '92', 'Graphic Design ', '', '1', 500.00, 500.00, '', 'Unchecked'),
(134, '92', 'A3 Color Print ', '', '4', 80.00, 320.00, '', 'Unchecked'),
(135, '92', 'A4 Color Print Out ', '', '10', 40.00, 400.00, '', 'Unchecked'),
(136, '93', 'A3 Color Print Out ', '', '4', 80.00, 320.00, '', 'Unchecked'),
(137, '94', 'Photocopy ', '', '8', 12.00, 96.00, '', 'Unchecked'),
(138, '95', 'Print Out ', '', '10', 15.00, 150.00, '', 'Unchecked'),
(139, '96', 'Graphic Design 1', '', '1', 150.00, 150.00, '', 'Unchecked'),
(140, '97', 'TYPE SETTING ', '', '1', 120.00, 120.00, '', 'Unchecked'),
(141, '97', 'Photocopy ', '', '3', 12.00, 36.00, '', 'Unchecked'),
(142, '97', 'Photocopy ', '', '35', 12.00, 420.00, '', 'Unchecked'),
(143, '97', 'Photocopy ', '', '2', 12.00, 24.00, '', 'Unchecked'),
(144, '97', 'Photocopy ', '', '10', 12.00, 120.00, '', 'Unchecked'),
(145, '97', 'Scan', '', '20', 20.00, 400.00, '', 'Unchecked'),
(146, '97', 'TYPE SETTING ', '', '1', 100.00, 100.00, '', 'Unchecked'),
(147, '97', 'Print Out ', '', '3', 15.00, 45.00, '', 'Unchecked'),
(148, '98', 'A4 Color Print Out ', '', '20', 40.00, 800.00, '', 'Unchecked'),
(149, '99', 'TYPE SETTING ', '', '3', 120.00, 360.00, '', 'Unchecked'),
(150, '99', 'Photocopy ', '', '30', 12.00, 360.00, '', 'Unchecked'),
(151, '100', 'TYPE SETTING ', '', '3', 100.00, 300.00, '', 'Unchecked'),
(152, '100', 'TYPE SETTING ', '', '1', 200.00, 200.00, '', 'Unchecked'),
(153, '101', 'Photocopy ', '', '20', 12.00, 240.00, '', 'Unchecked'),
(154, '101', 'TYPE SETTING ', '', '1', 120.00, 120.00, '', 'Unchecked'),
(155, '101', 'A4 Color Print Out ', '', '1', 40.00, 40.00, '', 'Unchecked'),
(156, '101', 'A4 Color Print Out ', '', '2', 40.00, 80.00, '', 'Unchecked'),
(157, '102', 'Graphic Design 1', '', '1', 150.00, 150.00, '', 'Unchecked'),
(158, '102', 'A4 Color Print Out ', '', '11', 40.00, 440.00, '', 'Unchecked'),
(159, '102', 'Print Out ', '', '2', 15.00, 30.00, '', 'Unchecked'),
(160, '102', 'Print Out ', '', '15', 20.00, 300.00, '', 'Unchecked'),
(161, '103', 'A3 Color Print Out ', '', '23', 100.00, 2300.00, '', 'Unchecked'),
(162, '104', 'TYPE SETTING ', '', '1', 1480.00, 1480.00, '', 'Unchecked'),
(163, '105', 'A4 Color Print Out ', '', '1', 40.00, 40.00, '', 'Unchecked'),
(164, '105', 'A4 Black & White Print', '', '1', 12.00, 12.00, '', 'Unchecked'),
(165, '105', 'A4 Laminate', '', '1', 80.00, 80.00, '', 'Unchecked'),
(166, '106', 'A3 Color Print Out ', '', '3', 70.00, 210.00, '', 'Unchecked'),
(167, '107', 'Invoice Book ', '', '5', 1700.00, 8500.00, '', 'Unchecked'),
(168, '108', 'Batch Party Ticket Print ', '', '1', 4000.00, 4000.00, '', 'Unchecked'),
(169, '109', '5x3 Flex ', '', '3', 3500.00, 10500.00, '', 'Unchecked'),
(170, '109', 'Invitation ', '', '1', 1200.00, 1200.00, '', 'Unchecked'),
(171, '109', 'Artwork ', '', '1', 1000.00, 1000.00, '', 'Unchecked'),
(172, '110', 'A4 Color Print Out ', '', '1', 40.00, 40.00, '', 'Unchecked'),
(173, '110', 'A3 Color Print Out ', '', '1', 80.00, 80.00, '', 'Unchecked'),
(174, '110', 'A4 Sticker Print', '', '1', 100.00, 100.00, '', 'Unchecked'),
(175, '110', 'Laminate ', '', '1', 80.00, 80.00, '', 'Unchecked'),
(176, '110', 'Laminate A3', '', '1', 120.00, 120.00, '', 'Unchecked'),
(177, '110', 'A4 Color Print Out ', '', '1', 40.00, 40.00, '', 'Unchecked'),
(178, '110', 'A3 Color Print Out ', '', '1', 80.00, 80.00, '', 'Unchecked'),
(179, '110', 'A4 Sticker Print', '', '1', 100.00, 100.00, '', 'Unchecked'),
(180, '110', 'Laminate ', '', '1', 80.00, 80.00, '', 'Unchecked'),
(181, '110', 'Laminate A3', '', '1', 120.00, 120.00, '', 'Unchecked'),
(182, '112', 'A3 Color Print Out ', '', '1', 80.00, 80.00, '', 'Unchecked'),
(183, '112', 'A4 Sticker Print', '', '1', 100.00, 100.00, '', 'Unchecked'),
(184, '112', 'A4 Color Print Out ', '', '1', 40.00, 40.00, '', 'Unchecked'),
(185, '112', 'Laminate A4', '', '1', 80.00, 80.00, '', 'Unchecked'),
(186, '112', 'Laminate A3', '', '1', 120.00, 120.00, '', 'Unchecked'),
(187, '113', 'Bill Book ', '', '20', 440.00, 8800.00, '', 'Unchecked'),
(188, '114', '6 x 7.5 Flex', '', '2', 8100.00, 16200.00, '', 'Unchecked'),
(189, '114', '6 x 4 Flex', '', '10', 4320.00, 43200.00, '', 'Unchecked'),
(190, '114', '3 x 2 Flex ', '', '4', 1080.00, 4320.00, '', 'Unchecked'),
(191, '115', '1/2 Ribbon Print ', '', '900', 8.50, 7650.00, '', 'Unchecked'),
(192, '116', '3x2 Flex ', '', '1', 1600.00, 1600.00, '', 'Unchecked'),
(193, '117', 'Inked Seal ', '', '1', 1200.00, 1200.00, '', 'Unchecked'),
(194, '118', 'Graphic Design 1', '', '1', 150.00, 150.00, '', 'Unchecked'),
(195, '118', 'A3 Color Print Out ', '', '2', 80.00, 160.00, '', 'Unchecked'),
(196, '119', 'Flex Print ', '', '1', 1200.00, 1200.00, '', 'Unchecked'),
(197, '120', 'Initation Cards ', '', '20', 60.00, 1200.00, '', 'Unchecked'),
(198, '120', 'Graphic Design 1', '', '1', 150.00, 150.00, '', 'Unchecked'),
(199, '120', 'Envilop ', '', '50', 10.00, 500.00, '', 'Unchecked'),
(200, '121', 'A3 Photo Copy ', '', '2', 25.00, 50.00, '', 'Unchecked'),
(201, '121', 'Graphic Design 1', '', '1', 150.00, 150.00, '', 'Unchecked'),
(202, '122', 'Photo Print 10x8', '', '2', 1500.00, 3000.00, '', 'Unchecked'),
(203, '123', 'A4 Color Print Out ', '', '3', 40.00, 120.00, '', 'Unchecked'),
(204, '123', 'letter head design', '', '1', 130.00, 130.00, '', 'Unchecked'),
(205, '124', 'A3 Photo Copy ', '', '25', 30.00, 750.00, '', 'Unchecked'),
(206, '124', 'A4 Photocopy', '', '60', 12.00, 720.00, '', 'Unchecked'),
(207, '125', 'A4 Photocopy', '', '480', 10.00, 4800.00, '', 'Unchecked'),
(208, '125', 'Tape Binding', '', '4', 120.00, 480.00, '', 'Unchecked'),
(209, '126', 'Dinum Adime Ticket Poth', '', '100', 60.00, 6000.00, '', 'Unchecked');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employ_id`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_name` (`item_name`);

--
-- Indexes for table `makeProduct`
--
ALTER TABLE `makeProduct`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product` (`product_name`) USING BTREE,
  ADD KEY `item` (`item_name`);

--
-- Indexes for table `pettycash`
--
ALTER TABLE `pettycash`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `product_name` (`product_name`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`sales_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employ_id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `makeProduct`
--
ALTER TABLE `makeProduct`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `pettycash`
--
ALTER TABLE `pettycash`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sales_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=210;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `makeProduct`
--
ALTER TABLE `makeProduct`
  ADD CONSTRAINT `item` FOREIGN KEY (`item_name`) REFERENCES `items` (`item_name`) ON UPDATE CASCADE,
  ADD CONSTRAINT `product` FOREIGN KEY (`product_name`) REFERENCES `products` (`product_name`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

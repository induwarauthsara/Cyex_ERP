-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 02, 2024 at 05:22 PM
-- Server version: 10.6.18-MariaDB-0ubuntu0.22.04.1
-- PHP Version: 8.1.2-1ubuntu2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ifix`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `account_name` varchar(30) NOT NULL,
  `account_type` varchar(20) NOT NULL DEFAULT 'cash',
  `amount` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `account_name`, `account_type`, `amount`) VALUES
(1, 'Stock Account', 'cash', -11675.00),
(2, 'Company Profit', 'cash', 712692.50),
(6, 'Utility Bills', 'cash', 0.00),
(7, 'cash_in_hand', 'cash', 738350.00),
(17, 'Dfcc', 'bank', 500.00);

-- --------------------------------------------------------

--
-- Table structure for table `action_log`
--

CREATE TABLE `action_log` (
  `action_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `action` varchar(250) NOT NULL,
  `description` text DEFAULT NULL,
  `date` date DEFAULT curdate(),
  `time` time DEFAULT curtime()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `action` enum('Clock In','Clock Out') NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `time` time NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `employee_id`, `action`, `date`, `time`) VALUES
(1, 1, 'Clock Out', '2024-09-29', '12:44:36');

-- --------------------------------------------------------

--
-- Table structure for table `bank_deposits`
--

CREATE TABLE `bank_deposits` (
  `bank_deposit_id` int(11) NOT NULL,
  `bank_account` varchar(20) NOT NULL,
  `amount` decimal(11,2) NOT NULL,
  `deposit_date` date NOT NULL DEFAULT current_timestamp(),
  `deposit_time` time NOT NULL DEFAULT current_timestamp(),
  `employee_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(3) NOT NULL,
  `customer_name` varchar(40) NOT NULL,
  `customer_type` varchar(10) DEFAULT NULL,
  `customer_mobile` int(10) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `customer_name`, `customer_type`, `customer_mobile`) VALUES
(1, 'gftjfu', NULL, 75562525),
(2, 'Cash', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employ_id` int(4) NOT NULL,
  `emp_name` varchar(50) NOT NULL,
  `mobile` int(10) NOT NULL,
  `address` varchar(150) DEFAULT NULL,
  `bank_account` varchar(30) DEFAULT NULL,
  `role` varchar(10) NOT NULL DEFAULT 'Employee',
  `nic` varchar(15) NOT NULL,
  `salary` decimal(8,2) NOT NULL DEFAULT 0.00,
  `day_salary` decimal(8,2) NOT NULL,
  `password` varchar(30) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1,
  `dp` longblob DEFAULT NULL,
  `is_clocked_in` tinyint(1) DEFAULT 0,
  `onboard_date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employ_id`, `emp_name`, `mobile`, `address`, `bank_account`, `role`, `nic`, `salary`, `day_salary`, `password`, `status`, `dp`, `is_clocked_in`, `onboard_date`) VALUES
(0, 'ifix', 718366077, '', '', 'Admin', '', 0.00, 0.00, 'indroot', 1, '', 0, '2024-09-08'),
(1, 'lakmal', 718366077, '0', '0', 'Admin', '0', 0.00, 0.00, 'admin@132', 1, '', 0, '2024-09-08'),
(14, 'Udaya', 779006160, '0', '8130907192 Commercial', 'Employee', '861722787', 0.00, 0.00, 'udaya', 1, NULL, 0, '2024-09-10'),
(15, 'Kasun', 0, '0', '0', 'Employee', '0', 0.00, 0.00, 'kasun', 1, NULL, 0, '2024-09-10');

-- --------------------------------------------------------

--
-- Table structure for table `error_log`
--

CREATE TABLE `error_log` (
  `error_id` int(11) NOT NULL,
  `error_code` int(11) NOT NULL,
  `error_message` text NOT NULL,
  `query` text NOT NULL,
  `action` varchar(250) NOT NULL,
  `action_description` text NOT NULL,
  `date` date DEFAULT curdate(),
  `time` time DEFAULT curtime(),
  `employee_id` int(11) NOT NULL,
  `status` enum('pending','fixed','','') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoice_number` int(10) NOT NULL,
  `invoice_type` varchar(10) NOT NULL DEFAULT 'product',
  `invoice_description` text DEFAULT NULL,
  `customer_name` varchar(80) NOT NULL,
  `invoice_date` date NOT NULL DEFAULT current_timestamp(),
  `time` time DEFAULT current_timestamp(),
  `customer_mobile` int(10) NOT NULL,
  `biller` varchar(20) NOT NULL,
  `primary_worker` varchar(20) NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) NOT NULL,
  `advance` decimal(15,2) NOT NULL,
  `balance` decimal(15,2) NOT NULL,
  `cost` double(10,2) NOT NULL DEFAULT 0.00,
  `profit` double(10,2) NOT NULL DEFAULT 0.00,
  `full_paid` tinyint(1) NOT NULL,
  `paymentMethod` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`invoice_number`, `invoice_type`, `invoice_description`, `customer_name`, `invoice_date`, `time`, `customer_mobile`, `biller`, `primary_worker`, `total`, `discount`, `advance`, `balance`, `cost`, `profit`, `full_paid`, `paymentMethod`) VALUES
(1, 'product', '', 'Cash', '2024-09-19', '09:07:44', 0, 'lakmal', 'Udaya', 5500.00, 0.00, 5500.00, 0.00, 3500.00, 2000.00, 1, 'Cash'),
(2, 'product', '', 'Cash', '2024-09-19', '10:53:54', 0, 'ifix', 'ifix', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, 'Cash'),
(3, 'product', '', 'Cash', '2024-09-19', '10:53:55', 0, 'ifix', 'ifix', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, 'Cash'),
(4, 'product', '', 'Cash', '2024-09-19', '10:53:56', 0, 'ifix', 'ifix', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, 'Cash'),
(5, 'product', '', 'Cash', '2024-09-23', '11:00:44', 0, 'lakmal', 'lakmal', 800.00, 0.00, 800.00, 0.00, 0.00, 600.00, 1, 'Cash'),
(6, 'product', '', 'Cash', '2024-09-28', '09:58:37', 0, 'lakmal', 'lakmal', 850.00, 0.00, 850.00, 0.00, 400.00, 450.00, 1, 'Cash'),
(7, 'product', '', 'Cash', '2024-10-01', '11:35:06', 0, 'lakmal', 'lakmal', 700.00, 0.00, 700.00, 0.00, 450.00, 250.00, 1, 'Cash'),
(8, 'product', '', 'Cash', '2024-10-01', '11:36:08', 0, 'lakmal', 'lakmal', 800.00, 0.00, 800.00, 0.00, 400.00, 400.00, 1, 'Cash'),
(9, 'product', '', 'Cash', '2024-10-01', '11:38:58', 0, 'lakmal', 'lakmal', 850.00, 0.00, 850.00, 0.00, 400.00, 450.00, 1, 'Cash'),
(10, 'product', ' ', 'Cash', '2024-10-01', '11:40:35', 0, 'lakmal', 'lakmal', 700.00, 0.00, 700.00, 0.00, 400.00, 300.00, 1, 'Cash'),
(11, 'product', '', 'Cash', '2024-10-02', '05:00:51', 0, 'lakmal', 'lakmal', 650.00, 0.00, 650.00, 0.00, 250.00, 400.00, 1, 'Cash'),
(12, 'product', ' ', 'Cash', '2024-10-01', '05:05:24', 0, 'lakmal', 'lakmal', 4250.00, 0.00, 4200.00, 50.00, 2300.00, 1900.00, 0, 'Cash'),
(13, 'product', '', 'Cash', '2024-10-01', '06:46:27', 0, 'lakmal', 'lakmal', 2400.00, 0.00, 2400.00, 0.00, 1400.00, 1000.00, 1, 'Cash'),
(14, 'product', '', 'Cash', '2024-10-02', '09:17:46', 0, 'lakmal', 'lakmal', 400.00, 0.00, 400.00, 0.00, 200.00, 200.00, 1, 'Cash'),
(15, 'product', '', 'Cash', '2024-10-02', '11:06:37', 0, 'lakmal', 'lakmal', 3500.00, 0.00, 3500.00, 0.00, 0.00, 3500.00, 1, 'Cash'),
(16, 'repair', 'PW : 12354', 'Cash', '2024-10-02', '16:34:57', 0, 'ifix', 'ifix', 120000.00, 0.00, 120000.00, 0.00, 4100.00, 115900.00, 1, 'Cash'),
(17, 'repair', '', 'Cash', '2024-10-02', '16:55:46', 0, 'ifix', 'ifix', 240000.00, 0.00, 240000.00, 0.00, 10020.00, 229980.00, 1, 'Cash'),
(18, 'product', '', 'Cash', '2024-10-02', '17:00:28', 0, 'ifix', 'ifix', 122000.00, 0.00, 122000.00, 0.00, 0.00, 122000.00, 1, 'Cash'),
(19, 'repair', '', 'Cash', '2024-10-02', '17:03:30', 0, 'ifix', 'ifix', 122000.00, 0.00, 122000.00, 0.00, 5610.00, 116390.00, 1, 'Cash'),
(20, 'product', '', 'Cash', '2024-10-02', '22:44:45', 0, 'ifix', 'ifix', 120000.00, 0.00, 120000.00, 0.00, 0.00, 120000.00, 1, 'Cash');

-- --------------------------------------------------------

--
-- Table structure for table `InvoiceBalPayRecords`
--

CREATE TABLE `InvoiceBalPayRecords` (
  `InvBalPayRecords_id` int(11) NOT NULL,
  `invoice_number` int(10) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `time` time NOT NULL DEFAULT current_timestamp(),
  `account` varchar(30) NOT NULL,
  `invoice_status` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `InvoiceBalPayRecords`
--

INSERT INTO `InvoiceBalPayRecords` (`InvBalPayRecords_id`, `invoice_number`, `amount`, `date`, `time`, `account`, `invoice_status`) VALUES
(1, 6, 850.00, '2024-09-28', '09:59:06', 'cash_in_hand', 'Invoice Full Paid');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(4) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `description` varchar(50) DEFAULT NULL,
  `cost` double(12,2) NOT NULL,
  `qty` int(5) NOT NULL,
  `supplier` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `item_name`, `description`, `cost`, `qty`, `supplier`) VALUES
(11, 'oms 5C batery ', NULL, 350.00, 29, NULL),
(12, 'oms 4c', NULL, 350.00, 4, NULL),
(13, 'Charging Pin ', NULL, 150.00, 29, NULL),
(14, 'dfg', NULL, 345.00, 34, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `makeProduct`
--

CREATE TABLE `makeProduct` (
  `id` int(6) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `qty` double(10,7) NOT NULL DEFAULT 1.0000000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `makeProduct`
--

INSERT INTO `makeProduct` (`id`, `item_name`, `product_name`, `qty`) VALUES
(6, 'oms 4c', 'oms 4c battery 3 month worrenty ', 1.0000000);

-- --------------------------------------------------------

--
-- Table structure for table `oneTimeProducts_sales`
--

CREATE TABLE `oneTimeProducts_sales` (
  `oneTimeProduct_id` int(11) NOT NULL,
  `invoice_number` int(10) NOT NULL,
  `product` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `qty` int(5) NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `profit` decimal(10,2) DEFAULT NULL,
  `status` enum('uncleared','skip','cleared') NOT NULL DEFAULT 'uncleared',
  `worker` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `oneTimeProducts_sales`
--

INSERT INTO `oneTimeProducts_sales` (`oneTimeProduct_id`, `invoice_number`, `product`, `qty`, `rate`, `amount`, `cost`, `profit`, `status`, `worker`) VALUES
(1, 1, 'llolko', 58, 65.00, 3770.00, NULL, NULL, 'skip', 'lakmal'),
(2, 4, 'Chargin Port Repart', 1, 500.00, 500.00, 200.00, 300.00, 'cleared', 'lakmal'),
(3, 5, 'M02 Backcover', 1, 600.00, 600.00, 0.00, 600.00, 'cleared', 'lakmal'),
(4, 8, 'Charging Pin Repair ', 1, 500.00, 500.00, NULL, NULL, 'skip', 'lakmal'),
(5, 15, '9a battery', 1, 3500.00, 3500.00, NULL, NULL, 'uncleared', 'lakmal'),
(6, 18, 'iPhone 12 Display Replace', 10, 5000.00, 120000.00, NULL, NULL, 'uncleared', 'ifix'),
(7, 18, 'M20 Display Replacement', 100, 500.00, 2000.00, NULL, NULL, 'uncleared', 'ifix'),
(8, 20, 'iPhone 12 Display Replace', 10, 5000.00, 120000.00, NULL, NULL, 'uncleared', 'ifix');

-- --------------------------------------------------------

--
-- Table structure for table `pettycash`
--

CREATE TABLE `pettycash` (
  `id` int(4) NOT NULL,
  `perrycash` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `time` time NOT NULL DEFAULT current_timestamp(),
  `emp_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(10) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `description` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_sinhala_ci DEFAULT NULL,
  `stock_qty` int(5) NOT NULL DEFAULT 1,
  `rate` decimal(8,2) NOT NULL,
  `cost` decimal(8,2) NOT NULL,
  `profit` double(10,2) NOT NULL,
  `has_stock` text NOT NULL DEFAULT '1',
  `stock_alert_limit` int(4) NOT NULL DEFAULT 20,
  `worker_commision` decimal(8,2) NOT NULL DEFAULT 0.00,
  `image` varchar(100) DEFAULT NULL,
  `show_in_landing_page` varchar(8) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `description`, `stock_qty`, `rate`, `cost`, `profit`, `has_stock`, `stock_alert_limit`, `worker_commision`, `image`, `show_in_landing_page`) VALUES
(5, 'oms 4c battery 3 month worrenty ', NULL, 4, 800.00, 350.00, 450.00, '1', 2, 0.00, NULL, ''),
(8, 'Oms 5c battery(3 month warranty)', NULL, 20, 800.00, 160.00, 540.00, '1', 5, 100.00, NULL, '0'),
(10, 'wireless Keyboard', NULL, 1, 4650.00, 3600.00, 1050.00, '1', 1, 0.00, NULL, '0'),
(11, 'Oms X3 Speeker', NULL, 2, 2850.00, 1850.00, 1000.00, '1', 1, 0.00, NULL, '0'),
(12, 'sy 718 Speeker', NULL, 1, 3750.00, 3250.00, 500.00, '1', 0, 0.00, NULL, '0'),
(13, 'Car holder JE027', NULL, 4, 2350.00, 1350.00, 1000.00, '1', 1, 0.00, NULL, '0'),
(14, 'bike holder By555', NULL, 5, 1350.00, 700.00, 650.00, '1', 1, 0.00, NULL, '0'),
(15, 'Car holder Jx033', NULL, 2, 1350.00, 750.00, 600.00, '1', 1, 0.00, NULL, '0'),
(16, 'Car Holder JX044', NULL, 1, 1350.00, 750.00, 600.00, '1', 0, 0.00, NULL, '0'),
(17, 'Bike holder Y02', NULL, 3, 1850.00, 900.00, 950.00, '1', 1, 0.00, NULL, '0'),
(18, 'Car holder MDe17', NULL, 3, 1350.00, 700.00, 650.00, '1', 1, 0.00, NULL, '0'),
(19, 'Car holder onetouch', NULL, 1, 950.00, 450.00, 500.00, '1', 0, 0.00, NULL, '0'),
(20, 'Car Charger JKXC86', NULL, 3, 1350.00, 700.00, 650.00, '1', 0, 0.00, NULL, '0'),
(21, 'Car charger A931', NULL, 2, 1350.00, 750.00, 600.00, '1', 0, 0.00, NULL, '0'),
(22, 'OSg09 Car charger', NULL, 4, 1650.00, 850.00, 800.00, '1', 2, 0.00, NULL, '0'),
(23, 'Samsung car adapter micro', NULL, 5, 650.00, 350.00, 300.00, '1', 2, 0.00, NULL, '0'),
(24, 'A903 Car charger', NULL, 9, 1250.00, 700.00, 550.00, '1', 2, 0.00, NULL, '0'),
(25, 'Om208 Car charger', NULL, 5, 2450.00, 1400.00, 1050.00, '1', 1, 0.00, NULL, '0'),
(26, 'om218 Car Charger', NULL, 10, 2650.00, 1450.00, 1200.00, '1', 2, 0.00, NULL, '0'),
(27, 'x6 Wireless mouse', NULL, 0, 1450.00, 700.00, 750.00, '0', 0, 0.00, NULL, '0'),
(28, 'M20 wire Mouse', NULL, 4, 650.00, 350.00, 300.00, '1', 0, 0.00, NULL, '0'),
(29, 'm3 wire mouse', NULL, 2, 750.00, 450.00, 300.00, '1', 0, 0.00, NULL, '0'),
(30, 'jm029 mouse', NULL, 2, 850.00, 450.00, 400.00, '1', 0, 0.00, NULL, '0'),
(31, 'Jr1 mouse', NULL, 1, 1450.00, 750.00, 700.00, '1', 0, 0.00, NULL, '0'),
(32, 'JR2 mouse wireless', NULL, 1, 1650.00, 850.00, 800.00, '1', 0, 0.00, NULL, '0'),
(33, 'flexible keyboard', NULL, 0, 1950.00, 850.00, 1100.00, '0', 0, 0.00, NULL, '0'),
(34, 'm100 hp mouse', NULL, 1, 1450.00, 700.00, 750.00, '1', 0, 0.00, NULL, '0'),
(35, '2.4 wireless mouse', NULL, 1, 1450.00, 700.00, 750.00, '1', 0, 0.00, NULL, '0'),
(36, 'sony mouse', NULL, 2, 450.00, 250.00, 200.00, '1', 0, 0.00, NULL, '0'),
(37, 'yayi mouse', NULL, 1, 550.00, 250.00, 300.00, '1', 0, 0.00, NULL, '0'),
(38, '20m mouse', NULL, 1, 1650.00, 950.00, 700.00, '1', 0, 0.00, NULL, '0'),
(39, '65W pd type C charger', NULL, 1, 3250.00, 1750.00, 1500.00, '1', 0, 0.00, NULL, '0'),
(40, 'note 10 charger', NULL, 4, 1150.00, 650.00, 500.00, '1', 0, 0.00, NULL, '0'),
(41, 'oms Ch32 01 (month warranty)', NULL, 7, 2250.00, 980.00, 1270.00, '1', 0, 0.00, NULL, '0'),
(42, 'oms Ch31 35W charger', NULL, 2, 2850.00, 1200.00, 1650.00, '1', 0, 0.00, NULL, '0'),
(43, '45W type C adepter', NULL, 1, 1850.00, 980.00, 870.00, '1', 0, 0.00, NULL, '0'),
(44, '15w Samsung travel adapter', NULL, 2, 1750.00, 980.00, 770.00, '1', 0, 0.00, NULL, '0'),
(45, '25W pd adapter with cable', NULL, 2, 2600.00, 1300.00, 1300.00, '1', 0, 0.00, NULL, '0'),
(46, 'sm12 sinha micro charger', NULL, 1, 1450.00, 700.00, 750.00, '1', 0, 0.00, NULL, '0'),
(47, 'osy08 pd 20w charger lighting', NULL, 5, 2650.00, 1450.00, 1200.00, '1', 2, 0.00, NULL, '0'),
(48, 'Osc26 onesom charger', NULL, 8, 1950.00, 1250.00, 700.00, '1', 2, 0.00, NULL, '0'),
(49, 'om310 10w adapter', NULL, 3, 1250.00, 750.00, 500.00, '1', 0, 0.00, NULL, '0'),
(50, '25W samsung pd adapter', NULL, 2, 2250.00, 1250.00, 1000.00, '1', 0, 0.00, NULL, '0'),
(51, 'md812b apple 5W charger', NULL, 4, 1250.00, 650.00, 600.00, '1', 0, 0.00, NULL, '0'),
(52, '20W type c apple adapter 06 month warranty', NULL, 1, 4950.00, 1250.00, 3700.00, '1', 0, 0.00, NULL, '0'),
(53, '20W C apple adapter 1 month warranty', NULL, 4, 2650.00, 1250.00, 1400.00, '1', 1, 0.00, NULL, '0'),
(54, 'xpc25 type c normal charger', NULL, 5, 850.00, 400.00, 450.00, '1', 5, 0.00, NULL, '0'),
(55, 'Xpc25 lightning normal charger', NULL, 4, 850.00, 400.00, 450.00, '1', 3, 0.00, NULL, '0'),
(56, 'XpC 30 lightning charger', NULL, 6, 1950.00, 1250.00, 700.00, '1', 3, 0.00, NULL, '0'),
(57, 'SL60 Samsung fast charger', NULL, 1, 2650.00, 1450.00, 1200.00, '1', 0, 0.00, NULL, '0'),
(58, 'Xpc type c normal charger', NULL, 1, 850.00, 450.00, 400.00, '1', 0, 0.00, NULL, '0'),
(59, 'Xpc25 micro normal charger', NULL, 76, 850.00, 400.00, 450.00, '1', 5, 0.00, NULL, '0'),
(60, 'Om418 type-c chager', NULL, 12, 2650.00, 1400.00, 1250.00, '1', 4, 0.00, NULL, '0'),
(62, 'Om418 Micro chager', NULL, 6, 2650.00, 1400.00, 1250.00, '1', 5, 0.00, NULL, '0'),
(63, 'Om418 Lightning', NULL, 2, 2600.00, 1400.00, 1200.00, '1', 2, 0.00, NULL, '0'),
(64, 'Xpc30 Micro chager', NULL, 7, 2200.00, 1250.00, 950.00, '1', 2, 0.00, NULL, '0'),
(65, 'Om401 TYPE-C Chager', NULL, 2, 2350.00, 1200.00, 1150.00, '1', 2, 0.00, NULL, '0'),
(66, 'Om409 C to C Chager', NULL, 2, 3450.00, 1850.00, 1600.00, '1', 1, 0.00, NULL, '0'),
(67, 'OsW01 10000Mah Powerbank', NULL, 1, 4950.00, 2900.00, 2050.00, '1', 0, 0.00, NULL, '0'),
(68, 'V12 10000Mah Powerbank', NULL, 1, 5650.00, 2950.00, 2700.00, '1', 0, 0.00, NULL, '0'),
(69, 'V22 20000Mah Powerbank', NULL, 1, 7950.00, 4300.00, 3650.00, '1', 0, 0.00, NULL, '0'),
(70, 'OSD24', NULL, 0, 4250.00, 2300.00, 1950.00, '0', 0, 0.00, NULL, '0'),
(71, 'A5 Airpods PRO', NULL, 1, 5650.00, 2800.00, 2850.00, '1', 0, 0.00, NULL, '0'),
(72, 'HE05 NeckBand', NULL, 1, 1850.00, 1200.00, 650.00, '1', 0, 0.00, NULL, '0'),
(73, 'V200 Wirless Headphone', NULL, 1, 2250.00, 1200.00, 1050.00, '1', 0, 0.00, NULL, '0'),
(74, '3D Earphonre ', NULL, 1, 1850.00, 1000.00, 850.00, '1', 0, 0.00, NULL, '0'),
(75, 'JD-808 Wird Headphone ', NULL, 1, 1850.00, 1250.00, 600.00, '1', 0, 0.00, NULL, '0'),
(76, 'AV-92 Vocal Microphone', NULL, 1, 3450.00, 1850.00, 1600.00, '1', 0, 0.00, NULL, '0'),
(77, 'KSC-494', NULL, 1, 750.00, 400.00, 350.00, '1', 0, 0.00, NULL, '0'),
(78, 'XXG-152', NULL, 10, 700.00, 400.00, 300.00, '1', 0, 0.00, NULL, '0'),
(79, 'A201 Type-C Chager', NULL, 1, 2250.00, 1200.00, 1050.00, '1', 0, 0.00, NULL, '0'),
(80, 'P10 SAMDSUNG Chager', NULL, 1, 1650.00, 1200.00, 450.00, '1', 0, 0.00, NULL, '0'),
(81, 'Belkin Iphone case ', NULL, 31, 1450.00, 800.00, 650.00, '1', 0, 0.00, NULL, '0'),
(82, 'Iphone Magsafe Case ', NULL, 17, 1200.00, 480.00, 720.00, '1', 0, 0.00, NULL, '0'),
(83, 'Microphone Stand', NULL, 1, 3850.00, 2500.00, 1350.00, '1', 0, 0.00, NULL, '0'),
(84, 'Type-c Microphone ', NULL, 1, 1450.00, 700.00, 750.00, '1', 0, 0.00, NULL, '0'),
(85, 'Boya Microphone BY-M1', NULL, 1, 2950.00, 1500.00, 1450.00, '1', 0, 0.00, NULL, '0'),
(86, 'Lightning Microphone ', NULL, 1, 1950.00, 1200.00, 750.00, '1', 0, 0.00, NULL, '0'),
(87, 'AKG Normal Handsfree', NULL, 3, 750.00, 450.00, 300.00, '1', 0, 0.00, NULL, '0'),
(88, 'Lightning Normal Cable ', NULL, 21, 450.00, 200.00, 250.00, '1', 0, 0.00, NULL, '0'),
(90, 'Beats Headset', NULL, 2, 850.00, 450.00, 400.00, '1', 0, 0.00, NULL, '0'),
(91, 'Baseus 100W cable', NULL, 3, 1850.00, 790.00, 1060.00, '1', 0, 0.00, NULL, '0'),
(92, 'RC-C053 Lightning to type-c', NULL, 5, 1650.00, 850.00, 800.00, '1', 0, 0.00, NULL, '0'),
(93, 'OM-751 C to C cable', NULL, 4, 1850.00, 700.00, 1150.00, '1', 0, 0.00, NULL, '0'),
(94, 'D20C C to C cable', NULL, 8, 450.00, 200.00, 250.00, '1', 0, 0.00, NULL, '0'),
(95, 'OS-A04 Type-c to Lightning Cable', NULL, 5, 950.00, 475.00, 475.00, '1', 0, 0.00, NULL, '0'),
(96, 'OS-A22 C to C cable', NULL, 6, 950.00, 450.00, 500.00, '1', 0, 0.00, NULL, '0'),
(97, 'K1000 MINI Keyboard', NULL, 2, 1750.00, 950.00, 800.00, '1', 0, 0.00, NULL, '0'),
(98, 'OS-A03 C to C cable', NULL, 1, 1650.00, 850.00, 800.00, '1', 0, 0.00, NULL, '0'),
(99, 'YS-859 Type C to C', NULL, 4, 1950.00, 980.00, 970.00, '1', 0, 0.00, NULL, '0'),
(100, 'YS-859 Ligthning to C cable', NULL, 4, 1850.00, 950.00, 900.00, '1', 0, 0.00, NULL, '0'),
(101, 'OSA28 Lightning to C cabel ', NULL, 1, 1850.00, 980.00, 870.00, '1', 0, 0.00, NULL, '0'),
(102, 'Lightning to C cable Normal', NULL, 3, 950.00, 450.00, 500.00, '1', 0, 0.00, NULL, '0'),
(103, 'Samsung Org Handsfree', NULL, 3, 700.00, 450.00, 250.00, '1', 0, 0.00, NULL, '0'),
(104, 'OM-154 Micro Cable ', NULL, 5, 400.00, 200.00, 200.00, '1', 0, 0.00, NULL, '0'),
(105, 'OM-154 Type-c Cable', NULL, 9, 400.00, 200.00, 200.00, '1', 0, 0.00, NULL, '0'),
(106, 'OM-154 Lightning Cable', NULL, 9, 400.00, 200.00, 200.00, '1', 0, 0.00, NULL, '0'),
(107, 'R162 Earphone', NULL, 19, 450.00, 280.00, 170.00, '1', 0, 0.00, NULL, '0'),
(108, 'SY-S8 Earphone ', NULL, 19, 450.00, 250.00, 200.00, '1', 0, 0.00, NULL, '0'),
(109, 'V13 Earphone ', NULL, 4, 400.00, 200.00, 200.00, '1', 0, 0.00, NULL, '0'),
(110, 'PA113 Headset ', NULL, 3, 1350.00, 650.00, 700.00, '1', 0, 0.00, NULL, '0'),
(111, 'OS-A16', NULL, 4, 1750.00, 980.00, 770.00, '1', 0, 0.00, NULL, '0'),
(112, 'BQZ-159 Headset', NULL, 28, 650.00, 260.00, 390.00, '1', 0, 0.00, NULL, '0'),
(113, 'OS-X02 Headset ', NULL, 3, 1450.00, 650.00, 800.00, '1', 0, 0.00, NULL, '0'),
(114, 'OS-X04 Headset', NULL, 5, 950.00, 450.00, 500.00, '1', 0, 0.00, NULL, '0'),
(115, 'D2 Headset ', NULL, 9, 900.00, 450.00, 450.00, '1', 0, 0.00, NULL, '0'),
(117, 'OS-E02 Lightning Earphone', NULL, 5, 1750.00, 950.00, 800.00, '1', 0, 0.00, NULL, '0'),
(118, 'B-10 Headset', NULL, 3, 1250.00, 750.00, 500.00, '1', 0, 0.00, NULL, '0'),
(119, 'PA200 Headset ', NULL, 6, 1250.00, 750.00, 500.00, '1', 0, 0.00, NULL, '0'),
(120, 'Om-508 Headset ', NULL, 25, 1450.00, 250.00, 1200.00, '1', 0, 0.00, NULL, '0'),
(121, 'OM515 Headset ', NULL, 14, 750.00, 450.00, 300.00, '1', 2, 0.00, NULL, '0'),
(122, 'U23 Headset ', NULL, 8, 450.00, 250.00, 200.00, '1', 2, 0.00, NULL, '0'),
(123, 'NA-18 Hradset', NULL, 5, 450.00, 250.00, 200.00, '1', 2, 0.00, NULL, '0'),
(124, 'NA-20 Headset ', NULL, 4, 450.00, 250.00, 200.00, '1', 2, 0.00, NULL, '0'),
(126, 'NA-21 Headset ', NULL, 1, 450.00, 250.00, 200.00, '1', 0, 0.00, NULL, '0'),
(127, 'M-877 Headset', NULL, 4, 350.00, 280.00, 70.00, '1', 1, 0.00, NULL, '0'),
(128, 'DX06 AUX Cable', NULL, 3, 450.00, 180.00, 270.00, '1', 0, 0.00, NULL, '0'),
(129, 'CA-48 Type-c Cable', NULL, 21, 750.00, 350.00, 400.00, '1', 1, 0.00, NULL, '0'),
(130, 'CA-T126 Micro Cable', NULL, 5, 350.00, 150.00, 200.00, '1', 1, 0.00, NULL, '0'),
(131, 'OM-168 Micro Cable ', NULL, 8, 700.00, 450.00, 250.00, '1', 0, 0.00, NULL, '0'),
(132, 'SM-07 Micro Cable', NULL, 9, 850.00, 400.00, 450.00, '1', 2, 0.00, NULL, '0'),
(133, 'IV-CA53 Micro Cable', NULL, 5, 1150.00, 450.00, 700.00, '1', 1, 0.00, NULL, '0'),
(134, 'D01V Micro Cable', NULL, 1, 450.00, 200.00, 250.00, '1', 0, 0.00, NULL, '0'),
(135, 'OM-150 Micro Cable ', NULL, 1, 650.00, 350.00, 300.00, '1', 0, 0.00, NULL, '0'),
(136, 'GL060 AUX to Lightning Cable ', NULL, 2, 950.00, 450.00, 500.00, '1', 0, 0.00, NULL, '0'),
(137, 'YS-A14 AUX Cable ', NULL, 2, 450.00, 200.00, 250.00, '1', 0, 0.00, NULL, '0'),
(138, 'AMP02667 49mm Watch Strap', NULL, 4, 1150.00, 450.00, 700.00, '1', 0, 0.00, NULL, '0'),
(139, 'V60 Watch Strap', NULL, 1, 950.00, 450.00, 500.00, '1', 0, 0.00, NULL, '0'),
(140, 'C10 Watch Case and Strap', NULL, 2, 1450.00, 700.00, 750.00, '1', 0, 0.00, NULL, '0'),
(141, 'P31 Watch protector', NULL, 8, 700.00, 300.00, 400.00, '1', 1, 0.00, NULL, '0'),
(142, 'SX-39 OTG USB', NULL, 5, 1750.00, 1250.00, 500.00, '1', 0, 0.00, NULL, '0'),
(143, 'JX-151 Lightning Cable ', NULL, 5, 750.00, 500.00, 250.00, '1', 0, 0.00, NULL, '0'),
(144, 'OM-750 Lightning Cable', NULL, 7, 950.00, 450.00, 500.00, '1', 0, 0.00, NULL, '0'),
(145, 'X87 Lightning Cable', NULL, 2, 950.00, 780.00, 170.00, '1', 0, 0.00, NULL, '0'),
(146, 'D01V Ligthning  Cable', NULL, 6, 400.00, 180.00, 220.00, '1', 2, 0.00, NULL, '0'),
(147, 'CB-33  Lightning Cable ', NULL, 5, 750.00, 380.00, 370.00, '1', 1, 0.00, NULL, '0'),
(148, 'OS-A02 Lightning Cable ', NULL, 5, 950.00, 450.00, 500.00, '1', 1, 0.00, NULL, '0'),
(149, 'OM-150 Lightning Cable ', NULL, 6, 700.00, 350.00, 350.00, '1', 1, 0.00, NULL, '0'),
(150, 'K62 Watch Strap ', NULL, 1, 950.00, 450.00, 500.00, '1', 0, 0.00, NULL, '0'),
(151, 'SM-07 Lightninjg Cable ', NULL, 6, 850.00, 480.00, 370.00, '1', 2, 0.00, NULL, '0'),
(152, 'OM-705 Lightning Cable ', NULL, 2, 950.00, 450.00, 500.00, '1', 0, 0.00, NULL, '0'),
(153, 'A1481 Lightning Cable ', NULL, 3, 1250.00, 600.00, 650.00, '1', 1, 0.00, NULL, '0'),
(154, 'A1703 Lightning to Type-C Cable ', NULL, 2, 1650.00, 750.00, 900.00, '1', 0, 0.00, NULL, '0'),
(155, 'CA-46 Type-c Cable ', NULL, 24, 750.00, 350.00, 400.00, '1', 2, 0.00, NULL, '0'),
(156, 'CB07 Type-C Cable', NULL, 26, 350.00, 150.00, 200.00, '1', 3, 0.00, NULL, '0'),
(157, 'YS-858 Type-C Cable ', NULL, 5, 1450.00, 790.00, 660.00, '1', 1, 0.00, NULL, '0'),
(158, 'OS-V02 Type-C Cable ', NULL, 3, 1850.00, 750.00, 1100.00, '1', 0, 0.00, NULL, '0'),
(159, 'A1997 C to C Cable ', NULL, 2, 950.00, 500.00, 450.00, '1', 0, 0.00, NULL, '0'),
(160, 'OS-A24 Type-c ', NULL, 3, 950.00, 450.00, 500.00, '1', 2, 0.00, NULL, '0'),
(161, 'Power Max Cable Type-C', NULL, 17, 700.00, 450.00, 250.00, '1', 2, 0.00, NULL, '0'),
(162, 'SM-07 Type-C Cable ', NULL, 11, 850.00, 400.00, 450.00, '1', 3, 0.00, NULL, '0'),
(163, 'OM-168 Type-c Cable ', NULL, 22, 700.00, 350.00, 350.00, '1', 2, 0.00, NULL, '0'),
(164, 'XXG-153 Type-C Cable ', NULL, 14, 750.00, 400.00, 350.00, '1', 2, 0.00, NULL, '0'),
(165, 'Iphone 6 Backcover', NULL, 18, 700.00, 250.00, 450.00, '1', 2, 0.00, NULL, '0'),
(166, 'Iphone 7/8/se Backcover', NULL, 14, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(167, 'Iphone 6+', NULL, 5, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(168, 'Iphone 12/12 pro', NULL, 7, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(169, 'Iphone12 pro max ', NULL, 4, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(170, 'Iphone 12 Mini ', NULL, 1, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(171, 'Iphone 11 pro max', NULL, 8, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(172, 'Iphone 11', NULL, 6, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(173, 'Iphone 13 pro ', NULL, 6, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(174, 'Iphone 7  ', NULL, 14, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(175, 'Iphone 11 pro ', NULL, 5, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(176, 'Iphone X/Xs', NULL, 15, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(177, 'Iphone Xr', NULL, 4, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(178, 'Iphone Xs Max ', NULL, 5, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(179, 'Iphone 13', NULL, 2, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(180, 'Iphone 14', NULL, 2, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(181, 'IPhone 14 plus', NULL, 1, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(182, 'Iphone 14 pro max', NULL, 1, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(183, 'IPhone 14 pro', NULL, 6, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(184, 'Iphone 15', NULL, 1, 600.00, 250.00, 350.00, '1', 1, 0.00, NULL, '0'),
(185, 'Iphone 15 pro', NULL, 2, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(186, 'Iphone 15 Plus ', NULL, 1, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(187, 'Iphone 15 pro max', NULL, 2, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(188, 'Redmi note 7', NULL, 1, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(189, 'Redmi 8/8a', NULL, 29, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(190, 'Redmi note 8', NULL, 3, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(192, 'Redmi 9A ', NULL, 6, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(193, 'Redmi 9c ', NULL, 7, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(194, 'Redmi 9', NULL, 5, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(195, 'Redmi 10c ', NULL, 3, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(196, 'Redmi note 9 pro', NULL, 2, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(197, 'redmi Note 9 Backcover', NULL, 2, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(198, 'Redmi 9T Backcover', NULL, 2, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(199, 'Redmi note 8 pro Backcover', NULL, 1, 600.00, 250.00, 350.00, '1', 0, 0.00, NULL, '0'),
(200, 'Redmi note 10 4G Backcover', NULL, 4, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(201, 'Redmi note 10 pro 4G Backcover', NULL, 5, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(202, 'Redmi 10 Backcover', NULL, 1, 600.00, 250.00, 350.00, '1', 1, 0.00, NULL, '0'),
(203, 'Redmi note 11 pro 4G Backcover', NULL, 3, 600.00, 250.00, 350.00, '1', 1, 0.00, NULL, '0'),
(204, 'Redmi note 11 4G Backcover', NULL, 4, 600.00, 250.00, 350.00, '1', 1, 0.00, NULL, '0'),
(205, 'Mi 11 Lite Backcover', NULL, 2, 600.00, 250.00, 350.00, '1', 0, 0.00, NULL, '0'),
(206, 'Redmi 12 c Backcover', NULL, 29, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(207, 'Redmi note 13 4G Backcover', NULL, 2, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(208, 'Redmi note 13 pro 4G Backcover', NULL, 3, 600.00, 250.00, 350.00, '1', 1, 0.00, NULL, '0'),
(209, 'Redmi note 12 pro Backcover', NULL, 3, 600.00, 250.00, 350.00, '1', 1, 0.00, NULL, '0'),
(210, 'Redmi 12 Backcover', NULL, 6, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(211, 'Redmi note 12 4G Backcover', NULL, 2, 600.00, 250.00, 350.00, '1', 0, 0.00, NULL, '0'),
(212, 'Redmi 13 C Backcover ', NULL, 7, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(214, 'Redmi note 13 pro plus Backcover', NULL, 2, 600.00, 250.00, 350.00, '1', 0, 0.00, NULL, '0'),
(216, 'MD-C37 AC Adapter', NULL, 4, 1250.00, 650.00, 600.00, '1', 1, 0.00, NULL, '0'),
(217, 'OS-U06 Adapter', NULL, 2, 1250.00, 700.00, 550.00, '1', 0, 0.00, NULL, '0'),
(218, 'H15 Airpods & Smart Watch', NULL, 1, 5850.00, 2800.00, 3050.00, '1', 0, 0.00, NULL, '0'),
(219, 'C & Q -100 Boost Power Code', NULL, 3, 1350.00, 650.00, 700.00, '1', 0, 0.00, NULL, '0'),
(220, 'AKG -21 Type-C Headphone', NULL, 3, 1650.00, 800.00, 850.00, '1', 0, 0.00, NULL, '0'),
(221, 'CB-06 Micro Cable ', NULL, 20, 350.00, 150.00, 200.00, '1', 2, 0.00, NULL, '0'),
(222, 'CA-03 Type-c Cable ', NULL, 20, 700.00, 300.00, 400.00, '1', 2, 0.00, NULL, '0'),
(223, '5CBS Nokia Battery', NULL, 5, 850.00, 350.00, 500.00, '1', 2, 0.00, NULL, '0'),
(224, 'MX-AX15 Lightning to Type-C Adapter', NULL, 2, 1750.00, 850.00, 900.00, '1', 0, 0.00, NULL, '0'),
(225, 'MX-AX17 Lightning to 3.5mm Adapter', NULL, 5, 1450.00, 650.00, 800.00, '1', 1, 0.00, NULL, '0'),
(226, 'DU09 Type-c to 3.5mm Adapter', NULL, 2, 950.00, 400.00, 550.00, '1', 0, 0.00, NULL, '0'),
(227, 'Bluetooth Recever', NULL, 4, 850.00, 400.00, 450.00, '1', 2, 0.00, NULL, '0'),
(228, 'CarG7 Bluetooth Car Chager', NULL, 2, 1250.00, 650.00, 600.00, '1', 0, 0.00, NULL, '0'),
(229, 'Multi Chager', NULL, 40, 450.00, 200.00, 250.00, '1', 5, 0.00, NULL, '0'),
(230, 'pixel 3 back cover', NULL, 2, 750.00, 250.00, 500.00, '1', 1, 0.00, NULL, '0'),
(231, 'pixel 3a back cover', NULL, 2, 750.00, 250.00, 500.00, '1', 1, 0.00, NULL, '0'),
(233, 'pixel 4a 4g back cover', NULL, 2, 700.00, 250.00, 450.00, '1', 1, 0.00, NULL, '0'),
(234, 'pixel 6a back cover', NULL, 2, 750.00, 250.00, 500.00, '1', 1, 0.00, NULL, '0'),
(235, 'pixel 6 back cover', NULL, 2, 750.00, 250.00, 500.00, '1', 1, 0.00, NULL, '0'),
(236, 'pixel 7pro back cover', NULL, 3, 750.00, 250.00, 500.00, '1', 1, 0.00, NULL, '0'),
(237, 'Motorola E13 back cover  ', NULL, 1, 750.00, 250.00, 500.00, '1', 0, 0.00, NULL, '0'),
(238, 'Motorola g22 back cover  ', NULL, 2, 750.00, 250.00, 500.00, '1', 0, 0.00, NULL, '0'),
(239, 'Motorola E30 back cover  ', NULL, 1, 750.00, 250.00, 500.00, '1', 0, 0.00, NULL, '0'),
(240, 'oneplus 9 back cover', NULL, 1, 700.00, 200.00, 500.00, '1', 0, 0.00, NULL, '0'),
(241, 'ZTE A34 Back cover', NULL, 4, 700.00, 250.00, 450.00, '1', 1, 0.00, NULL, '0'),
(242, 'ZTE V50 Back cover', NULL, 2, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(243, 'ZTE A54 Back cover', NULL, 8, 700.00, 250.00, 450.00, '1', 1, 0.00, NULL, '0'),
(244, 'Itel A60 Back cover', NULL, 1, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(245, 'Itel A05S Back cover', NULL, 2, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(246, 'Itel A48 Back cover', NULL, 4, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(248, 'Tecno pop 5 lite back cover', NULL, 7, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(249, 'Tecno spark 8c lite back cover', NULL, 5, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(250, 'Tecno spark 6 go lite back cover', NULL, 4, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(251, 'Tecno spark 10 pro back cover', NULL, 6, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(252, 'Tecno spark go 2023 back cover', NULL, 10, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(253, 'Tecno spark 10c back cover', NULL, 5, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(254, 'Tecno spark Go back cover', NULL, 1, 650.00, 250.00, 400.00, '1', 0, 0.00, NULL, '0'),
(255, 'tecno smart 8 pro back cover', NULL, 1, 650.00, 250.00, 400.00, '1', 0, 0.00, NULL, '0'),
(256, 'Tecno spark 8 back cover', NULL, 4, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(257, 'Tecno spark go 2024 back cover', NULL, 13, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(259, 'Infinix Smart 5 Backcover', NULL, 8, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(260, 'Infinix Hot 11 play  Backcover', NULL, 4, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(261, 'Infinix Hot 10 Backcover', NULL, 5, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(262, 'Infinix Hot 9 Backcover', NULL, 2, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(264, 'Infinix Hot 12 play Backcover', NULL, 3, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(266, 'Infinix Hot 12 Backcover', NULL, 2, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(268, 'Infinix Hot 10 play Backcover', NULL, 1, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(270, 'Infinix Zero 30 Backcover', NULL, 1, 650.00, 250.00, 400.00, '1', 0, 0.00, NULL, '0'),
(271, 'Infinix Hot 9 play Backcover', NULL, 12, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(273, 'Umidigi G3 Back cover', NULL, 2, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(275, 'Umidigi G1 Max Back cover', NULL, 2, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(277, 'Umidigi G5A Back cover', NULL, 2, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(279, 'Umidigi A15C Back cover', NULL, 2, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(281, 'Umidigi G5 Back cover', NULL, 2, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(283, 'Umidigi C1 Back cover', NULL, 1, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(284, 'Redmi A2 plus Back cover', NULL, 5, 600.00, 250.00, 350.00, '1', 0, 0.00, NULL, '0'),
(285, 'Redmi A1 plus Back cover', NULL, 1, 600.00, 250.00, 350.00, '1', 0, 0.00, NULL, '0'),
(286, 'Redmi A1Back cover', NULL, 7, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(287, 'Redmi A1 plus Pouch', NULL, 2, 600.00, 550.00, 50.00, '1', 0, 0.00, NULL, '0'),
(288, 'Redmi A3 back Cover', NULL, 6, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(289, '25W Oms samsum (6 month warranty) ', NULL, 5, 3950.00, 1450.00, 2500.00, '1', 1, 0.00, NULL, '0'),
(290, 'Samsung Note 9 back Cover', NULL, 2, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(291, 'Samsung Note 8 back Cover', NULL, 2, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(292, 'Samsung Note 10 back Cover', NULL, 1, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(293, 'Samsung S3 back Cover', NULL, 8, 400.00, 200.00, 200.00, '1', 0, 0.00, NULL, '0'),
(294, 'Samsung S4 back Cover', NULL, 1, 450.00, 200.00, 250.00, '1', 0, 0.00, NULL, '0'),
(295, 'Samsung S5 back Cover', NULL, 1, 450.00, 200.00, 250.00, '1', 0, 0.00, NULL, '0'),
(296, 'Samsung S6 edge back Cover', NULL, 2, 500.00, 200.00, 300.00, '1', 0, 0.00, NULL, '0'),
(297, 'Samsung S7 back Cover', NULL, 1, 500.00, 200.00, 300.00, '1', 0, 0.00, NULL, '0'),
(298, 'Samsung S8 plus back Cover', NULL, 2, 650.00, 250.00, 400.00, '1', 0, 0.00, NULL, '0'),
(300, 'Samsung S9 back Cover', NULL, 2, 600.00, 250.00, 350.00, '1', 0, 0.00, NULL, '0'),
(301, 'Samsung S10 all back Cover', NULL, 9, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(302, 'Samsung S20 fe back Cover', NULL, 1, 650.00, 250.00, 400.00, '1', 0, 0.00, NULL, '0'),
(304, 'Samsung S22 ultra back Cover', NULL, 2, 750.00, 250.00, 500.00, '1', 0, 0.00, NULL, '0'),
(305, 'Samsung S23 back Cover', NULL, 1, 750.00, 250.00, 500.00, '1', 0, 0.00, NULL, '0'),
(306, 'Samsung S23 ultra back Cover', NULL, 4, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(307, 'Samsung A9 2018 back Cover', NULL, 3, 500.00, 250.00, 250.00, '1', 0, 0.00, NULL, '0'),
(308, 'samsung J1 mini back Cover', NULL, 15, 500.00, 250.00, 250.00, '1', 1, 0.00, NULL, '0'),
(309, 'samsung J1 back Cover', NULL, 6, 500.00, 200.00, 300.00, '1', 1, 0.00, NULL, '0'),
(310, 'Samsung J1 2016', NULL, 4, 600.00, 200.00, 400.00, '1', 1, 0.00, NULL, '0'),
(311, 'samsung J1 ace back Cover', NULL, 3, 600.00, 200.00, 400.00, '1', 0, 0.00, NULL, '0'),
(312, 'Samsung J2 2016 back Cover', NULL, 10, 600.00, 250.00, 350.00, '1', 0, 0.00, NULL, '0'),
(313, 'Samsung j2 pro back Cover', NULL, 3, 600.00, 200.00, 400.00, '1', 0, 0.00, NULL, '0'),
(314, 'J2 2015', NULL, 6, 3.00, 200.00, -197.00, '1', 1, 0.00, NULL, '0'),
(315, 'Samsung J2 prime back Cover', NULL, 6, 600.00, 250.00, 350.00, '1', 1, 0.00, NULL, '0'),
(316, 'Samsung J3 back Cover', NULL, 4, 600.00, 200.00, 400.00, '1', 1, 0.00, NULL, '0'),
(317, 'Samsung J4 back Cover', NULL, 3, 600.00, 200.00, 400.00, '1', 1, 0.00, NULL, '0'),
(318, 'Samsung J4 plus back Cover', NULL, 8, 600.00, 200.00, 400.00, '1', 1, 0.00, NULL, '0'),
(319, 'Samsung J5 back Cover', NULL, 11, 450.00, 150.00, 300.00, '1', 1, 0.00, NULL, '0'),
(320, 'Samsung J5 2016 back Cover', NULL, 3, 500.00, 200.00, 300.00, '1', 0, 0.00, NULL, '0'),
(321, 'Samsung J6 back Cover', NULL, 6, 500.00, 200.00, 300.00, '1', 1, 0.00, NULL, '0'),
(322, 'Samsung J6 Plus back Cover', NULL, 8, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(323, 'Samsung J7 2015 back Cover', NULL, 3, 600.00, 250.00, 350.00, '1', 1, 0.00, NULL, '0'),
(324, 'Samsung J7 2016 back Cover', NULL, 4, 600.00, 250.00, 350.00, '1', 1, 0.00, NULL, '0'),
(325, 'Samsung J7 prime back Cover', NULL, 2, 600.00, 200.00, 400.00, '1', 1, 0.00, NULL, '0'),
(326, 'Samsung J7 Duo back Cover', NULL, 2, 500.00, 200.00, 300.00, '1', 0, 0.00, NULL, '0'),
(328, 'Samsung J8 back Cover', NULL, 1, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(329, 'Poco M3 Back Cover', NULL, 4, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(330, 'Poco X3 Back Cover', NULL, 4, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(331, 'Poco F3 Back Cover', NULL, 3, 600.00, 250.00, 350.00, '1', 1, 0.00, NULL, '0'),
(332, 'Realme C11 2021 Back cover', NULL, 10, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(333, 'Realme C11 Back cover', NULL, 6, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(335, 'Realme C21Y  2021 Back cover', NULL, 5, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(336, 'Realme C55 Back cover', NULL, 3, 650.00, 250.00, 400.00, '1', 0, 0.00, NULL, '0'),
(337, 'Realme C51  Back cover', NULL, 2, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(339, 'Realme C31 Back cover', NULL, 1, 600.00, 250.00, 350.00, '1', 0, 0.00, NULL, '0'),
(341, 'Realme C35 Back cover', NULL, 1, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(343, 'Realme C15  Back cover', NULL, 1, 500.00, 200.00, 300.00, '1', 0, 0.00, NULL, '0'),
(344, 'Honr X8A Back cover', NULL, 4, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(347, 'Honr X8B Back cover', NULL, 3, 650.00, 200.00, 450.00, '1', 1, 0.00, NULL, '0'),
(349, 'Honr X8 / X6 Back cover', NULL, 7, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(350, 'Honr X7 Back cover', NULL, 2, 700.00, 350.00, 350.00, '1', 0, 0.00, NULL, '0'),
(353, 'Honr X7A Back cover', NULL, 2, 600.00, 200.00, 400.00, '1', 0, 0.00, NULL, '0'),
(354, 'Honr X5 + Back cover', NULL, 3, 650.00, 250.00, 400.00, '1', 0, 0.00, NULL, '0'),
(355, 'Samsung M01 Core back Cover', NULL, 6, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(356, 'Samsung A2 Core  back Cover', NULL, 11, 650.00, 250.00, 400.00, '1', 4, 0.00, NULL, '0'),
(357, 'Samsung A10 back Cover', NULL, 31, 650.00, 250.00, 400.00, '1', 8, 0.00, NULL, '0'),
(358, 'Samsung A01M01 back Cover', NULL, 16, 650.00, 250.00, 400.00, '1', 5, 0.00, NULL, '0'),
(360, 'Samsung A10S back Cover', NULL, 16, 650.00, 250.00, 400.00, '1', 5, 0.00, NULL, '0'),
(361, 'Samsung A11 back Cover', NULL, 13, 650.00, 250.00, 400.00, '1', 5, 0.00, NULL, '0'),
(362, 'Samsung A12 back Cover', NULL, 21, 650.00, 250.00, 400.00, '1', 5, 0.00, NULL, '0'),
(364, 'Samsung A13 back Cover', NULL, 11, 650.00, 250.00, 400.00, '1', 5, 0.00, NULL, '0'),
(365, 'Samsung A15 back Cover', NULL, 10, 650.00, 250.00, 400.00, '1', 5, 0.00, NULL, '0'),
(366, 'Samsung A14 back Cover', NULL, 14, 650.00, 250.00, 400.00, '1', 5, 0.00, NULL, '0'),
(368, 'Samsung A20/A30 back Cover', NULL, 1, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(369, 'Samsung M20 back Cover', NULL, 8, 650.00, 250.00, 400.00, '1', 3, 0.00, NULL, '0'),
(370, 'Samsung A20S back Cover', NULL, 2, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(371, 'Samsung M31 back Cover', NULL, 13, 650.00, 250.00, 400.00, '1', 5, 0.00, NULL, '0'),
(372, 'Samsung A32 back Cover', NULL, 2, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(373, 'Samsung M32 back Cover', NULL, 3, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(374, 'Samsung A22 4g back Cover', NULL, 2, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(375, 'Samsung A23 back Cover', NULL, 2, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0');

-- --------------------------------------------------------

--
-- Table structure for table `purchase`
--

CREATE TABLE `purchase` (
  `purchase_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `single_unit_cost` decimal(10,2) NOT NULL,
  `item_qty` int(11) NOT NULL,
  `payment_account` varchar(255) NOT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `purchased_packet_qty` int(11) DEFAULT NULL,
  `quantity_per_packet` int(11) DEFAULT NULL,
  `packet_price` decimal(10,2) DEFAULT NULL,
  `first_payment` decimal(10,2) DEFAULT NULL,
  `balance_payment_date` date DEFAULT NULL,
  `balance_payment` decimal(10,2) DEFAULT NULL,
  `bill_total` decimal(10,2) NOT NULL,
  `bill_payment_type` enum('full','credit','','') NOT NULL DEFAULT 'full',
  `packet_purchase` tinyint(1) NOT NULL,
  `payment_status` enum('paid','due','','') NOT NULL,
  `purchase_date` date NOT NULL DEFAULT current_timestamp(),
  `purchase_time` time NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `repair_categories`
--

CREATE TABLE `repair_categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `repair_categories`
--

INSERT INTO `repair_categories` (`id`, `category_name`, `created_at`) VALUES
(1, 'Display Replacement', '2024-09-30 20:59:32');

-- --------------------------------------------------------

--
-- Table structure for table `repair_items`
--

CREATE TABLE `repair_items` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `repair_name` varchar(255) NOT NULL,
  `commission` decimal(10,2) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `profit` decimal(10,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `repair_items`
--

INSERT INTO `repair_items` (`id`, `category_id`, `repair_name`, `commission`, `cost`, `profit`, `selling_price`, `created_at`) VALUES
(1, 1, 'M20 Display Replacement', 500.00, 100.00, 1400.00, 2000.00, '2024-10-01 18:43:29'),
(2, 1, 'iPhone 12 Display Replace', 5000.00, 10.00, 114990.00, 120000.00, '2024-10-01 18:44:17');

-- --------------------------------------------------------

--
-- Table structure for table `repair_sell_records`
--

CREATE TABLE `repair_sell_records` (
  `id` int(11) NOT NULL,
  `invoice_number` int(10) NOT NULL,
  `repair_name` varchar(100) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `worker_commission` decimal(10,2) DEFAULT 0.00,
  `selling_price` decimal(10,2) NOT NULL,
  `worker` varchar(50) NOT NULL,
  `company_profit` decimal(10,2) GENERATED ALWAYS AS (`selling_price` - (`cost` + `worker_commission`)) STORED,
  `repair_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `repair_sell_records`
--

INSERT INTO `repair_sell_records` (`id`, `invoice_number`, `repair_name`, `cost`, `worker_commission`, `selling_price`, `worker`, `repair_date`) VALUES
(1, 16, 'iPhone 12 Display Replace', 100.00, 4000.00, 120000.00, 'ifix', '2024-10-02 16:53:57'),
(2, 17, 'iPhone 12 Display Replace', 10.00, 5000.00, 120000.00, 'Udaya', '2024-10-02 16:55:50'),
(3, 17, 'iPhone 12 Display Replace', 10.00, 5000.00, 120000.00, 'Kasun', '2024-10-02 16:55:55'),
(4, 19, 'iPhone 12 Display Replace', 10.00, 5000.00, 120000.00, 'Kasun', '2024-10-02 17:03:35'),
(5, 19, 'M20 Display Replacement', 100.00, 500.00, 2000.00, 'Udaya', '2024-10-02 17:03:41');

-- --------------------------------------------------------

--
-- Table structure for table `repair_stock`
--

CREATE TABLE `repair_stock` (
  `id` int(11) NOT NULL,
  `stock_item_name` varchar(255) NOT NULL,
  `stock_qty` int(11) NOT NULL,
  `stock_cost` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `repair_stock`
--

INSERT INTO `repair_stock` (`id`, `stock_item_name`, `stock_qty`, `stock_cost`, `created_at`) VALUES
(1, 'testyy', 15, 10.00, '2024-09-29 19:18:01'),
(3, 't2', 30, 20.00, '2024-09-30 04:00:22'),
(4, 't3', 30, 20.00, '2024-09-30 04:00:50'),
(5, 't4', 30, 30.00, '2024-09-30 04:01:46'),
(6, 'X', 20, 10.00, '2024-09-30 19:33:46');

-- --------------------------------------------------------

--
-- Table structure for table `repair_stock_map`
--

CREATE TABLE `repair_stock_map` (
  `id` int(11) NOT NULL,
  `repair_name` varchar(100) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `qty` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `repair_stock_map`
--

INSERT INTO `repair_stock_map` (`id`, `repair_name`, `item_name`, `qty`) VALUES
(1, 'test', 'testyy', 1),
(2, 'tw', 'testyy', 1),
(3, 'test', 'testyy', 1),
(4, 'test', 'testyy', 1),
(5, 'test', 'testyy', 1),
(6, 'test', 'testyy', 1),
(7, 'M20 Display Replacement', 'testyy', 1),
(8, 'iPhone 12 Display Replace', 'testyy', 1);

-- --------------------------------------------------------

--
-- Table structure for table `salary`
--

CREATE TABLE `salary` (
  `salary_id` int(10) NOT NULL,
  `emp_id` int(4) NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `time` time NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `salary`
--

INSERT INTO `salary` (`salary_id`, `emp_id`, `amount`, `description`, `date`, `time`) VALUES
(1, 1, 11.10, 'Profit from Invoice Number : <a href=\'/invoice/print.php?id=\'>  </a>', '2024-09-09', '05:45:52'),
(2, 1, 45.00, 'Profit from Invoice Number : <a href=\'/invoice/print.php?id=2\'> 2 </a>', '2024-09-09', '06:54:01'),
(3, 1, 90.00, 'Profit (Invoice Edit) from Invoice Number : <a href=\'/invoice/print.php?id=3\'> 3 </a>', '2024-09-10', '08:10:20'),
(4, 1, 200.00, 'Commission Added for Chargin Port Repart in Invoice Number : <a href=\'/invoice/print.php?id=4\'> 4 </a>', '2024-09-10', '08:20:30'),
(5, 1, 100.00, 'Commission from Invoice Number : <a href=\'/invoice/print.php?id=9\'> 9 </a> for Oms 5c battery(3 month warranty)', '2024-09-19', '08:07:26'),
(6, 0, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=10\'> 10 </a> for Oms 5c battery(3 month warranty)', '2024-09-19', '08:15:42'),
(7, 0, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=11\'> 11 </a> for Oms 5c battery(3 month warranty)', '2024-09-19', '08:16:37'),
(8, 0, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=12\'> 12 </a> for Oms 5c battery(3 month warranty)', '2024-09-19', '08:19:59'),
(9, 14, 1000.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=1\'> 1 </a> for Display replacement', '2024-09-19', '09:07:44'),
(10, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=5\'> 5 </a> for oms 4c battery 3 month worrenty ', '2024-09-23', '11:00:44'),
(11, 1, 0.00, 'Commission Added for M02 Backcover in Invoice Number : <a href=\'/invoice/print.php?id=5\'> 5 </a>', '2024-09-27', '12:48:02'),
(12, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=6\'> 6 </a> for Xpc25 micro normal charger', '2024-09-28', '09:58:37'),
(13, 1, 67.50, 'Profit (Balance Pay) from Invoice Number : <a href=\'/invoice/print.php?id=6\'> 6 </a>', '2024-09-28', '09:59:06'),
(14, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=7\'> 7 </a> for OM-168 Micro Cable ', '2024-10-01', '11:35:06'),
(15, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=8\'> 8 </a> for Xpc25 micro normal charger', '2024-10-01', '11:36:08'),
(16, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=9\'> 9 </a> for Xpc25 micro normal charger', '2024-10-01', '11:38:58'),
(17, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=10\'> 10 </a> for Xpc25 micro normal charger', '2024-10-01', '11:40:35'),
(18, 1, -22.50, 'Profit (Invoice Edit) from Invoice Number : <a href=\'/invoice/print.php?id=10\'> 10 </a>', '2024-10-01', '12:56:07'),
(19, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=11\'> 11 </a> for Realme C21Y  2021 Back cover', '2024-10-02', '05:00:51'),
(20, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=12\'> 12 </a> for OSD24', '2024-10-02', '05:05:24'),
(21, 1, 0.00, 'Profit (Invoice Edit) from Invoice Number : <a href=\'/invoice/print.php?id=12\'> 12 </a>', '2024-10-02', '05:47:01'),
(22, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=13\'> 13 </a> for Om418 Micro chager', '2024-10-02', '06:46:27'),
(23, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=14\'> 14 </a> for OM-154 Micro Cable ', '2024-10-02', '09:17:46'),
(24, 0, 4000.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=16\'> 16 </a> for iPhone 12 Display Replace', '2024-10-02', '16:35:00'),
(25, 0, 5000.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=17\'> 17 </a> for iPhone 12 Display Replace', '2024-10-02', '16:55:48'),
(26, 0, 5000.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=17\'> 17 </a> for iPhone 12 Display Replace', '2024-10-02', '16:55:53'),
(27, 15, 5000.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=19\'> 19 </a> for iPhone 12 Display Replace', '2024-10-02', '17:03:33'),
(28, 14, 500.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=19\'> 19 </a> for M20 Display Replacement', '2024-10-02', '17:03:39');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sales_id` int(5) NOT NULL,
  `invoice_number` int(10) NOT NULL,
  `product` varchar(100) NOT NULL,
  `description` varchar(50) DEFAULT NULL,
  `qty` varchar(8) NOT NULL,
  `rate` decimal(8,2) NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `cost` double(10,2) NOT NULL,
  `profit` double(10,2) NOT NULL,
  `worker` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sales_id`, `invoice_number`, `product`, `description`, `qty`, `rate`, `amount`, `cost`, `profit`, `worker`) VALUES
(1, 1, 'Display replacement', NULL, '1', 5500.00, 5500.00, 3500.00, 2000.00, 'Udaya'),
(2, 5, 'oms 4c battery 3 month worrenty ', NULL, '1', 800.00, 800.00, 350.00, 450.00, 'lakmal'),
(3, 6, 'Xpc25 micro normal charger', NULL, '1', 850.00, 850.00, 400.00, 450.00, 'lakmal'),
(4, 7, 'OM-168 Micro Cable ', NULL, '1', 700.00, 700.00, 450.00, 250.00, 'lakmal'),
(5, 8, 'Xpc25 micro normal charger', NULL, '1', 800.00, 800.00, 400.00, 400.00, 'lakmal'),
(6, 9, 'Xpc25 micro normal charger', NULL, '1', 850.00, 850.00, 400.00, 450.00, 'lakmal'),
(7, 10, 'Xpc25 micro normal charger', NULL, '1', 700.00, 700.00, 400.00, 450.00, 'lakmal'),
(8, 11, 'Realme C21Y  2021 Back cover', NULL, '1', 650.00, 650.00, 250.00, 400.00, 'lakmal'),
(9, 12, 'OSD24', NULL, '1', 4250.00, 4250.00, 2300.00, 1950.00, 'lakmal'),
(10, 13, 'Om418 Micro chager', NULL, '1', 2400.00, 2400.00, 1400.00, 1000.00, 'lakmal'),
(11, 14, 'OM-154 Micro Cable ', NULL, '1', 400.00, 400.00, 200.00, 200.00, 'lakmal'),
(12, 16, 'iPhone 12 Display Replace', NULL, '1', 120000.00, 120000.00, 100.00, 115900.00, 'ifix'),
(13, 17, 'iPhone 12 Display Replace', NULL, '1', 120000.00, 120000.00, 10.00, 114990.00, 'Udaya'),
(14, 17, 'iPhone 12 Display Replace', NULL, '1', 120000.00, 120000.00, 10.00, 114990.00, 'Kasun'),
(15, 19, 'iPhone 12 Display Replace', NULL, '1', 120000.00, 120000.00, 10.00, 114990.00, 'Kasun'),
(16, 19, 'M20 Display Replacement', NULL, '1', 2000.00, 2000.00, 100.00, 1400.00, 'Udaya');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(4) NOT NULL,
  `supplier_name` varchar(60) NOT NULL,
  `supplier_tel` int(11) NOT NULL DEFAULT 0,
  `credit_balance` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `todo`
--

CREATE TABLE `todo` (
  `todo_id` int(10) NOT NULL,
  `invoice_number` int(10) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `submision_time` datetime NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `todo`
--

INSERT INTO `todo` (`todo_id`, `invoice_number`, `title`, `submision_time`, `status`) VALUES
(1, 1, 'repair', '2024-09-11 23:48:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_log`
--

CREATE TABLE `transaction_log` (
  `transaction_id` int(10) NOT NULL,
  `transaction_type` varchar(80) NOT NULL,
  `description` text NOT NULL,
  `amount` int(10) NOT NULL,
  `transaction_date` date NOT NULL DEFAULT current_timestamp(),
  `transaction_time` time NOT NULL DEFAULT current_timestamp(),
  `employ_id` int(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `transaction_log`
--

INSERT INTO `transaction_log` (`transaction_id`, `transaction_type`, `description`, `amount`, `transaction_date`, `transaction_time`, `employ_id`) VALUES
(1, 'Invoice - Cash In', ' - ohh, Payment Method : Cash, Advance : Rs. 0', 0, '2024-09-09', '05:35:51', 1),
(2, 'Invoice - Company Profit', '85% Profit to Company. Inv:  - ohh, Profit : Rs. 62.9', 63, '2024-09-09', '05:45:52', 1),
(3, 'Invoice - Cash In', ' - ohh, Payment Method : Cash, Advance : Rs. 74', 74, '2024-09-09', '05:45:52', 1),
(4, 'Invoice - Cash In', '1 - gftjfu, Payment Method : Cash, Advance : Rs. 3770', 3770, '2024-09-09', '06:48:45', 1),
(5, 'Raw Item Purchase', 'charger', -2500, '2024-09-09', '06:51:34', 1),
(6, 'Invoice - Company Profit', '85% Profit to Company. Inv: 2 - Cash, Profit : Rs. 255', 255, '2024-09-09', '06:54:01', 1),
(7, 'Invoice - Cash In', '2 - Cash, Payment Method : Cash, Advance : Rs. 800', 800, '2024-09-09', '06:54:01', 1),
(8, 'Raw Item Purchase', 'TEST ITEM', -69, '2024-09-10', '07:55:41', 1),
(9, 'Raw Item Purchase', 'Xpert XPC25 Charger', -9000, '2024-09-10', '07:59:40', 1),
(10, 'Raw Item Purchase', 'Oms Micro Charger', -6000, '2024-09-10', '08:01:29', 1),
(11, 'Invoice - Company Profit', 'Profit to Company. Inv: 3 - Cash, Profit : Rs. 100', 100, '2024-09-10', '08:06:23', 1),
(12, 'Invoice - Cash In', '3 - Cash, Payment Method : Cash, Advance : Rs. 1000', 1000, '2024-09-10', '08:06:23', 1),
(13, 'Raw Item Purchase', 'asdsads', -21150, '2024-09-10', '08:11:19', 1),
(14, 'Raw Item Purchase', 'sfdsdfs', -274720, '2024-09-10', '08:11:29', 1),
(15, 'Raw Item Purchase', 'dsfsdfs', -300, '2024-09-10', '08:11:39', 1),
(16, 'Raw Item Purchase', 'dfgfdg', -50000, '2024-09-10', '08:12:02', 1),
(17, 'Raw Item Purchase', 'Chargin Port', -1000, '2024-09-10', '08:16:33', 1),
(18, 'Invoice - Company Profit', 'Profit to Company. Inv: 4 - Cash, Profit : Rs. 500', 500, '2024-09-10', '08:17:40', 1),
(19, 'Invoice - Cash In', '4 - Cash, Payment Method : Cash, Advance : Rs. 500', 500, '2024-09-10', '08:17:40', 1),
(20, 'Fall OneTimeProduct Cost from Company Profit', 'Fall Rs.200 in Company Profit Account for Repair/Service ID : 2', -200, '2024-09-10', '08:20:30', 1),
(21, 'Add Salary Commission', 'Increase Salary of lakmal for Chargin Port Repart by Rs.200', 200, '2024-09-10', '08:20:30', 1),
(22, 'Invoice - Company Profit', 'Profit to Company. Inv: 5 - Cash, Profit : Rs. 600', 600, '2024-09-10', '08:28:05', 1),
(23, 'Invoice - Cash In', '5 - Cash, Payment Method : Cash, Advance : Rs. 600', 600, '2024-09-10', '08:28:05', 1),
(24, 'Raw Item Purchase', 'Backcover', -300000, '2024-09-10', '08:30:43', 1),
(25, 'Invoice - Company Profit', 'Profit to Company. Inv: 6 - Cash, Profit : Rs. 450', 450, '2024-09-10', '08:31:44', 1),
(26, 'Invoice - Cash In', '6 - Cash, Payment Method : Cash, Advance : Rs. 750', 750, '2024-09-10', '08:31:44', 1),
(27, 'Raw Item Purchase', 'oms 5C batery ', -10150, '2024-09-11', '10:43:16', 1),
(28, 'Raw Item Purchase', 'oms 4c', -1400, '2024-09-11', '10:46:50', 1),
(29, 'Raw Item Purchase', 'Charging Pin ', -4500, '2024-09-11', '11:04:37', 1),
(30, 'Invoice - Company Profit', 'Profit to Company. Inv: 7 - Cash, Profit : Rs. 350', 350, '2024-09-11', '11:06:51', 1),
(31, 'Invoice - Cash In', '7 - Cash, Payment Method : Cash, Advance : Rs. 500', 500, '2024-09-11', '11:06:51', 1),
(32, 'Invoice - Company Profit', 'Profit to Company. Inv: 8 - Cash, Profit : Rs. 500', 500, '2024-09-11', '11:08:45', 1),
(33, 'Invoice - Cash In', '8 - Cash, Payment Method : Cash, Advance : Rs. 500', 500, '2024-09-11', '11:08:45', 1),
(34, 'Invoice - Company Profit', 'Profit to Company. Inv: 9 - Cash, Profit : Rs. 540', 540, '2024-09-19', '08:07:27', 0),
(35, 'Invoice - Cash In', '9 - Cash, Payment Method : Cash, Advance : Rs. 800', 800, '2024-09-19', '08:07:27', 0),
(36, 'Invoice - Company Profit', 'Profit to Company. Inv: 10 - Cash, Profit : Rs. 540', 540, '2024-09-19', '08:15:42', 0),
(37, 'Invoice - Cash In', '10 - Cash, Payment Method : Cash, Advance : Rs. 800', 800, '2024-09-19', '08:15:43', 0),
(38, 'Invoice - Company Profit', 'Profit to Company. Inv: 11 - Cash, Profit : Rs. 540', 540, '2024-09-19', '08:16:38', 0),
(39, 'Invoice - Cash In', '11 - Cash, Payment Method : Cash, Advance : Rs. 800', 800, '2024-09-19', '08:16:38', 0),
(40, 'Invoice - Company Profit', 'Profit to Company. Inv: 12 - Cash, Profit : Rs. 540', 540, '2024-09-19', '08:20:00', 0),
(41, 'Invoice - Cash In', '12 - Cash, Payment Method : Cash, Advance : Rs. 800', 800, '2024-09-19', '08:20:00', 0),
(42, 'Invoice - Company Profit', 'Profit to Company. Inv: 1 - Cash, Profit : Rs. 2000', 2000, '2024-09-19', '09:07:44', 1),
(43, 'Invoice - Cash In', '1 - Cash, Payment Method : Cash, Advance : Rs. 5500', 5500, '2024-09-19', '09:07:44', 1),
(44, 'Invoice - Company Profit', 'Profit to Company. Inv: 2 - Cash, Profit : Rs. 0', 0, '2024-09-19', '10:53:54', 0),
(45, 'Invoice - Cash In', '2 - Cash, Payment Method : Cash, Advance : Rs. 0', 0, '2024-09-19', '10:53:54', 0),
(46, 'Invoice - Company Profit', 'Profit to Company. Inv: 3 - Cash, Profit : Rs. 0', 0, '2024-09-19', '10:53:55', 0),
(47, 'Invoice - Cash In', '3 - Cash, Payment Method : Cash, Advance : Rs. 0', 0, '2024-09-19', '10:53:55', 0),
(48, 'Invoice - Company Profit', 'Profit to Company. Inv: 4 - Cash, Profit : Rs. 0', 0, '2024-09-19', '10:53:56', 0),
(49, 'Invoice - Cash In', '4 - Cash, Payment Method : Cash, Advance : Rs. 0', 0, '2024-09-19', '10:53:56', 0),
(50, 'Invoice - Company Profit', 'Profit to Company. Inv: 5 - Cash, Profit : Rs. 450', 450, '2024-09-23', '11:00:44', 1),
(51, 'Invoice - Cash In', '5 - Cash, Payment Method : Cash, Advance : Rs. 800', 800, '2024-09-23', '11:00:44', 1),
(52, 'Fall OneTimeProduct Cost from Company Profit', 'Fall Rs.0 in Company Profit Account for Repair/Service ID : 3', 0, '2024-09-27', '12:48:02', 1),
(53, 'Add Salary Commission', 'Increase Salary of lakmal for M02 Backcover by Rs.0', 0, '2024-09-27', '12:48:02', 1),
(54, 'Invoice - Company Profit', 'Profit to Company. Inv: 6 - Cash, Profit : Rs. -400', -400, '2024-09-28', '09:58:37', 1),
(55, 'Invoice - Cash In', '6 - Cash, Payment Method : Cash, Advance : Rs. 0', 0, '2024-09-28', '09:58:37', 1),
(59, 'Add Invoice Balance Payment', 'Add Fund to Invoice Number : 6 ', 850, '2024-09-28', '10:42:55', 0),
(60, 'Raw Item Purchase', 'dfg', -11730, '2024-09-29', '18:26:20', 0),
(61, 'Repair Stock Item Purchase', 'test', -200, '2024-09-29', '19:18:03', 0),
(62, 'Repair Stock Item Purchase', 'test2', -600, '2024-09-29', '19:23:11', 0),
(63, 'Repair Stock Item Purchase', 't2', -600, '2024-09-30', '04:00:24', 0),
(64, 'Repair Stock Item Purchase', 't3', -600, '2024-09-30', '04:00:52', 0),
(65, 'Repair Stock Item Purchase', 't4', -600, '2024-09-30', '04:01:47', 0),
(66, 'Repair Stock Item Purchase', 'X', -200, '2024-09-30', '19:33:47', 0),
(67, 'Invoice - Company Profit', 'Profit to Company. Inv: 7 - Cash, Profit : Rs. 250', 250, '2024-10-01', '11:35:06', 1),
(68, 'Invoice - Cash In', '7 - Cash, Payment Method : Cash, Advance : Rs. 700', 700, '2024-10-01', '11:35:06', 1),
(69, 'Invoice - Company Profit', 'Profit to Company. Inv: 8 - Cash, Profit : Rs. 400', 400, '2024-10-01', '11:36:08', 1),
(70, 'Invoice - Cash In', '8 - Cash, Payment Method : Cash, Advance : Rs. 800', 800, '2024-10-01', '11:36:08', 1),
(71, 'Invoice - Company Profit', 'Profit to Company. Inv: 9 - Cash, Profit : Rs. 450', 450, '2024-10-01', '11:38:58', 1),
(72, 'Invoice - Cash In', '9 - Cash, Payment Method : Cash, Advance : Rs. 850', 850, '2024-10-01', '11:38:58', 1),
(73, 'Invoice - Company Profit', 'Profit to Company. Inv: 10 - Cash, Profit : Rs. 450', 450, '2024-10-01', '11:40:35', 1),
(74, 'Invoice - Cash In', '10 - Cash, Payment Method : Cash, Advance : Rs. 850', 850, '2024-10-01', '11:40:35', 1),
(75, 'Invoice - Company Profit', 'Profit to Company. Inv: 11 - Cash, Profit : Rs. 400', 400, '2024-10-02', '05:00:51', 1),
(76, 'Invoice - Cash In', '11 - Cash, Payment Method : Cash, Advance : Rs. 650', 650, '2024-10-02', '05:00:51', 1),
(77, 'Invoice - Company Profit', 'Profit to Company. Inv: 12 - Cash, Profit : Rs. 1900', 1900, '2024-10-02', '05:05:24', 1),
(78, 'Invoice - Cash In', '12 - Cash, Payment Method : Cash, Advance : Rs. 4200', 4200, '2024-10-02', '05:05:24', 1),
(79, 'Invoice - Company Profit', 'Profit to Company. Inv: 13 - Cash, Profit : Rs. 1000', 1000, '2024-10-02', '06:46:27', 1),
(80, 'Invoice - Cash In', '13 - Cash, Payment Method : Cash, Advance : Rs. 2400', 2400, '2024-10-02', '06:46:27', 1),
(81, 'Invoice - Company Profit', 'Profit to Company. Inv: 14 - Cash, Profit : Rs. 200', 200, '2024-10-02', '09:17:46', 1),
(82, 'Invoice - Cash In', '14 - Cash, Payment Method : Cash, Advance : Rs. 400', 400, '2024-10-02', '09:17:46', 1),
(83, 'Invoice - Company Profit', 'Profit to Company. Inv: 15 - Cash, Profit : Rs. 3500', 3500, '2024-10-02', '11:06:37', 1),
(84, 'Invoice - Cash In', '15 - Cash, Payment Method : Cash, Advance : Rs. 3500', 3500, '2024-10-02', '11:06:37', 1),
(85, 'Invoice - Company Profit', 'Profit to Company. Inv: 16 - Cash, Profit : Rs. 115900', 115900, '2024-10-02', '16:35:08', 0),
(86, 'Invoice - Cash In', '16 - Cash, Payment Method : Cash, Advance : Rs. 120000', 120000, '2024-10-02', '16:35:11', 0),
(87, 'Invoice - Company Profit', 'Profit to Company. Inv: 17 - Cash, Profit : Rs. 229980', 229980, '2024-10-02', '16:55:58', 0),
(88, 'Invoice - Cash In', '17 - Cash, Payment Method : Cash, Advance : Rs. 240000', 240000, '2024-10-02', '16:56:01', 0),
(89, 'Invoice - Company Profit', 'Profit to Company. Inv: 18 - Cash, Profit : Rs. 122000', 122000, '2024-10-02', '17:00:33', 0),
(90, 'Invoice - Cash In', '18 - Cash, Payment Method : Cash, Advance : Rs. 122000', 122000, '2024-10-02', '17:00:36', 0),
(91, 'Invoice - Company Profit', 'Profit to Company. Inv: 19 - Cash, Profit : Rs. 116390', 116390, '2024-10-02', '17:03:44', 0),
(92, 'Invoice - Cash In', '19 - Cash, Payment Method : Cash, Advance : Rs. 122000', 122000, '2024-10-02', '17:03:47', 0),
(93, 'Invoice - Company Profit', 'Profit to Company. Inv: 20 - Cash, Profit : Rs. 120000', 120000, '2024-10-02', '22:44:49', 0),
(94, 'Invoice - Cash In', '20 - Cash, Payment Method : Cash, Advance : Rs. 120000', 120000, '2024-10-02', '22:44:51', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `action_log`
--
ALTER TABLE `action_log`
  ADD PRIMARY KEY (`action_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `bank_deposits`
--
ALTER TABLE `bank_deposits`
  ADD PRIMARY KEY (`bank_deposit_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employ_id`),
  ADD UNIQUE KEY `emp_name` (`emp_name`);

--
-- Indexes for table `error_log`
--
ALTER TABLE `error_log`
  ADD PRIMARY KEY (`error_id`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoice_number`);

--
-- Indexes for table `InvoiceBalPayRecords`
--
ALTER TABLE `InvoiceBalPayRecords`
  ADD PRIMARY KEY (`InvBalPayRecords_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `item_name` (`item_name`) USING BTREE;

--
-- Indexes for table `makeProduct`
--
ALTER TABLE `makeProduct`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product` (`product_name`) USING BTREE,
  ADD KEY `item` (`item_name`);

--
-- Indexes for table `oneTimeProducts_sales`
--
ALTER TABLE `oneTimeProducts_sales`
  ADD PRIMARY KEY (`oneTimeProduct_id`),
  ADD KEY `invoice number` (`invoice_number`),
  ADD KEY `worker` (`worker`);

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
  ADD UNIQUE KEY `product_name` (`product_name`) USING BTREE;

--
-- Indexes for table `purchase`
--
ALTER TABLE `purchase`
  ADD PRIMARY KEY (`purchase_id`),
  ADD KEY `supplier` (`supplier`);

--
-- Indexes for table `repair_categories`
--
ALTER TABLE `repair_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `repair_items`
--
ALTER TABLE `repair_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_name` (`repair_name`) USING BTREE,
  ADD KEY `repair_category_relation` (`category_id`);

--
-- Indexes for table `repair_sell_records`
--
ALTER TABLE `repair_sell_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`repair_name`),
  ADD KEY `worker` (`worker`),
  ADD KEY `invoice_number` (`invoice_number`);

--
-- Indexes for table `repair_stock`
--
ALTER TABLE `repair_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_item_name` (`stock_item_name`);

--
-- Indexes for table `repair_stock_map`
--
ALTER TABLE `repair_stock_map`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_name` (`item_name`),
  ADD KEY `repair_name` (`repair_name`);

--
-- Indexes for table `salary`
--
ALTER TABLE `salary`
  ADD PRIMARY KEY (`salary_id`),
  ADD KEY `emp_id` (`emp_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`sales_id`),
  ADD KEY `invoice_number` (`invoice_number`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`),
  ADD UNIQUE KEY `unique supplier name` (`supplier_name`);

--
-- Indexes for table `todo`
--
ALTER TABLE `todo`
  ADD PRIMARY KEY (`todo_id`);

--
-- Indexes for table `transaction_log`
--
ALTER TABLE `transaction_log`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `employ_id` (`employ_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `action_log`
--
ALTER TABLE `action_log`
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bank_deposits`
--
ALTER TABLE `bank_deposits`
  MODIFY `bank_deposit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employ_id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `error_log`
--
ALTER TABLE `error_log`
  MODIFY `error_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_number` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `InvoiceBalPayRecords`
--
ALTER TABLE `InvoiceBalPayRecords`
  MODIFY `InvBalPayRecords_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `makeProduct`
--
ALTER TABLE `makeProduct`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `oneTimeProducts_sales`
--
ALTER TABLE `oneTimeProducts_sales`
  MODIFY `oneTimeProduct_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pettycash`
--
ALTER TABLE `pettycash`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=376;

--
-- AUTO_INCREMENT for table `purchase`
--
ALTER TABLE `purchase`
  MODIFY `purchase_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `repair_categories`
--
ALTER TABLE `repair_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `repair_items`
--
ALTER TABLE `repair_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `repair_sell_records`
--
ALTER TABLE `repair_sell_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `repair_stock`
--
ALTER TABLE `repair_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `repair_stock_map`
--
ALTER TABLE `repair_stock_map`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `salary`
--
ALTER TABLE `salary`
  MODIFY `salary_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sales_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `todo`
--
ALTER TABLE `todo`
  MODIFY `todo_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transaction_log`
--
ALTER TABLE `transaction_log`
  MODIFY `transaction_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employ_id`);

--
-- Constraints for table `bank_deposits`
--
ALTER TABLE `bank_deposits`
  ADD CONSTRAINT `bank_deposits_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employ_id`);

--
-- Constraints for table `makeProduct`
--
ALTER TABLE `makeProduct`
  ADD CONSTRAINT `item` FOREIGN KEY (`item_name`) REFERENCES `items` (`item_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `makeProduct_ibfk_1` FOREIGN KEY (`product_name`) REFERENCES `products` (`product_name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `oneTimeProducts_sales`
--
ALTER TABLE `oneTimeProducts_sales`
  ADD CONSTRAINT `invoice number` FOREIGN KEY (`invoice_number`) REFERENCES `invoice` (`invoice_number`) ON UPDATE CASCADE,
  ADD CONSTRAINT `oneTimeProducts_sales_ibfk_1` FOREIGN KEY (`worker`) REFERENCES `employees` (`emp_name`) ON UPDATE CASCADE;

--
-- Constraints for table `purchase`
--
ALTER TABLE `purchase`
  ADD CONSTRAINT `purchase_ibfk_1` FOREIGN KEY (`supplier`) REFERENCES `suppliers` (`supplier_name`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `repair_items`
--
ALTER TABLE `repair_items`
  ADD CONSTRAINT `repair_category_relation` FOREIGN KEY (`category_id`) REFERENCES `repair_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `repair_sell_records`
--
ALTER TABLE `repair_sell_records`
  ADD CONSTRAINT `repair_sell_records_ibfk_3` FOREIGN KEY (`worker`) REFERENCES `employees` (`emp_name`),
  ADD CONSTRAINT `repair_sell_records_ibfk_4` FOREIGN KEY (`invoice_number`) REFERENCES `invoice` (`invoice_number`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `repair_stock_map`
--
ALTER TABLE `repair_stock_map`
  ADD CONSTRAINT `repair_stock_map_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `repair_stock` (`stock_item_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `repair_stock_map_ibfk_2` FOREIGN KEY (`repair_name`) REFERENCES `repair_items` (`repair_name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `salary`
--
ALTER TABLE `salary`
  ADD CONSTRAINT `salary_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`employ_id`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`invoice_number`) REFERENCES `invoice` (`invoice_number`),
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`invoice_number`) REFERENCES `invoice` (`invoice_number`);

--
-- Constraints for table `transaction_log`
--
ALTER TABLE `transaction_log`
  ADD CONSTRAINT `transaction_log_ibfk_1` FOREIGN KEY (`employ_id`) REFERENCES `employees` (`employ_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

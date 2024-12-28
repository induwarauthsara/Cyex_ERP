-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 28, 2024 at 05:46 PM
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
-- Database: `globalMartERP`
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
(1, 'Company Profit', 'cash', 0.00),
(2, 'Utility Bills', 'cash', 0.00),
(3, 'cash_in_hand', 'cash', 745.00),
(4, 'card_payment', 'bank', 30.00),
(20, 'online_transaction', 'bank', 50.00);

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

--
-- Dumping data for table `action_log`
--

INSERT INTO `action_log` (`action_id`, `employee_id`, `action`, `description`, `date`, `time`) VALUES
(1148, 16, 'New Employee Added', 'Name: Ashini', '2024-11-05', '09:34:08'),
(1149, 0, 'Add Category', 'New Category: 1', '2024-12-23', '07:28:57'),
(1150, 0, 'Add Category', 'New Category: gfghfh', '2024-12-23', '07:29:02'),
(1151, 0, 'Add Brand', 'New Brand: gfdgd', '2024-12-23', '07:29:07'),
(1152, 0, 'Add Brand', 'New Brand: asd', '2024-12-23', '07:29:25'),
(1153, 0, 'Add Brand', 'New Brand: 123', '2024-12-23', '07:32:05'),
(1154, 0, 'Add Brand', 'New Brand: new1', '2024-12-23', '07:33:42'),
(1155, 0, 'Add Category', 'New Category: newCt', '2024-12-23', '07:34:06');

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
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL,
  `brand_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`brand_id`, `brand_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Nestlé', 'A multinational food and beverage company.', '2024-10-27 18:41:38', '2024-10-27 18:41:38'),
(2, 'PepsiCo', 'A leading global food and beverage company.', '2024-10-27 18:41:38', '2024-10-27 18:41:38'),
(3, 'Coca-Cola', 'Famous for its soft drinks and beverages.', '2024-10-27 18:41:38', '2024-10-27 18:41:38'),
(4, 'gfdgd', NULL, '2024-12-23 01:59:07', '2024-12-23 01:59:07'),
(5, 'asd', NULL, '2024-12-23 01:59:25', '2024-12-23 01:59:25'),
(6, '123', NULL, '2024-12-23 02:02:05', '2024-12-23 02:02:05'),
(7, 'new1', NULL, '2024-12-23 02:03:42', '2024-12-23 02:03:42');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Dairy', 'Products derived from milk.', '2024-10-27 18:41:34', '2024-10-27 18:41:34'),
(2, 'Snacks', 'Packaged snacks and convenience foods.', '2024-10-27 18:41:34', '2024-10-27 18:41:34'),
(3, 'Beverages', 'Drinks including soft drinks, juices, and water.', '2024-10-27 18:41:34', '2024-10-27 18:41:34'),
(4, '1', NULL, '2024-12-23 01:58:56', '2024-12-23 01:58:56'),
(5, 'gfghfh', NULL, '2024-12-23 01:59:02', '2024-12-23 01:59:02'),
(6, 'newCt', NULL, '2024-12-23 02:04:06', '2024-12-23 02:04:06');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(3) NOT NULL,
  `customer_name` varchar(40) NOT NULL,
  `customer_type` varchar(10) DEFAULT 'regular',
  `customer_mobile` char(10) NOT NULL DEFAULT '0',
  `customer_extra_fund` decimal(9,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `customer_name`, `customer_type`, `customer_mobile`, `customer_extra_fund`) VALUES
(5, 'John Doe', 'individual', '0714730996', 0.00),
(6, 'Jane Smith', 'individual', '0789123456', 0.00),
(7, 'ABC Corp', 'business', '0112345678', 0.00),
(8, 'XYZ Ltd', 'business', '0776543210', 0.00),
(9, 'Sara Lee', 'individual', '0723456789', 0.00),
(11, 'sada', NULL, '0786607354', 0.00),
(12, 'ggg', NULL, '0714568948', 0.00),
(13, 'test1', NULL, '0123456789', 0.00),
(14, 'DD', NULL, '0728035904', 0.00),
(15, 'AS', NULL, '0714745444', 0.00),
(16, 'tesr456', NULL, '4564564564', 0.00),
(28, 'Walk-in Customer', 'regular', '0', 0.00),
(29, 'new customer', NULL, '1234567890', 0.00);

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
(0, 'Global Mart', 0, '0', '0', 'Admin', '0', 0.00, 0.00, '0', 1, NULL, 0, '2024-10-27'),
(17, 'Ashini', 761924250, '0', '0', 'Admin', '0', 0.00, 0.00, '123', 1, NULL, 0, '2024-11-05');

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

--
-- Dumping data for table `error_log`
--

INSERT INTO `error_log` (`error_id`, `error_code`, `error_message`, `query`, `action`, `action_description`, `date`, `time`, `employee_id`, `status`) VALUES
(1, 500, 'Field \'biller\' doesn\'t have a default value', 'INSERT INTO invoice (customer_name, customer_mobile, total, discount, balance, paymentMethod, full_paid, invoice_type, invoice_description)\r\n              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', 'invoice_submission', 'Error during invoice submission', '2024-11-09', '02:01:32', 16, 'pending'),
(2, 500, 'Field \'advance\' doesn\'t have a default value', 'INSERT INTO invoice (customer_name, customer_mobile, total, discount, balance, paymentMethod, full_paid, invoice_description, biller)\r\n          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', 'invoice_submission', 'Error during invoice submission', '2024-11-09', '02:10:14', 16, 'pending'),
(3, 500, 'Column \'amount\' cannot be null', 'INSERT INTO sales (invoice_number, product, qty, rate, amount, cost, profit, worker)\r\n                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)', 'invoice_submission', 'Error during invoice submission', '2024-11-09', '02:14:48', 16, 'pending'),
(4, 500, 'Unknown column \'stock_dqty\' in \'field list\'', 'UPDATE products SET stock_dqty = stock_qty - ? WHERE product_name = ?', 'invoice_submission', 'Error during invoice submission', '2024-11-11', '18:15:27', 0, 'pending'),
(5, 500, 'Column \'rate\' cannot be null', 'INSERT INTO sales (invoice_number, product, qty, rate, amount, cost, profit, worker)\r\n                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)', 'invoice_submission', 'Error during invoice submission', '2024-12-27', '00:52:28', 0, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `held_invoices`
--

CREATE TABLE `held_invoices` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_number` varchar(20) DEFAULT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`items`)),
  `total_amount` decimal(10,2) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `discount_type` enum('flat','percentage') DEFAULT 'percentage',
  `total_payable` decimal(10,2) DEFAULT NULL,
  `status` enum('held','completed') DEFAULT 'held',
  `held_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `discount_value` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoice_number` int(10) NOT NULL,
  `invoice_description` text DEFAULT NULL,
  `customer_name` varchar(80) NOT NULL,
  `invoice_date` date NOT NULL DEFAULT current_timestamp(),
  `time` time NOT NULL DEFAULT current_timestamp(),
  `customer_mobile` int(10) NOT NULL,
  `biller` varchar(20) NOT NULL,
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

INSERT INTO `invoice` (`invoice_number`, `invoice_description`, `customer_name`, `invoice_date`, `time`, `customer_mobile`, `biller`, `total`, `discount`, `advance`, `balance`, `cost`, `profit`, `full_paid`, `paymentMethod`) VALUES
(78, 'Standard Purchase', 'Walk-in Customer', '2024-11-09', '02:17:18', 0, '0', 2000.00, 0.00, 18000.00, -8000.00, 0.00, 0.00, 1, 'Cash'),
(79, 'Standard Purchase', 'sada', '2024-11-09', '02:25:59', 786607354, 'Global Mart', 1830.00, 0.00, 1870.00, -20.00, 0.00, 0.00, 1, 'Cash'),
(80, 'Standard Purchase', 'sada', '2024-11-11', '17:49:51', 786607354, 'Global Mart', 80.00, 0.00, 120.00, -20.00, 0.00, 0.00, 1, 'Cash'),
(81, 'Standard Purchase', 'Walk-in Customer', '2024-11-11', '17:51:31', 0, 'Global Mart', 80.00, 0.00, 100.00, -10.00, 0.00, 0.00, 1, 'Cash'),
(82, 'Standard Purchase', 'new customer', '2024-11-11', '17:55:38', 1234567890, 'Global Mart', 80.00, 0.00, 920.00, -420.00, 0.00, 0.00, 1, 'Cash'),
(84, 'Standard Purchase', 'Walk-in Customer', '2024-11-11', '18:16:39', 0, 'Global Mart', 80.00, 0.00, 120.00, -20.00, 0.00, 0.00, 1, 'Cash'),
(85, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '15:15:53', 0, 'Global Mart', 1700.00, 0.00, 1700.00, 0.00, 0.00, 0.00, 1, 'Online Transfer'),
(86, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '15:19:52', 0, 'Global Mart', 80.00, 0.00, 920.00, -420.00, 0.00, 0.00, 1, 'Cash'),
(87, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '15:20:13', 0, 'Global Mart', 80.00, 0.00, 920.00, -420.00, 0.00, 0.00, 1, 'Cash'),
(88, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '15:20:56', 0, 'Global Mart', 80.00, 0.00, 920.00, -420.00, 0.00, 0.00, 1, 'Cash'),
(89, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '15:23:31', 0, 'Global Mart', 80.00, 0.00, 920.00, -420.00, 0.00, 0.00, 1, 'Cash'),
(90, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '15:24:28', 0, 'Global Mart', 80.00, 0.00, 920.00, -420.00, 0.00, 0.00, 1, 'Cash'),
(91, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '15:46:42', 0, 'Global Mart', 160.00, 0.00, 160.00, 0.00, 0.00, 0.00, 1, 'Online Transfer'),
(92, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '15:47:14', 0, 'Global Mart', 100.00, 0.00, 900.00, -400.00, 0.00, 0.00, 1, 'Cash'),
(93, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '15:53:21', 0, 'Global Mart', 100.00, 0.00, 900.00, -400.00, 0.00, 0.00, 1, 'Cash'),
(94, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '16:02:47', 0, 'Global Mart', 80.00, 0.00, 120.00, -20.00, 0.00, 0.00, 1, 'Cash'),
(95, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '16:05:00', 0, 'Global Mart', 80.00, 0.00, 920.00, -420.00, 0.00, 0.00, 1, 'Cash'),
(96, 'Standard Purchase', 'John Doe', '2024-11-18', '20:15:05', 714730996, 'Global Mart', 80.22, 0.00, 1119.78, -519.78, 0.00, 0.00, 1, 'Cash'),
(97, 'Standard Purchase', 'John Doe', '2024-11-18', '23:20:50', 714730996, 'Global Mart', 80.00, 0.00, 60.00, 0.00, 0.00, 0.00, 1, 'Cash'),
(98, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '23:31:18', 0, 'Global Mart', 80.00, 0.00, 140.00, -40.00, 0.00, 0.00, 1, 'Cash'),
(99, 'Standard Purchase', 'Walk-in Customer', '2024-11-19', '01:05:03', 0, 'Global Mart', 80.00, 0.00, 60.00, 0.00, 0.00, 0.00, 1, 'Cash'),
(100, 'Standard Purchase', 'Walk-in Customer', '2024-11-19', '01:06:23', 0, 'Global Mart', 80.00, 0.00, 940.00, -440.00, 0.00, 0.00, 1, 'Cash'),
(101, 'Standard Purchase', 'Walk-in Customer', '2024-11-19', '01:29:14', 0, 'Global Mart', 80.00, 0.00, 9920.00, -4920.00, 0.00, 0.00, 1, 'Cash'),
(102, 'Standard Purchase', 'John Doe', '2024-11-19', '02:13:34', 714730996, 'Global Mart', 80.00, 0.00, 120.00, -20.00, 0.00, 0.00, 1, 'Cash'),
(103, 'Standard Purchase', 'Walk-in Customer', '2024-11-19', '02:25:30', 0, 'Global Mart', 100.00, 0.00, -100.00, 100.00, 0.00, 0.00, 0, 'Cash'),
(104, 'Standard Purchase', 'Walk-in Customer', '2024-11-19', '02:26:05', 0, 'Global Mart', 100.00, 0.00, -100.00, 100.00, 0.00, 0.00, 0, 'Cash'),
(105, 'Standard Purchase', 'Walk-in Customer', '2024-11-19', '02:26:58', 0, 'Global Mart', 80.00, 0.00, -80.00, 80.00, 0.00, 0.00, 0, 'Cash'),
(106, 'Standard Purchase', 'Walk-in Customer', '2024-11-19', '02:28:04', 0, 'Global Mart', 80.00, 0.00, 920.00, -420.00, 0.00, 0.00, 1, 'Cash'),
(107, 'Standard Purchase', 'John Doe', '2024-11-19', '02:35:14', 714730996, 'Global Mart', 100.00, 0.00, 100.00, -35.00, 0.00, 0.00, 1, 'Cash'),
(108, 'Standard Purchase', 'John Doe', '2024-11-19', '02:37:38', 714730996, 'Global Mart', 100.00, 0.00, 80.00, 0.00, 0.00, 0.00, 1, 'Cash'),
(109, 'Standard Purchase', 'Walk-in Customer', '2024-11-19', '02:39:32', 0, 'Global Mart', 80.00, 0.00, -80.00, 80.00, 0.00, 0.00, 0, 'Cash'),
(110, 'Standard Purchase', 'Walk-in Customer', '2024-11-19', '02:40:07', 0, 'Global Mart', 80.00, 0.00, -80.00, 80.00, 0.00, 0.00, 0, 'Cash'),
(111, 'Standard Purchase', 'Walk-in Customer', '2024-11-19', '02:43:15', 0, 'Global Mart', 80.00, 0.00, 920.00, -420.00, 0.00, 0.00, 1, 'Cash');

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
  `product_type` varchar(20) NOT NULL,
  `description` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_sinhala_ci DEFAULT NULL,
  `del_rate` decimal(8,2) DEFAULT NULL,
  `del_cost` decimal(8,2) DEFAULT NULL,
  `del_profit` double(10,2) DEFAULT NULL,
  `has_stock` text NOT NULL DEFAULT '1',
  `stock_alert_limit` int(4) NOT NULL DEFAULT 20,
  `image` varchar(100) DEFAULT NULL,
  `show_in_landing_page` varchar(8) NOT NULL DEFAULT '0',
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `sku` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_type`, `description`, `del_rate`, `del_cost`, `del_profit`, `has_stock`, `stock_alert_limit`, `image`, `show_in_landing_page`, `category_id`, `brand_id`, `barcode`, `created_at`, `updated_at`, `sku`) VALUES
(502, 'Full Cream Milk', '', '1 liter of full cream milk.', 200.00, 150.00, 50.00, '1', 10, NULL, '0', 1, 1, '9786249993402', '2024-10-27 18:49:53', '2024-10-27 18:49:53', NULL),
(503, 'Potato Chips', '', 'Crispy potato chips.', 80.00, 50.00, 30.00, '1', 20, NULL, '0', 2, 2, '3601660000750', '2024-10-27 18:49:53', '2024-11-11 12:46:41', NULL),
(504, 'Cola Soft Drink', '', '330ml can of cola.', 75.00, 30.00, 20.00, '1', 15, NULL, '0', 3, 3, '9786242040776', '2024-10-27 18:49:53', '2024-10-28 17:52:00', NULL),
(505, 'Cola Hot Drink', '', NULL, 50.00, 20.00, 100.00, '1', 20, NULL, '0', 1, 2, '6294015142945', '2024-10-28 17:04:16', '2024-10-29 19:32:30', 'testp001'),
(506, 'Orrepaste', '', NULL, 470.00, 300.00, 200.00, '1', 20, NULL, '0', 1, 3, '9556258002606', '2024-10-29 09:04:41', '2024-10-29 10:31:25', 'orrpaste001'),
(507, 'test', 'standard', NULL, NULL, NULL, NULL, '1', 20, NULL, '0', 2, 2, '9789556812299', '2024-12-26 19:19:06', '2024-12-26 19:19:06', 'test'),
(508, 'asdsad', 'standard', NULL, NULL, NULL, NULL, '1', 20, '676f58de030ec_317615432_1305125113675009_4776231069856830763_n - Copy.jpg', '1', 1, NULL, 'asdsad', '2024-12-28 01:48:17', '2024-12-28 01:48:17', 'sada'),
(510, 'vari check test product', 'standard', NULL, NULL, NULL, NULL, '1', 20, NULL, '0', 3, 1, 'PROD-404319', '2024-12-28 04:16:12', '2024-12-28 04:16:12', 'adasdadFFF');

-- --------------------------------------------------------

--
-- Table structure for table `product_batch`
--

CREATE TABLE `product_batch` (
  `batch_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `batch_number` varchar(50) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `profit` decimal(10,2) GENERATED ALWAYS AS (`selling_price` - `cost`) STORED,
  `expiry_date` date DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `purchase_date` date NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','expired','discontinued') DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_batch`
--

INSERT INTO `product_batch` (`batch_id`, `product_id`, `batch_number`, `cost`, `selling_price`, `expiry_date`, `quantity`, `supplier_id`, `purchase_date`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(5, 502, 'BATCH-001', 120.00, 200.00, '2025-01-01', 50, NULL, '2024-10-01', 'active', 'First batch of full cream milk.', '2024-10-27 18:55:35', '2024-10-27 18:55:35'),
(6, 503, 'BATCH-001', 40.00, 80.00, NULL, 100, NULL, '2024-10-01', 'active', 'First batch of potato chips.', '2024-10-27 18:55:35', '2024-10-27 18:55:35'),
(7, 503, 'BATCH-002', 50.00, 100.00, NULL, 100, NULL, '2024-10-01', 'active', 'Second batch of potato chips.', '2024-10-27 18:55:35', '2024-10-27 18:55:35'),
(8, 504, 'BATCH-001', 25.00, 50.00, NULL, 75, NULL, '2024-10-01', 'active', 'First batch of cola soft drink.', '2024-10-27 18:55:35', '2024-10-27 18:55:35'),
(9, 502, 'BATCH-001', 120.00, 200.00, '2025-01-01', 50, NULL, '2024-10-01', 'active', 'First batch of full cream milk.', '2024-10-27 18:59:19', '2024-10-27 18:59:19'),
(10, 503, 'BATCH-001', 40.00, 80.00, NULL, 100, NULL, '2024-10-01', 'active', 'First batch of potato chips.', '2024-10-27 18:59:19', '2024-10-27 18:59:19'),
(11, 503, 'BATCH-002', 50.00, 100.00, NULL, 100, NULL, '2024-10-01', 'active', 'Second batch of potato chips.', '2024-10-27 18:59:19', '2024-10-27 18:59:19'),
(13, 504, 'BATCH-003', 50.00, 100.00, NULL, 75, NULL, '2024-10-01', 'active', 'Third batch of cola soft drink.', '2024-10-27 18:59:19', '2024-10-27 18:59:19'),
(14, 506, '04213402', 300.00, 500.00, '2027-03-02', 10, NULL, '2024-10-29', 'active', NULL, '2024-10-29 09:08:36', '2024-10-29 09:12:24'),
(15, 507, 'BATCH-20241226-aabdb', 50.00, 150.00, NULL, 20, NULL, '2024-12-27', 'active', NULL, '2024-12-26 19:19:07', '2024-12-26 19:19:07'),
(16, 508, 'BATCH-20241228-b14b0', 23.00, 33.00, NULL, 0, NULL, '2024-12-28', 'active', NULL, '2024-12-28 01:48:18', '2024-12-28 01:48:18'),
(17, 510, 'test vari 1', 60.00, 70.00, NULL, 50, NULL, '2024-12-28', 'active', 'no', '2024-12-28 04:16:13', '2024-12-28 04:16:13'),
(18, 510, 'test vari 2', 160.00, 170.00, NULL, 150, NULL, '2024-12-28', 'active', 'yes', '2024-12-28 04:16:13', '2024-12-28 04:16:13');

-- --------------------------------------------------------

--
-- Stand-in structure for view `product_view`
-- (See below for the actual view)
--
CREATE TABLE `product_view` (
`product_id` int(10)
,`product_name` varchar(100)
,`description` varchar(50)
,`rate` decimal(10,2)
,`cost` decimal(10,2)
,`profit` decimal(10,2)
,`has_stock` text
,`stock_alert_limit` int(4)
,`image` varchar(100)
,`show_in_landing_page` varchar(8)
,`category_id` int(11)
,`brand_id` int(11)
,`barcode` varchar(50)
,`created_at` timestamp
,`updated_at` timestamp
,`sku` varchar(30)
,`stock_qty` decimal(32,0)
);

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
(87, 78, 'Orrepaste', NULL, '4', 500.00, 2000.00, 0.00, 2000.00, 'Global Mart'),
(88, 79, 'Orrepaste', NULL, '1', 500.00, 500.00, 0.00, 500.00, 'Global Mart'),
(89, 79, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(90, 79, 'Full Cream Milk', NULL, '6', 200.00, 1200.00, 0.00, 1200.00, 'Global Mart'),
(91, 79, 'Cola Soft Drink', NULL, '1', 50.00, 50.00, 0.00, 50.00, 'Global Mart'),
(92, 80, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(93, 81, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(94, 82, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(96, 84, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(97, 85, 'Potato Chips', NULL, '20', 80.00, 1600.00, 0.00, 1600.00, 'Global Mart'),
(98, 85, 'Potato Chips', NULL, '1', 100.00, 100.00, 0.00, 100.00, 'Global Mart'),
(99, 86, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(100, 87, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(101, 88, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(102, 89, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(103, 90, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(104, 91, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(105, 91, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(106, 92, 'Potato Chips', NULL, '1', 100.00, 100.00, 0.00, 100.00, 'Global Mart'),
(107, 93, 'Potato Chips', NULL, '1', 100.00, 100.00, 0.00, 100.00, 'Global Mart'),
(108, 94, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(109, 95, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(110, 96, 'Potato Chips', NULL, '1', 80.22, 80.22, 0.00, 80.22, 'Global Mart'),
(111, 97, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(112, 98, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(113, 99, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(114, 100, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(115, 101, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(116, 102, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(117, 103, 'Potato Chips', NULL, '1', 100.00, 100.00, 0.00, 100.00, 'Global Mart'),
(118, 104, 'Potato Chips', NULL, '1', 100.00, 100.00, 0.00, 100.00, 'Global Mart'),
(119, 105, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(120, 106, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(121, 107, 'Potato Chips', NULL, '1', 100.00, 100.00, 0.00, 100.00, 'Global Mart'),
(122, 108, 'Potato Chips', NULL, '1', 100.00, 100.00, 0.00, 100.00, 'Global Mart'),
(123, 109, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(124, 110, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart'),
(125, 111, 'Potato Chips', NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart');

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

-- --------------------------------------------------------

--
-- Structure for view `product_view`
--
DROP TABLE IF EXISTS `product_view`;

CREATE ALGORITHM=UNDEFINED
SQL SECURITY DEFINER
VIEW `product_view` AS 
SELECT 
    `p`.`product_id` AS `product_id`, 
    `p`.`product_name` AS `product_name`, 
    `p`.`description` AS `description`, 
    `pb`.`selling_price` AS `rate`, 
    `pb`.`cost` AS `cost`, 
    `pb`.`profit` AS `profit`, 
    `p`.`has_stock` AS `has_stock`, 
    `p`.`stock_alert_limit` AS `stock_alert_limit`, 
    `p`.`image` AS `image`, 
    `p`.`show_in_landing_page` AS `show_in_landing_page`, 
    `p`.`category_id` AS `category_id`, 
    `p`.`brand_id` AS `brand_id`, 
    `p`.`barcode` AS `barcode`, 
    `p`.`sku` AS `sku`, 
    coalesce(sum(`pb`.`quantity`),0) AS `stock_qty` 
FROM 
    (`products` `p` 
LEFT JOIN `product_batch` `pb` 
    ON (`p`.`product_id` = `pb`.`product_id`)) 
GROUP BY 
    `p`.`product_id`;


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
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`brand_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_mobile` (`customer_mobile`);

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
-- Indexes for table `held_invoices`
--
ALTER TABLE `held_invoices`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `pettycash`
--
ALTER TABLE `pettycash`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `product_name` (`product_name`) USING BTREE,
  ADD KEY `category_id` (`category_id`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `idx_product_name` (`product_name`),
  ADD KEY `idx_sku` (`sku`),
  ADD KEY `idx_barcode` (`barcode`);

--
-- Indexes for table `product_batch`
--
ALTER TABLE `product_batch`
  ADD PRIMARY KEY (`batch_id`),
  ADD KEY `product_batch_ibfk_1` (`product_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `purchase`
--
ALTER TABLE `purchase`
  ADD PRIMARY KEY (`purchase_id`),
  ADD KEY `supplier` (`supplier`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `action_log`
--
ALTER TABLE `action_log`
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1156;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bank_deposits`
--
ALTER TABLE `bank_deposits`
  MODIFY `bank_deposit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employ_id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `error_log`
--
ALTER TABLE `error_log`
  MODIFY `error_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `held_invoices`
--
ALTER TABLE `held_invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_number` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `InvoiceBalPayRecords`
--
ALTER TABLE `InvoiceBalPayRecords`
  MODIFY `InvBalPayRecords_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `pettycash`
--
ALTER TABLE `pettycash`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=511;

--
-- AUTO_INCREMENT for table `product_batch`
--
ALTER TABLE `product_batch`
  MODIFY `batch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `purchase`
--
ALTER TABLE `purchase`
  MODIFY `purchase_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salary`
--
ALTER TABLE `salary`
  MODIFY `salary_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sales_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_log`
--
ALTER TABLE `transaction_log`
  MODIFY `transaction_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=182;

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
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`);

--
-- Constraints for table `product_batch`
--
ALTER TABLE `product_batch`
  ADD CONSTRAINT `product_batch_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_batch_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`);

--
-- Constraints for table `purchase`
--
ALTER TABLE `purchase`
  ADD CONSTRAINT `purchase_ibfk_1` FOREIGN KEY (`supplier`) REFERENCES `suppliers` (`supplier_name`) ON DELETE SET NULL ON UPDATE CASCADE;

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

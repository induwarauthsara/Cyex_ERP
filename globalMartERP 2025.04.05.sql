-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 05, 2025 at 01:49 AM
-- Server version: 10.6.21-MariaDB-0ubuntu0.22.04.2
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

DELIMITER $$
--
-- Functions
--
CREATE DEFINER=`globalmart_user`@`%` FUNCTION `get_next_number` (`sequence_name` VARCHAR(50)) RETURNS VARCHAR(20) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC BEGIN
    DECLARE next_num INT;
    DECLARE prefix VARCHAR(10);
    DECLARE padding INT;
    DECLARE result VARCHAR(20);
    
    SELECT next_value, prefix, padding 
    INTO next_num, prefix, padding
    FROM sequences 
    WHERE name = sequence_name 
    FOR UPDATE;
    
    SET result = CONCAT(
        prefix, 
        LPAD(next_num, padding, '0')
    );
    
    UPDATE sequences 
    SET next_value = next_value + 1 
    WHERE name = sequence_name;
    
    RETURN result;
END$$

DELIMITER ;

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
(3, 'cash_in_hand', 'cash', 34655.00),
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
(1155, 0, 'Add Category', 'New Category: newCt', '2024-12-23', '07:34:06'),
(1156, 0, 'ADD_SUPPLIER', 'Added new supplier: test supplier 3', '2025-01-09', '00:12:50'),
(1157, 0, 'DELETE_SUPPLIER', 'Deleted supplier ID: 3', '2025-01-09', '00:18:08'),
(1158, 0, 'ADD_PAYMENT', 'Added supplier payment: 100', '2025-01-09', '00:30:59'),
(1159, 0, 'UPDATE_CREDIT', 'Updated supplier credit balance', '2025-01-09', '00:30:59'),
(1160, 0, 'ADD_SUPPLIER', 'Added new supplier: test supplier 3', '2025-01-09', '00:33:03'),
(1161, 0, 'EDIT_SUPPLIER', 'Updated supplier: test supplier 3', '2025-01-09', '00:34:25'),
(1162, 0, 'ADD_PAYMENT', 'Added supplier payment: 200', '2025-01-09', '01:42:46'),
(1163, 0, 'UPDATE_CREDIT', 'Updated supplier credit balance', '2025-01-09', '01:42:46'),
(1164, 0, 'Transaction Log', 'Transaction Type : ADD_PAYMENT, description : Payment added for supplier ID 1, Rs. 200 by 0 ', '2025-01-09', '01:42:47'),
(1165, 0, 'Add Brand', 'New Brand: abc', '2025-04-02', '03:41:27'),
(1166, 0, 'Add Brand', 'New Brand: abc', '2025-04-02', '03:41:30'),
(1167, 0, 'Add Category', 'New Category: Soap', '2025-04-02', '03:41:42'),
(1168, 0, 'Add Category', 'New Category: Soap', '2025-04-02', '03:41:45'),
(1169, 0, 'Add Category', 'New Category: soap 1', '2025-04-02', '03:41:58'),
(1170, 0, 'Add Brand', 'New Brand: soan dds', '2025-04-02', '03:45:20'),
(1171, 0, 'Add Category', 'New Category: jhdsjd', '2025-04-02', '03:45:30'),
(1172, 0, 'Add Brand', 'New Brand: OMS', '2025-04-04', '23:14:48'),
(1173, 0, 'Add Category', 'New Category: earbuds', '2025-04-04', '23:15:09'),
(1174, 0, 'Add Category', 'New Category: wireless-earbuds', '2025-04-04', '23:21:14');

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
-- Table structure for table `barcode_print_items`
--

CREATE TABLE `barcode_print_items` (
  `item_id` int(11) NOT NULL,
  `job_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `batch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barcode_print_jobs`
--

CREATE TABLE `barcode_print_jobs` (
  `job_id` int(11) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `status` enum('pending','completed','failed','saved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barcode_templates`
--

CREATE TABLE `barcode_templates` (
  `template_id` int(11) NOT NULL,
  `template_name` varchar(50) NOT NULL,
  `paper_width` decimal(10,2) NOT NULL,
  `paper_height` decimal(10,2) NOT NULL,
  `show_shop_name` tinyint(1) DEFAULT 1,
  `show_product_name` tinyint(1) DEFAULT 1,
  `show_price` tinyint(1) DEFAULT 1,
  `show_unit` tinyint(1) DEFAULT 0,
  `show_category` tinyint(1) DEFAULT 0,
  `show_promo_price` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `margin` decimal(5,2) DEFAULT 0.00,
  `font_size` float DEFAULT 8,
  `barcode_height` float DEFAULT 10,
  `shop_name` varchar(255) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barcode_templates`
--

INSERT INTO `barcode_templates` (`template_id`, `template_name`, `paper_width`, `paper_height`, `show_shop_name`, `show_product_name`, `show_price`, `show_unit`, `show_category`, `show_promo_price`, `created_at`, `updated_at`, `margin`, `font_size`, `barcode_height`, `shop_name`) VALUES
(6, '30x15 Barcode, Product Name, Price', 30.00, 15.00, 0, 1, 1, 0, 0, 0, '2025-04-04 21:57:03', '2025-04-04 22:01:51', 1.00, 9, 15, ''),
(7, '30x15 Barcode, Product Name', 30.00, 15.00, 0, 1, 0, 0, 0, 0, '2025-04-04 21:57:42', '2025-04-04 22:01:54', 1.00, 9, 15, ''),
(8, '30x15 Barcode Only', 30.00, 15.00, 0, 0, 0, 0, 0, 0, '2025-04-04 21:58:40', '2025-04-04 22:01:58', 1.00, 9, 15, ''),
(9, '30x15 Business Name, Barcode, Product Name, Price', 30.00, 15.00, 1, 1, 1, 0, 0, 0, '2025-04-04 22:52:31', '2025-04-04 22:52:31', 0.00, 8, 10, '');

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
(7, 'new1', NULL, '2024-12-23 02:03:42', '2024-12-23 02:03:42'),
(8, 'abc', NULL, '2025-04-02 03:41:27', '2025-04-02 03:41:27'),
(9, 'abc', NULL, '2025-04-02 03:41:29', '2025-04-02 03:41:29'),
(10, 'soan dds', NULL, '2025-04-02 03:45:19', '2025-04-02 03:45:19'),
(11, 'OMS', NULL, '2025-04-04 23:14:48', '2025-04-04 23:14:48');

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
(6, 'newCt', NULL, '2024-12-23 02:04:06', '2024-12-23 02:04:06'),
(7, 'Soap', NULL, '2025-04-02 03:41:42', '2025-04-02 03:41:42'),
(8, 'Soap', NULL, '2025-04-02 03:41:44', '2025-04-02 03:41:44'),
(9, 'soap 1', NULL, '2025-04-02 03:41:57', '2025-04-02 03:41:57'),
(10, 'jhdsjd', NULL, '2025-04-02 03:45:29', '2025-04-02 03:45:29'),
(11, 'earbuds', NULL, '2025-04-04 23:15:08', '2025-04-04 23:15:08'),
(12, 'wireless-earbuds', NULL, '2025-04-04 23:21:14', '2025-04-04 23:21:14');

-- --------------------------------------------------------

--
-- Table structure for table `combo_products`
--

CREATE TABLE `combo_products` (
  `id` int(11) NOT NULL,
  `combo_product_id` int(11) DEFAULT NULL,
  `component_product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `batch_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `combo_products`
--

INSERT INTO `combo_products` (`id`, `combo_product_id`, `component_product_id`, `quantity`, `batch_number`, `created_at`) VALUES
(1, 513, 506, 5, 'BATCH-20250109-d4a28', '2025-01-09 20:14:10'),
(2, 513, 502, 3, 'BATCH-20250109-d4a28', '2025-01-09 20:14:11'),
(3, 514, 507, 1, 'vari 2 comb', '2025-01-10 11:38:10'),
(4, 514, 507, 1, 'vari 2 comb', '2025-01-10 11:38:10'),
(5, 514, 513, 1, 'vari 2 comb', '2025-01-10 11:38:10'),
(6, 514, 510, 1, 'vari 2 comb', '2025-01-10 11:38:10'),
(7, 514, 511, 1, 'vari 2 comb', '2025-01-10 11:38:10');

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
(5, 500, 'Column \'rate\' cannot be null', 'INSERT INTO sales (invoice_number, product, qty, rate, amount, cost, profit, worker)\r\n                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)', 'invoice_submission', 'Error during invoice submission', '2024-12-27', '00:52:28', 0, 'pending'),
(11, 500, 'Unknown column \'id\' in \'field list\'', 'SELECT id FROM products WHERE product_name = ?', 'invoice_submission', 'Error during invoice submission', '2025-01-10', '22:11:29', 0, 'pending'),
(12, 500, 'Unknown column \'p.id\' in \'on clause\'', 'SELECT cp.component_product_id, cp.quantity as required_qty, p.product_name \r\n                      FROM combo_products cp \r\n                      JOIN products p ON p.id = cp.component_product_id \r\n                      WHERE cp.combo_product_id = ?', 'invoice_submission', 'Error during invoice submission', '2025-01-10', '22:13:17', 0, 'pending'),
(13, 500, 'Insufficient stock for component: Orrepaste', 'SELECT cp.component_product_id, cp.quantity as required_qty, p.product_name \r\n                      FROM combo_products cp \r\n                      JOIN products p ON p.product_id = cp.component_product_id \r\n                      WHERE cp.combo_product_id = ?', 'invoice_submission', 'Error during invoice submission', '2025-01-10', '22:30:56', 0, 'pending'),
(16, 500, 'Insufficient stock for component: Orrepaste', 'SELECT cp.component_product_id, cp.quantity as required_qty, p.product_name \r\n                      FROM combo_products cp \r\n                      JOIN products p ON p.product_id = cp.component_product_id \r\n                      WHERE cp.combo_product_id = ?', 'invoice_submission', 'Error during invoice submission', '2025-03-18', '00:52:09', 0, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `goods_receipt_notes`
--

CREATE TABLE `goods_receipt_notes` (
  `grn_id` int(11) NOT NULL,
  `grn_number` varchar(20) NOT NULL,
  `po_id` int(11) NOT NULL,
  `receipt_date` date NOT NULL DEFAULT current_timestamp(),
  `invoice_number` varchar(50) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('draft','completed','cancelled') DEFAULT 'draft',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grn_items`
--

CREATE TABLE `grn_items` (
  `grn_item_id` int(11) NOT NULL,
  `grn_id` int(11) NOT NULL,
  `po_item_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `received_qty` int(11) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `discount_value` decimal(5,2) DEFAULT 0.00,
  `individual_discount_mode` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `held_invoices`
--

INSERT INTO `held_invoices` (`id`, `customer_name`, `customer_number`, `items`, `total_amount`, `discount_amount`, `discount_type`, `total_payable`, `status`, `held_at`, `discount_value`, `individual_discount_mode`) VALUES
(26, 'ABC Corp', '0112345678', '[{\"name\":\"combo product test 1\",\"quantity\":1,\"price\":\"200.00\",\"subtotal\":\"200.00\"},{\"name\":\"combo product test 2\",\"quantity\":12,\"price\":\"35.00\",\"subtotal\":\"420.00\"}]', 620.00, 310.00, 'percentage', 310.00, 'completed', '2025-03-17 17:46:15', 50.00, 0);

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
  `paymentMethod` varchar(20) NOT NULL,
  `individual_discount_mode` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`invoice_number`, `invoice_description`, `customer_name`, `invoice_date`, `time`, `customer_mobile`, `biller`, `total`, `discount`, `advance`, `balance`, `cost`, `profit`, `full_paid`, `paymentMethod`, `individual_discount_mode`) VALUES
(78, 'Standard Purchase', 'Walk-in Customer', '2024-11-09', '02:17:18', 0, '0', 2000.00, 0.00, 18000.00, -8000.00, 0.00, 0.00, 1, 'Cash', 0),
(79, 'Standard Purchase', 'sada', '2024-11-09', '02:25:59', 786607354, 'Global Mart', 1830.00, 0.00, 1870.00, -20.00, 0.00, 0.00, 1, 'Cash', 0),
(80, 'Standard Purchase', 'sada', '2024-11-11', '17:49:51', 786607354, 'Global Mart', 80.00, 0.00, 120.00, -20.00, 0.00, 0.00, 1, 'Cash', 0),
(81, 'Standard Purchase', 'Walk-in Customer', '2024-11-11', '17:51:31', 0, 'Global Mart', 80.00, 0.00, 100.00, -10.00, 0.00, 0.00, 1, 'Cash', 0),
(82, 'Standard Purchase', 'new customer', '2024-11-11', '17:55:38', 1234567890, 'Global Mart', 80.00, 0.00, 920.00, -420.00, 0.00, 0.00, 1, 'Cash', 0),
(84, 'Standard Purchase', 'Walk-in Customer', '2024-11-11', '18:16:39', 0, 'Global Mart', 80.00, 0.00, 120.00, -20.00, 0.00, 0.00, 1, 'Cash', 0),
(85, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '15:15:53', 0, 'Global Mart', 1700.00, 0.00, 1700.00, 0.00, 0.00, 0.00, 1, 'Online Transfer', 0),
(86, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '15:19:52', 0, 'Global Mart', 80.00, 0.00, 920.00, -420.00, 0.00, 0.00, 1, 'Cash', 0),
(87, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '15:20:13', 0, 'Global Mart', 80.00, 0.00, 920.00, -420.00, 0.00, 0.00, 1, 'Cash', 0),
(88, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '15:20:56', 0, 'Global Mart', 80.00, 0.00, 920.00, -420.00, 0.00, 0.00, 1, 'Cash', 0),
(89, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '15:23:31', 0, 'Global Mart', 80.00, 0.00, 920.00, -420.00, 0.00, 0.00, 1, 'Cash', 0),
(90, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '15:24:28', 0, 'Global Mart', 80.00, 0.00, 920.00, -420.00, 0.00, 0.00, 1, 'Cash', 0),
(91, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '15:46:42', 0, 'Global Mart', 160.00, 0.00, 160.00, 0.00, 0.00, 0.00, 1, 'Online Transfer', 0),
(92, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '15:47:14', 0, 'Global Mart', 100.00, 0.00, 900.00, -400.00, 0.00, 0.00, 1, 'Cash', 0),
(93, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '15:53:21', 0, 'Global Mart', 100.00, 0.00, 900.00, -400.00, 0.00, 0.00, 1, 'Cash', 0),
(94, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '16:02:47', 0, 'Global Mart', 80.00, 0.00, 120.00, -20.00, 0.00, 0.00, 1, 'Cash', 0),
(95, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '16:05:00', 0, 'Global Mart', 80.00, 0.00, 920.00, -420.00, 0.00, 0.00, 1, 'Cash', 0),
(96, 'Standard Purchase', 'John Doe', '2024-11-18', '20:15:05', 714730996, 'Global Mart', 80.22, 0.00, 1119.78, -519.78, 0.00, 0.00, 1, 'Cash', 0),
(97, 'Standard Purchase', 'John Doe', '2024-11-18', '23:20:50', 714730996, 'Global Mart', 80.00, 0.00, 60.00, 0.00, 0.00, 0.00, 1, 'Cash', 0),
(98, 'Standard Purchase', 'Walk-in Customer', '2024-11-18', '23:31:18', 0, 'Global Mart', 80.00, 0.00, 140.00, -40.00, 0.00, 0.00, 1, 'Cash', 0),
(99, 'Standard Purchase', 'Walk-in Customer', '2024-11-19', '01:05:03', 0, 'Global Mart', 80.00, 0.00, 60.00, 0.00, 0.00, 0.00, 1, 'Cash', 0),
(100, 'Standard Purchase', 'Walk-in Customer', '2024-11-19', '01:06:23', 0, 'Global Mart', 80.00, 0.00, 940.00, -440.00, 0.00, 0.00, 1, 'Cash', 0),
(101, 'Standard Purchase', 'Walk-in Customer', '2024-11-19', '01:29:14', 0, 'Global Mart', 80.00, 0.00, 9920.00, -4920.00, 0.00, 0.00, 1, 'Cash', 0),
(102, 'Standard Purchase', 'John Doe', '2024-11-19', '02:13:34', 714730996, 'Global Mart', 80.00, 0.00, 120.00, -20.00, 0.00, 0.00, 1, 'Cash', 0),
(103, 'Standard Purchase', 'Walk-in Customer', '2024-11-19', '02:25:30', 0, 'Global Mart', 100.00, 0.00, -100.00, 100.00, 0.00, 0.00, 0, 'Cash', 0),
(104, 'Standard Purchase', 'Walk-in Customer', '2024-11-19', '02:26:05', 0, 'Global Mart', 100.00, 0.00, -100.00, 100.00, 0.00, 0.00, 0, 'Cash', 0),
(105, 'Standard Purchase', 'Walk-in Customer', '2024-11-19', '02:26:58', 0, 'Global Mart', 80.00, 0.00, -80.00, 80.00, 0.00, 0.00, 0, 'Cash', 0),
(106, 'Standard Purchase', 'Walk-in Customer', '2024-11-19', '02:28:04', 0, 'Global Mart', 80.00, 0.00, 920.00, -420.00, 0.00, 0.00, 1, 'Cash', 0),
(107, 'Standard Purchase', 'John Doe', '2024-11-19', '02:35:14', 714730996, 'Global Mart', 100.00, 0.00, 100.00, -35.00, 0.00, 0.00, 1, 'Cash', 0),
(108, 'Standard Purchase', 'John Doe', '2024-11-19', '02:37:38', 714730996, 'Global Mart', 100.00, 0.00, 80.00, 0.00, 0.00, 0.00, 1, 'Cash', 0),
(109, 'Standard Purchase', 'Walk-in Customer', '2024-11-19', '02:39:32', 0, 'Global Mart', 80.00, 0.00, -80.00, 80.00, 0.00, 0.00, 0, 'Cash', 0),
(110, 'Standard Purchase', 'Walk-in Customer', '2024-11-19', '02:40:07', 0, 'Global Mart', 80.00, 0.00, -80.00, 80.00, 0.00, 0.00, 0, 'Cash', 0),
(111, 'Standard Purchase', 'Walk-in Customer', '2024-11-19', '02:43:15', 0, 'Global Mart', 80.00, 0.00, 920.00, -420.00, 0.00, 0.00, 1, 'Cash', 0),
(113, 'Standard Purchase', 'Walk-in Customer', '2025-01-10', '00:54:22', 0, 'Global Mart', 475.00, 0.00, 525.00, -25.00, 0.00, 0.00, 1, 'Cash', 0),
(120, 'Standard Purchase', 'Walk-in Customer', '2025-01-10', '22:24:50', 0, 'Global Mart', 235.00, 0.00, 765.00, -265.00, 0.00, 0.00, 1, 'Cash', 0),
(122, 'Standard Purchase', 'Walk-in Customer', '2025-01-10', '22:36:18', 0, 'Global Mart', 1000.00, 0.00, 1000.00, 0.00, 0.00, 0.00, 1, 'Cash', 0),
(123, 'Standard Purchase', 'Walk-in Customer', '2025-01-10', '23:05:03', 0, 'Global Mart', 600.00, 0.00, 1400.00, -400.00, 0.00, 0.00, 1, 'Cash', 0),
(124, 'Standard Purchase', 'Walk-in Customer', '2025-01-11', '00:51:49', 0, 'Global Mart', 2600.00, 0.00, 7400.00, -2400.00, 0.00, 0.00, 1, 'Cash', 0),
(125, 'Standard Purchase', 'Walk-in Customer', '2025-01-11', '00:56:41', 0, 'Global Mart', 2600.00, 0.00, 7400.00, -2400.00, 0.00, 0.00, 1, 'Cash', 0),
(126, 'Standard Purchase', 'Walk-in Customer', '2025-01-11', '00:59:46', 0, 'Global Mart', 2600.00, 0.00, 7400.00, -2400.00, 0.00, 0.00, 1, 'Cash', 0),
(127, 'Standard Purchase', 'Walk-in Customer', '2025-01-11', '01:00:06', 0, 'Global Mart', 2600.00, 0.00, 7400.00, -2400.00, 0.00, 0.00, 1, 'Cash', 0),
(128, 'Standard Purchase', 'Walk-in Customer', '2025-01-11', '01:05:23', 0, 'Global Mart', 2600.00, 0.00, 7400.00, -2400.00, 0.00, 0.00, 1, 'Cash', 0),
(129, 'Standard Purchase', 'Walk-in Customer', '2025-01-11', '01:08:28', 0, 'Global Mart', 2600.00, 0.00, 7400.00, -2400.00, 0.00, 0.00, 1, 'Cash', 0),
(131, 'Standard Purchase', 'Walk-in Customer', '2025-01-11', '01:14:29', 0, 'Global Mart', 2600.00, 0.00, 7400.00, -2400.00, 0.00, 0.00, 1, 'Cash', 0),
(132, 'Standard Purchase', 'Walk-in Customer', '2025-01-11', '01:17:40', 0, 'Global Mart', 2600.00, 0.00, 7400.00, -2400.00, 0.00, 0.00, 1, 'Cash', 0),
(133, 'Standard Purchase', 'Walk-in Customer', '2025-01-11', '01:19:39', 0, 'Global Mart', 2600.00, 0.00, 7400.00, -2400.00, 0.00, 0.00, 1, 'Cash', 0),
(134, 'Standard Purchase', 'Walk-in Customer', '2025-01-11', '01:22:24', 0, 'Global Mart', 2600.00, 0.00, 7400.00, -2400.00, 0.00, 0.00, 1, 'Cash', 0),
(135, 'Standard Purchase', 'Walk-in Customer', '2025-01-11', '01:25:03', 0, 'Global Mart', 2600.00, 0.00, 7400.00, -2400.00, 0.00, 0.00, 1, 'Cash', 0),
(136, 'Standard Purchase', 'Walk-in Customer', '2025-01-11', '01:31:34', 0, 'Global Mart', 2600.00, 0.00, 7400.00, -2400.00, 0.00, 0.00, 1, 'Cash', 0),
(137, 'Standard Purchase', 'Walk-in Customer', '2025-01-11', '01:36:03', 0, 'Global Mart', 200.00, 0.00, 800.00, -300.00, 0.00, 0.00, 1, 'Cash', 0),
(138, 'Standard Purchase', 'Walk-in Customer', '2025-01-11', '01:37:22', 0, 'Global Mart', 200.00, 0.00, 800.00, -300.00, 0.00, 0.00, 1, 'Cash', 0);

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
  `product_type` varchar(20) NOT NULL DEFAULT 'standard',
  `description` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_sinhala_ci DEFAULT NULL,
  `del_rate` decimal(8,2) DEFAULT NULL,
  `del_cost` decimal(8,2) DEFAULT NULL,
  `del_profit` double(10,2) DEFAULT NULL,
  `has_stock` text NOT NULL DEFAULT '1',
  `stock_alert_limit` int(4) NOT NULL DEFAULT 20,
  `image` varchar(100) DEFAULT NULL,
  `show_in_landing_page` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'For E-commerce website visibility only',
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `barcode_symbology` varchar(20) DEFAULT 'CODE128',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `sku` varchar(30) DEFAULT NULL,
  `active_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1: Active, 0: Inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_type`, `description`, `del_rate`, `del_cost`, `del_profit`, `has_stock`, `stock_alert_limit`, `image`, `show_in_landing_page`, `category_id`, `brand_id`, `barcode`, `barcode_symbology`, `created_at`, `updated_at`, `sku`, `active_status`) VALUES
(502, 'Full Cream Milk', 'standard', '1 liter of full cream milk.', 200.00, 150.00, 50.00, '1', 10, NULL, 0, 1, 1, '9786249993402', 'CODE128', '2024-10-27 18:49:53', '2025-01-10 19:25:18', NULL, 1),
(503, 'Potato Chips', 'standard', 'Crispy potato chips.', 80.00, 50.00, 30.00, '1', 20, NULL, 0, 2, 2, '3601660000750', 'CODE128', '2024-10-27 18:49:53', '2025-01-10 19:25:15', NULL, 1),
(504, 'Cola Soft Drink2', 'standard', '330ml can of cola.', 75.00, 30.00, 20.00, '1', 15, NULL, 0, 1, 1, '97862420407766', 'CODE128', '2024-10-27 18:49:53', '2025-04-03 14:26:52', 'aaa', 1),
(505, 'Cola Hot Drink', 'standard', NULL, 50.00, 20.00, 100.00, '1', 20, NULL, 0, 1, 2, '6294015142945', 'CODE128', '2024-10-28 17:04:16', '2025-04-02 07:46:39', 'testp001', 1),
(506, 'Orrepaste', 'standard', NULL, 470.00, 300.00, 200.00, '1', 20, NULL, 0, 1, 3, '9556258002606', 'CODE128', '2024-10-29 09:04:41', '2025-01-10 19:25:14', 'orrpaste001', 1),
(507, 'test', 'standard', NULL, NULL, NULL, NULL, '1', 20, NULL, 0, 2, 2, '9789556812299', 'CODE128', '2024-12-26 19:19:06', '2024-12-26 19:19:06', 'test', 1),
(508, 'asdsad', 'standard', NULL, NULL, NULL, NULL, '1', 20, '676f58de030ec_317615432_1305125113675009_4776231069856830763_n - Copy.jpg', 0, NULL, NULL, '9786245572762', 'CODE128', '2024-12-28 01:48:17', '2025-04-03 10:50:02', 'sada', 1),
(510, 'vari check test product', 'standard', NULL, NULL, NULL, NULL, '1', 20, NULL, 0, 3, 1, 'PROD-404319', 'CODE128', '2024-12-28 04:16:12', '2024-12-28 04:16:12', 'adasdadFFF', 1),
(511, 'vari2 check test product', 'standard', NULL, NULL, NULL, NULL, '1', 20, NULL, 0, 1, 2, '9789556812212', 'CODE128', '2025-01-04 17:28:40', '2025-01-04 17:28:40', 'adasdadFFFvf', 1),
(513, 'combo product test 1', 'combo', NULL, NULL, NULL, NULL, '1', 20, NULL, 0, 2, 2, '9789556814512', 'CODE128', '2025-01-09 20:14:09', '2025-01-09 20:14:09', 'adasdadFFFssvf', 1),
(514, 'combo product test 2', 'combo', NULL, NULL, NULL, NULL, '1', 20, NULL, 0, 2, 3, '9789556834512', 'CODE128', '2025-01-10 11:38:09', '2025-04-03 07:25:32', 'adasdadFhFFvf', 1),
(516, 'discount_price_check', 'standard', NULL, NULL, NULL, NULL, '1', 20, NULL, 0, 2, 1, 'PROD-146418', 'CODE128', '2025-03-19 15:24:27', '2025-04-03 07:10:58', 'adassddadFFFvf', 1),
(517, 'discount_price_check_batch', 'standard', NULL, NULL, NULL, NULL, '1', 20, NULL, 0, 1, 1, 'PROD-416354', 'CODE128', '2025-03-19 16:09:40', '2025-03-19 16:09:40', 'adassddadFdFFvf', 1);

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
  `quantity` int(11) NOT NULL DEFAULT 0,
  `alert_quantity` int(11) DEFAULT 5,
  `supplier_id` int(11) DEFAULT NULL,
  `purchase_date` date NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','expired','discontinued') DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `restocked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `discount_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_batch`
--

INSERT INTO `product_batch` (`batch_id`, `product_id`, `batch_number`, `cost`, `selling_price`, `expiry_date`, `quantity`, `alert_quantity`, `supplier_id`, `purchase_date`, `status`, `notes`, `created_at`, `updated_at`, `restocked_at`, `discount_price`) VALUES
(5, 502, 'BATCH-001x', 120.00, 200.00, '2025-01-01', 17, 5, NULL, '2024-10-01', 'active', 'First batch of full cream milk.', '2024-10-27 18:55:35', '2025-03-19 15:10:37', '2025-01-10 17:35:05', 197.00),
(6, 503, 'BATCH-001z', 40.00, 80.00, NULL, 83, 5, NULL, '2024-10-01', 'active', 'First batch of potato chips.', '2024-10-27 18:55:35', '2025-01-10 19:18:28', '2025-01-10 17:15:02', NULL),
(7, 503, 'BATCH-002d', 50.00, 100.00, NULL, 100, 5, NULL, '2024-10-01', 'active', 'Second batch of potato chips.', '2024-10-27 18:55:35', '2025-03-19 13:37:40', '2025-01-10 17:15:04', 99.00),
(8, 504, 'BATCH-001dYY', 25.00, 50.00, NULL, 57, 5, NULL, '2024-10-01', 'active', '231adsaz', '2024-10-27 18:55:35', '2025-04-03 14:23:21', '2025-01-10 17:15:06', 90.00),
(9, 502, 'BATCH-001f', 120.00, 200.00, '2025-01-01', 24, 5, NULL, '2024-10-01', 'active', 'First batch of full cream milk.', '2024-10-27 18:59:19', '2025-01-10 20:07:23', '2025-01-10 17:15:09', NULL),
(10, 503, 'BATCH-001g', 40.00, 80.00, NULL, 82, 5, NULL, '2024-10-01', 'active', 'First batch of potato chips.', '2024-10-27 18:59:19', '2025-01-10 17:15:10', '2025-01-10 17:15:10', NULL),
(11, 503, 'BATCH-002h', 50.00, 100.00, NULL, 100, 5, NULL, '2024-10-01', 'active', 'Second batch of potato chips.', '2024-10-27 18:59:19', '2025-01-10 17:15:12', '2025-01-10 17:15:12', NULL),
(13, 504, 'BATCH-003j XX', 50.00, 1022.00, NULL, 75, 5, NULL, '2024-10-01', 'active', '123nbbx', '2024-10-27 18:59:19', '2025-04-03 14:23:20', '2025-01-10 17:15:15', 101.00),
(14, 506, '04213402', 300.00, 500.00, '2027-03-02', 5, 5, NULL, '2024-10-29', 'active', NULL, '2024-10-29 09:08:36', '2025-04-05 00:12:37', '2025-01-10 17:35:05', 450.00),
(15, 507, 'BATCH-20241226-aabdb', 50.00, 150.00, NULL, 17, 5, NULL, '2024-12-27', 'active', NULL, '2024-12-26 19:19:07', '2025-01-10 16:54:55', '2025-01-10 16:54:55', NULL),
(16, 508, 'BATCH-20241228-b14b0', 232.00, 330.00, NULL, 200, 10, NULL, '2024-12-28', 'active', NULL, '2024-12-28 01:48:18', '2025-04-03 10:52:54', '2025-01-04 17:02:22', NULL),
(17, 510, 'test vari 1', 60.00, 70.00, NULL, 49, 5, NULL, '2024-12-28', 'active', 'no', '2024-12-28 04:16:13', '2025-01-10 16:54:56', '2025-01-10 16:54:56', NULL),
(18, 510, 'test vari 2', 160.00, 170.00, NULL, 150, 5, NULL, '2024-12-28', 'active', 'බද', '2024-12-28 04:16:13', '2025-01-04 16:56:25', '2025-01-04 17:02:22', NULL),
(19, 511, 'test vari 21', 40.00, 55.00, NULL, 29, 5, NULL, '2025-01-04', 'active', 'no', '2025-01-04 17:28:41', '2025-01-10 16:54:57', '2025-01-10 16:54:57', NULL),
(20, 511, 'test vari 22', 60.00, 70.00, NULL, 53, 5, NULL, '2025-01-04', 'active', 'yes', '2025-01-04 17:28:43', '2025-01-10 19:00:31', '2025-01-04 17:28:43', NULL),
(21, 513, 'BATCH-20250109-d4a28', 1860.00, 200.00, NULL, 0, 5, NULL, '2025-01-10', 'active', NULL, '2025-01-09 20:14:10', '2025-01-09 20:14:10', '2025-01-09 20:14:10', NULL),
(22, 514, 'vari 1 comb', 20.00, 30.00, NULL, 0, 5, NULL, '2025-01-10', 'active', 'egg', '2025-01-10 11:38:09', '2025-01-10 18:13:03', '2025-01-10 11:38:09', NULL),
(23, 514, 'vari 2 comb', 25.00, 35.00, NULL, 0, 5, NULL, '2025-01-10', 'active', 'ea', '2025-01-10 11:38:10', '2025-01-10 19:13:51', '2025-01-10 11:38:10', NULL),
(25, 516, 'BATCH-20250319-38dd6', 30.00, 50.00, NULL, 20, 5, NULL, '2025-03-19', 'active', NULL, '2025-03-19 15:24:28', '2025-03-19 15:24:28', '2025-03-19 15:24:28', 45.00),
(26, 517, 'check 1', 10.00, 20.00, NULL, 0, 5, NULL, '2025-03-19', 'active', NULL, '2025-03-19 16:09:41', '2025-03-19 16:09:41', '2025-03-19 16:09:41', 15.00),
(27, 517, 'check 2', 50.00, 95.00, NULL, 0, 5, NULL, '2025-03-19', 'active', NULL, '2025-03-19 16:09:41', '2025-03-19 16:09:41', '2025-03-19 16:09:41', 90.00);

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
,`show_in_landing_page` tinyint(1)
,`category_id` int(11)
,`brand_id` int(11)
,`barcode` varchar(50)
,`created_at` timestamp
,`updated_at` timestamp
,`sku` varchar(30)
,`active_status` tinyint(1)
,`stock_qty` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `po_id` int(11) NOT NULL,
  `po_number` varchar(20) NOT NULL,
  `supplier_id` int(4) NOT NULL,
  `order_date` date NOT NULL DEFAULT current_timestamp(),
  `delivery_date` date DEFAULT NULL,
  `shipping_fee` decimal(10,2) DEFAULT 0.00,
  `discount_type` enum('percentage','fixed') DEFAULT 'fixed',
  `discount_value` decimal(10,2) DEFAULT 0.00,
  `tax_type` enum('percentage','fixed') DEFAULT 'percentage',
  `tax_value` decimal(10,2) DEFAULT 0.00,
  `status` enum('draft','pending','approved','ordered','received','cancelled') DEFAULT 'draft',
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`po_id`, `po_number`, `supplier_id`, `order_date`, `delivery_date`, `shipping_fee`, `discount_type`, `discount_value`, `tax_type`, `tax_value`, `status`, `subtotal`, `total_discount`, `total_tax`, `total_amount`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(11, 'PO-2024001', 1, '2025-11-18', '2024-01-15', 500.00, 'percentage', 5.00, 'percentage', 12.00, 'received', 10000.00, 500.00, 1140.00, 11140.00, 'Regular monthly order', 17, '2025-01-12 11:22:43', '2025-01-12 11:47:33'),
(12, 'PO-2024002', 2, '2025-12-08', '2024-01-17', 750.00, 'fixed', 1000.00, 'percentage', 12.00, 'cancelled', 20000.00, 1000.00, 2280.00, 22030.00, 'Bulk order for new products', 17, '2025-01-12 11:22:43', '2025-01-12 11:47:22'),
(13, 'PO-2024003', 1, '2025-03-11', '2024-01-20', 300.00, 'percentage', 3.00, 'percentage', 12.00, 'approved', 5000.00, 150.00, 582.00, 5732.00, 'Emergency stock replenishment', 17, '2025-01-12 11:22:43', '2025-04-02 07:28:21'),
(14, 'PO-2024004', 4, '2025-09-16', '2024-01-23', 600.00, 'fixed', 500.00, 'percentage', 12.00, 'received', 15000.00, 500.00, 1740.00, 16840.00, 'Seasonal products order', 0, '2025-01-12 11:22:43', '2025-04-02 07:31:02'),
(15, 'PO-2024005', 2, '2025-01-15', '2024-01-25', 450.00, 'percentage', 4.00, 'percentage', 12.00, 'received', 8000.00, 320.00, 921.60, 9051.60, 'Monthly inventory refresh', 0, '2025-01-12 11:22:43', '2025-04-02 07:35:15');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `po_item_id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `product_id` int(10) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_cost` decimal(10,2) NOT NULL,
  `total_cost` decimal(15,2) NOT NULL,
  `received_qty` int(11) DEFAULT 0,
  `status` enum('pending','approved','rejected','received') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`po_item_id`, `po_id`, `product_id`, `quantity`, `unit_cost`, `total_cost`, `received_qty`, `status`) VALUES
(46, 11, 502, 100, 25.00, 2500.00, 100, 'received'),
(47, 11, 503, 150, 30.00, 4500.00, 150, 'rejected'),
(48, 11, 505, 75, 40.00, 3000.00, 75, 'approved'),
(49, 12, 508, 200, 35.00, 7000.00, 0, 'received'),
(50, 12, 510, 300, 25.00, 7500.00, 0, 'rejected'),
(51, 12, 511, 220, 25.00, 5500.00, 0, 'rejected'),
(52, 13, 514, 50, 25.00, 1250.00, 30, 'received'),
(53, 13, 513, 75, 40.00, 3000.00, 50, 'received'),
(54, 13, 508, 25, 30.00, 750.00, 25, 'received'),
(55, 14, 508, 200, 30.00, 6000.00, 0, 'approved'),
(56, 14, 504, 150, 35.00, 5250.00, 0, 'approved'),
(57, 14, 502, 125, 30.00, 3750.00, 0, 'approved'),
(58, 15, 502, 100, 30.00, 3000.00, 0, 'rejected'),
(59, 15, 505, 125, 35.00, 4375.00, 0, 'rejected'),
(60, 15, 506, 25, 25.00, 625.00, 0, 'rejected');

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
  `batch` varchar(50) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `qty` varchar(8) NOT NULL,
  `rate` decimal(8,2) NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `cost` double(10,2) NOT NULL,
  `profit` double(10,2) NOT NULL,
  `worker` varchar(30) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT 0.00,
  `individual_discount_mode` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sales_id`, `invoice_number`, `product`, `batch`, `description`, `qty`, `rate`, `amount`, `cost`, `profit`, `worker`, `discount_price`, `individual_discount_mode`) VALUES
(87, 78, 'Orrepaste', NULL, NULL, '4', 500.00, 2000.00, 0.00, 2000.00, 'Global Mart', 0.00, 0),
(88, 79, 'Orrepaste', NULL, NULL, '1', 500.00, 500.00, 0.00, 500.00, 'Global Mart', 0.00, 0),
(89, 79, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(90, 79, 'Full Cream Milk', NULL, NULL, '6', 200.00, 1200.00, 0.00, 1200.00, 'Global Mart', 0.00, 0),
(91, 79, 'Cola Soft Drink', NULL, NULL, '1', 50.00, 50.00, 0.00, 50.00, 'Global Mart', 0.00, 0),
(92, 80, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(93, 81, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(94, 82, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(96, 84, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(97, 85, 'Potato Chips', NULL, NULL, '20', 80.00, 1600.00, 0.00, 1600.00, 'Global Mart', 0.00, 0),
(98, 85, 'Potato Chips', NULL, NULL, '1', 100.00, 100.00, 0.00, 100.00, 'Global Mart', 0.00, 0),
(99, 86, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(100, 87, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(101, 88, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(102, 89, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(103, 90, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(104, 91, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(105, 91, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(106, 92, 'Potato Chips', NULL, NULL, '1', 100.00, 100.00, 0.00, 100.00, 'Global Mart', 0.00, 0),
(107, 93, 'Potato Chips', NULL, NULL, '1', 100.00, 100.00, 0.00, 100.00, 'Global Mart', 0.00, 0),
(108, 94, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(109, 95, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(110, 96, 'Potato Chips', NULL, NULL, '1', 80.22, 80.22, 0.00, 80.22, 'Global Mart', 0.00, 0),
(111, 97, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(112, 98, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(113, 99, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(114, 100, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(115, 101, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(116, 102, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(117, 103, 'Potato Chips', NULL, NULL, '1', 100.00, 100.00, 0.00, 100.00, 'Global Mart', 0.00, 0),
(118, 104, 'Potato Chips', NULL, NULL, '1', 100.00, 100.00, 0.00, 100.00, 'Global Mart', 0.00, 0),
(119, 105, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(120, 106, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(121, 107, 'Potato Chips', NULL, NULL, '1', 100.00, 100.00, 0.00, 100.00, 'Global Mart', 0.00, 0),
(122, 108, 'Potato Chips', NULL, NULL, '1', 100.00, 100.00, 0.00, 100.00, 'Global Mart', 0.00, 0),
(123, 109, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(124, 110, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(125, 111, 'Potato Chips', NULL, NULL, '1', 80.00, 80.00, 0.00, 80.00, 'Global Mart', 0.00, 0),
(126, 113, 'vari check test product', NULL, NULL, '6', 70.00, 420.00, 0.00, 420.00, 'Global Mart', 0.00, 0),
(127, 113, 'vari2 check test product', NULL, NULL, '1', 55.00, 55.00, 0.00, 55.00, 'Global Mart', 0.00, 0),
(136, 120, 'combo product test 1', '21', NULL, '1', 200.00, 200.00, 0.00, 200.00, 'Global Mart', 0.00, 0),
(137, 120, 'combo product test 2', '23', NULL, '1', 35.00, 35.00, 0.00, 35.00, 'Global Mart', 0.00, 0),
(139, 122, 'combo product test 1', '21', NULL, '5', 200.00, 1000.00, 0.00, 1000.00, 'Global Mart', 0.00, 0),
(140, 123, 'combo product test 1', '21', NULL, '3', 200.00, 600.00, 0.00, 600.00, 'Global Mart', 0.00, 0),
(141, 124, 'Full Cream Milk', '5', NULL, '8', 200.00, 1600.00, 0.00, 1600.00, 'Global Mart', 0.00, 0),
(142, 124, 'Full Cream Milk', '9', NULL, '5', 200.00, 1000.00, 0.00, 1000.00, 'Global Mart', 0.00, 0),
(143, 125, 'Full Cream Milk', '5', NULL, '8', 200.00, 1600.00, 0.00, 1600.00, 'Global Mart', 0.00, 0),
(144, 125, 'Full Cream Milk', '9', NULL, '5', 200.00, 1000.00, 0.00, 1000.00, 'Global Mart', 0.00, 0),
(145, 126, 'Full Cream Milk', '5', NULL, '8', 200.00, 1600.00, 0.00, 1600.00, 'Global Mart', 0.00, 0),
(146, 126, 'Full Cream Milk', '9', NULL, '5', 200.00, 1000.00, 0.00, 1000.00, 'Global Mart', 0.00, 0),
(147, 127, 'Full Cream Milk', '5', NULL, '8', 200.00, 1600.00, 0.00, 1600.00, 'Global Mart', 0.00, 0),
(148, 127, 'Full Cream Milk', '9', NULL, '5', 200.00, 1000.00, 0.00, 1000.00, 'Global Mart', 0.00, 0),
(149, 128, 'Full Cream Milk', '5', NULL, '8', 200.00, 1600.00, 0.00, 1600.00, 'Global Mart', 0.00, 0),
(150, 128, 'Full Cream Milk', '9', NULL, '5', 200.00, 1000.00, 0.00, 1000.00, 'Global Mart', 0.00, 0),
(151, 129, 'Full Cream Milk', '5', NULL, '8', 200.00, 1600.00, 0.00, 1600.00, 'Global Mart', 0.00, 0),
(152, 129, 'Full Cream Milk', '9', NULL, '5', 200.00, 1000.00, 0.00, 1000.00, 'Global Mart', 0.00, 0),
(154, 131, 'Full Cream Milk', '5', NULL, '8', 200.00, 1600.00, 0.00, 1600.00, 'Global Mart', 0.00, 0),
(155, 131, 'Full Cream Milk', '9', NULL, '5', 200.00, 1000.00, 0.00, 1000.00, 'Global Mart', 0.00, 0),
(156, 132, 'Full Cream Milk', '5', NULL, '8', 200.00, 1600.00, 0.00, 1600.00, 'Global Mart', 0.00, 0),
(157, 132, 'Full Cream Milk', '9', NULL, '5', 200.00, 1000.00, 0.00, 1000.00, 'Global Mart', 0.00, 0),
(158, 133, 'Full Cream Milk', '5', NULL, '8', 200.00, 1600.00, 0.00, 1600.00, 'Global Mart', 0.00, 0),
(159, 133, 'Full Cream Milk', '9', NULL, '5', 200.00, 1000.00, 0.00, 1000.00, 'Global Mart', 0.00, 0),
(160, 134, 'Full Cream Milk', '5', NULL, '8', 200.00, 1600.00, 0.00, 1600.00, 'Global Mart', 0.00, 0),
(161, 134, 'Full Cream Milk', '9', NULL, '5', 200.00, 1000.00, 0.00, 1000.00, 'Global Mart', 0.00, 0),
(162, 135, 'Full Cream Milk', '5', NULL, '8', 200.00, 1600.00, 0.00, 1600.00, 'Global Mart', 0.00, 0),
(163, 135, 'Full Cream Milk', '9', NULL, '5', 200.00, 1000.00, 0.00, 1000.00, 'Global Mart', 0.00, 0),
(164, 136, 'Full Cream Milk', '5', NULL, '8', 200.00, 1600.00, 0.00, 1600.00, 'Global Mart', 0.00, 0),
(165, 136, 'Full Cream Milk', '9', NULL, '5', 200.00, 1000.00, 0.00, 1000.00, 'Global Mart', 0.00, 0),
(166, 137, 'Full Cream Milk', '5', NULL, '1', 200.00, 200.00, 0.00, 200.00, 'Global Mart', 0.00, 0),
(167, 138, 'combo product test 1', '21', NULL, '1', 200.00, 200.00, 0.00, 200.00, 'Global Mart', 0.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `sequences`
--

CREATE TABLE `sequences` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `prefix` varchar(10) NOT NULL,
  `next_value` int(11) NOT NULL DEFAULT 1,
  `padding` int(11) NOT NULL DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sequences`
--

INSERT INTO `sequences` (`id`, `name`, `prefix`, `next_value`, `padding`) VALUES
(1, 'purchase_order', 'PO', 1, 5),
(2, 'goods_receipt', 'GRN', 1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(4) NOT NULL,
  `supplier_name` varchar(60) NOT NULL,
  `supplier_tel` varchar(15) NOT NULL DEFAULT '0',
  `supplier_address` varchar(60) NOT NULL,
  `credit_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `note` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `supplier_tel`, `supplier_address`, `credit_balance`, `note`, `created_at`, `updated_at`) VALUES
(1, 'test supplier 1', '714730', 'ATT', -200.00, 'test 1', '2025-01-08 18:59:12', '2025-01-08 20:12:46'),
(2, 'test supplier 2', '55664', 'Colombo', 800.00, 'test 2', '2025-01-08 18:59:12', '2025-01-08 18:59:12'),
(4, 'test supplier 3', '0123456789', 'Korathota, Kaduwela', 0.00, 'asd', '2025-01-08 19:03:03', '2025-01-08 19:04:25');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_payments`
--

CREATE TABLE `supplier_payments` (
  `payment_id` int(11) NOT NULL,
  `supplier_id` int(4) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `amount` decimal(10,2) NOT NULL,
  `method` enum('cash','bank_transfer','cheque','credit_card') NOT NULL DEFAULT 'cash',
  `reference_no` varchar(50) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `po_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier_payments`
--

INSERT INTO `supplier_payments` (`payment_id`, `supplier_id`, `date`, `amount`, `method`, `reference_no`, `note`, `created_at`, `created_by`, `po_id`) VALUES
(1, 1, '2025-01-09', 100.00, 'cash', 'et', 'test', '2025-01-08 19:00:58', 0, 12),
(2, 1, '2025-01-09', 200.00, 'cheque', 'et', '20', '2025-01-08 20:12:46', 0, 13);

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
(182, 'ADD_PAYMENT', 'Payment added for supplier ID 1', 200, '2025-01-09', '01:42:47', 0);

-- --------------------------------------------------------

--
-- Structure for view `product_view`
--
DROP TABLE IF EXISTS `product_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`globalmart_user`@`%` SQL SECURITY DEFINER VIEW `product_view`  AS SELECT `p`.`product_id` AS `product_id`, `p`.`product_name` AS `product_name`, `p`.`description` AS `description`, `pb`.`selling_price` AS `rate`, `pb`.`cost` AS `cost`, `pb`.`profit` AS `profit`, `p`.`has_stock` AS `has_stock`, `p`.`stock_alert_limit` AS `stock_alert_limit`, `p`.`image` AS `image`, `p`.`show_in_landing_page` AS `show_in_landing_page`, `p`.`category_id` AS `category_id`, `p`.`brand_id` AS `brand_id`, `p`.`barcode` AS `barcode`, `p`.`created_at` AS `created_at`, `p`.`updated_at` AS `updated_at`, `p`.`sku` AS `sku`, `p`.`active_status` AS `active_status`, coalesce(sum(`pb`.`quantity`),0) AS `stock_qty` FROM (`products` `p` left join `product_batch` `pb` on(`p`.`product_id` = `pb`.`product_id`)) GROUP BY `p`.`product_id` ;

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
-- Indexes for table `barcode_print_items`
--
ALTER TABLE `barcode_print_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexes for table `barcode_print_jobs`
--
ALTER TABLE `barcode_print_jobs`
  ADD PRIMARY KEY (`job_id`),
  ADD KEY `template_id` (`template_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `barcode_templates`
--
ALTER TABLE `barcode_templates`
  ADD PRIMARY KEY (`template_id`);

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
-- Indexes for table `combo_products`
--
ALTER TABLE `combo_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_combo_product_id` (`combo_product_id`),
  ADD KEY `idx_component_product_id` (`component_product_id`);

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
-- Indexes for table `goods_receipt_notes`
--
ALTER TABLE `goods_receipt_notes`
  ADD PRIMARY KEY (`grn_id`),
  ADD UNIQUE KEY `grn_number` (`grn_number`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_grn_status` (`status`),
  ADD KEY `idx_grn_date` (`receipt_date`);

--
-- Indexes for table `grn_items`
--
ALTER TABLE `grn_items`
  ADD PRIMARY KEY (`grn_item_id`),
  ADD KEY `grn_id` (`grn_id`),
  ADD KEY `po_item_id` (`po_item_id`),
  ADD KEY `batch_id` (`batch_id`);

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
  ADD KEY `idx_barcode` (`barcode`),
  ADD KEY `idx_product_search` (`product_name`,`barcode`,`sku`),
  ADD KEY `idx_brand_id` (`brand_id`),
  ADD KEY `idx_category_id` (`category_id`),
  ADD KEY `idx_product_active` (`active_status`);

--
-- Indexes for table `product_batch`
--
ALTER TABLE `product_batch`
  ADD PRIMARY KEY (`batch_id`),
  ADD UNIQUE KEY `batch_number` (`batch_number`),
  ADD UNIQUE KEY `unique_product_batch` (`product_id`,`batch_number`),
  ADD KEY `product_batch_ibfk_1` (`product_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_batch_created` (`product_id`,`created_at`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`po_id`),
  ADD UNIQUE KEY `po_number` (`po_number`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_po_status` (`status`),
  ADD KEY `idx_po_date` (`order_date`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`po_item_id`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_product_id` (`product_id`);

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
-- Indexes for table `sequences`
--
ALTER TABLE `sequences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`),
  ADD UNIQUE KEY `unique supplier name` (`supplier_name`);

--
-- Indexes for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `po_id` (`po_id`);

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
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1175;

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
-- AUTO_INCREMENT for table `barcode_print_items`
--
ALTER TABLE `barcode_print_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `barcode_print_jobs`
--
ALTER TABLE `barcode_print_jobs`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `barcode_templates`
--
ALTER TABLE `barcode_templates`
  MODIFY `template_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `combo_products`
--
ALTER TABLE `combo_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  MODIFY `error_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `goods_receipt_notes`
--
ALTER TABLE `goods_receipt_notes`
  MODIFY `grn_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grn_items`
--
ALTER TABLE `grn_items`
  MODIFY `grn_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `held_invoices`
--
ALTER TABLE `held_invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_number` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

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
  MODIFY `product_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=529;

--
-- AUTO_INCREMENT for table `product_batch`
--
ALTER TABLE `product_batch`
  MODIFY `batch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `po_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `po_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `salary`
--
ALTER TABLE `salary`
  MODIFY `salary_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sales_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- AUTO_INCREMENT for table `sequences`
--
ALTER TABLE `sequences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transaction_log`
--
ALTER TABLE `transaction_log`
  MODIFY `transaction_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

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
-- Constraints for table `barcode_print_items`
--
ALTER TABLE `barcode_print_items`
  ADD CONSTRAINT `barcode_print_items_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `barcode_print_jobs` (`job_id`),
  ADD CONSTRAINT `barcode_print_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `barcode_print_items_ibfk_3` FOREIGN KEY (`batch_id`) REFERENCES `product_batch` (`batch_id`);

--
-- Constraints for table `barcode_print_jobs`
--
ALTER TABLE `barcode_print_jobs`
  ADD CONSTRAINT `barcode_print_jobs_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `barcode_templates` (`template_id`),
  ADD CONSTRAINT `barcode_print_jobs_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employ_id`);

--
-- Constraints for table `combo_products`
--
ALTER TABLE `combo_products`
  ADD CONSTRAINT `combo_products_ibfk_1` FOREIGN KEY (`combo_product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `combo_products_ibfk_2` FOREIGN KEY (`component_product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `goods_receipt_notes`
--
ALTER TABLE `goods_receipt_notes`
  ADD CONSTRAINT `grn_employee_fk` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employ_id`),
  ADD CONSTRAINT `grn_po_fk` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`);

--
-- Constraints for table `grn_items`
--
ALTER TABLE `grn_items`
  ADD CONSTRAINT `grni_batch_fk` FOREIGN KEY (`batch_id`) REFERENCES `product_batch` (`batch_id`),
  ADD CONSTRAINT `grni_grn_fk` FOREIGN KEY (`grn_id`) REFERENCES `goods_receipt_notes` (`grn_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grni_poi_fk` FOREIGN KEY (`po_item_id`) REFERENCES `purchase_order_items` (`po_item_id`);

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
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `po_employee_fk` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employ_id`),
  ADD CONSTRAINT `po_supplier_fk` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`);

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `poi_po_fk` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `poi_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

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
-- Constraints for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD CONSTRAINT `supplier_payments_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_payments_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employ_id`),
  ADD CONSTRAINT `supplier_payments_ibfk_3` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_log`
--
ALTER TABLE `transaction_log`
  ADD CONSTRAINT `transaction_log_ibfk_1` FOREIGN KEY (`employ_id`) REFERENCES `employees` (`employ_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

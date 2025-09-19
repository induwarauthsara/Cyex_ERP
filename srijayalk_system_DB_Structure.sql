-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- දායකයා: localhost:3306
-- උත්පාදන වේලාව: සැප්තැම්බර් 19, 2025 දින 10:12 AM ට
-- සේවාදායකයේ අනුවාදය: 10.6.23-MariaDB-cll-lve
-- PHP අනුවාදය: 8.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- දත්තගබඩාව: `srijayapos_new`
--

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `account_name` varchar(30) NOT NULL,
  `account_type` varchar(20) NOT NULL DEFAULT 'cash',
  `amount` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `account_transactions`
--

CREATE TABLE `account_transactions` (
  `id` int(11) NOT NULL,
  `account_name` varchar(50) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `type` enum('credit','debit') NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `reference` varchar(50) DEFAULT NULL,
  `transaction_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `action_log`
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
-- වගුවක් සඳහා වගු සැකිල්ල `attendance`
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
-- වගුවක් සඳහා වගු සැකිල්ල `bank_deposits`
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
-- වගුවක් සඳහා වගු සැකිල්ල `barcode_print_items`
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
-- වගුවක් සඳහා වගු සැකිල්ල `barcode_print_jobs`
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
-- වගුවක් සඳහා වගු සැකිල්ල `barcode_templates`
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

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `brands`
--

CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL,
  `brand_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `cash_register`
--

CREATE TABLE `cash_register` (
  `id` int(11) NOT NULL,
  `opening_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bank_deposit` decimal(10,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `closing_notes` text DEFAULT NULL,
  `opened_at` datetime NOT NULL,
  `closed_at` datetime DEFAULT NULL,
  `cash_out` decimal(10,2) DEFAULT 0.00,
  `cash_drawer_balance` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `combo_products`
--

CREATE TABLE `combo_products` (
  `id` int(11) NOT NULL,
  `combo_product_id` int(11) DEFAULT NULL,
  `component_product_id` int(11) DEFAULT NULL,
  `quantity` decimal(10,3) DEFAULT NULL,
  `batch_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `customers`
--

CREATE TABLE `customers` (
  `id` int(3) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `customer_type` varchar(10) DEFAULT 'regular',
  `customer_mobile` char(10) NOT NULL DEFAULT '0',
  `customer_extra_fund` decimal(9,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `employees`
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

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `error_log`
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
-- වගුවක් සඳහා වගු සැකිල්ල `fund_transactions`
--

CREATE TABLE `fund_transactions` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `type` enum('addition','deduction') NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `transaction_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `goods_receipt_notes`
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
-- වගුවක් සඳහා වගු සැකිල්ල `grn_items`
--

CREATE TABLE `grn_items` (
  `grn_item_id` int(11) NOT NULL,
  `grn_id` int(11) NOT NULL,
  `po_item_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `received_qty` decimal(6,3) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `held_invoices`
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

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `invoice`
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
  `individual_discount_mode` tinyint(1) DEFAULT 0,
  `credit_payment` tinyint(1) NOT NULL DEFAULT 0,
  `customer_id` int(2) DEFAULT 0,
  `primary_worker` varchar(10) NOT NULL DEFAULT '0',
  `amount_received` decimal(15,2) DEFAULT NULL,
  `cash_change` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `InvoiceBalPayRecords`
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
-- වගුවක් සඳහා වගු සැකිල්ල `items`
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
-- වගුවක් සඳහා වගු සැකිල්ල `oneTimeProducts_sales`
--

CREATE TABLE `oneTimeProducts_sales` (
  `oneTimeProduct_id` int(11) NOT NULL,
  `invoice_number` int(10) NOT NULL,
  `product` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `qty` decimal(6,3) NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `profit` decimal(10,2) DEFAULT NULL,
  `status` enum('uncleared','skip','cleared') NOT NULL DEFAULT 'uncleared',
  `worker` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `regular_price` decimal(10,2) DEFAULT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `supplier` varchar(50) DEFAULT NULL,
  `account_name` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `payment_details`
--

CREATE TABLE `payment_details` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `cash_amount` decimal(10,2) DEFAULT 0.00,
  `card_amount` decimal(10,2) DEFAULT 0.00,
  `bank_amount` decimal(10,2) DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT 'Cash',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `pettycash`
--

CREATE TABLE `pettycash` (
  `id` int(4) NOT NULL,
  `perrycash` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `time` time NOT NULL DEFAULT current_timestamp(),
  `emp_name` varchar(30) NOT NULL,
  `register_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `printer_counter_bank_deposit`
--

CREATE TABLE `printer_counter_bank_deposit` (
  `deposit_ID` int(10) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `amount` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `printer_counter_count`
--

CREATE TABLE `printer_counter_count` (
  `countID` int(2) NOT NULL,
  `typeID` int(2) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `time` time NOT NULL DEFAULT current_timestamp(),
  `count` int(10) NOT NULL,
  `cost` int(10) NOT NULL,
  `printerFinalCount` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `printer_counter_printers`
--

CREATE TABLE `printer_counter_printers` (
  `printerID` int(2) NOT NULL,
  `printerName` varchar(30) NOT NULL,
  `ipAddress` varchar(100) NOT NULL,
  `typeCount` int(2) NOT NULL,
  `rent_limit` int(5) NOT NULL DEFAULT 0,
  `active` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `printer_counter_statistics`
--

CREATE TABLE `printer_counter_statistics` (
  `stat_id` int(3) NOT NULL,
  `name` varchar(50) NOT NULL,
  `amount` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `printer_counter_types`
--

CREATE TABLE `printer_counter_types` (
  `typeID` int(2) NOT NULL,
  `printerID` int(2) NOT NULL,
  `typeName` varchar(50) NOT NULL,
  `cost` int(2) NOT NULL,
  `totalCount` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `products`
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

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `product_batch`
--

CREATE TABLE `product_batch` (
  `batch_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `batch_number` varchar(50) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `profit` decimal(10,2) GENERATED ALWAYS AS (`selling_price` - `cost`) STORED,
  `expiry_date` date DEFAULT NULL,
  `quantity` decimal(9,3) NOT NULL DEFAULT 0.000,
  `alert_quantity` decimal(9,3) DEFAULT 5.000,
  `supplier_id` int(11) DEFAULT NULL,
  `purchase_date` date NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','expired','discontinued') DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `restocked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `discount_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- දසුන සැදීම සඳහා තාවකාලික සැකිල්ල `product_view`
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
,`barcode_symbology` varchar(20)
,`created_at` timestamp
,`updated_at` timestamp
,`sku` varchar(30)
,`active_status` tinyint(1)
,`stock_qty` decimal(31,3)
);

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `purchase_orders`
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

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `po_item_id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `product_id` int(10) NOT NULL,
  `quantity` decimal(6,3) NOT NULL,
  `unit_cost` decimal(10,2) NOT NULL,
  `total_cost` decimal(15,2) NOT NULL,
  `received_qty` int(11) DEFAULT 0,
  `status` enum('pending','approved','rejected','received') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `salary`
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
-- වගුවක් සඳහා වගු සැකිල්ල `sales`
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
  `individual_discount_mode` tinyint(1) DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `sales_returns`
--

CREATE TABLE `sales_returns` (
  `return_id` int(11) NOT NULL,
  `invoice_id` varchar(20) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `return_date` datetime DEFAULT current_timestamp(),
  `return_amount` decimal(10,2) NOT NULL,
  `refund_method` enum('Cash','Store Credit') NOT NULL,
  `return_reason` varchar(100) NOT NULL,
  `return_note` text DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `sales_return_items`
--

CREATE TABLE `sales_return_items` (
  `return_item_id` int(11) NOT NULL,
  `return_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `quantity_returned` decimal(10,3) NOT NULL,
  `return_price` decimal(10,2) NOT NULL,
  `add_to_stock` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `sequences`
--

CREATE TABLE `sequences` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `prefix` varchar(10) NOT NULL,
  `next_value` int(11) NOT NULL DEFAULT 1,
  `padding` int(11) NOT NULL DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `settings`
--

CREATE TABLE `settings` (
  `setting_name` varchar(50) NOT NULL,
  `setting_description` text NOT NULL,
  `setting_value` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `suppliers`
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

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `supplier_payments`
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

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `todo`
--

CREATE TABLE `todo` (
  `todo_id` int(10) NOT NULL,
  `invoice_number` int(10) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `submision_time` datetime NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- වගුවක් සඳහා වගු සැකිල්ල `transaction_log`
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
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `account_transactions`
--
ALTER TABLE `account_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_name` (`account_name`),
  ADD KEY `transaction_date` (`transaction_date`);

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
-- Indexes for table `cash_register`
--
ALTER TABLE `cash_register`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `fund_transactions`
--
ALTER TABLE `fund_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `transaction_date` (`transaction_date`);

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
-- Indexes for table `oneTimeProducts_sales`
--
ALTER TABLE `oneTimeProducts_sales`
  ADD PRIMARY KEY (`oneTimeProduct_id`);

--
-- Indexes for table `payment_details`
--
ALTER TABLE `payment_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pettycash`
--
ALTER TABLE `pettycash`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pettycash_register` (`register_id`);

--
-- Indexes for table `printer_counter_bank_deposit`
--
ALTER TABLE `printer_counter_bank_deposit`
  ADD PRIMARY KEY (`deposit_ID`);

--
-- Indexes for table `printer_counter_count`
--
ALTER TABLE `printer_counter_count`
  ADD PRIMARY KEY (`countID`),
  ADD KEY `count_ibfk_1` (`typeID`);

--
-- Indexes for table `printer_counter_printers`
--
ALTER TABLE `printer_counter_printers`
  ADD PRIMARY KEY (`printerID`);

--
-- Indexes for table `printer_counter_statistics`
--
ALTER TABLE `printer_counter_statistics`
  ADD PRIMARY KEY (`stat_id`);

--
-- Indexes for table `printer_counter_types`
--
ALTER TABLE `printer_counter_types`
  ADD UNIQUE KEY `typeID` (`typeID`),
  ADD KEY `printerID` (`printerID`);

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
-- Indexes for table `sales_returns`
--
ALTER TABLE `sales_returns`
  ADD PRIMARY KEY (`return_id`),
  ADD KEY `idx_returns_invoice` (`invoice_id`),
  ADD KEY `idx_returns_customer` (`customer_id`);

--
-- Indexes for table `sales_return_items`
--
ALTER TABLE `sales_return_items`
  ADD PRIMARY KEY (`return_item_id`),
  ADD KEY `idx_return_items_return` (`return_id`),
  ADD KEY `idx_return_items_product` (`product_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `account_transactions`
--
ALTER TABLE `account_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `action_log`
--
ALTER TABLE `action_log`
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_deposits`
--
ALTER TABLE `bank_deposits`
  MODIFY `bank_deposit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barcode_print_items`
--
ALTER TABLE `barcode_print_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barcode_print_jobs`
--
ALTER TABLE `barcode_print_jobs`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barcode_templates`
--
ALTER TABLE `barcode_templates`
  MODIFY `template_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cash_register`
--
ALTER TABLE `cash_register`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `combo_products`
--
ALTER TABLE `combo_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employ_id` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `error_log`
--
ALTER TABLE `error_log`
  MODIFY `error_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fund_transactions`
--
ALTER TABLE `fund_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_number` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `InvoiceBalPayRecords`
--
ALTER TABLE `InvoiceBalPayRecords`
  MODIFY `InvBalPayRecords_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `oneTimeProducts_sales`
--
ALTER TABLE `oneTimeProducts_sales`
  MODIFY `oneTimeProduct_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_details`
--
ALTER TABLE `payment_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pettycash`
--
ALTER TABLE `pettycash`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `printer_counter_bank_deposit`
--
ALTER TABLE `printer_counter_bank_deposit`
  MODIFY `deposit_ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `printer_counter_count`
--
ALTER TABLE `printer_counter_count`
  MODIFY `countID` int(2) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `printer_counter_printers`
--
ALTER TABLE `printer_counter_printers`
  MODIFY `printerID` int(2) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `printer_counter_statistics`
--
ALTER TABLE `printer_counter_statistics`
  MODIFY `stat_id` int(3) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `printer_counter_types`
--
ALTER TABLE `printer_counter_types`
  MODIFY `typeID` int(2) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_batch`
--
ALTER TABLE `product_batch`
  MODIFY `batch_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `po_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `po_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salary`
--
ALTER TABLE `salary`
  MODIFY `salary_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sales_id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_returns`
--
ALTER TABLE `sales_returns`
  MODIFY `return_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_return_items`
--
ALTER TABLE `sales_return_items`
  MODIFY `return_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sequences`
--
ALTER TABLE `sequences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `todo`
--
ALTER TABLE `todo`
  MODIFY `todo_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_log`
--
ALTER TABLE `transaction_log`
  MODIFY `transaction_id` int(10) NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- දසුන සඳහා සැකිල්ල `product_view`
--
DROP TABLE IF EXISTS `product_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `product_view`  AS SELECT `p`.`product_id` AS `product_id`, `p`.`product_name` AS `product_name`, `p`.`description` AS `description`, `pb`.`selling_price` AS `rate`, `pb`.`cost` AS `cost`, `pb`.`profit` AS `profit`, `p`.`has_stock` AS `has_stock`, `p`.`stock_alert_limit` AS `stock_alert_limit`, `p`.`image` AS `image`, `p`.`show_in_landing_page` AS `show_in_landing_page`, `p`.`category_id` AS `category_id`, `p`.`brand_id` AS `brand_id`, `p`.`barcode` AS `barcode`, `p`.`barcode_symbology` AS `barcode_symbology`, `p`.`created_at` AS `created_at`, `p`.`updated_at` AS `updated_at`, `p`.`sku` AS `sku`, `p`.`active_status` AS `active_status`, coalesce(sum(`pb`.`quantity`),0) AS `stock_qty` FROM (`products` `p` left join `product_batch` `pb` on(`p`.`product_id` = `pb`.`product_id`)) GROUP BY `p`.`product_id` ;

--
-- නික්ෂේපනය කරන ලද වගු සඳහා සීමා බාධක
--

--
-- වගුව සඳහා සීමා බාධක `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employ_id`);

--
-- වගුව සඳහා සීමා බාධක `bank_deposits`
--
ALTER TABLE `bank_deposits`
  ADD CONSTRAINT `bank_deposits_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employ_id`);

--
-- වගුව සඳහා සීමා බාධක `barcode_print_items`
--
ALTER TABLE `barcode_print_items`
  ADD CONSTRAINT `barcode_print_items_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `barcode_print_jobs` (`job_id`),
  ADD CONSTRAINT `barcode_print_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `barcode_print_items_ibfk_3` FOREIGN KEY (`batch_id`) REFERENCES `product_batch` (`batch_id`);

--
-- වගුව සඳහා සීමා බාධක `barcode_print_jobs`
--
ALTER TABLE `barcode_print_jobs`
  ADD CONSTRAINT `barcode_print_jobs_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `barcode_templates` (`template_id`),
  ADD CONSTRAINT `barcode_print_jobs_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employ_id`);

--
-- වගුව සඳහා සීමා බාධක `combo_products`
--
ALTER TABLE `combo_products`
  ADD CONSTRAINT `combo_products_ibfk_1` FOREIGN KEY (`combo_product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `combo_products_ibfk_2` FOREIGN KEY (`component_product_id`) REFERENCES `products` (`product_id`);

--
-- වගුව සඳහා සීමා බාධක `goods_receipt_notes`
--
ALTER TABLE `goods_receipt_notes`
  ADD CONSTRAINT `grn_employee_fk` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employ_id`),
  ADD CONSTRAINT `grn_po_fk` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`);

--
-- වගුව සඳහා සීමා බාධක `grn_items`
--
ALTER TABLE `grn_items`
  ADD CONSTRAINT `grni_batch_fk` FOREIGN KEY (`batch_id`) REFERENCES `product_batch` (`batch_id`),
  ADD CONSTRAINT `grni_grn_fk` FOREIGN KEY (`grn_id`) REFERENCES `goods_receipt_notes` (`grn_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grni_poi_fk` FOREIGN KEY (`po_item_id`) REFERENCES `purchase_order_items` (`po_item_id`);

--
-- වගුව සඳහා සීමා බාධක `pettycash`
--
ALTER TABLE `pettycash`
  ADD CONSTRAINT `fk_pettycash_register` FOREIGN KEY (`register_id`) REFERENCES `cash_register` (`id`) ON DELETE SET NULL;

--
-- වගුව සඳහා සීමා බාධක `printer_counter_count`
--
ALTER TABLE `printer_counter_count`
  ADD CONSTRAINT `count_ibfk_1` FOREIGN KEY (`typeID`) REFERENCES `printer_counter_types` (`typeID`);

--
-- වගුව සඳහා සීමා බාධක `printer_counter_types`
--
ALTER TABLE `printer_counter_types`
  ADD CONSTRAINT `types_ibfk_1` FOREIGN KEY (`printerID`) REFERENCES `printer_counter_printers` (`printerID`);

--
-- වගුව සඳහා සීමා බාධක `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`);

--
-- වගුව සඳහා සීමා බාධක `product_batch`
--
ALTER TABLE `product_batch`
  ADD CONSTRAINT `product_batch_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_batch_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`);

--
-- වගුව සඳහා සීමා බාධක `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `po_employee_fk` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employ_id`),
  ADD CONSTRAINT `po_supplier_fk` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`);

--
-- වගුව සඳහා සීමා බාධක `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `poi_po_fk` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `poi_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- වගුව සඳහා සීමා බාධක `salary`
--
ALTER TABLE `salary`
  ADD CONSTRAINT `salary_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`employ_id`);

--
-- වගුව සඳහා සීමා බාධක `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`invoice_number`) REFERENCES `invoice` (`invoice_number`),
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`invoice_number`) REFERENCES `invoice` (`invoice_number`);

--
-- වගුව සඳහා සීමා බාධක `sales_returns`
--
ALTER TABLE `sales_returns`
  ADD CONSTRAINT `sales_returns_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- වගුව සඳහා සීමා බාධක `sales_return_items`
--
ALTER TABLE `sales_return_items`
  ADD CONSTRAINT `sales_return_items_ibfk_1` FOREIGN KEY (`return_id`) REFERENCES `sales_returns` (`return_id`);

--
-- වගුව සඳහා සීමා බාධක `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD CONSTRAINT `supplier_payments_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_payments_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employ_id`),
  ADD CONSTRAINT `supplier_payments_ibfk_3` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- වගුව සඳහා සීමා බාධක `transaction_log`
--
ALTER TABLE `transaction_log`
  ADD CONSTRAINT `transaction_log_ibfk_1` FOREIGN KEY (`employ_id`) REFERENCES `employees` (`employ_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

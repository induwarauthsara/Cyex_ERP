-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 16, 2026 at 06:06 AM
-- Server version: 10.6.24-MariaDB-cll-lve
-- PHP Version: 8.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `srijayapos_new`
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

-- --------------------------------------------------------

--
-- Table structure for table `account_transactions`
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

-- --------------------------------------------------------

--
-- Table structure for table `brands`
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
-- Table structure for table `cash_register`
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
-- Table structure for table `categories`
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
-- Table structure for table `combo_products`
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
-- Table structure for table `customers`
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

-- --------------------------------------------------------

--
-- Table structure for table `employee_commission_history`
--

CREATE TABLE `employee_commission_history` (
  `commission_id` int(11) NOT NULL,
  `invoice_number` int(10) NOT NULL,
  `sales_id` int(5) DEFAULT NULL,
  `employee_id` int(4) NOT NULL,
  `product_id` int(10) DEFAULT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_profit` decimal(15,2) NOT NULL,
  `commission_percentage` decimal(5,2) NOT NULL,
  `commission_amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `expense_id` int(11) NOT NULL,
  `reference_no` varchar(50) DEFAULT NULL COMMENT 'Receipt/Invoice number',
  `title` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `amount_paid` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total amount paid so far',
  `category_id` int(11) NOT NULL,
  `expense_date` datetime NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(50) NOT NULL DEFAULT 'Cash',
  `status` enum('paid','partial','unpaid','overdue') NOT NULL DEFAULT 'unpaid',
  `recurring_ref_id` int(11) DEFAULT NULL,
  `attachment_url` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `color_code` varchar(7) DEFAULT '#808080',
  `icon` varchar(50) DEFAULT 'money-bill',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1: Active, 0: Inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_payments`
--

CREATE TABLE `expense_payments` (
  `payment_id` int(11) NOT NULL,
  `expense_id` int(11) NOT NULL,
  `payment_amount` decimal(15,2) NOT NULL,
  `payment_date` datetime NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(50) NOT NULL DEFAULT 'Cash',
  `reference_no` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `expense_payments`
--
DELIMITER $$
CREATE TRIGGER `update_expense_status_after_payment` AFTER INSERT ON `expense_payments` FOR EACH ROW BEGIN
    DECLARE total_paid DECIMAL(15,2);
    DECLARE expense_total DECIMAL(15,2);
    
    -- Get total amount paid
    SELECT COALESCE(SUM(payment_amount), 0) INTO total_paid
    FROM expense_payments
    WHERE expense_id = NEW.expense_id;
    
    -- Get expense total amount
    SELECT amount INTO expense_total
    FROM expenses
    WHERE expense_id = NEW.expense_id;
    
    -- Update amount_paid and status
    UPDATE expenses
    SET amount_paid = total_paid,
        status = CASE
            WHEN total_paid >= expense_total THEN 'paid'
            WHEN total_paid > 0 THEN 'partial'
            ELSE 'unpaid'
        END
    WHERE expense_id = NEW.expense_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `fund_transactions`
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
-- Table structure for table `goods_receipt_notes`
--

CREATE TABLE `goods_receipt_notes` (
  `grn_id` int(11) NOT NULL,
  `grn_number` varchar(20) NOT NULL,
  `po_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `receipt_date` date NOT NULL DEFAULT current_timestamp(),
  `invoice_number` varchar(50) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('draft','completed','cancelled') DEFAULT 'draft',
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `outstanding_amount` decimal(12,2) GENERATED ALWAYS AS (`total_amount` - `paid_amount`) STORED,
  `payment_status` enum('paid','partial','unpaid') NOT NULL DEFAULT 'unpaid',
  `payment_method` enum('cash','bank_transfer','cheque','credit_card','mixed') DEFAULT NULL,
  `payment_reference` varchar(100) DEFAULT NULL,
  `payment_notes` text DEFAULT NULL,
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
  `po_item_id` int(11) DEFAULT NULL,
  `batch_id` int(11) NOT NULL,
  `received_qty` decimal(13,3) NOT NULL,
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
  `individual_discount_mode` tinyint(1) DEFAULT 0,
  `credit_payment` tinyint(1) NOT NULL DEFAULT 0,
  `customer_id` int(2) DEFAULT 0,
  `primary_worker` varchar(10) NOT NULL DEFAULT '0',
  `amount_received` decimal(15,2) DEFAULT NULL,
  `cash_change` decimal(15,2) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1: Soft deleted, 0: Active',
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `invoice_audit_logs`
--

CREATE TABLE `invoice_audit_logs` (
  `id` int(11) NOT NULL,
  `invoice_number` int(10) NOT NULL COMMENT 'Linked Bill ID',
  `action_type` enum('EDIT','DELETE') NOT NULL,
  `old_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_payload`)),
  `new_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_payload`)),
  `stock_changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`stock_changes`)),
  `commission_changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`commission_changes`)),
  `reason` text NOT NULL,
  `restock_items` tinyint(1) NOT NULL DEFAULT 1,
  `user_id` int(4) NOT NULL,
  `user_name` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `oneTimeProducts_sales`
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
-- Table structure for table `payment_details`
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
-- Table structure for table `pettycash`
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
-- Table structure for table `printer_counter_bank_deposit`
--

CREATE TABLE `printer_counter_bank_deposit` (
  `deposit_ID` int(10) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `amount` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `printer_counter_count`
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
-- Table structure for table `printer_counter_printers`
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
-- Table structure for table `printer_counter_statistics`
--

CREATE TABLE `printer_counter_statistics` (
  `stat_id` int(3) NOT NULL,
  `name` varchar(50) NOT NULL,
  `amount` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `printer_counter_types`
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
  `active_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1: Active, 0: Inactive',
  `employee_commission_percentage` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Employee commission percentage from product profit (0-100)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
,`barcode_symbology` varchar(20)
,`created_at` timestamp
,`updated_at` timestamp
,`sku` varchar(30)
,`active_status` tinyint(1)
,`stock_qty` decimal(31,3)
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

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
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
-- Table structure for table `quotations`
--

CREATE TABLE `quotations` (
  `id` int(11) NOT NULL,
  `quotation_number` varchar(50) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_mobile` varchar(20) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `quotation_date` date NOT NULL,
  `valid_until` date DEFAULT NULL,
  `note` text DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','sent','accepted','rejected','expired') DEFAULT 'draft',
  `created_by` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotation_items`
--

CREATE TABLE `quotation_items` (
  `id` int(11) NOT NULL,
  `quotation_id` int(11) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` decimal(8,2) NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recurring_expenses`
--

CREATE TABLE `recurring_expenses` (
  `recurring_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `category_id` int(11) NOT NULL,
  `frequency` enum('daily','weekly','monthly','annually') NOT NULL,
  `start_date` date NOT NULL,
  `next_due_date` date NOT NULL,
  `end_date` date DEFAULT NULL COMMENT 'Null means indefinite',
  `payment_method` varchar(50) DEFAULT 'Cash',
  `remind_days_before` int(2) DEFAULT 3 COMMENT 'Days before due date to show reminder',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `individual_discount_mode` tinyint(1) DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_returns`
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
-- Table structure for table `sales_return_items`
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
-- Table structure for table `sequences`
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
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_name` varchar(50) NOT NULL,
  `setting_description` text NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_name`, `setting_description`, `setting_value`, `created_at`, `updated_at`) VALUES
('company_address', 'Company Address displayed on invoices', 'FF26, Megacity, Athurugiriya.', '2026-01-15 13:02:19', '2026-01-15 13:02:19'),
('company_base_url', 'Base URL for API calls', 'https://pos.srijaya.lk', '2026-01-15 13:02:21', '2026-01-15 13:02:21'),
('company_logo', 'URL/Path to Company Logo', 'logo.png', '2026-01-15 13:02:21', '2026-01-15 13:02:21'),
('company_name', 'Company Name displayed on invoices and header', 'Srijaya Print House', '2026-01-15 13:02:18', '2026-01-15 13:02:18'),
('company_phone', 'Company Phone Number', '0714730996', '2026-01-15 13:02:20', '2026-01-15 13:02:20'),
('company_website', 'URL of Official Website', 'www.srijaya.lk', '2026-01-15 13:02:21', '2026-01-15 23:55:24'),
('employee_commission_enabled', 'Enable employee commission from invoice profit (1=yes, 0=no)', '1', '2026-01-12 19:20:19', '2026-01-12 19:38:48'),
('invoice_print_type', 'Default invoice print type (receipt, standard, or both)', 'standard', '2025-11-30 19:55:49', '2025-11-30 20:06:13'),
('quotation_auto_generate', 'Auto generate quotation numbers (1=yes, 0=no)', '1', '2025-11-30 21:47:53', '2025-11-30 21:47:53'),
('quotation_prefix', 'Prefix for quotation numbers', 'QT', '2025-11-30 21:47:53', '2025-11-30 21:47:53'),
('quotation_validity_days', 'Default validity period for quotations in days', '14', '2025-11-30 21:47:53', '2025-11-30 22:02:04'),
('sell_Inactive_batch_products', 'Allow selling from inactive batches (1=allow, 0=restrict)', '1', '2025-07-13 13:56:52', '2025-07-13 16:48:08'),
('sell_Insufficient_stock_item', 'Allow selling out-of-stock items (1=allow, 0=restrict)', '1', '2025-07-13 13:56:52', '2025-07-13 13:56:52');

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
  `grn_id` int(11) DEFAULT NULL,
  `po_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `telegram_config`
--

CREATE TABLE `telegram_config` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `telegram_logs`
--

CREATE TABLE `telegram_logs` (
  `id` int(11) NOT NULL,
  `message_type` varchar(50) DEFAULT NULL,
  `recipient_topic` varchar(50) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `status` enum('success','failed') NOT NULL DEFAULT 'success',
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `telegram_schedules`
--

CREATE TABLE `telegram_schedules` (
  `id` int(11) NOT NULL,
  `report_type` varchar(50) NOT NULL,
  `schedule_hour` int(2) NOT NULL COMMENT '0-23 Hour',
  `target_topic_key` varchar(50) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `telegram_topics`
--

CREATE TABLE `telegram_topics` (
  `id` int(11) NOT NULL,
  `topic_key` varchar(50) NOT NULL COMMENT 'internal key like inventory_alerts',
  `topic_name` varchar(100) NOT NULL COMMENT 'User facing name',
  `topic_id` int(11) DEFAULT NULL COMMENT 'Telegram Message Thread ID',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Stand-in structure for view `v_expense_category_summary`
-- (See below for the actual view)
--
CREATE TABLE `v_expense_category_summary` (
`category_id` int(11)
,`category_name` varchar(100)
,`color_code` varchar(7)
,`icon` varchar(50)
,`total_transactions` bigint(21)
,`total_amount` decimal(37,2)
,`total_paid` decimal(37,2)
,`current_month_amount` decimal(37,2)
,`current_month_paid` decimal(37,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_expense_payment_history`
-- (See below for the actual view)
--
CREATE TABLE `v_expense_payment_history` (
`payment_id` int(11)
,`expense_id` int(11)
,`expense_title` varchar(255)
,`expense_total` decimal(15,2)
,`payment_amount` decimal(15,2)
,`payment_date` datetime
,`payment_method` varchar(50)
,`reference_no` varchar(50)
,`notes` text
,`created_by_name` varchar(50)
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_upcoming_recurring_payments`
-- (See below for the actual view)
--
CREATE TABLE `v_upcoming_recurring_payments` (
`recurring_id` int(11)
,`title` varchar(255)
,`amount` decimal(15,2)
,`next_due_date` date
,`frequency` enum('daily','weekly','monthly','annually')
,`payment_method` varchar(50)
,`remind_days_before` int(2)
,`category_name` varchar(100)
,`color_code` varchar(7)
,`icon` varchar(50)
,`days_until_due` int(8)
,`payment_status` varchar(8)
);

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
-- Indexes for table `employee_commission_history`
--
ALTER TABLE `employee_commission_history`
  ADD PRIMARY KEY (`commission_id`),
  ADD KEY `idx_commission_invoice` (`invoice_number`),
  ADD KEY `idx_commission_employee` (`employee_id`),
  ADD KEY `idx_commission_product` (`product_id`),
  ADD KEY `idx_commission_date` (`created_at`);

--
-- Indexes for table `error_log`
--
ALTER TABLE `error_log`
  ADD PRIMARY KEY (`error_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`expense_id`),
  ADD KEY `idx_date` (`expense_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_recurring_ref` (`recurring_ref_id`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_expense_date` (`expense_date`),
  ADD KEY `idx_expense_amount` (`amount`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `idx_category_name` (`category_name`);

--
-- Indexes for table `expense_payments`
--
ALTER TABLE `expense_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `idx_expense` (`expense_id`),
  ADD KEY `idx_date` (`payment_date`);

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
  ADD KEY `idx_grn_date` (`receipt_date`),
  ADD KEY `grn_supplier_fk` (`supplier_id`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_grn_supplier_payment` (`supplier_id`,`payment_status`);

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
-- Indexes for table `invoice_audit_logs`
--
ALTER TABLE `invoice_audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_invoice_number` (`invoice_number`),
  ADD KEY `idx_action_type` (`action_type`);

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
-- Indexes for table `quotations`
--
ALTER TABLE `quotations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quotation_number` (`quotation_number`),
  ADD KEY `idx_quotation_number` (`quotation_number`),
  ADD KEY `idx_customer_name` (`customer_name`),
  ADD KEY `idx_quotation_date` (`quotation_date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `quotation_items`
--
ALTER TABLE `quotation_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_quotation_items_quotation` (`quotation_id`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- Indexes for table `recurring_expenses`
--
ALTER TABLE `recurring_expenses`
  ADD PRIMARY KEY (`recurring_id`),
  ADD KEY `idx_next_due` (`next_due_date`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_active` (`is_active`);

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
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD UNIQUE KEY `setting_name` (`setting_name`);

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
  ADD KEY `po_id` (`po_id`),
  ADD KEY `grn_id` (`grn_id`);

--
-- Indexes for table `telegram_config`
--
ALTER TABLE `telegram_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `telegram_logs`
--
ALTER TABLE `telegram_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `telegram_schedules`
--
ALTER TABLE `telegram_schedules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `telegram_topics`
--
ALTER TABLE `telegram_topics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `topic_key` (`topic_key`);

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
-- AUTO_INCREMENT for table `employee_commission_history`
--
ALTER TABLE `employee_commission_history`
  MODIFY `commission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `error_log`
--
ALTER TABLE `error_log`
  MODIFY `error_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_payments`
--
ALTER TABLE `expense_payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `invoice_audit_logs`
--
ALTER TABLE `invoice_audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quotation_items`
--
ALTER TABLE `quotation_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recurring_expenses`
--
ALTER TABLE `recurring_expenses`
  MODIFY `recurring_id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `telegram_config`
--
ALTER TABLE `telegram_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `telegram_logs`
--
ALTER TABLE `telegram_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `telegram_schedules`
--
ALTER TABLE `telegram_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `telegram_topics`
--
ALTER TABLE `telegram_topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- Structure for view `product_view`
--
DROP TABLE IF EXISTS `product_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `product_view`  AS SELECT `p`.`product_id` AS `product_id`, `p`.`product_name` AS `product_name`, `p`.`description` AS `description`, `pb`.`selling_price` AS `rate`, `pb`.`cost` AS `cost`, `pb`.`profit` AS `profit`, `p`.`has_stock` AS `has_stock`, `p`.`stock_alert_limit` AS `stock_alert_limit`, `p`.`image` AS `image`, `p`.`show_in_landing_page` AS `show_in_landing_page`, `p`.`category_id` AS `category_id`, `p`.`brand_id` AS `brand_id`, `p`.`barcode` AS `barcode`, `p`.`barcode_symbology` AS `barcode_symbology`, `p`.`created_at` AS `created_at`, `p`.`updated_at` AS `updated_at`, `p`.`sku` AS `sku`, `p`.`active_status` AS `active_status`, coalesce(sum(`pb`.`quantity`),0) AS `stock_qty` FROM (`products` `p` left join `product_batch` `pb` on(`p`.`product_id` = `pb`.`product_id`)) GROUP BY `p`.`product_id` ;

-- --------------------------------------------------------

--
-- Structure for view `v_expense_category_summary`
--
DROP TABLE IF EXISTS `v_expense_category_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`srijayapos`@`localhost` SQL SECURITY DEFINER VIEW `v_expense_category_summary`  AS SELECT `ec`.`category_id` AS `category_id`, `ec`.`category_name` AS `category_name`, `ec`.`color_code` AS `color_code`, `ec`.`icon` AS `icon`, count(`e`.`expense_id`) AS `total_transactions`, coalesce(sum(`e`.`amount`),0) AS `total_amount`, coalesce(sum(`e`.`amount_paid`),0) AS `total_paid`, coalesce(sum(case when month(`e`.`expense_date`) = month(curdate()) and year(`e`.`expense_date`) = year(curdate()) then `e`.`amount` else 0 end),0) AS `current_month_amount`, coalesce(sum(case when month(`e`.`expense_date`) = month(curdate()) and year(`e`.`expense_date`) = year(curdate()) then `e`.`amount_paid` else 0 end),0) AS `current_month_paid` FROM (`expense_categories` `ec` left join `expenses` `e` on(`ec`.`category_id` = `e`.`category_id`)) WHERE `ec`.`status` = 1 GROUP BY `ec`.`category_id`, `ec`.`category_name`, `ec`.`color_code`, `ec`.`icon` ;

-- --------------------------------------------------------

--
-- Structure for view `v_expense_payment_history`
--
DROP TABLE IF EXISTS `v_expense_payment_history`;

CREATE ALGORITHM=UNDEFINED DEFINER=`srijayapos`@`localhost` SQL SECURITY DEFINER VIEW `v_expense_payment_history`  AS SELECT `ep`.`payment_id` AS `payment_id`, `ep`.`expense_id` AS `expense_id`, `e`.`title` AS `expense_title`, `e`.`amount` AS `expense_total`, `ep`.`payment_amount` AS `payment_amount`, `ep`.`payment_date` AS `payment_date`, `ep`.`payment_method` AS `payment_method`, `ep`.`reference_no` AS `reference_no`, `ep`.`notes` AS `notes`, `emp`.`emp_name` AS `created_by_name`, `ep`.`created_at` AS `created_at` FROM ((`expense_payments` `ep` join `expenses` `e` on(`ep`.`expense_id` = `e`.`expense_id`)) left join `employees` `emp` on(`ep`.`created_by` = `emp`.`employ_id`)) ORDER BY `ep`.`payment_date` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `v_upcoming_recurring_payments`
--
DROP TABLE IF EXISTS `v_upcoming_recurring_payments`;

CREATE ALGORITHM=UNDEFINED DEFINER=`srijayapos`@`localhost` SQL SECURITY DEFINER VIEW `v_upcoming_recurring_payments`  AS SELECT `re`.`recurring_id` AS `recurring_id`, `re`.`title` AS `title`, `re`.`amount` AS `amount`, `re`.`next_due_date` AS `next_due_date`, `re`.`frequency` AS `frequency`, `re`.`payment_method` AS `payment_method`, `re`.`remind_days_before` AS `remind_days_before`, `ec`.`category_name` AS `category_name`, `ec`.`color_code` AS `color_code`, `ec`.`icon` AS `icon`, to_days(`re`.`next_due_date`) - to_days(curdate()) AS `days_until_due`, CASE WHEN `re`.`next_due_date` < curdate() THEN 'overdue' WHEN to_days(`re`.`next_due_date`) - to_days(curdate()) <= `re`.`remind_days_before` THEN 'due_soon' ELSE 'upcoming' END AS `payment_status` FROM (`recurring_expenses` `re` join `expense_categories` `ec` on(`re`.`category_id` = `ec`.`category_id`)) WHERE `re`.`is_active` = 1 ORDER BY `re`.`next_due_date` ASC ;

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
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `fk_expense_addedby_employe` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employ_id`),
  ADD CONSTRAINT `fk_expense_category` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`category_id`),
  ADD CONSTRAINT `fk_expense_recurring` FOREIGN KEY (`recurring_ref_id`) REFERENCES `recurring_expenses` (`recurring_id`) ON DELETE SET NULL;

--
-- Constraints for table `expense_payments`
--
ALTER TABLE `expense_payments`
  ADD CONSTRAINT `fk_payment_expense` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`expense_id`) ON DELETE CASCADE;

--
-- Constraints for table `goods_receipt_notes`
--
ALTER TABLE `goods_receipt_notes`
  ADD CONSTRAINT `grn_employee_fk` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employ_id`),
  ADD CONSTRAINT `grn_po_fk` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`),
  ADD CONSTRAINT `grn_supplier_fk` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `grn_items`
--
ALTER TABLE `grn_items`
  ADD CONSTRAINT `grni_batch_fk` FOREIGN KEY (`batch_id`) REFERENCES `product_batch` (`batch_id`),
  ADD CONSTRAINT `grni_grn_fk` FOREIGN KEY (`grn_id`) REFERENCES `goods_receipt_notes` (`grn_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grni_poi_fk` FOREIGN KEY (`po_item_id`) REFERENCES `purchase_order_items` (`po_item_id`);

--
-- Constraints for table `pettycash`
--
ALTER TABLE `pettycash`
  ADD CONSTRAINT `fk_pettycash_register` FOREIGN KEY (`register_id`) REFERENCES `cash_register` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `printer_counter_count`
--
ALTER TABLE `printer_counter_count`
  ADD CONSTRAINT `count_ibfk_1` FOREIGN KEY (`typeID`) REFERENCES `printer_counter_types` (`typeID`);

--
-- Constraints for table `printer_counter_types`
--
ALTER TABLE `printer_counter_types`
  ADD CONSTRAINT `types_ibfk_1` FOREIGN KEY (`printerID`) REFERENCES `printer_counter_printers` (`printerID`);

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
-- Constraints for table `quotation_items`
--
ALTER TABLE `quotation_items`
  ADD CONSTRAINT `fk_quotation_items_quotation` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recurring_expenses`
--
ALTER TABLE `recurring_expenses`
  ADD CONSTRAINT `fk_recurring_category` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`category_id`);

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
-- Constraints for table `sales_returns`
--
ALTER TABLE `sales_returns`
  ADD CONSTRAINT `sales_returns_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `sales_return_items`
--
ALTER TABLE `sales_return_items`
  ADD CONSTRAINT `sales_return_items_ibfk_1` FOREIGN KEY (`return_id`) REFERENCES `sales_returns` (`return_id`);

--
-- Constraints for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD CONSTRAINT `supplier_payments_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_payments_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employ_id`),
  ADD CONSTRAINT `supplier_payments_ibfk_3` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_payments_ibfk_4` FOREIGN KEY (`grn_id`) REFERENCES `goods_receipt_notes` (`grn_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_log`
--
ALTER TABLE `transaction_log`
  ADD CONSTRAINT `transaction_log_ibfk_1` FOREIGN KEY (`employ_id`) REFERENCES `employees` (`employ_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

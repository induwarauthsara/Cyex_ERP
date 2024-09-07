-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 08, 2024 at 03:01 AM
-- Server version: 10.6.18-MariaDB-cll-lve
-- PHP Version: 8.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+05:30";


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
  `account_type` varchar(20) NOT NULL DEFAULT 'cash',
  `amount` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `account_name`, `account_type`, `amount`) VALUES
(1, 'Stock Account', 'cash', 0.00),
(2, 'Company Profit', 'cash', 0.00),
(6, 'Utility Bills', 'cash', 83420.30),
(7, 'cash_in_hand', 'cash', 10000.70),
(12, 'BOC', 'bank', 6800.00),


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
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(3) NOT NULL,
  `customer_name` varchar(40) NOT NULL,
  `customer_type` varchar(10) NOT NULL,
  `customer_mobile` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employ_id` int(4) NOT NULL,
  `emp_name` varchar(30) NOT NULL,
  `mobile` int(10) NOT NULL,
  `address` varchar(150) NOT NULL,
  `bank_account` varchar(30) NOT NULL,
  `role` varchar(10) NOT NULL,
  `nic` varchar(15) NOT NULL,
  `salary` decimal(8,2) NOT NULL,
  `day_salary` decimal(8,2) NOT NULL,
  `password` varchar(30) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1,
  `dp` longblob NOT NULL,
  `is_clocked_in` tinyint(1) DEFAULT 0,
  `onboard_date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employ_id`, `emp_name`, `mobile`, `address`, `bank_account`, `role`, `nic`, `salary`, `day_salary`, `password`, `status`, `dp`, `is_clocked_in`, `onboard_date`) VALUES
(0, 'ifix', 718366077, '', '', 'Admin', '', 0.00, 0.00, 'indroot', 1, '', 0, '2024-09-08'),
(1, 'lakmal', 718366077, '', '', 'Admin', '', 0.00, 0.00, 'admin@132', 1, '', 0, '2024-09-08');

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
  `invoice_description` text NOT NULL,
  `customer_id` int(4) NOT NULL,
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
  `cost` double(10,2) NOT NULL,
  `profit` double(10,2) NOT NULL,
  `full_paid` tinyint(1) NOT NULL,
  `paymentMethod` varchar(20) NOT NULL
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
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(4) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `description` varchar(50) NOT NULL,
  `cost` double(12,2) NOT NULL,
  `qty` int(5) NOT NULL,
  `supplier` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `supplier` varchar(100) DEFAULT NULL,
  `account_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
  `description` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_sinhala_ci NOT NULL,
  `stock_qty` int(5) NOT NULL,
  `rate` decimal(8,2) NOT NULL,
  `cost` decimal(8,2) NOT NULL,
  `profit` double(10,2) NOT NULL,
  `has_stock` text NOT NULL,
  `image` varchar(100) NOT NULL,
  `show_in_landing_page` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `product` varchar(30) NOT NULL,
  `description` varchar(50) NOT NULL,
  `qty` varchar(8) NOT NULL,
  `rate` decimal(8,2) NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `cost` double(10,2) NOT NULL,
  `profit` double(10,2) NOT NULL,
  `worker` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `employ_id` int(4) NOT NULL DEFAULT 5
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
  ADD PRIMARY KEY (`employ_id`);

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
  ADD KEY `invoice number` (`invoice_number`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employ_id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `error_log`
--
ALTER TABLE `error_log`
  MODIFY `error_id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `makeProduct`
--
ALTER TABLE `makeProduct`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `oneTimeProducts_sales`
--
ALTER TABLE `oneTimeProducts_sales`
  MODIFY `oneTimeProduct_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pettycash`
--
ALTER TABLE `pettycash`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase`
--
ALTER TABLE `purchase`
  MODIFY `purchase_id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(4) NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `invoice number` FOREIGN KEY (`invoice_number`) REFERENCES `invoice` (`invoice_number`);

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

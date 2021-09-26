-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 19, 2021 at 07:30 AM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `srijaya`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `account_name` varchar(30) NOT NULL,
  `amount` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `account_name`, `amount`) VALUES
(1, 'Stock Account', '31750.01'),
(2, 'Company Profit', '14719.90'),
(3, 'Machines Account', '12099.80'),
(6, 'Utility Bills', '8129.80');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(3) NOT NULL,
  `customer_name` varchar(40) NOT NULL,
  `customer_type` varchar(10) NOT NULL,
  `customer_mobile` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `customer_name`, `customer_type`, `customer_mobile`) VALUES
(1, 'CusTomer_A', 'Group-A', 786607354),
(2, 'CusTomer_B', 'Group-B', 714730996),
(3, 'CusTomer_C', 'Group-C', 763547175),
(4, 'CusTomer_D', 'Group-D', 72605344),
(5, 'Winsara', 'Group-A', 719922440),
(7, 'newcustomer', '', 123),
(8, 'best customer', '', 999),
(10, 'aluth', '', 555);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employ_id`, `emp_name`, `mobile`, `address`, `bank_account`, `role`, `nic`, `salary`, `password`) VALUES
(1, 'Lochana', 714730996, '55/21, Galabadawatta, Korathota, Kaduwela.', '', 'CEO', '', '5747.12', ''),
(2, 'Nalani', 763547175, '55/21, Galabadawatta, Korathota, Kaduwela.', '', 'Employee', '', '4488.82', ''),
(3, 'Prabudda Lakshith', 0, '', '', 'Employee', '', '1383.45', ''),
(4, 'Induwara Uthsara', 786607354, '55/21, Galabadawatta, Korathota, Kaduwela.', '', 'Employee', '200501903425', '200.14', '');

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `id` int(4) NOT NULL,
  `item_name` varchar(30) NOT NULL,
  `product_name` varchar(30) NOT NULL,
  `qty` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`id`, `item_name`, `product_name`, `qty`) VALUES
(1, 'Plain Mug', 'Mug Print', 1),
(2, 'Pollymer', 'Pollymer Seal', 200),
(3, 'Seal Mount', 'Pollymer Seal', 1),
(4, 'Seal Tapes', 'Pollymer Seal', 1),
(5, 'Mug Print Paper', 'Mug Print', 1),
(10, 'Mug Print Paper', 'Photocopy', 1);

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(10) NOT NULL,
  `customer_name` varchar(20) NOT NULL,
  `invoice_date` date NOT NULL,
  `customer_mobile` int(10) NOT NULL,
  `biller` varchar(20) NOT NULL,
  `primary_worker` varchar(20) NOT NULL,
  `total` varchar(10) NOT NULL,
  `discount` varchar(10) NOT NULL,
  `advance` varchar(10) NOT NULL,
  `balance` varchar(10) NOT NULL,
  `full_paid` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`id`, `invoice_number`, `customer_name`, `invoice_date`, `customer_mobile`, `biller`, `primary_worker`, `total`, `discount`, `advance`, `balance`, `full_paid`) VALUES
(2, '#00001', 'aluth', '2021-09-19', 555, 'Nalani', 'Nalani', '850.00', '850.00', '0.00', '0.00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(4) NOT NULL,
  `item_name` varchar(30) NOT NULL,
  `description` varchar(50) NOT NULL,
  `cost` decimal(8,2) NOT NULL,
  `qty` int(5) NOT NULL,
  `supplier` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `item_name`, `description`, `cost`, `qty`, `supplier`) VALUES
(1, 'Pollymer', '', '5000.00', 2500, 'Sealer'),
(2, 'Seal Mount', 'S of S', '29.99', 100, 'ddddddddddddd'),
(3, 'Seal Tapes', '', '10.00', 100, ';'),
(4, 'Plain Mug', '', '500.00', 2, 'Mug'),
(5, 'Mug Print Paper', '', '50.00', 250, ''),
(6, 's', 'S of S', '5.75', 46, 'ddddddddddddd'),
(7, 'A4', '500gsm', '2.50', 500, 'Paperline'),
(8, 'A3', '500gsm', '4.50', 20, 'JK'),
(9, 'Glue', 'Glue', '5.00', 200, 'Gam bothal'),
(10, 'item_name', 'item_description', '110.00', 11, 'itemsupplier'),
(11, 'false', 'five', '55.00', 5, 'fiftyfive'),
(12, 'A4', '500gsm', '1.00', 2, 'paperline'),
(13, 'Ink', 'Seal ink', '2.50', 600, 'paperline'),
(14, 'Seal Ink', 'Blue Ink', '1000.00', 500, 'Sumaga'),
(15, 'A3 Photocopy', 'A3', '4.80', 800, 'PaperPLINE');

-- --------------------------------------------------------

--
-- Table structure for table `pettycash`
--

CREATE TABLE `pettycash` (
  `id` int(4) NOT NULL,
  `perrycash` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `emp_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pettycash`
--

INSERT INTO `pettycash` (`id`, `perrycash`, `amount`, `emp_name`) VALUES
(1, 'd', '22.00', '0'),
(2, 'yu', '0.00', ''),
(3, 'Hello', '5000.00', ''),
(4, 'a', '0.00', ''),
(5, 'da', '0.00', ''),
(6, 'dsad', '52.00', ''),
(7, '2', '55.50', ''),
(8, 'rice', '500.00', ''),
(9, 'Face Mask', '800.00', ''),
(10, 'res22', '50.00', ''),
(11, '', '0.00', ''),
(12, '', '0.00', ''),
(13, '', '0.00', ''),
(14, '', '0.00', ''),
(15, '', '0.00', ''),
(16, '', '0.00', ''),
(17, '', '0.00', ''),
(18, 'u', '55.00', ''),
(19, 'lol', '500.00', ''),
(20, '', '0.00', ''),
(21, '', '0.00', ''),
(22, '', '0.00', ''),
(23, '', '0.00', '');

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
  `has_stock` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `description`, `stock_qty`, `rate`, `cost`, `profit`, `has_stock`) VALUES
(1, 'Photocopy', '[value-3]', 992, '5.00', '2.50', 2.50, 0),
(2, 'Printout', '[value-3]', 762, '5.00', '2.50', 2.50, 0),
(3, 'Pollymer Seal', '[value-3]', 250, '300.00', '200.00', 100.00, 0),
(4, 'Mug Print', '[value-3]', -50, '850.00', '450.00', 400.00, 0),
(5, 'Bill Book', '[value-3]', 9999844, '100.00', '55.00', 45.00, 0),
(6, 'Laminating', '[value-3]', 365, '40.00', '30.00', 10.00, 0),
(7, 'Legal Photocopy', '[value-3]', 42, '7.00', '3.00', 4.00, 0),
(8, 'A5', 'A5 Copy', 20, '2.50', '2.00', 0.50, 1),
(9, 'A3', 'A3 copy', 1000, '4.80', '4.00', 0.80, 1);

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
  `amount` varchar(8) NOT NULL,
  `worker` varchar(30) NOT NULL,
  `todo` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sales_id`, `invoice_number`, `product`, `description`, `qty`, `rate`, `amount`, `worker`, `todo`) VALUES
(1, '#00001', 'Mug Print', 'Magic Mug', '1', '850.00', '850.00', 'Nalani', 'Unchecked'),
(2, '#00001', 'A3', 'Grade 10 Maths', '10', '0.00', '0.00', 'Nalani', 'Unchecked');

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
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product` (`product_name`) USING BTREE,
  ADD KEY `item` (`item_name`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_name` (`item_name`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employ_id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `pettycash`
--
ALTER TABLE `pettycash`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sales_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD CONSTRAINT `item` FOREIGN KEY (`item_name`) REFERENCES `items` (`item_name`) ON UPDATE CASCADE,
  ADD CONSTRAINT `product` FOREIGN KEY (`product_name`) REFERENCES `products` (`product_name`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

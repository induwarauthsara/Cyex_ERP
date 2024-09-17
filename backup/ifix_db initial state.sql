-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 17, 2024 at 07:37 PM
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
(1, 'Stock Account', 'cash', -1985.00),
(2, 'Company Profit', 'cash', 4163.15),
(6, 'Utility Bills', 'cash', 83420.30),
(7, 'cash_in_hand', 'cash', -535270.30),
(12, 'BOC', 'bank', 6800.00);

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
(1, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : , Rs. 0', '2024-09-09', '05:35:51'),
(2, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description :  - ohh, Payment Method : Cash, Advance : Rs. 0, Rs. 0 by 1 ', '2024-09-09', '05:35:51'),
(3, 1, 'Add Biller Profit to Employee Table', 'send biller Profit : lakmal Rs. 11.1', '2024-09-09', '05:45:52'),
(4, 1, 'Employee Salary Paid - Update Salary Table', 'Employee ID: 1, Rs. 11.1', '2024-09-09', '05:45:52'),
(5, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 62.9', '2024-09-09', '05:45:52'),
(6, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : 85% Profit to Company. Inv:  - ohh, Profit : Rs. 62.9, Rs. 62.9 by 1 ', '2024-09-09', '05:45:52'),
(7, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : , Rs. 74', '2024-09-09', '05:45:52'),
(8, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description :  - ohh, Payment Method : Cash, Advance : Rs. 74, Rs. 74 by 1 ', '2024-09-09', '05:45:52'),
(9, 1, 'Add New Customer', 'gftjfu', '2024-09-09', '06:48:45'),
(10, 1, 'Add New Invoice', 'Invoice Number : 1, Customer Name : gftjfu, Date : 2024-09-08, Total : 3770, Discount : 0, Advance : 3770, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-09-09', '06:48:45'),
(11, 1, 'Add New Action Log', 'Add New Invoice', '2024-09-09', '06:48:45'),
(12, 1, 'Add Todo Item', 'repair', '2024-09-09', '06:48:45'),
(13, 1, 'Sale One-Time-Product', 'llolko', '2024-09-09', '06:48:45'),
(14, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 1, Total Bill Cost : 0, Total Bill Profit : 3770', '2024-09-09', '06:48:45'),
(15, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 1, Rs. 3770', '2024-09-09', '06:48:45'),
(16, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 1 - gftjfu, Payment Method : Cash, Advance : Rs. 3770, Rs. 3770 by 1 ', '2024-09-09', '06:48:45'),
(17, 1, 'Add Raw Item', 'Name : charger, Price : 500, Qty : 5', '2024-09-09', '06:51:34'),
(18, 1, 'Fall Cash-In-Hand Account for Raw Item Purchase', 'Name : charger, Price : 500, Qty : 5', '2024-09-09', '06:51:34'),
(19, 1, 'Transaction Log', 'Transaction Type : Raw Item Purchase, description : charger, Rs. -2500 by 1 ', '2024-09-09', '06:51:34'),
(20, 1, 'Add Combo Product', 'Add New Combo Product : oms charger 25w', '2024-09-09', '06:53:03'),
(21, 1, 'Add New Customer', 'Cash', '2024-09-09', '06:54:01'),
(22, 1, 'Add New Invoice', 'Invoice Number : 2, Customer Name : Cash, Date : 2024-09-08, Total : 800, Discount : 0, Advance : 800, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-09-09', '06:54:01'),
(23, 1, 'Add New Action Log', 'Add New Invoice', '2024-09-09', '06:54:01'),
(24, 1, 'Add Product Cost to Stock Account', 'Rs. 500', '2024-09-09', '06:54:01'),
(25, 1, 'Add New Sale', 'Add New Sale : oms charger 25w', '2024-09-09', '06:54:01'),
(26, 1, 'fall item from stock', 'charger, Qty : 1 items', '2024-09-09', '06:54:01'),
(27, 1, 'Update Product available Qty', 'oms charger 25w Available Qty : 4', '2024-09-09', '06:54:01'),
(28, 1, 'Update Product Has_Stock State', 'oms charger 25w is In Stock', '2024-09-09', '06:54:01'),
(29, 1, 'Add Biller Profit to Employee Table', 'send biller Profit : lakmal Rs. 45', '2024-09-09', '06:54:01'),
(30, 1, 'Employee Salary Paid - Update Salary Table', 'Employee ID: 1, Rs. 45', '2024-09-09', '06:54:01'),
(31, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 255', '2024-09-09', '06:54:01'),
(32, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : 85% Profit to Company. Inv: 2 - Cash, Profit : Rs. 255, Rs. 255 by 1 ', '2024-09-09', '06:54:01'),
(33, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 2, Total Bill Cost : 500, Total Bill Profit : 300', '2024-09-09', '06:54:01'),
(34, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 2, Rs. 800', '2024-09-09', '06:54:01'),
(35, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 2 - Cash, Payment Method : Cash, Advance : Rs. 800, Rs. 800 by 1 ', '2024-09-09', '06:54:01'),
(36, 0, 'Error Solved', 'Error ID : 1', '2024-09-09', '07:05:30'),
(37, 0, 'Error Solved', 'Error ID : 2', '2024-09-09', '07:06:39'),
(38, 0, 'Error Solved', 'Error ID : 3', '2024-09-09', '07:06:41'),
(39, 0, 'Error Solved', 'Error ID : 4', '2024-09-09', '07:06:42'),
(40, 0, 'Error Solved', 'Error ID : 5', '2024-09-09', '07:06:45'),
(41, 1, 'Delete Raw Item', 'Delete Raw Items from makeProduct Table (Item Name: charger)', '2024-09-09', '08:05:24'),
(42, 1, 'Delete Raw Item', 'Delete Item from items Table (Item Name: charger)', '2024-09-09', '08:05:24'),
(43, 1, 'Delete Raw Item', 'Delete Raw Items from makeProduct Table (Item Name: charger)', '2024-09-09', '08:05:36'),
(44, 1, 'Delete Raw Item', 'Delete Item from items Table (Item Name: charger)', '2024-09-09', '08:05:36'),
(45, 1, 'Add Raw Item', 'Name : TEST ITEM, Price : 23, Qty : 3', '2024-09-10', '07:55:41'),
(46, 1, 'Fall Cash-In-Hand Account for Raw Item Purchase', 'Name : TEST ITEM, Price : 23, Qty : 3', '2024-09-10', '07:55:41'),
(47, 1, 'Transaction Log', 'Transaction Type : Raw Item Purchase, description : TEST ITEM, Rs. -69 by 1 ', '2024-09-10', '07:55:41'),
(48, 1, 'Delete Raw Item', 'Delete Raw Items from makeProduct Table (Item Name: TEST ITEM)', '2024-09-10', '07:55:49'),
(49, 1, 'Delete Raw Item', 'Delete Item from items Table (Item Name: TEST ITEM)', '2024-09-10', '07:55:49'),
(50, 1, 'Add Raw Item', 'Name : Xpert XPC25 Charger, Price : 450, Qty : 20', '2024-09-10', '07:59:40'),
(51, 1, 'Fall Cash-In-Hand Account for Raw Item Purchase', 'Name : Xpert XPC25 Charger, Price : 450, Qty : 20', '2024-09-10', '07:59:40'),
(52, 1, 'Transaction Log', 'Transaction Type : Raw Item Purchase, description : Xpert XPC25 Charger, Rs. -9000 by 1 ', '2024-09-10', '07:59:40'),
(53, 1, 'Add Raw Item', 'Name : Oms Micro Charger, Price : 300, Qty : 20', '2024-09-10', '08:01:29'),
(54, 1, 'Fall Cash-In-Hand Account for Raw Item Purchase', 'Name : Oms Micro Charger, Price : 300, Qty : 20', '2024-09-10', '08:01:29'),
(55, 1, 'Transaction Log', 'Transaction Type : Raw Item Purchase, description : Oms Micro Charger, Rs. -6000 by 1 ', '2024-09-10', '08:01:29'),
(56, 1, 'Add Combo Product', 'Add New Combo Product : OMS Micro Cable ', '2024-09-10', '08:04:07'),
(57, 1, 'Add New Invoice', 'Invoice Number : 3, Customer Name : Cash, Date : 2024-09-10, Total : 1350, Discount : 350, Advance : 1000, Balance : 500.00, Full Paid : 0, Payment Method : Cash', '2024-09-10', '08:06:23'),
(58, 1, 'Add New Action Log', 'Add New Invoice', '2024-09-10', '08:06:23'),
(59, 1, 'Add Product Cost to Stock Account', 'Rs. 300', '2024-09-10', '08:06:23'),
(60, 1, 'Add New Sale', 'Add New Sale : OMS Micro Cable ', '2024-09-10', '08:06:23'),
(61, 1, 'fall item from stock', 'Oms Micro Cable OM-154, Qty : 1 items', '2024-09-10', '08:06:23'),
(62, 1, 'Update Product available Qty', 'OMS Micro Cable  Available Qty : 19', '2024-09-10', '08:06:23'),
(63, 1, 'Update Product Has_Stock State', 'OMS Micro Cable  is In Stock', '2024-09-10', '08:06:23'),
(64, 1, 'Add Product Cost to Stock Account', 'Rs. 300', '2024-09-10', '08:06:23'),
(65, 1, 'Add New Sale', 'Add New Sale : OMS Micro Cable ', '2024-09-10', '08:06:23'),
(66, 1, 'fall item from stock', 'Oms Micro Cable OM-154, Qty : 1 items', '2024-09-10', '08:06:23'),
(67, 1, 'Update Product available Qty', 'OMS Micro Cable  Available Qty : 18', '2024-09-10', '08:06:23'),
(68, 1, 'Update Product Has_Stock State', 'OMS Micro Cable  is In Stock', '2024-09-10', '08:06:23'),
(69, 1, 'Add Product Cost to Stock Account', 'Rs. 300', '2024-09-10', '08:06:23'),
(70, 1, 'Add New Sale', 'Add New Sale : OMS Micro Cable ', '2024-09-10', '08:06:23'),
(71, 1, 'fall item from stock', 'Oms Micro Cable OM-154, Qty : 1 items', '2024-09-10', '08:06:23'),
(72, 1, 'Update Product available Qty', 'OMS Micro Cable  Available Qty : 17', '2024-09-10', '08:06:23'),
(73, 1, 'Update Product Has_Stock State', 'OMS Micro Cable  is In Stock', '2024-09-10', '08:06:23'),
(74, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 100', '2024-09-10', '08:06:23'),
(75, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 3 - Cash, Profit : Rs. 100, Rs. 100 by 1 ', '2024-09-10', '08:06:23'),
(76, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 3, Total Bill Cost : 900, Total Bill Profit : 100', '2024-09-10', '08:06:23'),
(77, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 3, Rs. 1000', '2024-09-10', '08:06:23'),
(78, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 3 - Cash, Payment Method : Cash, Advance : Rs. 1000, Rs. 1000 by 1 ', '2024-09-10', '08:06:23'),
(79, 1, 'Update Invoice', 'InvoiceNumber : 3, Invoice Description :  , Customer Name : Cash, Invoice Date : 2024-09-10, Customer Mobile : 0, Biller : lakmal, Primary Worker : lakmal, Total : 450, Discount : 350, Advance : 1000, Balance : -900.00, Full Paid : 1, Payment Method : Cash, Cost : 300, Profit : 700', '2024-09-10', '08:10:20'),
(80, 1, 'Add Biller Profit to Employee Table when Invoice Edit', 'send biller Profit : lakmal Rs. 90 , when Invoice Edit : 3', '2024-09-10', '08:10:20'),
(81, 1, 'Employee Salary Paid - Update Salary Table when Invoice Edit', 'Employee ID: 1, Rs. 90 , when Invoice Edit : 3', '2024-09-10', '08:10:20'),
(82, 1, 'Update Company Profit when Invoice Edit', 'Company Profit : Rs. 510, Invoice 3 Invoice Edit', '2024-09-10', '08:10:20'),
(83, 1, 'Delete Sales Data', 'Delete Sales Data When Invoice 3 Edit', '2024-09-10', '08:10:20'),
(84, 1, 'fall item from stock', 'Fall Oms Micro Cable OM-154 - -1 from Stock', '2024-09-10', '08:10:20'),
(85, 1, 'Update Product available Qty', 'OMS Micro Cable  has 18 qty', '2024-09-10', '08:10:20'),
(86, 1, 'Update Product Has_Stock State', 'OMS Micro Cable  stock is available', '2024-09-10', '08:10:20'),
(87, 1, 'Delete Sales Data', 'Delete Sales Data When Invoice 3 Edit', '2024-09-10', '08:10:20'),
(88, 1, 'fall item from stock', 'Fall Oms Micro Cable OM-154 - -1 from Stock', '2024-09-10', '08:10:20'),
(89, 1, 'Update Product available Qty', 'OMS Micro Cable  has 19 qty', '2024-09-10', '08:10:20'),
(90, 1, 'Update Product Has_Stock State', 'OMS Micro Cable  stock is available', '2024-09-10', '08:10:20'),
(91, 1, 'Add Raw Item', 'Name : asdsads, Price : 423, Qty : 50', '2024-09-10', '08:11:19'),
(92, 1, 'Fall Cash-In-Hand Account for Raw Item Purchase', 'Name : asdsads, Price : 423, Qty : 50', '2024-09-10', '08:11:19'),
(93, 1, 'Transaction Log', 'Transaction Type : Raw Item Purchase, description : asdsads, Rs. -21150 by 1 ', '2024-09-10', '08:11:19'),
(94, 1, 'Add Raw Item', 'Name : sfdsdfs, Price : 3434, Qty : 80', '2024-09-10', '08:11:29'),
(95, 1, 'Fall Cash-In-Hand Account for Raw Item Purchase', 'Name : sfdsdfs, Price : 3434, Qty : 80', '2024-09-10', '08:11:29'),
(96, 1, 'Transaction Log', 'Transaction Type : Raw Item Purchase, description : sfdsdfs, Rs. -274720 by 1 ', '2024-09-10', '08:11:29'),
(97, 1, 'Add Raw Item', 'Name : dsfsdfs, Price : 30, Qty : 10', '2024-09-10', '08:11:39'),
(98, 1, 'Fall Cash-In-Hand Account for Raw Item Purchase', 'Name : dsfsdfs, Price : 30, Qty : 10', '2024-09-10', '08:11:39'),
(99, 1, 'Transaction Log', 'Transaction Type : Raw Item Purchase, description : dsfsdfs, Rs. -300 by 1 ', '2024-09-10', '08:11:39'),
(100, 1, 'Add Raw Item', 'Name : dfgfdg, Price : 500, Qty : 100', '2024-09-10', '08:12:02'),
(101, 1, 'Fall Cash-In-Hand Account for Raw Item Purchase', 'Name : dfgfdg, Price : 500, Qty : 100', '2024-09-10', '08:12:02'),
(102, 1, 'Transaction Log', 'Transaction Type : Raw Item Purchase, description : dfgfdg, Rs. -50000 by 1 ', '2024-09-10', '08:12:02'),
(103, 1, 'Add Raw Item', 'Name : Chargin Port, Price : 20, Qty : 50', '2024-09-10', '08:16:33'),
(104, 1, 'Fall Cash-In-Hand Account for Raw Item Purchase', 'Name : Chargin Port, Price : 20, Qty : 50', '2024-09-10', '08:16:33'),
(105, 1, 'Transaction Log', 'Transaction Type : Raw Item Purchase, description : Chargin Port, Rs. -1000 by 1 ', '2024-09-10', '08:16:33'),
(106, 1, 'Add New Invoice', 'Invoice Number : 4, Customer Name : Cash, Date : 2024-09-10, Total : 500, Discount : 0, Advance : 500, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-09-10', '08:17:40'),
(107, 1, 'Add New Action Log', 'Add New Invoice', '2024-09-10', '08:17:40'),
(108, 1, 'Sale One-Time-Product', 'Chargin Port Repart', '2024-09-10', '08:17:40'),
(109, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 500', '2024-09-10', '08:17:40'),
(110, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 4 - Cash, Profit : Rs. 500, Rs. 500 by 1 ', '2024-09-10', '08:17:40'),
(111, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 4, Total Bill Cost : 0, Total Bill Profit : 500', '2024-09-10', '08:17:40'),
(112, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 4, Rs. 500', '2024-09-10', '08:17:40'),
(113, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 4 - Cash, Payment Method : Cash, Advance : Rs. 500, Rs. 500 by 1 ', '2024-09-10', '08:17:40'),
(114, 1, 'Add New Commission Record', 'Commission Added for Chargin Port Repart in Invoice Number : <a href=\'/invoice/print.php?id=4\'> 4 </a>', '2024-09-10', '08:20:30'),
(115, 1, 'Fall OneTimeProduct Cost from Company Profit', 'Fall Rs.200 in Company Profit Account for Repair/Service ID : 2', '2024-09-10', '08:20:30'),
(116, 1, 'Transaction Log', 'Transaction Type : Fall OneTimeProduct Cost from Company Profit, description : Fall Rs.200 in Company Profit Account for Repair/Service ID : 2, Rs. -200 by 1 ', '2024-09-10', '08:20:30'),
(117, 1, 'Update Invoice Cost and Profit', 'Update Invoice (ID:4) for Repair/Service ID : 2', '2024-09-10', '08:20:30'),
(118, 1, 'Update OneTimeProduct Sale', 'Update OneTimeProduct (ID:2) for Invoice ID : 4, Cost : 200, Profit : 300.00, worker : lakmal', '2024-09-10', '08:20:30'),
(119, 1, 'Add Salary Commission', 'Increase Salary of lakmal for Chargin Port Repart by Rs.200', '2024-09-10', '08:20:30'),
(120, 1, 'Transaction Log', 'Transaction Type : Add Salary Commission, description : Increase Salary of lakmal for Chargin Port Repart by Rs.200, Rs. 200 by 1 ', '2024-09-10', '08:20:30'),
(121, 1, 'New Employee Added', 'Name: Udaya', '2024-09-10', '08:23:41'),
(122, 1, 'New Employee Added', 'Name: Kasun', '2024-09-10', '08:24:15'),
(123, 1, 'Add New Invoice', 'Invoice Number : 5, Customer Name : Cash, Date : 2024-09-10, Total : 600, Discount : 0, Advance : 600, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-09-10', '08:28:05'),
(124, 1, 'Add New Action Log', 'Add New Invoice', '2024-09-10', '08:28:05'),
(125, 1, 'Sale One-Time-Product', 'M02 Backcover', '2024-09-10', '08:28:05'),
(126, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 600', '2024-09-10', '08:28:05'),
(127, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 5 - Cash, Profit : Rs. 600, Rs. 600 by 1 ', '2024-09-10', '08:28:05'),
(128, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 5, Total Bill Cost : 0, Total Bill Profit : 600', '2024-09-10', '08:28:05'),
(129, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 5, Rs. 600', '2024-09-10', '08:28:05'),
(130, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 5 - Cash, Payment Method : Cash, Advance : Rs. 600, Rs. 600 by 1 ', '2024-09-10', '08:28:05'),
(131, 1, 'Add Raw Item', 'Name : Backcover, Price : 300, Qty : 1000', '2024-09-10', '08:30:43'),
(132, 1, 'Fall Cash-In-Hand Account for Raw Item Purchase', 'Name : Backcover, Price : 300, Qty : 1000', '2024-09-10', '08:30:43'),
(133, 1, 'Transaction Log', 'Transaction Type : Raw Item Purchase, description : Backcover, Rs. -300000 by 1 ', '2024-09-10', '08:30:43'),
(134, 1, 'Add Combo Product', 'Add New Combo Product : Backcover', '2024-09-10', '08:31:20'),
(135, 1, 'Add New Invoice', 'Invoice Number : 6, Customer Name : Cash, Date : 2024-09-10, Total : 750, Discount : 0, Advance : 750, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-09-10', '08:31:44'),
(136, 1, 'Add New Action Log', 'Add New Invoice', '2024-09-10', '08:31:44'),
(137, 1, 'Add Product Cost to Stock Account', 'Rs. 300', '2024-09-10', '08:31:44'),
(138, 1, 'Add New Sale', 'Add New Sale : Backcover', '2024-09-10', '08:31:44'),
(139, 1, 'fall item from stock', 'Backcover, Qty : 1 items', '2024-09-10', '08:31:44'),
(140, 1, 'Update Product available Qty', 'Backcover Available Qty : 999', '2024-09-10', '08:31:44'),
(141, 1, 'Update Product Has_Stock State', 'Backcover is In Stock', '2024-09-10', '08:31:44'),
(142, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 450', '2024-09-10', '08:31:44'),
(143, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 6 - Cash, Profit : Rs. 450, Rs. 450 by 1 ', '2024-09-10', '08:31:44'),
(144, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 6, Total Bill Cost : 300, Total Bill Profit : 450', '2024-09-10', '08:31:44'),
(145, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 6, Rs. 750', '2024-09-10', '08:31:44'),
(146, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 6 - Cash, Payment Method : Cash, Advance : Rs. 750, Rs. 750 by 1 ', '2024-09-10', '08:31:44'),
(147, 1, 'Delete Raw Item', 'Delete Raw Items from makeProduct Table (Item Name: Xpert XPC25 Charger)', '2024-09-11', '10:37:54'),
(148, 1, 'Delete Raw Item', 'Delete Item from items Table (Item Name: Xpert XPC25 Charger)', '2024-09-11', '10:37:54'),
(149, 1, 'Delete Raw Item', 'Delete Raw Items from makeProduct Table (Item Name: Xpert XPC25 Charger)', '2024-09-11', '10:37:58'),
(150, 1, 'Delete Raw Item', 'Delete Item from items Table (Item Name: Xpert XPC25 Charger)', '2024-09-11', '10:37:58'),
(151, 1, 'Delete Raw Item', 'Delete Raw Items from makeProduct Table (Item Name: asdsads)', '2024-09-11', '10:38:02'),
(152, 1, 'Delete Raw Item', 'Delete Item from items Table (Item Name: asdsads)', '2024-09-11', '10:38:02'),
(153, 1, 'Delete Raw Item', 'Delete Raw Items from makeProduct Table (Item Name: Backcover)', '2024-09-11', '10:38:07'),
(154, 1, 'Delete Raw Item', 'Delete Item from items Table (Item Name: Backcover)', '2024-09-11', '10:38:07'),
(155, 1, 'Delete Raw Item', 'Delete Raw Items from makeProduct Table (Item Name: sfdsdfs)', '2024-09-11', '10:38:13'),
(156, 1, 'Delete Raw Item', 'Delete Item from items Table (Item Name: sfdsdfs)', '2024-09-11', '10:38:13'),
(157, 1, 'Delete Raw Item', 'Delete Raw Items from makeProduct Table (Item Name: Oms Micro Cable OM-154)', '2024-09-11', '10:38:17'),
(158, 1, 'Delete Raw Item', 'Delete Item from items Table (Item Name: Oms Micro Cable OM-154)', '2024-09-11', '10:38:17'),
(159, 1, 'Delete Raw Item', 'Delete Raw Items from makeProduct Table (Item Name: dsfsdfs)', '2024-09-11', '10:38:20'),
(160, 1, 'Delete Raw Item', 'Delete Item from items Table (Item Name: dsfsdfs)', '2024-09-11', '10:38:20'),
(161, 1, 'Delete Raw Item', 'Delete Raw Items from makeProduct Table (Item Name: dfgfdg)', '2024-09-11', '10:38:24'),
(162, 1, 'Delete Raw Item', 'Delete Item from items Table (Item Name: dfgfdg)', '2024-09-11', '10:38:24'),
(163, 1, 'Delete Raw Item', 'Delete Raw Items from makeProduct Table (Item Name: Chargin Port)', '2024-09-11', '10:38:27'),
(164, 1, 'Delete Raw Item', 'Delete Item from items Table (Item Name: Chargin Port)', '2024-09-11', '10:38:27'),
(165, 1, 'Add Raw Item', 'Name : oms 5C batery , Price : 350, Qty : 29', '2024-09-11', '10:43:16'),
(166, 1, 'Fall Cash-In-Hand Account for Raw Item Purchase', 'Name : oms 5C batery , Price : 350, Qty : 29', '2024-09-11', '10:43:16'),
(167, 1, 'Transaction Log', 'Transaction Type : Raw Item Purchase, description : oms 5C batery , Rs. -10150 by 1 ', '2024-09-11', '10:43:16'),
(168, 1, 'Add Combo Product', 'Add New Combo Product : Oms 5C 3 month warranty', '2024-09-11', '10:44:25'),
(169, 1, 'Delete product from makeProduct table', 'Delete from makeProduct table where product_name = OMS Micro Cable', '2024-09-11', '10:44:49'),
(170, 1, 'Delete product', 'Delete product : OMS Micro Cable', '2024-09-11', '10:44:49'),
(171, 1, 'Delete product from makeProduct table', 'Delete from makeProduct table where product_name = oms charger 25w', '2024-09-11', '10:44:54'),
(172, 1, 'Delete product', 'Delete product : oms charger 25w', '2024-09-11', '10:44:54'),
(173, 1, 'Delete product from makeProduct table', 'Delete from makeProduct table where product_name = Backcover', '2024-09-11', '10:45:07'),
(174, 1, 'Delete product', 'Delete product : Backcover', '2024-09-11', '10:45:07'),
(175, 1, 'Add Raw Item', 'Name : oms 4c, Price : 350, Qty : 4', '2024-09-11', '10:46:50'),
(176, 1, 'Fall Cash-In-Hand Account for Raw Item Purchase', 'Name : oms 4c, Price : 350, Qty : 4', '2024-09-11', '10:46:50'),
(177, 1, 'Transaction Log', 'Transaction Type : Raw Item Purchase, description : oms 4c, Rs. -1400 by 1 ', '2024-09-11', '10:46:50'),
(178, 1, 'Add Combo Product', 'Add New Combo Product : oms 4c battery 3 month worrenty ', '2024-09-11', '10:47:02'),
(179, 1, 'Add Raw Item', 'Name : Charging Pin , Price : 150, Qty : 30', '2024-09-11', '11:04:37'),
(180, 1, 'Fall Cash-In-Hand Account for Raw Item Purchase', 'Name : Charging Pin , Price : 150, Qty : 30', '2024-09-11', '11:04:37'),
(181, 1, 'Transaction Log', 'Transaction Type : Raw Item Purchase, description : Charging Pin , Rs. -4500 by 1 ', '2024-09-11', '11:04:37'),
(182, 1, 'Add Combo Product', 'Add New Combo Product : Charging Pin ', '2024-09-11', '11:05:54'),
(183, 1, 'Add New Invoice', 'Invoice Number : 7, Customer Name : Cash, Date : 2024-09-11, Total : 500, Discount : 0, Advance : 500, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-09-11', '11:06:51'),
(184, 1, 'Add New Action Log', 'Add New Invoice', '2024-09-11', '11:06:51'),
(185, 1, 'Add Product Cost to Stock Account', 'Rs. 150', '2024-09-11', '11:06:51'),
(186, 1, 'Add New Sale', 'Add New Sale : Charging Pin ', '2024-09-11', '11:06:51'),
(187, 1, 'fall item from stock', 'Charging Pin, Qty : 1 items', '2024-09-11', '11:06:51'),
(188, 1, 'Update Product available Qty', 'Charging Pin  Available Qty : 29', '2024-09-11', '11:06:51'),
(189, 1, 'Update Product Has_Stock State', 'Charging Pin  is In Stock', '2024-09-11', '11:06:51'),
(190, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 350', '2024-09-11', '11:06:51'),
(191, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 7 - Cash, Profit : Rs. 350, Rs. 350 by 1 ', '2024-09-11', '11:06:51'),
(192, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 7, Total Bill Cost : 150, Total Bill Profit : 350', '2024-09-11', '11:06:51'),
(193, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 7, Rs. 500', '2024-09-11', '11:06:51'),
(194, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 7 - Cash, Payment Method : Cash, Advance : Rs. 500, Rs. 500 by 1 ', '2024-09-11', '11:06:51'),
(195, 1, 'Add New Invoice', 'Invoice Number : 8, Customer Name : Cash, Date : 2024-09-11, Total : 500, Discount : 0, Advance : 500, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-09-11', '11:08:45'),
(196, 1, 'Add New Action Log', 'Add New Invoice', '2024-09-11', '11:08:45'),
(197, 1, 'Sale One-Time-Product', 'Charging Pin Repair ', '2024-09-11', '11:08:45'),
(198, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 500', '2024-09-11', '11:08:45'),
(199, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 8 - Cash, Profit : Rs. 500, Rs. 500 by 1 ', '2024-09-11', '11:08:45'),
(200, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 8, Total Bill Cost : 0, Total Bill Profit : 500', '2024-09-11', '11:08:45'),
(201, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 8, Rs. 500', '2024-09-11', '11:08:45'),
(202, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 8 - Cash, Payment Method : Cash, Advance : Rs. 500, Rs. 500 by 1 ', '2024-09-11', '11:08:45'),
(203, 0, 'Add Combo Product', 'Add New Combo Product : test', '2024-09-17', '18:48:44'),
(204, 0, 'Update Product', 'Update Product : test', '2024-09-17', '19:17:09'),
(205, 0, 'Update Product', 'Update Product : test', '2024-09-17', '19:17:37'),
(206, 0, 'Update Product', 'Update Product : test', '2024-09-17', '19:17:55'),
(207, 0, 'Update Product', 'Update Product : test', '2024-09-17', '19:18:16'),
(208, 0, 'Delete product from makeProduct table', 'Delete from makeProduct table where product_name = test', '2024-09-17', '19:18:24'),
(209, 0, 'Delete product', 'Delete product : test', '2024-09-17', '19:18:25'),
(210, 0, 'Edit Product', 'Edit Updated : Charging Pin ', '2024-09-17', '19:25:59'),
(211, 0, 'Edit Product', 'Edit Updated : Charging Pin ', '2024-09-17', '19:26:15'),
(212, 0, 'Edit Product', 'Edit Updated : Charging Pin ', '2024-09-17', '19:26:45'),
(213, 0, 'Edit Product', 'Edit Updated : Charging Pin ', '2024-09-17', '19:34:34'),
(214, 0, 'Edit Product', 'Edit Updated : Charging Pin ', '2024-09-17', '19:35:04'),
(215, 0, 'Edit Product', 'Edit Updated : Charging Pin ', '2024-09-17', '19:35:18');

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
(1, 'lakmal', 718366077, '0', '0', 'Admin', '0', 346.10, 0.00, 'admin@132', 1, '', 1, '2024-09-08'),
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

--
-- Dumping data for table `error_log`
--

INSERT INTO `error_log` (`error_id`, `error_code`, `error_message`, `query`, `action`, `action_description`, `date`, `time`, `employee_id`, `status`) VALUES
(1, 1366, 'Incorrect integer value: \'customer Not Found\' for column `ifix`.`customers`.`customer_mobile` at row 1', 'INSERT INTO customers (customer_name, customer_mobile) VALUES (\'ohh\', \'customer Not Found\')', 'Add New Customer', 'ohh', '2024-09-09', '05:35:51', 1, 'fixed'),
(2, 1366, 'Incorrect integer value: \'\' for column `ifix`.`oneTimeProducts_sales`.`invoice_number` at row 1', 'INSERT INTO `oneTimeProducts_sales`(`invoice_number`, `product`, `qty`, `rate`, `amount`, `Worker`) VALUES \n                            (\'\',\'hhhh\',\'1\',\'74.00\',\'74.00\', \'lakmal\')', 'Sale One-Time-Product', 'hhhh', '2024-09-09', '05:35:51', 1, 'fixed'),
(3, 1064, 'You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\' at line 1', 'UPDATE invoice SET cost = 0, profit = 0 WHERE invoice_number = ', 'Update Invoice Total Profit And Cost', 'Invoice Number : , Total Bill Cost : 0, Total Bill Profit : 0', '2024-09-09', '05:35:51', 1, 'fixed'),
(4, 1366, 'Incorrect integer value: \'customer Not Found\' for column `ifix`.`customers`.`customer_mobile` at row 1', 'INSERT INTO customers (customer_name, customer_mobile) VALUES (\'ohh\', \'customer Not Found\')', 'Add New Customer', 'ohh', '2024-09-09', '05:45:52', 1, 'fixed'),
(5, 1064, 'You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'\' at line 1', 'UPDATE invoice SET cost = 0, profit = 74 WHERE invoice_number = ', 'Update Invoice Total Profit And Cost', 'Invoice Number : , Total Bill Cost : 0, Total Bill Profit : 74', '2024-09-09', '05:45:52', 1, 'fixed'),
(6, 1064, 'You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \' \r\n            profit = 130, \r\n            stock_qty = 35, \r\n            has_...\' at line 5', 'UPDATE products \r\n        SET \r\n            product_name = \'test\', \r\n            rate = 200.00, \r\n            cost = , \r\n            profit = 130, \r\n            stock_qty = 35, \r\n            has_stock = 1, \r\n            stock_alert_limit = 60, \r\n            worker_commision = 70.00 \r\n        WHERE product_id = 7', 'Update Product', 'Update Product : test', '2024-09-17', '19:12:22', 0, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoice_number` int(10) NOT NULL,
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

INSERT INTO `invoice` (`invoice_number`, `invoice_description`, `customer_name`, `invoice_date`, `time`, `customer_mobile`, `biller`, `primary_worker`, `total`, `discount`, `advance`, `balance`, `cost`, `profit`, `full_paid`, `paymentMethod`) VALUES
(1, '', 'gftjfu', '2024-09-08', '06:48:45', 75562525, 'lakmal', 'lakmal', 3770.00, 0.00, 3770.00, 0.00, 0.00, 3770.00, 1, 'Cash'),
(2, '', 'Cash', '2024-09-08', '06:54:01', 0, 'lakmal', 'lakmal', 800.00, 0.00, 800.00, 0.00, 500.00, 300.00, 1, 'Cash'),
(3, ' ', 'Cash', '2024-09-10', '08:06:23', 0, 'lakmal', 'lakmal', 450.00, 350.00, 1000.00, -900.00, 300.00, 700.00, 1, 'Cash'),
(4, '', 'Cash', '2024-09-10', '08:17:40', 0, 'lakmal', 'lakmal', 500.00, 0.00, 500.00, 0.00, 200.00, 300.00, 1, 'Cash'),
(5, '', 'Cash', '2024-09-10', '08:28:05', 0, 'lakmal', 'lakmal', 600.00, 0.00, 600.00, 0.00, 0.00, 600.00, 1, 'Cash'),
(6, '', 'Cash', '2024-09-10', '08:31:44', 0, 'lakmal', 'lakmal', 750.00, 0.00, 750.00, 0.00, 300.00, 450.00, 1, 'Cash'),
(7, '', 'Cash', '2024-09-11', '11:06:51', 0, 'lakmal', 'lakmal', 500.00, 0.00, 500.00, 0.00, 150.00, 350.00, 1, 'Cash'),
(8, '', 'Cash', '2024-09-11', '11:08:45', 0, 'lakmal', 'lakmal', 500.00, 0.00, 500.00, 0.00, 0.00, 500.00, 1, 'Cash');

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

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `item_name`, `description`, `cost`, `qty`, `supplier`) VALUES
(11, 'oms 5C batery ', NULL, 350.00, 29, NULL),
(12, 'oms 4c', NULL, 350.00, 4, NULL),
(13, 'Charging Pin ', NULL, 150.00, 29, NULL);

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
(4, 'oms 5C batery', 'Oms 5C 3 month warranty', 1.0000000),
(5, 'oms 5C batery', 'Oms 5C 3 month warranty', 29.0000000),
(6, 'oms 4c', 'oms 4c battery 3 month worrenty ', 1.0000000),
(7, 'Charging Pin', 'Charging Pin ', 1.0000000);

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
(1, 1, 'llolko', 58, 65.00, 3770.00, NULL, NULL, 'uncleared', 'lakmal'),
(2, 4, 'Chargin Port Repart', 1, 500.00, 500.00, 200.00, 300.00, 'cleared', 'lakmal'),
(3, 5, 'M02 Backcover', 1, 600.00, 600.00, NULL, NULL, 'uncleared', 'lakmal'),
(4, 8, 'Charging Pin Repair ', 1, 500.00, 500.00, NULL, NULL, 'uncleared', 'lakmal');

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
(4, 'Oms 5C 3 month warranty', NULL, 29, 800.00, 10500.00, -9700.00, '1', 20, 0.00, NULL, ''),
(5, 'oms 4c battery 3 month worrenty ', NULL, 4, 800.00, 350.00, 450.00, '1', 20, 0.00, NULL, ''),
(6, 'Charging Pin ', NULL, 33, 501.00, 151.00, 250.00, '1', 21, 100.00, NULL, '');

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

--
-- Dumping data for table `salary`
--

INSERT INTO `salary` (`salary_id`, `emp_id`, `amount`, `description`, `date`, `time`) VALUES
(1, 1, 11.10, 'Profit from Invoice Number : <a href=\'/invoice/print.php?id=\'>  </a>', '2024-09-09', '05:45:52'),
(2, 1, 45.00, 'Profit from Invoice Number : <a href=\'/invoice/print.php?id=2\'> 2 </a>', '2024-09-09', '06:54:01'),
(3, 1, 90.00, 'Profit (Invoice Edit) from Invoice Number : <a href=\'/invoice/print.php?id=3\'> 3 </a>', '2024-09-10', '08:10:20'),
(4, 1, 200.00, 'Commission Added for Chargin Port Repart in Invoice Number : <a href=\'/invoice/print.php?id=4\'> 4 </a>', '2024-09-10', '08:20:30');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sales_id` int(5) NOT NULL,
  `invoice_number` int(10) NOT NULL,
  `product` varchar(30) NOT NULL,
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
(1, 2, 'oms charger 25w', NULL, '1', 800.00, 800.00, 500.00, 300.00, 'lakmal'),
(2, 3, 'OMS Micro Cable ', NULL, '1', 450.00, 450.00, 300.00, 150.00, 'lakmal'),
(5, 6, 'Backcover', NULL, '1', 750.00, 750.00, 300.00, 450.00, 'lakmal'),
(6, 7, 'Charging Pin ', NULL, '1', 500.00, 500.00, 150.00, 350.00, 'lakmal');

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
(1, 1, 'repair', '2024-09-11 23:48:00', 1);

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
(33, 'Invoice - Cash In', '8 - Cash, Payment Method : Cash, Advance : Rs. 500', 500, '2024-09-11', '11:08:45', 1);

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
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=216;

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
  MODIFY `error_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_number` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `InvoiceBalPayRecords`
--
ALTER TABLE `InvoiceBalPayRecords`
  MODIFY `InvBalPayRecords_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `makeProduct`
--
ALTER TABLE `makeProduct`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `oneTimeProducts_sales`
--
ALTER TABLE `oneTimeProducts_sales`
  MODIFY `oneTimeProduct_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pettycash`
--
ALTER TABLE `pettycash`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `purchase`
--
ALTER TABLE `purchase`
  MODIFY `purchase_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salary`
--
ALTER TABLE `salary`
  MODIFY `salary_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sales_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `transaction_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

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

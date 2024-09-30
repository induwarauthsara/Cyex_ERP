-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 29, 2024 at 05:31 PM
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
(1, 'Stock Account', 'cash', -5875.00),
(2, 'Company Profit', 'cash', 0.00),
(6, 'Utility Bills', 'cash', 0.00),
(7, 'cash_in_hand', 'cash', 850.00),
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
(215, 0, 'Edit Product', 'Edit Updated : Charging Pin ', '2024-09-17', '19:35:18'),
(216, 0, 'Error Solved', 'Error ID : 6', '2024-09-17', '19:39:42'),
(217, 0, 'Edit Product', 'Edit Updated : Oms 5C 3 month warranty', '2024-09-17', '19:49:26'),
(218, 1, 'Add Combo Product', 'Add New Combo Product : Oms 5c battery(3 month warranty)', '2024-09-18', '06:38:14'),
(219, 1, 'Delete product from makeProduct table', 'Delete from makeProduct table where product_name = Oms 5C 3 month warranty', '2024-09-18', '06:38:27'),
(220, 1, 'Delete product', 'Delete product : Oms 5C 3 month warranty', '2024-09-18', '06:38:27'),
(221, 1, 'Delete product from makeProduct table', 'Delete from makeProduct table where product_name = Charging Pin', '2024-09-18', '06:38:39'),
(222, 1, 'Delete product', 'Delete product : Charging Pin', '2024-09-18', '06:38:39'),
(223, 1, 'Skip OneTimeProduct', 'Skip OneTimeProduct ID : ', '2024-09-18', '06:39:51'),
(224, 1, 'Edit Product', 'Edit Updated : oms 4c battery 3 month worrenty ', '2024-09-18', '07:26:33'),
(225, 1, 'Update Todo Item Status', 'Todo ID: 1', '2024-09-18', '08:11:31'),
(226, 0, 'Edit Product', 'Edit Updated : Oms 5c battery(3 month warranty)', '2024-09-19', '08:06:34'),
(227, 0, 'Add New Invoice', 'Invoice Number : 9, Customer Name : Cash, Date : 2024-09-19, Total : 800, Discount : 0, Advance : 800, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-09-19', '08:07:26'),
(228, 0, 'Add New Action Log', 'Add New Invoice', '2024-09-19', '08:07:26'),
(229, 0, 'Add Product Cost to Stock Account', 'Rs. 160', '2024-09-19', '08:07:26'),
(230, 0, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100', '2024-09-19', '08:07:26'),
(231, 0, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 100 for Oms 5c battery(3 month warranty)', '2024-09-19', '08:07:26'),
(232, 0, 'Fall Sell Product Qty from Stock', 'Fall 1 of Oms 5c battery(3 month warranty) in Stock', '2024-09-19', '08:07:26'),
(233, 0, 'Update Product Has_Stock State', 'Oms 5c battery(3 month warranty) is In Stock', '2024-09-19', '08:07:26'),
(234, 0, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 540', '2024-09-19', '08:07:26'),
(235, 0, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 9 - Cash, Profit : Rs. 540, Rs. 540 by 0 ', '2024-09-19', '08:07:27'),
(236, 0, 'Update Invoice Total Profit And Cost', 'Invoice Number : 9, Total Bill Cost : 260, Total Bill Profit : 540', '2024-09-19', '08:07:27'),
(237, 0, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 9, Rs. 800', '2024-09-19', '08:07:27'),
(238, 0, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 9 - Cash, Payment Method : Cash, Advance : Rs. 800, Rs. 800 by 0 ', '2024-09-19', '08:07:27'),
(239, 0, 'Add New Invoice', 'Invoice Number : 10, Customer Name : Cash, Date : 2024-09-19, Total : 800, Discount : 0, Advance : 800, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-09-19', '08:15:42'),
(240, 0, 'Add New Action Log', 'Add New Invoice', '2024-09-19', '08:15:42'),
(241, 0, 'Add Product Cost to Stock Account', 'Rs. 160', '2024-09-19', '08:15:42'),
(242, 0, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100', '2024-09-19', '08:15:42'),
(243, 0, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 100 for Oms 5c battery(3 month warranty)', '2024-09-19', '08:15:42'),
(244, 0, 'Fall Sell Product Qty from Stock', 'Fall 1 of Oms 5c battery(3 month warranty) in Stock', '2024-09-19', '08:15:42'),
(245, 0, 'Update Product Has_Stock State', 'Oms 5c battery(3 month warranty) is In Stock', '2024-09-19', '08:15:42'),
(246, 0, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 540', '2024-09-19', '08:15:42'),
(247, 0, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 10 - Cash, Profit : Rs. 540, Rs. 540 by 0 ', '2024-09-19', '08:15:42'),
(248, 0, 'Update Invoice Total Profit And Cost', 'Invoice Number : 10, Total Bill Cost : 260, Total Bill Profit : 540', '2024-09-19', '08:15:43'),
(249, 0, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 10, Rs. 800', '2024-09-19', '08:15:43'),
(250, 0, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 10 - Cash, Payment Method : Cash, Advance : Rs. 800, Rs. 800 by 0 ', '2024-09-19', '08:15:43'),
(251, 0, 'Add New Invoice', 'Invoice Number : 11, Customer Name : Cash, Date : 2024-09-19, Total : 800, Discount : 0, Advance : 800, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-09-19', '08:16:37'),
(252, 0, 'Add New Action Log', 'Add New Invoice', '2024-09-19', '08:16:37'),
(253, 0, 'Add Product Cost to Stock Account', 'Rs. 160', '2024-09-19', '08:16:37'),
(254, 0, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100', '2024-09-19', '08:16:37'),
(255, 0, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 100 for Oms 5c battery(3 month warranty)', '2024-09-19', '08:16:37'),
(256, 0, 'Fall Sell Product Qty from Stock', 'Fall 1 of Oms 5c battery(3 month warranty) in Stock', '2024-09-19', '08:16:37'),
(257, 0, 'Update Product Has_Stock State', 'Oms 5c battery(3 month warranty) is In Stock', '2024-09-19', '08:16:37'),
(258, 0, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 540', '2024-09-19', '08:16:38'),
(259, 0, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 11 - Cash, Profit : Rs. 540, Rs. 540 by 0 ', '2024-09-19', '08:16:38'),
(260, 0, 'Update Invoice Total Profit And Cost', 'Invoice Number : 11, Total Bill Cost : 260, Total Bill Profit : 540', '2024-09-19', '08:16:38'),
(261, 0, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 11, Rs. 800', '2024-09-19', '08:16:38'),
(262, 0, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 11 - Cash, Payment Method : Cash, Advance : Rs. 800, Rs. 800 by 0 ', '2024-09-19', '08:16:38'),
(263, 0, 'Add New Invoice', 'Invoice Number : 12, Customer Name : Cash, Date : 2024-09-19, Total : 800, Discount : 0, Advance : 800, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-09-19', '08:19:59'),
(264, 0, 'Add New Action Log', 'Add New Invoice', '2024-09-19', '08:19:59'),
(265, 0, 'Add Product Cost to Stock Account', 'Rs. 160', '2024-09-19', '08:19:59'),
(266, 0, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100', '2024-09-19', '08:19:59'),
(267, 0, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 100 for Oms 5c battery(3 month warranty)', '2024-09-19', '08:19:59'),
(268, 0, 'Add New Sale', 'Add New Sale : Oms 5c battery(3 month warranty)', '2024-09-19', '08:19:59'),
(269, 0, 'Fall Sell Product Qty from Stock', 'Fall 1 of Oms 5c battery(3 month warranty) in Stock', '2024-09-19', '08:20:00'),
(270, 0, 'Update Product Has_Stock State', 'Oms 5c battery(3 month warranty) is In Stock', '2024-09-19', '08:20:00'),
(271, 0, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 540', '2024-09-19', '08:20:00'),
(272, 0, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 12 - Cash, Profit : Rs. 540, Rs. 540 by 0 ', '2024-09-19', '08:20:00'),
(273, 0, 'Update Invoice Total Profit And Cost', 'Invoice Number : 12, Total Bill Cost : 260, Total Bill Profit : 540', '2024-09-19', '08:20:00'),
(274, 0, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 12, Rs. 800', '2024-09-19', '08:20:00'),
(275, 0, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 12 - Cash, Payment Method : Cash, Advance : Rs. 800, Rs. 800 by 0 ', '2024-09-19', '08:20:00'),
(276, 1, 'Update Cash In Hand Amount Manually', 'Update Cash In Hand Amount to Rs.0', '2024-09-19', '08:56:32'),
(277, 1, 'Delete Bank Account', 'Delete Bank Account: BOC', '2024-09-19', '08:57:07'),
(278, 1, 'Create Bank Account', 'Create New Bank Account : Dfcc', '2024-09-19', '08:57:36'),
(279, 1, 'Error Solved', 'Error ID : 8', '2024-09-19', '08:59:36'),
(280, 1, 'Error Solved', 'Error ID : 9', '2024-09-19', '08:59:41'),
(281, 1, 'Error Solved', 'Error ID : 10', '2024-09-19', '08:59:45'),
(282, 1, 'Add Combo Product', 'Add New Combo Product : Display replacement', '2024-09-19', '09:05:35'),
(283, 1, 'Add New Invoice', 'Invoice Number : 1, Customer Name : Cash, Date : 2024-09-19, Total : 5500, Discount : 0, Advance : 5500, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-09-19', '09:07:44'),
(284, 1, 'Add New Action Log', 'Add New Invoice', '2024-09-19', '09:07:44'),
(285, 1, 'Add Product Cost to Stock Account', 'Rs. 2500', '2024-09-19', '09:07:44'),
(286, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 1000', '2024-09-19', '09:07:44'),
(287, 1, 'Add Commision - Update Salary Table', 'Add Commison to Udaya Rs. 1000 for Display replacement', '2024-09-19', '09:07:44'),
(288, 1, 'Add New Sale', 'Add New Sale : Display replacement', '2024-09-19', '09:07:44'),
(289, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of Display replacement in Stock', '2024-09-19', '09:07:44'),
(290, 1, 'Update Product Has_Stock State', 'Display replacement is Out of Stock', '2024-09-19', '09:07:44'),
(291, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 2000', '2024-09-19', '09:07:44'),
(292, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 1 - Cash, Profit : Rs. 2000, Rs. 2000 by 1 ', '2024-09-19', '09:07:44'),
(293, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 1, Total Bill Cost : 3500, Total Bill Profit : 2000', '2024-09-19', '09:07:44'),
(294, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 1, Rs. 5500', '2024-09-19', '09:07:44'),
(295, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 1 - Cash, Payment Method : Cash, Advance : Rs. 5500, Rs. 5500 by 1 ', '2024-09-19', '09:07:44'),
(296, 1, 'Delete product from makeProduct table', 'Delete from makeProduct table where product_name = Display replacement', '2024-09-19', '09:10:05'),
(297, 1, 'Delete product', 'Delete product : Display replacement', '2024-09-19', '09:10:05'),
(298, 1, 'Add Combo Product', 'Add New Combo Product : wireless Keyboard', '2024-09-19', '10:10:28'),
(299, 0, 'Add New Invoice', 'Invoice Number : 2, Customer Name : Cash, Date : 2024-09-19, Total : 0, Discount : 0, Advance : 0, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-09-19', '10:53:54'),
(300, 0, 'Add New Action Log', 'Add New Invoice', '2024-09-19', '10:53:54'),
(301, 0, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 0', '2024-09-19', '10:53:54'),
(302, 0, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 2 - Cash, Profit : Rs. 0, Rs. 0 by 0 ', '2024-09-19', '10:53:54'),
(303, 0, 'Update Invoice Total Profit And Cost', 'Invoice Number : 2, Total Bill Cost : 0, Total Bill Profit : 0', '2024-09-19', '10:53:54'),
(304, 0, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 2, Rs. 0', '2024-09-19', '10:53:54'),
(305, 0, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 2 - Cash, Payment Method : Cash, Advance : Rs. 0, Rs. 0 by 0 ', '2024-09-19', '10:53:55'),
(306, 0, 'Add New Invoice', 'Invoice Number : 3, Customer Name : Cash, Date : 2024-09-19, Total : 0, Discount : 0, Advance : 0, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-09-19', '10:53:55'),
(307, 0, 'Add New Action Log', 'Add New Invoice', '2024-09-19', '10:53:55'),
(308, 0, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 0', '2024-09-19', '10:53:55'),
(309, 0, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 3 - Cash, Profit : Rs. 0, Rs. 0 by 0 ', '2024-09-19', '10:53:55'),
(310, 0, 'Update Invoice Total Profit And Cost', 'Invoice Number : 3, Total Bill Cost : 0, Total Bill Profit : 0', '2024-09-19', '10:53:55'),
(311, 0, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 3, Rs. 0', '2024-09-19', '10:53:55'),
(312, 0, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 3 - Cash, Payment Method : Cash, Advance : Rs. 0, Rs. 0 by 0 ', '2024-09-19', '10:53:55'),
(313, 0, 'Add New Invoice', 'Invoice Number : 4, Customer Name : Cash, Date : 2024-09-19, Total : 0, Discount : 0, Advance : 0, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-09-19', '10:53:56'),
(314, 0, 'Add New Action Log', 'Add New Invoice', '2024-09-19', '10:53:56'),
(315, 0, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 0', '2024-09-19', '10:53:56'),
(316, 0, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 4 - Cash, Profit : Rs. 0, Rs. 0 by 0 ', '2024-09-19', '10:53:56'),
(317, 0, 'Update Invoice Total Profit And Cost', 'Invoice Number : 4, Total Bill Cost : 0, Total Bill Profit : 0', '2024-09-19', '10:53:56'),
(318, 0, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 4, Rs. 0', '2024-09-19', '10:53:56'),
(319, 0, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 4 - Cash, Payment Method : Cash, Advance : Rs. 0, Rs. 0 by 0 ', '2024-09-19', '10:53:56'),
(320, 1, 'Add New Invoice', 'Invoice Number : 5, Customer Name : Cash, Date : 2024-09-23, Total : 800, Discount : 0, Advance : 800, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-09-23', '11:00:44'),
(321, 1, 'Add New Action Log', 'Add New Invoice', '2024-09-23', '11:00:44'),
(322, 1, 'Add Product Cost to Stock Account', 'Rs. 350', '2024-09-23', '11:00:44'),
(323, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-09-23', '11:00:44'),
(324, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for oms 4c battery 3 month worrenty ', '2024-09-23', '11:00:44'),
(325, 1, 'Add New Sale', 'Add New Sale : oms 4c battery 3 month worrenty ', '2024-09-23', '11:00:44'),
(326, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of oms 4c battery 3 month worrenty  in Stock', '2024-09-23', '11:00:44'),
(327, 1, 'Update Product Has_Stock State', 'oms 4c battery 3 month worrenty  is In Stock', '2024-09-23', '11:00:44'),
(328, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 450', '2024-09-23', '11:00:44'),
(329, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 5 - Cash, Profit : Rs. 450, Rs. 450 by 1 ', '2024-09-23', '11:00:44'),
(330, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 5, Total Bill Cost : 350, Total Bill Profit : 450', '2024-09-23', '11:00:44'),
(331, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 5, Rs. 800', '2024-09-23', '11:00:44'),
(332, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 5 - Cash, Payment Method : Cash, Advance : Rs. 800, Rs. 800 by 1 ', '2024-09-23', '11:00:44'),
(333, 1, 'Update Cash In Hand Amount Manually', 'Update Cash In Hand Amount to Rs.00', '2024-09-27', '10:48:01'),
(334, 1, 'Add Combo Product', 'Add New Combo Product : Oms X3 Speeker', '2024-09-27', '10:53:49'),
(335, 1, 'Add Combo Product', 'Add New Combo Product : sy 718 Speeker', '2024-09-27', '10:54:47'),
(336, 1, 'Add Combo Product', 'Add New Combo Product : Car holder JE027', '2024-09-27', '10:56:06'),
(337, 1, 'Add Combo Product', 'Add New Combo Product : bike holder By555', '2024-09-27', '10:57:09'),
(338, 1, 'Add Combo Product', 'Add New Combo Product : Car holder Jx033', '2024-09-27', '10:58:05'),
(339, 1, 'Add Combo Product', 'Add New Combo Product : Car Holder JX044', '2024-09-27', '10:58:49'),
(340, 1, 'Add Combo Product', 'Add New Combo Product : Bike holder Y02', '2024-09-27', '10:59:32'),
(341, 1, 'Add Combo Product', 'Add New Combo Product : Car holder MDe17', '2024-09-27', '11:00:17'),
(342, 1, 'Add Combo Product', 'Add New Combo Product : Car holder onetouch', '2024-09-27', '11:01:09'),
(343, 1, 'Add Combo Product', 'Add New Combo Product : Car Charger JKXC86', '2024-09-27', '11:02:11'),
(344, 1, 'Add Combo Product', 'Add New Combo Product : Car charger A931', '2024-09-27', '11:02:59'),
(345, 1, 'Add Combo Product', 'Add New Combo Product : OSg09 Car charger', '2024-09-27', '11:04:49'),
(346, 1, 'Add Combo Product', 'Add New Combo Product : Samsung car adapter micro', '2024-09-27', '11:08:16'),
(347, 1, 'Add Combo Product', 'Add New Combo Product : A903 Car charger', '2024-09-27', '11:08:55'),
(348, 1, 'Add Combo Product', 'Add New Combo Product : Om208 Car charger', '2024-09-27', '11:44:50'),
(349, 1, 'Add Combo Product', 'Add New Combo Product : om218 Car Charger', '2024-09-27', '11:46:04'),
(350, 1, 'Add Combo Product', 'Add New Combo Product : x6 Wireless mouse', '2024-09-27', '11:48:03'),
(351, 1, 'Add Combo Product', 'Add New Combo Product : M20 wire Mouse', '2024-09-27', '11:49:18'),
(352, 1, 'Add Combo Product', 'Add New Combo Product : m3 wire mouse', '2024-09-27', '11:49:57'),
(353, 1, 'Add Combo Product', 'Add New Combo Product : jm029 mouse', '2024-09-27', '11:50:30'),
(354, 1, 'Add Combo Product', 'Add New Combo Product : Jr1 mouse', '2024-09-27', '11:51:11'),
(355, 1, 'Add Combo Product', 'Add New Combo Product : JR2 mouse wireless', '2024-09-27', '11:51:56'),
(356, 1, 'Add Combo Product', 'Add New Combo Product : flexible keyboard', '2024-09-27', '11:52:56'),
(357, 1, 'Add Combo Product', 'Add New Combo Product : m100 hp mouse', '2024-09-27', '11:53:26'),
(358, 1, 'Add Combo Product', 'Add New Combo Product : 2.4 wireless mouse', '2024-09-27', '11:54:33'),
(359, 1, 'Add Combo Product', 'Add New Combo Product : sony mouse', '2024-09-27', '11:55:00'),
(360, 1, 'Add Combo Product', 'Add New Combo Product : yayi mouse', '2024-09-27', '11:56:47'),
(361, 1, 'Add Combo Product', 'Add New Combo Product : 20m mouse', '2024-09-27', '11:57:30'),
(362, 1, 'Add Combo Product', 'Add New Combo Product : 65W pd type C charger', '2024-09-27', '11:59:03'),
(363, 1, 'Add Combo Product', 'Add New Combo Product : note 10 charger', '2024-09-27', '11:59:45'),
(364, 1, 'Add Combo Product', 'Add New Combo Product : oms Ch32 01 (month warranty)', '2024-09-27', '12:01:22'),
(365, 1, 'Add Combo Product', 'Add New Combo Product : oms Ch31 35W charger', '2024-09-27', '12:02:19'),
(366, 1, 'Add Combo Product', 'Add New Combo Product : 45W type C adepter', '2024-09-27', '12:03:17'),
(367, 1, 'Edit Product', 'Edit Updated : oms Ch32 01 (month warranty)', '2024-09-27', '12:03:57'),
(368, 1, 'Edit Product', 'Edit Updated : oms Ch32 01 (month warranty)', '2024-09-27', '12:05:14'),
(369, 1, 'Add Combo Product', 'Add New Combo Product : 15w Samsung travel adapter', '2024-09-27', '12:11:26'),
(370, 1, 'Add Combo Product', 'Add New Combo Product : 25W pd adapter with cable', '2024-09-27', '12:12:24'),
(371, 1, 'Add Combo Product', 'Add New Combo Product : sm12 sinha micro charger', '2024-09-27', '12:13:15'),
(372, 1, 'Add Combo Product', 'Add New Combo Product : osy08 pd 20w charger lighting', '2024-09-27', '12:15:45'),
(373, 1, 'Add Combo Product', 'Add New Combo Product : Osc26 onesom charger', '2024-09-27', '12:16:57'),
(374, 1, 'Add Combo Product', 'Add New Combo Product : om310 10w adapter', '2024-09-27', '12:18:23'),
(375, 1, 'Add Combo Product', 'Add New Combo Product : 25W samsung pd adapter', '2024-09-27', '12:19:05'),
(376, 1, 'Add Combo Product', 'Add New Combo Product : md812b apple 5W charger', '2024-09-27', '12:20:13'),
(377, 1, 'Add Combo Product', 'Add New Combo Product : 20W type c apple adapter 06 month warranty', '2024-09-27', '12:22:45'),
(378, 1, 'Add Combo Product', 'Add New Combo Product : 20W C apple adapter 1 month warranty', '2024-09-27', '12:26:07'),
(379, 1, 'Add Combo Product', 'Add New Combo Product : xpc25 type c normal charger', '2024-09-27', '12:33:34'),
(380, 1, 'Add Combo Product', 'Add New Combo Product : Xpc25 lightning normal charger', '2024-09-27', '12:34:36'),
(381, 1, 'Add Combo Product', 'Add New Combo Product : XpC 30 lightning charger', '2024-09-27', '12:36:21'),
(382, 1, 'Add Combo Product', 'Add New Combo Product : SL60 Samsung fast charger', '2024-09-27', '12:38:09'),
(383, 1, 'Add Combo Product', 'Add New Combo Product : Xpc type c normal charger', '2024-09-27', '12:38:58'),
(384, 1, 'Add Combo Product', 'Add New Combo Product : Xpc25 micro normal charger', '2024-09-27', '12:46:42'),
(385, 1, 'Edit Product', 'Edit Updated : Xpc25 micro normal charger', '2024-09-27', '12:47:11'),
(386, 1, 'Add New Commission Record', 'Commission Added for M02 Backcover in Invoice Number : <a href=\'/invoice/print.php?id=5\'> 5 </a>', '2024-09-27', '12:48:02'),
(387, 1, 'Fall OneTimeProduct Cost from Company Profit', 'Fall Rs.0 in Company Profit Account for Repair/Service ID : 3', '2024-09-27', '12:48:02'),
(388, 1, 'Transaction Log', 'Transaction Type : Fall OneTimeProduct Cost from Company Profit, description : Fall Rs.0 in Company Profit Account for Repair/Service ID : 3, Rs. 0 by 1 ', '2024-09-27', '12:48:02'),
(389, 1, 'Update Invoice Cost and Profit', 'Update Invoice (ID:5) for Repair/Service ID : 3', '2024-09-27', '12:48:02'),
(390, 1, 'Update OneTimeProduct Sale', 'Update OneTimeProduct (ID:3) for Invoice ID : 5, Cost : 0, Profit : 600.00, worker : lakmal', '2024-09-27', '12:48:02'),
(391, 1, 'Add Salary Commission', 'Increase Salary of lakmal for M02 Backcover by Rs.0', '2024-09-27', '12:48:02'),
(392, 1, 'Transaction Log', 'Transaction Type : Add Salary Commission, description : Increase Salary of lakmal for M02 Backcover by Rs.0, Rs. 0 by 1 ', '2024-09-27', '12:48:02'),
(393, 1, 'Skip OneTimeProduct', 'Skip OneTimeProduct ID : ', '2024-09-27', '12:48:09'),
(394, 0, 'Update Cash In Hand Amount Manually', 'Update Cash In Hand Amount to Rs.3', '2024-09-27', '17:09:54'),
(395, 0, 'Update Cash In Hand Amount Manually', 'Update Cash In Hand Amount to Rs.0', '2024-09-27', '17:10:22'),
(396, 0, 'Update Bank Account', 'Update Dfcc Bank Account Name to  Amount to Dfcc and Rs.501.00', '2024-09-27', '17:12:05'),
(397, 0, 'Update Bank Account', 'Update Dfcc Bank Account Name to  Amount to Dfcc and Rs.500.00', '2024-09-27', '17:12:29'),
(398, 0, 'Error Solved', 'Error ID : 7', '2024-09-27', '17:12:32'),
(399, 0, 'Error Solved', 'Error ID : 11', '2024-09-27', '17:12:36'),
(400, 1, 'Edit Product', 'Edit Updated : XpC 30 lightning charger', '2024-09-28', '08:34:17'),
(401, 1, 'Add Combo Product', 'Add New Combo Product : Om418 type-c chager', '2024-09-28', '08:37:09'),
(402, 1, 'Add Combo Product', 'Add New Combo Product : Om418 Micro chager', '2024-09-28', '08:39:01'),
(403, 1, 'Add Combo Product', 'Add New Combo Product : Om418 Lightning', '2024-09-28', '08:40:11'),
(404, 1, 'Add Combo Product', 'Add New Combo Product : Xpc30 Micro chager', '2024-09-28', '08:43:16'),
(405, 1, 'Edit Product', 'Edit Updated : Osc26 onesom charger', '2024-09-28', '08:44:41'),
(406, 1, 'Add Combo Product', 'Add New Combo Product : Om401 TYPE-C Chager', '2024-09-28', '08:47:10'),
(407, 1, 'Add Combo Product', 'Add New Combo Product : Om409 C to C Chager', '2024-09-28', '08:50:11'),
(408, 1, 'Add Combo Product', 'Add New Combo Product : OsW01 10000Mah Powerbank', '2024-09-28', '08:52:38'),
(409, 1, 'Add Combo Product', 'Add New Combo Product : V12 10000Mah Powerbank', '2024-09-28', '08:54:03'),
(410, 1, 'Add Combo Product', 'Add New Combo Product : V22 20000Mah Powerbank', '2024-09-28', '08:55:14'),
(411, 1, 'Add Combo Product', 'Add New Combo Product : OSD24', '2024-09-28', '08:56:11'),
(412, 1, 'Add Combo Product', 'Add New Combo Product : A5 Airpods PRO', '2024-09-28', '08:57:10'),
(413, 1, 'Add Combo Product', 'Add New Combo Product : HE05 NeckBand', '2024-09-28', '08:59:03'),
(414, 1, 'Add Combo Product', 'Add New Combo Product : V200 Wirless Headphone', '2024-09-28', '09:01:46'),
(415, 1, 'Add Combo Product', 'Add New Combo Product : 3D Earphonre ', '2024-09-28', '09:14:21'),
(416, 1, 'Add Combo Product', 'Add New Combo Product : JD-808 Wird Headphone ', '2024-09-28', '09:19:26'),
(417, 1, 'Add Combo Product', 'Add New Combo Product : AV-92 Vocal Microphone', '2024-09-28', '09:21:52'),
(418, 1, 'Add Combo Product', 'Add New Combo Product : KSC-494', '2024-09-28', '09:25:06'),
(419, 1, 'Add Combo Product', 'Add New Combo Product : XXG-152', '2024-09-28', '09:26:25'),
(420, 1, 'Add Combo Product', 'Add New Combo Product : A201 Type-C Chager', '2024-09-28', '09:28:37'),
(421, 1, 'Add Combo Product', 'Add New Combo Product : P10 SAMDSUNG Chager', '2024-09-28', '09:47:32'),
(422, 1, 'Add Combo Product', 'Add New Combo Product : Belkin Iphone case ', '2024-09-28', '09:57:46'),
(423, 1, 'Add New Invoice', 'Invoice Number : 6, Customer Name : Cash, Date : 2024-09-28, Total : 850, Discount : 0, Advance : 0, Balance : 850.00, Full Paid : 0, Payment Method : Cash', '2024-09-28', '09:58:37'),
(424, 1, 'Add New Action Log', 'Add New Invoice', '2024-09-28', '09:58:37'),
(425, 1, 'Add Product Cost to Stock Account', 'Rs. 400', '2024-09-28', '09:58:37');
INSERT INTO `action_log` (`action_id`, `employee_id`, `action`, `description`, `date`, `time`) VALUES
(426, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-09-28', '09:58:37'),
(427, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for Xpc25 micro normal charger', '2024-09-28', '09:58:37'),
(428, 1, 'Add New Sale', 'Add New Sale : Xpc25 micro normal charger', '2024-09-28', '09:58:37'),
(429, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of Xpc25 micro normal charger in Stock', '2024-09-28', '09:58:37'),
(430, 1, 'Update Product Has_Stock State', 'Xpc25 micro normal charger is In Stock', '2024-09-28', '09:58:37'),
(431, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. -400', '2024-09-28', '09:58:37'),
(432, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 6 - Cash, Profit : Rs. -400, Rs. -400 by 1 ', '2024-09-28', '09:58:37'),
(433, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 6, Total Bill Cost : 400, Total Bill Profit : -400', '2024-09-28', '09:58:37'),
(434, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 6, Rs. 0', '2024-09-28', '09:58:37'),
(435, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 6 - Cash, Payment Method : Cash, Advance : Rs. 0, Rs. 0 by 1 ', '2024-09-28', '09:58:37'),
(436, 1, 'Add Biller Profit to Employee Table', 'send biller Profit : lakmal Rs. 67.5', '2024-09-28', '09:59:06'),
(437, 1, 'Employee Salary Paid - Update Salary Table', 'Employee ID: 1, Rs. 67.5', '2024-09-28', '09:59:06'),
(438, 1, 'Update Company Profit when Invoice Balance Payment', 'Company Profit : Rs. 382.5, Invoice 6 Balance Payment', '2024-09-28', '09:59:06'),
(439, 1, 'Add Fund to Invoice - Add Invoice Balance Pay Records', 'Invoice Number : 6, Payment  : 850.00, New Invoice Balance : 0 ', '2024-09-28', '09:59:06'),
(440, 1, 'Add Fund to Invoice - Update Invoice Table', 'Invoice Number : 6, Payment  : 850.00, New Invoice Balance : 0 ', '2024-09-28', '09:59:06'),
(441, 1, 'Add Combo Product', 'Add New Combo Product : Iphone Magsafe Case ', '2024-09-28', '10:01:20'),
(442, 1, 'Add Combo Product', 'Add New Combo Product : Microphone Stand', '2024-09-28', '10:03:32'),
(443, 1, 'Add Combo Product', 'Add New Combo Product : Type-c Microphone ', '2024-09-28', '10:04:31'),
(444, 1, 'Add Combo Product', 'Add New Combo Product : Boya Microphone BY-M1', '2024-09-28', '10:05:43'),
(445, 1, 'Add Combo Product', 'Add New Combo Product : Lightning Microphone ', '2024-09-28', '10:09:23'),
(446, 1, 'Add Combo Product', 'Add New Combo Product : AKG Normal Handsfree', '2024-09-28', '10:10:38'),
(447, 1, 'Add Combo Product', 'Add New Combo Product : Lightning Normal Cable ', '2024-09-28', '10:12:19'),
(448, 1, 'Add Combo Product', 'Add New Combo Product : Beats Headset', '2024-09-28', '10:16:46'),
(449, 1, 'Add Combo Product', 'Add New Combo Product : Baseus 100W cable', '2024-09-28', '10:20:23'),
(450, 1, 'Add Combo Product', 'Add New Combo Product : RC-C053 Lightning to type-c', '2024-09-28', '10:22:37'),
(451, 1, 'Add Combo Product', 'Add New Combo Product : OM-751 C to C cable', '2024-09-28', '10:24:50'),
(452, 1, 'Add Combo Product', 'Add New Combo Product : D20C C to C cable', '2024-09-28', '10:26:11'),
(453, 1, 'Add Combo Product', 'Add New Combo Product : OS-A04 Type-c to Lightning Cable', '2024-09-28', '10:29:28'),
(454, 1, 'Edit Product', 'Edit Updated : Beats Headset', '2024-09-28', '10:30:00'),
(455, 1, 'Add Combo Product', 'Add New Combo Product : OS-A22 C to C cable', '2024-09-28', '10:31:52'),
(456, 1, 'Add Combo Product', 'Add New Combo Product : K1000 MINI Keyboard', '2024-09-28', '10:33:52'),
(457, 1, 'Add Combo Product', 'Add New Combo Product : OS-A03 C to C cable', '2024-09-28', '10:35:05'),
(458, 0, 'Error Solved', 'Error ID : 14', '2024-09-28', '10:43:10'),
(459, 0, 'Error Solved', 'Error ID : 13', '2024-09-28', '10:43:15'),
(460, 0, 'Error Solved', 'Error ID : 12', '2024-09-28', '10:43:19'),
(461, 1, 'Add Combo Product', 'Add New Combo Product : YS-859 Ligthning to C cable', '2024-09-28', '10:46:23'),
(462, 1, 'Add Combo Product', 'Add New Combo Product : OSA28 Lightning to C cabel ', '2024-09-28', '10:48:13'),
(463, 1, 'Add Combo Product', 'Add New Combo Product : Lightning to C cable ', '2024-09-28', '11:08:59'),
(464, 1, 'Edit Product', 'Edit Updated : Lightning to C cable Normal', '2024-09-28', '11:09:19'),
(465, 1, 'Add Combo Product', 'Add New Combo Product : Samsung Org Handsfree', '2024-09-28', '11:30:06'),
(466, 1, 'Add Combo Product', 'Add New Combo Product : OM-154 Micro Cable ', '2024-09-29', '07:46:35'),
(467, 1, 'Add Combo Product', 'Add New Combo Product : OM-154 Type-c Cable', '2024-09-29', '07:47:45'),
(468, 1, 'Add Combo Product', 'Add New Combo Product : OM-154 Lightning Cable', '2024-09-29', '07:48:49'),
(469, 1, 'Add Combo Product', 'Add New Combo Product : R162 Earphone', '2024-09-29', '07:53:12'),
(470, 1, 'Add Combo Product', 'Add New Combo Product : SY-S8 Earphone ', '2024-09-29', '07:54:20'),
(471, 1, 'Add Combo Product', 'Add New Combo Product : V13 Earphone ', '2024-09-29', '07:56:22'),
(472, 1, 'Add Combo Product', 'Add New Combo Product : PA113 Headset ', '2024-09-29', '07:58:23'),
(473, 1, 'Add Combo Product', 'Add New Combo Product : OS-A16', '2024-09-29', '08:00:34'),
(474, 1, 'Add Combo Product', 'Add New Combo Product : BQZ-159 Headset', '2024-09-29', '08:02:38'),
(475, 1, 'Add Combo Product', 'Add New Combo Product : OS-X02 Headset ', '2024-09-29', '08:04:49'),
(476, 1, 'Add Combo Product', 'Add New Combo Product : OS-X04 Headset', '2024-09-29', '08:06:08'),
(477, 1, 'Add Combo Product', 'Add New Combo Product : D2 Headset ', '2024-09-29', '08:08:07'),
(478, 1, 'Add Combo Product', 'Add New Combo Product : OS-E02 Lightning Earphone', '2024-09-29', '08:09:35'),
(479, 1, 'Add Combo Product', 'Add New Combo Product : B-10 Headset', '2024-09-29', '08:11:49'),
(480, 1, 'Add Combo Product', 'Add New Combo Product : PA200 Headset ', '2024-09-29', '08:15:35'),
(481, 1, 'Add Combo Product', 'Add New Combo Product : Om-508 Headset ', '2024-09-29', '08:19:41'),
(482, 1, 'Add Combo Product', 'Add New Combo Product : OM515 Headset ', '2024-09-29', '08:22:39'),
(483, 1, 'Edit Product', 'Edit Updated : R162 Earphone', '2024-09-29', '08:24:25'),
(484, 1, 'Edit Product', 'Edit Updated : Om-508 Headset ', '2024-09-29', '08:27:15'),
(485, 1, 'Add Combo Product', 'Add New Combo Product : U23 Headset ', '2024-09-29', '08:30:51'),
(486, 1, 'Add Combo Product', 'Add New Combo Product : NA-18 Hradset', '2024-09-29', '08:32:13'),
(487, 1, 'Add Combo Product', 'Add New Combo Product : NA-20 Headset ', '2024-09-29', '08:32:50'),
(488, 1, 'Add Combo Product', 'Add New Combo Product : NA-21 Headset ', '2024-09-29', '08:33:39'),
(489, 1, 'Add Combo Product', 'Add New Combo Product : M-877 Headset', '2024-09-29', '08:34:29'),
(490, 1, 'Add Combo Product', 'Add New Combo Product : DX06 AUX Cable', '2024-09-29', '08:51:15'),
(491, 1, 'Add Combo Product', 'Add New Combo Product : CA-48 Type-c Cable', '2024-09-29', '09:04:31'),
(492, 1, 'Add Combo Product', 'Add New Combo Product : CA-T126 Micro Cable', '2024-09-29', '09:06:28'),
(493, 1, 'Add Combo Product', 'Add New Combo Product : OM-168 Micro Cable ', '2024-09-29', '09:08:46'),
(494, 1, 'Add Combo Product', 'Add New Combo Product : SM-07 Micro Cable', '2024-09-29', '09:10:56'),
(495, 1, 'Add Combo Product', 'Add New Combo Product : IV-CA53 Micro Cable', '2024-09-29', '09:12:57'),
(496, 1, 'Add Combo Product', 'Add New Combo Product : D01V Micro Cable', '2024-09-29', '09:14:36'),
(497, 0, 'Error Solved', 'Error ID : 17', '2024-09-29', '09:15:04'),
(498, 0, 'Error Solved', 'Error ID : 16', '2024-09-29', '09:15:22'),
(499, 1, 'Add Combo Product', 'Add New Combo Product : OM-150 Micro Cable ', '2024-09-29', '09:19:48'),
(500, 1, 'Add Combo Product', 'Add New Combo Product : GL060 AUX to Lightning Cable ', '2024-09-29', '09:37:35'),
(501, 1, 'Add Combo Product', 'Add New Combo Product : YS-A14 AUX Cable ', '2024-09-29', '09:38:45'),
(502, 1, 'Add Combo Product', 'Add New Combo Product : AMP02667 49mm Watch Strap', '2024-09-29', '09:43:15'),
(503, 1, 'Add Combo Product', 'Add New Combo Product : V60 Watch Strap', '2024-09-29', '09:45:06'),
(504, 1, 'Add Combo Product', 'Add New Combo Product : C10 Watch Case and Strap', '2024-09-29', '09:47:21'),
(505, 1, 'Add Combo Product', 'Add New Combo Product : P31 Watch protector', '2024-09-29', '09:49:58'),
(506, 1, 'Add Combo Product', 'Add New Combo Product : SX-39 OTG USB', '2024-09-29', '09:51:57'),
(507, 1, 'Add Combo Product', 'Add New Combo Product : JX-151 Lightning Cable ', '2024-09-29', '09:57:27'),
(508, 1, 'Add Combo Product', 'Add New Combo Product : OM-750 Lightning Cable', '2024-09-29', '10:05:10'),
(509, 1, 'Add Combo Product', 'Add New Combo Product : X87 Lightning Cable', '2024-09-29', '10:06:48'),
(510, 1, 'Add Combo Product', 'Add New Combo Product : D01V Ligthning  Cable', '2024-09-29', '10:09:42'),
(511, 1, 'Add Combo Product', 'Add New Combo Product : CB-33  Lightning Cable ', '2024-09-29', '10:11:07'),
(512, 1, 'Add Combo Product', 'Add New Combo Product : OS-A02 Lightning Cable ', '2024-09-29', '10:13:39'),
(513, 1, 'Add Combo Product', 'Add New Combo Product : OM-150 Lightning Cable ', '2024-09-29', '10:16:00'),
(514, 1, 'Add Combo Product', 'Add New Combo Product : K62 Watch Strap ', '2024-09-29', '10:17:32'),
(515, 1, 'Add Combo Product', 'Add New Combo Product : SM-07 Lightninjg Cable ', '2024-09-29', '10:20:45'),
(516, 1, 'Add Combo Product', 'Add New Combo Product : OM-705 Lightning Cable ', '2024-09-29', '10:22:10'),
(517, 1, 'Add Combo Product', 'Add New Combo Product : A1481 Lightning Cable ', '2024-09-29', '10:24:00'),
(518, 1, 'Add Combo Product', 'Add New Combo Product : A1703 Lightning to Type-C Cable ', '2024-09-29', '10:25:51'),
(519, 1, 'Add Combo Product', 'Add New Combo Product : CA-46 Type-c Cable ', '2024-09-29', '10:29:38'),
(520, 1, 'Add Combo Product', 'Add New Combo Product : CB07 Type-C Cable', '2024-09-29', '10:34:57'),
(521, 1, 'Add Combo Product', 'Add New Combo Product : YS-858 Type-C Cable ', '2024-09-29', '10:37:43'),
(522, 1, 'Add Combo Product', 'Add New Combo Product : OS-V02 Type-C Cable ', '2024-09-29', '10:39:24'),
(523, 1, 'Add Combo Product', 'Add New Combo Product : A1997 C to C Cable ', '2024-09-29', '10:40:39'),
(524, 1, 'Add Combo Product', 'Add New Combo Product : OS-A24 Type-c ', '2024-09-29', '10:43:40'),
(525, 1, 'Edit Product', 'Edit Updated : OS-V02 Type-C Cable ', '2024-09-29', '10:45:20'),
(526, 1, 'Add Combo Product', 'Add New Combo Product : Power Max Cable Type-C', '2024-09-29', '10:47:25'),
(527, 1, 'Add Combo Product', 'Add New Combo Product : SM-07 Type-C Cable ', '2024-09-29', '10:48:52'),
(528, 1, 'Add Combo Product', 'Add New Combo Product : OM-168 Type-c Cable ', '2024-09-29', '10:51:27'),
(529, 1, 'Add Combo Product', 'Add New Combo Product : XXG-153 Type-C Cable ', '2024-09-29', '10:53:24'),
(530, 1, 'Add Combo Product', 'Add New Combo Product : Iphone 6 Backcover', '2024-09-29', '11:02:02'),
(531, 1, 'Add Combo Product', 'Add New Combo Product : Iphone 7/8/se Backcover', '2024-09-29', '11:04:43'),
(532, 1, 'Add Combo Product', 'Add New Combo Product : Iphone 6+', '2024-09-29', '11:05:34'),
(533, 1, 'Add Combo Product', 'Add New Combo Product : Iphone 12/12 pro/12 pro max', '2024-09-29', '11:08:48'),
(534, 1, 'Edit Product', 'Edit Updated : Iphone 12/12 pro', '2024-09-29', '11:10:10'),
(535, 1, 'Add Combo Product', 'Add New Combo Product : Iphone12 pro max ', '2024-09-29', '11:10:49'),
(536, 1, 'Add Combo Product', 'Add New Combo Product : Iphone 12 Mini ', '2024-09-29', '11:11:25'),
(537, 1, 'Add Combo Product', 'Add New Combo Product : Iphone 11 pro max', '2024-09-29', '11:12:46'),
(538, 1, 'Add Combo Product', 'Add New Combo Product : Iphone 11', '2024-09-29', '11:13:28'),
(539, 1, 'Add Combo Product', 'Add New Combo Product : Iphone 13 pro ', '2024-09-29', '11:14:12'),
(540, 1, 'Add Combo Product', 'Add New Combo Product : Iphone 7+ ', '2024-09-29', '11:15:15'),
(541, 1, 'Edit Product', 'Edit Updated : Iphone 7  ', '2024-09-29', '11:15:37'),
(542, 1, 'Add Combo Product', 'Add New Combo Product : Iphone 11 pro ', '2024-09-29', '11:16:12'),
(543, 1, 'Edit Product', 'Edit Updated : Iphone 11 pro ', '2024-09-29', '11:19:02'),
(544, 1, 'Edit Product', 'Edit Updated : Iphone 11', '2024-09-29', '11:19:31'),
(545, 1, 'Edit Product', 'Edit Updated : Iphone 11 pro max', '2024-09-29', '11:19:42'),
(546, 1, 'Add Combo Product', 'Add New Combo Product : Iphone X/Xs', '2024-09-29', '11:22:12'),
(547, 1, 'Add Combo Product', 'Add New Combo Product : Iphone Xr', '2024-09-29', '11:23:00'),
(548, 1, 'Add Combo Product', 'Add New Combo Product : Iphone Xs Max ', '2024-09-29', '11:23:33'),
(549, 1, 'Edit Product', 'Edit Updated : Iphone 11 pro max', '2024-09-29', '11:24:12'),
(550, 1, 'Add Combo Product', 'Add New Combo Product : Iphone 13', '2024-09-29', '11:25:15'),
(551, 1, 'Add Combo Product', 'Add New Combo Product : Iphone 14', '2024-09-29', '11:25:54'),
(552, 1, 'Add Combo Product', 'Add New Combo Product : IPhone 14 plus', '2024-09-29', '11:26:57'),
(553, 1, 'Add Combo Product', 'Add New Combo Product : Iphone 14 pro max', '2024-09-29', '11:27:33'),
(554, 1, 'Add Combo Product', 'Add New Combo Product : IPhone 14 pro', '2024-09-29', '11:28:12'),
(555, 1, 'Add Combo Product', 'Add New Combo Product : Iphone 15', '2024-09-29', '11:30:03'),
(556, 1, 'Add Combo Product', 'Add New Combo Product : Iphone 15 pro', '2024-09-29', '11:30:59'),
(557, 1, 'Add Combo Product', 'Add New Combo Product : Iphone 15 Plus ', '2024-09-29', '11:31:40'),
(558, 1, 'Add Combo Product', 'Add New Combo Product : Iphone 15 pro max', '2024-09-29', '11:32:52'),
(559, 1, 'Add Combo Product', 'Add New Combo Product : Redmi note 7', '2024-09-29', '11:41:02'),
(560, 1, 'Add Combo Product', 'Add New Combo Product : Redmi 8/8a', '2024-09-29', '11:42:04'),
(561, 1, 'Add Combo Product', 'Add New Combo Product : Redmi note 8', '2024-09-29', '11:43:09'),
(562, 1, 'Add Combo Product', 'Add New Combo Product : Redmi 9A ', '2024-09-29', '11:44:26'),
(563, 1, 'Add Combo Product', 'Add New Combo Product : Redmi 9c ', '2024-09-29', '11:46:06'),
(564, 1, 'Add Combo Product', 'Add New Combo Product : Redmi 9', '2024-09-29', '11:47:32'),
(565, 1, 'Add Combo Product', 'Add New Combo Product : Redmi 10c ', '2024-09-29', '11:49:21'),
(566, 1, 'Add Combo Product', 'Add New Combo Product : Redmi note 9 pro', '2024-09-29', '11:51:16'),
(567, 1, 'Add Combo Product', 'Add New Combo Product : redmi Note 9', '2024-09-29', '11:52:38'),
(568, 1, 'Add Combo Product', 'Add New Combo Product : Redmi 9T', '2024-09-29', '11:54:39'),
(569, 1, 'Add Combo Product', 'Add New Combo Product : Redmi note 8 pro ', '2024-09-29', '11:57:23'),
(570, 1, 'Add Combo Product', 'Add New Combo Product : Redmi note 10 4G', '2024-09-29', '11:59:29'),
(571, 1, 'Add Combo Product', 'Add New Combo Product : Redmi note 10 pro 4G', '2024-09-29', '12:00:23'),
(572, 1, 'Add Combo Product', 'Add New Combo Product : Redmi 10', '2024-09-29', '12:02:47'),
(573, 1, 'Add Combo Product', 'Add New Combo Product : Redmi note 11 pro 4G', '2024-09-29', '12:04:44'),
(574, 1, 'Add Combo Product', 'Add New Combo Product : Redmi note 11 4G ', '2024-09-29', '12:05:46'),
(575, 1, 'Add Combo Product', 'Add New Combo Product : Mi 11 Lite ', '2024-09-29', '12:06:29'),
(576, 1, 'Add Combo Product', 'Add New Combo Product : Redmi 12 c', '2024-09-29', '12:08:30'),
(577, 1, 'Add Combo Product', 'Add New Combo Product : Redmi note 13 4G', '2024-09-29', '12:10:13'),
(578, 1, 'Add Combo Product', 'Add New Combo Product : Redmi note 13 pro 4G ', '2024-09-29', '12:15:00'),
(579, 1, 'Edit Product', 'Edit Updated : Redmi 12 c', '2024-09-29', '12:18:11'),
(580, 1, 'Add Combo Product', 'Add New Combo Product : Redmi note 12 pro', '2024-09-29', '12:19:14'),
(581, 1, 'Add Combo Product', 'Add New Combo Product : Redmi 12', '2024-09-29', '12:20:07'),
(582, 1, 'Add Combo Product', 'Add New Combo Product : Redmi note 12 4G', '2024-09-29', '12:21:18'),
(583, 1, 'Edit Product', 'Edit Updated : Redmi 12 c', '2024-09-29', '12:24:59'),
(584, 1, 'Add Combo Product', 'Add New Combo Product : Redmi 13 C', '2024-09-29', '12:27:23'),
(585, 1, 'Add Combo Product', 'Add New Combo Product : Redmi note 13 pro plus', '2024-09-29', '12:29:33'),
(586, 1, 'Attendance Submitted', 'Attendance : Employee Name : lakmal , Action : Clock Out', '2024-09-29', '12:44:36'),
(587, 1, 'Attendance Updated in Employee Profile', 'Employee Name : lakmal , Action : Clock Out', '2024-09-29', '12:44:36'),
(588, 1, 'Error Solved', 'Error ID : 20', '2024-09-29', '12:48:07'),
(589, 1, 'Error Solved', 'Error ID : 18', '2024-09-29', '12:48:12'),
(590, 1, 'Error Solved', 'Error ID : 19', '2024-09-29', '12:48:18'),
(591, 1, 'Edit Product', 'Edit Updated : Redmi note 13 pro plus Backcover', '2024-09-29', '12:52:48'),
(592, 1, 'Edit Product', 'Edit Updated : Redmi 13 C Backcover ', '2024-09-29', '12:53:06'),
(593, 1, 'Edit Product', 'Edit Updated : Redmi note 12 4G Backcover', '2024-09-29', '12:53:21'),
(594, 1, 'Edit Product', 'Edit Updated : Redmi 12 Backcover', '2024-09-29', '12:53:47'),
(595, 1, 'Edit Product', 'Edit Updated : Redmi note 12 pro Backcover', '2024-09-29', '12:54:11'),
(596, 1, 'Edit Product', 'Edit Updated : Redmi note 13 pro 4G Backcover', '2024-09-29', '12:54:25'),
(597, 1, 'Edit Product', 'Edit Updated : Redmi note 13 4G Backcover', '2024-09-29', '12:54:55'),
(598, 1, 'Edit Product', 'Edit Updated : Redmi 12 c Backcover', '2024-09-29', '12:55:45'),
(599, 1, 'Edit Product', 'Edit Updated : Mi 11 Lite Backcover', '2024-09-29', '12:56:02'),
(600, 1, 'Edit Product', 'Edit Updated : Redmi note 11 4G Backcover', '2024-09-29', '12:56:17'),
(601, 1, 'Edit Product', 'Edit Updated : Redmi note 11 pro 4G Backcover', '2024-09-29', '12:56:52'),
(602, 1, 'Edit Product', 'Edit Updated : Redmi 10 Backcover', '2024-09-29', '12:57:19'),
(603, 1, 'Edit Product', 'Edit Updated : Redmi note 10 pro 4G Backcover', '2024-09-29', '12:57:47'),
(604, 1, 'Edit Product', 'Edit Updated : Redmi note 10 4G Backcover', '2024-09-29', '12:58:04'),
(605, 1, 'Edit Product', 'Edit Updated : Redmi note 8 pro Backcover', '2024-09-29', '12:58:23'),
(606, 1, 'Edit Product', 'Edit Updated : Redmi 9T Backcover', '2024-09-29', '12:58:43'),
(607, 1, 'Edit Product', 'Edit Updated : redmi Note 9 Backcover', '2024-09-29', '12:59:04');

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
(0, 'ifix', 718366077, '', '', 'Admin', '', 300.00, 0.00, 'indroot', 1, '', 0, '2024-09-08'),
(1, 'lakmal', 718366077, '0', '0', 'Admin', '0', 513.60, 0.00, 'admin@132', 1, '', 0, '2024-09-08'),
(14, 'Udaya', 779006160, '0', '8130907192 Commercial', 'Employee', '861722787', 1000.00, 0.00, 'udaya', 1, NULL, 0, '2024-09-10'),
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
(6, 1064, 'You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \' \r\n            profit = 130, \r\n            stock_qty = 35, \r\n            has_...\' at line 5', 'UPDATE products \r\n        SET \r\n            product_name = \'test\', \r\n            rate = 200.00, \r\n            cost = , \r\n            profit = 130, \r\n            stock_qty = 35, \r\n            has_stock = 1, \r\n            stock_alert_limit = 60, \r\n            worker_commision = 70.00 \r\n        WHERE product_id = 7', 'Update Product', 'Update Product : test', '2024-09-17', '19:12:22', 0, 'fixed'),
(7, 1366, 'Incorrect decimal value: \'\' for column `ifix`.`accounts`.`amount` at row 4', 'UPDATE accounts SET amount = \'\' WHERE account_name = \'cash_in_hand\';', 'Update Cash In Hand Amount Manually', 'Update Cash In Hand Amount to Rs.', '2024-09-18', '06:47:00', 1, 'fixed'),
(8, 1406, 'Data too long for column \'product\' at row 1', 'INSERT INTO sales (invoice_number, product, qty, rate, amount, worker, cost, profit)\r\n                                    VALUES (\'9\', \'Oms 5c battery(3 month warranty)\', \'1\', \'800.00\', \'800.00\', \'lakmal\', \'260\', \'540\')', 'Add New Sale', 'Add New Sale : Oms 5c battery(3 month warranty)', '2024-09-19', '08:07:26', 0, 'fixed'),
(9, 1406, 'Data too long for column \'product\' at row 1', 'INSERT INTO sales (invoice_number, product, qty, rate, amount, worker, cost, profit)\r\n                                    VALUES (\'10\', \'Oms 5c battery(3 month warranty)\', \'1\', \'800.00\', \'800.00\', \'ifix\', \'260\', \'540\')', 'Add New Sale', 'Add New Sale : Oms 5c battery(3 month warranty)', '2024-09-19', '08:15:42', 0, 'fixed'),
(10, 1406, 'Data too long for column \'product\' at row 1', 'INSERT INTO sales (invoice_number, product, qty, rate, amount, worker, cost, profit)\r\n                                    VALUES (\'11\', \'Oms 5c battery(3 month warranty)\', \'1\', \'800.00\', \'800.00\', \'ifix\', \'260\', \'540\')', 'Add New Sale', 'Add New Sale : Oms 5c battery(3 month warranty)', '2024-09-19', '08:16:37', 0, 'fixed'),
(11, 1366, 'Incorrect decimal value: \'\' for column `ifix`.`accounts`.`amount` at row 5', 'UPDATE accounts SET account_name = \'Dfcc\', amount = \'\' WHERE account_name = \'Dfcc\';', 'Update Bank Account', 'Update Dfcc Bank Account Name to  Amount to Dfcc and Rs.', '2024-09-27', '10:48:34', 1, 'fixed'),
(12, 1062, 'Duplicate entry \'Om418 type-c chager\' for key \'product_name\'', 'INSERT INTO products (product_name, rate, cost, profit, stock_qty, has_stock, stock_alert_limit, worker_commision) \n                     VALUES (\'Om418 type-c chager\', \'2650\', \'1400\', \'1250\',\'12\', \'1\', \'4\', \'0.00\')', 'Add Combo Product', 'Add New Combo Product : Om418 type-c chager', '2024-09-28', '08:37:49', 1, 'fixed'),
(13, 1452, 'Cannot add or update a child row: a foreign key constraint fails (`ifix`.`transaction_log`, CONSTRAINT `transaction_log_ibfk_1` FOREIGN KEY (`employ_id`) REFERENCES `employees` (`employ_id`))', 'INSERT INTO transaction_log (transaction_type, description, amount) VALUES (\'Invoice - Company Profit\', \'Profit (Balance Pay) from Invoice Number : <a href=\\\'/invoice/print.php?id=6\\\'> 6 </a>\', \'382.5\')', 'Add Invoice Company Profit - transaction_log', 'Invoice Number : 6, Payment  : 850.00, New Invoice Balance : 0 ', '2024-09-28', '09:59:06', 1, 'fixed'),
(14, 1452, 'Cannot add or update a child row: a foreign key constraint fails (`ifix`.`transaction_log`, CONSTRAINT `transaction_log_ibfk_1` FOREIGN KEY (`employ_id`) REFERENCES `employees` (`employ_id`))', 'INSERT INTO transaction_log (transaction_type, description, amount) VALUES (\'Add Invoice Balance Payment\', \'Add Fund to Invoice Number : <a href=\\\'/invoice/print.php?id=6\\\'> 6 </a>\', \'850.00\')', 'Add Fund to Invoice - transaction_log', 'Invoice Number : 6, Payment  : 850.00, New Invoice Balance : 0 ', '2024-09-28', '09:59:06', 1, 'fixed'),
(15, 1062, 'Duplicate entry \'Wireless Keyboard\' for key \'product_name\'', 'INSERT INTO products (product_name, rate, cost, profit, stock_qty, has_stock, stock_alert_limit, worker_commision) \n                     VALUES (\'Wireless Keyboard\', \'4650\', \'2650\', \'2000\',\'1\', \'1\', \'0\', \'0.00\')', 'Add Combo Product', 'Add New Combo Product : Wireless Keyboard', '2024-09-28', '10:15:41', 1, 'fixed'),
(16, 1062, 'Duplicate entry \'D2 Headset \' for key \'product_name\'', 'INSERT INTO products (product_name, rate, cost, profit, stock_qty, has_stock, stock_alert_limit, worker_commision) \n                     VALUES (\'D2 Headset \', \'900\', \'450\', \'450\',\'9\', \'1\', \'0\', \'0.00\')', 'Add Combo Product', 'Add New Combo Product : D2 Headset ', '2024-09-29', '08:08:07', 1, 'fixed'),
(17, 1062, 'Duplicate entry \'NA-20 Headset \' for key \'product_name\'', 'INSERT INTO products (product_name, rate, cost, profit, stock_qty, has_stock, stock_alert_limit, worker_commision) \n                     VALUES (\'NA-20 Headset \', \'450\', \'250\', \'200\',\'4\', \'1\', \'2\', \'0.00\')', 'Add Combo Product', 'Add New Combo Product : NA-20 Headset ', '2024-09-29', '08:32:50', 1, 'fixed'),
(18, 1062, 'Duplicate entry \'Redmi note 8\' for key \'product_name\'', 'INSERT INTO products (product_name, rate, cost, profit, stock_qty, has_stock, stock_alert_limit, worker_commision) \n                     VALUES (\'Redmi note 8\', \'600\', \'250\', \'350\',\'3\', \'1\', \'2\', \'0.00\')', 'Add Combo Product', 'Add New Combo Product : Redmi note 8', '2024-09-29', '11:43:09', 1, 'fixed'),
(19, 1062, 'Duplicate entry \'Redmi note 13 4G\' for key \'product_name\'', 'INSERT INTO products (product_name, rate, cost, profit, stock_qty, has_stock, stock_alert_limit, worker_commision) \n                     VALUES (\'Redmi note 13 4G\', \'600\', \'250\', \'350\',\'7\', \'1\', \'2\', \'0.00\')', 'Add Combo Product', 'Add New Combo Product : Redmi note 13 4G', '2024-09-29', '12:28:16', 1, 'fixed'),
(20, 1062, 'Duplicate entry \'Redmi note 13 pro 4G\' for key \'product_name\'', 'INSERT INTO products (product_name, rate, cost, profit, stock_qty, has_stock, stock_alert_limit, worker_commision) \n                     VALUES (\'Redmi note 13 pro 4G\', \'600\', \'250\', \'350\',\'3\', \'1\', \'0\', \'0.00\')', 'Add Combo Product', 'Add New Combo Product : Redmi note 13 pro 4G', '2024-09-29', '12:30:24', 1, 'fixed');

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
(1, '', 'Cash', '2024-09-19', '09:07:44', 0, 'lakmal', 'Udaya', 5500.00, 0.00, 5500.00, 0.00, 3500.00, 2000.00, 1, 'Cash'),
(2, '', 'Cash', '2024-09-19', '10:53:54', 0, 'ifix', 'ifix', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, 'Cash'),
(3, '', 'Cash', '2024-09-19', '10:53:55', 0, 'ifix', 'ifix', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, 'Cash'),
(4, '', 'Cash', '2024-09-19', '10:53:56', 0, 'ifix', 'ifix', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, 'Cash'),
(5, '', 'Cash', '2024-09-23', '11:00:44', 0, 'lakmal', 'lakmal', 800.00, 0.00, 800.00, 0.00, 0.00, 600.00, 1, 'Cash'),
(6, '', 'Cash', '2024-09-28', '09:58:37', 0, 'lakmal', 'lakmal', 850.00, 0.00, 850.00, 0.00, 400.00, 450.00, 1, 'Cash');

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
(4, 8, 'Charging Pin Repair ', 1, 500.00, 500.00, NULL, NULL, 'skip', 'lakmal');

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
(59, 'Xpc25 micro normal charger', NULL, 19, 850.00, 400.00, 450.00, '1', 5, 0.00, NULL, '0'),
(60, 'Om418 type-c chager', NULL, 12, 2650.00, 1400.00, 1250.00, '1', 4, 0.00, NULL, '0'),
(62, 'Om418 Micro chager', NULL, 7, 2650.00, 1400.00, 1250.00, '1', 5, 0.00, NULL, '0'),
(63, 'Om418 Lightning', NULL, 2, 2600.00, 1400.00, 1200.00, '1', 2, 0.00, NULL, '0'),
(64, 'Xpc30 Micro chager', NULL, 7, 2200.00, 1250.00, 950.00, '1', 2, 0.00, NULL, '0'),
(65, 'Om401 TYPE-C Chager', NULL, 2, 2350.00, 1200.00, 1150.00, '1', 2, 0.00, NULL, '0'),
(66, 'Om409 C to C Chager', NULL, 2, 3450.00, 1850.00, 1600.00, '1', 1, 0.00, NULL, '0'),
(67, 'OsW01 10000Mah Powerbank', NULL, 1, 4950.00, 2900.00, 2050.00, '1', 0, 0.00, NULL, '0'),
(68, 'V12 10000Mah Powerbank', NULL, 1, 5650.00, 2950.00, 2700.00, '1', 0, 0.00, NULL, '0'),
(69, 'V22 20000Mah Powerbank', NULL, 1, 7950.00, 4300.00, 3650.00, '1', 0, 0.00, NULL, '0'),
(70, 'OSD24', NULL, 1, 4250.00, 2300.00, 1950.00, '1', 0, 0.00, NULL, '0'),
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
(104, 'OM-154 Micro Cable ', NULL, 6, 400.00, 200.00, 200.00, '1', 0, 0.00, NULL, '0'),
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
(131, 'OM-168 Micro Cable ', NULL, 9, 700.00, 450.00, 250.00, '1', 0, 0.00, NULL, '0'),
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
(214, 'Redmi note 13 pro plus Backcover', NULL, 2, 600.00, 250.00, 350.00, '1', 0, 0.00, NULL, '0');

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
(4, 1, 200.00, 'Commission Added for Chargin Port Repart in Invoice Number : <a href=\'/invoice/print.php?id=4\'> 4 </a>', '2024-09-10', '08:20:30'),
(5, 1, 100.00, 'Commission from Invoice Number : <a href=\'/invoice/print.php?id=9\'> 9 </a> for Oms 5c battery(3 month warranty)', '2024-09-19', '08:07:26'),
(6, 0, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=10\'> 10 </a> for Oms 5c battery(3 month warranty)', '2024-09-19', '08:15:42'),
(7, 0, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=11\'> 11 </a> for Oms 5c battery(3 month warranty)', '2024-09-19', '08:16:37'),
(8, 0, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=12\'> 12 </a> for Oms 5c battery(3 month warranty)', '2024-09-19', '08:19:59'),
(9, 14, 1000.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=1\'> 1 </a> for Display replacement', '2024-09-19', '09:07:44'),
(10, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=5\'> 5 </a> for oms 4c battery 3 month worrenty ', '2024-09-23', '11:00:44'),
(11, 1, 0.00, 'Commission Added for M02 Backcover in Invoice Number : <a href=\'/invoice/print.php?id=5\'> 5 </a>', '2024-09-27', '12:48:02'),
(12, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=6\'> 6 </a> for Xpc25 micro normal charger', '2024-09-28', '09:58:37'),
(13, 1, 67.50, 'Profit (Balance Pay) from Invoice Number : <a href=\'/invoice/print.php?id=6\'> 6 </a>', '2024-09-28', '09:59:06');

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
(3, 6, 'Xpc25 micro normal charger', NULL, '1', 850.00, 850.00, 400.00, 450.00, 'lakmal');

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
(59, 'Add Invoice Balance Payment', 'Add Fund to Invoice Number : 6 ', 850, '2024-09-28', '10:42:55', 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `action_log`
--
ALTER TABLE `action_log`
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=608;

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
  MODIFY `error_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_number` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `InvoiceBalPayRecords`
--
ALTER TABLE `InvoiceBalPayRecords`
  MODIFY `InvBalPayRecords_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `product_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=216;

--
-- AUTO_INCREMENT for table `purchase`
--
ALTER TABLE `purchase`
  MODIFY `purchase_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salary`
--
ALTER TABLE `salary`
  MODIFY `salary_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sales_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `transaction_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

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

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 26, 2024 at 03:51 PM
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
(1, 'Stock Account', 'cash', -23855.00),
(2, 'Company Profit', 'cash', 24475.50),
(6, 'Utility Bills', 'cash', 0.00),
(7, 'cash_in_hand', 'cash', 15850.00),
(17, 'Dfcc', 'bank', 1164.00),
(18, 'Co op shop', 'bank', 51000.00),
(19, 'Co op Other', 'bank', 160040.00);

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
(1, 1, 'Add New Repair Invoice', 'Invoice Number : 1, Customer Name : Cash, Date : 2024-10-17, Total : 5100, Discount : 0, Advance : 5100, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-17', '10:55:55'),
(2, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-17', '10:55:55'),
(3, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 800.00', '2024-10-17', '10:55:55'),
(4, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 800.00 for m02 Original Display Replace', '2024-10-17', '10:55:55'),
(5, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : m02 Original Display Replace', '2024-10-17', '10:55:55'),
(6, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : m02 Original Display Replace', '2024-10-17', '10:55:55'),
(7, 1, 'Fall Sell Item from Stock', 'Fall M02 Org Display in Stock', '2024-10-17', '10:55:55'),
(8, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100.00', '2024-10-17', '10:55:55'),
(9, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 100.00 for Clear Tempered Glass ', '2024-10-17', '10:55:55'),
(10, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-17', '10:55:55'),
(11, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-17', '10:55:56'),
(12, 1, 'Fall Sell Item from Stock', 'Fall Clear Tmpered glass in Stock', '2024-10-17', '10:55:56'),
(13, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 1550', '2024-10-17', '10:55:56'),
(14, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 1 - Cash, Profit : Rs. 1550, Rs. 1550 by 1 ', '2024-10-17', '10:55:56'),
(15, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 1, Total Bill Cost : 3550, Total Bill Profit : 1550', '2024-10-17', '10:55:56'),
(16, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 1, Rs. 5100', '2024-10-17', '10:55:56'),
(17, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 1 - Cash, Payment Method : Cash, Advance : Rs. 5100, Rs. 5100 by 1 ', '2024-10-17', '10:55:56'),
(18, 1, 'Update Cash In Hand Amount Manually', 'Update Cash In Hand Amount to Rs.14900', '2024-10-18', '09:43:29'),
(19, 1, 'Add New Repair Invoice', 'Invoice Number : 2, Customer Name : Cash, Date : 2024-10-18, Total : 600, Discount : 0, Advance : 600, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '09:46:26'),
(20, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-18', '09:46:26'),
(21, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100.00', '2024-10-18', '09:46:26'),
(22, 1, 'Add Commision - Update Salary Table', 'Add Commison to Kasun Rs. 100.00 for Clear Tempered Glass ', '2024-10-18', '09:46:26'),
(23, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-18', '09:46:26'),
(24, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-18', '09:46:26'),
(25, 1, 'Fall Sell Item from Stock', 'Fall Clear Tmpered glass in Stock', '2024-10-18', '09:46:26'),
(26, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 350', '2024-10-18', '09:46:26'),
(27, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 2 - Cash, Profit : Rs. 350, Rs. 350 by 1 ', '2024-10-18', '09:46:26'),
(28, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 2, Total Bill Cost : 250, Total Bill Profit : 350', '2024-10-18', '09:46:26'),
(29, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 2, Rs. 600', '2024-10-18', '09:46:26'),
(30, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 2 - Cash, Payment Method : Cash, Advance : Rs. 600, Rs. 600 by 1 ', '2024-10-18', '09:46:26'),
(31, 1, 'Update Invoice', 'InvoiceNumber : 2, Invoice Description :  , Customer Name : Cash, Invoice Date : 2024-10-18, Customer Mobile : 0, Biller : lakmal, Primary Worker : lakmal, Total : 500, Discount : 0, Advance : 0, Balance : 500.00, Full Paid : 0, Payment Method : Cash, Cost : 0, Profit : 0', '2024-10-18', '09:48:04'),
(32, 1, 'Add Biller Profit to Employee Table when Invoice Edit', 'send biller Profit : lakmal Rs. -52.5 , when Invoice Edit : 2', '2024-10-18', '09:48:04'),
(33, 1, 'Employee Salary Paid - Update Salary Table when Invoice Edit', 'Employee ID: 1, Rs. -52.5 , when Invoice Edit : 2', '2024-10-18', '09:48:04'),
(34, 1, 'Update Company Profit when Invoice Edit', 'Company Profit : Rs. -297.5, Invoice 2 Invoice Edit', '2024-10-18', '09:48:04'),
(35, 1, 'Update Invoice', 'InvoiceNumber : 2, Invoice Description :   , Customer Name : Cash, Invoice Date : 2024-10-18, Customer Mobile : 0, Biller : lakmal, Primary Worker : lakmal, Total : 500, Discount : 0, Advance : 0, Balance : 500.00, Full Paid : 0, Payment Method : Cash, Cost : 0, Profit : 0', '2024-10-18', '09:48:09'),
(36, 1, 'Add Biller Profit to Employee Table when Invoice Edit', 'send biller Profit : lakmal Rs. 0 , when Invoice Edit : 2', '2024-10-18', '09:48:09'),
(37, 1, 'Employee Salary Paid - Update Salary Table when Invoice Edit', 'Employee ID: 1, Rs. 0 , when Invoice Edit : 2', '2024-10-18', '09:48:09'),
(38, 1, 'Update Company Profit when Invoice Edit', 'Company Profit : Rs. 0, Invoice 2 Invoice Edit', '2024-10-18', '09:48:09'),
(39, 1, 'Add Invoice Company Profit - transaction_log', 'Invoice Number : 2, Payment  : , New Invoice Balance :  ', '2024-10-18', '09:48:09'),
(40, 1, 'Update Cash In Hand Amount Manually', 'Update Cash In Hand Amount to Rs.15400', '2024-10-18', '09:48:26'),
(41, 1, 'Add New Invoice', 'Invoice Number : 3, Customer Name : Cash, Date : 2024-10-18, Total : 700, Discount : 0, Advance : 700, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '09:59:23'),
(42, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-18', '09:59:23'),
(43, 1, 'Sale One-Time-Product', 'watch Charger', '2024-10-18', '09:59:23'),
(44, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 700', '2024-10-18', '09:59:23'),
(45, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 3 - Cash, Profit : Rs. 700, Rs. 700 by 1 ', '2024-10-18', '09:59:23'),
(46, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 3, Total Bill Cost : 0, Total Bill Profit : 700', '2024-10-18', '09:59:23'),
(47, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 3, Rs. 700', '2024-10-18', '09:59:23'),
(48, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 3 - Cash, Payment Method : Cash, Advance : Rs. 700, Rs. 700 by 1 ', '2024-10-18', '09:59:23'),
(49, 1, 'Add Repair Stock Item', 'Name : Battery pin, Price : 150, Qty : 50', '2024-10-18', '10:18:48'),
(50, 1, 'Fall Cash-In-Hand Account for Repair Stock Item Purchase', 'Name : Battery pin, Price : 150, Qty : 50', '2024-10-18', '10:18:48'),
(51, 1, 'Transaction Log', 'Transaction Type : Repair Stock Item Purchase, description : Battery pin, Rs. -7500 by 1 ', '2024-10-18', '10:18:48'),
(52, 1, 'Add New Repair Invoice', 'Invoice Number : 4, Customer Name : Cash, Date : 2024-10-18, Total : 600, Discount : 0, Advance : 600, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '11:07:43'),
(53, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-18', '11:07:43'),
(54, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100.00', '2024-10-18', '11:07:43'),
(55, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 100.00 for Clear Tempered Glass ', '2024-10-18', '11:07:43'),
(56, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-18', '11:07:43'),
(57, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-18', '11:07:43'),
(58, 1, 'Fall Sell Item from Stock', 'Fall Clear Tmpered glass in Stock', '2024-10-18', '11:07:43'),
(59, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 350', '2024-10-18', '11:07:43'),
(60, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 4 - Cash, Profit : Rs. 350, Rs. 350 by 1 ', '2024-10-18', '11:07:43'),
(61, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 4, Total Bill Cost : 250, Total Bill Profit : 350', '2024-10-18', '11:07:43'),
(62, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 4, Rs. 600', '2024-10-18', '11:07:43'),
(63, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 4 - Cash, Payment Method : Cash, Advance : Rs. 600, Rs. 600 by 1 ', '2024-10-18', '11:07:43'),
(64, 1, 'Add New Customer', 'chr', '2024-10-18', '11:44:56'),
(65, 1, 'Add New Repair Invoice', 'Invoice Number : 5, Customer Name : chr, Date : 2024-10-18, Total : 800, Discount : 0, Advance : 800, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '11:44:56'),
(66, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-18', '11:44:56'),
(67, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 400.00', '2024-10-18', '11:44:56'),
(68, 1, 'Add Commision - Update Salary Table', 'Add Commison to Kasun Rs. 400.00 for charging Port Replacement', '2024-10-18', '11:44:56'),
(69, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : charging Port Replacement', '2024-10-18', '11:44:56'),
(70, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : charging Port Replacement', '2024-10-18', '11:44:56'),
(71, 1, 'Fall Sell Item from Stock', 'Fall charging port in Stock', '2024-10-18', '11:44:56'),
(72, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 200', '2024-10-18', '11:44:56'),
(73, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 5 - chr, Profit : Rs. 200, Rs. 200 by 1 ', '2024-10-18', '11:44:56'),
(74, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 5, Total Bill Cost : 600, Total Bill Profit : 200', '2024-10-18', '11:44:56'),
(75, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 5, Rs. 800', '2024-10-18', '11:44:56'),
(76, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 5 - chr, Payment Method : Cash, Advance : Rs. 800, Rs. 800 by 1 ', '2024-10-18', '11:44:56'),
(77, 1, 'Add Repair Stock Item', 'Name : matte Tempered Glass , Price : 800, Qty : 50', '2024-10-18', '11:49:05'),
(78, 1, 'Fall Cash-In-Hand Account for Repair Stock Item Purchase', 'Name : matte Tempered Glass , Price : 800, Qty : 50', '2024-10-18', '11:49:05'),
(79, 1, 'Transaction Log', 'Transaction Type : Repair Stock Item Purchase, description : matte Tempered Glass , Rs. -40000 by 1 ', '2024-10-18', '11:49:05'),
(80, 1, 'Add New Invoice', 'Invoice Number : 6, Customer Name : Cash, Date : 2024-10-18, Total : 800, Discount : 0, Advance : 800, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '11:51:57'),
(81, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-18', '11:51:57'),
(82, 1, 'Add Product Cost to Stock Account', 'Rs. 400', '2024-10-18', '11:51:57'),
(83, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-18', '11:51:57'),
(84, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for xpc25 type c normal charger', '2024-10-18', '11:51:57'),
(85, 1, 'Add New Sale', 'Add New Sale : xpc25 type c normal charger', '2024-10-18', '11:51:57'),
(86, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of xpc25 type c normal charger in Stock', '2024-10-18', '11:51:57'),
(87, 1, 'Update Product Has_Stock State', 'xpc25 type c normal charger is In Stock', '2024-10-18', '11:51:57'),
(88, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 400', '2024-10-18', '11:51:57'),
(89, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 6 - Cash, Profit : Rs. 400, Rs. 400 by 1 ', '2024-10-18', '11:51:57'),
(90, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 6, Total Bill Cost : 400, Total Bill Profit : 400', '2024-10-18', '11:51:57'),
(91, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 6, Rs. 800', '2024-10-18', '11:51:57'),
(92, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 6 - Cash, Payment Method : Cash, Advance : Rs. 800, Rs. 800 by 1 ', '2024-10-18', '11:51:57'),
(93, 1, 'Add Biller Profit to Employee Table', 'send biller Profit : lakmal Rs. 75', '2024-10-18', '12:03:24'),
(94, 1, 'Employee Salary Paid - Update Salary Table', 'Employee ID: 1, Rs. 75', '2024-10-18', '12:03:24'),
(95, 1, 'Update Company Profit when Invoice Balance Payment', 'Company Profit : Rs. 425, Invoice 2 Balance Payment', '2024-10-18', '12:03:24'),
(96, 1, 'Add Invoice Company Profit - transaction_log', 'Invoice Number : 2, Payment  : 500, New Invoice Balance : 0 ', '2024-10-18', '12:03:24'),
(97, 1, 'Add Fund to Invoice - transaction_log', 'Invoice Number : 2, Payment  : 500, New Invoice Balance : 0 ', '2024-10-18', '12:03:24'),
(98, 1, 'Add Fund to Invoice - Add Invoice Balance Pay Records', 'Invoice Number : 2, Payment  : 500, New Invoice Balance : 0 ', '2024-10-18', '12:03:24'),
(99, 1, 'Add Fund to Invoice - Update Invoice Table', 'Invoice Number : 2, Payment  : 500, New Invoice Balance : 0 ', '2024-10-18', '12:03:24'),
(100, 1, 'Add New Invoice', 'Invoice Number : 7, Customer Name : Cash, Date : 2024-10-18, Total : 600, Discount : 0, Advance : 600, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '12:22:58'),
(101, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-18', '12:22:58'),
(102, 1, 'Add Product Cost to Stock Account', 'Rs. 250', '2024-10-18', '12:22:58'),
(103, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-18', '12:22:58'),
(104, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for OPPO A59/f1s back cover', '2024-10-18', '12:22:58'),
(105, 1, 'Add New Sale', 'Add New Sale : OPPO A59/f1s back cover', '2024-10-18', '12:22:58'),
(106, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of OPPO A59/f1s back cover in Stock', '2024-10-18', '12:22:58'),
(107, 1, 'Update Product Has_Stock State', 'OPPO A59/f1s back cover is In Stock', '2024-10-18', '12:22:58'),
(108, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 350', '2024-10-18', '12:22:58'),
(109, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 7 - Cash, Profit : Rs. 350, Rs. 350 by 1 ', '2024-10-18', '12:22:58'),
(110, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 7, Total Bill Cost : 250, Total Bill Profit : 350', '2024-10-18', '12:22:58'),
(111, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 7, Rs. 600', '2024-10-18', '12:22:58'),
(112, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 7 - Cash, Payment Method : Cash, Advance : Rs. 600, Rs. 600 by 1 ', '2024-10-18', '12:22:58'),
(113, 1, 'Add Petty Cash', 'for : sig , Rs. 500, By : lakmal', '2024-10-18', '12:50:20'),
(114, 1, 'Fall Petty Cash from cash_in_hand Account and profit', 'for : sig , Rs. 500, By : lakmal, Account : cash_in_hand and profit', '2024-10-18', '12:50:20'),
(115, 1, 'Transaction Log', 'Transaction Type : Petty Cash, description : sig, Rs. -500 by 1 ', '2024-10-18', '12:50:20'),
(116, 1, 'Add New Repair Invoice', 'Invoice Number : 8, Customer Name : Cash, Date : 2024-10-18, Total : 1900, Discount : 0, Advance : 1900, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '13:02:30'),
(117, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-18', '13:02:30'),
(118, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 400.00', '2024-10-18', '13:02:30'),
(119, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 400.00 for Power  Key Repair', '2024-10-18', '13:02:30'),
(120, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Power  Key Repair', '2024-10-18', '13:02:30'),
(121, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Power  Key Repair', '2024-10-18', '13:02:30'),
(122, 1, 'Fall Sell Item from Stock', 'Fall Out key in Stock', '2024-10-18', '13:02:30'),
(123, 1, 'Fall Sell Item from Stock', 'Fall Power ribbon in Stock', '2024-10-18', '13:02:30'),
(124, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100.00', '2024-10-18', '13:02:30'),
(125, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 100.00 for Clear Tempered Glass ', '2024-10-18', '13:02:30'),
(126, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-18', '13:02:30'),
(127, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-18', '13:02:30'),
(128, 1, 'Fall Sell Item from Stock', 'Fall Clear Tmpered glass in Stock', '2024-10-18', '13:02:30'),
(129, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 750', '2024-10-18', '13:02:30'),
(130, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 8 - Cash, Profit : Rs. 750, Rs. 750 by 1 ', '2024-10-18', '13:02:30'),
(131, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 8, Total Bill Cost : 1150, Total Bill Profit : 750', '2024-10-18', '13:02:30'),
(132, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 8, Rs. 1900', '2024-10-18', '13:02:30'),
(133, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 8 - Cash, Payment Method : Cash, Advance : Rs. 1900, Rs. 1900 by 1 ', '2024-10-18', '13:02:30'),
(134, 1, 'Add New Commission Record', 'Commission Added for watch Charger in Invoice Number : <a href=\'/invoice/print.php?id=3\'> 3 </a>', '2024-10-18', '13:03:28'),
(135, 1, 'Fall OneTimeProduct Cost from Company Profit', 'Fall Rs.0 in Company Profit Account for Repair/Service ID : 1', '2024-10-18', '13:03:28'),
(136, 1, 'Transaction Log', 'Transaction Type : Fall OneTimeProduct Cost from Company Profit, description : Fall Rs.0 in Company Profit Account for Repair/Service ID : 1, Rs. 0 by 1 ', '2024-10-18', '13:03:28'),
(137, 1, 'Update Invoice Cost and Profit', 'Update Invoice (ID:3) for Repair/Service ID : 1', '2024-10-18', '13:03:28'),
(138, 1, 'Update OneTimeProduct Sale', 'Update OneTimeProduct (ID:1) for Invoice ID : 3, Cost : 0, Profit : 700.00, worker : lakmal', '2024-10-18', '13:03:28'),
(139, 1, 'Add Salary Commission', 'Increase Salary of lakmal for watch Charger by Rs.0', '2024-10-18', '13:03:28'),
(140, 1, 'Transaction Log', 'Transaction Type : Add Salary Commission, description : Increase Salary of lakmal for watch Charger by Rs.0, Rs. 0 by 1 ', '2024-10-18', '13:03:28'),
(141, 1, 'Update Invoice', 'InvoiceNumber : 8, Invoice Description :  , Customer Name : Cash, Invoice Date : 2024-10-18, Customer Mobile : 0, Biller : lakmal, Primary Worker : lakmal, Total : 1800, Discount : 0, Advance : 0, Balance : 1800.00, Full Paid : 0, Payment Method : Cash, Cost : 0, Profit : 0', '2024-10-18', '13:05:18'),
(142, 1, 'Add Biller Profit to Employee Table when Invoice Edit', 'send biller Profit : lakmal Rs. -112.5 , when Invoice Edit : 8', '2024-10-18', '13:05:18'),
(143, 1, 'Employee Salary Paid - Update Salary Table when Invoice Edit', 'Employee ID: 1, Rs. -112.5 , when Invoice Edit : 8', '2024-10-18', '13:05:18'),
(144, 1, 'Update Company Profit when Invoice Edit', 'Company Profit : Rs. -637.5, Invoice 8 Invoice Edit', '2024-10-18', '13:05:18'),
(145, 1, 'Add Invoice Company Profit - transaction_log', 'Invoice Number : 8, Payment  : , New Invoice Balance :  ', '2024-10-18', '13:05:18'),
(146, 1, 'Add Combo Product', 'Add New Combo Product : 8600 Charger', '2024-10-18', '13:08:18'),
(147, 1, 'Add New Invoice', 'Invoice Number : 9, Customer Name : Cash, Date : 2024-10-18, Total : 800, Discount : 0, Advance : 800, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '13:08:56'),
(148, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-18', '13:08:56'),
(149, 1, 'Add Product Cost to Stock Account', 'Rs. 400', '2024-10-18', '13:08:56'),
(150, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-18', '13:08:56'),
(151, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for Xpc25 micro normal charger', '2024-10-18', '13:08:56'),
(152, 1, 'Add New Sale', 'Add New Sale : Xpc25 micro normal charger', '2024-10-18', '13:08:56'),
(153, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of Xpc25 micro normal charger in Stock', '2024-10-18', '13:08:56'),
(154, 1, 'Update Product Has_Stock State', 'Xpc25 micro normal charger is In Stock', '2024-10-18', '13:08:56'),
(155, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 400', '2024-10-18', '13:08:56'),
(156, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 9 - Cash, Profit : Rs. 400, Rs. 400 by 1 ', '2024-10-18', '13:08:57'),
(157, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 9, Total Bill Cost : 400, Total Bill Profit : 400', '2024-10-18', '13:08:57'),
(158, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 9, Rs. 800', '2024-10-18', '13:08:57'),
(159, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 9 - Cash, Payment Method : Cash, Advance : Rs. 800, Rs. 800 by 1 ', '2024-10-18', '13:08:57'),
(160, 1, 'Add New Invoice', 'Invoice Number : 10, Customer Name : Cash, Date : 2024-10-18, Total : 500, Discount : 0, Advance : 500, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '13:09:46'),
(161, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-18', '13:09:46'),
(162, 1, 'Add Product Cost to Stock Account', 'Rs. 250', '2024-10-18', '13:09:46'),
(163, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-18', '13:09:46'),
(164, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for Samsung A11 back Cover', '2024-10-18', '13:09:46'),
(165, 1, 'Add New Sale', 'Add New Sale : Samsung A11 back Cover', '2024-10-18', '13:09:46'),
(166, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of Samsung A11 back Cover in Stock', '2024-10-18', '13:09:46'),
(167, 1, 'Update Product Has_Stock State', 'Samsung A11 back Cover is In Stock', '2024-10-18', '13:09:46'),
(168, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 250', '2024-10-18', '13:09:46'),
(169, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 10 - Cash, Profit : Rs. 250, Rs. 250 by 1 ', '2024-10-18', '13:09:46'),
(170, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 10, Total Bill Cost : 250, Total Bill Profit : 250', '2024-10-18', '13:09:46'),
(171, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 10, Rs. 500', '2024-10-18', '13:09:46'),
(172, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 10 - Cash, Payment Method : Cash, Advance : Rs. 500, Rs. 500 by 1 ', '2024-10-18', '13:09:46'),
(173, 1, 'Add New Invoice', 'Invoice Number : 11, Customer Name : Cash, Date : 2024-10-18, Total : 1300, Discount : 0, Advance : 1300, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '13:39:23'),
(174, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-18', '13:39:23'),
(175, 1, 'Add Product Cost to Stock Account', 'Rs. 700', '2024-10-18', '13:39:23'),
(176, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-18', '13:39:23'),
(177, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for bike holder By555', '2024-10-18', '13:39:23'),
(178, 1, 'Add New Sale', 'Add New Sale : bike holder By555', '2024-10-18', '13:39:23'),
(179, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of bike holder By555 in Stock', '2024-10-18', '13:39:23'),
(180, 1, 'Update Product Has_Stock State', 'bike holder By555 is In Stock', '2024-10-18', '13:39:23'),
(181, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 600', '2024-10-18', '13:39:23'),
(182, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 11 - Cash, Profit : Rs. 600, Rs. 600 by 1 ', '2024-10-18', '13:39:23'),
(183, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 11, Total Bill Cost : 700, Total Bill Profit : 600', '2024-10-18', '13:39:23'),
(184, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 11, Rs. 1300', '2024-10-18', '13:39:23'),
(185, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 11 - Cash, Payment Method : Cash, Advance : Rs. 1300, Rs. 1300 by 1 ', '2024-10-18', '13:39:23'),
(186, 1, 'Add New Invoice', 'Invoice Number : 12, Customer Name : Cash, Date : 2024-10-18, Total : 800, Discount : 0, Advance : 800, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '13:57:02'),
(187, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-18', '13:57:02'),
(188, 1, 'Add Product Cost to Stock Account', 'Rs. 160', '2024-10-18', '13:57:02'),
(189, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100', '2024-10-18', '13:57:02'),
(190, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 100 for Oms 5c battery(3 month warranty)', '2024-10-18', '13:57:02'),
(191, 1, 'Add New Sale', 'Add New Sale : Oms 5c battery(3 month warranty)', '2024-10-18', '13:57:02'),
(192, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of Oms 5c battery(3 month warranty) in Stock', '2024-10-18', '13:57:02'),
(193, 1, 'Update Product Has_Stock State', 'Oms 5c battery(3 month warranty) is In Stock', '2024-10-18', '13:57:02'),
(194, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 540', '2024-10-18', '13:57:02'),
(195, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 12 - Cash, Profit : Rs. 540, Rs. 540 by 1 ', '2024-10-18', '13:57:02'),
(196, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 12, Total Bill Cost : 260, Total Bill Profit : 540', '2024-10-18', '13:57:02'),
(197, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 12, Rs. 800', '2024-10-18', '13:57:02'),
(198, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 12 - Cash, Payment Method : Cash, Advance : Rs. 800, Rs. 800 by 1 ', '2024-10-18', '13:57:02'),
(199, 1, 'Add New Repair Invoice', 'Invoice Number : 13, Customer Name : Cash, Date : 2024-10-18, Total : 1000, Discount : 0, Advance : 1000, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '14:26:18'),
(200, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-18', '14:26:18'),
(201, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 400.00', '2024-10-18', '14:26:18'),
(202, 1, 'Add Commision - Update Salary Table', 'Add Commison to Udaya Rs. 400.00 for battery Short repair ', '2024-10-18', '14:26:18'),
(203, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : battery Short repair ', '2024-10-18', '14:26:18'),
(204, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : battery Short repair ', '2024-10-18', '14:26:18'),
(205, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 600', '2024-10-18', '14:26:18'),
(206, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 13 - Cash, Profit : Rs. 600, Rs. 600 by 1 ', '2024-10-18', '14:26:18'),
(207, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 13, Total Bill Cost : 400, Total Bill Profit : 600', '2024-10-18', '14:26:18'),
(208, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 13, Rs. 1000', '2024-10-18', '14:26:18'),
(209, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 13 - Cash, Payment Method : Cash, Advance : Rs. 1000, Rs. 1000 by 1 ', '2024-10-18', '14:26:18'),
(210, 1, 'Add Petty Cash', 'for : sig , Rs. 500, By : lakmal', '2024-10-18', '14:27:06'),
(211, 1, 'Fall Petty Cash from cash_in_hand Account and profit', 'for : sig , Rs. 500, By : lakmal, Account : cash_in_hand and profit', '2024-10-18', '14:27:06'),
(212, 1, 'Transaction Log', 'Transaction Type : Petty Cash, description : sig, Rs. -500 by 1 ', '2024-10-18', '14:27:06'),
(213, 1, 'Add Biller Profit to Employee Table', 'send biller Profit : lakmal Rs. 270', '2024-10-18', '14:28:08'),
(214, 1, 'Employee Salary Paid - Update Salary Table', 'Employee ID: 1, Rs. 270', '2024-10-18', '14:28:08'),
(215, 1, 'Update Company Profit when Invoice Balance Payment', 'Company Profit : Rs. 1530, Invoice 8 Balance Payment', '2024-10-18', '14:28:08'),
(216, 1, 'Add Invoice Company Profit - transaction_log', 'Invoice Number : 8, Payment  : 1800, New Invoice Balance : 0 ', '2024-10-18', '14:28:08'),
(217, 1, 'Add Fund to Invoice - transaction_log', 'Invoice Number : 8, Payment  : 1800, New Invoice Balance : 0 ', '2024-10-18', '14:28:08'),
(218, 1, 'Add Fund to Invoice - Add Invoice Balance Pay Records', 'Invoice Number : 8, Payment  : 1800, New Invoice Balance : 0 ', '2024-10-18', '14:28:08'),
(219, 1, 'Add Fund to Invoice - Update Invoice Table', 'Invoice Number : 8, Payment  : 1800, New Invoice Balance : 0 ', '2024-10-18', '14:28:08'),
(220, 1, 'Add New Invoice', 'Invoice Number : 14, Customer Name : Cash, Date : 2024-10-18, Total : 1000, Discount : 0, Advance : 1000, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '14:34:26'),
(221, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-18', '14:34:26'),
(222, 1, 'Add Product Cost to Stock Account', 'Rs. 480', '2024-10-18', '14:34:26'),
(223, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-18', '14:34:26'),
(224, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for Iphone Magsafe Case ', '2024-10-18', '14:34:26'),
(225, 1, 'Add New Sale', 'Add New Sale : Iphone Magsafe Case ', '2024-10-18', '14:34:26'),
(226, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of Iphone Magsafe Case  in Stock', '2024-10-18', '14:34:26'),
(227, 1, 'Update Product Has_Stock State', 'Iphone Magsafe Case  is In Stock', '2024-10-18', '14:34:26'),
(228, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 520', '2024-10-18', '14:34:26'),
(229, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 14 - Cash, Profit : Rs. 520, Rs. 520 by 1 ', '2024-10-18', '14:34:26'),
(230, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 14, Total Bill Cost : 480, Total Bill Profit : 520', '2024-10-18', '14:34:26'),
(231, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 14, Rs. 1000', '2024-10-18', '14:34:26'),
(232, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 14 - Cash, Payment Method : Cash, Advance : Rs. 1000, Rs. 1000 by 1 ', '2024-10-18', '14:34:26'),
(233, 1, 'Add Petty Cash', 'for : daily , Rs. 2000, By : lakmal', '2024-10-18', '14:53:53'),
(234, 1, 'Fall Petty Cash from cash_in_hand Account and profit', 'for : daily , Rs. 2000, By : lakmal, Account : cash_in_hand and profit', '2024-10-18', '14:53:53'),
(235, 1, 'Transaction Log', 'Transaction Type : Petty Cash, description : daily, Rs. -2000 by 1 ', '2024-10-18', '14:53:53'),
(236, 1, 'Create Bank Account', 'Create New Bank Account : Co op shop', '2024-10-18', '15:03:53'),
(237, 1, 'Create Bank Account', 'Create New Bank Account : Co op Other', '2024-10-18', '15:04:52'),
(238, 1, 'Bank Deposit', 'Fall Rs.5000 in Cash-in-Hand Account for Co op shop Bank Deposit', '2024-10-18', '15:05:24'),
(239, 1, 'Transaction Log', 'Transaction Type : Bank Deposit, description : Add Rs.5000 to Co op shop Bank Account, Rs. 5000 by 1 ', '2024-10-18', '15:05:24'),
(240, 1, 'Bank Deposit', 'Add Rs.5000 to Co op shop Bank Account', '2024-10-18', '15:05:24'),
(241, 1, 'Bank Deposit', 'Add Rs.5000 to Co op shop Bank Account', '2024-10-18', '15:05:24'),
(242, 1, 'Bank Deposit', 'Fall Rs.2000 in Cash-in-Hand Account for Co op Other Bank Deposit', '2024-10-18', '15:05:34'),
(243, 1, 'Transaction Log', 'Transaction Type : Bank Deposit, description : Add Rs.2000 to Co op Other Bank Account, Rs. 2000 by 1 ', '2024-10-18', '15:05:34'),
(244, 1, 'Bank Deposit', 'Add Rs.2000 to Co op Other Bank Account', '2024-10-18', '15:05:34'),
(245, 1, 'Bank Deposit', 'Add Rs.2000 to Co op Other Bank Account', '2024-10-18', '15:05:34'),
(246, 1, 'Add New Repair Invoice', 'Invoice Number : 15, Customer Name : Cash, Date : 2024-10-18, Total : 1200, Discount : 0, Advance : 1164, Balance : 0, Full Paid : 1, Payment Method : CardPayment', '2024-10-18', '15:46:07'),
(247, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-18', '15:46:07'),
(248, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 400.00', '2024-10-18', '15:46:07'),
(249, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 400.00 for Power  Key Repair', '2024-10-18', '15:46:07'),
(250, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Power  Key Repair', '2024-10-18', '15:46:07'),
(251, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Power  Key Repair', '2024-10-18', '15:46:07'),
(252, 1, 'Fall Sell Item from Stock', 'Fall Out key in Stock', '2024-10-18', '15:46:07'),
(253, 1, 'Fall Sell Item from Stock', 'Fall Power ribbon in Stock', '2024-10-18', '15:46:07'),
(254, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 264', '2024-10-18', '15:46:07'),
(255, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 15 - Cash, Profit : Rs. 264, Rs. 264 by 1 ', '2024-10-18', '15:46:07'),
(256, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 15, Total Bill Cost : 900, Total Bill Profit : 264', '2024-10-18', '15:46:07'),
(257, 1, 'Add Invoice Advance Money to DFCC Account', 'Invoice Number : 15, Rs. 1164, Payment Mothod : CardPayment', '2024-10-18', '15:46:07'),
(258, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 15 - Cash, Payment Method : CardPayment, Advance : Rs. 1164, Rs. 1164 by 1 ', '2024-10-18', '15:46:07'),
(259, 1, 'Add New Invoice', 'Invoice Number : 16, Customer Name : Cash, Date : 2024-10-18, Total : 300, Discount : 0, Advance : 300, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '16:08:47'),
(260, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-18', '16:08:47'),
(261, 1, 'Add Product Cost to Stock Account', 'Rs. 150', '2024-10-18', '16:08:47'),
(262, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-18', '16:08:47'),
(263, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for CB-06 Micro Cable ', '2024-10-18', '16:08:47'),
(264, 1, 'Add New Sale', 'Add New Sale : CB-06 Micro Cable ', '2024-10-18', '16:08:47'),
(265, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of CB-06 Micro Cable  in Stock', '2024-10-18', '16:08:47'),
(266, 1, 'Update Product Has_Stock State', 'CB-06 Micro Cable  is In Stock', '2024-10-18', '16:08:47'),
(267, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 150', '2024-10-18', '16:08:47'),
(268, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 16 - Cash, Profit : Rs. 150, Rs. 150 by 1 ', '2024-10-18', '16:08:47'),
(269, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 16, Total Bill Cost : 150, Total Bill Profit : 150', '2024-10-18', '16:08:47'),
(270, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 16, Rs. 300', '2024-10-18', '16:08:47'),
(271, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 16 - Cash, Payment Method : Cash, Advance : Rs. 300, Rs. 300 by 1 ', '2024-10-18', '16:08:47'),
(272, 1, 'Add New Repair Invoice', 'Invoice Number : 17, Customer Name : Cash, Date : 2024-10-18, Total : 600, Discount : 0, Advance : 600, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '16:16:04'),
(273, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-18', '16:16:04'),
(274, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100.00', '2024-10-18', '16:16:04'),
(275, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 100.00 for Clear Tempered Glass ', '2024-10-18', '16:16:04'),
(276, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-18', '16:16:04'),
(277, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-18', '16:16:04'),
(278, 1, 'Fall Sell Item from Stock', 'Fall Clear Tmpered glass in Stock', '2024-10-18', '16:16:04'),
(279, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 350', '2024-10-18', '16:16:04'),
(280, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 17 - Cash, Profit : Rs. 350, Rs. 350 by 1 ', '2024-10-18', '16:16:04'),
(281, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 17, Total Bill Cost : 250, Total Bill Profit : 350', '2024-10-18', '16:16:04'),
(282, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 17, Rs. 600', '2024-10-18', '16:16:04'),
(283, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 17 - Cash, Payment Method : Cash, Advance : Rs. 600, Rs. 600 by 1 ', '2024-10-18', '16:16:04'),
(284, 1, 'Add New Invoice', 'Invoice Number : 18, Customer Name : Cash, Date : 2024-10-18, Total : 1200, Discount : 0, Advance : 1200, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '16:32:20'),
(285, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-18', '16:32:20'),
(286, 1, 'Add Product Cost to Stock Account', 'Rs. 250', '2024-10-18', '16:32:20'),
(287, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-18', '16:32:20'),
(288, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for Redmi A3 back Cover', '2024-10-18', '16:32:20'),
(289, 1, 'Add New Sale', 'Add New Sale : Redmi A3 back Cover', '2024-10-18', '16:32:20'),
(290, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of Redmi A3 back Cover in Stock', '2024-10-18', '16:32:20'),
(291, 1, 'Update Product Has_Stock State', 'Redmi A3 back Cover is In Stock', '2024-10-18', '16:32:20'),
(292, 1, 'Sale One-Time-Product', 'tempered mtb', '2024-10-18', '16:32:20'),
(293, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 950', '2024-10-18', '16:32:20'),
(294, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 18 - Cash, Profit : Rs. 950, Rs. 950 by 1 ', '2024-10-18', '16:32:20'),
(295, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 18, Total Bill Cost : 250, Total Bill Profit : 950', '2024-10-18', '16:32:20'),
(296, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 18, Rs. 1200', '2024-10-18', '16:32:20'),
(297, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 18 - Cash, Payment Method : Cash, Advance : Rs. 1200, Rs. 1200 by 1 ', '2024-10-18', '16:32:20'),
(298, 1, 'Add Combo Product', 'Add New Combo Product : MTB Tempered', '2024-10-18', '16:38:54'),
(299, 1, 'Add Combo Product', 'Add New Combo Product : Super D tempered Glass ', '2024-10-18', '16:39:56'),
(300, 1, 'Add New Repair Invoice', 'Invoice Number : 19, Customer Name : Cash, Date : 2024-10-18, Total : 5900, Discount : 0, Advance : 5900, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '16:43:40'),
(301, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-18', '16:43:40'),
(302, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0.00', '2024-10-18', '16:43:40'),
(303, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 0.00 for Samsung M01 core Display Replacement', '2024-10-18', '16:43:40'),
(304, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Samsung M01 core Display Replacement', '2024-10-18', '16:43:40'),
(305, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Samsung M01 core Display Replacement', '2024-10-18', '16:43:40'),
(306, 1, 'Fall Sell Item from Stock', 'Fall Samsung M01 core Display in Stock', '2024-10-18', '16:43:40'),
(307, 1, 'Add New Repair Invoice', 'Invoice Number : 20, Customer Name : Cash, Date : 2024-10-18, Total : 5900, Discount : 0, Advance : 5900, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '16:44:22'),
(308, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-18', '16:44:22'),
(309, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0.00', '2024-10-18', '16:44:22'),
(310, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 0.00 for Samsung M01 core Display Replacement', '2024-10-18', '16:44:22'),
(311, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Samsung M01 core Display Replacement', '2024-10-18', '16:44:22'),
(312, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Samsung M01 core Display Replacement', '2024-10-18', '16:44:22'),
(313, 1, 'Fall Sell Item from Stock', 'Fall Samsung M01 core Display in Stock', '2024-10-18', '16:44:22'),
(314, 1, 'Add New Repair Invoice', 'Invoice Number : 21, Customer Name : Cash, Date : 2024-10-18, Total : 800, Discount : 0, Advance : 800, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '17:19:14'),
(315, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-18', '17:19:14'),
(316, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100.00', '2024-10-18', '17:19:14'),
(317, 1, 'Add Commision - Update Salary Table', 'Add Commison to Kasun Rs. 100.00 for charging Port Replacement', '2024-10-18', '17:19:14'),
(318, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : charging Port Replacement', '2024-10-18', '17:19:14'),
(319, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : charging Port Replacement', '2024-10-18', '17:19:14'),
(320, 1, 'Fall Sell Item from Stock', 'Fall charging port in Stock', '2024-10-18', '17:19:14'),
(321, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 600', '2024-10-18', '17:19:14'),
(322, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 21 - Cash, Profit : Rs. 600, Rs. 600 by 1 ', '2024-10-18', '17:19:14'),
(323, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 21, Total Bill Cost : 200, Total Bill Profit : 600', '2024-10-18', '17:19:14'),
(324, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 21, Rs. 800', '2024-10-18', '17:19:14'),
(325, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 21 - Cash, Payment Method : Cash, Advance : Rs. 800, Rs. 800 by 1 ', '2024-10-18', '17:19:14'),
(326, 1, 'Add Combo Product', 'Add New Combo Product : 5c normal Battery', '2024-10-18', '17:52:42'),
(327, 1, 'Add New Invoice', 'Invoice Number : 22, Customer Name : Cash, Date : 2024-10-18, Total : 400, Discount : 0, Advance : 400, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '17:53:07'),
(328, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-18', '17:53:07'),
(329, 1, 'Add Product Cost to Stock Account', 'Rs. 200', '2024-10-18', '17:53:07'),
(330, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-18', '17:53:07'),
(331, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for 5c normal Battery', '2024-10-18', '17:53:07'),
(332, 1, 'Add New Sale', 'Add New Sale : 5c normal Battery', '2024-10-18', '17:53:07'),
(333, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of 5c normal Battery in Stock', '2024-10-18', '17:53:07'),
(334, 1, 'Update Product Has_Stock State', '5c normal Battery is In Stock', '2024-10-18', '17:53:07'),
(335, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 200', '2024-10-18', '17:53:07'),
(336, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 22 - Cash, Profit : Rs. 200, Rs. 200 by 1 ', '2024-10-18', '17:53:07'),
(337, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 22, Total Bill Cost : 200, Total Bill Profit : 200', '2024-10-18', '17:53:07'),
(338, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 22, Rs. 400', '2024-10-18', '17:53:07'),
(339, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 22 - Cash, Payment Method : Cash, Advance : Rs. 400, Rs. 400 by 1 ', '2024-10-18', '17:53:07'),
(340, 1, 'Add New Repair Invoice', 'Invoice Number : 23, Customer Name : Cash, Date : 2024-10-18, Total : 4500, Discount : 0, Advance : 4500, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '18:11:10'),
(341, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-18', '18:11:10'),
(342, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 600.00', '2024-10-18', '18:11:10'),
(343, 1, 'Add Commision - Update Salary Table', 'Add Commison to Udaya Rs. 600.00 for Y5 2019 Display Replacement', '2024-10-18', '18:11:10'),
(344, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Y5 2019 Display Replacement', '2024-10-18', '18:11:10'),
(345, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Y5 2019 Display Replacement', '2024-10-18', '18:11:10'),
(346, 1, 'Fall Sell Item from Stock', 'Fall Y5 2019 Display in Stock', '2024-10-18', '18:11:10'),
(347, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 1100', '2024-10-18', '18:11:10'),
(348, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 23 - Cash, Profit : Rs. 1100, Rs. 1100 by 1 ', '2024-10-18', '18:11:10'),
(349, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 23, Total Bill Cost : 3400, Total Bill Profit : 1100', '2024-10-18', '18:11:10'),
(350, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 23, Rs. 4500', '2024-10-18', '18:11:10'),
(351, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 23 - Cash, Payment Method : Cash, Advance : Rs. 4500, Rs. 4500 by 1 ', '2024-10-18', '18:11:10'),
(352, 1, 'Add Repair Stock Item', 'Name : Software, Price : 01, Qty : 100', '2024-10-18', '18:15:35'),
(353, 1, 'Fall Cash-In-Hand Account for Repair Stock Item Purchase', 'Name : Software, Price : 01, Qty : 100', '2024-10-18', '18:15:35'),
(354, 1, 'Transaction Log', 'Transaction Type : Repair Stock Item Purchase, description : Software, Rs. -100 by 1 ', '2024-10-18', '18:15:35'),
(355, 1, 'Add New Invoice', 'Invoice Number : 24, Customer Name : Cash, Date : 2024-10-18, Total : 600, Discount : 0, Advance : 600, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '18:16:46'),
(356, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-18', '18:16:46'),
(357, 1, 'Add Product Cost to Stock Account', 'Rs. 250', '2024-10-18', '18:16:46'),
(358, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-18', '18:16:46'),
(359, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for Tecno pop 5 lite back cover', '2024-10-18', '18:16:46'),
(360, 1, 'Add New Sale', 'Add New Sale : Tecno pop 5 lite back cover', '2024-10-18', '18:16:46'),
(361, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of Tecno pop 5 lite back cover in Stock', '2024-10-18', '18:16:46'),
(362, 1, 'Update Product Has_Stock State', 'Tecno pop 5 lite back cover is In Stock', '2024-10-18', '18:16:46'),
(363, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 350', '2024-10-18', '18:16:46'),
(364, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 24 - Cash, Profit : Rs. 350, Rs. 350 by 1 ', '2024-10-18', '18:16:46'),
(365, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 24, Total Bill Cost : 250, Total Bill Profit : 350', '2024-10-18', '18:16:46'),
(366, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 24, Rs. 600', '2024-10-18', '18:16:46'),
(367, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 24 - Cash, Payment Method : Cash, Advance : Rs. 600, Rs. 600 by 1 ', '2024-10-18', '18:16:46'),
(368, 1, 'Add New Repair Invoice', 'Invoice Number : 25, Customer Name : Cash, Date : 2024-10-18, Total : 1000, Discount : 0, Advance : 1000, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-18', '18:18:47'),
(369, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-18', '18:18:47'),
(370, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 200.00', '2024-10-18', '18:18:47'),
(371, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 200.00 for software Frp', '2024-10-18', '18:18:47'),
(372, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : software Frp', '2024-10-18', '18:18:47'),
(373, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : software Frp', '2024-10-18', '18:18:47'),
(374, 1, 'Fall Sell Item from Stock', 'Fall Software in Stock', '2024-10-18', '18:18:47'),
(375, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 799', '2024-10-18', '18:18:47'),
(376, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 25 - Cash, Profit : Rs. 799, Rs. 799 by 1 ', '2024-10-18', '18:18:47'),
(377, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 25, Total Bill Cost : 201, Total Bill Profit : 799', '2024-10-18', '18:18:47'),
(378, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 25, Rs. 1000', '2024-10-18', '18:18:47');
INSERT INTO `action_log` (`action_id`, `employee_id`, `action`, `description`, `date`, `time`) VALUES
(379, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 25 - Cash, Payment Method : Cash, Advance : Rs. 1000, Rs. 1000 by 1 ', '2024-10-18', '18:18:47'),
(380, 1, 'Add New Commission Record', 'Commission Added for tempered mtb in Invoice Number : <a href=\'/invoice/print.php?id=18\'> 18 </a>', '2024-10-18', '18:19:52'),
(381, 1, 'Fall OneTimeProduct Cost from Company Profit', 'Fall Rs.100 in Company Profit Account for Repair/Service ID : 2', '2024-10-18', '18:19:52'),
(382, 1, 'Transaction Log', 'Transaction Type : Fall OneTimeProduct Cost from Company Profit, description : Fall Rs.100 in Company Profit Account for Repair/Service ID : 2, Rs. -100 by 1 ', '2024-10-18', '18:19:52'),
(383, 1, 'Update Invoice Cost and Profit', 'Update Invoice (ID:18) for Repair/Service ID : 2', '2024-10-18', '18:19:52'),
(384, 1, 'Update OneTimeProduct Sale', 'Update OneTimeProduct (ID:2) for Invoice ID : 18, Cost : 100, Profit : 500.00, worker : Udaya', '2024-10-18', '18:19:52'),
(385, 1, 'Add Salary Commission', 'Increase Salary of Udaya for tempered mtb by Rs.100', '2024-10-18', '18:19:52'),
(386, 1, 'Transaction Log', 'Transaction Type : Add Salary Commission, description : Increase Salary of Udaya for tempered mtb by Rs.100, Rs. 100 by 1 ', '2024-10-18', '18:19:52'),
(387, 1, 'Add Combo Product', 'Add New Combo Product : om208 12W micro car charger', '2024-10-18', '18:25:48'),
(388, 1, 'Add Combo Product', 'Add New Combo Product : om208 12W type C car charger', '2024-10-18', '18:26:36'),
(389, 1, 'Edit Product', 'Edit Updated : OM-168 Micro Cable ', '2024-10-18', '18:30:27'),
(390, 1, 'Add Combo Product', 'Add New Combo Product : om750 micro cable ', '2024-10-18', '18:32:54'),
(391, 1, 'Add Repair Stock Item', 'Name : y5 18 battery , Price : 1950, Qty : 1', '2024-10-19', '09:28:03'),
(392, 1, 'Fall Cash-In-Hand Account for Repair Stock Item Purchase', 'Name : y5 18 battery , Price : 1950, Qty : 1', '2024-10-19', '09:28:03'),
(393, 1, 'Transaction Log', 'Transaction Type : Repair Stock Item Purchase, description : y5 18 battery , Rs. -1950 by 1 ', '2024-10-19', '09:28:03'),
(394, 1, 'Add New Repair Invoice', 'Invoice Number : 26, Customer Name : Cash, Date : 2024-10-19, Total : 3600, Discount : 0, Advance : 3600, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '09:37:42'),
(395, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-19', '09:37:42'),
(396, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 400.00', '2024-10-19', '09:37:42'),
(397, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 400.00 for y5 18 battery replacement', '2024-10-19', '09:37:42'),
(398, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : y5 18 battery replacement', '2024-10-19', '09:37:42'),
(399, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : y5 18 battery replacement', '2024-10-19', '09:37:42'),
(400, 1, 'Fall Sell Item from Stock', 'Fall y5 18 battery in Stock', '2024-10-19', '09:37:42'),
(401, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100.00', '2024-10-19', '09:37:42'),
(402, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 100.00 for Clear Tempered Glass ', '2024-10-19', '09:37:42'),
(403, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-19', '09:37:42'),
(404, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-19', '09:37:42'),
(405, 1, 'Fall Sell Item from Stock', 'Fall Clear Tmpered glass in Stock', '2024-10-19', '09:37:42'),
(406, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 1000', '2024-10-19', '09:37:42'),
(407, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 26 - Cash, Profit : Rs. 1000, Rs. 1000 by 1 ', '2024-10-19', '09:37:42'),
(408, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 26, Total Bill Cost : 2600, Total Bill Profit : 1000', '2024-10-19', '09:37:42'),
(409, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 26, Rs. 3600', '2024-10-19', '09:37:42'),
(410, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 26 - Cash, Payment Method : Cash, Advance : Rs. 3600, Rs. 3600 by 1 ', '2024-10-19', '09:37:42'),
(411, 1, 'Add New Repair Invoice', 'Invoice Number : 27, Customer Name : Cash, Date : 2024-10-19, Total : 400, Discount : 0, Advance : 400, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '09:39:14'),
(412, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-19', '09:39:14'),
(413, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 200.00', '2024-10-19', '09:39:14'),
(414, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 200.00 for software Frp', '2024-10-19', '09:39:14'),
(415, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : software Frp', '2024-10-19', '09:39:14'),
(416, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : software Frp', '2024-10-19', '09:39:14'),
(417, 1, 'Fall Sell Item from Stock', 'Fall Software in Stock', '2024-10-19', '09:39:14'),
(418, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 199', '2024-10-19', '09:39:14'),
(419, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 27 - Cash, Profit : Rs. 199, Rs. 199 by 1 ', '2024-10-19', '09:39:14'),
(420, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 27, Total Bill Cost : 201, Total Bill Profit : 199', '2024-10-19', '09:39:14'),
(421, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 27, Rs. 400', '2024-10-19', '09:39:14'),
(422, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 27 - Cash, Payment Method : Cash, Advance : Rs. 400, Rs. 400 by 1 ', '2024-10-19', '09:39:14'),
(423, 1, 'Update Cash In Hand Amount Manually', 'Update Cash In Hand Amount to Rs.7700', '2024-10-19', '09:55:22'),
(424, 1, 'Add New Repair Invoice', 'Invoice Number : 28, Customer Name : Cash, Date : 2024-10-19, Total : 800, Discount : 0, Advance : 800, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '10:26:35'),
(425, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-19', '10:26:35'),
(426, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100.00', '2024-10-19', '10:26:35'),
(427, 1, 'Add Commision - Update Salary Table', 'Add Commison to Kasun Rs. 100.00 for Clear Tempered Glass ', '2024-10-19', '10:26:35'),
(428, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-19', '10:26:35'),
(429, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-19', '10:26:35'),
(430, 1, 'Fall Sell Item from Stock', 'Fall Clear Tmpered glass in Stock', '2024-10-19', '10:26:35'),
(431, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 550', '2024-10-19', '10:26:35'),
(432, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 28 - Cash, Profit : Rs. 550, Rs. 550 by 1 ', '2024-10-19', '10:26:35'),
(433, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 28, Total Bill Cost : 250, Total Bill Profit : 550', '2024-10-19', '10:26:35'),
(434, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 28, Rs. 800', '2024-10-19', '10:26:35'),
(435, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 28 - Cash, Payment Method : Cash, Advance : Rs. 800, Rs. 800 by 1 ', '2024-10-19', '10:26:35'),
(436, 1, 'Add New Invoice', 'Invoice Number : 29, Customer Name : Cash, Date : 2024-10-19, Total : 1700, Discount : 0, Advance : 1700, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '10:45:11'),
(437, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-19', '10:45:11'),
(438, 1, 'Add Product Cost to Stock Account', 'Rs. 950', '2024-10-19', '10:45:11'),
(439, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-19', '10:45:11'),
(440, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for OS-E02 Lightning Earphone', '2024-10-19', '10:45:11'),
(441, 1, 'Add New Sale', 'Add New Sale : OS-E02 Lightning Earphone', '2024-10-19', '10:45:11'),
(442, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of OS-E02 Lightning Earphone in Stock', '2024-10-19', '10:45:11'),
(443, 1, 'Update Product Has_Stock State', 'OS-E02 Lightning Earphone is In Stock', '2024-10-19', '10:45:11'),
(444, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 750', '2024-10-19', '10:45:11'),
(445, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 29 - Cash, Profit : Rs. 750, Rs. 750 by 1 ', '2024-10-19', '10:45:11'),
(446, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 29, Total Bill Cost : 950, Total Bill Profit : 750', '2024-10-19', '10:45:11'),
(447, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 29, Rs. 1700', '2024-10-19', '10:45:11'),
(448, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 29 - Cash, Payment Method : Cash, Advance : Rs. 1700, Rs. 1700 by 1 ', '2024-10-19', '10:45:11'),
(449, 1, 'Add Petty Cash', 'for : sig , Rs. 1000, By : lakmal', '2024-10-19', '10:54:07'),
(450, 1, 'Fall Petty Cash from cash_in_hand Account and profit', 'for : sig , Rs. 1000, By : lakmal, Account : cash_in_hand and profit', '2024-10-19', '10:54:07'),
(451, 1, 'Transaction Log', 'Transaction Type : Petty Cash, description : sig, Rs. -1000 by 1 ', '2024-10-19', '10:54:07'),
(452, 1, 'Update Invoice', 'InvoiceNumber : 29, Invoice Description :  , Customer Name : Cash, Invoice Date : 2024-10-19, Customer Mobile : 0, Biller : lakmal, Primary Worker : lakmal, Total : 1700, Discount : 0, Advance : 1700, Balance : 0.00, Full Paid : 1, Payment Method : CardPayment, Cost : 950, Profit : 750', '2024-10-19', '10:54:28'),
(453, 1, 'Add Biller Profit to Employee Table when Invoice Edit', 'send biller Profit : lakmal Rs. 0 , when Invoice Edit : 29', '2024-10-19', '10:54:28'),
(454, 1, 'Employee Salary Paid - Update Salary Table when Invoice Edit', 'Employee ID: 1, Rs. 0 , when Invoice Edit : 29', '2024-10-19', '10:54:28'),
(455, 1, 'Update Company Profit when Invoice Edit', 'Company Profit : Rs. 0, Invoice 29 Invoice Edit', '2024-10-19', '10:54:28'),
(456, 1, 'Add Invoice Company Profit - transaction_log', 'Invoice Number : 29, Payment  : , New Invoice Balance :  ', '2024-10-19', '10:54:28'),
(457, 1, 'Add Repair Stock Item', 'Name : 20pin Display, Price : 400, Qty : 40', '2024-10-19', '11:08:06'),
(458, 1, 'Fall Cash-In-Hand Account for Repair Stock Item Purchase', 'Name : 20pin Display, Price : 400, Qty : 40', '2024-10-19', '11:08:06'),
(459, 1, 'Transaction Log', 'Transaction Type : Repair Stock Item Purchase, description : 20pin Display, Rs. -16000 by 1 ', '2024-10-19', '11:08:06'),
(460, 1, 'Add New Invoice', 'Invoice Number : 30, Customer Name : Cash, Date : 2024-10-19, Total : 4600, Discount : 0, Advance : 4600, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '11:21:21'),
(461, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-19', '11:21:21'),
(462, 1, 'Add Product Cost to Stock Account', 'Rs. 3000', '2024-10-19', '11:21:21'),
(463, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-19', '11:21:21'),
(464, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for oms T20 Air pod', '2024-10-19', '11:21:21'),
(465, 1, 'Add New Sale', 'Add New Sale : oms T20 Air pod', '2024-10-19', '11:21:21'),
(466, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of oms T20 Air pod in Stock', '2024-10-19', '11:21:21'),
(467, 1, 'Update Product Has_Stock State', 'oms T20 Air pod is In Stock', '2024-10-19', '11:21:21'),
(468, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 1600', '2024-10-19', '11:21:21'),
(469, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 30 - Cash, Profit : Rs. 1600, Rs. 1600 by 1 ', '2024-10-19', '11:21:21'),
(470, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 30, Total Bill Cost : 3000, Total Bill Profit : 1600', '2024-10-19', '11:21:21'),
(471, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 30, Rs. 4600', '2024-10-19', '11:21:21'),
(472, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 30 - Cash, Payment Method : Cash, Advance : Rs. 4600, Rs. 4600 by 1 ', '2024-10-19', '11:21:21'),
(473, 1, 'Add New Repair Invoice', 'Invoice Number : 31, Customer Name : Cash, Date : 2024-10-19, Total : 5000, Discount : 0, Advance : 5000, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '11:37:20'),
(474, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-19', '11:37:20'),
(475, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0.00', '2024-10-19', '11:37:20'),
(476, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 0.00 for Samsung A10 normal Display Replacement', '2024-10-19', '11:37:20'),
(477, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Samsung A10 normal Display Replacement', '2024-10-19', '11:37:20'),
(478, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Samsung A10 normal Display Replacement', '2024-10-19', '11:37:20'),
(479, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100.00', '2024-10-19', '11:37:20'),
(480, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 100.00 for Clear Tempered Glass ', '2024-10-19', '11:37:20'),
(481, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-19', '11:37:20'),
(482, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-19', '11:37:20'),
(483, 1, 'Fall Sell Item from Stock', 'Fall Clear Tmpered glass in Stock', '2024-10-19', '11:37:20'),
(484, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 4750', '2024-10-19', '11:37:20'),
(485, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 31 - Cash, Profit : Rs. 4750, Rs. 4750 by 1 ', '2024-10-19', '11:37:20'),
(486, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 31, Total Bill Cost : 250, Total Bill Profit : 4750', '2024-10-19', '11:37:20'),
(487, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 31, Rs. 5000', '2024-10-19', '11:37:20'),
(488, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 31 - Cash, Payment Method : Cash, Advance : Rs. 5000, Rs. 5000 by 1 ', '2024-10-19', '11:37:20'),
(489, 1, 'Add New Invoice', 'Invoice Number : 32, Customer Name : Cash, Date : 2024-10-19, Total : 800, Discount : 0, Advance : 800, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '12:01:42'),
(490, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-19', '12:01:42'),
(491, 1, 'Add Product Cost to Stock Account', 'Rs. 400', '2024-10-19', '12:01:42'),
(492, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-19', '12:01:42'),
(493, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for xpc25 type c normal charger', '2024-10-19', '12:01:42'),
(494, 1, 'Add New Sale', 'Add New Sale : xpc25 type c normal charger', '2024-10-19', '12:01:42'),
(495, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of xpc25 type c normal charger in Stock', '2024-10-19', '12:01:42'),
(496, 1, 'Update Product Has_Stock State', 'xpc25 type c normal charger is In Stock', '2024-10-19', '12:01:42'),
(497, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 400', '2024-10-19', '12:01:42'),
(498, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 32 - Cash, Profit : Rs. 400, Rs. 400 by 1 ', '2024-10-19', '12:01:42'),
(499, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 32, Total Bill Cost : 400, Total Bill Profit : 400', '2024-10-19', '12:01:42'),
(500, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 32, Rs. 800', '2024-10-19', '12:01:42'),
(501, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 32 - Cash, Payment Method : Cash, Advance : Rs. 800, Rs. 800 by 1 ', '2024-10-19', '12:01:42'),
(502, 1, 'Add New Repair Invoice', 'Invoice Number : 33, Customer Name : Cash, Date : 2024-10-19, Total : 9000, Discount : 0, Advance : 9000, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '12:50:22'),
(503, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-19', '12:50:22'),
(504, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 800.00', '2024-10-19', '12:50:22'),
(505, 1, 'Add Commision - Update Salary Table', 'Add Commison to Udaya Rs. 800.00 for Samsung M21 oled display Replacement', '2024-10-19', '12:50:22'),
(506, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Samsung M21 oled display Replacement', '2024-10-19', '12:50:22'),
(507, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Samsung M21 oled display Replacement', '2024-10-19', '12:50:22'),
(508, 1, 'Fall Sell Item from Stock', 'Fall Samsung M21 oled display in Stock', '2024-10-19', '12:50:22'),
(509, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100.00', '2024-10-19', '12:50:22'),
(510, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 100.00 for Clear Tempered Glass ', '2024-10-19', '12:50:22'),
(511, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-19', '12:50:22'),
(512, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-19', '12:50:22'),
(513, 1, 'Fall Sell Item from Stock', 'Fall Clear Tmpered glass in Stock', '2024-10-19', '12:50:22'),
(514, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 1550', '2024-10-19', '12:50:22'),
(515, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 33 - Cash, Profit : Rs. 1550, Rs. 1550 by 1 ', '2024-10-19', '12:50:22'),
(516, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 33, Total Bill Cost : 7450, Total Bill Profit : 1550', '2024-10-19', '12:50:22'),
(517, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 33, Rs. 9000', '2024-10-19', '12:50:22'),
(518, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 33 - Cash, Payment Method : Cash, Advance : Rs. 9000, Rs. 9000 by 1 ', '2024-10-19', '12:50:22'),
(519, 1, 'Add Combo Product', 'Add New Combo Product : 4G dongle ', '2024-10-19', '12:58:34'),
(520, 1, 'Add New Invoice', 'Invoice Number : 34, Customer Name : Cash, Date : 2024-10-19, Total : 3800, Discount : 0, Advance : 3800, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '12:58:52'),
(521, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-19', '12:58:52'),
(522, 1, 'Add Product Cost to Stock Account', 'Rs. 3500', '2024-10-19', '12:58:52'),
(523, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-19', '12:58:52'),
(524, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for 4G dongle ', '2024-10-19', '12:58:52'),
(525, 1, 'Add New Sale', 'Add New Sale : 4G dongle ', '2024-10-19', '12:58:52'),
(526, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of 4G dongle  in Stock', '2024-10-19', '12:58:52'),
(527, 1, 'Update Product Has_Stock State', '4G dongle  is Out of Stock', '2024-10-19', '12:58:52'),
(528, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 300', '2024-10-19', '12:58:52'),
(529, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 34 - Cash, Profit : Rs. 300, Rs. 300 by 1 ', '2024-10-19', '12:58:52'),
(530, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 34, Total Bill Cost : 3500, Total Bill Profit : 300', '2024-10-19', '12:58:52'),
(531, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 34, Rs. 3800', '2024-10-19', '12:58:52'),
(532, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 34 - Cash, Payment Method : Cash, Advance : Rs. 3800, Rs. 3800 by 1 ', '2024-10-19', '12:58:52'),
(533, 1, 'Add Petty Cash', 'for : tea , Rs. 300, By : lakmal', '2024-10-19', '13:01:04'),
(534, 1, 'Fall Petty Cash from cash_in_hand Account and profit', 'for : tea , Rs. 300, By : lakmal, Account : cash_in_hand and profit', '2024-10-19', '13:01:04'),
(535, 1, 'Transaction Log', 'Transaction Type : Petty Cash, description : tea, Rs. -300 by 1 ', '2024-10-19', '13:01:04'),
(536, 1, 'Add New Invoice', 'Invoice Number : 35, Customer Name : Cash, Date : 2024-10-19, Total : 750, Discount : 0, Advance : 750, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '13:11:32'),
(537, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-19', '13:11:32'),
(538, 1, 'Add Product Cost to Stock Account', 'Rs. 250', '2024-10-19', '13:11:32'),
(539, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-19', '13:11:32'),
(540, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for pixel 6 back cover', '2024-10-19', '13:11:32'),
(541, 1, 'Add New Sale', 'Add New Sale : pixel 6 back cover', '2024-10-19', '13:11:32'),
(542, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of pixel 6 back cover in Stock', '2024-10-19', '13:11:32'),
(543, 1, 'Update Product Has_Stock State', 'pixel 6 back cover is In Stock', '2024-10-19', '13:11:32'),
(544, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 500', '2024-10-19', '13:11:32'),
(545, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 35 - Cash, Profit : Rs. 500, Rs. 500 by 1 ', '2024-10-19', '13:11:32'),
(546, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 35, Total Bill Cost : 250, Total Bill Profit : 500', '2024-10-19', '13:11:33'),
(547, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 35, Rs. 750', '2024-10-19', '13:11:33'),
(548, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 35 - Cash, Payment Method : Cash, Advance : Rs. 750, Rs. 750 by 1 ', '2024-10-19', '13:11:33'),
(549, 1, 'Add New Invoice', 'Invoice Number : 36, Customer Name : Cash, Date : 2024-10-19, Total : 500, Discount : 0, Advance : 500, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '13:19:20'),
(550, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-19', '13:19:20'),
(551, 1, 'Add Product Cost to Stock Account', 'Rs. 250', '2024-10-19', '13:19:20'),
(552, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-19', '13:19:20'),
(553, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for Samsung A12 back Cover', '2024-10-19', '13:19:20'),
(554, 1, 'Add New Sale', 'Add New Sale : Samsung A12 back Cover', '2024-10-19', '13:19:20'),
(555, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of Samsung A12 back Cover in Stock', '2024-10-19', '13:19:20'),
(556, 1, 'Update Product Has_Stock State', 'Samsung A12 back Cover is In Stock', '2024-10-19', '13:19:20'),
(557, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 250', '2024-10-19', '13:19:20'),
(558, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 36 - Cash, Profit : Rs. 250, Rs. 250 by 1 ', '2024-10-19', '13:19:20'),
(559, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 36, Total Bill Cost : 250, Total Bill Profit : 250', '2024-10-19', '13:19:20'),
(560, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 36, Rs. 500', '2024-10-19', '13:19:20'),
(561, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 36 - Cash, Payment Method : Cash, Advance : Rs. 500, Rs. 500 by 1 ', '2024-10-19', '13:19:20'),
(562, 1, 'Add New Invoice', 'Invoice Number : 37, Customer Name : Cash, Date : 2024-10-19, Total : 500, Discount : 0, Advance : 500, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '13:19:26'),
(563, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-19', '13:19:26'),
(564, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 500', '2024-10-19', '13:19:26'),
(565, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 37 - Cash, Profit : Rs. 500, Rs. 500 by 1 ', '2024-10-19', '13:19:26'),
(566, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 37, Total Bill Cost : 0, Total Bill Profit : 500', '2024-10-19', '13:19:26'),
(567, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 37, Rs. 500', '2024-10-19', '13:19:26'),
(568, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 37 - Cash, Payment Method : Cash, Advance : Rs. 500, Rs. 500 by 1 ', '2024-10-19', '13:19:26'),
(569, 1, 'Add New Repair Invoice', 'Invoice Number : 38, Customer Name : Cash, Date : 2024-10-19, Total : 500, Discount : 0, Advance : 500, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '13:19:55'),
(570, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-19', '13:19:55'),
(571, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 200.00', '2024-10-19', '13:19:55'),
(572, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 200.00 for software Frp', '2024-10-19', '13:19:55'),
(573, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : software Frp', '2024-10-19', '13:19:55'),
(574, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : software Frp', '2024-10-19', '13:19:55'),
(575, 1, 'Fall Sell Item from Stock', 'Fall Software in Stock', '2024-10-19', '13:19:55'),
(576, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 299', '2024-10-19', '13:19:55'),
(577, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 38 - Cash, Profit : Rs. 299, Rs. 299 by 1 ', '2024-10-19', '13:19:55'),
(578, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 38, Total Bill Cost : 201, Total Bill Profit : 299', '2024-10-19', '13:19:55'),
(579, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 38, Rs. 500', '2024-10-19', '13:19:55'),
(580, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 38 - Cash, Payment Method : Cash, Advance : Rs. 500, Rs. 500 by 1 ', '2024-10-19', '13:19:55'),
(581, 1, 'Add New Repair Invoice', 'Invoice Number : 39, Customer Name : Cash, Date : 2024-10-19, Total : 500, Discount : 0, Advance : 500, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '13:34:56'),
(582, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-19', '13:34:56'),
(583, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 400.00', '2024-10-19', '13:34:56'),
(584, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 400.00 for charging Port Replacement', '2024-10-19', '13:34:56'),
(585, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : charging Port Replacement', '2024-10-19', '13:34:56'),
(586, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : charging Port Replacement', '2024-10-19', '13:34:56'),
(587, 1, 'Fall Sell Item from Stock', 'Fall charging port in Stock', '2024-10-19', '13:34:56'),
(588, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. -100', '2024-10-19', '13:34:56'),
(589, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 39 - Cash, Profit : Rs. -100, Rs. -100 by 1 ', '2024-10-19', '13:34:56'),
(590, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 39, Total Bill Cost : 600, Total Bill Profit : -100', '2024-10-19', '13:34:56'),
(591, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 39, Rs. 500', '2024-10-19', '13:34:56'),
(592, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 39 - Cash, Payment Method : Cash, Advance : Rs. 500, Rs. 500 by 1 ', '2024-10-19', '13:34:56'),
(593, 1, 'Bank Deposit', 'Fall Rs.5000 in Cash-in-Hand Account for Co op shop Bank Deposit', '2024-10-19', '13:54:59'),
(594, 1, 'Transaction Log', 'Transaction Type : Bank Deposit, description : Add Rs.5000 to Co op shop Bank Account, Rs. 5000 by 1 ', '2024-10-19', '13:54:59'),
(595, 1, 'Bank Deposit', 'Add Rs.5000 to Co op shop Bank Account', '2024-10-19', '13:54:59'),
(596, 1, 'Bank Deposit', 'Add Rs.5000 to Co op shop Bank Account', '2024-10-19', '13:54:59'),
(597, 1, 'Bank Deposit', 'Fall Rs.2000 in Cash-in-Hand Account for Co op Other Bank Deposit', '2024-10-19', '13:55:07'),
(598, 1, 'Transaction Log', 'Transaction Type : Bank Deposit, description : Add Rs.2000 to Co op Other Bank Account, Rs. 2000 by 1 ', '2024-10-19', '13:55:07'),
(599, 1, 'Bank Deposit', 'Add Rs.2000 to Co op Other Bank Account', '2024-10-19', '13:55:07'),
(600, 1, 'Bank Deposit', 'Add Rs.2000 to Co op Other Bank Account', '2024-10-19', '13:55:07'),
(601, 1, 'Add New Invoice', 'Invoice Number : 40, Customer Name : Cash, Date : 2024-10-19, Total : 600, Discount : 0, Advance : 600, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '14:14:05'),
(602, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-19', '14:14:05'),
(603, 1, 'Add Product Cost to Stock Account', 'Rs. 250', '2024-10-19', '14:14:05'),
(604, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-19', '14:14:05'),
(605, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for Realme C11 Back cover', '2024-10-19', '14:14:05'),
(606, 1, 'Add New Sale', 'Add New Sale : Realme C11 Back cover', '2024-10-19', '14:14:05'),
(607, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of Realme C11 Back cover in Stock', '2024-10-19', '14:14:05'),
(608, 1, 'Update Product Has_Stock State', 'Realme C11 Back cover is In Stock', '2024-10-19', '14:14:05'),
(609, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 350', '2024-10-19', '14:14:05'),
(610, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 40 - Cash, Profit : Rs. 350, Rs. 350 by 1 ', '2024-10-19', '14:14:05'),
(611, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 40, Total Bill Cost : 250, Total Bill Profit : 350', '2024-10-19', '14:14:05'),
(612, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 40, Rs. 600', '2024-10-19', '14:14:05'),
(613, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 40 - Cash, Payment Method : Cash, Advance : Rs. 600, Rs. 600 by 1 ', '2024-10-19', '14:14:05'),
(614, 1, 'Add New Repair Invoice', 'Invoice Number : 41, Customer Name : Cash, Date : 2024-10-19, Total : 500, Discount : 0, Advance : 500, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '14:21:52'),
(615, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-19', '14:21:52'),
(616, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 400.00', '2024-10-19', '14:21:52'),
(617, 1, 'Add Commision - Update Salary Table', 'Add Commison to Kasun Rs. 400.00 for charging Port Replacement', '2024-10-19', '14:21:52'),
(618, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : charging Port Replacement', '2024-10-19', '14:21:52'),
(619, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : charging Port Replacement', '2024-10-19', '14:21:52'),
(620, 1, 'Fall Sell Item from Stock', 'Fall charging port in Stock', '2024-10-19', '14:21:52'),
(621, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. -100', '2024-10-19', '14:21:52'),
(622, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 41 - Cash, Profit : Rs. -100, Rs. -100 by 1 ', '2024-10-19', '14:21:52'),
(623, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 41, Total Bill Cost : 600, Total Bill Profit : -100', '2024-10-19', '14:21:52'),
(624, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 41, Rs. 500', '2024-10-19', '14:21:52'),
(625, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 41 - Cash, Payment Method : Cash, Advance : Rs. 500, Rs. 500 by 1 ', '2024-10-19', '14:21:52'),
(626, 1, 'Add New Repair Invoice', 'Invoice Number : 42, Customer Name : Cash, Date : 2024-10-19, Total : 800, Discount : 0, Advance : 800, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '14:32:39'),
(627, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-19', '14:32:39'),
(628, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 200.00', '2024-10-19', '14:32:39'),
(629, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 200.00 for software Frp', '2024-10-19', '14:32:39'),
(630, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : software Frp', '2024-10-19', '14:32:39'),
(631, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : software Frp', '2024-10-19', '14:32:39'),
(632, 1, 'Fall Sell Item from Stock', 'Fall Software in Stock', '2024-10-19', '14:32:39'),
(633, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 599', '2024-10-19', '14:32:39'),
(634, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 42 - Cash, Profit : Rs. 599, Rs. 599 by 1 ', '2024-10-19', '14:32:39'),
(635, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 42, Total Bill Cost : 201, Total Bill Profit : 599', '2024-10-19', '14:32:39'),
(636, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 42, Rs. 800', '2024-10-19', '14:32:39'),
(637, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 42 - Cash, Payment Method : Cash, Advance : Rs. 800, Rs. 800 by 1 ', '2024-10-19', '14:32:39'),
(638, 1, 'Add Repair Stock Item', 'Name : camera, Price : 1000, Qty : 20', '2024-10-19', '14:34:32'),
(639, 1, 'Fall Cash-In-Hand Account for Repair Stock Item Purchase', 'Name : camera, Price : 1000, Qty : 20', '2024-10-19', '14:34:32'),
(640, 1, 'Transaction Log', 'Transaction Type : Repair Stock Item Purchase, description : camera, Rs. -20000 by 1 ', '2024-10-19', '14:34:32'),
(641, 1, 'Add New Repair Invoice', 'Invoice Number : 43, Customer Name : Cash, Date : 2024-10-19, Total : 3000, Discount : 0, Advance : 3000, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '14:36:00'),
(642, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-19', '14:36:00'),
(643, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 500.00', '2024-10-19', '14:36:00'),
(644, 1, 'Add Commision - Update Salary Table', 'Add Commison to Udaya Rs. 500.00 for Camera replacement', '2024-10-19', '14:36:00'),
(645, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Camera replacement', '2024-10-19', '14:36:00'),
(646, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Camera replacement', '2024-10-19', '14:36:00'),
(647, 1, 'Fall Sell Item from Stock', 'Fall camera in Stock', '2024-10-19', '14:36:00'),
(648, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 1500', '2024-10-19', '14:36:00'),
(649, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 43 - Cash, Profit : Rs. 1500, Rs. 1500 by 1 ', '2024-10-19', '14:36:00'),
(650, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 43, Total Bill Cost : 1500, Total Bill Profit : 1500', '2024-10-19', '14:36:00'),
(651, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 43, Rs. 3000', '2024-10-19', '14:36:00'),
(652, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 43 - Cash, Payment Method : Cash, Advance : Rs. 3000, Rs. 3000 by 1 ', '2024-10-19', '14:36:00'),
(653, 1, 'Add New Invoice', 'Invoice Number : 44, Customer Name : Cash, Date : 2024-10-19, Total : 1000, Discount : 0, Advance : 1000, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '14:52:36'),
(654, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-19', '14:52:36'),
(655, 1, 'Add Product Cost to Stock Account', 'Rs. 250', '2024-10-19', '14:52:36'),
(656, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-19', '14:52:36'),
(657, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for Iphone 7  ', '2024-10-19', '14:52:36'),
(658, 1, 'Add New Sale', 'Add New Sale : Iphone 7  ', '2024-10-19', '14:52:36'),
(659, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of Iphone 7   in Stock', '2024-10-19', '14:52:36'),
(660, 1, 'Update Product Has_Stock State', 'Iphone 7   is In Stock', '2024-10-19', '14:52:36'),
(661, 1, 'Add Product Cost to Stock Account', 'Rs. 130', '2024-10-19', '14:52:36'),
(662, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100', '2024-10-19', '14:52:36'),
(663, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 100 for MTB Tempered', '2024-10-19', '14:52:36'),
(664, 1, 'Add New Sale', 'Add New Sale : MTB Tempered', '2024-10-19', '14:52:36'),
(665, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of MTB Tempered in Stock', '2024-10-19', '14:52:36'),
(666, 1, 'Update Product Has_Stock State', 'MTB Tempered is In Stock', '2024-10-19', '14:52:36'),
(667, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 520', '2024-10-19', '14:52:36'),
(668, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 44 - Cash, Profit : Rs. 520, Rs. 520 by 1 ', '2024-10-19', '14:52:36'),
(669, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 44, Total Bill Cost : 480, Total Bill Profit : 520', '2024-10-19', '14:52:36'),
(670, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 44, Rs. 1000', '2024-10-19', '14:52:36'),
(671, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 44 - Cash, Payment Method : Cash, Advance : Rs. 1000, Rs. 1000 by 1 ', '2024-10-19', '14:52:36'),
(672, 1, 'Add Petty Cash', 'for : sig , Rs. 500, By : lakmal', '2024-10-19', '15:05:42'),
(673, 1, 'Fall Petty Cash from cash_in_hand Account and profit', 'for : sig , Rs. 500, By : lakmal, Account : cash_in_hand and profit', '2024-10-19', '15:05:42'),
(674, 1, 'Transaction Log', 'Transaction Type : Petty Cash, description : sig, Rs. -500 by 1 ', '2024-10-19', '15:05:42'),
(675, 1, 'Add Petty Cash', 'for : home , Rs. 1500, By : lakmal', '2024-10-19', '15:06:32'),
(676, 1, 'Fall Petty Cash from cash_in_hand Account and profit', 'for : home , Rs. 1500, By : lakmal, Account : cash_in_hand and profit', '2024-10-19', '15:06:32'),
(677, 1, 'Transaction Log', 'Transaction Type : Petty Cash, description : home, Rs. -1500 by 1 ', '2024-10-19', '15:06:32'),
(678, 1, 'Add New Invoice', 'Invoice Number : 45, Customer Name : Cash, Date : 2024-10-19, Total : 1500, Discount : 0, Advance : 1500, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '15:58:55'),
(679, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-19', '15:58:55'),
(680, 1, 'Add Product Cost to Stock Account', 'Rs. 800', '2024-10-19', '15:58:55'),
(681, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-19', '15:58:55'),
(682, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for AKG -21 Type-C Headphone', '2024-10-19', '15:58:55'),
(683, 1, 'Add New Sale', 'Add New Sale : AKG -21 Type-C Headphone', '2024-10-19', '15:58:55'),
(684, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of AKG -21 Type-C Headphone in Stock', '2024-10-19', '15:58:55'),
(685, 1, 'Update Product Has_Stock State', 'AKG -21 Type-C Headphone is In Stock', '2024-10-19', '15:58:55'),
(686, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 700', '2024-10-19', '15:58:55'),
(687, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 45 - Cash, Profit : Rs. 700, Rs. 700 by 1 ', '2024-10-19', '15:58:55'),
(688, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 45, Total Bill Cost : 800, Total Bill Profit : 700', '2024-10-19', '15:58:55'),
(689, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 45, Rs. 1500', '2024-10-19', '15:58:55'),
(690, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 45 - Cash, Payment Method : Cash, Advance : Rs. 1500, Rs. 1500 by 1 ', '2024-10-19', '15:58:55'),
(691, 1, 'Add New Invoice', 'Invoice Number : 46, Customer Name : Cash, Date : 2024-10-19, Total : 800, Discount : 0, Advance : 800, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '16:29:24'),
(692, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-19', '16:29:24'),
(693, 1, 'Add Product Cost to Stock Account', 'Rs. 150', '2024-10-19', '16:29:24'),
(694, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100', '2024-10-19', '16:29:24'),
(695, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 100 for Super D tempered Glass ', '2024-10-19', '16:29:24'),
(696, 1, 'Add New Sale', 'Add New Sale : Super D tempered Glass ', '2024-10-19', '16:29:24'),
(697, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of Super D tempered Glass  in Stock', '2024-10-19', '16:29:24'),
(698, 1, 'Update Product Has_Stock State', 'Super D tempered Glass  is In Stock', '2024-10-19', '16:29:24'),
(699, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 550', '2024-10-19', '16:29:24'),
(700, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 46 - Cash, Profit : Rs. 550, Rs. 550 by 1 ', '2024-10-19', '16:29:24'),
(701, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 46, Total Bill Cost : 250, Total Bill Profit : 550', '2024-10-19', '16:29:24'),
(702, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 46, Rs. 800', '2024-10-19', '16:29:24'),
(703, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 46 - Cash, Payment Method : Cash, Advance : Rs. 800, Rs. 800 by 1 ', '2024-10-19', '16:29:24'),
(704, 1, 'Edit Product', 'Edit Updated : Samsung A20/A30 back Cover', '2024-10-19', '17:57:43'),
(705, 1, 'Add New Invoice', 'Invoice Number : 47, Customer Name : Cash, Date : 2024-10-19, Total : 800, Discount : 0, Advance : 800, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '18:11:55'),
(706, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-19', '18:11:55'),
(707, 1, 'Add Product Cost to Stock Account', 'Rs. 475', '2024-10-19', '18:11:55'),
(708, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-19', '18:11:55'),
(709, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for OS-A04 Type-c to Lightning Cable', '2024-10-19', '18:11:55'),
(710, 1, 'Add New Sale', 'Add New Sale : OS-A04 Type-c to Lightning Cable', '2024-10-19', '18:11:55'),
(711, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of OS-A04 Type-c to Lightning Cable in Stock', '2024-10-19', '18:11:55'),
(712, 1, 'Update Product Has_Stock State', 'OS-A04 Type-c to Lightning Cable is In Stock', '2024-10-19', '18:11:55'),
(713, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 325', '2024-10-19', '18:11:55'),
(714, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 47 - Cash, Profit : Rs. 325, Rs. 325 by 1 ', '2024-10-19', '18:11:55'),
(715, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 47, Total Bill Cost : 475, Total Bill Profit : 325', '2024-10-19', '18:11:55'),
(716, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 47, Rs. 800', '2024-10-19', '18:11:55'),
(717, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 47 - Cash, Payment Method : Cash, Advance : Rs. 800, Rs. 800 by 1 ', '2024-10-19', '18:11:55'),
(718, 1, 'Add New Invoice', 'Invoice Number : 48, Customer Name : Cash, Date : 2024-10-19, Total : 1700, Discount : 0, Advance : 1700, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '18:27:58'),
(719, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-19', '18:27:58'),
(720, 1, 'Add Product Cost to Stock Account', 'Rs. 950', '2024-10-19', '18:27:58'),
(721, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-19', '18:27:58'),
(722, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for YS-859 Ligthning to C cable', '2024-10-19', '18:27:58'),
(723, 1, 'Add New Sale', 'Add New Sale : YS-859 Ligthning to C cable', '2024-10-19', '18:27:58'),
(724, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of YS-859 Ligthning to C cable in Stock', '2024-10-19', '18:27:58'),
(725, 1, 'Update Product Has_Stock State', 'YS-859 Ligthning to C cable is In Stock', '2024-10-19', '18:27:58'),
(726, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 750', '2024-10-19', '18:27:58'),
(727, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 48 - Cash, Profit : Rs. 750, Rs. 750 by 1 ', '2024-10-19', '18:27:58'),
(728, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 48, Total Bill Cost : 950, Total Bill Profit : 750', '2024-10-19', '18:27:58'),
(729, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 48, Rs. 1700', '2024-10-19', '18:27:58'),
(730, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 48 - Cash, Payment Method : Cash, Advance : Rs. 1700, Rs. 1700 by 1 ', '2024-10-19', '18:27:58'),
(731, 1, 'Add New Invoice', 'Invoice Number : 49, Customer Name : Cash, Date : 2024-10-19, Total : 800, Discount : 0, Advance : 800, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '18:46:14'),
(732, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-19', '18:46:14'),
(733, 1, 'Add Product Cost to Stock Account', 'Rs. 700', '2024-10-19', '18:46:14'),
(734, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-19', '18:46:14'),
(735, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for sm12 sinha micro charger', '2024-10-19', '18:46:14'),
(736, 1, 'Add New Sale', 'Add New Sale : sm12 sinha micro charger', '2024-10-19', '18:46:14'),
(737, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of sm12 sinha micro charger in Stock', '2024-10-19', '18:46:14'),
(738, 1, 'Update Product Has_Stock State', 'sm12 sinha micro charger is Out of Stock', '2024-10-19', '18:46:14'),
(739, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 100', '2024-10-19', '18:46:14'),
(740, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 49 - Cash, Profit : Rs. 100, Rs. 100 by 1 ', '2024-10-19', '18:46:14'),
(741, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 49, Total Bill Cost : 700, Total Bill Profit : 100', '2024-10-19', '18:46:14'),
(742, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 49, Rs. 800', '2024-10-19', '18:46:14'),
(743, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 49 - Cash, Payment Method : Cash, Advance : Rs. 800, Rs. 800 by 1 ', '2024-10-19', '18:46:14'),
(744, 1, 'Add New Repair Invoice', 'Invoice Number : 50, Customer Name : Cash, Date : 2024-10-19, Total : 2400, Discount : 0, Advance : 2400, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-19', '18:54:00'),
(745, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-19', '18:54:00'),
(746, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 500.00', '2024-10-19', '18:54:01'),
(747, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 500.00 for Camera replacement', '2024-10-19', '18:54:01'),
(748, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Camera replacement', '2024-10-19', '18:54:01'),
(749, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Camera replacement', '2024-10-19', '18:54:01'),
(750, 1, 'Fall Sell Item from Stock', 'Fall camera in Stock', '2024-10-19', '18:54:01'),
(751, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 900', '2024-10-19', '18:54:01'),
(752, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 50 - Cash, Profit : Rs. 900, Rs. 900 by 1 ', '2024-10-19', '18:54:01'),
(753, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 50, Total Bill Cost : 1500, Total Bill Profit : 900', '2024-10-19', '18:54:01'),
(754, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 50, Rs. 2400', '2024-10-19', '18:54:01'),
(755, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 50 - Cash, Payment Method : Cash, Advance : Rs. 2400, Rs. 2400 by 1 ', '2024-10-19', '18:54:01'),
(756, 1, 'Edit Product', 'Edit Updated : wireless Keyboard', '2024-10-19', '19:00:18'),
(757, 1, 'Edit Product', 'Edit Updated : Redmi note 9 pro back cover', '2024-10-19', '19:16:02'),
(758, 1, 'Add New Repair Invoice', 'Invoice Number : 51, Customer Name : Cash, Date : 2024-10-20, Total : 1500, Discount : 0, Advance : 1500, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-20', '10:50:57'),
(759, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-20', '10:50:57'),
(760, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 400.00', '2024-10-20', '10:50:57');
INSERT INTO `action_log` (`action_id`, `employee_id`, `action`, `description`, `date`, `time`) VALUES
(761, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 400.00 for Power  Key Repair', '2024-10-20', '10:50:57'),
(762, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Power  Key Repair', '2024-10-20', '10:50:57'),
(763, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Power  Key Repair', '2024-10-20', '10:50:57'),
(764, 1, 'Fall Sell Item from Stock', 'Fall Out key in Stock', '2024-10-20', '10:50:57'),
(765, 1, 'Fall Sell Item from Stock', 'Fall Power ribbon in Stock', '2024-10-20', '10:50:57'),
(766, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 900', '2024-10-20', '10:50:57'),
(767, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 51 - Cash, Profit : Rs. 900, Rs. 900 by 1 ', '2024-10-20', '10:50:57'),
(768, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 51, Total Bill Cost : 600, Total Bill Profit : 900', '2024-10-20', '10:50:57'),
(769, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 51, Rs. 1500', '2024-10-20', '10:50:57'),
(770, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 51 - Cash, Payment Method : Cash, Advance : Rs. 1500, Rs. 1500 by 1 ', '2024-10-20', '10:50:57'),
(771, 1, 'Add New Invoice', 'Invoice Number : 52, Customer Name : Cash, Date : 2024-10-20, Total : 800, Discount : 0, Advance : 800, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-20', '10:52:01'),
(772, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-20', '10:52:01'),
(773, 1, 'Add Product Cost to Stock Account', 'Rs. 200', '2024-10-20', '10:52:01'),
(774, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-20', '10:52:01'),
(775, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for J2 2015', '2024-10-20', '10:52:01'),
(776, 1, 'Add New Sale', 'Add New Sale : J2 2015', '2024-10-20', '10:52:01'),
(777, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of J2 2015 in Stock', '2024-10-20', '10:52:01'),
(778, 1, 'Update Product Has_Stock State', 'J2 2015 is In Stock', '2024-10-20', '10:52:01'),
(779, 1, 'Add Product Cost to Stock Account', 'Rs. 130', '2024-10-20', '10:52:01'),
(780, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100', '2024-10-20', '10:52:01'),
(781, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 100 for MTB Tempered', '2024-10-20', '10:52:01'),
(782, 1, 'Add New Sale', 'Add New Sale : MTB Tempered', '2024-10-20', '10:52:01'),
(783, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of MTB Tempered in Stock', '2024-10-20', '10:52:01'),
(784, 1, 'Update Product Has_Stock State', 'MTB Tempered is In Stock', '2024-10-20', '10:52:01'),
(785, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 370', '2024-10-20', '10:52:01'),
(786, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 52 - Cash, Profit : Rs. 370, Rs. 370 by 1 ', '2024-10-20', '10:52:01'),
(787, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 52, Total Bill Cost : 430, Total Bill Profit : 370', '2024-10-20', '10:52:01'),
(788, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 52, Rs. 800', '2024-10-20', '10:52:01'),
(789, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 52 - Cash, Payment Method : Cash, Advance : Rs. 800, Rs. 800 by 1 ', '2024-10-20', '10:52:01'),
(790, 1, 'Add New Repair Invoice', 'Invoice Number : 53, Customer Name : Cash, Date : 2024-10-20, Total : 4500, Discount : 0, Advance : 4500, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-20', '11:20:37'),
(791, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-20', '11:20:37'),
(792, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0.00', '2024-10-20', '11:20:37'),
(793, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 0.00 for Samsung M01 core Display Replacement', '2024-10-20', '11:20:37'),
(794, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Samsung M01 core Display Replacement', '2024-10-20', '11:20:37'),
(795, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Samsung M01 core Display Replacement', '2024-10-20', '11:20:37'),
(796, 1, 'Fall Sell Item from Stock', 'Fall Samsung M01 core Display in Stock', '2024-10-20', '11:20:37'),
(797, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 2000', '2024-10-20', '11:20:37'),
(798, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 53 - Cash, Profit : Rs. 2000, Rs. 2000 by 1 ', '2024-10-20', '11:20:37'),
(799, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 53, Total Bill Cost : 2500, Total Bill Profit : 2000', '2024-10-20', '11:20:37'),
(800, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 53, Rs. 4500', '2024-10-20', '11:20:37'),
(801, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 53 - Cash, Payment Method : Cash, Advance : Rs. 4500, Rs. 4500 by 1 ', '2024-10-20', '11:20:37'),
(802, 1, 'Add New Invoice', 'Invoice Number : 54, Customer Name : Cash, Date : 2024-10-20, Total : 650, Discount : 0, Advance : 650, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-20', '11:25:28'),
(803, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-20', '11:25:28'),
(804, 1, 'Add Product Cost to Stock Account', 'Rs. 250', '2024-10-20', '11:25:28'),
(805, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-20', '11:25:28'),
(806, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for Samsung M01 Core back Cover', '2024-10-20', '11:25:28'),
(807, 1, 'Add New Sale', 'Add New Sale : Samsung M01 Core back Cover', '2024-10-20', '11:25:28'),
(808, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of Samsung M01 Core back Cover in Stock', '2024-10-20', '11:25:28'),
(809, 1, 'Update Product Has_Stock State', 'Samsung M01 Core back Cover is In Stock', '2024-10-20', '11:25:28'),
(810, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 400', '2024-10-20', '11:25:28'),
(811, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 54 - Cash, Profit : Rs. 400, Rs. 400 by 1 ', '2024-10-20', '11:25:28'),
(812, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 54, Total Bill Cost : 250, Total Bill Profit : 400', '2024-10-20', '11:25:28'),
(813, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 54, Rs. 650', '2024-10-20', '11:25:28'),
(814, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 54 - Cash, Payment Method : Cash, Advance : Rs. 650, Rs. 650 by 1 ', '2024-10-20', '11:25:28'),
(815, 1, 'Add New Invoice', 'Invoice Number : 55, Customer Name : Cash, Date : 2024-10-20, Total : 500, Discount : 0, Advance : 500, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-20', '15:02:57'),
(816, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-20', '15:02:57'),
(817, 1, 'Add Product Cost to Stock Account', 'Rs. 130', '2024-10-20', '15:02:57'),
(818, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100', '2024-10-20', '15:02:57'),
(819, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 100 for MTB Tempered', '2024-10-20', '15:02:57'),
(820, 1, 'Add New Sale', 'Add New Sale : MTB Tempered', '2024-10-20', '15:02:57'),
(821, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of MTB Tempered in Stock', '2024-10-20', '15:02:57'),
(822, 1, 'Update Product Has_Stock State', 'MTB Tempered is In Stock', '2024-10-20', '15:02:57'),
(823, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 270', '2024-10-20', '15:02:57'),
(824, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 55 - Cash, Profit : Rs. 270, Rs. 270 by 1 ', '2024-10-20', '15:02:57'),
(825, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 55, Total Bill Cost : 230, Total Bill Profit : 270', '2024-10-20', '15:02:57'),
(826, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 55, Rs. 500', '2024-10-20', '15:02:57'),
(827, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 55 - Cash, Payment Method : Cash, Advance : Rs. 500, Rs. 500 by 1 ', '2024-10-20', '15:02:57'),
(828, 1, 'Add New Invoice', 'Invoice Number : 56, Customer Name : Cash, Date : 2024-10-20, Total : 2000, Discount : 0, Advance : 2000, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-20', '15:22:23'),
(829, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-20', '15:22:23'),
(830, 1, 'Add Product Cost to Stock Account', 'Rs. 1250', '2024-10-20', '15:22:23'),
(831, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-20', '15:22:23'),
(832, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for Xpc30 Micro chager', '2024-10-20', '15:22:23'),
(833, 1, 'Add New Sale', 'Add New Sale : Xpc30 Micro chager', '2024-10-20', '15:22:23'),
(834, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of Xpc30 Micro chager in Stock', '2024-10-20', '15:22:23'),
(835, 1, 'Update Product Has_Stock State', 'Xpc30 Micro chager is In Stock', '2024-10-20', '15:22:23'),
(836, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 750', '2024-10-20', '15:22:23'),
(837, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 56 - Cash, Profit : Rs. 750, Rs. 750 by 1 ', '2024-10-20', '15:22:23'),
(838, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 56, Total Bill Cost : 1250, Total Bill Profit : 750', '2024-10-20', '15:22:23'),
(839, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 56, Rs. 2000', '2024-10-20', '15:22:23'),
(840, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 56 - Cash, Payment Method : Cash, Advance : Rs. 2000, Rs. 2000 by 1 ', '2024-10-20', '15:22:23'),
(841, 1, 'Add New Invoice', 'Invoice Number : 57, Customer Name : Cash, Date : 2024-10-20, Total : 3750, Discount : 0, Advance : 3750, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-20', '18:23:16'),
(842, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-20', '18:23:16'),
(843, 1, 'Add Product Cost to Stock Account', 'Rs. 1450', '2024-10-20', '18:23:16'),
(844, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-20', '18:23:16'),
(845, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for 25W Oms samsum (6 month warranty) ', '2024-10-20', '18:23:16'),
(846, 1, 'Add New Sale', 'Add New Sale : 25W Oms samsum (6 month warranty) ', '2024-10-20', '18:23:16'),
(847, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of 25W Oms samsum (6 month warranty)  in Stock', '2024-10-20', '18:23:16'),
(848, 1, 'Update Product Has_Stock State', '25W Oms samsum (6 month warranty)  is In Stock', '2024-10-20', '18:23:16'),
(849, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 2300', '2024-10-20', '18:23:16'),
(850, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 57 - Cash, Profit : Rs. 2300, Rs. 2300 by 1 ', '2024-10-20', '18:23:16'),
(851, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 57, Total Bill Cost : 1450, Total Bill Profit : 2300', '2024-10-20', '18:23:16'),
(852, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 57, Rs. 3750', '2024-10-20', '18:23:16'),
(853, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 57 - Cash, Payment Method : Cash, Advance : Rs. 3750, Rs. 3750 by 1 ', '2024-10-20', '18:23:16'),
(854, 1, 'Add Combo Product', 'Add New Combo Product : osy05 Dock only', '2024-10-21', '14:23:59'),
(855, 1, 'Add New Invoice', 'Invoice Number : 58, Customer Name : Cash, Date : 2024-10-21, Total : 800, Discount : 0, Advance : 800, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-21', '15:24:16'),
(856, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-21', '15:24:16'),
(857, 1, 'Add Product Cost to Stock Account', 'Rs. 400', '2024-10-21', '15:24:16'),
(858, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-21', '15:24:16'),
(859, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for Xpc25 micro normal charger', '2024-10-21', '15:24:16'),
(860, 1, 'Add New Sale', 'Add New Sale : Xpc25 micro normal charger', '2024-10-21', '15:24:16'),
(861, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of Xpc25 micro normal charger in Stock', '2024-10-21', '15:24:16'),
(862, 1, 'Update Product Has_Stock State', 'Xpc25 micro normal charger is In Stock', '2024-10-21', '15:24:16'),
(863, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 400', '2024-10-21', '15:24:16'),
(864, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 58 - Cash, Profit : Rs. 400, Rs. 400 by 1 ', '2024-10-21', '15:24:16'),
(865, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 58, Total Bill Cost : 400, Total Bill Profit : 400', '2024-10-21', '15:24:16'),
(866, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 58, Rs. 800', '2024-10-21', '15:24:16'),
(867, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 58 - Cash, Payment Method : Cash, Advance : Rs. 800, Rs. 800 by 1 ', '2024-10-21', '15:24:16'),
(868, 1, 'Add New Invoice', 'Invoice Number : 59, Customer Name : Cash, Date : 2024-10-21, Total : 800, Discount : 0, Advance : 800, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-21', '16:59:30'),
(869, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-21', '16:59:30'),
(870, 1, 'Add Product Cost to Stock Account', 'Rs. 400', '2024-10-21', '16:59:30'),
(871, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-21', '16:59:30'),
(872, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for Oms E250 Battery (3 month warranty ) ', '2024-10-21', '16:59:30'),
(873, 1, 'Add New Sale', 'Add New Sale : Oms E250 Battery (3 month warranty ) ', '2024-10-21', '16:59:30'),
(874, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of Oms E250 Battery (3 month warranty )  in Stock', '2024-10-21', '16:59:30'),
(875, 1, 'Update Product Has_Stock State', 'Oms E250 Battery (3 month warranty )  is In Stock', '2024-10-21', '16:59:30'),
(876, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 400', '2024-10-21', '16:59:30'),
(877, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 59 - Cash, Profit : Rs. 400, Rs. 400 by 1 ', '2024-10-21', '16:59:30'),
(878, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 59, Total Bill Cost : 400, Total Bill Profit : 400', '2024-10-21', '16:59:30'),
(879, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 59, Rs. 800', '2024-10-21', '16:59:30'),
(880, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 59 - Cash, Payment Method : Cash, Advance : Rs. 800, Rs. 800 by 1 ', '2024-10-21', '16:59:30'),
(881, 1, 'Add New Invoice', 'Invoice Number : 60, Customer Name : Cash, Date : 2024-10-21, Total : 2400, Discount : 0, Advance : 2400, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-21', '21:17:00'),
(882, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-21', '21:17:00'),
(883, 1, 'Add Product Cost to Stock Account', 'Rs. 1400', '2024-10-21', '21:17:00'),
(884, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-21', '21:17:00'),
(885, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for Om418 type-c chager', '2024-10-21', '21:17:00'),
(886, 1, 'Add New Sale', 'Add New Sale : Om418 type-c chager', '2024-10-21', '21:17:00'),
(887, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of Om418 type-c chager in Stock', '2024-10-21', '21:17:00'),
(888, 1, 'Update Product Has_Stock State', 'Om418 type-c chager is In Stock', '2024-10-21', '21:17:00'),
(889, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 1000', '2024-10-21', '21:17:00'),
(890, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 60 - Cash, Profit : Rs. 1000, Rs. 1000 by 1 ', '2024-10-21', '21:17:00'),
(891, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 60, Total Bill Cost : 1400, Total Bill Profit : 1000', '2024-10-21', '21:17:00'),
(892, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 60, Rs. 2400', '2024-10-21', '21:17:00'),
(893, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 60 - Cash, Payment Method : Cash, Advance : Rs. 2400, Rs. 2400 by 1 ', '2024-10-21', '21:17:00'),
(894, 1, 'Add New Repair Invoice', 'Invoice Number : 61, Customer Name : Cash, Date : 2024-10-23, Total : 5500, Discount : 0, Advance : 5500, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-23', '11:25:57'),
(895, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-23', '11:25:57'),
(896, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0.00', '2024-10-23', '11:25:57'),
(897, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 0.00 for Samsung A10s normal Display Replacement', '2024-10-23', '11:25:57'),
(898, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Samsung A10s normal Display Replacement', '2024-10-23', '11:25:57'),
(899, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Samsung A10s normal Display Replacement', '2024-10-23', '11:25:57'),
(900, 1, 'Fall Sell Item from Stock', 'Fall Samsung A10s normal Display in Stock', '2024-10-23', '11:25:57'),
(901, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 3100', '2024-10-23', '11:25:57'),
(902, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 61 - Cash, Profit : Rs. 3100, Rs. 3100 by 1 ', '2024-10-23', '11:25:57'),
(903, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 61, Total Bill Cost : 2400, Total Bill Profit : 3100', '2024-10-23', '11:25:57'),
(904, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 61, Rs. 5500', '2024-10-23', '11:25:57'),
(905, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 61 - Cash, Payment Method : Cash, Advance : Rs. 5500, Rs. 5500 by 1 ', '2024-10-23', '11:25:57'),
(906, 1, 'Update Employee Details', 'Name: lakmal', '2024-10-24', '17:59:34'),
(907, 1, 'Update Cash In Hand Amount Manually', 'Update Cash In Hand Amount to Rs.11800', '2024-10-24', '19:30:19'),
(908, 1, 'Update Cash In Hand Amount Manually', 'Update Cash In Hand Amount to Rs.6300', '2024-10-24', '19:30:57'),
(909, 1, 'Add New Repair Invoice', 'Invoice Number : 62, Customer Name : Cash, Date : 2024-10-24, Total : 5500, Discount : 0, Advance : 5500, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-24', '19:31:52'),
(910, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-24', '19:31:52'),
(911, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 800.00', '2024-10-24', '19:31:52'),
(912, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 800.00 for Redmi 9A Display Replacement', '2024-10-24', '19:31:52'),
(913, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Redmi 9A Display Replacement', '2024-10-24', '19:31:52'),
(914, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Redmi 9A Display Replacement', '2024-10-24', '19:31:52'),
(915, 1, 'Fall Sell Item from Stock', 'Fall Redmi 9A Display Replacement in Stock', '2024-10-24', '19:31:52'),
(916, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100.00', '2024-10-24', '19:31:52'),
(917, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 100.00 for Clear Tempered Glass ', '2024-10-24', '19:31:52'),
(918, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-24', '19:31:52'),
(919, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-24', '19:31:52'),
(920, 1, 'Fall Sell Item from Stock', 'Fall Clear Tmpered glass in Stock', '2024-10-24', '19:31:52'),
(921, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 1850', '2024-10-24', '19:31:52'),
(922, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 62 - Cash, Profit : Rs. 1850, Rs. 1850 by 1 ', '2024-10-24', '19:31:52'),
(923, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 62, Total Bill Cost : 3650, Total Bill Profit : 1850', '2024-10-24', '19:31:52'),
(924, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 62, Rs. 5500', '2024-10-24', '19:31:52'),
(925, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 62 - Cash, Payment Method : Cash, Advance : Rs. 5500, Rs. 5500 by 1 ', '2024-10-24', '19:31:52'),
(926, 1, 'Update Invoice', 'InvoiceNumber : 62, Invoice Description :  , Customer Name : Cash, Invoice Date : 2024-10-24, Customer Mobile : 0, Biller : lakmal, Primary Worker : Udaya, Total : 5500, Discount : 0, Advance : 5500, Balance : 0.00, Full Paid : 1, Payment Method : Cash, Cost : 0, Profit : 5500', '2024-10-24', '19:33:18'),
(927, 1, 'Add Biller Profit to Employee Table when Invoice Edit', 'send biller Profit : lakmal Rs. 182.5 , when Invoice Edit : 62', '2024-10-24', '19:33:18'),
(928, 1, 'Employee Salary Paid - Update Salary Table when Invoice Edit', 'Employee ID: 1, Rs. 182.5 , when Invoice Edit : 62', '2024-10-24', '19:33:18'),
(929, 1, 'Add Worker Profit to Employee Table', 'send worker Profit : Udaya Rs. 365 , when Invoice Edit : 62', '2024-10-24', '19:33:18'),
(930, 1, 'Employee Salary Paid - Update Salary Table', 'Employee ID: 14, Rs. 365 , when Invoice Edit : 62', '2024-10-24', '19:33:18'),
(931, 1, 'Update Company Profit when Invoice Edit', 'Company Profit : Rs. 3102.5, Invoice 62 Invoice Edit', '2024-10-24', '19:33:18'),
(932, 1, 'Add New Invoice', 'Invoice Number : 63, Customer Name : Cash, Date : 2024-10-25, Total : 500, Discount : 0, Advance : 500, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-25', '10:28:04'),
(933, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-25', '10:28:04'),
(934, 1, 'Add Product Cost to Stock Account', 'Rs. 130', '2024-10-25', '10:28:04'),
(935, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100', '2024-10-25', '10:28:04'),
(936, 1, 'Add Commision - Update Salary Table', 'Add Commison to Kasun Rs. 100 for MTB Tempered', '2024-10-25', '10:28:04'),
(937, 1, 'Add New Sale', 'Add New Sale : MTB Tempered', '2024-10-25', '10:28:04'),
(938, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of MTB Tempered in Stock', '2024-10-25', '10:28:04'),
(939, 1, 'Update Product Has_Stock State', 'MTB Tempered is In Stock', '2024-10-25', '10:28:04'),
(940, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 270', '2024-10-25', '10:28:04'),
(941, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 63 - Cash, Profit : Rs. 270, Rs. 270 by 1 ', '2024-10-25', '10:28:04'),
(942, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 63, Total Bill Cost : 230, Total Bill Profit : 270', '2024-10-25', '10:28:04'),
(943, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 63, Rs. 500', '2024-10-25', '10:28:04'),
(944, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 63 - Cash, Payment Method : Cash, Advance : Rs. 500, Rs. 500 by 1 ', '2024-10-25', '10:28:04'),
(945, 1, 'Update Cash In Hand Amount Manually', 'Update Cash In Hand Amount to Rs.9100', '2024-10-25', '10:30:24'),
(946, 1, 'Update Cash In Hand Amount Manually', 'Update Cash In Hand Amount to Rs.9800', '2024-10-25', '10:31:15'),
(947, 1, 'Add New Repair Invoice', 'Invoice Number : 64, Customer Name : Cash, Date : 2024-10-25, Total : 5500, Discount : 0, Advance : 5500, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-25', '10:36:40'),
(948, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-25', '10:36:40'),
(949, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 800.00', '2024-10-25', '10:36:40'),
(950, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 800.00 for Vivo y65 Display Replacement', '2024-10-25', '10:36:40'),
(951, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Vivo y65 Display Replacement', '2024-10-25', '10:36:40'),
(952, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Vivo y65 Display Replacement', '2024-10-25', '10:36:40'),
(953, 1, 'Fall Sell Item from Stock', 'Fall Vivo y65 Display Replacement in Stock', '2024-10-25', '10:36:40'),
(954, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 2200', '2024-10-25', '10:36:40'),
(955, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 64 - Cash, Profit : Rs. 2200, Rs. 2200 by 1 ', '2024-10-25', '10:36:40'),
(956, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 64, Total Bill Cost : 3300, Total Bill Profit : 2200', '2024-10-25', '10:36:40'),
(957, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 64, Rs. 5500', '2024-10-25', '10:36:40'),
(958, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 64 - Cash, Payment Method : Cash, Advance : Rs. 5500, Rs. 5500 by 1 ', '2024-10-25', '10:36:40'),
(959, 1, 'Add New Invoice', 'Invoice Number : 65, Customer Name : Cash, Date : 2024-10-25, Total : 1300, Discount : 0, Advance : 1300, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-25', '10:44:27'),
(960, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-25', '10:44:27'),
(961, 1, 'Sale One-Time-Product', 'M02 back cover', '2024-10-25', '10:44:27'),
(962, 1, 'Add Product Cost to Stock Account', 'Rs. 130', '2024-10-25', '10:44:27'),
(963, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100', '2024-10-25', '10:44:27'),
(964, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 100 for MTB Tempered', '2024-10-25', '10:44:27'),
(965, 1, 'Add New Sale', 'Add New Sale : MTB Tempered', '2024-10-25', '10:44:27'),
(966, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of MTB Tempered in Stock', '2024-10-25', '10:44:27'),
(967, 1, 'Update Product Has_Stock State', 'MTB Tempered is In Stock', '2024-10-25', '10:44:27'),
(968, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 1070', '2024-10-25', '10:44:27'),
(969, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 65 - Cash, Profit : Rs. 1070, Rs. 1070 by 1 ', '2024-10-25', '10:44:27'),
(970, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 65, Total Bill Cost : 230, Total Bill Profit : 1070', '2024-10-25', '10:44:27'),
(971, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 65, Rs. 1300', '2024-10-25', '10:44:27'),
(972, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 65 - Cash, Payment Method : Cash, Advance : Rs. 1300, Rs. 1300 by 1 ', '2024-10-25', '10:44:27'),
(973, 1, 'Add New Invoice', 'Invoice Number : 66, Customer Name : Cash, Date : 2024-10-25, Total : 1700, Discount : 0, Advance : 1700, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-25', '11:05:44'),
(974, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-25', '11:05:45'),
(975, 1, 'Add Product Cost to Stock Account', 'Rs. 200', '2024-10-25', '11:05:45'),
(976, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-25', '11:05:45'),
(977, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for Samsung J5 2016 back Cover', '2024-10-25', '11:05:45'),
(978, 1, 'Add New Sale', 'Add New Sale : Samsung J5 2016 back Cover', '2024-10-25', '11:05:45'),
(979, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of Samsung J5 2016 back Cover in Stock', '2024-10-25', '11:05:45'),
(980, 1, 'Update Product Has_Stock State', 'Samsung J5 2016 back Cover is In Stock', '2024-10-25', '11:05:45'),
(981, 1, 'Add Product Cost to Stock Account', 'Rs. 250', '2024-10-25', '11:05:45'),
(982, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-25', '11:05:45'),
(983, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for Samsung A15 back Cover', '2024-10-25', '11:05:45'),
(984, 1, 'Add New Sale', 'Add New Sale : Samsung A15 back Cover', '2024-10-25', '11:05:45'),
(985, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of Samsung A15 back Cover in Stock', '2024-10-25', '11:05:45'),
(986, 1, 'Update Product Has_Stock State', 'Samsung A15 back Cover is In Stock', '2024-10-25', '11:05:45'),
(987, 1, 'Add Product Cost to Stock Account', 'Rs. 130', '2024-10-25', '11:05:45'),
(988, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100', '2024-10-25', '11:05:45'),
(989, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 100 for MTB Tempered', '2024-10-25', '11:05:45'),
(990, 1, 'Add New Sale', 'Add New Sale : MTB Tempered', '2024-10-25', '11:05:45'),
(991, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of MTB Tempered in Stock', '2024-10-25', '11:05:45'),
(992, 1, 'Update Product Has_Stock State', 'MTB Tempered is In Stock', '2024-10-25', '11:05:45'),
(993, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 1020', '2024-10-25', '11:05:45'),
(994, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 66 - Cash, Profit : Rs. 1020, Rs. 1020 by 1 ', '2024-10-25', '11:05:45'),
(995, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 66, Total Bill Cost : 680, Total Bill Profit : 1020', '2024-10-25', '11:05:45'),
(996, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 66, Rs. 1700', '2024-10-25', '11:05:45'),
(997, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 66 - Cash, Payment Method : Cash, Advance : Rs. 1700, Rs. 1700 by 1 ', '2024-10-25', '11:05:45'),
(998, 1, 'Add New Invoice', 'Invoice Number : 67, Customer Name : Cash, Date : 2024-10-25, Total : 700, Discount : 0, Advance : 700, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-25', '11:10:00'),
(999, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-25', '11:10:00'),
(1000, 1, 'Add Product Cost to Stock Account', 'Rs. 150', '2024-10-25', '11:10:00'),
(1001, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100', '2024-10-25', '11:10:00'),
(1002, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 100 for Super D tempered Glass ', '2024-10-25', '11:10:00'),
(1003, 1, 'Add New Sale', 'Add New Sale : Super D tempered Glass ', '2024-10-25', '11:10:00'),
(1004, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of Super D tempered Glass  in Stock', '2024-10-25', '11:10:00'),
(1005, 1, 'Update Product Has_Stock State', 'Super D tempered Glass  is In Stock', '2024-10-25', '11:10:00'),
(1006, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 450', '2024-10-25', '11:10:00'),
(1007, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 67 - Cash, Profit : Rs. 450, Rs. 450 by 1 ', '2024-10-25', '11:10:00'),
(1008, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 67, Total Bill Cost : 250, Total Bill Profit : 450', '2024-10-25', '11:10:00'),
(1009, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 67, Rs. 700', '2024-10-25', '11:10:00'),
(1010, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 67 - Cash, Payment Method : Cash, Advance : Rs. 700, Rs. 700 by 1 ', '2024-10-25', '11:10:00'),
(1011, 1, 'Add New Repair Invoice', 'Invoice Number : 68, Customer Name : Cash, Date : 2024-10-25, Total : 5300, Discount : 0, Advance : 5300, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-25', '11:13:38'),
(1012, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-25', '11:13:38'),
(1013, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 800.00', '2024-10-25', '11:13:38'),
(1014, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 800.00 for oppo A5S Display replacement', '2024-10-25', '11:13:38'),
(1015, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : oppo A5S Display replacement', '2024-10-25', '11:13:38'),
(1016, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : oppo A5S Display replacement', '2024-10-25', '11:13:38'),
(1017, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100.00', '2024-10-25', '11:13:38'),
(1018, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 100.00 for Clear Tempered Glass ', '2024-10-25', '11:13:38'),
(1019, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-25', '11:13:38'),
(1020, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-25', '11:13:38'),
(1021, 1, 'Fall Sell Item from Stock', 'Fall Clear Tmpered glass in Stock', '2024-10-25', '11:13:38'),
(1022, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 1750', '2024-10-25', '11:13:38'),
(1023, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 68 - Cash, Profit : Rs. 1750, Rs. 1750 by 1 ', '2024-10-25', '11:13:38'),
(1024, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 68, Total Bill Cost : 3550, Total Bill Profit : 1750', '2024-10-25', '11:13:38'),
(1025, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 68, Rs. 5300', '2024-10-25', '11:13:38'),
(1026, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 68 - Cash, Payment Method : Cash, Advance : Rs. 5300, Rs. 5300 by 1 ', '2024-10-25', '11:13:38'),
(1027, 1, 'Add Petty Cash', 'for : sig , Rs. 500, By : lakmal', '2024-10-25', '11:24:03'),
(1028, 1, 'Fall Petty Cash from cash_in_hand Account and profit', 'for : sig , Rs. 500, By : lakmal, Account : cash_in_hand and profit', '2024-10-25', '11:24:03'),
(1029, 1, 'Transaction Log', 'Transaction Type : Petty Cash, description : sig, Rs. -500 by 1 ', '2024-10-25', '11:24:03'),
(1030, 1, 'Add New Repair Invoice', 'Invoice Number : 69, Customer Name : Cash, Date : 2024-10-25, Total : 500, Discount : 0, Advance : 500, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-25', '11:25:14'),
(1031, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-25', '11:25:14'),
(1032, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0.00', '2024-10-25', '11:25:14'),
(1033, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 0.00 for charging Port Replacement', '2024-10-25', '11:25:14'),
(1034, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : charging Port Replacement', '2024-10-25', '11:25:14'),
(1035, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : charging Port Replacement', '2024-10-25', '11:25:14'),
(1036, 1, 'Fall Sell Item from Stock', 'Fall charging port in Stock', '2024-10-25', '11:25:14'),
(1037, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 300', '2024-10-25', '11:25:14'),
(1038, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 69 - Cash, Profit : Rs. 300, Rs. 300 by 1 ', '2024-10-25', '11:25:14'),
(1039, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 69, Total Bill Cost : 200, Total Bill Profit : 300', '2024-10-25', '11:25:14'),
(1040, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 69, Rs. 500', '2024-10-25', '11:25:14'),
(1041, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 69 - Cash, Payment Method : Cash, Advance : Rs. 500, Rs. 500 by 1 ', '2024-10-25', '11:25:14'),
(1042, 1, 'Add New Repair Invoice', 'Invoice Number : 70, Customer Name : Cash, Date : 2024-10-25, Total : 400, Discount : 0, Advance : 400, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-25', '11:33:36'),
(1043, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-25', '11:33:36'),
(1044, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 200.00', '2024-10-25', '11:33:36'),
(1045, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 200.00 for software Frp', '2024-10-25', '11:33:36'),
(1046, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : software Frp', '2024-10-25', '11:33:36'),
(1047, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : software Frp', '2024-10-25', '11:33:36'),
(1048, 1, 'Fall Sell Item from Stock', 'Fall Software in Stock', '2024-10-25', '11:33:36'),
(1049, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 199', '2024-10-25', '11:33:36'),
(1050, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 70 - Cash, Profit : Rs. 199, Rs. 199 by 1 ', '2024-10-25', '11:33:36'),
(1051, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 70, Total Bill Cost : 201, Total Bill Profit : 199', '2024-10-25', '11:33:36'),
(1052, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 70, Rs. 400', '2024-10-25', '11:33:36'),
(1053, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 70 - Cash, Payment Method : Cash, Advance : Rs. 400, Rs. 400 by 1 ', '2024-10-25', '11:33:36'),
(1054, 1, 'Add Petty Cash', 'for : used phone , Rs. 5000, By : lakmal', '2024-10-25', '11:44:30'),
(1055, 1, 'Fall Petty Cash from cash_in_hand Account and profit', 'for : used phone , Rs. 5000, By : lakmal, Account : cash_in_hand and profit', '2024-10-25', '11:44:30'),
(1056, 1, 'Transaction Log', 'Transaction Type : Petty Cash, description : used phone, Rs. -5000 by 1 ', '2024-10-25', '11:44:30'),
(1057, 1, 'Add New Commission Record', 'Commission Added for M02 back cover in Invoice Number : <a href=\'/invoice/print.php?id=65\'> 65 </a>', '2024-10-25', '12:22:27'),
(1058, 1, 'Fall OneTimeProduct Cost from Company Profit', 'Fall Rs.0 in Company Profit Account for Repair/Service ID : 3', '2024-10-25', '12:22:27'),
(1059, 1, 'Transaction Log', 'Transaction Type : Fall OneTimeProduct Cost from Company Profit, description : Fall Rs.0 in Company Profit Account for Repair/Service ID : 3, Rs. 0 by 1 ', '2024-10-25', '12:22:27'),
(1060, 1, 'Update Invoice Cost and Profit', 'Update Invoice (ID:65) for Repair/Service ID : 3', '2024-10-25', '12:22:27'),
(1061, 1, 'Update OneTimeProduct Sale', 'Update OneTimeProduct (ID:3) for Invoice ID : 65, Cost : 0, Profit : 600.00, worker : lakmal', '2024-10-25', '12:22:27'),
(1062, 1, 'Add Salary Commission', 'Increase Salary of lakmal for M02 back cover by Rs.0', '2024-10-25', '12:22:27'),
(1063, 1, 'Transaction Log', 'Transaction Type : Add Salary Commission, description : Increase Salary of lakmal for M02 back cover by Rs.0, Rs. 0 by 1 ', '2024-10-25', '12:22:27'),
(1064, 1, 'Add New Repair Invoice', 'Invoice Number : 71, Customer Name : Cash, Date : 2024-10-25, Total : 1500, Discount : 0, Advance : 1500, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-25', '12:22:52'),
(1065, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-25', '12:22:52'),
(1066, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 200.00', '2024-10-25', '12:22:52'),
(1067, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 200.00 for software Frp', '2024-10-25', '12:22:52'),
(1068, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : software Frp', '2024-10-25', '12:22:52'),
(1069, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : software Frp', '2024-10-25', '12:22:52'),
(1070, 1, 'Fall Sell Item from Stock', 'Fall Software in Stock', '2024-10-25', '12:22:52'),
(1071, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 1299', '2024-10-25', '12:22:52'),
(1072, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 71 - Cash, Profit : Rs. 1299, Rs. 1299 by 1 ', '2024-10-25', '12:22:52'),
(1073, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 71, Total Bill Cost : 201, Total Bill Profit : 1299', '2024-10-25', '12:22:52'),
(1074, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 71, Rs. 1500', '2024-10-25', '12:22:52'),
(1075, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 71 - Cash, Payment Method : Cash, Advance : Rs. 1500, Rs. 1500 by 1 ', '2024-10-25', '12:22:52'),
(1076, 1, 'Add New Invoice', 'Invoice Number : 72, Customer Name : Cash, Date : 2024-10-25, Total : 2500, Discount : 0, Advance : 2500, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-25', '12:51:29'),
(1077, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-25', '12:51:29'),
(1078, 1, 'Add Product Cost to Stock Account', 'Rs. 1450', '2024-10-25', '12:51:29'),
(1079, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0', '2024-10-25', '12:51:29'),
(1080, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 0 for osy08 pd 20w charger lighting', '2024-10-25', '12:51:29'),
(1081, 1, 'Add New Sale', 'Add New Sale : osy08 pd 20w charger lighting', '2024-10-25', '12:51:29'),
(1082, 1, 'Fall Sell Product Qty from Stock', 'Fall 1 of osy08 pd 20w charger lighting in Stock', '2024-10-25', '12:51:29'),
(1083, 1, 'Update Product Has_Stock State', 'osy08 pd 20w charger lighting is In Stock', '2024-10-25', '12:51:29'),
(1084, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 1050', '2024-10-25', '12:51:29'),
(1085, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 72 - Cash, Profit : Rs. 1050, Rs. 1050 by 1 ', '2024-10-25', '12:51:29'),
(1086, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 72, Total Bill Cost : 1450, Total Bill Profit : 1050', '2024-10-25', '12:51:29'),
(1087, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 72, Rs. 2500', '2024-10-25', '12:51:29'),
(1088, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 72 - Cash, Payment Method : Cash, Advance : Rs. 2500, Rs. 2500 by 1 ', '2024-10-25', '12:51:29'),
(1089, 1, 'Add New Repair Invoice', 'Invoice Number : 73, Customer Name : Cash, Date : 2024-10-25, Total : 5100, Discount : 0, Advance : 5100, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-25', '13:05:45'),
(1090, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-25', '13:05:45'),
(1091, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 700.00', '2024-10-25', '13:05:45'),
(1092, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 700.00 for Y5 2018 Display Replacement', '2024-10-25', '13:05:45'),
(1093, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Y5 2018 Display Replacement', '2024-10-25', '13:05:45'),
(1094, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Y5 2018 Display Replacement', '2024-10-25', '13:05:45'),
(1095, 1, 'Fall Sell Item from Stock', 'Fall Y5 2018 Display in Stock', '2024-10-25', '13:05:45'),
(1096, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 100.00', '2024-10-25', '13:05:45'),
(1097, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 100.00 for Clear Tempered Glass ', '2024-10-25', '13:05:45'),
(1098, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-25', '13:05:45'),
(1099, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : Clear Tempered Glass ', '2024-10-25', '13:05:45'),
(1100, 1, 'Fall Sell Item from Stock', 'Fall Clear Tmpered glass in Stock', '2024-10-25', '13:05:45'),
(1101, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 1650', '2024-10-25', '13:05:45'),
(1102, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 73 - Cash, Profit : Rs. 1650, Rs. 1650 by 1 ', '2024-10-25', '13:05:45'),
(1103, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 73, Total Bill Cost : 3450, Total Bill Profit : 1650', '2024-10-25', '13:05:45'),
(1104, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 73, Rs. 5100', '2024-10-25', '13:05:45'),
(1105, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 73 - Cash, Payment Method : Cash, Advance : Rs. 5100, Rs. 5100 by 1 ', '2024-10-25', '13:05:45'),
(1106, 1, 'Add Petty Cash', 'for : colombo , Rs. 25000, By : lakmal', '2024-10-25', '13:46:32'),
(1107, 1, 'Fall Petty Cash from cash_in_hand Account and profit', 'for : colombo , Rs. 25000, By : lakmal, Account : cash_in_hand and profit', '2024-10-25', '13:46:32'),
(1108, 1, 'Transaction Log', 'Transaction Type : Petty Cash, description : colombo, Rs. -25000 by 1 ', '2024-10-25', '13:46:32'),
(1109, 1, 'Add New Invoice', 'Invoice Number : 74, Customer Name : Cash, Date : 2024-10-26, Total : 1000, Discount : 0, Advance : 1000, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-26', '09:56:46'),
(1110, 1, 'Add New Action Log', 'Add New Invoice', '2024-10-26', '09:56:46'),
(1111, 1, 'Add Product Cost to Stock Account', 'Rs. 260', '2024-10-26', '09:56:46'),
(1112, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 200', '2024-10-26', '09:56:46'),
(1113, 1, 'Add Commision - Update Salary Table', 'Add Commison to lakmal Rs. 200 for MTB Tempered', '2024-10-26', '09:56:46'),
(1114, 1, 'Add New Sale', 'Add New Sale : MTB Tempered', '2024-10-26', '09:56:46'),
(1115, 1, 'Fall Sell Product Qty from Stock', 'Fall 2 of MTB Tempered in Stock', '2024-10-26', '09:56:46'),
(1116, 1, 'Update Product Has_Stock State', 'MTB Tempered is In Stock', '2024-10-26', '09:56:46'),
(1117, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 540', '2024-10-26', '09:56:46'),
(1118, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 74 - Cash, Profit : Rs. 540, Rs. 540 by 1 ', '2024-10-26', '09:56:46'),
(1119, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 74, Total Bill Cost : 460, Total Bill Profit : 540', '2024-10-26', '09:56:46'),
(1120, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 74, Rs. 1000', '2024-10-26', '09:56:46'),
(1121, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 74 - Cash, Payment Method : Cash, Advance : Rs. 1000, Rs. 1000 by 1 ', '2024-10-26', '09:56:46'),
(1122, 1, 'Update Cash In Hand Amount Manually', 'Update Cash In Hand Amount to Rs.15000', '2024-10-26', '09:58:00'),
(1123, 1, 'Add New Repair Invoice', 'Invoice Number : 75, Customer Name : Cash, Date : 2024-10-26, Total : 850, Discount : 0, Advance : 850, Balance : 0, Full Paid : 1, Payment Method : Cash', '2024-10-26', '10:44:13'),
(1124, 1, 'Add New Action Log', 'Add New Repair Invoice', '2024-10-26', '10:44:13'),
(1125, 1, 'Add Employee Commission to Employee Account', 'Employee Commission : Rs. 0.00', '2024-10-26', '10:44:13'),
(1126, 1, 'Add Commision - Update Salary Table', 'Add Commison to ifix Rs. 0.00 for charging Port Replacement', '2024-10-26', '10:44:13'),
(1127, 1, 'Add New Repair Record to sales table', 'Add New Repair Record : charging Port Replacement', '2024-10-26', '10:44:13'),
(1128, 1, 'Add New Repair Record to repair_sell_records table', 'Add New Repair Record : charging Port Replacement', '2024-10-26', '10:44:13'),
(1129, 1, 'Fall Sell Item from Stock', 'Fall charging port in Stock', '2024-10-26', '10:44:13'),
(1130, 1, 'Add Company Profit to Company Profit Account', 'Company Profit : Rs. 650', '2024-10-26', '10:44:13'),
(1131, 1, 'Transaction Log', 'Transaction Type : Invoice - Company Profit, description : Profit to Company. Inv: 75 - Cash, Profit : Rs. 650, Rs. 650 by 1 ', '2024-10-26', '10:44:13'),
(1132, 1, 'Update Invoice Total Profit And Cost', 'Invoice Number : 75, Total Bill Cost : 200, Total Bill Profit : 650', '2024-10-26', '10:44:13'),
(1133, 1, 'Add Invoice Advance Money to Cash in Hand', 'Invoice Number : 75, Rs. 850', '2024-10-26', '10:44:13'),
(1134, 1, 'Transaction Log', 'Transaction Type : Invoice - Cash In, description : 75 - Cash, Payment Method : Cash, Advance : Rs. 850, Rs. 850 by 1 ', '2024-10-26', '10:44:13');

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
(1, 1, 'Clock Out', '2024-09-29', '12:44:36'),
(2, 1, 'Clock In', '2024-10-08', '11:27:09'),
(3, 15, 'Clock In', '2024-10-08', '16:36:29'),
(4, 14, 'Clock In', '2024-10-08', '16:36:38');

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

--
-- Dumping data for table `bank_deposits`
--

INSERT INTO `bank_deposits` (`bank_deposit_id`, `bank_account`, `amount`, `deposit_date`, `deposit_time`, `employee_id`) VALUES
(1, 'Co op shop', 5000.00, '2024-10-18', '15:05:24', 1),
(2, 'Co op Other', 2000.00, '2024-10-18', '15:05:34', 1),
(3, 'Co op shop', 5000.00, '2024-10-19', '13:54:59', 1),
(4, 'Co op Other', 2000.00, '2024-10-19', '13:55:07', 1);

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
(2, 'Cash', NULL, 0),
(4, 'chr', NULL, 7);

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
(0, 'ifix', 718366077, '', '', 'Admin', '', 10600.00, 0.00, 'indroot', 1, '', 0, '2024-09-08'),
(1, 'lakmal', 718366077, '0', '0', 'Admin', '0', 682.50, 0.00, 'admin@132', 1, '', 1, '2024-09-08'),
(14, 'Udaya', 779006160, '0', '8130907192 Commercial', 'Employee', '861722787', 2765.00, 0.00, 'udaya', 1, NULL, 1, '2024-09-10'),
(15, 'Kasun', 0, '0', '0', 'Employee', '0', 1200.00, 0.00, 'kasun', 1, NULL, 1, '2024-09-10');

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
(1, 'repair', '', 'Cash', '2024-10-17', '10:55:55', 0, 'lakmal', 'lakmal', 5100.00, 0.00, 5100.00, 0.00, 3550.00, 1550.00, 1, 'Cash'),
(2, 'repair', '  ', 'Cash', '2024-10-18', '09:46:26', 0, 'lakmal', 'lakmal', 500.00, 0.00, 500.00, 0.00, 0.00, 500.00, 1, 'Cash'),
(3, 'product', '', 'Cash', '2024-10-18', '09:59:23', 0, 'lakmal', 'lakmal', 700.00, 0.00, 700.00, 0.00, 0.00, 700.00, 1, 'Cash'),
(4, 'repair', '', 'Cash', '2024-10-18', '11:07:43', 0, 'lakmal', 'lakmal', 600.00, 0.00, 600.00, 0.00, 250.00, 350.00, 1, 'Cash'),
(5, 'repair', '', 'chr', '2024-10-18', '11:44:56', 7, 'lakmal', 'lakmal', 800.00, 0.00, 800.00, 0.00, 600.00, 200.00, 1, 'Cash'),
(6, 'product', '', 'Cash', '2024-10-18', '11:51:57', 0, 'lakmal', 'lakmal', 800.00, 0.00, 800.00, 0.00, 400.00, 400.00, 1, 'Cash'),
(7, 'product', '', 'Cash', '2024-10-18', '12:22:58', 0, 'lakmal', 'lakmal', 600.00, 0.00, 600.00, 0.00, 250.00, 350.00, 1, 'Cash'),
(8, 'repair', ' ', 'Cash', '2024-10-18', '13:02:30', 0, 'lakmal', 'lakmal', 1800.00, 0.00, 1800.00, 0.00, 0.00, 1800.00, 1, 'Cash'),
(9, 'product', '', 'Cash', '2024-10-18', '13:08:56', 0, 'lakmal', 'lakmal', 800.00, 0.00, 800.00, 0.00, 400.00, 400.00, 1, 'Cash'),
(10, 'product', '', 'Cash', '2024-10-18', '13:09:46', 0, 'lakmal', 'lakmal', 500.00, 0.00, 500.00, 0.00, 250.00, 250.00, 1, 'Cash'),
(11, 'product', '', 'Cash', '2024-10-18', '13:39:23', 0, 'lakmal', 'lakmal', 1300.00, 0.00, 1300.00, 0.00, 700.00, 600.00, 1, 'Cash'),
(12, 'product', '', 'Cash', '2024-10-18', '13:57:02', 0, 'lakmal', 'lakmal', 800.00, 0.00, 800.00, 0.00, 260.00, 540.00, 1, 'Cash'),
(13, 'repair', '', 'Cash', '2024-10-18', '14:26:18', 0, 'lakmal', 'lakmal', 1000.00, 0.00, 1000.00, 0.00, 400.00, 600.00, 1, 'Cash'),
(14, 'product', '', 'Cash', '2024-10-18', '14:34:26', 0, 'lakmal', 'lakmal', 1000.00, 0.00, 1000.00, 0.00, 480.00, 520.00, 1, 'Cash'),
(15, 'repair', '', 'Cash', '2024-10-18', '15:46:07', 0, 'lakmal', 'lakmal', 1200.00, 0.00, 1164.00, 0.00, 900.00, 264.00, 1, 'CardPayment'),
(16, 'product', '', 'Cash', '2024-10-18', '16:08:47', 0, 'lakmal', 'lakmal', 300.00, 0.00, 300.00, 0.00, 150.00, 150.00, 1, 'Cash'),
(17, 'repair', '', 'Cash', '2024-10-18', '16:16:04', 0, 'lakmal', 'lakmal', 600.00, 0.00, 600.00, 0.00, 250.00, 350.00, 1, 'Cash'),
(18, 'product', '', 'Cash', '2024-10-18', '16:32:20', 0, 'lakmal', 'lakmal', 1200.00, 0.00, 1200.00, 0.00, 100.00, 500.00, 1, 'Cash'),
(19, 'repair', '', 'Cash', '2024-10-18', '16:43:40', 0, 'lakmal', 'lakmal', 5900.00, 0.00, 5900.00, 0.00, 0.00, 0.00, 1, 'Cash'),
(20, 'repair', '', 'Cash', '2024-10-18', '16:44:22', 0, 'lakmal', 'lakmal', 5900.00, 0.00, 5900.00, 0.00, 0.00, 0.00, 1, 'Cash'),
(21, 'repair', '', 'Cash', '2024-10-18', '17:19:14', 0, 'lakmal', 'lakmal', 800.00, 0.00, 800.00, 0.00, 200.00, 600.00, 1, 'Cash'),
(22, 'product', '', 'Cash', '2024-10-18', '17:53:07', 0, 'lakmal', 'lakmal', 400.00, 0.00, 400.00, 0.00, 200.00, 200.00, 1, 'Cash'),
(23, 'repair', '', 'Cash', '2024-10-18', '18:11:10', 0, 'lakmal', 'lakmal', 4500.00, 0.00, 4500.00, 0.00, 3400.00, 1100.00, 1, 'Cash'),
(24, 'product', '', 'Cash', '2024-10-18', '18:16:46', 0, 'lakmal', 'lakmal', 600.00, 0.00, 600.00, 0.00, 250.00, 350.00, 1, 'Cash'),
(25, 'repair', '', 'Cash', '2024-10-18', '18:18:47', 0, 'lakmal', 'lakmal', 1000.00, 0.00, 1000.00, 0.00, 201.00, 799.00, 1, 'Cash'),
(26, 'repair', '', 'Cash', '2024-10-19', '09:37:42', 0, 'lakmal', 'lakmal', 3600.00, 0.00, 3600.00, 0.00, 2600.00, 1000.00, 1, 'Cash'),
(27, 'repair', '', 'Cash', '2024-10-19', '09:39:14', 0, 'lakmal', 'lakmal', 400.00, 0.00, 400.00, 0.00, 201.00, 199.00, 1, 'Cash'),
(28, 'repair', '', 'Cash', '2024-10-19', '10:26:35', 0, 'lakmal', 'lakmal', 800.00, 0.00, 800.00, 0.00, 250.00, 550.00, 1, 'Cash'),
(29, 'product', ' ', 'Cash', '2024-10-19', '10:45:11', 0, 'lakmal', 'lakmal', 1700.00, 0.00, 1700.00, 0.00, 950.00, 750.00, 1, 'CardPayment'),
(30, 'product', '', 'Cash', '2024-10-19', '11:21:21', 0, 'lakmal', 'lakmal', 4600.00, 0.00, 4600.00, 0.00, 3000.00, 1600.00, 1, 'Cash'),
(31, 'repair', '', 'Cash', '2024-10-19', '11:37:20', 0, 'lakmal', 'lakmal', 5000.00, 0.00, 5000.00, 0.00, 250.00, 4750.00, 1, 'Cash'),
(32, 'product', '', 'Cash', '2024-10-19', '12:01:42', 0, 'lakmal', 'lakmal', 800.00, 0.00, 800.00, 0.00, 400.00, 400.00, 1, 'Cash'),
(33, 'repair', '', 'Cash', '2024-10-19', '12:50:22', 0, 'lakmal', 'lakmal', 9000.00, 0.00, 9000.00, 0.00, 7450.00, 1550.00, 1, 'Cash'),
(34, 'product', '', 'Cash', '2024-10-19', '12:58:52', 0, 'lakmal', 'lakmal', 3800.00, 0.00, 3800.00, 0.00, 3500.00, 300.00, 1, 'Cash'),
(35, 'product', '', 'Cash', '2024-10-19', '13:11:32', 0, 'lakmal', 'lakmal', 750.00, 0.00, 750.00, 0.00, 250.00, 500.00, 1, 'Cash'),
(36, 'product', '', 'Cash', '2024-10-19', '13:19:20', 0, 'lakmal', 'lakmal', 500.00, 0.00, 500.00, 0.00, 250.00, 250.00, 1, 'Cash'),
(37, 'product', '', 'Cash', '2024-10-19', '13:19:26', 0, 'lakmal', 'lakmal', 500.00, 0.00, 500.00, 0.00, 0.00, 500.00, 1, 'Cash'),
(38, 'repair', '', 'Cash', '2024-10-19', '13:19:55', 0, 'lakmal', 'lakmal', 500.00, 0.00, 500.00, 0.00, 201.00, 299.00, 1, 'Cash'),
(39, 'repair', '', 'Cash', '2024-10-19', '13:34:56', 0, 'lakmal', 'lakmal', 500.00, 0.00, 500.00, 0.00, 600.00, -100.00, 1, 'Cash'),
(40, 'product', '', 'Cash', '2024-10-19', '14:14:05', 0, 'lakmal', 'lakmal', 600.00, 0.00, 600.00, 0.00, 250.00, 350.00, 1, 'Cash'),
(41, 'repair', '', 'Cash', '2024-10-19', '14:21:52', 0, 'lakmal', 'lakmal', 500.00, 0.00, 500.00, 0.00, 600.00, -100.00, 1, 'Cash'),
(42, 'repair', '', 'Cash', '2024-10-19', '14:32:39', 0, 'lakmal', 'lakmal', 800.00, 0.00, 800.00, 0.00, 201.00, 599.00, 1, 'Cash'),
(43, 'repair', '', 'Cash', '2024-10-19', '14:36:00', 0, 'lakmal', 'lakmal', 3000.00, 0.00, 3000.00, 0.00, 1500.00, 1500.00, 1, 'Cash'),
(44, 'product', '', 'Cash', '2024-10-19', '14:52:36', 0, 'lakmal', 'lakmal', 1000.00, 0.00, 1000.00, 0.00, 480.00, 520.00, 1, 'Cash'),
(45, 'product', '', 'Cash', '2024-10-19', '15:58:55', 0, 'lakmal', 'lakmal', 1500.00, 0.00, 1500.00, 0.00, 800.00, 700.00, 1, 'Cash'),
(46, 'product', '', 'Cash', '2024-10-19', '16:29:24', 0, 'lakmal', 'lakmal', 800.00, 0.00, 800.00, 0.00, 250.00, 550.00, 1, 'Cash'),
(47, 'product', '', 'Cash', '2024-10-19', '18:11:55', 0, 'lakmal', 'lakmal', 800.00, 0.00, 800.00, 0.00, 475.00, 325.00, 1, 'Cash'),
(48, 'product', '', 'Cash', '2024-10-19', '18:27:58', 0, 'lakmal', 'lakmal', 1700.00, 0.00, 1700.00, 0.00, 950.00, 750.00, 1, 'Cash'),
(49, 'product', '', 'Cash', '2024-10-19', '18:46:14', 0, 'lakmal', 'lakmal', 800.00, 0.00, 800.00, 0.00, 700.00, 100.00, 1, 'Cash'),
(50, 'repair', '', 'Cash', '2024-10-19', '18:54:00', 0, 'lakmal', 'lakmal', 2400.00, 0.00, 2400.00, 0.00, 1500.00, 900.00, 1, 'Cash'),
(51, 'repair', '', 'Cash', '2024-10-20', '10:50:57', 0, 'lakmal', 'lakmal', 1500.00, 0.00, 1500.00, 0.00, 600.00, 900.00, 1, 'Cash'),
(52, 'product', '', 'Cash', '2024-10-20', '10:52:01', 0, 'lakmal', 'lakmal', 800.00, 0.00, 800.00, 0.00, 430.00, 370.00, 1, 'Cash'),
(53, 'repair', '', 'Cash', '2024-10-20', '11:20:37', 0, 'lakmal', 'lakmal', 4500.00, 0.00, 4500.00, 0.00, 2500.00, 2000.00, 1, 'Cash'),
(54, 'product', '', 'Cash', '2024-10-20', '11:25:28', 0, 'lakmal', 'lakmal', 650.00, 0.00, 650.00, 0.00, 250.00, 400.00, 1, 'Cash'),
(55, 'product', '', 'Cash', '2024-10-20', '15:02:57', 0, 'lakmal', 'lakmal', 500.00, 0.00, 500.00, 0.00, 230.00, 270.00, 1, 'Cash'),
(56, 'product', '', 'Cash', '2024-10-20', '15:22:23', 0, 'lakmal', 'lakmal', 2000.00, 0.00, 2000.00, 0.00, 1250.00, 750.00, 1, 'Cash'),
(57, 'product', '', 'Cash', '2024-10-20', '18:23:16', 0, 'lakmal', 'lakmal', 3750.00, 0.00, 3750.00, 0.00, 1450.00, 2300.00, 1, 'Cash'),
(58, 'product', '', 'Cash', '2024-10-21', '15:24:16', 0, 'lakmal', 'lakmal', 800.00, 0.00, 800.00, 0.00, 400.00, 400.00, 1, 'Cash'),
(59, 'product', '', 'Cash', '2024-10-21', '16:59:30', 0, 'lakmal', 'lakmal', 800.00, 0.00, 800.00, 0.00, 400.00, 400.00, 1, 'Cash'),
(60, 'product', '', 'Cash', '2024-10-21', '21:17:00', 0, 'lakmal', 'lakmal', 2400.00, 0.00, 2400.00, 0.00, 1400.00, 1000.00, 1, 'Cash'),
(61, 'repair', '', 'Cash', '2024-10-23', '11:25:57', 0, 'lakmal', 'lakmal', 5500.00, 0.00, 5500.00, 0.00, 2400.00, 3100.00, 1, 'Cash'),
(62, 'repair', ' ', 'Cash', '2024-10-24', '19:31:52', 0, 'lakmal', 'Udaya', 5500.00, 0.00, 5500.00, 0.00, 0.00, 5500.00, 1, 'Cash'),
(63, 'product', '', 'Cash', '2024-10-25', '10:28:04', 0, 'lakmal', 'Kasun', 500.00, 0.00, 500.00, 0.00, 230.00, 270.00, 1, 'Cash'),
(64, 'repair', '', 'Cash', '2024-10-25', '10:36:40', 0, 'lakmal', 'lakmal', 5500.00, 0.00, 5500.00, 0.00, 3300.00, 2200.00, 1, 'Cash'),
(65, 'product', '', 'Cash', '2024-10-25', '10:44:27', 0, 'lakmal', 'lakmal', 1300.00, 0.00, 1300.00, 0.00, 0.00, 600.00, 1, 'Cash'),
(66, 'product', '', 'Cash', '2024-10-25', '11:05:44', 0, 'lakmal', 'lakmal', 1700.00, 0.00, 1700.00, 0.00, 680.00, 1020.00, 1, 'Cash'),
(67, 'product', '', 'Cash', '2024-10-25', '11:10:00', 0, 'lakmal', 'lakmal', 700.00, 0.00, 700.00, 0.00, 250.00, 450.00, 1, 'Cash'),
(68, 'repair', '', 'Cash', '2024-10-25', '11:13:38', 0, 'lakmal', 'lakmal', 5300.00, 0.00, 5300.00, 0.00, 3550.00, 1750.00, 1, 'Cash'),
(69, 'repair', '', 'Cash', '2024-10-25', '11:25:14', 0, 'lakmal', 'lakmal', 500.00, 0.00, 500.00, 0.00, 200.00, 300.00, 1, 'Cash'),
(70, 'repair', '', 'Cash', '2024-10-25', '11:33:36', 0, 'lakmal', 'lakmal', 400.00, 0.00, 400.00, 0.00, 201.00, 199.00, 1, 'Cash'),
(71, 'repair', '', 'Cash', '2024-10-25', '12:22:52', 0, 'lakmal', 'lakmal', 1500.00, 0.00, 1500.00, 0.00, 201.00, 1299.00, 1, 'Cash'),
(72, 'product', '', 'Cash', '2024-10-25', '12:51:29', 0, 'lakmal', 'lakmal', 2500.00, 0.00, 2500.00, 0.00, 1450.00, 1050.00, 1, 'Cash'),
(73, 'repair', '', 'Cash', '2024-10-25', '13:05:45', 0, 'lakmal', 'lakmal', 5100.00, 0.00, 5100.00, 0.00, 3450.00, 1650.00, 1, 'Cash'),
(74, 'product', '', 'Cash', '2024-10-26', '09:56:46', 0, 'lakmal', 'lakmal', 1000.00, 0.00, 1000.00, 0.00, 460.00, 540.00, 1, 'Cash'),
(75, 'repair', '', 'Cash', '2024-10-26', '10:44:13', 0, 'lakmal', 'lakmal', 850.00, 0.00, 850.00, 0.00, 200.00, 650.00, 1, 'Cash');

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
(1, 2, 500.00, '2024-10-18', '12:03:24', 'cash_in_hand', 'Invoice Full Paid'),
(2, 8, 1800.00, '2024-10-18', '14:28:08', 'cash_in_hand', 'Invoice Full Paid');

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
(1, 3, 'watch Charger', 1, 700.00, 700.00, 0.00, 700.00, 'cleared', 'lakmal'),
(2, 18, 'tempered mtb', 1, 600.00, 600.00, 100.00, 500.00, 'cleared', 'Udaya'),
(3, 65, 'M02 back cover', 1, 600.00, 600.00, 0.00, 600.00, 'cleared', 'lakmal');

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

--
-- Dumping data for table `pettycash`
--

INSERT INTO `pettycash` (`id`, `perrycash`, `amount`, `date`, `time`, `emp_name`) VALUES
(1, 'sig', 500.00, '2024-10-18', '12:50:20', 'lakmal'),
(2, 'sig', 500.00, '2024-10-18', '14:27:06', 'lakmal'),
(3, 'daily', 2000.00, '2024-10-18', '14:53:53', 'lakmal'),
(4, 'sig', 1000.00, '2024-10-19', '10:54:07', 'lakmal'),
(5, 'tea', 300.00, '2024-10-19', '13:01:04', 'lakmal'),
(6, 'sig', 500.00, '2024-10-19', '15:05:42', 'lakmal'),
(7, 'home', 1500.00, '2024-10-19', '15:06:32', 'lakmal'),
(8, 'sig', 500.00, '2024-10-25', '11:24:03', 'lakmal'),
(9, 'used phone', 5000.00, '2024-10-25', '11:44:30', 'lakmal'),
(10, 'colombo', 25000.00, '2024-10-25', '13:46:32', 'lakmal');

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
(8, 'Oms 5c battery(3 month warranty)', NULL, 17, 800.00, 160.00, 540.00, '1', 5, 100.00, NULL, '0'),
(10, 'wireless Keyboard', NULL, 1, 4650.00, 3600.00, 1050.00, '1', 0, 0.00, NULL, '0'),
(11, 'Oms X3 Speeker', NULL, 2, 2850.00, 1850.00, 1000.00, '1', 1, 0.00, NULL, '0'),
(12, 'sy 718 Speeker', NULL, 1, 3750.00, 3250.00, 500.00, '1', 0, 0.00, NULL, '0'),
(13, 'Car holder JE027', NULL, 4, 2350.00, 1350.00, 1000.00, '1', 1, 0.00, NULL, '0'),
(14, 'bike holder By555', NULL, 4, 1350.00, 700.00, 650.00, '1', 1, 0.00, NULL, '0'),
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
(43, '45W type C adepter', NULL, 0, 1850.00, 980.00, 870.00, '0', 0, 0.00, NULL, '0'),
(44, '15w Samsung travel adapter', NULL, 2, 1750.00, 980.00, 770.00, '1', 0, 0.00, NULL, '0'),
(45, '25W pd adapter with cable', NULL, 2, 2600.00, 1300.00, 1300.00, '1', 0, 0.00, NULL, '0'),
(46, 'sm12 sinha micro charger', NULL, 0, 1450.00, 700.00, 750.00, '0', 0, 0.00, NULL, '0'),
(47, 'osy08 pd 20w charger lighting', NULL, 4, 2650.00, 1450.00, 1200.00, '1', 2, 0.00, NULL, '0'),
(48, 'Osc26 onesom charger', NULL, 7, 1950.00, 1250.00, 700.00, '1', 2, 0.00, NULL, '0'),
(49, 'om310 10w adapter', NULL, 3, 1250.00, 750.00, 500.00, '1', 0, 0.00, NULL, '0'),
(50, '25W samsung pd adapter', NULL, 2, 2250.00, 1250.00, 1000.00, '1', 0, 0.00, NULL, '0'),
(51, 'md812b apple 5W charger', NULL, 4, 1250.00, 650.00, 600.00, '1', 0, 0.00, NULL, '0'),
(52, '20W type c apple adapter 06 month warranty', NULL, 1, 4950.00, 1250.00, 3700.00, '1', 0, 0.00, NULL, '0'),
(53, '20W C apple adapter 1 month warranty', NULL, 4, 2650.00, 1250.00, 1400.00, '1', 1, 0.00, NULL, '0'),
(54, 'xpc25 type c normal charger', NULL, 3, 850.00, 400.00, 450.00, '1', 5, 0.00, NULL, '0'),
(55, 'Xpc25 lightning normal charger', NULL, 4, 850.00, 400.00, 450.00, '1', 3, 0.00, NULL, '0'),
(56, 'XpC 30 lightning charger', NULL, 6, 1950.00, 1250.00, 700.00, '1', 3, 0.00, NULL, '0'),
(57, 'SL60 Samsung fast charger', NULL, 1, 2650.00, 1450.00, 1200.00, '1', 0, 0.00, NULL, '0'),
(58, 'Xpc type c normal charger', NULL, 14, 850.00, 450.00, 400.00, '1', 0, 0.00, NULL, '0'),
(59, 'Xpc25 micro normal charger', NULL, 74, 850.00, 400.00, 450.00, '1', 5, 0.00, NULL, '0'),
(60, 'Om418 type-c chager', NULL, 10, 2650.00, 1400.00, 1250.00, '1', 4, 0.00, NULL, '0'),
(62, 'Om418 Micro chager', NULL, 6, 2650.00, 1400.00, 1250.00, '1', 5, 0.00, NULL, '0'),
(63, 'Om418 Lightning', NULL, 2, 2600.00, 1400.00, 1200.00, '1', 2, 0.00, NULL, '0'),
(64, 'Xpc30 Micro chager', NULL, 6, 2200.00, 1250.00, 950.00, '1', 2, 0.00, NULL, '0'),
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
(82, 'Iphone Magsafe Case ', NULL, 16, 1200.00, 480.00, 720.00, '1', 0, 0.00, NULL, '0'),
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
(95, 'OS-A04 Type-c to Lightning Cable', NULL, 4, 950.00, 475.00, 475.00, '1', 0, 0.00, NULL, '0'),
(96, 'OS-A22 C to C cable', NULL, 6, 950.00, 450.00, 500.00, '1', 0, 0.00, NULL, '0'),
(97, 'K1000 MINI Keyboard', NULL, 2, 1750.00, 950.00, 800.00, '1', 0, 0.00, NULL, '0'),
(98, 'OS-A03 C to C cable', NULL, 1, 1650.00, 850.00, 800.00, '1', 0, 0.00, NULL, '0'),
(99, 'YS-859 Type C to C', NULL, 4, 1950.00, 980.00, 970.00, '1', 0, 0.00, NULL, '0'),
(100, 'YS-859 Ligthning to C cable', NULL, 3, 1850.00, 950.00, 900.00, '1', 0, 0.00, NULL, '0'),
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
(117, 'OS-E02 Lightning Earphone', NULL, 4, 1750.00, 950.00, 800.00, '1', 0, 0.00, NULL, '0'),
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
(131, 'OM-168 Micro Cable ', NULL, 25, 700.00, 340.00, 360.00, '1', 2, 0.00, NULL, '0'),
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
(174, 'Iphone 7  ', NULL, 13, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
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
(196, 'Redmi note 9 pro back cover', NULL, 2, 600.00, 250.00, 350.00, '1', 3, 0.00, NULL, '0'),
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
(220, 'AKG -21 Type-C Headphone', NULL, 2, 1650.00, 800.00, 850.00, '1', 0, 0.00, NULL, '0'),
(221, 'CB-06 Micro Cable ', NULL, 19, 350.00, 150.00, 200.00, '1', 2, 0.00, NULL, '0'),
(222, 'CA-03 Type-c Cable ', NULL, 20, 700.00, 300.00, 400.00, '1', 2, 0.00, NULL, '0'),
(223, '5CBS Nokia Battery', NULL, 0, 850.00, 350.00, 500.00, '0', 2, 0.00, NULL, '0'),
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
(235, 'pixel 6 back cover', NULL, 1, 750.00, 250.00, 500.00, '1', 1, 0.00, NULL, '0'),
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
(248, 'Tecno pop 5 lite back cover', NULL, 6, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
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
(288, 'Redmi A3 back Cover', NULL, 5, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(289, '25W Oms samsum (6 month warranty) ', NULL, 3, 3950.00, 1450.00, 2500.00, '1', 1, 0.00, NULL, '0'),
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
(314, 'J2 2015', NULL, 5, 3.00, 200.00, -197.00, '1', 1, 0.00, NULL, '0'),
(315, 'Samsung J2 prime back Cover', NULL, 6, 600.00, 250.00, 350.00, '1', 1, 0.00, NULL, '0'),
(316, 'Samsung J3 back Cover', NULL, 4, 600.00, 200.00, 400.00, '1', 1, 0.00, NULL, '0'),
(317, 'Samsung J4 back Cover', NULL, 3, 600.00, 200.00, 400.00, '1', 1, 0.00, NULL, '0'),
(318, 'Samsung J4 plus back Cover', NULL, 8, 600.00, 200.00, 400.00, '1', 1, 0.00, NULL, '0'),
(319, 'Samsung J5 back Cover', NULL, 11, 450.00, 150.00, 300.00, '1', 1, 0.00, NULL, '0'),
(320, 'Samsung J5 2016 back Cover', NULL, 2, 500.00, 200.00, 300.00, '1', 0, 0.00, NULL, '0'),
(321, 'Samsung J6 back Cover', NULL, 8, 500.00, 200.00, 300.00, '1', 1, 0.00, NULL, '0'),
(322, 'Samsung J6 Plus back Cover', NULL, 9, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(323, 'Samsung J7 2015 back Cover', NULL, 3, 600.00, 250.00, 350.00, '1', 1, 0.00, NULL, '0'),
(324, 'Samsung J7 2016 back Cover', NULL, 4, 600.00, 250.00, 350.00, '1', 1, 0.00, NULL, '0'),
(325, 'Samsung J7 prime back Cover', NULL, 5, 600.00, 200.00, 400.00, '1', 1, 0.00, NULL, '0'),
(326, 'Samsung J7 Duo back Cover', NULL, 2, 500.00, 200.00, 300.00, '1', 0, 0.00, NULL, '0'),
(328, 'Samsung J8 back Cover', NULL, 7, 700.00, 250.00, 450.00, '1', 0, 0.00, NULL, '0'),
(329, 'Poco M3 Back Cover', NULL, 4, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(330, 'Poco X3 Back Cover', NULL, 4, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(331, 'Poco F3 Back Cover', NULL, 3, 600.00, 250.00, 350.00, '1', 1, 0.00, NULL, '0'),
(332, 'Realme C11 2021 Back cover', NULL, 10, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(333, 'Realme C11 Back cover', NULL, 5, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
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
(355, 'Samsung M01 Core back Cover', NULL, 5, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(356, 'Samsung A2 Core  back Cover', NULL, 11, 650.00, 250.00, 400.00, '1', 4, 0.00, NULL, '0'),
(357, 'Samsung A10 back Cover', NULL, 31, 650.00, 250.00, 400.00, '1', 8, 0.00, NULL, '0'),
(358, 'Samsung A01M01 back Cover', NULL, 16, 650.00, 250.00, 400.00, '1', 5, 0.00, NULL, '0'),
(360, 'Samsung A10S back Cover', NULL, 16, 650.00, 250.00, 400.00, '1', 5, 0.00, NULL, '0'),
(361, 'Samsung A11 back Cover', NULL, 14, 650.00, 250.00, 400.00, '1', 5, 0.00, NULL, '0'),
(362, 'Samsung A12 back Cover', NULL, 20, 650.00, 250.00, 400.00, '1', 5, 0.00, NULL, '0'),
(364, 'Samsung A13 back Cover', NULL, 11, 650.00, 250.00, 400.00, '1', 5, 0.00, NULL, '0'),
(365, 'Samsung A15 back Cover', NULL, 9, 650.00, 250.00, 400.00, '1', 5, 0.00, NULL, '0'),
(366, 'Samsung A14 back Cover', NULL, 14, 650.00, 250.00, 400.00, '1', 5, 0.00, NULL, '0'),
(368, 'Samsung A20/A30 back Cover', NULL, 4, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(369, 'Samsung M20 back Cover', NULL, 8, 650.00, 250.00, 400.00, '1', 3, 0.00, NULL, '0'),
(370, 'Samsung A20S back Cover', NULL, 2, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(371, 'Samsung M31 back Cover', NULL, 13, 650.00, 250.00, 400.00, '1', 5, 0.00, NULL, '0'),
(372, 'Samsung A32 back Cover', NULL, 8, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(373, 'Samsung M32 back Cover', NULL, 3, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(374, 'Samsung A22 4g back Cover', NULL, 2, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(375, 'Samsung A23 back Cover', NULL, 2, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(376, 'CH36 oms 25W Charger', NULL, 3, 3950.00, 1400.00, 2550.00, '1', 2, 0.00, NULL, '0'),
(377, 'Samsung A35 back Cover', NULL, 9, 650.00, 250.00, 400.00, '1', 3, 0.00, NULL, '0'),
(378, 'Samsung A53 back Cover', NULL, 3, 650.00, 250.00, 400.00, '1', 0, 0.00, NULL, '0'),
(379, 'Samsung A33 back Cover', NULL, 3, 650.00, 250.00, 400.00, '1', 0, 0.00, NULL, '0'),
(380, 'Samsung A24 back Cover', NULL, 6, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(381, 'Samsung A54 back Cover', NULL, 9, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(383, 'Samsung A34 back Cover', NULL, 3, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(385, 'Samsung A50 back Cover', NULL, 5, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(387, 'Samsung A70 back Cover', NULL, 4, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(388, 'Samsung A71 back Cover', NULL, 1, 650.00, 250.00, 400.00, '1', 0, 0.00, NULL, '0'),
(389, 'Samsung A72 back Cover', NULL, 1, 600.00, 250.00, 350.00, '1', 0, 0.00, NULL, '0'),
(391, 'Samsung A73 back Cover', NULL, 3, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(392, 'Samsung A21s back Cover', NULL, 23, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(393, 'Samsung A05 back Cover', NULL, 17, 650.00, 250.00, 400.00, '1', 8, 0.00, NULL, '0'),
(394, 'Samsung A05s back Cover', NULL, 33, 650.00, 250.00, 400.00, '1', 5, 0.00, NULL, '0'),
(395, 'Samsung A04 back Cover', NULL, 12, 650.00, 250.00, 400.00, '1', 3, 0.00, NULL, '0'),
(396, 'Samsung A04s back Cover', NULL, 11, 650.00, 250.00, 400.00, '1', 5, 0.00, NULL, '0'),
(397, 'Samsung A03 core back Cover', NULL, 4, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(398, 'Samsung A03s back Cover', NULL, 12, 650.00, 250.00, 400.00, '1', 5, 0.00, NULL, '0'),
(400, 'Samsung A03 back Cover', NULL, 18, 650.00, 250.00, 400.00, '1', 5, 0.00, NULL, '0'),
(401, 'Samsung A02s back Cover', NULL, 17, 650.00, 250.00, 400.00, '1', 5, 0.00, NULL, '0'),
(402, 'Samsung A02 back Cover', NULL, 28, 650.00, 250.00, 400.00, '1', 5, 0.00, NULL, '0'),
(403, 'Huawei y3 17 back cover', NULL, 3, 450.00, 200.00, 250.00, '1', 1, 0.00, NULL, '0'),
(404, 'Huawei y3 ii back cover', NULL, 2, 450.00, 200.00, 250.00, '1', 0, 0.00, NULL, '0'),
(405, 'Huawei y5 17 back cover', NULL, 5, 450.00, 200.00, 250.00, '1', 2, 0.00, NULL, '0'),
(406, 'Huawei y5 ii back cover', NULL, 12, 450.00, 200.00, 250.00, '1', 2, 0.00, NULL, '0'),
(407, 'Huawei y5 19 back cover', NULL, 4, 450.00, 200.00, 250.00, '1', 1, 0.00, NULL, '0'),
(408, 'Huawei y5 p back cover', NULL, 5, 450.00, 200.00, 250.00, '1', 1, 0.00, NULL, '0'),
(409, 'Huawei y5 18 back cover', NULL, 5, 450.00, 200.00, 250.00, '1', 2, 0.00, NULL, '0'),
(410, 'Huawei y6 ii back cover', NULL, 2, 450.00, 200.00, 250.00, '1', 1, 0.00, NULL, '0'),
(411, 'Huawei y6 18 back cover', NULL, 7, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(412, 'Huawei y6 19 back cover', NULL, 9, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(413, 'Huawei y6 p back cover', NULL, 4, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(414, 'Huawei y7 17 back cover', NULL, 2, 600.00, 200.00, 400.00, '1', 0, 0.00, NULL, '0'),
(415, 'Huawei y7 18 back cover', NULL, 16, 600.00, 200.00, 400.00, '1', 2, 0.00, NULL, '0'),
(416, 'Huawei y7 19  back cover', NULL, 4, 500.00, 200.00, 300.00, '1', 2, 0.00, NULL, '0'),
(417, 'Huawei y7p back cover', NULL, 6, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(418, 'Huawei y7A back cover', NULL, 3, 600.00, 200.00, 400.00, '1', 1, 0.00, NULL, '0'),
(419, 'Huawei y9 18 back cover', NULL, 5, 500.00, 200.00, 300.00, '1', 2, 0.00, NULL, '0'),
(420, 'Huawei y9 19 back cover', NULL, 7, 650.00, 250.00, 400.00, '1', 3, 0.00, NULL, '0'),
(422, 'Huawei y9 prime 19 back cover', NULL, 9, 600.00, 200.00, 400.00, '1', 2, 0.00, NULL, '0'),
(423, 'Huawei y9s back cover', NULL, 3, 650.00, 250.00, 400.00, '1', 0, 0.00, NULL, '0'),
(424, 'Huawei nova 2i back cover', NULL, 9, 550.00, 200.00, 350.00, '1', 3, 0.00, NULL, '0'),
(425, 'Huawei nova 3i back cover', NULL, 12, 600.00, 250.00, 350.00, '1', 3, 0.00, NULL, '0'),
(426, 'Huawei nova 7se back cover', NULL, 2, 450.00, 200.00, 250.00, '1', 0, 0.00, NULL, '0'),
(427, 'Huawei nova 8i back cover', NULL, 3, 650.00, 450.00, 200.00, '1', 0, 0.00, NULL, '0'),
(428, 'Huawei nova y70 back cover', NULL, 1, 650.00, 252.00, 398.00, '1', 0, 0.00, NULL, '0'),
(429, 'Huawei nova y90 back cover', NULL, 1, 650.00, 250.00, 400.00, '1', 0, 0.00, NULL, '0'),
(430, 'Huawei p8 lite back cover', NULL, 1, 450.00, 200.00, 250.00, '1', 0, 0.00, NULL, '0'),
(431, 'Huawei gr3 17 back cover', NULL, 3, 450.00, 200.00, 250.00, '1', 0, 0.00, NULL, '0'),
(432, 'Huawei gr5 16 back cover', NULL, 6, 450.00, 200.00, 250.00, '1', 2, 0.00, NULL, '0'),
(433, 'Huawei gr5 17 back cover', NULL, 2, 450.00, 200.00, 250.00, '1', 2, 0.00, NULL, '0'),
(434, 'Oms E250 Battery (3 month warranty ) ', NULL, 5, 800.00, 400.00, 400.00, '1', 2, 0.00, NULL, '0'),
(435, 'OPPO A3s back cover', NULL, 29, 600.00, 400.00, 200.00, '1', 5, 0.00, NULL, '0'),
(436, 'OPPO A5s back cover', NULL, 14, 600.00, 200.00, 400.00, '1', 5, 0.00, NULL, '0'),
(437, 'OPPO A1k back cover', NULL, 22, 600.00, 400.00, 200.00, '1', 5, 0.00, NULL, '0'),
(438, 'OPPO A37 back cover', NULL, 7, 600.00, 200.00, 400.00, '1', 3, 0.00, NULL, '0'),
(439, 'OPPO A15 back cover', NULL, 8, 600.00, 200.00, 400.00, '1', 4, 0.00, NULL, '0'),
(440, 'OPPO A16 back cover', NULL, 5, 650.00, 250.00, 400.00, '1', 3, 0.00, NULL, '0'),
(441, 'OPPO A17 back cover', NULL, 3, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(442, 'OPPO A33 back cover', NULL, 5, 500.00, 250.00, 250.00, '1', 2, 0.00, NULL, '0'),
(443, 'OPPO A59/f1s back cover', NULL, 8, 600.00, 250.00, 350.00, '1', 4, 0.00, NULL, '0'),
(444, 'OPPO A57 back cover', NULL, 19, 550.00, 200.00, 350.00, '1', 6, 0.00, NULL, '0'),
(445, 'OPPO A53 back cover', NULL, 7, 600.00, 250.00, 350.00, '1', 3, 0.00, NULL, '0'),
(446, 'OPPO A54 back cover', NULL, 4, 550.00, 200.00, 350.00, '1', 2, 0.00, NULL, '0'),
(447, 'OPPO A54 5g back cover', NULL, 2, 650.00, 250.00, 400.00, '1', 0, 0.00, NULL, '0'),
(448, 'OPPO A9  A5 back cover', NULL, 6, 600.00, 250.00, 350.00, '1', 3, 0.00, NULL, '0'),
(449, 'OPPO F3 back cover', NULL, 5, 450.00, 200.00, 250.00, '1', 2, 0.00, NULL, '0'),
(451, 'OPPO F5 back cover', NULL, 1, 600.00, 250.00, 350.00, '1', 0, 0.00, NULL, '0'),
(452, 'OPPO F7 back cover', NULL, 2, 400.00, 200.00, 200.00, '1', 0, 0.00, NULL, '0'),
(453, 'OPPO F9 pro back cover', NULL, 6, 550.00, 250.00, 300.00, '1', 0, 0.00, NULL, '0'),
(454, 'OPPO F11 back cover', NULL, 2, 600.00, 250.00, 350.00, '1', 0, 0.00, NULL, '0'),
(455, 'OPPO F11 pro back cover', NULL, 2, 600.00, 250.00, 350.00, '1', 0, 0.00, NULL, '0'),
(456, 'OPPO F19 back cover', NULL, 3, 500.00, 200.00, 300.00, '1', 0, 0.00, NULL, '0'),
(457, 'OPPO F19 pro back cover', NULL, 1, 500.00, 200.00, 300.00, '1', 0, 0.00, NULL, '0'),
(458, 'OPPO F17  back cover', NULL, 15, 600.00, 250.00, 350.00, '1', 3, 0.00, NULL, '0'),
(459, 'OPPO A83 back cover', NULL, 1, 550.00, 250.00, 300.00, '1', 1, 0.00, NULL, '0'),
(460, 'Nokia 1 Back cover', NULL, 2, 450.00, 250.00, 200.00, '1', 1, 0.00, NULL, '0'),
(461, 'Nokia C1 Back cover', NULL, 5, 600.00, 250.00, 350.00, '1', 3, 0.00, NULL, '0'),
(462, 'Nokia C1 plus Back cover', NULL, 4, 450.00, 250.00, 200.00, '1', 2, 0.00, NULL, '0'),
(463, 'Nokia 2 Back cover', NULL, 3, 450.00, 250.00, 200.00, '1', 1, 0.00, NULL, '0'),
(464, 'Nokia 2.1 Back cover', NULL, 3, 450.00, 250.00, 200.00, '1', 1, 0.00, NULL, '0'),
(465, 'Nokia 2.2 Back cover', NULL, 3, 450.00, 250.00, 200.00, '1', 2, 0.00, NULL, '0'),
(466, 'Nokia 1.4 Back cover', NULL, 4, 450.00, 250.00, 200.00, '1', 1, 0.00, NULL, '0'),
(467, 'Nokia 5.1 Back cover', NULL, 3, 450.00, 250.00, 200.00, '1', 1, 0.00, NULL, '0'),
(468, 'Nokia 6 Back cover', NULL, 2, 450.00, 250.00, 200.00, '1', 1, 0.00, NULL, '0'),
(469, 'Nokia 5.3 Back cover', NULL, 1, 450.00, 250.00, 200.00, '1', 0, 0.00, NULL, '0'),
(470, 'Nokia C20 Back cover', NULL, 1, 650.00, 250.00, 400.00, '1', 0, 0.00, NULL, '0'),
(471, 'Nokia G10 Back cover', NULL, 1, 650.00, 250.00, 400.00, '1', 0, 0.00, NULL, '0'),
(472, 'oms T20 Air pod', NULL, 2, 4850.00, 3000.00, 1850.00, '1', 1, 0.00, NULL, '0'),
(473, 'Om405 Micro Charger ', NULL, 5, 1350.00, 650.00, 700.00, '1', 2, 0.00, NULL, '0'),
(474, 'Samsung A31 back Cover', NULL, 7, 650.00, 250.00, 400.00, '1', 3, 0.00, NULL, '0'),
(475, 'Samsung A06 back Cover', NULL, 10, 650.00, 250.00, 400.00, '1', 3, 0.00, NULL, '0'),
(476, 'Vivo y03 back cover', NULL, 2, 600.00, 250.00, 350.00, '1', 0, 0.00, NULL, '0'),
(477, 'Samsung A22 5g back Cover', NULL, 6, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(478, 'Realme C12 back cover', NULL, 5, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(479, 'Samsung J7 max back Cover', NULL, 4, 750.00, 350.00, 400.00, '1', 2, 0.00, NULL, '0'),
(480, 'Vivo Y85 back cover', NULL, 10, 750.00, 350.00, 400.00, '1', 1, 0.00, NULL, '0'),
(481, 'Vivo y1s back cover', NULL, 18, 650.00, 250.00, 400.00, '1', 3, 0.00, NULL, '0'),
(482, 'Vivo y01 back cover', NULL, 3, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(483, 'Vivo y02 back cover', NULL, 2, 650.00, 250.00, 400.00, '1', 0, 0.00, NULL, '0'),
(484, 'Vivo y20/y12s back cover', NULL, 6, 650.00, 250.00, 400.00, '1', 2, 0.00, NULL, '0'),
(485, 'Vivo y53 back cover', NULL, 3, 650.00, 250.00, 400.00, '1', 0, 0.00, NULL, '0'),
(486, 'Vivo Y95/93/91 back cover', NULL, 4, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(487, 'Vivo Y17/12/11/15 back cover', NULL, 12, 600.00, 250.00, 350.00, '1', 2, 0.00, NULL, '0'),
(488, 'Vivo Y67 back cover', NULL, 3, 600.00, 250.00, 350.00, '1', 1, 0.00, NULL, '0'),
(489, 'Vivo y17S back cover', NULL, 4, 650.00, 250.00, 400.00, '1', 1, 0.00, NULL, '0'),
(490, 'Vivo Y66 back cover', NULL, 7, 550.00, 200.00, 350.00, '1', 1, 0.00, NULL, '0'),
(491, 'Vivo y81/83 back cover', NULL, 16, 600.00, 250.00, 350.00, '1', 5, 0.00, NULL, '0'),
(493, '8600 Charger', NULL, 30, 350.00, 200.00, 150.00, '1', 5, 0.00, NULL, '0'),
(494, 'MTB Tempered', NULL, 192, 500.00, 130.00, 270.00, '1', 1, 100.00, NULL, '0'),
(495, 'Super D tempered Glass ', NULL, 98, 800.00, 150.00, 550.00, '1', 1, 100.00, NULL, '0'),
(496, '5c normal Battery', NULL, 19, 400.00, 200.00, 200.00, '1', 5, 0.00, NULL, '0'),
(497, 'om208 12W micro car charger', NULL, 5, 1350.00, 590.00, 760.00, '1', 1, 0.00, NULL, '0'),
(498, 'om208 12W type C car charger', NULL, 5, 1350.00, 590.00, 760.00, '1', 2, 0.00, NULL, '0'),
(499, 'om750 micro cable ', NULL, 30, 700.00, 330.00, 370.00, '1', 2, 0.00, NULL, '0'),
(500, '4G dongle ', NULL, 0, 4200.00, 3500.00, 700.00, '0', 1, 0.00, NULL, '0'),
(501, 'osy05 Dock only', NULL, 5, 1250.00, 650.00, 600.00, '1', 1, 0.00, NULL, '0');

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
(1, 'Display Replacement', '2024-09-30 20:59:32'),
(2, 'Tempered Glass', '2024-10-03 04:34:48'),
(3, 'Power Key Repair', '2024-10-13 04:34:50'),
(4, 'No Power', '2024-10-13 04:37:45'),
(5, 'Charging port replacement', '2024-10-15 04:39:53'),
(6, 'battery pin Replacement', '2024-10-18 04:47:10'),
(7, 'speaker repair', '2024-10-18 08:28:59'),
(8, 'Software frp', '2024-10-18 12:45:03'),
(9, 'Camera replacement', '2024-10-19 09:04:10');

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
(2, 1, 'iPhone 12 Display Replace', 5000.00, 10.00, 114990.00, 120000.00, '2024-10-01 18:44:17'),
(3, 2, 'Clear Tempered Glass ', 100.00, 150.00, 450.00, 600.00, '2024-10-03 04:37:49'),
(4, 1, 'm02 Display', 800.00, 3000.00, 700.00, 4500.00, '2024-10-03 04:45:47'),
(5, 1, 'm02 Original Display Replace', 1500.00, 3500.00, 1500.00, 6500.00, '2024-10-06 09:50:40'),
(6, 1, 'Samsung M02 normal display Replacement', 1000.00, 2500.00, 1000.00, 4500.00, '2024-10-06 09:51:08'),
(7, 1, 'Samsung A32 4G Display Replacement', 1500.00, 7000.00, 3000.00, 11500.00, '2024-10-06 09:52:48'),
(8, 1, 'Samsung M02s Normal Display Replasment', 0.00, 0.00, 4500.00, 4500.00, '2024-10-06 10:01:46'),
(9, 1, 'Samsung M02s org Display Replacment', 0.00, 3500.00, 3000.00, 6500.00, '2024-10-06 10:05:02'),
(10, 1, 'Samsung A04 Display Replacement', 0.00, 0.00, 5500.00, 5500.00, '2024-10-06 10:08:44'),
(11, 1, 'Samsung A04E Display Replacement', 0.00, 0.00, 60000.00, 60000.00, '2024-10-06 10:11:24'),
(12, 1, 'Samsung A20e org normal display Replacement', 0.00, 0.00, 6500.00, 6500.00, '2024-10-06 10:13:48'),
(13, 1, 'Samsung A2 Core Display Replacement', 0.00, 2600.00, 2400.00, 5000.00, '2024-10-06 10:15:49'),
(14, 1, 'Samsung J6 Plus ', 0.00, 3500.00, 2000.00, 5500.00, '2024-10-06 10:17:29'),
(15, 1, 'Samsung J6 Plus Replacement', 0.00, 3500.00, 2000.00, 5500.00, '2024-10-06 10:18:05'),
(20, 1, 'Samsung J6 Plus Display Replacement', 0.00, 0.00, 5500.00, 5500.00, '2024-10-06 10:20:52'),
(21, 1, 'Samsung A20 Oled Display Replacement', 0.00, 0.00, 9000.00, 9000.00, '2024-10-06 10:26:36'),
(22, 1, 'Samsung A13 Display Replacement', 0.00, 3500.00, 3000.00, 6500.00, '2024-10-06 10:38:56'),
(23, 1, 'Samsung j5 Prime Display Replacement', 0.00, 2500.00, 2000.00, 4500.00, '2024-10-06 10:41:46'),
(24, 1, 'Samsung J8 Display Replacement', 0.00, 2800.00, 2200.00, 5000.00, '2024-10-06 10:44:47'),
(25, 1, 'Samsung A11org Display Replacement', 0.00, 0.00, 6500.00, 6500.00, '2024-10-06 10:47:10'),
(26, 1, 'Samsung A05 Display Replcement', 0.00, 0.00, 6000.00, 6000.00, '2024-10-06 10:50:34'),
(27, 1, 'Samsung A21s Display Replacement', 0.00, 4000.00, 2500.00, 6500.00, '2024-10-06 10:52:35'),
(28, 1, 'Samsung A30 oled Display Replacement', 0.00, 6500.00, 2500.00, 9000.00, '2024-10-06 10:54:37'),
(29, 1, 'Samsung M01 core Display Replacement', 0.00, 2500.00, 2000.00, 4500.00, '2024-10-06 10:57:11'),
(30, 1, 'Samsung J4 old display Replaycement', 0.00, 5500.00, 2000.00, 7500.00, '2024-10-06 10:59:21'),
(31, 1, 'Samsung A03 core Display Replacement', 0.00, 3200.00, 2300.00, 5500.00, '2024-10-06 11:01:37'),
(32, 1, 'Samsung J7 prime Display Replacement ', 0.00, 3500.00, 2000.00, 5500.00, '2024-10-06 11:04:36'),
(33, 1, 'Samsung A20s normal display Replacement', 0.00, 3500.00, 2500.00, 6000.00, '2024-10-06 11:10:37'),
(34, 1, 'Samsung M20 display Replacement', 0.00, 3500.00, 2500.00, 6000.00, '2024-10-06 11:26:26'),
(35, 1, 'Samsung M21 oled display Replacement', 0.00, 6400.00, 3100.00, 9500.00, '2024-10-06 11:29:29'),
(36, 1, 'Samsung M12 normal display Replacement', 0.00, 0.00, 6500.00, 6500.00, '2024-10-06 11:34:23'),
(37, 1, 'Samsung A10s normal Display Replacement', 0.00, 2400.00, 2100.00, 4500.00, '2024-10-06 11:42:08'),
(38, 1, 'Samsung A10s  org display Replacement', 0.00, 3200.00, 3300.00, 6500.00, '2024-10-06 11:44:48'),
(40, 1, 'Samsung A10 normal Display Replacement', 0.00, 0.00, 4600.00, 4600.00, '2024-10-06 12:09:50'),
(41, 1, 'Samsung A10 org display Replacement', 0.00, 3200.00, 2800.00, 6000.00, '2024-10-06 12:11:37'),
(42, 1, 'Y9 prime 19 Display Replacement', 1000.00, 4200.00, 1300.00, 6500.00, '2024-10-07 05:11:16'),
(43, 1, 'Y9 2019 Display Replacement', 1000.00, 2.00, 5498.00, 6500.00, '2024-10-07 05:12:23'),
(44, 1, 'Nova 2i Display Replacement', 800.00, 3500.00, 1200.00, 5500.00, '2024-10-07 05:18:18'),
(45, 1, 'Nova 3i Display Replacement', 1000.00, 3500.00, 1000.00, 5500.00, '2024-10-07 05:19:14'),
(46, 1, 'Y7 19 Display Replacement', 800.00, 3200.00, 1500.00, 5500.00, '2024-10-07 05:21:10'),
(47, 1, 'y718 Display Replacement', 800.00, 3000.00, 1700.00, 5500.00, '2024-10-07 05:22:08'),
(48, 1, 'Y6p Display Replacement', 800.00, 3000.00, 2500.00, 5500.00, '2024-10-07 05:24:11'),
(49, 1, 'Y7p Display Replacement', 800.00, 3200.00, 2000.00, 6000.00, '2024-10-07 05:25:19'),
(50, 1, 'Y6 2019 Display Replacement', 800.00, 2800.00, 1900.00, 5500.00, '2024-10-07 05:27:17'),
(51, 1, 'Y6 2018 Display replacement', 800.00, 2800.00, 1900.00, 5500.00, '2024-10-07 05:28:12'),
(52, 1, 'Y5 2019 Display Replacement', 600.00, 2800.00, 1600.00, 5000.00, '2024-10-07 05:29:28'),
(53, 1, 'Y7a Display Replacement', 1000.00, 3800.00, 1200.00, 6000.00, '2024-10-07 05:33:48'),
(54, 1, 'Y9 2018 Display Replacement', 800.00, 3500.00, 2000.00, 5500.00, '2024-10-07 05:34:57'),
(55, 1, 'Y5 2018 Display Replacement', 700.00, 2500.00, 1300.00, 4500.00, '2024-10-07 05:36:10'),
(56, 1, 'Y5P Display Replacement', 800.00, 3300.00, 900.00, 5000.00, '2024-10-07 05:37:41'),
(57, 1, 'GR5 17 Display replacement', 1000.00, 3500.00, 2000.00, 5500.00, '2024-10-07 05:38:37'),
(58, 1, 'Y6 pro Display	Replacement ', 1000.00, 2500.00, 1500.00, 5000.00, '2024-10-07 05:39:50'),
(59, 1, 'Gr3 Display Replacement', 800.00, 2500.00, 1200.00, 4500.00, '2024-10-07 05:40:43'),
(60, 1, 'iPhone 7 plus black Display replacement', 1000.00, 3500.00, 2000.00, 6500.00, '2024-10-07 06:46:26'),
(61, 1, 'iPhone 6 plus white Display replacement', 1000.00, 3200.00, 1800.00, 6000.00, '2024-10-07 06:50:47'),
(62, 1, 'iPhone 6s plus white Display replacement', 1000.00, 3500.00, 3000.00, 6500.00, '2024-10-07 06:51:48'),
(63, 1, 'iPhone 7G black Display	replacement', 1000.00, 3200.00, 2300.00, 6500.00, '2024-10-07 06:54:09'),
(64, 1, 'iPhone 6s Black Display	replacement', 1000.00, 3200.00, 1800.00, 6000.00, '2024-10-07 07:10:09'),
(65, 1, 'iPhone 6s white Display replasement	', 1000.00, 3000.00, 2000.00, 6000.00, '2024-10-07 07:11:19'),
(66, 1, 'iPhone 6s white Display replacement ', 1000.00, 3000.00, 2000.00, 6000.00, '2024-10-07 07:11:36'),
(67, 1, 'iPhone 6g white Display replacement ', 1000.00, 3000.00, 2000.00, 6000.00, '2024-10-07 07:12:54'),
(68, 1, 'iPhone 7g white Display replacement ', 1000.00, 3000.00, 2000.00, 6000.00, '2024-10-07 07:13:42'),
(69, 1, 'iPhone 6 black Display replacement ', 1000.00, 3000.00, 2000.00, 6000.00, '2024-10-07 07:15:01'),
(70, 1, 'iPhone 6 plus black Display replacement ', 1000.00, 3000.00, 2000.00, 6000.00, '2024-10-07 07:21:39'),
(71, 1, 'iPhone X Display replacement', 1000.00, 7000.00, 4500.00, 12500.00, '2024-10-07 07:23:09'),
(72, 1, 'iPhone Xs Display replacement', 1000.00, 7000.00, 5500.00, 12500.00, '2024-10-07 07:24:09'),
(73, 1, 'Realme C15 Display Replaacement', 800.00, 2800.00, 1900.00, 5500.00, '2024-10-07 10:59:55'),
(74, 1, 'oppo A3s Display replacement', 800.00, 2600.00, 2100.00, 5500.00, '2024-10-07 11:08:50'),
(75, 1, 'oppo A83 Display replacement', 800.00, 2500.00, 3000.00, 5500.00, '2024-10-07 12:10:46'),
(76, 1, 'oppo A1k Display replacement', 800.00, 2500.00, 2000.00, 4500.00, '2024-10-07 12:12:16'),
(77, 1, 'oppo A57 Display replacement', 800.00, 2500.00, 1700.00, 5000.00, '2024-10-07 12:14:11'),
(78, 1, 'oppo A5S Display replacement', 800.00, 2500.00, 1700.00, 5000.00, '2024-10-07 12:17:33'),
(79, 1, 'oppo A53 Display replacement', 800.00, 2500.00, 3000.00, 5500.00, '2024-10-07 12:19:26'),
(80, 1, 'oppo A15 Display replacement	', 800.00, 2500.00, 3000.00, 5500.00, '2024-10-07 12:27:27'),
(81, 1, 'oppo A71 Display replacement	', 800.00, 2800.00, 1900.00, 5500.00, '2024-10-07 12:31:53'),
(82, 1, 'oppo A16 Display replacement', 800.00, 2800.00, 1900.00, 5500.00, '2024-10-07 12:33:14'),
(83, 1, 'realme C11 dual Display	replacement', 800.00, 2500.00, 1700.00, 5000.00, '2024-10-08 11:09:30'),
(84, 1, 'Realme C21 Display Replacement', 800.00, 2800.00, 1400.00, 5000.00, '2024-10-08 11:10:31'),
(85, 1, 'Redmi Note 9 Display Replacement', 800.00, 3000.00, 1700.00, 5500.00, '2024-10-08 11:15:35'),
(86, 1, 'Redmi Note 8 Display Replacement', 800.00, 2800.00, 1900.00, 5500.00, '2024-10-08 11:16:25'),
(87, 1, 'Redmi Note 8 Display Replacement', 800.00, 2800.00, 1900.00, 5500.00, '2024-10-08 11:16:25'),
(88, 1, 'Redmi Note 7 Display Replacement', 800.00, 2800.00, 1900.00, 5500.00, '2024-10-08 11:18:21'),
(89, 1, 'Redmi 9 Display Replacement', 800.00, 0.00, 4700.00, 5500.00, '2024-10-08 11:34:48'),
(90, 1, 'Redmi 9A Display Replacement', 800.00, 2600.00, 1600.00, 5000.00, '2024-10-08 11:36:31'),
(91, 1, 'Redmi 8A Display Replacement', 800.00, 2800.00, 1400.00, 5000.00, '2024-10-08 11:37:18'),
(92, 1, 'Redmi 10A Display Replacement', 800.00, 2800.00, 1900.00, 5500.00, '2024-10-08 11:38:05'),
(93, 1, 'Redmi 10 Display Replacement', 800.00, 2800.00, 1900.00, 5500.00, '2024-10-08 11:39:01'),
(94, 1, 'Samsung A04s Display Replacement', 800.00, 3000.00, 2200.00, 6000.00, '2024-10-08 12:50:14'),
(95, 1, 'Vivo y65 Display Replacement', 800.00, 2500.00, 2200.00, 5500.00, '2024-10-08 12:55:12'),
(96, 1, 'Nokia C2 Display Replacement', 800.00, 2700.00, 1500.00, 5000.00, '2024-10-08 12:59:53'),
(97, 1, 'Nokia 1 touch Replacement', 600.00, 500.00, 900.00, 2000.00, '2024-10-08 13:01:02'),
(98, 1, 'Nokia C01 plus Display Replacement', 800.00, 2800.00, 1400.00, 5000.00, '2024-10-08 13:04:04'),
(99, 1, 'Nokia 1.4 Display Replacement', 800.00, 3000.00, 1700.00, 5500.00, '2024-10-08 13:05:11'),
(100, 1, 'Nokia C1 Display Replacement', 600.00, 2400.00, 1500.00, 4500.00, '2024-10-08 13:06:26'),
(101, 1, 'Nokia 2.2 Display Replacement', 800.00, 2500.00, 1700.00, 5000.00, '2024-10-08 13:07:06'),
(102, 1, 'Nokia G10 Display Replacement', 800.00, 3200.00, 1500.00, 5500.00, '2024-10-08 13:07:46'),
(103, 3, 'redmi note 9', 500.00, 0.00, 1000.00, 1500.00, '2024-10-13 04:35:47'),
(104, 3, 'Power  Key Repair', 400.00, 500.00, 300.00, 1200.00, '2024-10-14 13:42:20'),
(105, 5, 'charging Port Replacement', 400.00, 200.00, 200.00, 800.00, '2024-10-15 04:41:20'),
(106, 4, 'battery Short repair ', 400.00, 0.00, 600.00, 1000.00, '2024-10-15 04:44:20'),
(107, 6, 'battery pin Replacement', 200.00, 150.00, 250.00, 600.00, '2024-10-18 04:48:52'),
(108, 2, 'matte Tempered glass', 100.00, 200.00, 600.00, 800.00, '2024-10-18 06:19:42'),
(109, 8, 'software Frp', 200.00, 1.00, 799.00, 1000.00, '2024-10-18 12:46:06'),
(110, 1, 'y5 18 battery replacement', 400.00, 1950.00, 850.00, 3200.00, '2024-10-19 03:58:28'),
(111, 1, '20pin Display replacement', 200.00, 0.00, 600.00, 800.00, '2024-10-19 05:40:31'),
(112, 1, 'Camera replacement', 500.00, 1000.00, 1500.00, 3000.00, '2024-10-19 09:05:00');

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
(1, 1, 'm02 Original Display Replace', 2500.00, 800.00, 4500.00, 'ifix', '2024-10-17 10:55:55'),
(2, 1, 'Clear Tempered Glass ', 150.00, 100.00, 500.00, 'ifix', '2024-10-17 10:55:55'),
(3, 2, 'Clear Tempered Glass ', 150.00, 100.00, 500.00, 'Kasun', '2024-10-18 09:46:26'),
(4, 4, 'Clear Tempered Glass ', 150.00, 100.00, 400.00, 'ifix', '2024-10-18 11:07:43'),
(5, 5, 'charging Port Replacement', 200.00, 400.00, 1000.00, 'Kasun', '2024-10-18 11:44:56'),
(6, 8, 'Power  Key Repair', 500.00, 400.00, 1300.00, 'ifix', '2024-10-18 13:02:30'),
(7, 8, 'Clear Tempered Glass ', 150.00, 100.00, 500.00, 'ifix', '2024-10-18 13:02:30'),
(8, 13, 'battery Short repair ', 0.00, 400.00, 1500.00, 'Udaya', '2024-10-18 14:26:18'),
(9, 15, 'Power  Key Repair', 500.00, 400.00, 2200.00, 'ifix', '2024-10-18 15:46:07'),
(10, 17, 'Clear Tempered Glass ', 150.00, 100.00, 500.00, 'lakmal', '2024-10-18 16:16:04'),
(11, 19, 'Samsung M01 core Display Replacement', 2500.00, 0.00, 4500.00, 'ifix', '2024-10-18 16:43:40'),
(12, 20, 'Samsung M01 core Display Replacement', 2500.00, 0.00, 4500.00, 'ifix', '2024-10-18 16:44:22'),
(13, 21, 'charging Port Replacement', 100.00, 100.00, 400.00, 'Kasun', '2024-10-18 17:19:14'),
(14, 23, 'Y5 2019 Display Replacement', 2800.00, 600.00, 4500.00, 'Udaya', '2024-10-18 18:11:10'),
(15, 25, 'software Frp', 1.00, 200.00, 1000.00, 'ifix', '2024-10-18 18:18:47'),
(16, 26, 'y5 18 battery replacement', 1950.00, 400.00, 3200.00, 'ifix', '2024-10-19 09:37:42'),
(17, 26, 'Clear Tempered Glass ', 150.00, 100.00, 400.00, 'ifix', '2024-10-19 09:37:42'),
(18, 27, 'software Frp', 1.00, 200.00, 400.00, 'ifix', '2024-10-19 09:39:14'),
(19, 28, 'Clear Tempered Glass ', 150.00, 100.00, 800.00, 'Kasun', '2024-10-19 10:26:35'),
(20, 31, 'Samsung A10 normal Display Replacement', 0.00, 0.00, 4500.00, 'ifix', '2024-10-19 11:37:20'),
(21, 31, 'Clear Tempered Glass ', 150.00, 100.00, 500.00, 'ifix', '2024-10-19 11:37:20'),
(22, 33, 'Samsung M21 oled display Replacement', 6400.00, 800.00, 8500.00, 'Udaya', '2024-10-19 12:50:22'),
(23, 33, 'Clear Tempered Glass ', 150.00, 100.00, 500.00, 'ifix', '2024-10-19 12:50:22'),
(24, 38, 'software Frp', 1.00, 200.00, 500.00, 'ifix', '2024-10-19 13:19:55'),
(25, 39, 'charging Port Replacement', 200.00, 400.00, 500.00, 'ifix', '2024-10-19 13:34:56'),
(26, 41, 'charging Port Replacement', 200.00, 400.00, 500.00, 'Kasun', '2024-10-19 14:21:52'),
(27, 42, 'software Frp', 1.00, 200.00, 800.00, 'ifix', '2024-10-19 14:32:39'),
(28, 43, 'Camera replacement', 1000.00, 500.00, 3000.00, 'Udaya', '2024-10-19 14:36:00'),
(29, 50, 'Camera replacement', 1000.00, 500.00, 2400.00, 'ifix', '2024-10-19 18:54:01'),
(30, 51, 'Power  Key Repair', 200.00, 400.00, 1500.00, 'ifix', '2024-10-20 10:50:57'),
(31, 53, 'Samsung M01 core Display Replacement', 2500.00, 0.00, 4500.00, 'ifix', '2024-10-20 11:20:37'),
(32, 61, 'Samsung A10s normal Display Replacement', 2400.00, 0.00, 5500.00, 'ifix', '2024-10-23 11:25:57'),
(33, 62, 'Redmi 9A Display Replacement', 2600.00, 800.00, 5000.00, 'ifix', '2024-10-24 19:31:52'),
(34, 62, 'Clear Tempered Glass ', 150.00, 100.00, 500.00, 'ifix', '2024-10-24 19:31:52'),
(35, 64, 'Vivo y65 Display Replacement', 2500.00, 800.00, 5500.00, 'ifix', '2024-10-25 10:36:40'),
(36, 68, 'oppo A5S Display replacement', 2500.00, 800.00, 4500.00, 'ifix', '2024-10-25 11:13:38'),
(37, 68, 'Clear Tempered Glass ', 150.00, 100.00, 800.00, 'ifix', '2024-10-25 11:13:38'),
(38, 69, 'charging Port Replacement', 200.00, 0.00, 500.00, 'ifix', '2024-10-25 11:25:14'),
(39, 70, 'software Frp', 1.00, 200.00, 400.00, 'ifix', '2024-10-25 11:33:36'),
(40, 71, 'software Frp', 1.00, 200.00, 1500.00, 'ifix', '2024-10-25 12:22:52'),
(41, 73, 'Y5 2018 Display Replacement', 2500.00, 700.00, 4500.00, 'ifix', '2024-10-25 13:05:45'),
(42, 73, 'Clear Tempered Glass ', 150.00, 100.00, 500.00, 'ifix', '2024-10-25 13:05:45'),
(43, 75, 'charging Port Replacement', 200.00, 0.00, 850.00, 'ifix', '2024-10-26 10:44:13');

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
(7, 'Clear Tmpered glass', 487, 150.00, '2024-10-03 04:35:53'),
(12, 'Samsung M02 normal display', 3, 2500.00, '2024-10-06 09:33:42'),
(13, 'M02 Org Display', 1, 3500.00, '2024-10-06 09:45:46'),
(14, 'Samsung A32 4G Display', 1, 7000.00, '2024-10-06 09:52:14'),
(15, 'Samsung M02s normal display', 2, 2500.00, '2024-10-06 10:00:30'),
(16, 'Samsung M02s org display', 2, 3500.00, '2024-10-06 10:02:29'),
(17, 'Samsung A04 display', 1, 3500.00, '2024-10-06 10:07:49'),
(18, 'Samsung A04E Display ', 1, 3500.00, '2024-10-06 10:10:53'),
(19, 'Samsung A20e org normal display', 1, 4200.00, '2024-10-06 10:12:43'),
(20, 'Samsung A2 core Org Display', 4, 2600.00, '2024-10-06 10:14:53'),
(21, 'Samsung J6 Plus  Display', 1, 3500.00, '2024-10-06 10:16:44'),
(22, 'Samsung A20 Oled display', 1, 6500.00, '2024-10-06 10:25:39'),
(23, 'Samsung A13 Display', 1, 3500.00, '2024-10-06 10:38:15'),
(24, 'Samsung J5 prime Display', 1, 2500.00, '2024-10-06 10:40:44'),
(25, 'Samsung J8 display', 1, 2800.00, '2024-10-06 10:43:53'),
(26, 'Samsung A11 org display', 1, 4500.00, '2024-10-06 10:45:54'),
(27, 'Samsung A05 Display', 2, 4000.00, '2024-10-06 10:49:16'),
(28, 'Samsung A21s Display', 1, 4000.00, '2024-10-06 10:51:56'),
(29, 'Samsung A30 oled Display', 1, 6500.00, '2024-10-06 10:53:58'),
(30, 'Samsung M01 core Display', -1, 2500.00, '2024-10-06 10:56:30'),
(31, 'Samsung J4 old display', 1, 5500.00, '2024-10-06 10:58:54'),
(32, 'Samsung A03 core Display', 0, 3200.00, '2024-10-06 11:00:56'),
(33, 'Samsung J7 prime display', 2, 3500.00, '2024-10-06 11:03:33'),
(34, 'Samsung A20s normal display', 3, 3500.00, '2024-10-06 11:06:52'),
(35, 'Samsung M20 display', 1, 3500.00, '2024-10-06 11:25:58'),
(36, 'Samsung M21 oled display', 0, 6400.00, '2024-10-06 11:29:07'),
(37, 'Samsung M12 normal display', 2, 3800.00, '2024-10-06 11:33:56'),
(38, 'Samsung A10s normal Display', 2, 2400.00, '2024-10-06 11:41:09'),
(39, 'Samsung A10s  org display', 1, 3200.00, '2024-10-06 11:42:49'),
(40, 'Samsung A10 normal display', 3, 2600.00, '2024-10-06 12:08:10'),
(41, 'Samsung A10 org display', 2, 3200.00, '2024-10-06 12:11:13'),
(42, 'Y9 prime 19 Display', 2, 4200.00, '2024-10-07 05:10:37'),
(43, 'Y9 2019 Display', 2, 3800.00, '2024-10-07 05:11:56'),
(44, 'Nova 2i Display', 2, 3500.00, '2024-10-07 05:17:37'),
(45, 'Nova 3i Display	', 2, 3500.00, '2024-10-07 05:18:42'),
(46, 'Y7 19 Display', 1, 3200.00, '2024-10-07 05:20:44'),
(47, 'y718 Display', 1, 3000.00, '2024-10-07 05:21:45'),
(48, 'Y6p Display', 1, 3000.00, '2024-10-07 05:23:45'),
(49, 'Y7p Display', 1, 3200.00, '2024-10-07 05:24:48'),
(50, 'Y6 2019 Display', 1, 2800.00, '2024-10-07 05:26:49'),
(51, 'Y6 2018 Display', 1, 2800.00, '2024-10-07 05:27:51'),
(52, 'Y5 2019 Display', 2, 2800.00, '2024-10-07 05:29:02'),
(53, 'Y7a Display', 1, 3800.00, '2024-10-07 05:31:57'),
(54, 'Y9 2018 Display', 1, 3500.00, '2024-10-07 05:34:22'),
(55, 'Y5 2018 Display', 1, 2500.00, '2024-10-07 05:35:40'),
(56, 'Y5P Display', 1, 3300.00, '2024-10-07 05:37:10'),
(57, 'GR5 17 Display', 1, 3500.00, '2024-10-07 05:38:15'),
(58, 'Y6 pro Display ', 1, 2500.00, '2024-10-07 05:39:19'),
(59, 'Gr3 Display', 1, 2500.00, '2024-10-07 05:40:15'),
(60, 'iPhone 7 plus black Display', 2, 3500.00, '2024-10-07 06:44:13'),
(61, 'iPhone 6 plus white Display', 1, 3200.00, '2024-10-07 06:50:25'),
(62, 'iPhone 6s plus white Display', 1, 3500.00, '2024-10-07 06:51:21'),
(63, 'iPhone 7G black Display', 1, 3200.00, '2024-10-07 06:53:43'),
(64, 'iPhone 6s Black Display ', 1, 3200.00, '2024-10-07 07:03:11'),
(65, 'iPhone 6s white Display	', 1, 3000.00, '2024-10-07 07:10:47'),
(66, 'iPhone 6 white Display', 1, 3000.00, '2024-10-07 07:12:33'),
(67, 'iPhone 7 white Display', 1, 3000.00, '2024-10-07 07:13:21'),
(68, 'iPhone 6s black Display', 1, 3000.00, '2024-10-07 07:14:15'),
(69, 'iPhone 6 plus black Display replacement ', 1, 3000.00, '2024-10-07 07:21:21'),
(70, 'iPhone X Display', 1, 7000.00, '2024-10-07 07:22:30'),
(71, 'iPhone Xs Display', 1, 7000.00, '2024-10-07 07:23:59'),
(72, 'Realme C15 Display', 1, 2800.00, '2024-10-07 10:59:06'),
(73, 'oppo A3s Display', 1, 2600.00, '2024-10-07 11:08:20'),
(74, 'oppo A83 Display', 2, 2500.00, '2024-10-07 12:10:06'),
(75, 'oppo A1k Display', 2, 2500.00, '2024-10-07 12:11:36'),
(76, 'oppo A57 Display', 2, 2500.00, '2024-10-07 12:13:40'),
(77, 'oppo A5S Display	', 4, 2500.00, '2024-10-07 12:14:53'),
(78, 'oppo A53 Display', 2, 2500.00, '2024-10-07 12:19:01'),
(79, 'oppo A15 Display replacement', 2, 2500.00, '2024-10-07 12:26:56'),
(80, 'oppo A71 Display', 1, 2800.00, '2024-10-07 12:27:51'),
(81, 'oppo A16 Display', 1, 2800.00, '2024-10-07 12:33:08'),
(82, 'realme C11 dual Display', 2, 2500.00, '2024-10-08 11:09:07'),
(83, 'Realme C21 Display', 1, 2800.00, '2024-10-08 11:10:18'),
(84, 'Redmi Note 9 Display', 2, 3000.00, '2024-10-08 11:15:20'),
(85, 'Redmi Note 8 Display', 1, 2800.00, '2024-10-08 11:16:06'),
(86, 'Redmi Note 7 Display', 1, 2800.00, '2024-10-08 11:18:05'),
(87, 'Redmi 9 Display', 2, 2800.00, '2024-10-08 11:20:47'),
(88, 'Redmi 9A Display Replacement', 2, 2600.00, '2024-10-08 11:36:19'),
(89, 'Redmi 8A Display Replacement', 2, 2800.00, '2024-10-08 11:37:01'),
(90, 'Redmi 10A Display', 1, 2800.00, '2024-10-08 11:37:47'),
(91, 'Redmi 10 Display', 1, 2800.00, '2024-10-08 11:38:42'),
(92, 'Samsung A04s Display', 1, 3000.00, '2024-10-08 12:49:16'),
(93, 'Vivo y65 Display Replacement', 0, 2500.00, '2024-10-08 12:54:39'),
(94, 'Nokia C2 Display', 2, 2700.00, '2024-10-08 12:59:39'),
(95, 'nokia 1 touch ', 3, 500.00, '2024-10-08 13:00:33'),
(96, 'Nokia C01 plus Display', 1, 2800.00, '2024-10-08 13:02:19'),
(97, 'Nokia 1.4 Display', 1, 3000.00, '2024-10-08 13:05:01'),
(98, 'Nokia C1 Display', 1, 2400.00, '2024-10-08 13:06:16'),
(99, 'Nokia 2.2 Display', 1, 2500.00, '2024-10-08 13:06:53'),
(100, 'Nokia G10 Display', 1, 3200.00, '2024-10-08 13:07:35'),
(101, 'Out key', 397, 200.00, '2024-10-14 13:41:29'),
(102, 'Power ribbon', 297, 300.00, '2024-10-14 13:41:58'),
(103, 'charging port', 493, 200.00, '2024-10-15 04:41:12'),
(104, 'Battery pin', 50, 150.00, '2024-10-18 04:48:48'),
(105, 'matte Tempered Glass ', 50, 200.00, '2024-10-18 06:19:05'),
(106, 'Software', 94, 1.00, '2024-10-18 12:45:35'),
(107, 'y5 18 battery ', 0, 1950.00, '2024-10-19 03:58:03'),
(108, '20pin Display', 40, 400.00, '2024-10-19 05:38:06'),
(109, 'camera', 18, 1000.00, '2024-10-19 09:04:32');

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
(9, 'Clear Tempered Glass ', 'Clear Tmpered glass', 1),
(11, 'm02 Original Display Replace', 'M02 Org Display', 1),
(12, 'Samsung M02 normal display Replacement', 'Samsung M02 normal display', 1),
(13, 'Samsung A32 4G Display Replacement', 'Samsung A32 4G Display', 1),
(14, 'Samsung M02s org Display Replacment', 'Samsung M02s org display', 1),
(15, 'Samsung A2 Core Display Replacement', 'Samsung A2 core Org Display', 1),
(22, 'Samsung A13 Display Replacement', 'Samsung A13 Display', 1),
(23, 'Samsung j5 Prime Display Replacement', 'Samsung J5 prime Display', 1),
(24, 'Samsung J8 Display Replacement', 'Samsung J8 display', 1),
(25, 'Samsung A21s Display Replacement', 'Samsung A21s Display', 1),
(26, 'Samsung A30 oled Display Replacement', 'Samsung A30 oled Display', 1),
(27, 'Samsung M01 core Display Replacement', 'Samsung M01 core Display', 1),
(28, 'Samsung J4 old display Replaycement', 'Samsung J4 old display', 1),
(29, 'Samsung A03 core Display Replacement', 'Samsung A03 core Display', 1),
(30, 'Samsung J7 prime Display Replacement ', 'Samsung J7 prime display', 1),
(31, 'Samsung A20s normal display Replacement', 'Samsung A20s normal display', 1),
(32, 'Samsung M20 display Replacement', 'Samsung M20 display', 1),
(33, 'Samsung M21 oled display Replacement', 'Samsung M21 oled display', 1),
(34, 'Samsung A10s normal Display Replacement', 'Samsung A10s normal Display', 1),
(37, 'Samsung A10 org display Replacement', 'Samsung A10 org display', 1),
(38, 'Y9 prime 19 Display Replacement', 'Y9 prime 19 Display', 1),
(40, 'Nova 2i Display Replacement', 'Nova 2i Display', 1),
(42, 'Y7 19 Display Replacement', 'Y7 19 Display', 1),
(43, 'y718 Display Replacement', 'y718 Display', 1),
(44, 'Y6p Display Replacement', 'Y6p Display', 1),
(45, 'Y7p Display Replacement', 'Y7p Display', 1),
(46, 'Y6 2019 Display Replacement', 'Y6 2019 Display', 1),
(47, 'Y6 2018 Display replacement', 'Y6 2018 Display', 1),
(48, 'Y5 2019 Display Replacement', 'Y5 2019 Display', 1),
(49, 'Y7a Display Replacement', 'Y7a Display', 1),
(50, 'Y9 2018 Display Replacement', 'Y9 2018 Display', 1),
(51, 'Y5 2018 Display Replacement', 'Y5 2018 Display', 1),
(52, 'Y5P Display Replacement', 'Y5P Display', 1),
(53, 'GR5 17 Display replacement', 'GR5 17 Display', 1),
(54, 'Y6 pro Display	Replacement ', 'Y6 pro Display', 1),
(55, 'Gr3 Display Replacement', 'Gr3 Display', 1),
(56, 'iPhone 7 plus black Display replacement', 'iPhone 7 plus black Display', 1),
(57, 'iPhone 6 plus white Display replacement', 'iPhone 6 plus white Display', 1),
(58, 'iPhone 6s plus white Display replacement', 'iPhone 6s plus white Display', 1),
(60, 'iPhone 6s Black Display	replacement', 'iPhone 6s Black Display', 1),
(63, 'iPhone 6g white Display replacement ', 'iPhone 6 white Display', 1),
(64, 'iPhone 7g white Display replacement ', 'iPhone 7 white Display', 1),
(65, 'iPhone 6 black Display replacement ', 'iPhone 6s black Display', 1),
(66, 'iPhone 6 plus black Display replacement ', 'iPhone 6 plus black Display replacement', 1),
(67, 'iPhone X Display replacement', 'iPhone X Display', 1),
(68, 'iPhone Xs Display replacement', 'iPhone Xs Display', 1),
(69, 'Realme C15 Display Replaacement', 'Realme C15 Display', 1),
(70, 'oppo A3s Display replacement', 'oppo A3s Display', 1),
(71, 'oppo A83 Display replacement', 'oppo A83 Display', 1),
(72, 'oppo A1k Display replacement', 'oppo A1k Display', 1),
(73, 'oppo A57 Display replacement', 'oppo A57 Display', 1),
(75, 'oppo A53 Display replacement', 'oppo A53 Display', 1),
(76, 'oppo A15 Display replacement	', 'oppo A15 Display replacement', 1),
(77, 'oppo A71 Display replacement	', 'oppo A71 Display', 1),
(78, 'oppo A16 Display replacement', 'oppo A16 Display', 1),
(79, 'realme C11 dual Display	replacement', 'realme C11 dual Display', 1),
(80, 'Realme C21 Display Replacement', 'Realme C21 Display', 1),
(81, 'Redmi Note 9 Display Replacement', 'Redmi Note 9 Display', 1),
(82, 'Redmi Note 8 Display Replacement', 'Redmi Note 8 Display', 1),
(83, 'Redmi Note 8 Display Replacement', 'Redmi Note 8 Display', 1),
(84, 'Redmi Note 7 Display Replacement', 'Redmi Note 7 Display', 1),
(85, 'Redmi 9A Display Replacement', 'Redmi 9A Display Replacement', 1),
(86, 'Redmi 8A Display Replacement', 'Redmi 8A Display Replacement', 1),
(87, 'Redmi 10A Display Replacement', 'Redmi 10A Display', 1),
(88, 'Redmi 10 Display Replacement', 'Redmi 10 Display', 1),
(89, 'Samsung A04s Display Replacement', 'Samsung A04s Display', 1),
(90, 'Vivo y65 Display Replacement', 'Vivo y65 Display Replacement', 1),
(91, 'Nokia C2 Display Replacement', 'Nokia C2 Display', 1),
(92, 'Nokia 1 touch Replacement', 'nokia 1 touch', 1),
(93, 'Nokia C01 plus Display Replacement', 'Nokia C01 plus Display', 1),
(94, 'Nokia 1.4 Display Replacement', 'Nokia 1.4 Display', 1),
(95, 'Nokia C1 Display Replacement', 'Nokia C1 Display', 1),
(96, 'Nokia 2.2 Display Replacement', 'Nokia 2.2 Display', 1),
(97, 'Nokia G10 Display Replacement', 'Nokia G10 Display', 1),
(98, 'Power  Key Repair', 'Out key', 1),
(99, 'Power  Key Repair', 'Power ribbon', 1),
(100, 'charging Port Replacement', 'charging port', 1),
(101, 'battery pin Replacement', 'Battery pin', 1),
(102, 'matte Tempered glass', 'matte Tempered Glass', 1),
(103, 'software Frp', 'Software', 1),
(104, 'y5 18 battery replacement', 'y5 18 battery', 1),
(105, 'Camera replacement', 'camera', 1);

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
(1, 0, 800.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=1\'> 1 </a> for m02 Original Display Replace', '2024-10-17', '10:55:55'),
(2, 0, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=1\'> 1 </a> for Clear Tempered Glass ', '2024-10-17', '10:55:55'),
(3, 15, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=2\'> 2 </a> for Clear Tempered Glass ', '2024-10-18', '09:46:26'),
(4, 1, -52.50, 'Profit (Invoice Edit) from Invoice Number : <a href=\'/invoice/print.php?id=2\'> 2 </a>', '2024-10-18', '09:48:04'),
(5, 1, 0.00, 'Profit (Invoice Edit) from Invoice Number : <a href=\'/invoice/print.php?id=2\'> 2 </a>', '2024-10-18', '09:48:09'),
(6, 0, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=4\'> 4 </a> for Clear Tempered Glass ', '2024-10-18', '11:07:43'),
(7, 15, 400.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=5\'> 5 </a> for charging Port Replacement', '2024-10-18', '11:44:56'),
(8, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=6\'> 6 </a> for xpc25 type c normal charger', '2024-10-18', '11:51:57'),
(9, 1, 75.00, 'Profit (Balance Pay) from Invoice Number : <a href=\'/invoice/print.php?id=2\'> 2 </a>', '2024-10-18', '12:03:24'),
(10, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=7\'> 7 </a> for OPPO A59/f1s back cover', '2024-10-18', '12:22:58'),
(11, 0, 400.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=8\'> 8 </a> for Power  Key Repair', '2024-10-18', '13:02:30'),
(12, 0, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=8\'> 8 </a> for Clear Tempered Glass ', '2024-10-18', '13:02:30'),
(13, 1, 0.00, 'Commission Added for watch Charger in Invoice Number : <a href=\'/invoice/print.php?id=3\'> 3 </a>', '2024-10-18', '13:03:28'),
(14, 1, -112.50, 'Profit (Invoice Edit) from Invoice Number : <a href=\'/invoice/print.php?id=8\'> 8 </a>', '2024-10-18', '13:05:18'),
(15, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=9\'> 9 </a> for Xpc25 micro normal charger', '2024-10-18', '13:08:56'),
(16, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=10\'> 10 </a> for Samsung A11 back Cover', '2024-10-18', '13:09:46'),
(17, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=11\'> 11 </a> for bike holder By555', '2024-10-18', '13:39:23'),
(18, 1, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=12\'> 12 </a> for Oms 5c battery(3 month warranty)', '2024-10-18', '13:57:02'),
(19, 14, 400.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=13\'> 13 </a> for battery Short repair ', '2024-10-18', '14:26:18'),
(20, 1, 270.00, 'Profit (Balance Pay) from Invoice Number : <a href=\'/invoice/print.php?id=8\'> 8 </a>', '2024-10-18', '14:28:08'),
(21, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=14\'> 14 </a> for Iphone Magsafe Case ', '2024-10-18', '14:34:26'),
(22, 0, 400.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=15\'> 15 </a> for Power  Key Repair', '2024-10-18', '15:46:07'),
(23, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=16\'> 16 </a> for CB-06 Micro Cable ', '2024-10-18', '16:08:47'),
(24, 1, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=17\'> 17 </a> for Clear Tempered Glass ', '2024-10-18', '16:16:04'),
(25, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=18\'> 18 </a> for Redmi A3 back Cover', '2024-10-18', '16:32:20'),
(26, 0, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=19\'> 19 </a> for Samsung M01 core Display Replacement', '2024-10-18', '16:43:40'),
(27, 0, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=20\'> 20 </a> for Samsung M01 core Display Replacement', '2024-10-18', '16:44:22'),
(28, 15, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=21\'> 21 </a> for charging Port Replacement', '2024-10-18', '17:19:14'),
(29, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=22\'> 22 </a> for 5c normal Battery', '2024-10-18', '17:53:07'),
(30, 14, 600.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=23\'> 23 </a> for Y5 2019 Display Replacement', '2024-10-18', '18:11:10'),
(31, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=24\'> 24 </a> for Tecno pop 5 lite back cover', '2024-10-18', '18:16:46'),
(32, 0, 200.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=25\'> 25 </a> for software Frp', '2024-10-18', '18:18:47'),
(33, 14, 100.00, 'Commission Added for tempered mtb in Invoice Number : <a href=\'/invoice/print.php?id=18\'> 18 </a>', '2024-10-18', '18:19:52'),
(34, 0, 400.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=26\'> 26 </a> for y5 18 battery replacement', '2024-10-19', '09:37:42'),
(35, 0, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=26\'> 26 </a> for Clear Tempered Glass ', '2024-10-19', '09:37:42'),
(36, 0, 200.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=27\'> 27 </a> for software Frp', '2024-10-19', '09:39:14'),
(37, 15, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=28\'> 28 </a> for Clear Tempered Glass ', '2024-10-19', '10:26:35'),
(38, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=29\'> 29 </a> for OS-E02 Lightning Earphone', '2024-10-19', '10:45:11'),
(39, 1, 0.00, 'Profit (Invoice Edit) from Invoice Number : <a href=\'/invoice/print.php?id=29\'> 29 </a>', '2024-10-19', '10:54:28'),
(40, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=30\'> 30 </a> for oms T20 Air pod', '2024-10-19', '11:21:21'),
(41, 0, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=31\'> 31 </a> for Samsung A10 normal Display Replacement', '2024-10-19', '11:37:20'),
(42, 0, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=31\'> 31 </a> for Clear Tempered Glass ', '2024-10-19', '11:37:20'),
(43, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=32\'> 32 </a> for xpc25 type c normal charger', '2024-10-19', '12:01:42'),
(44, 14, 800.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=33\'> 33 </a> for Samsung M21 oled display Replacement', '2024-10-19', '12:50:22'),
(45, 0, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=33\'> 33 </a> for Clear Tempered Glass ', '2024-10-19', '12:50:22'),
(46, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=34\'> 34 </a> for 4G dongle ', '2024-10-19', '12:58:52'),
(47, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=35\'> 35 </a> for pixel 6 back cover', '2024-10-19', '13:11:32'),
(48, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=36\'> 36 </a> for Samsung A12 back Cover', '2024-10-19', '13:19:20'),
(49, 0, 200.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=38\'> 38 </a> for software Frp', '2024-10-19', '13:19:55'),
(50, 0, 400.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=39\'> 39 </a> for charging Port Replacement', '2024-10-19', '13:34:56'),
(51, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=40\'> 40 </a> for Realme C11 Back cover', '2024-10-19', '14:14:05'),
(52, 15, 400.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=41\'> 41 </a> for charging Port Replacement', '2024-10-19', '14:21:52'),
(53, 0, 200.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=42\'> 42 </a> for software Frp', '2024-10-19', '14:32:39'),
(54, 14, 500.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=43\'> 43 </a> for Camera replacement', '2024-10-19', '14:36:00'),
(55, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=44\'> 44 </a> for Iphone 7  ', '2024-10-19', '14:52:36'),
(56, 1, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=44\'> 44 </a> for MTB Tempered', '2024-10-19', '14:52:36'),
(57, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=45\'> 45 </a> for AKG -21 Type-C Headphone', '2024-10-19', '15:58:55'),
(58, 1, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=46\'> 46 </a> for Super D tempered Glass ', '2024-10-19', '16:29:24'),
(59, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=47\'> 47 </a> for OS-A04 Type-c to Lightning Cable', '2024-10-19', '18:11:55'),
(60, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=48\'> 48 </a> for YS-859 Ligthning to C cable', '2024-10-19', '18:27:58'),
(61, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=49\'> 49 </a> for sm12 sinha micro charger', '2024-10-19', '18:46:14'),
(62, 0, 500.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=50\'> 50 </a> for Camera replacement', '2024-10-19', '18:54:01'),
(63, 0, 400.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=51\'> 51 </a> for Power  Key Repair', '2024-10-20', '10:50:57'),
(64, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=52\'> 52 </a> for J2 2015', '2024-10-20', '10:52:01'),
(65, 1, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=52\'> 52 </a> for MTB Tempered', '2024-10-20', '10:52:01'),
(66, 0, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=53\'> 53 </a> for Samsung M01 core Display Replacement', '2024-10-20', '11:20:37'),
(67, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=54\'> 54 </a> for Samsung M01 Core back Cover', '2024-10-20', '11:25:28'),
(68, 1, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=55\'> 55 </a> for MTB Tempered', '2024-10-20', '15:02:57'),
(69, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=56\'> 56 </a> for Xpc30 Micro chager', '2024-10-20', '15:22:23'),
(70, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=57\'> 57 </a> for 25W Oms samsum (6 month warranty) ', '2024-10-20', '18:23:16'),
(71, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=58\'> 58 </a> for Xpc25 micro normal charger', '2024-10-21', '15:24:16'),
(72, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=59\'> 59 </a> for Oms E250 Battery (3 month warranty ) ', '2024-10-21', '16:59:30'),
(73, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=60\'> 60 </a> for Om418 type-c chager', '2024-10-21', '21:17:00'),
(74, 0, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=61\'> 61 </a> for Samsung A10s normal Display Replacement', '2024-10-23', '11:25:57'),
(75, 0, 800.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=62\'> 62 </a> for Redmi 9A Display Replacement', '2024-10-24', '19:31:52'),
(76, 0, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=62\'> 62 </a> for Clear Tempered Glass ', '2024-10-24', '19:31:52'),
(77, 1, 182.50, 'Profit (Invoice Edit) from Invoice Number : <a href=\'/invoice/print.php?id=62\'>62</a>', '2024-10-24', '19:33:18'),
(78, 14, 365.00, 'Profit (Invoice Edit) from Invoice Number : <a href=\'/invoice/print.php?id=62\'> 62 </a>', '2024-10-24', '19:33:18'),
(79, 15, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=63\'> 63 </a> for MTB Tempered', '2024-10-25', '10:28:04'),
(80, 0, 800.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=64\'> 64 </a> for Vivo y65 Display Replacement', '2024-10-25', '10:36:40'),
(81, 1, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=65\'> 65 </a> for MTB Tempered', '2024-10-25', '10:44:27'),
(82, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=66\'> 66 </a> for Samsung J5 2016 back Cover', '2024-10-25', '11:05:45'),
(83, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=66\'> 66 </a> for Samsung A15 back Cover', '2024-10-25', '11:05:45'),
(84, 1, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=66\'> 66 </a> for MTB Tempered', '2024-10-25', '11:05:45'),
(85, 1, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=67\'> 67 </a> for Super D tempered Glass ', '2024-10-25', '11:10:00'),
(86, 0, 800.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=68\'> 68 </a> for oppo A5S Display replacement', '2024-10-25', '11:13:38'),
(87, 0, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=68\'> 68 </a> for Clear Tempered Glass ', '2024-10-25', '11:13:38'),
(88, 0, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=69\'> 69 </a> for charging Port Replacement', '2024-10-25', '11:25:14'),
(89, 0, 200.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=70\'> 70 </a> for software Frp', '2024-10-25', '11:33:36'),
(90, 1, 0.00, 'Commission Added for M02 back cover in Invoice Number : <a href=\'/invoice/print.php?id=65\'> 65 </a>', '2024-10-25', '12:22:27'),
(91, 0, 200.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=71\'> 71 </a> for software Frp', '2024-10-25', '12:22:52'),
(92, 1, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=72\'> 72 </a> for osy08 pd 20w charger lighting', '2024-10-25', '12:51:29'),
(93, 0, 700.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=73\'> 73 </a> for Y5 2018 Display Replacement', '2024-10-25', '13:05:45'),
(94, 0, 100.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=73\'> 73 </a> for Clear Tempered Glass ', '2024-10-25', '13:05:45'),
(95, 1, 200.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=74\'> 74 </a> for MTB Tempered', '2024-10-26', '09:56:46'),
(96, 0, 0.00, 'Commission from Invoice : <a href=\'/invoice/print.php?id=75\'> 75 </a> for charging Port Replacement', '2024-10-26', '10:44:13');

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
(1, 1, 'm02 Original Display Replace', NULL, '1', 4500.00, 4500.00, 2500.00, 1200.00, 'ifix'),
(2, 1, 'Clear Tempered Glass ', NULL, '1', 500.00, 500.00, 150.00, 250.00, 'ifix'),
(3, 2, 'Clear Tempered Glass ', NULL, '1', 500.00, 500.00, 150.00, 250.00, 'Kasun'),
(4, 4, 'Clear Tempered Glass ', NULL, '1', 400.00, 400.00, 150.00, 150.00, 'ifix'),
(5, 5, 'charging Port Replacement', NULL, '1', 1000.00, 1000.00, 200.00, 400.00, 'Kasun'),
(6, 6, 'xpc25 type c normal charger', NULL, '1', 800.00, 800.00, 400.00, 400.00, 'lakmal'),
(7, 7, 'OPPO A59/f1s back cover', NULL, '1', 600.00, 600.00, 250.00, 350.00, 'lakmal'),
(8, 8, 'Power  Key Repair', NULL, '1', 1300.00, 1300.00, 500.00, 400.00, 'ifix'),
(9, 8, 'Clear Tempered Glass ', NULL, '1', 500.00, 500.00, 150.00, 250.00, 'ifix'),
(10, 9, 'Xpc25 micro normal charger', NULL, '1', 800.00, 800.00, 400.00, 400.00, 'lakmal'),
(11, 10, 'Samsung A11 back Cover', NULL, '1', 500.00, 500.00, 250.00, 250.00, 'lakmal'),
(12, 11, 'bike holder By555', NULL, '1', 1300.00, 1300.00, 700.00, 600.00, 'lakmal'),
(13, 12, 'Oms 5c battery(3 month warranty)', NULL, '1', 800.00, 800.00, 260.00, 540.00, 'lakmal'),
(14, 13, 'battery Short repair ', NULL, '1', 1500.00, 1500.00, 0.00, 1100.00, 'Udaya'),
(15, 14, 'Iphone Magsafe Case ', NULL, '1', 1000.00, 1000.00, 480.00, 520.00, 'lakmal'),
(16, 15, 'Power  Key Repair', NULL, '1', 2200.00, 2200.00, 500.00, 1300.00, 'ifix'),
(17, 16, 'CB-06 Micro Cable ', NULL, '1', 300.00, 300.00, 150.00, 150.00, 'lakmal'),
(18, 17, 'Clear Tempered Glass ', NULL, '1', 500.00, 500.00, 150.00, 250.00, 'lakmal'),
(19, 18, 'Redmi A3 back Cover', NULL, '1', 600.00, 600.00, 250.00, 350.00, 'lakmal'),
(20, 19, 'Samsung M01 core Display Replacement', NULL, '1', 4500.00, 4500.00, 2500.00, 2000.00, 'ifix'),
(21, 20, 'Samsung M01 core Display Replacement', NULL, '1', 4500.00, 4500.00, 2500.00, 2000.00, 'ifix'),
(22, 21, 'charging Port Replacement', NULL, '1', 400.00, 400.00, 100.00, 200.00, 'Kasun'),
(23, 22, '5c normal Battery', NULL, '1', 400.00, 400.00, 200.00, 200.00, 'lakmal'),
(24, 23, 'Y5 2019 Display Replacement', NULL, '1', 4500.00, 4500.00, 2800.00, 1100.00, 'Udaya'),
(25, 24, 'Tecno pop 5 lite back cover', NULL, '1', 600.00, 600.00, 250.00, 350.00, 'lakmal'),
(26, 25, 'software Frp', NULL, '1', 1000.00, 1000.00, 1.00, 799.00, 'ifix'),
(27, 26, 'y5 18 battery replacement', NULL, '1', 3200.00, 3200.00, 1950.00, 850.00, 'ifix'),
(28, 26, 'Clear Tempered Glass ', NULL, '1', 400.00, 400.00, 150.00, 150.00, 'ifix'),
(29, 27, 'software Frp', NULL, '1', 400.00, 400.00, 1.00, 199.00, 'ifix'),
(30, 28, 'Clear Tempered Glass ', NULL, '1', 800.00, 800.00, 150.00, 550.00, 'Kasun'),
(31, 29, 'OS-E02 Lightning Earphone', NULL, '1', 1700.00, 1700.00, 950.00, 750.00, 'lakmal'),
(32, 30, 'oms T20 Air pod', NULL, '1', 4600.00, 4600.00, 3000.00, 1600.00, 'lakmal'),
(33, 31, 'Samsung A10 normal Display Replacement', NULL, '1', 4500.00, 4500.00, 0.00, 4500.00, 'ifix'),
(34, 31, 'Clear Tempered Glass ', NULL, '1', 500.00, 500.00, 150.00, 250.00, 'ifix'),
(35, 32, 'xpc25 type c normal charger', NULL, '1', 800.00, 800.00, 400.00, 400.00, 'lakmal'),
(36, 33, 'Samsung M21 oled display Replacement', NULL, '1', 8500.00, 8500.00, 6400.00, 1300.00, 'Udaya'),
(37, 33, 'Clear Tempered Glass ', NULL, '1', 500.00, 500.00, 150.00, 250.00, 'ifix'),
(38, 34, '4G dongle ', NULL, '1', 3800.00, 3800.00, 3500.00, 300.00, 'lakmal'),
(39, 35, 'pixel 6 back cover', NULL, '1', 750.00, 750.00, 250.00, 500.00, 'lakmal'),
(40, 36, 'Samsung A12 back Cover', NULL, '1', 500.00, 500.00, 250.00, 250.00, 'lakmal'),
(41, 38, 'software Frp', NULL, '1', 500.00, 500.00, 1.00, 299.00, 'ifix'),
(42, 39, 'charging Port Replacement', NULL, '1', 500.00, 500.00, 200.00, -100.00, 'ifix'),
(43, 40, 'Realme C11 Back cover', NULL, '1', 600.00, 600.00, 250.00, 350.00, 'lakmal'),
(44, 41, 'charging Port Replacement', NULL, '1', 500.00, 500.00, 200.00, -100.00, 'Kasun'),
(45, 42, 'software Frp', NULL, '1', 800.00, 800.00, 1.00, 599.00, 'ifix'),
(46, 43, 'Camera replacement', NULL, '1', 3000.00, 3000.00, 1000.00, 1500.00, 'Udaya'),
(47, 44, 'Iphone 7  ', NULL, '1', 500.00, 500.00, 250.00, 250.00, 'lakmal'),
(48, 44, 'MTB Tempered', NULL, '1', 500.00, 500.00, 480.00, 270.00, 'lakmal'),
(49, 45, 'AKG -21 Type-C Headphone', NULL, '1', 1500.00, 1500.00, 800.00, 700.00, 'lakmal'),
(50, 46, 'Super D tempered Glass ', NULL, '1', 800.00, 800.00, 250.00, 550.00, 'lakmal'),
(51, 47, 'OS-A04 Type-c to Lightning Cable', NULL, '1', 800.00, 800.00, 475.00, 325.00, 'lakmal'),
(52, 48, 'YS-859 Ligthning to C cable', NULL, '1', 1700.00, 1700.00, 950.00, 750.00, 'lakmal'),
(53, 49, 'sm12 sinha micro charger', NULL, '1', 800.00, 800.00, 700.00, 100.00, 'lakmal'),
(54, 50, 'Camera replacement', NULL, '1', 2400.00, 2400.00, 1000.00, 900.00, 'ifix'),
(55, 51, 'Power  Key Repair', NULL, '1', 1500.00, 1500.00, 200.00, 900.00, 'ifix'),
(56, 52, 'J2 2015', NULL, '1', 400.00, 400.00, 200.00, 200.00, 'lakmal'),
(57, 52, 'MTB Tempered', NULL, '1', 400.00, 400.00, 430.00, 170.00, 'lakmal'),
(58, 53, 'Samsung M01 core Display Replacement', NULL, '1', 4500.00, 4500.00, 2500.00, 2000.00, 'ifix'),
(59, 54, 'Samsung M01 Core back Cover', NULL, '1', 650.00, 650.00, 250.00, 400.00, 'lakmal'),
(60, 55, 'MTB Tempered', NULL, '1', 500.00, 500.00, 230.00, 270.00, 'lakmal'),
(61, 56, 'Xpc30 Micro chager', NULL, '1', 2000.00, 2000.00, 1250.00, 750.00, 'lakmal'),
(62, 57, '25W Oms samsum (6 month warranty) ', NULL, '1', 3750.00, 3750.00, 1450.00, 2300.00, 'lakmal'),
(63, 58, 'Xpc25 micro normal charger', NULL, '1', 800.00, 800.00, 400.00, 400.00, 'lakmal'),
(64, 59, 'Oms E250 Battery (3 month warranty ) ', NULL, '1', 800.00, 800.00, 400.00, 400.00, 'lakmal'),
(65, 60, 'Om418 type-c chager', NULL, '1', 2400.00, 2400.00, 1400.00, 1000.00, 'lakmal'),
(66, 61, 'Samsung A10s normal Display Replacement', NULL, '1', 5500.00, 5500.00, 2400.00, 3100.00, 'ifix'),
(67, 62, 'Redmi 9A Display Replacement', NULL, '1', 5000.00, 5000.00, 2600.00, 1600.00, 'ifix'),
(68, 62, 'Clear Tempered Glass ', NULL, '1', 500.00, 500.00, 150.00, 250.00, 'ifix'),
(69, 63, 'MTB Tempered', NULL, '1', 500.00, 500.00, 230.00, 270.00, 'Kasun'),
(70, 64, 'Vivo y65 Display Replacement', NULL, '1', 5500.00, 5500.00, 2500.00, 2200.00, 'ifix'),
(71, 65, 'MTB Tempered', NULL, '1', 700.00, 700.00, 230.00, 470.00, 'lakmal'),
(72, 66, 'Samsung J5 2016 back Cover', NULL, '1', 600.00, 600.00, 200.00, 400.00, 'lakmal'),
(73, 66, 'Samsung A15 back Cover', NULL, '1', 600.00, 600.00, 450.00, 350.00, 'lakmal'),
(74, 66, 'MTB Tempered', NULL, '1', 500.00, 500.00, 680.00, 270.00, 'lakmal'),
(75, 67, 'Super D tempered Glass ', NULL, '1', 700.00, 700.00, 250.00, 450.00, 'lakmal'),
(76, 68, 'oppo A5S Display replacement', NULL, '1', 4500.00, 4500.00, 2500.00, 1200.00, 'ifix'),
(77, 68, 'Clear Tempered Glass ', NULL, '1', 800.00, 800.00, 150.00, 550.00, 'ifix'),
(78, 69, 'charging Port Replacement', NULL, '1', 500.00, 500.00, 200.00, 300.00, 'ifix'),
(79, 70, 'software Frp', NULL, '1', 400.00, 400.00, 1.00, 199.00, 'ifix'),
(80, 71, 'software Frp', NULL, '1', 1500.00, 1500.00, 1.00, 1299.00, 'ifix'),
(81, 72, 'osy08 pd 20w charger lighting', NULL, '1', 2500.00, 2500.00, 1450.00, 1050.00, 'lakmal'),
(82, 73, 'Y5 2018 Display Replacement', NULL, '1', 4500.00, 4500.00, 2500.00, 1300.00, 'ifix'),
(83, 73, 'Clear Tempered Glass ', NULL, '1', 500.00, 500.00, 150.00, 250.00, 'ifix'),
(84, 74, 'MTB Tempered', NULL, '2', 500.00, 1000.00, 460.00, 540.00, 'lakmal'),
(85, 75, 'charging Port Replacement', NULL, '1', 850.00, 850.00, 200.00, 650.00, 'ifix');

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
  `employ_id` int(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `transaction_log`
--

INSERT INTO `transaction_log` (`transaction_id`, `transaction_type`, `description`, `amount`, `transaction_date`, `transaction_time`, `employ_id`) VALUES
(1, 'Invoice - Company Profit', 'Profit to Company. Inv: 1 - Cash, Profit : Rs. 1550', 1550, '2024-10-17', '10:55:56', 1),
(2, 'Invoice - Cash In', '1 - Cash, Payment Method : Cash, Advance : Rs. 5100', 5100, '2024-10-17', '10:55:56', 1),
(3, 'Invoice - Company Profit', 'Profit to Company. Inv: 2 - Cash, Profit : Rs. 350', 350, '2024-10-18', '09:46:26', 1),
(4, 'Invoice - Cash In', '2 - Cash, Payment Method : Cash, Advance : Rs. 600', 600, '2024-10-18', '09:46:26', 1),
(5, 'Invoice - Company Profit', 'Profit (Invoice Edit) from Invoice Number : <a href=\'/invoice/print.php?id=2\'> 2 </a>', 0, '2024-10-18', '09:48:09', 0),
(6, 'Invoice - Company Profit', 'Profit to Company. Inv: 3 - Cash, Profit : Rs. 700', 700, '2024-10-18', '09:59:23', 1),
(7, 'Invoice - Cash In', '3 - Cash, Payment Method : Cash, Advance : Rs. 700', 700, '2024-10-18', '09:59:23', 1),
(8, 'Repair Stock Item Purchase', 'Battery pin', -7500, '2024-10-18', '10:18:48', 1),
(9, 'Invoice - Company Profit', 'Profit to Company. Inv: 4 - Cash, Profit : Rs. 350', 350, '2024-10-18', '11:07:43', 1),
(10, 'Invoice - Cash In', '4 - Cash, Payment Method : Cash, Advance : Rs. 600', 600, '2024-10-18', '11:07:43', 1),
(11, 'Invoice - Company Profit', 'Profit to Company. Inv: 5 - chr, Profit : Rs. 200', 200, '2024-10-18', '11:44:56', 1),
(12, 'Invoice - Cash In', '5 - chr, Payment Method : Cash, Advance : Rs. 800', 800, '2024-10-18', '11:44:56', 1),
(13, 'Repair Stock Item Purchase', 'matte Tempered Glass ', -40000, '2024-10-18', '11:49:05', 1),
(14, 'Invoice - Company Profit', 'Profit to Company. Inv: 6 - Cash, Profit : Rs. 400', 400, '2024-10-18', '11:51:57', 1),
(15, 'Invoice - Cash In', '6 - Cash, Payment Method : Cash, Advance : Rs. 800', 800, '2024-10-18', '11:51:57', 1),
(16, 'Invoice - Company Profit', 'Profit (Balance Pay) from Invoice Number : <a href=\'/invoice/print.php?id=2\'> 2 </a>', 425, '2024-10-18', '12:03:24', 0),
(17, 'Add Invoice Balance Payment', 'Add Fund to Invoice Number : <a href=\'/invoice/print.php?id=2\'> 2 </a>', 500, '2024-10-18', '12:03:24', 0),
(18, 'Invoice - Company Profit', 'Profit to Company. Inv: 7 - Cash, Profit : Rs. 350', 350, '2024-10-18', '12:22:58', 1),
(19, 'Invoice - Cash In', '7 - Cash, Payment Method : Cash, Advance : Rs. 600', 600, '2024-10-18', '12:22:58', 1),
(20, 'Petty Cash', 'sig', -500, '2024-10-18', '12:50:20', 1),
(21, 'Invoice - Company Profit', 'Profit to Company. Inv: 8 - Cash, Profit : Rs. 750', 750, '2024-10-18', '13:02:30', 1),
(22, 'Invoice - Cash In', '8 - Cash, Payment Method : Cash, Advance : Rs. 1900', 1900, '2024-10-18', '13:02:30', 1),
(23, 'Fall OneTimeProduct Cost from Company Profit', 'Fall Rs.0 in Company Profit Account for Repair/Service ID : 1', 0, '2024-10-18', '13:03:28', 1),
(24, 'Add Salary Commission', 'Increase Salary of lakmal for watch Charger by Rs.0', 0, '2024-10-18', '13:03:28', 1),
(25, 'Invoice - Company Profit', 'Profit (Invoice Edit) from Invoice Number : <a href=\'/invoice/print.php?id=8\'> 8 </a>', -638, '2024-10-18', '13:05:18', 0),
(26, 'Invoice - Company Profit', 'Profit to Company. Inv: 9 - Cash, Profit : Rs. 400', 400, '2024-10-18', '13:08:57', 1),
(27, 'Invoice - Cash In', '9 - Cash, Payment Method : Cash, Advance : Rs. 800', 800, '2024-10-18', '13:08:57', 1),
(28, 'Invoice - Company Profit', 'Profit to Company. Inv: 10 - Cash, Profit : Rs. 250', 250, '2024-10-18', '13:09:46', 1),
(29, 'Invoice - Cash In', '10 - Cash, Payment Method : Cash, Advance : Rs. 500', 500, '2024-10-18', '13:09:46', 1),
(30, 'Invoice - Company Profit', 'Profit to Company. Inv: 11 - Cash, Profit : Rs. 600', 600, '2024-10-18', '13:39:23', 1),
(31, 'Invoice - Cash In', '11 - Cash, Payment Method : Cash, Advance : Rs. 1300', 1300, '2024-10-18', '13:39:23', 1),
(32, 'Invoice - Company Profit', 'Profit to Company. Inv: 12 - Cash, Profit : Rs. 540', 540, '2024-10-18', '13:57:02', 1),
(33, 'Invoice - Cash In', '12 - Cash, Payment Method : Cash, Advance : Rs. 800', 800, '2024-10-18', '13:57:02', 1),
(34, 'Invoice - Company Profit', 'Profit to Company. Inv: 13 - Cash, Profit : Rs. 600', 600, '2024-10-18', '14:26:18', 1),
(35, 'Invoice - Cash In', '13 - Cash, Payment Method : Cash, Advance : Rs. 1000', 1000, '2024-10-18', '14:26:18', 1),
(36, 'Petty Cash', 'sig', -500, '2024-10-18', '14:27:06', 1),
(37, 'Invoice - Company Profit', 'Profit (Balance Pay) from Invoice Number : <a href=\'/invoice/print.php?id=8\'> 8 </a>', 1530, '2024-10-18', '14:28:08', 0),
(38, 'Add Invoice Balance Payment', 'Add Fund to Invoice Number : <a href=\'/invoice/print.php?id=8\'> 8 </a>', 1800, '2024-10-18', '14:28:08', 0),
(39, 'Invoice - Company Profit', 'Profit to Company. Inv: 14 - Cash, Profit : Rs. 520', 520, '2024-10-18', '14:34:26', 1),
(40, 'Invoice - Cash In', '14 - Cash, Payment Method : Cash, Advance : Rs. 1000', 1000, '2024-10-18', '14:34:26', 1),
(41, 'Petty Cash', 'daily', -2000, '2024-10-18', '14:53:53', 1),
(42, 'Bank Deposit', 'Add Rs.5000 to Co op shop Bank Account', 5000, '2024-10-18', '15:05:24', 1),
(43, 'Bank Deposit', 'Add Rs.2000 to Co op Other Bank Account', 2000, '2024-10-18', '15:05:34', 1),
(44, 'Invoice - Company Profit', 'Profit to Company. Inv: 15 - Cash, Profit : Rs. 264', 264, '2024-10-18', '15:46:07', 1),
(45, 'Invoice - Cash In', '15 - Cash, Payment Method : CardPayment, Advance : Rs. 1164', 1164, '2024-10-18', '15:46:07', 1),
(46, 'Invoice - Company Profit', 'Profit to Company. Inv: 16 - Cash, Profit : Rs. 150', 150, '2024-10-18', '16:08:47', 1),
(47, 'Invoice - Cash In', '16 - Cash, Payment Method : Cash, Advance : Rs. 300', 300, '2024-10-18', '16:08:47', 1),
(48, 'Invoice - Company Profit', 'Profit to Company. Inv: 17 - Cash, Profit : Rs. 350', 350, '2024-10-18', '16:16:04', 1),
(49, 'Invoice - Cash In', '17 - Cash, Payment Method : Cash, Advance : Rs. 600', 600, '2024-10-18', '16:16:04', 1),
(50, 'Invoice - Company Profit', 'Profit to Company. Inv: 18 - Cash, Profit : Rs. 950', 950, '2024-10-18', '16:32:20', 1),
(51, 'Invoice - Cash In', '18 - Cash, Payment Method : Cash, Advance : Rs. 1200', 1200, '2024-10-18', '16:32:20', 1),
(52, 'Invoice - Company Profit', 'Profit to Company. Inv: 21 - Cash, Profit : Rs. 600', 600, '2024-10-18', '17:19:14', 1),
(53, 'Invoice - Cash In', '21 - Cash, Payment Method : Cash, Advance : Rs. 800', 800, '2024-10-18', '17:19:14', 1),
(54, 'Invoice - Company Profit', 'Profit to Company. Inv: 22 - Cash, Profit : Rs. 200', 200, '2024-10-18', '17:53:07', 1),
(55, 'Invoice - Cash In', '22 - Cash, Payment Method : Cash, Advance : Rs. 400', 400, '2024-10-18', '17:53:07', 1),
(56, 'Invoice - Company Profit', 'Profit to Company. Inv: 23 - Cash, Profit : Rs. 1100', 1100, '2024-10-18', '18:11:10', 1),
(57, 'Invoice - Cash In', '23 - Cash, Payment Method : Cash, Advance : Rs. 4500', 4500, '2024-10-18', '18:11:10', 1),
(58, 'Repair Stock Item Purchase', 'Software', -100, '2024-10-18', '18:15:35', 1),
(59, 'Invoice - Company Profit', 'Profit to Company. Inv: 24 - Cash, Profit : Rs. 350', 350, '2024-10-18', '18:16:46', 1),
(60, 'Invoice - Cash In', '24 - Cash, Payment Method : Cash, Advance : Rs. 600', 600, '2024-10-18', '18:16:46', 1),
(61, 'Invoice - Company Profit', 'Profit to Company. Inv: 25 - Cash, Profit : Rs. 799', 799, '2024-10-18', '18:18:47', 1),
(62, 'Invoice - Cash In', '25 - Cash, Payment Method : Cash, Advance : Rs. 1000', 1000, '2024-10-18', '18:18:47', 1),
(63, 'Fall OneTimeProduct Cost from Company Profit', 'Fall Rs.100 in Company Profit Account for Repair/Service ID : 2', -100, '2024-10-18', '18:19:52', 1),
(64, 'Add Salary Commission', 'Increase Salary of Udaya for tempered mtb by Rs.100', 100, '2024-10-18', '18:19:52', 1),
(65, 'Repair Stock Item Purchase', 'y5 18 battery ', -1950, '2024-10-19', '09:28:03', 1),
(66, 'Invoice - Company Profit', 'Profit to Company. Inv: 26 - Cash, Profit : Rs. 1000', 1000, '2024-10-19', '09:37:42', 1),
(67, 'Invoice - Cash In', '26 - Cash, Payment Method : Cash, Advance : Rs. 3600', 3600, '2024-10-19', '09:37:42', 1),
(68, 'Invoice - Company Profit', 'Profit to Company. Inv: 27 - Cash, Profit : Rs. 199', 199, '2024-10-19', '09:39:14', 1),
(69, 'Invoice - Cash In', '27 - Cash, Payment Method : Cash, Advance : Rs. 400', 400, '2024-10-19', '09:39:14', 1),
(70, 'Invoice - Company Profit', 'Profit to Company. Inv: 28 - Cash, Profit : Rs. 550', 550, '2024-10-19', '10:26:35', 1),
(71, 'Invoice - Cash In', '28 - Cash, Payment Method : Cash, Advance : Rs. 800', 800, '2024-10-19', '10:26:35', 1),
(72, 'Invoice - Company Profit', 'Profit to Company. Inv: 29 - Cash, Profit : Rs. 750', 750, '2024-10-19', '10:45:11', 1),
(73, 'Invoice - Cash In', '29 - Cash, Payment Method : Cash, Advance : Rs. 1700', 1700, '2024-10-19', '10:45:11', 1),
(74, 'Petty Cash', 'sig', -1000, '2024-10-19', '10:54:07', 1),
(75, 'Invoice - Company Profit', 'Profit (Invoice Edit) from Invoice Number : <a href=\'/invoice/print.php?id=29\'> 29 </a>', 0, '2024-10-19', '10:54:28', 0),
(76, 'Repair Stock Item Purchase', '20pin Display', -16000, '2024-10-19', '11:08:06', 1),
(77, 'Invoice - Company Profit', 'Profit to Company. Inv: 30 - Cash, Profit : Rs. 1600', 1600, '2024-10-19', '11:21:21', 1),
(78, 'Invoice - Cash In', '30 - Cash, Payment Method : Cash, Advance : Rs. 4600', 4600, '2024-10-19', '11:21:21', 1),
(79, 'Invoice - Company Profit', 'Profit to Company. Inv: 31 - Cash, Profit : Rs. 4750', 4750, '2024-10-19', '11:37:20', 1),
(80, 'Invoice - Cash In', '31 - Cash, Payment Method : Cash, Advance : Rs. 5000', 5000, '2024-10-19', '11:37:20', 1),
(81, 'Invoice - Company Profit', 'Profit to Company. Inv: 32 - Cash, Profit : Rs. 400', 400, '2024-10-19', '12:01:42', 1),
(82, 'Invoice - Cash In', '32 - Cash, Payment Method : Cash, Advance : Rs. 800', 800, '2024-10-19', '12:01:42', 1),
(83, 'Invoice - Company Profit', 'Profit to Company. Inv: 33 - Cash, Profit : Rs. 1550', 1550, '2024-10-19', '12:50:22', 1),
(84, 'Invoice - Cash In', '33 - Cash, Payment Method : Cash, Advance : Rs. 9000', 9000, '2024-10-19', '12:50:22', 1),
(85, 'Invoice - Company Profit', 'Profit to Company. Inv: 34 - Cash, Profit : Rs. 300', 300, '2024-10-19', '12:58:52', 1),
(86, 'Invoice - Cash In', '34 - Cash, Payment Method : Cash, Advance : Rs. 3800', 3800, '2024-10-19', '12:58:52', 1),
(87, 'Petty Cash', 'tea', -300, '2024-10-19', '13:01:04', 1),
(88, 'Invoice - Company Profit', 'Profit to Company. Inv: 35 - Cash, Profit : Rs. 500', 500, '2024-10-19', '13:11:32', 1),
(89, 'Invoice - Cash In', '35 - Cash, Payment Method : Cash, Advance : Rs. 750', 750, '2024-10-19', '13:11:33', 1),
(90, 'Invoice - Company Profit', 'Profit to Company. Inv: 36 - Cash, Profit : Rs. 250', 250, '2024-10-19', '13:19:20', 1),
(91, 'Invoice - Cash In', '36 - Cash, Payment Method : Cash, Advance : Rs. 500', 500, '2024-10-19', '13:19:20', 1),
(92, 'Invoice - Company Profit', 'Profit to Company. Inv: 37 - Cash, Profit : Rs. 500', 500, '2024-10-19', '13:19:26', 1),
(93, 'Invoice - Cash In', '37 - Cash, Payment Method : Cash, Advance : Rs. 500', 500, '2024-10-19', '13:19:26', 1),
(94, 'Invoice - Company Profit', 'Profit to Company. Inv: 38 - Cash, Profit : Rs. 299', 299, '2024-10-19', '13:19:55', 1),
(95, 'Invoice - Cash In', '38 - Cash, Payment Method : Cash, Advance : Rs. 500', 500, '2024-10-19', '13:19:55', 1),
(96, 'Invoice - Company Profit', 'Profit to Company. Inv: 39 - Cash, Profit : Rs. -100', -100, '2024-10-19', '13:34:56', 1),
(97, 'Invoice - Cash In', '39 - Cash, Payment Method : Cash, Advance : Rs. 500', 500, '2024-10-19', '13:34:56', 1),
(98, 'Bank Deposit', 'Add Rs.5000 to Co op shop Bank Account', 5000, '2024-10-19', '13:54:59', 1),
(99, 'Bank Deposit', 'Add Rs.2000 to Co op Other Bank Account', 2000, '2024-10-19', '13:55:07', 1),
(100, 'Invoice - Company Profit', 'Profit to Company. Inv: 40 - Cash, Profit : Rs. 350', 350, '2024-10-19', '14:14:05', 1),
(101, 'Invoice - Cash In', '40 - Cash, Payment Method : Cash, Advance : Rs. 600', 600, '2024-10-19', '14:14:05', 1),
(102, 'Invoice - Company Profit', 'Profit to Company. Inv: 41 - Cash, Profit : Rs. -100', -100, '2024-10-19', '14:21:52', 1),
(103, 'Invoice - Cash In', '41 - Cash, Payment Method : Cash, Advance : Rs. 500', 500, '2024-10-19', '14:21:52', 1),
(104, 'Invoice - Company Profit', 'Profit to Company. Inv: 42 - Cash, Profit : Rs. 599', 599, '2024-10-19', '14:32:39', 1),
(105, 'Invoice - Cash In', '42 - Cash, Payment Method : Cash, Advance : Rs. 800', 800, '2024-10-19', '14:32:39', 1),
(106, 'Repair Stock Item Purchase', 'camera', -20000, '2024-10-19', '14:34:32', 1),
(107, 'Invoice - Company Profit', 'Profit to Company. Inv: 43 - Cash, Profit : Rs. 1500', 1500, '2024-10-19', '14:36:00', 1),
(108, 'Invoice - Cash In', '43 - Cash, Payment Method : Cash, Advance : Rs. 3000', 3000, '2024-10-19', '14:36:00', 1),
(109, 'Invoice - Company Profit', 'Profit to Company. Inv: 44 - Cash, Profit : Rs. 520', 520, '2024-10-19', '14:52:36', 1),
(110, 'Invoice - Cash In', '44 - Cash, Payment Method : Cash, Advance : Rs. 1000', 1000, '2024-10-19', '14:52:36', 1),
(111, 'Petty Cash', 'sig', -500, '2024-10-19', '15:05:42', 1),
(112, 'Petty Cash', 'home', -1500, '2024-10-19', '15:06:32', 1),
(113, 'Invoice - Company Profit', 'Profit to Company. Inv: 45 - Cash, Profit : Rs. 700', 700, '2024-10-19', '15:58:55', 1),
(114, 'Invoice - Cash In', '45 - Cash, Payment Method : Cash, Advance : Rs. 1500', 1500, '2024-10-19', '15:58:55', 1),
(115, 'Invoice - Company Profit', 'Profit to Company. Inv: 46 - Cash, Profit : Rs. 550', 550, '2024-10-19', '16:29:24', 1),
(116, 'Invoice - Cash In', '46 - Cash, Payment Method : Cash, Advance : Rs. 800', 800, '2024-10-19', '16:29:24', 1),
(117, 'Invoice - Company Profit', 'Profit to Company. Inv: 47 - Cash, Profit : Rs. 325', 325, '2024-10-19', '18:11:55', 1),
(118, 'Invoice - Cash In', '47 - Cash, Payment Method : Cash, Advance : Rs. 800', 800, '2024-10-19', '18:11:55', 1),
(119, 'Invoice - Company Profit', 'Profit to Company. Inv: 48 - Cash, Profit : Rs. 750', 750, '2024-10-19', '18:27:58', 1),
(120, 'Invoice - Cash In', '48 - Cash, Payment Method : Cash, Advance : Rs. 1700', 1700, '2024-10-19', '18:27:58', 1),
(121, 'Invoice - Company Profit', 'Profit to Company. Inv: 49 - Cash, Profit : Rs. 100', 100, '2024-10-19', '18:46:14', 1),
(122, 'Invoice - Cash In', '49 - Cash, Payment Method : Cash, Advance : Rs. 800', 800, '2024-10-19', '18:46:14', 1),
(123, 'Invoice - Company Profit', 'Profit to Company. Inv: 50 - Cash, Profit : Rs. 900', 900, '2024-10-19', '18:54:01', 1),
(124, 'Invoice - Cash In', '50 - Cash, Payment Method : Cash, Advance : Rs. 2400', 2400, '2024-10-19', '18:54:01', 1),
(125, 'Invoice - Company Profit', 'Profit to Company. Inv: 51 - Cash, Profit : Rs. 900', 900, '2024-10-20', '10:50:57', 1),
(126, 'Invoice - Cash In', '51 - Cash, Payment Method : Cash, Advance : Rs. 1500', 1500, '2024-10-20', '10:50:57', 1),
(127, 'Invoice - Company Profit', 'Profit to Company. Inv: 52 - Cash, Profit : Rs. 370', 370, '2024-10-20', '10:52:01', 1),
(128, 'Invoice - Cash In', '52 - Cash, Payment Method : Cash, Advance : Rs. 800', 800, '2024-10-20', '10:52:01', 1),
(129, 'Invoice - Company Profit', 'Profit to Company. Inv: 53 - Cash, Profit : Rs. 2000', 2000, '2024-10-20', '11:20:37', 1),
(130, 'Invoice - Cash In', '53 - Cash, Payment Method : Cash, Advance : Rs. 4500', 4500, '2024-10-20', '11:20:37', 1),
(131, 'Invoice - Company Profit', 'Profit to Company. Inv: 54 - Cash, Profit : Rs. 400', 400, '2024-10-20', '11:25:28', 1),
(132, 'Invoice - Cash In', '54 - Cash, Payment Method : Cash, Advance : Rs. 650', 650, '2024-10-20', '11:25:28', 1),
(133, 'Invoice - Company Profit', 'Profit to Company. Inv: 55 - Cash, Profit : Rs. 270', 270, '2024-10-20', '15:02:57', 1),
(134, 'Invoice - Cash In', '55 - Cash, Payment Method : Cash, Advance : Rs. 500', 500, '2024-10-20', '15:02:57', 1),
(135, 'Invoice - Company Profit', 'Profit to Company. Inv: 56 - Cash, Profit : Rs. 750', 750, '2024-10-20', '15:22:23', 1),
(136, 'Invoice - Cash In', '56 - Cash, Payment Method : Cash, Advance : Rs. 2000', 2000, '2024-10-20', '15:22:23', 1),
(137, 'Invoice - Company Profit', 'Profit to Company. Inv: 57 - Cash, Profit : Rs. 2300', 2300, '2024-10-20', '18:23:16', 1),
(138, 'Invoice - Cash In', '57 - Cash, Payment Method : Cash, Advance : Rs. 3750', 3750, '2024-10-20', '18:23:16', 1),
(139, 'Invoice - Company Profit', 'Profit to Company. Inv: 58 - Cash, Profit : Rs. 400', 400, '2024-10-21', '15:24:16', 1),
(140, 'Invoice - Cash In', '58 - Cash, Payment Method : Cash, Advance : Rs. 800', 800, '2024-10-21', '15:24:16', 1),
(141, 'Invoice - Company Profit', 'Profit to Company. Inv: 59 - Cash, Profit : Rs. 400', 400, '2024-10-21', '16:59:30', 1),
(142, 'Invoice - Cash In', '59 - Cash, Payment Method : Cash, Advance : Rs. 800', 800, '2024-10-21', '16:59:30', 1),
(143, 'Invoice - Company Profit', 'Profit to Company. Inv: 60 - Cash, Profit : Rs. 1000', 1000, '2024-10-21', '21:17:00', 1),
(144, 'Invoice - Cash In', '60 - Cash, Payment Method : Cash, Advance : Rs. 2400', 2400, '2024-10-21', '21:17:00', 1),
(145, 'Invoice - Company Profit', 'Profit to Company. Inv: 61 - Cash, Profit : Rs. 3100', 3100, '2024-10-23', '11:25:57', 1),
(146, 'Invoice - Cash In', '61 - Cash, Payment Method : Cash, Advance : Rs. 5500', 5500, '2024-10-23', '11:25:57', 1),
(147, 'Invoice - Company Profit', 'Profit to Company. Inv: 62 - Cash, Profit : Rs. 1850', 1850, '2024-10-24', '19:31:52', 1),
(148, 'Invoice - Cash In', '62 - Cash, Payment Method : Cash, Advance : Rs. 5500', 5500, '2024-10-24', '19:31:52', 1),
(149, 'Invoice - Company Profit', 'Profit to Company. Inv: 63 - Cash, Profit : Rs. 270', 270, '2024-10-25', '10:28:04', 1),
(150, 'Invoice - Cash In', '63 - Cash, Payment Method : Cash, Advance : Rs. 500', 500, '2024-10-25', '10:28:04', 1),
(151, 'Invoice - Company Profit', 'Profit to Company. Inv: 64 - Cash, Profit : Rs. 2200', 2200, '2024-10-25', '10:36:40', 1),
(152, 'Invoice - Cash In', '64 - Cash, Payment Method : Cash, Advance : Rs. 5500', 5500, '2024-10-25', '10:36:40', 1),
(153, 'Invoice - Company Profit', 'Profit to Company. Inv: 65 - Cash, Profit : Rs. 1070', 1070, '2024-10-25', '10:44:27', 1),
(154, 'Invoice - Cash In', '65 - Cash, Payment Method : Cash, Advance : Rs. 1300', 1300, '2024-10-25', '10:44:27', 1),
(155, 'Invoice - Company Profit', 'Profit to Company. Inv: 66 - Cash, Profit : Rs. 1020', 1020, '2024-10-25', '11:05:45', 1),
(156, 'Invoice - Cash In', '66 - Cash, Payment Method : Cash, Advance : Rs. 1700', 1700, '2024-10-25', '11:05:45', 1),
(157, 'Invoice - Company Profit', 'Profit to Company. Inv: 67 - Cash, Profit : Rs. 450', 450, '2024-10-25', '11:10:00', 1),
(158, 'Invoice - Cash In', '67 - Cash, Payment Method : Cash, Advance : Rs. 700', 700, '2024-10-25', '11:10:00', 1),
(159, 'Invoice - Company Profit', 'Profit to Company. Inv: 68 - Cash, Profit : Rs. 1750', 1750, '2024-10-25', '11:13:38', 1),
(160, 'Invoice - Cash In', '68 - Cash, Payment Method : Cash, Advance : Rs. 5300', 5300, '2024-10-25', '11:13:38', 1),
(161, 'Petty Cash', 'sig', -500, '2024-10-25', '11:24:03', 1),
(162, 'Invoice - Company Profit', 'Profit to Company. Inv: 69 - Cash, Profit : Rs. 300', 300, '2024-10-25', '11:25:14', 1),
(163, 'Invoice - Cash In', '69 - Cash, Payment Method : Cash, Advance : Rs. 500', 500, '2024-10-25', '11:25:14', 1),
(164, 'Invoice - Company Profit', 'Profit to Company. Inv: 70 - Cash, Profit : Rs. 199', 199, '2024-10-25', '11:33:36', 1),
(165, 'Invoice - Cash In', '70 - Cash, Payment Method : Cash, Advance : Rs. 400', 400, '2024-10-25', '11:33:36', 1),
(166, 'Petty Cash', 'used phone', -5000, '2024-10-25', '11:44:30', 1),
(167, 'Fall OneTimeProduct Cost from Company Profit', 'Fall Rs.0 in Company Profit Account for Repair/Service ID : 3', 0, '2024-10-25', '12:22:27', 1),
(168, 'Add Salary Commission', 'Increase Salary of lakmal for M02 back cover by Rs.0', 0, '2024-10-25', '12:22:27', 1),
(169, 'Invoice - Company Profit', 'Profit to Company. Inv: 71 - Cash, Profit : Rs. 1299', 1299, '2024-10-25', '12:22:52', 1),
(170, 'Invoice - Cash In', '71 - Cash, Payment Method : Cash, Advance : Rs. 1500', 1500, '2024-10-25', '12:22:52', 1),
(171, 'Invoice - Company Profit', 'Profit to Company. Inv: 72 - Cash, Profit : Rs. 1050', 1050, '2024-10-25', '12:51:29', 1),
(172, 'Invoice - Cash In', '72 - Cash, Payment Method : Cash, Advance : Rs. 2500', 2500, '2024-10-25', '12:51:29', 1),
(173, 'Invoice - Company Profit', 'Profit to Company. Inv: 73 - Cash, Profit : Rs. 1650', 1650, '2024-10-25', '13:05:45', 1),
(174, 'Invoice - Cash In', '73 - Cash, Payment Method : Cash, Advance : Rs. 5100', 5100, '2024-10-25', '13:05:45', 1),
(175, 'Petty Cash', 'colombo', -25000, '2024-10-25', '13:46:32', 1),
(176, 'Invoice - Company Profit', 'Profit to Company. Inv: 74 - Cash, Profit : Rs. 540', 540, '2024-10-26', '09:56:46', 1),
(177, 'Invoice - Cash In', '74 - Cash, Payment Method : Cash, Advance : Rs. 1000', 1000, '2024-10-26', '09:56:46', 1),
(178, 'Invoice - Company Profit', 'Profit to Company. Inv: 75 - Cash, Profit : Rs. 650', 650, '2024-10-26', '10:44:13', 1),
(179, 'Invoice - Cash In', '75 - Cash, Payment Method : Cash, Advance : Rs. 850', 850, '2024-10-26', '10:44:13', 1);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `action_log`
--
ALTER TABLE `action_log`
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1135;

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
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `invoice_number` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

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
-- AUTO_INCREMENT for table `makeProduct`
--
ALTER TABLE `makeProduct`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `oneTimeProducts_sales`
--
ALTER TABLE `oneTimeProducts_sales`
  MODIFY `oneTimeProduct_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pettycash`
--
ALTER TABLE `pettycash`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=502;

--
-- AUTO_INCREMENT for table `purchase`
--
ALTER TABLE `purchase`
  MODIFY `purchase_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `repair_categories`
--
ALTER TABLE `repair_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `repair_items`
--
ALTER TABLE `repair_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `repair_sell_records`
--
ALTER TABLE `repair_sell_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `repair_stock`
--
ALTER TABLE `repair_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `repair_stock_map`
--
ALTER TABLE `repair_stock_map`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `salary`
--
ALTER TABLE `salary`
  MODIFY `salary_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sales_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

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
  MODIFY `transaction_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=180;

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

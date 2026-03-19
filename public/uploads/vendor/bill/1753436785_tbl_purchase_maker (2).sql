-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 19, 2025 at 06:51 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `drar_hms`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_purchase_maker`
--

CREATE TABLE `tbl_purchase_maker` (
  `id` int(11) NOT NULL,
  `serial_number` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_by` varchar(255) NOT NULL,
  `branch_id` varchar(11) NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `nature_payment` varchar(255) NOT NULL,
  `payment_status` varchar(255) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `utr_number` varchar(255) NOT NULL,
  `pan_number` varchar(255) NOT NULL,
  `pan_upload` varchar(255) NOT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `ifsc_code` varchar(255) NOT NULL,
  `invoice_upload` varchar(255) NOT NULL,
  `invoice_number` int(11) NOT NULL,
  `invoice_amount` int(11) NOT NULL,
  `bank_upload` varchar(255) NOT NULL,
  `already_paid` int(11) DEFAULT NULL,
  `po_upload` varchar(255) NOT NULL,
  `po_signed_upload` varchar(255) NOT NULL,
  `po_delivery_upload` varchar(255) NOT NULL,
  `checker_status` int(11) NOT NULL DEFAULT 0,
  `approval_status` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbl_purchase_maker`
--

INSERT INTO `tbl_purchase_maker` (`id`, `serial_number`, `user_id`, `created_by`, `branch_id`, `vendor`, `nature_payment`, `payment_status`, `payment_method`, `utr_number`, `pan_number`, `pan_upload`, `account_number`, `ifsc_code`, `invoice_upload`, `invoice_number`, `invoice_amount`, `bank_upload`, `already_paid`, `po_upload`, `po_signed_upload`, `po_delivery_upload`, `checker_status`, `approval_status`, `created_at`, `updated_at`) VALUES
(1, 'REC-001', 1, 'Zonal Head', '1', 'Vendor', 'Travell Allowance', 'Return', 'DD,IDhS', '884564855684', 'ABCDE1234F', '[\"1752471277_circular-030425151728.pdf\",\"1752471277_payslip-180325155519.pdf\"]', '2147483647', 'SBIN0001234', '[\"1752306789_screenshot-2025-07-11-182852.png\"]', 50000, 2000, '[\"1752308661_photo-6305159540290734627-y.jpg\"]', 1000, '[\"1752306789_vendor-payments-file-detailed-2page-1.pdf\"]', '[\"1752306789_whatsapp-image-2025-06-26-at-120622.png\"]', '[\"1752306789_6300638709254571013.jpg\"]', 0, 0, '2025-07-15 11:58:22', '2025-07-15 11:58:22'),
(3, 'REC-002', 1, 'Aravind', '2', 'Employee', 'Expense', 'Success', 'NEFT,RTGS', '1234567890', 'ABCDE1234F', '[\"1752480973_vendor-payments-file-detailed-2page.pdf\",\"1752480973_6300638709254571013.jpg\"]', '152462358710', 'SBIN0001234', '[\"1752480973_whatsapp-image-2025-06-26-at-120622.jpeg\",\"1752480973_circular-030425151728.pdf\"]', 0, 2000, '[\"1752480973_photo-6305159540290734627-y.jpg\"]', 1000, '[\"1752480973_circular-030425151728.pdf\",\"1752480973_payslip-180325155519.pdf\"]', '[\"1752480973_whatsapp-image-2025-06-26-at-120622.jpeg\"]', '[\"1752480973_vendor-payments-file-detailed-2page-1.pdf\",\"1752480973_photo-6305159540290734627-y.jpg\",\"1752480973_vendor-payments-file-detailed-2page.pdf\"]', 1, 1, '2025-07-14 13:57:28', '2025-07-14 14:44:37'),
(5, 'REC-003', 11, 'P.Kathirvel', '1', 'Employee', 'Expense', 'Return', 'RTGS,Cheque,DD,IDhS', '789654789654', 'ABCDE1234F', '[\"1752571884_employee-details.pdf\"]', '1254525552000', 'SBIN0001234', '[\"1752571884_photo-6305159540290734627-y.jpg\"]', 0, 100, '[\"1752571884_vendor-payments-file-detailed-2page.pdf\"]', 20, '[\"1752571884_whatsapp-image-2025-06-26-at-120622.png\"]', '[\"1752571884_whatsapp-image-2025-06-26-at-120622.png\"]', '[\"1752571884_circular-030425151728.pdf\"]', 0, 0, '2025-07-15 18:10:53', '2025-07-15 18:10:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_purchase_maker`
--
ALTER TABLE `tbl_purchase_maker`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_purchase_maker`
--
ALTER TABLE `tbl_purchase_maker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

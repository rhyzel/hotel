-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 04, 2025 at 03:18 PM
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
-- Database: `hotel`
--

-- --------------------------------------------------------

--
-- Table structure for table `grn`
--

CREATE TABLE `grn` (
  `grn_id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `po_number` varchar(50) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `category` enum('Hotel Supplies','Foods & Beverages','Cleaning & Sanitation','Utility Products','Office Supplies','Kitchen Equipment','Furniture & Fixtures','Laundry & Linen','Others') DEFAULT 'Others',
  `quantity_received` int(11) NOT NULL,
  `condition_status` varchar(50) DEFAULT 'Good',
  `inspected_by` varchar(100) NOT NULL,
  `date_received` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grn`
--

INSERT INTO `grn` (`grn_id`, `po_id`, `po_number`, `item_name`, `category`, `quantity_received`, `condition_status`, `inspected_by`, `date_received`) VALUES
(36, 45, 'PO-1005', 'tomato', 'Foods & Beverages', 1, 'Good', 'Kai Dela Cruz', '2025-09-04 21:04:22'),
(37, 46, 'PO-1006', 'san miguel beer', 'Foods & Beverages', 50000, 'Good', 'Kai Dela Cruz', '2025-09-04 21:07:58');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `category` enum('Hotel Supplies','Foods & Beverages','Cleaning & Sanitation','Utility Products','Office Supplies','Kitchen Equipment','Furniture & Fixtures','Laundry & Linen','Others') DEFAULT 'Others',
  `quantity_in_stock` int(11) NOT NULL DEFAULT 0,
  `used_qty` int(11) NOT NULL DEFAULT 0,
  `wasted_qty` int(11) NOT NULL DEFAULT 0,
  `unit_price` decimal(10,2) DEFAULT 0.00,
  `inspected_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`item_id`, `item_name`, `category`, `quantity_in_stock`, `used_qty`, `wasted_qty`, `unit_price`, `inspected_by`) VALUES
(140, 'san miguel beer', 'Foods & Beverages', 49500, 500, 0, 2500000.00, 'Kai Dela Cruz');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `po_id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `po_number` varchar(50) DEFAULT NULL,
  `item_name` varchar(100) DEFAULT NULL,
  `order_date` date NOT NULL,
  `status` enum('pending','approved','received','cancelled') DEFAULT 'pending',
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `category` varchar(100) DEFAULT '',
  `quantity` int(11) NOT NULL DEFAULT 1,
  `received_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`po_id`, `supplier_id`, `po_number`, `item_name`, `order_date`, `status`, `total_amount`, `category`, `quantity`, `received_date`) VALUES
(45, 1, 'PO-1005', 'tomato', '2025-09-04', 'received', 32.00, 'Foods & Beverages', 1, '2025-09-04 21:04:22'),
(46, 4, 'PO-1006', 'san miguel beer', '2025-09-04', 'received', 2500000.00, 'Foods & Beverages', 50000, '2025-09-04 21:07:58');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `position_id` int(11) DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `hire_date` date NOT NULL,
  `employment_status` enum('active','resigned','terminated') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `position_id`, `first_name`, `last_name`, `email`, `phone`, `address`, `hire_date`, `employment_status`, `created_at`) VALUES
(1, 1, 'Kai', 'Dela Cruz', 'kai.delacruz@example.com', '09171234567', 'Makati, Philippines', '2025-08-27', 'active', '2025-08-26 23:35:54'),
(2, 2, 'Mika', 'Reyes', 'mika.reyes@example.com', '09179876543', 'Quezon City, Philippines', '2025-08-27', 'active', '2025-08-26 23:35:54'),
(3, 1, 'Lio', 'Santos', 'lio.santos@example.com', '09175678901', 'Cebu City, Philippines', '2025-08-27', 'active', '2025-08-26 23:35:54');

-- --------------------------------------------------------

--
-- Table structure for table `stock_usage`
--

CREATE TABLE `stock_usage` (
  `usage_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `used_qty` int(11) NOT NULL,
  `used_by` varchar(100) NOT NULL,
  `date_used` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_usage`
--

INSERT INTO `stock_usage` (`usage_id`, `item_id`, `used_qty`, `used_by`, `date_used`) VALUES
(2, 131, 3, 'biancs', '2025-09-04 20:09:47'),
(3, 140, 500, 'cheska', '2025-09-04 21:08:49');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(100) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `category` enum('Hotel Supplies','Foods & Beverages','Cleaning & Sanitation','Utility Products','Office Supplies','Kitchen Equipment','Furniture & Fixtures','Laundry & Linen','Others') DEFAULT 'Others'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `contact_person`, `phone`, `email`, `address`, `created_at`, `is_active`, `category`) VALUES
(1, 'Sikat Tech Supplies', 'Juan Dela Cruz', '09171234567', 'juan@sikattech.ph', '123 Mabini St, Manila', '2025-08-27 06:17:45', 1, 'Hotel Supplies'),
(2, 'PaperWorld PH', 'Ana Reyes', '09182345678', 'ana@paperworld.ph', '45 Rizal Ave, Quezon City', '2025-08-27 06:17:45', 1, 'Hotel Supplies'),
(3, 'Casa Moderno', 'Marco Santos', '09183456789', 'marco@gmail.com', '78 Bonifacio Blvd, Makati', '2025-08-27 06:17:45', 0, 'Furniture & Fixtures'),
(4, 'FreshMart PH', 'Liza Mendoza', '09184567890', 'liza@freshmart.ph', '12 Edsa, Mandaluyong', '2025-08-27 06:17:45', 1, 'Foods & Beverages'),
(5, 'BIZ Office Depot', 'Rico Tan', '09185678901', 'rico@bizoffice.ph', '34 Ortigas Ave, Pasig', '2025-08-27 06:17:45', 0, 'Office Supplies'),
(6, 'Kusina King', 'Maya Santos', '09186789012', 'maya@kusinaking.ph', '56 Aguinaldo St, Quezon City', '2025-08-27 06:17:45', 0, 'Foods & Beverages'),
(7, 'Restaurante Risa', 'Carlos Villanueva', '09187890123', 'carlos@risa.ph', '90 Shaw Blvd, Mandaluyong', '2025-08-27 06:17:45', 1, 'Foods & Beverages'),
(8, 'sads', 'dasd', '', '', '', '2025-09-03 12:17:32', 0, 'Laundry & Linen'),
(9, 'okkoui', 'we', '09941813832', 'oouio@gmail.com', 'oioi', '2025-09-03 12:18:59', 1, 'Furniture & Fixtures'),
(10, 'avril ltd', 'avril lavigne', '09941813832', 'avril@gmail.com', 'dddd', '2025-09-04 04:20:17', 0, 'Utility Products');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `grn`
--
ALTER TABLE `grn`
  ADD PRIMARY KEY (`grn_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`po_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `stock_usage`
--
ALTER TABLE `stock_usage`
  ADD PRIMARY KEY (`usage_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `grn`
--
ALTER TABLE `grn`
  MODIFY `grn_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `po_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `stock_usage`
--
ALTER TABLE `stock_usage`
  MODIFY `usage_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

ALTER TABLE purchase_orders
ADD COLUMN received_date DATETIME NULL;


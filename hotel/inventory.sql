-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 08, 2025 at 07:37 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
  `item_name` varchar(255) NOT NULL,
  `category` enum('Hotel Supplies','Foods & Beverages','Cleaning & Sanitation','Utility Products','Office Supplies','Kitchen Equipment','Furniture & Fixtures','Laundry & Linen','Others') NOT NULL,
  `quantity_received` int(11) NOT NULL,
  `inspected_by` varchar(255) NOT NULL,
  `condition_status` enum('Good','Damaged','Expired') NOT NULL,
  `notes` text DEFAULT NULL,
  `date_received` datetime DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grn`
--

INSERT INTO `grn` (`grn_id`, `po_id`, `po_number`, `item_name`, `category`, `quantity_received`, `inspected_by`, `condition_status`, `notes`, `date_received`, `created_at`, `status`) VALUES
(1, 5, 'PO-20241202-002', 'Premium Coffee Beans', 'Foods & Beverages', 10, 'Carol Davis', 'Damaged', '', '2025-09-07 20:03:07', '2025-09-08 03:03:07', NULL),
(2, 9, 'PO-20241204-001', 'A4 Copy Paper (Ream)', 'Office Supplies', 20, 'Carol Davis', 'Damaged', '', '2025-09-07 20:06:18', '2025-09-08 03:06:18', NULL),
(3, 7, 'PO-20241203-001', 'All-Purpose Cleaner', 'Cleaning & Sanitation', 25, 'Bob Smith', 'Damaged', '', '2025-09-07 20:08:59', '2025-09-08 03:08:59', NULL),
(4, 4, 'PO-20241202-001', 'Fresh Vegetables Mix', 'Foods & Beverages', 20, 'Daniel Brown', 'Good', 'qwewqe', '2025-09-07 20:17:35', '2025-09-08 03:17:35', NULL),
(5, 6, 'PO-20241202-003', 'Bottled Water (24-pack)', 'Foods & Beverages', 15, 'Emma Wilson', 'Damaged', '', '2025-09-07 20:49:24', '2025-09-08 03:49:24', NULL),
(7, 10, 'PO-20241204-002', 'Chef Knives Set', 'Kitchen Equipment', 5, 'Carol Davis', 'Good', '', '2025-09-07 21:19:50', '2025-09-08 04:19:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `category` enum('Hotel Supplies','Foods & Beverages','Cleaning & Sanitation','Utility Products','Office Supplies','Kitchen Equipment','Furniture & Fixtures','Laundry & Linen','Others') NOT NULL,
  `quantity_in_stock` int(11) NOT NULL DEFAULT 0,
  `used_qty` int(11) DEFAULT 0,
  `wasted_qty` int(11) DEFAULT 0,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `inspected_by` varchar(255) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`item_id`, `item_name`, `category`, `quantity_in_stock`, `used_qty`, `wasted_qty`, `unit_price`, `inspected_by`, `last_updated`, `created_at`) VALUES
(1, 'Bed Sheets (King)', 'Hotel Supplies', 75, 25, 2, 30.00, 'Alice Johnson', '2025-09-08 02:53:32', '2025-09-08 02:53:32'),
(2, 'Towels (Hand)', 'Hotel Supplies', 200, 50, 5, 8.00, 'Alice Johnson', '2025-09-08 02:53:32', '2025-09-08 02:53:32'),
(3, 'Bathroom Amenities Kit', 'Hotel Supplies', 150, 75, 3, 12.50, 'Bob Smith', '2025-09-08 02:53:32', '2025-09-08 02:53:32'),
(4, 'Mineral Water (500ml)', 'Foods & Beverages', 500, 200, 10, 1.50, 'Carol Davis', '2025-09-08 02:53:32', '2025-09-08 02:53:32'),
(5, 'Ground Coffee', 'Foods & Beverages', 25, 15, 1, 35.00, 'Carol Davis', '2025-09-08 02:53:32', '2025-09-08 02:53:32'),
(6, 'Floor Cleaner', 'Cleaning & Sanitation', 30, 8, 0, 15.00, 'Daniel Brown', '2025-09-08 02:53:32', '2025-09-08 02:53:32'),
(7, 'Toilet Paper (12-roll)', 'Cleaning & Sanitation', 40, 20, 1, 18.00, 'Daniel Brown', '2025-09-08 02:53:32', '2025-09-08 02:53:32'),
(8, 'Printer Paper (A4)', 'Office Supplies', 50, 25, 2, 6.50, 'Emma Wilson', '2025-09-08 02:53:32', '2025-09-08 02:53:32'),
(9, 'Pens (Blue)', 'Office Supplies', 100, 40, 5, 0.75, 'Emma Wilson', '2025-09-08 02:53:32', '2025-09-08 02:53:32'),
(10, 'Mixing Bowls Set', 'Kitchen Equipment', 8, 2, 0, 25.00, 'Frank Miller', '2025-09-08 02:53:32', '2025-09-08 02:53:32'),
(11, 'Premium Coffee Beans', 'Foods & Beverages', 10, 0, 0, 45.00, 'Carol Davis', '2025-09-08 03:03:07', '2025-09-08 03:03:07'),
(12, 'A4 Copy Paper (Ream)', 'Office Supplies', 20, 0, 0, 6.50, 'Carol Davis', '2025-09-08 03:06:18', '2025-09-08 03:06:18'),
(13, 'All-Purpose Cleaner', 'Cleaning & Sanitation', 24, 1, 0, 8.50, 'Bob Smith', '2025-09-08 04:48:30', '2025-09-08 03:08:59'),
(14, 'Fresh Vegetables Mix', 'Foods & Beverages', 20, 0, 0, 12.50, 'Daniel Brown', '2025-09-08 03:17:35', '2025-09-08 03:17:35'),
(15, 'Bottled Water (24-pack)', 'Foods & Beverages', 15, 0, 0, 8.00, 'Emma Wilson', '2025-09-08 03:49:24', '2025-09-08 03:49:24'),
(17, 'Chef Knives Set', 'Kitchen Equipment', 5, 0, 0, 120.00, 'Carol Davis', '2025-09-08 04:19:50', '2025-09-08 04:19:50');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `po_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `po_number` varchar(50) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `category` enum('Hotel Supplies','Foods & Beverages','Cleaning & Sanitation','Utility Products','Office Supplies','Kitchen Equipment','Furniture & Fixtures','Laundry & Linen','Others') NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` date NOT NULL,
  `status` enum('pending','received','partially_received','cancelled') DEFAULT 'pending',
  `received_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `condition_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`po_id`, `supplier_id`, `po_number`, `item_name`, `category`, `quantity`, `unit_price`, `total_amount`, `order_date`, `status`, `received_date`, `created_at`, `updated_at`, `condition_status`) VALUES
(1, 1, 'PO-20241201-001', 'Bed Sheets (Queen)', 'Hotel Supplies', 50, 25.00, 1250.00, '2024-12-01', 'pending', NULL, '2025-09-08 02:53:32', '2025-09-08 02:53:32', NULL),
(2, 1, 'PO-20241201-002', 'Towels (Bath)', 'Hotel Supplies', 100, 15.00, 1500.00, '2024-12-01', 'pending', NULL, '2025-09-08 02:53:32', '2025-09-08 02:53:32', NULL),
(3, 1, 'PO-20241201-003', 'Pillows', 'Hotel Supplies', 30, 35.00, 1050.00, '2024-12-02', 'pending', NULL, '2025-09-08 02:53:32', '2025-09-08 02:53:32', NULL),
(4, 2, 'PO-20241202-001', 'Fresh Vegetables Mix', 'Foods & Beverages', 20, 12.50, 250.00, '2024-12-02', 'received', '2025-09-07 20:17:35', '2025-09-08 02:53:32', '2025-09-08 03:17:35', NULL),
(5, 2, 'PO-20241202-002', 'Premium Coffee Beans', 'Foods & Beverages', 10, 45.00, 450.00, '2024-12-02', 'received', '2025-09-07 20:03:07', '2025-09-08 02:53:32', '2025-09-08 03:03:07', NULL),
(6, 2, 'PO-20241202-003', 'Bottled Water (24-pack)', 'Foods & Beverages', 15, 8.00, 120.00, '2024-12-03', 'received', '2025-09-07 20:49:24', '2025-09-08 02:53:32', '2025-09-08 03:49:24', 'Damaged'),
(7, 3, 'PO-20241203-001', 'All-Purpose Cleaner', 'Cleaning & Sanitation', 25, 8.50, 212.50, '2024-12-03', 'received', '2025-09-07 20:08:59', '2025-09-08 02:53:32', '2025-09-08 03:08:59', NULL),
(8, 3, 'PO-20241203-002', 'Disinfectant Spray', 'Cleaning & Sanitation', 40, 12.00, 480.00, '2024-12-03', 'pending', NULL, '2025-09-08 02:53:32', '2025-09-08 02:53:32', NULL),
(9, 4, 'PO-20241204-001', 'A4 Copy Paper (Ream)', 'Office Supplies', 20, 6.50, 130.00, '2024-12-04', 'received', '2025-09-07 20:06:18', '2025-09-08 02:53:32', '2025-09-08 03:06:18', NULL),
(10, 5, 'PO-20241204-002', 'Chef Knives Set', 'Kitchen Equipment', 5, 120.00, 600.00, '2024-12-04', 'received', '2025-09-07 21:19:50', '2025-09-08 02:53:32', '2025-09-08 04:19:50', 'Good');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `position` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `first_name`, `last_name`, `position`, `department`, `email`, `phone`, `created_at`, `updated_at`) VALUES
(2, 'Bob', 'Smith', 'Warehouse Supervisor', 'Operations', 'bob.smith@hotel.com', '+1-555-1002', '2025-09-08 02:53:32', '2025-09-08 02:53:32'),
(3, 'Carol', 'Davis', 'Purchasing Officer', 'Procurement', 'carol.davis@hotel.com', '+1-555-1003', '2025-09-08 02:53:32', '2025-09-08 02:53:32'),
(4, 'Daniel', 'Brown', 'Quality Inspector', 'Operations', 'daniel.brown@hotel.com', '+1-555-1004', '2025-09-08 02:53:32', '2025-09-08 02:53:32'),
(5, 'Emma', 'Wilson', 'Assistant Manager', 'Operations', 'emma.wilson@hotel.com', '+1-555-1005', '2025-09-08 02:53:32', '2025-09-08 02:53:32'),
(6, 'Frank', 'Miller', 'Store Keeper', 'Operations', 'frank.miller@hotel.com', '+1-555-1006', '2025-09-08 02:53:32', '2025-09-08 02:53:32');

-- --------------------------------------------------------

--
-- Table structure for table `stock_usage`
--

CREATE TABLE `stock_usage` (
  `usage_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `used_qty` int(11) NOT NULL,
  `used_by` varchar(100) NOT NULL,
  `date_used` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `contact_person`, `email`, `phone`, `address`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Hotel Supply Co.', 'John Smith', 'john@hotelsupply.com', '+1-555-0101', '123 Supply Street, Business District', 1, '2025-09-08 02:53:31', '2025-09-08 02:53:31'),
(2, 'Fresh Food Distributors', 'Maria Garcia', 'maria@freshfood.com', '+1-555-0102', '456 Market Avenue, Food District', 1, '2025-09-08 02:53:31', '2025-09-08 02:53:31'),
(3, 'Clean Pro Services', 'David Johnson', 'david@cleanpro.com', '+1-555-0103', '789 Service Road, Industrial Area', 1, '2025-09-08 02:53:31', '2025-09-08 02:53:31'),
(4, 'Office Essentials Ltd', 'Sarah Wilson', 'sarah@officeessentials.com', '+1-555-0104', '321 Office Plaza, Downtown', 1, '2025-09-08 02:53:31', '2025-09-08 02:53:31'),
(5, 'Kitchen Masters Inc', 'Mike Chen', 'mike@kitchenmasters.com', '+1-555-0105', '654 Culinary Street, Restaurant District', 1, '2025-09-08 02:53:31', '2025-09-08 02:53:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `grn`
--
ALTER TABLE `grn`
  ADD PRIMARY KEY (`grn_id`),
  ADD KEY `idx_po_id` (`po_id`),
  ADD KEY `idx_date_received` (`date_received`),
  ADD KEY `idx_condition` (`condition_status`),
  ADD KEY `idx_inspector` (`inspected_by`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `idx_item_name` (`item_name`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_stock_level` (`quantity_in_stock`),
  ADD KEY `idx_item_category` (`item_name`,`category`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`po_id`),
  ADD UNIQUE KEY `po_number` (`po_number`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_order_date` (`order_date`),
  ADD KEY `idx_po_number` (`po_number`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD KEY `idx_name` (`first_name`,`last_name`);

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
  MODIFY `grn_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `po_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `stock_usage`
--
ALTER TABLE `stock_usage`
  MODIFY `usage_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `grn`
--
ALTER TABLE `grn`
  ADD CONSTRAINT `grn_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

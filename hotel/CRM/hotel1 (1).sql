-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 23, 2025 at 08:00 AM
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
-- Database: `hotel1`
--

-- --------------------------------------------------------

--
-- Table structure for table `billings`
--

CREATE TABLE `billings` (
  `billing_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `billing_type` enum('restaurant','room_service','minibar','other') DEFAULT 'restaurant',
  `total_amount` decimal(10,2) NOT NULL,
  `billing_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','paid','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `billing_items`
--

CREATE TABLE `billing_items` (
  `id` int(11) NOT NULL,
  `billing_id` int(11) NOT NULL,
  `item_name` varchar(150) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `guest_name` varchar(255) NOT NULL,
  `type` enum('review','service_feedback') DEFAULT 'review',
  `rating` int(1) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `message` text NOT NULL,
  `comment` text GENERATED ALWAYS AS (`message`) STORED,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `reply` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `folio`
--

CREATE TABLE `folio` (
  `folio_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `guest_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `first_phone` varchar(20) DEFAULT NULL,
  `second_phone` varchar(20) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `loyalty_tier` enum('bronze','silver','gold','platinum') NOT NULL DEFAULT 'bronze',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guests`
--

INSERT INTO `guests` (`guest_id`, `first_name`, `last_name`, `email`, `first_phone`, `second_phone`, `status`, `loyalty_tier`, `created_at`, `updated_at`) VALUES
(1, 'Michael', 'Thompson', 'michael.thompson@email.com', '+63-917-111-2222', '+63-918-333-4444', 'active', 'bronze', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(2, 'Jessica', 'Martinez', 'jessica.martinez@email.com', '+63-919-555-6666', NULL, 'active', 'bronze', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(3, 'David', 'Lee', 'david.lee@email.com', '+63-920-777-8888', '+63-921-999-0000', 'active', 'bronze', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(4, 'Sophie', 'Brown', 'sophie.brown@email.com', '+63-917-222-3333', NULL, 'active', 'bronze', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(5, 'Carlos', 'Garcia', 'carlos.garcia@email.com', '+63-918-444-5555', '+63-919-666-7777', 'active', 'bronze', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(6, 'Emily', 'Wilson', 'emily.wilson@email.com', '+63-920-888-9999', NULL, 'active', 'bronze', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(7, 'James', 'Anderson', 'james.anderson@email.com', '+63-917-123-9876', NULL, 'active', 'bronze', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(8, 'Lisa', 'Taylor', 'lisa.taylor@email.com', '+63-918-987-6543', '+63-919-456-7890', 'active', 'bronze', '2025-09-09 08:42:18', '2025-09-09 08:42:18');

--
-- Triggers `guests`
--
DELIMITER $$
CREATE TRIGGER `guests_before_update` BEFORE UPDATE ON `guests` FOR EACH ROW BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

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
(1, 'Toilet Paper Rolls', 'Hotel Supplies', 150, 25, 2, 45.00, 'Grace Santos', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(2, 'Bath Towels', 'Laundry & Linen', 75, 15, 1, 350.00, 'Grace Santos', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(3, 'Coffee Beans (1kg)', 'Foods & Beverages', 20, 8, 0, 580.00, 'Linda Cruz', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(4, 'All-Purpose Cleaner', 'Cleaning & Sanitation', 45, 12, 1, 125.00, 'Tony Mendoza', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(5, 'Printer Paper (Ream)', 'Office Supplies', 30, 5, 0, 250.00, 'Mark Rivera', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(6, 'Bed Sheets (Queen)', 'Laundry & Linen', 40, 8, 2, 450.00, 'Grace Santos', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(7, 'Hand Soap Dispensers', 'Cleaning & Sanitation', 25, 3, 0, 180.00, 'Grace Santos', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(8, 'Light Bulbs (LED)', 'Utility Products', 60, 15, 3, 95.00, 'Tony Mendoza', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(9, 'Shampoo Bottles', 'Hotel Supplies', 80, 20, 1, 85.00, 'Grace Santos', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(10, 'Kitchen Knives Set', 'Kitchen Equipment', 12, 0, 0, 1250.00, 'Tony Mendoza', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(11, 'Vacuum Cleaner Bags', 'Cleaning & Sanitation', 35, 8, 0, 75.00, 'Grace Santos', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(12, 'Pillows', 'Laundry & Linen', 50, 10, 2, 280.00, 'Grace Santos', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(13, 'Floor Wax', 'Cleaning & Sanitation', 18, 4, 0, 320.00, 'Tony Mendoza', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(14, 'Receipt Rolls', 'Office Supplies', 100, 25, 1, 35.00, 'Linda Cruz', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(15, 'Air Fresheners', 'Hotel Supplies', 42, 8, 0, 65.00, 'Grace Santos', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(16, 'Bottled Water', 'Foods & Beverages', 50, 0, 0, 25.00, 'System', '2025-09-12 02:57:07', '2025-09-12 02:54:35'),
(17, 'Soft Drinks / Soda', 'Foods & Beverages', 40, 0, 0, 45.00, 'System', '2025-09-12 02:57:07', '2025-09-12 02:54:35'),
(18, 'Juice', 'Foods & Beverages', 30, 0, 0, 55.00, 'System', '2025-09-12 02:57:07', '2025-09-12 02:54:35'),
(19, 'Beer (Bottled)', 'Foods & Beverages', 20, 0, 0, 80.00, 'System', '2025-09-12 02:57:07', '2025-09-12 02:54:35'),
(20, 'Wine (Red / White)', 'Foods & Beverages', 60, 0, 0, 350.00, 'System', '2025-09-12 02:57:07', '2025-09-12 02:54:35'),
(21, 'Whiskey / Vodka Shots', 'Foods & Beverages', 25, 0, 0, 120.00, 'System', '2025-09-12 02:57:07', '2025-09-12 02:54:35'),
(22, 'Sparkling Water', 'Foods & Beverages', 20, 0, 0, 60.00, 'System', '2025-09-12 02:57:07', '2025-09-12 02:54:35'),
(23, 'Chips / Crisps', 'Foods & Beverages', 55, 0, 0, 40.00, 'System', '2025-09-12 02:57:07', '2025-09-12 02:54:35'),
(24, 'Nuts / Mixed Nuts', 'Foods & Beverages', 65, 0, 0, 70.00, 'System', '2025-09-12 02:57:07', '2025-09-12 02:54:35'),
(25, 'Chocolate Bar', 'Foods & Beverages', 60, 0, 0, 45.00, 'System', '2025-09-12 02:57:07', '2025-09-12 02:54:35'),
(26, 'Cookies / Biscuits', 'Foods & Beverages', 45, 0, 0, 55.00, 'System', '2025-09-12 02:57:07', '2025-09-12 02:54:35'),
(27, 'Candy / Mints', 'Foods & Beverages', 80, 0, 0, 30.00, 'System', '2025-09-12 02:57:07', '2025-09-12 02:54:35');

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_programs`
--

CREATE TABLE `loyalty_programs` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `tier` enum('bronze','silver','gold','platinum') NOT NULL,
  `points_rate` decimal(3,1) NOT NULL,
  `benefits` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `members_count` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `points_redeemed` int(11) DEFAULT 0,
  `rewards_given` int(11) DEFAULT 0,
  `revenue_impact` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loyalty_programs`
--

INSERT INTO `loyalty_programs` (`id`, `name`, `tier`, `points_rate`, `benefits`, `description`, `members_count`, `status`, `created_at`, `points_redeemed`, `rewards_given`, `revenue_impact`) VALUES
(1, 'Bronze Program', 'bronze', 1.0, 'Basic membership benefits', NULL, 10, 'active', '2025-09-09 17:09:55', 0, 0, 0.00),
(2, 'Silver Program', 'silver', 1.5, 'Extended membership benefits', NULL, 5, 'active', '2025-09-09 17:09:55', 0, 0, 0.00),
(3, 'Gold Program', 'gold', 2.0, 'Premium membership benefits', NULL, 3, 'active', '2025-09-09 17:09:55', 0, 0, 0.00),
(4, 'Platinum Program', 'platinum', 3.0, 'Exclusive membership benefits', NULL, 2, 'active', '2025-09-09 17:09:55', 0, 0, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `minibar_consumption`
--

CREATE TABLE `minibar_consumption` (
  `consumption_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `room_number` varchar(20) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `checked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(7, 2, 'PO-20250909-210', 'bedsheets', 'Laundry & Linen', 50, NULL, 6500.00, '2025-09-09', 'pending', NULL, '2025-09-09 08:45:55', '2025-09-09 08:45:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `status` enum('reserved','checked_in','checked_out','cancelled') NOT NULL DEFAULT 'reserved',
  `reservation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `remarks` text DEFAULT NULL,
  `check_in` datetime NOT NULL,
  `check_out` datetime NOT NULL,
  `extended_duration` varchar(8) DEFAULT '00:00:00',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `actual_checkout` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reward_transactions`
--

CREATE TABLE `reward_transactions` (
  `id` int(11) NOT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL,
  `transaction_type` enum('reward_given','points_redeemed') NOT NULL,
  `reward_type` varchar(100) DEFAULT NULL,
  `reward_value` text DEFAULT NULL,
  `points_amount` int(11) DEFAULT 0,
  `redemption_value` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `max_occupancy` int(11) DEFAULT NULL,
  `price_rate` decimal(10,2) NOT NULL,
  `day` int(11) NOT NULL DEFAULT 1,
  `status` enum('available','occupied','reserved','under maintenance','dirty') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_number`, `room_type`, `max_occupancy`, `price_rate`, `day`, `status`, `created_at`, `updated_at`) VALUES
(1, '101', 'Single Room', 1, 1200.00, 1, 'available', '2025-09-13 02:27:29', '2025-09-13 07:46:15'),
(2, '102', 'Double Room', 2, 2000.00, 1, 'available', '2025-09-13 02:27:29', '2025-09-13 06:19:44'),
(3, '103', 'Twin Room', 2, 2000.00, 1, 'available', '2025-09-13 02:27:29', '2025-09-13 02:37:14'),
(4, '104', 'Deluxe Room', 3, 2500.00, 1, 'available', '2025-09-13 02:27:29', '2025-09-13 02:37:14'),
(5, '105', 'Suite', 4, 3500.00, 1, 'available', '2025-09-13 02:27:29', '2025-09-13 02:37:14'),
(6, '106', 'Family Room', 5, 4200.00, 1, 'available', '2025-09-13 02:27:29', '2025-09-13 02:37:14'),
(7, '107', 'VIP Room', 4, 5200.00, 1, 'available', '2025-09-13 02:27:29', '2025-09-13 21:56:25'),
(8, '108', 'Single Room', 1, 1200.00, 1, 'available', '2025-09-13 02:27:29', '2025-09-13 08:07:12'),
(9, '109', 'Double Room', 2, 2000.00, 1, 'available', '2025-09-13 02:27:29', '2025-09-13 02:37:14'),
(10, '110', 'Deluxe Room', 3, 2500.00, 1, 'available', '2025-09-13 02:27:29', '2025-09-13 02:37:14'),
(11, '201', 'Single Room', 1, 1200.00, 1, 'available', '2025-09-13 02:27:29', '2025-09-13 07:52:26'),
(12, '202', 'Double Room', 2, 2000.00, 1, 'available', '2025-09-13 02:27:29', '2025-09-13 02:37:14'),
(13, '203', 'Twin Room', 2, 2000.00, 1, 'available', '2025-09-13 02:27:29', '2025-09-13 02:37:14'),
(14, '204', 'Deluxe Room', 3, 2500.00, 1, 'available', '2025-09-13 02:27:29', '2025-09-13 02:37:14'),
(15, '205', 'Suite', 4, 3500.00, 1, 'available', '2025-09-13 02:27:29', '2025-09-13 02:37:14'),
(16, '206', 'Family Room', 5, 4200.00, 1, 'available', '2025-09-13 02:27:29', '2025-09-13 02:37:14'),
(17, '207', 'VIP Room', 4, 5200.00, 1, 'available', '2025-09-13 02:27:29', '2025-09-13 06:50:19'),
(18, '208', 'Single Room', 1, 1200.00, 1, 'available', '2025-09-13 02:27:29', '2025-09-13 08:13:43'),
(19, '209', 'Double Room', 2, 2000.00, 1, 'available', '2025-09-13 02:27:29', '2025-09-13 02:37:14'),
(20, '210', 'Deluxe Room', 3, 2500.00, 1, 'available', '2025-09-13 02:27:29', '2025-09-13 02:37:14'),
(21, '301', 'Single Room', 1, 1200.00, 1, 'available', '2025-09-13 02:27:43', '2025-09-13 10:06:15'),
(22, '302', 'Double Room', 2, 2000.00, 1, 'available', '2025-09-13 02:27:43', '2025-09-13 02:37:14'),
(23, '303', 'Twin Room', 2, 2000.00, 1, 'available', '2025-09-13 02:27:43', '2025-09-13 08:10:46'),
(24, '304', 'Deluxe Room', 3, 2500.00, 1, 'available', '2025-09-13 02:27:43', '2025-09-13 02:37:14'),
(25, '305', 'Suite', 4, 3500.00, 1, 'available', '2025-09-13 02:27:43', '2025-09-13 02:37:14'),
(26, '306', 'Family Room', 5, 4200.00, 1, 'available', '2025-09-13 02:27:43', '2025-09-13 02:37:14'),
(27, '307', 'VIP Room', 4, 5200.00, 1, 'available', '2025-09-13 02:27:43', '2025-09-13 02:37:14'),
(28, '308', 'Single Room', 1, 1200.00, 1, 'available', '2025-09-13 02:27:43', '2025-09-13 08:21:57'),
(29, '309', 'Double Room', 2, 2000.00, 1, 'available', '2025-09-13 02:27:43', '2025-09-13 02:37:14'),
(30, '310', 'Deluxe Room', 3, 2500.00, 1, 'available', '2025-09-13 02:27:43', '2025-09-13 02:37:14'),
(31, '401', 'Single Room', 1, 1200.00, 1, 'available', '2025-09-13 02:27:43', '2025-09-13 02:37:14'),
(32, '402', 'Double Room', 2, 2000.00, 1, 'available', '2025-09-13 02:27:43', '2025-09-13 02:37:14'),
(33, '403', 'Twin Room', 2, 2000.00, 1, 'available', '2025-09-13 02:27:43', '2025-09-13 21:42:35'),
(34, '404', 'Deluxe Room', 3, 2500.00, 1, 'available', '2025-09-13 02:27:43', '2025-09-13 02:37:14'),
(35, '405', 'Suite', 4, 3500.00, 1, 'available', '2025-09-13 02:27:43', '2025-09-13 02:37:14'),
(36, '406', 'Family Room', 5, 4200.00, 1, 'available', '2025-09-13 02:27:43', '2025-09-13 02:37:14'),
(37, '407', 'VIP Room', 4, 5200.00, 1, 'available', '2025-09-13 02:27:43', '2025-09-13 07:24:15'),
(38, '408', 'Single Room', 1, 1200.00, 1, 'available', '2025-09-13 02:27:43', '2025-09-13 02:37:14'),
(39, '409', 'Double Room', 2, 2000.00, 1, 'available', '2025-09-13 02:27:43', '2025-09-13 02:37:14'),
(40, '410', 'Deluxe Room', 3, 2500.00, 1, 'available', '2025-09-13 02:27:43', '2025-09-13 02:37:14'),
(41, '411', 'Single Room', 1, 1200.00, 1, 'available', '2025-09-13 02:28:10', '2025-09-13 02:37:14'),
(42, '412', 'Double Room', 2, 2000.00, 1, 'available', '2025-09-13 02:28:10', '2025-09-13 02:37:14'),
(43, '413', 'Twin Room', 2, 2000.00, 1, 'available', '2025-09-13 02:28:10', '2025-09-13 02:37:14'),
(44, '414', 'Deluxe Room', 3, 2500.00, 1, 'available', '2025-09-13 02:28:10', '2025-09-13 02:37:14'),
(45, '415', 'Suite', 4, 3500.00, 1, 'available', '2025-09-13 02:28:10', '2025-09-13 02:37:14'),
(46, '416', 'Family Room', 5, 4200.00, 1, 'available', '2025-09-13 02:28:10', '2025-09-13 02:37:14'),
(47, '417', 'VIP Room', 4, 5200.00, 1, 'available', '2025-09-13 02:28:10', '2025-09-13 10:17:38'),
(48, '418', 'Single Room', 1, 1200.00, 1, 'available', '2025-09-13 02:28:10', '2025-09-13 02:37:14'),
(49, '419', 'Double Room', 2, 2000.00, 1, 'available', '2025-09-13 02:28:10', '2025-09-13 02:37:14'),
(50, '420', 'Deluxe Room', 3, 2500.00, 1, 'available', '2025-09-13 02:28:10', '2025-09-13 02:37:14'),
(51, '421', 'Twin Room', 2, 2000.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(52, '422', 'Deluxe Room', 3, 2500.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(53, '423', 'Suite', 4, 3500.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(54, '424', 'Family Room', 5, 4200.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(55, '425', 'VIP Room', 4, 5200.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(56, '426', 'Single Room', 1, 1200.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 05:00:22'),
(57, '427', 'Double Room', 2, 2000.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(58, '428', 'Twin Room', 2, 2000.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(59, '429', 'Deluxe Room', 3, 2500.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(60, '430', 'Suite', 4, 3500.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(61, '431', 'Single Room', 1, 1200.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(62, '432', 'Double Room', 2, 2000.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(63, '433', 'Twin Room', 2, 2000.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(64, '434', 'Deluxe Room', 3, 2500.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(65, '435', 'Suite', 4, 3500.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(66, '436', 'Family Room', 5, 4200.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(67, '437', 'VIP Room', 4, 5200.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(68, '438', 'Single Room', 1, 1200.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 22:01:48'),
(69, '439', 'Double Room', 2, 2000.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(70, '440', 'Twin Room', 2, 2000.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(71, '441', 'Deluxe Room', 3, 2500.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(72, '442', 'Suite', 4, 3500.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(73, '443', 'Family Room', 5, 4200.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(74, '444', 'VIP Room', 4, 5200.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(75, '445', 'Single Room', 1, 1200.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 07:45:26'),
(76, '446', 'Double Room', 2, 2000.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(77, '447', 'Twin Room', 2, 2000.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(78, '448', 'Deluxe Room', 3, 2500.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(79, '449', 'Suite', 4, 3500.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(80, '450', 'Family Room', 5, 4200.00, 1, 'available', '2025-09-13 02:28:46', '2025-09-13 02:37:14'),
(81, '451', 'VIP Room', 4, 5200.00, 1, 'available', '2025-09-13 02:29:16', '2025-09-13 02:37:14'),
(82, '452', 'Single Room', 1, 1200.00, 1, 'available', '2025-09-13 02:29:16', '2025-09-13 21:53:21'),
(83, '453', 'Double Room', 2, 2000.00, 1, 'available', '2025-09-13 02:29:16', '2025-09-13 10:04:14'),
(84, '454', 'Twin Room', 2, 2000.00, 1, 'available', '2025-09-13 02:29:16', '2025-09-13 02:37:14'),
(85, '455', 'Deluxe Room', 3, 2500.00, 1, 'available', '2025-09-13 02:29:16', '2025-09-13 02:37:14'),
(86, '456', 'Suite', 4, 3500.00, 1, 'available', '2025-09-13 02:29:16', '2025-09-13 02:37:14'),
(87, '457', 'Family Room', 5, 4200.00, 1, 'available', '2025-09-13 02:29:16', '2025-09-13 02:37:14'),
(88, '458', 'VIP Room', 4, 5200.00, 1, 'available', '2025-09-13 02:29:16', '2025-09-13 02:37:14'),
(89, '459', 'Single Room', 1, 1200.00, 1, 'available', '2025-09-13 02:29:16', '2025-09-13 07:36:31'),
(90, '460', 'Double Room', 2, 2000.00, 1, 'available', '2025-09-13 02:29:16', '2025-09-13 07:58:04'),
(91, '461', 'Twin Room', 2, 2000.00, 1, 'available', '2025-09-13 02:29:16', '2025-09-13 02:37:14'),
(92, '462', 'Deluxe Room', 3, 2500.00, 1, 'available', '2025-09-13 02:29:16', '2025-09-13 02:37:14'),
(93, '463', 'Suite', 4, 3500.00, 1, 'available', '2025-09-13 02:29:16', '2025-09-13 02:37:14'),
(94, '464', 'Family Room', 5, 4200.00, 1, 'available', '2025-09-13 02:29:16', '2025-09-13 02:37:14'),
(95, '465', 'VIP Room', 4, 5200.00, 1, 'available', '2025-09-13 02:29:16', '2025-09-13 06:45:25'),
(96, '466', 'Single Room', 1, 1200.00, 1, 'available', '2025-09-13 02:29:16', '2025-09-13 07:23:21'),
(97, '467', 'Double Room', 2, 2000.00, 1, 'available', '2025-09-13 02:29:16', '2025-09-13 02:37:14'),
(98, '468', 'Deluxe Room', 3, 2500.00, 1, 'available', '2025-09-13 02:29:16', '2025-09-13 02:37:14'),
(99, '469', 'Suite', 4, 3500.00, 1, 'available', '2025-09-13 02:29:16', '2025-09-13 02:37:14'),
(100, '470', 'Family Room', 5, 4200.00, 1, 'available', '2025-09-13 02:29:16', '2025-09-13 02:37:14');

--
-- Triggers `rooms`
--
DELIMITER $$
CREATE TRIGGER `rooms_before_update` BEFORE UPDATE ON `rooms` FOR EACH ROW BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `room_payments`
--

CREATE TABLE `room_payments` (
  `payment_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `walkin_id` int(11) DEFAULT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `room_type` varchar(50) NOT NULL,
  `room_price` decimal(10,2) NOT NULL,
  `stay` varchar(50) DEFAULT NULL,
  `extended_price` decimal(10,2) DEFAULT 0.00,
  `extended_duration` varchar(8) DEFAULT '00:00:00',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'Mark', 'Rivera', 'Front Desk Manager', 'Reception', 'mark.rivera@hotel.com', '+63-917-100-2001', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(2, 'Grace', 'Santos', 'Housekeeping Supervisor', 'Housekeeping', 'grace.santos@hotel.com', '+63-918-200-3002', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(3, 'Tony', 'Mendoza', 'Maintenance Chief', 'Maintenance', 'tony.mendoza@hotel.com', '+63-919-300-4003', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(4, 'Linda', 'Cruz', 'Cashier', 'POS', 'linda.cruz@hotel.com', '+63-920-400-5004', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(5, 'Peter', 'Reyes', 'Room Attendant', 'Housekeeping', 'peter.reyes@hotel.com', '+63-921-500-6005', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(6, 'Mary', 'Gonzales', 'Receptionist', 'Reception', 'mary.gonzales@hotel.com', '+63-917-600-7006', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(7, 'Jose', 'Hernandez', 'Maintenance Worker', 'Maintenance', 'jose.hernandez@hotel.com', '+63-918-700-8007', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(8, 'Anna', 'Flores', 'Room Service', 'POS', 'anna.flores@hotel.com', '+63-919-800-9008', '2025-09-09 08:42:18', '2025-09-09 08:42:18');

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

--
-- Dumping data for table `stock_usage`
--

INSERT INTO `stock_usage` (`usage_id`, `item_id`, `used_qty`, `used_by`, `date_used`) VALUES
(1, 1, 15, 'Housekeeping', '2024-09-01 08:30:00'),
(2, 2, 8, 'Housekeeping', '2024-09-01 10:15:00'),
(3, 3, 3, 'POS', '2024-09-02 06:45:00'),
(4, 4, 5, 'Housekeeping', '2024-09-02 14:20:00'),
(5, 5, 2, 'POS', '2024-09-03 09:00:00'),
(6, 6, 4, 'Housekeeping', '2024-09-03 16:30:00'),
(7, 7, 2, 'Housekeeping', '2024-09-04 11:45:00'),
(8, 8, 8, 'Maintenance', '2024-09-04 13:15:00'),
(9, 9, 12, 'Housekeeping', '2024-09-05 07:30:00'),
(10, 11, 4, 'Housekeeping', '2024-09-05 15:00:00'),
(11, 12, 6, 'Housekeeping', '2024-09-06 12:20:00'),
(12, 13, 2, 'Maintenance', '2024-09-06 14:45:00'),
(13, 14, 15, 'POS', '2024-09-07 08:00:00'),
(14, 15, 5, 'Housekeeping', '2024-09-07 16:15:00'),
(15, 1, 10, 'Housekeeping', '2024-09-08 09:30:00');

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
(1, 'CleanCorp Supplies', 'Maria Santos', 'maria@cleancorp.com', '+63-917-123-4567', '123 Makati Ave, Makati City, Metro Manila', 1, '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(2, 'FreshFood Distributors', 'Juan Dela Cruz', 'juan@freshfood.ph', '+63-918-234-5678', '456 Quezon Blvd, Quezon City, Metro Manila', 1, '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(3, 'HotelMax Equipment', 'Sarah Johnson', 'sarah@hotelmax.com', '+63-919-345-6789', '789 BGC Drive, Taguig City, Metro Manila', 1, '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(4, 'Metro Office Solutions', 'Robert Kim', 'robert@metrooffice.ph', '+63-920-456-7890', '321 Ortigas Center, Pasig City, Metro Manila', 1, '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(5, 'Premium Linens Co.', 'Ana Rodriguez', 'ana@premiumlinens.com', '+63-921-567-8901', '654 Shaw Blvd, Mandaluyong City, Metro Manila', 1, '2025-09-09 08:42:18', '2025-09-09 08:42:18');

-- --------------------------------------------------------

--
-- Table structure for table `walk_in`
--

CREATE TABLE `walk_in` (
  `walkin_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'occupied',
  `remarks` text DEFAULT NULL,
  `check_in` timestamp NOT NULL DEFAULT current_timestamp(),
  `check_out` datetime NOT NULL,
  `extended_duration` varchar(8) DEFAULT '00:00:00',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `actual_checkout` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `billings`
--
ALTER TABLE `billings`
  ADD PRIMARY KEY (`billing_id`),
  ADD KEY `guest_id` (`guest_id`);

--
-- Indexes for table `billing_items`
--
ALTER TABLE `billing_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `billing_id` (`billing_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_guest_id` (`guest_id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `folio`
--
ALTER TABLE `folio`
  ADD PRIMARY KEY (`folio_id`),
  ADD KEY `guest_id` (`guest_id`);

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
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`guest_id`);

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
-- Indexes for table `minibar_consumption`
--
ALTER TABLE `minibar_consumption`
  ADD PRIMARY KEY (`consumption_id`),
  ADD KEY `guest_id` (`guest_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `staff_id` (`staff_id`);

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
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `guest_id` (`guest_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `reward_transactions`
--
ALTER TABLE `reward_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_guest_id` (`guest_id`),
  ADD KEY `idx_program_id` (`program_id`),
  ADD KEY `idx_transaction_type` (`transaction_type`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `room_number` (`room_number`);

--
-- Indexes for table `room_payments`
--
ALTER TABLE `room_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_payment_reservation` (`reservation_id`);

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
-- Indexes for table `walk_in`
--
ALTER TABLE `walk_in`
  ADD PRIMARY KEY (`walkin_id`),
  ADD KEY `guest_id` (`guest_id`),
  ADD KEY `room_id` (`room_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `billings`
--
ALTER TABLE `billings`
  MODIFY `billing_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `billing_items`
--
ALTER TABLE `billing_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `folio`
--
ALTER TABLE `folio`
  MODIFY `folio_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grn`
--
ALTER TABLE `grn`
  MODIFY `grn_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `guest_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `minibar_consumption`
--
ALTER TABLE `minibar_consumption`
  MODIFY `consumption_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `po_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reward_transactions`
--
ALTER TABLE `reward_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `room_payments`
--
ALTER TABLE `room_payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `stock_usage`
--
ALTER TABLE `stock_usage`
  MODIFY `usage_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `walk_in`
--
ALTER TABLE `walk_in`
  MODIFY `walkin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `billings`
--
ALTER TABLE `billings`
  ADD CONSTRAINT `billings_ibfk_1` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`);

--
-- Constraints for table `billing_items`
--
ALTER TABLE `billing_items`
  ADD CONSTRAINT `billing_items_ibfk_1` FOREIGN KEY (`billing_id`) REFERENCES `billings` (`billing_id`) ON DELETE CASCADE;

--
-- Constraints for table `folio`
--
ALTER TABLE `folio`
  ADD CONSTRAINT `folio_ibfk_1` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`);

--
-- Constraints for table `grn`
--
ALTER TABLE `grn`
  ADD CONSTRAINT `grn_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`) ON DELETE CASCADE;

--
-- Constraints for table `minibar_consumption`
--
ALTER TABLE `minibar_consumption`
  ADD CONSTRAINT `minibar_consumption_ibfk_1` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`),
  ADD CONSTRAINT `minibar_consumption_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `inventory` (`item_id`),
  ADD CONSTRAINT `minibar_consumption_ibfk_3` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`);

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `fk_reservations_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reservations_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `room_payments`
--
ALTER TABLE `room_payments`
  ADD CONSTRAINT `fk_payment_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`reservation_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `walk_in`
--
ALTER TABLE `walk_in`
  ADD CONSTRAINT `walk_in_ibfk_1` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `walk_in_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

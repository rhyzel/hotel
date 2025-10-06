-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 06, 2025 at 05:42 PM
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
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(10) UNSIGNED NOT NULL,
  `staff_id` varchar(20) NOT NULL,
  `attendance_date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `status` enum('Present','Absent') NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `staff_id`, `attendance_date`, `time_in`, `time_out`, `status`, `start_time`, `end_time`) VALUES
(1, 'EMP487077', '2025-09-13', '10:27:47', '12:18:57', 'Present', NULL, NULL),
(2, 'EMP453493', '2025-09-13', '10:27:56', '10:27:57', 'Present', NULL, NULL),
(3, 'EMP293486', '2025-09-13', '10:27:58', '10:27:59', 'Present', NULL, NULL),
(4, 'EMP223066', '2025-09-13', '10:28:00', '10:28:01', 'Present', NULL, NULL),
(5, 'EMP289373', '2025-09-13', '10:28:01', '10:28:03', 'Present', NULL, NULL),
(0, 'EMP487077', '2025-09-15', '01:26:26', '03:47:30', 'Present', NULL, NULL),
(0, 'EMP202596', '2025-09-15', '01:26:30', '03:47:34', 'Present', NULL, NULL),
(0, 'EMP453493', '2025-09-15', '01:26:30', '03:47:35', 'Present', NULL, NULL),
(0, 'EMP293486', '2025-09-15', '01:26:31', '03:47:36', 'Present', NULL, NULL),
(0, 'EMP223066', '2025-09-15', '01:26:32', '03:47:36', 'Present', NULL, NULL),
(0, 'EMP289373', '2025-09-15', '01:26:33', '03:47:37', 'Present', NULL, NULL),
(0, 'EMP155598', '2025-09-15', '03:47:32', '03:47:32', 'Present', NULL, NULL),
(0, 'EMP333504', '2025-09-15', '03:47:38', '03:47:40', 'Present', NULL, NULL),
(0, 'EMP289373', '2025-09-16', '09:46:43', '09:49:53', 'Present', NULL, NULL),
(0, 'EMP223066', '2025-09-16', '09:49:54', '09:49:57', 'Present', NULL, NULL),
(0, 'EMP827264', '2025-09-16', '12:23:54', '12:44:47', 'Present', '13:18:00', '00:17:00'),
(0, 'EMP827264', '2025-09-16', '12:23:54', '12:44:47', 'Present', '13:18:00', '00:17:00'),
(0, 'EMP487077', '2025-09-16', '12:23:55', '12:44:48', 'Present', NULL, NULL),
(0, 'EMP453493', '2025-09-16', '12:42:21', '12:44:49', 'Present', NULL, NULL),
(0, 'EMP293486', '2025-09-16', '12:44:50', '12:44:50', 'Present', NULL, NULL),
(0, 'EMP185152', '2025-09-16', '12:44:52', '12:44:53', 'Present', NULL, NULL),
(1, 'EMP487077', '2025-09-13', '10:27:47', '12:18:57', 'Present', NULL, NULL),
(2, 'EMP453493', '2025-09-13', '10:27:56', '10:27:57', 'Present', NULL, NULL),
(3, 'EMP293486', '2025-09-13', '10:27:58', '10:27:59', 'Present', NULL, NULL),
(4, 'EMP223066', '2025-09-13', '10:28:00', '10:28:01', 'Present', NULL, NULL),
(5, 'EMP289373', '2025-09-13', '10:28:01', '10:28:03', 'Present', NULL, NULL),
(0, 'EMP487077', '2025-09-15', '01:26:26', '03:47:30', 'Present', NULL, NULL),
(0, 'EMP202596', '2025-09-15', '01:26:30', '03:47:34', 'Present', NULL, NULL),
(0, 'EMP453493', '2025-09-15', '01:26:30', '03:47:35', 'Present', NULL, NULL),
(0, 'EMP293486', '2025-09-15', '01:26:31', '03:47:36', 'Present', NULL, NULL),
(0, 'EMP223066', '2025-09-15', '01:26:32', '03:47:36', 'Present', NULL, NULL),
(0, 'EMP289373', '2025-09-15', '01:26:33', '03:47:37', 'Present', NULL, NULL),
(0, 'EMP155598', '2025-09-15', '03:47:32', '03:47:32', 'Present', NULL, NULL),
(0, 'EMP333504', '2025-09-15', '03:47:38', '03:47:40', 'Present', NULL, NULL),
(0, 'EMP289373', '2025-09-16', '09:46:43', '09:49:53', 'Present', NULL, NULL),
(0, 'EMP223066', '2025-09-16', '09:49:54', '09:49:57', 'Present', NULL, NULL),
(0, 'EMP827264', '2025-09-16', '12:23:54', '12:44:47', 'Present', '13:18:00', '00:17:00'),
(0, 'EMP827264', '2025-09-16', '12:23:54', '12:44:47', 'Present', '13:18:00', '00:17:00'),
(0, 'EMP487077', '2025-09-16', '12:23:55', '12:44:48', 'Present', NULL, NULL),
(0, 'EMP453493', '2025-09-16', '12:42:21', '12:44:49', 'Present', NULL, NULL),
(0, 'EMP293486', '2025-09-16', '12:44:50', '12:44:50', 'Present', NULL, NULL),
(0, 'EMP185152', '2025-09-16', '12:44:52', '12:44:53', 'Present', NULL, NULL);

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

--
-- Dumping data for table `billings`
--

INSERT INTO `billings` (`billing_id`, `guest_id`, `billing_type`, `total_amount`, `billing_date`, `status`) VALUES
(4, 1, '', 895.00, '2025-09-23 10:01:28', 'pending'),
(5, 2, '', 85.00, '2025-09-23 10:19:13', 'pending'),
(6, 2, '', 205.00, '2025-09-26 14:20:06', 'pending'),
(7, 2, 'other', 300.00, '2025-09-26 16:06:03', 'pending'),
(8, 2, '', 120.00, '2025-09-26 16:22:10', 'pending');

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

--
-- Dumping data for table `billing_items`
--

INSERT INTO `billing_items` (`id`, `billing_id`, `item_name`, `quantity`, `unit_price`, `total_price`) VALUES
(1, 4, 'Coffee (Regular)', 4, 85.00, 340.00),
(2, 4, 'Coffee (Premium)', 3, 120.00, 360.00),
(3, 4, 'Tea (Local)', 3, 65.00, 195.00),
(4, 5, 'Coffee (Regular)', 1, 85.00, 85.00),
(5, 6, 'Coffee (Regular)', 1, 85.00, 85.00),
(6, 6, 'Coffee (Premium)', 1, 120.00, 120.00),
(7, 7, 'Hotel Keychain', 2, 150.00, 300.00),
(8, 8, 'Coffee (Premium)', 1, 120.00, 120.00);

-- --------------------------------------------------------

--
-- Table structure for table `campaigns`
--

CREATE TABLE `campaigns` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('email','sms','both') DEFAULT 'email',
  `target_audience` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `status` enum('draft','scheduled','active','completed') DEFAULT 'draft',
  `schedule` datetime DEFAULT NULL,
  `sent_count` int(11) DEFAULT 0,
  `open_rate` decimal(5,2) DEFAULT 0.00,
  `click_rate` decimal(5,2) DEFAULT 0.00,
  `created_by_user` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ceo`
--

CREATE TABLE `ceo` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `position_name` varchar(50) NOT NULL DEFAULT 'CEO',
  `department_name` varchar(50) NOT NULL DEFAULT 'Executive',
  `photo` varchar(255) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `base_salary` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ceo`
--

INSERT INTO `ceo` (`id`, `staff_id`, `first_name`, `last_name`, `position_name`, `department_name`, `photo`, `email`, `phone`, `address`, `hire_date`, `base_salary`) VALUES
(2, 'CEO', 'Alice', 'Garcia', 'CEO', 'Executive', 'ceo.jpg', NULL, NULL, NULL, NULL, NULL),
(2, 'CEO', 'Alice', 'Garcia', 'CEO', 'Executive', 'ceo.jpg', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `guest_name` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `type` enum('complaint') DEFAULT 'complaint',
  `rating` int(1) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `status` enum('pending','in-progress','resolved','dismissed') DEFAULT 'pending',
  `reply` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_documents`
--

CREATE TABLE `employee_documents` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(20) NOT NULL,
  `contract_file` varchar(255) DEFAULT NULL,
  `sss_no` varchar(20) DEFAULT NULL,
  `philhealth_no` varchar(20) DEFAULT NULL,
  `pagibig_no` varchar(20) DEFAULT NULL,
  `tin_no` varchar(20) DEFAULT NULL,
  `nbi_clearance` varchar(255) DEFAULT NULL,
  `birth_certificate` varchar(255) DEFAULT NULL,
  `diploma` varchar(255) DEFAULT NULL,
  `tor` varchar(255) DEFAULT NULL,
  `barangay_clearance` varchar(255) DEFAULT NULL,
  `police_clearance` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_documents`
--

INSERT INTO `employee_documents` (`id`, `staff_id`, `contract_file`, `sss_no`, `philhealth_no`, `pagibig_no`, `tin_no`, `nbi_clearance`, `birth_certificate`, `diploma`, `tor`, `barangay_clearance`, `police_clearance`) VALUES
(0, 'EMP289373', NULL, '', '', '', '', 'uploads/68c9786fd6bfa_68c599c72a462_68c060d770665_nbi.jpg', NULL, '', 'uploads/68c9786fd6e58_68c060d7709fc_Transcript-of-Records-TOR-1-320.jpg', 'uploads/68c9786fd70cb_68c599c72b8ca_68c544f5de366_68c060d770c2b_police-clearance-certificate-1024x576.jpg', NULL),
(0, 'EMP289373', NULL, '', '', '', '', 'uploads/68c9786fd6bfa_68c599c72a462_68c060d770665_nbi.jpg', NULL, '', 'uploads/68c9786fd6e58_68c060d7709fc_Transcript-of-Records-TOR-1-320.jpg', 'uploads/68c9786fd70cb_68c599c72b8ca_68c544f5de366_68c060d770c2b_police-clearance-certificate-1024x576.jpg', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `guest_name` varchar(255) NOT NULL,
  `type` enum('review') DEFAULT 'review',
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
-- Table structure for table `giftshop_inventory`
--

CREATE TABLE `giftshop_inventory` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `category` enum('Souvenirs','Clothing','Electronics','Books','Toiletries','Snacks','Beverages','Others') NOT NULL,
  `description` text DEFAULT NULL,
  `quantity_in_stock` int(11) NOT NULL DEFAULT 0,
  `unit_price` decimal(10,2) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `reorder_level` int(11) DEFAULT 10,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `giftshop_inventory`
--

INSERT INTO `giftshop_inventory` (`item_id`, `item_name`, `category`, `description`, `quantity_in_stock`, `unit_price`, `supplier_id`, `reorder_level`, `created_at`, `updated_at`) VALUES
(1, 'Hotel Logo T-Shirt', 'Clothing', 'Comfortable cotton t-shirt with hotel logo', 50, 450.00, 6, 10, '2025-09-13 04:00:00', '2025-09-13 04:00:00'),
(2, 'Hotel Keychain', 'Souvenirs', 'Metal keychain with hotel name and logo', 98, 150.00, 6, 20, '2025-09-13 04:00:00', '2025-09-26 16:06:03'),
(3, 'Coffee Mug', 'Souvenirs', 'Ceramic mug with hotel branding', 75, 280.00, 6, 15, '2025-09-13 04:00:00', '2025-09-13 04:00:00'),
(4, 'Local Handicraft', 'Souvenirs', 'Traditional Filipino handicraft item', 30, 650.00, 6, 5, '2025-09-13 04:00:00', '2025-09-13 04:00:00'),
(5, 'Phone Charger', 'Electronics', 'Universal USB phone charger', 25, 350.00, 6, 10, '2025-09-13 04:00:00', '2025-09-13 04:00:00'),
(6, 'Travel Guide Book', 'Books', 'Philippines travel guide book', 40, 420.00, 6, 8, '2025-09-13 04:00:00', '2025-09-13 04:00:00'),
(7, 'Toiletries Set', 'Toiletries', 'Travel-sized toiletries set', 60, 180.00, 6, 15, '2025-09-13 04:00:00', '2025-09-13 04:00:00'),
(8, 'Local Snacks Pack', 'Snacks', 'Assorted local Filipino snacks', 80, 120.00, 6, 20, '2025-09-13 04:00:00', '2025-09-13 04:00:00'),
(1, 'Hotel Logo T-Shirt', 'Clothing', 'Comfortable cotton t-shirt with hotel logo', 50, 450.00, 6, 10, '2025-09-13 04:00:00', '2025-09-13 04:00:00'),
(2, 'Hotel Keychain', 'Souvenirs', 'Metal keychain with hotel name and logo', 98, 150.00, 6, 20, '2025-09-13 04:00:00', '2025-09-26 16:06:03'),
(3, 'Coffee Mug', 'Souvenirs', 'Ceramic mug with hotel branding', 75, 280.00, 6, 15, '2025-09-13 04:00:00', '2025-09-13 04:00:00'),
(4, 'Local Handicraft', 'Souvenirs', 'Traditional Filipino handicraft item', 30, 650.00, 6, 5, '2025-09-13 04:00:00', '2025-09-13 04:00:00'),
(5, 'Phone Charger', 'Electronics', 'Universal USB phone charger', 25, 350.00, 6, 10, '2025-09-13 04:00:00', '2025-09-13 04:00:00'),
(6, 'Travel Guide Book', 'Books', 'Philippines travel guide book', 40, 420.00, 6, 8, '2025-09-13 04:00:00', '2025-09-13 04:00:00'),
(7, 'Toiletries Set', 'Toiletries', 'Travel-sized toiletries set', 60, 180.00, 6, 15, '2025-09-13 04:00:00', '2025-09-13 04:00:00'),
(8, 'Local Snacks Pack', 'Snacks', 'Assorted local Filipino snacks', 80, 120.00, 6, 20, '2025-09-13 04:00:00', '2025-09-13 04:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `giftshop_sales`
--

CREATE TABLE `giftshop_sales` (
  `sale_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `sale_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `staff_id` int(11) NOT NULL,
  `payment_method` enum('cash','card','room_charge') DEFAULT 'cash',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `giftshop_sales`
--

INSERT INTO `giftshop_sales` (`sale_id`, `guest_id`, `total_amount`, `sale_date`, `status`, `staff_id`, `payment_method`, `notes`) VALUES
(0, 2, 300.00, '2025-09-26 16:06:03', 'completed', 1, 'cash', '');

-- --------------------------------------------------------

--
-- Table structure for table `giftshop_sale_items`
--

CREATE TABLE `giftshop_sale_items` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `giftshop_sale_items`
--

INSERT INTO `giftshop_sale_items` (`id`, `sale_id`, `item_id`, `item_name`, `quantity`, `unit_price`, `total_price`) VALUES
(0, 0, 2, 'Hotel Keychain', 2, 150.00, 300.00);

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
(8, 'Lisa', 'Taylor', 'lisa.taylor@email.com', '+63-918-987-6543', '+63-919-456-7890', 'active', 'bronze', '2025-09-09 08:42:18', '2025-09-09 08:42:18'),
(9, 'onzi', 'delcruz', 'balansaggeoffrey06@gmail.com', '09817127679', '09931592553', 'vip', 'bronze', '2025-09-24 09:31:25', '2025-09-24 09:31:25');

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
-- Table structure for table `housekeeping_room_status`
--

CREATE TABLE `housekeeping_room_status` (
  `room_id` int(11) NOT NULL,
  `status` varchar(64) NOT NULL,
  `remarks` text DEFAULT NULL,
  `last_cleaned` date DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `ingredient_name` varchar(255) NOT NULL,
  `quantity` varchar(50) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`id`, `recipe_id`, `ingredient_name`, `quantity`, `unit`, `notes`, `created_at`, `updated_at`) VALUES
(725, 1, 'Beef', '500', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(726, 1, 'Potatoes', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(727, 1, 'Corn', '150', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(728, 1, 'Cabbage', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(729, 1, 'Water', '1', 'L', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(730, 2, 'Chicken', '500', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(731, 2, 'Ginger', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(732, 2, 'Papaya', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(733, 2, 'Malunggay', '30', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(734, 2, 'Water', '1', 'L', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(735, 3, 'Pork', '500', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(736, 3, 'Tamarind', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(737, 3, 'Radish', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(738, 3, 'Okra', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(739, 3, 'Water', '1.2', 'L', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(740, 4, 'Beef Shank', '600', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(741, 4, 'Marrow Bones', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(742, 4, 'Corn', '150', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(743, 4, 'Vegetables', '200', 'g', 'Mixed', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(744, 5, 'Chicken', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(745, 5, 'Macaroni', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(746, 5, 'Milk', '200', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(747, 5, 'Carrots', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(748, 6, 'Pancit Canton Noodles', '150', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(749, 6, 'Pork', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(750, 6, 'Shrimp', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(751, 6, 'Vegetables', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(752, 7, 'Bihon Noodles', '150', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(753, 7, 'Chicken', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(754, 7, 'Vegetables', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(755, 8, 'Rice Noodles', '150', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(756, 8, 'Seafood Sauce', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(757, 8, 'Boiled Egg', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(758, 9, 'Spaghetti', '150', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(759, 9, 'Hotdogs', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(760, 9, 'Tomato Sauce', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(761, 10, 'Pasta', '150', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(762, 10, 'Bacon', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(763, 10, 'White Sauce', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(764, 11, 'Chicken', '500', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(765, 11, 'Soy Sauce', '50', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(766, 11, 'Vinegar', '30', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(767, 11, 'Garlic', '20', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(768, 12, 'Pork Belly', '500', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(769, 12, 'Soy Sauce', '50', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(770, 12, 'Vinegar', '30', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(771, 12, 'Garlic', '20', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(772, 13, 'Oxtail', '500', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(773, 13, 'Peanut Sauce', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(774, 13, 'Vegetables', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(775, 14, 'Pork Belly', '500', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(776, 14, 'Oil', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(777, 15, 'Pork Leg', '1', 'kg', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(778, 15, 'Oil', '200', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(779, 16, 'Beef Strips', '300', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(780, 16, 'Rice', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(781, 16, 'Egg', '2', 'pcs', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(782, 17, 'Sweet Cured Pork', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(783, 17, 'Garlic Rice', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(784, 18, 'Pork Sausages', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(785, 18, 'Rice', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(786, 18, 'Egg', '2', 'pcs', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(787, 19, 'Bangus', '1', 'pc', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(788, 19, 'Vinegar', '50', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(789, 20, 'Chicken Leg', '500', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(790, 20, 'Annatto Oil', '20', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(791, 21, 'Tilapia', '1', 'pc', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(792, 21, 'Salt', '10', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(793, 22, 'Shrimp', '300', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(794, 22, 'Garlic Butter', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(795, 23, 'Squid Rings', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(796, 23, 'Flour', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(797, 23, 'Oil', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(798, 24, 'Fish Fillet', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(799, 24, 'Sweet and Sour Sauce', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(800, 25, 'Mixed Seafood', '300', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(801, 25, 'Peanut Sauce', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(802, 25, 'Vegetables', '150', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(803, 26, 'Sitaw', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(804, 26, 'Kalabasa', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(805, 26, 'Coconut Milk', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(806, 27, 'Vegetables', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(807, 27, 'Bagoong', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(808, 28, 'Dried Taro Leaves', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(809, 28, 'Coconut Milk', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(810, 29, 'Vegetables', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(811, 29, 'Chicken', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(812, 29, 'Shrimp', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(813, 30, 'Kangkong', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(814, 30, 'Soy Sauce', '30', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(815, 30, 'Vinegar', '20', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(816, 31, 'Shaved Ice', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(817, 31, 'Sweetened Fruits', '150', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(818, 31, 'Leche Flan', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(819, 32, 'Eggs', '3', 'pcs', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(820, 32, 'Milk', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(821, 32, 'Sugar', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(822, 33, 'Rice Flour', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(823, 33, 'Salted Egg', '1', 'pc', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(824, 33, 'Cheese', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(825, 34, 'Purple Rice', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(826, 34, 'Coconut', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(827, 35, 'Banana', '1', 'pc', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(828, 35, 'Jackfruit', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(829, 35, 'Lumpia Wrapper', '2', 'pcs', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(830, 36, 'Tea', '5', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(831, 36, 'Lemon', '1', 'pc', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(832, 36, 'Sugar', '20', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(833, 37, 'Calamansi', '5', 'pcs', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(834, 37, 'Sugar', '20', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(835, 37, 'Water', '200', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(836, 38, 'Mango', '1', 'pc', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(837, 38, 'Milk', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(838, 38, 'Ice', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(839, 39, 'Coconut Water', '200', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(840, 39, 'Coconut Meat', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(841, 40, 'Brown Sugar Syrup', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(842, 40, 'Sago', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(843, 40, 'Gulaman', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(844, 41, 'Chicken', '500', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(845, 41, 'Oil', '150', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(846, 42, 'Pork', '300', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(847, 42, 'Liver', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(848, 42, 'Tomato Sauce', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(849, 43, 'Beef', '500', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(850, 43, 'Tomato Sauce', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(851, 43, 'Vegetables', '150', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(852, 44, 'Chicken', '500', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(853, 44, 'Potatoes', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(854, 44, 'Bell Peppers', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(855, 44, 'Tomato Sauce', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(856, 45, 'Leftover Lechon', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(857, 45, 'Vinegar', '50', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(858, 46, 'Pork Face', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(859, 46, 'Onion', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(860, 46, 'Chili', '10', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(861, 47, 'Tofu', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(862, 47, 'Pork Ears', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(863, 47, 'Soy Sauce', '20', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(864, 47, 'Vinegar', '20', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(865, 48, 'Ground Pork', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(866, 48, 'Lumpia Wrapper', '5', 'pcs', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(867, 49, 'Fish', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(868, 49, 'Vinegar', '50', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(869, 49, 'Ginger', '10', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(870, 49, 'Chili', '5', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(871, 50, 'Green Mango', '150', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(872, 50, 'Bagoong', '30', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(873, 50, 'Onion', '30', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(725, 1, 'Beef', '500', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(726, 1, 'Potatoes', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(727, 1, 'Corn', '150', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(728, 1, 'Cabbage', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(729, 1, 'Water', '1', 'L', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(730, 2, 'Chicken', '500', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(731, 2, 'Ginger', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(732, 2, 'Papaya', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(733, 2, 'Malunggay', '30', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(734, 2, 'Water', '1', 'L', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(735, 3, 'Pork', '500', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(736, 3, 'Tamarind', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(737, 3, 'Radish', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(738, 3, 'Okra', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(739, 3, 'Water', '1.2', 'L', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(740, 4, 'Beef Shank', '600', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(741, 4, 'Marrow Bones', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(742, 4, 'Corn', '150', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(743, 4, 'Vegetables', '200', 'g', 'Mixed', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(744, 5, 'Chicken', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(745, 5, 'Macaroni', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(746, 5, 'Milk', '200', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(747, 5, 'Carrots', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(748, 6, 'Pancit Canton Noodles', '150', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(749, 6, 'Pork', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(750, 6, 'Shrimp', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(751, 6, 'Vegetables', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(752, 7, 'Bihon Noodles', '150', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(753, 7, 'Chicken', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(754, 7, 'Vegetables', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(755, 8, 'Rice Noodles', '150', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(756, 8, 'Seafood Sauce', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(757, 8, 'Boiled Egg', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(758, 9, 'Spaghetti', '150', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(759, 9, 'Hotdogs', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(760, 9, 'Tomato Sauce', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(761, 10, 'Pasta', '150', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(762, 10, 'Bacon', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(763, 10, 'White Sauce', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(764, 11, 'Chicken', '500', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(765, 11, 'Soy Sauce', '50', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(766, 11, 'Vinegar', '30', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(767, 11, 'Garlic', '20', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(768, 12, 'Pork Belly', '500', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(769, 12, 'Soy Sauce', '50', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(770, 12, 'Vinegar', '30', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(771, 12, 'Garlic', '20', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(772, 13, 'Oxtail', '500', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(773, 13, 'Peanut Sauce', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(774, 13, 'Vegetables', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(775, 14, 'Pork Belly', '500', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(776, 14, 'Oil', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(777, 15, 'Pork Leg', '1', 'kg', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(778, 15, 'Oil', '200', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(779, 16, 'Beef Strips', '300', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(780, 16, 'Rice', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(781, 16, 'Egg', '2', 'pcs', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(782, 17, 'Sweet Cured Pork', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(783, 17, 'Garlic Rice', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(784, 18, 'Pork Sausages', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(785, 18, 'Rice', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(786, 18, 'Egg', '2', 'pcs', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(787, 19, 'Bangus', '1', 'pc', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(788, 19, 'Vinegar', '50', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(789, 20, 'Chicken Leg', '500', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(790, 20, 'Annatto Oil', '20', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(791, 21, 'Tilapia', '1', 'pc', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(792, 21, 'Salt', '10', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(793, 22, 'Shrimp', '300', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(794, 22, 'Garlic Butter', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(795, 23, 'Squid Rings', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(796, 23, 'Flour', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(797, 23, 'Oil', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(798, 24, 'Fish Fillet', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(799, 24, 'Sweet and Sour Sauce', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(800, 25, 'Mixed Seafood', '300', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(801, 25, 'Peanut Sauce', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(802, 25, 'Vegetables', '150', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(803, 26, 'Sitaw', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(804, 26, 'Kalabasa', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(805, 26, 'Coconut Milk', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(806, 27, 'Vegetables', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(807, 27, 'Bagoong', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(808, 28, 'Dried Taro Leaves', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(809, 28, 'Coconut Milk', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(810, 29, 'Vegetables', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(811, 29, 'Chicken', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(812, 29, 'Shrimp', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(813, 30, 'Kangkong', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(814, 30, 'Soy Sauce', '30', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(815, 30, 'Vinegar', '20', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(816, 31, 'Shaved Ice', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(817, 31, 'Sweetened Fruits', '150', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(818, 31, 'Leche Flan', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(819, 32, 'Eggs', '3', 'pcs', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(820, 32, 'Milk', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(821, 32, 'Sugar', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(822, 33, 'Rice Flour', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(823, 33, 'Salted Egg', '1', 'pc', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(824, 33, 'Cheese', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(825, 34, 'Purple Rice', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(826, 34, 'Coconut', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(827, 35, 'Banana', '1', 'pc', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(828, 35, 'Jackfruit', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(829, 35, 'Lumpia Wrapper', '2', 'pcs', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(830, 36, 'Tea', '5', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(831, 36, 'Lemon', '1', 'pc', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(832, 36, 'Sugar', '20', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(833, 37, 'Calamansi', '5', 'pcs', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(834, 37, 'Sugar', '20', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(835, 37, 'Water', '200', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(836, 38, 'Mango', '1', 'pc', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(837, 38, 'Milk', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(838, 38, 'Ice', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(839, 39, 'Coconut Water', '200', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(840, 39, 'Coconut Meat', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(841, 40, 'Brown Sugar Syrup', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(842, 40, 'Sago', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(843, 40, 'Gulaman', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(844, 41, 'Chicken', '500', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(845, 41, 'Oil', '150', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(846, 42, 'Pork', '300', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(847, 42, 'Liver', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(848, 42, 'Tomato Sauce', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(849, 43, 'Beef', '500', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(850, 43, 'Tomato Sauce', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(851, 43, 'Vegetables', '150', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(852, 44, 'Chicken', '500', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(853, 44, 'Potatoes', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(854, 44, 'Bell Peppers', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(855, 44, 'Tomato Sauce', '100', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(856, 45, 'Leftover Lechon', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(857, 45, 'Vinegar', '50', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(858, 46, 'Pork Face', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(859, 46, 'Onion', '50', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(860, 46, 'Chili', '10', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(861, 47, 'Tofu', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(862, 47, 'Pork Ears', '100', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(863, 47, 'Soy Sauce', '20', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(864, 47, 'Vinegar', '20', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(865, 48, 'Ground Pork', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(866, 48, 'Lumpia Wrapper', '5', 'pcs', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(867, 49, 'Fish', '200', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(868, 49, 'Vinegar', '50', 'ml', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(869, 49, 'Ginger', '10', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(870, 49, 'Chili', '5', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(871, 50, 'Green Mango', '150', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(872, 50, 'Bagoong', '30', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02'),
(873, 50, 'Onion', '30', 'g', '', '2025-09-16 19:08:02', '2025-09-16 19:08:02');

-- --------------------------------------------------------

--
-- Table structure for table `ingredient_usage`
--

CREATE TABLE `ingredient_usage` (
  `usage_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `used_qty` int(11) NOT NULL,
  `used_by` varchar(100) NOT NULL,
  `date_used` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `integration_error_logs`
--

CREATE TABLE `integration_error_logs` (
  `log_id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL,
  `operation` varchar(100) NOT NULL,
  `error_message` text NOT NULL,
  `error_code` varchar(50) DEFAULT NULL,
  `source_table` varchar(50) DEFAULT NULL,
  `affected_ids` text DEFAULT NULL,
  `stack_trace` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `invoice_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `invoice_time` time DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `discount_rate` decimal(5,2) DEFAULT 0.00,
  `status` enum('unpaid','partial','paid','cancelled','refunded') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_experience`
--

CREATE TABLE `job_experience` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(20) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_experience`
--

INSERT INTO `job_experience` (`id`, `staff_id`, `company_name`, `position`, `start_date`, `end_date`) VALUES
(33, 'EMP223066', 'Hotel Savano', 'Receptionist', '2024-05-25', '2025-02-02'),
(33, 'EMP223066', 'Hotel Savano', 'Receptionist', '2024-05-25', '2025-02-02');

-- --------------------------------------------------------

--
-- Table structure for table `kitchen_orders`
--

CREATE TABLE `kitchen_orders` (
  `order_id` int(11) NOT NULL,
  `order_type` enum('restaurant','room_service') NOT NULL,
  `status` enum('pending','preparing','ready','completed') DEFAULT 'pending',
  `priority` int(11) DEFAULT 1,
  `table_number` varchar(10) DEFAULT NULL,
  `room_number` varchar(10) DEFAULT NULL,
  `assigned_chef` int(11) DEFAULT NULL,
  `guest_name` varchar(100) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `estimated_time` int(11) DEFAULT NULL COMMENT 'Time in minutes',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kitchen_orders`
--

INSERT INTO `kitchen_orders` (`order_id`, `order_type`, `status`, `priority`, `table_number`, `room_number`, `assigned_chef`, `guest_name`, `item_name`, `total_amount`, `notes`, `estimated_time`, `created_at`, `updated_at`) VALUES
(1, 'restaurant', 'preparing', 1, 'T1', NULL, 5, 'Juan Dela Cruz', 'Sinigang na Baboy, Pancit Canton', 680.00, 'Allergic to peanuts', 30, '2025-09-15 15:07:19', '2025-09-15 15:29:25'),
(2, 'restaurant', 'preparing', 2, 'T2', NULL, 7, 'Maria Santos', 'Lechon Kawali, Kare-Kare', 860.00, '', 25, '2025-09-15 15:07:19', '2025-09-15 15:07:19'),
(3, 'room_service', 'ready', 1, NULL, '101', 5, 'Carlos Reyes', 'Bicol Express, Halo-Halo', 560.00, 'Extra napkins', 20, '2025-09-15 15:07:19', '2025-09-15 15:29:30'),
(4, 'room_service', 'preparing', 2, NULL, '102', 7, 'Ana Lopez', 'Laing, Turon', 720.00, 'No spicy', 25, '2025-09-15 15:07:19', '2025-09-15 15:29:16'),
(5, 'restaurant', 'completed', 1, 'T3', NULL, 5, 'Miguel Ramos', 'Kare-Kare, Bibingka', 300.00, '', 15, '2025-09-15 15:07:19', '2025-09-15 15:39:48'),
(6, 'restaurant', 'preparing', 1, 'T5', NULL, 5, 'Test Guest', 'Beef Nilaga, Pancit Canton', 500.00, 'Test order', 40, '2025-09-16 18:50:29', '2025-09-16 18:50:29'),
(1, 'restaurant', 'pending', 1, 't3', NULL, NULL, 'Michael Thompson', 'Beef Nilaga', 560.00, 'not', NULL, '2025-09-23 09:58:54', '2025-09-23 09:58:54'),
(1, 'restaurant', 'preparing', 1, 'T1', NULL, 5, 'Juan Dela Cruz', 'Sinigang na Baboy, Pancit Canton', 680.00, 'Allergic to peanuts', 30, '2025-09-15 15:07:19', '2025-09-15 15:29:25'),
(2, 'restaurant', 'preparing', 2, 'T2', NULL, 7, 'Maria Santos', 'Lechon Kawali, Kare-Kare', 860.00, '', 25, '2025-09-15 15:07:19', '2025-09-15 15:07:19'),
(3, 'room_service', 'ready', 1, NULL, '101', 5, 'Carlos Reyes', 'Bicol Express, Halo-Halo', 560.00, 'Extra napkins', 20, '2025-09-15 15:07:19', '2025-09-15 15:29:30'),
(4, 'room_service', 'preparing', 2, NULL, '102', 7, 'Ana Lopez', 'Laing, Turon', 720.00, 'No spicy', 25, '2025-09-15 15:07:19', '2025-09-15 15:29:16'),
(5, 'restaurant', 'completed', 1, 'T3', NULL, 5, 'Miguel Ramos', 'Kare-Kare, Bibingka', 300.00, '', 15, '2025-09-15 15:07:19', '2025-09-15 15:39:48'),
(6, 'restaurant', 'preparing', 1, 'T5', NULL, 5, 'Test Guest', 'Beef Nilaga, Pancit Canton', 500.00, 'Test order', 40, '2025-09-16 18:50:29', '2025-09-16 18:50:29'),
(1, 'restaurant', 'pending', 1, 't3', NULL, NULL, 'Michael Thompson', 'Beef Nilaga', 560.00, 'not', NULL, '2025-09-23 09:58:54', '2025-09-23 09:58:54'),
(2, 'restaurant', 'pending', 1, 't3', NULL, NULL, 'onzi delcruz', 'Item 1', 0.00, '', NULL, '2025-09-26 16:25:57', '2025-09-26 16:25:57');

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `staff_id` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `staff_id`, `start_date`, `end_date`, `status`, `reason`, `created_at`) VALUES
(1, 'EMP289373', '2025-09-14', '2025-09-15', 'Approved', 'Maternity/Paternity', '2025-09-13 07:56:41'),
(0, 'EMP223066', '2025-09-17', '2025-09-18', 'Approved', 'Vacation', '2025-09-15 11:22:51'),
(0, 'EMP289373', '2025-09-18', '2025-09-19', 'Approved', 'Sick', '2025-09-16 13:59:08'),
(1, 'EMP289373', '2025-09-14', '2025-09-15', 'Approved', 'Maternity/Paternity', '2025-09-13 07:56:41'),
(0, 'EMP223066', '2025-09-17', '2025-09-18', 'Approved', 'Vacation', '2025-09-15 11:22:51'),
(0, 'EMP289373', '2025-09-18', '2025-09-19', 'Approved', 'Sick', '2025-09-16 13:59:08');

-- --------------------------------------------------------

--
-- Table structure for table `lounge_orders`
--

CREATE TABLE `lounge_orders` (
  `order_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `table_number` varchar(10) DEFAULT NULL,
  `order_type` enum('dine_in','takeaway') DEFAULT 'dine_in',
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','preparing','ready','served','cancelled') DEFAULT 'pending',
  `staff_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lounge_orders`
--

INSERT INTO `lounge_orders` (`order_id`, `guest_id`, `table_number`, `order_type`, `total_amount`, `order_date`, `status`, `staff_id`, `notes`) VALUES
(0, 1, 't2', 'dine_in', 895.00, '2025-09-23 10:01:28', 'pending', 1, ''),
(0, 1, 't2', 'dine_in', 895.00, '2025-09-23 10:01:28', 'pending', 1, ''),
(0, 2, 't2', 'dine_in', 85.00, '2025-09-23 10:19:13', 'pending', 1, ''),
(0, 2, 't2', 'dine_in', 205.00, '2025-09-26 14:20:06', 'pending', 1, ''),
(0, 2, 't2', 'dine_in', 120.00, '2025-09-26 16:22:10', 'pending', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `lounge_order_items`
--

CREATE TABLE `lounge_order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `category` enum('Beverages','Cocktails','Appetizers','Main Course','Desserts','Snacks') NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `special_instructions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lounge_order_items`
--

INSERT INTO `lounge_order_items` (`id`, `order_id`, `item_name`, `category`, `quantity`, `unit_price`, `total_price`, `special_instructions`) VALUES
(0, 0, 'Coffee (Regular)', 'Beverages', 4, 85.00, 340.00, NULL),
(0, 0, 'Coffee (Premium)', 'Beverages', 3, 120.00, 360.00, NULL),
(0, 0, 'Tea (Local)', 'Beverages', 3, 65.00, 195.00, NULL),
(0, 0, 'Coffee (Regular)', 'Beverages', 4, 85.00, 340.00, NULL),
(0, 0, 'Coffee (Premium)', 'Beverages', 3, 120.00, 360.00, NULL),
(0, 0, 'Tea (Local)', 'Beverages', 3, 65.00, 195.00, NULL),
(0, 0, 'Coffee (Regular)', 'Beverages', 1, 85.00, 85.00, NULL),
(0, 0, 'Coffee (Regular)', 'Beverages', 1, 85.00, 85.00, NULL),
(0, 0, 'Coffee (Premium)', 'Beverages', 1, 120.00, 120.00, NULL),
(0, 0, 'Coffee (Premium)', 'Beverages', 1, 120.00, 120.00, NULL);

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
  `revenue_impact` decimal(10,2) DEFAULT 0.00,
  `discount_rate` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loyalty_programs`
--

INSERT INTO `loyalty_programs` (`id`, `name`, `tier`, `points_rate`, `benefits`, `description`, `members_count`, `status`, `created_at`, `points_redeemed`, `rewards_given`, `revenue_impact`, `discount_rate`) VALUES
(1, 'Bronze Program', 'bronze', 1.0, 'Basic membership benefits', NULL, 9, 'active', '2025-09-09 09:09:55', 0, 0, 3060.00, 5.00),
(2, 'Silver Program', 'silver', 1.5, 'Extended membership benefits', NULL, 0, 'active', '2025-09-09 09:09:55', 0, 0, 0.00, 10.00),
(3, 'Gold Program', 'gold', 2.0, 'Premium membership benefits', NULL, 0, 'active', '2025-09-09 09:09:55', 0, 0, 0.00, 15.00),
(4, 'Platinum Program', 'platinum', 3.0, 'Exclusive membership benefits', NULL, 0, 'active', '2025-09-09 09:09:55', 0, 0, 0.00, 20.00);

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
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `special_instructions` text DEFAULT NULL,
  `status` enum('pending','preparing','ready','delivered') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `order_items`
--
DELIMITER $$
CREATE TRIGGER `deduct_inventory_after_order` AFTER INSERT ON `order_items` FOR EACH ROW BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE ing_id INT;
    DECLARE ing_recipe_id INT;
    DECLARE ing_name VARCHAR(255);
    DECLARE ing_qty DECIMAL(10,2);
    DECLARE ing_unit VARCHAR(50);

    -- Cursor to fetch all ingredients for the ordered recipe
    DECLARE ing_cursor CURSOR FOR
        SELECT id, recipe_id, ingredient_name, quantity, unit
        FROM ingredients
        WHERE recipe_id = NEW.recipe_id;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    OPEN ing_cursor;

    read_loop: LOOP
        FETCH ing_cursor INTO ing_id, ing_recipe_id, ing_name, ing_qty, ing_unit;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Deduct only if inventory category matches
        UPDATE inventory i
        JOIN (SELECT item_id FROM inventory WHERE item_name = ing_name 
              AND category IN ('Meat','Seafood','Vegetable','Fruit','Dairy','Seasoning','Grain')) AS inv
        ON i.item_id = inv.item_id
        SET i.quantity_in_stock = i.quantity_in_stock - (ing_qty * NEW.quantity);

        -- Insert usage record
        INSERT INTO stock_usage (item_id, used_qty, used_by, date_used)
        SELECT i.item_id, (ing_qty * NEW.quantity), 'POS', NOW()
        FROM inventory i
        WHERE i.item_name = ing_name
          AND i.category IN ('Meat','Seafood','Vegetable','Fruit','Dairy','Seasoning','Grain');

    END LOOP;

    CLOSE ing_cursor;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `restore_inventory_on_delete` AFTER DELETE ON `order_items` FOR EACH ROW BEGIN
  UPDATE inventory i
  JOIN ingredients ing ON i.item_name = ing.ingredient_name
  SET i.quantity_in_stock = i.quantity_in_stock + (ing.quantity * OLD.quantity)
  WHERE ing.recipe_id = OLD.recipe_id
    AND i.category = 'Food & Beverage';
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `restore_inventory_on_update` AFTER UPDATE ON `order_items` FOR EACH ROW BEGIN
  IF NEW.status = 'cancelled' AND OLD.status <> 'cancelled' THEN
    UPDATE inventory i
    JOIN ingredients ing ON i.item_name = ing.ingredient_name
    SET i.quantity_in_stock = i.quantity_in_stock + (ing.quantity * OLD.quantity)
    WHERE ing.recipe_id = OLD.recipe_id
      AND i.category = 'Food & Beverage';
  END IF;

  IF OLD.status = 'cancelled' AND NEW.status <> 'cancelled' THEN
    UPDATE inventory i
    JOIN ingredients ing ON i.item_name = ing.ingredient_name
    SET i.quantity_in_stock = i.quantity_in_stock - (ing.quantity * NEW.quantity)
    WHERE ing.recipe_id = NEW.recipe_id
      AND i.category = 'Food & Beverage';
  END IF;

  IF NEW.status <> 'cancelled' AND OLD.status <> 'cancelled' AND NEW.quantity <> OLD.quantity THEN
    UPDATE inventory i
    JOIN ingredients ing ON i.item_name = ing.ingredient_name
    SET i.quantity_in_stock = i.quantity_in_stock - (ing.quantity * (NEW.quantity - OLD.quantity))
    WHERE ing.recipe_id = NEW.recipe_id
      AND i.category = 'Food & Beverage';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `orientations`
--

CREATE TABLE `orientations` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orientations`
--

INSERT INTO `orientations` (`id`, `title`, `description`, `date`, `time`) VALUES
(1, 'Workplace Safety', 'Learn safety rules and emergency protocols.', '2025-09-15', '09:00:00'),
(2, 'Customer Service Training', 'Orientation for excellent customer service.', '2025-09-16', '13:00:00'),
(3, 'Fire Drill & Emergency Evacuation', 'Practice emergency evacuation procedures.', '2025-09-17', '10:00:00'),
(4, 'Workplace Safety', 'Learn safety rules and emergency protocols.', '2025-09-15', '09:00:00'),
(5, 'Customer Service Training', 'Orientation for excellent customer service.', '2025-09-16', '13:00:00'),
(6, 'Fire Drill & Emergency Evacuation', 'Practice emergency evacuation procedures.', '2025-09-17', '10:00:00'),
(7, 'Hotel Policies Overview', 'Introduction to hotel policies and standards.', '2025-09-18', '09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `orientation_attendance`
--

CREATE TABLE `orientation_attendance` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(20) NOT NULL,
  `orientation_id` int(11) NOT NULL,
  `status` enum('Pending','Attended') DEFAULT 'Pending',
  `attended_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orientation_attendance`
--

INSERT INTO `orientation_attendance` (`id`, `staff_id`, `orientation_id`, `status`, `attended_at`) VALUES
(82, 'EMP289373', 3, 'Attended', '2025-09-16 17:09:18'),
(83, 'EMP289373', 1, 'Attended', '2025-09-16 17:09:21'),
(84, 'EMP289373', 4, 'Attended', '2025-09-16 17:09:23'),
(85, 'EMP289373', 2, 'Attended', '2025-09-16 17:09:25'),
(86, 'EMP289373', 5, 'Attended', '2025-09-16 17:09:27'),
(87, 'EMP289373', 3, 'Attended', '2025-09-16 17:09:30'),
(88, 'EMP289373', 6, 'Attended', '2025-09-16 17:09:33'),
(89, 'EMP289373', 7, 'Attended', '2025-09-16 17:09:35');

-- --------------------------------------------------------

--
-- Table structure for table `overtime`
--

CREATE TABLE `overtime` (
  `id` int(10) UNSIGNED NOT NULL,
  `staff_id` varchar(20) NOT NULL,
  `overtime_date` date NOT NULL,
  `hours` int(11) NOT NULL DEFAULT 0,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `total_hours` decimal(5,2) GENERATED ALWAYS AS (time_to_sec(timediff(`end_time`,`start_time`)) / 3600) VIRTUAL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `overtime`
--

INSERT INTO `overtime` (`id`, `staff_id`, `overtime_date`, `hours`, `start_time`, `end_time`) VALUES
(6, 'EMP487077', '2025-09-13', 2, NULL, NULL),
(7, 'EMP343122', '2025-09-13', 1, NULL, NULL),
(8, 'EMP289373', '2025-09-13', 2, NULL, NULL),
(0, 'EMP453493', '2025-09-15', 2, NULL, NULL),
(0, 'EMP333504', '2025-09-15', 2, NULL, NULL),
(0, 'EMP487077', '2025-09-15', 2, NULL, NULL),
(0, 'EMP487077', '2025-09-15', 4, NULL, NULL),
(0, 'EMP289373', '2025-09-16', 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `group_billing_id` int(11) DEFAULT NULL,
  `payment_method` enum('cash','credit_card','debit_card','gcash','bank_transfer') DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `points_redeemed` int(11) DEFAULT 0,
  `payment_date` date DEFAULT NULL,
  `payment_time` time DEFAULT NULL,
  `gateway_name` varchar(50) DEFAULT 'manual',
  `gateway_reference` varchar(100) DEFAULT NULL,
  `status` enum('pending','succeeded','failed','refunded') DEFAULT 'pending',
  `comments` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` int(10) UNSIGNED NOT NULL,
  `position_name` varchar(100) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `required_count` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `position_name`, `department_name`, `required_count`) VALUES
(1, 'Front Desk', 'Front Desk', 4),
(2, 'Housekeeping', 'Housekeeping', 10),
(3, 'Maintenance', 'Maintenance', 1),
(4, 'Manager', 'Manager', 4),
(5, 'HR', 'HR', 6),
(6, 'Finance', 'Finance', 2),
(7, 'Bell guy', 'Front Desk', 2),
(8, 'Executive', 'Executive', 1),
(9, 'Director', 'Executive', 1),
(10, 'Chef', 'Chef', 4),
(11, 'Cook', 'Kitchen', 5),
(12, 'Concierge', 'Front Desk', 2),
(13, 'Receptionist', 'Front Desk', 5),
(14, 'Electrician', 'Maintenance', 5),
(15, 'IT Staff', 'IT', 2),
(16, 'Plumber', 'Maintenance', 3),
(17, 'Security Personnel', 'Security', 10),
(18, 'Waiter/Waitress', 'F&B', 7),
(19, 'Bartender', 'F&B', 2),
(20, 'Laundry', 'Housekeeping', 8),
(21, 'Valet', 'Front Desk', 2),
(22, 'Driver', 'Maintenance', 5),
(23, 'Manager', 'Housekeeping', 1),
(24, 'Manager', 'Front Desk', 1),
(25, 'Manager', 'Finance', 1),
(26, 'Manager', 'Maintenance', 1),
(27, 'Manager', 'HR', 1),
(28, 'Front Desk', 'Front Desk', 2),
(29, 'Housekeeping', 'Housekeeping', 3),
(30, 'Maintenance', 'Maintenance', 2),
(31, 'Manager', 'Housekeeping', 1),
(32, 'Manager', 'Front Desk', 1),
(33, 'Manager', 'Finance', 1),
(34, 'Manager', 'HR', 1),
(35, 'HR', 'HR', 1),
(36, 'Finance', 'Finance', 1),
(37, 'Bell guy', 'Front Desk', 2),
(38, 'Executive', 'Executive', 1),
(39, 'Director', 'Executive', 1),
(40, 'Chief', 'Executive', 1),
(41, 'Cook', 'Kitchen', 3),
(42, 'Concierge', 'Front Desk', 2),
(43, 'Receptionist', 'Front Desk', 2),
(44, 'Electrician', 'Maintenance', 1),
(45, 'IT Staff', 'IT', 2),
(46, 'Plumber', 'Maintenance', 1),
(47, 'Security Personnel', 'Security', 4),
(48, 'Waiter/Waitress', 'F&B', 5),
(49, 'Bartender', 'F&B', 3),
(50, 'Laundry', 'Housekeeping', 2),
(51, 'Valet', 'Front Desk', 2),
(52, 'Driver', 'Maintenance', 2),
(53, 'Front Desk', 'Front Desk', 3),
(54, 'Housekeeping', 'Housekeeping', 5),
(55, 'Maintenance', 'Maintenance', 3),
(56, 'Manager', 'Housekeeping', 1),
(57, 'Manager', 'Front Desk', 1),
(58, 'Manager', 'Finance', 1),
(59, 'Manager', 'HR', 1),
(60, 'Manager', 'IT', 1),
(61, 'HR', 'HR', 2),
(62, 'Finance', 'Finance', 2),
(63, 'Bell guy', 'Front Desk', 3),
(64, 'Executive', 'Executive', 2),
(65, 'Director', 'Executive', 1),
(66, 'Chief', 'Executive', 1),
(67, 'Cook', 'Kitchen', 4),
(68, 'Sous Chef', 'Kitchen', 2),
(69, 'Concierge', 'Front Desk', 2),
(70, 'Receptionist', 'Front Desk', 3),
(71, 'Electrician', 'Maintenance', 2),
(72, 'IT Staff', 'IT', 3),
(73, 'Plumber', 'Maintenance', 2),
(74, 'Security Personnel', 'Security', 5),
(75, 'Waiter/Waitress', 'F&B', 6),
(76, 'Bartender', 'F&B', 4),
(77, 'Laundry', 'Housekeeping', 3),
(78, 'Valet', 'Front Desk', 3),
(79, 'Driver', 'Maintenance', 3),
(80, 'Steward', 'F&B', 2),
(81, 'Housekeeping Supervisor', 'Housekeeping', 1),
(82, 'IT Manager', 'IT', 1),
(83, 'HR Assistant', 'HR', 2),
(84, 'Finance Analyst', 'Finance', 2),
(85, 'Maintenance Supervisor', 'Maintenance', 1),
(86, 'Front Desk', 'Front Desk', 2),
(87, 'Housekeeping', 'Housekeeping', 3),
(88, 'Maintenance', 'Maintenance', 2),
(89, 'Manager', 'Housekeeping', 1),
(90, 'Manager', 'Front Desk', 1),
(91, 'Manager', 'Finance', 1),
(92, 'Manager', 'HR', 1),
(93, 'HR', 'HR', 1),
(94, 'Finance', 'Finance', 1),
(95, 'Bell guy', 'Front Desk', 2),
(96, 'Chief', 'Executive', 1),
(97, 'Cook', 'Kitchen', 3),
(98, 'Concierge', 'Front Desk', 2),
(99, 'Receptionist', 'Front Desk', 2),
(100, 'Electrician', 'Maintenance', 1),
(101, 'IT Staff', 'IT', 2),
(102, 'Plumber', 'Maintenance', 1),
(103, 'Security Personnel', 'Security', 4),
(104, 'Waiter/Waitress', 'F&B', 5),
(105, 'Bartender', 'F&B', 3),
(106, 'Laundry', 'Housekeeping', 2),
(107, 'Valet', 'Front Desk', 2),
(108, 'Driver', 'Maintenance', 2);

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
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `recipe_name` varchar(100) NOT NULL,
  `category` enum('Breakfast','Lunch','Dinner','Appetizer','Main Course','Dessert','Beverage') DEFAULT 'Main Course',
  `instructions` text NOT NULL,
  `preparation_time` int(11) DEFAULT NULL COMMENT 'Time in minutes',
  `price` decimal(10,2) DEFAULT 0.00,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `recipe_name`, `category`, `instructions`, `preparation_time`, `price`, `is_active`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'Beef Nilaga', 'Main Course', 'Boil beef with potatoes, corn, and cabbage until tender.', 90, 250.00, 1, 1, '2025-09-16 13:01:59', '2025-09-16 13:04:31'),
(2, 'Tinolang Manok', 'Main Course', 'Simmer chicken with ginger, papaya, and malunggay leaves.', 60, 200.00, 1, 2, '2025-09-16 13:01:59', '2025-09-16 13:04:36'),
(3, 'Sinigang na Baboy', 'Main Course', 'Cook pork in tamarind broth with vegetables.', 75, 240.00, 1, 3, '2025-09-16 13:01:59', '2025-09-16 13:04:43'),
(4, 'Bulalo', 'Main Course', 'Boil beef shank with marrow, corn, and vegetables.', 120, 320.00, 1, 4, '2025-09-16 13:01:59', '2025-09-16 13:04:47'),
(5, 'Chicken Sopas', 'Breakfast', 'Cook macaroni soup with chicken and milk.', 45, 180.00, 1, 5, '2025-09-16 13:01:59', '2025-09-16 13:04:59'),
(6, 'Pancit Canton', 'Breakfast', 'Stir-fry noodles with pork, shrimp, and vegetables.', 45, 180.00, 1, 6, '2025-09-16 13:01:59', '2025-09-16 13:05:06'),
(7, 'Pancit Bihon', 'Breakfast', 'Stir-fry bihon noodles with chicken and vegetables.', 40, 170.00, 1, 7, '2025-09-16 13:01:59', '2025-09-16 13:05:21'),
(8, 'Pancit Malabon', 'Breakfast', 'Rice noodles topped with seafood sauce and egg.', 50, 220.00, 1, 8, '2025-09-16 13:01:59', '2025-09-16 13:05:26'),
(9, 'Spaghetti Filipino Style', 'Breakfast', 'Cook sweet-style spaghetti sauce with hotdogs and cheese.', 50, 150.00, 1, 9, '2025-09-16 13:01:59', '2025-09-16 13:05:34'),
(10, 'Carbonara', 'Breakfast', 'Creamy pasta with bacon and white sauce.', 40, 220.00, 1, 10, '2025-09-16 13:01:59', '2025-09-16 13:05:39'),
(11, 'Chicken Adobo', 'Main Course', 'Simmer chicken in soy sauce, vinegar, and garlic.', 60, 200.00, 1, 11, '2025-09-16 13:01:59', '2025-09-16 13:05:46'),
(12, 'Pork Adobo', 'Main Course', 'Simmer pork belly in soy sauce, vinegar, and garlic.', 65, 220.00, 1, 12, '2025-09-16 13:01:59', '2025-09-16 13:05:50'),
(13, 'Kare-Kare', 'Main Course', 'Stew oxtail and vegetables with peanut sauce.', 90, 280.00, 1, 13, '2025-09-16 13:01:59', '2025-09-16 13:05:53'),
(14, 'Lechon Kawali', 'Main Course', 'Deep fry pork belly until crispy.', 50, 260.00, 1, 14, '2025-09-16 13:01:59', '2025-09-16 13:05:57'),
(15, 'Crispy Pata', 'Main Course', 'Deep fry pork leg until crispy.', 120, 480.00, 1, 15, '2025-09-16 13:01:59', '2025-09-16 13:06:01'),
(16, 'Beef Tapa', 'Main Course', 'Marinate beef strips and serve with rice and egg.', 40, 180.00, 1, 16, '2025-09-16 13:01:59', '2025-09-16 13:06:06'),
(17, 'Tocino', 'Dinner', 'Pan-fry sweet cured pork and serve with garlic rice.', 30, 170.00, 1, 17, '2025-09-16 13:01:59', '2025-09-16 13:06:11'),
(18, 'Longganisa', 'Dinner', 'Fry pork sausages served with rice and egg.', 30, 160.00, 1, 18, '2025-09-16 13:01:59', '2025-09-16 13:06:16'),
(19, 'Daing na Bangus', 'Lunch', 'Marinate milkfish and fry until crispy.', 35, 200.00, 1, 19, '2025-09-16 13:01:59', '2025-09-16 13:06:19'),
(20, 'Chicken Inasal', 'Lunch', 'Grill marinated chicken leg with annatto oil.', 60, 210.00, 1, 20, '2025-09-16 13:01:59', '2025-09-16 13:06:23'),
(21, 'Grilled Tilapia', 'Lunch', 'Grill seasoned tilapia wrapped in banana leaf.', 40, 220.00, 1, 21, '2025-09-16 13:01:59', '2025-09-16 13:06:26'),
(22, 'Garlic Butter Shrimp', 'Main Course', 'Sauté shrimp in garlic butter sauce.', 30, 250.00, 1, 22, '2025-09-16 13:01:59', '2025-09-16 13:06:30'),
(23, 'Calamares', 'Appetizer', 'Batter-fry squid rings until golden brown.', 25, 200.00, 1, 23, '2025-09-16 13:01:59', '2025-09-16 13:06:36'),
(24, 'Sweet and Sour Fish Fillet', 'Main Course', 'Deep fry fish fillet and coat with sweet-sour sauce.', 35, 240.00, 1, 24, '2025-09-16 13:01:59', '2025-09-16 13:06:40'),
(25, 'Seafood Kare-Kare', 'Lunch', 'Stew seafood with peanut sauce and vegetables.', 70, 300.00, 1, 25, '2025-09-16 13:01:59', '2025-09-16 13:06:45'),
(26, 'Ginataang Sitaw at Kalabasa', 'Lunch', 'Cook sitaw and squash in coconut milk.', 40, 180.00, 1, 26, '2025-09-16 13:01:59', '2025-09-16 13:07:00'),
(27, 'Pinakbet', 'Main Course', 'Stew mixed vegetables with bagoong.', 45, 170.00, 1, 27, '2025-09-16 13:01:59', '2025-09-16 13:07:04'),
(28, 'Laing', 'Main Course', 'Simmer dried taro leaves in coconut milk.', 60, 200.00, 1, 28, '2025-09-16 13:01:59', '2025-09-16 13:07:08'),
(29, 'Chop Suey', 'Main Course', 'Stir-fry mixed vegetables with chicken and shrimp.', 35, 190.00, 1, 29, '2025-09-16 13:01:59', '2025-09-16 13:07:12'),
(30, 'Adobong Kangkong', 'Main Course', 'Cook kangkong with soy sauce and vinegar.', 25, 140.00, 1, 30, '2025-09-16 13:01:59', '2025-09-16 13:07:16'),
(31, 'Halo-Halo', 'Dessert', 'Mix shaved ice with sweetened fruits and leche flan.', 20, 150.00, 1, 31, '2025-09-16 13:01:59', '2025-09-16 13:01:59'),
(32, 'Leche Flan', 'Dessert', 'Steam custard topped with caramel.', 45, 120.00, 1, 32, '2025-09-16 13:01:59', '2025-09-16 13:01:59'),
(33, 'Bibingka', 'Dessert', 'Bake rice cake topped with salted egg and cheese.', 40, 140.00, 1, 33, '2025-09-16 13:01:59', '2025-09-16 13:01:59'),
(34, 'Puto Bumbong', 'Dessert', 'Steam purple rice cake topped with coconut.', 30, 120.00, 1, 34, '2025-09-16 13:01:59', '2025-09-16 13:01:59'),
(35, 'Turon', 'Dessert', 'Wrap banana and jackfruit in lumpia wrapper and fry.', 25, 100.00, 1, 35, '2025-09-16 13:01:59', '2025-09-16 13:01:59'),
(36, 'Iced Tea', 'Beverage', 'Brew tea and serve chilled with lemon.', 10, 50.00, 1, 36, '2025-09-16 13:01:59', '2025-09-16 13:01:59'),
(37, 'Calamansi Juice', 'Beverage', 'Mix calamansi juice with sugar and water.', 10, 60.00, 1, 37, '2025-09-16 13:01:59', '2025-09-16 13:01:59'),
(38, 'Mango Shake', 'Beverage', 'Blend mango with milk and ice.', 10, 90.00, 1, 38, '2025-09-16 13:01:59', '2025-09-16 13:01:59'),
(39, 'Buko Juice', 'Beverage', 'Serve chilled coconut water with meat.', 5, 70.00, 1, 39, '2025-09-16 13:01:59', '2025-09-16 13:01:59'),
(40, 'Sago Gulaman', 'Beverage', 'Mix brown sugar syrup with sago and gulaman.', 15, 80.00, 1, 40, '2025-09-16 13:01:59', '2025-09-16 13:01:59'),
(41, 'Fried Chicken', 'Main Course', 'Deep fry marinated chicken until golden.', 35, 200.00, 1, 41, '2025-09-16 13:01:59', '2025-09-16 13:07:21'),
(42, 'Menudo', 'Main Course', 'Cook pork, liver, and vegetables in tomato sauce.', 60, 220.00, 1, 42, '2025-09-16 13:01:59', '2025-09-16 13:07:24'),
(43, 'Caldereta', 'Main Course', 'Stew beef in tomato sauce with vegetables.', 90, 260.00, 1, 43, '2025-09-16 13:01:59', '2025-09-16 13:07:28'),
(44, 'Afritada', 'Main Course', 'Cook chicken with potatoes and bell peppers in tomato sauce.', 50, 210.00, 1, 44, '2025-09-16 13:01:59', '2025-09-16 13:07:43'),
(45, 'Paksiw na Lechon', 'Main Course', 'Stew leftover lechon in vinegar sauce.', 45, 240.00, 1, 45, '2025-09-16 13:01:59', '2025-09-16 13:07:47'),
(46, 'Sisig', 'Appetizer', 'Sizzle chopped pork face with onions and chili.', 40, 220.00, 1, 46, '2025-09-16 13:01:59', '2025-09-16 13:01:59'),
(47, 'Tokwa at Baboy', 'Appetizer', 'Mix fried tofu and pork ears with soy-vinegar sauce.', 30, 180.00, 1, 47, '2025-09-16 13:01:59', '2025-09-16 13:01:59'),
(48, 'Lumpiang Shanghai', 'Appetizer', 'Fry ground pork spring rolls.', 35, 160.00, 1, 48, '2025-09-16 13:01:59', '2025-09-16 13:01:59'),
(49, 'Kilawin', 'Appetizer', 'Marinate fish in vinegar with ginger and chili.', 25, 200.00, 1, 49, '2025-09-16 13:01:59', '2025-09-16 13:01:59'),
(50, 'Ensaladang Mangga', 'Appetizer', 'Mix green mango with bagoong and onions.', 15, 120.00, 1, 50, '2025-09-16 13:01:59', '2025-09-16 13:01:59');

-- --------------------------------------------------------

--
-- Table structure for table `recipe_ingredients`
--

CREATE TABLE `recipe_ingredients` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `inventory_item_id` int(11) NOT NULL,
  `quantity_needed` decimal(10,2) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recruitment`
--

CREATE TABLE `recruitment` (
  `id` int(10) UNSIGNED NOT NULL,
  `candidate_id` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `birth_date` date NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `applied_position` varchar(100) DEFAULT NULL,
  `applied_date` datetime DEFAULT current_timestamp(),
  `status` enum('Consider','Rejected','Fit to the Job') NOT NULL DEFAULT 'Consider',
  `interview_datetime` datetime DEFAULT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `benefits` varchar(255) DEFAULT NULL,
  `salary` varchar(255) DEFAULT NULL,
  `interview_result` varchar(20) DEFAULT NULL,
  `assessment_result` varchar(20) DEFAULT NULL,
  `assessment_retake` tinyint(1) DEFAULT 0,
  `stage` varchar(50) DEFAULT 'Resume Review'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recruitment`
--

INSERT INTO `recruitment` (`id`, `candidate_id`, `first_name`, `last_name`, `birth_date`, `email`, `phone`, `address`, `applied_position`, `applied_date`, `status`, `interview_datetime`, `resume`, `benefits`, `salary`, `interview_result`, `assessment_result`, `assessment_retake`, `stage`) VALUES
(1, 'CAND-0001', 'Sabel', 'Damirai', '2004-02-05', 'sabeld@gmail.com', '09665525671', 'sjdm - Bulacan', 'Bartender', '2025-09-13 18:37:15', 'Consider', '2025-09-14 10:00:00', 'uploads/resumes/1757521961_Full-Hotel-Database.pdf', NULL, NULL, 'Passed', 'Passed', 0, 'Interview Scheduled'),
(6, 'CAND-0002', 'Anna', 'Lisa', '2000-02-01', 'annalisa@gmail.com', '09312334451', 'sjdm - Bulacan', 'Security Personnel', '2025-09-15 13:40:41', '', NULL, 'uploads/resumes/1757513628_Full-Hotel-Database.pdf', NULL, NULL, NULL, NULL, 0, 'Resume Review');

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
-- Table structure for table `restaurant_orders`
--

CREATE TABLE `restaurant_orders` (
  `order_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `table_number` varchar(10) DEFAULT NULL,
  `order_type` enum('dine_in','takeaway','buffet') DEFAULT 'dine_in',
  `subtotal_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','in_progress','served','paid','cancelled','refunded') DEFAULT 'pending',
  `payment_method` enum('cash','card','room_charge','gcash','other') DEFAULT 'cash',
  `transaction_id` varchar(64) DEFAULT NULL,
  `staff_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurant_orders`
--

INSERT INTO `restaurant_orders` (`order_id`, `guest_id`, `table_number`, `order_type`, `subtotal_amount`, `tax_amount`, `total_amount`, `order_date`, `status`, `payment_method`, `transaction_id`, `staff_id`, `notes`) VALUES
(1, 1, 't3', 'dine_in', 500.00, 60.00, 560.00, '2025-09-23 09:58:54', 'pending', 'cash', 'RST-20250923-115854-023EAF', 1, 'not'),
(2, 9, 't3', 'dine_in', 0.00, 0.00, 0.00, '2025-09-26 16:25:57', 'pending', 'cash', 'RST-20250926-182557-F24251', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_order_items`
--

CREATE TABLE `restaurant_order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `category` enum('Appetizers','Main Course','Desserts','Beverages','Sides','Others') NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `special_instructions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurant_order_items`
--

INSERT INTO `restaurant_order_items` (`id`, `order_id`, `item_name`, `category`, `quantity`, `unit_price`, `total_price`, `special_instructions`) VALUES
(1, 1, 'Beef Nilaga', 'Main Course', 1, 250.00, 250.00, ''),
(2, 2, 'Item 1', 'Main Course', 1, 0.00, 0.00, 'no spicy');

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
-- Table structure for table `room_dining_orders`
--

CREATE TABLE `room_dining_orders` (
  `order_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `order_type` enum('breakfast','lunch','dinner','snacks','beverages') NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `delivery_time` datetime DEFAULT NULL,
  `status` enum('pending','preparing','out_for_delivery','delivered','cancelled') DEFAULT 'pending',
  `staff_id` int(11) NOT NULL,
  `special_instructions` text DEFAULT NULL,
  `delivery_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room_dining_order_items`
--

CREATE TABLE `room_dining_order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `category` enum('Breakfast','Lunch','Dinner','Snacks','Beverages','Desserts') NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `special_instructions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`);

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
-- Indexes for table `restaurant_orders`
--
ALTER TABLE `restaurant_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `uniq_restaurant_txn` (`transaction_id`);

--
-- Indexes for table `restaurant_order_items`
--
ALTER TABLE `restaurant_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

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
  MODIFY `billing_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `billing_items`
--
ALTER TABLE `billing_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `guest_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `restaurant_orders`
--
ALTER TABLE `restaurant_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `restaurant_order_items`
--
ALTER TABLE `restaurant_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2025 at 02:09 PM
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
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `booking_type` enum('reservation','walk-in') DEFAULT NULL,
  `status` enum('pending','confirmed','checked_in','checked_out') DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `check_in` timestamp NULL DEFAULT NULL,
  `check_out` timestamp NULL DEFAULT NULL,
  `booking_date` date NOT NULL
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guests`
--

INSERT INTO `guests` (`guest_id`, `first_name`, `last_name`, `email`, `first_phone`, `second_phone`, `status`, `created_at`, `updated_at`) VALUES
(1, 'John', 'Doe', 'john.doe@example.com', '09170000001', '09220000001', 'active', '2025-10-07 15:08:54', '2025-10-07 15:08:54'),
(2, 'Jane', 'Smith', 'jane.smith@example.com', '09170000002', '09220000002', 'active', '2025-10-07 15:08:54', '2025-10-07 15:08:54'),
(3, 'Michael', 'Johnson', 'michael.johnson@example.com', '09170000003', '09220000003', 'active', '2025-10-07 15:08:54', '2025-10-07 15:08:54'),
(4, 'Emily', 'Brown', 'emily.brown@example.com', '09170000004', '09220000004', 'active', '2025-10-07 15:08:54', '2025-10-07 15:08:54'),
(5, 'David', 'Williams', 'david.williams@example.com', '09170000005', '09220000005', 'active', '2025-10-07 15:08:54', '2025-10-07 15:08:54'),
(6, 'Sarah', 'Miller', 'sarah.miller@example.com', '09170000006', '09220000006', 'active', '2025-10-07 15:08:54', '2025-10-07 15:08:54'),
(7, 'James', 'Davis', 'james.davis@example.com', '09170000007', '09220000007', 'active', '2025-10-07 15:08:54', '2025-10-07 15:08:54'),
(8, 'Olivia', 'Garcia', 'olivia.garcia@example.com', '09170000008', '09220000008', 'active', '2025-10-07 15:08:54', '2025-10-07 15:08:54'),
(9, 'Robert', 'Rodriguez', 'robert.rodriguez@example.com', '09170000009', '09220000009', 'active', '2025-10-07 15:08:54', '2025-10-07 15:08:54'),
(10, 'Sophia', 'Martinez', 'sophia.martinez@example.com', '09170000010', '09220000010', 'active', '2025-10-07 15:08:54', '2025-10-07 15:08:54'),
(11, 'William', 'Hernandez', 'william.hernandez@example.com', '09170000011', '09220000011', 'active', '2025-10-07 15:08:54', '2025-10-07 15:08:54'),
(12, 'Isabella', 'Lopez', 'isabella.lopez@example.com', '09170000012', '09220000012', 'active', '2025-10-07 15:08:54', '2025-10-07 15:08:54'),
(13, 'Charles', 'Gonzalez', 'charles.gonzalez@example.com', '09170000013', '09220000013', 'active', '2025-10-07 15:08:54', '2025-10-07 15:08:54'),
(14, 'Amelia', 'Wilson', 'amelia.wilson@example.com', '09170000014', '09220000014', 'active', '2025-10-07 15:08:54', '2025-10-07 15:08:54'),
(15, 'Joseph', 'Anderson', 'joseph.anderson@example.com', '09170000015', '09220000015', 'active', '2025-10-07 15:08:54', '2025-10-07 15:08:54'),
(16, 'Mia', 'Thomas', 'mia.thomas@example.com', '09170000016', '09220000016', 'active', '2025-10-07 15:08:54', '2025-10-07 15:08:54'),
(17, 'Thomas', 'Taylor', 'thomas.taylor@example.com', '09170000017', '09220000017', 'active', '2025-10-07 15:08:54', '2025-10-07 15:08:54'),
(18, 'Ella', 'Moore', 'ella.moore@example.com', '09170000018', '09220000018', 'active', '2025-10-07 15:08:54', '2025-10-07 15:08:54'),
(19, 'Daniel', 'Jackson', 'daniel.jackson@example.com', '09170000019', '09220000019', 'active', '2025-10-07 15:08:54', '2025-10-07 15:08:54'),
(20, 'Ava', 'Martin', 'ava.martin@example.com', '09170000020', '09220000020', 'active', '2025-10-07 15:08:54', '2025-10-07 15:08:54'),
(21, 'John', 'Doe', 'john.doe@example.com', '09170000001', '09220000001', 'active', '2025-10-07 15:08:13', '2025-10-07 15:08:13'),
(22, 'Jane', 'Smith', 'jane.smith@example.com', '09170000002', '09220000002', 'active', '2025-10-07 15:08:13', '2025-10-07 15:08:13'),
(23, 'Michael', 'Johnson', 'michael.johnson@example.com', '09170000003', '09220000003', 'active', '2025-10-07 15:08:13', '2025-10-07 15:08:13'),
(24, 'Emily', 'Brown', 'emily.brown@example.com', '09170000004', '09220000004', 'active', '2025-10-07 15:08:13', '2025-10-07 15:08:13'),
(25, 'David', 'Williams', 'david.williams@example.com', '09170000005', '09220000005', 'active', '2025-10-07 15:08:13', '2025-10-07 15:08:13'),
(26, 'Sarah', 'Miller', 'sarah.miller@example.com', '09170000006', '09220000006', 'active', '2025-10-07 15:08:13', '2025-10-07 15:08:13'),
(27, 'James', 'Davis', 'james.davis@example.com', '09170000007', '09220000007', 'active', '2025-10-07 15:08:13', '2025-10-07 15:08:13'),
(28, 'Olivia', 'Garcia', 'olivia.garcia@example.com', '09170000008', '09220000008', 'active', '2025-10-07 15:08:13', '2025-10-07 15:08:13'),
(29, 'Robert', 'Rodriguez', 'robert.rodriguez@example.com', '09170000009', '09220000009', 'active', '2025-10-07 15:08:13', '2025-10-07 15:08:13'),
(30, 'Sophia', 'Martinez', 'sophia.martinez@example.com', '09170000010', '09220000010', 'active', '2025-10-07 15:08:13', '2025-10-07 15:08:13'),
(31, 'William', 'Hernandez', 'william.hernandez@example.com', '09170000011', '09220000011', 'active', '2025-10-07 15:08:13', '2025-10-07 15:08:13'),
(32, 'Isabella', 'Lopez', 'isabella.lopez@example.com', '09170000012', '09220000012', 'active', '2025-10-07 15:08:13', '2025-10-07 15:08:13'),
(33, 'Charles', 'Gonzalez', 'charles.gonzalez@example.com', '09170000013', '09220000013', 'active', '2025-10-07 15:08:13', '2025-10-07 15:08:13'),
(34, 'Amelia', 'Wilson', 'amelia.wilson@example.com', '09170000014', '09220000014', 'active', '2025-10-07 15:08:13', '2025-10-07 15:08:13'),
(35, 'Joseph', 'Anderson', 'joseph.anderson@example.com', '09170000015', '09220000015', 'active', '2025-10-07 15:08:13', '2025-10-07 15:08:13'),
(36, 'Mia', 'Thomas', 'mia.thomas@example.com', '09170000016', '09220000016', 'active', '2025-10-07 15:08:13', '2025-10-07 15:08:13'),
(37, 'Thomas', 'Taylor', 'thomas.taylor@example.com', '09170000017', '09220000017', 'active', '2025-10-07 15:08:13', '2025-10-07 15:08:13'),
(38, 'Ella', 'Moore', 'ella.moore@example.com', '09170000018', '09220000018', 'active', '2025-10-07 15:08:13', '2025-10-07 15:08:13'),
(39, 'Daniel', 'Jackson', 'daniel.jackson@example.com', '09170000019', '09220000019', 'active', '2025-10-07 15:08:13', '2025-10-07 15:08:13'),
(40, 'Ava', 'Martin', 'ava.martin@example.com', '09170000020', '09220000020', 'active', '2025-10-07 15:08:13', '2025-10-07 15:08:13');

--
-- Triggers `guests`
--
DELIMITER $$
CREATE TRIGGER `guests_before_insert` BEFORE INSERT ON `guests` FOR EACH ROW SET NEW.created_at = CURRENT_TIMESTAMP
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `guests_before_update` BEFORE UPDATE ON `guests` FOR EACH ROW SET NEW.updated_at = CURRENT_TIMESTAMP
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `guest_billing`
--

CREATE TABLE `guest_billing` (
  `id` int(11) NOT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `guest_name` varchar(255) DEFAULT NULL,
  `order_type` enum('Restaurant','Mini Bar','Lounge Bar','Gift Store','Room Service') DEFAULT NULL,
  `item` varchar(255) DEFAULT NULL,
  `order_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `payment_option` enum('Paid','To be billed','Refunded','Partial Payment') NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `partial_payment` decimal(10,2) DEFAULT 0.00,
  `remaining_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `remaining_total` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `total_amount` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guest_billing`
--

INSERT INTO `guest_billing` (`id`, `guest_id`, `guest_name`, `order_type`, `item`, `order_id`, `amount`, `quantity`, `payment_option`, `payment_method`, `partial_payment`, `remaining_amount`, `remaining_total`, `created_at`, `updated_at`, `total_amount`) VALUES
(32, 2, 'Jane Smith', 'Gift Store', 'Fragrant Candle', 6989, 350.00, 1, 'Partial Payment', 'Cash', 400.00, 2000.00, 2000, '2025-10-09 00:18:17', '2025-10-09 00:18:17', 2400.00),
(33, 2, 'Jane Smith', 'Gift Store', 'Handmade Bracelet', 6989, 300.00, 1, 'Partial Payment', 'Cash', 400.00, 2000.00, 2000, '2025-10-09 00:18:17', '2025-10-09 00:18:17', 2400.00),
(34, 2, 'Jane Smith', 'Gift Store', 'Limited Edition Hotel Calendar', 6989, 250.00, 1, 'Partial Payment', 'Cash', 400.00, 2000.00, 2000, '2025-10-09 00:18:17', '2025-10-09 00:18:17', 2400.00),
(35, 2, 'Jane Smith', 'Gift Store', 'La Vista Chocolate Gift Box', 6989, 1500.00, 1, 'Partial Payment', 'Cash', 400.00, 2000.00, 2000, '2025-10-09 00:18:17', '2025-10-09 00:18:17', 2400.00),
(60, 1, 'John Doe', 'Mini Bar', 'Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g', 3489, 70.00, 1, 'Partial Payment', 'Cash', 40.00, 600.00, 600, '2025-10-09 00:45:02', '2025-10-09 00:45:02', 640.00),
(61, 1, 'John Doe', 'Mini Bar', 'Candy Pack', 3489, 50.00, 1, 'Partial Payment', 'Cash', 40.00, 600.00, 600, '2025-10-09 00:45:02', '2025-10-09 00:45:02', 640.00),
(62, 1, 'John Doe', 'Mini Bar', 'Jack n Jill Piattos Sour Cream', 3489, 55.00, 1, 'Partial Payment', 'Cash', 40.00, 600.00, 600, '2025-10-09 00:45:02', '2025-10-09 00:45:02', 640.00),
(63, 1, 'John Doe', 'Mini Bar', 'KitKat Mini Pack', 3489, 85.00, 1, 'Partial Payment', 'Cash', 40.00, 600.00, 600, '2025-10-09 00:45:02', '2025-10-09 00:45:02', 640.00),
(64, 1, 'John Doe', 'Mini Bar', 'Lays', 3489, 80.00, 1, 'Partial Payment', 'Cash', 40.00, 600.00, 600, '2025-10-09 00:45:02', '2025-10-09 00:45:02', 640.00),
(65, 1, 'John Doe', 'Mini Bar', 'Lemonade Bottle', 3489, 80.00, 1, 'Partial Payment', 'Cash', 40.00, 600.00, 600, '2025-10-09 00:45:02', '2025-10-09 00:45:02', 640.00),
(66, 1, 'John Doe', 'Mini Bar', 'Jack \'N Jill Vcut Spicy Barbeque ', 3489, 130.00, 1, 'Partial Payment', 'Cash', 40.00, 600.00, 600, '2025-10-09 00:45:02', '2025-10-09 00:45:02', 640.00),
(67, 1, 'John Doe', 'Mini Bar', 'Growers Mixed Nuts', 3489, 90.00, 1, 'Partial Payment', 'Cash', 40.00, 600.00, 600, '2025-10-09 00:45:02', '2025-10-09 00:45:02', 640.00),
(68, 2, 'Jane Smith', 'Gift Store', 'Fridge Magnet Set', 9304, 180.00, 1, 'Partial Payment', 'Cash', 80.00, 900.00, 900, '2025-10-09 01:08:31', '2025-10-09 01:08:31', 980.00),
(69, 2, 'Jane Smith', 'Gift Store', 'Handmade Bracelet', 9304, 300.00, 1, 'Partial Payment', 'Cash', 80.00, 900.00, 900, '2025-10-09 01:08:31', '2025-10-09 01:08:31', 980.00),
(70, 2, 'Jane Smith', 'Gift Store', 'Ferrero Rocher Chocolate Box', 9304, 250.00, 1, 'Partial Payment', 'Cash', 80.00, 900.00, 900, '2025-10-09 01:08:31', '2025-10-09 01:08:31', 980.00),
(71, 2, 'Jane Smith', 'Gift Store', 'Limited Edition Hotel Calendar', 9304, 250.00, 1, 'Partial Payment', 'Cash', 80.00, 900.00, 900, '2025-10-09 01:08:31', '2025-10-09 01:08:31', 980.00),
(72, 6, 'Sarah Miller', 'Restaurant', 'Bulalo', 9961, 320.00, 1, 'To be billed', 'Cash', 0.00, 320.00, 920, '2025-10-09 01:15:45', '2025-10-09 01:15:45', 0.00),
(73, 6, 'Sarah Miller', 'Restaurant', 'Tinolang Manok', 9961, 200.00, 1, 'To be billed', 'Cash', 0.00, 200.00, 920, '2025-10-09 01:15:45', '2025-10-09 01:15:45', 0.00),
(74, 6, 'Sarah Miller', 'Restaurant', 'Cheesecake', 9961, 180.00, 1, 'To be billed', 'Cash', 0.00, 180.00, 920, '2025-10-09 01:15:45', '2025-10-09 01:15:45', 0.00),
(75, 6, 'Sarah Miller', 'Restaurant', 'Pork Adobo', 9961, 220.00, 1, 'To be billed', 'Cash', 0.00, 220.00, 920, '2025-10-09 01:15:45', '2025-10-09 01:15:45', 0.00),
(76, 9, 'Robert Rodriguez', 'Lounge Bar', 'Cocktail - Daiquiri, Chivas Regal 12 Years, Cocktail - Margarita', 8219, 0.00, 0, 'To be billed', NULL, 0.00, 7480.00, 0, '2025-10-09 01:16:46', '2025-10-09 01:16:46', 7480.00),
(77, 2, 'Jane Smith', 'Room Service', 'Bulalo', 9423, 320.00, 1, 'Partial Payment', 'Paymaya', 82.96, 237.04, 1000, '2025-10-09 10:56:33', '2025-10-09 10:56:33', 0.00),
(78, 2, 'Jane Smith', 'Room Service', 'Tinolang Manok', 9423, 200.00, 1, 'Partial Payment', 'Paymaya', 51.85, 148.15, 1000, '2025-10-09 10:56:33', '2025-10-09 10:56:33', 0.00),
(79, 2, 'Jane Smith', 'Room Service', 'Chicken Sopas', 9423, 180.00, 1, 'Partial Payment', 'Paymaya', 46.67, 133.33, 1000, '2025-10-09 10:56:33', '2025-10-09 10:56:33', 0.00),
(80, 2, 'Jane Smith', 'Room Service', 'Pancit Bihon', 9423, 170.00, 1, 'Partial Payment', 'Paymaya', 44.07, 125.93, 1000, '2025-10-09 10:56:33', '2025-10-09 10:56:33', 0.00),
(81, 2, 'Jane Smith', 'Room Service', 'Crispy Pata', 9423, 480.00, 1, 'Partial Payment', 'Paymaya', 124.44, 355.55, 1000, '2025-10-09 10:56:33', '2025-10-09 10:56:33', 0.00),
(82, 2, 'Jane Smith', 'Room Service', 'Pork Adobo', 8115, 220.00, 1, 'Refunded', 'Cash', 30.34, 810.34, 810, '2025-10-10 18:51:07', '2025-10-10 19:33:16', 0.00),
(83, 2, 'Jane Smith', 'Room Service', 'Kare-Kare', 8115, 280.00, 1, 'Refunded', 'Cash', 38.62, 568.96, 569, '2025-10-10 18:51:07', '2025-10-10 19:37:45', 0.00),
(84, 2, 'Jane Smith', 'Room Service', 'Crispy Pata', 8115, 480.00, 1, 'Refunded', 'Cash', 66.21, 155.17, 155, '2025-10-10 18:51:07', '2025-10-10 19:46:22', 0.00),
(85, 2, 'Jane Smith', 'Room Service', 'Cheesecake', 8115, 180.00, 1, 'Partial Payment', 'Cash', 24.83, 155.17, 1000, '2025-10-10 18:51:07', '2025-10-10 18:51:07', 0.00),
(86, 2, 'Jane Smith', 'Gift Store', 'Fridge Magnet Set', 8894, 180.00, 1, 'To be billed', NULL, 0.00, 830.00, 830, '2025-10-10 19:16:26', '2025-10-10 19:16:26', 830.00),
(87, 2, 'Jane Smith', 'Gift Store', 'Fragrant Candle', 8894, 350.00, 1, 'To be billed', NULL, 0.00, 830.00, 830, '2025-10-10 19:16:26', '2025-10-10 19:16:26', 830.00),
(88, 2, 'Jane Smith', 'Gift Store', 'Handmade Bracelet', 8894, 300.00, 1, 'To be billed', NULL, 0.00, 830.00, 830, '2025-10-10 19:16:26', '2025-10-10 19:16:26', 830.00),
(89, 2, 'Jane Smith', 'Lounge Bar', 'Cocktail - Margarita, Cocktail - Daiquiri', 1998, 0.00, 0, 'To be billed', NULL, 0.00, 580.00, 0, '2025-10-10 19:16:46', '2025-10-10 19:16:46', 580.00),
(90, 4, 'Emily Brown', 'Lounge Bar', 'Cocktail - Margarita, Fruit Platter, Cocktail Straw Set', 6128, 0.00, 0, 'Partial Payment', 'Cash', 30.00, 473.00, 0, '2025-10-10 19:16:59', '2025-10-10 19:16:59', 503.00),
(91, 2, 'Jane Smith', 'Restaurant', 'Tinolang Manok', 7008, 200.00, 1, 'Partial Payment', 'Cash', 10.81, 189.19, 700, '2025-10-10 20:02:13', '2025-10-10 20:02:13', 0.00),
(92, 2, 'Jane Smith', 'Restaurant', 'Bulalo', 7008, 320.00, 1, 'Partial Payment', 'Cash', 17.30, 302.70, 700, '2025-10-10 20:02:13', '2025-10-10 20:02:13', 0.00),
(93, 2, 'Jane Smith', 'Restaurant', 'Pork Adobo', 7008, 220.00, 1, 'Partial Payment', 'Cash', 11.89, 208.11, 700, '2025-10-10 20:02:13', '2025-10-10 20:02:13', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `item_id` int(11) NOT NULL,
  `item` varchar(255) NOT NULL,
  `category` enum('Hotel Supplies','Cleaning & Sanitation','Utility Products','Office Supplies','Kitchen Equipment','Furniture & Fixtures','Toiletries','Laundry & Linen','Beverage','Meat','Seafood','Vegetable','Fruit','Dairy','Seasoning','Grain','Beverage','Spice','Furniture & Fixtures','Electrical & Lighting','Plumbing Supplies','HVAC & Equipment Parts','Paint & Repair Materials','Tools & Hardware','Others','Mini Bar','Lounge Bar','Gift Store') NOT NULL,
  `quantity_in_stock` int(11) NOT NULL DEFAULT 0,
  `used_qty` int(11) DEFAULT 0,
  `wasted_qty` int(11) DEFAULT 0,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `inspected_by` varchar(255) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `unit` varchar(50) DEFAULT '-'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`item_id`, `item`, `category`, `quantity_in_stock`, `used_qty`, `wasted_qty`, `unit_price`, `inspected_by`, `last_updated`, `created_at`, `unit`) VALUES
(1204, 'Coffee Beans', 'Beverage', 0, 0, 0, 250.00, 'System', '2025-10-08 11:29:16', '2025-10-04 12:17:14', 'g'),
(1215, 'Water', 'Others', 50000, 0, 0, 5.00, 'System', '2025-10-08 11:08:56', '2025-10-04 12:17:14', 'L'),
(1266, 'Tote Bag', 'Gift Store', 50000, 0, 0, 200.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1267, 'Coffee Mug', 'Gift Store', 50000, 0, 0, 250.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1268, 'Notebook', 'Gift Store', 50000, 0, 0, 180.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1269, 'Pen Set', 'Gift Store', 0, 0, 0, 120.00, 'System', '2025-10-08 12:10:32', '2025-10-02 13:04:30', 'pcs'),
(1270, 'Local Snack Pack', 'Gift Store', 50000, 0, 0, 300.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1271, 'Mini Photo Frame', 'Gift Store', 50000, 0, 0, 220.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1272, 'Fragrant Candle', 'Gift Store', 49984, 0, 0, 350.00, 'System', '2025-10-10 11:16:26', '2025-10-02 13:04:30', 'pcs'),
(1273, 'Souvenir T-shirt', 'Gift Store', 50000, 0, 0, 400.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1274, 'Fridge Magnet Set', 'Gift Store', 49990, 0, 0, 180.00, 'System', '2025-10-10 11:16:26', '2025-10-02 13:04:30', 'pcs'),
(1275, 'Local Art Mini Canvas', 'Gift Store', 50000, 0, 0, 500.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1276, 'Ferrero Rocher Chocolate Box', 'Gift Store', 49976, 0, 0, 250.00, 'System', '2025-10-08 17:08:31', '2025-10-02 13:04:30', 'pcs'),
(1277, 'Decorative Coasters', 'Gift Store', 50000, 0, 0, 150.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1278, 'Handmade Bracelet', 'Gift Store', 49981, 0, 0, 300.00, 'System', '2025-10-10 11:16:26', '2025-10-02 13:04:30', 'pcs'),
(1279, 'Key Holder', 'Gift Store', 50000, 0, 0, 180.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1280, 'Miniature Figurine', 'Gift Store', 50000, 0, 0, 400.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1281, 'Local Snack Box', 'Gift Store', 50000, 0, 0, 350.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1282, 'Souvenir Pen', 'Gift Store', 50000, 0, 0, 100.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1283, 'Summit Natural Drinking Water ', 'Mini Bar', 50000, 0, 0, 50.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1284, 'Coca-Cola Original Taste Soft Drink Can', 'Mini Bar', 49999, 0, 0, 60.00, 'System', '2025-10-08 16:42:00', '2025-10-02 13:04:30', 'pcs'),
(1285, 'San Miguel Beer', 'Mini Bar', 50000, 0, 0, 120.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1286, 'Novellino - Rosso Classico', 'Mini Bar', 50000, 0, 0, 350.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1287, 'Lays', 'Mini Bar', 49998, 0, 0, 80.00, 'System', '2025-10-08 16:45:02', '2025-10-02 13:04:30', 'pcs'),
(1288, 'Toblerone', 'Mini Bar', 50000, 0, 0, 200.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1289, 'Growers Mixed Nuts', 'Mini Bar', 49999, 0, 0, 90.00, 'System', '2025-10-08 16:45:02', '2025-10-02 13:04:30', 'pcs'),
(1290, 'Minute Maid Fresh', 'Mini Bar', 50000, 0, 0, 30.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1291, 'Cobra Energy Drink ', 'Mini Bar', 49998, 0, 0, 120.00, 'System', '2025-10-08 16:42:00', '2025-10-02 13:04:30', 'pcs'),
(1292, 'Nestle Fresh Milk', 'Mini Bar', 50000, 0, 0, 40.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1293, 'Sparkling Water', 'Mini Bar', 50000, 0, 0, 80.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1294, 'Ding Dong Snack Mix', 'Mini Bar', 50000, 0, 0, 150.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1295, 'Candy Pack', 'Mini Bar', 49997, 0, 0, 50.00, 'System', '2025-10-08 16:45:02', '2025-10-02 13:04:30', 'pcs'),
(1296, 'Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g', 'Mini Bar', 49996, 0, 0, 70.00, 'System', '2025-10-08 16:45:02', '2025-10-02 13:04:30', 'pcs'),
(1297, 'Beef Jerky', 'Mini Bar', 49997, 0, 0, 200.00, 'System', '2025-10-08 16:43:32', '2025-10-02 13:04:30', 'pcs'),
(1298, 'Minute Maid Fresh Orange Juice', 'Mini Bar', 50000, 0, 0, 90.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1299, 'Lipton Green Tea Lively Fresh ', 'Mini Bar', 50000, 0, 0, 120.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1300, 'Nescafe Original ', 'Mini Bar', 50000, 0, 0, 100.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1301, 'Lemonade Bottle', 'Mini Bar', 49997, 0, 0, 80.00, 'System', '2025-10-08 16:45:02', '2025-10-02 13:04:30', 'pcs'),
(1302, 'Alaska Fruitti Yo! Strawberry Yoghurt Milk Drink ', 'Mini Bar', 49999, 0, 0, 90.00, 'System', '2025-10-08 16:43:32', '2025-10-02 13:04:30', 'pcs'),
(1303, 'Cocktail - Mojito', 'Lounge Bar', 50000, 0, 0, 250.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1304, 'Cocktail - Margarita', 'Lounge Bar', 49980, 0, 0, 300.00, 'System', '2025-10-10 11:16:59', '2025-10-02 13:04:30', 'pcs'),
(1305, 'Cocktail - Martini', 'Lounge Bar', 50000, 0, 0, 320.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1306, 'Jack Daniel\'s', 'Lounge Bar', 50000, 0, 0, 400.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1307, 'Bacardi Gold Rum', 'Lounge Bar', 50000, 0, 0, 380.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1308, 'Ginebra San Miguel Gin', 'Lounge Bar', 50000, 0, 0, 360.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1309, 'Absolut Vodka', 'Lounge Bar', 50000, 0, 0, 350.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1310, 'Red Horse Beer Bottle', 'Lounge Bar', 50000, 0, 0, 120.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1311, 'Red Wine Glass', 'Lounge Bar', 50000, 0, 0, 450.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1312, 'White Wine Glass', 'Lounge Bar', 50000, 0, 0, 450.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1313, 'Cocktail - Daiquiri', 'Lounge Bar', 49995, 0, 0, 280.00, 'System', '2025-10-10 11:16:46', '2025-10-02 13:04:30', 'pcs'),
(1314, 'Cocktail - Pina Colada', 'Lounge Bar', 50000, 0, 0, 300.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1315, 'Cocktail - Tequila Sunrise', 'Lounge Bar', 49999, 0, 0, 310.00, 'System', '2025-10-08 16:38:24', '2025-10-02 13:04:30', 'pcs'),
(1316, 'Mocktail - Virgin Mojito', 'Lounge Bar', 50000, 0, 0, 200.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1317, 'Mocktail - Sunrise', 'Lounge Bar', 50000, 0, 0, 180.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1318, 'Snack Platter', 'Lounge Bar', 50000, 0, 0, 250.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1319, 'Arla Natural Cheese Mozzarella Cheese Slices', 'Mini Bar', 49999, 0, 0, 300.00, 'System', '2025-10-08 16:43:32', '2025-10-02 13:04:30', 'pcs'),
(1320, 'Mixed Nuts Bowl', 'Lounge Bar', 50000, 0, 0, 150.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1321, 'Olives Bowl', 'Lounge Bar', 50000, 0, 0, 120.00, 'System', '2025-10-08 11:22:20', '2025-10-02 13:04:30', 'pcs'),
(1322, 'Fruit Platter', 'Lounge Bar', 49998, 0, 0, 200.00, 'System', '2025-10-10 11:16:59', '2025-10-02 13:04:30', 'pcs'),
(1323, 'Chicken', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:36:12', 'g'),
(1324, 'Papaya', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:36:12', 'g'),
(1325, 'Malunggay Leaves', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:36:12', 'g'),
(1326, 'Ginger', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:36:12', 'g'),
(1327, 'Pork', 'Meat', 0, 0, 0, 0.00, NULL, '2025-10-08 11:30:55', '2025-10-03 14:36:12', 'g'),
(1328, 'Tamarind', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:36:12', 'g'),
(1329, 'Kangkong', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:36:12', 'g'),
(1330, 'Tomato', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:36:12', 'g'),
(1331, 'Radish', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:36:12', 'g'),
(1332, 'Beef Shank', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:36:12', 'g'),
(1333, 'Corn', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:36:12', 'pcs'),
(1334, 'Cabbage', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:36:12', 'g'),
(1335, 'Marrow Bones', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:36:12', 'g'),
(1336, 'Chicken', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:36:12', 'g'),
(1337, 'Macaroni', 'Grain', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:36:12', 'g'),
(1338, 'Milk', 'Dairy', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:36:12', 'ml'),
(1339, 'Carrot', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:36:12', 'g'),
(1340, 'Onion', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:36:12', 'g'),
(1341, 'Noodles', 'Grain', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:36:12', 'g'),
(1342, 'Pork', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:07:40', '2025-10-03 14:36:12', 'g'),
(1343, 'Shrimp', 'Seafood', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:23:26', '2025-10-03 14:36:12', 'g'),
(1344, 'Cabbage', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:36:12', 'g'),
(1345, 'Carrot', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:36:12', 'g'),
(1346, 'Bihon Noodles', 'Grain', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:36:12', 'g'),
(1347, 'Chicken', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:36:12', 'g'),
(1348, 'Cabbage', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:36:12', 'g'),
(1349, 'Carrot', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:36:12', 'g'),
(1350, 'Rice Noodles', 'Grain', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:36:12', 'g'),
(1351, 'Shrimp', 'Seafood', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:36:12', 'g'),
(1352, 'Egg', 'Others', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:36:12', 'pcs'),
(1353, 'Seafood Sauce', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:36:12', 'ml'),
(1354, 'Spaghetti Noodles', 'Grain', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1355, 'Hotdog', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:07:40', '2025-10-03 14:37:23', 'g'),
(1356, 'Cheese', 'Dairy', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1357, 'Tomato Sauce', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'ml'),
(1358, 'Pasta', 'Grain', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1359, 'Bacon', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:07:40', '2025-10-03 14:37:23', 'g'),
(1360, 'Cream', 'Dairy', 49700, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:37:23', 'ml'),
(1361, 'Parmesan', 'Dairy', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1362, 'Pork Belly', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:37:23', 'g'),
(1363, 'Soy Sauce', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:37:23', 'ml'),
(1364, 'Vinegar', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:37:23', 'ml'),
(1365, 'Garlic', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1366, 'Oxtail', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:37:23', 'g'),
(1367, 'Peanut Sauce', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:37:23', 'ml'),
(1368, 'Eggplant', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1369, 'String Beans', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1370, 'Banana Heart', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1371, 'Pork Belly', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:37:23', 'g'),
(1372, 'Salt', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1373, 'Pepper', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1374, 'Oil', 'Others', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:37:23', 'ml'),
(1375, 'Pork Leg', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:37:23', 'g'),
(1376, 'Salt', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1377, 'Pepper', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1378, 'Oil', 'Others', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:37:23', 'ml'),
(1379, 'Beef', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:07:40', '2025-10-03 14:37:23', 'g'),
(1380, 'Soy Sauce', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:37:23', 'ml'),
(1381, 'Vinegar', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:37:23', 'ml'),
(1382, 'Garlic', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1383, 'Pork', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:07:40', '2025-10-03 14:37:23', 'g'),
(1384, 'Sugar', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1385, 'Salt', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1386, 'Garlic', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1387, 'Pork Sausage', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:07:40', '2025-10-03 14:37:23', 'g'),
(1388, 'Garlic', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1389, 'Salt', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1390, 'Pepper', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1391, 'Milkfish', 'Seafood', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1392, 'Vinegar', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:37:23', 'ml'),
(1393, 'Garlic', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1394, 'Salt', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1395, 'Chicken Leg', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:07:40', '2025-10-03 14:37:23', 'g'),
(1396, 'Soy Sauce', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:37:23', 'ml'),
(1397, 'Vinegar', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-03 14:37:23', 'ml'),
(1398, 'Garlic', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1399, 'Annatto Oil', 'Others', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'ml'),
(1400, 'Tilapia', 'Seafood', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1401, 'Salt', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1402, 'Pepper', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1403, 'Banana Leaf', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'pcs'),
(1404, 'Rice', 'Grain', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-03 14:37:23', 'g'),
(1405, 'Hotel La Vista Souvenir Mug', 'Gift Store', 50000, 8, 1, 250.00, 'EMP501', '2025-10-08 11:22:20', '2025-10-04 11:21:32', 'piece'),
(1406, 'Local Handmade Necklace', 'Gift Store', 50000, 5, 0, 180.00, 'EMP501', '2025-10-08 11:22:20', '2025-10-04 11:21:32', 'piece'),
(1407, 'Mini Teddy Bear Keychain', 'Gift Store', 50000, 15, 3, 95.00, 'EMP501', '2025-10-08 11:22:20', '2025-10-04 11:21:32', 'piece'),
(1408, 'La Vista Chocolate Gift Box', 'Gift Store', 49998, 6, 0, 1500.00, 'EMP501', '2025-10-08 16:18:17', '2025-10-04 11:21:32', 'box'),
(1409, 'Sprite Can 330ml', 'Mini Bar', 50000, 22, 1, 65.00, 'EMP601', '2025-10-08 11:22:20', '2025-10-04 11:21:32', 'can'),
(1410, 'Jack n Jill Piattos Sour Cream', 'Mini Bar', 49997, 18, 0, 55.00, 'EMP601', '2025-10-08 16:45:02', '2025-10-04 11:21:32', 'pack'),
(1411, 'Red Horse Beer Can 500ml', 'Mini Bar', 50000, 20, 2, 95.00, 'EMP602', '2025-10-08 11:22:20', '2025-10-04 11:21:32', 'bottle'),
(1412, 'KitKat Mini Pack', 'Mini Bar', 49997, 15, 1, 85.00, 'EMP603', '2025-10-08 16:45:02', '2025-10-04 11:21:32', 'pack'),
(1413, 'Nature Spring Water 500ml', 'Mini Bar', 50000, 30, 3, 40.00, 'EMP603', '2025-10-08 11:22:20', '2025-10-04 11:21:32', 'bottle'),
(1414, 'Jack \'N Jill Vcut Spicy Barbeque ', 'Mini Bar', 49997, 12, 0, 130.00, 'EMP603', '2025-10-08 16:45:02', '2025-10-04 11:21:32', 'pack'),
(1415, 'Chivas Regal 12 Years', 'Lounge Bar', 49999, 4, 0, 2400.00, 'EMP701', '2025-10-08 17:16:46', '2025-10-04 11:21:32', 'bottle'),
(1416, 'Grey Goose Vodka', 'Lounge Bar', 50000, 6, 0, 2700.00, 'EMP702', '2025-10-08 11:22:20', '2025-10-04 11:21:32', 'bottle'),
(1417, 'Jose Cuervo Tequila', 'Lounge Bar', 50000, 7, 1, 1950.00, 'EMP703', '2025-10-08 11:22:20', '2025-10-04 11:21:32', 'bottle'),
(1418, 'Hennessy VS Cognac', 'Lounge Bar', 50000, 3, 0, 3200.00, 'EMP704', '2025-10-08 11:22:20', '2025-10-04 11:21:32', 'bottle'),
(1419, 'Baileys Irish Cream', 'Lounge Bar', 50000, 5, 0, 1800.00, 'EMP705', '2025-10-08 11:22:20', '2025-10-04 11:21:32', 'bottle'),
(1420, 'Smirnoff Vodka', 'Lounge Bar', 50000, 8, 1, 1500.00, 'EMP705', '2025-10-08 11:22:20', '2025-10-04 11:21:32', 'bottle'),
(1421, 'Tanduay Ice', 'Lounge Bar', 50000, 10, 0, 95.00, 'EMP705', '2025-10-08 11:22:20', '2025-10-04 11:21:32', 'bottle'),
(1422, 'Mixology Cocktail Syrup', 'Lounge Bar', 50000, 6, 1, 380.00, 'EMP705', '2025-10-08 11:22:20', '2025-10-04 11:21:32', 'bottle'),
(1423, 'Cocktail Straw Set', 'Lounge Bar', 49998, 40, 5, 3.00, 'EMP705', '2025-10-10 11:16:59', '2025-10-04 11:21:32', 'piece'),
(1424, 'Highball Glass', 'Lounge Bar', 50000, 8, 2, 160.00, 'EMP705', '2025-10-08 11:22:20', '2025-10-04 11:21:32', 'piece'),
(1425, 'Hotel Signature Scent Perfume', 'Gift Store', 50000, 0, 0, 550.00, 'System', '2025-10-08 11:22:20', '2025-10-04 11:44:30', 'pcs'),
(1426, 'Handwoven Coin Purse', 'Gift Store', 50000, 0, 0, 180.00, 'System', '2025-10-08 11:22:20', '2025-10-04 11:44:30', 'pcs'),
(1427, 'Limited Edition Hotel Calendar', 'Gift Store', 49996, 0, 0, 250.00, 'System', '2025-10-08 17:08:31', '2025-10-04 11:44:30', 'pcs'),
(1431, 'Tea Leaves', 'Beverage', 50000, 0, 0, 0.50, 'System', '2025-10-08 11:10:19', '2025-10-04 11:58:37', 'g'),
(1432, 'Water', 'Others', 50000, 0, 0, 0.02, 'System', '2025-10-08 11:08:56', '2025-10-04 11:58:37', 'ml'),
(1433, 'Sugar', 'Seasoning', 50000, 0, 0, 0.10, 'System', '2025-10-08 11:08:56', '2025-10-04 11:58:37', 'g'),
(1434, 'Ice Cubes', 'Others', 50000, 0, 0, 0.05, 'System', '2025-10-08 11:08:56', '2025-10-04 11:58:37', 'g'),
(1435, 'Lemon Slice', 'Beverage', 50000, 0, 0, 5.00, 'System', '2025-10-08 11:22:20', '2025-10-04 11:58:37', 'pcs'),
(1436, 'All-purpose Flour', 'Others', 50000, 0, 0, 0.15, 'System', '2025-10-08 11:22:20', '2025-10-04 12:00:04', 'g'),
(1437, 'Cocoa Powder', 'Others', 50000, 0, 0, 0.30, 'System', '2025-10-08 11:22:20', '2025-10-04 12:00:04', 'g'),
(1438, 'Eggs', 'Others', 50000, 0, 0, 7.00, 'System', '2025-10-08 11:22:20', '2025-10-04 12:00:04', 'pcs'),
(1439, 'Butter', 'Dairy', 50000, 0, 0, 1.20, 'System', '2025-10-08 11:22:20', '2025-10-04 12:00:04', 'g'),
(1440, 'Sugar', 'Others', 50000, 0, 0, 0.10, 'System', '2025-10-08 11:22:20', '2025-10-04 12:00:04', 'g'),
(1441, 'Cream', 'Dairy', 49700, 0, 0, 0.50, 'System', '2025-10-10 12:03:44', '2025-10-04 12:00:04', 'ml'),
(1442, 'Mixed Fruits', 'Fruit', 50000, 0, 0, 0.25, 'System', '2025-10-08 11:17:09', '2025-10-04 12:00:04', 'g'),
(1443, 'Condensed Milk', 'Dairy', 50000, 0, 0, 0.60, 'System', '2025-10-08 11:15:55', '2025-10-04 12:00:04', 'ml'),
(1444, 'All-purpose Cream', 'Dairy', 49980, 0, 20, 0.70, 'System', '2025-10-10 10:46:31', '2025-10-04 12:00:04', 'ml'),
(1445, 'Fruit Cocktail Syrup', 'Others', 50000, 0, 0, 0.30, 'System', '2025-10-08 11:15:55', '2025-10-04 12:00:04', 'ml'),
(1446, 'Cream Cheese', 'Dairy', 50000, 0, 0, 1.50, 'System', '2025-10-08 11:15:55', '2025-10-04 12:00:04', 'g'),
(1447, 'Graham Crumbs', 'Grain', 50000, 0, 0, 0.25, 'System', '2025-10-08 11:15:55', '2025-10-04 12:00:04', 'g'),
(1448, 'Butter (for crust)', 'Dairy', 50000, 0, 0, 1.20, 'System', '2025-10-08 11:15:55', '2025-10-04 12:00:04', 'g'),
(1449, 'Sugar (for crust)', 'Seasoning', 50000, 0, 0, 0.10, 'System', '2025-10-08 11:15:55', '2025-10-04 12:00:04', 'g'),
(1450, 'Whipping Cream', 'Dairy', 50000, 0, 0, 0.80, 'System', '2025-10-08 11:15:55', '2025-10-04 12:00:04', 'ml'),
(1451, 'Chocolate Chips', 'Others', 50000, 0, 0, 0.40, 'System', '2025-10-08 11:15:55', '2025-10-04 12:00:04', 'g'),
(1452, 'Eggs (for brownies)', 'Dairy', 50000, 0, 0, 7.00, 'System', '2025-10-08 11:15:55', '2025-10-04 12:00:04', 'pcs'),
(1453, 'Flour (for brownies)', 'Grain', 50000, 0, 0, 0.15, 'System', '2025-10-08 11:15:55', '2025-10-04 12:00:04', 'g'),
(1454, 'Sugar (for brownies)', 'Seasoning', 50000, 0, 0, 0.10, 'System', '2025-10-08 11:15:55', '2025-10-04 12:00:04', 'g'),
(1455, 'Butter (for brownies)', 'Dairy', 50000, 0, 0, 1.20, 'System', '2025-10-08 11:15:55', '2025-10-04 12:00:04', 'g'),
(1456, 'Vanilla Ice Cream', 'Dairy', 50000, 0, 0, 0.80, 'System', '2025-10-08 11:15:55', '2025-10-04 12:00:04', 'g'),
(1457, 'Chocolate Syrup', 'Others', 50000, 0, 0, 0.50, 'System', '2025-10-08 11:15:55', '2025-10-04 12:00:04', 'ml'),
(1458, 'Whipped Cream', 'Dairy', 50000, 0, 0, 0.60, 'System', '2025-10-08 11:15:55', '2025-10-04 12:00:04', 'ml'),
(1459, 'Crushed Nuts', 'Others', 50000, 0, 0, 0.30, 'System', '2025-10-08 11:15:55', '2025-10-04 12:00:04', 'g'),
(1460, 'Cherries', 'Others', 50000, 0, 0, 1.00, 'System', '2025-10-08 11:15:55', '2025-10-04 12:00:04', 'pcs'),
(1461, 'Fresh Fruits', 'Beverage', 50000, 0, 0, 200.00, 'System', '2025-10-08 11:10:19', '2025-10-04 12:06:11', 'kg'),
(1462, 'Ice Cubes', 'Beverage', 50000, 0, 0, 10.00, 'System', '2025-10-08 11:10:19', '2025-10-04 12:06:11', 'kg'),
(1463, 'Sugar Syrup', 'Beverage', 50000, 0, 0, 80.00, 'System', '2025-10-08 11:10:19', '2025-10-04 12:06:11', 'L'),
(1464, 'Lemon', 'Fruit', 50000, 0, 0, 15.00, 'System', '2025-10-08 11:22:20', '2025-10-04 12:12:31', 'pcs'),
(1465, 'Water', 'Others', 50000, 0, 0, 0.00, 'System', '2025-10-08 11:08:56', '2025-10-04 12:12:31', 'L'),
(1466, 'Sugar', 'Seasoning', 50000, 0, 0, 80.00, 'System', '2025-10-08 11:08:56', '2025-10-04 12:12:31', 'g'),
(1467, 'Ice Cubes', 'Others', 50000, 0, 0, 10.00, 'System', '2025-10-08 11:08:56', '2025-10-04 12:12:31', 'pcs'),
(1468, 'Coffee Powder', 'Beverage', 50000, 0, 0, 250.00, 'System', '2025-10-08 11:10:19', '2025-10-04 12:13:59', 'g'),
(1469, 'Water', 'Beverage', 50000, 0, 0, 0.00, 'System', '2025-10-08 11:10:19', '2025-10-04 12:13:59', 'L'),
(1470, 'Sugar', 'Seasoning', 50000, 0, 0, 80.00, 'System', '2025-10-08 11:08:56', '2025-10-04 12:13:59', 'g'),
(1471, 'Milk', 'Dairy', 50000, 0, 0, 120.00, 'System', '2025-10-10 12:03:44', '2025-10-04 12:15:50', 'L'),
(1472, 'Chocolate Syrup', 'Others', 50000, 0, 0, 150.00, 'System', '2025-10-08 11:14:59', '2025-10-04 12:15:50', 'ml'),
(1473, 'Ice Cream', 'Dairy', 50000, 0, 0, 200.00, 'System', '2025-10-10 12:03:44', '2025-10-04 12:15:50', 'g'),
(1474, 'Ice Cubes', 'Beverage', 50000, 0, 0, 10.00, 'System', '2025-10-08 11:10:19', '2025-10-04 12:15:50', 'pcs'),
(1475, 'Whipped Cream', 'Dairy', 50000, 0, 0, 180.00, 'System', '2025-10-08 11:08:56', '2025-10-04 12:15:50', 'g'),
(1478, 'Sugar', 'Seasoning', 50000, 0, 0, 60.00, 'System', '2025-10-08 11:08:56', '2025-10-04 12:17:14', 'g'),
(1479, 'Coca-Cola', 'Beverage', 50000, 0, 0, 0.09, 'Kimberly Lababo', '2025-10-08 11:10:19', '2025-10-04 12:46:30', '-'),
(1480, 'Royal', 'Mini Bar', 50000, 0, 0, 1111.10, 'Isabel Reyes', '2025-10-08 11:22:20', '2025-10-04 12:47:42', '-'),
(1481, 'Chicken', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:34:37', 'g'),
(1482, 'Papaya', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:34:37', 'g'),
(1483, 'Malunggay Leaves', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:34:37', 'g'),
(1484, 'Ginger', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:34:37', 'g'),
(1485, 'Pork', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:07:40', '2025-10-05 07:34:37', 'g'),
(1486, 'Tamarind', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:34:37', 'g'),
(1487, 'Kangkong', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:34:37', 'g'),
(1488, 'Tomato', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:34:37', 'g'),
(1489, 'Radish', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:34:37', 'g'),
(1490, 'Beef Shank', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:34:37', 'g'),
(1491, 'Corn', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:34:37', 'pcs'),
(1492, 'Cabbage', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:34:37', 'g'),
(1493, 'Marrow Bones', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:34:37', 'g'),
(1494, 'Chicken', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:34:37', 'g'),
(1495, 'Macaroni', 'Grain', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:34:37', 'g'),
(1496, 'Milk', 'Dairy', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:34:37', 'ml'),
(1497, 'Carrot', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:34:37', 'g'),
(1498, 'Onion', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:34:37', 'g'),
(1499, 'Noodles', 'Grain', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:34:37', 'g'),
(1500, 'Pork', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:07:40', '2025-10-05 07:34:37', 'g'),
(1501, 'Shrimp', 'Seafood', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:34:37', 'g'),
(1502, 'Cabbage', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:34:37', 'g'),
(1503, 'Carrot', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:34:37', 'g'),
(1504, 'Bihon Noodles', 'Grain', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:34:37', 'g'),
(1505, 'Chicken', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:34:37', 'g'),
(1506, 'Rice Noodles', 'Grain', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:36:48', 'pcs'),
(1507, 'Seafood Mix', 'Seafood', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:36:48', 'g'),
(1508, 'Vegetables', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:36:48', 'g'),
(1509, 'Spaghetti Noodles', 'Grain', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:36:48', 'g'),
(1510, 'Tomato Sauce', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:36:48', 'ml'),
(1511, 'Ground Meat', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:07:40', '2025-10-05 07:36:48', 'g'),
(1512, 'Pasta', 'Grain', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:36:48', 'g'),
(1513, 'Bacon', 'Meat', 49950, 0, 50, 0.00, NULL, '2025-10-10 10:45:27', '2025-10-05 07:36:48', 'g'),
(1514, 'Cream', 'Dairy', 49700, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:36:48', 'ml'),
(1515, 'Pork Belly', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:36:48', 'g'),
(1516, 'Soy Sauce', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:36:48', 'ml'),
(1517, 'Vinegar', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:36:48', 'ml'),
(1518, 'Oxtail', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:36:49', 'g'),
(1519, 'Peanut Sauce', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:36:49', 'ml'),
(1520, 'Vegetables', 'Vegetable', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:36:49', 'g'),
(1521, 'Pork Belly', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:36:49', 'g'),
(1522, 'Oil', 'Others', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:36:49', 'ml'),
(1523, 'Pork Leg', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:36:49', 'g'),
(1524, 'Oil', 'Others', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:36:49', 'ml'),
(1525, 'Beef Strips', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:07:40', '2025-10-05 07:36:49', 'g'),
(1526, 'Soy Sauce', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:36:49', 'ml'),
(1527, 'Garlic', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:36:49', 'g'),
(1528, 'Pork', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:07:40', '2025-10-05 07:36:49', 'g'),
(1529, 'Sugar', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:36:49', 'g'),
(1530, 'Garlic', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:36:49', 'g'),
(1531, 'Pork Sausages', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:07:40', '2025-10-05 07:42:04', 'g'),
(1532, 'Milkfish', 'Seafood', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:42:04', 'g'),
(1533, 'Vinegar', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:42:04', 'ml'),
(1534, 'Garlic', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:42:04', 'g'),
(1535, 'Salt', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:42:04', 'g'),
(1536, 'Chicken Leg', 'Meat', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:07:40', '2025-10-05 07:42:04', 'g'),
(1537, 'Soy Sauce', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:42:04', 'ml'),
(1538, 'Vinegar', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:42:04', 'ml'),
(1539, 'Garlic', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:42:04', 'g'),
(1540, 'Tilapia', 'Seafood', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:42:04', 'g'),
(1541, 'Salt', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:42:04', 'g'),
(1542, 'Pepper', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:42:04', 'g'),
(1543, 'Rice', 'Grain', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:42:04', 'g'),
(1544, 'Water', 'Others', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:42:04', 'ml'),
(1545, 'Chocolate Cake Mix', 'Grain', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 07:42:04', 'g'),
(1546, 'Cream', 'Dairy', 49700, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:42:04', 'ml'),
(1547, 'Fruits', 'Fruit', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:42:04', 'g'),
(1548, 'Syrup', 'Others', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:42:04', 'ml'),
(1549, 'Cheesecake Mix', 'Dairy', 49100, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:42:04', 'g'),
(1550, 'Cream', 'Dairy', 49700, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:42:04', 'ml'),
(1551, 'Brownie Mix', 'Grain', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:42:04', 'g'),
(1552, 'Chocolate', 'Dairy', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:42:04', 'g'),
(1553, 'Ice Cream', 'Dairy', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:42:04', 'g'),
(1554, 'Toppings', 'Others', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 07:42:04', 'g'),
(1555, 'Chocolate Cake Mix', 'Grain', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 08:50:18', 'g'),
(1556, 'Cream', 'Dairy', 49700, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 08:50:18', 'ml'),
(1557, 'Fruits', 'Fruit', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 08:50:18', 'g'),
(1558, 'Syrup', 'Others', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 08:50:18', 'ml'),
(1559, 'Cheesecake Mix', 'Dairy', 49100, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 08:50:18', 'g'),
(1560, 'Cream', 'Dairy', 49700, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 08:50:18', 'ml'),
(1561, 'Brownie Mix', 'Grain', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 08:50:18', 'g'),
(1562, 'Chocolate', 'Dairy', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 08:50:18', 'g'),
(1563, 'Ice Cream', 'Dairy', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 08:50:18', 'g'),
(1564, 'Toppings', 'Others', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 08:50:18', 'g'),
(1565, 'Tea', 'Others', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 08:50:18', 'g'),
(1566, 'Sugar', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 08:50:18', 'g'),
(1567, 'Ice', 'Others', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 08:50:18', 'g'),
(1568, 'Fresh Fruits', 'Fruit', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:16:43', '2025-10-05 08:50:19', 'g'),
(1569, 'Water', 'Others', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 08:50:19', 'ml'),
(1570, 'Lemon', 'Fruit', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:16:39', '2025-10-05 08:50:19', 'g'),
(1571, 'Water', 'Others', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 08:50:19', 'ml'),
(1572, 'Sugar', 'Seasoning', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 08:50:19', 'g'),
(1573, 'Coffee', 'Others', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 08:50:19', 'g'),
(1574, 'Water', 'Others', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 08:50:19', 'ml'),
(1575, 'Milk', 'Dairy', 50000, 0, 0, 0.00, NULL, '2025-10-10 12:03:44', '2025-10-05 08:50:19', 'ml'),
(1576, 'Chocolate Syrup', 'Others', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:14:59', '2025-10-05 08:50:19', 'ml'),
(1577, 'Ice', 'Others', 50000, 0, 0, 0.00, NULL, '2025-10-08 11:08:56', '2025-10-05 08:50:19', 'g');

-- --------------------------------------------------------

--
-- Table structure for table `item_images`
--

CREATE TABLE `item_images` (
  `image_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_images`
--

INSERT INTO `item_images` (`image_id`, `item_id`, `filename`) VALUES
(5, 1297, '1759414297_Beef Jerky.jpg'),
(6, 1285, '1759414323_Beer Bottle.jpg'),
(7, 1295, '1759414362_Candy Pack.jpg'),
(8, 1287, '1759414445_Chips Pack.jpg'),
(9, 1288, '1759414501_Toblerone.jpg'),
(10, 1300, '1759414538_Nescafe Original.jpg'),
(11, 1296, '1759414580_Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g.jpg'),
(12, 1291, '1759414640_Cobra Energy Drink.jpg'),
(13, 1298, '1759414719_Minute Maid Fresh Orange Juice.jpg'),
(14, 1290, '1759414789_Minute Maid Fresh.jpg'),
(15, 1301, '1759414828_Lemonade Bottle.jpg'),
(16, 1292, '1759414865_Nestle Fresh Milk.jpg'),
(17, 1289, '1759414922_Growers Mixed Nuts.jpg'),
(18, 1294, '1759414967_Ding Dong Snack Mix.jpg'),
(19, 1284, '1759415002_Coca-Cola Original Taste Soft Drink Can.jpg'),
(20, 1293, '1759415031_Sparkling Water.jpg'),
(21, 1299, '1759415117_Lipton Green Tea Lively Fresh.jpg'),
(22, 1283, '1759415160_Summit Natural Drinking Water.jpg'),
(23, 1286, '1759415213_Novellino - Rosso Classico.jpg'),
(24, 1302, '1759581680_Alaska Fruitti Yo! Strawberry Yoghurt Milk Drink.jpg'),
(25, 1310, '1759415294_Red Horse Beer Bottle.jpg'),
(26, 1319, '1759415389_Arla Natural Cheese Mozzarella Cheese Slices.jpg'),
(27, 1313, '1759415430_Cocktail - Daiquiri.jpg'),
(28, 1304, '1759415464_Cocktail - Margarita.jpg'),
(29, 1305, '1759415502_Cocktail - Martini.jpg'),
(30, 1303, '1759415537_Cocktail - Mojito.jpg'),
(31, 1314, '1759415567_Cocktail - Pina Colada.jpg'),
(32, 1315, '1759415587_Cocktail - Tequila Sunrise.jpg'),
(33, 1322, '1759415610_Fruit Platter.jpg'),
(34, 1308, '1759415645_Ginebra San Miguel Gin.jpg'),
(35, 1320, '1759415677_Mixed Nuts Bowl.jpg'),
(36, 1317, '1759415705_Mocktail - Sunrise.jpg'),
(37, 1316, '1759415730_Mocktail - Virgin Mojito.jpg'),
(38, 1321, '1759415754_Olives Bowl.jpg'),
(39, 1311, '1759415781_Red Wine Glass.jpg'),
(40, 1307, '1759415827_Bacardi Gold Rum.jpg'),
(41, 1318, '1759415881_Snack Platter.jpg'),
(42, 1309, '1759415929_Absolut Vodka.jpg'),
(43, 1306, '1759942046_Jack Daniel\'s.jpg'),
(44, 1312, '1759415999_White Wine Glass.jpg'),
(45, 1276, '1759416040_Ferrero Rocher Chocolate Box.jpg'),
(46, 1267, '1759416071_Coffee Mug.jpg'),
(47, 1277, '1759416103_Decorative Coasters.jpg'),
(48, 1272, '1759416133_Fragrant Candle.jpg'),
(50, 1274, '1759416173_Fridge Magnet.jpg'),
(51, 1278, '1759416206_Handmade Bracelet.jpg'),
(52, 1279, '1759416232_Key Holder.jpg'),
(54, 1275, '1759416280_Local Art Mini Canvas.jpg'),
(55, 1281, '1759416303_Local Snack Box.jpg'),
(56, 1270, '1759416322_Local Snack Pack.jpg'),
(57, 1271, '1759416360_Mini Photo Frame.jpg'),
(58, 1280, '1759416387_Miniature Figurine.jpg'),
(59, 1268, '1759416415_Notebook.jpg'),
(60, 1269, '1759416443_Pen Set.jpg'),
(62, 1282, '1759416490_Souvenir Pen.jpg'),
(63, 1273, '1759416510_Souvenir T-shirt.jpg'),
(64, 1266, '1759416529_Tote Bag.jpg'),
(65, 1410, '1759577026_Jack n Jill Piattos Sour Cream.jpg'),
(66, 1412, '1759577061_KitKat Mini Pack.jpg'),
(67, 1414, '1759941280_Jack \'N Jill Vcut Spicy Barbeque.jpg'),
(68, 1413, '1759577216_Nature Spring Water 500ml.jpg'),
(69, 1411, '1759577259_Red Horse Beer 500ml.jpg'),
(70, 1409, '1759577299_Sprite Can 330ml.jpg'),
(71, 1423, '1759577326_Cocktail Straw Set.jpg'),
(72, 1419, '1759577360_Baileys Irish Cream.jpg'),
(73, 1415, '1759577443_Chivas Regal 12 Years.jpg'),
(75, 1416, '1759577701_Grey Goose Vodka.jpg'),
(80, 1418, '1759577721_Hennessy VS Cognac.jpg'),
(81, 1424, '1759577788_Highball Glass.jpg'),
(82, 1417, '1759577843_Jose Cuervo Tequila.jpg'),
(83, 1422, '1759577882_Mixology Cocktail Syrup.jpg'),
(84, 1420, '1759577941_Smirnoff Vodka.jpg'),
(85, 1421, '1759577968_Tanduay Ice.jpg'),
(86, 1405, '1759577992_Hotel La Vista Souvenir Mug.jpg'),
(87, 1408, '1759578026_La Vista Chocolate Gift Box.jpg'),
(88, 1406, '1759578092_Local Handmade Necklace.jpg'),
(89, 1407, '1759578115_Mini Teddy Bear Keychain.jpg'),
(90, 1426, '1759578304_Handwoven Coin Purse.jpg'),
(91, 1425, '1759578329_Hotel Signature Scent Perfume.jpg'),
(92, 1427, '1759578352_Limited Edition Hotel Calendar.jpg'),
(95, 1480, '1759582148_Royal.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `kitchen_orders`
--

CREATE TABLE `kitchen_orders` (
  `order_id` int(11) NOT NULL,
  `order_type` enum('Restaurant','Room Service') NOT NULL,
  `status` enum('preparing','Ready','completed') DEFAULT 'preparing',
  `resolution` varchar(50) DEFAULT NULL,
  `complain_reason` enum('Wrong Item','Undercooked / Raw','Bland / Tasteless','Spoiled','Late Delivery','Damaged','Other') DEFAULT NULL,
  `priority` int(11) DEFAULT 1,
  `table_number` varchar(10) DEFAULT NULL,
  `guest_name` varchar(255) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `assigned_chef` varchar(20) DEFAULT NULL,
  `item` text DEFAULT NULL,
  `quantity` text DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `estimated_time` int(11) DEFAULT 30,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `room_number` varchar(50) DEFAULT NULL,
  `order_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kitchen_orders`
--

INSERT INTO `kitchen_orders` (`order_id`, `order_type`, `status`, `resolution`, `complain_reason`, `priority`, `table_number`, `guest_name`, `room_id`, `guest_id`, `assigned_chef`, `item`, `quantity`, `total_amount`, `notes`, `staff_id`, `estimated_time`, `created_at`, `updated_at`, `room_number`, `order_notes`) VALUES
(7008, 'Restaurant', 'completed', NULL, NULL, 1, '15', 'Jane Smith', NULL, 2, NULL, 'Tinolang Manok, Bulalo, Pork Adobo', '1, 1, 1', 740.00, NULL, NULL, 30, '2025-10-10 12:02:13', '2025-10-10 12:03:32', NULL, ''),
(8104, 'Room Service', 'completed', 'Refund', 'Bland / Tasteless', 1, NULL, 'Jane Smith', NULL, 2, 'EMP575330', 'Bulalo, Pork Adobo, Cheesecake, Kare-Kare', '1, 1, 1, 3', 1560.00, NULL, NULL, 30, '2025-10-08 12:57:15', '2025-10-08 13:00:50', '102', ''),
(8115, 'Room Service', 'completed', 'Refund', 'Late Delivery', 1, NULL, 'Jane Smith', NULL, 2, 'EMPGEN121', 'Pork Adobo, Kare-Kare, Crispy Pata, Cheesecake', '1, 1, 1, 1', 1160.00, NULL, NULL, 30, '2025-10-10 10:51:07', '2025-10-10 11:48:40', '102', ''),
(9423, 'Room Service', 'completed', NULL, NULL, 1, NULL, 'Jane Smith', NULL, 2, NULL, 'Bulalo, Tinolang Manok, Chicken Sopas, Pancit Bihon, Crispy Pata', '1, 1, 1, 1, 1', 1350.00, NULL, NULL, 30, '2025-10-09 02:56:33', '2025-10-10 10:41:41', '102', ''),
(9961, 'Restaurant', 'completed', 'Replacement', 'Bland / Tasteless', 1, '1', 'Sarah Miller', NULL, 6, 'EMP354777', 'Bulalo, Tinolang Manok, Cheesecake, Pork Adobo', '1, 1, 1, 1', 920.00, NULL, NULL, 30, '2025-10-08 17:15:45', '2025-10-10 11:36:20', NULL, '');

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
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `recipe_name`, `category`, `instructions`, `preparation_time`, `price`, `image_path`, `is_active`, `display_order`, `created_at`, `updated_at`) VALUES
(2, 'Tinolang Manok', 'Main Course', '1. Clean and cut the chicken into serving pieces. 2. Peel and slice ginger. 3. Boil water in a pot and add chicken and ginger. 4. Add chopped green papaya and simmer until tender. 5. Add malunggay leaves and season with fish sauce and salt. 6. Serve hot.', 60, 200.00, 'tinolang_manok.jpg', 1, 2, '2025-09-16 05:01:59', '2025-10-03 14:38:29'),
(3, 'Sinigang na Baboy', 'Main Course', '1. Boil pork pieces until tender. 2. Add sliced tomatoes, onion, and tamarind broth. 3. Add vegetables such as kangkong, radish, and string beans. 4. Simmer until flavors meld. 5. Season with fish sauce and salt. 6. Serve hot with rice.', 75, 240.00, 'sinigang_na_baboy.jpg', 1, 3, '2025-09-16 05:01:59', '2025-10-03 14:38:29'),
(4, 'Bulalo', 'Main Course', '1. Boil beef shank with water and aromatics until meat is tender. 2. Add corn, cabbage, and other vegetables. 3. Simmer until vegetables are cooked. 4. Season with salt and pepper. 5. Serve with steamed rice.', 120, 320.00, 'bulalo.jpg', 1, 4, '2025-09-16 05:01:59', '2025-10-03 14:38:29'),
(5, 'Chicken Sopas', 'Main Course', '1. Boil chicken pieces until cooked. 2. Add macaroni and cook until tender. 3. Pour in milk and simmer. 4. Season with salt and pepper. 5. Serve hot as a comforting soup.', 45, 180.00, 'chicken_sopas.jpg', 1, 5, '2025-09-16 05:01:59', '2025-10-03 14:38:29'),
(6, 'Pancit Canton', 'Appetizer', '1. Stir-fry sliced pork until cooked. 2. Add shrimp and cook until pink. 3. Add chopped vegetables like carrots, cabbage, and bell pepper. 4. Stir in cooked noodles and soy sauce. 5. Toss evenly and serve hot.', 45, 180.00, 'pancit_canton.jpg', 1, 6, '2025-09-16 05:01:59', '2025-10-03 14:38:30'),
(7, 'Pancit Bihon', 'Appetizer', '1. Soak bihon noodles in water until soft. 2. Stir-fry chicken pieces until cooked. 3. Add vegetables like carrots, cabbage, and green beans. 4. Mix in softened noodles and season with soy sauce. 5. Toss everything together and serve hot.', 40, 170.00, 'pancit_bihon.jpg', 1, 7, '2025-09-16 05:01:59', '2025-10-03 14:38:30'),
(8, 'Pancit Malabon', 'Appetizer', '1. Boil rice noodles until soft. 2. Prepare seafood sauce with shrimp, squid, and fish sauce. 3. Pour sauce over noodles. 4. Top with boiled eggs, fried garlic, and chicharon. 5. Serve warm.', 50, 220.00, 'pancit_malabon.jpg', 1, 8, '2025-09-16 05:01:59', '2025-10-03 14:38:30'),
(9, 'Spaghetti Filipino Style', 'Appetizer', '1. Boil spaghetti noodles until al dente. 2. Prepare sweet-style tomato sauce and add sliced hotdogs. 3. Simmer sauce until slightly thickened. 4. Mix sauce with noodles. 5. Top with grated cheese and serve hot.', 50, 150.00, 'spaghetti_filipino_style.jpg', 1, 9, '2025-09-16 05:01:59', '2025-10-03 14:38:30'),
(10, 'Carbonara', 'Appetizer', '1. Cook pasta until al dente. 2. Fry bacon until crispy. 3. Prepare creamy white sauce with cream and cheese. 4. Combine pasta, bacon, and sauce. 5. Toss evenly and serve warm.', 40, 220.00, 'carbonara.jpg', 1, 10, '2025-09-16 05:01:59', '2025-10-03 14:38:30'),
(12, 'Pork Adobo', 'Main Course', '1. Marinate pork belly in soy sauce, vinegar, garlic, and pepper. 2. Simmer pork over medium heat until tender. 3. Reduce sauce to thicken slightly. 4. Serve hot with steamed rice.', 65, 220.00, 'pork_adobo.jpg', 1, 12, '2025-09-16 05:01:59', '2025-10-03 14:38:30'),
(13, 'Kare-Kare', 'Main Course', '1. Boil oxtail until tender. 2. Add peanut sauce and vegetables such as eggplant, string beans, and banana heart. 3. Simmer until sauce thickens. 4. Adjust seasoning with fish sauce or salt. 5. Serve hot with rice.', 90, 280.00, 'kare-kare.jpg', 1, 13, '2025-09-16 05:01:59', '2025-10-03 14:38:30'),
(14, 'Lechon Kawali', 'Main Course', '1. Boil pork belly until skin is tender. 2. Drain and pat dry. 3. Deep fry in hot oil until skin is crispy. 4. Serve sliced with dipping sauce.', 50, 260.00, 'lechon_kawali.jpg', 1, 14, '2025-09-16 05:01:59', '2025-10-03 14:38:30'),
(15, 'Crispy Pata', 'Main Course', '1. Boil pork leg until tender. 2. Deep fry in hot oil until skin is golden and crispy. 3. Serve with vinegar dipping sauce.', 120, 480.00, 'crispy_pata.jpg', 1, 15, '2025-09-16 05:01:59', '2025-10-03 14:38:30'),
(16, 'Beef Tapa', 'Main Course', '1. Marinate beef strips in soy sauce, garlic, and pepper. 2. Pan-fry beef until cooked and slightly caramelized. 3. Serve with garlic rice and fried egg.', 40, 180.00, 'beef_tapa.jpg', 1, 16, '2025-09-16 05:01:59', '2025-10-03 14:38:30'),
(17, 'Tocino', 'Main Course', '1. Marinate pork in sugar, salt, and garlic. 2. Pan-fry until caramelized and cooked through. 3. Serve with garlic fried rice.', 30, 170.00, 'tocino.jpg', 1, 17, '2025-09-16 05:01:59', '2025-10-03 14:38:30'),
(18, 'Longganisa', 'Main Course', '1. Fry pork sausages until cooked. 2. Serve with garlic rice and fried egg. 3. Optionally, drizzle with a bit of vinegar for extra flavor.', 30, 160.00, 'longganisa.jpg', 1, 18, '2025-09-16 05:01:59', '2025-10-03 14:38:30'),
(19, 'Daing na Bangus', 'Main Course', '1. Marinate milkfish in vinegar, garlic, and salt. 2. Fry until crispy on both sides. 3. Serve with steamed rice and dipping sauce.', 35, 200.00, 'daing_na_bangus.jpg', 1, 19, '2025-09-16 05:01:59', '2025-10-03 14:38:30'),
(20, 'Chicken Inasal', 'Main Course', '1. Marinate chicken leg in soy sauce, vinegar, garlic, and annatto oil. 2. Grill chicken over medium heat until cooked and slightly charred. 3. Serve with dipping sauce and steamed rice.', 60, 210.00, 'chicken_inasal.jpg', 1, 20, '2025-09-16 05:01:59', '2025-10-03 14:38:30'),
(21, 'Grilled Tilapia', 'Main Course', '1. Season tilapia with salt and pepper. 2. Wrap in banana leaf. 3. Grill until cooked through and lightly charred. 4. Serve hot with rice.', 40, 220.00, 'grilled_tilapia.jpg', 1, 21, '2025-09-16 05:01:59', '2025-10-03 14:38:30'),
(119, 'Rice', 'Main Course', '1. Rinse rice thoroughly. 2. Add rice and water to a pot. 3. Cook over medium heat until water is absorbed. 4. Fluff with a fork and serve.', 5, 50.00, 'rice.jpg', 1, 22, '2025-10-02 10:23:11', '2025-10-03 14:38:30'),
(120, 'Chocolate Cake', 'Dessert', 'Bake chocolate cake. Serve with cream.', 45, 150.00, 'chocolate_cake.jpg', 1, 1, '2025-10-04 06:43:26', '2025-10-04 06:43:26'),
(121, 'Fruit Salad', 'Dessert', 'Chop fruits and mix with syrup.', 15, 80.00, 'fruit_salad.jpg', 1, 2, '2025-10-04 06:43:26', '2025-10-04 06:43:26'),
(122, 'Cheesecake', 'Dessert', 'Prepare cheesecake and chill before serving.', 60, 180.00, 'cheesecake.jpg', 1, 3, '2025-10-04 06:43:26', '2025-10-04 06:43:26'),
(123, 'Brownies', 'Dessert', 'Bake chocolate brownies until soft inside.', 40, 120.00, 'brownies.jpg', 1, 4, '2025-10-04 06:43:26', '2025-10-04 06:43:26'),
(124, 'Ice Cream Sundae', 'Dessert', 'Scoop ice cream and add toppings.', 10, 100.00, 'ice_cream_sundae.jpg', 1, 5, '2025-10-04 06:43:26', '2025-10-04 06:43:26'),
(125, 'Iced Tea', 'Beverage', 'Mix tea with ice and sugar.', 5, 50.00, 'iced_tea.jpg', 1, 1, '2025-10-04 06:43:26', '2025-10-04 06:43:26'),
(126, 'Fresh Juice', 'Beverage', 'Blend fresh fruits. Serve chilled.', 5, 60.00, 'fresh_juice.jpg', 1, 2, '2025-10-04 06:43:26', '2025-10-04 06:43:26'),
(127, 'Lemonade', 'Beverage', 'Mix lemon, water, and sugar. Serve cold.', 5, 45.00, 'lemonade.jpg', 1, 3, '2025-10-04 06:43:26', '2025-10-04 06:43:26'),
(128, 'Coffee', 'Beverage', 'Brew coffee and serve hot.', 10, 70.00, 'coffee.jpg', 1, 4, '2025-10-04 06:43:26', '2025-10-04 06:43:26'),
(129, 'Chocolate Milkshake', 'Beverage', 'Blend milk with chocolate syrup and ice.', 7, 90.00, 'chocolate_milkshake.jpg', 1, 5, '2025-10-04 06:43:26', '2025-10-04 06:43:26');

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `refund_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `guest_name` varchar(255) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT 'Cash',
  `status` enum('Pending','Completed') DEFAULT 'Completed',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `replacement_orders`
--

CREATE TABLE `replacement_orders` (
  `id` int(11) NOT NULL,
  `original_order_id` varchar(50) NOT NULL,
  `order_type` varchar(100) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `table_number` varchar(50) DEFAULT NULL,
  `room_number` varchar(50) DEFAULT NULL,
  `assigned_chef` int(11) DEFAULT NULL,
  `guest_name` varchar(255) DEFAULT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `item` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `complain_reason` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `replacement_orders`
--

INSERT INTO `replacement_orders` (`id`, `original_order_id`, `order_type`, `status`, `table_number`, `room_number`, `assigned_chef`, `guest_name`, `guest_id`, `item`, `total_amount`, `complain_reason`, `created_at`, `updated_at`) VALUES
(1, '2387', 'Gift Store', 'pending', NULL, NULL, 0, 'Emily Brown', 4, 'Fragrant Candle', 0.00, 'Damaged Product', '2025-10-09 00:01:03', '2025-10-09 00:01:03'),
(2, '2387', 'Gift Store', 'pending', NULL, NULL, 0, 'Emily Brown', 4, 'Handmade Bracelet', 0.00, 'Missing Item', '2025-10-09 00:01:39', '2025-10-09 00:01:39'),
(3, '2387', 'Gift Store', 'pending', NULL, NULL, 0, 'Emily Brown', 4, 'Fragrant Candle', 0.00, 'Damaged Product', '2025-10-09 00:06:53', '2025-10-09 00:06:53'),
(4, '9961', 'Restaurant', 'pending', '1', NULL, 0, 'Sarah Miller', 6, 'Tinolang Manok', 0.00, 'Bland / Tasteless', '2025-10-10 19:36:20', '2025-10-10 19:36:20');

-- --------------------------------------------------------

--
-- Table structure for table `reported_items`
--

CREATE TABLE `reported_items` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `guest_name` varchar(255) DEFAULT NULL,
  `reported_item` varchar(255) NOT NULL,
  `complain_reason` text DEFAULT NULL,
  `resolution` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `reported_at` datetime DEFAULT current_timestamp(),
  `order_type` varchar(100) NOT NULL,
  `assigned_cashier` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reported_items`
--

INSERT INTO `reported_items` (`id`, `order_id`, `guest_id`, `guest_name`, `reported_item`, `complain_reason`, `resolution`, `status`, `reported_at`, `order_type`, `assigned_cashier`) VALUES
(10, '2387', 4, 'Emily Brown', 'Fragrant Candle', 'Damaged Product', 'Replacement', 'Replaced', '2025-10-09 00:17:20', 'Gift Store', 'EMP180969'),
(11, '8894', 2, 'Jane Smith', 'Fridge Magnet Set', 'Wrong Item', 'Replacement', 'Replaced', '2025-10-10 19:59:35', 'Gift Store', 'EMP862997'),
(12, '3489', 1, 'John Doe', 'Lemonade Bottle', 'Expired Item', 'Replacement', 'Replaced', '2025-10-10 20:05:32', 'Mini Bar', 'EMP862997');

-- --------------------------------------------------------

--
-- Table structure for table `reported_order`
--

CREATE TABLE `reported_order` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `recipe_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `complain_reason` varchar(255) DEFAULT NULL,
  `resolution` varchar(255) NOT NULL,
  `status` enum('pending','preparing','Ready','completed') DEFAULT 'pending',
  `reported_at` datetime DEFAULT current_timestamp(),
  `item` varchar(255) DEFAULT NULL,
  `assigned_chef` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `action` varchar(50) DEFAULT 'none',
  `estimated_time` time NOT NULL,
  `order_type` enum('Restaurant','Room Service') NOT NULL,
  `table_number` varchar(50) DEFAULT NULL,
  `room_number` varchar(50) DEFAULT NULL,
  `guest_name` varchar(50) NOT NULL,
  `priority` int(11) NOT NULL,
  `order_notes` text NOT NULL,
  `report_type` enum('Late Delivery','Cold','Bland','Raw','Missing','Wrong Item','Spilled','Overcooked','Undercooked','Wrong Portion') NOT NULL DEFAULT 'Late Delivery'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reported_order`
--

INSERT INTO `reported_order` (`id`, `order_id`, `guest_id`, `recipe_id`, `quantity`, `complain_reason`, `resolution`, `status`, `reported_at`, `item`, `assigned_chef`, `notes`, `action`, `estimated_time`, `order_type`, `table_number`, `room_number`, `guest_name`, `priority`, `order_notes`, `report_type`) VALUES
(3, 8104, 2, NULL, 1, 'Undercooked / Raw', 'Refund', 'pending', '2025-10-08 23:59:37', 'Cheesecake', 0, NULL, 'none', '00:00:00', 'Room Service', NULL, '102', 'Jane Smith', 0, '', 'Late Delivery'),
(4, 8115, 2, NULL, 1, 'Undercooked / Raw', 'Refund', 'pending', '2025-10-10 19:33:16', 'Pork Adobo', 0, NULL, 'none', '00:00:00', 'Room Service', NULL, '102', 'Jane Smith', 0, '', 'Late Delivery'),
(5, 9961, 6, NULL, 1, 'Bland / Tasteless', 'Replacement', 'pending', '2025-10-10 19:36:20', 'Tinolang Manok', 0, NULL, 'none', '00:00:00', 'Restaurant', '1', NULL, 'Sarah Miller', 0, '', 'Late Delivery'),
(6, 8115, 2, NULL, 1, 'Bland / Tasteless', 'Refund', 'pending', '2025-10-10 19:37:45', 'Kare-Kare', 0, NULL, 'none', '00:00:00', 'Room Service', NULL, '102', 'Jane Smith', 0, '', 'Late Delivery'),
(7, 8115, 2, NULL, 1, 'Late Delivery', 'Refund', 'pending', '2025-10-10 19:46:22', 'Crispy Pata', 0, NULL, 'none', '00:00:00', 'Room Service', NULL, '102', 'Jane Smith', 0, '', 'Late Delivery');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('reserved','checked_in','checked_out','cancelled') NOT NULL DEFAULT 'reserved',
  `check_in` datetime DEFAULT NULL,
  `check_out` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`reservation_id`, `guest_id`, `room_id`, `remarks`, `status`, `check_in`, `check_out`) VALUES
(1, 1, 1, '2 nights stay', 'checked_in', '2025-10-07 12:00:00', '2025-10-09 11:00:00'),
(2, 2, 2, '1 night business trip', 'checked_in', '2025-10-07 14:00:00', '2025-10-08 11:00:00'),
(3, 3, 3, 'Family visit', 'reserved', '2025-10-10 12:00:00', '2025-10-12 11:00:00'),
(4, 4, 4, 'Weekend staycation', 'checked_in', '2025-10-06 13:00:00', '2025-10-08 11:00:00'),
(5, 5, 5, 'Early check-in', 'checked_in', '2025-10-07 10:00:00', '2025-10-09 09:00:00'),
(6, 6, 6, 'Checked out early', 'checked_out', '2025-10-02 12:00:00', '2025-10-04 11:00:00'),
(7, 7, 7, 'Corporate guest', 'reserved', '2025-10-09 11:00:00', '2025-10-11 10:00:00'),
(8, 8, 8, '3-night reservation', 'checked_in', '2025-10-07 12:00:00', '2025-10-10 11:00:00'),
(9, 9, 9, 'Anniversary stay', 'reserved', '2025-10-08 12:00:00', '2025-10-10 11:00:00'),
(10, 10, 10, 'Vacation booking', 'checked_out', '2025-10-01 12:00:00', '2025-10-03 11:00:00'),
(11, 11, 1, '1 night transit', 'checked_in', '2025-10-07 15:00:00', '2025-10-08 10:00:00'),
(12, 12, 2, 'Family visit', 'reserved', '2025-10-09 12:00:00', '2025-10-11 11:00:00'),
(13, 13, 3, 'Short stay', 'checked_in', '2025-10-06 10:00:00', '2025-10-07 09:00:00'),
(14, 14, 4, 'Business meeting', 'checked_in', '2025-10-07 16:00:00', '2025-10-08 11:00:00'),
(15, 15, 5, 'Romantic getaway', 'reserved', '2025-10-08 12:00:00', '2025-10-10 11:00:00'),
(16, 16, 6, 'Family vacation', 'checked_in', '2025-10-06 11:00:00', '2025-10-09 10:00:00'),
(17, 17, 7, 'Checked in early', 'checked_in', '2025-10-07 09:00:00', '2025-10-09 08:00:00'),
(18, 18, 8, 'Conference stay', 'reserved', '2025-10-09 13:00:00', '2025-10-11 11:00:00'),
(19, 19, 9, 'Stay for event', 'checked_in', '2025-10-07 12:30:00', '2025-10-09 10:00:00'),
(20, 20, 10, 'Short rest', 'checked_out', '2025-10-03 12:00:00', '2025-10-04 10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `room_type` enum('Single Room','Double Room','Twin Room','Deluxe Room','Suite','Family Room') NOT NULL,
  `max_occupancy` int(11) DEFAULT NULL,
  `status` enum('available','occupied','reserved','under maintenance','dirty') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_number`, `room_type`, `max_occupancy`, `status`, `created_at`, `updated_at`) VALUES
(1, '101', 'Single Room', 1, 'occupied', '2025-10-07 15:09:20', '2025-10-07 15:09:20'),
(2, '102', 'Single Room', 1, 'available', '2025-10-07 15:09:20', '2025-10-07 15:09:20'),
(3, '103', 'Double Room', 2, 'occupied', '2025-10-07 15:09:20', '2025-10-07 15:09:20'),
(4, '104', 'Double Room', 2, 'available', '2025-10-07 15:09:20', '2025-10-07 15:09:20'),
(5, '105', 'Twin Room', 2, 'occupied', '2025-10-07 15:09:20', '2025-10-07 15:09:20'),
(6, '106', 'Twin Room', 2, 'available', '2025-10-07 15:09:20', '2025-10-07 15:09:20'),
(7, '107', 'Deluxe Room', 3, 'available', '2025-10-07 15:09:20', '2025-10-07 15:09:20'),
(8, '108', 'Deluxe Room', 3, 'occupied', '2025-10-07 15:09:20', '2025-10-07 15:09:20'),
(9, '109', 'Suite', 4, 'available', '2025-10-07 15:09:20', '2025-10-07 15:09:20'),
(10, '110', 'Suite', 4, 'occupied', '2025-10-07 15:09:20', '2025-10-07 15:09:20'),
(11, '111', 'Family Room', 5, 'available', '2025-10-07 15:09:20', '2025-10-07 15:09:20'),
(12, '112', 'Family Room', 5, 'occupied', '2025-10-07 15:09:20', '2025-10-07 15:09:20'),
(13, '113', 'Single Room', 1, 'available', '2025-10-07 15:09:20', '2025-10-07 15:09:20'),
(14, '114', 'Double Room', 2, 'available', '2025-10-07 15:09:20', '2025-10-07 15:09:20'),
(15, '115', 'Twin Room', 2, 'available', '2025-10-07 15:09:20', '2025-10-07 15:09:20'),
(16, '116', 'Deluxe Room', 3, 'occupied', '2025-10-07 15:09:20', '2025-10-07 15:09:20'),
(17, '117', 'Suite', 4, 'available', '2025-10-07 15:09:20', '2025-10-07 15:09:20'),
(18, '118', 'Family Room', 5, 'available', '2025-10-07 15:09:20', '2025-10-07 15:09:20'),
(19, '119', 'Single Room', 1, 'occupied', '2025-10-07 15:09:20', '2025-10-07 15:09:20'),
(20, '120', 'Double Room', 2, 'available', '2025-10-07 15:09:20', '2025-10-07 15:09:20');

--
-- Triggers `rooms`
--
DELIMITER $$
CREATE TRIGGER `rooms_before_insert` BEFORE INSERT ON `rooms` FOR EACH ROW SET NEW.created_at = CURRENT_TIMESTAMP
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `rooms_before_update` BEFORE UPDATE ON `rooms` FOR EACH ROW SET NEW.updated_at = CURRENT_TIMESTAMP
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` varchar(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `birth_date` date NOT NULL DEFAULT '2000-01-01',
  `gender` enum('Male','Female') NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `hire_date` date NOT NULL,
  `employment_status` enum('Active','Inactive','Probation','Resigned','Terminated','Floating','Lay Off') NOT NULL,
  `schedule_start_time` time DEFAULT NULL,
  `schedule_end_time` time DEFAULT NULL,
  `manager` varchar(200) DEFAULT NULL,
  `employment_type` enum('Full-time','Part-time','Contract','Internship') NOT NULL,
  `base_salary` decimal(10,2) NOT NULL,
  `hourly_rate` int(11) NOT NULL,
  `contract_file` varchar(255) DEFAULT NULL,
  `id_proof` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `account_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `emergency_contact` varchar(100) DEFAULT NULL,
  `health_insurance` varchar(100) DEFAULT NULL,
  `vacation_days` int(11) DEFAULT 0,
  `position_name` enum('Front Office Manager','Assistant Front Office Manager','Concierge','Room Attendant','Laundry Supervisor','Assistant Housekeeper','Cashier','Bartender','Baker','Sous Chef','F&B Manager','Chef de Partie','Demi Chef de Partie','Assistant F&B Manager','Waiter / Waitress','Restaurant Manager','Chief Engineer','Assistant Engineer','Inventory Manager','Inventory Inspector') NOT NULL,
  `department_name` enum('Front Office','Housekeeping','Food & Beverage Service','Kitchen / Food Production','Engineering / Maintenance','Security','Sales & Marketing','Finance & Accounting','Human Resources','Recreation / Spa / Leisure','Events & Banquets') NOT NULL,
  `department_id` int(10) UNSIGNED DEFAULT NULL,
  `password` varchar(255) NOT NULL DEFAULT 'temp123',
  `contract_signed` tinyint(1) NOT NULL DEFAULT 0,
  `contract_signed_at` datetime DEFAULT NULL,
  `job_experience` text DEFAULT NULL,
  `school` text DEFAULT NULL,
  `failed_attempts` int(11) NOT NULL DEFAULT 0,
  `last_failed_at` datetime DEFAULT NULL,
  `sss_no` varchar(50) DEFAULT NULL,
  `philhealth_no` varchar(50) DEFAULT NULL,
  `pagibig_no` varchar(50) DEFAULT NULL,
  `tin_no` varchar(50) DEFAULT NULL,
  `nbi_clearance` varchar(255) DEFAULT NULL,
  `birth_certificate` varchar(255) DEFAULT NULL,
  `diploma` varchar(255) DEFAULT NULL,
  `tor` varchar(255) DEFAULT NULL,
  `barangay_clearance` varchar(255) DEFAULT NULL,
  `police_clearance` varchar(255) DEFAULT NULL,
  `daily_rate` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `first_name`, `last_name`, `birth_date`, `gender`, `email`, `phone`, `address`, `hire_date`, `employment_status`, `schedule_start_time`, `schedule_end_time`, `manager`, `employment_type`, `base_salary`, `hourly_rate`, `contract_file`, `id_proof`, `photo`, `bank_name`, `account_name`, `account_number`, `emergency_contact`, `health_insurance`, `vacation_days`, `position_name`, `department_name`, `department_id`, `password`, `contract_signed`, `contract_signed_at`, `job_experience`, `school`, `failed_attempts`, `last_failed_at`, `sss_no`, `philhealth_no`, `pagibig_no`, `tin_no`, `nbi_clearance`, `birth_certificate`, `diploma`, `tor`, `barangay_clearance`, `police_clearance`, `daily_rate`) VALUES
('EMP104006', 'Ryan', 'Torres', '2000-01-01', 'Male', 'ryan.torres@example.com', '09170208888', 'Mandaluyong', '2025-09-23', 'Active', '07:00:00', '15:00:00', 'Kimberly Lababo', 'Full-time', 22000.00, 125, '../contracts/contract_EMP104006.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Waiter / Waitress', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP128638', 'Sophie', 'Lopez', '2000-01-01', 'Male', 'sophie.lopez@example.com', '09170103333', 'Taguig', '2025-09-23', 'Active', '15:00:00', '23:00:00', 'Jessica Lopez', 'Full-time', 30000.00, 170, '../contracts/contract_EMP128638.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Room Attendant', 'Housekeeping', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP143950', 'John', 'Tan', '2000-01-01', 'Male', 'john.tan@example.com', '09170002222', 'Quezon City', '2025-09-23', 'Active', '07:00:00', '15:00:00', 'Kimberly Lababo', 'Full-time', 38000.00, 216, '../contracts/contract_EMP143950.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Front Office Manager', 'Front Office', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP180595', 'cheska', 'bautista', '2000-01-01', 'Male', 'cheska.jalotjot@icloud.com', '09941813832', 'block 28 lot 11 phase 6C, barangay gaya-gaya SJDM Bulacan', '2025-10-05', 'Active', '15:00:00', '23:00:00', 'Kimberly Lababo', 'Full-time', 50000.00, 284, '../contracts/contract_EMP806107.pdf', NULL, 'EMP180595_1759656686_515491318_1940225643385875_3575146492547846042_n.jpg', NULL, NULL, NULL, '', 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Waiter / Waitress', 'Food & Beverage Service', NULL, 'Admin123*', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP180969', 'Kevin', 'Cruz', '2000-01-01', 'Male', 'kevin.cruz@example.com', '09170204444', 'Mandaluyong', '2025-09-23', 'Active', '07:00:00', '15:00:00', 'Monica Montes', 'Full-time', 20000.00, 114, '../contracts/contract_EMP180969.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Cashier', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP222430', 'Victor', 'Lim', '2000-01-01', 'Male', 'victor.lim@example.com', '09170108888', 'Taguig', '2025-09-23', 'Active', '23:00:00', '07:00:00', 'Brian Reyes', 'Full-time', 20000.00, 114, '../contracts/contract_EMP222430.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Room Attendant', 'Housekeeping', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP234689', 'Olivia', 'Navarro', '2000-01-01', 'Male', 'olivia.navarro@example.com', '09170207777', 'Taguig', '2025-09-23', 'Active', '15:00:00', '23:00:00', 'Anna Delos Reyes', 'Full-time', 18000.00, 102, '../contracts/contract_EMP234689.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Cashier', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP305438', 'Michelle', 'Tan', '2000-01-01', 'Male', 'michelle.tan@example.com', '09170203333', 'Taguig', '2025-09-23', 'Active', '23:00:00', '07:00:00', 'Samantha Garcia', 'Full-time', 35000.00, 199, '../contracts/contract_EMP305438.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Assistant Housekeeper', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP344096', 'Ryan', 'Ildefonso', '2000-01-01', 'Male', 'ryan.santos@example.com', '09170106666', 'Mandaluyong', '2025-09-23', 'Active', '07:00:00', '15:00:00', 'Michelle Tan', 'Full-time', 24000.00, 136, '../contracts/contract_EMP344096.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Room Attendant', 'Housekeeping', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP354777', 'Clara', 'Velasco', '2000-01-01', 'Male', 'clara.velasco@example.com', '09170309999', 'Makati', '2025-09-23', 'Active', '07:00:00', '15:00:00', 'Samantha Garcia', 'Full-time', 25000.00, 142, '../contracts/contract_EMP354777.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Chef de Partie', 'Kitchen / Food Production', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP405676', 'Leo', 'Santos', '2000-01-01', 'Male', 'leo.santos@example.com', '09170006666', 'Mandaluyong', '2025-09-23', 'Active', '15:00:00', '23:00:00', 'Kimberly Lababo', 'Full-time', 22000.00, 125, '../contracts/contract_EMP405676.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Front Office Manager', 'Front Office', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP471209', 'Bella', 'Reyes', '2000-01-01', 'Male', 'bella.reyes@example.com', '09170402222', 'Quezon', '2025-09-23', 'Active', '23:00:00', '07:00:00', 'Kimberly Lababo', 'Full-time', 30000.00, 170, '../contracts/contract_EMP471209.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Chief Engineer', 'Engineering / Maintenance', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP490735', 'Anna', 'Delos Reyes', '2000-01-01', 'Male', 'anna.delosreyes@example.com', '09170009999', 'Makati City', '2025-09-23', 'Active', '23:00:00', '07:00:00', 'Monica Montes', 'Full-time', 40000.00, 227, '../contracts/contract_EMP490735.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Front Office Manager', 'Front Office', NULL, 'Admin123*', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP519331', 'Isabel', 'Reyes', '2000-01-01', 'Male', 'isabel.reyes@example.com', '09170105555', 'Makati City', '2025-09-23', 'Active', '15:00:00', '23:00:00', 'Leo Santos', 'Full-time', 30000.00, 170, '../contracts/contract_EMP519331.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Front Office Manager', 'Front Office', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP552091', 'Daniel', 'Lim', '2000-01-01', 'Male', 'daniel.lim@example.com', '09170206666', 'Quezon', '2025-09-23', 'Active', '07:00:00', '15:00:00', 'Monica Montes', 'Full-time', 28000.00, 159, '../contracts/contract_EMP552091.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Assistant Housekeeper', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP564471', 'Carlos', 'Dela Cruz', '2000-01-01', 'Male', 'carlos.delacruz@example.com', '09170102222', 'Quezon City', '2025-09-23', 'Active', '15:00:00', '23:00:00', 'Kimberly Lababo', 'Full-time', 28000.00, 159, '../contracts/contract_EMP564471.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Room Attendant', 'Housekeeping', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP575330', 'Clara', 'Navarro', '2000-01-01', 'Male', 'clara.navarro@example.com', '09170109999', 'Makati City', '2025-09-23', 'Active', '23:00:00', '07:00:00', 'Samantha Garcia', 'Full-time', 32000.00, 182, '../contracts/contract_EMP575330.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Room Attendant', 'Housekeeping', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP577457', 'Jessica', 'Lopez', '2000-01-01', 'Male', 'jessica.lopez@example.com', '09170201111', 'Makati', '2025-09-23', 'Active', '15:00:00', '23:00:00', 'Monica Montes', 'Full-time', 50000.00, 284, '../contracts/contract_EMP577457.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Assistant Housekeeper', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP598813', 'Sophia', 'Velasco', '2000-01-01', 'Male', 'sophia.velasco@example.com', '09170209999', 'Makati', '2025-09-23', 'Active', '15:00:00', '23:00:00', 'Samantha Garcia', 'Full-time', 20000.00, 114, '../contracts/contract_EMP598813.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Laundry Supervisor', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP633259', 'Maria', 'Sandoval', '2000-01-01', 'Male', 'maria@sandoval.com', '09077915906', 'Sjdm Bulacan', '2025-09-24', 'Active', '07:00:00', '15:00:00', 'Anna Delos Reyes', 'Full-time', 18000.00, 102, '../contracts/contract_EMP633259.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Concierge', 'Housekeeping', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP636819', 'Alice', 'Reyes', '2000-01-01', 'Male', 'alice.reyes@example.com', '09170001111', 'Makati City', '2025-09-23', 'Active', '15:00:00', '23:00:00', 'Monica Montes', 'Full-time', 45000.00, 256, '../contracts/contract_EMP636819.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Front Office Manager', 'Front Office', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP706934', 'Brian', 'Reyes', '2000-01-01', 'Male', 'brian.reyes@example.com', '09170202222', 'Quezon', '2025-09-23', 'Active', '07:00:00', '15:00:00', 'Brian Reyes', 'Full-time', 40000.00, 227, '../contracts/contract_EMP706934.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Laundry Supervisor', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP723571', 'Adrian', 'Santos', '2000-01-01', 'Male', 'adrian.santos@example.com', '09170401111', 'Makati', '2025-09-24', 'Active', '15:00:00', '23:00:00', 'Juan Dela Cruz', 'Full-time', 50000.00, 284, '../contracts/contract_EMP723571.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Chief Engineer', 'Engineering / Maintenance', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP738778', 'Rita', 'Gonzales', '2000-01-01', 'Male', 'rita.gonzales@example.com', '09170005555', 'Makati City', '2025-09-23', 'Active', '23:00:00', '07:00:00', 'Kimberly Lababo', 'Full-time', 18000.00, 102, '../contracts/contract_EMP738778.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Front Office Manager', 'Front Office', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP772760', 'Leah', 'Gonzales', '2000-01-01', 'Male', 'leah.gonzales@example.com', '09170107777', 'Quezon City', '2025-09-23', 'Active', '15:00:00', '23:00:00', 'Anna Delos Reyes', 'Full-time', 16000.00, 91, '../contracts/contract_EMP772760.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Concierge', 'Housekeeping', NULL, 'Admin123*', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP772910', 'Eric', 'Gonzales', '2000-01-01', 'Male', 'eric.gonzales@example.com', '09170405555', 'Makati', '2025-09-24', 'Active', '15:00:00', '23:00:00', 'John Tan', 'Full-time', 20000.00, 114, '../contracts/contract_EMP772910.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Chief Engineer', 'Engineering / Maintenance', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP774690', 'Paul', 'Tan', '2000-01-01', 'Male', 'paul.tan@example.com', '09170302222', 'Quezon', '2025-09-23', 'Active', '15:00:00', '23:00:00', 'Samantha Garcia', 'Full-time', 32000.00, 182, '../contracts/contract_EMP774690.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Chef de Partie', 'Kitchen / Food Production', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP798632', 'Nina', 'Reyes', '2000-01-01', 'Male', 'nina.reyes@example.com', '09170007777', 'Quezon City', '2025-09-23', 'Active', '07:00:00', '15:00:00', 'Samantha Garcia', 'Full-time', 23000.00, 131, '../contracts/contract_EMP798632.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Cashier', 'Front Office', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP804127', 'Daniel', 'Torres', '2000-01-01', 'Male', 'daniel.torres@example.com', '09170104444', 'Mandaluyong', '2025-09-23', 'Active', '23:00:00', '07:00:00', 'Samantha Garcia', 'Full-time', 18000.00, 102, '../contracts/contract_EMP804127.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Concierge', 'Housekeeping', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP814687', 'James', 'Garcia', '2000-01-01', 'Male', 'james.garcia@example.com', '09170304444', 'Mandaluyong', '2025-09-23', 'Active', '23:00:00', '07:00:00', 'Samantha Garcia', 'Full-time', 28000.00, 159, '../contracts/contract_EMP814687.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Chef de Partie', 'Kitchen / Food Production', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP850049', 'Diana', 'Torres', '2000-01-01', 'Male', 'diana.torres@example.com', '09170404444', 'Mandaluyong', '2025-09-24', 'Active', '23:00:00', '07:00:00', 'Juan Dela Cruz', 'Full-time', 22000.00, 125, '../contracts/contract_EMP850049.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Chief Engineer', 'Engineering / Maintenance', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP862997', 'Mark', 'Gonzales', '2000-01-01', 'Male', 'mark.gonzales@example.com', '09170211111', 'Quezon', '2025-09-23', 'Active', '23:00:00', '07:00:00', 'Mary Lopez', 'Full-time', 18000.00, 102, '../contracts/contract_EMP862997.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Cashier', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP880782', 'Laura', 'Reyes', '2000-01-01', 'Male', 'laura.reyes@example.com', '09170301111', 'Makati', '2025-09-23', 'Active', '23:00:00', '07:00:00', 'Samantha Garcia', 'Full-time', 55000.00, 313, '../contracts/contract_EMP880782.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Chef de Partie', 'Kitchen / Food Production', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP910327', 'Leo', 'Gonzales', '2000-01-01', 'Male', 'leo.gonzales@example.com', '09170308888', 'Mandaluyong', '2025-09-23', 'Active', '15:00:00', '23:00:00', 'Samantha Garcia', 'Full-time', 35000.00, 199, '../contracts/contract_EMP910327.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Chef de Partie', 'Kitchen / Food Production', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP912822', 'Paul', 'Cruz', '2000-01-01', 'Male', 'paul.cruz@example.com', '09170004444', 'Taguig', '2025-09-23', 'Active', '07:00:00', '15:00:00', 'John Tan', 'Full-time', 26000.00, 148, '../contracts/contract_EMP912822.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Assistant Front Office Manager', 'Front Office', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP926087', 'Emma', 'Garcia', '2000-01-01', 'Male', 'emma.garcia@example.com', '09170101111', 'Makati City', '2025-09-23', 'Active', '07:00:00', '15:00:00', 'Samantha Garcia', 'Full-time', 40000.00, 227, '../contracts/contract_EMP926087.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Concierge', 'Housekeeping', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP928943', 'Mary', 'Lopez', '2000-01-01', 'Male', 'mary.lopez@example.com', '09170003333', 'Mandaluyong', '2025-09-23', 'Active', '23:00:00', '07:00:00', 'Kimberly Lababo', 'Full-time', 25000.00, 142, '../contracts/contract_EMP928943.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Assistant Front Office Manager', 'Front Office', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP981855', 'Nerie Ann', 'Sarabia', '2000-01-01', 'Male', 'nerie@gmail.com', '09077915906', 'sjdm Bulacan', '2025-09-23', 'Active', '23:00:00', '07:00:00', 'Monica Montes', 'Full-time', 18000.00, 102, '../contracts/contract_EMP981855.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Cashier', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN001', 'Kimberly', 'Lababo', '1980-05-15', 'Female', 'kimberlylababo@gmail.com', '09077933311', 'sjdm bulacan', '2020-01-01', 'Active', '07:00:00', '15:00:00', 'Monica Montes', 'Full-time', 45000.00, 256, 'contracts/EMPGEN001.pdf', 'EMPGEN001_id.jpg', 'EMPGEN001_photo.jpg', 'BPI', 'Kimberly Lababo', '99988877766', '', 'Health Insurance, Paid Leave, 13th Month Pay, Car Allowance', 20, 'Front Office Manager', 'Front Office', NULL, 'Admin123*', 1, '2025-09-24 00:38:57', '10+ years in hotel management', 'University of the Philippines', 0, NULL, 'SSS9001', 'PH9001', 'PG9001', 'TIN9001', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 0.00),
('EMPGEN112', 'Ramon', 'Santos', '1993-03-12', 'Male', 'ramon.santos@example.com', '09170001123', 'Makati', '2025-10-10', 'Active', '07:00:00', '15:00:00', 'F&B Manager', 'Full-time', 22000.00, 125, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Bartender', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN113', 'Marvin', 'Reyes', '1992-06-14', 'Male', 'marvin.reyes@example.com', '09170001124', 'Taguig', '2025-10-10', 'Active', '15:00:00', '23:00:00', 'F&B Manager', 'Full-time', 22000.00, 125, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Bartender', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN114', 'Maricel', 'Reyes', '1992-07-21', 'Female', 'maricel.reyes@example.com', '09170002234', 'Taguig', '2025-10-10', 'Active', '07:00:00', '15:00:00', 'Chef de Partie', 'Full-time', 24000.00, 136, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Baker', 'Kitchen / Food Production', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN115', 'Josie', 'Navarro', '1993-01-18', 'Female', 'josie.navarro@example.com', '09170002235', 'Quezon City', '2025-10-10', 'Active', '15:00:00', '23:00:00', 'Chef de Partie', 'Full-time', 24000.00, 136, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Baker', 'Kitchen / Food Production', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN116', 'Dennis', 'Lopez', '1990-05-10', 'Male', 'dennis.lopez@example.com', '09170003345', 'Quezon City', '2025-10-10', 'Active', '23:00:00', '07:00:00', 'John Tan', '', 40000.00, 227, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Front Office Manager', 'Front Office', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN117', 'Jayson', 'Torres', '1991-09-22', 'Male', 'jayson.torres@example.com', '09170003346', 'Makati', '2025-10-10', 'Active', '07:00:00', '15:00:00', 'John Tan', '', 40000.00, 227, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Front Office Manager', 'Front Office', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN118', 'Patricia', 'Torres', '1988-08-15', 'Female', 'patricia.torres@example.com', '09170004456', 'Makati', '2025-10-10', 'Active', '07:00:00', '15:00:00', 'General Manager', 'Full-time', 45000.00, 256, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'F&B Manager', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN119', 'Marvin', 'Gonzales', '1989-11-30', 'Male', 'marvin.gonzales@example.com', '09170004457', 'Quezon City', '2025-10-10', 'Active', '15:00:00', '23:00:00', 'General Manager', 'Full-time', 45000.00, 256, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'F&B Manager', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN120', 'Joshua', 'Navarro', '1995-01-12', 'Male', 'joshua.navarro@example.com', '09170005567', 'Quezon City', '2025-10-10', 'Active', '23:00:00', '07:00:00', 'Chef de Partie', 'Full-time', 26000.00, 148, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Demi Chef de Partie', 'Kitchen / Food Production', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN121', 'Elena', 'Santos', '1994-03-08', 'Female', 'elena.santos@example.com', '09170005568', 'Makati', '2025-10-10', 'Active', '07:00:00', '15:00:00', 'Chef de Partie', 'Full-time', 26000.00, 148, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Demi Chef de Partie', 'Kitchen / Food Production', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN122', 'Erika', 'Gonzales', '1992-04-30', 'Female', 'erika.gonzales@example.com', '09170006678', 'Mandaluyong', '2025-10-10', 'Active', '15:00:00', '23:00:00', 'F&B Manager', 'Full-time', 30000.00, 170, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Assistant F&B Manager', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN123', 'Anton', 'Reyes', '1993-08-15', 'Male', 'anton.reyes@example.com', '09170006679', 'Taguig', '2025-10-10', 'Active', '23:00:00', '07:00:00', 'F&B Manager', 'Full-time', 30000.00, 170, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Assistant F&B Manager', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN124', 'Riza', 'Delos Reyes', '1993-11-05', 'Female', 'riza.delosreyes@example.com', '09170007789', 'Taguig', '2025-10-10', 'Active', '07:00:00', '15:00:00', 'Kimberly Lababo\n', 'Full-time', 22000.00, 125, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Waiter / Waitress', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN125', 'Paulo', 'Santos', '1994-06-17', 'Male', 'paulo.santos@example.com', '09170008890', 'Makati', '2025-10-10', 'Active', '15:00:00', '23:00:00', 'Kimberly Lababo\n', 'Full-time', 22000.00, 125, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Waiter / Waitress', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN126', 'Angela', 'Reyes', '1995-02-20', 'Female', 'angela.reyes@example.com', '09170009901', 'Quezon City', '2025-10-10', 'Active', '23:00:00', '07:00:00', 'Kimberly Lababo\n', 'Full-time', 22000.00, 125, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Waiter / Waitress', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN127', 'Mark', 'Torres', '1993-09-10', 'Male', 'mark.torres@example.com', '09170100012', 'Mandaluyong', '2025-10-10', 'Active', '07:00:00', '15:00:00', 'Kimberly Lababo\n', 'Full-time', 22000.00, 125, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Waiter / Waitress', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN128', 'Lea', 'Gonzales', '1994-12-08', 'Female', 'lea.gonzales@example.com', '09170100013', 'Quezon City', '2025-10-10', 'Active', '15:00:00', '23:00:00', 'Kimberly Lababo\n', 'Full-time', 22000.00, 125, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Waiter / Waitress', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN129', 'Jomar', 'Delos Santos', '1992-10-12', 'Male', 'jomar.delossantos@example.com', '09170100014', 'Makati', '2025-10-10', 'Active', '23:00:00', '07:00:00', 'Kimberly Lababo\n', 'Full-time', 22000.00, 125, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Waiter / Waitress', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN130', 'Cecilia', 'Garcia', '1989-12-25', 'Female', 'cecilia.garcia@example.com', '09170101123', 'Makati', '2025-10-10', 'Active', '07:00:00', '15:00:00', 'General Manager', 'Full-time', 50000.00, 284, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Restaurant Manager', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN131', 'Julian', 'Torres', '1988-07-14', 'Male', 'julian.torres@example.com', '09170101124', 'Quezon City', '2025-10-10', 'Active', '15:00:00', '23:00:00', 'General Manager', 'Full-time', 50000.00, 284, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Restaurant Manager', 'Food & Beverage Service', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN132', 'Edwin', 'Lopez', '1987-04-17', 'Male', 'edwin.lopez@example.com', '09170102234', 'Quezon City', '2025-10-10', 'Active', '07:00:00', '15:00:00', 'John Tan', '', 40000.00, 227, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Inventory Manager', 'Front Office', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN133', 'Lorraine', 'Santos', '1990-10-05', 'Female', 'lorraine.santos@example.com', '09170103345', 'Mandaluyong', '2025-10-10', 'Active', '15:00:00', '23:00:00', 'John Tan', '', 40000.00, 227, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Inventory Manager', 'Front Office', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN134', 'Victor', 'Reyes', '1992-08-12', 'Male', 'victor.reyes@example.com', '09170104456', 'Taguig', '2025-10-10', 'Active', '23:00:00', '07:00:00', 'John Tan', '', 30000.00, 170, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Inventory Inspector', 'Front Office', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMPGEN135', 'Carla', 'Delos Santos', '1993-02-20', 'Female', 'carla.delossantos@example.com', '09170105567', 'Makati', '2025-10-10', 'Active', '07:00:00', '15:00:00', 'John Tan', '', 30000.00, 170, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Inventory Inspector', 'Front Office', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('ENG001', 'Juan', 'Dela Cruz', '1985-04-12', 'Male', 'juan.delacruz@example.com', '09171234567', '123 Mabuhay St., Manila', '2020-01-15', 'Active', '07:00:00', '15:00:00', 'Juan Dela Cruz', 'Full-time', 50000.00, 284, NULL, NULL, NULL, 'BPI', 'Juan Dela Cruz', '1234567890', 'Maria Dela Cruz - 09179876543', 'Maxicare', 15, 'Chief Engineer', 'Engineering / Maintenance', 5, 'temp123', 1, '2020-01-15 09:00:00', '10 years experience in hotel engineering', 'UST', 0, NULL, '34-5678901-2', '12-345678901-2', '123456789', '123-456-789-0', NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('ENG002', 'Pedro', 'Santos', '1990-07-23', 'Male', 'pedro.santos@example.com', '09172345678', '45 Sampaguita Ave., Quezon City', '2021-05-10', 'Active', '07:00:00', '15:00:00', 'Juan Dela Cruz', 'Full-time', 30000.00, 170, NULL, NULL, NULL, 'BDO', 'Pedro Santos', '9876543210', 'Ana Santos - 09171112222', 'PhilHealth', 12, 'Assistant Engineer', 'Engineering / Maintenance', 5, 'temp123', 1, '2021-05-10 09:00:00', '5 years in maintenance works', 'UP Diliman', 0, NULL, '35-6789012-3', '23-456789012-3', '234567890', '234-567-890-1', NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('ENG003', 'Luz', 'Reyes', '1992-12-01', 'Female', 'luz.reyes@example.com', '09173456789', '78 Rizal St., Makati', '2022-03-18', 'Probation', '23:00:00', '07:00:00', 'Juan Dela Cruz', 'Full-time', 25000.00, 142, NULL, NULL, NULL, 'Metrobank', 'Luz Reyes', '4567890123', 'Carlos Reyes - 09175553333', 'Maxicare', 10, 'Assistant Engineer', 'Engineering / Maintenance', 5, 'temp123', 0, NULL, '3 years in hotel technical support', 'FEU', 0, NULL, '36-7890123-4', '34-567890123-4', '345678901', '345-678-901-2', NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('ENG004', 'Rafael', 'Garcia', '1988-09-15', 'Male', 'rafael.garcia@example.com', '09174567890', '12 Quezon Blvd., Manila', '2019-11-25', 'Active', '15:00:00', '23:00:00', 'Juan Dela Cruz', 'Full-time', 22000.00, 125, NULL, NULL, NULL, 'BPI', 'Rafael Garcia', '5678901234', 'Luisa Garcia - 09176667777', 'Maxicare', 15, 'Assistant Engineer', 'Engineering / Maintenance', 5, 'temp123', 1, '2019-11-25 08:00:00', '7 years experience in carpentry', 'DLSU', 0, NULL, '37-8901234-5', '45-678901234-5', '456789012', '456-789-012-3', NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('ENG005', 'Maria', 'Lopez', '1995-06-30', 'Female', 'maria.lopez@example.com', '09175678901', '90 Mabini St., Pasay', '2023-01-05', 'Active', '23:00:00', '07:00:00', 'John Tan', '', 20000.00, 113, NULL, NULL, NULL, 'UnionBank', 'Maria Lopez', '6789012345', 'Jose Lopez - 09178889999', 'Maxicare', 12, 'Assistant Engineer', 'Front Office', 5, 'temp123', 1, '2023-01-05 10:00:00', '2 years experience painting interiors', 'UP Manila', 0, NULL, '38-9012345-6', '56-789012345-6', '567890123', '567-890-123-4', NULL, NULL, NULL, NULL, NULL, NULL, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `stock_usage`
--

CREATE TABLE `stock_usage` (
  `order_id` int(11) NOT NULL,
  `usage_id` int(11) NOT NULL,
  `item` varchar(255) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `guest_name` varchar(50) NOT NULL,
  `quantity_used` int(11) NOT NULL,
  `used_by` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_usage`
--

INSERT INTO `stock_usage` (`order_id`, `usage_id`, `item`, `guest_id`, `guest_name`, `quantity_used`, `used_by`, `created_at`) VALUES
(5006, 39, 'Handmade Bracelet', 2, 'Michael Smith', 1, 'Michael Smith', '2025-10-04 16:36:28'),
(5006, 40, 'Fridge Magnet Set', 2, 'Michael Smith', 1, 'Michael Smith', '2025-10-04 16:36:28'),
(5249, 41, 'Handmade Bracelet', 4, 'James Williams', 1, 'James Williams', '2025-10-04 16:37:42'),
(5249, 42, 'Ferrero Rocher Chocolate Box', 4, 'James Williams', 1, 'James Williams', '2025-10-04 16:37:42'),
(5249, 43, 'Decorative Coasters', 4, 'James Williams', 1, 'James Williams', '2025-10-04 16:37:42'),
(2027, 44, 'Ferrero Rocher Chocolate Box', 7, 'Charles Garcia', 1, 'Charles Garcia', '2025-10-04 16:38:58'),
(1775, 45, 'Arla Natural Cheese Mozzarella Cheese Slices', 12, 'Anthony Wilson', 2, 'Anthony Wilson', '2025-10-04 16:41:32'),
(3185, 47, 'Bacardi Gold Rum', 9, 'Christopher Hernandez', 1, 'Christopher Hernandez', '2025-10-04 17:12:52'),
(6556, 48, 'Baileys Irish Cream', 14, 'Paul Thomas', 1, 'Paul Thomas', '2025-10-04 19:47:03'),
(6556, 49, 'Chivas Regal 12 Years', 14, 'Paul Thomas', 2, 'Paul Thomas', '2025-10-04 19:47:03'),
(4970, 52, 'Cocktail - Mojito', 5, 'Robert Brown', 1, 'Robert Brown', '2025-10-04 19:49:43'),
(4970, 53, 'Cocktail - Martini', 5, 'Robert Brown', 1, 'Robert Brown', '2025-10-04 19:49:43'),
(4970, 54, 'Cocktail - Pina Colada', 5, 'Robert Brown', 1, 'Robert Brown', '2025-10-04 19:49:43'),
(7039, 55, 'Local Snack Box', 6, 'William Jones', 1, 'William Jones', '2025-10-04 20:44:24'),
(7039, 56, 'La Vista Chocolate Gift Box', 6, 'William Jones', 1, 'William Jones', '2025-10-04 20:44:24'),
(5436, 58, 'Fridge Magnet Set', 0, '', 1, '', '2025-10-04 20:44:53'),
(5891, 59, 'Beef Jerky', 5, 'Robert Brown', 1, 'Robert Brown', '2025-10-04 20:49:37'),
(5891, 60, 'Growers Mixed Nuts', 5, 'Robert Brown', 1, 'Robert Brown', '2025-10-04 20:49:37'),
(7774, 61, 'Chivas Regal 12 Years', 5, 'Robert Brown', 1, 'Robert Brown', '2025-10-04 20:50:27'),
(7774, 62, 'Baileys Irish Cream', 5, 'Robert Brown', 1, 'Robert Brown', '2025-10-04 20:50:27'),
(2354, 63, 'Fragrant Candle', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-08 21:05:31'),
(2354, 64, 'Ferrero Rocher Chocolate Box', 2, 'Jane Smith', 4, 'Jane Smith', '2025-10-08 21:05:31'),
(2354, 65, 'Handmade Bracelet', 2, 'Jane Smith', 2, 'Jane Smith', '2025-10-08 21:05:31'),
(1723, 66, 'Fridge Magnet Set', 0, '', 1, '', '2025-10-08 21:18:52'),
(1723, 67, 'Handmade Bracelet', 0, '', 4, '', '2025-10-08 21:18:52'),
(1723, 68, 'Fragrant Candle', 0, '', 2, '', '2025-10-08 21:18:52'),
(7191, 69, 'Fridge Magnet Set', 2, 'Jane Smith', 3, 'Jane Smith', '2025-10-08 21:21:35'),
(7191, 70, 'Fragrant Candle', 2, 'Jane Smith', 2, 'Jane Smith', '2025-10-08 21:21:35'),
(7965, 71, 'Cocktail - Daiquiri', 2, 'Jane Smith', 2, 'Jane Smith', '2025-10-08 22:20:27'),
(7965, 72, 'Cocktail - Margarita', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-08 22:20:27'),
(6020, 73, 'Fragrant Candle', 2, 'Jane Smith', 2, 'Jane Smith', '2025-10-08 23:00:57'),
(6020, 74, 'Ferrero Rocher Chocolate Box', 2, 'Jane Smith', 3, 'Jane Smith', '2025-10-08 23:00:57'),
(6020, 75, 'Fridge Magnet Set', 2, 'Jane Smith', 2, 'Jane Smith', '2025-10-08 23:00:57'),
(2387, 76, 'Fragrant Candle', 4, 'Emily Brown', 2, 'Emily Brown', '2025-10-08 23:25:08'),
(2387, 77, 'Fridge Magnet Set', 4, 'Emily Brown', 1, 'Emily Brown', '2025-10-08 23:25:08'),
(2387, 78, 'Handmade Bracelet', 4, 'Emily Brown', 1, 'Emily Brown', '2025-10-08 23:25:08'),
(2387, 79, 'Limited Edition Hotel Calendar', 4, 'Emily Brown', 1, 'Emily Brown', '2025-10-08 23:25:08'),
(2387, 80, 'La Vista Chocolate Gift Box', 4, 'Emily Brown', 1, 'Emily Brown', '2025-10-08 23:25:08'),
(8571, 81, 'Fragrant Candle', 0, '', 1, '', '2025-10-09 00:17:44'),
(8571, 82, 'Fridge Magnet Set', 0, '', 1, '', '2025-10-09 00:17:44'),
(8571, 83, 'Handmade Bracelet', 0, '', 1, '', '2025-10-09 00:17:44'),
(8571, 84, 'Limited Edition Hotel Calendar', 0, '', 1, '', '2025-10-09 00:17:44'),
(6989, 85, 'Fragrant Candle', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-09 00:18:17'),
(6989, 86, 'Handmade Bracelet', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-09 00:18:17'),
(6989, 87, 'Limited Edition Hotel Calendar', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-09 00:18:17'),
(6989, 88, 'La Vista Chocolate Gift Box', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-09 00:18:17'),
(7702, 89, 'Beef Jerky', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-09 00:31:20'),
(7702, 90, 'Candy Pack', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-09 00:31:20'),
(7702, 91, 'Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-09 00:31:20'),
(7702, 92, 'Cobra Energy Drink ', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-09 00:31:20'),
(7702, 93, 'KitKat Mini Pack', 2, 'Jane Smith', 2, 'Jane Smith', '2025-10-09 00:31:20'),
(7702, 94, 'Jack \'N Jill Vcut Spicy Barbeque ', 2, 'Jane Smith', 2, 'Jane Smith', '2025-10-09 00:31:20'),
(7702, 95, 'Jack n Jill Piattos Sour Cream', 2, 'Jane Smith', 2, 'Jane Smith', '2025-10-09 00:31:20'),
(3698, 96, 'Cocktail - Daiquiri', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-09 00:38:24'),
(3698, 97, 'Cocktail - Margarita', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-09 00:38:24'),
(3698, 98, 'Fruit Platter', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-09 00:38:24'),
(3698, 99, 'Cocktail Straw Set', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-09 00:38:24'),
(3698, 100, 'Cocktail - Tequila Sunrise', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-09 00:38:24'),
(9743, 101, 'Cobra Energy Drink ', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-09 00:42:00'),
(9743, 102, 'Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-09 00:42:00'),
(9743, 103, 'Coca-Cola Original Taste Soft Drink Can', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-09 00:42:00'),
(9743, 104, 'Lemonade Bottle', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-09 00:42:00'),
(9743, 105, 'Lays', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-09 00:42:00'),
(6014, 106, 'Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g', 4, 'Emily Brown', 1, 'Emily Brown', '2025-10-09 00:43:32'),
(6014, 107, 'Candy Pack', 4, 'Emily Brown', 1, 'Emily Brown', '2025-10-09 00:43:32'),
(6014, 108, 'Beef Jerky', 4, 'Emily Brown', 2, 'Emily Brown', '2025-10-09 00:43:32'),
(6014, 109, 'Arla Natural Cheese Mozzarella Cheese Slices', 4, 'Emily Brown', 1, 'Emily Brown', '2025-10-09 00:43:32'),
(6014, 110, 'Alaska Fruitti Yo! Strawberry Yoghurt Milk Drink ', 4, 'Emily Brown', 1, 'Emily Brown', '2025-10-09 00:43:32'),
(6014, 111, 'Lemonade Bottle', 4, 'Emily Brown', 1, 'Emily Brown', '2025-10-09 00:43:32'),
(3489, 112, 'Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g', 1, 'John Doe', 1, 'John Doe', '2025-10-09 00:45:02'),
(3489, 113, 'Candy Pack', 1, 'John Doe', 1, 'John Doe', '2025-10-09 00:45:02'),
(3489, 114, 'Jack n Jill Piattos Sour Cream', 1, 'John Doe', 1, 'John Doe', '2025-10-09 00:45:02'),
(3489, 115, 'KitKat Mini Pack', 1, 'John Doe', 1, 'John Doe', '2025-10-09 00:45:02'),
(3489, 116, 'Lays', 1, 'John Doe', 1, 'John Doe', '2025-10-09 00:45:02'),
(3489, 117, 'Lemonade Bottle', 1, 'John Doe', 1, 'John Doe', '2025-10-09 00:45:02'),
(3489, 118, 'Jack \'N Jill Vcut Spicy Barbeque ', 1, 'John Doe', 1, 'John Doe', '2025-10-09 00:45:02'),
(3489, 119, 'Growers Mixed Nuts', 1, 'John Doe', 1, 'John Doe', '2025-10-09 00:45:02'),
(9304, 120, 'Fridge Magnet Set', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-09 01:08:31'),
(9304, 121, 'Handmade Bracelet', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-09 01:08:31'),
(9304, 122, 'Ferrero Rocher Chocolate Box', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-09 01:08:31'),
(9304, 123, 'Limited Edition Hotel Calendar', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-09 01:08:31'),
(8219, 124, 'Cocktail - Daiquiri', 9, 'Robert Rodriguez', 1, 'Robert Rodriguez', '2025-10-09 01:16:46'),
(8219, 125, 'Chivas Regal 12 Years', 9, 'Robert Rodriguez', 1, 'Robert Rodriguez', '2025-10-09 01:16:46'),
(8219, 126, 'Cocktail - Margarita', 9, 'Robert Rodriguez', 16, 'Robert Rodriguez', '2025-10-09 01:16:46'),
(8894, 127, 'Fridge Magnet Set', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-10 19:16:26'),
(8894, 128, 'Fragrant Candle', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-10 19:16:26'),
(8894, 129, 'Handmade Bracelet', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-10 19:16:26'),
(1998, 130, 'Cocktail - Margarita', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-10 19:16:46'),
(1998, 131, 'Cocktail - Daiquiri', 2, 'Jane Smith', 1, 'Jane Smith', '2025-10-10 19:16:46'),
(6128, 132, 'Cocktail - Margarita', 4, 'Emily Brown', 1, 'Emily Brown', '2025-10-10 19:16:59'),
(6128, 133, 'Fruit Platter', 4, 'Emily Brown', 1, 'Emily Brown', '2025-10-10 19:16:59'),
(6128, 134, 'Cocktail Straw Set', 4, 'Emily Brown', 1, 'Emily Brown', '2025-10-10 19:16:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `guest_id` (`guest_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`guest_id`);

--
-- Indexes for table `guest_billing`
--
ALTER TABLE `guest_billing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `fk_inventory_inspector` (`inspected_by`);

--
-- Indexes for table `item_images`
--
ALTER TABLE `item_images`
  ADD PRIMARY KEY (`image_id`),
  ADD UNIQUE KEY `filename` (`filename`),
  ADD UNIQUE KEY `item_id` (`item_id`),
  ADD UNIQUE KEY `image_id` (`image_id`);

--
-- Indexes for table `kitchen_orders`
--
ALTER TABLE `kitchen_orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`refund_id`),
  ADD KEY `idx_order_item` (`order_id`,`item_name`);

--
-- Indexes for table `replacement_orders`
--
ALTER TABLE `replacement_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reported_items`
--
ALTER TABLE `reported_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reported_order`
--
ALTER TABLE `reported_order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `fk_reservations_guest` (`guest_id`),
  ADD KEY `fk_reservations_room` (`room_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `room_number` (`room_number`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD UNIQUE KEY `staff_id` (`staff_id`);

--
-- Indexes for table `stock_usage`
--
ALTER TABLE `stock_usage`
  ADD PRIMARY KEY (`usage_id`),
  ADD UNIQUE KEY `usage_id` (`usage_id`),
  ADD KEY `usage_id_2` (`usage_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `guest_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `guest_billing`
--
ALTER TABLE `guest_billing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1578;

--
-- AUTO_INCREMENT for table `item_images`
--
ALTER TABLE `item_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `refund_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `replacement_orders`
--
ALTER TABLE `replacement_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reported_items`
--
ALTER TABLE `reported_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `reported_order`
--
ALTER TABLE `reported_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `stock_usage`
--
ALTER TABLE `stock_usage`
  MODIFY `usage_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`);

--
-- Constraints for table `item_images`
--
ALTER TABLE `item_images`
  ADD CONSTRAINT `item_images_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `inventory` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `fk_reservations_guest` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reservations_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

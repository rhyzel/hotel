-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2025 at 11:26 AM
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

--
-- Dumping data for table `campaigns`
--

INSERT INTO `campaigns` (`id`, `name`, `description`, `type`, `target_audience`, `message`, `status`, `schedule`, `sent_count`, `open_rate`, `click_rate`, `created_by_user`, `created_at`, `updated_at`) VALUES
(1, 'Summer Welcome Package', 'Special welcome offer for summer guests', 'email', 'all', 'Welcome to our hotel! Enjoy complimentary breakfast with your summer stay.', 'active', NULL, 1250, 68.50, 24.30, NULL, '2025-09-13 02:16:35', '2025-10-08 18:17:30'),
(2, 'Loyalty Member Rewards', 'Exclusive rewards for platinum members', 'email', 'platinum', 'As a valued platinum member, enjoy these exclusive perks during your stay!', 'active', NULL, 245, 75.20, 32.10, NULL, '2025-09-13 02:16:35', '2025-10-08 18:19:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `campaigns`
--

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
  `status` enum('pending','resolved') DEFAULT 'pending',
  `reply` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `guest_id`, `guest_name`, `comment`, `type`, `status`, `reply`, `created_at`, `updated_at`) VALUES
(1, 1, 'Michael Thompson', 'not friendly staff', 'complaint', 'pending', NULL, '2025-10-16 08:10:33', NULL);

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `loyalty_tier` varchar(32) DEFAULT 'bronze'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guests`
--

INSERT INTO `guests` (`guest_id`, `first_name`, `last_name`, `email`, `first_phone`, `second_phone`, `status`, `created_at`, `updated_at`, `loyalty_tier`) VALUES
(1, 'Michael', 'Thompson', 'michael.thompson@email.com', '+63-917-111-2222', '+63-918-333-4444', 'vip', '2025-09-09 00:42:18', '2025-10-13 18:48:53', 'bronze'),
(2, 'Jessica', 'Martinez', 'jessica.martinez@email.com', '+63-919-555-6666', '', 'regular', '2025-09-09 00:42:18', '2025-10-13 18:23:38', 'bronze'),
(3, 'David', 'Lee', 'david.lee@email.com', '+63-920-777-8888', '+63-921-999-0000', 'regular', '2025-09-09 00:42:18', '2025-10-13 18:23:27', 'bronze'),
(4, 'Sophie', 'Brown', 'sophie.brown@email.com', '+63-917-222-3333', '', 'regular', '2025-09-09 00:42:18', '2025-10-13 18:23:57', 'bronze'),
(5, 'Carlos', 'Garcia', 'carlos.garcia@email.com', '+63-918-444-5555', '+63-919-666-7777', 'regular', '2025-09-09 00:42:18', '2025-10-13 18:23:23', 'bronze'),
(6, 'Emily', 'Wilson', 'emily.wilson@email.com', '+63-920-888-9999', '', 'regular', '2025-09-09 00:42:18', '2025-10-13 18:23:33', 'bronze'),
(7, 'James', 'Anderson', 'james.anderson@email.com', '+63-917-123-9876', '', 'regular', '2025-09-09 00:42:18', '2025-10-13 18:24:38', 'bronze'),
(8, 'Lisa', 'Taylor', 'lisa.taylor@email.com', '+63-918-987-6543', '+63-919-456-7890', 'regular', '2025-09-09 00:42:18', '2025-10-13 18:23:47', 'bronze');

-- --------------------------------------------------------

--
-- Table structure for table `guest_billing`
--

CREATE TABLE `guest_billing` (
  `id` int(11) NOT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `guest_name` varchar(255) DEFAULT NULL,
  `order_type` enum('Restaurant','Mini Bar','Lounge Bar','Gift Store','Room Service') DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `order_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_option` enum('Paid','To be billed') NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `partial_payment` decimal(10,2) DEFAULT 0.00,
  `remaining_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guest_billing`
--

INSERT INTO `guest_billing` (`id`, `guest_id`, `guest_name`, `order_type`, `item_name`, `order_id`, `total_amount`, `payment_option`, `payment_method`, `partial_payment`, `remaining_amount`, `created_at`, `updated_at`) VALUES
(304, 7, 'Charles Garcia', 'Room Service', 'Chicken Sopas', 4921, 180.00, 'To be billed', 'Card', 0.00, 180.00, '2025-10-03 22:22:47', '2025-10-03 22:22:47'),
(305, 7, 'Charles Garcia', 'Room Service', 'Bulalo', 1973, 320.00, 'To be billed', 'Card', 0.00, 320.00, '2025-10-03 22:23:11', '2025-10-03 22:23:11'),
(306, 5, 'Robert Brown', 'Room Service', 'Pancit Canton', 5991, 180.00, 'To be billed', 'Cash', 0.00, 180.00, '2025-10-03 22:26:06', '2025-10-03 22:26:06'),
(307, 7, 'Charles Garcia', 'Room Service', 'Chicken Sopas, Pancit Canton', 3844, 360.00, 'To be billed', 'Cash', 0.00, 360.00, '2025-10-03 22:44:31', '2025-10-03 22:44:31'),
(309, 5, 'Robert Brown', 'Room Service', 'Pancit Canton', 6320, 180.00, 'To be billed', 'Cash', 0.00, 180.00, '2025-10-03 22:46:50', '2025-10-03 22:46:50'),
(311, 6, 'William Jones', 'Room Service', 'Bulalo, Chicken Sopas', 7832, 500.00, 'To be billed', 'Cash', 0.00, 500.00, '2025-10-03 22:58:05', '2025-10-03 22:58:05'),
(313, 2, 'Michael Smith', 'Restaurant', 'Pancit Canton, Chicken Sopas', 9066, 360.00, 'Paid', 'GCash', 60.00, 300.00, '2025-10-03 22:59:58', '2025-10-03 22:59:58'),
(314, 3, 'David Johnson', 'Restaurant', 'Chicken Sopas', 6495, 180.00, 'To be billed', 'Cash', 0.00, 180.00, '2025-10-03 23:00:22', '2025-10-03 23:00:22'),
(315, 15, 'Steven Taylor', 'Room Service', 'Chicken Sopas, Pancit Canton', 6848, 360.00, 'Paid', 'BillEase', 60.00, 300.00, '2025-10-03 23:02:08', '2025-10-03 23:02:08'),
(316, 3, 'David Johnson', 'Restaurant', 'Pancit Canton, Pork Adobo', 2546, 400.00, 'To be billed', 'Cash', 0.00, 400.00, '2025-10-03 23:26:31', '2025-10-03 23:26:31'),
(317, 3, 'David Johnson', 'Gift Store', 'Handmade Bracelet, Fridge Magnet Set', 5591, 480.00, 'To be billed', 'Cash', 0.00, 480.00, '2025-10-03 23:31:17', '2025-10-03 23:31:17'),
(319, 2, 'Michael Smith', 'Gift Store', 'Fridge Magnet Set, Handmade Bracelet', 1081, 480.00, 'Paid', 'Cash', 22.00, 458.00, '2025-10-03 23:33:09', '2025-10-03 23:33:09'),
(324, 2, 'Michael Smith', 'Gift Store', 'Fridge Magnet Set, Handmade Bracelet', 4035, 480.00, 'Paid', 'Cash', 80.00, 400.00, '2025-10-03 23:38:03', '2025-10-03 23:38:03'),
(328, 4, 'James Williams', 'Gift Store', 'Handmade Bracelet, Fridge Magnet Set, Fragrant Candle', 6098, 830.00, 'To be billed', '-', 0.00, 830.00, '2025-10-03 23:50:03', '2025-10-16 15:46:36'),
(329, 4, 'James Williams', 'Gift Store', 'Ferrero Rocher Chocolate Box, Decorative Coasters', 4267, 400.00, 'Paid', 'GCash', 400.00, 0.00, '2025-10-03 23:50:18', '2025-10-03 23:50:18'),
(331, 7, 'Charles Garcia', 'Gift Store', 'Decorative Coasters, Coffee Mug, Ferrero Rocher Chocolate Box, Mini Photo Frame, Local Snack Pack', 7458, 1170.00, 'Paid', 'GCash', 170.00, 1000.00, '2025-10-03 23:51:01', '2025-10-03 23:51:01'),
(332, 4, 'James Williams', 'Restaurant', 'Pancit Canton, Pancit Bihon', 1359, 350.00, 'Paid', 'Cash', 50.00, 300.00, '2025-10-04 00:16:39', '2025-10-04 00:16:39'),
(333, 4, 'James Williams', 'Room Service', 'Ferrero Rocher Chocolate Box, Local Snack Box, Pancit Canton, Fragrant Candle, Fridge Magnet Set', 6731, 1310.00, 'To be billed', 'Cash', 0.00, 1310.00, '2025-10-04 00:23:25', '2025-10-04 00:23:25'),
(334, 5, 'Robert Brown', 'Room Service', 'Fridge Magnet Set, Handmade Bracelet, Miniature Figurine', 7076, 880.00, 'Paid', 'BillEase', 50.00, 830.00, '2025-10-04 00:28:29', '2025-10-04 00:28:29'),
(335, 5, 'Robert Brown', 'Gift Store', 'Miniature Figurine, Fridge Magnet Set', 4185, 580.00, 'To be billed', '-', 0.00, 580.00, '2025-10-04 00:28:58', '2025-10-16 15:46:36'),
(336, 6, 'William Jones', 'Gift Store', 'Fragrant Candle, Fridge Magnet Set, Mini Photo Frame', 8988, 750.00, 'To be billed', '-', 0.00, 750.00, '2025-10-04 00:29:44', '2025-10-16 15:46:36'),
(337, 9, 'Christopher Hernandez', 'Gift Store', 'Decorative Coasters, Ferrero Rocher Chocolate Box', 5604, 400.00, 'To be billed', '-', 0.00, 400.00, '2025-10-04 00:30:33', '2025-10-16 15:46:36'),
(338, 6, 'William Jones', 'Gift Store', 'Fridge Magnet Set, Handmade Bracelet, Fragrant Candle', 9032, 830.00, 'To be billed', '-', 0.00, 830.00, '2025-10-04 00:30:52', '2025-10-16 15:46:36'),
(339, 9, 'Christopher Hernandez', 'Gift Store', 'Fridge Magnet Set, Fragrant Candle, Ferrero Rocher Chocolate Box', 6633, 780.00, 'Paid', 'GCash', 80.00, 700.00, '2025-10-04 00:32:44', '2025-10-04 00:32:44'),
(340, 5, 'Robert Brown', 'Gift Store', 'Fridge Magnet Set, Fragrant Candle, Handmade Bracelet', 7929, 830.00, 'To be billed', '-', 0.00, 830.00, '2025-10-04 00:38:22', '2025-10-16 15:46:36'),
(341, 2, 'Michael Smith', 'Mini Bar', 'Cobra Energy Drink , Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g, Lemonade Bottle', 1847, 270.00, 'To be billed', '-', 0.00, 270.00, '2025-10-04 00:46:03', '2025-10-16 15:46:36'),
(345, 3, 'David Johnson', 'Mini Bar', 'Cobra Energy Drink , Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g', 5520, 190.00, 'To be billed', '-', 0.00, 190.00, '2025-10-04 01:03:03', '2025-10-16 15:46:36'),
(346, 7, 'Charles Garcia', 'Mini Bar', 'Beef Jerky, Arla Natural Cheese Mozzarella Cheese Slices, Growers Mixed Nuts, Ding Dong Snack Mix, Lays', 3550, 820.00, 'Paid', 'GCash', 100.00, 720.00, '2025-10-04 01:03:24', '2025-10-04 01:03:24'),
(347, 8, 'Thomas Martinez', 'Mini Bar', 'Cobra Energy Drink , Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g, Candy Pack', 2495, 240.00, 'To be billed', '-', 0.00, 240.00, '2025-10-04 01:05:09', '2025-10-16 15:46:36'),
(348, 2, 'Michael Smith', 'Mini Bar', 'Cobra Energy Drink , Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g, Candy Pack', 1170, 240.00, 'Paid', 'GCash', 42.00, 198.00, '2025-10-04 01:06:09', '2025-10-04 01:06:09'),
(355, 5, 'Robert Brown', 'Mini Bar', 'Cobra Energy Drink , Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g', 6669, 190.00, 'Paid', 'Card', 92.00, 98.00, '2025-10-04 01:25:39', '2025-10-04 01:25:39'),
(362, 5, 'Robert Brown', 'Lounge Bar', 'Cocktail - Mojito, Cocktail - Martini, Cocktail - Margarita', 8212, 870.00, 'To be billed', '-', 0.00, 870.00, '2025-10-04 01:29:52', '2025-10-16 15:46:36'),
(363, 7, 'Charles Garcia', 'Lounge Bar', 'Cocktail - Margarita, Jack Daniel\'s, Ginebra San Miguel Gin, Fruit Platter', 3739, 1260.00, 'Paid', 'GCash', 260.00, 1000.00, '2025-10-04 01:30:07', '2025-10-04 01:30:07'),
(368, 5, 'Robert Brown', 'Restaurant', 'Pancit Bihon, Pancit Canton, Chicken Sopas, Bulalo', 1034, 850.00, 'To be billed', 'Cash', 0.00, 850.00, '2025-10-04 01:38:04', '2025-10-04 01:38:04'),
(369, 20, 'Kevin Martin', 'Restaurant', 'Pancit Canton, Chicken Sopas', 7296, 360.00, 'Paid', 'Paymaya', 200.00, 160.00, '2025-10-04 02:09:29', '2025-10-04 02:09:29'),
(370, 1, 'John Doe', 'Lounge Bar', 'Cocktail - Mojito, Cocktail - Martini, Cocktail - Margarita', 6221, 870.00, 'Paid', 'Card', 100.00, 770.00, '2025-10-04 02:11:12', '2025-10-04 02:12:28'),
(372, 11, 'Matthew Gonzalez', 'Gift Store', 'Fridge Magnet Set, Fragrant Candle, Handmade Bracelet', 1689, 830.00, 'To be billed', '-', 0.00, 830.00, '2025-10-04 02:12:09', '2025-10-16 15:46:36'),
(373, 2, 'Michael Smith', 'Restaurant', 'Chicken Sopas, Bulalo', 1744, 500.00, 'Paid', 'Cash', 111.00, 389.00, '2025-10-04 02:18:45', '2025-10-04 02:18:45'),
(374, 1, 'John Doe', 'Restaurant', 'Chicken Sopas', 3450, 180.00, 'To be billed', 'Cash', 0.00, 180.00, '2025-10-04 02:20:02', '2025-10-04 02:20:02'),
(375, 6, 'William Jones', 'Restaurant', 'Pancit Bihon, Pancit Canton, Chicken Sopas', 8568, 530.00, 'Paid', 'GCash', 66.00, 464.00, '2025-10-04 02:21:59', '2025-10-04 02:21:59'),
(376, 3, 'David Johnson', 'Restaurant', 'Pancit Bihon, Pancit Canton', 9457, 350.00, 'To be billed', 'Cash', 0.00, 350.00, '2025-10-04 02:22:28', '2025-10-04 02:22:28'),
(377, 5, 'Robert Brown', 'Restaurant', 'Pancit Bihon, Pancit Canton', 7562, 350.00, 'To be billed', 'Cash', 0.00, 350.00, '2025-10-04 02:45:54', '2025-10-04 02:45:54'),
(378, 3, 'David Johnson', 'Restaurant', 'Lechon Kawali', 3604, 260.00, 'To be billed', 'Cash', 0.00, 260.00, '2025-10-04 03:05:09', '2025-10-04 03:05:09'),
(379, 1, 'John Doe', 'Restaurant', 'Sinigang na Baboy', 5556, 240.00, 'To be billed', 'Cash', 0.00, 240.00, '2025-10-04 03:11:52', '2025-10-04 03:11:52'),
(382, 6, 'William Jones', 'Restaurant', 'Pancit Canton', 3690, 360.00, 'Paid', 'GCash', 60.00, 300.00, '2025-10-04 12:39:32', '2025-10-04 12:39:32'),
(390, 4, 'James Williams', 'Gift Store', 'Fridge Magnet Set, Fragrant Candle, Ferrero Rocher Chocolate Box', 4922, 780.00, 'To be billed', '-', 0.00, 780.00, '2025-10-04 12:49:02', '2025-10-16 15:46:36'),
(396, 2, 'Michael Smith', 'Gift Store', 'Miniature Figurine, Handmade Bracelet, Fridge Magnet Set', 7746, 880.00, 'Paid', 'Cash', 500.00, 380.00, '2025-10-04 12:58:16', '2025-10-04 12:58:16'),
(397, 6, 'William Jones', 'Gift Store', 'Handmade Bracelet, Fridge Magnet Set, Fragrant Candle', 8252, 830.00, 'To be billed', '-', 0.00, 830.00, '2025-10-04 12:58:30', '2025-10-16 15:46:36'),
(398, 7, 'Charles Garcia', 'Gift Store', 'Fridge Magnet Set', 4918, 180.00, 'Paid', 'Cash', 80.00, 100.00, '2025-10-04 13:01:35', '2025-10-04 13:01:35'),
(407, 2, 'Michael Smith', 'Gift Store', 'Handmade Bracelet, Miniature Figurine', 5136, 700.00, 'To be billed', '-', 0.00, 700.00, '2025-10-04 13:11:00', '2025-10-16 15:46:36'),
(408, 6, 'William Jones', 'Mini Bar', 'Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g, Lipton Green Tea Lively Fresh , Candy Pack', 6565, 430.00, 'To be billed', '-', 0.00, 430.00, '2025-10-04 13:12:31', '2025-10-16 15:46:36'),
(409, 2, 'Michael Smith', 'Mini Bar', 'Cobra Energy Drink , Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g, Candy Pack', 7213, 240.00, 'Paid', 'GCash', 40.00, 200.00, '2025-10-04 13:12:50', '2025-10-04 13:12:50'),
(410, 5, 'Robert Brown', 'Gift Store', 'Fridge Magnet Set, Fragrant Candle', 5873, 530.00, 'To be billed', '-', 0.00, 530.00, '2025-10-04 13:13:43', '2025-10-16 15:46:36'),
(411, 5, 'Robert Brown', 'Mini Bar', 'Cobra Energy Drink ', 5532, 120.00, 'To be billed', '-', 0.00, 120.00, '2025-10-04 13:14:11', '2025-10-16 15:46:36'),
(412, 7, 'Charles Garcia', 'Gift Store', 'Fragrant Candle', 5959, 350.00, 'Paid', 'Card', 200.00, 150.00, '2025-10-04 13:14:32', '2025-10-04 13:14:32'),
(417, 10, 'Daniel Lopez', 'Lounge Bar', 'Cocktail - Martini', 2175, 320.00, 'To be billed', '-', 0.00, 320.00, '2025-10-04 13:17:51', '2025-10-16 15:46:36'),
(418, 20, 'Kevin Martin', 'Lounge Bar', 'Cocktail - Martini', 7853, 320.00, 'Paid', 'Cash', 200.00, 120.00, '2025-10-04 13:18:29', '2025-10-04 13:18:29'),
(419, 19, 'Brian Harris', 'Restaurant', 'Pancit Bihon, Pancit Canton, Chicken Sopas', 3754, 710.00, 'To be billed', 'Cash', 0.00, 710.00, '2025-10-04 13:19:00', '2025-10-04 13:19:00'),
(420, 7, 'Charles Garcia', 'Restaurant', 'Tinolang Manok', 6778, 200.00, 'To be billed', 'Cash', 0.00, 200.00, '2025-10-04 13:20:01', '2025-10-04 13:20:01'),
(421, 2, 'Michael Smith', 'Restaurant', 'Sinigang na Baboy, Fresh Juice', 1066, 420.00, 'To be billed', 'Cash', 0.00, 420.00, '2025-10-04 15:24:34', '2025-10-04 15:24:34'),
(422, 5, 'Robert Brown', 'Restaurant', 'Sinigang na Baboy, Fresh Juice, Fruit Salad', 4034, 380.00, 'Paid', 'Cash', 55.00, 325.00, '2025-10-04 15:25:39', '2025-10-04 15:25:39'),
(423, 5, 'Robert Brown', 'Gift Store', 'Handmade Bracelet, Fridge Magnet Set', 4148, 480.00, 'To be billed', '-', 0.00, 480.00, '2025-10-04 16:05:04', '2025-10-16 15:46:36'),
(428, 5, 'Robert Brown', 'Gift Store', 'Handmade Bracelet, Fridge Magnet Set', 3229, 480.00, 'To be billed', '-', 0.00, 480.00, '2025-10-04 16:13:12', '2025-10-16 15:46:36'),
(429, 10, 'Daniel Lopez', 'Mini Bar', 'Cobra Energy Drink , Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g', 5978, 190.00, 'To be billed', '-', 0.00, 190.00, '2025-10-04 16:13:57', '2025-10-16 15:46:36'),
(430, 12, 'Anthony Wilson', 'Mini Bar', 'Cobra Energy Drink , Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g', 9791, 190.00, 'Paid', 'Card', 90.00, 100.00, '2025-10-04 16:14:25', '2025-10-04 16:14:25'),
(434, 16, 'George Moore', 'Gift Store', 'Decorative Coasters, Handmade Bracelet', 6142, 450.00, 'To be billed', '-', 0.00, 450.00, '2025-10-04 16:26:32', '2025-10-16 15:46:36'),
(436, 20, 'Kevin Martin', 'Gift Store', 'Fridge Magnet Set, Fragrant Candle, Handmade Bracelet, Mini Photo Frame, Local Snack Pack', 5991, 1350.00, 'To be billed', '-', 0.00, 1350.00, '2025-10-04 16:26:44', '2025-10-16 15:46:36'),
(439, NULL, NULL, 'Gift Store', '', 1333, 0.00, 'To be billed', '-', 0.00, 0.00, '2025-10-04 16:31:24', '2025-10-16 15:46:36'),
(440, 2, 'Michael Smith', 'Gift Store', 'Handmade Bracelet, Fridge Magnet Set', 4985, 480.00, 'To be billed', '-', 0.00, 480.00, '2025-10-04 16:31:34', '2025-10-16 15:46:36'),
(441, 2, 'Michael Smith', 'Gift Store', 'Handmade Bracelet, Fridge Magnet Set', 5006, 480.00, 'To be billed', '-', 0.00, 480.00, '2025-10-04 16:36:28', '2025-10-16 15:46:36'),
(444, 4, 'James Williams', 'Gift Store', 'Handmade Bracelet, Ferrero Rocher Chocolate Box, Decorative Coasters', 5249, 700.00, 'To be billed', '-', 0.00, 700.00, '2025-10-04 16:37:42', '2025-10-16 15:46:36'),
(445, 7, 'Charles Garcia', 'Gift Store', 'Ferrero Rocher Chocolate Box', 2027, 250.00, 'Paid', 'Cash', 50.00, 200.00, '2025-10-04 16:38:58', '2025-10-04 16:38:58'),
(446, 12, 'Anthony Wilson', 'Mini Bar', 'Arla Natural Cheese Mozzarella Cheese Slices', 1775, 600.00, 'To be billed', '-', 0.00, 600.00, '2025-10-04 16:41:32', '2025-10-16 15:46:36'),
(447, 12, 'Anthony Wilson', 'Lounge Bar', 'Cocktail - Daiquiri', 6612, 280.00, 'To be billed', '-', 0.00, 280.00, '2025-10-04 16:43:52', '2025-10-16 15:46:36'),
(448, 4, 'James Williams', 'Restaurant', 'Fresh Juice, Fruit Salad', 3115, 140.00, 'To be billed', 'Cash', 0.00, 140.00, '2025-10-04 17:01:26', '2025-10-04 17:01:26'),
(449, 4, 'James Williams', 'Restaurant', 'Tinolang Manok', 2023, 200.00, 'To be billed', 'Cash', 0.00, 200.00, '2025-10-04 17:02:03', '2025-10-04 17:02:03'),
(450, 2, 'Michael Smith', 'Restaurant', 'Tinolang Manok', 8242, 200.00, 'To be billed', 'Cash', 0.00, 200.00, '2025-10-04 17:06:03', '2025-10-04 17:06:03'),
(451, 9, 'Christopher Hernandez', 'Lounge Bar', 'Bacardi Gold Rum', 3185, 380.00, 'To be billed', '-', 0.00, 380.00, '2025-10-04 17:12:52', '2025-10-16 15:46:36'),
(452, 14, 'Paul Thomas', 'Lounge Bar', 'Baileys Irish Cream, Chivas Regal 12 Years', 6556, 6600.00, 'To be billed', '-', 0.00, 6600.00, '2025-10-04 19:47:03', '2025-10-16 15:46:36'),
(454, 5, 'Robert Brown', 'Lounge Bar', 'Cocktail - Mojito, Cocktail - Martini, Cocktail - Pina Colada', 4970, 870.00, 'Paid', 'Cash', 50.00, 820.00, '2025-10-04 19:49:43', '2025-10-04 19:49:43'),
(455, 10, 'Daniel Lopez', 'Restaurant', 'Iced Tea', 5159, 100.00, 'To be billed', 'Cash', 0.00, 100.00, '2025-10-04 19:51:18', '2025-10-04 19:51:18'),
(456, 2, 'Michael Smith', 'Restaurant', 'Coffee', 6253, 70.00, 'To be billed', 'Cash', 0.00, 70.00, '2025-10-04 20:18:53', '2025-10-04 20:18:53'),
(457, 5, 'Robert Brown', 'Restaurant', 'Coffee', 3507, 70.00, 'To be billed', 'Paymaya', 0.00, 70.00, '2025-10-04 20:22:54', '2025-10-04 20:22:54'),
(458, 6, 'William Jones', 'Restaurant', 'Iced Tea', 3289, 50.00, 'To be billed', 'Cash', 0.00, 50.00, '2025-10-04 20:29:28', '2025-10-04 20:29:28'),
(459, 6, 'William Jones', 'Restaurant', 'Fresh Juice', 4727, 60.00, 'To be billed', 'Cash', 0.00, 60.00, '2025-10-04 20:31:26', '2025-10-04 20:31:26'),
(460, 7, 'Charles Garcia', 'Restaurant', 'Coffee', 3054, 70.00, 'To be billed', 'Cash', 0.00, 70.00, '2025-10-04 20:34:33', '2025-10-04 20:34:33'),
(461, 5, 'Robert Brown', 'Restaurant', 'Chocolate Cake', 7722, 150.00, 'To be billed', 'Cash', 0.00, 150.00, '2025-10-04 20:35:51', '2025-10-04 20:35:51'),
(462, 2, 'Michael Smith', 'Restaurant', 'Pancit Malabon, Chicken Sopas', 2820, 400.00, 'Paid', 'GCash', 200.00, 200.00, '2025-10-04 20:42:53', '2025-10-04 20:42:53'),
(463, 6, 'William Jones', 'Gift Store', 'Local Snack Box, La Vista Chocolate Gift Box', 7039, 1850.00, 'To be billed', '-', 0.00, 1850.00, '2025-10-04 20:44:24', '2025-10-16 15:46:36'),
(465, 5, 'Robert Brown', 'Mini Bar', 'Beef Jerky, Growers Mixed Nuts', 5891, 290.00, 'To be billed', '-', 0.00, 290.00, '2025-10-04 20:49:37', '2025-10-16 15:46:36'),
(466, 5, 'Robert Brown', 'Lounge Bar', 'Chivas Regal 12 Years, Baileys Irish Cream', 7774, 4200.00, 'Paid', 'GCash', 900.00, 3300.00, '2025-10-04 20:50:27', '2025-10-04 20:50:27');

-- --------------------------------------------------------

--
-- Table structure for table `guest_billing_backup`
--

CREATE TABLE `guest_billing_backup` (
  `id` int(11) NOT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `guest_name` varchar(255) DEFAULT NULL,
  `order_type` enum('Restaurant','Mini Bar','Lounge Bar','Gift Store','Room Service') DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `order_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_option` enum('Paid','To be billed') NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `partial_payment` decimal(10,2) DEFAULT 0.00,
  `remaining_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guest_billing_backup`
--

INSERT INTO `guest_billing_backup` (`id`, `guest_id`, `guest_name`, `order_type`, `item_name`, `order_id`, `total_amount`, `payment_option`, `payment_method`, `partial_payment`, `remaining_amount`, `created_at`, `updated_at`) VALUES
(304, 7, 'Charles Garcia', 'Room Service', 'Chicken Sopas', 4921, 180.00, 'To be billed', 'Card', 0.00, 180.00, '2025-10-03 22:22:47', '2025-10-03 22:22:47'),
(305, 7, 'Charles Garcia', 'Room Service', 'Bulalo', 1973, 320.00, 'To be billed', 'Card', 0.00, 320.00, '2025-10-03 22:23:11', '2025-10-03 22:23:11'),
(306, 5, 'Robert Brown', 'Room Service', 'Pancit Canton', 5991, 180.00, 'To be billed', 'Cash', 0.00, 180.00, '2025-10-03 22:26:06', '2025-10-03 22:26:06'),
(307, 7, 'Charles Garcia', 'Room Service', 'Chicken Sopas, Pancit Canton', 3844, 360.00, 'To be billed', 'Cash', 0.00, 360.00, '2025-10-03 22:44:31', '2025-10-03 22:44:31'),
(309, 5, 'Robert Brown', 'Room Service', 'Pancit Canton', 6320, 180.00, 'To be billed', 'Cash', 0.00, 180.00, '2025-10-03 22:46:50', '2025-10-03 22:46:50'),
(311, 6, 'William Jones', 'Room Service', 'Bulalo, Chicken Sopas', 7832, 500.00, 'To be billed', 'Cash', 0.00, 500.00, '2025-10-03 22:58:05', '2025-10-03 22:58:05'),
(313, 2, 'Michael Smith', 'Restaurant', 'Pancit Canton, Chicken Sopas', 9066, 360.00, 'Paid', 'GCash', 60.00, 300.00, '2025-10-03 22:59:58', '2025-10-03 22:59:58'),
(314, 3, 'David Johnson', 'Restaurant', 'Chicken Sopas', 6495, 180.00, 'To be billed', 'Cash', 0.00, 180.00, '2025-10-03 23:00:22', '2025-10-03 23:00:22'),
(315, 15, 'Steven Taylor', 'Room Service', 'Chicken Sopas, Pancit Canton', 6848, 360.00, 'Paid', 'BillEase', 60.00, 300.00, '2025-10-03 23:02:08', '2025-10-03 23:02:08'),
(316, 3, 'David Johnson', 'Restaurant', 'Pancit Canton, Pork Adobo', 2546, 400.00, 'To be billed', 'Cash', 0.00, 400.00, '2025-10-03 23:26:31', '2025-10-03 23:26:31'),
(317, 3, 'David Johnson', 'Gift Store', 'Handmade Bracelet, Fridge Magnet Set', 5591, 480.00, 'To be billed', 'Cash', 0.00, 480.00, '2025-10-03 23:31:17', '2025-10-03 23:31:17'),
(319, 2, 'Michael Smith', 'Gift Store', 'Fridge Magnet Set, Handmade Bracelet', 1081, 480.00, 'Paid', 'Cash', 22.00, 458.00, '2025-10-03 23:33:09', '2025-10-03 23:33:09'),
(324, 2, 'Michael Smith', 'Gift Store', 'Fridge Magnet Set, Handmade Bracelet', 4035, 480.00, 'Paid', 'Cash', 80.00, 400.00, '2025-10-03 23:38:03', '2025-10-03 23:38:03'),
(328, 4, 'James Williams', 'Gift Store', 'Handmade Bracelet, Fridge Magnet Set, Fragrant Candle', 6098, 830.00, 'To be billed', NULL, 0.00, 830.00, '2025-10-03 23:50:03', '2025-10-03 23:50:03'),
(329, 4, 'James Williams', 'Gift Store', 'Ferrero Rocher Chocolate Box, Decorative Coasters', 4267, 400.00, 'Paid', 'GCash', 400.00, 0.00, '2025-10-03 23:50:18', '2025-10-03 23:50:18'),
(331, 7, 'Charles Garcia', 'Gift Store', 'Decorative Coasters, Coffee Mug, Ferrero Rocher Chocolate Box, Mini Photo Frame, Local Snack Pack', 7458, 1170.00, 'Paid', 'GCash', 170.00, 1000.00, '2025-10-03 23:51:01', '2025-10-03 23:51:01'),
(332, 4, 'James Williams', 'Restaurant', 'Pancit Canton, Pancit Bihon', 1359, 350.00, 'Paid', 'Cash', 50.00, 300.00, '2025-10-04 00:16:39', '2025-10-04 00:16:39'),
(333, 4, 'James Williams', 'Room Service', 'Ferrero Rocher Chocolate Box, Local Snack Box, Pancit Canton, Fragrant Candle, Fridge Magnet Set', 6731, 1310.00, 'To be billed', 'Cash', 0.00, 1310.00, '2025-10-04 00:23:25', '2025-10-04 00:23:25'),
(334, 5, 'Robert Brown', 'Room Service', 'Fridge Magnet Set, Handmade Bracelet, Miniature Figurine', 7076, 880.00, 'Paid', 'BillEase', 50.00, 830.00, '2025-10-04 00:28:29', '2025-10-04 00:28:29'),
(335, 5, 'Robert Brown', 'Gift Store', 'Miniature Figurine, Fridge Magnet Set', 4185, 580.00, 'To be billed', NULL, 0.00, 580.00, '2025-10-04 00:28:58', '2025-10-04 00:28:58'),
(336, 6, 'William Jones', 'Gift Store', 'Fragrant Candle, Fridge Magnet Set, Mini Photo Frame', 8988, 750.00, 'To be billed', NULL, 0.00, 750.00, '2025-10-04 00:29:44', '2025-10-04 00:29:44'),
(337, 9, 'Christopher Hernandez', 'Gift Store', 'Decorative Coasters, Ferrero Rocher Chocolate Box', 5604, 400.00, 'To be billed', NULL, 0.00, 400.00, '2025-10-04 00:30:33', '2025-10-04 00:30:33'),
(338, 6, 'William Jones', 'Gift Store', 'Fridge Magnet Set, Handmade Bracelet, Fragrant Candle', 9032, 830.00, 'To be billed', NULL, 0.00, 830.00, '2025-10-04 00:30:52', '2025-10-04 00:30:52'),
(339, 9, 'Christopher Hernandez', 'Gift Store', 'Fridge Magnet Set, Fragrant Candle, Ferrero Rocher Chocolate Box', 6633, 780.00, 'Paid', 'GCash', 80.00, 700.00, '2025-10-04 00:32:44', '2025-10-04 00:32:44'),
(340, 5, 'Robert Brown', 'Gift Store', 'Fridge Magnet Set, Fragrant Candle, Handmade Bracelet', 7929, 830.00, 'To be billed', NULL, 0.00, 830.00, '2025-10-04 00:38:22', '2025-10-04 00:38:22'),
(341, 2, 'Michael Smith', 'Mini Bar', 'Cobra Energy Drink , Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g, Lemonade Bottle', 1847, 270.00, 'To be billed', NULL, 0.00, 270.00, '2025-10-04 00:46:03', '2025-10-04 00:46:03'),
(345, 3, 'David Johnson', 'Mini Bar', 'Cobra Energy Drink , Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g', 5520, 190.00, 'To be billed', NULL, 0.00, 190.00, '2025-10-04 01:03:03', '2025-10-04 01:03:03'),
(346, 7, 'Charles Garcia', 'Mini Bar', 'Beef Jerky, Arla Natural Cheese Mozzarella Cheese Slices, Growers Mixed Nuts, Ding Dong Snack Mix, Lays', 3550, 820.00, 'Paid', 'GCash', 100.00, 720.00, '2025-10-04 01:03:24', '2025-10-04 01:03:24'),
(347, 8, 'Thomas Martinez', 'Mini Bar', 'Cobra Energy Drink , Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g, Candy Pack', 2495, 240.00, 'To be billed', NULL, 0.00, 240.00, '2025-10-04 01:05:09', '2025-10-04 01:05:09'),
(348, 2, 'Michael Smith', 'Mini Bar', 'Cobra Energy Drink , Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g, Candy Pack', 1170, 240.00, 'Paid', 'GCash', 42.00, 198.00, '2025-10-04 01:06:09', '2025-10-04 01:06:09'),
(355, 5, 'Robert Brown', 'Mini Bar', 'Cobra Energy Drink , Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g', 6669, 190.00, 'Paid', 'Card', 92.00, 98.00, '2025-10-04 01:25:39', '2025-10-04 01:25:39'),
(362, 5, 'Robert Brown', 'Lounge Bar', 'Cocktail - Mojito, Cocktail - Martini, Cocktail - Margarita', 8212, 870.00, 'To be billed', NULL, 0.00, 870.00, '2025-10-04 01:29:52', '2025-10-04 01:29:52'),
(363, 7, 'Charles Garcia', 'Lounge Bar', 'Cocktail - Margarita, Jack Daniel\'s, Ginebra San Miguel Gin, Fruit Platter', 3739, 1260.00, 'Paid', 'GCash', 260.00, 1000.00, '2025-10-04 01:30:07', '2025-10-04 01:30:07'),
(368, 5, 'Robert Brown', 'Restaurant', 'Pancit Bihon, Pancit Canton, Chicken Sopas, Bulalo', 1034, 850.00, 'To be billed', 'Cash', 0.00, 850.00, '2025-10-04 01:38:04', '2025-10-04 01:38:04'),
(369, 20, 'Kevin Martin', 'Restaurant', 'Pancit Canton, Chicken Sopas', 7296, 360.00, 'Paid', 'Paymaya', 200.00, 160.00, '2025-10-04 02:09:29', '2025-10-04 02:09:29'),
(370, 1, 'John Doe', 'Lounge Bar', 'Cocktail - Mojito, Cocktail - Martini, Cocktail - Margarita', 6221, 870.00, 'Paid', 'Card', 100.00, 770.00, '2025-10-04 02:11:12', '2025-10-04 02:12:28'),
(372, 11, 'Matthew Gonzalez', 'Gift Store', 'Fridge Magnet Set, Fragrant Candle, Handmade Bracelet', 1689, 830.00, 'To be billed', NULL, 0.00, 830.00, '2025-10-04 02:12:09', '2025-10-04 02:12:09'),
(373, 2, 'Michael Smith', 'Restaurant', 'Chicken Sopas, Bulalo', 1744, 500.00, 'Paid', 'Cash', 111.00, 389.00, '2025-10-04 02:18:45', '2025-10-04 02:18:45'),
(374, 1, 'John Doe', 'Restaurant', 'Chicken Sopas', 3450, 180.00, 'To be billed', 'Cash', 0.00, 180.00, '2025-10-04 02:20:02', '2025-10-04 02:20:02'),
(375, 6, 'William Jones', 'Restaurant', 'Pancit Bihon, Pancit Canton, Chicken Sopas', 8568, 530.00, 'Paid', 'GCash', 66.00, 464.00, '2025-10-04 02:21:59', '2025-10-04 02:21:59'),
(376, 3, 'David Johnson', 'Restaurant', 'Pancit Bihon, Pancit Canton', 9457, 350.00, 'To be billed', 'Cash', 0.00, 350.00, '2025-10-04 02:22:28', '2025-10-04 02:22:28'),
(377, 5, 'Robert Brown', 'Restaurant', 'Pancit Bihon, Pancit Canton', 7562, 350.00, 'To be billed', 'Cash', 0.00, 350.00, '2025-10-04 02:45:54', '2025-10-04 02:45:54'),
(378, 3, 'David Johnson', 'Restaurant', 'Lechon Kawali', 3604, 260.00, 'To be billed', 'Cash', 0.00, 260.00, '2025-10-04 03:05:09', '2025-10-04 03:05:09'),
(379, 1, 'John Doe', 'Restaurant', 'Sinigang na Baboy', 5556, 240.00, 'To be billed', 'Cash', 0.00, 240.00, '2025-10-04 03:11:52', '2025-10-04 03:11:52'),
(382, 6, 'William Jones', 'Restaurant', 'Pancit Canton', 3690, 360.00, 'Paid', 'GCash', 60.00, 300.00, '2025-10-04 12:39:32', '2025-10-04 12:39:32'),
(390, 4, 'James Williams', 'Gift Store', 'Fridge Magnet Set, Fragrant Candle, Ferrero Rocher Chocolate Box', 4922, 780.00, 'To be billed', NULL, 0.00, 780.00, '2025-10-04 12:49:02', '2025-10-04 12:49:02'),
(396, 2, 'Michael Smith', 'Gift Store', 'Miniature Figurine, Handmade Bracelet, Fridge Magnet Set', 7746, 880.00, 'Paid', 'Cash', 500.00, 380.00, '2025-10-04 12:58:16', '2025-10-04 12:58:16'),
(397, 6, 'William Jones', 'Gift Store', 'Handmade Bracelet, Fridge Magnet Set, Fragrant Candle', 8252, 830.00, 'To be billed', NULL, 0.00, 830.00, '2025-10-04 12:58:30', '2025-10-04 12:58:30'),
(398, 7, 'Charles Garcia', 'Gift Store', 'Fridge Magnet Set', 4918, 180.00, 'Paid', 'Cash', 80.00, 100.00, '2025-10-04 13:01:35', '2025-10-04 13:01:35'),
(407, 2, 'Michael Smith', 'Gift Store', 'Handmade Bracelet, Miniature Figurine', 5136, 700.00, 'To be billed', NULL, 0.00, 700.00, '2025-10-04 13:11:00', '2025-10-04 13:11:00'),
(408, 6, 'William Jones', 'Mini Bar', 'Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g, Lipton Green Tea Lively Fresh , Candy Pack', 6565, 430.00, 'To be billed', NULL, 0.00, 430.00, '2025-10-04 13:12:31', '2025-10-04 13:12:31'),
(409, 2, 'Michael Smith', 'Mini Bar', 'Cobra Energy Drink , Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g, Candy Pack', 7213, 240.00, 'Paid', 'GCash', 40.00, 200.00, '2025-10-04 13:12:50', '2025-10-04 13:12:50'),
(410, 5, 'Robert Brown', 'Gift Store', 'Fridge Magnet Set, Fragrant Candle', 5873, 530.00, 'To be billed', NULL, 0.00, 530.00, '2025-10-04 13:13:43', '2025-10-04 13:13:43'),
(411, 5, 'Robert Brown', 'Mini Bar', 'Cobra Energy Drink ', 5532, 120.00, 'To be billed', NULL, 0.00, 120.00, '2025-10-04 13:14:11', '2025-10-04 13:14:11'),
(412, 7, 'Charles Garcia', 'Gift Store', 'Fragrant Candle', 5959, 350.00, 'Paid', 'Card', 200.00, 150.00, '2025-10-04 13:14:32', '2025-10-04 13:14:32'),
(417, 10, 'Daniel Lopez', 'Lounge Bar', 'Cocktail - Martini', 2175, 320.00, 'To be billed', NULL, 0.00, 320.00, '2025-10-04 13:17:51', '2025-10-04 13:17:51'),
(418, 20, 'Kevin Martin', 'Lounge Bar', 'Cocktail - Martini', 7853, 320.00, 'Paid', 'Cash', 200.00, 120.00, '2025-10-04 13:18:29', '2025-10-04 13:18:29'),
(419, 19, 'Brian Harris', 'Restaurant', 'Pancit Bihon, Pancit Canton, Chicken Sopas', 3754, 710.00, 'To be billed', 'Cash', 0.00, 710.00, '2025-10-04 13:19:00', '2025-10-04 13:19:00'),
(420, 7, 'Charles Garcia', 'Restaurant', 'Tinolang Manok', 6778, 200.00, 'To be billed', 'Cash', 0.00, 200.00, '2025-10-04 13:20:01', '2025-10-04 13:20:01'),
(421, 2, 'Michael Smith', 'Restaurant', 'Sinigang na Baboy, Fresh Juice', 1066, 420.00, 'To be billed', 'Cash', 0.00, 420.00, '2025-10-04 15:24:34', '2025-10-04 15:24:34'),
(422, 5, 'Robert Brown', 'Restaurant', 'Sinigang na Baboy, Fresh Juice, Fruit Salad', 4034, 380.00, 'Paid', 'Cash', 55.00, 325.00, '2025-10-04 15:25:39', '2025-10-04 15:25:39'),
(423, 5, 'Robert Brown', 'Gift Store', 'Handmade Bracelet, Fridge Magnet Set', 4148, 480.00, 'To be billed', NULL, 0.00, 480.00, '2025-10-04 16:05:04', '2025-10-04 16:05:04'),
(428, 5, 'Robert Brown', 'Gift Store', 'Handmade Bracelet, Fridge Magnet Set', 3229, 480.00, 'To be billed', NULL, 0.00, 480.00, '2025-10-04 16:13:12', '2025-10-04 16:13:12'),
(429, 10, 'Daniel Lopez', 'Mini Bar', 'Cobra Energy Drink , Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g', 5978, 190.00, 'To be billed', NULL, 0.00, 190.00, '2025-10-04 16:13:57', '2025-10-04 16:13:57'),
(430, 12, 'Anthony Wilson', 'Mini Bar', 'Cobra Energy Drink , Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g', 9791, 190.00, 'Paid', 'Card', 90.00, 100.00, '2025-10-04 16:14:25', '2025-10-04 16:14:25'),
(434, 16, 'George Moore', 'Gift Store', 'Decorative Coasters, Handmade Bracelet', 6142, 450.00, 'To be billed', NULL, 0.00, 450.00, '2025-10-04 16:26:32', '2025-10-04 16:26:32'),
(435, 0, 'Walk-in Guest', 'Gift Store', 'No Items', 3328, 0.00, 'To be billed', NULL, 0.00, 0.00, '2025-10-04 16:26:32', '2025-10-04 16:26:32'),
(436, 20, 'Kevin Martin', 'Gift Store', 'Fridge Magnet Set, Fragrant Candle, Handmade Bracelet, Mini Photo Frame, Local Snack Pack', 5991, 1350.00, 'To be billed', NULL, 0.00, 1350.00, '2025-10-04 16:26:44', '2025-10-04 16:26:44'),
(437, 0, 'Walk-in Guest', 'Gift Store', 'No Items', 8394, 0.00, 'To be billed', NULL, 0.00, 0.00, '2025-10-04 16:26:44', '2025-10-04 16:26:44'),
(438, 0, 'Walk-in Guest', 'Gift Store', 'No Items', 1361, 0.00, 'To be billed', NULL, 0.00, 0.00, '2025-10-04 16:28:11', '2025-10-04 16:28:11'),
(439, NULL, NULL, 'Gift Store', '', 1333, 0.00, 'To be billed', NULL, 0.00, 0.00, '2025-10-04 16:31:24', '2025-10-04 16:31:24'),
(440, 2, 'Michael Smith', 'Gift Store', 'Handmade Bracelet, Fridge Magnet Set', 4985, 480.00, 'To be billed', NULL, 0.00, 480.00, '2025-10-04 16:31:34', '2025-10-04 16:31:34'),
(441, 2, 'Michael Smith', 'Gift Store', 'Handmade Bracelet, Fridge Magnet Set', 5006, 480.00, 'To be billed', NULL, 0.00, 480.00, '2025-10-04 16:36:28', '2025-10-04 16:36:28'),
(444, 4, 'James Williams', 'Gift Store', 'Handmade Bracelet, Ferrero Rocher Chocolate Box, Decorative Coasters', 5249, 700.00, 'To be billed', NULL, 0.00, 700.00, '2025-10-04 16:37:42', '2025-10-04 16:37:42'),
(445, 7, 'Charles Garcia', 'Gift Store', 'Ferrero Rocher Chocolate Box', 2027, 250.00, 'Paid', 'Cash', 50.00, 200.00, '2025-10-04 16:38:58', '2025-10-04 16:38:58'),
(446, 12, 'Anthony Wilson', 'Mini Bar', 'Arla Natural Cheese Mozzarella Cheese Slices', 1775, 600.00, 'To be billed', NULL, 0.00, 600.00, '2025-10-04 16:41:32', '2025-10-04 16:41:32'),
(447, 12, 'Anthony Wilson', 'Lounge Bar', 'Cocktail - Daiquiri', 6612, 280.00, 'To be billed', NULL, 0.00, 280.00, '2025-10-04 16:43:52', '2025-10-04 16:43:52'),
(448, 4, 'James Williams', 'Restaurant', 'Fresh Juice, Fruit Salad', 3115, 140.00, 'To be billed', 'Cash', 0.00, 140.00, '2025-10-04 17:01:26', '2025-10-04 17:01:26'),
(449, 4, 'James Williams', 'Restaurant', 'Tinolang Manok', 2023, 200.00, 'To be billed', 'Cash', 0.00, 200.00, '2025-10-04 17:02:03', '2025-10-04 17:02:03'),
(450, 2, 'Michael Smith', 'Restaurant', 'Tinolang Manok', 8242, 200.00, 'To be billed', 'Cash', 0.00, 200.00, '2025-10-04 17:06:03', '2025-10-04 17:06:03'),
(451, 9, 'Christopher Hernandez', 'Lounge Bar', 'Bacardi Gold Rum', 3185, 380.00, 'To be billed', NULL, 0.00, 380.00, '2025-10-04 17:12:52', '2025-10-04 17:12:52'),
(452, 14, 'Paul Thomas', 'Lounge Bar', 'Baileys Irish Cream, Chivas Regal 12 Years', 6556, 6600.00, 'To be billed', NULL, 0.00, 6600.00, '2025-10-04 19:47:03', '2025-10-04 19:47:03'),
(454, 5, 'Robert Brown', 'Lounge Bar', 'Cocktail - Mojito, Cocktail - Martini, Cocktail - Pina Colada', 4970, 870.00, 'Paid', 'Cash', 50.00, 820.00, '2025-10-04 19:49:43', '2025-10-04 19:49:43'),
(455, 10, 'Daniel Lopez', 'Restaurant', 'Iced Tea', 5159, 100.00, 'To be billed', 'Cash', 0.00, 100.00, '2025-10-04 19:51:18', '2025-10-04 19:51:18'),
(456, 2, 'Michael Smith', 'Restaurant', 'Coffee', 6253, 70.00, 'To be billed', 'Cash', 0.00, 70.00, '2025-10-04 20:18:53', '2025-10-04 20:18:53'),
(457, 5, 'Robert Brown', 'Restaurant', 'Coffee', 3507, 70.00, 'To be billed', 'Paymaya', 0.00, 70.00, '2025-10-04 20:22:54', '2025-10-04 20:22:54'),
(458, 6, 'William Jones', 'Restaurant', 'Iced Tea', 3289, 50.00, 'To be billed', 'Cash', 0.00, 50.00, '2025-10-04 20:29:28', '2025-10-04 20:29:28'),
(459, 6, 'William Jones', 'Restaurant', 'Fresh Juice', 4727, 60.00, 'To be billed', 'Cash', 0.00, 60.00, '2025-10-04 20:31:26', '2025-10-04 20:31:26'),
(460, 7, 'Charles Garcia', 'Restaurant', 'Coffee', 3054, 70.00, 'To be billed', 'Cash', 0.00, 70.00, '2025-10-04 20:34:33', '2025-10-04 20:34:33'),
(461, 5, 'Robert Brown', 'Restaurant', 'Chocolate Cake', 7722, 150.00, 'To be billed', 'Cash', 0.00, 150.00, '2025-10-04 20:35:51', '2025-10-04 20:35:51'),
(462, 2, 'Michael Smith', 'Restaurant', 'Pancit Malabon, Chicken Sopas', 2820, 400.00, 'Paid', 'GCash', 200.00, 200.00, '2025-10-04 20:42:53', '2025-10-04 20:42:53'),
(463, 6, 'William Jones', 'Gift Store', 'Local Snack Box, La Vista Chocolate Gift Box', 7039, 1850.00, 'To be billed', NULL, 0.00, 1850.00, '2025-10-04 20:44:24', '2025-10-04 20:44:24'),
(464, 0, '', 'Gift Store', 'Fragrant Candle, Fridge Magnet Set', 5436, 530.00, 'Paid', 'Paymaya', 2.00, 528.00, '2025-10-04 20:44:53', '2025-10-04 20:44:53'),
(465, 5, 'Robert Brown', 'Mini Bar', 'Beef Jerky, Growers Mixed Nuts', 5891, 290.00, 'To be billed', NULL, 0.00, 290.00, '2025-10-04 20:49:37', '2025-10-04 20:49:37'),
(466, 5, 'Robert Brown', 'Lounge Bar', 'Chivas Regal 12 Years, Baileys Irish Cream', 7774, 4200.00, 'Paid', 'GCash', 900.00, 3300.00, '2025-10-04 20:50:27', '2025-10-04 20:50:27');

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
(1, 'Bronze Program', 'bronze', 1.0, 'Earn 1 point per ₱1 spent. Access to basic hotel promotions.', 'Basic membership for new guests. Earn points for every purchase and start your journey to higher rewards.', 0, 'active', '2025-09-09 01:09:55', 0, 0, 0.00, 5.00),
(2, 'Silver Program', 'silver', 1.5, 'Earn 1.5 points per ₱1 spent. 10% dining discount.', 'Silver members enjoy better rewards and exclusive seasonal discounts.', 0, 'active', '2025-09-09 01:09:55', 0, 0, 0.00, 10.00),
(3, 'Gold Program', 'gold', 2.0, 'Earn 2 points per ₱1 spent. 15% off on all hotel services.', 'Gold members receive priority booking, free room upgrades (when available), and more exclusive offers.', 0, 'active', '2025-09-09 01:09:55', 0, 0, 0.00, 15.00),
(4, 'Platinum Program', 'platinum', 3.0, 'Earn 3 points per ₱1 spent. 20% discount on all amenities and complimentary VIP perks.', 'Platinum members enjoy luxury privileges, exclusive events, and the highest points rate for all purchases.', 0, 'active', '2025-09-09 01:09:55', 0, 0, 0.00, 20.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`guest_id`);

--
-- AUTO_INCREMENT for dumped tables
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `guest_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

  ALTER TABLE `campaigns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
  
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

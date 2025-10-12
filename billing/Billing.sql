-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2025 at 04:36 PM
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
-- Table structure for table `folio`
--

CREATE TABLE `folio` (
  `id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `guest_name` varchar(255) NOT NULL,
  `order_type` varchar(100) NOT NULL,
  `item` varchar(255) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `total_amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) DEFAULT 0.00,
  `remaining_amount` decimal(10,2) GENERATED ALWAYS AS (`total_amount` - `paid_amount`) STORED,
  `payment_method` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `folio`
--

INSERT INTO `folio` (`id`, `guest_id`, `guest_name`, `order_type`, `item`, `order_id`, `quantity`, `total_amount`, `paid_amount`, `payment_method`, `created_at`, `updated_at`) VALUES
(174, 2, 'Jane Smith', 'Lounge Bar', 'Cocktail - Daiquiri', 3415, 1, 280.00, 280.00, 'Cash', '2025-10-12 21:17:31', '2025-10-12 21:17:31'),
(175, 2, 'Jane Smith', 'Lounge Bar', 'Chivas Regal 12 Years', 3415, 1, 2400.00, 2400.00, 'Cash', '2025-10-12 21:17:31', '2025-10-12 21:17:31'),
(176, 2, 'Jane Smith', 'Lounge Bar', 'Baileys Irish Cream', 3415, 1, 1800.00, 1800.00, 'Cash', '2025-10-12 21:17:31', '2025-10-12 21:17:31'),
(177, 2, 'Jane Smith', 'Lounge Bar', 'Bacardi Gold Rum', 3415, 1, 380.00, 380.00, 'Cash', '2025-10-12 21:17:31', '2025-10-12 21:17:31'),
(178, 2, 'Jane Smith', 'Room Service', 'Pork Adobo', 2345, 1, 220.00, 220.00, 'Cash', '2025-10-12 21:17:31', '2025-10-12 21:17:31'),
(179, 2, 'Jane Smith', 'Room Service', 'Crispy Pata', 2345, 1, 480.00, 480.00, 'Cash', '2025-10-12 21:17:31', '2025-10-12 21:17:31'),
(180, 2, 'Jane Smith', 'Room Service', 'Cheesecake', 2345, 1, 180.00, 180.00, 'Cash', '2025-10-12 21:17:31', '2025-10-12 21:17:31'),
(181, 2, 'Jane Smith', 'Room Service', 'Tinolang Manok', 2345, 1, 200.00, 200.00, 'Cash', '2025-10-12 21:17:31', '2025-10-12 21:17:31'),
(182, 2, 'Jane Smith', 'Lounge Bar', 'Cocktail - Margarita', 9316, 1, 300.00, 300.00, 'Cash', '2025-10-12 21:17:31', '2025-10-12 21:17:31'),
(183, 2, 'Jane Smith', 'Lounge Bar', 'Cocktail - Daiquiri', 9316, 1, 280.00, 280.00, 'Cash', '2025-10-12 21:17:31', '2025-10-12 21:17:31'),
(184, 2, 'Jane Smith', 'Lounge Bar', 'Chivas Regal 12 Years', 9316, 1, 2400.00, 2400.00, 'Cash', '2025-10-12 21:17:31', '2025-10-12 21:17:31'),
(185, 2, 'Jane Smith', 'Lounge Bar', 'Baileys Irish Cream', 9316, 1, 1800.00, 1800.00, 'Cash', '2025-10-12 21:17:31', '2025-10-12 21:17:31'),
(186, 2, 'Jane Smith', 'Room', 'Executive', NULL, 1, 5200.00, 5200.00, 'Cash', '2025-10-12 21:17:31', '2025-10-12 21:17:31'),
(187, 2, 'Jane Smith', 'Room Service', 'Pork Adobo', 2345, 1, 220.00, 220.00, 'Cash', '2025-10-12 21:18:04', '2025-10-12 21:18:04'),
(188, 2, 'Jane Smith', 'Room Service', 'Crispy Pata', 2345, 1, 480.00, 480.00, 'Cash', '2025-10-12 21:18:04', '2025-10-12 21:18:04'),
(189, 2, 'Jane Smith', 'Room Service', 'Cheesecake', 2345, 1, 180.00, 180.00, 'Cash', '2025-10-12 21:18:04', '2025-10-12 21:18:04'),
(190, 2, 'Jane Smith', 'Room Service', 'Tinolang Manok', 2345, 1, 200.00, 200.00, 'Cash', '2025-10-12 21:18:04', '2025-10-12 21:18:04'),
(191, 2, 'Jane Smith', 'Lounge Bar', 'Cocktail - Margarita', 9316, 1, 300.00, 300.00, 'Cash', '2025-10-12 21:18:04', '2025-10-12 21:18:04'),
(192, 2, 'Jane Smith', 'Lounge Bar', 'Cocktail - Daiquiri', 9316, 1, 280.00, 280.00, 'Cash', '2025-10-12 21:18:04', '2025-10-12 21:18:04'),
(193, 2, 'Jane Smith', 'Lounge Bar', 'Chivas Regal 12 Years', 9316, 1, 2400.00, 2400.00, 'Cash', '2025-10-12 21:18:04', '2025-10-12 21:18:04'),
(194, 2, 'Jane Smith', 'Lounge Bar', 'Baileys Irish Cream', 9316, 1, 1800.00, 1800.00, 'Cash', '2025-10-12 21:18:04', '2025-10-12 21:18:04'),
(195, 2, 'Jane Smith', 'Room', 'Executive', NULL, 1, 5200.00, 5200.00, 'Cash', '2025-10-12 21:18:04', '2025-10-12 21:18:04'),
(196, 2, 'Jane Smith', 'Room Service', 'Pork Adobo', 2345, 1, 220.00, 220.00, 'Cash', '2025-10-12 21:18:23', '2025-10-12 21:18:23'),
(197, 2, 'Jane Smith', 'Room Service', 'Crispy Pata', 2345, 1, 480.00, 480.00, 'Cash', '2025-10-12 21:18:23', '2025-10-12 21:18:23'),
(198, 2, 'Jane Smith', 'Room Service', 'Cheesecake', 2345, 1, 180.00, 180.00, 'Cash', '2025-10-12 21:18:23', '2025-10-12 21:18:23'),
(199, 2, 'Jane Smith', 'Room Service', 'Tinolang Manok', 2345, 1, 200.00, 200.00, 'Cash', '2025-10-12 21:18:23', '2025-10-12 21:18:23'),
(200, 2, 'Jane Smith', 'Lounge Bar', 'Cocktail - Margarita', 9316, 1, 300.00, 300.00, 'Cash', '2025-10-12 21:18:23', '2025-10-12 21:18:23'),
(201, 2, 'Jane Smith', 'Lounge Bar', 'Cocktail - Daiquiri', 9316, 1, 280.00, 280.00, 'Cash', '2025-10-12 21:18:23', '2025-10-12 21:18:23'),
(202, 2, 'Jane Smith', 'Lounge Bar', 'Chivas Regal 12 Years', 9316, 1, 2400.00, 2400.00, 'Cash', '2025-10-12 21:18:23', '2025-10-12 21:18:23'),
(203, 2, 'Jane Smith', 'Lounge Bar', 'Baileys Irish Cream', 9316, 1, 1800.00, 1800.00, 'Cash', '2025-10-12 21:18:23', '2025-10-12 21:18:23'),
(204, 2, 'Jane Smith', 'Gift Store', 'Handmade Bracelet', 3304, 1, 300.00, 300.00, 'Cash', '2025-10-12 21:18:23', '2025-10-12 21:18:23'),
(205, 2, 'Jane Smith', 'Gift Store', 'Fridge Magnet Set', 3304, 1, 180.00, 180.00, 'Cash', '2025-10-12 21:18:23', '2025-10-12 21:18:23'),
(206, 2, 'Jane Smith', 'Gift Store', 'Fragrant Candle', 3304, 1, 350.00, 350.00, 'Cash', '2025-10-12 21:18:23', '2025-10-12 21:18:23'),
(207, 2, 'Jane Smith', 'Gift Store', 'Ferrero Rocher Chocolate Box', 3304, 1, 250.00, 250.00, 'Cash', '2025-10-12 21:18:23', '2025-10-12 21:18:23'),
(208, 2, 'Jane Smith', 'Room', 'Executive', NULL, 1, 5200.00, 5200.00, 'Cash', '2025-10-12 21:18:23', '2025-10-12 21:18:23'),
(209, 2, 'Jane Smith', 'Room Service', 'Pork Adobo', 2345, 1, 220.00, 220.00, 'Cash', '2025-10-12 21:18:44', '2025-10-12 21:18:44'),
(210, 2, 'Jane Smith', 'Room Service', 'Crispy Pata', 2345, 1, 480.00, 480.00, 'Cash', '2025-10-12 21:18:44', '2025-10-12 21:18:44'),
(211, 2, 'Jane Smith', 'Room Service', 'Cheesecake', 2345, 1, 180.00, 180.00, 'Cash', '2025-10-12 21:18:44', '2025-10-12 21:18:44'),
(212, 2, 'Jane Smith', 'Room Service', 'Tinolang Manok', 2345, 1, 200.00, 200.00, 'Cash', '2025-10-12 21:18:44', '2025-10-12 21:18:44'),
(213, 2, 'Jane Smith', 'Lounge Bar', 'Cocktail - Margarita', 9316, 1, 300.00, 300.00, 'Cash', '2025-10-12 21:18:44', '2025-10-12 21:18:44'),
(214, 2, 'Jane Smith', 'Lounge Bar', 'Cocktail - Daiquiri', 9316, 1, 280.00, 280.00, 'Cash', '2025-10-12 21:18:44', '2025-10-12 21:18:44'),
(215, 2, 'Jane Smith', 'Lounge Bar', 'Chivas Regal 12 Years', 9316, 1, 2400.00, 2400.00, 'Cash', '2025-10-12 21:18:44', '2025-10-12 21:18:44'),
(216, 2, 'Jane Smith', 'Lounge Bar', 'Baileys Irish Cream', 9316, 1, 1800.00, 1800.00, 'Cash', '2025-10-12 21:18:44', '2025-10-12 21:18:44'),
(217, 2, 'Jane Smith', 'Gift Store', 'Handmade Bracelet', 3304, 1, 300.00, 300.00, 'Cash', '2025-10-12 21:18:44', '2025-10-12 21:18:44'),
(218, 2, 'Jane Smith', 'Gift Store', 'Fridge Magnet Set', 3304, 1, 180.00, 180.00, 'Cash', '2025-10-12 21:18:44', '2025-10-12 21:18:44'),
(219, 2, 'Jane Smith', 'Gift Store', 'Fragrant Candle', 3304, 1, 350.00, 350.00, 'Cash', '2025-10-12 21:18:44', '2025-10-12 21:18:44'),
(220, 2, 'Jane Smith', 'Gift Store', 'Ferrero Rocher Chocolate Box', 3304, 1, 250.00, 250.00, 'Cash', '2025-10-12 21:18:44', '2025-10-12 21:18:44'),
(221, 2, 'Jane Smith', 'Mini Bar', 'Cobra Energy Drink ', 8019, 1, 120.00, 120.00, 'Cash', '2025-10-12 21:18:44', '2025-10-12 21:18:44'),
(222, 2, 'Jane Smith', 'Mini Bar', 'Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g', 8019, 1, 70.00, 70.00, 'Cash', '2025-10-12 21:18:44', '2025-10-12 21:18:44'),
(223, 2, 'Jane Smith', 'Mini Bar', 'Candy Pack', 8019, 1, 50.00, 50.00, 'Cash', '2025-10-12 21:18:44', '2025-10-12 21:18:44'),
(224, 2, 'Jane Smith', 'Mini Bar', 'Beef Jerky', 8019, 1, 200.00, 200.00, 'Cash', '2025-10-12 21:18:44', '2025-10-12 21:18:44'),
(225, 2, 'Jane Smith', 'Mini Bar', 'Jack \'N Jill Vcut Spicy Barbeque ', 8019, 1, 130.00, 130.00, 'Cash', '2025-10-12 21:18:44', '2025-10-12 21:18:44'),
(226, 2, 'Jane Smith', 'Room', 'Executive', NULL, 1, 5200.00, 5200.00, 'Cash', '2025-10-12 21:18:44', '2025-10-12 21:18:44'),
(227, 2, 'Jane Smith', 'Room Service', 'Pork Adobo', 2345, 1, 220.00, 220.00, 'Cash', '2025-10-12 21:20:59', '2025-10-12 21:20:59'),
(228, 2, 'Jane Smith', 'Room Service', 'Crispy Pata', 2345, 1, 480.00, 480.00, 'Cash', '2025-10-12 21:20:59', '2025-10-12 21:20:59'),
(229, 2, 'Jane Smith', 'Room Service', 'Cheesecake', 2345, 1, 180.00, 180.00, 'Cash', '2025-10-12 21:20:59', '2025-10-12 21:20:59'),
(230, 2, 'Jane Smith', 'Room Service', 'Tinolang Manok', 2345, 1, 200.00, 200.00, 'Cash', '2025-10-12 21:20:59', '2025-10-12 21:20:59'),
(231, 2, 'Jane Smith', 'Lounge Bar', 'Cocktail - Margarita', 9316, 1, 300.00, 300.00, 'Cash', '2025-10-12 21:20:59', '2025-10-12 21:20:59'),
(232, 2, 'Jane Smith', 'Lounge Bar', 'Cocktail - Daiquiri', 9316, 1, 280.00, 280.00, 'Cash', '2025-10-12 21:20:59', '2025-10-12 21:20:59'),
(233, 2, 'Jane Smith', 'Lounge Bar', 'Chivas Regal 12 Years', 9316, 1, 2400.00, 2400.00, 'Cash', '2025-10-12 21:20:59', '2025-10-12 21:20:59'),
(234, 2, 'Jane Smith', 'Lounge Bar', 'Baileys Irish Cream', 9316, 1, 1800.00, 1800.00, 'Cash', '2025-10-12 21:20:59', '2025-10-12 21:20:59'),
(235, 2, 'Jane Smith', 'Gift Store', 'Handmade Bracelet', 3304, 1, 300.00, 300.00, 'Cash', '2025-10-12 21:20:59', '2025-10-12 21:20:59'),
(236, 2, 'Jane Smith', 'Gift Store', 'Fridge Magnet Set', 3304, 1, 180.00, 180.00, 'Cash', '2025-10-12 21:20:59', '2025-10-12 21:20:59'),
(237, 2, 'Jane Smith', 'Gift Store', 'Fragrant Candle', 3304, 1, 350.00, 350.00, 'Cash', '2025-10-12 21:20:59', '2025-10-12 21:20:59'),
(238, 2, 'Jane Smith', 'Gift Store', 'Ferrero Rocher Chocolate Box', 3304, 1, 250.00, 250.00, 'Cash', '2025-10-12 21:20:59', '2025-10-12 21:20:59'),
(239, 2, 'Jane Smith', 'Mini Bar', 'Cobra Energy Drink ', 8019, 1, 120.00, 120.00, 'Cash', '2025-10-12 21:20:59', '2025-10-12 21:20:59'),
(240, 2, 'Jane Smith', 'Mini Bar', 'Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g', 8019, 1, 70.00, 70.00, 'Cash', '2025-10-12 21:20:59', '2025-10-12 21:20:59'),
(241, 2, 'Jane Smith', 'Mini Bar', 'Candy Pack', 8019, 1, 50.00, 50.00, 'Cash', '2025-10-12 21:20:59', '2025-10-12 21:20:59'),
(242, 2, 'Jane Smith', 'Mini Bar', 'Beef Jerky', 8019, 1, 200.00, 200.00, 'Cash', '2025-10-12 21:20:59', '2025-10-12 21:20:59'),
(243, 2, 'Jane Smith', 'Mini Bar', 'Jack \'N Jill Vcut Spicy Barbeque ', 8019, 1, 130.00, 130.00, 'Cash', '2025-10-12 21:20:59', '2025-10-12 21:20:59'),
(244, 2, 'Jane Smith', 'Room', 'Executive', NULL, 1, 5200.00, 5200.00, 'Cash', '2025-10-12 21:20:59', '2025-10-12 21:20:59'),
(245, 2, 'Jane Smith', 'Room Service', 'Pork Adobo', 2345, 1, 220.00, 220.00, 'Gcash', '2025-10-12 21:21:45', '2025-10-12 21:21:45'),
(246, 2, 'Jane Smith', 'Room Service', 'Crispy Pata', 2345, 1, 480.00, 480.00, 'Gcash', '2025-10-12 21:21:45', '2025-10-12 21:21:45'),
(247, 2, 'Jane Smith', 'Room Service', 'Cheesecake', 2345, 1, 180.00, 180.00, 'Gcash', '2025-10-12 21:21:45', '2025-10-12 21:21:45'),
(248, 2, 'Jane Smith', 'Room Service', 'Tinolang Manok', 2345, 1, 200.00, 200.00, 'Gcash', '2025-10-12 21:21:45', '2025-10-12 21:21:45'),
(249, 2, 'Jane Smith', 'Lounge Bar', 'Cocktail - Margarita', 9316, 1, 300.00, 300.00, 'Gcash', '2025-10-12 21:21:45', '2025-10-12 21:21:45'),
(250, 2, 'Jane Smith', 'Lounge Bar', 'Cocktail - Daiquiri', 9316, 1, 280.00, 280.00, 'Gcash', '2025-10-12 21:21:45', '2025-10-12 21:21:45'),
(251, 2, 'Jane Smith', 'Lounge Bar', 'Chivas Regal 12 Years', 9316, 1, 2400.00, 2400.00, 'Gcash', '2025-10-12 21:21:45', '2025-10-12 21:21:45'),
(252, 2, 'Jane Smith', 'Lounge Bar', 'Baileys Irish Cream', 9316, 1, 1800.00, 1800.00, 'Gcash', '2025-10-12 21:21:45', '2025-10-12 21:21:45'),
(253, 2, 'Jane Smith', 'Gift Store', 'Handmade Bracelet', 3304, 1, 300.00, 300.00, 'Gcash', '2025-10-12 21:21:45', '2025-10-12 21:21:45'),
(254, 2, 'Jane Smith', 'Gift Store', 'Fridge Magnet Set', 3304, 1, 180.00, 180.00, 'Gcash', '2025-10-12 21:21:45', '2025-10-12 21:21:45'),
(255, 2, 'Jane Smith', 'Gift Store', 'Fragrant Candle', 3304, 1, 350.00, 350.00, 'Gcash', '2025-10-12 21:21:45', '2025-10-12 21:21:45'),
(256, 2, 'Jane Smith', 'Gift Store', 'Ferrero Rocher Chocolate Box', 3304, 1, 250.00, 250.00, 'Gcash', '2025-10-12 21:21:45', '2025-10-12 21:21:45'),
(257, 2, 'Jane Smith', 'Mini Bar', 'Cobra Energy Drink ', 8019, 1, 120.00, 120.00, 'Gcash', '2025-10-12 21:21:45', '2025-10-12 21:21:45'),
(258, 2, 'Jane Smith', 'Mini Bar', 'Chips Ahoy! Chocolate Chip Cookies Snack Pack 38g', 8019, 1, 70.00, 70.00, 'Gcash', '2025-10-12 21:21:45', '2025-10-12 21:21:45'),
(259, 2, 'Jane Smith', 'Mini Bar', 'Candy Pack', 8019, 1, 50.00, 50.00, 'Gcash', '2025-10-12 21:21:45', '2025-10-12 21:21:45'),
(260, 2, 'Jane Smith', 'Mini Bar', 'Beef Jerky', 8019, 1, 200.00, 200.00, 'Gcash', '2025-10-12 21:21:45', '2025-10-12 21:21:45'),
(261, 2, 'Jane Smith', 'Mini Bar', 'Jack \'N Jill Vcut Spicy Barbeque ', 8019, 1, 130.00, 130.00, 'Gcash', '2025-10-12 21:21:45', '2025-10-12 21:21:45'),
(262, 2, 'Jane Smith', 'Room', 'Executive', NULL, 1, 5200.00, 5200.00, 'Gcash', '2025-10-12 21:21:45', '2025-10-12 21:21:45'),
(263, 3, 'Michael Johnson', 'Room', 'Suite', NULL, 1, 10000.00, 10000.00, 'Gcash', '2025-10-12 21:40:40', '2025-10-12 21:40:40'),
(264, 2, 'Jane Smith', 'Gift Store', 'Fridge Magnet Set', 8913, 1, 180.00, 180.00, 'Gcash', '2025-10-12 21:55:32', '2025-10-12 21:55:32'),
(265, 2, 'Jane Smith', 'Gift Store', 'Fragrant Candle', 8913, 1, 350.00, 350.00, 'Gcash', '2025-10-12 21:55:32', '2025-10-12 21:55:32'),
(266, 2, 'Jane Smith', 'Gift Store', 'Handmade Bracelet', 8913, 1, 300.00, 300.00, 'Gcash', '2025-10-12 21:55:32', '2025-10-12 21:55:32'),
(267, 2, 'Jane Smith', 'Gift Store', 'Limited Edition Hotel Calendar', 8913, 1, 250.00, 250.00, 'Gcash', '2025-10-12 21:55:32', '2025-10-12 21:55:32'),
(268, 2, 'Jane Smith', 'Restaurant', 'Kare-Kare', 8824, 1, 280.00, 280.00, 'Gcash', '2025-10-12 21:55:32', '2025-10-12 21:55:32'),
(269, 2, 'Jane Smith', 'Restaurant', 'Crispy Pata', 8824, 1, 480.00, 480.00, 'Gcash', '2025-10-12 21:55:32', '2025-10-12 21:55:32'),
(270, 2, 'Jane Smith', 'Restaurant', 'Pancit Bihon', 8824, 1, 170.00, 170.00, 'Gcash', '2025-10-12 21:55:32', '2025-10-12 21:55:32'),
(271, 2, 'Jane Smith', 'Gift Store', 'Fridge Magnet Set', 6937, 1, 180.00, 180.00, 'Gcash', '2025-10-12 21:55:32', '2025-10-12 21:55:32'),
(272, 2, 'Jane Smith', 'Gift Store', 'Fragrant Candle', 6937, 1, 350.00, 350.00, 'Gcash', '2025-10-12 21:55:32', '2025-10-12 21:55:32'),
(273, 2, 'Jane Smith', 'Gift Store', 'Ferrero Rocher Chocolate Box', 6937, 1, 250.00, 250.00, 'Gcash', '2025-10-12 21:55:32', '2025-10-12 21:55:32'),
(274, 2, 'Jane Smith', 'Gift Store', 'Decorative Coasters', 6937, 1, 150.00, 150.00, 'Gcash', '2025-10-12 21:55:32', '2025-10-12 21:55:32'),
(275, 2, 'Jane Smith', 'Lounge Bar', 'Cocktail - Margarita', 5510, 1, 300.00, 300.00, 'Gcash', '2025-10-12 21:55:32', '2025-10-12 21:55:32'),
(276, 2, 'Jane Smith', 'Lounge Bar', 'Cocktail - Daiquiri', 5510, 1, 280.00, 280.00, 'Gcash', '2025-10-12 21:55:32', '2025-10-12 21:55:32'),
(277, 2, 'Jane Smith', 'Lounge Bar', 'Chivas Regal 12 Years', 5510, 1, 2400.00, 2400.00, 'Gcash', '2025-10-12 21:55:32', '2025-10-12 21:55:32'),
(278, 2, 'Jane Smith', 'Lounge Bar', 'Baileys Irish Cream', 5510, 1, 1800.00, 1800.00, 'Gcash', '2025-10-12 21:55:32', '2025-10-12 21:55:32'),
(279, 2, 'Jane Smith', 'Room', 'Executive', NULL, 1, 5200.00, 5200.00, 'Gcash', '2025-10-12 21:55:32', '2025-10-12 21:55:32');

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
(297, 2, 'Jane Smith', 'Gift Store', 'Fridge Magnet Set', 8913, 180.00, 1, 'Paid', 'Gcash', 13.33, 166.67, 1000, '2025-10-12 21:53:35', '2025-10-12 21:55:32', 1080.00),
(298, 2, 'Jane Smith', 'Gift Store', 'Fragrant Candle', 8913, 350.00, 1, 'Paid', 'Gcash', 25.93, 324.07, 1000, '2025-10-12 21:53:35', '2025-10-12 21:55:32', 1080.00),
(299, 2, 'Jane Smith', 'Gift Store', 'Handmade Bracelet', 8913, 300.00, 1, 'Paid', 'Gcash', 22.22, 277.78, 1000, '2025-10-12 21:53:35', '2025-10-12 21:55:32', 1080.00),
(300, 2, 'Jane Smith', 'Gift Store', 'Limited Edition Hotel Calendar', 8913, 250.00, 1, 'Paid', 'Gcash', 18.52, 231.48, 1000, '2025-10-12 21:53:35', '2025-10-12 21:55:32', 1080.00),
(301, 2, 'Jane Smith', 'Restaurant', 'Kare-Kare', 8824, 280.00, 1, 'Paid', 'Gcash', 9.03, 270.97, 900, '2025-10-12 21:54:03', '2025-10-12 21:55:32', 0.00),
(302, 2, 'Jane Smith', 'Restaurant', 'Crispy Pata', 8824, 480.00, 1, 'Paid', 'Gcash', 15.48, 464.52, 900, '2025-10-12 21:54:03', '2025-10-12 21:55:32', 0.00),
(303, 2, 'Jane Smith', 'Restaurant', 'Pancit Bihon', 8824, 170.00, 1, 'Paid', 'Gcash', 5.48, 164.51, 900, '2025-10-12 21:54:03', '2025-10-12 21:55:32', 0.00),
(304, 2, 'Jane Smith', 'Gift Store', 'Fridge Magnet Set', 6937, 180.00, 1, 'Paid', 'Gcash', 5.81, 174.19, 900, '2025-10-12 21:54:29', '2025-10-12 21:55:32', 930.00),
(305, 2, 'Jane Smith', 'Gift Store', 'Fragrant Candle', 6937, 350.00, 1, 'Paid', 'Gcash', 11.29, 338.71, 900, '2025-10-12 21:54:29', '2025-10-12 21:55:32', 930.00),
(306, 2, 'Jane Smith', 'Gift Store', 'Ferrero Rocher Chocolate Box', 6937, 250.00, 1, 'Paid', 'Gcash', 8.06, 241.94, 900, '2025-10-12 21:54:29', '2025-10-12 21:55:32', 930.00),
(307, 2, 'Jane Smith', 'Gift Store', 'Decorative Coasters', 6937, 150.00, 1, 'Paid', 'Gcash', 4.84, 145.16, 900, '2025-10-12 21:54:29', '2025-10-12 21:55:32', 930.00),
(308, 2, 'Jane Smith', 'Lounge Bar', 'Cocktail - Margarita', 5510, 300.00, 1, 'Paid', 'Gcash', 48.95, 251.05, 0, '2025-10-12 21:55:08', '2025-10-12 21:55:32', 300.00),
(309, 2, 'Jane Smith', 'Lounge Bar', 'Cocktail - Daiquiri', 5510, 280.00, 1, 'Paid', 'Gcash', 45.69, 234.31, 0, '2025-10-12 21:55:08', '2025-10-12 21:55:32', 280.00),
(310, 2, 'Jane Smith', 'Lounge Bar', 'Chivas Regal 12 Years', 5510, 2400.00, 1, 'Paid', 'Gcash', 391.63, 2008.37, 0, '2025-10-12 21:55:08', '2025-10-12 21:55:32', 2400.00),
(311, 2, 'Jane Smith', 'Lounge Bar', 'Baileys Irish Cream', 5510, 1800.00, 1, 'Paid', 'Gcash', 293.72, 1506.28, 0, '2025-10-12 21:55:08', '2025-10-12 21:55:32', 1800.00);

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_points`
--

CREATE TABLE `loyalty_points` (
  `id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `points` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `premium_cards`
--

CREATE TABLE `premium_cards` (
  `id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `card_number` varchar(50) NOT NULL,
  `issued_date` date NOT NULL DEFAULT curdate(),
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `benefits` text DEFAULT 'Priority check-in, Discounts on stays, Complimentary amenities'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `refund_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `guest_name` varchar(255) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item` varchar(255) NOT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `reason` varchar(50) NOT NULL,
  `payment_method` varchar(50) DEFAULT 'Cash',
  `status` enum('Pending','Completed') DEFAULT 'Completed',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `refunds`
--

INSERT INTO `refunds` (`refund_id`, `guest_id`, `guest_name`, `order_id`, `item`, `refund_amount`, `reason`, `payment_method`, `status`, `created_at`, `updated_at`) VALUES
(7, 2, 'Jane Smith', 2737, 'Bacardi Gold Rum', 380.00, 'Cancelled Order', 'PayMaya', 'Pending', '2025-10-12 14:59:48', '2025-10-12 14:59:48'),
(8, 2, 'Jane Smith', 2370, 'Cobra Energy Drink ', 120.00, 'Overcharge', 'Cash', 'Pending', '2025-10-12 14:59:55', '2025-10-12 14:59:55'),
(9, 2, 'Jane Smith', 6741, 'Fragrant Candle', 350.00, 'Cancelled Order', 'Cash', 'Pending', '2025-10-12 15:00:13', '2025-10-12 15:00:13'),
(10, 2, 'Jane Smith', 2345, 'Pork Adobo', 203.70, '', 'Cash', 'Completed', '2025-10-12 21:37:39', '2025-10-12 21:37:39'),
(11, 2, 'Jane Smith', 8019, 'Cobra Energy Drink ', 120.00, 'Cancelled Order', 'Cash', 'Pending', '2025-10-12 21:39:44', '2025-10-12 21:39:44');

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

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`reservation_id`, `guest_id`, `room_id`, `status`, `reservation_date`, `remarks`, `check_in`, `check_out`, `extended_duration`, `created_at`, `updated_at`, `actual_checkout`) VALUES
(1, 1, 101, 'reserved', '2025-10-05 00:00:00', 'Birthday stay', '2025-10-10 14:00:00', '2025-10-12 12:00:00', '0', '2025-10-10 22:00:33', '2025-10-10 22:00:33', NULL),
(2, 2, 102, 'checked_in', '2025-10-06 00:00:00', 'Business trip', '2025-10-09 13:00:00', '2025-10-11 11:00:00', '0', '2025-10-10 22:00:33', '2025-10-10 22:00:33', NULL),
(3, 3, 103, 'checked_out', '2025-10-03 00:00:00', 'Vacation', '2025-10-05 15:00:00', '2025-10-07 12:00:00', '1', '2025-10-10 22:00:33', '2025-10-10 22:00:33', '2025-10-08 11:00:00'),
(0, 4, 104, 'cancelled', '2025-10-02 00:00:00', 'Cancelled last minute', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '2025-10-10 22:00:33', '2025-10-10 22:00:33', NULL),
(0, 5, 105, 'reserved', '2025-10-07 00:00:00', 'Family weekend', '2025-10-14 14:00:00', '2025-10-16 12:00:00', '0', '2025-10-10 22:00:33', '2025-10-10 22:00:33', NULL),
(0, 6, 106, 'checked_in', '2025-10-08 00:00:00', 'Conference guest', '2025-10-09 14:00:00', '2025-10-10 12:00:00', '0', '2025-10-10 22:00:33', '2025-10-10 22:00:33', NULL),
(0, 7, 107, 'checked_out', '2025-09-30 00:00:00', 'Short stay', '2025-10-01 13:00:00', '2025-10-02 11:00:00', '0', '2025-10-10 22:00:33', '2025-10-10 22:00:33', '2025-10-02 10:30:00'),
(0, 8, 108, 'reserved', '2025-10-09 00:00:00', 'Anniversary', '2025-10-11 14:00:00', '2025-10-13 12:00:00', '0', '2025-10-10 22:00:33', '2025-10-10 22:00:33', NULL),
(0, 9, 109, 'reserved', '2025-10-10 00:00:00', 'Group booking', '2025-10-15 14:00:00', '2025-10-17 12:00:00', '0', '2025-10-10 22:00:33', '2025-10-10 22:00:33', NULL),
(0, 10, 110, 'checked_in', '2025-10-04 00:00:00', 'Extended weekend', '2025-10-09 14:00:00', '2025-10-12 12:00:00', '2', '2025-10-10 22:00:33', '2025-10-10 22:00:33', NULL);

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

--
-- Dumping data for table `room_payments`
--

INSERT INTO `room_payments` (`payment_id`, `guest_id`, `walkin_id`, `reservation_id`, `room_type`, `room_price`, `stay`, `extended_price`, `extended_duration`, `created_at`, `updated_at`) VALUES
(0, 1, NULL, 1, 'Deluxe', 4500.00, '2', 0.00, '0', '2025-10-10 22:00:34', '2025-10-10 22:00:34'),
(0, 2, NULL, 2, 'Executive', 5200.00, '2', 0.00, '0', '2025-10-10 22:00:34', '2025-10-10 22:00:34'),
(0, 3, NULL, 3, 'Suite', 8000.00, '3', 2000.00, '1', '2025-10-10 22:00:34', '2025-10-10 22:00:34'),
(0, 4, NULL, 4, 'Standard', 3000.00, '0', 0.00, '0', '2025-10-10 22:00:34', '2025-10-10 22:00:34'),
(0, 5, NULL, 5, 'Family Room', 6000.00, '2', 0.00, '0', '2025-10-10 22:00:34', '2025-10-10 22:00:34'),
(0, 6, NULL, 6, 'Executive', 5200.00, '1', 0.00, '0', '2025-10-10 22:00:34', '2025-10-10 22:00:34'),
(0, 7, NULL, 7, 'Single', 2500.00, '1', 0.00, '0', '2025-10-10 22:00:34', '2025-10-10 22:00:34'),
(0, 8, NULL, 8, 'Deluxe', 4500.00, '2', 0.00, '0', '2025-10-10 22:00:34', '2025-10-10 22:00:34'),
(0, 9, NULL, 9, 'Suite', 8000.00, '2', 0.00, '0', '2025-10-10 22:00:34', '2025-10-10 22:00:34'),
(0, 10, NULL, 10, 'Premier', 7000.00, '3', 3000.00, '2', '2025-10-10 22:00:34', '2025-10-10 22:00:34');

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
('EMP104006', 'Ryan', 'Torres', '2000-01-01', 'Male', 'ryan.torres@example.com', '09170208888', 'Mandaluyong', '2025-09-23', 'Active', '07:00:00', '15:00:00', 'Kimberly Lababo', 'Full-time', 22000.00, 125, '../contracts/contract_EMP104006.pdf', NULL, 'EMP104006_1760249442_hotel_room.jpg', NULL, NULL, NULL, '', 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Waiter / Waitress', 'Food & Beverage Service', NULL, 'Admin123*', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP128638', 'Sophie', 'Lopez', '2000-01-01', 'Male', 'sophie.lopez@example.com', '09170103333', 'Taguig', '2025-09-23', 'Active', '15:00:00', '23:00:00', 'Jessica Lopez', 'Full-time', 30000.00, 170, '../contracts/contract_EMP128638.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Room Attendant', 'Housekeeping', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
('EMP143950', 'John', 'Tan', '2000-01-01', 'Male', 'john.tan@example.com', '09170002222', 'Quezon City', '2025-09-23', 'Active', '07:00:00', '15:00:00', 'Kimberly Lababo', 'Full-time', 38000.00, 216, '../contracts/contract_EMP143950.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 'Health Insurance, Paid Leave, 13th Month Pay', 0, 'Front Office Manager', 'Front Office', NULL, 'temp123', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00),
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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `folio`
--
ALTER TABLE `folio`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guest_billing`
--
ALTER TABLE `guest_billing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loyalty_points`
--
ALTER TABLE `loyalty_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guest_id` (`guest_id`);

--
-- Indexes for table `premium_cards`
--
ALTER TABLE `premium_cards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `card_number` (`card_number`),
  ADD KEY `guest_id` (`guest_id`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`refund_id`),
  ADD KEY `idx_order_item` (`order_id`,`item`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`guest_id`);

--
-- Indexes for table `room_payments`
--
ALTER TABLE `room_payments`
  ADD PRIMARY KEY (`guest_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD UNIQUE KEY `staff_id` (`staff_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `folio`
--
ALTER TABLE `folio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=280;

--
-- AUTO_INCREMENT for table `guest_billing`
--
ALTER TABLE `guest_billing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=312;

--
-- AUTO_INCREMENT for table `loyalty_points`
--
ALTER TABLE `loyalty_points`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `premium_cards`
--
ALTER TABLE `premium_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `refund_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `loyalty_points`
--
ALTER TABLE `loyalty_points`
  ADD CONSTRAINT `loyalty_points_ibfk_1` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`) ON DELETE CASCADE;

--
-- Constraints for table `premium_cards`
--
ALTER TABLE `premium_cards`
  ADD CONSTRAINT `premium_cards_ibfk_1` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

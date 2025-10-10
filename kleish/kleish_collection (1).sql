-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 06, 2025 at 12:16 PM
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
-- Database: `kleish_collection`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` enum('Present','Absent','Late','On Leave') DEFAULT 'Present',
  `shift_start` time DEFAULT NULL,
  `shift_end` time DEFAULT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `day` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `employee_id`, `date`, `status`, `shift_start`, `shift_end`, `check_in`, `check_out`, `day`) VALUES
(1, 287688684, '2025-05-06', 'Present', NULL, NULL, '11:32:14', '11:51:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Bottom'),
(2, 'Bottom'),
(3, 'New Category'),
(4, 'Bottom'),
(5, 'Jacket'),
(6, 'Vintage'),
(7, 'Top'),
(8, 'Accessories'),
(9, 'Shoes'),
(10, 'Bags'),
(11, 'Others');

-- --------------------------------------------------------

--
-- Table structure for table `customer_feedback`
--

CREATE TABLE `customer_feedback` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `feedback` text NOT NULL,
  `date_submitted` timestamp NOT NULL DEFAULT current_timestamp(),
  `photo_1` varchar(255) DEFAULT NULL,
  `photo_2` varchar(255) DEFAULT NULL,
  `photo_3` varchar(255) DEFAULT NULL,
  `photo_4` varchar(255) DEFAULT NULL,
  `photo_5` varchar(255) DEFAULT NULL,
  `items_purchased` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_feedback`
--

INSERT INTO `customer_feedback` (`id`, `customer_name`, `feedback`, `date_submitted`, `photo_1`, `photo_2`, `photo_3`, `photo_4`, `photo_5`, `items_purchased`, `category`) VALUES
(1, 'Cheska Bautista', 'Great selection of products! I love how quickly I found what I was looking for. The quality of the items is top-notch, and the customer service was excellent. Will definitely return!', '2025-04-24 16:00:00', NULL, NULL, NULL, NULL, NULL, 'Vintage Denim Jacket, Floral Print Blouse', 'Clothing');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `employee_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  `mobile_number` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `emergency_contact_name` varchar(100) DEFAULT NULL,
  `emergency_contact_number` varchar(20) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `background` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`employee_id`, `first_name`, `last_name`, `role`, `mobile_number`, `email`, `address`, `birthdate`, `emergency_contact_name`, `emergency_contact_number`, `position`, `hire_date`, `salary`, `department`, `is_admin`, `password`, `created_at`, `background`) VALUES
(287688684, 'Cheska', 'Bautista', 'admin', '09077915906', 'jalotjot.cheska@gmail.com', 'Block 28 lot 11 phase 6-c towerville barangay gaya-gaya', '1995-05-18', '0', '', 'manager', '2025-05-06', 10000.00, NULL, 1, '$2y$10$3FTHXQOW0ALFPG3s/uywEO.GvoULshqxojJcT7nXh4lQWjnn.I6iq', '2025-05-06 09:18:17', 'N/A');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `discount` decimal(5,2) DEFAULT 0.00,
  `payment_method` enum('Cash','Credit Card','Debit Card','Online Payment') NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `product_id`, `quantity`, `total`, `order_date`, `status`, `customer_id`, `customer_name`, `discount`, `payment_method`, `total_amount`) VALUES
(1, NULL, NULL, NULL, 0.00, '2025-04-28 13:36:02', 'Pending', NULL, 'cheska', 0.00, 'Cash', 0.00),
(2, NULL, NULL, NULL, 3360.00, '2025-04-28 13:49:07', 'Pending', NULL, 'cheska', 20.00, 'Cash', 0.00),
(3, NULL, NULL, NULL, 180.00, '2025-04-28 13:51:11', 'Pending', NULL, 'cheska', 0.00, 'Cash', 0.00),
(4, NULL, NULL, NULL, 350.00, '2025-04-28 13:59:03', 'Pending', NULL, 'cheska', 0.00, 'Cash', 0.00),
(5, NULL, NULL, NULL, 9900.00, '2025-04-29 15:24:05', 'Pending', NULL, 'cheska', 10.00, 'Cash', 0.00),
(6, NULL, NULL, NULL, 500.00, '2025-04-29 15:24:31', 'Pending', NULL, 'cheska', 0.00, 'Cash', 0.00),
(7, NULL, NULL, NULL, 500.00, '2025-04-29 15:31:38', 'Pending', NULL, 'cheska', 0.00, 'Cash', 0.00),
(8, NULL, NULL, NULL, 8820.00, '2025-04-30 08:45:03', 'Pending', NULL, 'cheska', 10.00, 'Cash', 0.00),
(9, NULL, NULL, NULL, 560.00, '2025-04-30 09:49:36', 'Pending', NULL, 'ella', 20.00, 'Cash', 0.00),
(10, NULL, NULL, NULL, 351900.00, '2025-05-01 09:02:02', 'Pending', NULL, 'cheska', 10.00, 'Cash', 0.00),
(11, NULL, NULL, NULL, 20000.00, '2025-05-01 09:37:51', 'Pending', NULL, 'savage', 20.00, 'Cash', 0.00),
(12, NULL, NULL, NULL, 573696.00, '2025-05-01 11:03:59', 'Pending', NULL, 'john loyd', 10.00, 'Cash', 0.00),
(13, NULL, NULL, NULL, 332800.00, '2025-05-01 15:40:40', 'Pending', NULL, 'cheska', 0.00, 'Cash', 0.00),
(14, NULL, NULL, NULL, 1575.00, '2025-05-02 12:32:30', 'Pending', NULL, 'cheska', 10.00, 'Cash', 0.00),
(15, NULL, NULL, NULL, 19442.50, '2025-05-03 02:30:40', 'Pending', NULL, 'qwerty', 30.00, 'Cash', 0.00),
(16, NULL, NULL, NULL, 716.00, '2025-05-03 03:15:12', 'Pending', NULL, 'jessa', 20.00, 'Cash', 0.00),
(17, NULL, NULL, NULL, 22400.00, '2025-05-06 07:00:25', 'Pending', NULL, 'cheska', 0.00, 'Cash', 0.00),
(18, NULL, NULL, NULL, 91400.00, '2025-05-06 07:51:49', 'Pending', NULL, 'cheska', 0.00, 'Cash', 0.00),
(19, NULL, NULL, NULL, 82875.00, '2025-05-06 07:52:54', 'Pending', NULL, 'Princess', 15.00, 'Cash', 0.00),
(20, NULL, NULL, NULL, 30400.00, '2025-05-06 10:03:09', 'Pending', NULL, 'cheska', 5.00, 'Cash', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders_backup`
--

CREATE TABLE `orders_backup` (
  `order_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `discount` decimal(5,2) DEFAULT 0.00,
  `payment_method` enum('Cash','Credit Card','Debit Card','Online Payment') NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders_backup`
--

INSERT INTO `orders_backup` (`order_id`, `user_id`, `product_id`, `quantity`, `total`, `order_date`, `status`, `customer_id`, `customer_name`, `discount`, `payment_method`, `total_amount`) VALUES
(1, NULL, NULL, NULL, 0.00, '2025-04-28 13:36:02', 'Pending', NULL, 'cheska', 0.00, 'Cash', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `total`) VALUES
(1, 5, 9, 44, 24420.00),
(2, 6, 10, 22, 484.00),
(3, 7, 10, 22, 484.00),
(4, 8, 10, 22, 484.00),
(5, 9, 10, 111, 2442.00),
(6, 9, 9, 12, 6660.00),
(7, 10, 8, 55, 220.00),
(8, 11, 11, 1, 500.00),
(9, 11, 12, 1, 350.00),
(10, 11, 13, 1, 180.00),
(11, 11, 16, 1, 250.00),
(12, 11, 17, 1, 220.00),
(13, 11, 21, 3, 1950.00),
(14, 11, 23, 4, 2200.00),
(15, 11, 24, 5, 1750.00),
(16, 11, 13, 22, 3960.00),
(17, 11, 25, 22, 6600.00),
(18, 11, 29, 5, 750.00),
(19, 1, 19, 12, 5400.00),
(20, 1, 29, 22, 3300.00),
(21, 1, 13, 1, 180.00),
(22, 1, 13, 111, 19980.00),
(23, 2, 12, 12, 4200.00),
(24, 3, 13, 1, 180.00),
(25, 4, 12, 1, 350.00),
(26, 5, 11, 22, 11000.00),
(27, 6, 11, 1, 500.00),
(28, 7, 11, 1, 500.00),
(29, 8, 11, 2, 1000.00),
(30, 8, 14, 22, 8800.00),
(31, 9, 12, 2, 700.00),
(32, 10, 11, 5, 2500.00),
(33, 10, 15, 555, 388500.00),
(34, 11, 32, 500, 25000.00),
(35, 12, 18, 88, 15840.00),
(36, 12, 15, 888, 621600.00),
(37, 13, 14, 55, 22000.00),
(38, 13, 15, 444, 310800.00),
(39, 14, 12, 5, 1750.00),
(40, 15, 33, 5, 27775.00),
(41, 16, 35, 5, 895.00),
(42, 17, 14, 56, 22400.00),
(43, 18, 25, 5, 1500.00),
(44, 18, 24, 56, 19600.00),
(45, 18, 28, 66, 49500.00),
(46, 18, 24, 56, 19600.00),
(47, 18, 25, 4, 1200.00),
(48, 19, 25, 6, 1800.00),
(49, 19, 15, 66, 46200.00),
(50, 19, 28, 66, 49500.00),
(51, 20, 24, 5, 1750.00),
(52, 20, 23, 55, 30250.00);

-- --------------------------------------------------------

--
-- Table structure for table `pos_orders`
--

CREATE TABLE `pos_orders` (
  `order_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `order_date` datetime NOT NULL,
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `category_id`, `price`, `stock`) VALUES
(11, 'Vintage Leather Bag', 3, 500.00, 2147483647),
(12, 'Cargo Pants', 1, 350.00, 2147483583),
(13, 'Patterned Scarf', 4, 100.00, 2147483534),
(14, 'Striped Sweater', 2, 400.00, 2147483514),
(15, 'Sporty Sneakers', 3, 700.00, 2147481694),
(16, 'Chiffon Blouse', 2, 250.00, 2147483647),
(17, 'Canvas Tote Bag', 4, 220.00, 2147483647),
(18, 'High-Waisted Shorts', 1, 180.00, 2147483559),
(19, 'Graphic Hoodie', 2, 450.00, 94142),
(20, 'Flared Skirt', 1, 220.00, 2147483647),
(21, 'Chunky Sneakers', 3, 650.00, 40000000),
(22, 'Vintage Fedora Hat', 4, 300.00, 2147483647),
(23, 'Tweed Blazer', 1, 550.00, 54999945),
(24, 'Bootcut Jeans', 1, 350.00, 2147483530),
(25, 'Plaid Shirt', 2, 300.00, 2147483632),
(26, 'Summer Romper', 2, 350.00, 2147483647),
(27, 'Cozy Cardigan', 2, 400.00, 2147483647),
(28, 'Slouchy Boots', 3, 750.00, 2147483515),
(29, 'Leather Belt', 4, 150.00, 8000000),
(30, 'Knit Beanie', 4, 120.00, 2),
(31, 'tshirt', 7, 100.00, 555555),
(32, 'panty', 3, 50.00, 653944),
(33, 'pants', 1, 5555.00, 55550),
(35, 'denim', 1, 179.00, 95),
(36, 'polo crop', 7, 149.00, 6544),
(37, 'Trouser Short', 2, 199.00, 23536),
(38, 'Sleeveless', 7, 99.00, 1237);

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` int(11) NOT NULL,
  `promotion_name` varchar(255) DEFAULT NULL,
  `promotion_description` text DEFAULT NULL,
  `promotion_type` varchar(100) DEFAULT NULL,
  `promo_code` varchar(100) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promotions`
--

INSERT INTO `promotions` (`id`, `promotion_name`, `promotion_description`, `promotion_type`, `promo_code`, `start_date`, `end_date`) VALUES
(1, 'TikTok Trend Boost', 'Leverage viral TikTok trends to increase store visibility and drive ukay-ukay traffic. Includes featured hashtag challenges and influencer collabs.', 'Social Media Campaign', 'TTBOOST2025', '2025-05-10', '2025-06-10');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salaries`
--

CREATE TABLE `salaries` (
  `salary_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `basic_salary` decimal(10,2) DEFAULT NULL,
  `last_paid` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `sale_date` date DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `total_sales` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `role` enum('admin','customer','manager') NOT NULL DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `customer_feedback`
--
ALTER TABLE `customer_feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_customer_id` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `pos_orders`
--
ALTER TABLE `pos_orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `salaries`
--
ALTER TABLE `salaries`
  ADD PRIMARY KEY (`salary_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `customer_feedback`
--
ALTER TABLE `customer_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=287688685;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `pos_orders`
--
ALTER TABLE `pos_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salaries`
--
ALTER TABLE `salaries`
  MODIFY `salary_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `salaries`
--
ALTER TABLE `salaries`
  ADD CONSTRAINT `salaries_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

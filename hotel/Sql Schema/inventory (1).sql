-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 04, 2025 at 02:30 PM
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
(127, 'lop', 'Hotel Supplies', 1, 0, 0, 65.00, ''),
(128, '\'656', 'Foods & Beverages', 1, 0, 0, 656.00, 'System'),
(129, '545', 'Office Supplies', 1, 0, 0, 554.00, 'Kai Dela Cruz'),
(130, '\'656', 'Utility Products', 1, 0, 0, 656.00, 'Kai Dela Cruz'),
(131, '323', 'Furniture & Fixtures', 0, 7, 0, 322.00, 'Kai Dela Cruz'),
(132, 'adobo mix', 'Foods & Beverages', 200, 11, 0, 4000.00, 'Mika Reyes'),
(133, 'hjj', 'Cleaning & Sanitation', 1, 0, 0, 55.00, 'Kai Dela Cruz'),
(134, 'lop', 'Utility Products', 1, 0, 0, 65.00, 'Kai Dela Cruz');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`item_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

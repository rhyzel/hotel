/*
SQLyog Community v12.4.0 (64 bit)
MySQL - 10.4.32-MariaDB : Database - housekeepingdb
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`housekeepingdb` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `housekeepingdb`;

/*Table structure for table `guests` */

DROP TABLE IF EXISTS `guests`;

CREATE TABLE `guests` (
  `guest_id` int(11) NOT NULL,
  PRIMARY KEY (`guest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `guests` */

/*Table structure for table `housekeeping_inventory` */

DROP TABLE IF EXISTS `housekeeping_inventory`;

CREATE TABLE `housekeeping_inventory` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(255) NOT NULL,
  `category` enum('Cleaning Supply','Linen','Toiletry') NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `reorder_level` int(11) NOT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `housekeeping_inventory` */

/*Table structure for table `housekeeping_room_status` */

DROP TABLE IF EXISTS `housekeeping_room_status`;

CREATE TABLE `housekeeping_room_status` (
  `room_id` int(11) NOT NULL,
  `room_type` enum('Econo','Regular','Deluxe','Suite') DEFAULT NULL,
  `status` enum('Clean','Dirty','In Progress','Out of Service') NOT NULL,
  `last_cleaned` datetime DEFAULT NULL,
  `assigned_staff` int(11) DEFAULT NULL,
  `housekeeper_id` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `damaged_property` text DEFAULT NULL,
  `penalty` decimal(10,2) DEFAULT NULL,
  `guest_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`room_id`),
  KEY `assigned_staff` (`assigned_staff`),
  KEY `housekeeper_id` (`housekeeper_id`),
  KEY `guest_id` (`guest_id`),
  CONSTRAINT `housekeeping_room_status_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE,
  CONSTRAINT `housekeeping_room_status_ibfk_2` FOREIGN KEY (`assigned_staff`) REFERENCES `staff` (`staff_id`) ON DELETE SET NULL,
  CONSTRAINT `housekeeping_room_status_ibfk_3` FOREIGN KEY (`housekeeper_id`) REFERENCES `staff` (`staff_id`) ON DELETE SET NULL,
  CONSTRAINT `housekeeping_room_status_ibfk_4` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `housekeeping_room_status` */

/*Table structure for table `housekeeping_tasks` */

DROP TABLE IF EXISTS `housekeeping_tasks`;

CREATE TABLE `housekeeping_tasks` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `task_date` date NOT NULL,
  `task_type` enum('Cleaning','Laundry','Turn-down','Deep Cleaning') NOT NULL,
  `status` enum('Pending','In Progress','Completed') NOT NULL,
  `remarks` text DEFAULT NULL,
  PRIMARY KEY (`task_id`),
  KEY `room_id` (`room_id`),
  KEY `staff_id` (`staff_id`),
  CONSTRAINT `housekeeping_tasks_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE,
  CONSTRAINT `housekeeping_tasks_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `housekeeping_tasks` */

/*Table structure for table `maintenance_requests` */

DROP TABLE IF EXISTS `maintenance_requests`;

CREATE TABLE `maintenance_requests` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `reported_by` int(11) NOT NULL,
  `issue_description` text NOT NULL,
  `priority` enum('Low','Medium','High') NOT NULL,
  `status` enum('Pending','In Progress','Resolved') NOT NULL,
  `reported_date` datetime NOT NULL DEFAULT current_timestamp(),
  `completed_date` date DEFAULT NULL,
  PRIMARY KEY (`request_id`),
  KEY `room_id` (`room_id`),
  KEY `reported_by` (`reported_by`),
  CONSTRAINT `maintenance_requests_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE,
  CONSTRAINT `maintenance_requests_ibfk_2` FOREIGN KEY (`reported_by`) REFERENCES `staff` (`staff_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `maintenance_requests` */

/*Table structure for table `rooms` */

DROP TABLE IF EXISTS `rooms`;

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_number` varchar(20) NOT NULL,
  `room_type` enum('Econo','Regular','Deluxe','Suite') NOT NULL,
  PRIMARY KEY (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `rooms` */

/*Table structure for table `staff` */

DROP TABLE IF EXISTS `staff`;

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `staff_name` varchar(100) NOT NULL,
  PRIMARY KEY (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `staff` */

/*Table structure for table `staff_performance_tracking` */

DROP TABLE IF EXISTS `staff_performance_tracking`;

CREATE TABLE `staff_performance_tracking` (
  `performance_id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `tasks_completed` int(11) NOT NULL,
  `average_completion_time` time NOT NULL,
  `quality_rating` tinyint(4) NOT NULL CHECK (`quality_rating` between 1 and 5),
  `feedback` text DEFAULT NULL,
  `evaluator_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  PRIMARY KEY (`performance_id`),
  KEY `staff_id` (`staff_id`),
  KEY `task_id` (`task_id`),
  KEY `evaluator_id` (`evaluator_id`),
  CONSTRAINT `staff_performance_tracking_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`) ON DELETE CASCADE,
  CONSTRAINT `staff_performance_tracking_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `housekeeping_tasks` (`task_id`) ON DELETE SET NULL,
  CONSTRAINT `staff_performance_tracking_ibfk_3` FOREIGN KEY (`evaluator_id`) REFERENCES `staff` (`staff_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `staff_performance_tracking` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for smartcity
CREATE DATABASE IF NOT EXISTS `smartcity` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `smartcity`;

-- Dumping structure for table smartcity.newcomplaints
CREATE TABLE IF NOT EXISTS `newcomplaints` (
  `complaint_id` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int NOT NULL,
  `issue_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `issue_details` text COLLATE utf8mb4_general_ci NOT NULL,
  `date_time` datetime DEFAULT NULL,
  `location` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `landmark` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `additional_info` text COLLATE utf8mb4_general_ci,
  `status` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  `privacy_policy_agreement` tinyint(1) NOT NULL DEFAULT '0',
  `attachment1` varchar(1000) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `attachment2` varchar(1000) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_image` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`complaint_id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table smartcity.newcomplaints: ~11 rows (approximately)
INSERT INTO `newcomplaints` (`complaint_id`, `user_id`, `issue_type`, `issue_details`, `date_time`, `location`, `landmark`, `additional_info`, `status`, `privacy_policy_agreement`, `attachment1`, `attachment2`, `created_at`, `completed_image`) VALUES
	('111486', 18, 'Waterlogging', 'There is significant waterlogging on the main road, with stagnant rainwater covering the entire width of the street, making it difficult for vehicles and pedestrians to pass', '2025-09-09 10:29:40', 'Saveetha University Rd, Kuthambakkam, Tamil Nadu 602105, India', 'SIMATS Medical College', 'The waterlogging appears to be caused by a blocked or overflowing drainage system. The water is about ankle-deep and has been stagnant for over a day, creating a health hazard and a potential breeding ground for mosquitoes.', 'Completed', 1, 'uploads/111486_image1.jpg', 'uploads/111486_image2.jpg', '2025-09-15 05:04:23', 'images/111486_completed.jpg'),
	('165127', 2, 'Waterlogging', 'Water was logged outside of my house; please clear this as soon as possible.', '2025-09-10 11:19:39', 'C Block, Kg Centre Point, Chembarambakkam, Tamil Nadu 600123, India', 'KG Apartments', 'The rain has been relentless lately; I can\'t believe the amount of water that\'s accumulated. It\'s all sitting right outside my place, making it look like a small lake. This is definitely something that needs to be taken care of quickly before it causes problems. I\'m worried about potential damage to the foundation of the house with all this standing water. It\'s also creating an unwelcome breeding ground for mosquitoes, which isn\'t ideal. So let\'s get this sorted out ASAP to keep things running smoothly and prevent further issues.', 'Pending', 1, 'uploads/165127_image1.jpg', '', '2025-09-15 05:54:19', NULL),
	('389639', 18, 'Street Light Not Working', 'The primary street light at the main road junction is completely non-functional and does not switch on at night.', '2025-09-10 19:08:35', 'Chettipedu, Chembarambakkam, Tamil Nadu 600123, India', 'Vijaysanthi Apartments, Chettipedu', 'This specific light has been out for nearly a week. The area becomes very dark and is a safety concern for people walking from the bus stop in the evening. The pole number is SavCh-087.', 'Pending', 1, 'uploads/389639_image1.jpg', '', '2025-09-15 04:46:01', NULL),
	('399713', 18, 'Damaged Speed Breaker', 'The concrete speed breaker has been partially destroyed, with broken pieces and sharp edges creating a hazard on the road.', '2025-09-05 10:16:34', 'Thandalam Junction Mannur Road, Arakkonam Hwy, Thandal, Kuthambakkam, Tamil Nadu 602117, India', 'Bengaluru Highway', 'The damage is severe, exposing the internal metal rods. Instead of slowing traffic, it is now causing vehicles to swerve abruptly to avoid tyre damage, which is more dangerous. It is also a tripping hazard for pedestrians.', 'Pending', 1, 'uploads/399713_image1.jpg', '', '2025-09-15 04:51:05', NULL),
	('476118', 18, 'Garbage on Road', 'A significant amount of uncollected household garbage and plastic waste has been dumped on the side of the road, spilling onto the main thoroughfare.', '2025-09-01 11:12:41', '1A, Post, Chettipedu Village, Thandalam, Sriperunbudur Taluk, Tamil Nadu 602105, India', 'OTTO Clothes Factory', 'The garbage pile is emitting a strong foul odor and is attracting stray animals (dogs and cattle), which are scattering the waste across the road. This is creating a major public health issue and a potential hazard for traffic.', 'Pending', 1, 'uploads/476118_image1.jpg', 'uploads/476118_image2.jpg', '2025-09-15 05:45:57', NULL),
	('628495', 18, 'Waterlogging', 'water log', '2025-09-27 07:49:28', '22H8+654, Kuthambakkam, Tamil Nadu 602105, India', 'saveetha', 'please slove it fast', 'Pending', 1, 'uploads/628495_image1.jpg', '', '2025-09-27 02:21:26', NULL),
	('636336', 18, 'Waterlogging', 'water log', '2025-09-27 07:49:28', '22H8+654, Kuthambakkam, Tamil Nadu 602105, India', 'saveetha', 'please slove it fast', 'Pending', 1, '', '', '2025-09-27 02:21:58', NULL),
	('674651', 18, 'Waterlogging', 'water log', '2025-09-27 07:49:28', '22H8+654, Kuthambakkam, Tamil Nadu 602105, India', 'saveetha', 'please slove it fast', 'Pending', 1, 'uploads/674651_image1.jpg', '', '2025-09-27 02:21:44', NULL),
	('708465', 18, 'Faded Road Markings', 'Dude, the yellow paint for the parking and handicap signs on the road is super faded, and you can barely see it.', '2025-09-16 10:52:25', '43, Periamet, Kannappar Thidal, Park Town, Tiruvallur, Chennai, Tamil Nadu 600003, India', 'Dr. M .G. R. Central Railway Station', 'The markings are nearly invisible at night and during rain, causing confusion for drivers and posing a serious safety risk for pedestrians. The stop line before the crosswalk has completely disappeared.', 'Completed', 1, 'uploads/708465_image1.jpg', '', '2025-09-15 05:25:49', 'images/708465_completed.jpg'),
	('742553', 2, 'Potholes', 'A large and deep pothole has formed in the middle of the parking road, posing a significant risk to vehicles, especially two-wheelers', '2025-08-04 09:41:12', '22G8+5HW, Kuthambakkam, Tamil Nadu 602105, India', 'Saveetha Medical College, Car Parking', 'The pothole is quite deep and fills with water, making it hard to see. It has been causing traffic to slow down considerably, particularly during the morning rush hour.', 'Completed', 1, 'uploads/742553_image1.jpg', '', '2025-09-15 04:19:13', 'images/742553_completed.jpg'),
	('749093', 2, 'Faded Road Markings', 'The white paint for the lane dividers on the main road has become extremely faded and is difficult to see.', '2025-09-03 08:55:21', 'Kg Centre Point, Chembarambakkam, Tamil Nadu 600123, India', 'Papanchathiram (POP City)', 'The markings are nearly invisible at night and during rain, causing confusion for drivers and posing a serious safety risk for pedestrians. The stop line before the crosswalk has completely disappeared.', 'Pending', 1, 'uploads/749093_image1.jpg', 'uploads/749093_image2.jpg', '2025-09-15 05:19:17', 'images/749093_completed.jpg');

-- Dumping structure for table smartcity.users
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `mobile_number` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `city` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `occupation` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `profile_pic` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` enum('user','admin') COLLATE utf8mb4_general_ci DEFAULT 'user',
  `status` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `old_password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_token_hash` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL,
  `first_login` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`user_id`),
  KEY `idx_reset_token` (`reset_token_hash`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table smartcity.users: ~8 rows (approximately)
INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `date_of_birth`, `mobile_number`, `city`, `occupation`, `profile_pic`, `role`, `status`, `old_password`, `reset_token_hash`, `reset_token_expires_at`, `first_login`) VALUES
	(1, 'District Administator', 'admin@example.com', 'admin', '1990-01-01', '0000000000', 'Chennai', 'Administrator', 'no', 'admin', NULL, NULL, NULL, NULL, 0),
	(2, 'user', 'user@gmail.com', 'user', '2001-08-19', '1234567890', 'Hyderabad', 'Student', 'uploads/2_profilepic.jpeg', 'user', NULL, NULL, '89d09a1b0430c0495fed7ba34b793787dc759af4df9e5cbc9e4bfdd75c438e0d', '2025-09-27 02:41:53', 0),
	(6, 'Vishnu', 'vishnu@gmail.com', 'vishnu', '2000-01-01', '0000000000', 'Hyderabad', 'Student', 'https://example.com/pic.jpg', 'user', NULL, NULL, NULL, NULL, 0),
	(10, 'pavaneshwar', 'pavaneswar224@gmail.com', 'pavan', '2005-08-05', '6305240281', 'chennai', 'student', 'uploads/10_profilepic.jpeg', 'user', NULL, NULL, NULL, NULL, 1),
	(11, 'bharathkantu', 'kantubharath@gmail.com', 'Bharath@123', '2004-07-02', '7396203818', 'Ongole ', 'Student', 'https://example.com/pic.jpg', 'user', NULL, NULL, NULL, NULL, 1),
	(18, 'S. Vishnu Teja', 'testingone2023@gmail.com', 'user', '2025-09-25', '6305808659', 'Nellore', 'Student', 'uploads/18_profilepic.jpeg', 'user', NULL, NULL, NULL, NULL, 0),
	(19, 'VISHNU TEJA', 'sannapureddyvishnuteja@gmail.com', '', '2005-04-20', '6305808659', 'Nellore', 'Student', 'uploads/19_profilepic.jpeg', 'user', NULL, NULL, NULL, NULL, 1),
	(20, 'Vishnuteja S', 'vishnutejas4069.sse@saveetha.com', NULL, NULL, NULL, NULL, NULL, 'https://lh3.googleusercontent.com/a/ACg8ocLg11j6n8-Sv28unylbCQwaFyM5hxSD0evkDUm55vDbqDSDQIQ=s96-c', 'user', NULL, NULL, NULL, NULL, 1);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

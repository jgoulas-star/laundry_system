-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2026 at 04:58 PM
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
-- Database: `laundry_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laundry_cycles`
--

CREATE TABLE `laundry_cycles` (
  `cycle_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `machine_Id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `cycle_status` enum('running','finished') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laundry_cycles`
--

INSERT INTO `laundry_cycles` (`cycle_id`, `user_id`, `machine_Id`, `start_time`, `end_time`, `cycle_status`) VALUES
(0, 8, 2, '2026-04-15 15:23:11', '2026-04-15 15:24:11', 'finished');

-- --------------------------------------------------------

--
-- Table structure for table `machines`
--

CREATE TABLE `machines` (
  `machine_ID` int(11) NOT NULL,
  `machine_number` varchar(20) NOT NULL,
  `machine_type` enum('washer','dryer') NOT NULL,
  `status` enum('available','in_use','out_of_order') NOT NULL,
  `location` varchar(100) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `machines`
--

INSERT INTO `machines` (`machine_ID`, `machine_number`, `machine_type`, `status`, `location`, `added_at`) VALUES
(1, '1000', 'washer', 'out_of_order', 'Russell Tower', '2026-04-27 13:25:56'),
(2, '1001', 'washer', 'available', 'Mara Village', '2026-04-26 19:45:53'),
(3, '1002', 'dryer', 'available', 'Russell Tower', '2026-04-26 19:41:38'),
(4, '1003', 'dryer', 'available', 'Mara Village', '2026-04-27 13:12:58'),
(5, '1004', 'washer', 'in_use', 'Aubuchon Hall', '2026-04-27 13:25:50'),
(6, '1005', 'dryer', 'in_use', 'Aubuchon Hall', '2026-04-27 13:25:35'),
(7, '1006', 'dryer', 'out_of_order', 'Aubuchon Hall', '2026-04-27 13:25:44'),
(8, '1007', 'washer', 'available', 'Townhouse', '2026-04-27 13:15:46'),
(9, '1008', 'dryer', 'out_of_order', 'Townhouse', '2026-04-27 13:26:02'),
(10, '1009', 'dryer', 'available', 'Townhouse', '2026-04-27 13:17:05');

-- --------------------------------------------------------

--
-- Table structure for table `machine_reports`
--

CREATE TABLE `machine_reports` (
  `report_id` int(11) NOT NULL,
  `machine_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `report_type` enum('faulty','turned_off','other') NOT NULL,
  `message` text NOT NULL,
  `status` enum('open','resolved') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT 'Laundry Update',
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `machine_Id` int(11) NOT NULL,
  `reservation_start` datetime NOT NULL,
  `reservation_end` datetime NOT NULL,
  `status` enum('active','cancelled','completed') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`reservation_id`, `user_id`, `machine_Id`, `reservation_start`, `reservation_end`, `status`) VALUES
(1, 6, 2, '2026-04-25 14:19:53', '2026-04-25 14:20:53', 'cancelled'),
(2, 6, 1, '2026-04-25 14:20:03', '2026-04-25 14:21:03', 'cancelled'),
(3, 6, 1, '2026-04-25 14:21:16', '2026-04-25 14:22:16', 'cancelled'),
(4, 6, 1, '2026-04-25 14:21:18', '2026-04-25 14:22:18', 'cancelled'),
(5, 6, 3, '2026-04-26 21:40:47', '2026-04-26 22:40:47', 'cancelled'),
(6, 6, 1, '2026-04-26 23:36:08', '2026-04-27 00:36:08', 'cancelled'),
(7, 6, 6, '2026-04-27 15:19:24', '2026-04-27 16:19:24', 'cancelled');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_ID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student') NOT NULL DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_ID`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(6, 'Jordan', 'jordan@gmail.com', '$2y$10$WyJKg7I4/ViKZ.8CGoowOeUpPGJ8NFQtcsaeC9PigBEGh0XpgWo9K', 'student', '2026-04-13 22:44:22'),
(8, 'Jordan', 'jordangoulas@gmail.com', '$2y$10$hw.oRpX/vdyOTny/xOgWy.qsmdjW0etJRRptXp0sDOAc6mWJXTOuS', 'admin', '2026-04-13 23:14:35'),
(10, 'jordan', 'jordan1@gmail.com', '$2y$10$py0pgEK3ooigSLFnZw/FteBObY3u0GXU3UzoddidetfTknVOwV8aq', 'student', '2026-04-27 14:46:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laundry_cycles`
--
ALTER TABLE `laundry_cycles`
  ADD PRIMARY KEY (`cycle_id`),
  ADD KEY `user_id` (`user_id`,`machine_Id`),
  ADD KEY `machine_Id` (`machine_Id`);

--
-- Indexes for table `machines`
--
ALTER TABLE `machines`
  ADD PRIMARY KEY (`machine_ID`);

--
-- Indexes for table `machine_reports`
--
ALTER TABLE `machine_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `machine_id` (`machine_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `user_id` (`user_id`,`machine_Id`),
  ADD KEY `machine_Id` (`machine_Id`),
  ADD KEY `Reservation_ID` (`reservation_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `machines`
--
ALTER TABLE `machines`
  MODIFY `machine_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `machine_reports`
--
ALTER TABLE `machine_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `laundry_cycles`
--
ALTER TABLE `laundry_cycles`
  ADD CONSTRAINT `laundry_cycles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_ID`),
  ADD CONSTRAINT `laundry_cycles_ibfk_2` FOREIGN KEY (`machine_Id`) REFERENCES `machines` (`machine_ID`);

--
-- Constraints for table `machine_reports`
--
ALTER TABLE `machine_reports`
  ADD CONSTRAINT `machine_reports_ibfk_1` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`machine_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `machine_reports_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_ID`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`machine_Id`) REFERENCES `machines` (`machine_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

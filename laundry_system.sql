-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 25, 2026 at 03:56 AM
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
(23, 1, 6, '2026-04-24 21:23:23', '2026-04-24 21:24:23', 'finished'),
(24, 1, 6, '2026-04-24 21:25:14', '2026-04-24 21:26:14', 'finished'),
(25, 1, 7, '2026-04-24 21:27:26', '2026-04-24 21:28:26', 'finished'),
(26, 1, 6, '2026-04-24 21:27:33', '2026-04-24 21:28:33', 'finished'),
(27, 1, 6, '2026-04-24 21:31:03', '2026-04-24 21:32:03', 'finished'),
(28, 1, 5, '2026-04-24 21:32:31', '2026-04-24 21:33:31', 'finished'),
(29, 1, 2, '2026-04-24 21:34:11', '2026-04-24 21:35:11', 'finished'),
(30, 1, 4, '2026-04-24 21:34:24', '2026-04-24 21:35:24', 'finished');

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
(1, 'W1', 'washer', 'available', 'Russell Tower', '2026-04-24 23:22:35'),
(2, 'D1', 'dryer', 'available', 'Russell Tower', '2026-04-25 01:34:40'),
(3, 'W1', 'washer', 'available', 'TownHouses', '2026-04-24 23:22:36'),
(4, 'D1', 'dryer', 'available', 'TownHouses', '2026-04-25 01:34:39'),
(5, 'W1', 'washer', 'available', 'Aubuchon Hall', '2026-04-25 01:34:07'),
(6, 'D1', 'dryer', 'available', 'Aubuchon Hall', '2026-04-25 01:32:31'),
(7, 'W1', 'washer', 'available', 'Mara Village', '2026-04-25 01:27:30'),
(8, 'D1', 'dryer', 'available', 'Mara Village', '2026-04-25 01:29:58');

-- --------------------------------------------------------

--
-- Table structure for table `machine_reports`
--

CREATE TABLE `machine_reports` (
  `report_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `machine_id` int(11) NOT NULL,
  `report_type` enum('faulty','turned_off','other') NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  `status` enum('open','resolved') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_read` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `Reservation_ID` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `machine_Id` int(11) NOT NULL,
  `reservation_start` datetime NOT NULL,
  `reservation_end` datetime NOT NULL,
  `status` enum('active','cancelled','completed') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`Reservation_ID`, `user_id`, `machine_Id`, `reservation_start`, `reservation_end`, `status`) VALUES
(13, 1, 5, '2026-04-25 01:20:15', '2026-04-25 01:21:15', 'cancelled'),
(14, 1, 3, '2026-04-25 01:20:30', '2026-04-25 01:21:30', 'cancelled'),
(15, 1, 1, '2026-04-25 01:20:40', '2026-04-25 01:21:40', 'cancelled'),
(22, 1, 5, '2026-04-24 20:40:29', '2026-04-24 20:41:29', 'active'),
(27, 1, 5, '2026-04-24 21:21:14', '2026-04-24 21:22:14', 'active'),
(31, 1, 6, '2026-04-24 21:27:33', '2026-04-24 21:28:33', 'active'),
(32, 1, 6, '2026-04-24 21:31:03', '2026-04-24 21:32:03', 'active'),
(33, 1, 5, '2026-04-24 21:32:31', '2026-04-24 21:33:31', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_ID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role(customer/admin)` enum('customer','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_ID`, `name`, `email`, `password`, `role(customer/admin)`, `created_at`) VALUES
(1, 'Tafari', 'tafari@gmail.com', '$2y$10$82lzHpUwQptBnbYm3V0MQun75zLJfM4y/cyzzbRun/VFH5dMGviXm', 'admin', '2026-04-12 22:29:25'),
(2, 'Tafari', 'test@gmail.com', '$2y$10$E3hHsccwdeZx.5sXmk6YeOcd6yTt0a30E2xXyTsPQ.cWvJt5XeVAO', 'customer', '2026-04-12 21:34:09');

--
-- Indexes for dumped tables
--

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
  ADD PRIMARY KEY (`report_id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`Reservation_ID`,`user_id`,`machine_Id`,`reservation_start`,`reservation_end`,`status`),
  ADD KEY `user_id` (`user_id`,`machine_Id`),
  ADD KEY `machine_Id` (`machine_Id`),
  ADD KEY `Reservation_ID` (`Reservation_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `laundry_cycles`
--
ALTER TABLE `laundry_cycles`
  MODIFY `cycle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `machines`
--
ALTER TABLE `machines`
  MODIFY `machine_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `machine_reports`
--
ALTER TABLE `machine_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `Reservation_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_ID`);

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

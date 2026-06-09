-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2026 at 01:50 PM
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
-- Database: `event_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `created_at`) VALUES
(1, 'Mzumbe Day', 'you all welcomed', '2026-06-28', '2026-06-09 07:18:39'),
(2, 'Entrpreneurship camp', 'all student welcome to show up your innovation', '2026-06-27', '2026-06-09 07:30:32'),
(3, 'Welcome first year', 'lets welcome all fist year student', '2026-06-27', '2026-06-09 07:44:58'),
(4, 'Mzumbe Jogging ', 'welcome', '2026-06-30', '2026-06-09 08:11:09'),
(5, 'Uhuru Day', 'welcome you all', '2026-06-10', '2026-06-09 11:41:22');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `ticket_number` varchar(50) DEFAULT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `user_id`, `event_id`, `ticket_number`, `registered_at`) VALUES
(1, 1, 1, 'TKT-64349', '2026-06-09 07:19:06'),
(2, 4, 1, 'TKT-14022', '2026-06-09 07:22:55'),
(3, 4, 2, 'TKT-33946', '2026-06-09 07:31:42'),
(4, 1, 3, 'TKT-82924', '2026-06-09 07:45:42'),
(5, 1, 2, 'TKT-78005', '2026-06-09 08:09:02'),
(7, 1, 5, 'TKT-75769', '2026-06-09 11:42:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','admin') DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'kipapi', 'kipapi@gmail', '$2y$10$Q.MumgsKJNYEwlbfQHEeDOxERgt9gVLpJc0jr6rFJzqgGBJxVCAdi', 'student', '2026-06-08 20:40:09'),
(2, 'Admin', 'admin@gmail.com', '$2y$10$5n9STdLEpHDRAUVKUeTAWOwDeVNDPCjXceI6JxmWtvOq2wb2LLVVW', 'admin', '2026-06-08 20:48:04'),
(3, 'adolf james', 'adolf@gmail', '$2y$10$ehvfNEasfHG57Ktnkcbr6uDO57./gNdj9QnPfo90LyalztOTAa7/C', 'student', '2026-06-08 20:59:23'),
(4, 'msukuma mnyama', 'msukuma@gmail.com', '$2y$10$EDXHOEuL5bPItVs2aIlXNOPFPR.WTNcMpZ7LqDJAlWz2kvnmnWR3O', 'student', '2026-06-09 07:21:40'),
(5, 'silvano john', 'silvano@gmail', '$2y$10$5SgHffAZ6leoP8jFdlz18eBoM3ZK1xbHCsRFYr1T8KhSKtC2RXReK', 'student', '2026-06-09 11:36:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

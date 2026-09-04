-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 17, 2026 at 09:32 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `skinsense`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '12345678', '2026-05-14 04:10:32');

-- --------------------------------------------------------

--
-- Table structure for table `recommendations`
--

CREATE TABLE `recommendations` (
  `id` int NOT NULL,
  `skin_type` varchar(50) NOT NULL,
  `step` varchar(100) NOT NULL,
  `tip` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `recommendations`
--

INSERT INTO `recommendations` (`id`, `skin_type`, `step`, `tip`) VALUES
(1, 'oily', '🧼 Cleanser', 'Use a gel-based or foaming cleanser to control oil'),
(2, 'oily', '💧 Toner', 'Use salicylic acid toner to reduce excess sebum'),
(3, 'oily', '🌿 Moisturizer', 'Use oil-free lightweight moisturizer'),
(4, 'oily', '☀️ Sunscreen', 'Use matte-finish sunscreen SPF 30+'),
(5, 'dry', '🧼 Cleanser', 'Use a creamy hydrating cleanser'),
(6, 'dry', '💧 Toner', 'Use alcohol-free hydrating toner'),
(7, 'dry', '🌿 Moisturizer', 'Use rich cream with hyaluronic acid'),
(8, 'dry', '☀️ Sunscreen', 'Use moisturizing sunscreen SPF 30+'),
(9, 'normal', '🧼 Cleanser', 'Use a gentle balanced cleanser'),
(10, 'normal', '💧 Toner', 'Use a mild hydrating toner'),
(11, 'normal', '🌿 Moisturizer', 'Use a lightweight daily moisturizer'),
(12, 'normal', '☀️ Sunscreen', 'Use SPF 30+ sunscreen daily'),
(13, 'combination', '🧼 Cleanser', 'Use a gentle foaming cleanser'),
(14, 'combination', '💧 Toner', 'Use balancing toner for T-zone'),
(15, 'combination', '🌿 Moisturizer', 'Use gel on oily zones, cream on dry zones'),
(16, 'combination', '☀️ Sunscreen', 'Use balanced SPF 30+ sunscreen');

-- --------------------------------------------------------

--
-- Table structure for table `skin_analysis`
--

CREATE TABLE `skin_analysis` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `skin_type` varchar(50) DEFAULT NULL,
  `detected_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `skin_analysis`
--

INSERT INTO `skin_analysis` (`id`, `user_id`, `image_path`, `skin_type`, `detected_at`) VALUES
(1, 3, 'uploads/1778815888_3.jpg', 'combination', '2026-05-15 03:31:29'),
(2, 3, 'uploads/1778816545_3.jfif', 'combination', '2026-05-15 03:42:25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(3, 'nini', 'nini@gmail.com', '$2y$10$YtND03H/JW508B4ztEi1Ze/r3wviZLw14UBnJPGCk5zTPinN1qxui', '2026-05-15 03:30:54'),
(4, 'zack', 'zk@gmail.com', '$2y$10$hvCOlq9htIR6LWCEVWFpheyCCShPblBOwZLcJmpX48wJ/8r7LfKaC', '2026-05-15 06:36:06'),
(5, 'nurul alia', 'alia@gmail.com', '$2y$10$qtLBaAc8XXdr6DBHJMVQSOM.6p7HqHi4L.smY53qbTQUfdnijwK7K', '2026-05-17 07:00:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `recommendations`
--
ALTER TABLE `recommendations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `skin_analysis`
--
ALTER TABLE `skin_analysis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `recommendations`
--
ALTER TABLE `recommendations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `skin_analysis`
--
ALTER TABLE `skin_analysis`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `skin_analysis`
--
ALTER TABLE `skin_analysis`
  ADD CONSTRAINT `skin_analysis_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

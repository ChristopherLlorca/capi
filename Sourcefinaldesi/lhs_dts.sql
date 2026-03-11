-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2026 at 07:54 PM
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
-- Database: `lhs_dts`
--

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `student_id_fk` int(11) DEFAULT NULL,
  `tracking_number` varchar(50) NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `doc_type` varchar(50) NOT NULL,
  `student_name` varchar(50) NOT NULL,
  `grade_section` varchar(50) NOT NULL,
  `contact` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `from_school` varchar(100) NOT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL,
  `rejection_feedback` text DEFAULT NULL,
  `current_location` varchar(100) DEFAULT 'Registrar',
  `feedback` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `document_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `student_id_fk`, `tracking_number`, `student_id`, `doc_type`, `student_name`, `grade_section`, `contact`, `email`, `from_school`, `date_created`, `status`, `rejection_feedback`, `current_location`, `feedback`, `file_path`, `document_file`) VALUES
(57, NULL, 'LHS_639F9A80', '1222', 'Birth Certificate', 'Christopher Llorca', '12 - Ritchie', '09123456789', NULL, 'N/A', '2026-03-11 17:59:11', 'Pending', NULL, 'Registrar (Pending)', NULL, '1773248351_5.jpg', NULL),
(60, NULL, 'LHS_A2C11943', '1222', 'Birth Certificate', 'Christopher Llorca', '12 - Ritchie', '09123456789', NULL, 'N/A', '2026-03-11 18:01:46', 'Pending', NULL, 'Registrar (Pending)', NULL, '1773248506_wk1.jpg', NULL),
(62, NULL, 'LHS_79409950', '1222', 'Birth Certificate', 'Christopher Llorca', '12 - Ritchie', '09123456789', NULL, 'N/A', '2026-03-11 18:04:10', 'Pending', NULL, 'Registrar (Pending)', NULL, '1773248650_wk1.jpg', NULL),
(67, NULL, 'LHS_569FE918', '1222', 'Birth Certificate', 'Christopher Llorca', '12 - Ritchie', '09123456789', NULL, 'N/A', '2026-03-11 18:05:42', 'Pending', NULL, 'Registrar (Pending)', NULL, '1773248742_44.jpg', NULL),
(68, NULL, 'LHS_CAF98FF4', '1222', 'Report Card', 'Christopher Llorca', '12 - Ritchie', '09123456789', NULL, 'N/A', '2026-03-11 18:06:41', 'Pending', NULL, 'Registrar (Pending)', NULL, '1773248801_6142969373195439707.jpg', NULL),
(69, NULL, 'LHS_BD7B1ED8', '1', 'Birth Certificate', 'ong', '12 - Ritchie', '09123456789', NULL, 'N/A', '2026-03-11 18:21:22', 'Approved', NULL, 'Archive', NULL, '1773249682_5.jpg', NULL),
(70, NULL, 'LHS_B159860E', '1', 'Birth Certificate', 'ong', '12 - Ritchie', '09123456789', NULL, 'N/A', '2026-03-11 18:22:45', 'Pending', NULL, 'Registrar (Pending)', NULL, '1773249765_wk1.jpg', NULL),
(71, NULL, 'LHS_D9293B7B', '1', 'Birth Certificate', 'ong', '12 - Ritchie', '09123456789', NULL, 'N/A', '2026-03-11 18:24:00', 'Pending', NULL, 'Registrar (Pending)', NULL, '1773249840_55.jpg', NULL),
(72, NULL, 'LHS_F289FFD8', '1', 'Birth Certificate', 'ong', '12 - Ritchie', '09123456789', NULL, 'N/A', '2026-03-11 18:31:00', 'Out Going', NULL, 'Archive', NULL, '1773250260_wk1 (1).jpg', NULL),
(73, NULL, 'LHS_14A6485A', '1', 'Birth Certificate', 'ong', '12 - Ritchie', '09123456789', NULL, 'N/A', '2026-03-11 18:32:42', 'Out Going', NULL, 'Archive', NULL, '1773250362_6098083232625659126.jpg', NULL),
(77, NULL, 'LHS_7E6A46E5', '1', 'Birth Certificate', 'ong', '12 - Ritchie', '09123456789', '', 'N/A', '2026-03-11 18:54:28', 'Pending', NULL, 'Registrar (Pending)', NULL, '1773251668_55.jpg', NULL),
(88, NULL, 'LHS_D90386B9', '123', 'Birth Certificate', 'Christopher Llorca', '12 - Ritchie', '09123456789', 'llorcachristopher@gmail.com', 'N/A', '2026-03-11 19:05:59', 'Approved', NULL, 'Archive', NULL, '1773252359_44.jpg', NULL),
(90, NULL, 'LHS_AB76C5A1', '1331', 'Birth Certificate', 'Christopher Llorca', '12 - Ritchie', '09123456789', 'llorca588@gmail.com', 'N/A', '2026-03-11 19:32:36', 'Approved', NULL, 'Archive', NULL, '1773253956_5.jpg', NULL),
(92, NULL, 'LHS_55E0E2DB', '1331', 'Form 137', 'Christopher Conte Llorca', '12 - Ritchie', '09123456789', 'llorca588@gmail.com', 'N/A', '2026-03-11 19:50:47', 'Pending', NULL, 'Registrar (Pending)', NULL, '1773255047_5.jpg', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_requests`
--

CREATE TABLE `password_requests` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','resolved') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `middle_initial` varchar(5) NOT NULL,
  `age` int(11) NOT NULL,
  `date_created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `firstname`, `lastname`, `middle_initial`, `age`, `date_created`) VALUES
('1', 'ong', 'N/A', '', 0, '2026-03-11 18:31:00'),
('1222', 'Christopher', 'Llorca', '', 0, '2026-03-11 17:59:11'),
('123', 'Christopher', 'Llorca', '', 0, '2026-03-11 19:05:59'),
('1331', 'Christopher', 'Llorca', '', 0, '2026-03-11 19:32:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `lockout_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `role`, `status`, `created_at`, `profile_image`, `login_attempts`, `lockout_until`) VALUES
(29, 'admins', '$2y$10$Ar25313hudKgzONUl.GAWOviu/WvkRr2jhvryJeYbf.QCWWi3UKbK', 'admin', NULL, 'admin', 'active', '2026-03-07 18:38:04', NULL, 0, NULL),
(30, 'staffs', '$2y$10$tuLuVosHnkWmwbE9PTmTFeV5erAqHK7D1upN3WxqKoQFGhdajO47e', 'staff', NULL, 'staff', 'active', '2026-03-07 18:50:55', NULL, 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tracking_number` (`tracking_number`),
  ADD KEY `fk_student_record` (`student_id_fk`);

--
-- Indexes for table `password_requests`
--
ALTER TABLE `password_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `password_requests`
--
ALTER TABLE `password_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

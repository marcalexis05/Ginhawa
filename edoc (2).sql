-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 04, 2025 at 10:01 AM
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
-- Database: `edoc`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `aemail` varchar(255) NOT NULL,
  `apassword` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`aemail`, `apassword`) VALUES
('ginhawa@gmail.com', '$2y$10$yPiDT6Ez3oCXVdkiopOqQ.tyx6/N6OXAyOeUc3vwpDvubL24fjDWy');

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','read','processed') DEFAULT 'pending',
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_notifications`
--

INSERT INTO `admin_notifications` (`id`, `doctor_id`, `request_id`, `message`, `status`, `timestamp`) VALUES
(1, 1, 25, 'Dr. Test Doctor approved a session request from Marc Alexis Evangelista: Depression on 2025-04-08 from 08:00:00 to 08:30:00', 'pending', '2025-04-04 06:45:28'),
(2, 1, 26, 'Dr. Test Doctor approved a session request from Marc Alexis Evangelista: Try on 2025-04-09 from 08:00:00 to 08:30:00', 'pending', '2025-04-04 07:17:37'),
(3, 1, 28, 'Dr. Test Doctor approved a session request from Marc Alexis Evangelista: Wehn on 2025-04-11 from 08:00:00 to 08:30:00', 'pending', '2025-04-04 07:19:57'),
(4, 1, 29, 'Dr. Test Doctor approved a session request from Marc Alexis Evangelista: gggggg on 2025-04-10 from 11:00:00 to 11:30:00', 'pending', '2025-04-04 07:31:01'),
(5, 1, 30, 'Dr. Test Doctor approved a session request from Marc Alexis Evangelista: hhh on 2025-04-09 from 13:00:00 to 13:30:00', 'pending', '2025-04-04 07:38:58'),
(6, 5, 31, 'Dr. Marc Alexis approved a session request from marc evangelista: Depress on 2025-04-04 from 08:30:00 to 09:00:00', 'pending', '2025-04-04 08:31:05');

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `appoid` int(11) NOT NULL,
  `pid` int(10) DEFAULT NULL,
  `apponum` int(3) DEFAULT NULL,
  `scheduleid` int(10) DEFAULT NULL,
  `appodate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`appoid`, `pid`, `apponum`, `scheduleid`, `appodate`) VALUES
(1, 1, 1, 1, '2022-06-03'),
(2, 1, 2, 1, '2025-03-13'),
(4, 3, 3, 1, '2025-03-14'),
(5, 6, 1, 9, '2025-03-14'),
(9, 23, 1, 25, '2025-04-01'),
(10, 23, 1, 26, '2025-04-01'),
(11, 27, 1, 27, '2025-04-01'),
(31, 26, 2, 45, '2025-04-02'),
(39, 23, 1, 59, '2025-04-04'),
(40, 23, 1, 50, '2025-04-04'),
(41, 23, 1, 62, '2025-04-04'),
(44, 27, 2, 71, '2025-04-04'),
(45, 27, 1, 81, '2025-04-04'),
(46, 27, 1, 83, '2025-04-04');

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

CREATE TABLE `doctor` (
  `docid` int(11) NOT NULL,
  `docemail` varchar(255) DEFAULT NULL,
  `docname` varchar(255) DEFAULT NULL,
  `docpassword` varchar(255) DEFAULT NULL,
  `doctel` text DEFAULT NULL,
  `specialties` int(2) DEFAULT NULL,
  `ptid` varchar(10) DEFAULT NULL,
  `archived` tinyint(4) DEFAULT 0,
  `verification_code` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`docid`, `docemail`, `docname`, `docpassword`, `doctel`, `specialties`, `ptid`, `archived`, `verification_code`) VALUES
(1, 'doctor@ginhawa.com', 'Test Doctor', '$2y$10$i7l.98Bi7CL6SbM3ZeKQce6B.bAG7sykvZvXpJfhylwoo/81QTBMC', '', 1, '', 1, NULL),
(5, 'marcalexis_099@gmail.com', 'Marc Alexis', '$2y$10$LW4V9FmKDbo0YoXMYIycTu1cTtcN6WdbseAaGHWlbWSqejZqeZ3VC', '+639074301972', 14, 'PT002', 0, NULL),
(6, 'vjlamsenlamsen328@gmail.com', 'Layla', '$2y$10$R8fEebjlLFadN8SqAZpf0uHPxu/zzo1UR66ic1wgLBtPPA5Sktt22', '+639073121311', 1, 'PT003', 1, NULL),
(7, 'garciamarc1900@gmail.com', 'maria santiago', '$2y$10$FDLa/qVIffHIIq4zEEuBkOmQ1xHwMwcoeT1Sb9Z5o7ql7nvIgF61i', '+639604385093', 6, 'PT004', 0, NULL),
(8, 'royethnalang@gmail.com', 'Val', '$2y$10$AOf3reHbs9MYu/154LPMqO8J0Xywz86g4L8W6PPMyLegalMzFZnze', '+639075151412', 45, 'PT005', 0, NULL),
(11, 'billisutilitytracking@gmail.com', 'John Emmanuel', '$2y$10$3MAuHJXHy6pPas8Euek.kOdaKlf1PA1/gu2Hp5joptuVv9mMTBUlm', '+639058915131', 11, 'PT006', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `doctor_attendance`
--

CREATE TABLE `doctor_attendance` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `docemail` varchar(255) NOT NULL,
  `time_in` datetime DEFAULT NULL,
  `time_out` datetime DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_attendance`
--

INSERT INTO `doctor_attendance` (`id`, `doctor_id`, `docemail`, `time_in`, `time_out`, `date`) VALUES
(1, 5, 'marcalexis_099@gmail.com', '2025-04-01 14:37:37', '2025-04-01 14:42:51', '2025-04-01'),
(4, 5, 'marcalexis_099@gmail.com', '2025-04-01 15:09:48', NULL, '2025-04-01'),
(5, 5, 'marcalexis_099@gmail.com', '2025-04-01 15:11:18', NULL, '2025-04-01'),
(6, 5, 'marcalexis_099@gmail.com', '2025-04-01 15:15:51', NULL, '2025-04-01'),
(7, 5, 'marcalexis_099@gmail.com', '2025-04-01 15:17:38', NULL, '2025-04-01'),
(8, 5, 'marcalexis_099@gmail.com', '2025-04-01 15:20:21', NULL, '2025-04-01'),
(9, 5, 'marcalexis_099@gmail.com', '2025-04-01 15:22:38', '2025-04-01 19:34:39', '2025-04-01'),
(10, 5, 'marcalexis_099@gmail.com', '2025-04-01 15:26:53', '2025-04-01 15:29:41', '2025-04-01'),
(11, 5, 'marcalexis_099@gmail.com', '2025-04-01 15:29:56', '2025-04-01 15:30:02', '2025-04-01'),
(12, 5, 'marcalexis_099@gmail.com', '2025-04-01 15:30:43', '2025-04-01 15:30:47', '2025-04-01'),
(13, 5, 'marcalexis_099@gmail.com', '2025-04-01 17:40:33', '2025-04-01 17:45:48', '2025-04-01'),
(14, 5, 'marcalexis_099@gmail.com', '2025-04-01 13:32:09', NULL, '2025-04-01'),
(15, 5, 'marcalexis_099@gmail.com', '2025-04-01 13:34:38', NULL, '2025-04-01'),
(16, 5, 'marcalexis_099@gmail.com', '2025-04-01 16:18:52', '2025-04-01 23:17:50', '2025-04-01'),
(17, 5, 'marcalexis_099@gmail.com', '2025-04-01 17:18:21', '2025-04-01 23:21:45', '2025-04-01'),
(18, 5, 'marcalexis_099@gmail.com', '2025-04-01 23:30:13', '2025-04-01 23:30:28', '2025-04-01'),
(19, 5, 'marcalexis_099@gmail.com', '2025-04-01 23:57:32', '2025-04-02 00:53:01', '2025-04-01'),
(20, 5, 'marcalexis_099@gmail.com', '2025-04-02 00:53:10', '2025-04-02 01:22:53', '2025-04-02'),
(21, 5, 'marcalexis_099@gmail.com', '2025-04-02 01:31:40', NULL, '2025-04-02'),
(22, 5, 'marcalexis_099@gmail.com', '2025-04-02 09:24:25', '2025-04-02 09:24:30', '2025-04-02'),
(23, 5, 'marcalexis_099@gmail.com', '2025-04-02 10:10:40', NULL, '2025-04-02'),
(24, 5, 'marcalexis_099@gmail.com', '2025-04-02 11:07:16', NULL, '2025-04-02'),
(25, 5, 'marcalexis_099@gmail.com', '2025-04-02 12:19:24', '2025-04-02 14:14:52', '2025-04-02'),
(26, 5, 'marcalexis_099@gmail.com', '2025-04-02 14:15:32', '2025-04-02 17:16:30', '2025-04-02'),
(27, 5, 'marcalexis_099@gmail.com', '2025-04-02 20:25:09', '2025-04-02 23:33:39', '2025-04-02'),
(28, 5, 'marcalexis_099@gmail.com', '2025-04-02 23:50:54', NULL, '2025-04-02'),
(29, 5, 'marcalexis_099@gmail.com', '2025-04-02 23:54:37', '2025-04-03 00:37:52', '2025-04-02'),
(30, 5, 'marcalexis_099@gmail.com', '2025-04-03 01:00:10', NULL, '2025-04-03'),
(31, 5, 'marcalexis_099@gmail.com', '2025-04-03 01:29:25', NULL, '2025-04-03'),
(32, 5, 'marcalexis_099@gmail.com', '2025-04-03 01:35:03', NULL, '2025-04-03'),
(33, 5, 'marcalexis_099@gmail.com', '2025-04-03 02:42:17', NULL, '2025-04-03'),
(34, 5, 'marcalexis_099@gmail.com', '2025-04-03 03:56:57', NULL, '2025-04-03'),
(35, 5, 'marcalexis_099@gmail.com', '2025-04-03 03:58:38', '2025-04-03 03:58:40', '2025-04-03'),
(36, 5, 'marcalexis_099@gmail.com', '2025-04-03 03:58:50', NULL, '2025-04-03'),
(37, 5, 'marcalexis_099@gmail.com', '2025-04-03 04:04:55', '2025-04-03 04:05:11', '2025-04-03'),
(38, 5, 'marcalexis_099@gmail.com', '2025-04-03 04:11:49', NULL, '2025-04-03'),
(39, 5, 'marcalexis_099@gmail.com', '2025-04-03 04:12:48', '2025-04-03 04:12:54', '2025-04-03'),
(40, 5, 'marcalexis_099@gmail.com', '2025-04-03 05:03:04', '2025-04-03 05:40:16', '2025-04-03'),
(41, 5, 'marcalexis_099@gmail.com', '2025-04-03 05:45:05', '2025-04-03 05:48:21', '2025-04-03'),
(42, 5, 'marcalexis_099@gmail.com', '2025-04-03 06:02:28', '2025-04-03 06:02:39', '2025-04-03'),
(43, 5, 'marcalexis_099@gmail.com', '2025-04-03 06:03:40', NULL, '2025-04-03'),
(44, 5, 'marcalexis_099@gmail.com', '2025-04-04 00:14:05', '2025-04-04 00:14:44', '2025-04-04'),
(45, 5, 'marcalexis_099@gmail.com', '2025-04-04 00:28:28', '2025-04-04 00:29:28', '2025-04-04'),
(46, 1, 'doctor@ginhawa.com', '2025-04-04 03:46:09', '2025-04-04 03:52:02', '2025-04-04'),
(47, 1, 'doctor@ginhawa.com', '2025-04-04 04:51:53', '2025-04-04 05:11:30', '2025-04-04'),
(48, 1, 'doctor@ginhawa.com', '2025-04-04 05:51:34', '2025-04-04 06:27:21', '2025-04-04'),
(49, 1, 'doctor@ginhawa.com', '2025-04-04 06:44:17', NULL, '2025-04-04'),
(50, 1, 'doctor@ginhawa.com', '2025-04-04 07:33:10', '2025-04-04 07:39:01', '2025-04-04'),
(51, 1, 'doctor@ginhawa.com', '2025-04-04 07:47:21', '2025-04-04 08:04:36', '2025-04-04'),
(52, 5, 'marcalexis_099@gmail.com', '2025-04-04 08:13:09', '2025-04-04 08:32:03', '2025-04-04'),
(53, 5, 'marcalexis_099@gmail.com', '2025-04-04 08:32:25', NULL, '2025-04-04'),
(54, 5, 'marcalexis_099@gmail.com', '2025-04-04 13:19:44', '2025-04-04 14:34:56', '2025-04-04'),
(55, 5, 'marcalexis_099@gmail.com', '2025-04-04 14:35:33', '2025-04-04 14:58:22', '2025-04-04'),
(56, 11, 'billisutilitytracking@gmail.com', '2025-04-04 15:12:30', '2025-04-04 15:29:10', '2025-04-04'),
(57, 5, 'marcalexis_099@gmail.com', '2025-04-04 15:46:32', '2025-04-04 15:49:19', '2025-04-04');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires`, `created_at`) VALUES
(1, 'alexismarc066@gmail.com', '22afcc52f51914559e9754e7f241e8be09f9c5ca5ee20c093cf1de8c1739e008', '2025-03-30 05:27:39', '2025-03-30 02:27:39'),
(2, 'alexismarc066@gmail.com', 'ebb39dc93423b91c205f71634224ff53b36531062573338ae18608464d797c74', '2025-03-30 05:30:31', '2025-03-30 02:30:31'),
(3, 'alexismarc066@gmail.com', 'ffc2ff003341bf2532f768bb134ced3a7ffad510e95efda2828360598b0cf359', '2025-03-30 05:30:39', '2025-03-30 02:30:39'),
(5, 'alexismarc066@gmail.com', '689c63a8ed59aba16c8cecffceb75af0220d614927cf7a58c5855ade6c792fab', '2025-04-01 12:31:51', '2025-04-01 09:31:51');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `pid` int(11) NOT NULL,
  `pemail` varchar(255) DEFAULT NULL,
  `pname` varchar(255) DEFAULT NULL,
  `ppassword` varchar(255) DEFAULT NULL,
  `pclientid` varchar(15) DEFAULT NULL,
  `pdob` date DEFAULT NULL,
  `psex` enum('male','female','other') NOT NULL,
  `age` int(11) NOT NULL,
  `ptel` text DEFAULT NULL,
  `verification_code` varchar(6) DEFAULT NULL,
  `code_expiry` datetime DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`pid`, `pemail`, `pname`, `ppassword`, `pclientid`, `pdob`, `psex`, `age`, `ptel`, `verification_code`, `code_expiry`, `google_id`, `archived`) VALUES
(10, 'vjlamsenlamsen900@gmail.com', 'Marc Alexis Natividad Evangelista', '$2y$10$8.Cz92t/RY1sV3SApp5bSe1ckO282Vezh4fSMznhLzYRzqQUbJ.c2', 'CL256', '2005-01-05', 'male', 20, '+639073040705', NULL, NULL, NULL, 0),
(11, 'evangelistamarcalexis05@gmail.com', 'Val Javez Lamsen', '$2y$10$5LFQpPrx59Kl13FGAH15nOTeKrL7qBCAf/5flOAilYYCzx0UP0oWe', 'CL802', '2005-01-05', 'male', 20, '+639075715112', NULL, NULL, NULL, 0),
(12, 'vjlamsenlamsen238@gmail.com', 'Val Javez Lamsen', '$2y$10$E7MBtEO.Kt4h80haPU3zcuhtQadOZZEfmNj5YmXdgVdfDHeeCNB1.', 'CL003', '2005-01-05', 'male', 20, '+639074319031', NULL, NULL, NULL, 0),
(14, 'gene.tabios13@gmail.com', 'Marc Evangelista', '$2y$10$ILy8K2zy/4.yLCfYMDvO9eADl6X7fABNkF4ZDk1Dw1vP3I7UTegpa', 'CL333', '2007-04-02', 'male', 18, '+639079851513', '596375', '2025-03-14 06:41:00', NULL, 0),
(15, 'galdianojeraldg@gmail.com', 'Jerald Galdiano', '$2y$10$7.ddC8.kncTwf6CkXZOY4ONOE2LWVtfiJLC8.OJ0xQsoud5qkHiHi', 'CL803', '2007-03-08', 'male', 18, '+639954949299', NULL, NULL, NULL, 0),
(16, 'marcmasmela@gmail.com', 'Johnemmanuel Nalang', '$2y$10$hqs4u9BQ9kZ/H0YlVUA6GuIZgnL0J1.jJCEKIdadJq5vkyuAlQNBK', 'CL516', '2005-01-28', 'male', 20, '+639086753121', NULL, NULL, NULL, 0),
(17, 'marcalexis055@gmail.com', 'Marc Alexis Evangelista', '$2y$10$9/ab945t.2TB7cW/l/y8ouYA.ywNcLl9eHQKbMiPIKYj2IXTn9Vc2', 'CL686', '2007-04-01', 'male', 18, '+639075141213', '212428', '2025-04-01 05:51:31', NULL, 0),
(23, 'alexismarc066@gmail.com', 'Marc Alexis Evangelista', '$2y$10$dgCcQxnp6l5nwRXOT.Pjs.Rme6IBhz5z9BHiBuKJVqIE42EWcgn.C', 'CL787', '2007-04-01', 'male', 18, '+639079515141', NULL, NULL, NULL, 0),
(25, 'johnemmanuelnalang@gmail.com', 'Nalang, John Emmanuel P.', '$2y$10$321.IepL93PI4twbe5leOeovm.9DkHEDrwsHIeGfYox7M.8LT0Ju.', 'CL550', '2007-04-01', 'male', 18, '+639075751414', '913358', '2025-04-01 12:10:06', NULL, 0),
(26, 'agustin01262005@gmail.com', 'Agustin Khurt', '$2y$10$EluaZNHI0/rgi1tv9J..xeHM1pdcOH81RYstNKGeeSAL/FY8M65Xq', 'CL618', '2007-04-02', 'male', 18, '+639686764121', NULL, NULL, NULL, 0),
(27, 'marcevangelista85@gmail.com', 'marc evangelista', '$2y$10$GLcaGrUlX9YABDuRLoAr7.x55dOLFyrjgof62vLG2wcO04cRblpUO', 'CL218', '2005-01-05', 'male', 20, '+639075423134', NULL, NULL, NULL, 0),
(30, 'gene.tabios13@gmail.com', 'Tabios, Gene G.', NULL, 'CL890', NULL, 'male', 0, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `patient_requests`
--

CREATE TABLE `patient_requests` (
  `request_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `session_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `gmeet_request` tinyint(1) DEFAULT 0,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `rejection_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_requests`
--

INSERT INTO `patient_requests` (`request_id`, `patient_id`, `doctor_id`, `title`, `session_date`, `start_time`, `duration`, `end_time`, `gmeet_request`, `status`, `request_date`, `rejection_reason`) VALUES
(1, 27, 5, 'SANA', '2025-04-11', '09:00:00', 30, '00:00:09', 1, 'approved', '2025-04-04 04:48:07', NULL),
(2, 27, 5, 'MAA', '2025-04-11', '08:30:00', 30, '00:00:09', 1, 'rejected', '2025-04-04 04:53:32', NULL),
(3, 27, 5, 'MJGA', '2025-04-11', '13:00:00', 30, '00:00:13', 1, 'rejected', '2025-04-04 04:56:57', NULL),
(4, 27, 5, 'mkjA', '2025-04-09', '10:30:00', 30, '00:00:00', 1, 'rejected', '2025-04-04 05:10:18', NULL),
(5, 27, 5, 'loOGA', '2025-04-11', '10:00:00', 30, '00:00:10', 0, 'rejected', '2025-04-04 05:12:16', NULL),
(6, 27, 5, 'gagagag', '2025-04-11', '14:30:00', 30, '15:00:00', 1, 'rejected', '2025-04-04 05:39:31', NULL),
(7, 27, 5, 'gagagagrq', '2025-04-10', '09:30:00', 30, '10:00:00', 1, 'approved', '2025-04-04 05:58:48', NULL),
(9, 23, 5, 'LGAOGA', '2025-04-11', '10:00:00', 30, '10:30:00', 1, 'approved', '2025-04-04 06:27:20', NULL),
(10, 23, 5, 'gagarq', '2025-04-10', '11:00:00', 30, '11:30:00', 1, 'rejected', '2025-04-04 06:28:10', 'Schedule Conflict');

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `scheduleid` int(11) NOT NULL,
  `docid` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `scheduledate` date DEFAULT NULL,
  `start_time` time NOT NULL,
  `duration` enum('30','60','90','120') NOT NULL DEFAULT '60',
  `end_time` time NOT NULL,
  `gmeet_link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`scheduleid`, `docid`, `title`, `scheduledate`, `start_time`, `duration`, `end_time`, `gmeet_link`) VALUES
(50, '5', 'Depression', '2025-04-04', '14:30:00', '60', '15:30:00', 'https://meet.google.com/nes-pnkm-mmn'),
(69, '6', 'eet', '2025-03-31', '11:23:00', '60', '00:00:00', 'https://meet.google.com/nes-pnkm-mmn'),
(71, '5', 'Depress', '2025-04-04', '08:30:00', '30', '09:00:00', 'https://meet.google.com/ycw-qezb-rap'),
(75, '5', 'GGG', '2025-04-11', '15:30:00', '', '16:00:00', NULL),
(76, '5', 'GGGGG', '2025-04-04', '16:00:00', '60', '16:30:00', NULL),
(88, '5', 'LGAOGA', '2025-04-11', '10:00:00', '60', '10:30:00', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `session_requests`
--

CREATE TABLE `session_requests` (
  `request_id` int(11) NOT NULL,
  `docid` int(11) DEFAULT NULL,
  `session_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `session_requests`
--

INSERT INTO `session_requests` (`request_id`, `docid`, `session_date`, `start_time`, `duration`, `end_time`, `status`, `request_date`, `title`) VALUES
(1, 5, '2025-03-30', NULL, NULL, NULL, 'rejected', '2025-03-30 02:08:48', ''),
(2, 5, '2025-03-31', NULL, NULL, NULL, '', '2025-03-30 02:12:45', ''),
(3, 5, '2025-03-30', NULL, NULL, NULL, '', '2025-03-30 02:22:01', ''),
(4, 6, '2025-03-31', NULL, NULL, NULL, '', '2025-03-30 02:23:29', ''),
(5, 5, '2025-03-31', NULL, NULL, NULL, '', '2025-03-30 03:07:24', ''),
(13, 5, '2025-04-04', '15:30:00', 30, '16:00:00', 'rejected', '2025-04-04 00:47:30', 'Anxious'),
(14, 5, '2025-04-04', '15:00:00', 30, '15:30:00', '', '2025-04-04 01:03:58', 'Addict'),
(15, 5, '2025-04-04', '15:30:00', 30, '16:00:00', '', '2025-04-04 01:12:37', 'Cocaine'),
(16, 5, '2025-04-05', '15:30:00', 30, '16:00:00', 'rejected', '2025-04-04 01:22:04', 'Mandown'),
(17, 5, '2025-04-04', '16:00:00', 30, '16:30:00', 'rejected', '2025-04-04 01:32:26', 'Sad'),
(18, 5, '2025-04-11', '09:00:00', 30, '09:30:00', 'rejected', '2025-04-04 01:34:29', 'GG'),
(19, 5, '2025-04-11', '13:00:00', 30, NULL, 'rejected', '2025-04-04 01:39:06', 'GG'),
(20, 5, '2025-04-10', '15:00:00', 30, NULL, 'rejected', '2025-04-04 01:45:15', 'GG'),
(21, 5, '2025-04-04', '15:00:00', 30, NULL, 'rejected', '2025-04-04 01:46:46', 'gg'),
(22, 5, '2025-04-11', '16:00:00', 30, NULL, 'rejected', '2025-04-04 01:51:02', 'GG'),
(23, 5, '2025-04-05', '13:00:00', 30, NULL, 'rejected', '2025-04-04 01:53:04', 'GG'),
(24, 5, '2025-04-11', '15:00:00', 30, NULL, 'rejected', '2025-04-04 02:02:01', 'SAD'),
(25, 5, '2025-04-11', '15:00:00', 30, '15:30:00', '', '2025-04-04 02:06:12', 'GGG'),
(26, 5, '2025-04-10', '15:00:00', 30, '15:30:00', 'rejected', '2025-04-04 02:09:26', 'ZZZ'),
(27, 5, '2025-04-11', '15:30:00', 30, '16:00:00', '', '2025-04-04 02:43:00', 'GGG'),
(28, 5, '2025-04-09', '08:00:00', 60, '00:00:09', 'rejected', '2025-04-04 03:52:36', 'Anxious'),
(29, 5, '2025-04-09', '11:30:00', 30, '00:00:12', 'rejected', '2025-04-04 04:05:05', 'LONGKOT'),
(30, 5, '2025-04-11', '13:30:00', 30, '00:00:14', 'rejected', '2025-04-04 04:40:23', 'ANGRY');

-- --------------------------------------------------------

--
-- Table structure for table `specialties`
--

CREATE TABLE `specialties` (
  `id` int(2) NOT NULL,
  `sname` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `specialties`
--

INSERT INTO `specialties` (`id`, `sname`) VALUES
(1, 'Accident and emergency medicine'),
(2, 'Allergology'),
(3, 'Anaesthetics'),
(4, 'Biological hematology'),
(5, 'Cardiology'),
(6, 'Child psychiatry'),
(7, 'Clinical biology'),
(8, 'Clinical chemistry'),
(9, 'Clinical neurophysiology'),
(10, 'Clinical radiology'),
(11, 'Dental, oral and maxillo-facial surgery'),
(12, 'Dermato-venerology'),
(13, 'Dermatology'),
(14, 'Endocrinology'),
(15, 'Gastro-enterologic surgery'),
(16, 'Gastroenterology'),
(17, 'General hematology'),
(18, 'General Practice'),
(19, 'General surgery'),
(20, 'Geriatrics'),
(21, 'Immunology'),
(22, 'Infectious diseases'),
(23, 'Internal medicine'),
(24, 'Laboratory medicine'),
(25, 'Maxillo-facial surgery'),
(26, 'Microbiology'),
(27, 'Nephrology'),
(28, 'Neuro-psychiatry'),
(29, 'Neurology'),
(30, 'Neurosurgery'),
(31, 'Nuclear medicine'),
(32, 'Obstetrics and gynecology'),
(33, 'Occupational medicine'),
(34, 'Ophthalmology'),
(35, 'Orthopaedics'),
(36, 'Otorhinolaryngology'),
(37, 'Paediatric surgery'),
(38, 'Paediatrics'),
(39, 'Pathology'),
(40, 'Pharmacology'),
(41, 'Physical medicine and rehabilitation'),
(42, 'Plastic surgery'),
(43, 'Podiatric Medicine'),
(44, 'Podiatric Surgery'),
(45, 'Psychiatry'),
(46, 'Public health and Preventive Medicine'),
(47, 'Radiology'),
(48, 'Radiotherapy'),
(49, 'Respiratory medicine'),
(50, 'Rheumatology'),
(51, 'Stomatology'),
(52, 'Thoracic surgery'),
(53, 'Tropical medicine'),
(54, 'Urology'),
(55, 'Vascular surgery'),
(56, 'Venereology');

-- --------------------------------------------------------

--
-- Table structure for table `webuser`
--

CREATE TABLE `webuser` (
  `email` varchar(255) NOT NULL,
  `usertype` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `webuser`
--

INSERT INTO `webuser` (`email`, `usertype`) VALUES
('admin1@ginhawa.com', 'a'),
('agustin01262005@gmail.com', 'p'),
('alexismarc066@gmail.com', 'p'),
('billisutilitytracking@gmail.com', 'd'),
('doctor@ginhawa.com', 'd'),
('evangelistamarcalexis05@gmail.com', 'p'),
('galdianojeraldg@gmail.com', 'p'),
('garciamarc1900@gmail.com', 'd'),
('gene.tabios13@gmail.com', 'p'),
('genetabios@gmail.com', 'p'),
('ginhawa123@gmail.com', 'a'),
('ginhawa@gmail.com', 'a'),
('johnemmanuelnalang@gmail.com', 'p'),
('marcalexis05@gmail.com', 'p'),
('marcalexis99@gmail.com', 'p'),
('marcalexis@gmail.com', 'p'),
('marcalexis_099@gmail.com', 'd'),
('marcevangelista85@gmail.com', 'p'),
('marcjustine@gmail.com', 'p'),
('marcmasmela@gmail.com', 'p'),
('patient@ginhawa.com', 'p'),
('royethnalang@gmail.com', 'd'),
('valgene08@gmail.com', 'p'),
('val_lamsen89@gmail.com', 'p'),
('vjlamsenlamsen18@gmail.com', 'p'),
('vjlamsenlamsen238@gmail.com', 'p'),
('vjlamsenlamsen280@gmail.com', 'p'),
('vjlamsenlamsen28@gmail.com', 'p'),
('vjlamsenlamsen29@gmail.com', 'p'),
('vjlamsenlamsen300@gmail.com', 'p'),
('vjlamsenlamsen308@gmail.com', 'p'),
('vjlamsenlamsen328@gmail.com', 'd'),
('vjlamsenlamsen38@gmail.com', 'p'),
('vjlamsenlamsen398@gmail.com', 'p'),
('vjlamsenlamsen900@gmail.com', 'p');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`aemail`);

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appoid`),
  ADD KEY `pid` (`pid`),
  ADD KEY `scheduleid` (`scheduleid`);

--
-- Indexes for table `doctor`
--
ALTER TABLE `doctor`
  ADD PRIMARY KEY (`docid`),
  ADD UNIQUE KEY `ptid` (`ptid`),
  ADD KEY `specialties` (`specialties`);

--
-- Indexes for table `doctor_attendance`
--
ALTER TABLE `doctor_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`pid`);

--
-- Indexes for table `patient_requests`
--
ALTER TABLE `patient_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`scheduleid`),
  ADD KEY `docid` (`docid`);

--
-- Indexes for table `session_requests`
--
ALTER TABLE `session_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `docid` (`docid`);

--
-- Indexes for table `specialties`
--
ALTER TABLE `specialties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `webuser`
--
ALTER TABLE `webuser`
  ADD PRIMARY KEY (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appoid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `doctor`
--
ALTER TABLE `doctor`
  MODIFY `docid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `doctor_attendance`
--
ALTER TABLE `doctor_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `patient_requests`
--
ALTER TABLE `patient_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `scheduleid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `session_requests`
--
ALTER TABLE `session_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `doctor_attendance`
--
ALTER TABLE `doctor_attendance`
  ADD CONSTRAINT `doctor_attendance_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctor` (`docid`);

--
-- Constraints for table `patient_requests`
--
ALTER TABLE `patient_requests`
  ADD CONSTRAINT `patient_requests_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`pid`),
  ADD CONSTRAINT `patient_requests_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctor` (`docid`);

--
-- Constraints for table `session_requests`
--
ALTER TABLE `session_requests`
  ADD CONSTRAINT `session_requests_ibfk_1` FOREIGN KEY (`docid`) REFERENCES `doctor` (`docid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

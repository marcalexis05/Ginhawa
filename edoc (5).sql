-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 03, 2025 at 12:51 AM
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
(37, 23, 1, 57, '2025-04-03'),
(38, 27, 2, 58, '2025-04-03');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `sender_type` enum('patient','doctor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `ptid` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`docid`, `docemail`, `docname`, `docpassword`, `doctel`, `specialties`, `ptid`) VALUES
(1, 'doctor@ginhawa.com', 'Test Doctor', '123', '0110000000', 1, NULL),
(5, 'marcalexis_099@gmail.com', 'Marc Alexis', '$2y$10$LW4V9FmKDbo0YoXMYIycTu1cTtcN6WdbseAaGHWlbWSqejZqeZ3VC', '+639074301972', 14, 'PT002'),
(6, 'vjlamsenlamsen328@gmail.com', 'Layla', '$2y$10$R8fEebjlLFadN8SqAZpf0uHPxu/zzo1UR66ic1wgLBtPPA5Sktt22', '+639073121311', 1, 'PT003'),
(7, 'garciamarc1900@gmail.com', 'maria santiago', '$2y$10$FDLa/qVIffHIIq4zEEuBkOmQ1xHwMwcoeT1Sb9Z5o7ql7nvIgF61i', '+639604385093', 6, 'PT004'),
(8, 'royethnalang@gmail.com', 'Val', '$2y$10$AOf3reHbs9MYu/154LPMqO8J0Xywz86g4L8W6PPMyLegalMzFZnze', '+639075151412', 45, 'PT005');

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
(43, 5, 'marcalexis_099@gmail.com', '2025-04-03 06:03:40', NULL, '2025-04-03');

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
  `session_date` date NOT NULL,
  `start_time` time NOT NULL,
  `duration` enum('30','60','90','120') NOT NULL DEFAULT '60',
  `end_time` time NOT NULL,
  `request_date` datetime NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_requests`
--

INSERT INTO `patient_requests` (`request_id`, `patient_id`, `doctor_id`, `title`, `session_date`, `start_time`, `duration`, `end_time`, `request_date`, `status`) VALUES
(1, 23, 5, 'Anxiety', '2025-04-02', '15:00:00', '30', '15:30:00', '2025-04-02 13:15:22', 'approved'),
(2, 27, 5, 'Depression', '2025-04-03', '14:30:00', '30', '15:00:00', '2025-04-02 20:24:02', ''),
(3, 27, 5, 'Anxiety', '2025-04-03', '15:30:00', '30', '16:00:00', '2025-04-02 20:30:29', 'approved'),
(4, 23, 5, 'Depression', '2025-04-03', '13:00:00', '30', '13:30:00', '2025-04-02 20:44:04', 'approved'),
(5, 27, 5, 'Anxious', '2025-04-04', '16:00:00', '30', '16:30:00', '2025-04-02 20:49:41', 'approved'),
(6, 27, 5, 'Anxiety', '2025-04-04', '15:00:00', '30', '15:30:00', '2025-04-02 21:23:49', 'approved'),
(7, 27, 5, 'anxiety', '2025-04-05', '15:30:00', '30', '16:00:00', '2025-04-02 21:26:13', 'approved'),
(8, 26, 5, 'Anxiety', '2025-04-02', '14:00:00', '120', '16:00:00', '2025-04-02 23:35:28', 'rejected'),
(9, 26, 6, 'Anxiety', '2025-04-02', '14:00:00', '30', '14:30:00', '2025-04-02 23:43:35', 'pending'),
(10, 26, 5, 'Anxious', '2025-04-02', '08:00:00', '30', '08:30:00', '2025-04-02 23:51:26', 'rejected'),
(11, 26, 5, 'Anxiety', '2025-04-03', '15:00:00', '30', '15:30:00', '2025-04-03 00:37:24', 'approved'),
(12, 26, 5, 'Anxiety', '2025-04-05', '14:30:00', '60', '03:00:00', '0000-00-00 00:00:00', 'approved'),
(13, 26, 5, 'Depression', '2025-04-06', '14:30:00', '30', '15:00:00', '2025-04-03 01:29:13', 'approved'),
(14, 26, 5, 'Anxiety', '2025-04-05', '15:00:00', '30', '15:30:00', '2025-04-03 01:36:17', 'approved'),
(15, 23, 8, 'Anxiety', '2025-04-07', '14:30:00', '30', '15:00:00', '2025-04-03 03:48:43', 'pending'),
(16, 26, 5, 'Depression', '2025-04-04', '14:30:00', '60', '15:30:00', '2025-04-03 04:02:39', 'approved'),
(17, 27, 5, 'Addict', '2025-04-04', '14:00:00', '30', '14:30:00', '2025-04-03 04:04:29', ''),
(18, 27, 5, 'Depress', '2025-04-04', '16:00:00', '30', '16:30:00', '2025-04-03 04:12:38', 'approved'),
(19, 23, 5, 'Addicted', '2025-04-03', '12:30:00', '30', '13:00:00', '2025-04-03 05:17:48', 'approved'),
(20, 27, 5, 'GG', '2025-04-03', '09:00:00', '30', '09:30:00', '2025-04-03 06:03:22', 'approved');

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
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`scheduleid`, `docid`, `title`, `scheduledate`, `start_time`, `duration`, `end_time`) VALUES
(50, '5', 'Depression', '2025-04-04', '14:30:00', '60', '15:30:00'),
(57, '5', 'Addicted', '2025-04-03', '12:30:00', '30', '13:00:00'),
(58, '5', 'GG', '2025-04-03', '09:00:00', '30', '09:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `session_requests`
--

CREATE TABLE `session_requests` (
  `request_id` int(11) NOT NULL,
  `docid` int(11) DEFAULT NULL,
  `num_sessions` int(11) DEFAULT NULL,
  `session_date` date DEFAULT NULL,
  `session_time` time DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `session_requests`
--

INSERT INTO `session_requests` (`request_id`, `docid`, `num_sessions`, `session_date`, `session_time`, `status`, `request_date`, `title`) VALUES
(1, 5, 5, '2025-03-30', '11:08:00', 'rejected', '2025-03-30 02:08:48', ''),
(2, 5, 5, '2025-03-31', '10:14:00', 'approved', '2025-03-30 02:12:45', ''),
(3, 5, 5, '2025-03-30', '11:21:00', 'approved', '2025-03-30 02:22:01', ''),
(4, 6, 2, '2025-03-31', '11:23:00', 'approved', '2025-03-30 02:23:29', ''),
(5, 5, 2, '2025-03-31', '12:07:00', 'approved', '2025-03-30 03:07:24', '');

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
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appoid`),
  ADD KEY `pid` (`pid`),
  ADD KEY `scheduleid` (`scheduleid`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`,`receiver_id`);

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
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appoid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctor`
--
ALTER TABLE `doctor`
  MODIFY `docid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `doctor_attendance`
--
ALTER TABLE `doctor_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

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
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `scheduleid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `session_requests`
--
ALTER TABLE `session_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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

-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 06, 2026 at 11:43 PM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u601734414_sk_federation`
--

-- --------------------------------------------------------

--
-- Table structure for table `barangay`
--

CREATE TABLE `barangay` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barangay`
--

INSERT INTO `barangay` (`id`, `name`, `img`, `description`, `created_at`) VALUES
(1, 'Anilao', NULL, NULL, '2026-03-23 05:28:47'),
(2, 'Atlag', NULL, NULL, '2026-03-23 05:28:47'),
(3, 'Babatnin', NULL, NULL, '2026-03-23 05:28:47'),
(4, 'Bagna', NULL, NULL, '2026-03-23 05:28:47'),
(5, 'Bagong Bayan', NULL, NULL, '2026-03-23 05:28:47'),
(6, 'Balayong', NULL, NULL, '2026-03-23 05:28:47'),
(7, 'Balite', NULL, NULL, '2026-03-23 05:28:47'),
(8, 'Bangkal', NULL, NULL, '2026-03-23 05:28:47'),
(9, 'Barihan', NULL, NULL, '2026-03-23 05:28:47'),
(10, 'Bulihan', NULL, NULL, '2026-03-23 05:28:47'),
(11, 'Bungahan', NULL, NULL, '2026-03-23 05:28:47'),
(12, 'Caingin', NULL, NULL, '2026-03-23 05:28:47'),
(13, 'Calero', NULL, NULL, '2026-03-23 05:28:47'),
(14, 'Caliligawan', NULL, NULL, '2026-03-23 05:28:47'),
(15, 'Canalate', NULL, NULL, '2026-03-23 05:28:47'),
(16, 'Caniogan', NULL, NULL, '2026-03-23 05:28:47'),
(17, 'Catmon', NULL, NULL, '2026-03-23 05:28:47'),
(18, 'Cofradia', NULL, NULL, '2026-03-23 05:28:47'),
(19, 'Dakila', NULL, NULL, '2026-03-23 05:28:47'),
(20, 'Guinhawa', NULL, NULL, '2026-03-23 05:28:47'),
(21, 'Ligas', NULL, NULL, '2026-03-23 05:28:47'),
(22, 'Liang', NULL, NULL, '2026-03-23 05:28:47'),
(23, 'Longos', NULL, NULL, '2026-03-23 05:28:47'),
(24, 'Look 1st', NULL, NULL, '2026-03-23 05:28:47'),
(25, 'Look 2nd', NULL, NULL, '2026-03-23 05:28:47'),
(26, 'Lugam', NULL, NULL, '2026-03-23 05:28:47'),
(27, 'Mabolo', NULL, NULL, '2026-03-23 05:28:47'),
(28, 'Mambog', NULL, NULL, '2026-03-23 05:28:47'),
(29, 'Masile', NULL, NULL, '2026-03-23 05:28:47'),
(30, 'Matimbo', NULL, NULL, '2026-03-23 05:28:47'),
(31, 'Mojon', NULL, NULL, '2026-03-23 05:28:47'),
(32, 'Namayan', NULL, NULL, '2026-03-23 05:28:47'),
(33, 'Niugan', NULL, NULL, '2026-03-23 05:28:47'),
(34, 'Pamarawan', NULL, NULL, '2026-03-23 05:28:47'),
(35, 'Panasahan', NULL, NULL, '2026-03-23 05:28:47'),
(36, 'Pinagbakahan', NULL, NULL, '2026-03-23 05:28:47'),
(37, 'San Agustin', NULL, NULL, '2026-03-23 05:28:47'),
(38, 'San Gabriel', NULL, NULL, '2026-03-23 05:28:47'),
(39, 'San Juan', NULL, NULL, '2026-03-23 05:28:47'),
(40, 'San Pablo', NULL, NULL, '2026-03-23 05:28:47'),
(41, 'San Vicente', NULL, NULL, '2026-03-23 05:28:47'),
(42, 'Santiago', NULL, NULL, '2026-03-23 05:28:47'),
(43, 'Santor', NULL, NULL, '2026-03-23 05:28:47'),
(44, 'Santisima Trinidad', NULL, NULL, '2026-03-23 05:28:47'),
(45, 'Sto. Cristo', NULL, NULL, '2026-03-23 05:28:47'),
(46, 'Sto. Niño', NULL, NULL, '2026-03-23 05:28:47'),
(47, 'Santo Rosario', NULL, NULL, '2026-03-23 05:28:47'),
(48, 'Sumapang Bata', NULL, NULL, '2026-03-23 05:28:47'),
(49, 'Sumapang Matanda', NULL, NULL, '2026-03-23 05:28:47'),
(50, 'Taal', NULL, NULL, '2026-03-23 05:28:47'),
(51, 'Tikay', NULL, NULL, '2026-03-23 05:28:47');

-- --------------------------------------------------------

--
-- Table structure for table `barangays`
--

CREATE TABLE `barangays` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `img` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barangays`
--

INSERT INTO `barangays` (`id`, `name`, `img`, `description`, `updated_at`) VALUES
(1, 'Anilao', 'anilao.png', 'Anilao is known for its beauty', NULL),
(2, 'Atlag', 'atlag.png', 'Atlag hosts the annual town fiesta every May.', NULL),
(3, 'Babatnin', 'babatnin.png', 'A peaceful residential area with a strong community spirit.', NULL),
(4, 'Tikay', 'tikay.png', 'Tikay is the commercial hub of the municipality.', NULL),
(9, 'Cofradia', '', NULL, NULL),
(10, 'Lugam', '', NULL, NULL),
(11, 'Bungahan', '', NULL, NULL),
(12, 'Taal', '', NULL, NULL),
(13, 'Santo Rosario', '', NULL, NULL),
(14, 'Caingin', '', NULL, NULL),
(15, 'Santisima Trinidad', '', NULL, NULL),
(16, 'Santiago', '', NULL, NULL),
(17, 'San Vicente', '', NULL, NULL),
(18, 'San Juan', '', NULL, NULL),
(19, 'Matimbo', '', NULL, NULL),
(20, 'San Gabriel', '', NULL, NULL),
(21, 'Panasahan', '', NULL, NULL),
(22, 'Look 2nd', '', NULL, NULL),
(23, 'Mojon', '', NULL, NULL),
(24, 'Masile', '', NULL, NULL),
(25, 'Ligas', '', NULL, NULL),
(26, 'Liang', '', NULL, NULL),
(27, 'Guinhawa', '', NULL, NULL),
(28, 'Catmon', '', NULL, NULL),
(29, 'Calero', '', NULL, NULL),
(30, 'Bangkal', '', NULL, NULL),
(31, 'Sumapang Matanda', '', NULL, NULL),
(32, 'Sumapang Bata', '', NULL, NULL),
(33, 'Santor', '', NULL, NULL),
(34, 'San Agustin', '', NULL, NULL),
(35, 'Pinagbakahan', '', NULL, NULL),
(36, 'Niugan', '', NULL, NULL),
(37, 'Namayan', '', NULL, NULL),
(38, 'Mabolo', '', NULL, NULL),
(39, 'Look 1st', '', NULL, NULL),
(40, 'Longos', '', NULL, NULL),
(41, 'Caniogan', '', NULL, NULL),
(42, 'Canalate', '', NULL, NULL),
(43, 'Caliligawan', '', NULL, NULL),
(44, 'Bulihan', '', NULL, NULL),
(45, 'Balite', '', NULL, NULL),
(46, 'Balayong', '', NULL, NULL),
(47, 'Bagong Bayan', '', NULL, NULL),
(48, 'Bagna', '', NULL, NULL),
(49, 'Barihan', '', NULL, NULL),
(50, 'Dakila', '', NULL, NULL),
(51, 'Mambog', '', NULL, NULL),
(52, 'Pamarawan', '', NULL, NULL),
(53, 'San Pablo', '', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `barangay_officers`
--

CREATE TABLE `barangay_officers` (
  `id` int(11) NOT NULL,
  `barangay_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `role` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barangay_officers`
--

INSERT INTO `barangay_officers` (`id`, `barangay_id`, `name`, `role`, `image`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Aecee Viel Evangelista', 'SK Chairperson', 'images/officers/aecee.jpg', 1, '2026-01-31 18:03:50', '2026-01-31 18:03:50'),
(2, 1, 'Yuan Sumera Cruz', 'SK Kagawad', 'images/officers/yuan.jpg', 2, '2026-01-31 18:03:50', '2026-01-31 18:03:50'),
(3, 1, 'Gean Borlongan', 'SK Kagawad', 'images/officers/gean.jpg', 3, '2026-01-31 18:03:50', '2026-01-31 18:03:50'),
(4, 1, 'Frances Karyle Feliciano', 'SK Kagawad', 'images/officers/frances.jpg', 4, '2026-01-31 18:03:50', '2026-01-31 18:03:50'),
(5, 1, 'Shaina David', 'SK Kagawad', 'images/officers/shaina.jpg', 5, '2026-01-31 18:03:50', '2026-01-31 18:03:50'),
(6, 1, 'John Mark Guanterro', 'SK Kagawad', 'images/officers/john.jpg', 6, '2026-01-31 18:03:50', '2026-01-31 18:03:50'),
(7, 1, 'Sairen Jade Dumondon', 'SK Kagawad', 'images/officers/sairen.jpg', 7, '2026-01-31 18:03:50', '2026-01-31 18:03:50');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `barangay` varchar(100) NOT NULL,
  `venue` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('Proposed','approved','Ongoing','Completed') DEFAULT 'Proposed',
  `submitted_at` datetime DEFAULT current_timestamp(),
  `approved_at` datetime DEFAULT NULL,
  `barangay_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `barangay`, `venue`, `event_date`, `description`, `image`, `status`, `submitted_at`, `approved_at`, `barangay_id`) VALUES
(1, 'SK Youth Leadership Summit 2025', '', 'Anilao Covered Court', '2025-06-15', 'A 3-day training for SK officers on leadership, governance, and project management.', 'summit.jpg', 'approved', '2025-11-12 08:12:35', NULL, 1),
(2, 'Clean & Green River Cleanup', '', 'Anilao Riverside', '2025-07-20', 'Community-driven cleanup of Anilao River with tree planting and waste segregation.', 'cleanup.jpg', 'approved', '2025-11-12 08:12:35', NULL, 1),
(3, 'Anilao SK Sports Fest', '', 'Atlag Gymnasium', '2025-08-10', 'Annual basketball and volleyball tournament for youth.', 'sports.jpg', 'approved', '2025-11-12 08:12:35', NULL, 1),
(4, 'Sang-Race', 'Cofradia', 'Barangay Hall', '2025-11-27', 'Race', 'images/events/event_1_1763177660_6917f4bca6018.jpg', '', '2025-11-15 03:34:20', NULL, 9);

-- --------------------------------------------------------

--
-- Table structure for table `officers`
--

CREATE TABLE `officers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `barangay_id` int(11) NOT NULL,
  `role` varchar(100) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `image` varchar(100) DEFAULT 'default-officer.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `officers`
--

INSERT INTO `officers` (`id`, `name`, `barangay_id`, `role`, `sort_order`, `image`) VALUES
(1, 'Aecee Viel Evangelista', 1, 'SK Chairperson', 1, 'aecee.jpg'),
(2, 'Yuan Sumera Cruz', 1, 'SK Kagawad', 2, 'yuan.jpg'),
(3, 'Gean Borlongan', 1, 'SK Kagawad', 3, 'gean.jpg'),
(4, 'Frances Karyle Feliciano', 1, 'SK Kagawad', 4, 'frances.jpg'),
(5, 'Shaina David', 1, 'SK Kagawad', 5, 'shaina.jpg'),
(6, 'John Mark Guanterro', 1, 'SK Kagawad', 6, 'john.jpg'),
(7, 'Sairen Jade Dumondon', 1, 'SK Kagawad', 7, 'sairen.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `barangay_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `submitted_by` int(11) DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp(),
  `approved_at` datetime DEFAULT NULL,
  `budget` decimal(12,2) DEFAULT 0.00,
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `barangay_id`, `title`, `description`, `image`, `status`, `submitted_by`, `submitted_at`, `approved_at`, `budget`, `file_path`) VALUES
(1, 1, 'SK Youth Leadership Training', 'A 3‑day intensive training for SK officers on leadership, governance and project management. 50+ participants every year.', 'caravan.jpg', 'approved', 1, '2025-11-12 08:34:09', '2026-03-22 14:48:11', 1000000.00, NULL),
(2, 1, 'Monthly Clean‑Up Drive', 'River and street cleanup with waste segregation and tree planting. 200+ volunteers every month.', 'cleanup.jpg', 'pending', 1, '2025-11-12 08:34:09', NULL, 0.00, NULL),
(3, 1, 'Digital Literacy Program', 'Free 3‑month computer & internet course for out‑of‑school youth, ending with a certification.', 'digital.jpg', 'pending', 1, '2025-11-12 08:34:09', NULL, 0.00, NULL),
(12, 9, 'Free Board Exam', 'free exam', 'board.jpg', 'approved', 1, '2025-11-14 14:57:16', '2026-03-22 14:48:11', 0.00, NULL),
(13, 9, 'Clean Up Drive', 'Clean up drive for flood control', 'uploads/projects/project_1_1763187926_9275b5841b285ddf.jpg', 'approved', 1, '2025-11-15 06:25:26', '2026-03-22 14:48:11', 0.00, NULL),
(14, 9, 'Bayanihan', 'Bayanihan', 'uploads/projects/project_1_1774190074_c86e57beb47b44a6.jpg', 'approved', 1, '2026-03-22 14:34:34', '2026-03-22 14:48:11', 200000.00, NULL),
(15, 48, 'Bayanihan For Kabataan', 'Bayanihan', '/uploads/projects/project_11_1774242469_851067a2e11f.png', 'pending', 11, '2026-03-23 05:07:49', NULL, 99999.99, NULL),
(16, 48, 'Bayanihan For Kabataan', 'Bayanihan', '/uploads/projects/project_11_1774244516_3a57e61825b5.png', 'pending', 11, '2026-03-23 05:41:56', NULL, 99999.99, NULL),
(19, 9, 'Free Gas', '', 'img_1776795748_69e7c064d9981.jpg', 'pending', 1, '2026-04-21 18:22:28', NULL, 100000.00, 'doc_1776795748_69e7c064d9aee.png');

-- --------------------------------------------------------

--
-- Table structure for table `site_about`
--

CREATE TABLE `site_about` (
  `id` int(11) NOT NULL,
  `description` text NOT NULL DEFAULT '',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_about`
--

INSERT INTO `site_about` (`id`, `description`, `updated_at`) VALUES
(1, 'The Sangguniang Kabataan (SK) Federation of Malolos is committed to empowering the youth by promoting leadership, community participation, and sustainable development. Through collaboration and transparency, the Federation strengthens the voices of young leaders across 51 barangays.', '2026-01-31 17:44:31');

-- --------------------------------------------------------

--
-- Table structure for table `site_contact`
--

CREATE TABLE `site_contact` (
  `id` int(11) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_contact`
--

INSERT INTO `site_contact` (`id`, `email`, `phone`, `address`, `updated_at`) VALUES
(1, 'skfederation@malolos.gov.ph', '0912-345-6789', 'City of Malolos, Bulacan', '2026-01-31 18:39:37');

-- --------------------------------------------------------

--
-- Table structure for table `site_home`
--

CREATE TABLE `site_home` (
  `id` int(11) NOT NULL,
  `hero_title` varchar(255) NOT NULL,
  `hero_subtitle` text NOT NULL,
  `hero_button_text` varchar(100) DEFAULT NULL,
  `hero_button_link` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_home`
--

INSERT INTO `site_home` (`id`, `hero_title`, `hero_subtitle`, `hero_button_text`, `hero_button_link`, `updated_at`) VALUES
(1, 'Welcome to SK Federation of Malolos', 'Empowering the youth, shaping the future of our community', '', '', '2026-02-02 00:47:44');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `site_name` varchar(150) DEFAULT NULL,
  `footer_text` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `site_name`, `footer_text`, `updated_at`) VALUES
(1, 'SK Federation of Malolos', '© 2026 SK Federation of Malolos. All rights reserved.', '2026-01-30 18:14:15');

-- --------------------------------------------------------

--
-- Table structure for table `sk_council_members`
--

CREATE TABLE `sk_council_members` (
  `id` int(11) NOT NULL,
  `barangay_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sk_officers`
--

CREATE TABLE `sk_officers` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `position` varchar(100) NOT NULL,
  `barangay` varchar(100) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sk_officers`
--

INSERT INTO `sk_officers` (`id`, `full_name`, `position`, `barangay`, `photo`, `created_at`, `updated_at`) VALUES
(1, 'Hon. Rian Maclyn Dela Cruz', 'SK President', 'Barangay Lugam', 'images/officers/1769878874_centered.png', '2026-01-31 16:52:11', '2026-01-31 17:10:01'),
(2, 'Hon. Romeo Gabriel P. Santos', 'SK Vice President', 'Barangay Santor', 'images/officers/vice-president.jpg', '2026-01-31 16:52:11', '2026-01-31 16:52:11'),
(3, 'Hon. Lynea Ryziel B. Hina', 'SK Secretary', 'Barangay Barihan', 'images/officers/secretary.jpg', '2026-01-31 16:52:11', '2026-01-31 16:52:11'),
(4, 'Hon. Merylle Lugos', 'SK Treasurer', 'Barangay Sumapang Matanda', 'images/officers/treasurer.jpg', '2026-01-31 16:52:11', '2026-01-31 16:52:11'),
(5, 'Hon. Jhester M. Crisostomo', 'SK Auditor', 'Barangay Bungahan', 'images/officers/1769879367_centered.png', '2026-01-31 16:52:11', '2026-01-31 17:09:27'),
(6, 'Hon. Beatrice Capule', 'SK P.R.O', 'Barangay Bulihan', 'images/officers/pro.jpg', '2026-01-31 16:52:11', '2026-01-31 16:52:11'),
(7, 'Hon. Aecee Viel C. Evangelista', 'SK Sgt. at Arms', 'Barangay Anilao', 'images/officers/sgt.jpg', '2026-01-31 16:52:11', '2026-01-31 16:52:11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `surname` varchar(50) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `birthdate` date NOT NULL,
  `barangay` varchar(50) NOT NULL,
  `barangay_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('sk_chairperson','admin') DEFAULT 'sk_chairperson',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `middlename`, `surname`, `profile_pic`, `birthdate`, `barangay`, `barangay_id`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES
(1, 'Divine Grace', 'Robles', 'Larraquel', 'divine.jpg', '2002-07-12', 'Cofradia', 18, 'divinegracerlarraquel71202@gmail.com', '$2y$10$vBdT6QVBX12SOc6Wglml8eQ0U5UQAC0/U5jr4BB2J5v2HMyUqjPDi', 'sk_chairperson', 1, '2025-11-09 18:05:53'),
(2, 'Aecee Viel', '', 'Evangelista', NULL, '2002-12-12', 'Anilao', 0, 'dglarraquel@gmail.com', '$2y$10$xi90.RENexIvOaq5vF0QUeQH5HTz9N.k4u0NTFm.4mHOx4EkZPyym', 'sk_chairperson', 1, '2025-11-14 13:52:06'),
(7, 'Laila', '', 'Gutierrez', NULL, '2000-11-22', 'Bagong Bayan', 0, 'lailajanegutierrez66@gmail.com', '$2y$10$7/PcUTrCxSUpK6VZFygJ2.Z.6x8IGeid4j1nb6W.XqujLOz6C5Xku', 'sk_chairperson', 1, '2025-11-14 15:27:42'),
(8, 'Migs', '', 'Gatchalian', NULL, '2025-11-15', 'Santisima Trinidad', 0, 'migggatchalian@gmail.com', '$2y$10$i..af5OhLLeGceccaW4Gz.JH6dAA0ohvEn5xLAGZ7LRpPOL.Xzy9y', 'sk_chairperson', 1, '2025-11-15 06:49:22'),
(9, 'Crista Shane', '', 'de Vera', NULL, '2002-10-10', 'Taal', 0, 'ccristashanee@gmail.com', '$2y$10$z46MUz0Mwe1GAKKnxhF0neP6e9frRY8OtGqIx3R4i8LOm1mgu3KPK', 'sk_chairperson', 1, '2025-11-15 06:50:59'),
(10, 'Deserie', '', 'Robles', NULL, '1999-05-08', 'Guinhawa', 0, 'joanammc27@gmail.com', '$2y$10$mAmEZue1Ecn1zDsVStrSLelCJXQZ8/d0IcYljeC4hqQPSqpEcO8I6', 'sk_chairperson', 1, '2025-11-15 06:57:23'),
(11, 'Nerelyn', 'Villafuerte', 'Faustino', NULL, '2002-12-13', 'Bagna', 0, 'faustino.nerelynt8@gmail.com', '$2y$10$y/lImWQkEptOuJXldJpojuZZVbW/nDVVOgNkJshG0vy.AEj1BJU9e', 'sk_chairperson', 1, '2026-03-22 15:03:07'),
(12, 'Arden', 'Miles', 'Ryle', NULL, '2000-07-11', 'Tikay', 0, 'arden.milesryle@gmail.com', '$2y$10$GzR4WwbQl9f9H5v8yaGGsutf51Chg6n2TXjhc3UMStUp0aEhWT..a', 'sk_chairperson', 1, '2026-03-24 11:55:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barangay`
--
ALTER TABLE `barangay`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `barangays`
--
ALTER TABLE `barangays`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `barangay_officers`
--
ALTER TABLE `barangay_officers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barangay_id` (`barangay_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_event_barangay` (`barangay_id`);

--
-- Indexes for table `officers`
--
ALTER TABLE `officers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barangay_id` (`barangay_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_barangay_id` (`barangay_id`),
  ADD KEY `idx_submitted_by` (`submitted_by`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `site_about`
--
ALTER TABLE `site_about`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_contact`
--
ALTER TABLE `site_contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_home`
--
ALTER TABLE `site_home`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sk_council_members`
--
ALTER TABLE `sk_council_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_per_barangay` (`barangay_id`,`role`),
  ADD KEY `barangay_id` (`barangay_id`);

--
-- Indexes for table `sk_officers`
--
ALTER TABLE `sk_officers`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `barangay`
--
ALTER TABLE `barangay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `barangays`
--
ALTER TABLE `barangays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `barangay_officers`
--
ALTER TABLE `barangay_officers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `officers`
--
ALTER TABLE `officers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `site_contact`
--
ALTER TABLE `site_contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `site_home`
--
ALTER TABLE `site_home`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sk_council_members`
--
ALTER TABLE `sk_council_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sk_officers`
--
ALTER TABLE `sk_officers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barangay_officers`
--
ALTER TABLE `barangay_officers`
  ADD CONSTRAINT `barangay_officers_ibfk_1` FOREIGN KEY (`barangay_id`) REFERENCES `barangays` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_event_barangay` FOREIGN KEY (`barangay_id`) REFERENCES `barangays` (`id`);

--
-- Constraints for table `officers`
--
ALTER TABLE `officers`
  ADD CONSTRAINT `officers_ibfk_1` FOREIGN KEY (`barangay_id`) REFERENCES `barangays` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`barangay_id`) REFERENCES `barangays` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

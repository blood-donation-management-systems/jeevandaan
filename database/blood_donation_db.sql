-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 30, 2026 at 01:23 AM
-- Server version: 8.0.45-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blood_donation_db`
--
CREATE DATABASE IF NOT EXISTS `blood_donation_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `blood_donation_db`;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `blood_group` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requested_date` date DEFAULT NULL,
  `last_donation` date DEFAULT NULL,
  `status` enum('scheduled','pending','completed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `blood_group`, `requested_date`, `last_donation`, `status`, `created_at`) VALUES
(1, 1, 'A+', '2026-05-01', '2025-11-01', 'scheduled', '2026-04-27 11:37:32'),
(2, 1, 'B+', '2026-05-10', '2025-12-15', 'pending', '2026-04-27 11:37:32'),
(3, 1, 'O-', '2026-05-15', '2026-01-20', 'scheduled', '2026-04-27 11:37:32');

-- --------------------------------------------------------

--
-- Table structure for table `blood_donations`
--

CREATE TABLE `blood_donations` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `blood_group` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age` int DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `last_donation` date DEFAULT NULL,
  `units_ml` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blood_donations`
--

INSERT INTO `blood_donations` (`id`, `user_id`, `blood_group`, `location`, `age`, `weight`, `last_donation`, `units_ml`, `created_at`) VALUES
(1, 76, 'AB+', 'bir', 25, 65.00, '2025-05-06', 400, '2026-04-26 22:43:40'),
(2, 126, 'O-', '5', 18, 50.00, '2030-01-22', 400, '2026-04-27 13:35:22');

-- --------------------------------------------------------

--
-- Table structure for table `blood_inventory`
--

CREATE TABLE `blood_inventory` (
  `id` int NOT NULL,
  `blood_group` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `units_available` int DEFAULT '0',
  `storage_date` date DEFAULT NULL,
  `expire_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blood_inventory`
--

INSERT INTO `blood_inventory` (`id`, `blood_group`, `units_available`, `storage_date`, `expire_date`, `created_at`, `updated_at`) VALUES
(1, 'A+', 3, '2026-04-27', '2026-05-03', '2026-04-27 07:44:41', '2026-04-27 12:03:25'),
(2, 'A-', 0, '2026-04-27', '2026-06-08', '2026-04-27 07:44:41', '2026-04-27 07:44:41'),
(3, 'B+', 2, '2026-04-27', '2026-05-17', '2026-04-27 07:44:41', '2026-04-27 12:03:25'),
(4, 'B-', 0, '2026-04-27', '2026-06-08', '2026-04-27 07:44:41', '2026-04-27 07:44:41'),
(5, 'O+', 0, '2026-04-27', '2026-06-08', '2026-04-27 07:44:41', '2026-04-27 07:44:41'),
(6, 'O-', 5, '2026-04-27', '2026-05-10', '2026-04-27 07:44:41', '2026-04-27 12:03:25'),
(7, 'AB+', 0, '2026-04-27', '2026-06-08', '2026-04-27 07:44:41', '2026-04-27 07:44:41'),
(8, 'AB-', 3, '2026-04-27', '2026-06-08', '2026-04-27 07:44:41', '2026-04-27 11:49:04'),
(9, 'Rh-null', 0, '2026-04-27', '2026-06-08', '2026-04-27 07:44:41', '2026-04-27 07:44:41');

-- --------------------------------------------------------

--
-- Table structure for table `blood_requests`
--

CREATE TABLE `blood_requests` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `blood_group` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_needed` date DEFAULT NULL,
  `units_ml` int DEFAULT NULL,
  `urgency` enum('normal','emergency','critical') COLLATE utf8mb4_unicode_ci DEFAULT 'normal',
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blood_requests`
--

INSERT INTO `blood_requests` (`id`, `user_id`, `blood_group`, `location`, `date_needed`, `units_ml`, `urgency`, `status`, `created_at`) VALUES
(1, 76, 'A-', 'jhjd', '2026-04-22', 400, 'emergency', 'pending', '2026-04-26 22:45:57');

-- --------------------------------------------------------

--
-- Table structure for table `blood_testing`
--

CREATE TABLE `blood_testing` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `claimed_group` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tested_group` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hiv` enum('negative','positive') COLLATE utf8mb4_unicode_ci DEFAULT 'negative',
  `hepatitis_b` enum('negative','positive') COLLATE utf8mb4_unicode_ci DEFAULT 'negative',
  `hepatitis_c` enum('negative','positive') COLLATE utf8mb4_unicode_ci DEFAULT 'negative',
  `syphilis` enum('negative','positive') COLLATE utf8mb4_unicode_ci DEFAULT 'negative',
  `malaria` enum('negative','positive') COLLATE utf8mb4_unicode_ci DEFAULT 'negative',
  `covid` enum('negative','positive') COLLATE utf8mb4_unicode_ci DEFAULT 'negative',
  `hemoglobin` decimal(4,1) DEFAULT NULL,
  `blood_pressure` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pulse_rate` int DEFAULT NULL,
  `temperature` decimal(4,1) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `health_declaration` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','accepted','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blood_testing`
--

INSERT INTO `blood_testing` (`id`, `user_id`, `claimed_group`, `tested_group`, `hiv`, `hepatitis_b`, `hepatitis_c`, `syphilis`, `malaria`, `covid`, `hemoglobin`, `blood_pressure`, `pulse_rate`, `temperature`, `weight`, `health_declaration`, `status`, `created_at`) VALUES
(7, 98, 'A+', 'A+', 'negative', 'negative', 'negative', 'negative', 'negative', 'negative', 13.5, '120/80', 72, 36.7, 65.50, 'Healthy and fit for donation', 'accepted', '2026-04-27 08:13:47'),
(8, 118, 'O+', 'O+', 'negative', 'negative', 'negative', 'negative', 'negative', 'negative', 14.2, '118/76', 75, 36.8, 70.00, 'No recent illness', 'accepted', '2026-04-27 08:13:47'),
(9, 119, 'B+', 'B+', 'negative', 'positive', 'negative', 'negative', 'negative', 'negative', 12.8, '130/85', 80, 37.0, 68.20, 'Recovered from minor infection', 'rejected', '2026-04-27 08:13:47');

-- --------------------------------------------------------

--
-- Table structure for table `rare_blood_registry`
--

CREATE TABLE `rare_blood_registry` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `blood_group` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('donor','request') COLLATE utf8mb4_unicode_ci DEFAULT 'donor',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rare_blood_registry`
--

INSERT INTO `rare_blood_registry` (`id`, `user_id`, `blood_group`, `contact`, `type`, `created_at`) VALUES
(1, 76, 'A-', '982112322', 'request', '2026-04-26 22:44:57');

-- --------------------------------------------------------

--
-- Table structure for table `redcross_centers`
--

CREATE TABLE `redcross_centers` (
  `id` int NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `redcross_centers`
--

INSERT INTO `redcross_centers` (`id`, `name`, `location`, `contact`, `status`, `created_at`) VALUES
(1, 'Nepal Red Cross Kathmandu', 'Kathmandu', '01-4225344', 'active', '2026-04-27 08:08:39'),
(2, 'Pokhara Red Cross Society', 'Pokhara', '061-520123', 'active', '2026-04-27 08:08:39'),
(3, 'Chitwan Red Cross Center', 'Chitwan', '056-520456', 'inactive', '2026-04-27 08:08:39');

-- --------------------------------------------------------

--
-- Table structure for table `redcross_codes`
--

CREATE TABLE `redcross_codes` (
  `id` int NOT NULL,
  `code_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `redcross_codes`
--

INSERT INTO `redcross_codes` (`id`, `code_hash`, `label`, `active`, `created_at`) VALUES
(3, '$2y$10$RAus9OFVdDv/LD.xTYZqR.R/31tc7O.fMqGDYRUp32SPyJVDCcdKW', 'Default Red Cross Code', 1, '2026-04-26 18:39:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `full_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` int NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('user','redcross','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `status` enum('active','inactive','pending') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `blood_group` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `donations` int DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `age`, `email`, `phone`, `password`, `role`, `status`, `blood_group`, `donations`, `created_at`) VALUES
(1, 'Admin User', 30, 'admin@lifelink.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', NULL, 0, '2026-04-26 15:24:43'),
(69, 'dasfadsf', 55, NULL, '982123522', '$2y$10$OJDNVM7oYbpXNf0O5lvwKuD5q6Q/Taqpw1CgY.yWxb.s1/0wV.HJO', 'user', 'active', NULL, 0, '2026-04-26 22:17:50'),
(70, 'sfddasf', 55, NULL, 'dsafdf', '$2y$10$SCA8e.rVu8Im7vwp49.pj.kupjPLoSDMLAFXI858bp6bbuxf1ybyK', 'user', 'pending', NULL, 0, '2026-04-26 22:19:12'),
(71, 'aadil', 55, NULL, 'dfsdasf', '$2y$10$vG9vah.e3xfYEDLFNOUoo.6UqeXAgPYTbzJMkfAe8d2velu7sSncW', 'user', 'pending', NULL, 0, '2026-04-26 22:20:00'),
(76, 'kali', 22, NULL, '9821232636', '$2y$10$WoD5wfEpz/Y/pgSK8KtvW.ec4f0ReM66enooo9B/3TWb0SEwGNx7W', 'user', 'pending', NULL, 0, '2026-04-26 22:33:08'),
(78, 'dfaadf', 88, NULL, 'example@gmail', '$2y$10$l8m3LTqnAVnfr4V08OWNnu1Y2VS26sNHijtSzduIGW08k.cSWF4bC', 'user', 'pending', NULL, 0, '2026-04-26 22:37:11'),
(85, 'adsfas', 52, NULL, '9800000', '$2y$10$pqhTuSV3qTBntAXe5efZAOjc6cB.SjUkSoIeRyttyxKW7jm6UYW1e', 'user', 'pending', NULL, 0, '2026-04-27 06:28:52'),
(88, 'nsdfad', 88, NULL, 'niyat22@gmail', '$2y$10$ER9XTi/NnXINGk5UdV0ph.IC9l2ynQMZTpGJBTPVOJyLGO6TKJZUq', 'user', 'pending', NULL, 0, '2026-04-27 06:30:04'),
(98, 'Aadil', 25, '0', NULL, '$2y$10$WgXh/cfKgmnntY3CoMIA7eP468MryyYtfUyxFVIVxXLKDdCifK84e', 'user', 'pending', NULL, 0, '2026-04-27 06:36:50'),
(102, 'dasf', 55, NULL, '85222555', '$2y$10$bJNbD7Hy2ki64nfuTVU.eOm/EbQX6YrHf0G83tf1cYihAfpSzxKjG', 'user', 'pending', NULL, 0, '2026-04-27 06:38:52'),
(106, 'dsaf', 55, NULL, 'admins@lifelinsk', '$2y$10$J2e/V1Rqa9JonnPkyZGrS.c456.fAspeV1ZQOzvGQDw/XkyepP2ie', 'user', 'pending', NULL, 0, '2026-04-27 06:39:47'),
(118, 'Aadil', 25, 'aadil@gmail.com', NULL, '$2y$10$PEhs7mtp8ebsx6O4ARoJpe20Pnca8hLKE6iKfxLWIM8Kczq5vslQy', 'user', 'pending', NULL, 0, '2026-04-27 07:04:01'),
(119, 'dfadsf', 22, 'test@gmail.com', NULL, '$2y$10$i1PgdDlbQN9GqBBzQWUZLOJg.EKJ0QqHHF8aGvmiclUhLj3R76ljC', 'user', 'pending', NULL, 0, '2026-04-27 07:04:49'),
(120, 'dfasdf', 22, 'asfjk@gmail.com', NULL, '$2y$10$xYRqnytU9ig/z6OM9aqtoeT2/xzAsNEoYatS/caO5glJXGL9/iTpK', 'user', 'pending', NULL, 0, '2026-04-27 07:16:37'),
(121, 'asdf', 55, 'nata@gmail.com', NULL, '$2y$10$psscHt2NNWRpdopp595cVe3LNSRjmiyyBjp2dNTGHDSmnNxdlA76i', 'user', 'pending', NULL, 0, '2026-04-27 07:17:37'),
(122, 'sdafd', 88, 'neo@gmail.com', NULL, '$2y$10$Cko5sbjISei3RsqomgEazuUfMDA3RHa71ozaP7KprCZoFtv3aTwQK', 'user', 'pending', NULL, 0, '2026-04-27 07:21:00'),
(123, 'asdfa', 55, 'asdflj@gmail.com', NULL, '$2y$10$wec4dilp9kjE3d0FYVsMoO8xiGKbbLECOnqvzY.ZYsbkOQJ4hGisi', 'user', 'pending', NULL, 0, '2026-04-27 10:27:53'),
(124, 'diwash', 22, 'diwash@gmail.com', NULL, '$2y$10$gAnS7t16YHhtSLnJO1v55eickAlyW9rDS/FX9S7uNXWSKWEQT/Pzy', 'user', 'pending', NULL, 0, '2026-04-27 12:04:31'),
(125, 'dcfghj', 19, 'sdfgh@gmail.com', NULL, '$2y$10$qHyUKzXCROm03uBl4YcjcuS8TgfkC0ibkUILwceVdQPhHMrOLJeKa', 'user', 'pending', NULL, 0, '2026-04-27 13:06:02'),
(126, 'm.aadil', 22, 'aadil@99gmail.com', NULL, '$2y$10$oB/9NFmy3VuV0dvRtmbNPOtnki39q0CjPcF7gD4Q0RhZU5TlrsZIO', 'user', 'pending', NULL, 0, '2026-04-27 13:29:42');

-- --------------------------------------------------------

--
-- Table structure for table `verifications`
--

CREATE TABLE `verifications` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `front_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `back_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cert_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `submitted_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `verifications`
--

INSERT INTO `verifications` (`id`, `user_id`, `front_path`, `back_path`, `cert_path`, `status`, `submitted_at`) VALUES
(1, 69, 'citizenship/69_1777221195_f37eb9c4.jpg', 'citizenship/69_1777221195_f31c2bcf.jpg', NULL, 'pending', '2026-04-26 22:18:15'),
(2, 69, 'citizenship/69_1777221199_673fe937.jpg', 'citizenship/69_1777221199_abb8e2a0.jpg', NULL, 'pending', '2026-04-26 22:18:19'),
(3, 69, 'citizenship/69_1777221199_d0b79ff8.jpg', 'citizenship/69_1777221199_b41a43f9.jpg', NULL, 'pending', '2026-04-26 22:18:19'),
(4, 69, 'citizenship/69_1777221199_8974adaf.jpg', 'citizenship/69_1777221199_e4a3f87d.jpg', NULL, 'pending', '2026-04-26 22:18:19'),
(5, 70, 'citizenship/70_1777221280_b1ccd947.jpg', 'citizenship/70_1777221280_df0dc75a.jpg', NULL, 'pending', '2026-04-26 22:19:40'),
(6, 71, 'citizenship/71_1777221353_2afacc33.jpg', 'citizenship/71_1777221353_a9e92054.jpg', 'certificates/71_1777221353_1dd5088b.jpg', 'pending', '2026-04-26 22:20:53'),
(7, 71, 'citizenship/71_1777221355_734b198b.jpg', 'citizenship/71_1777221355_570c142f.jpg', 'certificates/71_1777221355_5c819646.jpg', 'pending', '2026-04-26 22:20:55'),
(8, 71, 'citizenship/71_1777221355_9389cfaf.jpg', 'citizenship/71_1777221355_e49d300b.jpg', 'certificates/71_1777221355_9eebcc92.jpg', 'pending', '2026-04-26 22:20:55'),
(9, 71, 'citizenship/71_1777221355_0041add2.jpg', 'citizenship/71_1777221355_1f87528b.jpg', 'certificates/71_1777221355_15a77a20.jpg', 'pending', '2026-04-26 22:20:55'),
(10, 71, 'citizenship/71_1777221359_7a61b0c3.jpg', 'citizenship/71_1777221359_3b2c1175.jpg', 'certificates/71_1777221359_5fbaf26d.jpg', 'pending', '2026-04-26 22:20:59'),
(11, 71, 'citizenship/71_1777221359_69a2fa89.jpg', 'citizenship/71_1777221359_4f1eac3a.jpg', 'certificates/71_1777221359_3a1df070.jpg', 'pending', '2026-04-26 22:20:59'),
(12, 71, 'citizenship/71_1777221359_08f5964e.jpg', 'citizenship/71_1777221359_2ae2cd7c.jpg', 'certificates/71_1777221359_8f0af8bc.jpg', 'pending', '2026-04-26 22:20:59'),
(13, 71, 'citizenship/71_1777221360_e802bcb0.jpg', 'citizenship/71_1777221360_3d4f9edd.jpg', 'certificates/71_1777221360_f62871f4.jpg', 'pending', '2026-04-26 22:21:00'),
(14, 71, 'citizenship/71_1777221360_cc3c9b05.jpg', 'citizenship/71_1777221360_0ec3a0af.jpg', 'certificates/71_1777221360_947a38a8.jpg', 'pending', '2026-04-26 22:21:00'),
(15, 71, 'citizenship/71_1777221360_c87abb34.jpg', 'citizenship/71_1777221360_306d81b7.jpg', 'certificates/71_1777221360_1b0bdb6d.jpg', 'pending', '2026-04-26 22:21:00'),
(16, 71, 'citizenship/71_1777221360_ec0bb4ba.jpg', 'citizenship/71_1777221360_b64ff8a3.jpg', 'certificates/71_1777221360_4765fb6b.jpg', 'pending', '2026-04-26 22:21:00'),
(17, 71, 'citizenship/71_1777221367_1a02044d.jpg', 'citizenship/71_1777221367_f58b71fa.jpg', 'certificates/71_1777221367_48e4ef46.jpg', 'pending', '2026-04-26 22:21:07'),
(18, 71, 'citizenship/71_1777221367_5dd8be8f.jpg', 'citizenship/71_1777221367_09e986a4.jpg', 'certificates/71_1777221367_3902b26e.jpg', 'pending', '2026-04-26 22:21:07'),
(19, 71, 'citizenship/71_1777221367_d290771a.jpg', 'citizenship/71_1777221367_2dbf0e62.jpg', 'certificates/71_1777221367_74e34b7a.jpg', 'pending', '2026-04-26 22:21:07'),
(20, 71, 'citizenship/71_1777221367_f1e29a65.jpg', 'citizenship/71_1777221367_5e870ad2.jpg', 'certificates/71_1777221367_9d78f163.jpg', 'pending', '2026-04-26 22:21:07'),
(21, 71, 'citizenship/71_1777221367_e468e012.jpg', 'citizenship/71_1777221367_cd7129a0.jpg', 'certificates/71_1777221367_7469f2b0.jpg', 'pending', '2026-04-26 22:21:07'),
(22, 71, 'citizenship/71_1777221368_2de14cd2.jpg', 'citizenship/71_1777221368_e5a349bb.jpg', 'certificates/71_1777221368_60a2553c.jpg', 'pending', '2026-04-26 22:21:08'),
(23, 71, 'citizenship/71_1777221368_ca5dd162.jpg', 'citizenship/71_1777221368_9360d535.jpg', 'certificates/71_1777221368_a0e9899b.jpg', 'pending', '2026-04-26 22:21:08'),
(24, 71, 'citizenship/71_1777221368_70c13ac0.jpg', 'citizenship/71_1777221368_4daa286d.jpg', 'certificates/71_1777221368_6aa35fb1.jpg', 'pending', '2026-04-26 22:21:08'),
(25, 71, 'citizenship/71_1777221368_083bde48.jpg', 'citizenship/71_1777221368_604aec4f.jpg', 'certificates/71_1777221368_d824c2ac.jpg', 'pending', '2026-04-26 22:21:08'),
(26, 71, 'citizenship/71_1777221369_0e2dceb1.jpg', 'citizenship/71_1777221369_de4cc388.jpg', 'certificates/71_1777221369_3f9fecb0.jpg', 'pending', '2026-04-26 22:21:09'),
(27, 71, 'citizenship/71_1777221372_4ec88955.jpg', 'citizenship/71_1777221372_73553f78.jpg', 'certificates/71_1777221372_c9123780.jpg', 'pending', '2026-04-26 22:21:12'),
(28, 70, 'citizenship/70_1777221437_a570ecb6.jpg', 'citizenship/70_1777221437_c7b6ebc2.jpg', NULL, 'pending', '2026-04-26 22:22:17'),
(29, 70, 'citizenship/70_1777221450_78ebc3ee.jpg', 'citizenship/70_1777221450_7e1b76bb.jpg', NULL, 'pending', '2026-04-26 22:22:30'),
(30, 71, 'citizenship/71_1777221506_9d9d0145.jpg', 'citizenship/71_1777221506_df486f88.jpg', NULL, 'pending', '2026-04-26 22:23:26'),
(31, 71, 'citizenship/71_1777221508_4fc5e587.jpg', 'citizenship/71_1777221508_f9826bdb.jpg', NULL, 'pending', '2026-04-26 22:23:28'),
(32, 71, 'citizenship/71_1777221584_a8be4312.jpg', 'citizenship/71_1777221584_918950b5.jpg', NULL, 'pending', '2026-04-26 22:24:44'),
(33, 71, 'citizenship/71_1777221994_ae0519cd.jpg', 'citizenship/71_1777221994_7a217dec.jpg', NULL, 'pending', '2026-04-26 22:31:34'),
(34, 76, 'citizenship/76_1777222116_175608fe.jpg', 'citizenship/76_1777222116_098e2e45.jpg', NULL, 'pending', '2026-04-26 22:33:36'),
(35, 76, 'citizenship/76_1777222644_fb82b9be.jpg', 'citizenship/76_1777222644_3981e9ed.jpg', NULL, 'pending', '2026-04-26 22:42:24'),
(36, 120, 'citizenship/120_1777253510_68c5124d.jpg', 'citizenship/120_1777253510_9e1808ab.jpg', NULL, 'pending', '2026-04-27 07:16:50'),
(37, 121, 'citizenship/121_1777253569_b9a4345e.jpg', 'citizenship/121_1777253569_193569c8.jpg', NULL, 'pending', '2026-04-27 07:17:49'),
(38, 122, 'citizenship/122_1777253770_03542bcb.jpg', 'citizenship/122_1777253770_c88d939c.jpg', NULL, 'pending', '2026-04-27 07:21:10'),
(39, 124, 'citizenship/124_1777270782_0966a8d8.jpg', 'citizenship/124_1777270782_e1c7e4f2.jpg', NULL, 'pending', '2026-04-27 12:04:42'),
(40, 125, 'citizenship/125_1777274490_57bb63ba.jpg', 'citizenship/125_1777274490_9e82b2f2.jpg', 'certificates/125_1777274490_9838ffcd.jpg', 'pending', '2026-04-27 13:06:30'),
(41, 126, 'citizenship/126_1777275897_e874be18.jpg', 'citizenship/126_1777275897_fbb3f710.jpg', NULL, 'pending', '2026-04-27 13:29:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `blood_donations`
--
ALTER TABLE `blood_donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `blood_inventory`
--
ALTER TABLE `blood_inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blood_group` (`blood_group`);

--
-- Indexes for table `blood_requests`
--
ALTER TABLE `blood_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `blood_testing`
--
ALTER TABLE `blood_testing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rare_blood_registry`
--
ALTER TABLE `rare_blood_registry`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `redcross_centers`
--
ALTER TABLE `redcross_centers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `redcross_codes`
--
ALTER TABLE `redcross_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `verifications`
--
ALTER TABLE `verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `blood_donations`
--
ALTER TABLE `blood_donations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `blood_inventory`
--
ALTER TABLE `blood_inventory`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `blood_requests`
--
ALTER TABLE `blood_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `blood_testing`
--
ALTER TABLE `blood_testing`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `rare_blood_registry`
--
ALTER TABLE `rare_blood_registry`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `redcross_centers`
--
ALTER TABLE `redcross_centers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `redcross_codes`
--
ALTER TABLE `redcross_codes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT for table `verifications`
--
ALTER TABLE `verifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `blood_donations`
--
ALTER TABLE `blood_donations`
  ADD CONSTRAINT `blood_donations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `blood_requests`
--
ALTER TABLE `blood_requests`
  ADD CONSTRAINT `blood_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `blood_testing`
--
ALTER TABLE `blood_testing`
  ADD CONSTRAINT `blood_testing_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rare_blood_registry`
--
ALTER TABLE `rare_blood_registry`
  ADD CONSTRAINT `rare_blood_registry_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `verifications`
--
ALTER TABLE `verifications`
  ADD CONSTRAINT `verifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

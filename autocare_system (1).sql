-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Oct 21, 2026 at 07:01 PM
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
-- Database: `autocare_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `module` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `module`, `description`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 1, 'seeded', 'System', 'Initial master data records and sample staff account initialized.', '127.0.0.1', NULL, '2026-10-19 06:08:35', '2026-10-19 06:08:35'),
(2, 2, 'created', 'Administration', 'Administrator Admin User (admin@autocare.com) created via Artisan console command.', '127.0.0.1', 'Symfony', '2026-10-19 06:08:40', '2026-10-19 06:08:40'),
(3, 1, 'login', 'Authentication', 'User Kamal Perera (staff) logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-19 06:47:51', '2026-10-19 06:47:51'),
(4, 1, 'logout', 'Authentication', 'User Kamal Perera logged out.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-19 06:50:04', '2026-10-19 06:50:04'),
(5, 2, 'login', 'Authentication', 'User Admin User (admin) logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-19 06:50:19', '2026-10-19 06:50:19'),
(6, 2, 'login', 'Authentication', 'User Admin User (admin) logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-19 22:43:56', '2026-10-19 22:43:56'),
(7, 2, 'generate_payroll', 'Payroll', 'Generated draft salary report for October 2026', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-20 00:32:35', '2026-10-20 00:32:35'),
(8, NULL, 'updated', 'Authentication', 'Administrator login email updated from admin@autocare.com to admin@gmail.com.', '127.0.0.1', 'Symfony', '2026-10-20 02:36:18', '2026-10-20 02:36:18'),
(9, 2, 'updated', 'Company Details', 'Updated Auto Care Center business details.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-20 04:11:39', '2026-10-20 04:11:39'),
(10, 2, 'created', 'Services', 'Added new service: SDK Tech Holdings (SRE1234).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-20 04:12:40', '2026-10-20 04:12:40'),
(11, 2, 'updated', 'Service Payments', 'Updated service prices and costs for: SDK Tech Holdings (SRE1234).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-20 04:13:15', '2026-10-20 04:13:15'),
(12, 2, 'updated', 'Service Payments', 'Updated service prices and costs for: SDK Tech Holdings (SRE1234).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-20 04:13:18', '2026-10-20 04:13:18'),
(13, 2, 'created', 'Vehicle Types', 'Added new vehicle type: AI Assistant.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-20 04:18:21', '2026-10-20 04:18:21'),
(14, 2, 'updated', 'Vehicle Types', 'Updated vehicle type: Car.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-20 04:18:49', '2026-10-20 04:18:49'),
(15, 2, 'updated', 'Vehicle Types', 'Updated vehicle type: Lorry.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-20 04:18:56', '2026-10-20 04:18:56'),
(16, 2, 'updated', 'Vehicle Types', 'Updated vehicle type: Motorbike.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-20 04:19:06', '2026-10-20 04:19:06'),
(17, 2, 'updated', 'Vehicle Types', 'Updated vehicle type: SUV.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-20 04:19:15', '2026-10-20 04:19:15'),
(18, 2, 'updated', 'Vehicle Types', 'Updated vehicle type: Van.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-20 04:19:21', '2026-10-20 04:19:21'),
(19, 2, 'updated', 'Services', 'Updated service details: Brake Inspection & Pad Replacement (SRV-002).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-20 04:19:33', '2026-10-20 04:19:33'),
(20, 2, 'created', 'Services', 'Added new service: QA VAT Company B Ltd (SRE12344).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-20 04:20:33', '2026-10-20 04:20:33'),
(21, 2, 'logout', 'Authentication', 'User Admin User logged out.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-20 04:21:08', '2026-10-20 04:21:08'),
(22, 2, 'login', 'Authentication', 'User Admin User (admin) logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-20 04:29:44', '2026-10-20 04:29:44'),
(23, 2, 'created', 'Suppliers', 'Registered new supplier: SDK Tech Holdings.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-20 04:37:13', '2026-10-20 04:37:13'),
(24, 2, 'updated', 'Suppliers', 'Updated supplier details for: SDK Tech Holdings.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-20 04:37:29', '2026-10-20 04:37:29'),
(25, 2, 'updated', 'Products', 'Updated product details: Bosch Aerotwin Premium Wiper Blade Set.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-20 04:41:59', '2026-10-20 04:41:59'),
(26, 2, 'login', 'Authentication', 'User Admin User (admin) logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-21 01:41:12', '2026-10-21 01:41:12'),
(27, 2, 'logout', 'Authentication', 'User Admin User logged out.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-21 01:56:33', '2026-10-21 01:56:33'),
(28, 2, 'login', 'Authentication', 'User Admin User (admin) logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-21 02:09:59', '2026-10-21 02:09:59'),
(29, 2, 'logout', 'Authentication', 'User Admin User logged out.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-21 02:48:12', '2026-10-21 02:48:12'),
(30, 2, 'login', 'Authentication', 'User Admin User (admin) logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-21 02:49:11', '2026-10-21 02:49:11'),
(31, 1, 'login', 'Authentication', 'User Kamal Perera (staff) logged in.', '127.0.0.1', NULL, '2026-10-21 03:19:47', '2026-10-21 03:19:47'),
(32, 1, 'login', 'Authentication', 'User Kamal Perera (staff) logged in.', '127.0.0.1', NULL, '2026-10-21 03:20:06', '2026-10-21 03:20:06'),
(33, 1, 'login', 'Authentication', 'User Kamal Perera (staff) logged in.', '127.0.0.1', NULL, '2026-10-21 03:20:17', '2026-10-21 03:20:17'),
(34, 1, 'login', 'Authentication', 'User Kamal Perera (staff) logged in.', '127.0.0.1', NULL, '2026-10-21 03:20:31', '2026-10-21 03:20:31'),
(35, 2, 'logout', 'Authentication', 'User Admin User logged out.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-21 04:33:37', '2026-10-21 04:33:37'),
(36, 2, 'login', 'Authentication', 'User Admin User (admin) logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-21 04:33:39', '2026-10-21 04:33:39'),
(37, 2, 'login', 'Authentication', 'User Admin User (admin) logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-21 04:33:39', '2026-10-21 04:33:39'),
(38, 2, 'completed_job', 'Job Cards', 'Completed Job #JOB-202610-0017 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-21 04:34:59', '2026-10-21 04:34:59'),
(39, 2, 'payment_recorded', 'Operations', 'Recorded cash payment #PAY-202610-0033 of LKR 5,000.00 for Job Card #JOB-202610-TEST01.', '127.0.0.1', 'Symfony', '2026-10-21 05:09:36', '2026-10-21 05:09:36'),
(40, 2, 'payment_recorded', 'Operations', 'Recorded cash payment #PAY-202610-0033 of LKR 5,000.00 for Job Card #JOB-202610-TEST01.', '127.0.0.1', 'Symfony', '2026-10-21 05:10:23', '2026-10-21 05:10:23'),
(41, 2, 'payment_recorded', 'Operations', 'Recorded card payment #PAY-202610-0034 of LKR 3,000.00 for Job Card #JOB-202610-TEST01.', '127.0.0.1', 'Symfony', '2026-10-21 05:10:24', '2026-10-21 05:10:24'),
(42, 2, 'payment_recorded', 'Operations', 'Recorded bank_transfer payment #PAY-202610-0035 of LKR 12,000.00 for Job Card #JOB-202610-TEST01.', '127.0.0.1', 'Symfony', '2026-10-21 05:10:24', '2026-10-21 05:10:24'),
(43, 2, 'company_credit_arranged', 'Operations', 'Approved Company Credit of LKR 15,000.00 verified for Job #JOB-202610-CREDIT01 (Customer: ABC Transport Ltd).', '127.0.0.1', 'Symfony', '2026-10-21 05:10:24', '2026-10-21 05:10:24'),
(44, 2, 'logout', 'Authentication', 'User Admin User logged out.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-21 08:35:50', '2026-10-21 08:35:50'),
(45, 2, 'login', 'Authentication', 'User Admin User (admin) logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-21 08:45:13', '2026-10-21 08:45:13'),
(46, 2, 'login', 'Authentication', 'User Admin User (admin) logged in.', '127.0.0.1', NULL, '2026-10-21 09:10:48', '2026-10-21 09:10:48'),
(47, 2, 'login', 'Authentication', 'User Admin User (admin) logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-21 09:19:14', '2026-10-21 09:19:14'),
(48, 2, 'updated', 'Vehicle Types', 'Updated vehicle type: Car (Carpets: Enabled).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-21 09:51:56', '2026-10-21 09:51:56'),
(49, 2, 'updated', 'Company Details', 'Updated Auto Care Center business details.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-21 10:25:41', '2026-10-21 10:25:41'),
(50, 1, 'create_shop_expense', 'Expenses', 'Recorded shop expense EXP-202611 for Boss Expenses (LKR 8,500.00, Status: Pay Later)', '127.0.0.1', 'Symfony', '2026-10-21 11:09:20', '2026-10-21 11:09:20'),
(51, 1, 'pay_expense', 'Expenses', 'Recorded payment of LKR 3,500.00 for expense EXP-202611 (Method: Cash)', '127.0.0.1', 'Symfony', '2026-10-21 11:09:21', '2026-10-21 11:09:21'),
(52, 1, 'pay_expense', 'Expenses', 'Recorded payment of LKR 5,000.00 for expense EXP-202611 (Method: Bank Transfer)', '127.0.0.1', 'Symfony', '2026-10-21 11:09:21', '2026-10-21 11:09:21'),
(53, 2, 'logout', 'Authentication', 'User Admin User logged out.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-21 11:30:58', '2026-10-21 11:30:58');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `appointment_number` varchar(50) NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vehicle_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `service_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(150) NOT NULL,
  `mobile_number` varchar(50) NOT NULL,
  `vehicle_number` varchar(50) NOT NULL,
  `service_name` varchar(150) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` varchar(20) NOT NULL,
  `status` enum('scheduled','confirmed','arrived','in_service','completed','cancelled','no_show') NOT NULL DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `job_card_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `appointment_number`, `branch_id`, `customer_id`, `vehicle_id`, `vehicle_type_id`, `service_id`, `customer_name`, `mobile_number`, `vehicle_number`, `service_name`, `appointment_date`, `appointment_time`, `status`, `notes`, `job_card_id`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 'APT-20261020-0001', 1, 21, 31, 5, 12, 'Wayamba Diagnostic Medical  Laboratory (Pvt) Ltd', '0714624024', 'BFH-4242', 'Air Conditioning & Cooling System Service', '2026-10-20', '09:00 AM', 'in_service', NULL, 32, 2, '2026-10-20 10:18:00', '2026-10-20 10:18:11');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `branch_code` varchar(50) NOT NULL,
  `branch_name` varchar(150) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_main_branch` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `company_id`, `branch_code`, `branch_name`, `address`, `phone`, `email`, `manager_id`, `is_main_branch`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'BR-001', 'Viran Auto Care Mattegoda', 'No. 10/B, Mattegoda Road, Mattegoda 10240', '077 620 80 59', 'colombo@autocare.local', 2, 1, 1, '2026-10-20 01:42:37', '2026-10-21 07:59:28'),
(3, 1, 'BR-002', 'AutoCare Kandy Service Center', 'No. 78, William Gopallawa Mawatha, Kandy, Sri Lanka', '+94 81 223 4567', 'kandy@autocare.local', 2, 0, 1, '2026-10-20 04:40:40', '2026-10-20 04:40:40');

-- --------------------------------------------------------

--
-- Table structure for table `branch_user`
--

CREATE TABLE `branch_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branch_user`
--

INSERT INTO `branch_user` (`id`, `branch_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '2026-10-20 01:42:37', '2026-10-20 01:42:37'),
(2, 1, 1, '2026-10-20 01:42:37', '2026-10-20 01:42:37'),
(5, 3, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(6, 3, 1, '2026-10-20 04:40:40', '2026-10-20 04:40:40');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('autocare-system-cache-admin@1234.com|127.0.0.1', 'i:1;', 1792490339),
('autocare-system-cache-admin@1234.com|127.0.0.1:timer', 'i:1792490339;', 1792490339),
('autocare-system-cache-admin@autocare.lk|127.0.0.1', 'i:2;', 1792572509),
('autocare-system-cache-admin@autocare.lk|127.0.0.1:timer', 'i:1792572509;', 1792572509);
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('autocare-system-cache-roster:project:v3:4d6e2ebb63bec57786b7d649e8cb230a', 'O:26:\"Laravel\\Roster\\ProjectScan\":8:{s:8:\"basePath\";s:29:\"D:\\Intern\\08\\autocare-system\\\";s:3:\"php\";O:35:\"Laravel\\Roster\\Ecosystems\\Ecosystem\":2:{s:9:\"\0*\0byName\";a:115:{s:10:\"brick/math\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"brick/math\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";E:43:\"Laravel\\Roster\\Enums\\PackageSource:Composer\";s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\Intern\\08\\autocare-system\\vendor\\brick\\math\";}s:31:\"carbonphp/carbon-doctrine-types\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"carbonphp/carbon-doctrine-types\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"D:\\Intern\\08\\autocare-system\\vendor\\carbonphp\\carbon-doctrine-types\";}s:23:\"dflydev/dot-access-data\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"dflydev/dot-access-data\";s:10:\"\0*\0version\";s:5:\"3.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\vendor\\dflydev\\dot-access-data\";}s:18:\"doctrine/inflector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"doctrine/inflector\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\vendor\\doctrine\\inflector\";}s:14:\"doctrine/lexer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"doctrine/lexer\";s:10:\"\0*\0version\";s:5:\"3.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\doctrine\\lexer\";}s:29:\"dragonmantank/cron-expression\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"dragonmantank/cron-expression\";s:10:\"\0*\0version\";s:5:\"3.6.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\vendor\\dragonmantank\\cron-expression\";}s:23:\"egulias/email-validator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"egulias/email-validator\";s:10:\"\0*\0version\";s:5:\"4.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\vendor\\egulias\\email-validator\";}s:18:\"fruitcake/php-cors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"fruitcake/php-cors\";s:10:\"\0*\0version\";s:5:\"1.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\vendor\\fruitcake\\php-cors\";}s:27:\"graham-campbell/result-type\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"graham-campbell/result-type\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"D:\\Intern\\08\\autocare-system\\vendor\\graham-campbell\\result-type\";}s:17:\"guzzlehttp/guzzle\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"guzzlehttp/guzzle\";s:10:\"\0*\0version\";s:5:\"8.3.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\guzzlehttp\\guzzle\";}s:19:\"guzzlehttp/promises\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"guzzlehttp/promises\";s:10:\"\0*\0version\";s:5:\"3.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\vendor\\guzzlehttp\\promises\";}s:15:\"guzzlehttp/psr7\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"guzzlehttp/psr7\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\guzzlehttp\\psr7\";}s:23:\"guzzlehttp/uri-template\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"guzzlehttp/uri-template\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\vendor\\guzzlehttp\\uri-template\";}s:17:\"laravel/framework\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"laravel/framework\";s:10:\"\0*\0version\";s:7:\"13.37.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^13.17\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\framework\";}s:15:\"laravel/prompts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"laravel/prompts\";s:10:\"\0*\0version\";s:6:\"0.3.26\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\prompts\";}s:28:\"laravel/serializable-closure\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"laravel/serializable-closure\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\serializable-closure\";}s:14:\"laravel/tinker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/tinker\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.0\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\tinker\";}s:17:\"league/commonmark\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"league/commonmark\";s:10:\"\0*\0version\";s:6:\"2.10.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\league\\commonmark\";}s:13:\"league/config\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"league/config\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\vendor\\league\\config\";}s:16:\"league/flysystem\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"league/flysystem\";s:10:\"\0*\0version\";s:6:\"3.36.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\vendor\\league\\flysystem\";}s:22:\"league/flysystem-local\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"league/flysystem-local\";s:10:\"\0*\0version\";s:6:\"3.35.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\league\\flysystem-local\";}s:26:\"league/mime-type-detection\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"league/mime-type-detection\";s:10:\"\0*\0version\";s:6:\"1.17.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"D:\\Intern\\08\\autocare-system\\vendor\\league\\mime-type-detection\";}s:10:\"league/uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"league/uri\";s:10:\"\0*\0version\";s:5:\"7.9.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\Intern\\08\\autocare-system\\vendor\\league\\uri\";}s:21:\"league/uri-interfaces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"league/uri-interfaces\";s:10:\"\0*\0version\";s:5:\"7.9.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\vendor\\league\\uri-interfaces\";}s:15:\"monolog/monolog\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"monolog/monolog\";s:10:\"\0*\0version\";s:6:\"3.12.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\monolog\\monolog\";}s:13:\"nesbot/carbon\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"nesbot/carbon\";s:10:\"\0*\0version\";s:6:\"3.15.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\vendor\\nesbot\\carbon\";}s:12:\"nette/schema\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"nette/schema\";s:10:\"\0*\0version\";s:5:\"1.3.6\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\Intern\\08\\autocare-system\\vendor\\nette\\schema\";}s:11:\"nette/utils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"nette/utils\";s:10:\"\0*\0version\";s:5:\"4.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\vendor\\nette\\utils\";}s:16:\"nikic/php-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"nikic/php-parser\";s:10:\"\0*\0version\";s:5:\"5.9.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\vendor\\nikic\\php-parser\";}s:19:\"nunomaduro/termwind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"nunomaduro/termwind\";s:10:\"\0*\0version\";s:5:\"2.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\vendor\\nunomaduro\\termwind\";}s:19:\"phpoption/phpoption\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"phpoption/phpoption\";s:10:\"\0*\0version\";s:6:\"1.10.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\vendor\\phpoption\\phpoption\";}s:9:\"psr/clock\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"psr/clock\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\clock\";}s:13:\"psr/container\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"psr/container\";s:10:\"\0*\0version\";s:5:\"2.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\container\";}s:20:\"psr/event-dispatcher\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"psr/event-dispatcher\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\event-dispatcher\";}s:15:\"psr/http-client\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"psr/http-client\";s:10:\"\0*\0version\";s:5:\"1.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\http-client\";}s:16:\"psr/http-factory\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/http-factory\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\http-factory\";}s:16:\"psr/http-message\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/http-message\";s:10:\"\0*\0version\";s:3:\"2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\http-message\";}s:7:\"psr/log\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"psr/log\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\log\";}s:16:\"psr/simple-cache\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/simple-cache\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\simple-cache\";}s:9:\"psy/psysh\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"psy/psysh\";s:10:\"\0*\0version\";s:7:\"0.12.24\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\Intern\\08\\autocare-system\\vendor\\psy\\psysh\";}s:17:\"ramsey/collection\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"ramsey/collection\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\ramsey\\collection\";}s:11:\"ramsey/uuid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"ramsey/uuid\";s:10:\"\0*\0version\";s:5:\"4.9.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\vendor\\ramsey\\uuid\";}s:13:\"symfony/clock\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"symfony/clock\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\clock\";}s:15:\"symfony/console\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/console\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\console\";}s:20:\"symfony/css-selector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"symfony/css-selector\";s:10:\"\0*\0version\";s:5:\"8.1.6\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\css-selector\";}s:29:\"symfony/deprecation-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"symfony/deprecation-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\deprecation-contracts\";}s:21:\"symfony/error-handler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/error-handler\";s:10:\"\0*\0version\";s:5:\"8.1.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\error-handler\";}s:24:\"symfony/event-dispatcher\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"symfony/event-dispatcher\";s:10:\"\0*\0version\";s:5:\"8.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\event-dispatcher\";}s:34:\"symfony/event-dispatcher-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"symfony/event-dispatcher-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\event-dispatcher-contracts\";}s:14:\"symfony/finder\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/finder\";s:10:\"\0*\0version\";s:5:\"8.1.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\finder\";}s:23:\"symfony/http-foundation\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"symfony/http-foundation\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\http-foundation\";}s:19:\"symfony/http-kernel\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"symfony/http-kernel\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\http-kernel\";}s:14:\"symfony/mailer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/mailer\";s:10:\"\0*\0version\";s:5:\"8.1.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\mailer\";}s:12:\"symfony/mime\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"symfony/mime\";s:10:\"\0*\0version\";s:5:\"8.1.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\mime\";}s:22:\"symfony/polyfill-ctype\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-ctype\";s:10:\"\0*\0version\";s:6:\"1.37.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-ctype\";}s:30:\"symfony/polyfill-intl-grapheme\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"symfony/polyfill-intl-grapheme\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-intl-grapheme\";}s:25:\"symfony/polyfill-intl-idn\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/polyfill-intl-idn\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-intl-idn\";}s:32:\"symfony/polyfill-intl-normalizer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"symfony/polyfill-intl-normalizer\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-intl-normalizer\";}s:25:\"symfony/polyfill-mbstring\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/polyfill-mbstring\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-mbstring\";}s:22:\"symfony/polyfill-php80\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php80\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-php80\";}s:22:\"symfony/polyfill-php82\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php82\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-php82\";}s:22:\"symfony/polyfill-php84\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php84\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-php84\";}s:22:\"symfony/polyfill-php85\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php85\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-php85\";}s:22:\"symfony/polyfill-php86\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php86\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-php86\";}s:21:\"symfony/polyfill-time\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/polyfill-time\";s:10:\"\0*\0version\";s:6:\"1.42.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-time\";}s:21:\"symfony/polyfill-uuid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/polyfill-uuid\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-uuid\";}s:15:\"symfony/process\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/process\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\process\";}s:15:\"symfony/routing\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/routing\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\routing\";}s:25:\"symfony/service-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/service-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\service-contracts\";}s:14:\"symfony/string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/string\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\string\";}s:19:\"symfony/translation\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"symfony/translation\";s:10:\"\0*\0version\";s:5:\"8.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\translation\";}s:29:\"symfony/translation-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"symfony/translation-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\translation-contracts\";}s:11:\"symfony/uid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"symfony/uid\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\uid\";}s:18:\"symfony/var-dumper\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"symfony/var-dumper\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\var-dumper\";}s:33:\"tijsverkoyen/css-to-inline-styles\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"tijsverkoyen/css-to-inline-styles\";s:10:\"\0*\0version\";s:5:\"2.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"D:\\Intern\\08\\autocare-system\\vendor\\tijsverkoyen\\css-to-inline-styles\";}s:16:\"vlucas/phpdotenv\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"vlucas/phpdotenv\";s:10:\"\0*\0version\";s:5:\"5.7.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\vendor\\vlucas\\phpdotenv\";}s:19:\"voku/portable-ascii\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"voku/portable-ascii\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\vendor\\voku\\portable-ascii\";}s:15:\"composer/semver\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"composer/semver\";s:10:\"\0*\0version\";s:5:\"3.5.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\composer\\semver\";}s:14:\"fakerphp/faker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"fakerphp/faker\";s:10:\"\0*\0version\";s:6:\"1.24.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.23\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\fakerphp\\faker\";}s:11:\"filp/whoops\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"filp/whoops\";s:10:\"\0*\0version\";s:6:\"2.18.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\vendor\\filp\\whoops\";}s:21:\"hamcrest/hamcrest-php\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"hamcrest/hamcrest-php\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\vendor\\hamcrest\\hamcrest-php\";}s:22:\"laravel/agent-detector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"laravel/agent-detector\";s:10:\"\0*\0version\";s:5:\"2.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\agent-detector\";}s:13:\"laravel/boost\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"laravel/boost\";s:10:\"\0*\0version\";s:6:\"2.10.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^2.10\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\boost\";}s:11:\"laravel/mcp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"laravel/mcp\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\mcp\";}s:12:\"laravel/pail\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"laravel/pail\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^1.2.5\";s:7:\"\0*\0path\";s:48:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\pail\";}s:11:\"laravel/pao\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"laravel/pao\";s:10:\"\0*\0version\";s:5:\"1.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^1.0.6\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\pao\";}s:12:\"laravel/pint\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"laravel/pint\";s:10:\"\0*\0version\";s:6:\"1.32.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.27\";s:7:\"\0*\0path\";s:48:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\pint\";}s:14:\"laravel/roster\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/roster\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\roster\";}s:15:\"mockery/mockery\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"mockery/mockery\";s:10:\"\0*\0version\";s:6:\"1.6.15\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^1.6\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\mockery\\mockery\";}s:17:\"myclabs/deep-copy\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"myclabs/deep-copy\";s:10:\"\0*\0version\";s:6:\"1.14.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\myclabs\\deep-copy\";}s:20:\"nunomaduro/collision\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"nunomaduro/collision\";s:10:\"\0*\0version\";s:5:\"8.9.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^8.6\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\vendor\\nunomaduro\\collision\";}s:16:\"phar-io/manifest\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"phar-io/manifest\";s:10:\"\0*\0version\";s:5:\"2.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\vendor\\phar-io\\manifest\";}s:15:\"phar-io/version\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"phar-io/version\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\phar-io\\version\";}s:25:\"phpunit/php-code-coverage\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-code-coverage\";s:10:\"\0*\0version\";s:6:\"12.5.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"D:\\Intern\\08\\autocare-system\\vendor\\phpunit\\php-code-coverage\";}s:25:\"phpunit/php-file-iterator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-file-iterator\";s:10:\"\0*\0version\";s:5:\"6.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"D:\\Intern\\08\\autocare-system\\vendor\\phpunit\\php-file-iterator\";}s:19:\"phpunit/php-invoker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"phpunit/php-invoker\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\vendor\\phpunit\\php-invoker\";}s:25:\"phpunit/php-text-template\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-text-template\";s:10:\"\0*\0version\";s:5:\"5.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"D:\\Intern\\08\\autocare-system\\vendor\\phpunit\\php-text-template\";}s:17:\"phpunit/php-timer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"phpunit/php-timer\";s:10:\"\0*\0version\";s:5:\"8.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\phpunit\\php-timer\";}s:15:\"phpunit/phpunit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"phpunit/phpunit\";s:10:\"\0*\0version\";s:7:\"12.5.40\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:8:\"^12.5.12\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\phpunit\\phpunit\";}s:20:\"sebastian/cli-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/cli-parser\";s:10:\"\0*\0version\";s:5:\"4.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\cli-parser\";}s:20:\"sebastian/comparator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/comparator\";s:10:\"\0*\0version\";s:5:\"7.1.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\comparator\";}s:20:\"sebastian/complexity\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/complexity\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\complexity\";}s:14:\"sebastian/diff\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"sebastian/diff\";s:10:\"\0*\0version\";s:5:\"7.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\diff\";}s:21:\"sebastian/environment\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"sebastian/environment\";s:10:\"\0*\0version\";s:5:\"8.1.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\environment\";}s:18:\"sebastian/exporter\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"sebastian/exporter\";s:10:\"\0*\0version\";s:5:\"7.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\exporter\";}s:22:\"sebastian/global-state\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"sebastian/global-state\";s:10:\"\0*\0version\";s:5:\"8.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\global-state\";}s:23:\"sebastian/lines-of-code\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"sebastian/lines-of-code\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\lines-of-code\";}s:27:\"sebastian/object-enumerator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"sebastian/object-enumerator\";s:10:\"\0*\0version\";s:5:\"7.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\object-enumerator\";}s:26:\"sebastian/object-reflector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"sebastian/object-reflector\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\object-reflector\";}s:27:\"sebastian/recursion-context\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"sebastian/recursion-context\";s:10:\"\0*\0version\";s:5:\"7.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\recursion-context\";}s:14:\"sebastian/type\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"sebastian/type\";s:10:\"\0*\0version\";s:5:\"6.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\type\";}s:17:\"sebastian/version\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"sebastian/version\";s:10:\"\0*\0version\";s:5:\"6.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\version\";}s:28:\"staabm/side-effects-detector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"staabm/side-effects-detector\";s:10:\"\0*\0version\";s:5:\"1.0.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"D:\\Intern\\08\\autocare-system\\vendor\\staabm\\side-effects-detector\";}s:12:\"symfony/yaml\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"symfony/yaml\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\yaml\";}s:17:\"theseer/tokenizer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"theseer/tokenizer\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\theseer\\tokenizer\";}}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:115:{i:0;r:5;i:1;r:13;i:2;r:21;i:3;r:29;i:4;r:37;i:5;r:45;i:6;r:53;i:7;r:61;i:8;r:69;i:9;r:77;i:10;r:85;i:11;r:93;i:12;r:101;i:13;r:109;i:14;r:117;i:15;r:125;i:16;r:133;i:17;r:141;i:18;r:149;i:19;r:157;i:20;r:165;i:21;r:173;i:22;r:181;i:23;r:189;i:24;r:197;i:25;r:205;i:26;r:213;i:27;r:221;i:28;r:229;i:29;r:237;i:30;r:245;i:31;r:253;i:32;r:261;i:33;r:269;i:34;r:277;i:35;r:285;i:36;r:293;i:37;r:301;i:38;r:309;i:39;r:317;i:40;r:325;i:41;r:333;i:42;r:341;i:43;r:349;i:44;r:357;i:45;r:365;i:46;r:373;i:47;r:381;i:48;r:389;i:49;r:397;i:50;r:405;i:51;r:413;i:52;r:421;i:53;r:429;i:54;r:437;i:55;r:445;i:56;r:453;i:57;r:461;i:58;r:469;i:59;r:477;i:60;r:485;i:61;r:493;i:62;r:501;i:63;r:509;i:64;r:517;i:65;r:525;i:66;r:533;i:67;r:541;i:68;r:549;i:69;r:557;i:70;r:565;i:71;r:573;i:72;r:581;i:73;r:589;i:74;r:597;i:75;r:605;i:76;r:613;i:77;r:621;i:78;r:629;i:79;r:637;i:80;r:645;i:81;r:653;i:82;r:661;i:83;r:669;i:84;r:677;i:85;r:685;i:86;r:693;i:87;r:701;i:88;r:709;i:89;r:717;i:90;r:725;i:91;r:733;i:92;r:741;i:93;r:749;i:94;r:757;i:95;r:765;i:96;r:773;i:97;r:781;i:98;r:789;i:99;r:797;i:100;r:805;i:101;r:813;i:102;r:821;i:103;r:829;i:104;r:837;i:105;r:845;i:106;r:853;i:107;r:861;i:108;r:869;i:109;r:877;i:110;r:885;i:111;r:893;i:112;r:901;i:113;r:909;i:114;r:917;}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:2:\"js\";O:37:\"Laravel\\Roster\\Ecosystems\\JsEcosystem\":3:{s:9:\"\0*\0byName\";a:124:{s:24:\"@alcalzone/ansi-tokenize\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"@alcalzone/ansi-tokenize\";s:10:\"\0*\0version\";s:5:\"0.3.1\";s:9:\"\0*\0source\";E:38:\"Laravel\\Roster\\Enums\\PackageSource:Npm\";s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"D:\\Intern\\08\\autocare-system\\node_modules\\@alcalzone\\ansi-tokenize\";}s:18:\"@laravel/multiplex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@laravel/multiplex\";s:10:\"\0*\0version\";s:5:\"0.4.5\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^0.4.1\";s:7:\"\0*\0path\";s:60:\"D:\\Intern\\08\\autocare-system\\node_modules\\@laravel\\multiplex\";}s:15:\"@vue/reactivity\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"@vue/reactivity\";s:10:\"\0*\0version\";s:6:\"3.5.44\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\node_modules\\@vue\\reactivity\";}s:11:\"@vue/shared\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"@vue/shared\";s:10:\"\0*\0version\";s:6:\"3.5.44\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\@vue\\shared\";}s:8:\"alpinejs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"alpinejs\";s:10:\"\0*\0version\";s:6:\"3.17.4\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^3.17.4\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\node_modules\\alpinejs\";}s:12:\"ansi-escapes\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"ansi-escapes\";s:10:\"\0*\0version\";s:5:\"7.3.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\ansi-escapes\";}s:10:\"ansi-regex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"ansi-regex\";s:10:\"\0*\0version\";s:5:\"6.4.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\ansi-regex\";}s:11:\"ansi-styles\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"ansi-styles\";s:10:\"\0*\0version\";s:5:\"7.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\ansi-styles\";}s:9:\"auto-bind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"auto-bind\";s:10:\"\0*\0version\";s:5:\"5.0.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\auto-bind\";}s:5:\"chalk\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"chalk\";s:10:\"\0*\0version\";s:5:\"5.6.2\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\node_modules\\chalk\";}s:9:\"cli-boxes\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"cli-boxes\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\cli-boxes\";}s:10:\"cli-cursor\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"cli-cursor\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\cli-cursor\";}s:12:\"cli-truncate\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"cli-truncate\";s:10:\"\0*\0version\";s:5:\"6.1.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\cli-truncate\";}s:12:\"code-excerpt\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"code-excerpt\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\code-excerpt\";}s:9:\"commander\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"commander\";s:10:\"\0*\0version\";s:6:\"15.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\commander\";}s:17:\"convert-to-spaces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"convert-to-spaces\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\node_modules\\convert-to-spaces\";}s:11:\"environment\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"environment\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\environment\";}s:10:\"es-toolkit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"es-toolkit\";s:10:\"\0*\0version\";s:6:\"1.52.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\es-toolkit\";}s:20:\"escape-string-regexp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"escape-string-regexp\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"D:\\Intern\\08\\autocare-system\\node_modules\\escape-string-regexp\";}s:20:\"get-east-asian-width\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"get-east-asian-width\";s:10:\"\0*\0version\";s:5:\"1.7.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"D:\\Intern\\08\\autocare-system\\node_modules\\get-east-asian-width\";}s:13:\"indent-string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"indent-string\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\node_modules\\indent-string\";}s:3:\"ink\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:3:\"ink\";s:10:\"\0*\0version\";s:5:\"7.1.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\Intern\\08\\autocare-system\\node_modules\\ink\";}s:23:\"is-fullwidth-code-point\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"is-fullwidth-code-point\";s:10:\"\0*\0version\";s:5:\"5.1.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\node_modules\\is-fullwidth-code-point\";}s:8:\"is-in-ci\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"is-in-ci\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\node_modules\\is-in-ci\";}s:8:\"mimic-fn\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"mimic-fn\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\node_modules\\mimic-fn\";}s:7:\"onetime\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"onetime\";s:10:\"\0*\0version\";s:5:\"5.1.2\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\node_modules\\onetime\";}s:13:\"patch-console\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"patch-console\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\node_modules\\patch-console\";}s:5:\"react\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"react\";s:10:\"\0*\0version\";s:6:\"19.3.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\node_modules\\react\";}s:16:\"react-reconciler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"react-reconciler\";s:10:\"\0*\0version\";s:6:\"0.33.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\node_modules\\react-reconciler\";}s:14:\"restore-cursor\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"restore-cursor\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\node_modules\\restore-cursor\";}s:9:\"scheduler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"scheduler\";s:10:\"\0*\0version\";s:6:\"0.27.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\scheduler\";}s:11:\"signal-exit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"signal-exit\";s:10:\"\0*\0version\";s:5:\"3.0.7\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\signal-exit\";}s:10:\"slice-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"slice-ansi\";s:10:\"\0*\0version\";s:5:\"9.0.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\slice-ansi\";}s:11:\"stack-utils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"stack-utils\";s:10:\"\0*\0version\";s:5:\"2.0.6\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\stack-utils\";}s:12:\"string-width\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"string-width\";s:10:\"\0*\0version\";s:5:\"8.3.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\string-width\";}s:10:\"strip-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"strip-ansi\";s:10:\"\0*\0version\";s:5:\"7.2.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\strip-ansi\";}s:10:\"tagged-tag\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"tagged-tag\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\tagged-tag\";}s:13:\"terminal-size\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"terminal-size\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\node_modules\\terminal-size\";}s:9:\"type-fest\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"type-fest\";s:10:\"\0*\0version\";s:6:\"5.10.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\type-fest\";}s:11:\"widest-line\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"widest-line\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\widest-line\";}s:9:\"wrap-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"wrap-ansi\";s:10:\"\0*\0version\";s:6:\"10.0.2\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\wrap-ansi\";}s:2:\"ws\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:2:\"ws\";s:10:\"\0*\0version\";s:6:\"8.22.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"D:\\Intern\\08\\autocare-system\\node_modules\\ws\";}s:11:\"yoga-layout\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"yoga-layout\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\yoga-layout\";}s:23:\"@jridgewell/gen-mapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"@jridgewell/gen-mapping\";s:10:\"\0*\0version\";s:6:\"0.3.13\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\node_modules\\@jridgewell\\gen-mapping\";}s:21:\"@jridgewell/remapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"@jridgewell/remapping\";s:10:\"\0*\0version\";s:5:\"2.3.5\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"D:\\Intern\\08\\autocare-system\\node_modules\\@jridgewell\\remapping\";}s:23:\"@jridgewell/resolve-uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"@jridgewell/resolve-uri\";s:10:\"\0*\0version\";s:5:\"3.1.2\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\node_modules\\@jridgewell\\resolve-uri\";}s:27:\"@jridgewell/sourcemap-codec\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"@jridgewell/sourcemap-codec\";s:10:\"\0*\0version\";s:5:\"1.6.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"D:\\Intern\\08\\autocare-system\\node_modules\\@jridgewell\\sourcemap-codec\";}s:25:\"@jridgewell/trace-mapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"@jridgewell/trace-mapping\";s:10:\"\0*\0version\";s:6:\"0.3.31\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"D:\\Intern\\08\\autocare-system\\node_modules\\@jridgewell\\trace-mapping\";}s:18:\"@oxc-project/types\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@oxc-project/types\";s:10:\"\0*\0version\";s:7:\"0.154.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"D:\\Intern\\08\\autocare-system\\node_modules\\@oxc-project\\types\";}s:34:\"@rolldown/binding-android-arm-eabi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-android-arm-eabi\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-android-arm-eabi\";}s:31:\"@rolldown/binding-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@rolldown/binding-android-arm64\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-android-arm64\";}s:30:\"@rolldown/binding-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@rolldown/binding-darwin-arm64\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-darwin-arm64\";}s:28:\"@rolldown/binding-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"@rolldown/binding-darwin-x64\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-darwin-x64\";}s:29:\"@rolldown/binding-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"@rolldown/binding-freebsd-x64\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-freebsd-x64\";}s:37:\"@rolldown/binding-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:37:\"@rolldown/binding-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:79:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-linux-arm-gnueabihf\";}s:33:\"@rolldown/binding-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-arm64-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-linux-arm64-gnu\";}s:34:\"@rolldown/binding-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-linux-arm64-musl\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-linux-arm64-musl\";}s:33:\"@rolldown/binding-linux-ppc64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-ppc64-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-linux-ppc64-gnu\";}s:33:\"@rolldown/binding-linux-s390x-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-s390x-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-linux-s390x-gnu\";}s:31:\"@rolldown/binding-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@rolldown/binding-linux-x64-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-linux-x64-gnu\";}s:32:\"@rolldown/binding-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@rolldown/binding-linux-x64-musl\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-linux-x64-musl\";}s:35:\"@rolldown/binding-openharmony-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@rolldown/binding-openharmony-arm64\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:77:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-openharmony-arm64\";}s:34:\"@rolldown/binding-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-win32-arm64-msvc\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-win32-arm64-msvc\";}s:32:\"@rolldown/binding-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@rolldown/binding-win32-x64-msvc\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-win32-x64-msvc\";}s:21:\"@rolldown/pluginutils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"@rolldown/pluginutils\";s:10:\"\0*\0version\";s:5:\"1.0.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\pluginutils\";}s:17:\"@tailwindcss/node\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"@tailwindcss/node\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\node\";}s:18:\"@tailwindcss/oxide\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@tailwindcss/oxide\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide\";}s:32:\"@tailwindcss/oxide-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@tailwindcss/oxide-android-arm64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-android-arm64\";}s:31:\"@tailwindcss/oxide-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@tailwindcss/oxide-darwin-arm64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-darwin-arm64\";}s:29:\"@tailwindcss/oxide-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"@tailwindcss/oxide-darwin-x64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-darwin-x64\";}s:30:\"@tailwindcss/oxide-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@tailwindcss/oxide-freebsd-x64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-freebsd-x64\";}s:38:\"@tailwindcss/oxide-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:38:\"@tailwindcss/oxide-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:80:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-linux-arm-gnueabihf\";}s:34:\"@tailwindcss/oxide-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@tailwindcss/oxide-linux-arm64-gnu\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-linux-arm64-gnu\";}s:35:\"@tailwindcss/oxide-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@tailwindcss/oxide-linux-arm64-musl\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:77:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-linux-arm64-musl\";}s:32:\"@tailwindcss/oxide-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@tailwindcss/oxide-linux-x64-gnu\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-linux-x64-gnu\";}s:33:\"@tailwindcss/oxide-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@tailwindcss/oxide-linux-x64-musl\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-linux-x64-musl\";}s:30:\"@tailwindcss/oxide-wasm32-wasi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@tailwindcss/oxide-wasm32-wasi\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-wasm32-wasi\";}s:35:\"@tailwindcss/oxide-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@tailwindcss/oxide-win32-arm64-msvc\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:77:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-win32-arm64-msvc\";}s:33:\"@tailwindcss/oxide-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@tailwindcss/oxide-win32-x64-msvc\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-win32-x64-msvc\";}s:17:\"@tailwindcss/vite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"@tailwindcss/vite\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^4.0.0\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\vite\";}s:5:\"cliui\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"cliui\";s:10:\"\0*\0version\";s:5:\"9.0.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\node_modules\\cliui\";}s:12:\"concurrently\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"concurrently\";s:10:\"\0*\0version\";s:6:\"10.0.6\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^10.0.3\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\concurrently\";}s:11:\"detect-libc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"detect-libc\";s:10:\"\0*\0version\";s:5:\"2.1.2\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\detect-libc\";}s:11:\"emoji-regex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"emoji-regex\";s:10:\"\0*\0version\";s:6:\"10.6.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\emoji-regex\";}s:16:\"enhanced-resolve\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"enhanced-resolve\";s:10:\"\0*\0version\";s:6:\"5.26.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\node_modules\\enhanced-resolve\";}s:8:\"escalade\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"escalade\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\node_modules\\escalade\";}s:4:\"fdir\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"fdir\";s:10:\"\0*\0version\";s:5:\"6.5.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\Intern\\08\\autocare-system\\node_modules\\fdir\";}s:8:\"fsevents\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"fsevents\";s:10:\"\0*\0version\";s:5:\"2.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\node_modules\\fsevents\";}s:15:\"get-caller-file\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"get-caller-file\";s:10:\"\0*\0version\";s:5:\"2.0.5\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\node_modules\\get-caller-file\";}s:11:\"graceful-fs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"graceful-fs\";s:10:\"\0*\0version\";s:6:\"4.2.11\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\graceful-fs\";}s:4:\"jiti\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"jiti\";s:10:\"\0*\0version\";s:5:\"2.7.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\Intern\\08\\autocare-system\\node_modules\\jiti\";}s:19:\"laravel-vite-plugin\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"laravel-vite-plugin\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.1\";s:7:\"\0*\0path\";s:61:\"D:\\Intern\\08\\autocare-system\\node_modules\\laravel-vite-plugin\";}s:12:\"lightningcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"lightningcss\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss\";}s:26:\"lightningcss-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"lightningcss-android-arm64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-android-arm64\";}s:25:\"lightningcss-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"lightningcss-darwin-arm64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-darwin-arm64\";}s:23:\"lightningcss-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"lightningcss-darwin-x64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-darwin-x64\";}s:24:\"lightningcss-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"lightningcss-freebsd-x64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-freebsd-x64\";}s:32:\"lightningcss-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"lightningcss-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-linux-arm-gnueabihf\";}s:28:\"lightningcss-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"lightningcss-linux-arm64-gnu\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-linux-arm64-gnu\";}s:29:\"lightningcss-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"lightningcss-linux-arm64-musl\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-linux-arm64-musl\";}s:26:\"lightningcss-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"lightningcss-linux-x64-gnu\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-linux-x64-gnu\";}s:27:\"lightningcss-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"lightningcss-linux-x64-musl\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-linux-x64-musl\";}s:29:\"lightningcss-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"lightningcss-win32-arm64-msvc\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-win32-arm64-msvc\";}s:27:\"lightningcss-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"lightningcss-win32-x64-msvc\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-win32-x64-msvc\";}s:12:\"magic-string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"magic-string\";s:10:\"\0*\0version\";s:7:\"0.30.21\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\magic-string\";}s:6:\"nanoid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"nanoid\";s:10:\"\0*\0version\";s:6:\"3.3.20\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\Intern\\08\\autocare-system\\node_modules\\nanoid\";}s:10:\"picocolors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"picocolors\";s:10:\"\0*\0version\";s:5:\"1.1.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\picocolors\";}s:9:\"picomatch\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"picomatch\";s:10:\"\0*\0version\";s:5:\"4.0.7\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\picomatch\";}s:7:\"postcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"postcss\";s:10:\"\0*\0version\";s:6:\"8.5.31\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\node_modules\\postcss\";}s:8:\"rolldown\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"rolldown\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\node_modules\\rolldown\";}s:4:\"rxjs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"rxjs\";s:10:\"\0*\0version\";s:5:\"7.8.2\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\Intern\\08\\autocare-system\\node_modules\\rxjs\";}s:11:\"shell-quote\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"shell-quote\";s:10:\"\0*\0version\";s:6:\"1.12.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\shell-quote\";}s:13:\"source-map-js\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"source-map-js\";s:10:\"\0*\0version\";s:5:\"1.2.2\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\node_modules\\source-map-js\";}s:14:\"supports-color\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"supports-color\";s:10:\"\0*\0version\";s:6:\"10.2.2\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\node_modules\\supports-color\";}s:11:\"tailwindcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"tailwindcss\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^4.0.0\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\tailwindcss\";}s:7:\"tapable\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"tapable\";s:10:\"\0*\0version\";s:5:\"2.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\node_modules\\tapable\";}s:10:\"tinyglobby\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"tinyglobby\";s:10:\"\0*\0version\";s:6:\"0.2.17\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\tinyglobby\";}s:9:\"tree-kill\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"tree-kill\";s:10:\"\0*\0version\";s:5:\"1.2.2\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\tree-kill\";}s:5:\"tslib\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"tslib\";s:10:\"\0*\0version\";s:5:\"2.8.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\node_modules\\tslib\";}s:4:\"vite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"vite\";s:10:\"\0*\0version\";s:5:\"8.3.4\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^8.0.0\";s:7:\"\0*\0path\";s:46:\"D:\\Intern\\08\\autocare-system\\node_modules\\vite\";}s:23:\"vite-plugin-full-reload\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"vite-plugin-full-reload\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\node_modules\\vite-plugin-full-reload\";}s:4:\"y18n\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"y18n\";s:10:\"\0*\0version\";s:5:\"5.0.8\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\Intern\\08\\autocare-system\\node_modules\\y18n\";}s:5:\"yargs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"yargs\";s:10:\"\0*\0version\";s:6:\"18.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\node_modules\\yargs\";}s:12:\"yargs-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"yargs-parser\";s:10:\"\0*\0version\";s:6:\"22.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\yargs-parser\";}}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:124:{i:0;r:1045;i:1;r:1053;i:2;r:1061;i:3;r:1069;i:4;r:1077;i:5;r:1085;i:6;r:1093;i:7;r:1101;i:8;r:1109;i:9;r:1117;i:10;r:1125;i:11;r:1133;i:12;r:1141;i:13;r:1149;i:14;r:1157;i:15;r:1165;i:16;r:1173;i:17;r:1181;i:18;r:1189;i:19;r:1197;i:20;r:1205;i:21;r:1213;i:22;r:1221;i:23;r:1229;i:24;r:1237;i:25;r:1245;i:26;r:1253;i:27;r:1261;i:28;r:1269;i:29;r:1277;i:30;r:1285;i:31;r:1293;i:32;r:1301;i:33;r:1309;i:34;r:1317;i:35;r:1325;i:36;r:1333;i:37;r:1341;i:38;r:1349;i:39;r:1357;i:40;r:1365;i:41;r:1373;i:42;r:1381;i:43;r:1389;i:44;r:1397;i:45;r:1405;i:46;r:1413;i:47;r:1421;i:48;r:1429;i:49;r:1437;i:50;r:1445;i:51;r:1453;i:52;r:1461;i:53;r:1469;i:54;r:1477;i:55;r:1485;i:56;r:1493;i:57;r:1501;i:58;r:1509;i:59;r:1517;i:60;r:1525;i:61;r:1533;i:62;r:1541;i:63;r:1549;i:64;r:1557;i:65;r:1565;i:66;r:1573;i:67;r:1581;i:68;r:1589;i:69;r:1597;i:70;r:1605;i:71;r:1613;i:72;r:1621;i:73;r:1629;i:74;r:1637;i:75;r:1645;i:76;r:1653;i:77;r:1661;i:78;r:1669;i:79;r:1677;i:80;r:1685;i:81;r:1693;i:82;r:1701;i:83;r:1709;i:84;r:1717;i:85;r:1725;i:86;r:1733;i:87;r:1741;i:88;r:1749;i:89;r:1757;i:90;r:1765;i:91;r:1773;i:92;r:1781;i:93;r:1789;i:94;r:1797;i:95;r:1805;i:96;r:1813;i:97;r:1821;i:98;r:1829;i:99;r:1837;i:100;r:1845;i:101;r:1853;i:102;r:1861;i:103;r:1869;i:104;r:1877;i:105;r:1885;i:106;r:1893;i:107;r:1901;i:108;r:1909;i:109;r:1917;i:110;r:1925;i:111;r:1933;i:112;r:1941;i:113;r:1949;i:114;r:1957;i:115;r:1965;i:116;r:1973;i:117;r:1981;i:118;r:1989;i:119;r:1997;i:120;r:2005;i:121;r:2013;i:122;r:2021;i:123;r:2029;}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:17:\"\0*\0packageManager\";E:41:\"Laravel\\Roster\\Enums\\JsPackageManager:Npm\";}s:6:\"stacks\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:1:{i:0;E:32:\"Laravel\\Roster\\Enums\\Stack:Blade\";}}s:21:\"browserTestFrameworks\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}s:9:\"frontends\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}s:6:\"agents\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:2:{i:0;E:37:\"Laravel\\Roster\\Enums\\Agent:ClaudeCode\";i:1;E:32:\"Laravel\\Roster\\Enums\\Agent:Codex\";}}s:7:\"editors\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}}', 1792414616);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carpet_categories`
--

CREATE TABLE `carpet_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `carpet_categories`
--

INSERT INTO `carpet_categories` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Front Carpet', 'active', '2026-10-21 09:26:49', '2026-10-21 09:26:49'),
(2, 'Rear Carpet', 'active', '2026-10-21 09:26:49', '2026-10-21 09:26:49'),
(3, 'Boot Carpet', 'active', '2026-10-21 09:26:49', '2026-10-21 09:26:49'),
(4, 'kkk', 'active', '2026-10-21 09:36:41', '2026-10-21 09:36:41');

-- --------------------------------------------------------

--
-- Table structure for table `carpet_category_vehicle_type`
--

CREATE TABLE `carpet_category_vehicle_type` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_type_id` bigint(20) UNSIGNED NOT NULL,
  `carpet_category_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `carpet_category_vehicle_type`
--

INSERT INTO `carpet_category_vehicle_type` (`id`, `vehicle_type_id`, `carpet_category_id`, `created_at`, `updated_at`) VALUES
(1, 1, 3, '2026-10-21 09:26:50', '2026-10-21 09:26:50'),
(2, 1, 1, '2026-10-21 09:26:50', '2026-10-21 09:26:50'),
(3, 1, 2, '2026-10-21 09:26:50', '2026-10-21 09:26:50'),
(4, 3, 3, '2026-10-21 09:26:50', '2026-10-21 09:26:50'),
(5, 3, 1, '2026-10-21 09:26:50', '2026-10-21 09:26:50'),
(6, 3, 2, '2026-10-21 09:26:50', '2026-10-21 09:26:50'),
(7, 2, 3, '2026-10-21 09:26:50', '2026-10-21 09:26:50'),
(8, 2, 1, '2026-10-21 09:26:50', '2026-10-21 09:26:50'),
(9, 2, 2, '2026-10-21 09:26:50', '2026-10-21 09:26:50'),
(10, 1, 4, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `company_settings`
--

CREATE TABLE `company_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `registration_number` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `tax_number` varchar(255) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_settings`
--

INSERT INTO `company_settings` (`id`, `company_name`, `registration_number`, `address`, `phone`, `email`, `tax_number`, `logo_path`, `created_at`, `updated_at`) VALUES
(1, 'Viran Auto Care', 'PV-2026-98765', 'No. 10/B, Mattegoda Road, Mattegoda 10240', '077 620 80 59', 'info@autocare.local', 'VAT-123456789-000', 'company/viran_auto_care_logo.png', '2026-10-19 06:08:35', '2026-10-21 07:59:28');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `customer_type` enum('individual','company') NOT NULL DEFAULT 'individual',
  `company_name` varchar(255) DEFAULT NULL,
  `registration_number` varchar(100) DEFAULT NULL,
  `tax_number` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `sms_consent` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `customer_name`, `phone`, `email`, `customer_type`, `company_name`, `registration_number`, `tax_number`, `address`, `notes`, `sms_consent`, `status`, `created_at`, `updated_at`) VALUES
(11, 'Sunil Shantha', '0772345671', 'sunil.shantha@gmail.com', 'individual', NULL, NULL, NULL, 'No. 45, Galle Road, Colombo 03', NULL, 1, 'active', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(12, 'Nimal Jayasinghe', '0713456782', 'nimal.jayasinghe@gmail.com', 'individual', NULL, NULL, NULL, 'No. 12, Peradeniya Road, Kandy', NULL, 1, 'active', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(13, 'Kasun Bandara', '0754567893', 'kasun.bandara@gmail.com', 'individual', NULL, NULL, NULL, 'No. 88, Kandy Road, Gampaha', NULL, 1, 'active', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(14, 'Chaminda Silva', '0765678904', 'chaminda.silva@gmail.com', 'individual', NULL, NULL, NULL, 'No. 24, Hill Street, Dehiwala', NULL, 1, 'active', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(15, 'Priyanka Fernando', '0726789015', 'priyanka.fernando@gmail.com', 'individual', NULL, NULL, NULL, 'No. 102, Main Street, Negombo', NULL, 1, 'active', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(16, 'Dilshan Perera', '0787890126', 'dilshan.perera@gmail.com', 'individual', NULL, NULL, NULL, 'No. 15, Old Road, Moratuwa', NULL, 1, 'active', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(17, 'Ruwan Wickramasinghe', '0708901237', 'ruwan.wick@gmail.com', 'individual', NULL, NULL, NULL, 'No. 67, Waragoda Road, Kelaniya', NULL, 1, 'active', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(18, 'Nadeeka Damayanthi', '0749012348', 'nadeeka.damayanthi@gmail.com', 'individual', NULL, NULL, NULL, 'No. 33, Parliament Road, Battaramulla', NULL, 1, 'active', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(19, 'Roshan De Silva', '0112445566', 'fleet@quickcab.lk', 'company', 'QuickCab Fleet Services (Pvt) Ltd', 'PV-QC-9988', 'VAT-987654321-001', 'No. 500, Nawala Road, Rajagiriya', NULL, 1, 'active', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(20, 'Gamini Wijesinghe', '0112778899', 'accounts@lankacabs.lk', 'company', 'Lanka Cabs & Tours (Pvt) Ltd', 'PV-LKA-9988', 'VAT-LKA-3322', 'No. 45, Galle Road, Colombo 03', NULL, 1, 'active', '2026-10-20 04:40:40', '2026-10-20 07:28:24'),
(21, 'QA Customer B', '0714624024', 'adeeshpm@gmail.com', 'individual', NULL, NULL, NULL, NULL, NULL, 1, 'active', '2026-10-20 04:49:55', '2026-10-21 08:46:40'),
(22, 'Sunil Fernando', '0112991122', 'fleet@abctransport.lk', 'company', 'ABC Transport Ltd', 'PV-ABC-1234', 'VAT-ABC-9988', 'No. 88, Kandy Road, Peliyagoda', NULL, 1, 'active', '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(23, 'Dinesh Gamage', '0112883344', 'accounts@xyzlogistics.lk', 'company', 'XYZ Logistics Ltd', 'PV-XYZ-5678', 'VAT-XYZ-5544', 'No. 150, Baseline Road, Colombo 09', NULL, 1, 'active', '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(24, 'Kamal Gunaratne', '0779988776', 'kamal.gunaratne@gmail.com', 'individual', NULL, NULL, NULL, 'No. 22, Station Road, Nugegoda', NULL, 1, 'active', '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(26, 'Live Test Customer', '0773334444', NULL, 'individual', NULL, NULL, NULL, NULL, NULL, 1, 'active', '2026-10-21 10:11:56', '2026-10-21 10:11:56');

-- --------------------------------------------------------

--
-- Table structure for table `customer_credit_accounts`
--

CREATE TABLE `customer_credit_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `credit_limit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_terms_days` int(11) NOT NULL DEFAULT 30,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_credit_accounts`
--

INSERT INTO `customer_credit_accounts` (`id`, `customer_id`, `credit_limit`, `payment_terms_days`, `opening_balance`, `status`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 22, 50000.00, 30, 0.00, 'active', 'Corporate fleet maintenance credit account - 30 days credit terms.', 2, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(2, 23, 100000.00, 30, 0.00, 'active', 'Heavy transport logistics fleet credit account.', 2, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(3, 24, 40000.00, 14, 0.00, 'active', 'Individual executive account approved for 14-day credit.', 2, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(4, 20, 200000.00, 30, 0.00, 'active', 'Tour fleet credit account for testing credit aging buckets.', 2, '2026-10-20 07:28:24', '2026-10-20 07:28:24');

-- --------------------------------------------------------

--
-- Table structure for table `direct_sales`
--

CREATE TABLE `direct_sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sale_number` varchar(50) NOT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sale_date` date NOT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `amount_paid` decimal(15,2) NOT NULL DEFAULT 0.00,
  `change_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('cash','card','bank_transfer','credit') NOT NULL DEFAULT 'cash',
  `status` enum('completed','cancelled') NOT NULL DEFAULT 'completed',
  `notes` text DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `cancelled_by` bigint(20) UNSIGNED DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `stock_reversed` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `direct_sales`
--

INSERT INTO `direct_sales` (`id`, `branch_id`, `sale_number`, `invoice_id`, `customer_id`, `sale_date`, `subtotal`, `discount_amount`, `tax_amount`, `grand_total`, `amount_paid`, `change_amount`, `payment_method`, `status`, `notes`, `cancellation_reason`, `cancelled_by`, `cancelled_at`, `stock_reversed`, `created_by`, `created_at`, `updated_at`) VALUES
(6, 1, 'DS-202610-0001', NULL, 13, '2026-10-15', 14000.00, 0.00, 0.00, 14000.00, 14000.00, 0.00, 'cash', 'completed', NULL, NULL, NULL, NULL, 0, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(7, 1, 'DS-202610-0002', NULL, 14, '2026-10-19', 6900.00, 0.00, 0.00, 6900.00, 6900.00, 0.00, 'card', 'completed', NULL, NULL, NULL, NULL, 0, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(8, 1, 'DS-202610-0003', NULL, 20, '2026-10-19', 10800.00, 0.00, 0.00, 10800.00, 10800.00, 0.00, 'bank_transfer', 'completed', NULL, NULL, NULL, NULL, 0, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(9, 1, 'DS-202610-0004', NULL, 17, '2026-10-16', 2400.00, 0.00, 0.00, 2400.00, 2400.00, 0.00, 'cash', 'completed', NULL, NULL, NULL, NULL, 0, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(10, 1, 'DS-202610-0005', 13, 11, '2026-10-18', 18000.00, 0.00, 0.00, 18000.00, 18000.00, 0.00, 'card', 'completed', NULL, NULL, NULL, NULL, 0, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(11, 1, 'DS-202610-0017', 30, 16, '2026-10-02', 6500.00, 0.00, 0.00, 6500.00, 6500.00, 0.00, 'cash', 'completed', NULL, NULL, NULL, NULL, 0, 1, '2026-10-20 07:11:45', '2026-10-20 07:11:45');

-- --------------------------------------------------------

--
-- Table structure for table `direct_sale_items`
--

CREATE TABLE `direct_sale_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `direct_sale_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_sku` varchar(50) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` decimal(12,2) NOT NULL DEFAULT 1.00,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unit_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `batch_allocations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`batch_allocations`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `direct_sale_items`
--

INSERT INTO `direct_sale_items` (`id`, `direct_sale_id`, `product_id`, `product_sku`, `product_name`, `quantity`, `unit_price`, `unit_cost`, `line_total`, `batch_allocations`, `created_at`, `updated_at`) VALUES
(10, 6, 17, 'PRD-MOB-002', 'Mobil Super 10W-40 Synthetic Blend Oil 4L', 1.00, 11200.00, 7800.00, 11200.00, '[{\"batch_id\":17,\"batch_number\":\"B26-0007-002\",\"quantity\":1,\"unit_cost\":7800,\"line_cost\":7800,\"remaining_in_batch\":14}]', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(11, 6, 18, 'PRD-DEN-003', 'Denso Premium Engine Oil Filter - Toyota/Nissan', 1.00, 2800.00, 1687.50, 2800.00, '[{\"batch_id\":18,\"batch_number\":\"B26-0008-001\",\"quantity\":1,\"unit_cost\":1687.5,\"line_cost\":1687.5,\"remaining_in_batch\":28}]', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(12, 7, 26, 'PRD-BOS-011', 'Bosch Aerotwin Premium Wiper Blade Set', 1.00, 4800.00, 3100.00, 4800.00, '[{\"batch_id\":25,\"batch_number\":\"B26-0010-003\",\"quantity\":1,\"unit_cost\":3100,\"line_cost\":3100,\"remaining_in_batch\":14}]', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(13, 7, 27, 'PRD-WUR-012', 'Wurth Multi-Purpose Penetrating Spray 400ml', 1.00, 2100.00, 1300.00, 2100.00, '[{\"batch_id\":29,\"batch_number\":\"B26-0011-003\",\"quantity\":1,\"unit_cost\":1300,\"line_cost\":1300,\"remaining_in_batch\":19}]', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(14, 8, 30, 'PRD-OSR-015', 'Osram H4 12V 60/55W Halogen Headlight Bulb', 2.00, 1800.00, 1100.00, 3600.00, '[{\"batch_id\":26,\"batch_number\":\"B26-0010-004\",\"quantity\":2,\"unit_cost\":1100,\"line_cost\":2200,\"remaining_in_batch\":2}]', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(15, 8, 23, 'PRD-BOS-008', 'Bosch Laser Iridium Spark Plug Set (4-Pack)', 1.00, 7200.00, 4800.00, 7200.00, '[{\"batch_id\":24,\"batch_number\":\"B26-0010-002\",\"quantity\":1,\"unit_cost\":4800,\"line_cost\":4800,\"remaining_in_batch\":18}]', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(16, 9, 24, 'PRD-WUR-009', 'Wurth DOT 4 High Performance Brake Fluid 500ml', 1.00, 2400.00, 1500.00, 2400.00, '[{\"batch_id\":27,\"batch_number\":\"B26-0011-001\",\"quantity\":1,\"unit_cost\":1500,\"line_cost\":1500,\"remaining_in_batch\":24}]', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(17, 10, 16, 'PRD-MOB-001', 'Mobil 1 5W-30 Full Synthetic Engine Oil 4L', 1.00, 14500.00, 9500.00, 14500.00, '[{\"batch_id\":16,\"batch_number\":\"B26-0007-001\",\"quantity\":1,\"unit_cost\":9500,\"line_cost\":9500,\"remaining_in_batch\":18}]', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(18, 10, 19, 'PRD-DEN-004', 'Denso High Flow Air Filter', 1.00, 3500.00, 2200.00, 3500.00, '[{\"batch_id\":19,\"batch_number\":\"B26-0008-002\",\"quantity\":1,\"unit_cost\":2200,\"line_cost\":2200,\"remaining_in_batch\":24}]', '2026-10-20 04:40:40', '2026-10-20 04:40:40');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_number` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `daily_wage` decimal(12,2) NOT NULL,
  `joining_date` date DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `branch_id`, `employee_number`, `name`, `mobile`, `address`, `daily_wage`, `joining_date`, `position`, `is_active`, `notes`, `user_id`, `created_at`, `updated_at`) VALUES
(6, 1, 'EMP-0001', 'Nilantha Kumara', '0771234567', 'No. 45, Kandy Road, Kelaniya', 3500.00, '2026-01-10', 'Senior Mechanic', 1, NULL, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(7, 1, 'EMP-0002', 'Roshan Silva', '0754567890', 'No. 88, Galle Road, Dehiwala', 3200.00, '2026-02-15', 'Auto Electrician', 1, NULL, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(8, 1, 'EMP-0003', 'Iresha Perera', '0719876543', 'No. 12, Station Road, Ragama', 2500.00, '2026-03-01', 'Detailing Specialist', 1, NULL, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(9, 1, 'EMP-0004', 'Hasini Kumari', '0761122334', 'No. 7, Temple Road, Maharagama', 2800.00, '2026-04-01', 'Service Coordinator', 1, NULL, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(10, 1, 'EMP-0005', 'Tharindu Jayakody', '0783344556', 'No. 55, High Level Road, Nugegoda', 2200.00, '2026-05-15', 'Junior Technician', 1, NULL, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(11, 1, 'EMP-0006', 'Dinesh Madushanka', '0729988771', 'No. 90, Baseline Road, Dematagoda', 1800.00, '2026-06-01', 'Shop Helper & Lubricator', 1, NULL, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40');

-- --------------------------------------------------------

--
-- Table structure for table `employee_advances`
--

CREATE TABLE `employee_advances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `advance_number` varchar(50) NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `advance_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'cash',
  `notes` text DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `payroll_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_advances`
--

INSERT INTO `employee_advances` (`id`, `advance_number`, `employee_id`, `advance_date`, `amount`, `payment_method`, `notes`, `status`, `payroll_item_id`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 'ADV-0001', 6, '2026-09-15', 10000.00, 'cash', NULL, 'deducted', 5, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(3, 'ADV-0002', 7, '2026-10-10', 5000.00, 'cash', NULL, 'pending', NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(4, 'ADV-202610-001', 6, '2026-10-10', 15000.00, 'cash', 'Mid-month personal salary advance - to be deducted at month-end.', 'pending', NULL, 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21');

-- --------------------------------------------------------

--
-- Table structure for table `employee_attendance`
--

CREATE TABLE `employee_attendance` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `attendance_date` date NOT NULL,
  `status` varchar(30) NOT NULL,
  `payable_day_fraction` decimal(4,2) NOT NULL DEFAULT 1.00,
  `applied_daily_wage` decimal(12,2) NOT NULL,
  `daily_earnings` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notes` varchar(255) DEFAULT NULL,
  `recorded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_attendance`
--

INSERT INTO `employee_attendance` (`id`, `employee_id`, `attendance_date`, `status`, `payable_day_fraction`, `applied_daily_wage`, `daily_earnings`, `notes`, `recorded_by`, `created_at`, `updated_at`) VALUES
(69, 6, '2026-09-01', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(70, 6, '2026-09-02', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(71, 6, '2026-09-03', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(72, 6, '2026-09-04', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(73, 6, '2026-09-05', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(74, 6, '2026-09-07', 'half_day', 0.50, 3500.00, 1750.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(75, 6, '2026-09-08', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(76, 6, '2026-09-09', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(77, 6, '2026-09-10', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(78, 6, '2026-09-11', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(79, 6, '2026-09-12', 'leave', 0.00, 3500.00, 0.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(80, 6, '2026-09-14', 'half_day', 0.50, 3500.00, 1750.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(81, 6, '2026-09-15', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(82, 6, '2026-09-16', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(83, 6, '2026-09-17', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(84, 6, '2026-09-18', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(85, 6, '2026-09-19', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(86, 6, '2026-09-21', 'half_day', 0.50, 3500.00, 1750.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(87, 6, '2026-09-22', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(88, 6, '2026-09-23', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(89, 6, '2026-09-24', 'leave', 0.00, 3500.00, 0.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(90, 6, '2026-09-25', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(91, 6, '2026-10-01', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(92, 6, '2026-10-02', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(93, 6, '2026-10-03', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(94, 6, '2026-10-05', 'half_day', 0.50, 3500.00, 1750.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(95, 6, '2026-10-06', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(96, 6, '2026-10-07', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(97, 6, '2026-10-08', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(98, 6, '2026-10-09', 'absent', 0.00, 3500.00, 0.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(99, 6, '2026-10-10', 'half_day', 0.50, 3500.00, 1750.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(100, 6, '2026-10-12', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(101, 6, '2026-10-13', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(102, 6, '2026-10-14', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(103, 6, '2026-10-15', 'half_day', 0.50, 3500.00, 1750.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(104, 6, '2026-10-16', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(105, 6, '2026-10-17', 'full_day', 1.00, 3500.00, 3500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(106, 7, '2026-09-01', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(107, 7, '2026-09-02', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(108, 7, '2026-09-03', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(109, 7, '2026-09-04', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(110, 7, '2026-09-05', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(111, 7, '2026-09-07', 'half_day', 0.50, 3200.00, 1600.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(112, 7, '2026-09-08', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(113, 7, '2026-09-09', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(114, 7, '2026-09-10', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(115, 7, '2026-09-11', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(116, 7, '2026-09-12', 'leave', 0.00, 3200.00, 0.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(117, 7, '2026-09-14', 'half_day', 0.50, 3200.00, 1600.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(118, 7, '2026-09-15', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(119, 7, '2026-09-16', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(120, 7, '2026-09-17', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(121, 7, '2026-09-18', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(122, 7, '2026-09-19', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(123, 7, '2026-09-21', 'half_day', 0.50, 3200.00, 1600.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(124, 7, '2026-09-22', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(125, 7, '2026-09-23', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(126, 7, '2026-09-24', 'leave', 0.00, 3200.00, 0.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(127, 7, '2026-09-25', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(128, 7, '2026-10-01', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(129, 7, '2026-10-02', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(130, 7, '2026-10-03', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(131, 7, '2026-10-05', 'half_day', 0.50, 3200.00, 1600.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(132, 7, '2026-10-06', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(133, 7, '2026-10-07', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(134, 7, '2026-10-08', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(135, 7, '2026-10-09', 'absent', 0.00, 3200.00, 0.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(136, 7, '2026-10-10', 'half_day', 0.50, 3200.00, 1600.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(137, 7, '2026-10-12', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(138, 7, '2026-10-13', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(139, 7, '2026-10-14', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(140, 7, '2026-10-15', 'half_day', 0.50, 3200.00, 1600.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(141, 7, '2026-10-16', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(142, 7, '2026-10-17', 'full_day', 1.00, 3200.00, 3200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(143, 8, '2026-09-01', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(144, 8, '2026-09-02', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(145, 8, '2026-09-03', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(146, 8, '2026-09-04', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(147, 8, '2026-09-05', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(148, 8, '2026-09-07', 'half_day', 0.50, 2500.00, 1250.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(149, 8, '2026-09-08', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(150, 8, '2026-09-09', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(151, 8, '2026-09-10', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(152, 8, '2026-09-11', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(153, 8, '2026-09-12', 'leave', 0.00, 2500.00, 0.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(154, 8, '2026-09-14', 'half_day', 0.50, 2500.00, 1250.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(155, 8, '2026-09-15', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(156, 8, '2026-09-16', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(157, 8, '2026-09-17', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(158, 8, '2026-09-18', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(159, 8, '2026-09-19', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(160, 8, '2026-09-21', 'half_day', 0.50, 2500.00, 1250.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(161, 8, '2026-09-22', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(162, 8, '2026-09-23', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(163, 8, '2026-09-24', 'leave', 0.00, 2500.00, 0.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(164, 8, '2026-09-25', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(165, 8, '2026-10-01', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(166, 8, '2026-10-02', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(167, 8, '2026-10-03', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(168, 8, '2026-10-05', 'half_day', 0.50, 2500.00, 1250.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(169, 8, '2026-10-06', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(170, 8, '2026-10-07', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(171, 8, '2026-10-08', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(172, 8, '2026-10-09', 'absent', 0.00, 2500.00, 0.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(173, 8, '2026-10-10', 'half_day', 0.50, 2500.00, 1250.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(174, 8, '2026-10-12', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(175, 8, '2026-10-13', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(176, 8, '2026-10-14', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(177, 8, '2026-10-15', 'half_day', 0.50, 2500.00, 1250.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(178, 8, '2026-10-16', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(179, 8, '2026-10-17', 'full_day', 1.00, 2500.00, 2500.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(180, 9, '2026-09-01', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(181, 9, '2026-09-02', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(182, 9, '2026-09-03', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(183, 9, '2026-09-04', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(184, 9, '2026-09-05', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(185, 9, '2026-09-07', 'half_day', 0.50, 2800.00, 1400.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(186, 9, '2026-09-08', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(187, 9, '2026-09-09', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(188, 9, '2026-09-10', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(189, 9, '2026-09-11', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(190, 9, '2026-09-12', 'leave', 0.00, 2800.00, 0.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(191, 9, '2026-09-14', 'half_day', 0.50, 2800.00, 1400.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(192, 9, '2026-09-15', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(193, 9, '2026-09-16', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(194, 9, '2026-09-17', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(195, 9, '2026-09-18', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(196, 9, '2026-09-19', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(197, 9, '2026-09-21', 'half_day', 0.50, 2800.00, 1400.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(198, 9, '2026-09-22', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(199, 9, '2026-09-23', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(200, 9, '2026-09-24', 'leave', 0.00, 2800.00, 0.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(201, 9, '2026-09-25', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(202, 9, '2026-10-01', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(203, 9, '2026-10-02', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(204, 9, '2026-10-03', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(205, 9, '2026-10-05', 'half_day', 0.50, 2800.00, 1400.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(206, 9, '2026-10-06', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(207, 9, '2026-10-07', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(208, 9, '2026-10-08', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(209, 9, '2026-10-09', 'absent', 0.00, 2800.00, 0.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(210, 9, '2026-10-10', 'half_day', 0.50, 2800.00, 1400.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(211, 9, '2026-10-12', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(212, 9, '2026-10-13', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(213, 9, '2026-10-14', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(214, 9, '2026-10-15', 'half_day', 0.50, 2800.00, 1400.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(215, 9, '2026-10-16', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(216, 9, '2026-10-17', 'full_day', 1.00, 2800.00, 2800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(217, 10, '2026-09-01', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(218, 10, '2026-09-02', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(219, 10, '2026-09-03', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(220, 10, '2026-09-04', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(221, 10, '2026-09-05', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(222, 10, '2026-09-07', 'half_day', 0.50, 2200.00, 1100.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(223, 10, '2026-09-08', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(224, 10, '2026-09-09', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(225, 10, '2026-09-10', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(226, 10, '2026-09-11', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(227, 10, '2026-09-12', 'leave', 0.00, 2200.00, 0.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(228, 10, '2026-09-14', 'half_day', 0.50, 2200.00, 1100.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(229, 10, '2026-09-15', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(230, 10, '2026-09-16', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(231, 10, '2026-09-17', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(232, 10, '2026-09-18', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(233, 10, '2026-09-19', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(234, 10, '2026-09-21', 'half_day', 0.50, 2200.00, 1100.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(235, 10, '2026-09-22', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(236, 10, '2026-09-23', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(237, 10, '2026-09-24', 'leave', 0.00, 2200.00, 0.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(238, 10, '2026-09-25', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(239, 10, '2026-10-01', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(240, 10, '2026-10-02', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(241, 10, '2026-10-03', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(242, 10, '2026-10-05', 'half_day', 0.50, 2200.00, 1100.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(243, 10, '2026-10-06', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(244, 10, '2026-10-07', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(245, 10, '2026-10-08', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(246, 10, '2026-10-09', 'absent', 0.00, 2200.00, 0.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(247, 10, '2026-10-10', 'half_day', 0.50, 2200.00, 1100.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(248, 10, '2026-10-12', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(249, 10, '2026-10-13', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(250, 10, '2026-10-14', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(251, 10, '2026-10-15', 'half_day', 0.50, 2200.00, 1100.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(252, 10, '2026-10-16', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(253, 10, '2026-10-17', 'full_day', 1.00, 2200.00, 2200.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(254, 11, '2026-09-01', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(255, 11, '2026-09-02', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(256, 11, '2026-09-03', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(257, 11, '2026-09-04', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(258, 11, '2026-09-05', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(259, 11, '2026-09-07', 'half_day', 0.50, 1800.00, 900.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(260, 11, '2026-09-08', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(261, 11, '2026-09-09', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(262, 11, '2026-09-10', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(263, 11, '2026-09-11', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(264, 11, '2026-09-12', 'leave', 0.00, 1800.00, 0.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(265, 11, '2026-09-14', 'half_day', 0.50, 1800.00, 900.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(266, 11, '2026-09-15', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(267, 11, '2026-09-16', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(268, 11, '2026-09-17', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(269, 11, '2026-09-18', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(270, 11, '2026-09-19', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(271, 11, '2026-09-21', 'half_day', 0.50, 1800.00, 900.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(272, 11, '2026-09-22', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(273, 11, '2026-09-23', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(274, 11, '2026-09-24', 'leave', 0.00, 1800.00, 0.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(275, 11, '2026-09-25', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(276, 11, '2026-10-01', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(277, 11, '2026-10-02', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(278, 11, '2026-10-03', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(279, 11, '2026-10-05', 'half_day', 0.50, 1800.00, 900.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(280, 11, '2026-10-06', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(281, 11, '2026-10-07', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(282, 11, '2026-10-08', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(283, 11, '2026-10-09', 'absent', 0.00, 1800.00, 0.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(284, 11, '2026-10-10', 'half_day', 0.50, 1800.00, 900.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(285, 11, '2026-10-12', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(286, 11, '2026-10-13', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(287, 11, '2026-10-14', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(288, 11, '2026-10-15', 'half_day', 0.50, 1800.00, 900.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(289, 11, '2026-10-16', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(290, 11, '2026-10-17', 'full_day', 1.00, 1800.00, 1800.00, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40');

-- --------------------------------------------------------

--
-- Table structure for table `employee_wage_rates`
--

CREATE TABLE `employee_wage_rates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `daily_wage` decimal(12,2) NOT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_wage_rates`
--

INSERT INTO `employee_wage_rates` (`id`, `employee_id`, `daily_wage`, `effective_from`, `effective_to`, `reason`, `created_at`, `updated_at`) VALUES
(5, 6, 3500.00, '2026-01-10', NULL, 'Initial approved daily wage rate.', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(6, 7, 3200.00, '2026-02-15', NULL, 'Initial approved daily wage rate.', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(7, 8, 2500.00, '2026-03-01', NULL, 'Initial approved daily wage rate.', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(8, 9, 2800.00, '2026-04-01', NULL, 'Initial approved daily wage rate.', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(9, 10, 2200.00, '2026-05-15', NULL, 'Initial approved daily wage rate.', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(10, 11, 1800.00, '2026-06-01', NULL, 'Initial approved daily wage rate.', '2026-10-20 04:40:40', '2026-10-20 04:40:40');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `expense_number` varchar(50) NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `expense_type` varchar(30) NOT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_advance_id` bigint(20) UNSIGNED DEFAULT NULL,
  `expense_date` date NOT NULL,
  `related_month` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `paid_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `balance_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_status` varchar(30) NOT NULL DEFAULT 'paid',
  `payment_method` varchar(50) NOT NULL DEFAULT 'cash',
  `payment_date` date DEFAULT NULL,
  `payment_reference` varchar(100) DEFAULT NULL,
  `receipt_path` varchar(255) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'active',
  `void_reason` varchar(255) DEFAULT NULL,
  `voided_by` bigint(20) UNSIGNED DEFAULT NULL,
  `voided_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `branch_id`, `expense_number`, `category_id`, `expense_type`, `employee_id`, `employee_advance_id`, `expense_date`, `related_month`, `description`, `amount`, `paid_amount`, `balance_amount`, `payment_status`, `payment_method`, `payment_date`, `payment_reference`, `receipt_path`, `status`, `void_reason`, `voided_by`, `voided_at`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'EXP-0001', 10, 'shop', NULL, NULL, '2026-09-05', '2026-09', 'Colombo Workshop Main Building Monthly Rental', 75000.00, 75000.00, 0.00, 'paid', 'bank_transfer', '2026-09-05', NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(2, 1, 'EXP-0002', 3, 'shop', NULL, NULL, '2026-09-12', '2026-09', 'CEB 3-Phase Commercial Electricity Bill Payment', 38500.00, 38500.00, 0.00, 'paid', 'bank_transfer', '2026-09-12', NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(3, 1, 'EXP-0003', 8, 'shop', NULL, NULL, '2026-09-18', '2026-09', 'Air compressor hoses, connectors and solvent cleaner', 14200.00, 14200.00, 0.00, 'paid', 'cash', '2026-09-18', NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(4, 1, 'EXP-0004', 5, 'shop', NULL, NULL, '2026-09-22', '2026-09', 'Waiting lounge tea, coffee and mineral water cans', 5800.00, 5800.00, 0.00, 'paid', 'cash', '2026-09-22', NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(5, 1, 'EXP-0005', 9, 'shop', NULL, NULL, '2026-09-28', '2026-09', 'Laser 4-wheel alignment machine sensor re-calibration', 22000.00, 22000.00, 0.00, 'paid', 'cheque', '2026-09-28', NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(6, 1, 'EXP-0006', 1, 'staff', 6, 2, '2026-09-15', '2026-09', 'Mid-month salary advance for Nilantha Kumara', 10000.00, 10000.00, 0.00, 'paid', 'cash', '2026-09-15', NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(7, 1, 'EXP-0007', 11, 'shop', NULL, NULL, '2026-09-29', '2026-09', 'A4 job card printing paper, invoice envelopes and ink', 6400.00, 6400.00, 0.00, 'paid', 'cash', '2026-09-29', NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(8, 1, 'EXP-0008', 11, 'shop', NULL, NULL, '2026-10-02', '2026-10', 'Fuel reimbursement for picking up urgent filters from Panchikawatta', 8500.00, 8500.00, 0.00, 'paid', 'cash', '2026-10-02', NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(9, 1, 'EXP-0009', 10, 'shop', NULL, NULL, '2026-10-05', '2026-10', 'Colombo Workshop October Rental Payment', 75000.00, 75000.00, 0.00, 'paid', 'bank_transfer', '2026-10-05', NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(10, 1, 'EXP-0010', 6, 'shop', NULL, NULL, '2026-10-08', '2026-10', 'October Water Supply & Municipal Drainage Charges', 41200.00, 41200.00, 0.00, 'paid', 'bank_transfer', '2026-10-08', NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(11, 1, 'EXP-0011', 1, 'staff', 7, 3, '2026-10-10', '2026-10', 'Medical advance for Roshan Silva', 5000.00, 5000.00, 0.00, 'paid', 'cash', '2026-10-10', NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(12, 1, 'EXP-0012', 8, 'shop', NULL, NULL, '2026-10-12', '2026-10', 'Safety gloves, cotton waste rags and degreaser cans', 11500.00, 11500.00, 0.00, 'paid', 'cash', '2026-10-12', NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(13, 1, 'EXP-0013', 5, 'shop', NULL, NULL, '2026-10-14', '2026-10', 'Customer lounge snacks and air freshener refills', 6200.00, 6200.00, 0.00, 'paid', 'cash', '2026-10-14', NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(14, 1, 'EXP-0014', 11, 'shop', NULL, NULL, '2026-10-16', '2026-10', 'Company recovery truck diesel fuel', 9800.00, 9800.00, 0.00, 'paid', 'cash', '2026-10-16', NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(15, 1, 'EXP-0015', 11, 'shop', NULL, NULL, '2026-10-18', '2026-10', 'Workshop first aid box medicine restock', 4500.00, 4500.00, 0.00, 'paid', 'cash', '2026-10-18', NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(16, 1, 'EXP-202609-0001', 12, 'shop', NULL, NULL, '2026-09-28', '2026-09', 'CEB Electricity Bill - September 2026', 24500.00, 24500.00, 0.00, 'paid', 'bank_transfer', NULL, NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21'),
(17, 1, 'EXP-202609-0002', 13, 'shop', NULL, NULL, '2026-09-28', '2026-09', 'National Water Board - September 2026', 4200.00, 4200.00, 0.00, 'paid', 'cash', NULL, NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21'),
(18, 1, 'EXP-202609-0003', 14, 'shop', NULL, NULL, '2026-09-25', '2026-09', 'SLT Fiber Broadband & Telephone - Sep 2026', 6800.00, 6800.00, 0.00, 'paid', 'card', NULL, NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21'),
(19, 1, 'EXP-202609-0004', 15, 'shop', NULL, NULL, '2026-09-15', '2026-09', 'Hydraulic Hoist #2 Servicing & Seal Repair', 12000.00, 12000.00, 0.00, 'paid', 'cash', NULL, NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21'),
(20, 1, 'EXP-202609-0005', 16, 'shop', NULL, NULL, '2026-09-12', '2026-09', 'Degreaser & Workshop Floor Wash Chemicals', 5500.00, 5500.00, 0.00, 'paid', 'cash', NULL, NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21'),
(21, 1, 'EXP-202610-0010', 12, 'shop', NULL, NULL, '2026-10-18', '2026-10', 'CEB Electricity Bill - October 2026', 26000.00, 26000.00, 0.00, 'paid', 'bank_transfer', NULL, NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21'),
(22, 1, 'EXP-202610-0011', 13, 'shop', NULL, NULL, '2026-10-18', '2026-10', 'Water Utility Supply - October 2026', 4500.00, 4500.00, 0.00, 'paid', 'cash', NULL, NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21'),
(23, 1, 'EXP-202610-0012', 14, 'shop', NULL, NULL, '2026-10-15', '2026-10', 'Telecom Broadband - October 2026', 7200.00, 7200.00, 0.00, 'paid', 'card', NULL, NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21'),
(24, 1, 'EXP-202610-0013', 15, 'shop', NULL, NULL, '2026-10-09', '2026-10', 'Wheel Balancer Calibration & Sensor Maintenance', 15000.00, 15000.00, 0.00, 'paid', 'cash', NULL, NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21'),
(25, 1, 'EXP-202610-0014', 16, 'shop', NULL, NULL, '2026-10-04', '2026-10', 'Bulk Microfiber Towels & Surface Cleaners', 6200.00, 6200.00, 0.00, 'paid', 'cash', NULL, NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21'),
(26, 1, 'EXP-202610-ADV01', 1, 'shop', NULL, 4, '2026-10-10', '2026-10', 'Salary Advance - ', 15000.00, 15000.00, 0.00, 'paid', 'cash', NULL, NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21');

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_categories`
--

INSERT INTO `expense_categories` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Salary Advance', 1, '2026-10-20 01:08:58', '2026-10-20 01:08:58'),
(2, 'Boss Expenses', 1, '2026-10-20 01:08:58', '2026-10-20 01:08:58'),
(3, 'Electricity', 1, '2026-10-20 01:08:58', '2026-10-20 01:08:58'),
(4, 'EPF / ETF', 1, '2026-10-20 01:08:58', '2026-10-20 01:08:58'),
(5, 'Staff Welfare', 1, '2026-10-20 01:08:58', '2026-10-20 01:08:58'),
(6, 'Water', 1, '2026-10-20 01:08:58', '2026-10-20 01:08:58'),
(7, 'Telephone / Internet', 1, '2026-10-20 01:08:58', '2026-10-20 01:08:58'),
(8, 'Chemicals', 1, '2026-10-20 01:08:58', '2026-10-20 01:08:58'),
(9, 'Repair', 1, '2026-10-20 01:08:58', '2026-10-20 01:08:58'),
(10, 'Maintenance', 1, '2026-10-20 01:08:58', '2026-10-20 01:08:58'),
(11, 'Other', 1, '2026-10-20 01:08:58', '2026-10-20 01:08:58'),
(12, 'Electricity & Power', 1, '2026-10-20 07:12:28', '2026-10-20 07:12:28'),
(13, 'Water Utilities', 1, '2026-10-20 07:12:28', '2026-10-20 07:12:28'),
(14, 'Telephone & Internet', 1, '2026-10-20 07:12:28', '2026-10-20 07:12:28'),
(15, 'Repairs & Maintenance', 1, '2026-10-20 07:12:28', '2026-10-20 07:12:28'),
(16, 'Workshop Consumables & Chemicals', 1, '2026-10-20 07:12:28', '2026-10-20 07:12:28');

-- --------------------------------------------------------

--
-- Table structure for table `expense_payments`
--

CREATE TABLE `expense_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `expense_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_number` varchar(50) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'cash',
  `reference_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_payments`
--

INSERT INTO `expense_payments` (`id`, `expense_id`, `branch_id`, `payment_number`, `payment_date`, `amount`, `payment_method`, `reference_number`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'EPAY-INIT-00001', '2026-09-05', 75000.00, 'bank_transfer', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(2, 2, 1, 'EPAY-INIT-00002', '2026-09-12', 38500.00, 'bank_transfer', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(3, 3, 1, 'EPAY-INIT-00003', '2026-09-18', 14200.00, 'cash', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(4, 4, 1, 'EPAY-INIT-00004', '2026-09-22', 5800.00, 'cash', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(5, 5, 1, 'EPAY-INIT-00005', '2026-09-28', 22000.00, 'cheque', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(6, 6, 1, 'EPAY-INIT-00006', '2026-09-15', 10000.00, 'cash', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(7, 7, 1, 'EPAY-INIT-00007', '2026-09-29', 6400.00, 'cash', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(8, 8, 1, 'EPAY-INIT-00008', '2026-10-02', 8500.00, 'cash', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(9, 9, 1, 'EPAY-INIT-00009', '2026-10-05', 75000.00, 'bank_transfer', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(10, 10, 1, 'EPAY-INIT-00010', '2026-10-08', 41200.00, 'bank_transfer', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(11, 11, 1, 'EPAY-INIT-00011', '2026-10-10', 5000.00, 'cash', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(12, 12, 1, 'EPAY-INIT-00012', '2026-10-12', 11500.00, 'cash', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(13, 13, 1, 'EPAY-INIT-00013', '2026-10-14', 6200.00, 'cash', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(14, 14, 1, 'EPAY-INIT-00014', '2026-10-16', 9800.00, 'cash', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(15, 15, 1, 'EPAY-INIT-00015', '2026-10-18', 4500.00, 'cash', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(16, 16, 1, 'EPAY-INIT-00016', '2026-09-28', 24500.00, 'bank_transfer', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21'),
(17, 17, 1, 'EPAY-INIT-00017', '2026-09-28', 4200.00, 'cash', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21'),
(18, 18, 1, 'EPAY-INIT-00018', '2026-09-25', 6800.00, 'card', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21'),
(19, 19, 1, 'EPAY-INIT-00019', '2026-09-15', 12000.00, 'cash', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21'),
(20, 20, 1, 'EPAY-INIT-00020', '2026-09-12', 5500.00, 'cash', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21'),
(21, 21, 1, 'EPAY-INIT-00021', '2026-10-18', 26000.00, 'bank_transfer', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21'),
(22, 22, 1, 'EPAY-INIT-00022', '2026-10-18', 4500.00, 'cash', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21'),
(23, 23, 1, 'EPAY-INIT-00023', '2026-10-15', 7200.00, 'card', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21'),
(24, 24, 1, 'EPAY-INIT-00024', '2026-10-09', 15000.00, 'cash', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21'),
(25, 25, 1, 'EPAY-INIT-00025', '2026-10-04', 6200.00, 'cash', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21'),
(26, 26, 1, 'EPAY-INIT-00026', '2026-10-10', 15000.00, 'cash', NULL, 'Initial payment recorded with expense', 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `goods_received_notes`
--

CREATE TABLE `goods_received_notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `grn_number` varchar(255) NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_invoice_number` varchar(255) DEFAULT NULL,
  `received_date` date NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `invoice_attachment` varchar(255) DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `posted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `goods_received_notes`
--

INSERT INTO `goods_received_notes` (`id`, `branch_id`, `grn_number`, `supplier_id`, `supplier_invoice_number`, `received_date`, `status`, `total_amount`, `notes`, `invoice_attachment`, `posted_at`, `posted_by`, `created_by`, `created_at`, `updated_at`) VALUES
(7, 1, 'GRN-202609-0001', 7, 'INV-MOB-8891', '2026-09-05', 'confirmed', 307000.00, 'Received verified auto care inventory batch.', NULL, '2026-10-20 04:40:40', 2, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(8, 1, 'GRN-202609-0002', 8, 'INV-DEN-5542', '2026-09-10', 'confirmed', 139000.00, 'Received verified auto care inventory batch.', NULL, '2026-10-20 04:40:40', 2, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(9, 1, 'GRN-202609-0003', 9, 'INV-FER-3310', '2026-09-15', 'confirmed', 207600.00, 'Received verified auto care inventory batch.', NULL, '2026-10-20 04:40:40', 2, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(10, 1, 'GRN-202609-0004', 10, 'INV-BOS-9901', '2026-09-20', 'confirmed', 406900.00, 'Received verified auto care inventory batch.', NULL, '2026-10-20 04:40:40', 2, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(11, 1, 'GRN-202609-0005', 11, 'INV-WUR-7721', '2026-09-25', 'confirmed', 99500.00, 'Received verified auto care inventory batch.', NULL, '2026-10-20 04:40:40', 2, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(12, 1, 'GRN-202610-0006', 7, 'INV-MOB-9944', '2026-10-10', 'confirmed', 147000.00, 'Received verified auto care inventory batch.', NULL, '2026-10-20 04:40:40', 2, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(13, 1, 'GRN-202609-DEN01', 7, 'INV-DEN-0911', '2026-09-01', 'confirmed', 16000.00, NULL, NULL, '2026-09-01 04:30:00', 2, 2, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(14, 1, 'GRN-202610-DEN02', 7, 'INV-DEN-1022', '2026-10-01', 'confirmed', 10000.00, NULL, NULL, '2026-10-01 04:30:00', 2, 2, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(15, 3, 'GRN-202610-KND01', 7, 'INV-FER-KND1', '2026-10-03', 'confirmed', 65000.00, NULL, NULL, '2026-10-03 04:30:00', 2, 2, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(16, 1, 'GRN-202609-FLT01', 7, 'INV-SUP-FLT01', '2026-09-01', 'confirmed', 12000.00, NULL, NULL, '2026-09-01 04:30:00', 2, 2, '2026-10-20 07:28:24', '2026-10-20 07:28:24'),
(17, 1, 'GRN-202610-FLT02', 7, 'INV-SUP-FLT02', '2026-10-01', 'confirmed', 8000.00, NULL, NULL, '2026-10-01 04:30:00', 2, 2, '2026-10-20 07:28:24', '2026-10-20 07:28:24'),
(18, 1, 'GRN-202609-OIL01', 7, 'INV-SUP-OIL01', '2026-09-02', 'confirmed', 30000.00, NULL, NULL, '2026-09-02 04:30:00', 2, 2, '2026-10-20 07:28:24', '2026-10-20 07:28:24'),
(19, 1, 'GRN-202610-OIL02', 7, 'INV-SUP-OIL02', '2026-10-02', 'confirmed', 48000.00, NULL, NULL, '2026-10-02 04:30:00', 2, 2, '2026-10-20 07:28:24', '2026-10-20 07:28:24');

-- --------------------------------------------------------

--
-- Table structure for table `goods_received_note_items`
--

CREATE TABLE `goods_received_note_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `grn_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_quantity` decimal(10,2) NOT NULL,
  `free_quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `purchase_unit_price` decimal(12,2) NOT NULL,
  `selling_price_snapshot` decimal(12,2) DEFAULT NULL,
  `line_total` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `goods_received_note_items`
--

INSERT INTO `goods_received_note_items` (`id`, `grn_id`, `product_id`, `purchase_quantity`, `free_quantity`, `purchase_unit_price`, `selling_price_snapshot`, `line_total`, `created_at`, `updated_at`) VALUES
(16, 7, 16, 20.00, 0.00, 9500.00, 14500.00, 190000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(17, 7, 17, 15.00, 0.00, 7800.00, 11200.00, 117000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(18, 8, 18, 30.00, 2.00, 1800.00, 2800.00, 54000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(19, 8, 19, 25.00, 0.00, 2200.00, 3500.00, 55000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(20, 8, 28, 15.00, 0.00, 2000.00, 3200.00, 30000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(21, 9, 20, 18.00, 0.00, 6200.00, 9500.00, 111600.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(22, 9, 21, 12.00, 0.00, 8000.00, 12000.00, 96000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(23, 10, 22, 10.00, 0.00, 26000.00, 36000.00, 260000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(24, 10, 23, 20.00, 0.00, 4800.00, 7200.00, 96000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(25, 10, 26, 15.00, 0.00, 3100.00, 4800.00, 46500.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(26, 10, 30, 4.00, 0.00, 1100.00, 1800.00, 4400.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(27, 11, 24, 25.00, 0.00, 1500.00, 2400.00, 37500.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(28, 11, 25, 20.00, 0.00, 1800.00, 2900.00, 36000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(29, 11, 27, 20.00, 0.00, 1300.00, 2100.00, 26000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(30, 12, 16, 15.00, 0.00, 9800.00, 14500.00, 147000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(31, 13, 18, 20.00, 0.00, 800.00, 2800.00, 16000.00, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(32, 14, 18, 10.00, 0.00, 1000.00, 2800.00, 10000.00, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(33, 15, 20, 10.00, 0.00, 6500.00, 9500.00, 65000.00, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(34, 16, 31, 15.00, 0.00, 800.00, 1800.00, 12000.00, '2026-10-20 07:28:24', '2026-10-20 07:28:24'),
(35, 17, 31, 8.00, 0.00, 1000.00, 1800.00, 8000.00, '2026-10-20 07:28:24', '2026-10-20 07:28:24'),
(36, 18, 32, 20.00, 0.00, 1500.00, 2200.00, 30000.00, '2026-10-20 07:28:24', '2026-10-20 07:28:24'),
(37, 19, 32, 30.00, 0.00, 1600.00, 2200.00, 48000.00, '2026-10-20 07:28:24', '2026-10-20 07:28:24');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invoice_number` varchar(100) NOT NULL,
  `invoice_date` date NOT NULL,
  `original_invoice_date` date DEFAULT NULL,
  `date_changed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `date_changed_at` timestamp NULL DEFAULT NULL,
  `date_change_reason` text DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `quotation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `job_card_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invoice_type` varchar(50) NOT NULL DEFAULT 'job_card',
  `subject` varchar(255) DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_type` enum('none','percentage','fixed') NOT NULL DEFAULT 'none',
  `discount_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `additional_charges` decimal(15,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `amount_paid` decimal(15,2) NOT NULL DEFAULT 0.00,
  `balance_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','issued','partially_paid','paid','cancelled') NOT NULL DEFAULT 'issued',
  `payment_method` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `cancelled_by` bigint(20) UNSIGNED DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `terms_conditions` text DEFAULT NULL,
  `customer_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`customer_snapshot`)),
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `branch_id`, `invoice_number`, `invoice_date`, `original_invoice_date`, `date_changed_by`, `date_changed_at`, `date_change_reason`, `due_date`, `customer_id`, `quotation_id`, `job_card_id`, `invoice_type`, `subject`, `reference`, `subtotal`, `discount_amount`, `discount_type`, `discount_value`, `tax_amount`, `tax_percentage`, `additional_charges`, `grand_total`, `amount_paid`, `balance_amount`, `status`, `payment_method`, `notes`, `cancellation_reason`, `cancelled_by`, `cancelled_at`, `terms_conditions`, `customer_snapshot`, `created_by`, `created_at`, `updated_at`) VALUES
(9, 1, 'INV-202610-0001', '2026-10-15', NULL, NULL, NULL, NULL, NULL, 15, NULL, NULL, 'service', NULL, NULL, 8200.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 8200.00, 8200.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(10, 1, 'INV-202610-0002', '2026-10-12', NULL, NULL, NULL, NULL, NULL, 19, NULL, NULL, 'service', NULL, NULL, 18500.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 18500.00, 0.00, 18500.00, 'issued', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(11, 1, 'INV-202610-0003', '2026-10-14', NULL, NULL, NULL, NULL, NULL, 19, NULL, NULL, 'direct_sale', NULL, NULL, 14800.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 14800.00, 0.00, 14800.00, 'issued', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(12, 1, 'INV-202610-0004', '2026-10-16', NULL, NULL, NULL, NULL, NULL, 19, NULL, NULL, 'service', NULL, NULL, 32000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 32000.00, 12000.00, 20000.00, 'partially_paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(13, 1, 'INV-202610-0005', '2026-10-18', NULL, NULL, NULL, NULL, NULL, 11, NULL, NULL, 'direct_sale', NULL, NULL, 18000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 18000.00, 18000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(14, 1, 'INV-202610-0006', '2026-10-18', NULL, NULL, NULL, NULL, NULL, 11, NULL, NULL, 'service', NULL, NULL, 6300.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 6300.00, 6300.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(15, 3, 'INV-202610-0007', '2026-10-15', NULL, NULL, NULL, NULL, NULL, 12, NULL, NULL, 'service', NULL, NULL, 12500.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 12500.00, 0.00, 12500.00, 'issued', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(16, 1, 'INV-202610-0008', '2026-10-19', NULL, NULL, NULL, NULL, NULL, 13, NULL, NULL, 'service', NULL, NULL, 25000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 25000.00, 0.00, 25000.00, 'issued', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(17, 1, 'INV-202610-0009', '2026-10-20', NULL, NULL, NULL, NULL, '2026-10-20', 21, NULL, 25, 'job_card', 'Service Invoice for BFH-4242 (Job #JOB-202610-0013)', 'JOB-202610-0013', 1200.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 1200.00, 1200.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, '{\"customer_name\":\"Shehan\",\"phone\":\"0714624024\",\"vehicle_number\":\"BFH-4242\",\"vehicle_type\":\"Motorbike\",\"current_mileage\":3400,\"next_service_mileage\":6900}', 2, '2026-10-20 04:55:30', '2026-10-20 07:08:59'),
(18, 1, 'INV-202609-0001', '2026-09-05', NULL, NULL, NULL, NULL, '2026-10-05', 22, NULL, 26, 'job_card', NULL, NULL, 12000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 12000.00, 12000.00, 0.00, 'paid', 'bank_transfer', NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(19, 1, 'INV-202610-0010', '2026-10-05', NULL, NULL, NULL, NULL, '2026-11-04', 22, NULL, 22, 'job_card', NULL, NULL, 15000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 15000.00, 15000.00, 0.00, 'paid', 'bank_transfer', NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(20, 1, 'INV-202610-0011', '2026-10-10', NULL, NULL, NULL, NULL, '2026-11-09', 22, NULL, 23, 'job_card', NULL, NULL, 8000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 8000.00, 5000.00, 3000.00, 'partially_paid', 'bank_transfer', NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(21, 1, 'INV-202610-0012', '2026-10-14', NULL, NULL, NULL, NULL, '2026-11-13', 22, NULL, 24, 'job_card', NULL, NULL, 12000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 12000.00, 0.00, 12000.00, 'issued', 'credit', NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 07:11:44', '2026-10-20 07:11:44'),
(22, 1, 'INV-202610-0013', '2026-10-12', NULL, NULL, NULL, NULL, '2026-11-11', 23, NULL, 25, 'job_card', NULL, NULL, 50000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 50000.00, 0.00, 50000.00, 'issued', 'credit', 'Vehicle WP-NB-1122 Heavy Fleet Overhaul.', NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 07:11:44', '2026-10-20 07:31:48'),
(23, 1, 'INV-202610-0014', '2026-10-15', NULL, NULL, NULL, NULL, '2026-11-14', 23, NULL, 29, 'job_card', NULL, NULL, 40000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 40000.00, 0.00, 40000.00, 'issued', 'credit', 'Vehicle WP-NB-3344 Suspension & Brake Service.', NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 07:11:44', '2026-10-20 07:31:48'),
(24, 1, 'INV-202610-0015', '2026-10-08', NULL, NULL, NULL, NULL, '2026-10-22', 24, NULL, NULL, 'job_card', NULL, NULL, 18000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 18000.00, 10000.00, 8000.00, 'partially_paid', 'cash', 'Vehicle WP-CAE-4554 Hybrid Diagnostics.', NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 07:11:44', '2026-10-20 07:11:44'),
(25, 1, 'INV-202610-0016', '2026-10-16', NULL, NULL, NULL, NULL, '2026-10-30', 24, NULL, NULL, 'job_card', NULL, NULL, 12000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 12000.00, 0.00, 12000.00, 'issued', 'credit', 'Vehicle WP-CAE-4554 Brake Pad Replacement.', NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 07:11:44', '2026-10-20 07:11:44'),
(26, 1, 'INV-202609-0023', '2026-09-05', NULL, NULL, NULL, NULL, '2026-10-05', 20, NULL, NULL, 'job_card', NULL, NULL, 18500.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 18500.00, 0.00, 18500.00, 'issued', 'credit', NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 07:11:45', '2026-10-20 07:28:24'),
(27, 1, 'INV-202608-0001', '2026-08-05', NULL, NULL, NULL, NULL, '2026-09-05', 20, NULL, NULL, 'job_card', NULL, NULL, 22000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 22000.00, 0.00, 22000.00, 'issued', 'credit', NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 07:11:45', '2026-10-20 07:28:24'),
(28, 1, 'INV-202607-0001', '2026-07-06', NULL, NULL, NULL, NULL, '2026-08-05', 20, NULL, NULL, 'job_card', NULL, NULL, 28000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 28000.00, 0.00, 28000.00, 'issued', 'credit', NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 07:11:45', '2026-10-20 07:28:24'),
(29, 1, 'INV-202605-0001', '2026-05-15', NULL, NULL, NULL, NULL, '2026-06-15', 20, NULL, NULL, 'job_card', NULL, NULL, 35000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 35000.00, 0.00, 35000.00, 'issued', 'credit', NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 07:11:45', '2026-10-20 07:28:24'),
(30, 1, 'INV-202610-0017', '2026-10-02', NULL, NULL, NULL, NULL, '2026-10-02', 16, NULL, NULL, 'direct_sale', NULL, NULL, 6500.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 6500.00, 6500.00, 0.00, 'paid', 'cash', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-20 07:11:45', '2026-10-20 07:11:45'),
(31, 1, 'INV-202610-0018', '2026-10-07', NULL, NULL, NULL, NULL, '2026-10-07', 13, NULL, NULL, 'direct_sale', NULL, NULL, 12500.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 12500.00, 12500.00, 0.00, 'paid', 'card', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-20 07:11:45', '2026-10-20 07:11:45'),
(32, 1, 'INV-202610-0019', '2026-10-11', NULL, NULL, NULL, NULL, '2026-10-11', 15, NULL, NULL, 'direct_sale', NULL, NULL, 21000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 21000.00, 21000.00, 0.00, 'paid', 'bank_transfer', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-20 07:11:45', '2026-10-20 07:11:45'),
(33, 1, 'INV-202610-0020', '2026-10-15', NULL, NULL, NULL, NULL, '2026-10-15', 17, NULL, NULL, 'direct_sale', NULL, NULL, 15000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 15000.00, 15000.00, 0.00, 'paid', 'cheque', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-20 07:11:45', '2026-10-20 07:11:45'),
(34, 1, 'INV-202610-0021', '2026-10-19', NULL, NULL, NULL, NULL, '2026-10-19', 16, NULL, NULL, 'direct_sale', NULL, NULL, 9000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 9000.00, 0.00, 9000.00, 'cancelled', NULL, NULL, 'Customer cancelled item selection prior to counter packaging.', 2, '2026-10-19 09:00:00', NULL, NULL, 1, '2026-10-20 07:11:45', '2026-10-20 07:11:45'),
(35, 3, 'INV-202610-0022', '2026-10-20', NULL, NULL, NULL, NULL, '2026-10-20', 24, NULL, NULL, 'direct_sale', NULL, NULL, 14500.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 14500.00, 14500.00, 0.00, 'paid', 'cash', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-20 07:11:45', '2026-10-20 07:11:45'),
(36, 1, 'INV-202609-0005', '2026-09-25', NULL, NULL, NULL, NULL, '2026-09-25', 13, NULL, NULL, 'direct_sale', NULL, NULL, 11000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 11000.00, 11000.00, 0.00, 'paid', 'cheque', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-20 07:11:45', '2026-10-20 07:11:45'),
(37, 1, 'INV-202610-AG01', '2026-10-10', NULL, NULL, NULL, NULL, '2026-11-10', 20, NULL, NULL, 'job_card', NULL, NULL, 15000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 15000.00, 0.00, 15000.00, 'issued', 'credit', NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 07:28:24', '2026-10-20 07:28:24'),
(38, 1, 'INV-202610-0030', '2026-10-17', NULL, NULL, NULL, NULL, '2026-10-17', 24, NULL, NULL, 'direct_sale', NULL, NULL, 20000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 20000.00, 20000.00, 0.00, 'paid', 'cash', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-20 07:28:47', '2026-10-20 07:34:05'),
(39, 1, 'INV-202610-0031', '2026-10-17', NULL, NULL, NULL, NULL, '2026-10-17', 24, NULL, NULL, 'direct_sale', NULL, NULL, 15000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 15000.00, 15000.00, 0.00, 'paid', 'card', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-20 07:29:07', '2026-10-20 07:34:05'),
(40, 1, 'INV-202610-0032', '2026-10-17', NULL, NULL, NULL, NULL, '2026-10-17', 24, NULL, NULL, 'direct_sale', NULL, NULL, 10000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 10000.00, 10000.00, 0.00, 'paid', 'cheque', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-20 07:29:07', '2026-10-20 07:34:05'),
(41, 1, 'INV-202610-0033', '2026-10-17', NULL, NULL, NULL, NULL, '2026-11-15', 16, NULL, NULL, 'job_card', NULL, NULL, 5000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 5000.00, 0.00, 5000.00, 'issued', 'credit', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-20 07:29:07', '2026-10-20 07:34:06'),
(42, NULL, 'INV-202610-0034', '2026-10-21', NULL, NULL, NULL, NULL, '2026-10-21', 21, NULL, 30, 'job_card', 'Service Invoice for BFH-4242 (Job #JOB-202610-0015)', 'JOB-202610-0015', 25200.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 25200.00, 0.00, 25200.00, 'issued', NULL, NULL, NULL, NULL, NULL, NULL, '{\"customer_name\":\"QA Customer B\",\"phone\":\"0714624024\",\"vehicle_number\":\"BFH-4242\",\"vehicle_type\":\"Lorry\",\"current_mileage\":3400,\"next_service_mileage\":6900}', 2, '2026-10-21 04:17:47', '2026-10-21 04:17:47'),
(43, NULL, 'INV-202610-TEST999', '2026-10-21', NULL, NULL, NULL, NULL, '2026-10-21', 11, NULL, NULL, 'job_card', NULL, NULL, 19000.00, 1000.00, 'none', 0.00, 0.00, 0.00, 0.00, 18000.00, 18000.00, 0.00, 'issued', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 04:25:06', '2026-10-21 04:25:06'),
(50, 1, 'INV-202610-0035', '2026-10-21', NULL, NULL, NULL, NULL, '2026-10-21', 22, NULL, 27, 'job_card', 'Service Invoice for CAA-5678 (Job #JOB-202609-0002)', 'JOB-202609-0002', 8000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 8000.00, 0.00, 8000.00, 'issued', NULL, NULL, NULL, NULL, NULL, NULL, '{\"customer_name\":\"Sunil Fernando\",\"phone\":\"0112991122\",\"vehicle_number\":\"CAA-5678\",\"vehicle_type\":\"Car\",\"current_mileage\":28000,\"next_service_mileage\":33000}', 2, '2026-10-21 05:08:14', '2026-10-21 05:08:14'),
(52, 1, 'INV-202610-0041', '2026-10-21', NULL, NULL, NULL, NULL, '2026-10-21', 23, NULL, 28, 'job_card', 'Service Invoice for WP-NB-1122 (Job #JOB-202609-0003)', 'JOB-202609-0003', 25000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 25000.00, 0.00, 25000.00, 'issued', NULL, NULL, NULL, NULL, NULL, NULL, '{\"customer_name\":\"Dinesh Gamage\",\"phone\":\"0112883344\",\"vehicle_number\":\"WP-NB-1122\",\"vehicle_type\":\"Lorry\",\"current_mileage\":102000,\"next_service_mileage\":107000}', 2, '2026-10-21 08:26:04', '2026-10-21 08:26:04');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `source_quotation_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `item_type` enum('service','product','custom') NOT NULL DEFAULT 'service',
  `item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_code` varchar(50) DEFAULT NULL,
  `item_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` decimal(12,2) NOT NULL DEFAULT 1.00,
  `unit` varchar(50) DEFAULT NULL,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_type` enum('none','percentage','fixed') NOT NULL DEFAULT 'none',
  `discount_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unit_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `source_quotation_item_id`, `sort_order`, `item_type`, `item_id`, `item_code`, `item_name`, `description`, `quantity`, `unit`, `unit_price`, `discount_amount`, `discount_type`, `discount_value`, `tax_rate`, `tax_amount`, `line_total`, `unit_cost`, `notes`, `created_at`, `updated_at`) VALUES
(6, 10, NULL, 0, 'service', NULL, NULL, 'Van Fleet Full Lubrication & Alignment', NULL, 1.00, NULL, 18500.00, 0.00, 'none', 0.00, 0.00, 0.00, 18500.00, 0.00, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(7, 11, NULL, 0, 'product', NULL, NULL, 'Emergency Battery & Bulbs Supply', NULL, 1.00, NULL, 14800.00, 0.00, 'none', 0.00, 0.00, 0.00, 14800.00, 0.00, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(8, 12, NULL, 0, 'service', NULL, NULL, 'Corporate Fleet Multi-Van Brake Overhaul', NULL, 1.00, NULL, 32000.00, 0.00, 'none', 0.00, 0.00, 0.00, 32000.00, 0.00, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(9, 15, NULL, 0, 'service', NULL, NULL, 'Brake Master Cylinder & Fluid Flush', NULL, 1.00, NULL, 12500.00, 0.00, 'none', 0.00, 0.00, 0.00, 12500.00, 0.00, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(10, 16, NULL, 0, 'service', NULL, NULL, 'SUV 4WD Transfer Case & Suspension Service', NULL, 1.00, NULL, 25000.00, 0.00, 'none', 0.00, 0.00, 0.00, 25000.00, 0.00, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(11, 17, NULL, 0, 'service', 1, 'SRV-001', 'Full Synthetic Oil & Filter Service', NULL, 1.00, NULL, 1200.00, 0.00, 'none', 0.00, 0.00, 0.00, 1200.00, 0.00, NULL, '2026-10-20 04:55:30', '2026-10-20 04:55:30'),
(12, 42, NULL, 0, 'service', 11, 'SRV-006', 'Automatic Transmission Fluid (ATF) Flush', NULL, 1.00, NULL, 9500.00, 0.00, 'none', 0.00, 0.00, 0.00, 9500.00, 0.00, NULL, '2026-10-21 04:17:47', '2026-10-21 04:17:47'),
(13, 42, NULL, 0, 'service', 3, 'SRV-003', 'Computerized Wheel Alignment & Balancing', NULL, 1.00, NULL, 4500.00, 0.00, 'none', 0.00, 0.00, 0.00, 4500.00, 0.00, NULL, '2026-10-21 04:17:47', '2026-10-21 04:17:47'),
(14, 42, NULL, 0, 'product', 17, 'PRD-MOB-002', 'Mobil Super 10W-40 Synthetic Blend Oil 4L', NULL, 1.00, NULL, 11200.00, 0.00, 'none', 0.00, 0.00, 0.00, 11200.00, 7800.00, NULL, '2026-10-21 04:17:47', '2026-10-21 04:17:47'),
(15, 43, NULL, 0, 'product', NULL, NULL, 'Synthetic Motor Oil 4L', NULL, 2.00, NULL, 9500.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2026-10-21 04:25:06', '2026-10-21 04:25:06'),
(16, 50, NULL, 0, 'service', 1, 'SRV-001', 'Periodic Brake Inspection & Engine Tune-up', NULL, 1.00, NULL, 3500.00, 0.00, 'none', 0.00, 0.00, 0.00, 3500.00, 0.00, NULL, '2026-10-21 05:08:14', '2026-10-21 05:08:14'),
(18, 52, NULL, 0, 'service', 1, 'SRV-001', 'Heavy Commercial Fleet Lubrication & Hub Greasing', NULL, 1.00, NULL, 10000.00, 0.00, 'none', 0.00, 0.00, 0.00, 10000.00, 0.00, NULL, '2026-10-21 08:26:04', '2026-10-21 08:26:04');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_sequences`
--

CREATE TABLE `invoice_sequences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `series` varchar(20) NOT NULL COMMENT 'YearMonth series e.g. 202610',
  `current_number` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_sequences`
--

INSERT INTO `invoice_sequences` (`id`, `series`, `current_number`, `created_at`, `updated_at`) VALUES
(1, '202610', 41, '2026-10-21 04:50:44', '2026-10-21 08:26:04');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_cards`
--

CREATE TABLE `job_cards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `job_number` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_type_id` bigint(20) UNSIGNED NOT NULL,
  `current_mileage` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `next_service_mileage` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('pending','in_progress','additional_work','completed','delivered','cancelled') DEFAULT 'pending',
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `service_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `product_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `appointment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_notes` text DEFAULT NULL,
  `service_notes` text DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `cancelled_by` bigint(20) UNSIGNED DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_cards`
--

INSERT INTO `job_cards` (`id`, `branch_id`, `job_number`, `date`, `customer_id`, `vehicle_id`, `vehicle_type_id`, `current_mileage`, `next_service_mileage`, `status`, `assigned_to`, `service_total`, `product_total`, `discount_amount`, `tax_amount`, `grand_total`, `invoice_id`, `appointment_id`, `customer_notes`, `service_notes`, `cancellation_reason`, `cancelled_by`, `cancelled_at`, `started_at`, `completed_at`, `delivered_at`, `created_by`, `created_at`, `updated_at`) VALUES
(13, 1, 'JOB-202610-0001', '2026-10-14', 11, 17, 1, 96200, 101200, 'pending', 2, 3000.00, 0.00, 0.00, 0.00, 3000.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(14, 1, 'JOB-202610-0002', '2026-10-05', 19, 20, 2, 128400, 133400, 'pending', 2, 3000.00, 0.00, 0.00, 0.00, 3000.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(15, 1, 'JOB-202610-0003', '2026-10-18', 20, 22, 5, 189000, 194000, 'pending', 2, 6000.00, 0.00, 0.00, 0.00, 6000.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(16, 1, 'JOB-202610-0004', '2026-10-17', 13, 25, 3, 112000, 117000, 'in_progress', 2, 9000.00, 0.00, 0.00, 0.00, 9000.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-18 04:40:40', NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(17, 1, 'JOB-202610-0005', '2026-10-11', 14, 26, 3, 68000, 73000, 'in_progress', 2, 5800.00, 0.00, 0.00, 0.00, 5800.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-18 04:40:40', NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(18, 1, 'JOB-202610-0006', '2026-10-11', 19, 21, 1, 74100, 79100, 'in_progress', 2, 3500.00, 2800.00, 0.00, 0.00, 6300.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-18 04:40:40', NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(19, 3, 'JOB-202610-0007', '2026-10-14', 12, 18, 1, 135000, 140000, 'completed', 2, 2500.00, 9500.00, 0.00, 0.00, 12000.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-18 04:40:40', '2026-10-19 04:40:40', NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(20, 1, 'JOB-202610-0008', '2026-10-15', 19, 19, 2, 152000, 157000, 'completed', 2, 8000.00, 17300.00, 0.00, 0.00, 25300.00, 10, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-18 04:40:40', '2026-10-19 04:40:40', NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(21, 1, 'JOB-202610-0009', '2026-10-16', 20, 23, 5, 164000, 169000, 'completed', 2, 6000.00, 12000.00, 0.00, 0.00, 18000.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-18 04:40:40', '2026-10-19 04:40:40', NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(22, 1, 'JOB-202610-0010', '2026-10-05', 22, 32, 2, 55000, 60000, 'completed', 2, 5500.00, 9500.00, 0.00, 0.00, 15000.00, 19, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-18 04:40:40', '2026-10-19 04:40:40', '2026-10-20 04:54:24', 2, '2026-10-20 04:40:40', '2026-10-20 07:10:07'),
(23, 1, 'JOB-202610-0011', '2026-10-10', 22, 33, 1, 32000, 37000, 'completed', 2, 3500.00, 4500.00, 0.00, 0.00, 8000.00, 20, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-18 04:40:40', '2026-10-19 04:40:40', '2026-10-20 04:40:40', 2, '2026-10-20 04:40:40', '2026-10-20 07:10:07'),
(24, 1, 'JOB-202610-0012', '2026-10-14', 22, 34, 5, 78000, 83000, 'completed', 2, 4000.00, 8000.00, 0.00, 0.00, 12000.00, 21, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-18 04:40:40', '2026-10-19 04:40:40', '2026-10-20 04:40:40', 2, '2026-10-20 04:40:40', '2026-10-20 07:11:44'),
(25, 1, 'JOB-202610-0013', '2026-10-12', 23, 35, 5, 110000, 115000, 'completed', NULL, 20000.00, 30000.00, 0.00, 0.00, 50000.00, 22, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-20 04:52:50', '2026-10-20 04:55:23', NULL, 2, '2026-10-20 04:49:55', '2026-10-20 07:31:48'),
(26, 1, 'JOB-202609-0001', '2026-09-05', 22, 32, 2, 50000, 55000, 'completed', NULL, 4500.00, 7500.00, 0.00, 0.00, 12000.00, 18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(27, 1, 'JOB-202609-0002', '2026-09-10', 22, 33, 1, 28000, 33000, 'completed', NULL, 3500.00, 4500.00, 0.00, 0.00, 8000.00, 50, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 07:28:24', '2026-10-21 05:08:14'),
(28, 1, 'JOB-202609-0003', '2026-09-18', 23, 35, 5, 102000, 107000, 'completed', NULL, 10000.00, 15000.00, 0.00, 0.00, 25000.00, 52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 07:28:24', '2026-10-21 08:26:04'),
(29, 1, 'JOB-202610-0014', '2026-10-15', 23, 36, 5, 145000, 150000, 'completed', NULL, 15000.00, 25000.00, 0.00, 0.00, 40000.00, 23, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 07:31:48', '2026-10-20 07:31:48'),
(30, NULL, 'JOB-202610-0015', '2026-10-20', 21, 31, 5, 3400, 6900, 'completed', NULL, 14000.00, 11200.00, 0.00, 0.00, 25200.00, 42, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-20 09:07:16', '2026-10-20 09:08:25', NULL, 2, '2026-10-20 09:06:22', '2026-10-21 04:17:47'),
(32, 1, 'JOB-202610-0016', '2026-10-20', 21, 31, 5, 3400, 6900, 'in_progress', NULL, 7000.00, 0.00, 0.00, 0.00, 7000.00, NULL, 2, NULL, 'Appointment #APT-20261020-0001', NULL, NULL, NULL, '2026-10-21 02:11:44', NULL, NULL, 2, '2026-10-20 10:18:11', '2026-10-21 02:11:44'),
(33, NULL, 'JOB-202610-0017', '2026-10-21', 21, 42, 5, 0, NULL, 'completed', NULL, 13500.00, 2400.00, 0.00, 0.00, 15900.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-21 02:21:00', '2026-10-21 04:34:59', NULL, 2, '2026-10-21 02:12:30', '2026-10-21 04:34:59'),
(37, NULL, 'JOB-202610-0018', '2026-10-21', 21, 43, 1, 2, 3502, 'pending', NULL, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-21 08:46:40', '2026-10-21 08:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `job_card_carpets`
--

CREATE TABLE `job_card_carpets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_card_id` bigint(20) UNSIGNED NOT NULL,
  `carpet_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category_name` varchar(100) NOT NULL,
  `quantity` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_card_products`
--

CREATE TABLE `job_card_products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_card_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_sku` varchar(50) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` decimal(12,2) NOT NULL DEFAULT 1.00,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unit_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `is_stock_deducted` tinyint(1) NOT NULL DEFAULT 0,
  `is_additional` tinyint(1) NOT NULL DEFAULT 0,
  `job_issue_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `batch_allocations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`batch_allocations`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_card_products`
--

INSERT INTO `job_card_products` (`id`, `job_card_id`, `product_id`, `product_sku`, `product_name`, `quantity`, `unit_price`, `unit_cost`, `line_total`, `is_stock_deducted`, `is_additional`, `job_issue_id`, `invoice_id`, `batch_allocations`, `notes`, `created_at`, `updated_at`) VALUES
(8, 18, 18, 'PRD-DEN-003', 'Denso Premium Engine Oil Filter - Toyota/Nissan', 1.00, 2800.00, 1687.50, 2800.00, 1, 0, NULL, NULL, '[{\"batch_id\":18,\"batch_number\":\"B26-0008-001\",\"quantity\":1,\"unit_cost\":1687.5,\"line_cost\":1687.5,\"remaining_in_batch\":31}]', NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(9, 19, 20, 'PRD-FER-005', 'Ferodo Ceramic Front Brake Pads - Car/SUV', 1.00, 9500.00, 6200.00, 9500.00, 1, 0, NULL, NULL, '[{\"batch_id\":21,\"batch_number\":\"B26-0009-001\",\"quantity\":1,\"unit_cost\":6200,\"line_cost\":6200,\"remaining_in_batch\":17}]', NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(10, 20, 16, 'PRD-MOB-001', 'Mobil 1 5W-30 Full Synthetic Engine Oil 4L', 1.00, 14500.00, 9500.00, 14500.00, 1, 0, NULL, 10, '[{\"batch_id\":16,\"batch_number\":\"B26-0007-001\",\"quantity\":1,\"unit_cost\":9500,\"line_cost\":9500,\"remaining_in_batch\":19}]', NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(11, 20, 18, 'PRD-DEN-003', 'Denso Premium Engine Oil Filter - Toyota/Nissan', 1.00, 2800.00, 1687.50, 2800.00, 1, 0, NULL, 10, '[{\"batch_id\":18,\"batch_number\":\"B26-0008-001\",\"quantity\":1,\"unit_cost\":1687.5,\"line_cost\":1687.5,\"remaining_in_batch\":30}]', NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(12, 21, 21, 'PRD-FER-006', 'Ferodo Heavy Duty Rear Brake Shoes - Van/Lorry', 1.00, 12000.00, 8000.00, 12000.00, 1, 0, NULL, NULL, '[{\"batch_id\":22,\"batch_number\":\"B26-0009-002\",\"quantity\":1,\"unit_cost\":8000,\"line_cost\":8000,\"remaining_in_batch\":11}]', NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(13, 22, 18, 'PRD-DEN-003', 'Denso Premium Engine Oil Filter - Toyota/Nissan', 1.00, 2800.00, 1687.50, 2800.00, 1, 0, NULL, 19, '[{\"batch_id\":18,\"batch_number\":\"B26-0008-001\",\"quantity\":1,\"unit_cost\":1687.5,\"line_cost\":1687.5,\"remaining_in_batch\":29}]', NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(14, 23, 23, 'PRD-BOS-008', 'Bosch Laser Iridium Spark Plug Set (4-Pack)', 1.00, 7200.00, 4800.00, 7200.00, 1, 0, NULL, 20, '[{\"batch_id\":24,\"batch_number\":\"B26-0010-002\",\"quantity\":1,\"unit_cost\":4800,\"line_cost\":4800,\"remaining_in_batch\":19}]', NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(15, 30, 17, 'PRD-MOB-002', 'Mobil Super 10W-40 Synthetic Blend Oil 4L', 1.00, 11200.00, 7800.00, 11200.00, 1, 0, NULL, 42, '[{\"batch_id\":17,\"batch_number\":\"B26-0007-002\",\"quantity\":1,\"unit_cost\":7800,\"line_cost\":7800,\"remaining_in_batch\":13}]', NULL, '2026-10-20 09:07:03', '2026-10-21 04:17:47'),
(16, 33, 24, 'PRD-WUR-009', 'Wurth DOT 4 High Performance Brake Fluid 500ml', 1.00, 2400.00, 1500.00, 2400.00, 1, 0, NULL, NULL, '[{\"batch_id\":27,\"batch_number\":\"B26-0011-001\",\"quantity\":1,\"unit_cost\":1500,\"line_cost\":1500,\"remaining_in_batch\":23}]', NULL, '2026-10-21 04:31:24', '2026-10-21 04:31:24');

-- --------------------------------------------------------

--
-- Table structure for table `job_card_services`
--

CREATE TABLE `job_card_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_card_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `service_code` varchar(50) DEFAULT NULL,
  `service_name` varchar(255) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `quantity` decimal(8,2) NOT NULL DEFAULT 1.00,
  `line_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `is_price_custom` tinyint(1) NOT NULL DEFAULT 0,
  `is_additional` tinyint(1) NOT NULL DEFAULT 0,
  `job_issue_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `technician_name` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_card_services`
--

INSERT INTO `job_card_services` (`id`, `job_card_id`, `service_id`, `service_code`, `service_name`, `unit_price`, `quantity`, `line_total`, `is_price_custom`, `is_additional`, `job_issue_id`, `invoice_id`, `technician_name`, `notes`, `created_at`, `updated_at`) VALUES
(14, 13, 4, 'SRV-004', 'Engine Diagnostic Scan & Tune-Up', 3000.00, 1.00, 3000.00, 0, 0, NULL, NULL, 'Nilantha Kumara', NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(15, 14, 3, 'SRV-003', 'Computerized Wheel Alignment & Balancing', 3000.00, 1.00, 3000.00, 0, 0, NULL, NULL, 'Nilantha Kumara', NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(16, 15, 2, 'SRV-002', 'Brake Inspection & Pad Replacement', 6000.00, 1.00, 6000.00, 0, 0, NULL, NULL, 'Nilantha Kumara', NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(17, 16, 13, 'SRV-008', 'Suspension & Steering Linkage Overhaul', 9000.00, 1.00, 9000.00, 0, 0, NULL, NULL, 'Nilantha Kumara', NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(18, 17, 12, 'SRV-007', 'Air Conditioning & Cooling System Service', 5800.00, 1.00, 5800.00, 0, 0, NULL, NULL, 'Nilantha Kumara', NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(19, 18, 1, 'SRV-001', 'Full Synthetic Oil & Filter Service', 3500.00, 1.00, 3500.00, 0, 0, NULL, NULL, 'Nilantha Kumara', NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(20, 19, 2, 'SRV-002', 'Brake Inspection & Pad Replacement', 2500.00, 1.00, 2500.00, 0, 0, NULL, NULL, 'Nilantha Kumara', NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(21, 20, 1, 'SRV-001', 'Full Synthetic Oil & Filter Service', 5000.00, 1.00, 5000.00, 0, 0, NULL, 10, 'Nilantha Kumara', NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(22, 20, 3, 'SRV-003', 'Computerized Wheel Alignment & Balancing', 3000.00, 1.00, 3000.00, 0, 0, NULL, 10, 'Nilantha Kumara', NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(23, 21, 2, 'SRV-002', 'Brake Inspection & Pad Replacement', 6000.00, 1.00, 6000.00, 0, 0, NULL, NULL, 'Nilantha Kumara', NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(24, 22, 1, 'SRV-001', 'Full Synthetic Oil & Filter Service', 5500.00, 1.00, 5500.00, 0, 0, NULL, 19, 'Nilantha Kumara', NULL, '2026-10-20 04:40:40', '2026-10-20 07:10:07'),
(25, 23, 4, 'SRV-004', 'Engine Diagnostic Scan & Tune-Up', 1000.00, 1.00, 1000.00, 0, 0, NULL, 20, 'Nilantha Kumara', NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(26, 24, 5, 'SRV-005', 'Express Wash & Underbody Detailing', 500.00, 1.00, 500.00, 0, 0, NULL, 21, 'Nilantha Kumara', NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(27, 25, 1, 'SRV-001', 'Heavy Fleet Overhaul & Transmission Service', 20000.00, 1.00, 20000.00, 1, 0, NULL, 22, NULL, NULL, '2026-10-20 04:52:10', '2026-10-20 07:31:47'),
(28, 26, 1, 'SRV-001', 'Full Synthetic Oil & Filter Service', 4500.00, 1.00, 4500.00, 0, 0, NULL, 18, NULL, NULL, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(29, 23, 1, 'SRV-001', 'Full Synthetic Oil & Filter Service', 3500.00, 1.00, 3500.00, 0, 0, NULL, 20, NULL, NULL, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(30, 24, 1, 'SRV-001', 'Full Synthetic Oil & Filter Service', 4000.00, 1.00, 4000.00, 0, 0, NULL, 21, NULL, NULL, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(31, 27, 1, 'SRV-001', 'Periodic Brake Inspection & Engine Tune-up', 3500.00, 1.00, 3500.00, 0, 0, NULL, 50, NULL, NULL, '2026-10-20 07:28:24', '2026-10-21 05:08:14'),
(32, 28, 1, 'SRV-001', 'Heavy Commercial Fleet Lubrication & Hub Greasing', 10000.00, 1.00, 10000.00, 0, 0, NULL, 52, NULL, NULL, '2026-10-20 07:28:24', '2026-10-21 08:26:04'),
(33, 29, 1, 'SRV-001', 'Suspension & Air Brake System Servicing', 15000.00, 1.00, 15000.00, 0, 0, NULL, 23, NULL, NULL, '2026-10-20 07:31:48', '2026-10-20 07:31:48'),
(34, 30, 11, 'SRV-006', 'Automatic Transmission Fluid (ATF) Flush', 9500.00, 1.00, 9500.00, 1, 0, NULL, 42, NULL, NULL, '2026-10-20 09:06:59', '2026-10-21 04:17:47'),
(35, 30, 3, 'SRV-003', 'Computerized Wheel Alignment & Balancing', 4500.00, 1.00, 4500.00, 1, 0, NULL, 42, NULL, NULL, '2026-10-20 09:08:20', '2026-10-21 04:17:47'),
(37, 32, 12, 'SRV-007', 'Air Conditioning & Cooling System Service', 7000.00, 1.00, 7000.00, 0, 0, NULL, NULL, NULL, NULL, '2026-10-20 10:18:11', '2026-10-20 10:18:11'),
(38, 33, 11, 'SRV-006', 'Automatic Transmission Fluid (ATF) Flush', 9500.00, 1.00, 9500.00, 1, 0, NULL, NULL, NULL, NULL, '2026-10-21 03:29:58', '2026-10-21 03:29:58'),
(39, 33, 5, 'SRV-005', 'Express Wash & Underbody Detailing', 4000.00, 1.00, 4000.00, 1, 0, NULL, NULL, NULL, NULL, '2026-10-21 03:30:10', '2026-10-21 03:30:10');

-- --------------------------------------------------------

--
-- Table structure for table `job_issues`
--

CREATE TABLE `job_issues` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_card_id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `recommended_repair` text NOT NULL,
  `estimated_cost` decimal(15,2) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('pending_approval','approved','declined','fixed','deferred') NOT NULL DEFAULT 'pending_approval',
  `approval_method` enum('verbal','phone','written','whatsapp') DEFAULT NULL,
  `approval_contact` varchar(100) DEFAULT NULL,
  `approval_note` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `recorded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `supplementary_invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_10_19_112550_create_services_table', 1),
(5, '2026_10_19_112551_create_vehicle_types_table', 1),
(6, '2026_10_19_112552_create_company_settings_table', 1),
(7, '2026_10_19_112553_create_activity_logs_table', 1),
(8, '2026_10_19_123022_create_service_vehicle_prices_table', 2),
(9, '2026_10_19_123508_add_image_to_vehicle_types_table', 3),
(10, '2026_10_19_130001_create_suppliers_table', 4),
(11, '2026_10_19_130002_create_units_of_measurement_table', 4),
(12, '2026_10_19_130003_create_products_table', 4),
(13, '2026_10_19_130004_create_goods_received_notes_table', 4),
(14, '2026_10_19_130005_create_goods_received_note_items_table', 4),
(15, '2026_10_19_130006_create_stock_batches_table', 4),
(16, '2026_10_19_130007_create_stock_movements_table', 4),
(17, '2026_10_19_130008_create_supplier_payments_table', 4),
(18, '2026_10_20_050001_create_customers_table', 5),
(19, '2026_10_20_050002_create_vehicles_table', 5),
(20, '2026_10_20_050003_create_invoices_table', 5),
(21, '2026_10_20_050004_create_invoice_items_table', 5),
(22, '2026_10_20_050005_create_payments_table', 5),
(23, '2026_10_20_050006_create_job_cards_table', 5),
(24, '2026_10_20_050007_create_job_card_services_table', 5),
(25, '2026_10_20_050008_create_job_card_products_table', 5),
(26, '2026_10_20_050009_create_direct_sales_table', 5),
(27, '2026_10_20_050010_create_direct_sale_items_table', 5),
(28, '2026_10_20_060001_create_employees_table', 6),
(29, '2026_10_20_060002_create_employee_wage_rates_table', 6),
(30, '2026_10_20_060003_create_employee_attendance_table', 6),
(31, '2026_10_20_060004_create_payroll_periods_table', 6),
(32, '2026_10_20_060005_create_payroll_items_table', 6),
(33, '2026_10_20_060006_create_employee_advances_table', 6),
(34, '2026_10_20_060007_create_payroll_payments_table', 6),
(35, '2026_10_20_070001_create_expense_categories_table', 7),
(36, '2026_10_20_070002_create_expenses_table', 7),
(37, '2026_10_20_080001_create_branches_table', 8),
(38, '2026_10_20_080002_create_branch_user_table', 8),
(39, '2026_10_20_080003_add_branch_id_to_existing_modules_tables', 8),
(40, '2026_10_20_090001_create_quotations_table', 9),
(41, '2026_10_20_090002_create_quotation_items_table', 9),
(42, '2026_10_20_090003_add_quotation_fields_to_invoices_table', 9),
(43, '2026_10_20_090004_add_quotation_fields_to_invoice_items_table', 9),
(44, '2026_10_20_100001_create_stage6_sales_and_payments_tables', 10),
(45, '2026_10_20_103542_add_details_to_supplier_payments_table', 11),
(46, '2026_10_20_111258_add_image_path_to_products_table', 12),
(47, '2026_10_20_120001_create_customer_credit_accounts_table', 13),
(48, '2026_10_20_090001_create_job_issues_table', 14),
(49, '2026_10_20_090002_add_additional_work_fields_to_job_cards_tables', 14),
(50, '2026_10_20_090003_create_appointments_table', 14),
(51, '2026_10_21_160000_create_invoice_sequences_and_job_card_payments', 15),
(52, '2026_10_21_210000_create_carpet_details_and_vehicle_type_settings', 16),
(53, '2026_10_21_220000_add_payment_tracking_to_expenses_table', 17);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `job_card_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_receipt_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_number` varchar(100) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('cash','card','bank_transfer','credit','cheque') NOT NULL DEFAULT 'cash',
  `reference_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_advance` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(30) NOT NULL DEFAULT 'completed',
  `reversed_at` timestamp NULL DEFAULT NULL,
  `reversal_reason` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `invoice_id`, `job_card_id`, `customer_id`, `payment_receipt_id`, `payment_number`, `payment_date`, `amount`, `payment_method`, `reference_number`, `notes`, `is_advance`, `status`, `reversed_at`, `reversal_reason`, `created_by`, `created_at`, `updated_at`) VALUES
(5, 9, NULL, 15, NULL, 'PAY-202610-0001', '2026-10-15', 8200.00, 'cash', NULL, NULL, 0, 'completed', NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(6, 12, NULL, 19, NULL, 'PAY-202610-0004', '2026-10-16', 12000.00, 'bank_transfer', 'TXN-QC-PARTIAL', NULL, 0, 'completed', NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(7, 13, NULL, 11, NULL, 'PAY-202610-0005', '2026-10-18', 18000.00, 'card', 'CARD-DS-0005', NULL, 0, 'completed', NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(8, 14, NULL, 11, NULL, 'PAY-202610-0006', '2026-10-18', 6300.00, 'card', 'CARD-JOB-0010', NULL, 0, 'completed', NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(9, 17, NULL, 21, NULL, 'PAY-202610-0007', '2026-10-20', 1200.00, 'cash', NULL, NULL, 0, 'completed', NULL, NULL, 2, '2026-10-20 04:56:04', '2026-10-20 04:56:04'),
(10, 18, NULL, 22, NULL, 'PAY-202609-0001', '2026-09-05', 12000.00, 'bank_transfer', 'TRX-BOC-554411', NULL, 0, 'completed', NULL, NULL, 2, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(11, 19, NULL, 22, 1, 'PAY-202610-0010', '2026-10-18', 15000.00, 'bank_transfer', 'TRX-BOC-998822-1', NULL, 0, 'completed', NULL, NULL, 2, '2026-10-20 07:11:44', '2026-10-20 07:11:44'),
(12, 20, NULL, 22, 1, 'PAY-202610-0011', '2026-10-18', 5000.00, 'bank_transfer', 'TRX-BOC-998822-2', NULL, 0, 'completed', NULL, NULL, 2, '2026-10-20 07:11:44', '2026-10-20 07:11:44'),
(13, 24, NULL, 24, NULL, 'PAY-202610-0015', '2026-10-16', 10000.00, 'cash', NULL, 'Counter cash payment towards INV-202610-0015.', 0, 'completed', NULL, NULL, 2, '2026-10-20 07:11:44', '2026-10-20 07:11:44'),
(14, 30, NULL, 16, NULL, 'PAY-202610-0017', '2026-10-02', 6500.00, 'cash', NULL, NULL, 0, 'completed', NULL, NULL, 1, '2026-10-20 07:11:45', '2026-10-20 07:11:45'),
(15, 31, NULL, 13, NULL, 'PAY-202610-0018', '2026-10-07', 12500.00, 'card', 'AUTH-VISA-88991', NULL, 0, 'completed', NULL, NULL, 1, '2026-10-20 07:11:45', '2026-10-20 07:11:45'),
(16, 32, NULL, 15, NULL, 'PAY-202610-0019', '2026-10-11', 21000.00, 'bank_transfer', 'TRX-HNB-334411', NULL, 0, 'completed', NULL, NULL, 1, '2026-10-20 07:11:45', '2026-10-20 07:11:45'),
(17, 33, NULL, 17, NULL, 'PAY-202610-0020', '2026-10-15', 15000.00, 'cheque', 'CHQ-889902', '[Status: Deposited] Sampath Bank Chq #889902', 0, 'completed', NULL, NULL, 1, '2026-10-20 07:11:45', '2026-10-20 07:11:45'),
(18, 35, NULL, 24, NULL, 'PAY-202610-0022', '2026-10-20', 14500.00, 'cash', NULL, NULL, 0, 'completed', NULL, NULL, 1, '2026-10-20 07:11:45', '2026-10-20 07:11:45'),
(19, 36, NULL, 13, NULL, 'PAY-202609-0005', '2026-09-25', 11000.00, 'cheque', 'CHQ-889901', '[Status: Cleared] Commercial Bank Chq #889901 cleared.', 0, 'completed', NULL, NULL, 1, '2026-10-20 07:11:45', '2026-10-20 07:11:45'),
(20, 24, NULL, 24, NULL, 'PAY-202610-0024', '2026-10-12', 8000.00, 'cheque', 'CHQ-889904', '[Status: Bounced] Seylan Bank Chq returned.', 0, 'reversed', '2026-10-13 04:30:00', 'Cheque bounced due to insufficient drawer funds.', 1, '2026-10-20 07:11:45', '2026-10-20 07:11:45'),
(21, 38, NULL, 24, NULL, 'PAY-202610-0030', '2026-10-17', 20000.00, 'cash', NULL, NULL, 0, 'completed', NULL, NULL, 1, '2026-10-20 07:28:47', '2026-10-20 07:34:05'),
(22, 39, NULL, 24, NULL, 'PAY-202610-0031', '2026-10-17', 15000.00, 'card', 'AUTH-MC-55442', NULL, 0, 'completed', NULL, NULL, 1, '2026-10-20 07:29:07', '2026-10-20 07:34:05'),
(23, 40, NULL, 24, NULL, 'PAY-202610-0032', '2026-10-17', 10000.00, 'cheque', 'CHQ-771122', '[Status: Received] HNB Cheque received, pending bank deposit.', 0, 'completed', NULL, NULL, 1, '2026-10-20 07:29:07', '2026-10-20 07:34:06');

-- --------------------------------------------------------

--
-- Table structure for table `payment_allocations`
--

CREATE TABLE `payment_allocations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_receipt_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `allocated_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_allocations`
--

INSERT INTO `payment_allocations` (`id`, `payment_receipt_id`, `invoice_id`, `allocated_amount`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 19, 15000.00, NULL, '2026-10-20 07:11:44', '2026-10-20 07:11:44'),
(2, 1, 20, 5000.00, NULL, '2026-10-20 07:11:44', '2026-10-20 07:11:44');

-- --------------------------------------------------------

--
-- Table structure for table `payment_receipts`
--

CREATE TABLE `payment_receipts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `receipt_number` varchar(50) NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `receipt_date` date NOT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) NOT NULL DEFAULT 'cash',
  `reference_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_receipts`
--

INSERT INTO `payment_receipts` (`id`, `receipt_number`, `branch_id`, `customer_id`, `receipt_date`, `total_amount`, `payment_method`, `reference_number`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'REC-202610-0001', 1, 22, '2026-10-18', 20000.00, 'bank_transfer', 'TRX-BOC-998822', 'Fleet October partial settlement: INV-202610-0010 (15,000) & INV-202610-0011 (5,000).', 2, '2026-10-20 07:11:44', '2026-10-20 07:11:44');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_items`
--

CREATE TABLE `payroll_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payroll_period_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `employee_name_snapshot` varchar(150) NOT NULL,
  `daily_wage_snapshot` decimal(12,2) NOT NULL,
  `full_days_count` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `half_days_count` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `absent_days_count` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `leave_days_count` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `day_off_days_count` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `payable_days` decimal(5,2) NOT NULL DEFAULT 0.00,
  `base_earnings` decimal(12,2) NOT NULL DEFAULT 0.00,
  `overtime_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `bonus_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `deductions` decimal(12,2) NOT NULL DEFAULT 0.00,
  `epf_employee_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `epf_employer_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `etf_employer_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `advances_applied` decimal(12,2) NOT NULL DEFAULT 0.00,
  `gross_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `net_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `balance_due` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` varchar(30) NOT NULL DEFAULT 'draft',
  `attendance_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attendance_breakdown`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payroll_items`
--

INSERT INTO `payroll_items` (`id`, `payroll_period_id`, `employee_id`, `employee_name_snapshot`, `daily_wage_snapshot`, `full_days_count`, `half_days_count`, `absent_days_count`, `leave_days_count`, `day_off_days_count`, `payable_days`, `base_earnings`, `overtime_amount`, `bonus_amount`, `deductions`, `epf_employee_amount`, `epf_employer_amount`, `etf_employer_amount`, `advances_applied`, `gross_salary`, `net_salary`, `paid_amount`, `balance_due`, `status`, `attendance_breakdown`, `notes`, `created_at`, `updated_at`) VALUES
(5, 2, 6, 'Nilantha Kumara', 3500.00, 0, 0, 0, 0, 0, 21.50, 75250.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 10000.00, 75250.00, 65250.00, 65250.00, 0.00, 'paid', NULL, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(6, 2, 7, 'Roshan Silva', 3200.00, 0, 0, 0, 0, 0, 21.50, 68800.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 68800.00, 68800.00, 68800.00, 0.00, 'paid', NULL, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(7, 2, 8, 'Iresha Perera', 2500.00, 0, 0, 0, 0, 0, 21.50, 53750.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 53750.00, 53750.00, 53750.00, 0.00, 'paid', NULL, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(8, 2, 9, 'Hasini Kumari', 2800.00, 0, 0, 0, 0, 0, 21.50, 60200.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 60200.00, 60200.00, 60200.00, 0.00, 'paid', NULL, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(9, 2, 10, 'Tharindu Jayakody', 2200.00, 0, 0, 0, 0, 0, 21.50, 47300.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 47300.00, 47300.00, 47300.00, 0.00, 'paid', NULL, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(10, 2, 11, 'Dinesh Madushanka', 1800.00, 0, 0, 0, 0, 0, 21.50, 38700.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 38700.00, 38700.00, 38700.00, 0.00, 'paid', NULL, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(11, 3, 6, 'Nilantha Kumara', 3500.00, 0, 0, 0, 0, 0, 14.50, 50750.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 50750.00, 50750.00, 0.00, 50750.00, 'draft', NULL, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(12, 3, 7, 'Roshan Silva', 3200.00, 0, 0, 0, 0, 0, 14.50, 46400.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 5000.00, 46400.00, 41400.00, 0.00, 41400.00, 'draft', NULL, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(13, 3, 8, 'Iresha Perera', 2500.00, 0, 0, 0, 0, 0, 14.50, 36250.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 36250.00, 36250.00, 0.00, 36250.00, 'draft', NULL, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(14, 3, 9, 'Hasini Kumari', 2800.00, 0, 0, 0, 0, 0, 14.50, 40600.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 40600.00, 40600.00, 0.00, 40600.00, 'draft', NULL, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(15, 3, 10, 'Tharindu Jayakody', 2200.00, 0, 0, 0, 0, 0, 14.50, 31900.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 31900.00, 31900.00, 0.00, 31900.00, 'draft', NULL, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(16, 3, 11, 'Dinesh Madushanka', 1800.00, 0, 0, 0, 0, 0, 14.50, 26100.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 26100.00, 26100.00, 0.00, 26100.00, 'draft', NULL, NULL, '2026-10-20 04:40:40', '2026-10-20 04:40:40');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_payments`
--

CREATE TABLE `payroll_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payroll_item_id` bigint(20) UNSIGNED NOT NULL,
  `payment_number` varchar(50) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'cash',
  `reference_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `recorded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payroll_payments`
--

INSERT INTO `payroll_payments` (`id`, `payroll_item_id`, `payment_number`, `payment_date`, `amount`, `payment_method`, `reference_number`, `notes`, `recorded_by`, `created_at`, `updated_at`) VALUES
(1, 5, 'PAY-0001', '2026-09-30', 65250.00, 'bank_transfer', 'SAL-202609-6', NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(2, 6, 'PAY-0002', '2026-09-30', 68800.00, 'bank_transfer', 'SAL-202609-7', NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(3, 7, 'PAY-0003', '2026-09-30', 53750.00, 'bank_transfer', 'SAL-202609-8', NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(4, 8, 'PAY-0004', '2026-09-30', 60200.00, 'bank_transfer', 'SAL-202609-9', NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(5, 9, 'PAY-0005', '2026-09-30', 47300.00, 'bank_transfer', 'SAL-202609-10', NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(6, 10, 'PAY-0006', '2026-09-30', 38700.00, 'bank_transfer', 'SAL-202609-11', NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_periods`
--

CREATE TABLE `payroll_periods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `year` smallint(5) UNSIGNED NOT NULL,
  `month` tinyint(3) UNSIGNED NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'draft',
  `total_base_earnings` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_overtime` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_bonuses` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_deductions` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_advances` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_gross` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_net` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_paid` decimal(14,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payroll_periods`
--

INSERT INTO `payroll_periods` (`id`, `year`, `month`, `status`, `total_base_earnings`, `total_overtime`, `total_bonuses`, `total_deductions`, `total_advances`, `total_gross`, `total_net`, `total_paid`, `notes`, `approved_by`, `approved_at`, `created_at`, `updated_at`) VALUES
(2, 2026, 9, 'paid', 380000.00, 0.00, 0.00, 0.00, 10000.00, 380000.00, 370000.00, 370000.00, NULL, 2, '2026-09-30 12:30:00', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(3, 2026, 10, 'approved', 250000.00, 0.00, 0.00, 0.00, 5000.00, 250000.00, 245000.00, 0.00, NULL, 2, '2026-10-20 03:30:00', '2026-10-20 04:40:40', '2026-10-20 07:15:21');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `uom_id` bigint(20) UNSIGNED NOT NULL,
  `preferred_supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `min_stock` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_stock` decimal(10,2) DEFAULT NULL,
  `selling_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `image_path` varchar(2048) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `name`, `description`, `uom_id`, `preferred_supplier_id`, `min_stock`, `max_stock`, `selling_price`, `image_path`, `is_active`, `created_at`, `updated_at`) VALUES
(16, 'PRD-MOB-001', 'Mobil 1 5W-30 Full Synthetic Engine Oil 4L', NULL, 1, 7, 5.00, 50.00, 14500.00, NULL, 1, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(17, 'PRD-MOB-002', 'Mobil Super 10W-40 Synthetic Blend Oil 4L', NULL, 2, 7, 5.00, 40.00, 11200.00, NULL, 1, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(18, 'PRD-DEN-003', 'Denso Premium Engine Oil Filter - Toyota/Nissan', NULL, 4, 8, 10.00, 60.00, 2800.00, NULL, 1, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(19, 'PRD-DEN-004', 'Denso High Flow Air Filter', NULL, 4, 8, 8.00, 50.00, 3500.00, NULL, 1, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(20, 'PRD-FER-005', 'Ferodo Ceramic Front Brake Pads - Car/SUV', NULL, 4, 9, 6.00, 30.00, 9500.00, NULL, 1, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(21, 'PRD-FER-006', 'Ferodo Heavy Duty Rear Brake Shoes - Van/Lorry', NULL, 4, 9, 5.00, 25.00, 12000.00, NULL, 1, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(22, 'PRD-BOS-007', 'Bosch S4 Mega Power 12V 65Ah Maintenance-Free Battery', NULL, 4, 10, 4.00, 20.00, 36000.00, NULL, 1, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(23, 'PRD-BOS-008', 'Bosch Laser Iridium Spark Plug Set (4-Pack)', NULL, 3, 10, 8.00, 40.00, 7200.00, NULL, 1, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(24, 'PRD-WUR-009', 'Wurth DOT 4 High Performance Brake Fluid 500ml', NULL, 1, 11, 10.00, 50.00, 2400.00, NULL, 1, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(25, 'PRD-WUR-010', 'Wurth Long-Life Radiator Coolant 1L', NULL, 1, 11, 10.00, 50.00, 2900.00, NULL, 1, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(26, 'PRD-BOS-011', 'Bosch Aerotwin Premium Wiper Blade Set', NULL, 4, 10, 8.00, 30.00, 4800.00, NULL, 1, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(27, 'PRD-WUR-012', 'Wurth Multi-Purpose Penetrating Spray 400ml', NULL, 2, 11, 10.00, 40.00, 2100.00, NULL, 1, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(28, 'PRD-DEN-013', 'Denso Cabin A/C Charcoal Air Filter', NULL, 4, 8, 6.00, 30.00, 3200.00, NULL, 1, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(29, 'PRD-GAT-014', 'Gates V-Ribbed Serpentine Fan Belt', NULL, 4, 10, 5.00, 25.00, 4500.00, NULL, 1, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(30, 'PRD-OSR-015', 'Osram H4 12V 60/55W Halogen Headlight Bulb', NULL, 4, 10, 10.00, 50.00, 1800.00, NULL, 1, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(31, 'PRD-FLT-001', 'Oil Filter', NULL, 4, 7, 5.00, 50.00, 1800.00, NULL, 1, '2026-10-20 07:28:24', '2026-10-20 07:28:24'),
(32, 'PRD-OIL-1L', 'Engine Oil 1L', NULL, 1, 7, 10.00, 100.00, 2200.00, NULL, 1, '2026-10-20 07:28:24', '2026-10-20 07:28:24');

-- --------------------------------------------------------

--
-- Table structure for table `quotations`
--

CREATE TABLE `quotations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quotation_number` varchar(100) NOT NULL,
  `quotation_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_type` enum('none','percentage','fixed') NOT NULL DEFAULT 'none',
  `discount_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `additional_charges` decimal(15,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','sent','accepted','rejected','expired','converted','cancelled') NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `terms_conditions` text DEFAULT NULL,
  `converted_invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `converted_job_card_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`customer_snapshot`)),
  `vehicle_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`vehicle_snapshot`)),
  `company_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`company_snapshot`)),
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quotations`
--

INSERT INTO `quotations` (`id`, `branch_id`, `customer_id`, `vehicle_id`, `quotation_number`, `quotation_date`, `expiry_date`, `subject`, `reference`, `subtotal`, `discount_type`, `discount_value`, `discount_amount`, `tax_percentage`, `tax_amount`, `additional_charges`, `grand_total`, `status`, `notes`, `terms_conditions`, `converted_invoice_id`, `converted_job_card_id`, `customer_snapshot`, `vehicle_snapshot`, `company_snapshot`, `created_by`, `created_at`, `updated_at`) VALUES
(6, 1, 11, 17, 'QT-202610-0001', '2026-10-15', '2026-11-04', 'Major 100,000 km Hybrid Service Estimate', NULL, 12500.00, 'none', 0.00, 0.00, 0.00, 0.00, 0.00, 12500.00, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(7, 1, 12, 18, 'QT-202610-0002', '2026-10-08', '2026-11-04', 'Brake & Suspension Replacement Estimate', NULL, 12500.00, 'none', 0.00, 0.00, 0.00, 0.00, 0.00, 12500.00, 'sent', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(8, 1, 19, 19, 'QT-202610-0003', '2026-10-07', '2026-11-04', 'Corporate Van Fleet Maintenance Contract', NULL, 12500.00, 'none', 0.00, 0.00, 0.00, 0.00, 0.00, 12500.00, 'accepted', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(9, 1, 14, 26, 'QT-202610-0004', '2026-10-07', '2026-11-04', 'A/C Evaporator Replacement Estimate', NULL, 12500.00, 'none', 0.00, 0.00, 0.00, 0.00, 0.00, 12500.00, 'rejected', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(10, 1, 15, 27, 'QT-202610-0005', '2026-10-12', '2026-11-04', 'Motorbike Full Tune-up and Spark Plugs', NULL, 12500.00, 'none', 0.00, 0.00, 0.00, 0.00, 0.00, 12500.00, 'converted', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40');

-- --------------------------------------------------------

--
-- Table structure for table `quotation_items`
--

CREATE TABLE `quotation_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quotation_id` bigint(20) UNSIGNED NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `item_type` enum('service','product','custom') NOT NULL DEFAULT 'service',
  `item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_code` varchar(50) DEFAULT NULL,
  `item_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` decimal(12,2) NOT NULL DEFAULT 1.00,
  `unit` varchar(50) DEFAULT NULL,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_type` enum('none','percentage','fixed') NOT NULL DEFAULT 'none',
  `discount_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quotation_items`
--

INSERT INTO `quotation_items` (`id`, `quotation_id`, `sort_order`, `item_type`, `item_id`, `item_code`, `item_name`, `description`, `quantity`, `unit`, `unit_price`, `discount_type`, `discount_value`, `discount_amount`, `tax_percentage`, `tax_amount`, `line_total`, `created_at`, `updated_at`) VALUES
(11, 6, 0, 'service', NULL, NULL, 'Preventive Inspection & Tune-up', NULL, 1.00, NULL, 5000.00, 'none', 0.00, 0.00, 0.00, 0.00, 5000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(12, 6, 0, 'product', NULL, NULL, 'Bosch Laser Iridium Spark Plug Set', NULL, 1.00, NULL, 7500.00, 'none', 0.00, 0.00, 0.00, 0.00, 7500.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(13, 7, 0, 'service', NULL, NULL, 'Preventive Inspection & Tune-up', NULL, 1.00, NULL, 5000.00, 'none', 0.00, 0.00, 0.00, 0.00, 5000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(14, 7, 0, 'product', NULL, NULL, 'Bosch Laser Iridium Spark Plug Set', NULL, 1.00, NULL, 7500.00, 'none', 0.00, 0.00, 0.00, 0.00, 7500.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(15, 8, 0, 'service', NULL, NULL, 'Preventive Inspection & Tune-up', NULL, 1.00, NULL, 5000.00, 'none', 0.00, 0.00, 0.00, 0.00, 5000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(16, 8, 0, 'product', NULL, NULL, 'Bosch Laser Iridium Spark Plug Set', NULL, 1.00, NULL, 7500.00, 'none', 0.00, 0.00, 0.00, 0.00, 7500.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(17, 9, 0, 'service', NULL, NULL, 'Preventive Inspection & Tune-up', NULL, 1.00, NULL, 5000.00, 'none', 0.00, 0.00, 0.00, 0.00, 5000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(18, 9, 0, 'product', NULL, NULL, 'Bosch Laser Iridium Spark Plug Set', NULL, 1.00, NULL, 7500.00, 'none', 0.00, 0.00, 0.00, 0.00, 7500.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(19, 10, 0, 'service', NULL, NULL, 'Preventive Inspection & Tune-up', NULL, 1.00, NULL, 5000.00, 'none', 0.00, 0.00, 0.00, 0.00, 5000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(20, 10, 0, 'product', NULL, NULL, 'Bosch Laser Iridium Spark Plug Set', NULL, 1.00, NULL, 7500.00, 'none', 0.00, 0.00, 0.00, 0.00, 7500.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `code`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'SRV-001', 'Full Synthetic Oil & Filter Service', 'Complete engine oil drainage, premium synthetic oil refill, filter change, and general fluids inspection.', 'active', '2026-10-19 06:08:35', '2026-10-19 06:08:35'),
(2, 'SRV-002', 'Brake Inspection & Pad Replacement', 'Hydraulic brake fluid test, caliper cleaning, rotor wear check, and front/rear brake pad replacement.', 'inactive', '2026-10-19 06:08:35', '2026-10-20 04:19:33'),
(3, 'SRV-003', 'Computerized Wheel Alignment & Balancing', 'Laser 4-wheel geometry alignment, tire tread check, and dynamic wheel balancing.', 'active', '2026-10-19 06:08:35', '2026-10-19 06:08:35'),
(4, 'SRV-004', 'Engine Diagnostic Scan & Tune-Up', 'Comprehensive OBD-II scan, ignition system check, throttle cleaning, and battery health report.', 'active', '2026-10-19 06:08:35', '2026-10-19 06:08:35'),
(5, 'SRV-005', 'Express Wash & Underbody Detailing', 'High pressure chassis wash, shampoo exterior body clean, tire shine, and cabin interior vacuum.', 'active', '2026-10-19 06:08:35', '2026-10-19 06:08:35'),
(6, 'SRE1234', 'SDK Tech Holdings', 'sdf', 'active', '2026-10-20 04:12:40', '2026-10-20 04:12:40'),
(7, 'SRE12344', 'QA VAT Company B Ltd', 'sdfghnbvcderthfd4tyhbvfethvcdyhbcdhbcdthbvcdthb', 'active', '2026-10-20 04:20:33', '2026-10-20 04:20:33'),
(11, 'SRV-006', 'Automatic Transmission Fluid (ATF) Flush', 'Complete automatic transmission fluid exchange, filter replacement, and pressure test.', 'active', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(12, 'SRV-007', 'Air Conditioning & Cooling System Service', 'A/C gas pressure check, refrigerant recharge, cabin deodorizing, and condenser wash.', 'active', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(13, 'SRV-008', 'Suspension & Steering Linkage Overhaul', 'Shock absorber test, ball joint and tie rod inspection, bushing lubrication, and road testing.', 'active', '2026-10-20 04:40:40', '2026-10-20 04:40:40');

-- --------------------------------------------------------

--
-- Table structure for table `service_vehicle_prices`
--

CREATE TABLE `service_vehicle_prices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_type_id` bigint(20) UNSIGNED NOT NULL,
  `service_price` decimal(10,2) DEFAULT NULL,
  `service_cost` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_vehicle_prices`
--

INSERT INTO `service_vehicle_prices` (`id`, `service_id`, `vehicle_type_id`, `service_price`, `service_cost`, `created_at`, `updated_at`) VALUES
(59, 1, 1, 3500.00, 1000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(60, 1, 2, 5000.00, 1500.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(61, 1, 3, 6200.00, 2000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(62, 1, 4, 1200.00, 400.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(63, 1, 5, 8000.00, 3000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(64, 1, 6, 1500.00, 500.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(65, 2, 1, 2500.00, 800.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(66, 2, 2, 3500.00, 1200.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(67, 2, 3, 4500.00, 1500.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(68, 2, 4, 800.00, 250.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(69, 2, 5, 6000.00, 2000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(70, 2, 6, 1000.00, 350.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(71, 3, 1, 2000.00, 500.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(72, 3, 2, 3000.00, 800.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(73, 3, 3, 3500.00, 1000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(74, 3, 4, 600.00, 150.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(75, 3, 5, 4500.00, 1200.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(76, 3, 6, 800.00, 200.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(77, 4, 1, 3000.00, 900.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(78, 4, 2, 4000.00, 1200.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(79, 4, 3, 5000.00, 1500.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(80, 4, 4, 1000.00, 300.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(81, 4, 5, 6500.00, 2200.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(82, 4, 6, 1200.00, 400.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(83, 5, 1, 1500.00, 400.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(84, 5, 2, 2200.00, 600.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(85, 5, 3, 2800.00, 800.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(86, 5, 4, 500.00, 150.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(87, 5, 5, 4000.00, 1200.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(88, 5, 6, 700.00, 200.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(89, 11, 1, 4500.00, 1500.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(90, 11, 2, 6000.00, 2000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(91, 11, 3, 7500.00, 2500.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(92, 11, 4, 1500.00, 500.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(93, 11, 5, 9500.00, 3500.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(94, 11, 6, 1800.00, 600.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(95, 12, 1, 3800.00, 1200.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(96, 12, 2, 4800.00, 1600.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(97, 12, 3, 5800.00, 2000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(98, 12, 4, 900.00, 300.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(99, 12, 5, 7000.00, 2500.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(100, 12, 6, 1200.00, 400.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(101, 13, 1, 5500.00, 1800.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(102, 13, 2, 7500.00, 2500.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(103, 13, 3, 9000.00, 3000.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(104, 13, 4, 1800.00, 600.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(105, 13, 5, 12000.00, 4500.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(106, 13, 6, 2200.00, 800.00, '2026-10-20 04:40:40', '2026-10-20 04:40:40');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0DhGVlMhXO2ZspXf0ibAquuYhako6a6eu0ikSFp4', 1, '127.0.0.1', 'Symfony', 'eyJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MSwiX3Rva2VuIjoiaUJjalcyNk0yOXJlaUFMNGoyb3N0V0VLRFpWakw1YTVqMzFPQ28xeiIsInBhc3N3b3JkX2hhc2hfd2ViIjoiZDM4ODk2NWVhOTA4YjgzM2I0ZGE3ZTQ1MjUzYmQ5M2JlZTlmYjAxYmMxNGY3ODM1YWUzNzg5Yzc5ZTllZWY0NyIsImFjdGl2ZV9icmFuY2hfaWQiOjEsIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC9sb2NhbGhvc3RcL2Rhc2hib2FyZCIsInJvdXRlIjoiZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1792601631),
('1PcWWlOo2oQZ3OtHENFpDV28chJKvKTXBVZUZZLz', NULL, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJxN3BoZGdVVVhVd0Zsd2hNQVNCRnZiMm1kZFJEbTI0dDFkWnVlUGdQIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvbG9jYWxob3N0XC9iaWxsaW5nXC9tdWx0aXBsZS1pbnZvaWNlLXBheW1lbnQifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvYmlsbGluZ1wvbXVsdGlwbGUtaW52b2ljZS1wYXltZW50Iiwicm91dGUiOiJiaWxsaW5nLm11bHRpcGxlLWludm9pY2UtcGF5bWVudC5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1792601168),
('ahk6cOb1gg30MubKf7I00vipdvQbqRJTcTMaG8Bh', 1, '127.0.0.1', 'Symfony', 'eyJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MSwiX3Rva2VuIjoiOEJLU1ZGNXkxSThrUFpVSUZSTU9nWmp5TUNtSVdoUUZNY3hXYmhERSIsInBhc3N3b3JkX2hhc2hfd2ViIjoiZDM4ODk2NWVhOTA4YjgzM2I0ZGE3ZTQ1MjUzYmQ5M2JlZTlmYjAxYmMxNGY3ODM1YWUzNzg5Yzc5ZTllZWY0NyIsImFjdGl2ZV9icmFuY2hfaWQiOjEsIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC9sb2NhbGhvc3RcL2JpbGxpbmdcL211bHRpcGxlLWludm9pY2UtcGF5bWVudCIsInJvdXRlIjoiYmlsbGluZy5tdWx0aXBsZS1pbnZvaWNlLXBheW1lbnQuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1792601396),
('CyR6Hm7JbIw23Bk8zL8WB4jHoaFAbvBPjv3yWTki', 2, '127.0.0.1', 'Symfony', 'eyJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MiwiX3Rva2VuIjoibVIyejY3ZG1qdE03R2lNcXo2RXE3VEMzUlJBeDFMekVuZU0xd2xuciIsInBhc3N3b3JkX2hhc2hfd2ViIjoiZTk0Y2Y5MTBhODI2ODMxNDc1ZjAyZGQ5NzNmNTBjOGQzZmFhYjhjZWRjMGYzNWNmYTZiMTVlOGM3YzBiZDRhNCIsImFjdGl2ZV9icmFuY2hfaWQiOjEsIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1792601735),
('eib6F6BusAWZU3Gr4koBGVWSgw8k20HAzGVw6p9k', NULL, '127.0.0.1', '', 'eyJfdG9rZW4iOiJnNUVqMzFRQk5BajZmWkF4QUtnVFVZdmVsdEVVWUk3SFozRFZzVGllIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1792593624),
('i6S68GtMuX2AgpDe6wC5JfEb23qJDhgAya8yIRzh', 1, '127.0.0.1', 'Symfony', 'eyJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MSwiX3Rva2VuIjoic3BIdGlFNXBGYVExbTA4V2kyQW9GME1GRXlQT3ozWXpnb3E3d1NyMCIsInBhc3N3b3JkX2hhc2hfd2ViIjoiZDM4ODk2NWVhOTA4YjgzM2I0ZGE3ZTQ1MjUzYmQ5M2JlZTlmYjAxYmMxNGY3ODM1YWUzNzg5Yzc5ZTllZWY0NyIsImFjdGl2ZV9icmFuY2hfaWQiOjEsIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC9sb2NhbGhvc3RcL2Rhc2hib2FyZCIsInJvdXRlIjoiZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1792601680),
('ibqVqoN1TazRc1rbs4g8YqHWOcgOokzeuCFA44PJ', 1, '127.0.0.1', 'Symfony', 'eyJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MSwiX3Rva2VuIjoicUNiZUZzQmxsbXVBcjRBd3NYdjdrSTRyUE15dmZKdzFXckhWcE9GTiIsInBhc3N3b3JkX2hhc2hfd2ViIjoiZDM4ODk2NWVhOTA4YjgzM2I0ZGE3ZTQ1MjUzYmQ5M2JlZTlmYjAxYmMxNGY3ODM1YWUzNzg5Yzc5ZTllZWY0NyIsImFjdGl2ZV9icmFuY2hfaWQiOjEsIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC9sb2NhbGhvc3RcL2JpbGxpbmdcL211bHRpcGxlLWludm9pY2UtcGF5bWVudCIsInJvdXRlIjoiYmlsbGluZy5tdWx0aXBsZS1pbnZvaWNlLXBheW1lbnQuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1792601209),
('kleYX4RwHkxcDinNgaWdoWDWhR33MR9zOTjGJnp8', 1, '127.0.0.1', 'Symfony', 'eyJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MSwiX3Rva2VuIjoiMG1XYWFTNGNzZjhPUkdRbU00ajRNeENtSkdTQ0l4VkNOU3ZMczlMRiIsInBhc3N3b3JkX2hhc2hfd2ViIjoiZDM4ODk2NWVhOTA4YjgzM2I0ZGE3ZTQ1MjUzYmQ5M2JlZTlmYjAxYmMxNGY3ODM1YWUzNzg5Yzc5ZTllZWY0NyIsImFjdGl2ZV9icmFuY2hfaWQiOjEsIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC9sb2NhbGhvc3RcL2JpbGxpbmdcL211bHRpcGxlLWludm9pY2UtcGF5bWVudCIsInJvdXRlIjoiYmlsbGluZy5tdWx0aXBsZS1pbnZvaWNlLXBheW1lbnQuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1792601200),
('obCAqi7agAiO6zAqsWGlS0L6G2AFRC8BJoeRL3VC', 1, '127.0.0.1', 'Symfony', 'eyJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MSwiX3Rva2VuIjoiQ2NJMUtwZkxwcE1VR3ZZZmdFVXlRY1Y0WWVRanM2YWdhQjFneDI2VSIsInBhc3N3b3JkX2hhc2hfd2ViIjoiZDM4ODk2NWVhOTA4YjgzM2I0ZGE3ZTQ1MjUzYmQ5M2JlZTlmYjAxYmMxNGY3ODM1YWUzNzg5Yzc5ZTllZWY0NyIsImFjdGl2ZV9icmFuY2hfaWQiOjEsIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC9sb2NhbGhvc3RcL2JpbGxpbmdcL211bHRpcGxlLWludm9pY2UtcGF5bWVudCIsInJvdXRlIjoiYmlsbGluZy5tdWx0aXBsZS1pbnZvaWNlLXBheW1lbnQuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1792601401),
('PdAETu3IfIgeaEiToUMtsLwuCFgh5y6Iq9MoZXww', NULL, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJ5YlhKeVpreDhzbkNsTUV1a3ZZR3YxUlF5UXlJaHhMQjNuSnp2UVVkIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvbG9jYWxob3N0XC9iaWxsaW5nXC9tdWx0aXBsZS1pbnZvaWNlLXBheW1lbnQifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvYmlsbGluZ1wvbXVsdGlwbGUtaW52b2ljZS1wYXltZW50Iiwicm91dGUiOiJiaWxsaW5nLm11bHRpcGxlLWludm9pY2UtcGF5bWVudC5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1792598567),
('qS2G2yk8V8BYsd9ithI9V6W0Y3LFaCHrGq84ObyZ', 2, '127.0.0.1', 'Symfony', 'eyJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MiwiX3Rva2VuIjoiZzRWeWdDb2pHZWRRNGxPT01kV0ZIRGdpZXJoWDRSWDJWQUxiUmFaUSIsInBhc3N3b3JkX2hhc2hfd2ViIjoiZTk0Y2Y5MTBhODI2ODMxNDc1ZjAyZGQ5NzNmNTBjOGQzZmFhYjhjZWRjMGYzNWNmYTZiMTVlOGM3YzBiZDRhNCIsIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC9sb2NhbGhvc3RcL29wZXJhdGlvbnNcL3ZlaGljbGVzXC9zZWFyY2g/bnVtYmVyPUNBQS0xMjM0Iiwicm91dGUiOiJvcGVyYXRpb25zLnZlaGljbGVzLnNlYXJjaCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1792597316),
('SDz9yUWDMteiK2nws0u0hOtasT2XbCtJJK8MOMHV', 1, '127.0.0.1', 'Symfony', 'eyJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MSwiX3Rva2VuIjoidkFEWlJXVE92bGFTaVF1WTZ6N3U5bENHTmFReGFnMVdZMzY4aGZUSiIsInBhc3N3b3JkX2hhc2hfd2ViIjoiZDM4ODk2NWVhOTA4YjgzM2I0ZGE3ZTQ1MjUzYmQ5M2JlZTlmYjAxYmMxNGY3ODM1YWUzNzg5Yzc5ZTllZWY0NyIsImFjdGl2ZV9icmFuY2hfaWQiOjEsIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC9sb2NhbGhvc3RcL2JpbGxpbmdcL211bHRpcGxlLWludm9pY2UtcGF5bWVudCIsInJvdXRlIjoiYmlsbGluZy5tdWx0aXBsZS1pbnZvaWNlLXBheW1lbnQuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1792601185),
('uh3T5vVPxQFsxMRp7VUJpUT7Kj2QwRJOXtK9wH0r', 2, '127.0.0.1', 'Symfony', 'eyJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MiwiX3Rva2VuIjoiQ3JkRTVLQjNzNFFYa0F0UjZESmZuazl3cTIxNjF1d3hlOXJERFc3aCIsInBhc3N3b3JkX2hhc2hfd2ViIjoiZTk0Y2Y5MTBhODI2ODMxNDc1ZjAyZGQ5NzNmNTBjOGQzZmFhYjhjZWRjMGYzNWNmYTZiMTVlOGM3YzBiZDRhNCIsImFjdGl2ZV9icmFuY2hfaWQiOjEsIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC9sb2NhbGhvc3RcL2Rhc2hib2FyZCIsInJvdXRlIjoiZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1792601713),
('WSgC8osIqj8EbBwclYwnGzLIu8YoxxA9MPvtuTid', 2, '127.0.0.1', '', 'eyJfdG9rZW4iOiJTZUNONlBEelJINXhZdUZhQlc2Y1N4RjJBM2RQNmhvWFJtZTgwbG15IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoyLCJwYXNzd29yZF9oYXNoX3dlYiI6ImU5NGNmOTEwYTgyNjgzMTQ3NWYwMmRkOTczZjUwYzhkM2ZhYWI4Y2VkYzBmMzVjZmE2YjE1ZThjN2MwYmQ0YTQiLCJhY3RpdmVfYnJhbmNoX2lkIjoxfQ==', 1792593649),
('wvrpMe86VX8ySVc96OJNqtfu2SrejRvZ1KD34zA0', 2, '127.0.0.1', 'Symfony', 'eyJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MiwiX3Rva2VuIjoiY1JYNW9PM0VHeExQMFFRNEwyTzlPanZ1anZLS1pTcGpmODFJMmljYiIsInBhc3N3b3JkX2hhc2hfd2ViIjoiZTk0Y2Y5MTBhODI2ODMxNDc1ZjAyZGQ5NzNmNTBjOGQzZmFhYjhjZWRjMGYzNWNmYTZiMTVlOGM3YzBiZDRhNCIsIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC9sb2NhbGhvc3RcL29wZXJhdGlvbnNcL3ZlaGljbGVzXC9zZWFyY2g/bnVtYmVyPUNBQS0xMjM0Iiwicm91dGUiOiJvcGVyYXRpb25zLnZlaGljbGVzLnNlYXJjaCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1792597343),
('YPUAvEGY5u5u9NCkQcYciLtnF3tTaPjNEK4hMwXk', 1, '127.0.0.1', 'Symfony', 'eyJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MSwiX3Rva2VuIjoibEVwNkFITGY1NVhhaDlmSXQxNkx4WnZXRnBCNVMzN2JibU5NUmZDNyIsInBhc3N3b3JkX2hhc2hfd2ViIjoiZDM4ODk2NWVhOTA4YjgzM2I0ZGE3ZTQ1MjUzYmQ5M2JlZTlmYjAxYmMxNGY3ODM1YWUzNzg5Yzc5ZTllZWY0NyIsImFjdGl2ZV9icmFuY2hfaWQiOjEsIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC9sb2NhbGhvc3RcL2JpbGxpbmdcL211bHRpcGxlLWludm9pY2UtcGF5bWVudCIsInJvdXRlIjoiYmlsbGluZy5tdWx0aXBsZS1pbnZvaWNlLXBheW1lbnQuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1792601405),
('yuvtg4ybzbujku8NBUcYZosPjfHpqsQSC8n3y8gl', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', 'eyJfdG9rZW4iOiI1YjYyNkJncmJWeThzN1gyT1lWWXJzYktVeVdieWhqekRreVhKVU1kIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9fQ==', 1792602058);

-- --------------------------------------------------------

--
-- Table structure for table `stock_batches`
--

CREATE TABLE `stock_batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `batch_number` varchar(255) NOT NULL,
  `grn_id` bigint(20) UNSIGNED NOT NULL,
  `grn_item_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_date` date NOT NULL,
  `supplier_lot_number` varchar(255) DEFAULT NULL,
  `received_quantity` decimal(10,2) NOT NULL,
  `free_quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `remaining_quantity` decimal(10,2) NOT NULL,
  `original_purchase_unit_price` decimal(12,2) NOT NULL,
  `inventory_unit_cost` decimal(12,4) NOT NULL,
  `selling_price_at_receipt` decimal(12,2) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_batches`
--

INSERT INTO `stock_batches` (`id`, `branch_id`, `batch_number`, `grn_id`, `grn_item_id`, `product_id`, `supplier_id`, `purchase_date`, `supplier_lot_number`, `received_quantity`, `free_quantity`, `remaining_quantity`, `original_purchase_unit_price`, `inventory_unit_cost`, `selling_price_at_receipt`, `status`, `created_at`, `updated_at`) VALUES
(16, 1, 'B26-0007-001', 7, 16, 16, 7, '2026-09-05', NULL, 20.00, 0.00, 18.00, 9500.00, 9500.0000, 14500.00, 'active', '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(17, 1, 'B26-0007-002', 7, 17, 17, 7, '2026-09-05', NULL, 15.00, 0.00, 13.00, 7800.00, 7800.0000, 11200.00, 'active', '2026-10-20 04:40:40', '2026-10-20 09:07:03'),
(18, 1, 'B26-0008-001', 8, 18, 18, 8, '2026-09-10', NULL, 32.00, 2.00, 28.00, 1800.00, 1687.5000, 2800.00, 'active', '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(19, 1, 'B26-0008-002', 8, 19, 19, 8, '2026-09-10', NULL, 25.00, 0.00, 24.00, 2200.00, 2200.0000, 3500.00, 'active', '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(20, 1, 'B26-0008-003', 8, 20, 28, 8, '2026-09-10', NULL, 15.00, 0.00, 15.00, 2000.00, 2000.0000, 3200.00, 'active', '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(21, 1, 'B26-0009-001', 9, 21, 20, 9, '2026-09-15', NULL, 18.00, 0.00, 17.00, 6200.00, 6200.0000, 9500.00, 'active', '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(22, 1, 'B26-0009-002', 9, 22, 21, 9, '2026-09-15', NULL, 12.00, 0.00, 11.00, 8000.00, 8000.0000, 12000.00, 'active', '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(23, 1, 'B26-0010-001', 10, 23, 22, 10, '2026-09-20', NULL, 10.00, 0.00, 10.00, 26000.00, 26000.0000, 36000.00, 'active', '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(24, 1, 'B26-0010-002', 10, 24, 23, 10, '2026-09-20', NULL, 20.00, 0.00, 18.00, 4800.00, 4800.0000, 7200.00, 'active', '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(25, 1, 'B26-0010-003', 10, 25, 26, 10, '2026-09-20', NULL, 15.00, 0.00, 14.00, 3100.00, 3100.0000, 4800.00, 'active', '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(26, 1, 'B26-0010-004', 10, 26, 30, 10, '2026-09-20', NULL, 4.00, 0.00, 2.00, 1100.00, 1100.0000, 1800.00, 'active', '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(27, 1, 'B26-0011-001', 11, 27, 24, 11, '2026-09-25', NULL, 25.00, 0.00, 23.00, 1500.00, 1500.0000, 2400.00, 'active', '2026-10-20 04:40:40', '2026-10-21 04:31:24'),
(28, 1, 'B26-0011-002', 11, 28, 25, 11, '2026-09-25', NULL, 20.00, 0.00, 20.00, 1800.00, 1800.0000, 2900.00, 'active', '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(29, 1, 'B26-0011-003', 11, 29, 27, 11, '2026-09-25', NULL, 20.00, 0.00, 19.00, 1300.00, 1300.0000, 2100.00, 'active', '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(30, 1, 'B26-0012-001', 12, 30, 16, 7, '2026-10-10', NULL, 15.00, 0.00, 15.00, 9800.00, 9800.0000, 14500.00, 'active', '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(31, 1, 'B26-09-DEN01', 13, 31, 18, 7, '2026-09-01', NULL, 20.00, 0.00, 10.00, 800.00, 800.0000, 2800.00, 'active', '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(32, 1, 'B26-10-DEN02', 14, 32, 18, 7, '2026-10-01', NULL, 10.00, 0.00, 5.00, 1000.00, 1000.0000, 2800.00, 'active', '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(33, 3, 'B26-10-KND01', 15, 33, 20, 7, '2026-10-03', NULL, 10.00, 0.00, 10.00, 6500.00, 6500.0000, 9500.00, 'active', '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(34, 1, 'B001', 16, 34, 31, 7, '2026-09-01', NULL, 15.00, 0.00, 10.00, 800.00, 800.0000, 1800.00, 'active', '2026-10-20 07:28:24', '2026-10-20 07:28:24'),
(35, 1, 'B002', 17, 35, 31, 7, '2026-10-01', NULL, 8.00, 0.00, 5.00, 1000.00, 1000.0000, 1800.00, 'active', '2026-10-20 07:28:24', '2026-10-20 07:28:24'),
(36, 1, 'BATCH-001', 18, 36, 32, 7, '2026-09-02', NULL, 20.00, 0.00, 15.00, 1500.00, 1500.0000, 2200.00, 'active', '2026-10-20 07:28:24', '2026-10-20 07:28:24'),
(37, 1, 'BATCH-002', 19, 37, 32, 7, '2026-10-02', NULL, 30.00, 0.00, 20.00, 1600.00, 1600.0000, 2200.00, 'active', '2026-10-20 07:28:24', '2026-10-20 07:28:24');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `stock_batch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `movement_type` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_cost` decimal(12,4) NOT NULL,
  `reference_type` varchar(255) DEFAULT NULL,
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `branch_id`, `product_id`, `stock_batch_id`, `movement_type`, `quantity`, `unit_cost`, `reference_type`, `reference_id`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(32, 1, 16, 16, 'grn_receipt', 20.00, 9500.0000, 'App\\Models\\GoodsReceivedNote', 7, 'Stock receipt from GRN #GRN-202609-0001 (Batch B26-0007-001)', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(33, 1, 17, 17, 'grn_receipt', 15.00, 7800.0000, 'App\\Models\\GoodsReceivedNote', 7, 'Stock receipt from GRN #GRN-202609-0001 (Batch B26-0007-002)', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(34, 1, 18, 18, 'grn_receipt', 32.00, 1687.5000, 'App\\Models\\GoodsReceivedNote', 8, 'Stock receipt from GRN #GRN-202609-0002 (Batch B26-0008-001)', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(35, 1, 19, 19, 'grn_receipt', 25.00, 2200.0000, 'App\\Models\\GoodsReceivedNote', 8, 'Stock receipt from GRN #GRN-202609-0002 (Batch B26-0008-002)', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(36, 1, 28, 20, 'grn_receipt', 15.00, 2000.0000, 'App\\Models\\GoodsReceivedNote', 8, 'Stock receipt from GRN #GRN-202609-0002 (Batch B26-0008-003)', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(37, 1, 20, 21, 'grn_receipt', 18.00, 6200.0000, 'App\\Models\\GoodsReceivedNote', 9, 'Stock receipt from GRN #GRN-202609-0003 (Batch B26-0009-001)', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(38, 1, 21, 22, 'grn_receipt', 12.00, 8000.0000, 'App\\Models\\GoodsReceivedNote', 9, 'Stock receipt from GRN #GRN-202609-0003 (Batch B26-0009-002)', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(39, 1, 22, 23, 'grn_receipt', 10.00, 26000.0000, 'App\\Models\\GoodsReceivedNote', 10, 'Stock receipt from GRN #GRN-202609-0004 (Batch B26-0010-001)', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(40, 1, 23, 24, 'grn_receipt', 20.00, 4800.0000, 'App\\Models\\GoodsReceivedNote', 10, 'Stock receipt from GRN #GRN-202609-0004 (Batch B26-0010-002)', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(41, 1, 26, 25, 'grn_receipt', 15.00, 3100.0000, 'App\\Models\\GoodsReceivedNote', 10, 'Stock receipt from GRN #GRN-202609-0004 (Batch B26-0010-003)', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(42, 1, 30, 26, 'grn_receipt', 4.00, 1100.0000, 'App\\Models\\GoodsReceivedNote', 10, 'Stock receipt from GRN #GRN-202609-0004 (Batch B26-0010-004)', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(43, 1, 24, 27, 'grn_receipt', 25.00, 1500.0000, 'App\\Models\\GoodsReceivedNote', 11, 'Stock receipt from GRN #GRN-202609-0005 (Batch B26-0011-001)', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(44, 1, 25, 28, 'grn_receipt', 20.00, 1800.0000, 'App\\Models\\GoodsReceivedNote', 11, 'Stock receipt from GRN #GRN-202609-0005 (Batch B26-0011-002)', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(45, 1, 27, 29, 'grn_receipt', 20.00, 1300.0000, 'App\\Models\\GoodsReceivedNote', 11, 'Stock receipt from GRN #GRN-202609-0005 (Batch B26-0011-003)', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(46, 1, 16, 30, 'grn_receipt', 15.00, 9800.0000, 'App\\Models\\GoodsReceivedNote', 12, 'Stock receipt from GRN #GRN-202610-0006 (Batch B26-0012-001)', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(47, 1, 18, 18, 'job_card', -1.00, 1687.5000, 'App\\Models\\JobCard', 18, 'FIFO issue from Batch B26-0008-001', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(48, 1, 20, 21, 'job_card', -1.00, 6200.0000, 'App\\Models\\JobCard', 19, 'FIFO issue from Batch B26-0009-001', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(49, 1, 16, 16, 'job_card', -1.00, 9500.0000, 'App\\Models\\JobCard', 20, 'FIFO issue from Batch B26-0007-001', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(50, 1, 18, 18, 'job_card', -1.00, 1687.5000, 'App\\Models\\JobCard', 20, 'FIFO issue from Batch B26-0008-001', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(51, 1, 21, 22, 'job_card', -1.00, 8000.0000, 'App\\Models\\JobCard', 21, 'FIFO issue from Batch B26-0009-002', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(52, 1, 18, 18, 'job_card', -1.00, 1687.5000, 'App\\Models\\JobCard', 22, 'FIFO issue from Batch B26-0008-001', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(53, 1, 23, 24, 'job_card', -1.00, 4800.0000, 'App\\Models\\JobCard', 23, 'FIFO issue from Batch B26-0010-002', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(54, 1, 17, 17, 'direct_sale', -1.00, 7800.0000, 'App\\Models\\DirectSale', 6, 'FIFO issue from Batch B26-0007-002', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(55, 1, 18, 18, 'direct_sale', -1.00, 1687.5000, 'App\\Models\\DirectSale', 6, 'FIFO issue from Batch B26-0008-001', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(56, 1, 26, 25, 'direct_sale', -1.00, 3100.0000, 'App\\Models\\DirectSale', 7, 'FIFO issue from Batch B26-0010-003', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(57, 1, 27, 29, 'direct_sale', -1.00, 1300.0000, 'App\\Models\\DirectSale', 7, 'FIFO issue from Batch B26-0011-003', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(58, 1, 30, 26, 'direct_sale', -2.00, 1100.0000, 'App\\Models\\DirectSale', 8, 'FIFO issue from Batch B26-0010-004', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(59, 1, 23, 24, 'direct_sale', -1.00, 4800.0000, 'App\\Models\\DirectSale', 8, 'FIFO issue from Batch B26-0010-002', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(60, 1, 24, 27, 'direct_sale', -1.00, 1500.0000, 'App\\Models\\DirectSale', 9, 'FIFO issue from Batch B26-0011-001', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(61, 1, 16, 16, 'direct_sale', -1.00, 9500.0000, 'App\\Models\\DirectSale', 10, 'FIFO issue from Batch B26-0007-001', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(62, 1, 19, 19, 'direct_sale', -1.00, 2200.0000, 'App\\Models\\DirectSale', 10, 'FIFO issue from Batch B26-0008-002', 2, '2026-10-20 04:40:40', '2026-10-20 07:08:59'),
(63, 1, 18, 31, 'grn_receipt', 20.00, 800.0000, 'App\\Models\\GoodsReceivedNote', 13, 'Test Batch B26-09-DEN01 receipt', 2, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(64, 1, 18, 32, 'grn_receipt', 10.00, 1000.0000, 'App\\Models\\GoodsReceivedNote', 14, 'Test Batch B26-10-DEN02 receipt', 2, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(65, 3, 20, 33, 'grn_receipt', 10.00, 6500.0000, 'App\\Models\\GoodsReceivedNote', 15, 'Kandy Branch Test Batch B26-10-KND01 receipt', 2, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(66, 1, 31, 34, 'grn_receipt', 15.00, 800.0000, 'App\\Models\\GoodsReceivedNote', 16, 'Test Batch B001 receipt (Oil Filter)', 2, '2026-10-20 07:28:24', '2026-10-20 07:28:24'),
(67, 1, 31, 35, 'grn_receipt', 8.00, 1000.0000, 'App\\Models\\GoodsReceivedNote', 17, 'Test Batch B002 receipt (Oil Filter)', 2, '2026-10-20 07:28:24', '2026-10-20 07:28:24'),
(68, 1, 32, 36, 'grn_receipt', 20.00, 1500.0000, 'App\\Models\\GoodsReceivedNote', 18, 'Test Batch 001 receipt (Engine Oil 1L)', 2, '2026-10-20 07:28:24', '2026-10-20 07:28:24'),
(69, 1, 32, 37, 'grn_receipt', 30.00, 1600.0000, 'App\\Models\\GoodsReceivedNote', 19, 'Test Batch 002 receipt (Engine Oil 1L)', 2, '2026-10-20 07:28:24', '2026-10-20 07:28:24'),
(70, 1, 17, 17, 'job_card_issue', -1.00, 7800.0000, 'App\\Models\\JobCard', 30, 'FIFO issue from Batch B26-0007-002', 2, '2026-10-20 09:07:03', '2026-10-20 09:07:03'),
(71, 1, 24, 27, 'job_card_issue', -1.00, 1500.0000, 'App\\Models\\JobCard', 33, 'FIFO issue from Batch B26-0011-001', 2, '2026-10-21 04:31:24', '2026-10-21 04:31:24');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `registration_number` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `office_phone` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `registration_number`, `address`, `mobile`, `fax`, `office_phone`, `is_active`, `created_at`, `updated_at`) VALUES
(7, 'Mobil Lubricants Lanka (Pvt) Ltd', 'PV-MOBIL-101', 'No. 320, T.B. Jaya Mawatha, Colombo 10', '0772233441', NULL, '0112445501', 1, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(8, 'Denso Auto Parts Ceylon', 'PV-DENSO-202', 'No. 88, Panchikawatta Road, Colombo 10', '0714455662', NULL, '0112889902', 1, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(9, 'Ferodo Brake Components Ltd', 'PV-FEROD-303', 'No. 45, Biyagama Export Zone, Kelaniya', '0765566773', NULL, '0112443303', 1, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(10, 'Bosch Automotive Sri Lanka', 'PV-BOSCH-404', 'No. 12, Union Place, Colombo 02', '0726677884', NULL, '0112667704', 1, '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(11, 'Wurth Lanka (Pvt) Ltd', 'PV-WURTH-505', 'No. 375, Negombo Road, Wattala', '0757788995', NULL, '0112998805', 1, '2026-10-20 04:40:40', '2026-10-20 04:40:40');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_payments`
--

CREATE TABLE `supplier_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_number` varchar(50) DEFAULT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `grn_id` bigint(20) UNSIGNED NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'completed',
  `reversed_at` datetime DEFAULT NULL,
  `reversed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reversal_reason` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier_payments`
--

INSERT INTO `supplier_payments` (`id`, `payment_number`, `supplier_id`, `branch_id`, `grn_id`, `payment_date`, `amount`, `payment_method`, `reference_number`, `notes`, `status`, `reversed_at`, `reversed_by`, `reversal_reason`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'SUP-PAY-202610-001', 7, 1, 7, '2026-10-14', 45000.00, 'bank_transfer', 'TRX-SUP-443322', 'Partial settlement for verified inventory GRN.', 'completed', NULL, NULL, NULL, 2, '2026-10-20 07:15:21', '2026-10-20 07:15:21');

-- --------------------------------------------------------

--
-- Table structure for table `units_of_measurement`
--

CREATE TABLE `units_of_measurement` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units_of_measurement`
--

INSERT INTO `units_of_measurement` (`id`, `name`, `code`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Bottle', 'BTL', 1, '2026-10-19 07:16:05', '2026-10-19 07:16:05'),
(2, 'Can', 'CAN', 1, '2026-10-19 07:16:05', '2026-10-19 07:16:05'),
(3, 'Packet', 'PKT', 1, '2026-10-19 07:16:05', '2026-10-19 07:16:05'),
(4, 'Item', 'ITM', 1, '2026-10-19 07:16:05', '2026-10-19 07:16:05'),
(5, 'Loose', 'LSE', 1, '2026-10-19 07:16:05', '2026-10-19 07:16:05'),
(6, 'Tube', 'TUB', 1, '2026-10-19 07:16:05', '2026-10-19 07:16:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'staff',
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `role`, `status`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Kamal Perera', 'staff@autocare.com', '+94 77 123 4567', 'staff', 'active', NULL, '$2y$12$30krr0TNFmhS7ZgwPRVmregLgWGWRKbSPPaBj1VfCzqeriOgnDYSu', NULL, '2026-10-19 06:08:35', '2026-10-19 06:08:35'),
(2, 'Admin User', 'admin@gmail.com', '+94 77 987 6543', 'admin', 'active', NULL, '$2y$12$PXvdioOhTCXVpaphayQO0.r8/GfWwP10sN1sUXBvrPqgfArZ1RyUG', NULL, '2026-10-19 06:08:40', '2026-10-20 02:36:18');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_type_id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_number` varchar(255) NOT NULL,
  `vehicle_number_normalized` varchar(255) NOT NULL,
  `make_model` varchar(255) DEFAULT NULL,
  `year_of_manufacture` varchar(10) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `chassis_number` varchar(100) DEFAULT NULL,
  `engine_number` varchar(100) DEFAULT NULL,
  `current_mileage` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `next_service_mileage` int(10) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `branch_id`, `customer_id`, `vehicle_type_id`, `vehicle_number`, `vehicle_number_normalized`, `make_model`, `year_of_manufacture`, `color`, `chassis_number`, `engine_number`, `current_mileage`, `next_service_mileage`, `notes`, `created_at`, `updated_at`) VALUES
(16, 1, 11, 1, 'WP-CAB-5544', 'WPCAB5544', 'Toyota Prius Hybrid 1.8L', '2017', 'Pearl White', NULL, NULL, 84500, 89500, 'Registered for routine workshop servicing.', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(17, 1, 11, 1, 'WP-CAR-1289', 'WPCAR1289', 'Honda Fit GP5 Hybrid', '2015', 'Silver Metallic', NULL, NULL, 96200, 101200, 'Registered for routine workshop servicing.', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(18, 3, 12, 1, 'CP-KH-8921', 'CPKH8921', 'Toyota Corolla 141 GLI', '2011', 'Dark Grey', NULL, NULL, 135000, 140000, 'Registered for routine workshop servicing.', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(19, 1, 19, 2, 'WP-KX-7788', 'WPKX7788', 'Toyota HiAce KDH 201', '2018', 'White', NULL, NULL, 152000, 157000, 'Registered for routine workshop servicing.', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(20, 1, 19, 2, 'WP-KY-3321', 'WPKY3321', 'Nissan Caravan NV350', '2019', 'Silver', NULL, NULL, 128400, 133400, 'Registered for routine workshop servicing.', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(21, 1, 19, 1, 'WP-CAR-9090', 'WPCAR9090', 'Suzuki Wagon R Stingray FX', '2018', 'Black', NULL, NULL, 74100, 79100, 'Registered for routine workshop servicing.', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(22, 1, 20, 5, 'WP-LY-4455', 'WPLY4455', 'Isuzu ELF 14.5ft Flatbed', '2016', 'White', NULL, NULL, 189000, 194000, 'Registered for routine workshop servicing.', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(23, 1, 20, 5, 'WP-LZ-6677', 'WPLZ6677', 'Mitsubishi Fuso Canter 16ft', '2017', 'Blue', NULL, NULL, 164000, 169000, 'Registered for routine workshop servicing.', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(24, 3, 20, 2, 'CP-LE-1122', 'CPLE1122', 'Toyota HiAce Super GL', '2015', 'Silver', NULL, NULL, 141000, 146000, 'Registered for routine workshop servicing.', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(25, 1, 13, 3, 'SP-BC-4412', 'SPBC4412', 'Mitsubishi Montero Sport 4WD', '2016', 'Titanium Grey', NULL, NULL, 112000, 117000, 'Registered for routine workshop servicing.', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(26, 1, 14, 3, 'WP-CAD-8822', 'WPCAD8822', 'Toyota Land Cruiser Prado TX', '2019', 'Black Mica', NULL, NULL, 68000, 73000, 'Registered for routine workshop servicing.', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(27, 1, 15, 4, 'WP-BHM-9911', 'WPBHM9911', 'Yamaha FZ-S V3 150cc', '2021', 'Matte Blue', NULL, NULL, 24500, 29500, 'Registered for routine workshop servicing.', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(28, 1, 16, 4, 'WP-BIP-4567', 'WPBIP4567', 'Honda Dio 110cc Scooter', '2020', 'Sports Red', NULL, NULL, 31200, 36200, 'Registered for routine workshop servicing.', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(29, 1, 17, 6, 'WP-AAH-3344', 'WPAAH3344', 'Bajaj RE 4S 205cc Auto Rickshaw', '2022', 'Golden Yellow', NULL, NULL, 18000, 23000, 'Registered for routine workshop servicing.', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(30, 1, 18, 1, 'WP-CBB-1100', 'WPCBB1100', 'Toyota Vitz KSP130 1.0L', '2019', 'Wine Red', NULL, NULL, 45000, 50000, 'Registered for routine workshop servicing.', '2026-10-20 04:40:40', '2026-10-20 04:40:40'),
(31, NULL, 21, 2, 'BFH-4242', 'BFH4242', NULL, NULL, NULL, NULL, NULL, 3400, 6900, NULL, '2026-10-20 04:49:55', '2026-10-21 02:12:10'),
(32, 1, 22, 2, 'CAB-1234', 'CAB1234', 'Toyota HiAce Super GL KDH201', NULL, NULL, NULL, NULL, 55000, 60000, NULL, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(33, 1, 22, 1, 'CAA-5678', 'CAA5678', 'Toyota Corolla Axio 1.5L', NULL, NULL, NULL, NULL, 32000, 37000, NULL, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(34, 1, 22, 5, 'BCD-7890', 'BCD7890', 'Isuzu ELF 14.5ft Flatbed', NULL, NULL, NULL, NULL, 78000, 83000, NULL, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(35, 1, 23, 5, 'WP-NB-1122', 'WPNB1122', 'Mitsubishi Fuso Canter 16ft', NULL, NULL, NULL, NULL, 110000, 115000, NULL, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(36, 1, 23, 5, 'WP-NB-3344', 'WPNB3344', 'Isuzu Forward 20ft Cargo Carrier', NULL, NULL, NULL, NULL, 145000, 150000, NULL, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(37, 1, 24, 1, 'WP-CAE-4554', 'WPCAE4554', 'Toyota Allion NZT260 1.5L', NULL, NULL, NULL, NULL, 42000, 47000, NULL, '2026-10-20 07:10:07', '2026-10-20 07:10:07'),
(38, 1, 20, 2, 'WP-CAB-8811', 'WPCAB8811', 'Toyota HiAce GL Tour Van', NULL, NULL, NULL, NULL, 88000, 93000, NULL, '2026-10-20 07:28:24', '2026-10-20 07:28:24'),
(39, 1, 24, 1, 'WP-NEW-1010', 'WPNEW1010', 'Honda Grace Hybrid EX 1.5L', NULL, NULL, NULL, NULL, 15000, 20000, NULL, '2026-10-20 07:28:24', '2026-10-20 07:28:24'),
(40, 1, 22, 1, 'WP-NEW-2020', 'WPNEW2020', 'Toyota Vitz F Safety Edition 1.0L', NULL, NULL, NULL, NULL, 28000, 33000, NULL, '2026-10-20 07:28:24', '2026-10-20 07:28:24'),
(42, NULL, 21, 4, 'BFH-42424', 'BFH42424', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-10-21 02:12:30', '2026-10-21 02:54:55'),
(43, NULL, 21, 1, 'BFH-42424JJ', 'BFH42424JJ', NULL, NULL, NULL, NULL, NULL, 2, 3502, NULL, '2026-10-21 08:46:40', '2026-10-21 08:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_types`
--

CREATE TABLE `vehicle_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `enable_carpets` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicle_types`
--

INSERT INTO `vehicle_types` (`id`, `name`, `image`, `enable_carpets`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Car', 'vehicle-types/qGqyscnQbYt2CpkgkHH7bpeENNIwVdra2BnV1R4S.png', 1, 'Standard passenger sedans, hatchbacks, and coupes.', 'active', '2026-10-19 06:08:35', '2026-10-20 04:18:49'),
(2, 'Van', 'vehicle-types/KbbfsYkekix9AphUzOegMnAVyTocoPBWzD7YzXO9.png', 1, 'Light passenger vans and commercial cargo transporters.', 'active', '2026-10-19 06:08:35', '2026-10-20 04:19:21'),
(3, 'SUV', 'vehicle-types/zoL3ws9bJS1sY2qTadqNVAmnadeozlMbc7T3QqQF.png', 1, 'Sport utility vehicles, compact crossovers, and 4x4 jeeps.', 'active', '2026-10-19 06:08:35', '2026-10-20 04:19:15'),
(4, 'Motorbike', 'vehicle-types/o56hcpe8BRMP153WVjbRrLnlIAT5Ps1v4R3HWkXN.png', 0, 'Two-wheel motorcycles, commuters, and motor scooters.', 'active', '2026-10-19 06:08:35', '2026-10-20 04:19:06'),
(5, 'Lorry', 'vehicle-types/sXxEZpZvlh2B9G0UiDW4ifMBUpAjnGGYMGKFnINZ.png', 0, 'Light trucks, flatbeds, and commercial transport vehicles.', 'active', '2026-10-19 06:08:35', '2026-10-20 04:18:56'),
(6, 'Three-Wheeler', 'vehicle-types/lvDxsinxeEgUyIQv0X3SjgLTVDEmKa4Q5J27tZRo.png', 0, 'Three-wheel passenger and cargo auto rickshaws.', 'active', '2026-10-20 04:18:21', '2026-10-20 04:40:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `appointments_appointment_number_unique` (`appointment_number`),
  ADD KEY `appointments_branch_id_foreign` (`branch_id`),
  ADD KEY `appointments_customer_id_foreign` (`customer_id`),
  ADD KEY `appointments_vehicle_id_foreign` (`vehicle_id`),
  ADD KEY `appointments_vehicle_type_id_foreign` (`vehicle_type_id`),
  ADD KEY `appointments_service_id_foreign` (`service_id`),
  ADD KEY `appointments_job_card_id_foreign` (`job_card_id`),
  ADD KEY `appointments_created_by_foreign` (`created_by`),
  ADD KEY `appointments_appointment_date_index` (`appointment_date`),
  ADD KEY `appointments_appointment_time_index` (`appointment_time`),
  ADD KEY `appointments_status_index` (`status`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branches_company_id_branch_code_unique` (`company_id`,`branch_code`),
  ADD KEY `branches_manager_id_foreign` (`manager_id`);

--
-- Indexes for table `branch_user`
--
ALTER TABLE `branch_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branch_user_branch_id_user_id_unique` (`branch_id`,`user_id`),
  ADD KEY `branch_user_user_id_foreign` (`user_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `carpet_categories`
--
ALTER TABLE `carpet_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `carpet_categories_name_unique` (`name`);

--
-- Indexes for table `carpet_category_vehicle_type`
--
ALTER TABLE `carpet_category_vehicle_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vtype_carpet_cat_unique` (`vehicle_type_id`,`carpet_category_id`),
  ADD KEY `carpet_category_vehicle_type_carpet_category_id_foreign` (`carpet_category_id`);

--
-- Indexes for table `company_settings`
--
ALTER TABLE `company_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customers_phone_index` (`phone`);

--
-- Indexes for table `customer_credit_accounts`
--
ALTER TABLE `customer_credit_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_credit_accounts_customer_id_unique` (`customer_id`),
  ADD KEY `customer_credit_accounts_created_by_foreign` (`created_by`);

--
-- Indexes for table `direct_sales`
--
ALTER TABLE `direct_sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `direct_sales_sale_number_unique` (`sale_number`),
  ADD KEY `direct_sales_invoice_id_foreign` (`invoice_id`),
  ADD KEY `direct_sales_customer_id_foreign` (`customer_id`),
  ADD KEY `direct_sales_created_by_foreign` (`created_by`),
  ADD KEY `direct_sales_status_index` (`status`),
  ADD KEY `direct_sales_branch_id_foreign` (`branch_id`),
  ADD KEY `direct_sales_cancelled_by_foreign` (`cancelled_by`);

--
-- Indexes for table `direct_sale_items`
--
ALTER TABLE `direct_sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `direct_sale_items_direct_sale_id_foreign` (`direct_sale_id`),
  ADD KEY `direct_sale_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_employee_number_unique` (`employee_number`),
  ADD KEY `employees_user_id_foreign` (`user_id`),
  ADD KEY `employees_mobile_index` (`mobile`),
  ADD KEY `employees_is_active_index` (`is_active`),
  ADD KEY `employees_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `employee_advances`
--
ALTER TABLE `employee_advances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_advances_advance_number_unique` (`advance_number`),
  ADD KEY `employee_advances_employee_id_foreign` (`employee_id`),
  ADD KEY `employee_advances_payroll_item_id_foreign` (`payroll_item_id`),
  ADD KEY `employee_advances_created_by_foreign` (`created_by`),
  ADD KEY `employee_advances_advance_date_index` (`advance_date`),
  ADD KEY `employee_advances_status_index` (`status`);

--
-- Indexes for table `employee_attendance`
--
ALTER TABLE `employee_attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_emp_attendance_date` (`employee_id`,`attendance_date`),
  ADD KEY `employee_attendance_recorded_by_foreign` (`recorded_by`),
  ADD KEY `employee_attendance_attendance_date_index` (`attendance_date`),
  ADD KEY `employee_attendance_status_index` (`status`);

--
-- Indexes for table `employee_wage_rates`
--
ALTER TABLE `employee_wage_rates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_wage_rates_employee_id_foreign` (`employee_id`),
  ADD KEY `employee_wage_rates_effective_from_index` (`effective_from`),
  ADD KEY `employee_wage_rates_effective_to_index` (`effective_to`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `expenses_expense_number_unique` (`expense_number`),
  ADD KEY `expenses_category_id_foreign` (`category_id`),
  ADD KEY `expenses_employee_id_foreign` (`employee_id`),
  ADD KEY `expenses_employee_advance_id_foreign` (`employee_advance_id`),
  ADD KEY `expenses_voided_by_foreign` (`voided_by`),
  ADD KEY `expenses_created_by_foreign` (`created_by`),
  ADD KEY `expenses_expense_type_index` (`expense_type`),
  ADD KEY `expenses_expense_date_index` (`expense_date`),
  ADD KEY `expenses_related_month_index` (`related_month`),
  ADD KEY `expenses_payment_date_index` (`payment_date`),
  ADD KEY `expenses_status_index` (`status`),
  ADD KEY `expenses_branch_id_foreign` (`branch_id`),
  ADD KEY `expenses_payment_status_index` (`payment_status`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `expense_categories_name_unique` (`name`),
  ADD KEY `expense_categories_is_active_index` (`is_active`);

--
-- Indexes for table `expense_payments`
--
ALTER TABLE `expense_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `expense_payments_payment_number_unique` (`payment_number`),
  ADD KEY `expense_payments_expense_id_foreign` (`expense_id`),
  ADD KEY `expense_payments_branch_id_foreign` (`branch_id`),
  ADD KEY `expense_payments_created_by_foreign` (`created_by`),
  ADD KEY `expense_payments_payment_date_index` (`payment_date`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `goods_received_notes`
--
ALTER TABLE `goods_received_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `goods_received_notes_grn_number_unique` (`grn_number`),
  ADD KEY `goods_received_notes_supplier_id_foreign` (`supplier_id`),
  ADD KEY `goods_received_notes_posted_by_foreign` (`posted_by`),
  ADD KEY `goods_received_notes_created_by_foreign` (`created_by`),
  ADD KEY `goods_received_notes_received_date_status_index` (`received_date`,`status`),
  ADD KEY `goods_received_notes_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `goods_received_note_items`
--
ALTER TABLE `goods_received_note_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `goods_received_note_items_grn_id_foreign` (`grn_id`),
  ADD KEY `goods_received_note_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `invoices_customer_id_foreign` (`customer_id`),
  ADD KEY `invoices_created_by_foreign` (`created_by`),
  ADD KEY `invoices_job_card_id_index` (`job_card_id`),
  ADD KEY `invoices_invoice_type_index` (`invoice_type`),
  ADD KEY `invoices_status_index` (`status`),
  ADD KEY `invoices_branch_id_foreign` (`branch_id`),
  ADD KEY `invoices_quotation_id_foreign` (`quotation_id`),
  ADD KEY `invoices_cancelled_by_foreign` (`cancelled_by`),
  ADD KEY `invoices_date_changed_by_foreign` (`date_changed_by`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_items_invoice_id_foreign` (`invoice_id`),
  ADD KEY `invoice_items_source_quotation_item_id_foreign` (`source_quotation_item_id`);

--
-- Indexes for table `invoice_sequences`
--
ALTER TABLE `invoice_sequences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_sequences_series_unique` (`series`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_cards`
--
ALTER TABLE `job_cards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_cards_job_number_unique` (`job_number`),
  ADD KEY `job_cards_customer_id_foreign` (`customer_id`),
  ADD KEY `job_cards_vehicle_id_foreign` (`vehicle_id`),
  ADD KEY `job_cards_vehicle_type_id_foreign` (`vehicle_type_id`),
  ADD KEY `job_cards_assigned_to_foreign` (`assigned_to`),
  ADD KEY `job_cards_invoice_id_foreign` (`invoice_id`),
  ADD KEY `job_cards_cancelled_by_foreign` (`cancelled_by`),
  ADD KEY `job_cards_created_by_foreign` (`created_by`),
  ADD KEY `job_cards_status_index` (`status`),
  ADD KEY `job_cards_branch_id_foreign` (`branch_id`),
  ADD KEY `job_cards_appointment_id_foreign` (`appointment_id`);

--
-- Indexes for table `job_card_carpets`
--
ALTER TABLE `job_card_carpets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_card_carpets_job_card_id_foreign` (`job_card_id`),
  ADD KEY `job_card_carpets_carpet_category_id_foreign` (`carpet_category_id`);

--
-- Indexes for table `job_card_products`
--
ALTER TABLE `job_card_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_card_products_job_card_id_foreign` (`job_card_id`),
  ADD KEY `job_card_products_product_id_foreign` (`product_id`),
  ADD KEY `job_card_products_job_issue_id_foreign` (`job_issue_id`),
  ADD KEY `job_card_products_invoice_id_foreign` (`invoice_id`);

--
-- Indexes for table `job_card_services`
--
ALTER TABLE `job_card_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_card_services_job_card_id_foreign` (`job_card_id`),
  ADD KEY `job_card_services_service_id_foreign` (`service_id`),
  ADD KEY `job_card_services_job_issue_id_foreign` (`job_issue_id`),
  ADD KEY `job_card_services_invoice_id_foreign` (`invoice_id`);

--
-- Indexes for table `job_issues`
--
ALTER TABLE `job_issues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_issues_job_card_id_foreign` (`job_card_id`),
  ADD KEY `job_issues_vehicle_id_foreign` (`vehicle_id`),
  ADD KEY `job_issues_approved_by_foreign` (`approved_by`),
  ADD KEY `job_issues_recorded_by_foreign` (`recorded_by`),
  ADD KEY `job_issues_supplementary_invoice_id_foreign` (`supplementary_invoice_id`),
  ADD KEY `job_issues_status_index` (`status`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_payment_number_unique` (`payment_number`),
  ADD KEY `payments_invoice_id_foreign` (`invoice_id`),
  ADD KEY `payments_customer_id_foreign` (`customer_id`),
  ADD KEY `payments_created_by_foreign` (`created_by`),
  ADD KEY `payments_payment_receipt_id_foreign` (`payment_receipt_id`),
  ADD KEY `payments_job_card_id_foreign` (`job_card_id`);

--
-- Indexes for table `payment_allocations`
--
ALTER TABLE `payment_allocations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_allocations_invoice_id_foreign` (`invoice_id`),
  ADD KEY `payment_allocations_payment_receipt_id_invoice_id_index` (`payment_receipt_id`,`invoice_id`);

--
-- Indexes for table `payment_receipts`
--
ALTER TABLE `payment_receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_receipts_receipt_number_unique` (`receipt_number`),
  ADD KEY `payment_receipts_branch_id_foreign` (`branch_id`),
  ADD KEY `payment_receipts_customer_id_foreign` (`customer_id`),
  ADD KEY `payment_receipts_created_by_foreign` (`created_by`);

--
-- Indexes for table `payroll_items`
--
ALTER TABLE `payroll_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_period_employee` (`payroll_period_id`,`employee_id`),
  ADD KEY `payroll_items_employee_id_foreign` (`employee_id`),
  ADD KEY `payroll_items_status_index` (`status`);

--
-- Indexes for table `payroll_payments`
--
ALTER TABLE `payroll_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payroll_payments_payment_number_unique` (`payment_number`),
  ADD KEY `payroll_payments_payroll_item_id_foreign` (`payroll_item_id`),
  ADD KEY `payroll_payments_recorded_by_foreign` (`recorded_by`),
  ADD KEY `payroll_payments_payment_date_index` (`payment_date`);

--
-- Indexes for table `payroll_periods`
--
ALTER TABLE `payroll_periods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_payroll_year_month` (`year`,`month`),
  ADD KEY `payroll_periods_approved_by_foreign` (`approved_by`),
  ADD KEY `payroll_periods_year_index` (`year`),
  ADD KEY `payroll_periods_month_index` (`month`),
  ADD KEY `payroll_periods_status_index` (`status`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_sku_unique` (`sku`),
  ADD KEY `products_uom_id_foreign` (`uom_id`),
  ADD KEY `products_preferred_supplier_id_foreign` (`preferred_supplier_id`),
  ADD KEY `products_name_sku_index` (`name`,`sku`);

--
-- Indexes for table `quotations`
--
ALTER TABLE `quotations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quotations_quotation_number_unique` (`quotation_number`),
  ADD KEY `quotations_branch_id_foreign` (`branch_id`),
  ADD KEY `quotations_customer_id_foreign` (`customer_id`),
  ADD KEY `quotations_vehicle_id_foreign` (`vehicle_id`),
  ADD KEY `quotations_created_by_foreign` (`created_by`),
  ADD KEY `quotations_status_index` (`status`),
  ADD KEY `quotations_converted_invoice_id_index` (`converted_invoice_id`),
  ADD KEY `quotations_converted_job_card_id_index` (`converted_job_card_id`);

--
-- Indexes for table `quotation_items`
--
ALTER TABLE `quotation_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quotation_items_quotation_id_foreign` (`quotation_id`),
  ADD KEY `quotation_items_item_id_index` (`item_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `services_code_unique` (`code`),
  ADD KEY `services_status_index` (`status`);

--
-- Indexes for table `service_vehicle_prices`
--
ALTER TABLE `service_vehicle_prices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `service_vehicle_prices_service_id_vehicle_type_id_unique` (`service_id`,`vehicle_type_id`),
  ADD KEY `service_vehicle_prices_vehicle_type_id_foreign` (`vehicle_type_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `stock_batches`
--
ALTER TABLE `stock_batches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_batches_batch_number_unique` (`batch_number`),
  ADD KEY `stock_batches_grn_id_foreign` (`grn_id`),
  ADD KEY `stock_batches_grn_item_id_foreign` (`grn_item_id`),
  ADD KEY `stock_batches_supplier_id_foreign` (`supplier_id`),
  ADD KEY `stock_batches_product_id_status_remaining_quantity_index` (`product_id`,`status`,`remaining_quantity`),
  ADD KEY `stock_batches_product_id_purchase_date_id_index` (`product_id`,`purchase_date`,`id`),
  ADD KEY `stock_batches_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_movements_stock_batch_id_foreign` (`stock_batch_id`),
  ADD KEY `stock_movements_created_by_foreign` (`created_by`),
  ADD KEY `stock_movements_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  ADD KEY `stock_movements_product_id_created_at_index` (`product_id`,`created_at`),
  ADD KEY `stock_movements_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `suppliers_name_unique` (`name`);

--
-- Indexes for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `supplier_payments_payment_number_unique` (`payment_number`),
  ADD KEY `supplier_payments_created_by_foreign` (`created_by`),
  ADD KEY `supplier_payments_supplier_id_payment_date_index` (`supplier_id`,`payment_date`),
  ADD KEY `supplier_payments_grn_id_index` (`grn_id`),
  ADD KEY `supplier_payments_branch_id_foreign` (`branch_id`),
  ADD KEY `supplier_payments_reversed_by_foreign` (`reversed_by`);

--
-- Indexes for table `units_of_measurement`
--
ALTER TABLE `units_of_measurement`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `units_of_measurement_name_unique` (`name`),
  ADD UNIQUE KEY `units_of_measurement_code_unique` (`code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicles_vehicle_number_normalized_unique` (`vehicle_number_normalized`),
  ADD KEY `vehicles_customer_id_foreign` (`customer_id`),
  ADD KEY `vehicles_vehicle_type_id_foreign` (`vehicle_type_id`),
  ADD KEY `vehicles_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `vehicle_types`
--
ALTER TABLE `vehicle_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicle_types_name_unique` (`name`),
  ADD KEY `vehicle_types_status_index` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `branch_user`
--
ALTER TABLE `branch_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `carpet_categories`
--
ALTER TABLE `carpet_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `carpet_category_vehicle_type`
--
ALTER TABLE `carpet_category_vehicle_type`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `company_settings`
--
ALTER TABLE `company_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `customer_credit_accounts`
--
ALTER TABLE `customer_credit_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `direct_sales`
--
ALTER TABLE `direct_sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `direct_sale_items`
--
ALTER TABLE `direct_sale_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `employee_advances`
--
ALTER TABLE `employee_advances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `employee_attendance`
--
ALTER TABLE `employee_attendance`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=291;

--
-- AUTO_INCREMENT for table `employee_wage_rates`
--
ALTER TABLE `employee_wage_rates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `expense_payments`
--
ALTER TABLE `expense_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goods_received_notes`
--
ALTER TABLE `goods_received_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `goods_received_note_items`
--
ALTER TABLE `goods_received_note_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `invoice_sequences`
--
ALTER TABLE `invoice_sequences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_cards`
--
ALTER TABLE `job_cards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `job_card_carpets`
--
ALTER TABLE `job_card_carpets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `job_card_products`
--
ALTER TABLE `job_card_products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `job_card_services`
--
ALTER TABLE `job_card_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `job_issues`
--
ALTER TABLE `job_issues`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `payment_allocations`
--
ALTER TABLE `payment_allocations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment_receipts`
--
ALTER TABLE `payment_receipts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payroll_items`
--
ALTER TABLE `payroll_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `payroll_payments`
--
ALTER TABLE `payroll_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payroll_periods`
--
ALTER TABLE `payroll_periods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `quotation_items`
--
ALTER TABLE `quotation_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `service_vehicle_prices`
--
ALTER TABLE `service_vehicle_prices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `stock_batches`
--
ALTER TABLE `stock_batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `units_of_measurement`
--
ALTER TABLE `units_of_measurement`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `vehicle_types`
--
ALTER TABLE `vehicle_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `appointments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `appointments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `appointments_job_card_id_foreign` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `appointments_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `appointments_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `appointments_vehicle_type_id_foreign` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_types` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `branches`
--
ALTER TABLE `branches`
  ADD CONSTRAINT `branches_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `company_settings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `branches_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `branch_user`
--
ALTER TABLE `branch_user`
  ADD CONSTRAINT `branch_user_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `branch_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `carpet_category_vehicle_type`
--
ALTER TABLE `carpet_category_vehicle_type`
  ADD CONSTRAINT `carpet_category_vehicle_type_carpet_category_id_foreign` FOREIGN KEY (`carpet_category_id`) REFERENCES `carpet_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carpet_category_vehicle_type_vehicle_type_id_foreign` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_credit_accounts`
--
ALTER TABLE `customer_credit_accounts`
  ADD CONSTRAINT `customer_credit_accounts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `customer_credit_accounts_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `direct_sales`
--
ALTER TABLE `direct_sales`
  ADD CONSTRAINT `direct_sales_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `direct_sales_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `direct_sales_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `direct_sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `direct_sales_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `direct_sale_items`
--
ALTER TABLE `direct_sale_items`
  ADD CONSTRAINT `direct_sale_items_direct_sale_id_foreign` FOREIGN KEY (`direct_sale_id`) REFERENCES `direct_sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `direct_sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_advances`
--
ALTER TABLE `employee_advances`
  ADD CONSTRAINT `employee_advances_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_advances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_advances_payroll_item_id_foreign` FOREIGN KEY (`payroll_item_id`) REFERENCES `payroll_items` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_attendance`
--
ALTER TABLE `employee_attendance`
  ADD CONSTRAINT `employee_attendance_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_attendance_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_wage_rates`
--
ALTER TABLE `employee_wage_rates`
  ADD CONSTRAINT `employee_wage_rates_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expenses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`),
  ADD CONSTRAINT `expenses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expenses_employee_advance_id_foreign` FOREIGN KEY (`employee_advance_id`) REFERENCES `employee_advances` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expenses_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expenses_voided_by_foreign` FOREIGN KEY (`voided_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `expense_payments`
--
ALTER TABLE `expense_payments`
  ADD CONSTRAINT `expense_payments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expense_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expense_payments_expense_id_foreign` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `goods_received_notes`
--
ALTER TABLE `goods_received_notes`
  ADD CONSTRAINT `goods_received_notes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `goods_received_notes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `goods_received_notes_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `goods_received_notes_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `goods_received_note_items`
--
ALTER TABLE `goods_received_note_items`
  ADD CONSTRAINT `goods_received_note_items_grn_id_foreign` FOREIGN KEY (`grn_id`) REFERENCES `goods_received_notes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `goods_received_note_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoices_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_date_changed_by_foreign` FOREIGN KEY (`date_changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoices_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoice_items_source_quotation_item_id_foreign` FOREIGN KEY (`source_quotation_item_id`) REFERENCES `quotation_items` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `job_cards`
--
ALTER TABLE `job_cards`
  ADD CONSTRAINT `job_cards_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_cards_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_cards_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_cards_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_cards_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_cards_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_cards_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_cards_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_cards_vehicle_type_id_foreign` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_types` (`id`);

--
-- Constraints for table `job_card_carpets`
--
ALTER TABLE `job_card_carpets`
  ADD CONSTRAINT `job_card_carpets_carpet_category_id_foreign` FOREIGN KEY (`carpet_category_id`) REFERENCES `carpet_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_card_carpets_job_card_id_foreign` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_card_products`
--
ALTER TABLE `job_card_products`
  ADD CONSTRAINT `job_card_products_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_card_products_job_card_id_foreign` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_card_products_job_issue_id_foreign` FOREIGN KEY (`job_issue_id`) REFERENCES `job_issues` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_card_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `job_card_services`
--
ALTER TABLE `job_card_services`
  ADD CONSTRAINT `job_card_services_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_card_services_job_card_id_foreign` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_card_services_job_issue_id_foreign` FOREIGN KEY (`job_issue_id`) REFERENCES `job_issues` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_card_services_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);

--
-- Constraints for table `job_issues`
--
ALTER TABLE `job_issues`
  ADD CONSTRAINT `job_issues_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_issues_job_card_id_foreign` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_issues_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_issues_supplementary_invoice_id_foreign` FOREIGN KEY (`supplementary_invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_issues_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_job_card_id_foreign` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_payment_receipt_id_foreign` FOREIGN KEY (`payment_receipt_id`) REFERENCES `payment_receipts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payment_allocations`
--
ALTER TABLE `payment_allocations`
  ADD CONSTRAINT `payment_allocations_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_allocations_payment_receipt_id_foreign` FOREIGN KEY (`payment_receipt_id`) REFERENCES `payment_receipts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_receipts`
--
ALTER TABLE `payment_receipts`
  ADD CONSTRAINT `payment_receipts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_receipts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_receipts_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll_items`
--
ALTER TABLE `payroll_items`
  ADD CONSTRAINT `payroll_items_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payroll_items_payroll_period_id_foreign` FOREIGN KEY (`payroll_period_id`) REFERENCES `payroll_periods` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll_payments`
--
ALTER TABLE `payroll_payments`
  ADD CONSTRAINT `payroll_payments_payroll_item_id_foreign` FOREIGN KEY (`payroll_item_id`) REFERENCES `payroll_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payroll_payments_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payroll_periods`
--
ALTER TABLE `payroll_periods`
  ADD CONSTRAINT `payroll_periods_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_preferred_supplier_id_foreign` FOREIGN KEY (`preferred_supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_uom_id_foreign` FOREIGN KEY (`uom_id`) REFERENCES `units_of_measurement` (`id`);

--
-- Constraints for table `quotations`
--
ALTER TABLE `quotations`
  ADD CONSTRAINT `quotations_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `quotations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `quotations_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `quotations_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `quotation_items`
--
ALTER TABLE `quotation_items`
  ADD CONSTRAINT `quotation_items_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_vehicle_prices`
--
ALTER TABLE `service_vehicle_prices`
  ADD CONSTRAINT `service_vehicle_prices_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_vehicle_prices_vehicle_type_id_foreign` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_batches`
--
ALTER TABLE `stock_batches`
  ADD CONSTRAINT `stock_batches_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_batches_grn_id_foreign` FOREIGN KEY (`grn_id`) REFERENCES `goods_received_notes` (`id`),
  ADD CONSTRAINT `stock_batches_grn_item_id_foreign` FOREIGN KEY (`grn_item_id`) REFERENCES `goods_received_note_items` (`id`),
  ADD CONSTRAINT `stock_batches_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `stock_batches_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_movements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `stock_movements_stock_batch_id_foreign` FOREIGN KEY (`stock_batch_id`) REFERENCES `stock_batches` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD CONSTRAINT `supplier_payments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `supplier_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `supplier_payments_grn_id_foreign` FOREIGN KEY (`grn_id`) REFERENCES `goods_received_notes` (`id`),
  ADD CONSTRAINT `supplier_payments_reversed_by_foreign` FOREIGN KEY (`reversed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `supplier_payments_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vehicles_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vehicles_vehicle_type_id_foreign` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_types` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

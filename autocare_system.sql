-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Oct 28, 2026 at 09:35 AM
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
(128, 2, 'login', 'Authentication', 'User Admin User (admin) logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:01:38', '2026-10-27 06:01:38'),
(129, 2, 'create_employee', 'Payroll', 'Registered new employee Kamal Ranjith (EMP-0001) as Monthly employee (Monthly Basic LKR 85,000.00)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:01:57', '2026-10-27 06:01:57'),
(130, 2, 'create_employee', 'Payroll', 'Registered new employee Nimal Perera (EMP-0002) as Monthly employee (Monthly Basic LKR 75,000.00)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:02:15', '2026-10-27 06:02:15'),
(131, 2, 'create_employee', 'Payroll', 'Registered new employee Saman Kumara (EMP-0003) as Daily employee (Daily Wage LKR 3,000.00)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:02:30', '2026-10-27 06:02:30'),
(132, 2, 'update_employee', 'Payroll', 'Updated employee details for Kamal Ranjith (EMP-0001)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:02:44', '2026-10-27 06:02:44'),
(133, 2, 'login', 'Authentication', 'User Admin User (admin) logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:13:28', '2026-10-27 06:13:28'),
(134, 2, 'login', 'Authentication', 'User Admin User (admin) logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:13:29', '2026-10-27 06:13:29'),
(135, 2, 'record_attendance', 'Payroll', 'Recorded daily attendance for 2026-10-27 (3 employees updated)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:21:10', '2026-10-27 06:21:10'),
(136, 2, 'generate_payroll', 'Payroll', 'Generated draft salary report for October 2026', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:21:30', '2026-10-27 06:21:30'),
(137, 2, 'created', 'Vehicle Types', 'Added new vehicle type: SUV (Carpets: Disabled).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:28:12', '2026-10-27 06:28:12'),
(138, 2, 'created', 'Vehicle Types', 'Added new vehicle type: Van (Carpets: Disabled).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:28:17', '2026-10-27 06:28:17'),
(139, 2, 'created', 'Customers', 'Registered new customer \'Ruwan Silva\' (0771122334).', NULL, NULL, '2026-10-27 06:32:22', '2026-10-27 06:32:22'),
(140, 2, 'created', 'Customers', 'Registered new customer \'Dinesh Perera\' (0772233445).', NULL, NULL, '2026-10-27 06:32:28', '2026-10-27 06:32:28'),
(141, 2, 'created', 'Customers', 'Registered new customer \'Chamara Wijesinghe\' (0773344556).', NULL, NULL, '2026-10-27 06:32:33', '2026-10-27 06:32:33'),
(142, 2, 'created', 'Customers', 'Registered new customer \'Kasun Jayawardena\' (0774455667).', NULL, NULL, '2026-10-27 06:32:39', '2026-10-27 06:32:39'),
(143, 2, 'created', 'Customers', 'Registered new customer \'Sunil Fernando\' (0775566778).', NULL, NULL, '2026-10-27 06:32:45', '2026-10-27 06:32:45'),
(144, 2, 'created', 'Suppliers', 'Registered new supplier: Colombo Motor Spares.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:44:47', '2026-10-27 06:44:47'),
(145, 2, 'created', 'Suppliers', 'Registered new supplier: Lanka Auto Components.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:44:53', '2026-10-27 06:44:53'),
(146, 2, 'created', 'Suppliers', 'Registered new supplier: Metro Parts Distribution.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:44:59', '2026-10-27 06:44:59'),
(147, 2, 'created', 'Products', 'Created product: Toyota Genuine Engine Oil Filter (SKU: FLT-TOY-001).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:45:05', '2026-10-27 06:45:05'),
(148, 2, 'created', 'Products', 'Created product: Toyota Engine Oil 0W-20 (SKU: OIL-TOY-0W20).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:45:10', '2026-10-27 06:45:10'),
(149, 2, 'created', 'Products', 'Created product: Toyota Genuine Air Filter (SKU: FLT-AIR-001).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:45:16', '2026-10-27 06:45:16'),
(150, 2, 'created', 'Products', 'Created product: Toyota Genuine Brake Pads (SKU: BRK-TOY-001).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:45:22', '2026-10-27 06:45:22'),
(151, 2, 'created', 'Products', 'Created product: Toyota Long Life Coolant (SKU: LUB-CLT-001).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:45:28', '2026-10-27 06:45:28'),
(152, 2, 'created', 'Products', 'Created product: Denso Spark Plug (SKU: IGN-SPK-001).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:45:34', '2026-10-27 06:45:34'),
(153, 2, 'created', 'Products', 'Created product: Bosch Wiper Blade (SKU: ACC-WPR-001).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:45:39', '2026-10-27 06:45:39'),
(154, 2, 'created', 'Products', 'Created product: NGK Spark Plug (SKU: IGN-SPK-002).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:45:45', '2026-10-27 06:45:45'),
(155, 2, 'created', 'Purchasing (GRN)', 'Drafted GRN #GRN-202610-0001 totaling LKR 40000.00.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:45:51', '2026-10-27 06:45:51'),
(156, 2, 'created', 'Services', 'Added new service: Engine Oil Change (SRV-001).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:45:58', '2026-10-27 06:45:58'),
(157, 2, 'created', 'Services', 'Added new service: Oil Filter Replacement (SRV-002).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:46:04', '2026-10-27 06:46:04'),
(158, 2, 'created', 'Services', 'Added new service: Brake Pad Replacement (SRV-003).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:46:10', '2026-10-27 06:46:10'),
(159, 2, 'created', 'Services', 'Added new service: Engine Diagnostic Scan (SRV-004).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:46:15', '2026-10-27 06:46:15'),
(160, 2, 'created', 'Services', 'Added new service: Air Conditioning Service (SRV-005).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:46:21', '2026-10-27 06:46:21'),
(161, 2, 'created', 'Services', 'Added new service: Wheel Alignment (SRV-006).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:46:27', '2026-10-27 06:46:27'),
(162, 2, 'created', 'Services', 'Added new service: Suspension Inspection (SRV-007).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:46:32', '2026-10-27 06:46:32'),
(163, 2, 'create_shop_expense', 'Expenses', 'Recorded shop expense EXP-0001 for Electricity (LKR 18,500.00, Status: Paid)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:46:50', '2026-10-27 06:46:50'),
(164, 2, 'create_shop_expense', 'Expenses', 'Recorded shop expense EXP-0002 for Maintenance (LKR 12,000.00, Status: Paid)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:46:54', '2026-10-27 06:46:54'),
(165, 2, 'create_shop_expense', 'Expenses', 'Recorded shop expense EXP-0003 for Chemicals (LKR 6,500.00, Status: Paid)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:46:58', '2026-10-27 06:46:58'),
(166, 2, 'create_shop_expense', 'Expenses', 'Recorded shop expense EXP-0004 for Other (LKR 4,500.00, Status: Paid)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:47:03', '2026-10-27 06:47:03'),
(167, 2, 'record_attendance', 'Payroll', 'Recorded daily attendance for 2026-10-27 (3 employees updated)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:47:09', '2026-10-27 06:47:09'),
(168, 2, 'create_holiday', 'Payroll', 'Declared company holiday \'Deepavali Festival\' from 2026-10-31 to 2026-10-31 (Paid, Full day)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:47:15', '2026-10-27 06:47:15'),
(169, 2, 'register_advance', 'Payroll', 'Registered salary advance ADV-0001 of LKR 20,000.00 for Nimal Perera [Status: pending]', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:56:13', '2026-10-27 06:56:13'),
(170, 2, 'register_allowance', 'Payroll', 'Configured 1 allowance(s) [Type: meal, Amount: LKR 2,000.00]', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:56:34', '2026-10-27 06:56:34'),
(171, 2, 'register_bonus', 'Payroll', 'Registered 1 bonus(es) for period 2026-10 [Type: performance]', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 06:56:55', '2026-10-27 06:56:55'),
(172, 2, 'login', 'Authentication', 'User Admin User (admin) logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 17:06:58', '2026-10-27 17:06:58'),
(173, 2, 'created', 'Vehicle Types', 'Added new vehicle type: Car / Sedan (Carpets: Enabled).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 17:08:28', '2026-10-27 17:08:28'),
(174, 2, 'confirmed', 'Purchasing (GRN)', 'Created and confirmed GRN #GRN-202610-0002 totaling LKR 1132000.00.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 17:10:14', '2026-10-27 17:10:14'),
(175, 2, 'reassigned_technician', 'Job Cards', 'Reassigned technician for Job #JOB-202610-0001 from Unassigned to Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 17:17:19', '2026-10-27 17:17:19'),
(176, 2, 'reassigned_technician', 'Job Cards', 'Reassigned technician for Job #JOB-202610-0002 from Unassigned to Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 17:17:36', '2026-10-27 17:17:36'),
(177, 2, 'reassigned_technician', 'Job Cards', 'Reassigned technician for Job #JOB-202610-0003 from Unassigned to Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 17:17:46', '2026-10-27 17:17:46'),
(178, 2, 'price_updated', 'Job Cards', 'Updated price for service \'Engine Oil Change\' on Job #JOB-202610-0001 from LKR 0 to LKR 2500 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 17:24:58', '2026-10-27 17:24:58'),
(179, 2, 'price_updated', 'Job Cards', 'Updated price for service \'Oil Filter Replacement\' on Job #JOB-202610-0001 from LKR 0 to LKR 1500 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 17:25:02', '2026-10-27 17:25:02'),
(180, 2, 'completed_job', 'Job Cards', 'Completed Job #JOB-202610-0001 and generated bill #INV-202610-0001 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 17:25:05', '2026-10-27 17:25:05'),
(181, 2, 'price_updated', 'Job Cards', 'Updated price for service \'Engine Diagnostic Scan\' on Job #JOB-202610-0002 from LKR 0 to LKR 3500 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 17:25:15', '2026-10-27 17:25:15'),
(182, 2, 'price_updated', 'Job Cards', 'Updated price for service \'Suspension Inspection\' on Job #JOB-202610-0002 from LKR 0 to LKR 2000 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 17:25:19', '2026-10-27 17:25:19'),
(183, 2, 'price_updated', 'Job Cards', 'Updated price for service \'Brake Pad Replacement\' on Job #JOB-202610-0003 from LKR 0 to LKR 3000 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 17:25:26', '2026-10-27 17:25:26'),
(184, 2, 'payment_recorded', 'Sales & Billing', 'Recorded payment PAY-202610-0001 of LKR 10,000.00 for Invoice #INV-202610-0001. New balance: LKR 15,100.00.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 17:29:51', '2026-10-27 17:29:51'),
(185, 2, 'payment_recorded', 'Sales & Billing', 'Recorded payment PAY-202610-0002 of LKR 5,500.00 for Invoice #INV-202610-0007. New balance: LKR 0.00.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 17:30:00', '2026-10-27 17:30:00'),
(186, 2, 'direct_sale_created', 'Sales & Billing', 'Completed Direct Sale #DS-202610-0001 (Sales Bill #BILL-DS-202610-0001) for LKR 18,500.00', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 17:34:10', '2026-10-27 17:34:10'),
(187, 2, 'create_shop_expense', 'Expenses', 'Recorded shop expense EXP-0005 for Electricity (LKR 18,500.00, Status: Paid)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 17:34:20', '2026-10-27 17:34:20'),
(188, 2, 'login', 'Authentication', 'User Admin User (admin) logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-27 17:34:58', '2026-10-27 17:34:58'),
(189, 2, 'direct_sale_created', 'Sales & Billing', 'Completed Direct Sale #DS-202610-0002 (Sales Bill #BILL-DS-202610-0002) for LKR 3,800.00', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-27 17:35:14', '2026-10-27 17:35:14'),
(190, 2, 'login', 'Authentication', 'User Admin User (admin) logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-27 22:36:12', '2026-10-27 22:36:12'),
(191, 2, 'login', 'Authentication', 'User Admin User (admin) logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 22:47:29', '2026-10-27 22:47:29'),
(192, 2, 'price_updated', 'Job Cards', 'Updated price for service \'Engine Oil Change\' on Job #JOB-202610-0006 from LKR 0 to LKR 2500 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 22:54:05', '2026-10-27 22:54:05'),
(193, 2, 'price_updated', 'Job Cards', 'Updated price for service \'Oil Filter Replacement\' on Job #JOB-202610-0006 from LKR 0 to LKR 1500 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 22:54:10', '2026-10-27 22:54:10'),
(194, 2, 'reassigned_technician', 'Job Cards', 'Reassigned technician for Job #JOB-202610-0006 from Unassigned to Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 22:54:25', '2026-10-27 22:54:25'),
(195, 2, 'completed_job', 'Job Cards', 'Completed Job #JOB-202610-0006 and generated bill #INV-202610-0011 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 22:54:32', '2026-10-27 22:54:32'),
(196, 2, 'payment_recorded', 'Sales & Billing', 'Recorded payment PAY-202610-0005 of LKR 25,100.00 for Invoice #INV-202610-0011. New balance: LKR 0.00.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 22:55:57', '2026-10-27 22:55:57'),
(197, 2, 'delivered_vehicle', 'Job Cards', 'Vehicle WP CAK-4512 delivered for Job #JOB-202610-0006 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 22:56:06', '2026-10-27 22:56:06'),
(198, 2, 'completed_job', 'Job Cards', 'Completed Job #JOB-202610-0003 and generated bill #INV-202610-0012 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 23:22:57', '2026-10-27 23:22:57'),
(199, 2, 'payment_recorded', 'Sales & Billing', 'Recorded payment PAY-202610-0006 of LKR 5,000.00 for Invoice #INV-202610-0012. New balance: LKR 10,500.00.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 23:25:10', '2026-10-27 23:25:10'),
(200, 2, 'payment_recorded', 'Sales & Billing', 'Recorded payment PAY-202610-0007 of LKR 500.00 for Invoice #INV-202610-0012. New balance: LKR 10,000.00.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 23:25:27', '2026-10-27 23:25:27'),
(201, 2, 'price_updated', 'Job Cards', 'Updated price for service \'Engine Oil Change\' on Job #JOB-202610-0007 from LKR 0 to LKR 2500 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 23:34:25', '2026-10-27 23:34:25'),
(202, 2, 'reassigned_technician', 'Job Cards', 'Reassigned technician for Job #JOB-202610-0007 from Unassigned to Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 23:34:30', '2026-10-27 23:34:30'),
(203, 2, 'completed_job', 'Job Cards', 'Completed Job #JOB-202610-0007 and generated bill #INV-202610-0013 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 23:34:34', '2026-10-27 23:34:34'),
(204, 2, 'direct_sale_created', 'Sales & Billing', 'Completed Direct Sale #DS-202610-0003 (Sales Bill #BILL-DS-202610-0003) for LKR 2,600.00', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 23:35:20', '2026-10-27 23:35:20'),
(205, 2, 'reopened_job_for_editing', 'Job Cards', 'JOB-202610-0001 reopened for editing by Admin User. Previous invoice INV-202610-0001 voided and removed from active billing. LKR 10,000.00 preserved as job advance.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-27 23:36:03', '2026-10-27 23:36:03'),
(206, 2, 'payment_receipt_emailed', 'Sales & Billing', 'Emailed Payment Receipt PAY-202610-0006 to chamara.w@example.local.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 23:53:13', '2026-10-27 23:53:13'),
(207, 2, 'status_changed', 'Suppliers', 'Supplier Metro Parts Distribution was deactivated.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-27 23:57:35', '2026-10-27 23:57:35'),
(208, 2, 'status_changed', 'Suppliers', 'Supplier Metro Parts Distribution was activated.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-27 23:57:37', '2026-10-27 23:57:37'),
(209, 2, 'payment_recorded', 'Sales & Billing', 'Recorded payment PAY-202610-0009 of LKR 1,000.00 for Invoice #INV-202610-0012. New balance: LKR 9,000.00.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 23:58:16', '2026-10-27 23:58:16'),
(210, 2, 'payment_recorded', 'Sales & Billing', 'Recorded payment PAY-202610-0010 of LKR 5,100.00 for Invoice #INV-202610-0013. New balance: LKR 0.00.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-27 23:58:31', '2026-10-27 23:58:31'),
(211, 2, 'payment_recorded', 'Sales & Billing', 'Recorded payment PAY-202610-0011 of LKR 1,000.00 for Invoice #INV-202610-0012. New balance: LKR 8,000.00.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-27 23:59:11', '2026-10-27 23:59:11'),
(212, 2, 'payment_recorded', 'Sales & Billing', 'Recorded payment PAY-202610-0012 of LKR 500.00 for Invoice #INV-202610-0012. New balance: LKR 7,500.00.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-28 00:00:20', '2026-10-28 00:00:20'),
(213, 2, 'payment_recorded', 'Sales & Billing', 'Recorded payment PAY-202610-0013 of LKR 7,500.00 for Invoice #INV-202610-0012. New balance: LKR 0.00.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-28 00:01:36', '2026-10-28 00:01:36'),
(214, 2, 'payment_recorded', 'Sales & Billing', 'Recorded payment PAY-202610-0014 of LKR 800.00 for Invoice #INV-202610-0006. New balance: LKR 8,200.00.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 00:05:25', '2026-10-28 00:05:25'),
(215, 2, 'created_company', 'Company & Vehicles', 'Registered company customer \'Lanka Express Logistics s (Pvt) Ltd\' with credit limit LKR 200,000.00 and 30-day terms.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 00:23:08', '2026-10-28 00:23:08'),
(216, 2, 'registered_company_vehicle', 'Company & Vehicles', 'Registered vehicle ND-2343 under company \'Lanka Express Logistics s (Pvt) Ltd\'.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 00:24:04', '2026-10-28 00:24:04'),
(217, 2, 'price_updated', 'Job Cards', 'Updated price for service \'Engine Diagnostic Scan\' on Job #JOB-202610-0008 from LKR 0 to LKR 4500 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 00:24:44', '2026-10-28 00:24:44'),
(218, 2, 'completed_job', 'Job Cards', 'Completed Job #JOB-202610-0008 and generated bill #INV-202610-0014 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 00:24:55', '2026-10-28 00:24:55'),
(219, 2, 'record_attendance', 'Payroll', 'Recorded daily attendance for 2026-10-28 (3 employees updated)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 00:26:39', '2026-10-28 00:26:39'),
(220, 2, 'record_attendance', 'Payroll', 'Recorded daily attendance for 2026-10-28 (3 employees updated)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 00:26:39', '2026-10-28 00:26:39'),
(221, 2, 'price_updated', 'Job Cards', 'Updated price for service \'Oil Filter Replacement\' on Job #JOB-202610-0009 from LKR 0 to LKR 34444 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 00:29:45', '2026-10-28 00:29:45'),
(222, 2, 'completed_job', 'Job Cards', 'Completed Job #JOB-202610-0009 and generated bill #INV-202610-0015 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 00:29:48', '2026-10-28 00:29:48'),
(223, 2, 'price_updated', 'Job Cards', 'Updated price for service \'Suspension Inspection\' on Job #JOB-202610-0010 from LKR 0 to LKR 34000 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 00:30:43', '2026-10-28 00:30:43'),
(224, 2, 'payment_recorded', 'Sales & Billing', 'Recorded payment PAY-202610-0015 of LKR 4,500.00 for Invoice #INV-202610-0014. New balance: LKR 0.00.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 00:31:06', '2026-10-28 00:31:06'),
(225, 2, 'invoice_emailed', 'Sales & Invoices', 'Emailed Invoice #INV-202610-0014 to Sashika@gmail.com.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 00:31:14', '2026-10-28 00:31:14'),
(226, 2, 'created_company', 'Company & Vehicles', 'Registered company customer \'RAV Transport Ltd\' with credit limit LKR 0.00 and 30-day terms.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 00:32:27', '2026-10-28 00:32:27'),
(227, 2, 'updated_company', 'Company & Vehicles', 'Updated company profile \'RAV Transport Ltd\'.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 00:32:37', '2026-10-28 00:32:37'),
(228, 2, 'registered_company_vehicle', 'Company & Vehicles', 'Registered vehicle ND-23434 under company \'RAV Transport Ltd\'.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 00:33:08', '2026-10-28 00:33:08'),
(229, 2, 'price_updated', 'Job Cards', 'Updated price for service \'Engine Oil Change\' on Job #JOB-202610-0011 from LKR 0 to LKR 4560 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 00:33:25', '2026-10-28 00:33:25'),
(230, 2, 'price_updated', 'Job Cards', 'Updated price for service \'Air Conditioning Service\' on Job #JOB-202610-0011 from LKR 0 to LKR 23400 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 00:33:31', '2026-10-28 00:33:31'),
(231, 2, 'completed_job', 'Job Cards', 'Completed Job #JOB-202610-0011 and generated bill #INV-202610-0016 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 00:33:39', '2026-10-28 00:33:39'),
(232, 2, 'created', 'Suppliers', 'Registered new supplier: TVS Parts Distribution.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 00:34:30', '2026-10-28 00:34:30'),
(233, 2, 'created', 'Products', 'Created product: TVS Light (SKU: JS1025).', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 00:35:24', '2026-10-28 00:35:24'),
(234, 2, 'confirmed', 'Purchasing (GRN)', 'Created and confirmed GRN #GRN-202610-0003 totaling LKR 85000.00.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 00:37:52', '2026-10-28 00:37:52'),
(235, 2, 'payment_recorded', 'Purchasing (GRN)', 'Recorded supplier payment SPAY-202610-0001 of LKR 85,000.00 for GRN #GRN-202610-0003 (TVS Parts Distribution) via Cash.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 00:38:02', '2026-10-28 00:38:02'),
(236, 2, 'created_company', 'Company & Vehicles', 'Registered company customer \'Lanka Express Logistics (Pvt) Ltd\' with credit limit LKR 500,000.00 and 30-day terms.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-28 00:40:35', '2026-10-28 00:40:35'),
(237, 2, 'registered_company_vehicle', 'Company & Vehicles', 'Registered vehicle CP HIL-2020 under company \'Lanka Express Logistics (Pvt) Ltd\'.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-28 00:42:12', '2026-10-28 00:42:12'),
(238, 2, 'price_updated', 'Job Cards', 'Updated price for service \'Engine Oil Change\' on Job #JOB-202610-0012 from LKR 0 to LKR 2500 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-28 00:44:11', '2026-10-28 00:44:11'),
(239, 2, 'price_updated', 'Job Cards', 'Updated price for service \'Engine Diagnostic Scan\' on Job #JOB-202610-0012 from LKR 0 to LKR 3500 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-28 00:44:14', '2026-10-28 00:44:14'),
(240, 2, 'price_updated', 'Job Cards', 'Updated price for service \'Suspension Inspection\' on Job #JOB-202610-0012 from LKR 0 to LKR 1500 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-28 00:44:18', '2026-10-28 00:44:18'),
(241, 2, 'reassigned_technician', 'Job Cards', 'Reassigned technician for Job #JOB-202610-0012 from Unassigned to Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-28 00:44:24', '2026-10-28 00:44:24'),
(242, 2, 'completed_job', 'Job Cards', 'Completed Job #JOB-202610-0012 and generated bill #INV-202610-0018 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-28 00:44:30', '2026-10-28 00:44:30'),
(243, 2, 'payment_recorded', 'Sales & Billing', 'Recorded payment PAY-202610-0016 of LKR 7,500.00 for Invoice #INV-202610-0018. New balance: LKR 0.00.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-28 00:44:41', '2026-10-28 00:44:41'),
(244, 2, 'delivered_vehicle', 'Job Cards', 'Vehicle CP HIL-2020 delivered for Job #JOB-202610-0012 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-10-28 00:44:48', '2026-10-28 00:44:48'),
(245, 2, 'payment_recorded', 'Sales & Billing', 'Recorded payment PAY-202610-0017 of LKR 2,300.00 for Invoice #INV-202610-0061. New balance: LKR 0.00.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 02:23:35', '2026-10-28 02:23:35'),
(246, 2, 'completed_job', 'Job Cards', 'Completed Job #JOB-202610-0013 and generated bill #INV-202610-0063 by Admin User.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 02:28:45', '2026-10-28 02:28:45'),
(247, 2, 'payment_recorded', 'Sales & Billing', 'Recorded payment PAY-202610-0018 of LKR 2,600.00 for Invoice #INV-202610-0063. New balance: LKR 0.00.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', '2026-10-28 02:28:51', '2026-10-28 02:28:51');

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
(3, 'APT-20261027-0001', 1, 42, 56, 10, NULL, 'Sunil Fernando', '0775556677', 'WP CBA-9045', NULL, '2026-10-28', '10:00 AM', 'scheduled', 'Scheduled 60,000km periodic maintenance and filter check', NULL, 2, '2026-10-27 17:33:59', '2026-10-27 17:33:59');

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
(5, 3, 2, '2026-10-20 04:40:40', '2026-10-20 04:40:40');

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
('viran-auto-care-cache-admin@1234.com|127.0.0.1', 'i:1;', 1793103990),
('viran-auto-care-cache-admin@1234.com|127.0.0.1:timer', 'i:1793103990;', 1793103990);
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('viran-auto-care-cache-roster:project:v3:3e647ebb076a5572eca63a027314cb39', 'O:26:\"Laravel\\Roster\\ProjectScan\":8:{s:8:\"basePath\";s:29:\"D:\\Intern\\08\\autocare-system\\\";s:3:\"php\";O:35:\"Laravel\\Roster\\Ecosystems\\Ecosystem\":2:{s:9:\"\0*\0byName\";a:121:{s:23:\"barryvdh/laravel-dompdf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"barryvdh/laravel-dompdf\";s:10:\"\0*\0version\";s:5:\"3.1.2\";s:9:\"\0*\0source\";E:43:\"Laravel\\Roster\\Enums\\PackageSource:Composer\";s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.1\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\vendor\\barryvdh\\laravel-dompdf\";}s:10:\"brick/math\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"brick/math\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\Intern\\08\\autocare-system\\vendor\\brick\\math\";}s:31:\"carbonphp/carbon-doctrine-types\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"carbonphp/carbon-doctrine-types\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"D:\\Intern\\08\\autocare-system\\vendor\\carbonphp\\carbon-doctrine-types\";}s:23:\"dflydev/dot-access-data\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"dflydev/dot-access-data\";s:10:\"\0*\0version\";s:5:\"3.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\vendor\\dflydev\\dot-access-data\";}s:18:\"doctrine/inflector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"doctrine/inflector\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\vendor\\doctrine\\inflector\";}s:14:\"doctrine/lexer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"doctrine/lexer\";s:10:\"\0*\0version\";s:5:\"3.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\doctrine\\lexer\";}s:13:\"dompdf/dompdf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"dompdf/dompdf\";s:10:\"\0*\0version\";s:5:\"3.1.6\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\vendor\\dompdf\\dompdf\";}s:19:\"dompdf/php-font-lib\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"dompdf/php-font-lib\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\vendor\\dompdf\\php-font-lib\";}s:18:\"dompdf/php-svg-lib\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"dompdf/php-svg-lib\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\vendor\\dompdf\\php-svg-lib\";}s:29:\"dragonmantank/cron-expression\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"dragonmantank/cron-expression\";s:10:\"\0*\0version\";s:5:\"3.6.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\vendor\\dragonmantank\\cron-expression\";}s:23:\"egulias/email-validator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"egulias/email-validator\";s:10:\"\0*\0version\";s:5:\"4.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\vendor\\egulias\\email-validator\";}s:18:\"fruitcake/php-cors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"fruitcake/php-cors\";s:10:\"\0*\0version\";s:5:\"1.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\vendor\\fruitcake\\php-cors\";}s:27:\"graham-campbell/result-type\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"graham-campbell/result-type\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"D:\\Intern\\08\\autocare-system\\vendor\\graham-campbell\\result-type\";}s:17:\"guzzlehttp/guzzle\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"guzzlehttp/guzzle\";s:10:\"\0*\0version\";s:5:\"8.3.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\guzzlehttp\\guzzle\";}s:19:\"guzzlehttp/promises\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"guzzlehttp/promises\";s:10:\"\0*\0version\";s:5:\"3.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\vendor\\guzzlehttp\\promises\";}s:15:\"guzzlehttp/psr7\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"guzzlehttp/psr7\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\guzzlehttp\\psr7\";}s:23:\"guzzlehttp/uri-template\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"guzzlehttp/uri-template\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\vendor\\guzzlehttp\\uri-template\";}s:17:\"laravel/framework\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"laravel/framework\";s:10:\"\0*\0version\";s:7:\"13.37.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^13.17\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\framework\";}s:15:\"laravel/prompts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"laravel/prompts\";s:10:\"\0*\0version\";s:6:\"0.3.26\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\prompts\";}s:28:\"laravel/serializable-closure\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"laravel/serializable-closure\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\serializable-closure\";}s:14:\"laravel/tinker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/tinker\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.0\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\tinker\";}s:17:\"league/commonmark\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"league/commonmark\";s:10:\"\0*\0version\";s:6:\"2.10.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\league\\commonmark\";}s:13:\"league/config\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"league/config\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\vendor\\league\\config\";}s:16:\"league/flysystem\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"league/flysystem\";s:10:\"\0*\0version\";s:6:\"3.36.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\vendor\\league\\flysystem\";}s:22:\"league/flysystem-local\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"league/flysystem-local\";s:10:\"\0*\0version\";s:6:\"3.35.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\league\\flysystem-local\";}s:26:\"league/mime-type-detection\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"league/mime-type-detection\";s:10:\"\0*\0version\";s:6:\"1.17.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"D:\\Intern\\08\\autocare-system\\vendor\\league\\mime-type-detection\";}s:10:\"league/uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"league/uri\";s:10:\"\0*\0version\";s:5:\"7.9.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\Intern\\08\\autocare-system\\vendor\\league\\uri\";}s:21:\"league/uri-interfaces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"league/uri-interfaces\";s:10:\"\0*\0version\";s:5:\"7.9.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\vendor\\league\\uri-interfaces\";}s:17:\"masterminds/html5\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"masterminds/html5\";s:10:\"\0*\0version\";s:6:\"2.11.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\masterminds\\html5\";}s:15:\"monolog/monolog\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"monolog/monolog\";s:10:\"\0*\0version\";s:6:\"3.12.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\monolog\\monolog\";}s:13:\"nesbot/carbon\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"nesbot/carbon\";s:10:\"\0*\0version\";s:6:\"3.15.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\vendor\\nesbot\\carbon\";}s:12:\"nette/schema\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"nette/schema\";s:10:\"\0*\0version\";s:5:\"1.3.6\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\Intern\\08\\autocare-system\\vendor\\nette\\schema\";}s:11:\"nette/utils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"nette/utils\";s:10:\"\0*\0version\";s:5:\"4.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\vendor\\nette\\utils\";}s:16:\"nikic/php-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"nikic/php-parser\";s:10:\"\0*\0version\";s:5:\"5.9.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\vendor\\nikic\\php-parser\";}s:19:\"nunomaduro/termwind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"nunomaduro/termwind\";s:10:\"\0*\0version\";s:5:\"2.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\vendor\\nunomaduro\\termwind\";}s:19:\"phpoption/phpoption\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"phpoption/phpoption\";s:10:\"\0*\0version\";s:6:\"1.10.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\vendor\\phpoption\\phpoption\";}s:9:\"psr/clock\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"psr/clock\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\clock\";}s:13:\"psr/container\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"psr/container\";s:10:\"\0*\0version\";s:5:\"2.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\container\";}s:20:\"psr/event-dispatcher\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"psr/event-dispatcher\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\event-dispatcher\";}s:15:\"psr/http-client\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"psr/http-client\";s:10:\"\0*\0version\";s:5:\"1.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\http-client\";}s:16:\"psr/http-factory\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/http-factory\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\http-factory\";}s:16:\"psr/http-message\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/http-message\";s:10:\"\0*\0version\";s:3:\"2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\http-message\";}s:7:\"psr/log\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"psr/log\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\log\";}s:16:\"psr/simple-cache\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/simple-cache\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\simple-cache\";}s:9:\"psy/psysh\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"psy/psysh\";s:10:\"\0*\0version\";s:7:\"0.12.24\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\Intern\\08\\autocare-system\\vendor\\psy\\psysh\";}s:17:\"ramsey/collection\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"ramsey/collection\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\ramsey\\collection\";}s:11:\"ramsey/uuid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"ramsey/uuid\";s:10:\"\0*\0version\";s:5:\"4.9.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\vendor\\ramsey\\uuid\";}s:25:\"sabberworm/php-css-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"sabberworm/php-css-parser\";s:10:\"\0*\0version\";s:5:\"9.5.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"D:\\Intern\\08\\autocare-system\\vendor\\sabberworm\\php-css-parser\";}s:13:\"symfony/clock\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"symfony/clock\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\clock\";}s:15:\"symfony/console\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/console\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\console\";}s:20:\"symfony/css-selector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"symfony/css-selector\";s:10:\"\0*\0version\";s:5:\"8.1.6\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\css-selector\";}s:29:\"symfony/deprecation-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"symfony/deprecation-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\deprecation-contracts\";}s:21:\"symfony/error-handler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/error-handler\";s:10:\"\0*\0version\";s:5:\"8.1.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\error-handler\";}s:24:\"symfony/event-dispatcher\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"symfony/event-dispatcher\";s:10:\"\0*\0version\";s:5:\"8.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\event-dispatcher\";}s:34:\"symfony/event-dispatcher-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"symfony/event-dispatcher-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\event-dispatcher-contracts\";}s:14:\"symfony/finder\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/finder\";s:10:\"\0*\0version\";s:5:\"8.1.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\finder\";}s:23:\"symfony/http-foundation\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"symfony/http-foundation\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\http-foundation\";}s:19:\"symfony/http-kernel\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"symfony/http-kernel\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\http-kernel\";}s:14:\"symfony/mailer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/mailer\";s:10:\"\0*\0version\";s:5:\"8.1.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\mailer\";}s:12:\"symfony/mime\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"symfony/mime\";s:10:\"\0*\0version\";s:5:\"8.1.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\mime\";}s:22:\"symfony/polyfill-ctype\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-ctype\";s:10:\"\0*\0version\";s:6:\"1.37.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-ctype\";}s:30:\"symfony/polyfill-intl-grapheme\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"symfony/polyfill-intl-grapheme\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-intl-grapheme\";}s:25:\"symfony/polyfill-intl-idn\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/polyfill-intl-idn\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-intl-idn\";}s:32:\"symfony/polyfill-intl-normalizer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"symfony/polyfill-intl-normalizer\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-intl-normalizer\";}s:25:\"symfony/polyfill-mbstring\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/polyfill-mbstring\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-mbstring\";}s:22:\"symfony/polyfill-php80\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php80\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-php80\";}s:22:\"symfony/polyfill-php82\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php82\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-php82\";}s:22:\"symfony/polyfill-php84\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php84\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-php84\";}s:22:\"symfony/polyfill-php85\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php85\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-php85\";}s:22:\"symfony/polyfill-php86\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php86\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-php86\";}s:21:\"symfony/polyfill-time\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/polyfill-time\";s:10:\"\0*\0version\";s:6:\"1.42.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-time\";}s:21:\"symfony/polyfill-uuid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/polyfill-uuid\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-uuid\";}s:15:\"symfony/process\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/process\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\process\";}s:15:\"symfony/routing\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/routing\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\routing\";}s:25:\"symfony/service-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/service-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\service-contracts\";}s:14:\"symfony/string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/string\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\string\";}s:19:\"symfony/translation\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"symfony/translation\";s:10:\"\0*\0version\";s:5:\"8.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\translation\";}s:29:\"symfony/translation-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"symfony/translation-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\translation-contracts\";}s:11:\"symfony/uid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"symfony/uid\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\uid\";}s:18:\"symfony/var-dumper\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"symfony/var-dumper\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\var-dumper\";}s:33:\"tijsverkoyen/css-to-inline-styles\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"tijsverkoyen/css-to-inline-styles\";s:10:\"\0*\0version\";s:5:\"2.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"D:\\Intern\\08\\autocare-system\\vendor\\tijsverkoyen\\css-to-inline-styles\";}s:16:\"vlucas/phpdotenv\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"vlucas/phpdotenv\";s:10:\"\0*\0version\";s:5:\"5.7.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\vendor\\vlucas\\phpdotenv\";}s:19:\"voku/portable-ascii\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"voku/portable-ascii\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\vendor\\voku\\portable-ascii\";}s:15:\"composer/semver\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"composer/semver\";s:10:\"\0*\0version\";s:5:\"3.5.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\composer\\semver\";}s:14:\"fakerphp/faker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"fakerphp/faker\";s:10:\"\0*\0version\";s:6:\"1.24.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.23\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\fakerphp\\faker\";}s:11:\"filp/whoops\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"filp/whoops\";s:10:\"\0*\0version\";s:6:\"2.18.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\vendor\\filp\\whoops\";}s:21:\"hamcrest/hamcrest-php\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"hamcrest/hamcrest-php\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\vendor\\hamcrest\\hamcrest-php\";}s:22:\"laravel/agent-detector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"laravel/agent-detector\";s:10:\"\0*\0version\";s:5:\"2.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\agent-detector\";}s:13:\"laravel/boost\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"laravel/boost\";s:10:\"\0*\0version\";s:6:\"2.10.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^2.10\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\boost\";}s:11:\"laravel/mcp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"laravel/mcp\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\mcp\";}s:12:\"laravel/pail\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"laravel/pail\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^1.2.5\";s:7:\"\0*\0path\";s:48:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\pail\";}s:11:\"laravel/pao\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"laravel/pao\";s:10:\"\0*\0version\";s:5:\"1.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^1.0.6\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\pao\";}s:12:\"laravel/pint\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"laravel/pint\";s:10:\"\0*\0version\";s:6:\"1.32.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.27\";s:7:\"\0*\0path\";s:48:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\pint\";}s:14:\"laravel/roster\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/roster\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\roster\";}s:15:\"mockery/mockery\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"mockery/mockery\";s:10:\"\0*\0version\";s:6:\"1.6.15\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^1.6\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\mockery\\mockery\";}s:17:\"myclabs/deep-copy\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"myclabs/deep-copy\";s:10:\"\0*\0version\";s:6:\"1.14.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\myclabs\\deep-copy\";}s:20:\"nunomaduro/collision\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"nunomaduro/collision\";s:10:\"\0*\0version\";s:5:\"8.9.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^8.6\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\vendor\\nunomaduro\\collision\";}s:16:\"phar-io/manifest\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"phar-io/manifest\";s:10:\"\0*\0version\";s:5:\"2.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\vendor\\phar-io\\manifest\";}s:15:\"phar-io/version\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"phar-io/version\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\phar-io\\version\";}s:25:\"phpunit/php-code-coverage\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-code-coverage\";s:10:\"\0*\0version\";s:6:\"12.5.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"D:\\Intern\\08\\autocare-system\\vendor\\phpunit\\php-code-coverage\";}s:25:\"phpunit/php-file-iterator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-file-iterator\";s:10:\"\0*\0version\";s:5:\"6.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"D:\\Intern\\08\\autocare-system\\vendor\\phpunit\\php-file-iterator\";}s:19:\"phpunit/php-invoker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"phpunit/php-invoker\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\vendor\\phpunit\\php-invoker\";}s:25:\"phpunit/php-text-template\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-text-template\";s:10:\"\0*\0version\";s:5:\"5.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"D:\\Intern\\08\\autocare-system\\vendor\\phpunit\\php-text-template\";}s:17:\"phpunit/php-timer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"phpunit/php-timer\";s:10:\"\0*\0version\";s:5:\"8.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\phpunit\\php-timer\";}s:15:\"phpunit/phpunit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"phpunit/phpunit\";s:10:\"\0*\0version\";s:7:\"12.5.40\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:8:\"^12.5.12\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\phpunit\\phpunit\";}s:20:\"sebastian/cli-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/cli-parser\";s:10:\"\0*\0version\";s:5:\"4.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\cli-parser\";}s:20:\"sebastian/comparator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/comparator\";s:10:\"\0*\0version\";s:5:\"7.1.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\comparator\";}s:20:\"sebastian/complexity\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/complexity\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\complexity\";}s:14:\"sebastian/diff\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"sebastian/diff\";s:10:\"\0*\0version\";s:5:\"7.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\diff\";}s:21:\"sebastian/environment\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"sebastian/environment\";s:10:\"\0*\0version\";s:5:\"8.1.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\environment\";}s:18:\"sebastian/exporter\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"sebastian/exporter\";s:10:\"\0*\0version\";s:5:\"7.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\exporter\";}s:22:\"sebastian/global-state\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"sebastian/global-state\";s:10:\"\0*\0version\";s:5:\"8.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\global-state\";}s:23:\"sebastian/lines-of-code\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"sebastian/lines-of-code\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\lines-of-code\";}s:27:\"sebastian/object-enumerator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"sebastian/object-enumerator\";s:10:\"\0*\0version\";s:5:\"7.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\object-enumerator\";}s:26:\"sebastian/object-reflector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"sebastian/object-reflector\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\object-reflector\";}s:27:\"sebastian/recursion-context\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"sebastian/recursion-context\";s:10:\"\0*\0version\";s:5:\"7.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\recursion-context\";}s:14:\"sebastian/type\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"sebastian/type\";s:10:\"\0*\0version\";s:5:\"6.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\type\";}s:17:\"sebastian/version\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"sebastian/version\";s:10:\"\0*\0version\";s:5:\"6.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\version\";}s:28:\"staabm/side-effects-detector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"staabm/side-effects-detector\";s:10:\"\0*\0version\";s:5:\"1.0.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"D:\\Intern\\08\\autocare-system\\vendor\\staabm\\side-effects-detector\";}s:12:\"symfony/yaml\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"symfony/yaml\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\yaml\";}s:17:\"theseer/tokenizer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"theseer/tokenizer\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\theseer\\tokenizer\";}}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:121:{i:0;r:5;i:1;r:13;i:2;r:21;i:3;r:29;i:4;r:37;i:5;r:45;i:6;r:53;i:7;r:61;i:8;r:69;i:9;r:77;i:10;r:85;i:11;r:93;i:12;r:101;i:13;r:109;i:14;r:117;i:15;r:125;i:16;r:133;i:17;r:141;i:18;r:149;i:19;r:157;i:20;r:165;i:21;r:173;i:22;r:181;i:23;r:189;i:24;r:197;i:25;r:205;i:26;r:213;i:27;r:221;i:28;r:229;i:29;r:237;i:30;r:245;i:31;r:253;i:32;r:261;i:33;r:269;i:34;r:277;i:35;r:285;i:36;r:293;i:37;r:301;i:38;r:309;i:39;r:317;i:40;r:325;i:41;r:333;i:42;r:341;i:43;r:349;i:44;r:357;i:45;r:365;i:46;r:373;i:47;r:381;i:48;r:389;i:49;r:397;i:50;r:405;i:51;r:413;i:52;r:421;i:53;r:429;i:54;r:437;i:55;r:445;i:56;r:453;i:57;r:461;i:58;r:469;i:59;r:477;i:60;r:485;i:61;r:493;i:62;r:501;i:63;r:509;i:64;r:517;i:65;r:525;i:66;r:533;i:67;r:541;i:68;r:549;i:69;r:557;i:70;r:565;i:71;r:573;i:72;r:581;i:73;r:589;i:74;r:597;i:75;r:605;i:76;r:613;i:77;r:621;i:78;r:629;i:79;r:637;i:80;r:645;i:81;r:653;i:82;r:661;i:83;r:669;i:84;r:677;i:85;r:685;i:86;r:693;i:87;r:701;i:88;r:709;i:89;r:717;i:90;r:725;i:91;r:733;i:92;r:741;i:93;r:749;i:94;r:757;i:95;r:765;i:96;r:773;i:97;r:781;i:98;r:789;i:99;r:797;i:100;r:805;i:101;r:813;i:102;r:821;i:103;r:829;i:104;r:837;i:105;r:845;i:106;r:853;i:107;r:861;i:108;r:869;i:109;r:877;i:110;r:885;i:111;r:893;i:112;r:901;i:113;r:909;i:114;r:917;i:115;r:925;i:116;r:933;i:117;r:941;i:118;r:949;i:119;r:957;i:120;r:965;}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:2:\"js\";O:37:\"Laravel\\Roster\\Ecosystems\\JsEcosystem\":3:{s:9:\"\0*\0byName\";a:125:{s:24:\"@alcalzone/ansi-tokenize\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"@alcalzone/ansi-tokenize\";s:10:\"\0*\0version\";s:5:\"0.3.1\";s:9:\"\0*\0source\";E:38:\"Laravel\\Roster\\Enums\\PackageSource:Npm\";s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"D:\\Intern\\08\\autocare-system\\node_modules\\@alcalzone\\ansi-tokenize\";}s:18:\"@laravel/multiplex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@laravel/multiplex\";s:10:\"\0*\0version\";s:5:\"0.4.5\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^0.4.1\";s:7:\"\0*\0path\";s:60:\"D:\\Intern\\08\\autocare-system\\node_modules\\@laravel\\multiplex\";}s:15:\"@vue/reactivity\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"@vue/reactivity\";s:10:\"\0*\0version\";s:6:\"3.5.44\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\node_modules\\@vue\\reactivity\";}s:11:\"@vue/shared\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"@vue/shared\";s:10:\"\0*\0version\";s:6:\"3.5.44\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\@vue\\shared\";}s:8:\"alpinejs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"alpinejs\";s:10:\"\0*\0version\";s:6:\"3.17.4\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^3.17.4\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\node_modules\\alpinejs\";}s:12:\"ansi-escapes\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"ansi-escapes\";s:10:\"\0*\0version\";s:5:\"7.3.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\ansi-escapes\";}s:10:\"ansi-regex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"ansi-regex\";s:10:\"\0*\0version\";s:5:\"6.4.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\ansi-regex\";}s:11:\"ansi-styles\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"ansi-styles\";s:10:\"\0*\0version\";s:5:\"7.0.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\ansi-styles\";}s:9:\"auto-bind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"auto-bind\";s:10:\"\0*\0version\";s:5:\"5.0.1\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\auto-bind\";}s:5:\"chalk\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"chalk\";s:10:\"\0*\0version\";s:5:\"5.6.2\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\node_modules\\chalk\";}s:9:\"cli-boxes\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"cli-boxes\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\cli-boxes\";}s:10:\"cli-cursor\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"cli-cursor\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\cli-cursor\";}s:12:\"cli-truncate\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"cli-truncate\";s:10:\"\0*\0version\";s:5:\"6.1.1\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\cli-truncate\";}s:12:\"code-excerpt\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"code-excerpt\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\code-excerpt\";}s:9:\"commander\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"commander\";s:10:\"\0*\0version\";s:6:\"15.0.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\commander\";}s:17:\"convert-to-spaces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"convert-to-spaces\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\node_modules\\convert-to-spaces\";}s:11:\"environment\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"environment\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\environment\";}s:10:\"es-toolkit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"es-toolkit\";s:10:\"\0*\0version\";s:6:\"1.52.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\es-toolkit\";}s:20:\"escape-string-regexp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"escape-string-regexp\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"D:\\Intern\\08\\autocare-system\\node_modules\\escape-string-regexp\";}s:20:\"get-east-asian-width\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"get-east-asian-width\";s:10:\"\0*\0version\";s:5:\"1.7.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"D:\\Intern\\08\\autocare-system\\node_modules\\get-east-asian-width\";}s:13:\"indent-string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"indent-string\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\node_modules\\indent-string\";}s:3:\"ink\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:3:\"ink\";s:10:\"\0*\0version\";s:5:\"7.1.1\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\Intern\\08\\autocare-system\\node_modules\\ink\";}s:23:\"is-fullwidth-code-point\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"is-fullwidth-code-point\";s:10:\"\0*\0version\";s:5:\"5.1.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\node_modules\\is-fullwidth-code-point\";}s:8:\"is-in-ci\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"is-in-ci\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\node_modules\\is-in-ci\";}s:8:\"mimic-fn\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"mimic-fn\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\node_modules\\mimic-fn\";}s:7:\"onetime\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"onetime\";s:10:\"\0*\0version\";s:5:\"5.1.2\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\node_modules\\onetime\";}s:13:\"patch-console\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"patch-console\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\node_modules\\patch-console\";}s:7:\"qz-tray\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"qz-tray\";s:10:\"\0*\0version\";s:5:\"2.3.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^2.3.0\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\node_modules\\qz-tray\";}s:5:\"react\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"react\";s:10:\"\0*\0version\";s:6:\"19.3.1\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\node_modules\\react\";}s:16:\"react-reconciler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"react-reconciler\";s:10:\"\0*\0version\";s:6:\"0.33.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\node_modules\\react-reconciler\";}s:14:\"restore-cursor\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"restore-cursor\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\node_modules\\restore-cursor\";}s:9:\"scheduler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"scheduler\";s:10:\"\0*\0version\";s:6:\"0.27.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\scheduler\";}s:11:\"signal-exit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"signal-exit\";s:10:\"\0*\0version\";s:5:\"3.0.7\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\signal-exit\";}s:10:\"slice-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"slice-ansi\";s:10:\"\0*\0version\";s:5:\"9.0.1\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\slice-ansi\";}s:11:\"stack-utils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"stack-utils\";s:10:\"\0*\0version\";s:5:\"2.0.6\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\stack-utils\";}s:12:\"string-width\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"string-width\";s:10:\"\0*\0version\";s:5:\"8.3.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\string-width\";}s:10:\"strip-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"strip-ansi\";s:10:\"\0*\0version\";s:5:\"7.2.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\strip-ansi\";}s:10:\"tagged-tag\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"tagged-tag\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\tagged-tag\";}s:13:\"terminal-size\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"terminal-size\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\node_modules\\terminal-size\";}s:9:\"type-fest\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"type-fest\";s:10:\"\0*\0version\";s:6:\"5.10.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\type-fest\";}s:11:\"widest-line\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"widest-line\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\widest-line\";}s:9:\"wrap-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"wrap-ansi\";s:10:\"\0*\0version\";s:6:\"10.0.2\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\wrap-ansi\";}s:2:\"ws\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:2:\"ws\";s:10:\"\0*\0version\";s:6:\"8.22.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"D:\\Intern\\08\\autocare-system\\node_modules\\ws\";}s:11:\"yoga-layout\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"yoga-layout\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\yoga-layout\";}s:23:\"@jridgewell/gen-mapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"@jridgewell/gen-mapping\";s:10:\"\0*\0version\";s:6:\"0.3.13\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\node_modules\\@jridgewell\\gen-mapping\";}s:21:\"@jridgewell/remapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"@jridgewell/remapping\";s:10:\"\0*\0version\";s:5:\"2.3.5\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"D:\\Intern\\08\\autocare-system\\node_modules\\@jridgewell\\remapping\";}s:23:\"@jridgewell/resolve-uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"@jridgewell/resolve-uri\";s:10:\"\0*\0version\";s:5:\"3.1.2\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\node_modules\\@jridgewell\\resolve-uri\";}s:27:\"@jridgewell/sourcemap-codec\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"@jridgewell/sourcemap-codec\";s:10:\"\0*\0version\";s:5:\"1.6.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"D:\\Intern\\08\\autocare-system\\node_modules\\@jridgewell\\sourcemap-codec\";}s:25:\"@jridgewell/trace-mapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"@jridgewell/trace-mapping\";s:10:\"\0*\0version\";s:6:\"0.3.31\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"D:\\Intern\\08\\autocare-system\\node_modules\\@jridgewell\\trace-mapping\";}s:18:\"@oxc-project/types\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@oxc-project/types\";s:10:\"\0*\0version\";s:7:\"0.154.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"D:\\Intern\\08\\autocare-system\\node_modules\\@oxc-project\\types\";}s:34:\"@rolldown/binding-android-arm-eabi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-android-arm-eabi\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-android-arm-eabi\";}s:31:\"@rolldown/binding-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@rolldown/binding-android-arm64\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-android-arm64\";}s:30:\"@rolldown/binding-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@rolldown/binding-darwin-arm64\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-darwin-arm64\";}s:28:\"@rolldown/binding-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"@rolldown/binding-darwin-x64\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-darwin-x64\";}s:29:\"@rolldown/binding-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"@rolldown/binding-freebsd-x64\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-freebsd-x64\";}s:37:\"@rolldown/binding-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:37:\"@rolldown/binding-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:79:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-linux-arm-gnueabihf\";}s:33:\"@rolldown/binding-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-arm64-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-linux-arm64-gnu\";}s:34:\"@rolldown/binding-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-linux-arm64-musl\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-linux-arm64-musl\";}s:33:\"@rolldown/binding-linux-ppc64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-ppc64-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-linux-ppc64-gnu\";}s:33:\"@rolldown/binding-linux-s390x-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-s390x-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-linux-s390x-gnu\";}s:31:\"@rolldown/binding-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@rolldown/binding-linux-x64-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-linux-x64-gnu\";}s:32:\"@rolldown/binding-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@rolldown/binding-linux-x64-musl\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-linux-x64-musl\";}s:35:\"@rolldown/binding-openharmony-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@rolldown/binding-openharmony-arm64\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:77:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-openharmony-arm64\";}s:34:\"@rolldown/binding-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-win32-arm64-msvc\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-win32-arm64-msvc\";}s:32:\"@rolldown/binding-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@rolldown/binding-win32-x64-msvc\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-win32-x64-msvc\";}s:21:\"@rolldown/pluginutils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"@rolldown/pluginutils\";s:10:\"\0*\0version\";s:5:\"1.0.1\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\pluginutils\";}s:17:\"@tailwindcss/node\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"@tailwindcss/node\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\node\";}s:18:\"@tailwindcss/oxide\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@tailwindcss/oxide\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide\";}s:32:\"@tailwindcss/oxide-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@tailwindcss/oxide-android-arm64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-android-arm64\";}s:31:\"@tailwindcss/oxide-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@tailwindcss/oxide-darwin-arm64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-darwin-arm64\";}s:29:\"@tailwindcss/oxide-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"@tailwindcss/oxide-darwin-x64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-darwin-x64\";}s:30:\"@tailwindcss/oxide-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@tailwindcss/oxide-freebsd-x64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-freebsd-x64\";}s:38:\"@tailwindcss/oxide-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:38:\"@tailwindcss/oxide-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:80:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-linux-arm-gnueabihf\";}s:34:\"@tailwindcss/oxide-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@tailwindcss/oxide-linux-arm64-gnu\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-linux-arm64-gnu\";}s:35:\"@tailwindcss/oxide-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@tailwindcss/oxide-linux-arm64-musl\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:77:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-linux-arm64-musl\";}s:32:\"@tailwindcss/oxide-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@tailwindcss/oxide-linux-x64-gnu\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-linux-x64-gnu\";}s:33:\"@tailwindcss/oxide-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@tailwindcss/oxide-linux-x64-musl\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-linux-x64-musl\";}s:30:\"@tailwindcss/oxide-wasm32-wasi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@tailwindcss/oxide-wasm32-wasi\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-wasm32-wasi\";}s:35:\"@tailwindcss/oxide-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@tailwindcss/oxide-win32-arm64-msvc\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:77:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-win32-arm64-msvc\";}s:33:\"@tailwindcss/oxide-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@tailwindcss/oxide-win32-x64-msvc\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-win32-x64-msvc\";}s:17:\"@tailwindcss/vite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"@tailwindcss/vite\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^4.0.0\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\vite\";}s:5:\"cliui\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"cliui\";s:10:\"\0*\0version\";s:5:\"9.0.1\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\node_modules\\cliui\";}s:12:\"concurrently\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"concurrently\";s:10:\"\0*\0version\";s:6:\"10.0.6\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^10.0.3\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\concurrently\";}s:11:\"detect-libc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"detect-libc\";s:10:\"\0*\0version\";s:5:\"2.1.2\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\detect-libc\";}s:11:\"emoji-regex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"emoji-regex\";s:10:\"\0*\0version\";s:6:\"10.6.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\emoji-regex\";}s:16:\"enhanced-resolve\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"enhanced-resolve\";s:10:\"\0*\0version\";s:6:\"5.26.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\node_modules\\enhanced-resolve\";}s:8:\"escalade\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"escalade\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\node_modules\\escalade\";}s:4:\"fdir\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"fdir\";s:10:\"\0*\0version\";s:5:\"6.5.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\Intern\\08\\autocare-system\\node_modules\\fdir\";}s:8:\"fsevents\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"fsevents\";s:10:\"\0*\0version\";s:5:\"2.3.3\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\node_modules\\fsevents\";}s:15:\"get-caller-file\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"get-caller-file\";s:10:\"\0*\0version\";s:5:\"2.0.5\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\node_modules\\get-caller-file\";}s:11:\"graceful-fs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"graceful-fs\";s:10:\"\0*\0version\";s:6:\"4.2.11\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\graceful-fs\";}s:4:\"jiti\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"jiti\";s:10:\"\0*\0version\";s:5:\"2.7.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\Intern\\08\\autocare-system\\node_modules\\jiti\";}s:19:\"laravel-vite-plugin\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"laravel-vite-plugin\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.1\";s:7:\"\0*\0path\";s:61:\"D:\\Intern\\08\\autocare-system\\node_modules\\laravel-vite-plugin\";}s:12:\"lightningcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"lightningcss\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss\";}s:26:\"lightningcss-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"lightningcss-android-arm64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-android-arm64\";}s:25:\"lightningcss-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"lightningcss-darwin-arm64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-darwin-arm64\";}s:23:\"lightningcss-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"lightningcss-darwin-x64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-darwin-x64\";}s:24:\"lightningcss-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"lightningcss-freebsd-x64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-freebsd-x64\";}s:32:\"lightningcss-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"lightningcss-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-linux-arm-gnueabihf\";}s:28:\"lightningcss-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"lightningcss-linux-arm64-gnu\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-linux-arm64-gnu\";}s:29:\"lightningcss-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"lightningcss-linux-arm64-musl\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-linux-arm64-musl\";}s:26:\"lightningcss-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"lightningcss-linux-x64-gnu\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-linux-x64-gnu\";}s:27:\"lightningcss-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"lightningcss-linux-x64-musl\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-linux-x64-musl\";}s:29:\"lightningcss-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"lightningcss-win32-arm64-msvc\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-win32-arm64-msvc\";}s:27:\"lightningcss-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"lightningcss-win32-x64-msvc\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-win32-x64-msvc\";}s:12:\"magic-string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"magic-string\";s:10:\"\0*\0version\";s:7:\"0.30.21\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\magic-string\";}s:6:\"nanoid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"nanoid\";s:10:\"\0*\0version\";s:6:\"3.3.20\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\Intern\\08\\autocare-system\\node_modules\\nanoid\";}s:10:\"picocolors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"picocolors\";s:10:\"\0*\0version\";s:5:\"1.1.1\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\picocolors\";}s:9:\"picomatch\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"picomatch\";s:10:\"\0*\0version\";s:5:\"4.0.7\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\picomatch\";}s:7:\"postcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"postcss\";s:10:\"\0*\0version\";s:6:\"8.5.31\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\node_modules\\postcss\";}s:8:\"rolldown\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"rolldown\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\node_modules\\rolldown\";}s:4:\"rxjs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"rxjs\";s:10:\"\0*\0version\";s:5:\"7.8.2\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\Intern\\08\\autocare-system\\node_modules\\rxjs\";}s:11:\"shell-quote\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"shell-quote\";s:10:\"\0*\0version\";s:6:\"1.12.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\shell-quote\";}s:13:\"source-map-js\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"source-map-js\";s:10:\"\0*\0version\";s:5:\"1.2.2\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\node_modules\\source-map-js\";}s:14:\"supports-color\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"supports-color\";s:10:\"\0*\0version\";s:6:\"10.2.2\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\node_modules\\supports-color\";}s:11:\"tailwindcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"tailwindcss\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^4.0.0\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\tailwindcss\";}s:7:\"tapable\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"tapable\";s:10:\"\0*\0version\";s:5:\"2.3.3\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\node_modules\\tapable\";}s:10:\"tinyglobby\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"tinyglobby\";s:10:\"\0*\0version\";s:6:\"0.2.17\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\tinyglobby\";}s:9:\"tree-kill\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"tree-kill\";s:10:\"\0*\0version\";s:5:\"1.2.2\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\tree-kill\";}s:5:\"tslib\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"tslib\";s:10:\"\0*\0version\";s:5:\"2.8.1\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\node_modules\\tslib\";}s:4:\"vite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"vite\";s:10:\"\0*\0version\";s:5:\"8.3.4\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^8.0.0\";s:7:\"\0*\0path\";s:46:\"D:\\Intern\\08\\autocare-system\\node_modules\\vite\";}s:23:\"vite-plugin-full-reload\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"vite-plugin-full-reload\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\node_modules\\vite-plugin-full-reload\";}s:4:\"y18n\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"y18n\";s:10:\"\0*\0version\";s:5:\"5.0.8\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\Intern\\08\\autocare-system\\node_modules\\y18n\";}s:5:\"yargs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"yargs\";s:10:\"\0*\0version\";s:6:\"18.0.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\node_modules\\yargs\";}s:12:\"yargs-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"yargs-parser\";s:10:\"\0*\0version\";s:6:\"22.0.0\";s:9:\"\0*\0source\";r:1102;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\yargs-parser\";}}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:125:{i:0;r:1099;i:1;r:1107;i:2;r:1115;i:3;r:1123;i:4;r:1131;i:5;r:1139;i:6;r:1147;i:7;r:1155;i:8;r:1163;i:9;r:1171;i:10;r:1179;i:11;r:1187;i:12;r:1195;i:13;r:1203;i:14;r:1211;i:15;r:1219;i:16;r:1227;i:17;r:1235;i:18;r:1243;i:19;r:1251;i:20;r:1259;i:21;r:1267;i:22;r:1275;i:23;r:1283;i:24;r:1291;i:25;r:1299;i:26;r:1307;i:27;r:1315;i:28;r:1323;i:29;r:1331;i:30;r:1339;i:31;r:1347;i:32;r:1355;i:33;r:1363;i:34;r:1371;i:35;r:1379;i:36;r:1387;i:37;r:1395;i:38;r:1403;i:39;r:1411;i:40;r:1419;i:41;r:1427;i:42;r:1435;i:43;r:1443;i:44;r:1451;i:45;r:1459;i:46;r:1467;i:47;r:1475;i:48;r:1483;i:49;r:1491;i:50;r:1499;i:51;r:1507;i:52;r:1515;i:53;r:1523;i:54;r:1531;i:55;r:1539;i:56;r:1547;i:57;r:1555;i:58;r:1563;i:59;r:1571;i:60;r:1579;i:61;r:1587;i:62;r:1595;i:63;r:1603;i:64;r:1611;i:65;r:1619;i:66;r:1627;i:67;r:1635;i:68;r:1643;i:69;r:1651;i:70;r:1659;i:71;r:1667;i:72;r:1675;i:73;r:1683;i:74;r:1691;i:75;r:1699;i:76;r:1707;i:77;r:1715;i:78;r:1723;i:79;r:1731;i:80;r:1739;i:81;r:1747;i:82;r:1755;i:83;r:1763;i:84;r:1771;i:85;r:1779;i:86;r:1787;i:87;r:1795;i:88;r:1803;i:89;r:1811;i:90;r:1819;i:91;r:1827;i:92;r:1835;i:93;r:1843;i:94;r:1851;i:95;r:1859;i:96;r:1867;i:97;r:1875;i:98;r:1883;i:99;r:1891;i:100;r:1899;i:101;r:1907;i:102;r:1915;i:103;r:1923;i:104;r:1931;i:105;r:1939;i:106;r:1947;i:107;r:1955;i:108;r:1963;i:109;r:1971;i:110;r:1979;i:111;r:1987;i:112;r:1995;i:113;r:2003;i:114;r:2011;i:115;r:2019;i:116;r:2027;i:117;r:2035;i:118;r:2043;i:119;r:2051;i:120;r:2059;i:121;r:2067;i:122;r:2075;i:123;r:2083;i:124;r:2091;}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:17:\"\0*\0packageManager\";E:41:\"Laravel\\Roster\\Enums\\JsPackageManager:Npm\";}s:6:\"stacks\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:1:{i:0;E:32:\"Laravel\\Roster\\Enums\\Stack:Blade\";}}s:21:\"browserTestFrameworks\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}s:9:\"frontends\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}s:6:\"agents\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:2:{i:0;E:37:\"Laravel\\Roster\\Enums\\Agent:ClaudeCode\";i:1;E:32:\"Laravel\\Roster\\Enums\\Agent:Codex\";}}s:7:\"editors\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}}', 1793173378);

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
(5, 'Front & Rear Mats', 'active', '2026-10-27 17:08:27', '2026-10-27 17:08:27');

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
(11, 10, 5, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `company_holidays`
--

CREATE TABLE `company_holidays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `duration` varchar(20) NOT NULL DEFAULT 'full_day',
  `holiday_type` varchar(20) NOT NULL DEFAULT 'paid',
  `applies_to_all` tinyint(1) NOT NULL DEFAULT 1,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_holidays`
--

INSERT INTO `company_holidays` (`id`, `branch_id`, `name`, `start_date`, `end_date`, `duration`, `holiday_type`, `applies_to_all`, `status`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Deepavali Festival', '2026-10-31', '2026-10-31', 'full_day', 'paid', 1, 'active', NULL, 2, '2026-10-27 06:47:15', '2026-10-27 06:47:15');

-- --------------------------------------------------------

--
-- Table structure for table `company_holiday_employees`
--

CREATE TABLE `company_holiday_employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_holiday_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `signature_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_settings`
--

INSERT INTO `company_settings` (`id`, `company_name`, `registration_number`, `address`, `phone`, `email`, `tax_number`, `logo_path`, `signature_path`, `created_at`, `updated_at`) VALUES
(1, 'Viran Auto Care', 'PV-2026-98765', 'No. 10/B, Mattegoda Road, Mattegoda 10240', '077 620 80 59', 'info@autocare.local', 'VAT-123456789-000', 'company/viran_auto_care_logo.png', NULL, '2026-10-19 06:08:35', '2026-10-21 07:59:28');

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
(38, 'Ruwan Silva', '0771122334', 'ruwan.silva@example.local', 'individual', NULL, NULL, NULL, '45 Galle Road, Colombo', NULL, 1, 'active', '2026-10-27 06:32:22', '2026-10-27 06:32:22'),
(39, 'Dinesh Perera', '0772233445', 'dinesh.perera@example.local', 'individual', NULL, NULL, NULL, '82 Kandy Road, Kiribathgoda', NULL, 1, 'active', '2026-10-27 06:32:28', '2026-10-27 06:32:28'),
(40, 'Chamara Wijesinghe', '0773344556', 'chamara.w@example.local', 'individual', NULL, NULL, NULL, '19 High Level Road, Maharagama', NULL, 1, 'active', '2026-10-27 06:32:33', '2026-10-27 06:32:33'),
(41, 'Kasun Jayawardena', '0774455667', 'kasun.j@example.local', 'individual', NULL, NULL, NULL, '63 Negombo Road, Ja-Ela', NULL, 1, 'active', '2026-10-27 06:32:39', '2026-10-27 06:32:39'),
(42, 'Sunil Fernando', '0775556677', 'sunil.f@example.local', 'individual', NULL, NULL, NULL, '110 Station Road, Moratuwa', NULL, 1, 'active', '2026-10-27 06:32:45', '2026-10-27 17:33:59'),
(43, 'Walk-in Customer', '0000000000', NULL, 'individual', NULL, NULL, NULL, NULL, NULL, 1, 'active', '2026-10-27 17:34:10', '2026-10-27 17:34:10'),
(44, 'Shashika', '071234567', 'Sashika@gmail.com', 'company', 'Lanka Express Logistics s (Pvt) Ltd', 'CM2323', NULL, 'Maharagama', NULL, 1, 'active', '2026-10-28 00:23:08', '2026-10-28 00:23:08'),
(45, 'Mihin', '0714624555', 'Mihin@lanka.com', 'individual', NULL, NULL, NULL, NULL, NULL, 1, 'active', '2026-10-28 00:29:23', '2026-10-28 00:29:23'),
(46, 'Saparamadu', '0714624534', 'Mihin@lanka.com', 'individual', NULL, NULL, NULL, NULL, NULL, 1, 'active', '2026-10-28 00:30:28', '2026-10-28 00:30:28'),
(47, 'Malaka', '071463434', 'MalakaSashika@gmail.com', 'company', 'RAV Transport Ltd', 'CM23233', NULL, 'Maharagama', NULL, 1, 'active', '2026-10-28 00:32:27', '2026-10-28 00:32:27'),
(48, 'Dinesh Perera', '0771239988', 'dinesh@lankaexpress.local', 'company', 'Lanka Express Logistics (Pvt) Ltd', 'PV-102938', NULL, 'No. 45 Logistics Park, Kelaniya', 'Corporate fleet logistics partner with 30-day payment credit terms.', 1, 'active', '2026-10-28 00:40:35', '2026-10-28 00:40:35'),
(49, 'Shehan', '0714624000', NULL, 'individual', NULL, NULL, NULL, NULL, NULL, 1, 'active', '2026-10-28 02:23:18', '2026-10-28 02:23:18');

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
(6, 44, 200000.00, 30, 0.00, 'active', 'Account opened on registration. Payment terms: 30_days.', 2, '2026-10-28 00:23:08', '2026-10-28 00:23:08'),
(7, 47, 300000.00, 30, 0.00, 'active', 'Account opened on registration. Payment terms: 30_days.', 2, '2026-10-28 00:32:27', '2026-10-28 00:32:37'),
(8, 48, 500000.00, 30, 0.00, 'active', 'Account opened on registration. Payment terms: 30_days.', 2, '2026-10-28 00:40:35', '2026-10-28 00:40:35');

-- --------------------------------------------------------

--
-- Table structure for table `direct_sales`
--

CREATE TABLE `direct_sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sale_number` varchar(50) NOT NULL,
  `bill_number` varchar(100) DEFAULT NULL,
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

INSERT INTO `direct_sales` (`id`, `branch_id`, `sale_number`, `bill_number`, `invoice_id`, `customer_id`, `sale_date`, `subtotal`, `discount_amount`, `tax_amount`, `grand_total`, `amount_paid`, `change_amount`, `payment_method`, `status`, `notes`, `cancellation_reason`, `cancelled_by`, `cancelled_at`, `stock_reversed`, `created_by`, `created_at`, `updated_at`) VALUES
(42, 1, 'DS-202610-0001', 'BILL-DS-202610-0001', NULL, 43, '2026-10-27', 18500.00, 0.00, 0.00, 18500.00, 3000.00, 0.00, 'cash', 'completed', NULL, NULL, NULL, NULL, 0, 2, '2026-10-27 17:34:10', '2026-10-27 17:34:10'),
(43, 1, 'DS-202610-0002', 'BILL-DS-202610-0002', NULL, 43, '2026-10-27', 3800.00, 0.00, 0.00, 3800.00, 3800.00, 0.00, 'cash', 'completed', NULL, NULL, NULL, NULL, 0, 2, '2026-10-27 17:35:14', '2026-10-27 17:35:14'),
(44, 1, 'DS-202610-0003', 'BILL-DS-202610-0003', NULL, 43, '2026-10-28', 2600.00, 0.00, 0.00, 2600.00, 2600.00, 0.00, 'cash', 'completed', NULL, NULL, NULL, NULL, 0, 2, '2026-10-27 23:35:20', '2026-10-27 23:35:20');

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
(57, 42, 35, 'OIL-TOY-0W20', 'Toyota Engine Oil 0W-20', 1.00, 18500.00, 14000.00, 18500.00, '[{\"batch_id\":40,\"batch_number\":\"B26-0027-001\",\"quantity\":1,\"unit_cost\":14000,\"line_cost\":14000,\"remaining_in_batch\":48}]', '2026-10-27 17:34:10', '2026-10-27 17:34:10'),
(58, 43, 36, 'FLT-AIR-001', 'Toyota Genuine Air Filter', 1.00, 3800.00, 2500.00, 3800.00, '[{\"batch_id\":43,\"batch_number\":\"B26-0027-004\",\"quantity\":1,\"unit_cost\":2500,\"line_cost\":2500,\"remaining_in_batch\":29}]', '2026-10-27 17:35:14', '2026-10-27 17:35:14'),
(59, 44, 34, 'FLT-TOY-001', 'Toyota Genuine Engine Oil Filter', 1.00, 2600.00, 1800.00, 2600.00, '[{\"batch_id\":41,\"batch_number\":\"B26-0027-002\",\"quantity\":1,\"unit_cost\":1800,\"line_cost\":1800,\"remaining_in_batch\":35}]', '2026-10-27 23:35:20', '2026-10-27 23:35:20');

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
  `monthly_basic_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `joining_date` date DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `salary_type` varchar(20) NOT NULL DEFAULT 'daily',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `payment_method` varchar(30) NOT NULL DEFAULT 'cash',
  `bank_name` varchar(100) DEFAULT NULL,
  `bank_branch` varchar(100) DEFAULT NULL,
  `bank_account_name` varchar(150) DEFAULT NULL,
  `bank_account_number` varchar(50) DEFAULT NULL,
  `epf_number` varchar(50) DEFAULT NULL,
  `tax_file_number` varchar(50) DEFAULT NULL,
  `primary_employment_declaration` tinyint(1) NOT NULL DEFAULT 1,
  `is_resident_for_tax` tinyint(1) NOT NULL DEFAULT 1,
  `statutory_exemption_reason` varchar(255) DEFAULT NULL,
  `is_epf_eligible` tinyint(1) NOT NULL DEFAULT 1,
  `is_etf_eligible` tinyint(1) NOT NULL DEFAULT 1,
  `eligible_for_off_days` tinyint(1) NOT NULL DEFAULT 1,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `branch_id`, `employee_number`, `name`, `mobile`, `address`, `daily_wage`, `monthly_basic_salary`, `joining_date`, `position`, `salary_type`, `is_active`, `notes`, `payment_method`, `bank_name`, `bank_branch`, `bank_account_name`, `bank_account_number`, `epf_number`, `tax_file_number`, `primary_employment_declaration`, `is_resident_for_tax`, `statutory_exemption_reason`, `is_epf_eligible`, `is_etf_eligible`, `eligible_for_off_days`, `user_id`, `created_at`, `updated_at`) VALUES
(14, 1, 'EMP-0001', 'Kamal Ranjith', '0771234561', '12/A, Temple Road, Mattegoda', 0.00, 85000.00, '2026-10-27', 'Lead Senior Mechanic', 'monthly', 1, 'Certified Toyota technician with 8 years experience.', 'bank_transfer', 'Commercial Bank', 'Kottawa', 'K. Ranjith', '8009912345', 'EPF-8891', NULL, 1, 1, NULL, 1, 1, 1, NULL, '2026-10-27 06:01:57', '2026-10-27 06:02:44'),
(15, 1, 'EMP-0002', 'Nimal Perera', '0772345672', '45, Station Road, Maharagama', 0.00, 75000.00, '2026-10-27', 'Service Advisor', 'monthly', 1, NULL, 'bank_transfer', 'Bank of Ceylon (BOC)', 'Maharagama', 'N. Perera', '7008812345', 'EPF-8892', NULL, 1, 1, NULL, 1, 1, 1, NULL, '2026-10-27 06:02:15', '2026-10-27 06:02:15'),
(16, 1, 'EMP-0003', 'Saman Kumara', '0773456783', '88, High Level Road, Pannipitiya', 3000.00, 0.00, '2026-10-27', 'Junior Mechanic', 'daily', 1, 'Apprentice technician on probation.', 'cash', NULL, NULL, NULL, NULL, 'EPF-8893', NULL, 1, 1, NULL, 1, 1, 1, NULL, '2026-10-27 06:02:30', '2026-10-27 06:02:30');

-- --------------------------------------------------------

--
-- Table structure for table `employee_advances`
--

CREATE TABLE `employee_advances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `advance_number` varchar(50) NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `advance_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `monthly_installment` decimal(12,2) DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'cash',
  `notes` text DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `payroll_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `disbursed_at` date DEFAULT NULL,
  `disbursed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `cancelled_by` bigint(20) UNSIGNED DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_advances`
--

INSERT INTO `employee_advances` (`id`, `advance_number`, `employee_id`, `branch_id`, `advance_date`, `amount`, `monthly_installment`, `payment_method`, `notes`, `status`, `payroll_item_id`, `created_by`, `approved_by`, `approved_at`, `disbursed_at`, `disbursed_by`, `rejected_by`, `rejected_at`, `rejection_reason`, `cancelled_by`, `cancelled_at`, `cancellation_reason`, `created_at`, `updated_at`) VALUES
(5, 'ADV-0001', 15, 1, '2026-10-27', 20000.00, NULL, 'cash', NULL, 'pending', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-27 06:56:13', '2026-10-27 06:56:13');

-- --------------------------------------------------------

--
-- Table structure for table `employee_advance_recoveries`
--

CREATE TABLE `employee_advance_recoveries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_advance_id` bigint(20) UNSIGNED NOT NULL,
  `payroll_period_id` bigint(20) UNSIGNED NOT NULL,
  `payroll_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `recovery_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'recovered',
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_allowances`
--

CREATE TABLE `employee_allowances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `allowance_number` varchar(50) NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `allowance_type` varchar(50) NOT NULL DEFAULT 'transport',
  `title` varchar(150) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `is_recurring` tinyint(1) NOT NULL DEFAULT 1,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `period_year` smallint(6) DEFAULT NULL,
  `period_month` tinyint(4) DEFAULT NULL,
  `is_epf_eligible` tinyint(1) NOT NULL DEFAULT 0,
  `is_etf_eligible` tinyint(1) NOT NULL DEFAULT 0,
  `is_taxable` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(30) NOT NULL DEFAULT 'active',
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `cancelled_by` bigint(20) UNSIGNED DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_allowances`
--

INSERT INTO `employee_allowances` (`id`, `allowance_number`, `employee_id`, `branch_id`, `allowance_type`, `title`, `amount`, `is_recurring`, `effective_from`, `effective_to`, `period_year`, `period_month`, `is_epf_eligible`, `is_etf_eligible`, `is_taxable`, `status`, `approved_by`, `approved_at`, `cancelled_by`, `cancelled_at`, `cancellation_reason`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'ALW-0001', 14, 1, 'meal', NULL, 2000.00, 1, '2026-10-27', NULL, NULL, NULL, 0, 0, 0, 'active', 2, '2026-10-27 06:56:34', NULL, NULL, NULL, NULL, 2, '2026-10-27 06:56:34', '2026-10-27 06:56:34');

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
(315, 14, '2026-10-27', 'full_day', 1.00, 0.00, 0.00, NULL, 2, '2026-10-27 06:21:10', '2026-10-27 06:21:10'),
(316, 15, '2026-10-27', 'full_day', 1.00, 0.00, 0.00, NULL, 2, '2026-10-27 06:21:10', '2026-10-27 06:47:09'),
(317, 16, '2026-10-27', 'full_day', 1.00, 3000.00, 3000.00, NULL, 2, '2026-10-27 06:21:10', '2026-10-27 06:21:10'),
(318, 14, '2026-10-28', 'full_day', 1.00, 0.00, 0.00, NULL, 2, '2026-10-28 00:26:39', '2026-10-28 00:26:39'),
(319, 15, '2026-10-28', 'full_day', 1.00, 0.00, 0.00, NULL, 2, '2026-10-28 00:26:39', '2026-10-28 00:26:39'),
(320, 16, '2026-10-28', 'full_day', 1.00, 3000.00, 3000.00, NULL, 2, '2026-10-28 00:26:39', '2026-10-28 00:26:39');

-- --------------------------------------------------------

--
-- Table structure for table `employee_bonuses`
--

CREATE TABLE `employee_bonuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bonus_number` varchar(50) NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bonus_type` varchar(50) NOT NULL DEFAULT 'performance',
  `calculation_type` varchar(30) NOT NULL DEFAULT 'fixed',
  `percentage_rate` decimal(5,2) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `is_epf_eligible` tinyint(1) NOT NULL DEFAULT 0,
  `is_taxable` tinyint(1) NOT NULL DEFAULT 1,
  `period_year` smallint(6) NOT NULL,
  `period_month` tinyint(4) NOT NULL,
  `effective_date` date DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `cancelled_by` bigint(20) UNSIGNED DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `payroll_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_bonuses`
--

INSERT INTO `employee_bonuses` (`id`, `bonus_number`, `employee_id`, `branch_id`, `bonus_type`, `calculation_type`, `percentage_rate`, `amount`, `is_epf_eligible`, `is_taxable`, `period_year`, `period_month`, `effective_date`, `status`, `approved_by`, `approved_at`, `rejected_by`, `rejected_at`, `rejection_reason`, `cancelled_by`, `cancelled_at`, `cancellation_reason`, `payroll_item_id`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'BON-0001', 14, 1, 'performance', 'fixed', NULL, 3000.00, 0, 1, 2026, 10, '2026-10-01', 'approved', 2, '2026-10-27 06:56:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-27 06:56:55', '2026-10-27 06:56:55');

-- --------------------------------------------------------

--
-- Table structure for table `employee_overtimes`
--

CREATE TABLE `employee_overtimes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `overtime_number` varchar(50) NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `overtime_date` date NOT NULL,
  `hours` decimal(6,2) NOT NULL,
  `hourly_rate` decimal(10,2) NOT NULL,
  `rate_multiplier` decimal(4,2) NOT NULL DEFAULT 1.50,
  `amount` decimal(12,2) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `cancelled_by` bigint(20) UNSIGNED DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `payroll_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_wage_rates`
--

CREATE TABLE `employee_wage_rates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `salary_type` varchar(20) NOT NULL DEFAULT 'daily',
  `daily_wage` decimal(12,2) NOT NULL,
  `monthly_basic_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_wage_rates`
--

INSERT INTO `employee_wage_rates` (`id`, `employee_id`, `salary_type`, `daily_wage`, `monthly_basic_salary`, `effective_from`, `effective_to`, `reason`, `created_at`, `updated_at`) VALUES
(13, 14, 'monthly', 0.00, 85000.00, '2026-10-27', NULL, 'Initial salary settings upon registration', '2026-10-27 06:01:57', '2026-10-27 06:01:57'),
(14, 15, 'monthly', 0.00, 75000.00, '2026-10-27', NULL, 'Initial salary settings upon registration', '2026-10-27 06:02:15', '2026-10-27 06:02:15'),
(15, 16, 'daily', 3000.00, 0.00, '2026-10-27', NULL, 'Initial salary settings upon registration', '2026-10-27 06:02:30', '2026-10-27 06:02:30');

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
(28, 1, 'EXP-0001', 19, 'shop', NULL, NULL, '2026-10-27', 'October 2026', 'Monthly CEB electricity bill for workshop', 18500.00, 18500.00, 0.00, 'paid', 'cash', '2026-10-27', NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-27 06:46:50', '2026-10-27 06:46:50'),
(29, 1, 'EXP-0002', 26, 'shop', NULL, NULL, '2026-10-27', 'October 2026', 'Hydraulic ramp inspection and compressor service', 12000.00, 12000.00, 0.00, 'paid', 'cash', '2026-10-27', NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-27 06:46:54', '2026-10-27 06:46:54'),
(30, 1, 'EXP-0003', 24, 'shop', NULL, NULL, '2026-10-27', 'October 2026', 'Workshop floor cleaning chemicals and detergent', 6500.00, 6500.00, 0.00, 'paid', 'cash', '2026-10-27', NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-27 06:46:58', '2026-10-27 06:46:58'),
(31, 1, 'EXP-0004', 27, 'shop', NULL, NULL, '2026-10-27', 'October 2026', 'Printer paper, receipt rolls, and stationery', 4500.00, 4500.00, 0.00, 'paid', 'cash', '2026-10-27', NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-27 06:47:03', '2026-10-27 06:47:03'),
(32, 1, 'EXP-0005', 19, 'shop', NULL, NULL, '2026-10-27', 'October 2026', 'Monthly CEB Electricity Bill for Workshop Operations', 18500.00, 18500.00, 0.00, 'paid', 'cash', '2026-10-27', NULL, NULL, 'active', NULL, NULL, NULL, 2, '2026-10-27 17:34:19', '2026-10-27 17:34:19');

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
(17, 'Salary Advance', 1, '2026-10-27 06:28:19', '2026-10-27 06:28:19'),
(18, 'Boss Expenses', 1, '2026-10-27 06:28:19', '2026-10-27 06:28:19'),
(19, 'Electricity', 1, '2026-10-27 06:28:19', '2026-10-27 06:28:19'),
(20, 'EPF / ETF', 1, '2026-10-27 06:28:19', '2026-10-27 06:28:19'),
(21, 'Staff Welfare', 1, '2026-10-27 06:28:19', '2026-10-27 06:28:19'),
(22, 'Water', 1, '2026-10-27 06:28:19', '2026-10-27 06:28:19'),
(23, 'Telephone / Internet', 1, '2026-10-27 06:28:19', '2026-10-27 06:28:19'),
(24, 'Chemicals', 1, '2026-10-27 06:28:19', '2026-10-27 06:28:19'),
(25, 'Repair', 1, '2026-10-27 06:28:19', '2026-10-27 06:28:19'),
(26, 'Maintenance', 1, '2026-10-27 06:28:19', '2026-10-27 06:28:19'),
(27, 'Other', 1, '2026-10-27 06:28:19', '2026-10-27 06:28:19');

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
(29, 28, 1, 'EPAY-202610-0001', '2026-10-27', 18500.00, 'cash', NULL, 'Initial payment at recording', 2, '2026-10-27 06:46:50', '2026-10-27 06:46:50'),
(30, 29, 1, 'EPAY-202610-0002', '2026-10-27', 12000.00, 'cash', NULL, 'Initial payment at recording', 2, '2026-10-27 06:46:54', '2026-10-27 06:46:54'),
(31, 30, 1, 'EPAY-202610-0003', '2026-10-27', 6500.00, 'cash', NULL, 'Initial payment at recording', 2, '2026-10-27 06:46:58', '2026-10-27 06:46:58'),
(32, 31, 1, 'EPAY-202610-0004', '2026-10-27', 4500.00, 'cash', NULL, 'Initial payment at recording', 2, '2026-10-27 06:47:03', '2026-10-27 06:47:03'),
(33, 32, 1, 'EPAY-202610-0005', '2026-10-27', 18500.00, 'cash', NULL, 'Initial payment at recording', 2, '2026-10-27 17:34:19', '2026-10-27 17:34:19');

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
(26, 1, 'GRN-202610-0001', 13, 'INV-CMS-2026-001', '2026-10-27', 'draft', 40000.00, NULL, NULL, NULL, NULL, 2, '2026-10-27 06:45:51', '2026-10-27 06:45:51'),
(27, 1, 'GRN-202610-0002', 13, 'INV-CMS-2026-002', '2026-10-27', 'confirmed', 1132000.00, NULL, NULL, '2026-10-27 17:10:14', 2, 2, '2026-10-27 17:10:14', '2026-10-27 17:10:14'),
(28, 1, 'GRN-202610-0003', 16, '23456', '2026-10-28', 'confirmed', 85000.00, NULL, 'grn-invoices/oGt7EFaSkmCRTDpMCzpJARVEJslUJoYjqozOdTFw.pdf', '2026-10-28 00:37:52', 2, 2, '2026-10-28 00:37:52', '2026-10-28 00:37:52');

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
(40, 26, 40, 20.00, 0.00, 2000.00, 3200.00, 40000.00, '2026-10-27 06:45:51', '2026-10-27 06:45:51'),
(41, 27, 35, 50.00, 0.00, 14000.00, 18500.00, 700000.00, '2026-10-27 17:10:14', '2026-10-27 17:10:14'),
(42, 27, 34, 40.00, 0.00, 1800.00, 2600.00, 72000.00, '2026-10-27 17:10:14', '2026-10-27 17:10:14'),
(43, 27, 37, 30.00, 0.00, 9500.00, 12500.00, 285000.00, '2026-10-27 17:10:14', '2026-10-27 17:10:14'),
(44, 27, 36, 30.00, 0.00, 2500.00, 3800.00, 75000.00, '2026-10-27 17:10:14', '2026-10-27 17:10:14'),
(45, 28, 42, 34.00, 0.00, 2500.00, 30000.00, 85000.00, '2026-10-28 00:37:52', '2026-10-28 00:37:52');

-- --------------------------------------------------------

--
-- Table structure for table `grn_sequences`
--

CREATE TABLE `grn_sequences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `series` varchar(20) NOT NULL COMMENT 'YearMonth series e.g. 202610',
  `current_number` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grn_sequences`
--

INSERT INTO `grn_sequences` (`id`, `series`, `current_number`, `created_at`, `updated_at`) VALUES
(2, '202610', 3, '2026-10-27 06:45:51', '2026-10-28 00:37:51');

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
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
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
  `vehicle_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`vehicle_snapshot`)),
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `branch_id`, `invoice_number`, `invoice_date`, `original_invoice_date`, `date_changed_by`, `date_changed_at`, `date_change_reason`, `due_date`, `customer_id`, `vehicle_id`, `quotation_id`, `job_card_id`, `invoice_type`, `subject`, `reference`, `subtotal`, `discount_amount`, `discount_type`, `discount_value`, `tax_amount`, `tax_percentage`, `additional_charges`, `grand_total`, `amount_paid`, `balance_amount`, `status`, `payment_method`, `notes`, `cancellation_reason`, `cancelled_by`, `cancelled_at`, `terms_conditions`, `customer_snapshot`, `vehicle_snapshot`, `created_by`, `created_at`, `updated_at`) VALUES
(74, 1, 'INV-202610-0001', '2026-10-28', NULL, NULL, NULL, NULL, '2026-10-28', 38, NULL, NULL, NULL, 'job_card', 'Service Bill for WP CAK-4512 (Job #JOB-202610-0001)', 'JOB-202610-0001 (Reopened)', 25100.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 25100.00, 0.00, 0.00, 'cancelled', NULL, 'Voided/Archived: Job Card #JOB-202610-0001 reopened for editing by Admin User on 2026-10-28 10:36:03.', 'Job Card #JOB-202610-0001 reopened for editing by Admin User.', 2, '2026-10-27 23:36:03', NULL, '{\"customer_name\":\"Ruwan Silva\",\"phone\":\"0771122334\",\"vehicle_number\":\"WP CAK-4512\",\"vehicle_type\":\"Car \\/ Sedan\",\"current_mileage\":65000,\"next_service_mileage\":75000}', NULL, 2, '2026-10-27 17:25:05', '2026-10-27 23:36:03'),
(75, 1, 'INV-202610-0006', '2026-10-27', NULL, NULL, NULL, NULL, '2026-11-03', 41, NULL, NULL, NULL, 'manual', 'Routine Automotive Maintenance', NULL, 9000.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 9000.00, 800.00, 8200.00, 'partially_paid', NULL, NULL, NULL, NULL, NULL, '1. Goods and services sold are subject to AutoCare warranty terms.\\n2. Payment is due upon receipt unless credit terms agreed.', '{\"customer_name\":\"Kasun Jayawardena\",\"phone\":\"0774455667\",\"email\":\"kasun.j@example.local\",\"customer_type\":\"individual\",\"company_name\":null,\"tax_number\":null,\"address\":\"63 Negombo Road, Ja-Ela\"}', NULL, 2, '2026-10-27 17:29:29', '2026-10-28 00:05:25'),
(76, 1, 'INV-202610-0007', '2026-10-27', NULL, NULL, NULL, NULL, '2026-11-03', 39, NULL, 12, NULL, 'quotation', 'Engine Diagnostic and Inspection', 'QT-202610-0002', 5500.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 5500.00, 5500.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, 'Quotation valid for 14 days. Parts subject to supplier market availability. Service labor warranty 30 days.', '{\"name\":\"Dinesh Perera\",\"phone\":\"0772233445\",\"email\":\"dinesh.perera@example.local\",\"customer_type\":\"individual\",\"company_name\":null,\"tax_number\":null,\"address\":\"82 Kandy Road, Kiribathgoda\"}', NULL, 2, '2026-10-27 17:29:39', '2026-10-27 17:30:00'),
(77, 1, 'INV-202610-0011', '2026-10-28', NULL, NULL, NULL, NULL, '2026-10-28', 38, NULL, NULL, 51, 'job_card', 'Service Bill for WP CAK-4512 (Job #JOB-202610-0006)', 'JOB-202610-0006', 25100.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 25100.00, 25100.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, '{\"customer_name\":\"Ruwan Silva\",\"phone\":\"0771122334\",\"vehicle_number\":\"WP CAK-4512\",\"vehicle_type\":\"Car \\/ Sedan\",\"current_mileage\":65000,\"next_service_mileage\":75000}', NULL, 2, '2026-10-27 22:54:32', '2026-10-27 22:55:57'),
(78, 1, 'INV-202610-0012', '2026-10-28', NULL, NULL, NULL, NULL, '2026-10-28', 40, NULL, NULL, 48, 'job_card', 'Service Bill for WP CAB-1902 (Job #JOB-202610-0003)', 'JOB-202610-0003', 15500.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 15500.00, 15500.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, '{\"customer_name\":\"Chamara Wijesinghe\",\"phone\":\"0773344556\",\"vehicle_number\":\"WP CAB-1902\",\"vehicle_type\":\"Car \\/ Sedan\",\"current_mileage\":94000,\"next_service_mileage\":104000}', NULL, 2, '2026-10-27 23:22:57', '2026-10-28 00:01:36'),
(79, 1, 'INV-202610-0013', '2026-10-28', NULL, NULL, NULL, NULL, '2026-10-28', 38, NULL, NULL, 52, 'job_card', 'Service Bill for WP CAK-4512 (Job #JOB-202610-0007)', 'JOB-202610-0007', 5100.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 5100.00, 5100.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, '{\"customer_name\":\"Ruwan Silva\",\"phone\":\"0771122334\",\"vehicle_number\":\"WP CAK-4512\",\"vehicle_type\":\"Car \\/ Sedan\",\"current_mileage\":65000,\"next_service_mileage\":75000}', NULL, 2, '2026-10-27 23:34:34', '2026-10-27 23:58:31'),
(80, 1, 'INV-202610-0014', '2026-10-28', NULL, NULL, NULL, NULL, '2026-10-28', 44, NULL, NULL, 53, 'job_card', 'Service Bill for ND-2343 (Job #JOB-202610-0008)', 'JOB-202610-0008', 4500.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 4500.00, 4500.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, '{\"customer_name\":\"Shashika\",\"phone\":\"071234567\",\"vehicle_number\":\"ND-2343\",\"vehicle_type\":\"Van\",\"current_mileage\":3400,\"next_service_mileage\":8400}', NULL, 2, '2026-10-28 00:24:54', '2026-10-28 00:31:06'),
(81, 1, 'INV-202610-0015', '2026-10-28', NULL, NULL, NULL, NULL, '2026-10-28', 45, NULL, NULL, 54, 'job_card', 'Service Bill for NF-3456 (Job #JOB-202610-0009)', 'JOB-202610-0009', 34444.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 34444.00, 0.00, 34444.00, 'issued', NULL, NULL, NULL, NULL, NULL, NULL, '{\"customer_name\":\"Mihin\",\"phone\":\"0714624555\",\"vehicle_number\":\"NF-3456\",\"vehicle_type\":\"Van\",\"current_mileage\":3400,\"next_service_mileage\":6900}', NULL, 2, '2026-10-28 00:29:48', '2026-10-28 00:29:48'),
(82, 1, 'INV-202610-0016', '2026-10-28', NULL, NULL, NULL, NULL, '2026-10-28', 47, NULL, NULL, 56, 'job_card', 'Service Bill for ND-23434 (Job #JOB-202610-0011)', 'JOB-202610-0011', 27960.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 27960.00, 0.00, 27960.00, 'issued', NULL, NULL, NULL, NULL, NULL, NULL, '{\"customer_name\":\"Malaka\",\"phone\":\"071463434\",\"vehicle_number\":\"ND-23434\",\"vehicle_type\":\"Van\",\"current_mileage\":3400,\"next_service_mileage\":8400}', NULL, 2, '2026-10-28 00:33:39', '2026-10-28 00:33:39'),
(83, 1, 'INV-202610-0018', '2026-10-28', NULL, NULL, NULL, NULL, '2026-10-28', 48, NULL, NULL, 57, 'job_card', 'Service Bill for CP HIL-2020 (Job #JOB-202610-0012)', 'JOB-202610-0012', 7500.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 7500.00, 7500.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, '{\"customer_name\":\"Dinesh Perera\",\"phone\":\"0771239988\",\"vehicle_number\":\"CP HIL-2020\",\"vehicle_type\":\"SUV\",\"current_mileage\":45000,\"next_service_mileage\":50000}', NULL, 2, '2026-10-28 00:44:28', '2026-10-28 00:44:41'),
(84, 1, 'INV-202610-0061', '2026-10-28', NULL, NULL, NULL, NULL, '2026-11-04', 49, NULL, NULL, NULL, 'manual', 'Direct Invoice for Shehan (DC-2323)', NULL, 2300.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 2300.00, 2300.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, '1. Goods and services sold are subject to AutoCare warranty terms.\\n2. Payment is due upon receipt unless credit terms agreed.', '{\"customer_name\":\"Shehan\",\"phone\":\"0714624000\",\"email\":null,\"customer_type\":\"individual\",\"company_name\":null,\"tax_number\":null,\"address\":null,\"is_manual\":false,\"vehicle_number\":\"DC-2323\",\"vehicle_type\":\"Car \\/ Sedan\",\"vehicle_make_model\":null}', NULL, 2, '2026-10-28 02:23:18', '2026-10-28 02:23:35'),
(85, 1, 'INV-202610-0062', '2026-10-28', NULL, NULL, NULL, NULL, '2026-11-04', 49, NULL, 15, NULL, 'quotation', 'Service Quotation for WE-2333', 'QT-202610-0005', 23330.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 23330.00, 0.00, 23330.00, 'issued', NULL, NULL, NULL, NULL, NULL, 'Quotation valid for 14 days. Parts subject to supplier market availability. Service labor warranty 30 days.', '{\"name\":\"Shehan\",\"phone\":\"0714624000\",\"email\":null,\"customer_type\":\"individual\",\"company_name\":null,\"tax_number\":null,\"address\":null,\"is_manual\":false}', NULL, 2, '2026-10-28 02:24:30', '2026-10-28 02:24:30'),
(86, 1, 'INV-202610-0063', '2026-10-28', NULL, NULL, NULL, NULL, '2026-10-28', 49, NULL, NULL, 58, 'job_card', 'Service Bill for WP HIL-2020 (Job #JOB-202610-0013)', 'JOB-202610-0013', 2600.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 2600.00, 2600.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, '{\"customer_name\":\"Shehan\",\"phone\":\"0714624000\",\"vehicle_number\":\"WP HIL-2020\",\"vehicle_type\":\"Car \\/ Sedan\",\"current_mileage\":0,\"next_service_mileage\":null}', NULL, 2, '2026-10-28 02:28:45', '2026-10-28 02:28:51'),
(87, 1, 'INV-202610-0077', '2026-10-28', NULL, NULL, NULL, NULL, '2026-11-04', 49, NULL, NULL, NULL, 'manual', 'Direct Invoice for Shehan (DC-2323)', NULL, 0.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'issued', NULL, NULL, NULL, NULL, NULL, '1. Goods and services sold are subject to AutoCare warranty terms.\\n2. Payment is due upon receipt unless credit terms agreed.', '{\"customer_name\":\"Shehan\",\"phone\":\"0714624000\",\"email\":null,\"customer_type\":\"individual\",\"company_name\":null,\"tax_number\":null,\"address\":null,\"is_manual\":false,\"vehicle_number\":\"DC-2323\",\"vehicle_type\":\"Car \\/ Sedan\",\"vehicle_make_model\":null}', NULL, 2, '2026-10-28 02:46:07', '2026-10-28 02:46:07'),
(88, 1, 'INV-202610-0078', '2026-10-28', NULL, NULL, NULL, NULL, '2026-11-04', 48, NULL, 18, NULL, 'quotation', 'Service Quotation for CP HIL-2020', 'QT-202610-0008', 0.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'issued', NULL, NULL, NULL, NULL, NULL, 'Quotation valid for 14 days. Parts subject to supplier market availability. Service labor warranty 30 days.', '{\"name\":\"Dinesh Perera\",\"phone\":\"0771239988\",\"email\":\"dinesh@lankaexpress.local\",\"customer_type\":\"company\",\"company_name\":\"Lanka Express Logistics (Pvt) Ltd\",\"tax_number\":null,\"address\":\"No. 45 Logistics Park, Kelaniya\",\"is_manual\":false}', NULL, 2, '2026-10-28 02:46:28', '2026-10-28 02:46:28');

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
(57, 74, NULL, 0, 'service', 15, 'SRV-001', 'Engine Oil Change', NULL, 1.00, NULL, 2500.00, 0.00, 'none', 0.00, 0.00, 0.00, 2500.00, 0.00, NULL, '2026-10-27 17:25:05', '2026-10-27 17:25:05'),
(58, 74, NULL, 0, 'service', 16, 'SRV-002', 'Oil Filter Replacement', NULL, 1.00, NULL, 1500.00, 0.00, 'none', 0.00, 0.00, 0.00, 1500.00, 0.00, NULL, '2026-10-27 17:25:05', '2026-10-27 17:25:05'),
(59, 74, NULL, 0, 'product', 35, 'OIL-TOY-0W20', 'Toyota Engine Oil 0W-20', NULL, 1.00, NULL, 18500.00, 0.00, 'none', 0.00, 0.00, 0.00, 18500.00, 14000.00, NULL, '2026-10-27 17:25:05', '2026-10-27 17:25:05'),
(60, 74, NULL, 0, 'product', 34, 'FLT-TOY-001', 'Toyota Genuine Engine Oil Filter', NULL, 1.00, NULL, 2600.00, 0.00, 'none', 0.00, 0.00, 0.00, 2600.00, 1800.00, NULL, '2026-10-27 17:25:05', '2026-10-27 17:25:05'),
(61, 75, NULL, 1, 'service', 15, NULL, 'Engine Oil Change', 'Drain old oil and fill new oil', 1.00, 'Job', 2500.00, 0.00, 'none', 0.00, 0.00, 0.00, 2500.00, 0.00, NULL, '2026-10-27 17:29:29', '2026-10-27 17:29:29'),
(62, 75, NULL, 2, 'service', 19, NULL, 'Air Conditioning Service', 'AC gas check and filter cleaning', 1.00, 'Job', 6500.00, 0.00, 'none', 0.00, 0.00, 0.00, 6500.00, 0.00, NULL, '2026-10-27 17:29:29', '2026-10-27 17:29:29'),
(63, 76, 24, 1, 'service', 18, NULL, 'Engine Diagnostic Scan', 'OBD-II computer fault code scan', 1.00, 'Job', 3500.00, 0.00, 'none', 0.00, 0.00, 0.00, 3500.00, 0.00, NULL, '2026-10-27 17:29:39', '2026-10-27 17:29:39'),
(64, 76, 25, 2, 'service', 21, NULL, 'Suspension Inspection', 'Bushing, shock absorber and tie-rod check', 1.00, 'Job', 2000.00, 0.00, 'none', 0.00, 0.00, 0.00, 2000.00, 0.00, NULL, '2026-10-27 17:29:39', '2026-10-27 17:29:39'),
(65, 77, NULL, 0, 'service', 15, 'SRV-001', 'Engine Oil Change', NULL, 1.00, NULL, 2500.00, 0.00, 'none', 0.00, 0.00, 0.00, 2500.00, 0.00, NULL, '2026-10-27 22:54:32', '2026-10-27 22:54:32'),
(66, 77, NULL, 0, 'service', 16, 'SRV-002', 'Oil Filter Replacement', NULL, 1.00, NULL, 1500.00, 0.00, 'none', 0.00, 0.00, 0.00, 1500.00, 0.00, NULL, '2026-10-27 22:54:32', '2026-10-27 22:54:32'),
(67, 77, NULL, 0, 'product', 34, 'FLT-TOY-001', 'Toyota Genuine Engine Oil Filter', NULL, 1.00, NULL, 2600.00, 0.00, 'none', 0.00, 0.00, 0.00, 2600.00, 1800.00, NULL, '2026-10-27 22:54:32', '2026-10-27 22:54:32'),
(68, 77, NULL, 0, 'product', 35, 'OIL-TOY-0W20', 'Toyota Engine Oil 0W-20', NULL, 1.00, NULL, 18500.00, 0.00, 'none', 0.00, 0.00, 0.00, 18500.00, 14000.00, NULL, '2026-10-27 22:54:32', '2026-10-27 22:54:32'),
(69, 78, NULL, 0, 'service', 17, 'SRV-003', 'Brake Pad Replacement', NULL, 1.00, NULL, 3000.00, 0.00, 'none', 0.00, 0.00, 0.00, 3000.00, 0.00, NULL, '2026-10-27 23:22:57', '2026-10-27 23:22:57'),
(70, 78, NULL, 0, 'product', 37, 'BRK-TOY-001', 'Toyota Genuine Brake Pads', NULL, 1.00, NULL, 12500.00, 0.00, 'none', 0.00, 0.00, 0.00, 12500.00, 9500.00, NULL, '2026-10-27 23:22:57', '2026-10-27 23:22:57'),
(71, 79, NULL, 0, 'service', 15, 'SRV-001', 'Engine Oil Change', NULL, 1.00, NULL, 2500.00, 0.00, 'none', 0.00, 0.00, 0.00, 2500.00, 0.00, NULL, '2026-10-27 23:34:34', '2026-10-27 23:34:34'),
(72, 79, NULL, 0, 'product', 34, 'FLT-TOY-001', 'Toyota Genuine Engine Oil Filter', NULL, 1.00, NULL, 2600.00, 0.00, 'none', 0.00, 0.00, 0.00, 2600.00, 1800.00, NULL, '2026-10-27 23:34:34', '2026-10-27 23:34:34'),
(73, 80, NULL, 0, 'service', 18, 'SRV-004', 'Engine Diagnostic Scan', NULL, 1.00, NULL, 4500.00, 0.00, 'none', 0.00, 0.00, 0.00, 4500.00, 0.00, NULL, '2026-10-28 00:24:54', '2026-10-28 00:24:54'),
(74, 81, NULL, 0, 'service', 16, 'SRV-002', 'Oil Filter Replacement', NULL, 1.00, NULL, 34444.00, 0.00, 'none', 0.00, 0.00, 0.00, 34444.00, 0.00, NULL, '2026-10-28 00:29:48', '2026-10-28 00:29:48'),
(75, 82, NULL, 0, 'service', 19, 'SRV-005', 'Air Conditioning Service', NULL, 1.00, NULL, 23400.00, 0.00, 'none', 0.00, 0.00, 0.00, 23400.00, 0.00, NULL, '2026-10-28 00:33:39', '2026-10-28 00:33:39'),
(76, 82, NULL, 0, 'service', 15, 'SRV-001', 'Engine Oil Change', NULL, 1.00, NULL, 4560.00, 0.00, 'none', 0.00, 0.00, 0.00, 4560.00, 0.00, NULL, '2026-10-28 00:33:39', '2026-10-28 00:33:39'),
(77, 83, NULL, 0, 'service', 15, 'SRV-001', 'Engine Oil Change', NULL, 1.00, NULL, 2500.00, 0.00, 'none', 0.00, 0.00, 0.00, 2500.00, 0.00, NULL, '2026-10-28 00:44:28', '2026-10-28 00:44:28'),
(78, 83, NULL, 0, 'service', 18, 'SRV-004', 'Engine Diagnostic Scan', NULL, 1.00, NULL, 3500.00, 0.00, 'none', 0.00, 0.00, 0.00, 3500.00, 0.00, NULL, '2026-10-28 00:44:28', '2026-10-28 00:44:28'),
(79, 83, NULL, 0, 'service', 21, 'SRV-007', 'Suspension Inspection', NULL, 1.00, NULL, 1500.00, 0.00, 'none', 0.00, 0.00, 0.00, 1500.00, 0.00, NULL, '2026-10-28 00:44:29', '2026-10-28 00:44:29'),
(80, 84, NULL, 1, 'service', 17, NULL, 'Brake Pad Replacement', 'Inspect calipers and replace brake pads', 1.00, 'Job', 2300.00, 0.00, 'none', 0.00, 0.00, 0.00, 2300.00, 0.00, NULL, '2026-10-28 02:23:18', '2026-10-28 02:23:18'),
(81, 85, 29, 1, 'service', 17, NULL, 'Brake Pad Replacement', 'Inspect calipers and replace brake pads', 1.00, 'Job', 23330.00, 0.00, 'none', 0.00, 0.00, 0.00, 23330.00, 0.00, NULL, '2026-10-28 02:24:30', '2026-10-28 02:24:30'),
(82, 86, NULL, 0, 'product', 34, 'FLT-TOY-001', 'Toyota Genuine Engine Oil Filter', NULL, 1.00, NULL, 2600.00, 0.00, 'none', 0.00, 0.00, 0.00, 2600.00, 1800.00, NULL, '2026-10-28 02:28:45', '2026-10-28 02:28:45'),
(83, 87, NULL, 1, 'service', NULL, NULL, 'Brake Pad Replacement', NULL, 1.00, 'Job', 0.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2026-10-28 02:46:07', '2026-10-28 02:46:07'),
(84, 88, 32, 1, 'service', 18, NULL, 'Engine Diagnostic Scan', 'OBD-II computer fault code scan', 1.00, 'Job', 0.00, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2026-10-28 02:46:28', '2026-10-28 02:46:28');

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
(2, '202610', 81, '2026-10-27 17:25:05', '2026-10-28 03:05:26');

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
  `driver_name` varchar(150) DEFAULT NULL,
  `driver_mobile` varchar(50) DEFAULT NULL,
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
  `completed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_cards`
--

INSERT INTO `job_cards` (`id`, `branch_id`, `job_number`, `date`, `customer_id`, `vehicle_id`, `vehicle_type_id`, `current_mileage`, `next_service_mileage`, `status`, `assigned_to`, `driver_name`, `driver_mobile`, `service_total`, `product_total`, `discount_amount`, `tax_amount`, `grand_total`, `invoice_id`, `appointment_id`, `customer_notes`, `service_notes`, `cancellation_reason`, `cancelled_by`, `cancelled_at`, `started_at`, `completed_at`, `completed_by`, `delivered_at`, `created_by`, `created_at`, `updated_at`) VALUES
(46, 1, 'JOB-202610-0001', '2026-10-27', 38, 52, 10, 65000, 75000, 'pending', 2, NULL, NULL, 4000.00, 21100.00, 0.00, 0.00, 25100.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-27 22:55:05', NULL, NULL, NULL, 2, '2026-10-27 17:11:25', '2026-10-27 23:36:03'),
(47, 1, 'JOB-202610-0002', '2026-10-27', 39, 53, 10, 82000, 92000, 'pending', 2, NULL, NULL, 5500.00, 21100.00, 0.00, 0.00, 26600.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-27 17:11:33', '2026-10-27 23:29:38'),
(48, 1, 'JOB-202610-0003', '2026-10-27', 40, 54, 10, 94000, 104000, 'completed', 2, NULL, NULL, 3000.00, 12500.00, 0.00, 0.00, 15500.00, 78, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-28 04:52:57', '2026-10-28 04:52:57', 2, NULL, 2, '2026-10-27 17:11:42', '2026-10-27 23:22:57'),
(49, 1, 'JOB-202610-0004', '2026-10-27', 41, 55, 10, 48000, 58000, 'pending', NULL, NULL, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-27 17:11:50', '2026-10-27 17:11:50'),
(50, 1, 'JOB-202610-0005', '2026-10-27', 42, 56, 8, 55000, 65000, 'pending', NULL, NULL, NULL, 0.00, 3800.00, 0.00, 0.00, 3800.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-27 17:11:58', '2026-10-27 18:14:56'),
(51, 1, 'JOB-202610-0006', '2026-10-28', 38, 52, 10, 65000, 75000, 'delivered', 2, NULL, NULL, 4000.00, 21100.00, 0.00, 0.00, 25100.00, 77, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-28 04:24:32', '2026-10-28 04:24:32', 2, '2026-10-27 22:56:06', 2, '2026-10-27 22:53:49', '2026-10-27 22:56:06'),
(52, 1, 'JOB-202610-0007', '2026-10-28', 38, 52, 10, 65000, 75000, 'completed', 2, NULL, NULL, 2500.00, 2600.00, 0.00, 0.00, 5100.00, 79, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-28 05:04:34', '2026-10-28 05:04:34', 2, NULL, 2, '2026-10-27 23:34:12', '2026-10-27 23:34:34'),
(53, 1, 'JOB-202610-0008', '2026-10-28', 44, 57, 9, 3400, 8400, 'completed', NULL, 'Rajitha', '071122343', 4500.00, 0.00, 0.00, 0.00, 4500.00, 80, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-28 05:54:54', '2026-10-28 05:54:54', 2, NULL, 2, '2026-10-28 00:24:25', '2026-10-28 00:24:55'),
(54, 1, 'JOB-202610-0009', '2026-10-28', 45, 58, 9, 3400, 6900, 'completed', NULL, NULL, NULL, 34444.00, 0.00, 0.00, 0.00, 34444.00, 81, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-28 05:59:48', '2026-10-28 05:59:48', 2, NULL, 2, '2026-10-28 00:29:23', '2026-10-28 00:29:48'),
(55, 1, 'JOB-202610-0010', '2026-10-28', 46, 59, 10, 4555, 8055, 'pending', NULL, NULL, NULL, 34000.00, 0.00, 0.00, 0.00, 34000.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-10-28 00:30:28', '2026-10-28 00:30:43'),
(56, 1, 'JOB-202610-0011', '2026-10-28', 47, 60, 9, 3400, 8400, 'completed', NULL, 'Kasun', '078556767', 27960.00, 0.00, 0.00, 0.00, 27960.00, 82, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-28 06:03:39', '2026-10-28 06:03:39', 2, NULL, 2, '2026-10-28 00:33:14', '2026-10-28 00:33:39'),
(57, 1, 'JOB-202610-0012', '2026-10-28', 48, 61, 8, 45000, 50000, 'delivered', 2, 'Sunil Shantha', '0779988776', 7500.00, 0.00, 0.00, 0.00, 7500.00, 83, NULL, 'Toyota Hilux 2.8L Diesel Double Cab fleet service.', NULL, NULL, NULL, NULL, '2026-10-28 06:14:28', '2026-10-28 06:14:28', 2, '2026-10-28 00:44:48', 2, '2026-10-28 00:43:54', '2026-10-28 00:44:48'),
(58, 1, 'JOB-202610-0013', '2026-10-28', 49, 64, 10, 0, NULL, 'completed', NULL, NULL, NULL, 0.00, 2600.00, 0.00, 0.00, 2600.00, 86, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-28 07:58:45', '2026-10-28 07:58:45', 2, NULL, 2, '2026-10-28 02:28:29', '2026-10-28 02:28:45');

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

--
-- Dumping data for table `job_card_carpets`
--

INSERT INTO `job_card_carpets` (`id`, `job_card_id`, `carpet_category_id`, `category_name`, `quantity`, `created_at`, `updated_at`) VALUES
(16, 55, 5, 'Front & Rear Mats', 1, '2026-10-28 00:30:28', '2026-10-28 00:30:28'),
(17, 58, 5, 'Front & Rear Mats', 1, '2026-10-28 02:28:29', '2026-10-28 02:28:29');

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
  `is_price_pending` tinyint(1) NOT NULL DEFAULT 0,
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

INSERT INTO `job_card_products` (`id`, `job_card_id`, `product_id`, `product_sku`, `product_name`, `quantity`, `unit_price`, `unit_cost`, `line_total`, `is_price_pending`, `is_stock_deducted`, `is_additional`, `job_issue_id`, `invoice_id`, `batch_allocations`, `notes`, `created_at`, `updated_at`) VALUES
(24, 46, 35, 'OIL-TOY-0W20', 'Toyota Engine Oil 0W-20', 1.00, 18500.00, 14000.00, 18500.00, 0, 1, 0, NULL, NULL, '[{\"batch_id\":40,\"batch_number\":\"B26-0027-001\",\"quantity\":1,\"unit_cost\":14000,\"line_cost\":14000,\"remaining_in_batch\":49}]', NULL, '2026-10-27 17:17:24', '2026-10-27 23:36:03'),
(25, 46, 34, 'FLT-TOY-001', 'Toyota Genuine Engine Oil Filter', 1.00, 2600.00, 1800.00, 2600.00, 0, 1, 0, NULL, NULL, '[{\"batch_id\":41,\"batch_number\":\"B26-0027-002\",\"quantity\":1,\"unit_cost\":1800,\"line_cost\":1800,\"remaining_in_batch\":39}]', NULL, '2026-10-27 17:17:26', '2026-10-27 23:36:03'),
(26, 48, 37, 'BRK-TOY-001', 'Toyota Genuine Brake Pads', 1.00, 12500.00, 9500.00, 12500.00, 0, 1, 0, NULL, NULL, '[{\"batch_id\":42,\"batch_number\":\"B26-0027-003\",\"quantity\":1,\"unit_cost\":9500,\"line_cost\":9500,\"remaining_in_batch\":29}]', NULL, '2026-10-27 17:17:50', '2026-10-27 17:17:50'),
(27, 47, 35, 'OIL-TOY-0W20', 'Toyota Engine Oil 0W-20', 1.00, 18500.00, 14000.00, 18500.00, 0, 1, 0, NULL, NULL, '[{\"batch_id\":40,\"batch_number\":\"B26-0027-001\",\"quantity\":1,\"unit_cost\":14000,\"line_cost\":14000,\"remaining_in_batch\":47}]', NULL, '2026-10-27 18:09:15', '2026-10-27 18:09:15'),
(28, 50, 36, 'FLT-AIR-001', 'Toyota Genuine Air Filter', 1.00, 3800.00, 2500.00, 3800.00, 0, 1, 0, NULL, NULL, '[{\"batch_id\":43,\"batch_number\":\"B26-0027-004\",\"quantity\":1,\"unit_cost\":2500,\"line_cost\":2500,\"remaining_in_batch\":28}]', NULL, '2026-10-27 18:14:56', '2026-10-27 18:14:56'),
(29, 51, 34, 'FLT-TOY-001', 'Toyota Genuine Engine Oil Filter', 1.00, 2600.00, 1800.00, 2600.00, 0, 1, 0, NULL, NULL, '[{\"batch_id\":41,\"batch_number\":\"B26-0027-002\",\"quantity\":1,\"unit_cost\":1800,\"line_cost\":1800,\"remaining_in_batch\":38}]', NULL, '2026-10-27 22:54:16', '2026-10-27 22:54:16'),
(30, 51, 35, 'OIL-TOY-0W20', 'Toyota Engine Oil 0W-20', 1.00, 18500.00, 14000.00, 18500.00, 0, 1, 0, NULL, NULL, '[{\"batch_id\":40,\"batch_number\":\"B26-0027-001\",\"quantity\":1,\"unit_cost\":14000,\"line_cost\":14000,\"remaining_in_batch\":46}]', NULL, '2026-10-27 22:54:20', '2026-10-27 22:54:20'),
(31, 47, 34, 'FLT-TOY-001', 'Toyota Genuine Engine Oil Filter', 1.00, 2600.00, 1800.00, 2600.00, 0, 1, 0, NULL, NULL, '[{\"batch_id\":41,\"batch_number\":\"B26-0027-002\",\"quantity\":1,\"unit_cost\":1800,\"line_cost\":1800,\"remaining_in_batch\":37}]', NULL, '2026-10-27 23:29:38', '2026-10-27 23:29:38'),
(32, 52, 34, 'FLT-TOY-001', 'Toyota Genuine Engine Oil Filter', 1.00, 2600.00, 1800.00, 2600.00, 0, 1, 0, NULL, NULL, '[{\"batch_id\":41,\"batch_number\":\"B26-0027-002\",\"quantity\":1,\"unit_cost\":1800,\"line_cost\":1800,\"remaining_in_batch\":36}]', NULL, '2026-10-27 23:34:20', '2026-10-27 23:34:20'),
(33, 58, 34, 'FLT-TOY-001', 'Toyota Genuine Engine Oil Filter', 1.00, 2600.00, 1800.00, 2600.00, 0, 1, 0, NULL, NULL, '[{\"batch_id\":41,\"batch_number\":\"B26-0027-002\",\"quantity\":1,\"unit_cost\":1800,\"line_cost\":1800,\"remaining_in_batch\":34}]', NULL, '2026-10-28 02:28:34', '2026-10-28 02:28:34');

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
  `is_price_pending` tinyint(1) NOT NULL DEFAULT 0,
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

INSERT INTO `job_card_services` (`id`, `job_card_id`, `service_id`, `service_code`, `service_name`, `unit_price`, `quantity`, `line_total`, `is_price_custom`, `is_price_pending`, `is_additional`, `job_issue_id`, `invoice_id`, `technician_name`, `notes`, `created_at`, `updated_at`) VALUES
(62, 46, 15, 'SRV-001', 'Engine Oil Change', 2500.00, 1.00, 2500.00, 1, 0, 0, NULL, NULL, 'Admin User', NULL, '2026-10-27 17:17:21', '2026-10-27 23:36:03'),
(63, 46, 16, 'SRV-002', 'Oil Filter Replacement', 1500.00, 1.00, 1500.00, 1, 0, 0, NULL, NULL, 'Admin User', NULL, '2026-10-27 17:17:22', '2026-10-27 23:36:03'),
(64, 47, 18, 'SRV-004', 'Engine Diagnostic Scan', 3500.00, 1.00, 3500.00, 1, 0, 0, NULL, NULL, 'Admin User', NULL, '2026-10-27 17:17:39', '2026-10-27 17:25:15'),
(65, 47, 21, 'SRV-007', 'Suspension Inspection', 2000.00, 1.00, 2000.00, 1, 0, 0, NULL, NULL, 'Admin User', NULL, '2026-10-27 17:17:40', '2026-10-27 17:25:19'),
(66, 48, 17, 'SRV-003', 'Brake Pad Replacement', 3000.00, 1.00, 3000.00, 1, 0, 0, NULL, NULL, 'Admin User', NULL, '2026-10-27 17:17:48', '2026-10-27 17:25:26'),
(67, 50, 18, 'SRV-004', 'Engine Diagnostic Scan', 0.00, 1.00, 0.00, 0, 1, 0, NULL, NULL, NULL, NULL, '2026-10-27 18:14:17', '2026-10-27 18:14:17'),
(68, 51, 15, 'SRV-001', 'Engine Oil Change', 2500.00, 1.00, 2500.00, 1, 0, 0, NULL, NULL, NULL, NULL, '2026-10-27 22:53:56', '2026-10-27 22:54:05'),
(69, 51, 16, 'SRV-002', 'Oil Filter Replacement', 1500.00, 1.00, 1500.00, 1, 0, 0, NULL, NULL, NULL, NULL, '2026-10-27 22:54:00', '2026-10-27 22:54:10'),
(70, 47, 15, 'SRV-001', 'Engine Oil Change', 0.00, 1.00, 0.00, 0, 1, 0, NULL, NULL, 'Admin User', NULL, '2026-10-27 23:29:35', '2026-10-27 23:29:35'),
(71, 52, 15, 'SRV-001', 'Engine Oil Change', 2500.00, 1.00, 2500.00, 1, 0, 0, NULL, NULL, NULL, NULL, '2026-10-27 23:34:17', '2026-10-27 23:34:25'),
(72, 53, 18, 'SRV-004', 'Engine Diagnostic Scan', 4500.00, 1.00, 4500.00, 1, 0, 0, NULL, NULL, NULL, NULL, '2026-10-28 00:24:34', '2026-10-28 00:24:44'),
(73, 54, 16, 'SRV-002', 'Oil Filter Replacement', 34444.00, 1.00, 34444.00, 1, 0, 0, NULL, NULL, 'Admin User', NULL, '2026-10-28 00:29:37', '2026-10-28 00:29:45'),
(74, 55, 21, 'SRV-007', 'Suspension Inspection', 34000.00, 1.00, 34000.00, 1, 0, 0, NULL, NULL, NULL, NULL, '2026-10-28 00:30:37', '2026-10-28 00:30:43'),
(75, 56, 19, 'SRV-005', 'Air Conditioning Service', 23400.00, 1.00, 23400.00, 1, 0, 0, NULL, NULL, NULL, NULL, '2026-10-28 00:33:17', '2026-10-28 00:33:31'),
(76, 56, 15, 'SRV-001', 'Engine Oil Change', 4560.00, 1.00, 4560.00, 1, 0, 0, NULL, NULL, NULL, NULL, '2026-10-28 00:33:18', '2026-10-28 00:33:25'),
(77, 57, 15, 'SRV-001', 'Engine Oil Change', 2500.00, 1.00, 2500.00, 1, 0, 0, NULL, NULL, NULL, NULL, '2026-10-28 00:43:59', '2026-10-28 00:44:11'),
(78, 57, 18, 'SRV-004', 'Engine Diagnostic Scan', 3500.00, 1.00, 3500.00, 1, 0, 0, NULL, NULL, NULL, NULL, '2026-10-28 00:44:01', '2026-10-28 00:44:14'),
(79, 57, 21, 'SRV-007', 'Suspension Inspection', 1500.00, 1.00, 1500.00, 1, 0, 0, NULL, NULL, NULL, NULL, '2026-10-28 00:44:04', '2026-10-28 00:44:18');

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
(53, '2026_10_21_220000_add_payment_tracking_to_expenses_table', 17),
(54, '2026_10_22_072000_create_printer_settings_and_logs_tables', 18),
(55, '2026_10_22_043812_add_signature_path_to_company_settings_table', 19),
(56, '2026_10_22_120000_update_direct_sales_and_payments_for_sales_bill', 20),
(57, '2026_10_22_130000_add_company_vehicle_and_driver_columns', 21),
(58, '2026_10_23_150000_create_grn_sequences_table', 22),
(59, '2026_10_26_085854_add_price_pending_and_completed_by_columns', 23),
(60, '2026_10_27_074754_add_salary_settings_to_employees_and_wage_rates_tables', 24),
(61, '2026_10_27_083000_create_company_holidays_tables', 25),
(62, '2026_10_27_090000_add_off_days_and_no_pay_to_payroll_items_table', 25),
(63, '2026_10_27_093000_create_salary_advance_management_tables', 25),
(64, '2026_10_27_103000_create_bonuses_allowances_overtime_tables', 26),
(65, '2026_10_27_120000_create_statutory_and_tax_support_tables', 27),
(66, '2026_10_28_120000_make_customer_and_vehicle_optional_on_billing_tables', 28);

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
  `direct_sale_id` bigint(20) UNSIGNED DEFAULT NULL,
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

INSERT INTO `payments` (`id`, `invoice_id`, `job_card_id`, `direct_sale_id`, `customer_id`, `payment_receipt_id`, `payment_number`, `payment_date`, `amount`, `payment_method`, `reference_number`, `notes`, `is_advance`, `status`, `reversed_at`, `reversal_reason`, `created_by`, `created_at`, `updated_at`) VALUES
(64, NULL, 46, NULL, 38, NULL, 'PAY-202610-0001', '2026-10-27', 10000.00, 'cash', 'REC-PARTIAL-001', '[Transferred to Job Advance upon reopening Job #JOB-202610-0001. Orig Invoice: INV-202610-0001]', 1, 'completed', NULL, NULL, 2, '2026-10-27 17:29:51', '2026-10-27 23:36:03'),
(65, 76, NULL, NULL, 39, NULL, 'PAY-202610-0002', '2026-10-27', 5500.00, 'bank_transfer', 'BANK-TXN-99812', NULL, 0, 'completed', NULL, NULL, 2, '2026-10-27 17:30:00', '2026-10-27 17:30:00'),
(66, NULL, NULL, 42, 43, NULL, 'PAY-202610-0003', '2026-10-27', 3000.00, 'cash', 'BILL-DS-202610-0001', 'POS Checkout for Bill #BILL-DS-202610-0001', 0, 'completed', NULL, NULL, 2, '2026-10-27 17:34:10', '2026-10-27 17:34:10'),
(67, NULL, NULL, 43, 43, NULL, 'PAY-202610-0004', '2026-10-27', 3800.00, 'cash', 'BILL-DS-202610-0002', 'POS Checkout for Bill #BILL-DS-202610-0002', 0, 'completed', NULL, NULL, 2, '2026-10-27 17:35:14', '2026-10-27 17:35:14'),
(68, 77, 51, NULL, 38, NULL, 'PAY-202610-0005', '2026-10-28', 25100.00, 'cash', NULL, 'Simulated cash payment - Live QA E2E Test', 0, 'completed', NULL, NULL, 2, '2026-10-27 22:55:57', '2026-10-27 22:55:57'),
(69, 78, 48, NULL, 40, NULL, 'PAY-202610-0006', '2026-10-28', 5000.00, 'cash', NULL, NULL, 0, 'completed', NULL, NULL, 2, '2026-10-27 23:25:10', '2026-10-27 23:25:10'),
(70, 78, 48, NULL, 40, NULL, 'PAY-202610-0007', '2026-10-28', 500.00, 'cash', NULL, NULL, 0, 'completed', NULL, NULL, 2, '2026-10-27 23:25:27', '2026-10-27 23:25:27'),
(71, NULL, NULL, 44, 43, NULL, 'PAY-202610-0008', '2026-10-28', 2600.00, 'cash', 'BILL-DS-202610-0003', 'POS Checkout for Bill #BILL-DS-202610-0003', 0, 'completed', NULL, NULL, 2, '2026-10-27 23:35:20', '2026-10-27 23:35:20'),
(72, 78, 48, NULL, 40, NULL, 'PAY-202610-0009', '2026-10-28', 1000.00, 'cash', NULL, 'QA Test Partial Payment LKR 1000', 0, 'completed', NULL, NULL, 2, '2026-10-27 23:58:16', '2026-10-27 23:58:16'),
(73, 79, 52, NULL, 38, NULL, 'PAY-202610-0010', '2026-10-28', 5100.00, 'cash', NULL, NULL, 0, 'completed', NULL, NULL, 2, '2026-10-27 23:58:31', '2026-10-27 23:58:31'),
(74, 78, 48, NULL, 40, NULL, 'PAY-202610-0011', '2026-10-28', 1000.00, 'cash', NULL, 'Duplicate Submission Test', 0, 'completed', NULL, NULL, 2, '2026-10-27 23:59:11', '2026-10-27 23:59:11'),
(75, 78, 48, NULL, 40, NULL, 'PAY-202610-0012', '2026-10-28', 500.00, 'cash', NULL, 'First payment 500', 0, 'completed', NULL, NULL, 2, '2026-10-28 00:00:20', '2026-10-28 00:00:20'),
(76, 78, 48, NULL, 40, NULL, 'PAY-202610-0013', '2026-10-28', 7500.00, 'cash', NULL, 'Final Full Settlement Payment', 0, 'completed', NULL, NULL, 2, '2026-10-28 00:01:36', '2026-10-28 00:01:36'),
(77, 75, NULL, NULL, 41, NULL, 'PAY-202610-0014', '2026-10-28', 800.00, 'cash', NULL, NULL, 0, 'completed', NULL, NULL, 2, '2026-10-28 00:05:25', '2026-10-28 00:05:25'),
(78, 80, 53, NULL, 44, NULL, 'PAY-202610-0015', '2026-10-28', 4500.00, 'cash', NULL, NULL, 0, 'completed', NULL, NULL, 2, '2026-10-28 00:31:06', '2026-10-28 00:31:06'),
(79, 83, 57, NULL, 48, NULL, 'PAY-202610-0016', '2026-10-28', 7500.00, 'bank_transfer', 'BT-LANKA-202610-01', 'Bank Transfer settlement via Lanka Express Logistics corporate account.', 0, 'completed', NULL, NULL, 2, '2026-10-28 00:44:41', '2026-10-28 00:44:41'),
(80, 84, NULL, NULL, 49, NULL, 'PAY-202610-0017', '2026-10-28', 2300.00, 'cash', NULL, NULL, 0, 'completed', NULL, NULL, 2, '2026-10-28 02:23:35', '2026-10-28 02:23:35'),
(81, 86, 58, NULL, 49, NULL, 'PAY-202610-0018', '2026-10-28', 2600.00, 'cash', NULL, NULL, 0, 'completed', NULL, NULL, 2, '2026-10-28 02:28:51', '2026-10-28 02:28:51');

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
  `salary_type_snapshot` varchar(20) NOT NULL DEFAULT 'daily',
  `monthly_basic_snapshot` decimal(12,2) NOT NULL DEFAULT 0.00,
  `full_days_count` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `half_days_count` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `absent_days_count` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `leave_days_count` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `day_off_days_count` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `company_off_days_count` decimal(4,2) NOT NULL DEFAULT 0.00,
  `paid_holidays_count` decimal(4,2) NOT NULL DEFAULT 0.00,
  `approved_leave_count` decimal(4,2) NOT NULL DEFAULT 0.00,
  `no_pay_days_count` decimal(4,2) NOT NULL DEFAULT 0.00,
  `payable_days` decimal(5,2) NOT NULL DEFAULT 0.00,
  `base_earnings` decimal(12,2) NOT NULL DEFAULT 0.00,
  `overtime_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `bonus_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `allowances_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `deductions` decimal(12,2) NOT NULL DEFAULT 0.00,
  `no_pay_deduction` decimal(12,2) NOT NULL DEFAULT 0.00,
  `epf_qualifying_earnings` decimal(10,2) NOT NULL DEFAULT 0.00,
  `etf_qualifying_earnings` decimal(10,2) NOT NULL DEFAULT 0.00,
  `apit_taxable_earnings` decimal(10,2) NOT NULL DEFAULT 0.00,
  `epf_employee_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `epf_employer_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `etf_employer_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `apit_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
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

INSERT INTO `payroll_items` (`id`, `payroll_period_id`, `employee_id`, `employee_name_snapshot`, `daily_wage_snapshot`, `salary_type_snapshot`, `monthly_basic_snapshot`, `full_days_count`, `half_days_count`, `absent_days_count`, `leave_days_count`, `day_off_days_count`, `company_off_days_count`, `paid_holidays_count`, `approved_leave_count`, `no_pay_days_count`, `payable_days`, `base_earnings`, `overtime_amount`, `bonus_amount`, `allowances_amount`, `deductions`, `no_pay_deduction`, `epf_qualifying_earnings`, `etf_qualifying_earnings`, `apit_taxable_earnings`, `epf_employee_amount`, `epf_employer_amount`, `etf_employer_amount`, `apit_amount`, `advances_applied`, `gross_salary`, `net_salary`, `paid_amount`, `balance_due`, `status`, `attendance_breakdown`, `notes`, `created_at`, `updated_at`) VALUES
(17, 4, 14, 'Kamal Ranjith', 0.00, 'monthly', 85000.00, 1, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 31.00, 85000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 85000.00, 85000.00, 85000.00, 6800.00, 10200.00, 2550.00, 0.00, 0.00, 85000.00, 78200.00, 0.00, 78200.00, 'draft', '{\"summary\":{\"employee_id\":14,\"employee_name\":\"Kamal Ranjith\",\"employee_number\":\"EMP-0001\",\"salary_type\":\"monthly\",\"daily_wage\":0,\"monthly_basic_salary\":85000,\"is_eligible_for_off_days\":true,\"days_in_month\":31,\"no_pay_divisor\":31,\"no_pay_divisor_type\":\"calendar_days\",\"full_days_count\":1,\"half_days_count\":0,\"absent_days_count\":0,\"explicit_day_off_count\":0,\"paid_leave_count\":0,\"unpaid_leave_count\":0,\"leave_days_count\":0,\"approved_leave_count\":0,\"missing_attendance_count\":4,\"statutory_rest_days_count\":0,\"paid_holidays_count\":0,\"unpaid_holidays_count\":0,\"total_absence_taken\":0,\"company_off_days_used\":0,\"no_pay_days_count\":0,\"no_pay_daily_rate\":2741.94,\"no_pay_deduction\":0,\"payable_days\":31,\"earned_attendance_wage\":0,\"paid_holiday_wage\":0,\"base_earnings\":85000,\"overtime_amount\":0,\"bonus_amount\":0,\"allowances_amount\":0,\"epf_qualifying_salary\":85000,\"etf_qualifying_salary\":85000,\"apit_taxable_earnings\":85000,\"apit_amount\":0},\"reconciled_days\":[{\"date\":\"2026-10-01\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-02\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-03\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-04\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-05\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-06\",\"day_name\":\"Tue\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-07\",\"day_name\":\"Wed\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-08\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-09\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-10\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-11\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-12\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-13\",\"day_name\":\"Tue\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-14\",\"day_name\":\"Wed\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-15\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-16\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-17\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-18\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-19\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-20\",\"day_name\":\"Tue\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-21\",\"day_name\":\"Wed\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-22\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-23\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-24\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-25\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-26\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-27\",\"day_name\":\"Tue\",\"status\":\"full_day\",\"category\":\"Worked Full Day\",\"fraction\":1,\"wage\":0,\"earnings\":0,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-28\",\"day_name\":\"Wed\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-29\",\"day_name\":\"Thu\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-30\",\"day_name\":\"Fri\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-31\",\"day_name\":\"Sat\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"holiday_name\":null,\"notes\":null}],\"planned_advance_recoveries\":[],\"bonuses_breakdown\":[],\"allowances_breakdown\":[],\"overtime_breakdown\":[],\"statutory_breakdown\":{\"epf_qualifying_salary\":85000,\"etf_qualifying_salary\":85000,\"epf_employee_rate\":8,\"epf_employer_rate\":12,\"etf_employer_rate\":3,\"epf_employee_amount\":6800,\"epf_employer_amount\":10200,\"etf_employer_amount\":2550,\"apit_taxable_earnings\":85000,\"apit_tax_relief\":150000,\"apit_taxable_income\":0,\"apit_amount\":0,\"apit_slabs_breakdown\":[],\"flagged_for_admin_review\":[]}}', NULL, '2026-10-27 06:21:30', '2026-10-27 06:21:30'),
(18, 4, 15, 'Nimal Perera', 0.00, 'monthly', 75000.00, 0, 1, 0, 0, 1, 0.50, 0.00, 0.00, 0.00, 31.00, 75000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 75000.00, 75000.00, 75000.00, 6000.00, 9000.00, 2250.00, 0.00, 0.00, 75000.00, 69000.00, 0.00, 69000.00, 'draft', '{\"summary\":{\"employee_id\":15,\"employee_name\":\"Nimal Perera\",\"employee_number\":\"EMP-0002\",\"salary_type\":\"monthly\",\"daily_wage\":0,\"monthly_basic_salary\":75000,\"is_eligible_for_off_days\":true,\"days_in_month\":31,\"no_pay_divisor\":31,\"no_pay_divisor_type\":\"calendar_days\",\"full_days_count\":0,\"half_days_count\":1,\"absent_days_count\":0,\"explicit_day_off_count\":0,\"paid_leave_count\":0,\"unpaid_leave_count\":0,\"leave_days_count\":0,\"approved_leave_count\":0,\"missing_attendance_count\":4,\"statutory_rest_days_count\":0,\"paid_holidays_count\":0,\"unpaid_holidays_count\":0,\"total_absence_taken\":0.5,\"company_off_days_used\":0.5,\"no_pay_days_count\":0,\"no_pay_daily_rate\":2419.35,\"no_pay_deduction\":0,\"payable_days\":31,\"earned_attendance_wage\":0,\"paid_holiday_wage\":0,\"base_earnings\":75000,\"overtime_amount\":0,\"bonus_amount\":0,\"allowances_amount\":0,\"epf_qualifying_salary\":75000,\"etf_qualifying_salary\":75000,\"apit_taxable_earnings\":75000,\"apit_amount\":0},\"reconciled_days\":[{\"date\":\"2026-10-01\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-02\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-03\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-04\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-05\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-06\",\"day_name\":\"Tue\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-07\",\"day_name\":\"Wed\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-08\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-09\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-10\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-11\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-12\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-13\",\"day_name\":\"Tue\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-14\",\"day_name\":\"Wed\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-15\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-16\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-17\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-18\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-19\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-20\",\"day_name\":\"Tue\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-21\",\"day_name\":\"Wed\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-22\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-23\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-24\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-25\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-26\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-27\",\"day_name\":\"Tue\",\"status\":\"half_day\",\"category\":\"Worked Half Day\",\"fraction\":0.5,\"wage\":0,\"earnings\":0,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-28\",\"day_name\":\"Wed\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-29\",\"day_name\":\"Thu\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-30\",\"day_name\":\"Fri\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-31\",\"day_name\":\"Sat\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"holiday_name\":null,\"notes\":null}],\"planned_advance_recoveries\":[],\"bonuses_breakdown\":[],\"allowances_breakdown\":[],\"overtime_breakdown\":[],\"statutory_breakdown\":{\"epf_qualifying_salary\":75000,\"etf_qualifying_salary\":75000,\"epf_employee_rate\":8,\"epf_employer_rate\":12,\"etf_employer_rate\":3,\"epf_employee_amount\":6000,\"epf_employer_amount\":9000,\"etf_employer_amount\":2250,\"apit_taxable_earnings\":75000,\"apit_tax_relief\":150000,\"apit_taxable_income\":0,\"apit_amount\":0,\"apit_slabs_breakdown\":[],\"flagged_for_admin_review\":[]}}', NULL, '2026-10-27 06:21:30', '2026-10-27 06:21:30'),
(19, 4, 16, 'Saman Kumara', 3000.00, 'daily', 0.00, 1, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 1.00, 3000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 3000.00, 3000.00, 3000.00, 240.00, 360.00, 90.00, 0.00, 0.00, 3000.00, 2760.00, 0.00, 2760.00, 'draft', '{\"summary\":{\"employee_id\":16,\"employee_name\":\"Saman Kumara\",\"employee_number\":\"EMP-0003\",\"salary_type\":\"daily\",\"daily_wage\":3000,\"monthly_basic_salary\":0,\"is_eligible_for_off_days\":true,\"days_in_month\":31,\"no_pay_divisor\":31,\"no_pay_divisor_type\":\"calendar_days\",\"full_days_count\":1,\"half_days_count\":0,\"absent_days_count\":0,\"explicit_day_off_count\":0,\"paid_leave_count\":0,\"unpaid_leave_count\":0,\"leave_days_count\":0,\"approved_leave_count\":0,\"missing_attendance_count\":4,\"statutory_rest_days_count\":0,\"paid_holidays_count\":0,\"unpaid_holidays_count\":0,\"total_absence_taken\":0,\"company_off_days_used\":0,\"no_pay_days_count\":0,\"no_pay_daily_rate\":3000,\"no_pay_deduction\":0,\"payable_days\":1,\"earned_attendance_wage\":3000,\"paid_holiday_wage\":0,\"base_earnings\":3000,\"overtime_amount\":0,\"bonus_amount\":0,\"allowances_amount\":0,\"epf_qualifying_salary\":3000,\"etf_qualifying_salary\":3000,\"apit_taxable_earnings\":3000,\"apit_amount\":0},\"reconciled_days\":[{\"date\":\"2026-10-01\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-02\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-03\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-04\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-05\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-06\",\"day_name\":\"Tue\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-07\",\"day_name\":\"Wed\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-08\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-09\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-10\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-11\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-12\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-13\",\"day_name\":\"Tue\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-14\",\"day_name\":\"Wed\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-15\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-16\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-17\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-18\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-19\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-20\",\"day_name\":\"Tue\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-21\",\"day_name\":\"Wed\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-22\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-23\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-24\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-25\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-26\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-27\",\"day_name\":\"Tue\",\"status\":\"full_day\",\"category\":\"Worked Full Day\",\"fraction\":1,\"wage\":3000,\"earnings\":3000,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-28\",\"day_name\":\"Wed\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":3000,\"earnings\":0,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-29\",\"day_name\":\"Thu\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":3000,\"earnings\":0,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-30\",\"day_name\":\"Fri\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":3000,\"earnings\":0,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-31\",\"day_name\":\"Sat\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":3000,\"earnings\":0,\"holiday_name\":null,\"notes\":null}],\"planned_advance_recoveries\":[],\"bonuses_breakdown\":[],\"allowances_breakdown\":[],\"overtime_breakdown\":[],\"statutory_breakdown\":{\"epf_qualifying_salary\":3000,\"etf_qualifying_salary\":3000,\"epf_employee_rate\":8,\"epf_employer_rate\":12,\"etf_employer_rate\":3,\"epf_employee_amount\":240,\"epf_employer_amount\":360,\"etf_employer_amount\":90,\"apit_taxable_earnings\":3000,\"apit_tax_relief\":150000,\"apit_taxable_income\":0,\"apit_amount\":0,\"apit_slabs_breakdown\":[],\"flagged_for_admin_review\":[]}}', NULL, '2026-10-27 06:21:30', '2026-10-27 06:21:30');

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
  `total_epf_employee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_epf_employer` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_etf_employer` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_apit` decimal(12,2) NOT NULL DEFAULT 0.00,
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

INSERT INTO `payroll_periods` (`id`, `year`, `month`, `status`, `total_base_earnings`, `total_overtime`, `total_bonuses`, `total_epf_employee`, `total_epf_employer`, `total_etf_employer`, `total_apit`, `total_deductions`, `total_advances`, `total_gross`, `total_net`, `total_paid`, `notes`, `approved_by`, `approved_at`, `created_at`, `updated_at`) VALUES
(4, 2026, 10, 'draft', 163000.00, 0.00, 0.00, 13040.00, 19560.00, 4890.00, 0.00, 13040.00, 0.00, 163000.00, 149960.00, 0.00, NULL, NULL, NULL, '2026-10-27 06:21:30', '2026-10-27 06:21:30');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_statutory_settings`
--

CREATE TABLE `payroll_statutory_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `epf_employee_rate` decimal(5,2) NOT NULL DEFAULT 8.00,
  `epf_employer_rate` decimal(5,2) NOT NULL DEFAULT 12.00,
  `etf_employer_rate` decimal(5,2) NOT NULL DEFAULT 3.00,
  `no_pay_divisor_type` varchar(30) NOT NULL DEFAULT 'calendar_days',
  `no_pay_custom_divisor` int(10) UNSIGNED DEFAULT NULL,
  `overtime_normal_multiplier` decimal(4,2) NOT NULL DEFAULT 1.50,
  `overtime_holiday_multiplier` decimal(4,2) NOT NULL DEFAULT 2.00,
  `overtime_monthly_divisor_hours` decimal(5,2) NOT NULL DEFAULT 200.00,
  `apit_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `apit_monthly_relief` decimal(12,2) NOT NULL DEFAULT 150000.00,
  `apit_tax_slabs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`apit_tax_slabs`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payroll_statutory_settings`
--

INSERT INTO `payroll_statutory_settings` (`id`, `effective_from`, `effective_to`, `epf_employee_rate`, `epf_employer_rate`, `etf_employer_rate`, `no_pay_divisor_type`, `no_pay_custom_divisor`, `overtime_normal_multiplier`, `overtime_holiday_multiplier`, `overtime_monthly_divisor_hours`, `apit_enabled`, `apit_monthly_relief`, `apit_tax_slabs`, `is_active`, `notes`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, '2023-01-01', '2025-03-31', 8.00, 12.00, 3.00, 'calendar_days', NULL, 1.50, 2.00, 200.00, 1, 100000.00, '[{\"slab\":1,\"limit\":41666.67,\"rate\":6},{\"slab\":2,\"limit\":41666.67,\"rate\":12},{\"slab\":3,\"limit\":41666.67,\"rate\":18},{\"slab\":4,\"limit\":41666.67,\"rate\":24},{\"slab\":5,\"limit\":41666.67,\"rate\":30},{\"slab\":6,\"limit\":null,\"rate\":36}]', 1, 'Sri Lanka Inland Revenue Department (IRD) APIT Table 01 (Pre-April 2025, YA 2023/24 - 2024/25)', NULL, NULL, '2026-10-27 05:24:31', '2026-10-27 05:24:31'),
(2, '2025-04-01', NULL, 8.00, 12.00, 3.00, 'calendar_days', NULL, 1.50, 2.00, 200.00, 1, 150000.00, '[{\"slab\":1,\"limit\":83333.33,\"rate\":6},{\"slab\":2,\"limit\":41666.67,\"rate\":18},{\"slab\":3,\"limit\":41666.67,\"rate\":24},{\"slab\":4,\"limit\":41666.67,\"rate\":30},{\"slab\":5,\"limit\":null,\"rate\":36}]', 1, 'Sri Lanka Inland Revenue Department (IRD) APIT Table 01 (Effective April 1, 2025 onwards, YA 2025/26)', NULL, NULL, '2026-10-27 05:24:31', '2026-10-27 05:24:31');

-- --------------------------------------------------------

--
-- Table structure for table `printer_settings`
--

CREATE TABLE `printer_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `workstation_name` varchar(100) NOT NULL DEFAULT 'Default-Workstation',
  `bill_printer` varchar(255) DEFAULT NULL,
  `job_card_printer` varchar(255) DEFAULT NULL,
  `paper_size` varchar(50) NOT NULL DEFAULT '80mm',
  `auto_route` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `print_logs`
--

CREATE TABLE `print_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `workstation_name` varchar(100) DEFAULT NULL,
  `document_type` varchar(50) NOT NULL,
  `document_id` bigint(20) UNSIGNED DEFAULT NULL,
  `document_number` varchar(100) DEFAULT NULL,
  `printer_name` varchar(255) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'submitted',
  `error_message` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `print_logs`
--

INSERT INTO `print_logs` (`id`, `user_id`, `branch_id`, `workstation_name`, `document_type`, `document_id`, `document_number`, `printer_name`, `status`, `error_message`, `ip_address`, `created_at`, `updated_at`) VALUES
(43, 2, NULL, 'Workstation-XWFK', 'bill', 78, 'INV-202610-0012', NULL, 'failed', 'Bill Printer is not configured for this workstation.', '127.0.0.1', '2026-10-27 23:23:20', '2026-10-27 23:23:20'),
(44, 2, NULL, 'Workstation-XWFK', 'payment_receipt', 69, 'PAY-202610-0006', NULL, 'failed', 'Bill Printer is not configured for this workstation.', '127.0.0.1', '2026-10-27 23:25:50', '2026-10-27 23:25:50'),
(45, 2, NULL, 'Workstation-XWFK', 'payment_receipt', 70, 'PAY-202610-0007', NULL, 'failed', 'Bill Printer is not configured for this workstation.', '127.0.0.1', '2026-10-27 23:26:16', '2026-10-27 23:26:16'),
(46, 2, NULL, 'Workstation-ZOR6', 'bill', 88, 'INV-202610-0078', NULL, 'failed', 'Bill Printer is not configured for this workstation.', '127.0.0.1', '2026-10-28 02:46:32', '2026-10-28 02:46:32');

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
(34, 'FLT-TOY-001', 'Toyota Genuine Engine Oil Filter', NULL, 4, NULL, 5.00, NULL, 2600.00, NULL, 1, '2026-10-27 06:45:05', '2026-10-27 06:45:05'),
(35, 'OIL-TOY-0W20', 'Toyota Engine Oil 0W-20', NULL, 2, NULL, 5.00, NULL, 18500.00, NULL, 1, '2026-10-27 06:45:10', '2026-10-27 06:45:10'),
(36, 'FLT-AIR-001', 'Toyota Genuine Air Filter', NULL, 4, NULL, 5.00, NULL, 3800.00, NULL, 1, '2026-10-27 06:45:16', '2026-10-27 06:45:16'),
(37, 'BRK-TOY-001', 'Toyota Genuine Brake Pads', NULL, 4, NULL, 5.00, NULL, 12500.00, NULL, 1, '2026-10-27 06:45:22', '2026-10-27 06:45:22'),
(38, 'LUB-CLT-001', 'Toyota Long Life Coolant', NULL, 1, NULL, 5.00, NULL, 6200.00, NULL, 1, '2026-10-27 06:45:28', '2026-10-27 06:45:28'),
(39, 'IGN-SPK-001', 'Denso Spark Plug', NULL, 4, NULL, 5.00, NULL, 2400.00, NULL, 1, '2026-10-27 06:45:34', '2026-10-27 06:45:34'),
(40, 'ACC-WPR-001', 'Bosch Wiper Blade', NULL, 4, NULL, 5.00, NULL, 3200.00, NULL, 1, '2026-10-27 06:45:39', '2026-10-27 06:45:39'),
(41, 'IGN-SPK-002', 'NGK Spark Plug', NULL, 4, NULL, 5.00, NULL, 2200.00, NULL, 1, '2026-10-27 06:45:45', '2026-10-27 06:45:45'),
(42, 'JS1025', 'TVS Light', NULL, 4, 16, 5.00, 34.00, 30000.00, NULL, 1, '2026-10-28 00:35:24', '2026-10-28 00:35:24');

-- --------------------------------------------------------

--
-- Table structure for table `quotations`
--

CREATE TABLE `quotations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
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
(11, 1, 38, 52, 'QT-202610-0001', '2026-10-27', '2026-11-10', 'Periodic Engine Oil & Filter Service', NULL, 23600.00, 'none', 0.00, 0.00, 0.00, 0.00, 0.00, 23600.00, 'draft', NULL, 'Quotation valid for 14 days. Parts subject to supplier market availability. Service labor warranty 30 days.', NULL, NULL, '{\"name\":\"Ruwan Silva\",\"phone\":\"0771122334\",\"email\":\"ruwan.silva@example.local\",\"customer_type\":\"individual\",\"company_name\":null,\"tax_number\":null,\"address\":\"45 Galle Road, Colombo\"}', '{\"vehicle_number\":\"WP CAK-4512\",\"make_model\":\"Toyota Aqua 2015\",\"vehicle_type\":\"Car \\/ Sedan\",\"current_mileage\":65000}', '{\"company_name\":\"Viran Auto Care\",\"registration_number\":\"PV-2026-98765\",\"address\":\"No. 10\\/B, Mattegoda Road, Mattegoda 10240\",\"phone\":\"077 620 80 59\",\"email\":\"info@autocare.local\",\"tax_number\":\"VAT-123456789-000\"}', 2, '2026-10-27 17:13:58', '2026-10-27 17:13:58'),
(12, 1, 39, 53, 'QT-202610-0002', '2026-10-27', '2026-11-10', 'Engine Diagnostic and Inspection', NULL, 5500.00, 'none', 0.00, 0.00, 0.00, 0.00, 0.00, 5500.00, 'converted', NULL, 'Quotation valid for 14 days. Parts subject to supplier market availability. Service labor warranty 30 days.', 76, NULL, '{\"name\":\"Dinesh Perera\",\"phone\":\"0772233445\",\"email\":\"dinesh.perera@example.local\",\"customer_type\":\"individual\",\"company_name\":null,\"tax_number\":null,\"address\":\"82 Kandy Road, Kiribathgoda\"}', '{\"vehicle_number\":\"WP CAA-8834\",\"make_model\":\"Toyota Prius 2016\",\"vehicle_type\":\"Car \\/ Sedan\",\"current_mileage\":82000}', '{\"company_name\":\"Viran Auto Care\",\"registration_number\":\"PV-2026-98765\",\"address\":\"No. 10\\/B, Mattegoda Road, Mattegoda 10240\",\"phone\":\"077 620 80 59\",\"email\":\"info@autocare.local\",\"tax_number\":\"VAT-123456789-000\"}', 2, '2026-10-27 17:14:11', '2026-10-27 17:29:39'),
(13, 1, 40, 54, 'QT-202610-0003', '2026-10-27', '2026-11-10', 'Brake System Maintenance Estimate', NULL, 15500.00, 'none', 0.00, 0.00, 0.00, 0.00, 0.00, 15500.00, 'draft', NULL, 'Quotation valid for 14 days. Parts subject to supplier market availability. Service labor warranty 30 days.', NULL, NULL, '{\"name\":\"Chamara Wijesinghe\",\"phone\":\"0773344556\",\"email\":\"chamara.w@example.local\",\"customer_type\":\"individual\",\"company_name\":null,\"tax_number\":null,\"address\":\"19 High Level Road, Maharagama\"}', '{\"vehicle_number\":\"WP CAB-1902\",\"make_model\":\"Toyota Axio 2014\",\"vehicle_type\":\"Car \\/ Sedan\",\"current_mileage\":94000}', '{\"company_name\":\"Viran Auto Care\",\"registration_number\":\"PV-2026-98765\",\"address\":\"No. 10\\/B, Mattegoda Road, Mattegoda 10240\",\"phone\":\"077 620 80 59\",\"email\":\"info@autocare.local\",\"tax_number\":\"VAT-123456789-000\"}', 2, '2026-10-27 17:14:23', '2026-10-27 17:14:23'),
(14, 1, 39, 60, 'QT-202610-0004', '2026-10-28', '2026-11-11', 'Service Quotation for ND-23434', NULL, 9000.00, 'none', 0.00, 0.00, 0.00, 0.00, 0.00, 9000.00, 'sent', NULL, 'Quotation valid for 14 days. Parts subject to supplier market availability. Service labor warranty 30 days.', NULL, NULL, '{\"name\":\"Dinesh Perera\",\"phone\":\"0772233445\",\"email\":\"dinesh.perera@example.local\",\"customer_type\":\"individual\",\"company_name\":null,\"tax_number\":null,\"address\":\"82 Kandy Road, Kiribathgoda\"}', '{\"vehicle_number\":\"ND-23434\",\"make_model\":\"Toyota KDH\",\"vehicle_type\":\"Van\",\"current_mileage\":3400}', '{\"company_name\":\"Viran Auto Care\",\"registration_number\":\"PV-2026-98765\",\"address\":\"No. 10\\/B, Mattegoda Road, Mattegoda 10240\",\"phone\":\"077 620 80 59\",\"email\":\"info@autocare.local\",\"tax_number\":\"VAT-123456789-000\"}', 2, '2026-10-28 00:41:25', '2026-10-28 00:41:51'),
(15, 1, 49, 63, 'QT-202610-0005', '2026-10-28', '2026-11-11', 'Service Quotation for WE-2333', NULL, 23330.00, 'none', 0.00, 0.00, 0.00, 0.00, 0.00, 23330.00, 'converted', NULL, 'Quotation valid for 14 days. Parts subject to supplier market availability. Service labor warranty 30 days.', 85, NULL, '{\"name\":\"Shehan\",\"phone\":\"0714624000\",\"email\":null,\"customer_type\":\"individual\",\"company_name\":null,\"tax_number\":null,\"address\":null,\"is_manual\":false}', '{\"vehicle_number\":\"WE-2333\",\"make_model\":null,\"vehicle_type\":\"Car \\/ Sedan\",\"current_mileage\":0,\"is_registered\":true}', '{\"company_name\":\"Viran Auto Care\",\"registration_number\":\"PV-2026-98765\",\"address\":\"No. 10\\/B, Mattegoda Road, Mattegoda 10240\",\"phone\":\"077 620 80 59\",\"email\":\"info@autocare.local\",\"tax_number\":\"VAT-123456789-000\"}', 2, '2026-10-28 02:24:23', '2026-10-28 02:24:30'),
(16, 1, 44, 57, 'QT-202610-0006', '2026-10-28', '2026-11-11', 'Service Quotation for ND-2343', NULL, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'draft', NULL, 'Quotation valid for 14 days. Parts subject to supplier market availability. Service labor warranty 30 days.', NULL, NULL, '{\"name\":\"Shashika\",\"phone\":\"071234567\",\"email\":\"Sashika@gmail.com\",\"customer_type\":\"company\",\"company_name\":\"Lanka Express Logistics s (Pvt) Ltd\",\"tax_number\":null,\"address\":\"Maharagama\",\"is_manual\":false}', '{\"vehicle_number\":\"ND-2343\",\"make_model\":\"Toyota KDH\",\"vehicle_type\":\"Van\",\"current_mileage\":3400,\"is_registered\":true}', '{\"company_name\":\"Viran Auto Care\",\"registration_number\":\"PV-2026-98765\",\"address\":\"No. 10\\/B, Mattegoda Road, Mattegoda 10240\",\"phone\":\"077 620 80 59\",\"email\":\"info@autocare.local\",\"tax_number\":\"VAT-123456789-000\"}', 2, '2026-10-28 02:41:16', '2026-10-28 02:41:16'),
(17, 1, 44, 57, 'QT-202610-0007', '2026-10-28', '2026-11-11', 'Service Quotation for ND-2343', NULL, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'draft', NULL, 'Quotation valid for 14 days. Parts subject to supplier market availability. Service labor warranty 30 days.', NULL, NULL, '{\"name\":\"Shashika\",\"phone\":\"071234567\",\"email\":\"Sashika@gmail.com\",\"customer_type\":\"company\",\"company_name\":\"Lanka Express Logistics s (Pvt) Ltd\",\"tax_number\":null,\"address\":\"Maharagama\",\"is_manual\":false}', '{\"vehicle_number\":\"ND-2343\",\"make_model\":\"Toyota KDH\",\"vehicle_type\":\"Van\",\"current_mileage\":3400,\"is_registered\":true}', '{\"company_name\":\"Viran Auto Care\",\"registration_number\":\"PV-2026-98765\",\"address\":\"No. 10\\/B, Mattegoda Road, Mattegoda 10240\",\"phone\":\"077 620 80 59\",\"email\":\"info@autocare.local\",\"tax_number\":\"VAT-123456789-000\"}', 2, '2026-10-28 02:41:16', '2026-10-28 02:41:16'),
(18, 1, 48, 61, 'QT-202610-0008', '2026-10-28', '2026-11-11', 'Service Quotation for CP HIL-2020', NULL, 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'converted', NULL, 'Quotation valid for 14 days. Parts subject to supplier market availability. Service labor warranty 30 days.', 88, NULL, '{\"name\":\"Dinesh Perera\",\"phone\":\"0771239988\",\"email\":\"dinesh@lankaexpress.local\",\"customer_type\":\"company\",\"company_name\":\"Lanka Express Logistics (Pvt) Ltd\",\"tax_number\":null,\"address\":\"No. 45 Logistics Park, Kelaniya\",\"is_manual\":false}', '{\"vehicle_number\":\"CP HIL-2020\",\"make_model\":\"Toyota Hilux\",\"vehicle_type\":\"SUV\",\"current_mileage\":45000,\"is_registered\":true}', '{\"company_name\":\"Viran Auto Care\",\"registration_number\":\"PV-2026-98765\",\"address\":\"No. 10\\/B, Mattegoda Road, Mattegoda 10240\",\"phone\":\"077 620 80 59\",\"email\":\"info@autocare.local\",\"tax_number\":\"VAT-123456789-000\"}', 2, '2026-10-28 02:46:22', '2026-10-28 02:46:28');

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
(21, 11, 1, 'service', 15, NULL, 'Engine Oil Change', 'Drain old oil and fill new oil', 1.00, 'Job', 2500.00, 'none', 0.00, 0.00, 0.00, 0.00, 2500.00, '2026-10-27 17:13:58', '2026-10-27 17:13:58'),
(22, 11, 2, 'product', 35, NULL, 'Toyota Engine Oil 0W-20', NULL, 1.00, 'Unit', 18500.00, 'none', 0.00, 0.00, 0.00, 0.00, 18500.00, '2026-10-27 17:13:58', '2026-10-27 17:13:58'),
(23, 11, 3, 'product', 34, NULL, 'Toyota Genuine Engine Oil Filter', NULL, 1.00, 'Unit', 2600.00, 'none', 0.00, 0.00, 0.00, 0.00, 2600.00, '2026-10-27 17:13:58', '2026-10-27 17:13:58'),
(24, 12, 1, 'service', 18, NULL, 'Engine Diagnostic Scan', 'OBD-II computer fault code scan', 1.00, 'Job', 3500.00, 'none', 0.00, 0.00, 0.00, 0.00, 3500.00, '2026-10-27 17:14:11', '2026-10-27 17:14:11'),
(25, 12, 2, 'service', 21, NULL, 'Suspension Inspection', 'Bushing, shock absorber and tie-rod check', 1.00, 'Job', 2000.00, 'none', 0.00, 0.00, 0.00, 0.00, 2000.00, '2026-10-27 17:14:11', '2026-10-27 17:14:11'),
(26, 13, 1, 'service', 17, NULL, 'Brake Pad Replacement', 'Inspect calipers and replace brake pads', 1.00, 'Job', 3000.00, 'none', 0.00, 0.00, 0.00, 0.00, 3000.00, '2026-10-27 17:14:23', '2026-10-27 17:14:23'),
(27, 13, 2, 'product', 37, NULL, 'Toyota Genuine Brake Pads', NULL, 1.00, 'Unit', 12500.00, 'none', 0.00, 0.00, 0.00, 0.00, 12500.00, '2026-10-27 17:14:23', '2026-10-27 17:14:23'),
(28, 14, 1, 'service', 19, NULL, 'Air Conditioning Service', 'AC gas check and filter cleaning', 3.00, 'Job', 3000.00, 'none', 0.00, 0.00, 0.00, 0.00, 9000.00, '2026-10-28 00:41:25', '2026-10-28 00:41:25'),
(29, 15, 1, 'service', 17, NULL, 'Brake Pad Replacement', 'Inspect calipers and replace brake pads', 1.00, 'Job', 23330.00, 'none', 0.00, 0.00, 0.00, 0.00, 23330.00, '2026-10-28 02:24:23', '2026-10-28 02:24:23'),
(30, 16, 1, 'service', 17, NULL, 'Brake Pad Replacement', 'Inspect calipers and replace brake pads', 1.00, 'Job', 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 0.00, '2026-10-28 02:41:16', '2026-10-28 02:41:16'),
(31, 17, 1, 'service', 17, NULL, 'Brake Pad Replacement', 'Inspect calipers and replace brake pads', 1.00, 'Job', 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 0.00, '2026-10-28 02:41:16', '2026-10-28 02:41:16'),
(32, 18, 1, 'service', 18, NULL, 'Engine Diagnostic Scan', 'OBD-II computer fault code scan', 1.00, 'Job', 0.00, 'none', 0.00, 0.00, 0.00, 0.00, 0.00, '2026-10-28 02:46:22', '2026-10-28 02:46:22');

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
(15, 'SRV-001', 'Engine Oil Change', 'Drain old oil and fill new oil', 'active', '2026-10-27 06:45:58', '2026-10-27 06:45:58'),
(16, 'SRV-002', 'Oil Filter Replacement', 'Replace engine oil filter cartridge', 'active', '2026-10-27 06:46:04', '2026-10-27 06:46:04'),
(17, 'SRV-003', 'Brake Pad Replacement', 'Inspect calipers and replace brake pads', 'active', '2026-10-27 06:46:10', '2026-10-27 06:46:10'),
(18, 'SRV-004', 'Engine Diagnostic Scan', 'OBD-II computer fault code scan', 'active', '2026-10-27 06:46:15', '2026-10-27 06:46:15'),
(19, 'SRV-005', 'Air Conditioning Service', 'AC gas check and filter cleaning', 'active', '2026-10-27 06:46:21', '2026-10-27 06:46:21'),
(20, 'SRV-006', 'Wheel Alignment', 'Laser 4-wheel alignment', 'active', '2026-10-27 06:46:27', '2026-10-27 06:46:27'),
(21, 'SRV-007', 'Suspension Inspection', 'Bushing, shock absorber and tie-rod check', 'active', '2026-10-27 06:46:32', '2026-10-27 06:46:32');

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
('kwPrJwenVLKXuD9fwSTpuRsWuBPuaiorJsvLiy7n', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJTZTE1Z0F3QTR2cUczaTlOcnk0UmlsZ01aTk9seURneTY4b1huZ05NIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9wcmludFwvY29uZmlnP3dvcmtzdGF0aW9uPVdvcmtzdGF0aW9uLVhXRksiLCJyb3V0ZSI6InByaW50LmNvbmZpZyJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoyLCJwYXNzd29yZF9oYXNoX3dlYiI6ImU5NGNmOTEwYTgyNjgzMTQ3NWYwMmRkOTczZjUwYzhkM2ZhYWI4Y2VkYzBmMzVjZmE2YjE1ZThjN2MwYmQ0YTQiLCJhY3RpdmVfYnJhbmNoX2lkIjoxfQ==', 1793176537),
('qqh2OiDeg2FCMwT9VrDEhzJJtPwDCf9Et2VHkpV1', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', 'eyJfdG9rZW4iOiJjbDdXZHdGaUc4bVpKODF1aDBrNDg3U1BmeG5WZ1N1cnJVSWNHR3hxIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9wcmludFwvY29uZmlnP3dvcmtzdGF0aW9uPVdvcmtzdGF0aW9uLVpPUjYiLCJyb3V0ZSI6InByaW50LmNvbmZpZyJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoyLCJwYXNzd29yZF9oYXNoX3dlYiI6ImU5NGNmOTEwYTgyNjgzMTQ3NWYwMmRkOTczZjUwYzhkM2ZhYWI4Y2VkYzBmMzVjZmE2YjE1ZThjN2MwYmQ0YTQiLCJhY3RpdmVfYnJhbmNoX2lkIjoxfQ==', 1793176538);

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
(40, 1, 'B26-0027-001', 27, 41, 35, 13, '2026-10-27', NULL, 50.00, 0.00, 46.00, 14000.00, 14000.0000, 18500.00, 'active', '2026-10-27 17:10:14', '2026-10-27 22:54:20'),
(41, 1, 'B26-0027-002', 27, 42, 34, 13, '2026-10-27', NULL, 40.00, 0.00, 34.00, 1800.00, 1800.0000, 2600.00, 'active', '2026-10-27 17:10:14', '2026-10-28 02:28:34'),
(42, 1, 'B26-0027-003', 27, 43, 37, 13, '2026-10-27', NULL, 30.00, 0.00, 29.00, 9500.00, 9500.0000, 12500.00, 'active', '2026-10-27 17:10:14', '2026-10-27 17:17:50'),
(43, 1, 'B26-0027-004', 27, 44, 36, 13, '2026-10-27', NULL, 30.00, 0.00, 28.00, 2500.00, 2500.0000, 3800.00, 'active', '2026-10-27 17:10:14', '2026-10-27 18:14:56'),
(44, 1, 'B26-0028-001', 28, 45, 42, 16, '2026-10-28', NULL, 34.00, 0.00, 34.00, 2500.00, 2500.0000, 30000.00, 'active', '2026-10-28 00:37:52', '2026-10-28 00:37:52');

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
(120, 1, 35, 40, 'grn_receipt', 50.00, 14000.0000, 'App\\Models\\GoodsReceivedNote', 27, 'Stock receipt from GRN #GRN-202610-0002 (Batch B26-0027-001)', 2, '2026-10-27 17:10:14', '2026-10-27 17:10:14'),
(121, 1, 34, 41, 'grn_receipt', 40.00, 1800.0000, 'App\\Models\\GoodsReceivedNote', 27, 'Stock receipt from GRN #GRN-202610-0002 (Batch B26-0027-002)', 2, '2026-10-27 17:10:14', '2026-10-27 17:10:14'),
(122, 1, 37, 42, 'grn_receipt', 30.00, 9500.0000, 'App\\Models\\GoodsReceivedNote', 27, 'Stock receipt from GRN #GRN-202610-0002 (Batch B26-0027-003)', 2, '2026-10-27 17:10:14', '2026-10-27 17:10:14'),
(123, 1, 36, 43, 'grn_receipt', 30.00, 2500.0000, 'App\\Models\\GoodsReceivedNote', 27, 'Stock receipt from GRN #GRN-202610-0002 (Batch B26-0027-004)', 2, '2026-10-27 17:10:14', '2026-10-27 17:10:14'),
(124, 1, 35, 40, 'job_card_issue', -1.00, 14000.0000, 'App\\Models\\JobCard', 46, 'FIFO issue from Batch B26-0027-001', 2, '2026-10-27 17:17:24', '2026-10-27 17:17:24'),
(125, 1, 34, 41, 'job_card_issue', -1.00, 1800.0000, 'App\\Models\\JobCard', 46, 'FIFO issue from Batch B26-0027-002', 2, '2026-10-27 17:17:26', '2026-10-27 17:17:26'),
(126, 1, 37, 42, 'job_card_issue', -1.00, 9500.0000, 'App\\Models\\JobCard', 48, 'FIFO issue from Batch B26-0027-003', 2, '2026-10-27 17:17:50', '2026-10-27 17:17:50'),
(127, 1, 35, 40, 'direct_sale', -1.00, 14000.0000, 'App\\Models\\DirectSale', 42, 'FIFO issue from Batch B26-0027-001', 2, '2026-10-27 17:34:10', '2026-10-27 17:34:10'),
(128, 1, 36, 43, 'direct_sale', -1.00, 2500.0000, 'App\\Models\\DirectSale', 43, 'FIFO issue from Batch B26-0027-004', 2, '2026-10-27 17:35:14', '2026-10-27 17:35:14'),
(129, 1, 35, 40, 'job_card_issue', -1.00, 14000.0000, 'App\\Models\\JobCard', 47, 'FIFO issue from Batch B26-0027-001', 2, '2026-10-27 18:09:15', '2026-10-27 18:09:15'),
(130, 1, 36, 43, 'job_card_issue', -1.00, 2500.0000, 'App\\Models\\JobCard', 50, 'FIFO issue from Batch B26-0027-004', 2, '2026-10-27 18:14:56', '2026-10-27 18:14:56'),
(131, 1, 34, 41, 'job_card_issue', -1.00, 1800.0000, 'App\\Models\\JobCard', 51, 'FIFO issue from Batch B26-0027-002', 2, '2026-10-27 22:54:16', '2026-10-27 22:54:16'),
(132, 1, 35, 40, 'job_card_issue', -1.00, 14000.0000, 'App\\Models\\JobCard', 51, 'FIFO issue from Batch B26-0027-001', 2, '2026-10-27 22:54:20', '2026-10-27 22:54:20'),
(133, 1, 34, 41, 'job_card_issue', -1.00, 1800.0000, 'App\\Models\\JobCard', 47, 'FIFO issue from Batch B26-0027-002', 2, '2026-10-27 23:29:38', '2026-10-27 23:29:38'),
(134, 1, 34, 41, 'job_card_issue', -1.00, 1800.0000, 'App\\Models\\JobCard', 52, 'FIFO issue from Batch B26-0027-002', 2, '2026-10-27 23:34:20', '2026-10-27 23:34:20'),
(135, 1, 34, 41, 'direct_sale', -1.00, 1800.0000, 'App\\Models\\DirectSale', 44, 'FIFO issue from Batch B26-0027-002', 2, '2026-10-27 23:35:20', '2026-10-27 23:35:20'),
(136, 1, 42, 44, 'grn_receipt', 34.00, 2500.0000, 'App\\Models\\GoodsReceivedNote', 28, 'Stock receipt from GRN #GRN-202610-0003 (Batch B26-0028-001)', 2, '2026-10-28 00:37:52', '2026-10-28 00:37:52'),
(137, 1, 34, 41, 'job_card_issue', -1.00, 1800.0000, 'App\\Models\\JobCard', 58, 'FIFO issue from Batch B26-0027-002', 2, '2026-10-28 02:28:34', '2026-10-28 02:28:34');

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
(13, 'Colombo Motor Spares', 'PV-101', 'Colombo Motor Spares Warehouse, Industrial Zone, Colombo', '0771234567', NULL, '0112345678', 1, '2026-10-27 06:44:47', '2026-10-27 06:44:47'),
(14, 'Lanka Auto Components', 'PV-102', 'Lanka Auto Components Warehouse, Industrial Zone, Colombo', '0772345678', NULL, '0112765432', 1, '2026-10-27 06:44:53', '2026-10-27 06:44:53'),
(15, 'Metro Parts Distribution', 'PV-103', 'Metro Parts Distribution Warehouse, Industrial Zone, Colombo', '0773456789', NULL, '0112890123', 1, '2026-10-27 06:44:59', '2026-10-27 23:57:37'),
(16, 'TVS Parts Distribution', 'TS-103', 'TVS Parts Distribution Warehouse, Industrial Zone, Colombo', '0773456789', '0773456789', NULL, 1, '2026-10-28 00:34:30', '2026-10-28 00:34:30');

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
(2, 'SPAY-202610-0001', 16, 1, 28, '2026-10-28', 85000.00, 'cash', NULL, NULL, 'completed', NULL, NULL, NULL, 2, '2026-10-28 00:38:02', '2026-10-28 00:38:02');

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
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `year_of_manufacture` varchar(10) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `chassis_number` varchar(100) DEFAULT NULL,
  `engine_number` varchar(100) DEFAULT NULL,
  `driver_name` varchar(150) DEFAULT NULL,
  `driver_mobile` varchar(50) DEFAULT NULL,
  `current_mileage` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `next_service_mileage` int(10) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `branch_id`, `customer_id`, `vehicle_type_id`, `vehicle_number`, `vehicle_number_normalized`, `make_model`, `brand`, `model`, `year_of_manufacture`, `color`, `chassis_number`, `engine_number`, `driver_name`, `driver_mobile`, `current_mileage`, `next_service_mileage`, `notes`, `created_at`, `updated_at`) VALUES
(52, NULL, 38, 10, 'WP CAK-4512', 'WPCAK4512', 'Toyota Aqua 2015', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 65000, 75000, NULL, '2026-10-27 17:11:24', '2026-10-27 17:11:24'),
(53, NULL, 39, 10, 'WP CAA-8834', 'WPCAA8834', 'Toyota Prius 2016', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 82000, 92000, NULL, '2026-10-27 17:11:33', '2026-10-27 17:11:33'),
(54, NULL, 40, 10, 'WP CAB-1902', 'WPCAB1902', 'Toyota Axio 2014', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 94000, 104000, NULL, '2026-10-27 17:11:42', '2026-10-27 17:11:42'),
(55, NULL, 41, 10, 'WP CBF-6721', 'WPCBF6721', 'Toyota Vitz 2018', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 48000, 58000, NULL, '2026-10-27 17:11:50', '2026-10-27 17:11:50'),
(56, NULL, 42, 10, 'WP CBA-9045', 'WPCBA9045', 'Toyota Hilux 2020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 55000, 65000, NULL, '2026-10-27 17:11:58', '2026-10-27 17:33:59'),
(57, 1, 44, 9, 'ND-2343', 'ND2343', 'Toyota KDH', 'Toyota', 'KDH', NULL, NULL, NULL, NULL, 'Rajitha', '071122343', 3400, 8400, NULL, '2026-10-28 00:24:04', '2026-10-28 00:24:04'),
(58, NULL, 45, 9, 'NF-3456', 'NF3456', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3400, 6900, NULL, '2026-10-28 00:29:23', '2026-10-28 00:29:23'),
(59, NULL, 46, 10, 'NF-3455', 'NF3455', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4555, 8055, NULL, '2026-10-28 00:30:28', '2026-10-28 00:30:28'),
(60, 1, 47, 9, 'ND-23434', 'ND23434', 'Toyota KDH', 'Toyota', 'KDH', NULL, NULL, NULL, NULL, 'Kasun', '078556767', 3400, 8400, NULL, '2026-10-28 00:33:08', '2026-10-28 00:33:08'),
(61, 1, 48, 8, 'CP HIL-2020', 'CPHIL2020', 'Toyota Hilux', 'Toyota', 'Hilux', NULL, NULL, NULL, NULL, 'Sunil Shantha', '0779988776', 45000, 50000, 'Toyota Hilux 2.8L Diesel Double Cab fleet service.', '2026-10-28 00:42:12', '2026-10-28 00:42:12'),
(62, 1, 49, 10, 'DC-2323', 'DC2323', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-10-28 02:23:18', '2026-10-28 02:23:18'),
(63, 1, 49, 10, 'WE-2333', 'WE2333', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-10-28 02:24:23', '2026-10-28 02:24:23'),
(64, NULL, 49, 10, 'WP HIL-2020', 'WPHIL2020', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-10-28 02:28:29', '2026-10-28 02:28:29');

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
(8, 'SUV', NULL, 0, NULL, 'active', '2026-10-27 06:28:12', '2026-10-27 06:28:12'),
(9, 'Van', NULL, 0, NULL, 'active', '2026-10-27 06:28:17', '2026-10-27 06:28:17'),
(10, 'Car / Sedan', NULL, 1, NULL, 'active', '2026-10-27 17:08:28', '2026-10-27 17:08:28');

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
-- Indexes for table `company_holidays`
--
ALTER TABLE `company_holidays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_holidays_created_by_foreign` (`created_by`),
  ADD KEY `company_holidays_branch_id_start_date_status_index` (`branch_id`,`start_date`,`status`),
  ADD KEY `company_holidays_start_date_index` (`start_date`),
  ADD KEY `company_holidays_end_date_index` (`end_date`),
  ADD KEY `company_holidays_status_index` (`status`);

--
-- Indexes for table `company_holiday_employees`
--
ALTER TABLE `company_holiday_employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_holiday_employee` (`company_holiday_id`,`employee_id`),
  ADD KEY `company_holiday_employees_employee_id_foreign` (`employee_id`);

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
  ADD UNIQUE KEY `direct_sales_bill_number_unique` (`bill_number`),
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
  ADD KEY `employees_branch_id_foreign` (`branch_id`),
  ADD KEY `employees_salary_type_index` (`salary_type`);

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
  ADD KEY `employee_advances_status_index` (`status`),
  ADD KEY `employee_advances_branch_id_foreign` (`branch_id`),
  ADD KEY `employee_advances_approved_by_foreign` (`approved_by`),
  ADD KEY `employee_advances_disbursed_by_foreign` (`disbursed_by`),
  ADD KEY `employee_advances_rejected_by_foreign` (`rejected_by`),
  ADD KEY `employee_advances_cancelled_by_foreign` (`cancelled_by`);

--
-- Indexes for table `employee_advance_recoveries`
--
ALTER TABLE `employee_advance_recoveries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_advance_recoveries_payroll_item_id_foreign` (`payroll_item_id`),
  ADD KEY `employee_advance_recoveries_employee_id_foreign` (`employee_id`),
  ADD KEY `employee_advance_recoveries_created_by_foreign` (`created_by`),
  ADD KEY `employee_advance_recoveries_employee_advance_id_status_index` (`employee_advance_id`,`status`),
  ADD KEY `employee_advance_recoveries_payroll_period_id_employee_id_index` (`payroll_period_id`,`employee_id`),
  ADD KEY `employee_advance_recoveries_recovery_date_index` (`recovery_date`),
  ADD KEY `employee_advance_recoveries_status_index` (`status`);

--
-- Indexes for table `employee_allowances`
--
ALTER TABLE `employee_allowances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_allowances_allowance_number_unique` (`allowance_number`),
  ADD KEY `employee_allowances_branch_id_foreign` (`branch_id`),
  ADD KEY `employee_allowances_approved_by_foreign` (`approved_by`),
  ADD KEY `employee_allowances_cancelled_by_foreign` (`cancelled_by`),
  ADD KEY `employee_allowances_created_by_foreign` (`created_by`),
  ADD KEY `employee_allowances_employee_id_status_index` (`employee_id`,`status`),
  ADD KEY `employee_allowances_effective_from_index` (`effective_from`),
  ADD KEY `employee_allowances_status_index` (`status`);

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
-- Indexes for table `employee_bonuses`
--
ALTER TABLE `employee_bonuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_bonuses_bonus_number_unique` (`bonus_number`),
  ADD KEY `employee_bonuses_branch_id_foreign` (`branch_id`),
  ADD KEY `employee_bonuses_approved_by_foreign` (`approved_by`),
  ADD KEY `employee_bonuses_rejected_by_foreign` (`rejected_by`),
  ADD KEY `employee_bonuses_cancelled_by_foreign` (`cancelled_by`),
  ADD KEY `employee_bonuses_payroll_item_id_foreign` (`payroll_item_id`),
  ADD KEY `employee_bonuses_created_by_foreign` (`created_by`),
  ADD KEY `employee_bonuses_employee_id_period_year_period_month_index` (`employee_id`,`period_year`,`period_month`),
  ADD KEY `employee_bonuses_status_period_year_period_month_index` (`status`,`period_year`,`period_month`),
  ADD KEY `employee_bonuses_period_year_index` (`period_year`),
  ADD KEY `employee_bonuses_period_month_index` (`period_month`),
  ADD KEY `employee_bonuses_status_index` (`status`);

--
-- Indexes for table `employee_overtimes`
--
ALTER TABLE `employee_overtimes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emp_ot_date_status_unique` (`employee_id`,`overtime_date`,`status`),
  ADD UNIQUE KEY `employee_overtimes_overtime_number_unique` (`overtime_number`),
  ADD KEY `employee_overtimes_branch_id_foreign` (`branch_id`),
  ADD KEY `employee_overtimes_approved_by_foreign` (`approved_by`),
  ADD KEY `employee_overtimes_rejected_by_foreign` (`rejected_by`),
  ADD KEY `employee_overtimes_cancelled_by_foreign` (`cancelled_by`),
  ADD KEY `employee_overtimes_payroll_item_id_foreign` (`payroll_item_id`),
  ADD KEY `employee_overtimes_created_by_foreign` (`created_by`),
  ADD KEY `employee_overtimes_employee_id_overtime_date_index` (`employee_id`,`overtime_date`),
  ADD KEY `employee_overtimes_overtime_date_index` (`overtime_date`),
  ADD KEY `employee_overtimes_status_index` (`status`);

--
-- Indexes for table `employee_wage_rates`
--
ALTER TABLE `employee_wage_rates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_wage_rates_employee_id_foreign` (`employee_id`),
  ADD KEY `employee_wage_rates_effective_from_index` (`effective_from`),
  ADD KEY `employee_wage_rates_effective_to_index` (`effective_to`),
  ADD KEY `employee_wage_rates_salary_type_index` (`salary_type`);

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
-- Indexes for table `grn_sequences`
--
ALTER TABLE `grn_sequences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `grn_sequences_series_unique` (`series`);

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
  ADD KEY `invoices_date_changed_by_foreign` (`date_changed_by`),
  ADD KEY `invoices_vehicle_id_foreign` (`vehicle_id`);

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
  ADD KEY `job_cards_appointment_id_foreign` (`appointment_id`),
  ADD KEY `job_cards_completed_by_foreign` (`completed_by`);

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
  ADD KEY `payments_job_card_id_foreign` (`job_card_id`),
  ADD KEY `payments_direct_sale_id_foreign` (`direct_sale_id`);

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
-- Indexes for table `payroll_statutory_settings`
--
ALTER TABLE `payroll_statutory_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_statutory_settings_created_by_foreign` (`created_by`),
  ADD KEY `payroll_statutory_settings_updated_by_foreign` (`updated_by`),
  ADD KEY `payroll_statutory_settings_effective_from_index` (`effective_from`),
  ADD KEY `payroll_statutory_settings_effective_to_index` (`effective_to`),
  ADD KEY `payroll_statutory_settings_is_active_index` (`is_active`);

--
-- Indexes for table `printer_settings`
--
ALTER TABLE `printer_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `printer_settings_branch_id_workstation_name_index` (`branch_id`,`workstation_name`);

--
-- Indexes for table `print_logs`
--
ALTER TABLE `print_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `print_logs_user_id_foreign` (`user_id`),
  ADD KEY `print_logs_document_type_status_index` (`document_type`,`status`),
  ADD KEY `print_logs_branch_id_created_at_index` (`branch_id`,`created_at`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=248;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `carpet_category_vehicle_type`
--
ALTER TABLE `carpet_category_vehicle_type`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `company_holidays`
--
ALTER TABLE `company_holidays`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `company_holiday_employees`
--
ALTER TABLE `company_holiday_employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_settings`
--
ALTER TABLE `company_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `customer_credit_accounts`
--
ALTER TABLE `customer_credit_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `direct_sales`
--
ALTER TABLE `direct_sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `direct_sale_items`
--
ALTER TABLE `direct_sale_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `employee_advances`
--
ALTER TABLE `employee_advances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employee_advance_recoveries`
--
ALTER TABLE `employee_advance_recoveries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_allowances`
--
ALTER TABLE `employee_allowances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employee_attendance`
--
ALTER TABLE `employee_attendance`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=321;

--
-- AUTO_INCREMENT for table `employee_bonuses`
--
ALTER TABLE `employee_bonuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employee_overtimes`
--
ALTER TABLE `employee_overtimes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_wage_rates`
--
ALTER TABLE `employee_wage_rates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `expense_payments`
--
ALTER TABLE `expense_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goods_received_notes`
--
ALTER TABLE `goods_received_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `goods_received_note_items`
--
ALTER TABLE `goods_received_note_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `grn_sequences`
--
ALTER TABLE `grn_sequences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `invoice_sequences`
--
ALTER TABLE `invoice_sequences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_cards`
--
ALTER TABLE `job_cards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `job_card_carpets`
--
ALTER TABLE `job_card_carpets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `job_card_products`
--
ALTER TABLE `job_card_products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `job_card_services`
--
ALTER TABLE `job_card_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `job_issues`
--
ALTER TABLE `job_issues`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `payroll_payments`
--
ALTER TABLE `payroll_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payroll_periods`
--
ALTER TABLE `payroll_periods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payroll_statutory_settings`
--
ALTER TABLE `payroll_statutory_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `printer_settings`
--
ALTER TABLE `printer_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `print_logs`
--
ALTER TABLE `print_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `quotation_items`
--
ALTER TABLE `quotation_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `service_vehicle_prices`
--
ALTER TABLE `service_vehicle_prices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `stock_batches`
--
ALTER TABLE `stock_batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `units_of_measurement`
--
ALTER TABLE `units_of_measurement`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `vehicle_types`
--
ALTER TABLE `vehicle_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
-- Constraints for table `company_holidays`
--
ALTER TABLE `company_holidays`
  ADD CONSTRAINT `company_holidays_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `company_holidays_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `company_holiday_employees`
--
ALTER TABLE `company_holiday_employees`
  ADD CONSTRAINT `company_holiday_employees_company_holiday_id_foreign` FOREIGN KEY (`company_holiday_id`) REFERENCES `company_holidays` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `company_holiday_employees_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `employee_advances_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_advances_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_advances_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_advances_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_advances_disbursed_by_foreign` FOREIGN KEY (`disbursed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_advances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_advances_payroll_item_id_foreign` FOREIGN KEY (`payroll_item_id`) REFERENCES `payroll_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_advances_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_advance_recoveries`
--
ALTER TABLE `employee_advance_recoveries`
  ADD CONSTRAINT `employee_advance_recoveries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_advance_recoveries_employee_advance_id_foreign` FOREIGN KEY (`employee_advance_id`) REFERENCES `employee_advances` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_advance_recoveries_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_advance_recoveries_payroll_item_id_foreign` FOREIGN KEY (`payroll_item_id`) REFERENCES `payroll_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_advance_recoveries_payroll_period_id_foreign` FOREIGN KEY (`payroll_period_id`) REFERENCES `payroll_periods` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_allowances`
--
ALTER TABLE `employee_allowances`
  ADD CONSTRAINT `employee_allowances_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_allowances_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_allowances_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_allowances_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_allowances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_attendance`
--
ALTER TABLE `employee_attendance`
  ADD CONSTRAINT `employee_attendance_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_attendance_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_bonuses`
--
ALTER TABLE `employee_bonuses`
  ADD CONSTRAINT `employee_bonuses_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_bonuses_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_bonuses_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_bonuses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_bonuses_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_bonuses_payroll_item_id_foreign` FOREIGN KEY (`payroll_item_id`) REFERENCES `payroll_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_bonuses_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_overtimes`
--
ALTER TABLE `employee_overtimes`
  ADD CONSTRAINT `employee_overtimes_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_overtimes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_overtimes_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_overtimes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_overtimes_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_overtimes_payroll_item_id_foreign` FOREIGN KEY (`payroll_item_id`) REFERENCES `payroll_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_overtimes_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
  ADD CONSTRAINT `invoices_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoices_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

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
  ADD CONSTRAINT `job_cards_completed_by_foreign` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
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
  ADD CONSTRAINT `payments_direct_sale_id_foreign` FOREIGN KEY (`direct_sale_id`) REFERENCES `direct_sales` (`id`) ON DELETE SET NULL,
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
-- Constraints for table `payroll_statutory_settings`
--
ALTER TABLE `payroll_statutory_settings`
  ADD CONSTRAINT `payroll_statutory_settings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payroll_statutory_settings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `printer_settings`
--
ALTER TABLE `printer_settings`
  ADD CONSTRAINT `printer_settings_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `print_logs`
--
ALTER TABLE `print_logs`
  ADD CONSTRAINT `print_logs_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `print_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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

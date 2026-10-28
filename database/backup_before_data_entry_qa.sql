-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: autocare_system
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `module` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=172 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (128,2,'login','Authentication','User Admin User (admin) logged in.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:01:38','2026-10-27 06:01:38'),(129,2,'create_employee','Payroll','Registered new employee Kamal Ranjith (EMP-0001) as Monthly employee (Monthly Basic LKR 85,000.00)','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:01:57','2026-10-27 06:01:57'),(130,2,'create_employee','Payroll','Registered new employee Nimal Perera (EMP-0002) as Monthly employee (Monthly Basic LKR 75,000.00)','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:02:15','2026-10-27 06:02:15'),(131,2,'create_employee','Payroll','Registered new employee Saman Kumara (EMP-0003) as Daily employee (Daily Wage LKR 3,000.00)','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:02:30','2026-10-27 06:02:30'),(132,2,'update_employee','Payroll','Updated employee details for Kamal Ranjith (EMP-0001)','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:02:44','2026-10-27 06:02:44'),(133,2,'login','Authentication','User Admin User (admin) logged in.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:13:28','2026-10-27 06:13:28'),(134,2,'login','Authentication','User Admin User (admin) logged in.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:13:29','2026-10-27 06:13:29'),(135,2,'record_attendance','Payroll','Recorded daily attendance for 2026-10-27 (3 employees updated)','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:21:10','2026-10-27 06:21:10'),(136,2,'generate_payroll','Payroll','Generated draft salary report for October 2026','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:21:30','2026-10-27 06:21:30'),(137,2,'created','Vehicle Types','Added new vehicle type: SUV (Carpets: Disabled).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:28:12','2026-10-27 06:28:12'),(138,2,'created','Vehicle Types','Added new vehicle type: Van (Carpets: Disabled).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:28:17','2026-10-27 06:28:17'),(139,2,'created','Customers','Registered new customer \'Ruwan Silva\' (0771122334).',NULL,NULL,'2026-10-27 06:32:22','2026-10-27 06:32:22'),(140,2,'created','Customers','Registered new customer \'Dinesh Perera\' (0772233445).',NULL,NULL,'2026-10-27 06:32:28','2026-10-27 06:32:28'),(141,2,'created','Customers','Registered new customer \'Chamara Wijesinghe\' (0773344556).',NULL,NULL,'2026-10-27 06:32:33','2026-10-27 06:32:33'),(142,2,'created','Customers','Registered new customer \'Kasun Jayawardena\' (0774455667).',NULL,NULL,'2026-10-27 06:32:39','2026-10-27 06:32:39'),(143,2,'created','Customers','Registered new customer \'Sunil Fernando\' (0775566778).',NULL,NULL,'2026-10-27 06:32:45','2026-10-27 06:32:45'),(144,2,'created','Suppliers','Registered new supplier: Colombo Motor Spares.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:44:47','2026-10-27 06:44:47'),(145,2,'created','Suppliers','Registered new supplier: Lanka Auto Components.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:44:53','2026-10-27 06:44:53'),(146,2,'created','Suppliers','Registered new supplier: Metro Parts Distribution.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:44:59','2026-10-27 06:44:59'),(147,2,'created','Products','Created product: Toyota Genuine Engine Oil Filter (SKU: FLT-TOY-001).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:45:05','2026-10-27 06:45:05'),(148,2,'created','Products','Created product: Toyota Engine Oil 0W-20 (SKU: OIL-TOY-0W20).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:45:10','2026-10-27 06:45:10'),(149,2,'created','Products','Created product: Toyota Genuine Air Filter (SKU: FLT-AIR-001).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:45:16','2026-10-27 06:45:16'),(150,2,'created','Products','Created product: Toyota Genuine Brake Pads (SKU: BRK-TOY-001).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:45:22','2026-10-27 06:45:22'),(151,2,'created','Products','Created product: Toyota Long Life Coolant (SKU: LUB-CLT-001).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:45:28','2026-10-27 06:45:28'),(152,2,'created','Products','Created product: Denso Spark Plug (SKU: IGN-SPK-001).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:45:34','2026-10-27 06:45:34'),(153,2,'created','Products','Created product: Bosch Wiper Blade (SKU: ACC-WPR-001).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:45:39','2026-10-27 06:45:39'),(154,2,'created','Products','Created product: NGK Spark Plug (SKU: IGN-SPK-002).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:45:45','2026-10-27 06:45:45'),(155,2,'created','Purchasing (GRN)','Drafted GRN #GRN-202610-0001 totaling LKR 40000.00.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:45:51','2026-10-27 06:45:51'),(156,2,'created','Services','Added new service: Engine Oil Change (SRV-001).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:45:58','2026-10-27 06:45:58'),(157,2,'created','Services','Added new service: Oil Filter Replacement (SRV-002).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:46:04','2026-10-27 06:46:04'),(158,2,'created','Services','Added new service: Brake Pad Replacement (SRV-003).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:46:10','2026-10-27 06:46:10'),(159,2,'created','Services','Added new service: Engine Diagnostic Scan (SRV-004).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:46:15','2026-10-27 06:46:15'),(160,2,'created','Services','Added new service: Air Conditioning Service (SRV-005).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:46:21','2026-10-27 06:46:21'),(161,2,'created','Services','Added new service: Wheel Alignment (SRV-006).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:46:27','2026-10-27 06:46:27'),(162,2,'created','Services','Added new service: Suspension Inspection (SRV-007).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:46:32','2026-10-27 06:46:32'),(163,2,'create_shop_expense','Expenses','Recorded shop expense EXP-0001 for Electricity (LKR 18,500.00, Status: Paid)','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:46:50','2026-10-27 06:46:50'),(164,2,'create_shop_expense','Expenses','Recorded shop expense EXP-0002 for Maintenance (LKR 12,000.00, Status: Paid)','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:46:54','2026-10-27 06:46:54'),(165,2,'create_shop_expense','Expenses','Recorded shop expense EXP-0003 for Chemicals (LKR 6,500.00, Status: Paid)','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:46:58','2026-10-27 06:46:58'),(166,2,'create_shop_expense','Expenses','Recorded shop expense EXP-0004 for Other (LKR 4,500.00, Status: Paid)','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:47:03','2026-10-27 06:47:03'),(167,2,'record_attendance','Payroll','Recorded daily attendance for 2026-10-27 (3 employees updated)','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:47:09','2026-10-27 06:47:09'),(168,2,'create_holiday','Payroll','Declared company holiday \'Deepavali Festival\' from 2026-10-31 to 2026-10-31 (Paid, Full day)','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:47:15','2026-10-27 06:47:15'),(169,2,'register_advance','Payroll','Registered salary advance ADV-0001 of LKR 20,000.00 for Nimal Perera [Status: pending]','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:56:13','2026-10-27 06:56:13'),(170,2,'register_allowance','Payroll','Configured 1 allowance(s) [Type: meal, Amount: LKR 2,000.00]','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:56:34','2026-10-27 06:56:34'),(171,2,'register_bonus','Payroll','Registered 1 bonus(es) for period 2026-10 [Type: performance]','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-27 06:56:55','2026-10-27 06:56:55');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appointments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `appointment_number` varchar(50) NOT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `vehicle_id` bigint(20) unsigned DEFAULT NULL,
  `vehicle_type_id` bigint(20) unsigned DEFAULT NULL,
  `service_id` bigint(20) unsigned DEFAULT NULL,
  `customer_name` varchar(150) NOT NULL,
  `mobile_number` varchar(50) NOT NULL,
  `vehicle_number` varchar(50) NOT NULL,
  `service_name` varchar(150) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` varchar(20) NOT NULL,
  `status` enum('scheduled','confirmed','arrived','in_service','completed','cancelled','no_show') NOT NULL DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `job_card_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `appointments_appointment_number_unique` (`appointment_number`),
  KEY `appointments_branch_id_foreign` (`branch_id`),
  KEY `appointments_customer_id_foreign` (`customer_id`),
  KEY `appointments_vehicle_id_foreign` (`vehicle_id`),
  KEY `appointments_vehicle_type_id_foreign` (`vehicle_type_id`),
  KEY `appointments_service_id_foreign` (`service_id`),
  KEY `appointments_job_card_id_foreign` (`job_card_id`),
  KEY `appointments_created_by_foreign` (`created_by`),
  KEY `appointments_appointment_date_index` (`appointment_date`),
  KEY `appointments_appointment_time_index` (`appointment_time`),
  KEY `appointments_status_index` (`status`),
  CONSTRAINT `appointments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `appointments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `appointments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `appointments_job_card_id_foreign` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards` (`id`) ON DELETE SET NULL,
  CONSTRAINT `appointments_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  CONSTRAINT `appointments_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `appointments_vehicle_type_id_foreign` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_types` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointments`
--

LOCK TABLES `appointments` WRITE;
/*!40000 ALTER TABLE `appointments` DISABLE KEYS */;
/*!40000 ALTER TABLE `appointments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branch_user`
--

DROP TABLE IF EXISTS `branch_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `branch_user_branch_id_user_id_unique` (`branch_id`,`user_id`),
  KEY `branch_user_user_id_foreign` (`user_id`),
  CONSTRAINT `branch_user_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `branch_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch_user`
--

LOCK TABLES `branch_user` WRITE;
/*!40000 ALTER TABLE `branch_user` DISABLE KEYS */;
INSERT INTO `branch_user` VALUES (1,1,2,'2026-10-20 01:42:37','2026-10-20 01:42:37'),(5,3,2,'2026-10-20 04:40:40','2026-10-20 04:40:40');
/*!40000 ALTER TABLE `branch_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `branch_code` varchar(50) NOT NULL,
  `branch_name` varchar(150) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `manager_id` bigint(20) unsigned DEFAULT NULL,
  `is_main_branch` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `branches_company_id_branch_code_unique` (`company_id`,`branch_code`),
  KEY `branches_manager_id_foreign` (`manager_id`),
  CONSTRAINT `branches_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `company_settings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `branches_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES (1,1,'BR-001','Viran Auto Care Mattegoda','No. 10/B, Mattegoda Road, Mattegoda 10240','077 620 80 59','colombo@autocare.local',2,1,1,'2026-10-20 01:42:37','2026-10-21 07:59:28'),(3,1,'BR-002','AutoCare Kandy Service Center','No. 78, William Gopallawa Mawatha, Kandy, Sri Lanka','+94 81 223 4567','kandy@autocare.local',2,0,1,'2026-10-20 04:40:40','2026-10-20 04:40:40');
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('viran-auto-care-cache-admin@1234.com|127.0.0.1','i:1;',1793103990),('viran-auto-care-cache-admin@1234.com|127.0.0.1:timer','i:1793103990;',1793103990);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carpet_categories`
--

DROP TABLE IF EXISTS `carpet_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carpet_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `carpet_categories_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carpet_categories`
--

LOCK TABLES `carpet_categories` WRITE;
/*!40000 ALTER TABLE `carpet_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `carpet_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carpet_category_vehicle_type`
--

DROP TABLE IF EXISTS `carpet_category_vehicle_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carpet_category_vehicle_type` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_type_id` bigint(20) unsigned NOT NULL,
  `carpet_category_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vtype_carpet_cat_unique` (`vehicle_type_id`,`carpet_category_id`),
  KEY `carpet_category_vehicle_type_carpet_category_id_foreign` (`carpet_category_id`),
  CONSTRAINT `carpet_category_vehicle_type_carpet_category_id_foreign` FOREIGN KEY (`carpet_category_id`) REFERENCES `carpet_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `carpet_category_vehicle_type_vehicle_type_id_foreign` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carpet_category_vehicle_type`
--

LOCK TABLES `carpet_category_vehicle_type` WRITE;
/*!40000 ALTER TABLE `carpet_category_vehicle_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `carpet_category_vehicle_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_holiday_employees`
--

DROP TABLE IF EXISTS `company_holiday_employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_holiday_employees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_holiday_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_holiday_employee` (`company_holiday_id`,`employee_id`),
  KEY `company_holiday_employees_employee_id_foreign` (`employee_id`),
  CONSTRAINT `company_holiday_employees_company_holiday_id_foreign` FOREIGN KEY (`company_holiday_id`) REFERENCES `company_holidays` (`id`) ON DELETE CASCADE,
  CONSTRAINT `company_holiday_employees_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_holiday_employees`
--

LOCK TABLES `company_holiday_employees` WRITE;
/*!40000 ALTER TABLE `company_holiday_employees` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_holiday_employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_holidays`
--

DROP TABLE IF EXISTS `company_holidays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_holidays` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `duration` varchar(20) NOT NULL DEFAULT 'full_day',
  `holiday_type` varchar(20) NOT NULL DEFAULT 'paid',
  `applies_to_all` tinyint(1) NOT NULL DEFAULT 1,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_holidays_created_by_foreign` (`created_by`),
  KEY `company_holidays_branch_id_start_date_status_index` (`branch_id`,`start_date`,`status`),
  KEY `company_holidays_start_date_index` (`start_date`),
  KEY `company_holidays_end_date_index` (`end_date`),
  KEY `company_holidays_status_index` (`status`),
  CONSTRAINT `company_holidays_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `company_holidays_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_holidays`
--

LOCK TABLES `company_holidays` WRITE;
/*!40000 ALTER TABLE `company_holidays` DISABLE KEYS */;
INSERT INTO `company_holidays` VALUES (1,NULL,'Deepavali Festival','2026-10-31','2026-10-31','full_day','paid',1,'active',NULL,2,'2026-10-27 06:47:15','2026-10-27 06:47:15');
/*!40000 ALTER TABLE `company_holidays` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_settings`
--

DROP TABLE IF EXISTS `company_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL,
  `registration_number` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `tax_number` varchar(255) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `signature_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_settings`
--

LOCK TABLES `company_settings` WRITE;
/*!40000 ALTER TABLE `company_settings` DISABLE KEYS */;
INSERT INTO `company_settings` VALUES (1,'Viran Auto Care','PV-2026-98765','No. 10/B, Mattegoda Road, Mattegoda 10240','077 620 80 59','info@autocare.local','VAT-123456789-000','company/viran_auto_care_logo.png',NULL,'2026-10-19 06:08:35','2026-10-21 07:59:28');
/*!40000 ALTER TABLE `company_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_credit_accounts`
--

DROP TABLE IF EXISTS `customer_credit_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_credit_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `credit_limit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_terms_days` int(11) NOT NULL DEFAULT 30,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_credit_accounts_customer_id_unique` (`customer_id`),
  KEY `customer_credit_accounts_created_by_foreign` (`created_by`),
  CONSTRAINT `customer_credit_accounts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `customer_credit_accounts_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_credit_accounts`
--

LOCK TABLES `customer_credit_accounts` WRITE;
/*!40000 ALTER TABLE `customer_credit_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_credit_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customers_phone_index` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (38,'Ruwan Silva','0771122334','ruwan.silva@example.local','individual',NULL,NULL,NULL,'45 Galle Road, Colombo',NULL,1,'active','2026-10-27 06:32:22','2026-10-27 06:32:22'),(39,'Dinesh Perera','0772233445','dinesh.perera@example.local','individual',NULL,NULL,NULL,'82 Kandy Road, Kiribathgoda',NULL,1,'active','2026-10-27 06:32:28','2026-10-27 06:32:28'),(40,'Chamara Wijesinghe','0773344556','chamara.w@example.local','individual',NULL,NULL,NULL,'19 High Level Road, Maharagama',NULL,1,'active','2026-10-27 06:32:33','2026-10-27 06:32:33'),(41,'Kasun Jayawardena','0774455667','kasun.j@example.local','individual',NULL,NULL,NULL,'63 Negombo Road, Ja-Ela',NULL,1,'active','2026-10-27 06:32:39','2026-10-27 06:32:39'),(42,'Sunil Fernando','0775566778','sunil.f@example.local','individual',NULL,NULL,NULL,'110 Station Road, Moratuwa',NULL,1,'active','2026-10-27 06:32:45','2026-10-27 06:32:45');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `direct_sale_items`
--

DROP TABLE IF EXISTS `direct_sale_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `direct_sale_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `direct_sale_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `product_sku` varchar(50) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` decimal(12,2) NOT NULL DEFAULT 1.00,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unit_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `batch_allocations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`batch_allocations`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `direct_sale_items_direct_sale_id_foreign` (`direct_sale_id`),
  KEY `direct_sale_items_product_id_foreign` (`product_id`),
  CONSTRAINT `direct_sale_items_direct_sale_id_foreign` FOREIGN KEY (`direct_sale_id`) REFERENCES `direct_sales` (`id`) ON DELETE CASCADE,
  CONSTRAINT `direct_sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `direct_sale_items`
--

LOCK TABLES `direct_sale_items` WRITE;
/*!40000 ALTER TABLE `direct_sale_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `direct_sale_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `direct_sales`
--

DROP TABLE IF EXISTS `direct_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `direct_sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `sale_number` varchar(50) NOT NULL,
  `bill_number` varchar(100) DEFAULT NULL,
  `invoice_id` bigint(20) unsigned DEFAULT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
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
  `cancelled_by` bigint(20) unsigned DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `stock_reversed` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `direct_sales_sale_number_unique` (`sale_number`),
  UNIQUE KEY `direct_sales_bill_number_unique` (`bill_number`),
  KEY `direct_sales_invoice_id_foreign` (`invoice_id`),
  KEY `direct_sales_customer_id_foreign` (`customer_id`),
  KEY `direct_sales_created_by_foreign` (`created_by`),
  KEY `direct_sales_status_index` (`status`),
  KEY `direct_sales_branch_id_foreign` (`branch_id`),
  KEY `direct_sales_cancelled_by_foreign` (`cancelled_by`),
  CONSTRAINT `direct_sales_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `direct_sales_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `direct_sales_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `direct_sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `direct_sales_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `direct_sales`
--

LOCK TABLES `direct_sales` WRITE;
/*!40000 ALTER TABLE `direct_sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `direct_sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_advance_recoveries`
--

DROP TABLE IF EXISTS `employee_advance_recoveries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_advance_recoveries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_advance_id` bigint(20) unsigned NOT NULL,
  `payroll_period_id` bigint(20) unsigned NOT NULL,
  `payroll_item_id` bigint(20) unsigned DEFAULT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `recovery_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'recovered',
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_advance_recoveries_payroll_item_id_foreign` (`payroll_item_id`),
  KEY `employee_advance_recoveries_employee_id_foreign` (`employee_id`),
  KEY `employee_advance_recoveries_created_by_foreign` (`created_by`),
  KEY `employee_advance_recoveries_employee_advance_id_status_index` (`employee_advance_id`,`status`),
  KEY `employee_advance_recoveries_payroll_period_id_employee_id_index` (`payroll_period_id`,`employee_id`),
  KEY `employee_advance_recoveries_recovery_date_index` (`recovery_date`),
  KEY `employee_advance_recoveries_status_index` (`status`),
  CONSTRAINT `employee_advance_recoveries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_advance_recoveries_employee_advance_id_foreign` FOREIGN KEY (`employee_advance_id`) REFERENCES `employee_advances` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_advance_recoveries_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_advance_recoveries_payroll_item_id_foreign` FOREIGN KEY (`payroll_item_id`) REFERENCES `payroll_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_advance_recoveries_payroll_period_id_foreign` FOREIGN KEY (`payroll_period_id`) REFERENCES `payroll_periods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_advance_recoveries`
--

LOCK TABLES `employee_advance_recoveries` WRITE;
/*!40000 ALTER TABLE `employee_advance_recoveries` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_advance_recoveries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_advances`
--

DROP TABLE IF EXISTS `employee_advances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_advances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `advance_number` varchar(50) NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `advance_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `monthly_installment` decimal(12,2) DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'cash',
  `notes` text DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `payroll_item_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `disbursed_at` date DEFAULT NULL,
  `disbursed_by` bigint(20) unsigned DEFAULT NULL,
  `rejected_by` bigint(20) unsigned DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `cancelled_by` bigint(20) unsigned DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_advances_advance_number_unique` (`advance_number`),
  KEY `employee_advances_employee_id_foreign` (`employee_id`),
  KEY `employee_advances_payroll_item_id_foreign` (`payroll_item_id`),
  KEY `employee_advances_created_by_foreign` (`created_by`),
  KEY `employee_advances_advance_date_index` (`advance_date`),
  KEY `employee_advances_status_index` (`status`),
  KEY `employee_advances_branch_id_foreign` (`branch_id`),
  KEY `employee_advances_approved_by_foreign` (`approved_by`),
  KEY `employee_advances_disbursed_by_foreign` (`disbursed_by`),
  KEY `employee_advances_rejected_by_foreign` (`rejected_by`),
  KEY `employee_advances_cancelled_by_foreign` (`cancelled_by`),
  CONSTRAINT `employee_advances_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_advances_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_advances_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_advances_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_advances_disbursed_by_foreign` FOREIGN KEY (`disbursed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_advances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_advances_payroll_item_id_foreign` FOREIGN KEY (`payroll_item_id`) REFERENCES `payroll_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_advances_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_advances`
--

LOCK TABLES `employee_advances` WRITE;
/*!40000 ALTER TABLE `employee_advances` DISABLE KEYS */;
INSERT INTO `employee_advances` VALUES (5,'ADV-0001',15,1,'2026-10-27',20000.00,NULL,'cash',NULL,'pending',NULL,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-27 06:56:13','2026-10-27 06:56:13');
/*!40000 ALTER TABLE `employee_advances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_allowances`
--

DROP TABLE IF EXISTS `employee_allowances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_allowances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `allowance_number` varchar(50) NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
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
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `cancelled_by` bigint(20) unsigned DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_allowances_allowance_number_unique` (`allowance_number`),
  KEY `employee_allowances_branch_id_foreign` (`branch_id`),
  KEY `employee_allowances_approved_by_foreign` (`approved_by`),
  KEY `employee_allowances_cancelled_by_foreign` (`cancelled_by`),
  KEY `employee_allowances_created_by_foreign` (`created_by`),
  KEY `employee_allowances_employee_id_status_index` (`employee_id`,`status`),
  KEY `employee_allowances_effective_from_index` (`effective_from`),
  KEY `employee_allowances_status_index` (`status`),
  CONSTRAINT `employee_allowances_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_allowances_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_allowances_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_allowances_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_allowances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_allowances`
--

LOCK TABLES `employee_allowances` WRITE;
/*!40000 ALTER TABLE `employee_allowances` DISABLE KEYS */;
INSERT INTO `employee_allowances` VALUES (1,'ALW-0001',14,1,'meal',NULL,2000.00,1,'2026-10-27',NULL,NULL,NULL,0,0,0,'active',2,'2026-10-27 06:56:34',NULL,NULL,NULL,NULL,2,'2026-10-27 06:56:34','2026-10-27 06:56:34');
/*!40000 ALTER TABLE `employee_allowances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_attendance`
--

DROP TABLE IF EXISTS `employee_attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_attendance` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `attendance_date` date NOT NULL,
  `status` varchar(30) NOT NULL,
  `payable_day_fraction` decimal(4,2) NOT NULL DEFAULT 1.00,
  `applied_daily_wage` decimal(12,2) NOT NULL,
  `daily_earnings` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notes` varchar(255) DEFAULT NULL,
  `recorded_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_emp_attendance_date` (`employee_id`,`attendance_date`),
  KEY `employee_attendance_recorded_by_foreign` (`recorded_by`),
  KEY `employee_attendance_attendance_date_index` (`attendance_date`),
  KEY `employee_attendance_status_index` (`status`),
  CONSTRAINT `employee_attendance_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_attendance_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=318 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_attendance`
--

LOCK TABLES `employee_attendance` WRITE;
/*!40000 ALTER TABLE `employee_attendance` DISABLE KEYS */;
INSERT INTO `employee_attendance` VALUES (315,14,'2026-10-27','full_day',1.00,0.00,0.00,NULL,2,'2026-10-27 06:21:10','2026-10-27 06:21:10'),(316,15,'2026-10-27','full_day',1.00,0.00,0.00,NULL,2,'2026-10-27 06:21:10','2026-10-27 06:47:09'),(317,16,'2026-10-27','full_day',1.00,3000.00,3000.00,NULL,2,'2026-10-27 06:21:10','2026-10-27 06:21:10');
/*!40000 ALTER TABLE `employee_attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_bonuses`
--

DROP TABLE IF EXISTS `employee_bonuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_bonuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bonus_number` varchar(50) NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
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
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_by` bigint(20) unsigned DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `cancelled_by` bigint(20) unsigned DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `payroll_item_id` bigint(20) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_bonuses_bonus_number_unique` (`bonus_number`),
  KEY `employee_bonuses_branch_id_foreign` (`branch_id`),
  KEY `employee_bonuses_approved_by_foreign` (`approved_by`),
  KEY `employee_bonuses_rejected_by_foreign` (`rejected_by`),
  KEY `employee_bonuses_cancelled_by_foreign` (`cancelled_by`),
  KEY `employee_bonuses_payroll_item_id_foreign` (`payroll_item_id`),
  KEY `employee_bonuses_created_by_foreign` (`created_by`),
  KEY `employee_bonuses_employee_id_period_year_period_month_index` (`employee_id`,`period_year`,`period_month`),
  KEY `employee_bonuses_status_period_year_period_month_index` (`status`,`period_year`,`period_month`),
  KEY `employee_bonuses_period_year_index` (`period_year`),
  KEY `employee_bonuses_period_month_index` (`period_month`),
  KEY `employee_bonuses_status_index` (`status`),
  CONSTRAINT `employee_bonuses_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_bonuses_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_bonuses_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_bonuses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_bonuses_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_bonuses_payroll_item_id_foreign` FOREIGN KEY (`payroll_item_id`) REFERENCES `payroll_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_bonuses_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_bonuses`
--

LOCK TABLES `employee_bonuses` WRITE;
/*!40000 ALTER TABLE `employee_bonuses` DISABLE KEYS */;
INSERT INTO `employee_bonuses` VALUES (1,'BON-0001',14,1,'performance','fixed',NULL,3000.00,0,1,2026,10,'2026-10-01','approved',2,'2026-10-27 06:56:55',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,'2026-10-27 06:56:55','2026-10-27 06:56:55');
/*!40000 ALTER TABLE `employee_bonuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_overtimes`
--

DROP TABLE IF EXISTS `employee_overtimes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_overtimes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `overtime_number` varchar(50) NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `overtime_date` date NOT NULL,
  `hours` decimal(6,2) NOT NULL,
  `hourly_rate` decimal(10,2) NOT NULL,
  `rate_multiplier` decimal(4,2) NOT NULL DEFAULT 1.50,
  `amount` decimal(12,2) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_by` bigint(20) unsigned DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `cancelled_by` bigint(20) unsigned DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `payroll_item_id` bigint(20) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `emp_ot_date_status_unique` (`employee_id`,`overtime_date`,`status`),
  UNIQUE KEY `employee_overtimes_overtime_number_unique` (`overtime_number`),
  KEY `employee_overtimes_branch_id_foreign` (`branch_id`),
  KEY `employee_overtimes_approved_by_foreign` (`approved_by`),
  KEY `employee_overtimes_rejected_by_foreign` (`rejected_by`),
  KEY `employee_overtimes_cancelled_by_foreign` (`cancelled_by`),
  KEY `employee_overtimes_payroll_item_id_foreign` (`payroll_item_id`),
  KEY `employee_overtimes_created_by_foreign` (`created_by`),
  KEY `employee_overtimes_employee_id_overtime_date_index` (`employee_id`,`overtime_date`),
  KEY `employee_overtimes_overtime_date_index` (`overtime_date`),
  KEY `employee_overtimes_status_index` (`status`),
  CONSTRAINT `employee_overtimes_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_overtimes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_overtimes_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_overtimes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_overtimes_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_overtimes_payroll_item_id_foreign` FOREIGN KEY (`payroll_item_id`) REFERENCES `payroll_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_overtimes_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_overtimes`
--

LOCK TABLES `employee_overtimes` WRITE;
/*!40000 ALTER TABLE `employee_overtimes` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_overtimes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_wage_rates`
--

DROP TABLE IF EXISTS `employee_wage_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_wage_rates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `salary_type` varchar(20) NOT NULL DEFAULT 'daily',
  `daily_wage` decimal(12,2) NOT NULL,
  `monthly_basic_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_wage_rates_employee_id_foreign` (`employee_id`),
  KEY `employee_wage_rates_effective_from_index` (`effective_from`),
  KEY `employee_wage_rates_effective_to_index` (`effective_to`),
  KEY `employee_wage_rates_salary_type_index` (`salary_type`),
  CONSTRAINT `employee_wage_rates_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_wage_rates`
--

LOCK TABLES `employee_wage_rates` WRITE;
/*!40000 ALTER TABLE `employee_wage_rates` DISABLE KEYS */;
INSERT INTO `employee_wage_rates` VALUES (13,14,'monthly',0.00,85000.00,'2026-10-27',NULL,'Initial salary settings upon registration','2026-10-27 06:01:57','2026-10-27 06:01:57'),(14,15,'monthly',0.00,75000.00,'2026-10-27',NULL,'Initial salary settings upon registration','2026-10-27 06:02:15','2026-10-27 06:02:15'),(15,16,'daily',3000.00,0.00,'2026-10-27',NULL,'Initial salary settings upon registration','2026-10-27 06:02:30','2026-10-27 06:02:30');
/*!40000 ALTER TABLE `employee_wage_rates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
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
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_employee_number_unique` (`employee_number`),
  KEY `employees_user_id_foreign` (`user_id`),
  KEY `employees_mobile_index` (`mobile`),
  KEY `employees_is_active_index` (`is_active`),
  KEY `employees_branch_id_foreign` (`branch_id`),
  KEY `employees_salary_type_index` (`salary_type`),
  CONSTRAINT `employees_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (14,1,'EMP-0001','Kamal Ranjith','0771234561','12/A, Temple Road, Mattegoda',0.00,85000.00,'2026-10-27','Lead Senior Mechanic','monthly',1,'Certified Toyota technician with 8 years experience.','bank_transfer','Commercial Bank','Kottawa','K. Ranjith','8009912345','EPF-8891',NULL,1,1,NULL,1,1,1,NULL,'2026-10-27 06:01:57','2026-10-27 06:02:44'),(15,1,'EMP-0002','Nimal Perera','0772345672','45, Station Road, Maharagama',0.00,75000.00,'2026-10-27','Service Advisor','monthly',1,NULL,'bank_transfer','Bank of Ceylon (BOC)','Maharagama','N. Perera','7008812345','EPF-8892',NULL,1,1,NULL,1,1,1,NULL,'2026-10-27 06:02:15','2026-10-27 06:02:15'),(16,1,'EMP-0003','Saman Kumara','0773456783','88, High Level Road, Pannipitiya',3000.00,0.00,'2026-10-27','Junior Mechanic','daily',1,'Apprentice technician on probation.','cash',NULL,NULL,NULL,NULL,'EPF-8893',NULL,1,1,NULL,1,1,1,NULL,'2026-10-27 06:02:30','2026-10-27 06:02:30');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expense_categories`
--

DROP TABLE IF EXISTS `expense_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expense_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expense_categories_name_unique` (`name`),
  KEY `expense_categories_is_active_index` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_categories`
--

LOCK TABLES `expense_categories` WRITE;
/*!40000 ALTER TABLE `expense_categories` DISABLE KEYS */;
INSERT INTO `expense_categories` VALUES (17,'Salary Advance',1,'2026-10-27 06:28:19','2026-10-27 06:28:19'),(18,'Boss Expenses',1,'2026-10-27 06:28:19','2026-10-27 06:28:19'),(19,'Electricity',1,'2026-10-27 06:28:19','2026-10-27 06:28:19'),(20,'EPF / ETF',1,'2026-10-27 06:28:19','2026-10-27 06:28:19'),(21,'Staff Welfare',1,'2026-10-27 06:28:19','2026-10-27 06:28:19'),(22,'Water',1,'2026-10-27 06:28:19','2026-10-27 06:28:19'),(23,'Telephone / Internet',1,'2026-10-27 06:28:19','2026-10-27 06:28:19'),(24,'Chemicals',1,'2026-10-27 06:28:19','2026-10-27 06:28:19'),(25,'Repair',1,'2026-10-27 06:28:19','2026-10-27 06:28:19'),(26,'Maintenance',1,'2026-10-27 06:28:19','2026-10-27 06:28:19'),(27,'Other',1,'2026-10-27 06:28:19','2026-10-27 06:28:19');
/*!40000 ALTER TABLE `expense_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expense_payments`
--

DROP TABLE IF EXISTS `expense_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expense_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `expense_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `payment_number` varchar(50) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'cash',
  `reference_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expense_payments_payment_number_unique` (`payment_number`),
  KEY `expense_payments_expense_id_foreign` (`expense_id`),
  KEY `expense_payments_branch_id_foreign` (`branch_id`),
  KEY `expense_payments_created_by_foreign` (`created_by`),
  KEY `expense_payments_payment_date_index` (`payment_date`),
  CONSTRAINT `expense_payments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expense_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expense_payments_expense_id_foreign` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_payments`
--

LOCK TABLES `expense_payments` WRITE;
/*!40000 ALTER TABLE `expense_payments` DISABLE KEYS */;
INSERT INTO `expense_payments` VALUES (29,28,1,'EPAY-202610-0001','2026-10-27',18500.00,'cash',NULL,'Initial payment at recording',2,'2026-10-27 06:46:50','2026-10-27 06:46:50'),(30,29,1,'EPAY-202610-0002','2026-10-27',12000.00,'cash',NULL,'Initial payment at recording',2,'2026-10-27 06:46:54','2026-10-27 06:46:54'),(31,30,1,'EPAY-202610-0003','2026-10-27',6500.00,'cash',NULL,'Initial payment at recording',2,'2026-10-27 06:46:58','2026-10-27 06:46:58'),(32,31,1,'EPAY-202610-0004','2026-10-27',4500.00,'cash',NULL,'Initial payment at recording',2,'2026-10-27 06:47:03','2026-10-27 06:47:03');
/*!40000 ALTER TABLE `expense_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `expense_number` varchar(50) NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `expense_type` varchar(30) NOT NULL,
  `employee_id` bigint(20) unsigned DEFAULT NULL,
  `employee_advance_id` bigint(20) unsigned DEFAULT NULL,
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
  `voided_by` bigint(20) unsigned DEFAULT NULL,
  `voided_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expenses_expense_number_unique` (`expense_number`),
  KEY `expenses_category_id_foreign` (`category_id`),
  KEY `expenses_employee_id_foreign` (`employee_id`),
  KEY `expenses_employee_advance_id_foreign` (`employee_advance_id`),
  KEY `expenses_voided_by_foreign` (`voided_by`),
  KEY `expenses_created_by_foreign` (`created_by`),
  KEY `expenses_expense_type_index` (`expense_type`),
  KEY `expenses_expense_date_index` (`expense_date`),
  KEY `expenses_related_month_index` (`related_month`),
  KEY `expenses_payment_date_index` (`payment_date`),
  KEY `expenses_status_index` (`status`),
  KEY `expenses_branch_id_foreign` (`branch_id`),
  KEY `expenses_payment_status_index` (`payment_status`),
  CONSTRAINT `expenses_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`),
  CONSTRAINT `expenses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_employee_advance_id_foreign` FOREIGN KEY (`employee_advance_id`) REFERENCES `employee_advances` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_voided_by_foreign` FOREIGN KEY (`voided_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
INSERT INTO `expenses` VALUES (28,1,'EXP-0001',19,'shop',NULL,NULL,'2026-10-27','October 2026','Monthly CEB electricity bill for workshop',18500.00,18500.00,0.00,'paid','cash','2026-10-27',NULL,NULL,'active',NULL,NULL,NULL,2,'2026-10-27 06:46:50','2026-10-27 06:46:50'),(29,1,'EXP-0002',26,'shop',NULL,NULL,'2026-10-27','October 2026','Hydraulic ramp inspection and compressor service',12000.00,12000.00,0.00,'paid','cash','2026-10-27',NULL,NULL,'active',NULL,NULL,NULL,2,'2026-10-27 06:46:54','2026-10-27 06:46:54'),(30,1,'EXP-0003',24,'shop',NULL,NULL,'2026-10-27','October 2026','Workshop floor cleaning chemicals and detergent',6500.00,6500.00,0.00,'paid','cash','2026-10-27',NULL,NULL,'active',NULL,NULL,NULL,2,'2026-10-27 06:46:58','2026-10-27 06:46:58'),(31,1,'EXP-0004',27,'shop',NULL,NULL,'2026-10-27','October 2026','Printer paper, receipt rolls, and stationery',4500.00,4500.00,0.00,'paid','cash','2026-10-27',NULL,NULL,'active',NULL,NULL,NULL,2,'2026-10-27 06:47:03','2026-10-27 06:47:03');
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `goods_received_note_items`
--

DROP TABLE IF EXISTS `goods_received_note_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goods_received_note_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `grn_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `purchase_quantity` decimal(10,2) NOT NULL,
  `free_quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `purchase_unit_price` decimal(12,2) NOT NULL,
  `selling_price_snapshot` decimal(12,2) DEFAULT NULL,
  `line_total` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `goods_received_note_items_grn_id_foreign` (`grn_id`),
  KEY `goods_received_note_items_product_id_foreign` (`product_id`),
  CONSTRAINT `goods_received_note_items_grn_id_foreign` FOREIGN KEY (`grn_id`) REFERENCES `goods_received_notes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `goods_received_note_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goods_received_note_items`
--

LOCK TABLES `goods_received_note_items` WRITE;
/*!40000 ALTER TABLE `goods_received_note_items` DISABLE KEYS */;
INSERT INTO `goods_received_note_items` VALUES (40,26,40,20.00,0.00,2000.00,3200.00,40000.00,'2026-10-27 06:45:51','2026-10-27 06:45:51');
/*!40000 ALTER TABLE `goods_received_note_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `goods_received_notes`
--

DROP TABLE IF EXISTS `goods_received_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goods_received_notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `grn_number` varchar(255) NOT NULL,
  `supplier_id` bigint(20) unsigned NOT NULL,
  `supplier_invoice_number` varchar(255) DEFAULT NULL,
  `received_date` date NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `invoice_attachment` varchar(255) DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `posted_by` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `goods_received_notes_grn_number_unique` (`grn_number`),
  KEY `goods_received_notes_supplier_id_foreign` (`supplier_id`),
  KEY `goods_received_notes_posted_by_foreign` (`posted_by`),
  KEY `goods_received_notes_created_by_foreign` (`created_by`),
  KEY `goods_received_notes_received_date_status_index` (`received_date`,`status`),
  KEY `goods_received_notes_branch_id_foreign` (`branch_id`),
  CONSTRAINT `goods_received_notes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `goods_received_notes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `goods_received_notes_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `goods_received_notes_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goods_received_notes`
--

LOCK TABLES `goods_received_notes` WRITE;
/*!40000 ALTER TABLE `goods_received_notes` DISABLE KEYS */;
INSERT INTO `goods_received_notes` VALUES (26,1,'GRN-202610-0001',13,'INV-CMS-2026-001','2026-10-27','draft',40000.00,NULL,NULL,NULL,NULL,2,'2026-10-27 06:45:51','2026-10-27 06:45:51');
/*!40000 ALTER TABLE `goods_received_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grn_sequences`
--

DROP TABLE IF EXISTS `grn_sequences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grn_sequences` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `series` varchar(20) NOT NULL COMMENT 'YearMonth series e.g. 202610',
  `current_number` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `grn_sequences_series_unique` (`series`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grn_sequences`
--

LOCK TABLES `grn_sequences` WRITE;
/*!40000 ALTER TABLE `grn_sequences` DISABLE KEYS */;
INSERT INTO `grn_sequences` VALUES (2,'202610',1,'2026-10-27 06:45:51','2026-10-27 06:45:51');
/*!40000 ALTER TABLE `grn_sequences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_items`
--

DROP TABLE IF EXISTS `invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint(20) unsigned NOT NULL,
  `source_quotation_item_id` bigint(20) unsigned DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `item_type` enum('service','product','custom') NOT NULL DEFAULT 'service',
  `item_id` bigint(20) unsigned DEFAULT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_items_invoice_id_foreign` (`invoice_id`),
  KEY `invoice_items_source_quotation_item_id_foreign` (`source_quotation_item_id`),
  CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_items_source_quotation_item_id_foreign` FOREIGN KEY (`source_quotation_item_id`) REFERENCES `quotation_items` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_items`
--

LOCK TABLES `invoice_items` WRITE;
/*!40000 ALTER TABLE `invoice_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_sequences`
--

DROP TABLE IF EXISTS `invoice_sequences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice_sequences` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `series` varchar(20) NOT NULL COMMENT 'YearMonth series e.g. 202610',
  `current_number` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_sequences_series_unique` (`series`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_sequences`
--

LOCK TABLES `invoice_sequences` WRITE;
/*!40000 ALTER TABLE `invoice_sequences` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_sequences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `invoice_number` varchar(100) NOT NULL,
  `invoice_date` date NOT NULL,
  `original_invoice_date` date DEFAULT NULL,
  `date_changed_by` bigint(20) unsigned DEFAULT NULL,
  `date_changed_at` timestamp NULL DEFAULT NULL,
  `date_change_reason` text DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `quotation_id` bigint(20) unsigned DEFAULT NULL,
  `job_card_id` bigint(20) unsigned DEFAULT NULL,
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
  `cancelled_by` bigint(20) unsigned DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `terms_conditions` text DEFAULT NULL,
  `customer_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`customer_snapshot`)),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  KEY `invoices_customer_id_foreign` (`customer_id`),
  KEY `invoices_created_by_foreign` (`created_by`),
  KEY `invoices_job_card_id_index` (`job_card_id`),
  KEY `invoices_invoice_type_index` (`invoice_type`),
  KEY `invoices_status_index` (`status`),
  KEY `invoices_branch_id_foreign` (`branch_id`),
  KEY `invoices_quotation_id_foreign` (`quotation_id`),
  KEY `invoices_cancelled_by_foreign` (`cancelled_by`),
  KEY `invoices_date_changed_by_foreign` (`date_changed_by`),
  CONSTRAINT `invoices_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_date_changed_by_foreign` FOREIGN KEY (`date_changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_card_carpets`
--

DROP TABLE IF EXISTS `job_card_carpets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_card_carpets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `job_card_id` bigint(20) unsigned NOT NULL,
  `carpet_category_id` bigint(20) unsigned DEFAULT NULL,
  `category_name` varchar(100) NOT NULL,
  `quantity` smallint(5) unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_card_carpets_job_card_id_foreign` (`job_card_id`),
  KEY `job_card_carpets_carpet_category_id_foreign` (`carpet_category_id`),
  CONSTRAINT `job_card_carpets_carpet_category_id_foreign` FOREIGN KEY (`carpet_category_id`) REFERENCES `carpet_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_card_carpets_job_card_id_foreign` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_card_carpets`
--

LOCK TABLES `job_card_carpets` WRITE;
/*!40000 ALTER TABLE `job_card_carpets` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_card_carpets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_card_products`
--

DROP TABLE IF EXISTS `job_card_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_card_products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `job_card_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `product_sku` varchar(50) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` decimal(12,2) NOT NULL DEFAULT 1.00,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unit_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `is_price_pending` tinyint(1) NOT NULL DEFAULT 0,
  `is_stock_deducted` tinyint(1) NOT NULL DEFAULT 0,
  `is_additional` tinyint(1) NOT NULL DEFAULT 0,
  `job_issue_id` bigint(20) unsigned DEFAULT NULL,
  `invoice_id` bigint(20) unsigned DEFAULT NULL,
  `batch_allocations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`batch_allocations`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_card_products_job_card_id_foreign` (`job_card_id`),
  KEY `job_card_products_product_id_foreign` (`product_id`),
  KEY `job_card_products_job_issue_id_foreign` (`job_issue_id`),
  KEY `job_card_products_invoice_id_foreign` (`invoice_id`),
  CONSTRAINT `job_card_products_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_card_products_job_card_id_foreign` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards` (`id`) ON DELETE CASCADE,
  CONSTRAINT `job_card_products_job_issue_id_foreign` FOREIGN KEY (`job_issue_id`) REFERENCES `job_issues` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_card_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_card_products`
--

LOCK TABLES `job_card_products` WRITE;
/*!40000 ALTER TABLE `job_card_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_card_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_card_services`
--

DROP TABLE IF EXISTS `job_card_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_card_services` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `job_card_id` bigint(20) unsigned NOT NULL,
  `service_id` bigint(20) unsigned NOT NULL,
  `service_code` varchar(50) DEFAULT NULL,
  `service_name` varchar(255) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `quantity` decimal(8,2) NOT NULL DEFAULT 1.00,
  `line_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `is_price_custom` tinyint(1) NOT NULL DEFAULT 0,
  `is_price_pending` tinyint(1) NOT NULL DEFAULT 0,
  `is_additional` tinyint(1) NOT NULL DEFAULT 0,
  `job_issue_id` bigint(20) unsigned DEFAULT NULL,
  `invoice_id` bigint(20) unsigned DEFAULT NULL,
  `technician_name` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_card_services_job_card_id_foreign` (`job_card_id`),
  KEY `job_card_services_service_id_foreign` (`service_id`),
  KEY `job_card_services_job_issue_id_foreign` (`job_issue_id`),
  KEY `job_card_services_invoice_id_foreign` (`invoice_id`),
  CONSTRAINT `job_card_services_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_card_services_job_card_id_foreign` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards` (`id`) ON DELETE CASCADE,
  CONSTRAINT `job_card_services_job_issue_id_foreign` FOREIGN KEY (`job_issue_id`) REFERENCES `job_issues` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_card_services_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_card_services`
--

LOCK TABLES `job_card_services` WRITE;
/*!40000 ALTER TABLE `job_card_services` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_card_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_cards`
--

DROP TABLE IF EXISTS `job_cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_cards` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `job_number` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `vehicle_id` bigint(20) unsigned NOT NULL,
  `vehicle_type_id` bigint(20) unsigned NOT NULL,
  `current_mileage` int(10) unsigned NOT NULL DEFAULT 0,
  `next_service_mileage` int(10) unsigned DEFAULT NULL,
  `status` enum('pending','in_progress','additional_work','completed','delivered','cancelled') DEFAULT 'pending',
  `assigned_to` bigint(20) unsigned DEFAULT NULL,
  `driver_name` varchar(150) DEFAULT NULL,
  `driver_mobile` varchar(50) DEFAULT NULL,
  `service_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `product_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `invoice_id` bigint(20) unsigned DEFAULT NULL,
  `appointment_id` bigint(20) unsigned DEFAULT NULL,
  `customer_notes` text DEFAULT NULL,
  `service_notes` text DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `cancelled_by` bigint(20) unsigned DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `completed_by` bigint(20) unsigned DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `job_cards_job_number_unique` (`job_number`),
  KEY `job_cards_customer_id_foreign` (`customer_id`),
  KEY `job_cards_vehicle_id_foreign` (`vehicle_id`),
  KEY `job_cards_vehicle_type_id_foreign` (`vehicle_type_id`),
  KEY `job_cards_assigned_to_foreign` (`assigned_to`),
  KEY `job_cards_invoice_id_foreign` (`invoice_id`),
  KEY `job_cards_cancelled_by_foreign` (`cancelled_by`),
  KEY `job_cards_created_by_foreign` (`created_by`),
  KEY `job_cards_status_index` (`status`),
  KEY `job_cards_branch_id_foreign` (`branch_id`),
  KEY `job_cards_appointment_id_foreign` (`appointment_id`),
  KEY `job_cards_completed_by_foreign` (`completed_by`),
  CONSTRAINT `job_cards_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_cards_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_cards_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_cards_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_cards_completed_by_foreign` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_cards_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_cards_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `job_cards_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_cards_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `job_cards_vehicle_type_id_foreign` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_cards`
--

LOCK TABLES `job_cards` WRITE;
/*!40000 ALTER TABLE `job_cards` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_cards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_issues`
--

DROP TABLE IF EXISTS `job_issues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_issues` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `job_card_id` bigint(20) unsigned NOT NULL,
  `vehicle_id` bigint(20) unsigned NOT NULL,
  `description` text NOT NULL,
  `recommended_repair` text NOT NULL,
  `estimated_cost` decimal(15,2) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('pending_approval','approved','declined','fixed','deferred') NOT NULL DEFAULT 'pending_approval',
  `approval_method` enum('verbal','phone','written','whatsapp') DEFAULT NULL,
  `approval_contact` varchar(100) DEFAULT NULL,
  `approval_note` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `recorded_by` bigint(20) unsigned DEFAULT NULL,
  `supplementary_invoice_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_issues_job_card_id_foreign` (`job_card_id`),
  KEY `job_issues_vehicle_id_foreign` (`vehicle_id`),
  KEY `job_issues_approved_by_foreign` (`approved_by`),
  KEY `job_issues_recorded_by_foreign` (`recorded_by`),
  KEY `job_issues_supplementary_invoice_id_foreign` (`supplementary_invoice_id`),
  KEY `job_issues_status_index` (`status`),
  CONSTRAINT `job_issues_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_issues_job_card_id_foreign` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards` (`id`) ON DELETE CASCADE,
  CONSTRAINT `job_issues_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_issues_supplementary_invoice_id_foreign` FOREIGN KEY (`supplementary_invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_issues_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_issues`
--

LOCK TABLES `job_issues` WRITE;
/*!40000 ALTER TABLE `job_issues` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_issues` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_10_19_112550_create_services_table',1),(5,'2026_10_19_112551_create_vehicle_types_table',1),(6,'2026_10_19_112552_create_company_settings_table',1),(7,'2026_10_19_112553_create_activity_logs_table',1),(8,'2026_10_19_123022_create_service_vehicle_prices_table',2),(9,'2026_10_19_123508_add_image_to_vehicle_types_table',3),(10,'2026_10_19_130001_create_suppliers_table',4),(11,'2026_10_19_130002_create_units_of_measurement_table',4),(12,'2026_10_19_130003_create_products_table',4),(13,'2026_10_19_130004_create_goods_received_notes_table',4),(14,'2026_10_19_130005_create_goods_received_note_items_table',4),(15,'2026_10_19_130006_create_stock_batches_table',4),(16,'2026_10_19_130007_create_stock_movements_table',4),(17,'2026_10_19_130008_create_supplier_payments_table',4),(18,'2026_10_20_050001_create_customers_table',5),(19,'2026_10_20_050002_create_vehicles_table',5),(20,'2026_10_20_050003_create_invoices_table',5),(21,'2026_10_20_050004_create_invoice_items_table',5),(22,'2026_10_20_050005_create_payments_table',5),(23,'2026_10_20_050006_create_job_cards_table',5),(24,'2026_10_20_050007_create_job_card_services_table',5),(25,'2026_10_20_050008_create_job_card_products_table',5),(26,'2026_10_20_050009_create_direct_sales_table',5),(27,'2026_10_20_050010_create_direct_sale_items_table',5),(28,'2026_10_20_060001_create_employees_table',6),(29,'2026_10_20_060002_create_employee_wage_rates_table',6),(30,'2026_10_20_060003_create_employee_attendance_table',6),(31,'2026_10_20_060004_create_payroll_periods_table',6),(32,'2026_10_20_060005_create_payroll_items_table',6),(33,'2026_10_20_060006_create_employee_advances_table',6),(34,'2026_10_20_060007_create_payroll_payments_table',6),(35,'2026_10_20_070001_create_expense_categories_table',7),(36,'2026_10_20_070002_create_expenses_table',7),(37,'2026_10_20_080001_create_branches_table',8),(38,'2026_10_20_080002_create_branch_user_table',8),(39,'2026_10_20_080003_add_branch_id_to_existing_modules_tables',8),(40,'2026_10_20_090001_create_quotations_table',9),(41,'2026_10_20_090002_create_quotation_items_table',9),(42,'2026_10_20_090003_add_quotation_fields_to_invoices_table',9),(43,'2026_10_20_090004_add_quotation_fields_to_invoice_items_table',9),(44,'2026_10_20_100001_create_stage6_sales_and_payments_tables',10),(45,'2026_10_20_103542_add_details_to_supplier_payments_table',11),(46,'2026_10_20_111258_add_image_path_to_products_table',12),(47,'2026_10_20_120001_create_customer_credit_accounts_table',13),(48,'2026_10_20_090001_create_job_issues_table',14),(49,'2026_10_20_090002_add_additional_work_fields_to_job_cards_tables',14),(50,'2026_10_20_090003_create_appointments_table',14),(51,'2026_10_21_160000_create_invoice_sequences_and_job_card_payments',15),(52,'2026_10_21_210000_create_carpet_details_and_vehicle_type_settings',16),(53,'2026_10_21_220000_add_payment_tracking_to_expenses_table',17),(54,'2026_10_22_072000_create_printer_settings_and_logs_tables',18),(55,'2026_10_22_043812_add_signature_path_to_company_settings_table',19),(56,'2026_10_22_120000_update_direct_sales_and_payments_for_sales_bill',20),(57,'2026_10_22_130000_add_company_vehicle_and_driver_columns',21),(58,'2026_10_23_150000_create_grn_sequences_table',22),(59,'2026_10_26_085854_add_price_pending_and_completed_by_columns',23),(60,'2026_10_27_074754_add_salary_settings_to_employees_and_wage_rates_tables',24),(61,'2026_10_27_083000_create_company_holidays_tables',25),(62,'2026_10_27_090000_add_off_days_and_no_pay_to_payroll_items_table',25),(63,'2026_10_27_093000_create_salary_advance_management_tables',25),(64,'2026_10_27_103000_create_bonuses_allowances_overtime_tables',26),(65,'2026_10_27_120000_create_statutory_and_tax_support_tables',27);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_allocations`
--

DROP TABLE IF EXISTS `payment_allocations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_allocations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_receipt_id` bigint(20) unsigned NOT NULL,
  `invoice_id` bigint(20) unsigned NOT NULL,
  `allocated_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_allocations_invoice_id_foreign` (`invoice_id`),
  KEY `payment_allocations_payment_receipt_id_invoice_id_index` (`payment_receipt_id`,`invoice_id`),
  CONSTRAINT `payment_allocations_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_allocations_payment_receipt_id_foreign` FOREIGN KEY (`payment_receipt_id`) REFERENCES `payment_receipts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_allocations`
--

LOCK TABLES `payment_allocations` WRITE;
/*!40000 ALTER TABLE `payment_allocations` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_allocations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_receipts`
--

DROP TABLE IF EXISTS `payment_receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_receipts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `receipt_number` varchar(50) NOT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `receipt_date` date NOT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) NOT NULL DEFAULT 'cash',
  `reference_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_receipts_receipt_number_unique` (`receipt_number`),
  KEY `payment_receipts_branch_id_foreign` (`branch_id`),
  KEY `payment_receipts_customer_id_foreign` (`customer_id`),
  KEY `payment_receipts_created_by_foreign` (`created_by`),
  CONSTRAINT `payment_receipts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_receipts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_receipts_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_receipts`
--

LOCK TABLES `payment_receipts` WRITE;
/*!40000 ALTER TABLE `payment_receipts` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_receipts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint(20) unsigned DEFAULT NULL,
  `job_card_id` bigint(20) unsigned DEFAULT NULL,
  `direct_sale_id` bigint(20) unsigned DEFAULT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `payment_receipt_id` bigint(20) unsigned DEFAULT NULL,
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
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_payment_number_unique` (`payment_number`),
  KEY `payments_invoice_id_foreign` (`invoice_id`),
  KEY `payments_customer_id_foreign` (`customer_id`),
  KEY `payments_created_by_foreign` (`created_by`),
  KEY `payments_payment_receipt_id_foreign` (`payment_receipt_id`),
  KEY `payments_job_card_id_foreign` (`job_card_id`),
  KEY `payments_direct_sale_id_foreign` (`direct_sale_id`),
  CONSTRAINT `payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_direct_sale_id_foreign` FOREIGN KEY (`direct_sale_id`) REFERENCES `direct_sales` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_job_card_id_foreign` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_payment_receipt_id_foreign` FOREIGN KEY (`payment_receipt_id`) REFERENCES `payment_receipts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_items`
--

DROP TABLE IF EXISTS `payroll_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payroll_period_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `employee_name_snapshot` varchar(150) NOT NULL,
  `daily_wage_snapshot` decimal(12,2) NOT NULL,
  `salary_type_snapshot` varchar(20) NOT NULL DEFAULT 'daily',
  `monthly_basic_snapshot` decimal(12,2) NOT NULL DEFAULT 0.00,
  `full_days_count` smallint(5) unsigned NOT NULL DEFAULT 0,
  `half_days_count` smallint(5) unsigned NOT NULL DEFAULT 0,
  `absent_days_count` smallint(5) unsigned NOT NULL DEFAULT 0,
  `leave_days_count` smallint(5) unsigned NOT NULL DEFAULT 0,
  `day_off_days_count` smallint(5) unsigned NOT NULL DEFAULT 0,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_period_employee` (`payroll_period_id`,`employee_id`),
  KEY `payroll_items_employee_id_foreign` (`employee_id`),
  KEY `payroll_items_status_index` (`status`),
  CONSTRAINT `payroll_items_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_items_payroll_period_id_foreign` FOREIGN KEY (`payroll_period_id`) REFERENCES `payroll_periods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_items`
--

LOCK TABLES `payroll_items` WRITE;
/*!40000 ALTER TABLE `payroll_items` DISABLE KEYS */;
INSERT INTO `payroll_items` VALUES (17,4,14,'Kamal Ranjith',0.00,'monthly',85000.00,1,0,0,0,0,0.00,0.00,0.00,0.00,31.00,85000.00,0.00,0.00,0.00,0.00,0.00,85000.00,85000.00,85000.00,6800.00,10200.00,2550.00,0.00,0.00,85000.00,78200.00,0.00,78200.00,'draft','{\"summary\":{\"employee_id\":14,\"employee_name\":\"Kamal Ranjith\",\"employee_number\":\"EMP-0001\",\"salary_type\":\"monthly\",\"daily_wage\":0,\"monthly_basic_salary\":85000,\"is_eligible_for_off_days\":true,\"days_in_month\":31,\"no_pay_divisor\":31,\"no_pay_divisor_type\":\"calendar_days\",\"full_days_count\":1,\"half_days_count\":0,\"absent_days_count\":0,\"explicit_day_off_count\":0,\"paid_leave_count\":0,\"unpaid_leave_count\":0,\"leave_days_count\":0,\"approved_leave_count\":0,\"missing_attendance_count\":4,\"statutory_rest_days_count\":0,\"paid_holidays_count\":0,\"unpaid_holidays_count\":0,\"total_absence_taken\":0,\"company_off_days_used\":0,\"no_pay_days_count\":0,\"no_pay_daily_rate\":2741.94,\"no_pay_deduction\":0,\"payable_days\":31,\"earned_attendance_wage\":0,\"paid_holiday_wage\":0,\"base_earnings\":85000,\"overtime_amount\":0,\"bonus_amount\":0,\"allowances_amount\":0,\"epf_qualifying_salary\":85000,\"etf_qualifying_salary\":85000,\"apit_taxable_earnings\":85000,\"apit_amount\":0},\"reconciled_days\":[{\"date\":\"2026-10-01\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-02\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-03\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-04\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-05\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-06\",\"day_name\":\"Tue\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-07\",\"day_name\":\"Wed\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-08\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-09\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-10\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-11\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-12\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-13\",\"day_name\":\"Tue\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-14\",\"day_name\":\"Wed\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-15\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-16\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-17\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-18\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-19\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-20\",\"day_name\":\"Tue\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-21\",\"day_name\":\"Wed\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-22\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-23\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-24\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-25\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-26\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-27\",\"day_name\":\"Tue\",\"status\":\"full_day\",\"category\":\"Worked Full Day\",\"fraction\":1,\"wage\":0,\"earnings\":0,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-28\",\"day_name\":\"Wed\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-29\",\"day_name\":\"Thu\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-30\",\"day_name\":\"Fri\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-31\",\"day_name\":\"Sat\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"holiday_name\":null,\"notes\":null}],\"planned_advance_recoveries\":[],\"bonuses_breakdown\":[],\"allowances_breakdown\":[],\"overtime_breakdown\":[],\"statutory_breakdown\":{\"epf_qualifying_salary\":85000,\"etf_qualifying_salary\":85000,\"epf_employee_rate\":8,\"epf_employer_rate\":12,\"etf_employer_rate\":3,\"epf_employee_amount\":6800,\"epf_employer_amount\":10200,\"etf_employer_amount\":2550,\"apit_taxable_earnings\":85000,\"apit_tax_relief\":150000,\"apit_taxable_income\":0,\"apit_amount\":0,\"apit_slabs_breakdown\":[],\"flagged_for_admin_review\":[]}}',NULL,'2026-10-27 06:21:30','2026-10-27 06:21:30'),(18,4,15,'Nimal Perera',0.00,'monthly',75000.00,0,1,0,0,1,0.50,0.00,0.00,0.00,31.00,75000.00,0.00,0.00,0.00,0.00,0.00,75000.00,75000.00,75000.00,6000.00,9000.00,2250.00,0.00,0.00,75000.00,69000.00,0.00,69000.00,'draft','{\"summary\":{\"employee_id\":15,\"employee_name\":\"Nimal Perera\",\"employee_number\":\"EMP-0002\",\"salary_type\":\"monthly\",\"daily_wage\":0,\"monthly_basic_salary\":75000,\"is_eligible_for_off_days\":true,\"days_in_month\":31,\"no_pay_divisor\":31,\"no_pay_divisor_type\":\"calendar_days\",\"full_days_count\":0,\"half_days_count\":1,\"absent_days_count\":0,\"explicit_day_off_count\":0,\"paid_leave_count\":0,\"unpaid_leave_count\":0,\"leave_days_count\":0,\"approved_leave_count\":0,\"missing_attendance_count\":4,\"statutory_rest_days_count\":0,\"paid_holidays_count\":0,\"unpaid_holidays_count\":0,\"total_absence_taken\":0.5,\"company_off_days_used\":0.5,\"no_pay_days_count\":0,\"no_pay_daily_rate\":2419.35,\"no_pay_deduction\":0,\"payable_days\":31,\"earned_attendance_wage\":0,\"paid_holiday_wage\":0,\"base_earnings\":75000,\"overtime_amount\":0,\"bonus_amount\":0,\"allowances_amount\":0,\"epf_qualifying_salary\":75000,\"etf_qualifying_salary\":75000,\"apit_taxable_earnings\":75000,\"apit_amount\":0},\"reconciled_days\":[{\"date\":\"2026-10-01\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-02\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-03\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-04\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-05\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-06\",\"day_name\":\"Tue\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-07\",\"day_name\":\"Wed\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-08\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-09\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-10\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-11\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-12\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-13\",\"day_name\":\"Tue\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-14\",\"day_name\":\"Wed\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-15\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-16\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-17\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-18\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-19\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-20\",\"day_name\":\"Tue\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-21\",\"day_name\":\"Wed\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-22\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-23\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-24\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-25\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-26\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-27\",\"day_name\":\"Tue\",\"status\":\"half_day\",\"category\":\"Worked Half Day\",\"fraction\":0.5,\"wage\":0,\"earnings\":0,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-28\",\"day_name\":\"Wed\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-29\",\"day_name\":\"Thu\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-30\",\"day_name\":\"Fri\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-31\",\"day_name\":\"Sat\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"holiday_name\":null,\"notes\":null}],\"planned_advance_recoveries\":[],\"bonuses_breakdown\":[],\"allowances_breakdown\":[],\"overtime_breakdown\":[],\"statutory_breakdown\":{\"epf_qualifying_salary\":75000,\"etf_qualifying_salary\":75000,\"epf_employee_rate\":8,\"epf_employer_rate\":12,\"etf_employer_rate\":3,\"epf_employee_amount\":6000,\"epf_employer_amount\":9000,\"etf_employer_amount\":2250,\"apit_taxable_earnings\":75000,\"apit_tax_relief\":150000,\"apit_taxable_income\":0,\"apit_amount\":0,\"apit_slabs_breakdown\":[],\"flagged_for_admin_review\":[]}}',NULL,'2026-10-27 06:21:30','2026-10-27 06:21:30'),(19,4,16,'Saman Kumara',3000.00,'daily',0.00,1,0,0,0,0,0.00,0.00,0.00,0.00,1.00,3000.00,0.00,0.00,0.00,0.00,0.00,3000.00,3000.00,3000.00,240.00,360.00,90.00,0.00,0.00,3000.00,2760.00,0.00,2760.00,'draft','{\"summary\":{\"employee_id\":16,\"employee_name\":\"Saman Kumara\",\"employee_number\":\"EMP-0003\",\"salary_type\":\"daily\",\"daily_wage\":3000,\"monthly_basic_salary\":0,\"is_eligible_for_off_days\":true,\"days_in_month\":31,\"no_pay_divisor\":31,\"no_pay_divisor_type\":\"calendar_days\",\"full_days_count\":1,\"half_days_count\":0,\"absent_days_count\":0,\"explicit_day_off_count\":0,\"paid_leave_count\":0,\"unpaid_leave_count\":0,\"leave_days_count\":0,\"approved_leave_count\":0,\"missing_attendance_count\":4,\"statutory_rest_days_count\":0,\"paid_holidays_count\":0,\"unpaid_holidays_count\":0,\"total_absence_taken\":0,\"company_off_days_used\":0,\"no_pay_days_count\":0,\"no_pay_daily_rate\":3000,\"no_pay_deduction\":0,\"payable_days\":1,\"earned_attendance_wage\":3000,\"paid_holiday_wage\":0,\"base_earnings\":3000,\"overtime_amount\":0,\"bonus_amount\":0,\"allowances_amount\":0,\"epf_qualifying_salary\":3000,\"etf_qualifying_salary\":3000,\"apit_taxable_earnings\":3000,\"apit_amount\":0},\"reconciled_days\":[{\"date\":\"2026-10-01\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-02\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-03\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-04\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-05\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-06\",\"day_name\":\"Tue\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-07\",\"day_name\":\"Wed\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-08\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-09\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-10\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-11\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-12\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-13\",\"day_name\":\"Tue\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-14\",\"day_name\":\"Wed\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-15\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-16\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-17\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-18\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-19\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-20\",\"day_name\":\"Tue\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-21\",\"day_name\":\"Wed\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-22\",\"day_name\":\"Thu\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-23\",\"day_name\":\"Fri\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-24\",\"day_name\":\"Sat\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-25\",\"day_name\":\"Sun\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-26\",\"day_name\":\"Mon\",\"status\":\"not_employed\",\"category\":\"Not Employed\",\"fraction\":0,\"wage\":0,\"earnings\":0,\"notes\":\"Prior to employee joining date\"},{\"date\":\"2026-10-27\",\"day_name\":\"Tue\",\"status\":\"full_day\",\"category\":\"Worked Full Day\",\"fraction\":1,\"wage\":3000,\"earnings\":3000,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-28\",\"day_name\":\"Wed\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":3000,\"earnings\":0,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-29\",\"day_name\":\"Thu\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":3000,\"earnings\":0,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-30\",\"day_name\":\"Fri\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":3000,\"earnings\":0,\"holiday_name\":null,\"notes\":null},{\"date\":\"2026-10-31\",\"day_name\":\"Sat\",\"status\":\"missing_attendance\",\"category\":\"Missing Attendance\",\"fraction\":0,\"wage\":3000,\"earnings\":0,\"holiday_name\":null,\"notes\":null}],\"planned_advance_recoveries\":[],\"bonuses_breakdown\":[],\"allowances_breakdown\":[],\"overtime_breakdown\":[],\"statutory_breakdown\":{\"epf_qualifying_salary\":3000,\"etf_qualifying_salary\":3000,\"epf_employee_rate\":8,\"epf_employer_rate\":12,\"etf_employer_rate\":3,\"epf_employee_amount\":240,\"epf_employer_amount\":360,\"etf_employer_amount\":90,\"apit_taxable_earnings\":3000,\"apit_tax_relief\":150000,\"apit_taxable_income\":0,\"apit_amount\":0,\"apit_slabs_breakdown\":[],\"flagged_for_admin_review\":[]}}',NULL,'2026-10-27 06:21:30','2026-10-27 06:21:30');
/*!40000 ALTER TABLE `payroll_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_payments`
--

DROP TABLE IF EXISTS `payroll_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payroll_item_id` bigint(20) unsigned NOT NULL,
  `payment_number` varchar(50) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'cash',
  `reference_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `recorded_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_payments_payment_number_unique` (`payment_number`),
  KEY `payroll_payments_payroll_item_id_foreign` (`payroll_item_id`),
  KEY `payroll_payments_recorded_by_foreign` (`recorded_by`),
  KEY `payroll_payments_payment_date_index` (`payment_date`),
  CONSTRAINT `payroll_payments_payroll_item_id_foreign` FOREIGN KEY (`payroll_item_id`) REFERENCES `payroll_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_payments_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_payments`
--

LOCK TABLES `payroll_payments` WRITE;
/*!40000 ALTER TABLE `payroll_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_periods`
--

DROP TABLE IF EXISTS `payroll_periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_periods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `year` smallint(5) unsigned NOT NULL,
  `month` tinyint(3) unsigned NOT NULL,
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
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_payroll_year_month` (`year`,`month`),
  KEY `payroll_periods_approved_by_foreign` (`approved_by`),
  KEY `payroll_periods_year_index` (`year`),
  KEY `payroll_periods_month_index` (`month`),
  KEY `payroll_periods_status_index` (`status`),
  CONSTRAINT `payroll_periods_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_periods`
--

LOCK TABLES `payroll_periods` WRITE;
/*!40000 ALTER TABLE `payroll_periods` DISABLE KEYS */;
INSERT INTO `payroll_periods` VALUES (4,2026,10,'draft',163000.00,0.00,0.00,13040.00,19560.00,4890.00,0.00,13040.00,0.00,163000.00,149960.00,0.00,NULL,NULL,NULL,'2026-10-27 06:21:30','2026-10-27 06:21:30');
/*!40000 ALTER TABLE `payroll_periods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_statutory_settings`
--

DROP TABLE IF EXISTS `payroll_statutory_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_statutory_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `epf_employee_rate` decimal(5,2) NOT NULL DEFAULT 8.00,
  `epf_employer_rate` decimal(5,2) NOT NULL DEFAULT 12.00,
  `etf_employer_rate` decimal(5,2) NOT NULL DEFAULT 3.00,
  `no_pay_divisor_type` varchar(30) NOT NULL DEFAULT 'calendar_days',
  `no_pay_custom_divisor` int(10) unsigned DEFAULT NULL,
  `overtime_normal_multiplier` decimal(4,2) NOT NULL DEFAULT 1.50,
  `overtime_holiday_multiplier` decimal(4,2) NOT NULL DEFAULT 2.00,
  `overtime_monthly_divisor_hours` decimal(5,2) NOT NULL DEFAULT 200.00,
  `apit_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `apit_monthly_relief` decimal(12,2) NOT NULL DEFAULT 150000.00,
  `apit_tax_slabs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`apit_tax_slabs`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payroll_statutory_settings_created_by_foreign` (`created_by`),
  KEY `payroll_statutory_settings_updated_by_foreign` (`updated_by`),
  KEY `payroll_statutory_settings_effective_from_index` (`effective_from`),
  KEY `payroll_statutory_settings_effective_to_index` (`effective_to`),
  KEY `payroll_statutory_settings_is_active_index` (`is_active`),
  CONSTRAINT `payroll_statutory_settings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payroll_statutory_settings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_statutory_settings`
--

LOCK TABLES `payroll_statutory_settings` WRITE;
/*!40000 ALTER TABLE `payroll_statutory_settings` DISABLE KEYS */;
INSERT INTO `payroll_statutory_settings` VALUES (1,'2023-01-01','2025-03-31',8.00,12.00,3.00,'calendar_days',NULL,1.50,2.00,200.00,1,100000.00,'[{\"slab\":1,\"limit\":41666.67,\"rate\":6},{\"slab\":2,\"limit\":41666.67,\"rate\":12},{\"slab\":3,\"limit\":41666.67,\"rate\":18},{\"slab\":4,\"limit\":41666.67,\"rate\":24},{\"slab\":5,\"limit\":41666.67,\"rate\":30},{\"slab\":6,\"limit\":null,\"rate\":36}]',1,'Sri Lanka Inland Revenue Department (IRD) APIT Table 01 (Pre-April 2025, YA 2023/24 - 2024/25)',NULL,NULL,'2026-10-27 05:24:31','2026-10-27 05:24:31'),(2,'2025-04-01',NULL,8.00,12.00,3.00,'calendar_days',NULL,1.50,2.00,200.00,1,150000.00,'[{\"slab\":1,\"limit\":83333.33,\"rate\":6},{\"slab\":2,\"limit\":41666.67,\"rate\":18},{\"slab\":3,\"limit\":41666.67,\"rate\":24},{\"slab\":4,\"limit\":41666.67,\"rate\":30},{\"slab\":5,\"limit\":null,\"rate\":36}]',1,'Sri Lanka Inland Revenue Department (IRD) APIT Table 01 (Effective April 1, 2025 onwards, YA 2025/26)',NULL,NULL,'2026-10-27 05:24:31','2026-10-27 05:24:31');
/*!40000 ALTER TABLE `payroll_statutory_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `print_logs`
--

DROP TABLE IF EXISTS `print_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `print_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `workstation_name` varchar(100) DEFAULT NULL,
  `document_type` varchar(50) NOT NULL,
  `document_id` bigint(20) unsigned DEFAULT NULL,
  `document_number` varchar(100) DEFAULT NULL,
  `printer_name` varchar(255) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'submitted',
  `error_message` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `print_logs_user_id_foreign` (`user_id`),
  KEY `print_logs_document_type_status_index` (`document_type`,`status`),
  KEY `print_logs_branch_id_created_at_index` (`branch_id`,`created_at`),
  CONSTRAINT `print_logs_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `print_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `print_logs`
--

LOCK TABLES `print_logs` WRITE;
/*!40000 ALTER TABLE `print_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `print_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `printer_settings`
--

DROP TABLE IF EXISTS `printer_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `printer_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `workstation_name` varchar(100) NOT NULL DEFAULT 'Default-Workstation',
  `bill_printer` varchar(255) DEFAULT NULL,
  `job_card_printer` varchar(255) DEFAULT NULL,
  `paper_size` varchar(50) NOT NULL DEFAULT '80mm',
  `auto_route` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `printer_settings_branch_id_workstation_name_index` (`branch_id`,`workstation_name`),
  CONSTRAINT `printer_settings_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `printer_settings`
--

LOCK TABLES `printer_settings` WRITE;
/*!40000 ALTER TABLE `printer_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `printer_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `uom_id` bigint(20) unsigned NOT NULL,
  `preferred_supplier_id` bigint(20) unsigned DEFAULT NULL,
  `min_stock` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_stock` decimal(10,2) DEFAULT NULL,
  `selling_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `image_path` varchar(2048) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  KEY `products_uom_id_foreign` (`uom_id`),
  KEY `products_preferred_supplier_id_foreign` (`preferred_supplier_id`),
  KEY `products_name_sku_index` (`name`,`sku`),
  CONSTRAINT `products_preferred_supplier_id_foreign` FOREIGN KEY (`preferred_supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_uom_id_foreign` FOREIGN KEY (`uom_id`) REFERENCES `units_of_measurement` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (34,'FLT-TOY-001','Toyota Genuine Engine Oil Filter',NULL,4,NULL,5.00,NULL,2600.00,NULL,1,'2026-10-27 06:45:05','2026-10-27 06:45:05'),(35,'OIL-TOY-0W20','Toyota Engine Oil 0W-20',NULL,2,NULL,5.00,NULL,18500.00,NULL,1,'2026-10-27 06:45:10','2026-10-27 06:45:10'),(36,'FLT-AIR-001','Toyota Genuine Air Filter',NULL,4,NULL,5.00,NULL,3800.00,NULL,1,'2026-10-27 06:45:16','2026-10-27 06:45:16'),(37,'BRK-TOY-001','Toyota Genuine Brake Pads',NULL,4,NULL,5.00,NULL,12500.00,NULL,1,'2026-10-27 06:45:22','2026-10-27 06:45:22'),(38,'LUB-CLT-001','Toyota Long Life Coolant',NULL,1,NULL,5.00,NULL,6200.00,NULL,1,'2026-10-27 06:45:28','2026-10-27 06:45:28'),(39,'IGN-SPK-001','Denso Spark Plug',NULL,4,NULL,5.00,NULL,2400.00,NULL,1,'2026-10-27 06:45:34','2026-10-27 06:45:34'),(40,'ACC-WPR-001','Bosch Wiper Blade',NULL,4,NULL,5.00,NULL,3200.00,NULL,1,'2026-10-27 06:45:39','2026-10-27 06:45:39'),(41,'IGN-SPK-002','NGK Spark Plug',NULL,4,NULL,5.00,NULL,2200.00,NULL,1,'2026-10-27 06:45:45','2026-10-27 06:45:45');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quotation_items`
--

DROP TABLE IF EXISTS `quotation_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quotation_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint(20) unsigned NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `item_type` enum('service','product','custom') NOT NULL DEFAULT 'service',
  `item_id` bigint(20) unsigned DEFAULT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quotation_items_quotation_id_foreign` (`quotation_id`),
  KEY `quotation_items_item_id_index` (`item_id`),
  CONSTRAINT `quotation_items_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quotation_items`
--

LOCK TABLES `quotation_items` WRITE;
/*!40000 ALTER TABLE `quotation_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `quotation_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quotations`
--

DROP TABLE IF EXISTS `quotations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quotations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `vehicle_id` bigint(20) unsigned DEFAULT NULL,
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
  `converted_invoice_id` bigint(20) unsigned DEFAULT NULL,
  `converted_job_card_id` bigint(20) unsigned DEFAULT NULL,
  `customer_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`customer_snapshot`)),
  `vehicle_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`vehicle_snapshot`)),
  `company_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`company_snapshot`)),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quotations_quotation_number_unique` (`quotation_number`),
  KEY `quotations_branch_id_foreign` (`branch_id`),
  KEY `quotations_customer_id_foreign` (`customer_id`),
  KEY `quotations_vehicle_id_foreign` (`vehicle_id`),
  KEY `quotations_created_by_foreign` (`created_by`),
  KEY `quotations_status_index` (`status`),
  KEY `quotations_converted_invoice_id_index` (`converted_invoice_id`),
  KEY `quotations_converted_job_card_id_index` (`converted_job_card_id`),
  CONSTRAINT `quotations_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotations_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `quotations_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quotations`
--

LOCK TABLES `quotations` WRITE;
/*!40000 ALTER TABLE `quotations` DISABLE KEYS */;
/*!40000 ALTER TABLE `quotations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_vehicle_prices`
--

DROP TABLE IF EXISTS `service_vehicle_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_vehicle_prices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) unsigned NOT NULL,
  `vehicle_type_id` bigint(20) unsigned NOT NULL,
  `service_price` decimal(10,2) DEFAULT NULL,
  `service_cost` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `service_vehicle_prices_service_id_vehicle_type_id_unique` (`service_id`,`vehicle_type_id`),
  KEY `service_vehicle_prices_vehicle_type_id_foreign` (`vehicle_type_id`),
  CONSTRAINT `service_vehicle_prices_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_vehicle_prices_vehicle_type_id_foreign` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_vehicle_prices`
--

LOCK TABLES `service_vehicle_prices` WRITE;
/*!40000 ALTER TABLE `service_vehicle_prices` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_vehicle_prices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `services` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `services_code_unique` (`code`),
  KEY `services_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (15,'SRV-001','Engine Oil Change','Drain old oil and fill new oil','active','2026-10-27 06:45:58','2026-10-27 06:45:58'),(16,'SRV-002','Oil Filter Replacement','Replace engine oil filter cartridge','active','2026-10-27 06:46:04','2026-10-27 06:46:04'),(17,'SRV-003','Brake Pad Replacement','Inspect calipers and replace brake pads','active','2026-10-27 06:46:10','2026-10-27 06:46:10'),(18,'SRV-004','Engine Diagnostic Scan','OBD-II computer fault code scan','active','2026-10-27 06:46:15','2026-10-27 06:46:15'),(19,'SRV-005','Air Conditioning Service','AC gas check and filter cleaning','active','2026-10-27 06:46:21','2026-10-27 06:46:21'),(20,'SRV-006','Wheel Alignment','Laser 4-wheel alignment','active','2026-10-27 06:46:27','2026-10-27 06:46:27'),(21,'SRV-007','Suspension Inspection','Bushing, shock absorber and tie-rod check','active','2026-10-27 06:46:32','2026-10-27 06:46:32');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('6rWV1QjmAN9ypggKXT3TpjVHZR0OyXyl43smChC2',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJUdHRqUzM2a2w3S2JVaUtZMElFU3VmNjR5V1BLeWFxY1diSEd6bDBUIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1793100435),('I7DkksszKC0HncCtchybv8ipTVXuAXwAkpmuu3zM',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9711','eyJfdG9rZW4iOiJFak0yMk53d1RSUnVKNHRGc0E3SGk5V052SkMwM0dsbmp1Mk5OZTdRIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1793140382),('jUAHVPxHwXKlJIihVdscvIjj3cQ8YDsAzzsZzIbR',2,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJocDExY3YwZWM0Z2FpVjg5R29pQzNrREtvN1NlQVNVaGdUNU9RSld3IiwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjIsInBhc3N3b3JkX2hhc2hfd2ViIjoiZTk0Y2Y5MTBhODI2ODMxNDc1ZjAyZGQ5NzNmNTBjOGQzZmFhYjhjZWRjMGYzNWNmYTZiMTVlOGM3YzBiZDRhNCIsIl9mbGFzaCI6eyJuZXciOltdLCJvbGQiOltdfSwiYWN0aXZlX2JyYW5jaF9pZCI6MSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9wcmludFwvY29uZmlnP3dvcmtzdGF0aW9uPVdvcmtzdGF0aW9uLVQ3REMiLCJyb3V0ZSI6InByaW50LmNvbmZpZyJ9fQ==',1793105779),('KHJdb8tfFxL8Zovaimen5QumGb0VZM5ANTUk0V3T',2,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI5MzBidDMwNTJCV1F2RTZkWk9qdTNKYkJOa0NCdm53ZHVCQ2xQMUs4IiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9wcmludFwvY29uZmlnP3dvcmtzdGF0aW9uPVdvcmtzdGF0aW9uLUhGUUQiLCJyb3V0ZSI6InByaW50LmNvbmZpZyJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoyLCJwYXNzd29yZF9oYXNoX3dlYiI6ImU5NGNmOTEwYTgyNjgzMTQ3NWYwMmRkOTczZjUwYzhkM2ZhYWI4Y2VkYzBmMzVjZmE2YjE1ZThjN2MwYmQ0YTQiLCJhY3RpdmVfYnJhbmNoX2lkIjoxfQ==',1793105793),('u8RhglFwxuhWMWzeuMTd5vI2aITmKsVfJ1TaXrhp',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','eyJfdG9rZW4iOiIyWEx5dnJoZjBOdUdkejBNcjNzMWxHcmJCYUlZZWU3c2NzMlJmakM1IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL3BheXJvbGxcL2VtcGxveWVlcyJ9LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1793140423),('VKD80A2AQM92ytYEs5orFlUbJSWNZYyrk7lPSYJ4',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0','eyJfdG9rZW4iOiJLQ0s3bU05WEQ0a1FKRlk2SjExWjRNcjZCSnEyZW9IdGZCTExQY1NsIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJ1cmwiOnsiaW50ZW5kZWQiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvcGF5cm9sbFwvYWR2YW5jZXMifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifX0=',1793105461),('X8YPfLh9Veyqx9FLGrY1adPPnCtwaB0Of0NDwQgf',2,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJlZHJzTXVkbmwwUGMyZm9EemM2bXdvY3R1TTdETmRzb0xKV2RDUmp5IiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6WyJzdWNjZXNzIl0sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MiwicGFzc3dvcmRfaGFzaF93ZWIiOiJlOTRjZjkxMGE4MjY4MzE0NzVmMDJkZDk3M2Y1MGM4ZDNmYWFiOGNlZGMwZjM1Y2ZhNmIxNWU4YzdjMGJkNGE0Iiwic3VjY2VzcyI6IldlbGNvbWUgYmFjaywgQWRtaW4gVXNlciEifQ==',1793101408);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_batches`
--

DROP TABLE IF EXISTS `stock_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_batches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `batch_number` varchar(255) NOT NULL,
  `grn_id` bigint(20) unsigned NOT NULL,
  `grn_item_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `supplier_id` bigint(20) unsigned NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_batches_batch_number_unique` (`batch_number`),
  KEY `stock_batches_grn_id_foreign` (`grn_id`),
  KEY `stock_batches_grn_item_id_foreign` (`grn_item_id`),
  KEY `stock_batches_supplier_id_foreign` (`supplier_id`),
  KEY `stock_batches_product_id_status_remaining_quantity_index` (`product_id`,`status`,`remaining_quantity`),
  KEY `stock_batches_product_id_purchase_date_id_index` (`product_id`,`purchase_date`,`id`),
  KEY `stock_batches_branch_id_foreign` (`branch_id`),
  CONSTRAINT `stock_batches_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_batches_grn_id_foreign` FOREIGN KEY (`grn_id`) REFERENCES `goods_received_notes` (`id`),
  CONSTRAINT `stock_batches_grn_item_id_foreign` FOREIGN KEY (`grn_item_id`) REFERENCES `goods_received_note_items` (`id`),
  CONSTRAINT `stock_batches_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `stock_batches_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_batches`
--

LOCK TABLES `stock_batches` WRITE;
/*!40000 ALTER TABLE `stock_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_movements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `stock_batch_id` bigint(20) unsigned DEFAULT NULL,
  `movement_type` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_cost` decimal(12,4) NOT NULL,
  `reference_type` varchar(255) DEFAULT NULL,
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_movements_stock_batch_id_foreign` (`stock_batch_id`),
  KEY `stock_movements_created_by_foreign` (`created_by`),
  KEY `stock_movements_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  KEY `stock_movements_product_id_created_at_index` (`product_id`,`created_at`),
  KEY `stock_movements_branch_id_foreign` (`branch_id`),
  CONSTRAINT `stock_movements_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `stock_movements_stock_batch_id_foreign` FOREIGN KEY (`stock_batch_id`) REFERENCES `stock_batches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_movements`
--

LOCK TABLES `stock_movements` WRITE;
/*!40000 ALTER TABLE `stock_movements` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_payments`
--

DROP TABLE IF EXISTS `supplier_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supplier_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_number` varchar(50) DEFAULT NULL,
  `supplier_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `grn_id` bigint(20) unsigned NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'completed',
  `reversed_at` datetime DEFAULT NULL,
  `reversed_by` bigint(20) unsigned DEFAULT NULL,
  `reversal_reason` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `supplier_payments_payment_number_unique` (`payment_number`),
  KEY `supplier_payments_created_by_foreign` (`created_by`),
  KEY `supplier_payments_supplier_id_payment_date_index` (`supplier_id`,`payment_date`),
  KEY `supplier_payments_grn_id_index` (`grn_id`),
  KEY `supplier_payments_branch_id_foreign` (`branch_id`),
  KEY `supplier_payments_reversed_by_foreign` (`reversed_by`),
  CONSTRAINT `supplier_payments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `supplier_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `supplier_payments_grn_id_foreign` FOREIGN KEY (`grn_id`) REFERENCES `goods_received_notes` (`id`),
  CONSTRAINT `supplier_payments_reversed_by_foreign` FOREIGN KEY (`reversed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `supplier_payments_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_payments`
--

LOCK TABLES `supplier_payments` WRITE;
/*!40000 ALTER TABLE `supplier_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `registration_number` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `office_phone` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (13,'Colombo Motor Spares','PV-101','Colombo Motor Spares Warehouse, Industrial Zone, Colombo','0771234567',NULL,'0112345678',1,'2026-10-27 06:44:47','2026-10-27 06:44:47'),(14,'Lanka Auto Components','PV-102','Lanka Auto Components Warehouse, Industrial Zone, Colombo','0772345678',NULL,'0112765432',1,'2026-10-27 06:44:53','2026-10-27 06:44:53'),(15,'Metro Parts Distribution','PV-103','Metro Parts Distribution Warehouse, Industrial Zone, Colombo','0773456789',NULL,'0112890123',1,'2026-10-27 06:44:59','2026-10-27 06:44:59');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `units_of_measurement`
--

DROP TABLE IF EXISTS `units_of_measurement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `units_of_measurement` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `units_of_measurement_name_unique` (`name`),
  UNIQUE KEY `units_of_measurement_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `units_of_measurement`
--

LOCK TABLES `units_of_measurement` WRITE;
/*!40000 ALTER TABLE `units_of_measurement` DISABLE KEYS */;
INSERT INTO `units_of_measurement` VALUES (1,'Bottle','BTL',1,'2026-10-19 07:16:05','2026-10-19 07:16:05'),(2,'Can','CAN',1,'2026-10-19 07:16:05','2026-10-19 07:16:05'),(3,'Packet','PKT',1,'2026-10-19 07:16:05','2026-10-19 07:16:05'),(4,'Item','ITM',1,'2026-10-19 07:16:05','2026-10-19 07:16:05'),(5,'Loose','LSE',1,'2026-10-19 07:16:05','2026-10-19 07:16:05'),(6,'Tube','TUB',1,'2026-10-19 07:16:05','2026-10-19 07:16:05');
/*!40000 ALTER TABLE `units_of_measurement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'staff',
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (2,'Admin User','admin@gmail.com','+94 77 987 6543','admin','active',NULL,'$2y$12$PXvdioOhTCXVpaphayQO0.r8/GfWwP10sN1sUXBvrPqgfArZ1RyUG',NULL,'2026-10-19 06:08:40','2026-10-20 02:36:18');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicle_types`
--

DROP TABLE IF EXISTS `vehicle_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicle_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `enable_carpets` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicle_types_name_unique` (`name`),
  KEY `vehicle_types_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicle_types`
--

LOCK TABLES `vehicle_types` WRITE;
/*!40000 ALTER TABLE `vehicle_types` DISABLE KEYS */;
INSERT INTO `vehicle_types` VALUES (8,'SUV',NULL,0,NULL,'active','2026-10-27 06:28:12','2026-10-27 06:28:12'),(9,'Van',NULL,0,NULL,'active','2026-10-27 06:28:17','2026-10-27 06:28:17');
/*!40000 ALTER TABLE `vehicle_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `vehicle_type_id` bigint(20) unsigned NOT NULL,
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
  `current_mileage` int(10) unsigned NOT NULL DEFAULT 0,
  `next_service_mileage` int(10) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicles_vehicle_number_normalized_unique` (`vehicle_number_normalized`),
  KEY `vehicles_customer_id_foreign` (`customer_id`),
  KEY `vehicles_vehicle_type_id_foreign` (`vehicle_type_id`),
  KEY `vehicles_branch_id_foreign` (`branch_id`),
  CONSTRAINT `vehicles_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `vehicles_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `vehicles_vehicle_type_id_foreign` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicles`
--

LOCK TABLES `vehicles` WRITE;
/*!40000 ALTER TABLE `vehicles` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehicles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-28  4:03:55

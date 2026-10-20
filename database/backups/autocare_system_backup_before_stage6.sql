-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: autocare_system
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (1,1,'seeded','System','Initial master data records and sample staff account initialized.','127.0.0.1',NULL,'2026-10-19 06:08:35','2026-10-19 06:08:35'),(2,2,'created','Administration','Administrator Admin User (admin@autocare.com) created via Artisan console command.','127.0.0.1','Symfony','2026-10-19 06:08:40','2026-10-19 06:08:40'),(3,1,'login','Authentication','User Kamal Perera (staff) logged in.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0','2026-10-19 06:47:51','2026-10-19 06:47:51'),(4,1,'logout','Authentication','User Kamal Perera logged out.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0','2026-10-19 06:50:04','2026-10-19 06:50:04'),(5,2,'login','Authentication','User Admin User (admin) logged in.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0','2026-10-19 06:50:19','2026-10-19 06:50:19'),(6,2,'login','Authentication','User Admin User (admin) logged in.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0','2026-10-19 22:43:56','2026-10-19 22:43:56'),(7,2,'generate_payroll','Payroll','Generated draft salary report for October 2026','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0','2026-10-20 00:32:35','2026-10-20 00:32:35'),(8,NULL,'updated','Authentication','Administrator login email updated from admin@autocare.com to admin@gmail.com.','127.0.0.1','Symfony','2026-10-20 02:36:18','2026-10-20 02:36:18');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch_user`
--

LOCK TABLES `branch_user` WRITE;
/*!40000 ALTER TABLE `branch_user` DISABLE KEYS */;
INSERT INTO `branch_user` VALUES (1,1,2,'2026-10-20 01:42:37','2026-10-20 01:42:37'),(2,1,1,'2026-10-20 01:42:37','2026-10-20 01:42:37');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES (1,1,'BR-001','AutoCare Service Center - Main Branch','No. 124, High Level Road, Colombo, Sri Lanka','+94 11 234 5678','info@autocare.local',NULL,1,1,'2026-10-20 01:42:37','2026-10-20 01:42:37');
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
INSERT INTO `cache` VALUES ('autocare-system-cache-admin@1234.com|127.0.0.1','i:1;',1792469637),('autocare-system-cache-admin@1234.com|127.0.0.1:timer','i:1792469637;',1792469637),('autocare-system-cache-roster:project:v3:4d6e2ebb63bec57786b7d649e8cb230a','O:26:\"Laravel\\Roster\\ProjectScan\":8:{s:8:\"basePath\";s:29:\"D:\\Intern\\08\\autocare-system\\\";s:3:\"php\";O:35:\"Laravel\\Roster\\Ecosystems\\Ecosystem\":2:{s:9:\"\0*\0byName\";a:115:{s:10:\"brick/math\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"brick/math\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";E:43:\"Laravel\\Roster\\Enums\\PackageSource:Composer\";s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\Intern\\08\\autocare-system\\vendor\\brick\\math\";}s:31:\"carbonphp/carbon-doctrine-types\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"carbonphp/carbon-doctrine-types\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"D:\\Intern\\08\\autocare-system\\vendor\\carbonphp\\carbon-doctrine-types\";}s:23:\"dflydev/dot-access-data\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"dflydev/dot-access-data\";s:10:\"\0*\0version\";s:5:\"3.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\vendor\\dflydev\\dot-access-data\";}s:18:\"doctrine/inflector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"doctrine/inflector\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\vendor\\doctrine\\inflector\";}s:14:\"doctrine/lexer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"doctrine/lexer\";s:10:\"\0*\0version\";s:5:\"3.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\doctrine\\lexer\";}s:29:\"dragonmantank/cron-expression\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"dragonmantank/cron-expression\";s:10:\"\0*\0version\";s:5:\"3.6.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\vendor\\dragonmantank\\cron-expression\";}s:23:\"egulias/email-validator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"egulias/email-validator\";s:10:\"\0*\0version\";s:5:\"4.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\vendor\\egulias\\email-validator\";}s:18:\"fruitcake/php-cors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"fruitcake/php-cors\";s:10:\"\0*\0version\";s:5:\"1.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\vendor\\fruitcake\\php-cors\";}s:27:\"graham-campbell/result-type\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"graham-campbell/result-type\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"D:\\Intern\\08\\autocare-system\\vendor\\graham-campbell\\result-type\";}s:17:\"guzzlehttp/guzzle\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"guzzlehttp/guzzle\";s:10:\"\0*\0version\";s:5:\"8.3.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\guzzlehttp\\guzzle\";}s:19:\"guzzlehttp/promises\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"guzzlehttp/promises\";s:10:\"\0*\0version\";s:5:\"3.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\vendor\\guzzlehttp\\promises\";}s:15:\"guzzlehttp/psr7\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"guzzlehttp/psr7\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\guzzlehttp\\psr7\";}s:23:\"guzzlehttp/uri-template\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"guzzlehttp/uri-template\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\vendor\\guzzlehttp\\uri-template\";}s:17:\"laravel/framework\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"laravel/framework\";s:10:\"\0*\0version\";s:7:\"13.37.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^13.17\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\framework\";}s:15:\"laravel/prompts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"laravel/prompts\";s:10:\"\0*\0version\";s:6:\"0.3.26\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\prompts\";}s:28:\"laravel/serializable-closure\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"laravel/serializable-closure\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\serializable-closure\";}s:14:\"laravel/tinker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/tinker\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.0\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\tinker\";}s:17:\"league/commonmark\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"league/commonmark\";s:10:\"\0*\0version\";s:6:\"2.10.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\league\\commonmark\";}s:13:\"league/config\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"league/config\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\vendor\\league\\config\";}s:16:\"league/flysystem\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"league/flysystem\";s:10:\"\0*\0version\";s:6:\"3.36.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\vendor\\league\\flysystem\";}s:22:\"league/flysystem-local\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"league/flysystem-local\";s:10:\"\0*\0version\";s:6:\"3.35.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\league\\flysystem-local\";}s:26:\"league/mime-type-detection\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"league/mime-type-detection\";s:10:\"\0*\0version\";s:6:\"1.17.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"D:\\Intern\\08\\autocare-system\\vendor\\league\\mime-type-detection\";}s:10:\"league/uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"league/uri\";s:10:\"\0*\0version\";s:5:\"7.9.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\Intern\\08\\autocare-system\\vendor\\league\\uri\";}s:21:\"league/uri-interfaces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"league/uri-interfaces\";s:10:\"\0*\0version\";s:5:\"7.9.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\vendor\\league\\uri-interfaces\";}s:15:\"monolog/monolog\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"monolog/monolog\";s:10:\"\0*\0version\";s:6:\"3.12.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\monolog\\monolog\";}s:13:\"nesbot/carbon\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"nesbot/carbon\";s:10:\"\0*\0version\";s:6:\"3.15.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\vendor\\nesbot\\carbon\";}s:12:\"nette/schema\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"nette/schema\";s:10:\"\0*\0version\";s:5:\"1.3.6\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\Intern\\08\\autocare-system\\vendor\\nette\\schema\";}s:11:\"nette/utils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"nette/utils\";s:10:\"\0*\0version\";s:5:\"4.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\vendor\\nette\\utils\";}s:16:\"nikic/php-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"nikic/php-parser\";s:10:\"\0*\0version\";s:5:\"5.9.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\vendor\\nikic\\php-parser\";}s:19:\"nunomaduro/termwind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"nunomaduro/termwind\";s:10:\"\0*\0version\";s:5:\"2.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\vendor\\nunomaduro\\termwind\";}s:19:\"phpoption/phpoption\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"phpoption/phpoption\";s:10:\"\0*\0version\";s:6:\"1.10.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\vendor\\phpoption\\phpoption\";}s:9:\"psr/clock\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"psr/clock\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\clock\";}s:13:\"psr/container\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"psr/container\";s:10:\"\0*\0version\";s:5:\"2.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\container\";}s:20:\"psr/event-dispatcher\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"psr/event-dispatcher\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\event-dispatcher\";}s:15:\"psr/http-client\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"psr/http-client\";s:10:\"\0*\0version\";s:5:\"1.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\http-client\";}s:16:\"psr/http-factory\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/http-factory\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\http-factory\";}s:16:\"psr/http-message\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/http-message\";s:10:\"\0*\0version\";s:3:\"2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\http-message\";}s:7:\"psr/log\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"psr/log\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\log\";}s:16:\"psr/simple-cache\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/simple-cache\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\vendor\\psr\\simple-cache\";}s:9:\"psy/psysh\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"psy/psysh\";s:10:\"\0*\0version\";s:7:\"0.12.24\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\Intern\\08\\autocare-system\\vendor\\psy\\psysh\";}s:17:\"ramsey/collection\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"ramsey/collection\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\ramsey\\collection\";}s:11:\"ramsey/uuid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"ramsey/uuid\";s:10:\"\0*\0version\";s:5:\"4.9.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\vendor\\ramsey\\uuid\";}s:13:\"symfony/clock\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"symfony/clock\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\clock\";}s:15:\"symfony/console\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/console\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\console\";}s:20:\"symfony/css-selector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"symfony/css-selector\";s:10:\"\0*\0version\";s:5:\"8.1.6\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\css-selector\";}s:29:\"symfony/deprecation-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"symfony/deprecation-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\deprecation-contracts\";}s:21:\"symfony/error-handler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/error-handler\";s:10:\"\0*\0version\";s:5:\"8.1.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\error-handler\";}s:24:\"symfony/event-dispatcher\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"symfony/event-dispatcher\";s:10:\"\0*\0version\";s:5:\"8.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\event-dispatcher\";}s:34:\"symfony/event-dispatcher-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"symfony/event-dispatcher-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\event-dispatcher-contracts\";}s:14:\"symfony/finder\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/finder\";s:10:\"\0*\0version\";s:5:\"8.1.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\finder\";}s:23:\"symfony/http-foundation\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"symfony/http-foundation\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\http-foundation\";}s:19:\"symfony/http-kernel\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"symfony/http-kernel\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\http-kernel\";}s:14:\"symfony/mailer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/mailer\";s:10:\"\0*\0version\";s:5:\"8.1.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\mailer\";}s:12:\"symfony/mime\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"symfony/mime\";s:10:\"\0*\0version\";s:5:\"8.1.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\mime\";}s:22:\"symfony/polyfill-ctype\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-ctype\";s:10:\"\0*\0version\";s:6:\"1.37.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-ctype\";}s:30:\"symfony/polyfill-intl-grapheme\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"symfony/polyfill-intl-grapheme\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-intl-grapheme\";}s:25:\"symfony/polyfill-intl-idn\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/polyfill-intl-idn\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-intl-idn\";}s:32:\"symfony/polyfill-intl-normalizer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"symfony/polyfill-intl-normalizer\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-intl-normalizer\";}s:25:\"symfony/polyfill-mbstring\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/polyfill-mbstring\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-mbstring\";}s:22:\"symfony/polyfill-php80\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php80\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-php80\";}s:22:\"symfony/polyfill-php82\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php82\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-php82\";}s:22:\"symfony/polyfill-php84\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php84\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-php84\";}s:22:\"symfony/polyfill-php85\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php85\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-php85\";}s:22:\"symfony/polyfill-php86\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php86\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-php86\";}s:21:\"symfony/polyfill-time\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/polyfill-time\";s:10:\"\0*\0version\";s:6:\"1.42.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-time\";}s:21:\"symfony/polyfill-uuid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/polyfill-uuid\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\polyfill-uuid\";}s:15:\"symfony/process\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/process\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\process\";}s:15:\"symfony/routing\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/routing\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\routing\";}s:25:\"symfony/service-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/service-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\service-contracts\";}s:14:\"symfony/string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/string\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\string\";}s:19:\"symfony/translation\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"symfony/translation\";s:10:\"\0*\0version\";s:5:\"8.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\translation\";}s:29:\"symfony/translation-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"symfony/translation-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\translation-contracts\";}s:11:\"symfony/uid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"symfony/uid\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\uid\";}s:18:\"symfony/var-dumper\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"symfony/var-dumper\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\var-dumper\";}s:33:\"tijsverkoyen/css-to-inline-styles\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"tijsverkoyen/css-to-inline-styles\";s:10:\"\0*\0version\";s:5:\"2.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"D:\\Intern\\08\\autocare-system\\vendor\\tijsverkoyen\\css-to-inline-styles\";}s:16:\"vlucas/phpdotenv\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"vlucas/phpdotenv\";s:10:\"\0*\0version\";s:5:\"5.7.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\vendor\\vlucas\\phpdotenv\";}s:19:\"voku/portable-ascii\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"voku/portable-ascii\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\vendor\\voku\\portable-ascii\";}s:15:\"composer/semver\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"composer/semver\";s:10:\"\0*\0version\";s:5:\"3.5.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\composer\\semver\";}s:14:\"fakerphp/faker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"fakerphp/faker\";s:10:\"\0*\0version\";s:6:\"1.24.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.23\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\fakerphp\\faker\";}s:11:\"filp/whoops\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"filp/whoops\";s:10:\"\0*\0version\";s:6:\"2.18.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\vendor\\filp\\whoops\";}s:21:\"hamcrest/hamcrest-php\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"hamcrest/hamcrest-php\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\vendor\\hamcrest\\hamcrest-php\";}s:22:\"laravel/agent-detector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"laravel/agent-detector\";s:10:\"\0*\0version\";s:5:\"2.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\agent-detector\";}s:13:\"laravel/boost\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"laravel/boost\";s:10:\"\0*\0version\";s:6:\"2.10.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^2.10\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\boost\";}s:11:\"laravel/mcp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"laravel/mcp\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\mcp\";}s:12:\"laravel/pail\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"laravel/pail\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^1.2.5\";s:7:\"\0*\0path\";s:48:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\pail\";}s:11:\"laravel/pao\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"laravel/pao\";s:10:\"\0*\0version\";s:5:\"1.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^1.0.6\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\pao\";}s:12:\"laravel/pint\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"laravel/pint\";s:10:\"\0*\0version\";s:6:\"1.32.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.27\";s:7:\"\0*\0path\";s:48:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\pint\";}s:14:\"laravel/roster\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/roster\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\laravel\\roster\";}s:15:\"mockery/mockery\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"mockery/mockery\";s:10:\"\0*\0version\";s:6:\"1.6.15\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^1.6\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\mockery\\mockery\";}s:17:\"myclabs/deep-copy\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"myclabs/deep-copy\";s:10:\"\0*\0version\";s:6:\"1.14.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\myclabs\\deep-copy\";}s:20:\"nunomaduro/collision\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"nunomaduro/collision\";s:10:\"\0*\0version\";s:5:\"8.9.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^8.6\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\vendor\\nunomaduro\\collision\";}s:16:\"phar-io/manifest\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"phar-io/manifest\";s:10:\"\0*\0version\";s:5:\"2.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\vendor\\phar-io\\manifest\";}s:15:\"phar-io/version\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"phar-io/version\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\phar-io\\version\";}s:25:\"phpunit/php-code-coverage\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-code-coverage\";s:10:\"\0*\0version\";s:6:\"12.5.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"D:\\Intern\\08\\autocare-system\\vendor\\phpunit\\php-code-coverage\";}s:25:\"phpunit/php-file-iterator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-file-iterator\";s:10:\"\0*\0version\";s:5:\"6.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"D:\\Intern\\08\\autocare-system\\vendor\\phpunit\\php-file-iterator\";}s:19:\"phpunit/php-invoker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"phpunit/php-invoker\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\vendor\\phpunit\\php-invoker\";}s:25:\"phpunit/php-text-template\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-text-template\";s:10:\"\0*\0version\";s:5:\"5.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"D:\\Intern\\08\\autocare-system\\vendor\\phpunit\\php-text-template\";}s:17:\"phpunit/php-timer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"phpunit/php-timer\";s:10:\"\0*\0version\";s:5:\"8.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\phpunit\\php-timer\";}s:15:\"phpunit/phpunit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"phpunit/phpunit\";s:10:\"\0*\0version\";s:7:\"12.5.40\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:8:\"^12.5.12\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\vendor\\phpunit\\phpunit\";}s:20:\"sebastian/cli-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/cli-parser\";s:10:\"\0*\0version\";s:5:\"4.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\cli-parser\";}s:20:\"sebastian/comparator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/comparator\";s:10:\"\0*\0version\";s:5:\"7.1.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\comparator\";}s:20:\"sebastian/complexity\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/complexity\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\complexity\";}s:14:\"sebastian/diff\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"sebastian/diff\";s:10:\"\0*\0version\";s:5:\"7.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\diff\";}s:21:\"sebastian/environment\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"sebastian/environment\";s:10:\"\0*\0version\";s:5:\"8.1.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\environment\";}s:18:\"sebastian/exporter\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"sebastian/exporter\";s:10:\"\0*\0version\";s:5:\"7.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\exporter\";}s:22:\"sebastian/global-state\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"sebastian/global-state\";s:10:\"\0*\0version\";s:5:\"8.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\global-state\";}s:23:\"sebastian/lines-of-code\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"sebastian/lines-of-code\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\lines-of-code\";}s:27:\"sebastian/object-enumerator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"sebastian/object-enumerator\";s:10:\"\0*\0version\";s:5:\"7.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\object-enumerator\";}s:26:\"sebastian/object-reflector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"sebastian/object-reflector\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\object-reflector\";}s:27:\"sebastian/recursion-context\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"sebastian/recursion-context\";s:10:\"\0*\0version\";s:5:\"7.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\recursion-context\";}s:14:\"sebastian/type\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"sebastian/type\";s:10:\"\0*\0version\";s:5:\"6.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\type\";}s:17:\"sebastian/version\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"sebastian/version\";s:10:\"\0*\0version\";s:5:\"6.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\sebastian\\version\";}s:28:\"staabm/side-effects-detector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"staabm/side-effects-detector\";s:10:\"\0*\0version\";s:5:\"1.0.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"D:\\Intern\\08\\autocare-system\\vendor\\staabm\\side-effects-detector\";}s:12:\"symfony/yaml\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"symfony/yaml\";s:10:\"\0*\0version\";s:5:\"8.1.9\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\Intern\\08\\autocare-system\\vendor\\symfony\\yaml\";}s:17:\"theseer/tokenizer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"theseer/tokenizer\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\vendor\\theseer\\tokenizer\";}}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:115:{i:0;r:5;i:1;r:13;i:2;r:21;i:3;r:29;i:4;r:37;i:5;r:45;i:6;r:53;i:7;r:61;i:8;r:69;i:9;r:77;i:10;r:85;i:11;r:93;i:12;r:101;i:13;r:109;i:14;r:117;i:15;r:125;i:16;r:133;i:17;r:141;i:18;r:149;i:19;r:157;i:20;r:165;i:21;r:173;i:22;r:181;i:23;r:189;i:24;r:197;i:25;r:205;i:26;r:213;i:27;r:221;i:28;r:229;i:29;r:237;i:30;r:245;i:31;r:253;i:32;r:261;i:33;r:269;i:34;r:277;i:35;r:285;i:36;r:293;i:37;r:301;i:38;r:309;i:39;r:317;i:40;r:325;i:41;r:333;i:42;r:341;i:43;r:349;i:44;r:357;i:45;r:365;i:46;r:373;i:47;r:381;i:48;r:389;i:49;r:397;i:50;r:405;i:51;r:413;i:52;r:421;i:53;r:429;i:54;r:437;i:55;r:445;i:56;r:453;i:57;r:461;i:58;r:469;i:59;r:477;i:60;r:485;i:61;r:493;i:62;r:501;i:63;r:509;i:64;r:517;i:65;r:525;i:66;r:533;i:67;r:541;i:68;r:549;i:69;r:557;i:70;r:565;i:71;r:573;i:72;r:581;i:73;r:589;i:74;r:597;i:75;r:605;i:76;r:613;i:77;r:621;i:78;r:629;i:79;r:637;i:80;r:645;i:81;r:653;i:82;r:661;i:83;r:669;i:84;r:677;i:85;r:685;i:86;r:693;i:87;r:701;i:88;r:709;i:89;r:717;i:90;r:725;i:91;r:733;i:92;r:741;i:93;r:749;i:94;r:757;i:95;r:765;i:96;r:773;i:97;r:781;i:98;r:789;i:99;r:797;i:100;r:805;i:101;r:813;i:102;r:821;i:103;r:829;i:104;r:837;i:105;r:845;i:106;r:853;i:107;r:861;i:108;r:869;i:109;r:877;i:110;r:885;i:111;r:893;i:112;r:901;i:113;r:909;i:114;r:917;}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:2:\"js\";O:37:\"Laravel\\Roster\\Ecosystems\\JsEcosystem\":3:{s:9:\"\0*\0byName\";a:124:{s:24:\"@alcalzone/ansi-tokenize\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"@alcalzone/ansi-tokenize\";s:10:\"\0*\0version\";s:5:\"0.3.1\";s:9:\"\0*\0source\";E:38:\"Laravel\\Roster\\Enums\\PackageSource:Npm\";s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"D:\\Intern\\08\\autocare-system\\node_modules\\@alcalzone\\ansi-tokenize\";}s:18:\"@laravel/multiplex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@laravel/multiplex\";s:10:\"\0*\0version\";s:5:\"0.4.5\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^0.4.1\";s:7:\"\0*\0path\";s:60:\"D:\\Intern\\08\\autocare-system\\node_modules\\@laravel\\multiplex\";}s:15:\"@vue/reactivity\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"@vue/reactivity\";s:10:\"\0*\0version\";s:6:\"3.5.44\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\node_modules\\@vue\\reactivity\";}s:11:\"@vue/shared\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"@vue/shared\";s:10:\"\0*\0version\";s:6:\"3.5.44\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\@vue\\shared\";}s:8:\"alpinejs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"alpinejs\";s:10:\"\0*\0version\";s:6:\"3.17.4\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^3.17.4\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\node_modules\\alpinejs\";}s:12:\"ansi-escapes\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"ansi-escapes\";s:10:\"\0*\0version\";s:5:\"7.3.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\ansi-escapes\";}s:10:\"ansi-regex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"ansi-regex\";s:10:\"\0*\0version\";s:5:\"6.4.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\ansi-regex\";}s:11:\"ansi-styles\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"ansi-styles\";s:10:\"\0*\0version\";s:5:\"7.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\ansi-styles\";}s:9:\"auto-bind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"auto-bind\";s:10:\"\0*\0version\";s:5:\"5.0.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\auto-bind\";}s:5:\"chalk\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"chalk\";s:10:\"\0*\0version\";s:5:\"5.6.2\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\node_modules\\chalk\";}s:9:\"cli-boxes\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"cli-boxes\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\cli-boxes\";}s:10:\"cli-cursor\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"cli-cursor\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\cli-cursor\";}s:12:\"cli-truncate\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"cli-truncate\";s:10:\"\0*\0version\";s:5:\"6.1.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\cli-truncate\";}s:12:\"code-excerpt\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"code-excerpt\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\code-excerpt\";}s:9:\"commander\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"commander\";s:10:\"\0*\0version\";s:6:\"15.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\commander\";}s:17:\"convert-to-spaces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"convert-to-spaces\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\node_modules\\convert-to-spaces\";}s:11:\"environment\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"environment\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\environment\";}s:10:\"es-toolkit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"es-toolkit\";s:10:\"\0*\0version\";s:6:\"1.52.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\es-toolkit\";}s:20:\"escape-string-regexp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"escape-string-regexp\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"D:\\Intern\\08\\autocare-system\\node_modules\\escape-string-regexp\";}s:20:\"get-east-asian-width\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"get-east-asian-width\";s:10:\"\0*\0version\";s:5:\"1.7.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"D:\\Intern\\08\\autocare-system\\node_modules\\get-east-asian-width\";}s:13:\"indent-string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"indent-string\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\node_modules\\indent-string\";}s:3:\"ink\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:3:\"ink\";s:10:\"\0*\0version\";s:5:\"7.1.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"D:\\Intern\\08\\autocare-system\\node_modules\\ink\";}s:23:\"is-fullwidth-code-point\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"is-fullwidth-code-point\";s:10:\"\0*\0version\";s:5:\"5.1.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\node_modules\\is-fullwidth-code-point\";}s:8:\"is-in-ci\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"is-in-ci\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\node_modules\\is-in-ci\";}s:8:\"mimic-fn\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"mimic-fn\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\node_modules\\mimic-fn\";}s:7:\"onetime\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"onetime\";s:10:\"\0*\0version\";s:5:\"5.1.2\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\node_modules\\onetime\";}s:13:\"patch-console\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"patch-console\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\node_modules\\patch-console\";}s:5:\"react\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"react\";s:10:\"\0*\0version\";s:6:\"19.3.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\node_modules\\react\";}s:16:\"react-reconciler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"react-reconciler\";s:10:\"\0*\0version\";s:6:\"0.33.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\node_modules\\react-reconciler\";}s:14:\"restore-cursor\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"restore-cursor\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\node_modules\\restore-cursor\";}s:9:\"scheduler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"scheduler\";s:10:\"\0*\0version\";s:6:\"0.27.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\scheduler\";}s:11:\"signal-exit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"signal-exit\";s:10:\"\0*\0version\";s:5:\"3.0.7\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\signal-exit\";}s:10:\"slice-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"slice-ansi\";s:10:\"\0*\0version\";s:5:\"9.0.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\slice-ansi\";}s:11:\"stack-utils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"stack-utils\";s:10:\"\0*\0version\";s:5:\"2.0.6\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\stack-utils\";}s:12:\"string-width\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"string-width\";s:10:\"\0*\0version\";s:5:\"8.3.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\string-width\";}s:10:\"strip-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"strip-ansi\";s:10:\"\0*\0version\";s:5:\"7.2.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\strip-ansi\";}s:10:\"tagged-tag\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"tagged-tag\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\tagged-tag\";}s:13:\"terminal-size\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"terminal-size\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\node_modules\\terminal-size\";}s:9:\"type-fest\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"type-fest\";s:10:\"\0*\0version\";s:6:\"5.10.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\type-fest\";}s:11:\"widest-line\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"widest-line\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\widest-line\";}s:9:\"wrap-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"wrap-ansi\";s:10:\"\0*\0version\";s:6:\"10.0.2\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\wrap-ansi\";}s:2:\"ws\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:2:\"ws\";s:10:\"\0*\0version\";s:6:\"8.22.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"D:\\Intern\\08\\autocare-system\\node_modules\\ws\";}s:11:\"yoga-layout\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"yoga-layout\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\yoga-layout\";}s:23:\"@jridgewell/gen-mapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"@jridgewell/gen-mapping\";s:10:\"\0*\0version\";s:6:\"0.3.13\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\node_modules\\@jridgewell\\gen-mapping\";}s:21:\"@jridgewell/remapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"@jridgewell/remapping\";s:10:\"\0*\0version\";s:5:\"2.3.5\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"D:\\Intern\\08\\autocare-system\\node_modules\\@jridgewell\\remapping\";}s:23:\"@jridgewell/resolve-uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"@jridgewell/resolve-uri\";s:10:\"\0*\0version\";s:5:\"3.1.2\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\node_modules\\@jridgewell\\resolve-uri\";}s:27:\"@jridgewell/sourcemap-codec\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"@jridgewell/sourcemap-codec\";s:10:\"\0*\0version\";s:5:\"1.6.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"D:\\Intern\\08\\autocare-system\\node_modules\\@jridgewell\\sourcemap-codec\";}s:25:\"@jridgewell/trace-mapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"@jridgewell/trace-mapping\";s:10:\"\0*\0version\";s:6:\"0.3.31\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"D:\\Intern\\08\\autocare-system\\node_modules\\@jridgewell\\trace-mapping\";}s:18:\"@oxc-project/types\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@oxc-project/types\";s:10:\"\0*\0version\";s:7:\"0.154.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"D:\\Intern\\08\\autocare-system\\node_modules\\@oxc-project\\types\";}s:34:\"@rolldown/binding-android-arm-eabi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-android-arm-eabi\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-android-arm-eabi\";}s:31:\"@rolldown/binding-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@rolldown/binding-android-arm64\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-android-arm64\";}s:30:\"@rolldown/binding-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@rolldown/binding-darwin-arm64\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-darwin-arm64\";}s:28:\"@rolldown/binding-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"@rolldown/binding-darwin-x64\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-darwin-x64\";}s:29:\"@rolldown/binding-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"@rolldown/binding-freebsd-x64\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-freebsd-x64\";}s:37:\"@rolldown/binding-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:37:\"@rolldown/binding-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:79:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-linux-arm-gnueabihf\";}s:33:\"@rolldown/binding-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-arm64-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-linux-arm64-gnu\";}s:34:\"@rolldown/binding-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-linux-arm64-musl\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-linux-arm64-musl\";}s:33:\"@rolldown/binding-linux-ppc64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-ppc64-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-linux-ppc64-gnu\";}s:33:\"@rolldown/binding-linux-s390x-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-s390x-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-linux-s390x-gnu\";}s:31:\"@rolldown/binding-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@rolldown/binding-linux-x64-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-linux-x64-gnu\";}s:32:\"@rolldown/binding-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@rolldown/binding-linux-x64-musl\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-linux-x64-musl\";}s:35:\"@rolldown/binding-openharmony-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@rolldown/binding-openharmony-arm64\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:77:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-openharmony-arm64\";}s:34:\"@rolldown/binding-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-win32-arm64-msvc\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-win32-arm64-msvc\";}s:32:\"@rolldown/binding-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@rolldown/binding-win32-x64-msvc\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\binding-win32-x64-msvc\";}s:21:\"@rolldown/pluginutils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"@rolldown/pluginutils\";s:10:\"\0*\0version\";s:5:\"1.0.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"D:\\Intern\\08\\autocare-system\\node_modules\\@rolldown\\pluginutils\";}s:17:\"@tailwindcss/node\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"@tailwindcss/node\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\node\";}s:18:\"@tailwindcss/oxide\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@tailwindcss/oxide\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide\";}s:32:\"@tailwindcss/oxide-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@tailwindcss/oxide-android-arm64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-android-arm64\";}s:31:\"@tailwindcss/oxide-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@tailwindcss/oxide-darwin-arm64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:73:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-darwin-arm64\";}s:29:\"@tailwindcss/oxide-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"@tailwindcss/oxide-darwin-x64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-darwin-x64\";}s:30:\"@tailwindcss/oxide-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@tailwindcss/oxide-freebsd-x64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-freebsd-x64\";}s:38:\"@tailwindcss/oxide-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:38:\"@tailwindcss/oxide-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:80:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-linux-arm-gnueabihf\";}s:34:\"@tailwindcss/oxide-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@tailwindcss/oxide-linux-arm64-gnu\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:76:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-linux-arm64-gnu\";}s:35:\"@tailwindcss/oxide-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@tailwindcss/oxide-linux-arm64-musl\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:77:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-linux-arm64-musl\";}s:32:\"@tailwindcss/oxide-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@tailwindcss/oxide-linux-x64-gnu\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-linux-x64-gnu\";}s:33:\"@tailwindcss/oxide-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@tailwindcss/oxide-linux-x64-musl\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-linux-x64-musl\";}s:30:\"@tailwindcss/oxide-wasm32-wasi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@tailwindcss/oxide-wasm32-wasi\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-wasm32-wasi\";}s:35:\"@tailwindcss/oxide-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@tailwindcss/oxide-win32-arm64-msvc\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:77:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-win32-arm64-msvc\";}s:33:\"@tailwindcss/oxide-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@tailwindcss/oxide-win32-x64-msvc\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:75:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\oxide-win32-x64-msvc\";}s:17:\"@tailwindcss/vite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"@tailwindcss/vite\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^4.0.0\";s:7:\"\0*\0path\";s:59:\"D:\\Intern\\08\\autocare-system\\node_modules\\@tailwindcss\\vite\";}s:5:\"cliui\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"cliui\";s:10:\"\0*\0version\";s:5:\"9.0.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\node_modules\\cliui\";}s:12:\"concurrently\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"concurrently\";s:10:\"\0*\0version\";s:6:\"10.0.6\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^10.0.3\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\concurrently\";}s:11:\"detect-libc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"detect-libc\";s:10:\"\0*\0version\";s:5:\"2.1.2\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\detect-libc\";}s:11:\"emoji-regex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"emoji-regex\";s:10:\"\0*\0version\";s:6:\"10.6.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\emoji-regex\";}s:16:\"enhanced-resolve\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"enhanced-resolve\";s:10:\"\0*\0version\";s:6:\"5.26.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"D:\\Intern\\08\\autocare-system\\node_modules\\enhanced-resolve\";}s:8:\"escalade\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"escalade\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\node_modules\\escalade\";}s:4:\"fdir\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"fdir\";s:10:\"\0*\0version\";s:5:\"6.5.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\Intern\\08\\autocare-system\\node_modules\\fdir\";}s:8:\"fsevents\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"fsevents\";s:10:\"\0*\0version\";s:5:\"2.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\node_modules\\fsevents\";}s:15:\"get-caller-file\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"get-caller-file\";s:10:\"\0*\0version\";s:5:\"2.0.5\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"D:\\Intern\\08\\autocare-system\\node_modules\\get-caller-file\";}s:11:\"graceful-fs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"graceful-fs\";s:10:\"\0*\0version\";s:6:\"4.2.11\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\graceful-fs\";}s:4:\"jiti\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"jiti\";s:10:\"\0*\0version\";s:5:\"2.7.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\Intern\\08\\autocare-system\\node_modules\\jiti\";}s:19:\"laravel-vite-plugin\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"laravel-vite-plugin\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.1\";s:7:\"\0*\0path\";s:61:\"D:\\Intern\\08\\autocare-system\\node_modules\\laravel-vite-plugin\";}s:12:\"lightningcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"lightningcss\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss\";}s:26:\"lightningcss-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"lightningcss-android-arm64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-android-arm64\";}s:25:\"lightningcss-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"lightningcss-darwin-arm64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-darwin-arm64\";}s:23:\"lightningcss-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"lightningcss-darwin-x64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-darwin-x64\";}s:24:\"lightningcss-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"lightningcss-freebsd-x64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-freebsd-x64\";}s:32:\"lightningcss-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"lightningcss-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:74:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-linux-arm-gnueabihf\";}s:28:\"lightningcss-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"lightningcss-linux-arm64-gnu\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:70:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-linux-arm64-gnu\";}s:29:\"lightningcss-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"lightningcss-linux-arm64-musl\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-linux-arm64-musl\";}s:26:\"lightningcss-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"lightningcss-linux-x64-gnu\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-linux-x64-gnu\";}s:27:\"lightningcss-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"lightningcss-linux-x64-musl\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-linux-x64-musl\";}s:29:\"lightningcss-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"lightningcss-win32-arm64-msvc\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-win32-arm64-msvc\";}s:27:\"lightningcss-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"lightningcss-win32-x64-msvc\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"D:\\Intern\\08\\autocare-system\\node_modules\\lightningcss-win32-x64-msvc\";}s:12:\"magic-string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"magic-string\";s:10:\"\0*\0version\";s:7:\"0.30.21\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\magic-string\";}s:6:\"nanoid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"nanoid\";s:10:\"\0*\0version\";s:6:\"3.3.20\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"D:\\Intern\\08\\autocare-system\\node_modules\\nanoid\";}s:10:\"picocolors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"picocolors\";s:10:\"\0*\0version\";s:5:\"1.1.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\picocolors\";}s:9:\"picomatch\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"picomatch\";s:10:\"\0*\0version\";s:5:\"4.0.7\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\picomatch\";}s:7:\"postcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"postcss\";s:10:\"\0*\0version\";s:6:\"8.5.31\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\node_modules\\postcss\";}s:8:\"rolldown\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"rolldown\";s:10:\"\0*\0version\";s:6:\"1.2.14\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"D:\\Intern\\08\\autocare-system\\node_modules\\rolldown\";}s:4:\"rxjs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"rxjs\";s:10:\"\0*\0version\";s:5:\"7.8.2\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\Intern\\08\\autocare-system\\node_modules\\rxjs\";}s:11:\"shell-quote\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"shell-quote\";s:10:\"\0*\0version\";s:6:\"1.12.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\shell-quote\";}s:13:\"source-map-js\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"source-map-js\";s:10:\"\0*\0version\";s:5:\"1.2.2\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"D:\\Intern\\08\\autocare-system\\node_modules\\source-map-js\";}s:14:\"supports-color\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"supports-color\";s:10:\"\0*\0version\";s:6:\"10.2.2\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"D:\\Intern\\08\\autocare-system\\node_modules\\supports-color\";}s:11:\"tailwindcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"tailwindcss\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^4.0.0\";s:7:\"\0*\0path\";s:53:\"D:\\Intern\\08\\autocare-system\\node_modules\\tailwindcss\";}s:7:\"tapable\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"tapable\";s:10:\"\0*\0version\";s:5:\"2.3.3\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"D:\\Intern\\08\\autocare-system\\node_modules\\tapable\";}s:10:\"tinyglobby\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"tinyglobby\";s:10:\"\0*\0version\";s:6:\"0.2.17\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"D:\\Intern\\08\\autocare-system\\node_modules\\tinyglobby\";}s:9:\"tree-kill\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"tree-kill\";s:10:\"\0*\0version\";s:5:\"1.2.2\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"D:\\Intern\\08\\autocare-system\\node_modules\\tree-kill\";}s:5:\"tslib\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"tslib\";s:10:\"\0*\0version\";s:5:\"2.8.1\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\node_modules\\tslib\";}s:4:\"vite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"vite\";s:10:\"\0*\0version\";s:5:\"8.3.4\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^8.0.0\";s:7:\"\0*\0path\";s:46:\"D:\\Intern\\08\\autocare-system\\node_modules\\vite\";}s:23:\"vite-plugin-full-reload\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"vite-plugin-full-reload\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"D:\\Intern\\08\\autocare-system\\node_modules\\vite-plugin-full-reload\";}s:4:\"y18n\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"y18n\";s:10:\"\0*\0version\";s:5:\"5.0.8\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"D:\\Intern\\08\\autocare-system\\node_modules\\y18n\";}s:5:\"yargs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"yargs\";s:10:\"\0*\0version\";s:6:\"18.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"D:\\Intern\\08\\autocare-system\\node_modules\\yargs\";}s:12:\"yargs-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"yargs-parser\";s:10:\"\0*\0version\";s:6:\"22.0.0\";s:9:\"\0*\0source\";r:1048;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"D:\\Intern\\08\\autocare-system\\node_modules\\yargs-parser\";}}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:124:{i:0;r:1045;i:1;r:1053;i:2;r:1061;i:3;r:1069;i:4;r:1077;i:5;r:1085;i:6;r:1093;i:7;r:1101;i:8;r:1109;i:9;r:1117;i:10;r:1125;i:11;r:1133;i:12;r:1141;i:13;r:1149;i:14;r:1157;i:15;r:1165;i:16;r:1173;i:17;r:1181;i:18;r:1189;i:19;r:1197;i:20;r:1205;i:21;r:1213;i:22;r:1221;i:23;r:1229;i:24;r:1237;i:25;r:1245;i:26;r:1253;i:27;r:1261;i:28;r:1269;i:29;r:1277;i:30;r:1285;i:31;r:1293;i:32;r:1301;i:33;r:1309;i:34;r:1317;i:35;r:1325;i:36;r:1333;i:37;r:1341;i:38;r:1349;i:39;r:1357;i:40;r:1365;i:41;r:1373;i:42;r:1381;i:43;r:1389;i:44;r:1397;i:45;r:1405;i:46;r:1413;i:47;r:1421;i:48;r:1429;i:49;r:1437;i:50;r:1445;i:51;r:1453;i:52;r:1461;i:53;r:1469;i:54;r:1477;i:55;r:1485;i:56;r:1493;i:57;r:1501;i:58;r:1509;i:59;r:1517;i:60;r:1525;i:61;r:1533;i:62;r:1541;i:63;r:1549;i:64;r:1557;i:65;r:1565;i:66;r:1573;i:67;r:1581;i:68;r:1589;i:69;r:1597;i:70;r:1605;i:71;r:1613;i:72;r:1621;i:73;r:1629;i:74;r:1637;i:75;r:1645;i:76;r:1653;i:77;r:1661;i:78;r:1669;i:79;r:1677;i:80;r:1685;i:81;r:1693;i:82;r:1701;i:83;r:1709;i:84;r:1717;i:85;r:1725;i:86;r:1733;i:87;r:1741;i:88;r:1749;i:89;r:1757;i:90;r:1765;i:91;r:1773;i:92;r:1781;i:93;r:1789;i:94;r:1797;i:95;r:1805;i:96;r:1813;i:97;r:1821;i:98;r:1829;i:99;r:1837;i:100;r:1845;i:101;r:1853;i:102;r:1861;i:103;r:1869;i:104;r:1877;i:105;r:1885;i:106;r:1893;i:107;r:1901;i:108;r:1909;i:109;r:1917;i:110;r:1925;i:111;r:1933;i:112;r:1941;i:113;r:1949;i:114;r:1957;i:115;r:1965;i:116;r:1973;i:117;r:1981;i:118;r:1989;i:119;r:1997;i:120;r:2005;i:121;r:2013;i:122;r:2021;i:123;r:2029;}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:17:\"\0*\0packageManager\";E:41:\"Laravel\\Roster\\Enums\\JsPackageManager:Npm\";}s:6:\"stacks\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:1:{i:0;E:32:\"Laravel\\Roster\\Enums\\Stack:Blade\";}}s:21:\"browserTestFrameworks\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}s:9:\"frontends\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}s:6:\"agents\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:2:{i:0;E:37:\"Laravel\\Roster\\Enums\\Agent:ClaudeCode\";i:1;E:32:\"Laravel\\Roster\\Enums\\Agent:Codex\";}}s:7:\"editors\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}}',1792414616);
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
INSERT INTO `company_settings` VALUES (1,'AutoCare Service Center','PV-2026-98765','No. 124, High Level Road, Colombo, Sri Lanka','+94 11 234 5678','info@autocare.local','VAT-123456789-000',NULL,'2026-10-19 06:08:35','2026-10-19 06:08:35');
/*!40000 ALTER TABLE `company_settings` ENABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `direct_sales_sale_number_unique` (`sale_number`),
  KEY `direct_sales_invoice_id_foreign` (`invoice_id`),
  KEY `direct_sales_customer_id_foreign` (`customer_id`),
  KEY `direct_sales_created_by_foreign` (`created_by`),
  KEY `direct_sales_status_index` (`status`),
  KEY `direct_sales_branch_id_foreign` (`branch_id`),
  CONSTRAINT `direct_sales_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `direct_sales_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `direct_sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `direct_sales_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `direct_sales`
--

LOCK TABLES `direct_sales` WRITE;
/*!40000 ALTER TABLE `direct_sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `direct_sales` ENABLE KEYS */;
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
  `advance_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'cash',
  `notes` text DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `payroll_item_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_advances_advance_number_unique` (`advance_number`),
  KEY `employee_advances_employee_id_foreign` (`employee_id`),
  KEY `employee_advances_payroll_item_id_foreign` (`payroll_item_id`),
  KEY `employee_advances_created_by_foreign` (`created_by`),
  KEY `employee_advances_advance_date_index` (`advance_date`),
  KEY `employee_advances_status_index` (`status`),
  CONSTRAINT `employee_advances_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_advances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_advances_payroll_item_id_foreign` FOREIGN KEY (`payroll_item_id`) REFERENCES `payroll_items` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_advances`
--

LOCK TABLES `employee_advances` WRITE;
/*!40000 ALTER TABLE `employee_advances` DISABLE KEYS */;
INSERT INTO `employee_advances` VALUES (1,'ADV-0001',3,'2026-10-10',5000.00,'cash','Festival advance','pending',NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00');
/*!40000 ALTER TABLE `employee_advances` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_attendance`
--

LOCK TABLES `employee_attendance` WRITE;
/*!40000 ALTER TABLE `employee_attendance` DISABLE KEYS */;
INSERT INTO `employee_attendance` VALUES (1,1,'2026-10-01','full_day',1.00,3200.00,3200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(2,2,'2026-10-01','full_day',1.00,2200.00,2200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(3,3,'2026-10-01','full_day',1.00,3000.00,3000.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(4,4,'2026-10-01','full_day',1.00,2500.00,2500.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(5,1,'2026-10-02','full_day',1.00,3200.00,3200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(6,2,'2026-10-02','full_day',1.00,2200.00,2200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(7,3,'2026-10-02','full_day',1.00,3000.00,3000.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(8,4,'2026-10-02','full_day',1.00,2500.00,2500.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(9,1,'2026-10-03','full_day',1.00,3200.00,3200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(10,2,'2026-10-03','half_day',0.50,2200.00,1100.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(11,3,'2026-10-03','full_day',1.00,3000.00,3000.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(12,4,'2026-10-03','full_day',1.00,2500.00,2500.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(13,1,'2026-10-05','full_day',1.00,3200.00,3200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(14,2,'2026-10-05','full_day',1.00,2200.00,2200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(15,3,'2026-10-05','full_day',1.00,3000.00,3000.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(16,4,'2026-10-05','full_day',1.00,2500.00,2500.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(17,1,'2026-10-06','full_day',1.00,3200.00,3200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(18,2,'2026-10-06','full_day',1.00,2200.00,2200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(19,3,'2026-10-06','full_day',1.00,3000.00,3000.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(20,4,'2026-10-06','full_day',1.00,2500.00,2500.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(21,1,'2026-10-07','full_day',1.00,3200.00,3200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(22,2,'2026-10-07','full_day',1.00,2200.00,2200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(23,3,'2026-10-07','full_day',1.00,3000.00,3000.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(24,4,'2026-10-07','full_day',1.00,2500.00,2500.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(25,1,'2026-10-08','full_day',1.00,3200.00,3200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(26,2,'2026-10-08','full_day',1.00,2200.00,2200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(27,3,'2026-10-08','full_day',1.00,3000.00,3000.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(28,4,'2026-10-08','full_day',1.00,2500.00,2500.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(29,1,'2026-10-09','full_day',1.00,3200.00,3200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(30,2,'2026-10-09','full_day',1.00,2200.00,2200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(31,3,'2026-10-09','full_day',1.00,3000.00,3000.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(32,4,'2026-10-09','full_day',1.00,2500.00,2500.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(33,1,'2026-10-10','full_day',1.00,3200.00,3200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(34,2,'2026-10-10','half_day',0.50,2200.00,1100.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(35,3,'2026-10-10','absent',0.00,3000.00,0.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(36,4,'2026-10-10','full_day',1.00,2500.00,2500.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(37,1,'2026-10-12','full_day',1.00,3200.00,3200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(38,2,'2026-10-12','full_day',1.00,2200.00,2200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(39,3,'2026-10-12','full_day',1.00,3000.00,3000.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(40,4,'2026-10-12','full_day',1.00,2500.00,2500.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(41,1,'2026-10-13','full_day',1.00,3200.00,3200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(42,2,'2026-10-13','full_day',1.00,2200.00,2200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(43,3,'2026-10-13','full_day',1.00,3000.00,3000.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(44,4,'2026-10-13','full_day',1.00,2500.00,2500.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(45,1,'2026-10-14','full_day',1.00,3200.00,3200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(46,2,'2026-10-14','full_day',1.00,2200.00,2200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(47,3,'2026-10-14','full_day',1.00,3000.00,3000.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(48,4,'2026-10-14','full_day',1.00,2500.00,2500.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(49,1,'2026-10-15','full_day',1.00,3200.00,3200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(50,2,'2026-10-15','full_day',1.00,2200.00,2200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(51,3,'2026-10-15','full_day',1.00,3000.00,3000.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(52,4,'2026-10-15','full_day',1.00,2500.00,2500.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(53,1,'2026-10-16','full_day',1.00,3200.00,3200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(54,2,'2026-10-16','full_day',1.00,2200.00,2200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(55,3,'2026-10-16','full_day',1.00,3000.00,3000.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(56,4,'2026-10-16','full_day',1.00,2500.00,2500.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(57,1,'2026-10-17','full_day',1.00,3200.00,3200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(58,2,'2026-10-17','half_day',0.50,2200.00,1100.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(59,3,'2026-10-17','full_day',1.00,3000.00,3000.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(60,4,'2026-10-17','full_day',1.00,2500.00,2500.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(61,1,'2026-10-19','full_day',1.00,3200.00,3200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(62,2,'2026-10-19','full_day',1.00,2200.00,2200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(63,3,'2026-10-19','full_day',1.00,3000.00,3000.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(64,4,'2026-10-19','full_day',1.00,2500.00,2500.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(65,1,'2026-10-20','full_day',1.00,3200.00,3200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(66,2,'2026-10-20','full_day',1.00,2200.00,2200.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(67,3,'2026-10-20','full_day',1.00,3000.00,3000.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(68,4,'2026-10-20','full_day',1.00,2500.00,2500.00,NULL,NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00');
/*!40000 ALTER TABLE `employee_attendance` ENABLE KEYS */;
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
  `daily_wage` decimal(12,2) NOT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_wage_rates_employee_id_foreign` (`employee_id`),
  KEY `employee_wage_rates_effective_from_index` (`effective_from`),
  KEY `employee_wage_rates_effective_to_index` (`effective_to`),
  CONSTRAINT `employee_wage_rates_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_wage_rates`
--

LOCK TABLES `employee_wage_rates` WRITE;
/*!40000 ALTER TABLE `employee_wage_rates` DISABLE KEYS */;
INSERT INTO `employee_wage_rates` VALUES (1,1,3200.00,'2026-01-15',NULL,'Initial wage rate upon employment','2026-10-20 00:08:00','2026-10-20 00:08:00'),(2,2,2200.00,'2026-03-01',NULL,'Initial wage rate upon employment','2026-10-20 00:08:00','2026-10-20 00:08:00'),(3,3,3000.00,'2026-02-10',NULL,'Initial wage rate upon employment','2026-10-20 00:08:00','2026-10-20 00:08:00'),(4,4,2500.00,'2026-04-01',NULL,'Initial wage rate upon employment','2026-10-20 00:08:00','2026-10-20 00:08:00');
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
  `joining_date` date DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_employee_number_unique` (`employee_number`),
  KEY `employees_user_id_foreign` (`user_id`),
  KEY `employees_mobile_index` (`mobile`),
  KEY `employees_is_active_index` (`is_active`),
  KEY `employees_branch_id_foreign` (`branch_id`),
  CONSTRAINT `employees_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,1,'EMP-0001','Nilantha Kumara','0771234567','No. 45, Kandy Road, Kelaniya',3200.00,'2026-01-15','Senior Mechanic',1,'Head of engine servicing & overhaul division',NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(2,1,'EMP-0002','Iresha Perera','0719876543','No. 12, Station Road, Ragama',2200.00,'2026-03-01','Detailing Specialist',1,'Vehicle body wash and interior shampooing',NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(3,1,'EMP-0003','Roshan Silva','0754567890','No. 88, Galle Road, Dehiwala',3000.00,'2026-02-10','Auto Electrician',1,'Vehicle scanning, battery & starter repairs',NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00'),(4,1,'EMP-0004','Hasini Kumari','0761122334','No. 7, Temple Road, Maharagama',2500.00,'2026-04-01','Service Coordinator',1,'Front desk job card coordination',NULL,'2026-10-20 00:08:00','2026-10-20 00:08:00');
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_categories`
--

LOCK TABLES `expense_categories` WRITE;
/*!40000 ALTER TABLE `expense_categories` DISABLE KEYS */;
INSERT INTO `expense_categories` VALUES (1,'Salary Advance',1,'2026-10-20 01:08:58','2026-10-20 01:08:58'),(2,'Boss Expenses',1,'2026-10-20 01:08:58','2026-10-20 01:08:58'),(3,'Electricity',1,'2026-10-20 01:08:58','2026-10-20 01:08:58'),(4,'EPF / ETF',1,'2026-10-20 01:08:58','2026-10-20 01:08:58'),(5,'Staff Welfare',1,'2026-10-20 01:08:58','2026-10-20 01:08:58'),(6,'Water',1,'2026-10-20 01:08:58','2026-10-20 01:08:58'),(7,'Telephone / Internet',1,'2026-10-20 01:08:58','2026-10-20 01:08:58'),(8,'Chemicals',1,'2026-10-20 01:08:58','2026-10-20 01:08:58'),(9,'Repair',1,'2026-10-20 01:08:58','2026-10-20 01:08:58'),(10,'Maintenance',1,'2026-10-20 01:08:58','2026-10-20 01:08:58'),(11,'Other',1,'2026-10-20 01:08:58','2026-10-20 01:08:58');
/*!40000 ALTER TABLE `expense_categories` ENABLE KEYS */;
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
  CONSTRAINT `expenses_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`),
  CONSTRAINT `expenses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_employee_advance_id_foreign` FOREIGN KEY (`employee_advance_id`) REFERENCES `employee_advances` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_voided_by_foreign` FOREIGN KEY (`voided_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goods_received_note_items`
--

LOCK TABLES `goods_received_note_items` WRITE;
/*!40000 ALTER TABLE `goods_received_note_items` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goods_received_notes`
--

LOCK TABLES `goods_received_notes` WRITE;
/*!40000 ALTER TABLE `goods_received_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `goods_received_notes` ENABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_items`
--

LOCK TABLES `invoice_items` WRITE;
/*!40000 ALTER TABLE `invoice_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_items` ENABLE KEYS */;
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
  CONSTRAINT `invoices_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `is_stock_deducted` tinyint(1) NOT NULL DEFAULT 0,
  `batch_allocations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`batch_allocations`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_card_products_job_card_id_foreign` (`job_card_id`),
  KEY `job_card_products_product_id_foreign` (`product_id`),
  CONSTRAINT `job_card_products_job_card_id_foreign` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards` (`id`) ON DELETE CASCADE,
  CONSTRAINT `job_card_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `technician_name` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_card_services_job_card_id_foreign` (`job_card_id`),
  KEY `job_card_services_service_id_foreign` (`service_id`),
  CONSTRAINT `job_card_services_job_card_id_foreign` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards` (`id`) ON DELETE CASCADE,
  CONSTRAINT `job_card_services_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `status` enum('pending','in_progress','completed','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `assigned_to` bigint(20) unsigned DEFAULT NULL,
  `service_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `product_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `invoice_id` bigint(20) unsigned DEFAULT NULL,
  `customer_notes` text DEFAULT NULL,
  `service_notes` text DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `cancelled_by` bigint(20) unsigned DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
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
  CONSTRAINT `job_cards_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_cards_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_cards_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_cards_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_cards_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `job_cards_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_cards_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `job_cards_vehicle_type_id_foreign` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_cards`
--

LOCK TABLES `job_cards` WRITE;
/*!40000 ALTER TABLE `job_cards` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_cards` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_10_19_112550_create_services_table',1),(5,'2026_10_19_112551_create_vehicle_types_table',1),(6,'2026_10_19_112552_create_company_settings_table',1),(7,'2026_10_19_112553_create_activity_logs_table',1),(8,'2026_10_19_123022_create_service_vehicle_prices_table',2),(9,'2026_10_19_123508_add_image_to_vehicle_types_table',3),(10,'2026_10_19_130001_create_suppliers_table',4),(11,'2026_10_19_130002_create_units_of_measurement_table',4),(12,'2026_10_19_130003_create_products_table',4),(13,'2026_10_19_130004_create_goods_received_notes_table',4),(14,'2026_10_19_130005_create_goods_received_note_items_table',4),(15,'2026_10_19_130006_create_stock_batches_table',4),(16,'2026_10_19_130007_create_stock_movements_table',4),(17,'2026_10_19_130008_create_supplier_payments_table',4),(18,'2026_10_20_050001_create_customers_table',5),(19,'2026_10_20_050002_create_vehicles_table',5),(20,'2026_10_20_050003_create_invoices_table',5),(21,'2026_10_20_050004_create_invoice_items_table',5),(22,'2026_10_20_050005_create_payments_table',5),(23,'2026_10_20_050006_create_job_cards_table',5),(24,'2026_10_20_050007_create_job_card_services_table',5),(25,'2026_10_20_050008_create_job_card_products_table',5),(26,'2026_10_20_050009_create_direct_sales_table',5),(27,'2026_10_20_050010_create_direct_sale_items_table',5),(28,'2026_10_20_060001_create_employees_table',6),(29,'2026_10_20_060002_create_employee_wage_rates_table',6),(30,'2026_10_20_060003_create_employee_attendance_table',6),(31,'2026_10_20_060004_create_payroll_periods_table',6),(32,'2026_10_20_060005_create_payroll_items_table',6),(33,'2026_10_20_060006_create_employee_advances_table',6),(34,'2026_10_20_060007_create_payroll_payments_table',6),(35,'2026_10_20_070001_create_expense_categories_table',7),(36,'2026_10_20_070002_create_expenses_table',7),(37,'2026_10_20_080001_create_branches_table',8),(38,'2026_10_20_080002_create_branch_user_table',8),(39,'2026_10_20_080003_add_branch_id_to_existing_modules_tables',8),(40,'2026_10_20_090001_create_quotations_table',9),(41,'2026_10_20_090002_create_quotation_items_table',9),(42,'2026_10_20_090003_add_quotation_fields_to_invoices_table',9),(43,'2026_10_20_090004_add_quotation_fields_to_invoice_items_table',9);
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
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint(20) unsigned NOT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `payment_number` varchar(100) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('cash','card','bank_transfer','credit','cheque') NOT NULL DEFAULT 'cash',
  `reference_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_payment_number_unique` (`payment_number`),
  KEY `payments_invoice_id_foreign` (`invoice_id`),
  KEY `payments_customer_id_foreign` (`customer_id`),
  KEY `payments_created_by_foreign` (`created_by`),
  CONSTRAINT `payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `full_days_count` smallint(5) unsigned NOT NULL DEFAULT 0,
  `half_days_count` smallint(5) unsigned NOT NULL DEFAULT 0,
  `absent_days_count` smallint(5) unsigned NOT NULL DEFAULT 0,
  `leave_days_count` smallint(5) unsigned NOT NULL DEFAULT 0,
  `day_off_days_count` smallint(5) unsigned NOT NULL DEFAULT 0,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_period_employee` (`payroll_period_id`,`employee_id`),
  KEY `payroll_items_employee_id_foreign` (`employee_id`),
  KEY `payroll_items_status_index` (`status`),
  CONSTRAINT `payroll_items_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_items_payroll_period_id_foreign` FOREIGN KEY (`payroll_period_id`) REFERENCES `payroll_periods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_items`
--

LOCK TABLES `payroll_items` WRITE;
/*!40000 ALTER TABLE `payroll_items` DISABLE KEYS */;
INSERT INTO `payroll_items` VALUES (1,1,1,'Nilantha Kumara',3200.00,17,0,0,0,0,17.00,54400.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,54400.00,54400.00,0.00,54400.00,'draft','[{\"date\":\"2026-10-01\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3200,\"earnings\":3200,\"notes\":null},{\"date\":\"2026-10-02\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3200,\"earnings\":3200,\"notes\":null},{\"date\":\"2026-10-03\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3200,\"earnings\":3200,\"notes\":null},{\"date\":\"2026-10-05\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3200,\"earnings\":3200,\"notes\":null},{\"date\":\"2026-10-06\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3200,\"earnings\":3200,\"notes\":null},{\"date\":\"2026-10-07\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3200,\"earnings\":3200,\"notes\":null},{\"date\":\"2026-10-08\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3200,\"earnings\":3200,\"notes\":null},{\"date\":\"2026-10-09\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3200,\"earnings\":3200,\"notes\":null},{\"date\":\"2026-10-10\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3200,\"earnings\":3200,\"notes\":null},{\"date\":\"2026-10-12\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3200,\"earnings\":3200,\"notes\":null},{\"date\":\"2026-10-13\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3200,\"earnings\":3200,\"notes\":null},{\"date\":\"2026-10-14\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3200,\"earnings\":3200,\"notes\":null},{\"date\":\"2026-10-15\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3200,\"earnings\":3200,\"notes\":null},{\"date\":\"2026-10-16\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3200,\"earnings\":3200,\"notes\":null},{\"date\":\"2026-10-17\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3200,\"earnings\":3200,\"notes\":null},{\"date\":\"2026-10-19\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3200,\"earnings\":3200,\"notes\":null},{\"date\":\"2026-10-20\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3200,\"earnings\":3200,\"notes\":null}]',NULL,'2026-10-20 00:32:35','2026-10-20 00:32:35'),(2,1,2,'Iresha Perera',2200.00,14,3,0,0,0,15.50,34100.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,34100.00,34100.00,0.00,34100.00,'draft','[{\"date\":\"2026-10-01\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2200,\"earnings\":2200,\"notes\":null},{\"date\":\"2026-10-02\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2200,\"earnings\":2200,\"notes\":null},{\"date\":\"2026-10-03\",\"status\":\"half_day\",\"fraction\":0.5,\"wage\":2200,\"earnings\":1100,\"notes\":null},{\"date\":\"2026-10-05\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2200,\"earnings\":2200,\"notes\":null},{\"date\":\"2026-10-06\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2200,\"earnings\":2200,\"notes\":null},{\"date\":\"2026-10-07\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2200,\"earnings\":2200,\"notes\":null},{\"date\":\"2026-10-08\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2200,\"earnings\":2200,\"notes\":null},{\"date\":\"2026-10-09\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2200,\"earnings\":2200,\"notes\":null},{\"date\":\"2026-10-10\",\"status\":\"half_day\",\"fraction\":0.5,\"wage\":2200,\"earnings\":1100,\"notes\":null},{\"date\":\"2026-10-12\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2200,\"earnings\":2200,\"notes\":null},{\"date\":\"2026-10-13\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2200,\"earnings\":2200,\"notes\":null},{\"date\":\"2026-10-14\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2200,\"earnings\":2200,\"notes\":null},{\"date\":\"2026-10-15\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2200,\"earnings\":2200,\"notes\":null},{\"date\":\"2026-10-16\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2200,\"earnings\":2200,\"notes\":null},{\"date\":\"2026-10-17\",\"status\":\"half_day\",\"fraction\":0.5,\"wage\":2200,\"earnings\":1100,\"notes\":null},{\"date\":\"2026-10-19\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2200,\"earnings\":2200,\"notes\":null},{\"date\":\"2026-10-20\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2200,\"earnings\":2200,\"notes\":null}]',NULL,'2026-10-20 00:32:35','2026-10-20 00:32:35'),(3,1,3,'Roshan Silva',3000.00,16,0,1,0,0,16.00,48000.00,0.00,0.00,0.00,0.00,0.00,0.00,5000.00,48000.00,43000.00,0.00,43000.00,'draft','[{\"date\":\"2026-10-01\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3000,\"earnings\":3000,\"notes\":null},{\"date\":\"2026-10-02\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3000,\"earnings\":3000,\"notes\":null},{\"date\":\"2026-10-03\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3000,\"earnings\":3000,\"notes\":null},{\"date\":\"2026-10-05\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3000,\"earnings\":3000,\"notes\":null},{\"date\":\"2026-10-06\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3000,\"earnings\":3000,\"notes\":null},{\"date\":\"2026-10-07\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3000,\"earnings\":3000,\"notes\":null},{\"date\":\"2026-10-08\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3000,\"earnings\":3000,\"notes\":null},{\"date\":\"2026-10-09\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3000,\"earnings\":3000,\"notes\":null},{\"date\":\"2026-10-10\",\"status\":\"absent\",\"fraction\":0,\"wage\":3000,\"earnings\":0,\"notes\":null},{\"date\":\"2026-10-12\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3000,\"earnings\":3000,\"notes\":null},{\"date\":\"2026-10-13\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3000,\"earnings\":3000,\"notes\":null},{\"date\":\"2026-10-14\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3000,\"earnings\":3000,\"notes\":null},{\"date\":\"2026-10-15\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3000,\"earnings\":3000,\"notes\":null},{\"date\":\"2026-10-16\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3000,\"earnings\":3000,\"notes\":null},{\"date\":\"2026-10-17\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3000,\"earnings\":3000,\"notes\":null},{\"date\":\"2026-10-19\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3000,\"earnings\":3000,\"notes\":null},{\"date\":\"2026-10-20\",\"status\":\"full_day\",\"fraction\":1,\"wage\":3000,\"earnings\":3000,\"notes\":null}]',NULL,'2026-10-20 00:32:35','2026-10-20 00:32:35'),(4,1,4,'Hasini Kumari',2500.00,17,0,0,0,0,17.00,42500.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,42500.00,42500.00,0.00,42500.00,'draft','[{\"date\":\"2026-10-01\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2500,\"earnings\":2500,\"notes\":null},{\"date\":\"2026-10-02\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2500,\"earnings\":2500,\"notes\":null},{\"date\":\"2026-10-03\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2500,\"earnings\":2500,\"notes\":null},{\"date\":\"2026-10-05\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2500,\"earnings\":2500,\"notes\":null},{\"date\":\"2026-10-06\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2500,\"earnings\":2500,\"notes\":null},{\"date\":\"2026-10-07\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2500,\"earnings\":2500,\"notes\":null},{\"date\":\"2026-10-08\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2500,\"earnings\":2500,\"notes\":null},{\"date\":\"2026-10-09\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2500,\"earnings\":2500,\"notes\":null},{\"date\":\"2026-10-10\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2500,\"earnings\":2500,\"notes\":null},{\"date\":\"2026-10-12\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2500,\"earnings\":2500,\"notes\":null},{\"date\":\"2026-10-13\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2500,\"earnings\":2500,\"notes\":null},{\"date\":\"2026-10-14\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2500,\"earnings\":2500,\"notes\":null},{\"date\":\"2026-10-15\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2500,\"earnings\":2500,\"notes\":null},{\"date\":\"2026-10-16\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2500,\"earnings\":2500,\"notes\":null},{\"date\":\"2026-10-17\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2500,\"earnings\":2500,\"notes\":null},{\"date\":\"2026-10-19\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2500,\"earnings\":2500,\"notes\":null},{\"date\":\"2026-10-20\",\"status\":\"full_day\",\"fraction\":1,\"wage\":2500,\"earnings\":2500,\"notes\":null}]',NULL,'2026-10-20 00:32:35','2026-10-20 00:32:35');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_periods`
--

LOCK TABLES `payroll_periods` WRITE;
/*!40000 ALTER TABLE `payroll_periods` DISABLE KEYS */;
INSERT INTO `payroll_periods` VALUES (1,2026,10,'draft',179000.00,0.00,0.00,0.00,5000.00,179000.00,174000.00,0.00,NULL,NULL,NULL,'2026-10-20 00:32:35','2026-10-20 00:32:35');
/*!40000 ALTER TABLE `payroll_periods` ENABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_vehicle_prices`
--

LOCK TABLES `service_vehicle_prices` WRITE;
/*!40000 ALTER TABLE `service_vehicle_prices` DISABLE KEYS */;
INSERT INTO `service_vehicle_prices` VALUES (1,1,1,3500.00,1000.00,'2026-10-19 07:01:46','2026-10-19 07:01:46'),(2,1,2,5000.00,1500.00,'2026-10-19 07:01:46','2026-10-19 07:01:46'),(3,1,3,6200.00,2000.00,'2026-10-19 07:01:46','2026-10-19 07:01:46'),(4,1,4,1200.00,400.00,'2026-10-19 07:01:46','2026-10-19 07:01:46'),(5,1,5,8000.00,3000.00,'2026-10-19 07:01:46','2026-10-19 07:01:46');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,'SRV-001','Full Synthetic Oil & Filter Service','Complete engine oil drainage, premium synthetic oil refill, filter change, and general fluids inspection.','active','2026-10-19 06:08:35','2026-10-19 06:08:35'),(2,'SRV-002','Brake Inspection & Pad Replacement','Hydraulic brake fluid test, caliper cleaning, rotor wear check, and front/rear brake pad replacement.','active','2026-10-19 06:08:35','2026-10-19 06:08:35'),(3,'SRV-003','Computerized Wheel Alignment & Balancing','Laser 4-wheel geometry alignment, tire tread check, and dynamic wheel balancing.','active','2026-10-19 06:08:35','2026-10-19 06:08:35'),(4,'SRV-004','Engine Diagnostic Scan & Tune-Up','Comprehensive OBD-II scan, ignition system check, throttle cleaning, and battery health report.','active','2026-10-19 06:08:35','2026-10-19 06:08:35'),(5,'SRV-005','Express Wash & Underbody Detailing','High pressure chassis wash, shampoo exterior body clean, tire shine, and cabin interior vacuum.','active','2026-10-19 06:08:35','2026-10-19 06:08:35');
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
INSERT INTO `sessions` VALUES ('M19GssPZ7BnPsrwRxKzsGohMAJD6gds5KPopUnFQ',NULL,'127.0.0.1','curl/8.21.0','eyJfdG9rZW4iOiJ4Y3ZBeDdUSllGQ0ZERnFhMkZjUUJ1dzAxeml1ZkIyZWozUktJQVFLIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1792474760),('RYiuy6F2HfpLgEzu0KGAsSQkC7C0bjFQ0ANZ2MDl',NULL,'127.0.0.1','curl/8.21.0','eyJfdG9rZW4iOiJwdmhEOE5OSWhoWkd1a1FTZm1DQlIwV0tSYzBxQ1FyaEE5VWNQUWdCIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1792481075),('WOF80mZIXtsrxmFCh0LOIoLI6fWwfCICHIQ7bPON',2,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0','eyJfdG9rZW4iOiJXNUI2WHRPYXU2eHNGdmpGYmFLY3dzMlZUYmh2b1RHMTQxelNqMFdVIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9iaWxsaW5nXC9xdW90YXRpb25zXC9jcmVhdGUiLCJyb3V0ZSI6ImJpbGxpbmcucXVvdGF0aW9ucy5jcmVhdGUifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MiwicGFzc3dvcmRfaGFzaF93ZWIiOiIyZTIzODg5MWI1Y2Y2N2VjY2UyZmVlMjE5OGI2NzhlMmFmOTAxYTY5ZTVlNTc1ZmI4YjE4NjE0MWMzZDc3ZGI2IiwiYWN0aXZlX2JyYW5jaF9pZCI6MX0=',1792487019);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `supplier_id` bigint(20) unsigned NOT NULL,
  `grn_id` bigint(20) unsigned NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_payments_created_by_foreign` (`created_by`),
  KEY `supplier_payments_supplier_id_payment_date_index` (`supplier_id`,`payment_date`),
  KEY `supplier_payments_grn_id_index` (`grn_id`),
  CONSTRAINT `supplier_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `supplier_payments_grn_id_foreign` FOREIGN KEY (`grn_id`) REFERENCES `goods_received_notes` (`id`),
  CONSTRAINT `supplier_payments_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Kamal Perera','staff@autocare.com','+94 77 123 4567','staff','active',NULL,'$2y$12$30krr0TNFmhS7ZgwPRVmregLgWGWRKbSPPaBj1VfCzqeriOgnDYSu',NULL,'2026-10-19 06:08:35','2026-10-19 06:08:35'),(2,'Admin User','admin@gmail.com','+94 77 987 6543','admin','active',NULL,'$2y$12$PXvdioOhTCXVpaphayQO0.r8/GfWwP10sN1sUXBvrPqgfArZ1RyUG',NULL,'2026-10-19 06:08:40','2026-10-20 02:36:18');
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
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicle_types_name_unique` (`name`),
  KEY `vehicle_types_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicle_types`
--

LOCK TABLES `vehicle_types` WRITE;
/*!40000 ALTER TABLE `vehicle_types` DISABLE KEYS */;
INSERT INTO `vehicle_types` VALUES (1,'Car',NULL,'Standard passenger sedans, hatchbacks, and coupes.','active','2026-10-19 06:08:35','2026-10-19 06:08:35'),(2,'Van',NULL,'Light passenger vans and commercial cargo transporters.','active','2026-10-19 06:08:35','2026-10-19 06:08:35'),(3,'SUV',NULL,'Sport utility vehicles, compact crossovers, and 4x4 jeeps.','active','2026-10-19 06:08:35','2026-10-19 06:08:35'),(4,'Motorbike',NULL,'Two-wheel motorcycles, commuters, and motor scooters.','active','2026-10-19 06:08:35','2026-10-19 06:08:35'),(5,'Lorry',NULL,'Light trucks, flatbeds, and commercial transport vehicles.','active','2026-10-19 06:08:35','2026-10-19 06:08:35');
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
  `year_of_manufacture` varchar(10) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `chassis_number` varchar(100) DEFAULT NULL,
  `engine_number` varchar(100) DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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

-- Dump completed on 2026-10-20 14:40:44

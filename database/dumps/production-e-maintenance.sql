-- MySQL dump 10.13  Distrib 8.0.46, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: e_maintenance
-- ------------------------------------------------------
-- Server version	8.0.46-0ubuntu0.24.04.4

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('e-maintenance-cache-bd307a3ec329e10a2cff8fb87480823da114f8f4','i:1;',1792487904),('e-maintenance-cache-bd307a3ec329e10a2cff8fb87480823da114f8f4:timer','i:1792487904;',1792487904);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
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
-- Table structure for table `damage_categories`
--

DROP TABLE IF EXISTS `damage_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `damage_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `allows_other` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `damage_categories_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `damage_categories`
--

LOCK TABLES `damage_categories` WRITE;
/*!40000 ALTER TABLE `damage_categories` DISABLE KEYS */;
INSERT INTO `damage_categories` VALUES (1,'Mesin Injection',0,1,1,'2026-05-25 07:00:48','2026-05-25 07:00:48'),(2,'Mold',0,1,2,'2026-05-25 07:00:48','2026-05-25 07:00:48'),(3,'Utility (Dehum, MTC, Hopper, Selang, Crusher, Hot Runner, Squencer, dll)',0,1,3,'2026-05-25 07:00:48','2026-05-25 07:00:48'),(4,'Robot',0,1,4,'2026-05-25 07:00:48','2026-05-25 07:00:48'),(5,'Assy (Roller BBP, K1AL, Helm, Dll)',0,1,5,'2026-05-25 07:00:48','2026-05-25 07:00:48'),(6,'Painting',0,1,6,'2026-05-25 07:00:48','2026-05-25 07:00:48'),(7,'Jig Product',0,1,7,'2026-05-25 07:00:48','2026-05-25 07:00:48'),(8,'Other',1,1,8,'2026-05-25 07:00:48','2026-05-25 07:00:48');
/*!40000 ALTER TABLE `damage_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipments`
--

DROP TABLE IF EXISTS `equipments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `equipments_category_number_unique` (`category`,`number`),
  KEY `equipments_category_index` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipments`
--

LOCK TABLES `equipments` WRITE;
/*!40000 ALTER TABLE `equipments` DISABLE KEYS */;
INSERT INTO `equipments` VALUES (1,'dehum','01','Dehum 1',1,'2026-09-10 09:33:25','2026-09-10 09:33:25'),(2,'hopper','01','Hopper 1',1,'2026-09-10 09:33:34','2026-09-10 09:33:34'),(3,'mtc','01','Temperature 1',1,'2026-09-10 09:33:47','2026-09-10 09:33:47'),(4,'mtc_mold','01','Mold TRX',1,'2026-09-10 09:33:58','2026-09-10 09:33:58');
/*!40000 ALTER TABLE `equipments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
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
-- Table structure for table `finding_recap_status_notes`
--

DROP TABLE IF EXISTS `finding_recap_status_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `finding_recap_status_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `finding_recap_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `finding_recap_status_notes_user_id_foreign` (`user_id`),
  KEY `finding_recap_status_notes_finding_recap_id_status_index` (`finding_recap_id`,`status`),
  CONSTRAINT `finding_recap_status_notes_finding_recap_id_foreign` FOREIGN KEY (`finding_recap_id`) REFERENCES `finding_recaps` (`id`) ON DELETE CASCADE,
  CONSTRAINT `finding_recap_status_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finding_recap_status_notes`
--

LOCK TABLES `finding_recap_status_notes` WRITE;
/*!40000 ALTER TABLE `finding_recap_status_notes` DISABLE KEYS */;
INSERT INTO `finding_recap_status_notes` VALUES (14,8,14,'open','Bocor pada bagian Cover Piston Screw Maju-Mundur, perlu bongkar & ganti O-ring','2026-09-04 16:38:59','2026-09-04 16:38:59'),(21,9,14,'open','Proses Order ke Victory, indent +- 2 bulan','2026-09-07 16:32:18','2026-09-07 16:32:18'),(26,11,14,'open','KOORDINASI OFF','2026-09-30 13:41:09','2026-09-30 13:41:09'),(27,9,14,'on_progress','SUDAH PROSES PO KE VICTORY, INDENT 2 BULAN','2026-09-30 13:41:33','2026-09-30 13:41:33'),(28,9,14,'close','Sudah selesai instal toggle perbaikan pada tanggal 13/10/2026 mesin 39. hwc 800T','2026-10-16 16:49:50','2026-10-16 16:49:50'),(29,9,14,'open','Open','2026-10-16 16:50:07','2026-10-16 16:50:07');
/*!40000 ALTER TABLE `finding_recap_status_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finding_recaps`
--

DROP TABLE IF EXISTS `finding_recaps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `finding_recaps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `machine_name_id` bigint unsigned DEFAULT NULL,
  `machine_number` smallint unsigned NOT NULL,
  `machine_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `equipment_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `found_date` date NOT NULL,
  `due_date` date NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `finding_recaps_user_id_foreign` (`user_id`),
  KEY `finding_recaps_machine_name_id_foreign` (`machine_name_id`),
  KEY `finding_recaps_status_found_date_index` (`status`,`found_date`),
  KEY `finding_recaps_machine_number_found_date_index` (`machine_number`,`found_date`),
  CONSTRAINT `finding_recaps_machine_name_id_foreign` FOREIGN KEY (`machine_name_id`) REFERENCES `machine_names` (`id`) ON DELETE SET NULL,
  CONSTRAINT `finding_recaps_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finding_recaps`
--

LOCK TABLES `finding_recaps` WRITE;
/*!40000 ALTER TABLE `finding_recaps` DISABLE KEYS */;
INSERT INTO `finding_recaps` VALUES (8,14,34,26,'LST LST 400 400T','Inject Unit','2026-09-04','2026-09-11','open','2026-09-04 16:38:59','2026-09-04 16:38:59'),(9,14,78,37,'Borche BS 650 - III 650T','Spare part toggle','2026-09-07','2026-11-09','open','2026-09-07 16:32:18','2026-10-16 16:50:07'),(11,14,33,25,'LG 250T','BLOCK VALVE BOCOR, PERLU PREVENTIVE','2026-09-30','2026-10-30','open','2026-09-30 13:41:09','2026-09-30 13:41:09');
/*!40000 ALTER TABLE `finding_recaps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
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
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
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
-- Table structure for table `machine_names`
--

DROP TABLE IF EXISTS `machine_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `machine_names` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `machine_number` smallint unsigned DEFAULT NULL,
  `asset_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `screw_diameter_mm` smallint unsigned DEFAULT NULL,
  `machine_photos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `brand` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `machine_model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tonnage` smallint unsigned DEFAULT NULL,
  `clamping_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `machine_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tie_bar_distance_mm` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platen_size_mm` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mold_thickness_min_mm` smallint unsigned DEFAULT NULL,
  `mold_thickness_max_mm` smallint unsigned DEFAULT NULL,
  `clamp_stroke_mm` smallint unsigned DEFAULT NULL,
  `power_pump_kw` decimal(8,2) DEFAULT NULL,
  `power_heater_kw` decimal(8,2) DEFAULT NULL,
  `power_total_kw` decimal(8,2) DEFAULT NULL,
  `shot_weight_small_gr` smallint unsigned DEFAULT NULL,
  `shot_weight_med_gr` smallint unsigned DEFAULT NULL,
  `manufacture_year` smallint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `specification` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `machine_names_machine_number_unique` (`machine_number`),
  UNIQUE KEY `machine_names_asset_number_unique` (`asset_number`),
  CONSTRAINT `machine_names_chk_1` CHECK (json_valid(`machine_photos`))
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `machine_names`
--

LOCK TABLES `machine_names` WRITE;
/*!40000 ALTER TABLE `machine_names` DISABLE KEYS */;
INSERT INTO `machine_names` VALUES (9,1,'43000000023',1,'[\"machine-photos\\/1\\/7PRYPdzVQ0yjOAbaVAikQZnDS2mBDc73NSIqmpsS.jpg\"]','Hwachin','HC 600 SE',600,'Toggle','Hidrolik Servo','880 x 880','1300 x 1300',275,1020,970,59.00,38.00,97.00,1748,1989,2012,'Hwachin HC 600 SE 600T',NULL,1,'2026-06-12 09:39:48','2026-09-11 13:12:34'),(10,2,'41000000061',75,'[\"machine-photos\\/2\\/2jJgWvZS9I7II64GuwpdDSjtJJFuQemAlqqwjqCC.png\"]','Fu Chung Sin','FCS-HT-450SV',450,'Toggle','Hidrolik Servo','760 x 760','1120 x 1120',250,800,730,44.76,23.05,67.81,840,1011,2011,'Fu Chung Sin FCS-HT-450SV 450T',NULL,1,'2026-08-18 04:28:12','2026-09-04 08:48:13'),(11,3,'41000000238',1,NULL,'Toshiba','IS 550 GS-34 A',550,'Direct Hydraulic','Hidrolik Non-Servo','860 x 860','1230 x 1230',400,400,1200,74.00,29.20,103.20,1,1,1996,'Toshiba IS 550 GS-34 A 550T','Mesin Akan Disposal pada Oktober 2026',1,'2026-08-28 03:37:37','2026-09-07 13:36:12'),(12,4,'41000000142',1,NULL,'Hwa Chin','HC 125 SE',125,'Toggle','Hidrolik Servo','410 x 400','580 x 580',125,425,450,15.00,8.20,23.20,127,180,2012,'Hwa Chin HC 125 SE 125T',NULL,1,'2026-08-28 03:37:37','2026-09-04 08:55:52'),(13,5,'41000000037',1,NULL,'Nissei','FE160S36A',160,'Toggle','Hidrolik Servo','460 x 460','685 x 685',200,410,470,30.00,12.50,42.50,225,314,1995,'Nissei FE160S36A 160T',NULL,1,'2026-08-28 03:37:37','2026-09-04 09:00:25'),(15,7,'41000000170',1,NULL,'LST','LST 160',160,'Toggle','Hidrolik Servo','480 x 480','710 x 710',180,520,435,23.00,11.50,34.50,226,316,2013,'LST LST 160 160T',NULL,1,'2026-09-02 06:53:54','2026-09-04 09:06:00'),(16,8,'41000000060',1,NULL,'Fu Chung Sin','FCS-HT-250SV',250,'Toggle','Hidrolik Servo','555 x 555','820 x 820',160,670,550,29.84,16.53,46.37,429,500,2011,'Fu Chung Sin FCS-HT-250SV 250T',NULL,1,'2026-09-02 06:53:54','2026-09-04 09:11:04'),(17,9,'41000000034',1,NULL,'Borche','BS 120 - III',120,'Toggle','Hidrolik Servo','410 x 410','590 x 590',145,450,340,11.00,8.86,19.86,171,225,2010,'Borche BS 120 - III 120T',NULL,1,'2026-09-02 06:53:54','2026-09-04 09:14:06'),(18,10,'41000000056',1,NULL,'LG','LGH250D',250,'Direct Hydraulic','Hidrolik Non-Servo','610 x 610','830 x 830',240,240,770,37.00,16.17,53.70,551,666,2006,'LG LGH250D 250T',NULL,1,'2026-09-02 06:53:54','2026-09-04 09:18:13'),(19,11,'41000000031',1,NULL,'Borche','BS 260 - III',260,'Toggle','Hidrolik Servo','580 x 580','835 x 835',195,600,520,22.00,16.10,38.10,552,800,2010,'Borche BS 260 - III 260T',NULL,1,'2026-09-02 06:53:54','2026-09-04 10:32:22'),(20,12,'41000000419',1,NULL,'Shibaura','EC850SXIII/i78',850,'Toggle','Elektrik Servo','1320 x 1320','1790 X 1790',500,1100,1200,105.00,44.90,57.20,3160,3980,2023,'Shibaura EC850SXIII/i78 850T',NULL,1,'2026-09-02 06:53:54','2026-09-04 10:34:35'),(21,13,'41000000420',1,NULL,'Shibaura','EC850SXIII/i78',850,'Toggle','Elektrik Servo','1320 x 1320','1790 X 1790',500,1100,1200,105.00,44.40,57.20,3160,3980,2023,'Shibaura EC850SXIII/i78 850T',NULL,1,'2026-09-02 06:53:54','2026-09-04 10:37:27'),(22,14,'41000000447',1,NULL,'Shibaura','TD 850/1120',850,'Toggle','Hidrolik Servo','1120 x 1000','1620 x 1500',500,1170,1030,75.00,44.90,119.90,861,2870,2025,'Shibaura TD 850/1120 850T',NULL,1,'2026-09-02 06:53:54','2026-09-04 10:39:23'),(23,15,'DEMO-015',NULL,NULL,'Demo','Mesin Demo 15',300,'Toggle','Injection',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Mesin Demo 15',NULL,0,'2026-09-02 06:53:54','2026-09-04 10:48:11'),(24,16,'41000000423',1,NULL,'Shibaura','EC350SXIII/i17A',350,'Toggle','Elektrik Servo','820 x 740','1110 X 1030',300,820,650,60.00,46.70,1.00,619,780,2023,'Shibaura EC350SXIII/i17A 350T',NULL,1,'2026-09-02 06:53:54','2026-09-04 10:50:40'),(25,17,'41000000424',1,NULL,'Shibaura','EC350SXIII/i17A',350,'Toggle','Elektrik Servo','820 x 740','1110 X 1030',300,820,650,60.00,46.70,1.00,619,780,2023,'Shibaura EC350SXIII/i17A 350T',NULL,1,'2026-09-02 06:53:54','2026-09-04 13:23:12'),(26,18,'41000000450',1,NULL,'JSW','J450AD-1400H',450,'Toggle','Elektrik Servo','900 X 810','1300 X 1210',350,800,1600,1.00,36.00,51.00,1238,1,2011,'JSW J450AD-1400H 450T',NULL,1,'2026-09-02 06:53:54','2026-09-04 13:27:37'),(27,19,'41000000451',1,NULL,'JSW','J450AD-1400H',450,'Toggle','Elektrik Servo','900 X 810','1300 X 1210',350,800,1600,1.00,36.00,50.98,1238,1,2011,'JSW J450AD-1400H 450T',NULL,1,'2026-09-02 06:53:54','2026-09-04 13:39:19'),(28,20,'41000000461',1,NULL,'JSW','J450AD-1400H',450,'Toggle','Elektrik Servo','900 X 810','1301 X 1210',350,800,1600,1.00,36.00,51.00,1238,1,2013,'JSW J450AD-1400H 450T',NULL,1,'2026-09-02 06:53:54','2026-09-04 13:40:48'),(29,21,'41000000452',1,NULL,'JSW','J450AD-1400H',450,'Toggle','Elektrik Servo','900 X 810','1302 X 1210',350,800,1600,1.00,36.00,51.01,1238,1,2012,'JSW J450AD-1400H 450T',NULL,1,'2026-09-02 06:53:54','2026-09-04 13:42:35'),(30,22,'41000000453',1,NULL,'JSW','J450AD-1400H',450,'Toggle','Elektrik Servo','900 X 810','1303 X 1210',350,800,1600,1.00,36.00,51.00,1238,1,2012,'JSW J450AD-1400H 450T',NULL,1,'2026-09-02 06:53:54','2026-09-04 13:44:15'),(31,23,'41000000449',1,NULL,'JSW','J450AD-2300H',200,'Toggle','Elektrik Servo','900 X 810','1300 X 1210',350,800,1600,1.00,41.10,55.90,2118,1,2012,'JSW J450AD-2300H 200T',NULL,1,'2026-09-02 06:53:54','2026-09-04 14:00:20'),(32,24,'41000000460',1,NULL,'JSW','J450AD-2300H',450,'Toggle','Elektrik Servo','900 X 810','1300 X 1210',350,800,1600,1.00,41.10,55.90,2118,1,2012,'JSW J450AD-2300H 450T',NULL,1,'2026-09-02 06:53:54','2026-09-04 14:02:51'),(33,25,'41000000056-2',1,NULL,'LG','LGH250D',250,'Direct Hydaulic','Hidrolik Non-Servo','610 x 610','830 x 830',240,240,770,37.00,16.70,53.70,551,666,2006,'LG LGH250D 250T',NULL,1,'2026-09-02 06:53:54','2026-09-04 14:05:22'),(34,26,'41000000173-1',1,NULL,'LST','LST 400',400,'Toggle','Hidrolik Servo','730 x 730','1470 x 1470',250,780,720,58.00,26.00,84.00,1293,1722,2013,'LST LST 400 400T',NULL,1,'2026-09-02 06:53:54','2026-09-04 14:07:10'),(69,27,'41000000173-2',1,NULL,'LST','LST 400',400,'Toggle','Hidrolik Servo','730 x 730','1470 x 1470',250,780,720,58.00,26.00,84.00,1293,1722,2013,'LST LST 400 400T',NULL,1,'2026-09-04 14:12:17','2026-09-04 14:12:17'),(70,28,'41000000171',1,NULL,'LST','LST 280',280,'Toggle','Hidrolik Servo','630 x 630','940 x 940',200,630,590,39.00,16.00,55.00,509,733,2013,'LST LST 280 280T',NULL,1,'2026-09-04 16:16:53','2026-09-04 16:16:53'),(71,30,'41000000013',1,NULL,'Borche','BS 320 - III',320,'Toggle','Hidrolik Servo','670 x 670','950 x 950',220,655,580,30.00,16.10,46.10,552,800,2010,'Borche BS 320 - III 320T',NULL,1,'2026-09-04 16:18:37','2026-09-04 16:18:37'),(72,31,'41000000051',1,NULL,'JSW','J - 550E - III',550,'t','Hidrolik Servo','900 x 900','1300 x 1300',500,900,1300,70.00,39.20,109.20,2118,2541,1998,'JSW J - 550E - III 550T',NULL,1,'2026-09-04 16:20:00','2026-09-04 16:20:00'),(73,32,'43000000023-2',1,NULL,'Hwa Chin','HC 600 SE',600,'Toggle','Hidrolik Servo','880 x 880','1300 x 1300',275,1020,970,59.00,38.00,97.00,1748,1989,2012,'Hwa Chin HC 600 SE 600T',NULL,1,'2026-09-04 16:22:01','2026-09-04 16:22:01'),(74,33,'41000000368',1,NULL,'Woojin','Woojin DL850A5',850,'Half-Nut Two Platen','Hidrolik Servo','1110 X 1110','1610 X 1610',500,1200,1,94.00,49.60,143.60,3788,4628,2019,'Woojin Woojin DL850A5 850T',NULL,1,'2026-09-04 16:23:51','2026-09-04 16:23:51'),(75,34,'41000000367',1,NULL,'Woojin','Woojin DL450A5',450,'Half-Nut Two Platen','Hidrolik Servo','860 X 810','1240 X 1190',350,800,1,52.00,24.20,76.20,1177,1365,2019,'Woojin Woojin DL450A5 450T',NULL,1,'2026-09-04 16:26:40','2026-09-04 16:26:40'),(76,35,'41000000370',1,NULL,'Woojin','Woojin DL850A5',850,'Half-Nut Two Platen','h','1110 X 1110','1610 X 1610',500,1200,1,94.00,49.60,143.60,3788,4628,2019,'Woojin Woojin DL850A5 850T',NULL,1,'2026-09-04 16:27:56','2026-09-04 16:27:56'),(77,36,'41000000443',1,NULL,'Shibaura','TD 650/1000',650,'Toggle','Hidrolik Servo','1000X900','1430X1350',450,1070,930,55.00,1.00,55.00,1940,1,2025,'Shibaura TD 650/1000 650T',NULL,1,'2026-09-04 16:57:46','2026-09-04 16:57:46'),(78,37,'41000000040',1,NULL,'Borche','BS 650 - III',650,'Toggle','Hidrolik Servo','930 x 900','1450 x 1300',300,870,880,51.00,33.20,84.20,2125,2690,2010,'Borche BS 650 - III 650T',NULL,1,'2026-09-04 16:59:27','2026-09-04 16:59:27'),(79,38,'41000000442',1,NULL,'Shibaura','TD 550/870',550,'Toggle','Hidrolik Servo','870X870','1265X1265',380,900,980,55.00,1.00,1.00,1940,1,2025,'Shibaura TD 550/870 550T',NULL,1,'2026-09-04 17:00:48','2026-09-04 17:00:48'),(80,39,'41000000084',1,NULL,'Hwa Chin','HC 800 SE',800,'Toggle','Hidrolik Servo','1005 x 1000','1470 x 1470',300,1100,1080,74.20,38.00,112.20,2124,2689,2012,'Hwa Chin HC 800 SE 800T',NULL,1,'2026-09-04 17:02:22','2026-09-04 17:02:22'),(81,40,'41000000056-3',1,NULL,'LG','LGH250D',250,'Direct Hydaulic','Hidrolik Non-Servo','610 x 610','830 x 830',240,240,770,37.00,16.70,53.70,551,666,2005,'LG LGH250D 250T',NULL,1,'2026-09-04 17:04:05','2026-09-04 17:04:05'),(82,41,'43000000022',1,NULL,'Hwa Chin','HC 250 SE',250,'Toggle','Hidrolik Servo','560 x 550','810 x 810',180,600,600,30.00,16.00,46.00,382,502,2012,'Hwa Chin HC 250 SE 250T',NULL,1,'2026-09-04 17:05:50','2026-09-04 17:05:50'),(83,42,'43000000022-2',1,NULL,'Hwa Chin','HC 250 SE',250,'Toggle','Hidrolik Servo','560 x 550','810 x 810',180,600,600,30.00,16.00,46.00,382,502,2012,'Hwa Chin HC 250 SE 250T',NULL,1,'2026-09-04 17:07:02','2026-09-04 17:07:02'),(84,43,'41000000441',1,NULL,'Shibaura','TD 250/580',250,'Toggle','Hidrolik Servo','580x580','860x860',350,790,790,30.00,1.00,1.00,1,1,2024,'Shibaura TD 250/580 250T',NULL,1,'2026-09-04 17:08:25','2026-09-04 17:08:25'),(85,44,'41000000459',1,NULL,'JSW','J650AD-2300H',650,'Toggle','Elektrik Servo','1060 X 960','1500 X 1400',450,1000,2000,1.00,39.20,1.00,2118,2118,2017,'JSW J650AD-2300H 650T',NULL,1,'2026-09-04 17:09:54','2026-09-04 17:09:54'),(86,45,'41000000457',1,NULL,'JSW','J450AD-1400H',450,'Toggle','Elektrik Servo','900 X 810','1301 X 1210',350,800,1600,1.00,36.00,1.00,1238,1238,2013,'JSW J450AD-1400H 450T',NULL,1,'2026-09-04 17:11:23','2026-09-04 17:11:23'),(87,46,'41000000455',1,NULL,'JSW','J450AD-2300H',450,'Toggle','Elektrik Servo','900 X 810','1300 X 1210',350,800,1600,1.00,41.10,55.90,2118,2118,2013,'JSW J450AD-2300H 450T',NULL,1,'2026-09-04 17:12:36','2026-09-04 17:12:36'),(88,47,'41000000456',1,NULL,'JSW','J450AD-2300H',450,'Toggle','Elektrik Servo','900 X 810','1300 X 1210',350,800,1600,1.00,41.10,55.90,2118,2118,2013,'JSW J450AD-2300H 450T',NULL,1,'2026-09-04 17:14:41','2026-09-04 17:14:41'),(89,48,'41000000458',1,NULL,'JSW','J650AD-2300H',650,'Toggle','Elektrik Servo','1060 X 960','1500 X 1400',450,1000,2000,1.00,39.20,1.00,2118,2118,2013,'JSW J650AD-2300H 650T',NULL,1,'2026-09-04 17:16:43','2026-09-04 17:16:43'),(90,49,'41000000082-1',1,NULL,'Hwa Chin','HC 450 SE',450,'Toggle','Hidrolik Servo','780 x 780','1160 x 1160',250,870,900,45.00,31.00,76.00,1385,1590,2012,'Hwa Chin HC 450 SE 450T',NULL,1,'2026-09-07 13:13:41','2026-09-07 13:13:41'),(91,50,'41000000082-2',1,NULL,'Hwa Chin','HC 450 SE',450,'Toggle','Hidrolik Servo','780 x 780','1160 x 1160',250,870,900,45.00,31.00,76.00,1385,1590,2012,'Hwa Chin HC 450 SE 450T',NULL,1,'2026-09-07 13:15:12','2026-09-07 13:15:12'),(92,51,'41000000090',1,NULL,'Hwa Chin','HC 250 SE',450,'Toggle','Hidrolik Servo','780 x 780','1160 x 1160',250,870,900,45.00,31.00,76.00,1385,1590,2012,'Hwa Chin HC 250 SE 450T',NULL,1,'2026-09-07 13:16:44','2026-09-07 13:16:44'),(93,52,'41000000173',1,NULL,'LST','LST 400',400,'Toggle','Hidrolik Servo','730 x 730','1470 x 1470',250,780,720,58.00,26.00,84.00,1293,1722,2013,'LST LST 400 400T',NULL,1,'2026-09-07 13:18:07','2026-09-07 13:18:07'),(94,53,'41000000084-2',1,NULL,'Hwa Chin','HC 800 SE',800,'Toggle','Hidrolik Servo','1005 x 1000','1470 x 1470',300,1100,1080,74.20,38.00,112.20,2124,2689,2012,'Hwa Chin HC 800 SE 800T',NULL,1,'2026-09-07 13:19:34','2026-09-07 13:19:34'),(95,54,'41000000085',1,NULL,'JSW','J850 E - II',850,'Toggle','Hidrolik Non-Servo','1060 x 1060','1590 x 1590',550,1100,1200,85.10,46.30,131.40,3574,4323,1999,'JSW J850 E - II 850T',NULL,1,'2026-09-07 13:20:54','2026-09-07 13:20:54'),(96,55,'41000000414',1,NULL,'Toshiba','1300T',1300,'Half-Nut Two Platen','Hidrolik Servo','1340 x 1300','2000 x 1900',1,1,1,1.00,1.00,1.00,1,1,2005,'Toshiba 1300T 1300T',NULL,1,'2026-09-07 13:22:04','2026-09-07 13:22:04'),(97,57,'41000000274',1,NULL,'Woojin','Vertical VHA 50RS',50,'Toggle','Hidrolik Servo','1','1',1,1,1,1.00,1.00,1.00,1,1,2017,'Woojin Vertical VHA 50RS 50T',NULL,1,'2026-09-07 13:23:16','2026-09-07 13:23:16'),(98,58,'41000000232',1,NULL,'Jet Master','Jet Master 88 TON',88,'Toggle','Hidrolik Non-Servo','380 x 380','530 X 530',150,350,460,22.00,8.00,30.00,1,1,1996,'Jet Master Jet Master 88 TON 88T',NULL,1,'2026-09-07 13:24:31','2026-09-07 13:24:31'),(99,59,'41000000233',1,NULL,'Toshiba','100 TON',100,'Toggle','Elektrik Servo','460 x 410','660 x 610',180,180,350,1.00,1.00,1.00,1,1,2005,'Toshiba 100 TON 100T',NULL,1,'2026-09-07 13:25:36','2026-09-07 13:25:36'),(100,60,'43000000123',1,NULL,'Toshiba','EC-30S (i0.4A)',30,'Toggle','Elektrik Servo','320 x 290','440 x 420',150,330,230,1.00,1.00,1.00,13,16,2015,'Toshiba EC-30S (i0.4A) 30T',NULL,1,'2026-09-07 13:26:41','2026-09-09 11:49:09');
/*!40000 ALTER TABLE `machine_names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maintenance_reports`
--

DROP TABLE IF EXISTS `maintenance_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `maintenance_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `report_date` date NOT NULL,
  `machine_number` tinyint unsigned NOT NULL,
  `machine_name_id` bigint unsigned DEFAULT NULL,
  `machine_name_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `damage_category_id` bigint unsigned NOT NULL,
  `damage_category_other` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mold_assy_utility` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `pic_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `work_shift` enum('shift_1','shift_2','shift_3') COLLATE utf8mb4_unicode_ci NOT NULL,
  `repair_started_at` datetime NOT NULL,
  `repair_finished_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `masalah` text COLLATE utf8mb4_unicode_ci,
  `analisa_penyebab_kerusakan` text COLLATE utf8mb4_unicode_ci,
  `damage_cause_category` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tindakan_perbaikan` text COLLATE utf8mb4_unicode_ci,
  `tindakan_perbaikan_2` text COLLATE utf8mb4_unicode_ci,
  `evidence_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penggunaan_sparepart` text COLLATE utf8mb4_unicode_ci,
  `sparepart_items` json DEFAULT NULL,
  `ada_op` tinyint(1) NOT NULL DEFAULT '0',
  `status_perbaikan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `step2_completed_at` timestamp NULL DEFAULT NULL,
  `operator_report_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `maintenance_reports_operator_report_id_unique` (`operator_report_id`),
  KEY `maintenance_reports_machine_name_id_foreign` (`machine_name_id`),
  KEY `maintenance_reports_damage_category_id_foreign` (`damage_category_id`),
  KEY `maintenance_reports_user_id_foreign` (`user_id`),
  KEY `maintenance_reports_machine_number_report_date_index` (`machine_number`,`report_date`),
  CONSTRAINT `maintenance_reports_damage_category_id_foreign` FOREIGN KEY (`damage_category_id`) REFERENCES `damage_categories` (`id`),
  CONSTRAINT `maintenance_reports_machine_name_id_foreign` FOREIGN KEY (`machine_name_id`) REFERENCES `machine_names` (`id`) ON DELETE SET NULL,
  CONSTRAINT `maintenance_reports_operator_report_id_foreign` FOREIGN KEY (`operator_report_id`) REFERENCES `operator_reports` (`id`) ON DELETE SET NULL,
  CONSTRAINT `maintenance_reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maintenance_reports`
--

LOCK TABLES `maintenance_reports` WRITE;
/*!40000 ALTER TABLE `maintenance_reports` DISABLE KEYS */;
INSERT INTO `maintenance_reports` VALUES (3,'2026-08-28',1,9,NULL,2,NULL,'Shell TRX',13,'Andi','shift_2','2026-08-28 11:18:00','2026-08-28 11:18:00','2026-08-28 04:18:19','2026-09-02 04:57:50','Reject','Reject',NULL,'Selang oiasdas',NULL,NULL,'Reject',NULL,1,'close','2026-08-28 04:18:32',5),(9,'2026-09-03',22,30,NULL,1,NULL,NULL,13,'Andi','shift_2','2026-09-03 05:22:44','2026-09-03 06:32:44','2026-09-03 02:11:15','2026-09-04 16:11:31','Interlock safety door mati.','Limit switch aus dan kabel longgar.',NULL,'Ganti limit switch dan tes interlock 5x.',NULL,NULL,'Limit switch safety door — 1 pcs',NULL,1,'close','2026-09-02 23:32:44',13),(11,'2026-09-03',4,12,NULL,1,NULL,NULL,13,'Andi','shift_2','2026-09-03 04:22:44','2026-09-03 04:47:44','2026-09-03 02:11:15','2026-09-03 02:22:44','Level oli hidrolik rendah dan filter kotor.','Filter suction tersumbat, oli di bawah min.',NULL,'Isi oli dan ganti filter suction.',NULL,NULL,'Filter oli hidrolik — 1 pcs, Oli hidrolik 20 L',NULL,1,'close','2026-09-02 21:47:44',15),(12,'2026-09-03',9,17,NULL,3,NULL,NULL,13,'Budi','shift_2','2026-09-03 04:42:44','2026-09-03 05:32:44','2026-09-03 02:11:15','2026-09-03 02:22:44','Pompa MTC lemah, heater MTC cycling.','Filter air MTC tersumbat kerak.',NULL,'Bersihkan filter dan flushing saluran mold.',NULL,NULL,'-',NULL,1,'close','2026-09-02 22:32:44',16),(13,'2026-09-03',18,26,NULL,1,NULL,NULL,13,'Cici PIC','shift_2','2026-09-03 03:22:44','2026-09-03 04:57:44','2026-09-03 02:11:15','2026-09-03 02:22:44','Material hang di barrel, screw macet.','Suhu zona 3 drop saat idle, material padat.',NULL,'Purge material, naikkan suhu, cek heater band.',NULL,NULL,'Heater band zona 3 — 1 pcs',NULL,1,'close','2026-09-02 21:57:44',17),(15,'2026-09-08',3,11,NULL,2,NULL,'Cover Dashboard Avanza',13,'Udin','shift_2','2026-09-08 09:22:00','2026-09-08 09:22:00','2026-09-08 09:22:51','2026-09-09 13:23:28','Mesin alarm hydraulic pressure low, clamping tidak mengunci.','sudah oke',NULL,'sudah oke',NULL,NULL,'sudah oke',NULL,1,'close','2026-09-08 09:23:06',7),(16,'2026-09-08',7,15,NULL,1,NULL,'Panel Pintu Kanan',13,'Udin','shift_2','2026-09-08 09:26:00','2026-09-08 09:50:00','2026-09-08 09:29:58','2026-10-20 15:18:52','Heater zona 2 tidak naik suhu, material dingin di nozzle.','ssr mati','error_program','Ganti ssr 24vdc',NULL,NULL,'SSR 24V',NULL,1,'close','2026-09-08 09:31:17',8),(18,'2026-09-08',36,77,NULL,2,NULL,'Test3',13,'Udin','shift_2','2026-09-08 11:10:00','2026-09-08 11:10:00','2026-09-08 11:10:34','2026-09-08 11:10:45','test3','test',NULL,'test',NULL,NULL,'test',NULL,1,'close_by_technician','2026-09-08 11:10:45',23),(20,'2026-10-20',18,26,NULL,1,NULL,'Cover Front',13,'Khasanudin','shift_2','2026-09-14 13:10:00','2026-10-16 14:06:00','2026-10-16 14:06:44','2026-10-20 16:17:30',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'on_progress',NULL,21),(21,'2026-10-15',2,10,NULL,1,NULL,NULL,13,'Khasanudin','shift_2','2026-10-15 14:24:27','2026-10-15 15:19:27','2026-10-16 14:54:27','2026-10-16 14:54:27','Seal clamping aus.','Umur seal sudah habis.',NULL,'Ganti seal clamping.',NULL,NULL,'- Seal clamping 50x70x10 x 2 pcs\n- O-ring P20 x 4 pcs','[{\"qty\": 2, \"name\": \"Seal clamping 50x70x10\"}, {\"qty\": 4, \"name\": \"O-ring P20\"}]',1,'close','2026-10-15 15:19:27',25),(22,'2026-10-14',6,NULL,NULL,1,NULL,NULL,13,'Welly','shift_2','2026-10-14 14:14:27','2026-10-14 15:24:27','2026-10-16 14:54:27','2026-10-16 14:54:27','Heater band zona 1 putus.','Heater band terbakar.',NULL,'Ganti heater band.',NULL,NULL,'- Heater band zona 1 x 1 pcs','[{\"qty\": 1, \"name\": \"Heater band zona 1\"}]',1,'close','2026-10-14 15:24:27',26),(23,'2026-10-13',10,18,NULL,1,NULL,NULL,13,'Erickson','shift_2','2026-10-13 14:29:27','2026-10-13 15:49:27','2026-10-16 14:54:28','2026-10-16 14:54:28','Hose hidrolik retak.','Tekanan tinggi dan umur selang.',NULL,'Ganti selang dan fitting.',NULL,NULL,'- Selang hidrolik 1/2\" x 1 pcs\n- Fitting hidrolik 1/2\" x 2 pcs','[{\"qty\": 1, \"name\": \"Selang hidrolik 1/2\\\"\"}, {\"qty\": 2, \"name\": \"Fitting hidrolik 1/2\\\"\"}]',1,'close','2026-10-13 15:49:27',27),(24,'2026-10-11',14,22,NULL,1,NULL,NULL,13,'Khasanudin','shift_2','2026-10-11 14:34:27','2026-10-11 15:14:27','2026-10-16 14:54:28','2026-10-16 14:54:28','Limit switch ejector aus.','Kontak switch sudah patah.',NULL,'Ganti limit switch.',NULL,NULL,'- Limit switch ejector x 1 pcs','[{\"qty\": 1, \"name\": \"Limit switch ejector\"}]',1,'close','2026-10-11 15:14:27',28),(25,'2026-10-09',20,28,NULL,3,NULL,NULL,13,'Welly','shift_2','2026-10-09 14:19:27','2026-10-09 15:49:27','2026-10-16 14:54:28','2026-10-16 14:54:28','Mechanical seal pompa MTC bocor.','Seal aus dan impeller kotor.',NULL,'Ganti mechanical seal dan bersihkan impeller.',NULL,NULL,'- Mechanical seal pompa MTC x 1 pcs\n- Filter air MTC x 2 pcs','[{\"qty\": 1, \"name\": \"Mechanical seal pompa MTC\"}, {\"qty\": 2, \"name\": \"Filter air MTC\"}]',1,'close','2026-10-09 15:49:27',29),(26,'2026-10-08',25,33,NULL,2,NULL,NULL,13,'Erickson','shift_2','2026-10-08 14:39:27','2026-10-08 15:14:27','2026-10-16 14:54:28','2026-10-16 14:54:28','Sensor proximity mold rusak.','Kabel sensor putus dekat connector.',NULL,'Ganti proximity sensor.',NULL,NULL,'- Proximity sensor M12 x 1 pcs\n- Connector sensor 4 pin x 1 pcs','[{\"qty\": 1, \"name\": \"Proximity sensor M12\"}, {\"qty\": 1, \"name\": \"Connector sensor 4 pin\"}]',1,'close','2026-10-08 15:14:27',30);
/*!40000 ALTER TABLE `maintenance_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_05_25_135621_add_role_to_users_table',1),(5,'2026_05_25_135621_create_machine_names_table',1),(6,'2026_05_25_135622_create_damage_categories_table',1),(7,'2026_05_25_135623_create_maintenance_reports_table',1),(8,'2026_05_25_141917_remove_code_from_damage_categories_table',2),(9,'2026_05_25_144343_add_step2_fields_to_maintenance_reports_table',2),(10,'2026_05_25_200000_rename_email_to_npk_on_users_table',2),(11,'2026_06_12_000001_add_fields_to_machine_names_table',2),(12,'2026_06_12_000002_add_spec_fields_to_machine_names_table',2),(13,'2026_06_12_000003_add_asset_and_photos_to_machine_names_table',2),(14,'2026_08_18_000001_create_preventive_reports_table',3),(15,'2026_08_20_000001_create_operator_reports_table',4),(16,'2026_08_20_000002_add_claim_fields_to_operator_reports',5),(17,'2026_08_20_000003_add_status_to_operator_reports',6),(18,'2026_08_28_000001_create_technicians_table',7),(19,'2026_08_28_000002_add_claimed_technician_id_to_operator_reports',7),(20,'2026_09_02_000001_create_preventive_conveyor_reports_table',8),(21,'2026_09_02_000002_add_category_to_preventive_reports_table',9),(22,'2026_09_02_000003_add_readings_to_preventive_reports_table',10),(23,'2026_09_03_000001_create_finding_recaps_tables',11),(24,'2026_09_03_000001_create_robots_table',12),(25,'2026_09_03_000002_add_robot_to_preventive_reports_table',12),(26,'2026_09_03_000003_add_equipment_number_to_preventive_reports_table',13),(27,'2026_09_07_000001_create_preventive_checklist_items_table',14),(28,'2026_09_09_000001_create_preventive_schedules_table',15),(29,'2026_09_09_000002_add_pic_name_to_preventive_schedules_table',16),(30,'2026_09_10_000001_create_equipments_table',17),(31,'2026_09_10_000002_add_equipment_id_to_preventive_reports_table',17),(32,'2026_10_16_000001_remove_quality_users',18),(33,'2026_10_16_000002_add_damage_cause_category_to_maintenance_reports',19),(34,'2026_10_16_000003_add_evidence_paths_to_preventive_reports',20),(35,'2026_10_16_000004_add_sparepart_items_to_maintenance_reports',21),(36,'2026_10_19_000001_add_status_to_preventive_reports',22);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `operator_reports`
--

DROP TABLE IF EXISTS `operator_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `operator_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `operator_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `npk` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `machine_name_id` bigint unsigned DEFAULT NULL,
  `machine_number` tinyint unsigned NOT NULL,
  `stop_date` date NOT NULL,
  `stop_time` time NOT NULL,
  `problem` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `chronology` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `claimed_by` bigint unsigned DEFAULT NULL,
  `claimed_technician_id` bigint unsigned DEFAULT NULL,
  `claimed_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  PRIMARY KEY (`id`),
  KEY `operator_reports_machine_name_id_foreign` (`machine_name_id`),
  KEY `operator_reports_machine_number_stop_date_index` (`machine_number`,`stop_date`),
  KEY `operator_reports_claimed_by_foreign` (`claimed_by`),
  KEY `operator_reports_claimed_technician_id_foreign` (`claimed_technician_id`),
  CONSTRAINT `operator_reports_claimed_by_foreign` FOREIGN KEY (`claimed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `operator_reports_claimed_technician_id_foreign` FOREIGN KEY (`claimed_technician_id`) REFERENCES `technicians` (`id`) ON DELETE SET NULL,
  CONSTRAINT `operator_reports_machine_name_id_foreign` FOREIGN KEY (`machine_name_id`) REFERENCES `machine_names` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operator_reports`
--

LOCK TABLES `operator_reports` WRITE;
/*!40000 ALTER TABLE `operator_reports` DISABLE KEYS */;
INSERT INTO `operator_reports` VALUES (4,'Niken','20222026','Shell Trx',9,1,'2026-08-28','10:54:00','Down Time','Down Time','2026-08-28 03:54:27','2026-08-28 04:13:39',NULL,1,'2026-08-28 03:58:37','close'),(5,'Niken','20262026','Shell TRX',9,1,'2026-08-28','11:17:00','Reject','Reject','2026-08-28 04:17:29','2026-09-02 04:57:50',13,1,'2026-08-28 04:17:55','close'),(7,'Rina Wulandari','880001','Cover Dashboard Avanza',11,3,'2026-09-03','07:15:00','Mesin alarm hydraulic pressure low, clamping tidak mengunci.','Alarm muncul saat mulai cycle pertama shift 1.','2026-09-03 02:11:15','2026-09-09 13:23:28',13,1,'2026-09-08 08:58:10','close'),(8,'Joko Santoso','880002','Panel Pintu Kanan',15,7,'2026-09-03','08:40:00','Heater zona 2 tidak naik suhu, material dingin di nozzle.','Suhu zona 2 stuck di 80°C selama 10 menit.','2026-09-03 02:11:15','2026-10-20 15:18:52',13,1,'2026-09-08 09:07:10','close'),(13,'Bayu Firmansyah','880007','Spoiler Belakang',30,22,'2026-09-03','05:10:00','Safety door interlock tidak merespons, mesin tidak auto.','Setelah istirahat, interlock tidak detect pintu tertutup.','2026-09-02 22:14:44','2026-09-04 16:11:31',13,1,'2026-09-02 22:22:44','close'),(15,'Eko Wijaya','880009','Trim Pintu Dalam',12,4,'2026-09-03','04:30:00','Pompa hidrolik berisik, tekanan naik-turun.','Suara cavitation terdengar sejak awal shift.','2026-09-02 21:14:44','2026-09-02 21:47:44',13,1,'2026-09-02 21:22:44','close'),(16,'Nur Aisyah','880010','Handle Pintu',17,9,'2026-09-03','03:45:00','MTC tidak stabil, mold terlalu dingin.','Suhu mold turun 15°C dari setting.','2026-09-02 21:34:44','2026-09-02 22:32:44',13,2,'2026-09-02 21:42:44','close'),(17,'Hendra Gunawan','880011','Cover Radiator',26,18,'2026-09-03','02:20:00','Screw stuck, injection tidak maju.','Material hang di barrel setelah idle 40 menit.','2026-09-02 20:14:44','2026-09-02 21:57:44',13,3,'2026-09-02 20:22:44','close'),(21,'prayudiii','20221915','Cover Front',26,18,'2026-09-08','09:47:00','Test 1','Test 1','2026-09-08 09:47:34','2026-09-14 13:10:18',13,1,'2026-09-14 13:10:18','on_progress'),(23,'prayudiiiii','20221918','Test3',77,36,'2026-09-08','09:48:00','test3','test3','2026-09-08 09:48:55','2026-09-08 11:10:45',13,1,'2026-09-08 11:10:19','close_by_technician'),(24,'Prayudi','2222222','Shell',15,7,'2026-10-16','13:12:00','test','test','2026-10-16 13:12:17','2026-10-16 13:12:17',NULL,NULL,NULL,'open'),(25,'Rudi Hartono','990001','Cover Dashboard',10,2,'2026-10-15','08:10:00','Seal clamping bocor, oli menetes.',NULL,'2026-10-15 14:16:27','2026-10-15 15:19:27',13,1,'2026-10-15 14:24:27','close'),(26,'Wati Suryani','990002','Panel Pintu',NULL,6,'2026-10-14','09:40:00','Heater zona 1 mati.',NULL,'2026-10-14 14:06:27','2026-10-14 15:24:27',13,2,'2026-10-14 14:14:27','close'),(27,'Tono Wijaya','990003','Bumper Depan',18,10,'2026-10-13','13:20:00','Selang hidrolik pecah.',NULL,'2026-10-13 14:21:27','2026-10-13 15:49:27',13,3,'2026-10-13 14:29:27','close'),(28,'Sinta Lestari','990004','Grill Depan',22,14,'2026-10-11','07:05:00','Limit switch ejector error.',NULL,'2026-10-11 14:26:27','2026-10-11 15:14:27',13,1,'2026-10-11 14:34:27','close'),(29,'Fajar Nugroho','990005','Console Box',28,20,'2026-10-09','15:45:00','Pompa MTC tidak stabil.',NULL,'2026-10-09 14:11:27','2026-10-09 15:49:27',13,2,'2026-10-09 14:19:27','close'),(30,'Dewi Anggraini','990006','Cover Spion',33,25,'2026-10-08','11:30:00','Proximity mold tidak detect.',NULL,'2026-10-08 14:31:27','2026-10-08 15:14:27',13,3,'2026-10-08 14:39:27','close');
/*!40000 ALTER TABLE `operator_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `preventive_checklist_items`
--

DROP TABLE IF EXISTS `preventive_checklist_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `preventive_checklist_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_key` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_label` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'condition',
  `layout` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_key` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `standard` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `has_input` tinyint(1) NOT NULL DEFAULT '0',
  `input_placeholder` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `input_suffix` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group_sort_order` int unsigned NOT NULL DEFAULT '0',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `preventive_checklist_items_category_item_key_unique` (`category`,`item_key`),
  KEY `pci_category_active_idx` (`category`,`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=331 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preventive_checklist_items`
--

LOCK TABLES `preventive_checklist_items` WRITE;
/*!40000 ALTER TABLE `preventive_checklist_items` DISABLE KEYS */;
INSERT INTO `preventive_checklist_items` VALUES (1,'PM1','voltage','Tegangan','measurement','voltage','voltage_rs','R-S','',1,'380','VAC',0,0,1,'2026-09-07 14:14:08','2026-09-07 14:14:08'),(2,'PM1','voltage','Tegangan','measurement','voltage','voltage_st','S-T','',1,'380','VAC',0,1,1,'2026-09-07 14:14:08','2026-09-07 14:14:08'),(3,'PM1','voltage','Tegangan','measurement','voltage','voltage_rt','R-T','',1,'380','VAC',0,2,1,'2026-09-07 14:14:08','2026-09-07 14:14:08'),(4,'PM1','voltage','Tegangan','measurement','voltage','voltage_pe_n','PE-N','',1,'0','VAC',0,3,1,'2026-09-07 14:14:08','2026-09-07 14:14:08'),(5,'PM1','voltage','Tegangan','measurement','voltage','voltage_power_supply','Tegangan Power Supply','',1,'24','VDC',0,4,1,'2026-09-07 14:14:08','2026-09-07 14:14:08'),(6,'PM1','electrical','Electrical Section Check','condition',NULL,'thermocouple','Thermocouple','Berfungsi',0,NULL,NULL,1,0,1,'2026-09-07 14:14:08','2026-09-07 14:14:08'),(7,'PM1','electrical','Electrical Section Check','condition',NULL,'coil_band_heater','Coil/ Band Heater','Berfungsi',0,NULL,NULL,1,1,1,'2026-09-07 14:14:08','2026-09-07 14:14:08'),(8,'PM1','electrical','Electrical Section Check','condition',NULL,'heater_wiring','Heater Wiring','Rapi',0,NULL,NULL,1,2,1,'2026-09-07 14:14:08','2026-09-07 14:14:08'),(9,'PM1','electrical','Electrical Section Check','condition',NULL,'heater_ssr_mcb_fuse','Heater, SSR, MCB, Fuse','Berfungsi',0,NULL,NULL,1,3,1,'2026-09-07 14:14:08','2026-09-07 14:14:08'),(10,'PM1','electrical','Electrical Section Check','condition',NULL,'magnetic_contactor','Magnetic Contactor','Berfungsi',0,NULL,NULL,1,4,1,'2026-09-07 14:14:08','2026-09-07 14:14:08'),(11,'PM1','electrical','Electrical Section Check','condition',NULL,'operation_panel','Operation Panel','Berfungsi',0,NULL,NULL,1,5,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(12,'PM1','electrical','Electrical Section Check','condition',NULL,'selector_switch','Selector Switch','Berfungsi',0,NULL,NULL,1,6,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(13,'PM1','electrical','Electrical Section Check','condition',NULL,'cooling_fan_panel','Kipas Pendingin Panel','',0,NULL,NULL,1,7,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(14,'PM1','electrical','Electrical Section Check','condition',NULL,'cooling_fan_motor_servo','Kipas Pendingin Motor/Servo','',0,NULL,NULL,1,8,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(15,'PM1','electrical','Electrical Section Check','condition',NULL,'electrical_component_temp','Temperatur Komponen Elektrik','< 40°C',1,'36','°C',1,9,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(16,'PM1','safety','Safety Section Check','condition',NULL,'limit_switch_front_door','Limit Switch Pintu bag. Depan','Berfungsi',0,NULL,NULL,2,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(17,'PM1','safety','Safety Section Check','condition',NULL,'limit_switch_side_door','Limit Switch Pintu bag. Samping','Berfungsi',0,NULL,NULL,2,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(18,'PM1','safety','Safety Section Check','condition',NULL,'emergency_button','Tombol Emergency','Berfungsi',0,NULL,NULL,2,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(19,'PM1','safety','Safety Section Check','condition',NULL,'nozzle_safety_door','Pintu Safety Nozzle','',0,NULL,NULL,2,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(20,'PM1','hydraulic','Hydraulic Section Check','condition',NULL,'oil_clamp_cylinder','Oli dari Silinder Clamping','Tidak Bocor',0,NULL,NULL,3,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(21,'PM1','hydraulic','Hydraulic Section Check','condition',NULL,'oil_injection_cylinder','Oli dari Silinder Injeksi','Tidak Bocor',0,NULL,NULL,3,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(22,'PM1','hydraulic','Hydraulic Section Check','condition',NULL,'oil_ejector_cylinder','Oli dari Silinder Ejector','Tidak Bocor',0,NULL,NULL,3,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(23,'PM1','hydraulic','Hydraulic Section Check','condition',NULL,'oil_block_valve','Oli dari Semua Block Valve','Tidak Bocor',0,NULL,NULL,3,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(24,'PM1','hydraulic','Hydraulic Section Check','condition',NULL,'oil_pressure_gauge','Oli dari Pressure Gauge','',0,NULL,NULL,3,4,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(25,'PM1','hydraulic','Hydraulic Section Check','condition',NULL,'oil_pipe_hose','Oli dari Pipa/ Selang Hidrolik','Tidak Bocor/Tidak Retak',0,NULL,NULL,3,5,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(26,'PM1','hydraulic','Hydraulic Section Check','condition',NULL,'clamping_pressure','Clamping Pressure','',0,NULL,NULL,3,6,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(27,'PM1','hydraulic','Hydraulic Section Check','condition',NULL,'injection_pressure','Injection Pressure','',0,NULL,NULL,3,7,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(28,'PM1','hydraulic','Hydraulic Section Check','condition',NULL,'hydraulic_oil_level','Level Oli Hidrolik','Min. Middle',0,NULL,NULL,3,8,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(29,'PM1','hydraulic','Hydraulic Section Check','condition',NULL,'hydraulic_oil_temp','Temperatur Oli Hidrolik','< 55°C',1,'50','°C',3,9,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(30,'PM1','hydraulic','Hydraulic Section Check','condition',NULL,'servo_pump_noise','Servo Pump/ Motor (Noise)','Tidak Berisik',0,NULL,NULL,3,10,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(31,'PM1','mechanical','Mechanical Section Check','condition',NULL,'nozzle_center','Nozzle Center','Tidak Blobor',0,NULL,NULL,4,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(32,'PM1','mechanical','Mechanical Section Check','condition',NULL,'nozzle_hole_radius','Dia. Lubang & Radius Nozzle','Sesuai Std',1,'Dia / Radius','',4,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(33,'PM1','mechanical','Mechanical Section Check','condition',NULL,'machine_level','Machine Level','Seimbang',0,NULL,NULL,4,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(34,'PM1','mechanical','Mechanical Section Check','condition',NULL,'toggle_part_abrasion','Abrasi Part Toggle','Tidak Aus',0,NULL,NULL,4,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(35,'PM1','mechanical','Mechanical Section Check','condition',NULL,'lm_guide_sliding_plate','Kondisi LM Guide/ Sliding Plate','',0,NULL,NULL,4,4,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(36,'PM1','mechanical','Mechanical Section Check','condition',NULL,'clamping_adjusting_motor','Clamping Adjusting Motor','Berfungsi',0,NULL,NULL,4,5,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(37,'PM1','lubrication','Lubrication Section Check','condition',NULL,'grease_level','Level Grease Lubrikasi','Min. Terisi',0,NULL,NULL,5,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(38,'PM1','lubrication','Lubrication Section Check','condition',NULL,'lubrication_oil_level','Level Oli Lubrikasi','Setengah',0,NULL,NULL,5,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(39,'PM1','lubrication','Lubrication Section Check','condition',NULL,'lubrication_flow','Aliran Lubrikasi','Berfungsi',0,NULL,NULL,5,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(40,'PM1','lubrication','Lubrication Section Check','condition',NULL,'lubrication_nipple_hose','Kondisi Nepel & Selang Lubrikasi','Berfungsi',0,NULL,NULL,5,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(41,'PM1','lubrication_manual','Pelumasan Manual','action',NULL,'lube_tie_bar','Tie-bar & Nut Tie-bar','Terlubrikasi',0,NULL,NULL,6,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(42,'PM1','lubrication_manual','Pelumasan Manual','action',NULL,'lube_sliding_injection','Sliding Plate Injection','Terlubrikasi',0,NULL,NULL,6,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(43,'PM1','lubrication_manual','Pelumasan Manual','action',NULL,'lube_sliding_clamp','Sliding Plate Clamp','Terlubrikasi',0,NULL,NULL,6,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(44,'PM1','lubrication_manual','Pelumasan Manual','action',NULL,'lube_gear_adjust_mold','Gear Adjust Mold','Terlubrikasi',0,NULL,NULL,6,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(45,'PM1','outlook','Outlook of Machine','action',NULL,'clean_cover_under_machine','Bersihkan Cover & Bawah Mesin','',0,NULL,NULL,7,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(46,'PM1','outlook','Outlook of Machine','action',NULL,'clean_electrical_panel','Bersihkan Panel Elektrik Mesin','Bersih',0,NULL,NULL,7,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(47,'PM1','additional','ADDITIONAL PM','action',NULL,'grease_gearbox_injection','Tambah Grease Gearbox Injection','Isi Ulang',0,NULL,NULL,8,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(48,'PM1','additional','ADDITIONAL PM','action',NULL,'clean_air_filter','Bersihkan Filter Udara','Tidak Berdebu',0,NULL,NULL,8,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(49,'PM1','additional','ADDITIONAL PM','action',NULL,'machine_foot_check','Cek Sepatu Mesin','Tidak Goyang',0,NULL,NULL,8,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(50,'PM1','additional','ADDITIONAL PM','action',NULL,'clean_cooling_barrel','Bersihkan Cooling Barrel','Tidak Mampet',0,NULL,NULL,8,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(51,'PM1','additional','ADDITIONAL PM','action',NULL,'autoclamp_hose_pressure','Cek Selang & Pressure Autoclamp','Tidak Bocor',0,NULL,NULL,8,4,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(52,'PM2','voltage','Tegangan','measurement','voltage','voltage_rs','R-S','',1,'380','VAC',0,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(53,'PM2','voltage','Tegangan','measurement','voltage','voltage_st','S-T','',1,'380','VAC',0,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(54,'PM2','voltage','Tegangan','measurement','voltage','voltage_rt','R-T','',1,'380','VAC',0,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(55,'PM2','voltage','Tegangan','measurement','voltage','voltage_pe_n','PE-N','',1,'0','VAC',0,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(56,'PM2','voltage','Tegangan','measurement','voltage','voltage_power_supply','Tegangan Power Supply','',1,'24','VDC',0,4,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(57,'PM2','electrical','Electrical Section Check','condition',NULL,'thermocouple','Thermocouple','Berfungsi',0,NULL,NULL,1,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(58,'PM2','electrical','Electrical Section Check','condition',NULL,'coil_band_heater','Coil/ Band Heater','Berfungsi',0,NULL,NULL,1,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(59,'PM2','electrical','Electrical Section Check','condition',NULL,'heater_wiring','Heater Wiring','Rapi',0,NULL,NULL,1,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(60,'PM2','electrical','Electrical Section Check','condition',NULL,'heater_ssr_mcb_fuse','Heater, SSR, MCB, Fuse','Berfungsi',0,NULL,NULL,1,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(61,'PM2','electrical','Electrical Section Check','condition',NULL,'magnetic_contactor','Magnetic Contactor','Berfungsi',0,NULL,NULL,1,4,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(62,'PM2','electrical','Electrical Section Check','condition',NULL,'operation_panel','Operation Panel','Berfungsi',0,NULL,NULL,1,5,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(63,'PM2','electrical','Electrical Section Check','condition',NULL,'selector_switch','Selector Switch','Berfungsi',0,NULL,NULL,1,6,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(64,'PM2','electrical','Electrical Section Check','condition',NULL,'cooling_fan_panel','Kipas Pendingin Panel','',0,NULL,NULL,1,7,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(65,'PM2','electrical','Electrical Section Check','condition',NULL,'cooling_fan_motor_servo','Kipas Pendingin Motor/Servo','',0,NULL,NULL,1,8,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(66,'PM2','electrical','Electrical Section Check','condition',NULL,'electrical_component_temp','Temperatur Komponen Elektrik','< 40°C',1,'36','°C',1,9,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(67,'PM2','safety','Safety Section Check','condition',NULL,'limit_switch_front_door','Limit Switch Pintu bag. Depan','Berfungsi',0,NULL,NULL,2,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(68,'PM2','safety','Safety Section Check','condition',NULL,'limit_switch_side_door','Limit Switch Pintu bag. Samping','Berfungsi',0,NULL,NULL,2,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(69,'PM2','safety','Safety Section Check','condition',NULL,'emergency_button','Tombol Emergency','Berfungsi',0,NULL,NULL,2,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(70,'PM2','safety','Safety Section Check','condition',NULL,'nozzle_safety_door','Pintu Safety Nozzle','',0,NULL,NULL,2,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(71,'PM2','hydraulic','Hydraulic Section Check','condition',NULL,'oil_clamp_cylinder','Oli dari Silinder Clamping','Tidak Bocor',0,NULL,NULL,3,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(72,'PM2','hydraulic','Hydraulic Section Check','condition',NULL,'oil_injection_cylinder','Oli dari Silinder Injeksi','Tidak Bocor',0,NULL,NULL,3,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(73,'PM2','hydraulic','Hydraulic Section Check','condition',NULL,'oil_ejector_cylinder','Oli dari Silinder Ejector','Tidak Bocor',0,NULL,NULL,3,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(74,'PM2','hydraulic','Hydraulic Section Check','condition',NULL,'oil_block_valve','Oli dari Semua Block Valve','Tidak Bocor',0,NULL,NULL,3,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(75,'PM2','hydraulic','Hydraulic Section Check','condition',NULL,'oil_pressure_gauge','Oli dari Pressure Gauge','',0,NULL,NULL,3,4,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(76,'PM2','hydraulic','Hydraulic Section Check','condition',NULL,'oil_pipe_hose','Oli dari Pipa/ Selang Hidrolik','Tidak Bocor/Tidak Retak',0,NULL,NULL,3,5,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(77,'PM2','hydraulic','Hydraulic Section Check','condition',NULL,'clamping_pressure','Clamping Pressure','',0,NULL,NULL,3,6,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(78,'PM2','hydraulic','Hydraulic Section Check','condition',NULL,'injection_pressure','Injection Pressure','',0,NULL,NULL,3,7,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(79,'PM2','hydraulic','Hydraulic Section Check','condition',NULL,'hydraulic_oil_level','Level Oli Hidrolik','Min. Middle',0,NULL,NULL,3,8,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(80,'PM2','hydraulic','Hydraulic Section Check','condition',NULL,'hydraulic_oil_temp','Temperatur Oli Hidrolik','< 55°C',1,'50','°C',3,9,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(81,'PM2','hydraulic','Hydraulic Section Check','condition',NULL,'servo_pump_noise','Servo Pump/ Motor (Noise)','Tidak Berisik',0,NULL,NULL,3,10,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(82,'PM2','mechanical','Mechanical Section Check','condition',NULL,'nozzle_center','Nozzle Center','Tidak Blobor',0,NULL,NULL,4,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(83,'PM2','mechanical','Mechanical Section Check','condition',NULL,'nozzle_hole_radius','Dia. Lubang & Radius Nozzle','Sesuai Std',1,'Dia / Radius','',4,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(84,'PM2','mechanical','Mechanical Section Check','condition',NULL,'machine_level','Machine Level','Seimbang',0,NULL,NULL,4,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(85,'PM2','mechanical','Mechanical Section Check','condition',NULL,'toggle_part_abrasion','Abrasi Part Toggle','Tidak Aus',0,NULL,NULL,4,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(86,'PM2','mechanical','Mechanical Section Check','condition',NULL,'lm_guide_sliding_plate','Kondisi LM Guide/ Sliding Plate','',0,NULL,NULL,4,4,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(87,'PM2','mechanical','Mechanical Section Check','condition',NULL,'clamping_adjusting_motor','Clamping Adjusting Motor','Berfungsi',0,NULL,NULL,4,5,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(88,'PM2','lubrication','Lubrication Section Check','condition',NULL,'grease_level','Level Grease Lubrikasi','Min. Terisi',0,NULL,NULL,5,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(89,'PM2','lubrication','Lubrication Section Check','condition',NULL,'lubrication_oil_level','Level Oli Lubrikasi','Setengah',0,NULL,NULL,5,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(90,'PM2','lubrication','Lubrication Section Check','condition',NULL,'lubrication_flow','Aliran Lubrikasi','Berfungsi',0,NULL,NULL,5,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(91,'PM2','lubrication','Lubrication Section Check','condition',NULL,'lubrication_nipple_hose','Kondisi Nepel & Selang Lubrikasi','Berfungsi',0,NULL,NULL,5,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(92,'PM2','lubrication_manual','Pelumasan Manual','action',NULL,'lube_tie_bar','Tie-bar & Nut Tie-bar','Terlubrikasi',0,NULL,NULL,6,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(93,'PM2','lubrication_manual','Pelumasan Manual','action',NULL,'lube_sliding_injection','Sliding Plate Injection','Terlubrikasi',0,NULL,NULL,6,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(94,'PM2','lubrication_manual','Pelumasan Manual','action',NULL,'lube_sliding_clamp','Sliding Plate Clamp','Terlubrikasi',0,NULL,NULL,6,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(95,'PM2','lubrication_manual','Pelumasan Manual','action',NULL,'lube_gear_adjust_mold','Gear Adjust Mold','Terlubrikasi',0,NULL,NULL,6,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(96,'PM2','outlook','Outlook of Machine','action',NULL,'clean_cover_under_machine','Bersihkan Cover & Bawah Mesin','',0,NULL,NULL,7,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(97,'PM2','outlook','Outlook of Machine','action',NULL,'clean_electrical_panel','Bersihkan Panel Elektrik Mesin','Bersih',0,NULL,NULL,7,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(98,'PM2','additional','ADDITIONAL PM','action',NULL,'clean_oil_cooler','Bersihkan Oil Cooler','Tidak Berkarak',0,NULL,NULL,8,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(99,'PM2','additional','ADDITIONAL PM','action',NULL,'replace_oil_tissue_filter','Ganti Filter Tissue Oli','Baru',0,NULL,NULL,8,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(100,'PM2','additional','ADDITIONAL PM','action',NULL,'clean_oil_filter','Bersihkan Filter Oli','Bersih',0,NULL,NULL,8,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(101,'PM2','additional','ADDITIONAL PM','action',NULL,'oil_sample_color','Ambil Sampel, Cek Warna Oli','Jernih, Bersih',0,NULL,NULL,8,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(102,'PM2','additional','ADDITIONAL PM','action',NULL,'check_ring_barrel','Cek Check Ring & Barrel','Tidak Bocor',0,NULL,NULL,8,4,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(103,'PM3','voltage','Tegangan','measurement','voltage','voltage_rs','R-S','',1,'380','VAC',0,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(104,'PM3','voltage','Tegangan','measurement','voltage','voltage_st','S-T','',1,'380','VAC',0,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(105,'PM3','voltage','Tegangan','measurement','voltage','voltage_rt','R-T','',1,'380','VAC',0,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(106,'PM3','voltage','Tegangan','measurement','voltage','voltage_pe_n','PE-N','',1,'0','VAC',0,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(107,'PM3','voltage','Tegangan','measurement','voltage','voltage_power_supply','Tegangan Power Supply','',1,'24','VDC',0,4,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(108,'PM3','electrical','Electrical Section Check','condition',NULL,'thermocouple','Thermocouple','Berfungsi',0,NULL,NULL,1,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(109,'PM3','electrical','Electrical Section Check','condition',NULL,'coil_band_heater','Coil/ Band Heater','Berfungsi',0,NULL,NULL,1,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(110,'PM3','electrical','Electrical Section Check','condition',NULL,'heater_wiring','Heater Wiring','Rapi',0,NULL,NULL,1,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(111,'PM3','electrical','Electrical Section Check','condition',NULL,'heater_ssr_mcb_fuse','Heater, SSR, MCB, Fuse','Berfungsi',0,NULL,NULL,1,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(112,'PM3','electrical','Electrical Section Check','condition',NULL,'magnetic_contactor','Magnetic Contactor','Berfungsi',0,NULL,NULL,1,4,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(113,'PM3','electrical','Electrical Section Check','condition',NULL,'operation_panel','Operation Panel','Berfungsi',0,NULL,NULL,1,5,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(114,'PM3','electrical','Electrical Section Check','condition',NULL,'selector_switch','Selector Switch','Berfungsi',0,NULL,NULL,1,6,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(115,'PM3','electrical','Electrical Section Check','condition',NULL,'cooling_fan_panel','Kipas Pendingin Panel','',0,NULL,NULL,1,7,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(116,'PM3','electrical','Electrical Section Check','condition',NULL,'cooling_fan_motor_servo','Kipas Pendingin Motor/Servo','',0,NULL,NULL,1,8,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(117,'PM3','electrical','Electrical Section Check','condition',NULL,'electrical_component_temp','Temperatur Komponen Elektrik','< 40°C',1,'36','°C',1,9,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(118,'PM3','safety','Safety Section Check','condition',NULL,'limit_switch_front_door','Limit Switch Pintu bag. Depan','Berfungsi',0,NULL,NULL,2,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(119,'PM3','safety','Safety Section Check','condition',NULL,'limit_switch_side_door','Limit Switch Pintu bag. Samping','Berfungsi',0,NULL,NULL,2,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(120,'PM3','safety','Safety Section Check','condition',NULL,'emergency_button','Tombol Emergency','Berfungsi',0,NULL,NULL,2,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(121,'PM3','safety','Safety Section Check','condition',NULL,'nozzle_safety_door','Pintu Safety Nozzle','',0,NULL,NULL,2,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(122,'PM3','hydraulic','Hydraulic Section Check','condition',NULL,'oil_clamp_cylinder','Oli dari Silinder Clamping','Tidak Bocor',0,NULL,NULL,3,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(123,'PM3','hydraulic','Hydraulic Section Check','condition',NULL,'oil_injection_cylinder','Oli dari Silinder Injeksi','Tidak Bocor',0,NULL,NULL,3,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(124,'PM3','hydraulic','Hydraulic Section Check','condition',NULL,'oil_ejector_cylinder','Oli dari Silinder Ejector','Tidak Bocor',0,NULL,NULL,3,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(125,'PM3','hydraulic','Hydraulic Section Check','condition',NULL,'oil_block_valve','Oli dari Semua Block Valve','Tidak Bocor',0,NULL,NULL,3,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(126,'PM3','hydraulic','Hydraulic Section Check','condition',NULL,'oil_pressure_gauge','Oli dari Pressure Gauge','',0,NULL,NULL,3,4,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(127,'PM3','hydraulic','Hydraulic Section Check','condition',NULL,'oil_pipe_hose','Oli dari Pipa/ Selang Hidrolik','Tidak Bocor/Tidak Retak',0,NULL,NULL,3,5,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(128,'PM3','hydraulic','Hydraulic Section Check','condition',NULL,'clamping_pressure','Clamping Pressure','',0,NULL,NULL,3,6,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(129,'PM3','hydraulic','Hydraulic Section Check','condition',NULL,'injection_pressure','Injection Pressure','',0,NULL,NULL,3,7,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(130,'PM3','hydraulic','Hydraulic Section Check','condition',NULL,'hydraulic_oil_level','Level Oli Hidrolik','Min. Middle',0,NULL,NULL,3,8,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(131,'PM3','hydraulic','Hydraulic Section Check','condition',NULL,'hydraulic_oil_temp','Temperatur Oli Hidrolik','< 55°C',1,'50','°C',3,9,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(132,'PM3','hydraulic','Hydraulic Section Check','condition',NULL,'servo_pump_noise','Servo Pump/ Motor (Noise)','Tidak Berisik',0,NULL,NULL,3,10,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(133,'PM3','mechanical','Mechanical Section Check','condition',NULL,'nozzle_center','Nozzle Center','Tidak Blobor',0,NULL,NULL,4,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(134,'PM3','mechanical','Mechanical Section Check','condition',NULL,'nozzle_hole_radius','Dia. Lubang & Radius Nozzle','Sesuai Std',1,'Dia / Radius','',4,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(135,'PM3','mechanical','Mechanical Section Check','condition',NULL,'machine_level','Machine Level','Seimbang',0,NULL,NULL,4,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(136,'PM3','mechanical','Mechanical Section Check','condition',NULL,'toggle_part_abrasion','Abrasi Part Toggle','Tidak Aus',0,NULL,NULL,4,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(137,'PM3','mechanical','Mechanical Section Check','condition',NULL,'lm_guide_sliding_plate','Kondisi LM Guide/ Sliding Plate','',0,NULL,NULL,4,4,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(138,'PM3','mechanical','Mechanical Section Check','condition',NULL,'clamping_adjusting_motor','Clamping Adjusting Motor','Berfungsi',0,NULL,NULL,4,5,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(139,'PM3','lubrication','Lubrication Section Check','condition',NULL,'grease_level','Level Grease Lubrikasi','Min. Terisi',0,NULL,NULL,5,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(140,'PM3','lubrication','Lubrication Section Check','condition',NULL,'lubrication_oil_level','Level Oli Lubrikasi','Setengah',0,NULL,NULL,5,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(141,'PM3','lubrication','Lubrication Section Check','condition',NULL,'lubrication_flow','Aliran Lubrikasi','Berfungsi',0,NULL,NULL,5,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(142,'PM3','lubrication','Lubrication Section Check','condition',NULL,'lubrication_nipple_hose','Kondisi Nepel & Selang Lubrikasi','Berfungsi',0,NULL,NULL,5,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(143,'PM3','lubrication_manual','Pelumasan Manual','action',NULL,'lube_tie_bar','Tie-bar & Nut Tie-bar','Terlubrikasi',0,NULL,NULL,6,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(144,'PM3','lubrication_manual','Pelumasan Manual','action',NULL,'lube_sliding_injection','Sliding Plate Injection','Terlubrikasi',0,NULL,NULL,6,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(145,'PM3','lubrication_manual','Pelumasan Manual','action',NULL,'lube_sliding_clamp','Sliding Plate Clamp','Terlubrikasi',0,NULL,NULL,6,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(146,'PM3','lubrication_manual','Pelumasan Manual','action',NULL,'lube_gear_adjust_mold','Gear Adjust Mold','Terlubrikasi',0,NULL,NULL,6,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(147,'PM3','outlook','Outlook of Machine','action',NULL,'clean_cover_under_machine','Bersihkan Cover & Bawah Mesin','',0,NULL,NULL,7,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(148,'PM3','outlook','Outlook of Machine','action',NULL,'clean_electrical_panel','Bersihkan Panel Elektrik Mesin','Bersih',0,NULL,NULL,7,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(149,'PM3','additional','ADDITIONAL PM','action',NULL,'oring_screw_maju_mundur','Ganti O-ring Screw Maju/Mundur','Tidak Bocor',0,NULL,NULL,8,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(150,'PM3','additional','ADDITIONAL PM','action',NULL,'oring_inject_maju_mundur','Ganti O-ring Inject Maju-mundur','Tidak Bocor',0,NULL,NULL,8,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(151,'PM3','additional','ADDITIONAL PM','action',NULL,'oring_suckback','Ganti O-ring Suckback','Tidak Bocor',0,NULL,NULL,8,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(152,'PM3','additional','ADDITIONAL PM','action',NULL,'oring_proportional_valve','Ganti O-ring Proportional Valve','Tidak Bocor',0,NULL,NULL,8,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(153,'PM4','voltage','Tegangan','measurement','voltage','voltage_rs','R-S','',1,'380','VAC',0,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(154,'PM4','voltage','Tegangan','measurement','voltage','voltage_st','S-T','',1,'380','VAC',0,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(155,'PM4','voltage','Tegangan','measurement','voltage','voltage_rt','R-T','',1,'380','VAC',0,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(156,'PM4','voltage','Tegangan','measurement','voltage','voltage_pe_n','PE-N','',1,'0','VAC',0,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(157,'PM4','voltage','Tegangan','measurement','voltage','voltage_power_supply','Tegangan Power Supply','',1,'24','VDC',0,4,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(158,'PM4','electrical','Electrical Section Check','condition',NULL,'thermocouple','Thermocouple','Berfungsi',0,NULL,NULL,1,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(159,'PM4','electrical','Electrical Section Check','condition',NULL,'coil_band_heater','Coil/ Band Heater','Berfungsi',0,NULL,NULL,1,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(160,'PM4','electrical','Electrical Section Check','condition',NULL,'heater_wiring','Heater Wiring','Rapi',0,NULL,NULL,1,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(161,'PM4','electrical','Electrical Section Check','condition',NULL,'heater_ssr_mcb_fuse','Heater, SSR, MCB, Fuse','Berfungsi',0,NULL,NULL,1,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(162,'PM4','electrical','Electrical Section Check','condition',NULL,'magnetic_contactor','Magnetic Contactor','Berfungsi',0,NULL,NULL,1,4,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(163,'PM4','electrical','Electrical Section Check','condition',NULL,'operation_panel','Operation Panel','Berfungsi',0,NULL,NULL,1,5,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(164,'PM4','electrical','Electrical Section Check','condition',NULL,'selector_switch','Selector Switch','Berfungsi',0,NULL,NULL,1,6,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(165,'PM4','electrical','Electrical Section Check','condition',NULL,'cooling_fan_panel','Kipas Pendingin Panel','',0,NULL,NULL,1,7,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(166,'PM4','electrical','Electrical Section Check','condition',NULL,'cooling_fan_motor_servo','Kipas Pendingin Motor/Servo','',0,NULL,NULL,1,8,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(167,'PM4','electrical','Electrical Section Check','condition',NULL,'electrical_component_temp','Temperatur Komponen Elektrik','< 40°C',1,'36','°C',1,9,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(168,'PM4','safety','Safety Section Check','condition',NULL,'limit_switch_front_door','Limit Switch Pintu bag. Depan','Berfungsi',0,NULL,NULL,2,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(169,'PM4','safety','Safety Section Check','condition',NULL,'limit_switch_side_door','Limit Switch Pintu bag. Samping','Berfungsi',0,NULL,NULL,2,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(170,'PM4','safety','Safety Section Check','condition',NULL,'emergency_button','Tombol Emergency','Berfungsi',0,NULL,NULL,2,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(171,'PM4','safety','Safety Section Check','condition',NULL,'nozzle_safety_door','Pintu Safety Nozzle','',0,NULL,NULL,2,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(172,'PM4','hydraulic','Hydraulic Section Check','condition',NULL,'oil_clamp_cylinder','Oli dari Silinder Clamping','Tidak Bocor',0,NULL,NULL,3,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(173,'PM4','hydraulic','Hydraulic Section Check','condition',NULL,'oil_injection_cylinder','Oli dari Silinder Injeksi','Tidak Bocor',0,NULL,NULL,3,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(174,'PM4','hydraulic','Hydraulic Section Check','condition',NULL,'oil_ejector_cylinder','Oli dari Silinder Ejector','Tidak Bocor',0,NULL,NULL,3,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(175,'PM4','hydraulic','Hydraulic Section Check','condition',NULL,'oil_block_valve','Oli dari Semua Block Valve','Tidak Bocor',0,NULL,NULL,3,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(176,'PM4','hydraulic','Hydraulic Section Check','condition',NULL,'oil_pressure_gauge','Oli dari Pressure Gauge','',0,NULL,NULL,3,4,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(177,'PM4','hydraulic','Hydraulic Section Check','condition',NULL,'oil_pipe_hose','Oli dari Pipa/ Selang Hidrolik','Tidak Bocor/Tidak Retak',0,NULL,NULL,3,5,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(178,'PM4','hydraulic','Hydraulic Section Check','condition',NULL,'clamping_pressure','Clamping Pressure','',0,NULL,NULL,3,6,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(179,'PM4','hydraulic','Hydraulic Section Check','condition',NULL,'injection_pressure','Injection Pressure','',0,NULL,NULL,3,7,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(180,'PM4','hydraulic','Hydraulic Section Check','condition',NULL,'hydraulic_oil_level','Level Oli Hidrolik','Min. Middle',0,NULL,NULL,3,8,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(181,'PM4','hydraulic','Hydraulic Section Check','condition',NULL,'hydraulic_oil_temp','Temperatur Oli Hidrolik','< 55°C',1,'50','°C',3,9,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(182,'PM4','hydraulic','Hydraulic Section Check','condition',NULL,'servo_pump_noise','Servo Pump/ Motor (Noise)','Tidak Berisik',0,NULL,NULL,3,10,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(183,'PM4','mechanical','Mechanical Section Check','condition',NULL,'nozzle_center','Nozzle Center','Tidak Blobor',0,NULL,NULL,4,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(184,'PM4','mechanical','Mechanical Section Check','condition',NULL,'nozzle_hole_radius','Dia. Lubang & Radius Nozzle','Sesuai Std',1,'Dia / Radius','',4,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(185,'PM4','mechanical','Mechanical Section Check','condition',NULL,'machine_level','Machine Level','Seimbang',0,NULL,NULL,4,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(186,'PM4','mechanical','Mechanical Section Check','condition',NULL,'toggle_part_abrasion','Abrasi Part Toggle','Tidak Aus',0,NULL,NULL,4,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(187,'PM4','mechanical','Mechanical Section Check','condition',NULL,'lm_guide_sliding_plate','Kondisi LM Guide/ Sliding Plate','',0,NULL,NULL,4,4,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(188,'PM4','mechanical','Mechanical Section Check','condition',NULL,'clamping_adjusting_motor','Clamping Adjusting Motor','Berfungsi',0,NULL,NULL,4,5,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(189,'PM4','lubrication','Lubrication Section Check','condition',NULL,'grease_level','Level Grease Lubrikasi','Min. Terisi',0,NULL,NULL,5,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(190,'PM4','lubrication','Lubrication Section Check','condition',NULL,'lubrication_oil_level','Level Oli Lubrikasi','Setengah',0,NULL,NULL,5,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(191,'PM4','lubrication','Lubrication Section Check','condition',NULL,'lubrication_flow','Aliran Lubrikasi','Berfungsi',0,NULL,NULL,5,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(192,'PM4','lubrication','Lubrication Section Check','condition',NULL,'lubrication_nipple_hose','Kondisi Nepel & Selang Lubrikasi','Berfungsi',0,NULL,NULL,5,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(193,'PM4','lubrication_manual','Pelumasan Manual','action',NULL,'lube_tie_bar','Tie-bar & Nut Tie-bar','Terlubrikasi',0,NULL,NULL,6,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(194,'PM4','lubrication_manual','Pelumasan Manual','action',NULL,'lube_sliding_injection','Sliding Plate Injection','Terlubrikasi',0,NULL,NULL,6,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(195,'PM4','lubrication_manual','Pelumasan Manual','action',NULL,'lube_sliding_clamp','Sliding Plate Clamp','Terlubrikasi',0,NULL,NULL,6,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(196,'PM4','lubrication_manual','Pelumasan Manual','action',NULL,'lube_gear_adjust_mold','Gear Adjust Mold','Terlubrikasi',0,NULL,NULL,6,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(197,'PM4','outlook','Outlook of Machine','action',NULL,'clean_cover_under_machine','Bersihkan Cover & Bawah Mesin','',0,NULL,NULL,7,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(198,'PM4','outlook','Outlook of Machine','action',NULL,'clean_electrical_panel','Bersihkan Panel Elektrik Mesin','Bersih',0,NULL,NULL,7,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(199,'PM4','additional','ADDITIONAL PM','action',NULL,'oring_valve_open','Ganti O-ring Valve Open','Tidak Bocor',0,NULL,NULL,8,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(200,'PM4','additional','ADDITIONAL PM','action',NULL,'oring_valve_close','Ganti O-ring Valve Close','Tidak Bocor',0,NULL,NULL,8,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(201,'PM4','additional','ADDITIONAL PM','action',NULL,'oring_valve_mold_adjust','Ganti O-ring Valve Mold Adjust','Tidak Bocor',0,NULL,NULL,8,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(202,'PM4','additional','ADDITIONAL PM','action',NULL,'oring_valve_mold_core','Ganti O-ring Valve Mold Core','Tidak Bocor',0,NULL,NULL,8,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(203,'PM4','additional','ADDITIONAL PM','action',NULL,'oring_valve_ejector','Ganti O-ring Valve Ejector','Tidak Bocor',0,NULL,NULL,8,4,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(204,'P1E','voltage','Tegangan','measurement','voltage','voltage_rs','R-S','',1,'380','VAC',0,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(205,'P1E','voltage','Tegangan','measurement','voltage','voltage_st','S-T','',1,'380','VAC',0,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(206,'P1E','voltage','Tegangan','measurement','voltage','voltage_rt','R-T','',1,'380','VAC',0,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(207,'P1E','voltage','Tegangan','measurement','voltage','voltage_pe_n','PE-N','',1,'0','VAC',0,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(208,'P1E','voltage','Tegangan','measurement','voltage','voltage_power_supply','Tegangan Power Supply','',1,'24','VDC',0,4,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(209,'P1E','electrical','Electrical Section Check','condition',NULL,'thermocouple','Thermocouple','Berfungsi',0,NULL,NULL,1,0,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(210,'P1E','electrical','Electrical Section Check','condition',NULL,'coil_band_heater','Coil/ Band Heater','Berfungsi',0,NULL,NULL,1,1,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(211,'P1E','electrical','Electrical Section Check','condition',NULL,'heater_wiring','Heater Wiring','Rapi',0,NULL,NULL,1,2,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(212,'P1E','electrical','Electrical Section Check','condition',NULL,'heater_ssr_mcb_fuse','Heater, SSR, MCB, Fuse','Berfungsi',0,NULL,NULL,1,3,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(213,'P1E','electrical','Electrical Section Check','condition',NULL,'magnetic_contactor','Magnetic Contactor','Berfungsi',0,NULL,NULL,1,4,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(214,'P1E','electrical','Electrical Section Check','condition',NULL,'operation_panel','Operation Panel','Berfungsi',0,NULL,NULL,1,5,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(215,'P1E','electrical','Electrical Section Check','condition',NULL,'selector_switch','Selector Switch','Berfungsi',0,NULL,NULL,1,6,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(216,'P1E','electrical','Electrical Section Check','condition',NULL,'cooling_fan_panel','Kipas Pendingin Panel','',0,NULL,NULL,1,7,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(217,'P1E','electrical','Electrical Section Check','condition',NULL,'cooling_fan_motor_servo','Kipas Pendingin Motor/Servo','',0,NULL,NULL,1,8,1,'2026-09-07 14:14:09','2026-09-07 14:14:09'),(218,'P1E','electrical','Electrical Section Check','condition',NULL,'electrical_component_temp','Temperatur Komponen Elektrik','< 40°C',1,'36','°C',1,9,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(219,'P1E','safety','Safety Section Check','condition',NULL,'limit_switch_front_door','Limit Switch Pintu bag. Depan','Berfungsi',0,NULL,NULL,2,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(220,'P1E','safety','Safety Section Check','condition',NULL,'limit_switch_side_door','Limit Switch Pintu bag. Samping','Berfungsi',0,NULL,NULL,2,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(221,'P1E','safety','Safety Section Check','condition',NULL,'safety_foot_mold','Safety Foot Mold (850T)','Berfungsi',0,NULL,NULL,2,2,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(222,'P1E','safety','Safety Section Check','condition',NULL,'emergency_button','Tombol Emergency','Berfungsi',0,NULL,NULL,2,3,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(223,'P1E','safety','Safety Section Check','condition',NULL,'nozzle_safety_door','Pintu Safety Nozzle','Berfungsi',0,NULL,NULL,2,4,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(224,'P1E','hydraulic','Hydraulic Section Check (850T)','condition',NULL,'autoclamp_oil','Oli Autoclamp','Min. Terisi Setengah',0,NULL,NULL,3,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(225,'P1E','hydraulic','Hydraulic Section Check (850T)','condition',NULL,'autoclamp_hose','Selang Autoclamp','Tidak bocor',0,NULL,NULL,3,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(226,'P1E','hydraulic','Hydraulic Section Check (850T)','condition',NULL,'autoclamp_pressure','Pressure Autoclamp','',0,NULL,NULL,3,2,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(227,'P1E','hydraulic','Hydraulic Section Check (850T)','condition',NULL,'hydraulic_corepull','Hydraulic Corepull (jika dipakai)','Tidak Bocor',0,NULL,NULL,3,3,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(228,'P1E','mechanical','Mechanical Section Check','condition',NULL,'nozzle_center','Nozzle Center','Tidak Blobor',0,NULL,NULL,4,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(229,'P1E','mechanical','Mechanical Section Check','condition',NULL,'nozzle_hole_radius','Dia. Lubang & Radius Nozzle','Sesuai Std',1,'Dia / Radius','',4,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(230,'P1E','mechanical','Mechanical Section Check','condition',NULL,'machine_level','Machine Level','Seimbang',0,NULL,NULL,4,2,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(231,'P1E','mechanical','Mechanical Section Check','condition',NULL,'toggle_part_abrasion','Abrasi Part Toggle','Tidak Aus',0,NULL,NULL,4,3,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(232,'P1E','mechanical','Mechanical Section Check','condition',NULL,'lm_guide_sliding_plate','Kondisi LM Guide/ Sliding Plate','',0,NULL,NULL,4,4,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(233,'P1E','mechanical','Mechanical Section Check','condition',NULL,'clamping_adjusting_motor','Clamping Adjusting Motor','Berfungsi',0,NULL,NULL,4,5,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(234,'P1E','mechanical','Mechanical Section Check','condition',NULL,'check_ring_barrel','Cek Check Ring & Barrel','Tidak Bocor',0,NULL,NULL,4,6,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(235,'P1E','mechanical_action','Mechanical — Perawatan','action',NULL,'grease_gearbox_injection','Tambah Grease Gearbox Injection','Isi Ulang',0,NULL,NULL,5,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(236,'P1E','mechanical_action','Mechanical — Perawatan','action',NULL,'clean_cooling_barrel','Bersihkan Cooling Barrel','Tidak Mampet',0,NULL,NULL,5,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(237,'P1E','lubrication','Lubrication Section','condition',NULL,'lube_grease_replace','Lube Grease, Ganti Jika Habis','Lube Grease',0,NULL,NULL,6,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(238,'P1E','lubrication','Lubrication Section','condition',NULL,'lubrication_flow_nipple','Aliran Lubrikasi & Kondisi Nepel/Selang','Berfungsi',0,NULL,NULL,6,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(239,'P1E','lubrication_manual','Pelumasan Manual','action',NULL,'lube_tie_bar','Tie-bar & Nut Tie-bar','Terlubrikasi',0,NULL,NULL,7,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(240,'P1E','lubrication_manual','Pelumasan Manual','action',NULL,'lube_sliding_injection','Sliding Plate Injection','Terlubrikasi',0,NULL,NULL,7,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(241,'P1E','lubrication_manual','Pelumasan Manual','action',NULL,'lube_sliding_clamp','Sliding Plate Clamp','Terlubrikasi',0,NULL,NULL,7,2,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(242,'P1E','lubrication_manual','Pelumasan Manual','action',NULL,'lube_gear_adjust_mold','Gear Adjust Mold','Terlubrikasi',0,NULL,NULL,7,3,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(243,'P1E','electrical_panel','Panel Elektrik','action',NULL,'clean_electrical_panel','Bersihkan Panel Elektrik Mesin','Tidak Berdebu',0,NULL,NULL,8,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(244,'P1E','electrical_panel','Panel Elektrik','action',NULL,'clean_panel_fan','Bersihkan Kipas Panel','Tidak Berdebu',0,NULL,NULL,8,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(245,'P1E','electrical_panel','Panel Elektrik','action',NULL,'clean_air_filter','Bersihkan Filter Udara','Tidak Berdebu',0,NULL,NULL,8,2,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(246,'P1E','electrical_panel','Panel Elektrik','action',NULL,'clean_servo_fan','Bersihkan Kipas Servo','Tidak Berdebu',0,NULL,NULL,8,3,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(247,'P1E','electrical_panel','Panel Elektrik','action',NULL,'component_thermal_temp','Cek Suhu Komponen (Thermal)','< 50°C',1,'45','°C',8,4,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(248,'P1E','outlook','Outlook Of Machine','action',NULL,'clean_cover_under_machine','Bersihkan Cover & Bawah Mesin','Bersih',0,NULL,NULL,9,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(249,'P1E','outlook','Outlook Of Machine','action',NULL,'machine_foot_check','Cek Sepatu Mesin','Tidak Goyang',0,NULL,NULL,9,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(250,'P1E','outlook','Outlook Of Machine','action',NULL,'replace_water_hoses','Ganti Selang-selang Air','Tidak kaku',0,NULL,NULL,9,2,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(251,'P1E','outlook','Outlook Of Machine','action',NULL,'replace_air_hoses','Ganti Selang-selang angin','Tidak kaku',0,NULL,NULL,9,3,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(252,'MTC','mechanical_fluid','Mekanikal & Saluran Fluida','condition',NULL,'hose_fitting_leak','Kebocoran Air/Oli pada Selang & Fitting','Tidak ada bocor/rembes (Visual)',0,NULL,NULL,0,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(253,'MTC','mechanical_fluid','Mekanikal & Saluran Fluida','condition',NULL,'water_inlet_outlet_pressure','Tekanan Air Inlet / Outlet (Pressure Gauge)','Stabil sesuai standar spesifikasi (Visual)',0,NULL,NULL,0,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(254,'MTC','mechanical_fluid','Mekanikal & Saluran Fluida','condition',NULL,'circulation_pump','Kondisi Pompa Sirkulasi (Suara & Getaran)','Suara halus, tidak ada getaran abnormal (Auditori)',0,NULL,NULL,0,2,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(255,'MTC','mechanical_fluid','Mekanikal & Saluran Fluida','condition',NULL,'water_inflow_strainer','Kondisi Strainer / Filter Air Inflow','Bersih dari kerak & kotoran (Pembersihan)',0,NULL,NULL,0,3,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(256,'MTC','electrical_control','Kelistrikan & Kontrol','condition',NULL,'pid_controller_display','Display Suhu Controller (PID)','Indikator temperatur menyala & akurat',0,NULL,NULL,1,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(257,'MTC','electrical_control','Kelistrikan & Kontrol','condition',NULL,'electrical_terminal_tightness','Pengencangan Terminal Listrik & Kabel','Tidak kendor / tidak ada tanda gosong (Torque)',0,NULL,NULL,1,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(258,'MTC','protection_safety','Sistem Proteksi & Safety','condition',NULL,'overheat_dry_burn_alarm','Fungsi Alarm & Sensor Overheat / Dry Burn','Alarm berbunyi & memotong arus saat overheat',0,NULL,NULL,2,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(259,'HOPPER','air_blower','Sistem Udara & Blower','condition',NULL,'air_inflow_filter','Kebersihan Filter Udara Masuk (Air Inflow Filter)','Bebas dari debu & partikel (Semprot Angin)',0,NULL,NULL,0,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(260,'HOPPER','air_blower','Sistem Udara & Blower','condition',NULL,'blower_motor_sound_vibration','Suara & Getaran Blower Motor','Berputar halus, tidak bisik/getar (Auditori)',0,NULL,NULL,0,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(261,'HOPPER','heating','Sistem Pemanas','condition',NULL,'heater_pipe','Kondisi & Fungsi Pemanas (Heater Pipe)','Suhu tercapai sesuai setpoint (Sensor Test)',0,NULL,NULL,1,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(262,'HOPPER','tank_mechanical','Fisik Tanki & Mekanikal','condition',NULL,'hopper_tank_magnet_trap','Kebersihan Dalam Tangki Hopper & Magnet Trap','Bersih total dari debu & serbuk logam',0,NULL,NULL,2,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(263,'HOPPER','tank_mechanical','Fisik Tanki & Mekanikal','condition',NULL,'hopper_mounting_bolts','Kondisi Baut Dudukan Hopper','Baut terpasang lengkap & kencang, tidak goyang',0,NULL,NULL,2,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(264,'DEHUM','air_dew_point','Sistem Udara & Dew Point','condition',NULL,'dry_air_dew_point','Nilai Dew Point Udara Kering (Dew Point Meter)','Mencapai standar -40°C s/d -50°C',1,'-45','°C',0,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(265,'DEHUM','air_dew_point','Sistem Udara & Dew Point','condition',NULL,'process_return_filter','Kebersihan Process Air Filter & Return Filter','Bebas sumbatan debu plastik (Semprot Angin)',0,NULL,NULL,0,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(266,'DEHUM','desiccant_rotor','Desiccant Rotor','condition',NULL,'honeycomb_rotor_drive','Putaran Honeycomb Rotor & V-Belt / Chain Drive','Rotor berputar lancar, belt tidak kendur',0,NULL,NULL,1,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(267,'DEHUM','regen_heater_electrical','Sistem Pemanas Regenerasi & Kelistrikan','condition',NULL,'heater_ampere','Pemeriksaan Arus Pemanas (Heater Ampere)','Arus fasa R-S-T seimbang sesuai spesifikasi',0,NULL,NULL,2,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(268,'ROBOT','mechanical_pneumatic','Mekanikal & Pneumatik','condition',NULL,'main_air_pressure','Tekanan Angin Utama (Air Pressure Gauge)','Stabil pada 0.49 - 0.59 MPa (5-6 bar)',1,'0.54','MPa',0,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(269,'ROBOT','mechanical_pneumatic','Mekanikal & Pneumatik','condition',NULL,'vacuum_pad_pneumatic_hose','Kondisi Pad Vakum & Selang Pneumatik','Vakum pad tidak robek, selang tidak bocor',0,NULL,NULL,0,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(270,'ROBOT','mechanical_pneumatic','Mekanikal & Pneumatik','condition',NULL,'guide_rail_ball_screw_lube','Pelumasan Guide Rail & Ball Screw (X, Y, Z Axis)','Terlapisi kris/grease khusus secara merata',0,NULL,NULL,0,2,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(271,'ROBOT','mechanical_pneumatic','Mekanikal & Pneumatik','condition',NULL,'eoat_chuck_plate_bolts','Kekencangan Baut EOAT (Chuck Plate)','Baut terpasang kencang, tidak bergetar',0,NULL,NULL,0,3,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(272,'ROBOT','mechanical_pneumatic','Mekanikal & Pneumatik','condition',NULL,'timing_belt_xyz','Timing Belt (X/Y/Z Axis)','Ketegangan belt sesuai standar, tidak ada retak atau benang aus',0,NULL,NULL,0,4,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(273,'ROBOT','mechanical_pneumatic','Mekanikal & Pneumatik','condition',NULL,'air_filter_water_trap','Air Filter & Water Trap','Tabung filter udara bersih, buang endapan air/embun',0,NULL,NULL,0,5,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(274,'ROBOT','mechanical_pneumatic','Mekanikal & Pneumatik','condition',NULL,'structural_frame_bolts','Baut Structural / Frame','Baut mounting frame utama dan arm kencang (sesuai torsi)',0,NULL,NULL,0,6,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(275,'ROBOT','mechanical_pneumatic','Mekanikal & Pneumatik','condition',NULL,'cylinder_arm','Cylinder Arm','Pergerakan stabil dan tidak ada rembesan oli',0,NULL,NULL,0,7,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(276,'ROBOT','mechanical_pneumatic','Mekanikal & Pneumatik','condition',NULL,'robot_arm_cable_trail_bolts','Baut Trail Kabel Arm Robot','Baut lengkap, tidak goyang/longgar',0,NULL,NULL,0,8,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(277,'ROBOT','mechanical_pneumatic','Mekanikal & Pneumatik','condition',NULL,'lubrication_tank','Tanki Lubrikasi','Cek level oli, isi jika kurang (minimal terisi 1/3)',0,NULL,NULL,0,9,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(278,'ROBOT','electrical_safety','Elektrik & Safety','condition',NULL,'emergency_stop_mold_interlock','Fungsi Emergency Stop & Safety Interlock Mold','Mesin & robot langsung berhenti saat E-Stop ditekan',0,NULL,NULL,1,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(279,'ROBOT','electrical_safety','Elektrik & Safety','condition',NULL,'controller_cable_carrier','Kondisi Kabel Controller (E-Touch) & Cable Carrier','Kabel tidak terjepit / terkelupas di dalam carrier',0,NULL,NULL,1,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(280,'ROBOT','electrical_safety','Elektrik & Safety','condition',NULL,'safety_door_interlock','Fungsi Safety Door Interlock','Robot otomatis berhenti saat pintu pagar keselamatan dibuka',0,NULL,NULL,1,2,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(281,'ROBOT','electrical_safety','Elektrik & Safety','condition',NULL,'indicator_lights','Lampu Indikator','Lampu Signal Tower (Red/Yellow/Green) menyala sesuai status',0,NULL,NULL,1,3,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(282,'ROBOT','electrical_safety','Elektrik & Safety','condition',NULL,'panel_cooling_fan','Kipas Pendingin Panel','Kipas pendingin (fan) berputar lancar, filter udara panel bersih',0,NULL,NULL,1,4,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(283,'ROBOT','electrical_safety','Elektrik & Safety','condition',NULL,'fan_cover','Cover Kipas','Cover & Filter',0,NULL,NULL,1,5,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(284,'ROBOT','electrical_safety','Elektrik & Safety','condition',NULL,'power_on_push_button','Push Button Power On','Tombol berfungsi untuk menyalakan robot',0,NULL,NULL,1,6,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(285,'ROBOT','lubrication_base_cleanliness','Pelumasan & Kebersihan Base','condition',NULL,'robot_area_cleanliness','Kebersihan Area Robot','Bebas dari ceceran oli, grease berlebih, debu',0,NULL,NULL,2,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(286,'ROBOT','lubrication_base_cleanliness','Pelumasan & Kebersihan Base','condition',NULL,'linear_guide_rail_xyz','Linear Guide Rail (X/Y/Z)','Lapisan grease tipis merata, tidak ada akumulasi gram/kotoran',0,NULL,NULL,2,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(287,'ROBOT','lubrication_base_cleanliness','Pelumasan & Kebersihan Base','condition',NULL,'ball_screw_rack_gear','Ball Screw / Rack Gear','Pelumasan memadai, pergerakan mulus tanpa suara kasar',0,NULL,NULL,2,2,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(288,'HOT','hot_runner_controller','Hot Runner Controller','condition',NULL,'zone_nozzle_temp_accuracy','Akurasi Suhu Tiap Zone / Nozzle (Yudo TBM/D-Type)','Suhu aktual sesuai setpoint, tidak over/under heat',0,NULL,NULL,0,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(289,'HOT','hot_runner_controller','Hot Runner Controller','condition',NULL,'soft_start_dehumidify','Sistem Soft-Start / Dehumidify Feature','Fungsi soft-start aktif saat awal pemanasan',0,NULL,NULL,0,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(290,'HOT','valve_gate_sequencer','Valve Gate Sequencer (SVG)','condition',NULL,'valve_gate_pressure','Tekanan Udara / Hidrolik Valve Gate (Air Pressure Regulator)','Stabil sesuai standar spesifikasi (4 - 6 Bar)',1,'5','Bar',1,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(291,'HOT','valve_gate_sequencer','Valve Gate Sequencer (SVG)','condition',NULL,'valve_pin_timing','Timing & Urutan Buka-Tutup Valve Pin (Timer/Position)','Gerakan pin sesuai urutan (sekuensial), tidak macet',0,NULL,NULL,1,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(292,'HOT','valve_gate_sequencer','Valve Gate Sequencer (SVG)','condition',NULL,'solenoid_pneumatic_hose','Kondisi Solenoid Valve & Selang Pneumatik Sequencer','Tidak ada bocor angin/oli, respon solenoid cepat',0,NULL,NULL,1,2,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(293,'HOT','cable_socket_pin','Kabel & Socket Pin','condition',NULL,'power_thermocouple_cables','Kondisi Kabel Power & Thermocouple Mold (Heavy Duty Connector)','Pin connector tidak bengkok, tidak ada gosong/longgar',0,NULL,NULL,2,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(294,'HOT','cable_socket_pin','Kabel & Socket Pin','condition',NULL,'heater_ohm_short_to_ground','Resistansi Pemanas (Heater Ohm) & Short-to-Ground','Nilai Ohm stabil, tidak ada kebocoran arus ke bodi mold',0,NULL,NULL,2,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(295,'JSWEL','inject_unit','C. Inject Unit','condition',NULL,'servo_motor_inject','Servo Motor Inject','Cek temp max 80°C & 145 Ampere motor',0,NULL,NULL,0,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(296,'JSWEL','inject_unit','C. Inject Unit','condition',NULL,'servo_motor_plasticization','Servo Motor Plasticization','Cek temp max 80°C & 145 Ampere motor',0,NULL,NULL,0,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(297,'JSWEL','inject_unit','C. Inject Unit','condition',NULL,'cooling_fan_servo_screw','Cooling Fan Servo Motor Screw','Berfungsi & bersih',0,NULL,NULL,0,2,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(298,'JSWEL','inject_unit','C. Inject Unit','condition',NULL,'cooling_fan_servo_inject','Cooling Fan Servo Motor Inject','Berfungsi & bersih',0,NULL,NULL,0,3,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(299,'JSWEL','inject_unit','C. Inject Unit','condition',NULL,'ball_screw','Ball Screw','Bersih',0,NULL,NULL,0,4,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(300,'JSWEL','inject_unit','C. Inject Unit','condition',NULL,'gear_box','Gear Box','Level oil',0,NULL,NULL,0,5,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(301,'JSWEL','inject_unit','C. Inject Unit','condition',NULL,'automatic_grease_lubrication','Automatic Grease Lubrication','Berfungsi minimal 5 Bar',0,NULL,NULL,0,6,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(302,'JSWEL','inject_unit','C. Inject Unit','condition',NULL,'hopper_cooler','Hopper Cooler','Temperatur sesuai setting (50°C s/d 80°C)',0,NULL,NULL,0,7,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(303,'JSWEL','inject_unit','C. Inject Unit','condition',NULL,'heater_nozzle','Heater Nozzle','Temperatur actual sesuai setting',0,NULL,NULL,0,8,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(304,'JSWEL','inject_unit','C. Inject Unit','condition',NULL,'hydraulic_cylinder','Hydraulic Cylinder','Bersih & tidak bocor',0,NULL,NULL,0,9,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(305,'JSWEL','inject_unit','C. Inject Unit','condition',NULL,'grease_cartridge','Grease Cartridge','Level grease 1/4 ganti baru',0,NULL,NULL,0,10,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(306,'JSWEL','inject_unit','C. Inject Unit','condition',NULL,'nozzle_center_radius','Nozzle Center & Radius Nozzle','Nozzle center, tidak gompal',1,'OK / catatan','',0,11,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(307,'JSWEL','inject_unit','C. Inject Unit','condition',NULL,'nozzle_hole_radius','Dia. Lubang & Radius Nozzle','Lubang nozzle 1 mm < lubang mold',1,'Dia / Radius','',0,12,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(308,'JSWEL','pneumatic','D. System Pneumatic Low Pressure','condition',NULL,'pressure_regulator','Pressure Regulator','Pressure 5 Bar',0,NULL,NULL,1,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(309,'JSWEL','pneumatic','D. System Pneumatic Low Pressure','condition',NULL,'filter_regulator','Filter Regulator','Bersih & tidak ada air di dalam filter',0,NULL,NULL,1,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(310,'JSWEL','pneumatic','D. System Pneumatic Low Pressure','condition',NULL,'valve_pneumatic','Valve Pneumatic','Tidak bocor',0,NULL,NULL,1,2,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(311,'JSWEL','safety','E. Safety Section','condition',NULL,'limit_switch_front_door','Limit Switch Pintu Depan (Operator)','Bersih & berfungsi',1,'OK','',2,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(312,'JSWEL','safety','E. Safety Section','condition',NULL,'limit_switch_rear_door','Limit Switch Pintu Belakang','Bersih & berfungsi',1,'OK','',2,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(313,'JSWEL','safety','E. Safety Section','condition',NULL,'nozzle_safety_door','Pintu Safety Nozzle','Bersih & berfungsi',1,'OK','',2,2,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(314,'JSWEL','safety','E. Safety Section','condition',NULL,'emergency_button','Tombol Emergency','Bersih & berfungsi',1,'OK','',2,3,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(315,'JSWEL','panel','F. Panel Mesin','condition',NULL,'main_voltage_3_phase','Tegangan Utama 3 Phase','Tegangan output 380V ±10%',1,'R-S / S-T / R-T','VAC',3,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(316,'JSWEL','panel','F. Panel Mesin','condition',NULL,'exhaust_fan','Exhaust Fan','Bersih & berfungsi',0,NULL,NULL,3,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(317,'JSWEL','panel','F. Panel Mesin','condition',NULL,'filter_electrical_cabin','Filter Electrical Cabin','Bersih',0,NULL,NULL,3,2,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(318,'JSWEL','panel','F. Panel Mesin','condition',NULL,'electro_magnetic_contactor','Electro Magnetic Contactor','Temperature / 150 Ampere',0,NULL,NULL,3,3,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(319,'JSWEL','panel','F. Panel Mesin','condition',NULL,'power_supply','Power Supply','Tegangan output 24 Volt DC',0,NULL,NULL,3,4,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(320,'JSWEL','panel','F. Panel Mesin','condition',NULL,'breaker_cb0','Breaker CB 0','Temperature / 140 Ampere / tegangan',0,NULL,NULL,3,5,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(321,'JSWEL','panel','F. Panel Mesin','condition',NULL,'running_hours_meter','Running Hours Meter','Berfungsi',0,NULL,NULL,3,6,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(322,'JSWEL','panel','F. Panel Mesin','condition',NULL,'cover_bolts','Baut Cover','Lengkap dan kencang',0,NULL,NULL,3,7,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(323,'CONVEYOR','belt','Belt (Sabuk)','condition',NULL,'belt_physical','Kondisi Fisik Belt','Kondisi Belt tidak retak/sobek',0,NULL,NULL,0,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(324,'CONVEYOR','belt','Belt (Sabuk)','condition',NULL,'belt_tension','Cek Ketegangan & Kelurusan Belt','Belt tidak longgar dan lurus',0,NULL,NULL,0,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(325,'CONVEYOR','drive','Unit Penggerak','condition',NULL,'drive_motor','Cek kondisi motor & rantai sprocket','Suhu & suara motor halus',0,NULL,NULL,1,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(326,'CONVEYOR','drive','Unit Penggerak','condition',NULL,'drive_roller','Cek Kondisi Roller & Bearing','Putaran drive & tail pulley halus, bersih',0,NULL,NULL,1,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(327,'CONVEYOR','drive','Unit Penggerak','condition',NULL,'drive_greasing','Pelumasan Block Bearing','Lakukan pelumasan pada block bearing',0,NULL,NULL,1,2,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(328,'CONVEYOR','electrical','Kelistrikan','condition',NULL,'electrical_cabling','Cek Kondisi Perkabelan Conveyor','Kabel tidak semrawut, tidak terkena air, rapi',0,NULL,NULL,2,0,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(329,'CONVEYOR','electrical','Kelistrikan','condition',NULL,'electrical_inverter','Cek fungsi Inverter/ VFD','Respon pengatur kecepatan sesuai',0,NULL,NULL,2,1,1,'2026-09-07 14:14:10','2026-09-07 14:14:10'),(330,'CONVEYOR','electrical','Kelistrikan','condition',NULL,'electrical_safety','Fungsi tombol E-Stop & Safety lainnya','Cek fungsi',0,NULL,NULL,2,2,1,'2026-09-07 14:14:10','2026-09-07 14:14:10');
/*!40000 ALTER TABLE `preventive_checklist_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `preventive_conveyor_reports`
--

DROP TABLE IF EXISTS `preventive_conveyor_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `preventive_conveyor_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `pic_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conveyor_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `work_date` date NOT NULL,
  `work_shift` enum('shift_1','shift_2','shift_3') COLLATE utf8mb4_unicode_ci NOT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `evidence_paths` json DEFAULT NULL,
  `result` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ok',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `preventive_conveyor_reports_user_id_foreign` (`user_id`),
  KEY `preventive_conveyor_reports_conveyor_name_work_date_index` (`conveyor_name`,`work_date`),
  CONSTRAINT `preventive_conveyor_reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `preventive_conveyor_reports_chk_1` CHECK (json_valid(`items`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preventive_conveyor_reports`
--

LOCK TABLES `preventive_conveyor_reports` WRITE;
/*!40000 ALTER TABLE `preventive_conveyor_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `preventive_conveyor_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `preventive_reports`
--

DROP TABLE IF EXISTS `preventive_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `preventive_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `pic_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `machine_name_id` bigint unsigned DEFAULT NULL,
  `machine_number` tinyint unsigned NOT NULL,
  `equipment_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `equipment_id` bigint unsigned DEFAULT NULL,
  `equipment_type` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `robot_id` bigint unsigned DEFAULT NULL,
  `robot_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `robot_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `work_date` date NOT NULL,
  `work_shift` enum('shift_1','shift_2','shift_3') COLLATE utf8mb4_unicode_ci NOT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `readings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `evidence_paths` json DEFAULT NULL,
  `result` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ok',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'close_by_technician',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `preventive_reports_user_id_foreign` (`user_id`),
  KEY `preventive_reports_machine_name_id_foreign` (`machine_name_id`),
  KEY `preventive_reports_machine_number_work_date_index` (`machine_number`,`work_date`),
  KEY `preventive_reports_machine_number_category_index` (`machine_number`,`category`),
  KEY `preventive_reports_robot_id_foreign` (`robot_id`),
  KEY `preventive_reports_equipment_id_foreign` (`equipment_id`),
  KEY `preventive_reports_status_index` (`status`),
  CONSTRAINT `preventive_reports_equipment_id_foreign` FOREIGN KEY (`equipment_id`) REFERENCES `equipments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `preventive_reports_machine_name_id_foreign` FOREIGN KEY (`machine_name_id`) REFERENCES `machine_names` (`id`) ON DELETE SET NULL,
  CONSTRAINT `preventive_reports_robot_id_foreign` FOREIGN KEY (`robot_id`) REFERENCES `robots` (`id`) ON DELETE SET NULL,
  CONSTRAINT `preventive_reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `preventive_reports_chk_1` CHECK (json_valid(`items`)),
  CONSTRAINT `preventive_reports_chk_2` CHECK (json_valid(`readings`))
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preventive_reports`
--

LOCK TABLES `preventive_reports` WRITE;
/*!40000 ALTER TABLE `preventive_reports` DISABLE KEYS */;
INSERT INTO `preventive_reports` VALUES (7,13,'Budi',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'PM1','2026-09-09','shift_2','{\"thermocouple\":\"ok\",\"coil_band_heater\":\"ok\",\"heater_wiring\":\"ok\",\"heater_ssr_mcb_fuse\":\"ok\",\"magnetic_contactor\":\"ok\",\"operation_panel\":\"ok\",\"selector_switch\":\"ok\",\"cooling_fan_panel\":\"ok\",\"cooling_fan_motor_servo\":\"ok\",\"electrical_component_temp\":\"ok\",\"limit_switch_front_door\":\"ok\",\"limit_switch_side_door\":\"ok\",\"emergency_button\":\"ok\",\"nozzle_safety_door\":\"ok\",\"oil_clamp_cylinder\":\"ok\",\"oil_injection_cylinder\":\"ok\",\"oil_ejector_cylinder\":\"ok\",\"oil_block_valve\":\"ok\",\"oil_pressure_gauge\":\"ok\",\"oil_pipe_hose\":\"ok\",\"clamping_pressure\":\"ok\",\"injection_pressure\":\"ok\",\"hydraulic_oil_level\":\"ok\",\"hydraulic_oil_temp\":\"ok\",\"servo_pump_noise\":\"ok\",\"nozzle_center\":\"ok\",\"nozzle_hole_radius\":\"ok\",\"machine_level\":\"ok\",\"toggle_part_abrasion\":\"ok\",\"lm_guide_sliding_plate\":\"ok\",\"clamping_adjusting_motor\":\"ok\",\"grease_level\":\"ok\",\"lubrication_oil_level\":\"ok\",\"lubrication_flow\":\"ok\",\"lubrication_nipple_hose\":\"ok\",\"lube_tie_bar\":\"ok\",\"lube_sliding_injection\":\"ok\",\"lube_sliding_clamp\":\"ok\",\"lube_gear_adjust_mold\":\"ok\",\"clean_cover_under_machine\":\"ok\",\"clean_electrical_panel\":\"ok\",\"grease_gearbox_injection\":\"ok\",\"clean_air_filter\":\"ok\",\"machine_foot_check\":\"ok\",\"clean_cooling_barrel\":\"ok\",\"autoclamp_hose_pressure\":\"ok\"}','{\"voltage_rs\":\"232\",\"voltage_st\":\"232\",\"voltage_rt\":\"232\",\"voltage_pe_n\":\"232\",\"voltage_power_supply\":\"232\",\"electrical_component_temp\":\"34\",\"hydraulic_oil_temp\":\"50\",\"nozzle_hole_radius\":\"32\"}',NULL,NULL,'ok','close','2026-09-09 12:53:42','2026-09-09 12:53:42'),(8,13,'Udin',9,1,NULL,NULL,NULL,NULL,NULL,NULL,'PM2','2026-09-09','shift_2','{\"thermocouple\":\"ok\",\"coil_band_heater\":\"ok\",\"heater_wiring\":\"ok\",\"heater_ssr_mcb_fuse\":\"ok\",\"magnetic_contactor\":\"ok\",\"operation_panel\":\"ok\",\"selector_switch\":\"ok\",\"cooling_fan_panel\":\"ok\",\"cooling_fan_motor_servo\":\"ok\",\"electrical_component_temp\":\"ok\",\"limit_switch_front_door\":\"ok\",\"limit_switch_side_door\":\"ok\",\"emergency_button\":\"ok\",\"nozzle_safety_door\":\"ok\",\"oil_clamp_cylinder\":\"ok\",\"oil_injection_cylinder\":\"ok\",\"oil_ejector_cylinder\":\"ok\",\"oil_block_valve\":\"ok\",\"oil_pressure_gauge\":\"ok\",\"oil_pipe_hose\":\"ok\",\"clamping_pressure\":\"ok\",\"injection_pressure\":\"ok\",\"hydraulic_oil_level\":\"ok\",\"hydraulic_oil_temp\":\"ok\",\"servo_pump_noise\":\"ok\",\"nozzle_center\":\"ok\",\"nozzle_hole_radius\":\"ok\",\"machine_level\":\"ok\",\"toggle_part_abrasion\":\"ok\",\"lm_guide_sliding_plate\":\"ok\",\"clamping_adjusting_motor\":\"ok\",\"grease_level\":\"ok\",\"lubrication_oil_level\":\"ok\",\"lubrication_flow\":\"ok\",\"lubrication_nipple_hose\":\"ok\",\"lube_tie_bar\":\"ok\",\"lube_sliding_injection\":\"ok\",\"lube_sliding_clamp\":\"ok\",\"lube_gear_adjust_mold\":\"ok\",\"clean_cover_under_machine\":\"ok\",\"clean_electrical_panel\":\"ok\",\"clean_oil_cooler\":\"ok\",\"replace_oil_tissue_filter\":\"ok\",\"clean_oil_filter\":\"ok\",\"oil_sample_color\":\"ok\",\"check_ring_barrel\":\"ok\"}','{\"voltage_rs\":\"232\",\"voltage_st\":\"424\",\"voltage_rt\":\"323\",\"voltage_pe_n\":\"23\",\"voltage_power_supply\":\"23\",\"electrical_component_temp\":\"35\",\"hydraulic_oil_temp\":\"50\",\"nozzle_hole_radius\":\"2\"}',NULL,NULL,'ok','close','2026-09-09 13:08:30','2026-09-09 13:08:30'),(9,13,'Budi',13,5,'01',3,'Temperature 1',NULL,NULL,NULL,'MTC','2026-09-10','shift_2','{\"hose_fitting_leak\":\"ok\",\"water_inlet_outlet_pressure\":\"ok\",\"circulation_pump\":\"ok\",\"water_inflow_strainer\":\"ok\",\"pid_controller_display\":\"ok\",\"electrical_terminal_tightness\":\"ok\",\"overheat_dry_burn_alarm\":\"ok\"}',NULL,NULL,NULL,'ok','close','2026-09-10 10:56:44','2026-09-10 10:56:44'),(10,13,'Khasanudin',9,1,'01',2,'Hopper 1',NULL,NULL,NULL,'HOPPER','2026-10-16','shift_3','{\"air_inflow_filter\":\"ok\",\"blower_motor_sound_vibration\":\"ok\",\"heater_pipe\":\"ok\",\"hopper_tank_magnet_trap\":\"ok\",\"hopper_mounting_bolts\":\"ok\"}',NULL,'ok',NULL,'ok','close','2026-10-16 17:00:01','2026-10-16 17:00:01'),(11,13,'Khasanudin',11,3,NULL,NULL,NULL,NULL,NULL,NULL,'PM1','2026-10-19','shift_1','{\"thermocouple\":\"ok\",\"coil_band_heater\":\"ok\",\"heater_wiring\":\"ok\",\"heater_ssr_mcb_fuse\":\"ok\",\"magnetic_contactor\":\"ok\",\"operation_panel\":\"ok\",\"selector_switch\":\"ok\",\"cooling_fan_panel\":\"ok\",\"cooling_fan_motor_servo\":\"ok\",\"electrical_component_temp\":\"ok\",\"limit_switch_front_door\":\"ok\",\"limit_switch_side_door\":\"ok\",\"emergency_button\":\"ok\",\"nozzle_safety_door\":\"ok\",\"oil_clamp_cylinder\":\"ok\",\"oil_injection_cylinder\":\"ok\",\"oil_ejector_cylinder\":\"ok\",\"oil_block_valve\":\"ok\",\"oil_pressure_gauge\":\"ok\",\"oil_pipe_hose\":\"ok\",\"clamping_pressure\":\"ok\",\"injection_pressure\":\"ok\",\"hydraulic_oil_level\":\"ok\",\"hydraulic_oil_temp\":\"ok\",\"servo_pump_noise\":\"ok\",\"nozzle_center\":\"ok\",\"nozzle_hole_radius\":\"ok\",\"machine_level\":\"ok\",\"toggle_part_abrasion\":\"ok\",\"lm_guide_sliding_plate\":\"ok\",\"clamping_adjusting_motor\":\"ok\",\"grease_level\":\"ok\",\"lubrication_oil_level\":\"ok\",\"lubrication_flow\":\"ok\",\"lubrication_nipple_hose\":\"ok\",\"lube_tie_bar\":\"ok\",\"lube_sliding_injection\":\"ok\",\"lube_sliding_clamp\":\"ok\",\"lube_gear_adjust_mold\":\"ok\",\"clean_cover_under_machine\":\"ok\",\"clean_electrical_panel\":\"ok\",\"grease_gearbox_injection\":\"ok\",\"clean_air_filter\":\"ok\",\"machine_foot_check\":\"ok\",\"clean_cooling_barrel\":\"ok\",\"autoclamp_hose_pressure\":\"ok\"}','{\"voltage_rs\":\"380\",\"voltage_st\":\"380\",\"voltage_rt\":\"380\",\"voltage_pe_n\":\"0\",\"voltage_power_supply\":\"24\",\"electrical_component_temp\":\"36\",\"hydraulic_oil_temp\":\"50\",\"nozzle_hole_radius\":\"Dia \\/ Radius\"}','[DEMO APPROVAL] PM1 selesai teknisi. Menunggu approve Injection.',NULL,'ok','close_by_technician','2026-10-19 10:07:48','2026-10-19 10:07:48'),(12,13,'Welly',16,8,NULL,NULL,NULL,NULL,NULL,NULL,'PM2','2026-10-19','shift_1','{\"thermocouple\":\"ok\",\"coil_band_heater\":\"ok\",\"heater_wiring\":\"ok\",\"heater_ssr_mcb_fuse\":\"ok\",\"magnetic_contactor\":\"ok\",\"operation_panel\":\"ok\",\"selector_switch\":\"ok\",\"cooling_fan_panel\":\"ok\",\"cooling_fan_motor_servo\":\"ok\",\"electrical_component_temp\":\"ok\",\"limit_switch_front_door\":\"ok\",\"limit_switch_side_door\":\"ok\",\"emergency_button\":\"ok\",\"nozzle_safety_door\":\"ok\",\"oil_clamp_cylinder\":\"ok\",\"oil_injection_cylinder\":\"ok\",\"oil_ejector_cylinder\":\"ok\",\"oil_block_valve\":\"ok\",\"oil_pressure_gauge\":\"ok\",\"oil_pipe_hose\":\"ok\",\"clamping_pressure\":\"ok\",\"injection_pressure\":\"ok\",\"hydraulic_oil_level\":\"ok\",\"hydraulic_oil_temp\":\"ok\",\"servo_pump_noise\":\"ok\",\"nozzle_center\":\"ok\",\"nozzle_hole_radius\":\"ok\",\"machine_level\":\"ok\",\"toggle_part_abrasion\":\"ok\",\"lm_guide_sliding_plate\":\"ok\",\"clamping_adjusting_motor\":\"ok\",\"grease_level\":\"ok\",\"lubrication_oil_level\":\"ok\",\"lubrication_flow\":\"ok\",\"lubrication_nipple_hose\":\"ok\",\"lube_tie_bar\":\"ok\",\"lube_sliding_injection\":\"ok\",\"lube_sliding_clamp\":\"ok\",\"lube_gear_adjust_mold\":\"ok\",\"clean_cover_under_machine\":\"ok\",\"clean_electrical_panel\":\"ok\",\"clean_oil_cooler\":\"not_ok\",\"replace_oil_tissue_filter\":\"ok\",\"clean_oil_filter\":\"ok\",\"oil_sample_color\":\"ok\",\"check_ring_barrel\":\"ok\"}','{\"voltage_rs\":\"380\",\"voltage_st\":\"380\",\"voltage_rt\":\"380\",\"voltage_pe_n\":\"0\",\"voltage_power_supply\":\"24\",\"electrical_component_temp\":\"36\",\"hydraulic_oil_temp\":\"50\",\"nozzle_hole_radius\":\"Dia \\/ Radius\"}','[DEMO APPROVAL] Oil cooler kotor. Menunggu approve Injection.',NULL,'follow_up','close_by_technician','2026-10-19 10:07:48','2026-10-19 10:07:48'),(13,13,'Khasanudin',19,11,NULL,NULL,NULL,NULL,NULL,NULL,'HOPPER','2026-10-19','shift_1','{\"air_inflow_filter\":\"ok\",\"blower_motor_sound_vibration\":\"ok\",\"heater_pipe\":\"ok\",\"hopper_tank_magnet_trap\":\"ok\",\"hopper_mounting_bolts\":\"ok\"}',NULL,'[DEMO APPROVAL] Hopper OK. Injection sudah approve, menunggu Maintenance.',NULL,'ok','approve_produksi_injection','2026-10-19 10:07:48','2026-10-19 10:07:48'),(14,13,'Erickson',24,16,'R16',NULL,NULL,16,'R16','YUSHIN SC 250 - JSW 450T','ROBOT','2026-10-19','shift_1','{\"main_air_pressure\":\"ok\",\"vacuum_pad_pneumatic_hose\":\"ok\",\"guide_rail_ball_screw_lube\":\"ok\",\"eoat_chuck_plate_bolts\":\"ok\",\"timing_belt_xyz\":\"ok\",\"air_filter_water_trap\":\"ok\",\"structural_frame_bolts\":\"ok\",\"cylinder_arm\":\"ok\",\"robot_arm_cable_trail_bolts\":\"ok\",\"lubrication_tank\":\"ok\",\"emergency_stop_mold_interlock\":\"ok\",\"controller_cable_carrier\":\"ok\",\"safety_door_interlock\":\"ok\",\"indicator_lights\":\"ok\",\"panel_cooling_fan\":\"ok\",\"fan_cover\":\"ok\",\"power_on_push_button\":\"ok\",\"robot_area_cleanliness\":\"ok\",\"linear_guide_rail_xyz\":\"ok\",\"ball_screw_rack_gear\":\"ok\"}','{\"main_air_pressure\":\"0.54\"}','[DEMO APPROVAL] Robot OK. Injection sudah approve, menunggu Maintenance.',NULL,'ok','close','2026-10-19 10:07:48','2026-10-20 16:18:40'),(15,13,'Welly',29,21,NULL,NULL,NULL,NULL,NULL,NULL,'PM1','2026-10-19','shift_1','{\"thermocouple\":\"ok\",\"coil_band_heater\":\"ok\",\"heater_wiring\":\"ok\",\"heater_ssr_mcb_fuse\":\"ok\",\"magnetic_contactor\":\"ok\",\"operation_panel\":\"ok\",\"selector_switch\":\"ok\",\"cooling_fan_panel\":\"ok\",\"cooling_fan_motor_servo\":\"ok\",\"electrical_component_temp\":\"ok\",\"limit_switch_front_door\":\"ok\",\"limit_switch_side_door\":\"ok\",\"emergency_button\":\"ok\",\"nozzle_safety_door\":\"ok\",\"oil_clamp_cylinder\":\"ok\",\"oil_injection_cylinder\":\"ok\",\"oil_ejector_cylinder\":\"ok\",\"oil_block_valve\":\"ok\",\"oil_pressure_gauge\":\"ok\",\"oil_pipe_hose\":\"ok\",\"clamping_pressure\":\"ok\",\"injection_pressure\":\"ok\",\"hydraulic_oil_level\":\"ok\",\"hydraulic_oil_temp\":\"ok\",\"servo_pump_noise\":\"ok\",\"nozzle_center\":\"ok\",\"nozzle_hole_radius\":\"ok\",\"machine_level\":\"ok\",\"toggle_part_abrasion\":\"ok\",\"lm_guide_sliding_plate\":\"ok\",\"clamping_adjusting_motor\":\"ok\",\"grease_level\":\"ok\",\"lubrication_oil_level\":\"ok\",\"lubrication_flow\":\"ok\",\"lubrication_nipple_hose\":\"ok\",\"lube_tie_bar\":\"ok\",\"lube_sliding_injection\":\"ok\",\"lube_sliding_clamp\":\"ok\",\"lube_gear_adjust_mold\":\"ok\",\"clean_cover_under_machine\":\"ok\",\"clean_electrical_panel\":\"ok\",\"grease_gearbox_injection\":\"ok\",\"clean_air_filter\":\"ok\",\"machine_foot_check\":\"ok\",\"clean_cooling_barrel\":\"ok\",\"autoclamp_hose_pressure\":\"ok\"}','{\"voltage_rs\":\"380\",\"voltage_st\":\"380\",\"voltage_rt\":\"380\",\"voltage_pe_n\":\"0\",\"voltage_power_supply\":\"24\",\"electrical_component_temp\":\"36\",\"hydraulic_oil_temp\":\"50\",\"nozzle_hole_radius\":\"Dia \\/ Radius\"}','[DEMO APPROVAL] PM1 sudah di-approve Injection dan Maintenance.',NULL,'ok','close','2026-10-19 10:07:49','2026-10-19 10:07:49'),(16,13,'Khasanudin',69,27,NULL,NULL,NULL,NULL,NULL,NULL,'DEHUM','2026-10-19','shift_1','{\"dry_air_dew_point\":\"not_ok\",\"process_return_filter\":\"ok\",\"honeycomb_rotor_drive\":\"ok\",\"heater_ampere\":\"ok\"}','{\"dry_air_dew_point\":\"-45\"}','[DEMO APPROVAL] Filter dehum perlu ganti. Sudah close Maintenance.',NULL,'follow_up','close','2026-10-19 10:07:49','2026-10-19 10:07:49');
/*!40000 ALTER TABLE `preventive_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `preventive_schedules`
--

DROP TABLE IF EXISTS `preventive_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `preventive_schedules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `machine_name_id` bigint unsigned DEFAULT NULL,
  `machine_number` smallint unsigned NOT NULL,
  `machine_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `machine_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pic_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan_date` date NOT NULL,
  `preventive_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `preventive_schedules_user_id_foreign` (`user_id`),
  KEY `preventive_schedules_machine_name_id_foreign` (`machine_name_id`),
  KEY `preventive_schedules_plan_date_index` (`plan_date`),
  KEY `preventive_schedules_machine_number_plan_date_index` (`machine_number`,`plan_date`),
  CONSTRAINT `preventive_schedules_machine_name_id_foreign` FOREIGN KEY (`machine_name_id`) REFERENCES `machine_names` (`id`) ON DELETE SET NULL,
  CONSTRAINT `preventive_schedules_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preventive_schedules`
--

LOCK TABLES `preventive_schedules` WRITE;
/*!40000 ALTER TABLE `preventive_schedules` DISABLE KEYS */;
INSERT INTO `preventive_schedules` VALUES (1,3,9,1,'Hwachin 600T','Hidrolik Servo',NULL,'2026-09-10','PM1',NULL,'2026-09-09 10:18:53','2026-09-09 10:18:53'),(2,3,10,2,'Fu Chung Sin 450T','Hidrolik Servo',NULL,'2026-09-11','PM2',NULL,'2026-09-09 12:49:19','2026-09-09 12:49:19'),(3,3,11,3,'Toshiba 550T','Hidrolik Non-Servo',NULL,'2026-09-12','PM3',NULL,'2026-09-09 12:49:19','2026-09-09 12:49:19'),(4,3,9,1,'Hwachin 600T','Hidrolik Servo',NULL,'2026-09-11','PM2',NULL,'2026-09-09 12:51:33','2026-09-09 12:51:33'),(5,3,9,1,'Hwachin 600T','Hidrolik Servo',NULL,'2026-09-10','PM3',NULL,'2026-09-09 13:11:24','2026-09-09 13:11:24'),(6,3,12,4,'Hwa Chin 125T','Hidrolik Servo',NULL,'2026-09-14','ROBOT',NULL,'2026-09-09 13:11:24','2026-09-09 13:11:24'),(7,3,13,5,'Nissei 160T','Hidrolik Servo',NULL,'2026-09-08','MTC',NULL,'2026-09-09 13:11:24','2026-09-09 13:11:24'),(8,3,9,1,'Hwachin 600T','Hidrolik Servo',NULL,'2026-09-09','HOPPER',NULL,'2026-09-09 13:11:54','2026-09-09 13:11:54'),(9,3,9,1,'Hwachin 600T','Hidrolik Servo',NULL,'2026-09-09','HOT',NULL,'2026-09-09 13:49:24','2026-09-09 13:49:24'),(10,3,11,3,'Toshiba 550T','Hidrolik Non-Servo',NULL,'2026-09-10','JSWEL',NULL,'2026-09-09 13:49:24','2026-09-09 13:49:24'),(11,3,26,18,'JSW 450T','Elektrik Servo',NULL,'2026-09-16','DEHUM',NULL,'2026-09-09 13:49:24','2026-09-09 13:49:24');
/*!40000 ALTER TABLE `preventive_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `robots`
--

DROP TABLE IF EXISTS `robots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `robots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `robot_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `robot_type` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `robots_robot_number_unique` (`robot_number`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `robots`
--

LOCK TABLES `robots` WRITE;
/*!40000 ALTER TABLE `robots` DISABLE KEYS */;
INSERT INTO `robots` VALUES (1,'R01','YUSHIN SA 350 - WJ 450T',1,'2026-09-03 03:43:07','2026-09-07 14:08:25'),(2,'R02','YUSHIN SA 250 - FCS 450T',1,'2026-09-03 03:43:07','2026-09-07 14:08:51'),(3,'R03','YUSHIN HOP FIVE 550XC-GIIB - HC 125T',1,'2026-09-03 03:43:07','2026-09-07 14:09:05'),(4,'R04','YUSHIN SC 150 - NISSEI 160T',1,'2026-09-03 03:43:07','2026-09-07 14:09:27'),(5,'R05','YUSHIN SC 150 - LST 160T',1,'2026-09-03 03:43:07','2026-09-07 14:09:45'),(6,'R06','YUSHIN SC 150 - FCS 250T',1,'2026-09-03 03:43:07','2026-09-07 14:10:00'),(7,'R07','YUSHIN SC 150 - LG 250T',1,'2026-09-03 03:43:07','2026-09-07 14:10:17'),(8,'R08','YUSHIN SA 150 - BC 260T',1,'2026-09-03 03:43:07','2026-09-07 14:10:31'),(9,'R09','YUSHIN SA 1000 - SHB 850T',1,'2026-09-03 03:43:07','2026-09-07 14:10:46'),(10,'R10','YUSHIN SA 850 - SHB 850T',1,'2026-09-03 03:43:07','2026-09-07 14:11:00'),(11,'R11','YUSHIN SA 850 - SHB 850T',1,'2026-09-03 03:43:07','2026-09-07 14:11:12'),(12,'R12','YUSHIN SC 150 - SHB 350T',1,'2026-09-03 03:43:07','2026-09-07 14:12:00'),(13,'R13','YUSHIN SC 150 - SHB 350T',1,'2026-09-03 03:43:07','2026-09-07 14:12:06'),(14,'R14','EINS STAR ES-1200S - JSW 450T',1,'2026-09-03 03:43:07','2026-09-07 14:12:24'),(15,'R15','YUSHIN SC 350 - JSW 450T',1,'2026-09-03 03:43:07','2026-09-07 15:53:30'),(16,'R16','YUSHIN SC 250 - JSW 450T',1,'2026-09-03 03:43:07','2026-09-07 15:53:42'),(17,'R17','YUSHIN SC 250 - JSW 450T',1,'2026-09-03 03:43:07','2026-09-07 15:53:51'),(18,'R18','YUSHIN SC 350 - JSW 450T',1,'2026-09-03 03:43:07','2026-09-07 15:54:01'),(19,'R19','EINS STAR ES-1200S - JSW 450T',1,'2026-09-03 03:43:07','2026-09-07 15:54:30'),(20,'R20','EINS STAR ES-1200S - JSW 450T',1,'2026-09-03 03:43:07','2026-09-07 15:54:41'),(21,'R21','YUSHIN SC 150 - LG 250T',1,'2026-09-03 03:43:07','2026-09-07 15:55:03'),(22,'R22','STAR CYWS-1200GII - LS 400T',1,'2026-09-03 03:43:07','2026-09-07 15:55:21'),(23,'R23','YUSHIN SC 250 - LS 400T',1,'2026-09-03 03:43:07','2026-09-07 15:55:34'),(24,'R24','YUSHIN SA 150 - LS 280T',1,'2026-09-03 03:43:07','2026-09-07 15:55:49'),(25,'R25','YUSHIN SC 350 - JSW 550T',1,'2026-09-03 03:43:07','2026-09-07 15:56:03'),(26,'R26','YUSHIN SC 350 - HC 600T',1,'2026-09-03 03:43:07','2026-09-07 15:56:19'),(27,'R27','YUSHIN SA 850 - WJ 850T',1,'2026-09-03 03:43:07','2026-09-07 15:56:31'),(28,'R28','YUSHIN SA II 850 - WJ 850T',1,'2026-09-03 03:43:07','2026-09-07 15:56:45'),(29,'R29','YUSHIN SC 350 - SHB 650T',1,'2026-09-03 03:43:07','2026-09-07 15:57:02'),(30,'R30','CYH-1300P-L2000 - BC 650T',1,'2026-09-03 03:43:07','2026-09-07 15:57:40'),(31,'R31','YUSHIN SC 350 - SHB 550T',1,'2026-09-03 03:43:07','2026-09-07 15:57:54'),(32,'R32','CHUANY - HC 800T',1,'2026-09-03 03:43:07','2026-09-07 15:58:09'),(33,'R33','YUSHIN SA 150 - LG 250T',1,'2026-09-03 03:43:07','2026-09-07 15:58:26'),(34,'R34','CHUANY - HC 250T',1,'2026-09-03 03:43:07','2026-09-07 15:58:40'),(35,'R35','YUSHIN SA 150 - HC 250T',1,'2026-09-03 03:43:07','2026-09-07 15:59:02'),(36,'R36','YUSHIN SC 150 - SHB 250T',1,'2026-09-03 03:43:07','2026-09-07 15:59:18'),(37,'R37','YUSHIN SC 650T II-600 - SHB 650T',1,'2026-09-03 03:43:07','2026-09-07 15:59:56'),(38,'R38','YUSHIN SC 350T - JSW 450T',1,'2026-09-03 03:43:07','2026-09-07 16:00:19'),(39,'R39','YUSHIN SC 350T - JSW 450T',1,'2026-09-03 03:43:07','2026-09-07 16:00:30'),(40,'R40','YUSHIN SA 350T II-850 - JSW 650T',1,'2026-09-03 03:43:07','2026-09-07 16:00:43'),(41,'R41','YUSHIN SC 250 - HC 450T',1,'2026-09-03 03:43:07','2026-09-07 16:01:01'),(42,'R42','YUSHIN SC 250 - HC 450T',1,'2026-09-03 03:43:07','2026-09-07 16:01:06'),(43,'R43','YUSHIN SC 250 - HC 450T',1,'2026-09-03 03:43:07','2026-09-07 16:01:14'),(44,'R44','YUSHIN SC 250 - LS 400T',1,'2026-09-03 03:43:07','2026-09-07 16:01:28'),(45,'R45','STAR C4WS-1200GII - HC 800T',1,'2026-09-03 03:43:07','2026-09-07 16:01:42'),(46,'R46','YUSHIN SA II-850 - JSW 850T',1,'2026-09-03 03:43:07','2026-09-07 16:01:56'),(47,'R47','YUSHIN SA II-1000 - TOSH 1300T',1,'2026-09-03 03:43:07','2026-09-07 16:02:09'),(48,'R48','YUSHIN HOP FIVE 550XC-GIIB - JM 88T',1,'2026-09-03 03:43:07','2026-09-07 16:02:21');
/*!40000 ALTER TABLE `robots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
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
INSERT INTO `sessions` VALUES ('7v8uDe2dEQKS4cE7KJdcKRt8Z8nXu1nYcQkN4mCA',14,'192.168.50.126','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUjFSY3V4S2NtZnJTdWhZZ2hvbFlSOHdTWkFaSVBmTnhCZGwwRmRPaiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly9lLW1haW50ZW5hbmNlLmRwcGxhc3QuY29tL2FkbWluL2Rhc2hib2FyZCI7czo1OiJyb3V0ZSI7czoxNToiYWRtaW4uZGFzaGJvYXJkIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTQ7fQ==',1792651354);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `technicians`
--

DROP TABLE IF EXISTS `technicians`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `technicians` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `npk` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `technicians_npk_unique` (`npk`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `technicians`
--

LOCK TABLES `technicians` WRITE;
/*!40000 ALTER TABLE `technicians` DISABLE KEYS */;
INSERT INTO `technicians` VALUES (1,'Khasanudin','20161396','$2y$12$028puS0Yamy7UqFmPZ.RLOuUpKK2xbN5todXnMPkISEjCviYPj/Da',1,'2026-08-28 03:37:37','2026-09-21 16:14:33'),(2,'Welly','2016397','$2y$12$ux2jElQ689K/AOPpRuQ77eDFdfPob4N/bccbFY0LkzTY5KQWFOTZm',1,'2026-08-28 03:37:37','2026-09-21 16:15:12'),(3,'Erickson','20191732','$2y$12$.mWhIPvqIlRQ9s8QYr8MPOjm4WTJ9ajkGppgk85ladOev7.fKFNX2',1,'2026-09-03 02:11:15','2026-09-21 16:14:57'),(4,'Adit','20262093','$2y$12$BG32x4GW0WubiY3kppOMseLJGrflTKXVmxTxXueOw16bcChGDld0u',1,'2026-09-21 16:15:35','2026-09-21 16:15:35'),(5,'Warno','20141075','$2y$12$m3A.k..NC.gMVLbs97NbTu.zDLrpIaS7BPsnBUBB4JPt4wwlKEGKC',1,'2026-09-21 16:15:54','2026-09-21 16:15:54'),(6,'Yitno Unix','0111','$2y$12$.OYP2PQc0h2z0xwyJyYRz.zzYZ6bXxjvh7h3AZ/2nrYRRtSSKmmlq',1,'2026-09-21 16:16:49','2026-09-21 16:16:49'),(7,'Doni Unix','0222','$2y$12$5bnf/eTWu0rXJShVQJPa1Ob5qkokxS1z3JE1BbA9yjkMh.SQ9PHOW',1,'2026-09-21 16:17:03','2026-09-21 16:17:30'),(8,'Rapli Unix','0333','$2y$12$tb/0g.j0tniUq1qLyJVl8ewpzvrY74WTSM2AoYDaETkmZ.09xdki2',1,'2026-09-21 16:17:25','2026-09-21 16:17:25'),(9,'Gibson Unix','0444','$2y$12$LXM3LTO54KSIsjFDeFPCSefrNELm51tTYCucSNJ5cl4DaFKnUkeWG',1,'2026-09-21 16:17:54','2026-09-21 16:17:54'),(10,'Nur ATS','1222','$2y$12$bSyp0TZBZ2qyEXT.95NvyuZEND04okl9n/MNwdFZwpfDmbl41YrrO',1,'2026-09-21 16:18:14','2026-09-21 16:18:14'),(11,'Nurul Robot','2111','$2y$12$/vPeSmZDyW.Gh8eLSnE.F.1554hXAoPVu7T0rsrT/QzOvwXuDtCN2',1,'2026-09-21 16:18:33','2026-09-21 16:18:33'),(12,'Rozak JSW','3111','$2y$12$jhU5xZ7IzikcB3a65D94FeCWc4BEHsc6ZW.7OYlC1d1VE34x..9fC',1,'2026-09-21 16:18:50','2026-09-21 16:18:50');
/*!40000 ALTER TABLE `technicians` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `npk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'technician',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_npk_unique` (`npk`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (3,'Prayudi','20221913','admin',NULL,'$2y$12$jOh/2Qmgtvc0KiFRJjcN2.KhMdoRv/o45wdXPs0uyjcihEpWuD17a',NULL,'2026-05-26 04:03:18','2026-10-19 10:25:24'),(7,'Prayudi Injection','20221916','produksi_injection',NULL,'$2y$12$lCMUrsgZFlUS8IbR1C5e3.emLSh4jr53VpThxQ/03WNsB9xJZTWm.',NULL,'2026-08-28 03:00:02','2026-10-19 10:09:26'),(13,'Teknisi Maintenance','20260109','technician',NULL,'$2y$12$t4kLk.Qa8bfWr/hZD/Llf.qvaP0oKvvOaZBFC5w0oC3QXi02rNRGC',NULL,'2026-08-28 04:15:53','2026-10-20 16:18:32'),(14,'Apfia Niken T','20201799','admin',NULL,'$2y$12$PVV7rlEk2EtYQCq/twQIYeBmAekQE7Mx0PwkFhqSZSRrYrfzTyleK',NULL,'2026-09-03 02:40:00','2026-10-22 09:21:54'),(15,'Jumihadi','20030028','admin',NULL,'$2y$12$fC5K.Tg4LxVI685kDce5DOAvAYNAfpsfBqUVk4TNwTdrLdTVZL93K',NULL,'2026-09-03 11:33:04','2026-09-18 08:59:15'),(16,'Rangga E. Hutomo','20242014','admin',NULL,'$2y$12$3OVHh7f0ooqRiJmI21i4S.qG8DAy7038qM5PcrIkZSTvLQQ/z.zFO',NULL,'2026-09-08 08:03:14','2026-09-21 16:19:41'),(17,'Erickson H.','20191732','admin',NULL,'$2y$12$unhLqW9WiQPvK1opqcGDKOToCEIPctCQTg6W7i1n8kTanMAybfu5C',NULL,'2026-09-08 08:03:47','2026-09-21 16:19:20'),(19,'Injection','20260209','produksi_injection',NULL,'$2y$12$lEYERKeP529lObKoQVuN0.qpSLMJq3WfR.K3lZN7tpeUuo3lQ6JGm',NULL,'2026-10-16 17:01:08','2026-10-16 17:03:08'),(20,'Quality Demo','300001','quality','2026-10-16 17:12:38','$2y$12$x2sX1GJsNAnWpdrbo7sG6.Z2ib.dozvF0eTRRWOBoEXMvAYeiyu3S',NULL,'2026-10-16 17:12:38','2026-10-16 17:12:38'),(21,'Management Demo','500001','management','2026-10-19 10:16:47','$2y$12$JzEDJxEYIeREGsDFZWgBVObTO6YuEgqyQcUMoQ.ijAtfIEpE4IaKq',NULL,'2026-10-19 10:16:47','2026-10-20 14:34:40');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'e_maintenance'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-22  6:42:43

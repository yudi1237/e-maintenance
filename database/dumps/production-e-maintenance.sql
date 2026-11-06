-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: e_maintenance
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
-- Table structure for table `area_operator_reports`
--

DROP TABLE IF EXISTS `area_operator_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `area_operator_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `area` varchar(255) NOT NULL,
  `reporter_name` varchar(255) NOT NULL,
  `part_name` varchar(255) DEFAULT NULL,
  `equipment_name` varchar(255) DEFAULT NULL,
  `stop_date` date NOT NULL,
  `stop_time` time NOT NULL,
  `problem` text NOT NULL,
  `chronology` text DEFAULT NULL,
  `claimed_by` bigint(20) unsigned DEFAULT NULL,
  `claimed_technician_id` bigint(20) unsigned DEFAULT NULL,
  `claimed_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `area_operator_reports_claimed_by_foreign` (`claimed_by`),
  KEY `area_operator_reports_claimed_technician_id_foreign` (`claimed_technician_id`),
  KEY `area_operator_reports_area_stop_date_index` (`area`,`stop_date`),
  KEY `area_operator_reports_status_index` (`status`),
  CONSTRAINT `area_operator_reports_claimed_by_foreign` FOREIGN KEY (`claimed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `area_operator_reports_claimed_technician_id_foreign` FOREIGN KEY (`claimed_technician_id`) REFERENCES `technicians` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `area_operator_reports`
--

LOCK TABLES `area_operator_reports` WRITE;
/*!40000 ALTER TABLE `area_operator_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `area_operator_reports` ENABLE KEYS */;
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
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
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
  `expiration` int(11) NOT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `damage_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `allows_other` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `damage_categories_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `damage_categories`
--

LOCK TABLES `damage_categories` WRITE;
/*!40000 ALTER TABLE `damage_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `damage_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipments`
--

DROP TABLE IF EXISTS `equipments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `equipments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(40) NOT NULL,
  `number` varchar(50) NOT NULL,
  `type` varchar(150) NOT NULL,
  `specification` varchar(200) DEFAULT NULL,
  `area` varchar(40) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `equipments_category_number_unique` (`category`,`number`),
  KEY `equipments_category_index` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipments`
--

LOCK TABLES `equipments` WRITE;
/*!40000 ALTER TABLE `equipments` DISABLE KEYS */;
/*!40000 ALTER TABLE `equipments` ENABLE KEYS */;
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
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `finding_recap_status_notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `finding_recap_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `note` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `finding_recap_status_notes_user_id_foreign` (`user_id`),
  KEY `finding_recap_status_notes_finding_recap_id_status_index` (`finding_recap_id`,`status`),
  CONSTRAINT `finding_recap_status_notes_finding_recap_id_foreign` FOREIGN KEY (`finding_recap_id`) REFERENCES `finding_recaps` (`id`) ON DELETE CASCADE,
  CONSTRAINT `finding_recap_status_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finding_recap_status_notes`
--

LOCK TABLES `finding_recap_status_notes` WRITE;
/*!40000 ALTER TABLE `finding_recap_status_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `finding_recap_status_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finding_recaps`
--

DROP TABLE IF EXISTS `finding_recaps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `finding_recaps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `machine_name_id` bigint(20) unsigned DEFAULT NULL,
  `machine_number` smallint(5) unsigned NOT NULL,
  `machine_name` varchar(255) NOT NULL,
  `equipment_name` varchar(255) NOT NULL,
  `found_date` date NOT NULL,
  `due_date` date NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `finding_recaps_user_id_foreign` (`user_id`),
  KEY `finding_recaps_machine_name_id_foreign` (`machine_name_id`),
  KEY `finding_recaps_status_found_date_index` (`status`,`found_date`),
  KEY `finding_recaps_machine_number_found_date_index` (`machine_number`,`found_date`),
  CONSTRAINT `finding_recaps_machine_name_id_foreign` FOREIGN KEY (`machine_name_id`) REFERENCES `machine_names` (`id`) ON DELETE SET NULL,
  CONSTRAINT `finding_recaps_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finding_recaps`
--

LOCK TABLES `finding_recaps` WRITE;
/*!40000 ALTER TABLE `finding_recaps` DISABLE KEYS */;
/*!40000 ALTER TABLE `finding_recaps` ENABLE KEYS */;
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
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
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
-- Table structure for table `layout_cluster_names`
--

DROP TABLE IF EXISTS `layout_cluster_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `layout_cluster_names` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `layout_cluster_names_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `layout_cluster_names`
--

LOCK TABLES `layout_cluster_names` WRITE;
/*!40000 ALTER TABLE `layout_cluster_names` DISABLE KEYS */;
/*!40000 ALTER TABLE `layout_cluster_names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `machine_names`
--

DROP TABLE IF EXISTS `machine_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `machine_names` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `machine_number` smallint(5) unsigned DEFAULT NULL,
  `plant` varchar(40) NOT NULL DEFAULT 'plant_1_cikarang',
  `asset_number` varchar(255) DEFAULT NULL,
  `screw_diameter_mm` smallint(5) unsigned DEFAULT NULL,
  `machine_photos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `brand` varchar(255) DEFAULT NULL,
  `machine_model` varchar(255) DEFAULT NULL,
  `tonnage` smallint(5) unsigned DEFAULT NULL,
  `clamping_type` varchar(255) DEFAULT NULL,
  `machine_type` varchar(255) DEFAULT NULL,
  `tie_bar_distance_mm` varchar(255) DEFAULT NULL,
  `platen_size_mm` varchar(255) DEFAULT NULL,
  `mold_thickness_min_mm` smallint(5) unsigned DEFAULT NULL,
  `mold_thickness_max_mm` smallint(5) unsigned DEFAULT NULL,
  `clamp_stroke_mm` smallint(5) unsigned DEFAULT NULL,
  `power_pump_kw` decimal(8,2) DEFAULT NULL,
  `power_heater_kw` decimal(8,2) DEFAULT NULL,
  `power_total_kw` decimal(8,2) DEFAULT NULL,
  `shot_weight_small_gr` smallint(5) unsigned DEFAULT NULL,
  `shot_weight_med_gr` smallint(5) unsigned DEFAULT NULL,
  `manufacture_year` smallint(5) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `specification` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `severity_conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`severity_conditions`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `mold_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `machine_names_asset_number_unique` (`asset_number`),
  UNIQUE KEY `machine_names_plant_machine_number_unique` (`plant`,`machine_number`),
  KEY `machine_names_mold_id_foreign` (`mold_id`),
  CONSTRAINT `machine_names_mold_id_foreign` FOREIGN KEY (`mold_id`) REFERENCES `molds` (`id`) ON DELETE SET NULL,
  CONSTRAINT `machine_names_chk_1` CHECK (json_valid(`machine_photos`))
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `machine_names`
--

LOCK TABLES `machine_names` WRITE;
/*!40000 ALTER TABLE `machine_names` DISABLE KEYS */;
INSERT INTO `machine_names` VALUES (9,1,'plant_1_cikarang','43000000023',1,'[\"machine-photos\\/1\\/7PRYPdzVQ0yjOAbaVAikQZnDS2mBDc73NSIqmpsS.jpg\"]','Hwachin','HC 600 SE',600,'Toggle','Hidrolik Servo','880 x 880','1300 x 1300',275,1020,970,59.00,38.00,97.00,1748,1989,2012,'Hwachin HC 600 SE 600T',NULL,1,'[\"minor_issue\"]','2026-06-12 09:39:48','2026-11-03 13:56:09',NULL),(10,2,'plant_1_cikarang','41000000061',75,'[\"machine-photos\\/2\\/2jJgWvZS9I7II64GuwpdDSjtJJFuQemAlqqwjqCC.png\"]','Fu Chung Sin','FCS-HT-450SV',450,'Toggle','Hidrolik Servo','760 x 760','1120 x 1120',250,800,730,44.76,23.05,67.81,840,1011,2011,'Fu Chung Sin FCS-HT-450SV 450T',NULL,1,'[\"total_breakdown\"]','2026-08-18 04:28:12','2026-10-28 10:39:59',NULL),(11,3,'plant_1_cikarang','41000000238',1,NULL,'Toshiba','IS 550 GS-34 A',550,'Direct Hydraulic','Hidrolik Non-Servo','860 x 860','1230 x 1230',400,400,1200,74.00,29.20,103.20,1,1,1996,'Toshiba IS 550 GS-34 A 550T','Mesin Akan Disposal pada Oktober 2026',0,'[\"major_breakdown\"]','2026-08-28 03:37:37','2026-11-03 13:59:28',NULL),(12,4,'plant_1_cikarang','41000000142',1,NULL,'Hwa Chin','HC 125 SE',125,'Toggle','Hidrolik Servo','410 x 400','580 x 580',125,425,450,15.00,8.20,23.20,127,180,2012,'Hwa Chin HC 125 SE 125T',NULL,1,'[\"medium_breakdown\"]','2026-08-28 03:37:37','2026-10-28 10:40:17',NULL),(13,5,'plant_1_cikarang','41000000037',1,NULL,'Nissei','FE160S36A',160,'Toggle','Hidrolik Servo','460 x 460','685 x 685',200,410,470,30.00,12.50,42.50,225,314,1995,'Nissei FE160S36A 160T',NULL,1,'[\"major_breakdown\"]','2026-08-28 03:37:37','2026-10-28 10:40:25',NULL),(15,7,'plant_1_cikarang','41000000170',1,NULL,'LST','LST 160',160,'Toggle','Hidrolik Servo','480 x 480','710 x 710',180,520,435,23.00,11.50,34.50,226,316,2013,'LST LST 160 160T',NULL,1,'[\"total_breakdown\"]','2026-09-02 06:53:54','2026-10-28 10:12:48',NULL),(16,8,'plant_1_cikarang','41000000060',1,NULL,'Fu Chung Sin','FCS-HT-250SV',250,'Toggle','Hidrolik Servo','555 x 555','820 x 820',160,670,550,29.84,16.53,46.37,429,500,2011,'Fu Chung Sin FCS-HT-250SV 250T',NULL,1,'[\"quick_fix\"]','2026-09-02 06:53:54','2026-10-28 10:40:35',NULL),(17,9,'plant_1_cikarang','41000000034',1,NULL,'Borche','BS 120 - III',120,'Toggle','Hidrolik Servo','410 x 410','590 x 590',145,450,340,11.00,8.86,19.86,171,225,2010,'Borche BS 120 - III 120T',NULL,1,'[\"minor_issue\"]','2026-09-02 06:53:54','2026-10-28 10:40:42',NULL),(18,10,'plant_1_cikarang','41000000056',1,NULL,'LG','LGH250D',250,'Direct Hydraulic','Hidrolik Non-Servo','610 x 610','830 x 830',240,240,770,37.00,16.17,53.70,551,666,2006,'LG LGH250D 250T',NULL,1,NULL,'2026-09-02 06:53:54','2026-10-28 10:40:51',NULL),(19,11,'plant_1_cikarang','41000000031',1,NULL,'Borche','BS 260 - III',260,'Toggle','Hidrolik Servo','580 x 580','835 x 835',195,600,520,22.00,16.10,38.10,552,800,2010,'Borche BS 260 - III 260T',NULL,1,'[\"medium_breakdown\"]','2026-09-02 06:53:54','2026-10-28 10:40:59',NULL),(20,12,'plant_1_cikarang','41000000419',1,NULL,'Shibaura','EC850SXIII/i78',850,'Toggle','Elektrik Servo','1320 x 1320','1790 X 1790',500,1100,1200,105.00,44.90,57.20,3160,3980,2023,'Shibaura EC850SXIII/i78 850T',NULL,1,'[\"major_breakdown\"]','2026-09-02 06:53:54','2026-10-28 10:41:06',NULL),(21,13,'plant_1_cikarang','41000000420',1,NULL,'Shibaura','EC850SXIII/i78',850,'Toggle','Elektrik Servo','1320 x 1320','1790 X 1790',500,1100,1200,105.00,44.40,57.20,3160,3980,2023,'Shibaura EC850SXIII/i78 850T',NULL,1,NULL,'2026-09-02 06:53:54','2026-10-28 10:41:15',NULL),(22,14,'plant_1_cikarang','41000000447',1,NULL,'Shibaura','TD 850/1120',850,'Toggle','Hidrolik Servo','1120 x 1000','1620 x 1500',500,1170,1030,75.00,44.90,119.90,861,2870,2025,'Shibaura TD 850/1120 850T',NULL,1,NULL,'2026-09-02 06:53:54','2026-10-28 10:41:48',NULL),(23,15,'plant_1_cikarang','DEMO-015',NULL,NULL,'Demo','Mesin Demo 15',300,'Toggle','Injection',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Mesin Demo 15',NULL,1,'[\"total_breakdown\"]','2026-09-02 06:53:54','2026-11-06 09:03:36',NULL),(24,16,'plant_1_cikarang','41000000423',1,NULL,'Shibaura','EC350SXIII/i17A',350,'Toggle','Elektrik Servo','820 x 740','1110 X 1030',300,820,650,60.00,46.70,1.00,619,780,2023,'Shibaura EC350SXIII/i17A 350T',NULL,1,NULL,'2026-09-02 06:53:54','2026-10-28 10:41:42',NULL),(25,17,'plant_1_cikarang','41000000424',1,NULL,'Shibaura','EC350SXIII/i17A',350,'Toggle','Elektrik Servo','820 x 740','1110 X 1030',300,820,650,60.00,46.70,1.00,619,780,2023,'Shibaura EC350SXIII/i17A 350T',NULL,1,NULL,'2026-09-02 06:53:54','2026-10-28 10:41:56',NULL),(26,18,'plant_1_cikarang','41000000450',1,NULL,'JSW','J450AD-1400H',450,'Toggle','Elektrik Servo','900 X 810','1300 X 1210',350,800,1600,1.00,36.00,51.00,1238,1,2011,'JSW J450AD-1400H 450T',NULL,1,'[\"quick_fix\"]','2026-09-02 06:53:54','2026-10-28 10:42:08',NULL),(27,19,'plant_1_cikarang','41000000451',1,NULL,'JSW','J450AD-1400H',450,'Toggle','Elektrik Servo','900 X 810','1300 X 1210',350,800,1600,1.00,36.00,50.98,1238,1,2011,'JSW J450AD-1400H 450T',NULL,1,NULL,'2026-09-02 06:53:54','2026-10-28 10:42:16',NULL),(28,20,'plant_1_cikarang','41000000461',1,NULL,'JSW','J450AD-1400H',450,'Toggle','Elektrik Servo','900 X 810','1301 X 1210',350,800,1600,1.00,36.00,51.00,1238,1,2013,'JSW J450AD-1400H 450T',NULL,1,NULL,'2026-09-02 06:53:54','2026-10-28 10:42:22',NULL),(29,21,'plant_1_cikarang','41000000452',1,NULL,'JSW','J450AD-1400H',450,'Toggle','Elektrik Servo','900 X 810','1302 X 1210',350,800,1600,1.00,36.00,51.01,1238,1,2012,'JSW J450AD-1400H 450T',NULL,1,NULL,'2026-09-02 06:53:54','2026-10-28 10:42:29',NULL),(30,22,'plant_1_cikarang','41000000453',1,NULL,'JSW','J450AD-1400H',450,'Toggle','Elektrik Servo','900 X 810','1303 X 1210',350,800,1600,1.00,36.00,51.00,1238,1,2012,'JSW J450AD-1400H 450T',NULL,1,'[\"minor_issue\"]','2026-09-02 06:53:54','2026-10-28 10:42:36',NULL),(31,23,'plant_1_cikarang','41000000449',1,NULL,'JSW','J450AD-2300H',200,'Toggle','Elektrik Servo','900 X 810','1300 X 1210',350,800,1600,1.00,41.10,55.90,2118,1,2012,'JSW J450AD-2300H 200T',NULL,1,NULL,'2026-09-02 06:53:54','2026-10-28 10:42:44',NULL),(32,24,'plant_1_cikarang','41000000460',1,NULL,'JSW','J450AD-2300H',450,'Toggle','Elektrik Servo','900 X 810','1300 X 1210',350,800,1600,1.00,41.10,55.90,2118,1,2012,'JSW J450AD-2300H 450T',NULL,1,NULL,'2026-09-02 06:53:54','2026-10-28 10:42:51',NULL),(33,25,'plant_1_cikarang','41000000056-2',1,NULL,'LG','LGH250D',250,'Direct Hydaulic','Hidrolik Non-Servo','610 x 610','830 x 830',240,240,770,37.00,16.70,53.70,551,666,2006,'LG LGH250D 250T',NULL,1,NULL,'2026-09-02 06:53:54','2026-10-28 10:42:57',NULL),(34,26,'plant_1_cikarang','41000000173-1',1,NULL,'LST','LST 400',400,'Toggle','Hidrolik Servo','730 x 730','1470 x 1470',250,780,720,58.00,26.00,84.00,1293,1722,2013,'LST LST 400 400T',NULL,1,NULL,'2026-09-02 06:53:54','2026-10-28 10:43:03',NULL),(69,27,'plant_1_cikarang','41000000173-2',1,NULL,'LST','LST 400',400,'Toggle','Hidrolik Servo','730 x 730','1470 x 1470',250,780,720,58.00,26.00,84.00,1293,1722,2013,'LST LST 400 400T',NULL,1,NULL,'2026-09-04 14:12:17','2026-10-28 10:43:10',NULL),(70,28,'plant_1_cikarang','41000000171',1,NULL,'LST','LST 280',280,'Toggle','Hidrolik Servo','630 x 630','940 x 940',200,630,590,39.00,16.00,55.00,509,733,2013,'LST LST 280 280T',NULL,1,NULL,'2026-09-04 16:16:53','2026-10-28 10:43:17',NULL),(71,30,'plant_1_cikarang','41000000013',1,NULL,'Borche','BS 320 - III',320,'Toggle','Hidrolik Servo','670 x 670','950 x 950',220,655,580,30.00,16.10,46.10,552,800,2010,'Borche BS 320 - III 320T',NULL,1,NULL,'2026-09-04 16:18:37','2026-10-28 10:43:23',NULL),(72,31,'plant_1_cikarang','41000000051',1,NULL,'JSW','J - 550E - III',550,'t','Hidrolik Servo','900 x 900','1300 x 1300',500,900,1300,70.00,39.20,109.20,2118,2541,1998,'JSW J - 550E - III 550T',NULL,1,NULL,'2026-09-04 16:20:00','2026-09-04 16:20:00',NULL),(73,32,'plant_1_cikarang','43000000023-2',1,NULL,'Hwa Chin','HC 600 SE',600,'Toggle','Hidrolik Servo','880 x 880','1300 x 1300',275,1020,970,59.00,38.00,97.00,1748,1989,2012,'Hwa Chin HC 600 SE 600T',NULL,1,NULL,'2026-09-04 16:22:01','2026-09-04 16:22:01',NULL),(74,33,'plant_1_cikarang','41000000368',1,NULL,'Woojin','Woojin DL850A5',850,'Half-Nut Two Platen','Hidrolik Servo','1110 X 1110','1610 X 1610',500,1200,1,94.00,49.60,143.60,3788,4628,2019,'Woojin Woojin DL850A5 850T',NULL,1,NULL,'2026-09-04 16:23:51','2026-09-04 16:23:51',NULL),(75,34,'plant_1_cikarang','41000000367',1,NULL,'Woojin','Woojin DL450A5',450,'Half-Nut Two Platen','Hidrolik Servo','860 X 810','1240 X 1190',350,800,1,52.00,24.20,76.20,1177,1365,2019,'Woojin Woojin DL450A5 450T',NULL,1,NULL,'2026-09-04 16:26:40','2026-09-04 16:26:40',NULL),(76,35,'plant_1_cikarang','41000000370',1,NULL,'Woojin','Woojin DL850A5',850,'Half-Nut Two Platen','h','1110 X 1110','1610 X 1610',500,1200,1,94.00,49.60,143.60,3788,4628,2019,'Woojin Woojin DL850A5 850T',NULL,1,NULL,'2026-09-04 16:27:56','2026-09-04 16:27:56',NULL),(77,36,'plant_1_cikarang','41000000443',1,NULL,'Shibaura','TD 650/1000',650,'Toggle','Hidrolik Servo','1000X900','1430X1350',450,1070,930,55.00,1.00,55.00,1940,1,2025,'Shibaura TD 650/1000 650T',NULL,1,NULL,'2026-09-04 16:57:46','2026-09-04 16:57:46',NULL),(78,37,'plant_1_cikarang','41000000040',1,NULL,'Borche','BS 650 - III',650,'Toggle','Hidrolik Servo','930 x 900','1450 x 1300',300,870,880,51.00,33.20,84.20,2125,2690,2010,'Borche BS 650 - III 650T',NULL,1,NULL,'2026-09-04 16:59:27','2026-09-04 16:59:27',NULL),(79,38,'plant_1_cikarang','41000000442',1,NULL,'Shibaura','TD 550/870',550,'Toggle','Hidrolik Servo','870X870','1265X1265',380,900,980,55.00,1.00,1.00,1940,1,2025,'Shibaura TD 550/870 550T',NULL,1,NULL,'2026-09-04 17:00:48','2026-09-04 17:00:48',NULL),(80,39,'plant_1_cikarang','41000000084',1,NULL,'Hwa Chin','HC 800 SE',800,'Toggle','Hidrolik Servo','1005 x 1000','1470 x 1470',300,1100,1080,74.20,38.00,112.20,2124,2689,2012,'Hwa Chin HC 800 SE 800T',NULL,1,NULL,'2026-09-04 17:02:22','2026-09-04 17:02:22',NULL),(81,40,'plant_1_cikarang','41000000056-3',1,NULL,'LG','LGH250D',250,'Direct Hydaulic','Hidrolik Non-Servo','610 x 610','830 x 830',240,240,770,37.00,16.70,53.70,551,666,2005,'LG LGH250D 250T',NULL,1,NULL,'2026-09-04 17:04:05','2026-09-04 17:04:05',NULL),(82,41,'plant_1_cikarang','43000000022',1,NULL,'Hwa Chin','HC 250 SE',250,'Toggle','Hidrolik Servo','560 x 550','810 x 810',180,600,600,30.00,16.00,46.00,382,502,2012,'Hwa Chin HC 250 SE 250T',NULL,1,NULL,'2026-09-04 17:05:50','2026-09-04 17:05:50',NULL),(83,42,'plant_1_cikarang','43000000022-2',1,NULL,'Hwa Chin','HC 250 SE',250,'Toggle','Hidrolik Servo','560 x 550','810 x 810',180,600,600,30.00,16.00,46.00,382,502,2012,'Hwa Chin HC 250 SE 250T',NULL,1,NULL,'2026-09-04 17:07:02','2026-09-04 17:07:02',NULL),(84,43,'plant_1_cikarang','41000000441',1,NULL,'Shibaura','TD 250/580',250,'Toggle','Hidrolik Servo','580x580','860x860',350,790,790,30.00,1.00,1.00,1,1,2024,'Shibaura TD 250/580 250T',NULL,1,NULL,'2026-09-04 17:08:25','2026-09-04 17:08:25',NULL),(85,44,'plant_1_cikarang','41000000459',1,NULL,'JSW','J650AD-2300H',650,'Toggle','Elektrik Servo','1060 X 960','1500 X 1400',450,1000,2000,1.00,39.20,1.00,2118,2118,2017,'JSW J650AD-2300H 650T',NULL,1,NULL,'2026-09-04 17:09:54','2026-09-04 17:09:54',NULL),(86,45,'plant_1_cikarang','41000000457',1,NULL,'JSW','J450AD-1400H',450,'Toggle','Elektrik Servo','900 X 810','1301 X 1210',350,800,1600,1.00,36.00,1.00,1238,1238,2013,'JSW J450AD-1400H 450T',NULL,1,NULL,'2026-09-04 17:11:23','2026-09-04 17:11:23',NULL),(87,46,'plant_1_cikarang','41000000455',1,NULL,'JSW','J450AD-2300H',450,'Toggle','Elektrik Servo','900 X 810','1300 X 1210',350,800,1600,1.00,41.10,55.90,2118,2118,2013,'JSW J450AD-2300H 450T',NULL,1,NULL,'2026-09-04 17:12:36','2026-09-04 17:12:36',NULL),(88,47,'plant_1_cikarang','41000000456',1,NULL,'JSW','J450AD-2300H',450,'Toggle','Elektrik Servo','900 X 810','1300 X 1210',350,800,1600,1.00,41.10,55.90,2118,2118,2013,'JSW J450AD-2300H 450T',NULL,1,NULL,'2026-09-04 17:14:41','2026-09-04 17:14:41',NULL),(89,48,'plant_1_cikarang','41000000458',1,NULL,'JSW','J650AD-2300H',650,'Toggle','Elektrik Servo','1060 X 960','1500 X 1400',450,1000,2000,1.00,39.20,1.00,2118,2118,2013,'JSW J650AD-2300H 650T',NULL,1,NULL,'2026-09-04 17:16:43','2026-09-04 17:16:43',NULL),(90,49,'plant_1_cikarang','41000000082-1',1,NULL,'Hwa Chin','HC 450 SE',450,'Toggle','Hidrolik Servo','780 x 780','1160 x 1160',250,870,900,45.00,31.00,76.00,1385,1590,2012,'Hwa Chin HC 450 SE 450T',NULL,1,NULL,'2026-09-07 13:13:41','2026-09-07 13:13:41',NULL),(91,50,'plant_1_cikarang','41000000082-2',1,NULL,'Hwa Chin','HC 450 SE',450,'Toggle','Hidrolik Servo','780 x 780','1160 x 1160',250,870,900,45.00,31.00,76.00,1385,1590,2012,'Hwa Chin HC 450 SE 450T',NULL,1,NULL,'2026-09-07 13:15:12','2026-09-07 13:15:12',NULL),(92,51,'plant_1_cikarang','41000000090',1,NULL,'Hwa Chin','HC 250 SE',450,'Toggle','Hidrolik Servo','780 x 780','1160 x 1160',250,870,900,45.00,31.00,76.00,1385,1590,2012,'Hwa Chin HC 250 SE 450T',NULL,1,NULL,'2026-09-07 13:16:44','2026-09-07 13:16:44',NULL),(93,52,'plant_1_cikarang','41000000173',1,NULL,'LST','LST 400',400,'Toggle','Hidrolik Servo','730 x 730','1470 x 1470',250,780,720,58.00,26.00,84.00,1293,1722,2013,'LST LST 400 400T',NULL,1,NULL,'2026-09-07 13:18:07','2026-09-07 13:18:07',NULL),(94,53,'plant_1_cikarang','41000000084-2',1,NULL,'Hwa Chin','HC 800 SE',800,'Toggle','Hidrolik Servo','1005 x 1000','1470 x 1470',300,1100,1080,74.20,38.00,112.20,2124,2689,2012,'Hwa Chin HC 800 SE 800T',NULL,1,NULL,'2026-09-07 13:19:34','2026-09-07 13:19:34',NULL),(95,54,'plant_1_cikarang','41000000085',1,NULL,'JSW','J850 E - II',850,'Toggle','Hidrolik Non-Servo','1060 x 1060','1590 x 1590',550,1100,1200,85.10,46.30,131.40,3574,4323,1999,'JSW J850 E - II 850T',NULL,1,NULL,'2026-09-07 13:20:54','2026-09-07 13:20:54',NULL),(96,55,'plant_1_cikarang','41000000414',1,NULL,'Toshiba','1300T',1300,'Half-Nut Two Platen','Hidrolik Servo','1340 x 1300','2000 x 1900',1,1,1,1.00,1.00,1.00,1,1,2005,'Toshiba 1300T 1300T',NULL,1,NULL,'2026-09-07 13:22:04','2026-09-07 13:22:04',NULL),(97,57,'plant_1_cikarang','41000000274',1,NULL,'Woojin','Vertical VHA 50RS',50,'Toggle','Hidrolik Servo','1','1',1,1,1,1.00,1.00,1.00,1,1,2017,'Woojin Vertical VHA 50RS 50T',NULL,1,NULL,'2026-09-07 13:23:16','2026-09-07 13:23:16',NULL),(98,58,'plant_1_cikarang','41000000232',1,NULL,'Jet Master','Jet Master 88 TON',88,'Toggle','Hidrolik Non-Servo','380 x 380','530 X 530',150,350,460,22.00,8.00,30.00,1,1,1996,'Jet Master Jet Master 88 TON 88T',NULL,1,NULL,'2026-09-07 13:24:31','2026-09-07 13:24:31',NULL),(99,59,'plant_1_cikarang','41000000233',1,NULL,'Toshiba','100 TON',100,'Toggle','Elektrik Servo','460 x 410','660 x 610',180,180,350,1.00,1.00,1.00,1,1,2005,'Toshiba 100 TON 100T',NULL,1,NULL,'2026-09-07 13:25:36','2026-09-07 13:25:36',NULL),(100,60,'plant_1_cikarang','43000000123',1,NULL,'Toshiba','EC-30S (i0.4A)',30,'Toggle','Elektrik Servo','320 x 290','440 x 420',150,330,230,1.00,1.00,1.00,13,16,2015,'Toshiba EC-30S (i0.4A) 30T',NULL,1,NULL,'2026-09-07 13:26:41','2026-09-09 11:49:09',NULL);
/*!40000 ALTER TABLE `machine_names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `machine_run_days`
--

DROP TABLE IF EXISTS `machine_run_days`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `machine_run_days` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `machine_number` smallint(5) unsigned NOT NULL,
  `run_date` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'running',
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `machine_run_days_machine_number_run_date_unique` (`machine_number`,`run_date`),
  KEY `machine_run_days_user_id_foreign` (`user_id`),
  CONSTRAINT `machine_run_days_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `machine_run_days`
--

LOCK TABLES `machine_run_days` WRITE;
/*!40000 ALTER TABLE `machine_run_days` DISABLE KEYS */;
/*!40000 ALTER TABLE `machine_run_days` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maintenance_reports`
--

DROP TABLE IF EXISTS `maintenance_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maintenance_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `report_date` date NOT NULL,
  `machine_number` tinyint(3) unsigned NOT NULL,
  `machine_name_id` bigint(20) unsigned DEFAULT NULL,
  `machine_name_text` varchar(255) DEFAULT NULL,
  `damage_category_id` bigint(20) unsigned NOT NULL,
  `damage_category_other` varchar(255) DEFAULT NULL,
  `mold_assy_utility` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `pic_name` varchar(255) NOT NULL,
  `work_shift` enum('shift_1','shift_2','shift_3') NOT NULL,
  `repair_started_at` datetime NOT NULL,
  `repair_finished_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `masalah` text DEFAULT NULL,
  `analisa_penyebab_kerusakan` text DEFAULT NULL,
  `damage_cause_category` varchar(64) DEFAULT NULL,
  `line_stop_mesin` tinyint(1) DEFAULT NULL,
  `sub_damage_category_id` bigint(20) unsigned DEFAULT NULL,
  `tindakan_perbaikan` text DEFAULT NULL,
  `tindakan_perbaikan_2` text DEFAULT NULL,
  `evidence_path` varchar(255) DEFAULT NULL,
  `penggunaan_sparepart` text DEFAULT NULL,
  `sparepart_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sparepart_items`)),
  `yang_mengerjakan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`yang_mengerjakan`)),
  `ada_op` tinyint(1) NOT NULL DEFAULT 0,
  `status_perbaikan` varchar(255) NOT NULL DEFAULT 'open',
  `injection_approved_by` bigint(20) unsigned DEFAULT NULL,
  `quality_approved_by` bigint(20) unsigned DEFAULT NULL,
  `maintenance_approved_by` bigint(20) unsigned DEFAULT NULL,
  `step2_completed_at` timestamp NULL DEFAULT NULL,
  `operator_report_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `maintenance_reports_operator_report_id_unique` (`operator_report_id`),
  KEY `maintenance_reports_machine_name_id_foreign` (`machine_name_id`),
  KEY `maintenance_reports_damage_category_id_foreign` (`damage_category_id`),
  KEY `maintenance_reports_user_id_foreign` (`user_id`),
  KEY `maintenance_reports_machine_number_report_date_index` (`machine_number`,`report_date`),
  KEY `maintenance_reports_sub_damage_category_id_foreign` (`sub_damage_category_id`),
  KEY `maintenance_reports_injection_approved_by_foreign` (`injection_approved_by`),
  KEY `maintenance_reports_quality_approved_by_foreign` (`quality_approved_by`),
  KEY `maintenance_reports_maintenance_approved_by_foreign` (`maintenance_approved_by`),
  CONSTRAINT `maintenance_reports_damage_category_id_foreign` FOREIGN KEY (`damage_category_id`) REFERENCES `damage_categories` (`id`),
  CONSTRAINT `maintenance_reports_injection_approved_by_foreign` FOREIGN KEY (`injection_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `maintenance_reports_machine_name_id_foreign` FOREIGN KEY (`machine_name_id`) REFERENCES `machine_names` (`id`) ON DELETE SET NULL,
  CONSTRAINT `maintenance_reports_maintenance_approved_by_foreign` FOREIGN KEY (`maintenance_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `maintenance_reports_operator_report_id_foreign` FOREIGN KEY (`operator_report_id`) REFERENCES `operator_reports` (`id`) ON DELETE SET NULL,
  CONSTRAINT `maintenance_reports_quality_approved_by_foreign` FOREIGN KEY (`quality_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `maintenance_reports_sub_damage_category_id_foreign` FOREIGN KEY (`sub_damage_category_id`) REFERENCES `damage_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `maintenance_reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maintenance_reports`
--

LOCK TABLES `maintenance_reports` WRITE;
/*!40000 ALTER TABLE `maintenance_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `maintenance_reports` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_05_25_135621_add_role_to_users_table',1),(5,'2026_05_25_135621_create_machine_names_table',1),(6,'2026_05_25_135622_create_damage_categories_table',1),(7,'2026_05_25_135623_create_maintenance_reports_table',1),(8,'2026_05_25_141917_remove_code_from_damage_categories_table',2),(9,'2026_05_25_144343_add_step2_fields_to_maintenance_reports_table',2),(10,'2026_05_25_200000_rename_email_to_npk_on_users_table',2),(11,'2026_06_12_000001_add_fields_to_machine_names_table',2),(12,'2026_06_12_000002_add_spec_fields_to_machine_names_table',2),(13,'2026_06_12_000003_add_asset_and_photos_to_machine_names_table',2),(14,'2026_08_18_000001_create_preventive_reports_table',3),(15,'2026_08_20_000001_create_operator_reports_table',4),(16,'2026_08_20_000002_add_claim_fields_to_operator_reports',5),(17,'2026_08_20_000003_add_status_to_operator_reports',6),(18,'2026_08_28_000001_create_technicians_table',7),(19,'2026_08_28_000002_add_claimed_technician_id_to_operator_reports',7),(20,'2026_09_02_000001_create_preventive_conveyor_reports_table',8),(21,'2026_09_02_000002_add_category_to_preventive_reports_table',9),(22,'2026_09_02_000003_add_readings_to_preventive_reports_table',10),(23,'2026_09_03_000001_create_finding_recaps_tables',11),(24,'2026_09_03_000001_create_robots_table',12),(25,'2026_09_03_000002_add_robot_to_preventive_reports_table',12),(26,'2026_09_03_000003_add_equipment_number_to_preventive_reports_table',13),(27,'2026_09_07_000001_create_preventive_checklist_items_table',14),(28,'2026_09_09_000001_create_preventive_schedules_table',15),(29,'2026_09_09_000002_add_pic_name_to_preventive_schedules_table',16),(30,'2026_09_10_000001_create_equipments_table',17),(31,'2026_09_10_000002_add_equipment_id_to_preventive_reports_table',17),(32,'2026_10_16_000001_remove_quality_users',18),(33,'2026_10_16_000002_add_damage_cause_category_to_maintenance_reports',19),(34,'2026_10_16_000003_add_evidence_paths_to_preventive_reports',20),(35,'2026_10_16_000004_add_sparepart_items_to_maintenance_reports',21),(36,'2026_10_19_000001_add_status_to_preventive_reports',22),(37,'2026_10_23_100000_create_preventive_categories_table',23),(38,'2026_10_23_110000_add_severity_conditions_to_machine_names',24),(39,'2026_10_23_120000_add_plant_to_machine_names',25),(40,'2026_10_23_130000_add_equipment_specification_and_area',26),(41,'2026_10_23_140000_add_yang_mengerjakan_to_maintenance_reports',27),(42,'2026_10_23_150000_add_line_stop_and_sub_damage_to_maintenance_reports',28),(43,'2026_10_23_160000_create_machine_run_days_table',29),(44,'2026_10_23_170000_add_team_members_to_preventive_reports',30),(45,'2026_10_27_100000_add_sparepart_to_preventive_reports',31),(46,'2026_10_27_140000_add_approval_actors_to_reports',32),(47,'2026_10_28_000001_add_item_remarks_to_preventive_reports',33),(48,'2026_10_28_100000_create_molds_table',34),(49,'2026_10_29_150000_add_technician_shift_and_preventive_jobs',35),(50,'2026_11_03_100000_add_status_to_machine_run_days_table',36),(51,'2026_11_03_111800_add_off_duty_to_technicians_table',37),(52,'2026_11_03_112400_add_overtime_to_technicians_table',38),(53,'2026_11_03_120000_create_layout_cluster_names_table',39),(54,'2026_11_03_132500_create_area_operator_reports_table',40),(55,'2026_11_03_140500_make_machine_numbers_unique_per_plant',41),(56,'2026_11_05_100000_add_quality_approved_by_to_preventive_reports',42);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `molds`
--

DROP TABLE IF EXISTS `molds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `molds` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `customer` varchar(150) NOT NULL,
  `dimension` varchar(80) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `molds`
--

LOCK TABLES `molds` WRITE;
/*!40000 ALTER TABLE `molds` DISABLE KEYS */;
/*!40000 ALTER TABLE `molds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `operator_reports`
--

DROP TABLE IF EXISTS `operator_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `operator_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `operator_name` varchar(255) NOT NULL,
  `npk` varchar(50) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `machine_name_id` bigint(20) unsigned DEFAULT NULL,
  `machine_number` tinyint(3) unsigned NOT NULL,
  `stop_date` date NOT NULL,
  `stop_time` time NOT NULL,
  `problem` text NOT NULL,
  `chronology` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `claimed_by` bigint(20) unsigned DEFAULT NULL,
  `claimed_technician_id` bigint(20) unsigned DEFAULT NULL,
  `claimed_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'open',
  PRIMARY KEY (`id`),
  KEY `operator_reports_machine_name_id_foreign` (`machine_name_id`),
  KEY `operator_reports_machine_number_stop_date_index` (`machine_number`,`stop_date`),
  KEY `operator_reports_claimed_by_foreign` (`claimed_by`),
  KEY `operator_reports_claimed_technician_id_foreign` (`claimed_technician_id`),
  CONSTRAINT `operator_reports_claimed_by_foreign` FOREIGN KEY (`claimed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `operator_reports_claimed_technician_id_foreign` FOREIGN KEY (`claimed_technician_id`) REFERENCES `technicians` (`id`) ON DELETE SET NULL,
  CONSTRAINT `operator_reports_machine_name_id_foreign` FOREIGN KEY (`machine_name_id`) REFERENCES `machine_names` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operator_reports`
--

LOCK TABLES `operator_reports` WRITE;
/*!40000 ALTER TABLE `operator_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `operator_reports` ENABLE KEYS */;
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
-- Table structure for table `preventive_categories`
--

DROP TABLE IF EXISTS `preventive_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `preventive_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL,
  `label` varchar(150) NOT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `preventive_categories_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preventive_categories`
--

LOCK TABLES `preventive_categories` WRITE;
/*!40000 ALTER TABLE `preventive_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `preventive_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `preventive_checklist_items`
--

DROP TABLE IF EXISTS `preventive_checklist_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `preventive_checklist_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(30) NOT NULL,
  `group_key` varchar(80) NOT NULL,
  `group_label` varchar(150) NOT NULL,
  `item_type` varchar(30) NOT NULL DEFAULT 'condition',
  `layout` varchar(30) DEFAULT NULL,
  `item_key` varchar(80) NOT NULL,
  `label` varchar(255) NOT NULL,
  `standard` varchar(255) DEFAULT NULL,
  `has_input` tinyint(1) NOT NULL DEFAULT 0,
  `input_placeholder` varchar(100) DEFAULT NULL,
  `input_suffix` varchar(30) DEFAULT NULL,
  `group_sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `preventive_checklist_items_category_item_key_unique` (`category`,`item_key`),
  KEY `pci_category_active_idx` (`category`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preventive_checklist_items`
--

LOCK TABLES `preventive_checklist_items` WRITE;
/*!40000 ALTER TABLE `preventive_checklist_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `preventive_checklist_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `preventive_conveyor_reports`
--

DROP TABLE IF EXISTS `preventive_conveyor_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `preventive_conveyor_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `pic_name` varchar(255) NOT NULL,
  `conveyor_name` varchar(255) NOT NULL,
  `work_date` date NOT NULL,
  `work_shift` enum('shift_1','shift_2','shift_3') NOT NULL,
  `team_members` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`team_members`)),
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `item_remarks` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`item_remarks`)),
  `notes` text DEFAULT NULL,
  `penggunaan_sparepart` text DEFAULT NULL,
  `sparepart_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sparepart_items`)),
  `evidence_paths` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`evidence_paths`)),
  `result` varchar(255) NOT NULL DEFAULT 'ok',
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `preventive_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `pic_name` varchar(255) NOT NULL,
  `machine_name_id` bigint(20) unsigned DEFAULT NULL,
  `machine_number` tinyint(3) unsigned NOT NULL,
  `equipment_number` varchar(255) DEFAULT NULL,
  `equipment_id` bigint(20) unsigned DEFAULT NULL,
  `equipment_type` varchar(150) DEFAULT NULL,
  `robot_id` bigint(20) unsigned DEFAULT NULL,
  `robot_number` varchar(255) DEFAULT NULL,
  `robot_type` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `work_date` date NOT NULL,
  `work_shift` enum('shift_1','shift_2','shift_3') NOT NULL,
  `team_members` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`team_members`)),
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `readings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `item_remarks` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`item_remarks`)),
  `notes` text DEFAULT NULL,
  `penggunaan_sparepart` text DEFAULT NULL,
  `sparepart_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sparepart_items`)),
  `evidence_paths` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`evidence_paths`)),
  `result` varchar(255) NOT NULL DEFAULT 'ok',
  `status` varchar(255) NOT NULL DEFAULT 'close_by_technician',
  `injection_approved_by` bigint(20) unsigned DEFAULT NULL,
  `quality_approved_by` bigint(20) unsigned DEFAULT NULL,
  `maintenance_approved_by` bigint(20) unsigned DEFAULT NULL,
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
  KEY `preventive_reports_injection_approved_by_foreign` (`injection_approved_by`),
  KEY `preventive_reports_maintenance_approved_by_foreign` (`maintenance_approved_by`),
  KEY `preventive_reports_quality_approved_by_foreign` (`quality_approved_by`),
  CONSTRAINT `preventive_reports_equipment_id_foreign` FOREIGN KEY (`equipment_id`) REFERENCES `equipments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `preventive_reports_injection_approved_by_foreign` FOREIGN KEY (`injection_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `preventive_reports_machine_name_id_foreign` FOREIGN KEY (`machine_name_id`) REFERENCES `machine_names` (`id`) ON DELETE SET NULL,
  CONSTRAINT `preventive_reports_maintenance_approved_by_foreign` FOREIGN KEY (`maintenance_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `preventive_reports_quality_approved_by_foreign` FOREIGN KEY (`quality_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `preventive_reports_robot_id_foreign` FOREIGN KEY (`robot_id`) REFERENCES `robots` (`id`) ON DELETE SET NULL,
  CONSTRAINT `preventive_reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `preventive_reports_chk_1` CHECK (json_valid(`items`)),
  CONSTRAINT `preventive_reports_chk_2` CHECK (json_valid(`readings`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preventive_reports`
--

LOCK TABLES `preventive_reports` WRITE;
/*!40000 ALTER TABLE `preventive_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `preventive_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `preventive_schedules`
--

DROP TABLE IF EXISTS `preventive_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `preventive_schedules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `machine_name_id` bigint(20) unsigned DEFAULT NULL,
  `machine_number` smallint(5) unsigned NOT NULL,
  `machine_name` varchar(255) NOT NULL,
  `machine_type` varchar(255) DEFAULT NULL,
  `pic_name` varchar(255) DEFAULT NULL,
  `plan_date` date NOT NULL,
  `preventive_type` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `preventive_schedules_user_id_foreign` (`user_id`),
  KEY `preventive_schedules_machine_name_id_foreign` (`machine_name_id`),
  KEY `preventive_schedules_plan_date_index` (`plan_date`),
  KEY `preventive_schedules_machine_number_plan_date_index` (`machine_number`,`plan_date`),
  CONSTRAINT `preventive_schedules_machine_name_id_foreign` FOREIGN KEY (`machine_name_id`) REFERENCES `machine_names` (`id`) ON DELETE SET NULL,
  CONSTRAINT `preventive_schedules_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preventive_schedules`
--

LOCK TABLES `preventive_schedules` WRITE;
/*!40000 ALTER TABLE `preventive_schedules` DISABLE KEYS */;
/*!40000 ALTER TABLE `preventive_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `robots`
--

DROP TABLE IF EXISTS `robots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `robots` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `robot_number` varchar(50) NOT NULL,
  `robot_type` varchar(150) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `robots_robot_number_unique` (`robot_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `robots`
--

LOCK TABLES `robots` WRITE;
/*!40000 ALTER TABLE `robots` DISABLE KEYS */;
/*!40000 ALTER TABLE `robots` ENABLE KEYS */;
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
INSERT INTO `sessions` VALUES ('2pEWOIOS388rjuqGpp4pcqLpQJ1NMkzjzrLFeFPz',3,'192.168.52.213','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiM1o1T2x1YXhWWTFaZVFvbldPZlFxbHNKS3BCaW9jWTl2aXJvdFc0dCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQ4OiJodHRwOi8vZS1tYWludGVuYW5jZS5kcHBsYXN0LmNvbS9hZG1pbi9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6MTU6ImFkbWluLmRhc2hib2FyZCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7fQ==',1793934633);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `technician_preventive_jobs`
--

DROP TABLE IF EXISTS `technician_preventive_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `technician_preventive_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `technician_id` bigint(20) unsigned NOT NULL,
  `machine_number` int(10) unsigned DEFAULT NULL,
  `category` varchar(20) NOT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `technician_preventive_jobs_technician_id_foreign` (`technician_id`),
  CONSTRAINT `technician_preventive_jobs_technician_id_foreign` FOREIGN KEY (`technician_id`) REFERENCES `technicians` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `technician_preventive_jobs`
--

LOCK TABLES `technician_preventive_jobs` WRITE;
/*!40000 ALTER TABLE `technician_preventive_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `technician_preventive_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `technicians`
--

DROP TABLE IF EXISTS `technicians`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `technicians` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `npk` varchar(50) NOT NULL,
  `pin` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `shift` tinyint(3) unsigned DEFAULT NULL,
  `off_duty` tinyint(1) NOT NULL DEFAULT 0,
  `overtime` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `technicians_npk_unique` (`npk`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `technicians`
--

LOCK TABLES `technicians` WRITE;
/*!40000 ALTER TABLE `technicians` DISABLE KEYS */;
INSERT INTO `technicians` VALUES (1,'Khasanudin','20161396','$2y$12$028puS0Yamy7UqFmPZ.RLOuUpKK2xbN5todXnMPkISEjCviYPj/Da',1,'2026-08-28 03:37:37','2026-10-29 15:10:21',2,0,0),(2,'Welly','2016397','$2y$12$ux2jElQ689K/AOPpRuQ77eDFdfPob4N/bccbFY0LkzTY5KQWFOTZm',1,'2026-08-28 03:37:37','2026-11-04 09:19:56',2,0,0),(3,'Erickson','20191732','$2y$12$.mWhIPvqIlRQ9s8QYr8MPOjm4WTJ9ajkGppgk85ladOev7.fKFNX2',1,'2026-09-03 02:11:15','2026-10-29 15:10:21',1,0,0),(4,'Adit','20262093','$2y$12$BG32x4GW0WubiY3kppOMseLJGrflTKXVmxTxXueOw16bcChGDld0u',1,'2026-09-21 16:15:35','2026-10-29 15:10:21',1,0,0),(5,'Warno','20141075','$2y$12$m3A.k..NC.gMVLbs97NbTu.zDLrpIaS7BPsnBUBB4JPt4wwlKEGKC',1,'2026-09-21 16:15:54','2026-11-04 09:19:57',1,0,1),(6,'Yitno Unix','0111','$2y$12$.OYP2PQc0h2z0xwyJyYRz.zzYZ6bXxjvh7h3AZ/2nrYRRtSSKmmlq',1,'2026-09-21 16:16:49','2026-11-03 15:11:13',2,1,0),(7,'Doni Unix','0222','$2y$12$5bnf/eTWu0rXJShVQJPa1Ob5qkokxS1z3JE1BbA9yjkMh.SQ9PHOW',1,'2026-09-21 16:17:03','2026-11-03 11:09:57',3,0,0),(8,'Rapli Unix','0333','$2y$12$tb/0g.j0tniUq1qLyJVl8ewpzvrY74WTSM2AoYDaETkmZ.09xdki2',1,'2026-09-21 16:17:25','2026-10-29 15:10:21',3,0,0),(9,'Gibson Unix','0444','$2y$12$LXM3LTO54KSIsjFDeFPCSefrNELm51tTYCucSNJ5cl4DaFKnUkeWG',1,'2026-09-21 16:17:54','2026-11-03 15:11:36',2,0,0),(10,'Nur ATS','1222','$2y$12$bSyp0TZBZ2qyEXT.95NvyuZEND04okl9n/MNwdFZwpfDmbl41YrrO',1,'2026-09-21 16:18:14','2026-10-29 15:10:22',2,0,0),(11,'Nurul Robot','2111','$2y$12$/vPeSmZDyW.Gh8eLSnE.F.1554hXAoPVu7T0rsrT/QzOvwXuDtCN2',1,'2026-09-21 16:18:33','2026-10-29 15:10:22',3,0,0),(12,'Rozak JSW','3111','$2y$12$jhU5xZ7IzikcB3a65D94FeCWc4BEHsc6ZW.7OYlC1d1VE34x..9fC',1,'2026-09-21 16:18:50','2026-11-03 15:11:13',2,1,0);
/*!40000 ALTER TABLE `technicians` ENABLE KEYS */;
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
  `npk` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'technician',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_npk_unique` (`npk`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (3,'Prayudi','20221913','admin',NULL,'$2y$12$jOh/2Qmgtvc0KiFRJjcN2.KhMdoRv/o45wdXPs0uyjcihEpWuD17a',NULL,'2026-05-26 04:03:18','2026-11-03 15:06:56'),(7,'Prayudi Injection','20221916','produksi_injection',NULL,'$2y$12$lCMUrsgZFlUS8IbR1C5e3.emLSh4jr53VpThxQ/03WNsB9xJZTWm.',NULL,'2026-08-28 03:00:02','2026-11-03 11:17:05'),(13,'Teknisi Maintenance','20260109','technician',NULL,'$2y$12$t4kLk.Qa8bfWr/hZD/Llf.qvaP0oKvvOaZBFC5w0oC3QXi02rNRGC',NULL,'2026-08-28 04:15:53','2026-11-04 09:27:20'),(14,'Apfia Niken T','20201799','admin',NULL,'$2y$12$.yoho95e70uf0BF8wGfSz.v1t.xD8spyqOANWxWEgvE365fuU.fW2',NULL,'2026-09-03 02:40:00','2026-11-06 08:47:40'),(15,'Jumihadi','20030028','admin',NULL,'$2y$12$fC5K.Tg4LxVI685kDce5DOAvAYNAfpsfBqUVk4TNwTdrLdTVZL93K',NULL,'2026-09-03 11:33:04','2026-10-28 16:15:42'),(16,'Rangga E. Hutomo','20242014','admin',NULL,'$2y$12$3OVHh7f0ooqRiJmI21i4S.qG8DAy7038qM5PcrIkZSTvLQQ/z.zFO',NULL,'2026-09-08 08:03:14','2026-09-21 16:19:41'),(17,'Erickson H.','20191732','admin',NULL,'$2y$12$unhLqW9WiQPvK1opqcGDKOToCEIPctCQTg6W7i1n8kTanMAybfu5C',NULL,'2026-09-08 08:03:47','2026-09-21 16:19:20'),(19,'Injection','20260209','produksi_injection',NULL,'$2y$12$lEYERKeP529lObKoQVuN0.qpSLMJq3WfR.K3lZN7tpeUuo3lQ6JGm',NULL,'2026-10-16 17:01:08','2026-10-28 10:12:58'),(20,'Quality Demo','00000','quality','2026-10-16 17:12:38','$2y$12$HMrg8HbPYEAoHl5V1zpuLeKdVRenmpDkTl52db0O5uu5q1jxP7tGS',NULL,'2026-10-16 17:12:38','2026-10-23 10:40:56'),(21,'Management Demo','500001','management','2026-10-19 10:16:47','$2y$12$JzEDJxEYIeREGsDFZWgBVObTO6YuEgqyQcUMoQ.ijAtfIEpE4IaKq',NULL,'2026-10-19 10:16:47','2026-11-06 08:48:22'),(22,'Administrator','100001','admin','2026-10-23 12:26:25','$2y$12$1j4.irQ.ebY3vq9EtxblZesKADEzXl.5odJITU8Fbgt1t43gUOF9m',NULL,'2026-10-23 12:26:24','2026-10-23 12:26:25'),(23,'Teknisi Maintenance','200001','technician','2026-10-23 12:26:25','$2y$12$hFINZgV90/Xc4oUDTTl8ZO/R5wTz3IvMaDeMo/uCvroICr.XKMMQ2',NULL,'2026-10-23 12:26:25','2026-10-23 12:26:25'),(24,'Produksi Injection Demo','400001','produksi_injection','2026-10-23 12:26:25','$2y$12$o6Kw9rc66Zti2lWBVUYwx.fn98UU3RZzO/q/48WEmT.djDyy6i/kS',NULL,'2026-10-23 12:26:25','2026-10-23 12:26:25'),(25,'Quality Demo','300001','quality','2026-10-23 12:26:26','$2y$12$49z.hyaZqqC2iqejKrrMz.Skbeq37Tff9psWaNx1VIM.J3aMqUh8O',NULL,'2026-10-23 12:26:26','2026-10-23 12:26:26'),(26,'PPIC','600001','ppic',NULL,'$2y$12$F.tOgb8gI94H7jonZwrh7OaYbe2ZkfBGpUedt0egSuCA11aWlq0Uu',NULL,'2026-10-23 14:15:47','2026-10-23 14:35:18'),(27,'Quality','20260309','quality',NULL,'$2y$12$25LwubxKHiQzgr1Ns05HGOIeBFeh7piecHHpYnqm/A.Iliz/aXZki',NULL,'2026-10-27 14:26:35','2026-10-27 14:26:59'),(28,'Operator Yudi','20272027','operator',NULL,'$2y$12$jll5rqHwkbGmdMIrVRcxrudfA6ZuyzbjzlGdsCyG1NER/vHrBOsvu',NULL,'2026-10-27 15:15:41','2026-11-03 15:03:44');
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

-- Dump completed on 2026-11-06 10:16:50

-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: ccr_db
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
  `report_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(60) NOT NULL,
  `subject_label` varchar(120) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `activity_logs_report_id_foreign` (`report_id`),
  KEY `activity_logs_created_at_index` (`created_at`),
  KEY `activity_logs_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `activity_logs_action_index` (`action`),
  CONSTRAINT `activity_logs_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE SET NULL,
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (1,1,1,'report_draft_created','SSC-8316','Menyimpan draft laporan SSC-8316','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-10 21:28:25'),(2,1,2,'report_draft_created','SSC-9391','Menyimpan draft laporan SSC-9391','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-10 21:32:07'),(3,NULL,3,'report_draft_created','SSC-1464','Menyimpan draft laporan SSC-1464','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-10 22:21:05'),(4,NULL,3,'report_exported_excel','SSC-1464','Mengunduh Excel laporan SSC-1464','{\"part_name\":\"OUTPUT SHAFT\"}','2026-11-10 23:43:39'),(5,NULL,NULL,'profile_updated',NULL,'Memperbarui data profil akun (QC Leader)','{\"old_name\":\"QC Leader\",\"name\":\"QC Leader\",\"email\":\"qc@sscworks.com\"}','2026-11-10 23:45:43'),(6,NULL,NULL,'logout',NULL,'Keluar dari sistem QCMS',NULL,'2026-11-10 23:45:55'),(7,1,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-10 23:46:06'),(8,1,NULL,'logout',NULL,'Keluar dari sistem QCMS',NULL,'2026-11-10 23:49:09'),(9,NULL,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-10 23:49:19'),(10,NULL,NULL,'profile_updated',NULL,'Menghapus foto profil akun (QC Leader) dan kembali ke inisial nama','{\"user\":\"qc@sscworks.com\"}','2026-11-10 23:49:48'),(11,NULL,NULL,'preferences_updated',NULL,'Pengaturan tema dan bahasa berhasil diperbarui!','{\"theme\":\"dark\",\"locale\":\"id\"}','2026-11-11 00:29:59'),(12,NULL,NULL,'preferences_updated',NULL,'Pengaturan tema dan bahasa berhasil diperbarui!','{\"theme\":\"dark\",\"locale\":\"id\"}','2026-11-11 00:30:15'),(13,NULL,NULL,'profile_updated',NULL,'Memperbarui foto profil akun (QC Leader)','{\"user\":\"qc@sscworks.com\"}','2026-11-11 01:20:45'),(14,NULL,NULL,'logout',NULL,'Keluar dari sistem QCMS',NULL,'2026-11-11 01:20:56'),(15,1,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-11 01:21:11'),(16,1,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-11 05:27:56'),(17,1,NULL,'logout',NULL,'Keluar dari sistem QCMS',NULL,'2026-11-11 05:31:10'),(18,1,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-11 05:31:23'),(19,1,NULL,'logout',NULL,'Keluar dari sistem QCMS',NULL,'2026-11-11 05:31:29'),(20,NULL,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-11 05:31:43'),(21,NULL,NULL,'logout',NULL,'Keluar dari sistem QCMS',NULL,'2026-11-11 05:33:00'),(22,1,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-11 05:33:12'),(23,1,NULL,'preferences_updated',NULL,'Theme and language preferences updated successfully!','{\"theme\":\"light\",\"locale\":\"en\"}','2026-11-11 06:22:04'),(24,1,NULL,'profile_updated',NULL,'Memperbarui foto profil akun (QC Inspector)','{\"user\":\"admin@qcms.com\"}','2026-11-11 06:52:35'),(25,1,NULL,'profile_updated',NULL,'Menghapus foto profil akun (QC Inspector) dan kembali ke inisial nama','{\"user\":\"admin@qcms.com\"}','2026-11-11 06:52:53'),(26,1,NULL,'profile_updated',NULL,'Memperbarui foto profil akun (QC Inspector)','{\"user\":\"admin@qcms.com\"}','2026-11-11 07:00:07'),(27,1,NULL,'logout',NULL,'Keluar dari sistem QCMS',NULL,'2026-11-11 07:00:19'),(28,1,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-11 07:00:31'),(29,1,NULL,'logout',NULL,'Keluar dari sistem QCMS',NULL,'2026-11-11 07:00:49'),(30,NULL,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-11 07:00:59'),(31,NULL,NULL,'profile_updated',NULL,'Menghapus foto profil akun (QC Leader) dan kembali ke inisial nama','{\"user\":\"qc@sscworks.com\"}','2026-11-11 07:01:14'),(32,NULL,NULL,'logout',NULL,'Keluar dari sistem QCMS',NULL,'2026-11-11 07:01:19'),(33,1,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-11 07:01:28'),(34,1,NULL,'logout',NULL,'Keluar dari sistem QCMS',NULL,'2026-11-11 07:01:46'),(35,1,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-11 07:23:04'),(36,1,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-11 17:22:09'),(37,1,NULL,'logout',NULL,'Keluar dari sistem QCMS',NULL,'2026-11-11 17:22:37'),(38,1,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-11 17:25:46'),(39,1,NULL,'preferences_updated',NULL,'Pengaturan tema dan bahasa berhasil diperbarui!','{\"theme\":\"light\",\"locale\":\"id\"}','2026-11-11 17:26:38'),(40,1,NULL,'profile_updated',NULL,'Menghapus foto profil akun (QC Inspector) dan kembali ke inisial nama','{\"user\":\"admin@qcms.com\"}','2026-11-11 18:04:39'),(41,1,NULL,'profile_updated',NULL,'Memperbarui foto profil akun (QC Inspector)','{\"user\":\"admin@qcms.com\"}','2026-11-11 18:16:59'),(42,1,NULL,'logout',NULL,'Keluar dari sistem QCMS',NULL,'2026-11-11 18:17:11'),(43,NULL,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-11 18:17:21'),(44,NULL,NULL,'profile_updated',NULL,'Memperbarui foto profil akun (QC Leader)','{\"user\":\"qc@sscworks.com\"}','2026-11-11 18:18:29'),(45,NULL,NULL,'preferences_updated',NULL,'Pengaturan tema dan bahasa berhasil diperbarui!','{\"theme\":\"light\",\"locale\":\"id\"}','2026-11-11 18:18:54'),(46,NULL,NULL,'logout',NULL,'Keluar dari sistem QCMS',NULL,'2026-11-11 18:19:34'),(47,1,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-11 18:19:47'),(48,1,NULL,'user_deleted','QC Leader','Menghapus akun QC Leader','{\"subject\":\"QC Leader\"}','2026-11-11 18:22:34'),(49,1,NULL,'user_created','QC Leader','Menambahkan akun baru QC Leader (QC Leader)','{\"subject\":\"QC Leader\",\"role\":\"qc_leader\"}','2026-11-11 18:23:07'),(50,1,NULL,'logout',NULL,'Keluar dari sistem QCMS',NULL,'2026-11-11 18:23:16'),(51,NULL,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-11 18:23:25'),(52,NULL,NULL,'logout',NULL,'Keluar dari sistem QCMS',NULL,'2026-11-11 19:01:26'),(53,1,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-11 19:01:40'),(54,1,NULL,'preferences_updated',NULL,'Pengaturan tema dan bahasa berhasil diperbarui!','{\"theme\":\"dark\",\"locale\":\"id\"}','2026-11-11 19:01:52'),(55,1,NULL,'preferences_updated',NULL,'Pengaturan tema dan bahasa berhasil diperbarui!','{\"theme\":\"light\",\"locale\":\"id\"}','2026-11-11 19:22:59'),(56,1,NULL,'user_deleted','QC Leader','Menghapus akun QC Leader','{\"subject\":\"QC Leader\"}','2026-11-11 20:15:50'),(57,1,NULL,'user_created','QC Leader','Menambahkan akun baru QC Leader (QC Leader)','{\"subject\":\"QC Leader\",\"role\":\"qc_leader\"}','2026-11-11 20:16:21'),(58,4,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-12 04:53:29'),(59,4,5,'report_draft_created','SSC-6361','Menyimpan draft laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-12 04:54:58'),(60,4,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-15 19:48:47'),(61,4,NULL,'profile_updated',NULL,'Memperbarui foto profil akun (QC Leader)','{\"user\":\"qc@sscworks.com\"}','2026-11-15 19:48:56'),(62,4,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-17 05:49:36'),(63,4,5,'report_draft_updated','SSC-6361','Memperbarui draft laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-17 05:50:11'),(64,4,5,'report_draft_updated','SSC-6361','Memperbarui draft laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-17 05:50:20'),(65,4,5,'report_draft_updated','SSC-6361','Memperbarui draft laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-17 05:50:25'),(66,4,5,'report_draft_updated','SSC-6361','Memperbarui draft laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-17 05:50:26'),(67,4,5,'report_exported_pdf','SSC-6361','Membuka cetak / PDF laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\"}','2026-11-17 05:50:26'),(68,4,5,'report_draft_updated','SSC-6361','Memperbarui draft laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-17 05:50:38'),(69,4,5,'report_exported_excel','SSC-6361','Mengunduh Excel laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\"}','2026-11-17 05:50:43'),(70,4,NULL,'logout',NULL,'Keluar dari sistem QCMS',NULL,'2026-11-17 05:52:54'),(71,4,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-17 06:30:42'),(72,4,5,'report_draft_updated','SSC-6361','Memperbarui draft laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-17 06:31:30'),(73,4,5,'report_draft_updated','SSC-6361','Memperbarui draft laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-17 06:31:37'),(74,4,5,'report_draft_updated','SSC-6361','Memperbarui draft laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-17 06:31:43'),(75,4,5,'report_draft_updated','SSC-6361','Memperbarui draft laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-17 06:32:01'),(76,4,5,'report_draft_updated','SSC-6361','Memperbarui draft laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-17 06:32:13'),(77,4,5,'report_draft_updated','SSC-6361','Memperbarui draft laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-17 06:32:34'),(78,4,5,'report_exported_excel','SSC-6361','Mengunduh Excel laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\"}','2026-11-17 06:32:38'),(79,4,5,'report_draft_updated','SSC-6361','Memperbarui draft laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-17 06:34:10'),(80,4,5,'report_exported_excel','SSC-6361','Mengunduh Excel laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\"}','2026-11-17 06:34:14'),(81,4,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-17 18:16:39'),(82,4,5,'report_draft_updated','SSC-6361','Memperbarui draft laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-17 18:17:14'),(83,4,5,'report_draft_updated','SSC-6361','Memperbarui draft laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-17 18:17:36'),(84,4,5,'report_draft_updated','SSC-6361','Memperbarui draft laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-17 18:17:55'),(85,4,5,'report_draft_updated','SSC-6361','Memperbarui draft laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-17 18:17:58'),(86,4,5,'report_draft_updated','SSC-6361','Memperbarui draft laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-17 18:18:15'),(87,4,5,'report_exported_pdf','SSC-6361','Membuka cetak / PDF laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\"}','2026-11-17 18:18:20'),(88,4,5,'report_exported_excel','SSC-6361','Mengunduh Excel laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\"}','2026-11-17 18:37:37'),(89,4,5,'report_exported_excel','SSC-6361','Mengunduh Excel laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\"}','2026-11-17 19:00:58'),(90,4,5,'report_draft_updated','SSC-6361','Memperbarui draft laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-17 19:02:23'),(91,4,5,'report_exported_excel','SSC-6361','Mengunduh Excel laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\"}','2026-11-17 19:02:25'),(92,4,NULL,'login',NULL,'Masuk ke sistem QCMS',NULL,'2026-11-17 19:26:35'),(93,4,5,'report_exported_excel','SSC-6361','Mengunduh Excel laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\"}','2026-11-17 19:26:49'),(94,4,5,'report_draft_updated','SSC-6361','Memperbarui draft laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\",\"status\":\"draft\"}','2026-11-17 19:48:09'),(95,4,5,'report_exported_excel','SSC-6361','Mengunduh Excel laporan SSC-6361','{\"part_name\":\"OUTPUT SHAFT\"}','2026-11-17 19:48:14');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
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
INSERT INTO `cache` VALUES ('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba','i:1;',1794968854),('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba:timer','i:1794968854;',1794968854),('laravel-cache-b01fd35cc175db11af0261a32ba435c1adf3901e','i:1;',1794529392),('laravel-cache-b01fd35cc175db11af0261a32ba435c1adf3901e:timer','i:1794529392;',1794529392),('laravel-cache-e7a567fd71bc5a5efc3f263736bccbb7de4755ea','i:1;',1794529621),('laravel-cache-e7a567fd71bc5a5efc3f263736bccbb7de4755ea:timer','i:1794529621;',1794529621);
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_11_02_000001_create_reports_table',1),(5,'2026_11_05_000001_add_photos_to_reports_table',1),(6,'2026_11_05_000002_add_doc_control_to_reports_table',1),(7,'2026_11_09_000001_add_role_to_users_table',1),(8,'2026_11_10_000001_add_review_workflow_to_reports_table',1),(9,'2026_11_11_000001_add_draft_and_checker_to_reports_table',1),(10,'2026_11_11_000002_create_activity_logs_table',2),(11,'2026_11_11_000003_add_avatar_to_users_table',2),(12,'2026_11_11_000003_add_preferences_to_users_table',3),(13,'2026_11_12_000001_add_last_seen_at_to_users_table',4),(14,'2026_11_12_000002_create_passkeys_table',5);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `passkeys`
--

DROP TABLE IF EXISTS `passkeys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `passkeys` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `credential_id` varchar(512) NOT NULL,
  `public_key` text NOT NULL,
  `algorithm` int(11) NOT NULL,
  `sign_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `transports` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`transports`)),
  `rp_id` varchar(255) DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `passkeys_credential_id_unique` (`credential_id`),
  KEY `passkeys_user_id_foreign` (`user_id`),
  CONSTRAINT `passkeys_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `passkeys`
--

LOCK TABLES `passkeys` WRITE;
/*!40000 ALTER TABLE `passkeys` DISABLE KEYS */;
/*!40000 ALTER TABLE `passkeys` ENABLE KEYS */;
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
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `checker_id` bigint(20) unsigned DEFAULT NULL,
  `reviewer_id` bigint(20) unsigned DEFAULT NULL,
  `ssc_id` varchar(255) NOT NULL,
  `report_no` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `customer` varchar(255) DEFAULT NULL,
  `part_name` varchar(255) NOT NULL DEFAULT 'OUTPUT SHAFT',
  `inspection_type` enum('Pre','Post') NOT NULL DEFAULT 'Pre',
  `job_no` varchar(255) NOT NULL DEFAULT '-',
  `model_unit` varchar(255) NOT NULL DEFAULT 'HT051',
  `quote_no` varchar(255) NOT NULL DEFAULT '-',
  `part_no` varchar(255) NOT NULL DEFAULT '-',
  `doc_no` varchar(255) NOT NULL DEFAULT 'FORM-QC-002',
  `rev` varchar(255) NOT NULL DEFAULT '04',
  `responsibility` varchar(255) NOT NULL DEFAULT 'QC Inspector',
  `effective_date` date DEFAULT NULL,
  `page` varchar(255) NOT NULL DEFAULT '1',
  `sections` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sections`)),
  `tir` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tir`)),
  `recommendations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`recommendations`)),
  `signatures` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`signatures`)),
  `photos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`photos`)),
  `has_rejects` tinyint(1) NOT NULL DEFAULT 0,
  `review_status` enum('draft','waiting_review','reviewed') NOT NULL DEFAULT 'waiting_review',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reports_ssc_id_unique` (`ssc_id`),
  KEY `reports_created_by_foreign` (`created_by`),
  KEY `reports_reviewer_id_foreign` (`reviewer_id`),
  KEY `reports_reviewed_by_foreign` (`reviewed_by`),
  KEY `reports_checker_id_foreign` (`checker_id`),
  CONSTRAINT `reports_checker_id_foreign` FOREIGN KEY (`checker_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reports_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reports_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reports_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
INSERT INTO `reports` VALUES (1,1,NULL,NULL,'SSC-8316',NULL,'2026-11-11',NULL,'OUTPUT SHAFT','Pre','-','HT051','-','-','FORM-QC-002','04','QC Inspector','2026-11-11','1','{\"item01\":{\"title\":\"OD Area \\\"A\\\" and OD Spline\",\"spec\":\"\\u00d879 \\u00b1 0.25\",\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\",\"remark1\":null,\"lengthResult\":null,\"penetrantRemark\":\"No indication crack at OD area \\\"A\\\" and OD spline after penetrant check\"},\"item02\":{\"title\":\"OD Area \\\"B and C\\\"\",\"itemB\":{\"spec\":\"\\u00d882 \\u00b1 0.25\",\"row1\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row2\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row3\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"}},\"itemC\":{\"spec\":\"\\u00d877 \\u00b1 0.25\",\"row1\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row2\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row3\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"}},\"remarks\":[\"Actual measurement OD area \\\"B and C\\\" in good condition\"],\"penetrantRemark\":\"No indication crack at OD Area \\\"B and C\\\" after penetrant check\"},\"item03\":{\"title\":\"OD Area \\\"D\\\"\",\"itemD\":{\"spec\":\"\\u00d8118 \\u00b1 0.5\",\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"distanceE\":{\"spec\":\"13 \\u00b1 0.5\",\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"remarks\":[\"Actual measurement OD area \\\"D\\\" in good condition\",\"Actual distance area \\\"E\\\" in good condition\"],\"penetrantRemark\":\"No indication crack at OD area \\\"D\\\" after penetrant check\"},\"item04\":{\"title\":\"OD Area \\\"F\\\"\",\"itemF\":{\"spec\":\"\\u00d8100,01 \\u00b1 0,008\",\"row1\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row2\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row3\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"}},\"distanceK\":{\"spec\":\"N\\/A\",\"result\":null,\"ar\":\"-\"},\"remarks\":[\"Actual measurement OD area \\\"F\\\" in good condition\"],\"penetrantRemark\":\"No indication crack at area OD area \\\"F\\\" after penetrant check\"},\"item05\":{\"title\":\"OD Area \\\"H\\\"\",\"itemH\":{\"spec\":\"\\u00d894 \\u00b1 0,013\",\"row1\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row2\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row3\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"}},\"distanceJ\":{\"spec\":\"N\\/A\",\"result\":null,\"ar\":\"-\"},\"remarks\":[\"Actual measurement OD area \\\"H\\\" in good condition\"],\"penetrantRemark\":\"No indication crack at area OD area \\\"H\\\" after penetrant check\"},\"item06\":{\"title\":\"OD Area \\\"G\\\" and OD Spline\",\"itemG\":{\"spec\":\"\\u00d884,2 \\u00b1 0,13\",\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"distanceI\":{\"spec\":\"N\\/A\",\"result\":null,\"ar\":\"-\"},\"remarks\":[\"Actual measurement OD area \\\"G\\\" in good condition\"],\"penetrantRemark\":\"No indication crack at area OD spline after penetrant check\"}}','{\"spec\":\"Max 0,2\",\"datum\":\"Datum at item \\\"B and E\\\"\",\"items\":[{\"item\":\"A\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"},{\"item\":\"B\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"},{\"item\":\"C\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"},{\"item\":\"D\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"},{\"item\":\"E\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"},{\"item\":\"F\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"}]}','[{\"item\":\"01\",\"remarks\":null,\"recommendation\":null}]','{\"checkedBy\":{\"name\":null,\"title\":\"QC Inspector\",\"date\":\"2026-11-11\"},\"createdBy\":{\"name\":\"QC Inspector\",\"title\":\"QC Inspector\",\"date\":\"2026-11-11\"},\"reviewedBy\":{\"name\":null,\"title\":\"Leader QC\",\"date\":\"2026-11-11\"}}','{\"drawing_dimension\":null,\"finding_component\":null,\"area_a_visual_1\":null,\"area_a_visual_2\":null,\"area_a_visual_3\":null,\"area_a_visual_4\":null,\"area_a_penet_1\":null,\"area_a_penet_2\":null,\"area_a_penet_3\":null,\"area_a_penet_4\":null,\"area_bc_visual_1\":null,\"area_bc_visual_2\":null,\"area_bc_visual_3\":null,\"area_bc_visual_4\":null,\"area_bc_penet_1\":null,\"area_bc_penet_2\":null,\"area_bc_penet_3\":null,\"area_bc_penet_4\":null,\"area_d_visual_1\":null,\"area_d_visual_2\":null,\"area_d_visual_3\":null,\"area_d_visual_4\":null,\"area_d_penet_1\":null,\"area_d_penet_2\":null,\"area_d_penet_3\":null,\"area_d_penet_4\":null,\"area_f_visual_1\":null,\"area_f_visual_2\":null,\"area_f_visual_3\":null,\"area_f_visual_4\":null,\"area_f_penet_1\":null,\"area_f_penet_2\":null,\"area_f_penet_3\":null,\"area_f_penet_4\":null,\"area_h_visual_1\":null,\"area_h_visual_2\":null,\"area_h_visual_3\":null,\"area_h_visual_4\":null,\"area_h_penet_1\":null,\"area_h_penet_2\":null,\"area_h_penet_3\":null,\"area_h_penet_4\":null,\"area_g_visual_1\":null,\"area_g_visual_2\":null,\"area_g_visual_3\":null,\"area_g_visual_4\":null,\"area_g_penet_1\":null,\"area_g_penet_2\":null,\"area_g_penet_3\":null,\"area_g_penet_4\":null,\"tir_diagram\":null}',0,'draft',NULL,NULL,NULL,'2026-11-10 21:28:25','2026-11-10 21:28:25'),(2,1,NULL,NULL,'SSC-9391',NULL,'2026-11-11',NULL,'OUTPUT SHAFT','Pre','-','HT051','-','-','FORM-QC-002','04','QC Inspector','2026-11-11','1','{\"item01\":{\"title\":\"OD Area \\\"A\\\" and OD Spline\",\"spec\":\"\\u00d879 \\u00b1 0.25\",\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\",\"remark1\":null,\"lengthResult\":null,\"penetrantRemark\":\"No indication crack at OD area \\\"A\\\" and OD spline after penetrant check\"},\"item02\":{\"title\":\"OD Area \\\"B and C\\\"\",\"itemB\":{\"spec\":\"\\u00d882 \\u00b1 0.25\",\"row1\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row2\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row3\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"}},\"itemC\":{\"spec\":\"\\u00d877 \\u00b1 0.25\",\"row1\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row2\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row3\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"}},\"remarks\":[\"Actual measurement OD area \\\"B and C\\\" in good condition\"],\"penetrantRemark\":\"No indication crack at OD Area \\\"B and C\\\" after penetrant check\"},\"item03\":{\"title\":\"OD Area \\\"D\\\"\",\"itemD\":{\"spec\":\"\\u00d8118 \\u00b1 0.5\",\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"distanceE\":{\"spec\":\"13 \\u00b1 0.5\",\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"remarks\":[\"Actual measurement OD area \\\"D\\\" in good condition\",\"Actual distance area \\\"E\\\" in good condition\"],\"penetrantRemark\":\"No indication crack at OD area \\\"D\\\" after penetrant check\"},\"item04\":{\"title\":\"OD Area \\\"F\\\"\",\"itemF\":{\"spec\":\"\\u00d8100,01 \\u00b1 0,008\",\"row1\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row2\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row3\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"}},\"distanceK\":{\"spec\":\"N\\/A\",\"result\":null,\"ar\":\"-\"},\"remarks\":[\"Actual measurement OD area \\\"F\\\" in good condition\"],\"penetrantRemark\":\"No indication crack at area OD area \\\"F\\\" after penetrant check\"},\"item05\":{\"title\":\"OD Area \\\"H\\\"\",\"itemH\":{\"spec\":\"\\u00d894 \\u00b1 0,013\",\"row1\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row2\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row3\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"}},\"distanceJ\":{\"spec\":\"N\\/A\",\"result\":null,\"ar\":\"-\"},\"remarks\":[\"Actual measurement OD area \\\"H\\\" in good condition\"],\"penetrantRemark\":\"No indication crack at area OD area \\\"H\\\" after penetrant check\"},\"item06\":{\"title\":\"OD Area \\\"G\\\" and OD Spline\",\"itemG\":{\"spec\":\"\\u00d884,2 \\u00b1 0,13\",\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"distanceI\":{\"spec\":\"N\\/A\",\"result\":null,\"ar\":\"-\"},\"remarks\":[\"Actual measurement OD area \\\"G\\\" in good condition\"],\"penetrantRemark\":\"No indication crack at area OD spline after penetrant check\"}}','{\"spec\":\"Max 0,2\",\"datum\":\"Datum at item \\\"B and E\\\"\",\"items\":[{\"item\":\"A\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"},{\"item\":\"B\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"},{\"item\":\"C\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"},{\"item\":\"D\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"},{\"item\":\"E\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"},{\"item\":\"F\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"}]}','[{\"item\":\"01\",\"remarks\":null,\"recommendation\":null}]','{\"checkedBy\":{\"name\":null,\"title\":\"QC Inspector\",\"date\":\"2026-11-11\"},\"createdBy\":{\"name\":\"QC Inspector\",\"title\":\"QC Inspector\",\"date\":\"2026-11-11\"},\"reviewedBy\":{\"name\":null,\"title\":\"Leader QC\",\"date\":\"2026-11-11\"}}','{\"drawing_dimension\":null,\"finding_component\":null,\"area_a_visual_1\":null,\"area_a_visual_2\":null,\"area_a_visual_3\":null,\"area_a_visual_4\":null,\"area_a_penet_1\":null,\"area_a_penet_2\":null,\"area_a_penet_3\":null,\"area_a_penet_4\":null,\"area_bc_visual_1\":null,\"area_bc_visual_2\":null,\"area_bc_visual_3\":null,\"area_bc_visual_4\":null,\"area_bc_penet_1\":null,\"area_bc_penet_2\":null,\"area_bc_penet_3\":null,\"area_bc_penet_4\":null,\"area_d_visual_1\":null,\"area_d_visual_2\":null,\"area_d_visual_3\":null,\"area_d_visual_4\":null,\"area_d_penet_1\":null,\"area_d_penet_2\":null,\"area_d_penet_3\":null,\"area_d_penet_4\":null,\"area_f_visual_1\":null,\"area_f_visual_2\":null,\"area_f_visual_3\":null,\"area_f_visual_4\":null,\"area_f_penet_1\":null,\"area_f_penet_2\":null,\"area_f_penet_3\":null,\"area_f_penet_4\":null,\"area_h_visual_1\":null,\"area_h_visual_2\":null,\"area_h_visual_3\":null,\"area_h_visual_4\":null,\"area_h_penet_1\":null,\"area_h_penet_2\":null,\"area_h_penet_3\":null,\"area_h_penet_4\":null,\"area_g_visual_1\":null,\"area_g_visual_2\":null,\"area_g_visual_3\":null,\"area_g_visual_4\":null,\"area_g_penet_1\":null,\"area_g_penet_2\":null,\"area_g_penet_3\":null,\"area_g_penet_4\":null,\"tir_diagram\":null}',0,'draft',NULL,NULL,NULL,'2026-11-10 21:32:07','2026-11-10 21:32:07'),(3,NULL,NULL,NULL,'SSC-1464',NULL,'2026-11-11',NULL,'OUTPUT SHAFT','Pre','-','HT051','-','-','FORM-QC-002','04','QC Inspector','2026-11-11','1','{\"item01\":{\"title\":\"OD Area \\\"A\\\" and OD Spline\",\"spec\":\"\\u00d879 \\u00b1 0.25\",\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\",\"remark1\":null,\"lengthResult\":null,\"penetrantRemark\":\"No indication crack at OD area \\\"A\\\" and OD spline after penetrant check\"},\"item02\":{\"title\":\"OD Area \\\"B and C\\\"\",\"itemB\":{\"spec\":\"\\u00d882 \\u00b1 0.25\",\"row1\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row2\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row3\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"}},\"itemC\":{\"spec\":\"\\u00d877 \\u00b1 0.25\",\"row1\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row2\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row3\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"}},\"remarks\":[\"Actual measurement OD area \\\"B and C\\\" in good condition\"],\"penetrantRemark\":\"No indication crack at OD Area \\\"B and C\\\" after penetrant check\"},\"item03\":{\"title\":\"OD Area \\\"D\\\"\",\"itemD\":{\"spec\":\"\\u00d8118 \\u00b1 0.5\",\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"distanceE\":{\"spec\":\"13 \\u00b1 0.5\",\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"remarks\":[\"Actual measurement OD area \\\"D\\\" in good condition\",\"Actual distance area \\\"E\\\" in good condition\"],\"penetrantRemark\":\"No indication crack at OD area \\\"D\\\" after penetrant check\"},\"item04\":{\"title\":\"OD Area \\\"F\\\"\",\"itemF\":{\"spec\":\"\\u00d8100,01 \\u00b1 0,008\",\"row1\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row2\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row3\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"}},\"distanceK\":{\"spec\":\"N\\/A\",\"result\":null,\"ar\":\"-\"},\"remarks\":[\"Actual measurement OD area \\\"F\\\" in good condition\"],\"penetrantRemark\":\"No indication crack at area OD area \\\"F\\\" after penetrant check\"},\"item05\":{\"title\":\"OD Area \\\"H\\\"\",\"itemH\":{\"spec\":\"\\u00d894 \\u00b1 0,013\",\"row1\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row2\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row3\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"}},\"distanceJ\":{\"spec\":\"N\\/A\",\"result\":null,\"ar\":\"-\"},\"remarks\":[\"Actual measurement OD area \\\"H\\\" in good condition\"],\"penetrantRemark\":\"No indication crack at area OD area \\\"H\\\" after penetrant check\"},\"item06\":{\"title\":\"OD Area \\\"G\\\" and OD Spline\",\"itemG\":{\"spec\":\"\\u00d884,2 \\u00b1 0,13\",\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"distanceI\":{\"spec\":\"N\\/A\",\"result\":null,\"ar\":\"-\"},\"remarks\":[\"Actual measurement OD area \\\"G\\\" in good condition\"],\"penetrantRemark\":\"No indication crack at area OD spline after penetrant check\"}}','{\"spec\":\"Max 0,2\",\"datum\":\"Datum at item \\\"B and E\\\"\",\"items\":[{\"item\":\"A\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"},{\"item\":\"B\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"},{\"item\":\"C\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"},{\"item\":\"D\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"},{\"item\":\"E\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"},{\"item\":\"F\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"}]}','[{\"item\":\"01\",\"remarks\":null,\"recommendation\":null}]','{\"checkedBy\":{\"name\":null,\"title\":\"QC Inspector\",\"date\":\"2026-11-11\"},\"createdBy\":{\"name\":\"QC Leader\",\"title\":\"QC Inspector\",\"date\":\"2026-11-11\"},\"reviewedBy\":{\"name\":null,\"title\":\"Leader QC\",\"date\":\"2026-11-11\"}}','{\"drawing_dimension\":null,\"finding_component\":null,\"area_a_visual_1\":null,\"area_a_visual_2\":null,\"area_a_visual_3\":null,\"area_a_visual_4\":null,\"area_a_penet_1\":null,\"area_a_penet_2\":null,\"area_a_penet_3\":null,\"area_a_penet_4\":null,\"area_bc_visual_1\":null,\"area_bc_visual_2\":null,\"area_bc_visual_3\":null,\"area_bc_visual_4\":null,\"area_bc_penet_1\":null,\"area_bc_penet_2\":null,\"area_bc_penet_3\":null,\"area_bc_penet_4\":null,\"area_d_visual_1\":null,\"area_d_visual_2\":null,\"area_d_visual_3\":null,\"area_d_visual_4\":null,\"area_d_penet_1\":null,\"area_d_penet_2\":null,\"area_d_penet_3\":null,\"area_d_penet_4\":null,\"area_f_visual_1\":null,\"area_f_visual_2\":null,\"area_f_visual_3\":null,\"area_f_visual_4\":null,\"area_f_penet_1\":null,\"area_f_penet_2\":null,\"area_f_penet_3\":null,\"area_f_penet_4\":null,\"area_h_visual_1\":null,\"area_h_visual_2\":null,\"area_h_visual_3\":null,\"area_h_visual_4\":null,\"area_h_penet_1\":null,\"area_h_penet_2\":null,\"area_h_penet_3\":null,\"area_h_penet_4\":null,\"area_g_visual_1\":null,\"area_g_visual_2\":null,\"area_g_visual_3\":null,\"area_g_visual_4\":null,\"area_g_penet_1\":null,\"area_g_penet_2\":null,\"area_g_penet_3\":null,\"area_g_penet_4\":null,\"tir_diagram\":null}',0,'draft',NULL,NULL,NULL,'2026-11-10 22:21:05','2026-11-10 22:21:05'),(5,4,NULL,NULL,'SSC-6361',NULL,'2026-11-12',NULL,'OUTPUT SHAFT','Pre','-jjjjjjjjjjjjjj','HT051','-','-','FORM-QC-002','04','QC Inspector','2026-11-12','1','{\"item01\":{\"title\":\"OD Area \\\"A\\\" and OD Spline\",\"spec\":\"\\u00d879 \\u00b1 0.25\",\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\",\"remark1\":null,\"lengthResult\":null,\"penetrantRemark\":\"No indication crack at OD area \\\"A\\\" and OD spline after penetrant check\"},\"item02\":{\"title\":\"OD Area \\\"B and C\\\"\",\"itemB\":{\"spec\":\"\\u00d882 \\u00b1 0.25\",\"row1\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row2\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row3\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"}},\"itemC\":{\"spec\":\"\\u00d877 \\u00b1 0.25\",\"row1\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row2\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row3\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"}},\"remarks\":[\"Actual measurement OD area \\\"B and C\\\" in good condition\"],\"penetrantRemark\":\"No indication crack at OD Area \\\"B and C\\\" after penetrant check\"},\"item03\":{\"title\":\"OD Area \\\"D\\\"\",\"itemD\":{\"spec\":\"\\u00d8118 \\u00b1 0.5\",\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"distanceE\":{\"spec\":\"13 \\u00b1 0.5\",\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"remarks\":[\"Actual measurement OD area \\\"D\\\" in good condition\",\"Actual distance area \\\"E\\\" in good condition\"],\"penetrantRemark\":\"No indication crack at OD area \\\"D\\\" after penetrant check\"},\"item04\":{\"title\":\"OD Area \\\"F\\\"\",\"itemF\":{\"spec\":\"\\u00d8100,01 \\u00b1 0,008\",\"row1\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row2\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row3\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"}},\"distanceK\":{\"spec\":\"N\\/A\",\"result\":null,\"ar\":\"-\"},\"remarks\":[\"Actual measurement OD area \\\"F\\\" in good condition\"],\"penetrantRemark\":\"No indication crack at area OD area \\\"F\\\" after penetrant check\"},\"item05\":{\"title\":\"OD Area \\\"H\\\"\",\"itemH\":{\"spec\":\"\\u00d894 \\u00b1 0,013\",\"row1\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row2\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"row3\":{\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"}},\"distanceJ\":{\"spec\":\"N\\/A\",\"result\":null,\"ar\":\"-\"},\"remarks\":[\"Actual measurement OD area \\\"H\\\" in good condition\"],\"penetrantRemark\":\"No indication crack at area OD area \\\"H\\\" after penetrant check\"},\"item06\":{\"title\":\"OD Area \\\"G\\\" and OD Spline\",\"itemG\":{\"spec\":\"\\u00d884,2 \\u00b1 0,13\",\"x\":null,\"y\":null,\"z\":null,\"ar\":\"A\"},\"distanceI\":{\"spec\":\"N\\/A\",\"result\":null,\"ar\":\"-\"},\"remarks\":[\"Actual measurement OD area \\\"G\\\" in good condition\"],\"penetrantRemark\":\"No indication crack at area OD spline after penetrant check\"}}','{\"spec\":\"Max 0,2\",\"datum\":\"Datum at item \\\"B and E\\\"\",\"items\":[{\"item\":\"A\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"},{\"item\":\"B\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"},{\"item\":\"C\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"},{\"item\":\"D\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"},{\"item\":\"E\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"},{\"item\":\"F\",\"d0\":null,\"d90\":null,\"d180\":null,\"d270\":null,\"ar\":\"A\"}]}','[{\"item\":\"01\",\"remarks\":null,\"recommendation\":null}]','{\"checkedBy\":{\"name\":null,\"title\":\"QC Inspector\",\"date\":\"2026-11-12\"},\"createdBy\":{\"name\":\"QC Leader\",\"title\":\"QC Inspector\",\"date\":\"2026-11-12\"},\"reviewedBy\":{\"name\":null,\"title\":\"Leader QC\",\"date\":\"2026-11-12\"}}','{\"drawing_dimension\":null,\"finding_component\":\"\\/uploads\\/reports\\/photos\\/qc_finding_component_1794964670_6afcfcbe158ab.jpg\",\"area_a_visual_1\":null,\"area_a_visual_2\":null,\"area_a_visual_3\":null,\"area_a_visual_4\":null,\"area_a_penet_1\":null,\"area_a_penet_2\":null,\"area_a_penet_3\":\"\\/reports\\/photo\\/qc_area_a_penet_3_1794979080_d613b05d17722b33.jpg\",\"area_a_penet_4\":null,\"area_bc_visual_1\":null,\"area_bc_visual_2\":null,\"area_bc_visual_3\":null,\"area_bc_visual_4\":null,\"area_bc_penet_1\":null,\"area_bc_penet_2\":null,\"area_bc_penet_3\":null,\"area_bc_penet_4\":null,\"area_d_visual_1\":null,\"area_d_visual_2\":null,\"area_d_visual_3\":null,\"area_d_visual_4\":null,\"area_d_penet_1\":null,\"area_d_penet_2\":null,\"area_d_penet_3\":null,\"area_d_penet_4\":null,\"area_f_visual_1\":null,\"area_f_visual_2\":null,\"area_f_visual_3\":null,\"area_f_visual_4\":null,\"area_f_penet_1\":null,\"area_f_penet_2\":null,\"area_f_penet_3\":null,\"area_f_penet_4\":null,\"area_h_visual_1\":null,\"area_h_visual_2\":null,\"area_h_visual_3\":null,\"area_h_visual_4\":null,\"area_h_penet_1\":null,\"area_h_penet_2\":null,\"area_h_penet_3\":null,\"area_h_penet_4\":null,\"area_g_visual_1\":null,\"area_g_visual_2\":null,\"area_g_visual_3\":null,\"area_g_visual_4\":null,\"area_g_penet_1\":null,\"area_g_penet_2\":null,\"area_g_penet_3\":null,\"area_g_penet_4\":null,\"tir_diagram\":null,\"_markers\":{\"finding_component\":[{\"id\":\"m_1794964662879_uanh\",\"number\":1,\"xPercent\":63.18510641385762,\"yPercent\":50.04327616456201,\"direction\":\"down\",\"size\":\"md\",\"color\":\"#dc2626\"}],\"area_a_penet_3\":[{\"id\":\"m_1794978894060_nels\",\"number\":1,\"xPercent\":90.43620801160394,\"yPercent\":40.61236436796092,\"direction\":\"down\",\"size\":\"md\",\"color\":\"#dc2626\",\"rendered\":{\"dataUrl\":\"data:image\\/png;base64,iVBORw0KGgoAAAANSUhEUgAAADQAAABUCAYAAADNjBSxAAAH2ElEQVR4Aexab4hUVRQ\\/b1bFXLNWqdXE1nbnD7EmglBbSVnoJpqWhohCKPhBCyWMkixDJWHDAgml1g\\/CirAgloKl1BrZB1H7sCDigrMzu7mJ2Ga7q+GKqDOv83v7zps7M29m3pudNzvKyD1zzj333HN+v3ffn\\/ve6qOH7F+ZUKkvaHmFnKxQtK7uya5AYF40EFjXFQxui4ZCX0EMGz6McYyTXG5jCrZCEb+\\/kUE3R4PBy1RR0atr2knStGadaDvp+kcQHTZ8GOMYxGJOdyAwnwr0b9iEukKh9Qzsoubz\\/cKg1zGuGhanrQZz4prWhhzI5XRipri8CTGApSwXdV3\\/jpPXsyS1iqoqGjt7No1fuJAmLF9uCGz4MJYUPNSpRy7kZFk65HL\\/mxchnCZc6ghLEhGAnbRlC007fpymnztHU1tbqXr3bnpi505DYMOHMcQgFnM4j9qQ84hZQ\\/U7sl0RCgcCtZFg8BxOEzU7jvzUQ4cMAo+vWUNj\\/H512NZGDGJBEHORIylQ19ehFmom+XN0HBPqDoVmVmjabxrRC5JzTChEU\\/bvN1Zh7KxZ4natMRerh1zIKQlQCzVRW3y5tCNCOEpxXT\\/GyawLHtfFtGPHaNycOewuTEMu5ERuJWMNagOD4stoOiLk07RWzmCRmbhxo3FNsM+ThmsONZTkNSYGxWVv5iSEixNLL9NRqGrDBul6plEDtaQAMACL9DPprISM2ydfnDIZpwIKSd9rjVqoadVhLAYmy5FuZCXE4V+wGA0XK04Fo1PEH9REbaWkhUnxWWZGQuZTu14iJ23eLKZrPTh4m65f70vIv3107959x3lSateb2GznZyTET23rQsEzAncg2wxZnLFYjH483kavzHubQjNftmTB4pV0qTOSZWbyEGoDg3hVbOITbUvI3Cxaq\\/PY6tUS71hjRT7cvI3e\\/+AT+vPyX47nZQpMwVCPzbBdrC2huM\\/3jgRja4IHn\\/Sd6PMXOujNZe\\/SwdbvCadbpjlu\\/MAALDJHq6hYJraqbQnx1maBBFU2NorpWPf3D1Ak2m3EV1aOo4bnZ9OkiVVGfzg\\/SVh03cKo5kwjhJczDrAeojh\\/uZ9Xe2b60\\/TtN19S087PaMKER\\/PKoU5KwVJjYlVDKI2Q5vPNlAhs87GJlL4bvfSthfTT0YO0eFEj+Xz8WHQzOUMssACTDKtYxZdGiN8q62RwdG2tmK40TrHmPbtoyuRqV\\/OcBKuYVKwyN42QpmmTZXBUdX6Axo17hEaPHiVpCqpVTCpWKZJGiL8FjLcGKyvFLBntUzCpWAVgGiEZeFB1GiFN128JmfjgoJglo1VMKlYBmEaItxV\\/y+D93l4xS0armFSsAjCNEN9gu2TwXvfQw1H6paBVTCpWwZZGSI\\/HL8hgbGCA7kaj0h1xDSzAJEBUrOJLI+Tv6vqHB3tYjHb79GlDl8JPCpYeE2sStDRCxqim\\/Wxo\\/hlsa+Pf0mhJWBSMKjpbQnosdkSC7rS3053z56U7YhoYgEUA+OLxH8RWtS2hQDSKZemQwJsHDog5YvrmgSQMHbWRyEk7MLaEEMjbir3QkFsnTlDK+Qu3Y3mu\\/llqP9tG\\/dcuGQIbPqcJUBsYJF7FJj7RGQnVhcPNHGStUt+uXdwdmZZSu8PEZgsmIyEz+nNT091wmK5v3SrdomnURG2loIVJ8VlmVkL+zs6j\\/EerfRL93+HDNLDXOhPF7ZlGLdS0CmjaPgOT5Ug3shJCuD8cXs\\/vHX\\/AhvTv2VMUUiCDWqgJAQZggZ1NchLCZP5Yvoq19bBFIZwK7POkITdqKMl7TAyKy950RCgUiXT7NG0Jp7BI4VS4smTJsO5+nC+p3eZdCXIitzLQg9rAoPgymo4IYXZtOHwhpuuvY+nRh+BivbZ2LfVu2jSshy8emsiBXMiJ3BDUQk3URt+JOCaEZDhKgc7OBvVGAT+eEVdXrKCrq1bRjZYWRxtabDRvtLQYczAXOZDLEr4BoBZqWj4HhitCks+8OPGhz3pOYQxbk76mJrqyaBFdbmgwwOLI45qAwAZpjCEGsZiDuYog5zKzhuJ2ZuZFCKlx+2SZwU\\/t97gPEKwSDdt8gMWRxzUBgQ0fxhKRltWBXMjJctTyujTyJiR18NRmADN8ut5onorWjUNisugezOH3mjeQA7myxDoaGjYhqYLNIk4TBjadYrFqft+fz5+U1\\/P4GRZpZ+AzxjgGsZhjboYlZli6YIRUFHjxqotEfvVHIthlnFXGzsJnjA29SCpDhTE9IVQYaPllKRPK77gVb1Z5hYp3rPOrVF6h\\/I5b8WaVV6h4xzq\\/Sg\\/GCrngViaU62BFgsHtkJxxfv\\/cCEuuOLfjBV8hfsvczrvpV6OBwCk7YiCBMf6T\\/CneZf\\/uFnCu+IITQkH+y9oOfs+Zy3+Q2sb9F1mkrQQRjPH3gh3iLKT2hJBx5HVdjv5LCuCnYOtEO7CSsAstnhACSGOVYNhJPC5k7UaH5fOMUMoqWSCN1YlGHzxCYMBvp69Bq+LVqSY1PFshKcA68U0hcV2x25tWDEKJbwqa1u4NjUTWYhBKVCuC5Tkhvgn0CQ\\/+dmf9LxXxFVp7TohvAk1M5GMm9il\\/u\\/u60ARS83lOCAVBBMRgey1FIeQ1CTV\\/mZB6NErRLq9QKa6KiqnwK6RmHwG7TGgEDrqrkuUVcnW4RiD4oVuh\\/wEAAP\\/\\/LJqfwwAAAAZJREFUAwALbO\\/H\\/EhmGwAAAABJRU5ErkJggg==\",\"width\":52,\"height\":84,\"tipOffsetX\":26,\"tipOffsetY\":70}},{\"id\":\"m_1794978922755_w30e\",\"number\":17,\"xPercent\":40.473771843769825,\"yPercent\":23.214219021268796,\"direction\":\"down\",\"size\":\"md\",\"color\":\"#dc2626\",\"rendered\":{\"dataUrl\":\"data:image\\/png;base64,iVBORw0KGgoAAAANSUhEUgAAADQAAABUCAYAAADNjBSxAAAI5ElEQVR4Aexae2wUVRc\\/s\\/0+Px79qkiwPmKq7T6iNcRXFBUjEgTkGatoIFFM1IAJxpCoUYMKSiQRE\\/5QI5gQi5oqGtEQ8QGIL15qMIZI4na31Uqw8mjLq1XE3fH8hj23d3Zmt7O7MwuabebsPffcc8\\/5\\/ebMnbmz2xD9y\\/4qhE71glYq5KVCyYaGs9oikXHJSGROWzT6VDIWWwqxdNgwxj5eYhXq41uFEuHweAa9PBmN\\/kxVVXtNw9hAhrHcJFpIpvkQxIQOG8bYB76Y0x6J3EQ+\\/ZVMqC0Wm8vAfjBCoU8Y9BzGVcfi9ajDnLRhrEcMxPI6MZdf0YQYwC0sP5im+TIHb2SxHVXDhtGgK66g6kmTqGbGDEugw4Yxm\\/OJTiNiISbLLSdMhX8WRQiXCadaw2IjArDDH3uMzl+3ji7Yvp3Oa2mh2mXLaMTixZZAhw1j8IEv5nAc\\/UDMNZkcut2TXhCheCRSn4hGt+My0aPjzJ+3erVF4Iy776bTwmF92FWHD3xBEHMRw+ZomnOQCzlt9gE6ngm1x2Ijqwxjk0F0tcQ8LRajc1autKow6NJLxVxwi7moHmIhpgRALuREbrEN1HoihLOUNs21HEwteKyL89eupSGjR7PZnwOxEBOxtYh1yA0Mmi2n6olQyDBaOIIic+YDD1hrgm2BHFhzyKEFr8tg0Ezu6oCEsDhRepmORMPmzZNuYC1yIJckAAZgkX6uNi8h6\\/bJi1Mm41JAIukH3SIXcqo8jMXCpAxOJS8hdn+GxTqwWHEpWJ0yfiAncmspFSbNptSchDJP7UbxHP7II6K6tocPH6H9+7uUHOjqplQqxXd4k3oOHlJ23SenfqCLjh\\/\\/S+XJyt2YwabGdSUnIX5qq4WCZwTuQPpE0Y8dO0avrHyDrrr+ZoqNvE7JzLvup+7ug9TX9zvNf\\/hJZdd9cukTp86kH1sTksK6kwKDGHRsYpPWlVBms6iqc\\/rs2eJva9t\\/+oVm3\\/sgPbpgMe3bd8A25ncnC0MjNsNuOVwJpUOhW8UZWxM8+KQv7QcfbaDpt82m9Rs\\/F1PBbSETgAFYZI5RVdUkut6G9I7STXOi6EPHjxfV1nZ27qM9v3Zatpqa\\/9Poa6+y9OyPIUMG07KlT1N855acsuKlpbZpgwb9j4YMHmyzoWPDomHEmEhIFGnxcsa6eojmWjvsYx2XXzaS3nztZXpw3n1WP\\/vDMAwadsbpNGLEcFepqammjZ9+aZt284SxVH+hgqDGsrDUZbCqcSgOQvxeMxIDEGzzsYmEni2hkEFz7r2T3n\\/7Vbrm6iv5XY4ffdlOHvpffLWdNn2+WXlGIw10+63TXOMBCzCJs45VbA5C\\/FbZIIP\\/ra8X1dHOaJpKzz79OFVXD3WMeTX09vZRy+o11NXdo6ZMmTSOIuHceXVMOlYJ4CBkGMbZMvif2lpRHW0Nrxv2ddgLMWzZ9i1t2fqNmpKvOuKkY+L8CquMOwjxdwHVanBo8WdfYuRqi6kOYoU0TDpWjEEchGAshxRTHS+4HIQM0zwqE9O9vaL62rpVZ\\/qUCXnXjgDQMelYZdxBiLcVv8ngX3v3iuprm12diy+K0m1NU1zvbNmJdUw6VvFzEOKbb5sMHm9vF9W31q06kyeOo3DDhZ5y6Jh0rDLZQchMp3fKYKqnh\\/5MJqXrS1tKdYAFmASIjlVsDkLhtrZ9PNjBYh19m\\/sfepahhI8jR45S8+tv2Z47hVQnC0tHBqsNkYOQNWoYH1stf\\/SuX8+f\\/hxfbN5GX235WgUrZO1gkg2LhhFjIq6EzFQKXyJaPn\\/s2EF\\/fP+9pZfygeq0vLWGsIYkTiHVAQZgkbmhdPpd0fXWlVAkmURZdonjoVWrRC263cT7tVKqc2iVDcOu+kRigxsYV0Jw5G3Fi2ghRz\\/8kLKuX5htMnbMaOru\\/FHJhnWrrd21OE2fOpF2J79T45s3rfX03MF85AYG6BAdG\\/q65CTUEI8vZ0dVpa7nnuPuyTmycu\\/KYHMFk5NQxvuJTEt\\/xuO0f8EC6ZatRU7k1hIqTJpNqXkJhVtb3+PH9wrxPvzOO9TzoroSxRxYi1zIqRIYxgoLkzI4lbyE4B6Ox+fye8fX0CHdL7xQFlIgg1zICQEGYIGeTwYkhMn8ZfksbtXDFolwKbAtkAOxkUML3pHBoJncVU+EYolEe8gwpnEIRQqXwu5p0wa8+\\/Ecz0cf70oQE7G1SR3IDQyaLafqiRBm18fjO1OmORalRx+Cxdp5zz20d\\/78kh6+eGgiBmIhJmJDkAs5kRt9L+KZEILhLEVaW0fpNwrY8YzYc8cdtGfWLDrY3OxpQ4uN5sHmZmsO5iIGYinhGwByIaeyeVAKIiTxMouzifvqOcU6YWvStWQJ7Z48mX4eNcoCizOPNQGBDtIYgw98MQdzNUHMpkwOzexNLYoQQuP2yXIJP7Xv5z5AcNN\\/YJsPsDjzWBMQ6LBhrN9TabsQCzFZ3lPWApWiCUkePLUZwCUh0xyfuRTVjUN88rQdmMPvNRMQA7Hy+HoaKpmQZMFmEZcJA7uAf0ep5ff9m\\/i3lLk8vpVFjq2wWWOpVC18MSezGRafklrfCOko8OLVkEhsDCcS2GVs08a2wWaNnXiR1Ib8UQMh5A+04qJUCBV33so3q1Kh8p3r4jJVKlTceSvfrEqFyneui8v0z6hQAdwqhAY6WYlodCFkQL9weEyCZSC\\/Qsd9rxC\\/ZS7k3fQNyUjkMzdiIIEx\\/kn+M95l+\\/5vKL4TwhnlX9YW8XvOGP5B6inuX8Mix0wQwRh\\/X7BIjH62gRCyzrxpytm\\/VgN8LnSTaBEqCd1vCYQQQFpVguIm6bSQdRstyRYYoawqKZBWdZLJfx4hMOC30xvR6hLUpSY5AquQJOC2\\/zuF\\/nXF5mCOchDq\\/07BMHYEQ6M\\/ajkI9WcrgxY4Ib4JdAkP\\/u5O\\/ZeK2PxuAyfEN4ElTORhJvY4f3f3vN8EsuMFTggJQQTEoActZSEUNAk9foWQfjZORb1SoVOxKjom\\/yukRz8JeoXQSTjpBaWsVKig03USnP91FfobAAD\\/\\/6Xzn00AAAAGSURBVAMAkB2p1g+pyqgAAAAASUVORK5CYII=\",\"width\":52,\"height\":84,\"tipOffsetX\":26,\"tipOffsetY\":70}}]},\"_originals\":{\"finding_component\":\"\\/uploads\\/reports\\/photos\\/qc_finding_component_1794964651_6afcfcab47150.webp\",\"area_a_penet_3\":\"\\/reports\\/photo\\/qc_area_a_penet_3_1794978889_f60612af5e88b143.webp\"}}',0,'draft',NULL,NULL,NULL,'2026-11-12 04:54:58','2026-11-17 22:18:05');
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
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
INSERT INTO `sessions` VALUES ('IsVj3OLk6lor3GFnM1g7QRmCXliO0WFzlqTTrCgW',4,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36','YTo3OntzOjY6Il90b2tlbiI7czo0MDoiTW9lWUh5MEZrSHRwcWFYRzRSV1BwWUIzRXlBeHhrUDBCYjJYR1d2NiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjg1OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvcmVwb3J0cy9waG90by9xY19hcmVhX2FfcGVuZXRfM18xNzk0OTc5MDgwX2Q2MTNiMDVkMTc3MjJiMzMuanBnIjtzOjU6InJvdXRlIjtzOjEzOiJyZXBvcnRzLnBob3RvIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NDtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiYzVhZGVmMzgzMTI0OGRjZWUzMzlkOWFkYWYxMzJlM2ZhN2EyNTA1MmQxZWIyNGYzYTE4MDg0MDYwMTgzMTk3YSI7czoxNToiX3FjX3ByZXNlbmNlX3RzIjtpOjE3OTQ5Nzk4NTQ7fQ==',1794979854);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
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
  `avatar` varchar(255) DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user',
  `theme` varchar(15) NOT NULL DEFAULT 'light',
  `locale` varchar(10) NOT NULL DEFAULT 'id',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_seen_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_last_seen_at_index` (`last_seen_at`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'QC Inspector','admin@qcms.com','avatar_1_1794446219.webp','admin','light','id',NULL,'$2y$12$NDGySIiEmHE6AgUIQX9nTeJseNuATAxAH8b78bZZ2dy/xnXZj.WRC',NULL,'2026-11-10 19:54:48','2026-11-11 19:22:59','2026-11-11 20:38:11'),(4,'QC Leader','qc@sscworks.com','avatar_4_1794797336.webp','qc_leader','light','id',NULL,'$2y$12$lIRyWl8.sKCE2N4sV0otj.Lj1AYf3JmZJI6R2uUObjV5.hEdvsEEq',NULL,'2026-11-11 20:16:21','2026-11-15 19:48:56','2026-11-17 22:30:54');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-18 12:31:12

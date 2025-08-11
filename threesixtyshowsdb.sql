-- MySQL dump 10.13  Distrib 8.0.35, for Win64 (x86_64)
--
-- Host: localhost    Database: threesixtyshowsdb
-- ------------------------------------------------------
-- Server version	8.0.35

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
-- Table structure for table `booking_items`
--

DROP TABLE IF EXISTS `booking_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `ticket_type_id` bigint unsigned NOT NULL,
  `seat_id` bigint unsigned DEFAULT NULL,
  `general_admission_area_id` bigint unsigned DEFAULT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `seat_identifier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_items_general_admission_area_id_foreign` (`general_admission_area_id`),
  KEY `booking_items_booking_id_index` (`booking_id`),
  KEY `booking_items_ticket_type_id_index` (`ticket_type_id`),
  KEY `booking_items_seat_id_index` (`seat_id`),
  CONSTRAINT `booking_items_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `booking_items_general_admission_area_id_foreign` FOREIGN KEY (`general_admission_area_id`) REFERENCES `general_admission_areas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `booking_items_seat_id_foreign` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`id`) ON DELETE CASCADE,
  CONSTRAINT `booking_items_ticket_type_id_foreign` FOREIGN KEY (`ticket_type_id`) REFERENCES `ticket_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_items`
--

LOCK TABLES `booking_items` WRITE;
/*!40000 ALTER TABLE `booking_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `booking_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `show_id` bigint unsigned NOT NULL,
  `booking_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number_of_tickets` int NOT NULL,
  `booking_type` enum('assigned_seats','general_admission','mixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general_admission',
  `seat_details` json DEFAULT NULL,
  `booking_metadata` json DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `booking_date` datetime NOT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bookings_booking_number_unique` (`booking_number`),
  KEY `bookings_customer_id_status_index` (`customer_id`,`status`),
  KEY `bookings_show_id_status_index` (`show_id`,`status`),
  KEY `bookings_status_created_at_index` (`status`,`created_at`),
  KEY `bookings_payment_status_created_at_index` (`payment_status`,`created_at`),
  KEY `bookings_expires_at_index` (`expires_at`),
  CONSTRAINT `bookings_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bookings_show_id_foreign` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

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
  PRIMARY KEY (`key`)
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
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
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_email_unique` (`email`),
  KEY `customers_user_id_index` (`user_id`),
  CONSTRAINT `customers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
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
-- Table structure for table `galleries`
--

DROP TABLE IF EXISTS `galleries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `galleries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `show_id` bigint unsigned NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `display_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `galleries_show_id_foreign` (`show_id`),
  CONSTRAINT `galleries_show_id_foreign` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `galleries`
--

LOCK TABLES `galleries` WRITE;
/*!40000 ALTER TABLE `galleries` DISABLE KEYS */;
/*!40000 ALTER TABLE `galleries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `general_admission_areas`
--

DROP TABLE IF EXISTS `general_admission_areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `general_admission_areas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `venue_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `capacity` int NOT NULL,
  `default_price` decimal(10,2) DEFAULT NULL,
  `color_code` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `general_admission_areas_venue_id_is_active_index` (`venue_id`,`is_active`),
  CONSTRAINT `general_admission_areas_venue_id_foreign` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `general_admission_areas`
--

LOCK TABLES `general_admission_areas` WRITE;
/*!40000 ALTER TABLE `general_admission_areas` DISABLE KEYS */;
/*!40000 ALTER TABLE `general_admission_areas` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',2),(3,'0001_01_01_000002_create_jobs_table',3),(4,'2025_04_05_153837_add_role_to_users_table',4),(5,'2025_04_03_123334_create_show_categories_table',5),(6,'2025_04_03_123335_create_venues_table',6),(7,'2025_04_10_144433_create_customers_table',7),(8,'2025_04_03_123333_create_shows_table',8),(9,'2025_04_10_145012_create_posters_table',9),(10,'2025_04_10_145024_create_photo_galleries_table',10),(11,'2025_04_10_145038_create_video_galleries_table',11),(12,'2025_04_03_123339_create_galleries_table',12),(13,'2025_04_10_192044_create_ticket_types_table',13),(14,'2025_04_10_200226_create_seats_table',14),(15,'2025_04_10_200402_create_seat_maps_table',15),(16,'2025_04_03_123335_create_bookings_table',16),(17,'2025_04_10_144801_create_tickets_table',17),(18,'2025_04_16_140512_create_seat_categories_and_modify_tables',18),(19,'2025_04_18_202713_add_redirect_fields_to_shows_table',19),(20,'2025_04_19_081350_change_performers_column_type_in_shows_table',20),(21,'2025_04_20_215511_add_user_id_to_customers_table',21),(22,'2025_05_03_082034_create_photosin_galleries_table',22),(23,'2019_12_14_000001_create_personal_access_tokens_table',23),(24,'2025_06_16_092019_create_videosin_galleries_table',23),(25,'2025_07_16_220013_add_youtubelink_to_videosin_galaries_table',24),(26,'2025_07_20_075614_update_shows_table_add_seating_type',25),(27,'2025_07_20_093314_update_ticket_types_table',26),(28,'2025_07_20_093937_update_tickets_table',27),(29,'2025_07_20_094229_update_seat_reservations_table',28),(30,'2025_07_20_094424_create_general_admission_areas_table',29),(31,'2025_07_20_094620_create_show_ticket_quotas_table',29),(32,'2025_07_20_094714_create_ticket_holds_table',29),(33,'2025_07_20_094838_update_bookings_table',29),(34,'2025_07_20_095026_create_booking_items_table',30),(35,'2025_07_20_095155_add_performance_indexes',31),(36,'2025_07_23_223041_create_payments_table',32),(37,'2025_07_23_223145_update_bookings_table_add_missing_fields',32),(38,'2025_07_23_223209_update_tickets_table_add_missing_fields',32),(39,'2025_07_29_020600_optimize_database_indexes',33);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
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
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
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
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `photo_galleries`
--

DROP TABLE IF EXISTS `photo_galleries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `photo_galleries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `show_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `display_order` int NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `photo_galleries_show_id_foreign` (`show_id`),
  CONSTRAINT `photo_galleries_show_id_foreign` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `photo_galleries`
--

LOCK TABLES `photo_galleries` WRITE;
/*!40000 ALTER TABLE `photo_galleries` DISABLE KEYS */;
INSERT INTO `photo_galleries` VALUES (1,1,'Chicago Photo Gallery 1','photos/KB9gwcIqk9Mj7733QTnl4o8vvMqBToAhfkrWn8Iv.jpg','Chicago Photo Gallery 1',0,1,1,'2025-04-24 14:53:56','2025-04-24 14:53:56'),(2,13,'Umrao Jaan Ada (26th April 2024)','photos/D3oilgCXHprkFvU4LIVxylANiaq5NYf4CNUtCV3Q.jpg',NULL,0,1,1,'2025-07-30 10:19:46','2025-07-30 10:19:46');
/*!40000 ALTER TABLE `photo_galleries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `photosin_galleries`
--

DROP TABLE IF EXISTS `photosin_galleries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `photosin_galleries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `photo_gallery_id` bigint unsigned NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `display_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `photosin_galleries_photo_gallery_id_foreign` (`photo_gallery_id`),
  CONSTRAINT `photosin_galleries_photo_gallery_id_foreign` FOREIGN KEY (`photo_gallery_id`) REFERENCES `photo_galleries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `photosin_galleries`
--

LOCK TABLES `photosin_galleries` WRITE;
/*!40000 ALTER TABLE `photosin_galleries` DISABLE KEYS */;
INSERT INTO `photosin_galleries` VALUES (1,1,'/storage/uploads/photos_in_galleries/1750008266_aajkiraat.webp','NYE 2025',0,0,'2025-06-12 18:13:49','2025-06-15 12:24:26'),(2,1,'/storage/uploads/photos_in_galleries/1749770029_badshah-updated-dallas.jpeg','Badshah Unfinished Tour',0,1,'2025-06-12 18:13:49','2025-07-29 16:07:47'),(3,1,'/storage/uploads/photos_in_galleries/1749770448_nehakakar.webp','Neha Kakar Live in Concert',0,1,'2025-06-12 18:20:48','2025-07-29 16:05:23'),(4,1,'/storage/uploads/photos_in_galleries/1749770448_nora-dallas.jpeg','Nora Fatehi',0,1,'2025-06-12 18:20:48','2025-07-29 16:06:50'),(5,1,'/storage/uploads/photos_in_galleries/1749770448_saraalikhan.webp','Dallas Fashion Gala',0,1,'2025-06-12 18:20:48','2025-07-29 16:08:50'),(6,1,'/storage/uploads/photos_in_galleries/1753823807_ABHIJEET-Bhattacharya.jpg','ABHIJEET-Bhattacharya',0,1,'2025-06-15 12:03:16','2025-07-29 16:16:47'),(7,1,'/storage/uploads/photos_in_galleries/1753824828_Azadi Utsave dallas.jpg','Azadi Utsave dallas',0,1,'2025-06-15 12:03:16','2025-07-29 16:33:48'),(8,2,'/storage/uploads/photos_in_galleries/1753890745_rav00810 - Copy.jpg','Umrao Jaan Ada',0,1,'2025-07-30 10:52:25','2025-07-30 10:52:25'),(9,2,'/storage/uploads/photos_in_galleries/1753890745_rav00810.jpg','Umrao Jaan Ada',0,1,'2025-07-30 10:52:25','2025-07-30 10:52:25'),(10,2,'/storage/uploads/photos_in_galleries/1753890745_rav00864 - Copy.jpg','Umrao Jaan Ada',0,1,'2025-07-30 10:52:25','2025-07-30 10:52:25'),(11,2,'/storage/uploads/photos_in_galleries/1753890785_rav00864.jpg','Umrao Jaan Ada',0,1,'2025-07-30 10:53:05','2025-07-30 10:53:05'),(12,2,'/storage/uploads/photos_in_galleries/1753890785_rav00890 - Copy.jpg','Umrao Jaan Ada',0,1,'2025-07-30 10:53:05','2025-07-30 10:53:05'),(13,2,'/storage/uploads/photos_in_galleries/1753890785_rav00890.jpg','Umrao Jaan Ada',0,1,'2025-07-30 10:53:05','2025-07-30 10:53:05'),(14,2,'/storage/uploads/photos_in_galleries/1753890827_rav00928 - Copy.jpg','Umrao Jan Ada',0,1,'2025-07-30 10:53:47','2025-07-30 10:53:47'),(15,2,'/storage/uploads/photos_in_galleries/1753890827_rav00928.jpg','Umrao Jan Ada',0,1,'2025-07-30 10:53:47','2025-07-30 10:53:47'),(16,2,'/storage/uploads/photos_in_galleries/1753890827_rav00960.jpg','Umrao Jan Ada',0,1,'2025-07-30 10:53:47','2025-07-30 10:53:47'),(17,2,'/storage/uploads/photos_in_galleries/1753890827_rav00982.jpg','Umrao Jan Ada',0,1,'2025-07-30 10:53:47','2025-07-30 10:53:47'),(18,2,'/storage/uploads/photos_in_galleries/1753891045_rav00996.jpg','Umrao Jaan Ada',0,1,'2025-07-30 10:57:25','2025-07-30 10:57:25'),(19,2,'/storage/uploads/photos_in_galleries/1753891045_rav01032.jpg','Umrao Jaan Ada',0,1,'2025-07-30 10:57:25','2025-07-30 10:57:25'),(20,2,'/storage/uploads/photos_in_galleries/1753891045_rav01036.jpg','Umrao Jaan Ada',0,1,'2025-07-30 10:57:25','2025-07-30 10:57:25'),(21,2,'/storage/uploads/photos_in_galleries/1753891105_rav01081.jpg','Umrao Jan Ada',0,1,'2025-07-30 10:58:25','2025-07-30 10:58:25'),(22,2,'/storage/uploads/photos_in_galleries/1753891105_rav01086.jpg','Umrao Jan Ada',0,1,'2025-07-30 10:58:25','2025-07-30 10:58:25'),(23,2,'/storage/uploads/photos_in_galleries/1753891105_rav01153.jpg','Umrao Jan Ada',0,1,'2025-07-30 10:58:25','2025-07-30 10:58:25'),(24,2,'/storage/uploads/photos_in_galleries/1753891105_rav01166.jpg','Umrao Jan Ada',0,1,'2025-07-30 10:58:25','2025-07-30 10:58:25'),(25,2,'/storage/uploads/photos_in_galleries/1753891105_rav01224.jpg','Umrao Jan Ada',0,1,'2025-07-30 10:58:25','2025-07-30 10:58:25'),(26,2,'/storage/uploads/photos_in_galleries/1753891138_rav01235.jpg','Umrao Jan Ada',0,1,'2025-07-30 10:58:58','2025-07-30 10:58:58'),(27,2,'/storage/uploads/photos_in_galleries/1753891138_rav01272.jpg','Umrao Jan Ada',0,1,'2025-07-30 10:58:58','2025-07-30 10:58:58'),(28,2,'/storage/uploads/photos_in_galleries/1753891138_rav01280.jpg','Umrao Jan Ada',0,1,'2025-07-30 10:58:58','2025-07-30 10:58:58'),(29,2,'/storage/uploads/photos_in_galleries/1753891138_rav01291.jpg','Umrao Jan Ada',0,1,'2025-07-30 10:58:58','2025-07-30 10:58:58'),(30,2,'/storage/uploads/photos_in_galleries/1753891138_rav01501.jpg','Umrao Jan Ada',0,1,'2025-07-30 10:58:58','2025-07-30 10:58:58'),(31,2,'/storage/uploads/photos_in_galleries/1753891174_rav01530.jpg','Umrao Jan Ada',0,1,'2025-07-30 10:59:34','2025-07-30 10:59:34'),(32,2,'/storage/uploads/photos_in_galleries/1753891174_rav01562.jpg','Umrao Jan Ada',0,1,'2025-07-30 10:59:34','2025-07-30 10:59:34'),(33,2,'/storage/uploads/photos_in_galleries/1753891174_rav01575.jpg','Umrao Jan Ada',0,1,'2025-07-30 10:59:34','2025-07-30 10:59:34'),(34,2,'/storage/uploads/photos_in_galleries/1753891174_rav01756.jpg','Umrao Jan Ada',0,1,'2025-07-30 10:59:34','2025-07-30 10:59:34'),(35,2,'/storage/uploads/photos_in_galleries/1753891203_rav01794.jpg','Umrao Jan Ada',0,1,'2025-07-30 11:00:03','2025-07-30 11:00:03'),(36,2,'/storage/uploads/photos_in_galleries/1753891203_rav01797.jpg','Umrao Jan Ada',0,1,'2025-07-30 11:00:03','2025-07-30 11:00:03'),(37,2,'/storage/uploads/photos_in_galleries/1753891203_rav01802.jpg','Umrao Jan Ada',0,1,'2025-07-30 11:00:03','2025-07-30 11:00:03');
/*!40000 ALTER TABLE `photosin_galleries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posters`
--

DROP TABLE IF EXISTS `posters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `show_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `display_order` int NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `posters_show_id_foreign` (`show_id`),
  CONSTRAINT `posters_show_id_foreign` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posters`
--

LOCK TABLES `posters` WRITE;
/*!40000 ALTER TABLE `posters` DISABLE KEYS */;
/*!40000 ALTER TABLE `posters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seat_categories`
--

DROP TABLE IF EXISTS `seat_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seat_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `color_code` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seat_categories`
--

LOCK TABLES `seat_categories` WRITE;
/*!40000 ALTER TABLE `seat_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `seat_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seat_maps`
--

DROP TABLE IF EXISTS `seat_maps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seat_maps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `venue_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `map_data` json DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `seat_maps_venue_id_foreign` (`venue_id`),
  CONSTRAINT `seat_maps_venue_id_foreign` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seat_maps`
--

LOCK TABLES `seat_maps` WRITE;
/*!40000 ALTER TABLE `seat_maps` DISABLE KEYS */;
/*!40000 ALTER TABLE `seat_maps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seat_reservations`
--

DROP TABLE IF EXISTS `seat_reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seat_reservations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `show_id` bigint unsigned NOT NULL,
  `seat_id` bigint unsigned NOT NULL,
  `ticket_id` bigint unsigned DEFAULT NULL,
  `ticket_type_id` bigint unsigned DEFAULT NULL,
  `booking_id` bigint unsigned DEFAULT NULL,
  `status` enum('temporary','reserved','booked','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'temporary',
  `reservation_type` enum('permanent','temporary','held') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'permanent',
  `reserved_by` bigint unsigned DEFAULT NULL,
  `reserved_until` datetime DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `seat_reservations_ticket_type_id_foreign` (`ticket_type_id`),
  KEY `seat_reservations_show_id_status_reservation_type_index` (`show_id`,`status`,`reservation_type`),
  KEY `seat_reservations_expires_at_index` (`expires_at`),
  KEY `seat_reservations_show_id_status_index` (`show_id`,`status`),
  KEY `seat_reservations_seat_id_show_id_index` (`seat_id`,`show_id`),
  KEY `seat_reservations_reserved_by_status_index` (`reserved_by`,`status`),
  KEY `seat_reservations_booking_id_status_index` (`booking_id`,`status`),
  KEY `seat_reservations_reserved_until_status_index` (`reserved_until`,`status`),
  CONSTRAINT `seat_reservations_seat_id_foreign` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`id`) ON DELETE CASCADE,
  CONSTRAINT `seat_reservations_show_id_foreign` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON DELETE CASCADE,
  CONSTRAINT `seat_reservations_ticket_type_id_foreign` FOREIGN KEY (`ticket_type_id`) REFERENCES `ticket_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seat_reservations`
--

LOCK TABLES `seat_reservations` WRITE;
/*!40000 ALTER TABLE `seat_reservations` DISABLE KEYS */;
/*!40000 ALTER TABLE `seat_reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seats`
--

DROP TABLE IF EXISTS `seats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `venue_id` bigint unsigned NOT NULL,
  `section` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `row` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `seat_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('available','reserved','sold','blocked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `seat_category_id` bigint unsigned DEFAULT NULL,
  `coordinates_x` int DEFAULT NULL,
  `coordinates_y` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `seats_venue_id_section_row_seat_number_unique` (`venue_id`,`section`,`row`,`seat_number`),
  KEY `seats_venue_id_status_is_active_index` (`venue_id`,`status`,`is_active`),
  KEY `seats_seat_category_id_status_index` (`seat_category_id`,`status`),
  KEY `seats_venue_id_is_active_index` (`venue_id`,`is_active`),
  KEY `seats_seat_category_id_is_active_index` (`seat_category_id`,`is_active`),
  CONSTRAINT `seats_seat_category_id_foreign` FOREIGN KEY (`seat_category_id`) REFERENCES `seat_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `seats_venue_id_foreign` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seats`
--

LOCK TABLES `seats` WRITE;
/*!40000 ALTER TABLE `seats` DISABLE KEYS */;
/*!40000 ALTER TABLE `seats` ENABLE KEYS */;
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
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `show_categories`
--

DROP TABLE IF EXISTS `show_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `show_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `show_categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `show_categories`
--

LOCK TABLES `show_categories` WRITE;
/*!40000 ALTER TABLE `show_categories` DISABLE KEYS */;
INSERT INTO `show_categories` VALUES (1,'Events','events','Event Category','show-categories/2025-02-14-12-50-40-436_92_1744983241.webp',1,'2025-04-18 08:34:02','2025-04-18 08:34:02'),(2,'Concerts','concerts','Concerts','show-categories/Gamechangerprerelease_1744983327.jpg',1,'2025-04-18 08:35:27','2025-04-18 08:35:27');
/*!40000 ALTER TABLE `show_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `show_ticket_quotas`
--

DROP TABLE IF EXISTS `show_ticket_quotas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `show_ticket_quotas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `show_id` bigint unsigned NOT NULL,
  `ticket_type_id` bigint unsigned NOT NULL,
  `area_id` bigint unsigned DEFAULT NULL,
  `area_type` enum('seat_category','general_admission') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_quota` int NOT NULL,
  `sold_count` int NOT NULL DEFAULT '0',
  `reserved_count` int NOT NULL DEFAULT '0',
  `available_count` int GENERATED ALWAYS AS (((`total_quota` - `sold_count`) - `reserved_count`)) VIRTUAL,
  `price_override` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `show_ticket_quota_unique` (`show_id`,`ticket_type_id`,`area_id`,`area_type`),
  KEY `show_ticket_quotas_ticket_type_id_foreign` (`ticket_type_id`),
  KEY `show_ticket_quotas_show_id_is_active_index` (`show_id`,`is_active`),
  CONSTRAINT `show_ticket_quotas_show_id_foreign` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON DELETE CASCADE,
  CONSTRAINT `show_ticket_quotas_ticket_type_id_foreign` FOREIGN KEY (`ticket_type_id`) REFERENCES `ticket_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `show_ticket_quotas`
--

LOCK TABLES `show_ticket_quotas` WRITE;
/*!40000 ALTER TABLE `show_ticket_quotas` DISABLE KEYS */;
/*!40000 ALTER TABLE `show_ticket_quotas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shows`
--

DROP TABLE IF EXISTS `shows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shows` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `venue_id` bigint unsigned NOT NULL,
  `seating_type` enum('assigned','general_admission','mixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general_admission',
  `requires_seat_selection` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `featured_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `available_tickets` int DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('upcoming','ongoing','past','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'upcoming',
  `performers` text COLLATE utf8mb4_unicode_ci,
  `additional_info` json DEFAULT NULL,
  `duration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age_restriction` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `redirect` tinyint(1) NOT NULL DEFAULT '0',
  `redirect_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shows_slug_unique` (`slug`),
  KEY `shows_is_active_status_index` (`is_active`,`status`),
  KEY `shows_venue_id_is_active_index` (`venue_id`,`is_active`),
  KEY `shows_start_date_is_active_index` (`start_date`,`is_active`),
  KEY `shows_category_id_is_active_index` (`category_id`,`is_active`),
  CONSTRAINT `shows_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `show_categories` (`id`),
  CONSTRAINT `shows_venue_id_foreign` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shows`
--

LOCK TABLES `shows` WRITE;
/*!40000 ALTER TABLE `shows` DISABLE KEYS */;
INSERT INTO `shows` VALUES (1,'Rangotsav Holi Festival  - Chicago','rangotsav-holi-festival-chicago',1,6,'general_admission',0,'Put Your Hands Up, New Jersey! Join the Hrithik Roshan Tour Kickoff\r\n\r\nPrepare yourself for a unique Holi festival experience with a special meet and greet with the incredibly charismatic Hrithik Roshan. Hrithik Roshan, renowned for his exceptional dancing skills, has starred in numerous Bollywood hits like \"Kaho Naa... Pyaar Hai,\" \"Dhoom 2,\" and \"War.\" He is not only an acclaimed actor but also a celebrated style icon and philanthropist. This festival promises an unforgettable experience, allowing fans to interact with Hrithik and enjoy a vibrant display of colors, music, and dance.\r\n\r\nA huge thank you to Atique B Sheikh of Worldstar for adding extra magic to this grand celebration.\r\n\r\nEvent Details:\r\n\r\nVenue: TBD\r\nMore Info: Visit 3sixtyshows.com\r\nTickets: Available\r\n\r\n\r\nPrepare yourself for a unique Holi festival experience with a special meet and greet with the incredibly charismatic Hrithik Roshan. Don’t miss out on this unforgettable celebration starting with a bang!','Rangotsav Holi Festival  - Chicago','shows/1749824264.jpg','2025-04-22 19:00:00','2025-04-22 22:00:00',1000.00,0,1,'past','Hrithik Roshan','[]','180',NULL,0,1,'https://3sixtyshows.com/us/schaumburg/events/rangotsav-holi-festival---chicago-at-to-be-announced?frm=pe','2025-04-19 04:57:10','2025-08-05 14:53:56'),(2,'Badshah Un-Finished Tour Dallas','badshah-un-finished-tour-dallas',1,2,'general_admission',0,'Introduction\r\n\r\nBadshah, the renowned Indian rapper and music producer, is all set to take the nation by storm with his Un-Finished Tour. This multi-city musical journey promises to be an unforgettable experience for fans across the country. In this article, we explore the highlights of Badshah’s tour, the electrifying performances, and the venues that will witness the magic.\r\n\r\nWho Is Badshah?\r\n\r\nBorn as Aditya Prateek Singh Sisodia, Badshah has carved a niche for himself in the Indian music industry. His unique blend of rap, hip-hop, and Punjabi beats has made him a household name. From chart-topping singles to blockbuster Bollywood tracks, Badshah’s musical prowess knows no bounds.\r\n\r\nThe Concert Experience\r\n\r\nBadshah’s concerts are more than just music; they are a celebration of life, rhythm, and unbridled passion. From his iconic hits like “DJ Waley Babu” to soul-stirring melodies, every track resonates with fans. The stage lights up, the beats drop, and Badshah’s magnetic presence takes center stage.\r\n\r\nThe Paagal Anthem: Badshah’s hit single “Paagal” has become an anthem for partygoers. Its catchy beats and infectious lyrics have taken the world by storm.\r\nSocial Media Buzz: Fans can’t stop talking about the tour on social media. From Instagram stories to Twitter trends, the excitement is palpable.\r\nTicket Information:www.3sixtyshows.com\r\nDon’t miss out on this sensational event—it’s going to be paagal (crazy) in the best way possible! \r\nConclusion\r\n\r\nAs Badshah’s beats reverberate through concert halls, fans will dance, sing, and create memories that last a lifetime. The Un-Finished Tour is not just a concert; it’s an experience that transcends boundaries and unites music lovers across the nation.\r\n\r\nDon’t miss the chance to be part of this musical extravaganza! 🎶🔥','Badshah Un-Finished Tour Dallas','shows/1745177061.jpeg','2025-09-19 20:00:00','2025-09-19 22:00:00',500.00,1000,1,'upcoming','Badshah','[]','180',NULL,1,0,NULL,'2025-04-20 14:23:46','2025-04-20 14:24:21'),(3,'BOLLYWOOD NIGHT WITH NORA FATEHI','bollywood-night-with-nora-fatehi',1,13,'general_admission',0,'Ajani Records x 3Sixty Show x TopShot Events Presents\r\n🎉 Techno Bollywood Night ft. Nora Fatehi | Ethnique Dance | DJ Rzon | DJ Dimple 💃🕺\r\n\r\nBrace yourself for the most electrifying Bollywood-EDM fusion night of the year!\r\n\r\n🎤 Featuring:\r\n🌟 Nora Fatehi – International superstar, dance diva, showstopper... and YES – she’ll be there for an exclusive chit-chat and meet & greet with fans! 🔥\r\n💃 Ethnique Dance Crew – Explosive live performances blending Bollywood and contemporary\r\n🎧 DJ Rzon & DJ Dimple – Back-to-back sets of high-energy Techno x Bollywood remixes all night long\r\n\r\n✨ Lights, rhythm, glam, and unforgettable energy – all in one epic night!\r\n🎭 Dress Code: Desi Glam x EDM Chic\r\n\r\n \r\n\r\n📅 Date: Saturday, 26th April 2025\r\n⏰ Time: Doors open at 10:00 PM\r\n📍 Venue: Sambuca 360, Plano, TX\r\n\r\n📲 Follow us for updates: @topshotevents @3sixtyshows @ajanirecords\r\n\r\n🎟️ Tickets: Limited availability – Book now before it’s SOLD OUT!\r\n\r\n🔥 It’s more than a party. It’s a movement. It’s time to ROAR again!                                                                      #NoraInTheHouse #BollywoodMeetsTechno #DFWPartyScene #NoraFatehiLive #Sambuca360 #BollywoodNightDFW #EDMXBollywood #VIPNightVibes #DesiRave\r\n#PlanoNightlife #AjaniRecords #3SixtyShow #TopShotEvents\r\n\r\n \r\n\r\n💃 VIP Booths Available:\r\n\r\n📞 Call Isha: 🎉 VIP Section Reservations:\r\n📞 Isha: (469) 560-5560\r\n📞 Hussain: (346) 690-9128\r\n📞 Hotline: (855) 360-SHOWS\r\n\r\nto reserve your exclusive VIP booth. Elevate your night with luxury and style!','BOLLYWOOD NIGHT WITH NORA FATEHI','shows/1745183193.jpeg','2025-04-26 22:00:00','2025-04-26 23:59:00',1000.00,1000,1,'past','BOLLYWOOD NIGHT WITH NORA FATEHI','[]','180',NULL,0,0,NULL,'2025-04-20 16:06:33','2025-08-05 14:52:02'),(4,'Ghulam Ali - Farewell Tour Dallas','ghulam-ali-farewell-tour-dallas',1,11,'general_admission',0,'🎶 Experience the Magic One Last Time: Ghulam Ali Farewell Tour in Plano 🎶\r\n\r\nJoin legendary ghazal maestro Ustad Ghulam Ali for his final U.S. performance in Plano, Texas. This unforgettable evening of soulful melodies takes place on Sunday, April 20, 2025, at 6:30 PM at The Grand Center. Don\'t miss this rare opportunity to witness a musical icon live on stage.​\r\n\r\n📍 Venue: The Grand Center, 300 Chisholm Pl, Plano, TX 75075\r\n🎟️ Tickets: Starting at only $45, with upgrade  options including dinner and meet & greet experiences.\r\n🔗 Reserve Your Seats Now​ at: \r\nhttps://events.sulekha.com/ghulam-ali-farewell-tour_event-in_plano-tx_384577\r\n\r\nCelebrate a lifetime of ghazals and bid farewell to a true legend. Secure your tickets today for an evening that promises to be both nostalgic and extraordinary.','Ghulam Ali - Farewell Tour Dallas','shows/1753987537.jpg','2023-02-24 19:00:00','2023-02-24 22:00:00',1000.00,1000,1,'past','Ghulam Ali - Farewell Tour Dallas','[]','180',NULL,0,0,NULL,'2025-04-20 16:19:57','2025-08-05 14:49:00'),(5,'Shahzada Kartik Aryan 2023','shahzada-kartik-aryan-2023',1,19,'general_admission',0,'Holi with Shezada of Bollywood Kartik Aryan.\r\n\r\n\r\n\r\nDALLAS\r\n\r\nAT VSTA MALL (PARKING LOT) DEW EVENT CENTER. 2401 S STEMMONS F W Y.\r\n\r\nLEWISVILLE. TX 75067 ( NEXT TO UPPER LEVEL OF DILLARDS )','Shahzada Kartik Aryan 2023','shows/1745906134.jpg','2023-01-29 19:00:00','2025-04-29 22:00:00',1000.00,0,1,'past','Shahzada Kartik Aryan','[]','180',NULL,0,0,NULL,'2025-04-29 00:55:35','2025-08-06 02:58:39'),(7,'ABHIJEET-Bhattacharya','abhijeet-bhattacharya',1,14,'general_admission',0,'Here we go, Dallas\r\n\r\nThe legendary voice of the 90s, Abhijeet Bhattacharya, is bringing his Retro 90’s magic live on stage!\r\n\r\nDate: Sunday, Nov 2nd\r\n\r\nVenue: TBA\r\n\r\nTickets & Info: www.3sixtyshows.com\r\n\r\nCall: 855-360-SHOW |\r\n\r\n469-264-2257\r\n\r\nDon’t miss this romantic musical night as we celebrate Shahrukh Khan’s birthday with all his song!\r\n\r\n#AbhijeetLive #Retro90s #DallasEvents #3SixtyShows #LiveMusic #BollywoodNostalgia #ConcertVibes Karl Kalra','ABHIJEET-Bhattacharya','shows/1753826098.jpg','2025-11-02 19:00:00','2025-11-30 19:00:00',1000.00,NULL,1,'upcoming',NULL,'[]',NULL,NULL,1,0,NULL,'2025-07-29 16:54:58','2025-08-05 14:43:22'),(8,'AZADI UTSAV DALLAS','azadi-utsav-dallas',2,14,'general_admission',0,'🇺🇸DALLAS, MARK YOUR CALENDARS!\r\n\r\n \r\n\r\n🇮🇳Celebrating India’s Independence Day\r\n\r\nAZADI UTSAV – The Jashan of Freedom is coming to you with Bollywood heartthrob KARTIK AARYAN – LIVE in DALLAS! 🎉💥\r\n\r\n \r\n\r\n🗓 Saturday, August 16, 2025\r\n\r\n📍 Dallas, TX\r\n\r\n🌟 LARGEST INDOOR CELEBRATION\r\n\r\n \r\n\r\nProudly presented by Vinessa Events\r\n\r\nNational Promoters: Diamond Productions & AP Square\r\n\r\n \r\n\r\nGet ready for a night of music, pride, and patriotism!\r\n\r\n📞 For sponsorships, contact Hari Dornala: (469) 442-8770\r\n\r\n📢 More exciting details coming soon!\r\n\r\n \r\n\r\n#AzadiUtsavDallas #KartikAaryanLive #CelebrateFreedom #DallasEvents #DesiVibes #IndependenceDay2025 #BollywoodNight #VinessaEvents #DiamondProductions #APSquare #3SixtyShows','AZADI UTSAV DALLAS','shows/1753826303.jpg','2025-08-16 19:00:00','2025-08-16 19:00:00',1000.00,NULL,1,'upcoming',NULL,'[]',NULL,NULL,1,0,NULL,'2025-07-29 16:58:23','2025-08-05 14:36:38'),(9,'AZADI UTSAV HOUSTON','azadi-utsav-houston',2,14,'general_admission',0,'Kartik Aaryan, Madhubanti Bagchi & Arjun Kanungoon August 15 at Houston’s Azadi Utsavf\r\n\r\nEvent Details\r\n\r\nDate: August 15, 2025\r\n\r\nTime: 7:00 PM onwards (5 hours of non-stop entertainment!)\r\n\r\nVenue: To Be Announced Soon\r\n\r\nArtists: Kartik Aaryan • Madhubanti Bagchi • Arjun Kanungo\r\n\r\nBe part of the first-ever indoor India Independence Day celebration in Houston — an evening of electrifying performances, cultural pride, and unforgettable memories!\r\n\r\nTickets\r\n\r\nGeneral Admission (Presale – July 4th weekend): Only $5 per person\r\n\r\nChildren 2 and under: FREE\r\n\r\n Grab your tickets during the July 4th weekend presale to lock in this special price!\r\n\r\nVendor Booths\r\n\r\nShowcase your business and connect with thousands of attendees!\r\n\r\n1 Food Booth: $2,500\r\n\r\n2 Food Booths: $4,000\r\n\r\nCommercial Vendor Booth (non sales): $1,500\r\n\r\n Sponsorship & Branding Opportunities\r\n\r\nElevate your brand by sponsoring Houston’s premier Azadi Utsav!\r\n\r\nCustomized sponsorship & branding packages available\r\n\r\nReach thousands of attendees & align with a high-energy, cultural celebration\r\n\r\nWhy You Shouldn’t Miss This:\r\n\r\nCelebrate India’s Independence in a grand indoor setting Star-studded performances Family-friendly event Affordable tickets & vendor options Prime opportunities for sponsors and vendors\r\n\r\n\r\nStay tuned for the venue announcement — but don’t wait to secure your spot at this incredible celebration!','AZADI UTSAV HOUSTON','shows/1753826427.jpg','2025-08-15 19:00:00','2025-08-15 19:00:00',1000.00,0,1,'upcoming',NULL,'[]',NULL,NULL,1,0,NULL,'2025-07-29 17:00:27','2025-08-06 02:59:37'),(10,'GARBA AGHORI MUZIK  DALLAS','garba-aghori-muzik-dallas',2,14,'general_admission',0,'🎶 Garba goes wild, the Aghori way! 🔥\r\n\r\n \r\n\r\nDallas, Chalo Ready for a Pre-Navratri night of unstoppable beats, hip-folk fusion, and pure desi madness?\r\n\r\nAghori Muzik is bringing the vibe — and you don’t want to miss it! 🪘💃\r\n\r\n📍 Dallas\r\n\r\n📅 Friday, Sept 12th\r\n\r\n📞 855-360-SHOWS | 469-264-2257\r\n\r\n🌐 www.3sixtyshows.com\r\n\r\n#AghoriMuzik #HipFolkGarba #Garba2025 #3SixtyShows #DallasEvents #BYLiveEvents #DesiBeats #NavratriNights','3SIXTY SHOWS PRESENTS\r\n\r\nPRE NAVRATRI\r\nHIP-FOLK GARBA\r\nAGHORI MUZIK\r\n\r\nDALLAS FRI, SEPT 12TH','shows/1753826741.jpg','2025-09-12 19:00:00','2025-09-12 19:00:00',1000.00,NULL,1,'upcoming',NULL,'[]',NULL,NULL,1,0,NULL,'2025-07-29 17:05:41','2025-08-06 02:59:07'),(11,'PURVASTIC TOUR','purvastic-tour',2,14,'general_admission',0,'🎤 Dandiya Ready, Dallas \r\n\r\n \r\n\r\nThe PURVASTIC TOUR featuring the electrifying Purva Mantri is about to set the stage on fire! 🔥\r\n\r\nDrums, dance, and desi vibes ⚡\r\n\r\nThis is the celebration you don’t want to miss! 💃🕺\r\n\r\n \r\n\r\n📍 Dallas\r\n\r\n📅 Saturday, Sept 13th\r\n\r\n🎟 Call now: 855-360-SHOWS | 469-264-2257\r\n\r\n🌐 www.3sixtyshows.com\r\n\r\n#PurvasticTour #PurvaMantri #LiveInDallas #3SixtyShows #DesiVibes #DholBeats #BY','PURVASTIC TOUR','shows/1753826883.jpg','2025-09-13 19:00:00','2925-09-13 19:00:00',1000.00,NULL,1,'upcoming',NULL,'[]',NULL,NULL,1,0,NULL,'2025-07-29 17:08:03','2025-08-05 14:37:32'),(12,'Badshah Live In Concert','badshah-live-in-concert',2,14,'general_admission',0,'Badshah Live In Concert','Badshah Live In Concert','shows/1754160899.jpg','2024-06-15 19:00:00','2024-06-15 19:00:00',1000.00,0,1,'past',NULL,'[]',NULL,NULL,0,1,'https://curtisculwellcenter.com/events-tickets/badshah-live-concert-0','2025-07-29 17:11:21','2025-08-06 02:58:05'),(13,'Umrao Jaan Ada (26th April 2024)','umrao-jaan-ada-26th-april-2024',2,9,'general_admission',0,'Step into the world of classical charm and poetic elegance with Neetu Chandra as she brings to life the iconic character of Umrao Jaan Ada in a musical play that promises to be a visual and auditory feast. This enchanting production, set to grace the stage in Dallas on the 26th and 27th of April, is a must-see for anyone who appreciates the fusion of traditional storytelling with contemporary theatrical flair.\r\n\r\nNeetu Chandra’s portrayal of Umrao Jaan, the famous courtesan from Lucknow, is not just a performance but an embodiment of grace, depth, and the complexities of a character that has captivated audiences for generations. The musical, directed by Raajev Goswami, features the mesmerizing music recreated by the renowned composer duo Salim and Sulaiman, ensuring that each note strikes a chord with the soul.\r\n\r\nThe production boasts over 3500 kg of material shipped to America, with more than 350 colorful costumes designed to transport you to the heart of Lucknow’s poetic grandeur1. Accompanied by four live singers, the musical play offers an eclectic atmosphere, reviving the timeless songs from the classic 1981 movie.\r\n\r\nDon’t miss the opportunity to witness this spectacular blend of history, culture, and artistry. Join us for a memorable evening at the Umrao Jaan Ada musical play live in Dallas. Secure your tickets now at www.3sixtyshows.com and be part of a mesmerizing experience that will leave you spellbound.','Umrao Jaan Ada (26th April 2024)','shows/1753888619.jpg','2024-04-26 19:00:00','2024-04-26 23:00:00',1000.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-07-30 10:17:01','2025-08-06 02:28:43'),(14,'DALLAS FASHION GALA with SARA ALI KHAN','dallas-fashion-gala-with-sara-ali-khan',2,15,'general_admission',0,'Elevate your fashion game and be part of the glitz and glamour at the \"Dallas Fashion Gala with Sara Ali Khan.\" Join us for a night of haute couture, style, and star-studded elegance as the Bollywood sensation, Sara Ali Khan, graces the event. Experience a runway showcase of the latest trends, impeccable designs, and the fusion of culture and fashion. Don\'t miss this chance to indulge in a night of fashion and sophistication like no other. Secure your tickets now and immerse yourself in the world of high fashion with Sara Ali Khan in Dallas!','DALLAS FASHION GALA with SARA ALI KHAN','shows/1753901215.webp','2022-08-12 19:00:00','2022-08-12 23:00:00',100.00,0,0,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-07-30 13:46:55','2025-08-06 02:47:18'),(15,'The Sonu Nigam Show In Dallas','the-sonu-nigam-show-in-dallas',2,12,'general_admission',0,'The Sonu Nigam Show is a live concert tour by Indian playback singer Sonu Nigam. The tour will kick off in August 2023 and will travel to major cities in the United States. Nigam will perform a repertoire of his popular songs from Hindi films, as well as some of his own compositions. He will be accompanied by a live band and a troupe of dancers.\r\n\r\nNigam is one of the most popular and successful playback singers in India. He has won numerous awards for his singing, including the National Film Award for Best Male Playback Singer and the Filmfare Award for Best Male Playback Singer. He is known for his powerful voice and his versatility. He can sing in a variety of genres, including pop, classical, and folk.\r\n\r\nThe Sonu Nigam Show is a must-see for fans of Indian music. Nigam is a true master of his craft and he is sure to put on a memorable performance. The concert will be a celebration of Indian music and culture.\r\n\r\nHere are some of the things you can expect at The Sonu Nigam Show:\r\n\r\nA wide variety of popular songs from Hindi films, such as \"Kal Ho Naa Ho\", \"Tujhe Dekha To\", and \"Sandese Aate Hain\"\r\nSome of Nigam\'s own compositions, such as \"Saathiya\" and \"Abhi Mujh Mein Kahi\"\r\nA live band and a troupe of dancers\r\nA visually stunning stage set\r\nAn energetic and interactive performance\r\nThe Sonu Nigam Show is a once-in-a-lifetime opportunity to see one of India\'s greatest singers live in concert. Tickets are on sale now, so don\'t miss out!','The Sonu Nigam Show In Dallas','shows/1753905018.jpg','2023-08-18 19:00:00','2023-08-18 19:00:00',1000.00,0,0,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-07-30 14:50:18','2025-08-06 02:48:59'),(16,'“Zindagi Ka Safar” with Anupam Kher','zindagi-ka-safar-with-anupam-kher',2,16,'general_admission',0,'Embark on a unique journey through life\'s experiences with the legendary Anupam Kher in \"Zindagi Ka Safar.\" Join us for an insightful and inspiring evening as Mr. Kher shares his wisdom, anecdotes, and the lessons he\'s learned along the way. Get ready to be captivated by his storytelling and unparalleled charisma as he takes you on a heartwarming expedition through the ups and downs of life. Don\'t miss this opportunity to be part of an unforgettable event filled with laughter, reflection, and a deeper understanding of \"Zindagi Ka Safar\" - the journey of life. Secure your tickets now for this enriching and memorable experience!','“Zindagi Ka Safar” with Anupam Kher','shows/1753905616.png','2021-08-27 19:00:00','2021-08-27 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-07-30 15:00:16','2025-08-06 02:46:48'),(18,'Ankit Tiwari Live in Dhaka','ankit-tiwari-live-in-dhaka',2,18,'general_admission',0,'Ankit Tiwari Live in Dhaka','Ankit Tiwari Live in Dhaka','shows/1753908271.jpg','2019-07-19 19:00:00','2019-07-19 23:00:00',100.00,0,0,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-07-30 15:44:31','2025-08-06 02:57:27'),(19,'Rangotsav Holi Festival - Atlanta','rangotsav-holi-festival-atlanta',2,5,'general_admission',0,'Put Your Hands Up, Atlanta! Join the Hrithik Roshan Tour at Global Mall in Atlanta, GA\r\n\r\nPrepare yourself for a unique Holi festival experience with a special meet and greet with the incredibly charismatic Hrithik Roshan. Hrithik Roshan, renowned for his exceptional dancing skills, has starred in numerous Bollywood hits like \"Kaho Naa... Pyaar Hai,\" \"Dhoom 2,\" and \"War.\" He is not only an acclaimed actor but also a celebrated style icon and philanthropist. This festival promises an unforgettable experience, allowing fans to interact with Hrithik and enjoy a vibrant display of colors, music, and dance.\r\n\r\nA huge thank you to BK Bindu Kohli, Rina Gupta & National Sponsor Fusion Group Games for adding extra magic to this grand celebration.\r\n\r\nEvent Details:\r\n\r\nVenue: Global Mall 5675 Jimmy Carter Blvd, Norcross, GA 30071\r\nMore Info: Visit 3sixtyshows.com\r\nTickets: starting $30 at 3SixtyShows.com                                           Prepare yourself for a unique Holi festival experience with a special meet and greet with the incredibly charismatic Hrithik Roshan. Don’t miss out on this unforgettable celebration starting with a bang!','Rangotsav Holi Festival - Atlanta','shows/1753989749.jpg','2025-04-04 19:00:00','2025-04-04 23:00:00',1000.00,0,1,'past','Hrithic Roshan','[]',NULL,NULL,0,0,NULL,'2025-07-31 14:22:29','2025-08-06 02:56:01'),(20,'Rangotsav Holi Festival - Bay Area','rangotsav-holi-festival-bay-area',2,7,'general_admission',0,'Put Your Hands Up, Bay Area! Join the Hrithik Roshan Tour Kickoff\r\n\r\nPrepare yourself for a unique Holi festival experience with a special meet and greet with the incredibly charismatic Hrithik Roshan. Hrithik Roshan, renowned for his exceptional dancing skills, has starred in numerous Bollywood hits like \"Kaho Naa... Pyaar Hai,\" \"Dhoom 2,\" and \"War.\" He is not only an acclaimed actor but also a celebrated style icon and philanthropist. This festival promises an unforgettable experience, allowing fans to interact with Hrithik and enjoy a vibrant display of colors, music, and dance.\r\n\r\nEvent Details:\r\nApril 13th - Sunday\r\n \r\n\r\nVenue: TBD\r\nMore Info: Visit 3sixtyshows.com\r\nTickets: Available\r\n \r\n\r\nPrepare yourself for a unique Holi festival experience with a special meet and greet with the incredibly charismatic Hrithik Roshan. Don’t miss out on this unforgettable celebration starting with a bang!','Rangotsav Holi Festival - Bay Area','shows/1753990509.jpg','2025-04-13 19:00:00','2025-04-13 23:00:00',1000.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-07-31 14:35:09','2025-08-06 02:55:34'),(21,'Rangotsav Holi Festival -Dallas','rangotsav-holi-festival-dallas',2,19,'general_admission',0,'Put Your Hands Up, Dallas! Join the Hrithik Roshan Tour Kickoff\r\n\r\nPrepare yourself for a unique Holi festival experience with a special meet and greet with the incredibly charismatic Hrithik Roshan. Hrithik Roshan, renowned for his exceptional dancing skills, has starred in numerous Bollywood hits like \"Kaho Naa... Pyaar Hai,\" \"Dhoom 2,\" and \"War.\" He is not only an acclaimed actor but also a celebrated style icon and philanthropist. This festival promises an unforgettable experience, allowing fans to interact with Hrithik and enjoy a vibrant display of colors, music, and dance.\r\n\r\nA huge thank you to FunAsiA Radio & TopShot Events for adding extra magic to this grand celebration.\r\n\r\nEvent Details:\r\n\r\nVenue: Music City\r\nMore Info: Visit 3sixtyshows.com\r\nTickets: Available\r\n \r\n\r\nPrepare yourself for a unique Holi festival experience with a special meet and greet with the incredibly charismatic Hrithik Roshan. Don’t miss out on this unforgettable celebration starting with a bang!\r\n\r\n \r\n\r\nSponsors and Partners:\r\n\r\nFusion Group Games\r\n#truPayment\r\nPatel Brothers\r\nDesi District\r\nBK Khan\'s BBQ\r\nCeleb Bazaar\r\n \r\n\r\n🧨✨\r\n\r\nFusion Group Games\r\n#truPayment\r\nPatel Brothers\r\nDesi District\r\nBK Khan\'s BBQ\r\nCeleb Bazaar','Rangotsav Holi Festival -Dallas','shows/1753999544.jpg','2025-04-05 19:01:00','2025-04-05 23:01:00',1000.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-07-31 17:05:44','2025-08-06 02:55:10'),(22,'Rangotsav Holi Festival - Houston','rangotsav-holi-festival-houston',2,2,'general_admission',0,'Put Your Hands Up, Houston! Join the Hrithik Roshan Tour Kickoff\r\n\r\nPrepare yourself for a unique Holi festival experience with a special meet and greet with the incredibly charismatic Hrithik Roshan. Hrithik Roshan, renowned for his exceptional dancing skills, has starred in numerous Bollywood hits like \"Kaho Naa... Pyaar Hai,\" \"Dhoom 2,\" and \"War.\" He is not only an acclaimed actor but also a celebrated style icon and philanthropist. This festival promises an unforgettable experience, allowing fans to interact with Hrithik and enjoy a vibrant display of colors, music, and dance.\r\n\r\nEvent Details:\r\n\r\nVenue: BH Ranch\r\nMore Info: Visit 3sixtyshows.com\r\nTickets: Available\r\n \r\n\r\nPrepare yourself for a unique Holi festival experience with a special meet and greet with the incredibly charismatic Hrithik Roshan. Don’t miss out on this unforgettable celebration starting with a bang!','Rangotsav Holi Festival - Houston','shows/1753999741.jpg','2025-04-06 11:00:00','2025-04-06 13:00:00',1000.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-07-31 17:09:02','2025-08-05 15:03:37'),(23,'Rangotsav Holi Festival - New Jersey','rangotsav-holi-festival-new-jersey',2,9,'general_admission',0,'Put Your Hands Up, New Jersey! Join the Hrithik Roshan Tour Kickoff\r\n\r\nPrepare yourself for a unique Holi festival experience with a special meet and greet with the incredibly charismatic Hrithik Roshan. Hrithik Roshan, renowned for his exceptional dancing skills, has starred in numerous Bollywood hits like \"Kaho Naa... Pyaar Hai,\" \"Dhoom 2,\" and \"War.\" He is not only an acclaimed actor but also a celebrated style icon and philanthropist. This festival promises an unforgettable experience, allowing fans to interact with Hrithik and enjoy a vibrant display of colors, music, and dance.\r\n\r\nA huge thank you to Atique B Sheikh of Worldstar for adding extra magic to this grand celebration.\r\n\r\nEvent Details:\r\n\r\nVenue: April 10th Royal Ballroom, NJ\r\nMore Info: Visit 3sixtyshows.com\r\nTickets: Available\r\n \r\n\r\nPrepare yourself for a unique Holi festival experience with a special meet and greet with the incredibly charismatic Hrithik Roshan. Don’t miss out on this unforgettable celebration starting with a bang!','Rangotsav Holi Festival - New Jersey','shows/1753999964.jpg','2025-04-10 19:00:00','2025-04-10 23:00:00',1000.00,NULL,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-07-31 17:12:44','2025-08-05 14:57:55'),(24,'AAJ Ki Raat NYE Dallas 2025','aaj-ki-raat-nye-dallas-2025',2,6,'general_admission',0,'🎉 Celebrate New Year\'s Eve like never before at \"Aaj Ki Raat NYE 2025\"! Featuring the sensational Tamannaah Bhatia,this glamorous night promises unforgettable entertainment, delicious food by BK Kabab BBQ, and memories captured by Studio 3Sixty. Powered by Fusion Group Games and Car Cost Control, with grand sponsors like Patel Brothers, this event is the perfect way to ring in the new year. Don\'t miss out—grab your tickets now at www.3sixtyshows.com or call 855.360.SHOWS for more info! \r\n\r\n*𝐀𝐚𝐣 𝐊𝐢 𝐑𝐚𝐚𝐭 𝟐𝟎𝟐𝟓*\r\n\r\n𝐁𝐢𝐠𝐠𝐞𝐬𝐭 𝐍𝐞𝐰 𝐘𝐞𝐚𝐫𝐬 𝐄𝐯𝐞 𝐏𝐚𝐫𝐭𝐲 𝐰𝐢𝐭𝐡 𝐓𝐫𝐞𝐧𝐝-𝐒𝐞𝐭𝐭𝐞𝐫 “𝐓𝐚𝐦𝐚𝐧𝐧𝐚𝐡 𝐁𝐡𝐚𝐭𝐢𝐚”!!!\r\n\r\n𝐒𝐚𝐯𝐞 𝐭𝐡𝐞 𝐝𝐚𝐭𝐞 𝐚𝐧𝐝 𝐠𝐞𝐭 𝐫𝐞𝐚𝐝𝐲 𝐭𝐨 𝐣𝐨𝐢𝐧 𝐮𝐬','AAJ Ki Raat NYE Dallas 2025','shows/1754034162.jpg','2024-12-31 19:00:00','2024-12-31 23:45:00',1000.00,0,0,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-01 02:42:42','2025-08-06 02:54:44'),(25,'SID SRIRAM DALLAS','sid-sriram-dallas',2,12,'general_admission',0,'Get ready to be serenaded by the sensational voice of Sid Sriram in Dallas! Join us for an enchanting evening filled with soulful melodies and heartwarming performances. Sid Sriram, known for his captivating vocals and chart-topping hits, is all set to create musical magic on stage. Don\'t miss this chance to witness his extraordinary talent live in Dallas. Secure your tickets now for an unforgettable night of music and emotions with Sid Sriram!','SID SRIRAM DALLAS','shows/1754034692.webp','2023-09-22 19:00:00','2023-09-22 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-01 02:51:32','2025-08-06 02:42:39'),(26,'Bismil Ki Mehfil Dallas 2023','Bismil Ki Mehfil Dallas 2023',2,4,'general_admission',0,'Introduction\r\n\r\nBismil, a name that resonates with soulful melodies and heartfelt emotions, has taken the music world by storm. His unique blend of Sufi, Bollywood, and folk music has captivated audiences across the globe. In this article, we delve into the life, music, and mesmerizing performances of this extraordinary artist.\r\n\r\nWho Is Bismil?\r\n\r\nBorn with an innate passion for music, Bismil embarked on his musical journey at a young age. His soulful voice and ability to infuse traditional Sufi tunes with contemporary elements set him apart. Bismil’s dedication to promoting Sufi-fusion music globally has earned him international acclaim.\r\n\r\nThe Concept\r\n\r\nTrue to its name, Bismil Ki Mehfil is more than just a concert; it’s an emotional experience. Imagine being transported to a mystical gathering where intricate emotions intertwine to form beautiful melodies. Bismil’s performances evoke the same feelings that kings of the past must have felt while witnessing their kingdom’s remarkable performers.\r\n\r\nThe Setup\r\n\r\nEvery detail matters in Bismil’s shows. The placement of each instrumentalist and vocalist is meticulously planned to complement and uplift the entire ensemble. The stage becomes a canvas where music, passion, and artistry converge.\r\n\r\nSold-Out Shows and Social Media Craze\r\n\r\nWhat began as a small setup with a few shows has now blossomed into a nationwide phenomenon. Bismil’s tours crisscross the country, with sold-out shows leaving audiences spellbound. His social media presence amplifies the fervor, making him a sensation among music lovers.\r\n\r\nTimes Square Billboard Feature\r\n\r\nBismil’s face graced the iconic Times Square billboard, a testament to his dedication and talent. As the first Indian artist to champion Sufi-fusion music globally, Bismil’s journey is awe-inspiring.\r\n\r\nUnforgettable Concerts\r\n\r\nFrom Mumbai to New York, Bismil’s concerts are unforgettable. Whether it’s the soul-stirring “Halka Halka Suroor” or the timeless “Kali Kali Zulfon Ke Phande,” each performance leaves an indelible mark.\r\n\r\nConclusion\r\n\r\nBismil’s music transcends boundaries, touching hearts and souls. His Mehfil is not just a gathering; it’s a celebration of life, love, and music. As Bismil continues to enchant audiences worldwide, we eagerly await the next chapter in his musical odyssey.\r\n\r\nNote: If you’d like to attend a Bismil Ki Mehfil show, check out the upcoming events on https://www.3sixtyshows.com Don’t miss the chance to be part of this magical experience! 🎶🌟','Bismil Ki Mehfil Dallas','shows/1754035799.png','2023-04-29 19:00:00','2023-04-29 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-01 03:10:00','2025-08-06 02:40:52'),(27,'Bismil Ki Mehfil Dallas 2024','bismil-ki-mehfil-dallas-2024',2,21,'general_admission',0,'Indulge in the enchanting ambiance of \"Bismil ki Mehfil,\" a celebration of poetry, music, and artistry. This event promises an evening filled with soul-stirring performances and profound expressions of creativity. Join us as talented poets, musicians, and artists come together to create an unforgettable experience, where emotions flow freely, and hearts are touched. Immerse yourself in the world of \"Bismil ki Mehfil\" and let the power of words and music take you on a journey of inspiration and introspection. Don\'t miss this unique gathering of talent and passion!','Bismil Ki Mehfil Dallas 2024','shows/1754036419.jpg','2024-04-21 19:00:00','2024-04-21 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-01 03:20:19','2025-08-06 02:54:09'),(28,'ATIF ASLAM LIVE IN DALLAS','atif-aslam-live-in-dallas',2,12,'general_admission',0,'Experience the magic of Atif Aslam live in Dallas! Join us for an unforgettable night filled with his chart-topping hits and soulful melodies. Get your tickets now for an epic musical journey.','ATIF ASLAM LIVE IN DALLAS','shows/1754045947.jpg','2023-10-28 19:00:00','2023-10-28 23:00:00',100.00,0,0,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-01 05:59:07','2025-08-06 02:53:38'),(29,'LOL Tour Dallas','lol-tour-dallas',2,22,'general_admission',0,'Get ready to burst into laughter at the \"LOL Tour Dallas\"! Join us for an uproarious evening filled with side-splitting humor and comedy that will leave you in stitches. The best comedians are coming to town to deliver an unforgettable night of laughter and entertainment. Secure your tickets now for a comedy show that promises to tickle your funny bone and make your night a memorable one at the \"LOL Tour Dallas\"!','LOL Tour Dallas','shows/1754060265.jpg','2023-06-18 19:00:00','2023-06-18 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-01 09:57:46','2025-08-06 02:31:52'),(30,'AR Rahman live in Concert','ar-rahman-live-in-concert',2,12,'general_admission',0,'AR Rahman live in Concert','AR Rahman live in Concert','shows/1754077352.jpg','2023-09-08 19:00:00','2023-09-08 23:00:00',100.00,0,0,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-01 14:42:32','2025-08-06 02:53:13'),(31,'NEHA KAKKAR Live in DALLAS','neha-kakkar-live-in-dallas',1,12,'general_admission',0,'Prepare for an exhilarating musical extravaganza as the sensational Neha Kakkar takes center stage in Dallas! Join us for a night of pure Bollywood magic with \"NEHA KAKKAR Live in DALLAS.\" Neha Kakkar, known for her chart-topping hits and dynamic performances, is all set to dazzle the audience with her powerhouse vocals and infectious energy. Don\'t miss this opportunity to be part of an unforgettable evening. Grab your tickets now and get ready to dance, sing, and celebrate with one of India\'s biggest music sensations!','NEHA KAKKAR Live in DALLAS','shows/1754078127.jpeg','2022-07-08 19:00:00','2022-07-08 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-01 14:55:27','2025-08-06 02:44:43'),(32,'The Intense Tour - Indian Idols 12 (Dallas)','the-intense-tour-indian-idols-12-dallas',2,22,'general_admission',0,'Get ready for a night of electrifying performances as the Indian Idol 12 contestants hit the stage in Dallas for \"The Intense Tour.\" Join us for an unforgettable evening filled with soulful singing, incredible talent, and high-energy entertainment. Witness the rising stars of Indian music as they showcase their vocal prowess and leave you in awe. Don\'t miss this chance to experience the magic of Indian Idol 12 live in Dallas. Secure your tickets now for a musical journey like no other!','The Intense Tour - Indian Idols 12 (Dallas)','shows/1754078421.jpg','2022-03-11 19:00:00','2022-03-11 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-01 15:00:22','2025-08-06 02:44:01'),(33,'Eid Mela with Ali Quli Mirza & Alamgir','eid-mela-with-ali-quli-mirza-alamgir',2,23,'general_admission',0,'Celebrate Eid like never before at the \"Eid Mela with Ali Quli Mirza & Alamgir\"! Join us for a joyous event featuring the enchanting voices of Ali Quli Mirza and the legendary Alamgir. Get ready for an evening of soulful music, delicious food, and festive fun. Secure your tickets today and make this Eid truly memorable!','Eid Mela with Ali Quli Mirza & Alamgir','shows/1754078876.png','2022-05-07 19:00:00','2022-05-07 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-01 15:07:56','2025-08-06 02:37:33'),(34,'Arijit Singh Show Talent Hunt','arijit-singh-show-talent-hunt',2,5,'general_admission',0,'Get ready for the \"Arijit Singh Show Talent Hunt,\" where dreams meet destiny! This exciting event invites aspiring singers to showcase their vocal prowess and compete for a chance to shine in the spotlight. Join us for a thrilling competition that promises to discover the next singing sensation. Whether you\'re a contestant or a music enthusiast, this talent hunt is an opportunity you won\'t want to miss. Stay tuned for details on auditions and be part of this incredible journey to stardom with Arijit Singh!','Arijit Singh Show Talent Hunt','shows/1754079710.jpg','2022-05-08 19:00:00','2022-05-08 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-01 15:21:50','2025-08-06 02:36:14'),(35,'Padma Bhushan Gulzar Live in Ijaazat','padma-bhushan-gulzar-live-in-ijaazat',2,24,'general_admission',0,'Padma Bhushan Gulzar Live in Ijaazat : An Evening of Music, Ghazal and Poetry with Legend Himself','Padma Bhushan Gulzar Live in Ijaazat','shows/1754080647.jpg','2022-07-24 19:00:00','2022-07-24 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-01 15:37:27','2025-08-06 02:52:04'),(36,'Grand Tour 2022 Afsana Khan, Saajz, Khuda Baksh','grand-tour-2022-afsana-khan-saajz-khuda-baksh',2,13,'general_admission',0,'Prepare for a grand musical spectacle as Afsana Khan, Saajz, and Khuda Baksh take center stage in Dallas for the \"Grand Tour 2022.\" Join us for a night of electrifying performances, soulful melodies, and captivating vocals that will leave you in awe. These talented artists promise an unforgettable evening filled with chart-topping hits and energetic entertainment. Secure your tickets now for a musical journey like no other, and get ready to groove to the beats of this sensational lineup live in Dallas!','Grand Tour 2022 Afsana Khan, Saajz, Khuda Baksh','shows/1754081901.jpg','2022-06-12 19:00:00','2022-06-12 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-01 15:58:21','2025-08-06 02:32:58'),(37,'ARIJIT SINGH LIVE IN CONCERT 2017','arijit-singh-live-in-concert-2017',2,12,'general_admission',0,'ARIJIT SINGH LIVE IN CONCERT','ARIJIT SINGH LIVE IN CONCERT','shows/1754082185.jpg','2017-04-23 19:00:00','2017-04-23 23:00:00',100.00,1000,0,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-01 16:03:05','2025-08-01 16:05:22'),(38,'ARIJIT SINGH LIVE IN CONCERT 2015','arijit-singh-live-in-concert-2015',2,12,'general_admission',0,'ARIJIT SINGH LIVE IN CONCERT 2015','ARIJIT SINGH LIVE IN CONCERT 2015','shows/1754082437.jpg','2015-10-04 19:00:00','2015-10-04 23:00:00',100.00,1000,1,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-01 16:07:17','2025-08-01 16:07:17'),(42,'Ghazal Night on Valentines Day 14 Feb 2020','ghazal-night-on-valentines-day-14-feb-2020',2,13,'general_admission',0,'Ghazal Night on Valentines Day','Ghazal Night on Valentines Day','shows/1754083527.jpg','2020-02-14 19:00:00','2020-02-14 23:00:00',100.00,NULL,1,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-01 16:25:27','2025-08-01 16:25:27'),(43,'Mere Pass Tum Ho-Dallas Evening Gala','mere-pass-tum-ho-dallas-evening-gala',2,25,'general_admission',0,'Prepare for an evening of love, drama, and entertainment at the \"Mere Pass Tum Ho - Dallas Evening Gala.\" Step into the world of one of Pakistan\'s most beloved television dramas, as we bring the characters and emotions to life on stage. Join us for a captivating event that promises to immerse you in the heart-wrenching story and unforgettable performances. Don\'t miss this unique opportunity to experience \"Mere Pass Tum Ho\" in a gala setting in Dallas. Secure your tickets now and be part of this extraordinary evening filled with passion and intrigue.','Mere Pass Tum Ho-Dallas Evening Gala','shows/1754083998.jpg','2020-03-13 19:00:00','2020-03-13 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-01 16:33:18','2025-08-06 02:43:20'),(44,'A Charity Gala Evening with Hrithik Roshan','a-charity-gala-evening-with-hrithik-roshan',2,26,'general_admission',0,'Don\'t miss the opportunity to attend a memorable \"Charity Gala Evening with Hrithik Roshan.\" Join us for an exclusive event featuring the Bollywood superstar, Hrithik Roshan, as we come together for a noble cause. Enjoy an evening of glamour, entertainment, and philanthropy, all in the company of one of India\'s most beloved actors. Secure your tickets now for this star-studded night dedicated to making a positive impact.','A Charity Gala Evening with Hrithik Roshan','shows/1754084804.jpg','2020-01-25 19:00:00','2020-01-25 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-01 16:46:44','2025-08-06 02:39:20'),(45,'New Year\'s Eve Party - 31st Night With Urvashi Rautela','new-years-eve-party-31st-night-with-urvashi-rautela',2,28,'general_admission',0,'New Year\'s Eve Party - 31st Night With Urvashi Rautela','New Year\'s Eve Party - 31st Night With Urvashi Rautela','shows/1754085466.jpg','2020-12-31 19:00:00','2020-12-31 23:00:00',100.00,1000,1,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-01 16:57:46','2025-08-01 16:57:46'),(46,'Guru Randhawa Sensational Tour Dallas','guru-randhawa-sensational-tour-dallas',2,29,'general_admission',0,'Get ready for a sensational night of music and entertainment as Guru Randhawa takes the stage in Dallas! Join us for the \"Guru Randhawa Sensational Tour Dallas\" and experience the magic of one of India\'s most iconic music sensations. With chart-topping hits and a magnetic stage presence, Guru Randhawa promises an unforgettable evening of non-stop fun and dance. Secure your tickets now for a musical journey like no other, and get ready to groove to the beats of this Punjabi pop sensation!','Guru Randhawa Sensational Tour Dallas','shows/1754085953.jpg','2019-07-26 19:00:00','2019-07-26 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-01 17:05:53','2025-08-06 02:45:51'),(47,'Dabangg Reloaded - Dubai','dabangg-reloaded-dubai',2,30,'general_admission',0,'Dabangg Reloaded - Dubai','Dabangg Reloaded - Dubai','shows/1754086251.jpg','2019-03-15 19:00:00','2019-03-15 23:00:00',100.00,1000,0,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-01 17:10:51','2025-08-03 02:22:11'),(48,'Biggest Bollywood Party By \"Akbar Sami\"','biggest-bollywood-party-by-akbar-sami',2,14,'general_admission',0,'Get ready for the ultimate Bollywood party of the year with none other than the No. 1 DJ of India, Akbar Sami! Join us for a night of non-stop music, dance, and entertainment at the \"Biggest Bollywood Party.\" DJ Akbar Sami will take you on a musical journey through the best Bollywood beats, making sure the dance floor is sizzling all night long. Don\'t miss this chance to groove to the hottest tracks and experience the magic of India\'s top DJ. Grab your tickets now and get ready to dance the night away!','Biggest Bollywood Party By \"Akbar Sami\"','shows/1754086556.jpg','2019-04-06 19:00:00','2019-04-06 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-01 17:15:56','2025-08-06 02:40:11'),(49,'Sonu Nigam & Neha Kakkar Live in Dallas','sonu-nigam-neha-kakkar-live-in-dallas',2,14,'general_admission',0,'Get ready to be serenaded by two of India\'s most sensational voices at \"Sonu Nigam & Neha Kakkar Live in Dallas\"! Join us for an unforgettable evening filled with soulful melodies and high-energy performances. Sonu Nigam and Neha Kakkar are all set to captivate the audience with their chart-topping hits and charismatic stage presence. Don\'t miss this chance to witness these musical maestros live on stage. Secure your tickets now for an enchanting night of Bollywood magic in Dallas!','Sonu Nigam & Neha Kakkar Live in Dallas','shows/1754087480.jpg','2019-04-07 19:00:00','2019-04-07 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-01 17:31:20','2025-08-06 02:35:06'),(51,'Shreya Ghoshal- Dallas','shreya-ghoshal-dallas',2,12,'general_admission',0,'Experience the enchanting melodies of Shreya Ghoshal in Dallas! Join us for a mesmerizing evening as the iconic Bollywood playback singer graces the stage with her soulful voice. Get ready to be transported to a world of musical bliss as Shreya Ghoshal performs her chart-topping hits and timeless classics. Don\'t miss this opportunity to witness her incredible talent live in Dallas. Secure your tickets now for a night filled with unforgettable music and moments.','Shreya Ghoshal- Dallas','shows/1754088087.jpg','2019-08-25 19:00:00','2019-08-25 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-01 17:41:27','2025-08-06 02:34:29'),(52,'ALI ZAFAR Live in Dallas','ali-zafar-live-in-dallas',2,24,'general_admission',0,'Get ready for an unforgettable musical experience as Ali Zafar takes the stage in Dallas! Join us for a night filled with soulful melodies, electrifying performances, and the magic of Ali Zafar\'s music. With his captivating vocals and chart-topping hits, he\'s all set to create an atmosphere of pure musical delight. Don\'t miss this chance to witness the extraordinary talent of Ali Zafar live in Dallas. Secure your tickets now for an evening that promises to be a musical journey like no other!','ALI ZAFAR Live in Dallas','shows/1754089338.jpg','2022-07-27 19:00:00','2022-07-27 23:00:00',1000.00,NULL,1,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-01 18:02:18','2025-08-01 18:02:18'),(53,'New Years Eve\'s with Chitrangada Singh in Dallas','new-years-eves-with-chitrangada-singh-in-dallas',2,20,'general_admission',0,'Ring in the New Year with glamour and style at \"New Year\'s Eve with Chitrangada Singh in Dallas\"! Join us for an exclusive and unforgettable night as we welcome the year ahead in the company of the Bollywood icon, Chitrangada Singh. Experience a dazzling evening of entertainment, music, and celebration that promises to be the highlight of your year-end festivities. Secure your tickets now for a star-studded New Year\'s Eve event like no other, and make memories that will last a lifetime!','New Years Eve\'s with Chitrangada Singh in Dallas','shows/1754148981.jpg','2018-12-31 19:00:00','2018-12-31 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-02 10:36:22','2025-08-06 02:41:47'),(55,'ATIF ASLAM LIVE IN DALLAS 2017','atif-aslam-live-in-dallas-2017',1,31,'general_admission',0,'Experience the magic of Atif Aslam live in Dallas! Join us for an unforgettable night filled with his chart-topping hits and soulful melodies. Get your tickets now for an epic musical journey.','ATIF ASLAM LIVE IN DALLAS','shows/1754149636.jpg','2017-07-09 19:00:00','2017-07-09 23:00:00',100.00,1000,1,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-02 10:47:16','2025-08-02 10:47:16'),(56,'ARMAN MALIK LIVE','arman-malik-live',2,32,'general_admission',0,'Armaan Malik\r\n\r\nArmaan Malik Live in Concert: A Musical Journey at Majestic Theatre, Dallas\r\n\r\nGet ready to be serenaded by the soulful tunes of Armaan Malik as he takes the stage for an unforgettable live concert at the historic Majestic Theatre in Dallas! Join us for an evening of heartwarming melodies, electrifying performances, and a musical journey that will touch your soul.','ARMAN MALIK LIVE','shows/1754150596.jpg','2016-09-11 19:00:00','2016-09-11 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-02 11:03:16','2025-08-06 02:51:34'),(57,'ARIF LOHAR JUGNI','arif-lohar-jugni',2,13,'general_admission',0,'ARIF LOHAR JUGNI','ARIF LOHAR JUGNI','shows/1754154772.jpg','2016-05-28 19:00:00','2016-05-28 23:00:00',100.00,1000,1,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-02 12:12:53','2025-08-02 12:12:53'),(58,'DOSTI MELA KAILASA JUGNI','dosti-mela-kailasa-jugni',1,14,'general_admission',0,'DOSTI MELA KAILASA JUGNI','DOSTI MELA KAILASA JUGNI','shows/1754155158.jpg','2016-08-14 19:00:00','2016-08-14 23:00:00',100.00,1000,1,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-02 12:19:18','2025-08-02 12:19:18'),(59,'HEART THROBS BREATHLESS LIVE IN CONCERT','heart-throbs-breathless-live-in-concert',2,33,'general_admission',0,'HEART THROBS BREATHLESS LIVE IN CONCERT','HEART THROBS BREATHLESS LIVE IN CONCERT','shows/1754156877.jpg','2004-04-09 19:00:00','2004-04-09 23:00:00',100.00,1000,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-02 12:47:57','2025-08-03 02:17:55'),(60,'WHAT\'S DONE IS DONE','whats-done-is-done',1,32,'general_admission',0,'WHAT\'S DONE IS DONE','WHAT\'S DONE IS DONE','shows/1754157451.jpg','2016-07-16 19:00:00','2016-07-16 23:00:00',100.00,1000,1,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-02 12:57:31','2025-08-02 12:57:31'),(61,'NAVRATRI GARBA MOHATSAV 2016','navratri-garba-mohatsav-2016',1,34,'general_admission',0,'NAVRATRI GARBA MOHATSAV 2016','NAVRATRI GARBA MOHATSAV 2016','shows/1754160423.jpg','2016-09-18 19:00:00','2016-09-18 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-02 13:47:03','2025-08-06 02:50:43'),(62,'CHAND RAAT TEHSEEN JAVED','chand-raat-tehseen-javed',1,36,'general_admission',0,'CHAND RAAT TEHSEEN JAVED','CHAND RAAT TEHSEEN JAVED','shows/1754169127.jpg','2018-06-14 19:00:00','2018-06-14 23:00:00',100.00,1000,0,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-02 16:12:07','2025-08-02 16:12:07'),(63,'ORCHESTRA MUSIC LOVERS DALLAS PALMS','orchestra-music-lovers-dallas-palms',1,25,'general_admission',0,'ORCHESTRA MUSIC LOVERS DALLAS PALMS','ORCHESTRA MUSIC LOVERS DALLAS PALMS','shows/1754218953.jpg','2023-04-12 19:00:00','2023-04-12 23:00:00',100.00,1000,1,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-03 06:02:34','2025-08-03 06:02:34'),(64,'Salim Sulaiman Live','salim-sulaiman-live',1,24,'general_admission',0,'Celebrate Independence Day with the electrifying music of Salim-Sulaiman live in Dallas, TX! This renowned Indian composer duo will be performing their popular hits on July 4th weekend. Get ready for an unforgettable night of music and dance. Stay tuned for ticketing and venue details coming soon!\r\n\r\nExperience the Musical Extravaganza with Salim-Sulaiman Live in Concert!\r\n\r\nGet ready to be swept off your feet by the dynamic duo of Indian music, Salim-Sulaiman, as they bring their electrifying concert to your city! Known for their soul-stirring compositions and high-energy performances, Salim-Sulaiman have become synonymous with some of the most iconic soundtracks in Bollywood.\r\n\r\nAs you step into the realm of rhythm and melodies, prepare to be greeted by chart-topping hits like \"Chak De India,\" the anthem that fueled the spirit of a nation, and \"Shukran Allah\" from the movie \'Kurbaan,\' a song that touches the heart with its message of gratitude and love. Let the enchanting tunes of \"Mar Jawaan\" from \'Fashion\' captivate you, and the romantic vibes of \"Ainvayi Ainvayi\" from \'Band Baaja Baaraat\' get you grooving.\r\n\r\nSalim-Sulaiman\'s music is not just about the beats; it\'s a journey through a spectrum of emotions, from the intense \"Kurbaan Hua\" to the uplifting \"Ali Maula.\" Their versatility spans across various genres, making every concert a unique experience that resonates with people of all ages.\r\n\r\nThe concert will also feature mesmerizing performances by talented vocalists who have frequently collaborated with the duo, bringing to life the songs that have defined a generation. Expect to hear a blend of cinematic, folk, electronica, and Sufi influences that have become the hallmark of Salim-Sulaiman\'s music.\r\n\r\nDon\'t miss the chance to be part of this musical celebration. Join us for an evening where music transcends boundaries and unites hearts. Book your tickets now and be ready to witness the magic of Salim-Sulaiman live in concert! It\'s more than just a concert; it\'s an experience that will leave you with memories to cherish for a lifetime. See you there!','Salim Sulaiman Live','shows/1754219630.jpg','2024-07-03 19:00:00','2024-07-03 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-03 06:13:50','2025-08-05 16:05:12'),(65,'DA-BANGG THE TOUR RELOADED 2018','da-bangg-the-tour-reloaded-2018',1,37,'general_admission',0,'DA-BANGG THE TOUR RELOADED \r\nLive at American Airlines Center Dallas\r\nJune, 29 2018','DA-BANGG THE TOUR RELOADED 2018','shows/1754222925.jpg','2018-06-29 19:00:00','2018-06-29 23:00:00',100.00,1000,1,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-03 07:08:46','2025-08-03 07:08:46'),(66,'2017 NEW YEAR EVENING DALLAS','2017-new-year-evening-dallas',1,38,'general_admission',0,'2017 NEW YEAR EVENING DALLAS','2017 NEW YEAR EVENING DALLAS','shows/1754224199.jpg','2016-12-31 19:00:00','2016-12-31 23:00:00',100.00,1000,1,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-03 07:29:59','2025-08-03 07:29:59'),(67,'FARHAN PAPON LIVE','farhan-papon-live',1,12,'general_admission',0,'FARHAN PAPON LIVE\r\nZINDA TOUR\r\nFIRST TIME \r\nLIVE IN DALLAS','FARHAN PAPON LIVE','shows/1754224837.jpg','2016-05-20 19:00:00','2016-05-20 23:00:00',100.00,1000,1,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-03 07:40:38','2025-08-03 07:40:38'),(68,'HERO NO 1 GOVINDA','hero-no-1-govinda',1,1,'general_admission',0,'HERO NO 1 GOVINDA\r\nLIVE ON MAY 26 2024\r\nALI BABA HOKAH LOUNGE IRVING\r\n\r\nStep into the vibrant world of Bollywood and experience an evening of glamour and charisma with the legendary Govinda! 3SixtyShows invites you to an exclusive meet and greet event on May 26th, at the Alibaba Hookah Lounge in Irving where you can rub shoulders with one of India\'s most adored film stars. Known for his dynamic dance moves and infectious energy, Govinda has captured hearts across the globe, and now it\'s your chance to witness his magnetic personality up close. This is a once-in-a-lifetime opportunity to interact with a superstar who has redefined the Hindi film industry with his unique style and performances. Don\'t miss out on the chance to create unforgettable memories, take photos, and enjoy an evening filled with excitement and star power. Mark your calendars and be part of this stellar event!','HERO NO 1 GOVINDA','shows/1754225954.jpg','2024-05-26 19:00:00','2024-05-26 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-03 07:59:14','2025-08-05 16:03:58'),(69,'JAVED AKHTAR','javed-akhtar',1,13,'general_admission',0,'JAVED AKHTAR\r\nLIVE ON OCTOBER 28 2018\r\nAT GRAND CENTER','JAVED AKHTAR','shows/1754226476.jpg','2018-10-28 19:00:00','2018-10-28 23:00:00',100.00,NULL,1,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-03 08:07:56','2025-08-03 08:07:56'),(70,'JUBIN NAUTIYAL','jubin-nautiyal',1,11,'general_admission',0,'JUBIN NAUTIYAL\r\nON MARCH, 3RD 2024\r\nAT Rayleigh Underground\r\n\r\nJubin Nautiyal, the Bollywood sensation and the voice behind hit songs like \"Tum Hi Aana\" and \"Bewafa Tera Masoom Chehra\", is coming to Rayleigh Underground on Sunday, March 3rd for a spectacular concert.\r\n\r\nMost Happening Party In Vegas Style is happening on March 3rd.\r\n\r\nUpscale venue\r\nFresh locale\r\nConcert with Vegas-style flair\r\nMultilevel space featuring a patio\r\nExclusive access to a private bar\r\nComplimentary parking available\r\n**General admission $79**\r\n \r\n\r\nDon\'t miss this chance to see him live and enjoy his soulful melodies and energetic performances. Whether you are a fan of his romantic ballads or his upbeat dance numbers, you will find something to love in his diverse repertoire.\r\n\r\nTickets start from $79.00 and go up to $5,000.00 for VVIP packages that include meet and greet, photo opportunities, and premium seating. Hurry and book your tickets now before they sell out. This is a once-in-a-lifetime opportunity to witness the magic of Jubin Nautiyal in concert.','JUBIN NAUTIYAL','shows/1754226879.jpeg','2024-03-03 19:00:00','2024-03-03 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-03 08:14:39','2025-08-06 02:48:00'),(71,'MERA WO MATLAB NAHI THA','mera-wo-matlab-nahi-tha',1,39,'general_admission',0,'MERA WO MATLAB NAHI THA \r\nON AUGUST, 08 2015\r\nAT THE BLACK ACADEMY OF ARTS & LETTERS','MERA WO MATLAB NAHI THA','shows/1754227440.jpg','2015-08-08 19:00:00','2015-08-08 23:00:00',100.00,0,1,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-03 08:24:00','2025-08-03 08:24:00'),(74,'NYE PARTY URUASHI RAUTELA','nye-party-uruashi-rautela',1,28,'general_admission',0,'NYE PARTY URUASHI RAUTELA\r\nON DECEMBER 31 2020\r\nAT PALAZZO VERSACE DUBAI','NYE PARTY URUASHI RAUTELA','shows/1754228857.jpg','2020-12-31 19:00:00','2020-12-31 23:00:00',100.00,NULL,1,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-03 08:47:37','2025-08-03 08:47:37'),(75,'REKHA BHARDWAJ','rekha-bhardwaj',1,13,'general_admission',0,'REKHA BHARDWAJ','REKHA BHARDWAJ','shows/1754229180.jpg','2017-09-29 19:00:00','2017-09-29 23:00:00',100.00,NULL,1,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-03 08:53:00','2025-08-03 08:53:00'),(76,'SAJJAD ALI 2022','sajjad-ali-2022',1,14,'general_admission',0,'SAJJAD ALI 2022','SAJJAD ALI 2022','shows/1754229384.jpg','2022-10-23 19:00:00','2022-10-23 23:00:00',100.00,0,1,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-03 08:56:24','2025-08-03 08:56:24'),(77,'WOMEN EMPOWERMENT GALA 2017','women-empowerment-gala-2017',1,14,'general_admission',0,'WOMEN EMPOWERMENT GALA 2017','WOMEN EMPOWERMENT GALA 2017','shows/1754230597.jpg','2017-03-04 19:00:00','2017-03-04 23:00:00',100.00,0,1,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-03 09:16:37','2025-08-03 09:16:37'),(78,'Ek Shaam Rafi Ke Naam','ek-shaam-rafi-ke-naam',1,13,'general_admission',0,'Ek Shaam Rafi Ke Naam 🎶\r\n\r\nExperience a magical musical evening with the legendary Bankim Pathak and Daksha Gohil – a tribute to the golden voice of Mohammed Rafi! 🌟\r\n\r\n📅 June 21st\r\n\r\n📍 Grand Center, Plano, TX\r\n\r\n🎟️ Tickets: $35 | $55 | $75 | $100\r\n\r\n📞 Call: 214-679-6818 | 855-360-SHOW\r\n\r\n🌐 https://www.meraboxoffice.com/.../bankim-pathank-ek-shaam...\r\n\r\nPresented by 3Sixty Shows in association with True Payment Solution\r\n\r\nGrand Sponsor: Patel Brothers\r\n\r\n#EkShaamRafiKeNaam #BankimPathak #LiveConcert #PlanoEvents #IndianMusic #3SixtyShows #DakshaGohil #MohammedRafiTribute #MusicLovers #LiveMusicTexas','Ek Shaam Rafi Ke Naam','shows/1754411794.jpg','2024-06-21 19:00:00','2024-06-21 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-05 11:36:35','2025-08-05 14:29:28'),(79,'Game Changer Pre Release Event!','game-changer-pre-release-event',1,12,'general_admission',0,'he stage is set, and the anticipation is building! \r\n\r\nJoin us on December 21st as 3Sixty Shows celebrates the Game Changer Pre Release Event! 🎬🙌 \r\n\r\n#InThisTogether #3SixtyShows #gamechanger #charismadreamsentertainment Ram Charan Kiara Advani Thaman S S J Suryah Dil Raju TopShot Events Bindu Kohli','Game Changer Pre Release Event!','shows/1754425047.jpg','2024-12-21 19:00:00','2024-12-21 23:00:00',100.00,0,1,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-05 15:17:31','2025-08-05 15:17:31'),(80,'After Party - Ayushmann Khurranna','after-party-ayushmann-khurranna',1,11,'general_admission',0,'Experience the AFTER PARTY with Ayushmann Khurranna LIVE in Dallas! ✨🎤\r\n\r\nJoin us for an unforgettable evening as the multi-talented Ayushmann Khurranna brings his electrifying Bhava Live Show to Dallas! \r\n\r\nMark your calendars for November 24th and get ready to be mesmerized!\r\n\r\n📅 Date: November 24th, 2024\r\n🕒 Time: Doors open at 10:00pm to 2:00am \r\n\r\n📍 Venue: Rayleigh Underground, 316 W Las Colinas Blvd. Suite 100, Irving, TX 75039\r\n\r\nAyushmann Khurrana, acclaimed actor, singer, and performer, is known for his incredible versatility and chart-topping hits. This live show promises an eclectic mix of his biggest Bollywood numbers, soulful ballads, and a few surprises that will leave you wanting more!','After Party - Ayushmann Khurranna','shows/1754425575.png','2024-11-24 19:00:00','2024-11-24 23:00:00',100.00,0,1,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-05 15:26:15','2025-08-05 15:26:15'),(81,'Udit and Aditya Narayan Live in Concert','udit-and-aditya-narayan-live-in-concert',2,3,'general_admission',0,'On September 27th, 2024, the Udit Narayan and Aditya Narayan “Bade Miyan Chhote Miyan Concert” will grace the stage at the Credit Union of Texas Allen Event Center. This captivating musical event showcases the legendary Udit Narayan Jha, his exceptionally talented son Aditya Narayan Jha, and the melodious voice of Udit\'s wife, Deepa Narayan Jha.\r\n\r\nUdit Narayan Jha: An iconic figure in the world of music, Udit Narayan’s soulful and versatile singing has graced countless blockbuster movie soundtracks across various languages. With a career spanning over four decades, he has won numerous awards and captured the hearts of millions worldwide.\r\nAditya Narayan Jhan: Following in his father’s illustrious footsteps, Aditya is a multifaceted talent in the entertainment industry. As a singer, actor, and popular television host, he brings youthful energy and dynamic presence to every performance, resonating with audiences across generations.\r\nDeepa Narayan Jha: The talented singer and wife of Udit Narayan, Deepa adds a special charm to the concert with her melodious voice. Her duets with Udit Narayan are especially beloved, showcasing their beautiful synergy and mutual admiration.\r\nExpect an electrifying atmosphere backed by live bands, delivering hit songs, classic melodies, and new favorites. For aficionados and rookies alike, the Narayan family\'s concert is the talk of the town in Dallas, TX! It\'s an unmissable shindig for anyone who fancies a symphony of tunes that\'ll tickle your musical taste buds. 🎶✨\r\n\r\nDon’t miss out on this once-in-a-lifetime opportunity to witness their magic live! Secure your tickets and be part of this unforgettable musical journey. 🎤🎶','Udit and Aditya Narayan Live in Concert','shows/1754426341.jpeg','2024-09-27 19:00:00','2024-09-27 23:00:00',100.00,0,1,'past',NULL,'[]',NULL,NULL,0,0,NULL,'2025-08-05 15:39:01','2025-08-05 15:49:48'),(84,'Queen of Bollywood Madhuri Dixit','queen-of-bollywood-madhuri-dixit',1,33,'general_admission',0,'An Evening with Queen of Bollywood Madhuri Dixit\r\n\r\nThis is your chance to see Bollywood royalty live! Don’t miss An Evening with Queen of Bollywood Madhuri Dixit, presented by 3SIXTY Shows, FunAsia Entertainment, Diamond Productions. The event will be held at the Malachite Room at the Renaissance in Addison on August 9th at 9 pm.\r\n\r\nTickets can be purchased by calling 855-360-SHOWS or visiting https://www.3sixtyshows.com/.\r\n\r\nThe description mentions that the dress code is black tie/tux affair or Western/Desi formal. Food and photography partners are also listed.','Queen of Bollywood Madhuri Dixit','shows/1754427615.jpeg','2024-08-09 19:00:00','2024-08-09 23:00:00',100.00,0,1,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-05 16:00:15','2025-08-05 16:00:15'),(85,'Unforgettable 90s Kumar Sanu and Sadhana Sargam','unforgettable-90s-kumar-sanu-and-sadhana-sargam',2,3,'general_admission',0,'The \'90s were an era of iconic Bollywood music, and two legendary playback singers, Kumar Sanu and Sadhana Sargam, played a pivotal role in shaping its melodious landscape. Their soulful duets and solo hits continue to resonate with music lovers even today. If you’re a fan of timeless melodies, mark your calendars for May 12th, 2024, as these maestros take the stage at the Credit Union of Texas Event Center for an unforgettable musical experience.\r\n\r\nAbout Kumar Sanu\r\nKumar Sanu, a name synonymous with romance and emotions, has recorded over 20,000 songs in various languages. His voice has the power to evoke nostalgia and transport listeners back to the golden era of Bollywood.\r\nDid you know that Kumar Sanu holds the Guinness World Record for recording the most songs in a single day? In 1993, he mesmerized the world by lending his voice to 28 songs!\r\nHis collaboration with music producers Nadeem-Shravan resulted in the blockbuster soundtrack of the film “Aashiqui”, which sold a staggering 100 million albums. This marked the beginning of his reign as the king of playback singing.\r\n \r\nSadhana Sargam: The Melody Queen\r\n \r\n\r\nSadhana Sargam, with her versatile voice, ruled the \'90s music scene. Her renditions spanned genres, from romantic ballads to peppy dance numbers.\r\nShe lent her voice to several chart-topping hits, including songs from movies like “Hum Aapke Hain Koun…!”, “Raja Hindustani”, and “Pardes”.\r\nSadhana’s ability to convey emotions through her singing made her an irreplaceable part of Bollywood’s musical legacy.\r\n \r\nUnforgettable \'90s Concert Details\r\n \r\n\r\nDate: May 12th, 2024\r\nTime: Concert starts at 6:30 PM\r\nVenue: Credit Union of Texas Event Center\r\nTicket Information: Tickets are available for purchase at 3SixtyShows.com. Don’t miss this chance to witness Kumar Sanu and Sadhana Sargam recreate the magic of the \'90s on stage!\r\n \r\nHow to Get Tickets\r\n \r\n\r\nVisit 3SixtyShows.com.\r\nSelect the Unforgettable \'90s event on May 12th, 2024.\r\nChoose your preferred seating category.\r\nComplete the purchase process securely.\r\n \r\nConclusion\r\n \r\n\r\nPrepare to be swept away by the timeless melodies, relive the magic of the \'90s, and create memories that will stay with you forever. Kumar Sanu and Sadhana Sargam are ready to serenade you with their unforgettable tunes. Get your tickets now and be part of this musical journey! 🎶🎤🎵','Unforgettable 90s Kumar Sanu and Sadhana Sargam','shows/1754428567.jpeg','2024-05-12 19:00:00','2024-05-12 23:00:00',100.00,0,1,'past',NULL,NULL,NULL,NULL,0,0,NULL,'2025-08-05 16:16:07','2025-08-05 16:16:07');
/*!40000 ALTER TABLE `shows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_holds`
--

DROP TABLE IF EXISTS `ticket_holds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_holds` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `show_id` bigint unsigned NOT NULL,
  `ticket_type_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seat_id` bigint unsigned DEFAULT NULL,
  `general_admission_area_id` bigint unsigned DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `hold_type` enum('seat_selection','quantity_hold') COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `hold_data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_holds_ticket_type_id_foreign` (`ticket_type_id`),
  KEY `ticket_holds_customer_id_foreign` (`customer_id`),
  KEY `ticket_holds_seat_id_foreign` (`seat_id`),
  KEY `ticket_holds_general_admission_area_id_foreign` (`general_admission_area_id`),
  KEY `ticket_holds_expires_at_index` (`expires_at`),
  KEY `ticket_holds_show_id_expires_at_index` (`show_id`,`expires_at`),
  CONSTRAINT `ticket_holds_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_holds_general_admission_area_id_foreign` FOREIGN KEY (`general_admission_area_id`) REFERENCES `general_admission_areas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_holds_seat_id_foreign` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_holds_show_id_foreign` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_holds_ticket_type_id_foreign` FOREIGN KEY (`ticket_type_id`) REFERENCES `ticket_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_holds`
--

LOCK TABLES `ticket_holds` WRITE;
/*!40000 ALTER TABLE `ticket_holds` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_holds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_types`
--

DROP TABLE IF EXISTS `ticket_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `show_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` enum('assigned_seat','general_admission','standing') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general_admission',
  `seat_category_ids` json DEFAULT NULL,
  `allows_seat_selection` tinyint(1) NOT NULL DEFAULT '0',
  `price` decimal(10,2) NOT NULL,
  `capacity` int DEFAULT NULL,
  `available_quantity` int DEFAULT NULL,
  `sold_quantity` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_types_show_id_is_active_index` (`show_id`,`is_active`),
  CONSTRAINT `ticket_types_show_id_foreign` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_types`
--

LOCK TABLES `ticket_types` WRITE;
/*!40000 ALTER TABLE `ticket_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `show_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `booking_id` bigint unsigned NOT NULL,
  `ticket_type_id` bigint unsigned NOT NULL,
  `seat_id` bigint unsigned DEFAULT NULL,
  `ticket_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `seat_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seat_identifier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ticket_mode` enum('assigned_seat','general_admission') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general_admission',
  `ticket_metadata` json DEFAULT NULL,
  `purchased_date` datetime NOT NULL,
  `qr_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tickets_ticket_number_unique` (`ticket_number`),
  KEY `tickets_seat_id_foreign` (`seat_id`),
  KEY `tickets_show_id_ticket_mode_status_index` (`show_id`,`ticket_mode`,`status`),
  KEY `tickets_booking_id_status_index` (`booking_id`,`status`),
  KEY `tickets_show_id_status_index` (`show_id`,`status`),
  KEY `tickets_customer_id_status_index` (`customer_id`,`status`),
  KEY `tickets_ticket_type_id_status_index` (`ticket_type_id`,`status`),
  CONSTRAINT `tickets_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tickets_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tickets_seat_id_foreign` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tickets_show_id_foreign` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tickets_ticket_type_id_foreign` FOREIGN KEY (`ticket_type_id`) REFERENCES `ticket_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
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
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Muhammad Rashid','mrashid@gmail.com',NULL,'$2y$10$klsxHllprx65DjE.9higkeObKAPaNBFf/sTAPDwUwJciDWjyOVLoK',NULL,'2025-04-16 13:17:02','2025-04-16 13:17:02','admin'),(2,'Muhammad Amad Khan','amad@gmail.com',NULL,'$2y$10$IkGxn9z8LmSykfjTOS1DAeA4Xi.EEHqoJvCVHOVpqpuUdoau5nGaG',NULL,'2025-07-21 02:01:57','2025-07-21 02:01:57','user');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `venues`
--

DROP TABLE IF EXISTS `venues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `venues` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `contact_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacity` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `venues_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `venues`
--

LOCK TABLES `venues` WRITE;
/*!40000 ALTER TABLE `venues` DISABLE KEYS */;
INSERT INTO `venues` VALUES (1,'Ali Baba Hookah Lounge','ali-baba-hookah-lounge','Ali Baba Hookah Lounge','2238 W Walnut Hill Ln','Irving','Texas','USA','75038',NULL,NULL,'Info@3sixtyshows.com','(469) 586-5437','https://3sixtyshows.com',1000,'2025-04-18 08:50:46','2025-04-18 08:50:46'),(2,'BH Ranch','bh-ranch','BH Ranch','14149 Player St','Houston','Texas','USA','77045',NULL,NULL,'bhranchent@gmail.com','832-878-8535','https://mybhranch.com/',500,'2025-04-18 08:53:41','2025-04-18 08:53:41'),(3,'Credit Union of Texas Event Center','credit-union-of-texas-event-center','200 E Stacy Rd #1350','200 E Stacy Rd #1350','Allen','Texas','USA','75002',NULL,NULL,'info@3sixtyshows.com','1 972-678-4646','https://3sixtyshows.com',2000,'2025-04-18 08:56:56','2025-04-18 08:56:56'),(4,'Dew Events Center','dew-events-center','Dew Events Center','2401 S Stemmons Fwy','Lewisville','Texas','USA','75067',NULL,NULL,'info@thevistacenter.com','+19723150015','https://thevistacenter.com/',3000,'2025-04-18 08:59:31','2025-04-18 08:59:31'),(5,'Global Mall','global-mall',NULL,'5675 Jimmy Carter Blvd','Norcross','Georgia','USA','30071',NULL,NULL,'info@amsglobalmall.com','7704161111','https://amsglobalmall.com/',3000,'2025-04-18 09:42:49','2025-04-18 09:53:50'),(6,'NATIONAL INDIA HUB','national-india-hub','NATIONAL INDIA HUB','930 National Parkway','Schaumburg','Illinois','USA','60173',NULL,NULL,'info@3sixtyshows.com','1234567890','https://3sixtyshows.com',4000,'2025-04-18 09:58:46','2025-04-18 09:59:56'),(7,'NewPark Mall (Parking Lot)','newpark-mall-parking-lot','NewPark Mall (Parking Lot)','2086 Newpark Mall Roa','Newark','California','USA','94560',NULL,NULL,'info@3sixtyshows.com','1234567890','https://3sixtyshows.com',600,'2025-04-18 13:49:34','2025-04-18 13:49:34'),(8,'Renaissance Dallas Addison Hotel','renaissance-dallas-addison-hotel','Renaissance Dallas Addison Hotel','15201 Dallas Pkwy','Addison','Texas','USA','75001',NULL,NULL,'info@marriott.com','19723866000','https://www.marriott.com/en-us/hotels/dalic-renaissance-dallas-addison-hotel/overview/?scid=f2ae0541-1279-4f24-b197-a979c79310b0',1000,'2025-04-18 13:52:35','2025-04-18 13:52:35'),(9,'ROYAL BALL ROOM','royal-ball-room','ROYAL BALL ROOM','1050 KING GEORGES POST RD','Fords','New Jersry','USA','08863',NULL,NULL,'info@royalalbertpalace.com','7326611070','http://www.royalalbertspalace.com/',500,'2025-04-18 13:55:59','2025-04-18 13:55:59'),(10,'Sambuca 360','sambuca-360','Sambuca 360','Suite # 270 7200 Bishop Rd','Plano','Texas','USA','755024',NULL,NULL,'info@sambuca360.com','4694673393','http://www.sambuca360.com',500,'2025-04-18 13:58:48','2025-04-18 13:58:48'),(11,'Rayleigh Underground','rayleigh-underground','Rayleigh Underground','Suite 100 316 W Las Colinas Blvd.','Irving','Texas','USA','75039',NULL,NULL,'Info@rayleighunderground.com','(469) 960-6878','http://rayleighunderground.com',500,'2025-04-18 14:13:31','2025-04-18 14:13:31'),(12,'Curtis Culwell Center','curtis-culwell-center','Curtis Culwell Center','4999 Naaman Forest Boulevard','Garland','Texas','USA','75040',32.9593124,-96.6418400,'tickets@3sixtyshows.com','9999999999','http://www.curtisculwellcenter.com/',1000,'2025-04-20 14:22:26','2025-04-20 14:22:26'),(13,'Grand Center','grand-center','Grand Center','300 Chisholm Pl','Plano','Texas','USA','75075',NULL,NULL,'gagan@teletechteam.com','1 469-878-1112','https://www.facebook.com/thegrandcenter/',1000,'2025-04-20 15:29:50','2025-04-20 15:29:50'),(14,'To be Announced','to-be-announced','To be Announced','To be Announced','To be Announced',NULL,'To be Announced',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-07-29 16:54:12','2025-07-29 16:54:12'),(15,'Doubletree Dallas/Richardson','Doubletree Dallas','Doubletree Dallas/Richardson','Doubletree Dallas/Richardson','Dallas','Texas','USA',NULL,NULL,NULL,NULL,NULL,NULL,1000,'2025-07-30 13:42:40','2025-07-30 13:42:40'),(16,'300 Chisholm Pl, Plano','300 Chisholm Pl, Plano','300 Chisholm Pl, Plano','300 Chisholm Pl, Plano, TX 75075-6929, United States  · Plano','Plano','Texas','USA',NULL,NULL,NULL,NULL,NULL,NULL,1000,'2025-07-30 14:54:51','2025-07-30 14:54:51'),(17,'3Sixty Shows  · Dallas','3Sixty Shows  · Dallas','3Sixty Shows  · Dallas','3Sixty Shows  · Dallas','Dallas','Texas','USA',NULL,NULL,NULL,NULL,NULL,NULL,1000,'2025-07-30 15:01:52','2025-07-30 15:01:52'),(18,'Bashundhara Convention Hall','Bashundhara Convention Hall','Bashundhara Convention Hall','Bashundhara Convention Hall','Dhaka',NULL,'Bangladesh',NULL,NULL,NULL,NULL,NULL,NULL,1000,'2025-07-30 15:12:04','2025-07-30 15:12:04'),(19,'Vista Mall (Parking Lot)','Vista Mall (Parking Lot)','Vista Mall (Parking Lot) Dallas','Vista Mall (Parking Lot) Dallas','Dallas','Texas','USA',NULL,NULL,NULL,NULL,NULL,NULL,1000,'2025-07-31 14:25:02','2025-07-31 14:25:02'),(20,'Royal Ball Room','Royal Ball Room','Royal Ball Room 1050 KING GEORGES POST RD, FORDS, NJ 08863','Royal Ball Room 1050 KING GEORGES POST RD, FORDS, NJ 08863','New Jersey','New Jersey','USA','08863',NULL,NULL,NULL,NULL,NULL,1000,'2025-07-31 14:32:26','2025-07-31 14:32:26'),(21,'South Stemmons Freeway','South Stemmons Freeway','2401 South Stemmons Freeway Suite # 2414','2401 South Stemmons Freeway Suite # 2414','Dallas','Texas','USA','75067',NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-01 03:17:31','2025-08-01 03:17:31'),(22,'Plano Event Center Plano','Plano Event Center  · Plano','Plano Event Center  · Plano','Plano Event Center  · Plano','Plano','Texas','USA',NULL,NULL,NULL,NULL,NULL,NULL,1999,'2025-08-01 09:53:53','2025-08-01 09:53:53'),(23,'Mar Thoma Event Center','Mar Thoma Event Center','Mar Thoma Event Center','400 East Las Colinas Boulevard, Irving, TX‎','Irving','Texas','USA','75234',NULL,NULL,'secretary@mtcfb.org','0014699729436','https://www.mtcfb.org/',1000,'2025-08-01 15:12:48','2025-08-01 15:12:48'),(24,'Charles W. Eisemann Center','Charles W. Eisemann Center','Charles W. Eisemann Center','Charles W. Eisemann Center','Richardson','Texas','USA',NULL,NULL,NULL,NULL,NULL,NULL,1000,'2025-08-01 15:38:58','2025-08-01 15:38:58'),(25,'Dallas Palms','Dallas Palms',NULL,'2424 Marsh Ln','Carrollton',NULL,'USA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-01 16:37:12','2025-08-01 16:37:12'),(26,'Hilton Dallas','Hilton Dallas','Hilton Dallas','Hilton Dallas','Dallas','Texas','USA',NULL,NULL,NULL,NULL,NULL,NULL,1000,'2025-08-01 16:48:53','2025-08-01 16:48:53'),(27,'Plano Granite Park','Plano Granite Park','Plano Granite Park','Plano Granite Park','Plano',NULL,'USA',NULL,NULL,NULL,NULL,NULL,NULL,1000,'2025-08-01 16:49:47','2025-08-01 16:49:47'),(28,'Palazzo Versace Dubai','Palazzo Versace Dubai','Palazzo Versace Dubai','Palazzo Versace Dubai','Dubai',NULL,'UAE',NULL,NULL,NULL,NULL,NULL,NULL,1000,'2025-08-01 16:54:42','2025-08-01 16:54:42'),(29,'GAS MONKEY LIVE DALLAS','GAS MONKEY LIVE DALLAS','GAS MONKEY LIVE DALLAS','GAS MONKEY LIVE DALLAS','DALLAS',NULL,'USA',NULL,NULL,NULL,NULL,NULL,NULL,1000,'2025-08-01 17:03:01','2025-08-01 17:03:01'),(30,'Dubai Festival City Mall','Dubai Festival City Mall','Dubai Festival City Mall','Dubai Festival City Mall','DUBAI',NULL,'UAE',NULL,NULL,NULL,NULL,NULL,NULL,1000,'2025-08-01 17:08:41','2025-08-01 17:08:41'),(31,'MUSIC HALL AT FAIR PARK','MUSIC HALL AT FAIR PARK','MUSIC HALL AT FAIR PARK','MUSIC HALL AT FAIR PARK DALLAS TEXAS','DALLAS','TEXAS','USA',NULL,NULL,NULL,NULL,NULL,NULL,1000,'2025-08-02 10:43:17','2025-08-02 10:43:17'),(32,'MAGESTIC THEATER','MAGESTIC THEATER','MAGESTIC THEATER','MAGESTIC THEATER','DALLAS',NULL,'USA',NULL,NULL,NULL,NULL,NULL,NULL,1000,'2025-08-02 10:59:40','2025-08-02 10:59:40'),(33,'Manchester Evening News Arena','Manchester Evening News Arena','Manchester Evening News Arena','Manchester Evening News Arena','London',NULL,'UK',NULL,NULL,NULL,NULL,NULL,NULL,1000,'2025-08-02 12:40:44','2025-08-02 12:40:44'),(34,'IRVING CONVENTION CENTER','IRVING CONVENTION CENTER','IRVING CONVENTION CENTER','IRVING CONVENTION CENTER','IRVING','TEXAS','USA',NULL,NULL,NULL,NULL,NULL,NULL,1000,'2025-08-02 13:43:31','2025-08-02 13:43:31'),(36,'ROMA PALACE GARLAND','ROMA PALACE GARLAND','ROMA PALACE','ROMA PALACE 4550 W. Buckingham Rd. Garland TX, 75042','Garland','TEXAS','USA','75042',NULL,NULL,NULL,NULL,NULL,1000,'2025-08-02 16:07:00','2025-08-02 16:07:00'),(37,'American Airlines Center Dallas','American Airlines Center Dallas','American Airlines Center Dallas','American Airlines Center Dallas','DALLAS','TEXAS','USA',NULL,NULL,NULL,NULL,NULL,NULL,1000,'2025-08-03 07:01:58','2025-08-03 07:01:58'),(38,'HYATT REGENCY NORTH DALLAS RICHARDSON','HYATT REGENCY NORTH DALLAS RICHARDSON','HYATT REGENCY NORTH DALLAS RICHARDSON','HYATT REGENCY NORTH DALLAS RICHARDSON','DALLAS','TEXAS','USA',NULL,NULL,NULL,NULL,NULL,NULL,1000,'2025-08-03 07:19:03','2025-08-03 07:19:03'),(39,'The Black Academy of Arts and Letters','The Black Academy of Arts and Letters','The Black Academy of Arts and Letters','The Black Academy of Arts and Letters','DALLAS','TEXAS','USA',NULL,NULL,NULL,NULL,NULL,NULL,1000,'2025-08-03 08:20:12','2025-08-03 08:20:12'),(40,'Malachite Room at the Renaissance in Addison','Malachite Room at the Renaissance in Addison','Malachite Room at the Renaissance in Addison','Malachite Room at the Renaissance in Addison','Addison',NULL,'USA',NULL,NULL,NULL,NULL,NULL,NULL,100,'2025-08-05 15:55:52','2025-08-05 15:55:52'),(41,'Mumbai Arena','mumbai-arena',NULL,'123 Entertainment District','Mumbai','Maharashtra','India',NULL,NULL,NULL,NULL,NULL,NULL,500,'2025-08-06 16:06:45','2025-08-06 16:06:45');
/*!40000 ALTER TABLE `venues` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `video_galleries`
--

DROP TABLE IF EXISTS `video_galleries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `video_galleries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `show_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `video_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `video_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'youtube',
  `display_order` int NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `video_galleries_show_id_foreign` (`show_id`),
  CONSTRAINT `video_galleries_show_id_foreign` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `video_galleries`
--

LOCK TABLES `video_galleries` WRITE;
/*!40000 ALTER TABLE `video_galleries` DISABLE KEYS */;
INSERT INTO `video_galleries` VALUES (3,1,'Hrithik Roshan Chicago','https://youtu.be/6HBWly-NKwY','photos/zgjPVz6Gv3dnxpRQdVrVh94swljqzburjI2Q6s8g.jpg','Hrithik Roshan Chicago','youtube',0,1,1,'2025-05-02 15:23:24','2025-05-02 15:55:37');
/*!40000 ALTER TABLE `video_galleries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `videosin_galleries`
--

DROP TABLE IF EXISTS `videosin_galleries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `videosin_galleries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `video_gallery_id` bigint unsigned NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `youtubelink` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `videosin_galleries_video_gallery_id_display_order_index` (`video_gallery_id`,`display_order`),
  KEY `videosin_galleries_is_active_index` (`is_active`),
  CONSTRAINT `videosin_galleries_video_gallery_id_foreign` FOREIGN KEY (`video_gallery_id`) REFERENCES `video_galleries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `videosin_galleries`
--

LOCK TABLES `videosin_galleries` WRITE;
/*!40000 ALTER TABLE `videosin_galleries` DISABLE KEYS */;
INSERT INTO `videosin_galleries` VALUES (1,3,'/storage/uploads/videos_in_galleries/1752772267_Eventbrite Ticket Chicago.webp','Hrithik Video Chicago 1','https://www.youtube.com/shorts/Fo_3jiXg53I',0,1,'2025-07-16 17:45:43','2025-07-17 12:23:31');
/*!40000 ALTER TABLE `videosin_galleries` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-07  2:18:10

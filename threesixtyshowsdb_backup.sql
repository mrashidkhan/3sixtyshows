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
  KEY `booking_items_booking_id_foreign` (`booking_id`),
  KEY `booking_items_ticket_type_id_foreign` (`ticket_type_id`),
  KEY `booking_items_seat_id_foreign` (`seat_id`),
  KEY `booking_items_general_admission_area_id_foreign` (`general_admission_area_id`),
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
  KEY `bookings_customer_id_foreign` (`customer_id`),
  KEY `bookings_show_id_foreign` (`show_id`),
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
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',2),(3,'0001_01_01_000002_create_jobs_table',3),(4,'2025_04_05_153837_add_role_to_users_table',4),(5,'2025_04_03_123334_create_show_categories_table',5),(6,'2025_04_03_123335_create_venues_table',6),(7,'2025_04_10_144433_create_customers_table',7),(8,'2025_04_03_123333_create_shows_table',8),(9,'2025_04_10_145012_create_posters_table',9),(10,'2025_04_10_145024_create_photo_galleries_table',10),(11,'2025_04_10_145038_create_video_galleries_table',11),(12,'2025_04_03_123339_create_galleries_table',12),(13,'2025_04_10_192044_create_ticket_types_table',13),(14,'2025_04_10_200226_create_seats_table',14),(15,'2025_04_10_200402_create_seat_maps_table',15),(16,'2025_04_03_123335_create_bookings_table',16),(17,'2025_04_10_144801_create_tickets_table',17),(18,'2025_04_16_140512_create_seat_categories_and_modify_tables',18),(19,'2025_04_18_202713_add_redirect_fields_to_shows_table',19),(20,'2025_04_19_081350_change_performers_column_type_in_shows_table',20),(21,'2025_04_20_215511_add_user_id_to_customers_table',21),(22,'2025_05_03_082034_create_photosin_galleries_table',22),(23,'2019_12_14_000001_create_personal_access_tokens_table',23),(24,'2025_06_16_092019_create_videosin_galleries_table',23),(25,'2025_07_16_220013_add_youtubelink_to_videosin_galaries_table',24),(26,'2025_07_20_075614_update_shows_table_add_seating_type',25),(27,'2025_07_20_093314_update_ticket_types_table',26),(28,'2025_07_20_093937_update_tickets_table',27),(29,'2025_07_20_094229_update_seat_reservations_table',28),(30,'2025_07_20_094424_create_general_admission_areas_table',29),(31,'2025_07_20_094620_create_show_ticket_quotas_table',29),(32,'2025_07_20_094714_create_ticket_holds_table',29),(33,'2025_07_20_094838_update_bookings_table',29),(34,'2025_07_20_095026_create_booking_items_table',30),(35,'2025_07_20_095155_add_performance_indexes',31);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `photo_galleries`
--

LOCK TABLES `photo_galleries` WRITE;
/*!40000 ALTER TABLE `photo_galleries` DISABLE KEYS */;
INSERT INTO `photo_galleries` VALUES (1,1,'Chicago Photo Gallery 1','photos/KB9gwcIqk9Mj7733QTnl4o8vvMqBToAhfkrWn8Iv.jpg','Chicago Photo Gallery 1',0,1,1,'2025-04-24 14:53:56','2025-04-24 14:53:56');
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `photosin_galleries`
--

LOCK TABLES `photosin_galleries` WRITE;
/*!40000 ALTER TABLE `photosin_galleries` DISABLE KEYS */;
INSERT INTO `photosin_galleries` VALUES (1,1,'/storage/uploads/photos_in_galleries/1750008266_aajkiraat.webp','NYE 2025',0,0,'2025-06-12 18:13:49','2025-06-15 12:24:26'),(2,1,'/storage/uploads/photos_in_galleries/1749770029_badshah-updated-dallas.jpeg',NULL,0,1,'2025-06-12 18:13:49','2025-06-12 18:13:49'),(3,1,'/storage/uploads/photos_in_galleries/1749770448_nehakakar.webp','three shows images',0,1,'2025-06-12 18:20:48','2025-06-12 18:20:48'),(4,1,'/storage/uploads/photos_in_galleries/1749770448_nora-dallas.jpeg','three shows images',0,1,'2025-06-12 18:20:48','2025-06-12 18:20:48'),(5,1,'/storage/uploads/photos_in_galleries/1749770448_saraalikhan.webp','three shows images',0,1,'2025-06-12 18:20:48','2025-06-12 18:20:48'),(6,1,'/storage/uploads/photos_in_galleries/1750006994_EidulAdha2025.jpeg','abc',0,1,'2025-06-15 12:03:16','2025-06-15 12:03:16'),(7,1,'/storage/uploads/photos_in_galleries/1750006996_EidulAzharashid2025.jpg','abc',0,1,'2025-06-15 12:03:16','2025-06-15 12:03:16');
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
  KEY `seat_reservations_seat_id_foreign` (`seat_id`),
  KEY `seat_reservations_ticket_type_id_foreign` (`ticket_type_id`),
  KEY `seat_reservations_show_id_status_reservation_type_index` (`show_id`,`status`,`reservation_type`),
  KEY `seat_reservations_expires_at_index` (`expires_at`),
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
INSERT INTO `show_categories` VALUES (1,'Events','events','Event Category','show-categories/2025-02-14-12-50-40-436_92_1744983241.webp',1,'2025-04-18 08:34:02','2025-04-18 08:34:02'),(2,'Concerts','concerts','Concerts','show-categories/Gamechangerprerelease_1744983327.jpg',1,'2025-04-18 08:35:27','2025-04-18 08:35:27'),(3,'Movie','movie','Movie','show-categories/AN9I9370 ff_1744983997.jpg',1,'2025-04-18 08:46:37','2025-04-18 08:46:37'),(4,'Sports','sports','sports','show-categories/AN9I9433 ff_1744984044.jpg',1,'2025-04-18 08:47:24','2025-04-18 08:47:24');
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
  KEY `shows_category_id_foreign` (`category_id`),
  KEY `shows_venue_id_foreign` (`venue_id`),
  CONSTRAINT `shows_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `show_categories` (`id`),
  CONSTRAINT `shows_venue_id_foreign` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shows`
--

LOCK TABLES `shows` WRITE;
/*!40000 ALTER TABLE `shows` DISABLE KEYS */;
INSERT INTO `shows` VALUES (1,'Rangotsav Holi Festival  - Chicago','rangotsav-holi-festival-chicago',1,6,'general_admission',0,'Put Your Hands Up, New Jersey! Join the Hrithik Roshan Tour Kickoff\r\n\r\nPrepare yourself for a unique Holi festival experience with a special meet and greet with the incredibly charismatic Hrithik Roshan. Hrithik Roshan, renowned for his exceptional dancing skills, has starred in numerous Bollywood hits like \"Kaho Naa... Pyaar Hai,\" \"Dhoom 2,\" and \"War.\" He is not only an acclaimed actor but also a celebrated style icon and philanthropist. This festival promises an unforgettable experience, allowing fans to interact with Hrithik and enjoy a vibrant display of colors, music, and dance.\r\n\r\nA huge thank you to Atique B Sheikh of Worldstar for adding extra magic to this grand celebration.\r\n\r\nEvent Details:\r\n\r\nVenue: TBD\r\nMore Info: Visit 3sixtyshows.com\r\nTickets: Available\r\n\r\n\r\nPrepare yourself for a unique Holi festival experience with a special meet and greet with the incredibly charismatic Hrithik Roshan. Don’t miss out on this unforgettable celebration starting with a bang!','Rangotsav Holi Festival  - Chicago','shows/1749824264.jpg','2025-04-22 19:00:00','2025-04-22 22:00:00',1000.00,1000,1,'past','Hrithik Roshan','[]','180',NULL,1,1,'https://3sixtyshows.com/us/schaumburg/events/rangotsav-holi-festival---chicago-at-to-be-announced?frm=pe','2025-04-19 04:57:10','2025-06-13 09:17:44'),(2,'Badshah Un-Finished Tour Dallas','badshah-un-finished-tour-dallas',1,2,'general_admission',0,'Introduction\r\n\r\nBadshah, the renowned Indian rapper and music producer, is all set to take the nation by storm with his Un-Finished Tour. This multi-city musical journey promises to be an unforgettable experience for fans across the country. In this article, we explore the highlights of Badshah’s tour, the electrifying performances, and the venues that will witness the magic.\r\n\r\nWho Is Badshah?\r\n\r\nBorn as Aditya Prateek Singh Sisodia, Badshah has carved a niche for himself in the Indian music industry. His unique blend of rap, hip-hop, and Punjabi beats has made him a household name. From chart-topping singles to blockbuster Bollywood tracks, Badshah’s musical prowess knows no bounds.\r\n\r\nThe Concert Experience\r\n\r\nBadshah’s concerts are more than just music; they are a celebration of life, rhythm, and unbridled passion. From his iconic hits like “DJ Waley Babu” to soul-stirring melodies, every track resonates with fans. The stage lights up, the beats drop, and Badshah’s magnetic presence takes center stage.\r\n\r\nThe Paagal Anthem: Badshah’s hit single “Paagal” has become an anthem for partygoers. Its catchy beats and infectious lyrics have taken the world by storm.\r\nSocial Media Buzz: Fans can’t stop talking about the tour on social media. From Instagram stories to Twitter trends, the excitement is palpable.\r\nTicket Information:www.3sixtyshows.com\r\nDon’t miss out on this sensational event—it’s going to be paagal (crazy) in the best way possible! \r\nConclusion\r\n\r\nAs Badshah’s beats reverberate through concert halls, fans will dance, sing, and create memories that last a lifetime. The Un-Finished Tour is not just a concert; it’s an experience that transcends boundaries and unites music lovers across the nation.\r\n\r\nDon’t miss the chance to be part of this musical extravaganza! 🎶🔥','Badshah Un-Finished Tour Dallas','shows/1745177061.jpeg','2025-09-19 20:00:00','2025-09-19 22:00:00',500.00,1000,1,'upcoming','Badshah','[]','180',NULL,1,0,NULL,'2025-04-20 14:23:46','2025-04-20 14:24:21'),(3,'BOLLYWOOD NIGHT WITH NORA FATEHI','bollywood-night-with-nora-fatehi',1,13,'general_admission',0,'BOLLYWOOD NIGHT WITH NORA FATEHI','BOLLYWOOD NIGHT WITH NORA FATEHI','shows/1745183193.jpeg','2025-04-26 22:00:00','2025-04-26 23:59:00',1000.00,1000,1,'upcoming','BOLLYWOOD NIGHT WITH NORA FATEHI',NULL,'180',NULL,1,0,NULL,'2025-04-20 16:06:33','2025-04-20 16:06:33'),(4,'Ghulam Ali - Farewell Tour Dallas','ghulam-ali-farewell-tour-dallas',1,11,'general_admission',0,'Ghulam Ali - Farewell Tour Dallas','Ghulam Ali - Farewell Tour Dallas','shows/1745184221.jpg','0025-04-21 19:00:00','2025-04-21 22:00:00',1000.00,1000,1,'ongoing','Ghulam Ali - Farewell Tour Dallas','[]','180',NULL,1,0,NULL,'2025-04-20 16:19:57','2025-04-20 16:24:15'),(5,'Shahzada Kartik Aryan 2023','shahzada-kartik-aryan-2023',1,13,'general_admission',0,'Shahzada Kartik Aryan 2023','Shahzada Kartik Aryan 2023','shows/1745906134.jpg','2023-01-29 19:00:00','2025-04-29 22:00:00',1000.00,0,1,'ongoing','Shahzada Kartik Aryan','[]','180',NULL,1,0,NULL,'2025-04-29 00:55:35','2025-04-29 00:59:15'),(6,'Hrithick Roshan Dallas','hrithick-roshan-dallas',1,12,'general_admission',0,'Hrithik Roshan Dallas','Hrithik Roshan Dallas','shows/1752775278.jpg','2025-07-20 19:00:00','2025-07-20 23:00:00',40.00,NULL,1,'upcoming',NULL,'[]',NULL,NULL,1,0,NULL,'2025-07-17 13:01:18','2025-07-17 13:03:51');
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
  KEY `ticket_types_show_id_foreign` (`show_id`),
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
  KEY `tickets_customer_id_foreign` (`customer_id`),
  KEY `tickets_ticket_type_id_foreign` (`ticket_type_id`),
  KEY `tickets_seat_id_foreign` (`seat_id`),
  KEY `tickets_show_id_ticket_mode_status_index` (`show_id`,`ticket_mode`,`status`),
  KEY `tickets_booking_id_status_index` (`booking_id`,`status`),
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `venues`
--

LOCK TABLES `venues` WRITE;
/*!40000 ALTER TABLE `venues` DISABLE KEYS */;
INSERT INTO `venues` VALUES (1,'Ali Baba Hookah Lounge','ali-baba-hookah-lounge','Ali Baba Hookah Lounge','2238 W Walnut Hill Ln','Irving','Texas','USA','75038',NULL,NULL,'Info@3sixtyshows.com','(469) 586-5437','https://3sixtyshows.com',1000,'2025-04-18 08:50:46','2025-04-18 08:50:46'),(2,'BH Ranch','bh-ranch','BH Ranch','14149 Player St','Houston','Texas','USA','77045',NULL,NULL,'bhranchent@gmail.com','832-878-8535','https://mybhranch.com/',500,'2025-04-18 08:53:41','2025-04-18 08:53:41'),(3,'Credit Union of Texas Event Center','credit-union-of-texas-event-center','200 E Stacy Rd #1350','200 E Stacy Rd #1350','Allen','Texas','USA','75002',NULL,NULL,'info@3sixtyshows.com','1 972-678-4646','https://3sixtyshows.com',2000,'2025-04-18 08:56:56','2025-04-18 08:56:56'),(4,'Dew Events Center','dew-events-center','Dew Events Center','2401 S Stemmons Fwy','Lewisville','Texas','USA','75067',NULL,NULL,'info@thevistacenter.com','+19723150015','https://thevistacenter.com/',3000,'2025-04-18 08:59:31','2025-04-18 08:59:31'),(5,'Global Mall','global-mall',NULL,'5675 Jimmy Carter Blvd','Norcross','Georgia','USA','30071',NULL,NULL,'info@amsglobalmall.com','7704161111','https://amsglobalmall.com/',3000,'2025-04-18 09:42:49','2025-04-18 09:53:50'),(6,'NATIONAL INDIA HUB','national-india-hub','NATIONAL INDIA HUB','930 National Parkway','Schaumburg','Illinois','USA','60173',NULL,NULL,'info@3sixtyshows.com','1234567890','https://3sixtyshows.com',4000,'2025-04-18 09:58:46','2025-04-18 09:59:56'),(7,'NewPark Mall (Parking Lot)','newpark-mall-parking-lot','NewPark Mall (Parking Lot)','2086 Newpark Mall Roa','Newark','California','USA','94560',NULL,NULL,'info@3sixtyshows.com','1234567890','https://3sixtyshows.com',600,'2025-04-18 13:49:34','2025-04-18 13:49:34'),(8,'Renaissance Dallas Addison Hotel','renaissance-dallas-addison-hotel','Renaissance Dallas Addison Hotel','15201 Dallas Pkwy','Addison','Texas','USA','75001',NULL,NULL,'info@marriott.com','19723866000','https://www.marriott.com/en-us/hotels/dalic-renaissance-dallas-addison-hotel/overview/?scid=f2ae0541-1279-4f24-b197-a979c79310b0',1000,'2025-04-18 13:52:35','2025-04-18 13:52:35'),(9,'ROYAL BALL ROOM','royal-ball-room','ROYAL BALL ROOM','1050 KING GEORGES POST RD','Fords','New Jersry','USA','08863',NULL,NULL,'info@royalalbertpalace.com','7326611070','http://www.royalalbertspalace.com/',500,'2025-04-18 13:55:59','2025-04-18 13:55:59'),(10,'Sambuca 360','sambuca-360','Sambuca 360','Suite # 270 7200 Bishop Rd','Plano','Texas','USA','755024',NULL,NULL,'info@sambuca360.com','4694673393','http://www.sambuca360.com',500,'2025-04-18 13:58:48','2025-04-18 13:58:48'),(11,'Rayleigh Underground','rayleigh-underground','Rayleigh Underground','Suite 100 316 W Las Colinas Blvd.','Irving','Texas','USA','75039',NULL,NULL,'Info@rayleighunderground.com','(469) 960-6878','http://rayleighunderground.com',500,'2025-04-18 14:13:31','2025-04-18 14:13:31'),(12,'Curtis Culwell Center','curtis-culwell-center','Curtis Culwell Center','4999 Naaman Forest Boulevard','Garland','Texas','USA','75040',32.9593124,-96.6418400,'tickets@3sixtyshows.com','9999999999','http://www.curtisculwellcenter.com/',1000,'2025-04-20 14:22:26','2025-04-20 14:22:26'),(13,'Grand Center','grand-center','Grand Center','300 Chisholm Pl','Plano','Texas','USA','75075',NULL,NULL,'gagan@teletechteam.com','1 469-878-1112','https://www.facebook.com/thegrandcenter/',1000,'2025-04-20 15:29:50','2025-04-20 15:29:50');
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
INSERT INTO `video_galleries` VALUES (3,1,'Hrithik Roshan Chicago','https://youtu.be/6HBWly-NKwY','photos/zgjPVz6Gv3dnxpRQdVrVh94swljqzburjI2Q6s8g.jpg','Hrithik Roshan Chicago','youtube',0,1,1,'2025-05-02 15:23:24','2025-05-02 15:55:37'),(4,6,'Hrithik Roshan Dallas Airport','https://youtu.be/6HBWly-NKwY','photos/YpiHRgpQg0damkAzI9dR0NAhNbnPQa3JxNTGm6rx.jpg','Hrithik Roshan Dallas Airport','youtube',0,0,1,'2025-07-17 13:05:47','2025-07-17 13:05:47');
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
INSERT INTO `videosin_galleries` VALUES (1,3,'/storage/uploads/videos_in_galleries/1752772267_Eventbrite Ticket Chicago.webp','Hrithik Video Chicago 1','https://www.youtube.com/shorts/Fo_3jiXg53I',0,1,'2025-07-16 17:45:43','2025-07-17 12:23:31'),(2,4,'/storage/uploads/videos_in_galleries/1752777631_hrithik flyer 3 dallas.jpg','Hrithik Roshan','https://www.youtube.com/watch?v=zR01r3Kq1Ws',0,1,'2025-07-17 13:40:31','2025-07-17 13:40:31');
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

-- Dump completed on 2025-07-24  1:53:05

-- MySQL dump 10.13  Distrib 8.0.43, for Linux (x86_64)
--
-- Host: localhost    Database: laravel
-- ------------------------------------------------------
-- Server version	8.0.43-0ubuntu0.24.04.1

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
-- Table structure for table `bombas`
--

DROP TABLE IF EXISTS `bombas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bombas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gasolinera_id` bigint unsigned NOT NULL,
  `galonaje_super` decimal(10,2) NOT NULL DEFAULT '0.00',
  `galonaje_regular` decimal(10,2) NOT NULL DEFAULT '0.00',
  `galonaje_diesel` decimal(10,2) NOT NULL DEFAULT '0.00',
  `galonaje_cc` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado` enum('activa','inactiva','mantenimiento') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activa',
  `fotografia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bombas`
--

LOCK TABLES `bombas` WRITE;
/*!40000 ALTER TABLE `bombas` DISABLE KEYS */;
INSERT INTO `bombas` VALUES (13,'Bomba 1',4,2829.22,1684.09,2301.33,214433.20,'activa',NULL,'2025-09-13 20:01:24','2025-09-13 20:31:01'),(14,'Bomba 2',4,3139.11,1950.38,2577.45,241198.50,'activa',NULL,'2025-09-13 20:01:24','2025-09-13 20:32:18'),(15,'Bomba 3',4,36228.47,29100.58,43018.54,3598101.40,'activa',NULL,'2025-09-13 20:01:24','2025-09-13 20:33:36'),(16,'Bomba 4',4,35870.02,31389.37,49242.79,49267.08,'activa',NULL,'2025-09-13 20:01:24','2025-09-13 20:36:21'),(17,'Bomba 1',5,2769.73,1650.39,2288.66,0.00,'activa',NULL,'2025-09-13 20:24:14','2025-09-13 20:24:36'),(18,'Bomba 2',5,378.94,1912.82,2558.20,0.00,'activa',NULL,'2025-09-13 20:24:14','2025-09-13 20:24:58'),(19,'Bomba 3',5,36176.77,25994.45,42991.51,0.00,'activa',NULL,'2025-09-13 20:24:14','2025-09-13 20:25:18'),(20,'Bomba 4',5,35870.02,31389.37,49242.79,0.00,'activa',NULL,'2025-09-13 20:24:14','2025-09-13 20:25:42');
/*!40000 ALTER TABLE `bombas` ENABLE KEYS */;
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
INSERT INTO `cache` VALUES ('sistema-gasolineras-cache-livewire-rate-limiter:12098751669f44d3789d4ba6a623b0f92bf35e16','i:1;',1757795637),('sistema-gasolineras-cache-livewire-rate-limiter:12098751669f44d3789d4ba6a623b0f92bf35e16:timer','i:1757795637;',1757795637),('sistema-gasolineras-cache-livewire-rate-limiter:d224e8a7ea96eeb4988cb9ac5645a9f47aabd0c3','i:1;',1757814381),('sistema-gasolineras-cache-livewire-rate-limiter:d224e8a7ea96eeb4988cb9ac5645a9f47aabd0c3:timer','i:1757814381;',1757814381),('sistema-gasolineras-cache-spatie.permission.cache','a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:6:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:18:\"gestionar usuarios\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:21:\"gestionar gasolineras\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:16:\"gestionar bombas\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:16:\"ver panel turnos\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:19:\"abrir cerrar turnos\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:19:\"actualizar lecturas\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}}s:5:\"roles\";a:2:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:5:\"admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:8:\"operador\";s:1:\"c\";s:3:\"web\";}}}',1757881978);
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
-- Table structure for table `gasolineras`
--

DROP TABLE IF EXISTS `gasolineras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gasolineras` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ubicacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precio_super` decimal(8,2) NOT NULL DEFAULT '0.00',
  `precio_regular` decimal(8,2) NOT NULL DEFAULT '0.00',
  `precio_diesel` decimal(8,2) NOT NULL DEFAULT '0.00',
  `fecha_actualizacion_precios` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gasolineras`
--

LOCK TABLES `gasolineras` WRITE;
/*!40000 ALTER TABLE `gasolineras` DISABLE KEYS */;
INSERT INTO `gasolineras` VALUES (4,'MONTEKARLO I','MK 1',32.00,29.00,27.00,NULL,'2025-09-13 19:59:37','2025-09-13 19:59:37'),(5,'MONTEKARLO II','MK 2',32.00,29.00,27.00,NULL,'2025-09-13 19:59:49','2025-09-13 20:00:11'),(6,'NUEVO AMANECER I','MK 3',30.00,28.00,26.00,NULL,'2025-09-13 20:00:58','2025-09-13 20:00:58'),(7,'NUEVO AMANECER II','MK 4',30.00,28.00,26.00,NULL,'2025-09-13 20:01:21','2025-09-13 20:01:21');
/*!40000 ALTER TABLE `gasolineras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gastos`
--

DROP TABLE IF EXISTS `gastos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gastos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `categoria` enum('operativo','mantenimiento','administrativo','inventario') COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `proveedor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gasolinera_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gastos_gasolinera_id_foreign` (`gasolinera_id`),
  CONSTRAINT `gastos_gasolinera_id_foreign` FOREIGN KEY (`gasolinera_id`) REFERENCES `gasolineras` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=181 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gastos`
--

LOCK TABLES `gastos` WRITE;
/*!40000 ALTER TABLE `gastos` DISABLE KEYS */;
/*!40000 ALTER TABLE `gastos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gastos_mensuales`
--

DROP TABLE IF EXISTS `gastos_mensuales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gastos_mensuales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `anio` int NOT NULL,
  `mes` int NOT NULL,
  `impuestos` decimal(10,2) NOT NULL DEFAULT '0.00',
  `servicios` decimal(10,2) NOT NULL DEFAULT '0.00',
  `planilla` decimal(10,2) NOT NULL DEFAULT '0.00',
  `renta` decimal(10,2) NOT NULL DEFAULT '0.00',
  `gastos_adicionales` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gastos_mensuales_anio_mes_unique` (`anio`,`mes`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gastos_mensuales`
--

LOCK TABLES `gastos_mensuales` WRITE;
/*!40000 ALTER TABLE `gastos_mensuales` DISABLE KEYS */;
INSERT INTO `gastos_mensuales` VALUES (3,2025,9,10000.00,10000.00,5000.00,5000.00,'[]','2025-09-13 20:07:39','2025-09-13 20:07:39');
/*!40000 ALTER TABLE `gastos_mensuales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historial_bombas`
--

DROP TABLE IF EXISTS `historial_bombas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historial_bombas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bomba_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `campo_modificado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_anterior` decimal(10,2) NOT NULL,
  `valor_nuevo` decimal(10,2) NOT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `historial_bombas_bomba_id_foreign` (`bomba_id`),
  KEY `historial_bombas_user_id_foreign` (`user_id`),
  CONSTRAINT `historial_bombas_bomba_id_foreign` FOREIGN KEY (`bomba_id`) REFERENCES `bombas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `historial_bombas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_bombas`
--

LOCK TABLES `historial_bombas` WRITE;
/*!40000 ALTER TABLE `historial_bombas` DISABLE KEYS */;
INSERT INTO `historial_bombas` VALUES (6079,16,4,'galonaje_super',0.00,35870.02,'Galonaje super actualizado','2025-09-13 20:16:56','2025-09-13 20:16:56'),(6080,15,4,'galonaje_super',0.00,36176.77,'Galonaje super actualizado','2025-09-13 20:17:23','2025-09-13 20:17:23'),(6081,14,4,'galonaje_super',0.00,378.94,'Galonaje super actualizado','2025-09-13 20:17:41','2025-09-13 20:17:41'),(6082,13,4,'galonaje_super',0.00,2769.73,'Galonaje super actualizado','2025-09-13 20:17:59','2025-09-13 20:17:59'),(6083,13,4,'galonaje_regular',0.00,1650.39,'Galonaje regular actualizado','2025-09-13 20:18:41','2025-09-13 20:18:41'),(6084,14,4,'galonaje_regular',0.00,1912.82,'Galonaje regular actualizado','2025-09-13 20:21:03','2025-09-13 20:21:03'),(6085,15,4,'galonaje_regular',0.00,25994.45,'Galonaje regular actualizado','2025-09-13 20:21:47','2025-09-13 20:21:47'),(6086,16,4,'galonaje_regular',0.00,31389.37,'Galonaje regular actualizado','2025-09-13 20:22:12','2025-09-13 20:22:12'),(6087,13,4,'galonaje_diesel',0.00,2288.66,'Galonaje diesel actualizado','2025-09-13 20:22:38','2025-09-13 20:22:38'),(6088,14,4,'galonaje_diesel',0.00,2558.20,'Galonaje diesel actualizado','2025-09-13 20:22:51','2025-09-13 20:22:51'),(6089,15,4,'galonaje_diesel',0.00,49242.79,'Galonaje diesel actualizado','2025-09-13 20:23:21','2025-09-13 20:23:21'),(6090,16,4,'galonaje_diesel',0.00,49242.79,'Galonaje diesel actualizado','2025-09-13 20:23:31','2025-09-13 20:23:31'),(6091,15,4,'galonaje_diesel',49242.79,42991.51,'Galonaje diesel actualizado','2025-09-13 20:23:47','2025-09-13 20:23:47'),(6092,17,4,'galonaje_super',0.00,2769.73,'Galonaje super actualizado','2025-09-13 20:24:36','2025-09-13 20:24:36'),(6093,17,4,'galonaje_regular',0.00,1650.39,'Galonaje regular actualizado','2025-09-13 20:24:36','2025-09-13 20:24:36'),(6094,17,4,'galonaje_diesel',0.00,2288.66,'Galonaje diesel actualizado','2025-09-13 20:24:36','2025-09-13 20:24:36'),(6095,18,4,'galonaje_super',0.00,378.94,'Galonaje super actualizado','2025-09-13 20:24:58','2025-09-13 20:24:58'),(6096,18,4,'galonaje_regular',0.00,1912.82,'Galonaje regular actualizado','2025-09-13 20:24:58','2025-09-13 20:24:58'),(6097,18,4,'galonaje_diesel',0.00,2558.20,'Galonaje diesel actualizado','2025-09-13 20:24:58','2025-09-13 20:24:58'),(6098,19,4,'galonaje_super',0.00,36176.77,'Galonaje super actualizado','2025-09-13 20:25:18','2025-09-13 20:25:18'),(6099,19,4,'galonaje_regular',0.00,25994.45,'Galonaje regular actualizado','2025-09-13 20:25:18','2025-09-13 20:25:18'),(6100,19,4,'galonaje_diesel',0.00,42991.51,'Galonaje diesel actualizado','2025-09-13 20:25:18','2025-09-13 20:25:18'),(6101,20,4,'galonaje_super',0.00,35870.02,'Galonaje super actualizado','2025-09-13 20:25:42','2025-09-13 20:25:42'),(6102,20,4,'galonaje_regular',0.00,31389.37,'Galonaje regular actualizado','2025-09-13 20:25:42','2025-09-13 20:25:42'),(6103,20,4,'galonaje_diesel',0.00,49242.79,'Galonaje diesel actualizado','2025-09-13 20:25:42','2025-09-13 20:25:42');
/*!40000 ALTER TABLE `historial_bombas` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_08_04_000000_create_gasolineras_table',2),(5,'2025_08_04_204730_create_permission_tables',3),(6,'2025_08_04_000000_create_combustibles_table',4),(7,'2025_08_04_000000_create_bombas_table',2),(8,'2025_08_04_204740_add_gasolinera_to_users_table',5),(9,'2025_08_04_220627_create_turnos_table',5),(10,'2025_08_04_224827_modify_bombas_table_add_combustible_fields',5),(11,'2025_08_05_021655_create_historial_bombas_table',5),(12,'2025_08_06_152254_add_precios_to_gasolineras_table',5),(13,'2025_08_06_152338_restructure_bombas_and_combustibles',5),(14,'2025_08_06_153001_clean_bombas_table',5),(15,'2025_08_06_155729_add_fecha_actualizacion_precios_to_gasolineras_table',5),(16,'2025_08_06_170825_add_cc_activo_to_gasolineras_table',5),(17,'2025_08_06_194622_create_gastos_table',5),(18,'2025_08_06_211306_create_gastos_mensuales_table',5),(19,'2025_08_06_211838_remove_gasolinera_foreign_key_from_gastos_mensuales_table',5),(20,'2025_08_06_212930_add_gastos_adicionales_to_gastos_mensuales_table',5),(21,'2025_08_07_105058_create_precio_mensuals_table',5),(22,'2025_09_02_125356_remove_cc_pricing_from_gasolineras_table',6),(23,'2025_09_02_162122_add_themes_settings_to_users_table',7),(26,'2025_09_05_120537_add_fotografia_to_bombas_table',8),(27,'2025_09_05_121120_create_turno_bomba_datos_table',8),(28,'2025_09_05_124047_add_turno_id_to_turno_bomba_datos_table',9),(29,'2025_09_05_124431_make_turno_identificador_nullable_in_turno_bomba_datos_table',10),(30,'2025_09_13_204714_add_ventas_and_tanques_to_turnos_table',11);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(1,'App\\Models\\User',4);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
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
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'gestionar usuarios','web','2025-08-07 13:11:47','2025-08-07 13:11:47'),(2,'gestionar gasolineras','web','2025-08-07 13:11:47','2025-08-07 13:11:47'),(3,'gestionar bombas','web','2025-08-07 13:11:47','2025-08-07 13:11:47'),(4,'ver panel turnos','web','2025-08-07 13:11:47','2025-08-07 13:11:47'),(5,'abrir cerrar turnos','web','2025-08-07 13:11:47','2025-08-07 13:11:47'),(6,'actualizar lecturas','web','2025-08-07 13:11:47','2025-08-07 13:11:47');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `precios_mensuales`
--

DROP TABLE IF EXISTS `precios_mensuales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `precios_mensuales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `anio` int NOT NULL,
  `mes` int NOT NULL,
  `super_compra` decimal(8,2) NOT NULL DEFAULT '0.00',
  `diesel_compra` decimal(8,2) NOT NULL DEFAULT '0.00',
  `regular_compra` decimal(8,2) NOT NULL DEFAULT '0.00',
  `super_venta` decimal(8,2) NOT NULL DEFAULT '0.00',
  `diesel_venta` decimal(8,2) NOT NULL DEFAULT '0.00',
  `regular_venta` decimal(8,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `precios_mensuales_anio_mes_unique` (`anio`,`mes`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `precios_mensuales`
--

LOCK TABLES `precios_mensuales` WRITE;
/*!40000 ALTER TABLE `precios_mensuales` DISABLE KEYS */;
INSERT INTO `precios_mensuales` VALUES (2,2025,9,26.00,22.00,20.00,0.00,0.00,0.00,'2025-09-13 20:08:15','2025-09-13 20:08:15');
/*!40000 ALTER TABLE `precios_mensuales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(4,2),(5,2),(6,2);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','web','2025-08-07 13:11:47','2025-08-07 13:11:47'),(2,'operador','web','2025-08-07 13:11:47','2025-08-07 13:11:47');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
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
-- Table structure for table `turno_bomba_datos`
--

DROP TABLE IF EXISTS `turno_bomba_datos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `turno_bomba_datos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bomba_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `turno_id` bigint unsigned DEFAULT NULL,
  `turno_identificador` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `galonaje_super` decimal(10,3) NOT NULL DEFAULT '0.000',
  `galonaje_regular` decimal(10,3) NOT NULL DEFAULT '0.000',
  `galonaje_diesel` decimal(10,3) NOT NULL DEFAULT '0.000',
  `lectura_cc` decimal(10,3) NOT NULL DEFAULT '0.000',
  `fotografia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `fecha_turno` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `turno_bomba_datos_user_id_foreign` (`user_id`),
  KEY `turno_bomba_datos_bomba_id_turno_identificador_index` (`bomba_id`,`turno_identificador`),
  KEY `turno_bomba_datos_fecha_turno_index` (`fecha_turno`),
  KEY `turno_bomba_datos_turno_id_foreign` (`turno_id`),
  CONSTRAINT `turno_bomba_datos_bomba_id_foreign` FOREIGN KEY (`bomba_id`) REFERENCES `bombas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `turno_bomba_datos_turno_id_foreign` FOREIGN KEY (`turno_id`) REFERENCES `turnos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `turno_bomba_datos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `turno_bomba_datos`
--

LOCK TABLES `turno_bomba_datos` WRITE;
/*!40000 ALTER TABLE `turno_bomba_datos` DISABLE KEYS */;
INSERT INTO `turno_bomba_datos` VALUES (2,13,10,305,NULL,2829.222,1684.089,2301.334,214433.200,'turnos/bombas/turno_305_bomba_13_1757817061.jpg','Datos guardados para turno 305 - Bomba 1','2025-09-13 20:31:01','2025-09-13 20:31:01','2025-09-13 20:31:01'),(3,14,10,305,NULL,3139.106,1950.377,2577.453,241198.500,'turnos/bombas/turno_305_bomba_14_1757817138.jpg','Datos guardados para turno 305 - Bomba 2','2025-09-13 20:32:18','2025-09-13 20:32:18','2025-09-13 20:32:18'),(4,15,10,305,NULL,36228.468,29100.576,43018.536,3598101.400,'turnos/bombas/turno_305_bomba_15_1757817216.jpg','Datos guardados para turno 305 - Bomba 3','2025-09-13 20:33:36','2025-09-13 20:33:36','2025-09-13 20:33:36'),(5,16,10,305,NULL,0.000,0.000,0.000,49267.084,'turnos/bombas/turno_305_bomba_16_1757817381.jpg','Datos guardados para turno 305 - Bomba 4','2025-09-13 20:36:21','2025-09-13 20:36:21','2025-09-13 20:36:21');
/*!40000 ALTER TABLE `turno_bomba_datos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `turnos`
--

DROP TABLE IF EXISTS `turnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `turnos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `gasolinera_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `dinero_apertura` decimal(10,2) DEFAULT NULL,
  `dinero_cierre` decimal(10,2) DEFAULT NULL,
  `estado` enum('abierto','cerrado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'abierto',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `venta_credito` decimal(10,2) DEFAULT NULL,
  `venta_tarjetas` decimal(10,2) DEFAULT NULL,
  `venta_efectivo` decimal(10,2) DEFAULT NULL,
  `venta_descuentos` decimal(10,2) DEFAULT NULL,
  `tanque_super_pulgadas` decimal(8,2) DEFAULT NULL,
  `tanque_regular_pulgadas` decimal(8,2) DEFAULT NULL,
  `tanque_diesel_pulgadas` decimal(8,2) DEFAULT NULL,
  `tanque_super_galones` decimal(10,2) DEFAULT NULL,
  `tanque_regular_galones` decimal(10,2) DEFAULT NULL,
  `tanque_diesel_galones` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `turnos_gasolinera_id_foreign` (`gasolinera_id`),
  KEY `turnos_user_id_foreign` (`user_id`),
  CONSTRAINT `turnos_gasolinera_id_foreign` FOREIGN KEY (`gasolinera_id`) REFERENCES `gasolineras` (`id`) ON DELETE CASCADE,
  CONSTRAINT `turnos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=309 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `turnos`
--

LOCK TABLES `turnos` WRITE;
/*!40000 ALTER TABLE `turnos` DISABLE KEYS */;
INSERT INTO `turnos` VALUES (305,4,10,'2025-09-13','20:26:10','20:36:31',0.00,0.00,'cerrado','2025-09-13 20:26:10','2025-09-13 20:36:31',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `turnos` ENABLE KEYS */;
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
  `gasolinera_id` bigint unsigned DEFAULT NULL,
  `tipo_usuario` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'operador',
  `theme` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'default',
  `theme_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_gasolinera_id_foreign` (`gasolinera_id`),
  CONSTRAINT `users_gasolinera_id_foreign` FOREIGN KEY (`gasolinera_id`) REFERENCES `gasolineras` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrador Principal','admin@gasolinera.com',NULL,'$2y$12$Lyf/CAbPVTLeonuf0k15B.R7FEE2P31sppP9vHdYO87Gy4t6ysrhW',NULL,'2025-08-07 13:11:48','2025-08-07 13:11:48',NULL,'admin','default',NULL),(4,'M Admin','m@epic.gt','2025-08-07 19:53:16','$2y$12$IHmYeyA6keJqyW9yrZOc8ufSJC0ZoLN/BV8OvMtu1f4AB9aLGTH2.',NULL,'2025-08-07 19:53:16','2025-08-07 19:53:16',NULL,'admin','default',NULL),(10,'MK1','mk1@mk.epic',NULL,'$2y$12$Bw7qUbvgfSiKsUrA/9I/CeclaleqebKkFHUue6qxbmxtzeefMOJfe',NULL,'2025-09-13 20:12:23','2025-09-13 20:12:45',4,'operador','default',NULL),(11,'MK2','mk2@mk.epic',NULL,'$2y$12$odlyQNHUutJervr.lZcbZu8Kzb8PuWJXqQsLBhkWps5IydLZsAwuG',NULL,'2025-09-13 20:13:11','2025-09-13 20:13:11',5,'operador','default',NULL),(12,'MK3','mk3@mk.epic',NULL,'$2y$12$7g03eJt1UW68SVdPlpJQ2.F42iApYh4qiGJMZJBPba/RidneNg8ky',NULL,'2025-09-13 20:13:43','2025-09-13 20:13:43',6,'operador','default',NULL),(13,'MK4','mk4@mk.epic',NULL,'$2y$12$zjAIQijvvCj07n3OQYi6aOz4F9RdXfEWAzdgGpCBngKWyIPkm11li',NULL,'2025-09-13 20:14:05','2025-09-13 20:14:05',7,'operador','default',NULL);
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

-- Dump completed on 2025-09-14  3:09:57

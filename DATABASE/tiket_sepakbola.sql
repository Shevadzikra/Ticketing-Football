-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: tiket_sepakbola
-- ------------------------------------------------------
-- Server version	8.0.30

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
-- Table structure for table `matches`
--

DROP TABLE IF EXISTS `matches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `matches` (
  `id` int NOT NULL AUTO_INCREMENT,
  `team_home` varchar(100) NOT NULL,
  `team_away` varchar(100) NOT NULL,
  `match_date` datetime NOT NULL,
  `stadium` varchar(100) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_completed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matches`
--

LOCK TABLES `matches` WRITE;
/*!40000 ALTER TABLE `matches` DISABLE KEYS */;
INSERT INTO `matches` VALUES (1,'Thunder FC','Sky Hawks','2025-05-10 16:00:00','National Stadium','Big match penentuan juara liga.','2025-05-01 03:30:00',0),(2,'Ocean Warriors','Blazing Tigers','2025-05-12 19:00:00','Seaside Arena','Pertandingan sengit antara rival sekota.','2025-05-02 04:00:00',0),(3,'Sky Hawks','Golden Eagles','2025-05-15 14:30:00','National Stadium','Pertandingan semi-final yang ditunggu.','2025-05-03 02:45:00',0),(4,'Thunder FC','Ocean Warriors','2025-04-28 18:00:00','Thunder Park','Laga seru dengan peluang besar bagi Thunder FC.','2025-04-20 05:00:00',0),(5,'Blazing Tigers','Golden Eagles','2025-04-30 20:00:00','Tiger Arena','Pertandingan untuk memperebutkan posisi 3.','2025-04-21 07:20:00',0);
/*!40000 ALTER TABLE `matches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `ticket_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `ticket_id` (`ticket_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,12,14,2,150000.00),(2,13,16,4,30000.00),(3,14,16,2,30000.00),(4,15,16,2,30000.00),(5,16,16,1,30000.00),(6,17,16,1,30000.00);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'completed',
  `payment_method` varchar(50) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (12,2,'2025-05-03 13:40:59',300000.00,'pending','bank_transfer','budi','budi@gmail.com','085335363218'),(13,5,'2025-05-03 22:57:54',120000.00,'completed','bank_transfer','lavi','lavi@gmail.ocm','08123456'),(14,5,'2025-05-03 23:48:33',60000.00,'completed','bank_transfer','lavi','lavi@gmail.ocm','08123456'),(15,5,'2025-05-04 00:06:05',60000.00,'completed','credit_card','lavi','lavi@gmail.ocm','08123456'),(16,5,'2025-05-04 00:09:31',30000.00,'completed','bank_transfer','lavi','lavi@gmail.ocm','08123456'),(17,5,'2025-05-04 00:19:33',30000.00,'completed','credit_card','lavi','lavi@gmail.ocm','08123456');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seats`
--

DROP TABLE IF EXISTS `seats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `match_id` int NOT NULL,
  `ticket_id` int NOT NULL,
  `seat_number` varchar(10) NOT NULL,
  `seat_row` varchar(5) NOT NULL,
  `seat_column` varchar(5) NOT NULL,
  `status` enum('available','booked','reserved') DEFAULT 'available',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_seat` (`match_id`,`seat_number`),
  KEY `idx_seats_ticket` (`ticket_id`),
  KEY `idx_seats_status` (`status`),
  CONSTRAINT `fk_seats_tickets` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_seats_tickets_match` FOREIGN KEY (`match_id`) REFERENCES `tickets` (`match_id`) ON DELETE CASCADE,
  CONSTRAINT `seats_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `seats_ibfk_2` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1151 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seats`
--

LOCK TABLES `seats` WRITE;
/*!40000 ALTER TABLE `seats` DISABLE KEYS */;
INSERT INTO `seats` VALUES (1090,1,14,'A1','A','1','booked'),(1091,1,14,'A2','A','2','available'),(1092,1,14,'A3','A','3','available'),(1093,1,14,'A4','A','4','available'),(1094,1,14,'A5','A','5','available'),(1095,1,14,'A6','A','6','available'),(1096,1,14,'A7','A','7','available'),(1097,1,14,'A8','A','8','available'),(1098,1,14,'A9','A','9','available'),(1099,1,14,'A10','A','10','booked'),(1100,1,14,'A11','A','11','booked'),(1101,1,14,'A12','A','12','booked'),(1102,1,14,'A13','A','13','available'),(1103,1,14,'A14','A','14','available'),(1104,1,14,'A15','A','15','available'),(1105,1,14,'A16','A','16','available'),(1106,1,14,'A17','A','17','available'),(1107,1,14,'A18','A','18','available'),(1108,1,14,'A19','A','19','available'),(1109,1,14,'A20','A','20','available'),(1126,3,16,'A1','A','1','booked'),(1127,3,16,'A2','A','2','booked'),(1128,3,16,'A3','A','3','booked'),(1129,3,16,'A4','A','4','booked'),(1130,3,16,'A5','A','5','available'),(1131,3,16,'A6','A','6','available'),(1132,3,16,'A7','A','7','available'),(1133,3,16,'A8','A','8','available'),(1134,3,16,'A9','A','9','available'),(1135,3,16,'A10','A','10','booked'),(1136,3,16,'A11','A','11','booked'),(1137,3,16,'A12','A','12','booked'),(1138,3,16,'A13','A','13','booked'),(1139,3,16,'A14','A','14','booked'),(1140,3,16,'A15','A','15','booked'),(1141,3,16,'A16','A','16','available'),(1142,3,16,'A17','A','17','available'),(1143,3,16,'A18','A','18','available'),(1144,3,16,'A19','A','19','available'),(1145,3,16,'A20','A','20','available'),(1146,3,16,'B1','B','1','available'),(1147,3,16,'B2','B','2','available'),(1148,3,16,'B3','B','3','available'),(1149,3,16,'B4','B','4','available'),(1150,3,16,'B5','B','5','available');
/*!40000 ALTER TABLE `seats` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_seat_insert` AFTER INSERT ON `seats` FOR EACH ROW BEGIN
  UPDATE tickets 
  SET quantity_available = (
    SELECT COUNT(*) 
    FROM seats 
    WHERE ticket_id = NEW.ticket_id AND status = 'available'
  )
  WHERE id = NEW.ticket_id;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_seat_update` AFTER UPDATE ON `seats` FOR EACH ROW BEGIN
  IF NEW.status <> OLD.status OR NEW.ticket_id <> OLD.ticket_id THEN
    UPDATE tickets 
    SET quantity_available = (
      SELECT COUNT(*) 
      FROM seats 
      WHERE ticket_id = NEW.ticket_id AND status = 'available'
    )
    WHERE id = NEW.ticket_id;
    
    IF NEW.ticket_id <> OLD.ticket_id THEN
      UPDATE tickets 
      SET quantity_available = (
        SELECT COUNT(*) 
        FROM seats 
        WHERE ticket_id = OLD.ticket_id AND status = 'available'
      )
      WHERE id = OLD.ticket_id;
    END IF;
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_seat_delete` AFTER DELETE ON `seats` FOR EACH ROW BEGIN
  UPDATE tickets 
  SET quantity_available = (
    SELECT COUNT(*) 
    FROM seats 
    WHERE ticket_id = OLD.ticket_id AND status = 'available'
  )
  WHERE id = OLD.ticket_id;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Temporary view structure for view `ticket_availability_real`
--

DROP TABLE IF EXISTS `ticket_availability_real`;
/*!50001 DROP VIEW IF EXISTS `ticket_availability_real`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `ticket_availability_real` AS SELECT 
 1 AS `id`,
 1 AS `match_id`,
 1 AS `ticket_type`,
 1 AS `price`,
 1 AS `description`,
 1 AS `real_quantity_available`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `match_id` int NOT NULL,
  `ticket_type` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity_available` int DEFAULT '0',
  `description` text,
  PRIMARY KEY (`id`),
  KEY `match_id` (`match_id`),
  CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES (14,1,'Thunder FC vs Sky Hawks',150000.00,16,'Big match penentuan juara liga.'),(16,3,'Sky Hawks vs Golden Eagles',30000.00,15,'Pertandingan semi-final yang ditunggu.');
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'budi','$2y$10$DyQTnvUAEkHKW0LoU3mntuN77duWyGHLR.Q9MkE540xEqrUzX.SlO','budi@gmail.com','bubudidi','12345678490',0,'2025-04-29 04:34:00'),(2,'admin','admin','admin@gmail.com','admin','123456789',1,'2025-04-29 05:49:30'),(3,'bubu','$2y$10$UUeV3SnOyayjUvytpMCUe.Q.A.5k6O576anEqLOen959cFdR0e.G2','bubu@gmail.com','bububaba','123',0,'2025-04-29 07:47:15'),(4,'bob','$2y$10$JIy9W/OHJOckuDk/yftaOekQ370zxo42fh2Ve/aaf8UlS/EvMn1ju','bob@gmail.com','bobob','123',0,'2025-04-29 11:35:22'),(5,'lavi','$2y$10$N7pUUhaD2k66r6YEsaM2MOATTny5WBpbire8hKKq1V.eVn8BV42Z6','lavi@gmail.ocm','lavi','081234567',0,'2025-05-03 22:53:01'),(6,'vava','$2y$10$0kCoAEx1YlFTt1FYsi.eaODoAROio6OMr324wkRAG08gA3dwPLI5e','vava@gmail.com','vava','123456789',0,'2025-05-03 23:38:14');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `ticket_availability_real`
--

/*!50001 DROP VIEW IF EXISTS `ticket_availability_real`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `ticket_availability_real` AS select `t`.`id` AS `id`,`t`.`match_id` AS `match_id`,`t`.`ticket_type` AS `ticket_type`,`t`.`price` AS `price`,`t`.`description` AS `description`,count(`s`.`id`) AS `real_quantity_available` from (`tickets` `t` left join `seats` `s` on(((`t`.`id` = `s`.`ticket_id`) and (`s`.`status` = 'available')))) group by `t`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-04  7:45:15

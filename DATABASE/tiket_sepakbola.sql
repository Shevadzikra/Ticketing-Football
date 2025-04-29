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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matches`
--

LOCK TABLES `matches` WRITE;
/*!40000 ALTER TABLE `matches` DISABLE KEYS */;
INSERT INTO `matches` VALUES (1,'Real Madrid','Barcelona FC','2025-05-16 01:55:17','Stadion Diponegoro','Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.','2025-04-29 01:55:33'),(2,'Persib Bandung','Arema FC','2025-12-01 19:00:00','Stadion Gelora Bandung Lautan Api','Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.','2025-04-29 02:01:36'),(3,'Persija Jakarta','Bali United','2024-01-05 20:30:00','Stadion GBK','Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.','2025-04-29 02:01:36'),(5,'Tim Boyboyboi','Tim Upin Ipin','2025-05-10 14:22:00','Stadion Hura Hura','Lorem Ipsum','2025-04-29 07:22:42'),(6,'Tim Loerm','Tip Ipsum','2025-05-10 15:10:00','Stadion Hura Hura','Lorem ipsum','2025-04-29 08:10:42');
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
  `seat_id` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `seat_id` (`seat_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (6,7,1,1,1,10000.00),(7,7,1,2,1,10000.00),(8,7,1,5,1,10000.00),(9,9,1,NULL,1,10000.00);
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
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (7,3,'2025-04-29 08:08:56',30000.00,'pending','e_wallet','bubudidi','budi@gmail.com','123'),(8,4,'2025-04-29 13:00:31',0.00,'pending','bank_transfer','bobob','bob@gmail.com','123'),(9,4,'2025-04-29 13:00:42',10000.00,'pending','bank_transfer','bobob','bob@gmail.com','123');
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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seats`
--

LOCK TABLES `seats` WRITE;
/*!40000 ALTER TABLE `seats` DISABLE KEYS */;
INSERT INTO `seats` VALUES (1,1,1,'A1-001','A1','001','booked'),(2,1,1,'A1-002','A1','002','booked'),(3,1,1,'A1-003','A1','003','available'),(4,1,1,'A2-001','A2','001','available'),(5,1,1,'A2-002','A2','002','available'),(6,1,1,'B1-001','B1','001','available'),(7,1,1,'B1-002','B1','002','available'),(8,1,1,'B1-003','B1','003','booked'),(9,1,1,'B2-001','B2','001','available'),(10,1,1,'B2-002','B2','002','available'),(11,1,1,'C1-001','C1','001','reserved'),(12,1,1,'C1-002','C1','002','reserved'),(13,1,1,'C1-003','C1','003','reserved'),(14,1,1,'C2-001','C2','001','reserved'),(15,1,1,'C2-002','C2','002','reserved'),(16,2,2,'A1-001','A1','001','available'),(17,2,2,'A1-002','A1','002','booked'),(18,2,2,'A1-003','A1','003','available'),(19,2,2,'A2-001','A2','001','booked'),(20,2,2,'A2-002','A2','002','booked'),(21,2,2,'A3001','A3','001','available'),(22,2,2,'A4001','A4','001','available'),(23,2,2,'A5001','A5','001','available'),(24,2,2,'A6001','A6','001','available'),(25,6,6,'A1001','A1','001','available'),(26,6,6,'A2001','A2','001','available');
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
  `quantity_available` int NOT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `match_id` (`match_id`),
  CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES (1,1,'indo',10000.00,6,'hura'),(2,2,'tiket',15000.00,6,'hura'),(6,6,'Tiket',90000.00,2,'lorem');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'budi','$2y$10$DyQTnvUAEkHKW0LoU3mntuN77duWyGHLR.Q9MkE540xEqrUzX.SlO','budi@gmail.com','bubudidi','12345678490',0,'2025-04-29 04:34:00'),(2,'admin','admin','admin@gmail.com','admin','123456789',1,'2025-04-29 05:49:30'),(3,'bubu','$2y$10$UUeV3SnOyayjUvytpMCUe.Q.A.5k6O576anEqLOen959cFdR0e.G2','bubu@gmail.com','bububaba','123',0,'2025-04-29 07:47:15'),(4,'bob','$2y$10$JIy9W/OHJOckuDk/yftaOekQ370zxo42fh2Ve/aaf8UlS/EvMn1ju','bob@gmail.com','bobob','123',0,'2025-04-29 11:35:22');
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

-- Dump completed on 2025-04-29 21:46:07

-- MySQL dump 10.13  Distrib 5.5.20, for debian6.0 (i686)
--
-- Host: localhost    Database: pfleet
-- ------------------------------------------------------
-- Server version	5.5.17

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `payment_setup`
--
USE `pfleet`;

TRUNCATE `payments`;
TRUNCATE `payment_setup`;
TRUNCATE `deductions`;
TRUNCATE `deduction_setup`;

LOCK TABLES `payment_setup` WRITE;
/*!40000 ALTER TABLE `payment_setup` DISABLE KEYS */;
INSERT INTO `payment_setup` VALUES (1,1,2,'Delivery','Delivery','Delivery - Standard','Delivery',0,'','423423','',1,1,2,'2012-04-04','2012-05-01',75.0000),(2,1,2,'Mileage','Mileage Std','Mileage - Standard','Mileage',0,'','4234','',0,2,1,'2012-05-05','2012-05-31',0.9870);
/*!40000 ALTER TABLE `payment_setup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,1,'Salary','hourly','','0000-00-00','0000-00-00','','',40,20.0000,800.0000,'','0000-00-00','2012-05-23','0000-00-00 00:00:00',NULL,'2012-05-21 15:49:40',3,1,2),(2,1,'Salary','overtime','','0000-00-00','0000-00-00','','',10,30.0000,300.0000,'','0000-00-00','2012-05-23','0000-00-00 00:00:00',NULL,'2012-05-21 15:51:01',3,1,2),(3,1,'Bonus','Commission','','0000-00-00','0000-00-00','','',3000,0.1000,300.0000,'','0000-00-00','0000-00-00','0000-00-00 00:00:00',NULL,'2012-05-21 15:52:22',3,1,2);
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `deduction_setup`
--

LOCK TABLES `deduction_setup` WRITE;
/*!40000 ALTER TABLE `deduction_setup` DISABLE KEYS */;
INSERT INTO `deduction_setup` VALUES (1,1,2,'TRL','Truck Lease','Truck','','3224','FuelCode',NULL,1,1,2,0,'0000-00-00','0000-00-00','0000-00-00',300.0000,1,NULL,NULL),(2,1,2,'FUL','Fuel Cards','Fuel','','423423','',NULL,0,1,2,7,'0000-00-00','0000-00-00','0000-00-00',25.0000,0,NULL,NULL);
/*!40000 ALTER TABLE `deduction_setup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `deductions`
--

LOCK TABLES `deductions` WRITE;
/*!40000 ALTER TABLE `deductions` DISABLE KEYS */;
INSERT INTO `deductions` VALUES (1,1,'','health insurance',NULL,'','0000-00-00','0000-00-00','','','',150.0000,1,150,'0000-00-00','2012-05-22',150.0000,0.0000,0.0000,0,0,'0000-00-00 00:00:00',NULL,'2012-05-21 16:26:20',3,1,2),(2,1,'','uniforms',NULL,'','0000-00-00','0000-00-00','','','',200.0000,1,200,'0000-00-00','0000-00-00',200.0000,0.0000,0.0000,0,0,'0000-00-00 00:00:00',NULL,'2012-05-21 16:27:11',3,1,2);
/*!40000 ALTER TABLE `deductions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-05-22  9:28:22

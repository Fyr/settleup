-- MySQL dump 10.13  Distrib 5.5.25, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: pfleet
-- ------------------------------------------------------
-- Server version	5.5.25-1~ppa1~oneiric

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
-- Table structure for table `bank_account`
--

DROP TABLE IF EXISTS `bank_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bank_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity_id` int(10) unsigned NOT NULL,
  `account_nickname` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `payment_type` int(10) unsigned NOT NULL,
  `process` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `account_type` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `name_on_account` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `amount` decimal(10,4) DEFAULT NULL,
  `percentage` decimal(10,4) DEFAULT NULL,
  `priority` int(10) DEFAULT NULL,
  `limit_type` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_bank_account_entity_id` (`entity_id`),
  KEY `fk_bank_account_payment_type` (`payment_type`),
  KEY `fk_bank_account_limit_type` (`limit_type`),
  CONSTRAINT `fk_bank_account_payment_type` FOREIGN KEY (`payment_type`) REFERENCES `payment_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bank_account_limit_type` FOREIGN KEY (`limit_type`) REFERENCES `bank_account_limit_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bank_account_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_account`
--

LOCK TABLES `bank_account` WRITE;
/*!40000 ALTER TABLE `bank_account` DISABLE KEYS */;
INSERT INTO `bank_account` VALUES (1,1,'Priorbank',2,'some process','Priorbank Account Type','Priorbank Name',0.0000,9.9900,NULL,1),(2,1,'National',1,'National Bank process','National Bank Account Type','National Bank name',0.0000,15.0000,NULL,1),(3,19,'Ven1AmountAccount',1,'','','Ven1AmountAccount',200.0000,NULL,2,2),(4,19,'Ven1PersentAccount',1,'','','Ven1PersentAccount',NULL,50.0000,3,1),(5,20,'Ven2AmountAccount',1,'','','Ven2AmountAccount',200.0000,NULL,4,2),(6,20,'Ven2PersentAccount',1,'','','Ven2AmountAccount',NULL,50.0000,5,1),(7,16,'CON1Account',2,'','','CON1Account',NULL,100.0000,0,1),(8,17,'CON2Account',1,'','','CON2Account',NULL,100.0000,0,1),(9,18,'CON3Account',1,'','','CON3Account',NULL,100.0000,0,1),(10,15,'CAR1',1,'','','CAR1 Account',100.0000,NULL,0,2);
/*!40000 ALTER TABLE `bank_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bank_account_ach`
--

DROP TABLE IF EXISTS `bank_account_ach`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bank_account_ach` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bank_account_id` int(10) unsigned NOT NULL,
  `ACH_bank_routing_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `ACH_bank_account_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`bank_account_id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_bank_account_ach_bank_account_id` (`bank_account_id`),
  CONSTRAINT `fk_bank_account_ach_bank_account_id` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_account` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_account_ach`
--

LOCK TABLES `bank_account_ach` WRITE;
/*!40000 ALTER TABLE `bank_account_ach` DISABLE KEYS */;
INSERT INTO `bank_account_ach` VALUES (1,1,'1234','5678'),(2,7,'123456','234567');
/*!40000 ALTER TABLE `bank_account_ach` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bank_account_cc`
--

DROP TABLE IF EXISTS `bank_account_cc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bank_account_cc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bank_account_id` int(10) unsigned NOT NULL,
  `card_number` varchar(255) COLLATE utf8_bin NOT NULL,
  `name_on_card` varchar(255) COLLATE utf8_bin NOT NULL,
  `CC_billing_address` varchar(255) COLLATE utf8_bin NOT NULL,
  `CC_city` varchar(255) COLLATE utf8_bin NOT NULL,
  `CC_state` varchar(255) COLLATE utf8_bin NOT NULL,
  `CC_zip` varchar(255) COLLATE utf8_bin NOT NULL,
  `expiration_date` date NOT NULL,
  `cvs_code` int(11) NOT NULL,
  PRIMARY KEY (`bank_account_id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_bank_account_cc_bank_account_id` (`bank_account_id`),
  CONSTRAINT `fk_bank_account_cc_bank_account_id` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_account` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_account_cc`
--

LOCK TABLES `bank_account_cc` WRITE;
/*!40000 ALTER TABLE `bank_account_cc` DISABLE KEYS */;
/*!40000 ALTER TABLE `bank_account_cc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bank_account_check`
--

DROP TABLE IF EXISTS `bank_account_check`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bank_account_check` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bank_account_id` int(10) unsigned NOT NULL,
  `bank_name` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`bank_account_id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_bank_account_check_bank_account_id` (`bank_account_id`),
  CONSTRAINT `fk_bank_account_check_bank_account_id` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_account` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_account_check`
--

LOCK TABLES `bank_account_check` WRITE;
/*!40000 ALTER TABLE `bank_account_check` DISABLE KEYS */;
INSERT INTO `bank_account_check` VALUES (1,2,'National Bank'),(2,3,'Priorbank'),(3,4,'BPSBank'),(4,5,'Priorbank'),(5,6,'BPSBank'),(7,8,'Priorbank'),(8,9,'Priorbank'),(9,10,'Priorbank');
/*!40000 ALTER TABLE `bank_account_check` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bank_account_history`
--

DROP TABLE IF EXISTS `bank_account_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bank_account_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bank_account_id` int(10) unsigned NOT NULL,
  `account_nickname` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `payment_type` int(10) unsigned NOT NULL,
  `process` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `ACH_bank_routing_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `ACH_bank_account_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `account_type` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `name_on_account` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `bank_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `card_number` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `name_on_card` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `CC_billing_address` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `CC_city` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `CC_state` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `CC_zip` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `cvs_code` int(11) DEFAULT NULL,
  `amount` decimal(10,4) DEFAULT NULL,
  `percentage` decimal(10,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_bank_account_history_payment_type` (`payment_type`),
  KEY `fk_bank_account_history_bank_account_id` (`bank_account_id`),
  CONSTRAINT `fk_bank_account_history_payment_type` FOREIGN KEY (`payment_type`) REFERENCES `payment_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bank_account_history_bank_account_id` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_account_history`
--

LOCK TABLES `bank_account_history` WRITE;
/*!40000 ALTER TABLE `bank_account_history` DISABLE KEYS */;
INSERT INTO `bank_account_history` VALUES (1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,7,'CON1Account',2,'','123456','234567','','CON1Account',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,100.0000),(3,8,'CON2Account',1,'',NULL,NULL,'','CON2Account','Priorbank',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,100.0000),(4,9,'CON3Account',1,'',NULL,NULL,'','CON3Account','Priorbank',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,100.0000),(5,10,'CAR1',1,'',NULL,NULL,'','CAR1 Account','Priorbank',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,100.0000,NULL),(6,4,'Ven1PersentAccount',1,'',NULL,NULL,'','Ven1PersentAccount','BPSBank',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,50.0000),(7,3,'Ven1AmountAccount',1,'',NULL,NULL,'','Ven1AmountAccount','Priorbank',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,200.0000,NULL),(8,6,'Ven2PersentAccount',1,'',NULL,NULL,'','Ven2AmountAccount','BPSBank',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,50.0000),(9,5,'Ven2AmountAccount',1,'',NULL,NULL,'','Ven2AmountAccount','Priorbank',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,200.0000,NULL);
/*!40000 ALTER TABLE `bank_account_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bank_account_limit_type`
--

DROP TABLE IF EXISTS `bank_account_limit_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bank_account_limit_type` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_account_limit_type`
--

LOCK TABLES `bank_account_limit_type` WRITE;
/*!40000 ALTER TABLE `bank_account_limit_type` DISABLE KEYS */;
INSERT INTO `bank_account_limit_type` VALUES (1,'Percentage'),(2,'Amount');
/*!40000 ALTER TABLE `bank_account_limit_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carrier`
--

DROP TABLE IF EXISTS `carrier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carrier` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity_id` int(10) unsigned NOT NULL,
  `tax_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `short_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `contact` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`entity_id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_carrier_entity_id` (`entity_id`),
  CONSTRAINT `fk_carrier_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrier`
--

LOCK TABLES `carrier` WRITE;
/*!40000 ALTER TABLE `carrier` DISABLE KEYS */;
INSERT INTO `carrier` VALUES (1,1,'123951753','SWI','Southwest Intermodal','Jay Abraham'),(3,12,'1234','JD','John Doe',NULL),(4,15,'CAR1','CAR1','Carrier1','car1 contact');
/*!40000 ALTER TABLE `carrier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carrier_contractor`
--

DROP TABLE IF EXISTS `carrier_contractor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carrier_contractor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(10) unsigned NOT NULL,
  `contractor_id` int(10) unsigned NOT NULL,
  `status` int(10) unsigned NOT NULL,
  `start_date` date DEFAULT NULL,
  `termination_date` date DEFAULT NULL,
  `rehire_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_carrier_contractor_carrier_id` (`carrier_id`),
  KEY `fk_carrier_contractor_contractor_id` (`contractor_id`),
  KEY `fk_carrier_contractor_status` (`status`),
  CONSTRAINT `fk_carrier_contractor_carrier_id` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_carrier_contractor_contractor_id` FOREIGN KEY (`contractor_id`) REFERENCES `contractor` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_carrier_contractor_status` FOREIGN KEY (`status`) REFERENCES `contractor_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrier_contractor`
--

LOCK TABLES `carrier_contractor` WRITE;
/*!40000 ALTER TABLE `carrier_contractor` DISABLE KEYS */;
INSERT INTO `carrier_contractor` VALUES (1,1,2,1,'2012-06-07',NULL,NULL),(2,1,3,1,'2012-06-07',NULL,NULL),(3,12,2,1,'2012-06-29',NULL,NULL),(4,12,3,1,'2012-06-29',NULL,NULL),(5,12,4,1,'2012-06-29',NULL,NULL),(6,12,5,1,'2012-06-29',NULL,NULL),(7,12,6,1,'2012-06-29',NULL,NULL),(8,12,7,1,'2012-06-29',NULL,NULL),(9,15,16,1,'2012-07-11',NULL,NULL),(10,15,17,1,'2012-07-11',NULL,NULL),(11,15,18,1,'2012-07-11',NULL,NULL);
/*!40000 ALTER TABLE `carrier_contractor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carrier_vendor`
--

DROP TABLE IF EXISTS `carrier_vendor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carrier_vendor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(10) unsigned NOT NULL,
  `vendor_id` int(10) unsigned NOT NULL,
  `status` int(10) unsigned NOT NULL,
  `start_date` date DEFAULT NULL,
  `termination_date` date DEFAULT NULL,
  `rehire_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_carrier_vendor_carrier_id` (`carrier_id`),
  KEY `fk_carrier_vendor_vendor_id` (`vendor_id`),
  KEY `fk_carrier_vendor_status` (`status`),
  CONSTRAINT `fk_carrier_vendor_status` FOREIGN KEY (`status`) REFERENCES `vendor_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_carrier_vendor_carrier_id` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_carrier_vendor_vendor_id` FOREIGN KEY (`vendor_id`) REFERENCES `vendor` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrier_vendor`
--

LOCK TABLES `carrier_vendor` WRITE;
/*!40000 ALTER TABLE `carrier_vendor` DISABLE KEYS */;
/*!40000 ALTER TABLE `carrier_vendor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contractor`
--

DROP TABLE IF EXISTS `contractor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contractor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity_id` int(10) unsigned NOT NULL,
  `social_security_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `tax_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `state_of_operation` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `classification` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `status` int(10) unsigned NOT NULL,
  `division` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `department` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `route` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `termination_date` date DEFAULT NULL,
  `rehire_date` date DEFAULT NULL,
  `rehire_status` int(11) DEFAULT NULL,
  `correspondence_method` int(10) unsigned NOT NULL,
  PRIMARY KEY (`entity_id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_contractor_status` (`status`),
  KEY `fk_contractor_correspondence_method` (`correspondence_method`),
  KEY `fk_contactor_entity_id` (`entity_id`),
  CONSTRAINT `fk_contractor_status` FOREIGN KEY (`status`) REFERENCES `contractor_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_contractor_correspondence_method` FOREIGN KEY (`correspondence_method`) REFERENCES `entity_contact_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_contactor_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contractor`
--

LOCK TABLES `contractor` WRITE;
/*!40000 ALTER TABLE `contractor` DISABLE KEYS */;
INSERT INTO `contractor` VALUES (1,2,'456159753','123956182','MSC Cyprus','Bernard','Arnault','CA','1980-04-25','',1,'Southwest','Lowes','104555','2010-11-15','2010-12-31','2011-01-04',NULL,1),(2,3,'987654320','451263897','Navibulgar','David','Bach','AZ','1970-07-25','',1,'Southwest','Home Depot','334332','2007-04-03','2007-08-03','2007-12-03',NULL,1),(3,4,'777668888','887874822','John\'s Transport','John','Smith','WA','0000-00-00','',1,'Northwest','Lowes','887878','2012-05-18','2012-05-31','2012-05-31',NULL,1),(4,5,'123456789','234567890','Best Delivery','Jim','Dalton','NV','0000-00-00','',1,'Southwest','Home Depot','334223','2012-10-10','2012-05-19','2012-05-31',NULL,1),(5,6,'222334444','123334444','Ken\'s Transport','Ken','Adams','FL','0000-00-00','',1,'Southeast','Home Depot','77878','2012-05-04','2012-05-26','2012-05-30',NULL,9),(6,7,'666558888','348887676','Quick Delivery','John','Quick','GA','0000-00-00','',1,'Southeast','Best Buy','888788','2012-05-03','2012-05-05','2012-05-17',NULL,1),(7,8,'999554545','763434555','Gonazales Delivery','Hector','Gonzales','AZ','0000-00-00','',1,'Southwest','Best Buy','776776','2012-05-11','2012-05-12','2012-05-19',NULL,1),(8,16,'546874290','543969027','IvanovCon1','Ivan','Ivanov','II','2010-01-01','',1,'Southwest','Lowes','104555','2010-11-15','2010-12-31','2011-01-04',NULL,1),(9,17,'786874290','543945627','PetrovCon2','Petr','Petrov','PP','2010-01-01','',1,'Southwest','Lowes','104555','2010-11-15','2010-12-31','2011-01-04',NULL,1),(10,18,'546898579','54567678','SidorovCon3','Sidr','Sidorov','SS','2010-01-01','',1,'Southwest','Lowes','104555','2010-11-15','2010-12-31','2011-01-04',NULL,1);
/*!40000 ALTER TABLE `contractor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contractor_code`
--

DROP TABLE IF EXISTS `contractor_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contractor_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(10) unsigned NOT NULL,
  `contractor_id` int(10) unsigned NOT NULL,
  `code` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `carrier_id_code_UNIQUE` (`carrier_id`,`code`),
  KEY `fk_contractor_code_carrier_id` (`carrier_id`),
  KEY `fk_contractor_code_contractor_id` (`contractor_id`),
  CONSTRAINT `fk_contractor_code_carrier_id` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_contractor_code_contractor_id` FOREIGN KEY (`contractor_id`) REFERENCES `contractor` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contractor_code`
--

LOCK TABLES `contractor_code` WRITE;
/*!40000 ALTER TABLE `contractor_code` DISABLE KEYS */;
INSERT INTO `contractor_code` VALUES (2,1,2,563),(3,1,3,866),(4,12,2,1),(5,12,3,2),(6,12,4,3),(7,12,5,4),(8,12,6,5),(9,12,7,6),(10,15,16,1),(11,15,17,2),(12,15,18,3);
/*!40000 ALTER TABLE `contractor_code` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contractor_status`
--

DROP TABLE IF EXISTS `contractor_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contractor_status` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contractor_status`
--

LOCK TABLES `contractor_status` WRITE;
/*!40000 ALTER TABLE `contractor_status` DISABLE KEYS */;
INSERT INTO `contractor_status` VALUES (1,'Active'),(2,'Leave'),(3,'Terminated'),(4,'Not configured');
/*!40000 ALTER TABLE `contractor_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cycle_date`
--

DROP TABLE IF EXISTS `cycle_date`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cycle_date` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cycle_type` int(10) unsigned NOT NULL,
  `cycle_owner` int(10) unsigned NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_cycle_date_cycle_type` (`cycle_type`),
  KEY `fk_cycle_date_cycle_owner` (`cycle_owner`),
  CONSTRAINT `fk_cycle_date_cycle_type` FOREIGN KEY (`cycle_type`) REFERENCES `cycle_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_cycle_date_cycle_owner` FOREIGN KEY (`cycle_owner`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cycle_date`
--

LOCK TABLES `cycle_date` WRITE;
/*!40000 ALTER TABLE `cycle_date` DISABLE KEYS */;
/*!40000 ALTER TABLE `cycle_date` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cycle_period`
--

DROP TABLE IF EXISTS `cycle_period`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cycle_period` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cycle_period`
--

LOCK TABLES `cycle_period` WRITE;
/*!40000 ALTER TABLE `cycle_period` DISABLE KEYS */;
INSERT INTO `cycle_period` VALUES (1,'Weekly'),(2,'Biweekly'),(3,'Monthly'),(4,'Semy-monthly');
/*!40000 ALTER TABLE `cycle_period` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cycle_type`
--

DROP TABLE IF EXISTS `cycle_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cycle_type` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cycle_type`
--

LOCK TABLES `cycle_type` WRITE;
/*!40000 ALTER TABLE `cycle_type` DISABLE KEYS */;
INSERT INTO `cycle_type` VALUES (1,'Close'),(2,'Disbursement');
/*!40000 ALTER TABLE `cycle_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deduction_setup`
--

DROP TABLE IF EXISTS `deduction_setup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deduction_setup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `provider_id` int(10) unsigned NOT NULL,
  `contractor_id` int(10) unsigned DEFAULT NULL,
  `vendor_deduction_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `category` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `department` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `gl_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `disbursement_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `recurring` int(11) DEFAULT NULL,
  `level_id` int(10) unsigned NOT NULL,
  `billing_cycle_id` int(10) unsigned NOT NULL,
  `terms` int(11) DEFAULT NULL,
  `rate` decimal(10,4) DEFAULT NULL,
  `eligible` int(11) DEFAULT NULL,
  `reserve_account_receiver` int(10) unsigned DEFAULT NULL,
  `first_start_day` int(10) DEFAULT NULL,
  `second_start_day` int(10) DEFAULT NULL,
  `deduction_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_deduction_setup_level_id` (`level_id`),
  KEY `fk_deduction_setup_billing_cycle_id` (`billing_cycle_id`),
  KEY `fk_deduction_setup_provider_id` (`provider_id`),
  KEY `fk_deduction_setup_contractor_id` (`contractor_id`),
  KEY `fk_deduction_setup_reserve_account_receiver` (`reserve_account_receiver`),
  CONSTRAINT `fk_deduction_setup_billing_cycle_id` FOREIGN KEY (`billing_cycle_id`) REFERENCES `cycle_period` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deduction_setup_contractor_id` FOREIGN KEY (`contractor_id`) REFERENCES `contractor` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deduction_setup_level_id` FOREIGN KEY (`level_id`) REFERENCES `setup_level` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deduction_setup_provider_id` FOREIGN KEY (`provider_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deduction_setup_reserve_account_receiver` FOREIGN KEY (`reserve_account_receiver`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deduction_setup`
--

LOCK TABLES `deduction_setup` WRITE;
/*!40000 ALTER TABLE `deduction_setup` DISABLE KEYS */;
INSERT INTO `deduction_setup` VALUES (1,9,NULL,'TRL','Truck Lease','Truck','','3224','',0,1,1,2,0,300.0000,1,5,NULL,NULL,NULL),(2,9,NULL,'FUL','Fuel Cards','Fuel','','423423','FuelCode',1,0,1,2,0,25.0000,0,5,NULL,NULL,NULL),(3,9,NULL,'MNT','Truck Мaintenance','Мaintenance','','65786798','',2,1,1,2,12345,50.0000,0,5,NULL,NULL,NULL),(4,15,16,'','uniforms','DS1','','','',NULL,0,2,2,3,150.0000,0,NULL,NULL,NULL,NULL),(5,19,NULL,'','health insurance','DS2','','','',NULL,1,1,2,0,300.0000,0,8,NULL,NULL,NULL),(6,20,NULL,'','phone service','DS3','','','',NULL,1,1,2,3,100.0000,0,9,NULL,NULL,NULL);
/*!40000 ALTER TABLE `deduction_setup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deductions`
--

DROP TABLE IF EXISTS `deductions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deductions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `setup_id` int(10) unsigned NOT NULL,
  `category` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `invoice_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `invoice_due_date` date DEFAULT NULL,
  `department` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `gl_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `disbursement_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `rate` decimal(10,4) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `amount` decimal(10,4) DEFAULT NULL,
  `disbursement_date` date DEFAULT NULL,
  `balance` decimal(10,4) DEFAULT NULL,
  `adjusted_balance` decimal(10,4) DEFAULT NULL,
  `approved_datetime` datetime DEFAULT NULL,
  `approved_by` int(10) unsigned DEFAULT NULL,
  `created_datetime` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `source_id` int(10) unsigned DEFAULT NULL,
  `status` int(10) unsigned NOT NULL,
  `settlement_cycle_id` int(10) unsigned DEFAULT NULL,
  `contractor_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_deductions_setup_id` (`setup_id`),
  KEY `fk_deductions_approved_by` (`approved_by`),
  KEY `fk_deductions_created_by` (`created_by`),
  KEY `fk_deductions_source_id` (`source_id`),
  KEY `fk_deductions_status` (`status`),
  KEY `fk_deductions_settlement_cycle_id` (`settlement_cycle_id`),
  KEY `fk_deductions_contractor_entity_id` (`contractor_id`),
  CONSTRAINT `fk_deductions_contractor_entity_id` FOREIGN KEY (`contractor_id`) REFERENCES `contractor` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deductions_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deductions_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deductions_settlement_cycle_id` FOREIGN KEY (`settlement_cycle_id`) REFERENCES `settlement_cycle` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deductions_setup_id` FOREIGN KEY (`setup_id`) REFERENCES `deduction_setup` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deductions_source_id` FOREIGN KEY (`source_id`) REFERENCES `file_storage` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deductions_status` FOREIGN KEY (`status`) REFERENCES `payment_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deductions`
--

LOCK TABLES `deductions` WRITE;
/*!40000 ALTER TABLE `deductions` DISABLE KEYS */;
INSERT INTO `deductions` VALUES (1,1,'Delivery','Delivery - Standard',0,'','2012-06-21','2012-06-21','','423423','',75.0000,1,75.0000,'2012-06-21',75.0000,0.0000,NULL,NULL,'2012-06-21 23:56:17',6,1,2,1,2),(2,1,'Delivery','Delivery - Standard',1,'','2012-06-21','2012-06-21','','423423','',75.0000,1,75.0000,'2012-06-21',75.0000,0.0000,NULL,NULL,'2012-06-21 23:56:17',6,1,2,1,3),(3,2,'Mileage','Mileage - Standard',2,'','2012-06-21','2012-06-21','','4234','',0.9870,15,15.0000,'2012-06-21',15.0000,0.0000,NULL,NULL,'2012-06-21 23:56:56',6,1,2,1,3),(4,2,'Mileage','Mileage - Standard',3,'','2012-06-21','2012-06-21','','4234','',1.2000,60,72.0000,'2012-06-21',72.0000,0.0000,NULL,NULL,'2012-06-21 23:57:16',6,1,2,1,3),(5,2,'Mileage','Mileage - Standard',4,'','2012-06-21','2012-06-21','','4234','',0.9870,45,44.0000,'2012-06-21',44.0000,0.0000,NULL,NULL,'2012-06-21 23:57:36',6,1,2,1,3),(6,3,'Bonus','Bonus - Standard',5,'','2012-06-21','2046-04-09','','67589','',100.0000,1,100.0000,'2012-06-21',100.0000,0.0000,NULL,NULL,'2012-06-21 23:58:03',6,1,2,1,2),(7,3,'Bonus','Bonus - Standard',6,'','2012-06-21','2046-04-09','','67589','',100.0000,1,100.0000,'2012-06-21',100.0000,0.0000,NULL,NULL,'2012-06-21 23:58:03',6,1,2,1,3),(8,3,'Bonus','Bonus - Standard',7,'','2012-06-21','2046-04-09','','67589','',100.0000,1,100.0000,'2012-06-21',100.0000,0.0000,NULL,NULL,'2012-06-21 23:58:38',6,1,2,1,2),(9,3,'Bonus','Bonus - Standard',8,'','2012-06-21','2046-04-09','','67589','',100.0000,1,100.0000,'2012-06-21',100.0000,0.0000,NULL,NULL,'2012-06-21 23:58:38',6,1,2,1,3),(10,1,'Delivery','Delivery - Standard',9,'','2012-06-21','2012-06-21','','423423','',75.0000,3,225.0000,'2012-06-21',225.0000,0.0000,NULL,NULL,'2012-06-21 23:59:05',6,1,2,1,2),(11,1,'Delivery','Delivery - Standard',10,'','2012-06-21','2012-06-21','','423423','',75.0000,3,225.0000,'2012-06-21',225.0000,0.0000,NULL,NULL,'2012-06-21 23:59:05',6,1,2,1,3),(12,2,'Mileage','Mileage - Standard',11,'','2012-06-21','2012-06-21','','4234','',0.9870,60,59.0000,'2012-06-21',59.0000,0.0000,NULL,NULL,'2012-06-21 23:59:25',6,1,2,1,3),(13,2,'Mileage','Mileage - Standard',12,'','2012-06-21','2012-06-21','','4234','',0.9870,120,118.0000,'2012-06-21',118.0000,0.0000,NULL,NULL,'2012-06-21 23:59:42',6,1,2,1,3),(14,2,'Mileage','Mileage - Standard',13,'','2012-06-21','2012-06-21','','4234','',0.9870,20,20.0000,'2012-06-21',20.0000,0.0000,NULL,NULL,'2012-06-21 23:59:58',6,1,2,1,3),(15,1,'Delivery','Delivery - Standard',14,'','2012-06-22','2012-06-22','','423423','',75.0000,1,75.0000,'2012-06-22',75.0000,0.0000,NULL,NULL,'2012-06-22 00:00:15',6,1,2,1,2),(16,4,'DS1','uniforms',NULL,'','2012-07-13','2012-07-16','','','',150.0000,2,300.0000,'2012-07-22',300.0000,150.0000,'2012-08-03 16:57:15',3,'2012-07-13 09:13:34',6,NULL,3,2,16),(17,5,'DS2','health insurance',NULL,'','2012-07-13','2012-07-13','','','',300.0000,5,1500.0000,'2012-07-22',1500.0000,0.0000,'2012-08-03 16:57:15',3,'2012-07-13 09:14:47',6,NULL,3,2,16),(18,5,'DS2','health insurance',NULL,'','2012-07-13','2012-07-13','','','',300.0000,1,300.0000,'2012-07-22',300.0000,0.0000,'2012-08-03 16:57:15',3,'2012-07-13 09:14:47',6,NULL,3,2,17),(19,5,'DS2','health insurance',NULL,'','2012-07-13','2012-07-13','','','',300.0000,1,300.0000,'2012-07-22',300.0000,0.0000,'2012-08-03 16:57:15',3,'2012-07-13 09:14:47',6,NULL,3,2,18),(20,6,'DS3','phone service',NULL,'','2012-07-13','2012-07-16','','','',100.0000,2,200.0000,'2012-07-22',200.0000,0.0000,'2012-08-03 16:57:15',3,'2012-07-13 09:16:45',6,NULL,3,2,16),(21,6,'DS3','phone service',NULL,'','2012-07-13','2012-07-16','','','',100.0000,1,100.0000,'2012-07-22',100.0000,0.0000,'2012-08-03 16:57:15',3,'2012-07-13 09:16:45',6,NULL,3,2,17),(22,6,'DS3','phone service',NULL,'','2012-07-13','2012-07-16','','','',100.0000,2,200.0000,'2012-07-22',200.0000,0.0000,'2012-08-03 16:57:15',3,'2012-07-13 09:16:45',6,NULL,3,2,18);
/*!40000 ALTER TABLE `deductions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deductions_temp`
--

DROP TABLE IF EXISTS `deductions_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deductions_temp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `invoice` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `department` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `gl_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `rate` decimal(10,4) DEFAULT NULL,
  `priority` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `source_id` int(10) unsigned DEFAULT NULL,
  `contractor_id` int(10) unsigned NOT NULL,
  `vendor_deduction` int(10) unsigned DEFAULT NULL,
  `contract` int(10) unsigned DEFAULT NULL,
  `deduction_code` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `code` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `setup_id` int(10) DEFAULT NULL,
  `status_id` int(10) unsigned NOT NULL,
  `error` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_temp_status_id` (`status_id`),
  CONSTRAINT `payment_temp_status_id0` FOREIGN KEY (`status_id`) REFERENCES `payment_temp_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deductions_temp`
--

LOCK TABLES `deductions_temp` WRITE;
/*!40000 ALTER TABLE `deductions_temp` DISABLE KEYS */;
/*!40000 ALTER TABLE `deductions_temp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `disbursement_check`
--

DROP TABLE IF EXISTS `disbursement_check`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disbursement_check` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `disburstment_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_disburstment_check_disburstment_id` (`disburstment_id`),
  CONSTRAINT `fk_disburstment_check_disburstment_id` FOREIGN KEY (`disburstment_id`) REFERENCES `disbursement_transaction` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disbursement_check`
--

LOCK TABLES `disbursement_check` WRITE;
/*!40000 ALTER TABLE `disbursement_check` DISABLE KEYS */;
/*!40000 ALTER TABLE `disbursement_check` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `disbursement_transaction`
--

DROP TABLE IF EXISTS `disbursement_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disbursement_transaction` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bank_account_history_id` int(10) unsigned NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `process_type` int(10) unsigned DEFAULT NULL,
  `code` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `disbursement_code` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `amount` decimal(10,4) DEFAULT NULL,
  `status` int(11) unsigned DEFAULT NULL,
  `settlement_cycle_close_date` date DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `approved_by` int(10) unsigned DEFAULT NULL,
  `approved_datetime` datetime DEFAULT NULL,
  `settlement_cycle_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_disbursement_created_by` (`created_by`),
  KEY `fk_disbursement_approved_by` (`approved_by`),
  KEY `fk_disbursement_status` (`status`),
  KEY `fk_disbursement_entity_id` (`entity_id`),
  KEY `fk_disbursement_process_type` (`process_type`),
  KEY `fk_disbursement_bank_account_history_id` (`bank_account_history_id`),
  KEY `fk_disburstment_settlement_cycle_id` (`settlement_cycle_id`),
  CONSTRAINT `fk_disbursement_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disbursement_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disbursement_status` FOREIGN KEY (`status`) REFERENCES `payment_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disburstment_settlement_cycle_id` FOREIGN KEY (`settlement_cycle_id`) REFERENCES `settlement_cycle` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disbursement_bank_account_history_id` FOREIGN KEY (`bank_account_history_id`) REFERENCES `bank_account_history` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disbursement_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disbursement_process_type` FOREIGN KEY (`process_type`) REFERENCES `disbursement_transaction_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disbursement_transaction`
--

LOCK TABLES `disbursement_transaction` WRITE;
/*!40000 ALTER TABLE `disbursement_transaction` DISABLE KEYS */;
INSERT INTO `disbursement_transaction` VALUES (1,2,16,1,NULL,NULL,NULL,1150.0000,1,NULL,'2012-08-03 16:57:32',3,NULL,NULL,2),(2,3,17,1,NULL,NULL,NULL,1360.0000,1,NULL,'2012-08-03 16:57:32',3,NULL,NULL,2),(3,5,15,2,NULL,NULL,NULL,300.0000,1,NULL,'2012-08-03 16:57:33',3,NULL,NULL,2),(4,6,19,2,NULL,NULL,NULL,1050.0000,1,NULL,'2012-08-03 16:57:33',3,NULL,NULL,2),(5,7,19,2,NULL,NULL,NULL,1050.0000,1,NULL,'2012-08-03 16:57:33',3,NULL,NULL,2),(6,8,20,2,NULL,NULL,NULL,250.0000,1,NULL,'2012-08-03 16:57:34',3,NULL,NULL,2),(7,9,20,2,NULL,NULL,NULL,250.0000,1,NULL,'2012-08-03 16:57:34',3,NULL,NULL,2);
/*!40000 ALTER TABLE `disbursement_transaction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `disbursement_transaction_type`
--

DROP TABLE IF EXISTS `disbursement_transaction_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disbursement_transaction_type` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disbursement_transaction_type`
--

LOCK TABLES `disbursement_transaction_type` WRITE;
/*!40000 ALTER TABLE `disbursement_transaction_type` DISABLE KEYS */;
INSERT INTO `disbursement_transaction_type` VALUES (1,'Payment'),(2,'Deduction');
/*!40000 ALTER TABLE `disbursement_transaction_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entity`
--

DROP TABLE IF EXISTS `entity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_entity_entity_type_id` (`entity_type_id`),
  KEY `fk_entity_user_id` (`user_id`),
  CONSTRAINT `fk_entity_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_entity_entity_type_id` FOREIGN KEY (`entity_type_id`) REFERENCES `entity_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entity`
--

LOCK TABLES `entity` WRITE;
/*!40000 ALTER TABLE `entity` DISABLE KEYS */;
INSERT INTO `entity` VALUES (1,1,3),(2,2,3),(3,2,3),(4,2,3),(5,2,3),(6,2,3),(7,2,3),(8,2,3),(9,3,3),(10,3,3),(12,1,7),(13,2,8),(14,3,9),(15,1,10),(16,2,11),(17,2,12),(18,2,13),(19,3,14),(20,3,15);
/*!40000 ALTER TABLE `entity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entity_contact_info`
--

DROP TABLE IF EXISTS `entity_contact_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entity_contact_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contact_type` int(10) unsigned NOT NULL,
  `value` varchar(255) COLLATE utf8_bin NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_user_contact_info_contact_type` (`contact_type`),
  KEY `fk_entity_contact_info_entity_id` (`entity_id`),
  CONSTRAINT `fk_user_contact_info_contact_type` FOREIGN KEY (`contact_type`) REFERENCES `entity_contact_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_entity_contact_info_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entity_contact_info`
--

LOCK TABLES `entity_contact_info` WRITE;
/*!40000 ALTER TABLE `entity_contact_info` DISABLE KEYS */;
INSERT INTO `entity_contact_info` VALUES (1,7,'+1 888 253 5696',1),(2,1,'14 Fatin str. #170',2),(3,7,'+375 29 1643762',3),(4,1,'56 Main str. #560',4),(5,1,'56 Main str. #559',5),(6,1,'56 Main str. #545',7),(7,1,'56 Main str. #540',8),(8,1,'fatin 3-38',16),(9,1,'fatin 2-139',16),(10,2,'Mogilev',16),(11,4,'212038',16),(12,3,'Mgl',16);
/*!40000 ALTER TABLE `entity_contact_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entity_contact_type`
--

DROP TABLE IF EXISTS `entity_contact_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entity_contact_type` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entity_contact_type`
--

LOCK TABLES `entity_contact_type` WRITE;
/*!40000 ALTER TABLE `entity_contact_type` DISABLE KEYS */;
INSERT INTO `entity_contact_type` VALUES (1,'Address'),(2,'City'),(3,'State'),(4,'Zip'),(5,'Home Phone'),(6,'Office Phone'),(7,'Mobile Phone'),(8,'Email'),(9,'Fax');
/*!40000 ALTER TABLE `entity_contact_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entity_type`
--

DROP TABLE IF EXISTS `entity_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entity_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entity_type`
--

LOCK TABLES `entity_type` WRITE;
/*!40000 ALTER TABLE `entity_type` DISABLE KEYS */;
INSERT INTO `entity_type` VALUES (1,'carrier'),(2,'contractor'),(3,'vendor');
/*!40000 ALTER TABLE `entity_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `file_storage`
--

DROP TABLE IF EXISTS `file_storage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `file_storage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `source_link` text COLLATE utf8_bin NOT NULL,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `file_type` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_file_storage_file_type` (`file_type`),
  CONSTRAINT `fk_file_storage_file_type` FOREIGN KEY (`file_type`) REFERENCES `file_storage_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `file_storage`
--

LOCK TABLES `file_storage` WRITE;
/*!40000 ALTER TABLE `file_storage` DISABLE KEYS */;
INSERT INTO `file_storage` VALUES (1,'dsdas','Some title',3,0),(2,'1342535801_payments-import-file.xls','TestXlsFile',6,1);
/*!40000 ALTER TABLE `file_storage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `file_storage_type`
--

DROP TABLE IF EXISTS `file_storage_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `file_storage_type` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `file_storage_type`
--

LOCK TABLES `file_storage_type` WRITE;
/*!40000 ALTER TABLE `file_storage_type` DISABLE KEYS */;
INSERT INTO `file_storage_type` VALUES (1,'Payments'),(2,'Deductions');
/*!40000 ALTER TABLE `file_storage_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_setup`
--

DROP TABLE IF EXISTS `payment_setup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_setup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(10) unsigned NOT NULL,
  `contractor_id` int(10) unsigned DEFAULT NULL,
  `payment_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `carrier_payment_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `category` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `terms` int(11) DEFAULT NULL,
  `department` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `gl_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `disbursement_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `recurring` int(11) DEFAULT NULL,
  `level_id` int(10) unsigned NOT NULL,
  `billing_cycle_id` int(10) unsigned NOT NULL,
  `rate` decimal(10,4) DEFAULT NULL,
  `first_start_day` int(10) DEFAULT NULL,
  `second_start_day` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_payment_setup_level_id` (`level_id`),
  KEY `fk_payment_setup_billing_cycle_id` (`billing_cycle_id`),
  KEY `fk_payment_setup_carrier_id` (`carrier_id`),
  KEY `fk_payment_setup_contractor_id` (`contractor_id`),
  CONSTRAINT `fk_payment_setup_billing_cycle_id` FOREIGN KEY (`billing_cycle_id`) REFERENCES `cycle_period` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_setup_carrier_id` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_setup_contractor_id` FOREIGN KEY (`contractor_id`) REFERENCES `contractor` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_setup_level_id` FOREIGN KEY (`level_id`) REFERENCES `setup_level` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_setup`
--

LOCK TABLES `payment_setup` WRITE;
/*!40000 ALTER TABLE `payment_setup` DISABLE KEYS */;
INSERT INTO `payment_setup` VALUES (1,1,NULL,'Delivery','Delivery','Delivery - Standard','Delivery',0,'','423423','',1,1,2,75.0000,NULL,NULL),(2,1,2,'Mileage','Mileage Std','Mileage - Standard','Mileage',0,'','4234','',0,2,1,0.9870,NULL,NULL),(3,1,NULL,'Bonus','Bonus','Bonus - Standard','Bonus',1,'','67589','',1,1,3,100.0000,NULL,NULL),(4,1,NULL,'Waiting','Waiting','Waiting - Standard','Waiting',0,'','4564567','',1,1,3,20.0000,NULL,NULL),(5,15,NULL,'','','hourly','PS1',0,'','','',1,1,1,20.0000,NULL,NULL),(6,15,17,'','','overtime','PS2',0,'','','',0,2,2,30.0000,NULL,NULL),(7,15,18,'','','commission','PS3',14,'','','',0,2,2,300.0000,NULL,NULL);
/*!40000 ALTER TABLE `payment_setup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_status`
--

DROP TABLE IF EXISTS `payment_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_status` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_status`
--

LOCK TABLES `payment_status` WRITE;
/*!40000 ALTER TABLE `payment_status` DISABLE KEYS */;
INSERT INTO `payment_status` VALUES (1,'Verified'),(2,'Processed'),(3,'Approved');
/*!40000 ALTER TABLE `payment_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_temp_status`
--

DROP TABLE IF EXISTS `payment_temp_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_temp_status` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_temp_status`
--

LOCK TABLES `payment_temp_status` WRITE;
/*!40000 ALTER TABLE `payment_temp_status` DISABLE KEYS */;
INSERT INTO `payment_temp_status` VALUES (1,'Valid'),(2,'Not Valid');
/*!40000 ALTER TABLE `payment_temp_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_type`
--

DROP TABLE IF EXISTS `payment_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_type` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_type`
--

LOCK TABLES `payment_type` WRITE;
/*!40000 ALTER TABLE `payment_type` DISABLE KEYS */;
INSERT INTO `payment_type` VALUES (1,'Check'),(2,'ACH'),(3,'Debit Card');
/*!40000 ALTER TABLE `payment_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `setup_id` int(10) unsigned NOT NULL,
  `category` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `invoice` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `invoice_due_date` date DEFAULT NULL,
  `department` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `gl_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `rate` decimal(10,4) DEFAULT NULL,
  `amount` decimal(10,4) DEFAULT NULL,
  `check_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `disbursement_date` date DEFAULT NULL,
  `approved_datetime` datetime DEFAULT NULL,
  `approved_by` int(10) unsigned DEFAULT NULL,
  `created_datetime` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `source_id` int(10) unsigned DEFAULT NULL,
  `status` int(10) unsigned NOT NULL,
  `settlement_cycle_id` int(10) unsigned DEFAULT NULL,
  `contractor_id` int(10) unsigned NOT NULL,
  `balance` decimal(10,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_payments_setup_id` (`setup_id`),
  KEY `fk_payments_approved_by` (`approved_by`),
  KEY `fk_payments_created_by` (`created_by`),
  KEY `fk_payments_source_id` (`source_id`),
  KEY `fk_payments_status` (`status`),
  KEY `fk_payments_settlement_cycle_id` (`settlement_cycle_id`),
  KEY `fk_payments_contractor_entity_id` (`contractor_id`),
  CONSTRAINT `fk_payments_contractor_entity_id` FOREIGN KEY (`contractor_id`) REFERENCES `contractor` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payments_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payments_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payments_settlement_cycle_id` FOREIGN KEY (`settlement_cycle_id`) REFERENCES `settlement_cycle` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payments_setup_id` FOREIGN KEY (`setup_id`) REFERENCES `payment_setup` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payments_source_id` FOREIGN KEY (`source_id`) REFERENCES `file_storage` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payments_status` FOREIGN KEY (`status`) REFERENCES `payment_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,1,'Delivery','Delivery - Standard','','2012-06-21','2012-06-21','','423423',1,75.0000,75.0000,'','2012-06-21',NULL,NULL,'2012-06-21 23:23:10',6,1,2,1,2,75.0000),(2,1,'Delivery','Delivery - Standard','','2012-06-21','2012-06-21','','423423',1,75.0000,75.0000,'','2012-06-21',NULL,NULL,'2012-06-21 23:23:10',6,1,2,1,3,75.0000),(3,2,'Mileage','Mileage - Standard','','2012-06-21','2012-06-21','','4234',250,0.9870,246.7500,'','2012-06-21',NULL,NULL,'2012-06-21 23:24:23',6,1,2,1,3,246.7500),(4,2,'Mileage','Mileage - Standard','','2012-06-21','2012-06-21','','4234',400,0.9870,394.8000,'','2012-06-21',NULL,NULL,'2012-06-21 23:24:53',6,1,2,1,3,394.8000),(5,2,'Mileage','Mileage - Standard','','2012-06-21','2012-06-21','','4234',40,0.9870,39.4800,'','2012-06-21',NULL,NULL,'2012-06-21 23:25:35',6,1,2,1,3,39.4800),(6,2,'Mileage','Mileage - Standard','','2012-06-21','2012-06-21','','4234',25,1.0000,25.0000,'','2012-06-21',NULL,NULL,'2012-06-21 23:25:57',6,1,2,1,3,25.0000),(7,2,'Mileage','Mileage - Standard','','2012-06-21','2012-06-21','','4234',40,1.2000,48.0000,'','2012-06-21',NULL,NULL,'2012-06-21 23:26:50',6,1,2,1,3,48.0000),(8,4,'Waiting','Waiting - Standard','','2012-06-21','2012-06-21','','4564567',2,20.0000,40.0000,'','2012-06-21',NULL,NULL,'2012-06-21 23:28:45',6,1,2,1,2,40.0000),(9,4,'Waiting','Waiting - Standard','','2012-06-21','2012-06-21','','4564567',2,20.0000,40.0000,'','2012-06-21',NULL,NULL,'2012-06-21 23:28:45',6,1,2,1,3,40.0000),(10,3,'Bonus','Bonus - Standard','','2012-06-21','2012-06-22','','67589',1,100.0000,100.0000,'','2012-06-21',NULL,NULL,'2012-06-21 23:29:06',6,1,2,1,2,100.0000),(11,3,'Bonus','Bonus - Standard','','2012-06-21','2012-06-22','','67589',1,100.0000,100.0000,'','2012-06-21',NULL,NULL,'2012-06-21 23:29:06',6,1,2,1,3,100.0000),(12,2,'Mileage','Mileage - Standard','','2012-06-21','2012-06-21','','4234',23,0.9870,22.7010,'','2012-06-21',NULL,NULL,'2012-06-21 23:29:53',6,1,2,1,3,22.7010),(13,1,'Delivery','Delivery - Standard','','2012-06-21','2012-06-21','','423423',1,75.0000,75.0000,'','2012-06-21',NULL,NULL,'2012-06-21 23:30:11',6,1,2,1,2,75.0000),(14,1,'Delivery','Delivery - Standard','','2012-06-21','2012-06-21','','423423',1,75.0000,75.0000,'','2012-06-21',NULL,NULL,'2012-06-21 23:30:11',6,1,2,1,3,75.0000),(15,2,'Mileage','Mileage - Standard','','2012-06-21','2012-06-21','','4234',65,0.9870,64.1550,'','2012-06-21',NULL,NULL,'2012-06-21 23:30:38',6,1,2,1,3,64.1550),(16,5,'PS1','hourly','','2012-07-12','2012-07-12','','',160,20.0000,3200.0000,'','2012-07-22','2012-08-03 16:56:58',3,'2012-07-12 10:25:44',6,NULL,3,2,16,1200.0000),(17,5,'PS1','hourly','','2012-07-12','2012-07-12','','',80,20.0000,1600.0000,'','2012-07-22','2012-08-03 16:56:59',3,'2012-07-12 10:25:44',6,NULL,3,2,17,1200.0000),(18,5,'PS1','hourly','','2012-07-12','2012-07-12','','',1,20.0000,20.0000,'','2012-07-22','2012-08-03 16:56:59',3,'2012-07-12 10:25:44',6,NULL,3,2,18,20.0000),(19,6,'PS2','overtime','','2012-07-12','2012-07-12','','',17,30.0000,510.0000,'','2012-07-22','2012-08-03 16:56:59',3,'2012-07-12 14:35:30',6,NULL,3,2,17,510.0000),(20,7,'PS3','commission','','2012-07-12','2012-07-26','','',1,300.0000,300.0000,'','2012-07-22','2012-08-03 16:56:59',3,'2012-07-12 14:37:32',6,NULL,3,2,18,300.0000),(21,5,'PS1','hourly','','2012-07-12','2012-07-12','','',160,20.0000,3200.0000,'','2012-07-22',NULL,NULL,'2012-07-12 10:25:44',6,NULL,2,3,16,3200.0000);
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments_temp`
--

DROP TABLE IF EXISTS `payments_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments_temp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `invoice` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `department` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `gl_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `rate` decimal(10,4) DEFAULT NULL,
  `check_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `source_id` int(10) unsigned DEFAULT NULL,
  `contractor_id` int(10) unsigned NOT NULL,
  `carrier_payment` int(10) unsigned DEFAULT NULL,
  `contract` int(10) unsigned DEFAULT NULL,
  `payment_code` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `code` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `setup_id` int(10) DEFAULT NULL,
  `status_id` int(10) unsigned NOT NULL,
  `error` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_temp_status_id` (`status_id`),
  CONSTRAINT `payment_temp_status_id` FOREIGN KEY (`status_id`) REFERENCES `payment_temp_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments_temp`
--

LOCK TABLES `payments_temp` WRITE;
/*!40000 ALTER TABLE `payments_temp` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments_temp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reserve_account`
--

DROP TABLE IF EXISTS `reserve_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reserve_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity_id` int(10) unsigned NOT NULL,
  `bank_account_id` int(10) unsigned NOT NULL,
  `account_name` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `min_balance` decimal(10,4) DEFAULT NULL,
  `contribution_amount` decimal(10,4) DEFAULT NULL,
  `max_withdrawal_amount` decimal(10,4) DEFAULT NULL,
  `initial_balance` decimal(10,4) DEFAULT NULL,
  `current_balance` decimal(10,4) DEFAULT NULL,
  `disbursement_code` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reserve_account_entity_id` (`entity_id`),
  KEY `fk_reserve_account_bank_account_id` (`bank_account_id`),
  CONSTRAINT `fk_reserve_account_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_account_bank_account_id` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reserve_account`
--

LOCK TABLES `reserve_account` WRITE;
/*!40000 ALTER TABLE `reserve_account` DISABLE KEYS */;
INSERT INTO `reserve_account` VALUES (1,3,1,'Navibulgar reserve account','some description',1,1.0000,10.0000,10000.0000,500.0000,1000.0000,'1234567890'),(2,4,1,'John\'s Account','John\'s Description',2,1.0000,123.0000,345.0000,500.0000,2000.0000,'123456'),(3,8,2,'Gonazales account','blablabla description',0,200.0000,400.0000,600.0000,30.0000,60.0000,'code'),(4,5,2,'Best Acc','best description',3,10.0000,20.0000,30.0000,40.0000,50.0000,'666'),(5,9,2,'Penske account Name','Penske description',0,60.0000,40.0000,888.0000,999.0000,3000.0000,'my code'),(6,10,2,'Soso account','Soso some description',NULL,1.0000,5.0000,4.0000,2.0000,110.0000,'soso code'),(7,2,2,'Penske account Name','Penske description',NULL,60.0000,40.0000,888.0000,999.0000,3000.0000,'my code'),(8,19,3,'Ven1AmountAccount','VasilAccount',1,400.0000,200.0000,500.0000,0.0000,1000.0000,''),(9,20,5,'Ven2AmountAccount','ValeraAccount',0,300.0000,150.0000,300.0000,0.0000,500.0000,''),(10,16,7,'Ven1AmountAccount','VasilAccount',1,400.0000,200.0000,500.0000,1000.0000,370.0000,''),(11,16,7,'Ven2AmountAccount','ValeraAccount',0,300.0000,150.0000,300.0000,500.0000,400.0000,''),(12,17,8,'Ven1AmountAccount','VasilAccount',1,400.0000,200.0000,500.0000,1000.0000,400.0000,''),(13,17,8,'Ven2AmountAccount','ValeraAccount',2,300.0000,150.0000,300.0000,500.0000,190.0000,''),(14,18,9,'Ven1AmountAccount','VasilAccount',3,400.0000,200.0000,500.0000,1000.0000,500.0000,''),(15,18,9,'Ven2AmountAccount','ValeraAccount',0,300.0000,150.0000,300.0000,500.0000,120.0000,'');
/*!40000 ALTER TABLE `reserve_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reserve_account_carrier`
--

DROP TABLE IF EXISTS `reserve_account_carrier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reserve_account_carrier` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reserve_account_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reserve_account_carrier_reserve_account_id` (`reserve_account_id`),
  CONSTRAINT `fk_reserve_account_carrier_reserve_account_id` FOREIGN KEY (`reserve_account_id`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reserve_account_carrier`
--

LOCK TABLES `reserve_account_carrier` WRITE;
/*!40000 ALTER TABLE `reserve_account_carrier` DISABLE KEYS */;
/*!40000 ALTER TABLE `reserve_account_carrier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reserve_account_contractor`
--

DROP TABLE IF EXISTS `reserve_account_contractor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reserve_account_contractor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reserve_account_id` int(10) unsigned NOT NULL,
  `reserve_account_vendor_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reserve_account_contractor_reserve_account_id` (`reserve_account_id`),
  KEY `fk_reserve_account_contractor_reserve_account_vendor_id` (`reserve_account_vendor_id`),
  CONSTRAINT `fk_reserve_account_contractor_reserve_account_vendor_id` FOREIGN KEY (`reserve_account_vendor_id`) REFERENCES `reserve_account_vendor` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_account_contractor_reserve_account_id` FOREIGN KEY (`reserve_account_id`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reserve_account_contractor`
--

LOCK TABLES `reserve_account_contractor` WRITE;
/*!40000 ALTER TABLE `reserve_account_contractor` DISABLE KEYS */;
INSERT INTO `reserve_account_contractor` VALUES (1,1,1),(2,2,1),(3,3,2),(4,4,2),(5,7,1),(6,10,3),(7,11,4),(8,12,3),(9,13,4),(10,14,3),(11,15,4);
/*!40000 ALTER TABLE `reserve_account_contractor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reserve_account_vendor`
--

DROP TABLE IF EXISTS `reserve_account_vendor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reserve_account_vendor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reserve_account_id` int(10) unsigned NOT NULL,
  `vendor_reserve_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reserve_account_vendor_reserve_account_id` (`reserve_account_id`),
  CONSTRAINT `fk_reserve_account_vendor_reserve_account_id` FOREIGN KEY (`reserve_account_id`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reserve_account_vendor`
--

LOCK TABLES `reserve_account_vendor` WRITE;
/*!40000 ALTER TABLE `reserve_account_vendor` DISABLE KEYS */;
INSERT INTO `reserve_account_vendor` VALUES (1,5,'code'),(2,6,'123456'),(3,8,'V1RA'),(4,9,'V2RA');
/*!40000 ALTER TABLE `reserve_account_vendor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reserve_transaction`
--

DROP TABLE IF EXISTS `reserve_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reserve_transaction` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reserve_account_sender` int(10) unsigned NOT NULL,
  `reserve_account_receiver` int(10) unsigned NOT NULL,
  `vendor_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `type` int(10) unsigned NOT NULL,
  `deduction_id` int(10) unsigned DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `amount` decimal(10,4) DEFAULT NULL,
  `balance` decimal(10,4) DEFAULT NULL,
  `adjusted_balance` decimal(10,4) DEFAULT NULL,
  `settlement_cycle_id` int(10) unsigned NOT NULL,
  `approved_datetime` datetime DEFAULT NULL,
  `approved_by` int(10) unsigned DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `source_id` int(10) unsigned DEFAULT NULL,
  `disbursement_id` int(11) DEFAULT NULL,
  `status` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reserve_transaction_reserve_account_sender` (`reserve_account_sender`),
  KEY `fk_reserve_transaction_reserve_account_receiver` (`reserve_account_receiver`),
  KEY `fk_reserve_transaction_type` (`type`),
  KEY `fk_reserve_transaction_deduction_id` (`deduction_id`),
  KEY `fk_reserve_transaction_approved_by` (`approved_by`),
  KEY `fk_reserve_transaction_created_by` (`created_by`),
  KEY `fk_reserve_transaction_source_id` (`source_id`),
  KEY `fk_reserve_transaction_settlement_cycle_id` (`settlement_cycle_id`),
  KEY `fk_reserve_transaction_status` (`status`),
  CONSTRAINT `fk_reserve_transaction_status` FOREIGN KEY (`status`) REFERENCES `payment_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_deduction_id` FOREIGN KEY (`deduction_id`) REFERENCES `deductions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_reserve_account_receiver` FOREIGN KEY (`reserve_account_receiver`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_reserve_account_sender` FOREIGN KEY (`reserve_account_sender`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_settlement_cycle_id` FOREIGN KEY (`settlement_cycle_id`) REFERENCES `settlement_cycle` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_source_id` FOREIGN KEY (`source_id`) REFERENCES `file_storage` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_type` FOREIGN KEY (`type`) REFERENCES `reserve_transaction_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reserve_transaction`
--

LOCK TABLES `reserve_transaction` WRITE;
/*!40000 ALTER TABLE `reserve_transaction` DISABLE KEYS */;
INSERT INTO `reserve_transaction` VALUES (1,10,8,NULL,1,NULL,NULL,200.0000,NULL,NULL,2,'2012-08-03 16:57:25',3,'2012-08-03 16:56:42',3,NULL,NULL,3),(2,12,8,NULL,1,NULL,NULL,200.0000,NULL,NULL,2,'2012-08-03 16:57:25',3,'2012-08-03 16:56:43',3,NULL,NULL,3),(3,13,9,NULL,1,NULL,NULL,150.0000,NULL,NULL,2,'2012-08-03 16:57:25',3,'2012-08-03 16:56:43',3,NULL,NULL,3),(4,15,9,NULL,2,22,NULL,180.0000,180.0000,NULL,2,'2012-08-03 16:57:25',3,'2012-08-03 16:56:48',3,NULL,NULL,3);
/*!40000 ALTER TABLE `reserve_transaction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reserve_transaction_type`
--

DROP TABLE IF EXISTS `reserve_transaction_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reserve_transaction_type` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reserve_transaction_type`
--

LOCK TABLES `reserve_transaction_type` WRITE;
/*!40000 ALTER TABLE `reserve_transaction_type` DISABLE KEYS */;
INSERT INTO `reserve_transaction_type` VALUES (1,'Contribution'),(2,'Withdrawal'),(3,'Cash Advance');
/*!40000 ALTER TABLE `reserve_transaction_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settlement_cycle`
--

DROP TABLE IF EXISTS `settlement_cycle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settlement_cycle` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(10) unsigned NOT NULL,
  `cycle_period_id` int(10) unsigned NOT NULL,
  `payment_terms` int(11) NOT NULL,
  `disbursement_terms` int(11) NOT NULL,
  `cycle_start_date` date NOT NULL,
  `cycle_close_date` date NOT NULL,
  `status_id` int(10) unsigned NOT NULL,
  `first_start_day` int(11) DEFAULT NULL,
  `second_start_day` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_settlement_cycle_carrier_id` (`carrier_id`),
  KEY `fk_settlement_cycle_cycle_period_id` (`cycle_period_id`),
  KEY `fk_settlement_cycle_status_id` (`status_id`),
  CONSTRAINT `fk_settlement_cycle_carrier_id` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_settlement_cycle_cycle_period_id` FOREIGN KEY (`cycle_period_id`) REFERENCES `cycle_period` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_settlement_cycle_status_id` FOREIGN KEY (`status_id`) REFERENCES `settlement_cycle_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settlement_cycle`
--

LOCK TABLES `settlement_cycle` WRITE;
/*!40000 ALTER TABLE `settlement_cycle` DISABLE KEYS */;
INSERT INTO `settlement_cycle` VALUES (1,1,3,0,5,'2012-06-20','2012-07-20',1,NULL,NULL),(2,15,1,2,5,'2012-07-10','2012-07-17',5,NULL,NULL),(3,15,1,2,5,'2012-07-17','2012-07-24',1,NULL,NULL);
/*!40000 ALTER TABLE `settlement_cycle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settlement_cycle_status`
--

DROP TABLE IF EXISTS `settlement_cycle_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settlement_cycle_status` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settlement_cycle_status`
--

LOCK TABLES `settlement_cycle_status` WRITE;
/*!40000 ALTER TABLE `settlement_cycle_status` DISABLE KEYS */;
INSERT INTO `settlement_cycle_status` VALUES (1,'Not verified'),(2,'Verified'),(3,'Processing'),(4,'Approved'),(5,'Closed');
/*!40000 ALTER TABLE `settlement_cycle_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `setup_level`
--

DROP TABLE IF EXISTS `setup_level`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `setup_level` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `setup_level`
--

LOCK TABLES `setup_level` WRITE;
/*!40000 ALTER TABLE `setup_level` DISABLE KEYS */;
INSERT INTO `setup_level` VALUES (1,'Global'),(2,'Individual');
/*!40000 ALTER TABLE `setup_level` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_values`
--

DROP TABLE IF EXISTS `system_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_values` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  `value` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_values`
--

LOCK TABLES `system_values` WRITE;
/*!40000 ALTER TABLE `system_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_role` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role`
--

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
INSERT INTO `user_role` VALUES (1,'Super admin'),(2,'Carrier'),(3,'Contractor'),(4,'Vendor');
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_bin NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `last_login_ip` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `last_selected_carrier` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_users_role_id` (`role_id`),
  CONSTRAINT `fk_users_role_id` FOREIGN KEY (`role_id`) REFERENCES `user_role` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'danny@danny.com','danny','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1',NULL),(2,1,'danny@true.com','Danny','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1',NULL),(3,1,'dkozhemyako@tula.co','Daniel Kozhemyako','1a1dc91c907325c69271ddf0c944bc72','82.209.239.149',NULL),(4,1,'john@smith.com','John Smith','1a1dc91c907325c69271ddf0c944bc72','82.209.239.149',NULL),(5,1,'jake.zuanich@pfleet.com','Jake Zuanich','82e9dd1f989d339f09c629d0abd942d4','12.46.64.53',NULL),(6,1,'bivi@mail.by','bivi','05546b0e38ab9175cd905eebcc6ebb76','127.0.0.1',NULL),(7,2,'johndoe@example.com','John Doe','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1',NULL),(8,3,'contractor1@contractor1.com','contractor1','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1',NULL),(9,4,'vendor1@vendor1.com','vendor1','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1',NULL),(10,2,'car1@test.com','CAR1','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1',NULL),(11,3,'con1@test.com','CON1','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1',NULL),(12,3,'con2@test.com','CON2','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1',NULL),(13,3,'con3@test.com','CON3','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1',NULL),(14,4,'ven1@test.com','VEN1','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1',NULL),(15,4,'ven2@test.com','VEN2','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1',NULL),(16,1,'phpunittest@pfleet.loc','phpunittest','74cbea5364321be7a0e15e5b2ce1d14d','127.0.0.1',4);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_visibility`
--

DROP TABLE IF EXISTS `users_visibility`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_visibility` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity_id` int(10) unsigned NOT NULL,
  `participant_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_users_visibility_entity_id` (`entity_id`),
  KEY `fk_users_visibility_participant_id` (`participant_id`),
  CONSTRAINT `fk_users_visibility_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_visibility_participant_id` FOREIGN KEY (`participant_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_visibility`
--

LOCK TABLES `users_visibility` WRITE;
/*!40000 ALTER TABLE `users_visibility` DISABLE KEYS */;
INSERT INTO `users_visibility` VALUES (1,15,16),(2,15,17),(3,15,18),(4,15,19),(5,15,20),(6,19,15),(7,20,15),(8,12,2),(9,12,3),(10,12,4),(11,12,5),(12,12,6),(13,12,7);
/*!40000 ALTER TABLE `users_visibility` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vendor`
--

DROP TABLE IF EXISTS `vendor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity_id` int(10) unsigned NOT NULL,
  `tax_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `contact` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `terms` int(11) DEFAULT NULL,
  `resubmit` int(11) DEFAULT NULL,
  `recurring_deductions` int(11) DEFAULT NULL,
  `reserve_account` int(11) DEFAULT NULL,
  PRIMARY KEY (`entity_id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_vendor_entity_id` (`entity_id`),
  CONSTRAINT `fk_vendor_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendor`
--

LOCK TABLES `vendor` WRITE;
/*!40000 ALTER TABLE `vendor` DISABLE KEYS */;
INSERT INTO `vendor` VALUES (1,9,'451263897','Penske Truck Lease','Steve Ballmer',0,0,1,1),(2,10,'456397127','Soco Fuel Cards','Glenn Beck',7,0,0,0),(3,19,'568369207','Vasil Pypkin','VasilVen1Co',0,0,1,1),(4,20,'947893186','Valera Pypkin','ValerVen2Co',7,0,0,0);
/*!40000 ALTER TABLE `vendor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vendor_status`
--

DROP TABLE IF EXISTS `vendor_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendor_status` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendor_status`
--

LOCK TABLES `vendor_status` WRITE;
/*!40000 ALTER TABLE `vendor_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `vendor_status` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-08-03 16:57:47

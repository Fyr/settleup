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
-- Table structure for table `bank_account`
--

DROP TABLE IF EXISTS `bank_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bank_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` int(10) unsigned NOT NULL,
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
  KEY `fk_bank_account_owner_id` (`owner_id`),
  KEY `fk_bank_account_payment_type` (`payment_type`),
  CONSTRAINT `fk_bank_account_payment_type` FOREIGN KEY (`payment_type`) REFERENCES `payment_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bank_account_owner_id` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_account`
--

LOCK TABLES `bank_account` WRITE;
/*!40000 ALTER TABLE `bank_account` DISABLE KEYS */;
INSERT INTO `bank_account` VALUES (1,3,'Main Account',3,'1','1','1','1','My Main account','Bank of America','4111111111111111','Daniel Kozhemyako','14 Fatin str. #170','New York','New York','90001','2015-04-20',341,500.0000,3.0000),(2,3,'My Additional Account',3,'1','2','4','5','My additional account','Prior Bank','456123789654','Daniel Kozhemyako','14 Fatin str. #170','New York','New York','90001','2013-02-23',534,300.0000,5.0000);
/*!40000 ALTER TABLE `bank_account` ENABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_account_history`
--

LOCK TABLES `bank_account_history` WRITE;
/*!40000 ALTER TABLE `bank_account_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `bank_account_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carrier`
--

DROP TABLE IF EXISTS `carrier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carrier` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` int(10) unsigned NOT NULL,
  `tax_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `short_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `contact` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `settlement_cycle` int(10) unsigned NOT NULL,
  `settlement_day` int(11) DEFAULT NULL,
  `recurring_payments` int(11) DEFAULT NULL,
  `payment_terms` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_carrier_owner_id` (`owner_id`),
  KEY `fk_carrier_settlement_cycle` (`settlement_cycle`),
  CONSTRAINT `fk_carrier_owner_id` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_carrier_settlement_cycle` FOREIGN KEY (`settlement_cycle`) REFERENCES `cycle_period` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrier`
--

LOCK TABLES `carrier` WRITE;
/*!40000 ALTER TABLE `carrier` DISABLE KEYS */;
INSERT INTO `carrier` VALUES (4,3,'123951753','JAB','Jay Abraham','Jay Abraham',3,3,1,2),(5,3,'456951753','PAL','Paul Allen','Paul Allen',2,4,1,5),(6,3,'951423126','ROB','Robert Allen','Robert Allen',1,3,0,4);
/*!40000 ALTER TABLE `carrier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contractor`
--

DROP TABLE IF EXISTS `contractor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contractor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` int(10) unsigned NOT NULL,
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
  PRIMARY KEY (`id`),
  KEY `fk_contractor_owner_id` (`owner_id`),
  KEY `fk_contractor_status` (`status`),
  KEY `fk_contractor_correspondence_method` (`correspondence_method`),
  CONSTRAINT `fk_contractor_owner_id` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_contractor_status` FOREIGN KEY (`status`) REFERENCES `contractor_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_contractor_correspondence_method` FOREIGN KEY (`correspondence_method`) REFERENCES `user_contact_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contractor`
--

LOCK TABLES `contractor` WRITE;
/*!40000 ALTER TABLE `contractor` DISABLE KEYS */;
INSERT INTO `contractor` VALUES (1,3,'456159753','123956182','MSC Cyprus','Bernard','Arnault','California','1980-04-25','',1,'','','','2010-11-15','2010-12-31','2011-01-04',0,1),(2,3,'987654320','451263897','Navibulgar','David','Bach','Michigan','1970-07-25','',1,'','','','2007-04-03','2007-08-03','2007-12-03',0,1);
/*!40000 ALTER TABLE `contractor` ENABLE KEYS */;
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
INSERT INTO `contractor_status` VALUES (1,'Active'),(2,'Leave'),(3,'Terminated');
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
  `vendor_id` int(10) unsigned NOT NULL,
  `contractor_id` int(10) unsigned NOT NULL,
  `vendor_deduction_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `category` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `department` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `gl_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `disbursement_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `recurring` int(11) DEFAULT NULL,
  `level` int(10) unsigned NOT NULL,
  `billing_cycle` int(10) unsigned NOT NULL,
  `terms` int(11) DEFAULT NULL,
  `last_recurring_date` date DEFAULT NULL,
  `last_cycle_close_day` date DEFAULT NULL,
  `cycle_close_date` date DEFAULT NULL,
  `rate` decimal(10,4) DEFAULT NULL,
  `eligible` int(11) DEFAULT NULL,
  `reserve_account_sender` int(10) unsigned DEFAULT NULL,
  `reserve_account_receiver` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_deduction_setup_level` (`level`),
  KEY `fk_deduction_setup_billing_cycle` (`billing_cycle`),
  KEY `fk_deduction_setup_vendor_id` (`vendor_id`),
  KEY `fk_deduction_setup_contractor_id` (`contractor_id`),
  KEY `fk_deduction_setup_reserve_account_sender` (`reserve_account_sender`),
  KEY `fk_deduction_setup_reserve_account_receiver` (`reserve_account_receiver`),
  CONSTRAINT `fk_deduction_setup_contractor_id` FOREIGN KEY (`contractor_id`) REFERENCES `contractor` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deduction_setup_billing_cycle` FOREIGN KEY (`billing_cycle`) REFERENCES `cycle_period` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deduction_setup_level` FOREIGN KEY (`level`) REFERENCES `setup_level` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deduction_setup_vendor_id` FOREIGN KEY (`vendor_id`) REFERENCES `vendor` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deduction_setup_reserve_account_sender` FOREIGN KEY (`reserve_account_sender`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deduction_setup_reserve_account_receiver` FOREIGN KEY (`reserve_account_receiver`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deduction_setup`
--

LOCK TABLES `deduction_setup` WRITE;
/*!40000 ALTER TABLE `deduction_setup` DISABLE KEYS */;
INSERT INTO `deduction_setup` VALUES (3,2,1,'VDC','Description','Some Category','Some Dep','3224','FuelCode',1,1,1,2,0,'0000-00-00','0000-00-00','0000-00-00',50.0000,1,NULL,NULL),(4,2,1,'VDCINSURANCE','Description','Insurance','Broker Dept','423423','',0,0,1,1,1,'0000-00-00','0000-00-00','0000-00-00',25.0000,0,NULL,NULL);
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
  `amount` int(11) DEFAULT NULL,
  `disbursement_date` date DEFAULT NULL,
  `cycle_close_date` date DEFAULT NULL,
  `balance` decimal(10,4) DEFAULT NULL,
  `adjusted_balance` decimal(10,4) DEFAULT NULL,
  `adjusted_balance_use` decimal(10,4) DEFAULT NULL,
  `reserve_account_contractor` int(11) DEFAULT NULL,
  `eligible` int(11) DEFAULT NULL,
  `approved_datetime` datetime DEFAULT NULL,
  `approved_by` int(10) unsigned NOT NULL,
  `created_datetime` datetime DEFAULT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `source_id` int(10) unsigned NOT NULL,
  `status` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_deductions_setup_id` (`setup_id`),
  KEY `fk_deductions_approved_by` (`approved_by`),
  KEY `fk_deductions_created_by` (`created_by`),
  KEY `fk_deductions_source_id` (`source_id`),
  KEY `fk_deductions_status` (`status`),
  CONSTRAINT `fk_deductions_status` FOREIGN KEY (`status`) REFERENCES `payment_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deductions_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deductions_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deductions_setup_id` FOREIGN KEY (`setup_id`) REFERENCES `deduction_setup` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deductions_source_id` FOREIGN KEY (`source_id`) REFERENCES `file_storage` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deductions`
--

LOCK TABLES `deductions` WRITE;
/*!40000 ALTER TABLE `deductions` DISABLE KEYS */;
INSERT INTO `deductions` VALUES (1,3,'Some Category','Description',1,'','0000-00-00','0000-00-00','','myGLCode','',0.0000,0,0,'0000-00-00','0000-00-00',500.0000,0.0000,0.0000,0,0,'0000-00-00 00:00:00',3,'0000-00-00 00:00:00',3,1,3);
/*!40000 ALTER TABLE `deductions` ENABLE KEYS */;
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
  `owner_id` int(10) unsigned NOT NULL,
  `source_process_code` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `code` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `disbursement_code` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `amount` decimal(10,4) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `settlement_cycle_end_date` date DEFAULT NULL,
  `disbursement_date` date DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL,
  `submission_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_disbursement_transaction_bank_account_history_id` (`bank_account_history_id`),
  KEY `fk_disbursement_transaction_owner_id` (`owner_id`),
  CONSTRAINT `fk_disbursement_transaction_bank_account_history_id` FOREIGN KEY (`bank_account_history_id`) REFERENCES `bank_account_history` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disbursement_transaction_owner_id` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disbursement_transaction`
--

LOCK TABLES `disbursement_transaction` WRITE;
/*!40000 ALTER TABLE `disbursement_transaction` DISABLE KEYS */;
/*!40000 ALTER TABLE `disbursement_transaction` ENABLE KEYS */;
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `file_storage`
--

LOCK TABLES `file_storage` WRITE;
/*!40000 ALTER TABLE `file_storage` DISABLE KEYS */;
INSERT INTO `file_storage` VALUES (1,'dsdas','Some title',3);
/*!40000 ALTER TABLE `file_storage` ENABLE KEYS */;
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
  `contractor_id` int(10) unsigned NOT NULL,
  `payment_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `carrier_payment_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `category` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `terms` int(11) DEFAULT NULL,
  `department` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `gl_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `disbursement_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `recurring` int(11) DEFAULT NULL,
  `level` int(10) unsigned NOT NULL,
  `billing_cycle` int(10) unsigned NOT NULL,
  `last_recurring_date` date DEFAULT NULL,
  `cycle_close_date` date DEFAULT NULL,
  `rate` decimal(10,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_payment_setup_level` (`level`),
  KEY `fk_payment_setup_billing_cycle` (`billing_cycle`),
  KEY `fk_payment_setup_carrier_id` (`carrier_id`),
  KEY `fk_payment_setup_contractor_id` (`contractor_id`),
  CONSTRAINT `fk_payment_setup_contractor_id` FOREIGN KEY (`contractor_id`) REFERENCES `contractor` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_setup_billing_cycle` FOREIGN KEY (`billing_cycle`) REFERENCES `cycle_period` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_setup_carrier_id` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_setup_level` FOREIGN KEY (`level`) REFERENCES `setup_level` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_setup`
--

LOCK TABLES `payment_setup` WRITE;
/*!40000 ALTER TABLE `payment_setup` DISABLE KEYS */;
INSERT INTO `payment_setup` VALUES (1,4,1,'simpleCode','carrierSimpleCode','My Description','Salary',2,'Some Dep','423423','salaryCode',1,1,1,'2012-04-25','2012-05-01',500.0000),(2,4,1,'simpleCode2','carrierSimpleCode2','Description','Fuel',3,'Broker Dept','4234','234',0,2,1,'2012-05-05','2012-05-31',30.0000);
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
  `cycle_close_date` date DEFAULT NULL,
  `approved_datetime` datetime DEFAULT NULL,
  `approved_by` int(10) unsigned NOT NULL,
  `created_datetime` datetime DEFAULT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `source_id` int(10) unsigned NOT NULL,
  `status` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_payments_setup_id` (`setup_id`),
  KEY `fk_payments_approved_by` (`approved_by`),
  KEY `fk_payments_created_by` (`created_by`),
  KEY `fk_payments_source_id` (`source_id`),
  KEY `fk_payments_status` (`status`),
  CONSTRAINT `fk_payments_status` FOREIGN KEY (`status`) REFERENCES `payment_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payments_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payments_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payments_setup_id` FOREIGN KEY (`setup_id`) REFERENCES `payment_setup` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payments_source_id` FOREIGN KEY (`source_id`) REFERENCES `file_storage` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (6,1,'Salary','Description','','0000-00-00','0000-00-00','Some Dep','',1,500.0000,500.0000,'1564561616','2012-03-17','2012-04-25','0000-00-00 00:00:00',3,'0000-00-00 00:00:00',3,1,3),(7,2,'Fuel','Description','','0000-00-00','0000-00-00','Broker Dept','',45,30.0000,1350.0000,'terterte','0000-00-00','0000-00-00','0000-00-00 00:00:00',3,'0000-00-00 00:00:00',3,1,3);
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reserve_account`
--

DROP TABLE IF EXISTS `reserve_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reserve_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) unsigned NOT NULL,
  `owner_id` int(10) unsigned NOT NULL,
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
  KEY `fk_reserve_account_type_id` (`type_id`),
  KEY `fk_reserve_account_owner_id` (`owner_id`),
  KEY `fk_reserve_account_bank_account_id` (`bank_account_id`),
  CONSTRAINT `fk_reserve_account_type_id` FOREIGN KEY (`type_id`) REFERENCES `reserve_account_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_account_owner_id` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_account_bank_account_id` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reserve_account`
--

LOCK TABLES `reserve_account` WRITE;
/*!40000 ALTER TABLE `reserve_account` DISABLE KEYS */;
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
  `carrier_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reserve_account_contractor_contractor_id` (`carrier_id`),
  KEY `fk_reserve_account_carrier_reserve_account_id` (`reserve_account_id`),
  CONSTRAINT `fk_reserve_account_carrier_reserve_account_id` FOREIGN KEY (`reserve_account_id`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_account_carrier_carrier_id` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
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
  `contractor_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reserve_account_contractor_contractor_id` (`contractor_id`),
  KEY `fk_reserve_account_contractor_reserve_account_id` (`reserve_account_id`),
  CONSTRAINT `fk_reserve_account_contractor_reserve_account_id` FOREIGN KEY (`reserve_account_id`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_account_contractor_contractor_id` FOREIGN KEY (`contractor_id`) REFERENCES `contractor` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reserve_account_contractor`
--

LOCK TABLES `reserve_account_contractor` WRITE;
/*!40000 ALTER TABLE `reserve_account_contractor` DISABLE KEYS */;
/*!40000 ALTER TABLE `reserve_account_contractor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reserve_account_type`
--

DROP TABLE IF EXISTS `reserve_account_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reserve_account_type` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reserve_account_type`
--

LOCK TABLES `reserve_account_type` WRITE;
/*!40000 ALTER TABLE `reserve_account_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `reserve_account_type` ENABLE KEYS */;
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
  `vendor_id` int(10) unsigned NOT NULL,
  `vendor_reserve_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reserve_account_vendor_vendor_id` (`vendor_id`),
  KEY `fk_reserve_account_vendor_reserve_account_id` (`reserve_account_id`),
  CONSTRAINT `fk_reserve_account_vendor_reserve_account_id` FOREIGN KEY (`reserve_account_id`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_account_vendor_vendor_id` FOREIGN KEY (`vendor_id`) REFERENCES `vendor` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reserve_account_vendor`
--

LOCK TABLES `reserve_account_vendor` WRITE;
/*!40000 ALTER TABLE `reserve_account_vendor` DISABLE KEYS */;
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
  `deduction_id` int(10) unsigned NOT NULL,
  `priority` int(11) DEFAULT NULL,
  `amount` decimal(10,4) DEFAULT NULL,
  `balance` decimal(10,4) DEFAULT NULL,
  `adjusted_balance` decimal(10,4) DEFAULT NULL,
  `adjusted_balance_use` decimal(10,4) DEFAULT NULL,
  `settlement_cycle_date` date DEFAULT NULL,
  `approved_datetime` datetime DEFAULT NULL,
  `approved_by` int(10) unsigned NOT NULL,
  `created_datetime` datetime DEFAULT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `source_id` int(10) unsigned NOT NULL,
  `disbursement_id` int(11) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reserve_transaction_reserve_account_sender` (`reserve_account_sender`),
  KEY `fk_reserve_transaction_reserve_account_receiver` (`reserve_account_receiver`),
  KEY `fk_reserve_transaction_type` (`type`),
  KEY `fk_reserve_transaction_deduction_id` (`deduction_id`),
  KEY `fk_reserve_transaction_approved_by` (`approved_by`),
  KEY `fk_reserve_transaction_created_by` (`created_by`),
  KEY `fk_reserve_transaction_source_id` (`source_id`),
  CONSTRAINT `fk_reserve_transaction_source_id` FOREIGN KEY (`source_id`) REFERENCES `file_storage` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_deduction_id` FOREIGN KEY (`deduction_id`) REFERENCES `deductions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_reserve_account_sender` FOREIGN KEY (`reserve_account_sender`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_reserve_account_receiver` FOREIGN KEY (`reserve_account_receiver`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_type` FOREIGN KEY (`type`) REFERENCES `reserve_transaction_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reserve_transaction`
--

LOCK TABLES `reserve_transaction` WRITE;
/*!40000 ALTER TABLE `reserve_transaction` DISABLE KEYS */;
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
-- Table structure for table `user_contact_info`
--

DROP TABLE IF EXISTS `user_contact_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_contact_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `contact_type` int(10) unsigned NOT NULL,
  `value` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_contact_info_user_id` (`user_id`),
  KEY `fk_user_contact_info_contact_type` (`contact_type`),
  CONSTRAINT `fk_user_contact_info_contact_type` FOREIGN KEY (`contact_type`) REFERENCES `user_contact_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_contact_info_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_contact_info`
--

LOCK TABLES `user_contact_info` WRITE;
/*!40000 ALTER TABLE `user_contact_info` DISABLE KEYS */;
INSERT INTO `user_contact_info` VALUES (8,1,1,'dfdsfsdf'),(12,3,7,'+1 888 253 5696');
/*!40000 ALTER TABLE `user_contact_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_contact_type`
--

DROP TABLE IF EXISTS `user_contact_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_contact_type` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_contact_type`
--

LOCK TABLES `user_contact_type` WRITE;
/*!40000 ALTER TABLE `user_contact_type` DISABLE KEYS */;
INSERT INTO `user_contact_type` VALUES (1,'Address'),(2,'City'),(3,'State'),(4,'Zip'),(5,'Home Phone'),(6,'Office Phone'),(7,'Mobile Phone'),(8,'Email'),(9,'Fax');
/*!40000 ALTER TABLE `user_contact_type` ENABLE KEYS */;
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
  PRIMARY KEY (`id`),
  KEY `fk_users_role_id` (`role_id`),
  CONSTRAINT `fk_users_role_id` FOREIGN KEY (`role_id`) REFERENCES `user_role` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,NULL,'danny@danny.com','danny','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1'),(2,NULL,'danny@true.com','Danny','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1'),(3,NULL,'dkozhemyako@tula.co','Daniel Kozhemyako','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vendor`
--

DROP TABLE IF EXISTS `vendor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` int(10) unsigned NOT NULL,
  `tax_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `contact` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `terms` int(11) DEFAULT NULL,
  `priority_level` int(11) DEFAULT NULL,
  `resubmit` int(11) DEFAULT NULL,
  `recurring_deductions` int(11) DEFAULT NULL,
  `reserve_account` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_vendor_owner_id` (`owner_id`),
  CONSTRAINT `fk_vendor_owner_id` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendor`
--

LOCK TABLES `vendor` WRITE;
/*!40000 ALTER TABLE `vendor` DISABLE KEYS */;
INSERT INTO `vendor` VALUES (1,3,'451263897','Steve Ballmer','Steve Ballmer',5,3,1,1,0),(2,3,'456397127','Glenn Beck','Glenn Beck',2,1,1,1,1),(3,3,'456951123','Ben Bernanke','Ben Bernanke',1,1,1,0,0);
/*!40000 ALTER TABLE `vendor` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-05-11 16:16:42

-- MySQL dump 10.13  Distrib 5.6.19, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: pfleet
-- ------------------------------------------------------
-- Server version	5.6.19-0ubuntu0.14.04.1

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
  `account_type` int(10) unsigned DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `percentage` decimal(10,4) DEFAULT NULL,
  `priority` int(10) DEFAULT NULL,
  `limit_type` int(10) unsigned NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `process_type` int(10) unsigned DEFAULT NULL,
  `payee` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `payee_id` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `fk_bank_account_entity_id` (`entity_id`),
  KEY `fk_bank_account_payment_type` (`payment_type`),
  KEY `fk_bank_account_limit_type` (`limit_type`),
  KEY `fk_bank_account_account_type` (`account_type`),
  KEY `deleted` (`deleted`),
  KEY `fk_bank_account_process_type` (`process_type`),
  CONSTRAINT `fk_bank_account_account_type` FOREIGN KEY (`account_type`) REFERENCES `bank_account_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bank_account_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bank_account_limit_type` FOREIGN KEY (`limit_type`) REFERENCES `bank_account_limit_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bank_account_payment_type` FOREIGN KEY (`payment_type`) REFERENCES `payment_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bank_account_process_type` FOREIGN KEY (`process_type`) REFERENCES `disbursement_transaction_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `bank_account_cc`
--

DROP TABLE IF EXISTS `bank_account_cc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bank_account_cc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bank_account_id` int(10) unsigned NOT NULL,
  `card_number` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `ACH_bank_routing_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `ACH_bank_account_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`bank_account_id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_bank_account_cc_bank_account_id` (`bank_account_id`),
  CONSTRAINT `fk_bank_account_cc_bank_account_id` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_account` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bank_account_check`
--

DROP TABLE IF EXISTS `bank_account_check`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bank_account_check` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bank_account_id` int(10) unsigned NOT NULL,
  `payee_address` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `payee_address_2` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `payee_city` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `payee_state` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `payee_zip` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `check_message` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `check_message_2` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`bank_account_id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_bank_account_check_bank_account_id` (`bank_account_id`),
  CONSTRAINT `fk_bank_account_check_bank_account_id` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_account` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


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
  `card_number` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `amount` decimal(10,4) DEFAULT NULL,
  `percentage` decimal(10,4) DEFAULT NULL,
  `process_type` int(10) unsigned DEFAULT NULL,
  `priority` int(10) DEFAULT NULL,
  `payee` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `payee_id` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `payee_address` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `payee_address_2` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `payee_city` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `payee_state` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `payee_zip` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `check_message` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `check_message_2` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `fk_bank_account_history_payment_type` (`payment_type`),
  KEY `fk_bank_account_history_bank_account_id` (`bank_account_id`),
  KEY `fk_bank_account_history_process_type` (`process_type`),
  CONSTRAINT `fk_bank_account_history_bank_account_id` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bank_account_history_payment_type` FOREIGN KEY (`payment_type`) REFERENCES `payment_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bank_account_history_process_type` FOREIGN KEY (`process_type`) REFERENCES `disbursement_transaction_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


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
-- Table structure for table `bank_account_temp`
--

DROP TABLE IF EXISTS `bank_account_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bank_account_temp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_nickname` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `payment_type` int(10) unsigned DEFAULT NULL,
  `account_type` int(10) unsigned DEFAULT NULL,
  `name_on_account` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `percentage` decimal(10,4) DEFAULT NULL,
  `priority` int(10) DEFAULT NULL,
  `limit_type` int(10) unsigned DEFAULT NULL,
  `ACH_bank_routing_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `ACH_bank_account_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `card_number` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `limit_value` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `status_id` int(10) unsigned NOT NULL,
  `error` text COLLATE utf8_bin,
  `source_id` int(10) unsigned DEFAULT NULL,
  `contractor_temp_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_bank_account_temp_status_id` (`status_id`),
  KEY `fk_bank_account_temp_contractor_temp_id` (`contractor_temp_id`),
  CONSTRAINT `fk_bank_account_temp_contractor_temp_id` FOREIGN KEY (`contractor_temp_id`) REFERENCES `contractor_temp` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bank_account_temp_status_id` FOREIGN KEY (`status_id`) REFERENCES `payment_temp_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `bank_account_type`
--

DROP TABLE IF EXISTS `bank_account_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bank_account_type` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_account_type`
--

LOCK TABLES `bank_account_type` WRITE;
/*!40000 ALTER TABLE `bank_account_type` DISABLE KEYS */;
INSERT INTO `bank_account_type` VALUES (1,'Checking'),(2,'Savings');
/*!40000 ALTER TABLE `bank_account_type` ENABLE KEYS */;
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
  `terms` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `create_contractor_type` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`entity_id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_carrier_entity_id` (`entity_id`),
  CONSTRAINT `fk_carrier_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


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
  `expires` date DEFAULT NULL,
  `classification` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `division` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `department` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `route` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `correspondence_method` int(10) unsigned NOT NULL,
  `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `driver_license` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `carrier_id` int(10) unsigned NOT NULL,
  `carrier_status_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` int(10) unsigned NOT NULL,
  `start_date` date DEFAULT NULL,
  `termination_date` date DEFAULT NULL,
  `rehire_date` date DEFAULT NULL,
  `gender_id` tinyint(4) DEFAULT '1',
  `middle_initial` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `deduction_priority` tinyint(1) NOT NULL DEFAULT '1',
  `rac_priority` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`entity_id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_contractor_correspondence_method` (`correspondence_method`),
  KEY `fk_contactor_entity_id` (`entity_id`),
  KEY `carrier_id` (`carrier_id`),
  CONSTRAINT `contractor_ibfk_1` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_contactor_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_contractor_correspondence_method` FOREIGN KEY (`correspondence_method`) REFERENCES `entity_contact_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


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
-- Table structure for table `contractor_temp`
--

DROP TABLE IF EXISTS `contractor_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contractor_temp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `social_security_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `tax_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `state_of_operation` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `dob` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `expires` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `classification` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `division` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `department` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `route` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `correspondence_method` int(10) unsigned DEFAULT NULL,
  `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `driver_license` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `status_id` int(10) unsigned NOT NULL,
  `error` text COLLATE utf8_bin,
  `source_id` int(10) unsigned DEFAULT NULL,
  `middle_initial` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `gender_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `start_date` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `termination_date` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `rehire_date` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_contractor_temp_status_id` (`status_id`),
  CONSTRAINT `fk_contractor_temp_status_id` FOREIGN KEY (`status_id`) REFERENCES `payment_temp_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `contractor_vendor`
--

DROP TABLE IF EXISTS `contractor_vendor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contractor_vendor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contractor_id` int(10) unsigned NOT NULL,
  `vendor_id` int(10) unsigned NOT NULL,
  `status` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_contractor_vendor_contractor_id` (`contractor_id`),
  KEY `fk_contractor_vendor_vendor_id` (`vendor_id`),
  KEY `fk_contractor_vendor_status` (`status`),
  CONSTRAINT `fk_contractor_vendor_contractor_id0` FOREIGN KEY (`contractor_id`) REFERENCES `contractor` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_contractor_vendor_status0` FOREIGN KEY (`status`) REFERENCES `vendor_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_contractor_vendor_vendor_id0` FOREIGN KEY (`vendor_id`) REFERENCES `vendor` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `contractor_vendor_temp`
--

DROP TABLE IF EXISTS `contractor_vendor_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contractor_vendor_temp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` int(10) unsigned DEFAULT NULL,
  `vendor_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `status` int(10) unsigned DEFAULT NULL,
  `status_id` int(10) unsigned NOT NULL,
  `error` text COLLATE utf8_bin,
  `source_id` int(10) unsigned DEFAULT NULL,
  `contractor_temp_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_contractor_vendor_temp_status_id` (`status_id`),
  KEY `fk_contractor_vendor_temp_contractor_temp_id` (`contractor_temp_id`),
  CONSTRAINT `fk_contractor_vendor_temp_contractor_temp_id` FOREIGN KEY (`contractor_temp_id`) REFERENCES `contractor_temp` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_contractor_vendor_temp_status_id` FOREIGN KEY (`status_id`) REFERENCES `payment_temp_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


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
INSERT INTO `cycle_period` VALUES (1,'Weekly'),(2,'Biweekly'),(3,'Monthly'),(4,'Semi-Monthly'),(5,'Semi-Monthly'),(6,'Semi-Weekly');
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
  `rate` decimal(10,2) DEFAULT NULL,
  `eligible` int(11) DEFAULT NULL,
  `reserve_account_receiver` int(10) unsigned DEFAULT NULL,
  `first_start_day` int(10) DEFAULT NULL,
  `second_start_day` int(10) DEFAULT NULL,
  `deduction_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `week_offset` tinyint(1) DEFAULT NULL,
  `master_setup_id` int(10) unsigned DEFAULT NULL,
  `contractor_id` int(10) unsigned DEFAULT NULL,
  `changed` tinyint(1) NOT NULL DEFAULT '0',
  `biweekly_start_day` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_deduction_setup_level_id` (`level_id`),
  KEY `fk_deduction_setup_billing_cycle_id` (`billing_cycle_id`),
  KEY `fk_deduction_setup_provider_id` (`provider_id`),
  KEY `fk_deduction_setup_reserve_account_receiver` (`reserve_account_receiver`),
  KEY `deleted` (`deleted`),
  KEY `contractor_id` (`contractor_id`),
  KEY `master_setup_id` (`master_setup_id`),
  CONSTRAINT `fk_deduction_setup_billing_cycle_id` FOREIGN KEY (`billing_cycle_id`) REFERENCES `cycle_period` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_deduction_setup_contractor` FOREIGN KEY (`contractor_id`) REFERENCES `contractor` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deduction_setup_level_id` FOREIGN KEY (`level_id`) REFERENCES `setup_level` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deduction_setup_provider_id` FOREIGN KEY (`provider_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deduction_setup_reserve_account_receiver` FOREIGN KEY (`reserve_account_receiver`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `deductions`
--

DROP TABLE IF EXISTS `deductions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deductions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `setup_id` int(10) unsigned DEFAULT NULL,
  `category` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `invoice_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `invoice_due_date` date DEFAULT NULL,
  `department` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `gl_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `disbursement_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `disbursement_date` date DEFAULT NULL,
  `balance` decimal(10,2) DEFAULT NULL,
  `adjusted_balance` decimal(10,2) DEFAULT NULL,
  `approved_datetime` datetime DEFAULT NULL,
  `approved_by` int(10) unsigned DEFAULT NULL,
  `created_datetime` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `source_id` int(10) unsigned DEFAULT NULL,
  `status` int(10) unsigned DEFAULT NULL,
  `settlement_cycle_id` int(10) unsigned DEFAULT NULL,
  `added_in_cycle` int(10) unsigned DEFAULT NULL,
  `contractor_id` int(10) unsigned NOT NULL,
  `provider_id` int(10) unsigned NOT NULL,
  `terms` int(10) DEFAULT NULL,
  `recurring` int(10) DEFAULT NULL,
  `reserve_account_receiver` int(10) unsigned DEFAULT NULL,
  `billing_cycle_id` int(10) unsigned DEFAULT NULL,
  `eligible` int(11) DEFAULT NULL,
  `first_start_day` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `second_start_day` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `deduction_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `week_offset` tinyint(1) DEFAULT NULL,
  `recurring_parent_id` int(10) unsigned DEFAULT NULL,
  `carrier_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_deductions_setup_id` (`setup_id`),
  KEY `fk_deductions_approved_by` (`approved_by`),
  KEY `fk_deductions_created_by` (`created_by`),
  KEY `fk_deductions_source_id` (`source_id`),
  KEY `fk_deductions_status` (`status`),
  KEY `fk_deductions_settlement_cycle_id` (`settlement_cycle_id`),
  KEY `fk_deductions_contractor_entity_id` (`contractor_id`),
  KEY `fk_deduction_provider_id_idx` (`provider_id`),
  KEY `fk_deductions_recerve_account_receiver` (`reserve_account_receiver`),
  KEY `fk_deduction_billing_cycle_id_idx` (`billing_cycle_id`),
  KEY `fk_deductions_recurring` (`recurring`),
  KEY `settlement_deleted_index` (`settlement_cycle_id`,`deleted`),
  KEY `added_in_cycle` (`added_in_cycle`),
  CONSTRAINT `fk_deductions_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deductions_contractor_entity_id` FOREIGN KEY (`contractor_id`) REFERENCES `contractor` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deductions_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deductions_recurring` FOREIGN KEY (`recurring`) REFERENCES `recurring_title` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deductions_settlement_cycle_id` FOREIGN KEY (`settlement_cycle_id`) REFERENCES `settlement_cycle` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deductions_setup_id` FOREIGN KEY (`setup_id`) REFERENCES `deduction_setup` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deductions_source_id` FOREIGN KEY (`source_id`) REFERENCES `file_storage` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deductions_status` FOREIGN KEY (`status`) REFERENCES `payment_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deduction_billing_cycle_id` FOREIGN KEY (`billing_cycle_id`) REFERENCES `cycle_period` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deduction_provider_id` FOREIGN KEY (`provider_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_deduction_reserve_account_receiver` FOREIGN KEY (`reserve_account_receiver`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `deductions_temp`
--

DROP TABLE IF EXISTS `deductions_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deductions_temp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `setup_id` int(10) unsigned DEFAULT NULL,
  `category` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `invoice_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `invoice_date` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `invoice_due_date` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `department` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `gl_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `disbursement_date` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `contractor_id` int(10) unsigned NOT NULL,
  `deduction_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `provider_id` int(10) unsigned NOT NULL,
  `terms` int(10) DEFAULT NULL,
  `disbursement_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `status_id` int(10) unsigned NOT NULL,
  `error` text COLLATE utf8_bin,
  `created_datetime` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `status` int(10) unsigned NOT NULL DEFAULT '1',
  `source_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deduction_temp_status_id` (`status_id`),
  CONSTRAINT `deduction_temp_status_id` FOREIGN KEY (`status_id`) REFERENCES `payment_temp_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


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
  `amount` decimal(10,2) DEFAULT NULL,
  `status` int(11) unsigned DEFAULT NULL,
  `settlement_cycle_close_date` date DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `approved_by` int(10) unsigned DEFAULT NULL,
  `approved_datetime` datetime DEFAULT NULL,
  `settlement_cycle_id` int(10) unsigned NOT NULL,
  `entity_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `disbursement_reference` int(10) unsigned DEFAULT NULL,
  `escrow_account_history_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_disbursement_created_by` (`created_by`),
  KEY `fk_disbursement_approved_by` (`approved_by`),
  KEY `fk_disbursement_status` (`status`),
  KEY `fk_disbursement_entity_id` (`entity_id`),
  KEY `fk_disbursement_process_type` (`process_type`),
  KEY `fk_disbursement_bank_account_history_id` (`bank_account_history_id`),
  KEY `fk_disburstment_settlement_cycle_id` (`settlement_cycle_id`),
  CONSTRAINT `fk_disbursement_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disbursement_bank_account_history_id` FOREIGN KEY (`bank_account_history_id`) REFERENCES `bank_account_history` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disbursement_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disbursement_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disbursement_process_type` FOREIGN KEY (`process_type`) REFERENCES `disbursement_transaction_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_disbursement_status` FOREIGN KEY (`status`) REFERENCES `payment_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


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
INSERT INTO `disbursement_transaction_type` VALUES (1,'Settlement'),(2,'Deduction');
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
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_entity_entity_type_id` (`entity_type_id`),
  KEY `deleted` (`deleted`),
  CONSTRAINT `fk_entity_entity_type_id` FOREIGN KEY (`entity_type_id`) REFERENCES `entity_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `entity_contact_info`
--

DROP TABLE IF EXISTS `entity_contact_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entity_contact_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contact_type` int(10) unsigned NOT NULL,
  `value` text COLLATE utf8_bin NOT NULL,
  `entity_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_user_contact_info_contact_type` (`contact_type`),
  KEY `fk_entity_contact_info_entity_id` (`entity_id`),
  KEY `fk_entity_contact_info_user_id` (`user_id`),
  CONSTRAINT `fk_entity_contact_info_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_entity_contact_info_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_contact_info_contact_type` FOREIGN KEY (`contact_type`) REFERENCES `entity_contact_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `entity_contact_info_temp`
--

DROP TABLE IF EXISTS `entity_contact_info_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entity_contact_info_temp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contact_type` int(10) unsigned DEFAULT NULL,
  `value` text COLLATE utf8_bin,
  `status_id` int(10) unsigned NOT NULL,
  `error` text COLLATE utf8_bin,
  `source_id` int(10) unsigned DEFAULT NULL,
  `contractor_temp_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_entity_contact_info_temp_status_id` (`status_id`),
  KEY `fk_entity_contact_info_temp_contractor_temp_id` (`contractor_temp_id`),
  CONSTRAINT `fk_entity_contact_info_temp_contractor_temp_id` FOREIGN KEY (`contractor_temp_id`) REFERENCES `contractor_temp` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_entity_contact_info_temp_status_id` FOREIGN KEY (`status_id`) REFERENCES `payment_temp_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


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

INSERT INTO entity_contact_type (id, title) VALUES
(1, 'Address'),
(3, 'State'),
(4, 'Zip'),
(5, 'Phone'),
(2, 'City'),
(6, 'Office Phone'),
(7, 'Mobile Phone'),
(8, 'Email'),
(9, 'Fax'),
(10, 'Carrier Distributes');



--
-- Table structure for table `entity_history`
--

DROP TABLE IF EXISTS `entity_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entity_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cycle_id` int(10) unsigned NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `type_id` tinyint(1) unsigned NOT NULL,
  `data` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_cycle_id` (`cycle_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


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
-- Table structure for table `escrow_accounts`
--

DROP TABLE IF EXISTS `escrow_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `escrow_accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(10) unsigned NOT NULL,
  `escrow_account_holder` varchar(255) NOT NULL,
  `holder_federal_tax_id` varchar(255) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `bank_routing_number` varchar(255) NOT NULL,
  `bank_account_number` varchar(255) NOT NULL,
  `next_check_number` int(10) unsigned DEFAULT '1',
  `holder_address` varchar(255) NOT NULL,
  `holder_address_2` varchar(255) NOT NULL,
  `holder_city` varchar(255) NOT NULL,
  `holder_state` varchar(255) NOT NULL,
  `holder_zip` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `carrier_id` (`carrier_id`),
  CONSTRAINT `escrow_accounts_ibfk_1` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `escrow_accounts_history`
--

DROP TABLE IF EXISTS `escrow_accounts_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `escrow_accounts_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(10) unsigned NOT NULL,
  `escrow_account_holder` varchar(255) NOT NULL,
  `holder_federal_tax_id` varchar(255) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `bank_routing_number` varchar(255) NOT NULL,
  `bank_account_number` varchar(255) NOT NULL,
  `next_check_number` int(10) unsigned DEFAULT NULL,
  `holder_address` varchar(255) NOT NULL,
  `holder_address_2` varchar(255) NOT NULL,
  `holder_city` varchar(255) NOT NULL,
  `holder_state` varchar(255) NOT NULL,
  `holder_zip` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `carrier_id` (`carrier_id`),
  CONSTRAINT `escrow_accounts_carrier_id` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


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
INSERT INTO `file_storage_type` VALUES (1,'Payments'),(2,'Deductions'),(3,'Contractors'),(4,'Contacts'),(5,'Contractor-Vendor'),(6,'Bank Accounts');
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
  `rate` decimal(10,2) DEFAULT NULL,
  `first_start_day` int(10) DEFAULT NULL,
  `second_start_day` int(10) DEFAULT NULL,
  `quantity` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `week_offset` tinyint(1) DEFAULT NULL,
  `master_setup_id` int(10) unsigned DEFAULT NULL,
  `contractor_id` int(10) unsigned DEFAULT NULL,
  `changed` tinyint(1) NOT NULL DEFAULT '0',
  `biweekly_start_day` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_payment_setup_level_id` (`level_id`),
  KEY `fk_payment_setup_billing_cycle_id` (`billing_cycle_id`),
  KEY `fk_payment_setup_carrier_id` (`carrier_id`),
  KEY `deleted` (`deleted`),
  KEY `contractor_id` (`contractor_id`),
  KEY `master_setup_id` (`master_setup_id`),
  CONSTRAINT `fk_payment_setup_billing_cycle_id` FOREIGN KEY (`billing_cycle_id`) REFERENCES `cycle_period` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_setup_carrier_id` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_payment_setup_contractor` FOREIGN KEY (`contractor_id`) REFERENCES `contractor` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_setup_level_id` FOREIGN KEY (`level_id`) REFERENCES `setup_level` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


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
INSERT INTO `payment_status` VALUES (1,'Verified'),(2,'Processed'),(3,'Approved'),(4,'Not Approved');
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
  `setup_id` int(10) unsigned DEFAULT NULL,
  `category` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `invoice` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `invoice_due_date` date DEFAULT NULL,
  `department` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `gl_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `amount` decimal(10,4) DEFAULT NULL,
  `check_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `disbursement_date` date DEFAULT NULL,
  `approved_datetime` datetime DEFAULT NULL,
  `approved_by` int(10) unsigned DEFAULT NULL,
  `created_datetime` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `source_id` int(10) unsigned DEFAULT NULL,
  `status` int(10) unsigned DEFAULT NULL,
  `settlement_cycle_id` int(10) unsigned DEFAULT NULL,
  `added_in_cycle` int(10) unsigned DEFAULT NULL,
  `contractor_id` int(10) unsigned NOT NULL,
  `balance` decimal(10,4) DEFAULT NULL,
  `carrier_id` int(10) unsigned NOT NULL,
  `payment_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `carrier_payment_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `terms` int(10) DEFAULT NULL,
  `disbursement_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `recurring` int(11) DEFAULT NULL,
  `billing_cycle_id` int(10) unsigned DEFAULT NULL,
  `first_start_day` int(10) DEFAULT NULL,
  `second_start_day` int(10) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `week_offset` tinyint(1) DEFAULT NULL,
  `recurring_parent_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_payments_setup_id` (`setup_id`),
  KEY `fk_payments_approved_by` (`approved_by`),
  KEY `fk_payments_created_by` (`created_by`),
  KEY `fk_payments_source_id` (`source_id`),
  KEY `fk_payments_status` (`status`),
  KEY `fk_payments_settlement_cycle_id` (`settlement_cycle_id`),
  KEY `fk_payments_contractor_entity_id` (`contractor_id`),
  KEY `fk_payments_carrier_id_idx` (`carrier_id`),
  KEY `fk_payments_cycle_period_id_idx` (`billing_cycle_id`),
  KEY `settlement_deleted_index` (`settlement_cycle_id`,`deleted`),
  KEY `added_in_cycle` (`added_in_cycle`),
  CONSTRAINT `fk_payments_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payments_carrier_entity_id` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payments_contractor_entity_id` FOREIGN KEY (`contractor_id`) REFERENCES `contractor` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payments_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payments_cycle_period_id` FOREIGN KEY (`billing_cycle_id`) REFERENCES `cycle_period` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payments_settlement_cycle_id` FOREIGN KEY (`settlement_cycle_id`) REFERENCES `settlement_cycle` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payments_setup_id` FOREIGN KEY (`setup_id`) REFERENCES `payment_setup` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payments_source_id` FOREIGN KEY (`source_id`) REFERENCES `file_storage` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payments_status` FOREIGN KEY (`status`) REFERENCES `payment_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `payments_temp`
--

DROP TABLE IF EXISTS `payments_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments_temp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `setup_id` int(10) unsigned DEFAULT NULL,
  `category` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `invoice` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `invoice_date` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `invoice_due_date` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `department` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `gl_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `disbursement_date` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `contractor_id` int(10) unsigned NOT NULL,
  `payment_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `carrier_payment_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `terms` int(10) DEFAULT NULL,
  `disbursement_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `status_id` int(10) unsigned NOT NULL,
  `error` text COLLATE utf8_bin,
  `created_datetime` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `status` int(10) unsigned NOT NULL DEFAULT '1',
  `source_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_temp_status_id` (`status_id`),
  CONSTRAINT `payment_temp_status_id` FOREIGN KEY (`status_id`) REFERENCES `payment_temp_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `recurring_title`
--

DROP TABLE IF EXISTS `recurring_title`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recurring_title` (
  `id` int(11) NOT NULL,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recurring_title`
--

LOCK TABLES `recurring_title` WRITE;
/*!40000 ALTER TABLE `recurring_title` DISABLE KEYS */;
INSERT INTO `recurring_title` VALUES (0,'No'),(1,'Yes');
/*!40000 ALTER TABLE `recurring_title` ENABLE KEYS */;
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
  `bank_account_id` int(10) unsigned DEFAULT NULL,
  `account_name` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `min_balance` decimal(10,2) DEFAULT NULL,
  `contribution_amount` decimal(10,2) DEFAULT NULL,
  `max_withdrawal_amount` decimal(10,2) DEFAULT NULL,
  `initial_balance` decimal(10,2) DEFAULT NULL,
  `current_balance` decimal(10,2) DEFAULT NULL,
  `disbursement_code` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `balance` decimal(10,2) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `starting_balance` decimal(10,2) DEFAULT NULL,
  `verify_balance` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reserve_account_entity_id` (`entity_id`),
  KEY `fk_reserve_account_bank_account_id` (`bank_account_id`),
  KEY `deleted` (`deleted`),
  CONSTRAINT `fk_reserve_account_bank_account_id` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_account_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


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
  `vendor_reserve_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reserve_account_contractor_reserve_account_id` (`reserve_account_id`),
  KEY `fk_reserve_account_contractor_reserve_account_vendor_id` (`reserve_account_vendor_id`),
  CONSTRAINT `fk_reserve_account_contractor_reserve_account_id` FOREIGN KEY (`reserve_account_id`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_account_contractor_reserve_account_vendor_id` FOREIGN KEY (`reserve_account_vendor_id`) REFERENCES `reserve_account_vendor` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `reserve_account_history`
--

DROP TABLE IF EXISTS `reserve_account_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reserve_account_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `settlement_cycle_id` int(10) unsigned NOT NULL,
  `reserve_account_id` int(10) unsigned NOT NULL,
  `verify_balance` decimal(10,2) NOT NULL,
  `starting_balance` decimal(10,2) NOT NULL,
  `current_balance` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_settlement_rac` (`settlement_cycle_id`,`reserve_account_id`),
  KEY `reserve_account_id` (`reserve_account_id`),
  KEY `settlement_cycle_id` (`settlement_cycle_id`),
  CONSTRAINT `reserve_account_history_reserve_account_id` FOREIGN KEY (`reserve_account_id`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `reserve_account_history_settlement_cycle_id` FOREIGN KEY (`settlement_cycle_id`) REFERENCES `settlement_cycle` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `reserve_transaction`
--

DROP TABLE IF EXISTS `reserve_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reserve_transaction` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reserve_account_contractor` int(10) unsigned DEFAULT NULL,
  `reserve_account_vendor` int(10) unsigned DEFAULT NULL,
  `vendor_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `type` int(10) unsigned NOT NULL,
  `deduction_id` int(10) unsigned DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `balance` decimal(10,4) DEFAULT NULL,
  `adjusted_balance` decimal(10,4) DEFAULT NULL,
  `settlement_cycle_id` int(10) unsigned NOT NULL,
  `approved_datetime` datetime DEFAULT NULL,
  `approved_by` int(10) unsigned DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `source_id` int(10) unsigned DEFAULT NULL,
  `disbursement_id` int(11) DEFAULT NULL,
  `status` int(10) unsigned DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `contractor_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reserve_transaction_reserve_account_sender` (`reserve_account_contractor`),
  KEY `fk_reserve_transaction_reserve_account_receiver` (`reserve_account_vendor`),
  KEY `fk_reserve_transaction_type` (`type`),
  KEY `fk_reserve_transaction_deduction_id` (`deduction_id`),
  KEY `fk_reserve_transaction_approved_by` (`approved_by`),
  KEY `fk_reserve_transaction_created_by` (`created_by`),
  KEY `fk_reserve_transaction_source_id` (`source_id`),
  KEY `fk_reserve_transaction_settlement_cycle_id` (`settlement_cycle_id`),
  KEY `fk_reserve_transaction_status` (`status`),
  KEY `contractor_id` (`contractor_id`),
  KEY `settlement_deleted_index` (`settlement_cycle_id`,`deleted`,`type`),
  CONSTRAINT `fk_reserve_transaction_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_deduction_id` FOREIGN KEY (`deduction_id`) REFERENCES `deductions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_settlement_cycle_id` FOREIGN KEY (`settlement_cycle_id`) REFERENCES `settlement_cycle` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_source_id` FOREIGN KEY (`source_id`) REFERENCES `file_storage` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_status` FOREIGN KEY (`status`) REFERENCES `payment_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_type` FOREIGN KEY (`type`) REFERENCES `reserve_transaction_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `reserve_transaction_ibfk_1` FOREIGN KEY (`contractor_id`) REFERENCES `contractor` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `reserve_transaction_ibfk_2` FOREIGN KEY (`reserve_account_contractor`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `reserve_transaction_ibfk_3` FOREIGN KEY (`reserve_account_vendor`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `reserve_transaction_type`
--

DROP TABLE IF EXISTS `reserve_transaction_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reserve_transaction_type` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  `type_priority` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type_priority` (`type_priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reserve_transaction_type`
--

LOCK TABLES `reserve_transaction_type` WRITE;
/*!40000 ALTER TABLE `reserve_transaction_type` DISABLE KEYS */;
INSERT INTO `reserve_transaction_type` VALUES (1,'Contribution',3),(2,'Withdrawal',4),(3,'Cash Advance',5),(4,'Adjustment Decrease',2),(5,'Adjustment Increase',1);
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
  `processing_date` date DEFAULT NULL,
  `disbursement_date` date DEFAULT NULL,
  `disbursement_status` int(11) unsigned DEFAULT '4',
  `parent_cycle_id` int(10) DEFAULT NULL,
  `approved_datetime` datetime DEFAULT NULL,
  `approved_by` int(10) unsigned DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `disbursement_approved_datetime` datetime DEFAULT NULL,
  `disbursement_approved_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_settlement_cycle_carrier_id` (`carrier_id`),
  KEY `fk_settlement_cycle_cycle_period_id` (`cycle_period_id`),
  KEY `fk_settlement_cycle_status_id` (`status_id`),
  KEY `fk_settlement_cycle_disbursement_status` (`disbursement_status`),
  KEY `parent_cycle_id` (`parent_cycle_id`),
  KEY `fk_settlement_cycle_approved_by` (`approved_by`),
  KEY `deleted` (`deleted`),
  KEY `fk_settlement_cycle_disbursement_approved_by` (`disbursement_approved_by`),
  CONSTRAINT `fk_settlement_cycle_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_settlement_cycle_carrier_id` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_settlement_cycle_cycle_period_id` FOREIGN KEY (`cycle_period_id`) REFERENCES `cycle_period` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_settlement_cycle_disbursement_approved_by` FOREIGN KEY (`disbursement_approved_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_settlement_cycle_disbursement_status` FOREIGN KEY (`disbursement_status`) REFERENCES `payment_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_settlement_cycle_status_id` FOREIGN KEY (`status_id`) REFERENCES `settlement_cycle_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `settlement_cycle_rule`
--

DROP TABLE IF EXISTS `settlement_cycle_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settlement_cycle_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(10) unsigned NOT NULL,
  `cycle_period_id` int(10) unsigned NOT NULL,
  `payment_terms` int(11) NOT NULL,
  `disbursement_terms` int(11) NOT NULL,
  `cycle_start_date` date NOT NULL,
  `first_start_day` int(11) DEFAULT NULL,
  `second_start_day` int(11) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `last_closed_cycle_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_settlement_cycle_rule_carrier_id` (`carrier_id`),
  KEY `fk_settlement_cycle_rule_cycle_period_id` (`cycle_period_id`),
  KEY `deleted` (`deleted`),
  KEY `last_closed_cycle_id` (`last_closed_cycle_id`),
  CONSTRAINT `fk_settlement_cycle_rule_carrier_id` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_settlement_cycle_rule_cycle_period_id` FOREIGN KEY (`cycle_period_id`) REFERENCES `cycle_period` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_settlement_cycle_rule_last_cycle_id` FOREIGN KEY (`last_closed_cycle_id`) REFERENCES `settlement_cycle` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


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
INSERT INTO `settlement_cycle_status` VALUES (1,'Not Verified'),(2,'Verified'),(3,'Processed'),(4,'Approved');
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
INSERT INTO `setup_level` VALUES (1,'Master'),(2,'Individual');
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
-- Table structure for table `tbl_migrations`
--

DROP TABLE IF EXISTS `tbl_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_migrations` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` char(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` char(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=258 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_migrations`
--

LOCK TABLES `tbl_migrations` WRITE;
/*!40000 ALTER TABLE `tbl_migrations` DISABLE KEYS */;
INSERT INTO `tbl_migrations` VALUES (1,'update20120425_174500.sql','2014-03-03 12:39:58'),(2,'update20120426_235100.sql','2014-03-03 12:39:58'),(3,'update20120427_120000.sql','2014-03-03 12:39:58'),(4,'update20120427_164300.sql','2014-03-03 12:39:58'),(5,'update20120430_110000.sql','2014-03-03 12:39:58'),(6,'update20120502_154800.sql','2014-03-03 12:39:58'),(7,'update20120502_171600.sql','2014-03-03 12:39:58'),(8,'update20120508_150000.sql','2014-03-03 12:39:58'),(9,'update20120508_152000.sql','2014-03-03 12:39:58'),(10,'update20120508_233000.sql','2014-03-03 12:39:58'),(11,'update20120510_150000.sql','2014-03-03 12:39:58'),(12,'update20120511_090000.sql','2014-03-03 12:39:58'),(13,'update20120511_160000.sql','2014-03-03 12:39:58'),(14,'update20120514_140900.sql','2014-03-03 12:39:58'),(15,'update20120514_170700.sql','2014-03-03 12:39:58'),(16,'update20120515_142100.sql','2014-03-03 12:39:58'),(17,'update20120515_152100.sql','2014-03-03 12:39:58'),(18,'update20120516_100000.sql','2014-03-03 12:39:58'),(19,'update20120516_170000.sql','2014-03-03 12:39:58'),(20,'update20120517_094500.sql','2014-03-03 12:39:58'),(21,'update20120517_133500.sql','2014-03-03 12:39:58'),(22,'update20120517_150000.sql','2014-03-03 12:39:58'),(23,'update20120518_103000.sql','2014-03-03 12:39:58'),(24,'update20120518_163000.sql','2014-03-03 12:39:58'),(25,'update20120521_094000.sql','2014-03-03 12:39:58'),(26,'update20120521_102000.sql','2014-03-03 12:39:58'),(27,'update20120521_113000.sql','2014-03-03 12:39:58'),(28,'update20120522_093000.sql','2014-03-03 12:39:58'),(29,'update20120522_150000.sql','2014-03-03 12:39:58'),(30,'update20120522_165000.sql','2014-03-03 12:39:58'),(31,'update20120523_100000.sql','2014-03-03 12:39:58'),(32,'update20120523_160000.sql','2014-03-03 12:39:58'),(33,'update20120528_101100.sql','2014-03-03 12:39:58'),(34,'update20120528_104000.sql','2014-03-03 12:39:58'),(35,'update20120529_164500.sql','2014-03-03 12:39:58'),(36,'update20120604_130000.sql','2014-03-03 12:39:58'),(37,'update20120605_143500.sql','2014-03-03 12:39:58'),(38,'update20120606_113000.sql','2014-03-03 12:39:58'),(39,'update20120606_180000.sql','2014-03-03 12:39:58'),(40,'update20120607_160000.sql','2014-03-03 12:39:58'),(41,'update20120608_162000.sql','2014-03-03 12:39:58'),(42,'update20120611_112000.sql','2014-03-03 12:39:58'),(43,'update20120611_122000.sql','2014-03-03 12:39:58'),(44,'update20120611_122500.sql','2014-03-03 12:39:58'),(45,'update20120611_173000.sql','2014-03-03 12:39:58'),(46,'update20120612_105600.sql','2014-03-03 12:39:58'),(47,'update20120613_140500.sql','2014-03-03 12:39:58'),(48,'update20120614_193000.sql','2014-03-03 12:39:58'),(49,'update20120615_090000.sql','2014-03-03 12:39:58'),(50,'update20120615_100000.sql','2014-03-03 12:39:58'),(51,'update20120615_174000.sql','2014-03-03 12:39:58'),(52,'update20120619_122000.sql','2014-03-03 12:39:58'),(53,'update20120620_180000.sql','2014-03-03 12:39:58'),(54,'update20120621_120000.sql','2014-03-03 12:39:58'),(55,'update20120621_170000.sql','2014-03-03 12:39:58'),(56,'update20120622_123000.sql','2014-03-03 12:39:58'),(57,'update20120627_093000.sql','2014-03-03 12:39:58'),(58,'update20120627_124000.sql','2014-03-03 12:39:58'),(59,'update20120627_163000.sql','2014-03-03 12:39:58'),(60,'update20120627_173000.sql','2014-03-03 12:39:58'),(61,'update20120629_093000.sql','2014-03-03 12:39:58'),(62,'update20120629_151000.sql','2014-03-03 12:39:58'),(63,'update20120629_161000.sql','2014-03-03 12:39:58'),(64,'update20120630_133000.sql','2014-03-03 12:39:58'),(65,'update20120704_104500.sql','2014-03-03 12:39:58'),(66,'update20120704_121000.sql','2014-03-03 12:39:58'),(67,'update20120704_143100.sql','2014-03-03 12:39:58'),(68,'update20120705_120000.sql','2014-03-03 12:39:58'),(69,'update20120706_104000.sql','2014-03-03 12:39:58'),(70,'update20120706_131000.sql','2014-03-03 12:39:58'),(71,'update20120710_093000.sql','2014-03-03 12:39:58'),(72,'update20120710_172000.sql','2014-03-03 12:39:58'),(73,'update20120711_141000.sql','2014-03-03 12:39:58'),(74,'update20120711_162000.sql','2014-03-03 12:39:58'),(75,'update20120711_163000.sql','2014-03-03 12:39:58'),(76,'update20120712_110000.sql','2014-03-03 12:39:58'),(77,'update20120712_142000.sql','2014-03-03 12:39:58'),(78,'update20120712_154000.sql','2014-03-03 12:39:58'),(79,'update20120712_160000.sql','2014-03-03 12:39:58'),(80,'update20120713_100000.sql','2014-03-03 12:39:58'),(81,'update20120713_102000.sql','2014-03-03 12:39:58'),(82,'update20120713_112000.sql','2014-03-03 12:39:58'),(83,'update20120713_121500.sql','2014-03-03 12:39:58'),(84,'update20120713_144000.sql','2014-03-03 12:39:58'),(85,'update20120713_173000.sql','2014-03-03 12:39:58'),(86,'update20120716_113000.sql','2014-03-03 12:39:58'),(87,'update20120716_171000.sql','2014-03-03 12:39:58'),(88,'update20120716_173000.sql','2014-03-03 12:39:58'),(89,'update20120717_153000.sql','2014-03-03 12:39:58'),(90,'update20120717_173000.sql','2014-03-03 12:39:58'),(91,'update20120718_092000.sql','2014-03-03 12:39:58'),(92,'update20120718_100000.sql','2014-03-03 12:39:58'),(93,'update20120718_112000.sql','2014-03-03 12:39:58'),(94,'update20120718_112200.sql','2014-03-03 12:39:58'),(95,'update20120718_130000.sql','2014-03-03 12:39:58'),(96,'update20120718_164000.sql','2014-03-03 12:39:58'),(97,'update20120718_235900.sql','2014-03-03 12:39:58'),(98,'update20120719_100000.sql','2014-03-03 12:39:58'),(99,'update20120723_093000.sql','2014-03-03 12:39:58'),(100,'update20120723_113000.sql','2014-03-03 12:39:58'),(101,'update20120727_114000.sql','2014-03-03 12:39:58'),(102,'update20120731_233500.sql','2014-03-03 12:39:58'),(103,'update20120801_095000.sql','2014-03-03 12:39:58'),(104,'update20120801_101500.sql','2014-03-03 12:39:58'),(105,'update20120803_112100.sql','2014-03-03 12:39:58'),(106,'update20120803_145000.sql','2014-03-03 12:39:58'),(107,'update20120803_162500.sql','2014-03-03 12:39:58'),(108,'update20120808_094100.sql','2014-03-03 12:39:58'),(109,'update20120810_142500.sql','2014-03-03 12:39:58'),(110,'update20120814_155000.sql','2014-03-03 12:39:58'),(111,'update20120815_100500.sql','2014-03-03 12:39:58'),(112,'update20120911_093500.sql','2014-03-03 12:39:58'),(113,'update20120911_151000.sql','2014-03-03 12:39:58'),(114,'update20120911_231300.sql','2014-03-03 12:39:58'),(115,'update20120912_104000.sql','2014-03-03 12:39:58'),(116,'update20120912_105000.sql','2014-03-03 12:39:58'),(117,'update20120917_102000.sql','2014-03-03 12:39:58'),(118,'update20120917_140000.sql','2014-03-03 12:39:58'),(119,'update20120929_213000.sql','2014-03-03 12:39:58'),(120,'update20121002_142000.sql','2014-03-03 12:39:58'),(121,'update20121002_213500.sql','2014-03-03 12:39:58'),(122,'update20121005_121100.sql','2014-03-03 12:39:58'),(123,'update20121008_113000.sql','2014-03-03 12:39:58'),(124,'update20121008_133000.sql','2014-03-03 12:39:58'),(125,'update20121008_133500.sql','2014-03-03 12:39:58'),(126,'update20121015_120000.sql','2014-03-03 12:39:58'),(127,'update20121015_123500.sql','2014-03-03 12:39:58'),(128,'update20121015_130000.sql','2014-03-03 12:39:58'),(129,'update20121015_172000.sql','2014-03-03 12:39:58'),(130,'update20121016_154400.sql','2014-03-03 12:39:58'),(131,'update20121017_105600.sql','2014-03-03 12:39:58'),(132,'update20121018_182000.sql','2014-03-03 12:39:58'),(133,'update20121019_114600.sql','2014-03-03 12:39:58'),(134,'update20121105_143000.sql','2014-03-03 12:39:58'),(135,'update20121106_093500.sql','2014-03-03 12:39:58'),(136,'update20121108_172500.sql','2014-03-03 12:39:58'),(137,'update20121109_084500.sql','2014-03-03 12:39:58'),(138,'update20121112_090000.sql','2014-03-03 12:39:58'),(139,'update20121114_150000.sql','2014-03-03 12:39:58'),(140,'update20140311_140100.sql','2014-03-26 07:40:04'),(141,'update20140331_140100.sql','2014-03-31 10:50:04'),(142,'update20140324_121400.sql','2014-04-04 10:10:04'),(143,'update20140402_152500.sql','2014-04-04 10:10:04'),(144,'update20140403_155400.sql','2014-04-04 10:10:04'),(145,'update20140331_163800.sql','2014-04-08 09:10:04'),(146,'update20140411_165700.sql','2014-04-14 04:20:04'),(147,'update20140414_150900.sql','2014-04-14 10:10:04'),(148,'update20140415_133100.sql','2014-04-15 06:40:04'),(150,'update20140416_163700.sql','2014-04-16 09:40:04'),(151,'update20140416_180500.sql','2014-04-16 11:20:03'),(152,'update20140417_153100.sql','2014-04-17 09:20:04'),(153,'update20140417_181100.sql','2014-04-17 11:50:03'),(154,'update20140421_093200.sql','2014-04-21 03:00:04'),(155,'update20140421_111300.sql','2014-04-21 04:30:04'),(156,'update20140421_174800.sql','2014-04-21 11:00:04'),(157,'update20140416_133100.sql','2014-04-23 04:50:03'),(158,'update20140418_100000.sql','2014-04-23 04:50:03'),(159,'update20140423_150300.sql','2014-04-23 09:50:03'),(160,'update20140424_112500.sql','2014-04-24 09:20:04'),(161,'update20140424_090400.sql','2014-04-24 12:50:04'),(162,'update20140425_160000.sql','2014-04-25 13:30:04'),(163,'update20140425_104900.sql','2014-04-26 04:50:04'),(164,'update20140426_140300.sql','2014-04-26 07:10:03'),(165,'update20140506_114500.sql','2014-05-05 10:50:04'),(166,'update20140506_191500.sql','2014-05-07 03:50:04'),(167,'update20140507_144100.sql','2014-05-07 09:20:04'),(168,'update20140513_180000.sql','2014-05-14 06:30:03'),(169,'update20140514_165700.sql','2014-05-14 12:40:04'),(170,'update20140515_141100.sql','2014-05-15 12:20:03'),(171,'update20140516_173200.sql','2014-05-16 10:50:03'),(172,'update20140527_130600.sql','2014-05-27 06:20:03'),(173,'update20140529_110200.sql','2014-05-29 05:50:04'),(174,'update20140602_110200.sql','2014-06-02 06:50:04'),(175,'update20140604_152200.sql','2014-06-05 11:20:04'),(176,'update20140606_174500.sql','2014-06-06 11:00:04'),(177,'update20140610_154300.sql','2014-06-10 11:00:03'),(178,'update20140612_090000.sql','2014-06-12 04:20:04'),(179,'update20140616_150000.sql','2014-06-16 09:10:03'),(180,'update20140617_131200.sql','2014-06-17 07:40:04'),(181,'update20140618_131200.sql','2014-06-19 07:30:03'),(182,'update20140623_220000.sql','2014-06-23 15:00:04'),(184,'update20140624_110000.sql','2014-06-24 04:07:47'),(185,'update20140625_143500.sql','2014-06-25 09:30:03'),(186,'update20140625_161000.sql','2014-06-26 04:30:03'),(187,'update20140701_174000.sql','2014-07-01 11:50:03'),(188,'update20140702_100500.sql','2014-07-02 03:30:04'),(189,'update20140702_141700.sql','2014-07-02 08:50:02'),(190,'update20140702_154300.sql','2014-07-02 09:20:03'),(191,'update20140808_181800.sql','2014-07-09 09:30:03'),(192,'update20140712_183100.sql','2014-07-12 12:00:04'),(193,'update20140714_173000.sql','2014-07-15 04:00:04'),(194,'update20140715_113000.sql','2014-07-15 04:40:03'),(195,'update20140722_111600.sql','2014-07-25 05:10:04'),(196,'update20140722_101800.sql','2014-07-28 02:50:04'),(197,'update20140804_160000.sql','2014-08-04 09:10:03'),(198,'update20140801_175200.sql','2014-08-05 09:00:04'),(199,'update20140805_164600.sql','2014-08-05 12:00:04'),(200,'update20140807_180300.sql','2014-08-08 07:00:03'),(201,'update20140811_152600.sql','2014-08-11 08:40:04'),(202,'update20140811_180300.sql','2014-08-11 11:20:03'),(203,'update20140812_145200.sql','2014-08-12 08:10:04'),(204,'update20140819_100000.sql','2014-08-19 11:10:03'),(205,'update20140820_094900.sql','2014-08-20 10:00:04'),(206,'update20140822_112000.sql','2014-08-22 09:10:03'),(207,'update20140825_110000.sql','2014-08-25 10:20:04'),(208,'update20140825_230000.sql','2014-08-25 16:00:04'),(209,'update20140827_170000.sql','2014-08-27 10:00:04'),(210,'update20140904_110000.sql','2014-09-04 09:00:04'),(211,'update20140904_164000.sql','2014-09-10 08:30:04'),(212,'update20140910_102400.sql','2014-09-10 08:30:04'),(213,'update20140911_132400.sql','2014-09-11 08:50:03'),(214,'update20140917_154700.sql','2014-09-17 09:00:04'),(215,'update20140918_143500.sql','2014-09-18 09:10:04'),(216,'update20140922_113800.sql','2014-09-23 07:20:04'),(217,'update20140923_113800.sql','2014-09-23 09:10:03'),(218,'update20140923_203800.sql','2014-09-23 13:50:03'),(219,'update20140924_093900.sql','2014-09-24 10:20:04'),(220,'update20141010_113000.sql','2014-10-10 10:20:04'),(221,'update20141013_175200.sql','2014-10-13 11:40:04'),(222,'update20141015_115100.sql','2014-10-15 08:30:04'),(223,'update20141016_151100.sql','2014-10-16 08:40:04'),(224,'update20141021_112800.sql','2014-10-21 04:40:04'),(225,'update20141022_192400.sql','2014-10-22 12:40:04'),(226,'update20141023_170800.sql','2014-10-23 11:50:04'),(227,'update20141023_190900.sql','2014-10-24 05:10:04'),(228,'update20141024_163800.sql','2014-11-10 01:50:03'),(229,'update20141027_153900.sql','2014-11-10 01:50:03'),(230,'update20141028_160000.sql','2014-11-10 01:50:03'),(231,'update20141030_140000.sql','2014-11-10 01:50:03'),(232,'update20141030_164400.sql','2014-11-10 01:50:03'),(233,'update20141123_153100.sql','2014-11-23 07:50:03'),(234,'update20141124_215200.sql','2014-11-25 05:50:04'),(235,'update20141124_103100.sql','2014-12-03 07:20:04'),(236,'update20141127_112000.sql','2014-12-03 07:20:04'),(237,'update20141204_111000.sql','2014-12-04 03:40:03'),(238,'update20141204_112000.sql','2014-12-04 03:40:03'),(239,'update20141222_2212900.sql','2014-12-22 15:00:04'),(240,'update20141223_114000.sql','2014-12-29 09:50:04'),(241,'update20150106_134000.sql','2015-01-08 06:50:04'),(242,'update20150108_134000.sql','2015-01-08 09:30:04'),(243,'update20150113_210000.sql','2015-01-13 16:20:03'),(244,'update20150121_180000.sql','2015-01-21 16:50:04'),(245,'update20150128_132600.sql','2015-01-28 05:50:03'),(246,'update20150129_172600.sql','2015-01-29 09:40:03'),(247,'update20150209_103100.sql','2015-02-09 11:50:04'),(248,'update20150129_141000.sql','2015-04-09 04:40:08'),(249,'update20150415_092800.sql','2015-04-15 02:50:04'),(250,'update20150415_181000.sql','2015-04-16 09:10:03'),(251,'update20150416_170600.sql','2015-04-16 11:40:04'),(252,'update20150417_190600.sql','2015-04-18 15:40:03'),(253,'update20150422_120700.sql','2015-04-22 08:10:04'),(254,'update20150424_170400.sql','2015-04-24 10:20:03'),(255,'update20150428_114402.sql','2015-04-28 08:10:04'),(256,'update20150428_172000.sql','2015-04-28 12:10:03'),(257,'update20150430_192400.sql','2015-04-30 13:00:04');
/*!40000 ALTER TABLE `tbl_migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tmp_disbursement_check`
--

DROP TABLE IF EXISTS `tmp_disbursement_check`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tmp_disbursement_check` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `disburstment_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `user_permissions`
--

DROP TABLE IF EXISTS `user_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `settlement_edit` tinyint(1) NOT NULL DEFAULT '1',
  `settlement_verify` tinyint(1) NOT NULL DEFAULT '1',
  `settlement_process` tinyint(1) NOT NULL DEFAULT '1',
  `settlement_delete` tinyint(1) NOT NULL DEFAULT '1',
  `settlement_approve` tinyint(1) NOT NULL DEFAULT '1',
  `settlement_reject` tinyint(1) NOT NULL DEFAULT '1',
  `settlement_data_view` tinyint(1) NOT NULL DEFAULT '1',
  `settlement_data_manage` tinyint(1) NOT NULL DEFAULT '1',
  `settlement_rule_view` tinyint(1) NOT NULL DEFAULT '1',
  `settlement_rule_manage` tinyint(1) NOT NULL DEFAULT '1',
  `settlement_escrow_account_view` tinyint(1) NOT NULL DEFAULT '1',
  `reserve_transaction_vendor_view` tinyint(1) NOT NULL DEFAULT '1',
  `reserve_account_carrier_view` tinyint(1) NOT NULL DEFAULT '1',
  `reserve_account_carrier_manage` tinyint(1) NOT NULL DEFAULT '1',
  `reserve_account_vendor_view` tinyint(1) NOT NULL DEFAULT '1',
  `reserve_account_vendor_manage` tinyint(1) NOT NULL DEFAULT '1',
  `reserve_account_contractor_view` tinyint(1) NOT NULL DEFAULT '1',
  `bank_account_contractor_view` tinyint(1) NOT NULL DEFAULT '1',
  `bank_account_contractor_manage` tinyint(1) NOT NULL DEFAULT '1',
  `bank_account_vendor_view` tinyint(1) NOT NULL DEFAULT '1',
  `bank_account_vendor_manage` tinyint(1) NOT NULL DEFAULT '1',
  `bank_account_carrier_view` tinyint(1) NOT NULL DEFAULT '1',
  `bank_account_carrier_manage` tinyint(1) NOT NULL DEFAULT '1',
  `disbursement_view` tinyint(1) NOT NULL DEFAULT '1',
  `disbursement_manage` tinyint(1) NOT NULL DEFAULT '1',
  `disbursement_approve` tinyint(1) NOT NULL DEFAULT '1',
  `vendor_deduction_view` tinyint(1) NOT NULL DEFAULT '1',
  `vendor_deduction_manage` tinyint(1) NOT NULL DEFAULT '1',
  `reporting_ach_check` tinyint(1) NOT NULL DEFAULT '1',
  `reporting_deduction_remittance_file` tinyint(1) NOT NULL DEFAULT '1',
  `reporting_settlement_reconciliation` tinyint(1) NOT NULL DEFAULT '1',
  `reporting_general` tinyint(1) NOT NULL DEFAULT '1',
  `contractor_view` tinyint(1) NOT NULL DEFAULT '1',
  `contractor_manage` tinyint(1) NOT NULL DEFAULT '1',
  `vendor_view` tinyint(1) NOT NULL DEFAULT '1',
  `vendor_manage` tinyint(1) NOT NULL DEFAULT '1',
  `carrier_view` tinyint(1) NOT NULL DEFAULT '1',
  `carrier_manage` tinyint(1) NOT NULL DEFAULT '1',
  `template_view` tinyint(1) NOT NULL DEFAULT '1',
  `template_manage` tinyint(1) NOT NULL DEFAULT '1',
  `uploading` tinyint(1) NOT NULL DEFAULT '1',
  `contractor_vendor_auth_manage` tinyint(1) NOT NULL DEFAULT '1',
  `permissions_manage` tinyint(1) NOT NULL DEFAULT '1',
  `vendor_user_create` tinyint(1) NOT NULL DEFAULT '1',
  `contractor_user_create` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_user_permissions_user_id_idx` (`user_id`),
  CONSTRAINT `fk_user_permissions_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


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
INSERT INTO `user_role` VALUES (1,'Super admin'),(2,'Carrier'),(3,'Contractor'),(4,'Vendor'),(5,'Admin');
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
  `email` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `last_login_ip` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `last_selected_carrier` int(10) DEFAULT NULL,
  `last_selected_contractor` int(10) DEFAULT NULL,
  `receive_notifications` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `entity_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_users_role_id` (`role_id`),
  KEY `deleted` (`deleted`),
  KEY `entity_id` (`entity_id`),
  CONSTRAINT `fk_users_role_id` FOREIGN KEY (`role_id`) REFERENCES `user_role` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


INSERT INTO users (id, role_id, email, name, password, last_login_ip, last_selected_carrier, last_selected_contractor, receive_notifications, deleted, entity_id) VALUES
  (1, 1, 'daniel@nikait.co', 'Daniel Kozhemyako', '', null, 0, null, 1, 0, null),
  (2, 1, 'jake.zuanich@pfleet.com', 'Jake Zuanich', '', null, 0, null, 1, 0, null),
  (3, 1, 'qa@nikait.co', 'QA User', '', null, 0, null, 1, 0, null);




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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


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
  `priority` int(10) DEFAULT NULL,
  `correspondence_method` int(10) unsigned NOT NULL DEFAULT '1',
  `carrier_id` int(10) unsigned DEFAULT NULL,
  `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`entity_id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_vendor_entity_id` (`entity_id`),
  KEY `fk_vendor_correspondence_method` (`correspondence_method`),
  KEY `fk_vendor_carrier_id` (`carrier_id`),
  CONSTRAINT `fk_vendor_carrier_id` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_vendor_correspondence_method` FOREIGN KEY (`correspondence_method`) REFERENCES `entity_contact_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_vendor_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;


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
INSERT INTO `vendor_status` VALUES (0,'Approved'),(1,'Not Approved'),(2,'Rescinded');
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

-- Dump completed on 2015-05-18 20:09:27

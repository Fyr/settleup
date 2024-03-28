SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

USE `pfleet`;



DROP TABLE IF EXISTS `bank_account`;
CREATE TABLE IF NOT EXISTS `bank_account` (
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
  KEY `fk_bank_account_limit_type` (`limit_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=28 ;

INSERT INTO `bank_account` (`id`, `entity_id`, `account_nickname`, `payment_type`, `process`, `account_type`, `name_on_account`, `amount`, `percentage`, `priority`, `limit_type`) VALUES
(1, 1, 'Priorbank', 2, 'some process', 'Priorbank Account Type', 'Priorbank Name', NULL, '9.9900', 0, 1),
(2, 1, 'National', 1, 'National Bank process', 'National Bank Account Type', 'National Bank name', '0.0000', '15.0000', 1, 1),
(3, 19, 'Ven1AmountAccount', 1, '', '', 'Ven1AmountAccount', '200.0000', NULL, 2, 2),
(4, 19, 'Ven1PersentAccount', 1, '', '', 'Ven1PersentAccount', NULL, '50.0000', 3, 1),
(5, 20, 'Ven2AmountAccount', 1, '', '', 'Ven2AmountAccount', '200.0000', NULL, 4, 2),
(6, 20, 'Ven2PersentAccount', 1, '', '', 'Ven2AmountAccount', NULL, '50.0000', 5, 1),
(7, 16, 'CON1Account', 2, '', '', 'CON1Account', NULL, '100.0000', 6, 1),
(8, 17, 'CON2Account', 1, '', '', 'CON2Account', NULL, '100.0000', 7, 1),
(9, 18, 'CON3Account', 1, '', '', 'CON3Account', NULL, '100.0000', 8, 1),
(10, 31, 'CAR1', 1, '', '', 'CAR1 Account', '100.0000', NULL, 9, 2),
(11, 36, 'P-Fleet Reserve Bank Acct', 2, 'Reserve Account', 'Checking', 'P-Fleet', '750.0000', NULL, 1, 2),
(12, 21, 'Reserve Acct', 2, '', 'Checking', 'Test One Checking', '100.0000', NULL, 10, 2),
(13, 31, 'A Tran Bank', 2, '', 'Checking', 'A Transport', '100.0000', NULL, 11, 2),
(14, 32, 'B Tran Bank', 2, '', 'Checking', 'B Transport', '3.0000', NULL, 12, 2),
(15, 33, 'C Tran Checking', 1, '', 'Checking', 'C Transport', '100.0000', NULL, 13, 2),
(16, 34, 'D Tran', 1, '', 'Checking', 'D Transport', '88.0000', NULL, 14, 2),
(17, 21, 'Test Ony Payments', 2, '', 'Checking', 'Test One', '22.0000', NULL, 15, 2),
(18, 35, 'E Trans', 2, '', 'Checking', 'E Transport', '89.0000', NULL, 16, 2),
(19, 38, 'Settlement', 2, 'Settlement', 'Checking', 'AA Trucking', NULL, NULL, 17, 2),
(20, 39, 'AB Settlement', 1, 'Settlement', 'Checking', 'AB Trucking', NULL, NULL, 18, 2),
(21, 40, 'AC Debit Card', 3, 'Settlemetn', 'Checking', 'AC Trucking', NULL, NULL, 19, 2),
(22, 41, 'AA Leasing ACH', 2, 'Deducitons', 'Checking', 'AA Leasing', NULL, NULL, 20, 2),
(23, 42, 'AB Insurance ACH', 2, 'Deductions', 'Checking', 'AB Insurance', NULL, NULL, 21, 2),
(24, 43, 'AC Fuel Deducitons', 2, 'Deductions', 'Checking', 'AC Fuel', NULL, NULL, 22, 2),
(25, 37, 'Test Two Payment', 2, 'Payments', 'Checking', 'Test Two', NULL, NULL, 23, 2),
(27, 51, 'Reserve Acct Leasing Co X', 2, '', '', 'Leasing Co X', '2000.0000', NULL, 24, 2);

DROP TABLE IF EXISTS `bank_account_ach`;
CREATE TABLE IF NOT EXISTS `bank_account_ach` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bank_account_id` int(10) unsigned NOT NULL,
  `ACH_bank_routing_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `ACH_bank_account_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`bank_account_id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_bank_account_ach_bank_account_id` (`bank_account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

INSERT INTO `bank_account_ach` (`id`, `bank_account_id`, `ACH_bank_routing_id`, `ACH_bank_account_id`) VALUES
(1, 1, '1234', '5678'),
(2, 7, '123456', '234567'),
(3, 11, '123456789', '987654321'),
(4, 12, '123456789', '232344444'),
(5, 13, '2222222394', '223938487'),
(6, 14, '555667777', '67676447'),
(7, 17, '123454444', '12384847'),
(8, 18, '777766666', '7676888'),
(9, 19, '123456789', '67674456'),
(10, 22, '98765432', '87876545'),
(11, 23, '787878655', '12354674'),
(12, 24, '789098778', '63534262'),
(13, 25, '536356472', '444567789'),
(15, 27, '9999999', '1234567890');

DROP TABLE IF EXISTS `bank_account_cc`;
CREATE TABLE IF NOT EXISTS `bank_account_cc` (
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
  KEY `fk_bank_account_cc_bank_account_id` (`bank_account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

INSERT INTO `bank_account_cc` (`id`, `bank_account_id`, `card_number`, `name_on_card`, `CC_billing_address`, `CC_city`, `CC_state`, `CC_zip`, `expiration_date`, `cvs_code`) VALUES
(1, 21, '8787656567897654', 'AC Trucking', '4675 Main St', 'Carlsbad', 'CA', '87676', '2016-09-08', 454);

DROP TABLE IF EXISTS `bank_account_check`;
CREATE TABLE IF NOT EXISTS `bank_account_check` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bank_account_id` int(10) unsigned NOT NULL,
  `bank_name` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`bank_account_id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_bank_account_check_bank_account_id` (`bank_account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=13 ;

INSERT INTO `bank_account_check` (`id`, `bank_account_id`, `bank_name`) VALUES
(1, 2, 'National Bank'),
(2, 3, 'Priorbank'),
(3, 4, 'BPSBank'),
(4, 5, 'Priorbank'),
(5, 6, 'BPSBank'),
(7, 8, 'Priorbank'),
(8, 9, 'Priorbank'),
(9, 10, 'Priorbank'),
(10, 15, 'BofA'),
(11, 16, 'US Bank'),
(12, 20, 'US Bank');

DROP TABLE IF EXISTS `bank_account_history`;
CREATE TABLE IF NOT EXISTS `bank_account_history` (
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
  KEY `fk_bank_account_history_bank_account_id` (`bank_account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=41 ;

INSERT INTO `bank_account_history` (`id`, `bank_account_id`, `account_nickname`, `payment_type`, `process`, `ACH_bank_routing_id`, `ACH_bank_account_id`, `account_type`, `name_on_account`, `bank_name`, `card_number`, `name_on_card`, `CC_billing_address`, `CC_city`, `CC_state`, `CC_zip`, `expiration_date`, `cvs_code`, `amount`, `percentage`) VALUES
(1, 1, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 7, 'CON1Account', 2, '', '123456', '234567', '', 'CON1Account', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '100.0000'),
(3, 8, 'CON2Account', 1, '', NULL, NULL, '', 'CON2Account', 'Priorbank', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '100.0000'),
(4, 9, 'CON3Account', 1, '', NULL, NULL, '', 'CON3Account', 'Priorbank', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '100.0000'),
(5, 10, 'CAR1', 1, '', NULL, NULL, '', 'CAR1 Account', 'Priorbank', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '100.0000', NULL),
(6, 4, 'Ven1PersentAccount', 1, '', NULL, NULL, '', 'Ven1PersentAccount', 'BPSBank', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '50.0000'),
(7, 3, 'Ven1AmountAccount', 1, '', NULL, NULL, '', 'Ven1AmountAccount', 'Priorbank', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '200.0000', NULL),
(8, 6, 'Ven2PersentAccount', 1, '', NULL, NULL, '', 'Ven2AmountAccount', 'BPSBank', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '50.0000'),
(9, 5, 'Ven2AmountAccount', 1, '', NULL, NULL, '', 'Ven2AmountAccount', 'Priorbank', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '200.0000', NULL),
(10, 19, 'Settlement', 2, 'Settlement', '123456789', '67674456', 'Checking', 'AA Trucking', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 19, 'Settlement', 2, 'Settlement', '123456789', '67674456', 'Checking', 'AA Trucking', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 19, 'Settlement', 2, 'Settlement', '123456789', '67674456', 'Checking', 'AA Trucking', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 19, 'Settlement', 2, 'Settlement', '123456789', '67674456', 'Checking', 'AA Trucking', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 19, 'Settlement', 2, 'Settlement', '123456789', '67674456', 'Checking', 'AA Trucking', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 19, 'Settlement', 2, 'Settlement', '123456789', '67674456', 'Checking', 'AA Trucking', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 19, 'Settlement', 2, 'Settlement', '123456789', '67674456', 'Checking', 'AA Trucking', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 7, 'CON1Account', 2, '', '123456', '234567', '', 'CON1Account', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '100.0000'),
(18, 8, 'CON2Account', 1, '', NULL, NULL, '', 'CON2Account', 'Priorbank', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '100.0000'),
(19, 9, 'CON3Account', 1, '', NULL, NULL, '', 'CON3Account', 'Priorbank', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '100.0000'),
(20, 19, 'Settlement', 2, 'Settlement', '123456789', '67674456', 'Checking', 'AA Trucking', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 19, 'Settlement', 2, 'Settlement', '123456789', '67674456', 'Checking', 'AA Trucking', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 19, 'Settlement', 2, 'Settlement', '123456789', '67674456', 'Checking', 'AA Trucking', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 19, 'Settlement', 2, 'Settlement', '123456789', '67674456', 'Checking', 'AA Trucking', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 19, 'Settlement', 2, 'Settlement', '123456789', '67674456', 'Checking', 'AA Trucking', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 19, 'Settlement', 2, 'Settlement', '123456789', '67674456', 'Checking', 'AA Trucking', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 13, 'A Tran Bank', 2, '', '2222222394', '223938487', 'Checking', 'A Transport', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '100.0000', NULL),
(27, 10, 'CAR1', 1, '', NULL, NULL, '', 'CAR1 Account', 'Priorbank', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '100.0000', NULL),
(28, 14, 'B Tran Bank', 2, '', '555667777', '67676447', 'Checking', 'B Transport', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '3.0000', NULL),
(29, 15, 'C Tran Checking', 1, '', NULL, NULL, 'Checking', 'C Transport', 'BofA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '100.0000', NULL),
(30, 16, 'D Tran', 1, '', NULL, NULL, 'Checking', 'D Transport', 'US Bank', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '88.0000', NULL),
(31, 17, 'Test Ony Payments', 2, '', '123454444', '12384847', 'Checking', 'Test One', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '22.0000', NULL),
(32, 12, 'Reserve Acct', 2, '', '123456789', '232344444', 'Checking', 'Test One Checking', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '100.0000', NULL),
(33, 11, 'P-Fleet Reserve Bank Acct', 2, 'Reserve Account', '123456789', '987654321', 'Checking', 'P-Fleet', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '750.0000', NULL),
(34, 19, 'Settlement', 2, 'Settlement', '123456789', '67674456', 'Checking', 'AA Trucking', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 19, 'Settlement', 2, 'Settlement', '123456789', '67674456', 'Checking', 'AA Trucking', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 19, 'Settlement', 2, 'Settlement', '123456789', '67674456', 'Checking', 'AA Trucking', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 19, 'Settlement', 2, 'Settlement', '123456789', '67674456', 'Checking', 'AA Trucking', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 20, 'AB Settlement', 1, 'Settlement', NULL, NULL, 'Checking', 'AB Trucking', 'US Bank', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 21, 'AC Debit Card', 3, 'Settlemetn', NULL, NULL, 'Checking', 'AC Trucking', NULL, '8787656567897654', 'AC Trucking', '4675 Main St', 'Carlsbad', 'CA', '87676', '2016-09-08', 454, NULL, NULL),
(40, 24, 'AC Fuel Deducitons', 2, 'Deductions', '789098778', '63534262', 'Checking', 'AC Fuel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

DROP TABLE IF EXISTS `bank_account_limit_type`;
CREATE TABLE IF NOT EXISTS `bank_account_limit_type` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `bank_account_limit_type` (`id`, `title`) VALUES
(1, 'Percentage'),
(2, 'Amount');

DROP TABLE IF EXISTS `carrier`;
CREATE TABLE IF NOT EXISTS `carrier` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity_id` int(10) unsigned NOT NULL,
  `tax_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `short_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `contact` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`entity_id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_carrier_entity_id` (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=8 ;

INSERT INTO `carrier` (`id`, `entity_id`, `tax_id`, `short_code`, `name`, `contact`) VALUES
(1, 1, '123951753', 'SWI', 'Southwest Intermodal', 'Jay Abraham'),
(3, 12, '1234', 'JD', 'John Doe', NULL),
(4, 15, 'CAR1', 'CAR1', 'Carrier1', 'car1 contact'),
(5, 21, '33676776', 'TST', 'Test One', 'Jake Zuanich'),
(6, 37, '11345678', 'TS2', 'Test Two', 'John Verardo'),
(7, 44, '1299999', 'JT', 'John Test', 'John Verardo');

DROP TABLE IF EXISTS `carrier_contractor`;
CREATE TABLE IF NOT EXISTS `carrier_contractor` (
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
  KEY `fk_carrier_contractor_status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=32 ;

INSERT INTO `carrier_contractor` (`id`, `carrier_id`, `contractor_id`, `status`, `start_date`, `termination_date`, `rehire_date`) VALUES
(1, 1, 2, 1, '2012-06-07', NULL, NULL),
(2, 1, 3, 1, '2012-06-07', NULL, NULL),
(3, 12, 2, 1, '2012-06-29', NULL, NULL),
(4, 12, 3, 1, '2012-06-29', NULL, NULL),
(5, 12, 4, 1, '2012-06-29', NULL, NULL),
(6, 12, 5, 1, '2012-06-29', NULL, NULL),
(7, 12, 6, 1, '2012-06-29', NULL, NULL),
(8, 12, 7, 1, '2012-06-29', NULL, NULL),
(9, 15, 16, 1, '2012-07-11', NULL, NULL),
(10, 15, 17, 1, '2012-07-11', NULL, NULL),
(11, 15, 18, 1, '2012-07-11', NULL, NULL),
(12, 1, 8, 1, '2012-08-08', NULL, NULL),
(14, 21, 23, 1, '2012-08-08', '2012-08-14', '2012-08-14'),
(18, 15, 2, 1, '2012-08-09', NULL, NULL),
(19, 21, 31, 1, '2012-08-09', NULL, NULL),
(20, 21, 32, 1, '2012-08-09', NULL, NULL),
(21, 21, 33, 1, '2012-08-09', NULL, NULL),
(22, 21, 34, 1, '2012-08-09', NULL, NULL),
(24, 37, 38, 1, '2012-09-21', NULL, NULL),
(25, 37, 39, 1, '2012-09-21', NULL, NULL),
(26, 37, 40, 1, '2012-09-21', NULL, NULL),
(27, 44, 45, 1, '2012-09-27', NULL, NULL),
(28, 44, 46, 1, '2012-09-27', NULL, NULL),
(29, 44, 47, 1, '2012-09-27', NULL, NULL),
(30, 44, 48, 1, '2012-09-27', NULL, NULL),
(31, 44, 49, 1, '2012-09-27', NULL, NULL);

DROP TABLE IF EXISTS `carrier_vendor`;
CREATE TABLE IF NOT EXISTS `carrier_vendor` (
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
  KEY `fk_carrier_vendor_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `contractor`;
CREATE TABLE IF NOT EXISTS `contractor` (
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
  `division` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `department` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `route` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `correspondence_method` int(10) unsigned NOT NULL,
  `code` int(10) DEFAULT NULL,
  PRIMARY KEY (`entity_id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_contractor_correspondence_method` (`correspondence_method`),
  KEY `fk_contactor_entity_id` (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=33 ;

INSERT INTO `contractor` (`id`, `entity_id`, `social_security_id`, `tax_id`, `company_name`, `first_name`, `last_name`, `state_of_operation`, `dob`, `classification`, `division`, `department`, `route`, `correspondence_method`, `code`) VALUES
(1, 2, '456159753', '123956182', 'MSC Cyprus', 'Bernard', 'Arnault', 'CA', '1980-04-25', '', 'Southwest', 'Lowes', '104555', 1, NULL),
(2, 3, '987654320', '451263897', 'Navibulgar', 'David', 'Bach', 'AZ', '1970-07-25', '', 'Southwest', 'Home Depot', '334332', 1, NULL),
(3, 4, '777668888', '887874822', 'John''s Transport', 'John', 'Smith', 'WA', '1980-07-05', '', 'Northwest', 'Lowes', '887878', 1, NULL),
(4, 5, '123456789', '234567890', 'Best Delivery', 'Jim', 'Dalton', 'NV', '1980-07-05', '', 'Southwest', 'Home Depot', '334223', 1, NULL),
(5, 6, '222334444', '123334444', 'Ken''s Transport', 'Ken', 'Adams', 'FL', '1980-07-05', '', 'Southeast', 'Home Depot', '77878', 9, NULL),
(6, 7, '666558888', '348887676', 'Quick Delivery', 'John', 'Quick', 'GA', '1980-07-05', '', 'Southeast', 'Best Buy', '888788', 1, NULL),
(7, 8, '999554545', '763434555', 'Gonazales Delivery', 'Hector', 'Gonzales', 'AZ', '1980-07-05', '', 'Southwest', 'Best Buy', '776776', 1, NULL),
(8, 16, '546874290', '543969027', 'IvanovCon1', 'Ivan', 'Ivanov', 'II', '2010-01-01', '', 'Southwest', 'Lowes', '104555', 1, NULL),
(9, 17, '786874290', '543945627', 'PetrovCon2', 'Petr', 'Petrov', 'PP', '2010-01-01', '', 'Southwest', 'Lowes', '104555', 1, NULL),
(10, 18, '546898579', '54567678', 'SidorovCon3', 'Sidr', 'Sidorov', 'SS', '2010-01-01', '', 'Southwest', 'Lowes', '104555', 1, NULL),
(12, 23, '746475885', '383736363', 'ABC Transport', 'John', 'Gonzales', 'CA', '1980-07-05', '', '', '', '', 1, NULL),
(15, 26, '555660001', '9450949', 'CD Trucking', 'Carl', 'Dane', '', '1965-06-03', '', '', '', '', 1, NULL),
(16, 27, '777557777', '65656656', 'HG Trucking', 'John', 'Havner', '', '1965-06-03', '', '', '', '', 1, NULL),
(17, 28, '43543543543', '45435435', 'fdgfdgffgfd', 'gfdgdg', 'sfsfs', '', '1965-06-03', '', '', '', '', 1, NULL),
(19, 30, '444660001', '3455435', 'CBA Trucking', 'Charles', 'Bicker', 'AZ', '1965-06-03', '', 'West', 'Home Depot', 'Local', 1, NULL),
(20, 31, '111220001', '1111111', 'A Transport', 'AAA', 'BBB', '', '1965-06-03', '', '', '', '', 1, NULL),
(21, 32, '111220002', '1111112', 'B Transport', 'BBB', 'CCC', '', '1965-06-03', '', '', '', '', 1, NULL),
(22, 33, '111220003', '1111113', 'C Transport', 'CCC', 'DDD', '', '1965-06-03', '', '', '', '', 1, NULL),
(23, 34, '111220004', '1111114', 'D Transport', 'DDD', 'EEE', '', '1965-06-03', '', '', '', '', 1, NULL),
(24, 35, '111220005', '1111115', 'E Transport', 'EEE', 'FFF', '', '1965-06-03', '', '', '', '', 1, NULL),
(25, 38, '777881212', '1266565', 'AA Trucking', 'Andy', 'Ansel', 'CA', '2002-09-03', 'Contractor', 'West', 'Remote', '', 8, 1345645),
(26, 39, '888645635', '34786746', 'AB Trucking', 'Arne', 'Boyd', 'AZ', '2004-09-09', 'Contractor', 'Southwest', 'Sears', 'Phoenix', 8, 8748473),
(27, 40, '123889898', '7656456', 'AC Trucking', 'Al', 'Cooper', 'NV', '2012-05-07', 'Contractor', 'Southwest', 'Remote', 'Las Vegas', 8, 7887847),
(28, 45, '123-45-6789', '12345', 'ABC Company 1', 'John', 'Doe 1', '', '0000-00-00', '', '', '', '', 1, 1),
(29, 46, '123-45-6790', '23456', 'ABC Company 2', 'John', 'Doe 2', '', '0000-00-00', '', '', '', '', 1, 2),
(30, 47, '123-45-6791', '34567', 'ABC Company 3', 'John', 'Doe 3', '', '0000-00-00', '', '', '', '', 1, 3),
(31, 48, '123-45-6793', '45678', 'ABC Company 4', 'John', 'Doe 4', '', '0000-00-00', '', '', '', '', 1, 4),
(32, 49, '123-45-6794', '56789', 'ABC Company 5', 'John', 'Doe 5', '', '0000-00-00', '', '', '', '', 1, 5);

DROP TABLE IF EXISTS `contractor_status`;
CREATE TABLE IF NOT EXISTS `contractor_status` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `contractor_status` (`id`, `title`) VALUES
(1, 'Active'),
(2, 'Leave'),
(3, 'Terminated'),
(4, 'Not configured');

DROP TABLE IF EXISTS `cycle_date`;
CREATE TABLE IF NOT EXISTS `cycle_date` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cycle_type` int(10) unsigned NOT NULL,
  `cycle_owner` int(10) unsigned NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_cycle_date_cycle_type` (`cycle_type`),
  KEY `fk_cycle_date_cycle_owner` (`cycle_owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `cycle_period`;
CREATE TABLE IF NOT EXISTS `cycle_period` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `cycle_period` (`id`, `title`) VALUES
(1, 'Weekly'),
(2, 'Biweekly'),
(3, 'Monthly'),
(4, 'Semy-monthly');

DROP TABLE IF EXISTS `cycle_type`;
CREATE TABLE IF NOT EXISTS `cycle_type` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `cycle_type` (`id`, `title`) VALUES
(1, 'Close'),
(2, 'Disbursement');

DROP TABLE IF EXISTS `deduction_setup`;
CREATE TABLE IF NOT EXISTS `deduction_setup` (
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
  `rate` decimal(10,4) DEFAULT NULL,
  `eligible` int(11) DEFAULT NULL,
  `reserve_account_receiver` int(10) unsigned DEFAULT NULL,
  `first_start_day` int(10) DEFAULT NULL,
  `second_start_day` int(10) DEFAULT NULL,
  `deduction_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `deduction_setupcol` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_deduction_setup_level_id` (`level_id`),
  KEY `fk_deduction_setup_billing_cycle_id` (`billing_cycle_id`),
  KEY `fk_deduction_setup_provider_id` (`provider_id`),
  KEY `fk_deduction_setup_reserve_account_receiver` (`reserve_account_receiver`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

INSERT INTO `deduction_setup` (`id`, `provider_id`, `vendor_deduction_code`, `description`, `category`, `department`, `gl_code`, `disbursement_code`, `priority`, `recurring`, `level_id`, `billing_cycle_id`, `terms`, `rate`, `eligible`, `reserve_account_receiver`, `first_start_day`, `second_start_day`, `deduction_code`, `deduction_setupcol`, `quantity`) VALUES
(1, 9, 'TRL', 'Truck Lease', 'Truck', '', '3224', '', 0, 1, 1, 2, 0, '300.0000', 1, 5, NULL, NULL, NULL, NULL, 1),
(2, 9, 'FUL', 'Fuel Cards', 'Fuel', '', '423423', 'FuelCode', 1, 0, 1, 2, 0, '25.0000', 0, 5, NULL, NULL, NULL, NULL, 1),
(3, 9, 'MNT', 'Truck Мaintenance', 'Мaintenance', '', '65786798', '', 2, 1, 1, 2, 12345, '50.0000', 0, 5, NULL, NULL, NULL, NULL, 1),
(4, 15, '', 'uniforms', 'DS1', '', '', '', NULL, 0, 2, 2, 3, '150.0000', 0, NULL, NULL, NULL, NULL, NULL, 1),
(5, 19, '', 'health insurance', 'DS2', '', '', '', NULL, 1, 1, 2, 0, '300.0000', 0, 8, NULL, NULL, NULL, NULL, 1),
(6, 20, '', 'phone service', 'DS3', '', '', '', NULL, 1, 1, 2, 3, '100.0000', 0, 9, NULL, NULL, NULL, NULL, 1),
(7, 21, 'LES', 'Truck Lease', 'Truck', 'West', '7778-9898', '', NULL, 1, 1, 1, 2, '225.0000', 1, NULL, 1, 1, 'LES', NULL, 1),
(8, 21, 'MNT', 'Maintenance', 'Maintenance', 'West', '', '', NULL, 0, 1, 2, 1, '1.0000', 0, NULL, 1, 1, 'MNT', NULL, 1),
(9, 36, 'VFR', 'Fuel Invoice', '', 'West', '', '', NULL, 0, 1, 2, 2, '300.0000', 1, 16, 1, 1, 'VFR', NULL, 1),
(10, 41, 'AAL', 'Truck Lease', 'Lease', 'Test Two', '89898767', 'AALR', NULL, 1, 1, 1, 0, '150.0000', 1, 19, 1, 1, 'LS', NULL, 1),
(11, 42, 'ABI', 'Insurance', 'Insurance', 'Test Southwest', '8474633', 'ABI', NULL, 1, 1, 1, 7, '35.0000', 0, NULL, 1, 1, 'INS', NULL, 1),
(12, 43, 'FUEL', 'Fuel Invoice', 'Fuel', 'Contractor', '746733', 'ACF', NULL, 0, 1, 2, 0, '1.0000', 1, 20, 1, 1, 'ACF', NULL, 1),
(13, 50, 'Insurance', 'Insurance', 'Insurance', '', '', '', NULL, 1, 1, 1, 7, '50.0000', 0, NULL, 1, 1, '', NULL, 1),
(14, 51, 'Leasing', 'Leasing', 'Leasing', '', '', '', NULL, 0, 1, 2, 7, '100.0000', 0, NULL, 1, 1, '', NULL, 1),
(15, 52, 'Fuel', 'Fuel', 'Fuel', '', '', '', NULL, 1, 1, 1, 0, '500.0000', 0, NULL, 1, 1, '', NULL, 1);

DROP TABLE IF EXISTS `deductions`;
CREATE TABLE IF NOT EXISTS `deductions` (
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
  KEY `fk_deductions_contractor_entity_id` (`contractor_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=60 ;

INSERT INTO `deductions` (`id`, `setup_id`, `category`, `description`, `priority`, `invoice_id`, `invoice_date`, `invoice_due_date`, `department`, `gl_code`, `disbursement_code`, `rate`, `quantity`, `amount`, `disbursement_date`, `balance`, `adjusted_balance`, `approved_datetime`, `approved_by`, `created_datetime`, `created_by`, `source_id`, `status`, `settlement_cycle_id`, `contractor_id`) VALUES
(1, 1, 'Delivery', 'Delivery - Standard', 0, '', '2012-06-21', '2012-06-21', '', '423423', '', '75.0000', 1, '75.0000', '2012-06-21', '75.0000', '0.0000', NULL, NULL, '2012-06-21 23:56:17', 6, 1, 2, 1, 2),
(2, 1, 'Delivery', 'Delivery - Standard', 1, '', '2012-06-21', '2012-06-21', '', '423423', '', '75.0000', 1, '75.0000', '2012-06-21', '75.0000', '0.0000', NULL, NULL, '2012-06-21 23:56:17', 6, 1, 2, 1, 3),
(3, 2, 'Mileage', 'Mileage - Standard', 2, '', '2012-06-21', '2012-06-21', '', '4234', '', '0.9870', 15, '15.0000', '2012-06-21', '15.0000', '0.0000', NULL, NULL, '2012-06-21 23:56:56', 6, 1, 2, 1, 3),
(4, 2, 'Mileage', 'Mileage - Standard', 3, '', '2012-06-21', '2012-06-21', '', '4234', '', '1.2000', 60, '72.0000', '2012-06-21', '72.0000', '0.0000', NULL, NULL, '2012-06-21 23:57:16', 6, 1, 2, 1, 3),
(5, 2, 'Mileage', 'Mileage - Standard', 4, '', '2012-06-21', '2012-06-21', '', '4234', '', '0.9870', 45, '44.0000', '2012-06-21', '44.0000', '0.0000', NULL, NULL, '2012-06-21 23:57:36', 6, 1, 2, 1, 3),
(6, 3, 'Bonus', 'Bonus - Standard', 5, '', '2012-06-21', '2046-04-09', '', '67589', '', '100.0000', 1, '100.0000', '2012-06-21', '100.0000', '0.0000', NULL, NULL, '2012-06-21 23:58:03', 6, 1, 2, 1, 2),
(7, 3, 'Bonus', 'Bonus - Standard', 6, '', '2012-06-21', '2046-04-09', '', '67589', '', '100.0000', 1, '100.0000', '2012-06-21', '100.0000', '0.0000', NULL, NULL, '2012-06-21 23:58:03', 6, 1, 2, 1, 3),
(8, 3, 'Bonus', 'Bonus - Standard', 7, '', '2012-06-21', '2046-04-09', '', '67589', '', '100.0000', 1, '100.0000', '2012-06-21', '100.0000', '0.0000', NULL, NULL, '2012-06-21 23:58:38', 6, 1, 2, 1, 2),
(9, 3, 'Bonus', 'Bonus - Standard', 8, '', '2012-06-21', '2046-04-09', '', '67589', '', '100.0000', 1, '100.0000', '2012-06-21', '100.0000', '0.0000', NULL, NULL, '2012-06-21 23:58:38', 6, 1, 2, 1, 3),
(10, 1, 'Delivery', 'Delivery - Standard', 9, '', '2012-06-21', '2012-06-21', '', '423423', '', '75.0000', 3, '225.0000', '2012-06-21', '225.0000', '0.0000', NULL, NULL, '2012-06-21 23:59:05', 6, 1, 2, 1, 2),
(11, 1, 'Delivery', 'Delivery - Standard', 10, '', '2012-06-21', '2012-06-21', '', '423423', '', '75.0000', 3, '225.0000', '2012-06-21', '225.0000', '0.0000', NULL, NULL, '2012-06-21 23:59:05', 6, 1, 2, 1, 3),
(12, 2, 'Mileage', 'Mileage - Standard', 11, '', '2012-06-21', '2012-06-21', '', '4234', '', '0.9870', 60, '59.0000', '2012-06-21', '59.0000', '0.0000', NULL, NULL, '2012-06-21 23:59:25', 6, 1, 2, 1, 3),
(13, 2, 'Mileage', 'Mileage - Standard', 12, '', '2012-06-21', '2012-06-21', '', '4234', '', '0.9870', 120, '118.0000', '2012-06-21', '118.0000', '0.0000', NULL, NULL, '2012-06-21 23:59:42', 6, 1, 2, 1, 3),
(14, 2, 'Mileage', 'Mileage - Standard', 13, '', '2012-06-21', '2012-06-21', '', '4234', '', '0.9870', 20, '20.0000', '2012-06-21', '20.0000', '0.0000', NULL, NULL, '2012-06-21 23:59:58', 6, 1, 2, 1, 3),
(15, 1, 'Delivery', 'Delivery - Standard', 14, '', '2012-06-22', '2012-06-22', '', '423423', '', '75.0000', 1, '75.0000', '2012-06-22', '75.0000', '0.0000', NULL, NULL, '2012-06-22 00:00:15', 6, 1, 2, 1, 2),
(16, 4, 'DS1', 'uniforms', 15, '', '2012-07-13', '2012-07-16', '', '', '', '150.0000', 2, '300.0000', '2012-07-22', '300.0000', '150.0000', '2012-08-03 16:57:15', 3, '2012-07-13 09:13:34', 6, NULL, 3, 2, 16),
(17, 5, 'DS2', 'health insurance', 16, '', '2012-07-13', '2012-07-13', '', '', '', '300.0000', 5, '1500.0000', '2012-07-22', '1500.0000', '0.0000', '2012-08-03 16:57:15', 3, '2012-07-13 09:14:47', 6, NULL, 3, 2, 16),
(18, 5, 'DS2', 'health insurance', 17, '', '2012-07-13', '2012-07-13', '', '', '', '300.0000', 1, '300.0000', '2012-07-22', '300.0000', '0.0000', '2012-08-03 16:57:15', 3, '2012-07-13 09:14:47', 6, NULL, 3, 2, 17),
(19, 5, 'DS2', 'health insurance', 18, '', '2012-07-13', '2012-07-13', '', '', '', '300.0000', 1, '300.0000', '2012-07-22', '300.0000', '0.0000', '2012-08-03 16:57:15', 3, '2012-07-13 09:14:47', 6, NULL, 3, 2, 18),
(20, 6, 'DS3', 'phone service', 19, '', '2012-07-13', '2012-07-16', '', '', '', '100.0000', 2, '200.0000', '2012-07-22', '200.0000', '0.0000', '2012-08-03 16:57:15', 3, '2012-07-13 09:16:45', 6, NULL, 3, 2, 16),
(21, 6, 'DS3', 'phone service', 20, '', '2012-07-13', '2012-07-16', '', '', '', '100.0000', 1, '100.0000', '2012-07-22', '100.0000', '0.0000', '2012-08-03 16:57:15', 3, '2012-07-13 09:16:45', 6, NULL, 3, 2, 17),
(22, 6, 'DS3', 'phone service', 21, '', '2012-07-13', '2012-07-16', '', '', '', '100.0000', 2, '200.0000', '2012-07-22', '200.0000', '0.0000', '2012-08-03 16:57:15', 3, '2012-07-13 09:16:45', 6, NULL, 3, 2, 18),
(23, 7, 'Truck', 'Truck Lease', 0, '', '2012-08-10', '2012-08-12', 'West', '7778-9898', '', '225.0000', 1, '225.0000', '2012-08-18', '225.0000', '0.0000', '2012-08-29 18:34:32', 5, '2012-08-10 00:14:29', 5, NULL, 3, 4, 23),
(24, 7, 'Truck', 'Truck Lease', 3, '', '2012-08-10', '2012-08-12', 'West', '7778-9898', '', '225.0000', 1, '225.0000', '2012-08-18', '225.0000', '0.0000', '2012-08-29 18:34:32', 5, '2012-08-10 00:14:29', 5, NULL, 3, 4, 31),
(25, 7, 'Truck', 'Truck Lease', 2, '', '2012-08-10', '2012-08-12', 'West', '7778-9898', '', '225.0000', 1, '225.0000', '2012-08-18', '225.0000', '0.0000', '2012-08-29 18:34:32', 5, '2012-08-10 00:14:29', 5, NULL, 3, 4, 32),
(26, 7, 'Truck', 'Truck Lease', 1, '', '2012-08-10', '2012-08-12', 'West', '7778-9898', '', '225.0000', 1, '225.0000', '2012-08-18', '225.0000', '0.0000', '2012-08-29 18:34:32', 5, '2012-08-10 00:14:29', 5, NULL, 3, 4, 33),
(27, 7, 'Truck', 'Truck Lease', 4, '', '2012-08-10', '2012-08-12', 'West', '7778-9898', '', '225.0000', 1, '225.0000', '2012-08-18', '225.0000', '0.0000', '2012-08-29 18:34:32', 5, '2012-08-10 00:14:29', 5, NULL, 3, 4, 34),
(28, 9, '', 'Fuel Invoice', 5, '7678s', '2012-08-10', '2012-08-12', 'West', '', '', '300.0000', 1, '300.0000', '2012-08-18', '300.0000', '0.0000', '2012-08-29 18:34:32', 5, '2012-08-10 20:49:12', 19, NULL, 3, 4, 23),
(29, 9, '', 'Fuel Invoice', 6, '7678s', '2012-08-10', '2012-08-12', 'West', '', '', '300.0000', 1, '300.0000', '2012-08-18', '300.0000', '0.0000', '2012-08-29 18:34:32', 5, '2012-08-10 20:49:12', 19, NULL, 3, 4, 31),
(30, 9, '', 'Fuel Invoice', 7, '7678s', '2012-08-10', '2012-08-12', 'West', '', '', '300.0000', 1, '300.0000', '2012-08-18', '300.0000', '0.0000', '2012-08-29 18:34:33', 5, '2012-08-10 20:49:12', 19, NULL, 3, 4, 32),
(31, 9, '', 'Fuel Invoice', 8, '7678s', '2012-08-10', '2012-08-12', 'West', '', '', '300.0000', 1, '300.0000', '2012-08-18', '300.0000', '0.0000', '2012-08-29 18:34:33', 5, '2012-08-10 20:49:12', 19, NULL, 3, 4, 33),
(32, 9, '', 'Fuel Invoice', 9, '7678s', '2012-08-10', '2012-08-12', 'West', '', '', '300.0000', 1, '300.0000', '2012-08-18', '300.0000', '0.0000', '2012-08-29 18:34:33', 5, '2012-08-10 20:49:12', 19, NULL, 3, 4, 34),
(33, 12, 'Fuel', 'Fuel Invoice', 4, '', '2012-09-21', '2012-09-21', 'Contractor', '746733', 'ACF', '456.5600', 1, '456.5600', '2012-09-20', '456.5600', '0.0000', '2012-09-25 18:15:17', 5, '2012-09-21 19:52:17', 17, NULL, 3, 8, 38),
(34, 12, 'Fuel', 'Fuel Invoice', 10, '', '2012-09-21', '2012-09-21', 'Contractor', '746733', 'ACF', '1167.0000', 1, '1167.0000', '2012-09-20', '1167.0000', '0.0000', '2012-09-25 18:15:17', 5, '2012-09-21 19:52:17', 17, NULL, 3, 8, 39),
(35, 12, 'Fuel', 'Fuel Invoice', 6, '', '2012-09-21', '2012-09-21', 'Contractor', '746733', 'ACF', '634.1100', 1, '634.1100', '2012-09-20', '634.1100', '0.0000', '2012-09-25 18:15:17', 5, '2012-09-21 19:52:17', 17, NULL, 3, 8, 40),
(36, 10, 'Lease', 'Truck Lease', 4, NULL, '2012-09-26', '2012-09-26', 'Test Two', '89898767', 'AALR', '150.0000', 1, '150.0000', '2012-09-20', '150.0000', NULL, '2012-10-02 20:09:13', 5, '2012-09-26 21:12:50', 17, NULL, 3, 9, 38),
(37, 10, 'Lease', 'Truck Lease', 5, NULL, '2012-09-26', '2012-09-26', 'Test Two', '89898767', 'AALR', '150.0000', 1, '150.0000', '2012-09-20', '150.0000', NULL, '2012-10-02 20:09:13', 5, '2012-09-26 21:12:50', 17, NULL, 3, 9, 39),
(38, 10, 'Lease', 'Truck Lease', 6, NULL, '2012-09-26', '2012-09-26', 'Test Two', '89898767', 'AALR', '150.0000', 1, '150.0000', '2012-09-20', '150.0000', NULL, '2012-10-02 20:09:13', 5, '2012-09-26 21:12:50', 17, NULL, 3, 9, 40),
(39, 11, 'Insurance', 'Insurance', 1, NULL, '2012-09-26', '2012-09-26', 'Test Southwest', '8474633', 'ABI', '35.0000', 1, '35.0000', '2012-09-20', '35.0000', NULL, '2012-10-02 20:09:13', 5, '2012-09-26 21:12:50', 17, NULL, 3, 9, 38),
(40, 11, 'Insurance', 'Insurance', 2, NULL, '2012-09-26', '2012-09-26', 'Test Southwest', '8474633', 'ABI', '35.0000', 1, '35.0000', '2012-09-20', '35.0000', NULL, '2012-10-02 20:09:13', 5, '2012-09-26 21:12:50', 17, NULL, 3, 9, 39),
(41, 11, 'Insurance', 'Insurance', 3, NULL, '2012-09-26', '2012-09-26', 'Test Southwest', '8474633', 'ABI', '35.0000', 1, '35.0000', '2012-09-20', '35.0000', NULL, '2012-10-02 20:09:13', 5, '2012-09-26 21:12:50', 17, NULL, 3, 9, 40),
(42, 12, 'Fuel', 'Fuel Invoice', 14, '', '2012-09-26', '2012-09-26', 'Contractor', '746733', 'ACF', '345.0000', 1, '345.0000', '2012-09-20', '345.0000', '0.0000', '2012-10-02 20:09:14', 5, '2012-09-26 21:12:50', 17, NULL, 3, 9, 38),
(43, 12, 'Fuel', 'Fuel Invoice', 16, '', '2012-09-26', '2012-09-26', 'Contractor', '746733', 'ACF', '766.4600', 1, '766.4600', '2012-09-20', '766.4600', '0.0000', '2012-09-26 21:22:36', 17, '2012-09-26 21:12:50', 17, NULL, 2, 9, 39),
(44, 12, 'Fuel', 'Fuel Invoice', 17, '', '2012-09-26', '2012-09-26', 'Contractor', '746733', 'ACF', '1356.5600', 1, '1356.5600', '2012-09-20', '1356.5600', '0.0000', '2012-09-26 21:22:36', 17, '2012-09-26 21:12:50', 17, NULL, 2, 9, 40),
(45, 13, 'Insurance', 'Insurance', 21, NULL, '2012-09-28', '2012-10-05', '', '', '', '50.0000', 1, '50.0000', '2012-09-27', '50.0000', NULL, NULL, NULL, '2012-09-28 01:01:48', 20, NULL, 2, 14, 45),
(46, 13, 'Insurance', 'Insurance', 22, NULL, '2012-09-28', '2012-10-05', '', '', '', '50.0000', 1, '50.0000', '2012-09-27', '50.0000', NULL, NULL, NULL, '2012-09-28 01:01:48', 20, NULL, 2, 14, 46),
(47, 13, 'Insurance', 'Insurance', 23, NULL, '2012-09-28', '2012-10-05', '', '', '', '50.0000', 1, '50.0000', '2012-09-27', '50.0000', NULL, NULL, NULL, '2012-09-28 01:01:48', 20, NULL, 2, 14, 47),
(48, 13, 'Insurance', 'Insurance', 24, NULL, '2012-09-28', '2012-10-05', '', '', '', '50.0000', 1, '50.0000', '2012-09-27', '50.0000', NULL, NULL, NULL, '2012-09-28 01:01:48', 20, NULL, 2, 14, 48),
(49, 13, 'Insurance', 'Insurance', 25, NULL, '2012-09-28', '2012-10-05', '', '', '', '50.0000', 1, '50.0000', '2012-09-27', '50.0000', NULL, NULL, NULL, '2012-09-28 01:01:48', 20, NULL, 2, 14, 49),
(50, 14, 'Leasing', 'Leasing', 19, '', '2012-09-28', '2012-10-05', '', '', '', '100.0000', 4, '400.0000', '2012-09-27', '400.0000', '0.0000', '2012-09-28 01:43:43', 20, '2012-09-28 01:01:48', 20, NULL, 2, 14, 45),
(51, 14, 'Leasing', 'Leasing', 16, NULL, '2012-09-28', '2012-10-05', '', '', '', '100.0000', 1, '100.0000', '2012-09-27', '100.0000', NULL, '2012-09-28 01:43:43', 20, '2012-09-28 01:01:48', 20, NULL, 2, 14, 46),
(52, 14, 'Leasing', 'Leasing', 17, NULL, '2012-09-28', '2012-10-05', '', '', '', '100.0000', 1, '100.0000', '2012-09-27', '100.0000', NULL, '2012-09-28 01:43:43', 20, '2012-09-28 01:01:48', 20, NULL, 2, 14, 47),
(53, 14, 'Leasing', 'Leasing', 18, NULL, '2012-09-28', '2012-10-05', '', '', '', '100.0000', 1, '100.0000', '2012-09-27', '100.0000', NULL, '2012-09-28 01:43:43', 20, '2012-09-28 01:01:48', 20, NULL, 2, 14, 48),
(54, 14, 'Leasing', 'Leasing', 20, '', '2012-09-28', '2012-10-05', '', '', '', '100.0000', 3, '300.0000', '2012-09-27', '300.0000', '0.0000', '2012-09-28 01:43:43', 20, '2012-09-28 01:01:48', 20, NULL, 2, 14, 49),
(55, 15, 'Fuel', 'Fuel', 7, '', '2012-09-28', '2012-10-05', '', '', '', '4000.0000', 1, '4000.0000', '2012-09-27', '4000.0000', '0.0000', '2012-09-28 01:43:43', 20, '2012-09-28 01:01:48', 20, NULL, 2, 14, 45),
(56, 15, 'Fuel', 'Fuel', 10, '', '2012-09-28', '2012-10-05', '', '', '', '3200.0000', 1, '3200.0000', '2012-09-27', '3200.0000', '0.0000', '2012-09-28 01:43:43', 20, '2012-09-28 01:01:48', 20, NULL, 2, 14, 46),
(57, 15, 'Fuel', 'Fuel', 11, '', '2012-09-28', '2012-10-05', '', '', '', '2200.0000', 1, '2200.0000', '2012-09-27', '2200.0000', '0.0000', '2012-09-28 01:43:43', 20, '2012-09-28 01:01:48', 20, NULL, 2, 14, 47),
(58, 15, 'Fuel', 'Fuel', 12, '', '2012-09-28', '2012-10-05', '', '', '', '1000.0000', 1, '1000.0000', '2012-09-27', '1000.0000', '0.0000', '2012-09-28 01:43:43', 20, '2012-09-28 01:01:48', 20, NULL, 2, 14, 48),
(59, 15, 'Fuel', 'Fuel', 15, '', '2012-09-28', '2012-10-05', '', '', '', '1500.0000', 1, '1500.0000', '2012-09-27', '1500.0000', '0.0000', '2012-09-28 01:43:43', 20, '2012-09-28 01:01:48', 20, NULL, 2, 14, 49);

DROP TABLE IF EXISTS `deductions_temp`;
CREATE TABLE IF NOT EXISTS `deductions_temp` (
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
  KEY `payment_temp_status_id` (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `disbursement_check`;
CREATE TABLE IF NOT EXISTS `disbursement_check` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `disburstment_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_disburstment_check_disburstment_id` (`disburstment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

INSERT INTO `disbursement_check` (`id`, `disburstment_id`) VALUES
(1, 1);

DROP TABLE IF EXISTS `disbursement_transaction`;
CREATE TABLE IF NOT EXISTS `disbursement_transaction` (
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
  `sender_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_disbursement_created_by` (`created_by`),
  KEY `fk_disbursement_approved_by` (`approved_by`),
  KEY `fk_disbursement_status` (`status`),
  KEY `fk_disbursement_entity_id` (`entity_id`),
  KEY `fk_disbursement_process_type` (`process_type`),
  KEY `fk_disbursement_bank_account_history_id` (`bank_account_history_id`),
  KEY `fk_disburstment_settlement_cycle_id` (`settlement_cycle_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=36 ;

INSERT INTO `disbursement_transaction` (`id`, `bank_account_history_id`, `entity_id`, `process_type`, `code`, `description`, `disbursement_code`, `amount`, `status`, `settlement_cycle_close_date`, `created_datetime`, `created_by`, `approved_by`, `approved_datetime`, `settlement_cycle_id`, `sender_name`) VALUES
(1, 2, 16, 1, '', NULL, '', '1150.0000', 1, '0000-00-00', '2012-08-03 16:57:32', 3, NULL, NULL, 2, NULL),
(2, 3, 17, 1, NULL, NULL, NULL, '1360.0000', 1, NULL, '2012-08-03 16:57:32', 3, NULL, NULL, 2, NULL),
(3, 5, 15, 2, NULL, NULL, NULL, '300.0000', 1, NULL, '2012-08-03 16:57:33', 3, NULL, NULL, 2, NULL),
(4, 6, 19, 2, NULL, NULL, NULL, '1050.0000', 1, NULL, '2012-08-03 16:57:33', 3, NULL, NULL, 2, NULL),
(5, 7, 19, 2, NULL, NULL, NULL, '1050.0000', 1, NULL, '2012-08-03 16:57:33', 3, NULL, NULL, 2, NULL),
(6, 8, 20, 2, NULL, NULL, NULL, '250.0000', 1, NULL, '2012-08-03 16:57:34', 3, NULL, NULL, 2, NULL),
(7, 9, 20, 2, NULL, NULL, NULL, '250.0000', 1, NULL, '2012-08-03 16:57:34', 3, NULL, NULL, 2, NULL),
(15, 17, 16, 1, NULL, NULL, NULL, '3170.0000', 1, NULL, '2012-09-25 12:26:16', 1, NULL, NULL, 3, NULL),
(22, 26, 31, 1, NULL, NULL, NULL, '100.0000', 1, NULL, '2012-09-26 09:23:55', 6, NULL, NULL, 4, NULL),
(23, 27, 31, 1, NULL, NULL, NULL, '730.0000', 1, NULL, '2012-09-26 09:23:55', 6, NULL, NULL, 4, NULL),
(24, 28, 32, 1, NULL, NULL, NULL, '200.0000', 1, NULL, '2012-09-26 09:23:56', 6, NULL, NULL, 4, NULL),
(25, 29, 33, 1, NULL, NULL, NULL, '470.0000', 1, NULL, '2012-09-26 09:23:56', 6, NULL, NULL, 4, NULL),
(26, 30, 34, 1, NULL, NULL, NULL, '200.0000', 1, NULL, '2012-09-26 09:23:56', 6, NULL, NULL, 4, NULL),
(27, 31, 21, 2, NULL, NULL, NULL, '22.0000', 1, NULL, '2012-09-26 09:23:56', 6, NULL, NULL, 4, NULL),
(28, 32, 21, 2, NULL, NULL, NULL, '1103.0000', 1, NULL, '2012-09-26 09:23:56', 6, NULL, NULL, 4, NULL),
(29, 33, 36, 2, NULL, NULL, NULL, '1500.0000', 1, NULL, '2012-09-26 09:23:56', 6, NULL, NULL, 4, NULL),
(33, 37, 38, 1, '', NULL, '', '961.4400', 1, '0000-00-00', '2012-09-26 15:36:27', 1, NULL, NULL, 8, 'AA Trucking'),
(34, 39, 40, 1, '', NULL, '', '215.8900', 1, '0000-00-00', '2012-09-26 15:36:27', 1, NULL, NULL, 8, 'AC Trucking'),
(35, 40, 43, 2, '', NULL, '', '2257.6700', 1, '0000-00-00', '2012-09-26 15:36:27', 1, NULL, NULL, 8, 'AC Fuel');

DROP TABLE IF EXISTS `disbursement_transaction_type`;
CREATE TABLE IF NOT EXISTS `disbursement_transaction_type` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `disbursement_transaction_type` (`id`, `title`) VALUES
(1, 'Payment'),
(2, 'Deduction');

DROP TABLE IF EXISTS `entity`;
CREATE TABLE IF NOT EXISTS `entity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_entity_entity_type_id` (`entity_type_id`),
  KEY `fk_entity_user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=53 ;

INSERT INTO `entity` (`id`, `entity_type_id`, `user_id`) VALUES
(1, 1, 18),
(2, 2, 3),
(3, 2, 3),
(4, 2, 3),
(5, 2, 3),
(6, 2, 3),
(7, 2, 3),
(8, 2, 3),
(9, 3, 3),
(10, 3, 3),
(12, 1, 7),
(13, 2, 8),
(14, 3, 9),
(15, 1, 10),
(16, 2, 11),
(17, 2, 12),
(18, 2, 13),
(19, 3, 14),
(20, 3, 15),
(21, 1, NULL),
(22, 2, NULL),
(23, 2, NULL),
(24, 2, NULL),
(25, 2, NULL),
(26, 2, 5),
(27, 2, 5),
(28, 2, 3),
(29, 2, 18),
(30, 2, 18),
(31, 2, 18),
(32, 2, 18),
(33, 2, 18),
(34, 2, 18),
(35, 2, 18),
(36, 3, NULL),
(37, 1, 17),
(38, 2, 19),
(39, 2, 17),
(40, 2, 17),
(41, 3, 17),
(42, 3, 18),
(43, 3, 22),
(44, 1, 20),
(45, 2, 20),
(46, 2, 20),
(47, 2, 20),
(48, 2, 20),
(49, 2, 20),
(50, 3, 20),
(51, 3, 20),
(52, 3, 20);

DROP TABLE IF EXISTS `entity_contact_info`;
CREATE TABLE IF NOT EXISTS `entity_contact_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contact_type` int(10) unsigned NOT NULL,
  `value` varchar(255) COLLATE utf8_bin NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_user_contact_info_contact_type` (`contact_type`),
  KEY `fk_entity_contact_info_entity_id` (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=49 ;

INSERT INTO `entity_contact_info` (`id`, `contact_type`, `value`, `entity_id`) VALUES
(1, 7, '+1 888 253 5696', 1),
(2, 1, '14 Fatin str. #170', 2),
(3, 7, '+375 29 1643762', 3),
(4, 1, '56 Main str. #560', 4),
(5, 1, '56 Main str. #559', 5),
(6, 1, '56 Main str. #545', 7),
(7, 1, '56 Main str. #540', 8),
(8, 1, 'fatin 3-38', 16),
(9, 1, 'fatin 2-139', 16),
(10, 2, 'Mogilev', 16),
(11, 4, '212038', 16),
(12, 3, 'Mgl', 16),
(14, 1, '123 Main St.', 21),
(15, 2, 'San Diego', 21),
(16, 8, 'jake.zuanich@pfleet.com', 21),
(17, 9, '858.234.2596', 21),
(18, 5, '(800)499-4645', 21),
(19, 3, 'CA', 21),
(20, 4, '92122', 21),
(21, 1, '443 A St', 24),
(22, 2, 'San Diego', 24),
(23, 3, 'CA', 24),
(24, 4, '92122', 24),
(25, 8, 'jimd@hotmail.com', 24),
(26, 7, '8004994645', 24),
(27, 1, '623 Greenwich', 37),
(28, 2, 'San Diego', 37),
(29, 3, 'CA', 37),
(30, 4, '92122', 37),
(31, 8, 'john.verardo@pfleet.com', 37),
(32, 1, '12432 Mesa Dr', 39),
(33, 2, 'Phoenix', 39),
(34, 3, 'AZ', 39),
(35, 4, '86764', 39),
(36, 8, 'boyd@aol.com', 39),
(37, 1, '656 Elm St', 40),
(38, 2, 'Las Vegas', 40),
(39, 3, 'NV', 40),
(40, 4, '76765', 40),
(41, 1, '567 Pine Ave', 41),
(42, 2, 'Gary', 41),
(43, 3, 'IN', 41),
(44, 4, '34543', 41),
(45, 8, 'john@aaleasing.com', 41),
(46, 6, '8005656789', 41),
(47, 6, '8776567878', 42),
(48, 8, 'jim@acfuel.com', 43);

DROP TABLE IF EXISTS `entity_contact_type`;
CREATE TABLE IF NOT EXISTS `entity_contact_type` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `entity_contact_type` (`id`, `title`) VALUES
(1, 'Address'),
(2, 'City'),
(3, 'State'),
(4, 'Zip'),
(5, 'Home Phone'),
(6, 'Office Phone'),
(7, 'Mobile Phone'),
(8, 'Email'),
(9, 'Fax');

DROP TABLE IF EXISTS `entity_type`;
CREATE TABLE IF NOT EXISTS `entity_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

INSERT INTO `entity_type` (`id`, `title`) VALUES
(1, 'carrier'),
(2, 'contractor'),
(3, 'vendor');

DROP TABLE IF EXISTS `file_storage`;
CREATE TABLE IF NOT EXISTS `file_storage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `source_link` text COLLATE utf8_bin NOT NULL,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `file_type` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_file_storage_file_type` (`file_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=6 ;

INSERT INTO `file_storage` (`id`, `source_link`, `title`, `uploaded_by`, `file_type`) VALUES
(1, 'dsdas', 'Some title', 3, 0),
(2, '1342535801_payments-import-file.xls', 'TestXlsFile', 6, 1),
(3, '1344002634_payments-import-file.xls', 'gsdfgdf', 3, 1),
(4, '1344964517_Payment_Import_Test_8-14-12.xls', 'Payment Import 8-14-12', 5, 1),
(5, '1344964561_Payment_Import_Test_8-14-12.xls', 'Payment Import 8-14-12', 5, 1);

DROP TABLE IF EXISTS `file_storage_type`;
CREATE TABLE IF NOT EXISTS `file_storage_type` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `file_storage_type` (`id`, `title`) VALUES
(1, 'Payments'),
(2, 'Deductions');

DROP TABLE IF EXISTS `payment_setup`;
CREATE TABLE IF NOT EXISTS `payment_setup` (
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
  `rate` decimal(10,4) DEFAULT NULL,
  `first_start_day` int(10) DEFAULT NULL,
  `second_start_day` int(10) DEFAULT NULL,
  `quantity` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_payment_setup_level_id` (`level_id`),
  KEY `fk_payment_setup_billing_cycle_id` (`billing_cycle_id`),
  KEY `fk_payment_setup_carrier_id` (`carrier_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=25 ;

INSERT INTO `payment_setup` (`id`, `carrier_id`, `payment_code`, `carrier_payment_code`, `description`, `category`, `terms`, `department`, `gl_code`, `disbursement_code`, `recurring`, `level_id`, `billing_cycle_id`, `rate`, `first_start_day`, `second_start_day`, `quantity`) VALUES
(1, 1, 'Delivery', 'Delivery', 'Delivery - Standard', 'Delivery', 0, '', '423423', '', 1, 1, 2, '75.0000', NULL, NULL, '1'),
(2, 1, 'Mileage', 'Mileage Std', 'Mileage - Standard', 'Mileage', 0, '', '4234', '', 0, 2, 1, '0.9870', NULL, NULL, '1'),
(3, 1, 'Bonus', 'Bonus', 'Bonus - Standard', 'Bonus', 1, '', '67589', '', 1, 1, 3, '100.0000', NULL, NULL, '1'),
(4, 1, 'Waiting', 'Waiting', 'Waiting - Standard', 'Waiting', 0, '', '4564567', '', 1, 1, 3, '20.0000', NULL, NULL, '1'),
(5, 15, '', '', 'hourly', 'PS1', 0, '', '', '', 1, 1, 1, '20.0000', NULL, NULL, '1'),
(6, 15, '', '', 'overtime', 'PS2', 0, '', '', '', 0, 2, 2, '30.0000', NULL, NULL, '1'),
(7, 15, '', '', 'commission', 'PS3', 14, '', '', '', 0, 2, 2, '300.0000', NULL, NULL, '1'),
(8, 21, 'DEL', 'Deliver', 'Home Delivery', '', 1, 'West', '55600-43544', '', 0, 1, 2, '75.0000', NULL, NULL, '1'),
(9, 21, 'RET', 'RET', 'Home Return', '', 2, 'West', '', '', 0, 1, 2, '85.0000', NULL, NULL, '1'),
(10, 21, 'RETS', 'RETS', 'Return Special', '', 2, '', '', '', 0, 2, 2, '90.0000', NULL, NULL, '1'),
(11, 21, 'MIL', 'MIL', 'Mileage Reimbursement', '', 1, '', '', '', 1, 1, 1, '0.5600', NULL, NULL, '1'),
(13, 21, 'LDD', 'LDD', 'Long Distance Deliver', '', 1, 'Southwest', '', '', 0, 2, 2, '135.0000', NULL, NULL, '1'),
(15, 37, 'HD', 'CHD', 'Home Delivery', 'Delivery', 7, 'Delivery', '6767-90988', 'CHD', 0, 1, 2, '85.0000', NULL, NULL, '1'),
(16, 37, 'HR', 'CHR', 'Home Return', 'Return', 7, 'Delivery', '6767-90977', '', 0, 1, 2, '75.0000', NULL, NULL, '1'),
(17, 37, 'BR', 'CBR', 'Business Return', 'Return', 0, 'Delivery', '', '', 0, 1, 2, '80.0000', NULL, NULL, '1'),
(18, 37, 'BD', 'CBD', 'Business Delivery', 'Delivery', 0, 'Delivery', '', '', 0, 1, 2, '95.0000', NULL, NULL, '1'),
(19, 37, 'ML', 'CML', 'Mileage', 'Mileage', 0, 'Expense', '', '', 0, 1, 2, '0.7000', NULL, NULL, '1'),
(20, 37, 'HDAA', 'CHDAA', 'Home Delivery AA', 'Delivery', 7, 'Delivery', '', '', 0, 2, 2, '100.0000', NULL, NULL, '1'),
(21, 37, 'BN', 'CBN', 'Bonus', 'Additional', 7, 'Additional', '', '', 1, 1, 1, '200.0000', NULL, NULL, '1'),
(22, 44, 'Mileage', '', 'Mileage', '', 0, '', '', '', 0, 1, 2, '0.3500', NULL, NULL, '500'),
(23, 44, 'Wage', '', 'Wage', '', 0, '', '', '', 1, 1, 1, '900.0000', NULL, NULL, '1'),
(24, 44, 'Delivery', '', 'Delivery', '', 0, '', '', '', 0, 1, 2, '50.0000', NULL, NULL, '30');

DROP TABLE IF EXISTS `payment_status`;
CREATE TABLE IF NOT EXISTS `payment_status` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `payment_status` (`id`, `title`) VALUES
(1, 'Verified'),
(2, 'Processed'),
(3, 'Approved');

DROP TABLE IF EXISTS `payment_temp_status`;
CREATE TABLE IF NOT EXISTS `payment_temp_status` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `payment_temp_status` (`id`, `title`) VALUES
(1, 'Valid'),
(2, 'Not Valid');

DROP TABLE IF EXISTS `payment_type`;
CREATE TABLE IF NOT EXISTS `payment_type` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `payment_type` (`id`, `title`) VALUES
(1, 'Check'),
(2, 'ACH'),
(3, 'Debit Card');

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
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
  KEY `fk_payments_contractor_entity_id` (`contractor_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=123 ;

INSERT INTO `payments` (`id`, `setup_id`, `category`, `description`, `invoice`, `invoice_date`, `invoice_due_date`, `department`, `gl_code`, `quantity`, `rate`, `amount`, `check_id`, `disbursement_date`, `approved_datetime`, `approved_by`, `created_datetime`, `created_by`, `source_id`, `status`, `settlement_cycle_id`, `contractor_id`, `balance`) VALUES
(1, 1, 'Delivery', 'Delivery - Standard', '', '2012-06-21', '2012-06-21', '', '423423', 1, '75.0000', '75.0000', '', '2012-06-21', NULL, NULL, '2012-06-21 23:23:10', 6, 1, 2, 1, 2, '75.0000'),
(2, 1, 'Delivery', 'Delivery - Standard', '', '2012-06-21', '2012-06-21', '', '423423', 1, '75.0000', '75.0000', '', '2012-06-21', NULL, NULL, '2012-06-21 23:23:10', 6, 1, 2, 1, 3, '75.0000'),
(3, 2, 'Mileage', 'Mileage - Standard', '', '2012-06-21', '2012-06-21', '', '4234', 250, '0.9870', '246.7500', '', '2012-06-21', NULL, NULL, '2012-06-21 23:24:23', 6, 1, 2, 1, 3, '246.7500'),
(4, 2, 'Mileage', 'Mileage - Standard', '', '2012-06-21', '2012-06-21', '', '4234', 400, '0.9870', '394.8000', '', '2012-06-21', NULL, NULL, '2012-06-21 23:24:53', 6, 1, 2, 1, 3, '394.8000'),
(5, 2, 'Mileage', 'Mileage - Standard', '', '2012-06-21', '2012-06-21', '', '4234', 40, '0.9870', '39.4800', '', '2012-06-21', NULL, NULL, '2012-06-21 23:25:35', 6, 1, 2, 1, 3, '39.4800'),
(6, 2, 'Mileage', 'Mileage - Standard', '', '2012-06-21', '2012-06-21', '', '4234', 25, '1.0000', '25.0000', '', '2012-06-21', NULL, NULL, '2012-06-21 23:25:57', 6, 1, 2, 1, 3, '25.0000'),
(7, 2, 'Mileage', 'Mileage - Standard', '', '2012-06-21', '2012-06-21', '', '4234', 40, '1.2000', '48.0000', '', '2012-06-21', NULL, NULL, '2012-06-21 23:26:50', 6, 1, 2, 1, 3, '48.0000'),
(8, 4, 'Waiting', 'Waiting - Standard', '', '2012-06-21', '2012-06-21', '', '4564567', 2, '20.0000', '40.0000', '', '2012-06-21', NULL, NULL, '2012-06-21 23:28:45', 6, 1, 2, 1, 2, '40.0000'),
(9, 4, 'Waiting', 'Waiting - Standard', '', '2012-06-21', '2012-06-21', '', '4564567', 2, '20.0000', '40.0000', '', '2012-06-21', NULL, NULL, '2012-06-21 23:28:45', 6, 1, 2, 1, 3, '40.0000'),
(10, 3, 'Bonus', 'Bonus - Standard', '', '2012-06-21', '2012-06-22', '', '67589', 1, '100.0000', '100.0000', '', '2012-06-21', NULL, NULL, '2012-06-21 23:29:06', 6, 1, 2, 1, 2, '100.0000'),
(11, 3, 'Bonus', 'Bonus - Standard', '', '2012-06-21', '2012-06-22', '', '67589', 1, '100.0000', '100.0000', '', '2012-06-21', NULL, NULL, '2012-06-21 23:29:06', 6, 1, 2, 1, 3, '100.0000'),
(12, 2, 'Mileage', 'Mileage - Standard', '', '2012-06-21', '2012-06-21', '', '4234', 23, '0.9870', '22.7010', '', '2012-06-21', NULL, NULL, '2012-06-21 23:29:53', 6, 1, 2, 1, 3, '22.7010'),
(13, 1, 'Delivery', 'Delivery - Standard', '', '2012-06-21', '2012-06-21', '', '423423', 1, '75.0000', '75.0000', '', '2012-06-21', NULL, NULL, '2012-06-21 23:30:11', 6, 1, 2, 1, 2, '75.0000'),
(14, 1, 'Delivery', 'Delivery - Standard', '', '2012-06-21', '2012-06-21', '', '423423', 1, '75.0000', '75.0000', '', '2012-06-21', NULL, NULL, '2012-06-21 23:30:11', 6, 1, 2, 1, 3, '75.0000'),
(15, 2, 'Mileage', 'Mileage - Standard', '', '2012-06-21', '2012-06-21', '', '4234', 65, '0.9870', '64.1550', '', '2012-06-21', NULL, NULL, '2012-06-21 23:30:38', 6, 1, 2, 1, 3, '64.1550'),
(16, 5, 'PS1', 'hourly', '', '2012-07-12', '2012-07-12', '', '', 160, '20.0000', '3200.0000', '', '2012-07-22', '2012-08-03 16:56:58', 3, '2012-07-12 10:25:44', 6, NULL, 3, 2, 16, '1200.0000'),
(17, 5, 'PS1', 'hourly', '', '2012-07-12', '2012-07-12', '', '', 80, '20.0000', '1600.0000', '', '2012-07-22', '2012-08-03 16:56:59', 3, '2012-07-12 10:25:44', 6, NULL, 3, 2, 17, '1200.0000'),
(18, 5, 'PS1', 'hourly', '', '2012-07-12', '2012-07-12', '', '', 1, '20.0000', '20.0000', '', '2012-07-22', '2012-08-03 16:56:59', 3, '2012-07-12 10:25:44', 6, NULL, 3, 2, 18, '20.0000'),
(19, 6, 'PS2', 'overtime', '', '2012-07-12', '2012-07-12', '', '', 17, '30.0000', '510.0000', '', '2012-07-22', '2012-08-03 16:56:59', 3, '2012-07-12 14:35:30', 6, NULL, 3, 2, 17, '510.0000'),
(20, 7, 'PS3', 'commission', '', '2012-07-12', '2012-07-26', '', '', 1, '300.0000', '300.0000', '', '2012-07-22', '2012-08-03 16:56:59', 3, '2012-07-12 14:37:32', 6, NULL, 3, 2, 18, '300.0000'),
(21, 5, 'PS1', 'hourly', '', '2012-07-12', '2012-07-12', '', '', 160, '20.0000', '3200.0000', '', '2012-07-22', '2012-08-05 10:55:56', 1, '2012-07-12 10:25:44', 6, NULL, 3, 3, 16, '3200.0000'),
(22, 9, '', 'Home Return', 'a12345', '2012-08-10', '2012-08-12', 'West', '', 4, '85.0000', '340.0000', '', '2012-08-18', '2012-08-29 18:34:01', 5, '2012-08-10 00:10:44', 5, NULL, 3, 4, 23, '155.0000'),
(23, 9, '', 'Home Return', 'a12345', '2012-08-10', '2012-08-12', 'West', '', 4, '85.0000', '340.0000', '', '2012-08-18', '2012-08-29 18:34:01', 5, '2012-08-10 00:10:44', 5, NULL, 3, 4, 31, '155.0000'),
(24, 9, '', 'Home Return', 'a12345', '2012-08-10', '2012-08-12', 'West', '', 4, '85.0000', '340.0000', '', '2012-08-18', '2012-08-29 18:34:01', 5, '2012-08-10 00:10:44', 5, NULL, 3, 4, 32, '155.0000'),
(25, 9, '', 'Home Return', 'a12345', '2012-08-10', '2012-08-12', 'West', '', 4, '85.0000', '340.0000', '', '2012-08-18', '2012-08-29 18:34:01', 5, '2012-08-10 00:10:44', 5, NULL, 3, 4, 33, '155.0000'),
(26, 9, '', 'Home Return', 'a12345', '2012-08-10', '2012-08-12', 'West', '', 4, '85.0000', '340.0000', '', '2012-08-18', '2012-08-29 18:34:01', 5, '2012-08-10 00:10:44', 5, NULL, 3, 4, 34, '155.0000'),
(27, 10, '', 'Return Special', '', '2012-08-10', '2012-08-12', '', '', 3, '90.0000', '270.0000', '', '2012-08-18', '2012-08-29 18:34:02', 5, '2012-08-10 00:11:46', 5, NULL, 3, 4, 31, '270.0000'),
(28, 9, '', 'Home Return', '', '2012-08-10', '2012-08-12', 'West', '', 1, '85.0000', '85.0000', '', '2012-08-18', '2012-08-29 18:34:02', 5, '2012-08-10 00:12:10', 5, NULL, 3, 4, 23, '85.0000'),
(29, 9, '', 'Home Return', '', '2012-08-10', '2012-08-12', 'West', '', 1, '85.0000', '85.0000', '', '2012-08-18', '2012-08-29 18:34:02', 5, '2012-08-10 00:12:10', 5, NULL, 3, 4, 31, '85.0000'),
(30, 9, '', 'Home Return', '', '2012-08-10', '2012-08-12', 'West', '', 1, '85.0000', '85.0000', '', '2012-08-18', '2012-08-29 18:34:02', 5, '2012-08-10 00:12:10', 5, NULL, 3, 4, 32, '85.0000'),
(31, 9, '', 'Home Return', '', '2012-08-10', '2012-08-12', 'West', '', 1, '85.0000', '85.0000', '', '2012-08-18', '2012-08-29 18:34:02', 5, '2012-08-10 00:12:10', 5, NULL, 3, 4, 33, '85.0000'),
(32, 9, '', 'Home Return', '', '2012-08-10', '2012-08-12', 'West', '', 1, '85.0000', '85.0000', '', '2012-08-18', '2012-08-29 18:34:02', 5, '2012-08-10 00:12:10', 5, NULL, 3, 4, 34, '85.0000'),
(33, 13, '', 'Long Distance Deliver', '', '2012-08-10', '2012-08-11', 'Southwest', '', 2, '135.0000', '270.0000', '', '2012-08-18', '2012-08-29 18:34:02', 5, '2012-08-10 21:02:06', 5, NULL, 3, 4, 33, '270.0000'),
(34, 8, '', 'Home Delivery', '', '2012-08-17', '2012-08-18', 'West', '55600-43544', 4, '75.0000', '300.0000', '', '2012-08-18', '2012-08-29 18:34:02', 5, '2012-08-17 23:20:06', 5, NULL, 3, 4, 23, '300.0000'),
(35, 8, '', 'Home Delivery', '', '2012-08-17', '2012-08-18', 'West', '55600-43544', 4, '75.0000', '300.0000', '', '2012-08-18', '2012-08-29 18:34:03', 5, '2012-08-17 23:20:06', 5, NULL, 3, 4, 31, '300.0000'),
(36, 8, '', 'Home Delivery', '', '2012-08-17', '2012-08-18', 'West', '55600-43544', 4, '75.0000', '300.0000', '', '2012-08-18', '2012-08-29 18:34:03', 5, '2012-08-17 23:20:06', 5, NULL, 3, 4, 32, '300.0000'),
(37, 8, '', 'Home Delivery', '', '2012-08-17', '2012-08-18', 'West', '55600-43544', 4, '75.0000', '300.0000', '', '2012-08-18', '2012-08-29 18:34:03', 5, '2012-08-17 23:20:06', 5, NULL, 3, 4, 33, '300.0000'),
(38, 8, '', 'Home Delivery', '', '2012-08-17', '2012-08-18', 'West', '55600-43544', 4, '75.0000', '300.0000', '', '2012-08-18', '2012-08-29 18:34:03', 5, '2012-08-17 23:20:06', 5, NULL, 3, 4, 34, '300.0000'),
(39, 10, '', 'Return Special', '', '2012-08-17', '2012-08-19', '', '', 4, '90.0000', '360.0000', '', '2012-08-18', '2012-08-29 18:34:03', 5, '2012-08-17 23:56:38', 5, NULL, 3, 4, 31, '360.0000'),
(40, 15, 'Delivery', 'Home Delivery', '', '2012-09-21', '2012-09-28', 'Delivery', '6767-90988', 12, '85.0000', '1020.0000', '', '2012-09-20', '2012-09-25 18:15:04', 5, '2012-09-21 19:47:23', 17, NULL, 3, 8, 38, '563.4400'),
(41, 15, 'Delivery', 'Home Delivery', '', '2012-09-21', '2012-09-28', 'Delivery', '6767-90988', 5, '85.0000', '425.0000', '', '2012-09-20', '2012-09-25 18:15:04', 5, '2012-09-21 19:47:23', 17, NULL, 3, 8, 39, '425.0000'),
(42, 19, 'Mileage', 'Mileage', '', '2012-09-21', '2012-09-21', 'Expense', '', 340, '0.7000', '238.0000', '', '2012-09-20', '2012-09-25 18:15:05', 5, '2012-09-21 19:48:20', 17, NULL, 3, 8, 38, '238.0000'),
(43, 19, 'Mileage', 'Mileage', '', '2012-09-21', '2012-09-21', 'Expense', '', 234, '0.7000', '163.8000', '', '2012-09-20', '2012-09-25 18:15:05', 5, '2012-09-21 19:48:20', 17, NULL, 3, 8, 39, '163.8000'),
(44, 19, 'Mileage', 'Mileage', '', '2012-09-21', '2012-09-21', 'Expense', '', 400, '0.7000', '280.0000', '', '2012-09-20', '2012-09-25 18:15:05', 5, '2012-09-21 19:48:20', 17, NULL, 3, 8, 40, '280.0000'),
(45, 18, 'Delivery', 'Business Delivery', '', '2012-09-21', '2012-09-21', 'Delivery', '', 3, '95.0000', '285.0000', '', '2012-09-20', '2012-09-25 18:15:05', 5, '2012-09-21 19:50:03', 17, NULL, 3, 8, 39, '285.0000'),
(46, 18, 'Delivery', 'Business Delivery', '', '2012-09-21', '2012-09-21', 'Delivery', '', 6, '95.0000', '570.0000', '', '2012-09-20', '2012-09-25 18:15:05', 5, '2012-09-21 19:50:03', 17, NULL, 3, 8, 40, '215.8900'),
(47, 17, 'Return', 'Business Return', '', '2012-09-21', '2012-09-21', 'Delivery', '', 2, '80.0000', '160.0000', '', '2012-09-20', '2012-09-25 18:15:05', 5, '2012-09-21 20:32:13', 17, NULL, 3, 8, 38, '160.0000'),
(48, 17, 'Return', 'Business Return', NULL, '2012-09-21', '2012-09-21', 'Delivery', '', 1, '80.0000', '80.0000', NULL, '2012-09-20', '2012-09-25 18:15:05', 5, '2012-09-21 20:32:13', 17, NULL, 3, 8, 39, '80.0000'),
(49, 15, 'Delivery', 'Home Delivery', '', '2012-09-26', '2012-10-03', 'Delivery', '6767-90988', 4, '85.0000', '340.0000', '', '2012-09-20', '2012-10-02 20:08:38', 5, '2012-09-26 21:06:33', 17, NULL, 3, 9, 38, '150.0000'),
(50, 15, 'Delivery', 'Home Delivery', '', '2012-09-26', '2012-10-03', 'Delivery', '6767-90988', 6, '85.0000', '510.0000', '', '2012-09-20', '2012-10-02 20:08:38', 5, '2012-09-26 21:06:33', 17, NULL, 3, 9, 39, '68.5400'),
(51, 16, 'Return', 'Home Return', '', '2012-09-26', '2012-10-03', 'Delivery', '6767-90977', 3, '75.0000', '225.0000', '', '2012-09-20', '2012-10-02 20:08:38', 5, '2012-09-26 21:07:20', 17, NULL, 3, 9, 39, '225.0000'),
(52, 16, 'Return', 'Home Return', '', '2012-09-26', '2012-10-10', 'Delivery', '6767-90977', 2, '75.0000', '150.0000', '', '2012-09-20', '2012-10-02 20:08:38', 5, '2012-09-26 21:07:20', 17, NULL, 3, 9, 40, '150.0000'),
(53, 18, 'Delivery', 'Business Delivery', '', '2012-09-26', '2012-09-26', 'Delivery', '', 5, '95.0000', '475.0000', '', '2012-09-20', '2012-10-02 20:08:38', 5, '2012-09-26 21:08:28', 17, NULL, 3, 9, 39, '475.0000'),
(54, 18, 'Delivery', 'Business Delivery', '', '2012-09-26', '2012-10-09', 'Delivery', '', 3, '95.0000', '285.0000', '', '2012-09-20', '2012-10-02 20:08:38', 5, '2012-09-26 21:08:28', 17, NULL, 3, 9, 40, '285.0000'),
(55, 19, 'Mileage', 'Mileage', '', '2012-09-26', '2012-09-26', 'Expense', '', 545, '0.7000', '381.5000', '', '2012-09-20', '2012-10-02 20:08:38', 5, '2012-09-26 21:11:04', 17, NULL, 3, 9, 38, '381.5000'),
(56, 19, 'Mileage', 'Mileage', '', '2012-09-26', '2012-09-26', 'Expense', '', 470, '0.7000', '329.0000', '', '2012-09-20', '2012-10-02 20:08:38', 5, '2012-09-26 21:11:04', 17, NULL, 3, 9, 39, '329.0000'),
(57, 19, 'Mileage', 'Mileage', '', '2012-09-26', '2012-09-26', 'Expense', '', 576, '0.7000', '403.2000', '', '2012-09-20', '2012-10-02 20:08:38', 5, '2012-09-26 21:11:04', 17, NULL, 3, 9, 40, '403.2000'),
(58, 21, 'Additional', 'Bonus', '', '2012-09-26', '2012-09-26', 'Additional', '', 1, '200.0000', '200.0000', '', '2012-09-20', '2012-10-02 20:08:38', 5, '2012-09-26 21:11:04', 17, NULL, 3, 9, 38, '200.0000'),
(59, 21, 'Additional', 'Bonus', NULL, '2012-09-26', '2012-09-26', 'Additional', '', 1, '200.0000', '200.0000', NULL, '2012-09-20', '2012-10-02 20:08:38', 5, '2012-09-26 21:11:04', 17, NULL, 3, 9, 39, '200.0000'),
(60, 21, 'Additional', 'Bonus', NULL, '2012-09-26', '2012-09-26', 'Additional', '', 1, '200.0000', '200.0000', NULL, '2012-09-20', '2012-10-02 20:08:38', 5, '2012-09-26 21:11:04', 17, NULL, 3, 9, 40, '200.0000'),
(106, 22, '', 'Mileage', '', '2012-09-28', '2012-09-28', '', '', 3000, '0.3500', '1050.0000', '', '2012-09-27', '2012-09-28 01:43:32', 20, '2012-09-28 01:11:58', 20, NULL, 2, 14, 45, '1050.0000'),
(107, 22, '', 'Mileage', '', '2012-09-28', '2012-09-28', '', '', 1500, '0.3500', '525.0000', '', '2012-09-27', '2012-09-28 01:43:32', 20, '2012-09-28 01:11:58', 20, NULL, 2, 14, 46, '525.0000'),
(108, 22, '', 'Mileage', '', '2012-09-28', '2012-09-28', '', '', 700, '0.3500', '245.0000', '', '2012-09-27', '2012-09-28 01:43:32', 20, '2012-09-28 01:11:58', 20, NULL, 2, 14, 47, '245.0000'),
(109, 22, '', 'Mileage', NULL, '2012-09-28', '2012-09-28', '', '', 500, '0.3500', '175.0000', NULL, '2012-09-27', '2012-09-28 01:43:33', 20, '2012-09-28 01:11:58', 20, NULL, 2, 14, 48, '175.0000'),
(110, 22, '', 'Mileage', '', '2012-09-28', '2012-09-28', '', '', 100, '0.3500', '35.0000', '', '2012-09-27', '2012-09-28 01:43:33', 20, '2012-09-28 01:11:58', 20, NULL, 2, 14, 49, '35.0000'),
(111, 23, '', 'Wage', '', '2012-09-28', '2012-09-28', '', '', 4, '900.0000', '3600.0000', '', '2012-09-27', '2012-09-28 01:43:33', 20, '2012-09-28 01:11:58', 20, NULL, 2, 14, 45, '3600.0000'),
(112, 23, '', 'Wage', '', '2012-09-28', '2012-09-28', '', '', 3, '900.0000', '2700.0000', '', '2012-09-27', '2012-09-28 01:43:33', 20, '2012-09-28 01:11:58', 20, NULL, 2, 14, 46, '2700.0000'),
(113, 23, '', 'Wage', '', '2012-09-28', '2012-09-28', '', '', 2, '900.0000', '1800.0000', '', '2012-09-27', '2012-09-28 01:43:33', 20, '2012-09-28 01:11:58', 20, NULL, 2, 14, 47, '1800.0000'),
(114, 23, '', 'Wage', NULL, '2012-09-28', '2012-09-28', '', '', 1, '900.0000', '900.0000', NULL, '2012-09-27', '2012-09-28 01:43:33', 20, '2012-09-28 01:11:58', 20, NULL, 2, 14, 48, '900.0000'),
(115, 23, '', 'Wage', NULL, '2012-09-28', '2012-09-28', '', '', 1, '900.0000', '900.0000', NULL, '2012-09-27', '2012-09-28 01:43:33', 20, '2012-09-28 01:11:58', 20, NULL, 2, 14, 49, '900.0000'),
(116, 24, '', 'Delivery', '', '2012-09-28', '2012-09-28', '', '', 100, '50.0000', '5000.0000', '', '2012-09-27', NULL, NULL, '2012-09-28 01:11:58', 20, NULL, 2, 14, 45, '5000.0000'),
(117, 24, '', 'Delivery', '', '2012-09-28', '2012-09-28', '', '', 50, '50.0000', '2500.0000', '', '2012-09-27', NULL, NULL, '2012-09-28 01:11:58', 20, NULL, 2, 14, 46, '2500.0000'),
(118, 24, '', 'Delivery', NULL, '2012-09-28', '2012-09-28', '', '', 30, '50.0000', '1500.0000', NULL, '2012-09-27', NULL, NULL, '2012-09-28 01:11:58', 20, NULL, 2, 14, 47, '1500.0000'),
(119, 24, '', 'Delivery', '', '2012-09-28', '2012-09-28', '', '', 15, '50.0000', '750.0000', '', '2012-09-27', NULL, NULL, '2012-09-28 01:11:58', 20, NULL, 2, 14, 48, '750.0000'),
(120, 24, '', 'Delivery', '', '2012-09-28', '2012-09-28', '', '', 5, '50.0000', '250.0000', '', '2012-09-27', NULL, NULL, '2012-09-28 01:11:58', 20, NULL, 2, 14, 49, '250.0000'),
(121, 15, 'Delivery', 'Home Delivery', NULL, '2012-10-02', '2012-10-09', 'Delivery', '6767-90988', 1, '85.0000', '85.0000', NULL, '2012-09-20', '2012-10-02 20:08:38', 5, '2012-10-02 20:07:49', 5, NULL, 3, 9, 39, '85.0000'),
(122, 16, 'Return', 'Home Return', NULL, '2012-10-02', '2012-10-09', 'Delivery', '6767-90977', 1, '75.0000', '75.0000', NULL, '2012-09-20', '2012-10-02 20:08:38', 5, '2012-10-02 20:07:49', 5, NULL, 3, 9, 39, '85.0000');

DROP TABLE IF EXISTS `payments_temp`;
CREATE TABLE IF NOT EXISTS `payments_temp` (
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
  KEY `payment_temp_status_id` (`status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

INSERT INTO `payments_temp` (`id`, `category`, `invoice`, `invoice_date`, `department`, `gl_code`, `quantity`, `rate`, `check_id`, `source_id`, `contractor_id`, `carrier_payment`, `contract`, `payment_code`, `code`, `setup_id`, `status_id`, `error`) VALUES
(4, NULL, NULL, '2012-06-21', NULL, NULL, 25, NULL, NULL, 3, 563, 228864, 12246, 'Delivery', NULL, NULL, 2, 'Contractor not found (invalid Contactor Id code)<br>Payment Setup not found (invalid payment code)<br>'),
(5, NULL, NULL, '2012-06-21', NULL, NULL, 30, NULL, NULL, 3, 866, 323256, 125454, 'Delivery', NULL, NULL, 2, 'Contractor not found (invalid Contactor Id code)<br>Payment Setup not found (invalid payment code)<br>'),
(6, NULL, NULL, '2012-06-25', NULL, NULL, 500, NULL, NULL, 3, 56565, 4564564, NULL, 'Mileage ', NULL, NULL, 2, 'Contractor not found (invalid Contactor Id code)<br>Payment Setup not found (invalid payment code)<br>'),
(7, NULL, NULL, '1970-01-01', NULL, NULL, 10, NULL, NULL, 4, 32, 228864, 12246, 'DEL', NULL, 8, 1, NULL),
(8, NULL, NULL, '1970-01-01', NULL, NULL, 11, NULL, NULL, 4, 33, 323256, 125454, 'DEL', NULL, 8, 1, NULL),
(9, NULL, NULL, '1970-01-01', NULL, NULL, 5, NULL, NULL, 4, 34, 4564564, NULL, 'LDD', NULL, 13, 1, NULL),
(10, NULL, NULL, '1970-01-01', NULL, NULL, 6, NULL, NULL, 4, 34, 4564565, NULL, 'DEL', NULL, 8, 1, NULL),
(11, NULL, NULL, '1970-01-01', NULL, NULL, 5, NULL, NULL, 4, 33, 323254, 125454, 'RET', NULL, 9, 1, NULL),
(12, NULL, NULL, '1970-01-01', NULL, NULL, 11, NULL, NULL, 5, 33, 323256, 125454, 'DEL', NULL, 8, 1, NULL),
(13, NULL, NULL, '1970-01-01', NULL, NULL, 5, NULL, NULL, 5, 34, 4564564, NULL, 'LDD', NULL, 13, 1, NULL),
(14, NULL, NULL, '1970-01-01', NULL, NULL, 6, NULL, NULL, 5, 34, 4564565, NULL, 'DEL', NULL, 8, 1, NULL),
(15, NULL, NULL, '1970-01-01', NULL, NULL, 5, NULL, NULL, 5, 33, 323254, 125454, 'RET', NULL, 9, 1, NULL);

DROP TABLE IF EXISTS `reserve_account`;
CREATE TABLE IF NOT EXISTS `reserve_account` (
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
  `balance` decimal(10,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reserve_account_entity_id` (`entity_id`),
  KEY `fk_reserve_account_bank_account_id` (`bank_account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=33 ;

INSERT INTO `reserve_account` (`id`, `entity_id`, `bank_account_id`, `account_name`, `description`, `priority`, `min_balance`, `contribution_amount`, `max_withdrawal_amount`, `initial_balance`, `current_balance`, `disbursement_code`, `balance`) VALUES
(1, 3, 1, 'Navibulgar reserve account', 'some description', 1, '1.0000', '10.0000', '10000.0000', '500.0000', '1000.0000', '1234567890', '1000.0000'),
(2, 4, 1, 'John''s Account', 'John''s Description', 2, '1.0000', '123.0000', '345.0000', '500.0000', '2000.0000', '123456', '2000.0000'),
(3, 8, 2, 'Gonazales account', 'blablabla description', 0, '200.0000', '400.0000', '600.0000', '30.0000', '60.0000', 'code', '60.0000'),
(4, 5, 2, 'Best Acc', 'best description', 3, '10.0000', '20.0000', '30.0000', '40.0000', '50.0000', '666', '50.0000'),
(5, 9, 2, 'Penske account Name', 'Penske description', 6, '60.0000', '40.0000', '888.0000', '999.0000', '3000.0000', 'my code', '3000.0000'),
(6, 10, 2, 'Soso account', 'Soso some description', 7, '1.0000', '5.0000', '4.0000', '2.0000', '110.0000', 'soso code', '110.0000'),
(7, 2, 2, 'Penske account Name', 'Penske description', 8, '60.0000', '40.0000', '888.0000', '999.0000', '3000.0000', 'my code', '3000.0000'),
(8, 19, 3, 'Ven1AmountAccount', 'VasilAccount', 9, '400.0000', '200.0000', '500.0000', '0.0000', '1000.0000', '', '1000.0000'),
(9, 20, 5, 'Ven2AmountAccount', 'ValeraAccount', 10, '300.0000', '150.0000', '300.0000', '0.0000', '500.0000', '', '500.0000'),
(10, 16, 7, 'Ven1AmountAccount', 'VasilAccount', 11, '400.0000', '200.0000', '500.0000', '1000.0000', '400.0000', '', '400.0000'),
(11, 16, 7, 'Ven2AmountAccount', 'ValeraAccount', 12, '300.0000', '150.0000', '300.0000', '500.0000', '400.0000', '', '400.0000'),
(12, 17, 8, 'Ven1AmountAccount', 'VasilAccount', 13, '400.0000', '200.0000', '500.0000', '1000.0000', '400.0000', '', '200.0000'),
(13, 17, 8, 'Ven2AmountAccount', 'ValeraAccount', 14, '300.0000', '150.0000', '300.0000', '500.0000', '300.0000', '', '300.0000'),
(14, 18, 9, 'Ven1AmountAccount', 'VasilAccount', 15, '400.0000', '200.0000', '500.0000', '1000.0000', '500.0000', '', '500.0000'),
(15, 18, 9, 'Ven2AmountAccount', 'ValeraAccount', 16, '300.0000', '150.0000', '300.0000', '500.0000', '270.0000', '', '270.0000'),
(16, 36, 11, 'Fuel Reserve Account', 'Fuel Reserve', NULL, '1000.0000', '100.0000', '500.0000', '0.0000', '0.0000', '', '0.0000'),
(17, 34, 11, 'Fuel Reserve Account', 'Fuel Reserve', NULL, '1000.0000', '100.0000', '500.0000', '0.0000', '0.0000', '', '0.0000'),
(18, 36, 12, 'Maintenance', 'Truck Maintenance', NULL, '1000.0000', '100.0000', '500.0000', '0.0000', '0.0000', '', '0.0000'),
(19, 41, 22, 'AA Leasing Reserve', 'AA Lease Res', NULL, '1000.0000', '200.0000', '500.0000', '850.0000', '850.0000', 'AALR', '850.0000'),
(20, 43, 24, 'AC Fuel Reserve Account', 'AC Fuel Res. Act.', NULL, '500.0000', '50.0000', '500.0000', '200.0000', '1137.0000', '', '1137.0000'),
(21, 38, 24, 'AC Fuel Reserve Account', 'AC Fuel Res. Act.', NULL, '500.0000', '50.0000', '500.0000', '100.0000', '250.0000', '', '250.0000'),
(22, 39, 24, 'AC Fuel Reserve Account', 'AC Fuel Res. Act.', NULL, '500.0000', '50.0000', '500.0000', '100.0000', '-113.2000', 'ACF', '-113.2000'),
(23, 40, 24, 'AC Fuel Reserve Account', 'AC Fuel Res. Act.', NULL, '500.0000', '50.0000', '500.0000', '0.0000', '1000.0000', '', '681.6400'),
(24, 38, 22, 'AA Leasing Reserve', 'AA Lease Res', NULL, '1000.0000', '200.0000', '500.0000', '100.0000', '100.0000', 'AALR', '100.0000'),
(25, 39, 22, 'AA Leasing Reserve', 'AA Lease Res', NULL, '1000.0000', '200.0000', '500.0000', '300.0000', '300.0000', 'AALR', '300.0000'),
(26, 40, 22, 'AA Leasing Reserve', 'AA Lease Res', NULL, '1000.0000', '200.0000', '500.0000', '450.0000', '450.0000', 'AALR', '300.0000'),
(27, 51, 27, 'Leasing Co X Reserve', 'Maintenance', NULL, '300.0000', '100.0000', '1000.0000', '0.0000', '5050.0000', '', '5050.0000'),
(28, 45, 27, 'Leasing Co X Reserve', 'Maintenance', NULL, '300.0000', '100.0000', '1000.0000', '0.0000', '2000.0000', '', '2000.0000'),
(29, 46, 27, 'Leasing Co X Reserve', 'Maintenance', NULL, '300.0000', '100.0000', '1000.0000', '0.0000', '1750.0000', '', '1750.0000'),
(30, 49, 27, 'Leasing Co X Reserve', 'Maintenance', NULL, '300.0000', '100.0000', '1000.0000', '0.0000', '1200.0000', '', '1200.0000'),
(31, 48, 27, 'Leasing Co X Reserve', 'Maintenance', NULL, '300.0000', '100.0000', '1000.0000', '0.0000', '0.0000', '', '0.0000'),
(32, 47, 27, 'Leasing Co X Reserve', 'Maintenance', NULL, '300.0000', '100.0000', '1000.0000', '0.0000', '100.0000', '', '100.0000');

DROP TABLE IF EXISTS `reserve_account_carrier`;
CREATE TABLE IF NOT EXISTS `reserve_account_carrier` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reserve_account_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reserve_account_carrier_reserve_account_id` (`reserve_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `reserve_account_contractor`;
CREATE TABLE IF NOT EXISTS `reserve_account_contractor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reserve_account_id` int(10) unsigned NOT NULL,
  `reserve_account_vendor_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reserve_account_contractor_reserve_account_id` (`reserve_account_id`),
  KEY `fk_reserve_account_contractor_reserve_account_vendor_id` (`reserve_account_vendor_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=24 ;

INSERT INTO `reserve_account_contractor` (`id`, `reserve_account_id`, `reserve_account_vendor_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 2),
(4, 4, 2),
(5, 7, 1),
(6, 10, 3),
(7, 11, 4),
(8, 12, 3),
(9, 13, 4),
(10, 14, 3),
(11, 15, 4),
(12, 17, 5),
(13, 21, 8),
(14, 22, 8),
(15, 23, 8),
(16, 24, 7),
(17, 25, 7),
(18, 26, 7),
(19, 28, 9),
(20, 29, 9),
(21, 30, 9),
(22, 31, 9),
(23, 32, 9);

DROP TABLE IF EXISTS `reserve_account_vendor`;
CREATE TABLE IF NOT EXISTS `reserve_account_vendor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reserve_account_id` int(10) unsigned NOT NULL,
  `vendor_reserve_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reserve_account_vendor_reserve_account_id` (`reserve_account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=10 ;

INSERT INTO `reserve_account_vendor` (`id`, `reserve_account_id`, `vendor_reserve_code`) VALUES
(1, 5, 'code'),
(2, 6, '123456'),
(3, 8, 'V1RA'),
(4, 9, 'V2RA'),
(5, 16, 'PFR'),
(6, 18, 'Maint'),
(7, 19, 'AALRA'),
(8, 20, 'ACF'),
(9, 27, '');

DROP TABLE IF EXISTS `reserve_transaction`;
CREATE TABLE IF NOT EXISTS `reserve_transaction` (
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
  KEY `fk_reserve_transaction_status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=17 ;

INSERT INTO `reserve_transaction` (`id`, `reserve_account_sender`, `reserve_account_receiver`, `vendor_code`, `type`, `deduction_id`, `priority`, `amount`, `balance`, `adjusted_balance`, `settlement_cycle_id`, `approved_datetime`, `approved_by`, `created_datetime`, `created_by`, `source_id`, `disbursement_id`, `status`) VALUES
(1, 10, 8, NULL, 1, NULL, NULL, '200.0000', NULL, NULL, 2, '2012-08-03 16:57:25', 3, '2012-08-03 16:56:42', 3, NULL, NULL, 3),
(2, 12, 8, NULL, 1, NULL, NULL, '200.0000', NULL, NULL, 2, '2012-08-03 16:57:25', 3, '2012-08-03 16:56:43', 3, NULL, NULL, 3),
(3, 13, 9, NULL, 1, NULL, NULL, '150.0000', NULL, NULL, 2, '2012-08-03 16:57:25', 3, '2012-08-03 16:56:43', 3, NULL, NULL, 3),
(4, 15, 9, NULL, 2, 22, NULL, '180.0000', '180.0000', NULL, 2, '2012-08-03 16:57:25', 3, '2012-08-03 16:56:48', 3, NULL, NULL, 3),
(11, 10, 8, NULL, 1, NULL, NULL, '30.0000', NULL, NULL, 3, '2012-09-25 12:25:02', 1, '2012-09-25 12:23:11', 1, NULL, NULL, 3),
(12, 13, 9, NULL, 1, NULL, NULL, '110.0000', NULL, NULL, 3, '2012-09-25 12:25:02', 1, '2012-09-25 12:23:12', 1, NULL, NULL, 3),
(13, 15, 9, NULL, 1, NULL, NULL, '150.0000', NULL, NULL, 3, '2012-09-25 12:25:02', 1, '2012-09-25 12:23:12', 1, NULL, NULL, 3),
(14, 22, 20, NULL, 2, 34, NULL, '213.2000', '213.2000', NULL, 8, '2012-09-25 18:16:00', 5, '2012-09-25 18:15:35', 5, NULL, NULL, 3),
(15, 23, 20, NULL, 2, 44, NULL, '318.3600', '318.3600', NULL, 9, NULL, NULL, '2012-10-03 17:30:12', 1, NULL, NULL, 2),
(16, 26, 19, NULL, 2, 38, NULL, '150.0000', '150.0000', NULL, 9, NULL, NULL, '2012-10-03 17:30:13', 1, NULL, NULL, 2);

DROP TABLE IF EXISTS `reserve_transaction_type`;
CREATE TABLE IF NOT EXISTS `reserve_transaction_type` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `reserve_transaction_type` (`id`, `title`) VALUES
(1, 'Contribution'),
(2, 'Withdrawal'),
(3, 'Cash Advance');

DROP TABLE IF EXISTS `settlement_cycle`;
CREATE TABLE IF NOT EXISTS `settlement_cycle` (
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
  KEY `fk_settlement_cycle_status_id` (`status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

INSERT INTO `settlement_cycle` (`id`, `carrier_id`, `cycle_period_id`, `payment_terms`, `disbursement_terms`, `cycle_start_date`, `cycle_close_date`, `status_id`, `first_start_day`, `second_start_day`) VALUES
(1, 1, 3, 0, 5, '2012-06-20', '2012-07-20', 1, NULL, NULL),
(2, 15, 1, 2, 5, '2012-07-10', '2012-07-17', 5, NULL, NULL),
(3, 15, 1, 2, 5, '2012-07-17', '2012-07-24', 5, NULL, NULL),
(4, 21, 1, 3, 6, '2012-08-05', '2012-08-12', 5, NULL, NULL),
(5, 21, 1, 3, 5, '2012-08-12', '2012-08-19', 5, NULL, NULL),
(6, 21, 1, 3, 5, '2012-08-19', '2012-08-26', 2, NULL, NULL),
(7, 21, 4, 3, 5, '2012-08-26', '2012-09-06', 1, 6, 21),
(8, 37, 1, 7, 5, '2012-09-09', '2012-09-15', 5, NULL, NULL),
(9, 37, 1, 7, 5, '2012-09-17', '2012-09-23', 7, NULL, NULL),
(10, 37, 1, 7, 5, '2012-09-16', '2012-09-22', 1, NULL, NULL),
(11, 37, 1, 7, 5, '2012-09-16', '2012-09-22', 1, NULL, NULL),
(12, 37, 1, 7, 5, '2012-09-24', '2012-09-30', 1, NULL, NULL),
(13, 15, 1, 2, 5, '2012-07-25', '2012-07-31', 1, NULL, NULL),
(14, 44, 1, 7, 5, '2012-09-16', '2012-09-22', 2, NULL, NULL),
(15, 44, 1, 7, 5, '2012-09-23', '2012-09-29', 1, NULL, NULL);

DROP TABLE IF EXISTS `settlement_cycle_status`;
CREATE TABLE IF NOT EXISTS `settlement_cycle_status` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `settlement_cycle_status` (`id`, `title`) VALUES
(1, 'Not verified'),
(2, 'Verified'),
(3, 'Processing'),
(4, 'Approved'),
(5, 'Closed'),
(6, 'Fully funded'),
(7, 'Not fully funded');

DROP TABLE IF EXISTS `setup_level`;
CREATE TABLE IF NOT EXISTS `setup_level` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `setup_level` (`id`, `title`) VALUES
(1, 'Global'),
(2, 'Individual');

DROP TABLE IF EXISTS `system_values`;
CREATE TABLE IF NOT EXISTS `system_values` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  `value` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `user_role`;
CREATE TABLE IF NOT EXISTS `user_role` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `user_role` (`id`, `title`) VALUES
(1, 'Super admin'),
(2, 'Carrier'),
(3, 'Contractor'),
(4, 'Vendor');

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_bin NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `last_login_ip` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `last_selected_carrier` int(10) DEFAULT NULL,
  `last_selected_contractor` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_users_role_id` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=23 ;

INSERT INTO `users` (`id`, `role_id`, `email`, `name`, `password`, `last_login_ip`, `last_selected_carrier`, `last_selected_contractor`) VALUES
(1, 1, 'danny@danny.com', 'danny', '1a1dc91c907325c69271ddf0c944bc72', '127.0.0.1', 4, NULL),
(2, 2, 'danny@true.com', 'Dannyy', '1a1dc91c907325c69271ddf0c944bc72', '178.127.150.29', NULL, NULL),
(3, 1, 'dkozhemyako@tula.co', 'Daniel Kozhemyako', '1a1dc91c907325c69271ddf0c944bc72', '94.69.144.15', 6, NULL),
(4, 2, 'john@smith.com', 'John Smith', '1a1dc91c907325c69271ddf0c944bc72', '82.209.239.149', NULL, NULL),
(5, 1, 'jake.zuanich@pfleet.com', 'Jake Zuanich', '82e9dd1f989d339f09c629d0abd942d4', '12.46.64.53', 6, NULL),
(6, 1, 'bivi@mail.by', 'bivi', '05546b0e38ab9175cd905eebcc6ebb76', '82.209.239.149', 6, NULL),
(7, 2, 'johndoe@example.com', 'John Doeeeee', '1a1dc91c907325c69271ddf0c944bc72', '127.0.0.1', NULL, NULL),
(8, 3, 'contractor1@contractor1.com', 'contractor1', '1a1dc91c907325c69271ddf0c944bc72', '127.0.0.1', NULL, NULL),
(9, 4, 'vendor1@vendor1.com', 'vendor1', '1a1dc91c907325c69271ddf0c944bc72', '127.0.0.1', NULL, NULL),
(10, 2, 'car1@test.com', 'CAR1', '1a1dc91c907325c69271ddf0c944bc72', '82.209.239.149', NULL, NULL),
(11, 3, 'con1@test.com', 'CON1', '1a1dc91c907325c69271ddf0c944bc72', '127.0.0.1', NULL, NULL),
(12, 3, 'con2@test.com', 'CON2', '1a1dc91c907325c69271ddf0c944bc72', '127.0.0.1', NULL, NULL),
(13, 3, 'con3@test.com', 'CON3', '1a1dc91c907325c69271ddf0c944bc72', '127.0.0.1', NULL, NULL),
(14, 4, 'ven1@test.com', 'VEN1', '1a1dc91c907325c69271ddf0c944bc72', '127.0.0.1', NULL, NULL),
(15, 4, 'ven2@test.com', 'VEN2', '1a1dc91c907325c69271ddf0c944bc72', '127.0.0.1', NULL, NULL),
(16, 1, 'phpunittest@pfleet.loc', 'phpunittest', '74cbea5364321be7a0e15e5b2ce1d14d', '127.0.0.1', 4, NULL),
(17, 2, 'carrier@pfleet.com', 'Jake Carrier', '667698a37e0cc3ce500905917ffe3507', '12.46.64.53', 1, NULL),
(18, 2, 'vendor@pfleet.com', 'Jake Vendor', '667698a37e0cc3ce500905917ffe3507', '12.46.64.53', NULL, NULL),
(19, 3, 'contractor@pfleet.com', 'Jake Contractor', '667698a37e0cc3ce500905917ffe3507', '12.46.64.53', 5, NULL),
(20, 2, 'john.verardo@pfleet.com', 'John V', '667698a37e0cc3ce500905917ffe3507', '12.46.64.53', NULL, NULL),
(21, 4, 'vendor@pfleet.com', 'jim dodd', '667698a37e0cc3ce500905917ffe3507', '12.46.64.53', 7, NULL),
(22, 4, 'vendors@pfleet.com', 'Jake Vendor', '667698a37e0cc3ce500905917ffe3507', '12.46.64.53', 6, NULL);

DROP TABLE IF EXISTS `users_visibility`;
CREATE TABLE IF NOT EXISTS `users_visibility` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity_id` int(10) unsigned NOT NULL,
  `participant_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_users_visibility_entity_id` (`entity_id`),
  KEY `fk_users_visibility_participant_id` (`participant_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=45 ;

INSERT INTO `users_visibility` (`id`, `entity_id`, `participant_id`) VALUES
(1, 15, 16),
(2, 15, 17),
(3, 15, 18),
(4, 15, 19),
(5, 15, 20),
(6, 19, 15),
(7, 20, 15),
(8, 12, 2),
(9, 12, 3),
(10, 12, 4),
(11, 12, 5),
(12, 12, 6),
(13, 12, 7),
(14, 1, 2),
(15, 1, 5),
(16, 1, 7),
(17, 1, 8),
(18, 21, 22),
(19, 21, 23),
(20, 21, 24),
(21, 21, 25),
(22, 21, 29),
(23, 21, 26),
(24, 22, 31),
(25, 22, 32),
(26, 22, 33),
(27, 22, 34),
(28, 22, 35),
(29, 36, 21),
(30, 21, 36),
(31, 37, 38),
(32, 37, 39),
(33, 37, 40),
(34, 37, 41),
(35, 37, 42),
(36, 37, 43),
(37, 44, 45),
(38, 44, 46),
(39, 44, 47),
(40, 44, 48),
(41, 44, 49),
(42, 44, 50),
(43, 44, 51),
(44, 44, 52);

DROP TABLE IF EXISTS `vendor`;
CREATE TABLE IF NOT EXISTS `vendor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity_id` int(10) unsigned NOT NULL,
  `tax_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `contact` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `terms` int(11) DEFAULT NULL,
  `resubmit` int(11) DEFAULT NULL,
  `recurring_deductions` int(11) DEFAULT NULL,
  `reserve_account` int(11) DEFAULT NULL,
  `priority` int(10) DEFAULT NULL,
  PRIMARY KEY (`entity_id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_vendor_entity_id` (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=12 ;

INSERT INTO `vendor` (`id`, `entity_id`, `tax_id`, `name`, `contact`, `terms`, `resubmit`, `recurring_deductions`, `reserve_account`, `priority`) VALUES
(1, 9, '451263897', 'Penske Truck Lease', 'Steve Ballmer', 0, 0, 1, 1, NULL),
(2, 10, '456397127', 'Soco Fuel Cards', 'Glenn Beck', 7, 0, 0, 0, NULL),
(3, 19, '568369207', 'Vasil Pypkin', 'VasilVen1Co', 0, 0, 1, 1, NULL),
(4, 20, '947893186', 'Valera Pypkin', 'ValerVen2Co', 7, 0, 0, 0, NULL),
(5, 36, '4566676', 'P-Fleet', 'Jake Zuanich', 2, 0, 0, 1, NULL),
(6, 41, '9867654', 'AA Leasing', 'John Thompson', 0, 0, 1, 1, 3),
(7, 42, '5689899', 'AB Insurance', 'Kim Smith', 5, 0, 1, 0, 1),
(8, 43, '4647787', 'AC Fuel', 'Jim Dodd', 7, 0, 0, 1, 4),
(9, 50, '1234567', 'Insurance Company X', 'Jane Doe', 7, 1, 1, 0, 5),
(10, 51, '2345678', 'Leasing Company X', 'Jane Doe', 7, 1, 1, 1, 4),
(11, 52, '3456789', 'Fuel Company X', 'Jane Doe', 0, 1, 1, 0, 6);

DROP TABLE IF EXISTS `vendor_status`;
CREATE TABLE IF NOT EXISTS `vendor_status` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(45) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


ALTER TABLE `bank_account`
  ADD CONSTRAINT `fk_bank_account_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_bank_account_limit_type` FOREIGN KEY (`limit_type`) REFERENCES `bank_account_limit_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_bank_account_payment_type` FOREIGN KEY (`payment_type`) REFERENCES `payment_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `bank_account_ach`
  ADD CONSTRAINT `fk_bank_account_ach_bank_account_id` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_account` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `bank_account_cc`
  ADD CONSTRAINT `fk_bank_account_cc_bank_account_id` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_account` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `bank_account_check`
  ADD CONSTRAINT `fk_bank_account_check_bank_account_id` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_account` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `bank_account_history`
  ADD CONSTRAINT `fk_bank_account_history_bank_account_id` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_bank_account_history_payment_type` FOREIGN KEY (`payment_type`) REFERENCES `payment_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `carrier`
  ADD CONSTRAINT `fk_carrier_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `carrier_contractor`
  ADD CONSTRAINT `fk_carrier_contractor_carrier_id` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_carrier_contractor_contractor_id` FOREIGN KEY (`contractor_id`) REFERENCES `contractor` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_carrier_contractor_status` FOREIGN KEY (`status`) REFERENCES `contractor_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `carrier_vendor`
  ADD CONSTRAINT `fk_carrier_vendor_carrier_id` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_carrier_vendor_status` FOREIGN KEY (`status`) REFERENCES `vendor_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_carrier_vendor_vendor_id` FOREIGN KEY (`vendor_id`) REFERENCES `vendor` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `contractor`
  ADD CONSTRAINT `fk_contactor_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_contractor_correspondence_method` FOREIGN KEY (`correspondence_method`) REFERENCES `entity_contact_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `cycle_date`
  ADD CONSTRAINT `fk_cycle_date_cycle_owner` FOREIGN KEY (`cycle_owner`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_cycle_date_cycle_type` FOREIGN KEY (`cycle_type`) REFERENCES `cycle_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `deduction_setup`
  ADD CONSTRAINT `fk_deduction_setup_billing_cycle_id` FOREIGN KEY (`billing_cycle_id`) REFERENCES `cycle_period` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_deduction_setup_level_id` FOREIGN KEY (`level_id`) REFERENCES `setup_level` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_deduction_setup_provider_id` FOREIGN KEY (`provider_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_deduction_setup_reserve_account_receiver` FOREIGN KEY (`reserve_account_receiver`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `deductions`
  ADD CONSTRAINT `fk_deductions_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_deductions_contractor_entity_id` FOREIGN KEY (`contractor_id`) REFERENCES `contractor` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_deductions_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_deductions_settlement_cycle_id` FOREIGN KEY (`settlement_cycle_id`) REFERENCES `settlement_cycle` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_deductions_setup_id` FOREIGN KEY (`setup_id`) REFERENCES `deduction_setup` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_deductions_source_id` FOREIGN KEY (`source_id`) REFERENCES `file_storage` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_deductions_status` FOREIGN KEY (`status`) REFERENCES `payment_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `deductions_temp`
  ADD CONSTRAINT `payment_temp_status_id0` FOREIGN KEY (`status_id`) REFERENCES `payment_temp_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `disbursement_check`
  ADD CONSTRAINT `fk_disburstment_check_disburstment_id` FOREIGN KEY (`disburstment_id`) REFERENCES `disbursement_transaction` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `disbursement_transaction`
  ADD CONSTRAINT `fk_disbursement_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_disbursement_bank_account_history_id` FOREIGN KEY (`bank_account_history_id`) REFERENCES `bank_account_history` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_disbursement_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_disbursement_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_disbursement_process_type` FOREIGN KEY (`process_type`) REFERENCES `disbursement_transaction_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_disbursement_status` FOREIGN KEY (`status`) REFERENCES `payment_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `entity`
  ADD CONSTRAINT `fk_entity_entity_type_id` FOREIGN KEY (`entity_type_id`) REFERENCES `entity_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_entity_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `entity_contact_info`
  ADD CONSTRAINT `fk_entity_contact_info_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_contact_info_contact_type` FOREIGN KEY (`contact_type`) REFERENCES `entity_contact_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `file_storage`
  ADD CONSTRAINT `fk_file_storage_file_type` FOREIGN KEY (`file_type`) REFERENCES `file_storage_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `payment_setup`
  ADD CONSTRAINT `fk_payment_setup_billing_cycle_id` FOREIGN KEY (`billing_cycle_id`) REFERENCES `cycle_period` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_payment_setup_carrier_id` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_payment_setup_level_id` FOREIGN KEY (`level_id`) REFERENCES `setup_level` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_payments_contractor_entity_id` FOREIGN KEY (`contractor_id`) REFERENCES `contractor` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_payments_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_payments_settlement_cycle_id` FOREIGN KEY (`settlement_cycle_id`) REFERENCES `settlement_cycle` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_payments_setup_id` FOREIGN KEY (`setup_id`) REFERENCES `payment_setup` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_payments_source_id` FOREIGN KEY (`source_id`) REFERENCES `file_storage` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_payments_status` FOREIGN KEY (`status`) REFERENCES `payment_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `payments_temp`
  ADD CONSTRAINT `payment_temp_status_id` FOREIGN KEY (`status_id`) REFERENCES `payment_temp_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `reserve_account`
  ADD CONSTRAINT `fk_reserve_account_bank_account_id` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_reserve_account_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `reserve_account_carrier`
  ADD CONSTRAINT `fk_reserve_account_carrier_reserve_account_id` FOREIGN KEY (`reserve_account_id`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `reserve_account_contractor`
  ADD CONSTRAINT `fk_reserve_account_contractor_reserve_account_id` FOREIGN KEY (`reserve_account_id`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_reserve_account_contractor_reserve_account_vendor_id` FOREIGN KEY (`reserve_account_vendor_id`) REFERENCES `reserve_account_vendor` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `reserve_account_vendor`
  ADD CONSTRAINT `fk_reserve_account_vendor_reserve_account_id` FOREIGN KEY (`reserve_account_id`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `reserve_transaction`
  ADD CONSTRAINT `fk_reserve_transaction_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_reserve_transaction_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_reserve_transaction_deduction_id` FOREIGN KEY (`deduction_id`) REFERENCES `deductions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_reserve_transaction_reserve_account_receiver` FOREIGN KEY (`reserve_account_receiver`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_reserve_transaction_reserve_account_sender` FOREIGN KEY (`reserve_account_sender`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_reserve_transaction_settlement_cycle_id` FOREIGN KEY (`settlement_cycle_id`) REFERENCES `settlement_cycle` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_reserve_transaction_source_id` FOREIGN KEY (`source_id`) REFERENCES `file_storage` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_reserve_transaction_status` FOREIGN KEY (`status`) REFERENCES `payment_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_reserve_transaction_type` FOREIGN KEY (`type`) REFERENCES `reserve_transaction_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `settlement_cycle`
  ADD CONSTRAINT `fk_settlement_cycle_carrier_id` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_settlement_cycle_cycle_period_id` FOREIGN KEY (`cycle_period_id`) REFERENCES `cycle_period` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_settlement_cycle_status_id` FOREIGN KEY (`status_id`) REFERENCES `settlement_cycle_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role_id` FOREIGN KEY (`role_id`) REFERENCES `user_role` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `users_visibility`
  ADD CONSTRAINT `fk_users_visibility_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_users_visibility_participant_id` FOREIGN KEY (`participant_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `vendor`
  ADD CONSTRAINT `fk_vendor_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
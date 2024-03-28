/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
DROP TABLE IF EXISTS `contractor_temp`;
DROP TABLE IF EXISTS `contractor_vendor_temp`;
DROP TABLE IF EXISTS `entity_contact_info_temp`;
DROP TABLE IF EXISTS `bank_account_temp`;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;

CREATE TABLE `contractor_temp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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

  `status_id` int(10) unsigned NOT NULL,
  `error` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `source_id` int(10) unsigned NULL DEFAULT NULL,

  PRIMARY KEY (`id`),
  KEY `fk_contractor_temp_status_id` (`status_id`),
  CONSTRAINT `fk_contractor_temp_status_id` FOREIGN KEY (`status_id`) REFERENCES `payment_temp_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `contractor_vendor_temp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` int(10) unsigned NULL DEFAULT NULL,
  `vendor_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `status` int(10) unsigned NULL DEFAULT NULL,

  `status_id` int(10) unsigned NOT NULL,
  `error` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `source_id` int(10) unsigned NULL DEFAULT NULL,
  `contractor_temp_id`int(10) unsigned NOT NULL,

  PRIMARY KEY (`id`),
  KEY `fk_contractor_vendor_temp_status_id` (`status_id`),
  KEY `fk_contractor_vendor_temp_contractor_temp_id` (`contractor_temp_id`),
  CONSTRAINT `fk_contractor_vendor_temp_status_id` FOREIGN KEY (`status_id`) REFERENCES `payment_temp_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_contractor_vendor_temp_contractor_temp_id` FOREIGN KEY (`contractor_temp_id`) REFERENCES `contractor_temp` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `entity_contact_info_temp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,

  `contact_type` int(10) unsigned NULL DEFAULT NULL,
  `value` text COLLATE utf8_bin NULL DEFAULT NULL,

  `status_id` int(10) unsigned NOT NULL,
  `error` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `source_id` int(10) unsigned NULL DEFAULT NULL,
  `contractor_temp_id`int(10) unsigned NOT NULL,

  PRIMARY KEY (`id`),
  KEY `fk_entity_contact_info_temp_status_id` (`status_id`),
  KEY `fk_entity_contact_info_temp_contractor_temp_id` (`contractor_temp_id`),
  CONSTRAINT `fk_entity_contact_info_temp_status_id` FOREIGN KEY (`status_id`) REFERENCES `payment_temp_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_entity_contact_info_temp_contractor_temp_id` FOREIGN KEY (`contractor_temp_id`) REFERENCES `contractor_temp` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `bank_account_temp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,

  `account_nickname` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `payment_type` int(10) unsigned NULL DEFAULT NULL,
  `account_type` int(10) unsigned NULL DEFAULT NULL,
  `name_on_account` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `percentage` decimal(10,4) DEFAULT NULL,
  `priority` int(10) DEFAULT NULL,
  `limit_type` int(10) unsigned NULL DEFAULT NULL,
  `ACH_bank_routing_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `ACH_bank_account_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `card_number` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `limit_value` varchar(20) NULL DEFAULT NULL,

  `status_id` int(10) unsigned NOT NULL,
  `error` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `source_id` int(10) unsigned NULL DEFAULT NULL,
  `contractor_temp_id`int(10) unsigned NOT NULL,

  PRIMARY KEY (`id`),
  KEY `fk_bank_account_temp_status_id` (`status_id`),
  KEY `fk_bank_account_temp_contractor_temp_id` (`contractor_temp_id`),
  CONSTRAINT `fk_bank_account_temp_status_id` FOREIGN KEY (`status_id`) REFERENCES `payment_temp_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bank_account_temp_contractor_temp_id` FOREIGN KEY (`contractor_temp_id`) REFERENCES `contractor_temp` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `file_storage_type` (`id`, `title`) VALUES (3, 'Contractors'), (4, 'Contacts'), (5, 'Contractor-Vendor'), (6, 'Bank Accounts');
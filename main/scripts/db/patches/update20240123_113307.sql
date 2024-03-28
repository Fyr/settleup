DROP TABLE IF EXISTS `reserve_account_contractor_temp`;

CREATE TABLE `reserve_account_contractor_temp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contractor_code` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `vendor_code` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `vendor_reserve_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `min_balance` decimal(18,2) DEFAULT NULL,
  `contribution_amount` decimal(18,2) DEFAULT '0.00',
  `error` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `status_id` int(10) unsigned NOT NULL,
  `source_id` int(10) unsigned NOT NULL,
  `entity_id` int(10) unsigned DEFAULT '0',
  `initial_balance` decimal(18,2) DEFAULT NULL,
  `current_balance` decimal(18,2) DEFAULT NULL,
  `reserve_account_vendor_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `reserve_account_vendor_temp`;

CREATE TABLE `reserve_account_vendor_temp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8_bin NOT NULL,
  `account_name` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `vendor_reserve_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `min_balance` decimal(18,2) DEFAULT NULL,
  `contribution_amount` decimal(18,2) DEFAULT '0.00',
  `error` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `status_id` int(10) unsigned NOT NULL,
  `source_id` int(10) unsigned NOT NULL,
  `entity_id` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `vendor_temp`;

CREATE TABLE `vendor_temp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `priority` int(10) DEFAULT NULL,
  `carrier_id` int(10) unsigned DEFAULT NULL,
  `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `status_id` int(10) unsigned NOT NULL,
  `source_id` int(10) unsigned NOT NULL,
  `error` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


INSERT INTO `file_storage_type` (`id`, `title`) VALUES 
(8, 'Vendors'),
(9, 'Vendor Reserve Accounts'),
(10, 'Contractor Reserve Accounts');


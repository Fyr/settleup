DROP TABLE IF EXISTS `powerunit`;
CREATE TABLE `powerunit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(10) unsigned NOT NULL,
  `contractor_id` int(10) unsigned NOT NULL,
  `contractor_code` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `contractor_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `start_date` date DEFAULT NULL,
  `termination_date` date DEFAULT NULL,
  `status` int(10) unsigned NOT NULL,
  `domicile` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `plate_owner` int(10) unsigned NOT NULL,
  `form2290` tinyint(1) NOT NULL DEFAULT '0',
  `ifta_filing_owner` int(10) unsigned NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_carrier_id` (`carrier_id`),
  KEY `fk_contractor_id` (`contractor_id`),
  CONSTRAINT `fk_carrier_id` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_contractor_id` FOREIGN KEY (`contractor_id`) REFERENCES `contractor` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
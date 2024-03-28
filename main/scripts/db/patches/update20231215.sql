DROP TABLE IF EXISTS `powerunit_temp`;
CREATE TABLE `powerunit_temp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(10) unsigned NOT NULL,
  `contractor_code` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,

  `start_date` date DEFAULT NULL,
  `termination_date` date DEFAULT NULL,
  `status` int(10) unsigned NOT NULL,
  `domicile` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `plate_owner` int(10) unsigned NOT NULL,
  `form2290` tinyint(1) NOT NULL DEFAULT '0',
  `ifta_filing_owner` int(10) unsigned NOT NULL,
  `error` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `status_id` int(10) unsigned NOT NULL,
  `source_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
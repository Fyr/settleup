CREATE TABLE IF NOT EXISTS `entity_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cycle_id` int(10) unsigned NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `type_id` tinyint(1) unsigned NOT NULL,
  `data` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_cycle_id` (`cycle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
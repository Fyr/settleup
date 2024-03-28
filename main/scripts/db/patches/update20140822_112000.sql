CREATE TABLE IF NOT EXISTS `escrow_accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(10) unsigned NOT NULL,
  `escrow_account_holder` varchar(255) NOT NULL,
  `holder_federal_tax_id` varchar(255) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `bank_routing_number` varchar(255) NOT NULL,
  `bank_account_number` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `carrier_id` (`carrier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE  `escrow_accounts` ADD FOREIGN KEY (  `carrier_id` ) REFERENCES `carrier` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION ;
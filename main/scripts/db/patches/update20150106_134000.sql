ALTER TABLE `escrow_accounts` ADD COLUMN `next_check_number` INT(10) UNSIGNED NULL DEFAULT '1';

CREATE TABLE `escrow_accounts_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(10) unsigned NOT NULL,
  `escrow_account_holder` varchar(255) NOT NULL,
  `holder_federal_tax_id` varchar(255) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `bank_routing_number` varchar(255) NOT NULL,
  `bank_account_number` varchar(255) NOT NULL,
  `next_check_number` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carrier_id` (`carrier_id`),
  CONSTRAINT `escrow_accounts_carrier_id` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE `disbursement_transaction` ADD COLUMN `escrow_account_history_id` INT(10) UNSIGNED NULL DEFAULT NULL;

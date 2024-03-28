DROP TABLE IF EXISTS `carrier_contractor`;

ALTER TABLE `bank_account` CHANGE COLUMN `account_type` `account_type` INT(10) UNSIGNED NULL DEFAULT NULL;
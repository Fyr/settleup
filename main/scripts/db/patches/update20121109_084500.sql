ALTER TABLE `pfleet`.`disbursement_transaction` DROP COLUMN `sender_name`;
ALTER TABLE `pfleet`.`bank_account` CHANGE COLUMN `amount` `amount` DECIMAL(10,2) NULL DEFAULT NULL;
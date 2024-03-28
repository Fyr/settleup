ALTER TABLE `pfleet`.`deductions` CHANGE COLUMN `amount` `amount` DECIMAL(10,4) NULL DEFAULT NULL;
ALTER TABLE `pfleet`.`payments` ADD COLUMN `balance` DECIMAL(10,4) NULL DEFAULT 0  AFTER `amount`;

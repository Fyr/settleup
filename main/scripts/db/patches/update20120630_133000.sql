ALTER TABLE `pfleet`.`payment_setup` ADD COLUMN `first_start_day` INT(10) NULL DEFAULT NULL  AFTER `rate` , ADD COLUMN `second_start_day` INT(10) NULL DEFAULT NULL  AFTER `first_start_day` , DROP FOREIGN KEY `fk_payment_setup_contractor_id`;
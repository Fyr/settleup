ALTER TABLE `deductions` CHANGE COLUMN `status` `status` INT(10) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `payments` CHANGE COLUMN `status` `status` INT(10) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `reserve_transaction` CHANGE COLUMN `status` `status` INT(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `payments` ADD COLUMN `deleted_in_cycle` INT(10) UNSIGNED DEFAULT NULL AFTER `added_in_cycle`;
ALTER TABLE `deductions` ADD COLUMN `deleted_in_cycle` INT(10) UNSIGNED DEFAULT NULL AFTER `added_in_cycle`;
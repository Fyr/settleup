ALTER TABLE `payments_temp` CHANGE COLUMN `contractor_id` `contractor_id` INT(10) UNSIGNED NOT NULL DEFAULT 0 ;
ALTER TABLE `deductions_temp` CHANGE COLUMN `contractor_id` `contractor_id` INT(10) UNSIGNED NOT NULL DEFAULT 0 ;

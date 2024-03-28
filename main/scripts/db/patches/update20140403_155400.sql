ALTER TABLE  `deductions` CHANGE  `billing_cycle_id`  `billing_cycle_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
UPDATE `deductions` SET `billing_cycle_id` = NULL WHERE `recurring` = 0;
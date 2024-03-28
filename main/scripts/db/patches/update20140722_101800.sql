ALTER TABLE  `payments` ADD  `recurring_parent_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `deductions` ADD  `recurring_parent_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `payments` ADD  `added_in_cycle` INT( 10 ) UNSIGNED NULL DEFAULT NULL AFTER  `settlement_cycle_id` ,
ADD INDEX (  `added_in_cycle` ) ;
ALTER TABLE  `deductions` ADD  `added_in_cycle` INT( 10 ) UNSIGNED NULL DEFAULT NULL AFTER  `settlement_cycle_id` ,
ADD INDEX (  `added_in_cycle` ) ;


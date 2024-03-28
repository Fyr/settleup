use pfleet;

ALTER TABLE `carrier` DROP FOREIGN KEY `fk_carrier_settlement_cycle` ;

ALTER TABLE `carrier` DROP `settlement_cycle` ,
DROP `settlement_day` ,
DROP `recurring_payments` ,
DROP `payment_terms` ;

ALTER TABLE `deductions` DROP `cycle_close_date` ,
DROP `adjusted_balance_use` ,
DROP `reserve_account_contractor` ;

ALTER TABLE `deduction_setup` DROP FOREIGN KEY `fk_deduction_setup_reserve_account_sender` ;

ALTER TABLE `deduction_setup` DROP `last_recurring_date` ,
DROP `last_cycle_close_day` ,
DROP `reserve_account_sender` ;

ALTER TABLE `payments` DROP `cycle_close_date` ;

ALTER TABLE `payment_setup` DROP `last_recurring_date` ,
DROP `cycle_close_date` ;

ALTER TABLE `users` CHANGE `last_selected_carrier` `last_selected_carrier` INT( 10 ) NULL DEFAULT NULL;
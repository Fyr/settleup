UPDATE `pfleet`.`deduction_setup` SET `contractor_id` = NULL WHERE `deduction_setup`.`level_id` =1;
UPDATE `pfleet`.`payment_setup` SET `contractor_id` = NULL WHERE `payment_setup`.`level_id` =1;
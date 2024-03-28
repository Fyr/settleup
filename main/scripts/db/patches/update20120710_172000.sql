UPDATE `pfleet`.`payments` SET `quantity` = '1',
`amount` = '75',
`balance` = '75' WHERE `payments`.`id` =1;

UPDATE `pfleet`.`deduction_setup` SET `provider_id` = '9',
`reserve_account_receiver` = '5' WHERE `deduction_setup`.`id` =1;

UPDATE `pfleet`.`deduction_setup` SET `provider_id` = '9',
`reserve_account_receiver` = '5' WHERE `deduction_setup`.`id` =2;

UPDATE `pfleet`.`deduction_setup` SET `provider_id` = '9',
`reserve_account_receiver` = '5' WHERE `deduction_setup`.`id` =3;

INSERT INTO `pfleet`.`reserve_account` (`id`, `entity_id`, `bank_account_id`, `account_name`, `description`, `priority`, `min_balance`, `contribution_amount`, `max_withdrawal_amount`, `initial_balance`, `current_balance`, `disbursement_code`) VALUES
(7, 2, 2, 'Penske account Name', 'Penske description', NULL, '60.0000', '40.0000', '888.0000', '999.0000', '3000.0000', 'my code');

INSERT INTO `pfleet`.`reserve_account_contractor` (`id`, `reserve_account_id`, `reserve_account_vendor_id`) VALUES
(5, 7, 1);
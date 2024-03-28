use `pfleet`;

INSERT INTO `bank_account` (`id`, `entity_id`, `account_nickname`, `payment_type`, `process`, `account_type`, `name_on_account`, `amount`, `percentage`, `priority`, `limit_type`) VALUES
(7, 16, 'CON1Account', 1, '', '', 'CON1Account', NULL, '100.0000', 0, 1),
(8, 17, 'CON2Account', 1, '', '', 'CON2Account', NULL, '100.0000', 0, 1),
(9, 18, 'CON3Account', 1, '', '', 'CON3Account', NULL, '100.0000', 0, 1);

INSERT INTO `bank_account_check` (`id`, `bank_account_id`, `bank_name`) VALUES
(6, 7, 'Priorbank'),
(7, 8, 'Priorbank'),
(8, 9, 'Priorbank');


UPDATE `pfleet`.`reserve_account` SET `bank_account_id` = '7' WHERE `reserve_account`.`id` =10;
UPDATE `pfleet`.`reserve_account` SET `bank_account_id` = '7' WHERE `reserve_account`.`id` =11;
UPDATE `pfleet`.`reserve_account` SET `bank_account_id` = '8' WHERE `reserve_account`.`id` =12;
UPDATE `pfleet`.`reserve_account` SET `bank_account_id` = '8' WHERE `reserve_account`.`id` =13;
UPDATE `pfleet`.`reserve_account` SET `bank_account_id` = '9' WHERE `reserve_account`.`id` =14;
UPDATE `pfleet`.`reserve_account` SET `bank_account_id` = '9' WHERE `reserve_account`.`id` =15;
use pfleet;
INSERT INTO `bank_account` (`id`, `entity_id`, `account_nickname`, `payment_type`, `process`, `account_type`, `name_on_account`, `amount`, `percentage`, `priority`, `limit_type`) VALUES
(10, 15, 'CAR1', 1, '', '', 'CAR1 Account', '100.0000', NULL, 0, 2);
INSERT INTO `bank_account_check` (`id`, `bank_account_id`, `bank_name`) VALUES
(9, 10, 'Priorbank');
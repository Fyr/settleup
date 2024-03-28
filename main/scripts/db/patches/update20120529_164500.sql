SET FOREIGN_KEY_CHECKS=0;
USE `pfleet` ;

INSERT INTO `bank_account` (`id`, `entity_id`, `account_nickname`, `payment_type`, `process`, `account_type`, `name_on_account`, `amount`, `percentage`) VALUES
(1, 1, 'Priorbank', 2, 'some process', 'Priorbank Account Type', 'Priorbank Name', '10.0000', '9.9900'),
(2, 1, 'National', 1, 'National Bank process', 'National Bank Account Type', 'National Bank name', '10.0000', '15.0000');

INSERT INTO `bank_account_ach` (`id`, `bank_account_id`, `ACH_bank_routing_id`, `ACH_bank_account_id`) VALUES
(1, 1, '1234', '5678');

INSERT INTO `bank_account_check` (`id`, `bank_account_id`, `bank_name`) VALUES
(1, 2, 'National Bank');

INSERT INTO `reserve_account` (`id`, `entity_id`, `bank_account_id`, `account_name`, `description`, `priority`, `min_balance`, `contribution_amount`, `max_withdrawal_amount`, `initial_balance`, `current_balance`, `disbursement_code`) VALUES
(1, 3, 1, 'Navibulgar reserve account', 'some description', 1, '1.0000', '10.0000', '10000.0000', '500.0000', '1000.0000', '1234567890'),
(2, 4, 1, 'John''s Account', 'John''s Description', 2, '1.0000', '123.0000', '345.0000', '500.0000', '2000.0000', '123456'),
(3, 8, 2, 'Gonazales account', 'blablabla description', 0, '200.0000', '400.0000', '600.0000', '30.0000', '60.0000', 'code'),
(4, 5, 2, 'Best Acc', 'best description', 3, '10.0000', '20.0000', '30.0000', '40.0000', '50.0000', '666'),
(5, 9, 2, 'Penske account Name', 'Penske description', 0, '60.0000', '40.0000', '888.0000', '999.0000', '60.0000', 'my code'),
(6, 10, 2, 'Soso account', 'Soso some description', NULL, '1.0000', '5.0000', '4.0000', '2.0000', '100500.0000', 'soso code');

INSERT INTO `reserve_account_contractor` (`id`, `reserve_account_id`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4);

INSERT INTO `reserve_account_vendor` (`id`, `reserve_account_id`, `vendor_reserve_code`) VALUES
(1, 5, 'code'),
(2, 6, '123456');

INSERT INTO `users` (`id`, `role_id`, `email`, `name`, `password`, `last_login_ip`) VALUES
(6, NULL, 'bivi@mail.by', 'bivi', '05546b0e38ab9175cd905eebcc6ebb76', '127.0.0.1');

SET FOREIGN_KEY_CHECKS=1;

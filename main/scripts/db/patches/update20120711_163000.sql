INSERT INTO `pfleet`.`bank_account` (`id`, `entity_id`, `account_nickname`, `payment_type`, `process`, `account_type`, `name_on_account`, `amount`, `percentage`, `priority`, `limit_type`) VALUES
(3, 14, 'Ven1AmountAccount', 1, '', '', 'Ven1AmountAccount', '200.0000', NULL, 2, 2),
(4, 14, 'Ven1PersentAccount', 1, '', '', 'Ven1PersentAccount', NULL, '50.0000', 3, 1),
(5, 15, 'Ven2AmountAccount', 1, '', '', 'Ven2AmountAccount', '200.0000', NULL, 4, 2),
(6, 15, 'Ven2PersentAccount', 1, '', '', 'Ven2AmountAccount', NULL, '50.0000', 5, 1);

INSERT INTO `pfleet`.`bank_account_check` (`id`, `bank_account_id`, `bank_name`) VALUES
(2, 3, 'Priorbank'),
(3, 4, 'BPSBank'),
(4, 5, 'Priorbank'),
(5, 6, 'BPSBank');

INSERT INTO `pfleet`.`reserve_account` (`id`, `entity_id`, `bank_account_id`, `account_name`, `description`, `priority`, `min_balance`, `contribution_amount`, `max_withdrawal_amount`, `initial_balance`, `current_balance`, `disbursement_code`) VALUES
(8, 19, 3, 'Ven1AmountAccount', 'VasilAccount', 1, '400.0000', '200.0000', '500.0000', '0.0000', '1000.0000', ''),
(9, 20, 5, 'Ven2AmountAccount', 'ValeraAccount', 0, '300.0000', '150.0000', '300.0000', '0.0000', '500.0000', '');

INSERT INTO `pfleet`.`reserve_account_vendor` (`id`, `reserve_account_id`, `vendor_reserve_code`) VALUES
(3, 8, 'V1RA'),
(4, 9, 'V2RA');

INSERT INTO `pfleet`.`reserve_account` (`id`, `entity_id`, `bank_account_id`, `account_name`, `description`, `priority`, `min_balance`, `contribution_amount`, `max_withdrawal_amount`, `initial_balance`, `current_balance`, `disbursement_code`) VALUES
(10, 16, 3, 'Ven1AmountAccount', 'VasilAccount', 1, '400.0000', '200.0000', '500.0000', '1000.0000', '170.0000', ''),
(11, 16, 5, 'Ven2AmountAccount', 'ValeraAccount', 0, '300.0000', '150.0000', '300.0000', '500.0000', '400.0000', '');

INSERT INTO `pfleet`.`reserve_account_contractor` (`id`, `reserve_account_id`, `reserve_account_vendor_id`) VALUES
(6, 10, 3),
(7, 11, 4);
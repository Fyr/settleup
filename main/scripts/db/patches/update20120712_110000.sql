INSERT INTO `pfleet`.`reserve_account` (`id`, `entity_id`, `bank_account_id`, `account_name`, `description`, `priority`, `min_balance`, `contribution_amount`, `max_withdrawal_amount`, `initial_balance`, `current_balance`, `disbursement_code`) VALUES
(12, 17, 3, 'Ven1AmountAccount', 'VasilAccount', 1, '400.0000', '200.0000', '500.0000', '1000.0000', '200.0000', ''),
(13, 17, 5, 'Ven2AmountAccount', 'ValeraAccount', 2, '300.0000', '150.0000', '300.0000', '500.0000', '40.0000', ''),
(14, 18, 3, 'Ven1AmountAccount', 'VasilAccount', 3, '400.0000', '200.0000', '500.0000', '1000.0000', '500.0000', ''),
(15, 18, 5, 'Ven2AmountAccount', 'ValeraAccount', 0, '300.0000', '150.0000', '300.0000', '500.0000', '300.0000', '');

INSERT INTO `pfleet`.`reserve_account_contractor` (`id`, `reserve_account_id`, `reserve_account_vendor_id`) VALUES
(8, 12, 3),
(9, 13, 4),
(10, 14, 3),
(11, 15, 4);

INSERT INTO `pfleet`.`payment_setup` (`id`, `carrier_id`, `contractor_id`, `payment_code`, `carrier_payment_code`, `description`, `category`, `terms`, `department`, `gl_code`, `disbursement_code`, `recurring`, `level_id`, `billing_cycle_id`, `rate`) VALUES
(5, 15, NULL, '', '', 'hourly', 'PS1', 0, '', '', '', 1, 1, 1, '20.0000'),
(6, 15, 9, '', '', 'overtime', 'PS2', 0, '', '', '', 0, 2, 2, '30.0000'),
(7, 15, 10, '', '', 'commission', 'PS3', 14, '', '', '', 0, 2, 2, '300.0000');

INSERT INTO `pfleet`.`deduction_setup` (`id`, `provider_id`, `contractor_id`, `vendor_deduction_code`, `description`, `category`, `department`, `gl_code`, `disbursement_code`, `priority`, `recurring`, `level_id`, `billing_cycle_id`, `terms`, `rate`, `eligible`, `reserve_account_receiver`) VALUES
(4, 15, 16, '', 'uniforms', 'DS1', '', '', '', NULL, 0, 2, 2, 3, '150.0000', 0, NULL),
(5, 19, NULL, '', 'health insurance', 'DS2', '', '', '', NULL, 1, 1, 2, 0, '300.0000', 0, 8),
(6, 20, NULL, '', 'phone service', 'DS3', '', '', '', NULL, 1, 1, 2, 3, '100.0000', 0, 9);

INSERT INTO `pfleet`.`settlement_cycle` (`id`, `carrier_id`, `cycle_period_id`, `payment_terms`, `disbursement_terms`, `cycle_start_date`, `cycle_close_date`, `status_id`, `first_start_day`, `second_start_day`) VALUES
(2, 15, 1, 2, 5, '2012-07-10', '2012-07-17', 1, NULL, NULL);

INSERT INTO `pfleet`.`payments` (`id`, `setup_id`, `category`, `description`, `invoice`, `invoice_date`, `invoice_due_date`, `department`, `gl_code`, `quantity`, `rate`, `amount`, `balance`, `check_id`, `disbursement_date`, `approved_datetime`, `approved_by`, `created_datetime`, `created_by`, `source_id`, `status`, `settlement_cycle_id`, `contractor_id`) VALUES
(16, 5, 'PS1', 'hourly', '', '2012-07-12', '2012-07-12', '', '', 160, '20.0000', '3200.0000', '0.0000', '', '2012-07-22', NULL, NULL, '2012-07-12 10:25:44', 6, NULL, 2, 2, 8),
(17, 5, 'PS1', 'hourly', '', '2012-07-12', '2012-07-12', '', '', 80, '20.0000', '1600.0000', '0.0000', '', '2012-07-22', NULL, NULL, '2012-07-12 10:25:44', 6, NULL, 2, 2, 9),
(18, 5, 'PS1', 'hourly', '', '2012-07-12', '2012-07-12', '', '', 1, '20.0000', '20.0000', '0.0000', '', '2012-07-22', NULL, NULL, '2012-07-12 10:25:44', 6, NULL, 2, 2, 10);
INSERT INTO `pfleet`.`payments` (`id`, `setup_id`, `category`, `description`, `invoice`, `invoice_date`, `invoice_due_date`, `department`, `gl_code`, `quantity`, `rate`, `amount`, `check_id`, `disbursement_date`, `approved_datetime`, `approved_by`, `created_datetime`, `created_by`, `source_id`, `status`, `settlement_cycle_id`, `contractor_id`, `balance`) VALUES
(19, 6, 'PS2', 'overtime', '', '2012-07-12', '2012-07-12', '', '', 17, '30.0000', '510.0000', '', '2012-07-22', NULL, NULL, '2012-07-12 14:35:30', 6, NULL, 2, 2, 9, '0.0000'),
(20, 7, 'PS3', 'commission', '', '2012-07-12', '2012-07-26', '', '', 1, '300.0000', '300.0000', '', '2012-07-22', NULL, NULL, '2012-07-12 14:37:32', 6, NULL, 2, 2, 10, '0.0000');

use pfleet;

INSERT INTO `payment_setup` (`id`, `carrier_id`, `contractor_id`, `payment_code`, `carrier_payment_code`, `description`, `category`, `terms`, `department`, `gl_code`, `disbursement_code`, `recurring`, `level_id`, `billing_cycle_id`, `last_recurring_date`, `cycle_close_date`, `rate`) VALUES
(3, 1, 6, 'Anatoli', '234567', 'Southwest Description', 'category', 2250, 'Department', 'code2', 'dCode', 1, 2, 3, '0000-00-00', '0000-00-00', '150.0000');

INSERT INTO `payments` (`id`, `setup_id`, `category`, `description`, `invoice`, `invoice_date`, `invoice_due_date`, `department`, `gl_code`, `quantity`, `rate`, `amount`, `check_id`, `disbursement_date`, `cycle_close_date`, `approved_datetime`, `approved_by`, `created_datetime`, `created_by`, `source_id`, `status`, `settlement_cycle_id`, `contractor_id`) VALUES
(4, 2, 'Mileage', 'Mileage - Standard', 'Invoice', '2012-06-30', '2012-06-30', 'Department', '4234', 20, '0.9870', '19.7400', '', '2012-06-15', '2012-05-31', '0000-00-00 00:00:00', NULL, '2012-06-15 17:38:09', 6, 1, 2, 1, 2),
(5, 3, 'category', 'Southwest Description', '', '2012-06-15', '2012-06-15', 'Department', 'code2', 140, '150.0000', '21000.0000', '', '2012-06-15', '0000-00-00', '0000-00-00 00:00:00', NULL, '2012-06-15 18:00:38', 6, 1, 2, 2, 6),
(6, 3, 'Car', 'Description', '', '2012-06-15', '2012-06-15', 'Department', 'code5', 140, '150.0000', '21000.0000', '', '2012-06-15', '0000-00-00', '0000-00-00 00:00:00', NULL, '2012-06-15 18:00:38', 6, 1, 2, 1, 6);

INSERT INTO `reserve_transaction` (`id`, `reserve_account_sender`, `reserve_account_receiver`, `vendor_code`, `type`, `deduction_id`, `priority`, `amount`, `balance`, `adjusted_balance`, `adjusted_balance_use`, `settlement_cycle_id`, `approved_datetime`, `approved_by`, `created_datetime`, `created_by`, `source_id`, `disbursement_id`, `status`) VALUES
(2, 1, 1, NULL, 1, NULL, NULL, '0.9000', NULL, NULL, NULL, 1, '2012-06-30 17:45:05', 6, '2012-06-20 16:08:56', 6, NULL, NULL, 3);

INSERT INTO `deductions` (`id`, `setup_id`, `category`, `description`, `priority`, `invoice_id`, `invoice_date`, `invoice_due_date`, `department`, `gl_code`, `disbursement_code`, `rate`, `quantity`, `amount`, `disbursement_date`, `cycle_close_date`, `balance`, `adjusted_balance`, `adjusted_balance_use`, `reserve_account_contractor`, `eligible`, `approved_datetime`, `approved_by`, `created_datetime`, `created_by`, `source_id`, `status`, `settlement_cycle_id`, `contractor_id`) VALUES
(7, 2, 'Fuel', 'Fuel Cards', NULL, '', '2012-06-15', '2012-06-15', '', '423423', '', '25.0000', 40, 1000, '2012-06-15', '0000-00-00', '0.0000', '0.0000', '0.0000', 0, 0, '0000-00-00 00:00:00', NULL, '2012-06-15 18:06:39', 6, 1, 2, 1, 2);


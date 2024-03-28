use pfleet;
INSERT INTO `settlement_cycle` (`id`, `carrier_id`, `cycle_period_id`, `settlement_day`, `payment_terms`, `disbursement_terms`, `cycle_start_date`, `cycle_close_date`, `status_id`) VALUES
(2, 1, 3, 1, 1, 1, '2012-06-15', '2012-07-15', 1);

INSERT INTO `deduction_setup` (`id`, `provider_id`, `contractor_id`, `vendor_deduction_code`, `description`, `category`, `department`, `gl_code`, `disbursement_code`, `priority`, `recurring`, `level_id`, `billing_cycle_id`, `terms`, `last_recurring_date`, `last_cycle_close_day`, `cycle_close_date`, `rate`, `eligible`, `reserve_account_sender`, `reserve_account_receiver`) VALUES
(3, 1, 2, '123456', 'Test Deduction Setup', 'Car', 'dep', 'code1', 'code2', NULL, 1, 2, 2, 12345, '0000-00-00', '0000-00-00', '0000-00-00', '20.0000', 0, 1, 1);

INSERT INTO `payments` (`id`, `setup_id`, `category`, `description`, `invoice`, `invoice_date`, `invoice_due_date`, `department`, `gl_code`, `quantity`, `rate`, `amount`, `check_id`, `disbursement_date`, `cycle_close_date`, `approved_datetime`, `approved_by`, `created_datetime`, `created_by`, `source_id`, `status`, `settlement_cycle_id`, `contractor_id`) VALUES
(1, 1, 'Test Payment for SC', 'Delivery - Standard', '', '2012-06-15', '2012-06-15', '', '423423', 20, '75.0000', '1500.0000', '', '2012-06-15', '2012-07-31', '0000-00-00 00:00:00', NULL, '2012-06-15 09:37:00', 6, 1, 2, 2, 1),
(2, 1, 'Test Payment for SC', 'Delivery - Standard', '', '2012-06-15', '2012-06-15', '', '423423', 20, '75.0000', '1500.0000', '', '2012-06-15', '2012-07-31', '0000-00-00 00:00:00', NULL, '2012-06-15 09:37:00', 6, 1, 2, 2, 2);

INSERT INTO `deductions` (`id`, `setup_id`, `category`, `description`, `priority`, `invoice_id`, `invoice_date`, `invoice_due_date`, `department`, `gl_code`, `disbursement_code`, `rate`, `quantity`, `amount`, `disbursement_date`, `cycle_close_date`, `balance`, `adjusted_balance`, `adjusted_balance_use`, `reserve_account_contractor`, `eligible`, `approved_datetime`, `approved_by`, `created_datetime`, `created_by`, `source_id`, `status`, `settlement_cycle_id`, `contractor_id`) VALUES
(3, 1, 'Truck', 'Truck Lease Test', 0, '', '2012-06-30', '2012-06-30', '', '3224', 'FuelCode', '300.0000', 20, 6000, '2012-06-15', '2012-06-30', '0.0000', '0.0000', '0.0000', 0, 1, '0000-00-00 00:00:00', NULL, '2012-06-15 09:48:23', 6, 1, 2, 2, 1),
(4, 1, 'Truck', 'Truck Lease Test', 1, '', '2012-06-15', '2012-06-15', '', '3224', 'FuelCode', '300.0000', 20, 6000, '2012-06-15', '2012-06-30', '0.0000', '0.0000', '0.0000', 0, 1, '0000-00-00 00:00:00', NULL, '2012-06-15 09:48:23', 6, 1, 2, 2, 2),
(5, 3, 'Car', 'Test Deduction Setup', 2, '', '2012-06-16', '2012-06-16', 'dep', 'code1', 'code2', '20.0000', 20, 400, '2012-06-16', '2012-07-30', '0.0000', '0.0000', '0.0000', 0, 0, '2012-06-15 10:01:49', 6, '2012-06-15 09:51:45', 6, 1, 3, 1, 2);


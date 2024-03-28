use pfleet;

INSERT INTO `entity` (`id`, `entity_type_id`, `user_id`) VALUES
(11, 1, 6);

INSERT INTO `carrier` (`id`, `entity_id`, `tax_id`, `short_code`, `name`, `contact`, `settlement_cycle`, `settlement_day`, `recurring_payments`, `payment_terms`) VALUES
(2, 11, '54566789', '45678', 'Valera', 'NY 12-234', 3, 3, 0, 45678);

INSERT INTO `payment_setup` (`id`, `carrier_id`, `contractor_id`, `payment_code`, `carrier_payment_code`, `description`, `category`, `terms`, `department`, `gl_code`, `disbursement_code`, `recurring`, `level_id`, `billing_cycle_id`, `last_recurring_date`, `cycle_close_date`, `rate`) VALUES
(4, 1, NULL, '212038', '56578989', 'Southwest', 'Category 1', 34567, '23456', '4564567', '', 1, 1, 2, '2012-05-05', '2012-05-31', '250.0000');


INSERT INTO `payments` (`id`, `setup_id`, `category`, `description`, `invoice`, `invoice_date`, `invoice_due_date`, `department`, `gl_code`, `quantity`, `rate`, `amount`, `check_id`, `disbursement_date`, `cycle_close_date`, `approved_datetime`, `approved_by`, `created_datetime`, `created_by`, `source_id`, `status`, `settlement_cycle_id`, `contractor_id`) VALUES
(7, 4, 'Category 1', 'Southwest', '', '2012-06-20', '2012-06-20', '23456', '4564567', 6, '250.0000', '1500.0000', '', '2012-06-20', '2012-05-31', '0000-00-00 00:00:00', NULL, '2012-06-20 17:49:30', 6, 1, 2, 2, 1),
(8, 4, 'Category 1', 'Southwest', '', '2012-06-20', '2012-06-20', '23456', '4564567', 6, '250.0000', '1500.0000', '', '2012-06-20', '2012-05-31', '0000-00-00 00:00:00', NULL, '2012-06-20 17:49:30', 6, 1, 2, 2, 2),
(9, 4, 'Test Payment', 'Southwest', '', '2012-06-20', '2012-06-20', '23456', '4564567', 40, '250.0000', '250.0000', '', '2012-06-20', '2012-05-31', '0000-00-00 00:00:00', NULL, '2012-06-20 17:50:26', 6, 1, 2, 2, 1),
(10, 4, 'Test Payment', 'Southwest', '', '2012-06-20', '2012-06-20', '23456', '4564567', 40, '250.0000', '250.0000', '', '2012-06-20', '2012-05-31', '0000-00-00 00:00:00', NULL, '2012-06-20 17:50:26', 6, 1, 2, 2, 2),
(11, 3, 'category 3', 'Southwest Description', '', '2012-06-20', '2012-06-20', 'Department', 'code2', 20, '150.0000', '3000.0000', '', '2012-06-20', '0000-00-00', '0000-00-00 00:00:00', NULL, '2012-06-20 17:51:10', 6, 1, 2, 2, 6),
(12, 3, 'my category', 'Southwest', '', '2012-06-20', '2012-06-20', 'Department', 'code2', 50, '150.0000', '7500.0000', '', '2012-06-20', '0000-00-00', '0000-00-00 00:00:00', NULL, '2012-06-20 17:51:50', 6, 1, 2, 2, 6),
(13, 4, 'first', 'Southwest', '', '2012-06-20', '2012-06-20', '23456', '4564567', 70, '250.0000', '17500.0000', '', '2012-06-20', '2012-05-31', '0000-00-00 00:00:00', NULL, '2012-06-20 17:52:44', 6, 1, 2, 2, 1),
(14, 4, 'first', 'Southwest', '', '2012-06-20', '2012-06-20', '23456', '4564567', 70, '250.0000', '17500.0000', '', '2012-06-20', '2012-05-31', '0000-00-00 00:00:00', NULL, '2012-06-20 17:52:44', 6, 1, 2, 2, 2),
(15, 3, 'category', 'South', '', '2012-06-20', '2012-06-20', 'Department', 'code2', 7, '150.0000', '1050.0000', '', '2012-06-20', '0000-00-00', '0000-00-00 00:00:00', NULL, '2012-06-20 17:53:01', 6, 1, 2, 2, 6);










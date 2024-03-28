
SET FOREIGN_KEY_CHECKS=0;
USE `pfleet` ;

TRUNCATE TABLE deductions;
TRUNCATE TABLE payments;
TRUNCATE TABLE reserve_transaction;
TRUNCATE TABLE settlement_cycle;
TRUNCATE TABLE payment_setup;
TRUNCATE TABLE deduction_setup;

SET FOREIGN_KEY_CHECKS=1;

INSERT INTO `settlement_cycle` (`id`, `carrier_id`, `cycle_period_id`, `settlement_day`, `payment_terms`, `disbursement_terms`, `cycle_start_date`, `cycle_close_date`, `status_id`) VALUES
(1, 1, 3, 1, 0, 5, '2012-06-20', '2012-07-20', 1);

DELETE FROM `pfleet`.`carrier` WHERE `carrier`.`entity_id` = 11;
DELETE FROM `pfleet`.`entity` WHERE `entity`.`id` = 11;

INSERT INTO `payment_setup` (`id`, `carrier_id`, `contractor_id`, `payment_code`, `carrier_payment_code`, `description`, `category`, `terms`, `department`, `gl_code`, `disbursement_code`, `recurring`, `level_id`, `billing_cycle_id`, `last_recurring_date`, `cycle_close_date`, `rate`) VALUES
(1, 1, NULL, 'Delivery', 'Delivery', 'Delivery - Standard', 'Delivery', 0, '', '423423', '', 1, 1, 2, '2012-06-20', '2012-07-20', '75.0000'),
(2, 1, 2, 'Mileage', 'Mileage Std', 'Mileage - Standard', 'Mileage', 0, '', '4234', '', 0, 2, 1, '2012-06-20', '2012-07-20', '0.9870'),
(3, 1, NULL, 'Bonus', 'Bonus', 'Bonus - Standard', 'Bonus', 1, '', '67589', '', 1, 1, 3, '2012-06-20', '2012-07-20', '100.0000'),
(4, 1, NULL, 'Waiting', 'Waiting', 'Waiting - Standard', 'Waiting', 0, '', '4564567', '', 1, 1, 3, '2012-06-20', '2012-07-20', '20.0000');

INSERT INTO `payments` (`id`, `setup_id`, `category`, `description`, `invoice`, `invoice_date`, `invoice_due_date`, `department`, `gl_code`, `quantity`, `rate`, `amount`, `check_id`, `disbursement_date`, `cycle_close_date`, `approved_datetime`, `approved_by`, `created_datetime`, `created_by`, `source_id`, `status`, `settlement_cycle_id`, `contractor_id`) VALUES
(1, 1, 'Delivery', 'Delivery - Standard', '', '2012-06-21', '2012-06-21', '', '423423', 1, '75.0000', '75.0000', '', '2012-06-21', '2012-07-20', '0000-00-00 00:00:00', NULL, '2012-06-21 23:23:10', 6, 1, 2, 1, 1),
(2, 1, 'Delivery', 'Delivery - Standard', '', '2012-06-21', '2012-06-21', '', '423423', 1, '75.0000', '75.0000', '', '2012-06-21', '2012-07-20', '0000-00-00 00:00:00', NULL, '2012-06-21 23:23:10', 6, 1, 2, 1, 2),
(3, 2, 'Mileage', 'Mileage - Standard', '', '2012-06-21', '2012-06-21', '', '4234', 250, '0.9870', '246.7500', '', '2012-06-21', '2012-07-20', '0000-00-00 00:00:00', NULL, '2012-06-21 23:24:23', 6, 1, 2, 1, 2),
(4, 2, 'Mileage', 'Mileage - Standard', '', '2012-06-21', '2012-06-21', '', '4234', 400, '0.9870', '394.8000', '', '2012-06-21', '2012-07-20', '0000-00-00 00:00:00', NULL, '2012-06-21 23:24:53', 6, 1, 2, 1, 2),
(5, 2, 'Mileage', 'Mileage - Standard', '', '2012-06-21', '2012-06-21', '', '4234', 40, '0.9870', '39.4800', '', '2012-06-21', '2012-07-20', '0000-00-00 00:00:00', NULL, '2012-06-21 23:25:35', 6, 1, 2, 1, 2),
(6, 2, 'Mileage', 'Mileage - Standard', '', '2012-06-21', '2012-06-21', '', '4234', 25, '1.0000', '25.0000', '', '2012-06-21', '2012-07-20', '0000-00-00 00:00:00', NULL, '2012-06-21 23:25:57', 6, 1, 2, 1, 2),
(7, 2, 'Mileage', 'Mileage - Standard', '', '2012-06-21', '2012-06-21', '', '4234', 40, '1.2000', '48.0000', '', '2012-06-21', '2012-07-20', '0000-00-00 00:00:00', NULL, '2012-06-21 23:26:50', 6, 1, 2, 1, 2),
(8, 4, 'Waiting', 'Waiting - Standard', '', '2012-06-21', '2012-06-21', '', '4564567', 2, '20.0000', '40.0000', '', '2012-06-21', '2012-07-20', '0000-00-00 00:00:00', NULL, '2012-06-21 23:28:45', 6, 1, 2, 1, 1),
(9, 4, 'Waiting', 'Waiting - Standard', '', '2012-06-21', '2012-06-21', '', '4564567', 2, '20.0000', '40.0000', '', '2012-06-21', '2012-07-20', '0000-00-00 00:00:00', NULL, '2012-06-21 23:28:45', 6, 1, 2, 1, 2),
(10, 3, 'Bonus', 'Bonus - Standard', '', '2012-06-21', '2012-06-22', '', '67589', 1, '100.0000', '100.0000', '', '2012-06-21', '2012-07-20', '0000-00-00 00:00:00', NULL, '2012-06-21 23:29:06', 6, 1, 2, 1, 1),
(11, 3, 'Bonus', 'Bonus - Standard', '', '2012-06-21', '2012-06-22', '', '67589', 1, '100.0000', '100.0000', '', '2012-06-21', '2012-07-20', '0000-00-00 00:00:00', NULL, '2012-06-21 23:29:06', 6, 1, 2, 1, 2),
(12, 2, 'Mileage', 'Mileage - Standard', '', '2012-06-21', '2012-06-21', '', '4234', 23, '0.9870', '22.7010', '', '2012-06-21', '2012-07-20', '0000-00-00 00:00:00', NULL, '2012-06-21 23:29:53', 6, 1, 2, 1, 2),
(13, 1, 'Delivery', 'Delivery - Standard', '', '2012-06-21', '2012-06-21', '', '423423', 1, '75.0000', '75.0000', '', '2012-06-21', '2012-07-20', '0000-00-00 00:00:00', NULL, '2012-06-21 23:30:11', 6, 1, 2, 1, 1),
(14, 1, 'Delivery', 'Delivery - Standard', '', '2012-06-21', '2012-06-21', '', '423423', 1, '75.0000', '75.0000', '', '2012-06-21', '2012-07-20', '0000-00-00 00:00:00', NULL, '2012-06-21 23:30:11', 6, 1, 2, 1, 2),
(15, 2, 'Mileage', 'Mileage - Standard', '', '2012-06-21', '2012-06-21', '', '4234', 65, '0.9870', '64.1550', '', '2012-06-21', '2012-07-20', '0000-00-00 00:00:00', NULL, '2012-06-21 23:30:38', 6, 1, 2, 1, 2);

INSERT INTO `deduction_setup` (`id`, `provider_id`, `contractor_id`, `vendor_deduction_code`, `description`, `category`, `department`, `gl_code`, `disbursement_code`, `priority`, `recurring`, `level_id`, `billing_cycle_id`, `terms`, `last_recurring_date`, `last_cycle_close_day`, `cycle_close_date`, `rate`, `eligible`, `reserve_account_sender`, `reserve_account_receiver`) VALUES
(1, 1, NULL, 'TRL', 'Truck Lease', 'Truck', '', '3224', '', 0, 1, 1, 2, 0, '2012-06-20', '2012-06-20', '2012-07-20', '300.0000', 1, NULL, NULL),
(2, 2, NULL, 'FUL', 'Fuel Cards', 'Fuel', '', '423423', 'FuelCode', 1, 0, 1, 2, 0, '2012-06-20', '2012-06-20', '2012-07-20', '25.0000', 0, NULL, NULL),
(3, 1, NULL, 'MNT', 'Truck Мaintenance', 'Мaintenance', '', '65786798', '', 2, 1, 1, 2, 12345, '2012-06-20', '2012-06-20', '2012-07-20', '50.0000', 0, NULL, NULL);

INSERT INTO `deductions` (`id`, `setup_id`, `category`, `description`, `priority`, `invoice_id`, `invoice_date`, `invoice_due_date`, `department`, `gl_code`, `disbursement_code`, `rate`, `quantity`, `amount`, `disbursement_date`, `cycle_close_date`, `balance`, `adjusted_balance`, `adjusted_balance_use`, `reserve_account_contractor`, `eligible`, `approved_datetime`, `approved_by`, `created_datetime`, `created_by`, `source_id`, `status`, `settlement_cycle_id`, `contractor_id`) VALUES
(1, 1, 'Delivery', 'Delivery - Standard', NULL, '', '2012-06-21', '2012-06-21', '', '423423', '', '75.0000', 1, 75, '2012-06-21', '2012-07-20', '0.0000', '0.0000', '0.0000', 0, 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:56:17', 6, 1, 2, 1, 1),
(2, 1, 'Delivery', 'Delivery - Standard', NULL, '', '2012-06-21', '2012-06-21', '', '423423', '', '75.0000', 1, 75, '2012-06-21', '2012-07-20', '0.0000', '0.0000', '0.0000', 0, 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:56:17', 6, 1, 2, 1, 2),
(3, 2, 'Mileage', 'Mileage - Standard', NULL, '', '2012-06-21', '2012-06-21', '', '4234', '', '0.9870', 15, 15, '2012-06-21', '2012-07-20', '0.0000', '0.0000', '0.0000', 0, 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:56:56', 6, 1, 2, 1, 2),
(4, 2, 'Mileage', 'Mileage - Standard', NULL, '', '2012-06-21', '2012-06-21', '', '4234', '', '1.2000', 60, 72, '2012-06-21', '2012-07-20', '0.0000', '0.0000', '0.0000', 0, 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:57:16', 6, 1, 2, 1, 2),
(5, 2, 'Mileage', 'Mileage - Standard', NULL, '', '2012-06-21', '2012-06-21', '', '4234', '', '0.9870', 45, 44, '2012-06-21', '2012-07-20', '0.0000', '0.0000', '0.0000', 0, 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:57:36', 6, 1, 2, 1, 2),
(6, 3, 'Bonus', 'Bonus - Standard', NULL, '', '2012-06-21', '2046-04-09', '', '67589', '', '100.0000', 1, 100, '2012-06-21', '2012-07-20', '0.0000', '0.0000', '0.0000', 0, 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:58:03', 6, 1, 2, 1, 1),
(7, 3, 'Bonus', 'Bonus - Standard', NULL, '', '2012-06-21', '2046-04-09', '', '67589', '', '100.0000', 1, 100, '2012-06-21', '2012-07-20', '0.0000', '0.0000', '0.0000', 0, 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:58:03', 6, 1, 2, 1, 2),
(8, 3, 'Bonus', 'Bonus - Standard', NULL, '', '2012-06-21', '2046-04-09', '', '67589', '', '100.0000', 1, 100, '2012-06-21', '2012-07-20', '0.0000', '0.0000', '0.0000', 0, 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:58:38', 6, 1, 2, 1, 1),
(9, 3, 'Bonus', 'Bonus - Standard', NULL, '', '2012-06-21', '2046-04-09', '', '67589', '', '100.0000', 1, 100, '2012-06-21', '2012-07-20', '0.0000', '0.0000', '0.0000', 0, 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:58:38', 6, 1, 2, 1, 2),
(10, 1, 'Delivery', 'Delivery - Standard', NULL, '', '2012-06-21', '2012-06-21', '', '423423', '', '75.0000', 3, 225, '2012-06-21', '2012-07-20', '0.0000', '0.0000', '0.0000', 0, 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:59:05', 6, 1, 2, 1, 1),
(11, 1, 'Delivery', 'Delivery - Standard', NULL, '', '2012-06-21', '2012-06-21', '', '423423', '', '75.0000', 3, 225, '2012-06-21', '2012-07-20', '0.0000', '0.0000', '0.0000', 0, 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:59:05', 6, 1, 2, 1, 2),
(12, 2, 'Mileage', 'Mileage - Standard', NULL, '', '2012-06-21', '2012-06-21', '', '4234', '', '0.9870', 60, 59, '2012-06-21', '2012-07-20', '0.0000', '0.0000', '0.0000', 0, 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:59:25', 6, 1, 2, 1, 2),
(13, 2, 'Mileage', 'Mileage - Standard', NULL, '', '2012-06-21', '2012-06-21', '', '4234', '', '0.9870', 120, 118, '2012-06-21', '2012-07-20', '0.0000', '0.0000', '0.0000', 0, 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:59:42', 6, 1, 2, 1, 2),
(14, 2, 'Mileage', 'Mileage - Standard', NULL, '', '2012-06-21', '2012-06-21', '', '4234', '', '0.9870', 20, 20, '2012-06-21', '2012-07-20', '0.0000', '0.0000', '0.0000', 0, 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:59:58', 6, 1, 2, 1, 2),
(15, 1, 'Delivery', 'Delivery - Standard', NULL, '', '2012-06-22', '2012-06-22', '', '423423', '', '75.0000', 1, 75, '2012-06-22', '2012-07-20', '0.0000', '0.0000', '0.0000', 0, 0, '0000-00-00 00:00:00', NULL, '2012-06-22 00:00:15', 6, 1, 2, 1, 1),
(16, 1, 'Delivery', 'Delivery - Standard', NULL, '', '2012-06-22', '2012-06-22', '', '423423', '', '75.0000', 1, 75, '2012-06-22', '2012-07-20', '0.0000', '0.0000', '0.0000', 0, 0, '0000-00-00 00:00:00', NULL, '2012-06-22 00:00:15', 6, 1, 2, 1, 2),
(17, 2, 'Mileage', 'Mileage - Standard', NULL, '', '2012-06-21', '2012-06-21', '', '4234', '', '0.9870', 20, 20, '2012-06-21', '2012-07-20', '0.0000', '0.0000', '0.0000', 0, 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:59:58', 6, 1, 2, 1, 2);


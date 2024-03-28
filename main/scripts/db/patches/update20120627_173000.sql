
SET FOREIGN_KEY_CHECKS=0;
USE `pfleet` ;

TRUNCATE TABLE deductions;
TRUNCATE TABLE payments;

SET FOREIGN_KEY_CHECKS=1;

INSERT INTO `deductions` (`id`, `setup_id`, `category`, `description`, `priority`, `invoice_id`, `invoice_date`, `invoice_due_date`, `department`, `gl_code`, `disbursement_code`, `rate`, `quantity`, `amount`, `disbursement_date`, `balance`, `adjusted_balance`, `eligible`, `approved_datetime`, `approved_by`, `created_datetime`, `created_by`, `source_id`, `status`, `settlement_cycle_id`, `contractor_id`) VALUES
(1, 1, 'Delivery', 'Delivery - Standard', 0, '', '2012-06-21', '2012-06-21', '', '423423', '', '75.0000', 1, '75.0000', '2012-06-21', '75.0000', '0.0000', 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:56:17', 6, 1, 2, 1, 1),
(2, 1, 'Delivery', 'Delivery - Standard', 1, '', '2012-06-21', '2012-06-21', '', '423423', '', '75.0000', 1, '75.0000', '2012-06-21', '75.0000', '0.0000', 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:56:17', 6, 1, 2, 1, 2),
(3, 2, 'Mileage', 'Mileage - Standard', 2, '', '2012-06-21', '2012-06-21', '', '4234', '', '0.9870', 15, '15.0000', '2012-06-21', '15.0000', '0.0000', 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:56:56', 6, 1, 2, 1, 2),
(4, 2, 'Mileage', 'Mileage - Standard', 3, '', '2012-06-21', '2012-06-21', '', '4234', '', '1.2000', 60, '72.0000', '2012-06-21', '72.0000', '0.0000', 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:57:16', 6, 1, 2, 1, 2),
(5, 2, 'Mileage', 'Mileage - Standard', 4, '', '2012-06-21', '2012-06-21', '', '4234', '', '0.9870', 45, '44.0000', '2012-06-21', '44.0000', '0.0000', 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:57:36', 6, 1, 2, 1, 2),
(6, 3, 'Bonus', 'Bonus - Standard', 5, '', '2012-06-21', '2046-04-09', '', '67589', '', '100.0000', 1, '100.0000', '2012-06-21', '100.0000', '0.0000', 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:58:03', 6, 1, 2, 1, 1),
(7, 3, 'Bonus', 'Bonus - Standard', 6, '', '2012-06-21', '2046-04-09', '', '67589', '', '100.0000', 1, '100.0000', '2012-06-21', '100.0000', '0.0000', 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:58:03', 6, 1, 2, 1, 2),
(8, 3, 'Bonus', 'Bonus - Standard', 7, '', '2012-06-21', '2046-04-09', '', '67589', '', '100.0000', 1, '100.0000', '2012-06-21', '100.0000', '0.0000', 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:58:38', 6, 1, 2, 1, 1),
(9, 3, 'Bonus', 'Bonus - Standard', 8, '', '2012-06-21', '2046-04-09', '', '67589', '', '100.0000', 1, '100.0000', '2012-06-21', '100.0000', '0.0000', 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:58:38', 6, 1, 2, 1, 2),
(10, 1, 'Delivery', 'Delivery - Standard', 9, '', '2012-06-21', '2012-06-21', '', '423423', '', '75.0000', 3, '225.0000', '2012-06-21', '225.0000', '0.0000', 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:59:05', 6, 1, 2, 1, 1),
(11, 1, 'Delivery', 'Delivery - Standard', 10, '', '2012-06-21', '2012-06-21', '', '423423', '', '75.0000', 3, '225.0000', '2012-06-21', '225.0000', '0.0000', 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:59:05', 6, 1, 2, 1, 2),
(12, 2, 'Mileage', 'Mileage - Standard', 11, '', '2012-06-21', '2012-06-21', '', '4234', '', '0.9870', 60, '59.0000', '2012-06-21', '59.0000', '0.0000', 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:59:25', 6, 1, 2, 1, 2),
(13, 2, 'Mileage', 'Mileage - Standard', 12, '', '2012-06-21', '2012-06-21', '', '4234', '', '0.9870', 120, '118.0000', '2012-06-21', '118.0000', '0.0000', 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:59:42', 6, 1, 2, 1, 2),
(14, 2, 'Mileage', 'Mileage - Standard', 13, '', '2012-06-21', '2012-06-21', '', '4234', '', '0.9870', 20, '20.0000', '2012-06-21', '20.0000', '0.0000', 0, '0000-00-00 00:00:00', NULL, '2012-06-21 23:59:58', 6, 1, 2, 1, 2),
(15, 1, 'Delivery', 'Delivery - Standard', 14, '', '2012-06-22', '2012-06-22', '', '423423', '', '75.0000', 1, '75.0000', '2012-06-22', '75.0000', '0.0000', 0, '0000-00-00 00:00:00', NULL, '2012-06-22 00:00:15', 6, 1, 2, 1, 1);

INSERT INTO `payments` (`id`, `setup_id`, `category`, `description`, `invoice`, `invoice_date`, `invoice_due_date`, `department`, `gl_code`, `quantity`, `rate`, `amount`, `balance`, `check_id`, `disbursement_date`, `approved_datetime`, `approved_by`, `created_datetime`, `created_by`, `source_id`, `status`, `settlement_cycle_id`, `contractor_id`) VALUES
(1, 1, 'Delivery', 'Delivery - Standard', '', '2012-06-21', '2012-06-21', '', '423423', 1, '75.0000', '75.0000', '75.0000', '', '2012-06-21', '0000-00-00 00:00:00', NULL, '2012-06-21 23:23:10', 6, 1, 2, 1, 1),
(2, 1, 'Delivery', 'Delivery - Standard', '', '2012-06-21', '2012-06-21', '', '423423', 1, '75.0000', '75.0000', '75.0000', '', '2012-06-21', '0000-00-00 00:00:00', NULL, '2012-06-21 23:23:10', 6, 1, 2, 1, 2),
(3, 2, 'Mileage', 'Mileage - Standard', '', '2012-06-21', '2012-06-21', '', '4234', 250, '0.9870', '246.7500', '246.7500', '', '2012-06-21', '0000-00-00 00:00:00', NULL, '2012-06-21 23:24:23', 6, 1, 2, 1, 2),
(4, 2, 'Mileage', 'Mileage - Standard', '', '2012-06-21', '2012-06-21', '', '4234', 400, '0.9870', '394.8000', '394.8000', '', '2012-06-21', '0000-00-00 00:00:00', NULL, '2012-06-21 23:24:53', 6, 1, 2, 1, 2),
(5, 2, 'Mileage', 'Mileage - Standard', '', '2012-06-21', '2012-06-21', '', '4234', 40, '0.9870', '39.4800', '39.4800', '', '2012-06-21', '0000-00-00 00:00:00', NULL, '2012-06-21 23:25:35', 6, 1, 2, 1, 2),
(6, 2, 'Mileage', 'Mileage - Standard', '', '2012-06-21', '2012-06-21', '', '4234', 25, '1.0000', '25.0000', '25.0000', '', '2012-06-21', '0000-00-00 00:00:00', NULL, '2012-06-21 23:25:57', 6, 1, 2, 1, 2),
(7, 2, 'Mileage', 'Mileage - Standard', '', '2012-06-21', '2012-06-21', '', '4234', 40, '1.2000', '48.0000', '48.0000', '', '2012-06-21', '0000-00-00 00:00:00', NULL, '2012-06-21 23:26:50', 6, 1, 2, 1, 2),
(8, 4, 'Waiting', 'Waiting - Standard', '', '2012-06-21', '2012-06-21', '', '4564567', 2, '20.0000', '40.0000', '40.0000', '', '2012-06-21', '0000-00-00 00:00:00', NULL, '2012-06-21 23:28:45', 6, 1, 2, 1, 1),
(9, 4, 'Waiting', 'Waiting - Standard', '', '2012-06-21', '2012-06-21', '', '4564567', 2, '20.0000', '40.0000', '40.0000', '', '2012-06-21', '0000-00-00 00:00:00', NULL, '2012-06-21 23:28:45', 6, 1, 2, 1, 2),
(10, 3, 'Bonus', 'Bonus - Standard', '', '2012-06-21', '2012-06-22', '', '67589', 1, '100.0000', '100.0000', '100.0000', '', '2012-06-21', '0000-00-00 00:00:00', NULL, '2012-06-21 23:29:06', 6, 1, 2, 1, 1),
(11, 3, 'Bonus', 'Bonus - Standard', '', '2012-06-21', '2012-06-22', '', '67589', 1, '100.0000', '100.0000', '100.0000', '', '2012-06-21', '0000-00-00 00:00:00', NULL, '2012-06-21 23:29:06', 6, 1, 2, 1, 2),
(12, 2, 'Mileage', 'Mileage - Standard', '', '2012-06-21', '2012-06-21', '', '4234', 23, '0.9870', '22.7010', '22.7010', '', '2012-06-21', '0000-00-00 00:00:00', NULL, '2012-06-21 23:29:53', 6, 1, 2, 1, 2),
(13, 1, 'Delivery', 'Delivery - Standard', '', '2012-06-21', '2012-06-21', '', '423423', 1, '75.0000', '75.0000', '75.0000', '', '2012-06-21', '0000-00-00 00:00:00', NULL, '2012-06-21 23:30:11', 6, 1, 2, 1, 1),
(14, 1, 'Delivery', 'Delivery - Standard', '', '2012-06-21', '2012-06-21', '', '423423', 1, '75.0000', '75.0000', '75.0000', '', '2012-06-21', '0000-00-00 00:00:00', NULL, '2012-06-21 23:30:11', 6, 1, 2, 1, 2),
(15, 2, 'Mileage', 'Mileage - Standard', '', '2012-06-21', '2012-06-21', '', '4234', 65, '0.9870', '64.1550', '64.1550', '', '2012-06-21', '0000-00-00 00:00:00', NULL, '2012-06-21 23:30:38', 6, 1, 2, 1, 2);

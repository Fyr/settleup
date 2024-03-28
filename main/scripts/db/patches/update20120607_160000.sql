use pfleet;
INSERT INTO `carrier_contractor` (`id`, `carrier_id`, `contractor_id`, `status`, `start_date`, `termination_date`, `rehire_date`) VALUES
(1, 1, 2, 1, '2012-06-07', NULL, NULL),
(2, 1, 3, 1, '2012-06-07', NULL, NULL);

INSERT INTO `reserve_transaction` (`id`, `reserve_account_sender`, `reserve_account_receiver`, `vendor_code`, `type`, `deduction_id`, `priority`, `amount`, `balance`, `adjusted_balance`, `adjusted_balance_use`, `settlement_cycle_id`, `approved_datetime`, `approved_by`, `created_datetime`, `created_by`, `source_id`, `disbursement_id`, `status`) VALUES
(1, 1, 1, NULL, 1, NULL, NULL, '0.9000', NULL, NULL, NULL, 1, NULL, NULL, '2012-06-07 16:08:56', 6, NULL, NULL, 2);
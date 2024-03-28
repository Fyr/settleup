ALTER TABLE `disbursement_check`
DROP FOREIGN KEY `fk_disburstment_check_disburstment_id`;
ALTER TABLE `disbursement_check`
DROP INDEX `fk_disburstment_check_disburstment_id` , RENAME TO  `tmp_disbursement_check`;

DROP TABLE `recurring_deduction`;
DROP TABLE `recurring_payment`;
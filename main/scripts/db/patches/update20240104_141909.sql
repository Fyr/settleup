ALTER TABLE `disbursement_transaction`
DROP FOREIGN KEY `fk_disbursement_bank_account_history_id`,
DROP COLUMN `bank_account_history_id`;
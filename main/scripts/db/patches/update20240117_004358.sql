ALTER TABLE `reserve_account`
DROP FOREIGN KEY `fk_reserve_account_bank_account_id`,
DROP COLUMN `bank_account_id`;
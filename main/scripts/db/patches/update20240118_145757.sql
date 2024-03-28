--  Every new carrier will be set as NOT CONFIGURED status by default.
ALTER TABLE `carrier` MODIFY `status` tinyint(1) NOT NULL DEFAULT '0';

ALTER TABLE `escrow_accounts_history`
DROP COLUMN `bank_name`,
DROP COLUMN `bank_routing_number`,
DROP COLUMN `bank_account_number`;
ALTER TABLE `reserve_account` CHANGE COLUMN `contribution_amount` `contribution_amount` DECIMAL(10,2) NULL DEFAULT NULL  , CHANGE COLUMN `max_withdrawal_amount` `max_withdrawal_amount` DECIMAL(10,2) NULL DEFAULT NULL  , CHANGE COLUMN `balance` `balance` DECIMAL(10,2) NULL DEFAULT NULL  , CHANGE COLUMN `initial_balance` `initial_balance` DECIMAL(10,2) NULL DEFAULT NULL , CHANGE COLUMN `current_balance` `current_balance` DECIMAL(10,2) NULL DEFAULT NULL;
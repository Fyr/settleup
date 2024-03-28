ALTER TABLE `deductions`
 CHANGE COLUMN `balance` `balance` DECIMAL(10,2) NULL DEFAULT NULL,
 CHANGE COLUMN `amount` `amount` DECIMAL(10,2) NULL DEFAULT NULL,
 CHANGE COLUMN `adjusted_balance` `adjusted_balance` DECIMAL(10,2) NULL DEFAULT NULL
;
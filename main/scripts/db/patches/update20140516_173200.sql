ALTER TABLE `bank_account_history` ADD COLUMN `priority` INT(10) NULL DEFAULT NULL;

UPDATE bank_account_history bh LEFT JOIN bank_account b ON bh.bank_account_id = b.id SET bh.priority = b.priority;

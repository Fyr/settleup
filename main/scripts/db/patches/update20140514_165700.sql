ALTER TABLE `bank_account` ADD COLUMN `process_type` INT(10) UNSIGNED NULL DEFAULT NULL,
 ADD CONSTRAINT `fk_bank_account_process_type`
  FOREIGN KEY (`process_type`)
  REFERENCES `disbursement_transaction_type` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
;

UPDATE `disbursement_transaction_type` SET `title` = 'Settlement' WHERE `id` = 1;

UPDATE `bank_account` SET `process_type` = 1 WHERE `process` = 'Settlement';
UPDATE `bank_account` SET `process_type` = 1 WHERE `process` = 'Settlement';

UPDATE bank_account b
 LEFT JOIN entity e ON b.entity_id = e.id
SET b.process_type = 1
  WHERE e.entity_type_id != 2;

UPDATE bank_account b
 LEFT JOIN entity e ON b.entity_id = e.id
SET b.process_type = 2
  WHERE e.entity_type_id = 1;

  ALTER TABLE `bank_account_history` ADD COLUMN `process_type` INT(10) UNSIGNED NULL DEFAULT NULL,
 ADD CONSTRAINT `fk_bank_account_history_process_type`
  FOREIGN KEY (`process_type`)
  REFERENCES `disbursement_transaction_type` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
;

UPDATE `bank_account_history` SET `process_type` = 1 WHERE `process` = 'Settlement';
UPDATE `bank_account_history` SET `process_type` = 1 WHERE `process` = 'Settlement';
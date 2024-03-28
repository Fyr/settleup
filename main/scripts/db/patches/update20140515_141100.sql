INSERT INTO payment_status (id, title) VALUES (4, 'Not Approved');

UPDATE disbursement_transaction SET status = 4 WHERE status != 3;

ALTER TABLE `settlement_cycle` ADD COLUMN `disbursement_status` INT(11) UNSIGNED NULL DEFAULT 4,
 ADD CONSTRAINT `fk_settlement_cycle_disbursement_status`
  FOREIGN KEY (`disbursement_status`)
  REFERENCES `payment_status` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
;
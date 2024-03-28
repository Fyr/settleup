SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`disbursement_transaction` DROP FOREIGN KEY `fk_disbursement_transaction_entity_id` , DROP FOREIGN KEY `fk_disbursement_transaction_bank_account_history_id` , DROP FOREIGN KEY `fk_disbursement_transaction_process_type` , DROP FOREIGN KEY `fk_disbursement_transaction_status` , DROP FOREIGN KEY `fk_disbursement_transaction_approved_by` , DROP FOREIGN KEY `fk_disbursement_transaction_created_by` ;

ALTER TABLE `pfleet`.`disbursement_transaction` 
  ADD CONSTRAINT `fk_disbursement_approved_by`
  FOREIGN KEY (`approved_by` )
  REFERENCES `pfleet`.`users` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_disbursement_created_by`
  FOREIGN KEY (`created_by` )
  REFERENCES `pfleet`.`users` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_disbursement_status`
  FOREIGN KEY (`status` )
  REFERENCES `pfleet`.`payment_status` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_disbursement_created_by` (`created_by` ASC) 
, ADD INDEX `fk_disbursement_approved_by` (`approved_by` ASC) 
, ADD INDEX `fk_disbursement_status` (`status` ASC) 
, ADD INDEX `fk_disbursement_bank_a_h_id` (`bank_account_history_id` ASC) 
, ADD INDEX `fk_disbursement_entity_id` (`entity_id` ASC) 
, ADD INDEX `fk_disbursement_process_type` (`process_type` ASC) 
, DROP INDEX `fk_disbursement_transaction_process_type` 
, DROP INDEX `fk_disbursement_transaction_status` 
, DROP INDEX `fk_disbursement_transaction_approved_by` 
, DROP INDEX `fk_disbursement_transaction_created_by` 
, DROP INDEX `fk_disbursement_transaction_entity_id` 
, DROP INDEX `fk_disbursement_transaction_bank_account_history_id` ;

ALTER TABLE `pfleet`.`entity` CHANGE COLUMN `user_id` `user_id` INT(10) UNSIGNED NULL DEFAULT NULL  , 
  ADD CONSTRAINT `fk_entity_user_id`
  FOREIGN KEY (`user_id` )
  REFERENCES `pfleet`.`users` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `pfleet`.`payment_setup` 
  ADD CONSTRAINT `fk_payment_setup_contractor_id`
  FOREIGN KEY (`contractor_id` )
  REFERENCES `pfleet`.`contractor` (`entity_id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `pfleet`.`payments` CHANGE COLUMN `balance` `balance` DECIMAL(10,4) NULL DEFAULT NULL  AFTER `contractor_id` , DROP FOREIGN KEY `fk_payments_settlement_cycle_id` , DROP FOREIGN KEY `fk_payments_contractor_id` ;

ALTER TABLE `pfleet`.`payments` 
  ADD CONSTRAINT `fk_payments_settlement_cycle_id`
  FOREIGN KEY (`settlement_cycle_id` )
  REFERENCES `pfleet`.`settlement_cycle` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_payments_contractor_id`
  FOREIGN KEY (`contractor_id` )
  REFERENCES `pfleet`.`contractor` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

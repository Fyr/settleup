SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`bank_account` CHANGE COLUMN `percentage` `percentage` DECIMAL(10,2) NULL DEFAULT NULL  ;

ALTER TABLE `pfleet`.`bank_account_history` CHANGE COLUMN `amount` `amount` DECIMAL(10,2) NULL DEFAULT NULL  , CHANGE COLUMN `percentage` `percentage` DECIMAL(10,2) NULL DEFAULT NULL  ;

ALTER TABLE `pfleet`.`deduction_setup` CHANGE COLUMN `rate` `rate` DECIMAL(10,2) NULL DEFAULT NULL  ;

ALTER TABLE `pfleet`.`deductions` CHANGE COLUMN `rate` `rate` DECIMAL(10,2) NULL DEFAULT NULL  , CHANGE COLUMN `amount` `amount` DECIMAL(10,2) NULL DEFAULT NULL  , CHANGE COLUMN `balance` `balance` DECIMAL(10,2) NULL DEFAULT NULL  , CHANGE COLUMN `adjusted_balance` `adjusted_balance` DECIMAL(10,2) NULL DEFAULT NULL  , DROP FOREIGN KEY `fk_deduction_billing_cycle_id` , DROP FOREIGN KEY `fk_deductions_recurring` ;

ALTER TABLE `pfleet`.`deductions` 
  ADD CONSTRAINT `fk_deduction_billing_cycle_id`
  FOREIGN KEY (`billing_cycle_id` )
  REFERENCES `pfleet`.`cycle_period` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_deductions_recurring`
  FOREIGN KEY (`recurring` )
  REFERENCES `pfleet`.`recurring_title` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `pfleet`.`disbursement_transaction` CHANGE COLUMN `amount` `amount` DECIMAL(10,2) NULL DEFAULT NULL  , DROP FOREIGN KEY `fk_disbursement_created_by` , DROP FOREIGN KEY `fk_disbursement_status` , DROP FOREIGN KEY `fk_disburstment_settlement_cycle_id` ;

ALTER TABLE `pfleet`.`disbursement_transaction` 
  ADD CONSTRAINT `fk_disbursement_created_by`
  FOREIGN KEY (`created_by` )
  REFERENCES `pfleet`.`users` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_disbursement_status`
  FOREIGN KEY (`status` )
  REFERENCES `pfleet`.`payment_status` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_disburstment_settlement_cycle_id`
  FOREIGN KEY (`settlement_cycle_id` )
  REFERENCES `pfleet`.`settlement_cycle` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `pfleet`.`payment_setup` CHANGE COLUMN `rate` `rate` DECIMAL(10,2) NULL DEFAULT NULL  ;

ALTER TABLE `pfleet`.`payments` CHANGE COLUMN `rate` `rate` DECIMAL(10,2) NULL DEFAULT NULL  , CHANGE COLUMN `amount` `amount` DECIMAL(10,2) NULL DEFAULT NULL  , CHANGE COLUMN `balance` `balance` DECIMAL(10,2) NULL DEFAULT NULL  , DROP FOREIGN KEY `fk_payments_settlement_cycle_id` , DROP FOREIGN KEY `fk_payments_contractor_entity_id` , DROP FOREIGN KEY `fk_payments_carrier_entity_id` , DROP FOREIGN KEY `fk_payments_cycle_period_id` , DROP FOREIGN KEY `fk_payments_recurring` ;

ALTER TABLE `pfleet`.`payments` 
  ADD CONSTRAINT `fk_payments_settlement_cycle_id`
  FOREIGN KEY (`settlement_cycle_id` )
  REFERENCES `pfleet`.`settlement_cycle` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_payments_contractor_entity_id`
  FOREIGN KEY (`contractor_id` )
  REFERENCES `pfleet`.`contractor` (`entity_id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_payments_carrier_entity_id`
  FOREIGN KEY (`carrier_id` )
  REFERENCES `pfleet`.`carrier` (`entity_id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_payments_cycle_period_id`
  FOREIGN KEY (`billing_cycle_id` )
  REFERENCES `pfleet`.`cycle_period` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_payments_recurring`
  FOREIGN KEY (`recurring` )
  REFERENCES `pfleet`.`recurring_title` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `pfleet`.`reserve_account` CHANGE COLUMN `min_balance` `min_balance` DECIMAL(10,2) NULL DEFAULT NULL  , CHANGE COLUMN `contribution_amount` `contribution_amount` DECIMAL(10,2) NULL DEFAULT NULL  , CHANGE COLUMN `max_withdrawal_amount` `max_withdrawal_amount` DECIMAL(10,2) NULL DEFAULT NULL  , CHANGE COLUMN `initial_balance` `initial_balance` DECIMAL(10,2) NULL DEFAULT NULL  , CHANGE COLUMN `current_balance` `current_balance` DECIMAL(10,2) NULL DEFAULT NULL  , CHANGE COLUMN `balance` `balance` DECIMAL(10,2) NULL DEFAULT NULL  ;

ALTER TABLE `pfleet`.`reserve_transaction` CHANGE COLUMN `amount` `amount` DECIMAL(10,2) NULL DEFAULT NULL  , CHANGE COLUMN `balance` `balance` DECIMAL(10,2) NULL DEFAULT NULL  , CHANGE COLUMN `adjusted_balance` `adjusted_balance` DECIMAL(10,2) NULL DEFAULT NULL  , DROP FOREIGN KEY `fk_reserve_transaction_status` ;

ALTER TABLE `pfleet`.`reserve_transaction` 
  ADD CONSTRAINT `fk_reserve_transaction_status`
  FOREIGN KEY (`status` )
  REFERENCES `pfleet`.`payment_status` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `pfleet`.`payments_temp` CHANGE COLUMN `rate` `rate` DECIMAL(10,2) NULL DEFAULT NULL  ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

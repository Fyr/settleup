SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`disbursement_transaction` 
  ADD CONSTRAINT `fk_disbursement_bank_account_history_id`
  FOREIGN KEY (`bank_account_history_id` )
  REFERENCES `pfleet`.`bank_account_history` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_disbursement_entity_id`
  FOREIGN KEY (`entity_id` )
  REFERENCES `pfleet`.`entity` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_disbursement_process_type`
  FOREIGN KEY (`process_type` )
  REFERENCES `pfleet`.`disbursement_transaction_type` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_disbursement_bank_account_history_id` (`bank_account_history_id` ASC) 
, DROP INDEX `fk_disbursement_bank_a_h_id` ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

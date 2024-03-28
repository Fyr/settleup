SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `pfleet`.`deductions` DROP COLUMN `eligable` , ADD COLUMN `eligible` INT(11) NULL DEFAULT NULL  AFTER `billing_cycle_id` , DROP FOREIGN KEY `fk_deduction_provider_id` , DROP FOREIGN KEY `fk_deduction_reserve_account_receiver` , DROP FOREIGN KEY `fk_deduction_billing_cycle_id` ;

ALTER TABLE `pfleet`.`deductions` 
  ADD CONSTRAINT `fk_deduction_provider_id`
  FOREIGN KEY (`provider_id` )
  REFERENCES `pfleet`.`entity` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_deduction_reserve_account_receiver`
  FOREIGN KEY (`reserve_account_receiver` )
  REFERENCES `pfleet`.`reserve_account` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_deduction_billing_cycle_id`
  FOREIGN KEY (`billing_cycle_id` )
  REFERENCES `pfleet`.`cycle_period` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

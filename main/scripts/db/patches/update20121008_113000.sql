SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `pfleet`.`deduction_setup` DROP COLUMN `deduction_setupcol` ;

ALTER TABLE `pfleet`.`deductions` ADD COLUMN `provider_id` INT(10) UNSIGNED NOT NULL  AFTER `contractor_id` , ADD COLUMN `terms` INT(10) NULL DEFAULT NULL  AFTER `provider_id` , ADD COLUMN `recurring` INT(10) NULL DEFAULT NULL  AFTER `terms` , ADD COLUMN `reserve_account_receiver` INT(10) UNSIGNED NULL DEFAULT NULL  AFTER `recurring` , ADD COLUMN `billing_cycle_id` INT(10) UNSIGNED NOT NULL  AFTER `reserve_account_receiver` , ADD COLUMN `eligable` INT(11) NULL DEFAULT NULL  AFTER `billing_cycle_id` , DROP FOREIGN KEY `fk_deductions_approved_by` ;

ALTER TABLE `pfleet`.`deductions` 
  ADD CONSTRAINT `fk_deductions_approved_by`
  FOREIGN KEY (`approved_by` )
  REFERENCES `pfleet`.`users` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
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
  ON UPDATE NO ACTION
, ADD INDEX `fk_deduction_provider_id_idx` (`provider_id` ASC) 
, ADD INDEX `fk_deductions_recerve_account_receiver` (`reserve_account_receiver` ASC) 
, ADD INDEX `fk_deduction_billing_cycle_id_idx` (`billing_cycle_id` ASC) ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

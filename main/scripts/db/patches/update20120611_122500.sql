SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`deductions` CHANGE COLUMN `settlement_cycle_id` `settlement_cycle_id` INT(10) UNSIGNED NULL DEFAULT NULL  , DROP FOREIGN KEY `fk_deductions_settlement_cycle_id` , DROP FOREIGN KEY `fk_deductions_contractor_id` ;

ALTER TABLE `pfleet`.`deductions` 
  ADD CONSTRAINT `fk_deductions_settlement_cycle_id`
  FOREIGN KEY (`settlement_cycle_id` )
  REFERENCES `pfleet`.`settlement_cycle` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_deductions_contractor_id`
  FOREIGN KEY (`contractor_id` )
  REFERENCES `pfleet`.`contractor` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `pfleet`.`reserve_account_contractor` ADD COLUMN `reserve_account_vendor_id` INT(10) UNSIGNED NOT NULL  AFTER `reserve_account_id` , 
  ADD CONSTRAINT `fk_reserve_account_contractor_reserve_account_vendor_id`
  FOREIGN KEY (`reserve_account_vendor_id` )
  REFERENCES `pfleet`.`reserve_account_vendor` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_reserve_account_contractor_reserve_account_vendor_id` (`reserve_account_vendor_id` ASC) ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

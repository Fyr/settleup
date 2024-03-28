SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`deductions_temp` DROP FOREIGN KEY `payment_temp_status_id0` ;

ALTER TABLE `pfleet`.`disbursement_transaction` ADD COLUMN `settlement_cycle_id` INT(10) UNSIGNED NOT NULL  AFTER `approved_datetime` , DROP FOREIGN KEY `fk_disbursement_approved_by` , DROP FOREIGN KEY `fk_disbursement_created_by` , DROP FOREIGN KEY `fk_disbursement_status` ;

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
  ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_disburstment_settlement_cycle_id`
  FOREIGN KEY (`settlement_cycle_id` )
  REFERENCES `pfleet`.`settlement_cycle` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_disburstment_settlement_cycle_id` (`settlement_cycle_id` ASC) ;

ALTER TABLE `pfleet`.`deductions_temp` DROP COLUMN `deduction_code` , DROP COLUMN `vendor_deduction` , DROP COLUMN `priority` , ADD COLUMN `priority` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL  AFTER `rate` , ADD COLUMN `vendor_deduction` INT(10) UNSIGNED NULL DEFAULT NULL  AFTER `contractor_id` , ADD COLUMN `deduction_code` VARCHAR(45) NULL DEFAULT NULL  AFTER `contract` ,
  ADD CONSTRAINT `payment_temp_status_id0`
  FOREIGN KEY (`status_id` )
  REFERENCES `pfleet`.`payment_temp_status` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

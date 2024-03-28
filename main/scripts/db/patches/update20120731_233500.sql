SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`deductions_temp` DROP FOREIGN KEY `payment_temp_status_id0` ;

ALTER TABLE `pfleet`.`deductions_temp` DROP COLUMN `deduction_code` , DROP COLUMN `vendor_deduction` , DROP COLUMN `priority` , ADD COLUMN `priority` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL  AFTER `rate` , ADD COLUMN `vendor_deduction` INT(10) UNSIGNED NULL DEFAULT NULL  AFTER `contractor_id` , ADD COLUMN `deduction_code` VARCHAR(45) NULL DEFAULT NULL  AFTER `contract` , 
  ADD CONSTRAINT `payment_temp_status_id0`
  FOREIGN KEY (`status_id` )
  REFERENCES `pfleet`.`payment_temp_status` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

CREATE  TABLE IF NOT EXISTS `pfleet`.`disbursement_check` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `disburstment_id` INT(10) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_disburstment_check_disburstment_id` (`disburstment_id` ASC) ,
  CONSTRAINT `fk_disburstment_check_disburstment_id`
    FOREIGN KEY (`disburstment_id` )
    REFERENCES `pfleet`.`disbursement_transaction` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

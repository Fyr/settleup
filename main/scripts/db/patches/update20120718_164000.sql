SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE  TABLE IF NOT EXISTS `pfleet`.`deductions_temp` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `category` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `invoice` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `invoice_date` DATE NULL DEFAULT NULL ,
  `department` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `gl_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `quantity` INT(11) NULL DEFAULT NULL ,
  `rate` DECIMAL(10,4) NULL DEFAULT NULL ,
  `priority` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `source_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `contractor_id` INT(10) UNSIGNED NOT NULL ,
  `vendor_deduction` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `contract` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `deduction_code` VARCHAR(45) NULL DEFAULT NULL ,
  `code` VARCHAR(45) NULL DEFAULT NULL ,
  `setup_id` INT(10) NULL DEFAULT NULL ,
  `status_id` INT(10) UNSIGNED NOT NULL ,
  `error` VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `payment_temp_status_id` (`status_id` ASC) ,
  CONSTRAINT `payment_temp_status_id0`
    FOREIGN KEY (`status_id` )
    REFERENCES `pfleet`.`payment_temp_status` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

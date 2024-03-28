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

CREATE  TABLE IF NOT EXISTS `pfleet`.`carrier_vendor` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `carrier_id` INT(10) UNSIGNED NOT NULL ,
  `vendor_id` INT(10) UNSIGNED NOT NULL ,
  `status` INT(10) UNSIGNED NOT NULL ,
  `start_date` DATE NULL DEFAULT NULL ,
  `termination_date` DATE NULL DEFAULT NULL ,
  `rehire_date` DATE NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_carrier_vendor_carrier_id` (`carrier_id` ASC) ,
  INDEX `fk_carrier_vendor_vendor_id` (`vendor_id` ASC) ,
  INDEX `fk_carrier_vendor_status` (`status` ASC) ,
  CONSTRAINT `fk_carrier_vendor_carrier_id`
    FOREIGN KEY (`carrier_id` )
    REFERENCES `pfleet`.`carrier` (`entity_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_carrier_vendor_vendor_id`
    FOREIGN KEY (`vendor_id` )
    REFERENCES `pfleet`.`vendor` (`entity_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_carrier_vendor_status`
    FOREIGN KEY (`status` )
    REFERENCES `pfleet`.`vendor_status` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;

CREATE  TABLE IF NOT EXISTS `pfleet`.`vendor_status` (
  `id` INT(10) UNSIGNED NOT NULL ,
  `title` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `pfleet` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin ;
USE `pfleet` ;

-- -----------------------------------------------------
-- Table `pfleet`.`carrier_contractor`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pfleet`.`carrier_contractor` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `carrier_id` INT UNSIGNED NOT NULL ,
  `contractor_id` INT UNSIGNED NOT NULL ,
  `status` INT UNSIGNED NOT NULL ,
  `start_date` DATE NULL ,
  `termination_date` DATE NULL ,
  `rehire_date` DATE NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_carrier_contractor_carrier_id` (`carrier_id` ASC) ,
  INDEX `fk_carrier_contractor_contractor_id` (`contractor_id` ASC) ,
  INDEX `fk_carrier_contractor_status` (`status` ASC) ,
  CONSTRAINT `fk_carrier_contractor_carrier_id`
    FOREIGN KEY (`carrier_id` )
    REFERENCES `pfleet`.`carrier` (`entity_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_carrier_contractor_contractor_id`
    FOREIGN KEY (`contractor_id` )
    REFERENCES `pfleet`.`contractor` (`entity_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_carrier_contractor_status`
    FOREIGN KEY (`status` )
    REFERENCES `pfleet`.`contractor_status` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE  TABLE IF NOT EXISTS `pfleet`.`settlement_cycle` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `carrier_id` INT(10) UNSIGNED NOT NULL ,
  `cycle_period_id` INT(10) UNSIGNED NOT NULL ,
  `settlement_day` INT(11) NOT NULL ,
  `payment_terms` INT(11) NOT NULL ,
  `settlement_start_date` DATE NOT NULL ,
  `settlement_close_date` DATE NOT NULL ,
  `payment_date` DATE NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_settlement_cycle_carrier_id` (`carrier_id` ASC) ,
  INDEX `fk_settlement_cycle_cycle_period_id` (`cycle_period_id` ASC) ,
  CONSTRAINT `fk_settlement_cycle_carrier_id`
    FOREIGN KEY (`carrier_id` )
    REFERENCES `pfleet`.`carrier` (`entity_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_settlement_cycle_cycle_period_id`
    FOREIGN KEY (`cycle_period_id` )
    REFERENCES `pfleet`.`cycle_period` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

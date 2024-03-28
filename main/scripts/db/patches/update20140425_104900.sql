SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE  TABLE IF NOT EXISTS `settlement_cycle_rule` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `carrier_id` INT UNSIGNED NOT NULL ,
  `cycle_period_id` INT UNSIGNED NOT NULL ,
  `payment_terms` INT NOT NULL ,
  `disbursement_terms` INT NOT NULL ,
  `cycle_start_date` DATE NOT NULL ,
  `first_start_day` INT(11) NULL DEFAULT NULL ,
  `second_start_day` INT(11) NULL DEFAULT NULL,
  `deleted` TINYINT( 1 ) NOT NULL DEFAULT  '0',
  PRIMARY KEY (`id`) ,
  INDEX `fk_settlement_cycle_rule_carrier_id` (`carrier_id` ASC) ,
  INDEX `fk_settlement_cycle_rule_cycle_period_id` (`cycle_period_id` ASC) ,
  INDEX (  `deleted` ),
  CONSTRAINT `fk_settlement_cycle_rule_carrier_id`
    FOREIGN KEY (`carrier_id` )
    REFERENCES `carrier` (`entity_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_settlement_cycle_rule_cycle_period_id`
    FOREIGN KEY (`cycle_period_id` )
    REFERENCES `cycle_period` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
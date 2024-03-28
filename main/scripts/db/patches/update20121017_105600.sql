SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

use pfleet;

ALTER TABLE `pfleet`.`deductions` ADD COLUMN `first_start_day` VARCHAR(45) NULL DEFAULT NULL  AFTER `eligible` , ADD COLUMN `second_start_day` VARCHAR(45) NULL DEFAULT NULL  AFTER `first_start_day` , DROP FOREIGN KEY `fk_deduction_provider_id` , DROP FOREIGN KEY `fk_deduction_reserve_account_receiver` , DROP FOREIGN KEY `fk_deduction_billing_cycle_id` ;

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
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_deductions_recurring`
  FOREIGN KEY (`recurring` )
  REFERENCES `pfleet`.`recurring_title` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_deductions_recurring` (`recurring` ASC) ;

ALTER TABLE `pfleet`.`payments` DROP FOREIGN KEY `fk_payments_contractor_entity_id` , DROP FOREIGN KEY `fk_payments_carrier_entity_id` , DROP FOREIGN KEY `fk_payments_cycle_period_id` ;

ALTER TABLE `pfleet`.`payments` 
  ADD CONSTRAINT `fk_payments_contractor_entity_id`
  FOREIGN KEY (`contractor_id` )
  REFERENCES `pfleet`.`contractor` (`entity_id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_payments_carrier_entity_id`
  FOREIGN KEY (`carrier_id` )
  REFERENCES `pfleet`.`carrier` (`entity_id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_payments_cycle_period_id`
  FOREIGN KEY (`billing_cycle_id` )
  REFERENCES `pfleet`.`cycle_period` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_payments_recurring`
  FOREIGN KEY (`recurring` )
  REFERENCES `pfleet`.`recurring_title` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_payments_recurring` (`recurring` ASC) ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`recurring_title` (
  `id` INT(11) NOT NULL,
  `title` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;

INSERT INTO `recurring_title` (`id`, `title`) VALUES
(0, 'No'),
(1, 'Yes');

UPDATE deductions
SET first_start_day = ( SELECT deduction_setup.first_start_day
FROM deduction_setup
WHERE deduction_setup.id = deductions.setup_id);

UPDATE deductions
SET second_start_day = ( SELECT deduction_setup.second_start_day
FROM deduction_setup
WHERE deduction_setup.id = deductions.setup_id);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

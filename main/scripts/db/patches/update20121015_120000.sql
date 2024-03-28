SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`payments` ADD COLUMN `carrier_id` INT(10) UNSIGNED NOT NULL  AFTER `balance` , ADD COLUMN `payment_code` VARCHAR(255) NULL DEFAULT NULL  AFTER `carrier_id` , ADD COLUMN `carrier_payment_code` VARCHAR(255) NULL DEFAULT NULL  AFTER `payment_code` , ADD COLUMN `terms` INT(10) NULL DEFAULT NULL  AFTER `carrier_payment_code` , ADD COLUMN `disbursement_code` VARCHAR(255) NULL DEFAULT NULL  AFTER `terms` , ADD COLUMN `recurring` INT(11) NULL DEFAULT NULL  AFTER `disbursement_code` , ADD COLUMN `level_id` INT(10) UNSIGNED NOT NULL  AFTER `recurring` , ADD COLUMN `billing_cycle_id` INT(10) UNSIGNED NOT NULL  AFTER `level_id` , ADD COLUMN `first_start_day` INT(10) NULL DEFAULT NULL  AFTER `billing_cycle_id` , ADD COLUMN `second_start_day` INT(10) NULL DEFAULT NULL  AFTER `first_start_day` , DROP FOREIGN KEY `fk_payments_contractor_entity_id`;
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
  ADD CONSTRAINT `fk_payments_level_id`
  FOREIGN KEY (`level_id` )
  REFERENCES `pfleet`.`setup_level` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_payments_cycle_period_id`
  FOREIGN KEY (`billing_cycle_id` )
  REFERENCES `pfleet`.`cycle_period` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_payments_carrier_id_idx` (`carrier_id` ASC) 
, ADD INDEX `fk_payments_level_id_idx` (`level_id` ASC) 
, ADD INDEX `fk_payments_cycle_period_id_idx` (`billing_cycle_id` ASC) ;

ALTER TABLE `pfleet`.`vendor` DROP COLUMN `reserve_account` , DROP COLUMN `recurring_deductions` , DROP COLUMN `resubmit`;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;






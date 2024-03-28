SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`payments` DROP FOREIGN KEY `fk_payments_level_id` ;

ALTER TABLE `pfleet`.`payments` DROP COLUMN `level_id` , DROP FOREIGN KEY `fk_payments_settlement_cycle_id` , DROP FOREIGN KEY `fk_payments_contractor_entity_id` , DROP FOREIGN KEY `fk_payments_carrier_entity_id` , DROP FOREIGN KEY `fk_payments_cycle_period_id` ;

ALTER TABLE `pfleet`.`payments` 
  ADD CONSTRAINT `fk_payments_settlement_cycle_id`
  FOREIGN KEY (`settlement_cycle_id` )
  REFERENCES `pfleet`.`settlement_cycle` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
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
  ON UPDATE NO ACTION;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

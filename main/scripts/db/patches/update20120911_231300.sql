SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`payment_setup` ADD COLUMN `quantity` INT(10) NULL DEFAULT NULL  AFTER `second_start_day` ;

ALTER TABLE `pfleet`.`payments` DROP FOREIGN KEY `fk_payments_settlement_cycle_id` , DROP FOREIGN KEY `fk_payments_contractor_entity_id` ;

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
  ON UPDATE NO ACTION;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

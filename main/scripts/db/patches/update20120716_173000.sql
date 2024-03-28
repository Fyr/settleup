SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`deduction_setup` ADD COLUMN `first_start_day` INT(10) NULL DEFAULT NULL  AFTER `reserve_account_receiver` , ADD COLUMN `second_start_day` INT(10) NULL DEFAULT NULL  AFTER `first_start_day` , DROP FOREIGN KEY `fk_deduction_setup_contractor_id` ;

ALTER TABLE `pfleet`.`deduction_setup` 
  ADD CONSTRAINT `fk_deduction_setup_contractor_id`
  FOREIGN KEY (`contractor_id` )
  REFERENCES `pfleet`.`contractor` (`entity_id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `pfleet`.`payment_setup` ADD COLUMN `first_start_day` INT(10) NULL DEFAULT NULL  AFTER `rate` , ADD COLUMN `second_start_day` INT(10) NULL DEFAULT NULL  AFTER `first_start_day` ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DELETE FROM `pfleet`.`deductions` WHERE `id`='2';
DELETE FROM `pfleet`.`deductions` WHERE `id`='1';

ALTER TABLE `pfleet`.`deductions` ADD COLUMN `settlement_cycle_id` INT(10) UNSIGNED NULL DEFAULT NULL  AFTER `status` , 
  ADD CONSTRAINT `fk_deductions_settlement_cycle_id`
  FOREIGN KEY (`settlement_cycle_id` )
  REFERENCES `pfleet`.`settlement_cycle` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_deductions_settlement_cycle_id` (`settlement_cycle_id` ASC) ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

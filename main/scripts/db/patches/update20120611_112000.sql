SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DELETE FROM `pfleet`.`payments` WHERE `id`='3';
DELETE FROM `pfleet`.`payments` WHERE `id`='2';
DELETE FROM `pfleet`.`payments` WHERE `id`='1';

ALTER TABLE `pfleet`.`payments` ADD COLUMN `contractor_id` INT(10) UNSIGNED NOT NULL  AFTER `settlement_cycle_id` , 
  ADD CONSTRAINT `fk_payments_contractor_id`
  FOREIGN KEY (`contractor_id` )
  REFERENCES `pfleet`.`contractor` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_payments_contractor_id` (`contractor_id` ASC) ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`deductions` DROP FOREIGN KEY `fk_deductions_settlement_cycle_id` , DROP FOREIGN KEY `fk_deductions_contractor_id` ;

ALTER TABLE `pfleet`.`deductions` 
  ADD CONSTRAINT `fk_deductions_settlement_cycle_id`
  FOREIGN KEY (`settlement_cycle_id` )
  REFERENCES `pfleet`.`settlement_cycle` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_deductions_contractor_entity_id`
  FOREIGN KEY (`contractor_id` )
  REFERENCES `pfleet`.`contractor` (`entity_id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, DROP INDEX `fk_deductions_contractor_id` 
, ADD INDEX `fk_deductions_contractor_entity_id` (`contractor_id` ASC) ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

UPDATE `pfleet`.`deductions` SET `contractor_id` = '2' WHERE `deductions`.`id` =1;
UPDATE `pfleet`.`deductions` SET `contractor_id` = '3' WHERE `deductions`.`id` =2;
UPDATE `pfleet`.`deductions` SET `contractor_id` = '3' WHERE `deductions`.`id` =3;
UPDATE `pfleet`.`deductions` SET `contractor_id` = '3' WHERE `deductions`.`id` =4;
UPDATE `pfleet`.`deductions` SET `contractor_id` = '3' WHERE `deductions`.`id` =5;
UPDATE `pfleet`.`deductions` SET `contractor_id` = '2' WHERE `deductions`.`id` =6;
UPDATE `pfleet`.`deductions` SET `contractor_id` = '3' WHERE `deductions`.`id` =7;
UPDATE `pfleet`.`deductions` SET `contractor_id` = '2' WHERE `deductions`.`id` =8;
UPDATE `pfleet`.`deductions` SET `contractor_id` = '3' WHERE `deductions`.`id` =9;
UPDATE `pfleet`.`deductions` SET `contractor_id` = '2' WHERE `deductions`.`id` =10;
UPDATE `pfleet`.`deductions` SET `contractor_id` = '3' WHERE `deductions`.`id` =11;
UPDATE `pfleet`.`deductions` SET `contractor_id` = '3' WHERE `deductions`.`id` =12;
UPDATE `pfleet`.`deductions` SET `contractor_id` = '3' WHERE `deductions`.`id` =13;
UPDATE `pfleet`.`deductions` SET `contractor_id` = '3' WHERE `deductions`.`id` =14;
UPDATE `pfleet`.`deductions` SET `contractor_id` = '2' WHERE `deductions`.`id` =15;
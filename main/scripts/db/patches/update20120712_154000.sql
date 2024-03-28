SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`payments` DROP FOREIGN KEY `fk_payments_contractor_id` ;

ALTER TABLE `pfleet`.`payments` 
  ADD CONSTRAINT `fk_payments_contractor_entity_id`
  FOREIGN KEY (`contractor_id` )
  REFERENCES `pfleet`.`contractor` (`entity_id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_payments_contractor_entity_id` (`contractor_id` ASC) 
, DROP INDEX `fk_payments_contractor_id` ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;


UPDATE `pfleet`.`payments` SET `contractor_id` = '2' WHERE `payments`.`id` =1;
UPDATE `pfleet`.`payments` SET `contractor_id` = '3' WHERE `payments`.`id` =2;
UPDATE `pfleet`.`payments` SET `contractor_id` = '3' WHERE `payments`.`id` =3;
UPDATE `pfleet`.`payments` SET `contractor_id` = '3' WHERE `payments`.`id` =4;
UPDATE `pfleet`.`payments` SET `contractor_id` = '3' WHERE `payments`.`id` =5;
UPDATE `pfleet`.`payments` SET `contractor_id` = '3' WHERE `payments`.`id` =6;
UPDATE `pfleet`.`payments` SET `contractor_id` = '3' WHERE `payments`.`id` =7;
UPDATE `pfleet`.`payments` SET `contractor_id` = '2' WHERE `payments`.`id` =8;
UPDATE `pfleet`.`payments` SET `contractor_id` = '3' WHERE `payments`.`id` =9;
UPDATE `pfleet`.`payments` SET `contractor_id` = '2' WHERE `payments`.`id` =10;
UPDATE `pfleet`.`payments` SET `contractor_id` = '3' WHERE `payments`.`id` =11;
UPDATE `pfleet`.`payments` SET `contractor_id` = '3' WHERE `payments`.`id` =12;
UPDATE `pfleet`.`payments` SET `contractor_id` = '2' WHERE `payments`.`id` =13;
UPDATE `pfleet`.`payments` SET `contractor_id` = '3' WHERE `payments`.`id` =14;
UPDATE `pfleet`.`payments` SET `contractor_id` = '3' WHERE `payments`.`id` =15;
UPDATE `pfleet`.`payments` SET `contractor_id` = '16' WHERE `payments`.`id` =16;
UPDATE `pfleet`.`payments` SET `contractor_id` = '17' WHERE `payments`.`id` =17;
UPDATE `pfleet`.`payments` SET `contractor_id` = '18' WHERE `payments`.`id` =18;
UPDATE `pfleet`.`payments` SET `contractor_id` = '17' WHERE `payments`.`id` =19;
UPDATE `pfleet`.`payments` SET `contractor_id` = '18' WHERE `payments`.`id` =20;


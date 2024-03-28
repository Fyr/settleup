SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`deductions` DROP FOREIGN KEY `fk_deductions_contractor_entity_id` ;

ALTER TABLE `pfleet`.`users_visibility` DROP FOREIGN KEY `fk_entity_visibility_object_entity_id` ;

ALTER TABLE `pfleet`.`deductions` 
  ADD CONSTRAINT `fk_deductions_contractor_entity_id`
  FOREIGN KEY (`contractor_id` )
  REFERENCES `pfleet`.`contractor` (`entity_id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_deductions_contractor_entity_id` (`contractor_id` ASC) 
, DROP INDEX `fk_deductions_contractor_entity_id` ;

ALTER TABLE `pfleet`.`users_visibility` CHANGE COLUMN `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT  , CHANGE COLUMN `object_entity_id` `entity_id` INT(10) UNSIGNED NOT NULL  , 
  ADD CONSTRAINT `fk_entity_visibility_entity_id`
  FOREIGN KEY (`entity_id` )
  REFERENCES `pfleet`.`entity` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, DROP INDEX `fk_entity_visibility_object_entity_id` 
, ADD INDEX `fk_entity_visibility_entity_id` (`entity_id` ASC) ;

INSERT INTO `pfleet`.`users_visibility` (`user_id`, `entity_id`)
  VALUES (10,16), (10,17), (10,18), (10,19), (10,20), (14,15), (15,15);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

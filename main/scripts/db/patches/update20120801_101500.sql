SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`users_visibility` DROP FOREIGN KEY `fk_entity_visibility_user_id` , DROP FOREIGN KEY `fk_entity_visibility_entity_id` ;

ALTER TABLE `pfleet`.`users_visibility` CHANGE COLUMN `user_id` `entity_id` INT(10) UNSIGNED NOT NULL  , CHANGE COLUMN `entity_id` `participant_id` INT(10) UNSIGNED NOT NULL  , 
  ADD CONSTRAINT `fk_users_visibility_entity_id`
  FOREIGN KEY (`entity_id` )
  REFERENCES `pfleet`.`entity` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_users_visibility_participant_id`
  FOREIGN KEY (`participant_id` )
  REFERENCES `pfleet`.`entity` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_users_visibility_entity_id` (`entity_id` ASC) 
, ADD INDEX `fk_users_visibility_participant_id` (`participant_id` ASC) 
, DROP INDEX `fk_entity_visibility_entity_id` 
, DROP INDEX `fk_entity_visibility_user_id` ;

TRUNCATE TABLE `pfleet`.`users_visibility`;

INSERT INTO `pfleet`.`users_visibility` (`entity_id`,`participant_id`)
  VALUES (15,16), (15,17), (15,18), (15,19), (15,20), (19,15), (20,15), (12,2), (12,3), (12,4), (12,5), (12,6), (12,7);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

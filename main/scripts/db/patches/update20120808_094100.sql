SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

UPDATE `pfleet`.`contractor` SET `dob` = NULL WHERE `dob` = '0000-00-00';

ALTER TABLE `pfleet`.`contractor` DROP FOREIGN KEY `fk_contractor_status` ;

ALTER TABLE `pfleet`.`contractor` DROP COLUMN `rehire_status` , DROP COLUMN `rehire_date` , DROP COLUMN `termination_date` , DROP COLUMN `start_date` , DROP COLUMN `status` , DROP FOREIGN KEY `fk_contractor_correspondence_method` ;

ALTER TABLE `pfleet`.`contractor` 
  ADD CONSTRAINT `fk_contractor_correspondence_method`
  FOREIGN KEY (`correspondence_method` )
  REFERENCES `pfleet`.`entity_contact_type` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

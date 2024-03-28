SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`file_storage` ADD COLUMN `file_type` INT(10) UNSIGNED NOT NULL  AFTER `uploaded_by` , 
  ADD CONSTRAINT `fk_file_storage_file_type`
  FOREIGN KEY (`file_type` )
  REFERENCES `pfleet`.`file_storage_type` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_file_storage_file_type` (`file_type` ASC) ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`file_storage_type` (
  `id` INT(10) UNSIGNED NOT NULL ,
  `title` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;

INSERT INTO `pfleet`.`file_storage_type` ( `id` , `title` )
VALUES ( 1, 'Payments' ) , ( 2, 'Deductions' );

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

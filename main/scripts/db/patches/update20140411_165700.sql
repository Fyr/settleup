SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

UPDATE `bank_account` SET `account_type` = 1;

ALTER TABLE `bank_account` CHANGE COLUMN `account_type` `account_type` INT(10) UNSIGNED NOT NULL  , 
  ADD CONSTRAINT `fk_bank_account_account_type`
  FOREIGN KEY (`account_type` )
  REFERENCES `bank_account_type` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_bank_account_account_type` (`account_type` ASC) ;

CREATE  TABLE IF NOT EXISTS `bank_account_type` (
  `id` INT(10) UNSIGNED NOT NULL ,
  `title` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;

INSERT INTO `bank_account_type` VALUES (1, 'Checking'), (2, 'Savings');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
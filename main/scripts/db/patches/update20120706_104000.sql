SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`bank_account` ADD COLUMN `limit_type` INT(10) UNSIGNED NOT NULL  AFTER `priority`, DROP FOREIGN KEY `fk_bank_account_payment_type` ;

ALTER TABLE `pfleet`.`bank_account` 
  ADD CONSTRAINT `fk_bank_account_payment_type`
  FOREIGN KEY (`payment_type` )
  REFERENCES `pfleet`.`payment_type` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_bank_account_limit_type`
  FOREIGN KEY (`limit_type` )
  REFERENCES `pfleet`.`bank_account_limit_type` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_bank_account_limit_type` (`limit_type` ASC) ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`bank_account_limit_type` (
  `id` INT(10) UNSIGNED NOT NULL ,
  `title` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;

INSERT INTO `pfleet`.`bank_account_limit_type`(`id`, `title`) VALUES (1, 'Percentage'), (2, 'Amount');
UPDATE `pfleet`.`bank_account` SET `limit_type` = '1' WHERE `bank_account`.`limit_type` = '0';
UPDATE `pfleet`.`bank_account` SET `amount` = '0' WHERE `bank_account`.`limit_type` = '1';


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

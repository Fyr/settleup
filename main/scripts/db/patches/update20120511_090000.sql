SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`reserve_account_contractor` ADD COLUMN `reserve_account_id` INT(10) UNSIGNED NOT NULL  AFTER `id` , 
  ADD CONSTRAINT `fk_reserve_account_contractor_reserve_account_id`
  FOREIGN KEY (`reserve_account_id` )
  REFERENCES `pfleet`.`reserve_account` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_reserve_account_contractor_reserve_account_id` (`reserve_account_id` ASC) ;

ALTER TABLE `pfleet`.`reserve_account_vendor` ADD COLUMN `reserve_account_id` INT(10) UNSIGNED NOT NULL  AFTER `id` , 
  ADD CONSTRAINT `fk_reserve_account_vendor_reserve_account_id`
  FOREIGN KEY (`reserve_account_id` )
  REFERENCES `pfleet`.`reserve_account` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_reserve_account_vendor_reserve_account_id` (`reserve_account_id` ASC) ;

ALTER TABLE `pfleet`.`reserve_account_carrier` ADD COLUMN `reserve_account_id` INT(10) UNSIGNED NOT NULL  AFTER `id` ,
  ADD CONSTRAINT `fk_reserve_account_carrier_reserve_account_id`
  FOREIGN KEY (`reserve_account_id` )
  REFERENCES `pfleet`.`reserve_account` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_reserve_account_carrier_reserve_account_id` (`reserve_account_id` ASC) ;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`deduction_setup` DROP FOREIGN KEY `fk_deduction_setup_contractor_id` ;

ALTER TABLE `pfleet`.`payment_setup` DROP FOREIGN KEY `fk_payment_setup_contractor_id` ;

ALTER TABLE `pfleet`.`deduction_setup` DROP COLUMN `contractor_id` 
, DROP INDEX `fk_deduction_setup_contractor_id` ;

ALTER TABLE `pfleet`.`payment_setup` DROP COLUMN `contractor_id` 
, DROP INDEX `fk_payment_setup_contractor_id` ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

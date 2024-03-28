SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`deduction_setup` ADD COLUMN `deduction_setupcol` VARCHAR(45) NULL DEFAULT NULL  AFTER `deduction_code` , ADD COLUMN `quantity` INT(11) NULL DEFAULT NULL  AFTER `deduction_setupcol` ;

UPDATE `pfleet`.`deduction_setup` SET `quantity` = '1';
UPDATE `pfleet`.`payment_setup` SET `quantity` = '1';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

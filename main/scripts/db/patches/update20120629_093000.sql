SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`reserve_transaction` DROP COLUMN `adjusted_balance_use` , DROP FOREIGN KEY `fk_reserve_transaction_status` ;

ALTER TABLE `pfleet`.`reserve_transaction` 
  ADD CONSTRAINT `fk_reserve_transaction_status`
  FOREIGN KEY (`status` )
  REFERENCES `pfleet`.`payment_status` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

UPDATE `pfleet`.`payments` SET `approved_datetime` = NULL WHERE `approved_datetime` = '0000-00-00 00:00:00';

UPDATE `pfleet`.`deductions` SET `approved_datetime` = NULL WHERE `approved_datetime` = '0000-00-00 00:00:00';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`disbursement_transaction` DROP COLUMN `submission_datetime` , DROP COLUMN `disbursement_date` , ADD COLUMN `created_by` INT(10) UNSIGNED NULL DEFAULT NULL  AFTER `created_datetime` , ADD COLUMN `approved_by` INT(10) UNSIGNED NULL DEFAULT NULL  AFTER `created_by` , ADD COLUMN `approved_datetime` DATETIME NULL DEFAULT NULL  AFTER `approved_by` , CHANGE COLUMN `source_process_code` `process_type` INT(10) UNSIGNED NULL DEFAULT NULL  , CHANGE COLUMN `status` `status` INT(11) UNSIGNED NULL DEFAULT NULL  , CHANGE COLUMN `settlement_cycle_end_date` `settlement_cycle_close_date` DATE NULL DEFAULT NULL  , 
  ADD CONSTRAINT `fk_disbursement_transaction_created_by`
  FOREIGN KEY (`created_by` )
  REFERENCES `pfleet`.`users` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_disbursement_transaction_approved_by`
  FOREIGN KEY (`approved_by` )
  REFERENCES `pfleet`.`users` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_disbursement_transaction_status`
  FOREIGN KEY (`status` )
  REFERENCES `pfleet`.`payment_status` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_disbursement_transaction_process_type`
  FOREIGN KEY (`process_type` )
  REFERENCES `pfleet`.`disbursement_transaction_type` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_disbursement_transaction_created_by` (`created_by` ASC) 
, ADD INDEX `fk_disbursement_transaction_approved_by` (`approved_by` ASC) 
, ADD INDEX `fk_disbursement_transaction_status` (`status` ASC) 
, ADD INDEX `fk_disbursement_transaction_process_type` (`process_type` ASC) ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`disbursement_transaction_type` (
  `id` INT(10) UNSIGNED NOT NULL ,
  `title` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;

INSERT INTO `pfleet`.`disbursement_transaction_type` ( `id` , `title` )
VALUES ( 1, 'Payment' ) , ( 2, 'Deduction' );

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

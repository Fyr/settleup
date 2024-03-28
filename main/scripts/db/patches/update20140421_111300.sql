ALTER TABLE `reserve_account` DROP FOREIGN KEY `fk_reserve_account_bank_account_id`;
ALTER TABLE `reserve_account` CHANGE COLUMN `bank_account_id` `bank_account_id` INT(10) UNSIGNED NULL DEFAULT NULL  ,
        ADD CONSTRAINT `fk_reserve_account_bank_account_id`
  FOREIGN KEY (`bank_account_id` )
    REFERENCES `bank_account` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION;
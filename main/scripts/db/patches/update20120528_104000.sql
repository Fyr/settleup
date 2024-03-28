SET FOREIGN_KEY_CHECKS=0;
USE `pfleet` ;

-- -----------------------------------------------------
-- Table `pfleet`.`reserve_account`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`reserve_account` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`reserve_account` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `entity_id` INT UNSIGNED NOT NULL ,
  `bank_account_id` INT UNSIGNED NOT NULL ,
  `account_name` VARCHAR(45) NULL ,
  `description` VARCHAR(255) NULL ,
  `priority` INT NULL ,
  `min_balance` DECIMAL(10,4) NULL ,
  `contribution_amount` DECIMAL(10,4) NULL ,
  `max_withdrawal_amount` DECIMAL(10,4) NULL ,
  `initial_balance` DECIMAL(10,4) NULL ,
  `current_balance` DECIMAL(10,4) NULL ,
  `disbursement_code` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_reserve_account_entity_id` (`entity_id` ASC) ,
  INDEX `fk_reserve_account_bank_account_id` (`bank_account_id` ASC) ,
  CONSTRAINT `fk_reserve_account_entity_id`
    FOREIGN KEY (`entity_id` )
    REFERENCES `pfleet`.`entity` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_account_bank_account_id`
    FOREIGN KEY (`bank_account_id` )
    REFERENCES `pfleet`.`bank_account` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
SET FOREIGN_KEY_CHECKS=1;

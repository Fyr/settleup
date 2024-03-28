-- -----------------------------------------------------
-- Table `pfleet`.`bank_account`
-- -----------------------------------------------------
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `pfleet`.`bank_account` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`bank_account` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `entity_id` INT UNSIGNED NOT NULL ,
  `account_nickname` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `payment_type` INT UNSIGNED NOT NULL ,
  `process` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `account_type` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `name_on_account` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `amount` DECIMAL(10,4) NULL DEFAULT NULL ,
  `percentage` DECIMAL(10,4) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_bank_account_entity_id` (`entity_id` ASC) ,
  INDEX `fk_bank_account_payment_type` (`payment_type` ASC) ,
  CONSTRAINT `fk_bank_account_payment_type`
    FOREIGN KEY (`payment_type` )
    REFERENCES `pfleet`.`payment_type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_bank_account_entity_id`
    FOREIGN KEY (`entity_id` )
    REFERENCES `pfleet`.`entity` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`bank_account_check`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`bank_account_check` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`bank_account_check` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `bank_account_id` INT UNSIGNED NOT NULL ,
  `bank_name` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_bank_account_check_bank_account_id` (`bank_account_id` ASC) ,
  CONSTRAINT `fk_bank_account_check_bank_account_id`
    FOREIGN KEY (`bank_account_id` )
    REFERENCES `pfleet`.`bank_account` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`bank_account_ach`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`bank_account_ach` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`bank_account_ach` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `bank_account_id` INT UNSIGNED NOT NULL ,
  `ACH_bank_routing_id` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `ACH_bank_account_id` VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_bank_account_ach_bank_account_id` (`bank_account_id` ASC) ,
  CONSTRAINT `fk_bank_account_ach_bank_account_id`
    FOREIGN KEY (`bank_account_id` )
    REFERENCES `pfleet`.`bank_account` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`bank_account_cc`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`bank_account_cc` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`bank_account_cc` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `bank_account_id` INT UNSIGNED NOT NULL ,
  `card_number` VARCHAR(255) NOT NULL ,
  `name_on_card` VARCHAR(255) NOT NULL ,
  `CC_billing_address` VARCHAR(255) NOT NULL ,
  `CC_city` VARCHAR(255) NOT NULL ,
  `CC_state` VARCHAR(255) NOT NULL ,
  `CC_zip` VARCHAR(255) NOT NULL ,
  `expiration_date` DATE NOT NULL ,
  `cvs_code` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_bank_account_cc_bank_account_id` (`bank_account_id` ASC) ,
  CONSTRAINT `fk_bank_account_cc_bank_account_id`
    FOREIGN KEY (`bank_account_id` )
    REFERENCES `pfleet`.`bank_account` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;
SET FOREIGN_KEY_CHECKS=1;
SET FOREIGN_KEY_CHECKS=0;

-- -----------------------------------------------------
-- Table `pfleet`.`deduction_setup`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`deduction_setup` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`deduction_setup` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `provider_id` INT UNSIGNED NOT NULL ,
  `contractor_id` INT UNSIGNED NOT NULL ,
  `vendor_deduction_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `description` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `category` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `department` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `gl_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `disbursement_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `priority` INT NULL DEFAULT NULL ,
  `recurring` INT NULL DEFAULT NULL ,
  `level` INT UNSIGNED NOT NULL ,
  `billing_cycle` INT UNSIGNED NOT NULL ,
  `terms` INT NULL DEFAULT NULL ,
  `last_recurring_date` DATE NULL DEFAULT NULL ,
  `last_cycle_close_day` DATE NULL DEFAULT NULL ,
  `cycle_close_date` DATE NULL DEFAULT NULL ,
  `rate` DECIMAL(10,4) NULL DEFAULT NULL ,
  `eligible` INT NULL DEFAULT NULL ,
  `reserve_account_sender` INT UNSIGNED NULL ,
  `reserve_account_receiver` INT UNSIGNED NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_deduction_setup_level` (`level` ASC) ,
  INDEX `fk_deduction_setup_billing_cycle` (`billing_cycle` ASC) ,
  INDEX `fk_deduction_setup_provider_id` (`provider_id` ASC) ,
  INDEX `fk_deduction_setup_contractor_id` (`contractor_id` ASC) ,
  INDEX `fk_deduction_setup_reserve_account_sender` (`reserve_account_sender` ASC) ,
  INDEX `fk_deduction_setup_reserve_account_receiver` (`reserve_account_receiver` ASC) ,
  CONSTRAINT `fk_deduction_setup_contractor_id`
    FOREIGN KEY (`contractor_id` )
    REFERENCES `pfleet`.`contractor` (`entity_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_deduction_setup_billing_cycle`
    FOREIGN KEY (`billing_cycle` )
    REFERENCES `pfleet`.`cycle_period` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_deduction_setup_level`
    FOREIGN KEY (`level` )
    REFERENCES `pfleet`.`setup_level` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_deduction_setup_provider_id`
    FOREIGN KEY (`provider_id` )
    REFERENCES `pfleet`.`entity` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_deduction_setup_reserve_account_sender`
    FOREIGN KEY (`reserve_account_sender` )
    REFERENCES `pfleet`.`reserve_account` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_deduction_setup_reserve_account_receiver`
    FOREIGN KEY (`reserve_account_receiver` )
    REFERENCES `pfleet`.`reserve_account` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`deductions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`deductions` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`deductions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `setup_id` INT UNSIGNED NULL DEFAULT NULL ,
  `category` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `description` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `priority` INT NULL DEFAULT NULL ,
  `invoice_id` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `invoice_date` DATE NULL DEFAULT NULL ,
  `invoice_due_date` DATE NULL DEFAULT NULL ,
  `department` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `gl_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `disbursement_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `rate` DECIMAL(10,4) NULL DEFAULT NULL ,
  `quantity` INT NULL DEFAULT NULL ,
  `amount` INT NULL DEFAULT NULL ,
  `disbursement_date` DATE NULL DEFAULT NULL ,
  `cycle_close_date` DATE NULL DEFAULT NULL ,
  `balance` DECIMAL(10,4) NULL DEFAULT NULL ,
  `adjusted_balance` DECIMAL(10,4) NULL DEFAULT NULL ,
  `adjusted_balance_use` DECIMAL(10,4) NULL DEFAULT NULL ,
  `reserve_account_contractor` INT NULL DEFAULT NULL ,
  `eligible` INT NULL DEFAULT NULL ,
  `approved_datetime` DATETIME NULL DEFAULT NULL ,
  `approved_by` INT UNSIGNED NULL DEFAULT NULL ,
  `created_datetime` DATETIME NOT NULL ,
  `created_by` INT UNSIGNED NOT NULL ,
  `source_id` INT UNSIGNED NULL DEFAULT NULL ,
  `status` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_deductions_setup_id` (`setup_id` ASC) ,
  INDEX `fk_deductions_approved_by` (`approved_by` ASC) ,
  INDEX `fk_deductions_created_by` (`created_by` ASC) ,
  INDEX `fk_deductions_source_id` (`source_id` ASC) ,
  INDEX `fk_deductions_status` (`status` ASC) ,
  CONSTRAINT `fk_deductions_status`
    FOREIGN KEY (`status` )
    REFERENCES `pfleet`.`payment_status` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_deductions_approved_by`
    FOREIGN KEY (`approved_by` )
    REFERENCES `pfleet`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_deductions_created_by`
    FOREIGN KEY (`created_by` )
    REFERENCES `pfleet`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_deductions_setup_id`
    FOREIGN KEY (`setup_id` )
    REFERENCES `pfleet`.`deduction_setup` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_deductions_source_id`
    FOREIGN KEY (`source_id` )
    REFERENCES `pfleet`.`file_storage` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`payments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`payments` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`payments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `setup_id` INT UNSIGNED NULL DEFAULT NULL ,
  `category` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `description` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `invoice` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `invoice_date` DATE NULL DEFAULT NULL ,
  `invoice_due_date` DATE NULL DEFAULT NULL ,
  `department` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `gl_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `quantity` INT NULL DEFAULT NULL ,
  `rate` DECIMAL(10,4) NULL DEFAULT NULL ,
  `amount` DECIMAL(10,4) NULL DEFAULT NULL ,
  `check_id` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `disbursement_date` DATE NULL DEFAULT NULL ,
  `cycle_close_date` DATE NULL DEFAULT NULL ,
  `approved_datetime` DATETIME NULL DEFAULT NULL ,
  `approved_by` INT UNSIGNED NULL DEFAULT NULL ,
  `created_datetime` DATETIME NOT NULL ,
  `created_by` INT UNSIGNED NOT NULL ,
  `source_id` INT UNSIGNED NULL DEFAULT NULL ,
  `status` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_payments_setup_id` (`setup_id` ASC) ,
  INDEX `fk_payments_approved_by` (`approved_by` ASC) ,
  INDEX `fk_payments_created_by` (`created_by` ASC) ,
  INDEX `fk_payments_source_id` (`source_id` ASC) ,
  INDEX `fk_payments_status` (`status` ASC) ,
  CONSTRAINT `fk_payments_status`
    FOREIGN KEY (`status` )
    REFERENCES `pfleet`.`payment_status` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_payments_approved_by`
    FOREIGN KEY (`approved_by` )
    REFERENCES `pfleet`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_payments_created_by`
    FOREIGN KEY (`created_by` )
    REFERENCES `pfleet`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_payments_setup_id`
    FOREIGN KEY (`setup_id` )
    REFERENCES `pfleet`.`payment_setup` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_payments_source_id`
    FOREIGN KEY (`source_id` )
    REFERENCES `pfleet`.`file_storage` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;

SET FOREIGN_KEY_CHECKS=1;
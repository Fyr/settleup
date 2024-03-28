SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `pfleet` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin ;
USE `pfleet` ;

-- -----------------------------------------------------
-- Table `pfleet`.`payment_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`payment_type` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`payment_type` (
  `id` INT UNSIGNED NOT NULL ,
  `title` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`user_role`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`user_role` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`user_role` (
  `id` INT UNSIGNED NOT NULL ,
  `title` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`users` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `role_id` INT UNSIGNED NULL DEFAULT NULL ,
  `email` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ,
  `name` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ,
  `password` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ,
  `last_login_ip` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_users_role_id` (`role_id` ASC) ,
  CONSTRAINT `fk_users_role_id`
    FOREIGN KEY (`role_id` )
    REFERENCES `pfleet`.`user_role` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`bank_account`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`bank_account` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`bank_account` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `owner_id` INT UNSIGNED NOT NULL ,
  `account_nickname` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `payment_type` INT UNSIGNED NOT NULL ,
  `process` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `ACH_bank_routing_id` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `ACH_bank_account_id` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `account_type` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `name_on_account` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `bank_name` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `card_number` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `name_on_card` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `CC_billing_address` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `CC_city` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `CC_state` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `CC_zip` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `expiration_date` DATE NULL DEFAULT NULL ,
  `cvs_code` INT NULL DEFAULT NULL ,
  `amount` DECIMAL(10,4) NULL DEFAULT NULL ,
  `percentage` DECIMAL(10,4) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_bank_account_owner_id` (`owner_id` ASC) ,
  INDEX `fk_bank_account_payment_type` (`payment_type` ASC) ,
  CONSTRAINT `fk_bank_account_payment_type`
    FOREIGN KEY (`payment_type` )
    REFERENCES `pfleet`.`payment_type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_bank_account_owner_id`
    FOREIGN KEY (`owner_id` )
    REFERENCES `pfleet`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`cycle_period`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`cycle_period` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`cycle_period` (
  `id` INT UNSIGNED NOT NULL ,
  `title` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`carrier`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`carrier` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`carrier` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `owner_id` INT UNSIGNED NOT NULL ,
  `tax_id` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `short_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `name` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `contact` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `settlement_cycle` INT UNSIGNED NOT NULL ,
  `settlement_day` INT NULL DEFAULT NULL ,
  `recurring_payments` INT NULL DEFAULT NULL ,
  `payment_terms` INT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_carrier_owner_id` (`owner_id` ASC) ,
  INDEX `fk_carrier_settlement_cycle` (`settlement_cycle` ASC) ,
  CONSTRAINT `fk_carrier_owner_id`
    FOREIGN KEY (`owner_id` )
    REFERENCES `pfleet`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_carrier_settlement_cycle`
    FOREIGN KEY (`settlement_cycle` )
    REFERENCES `pfleet`.`cycle_period` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`contractor_status`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`contractor_status` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`contractor_status` (
  `id` INT UNSIGNED NOT NULL ,
  `title` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`user_contact_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`user_contact_type` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`user_contact_type` (
  `id` INT UNSIGNED NOT NULL ,
  `title` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`contractor`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`contractor` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`contractor` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `owner_id` INT UNSIGNED NOT NULL ,
  `social_security_id` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `tax_id` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `company_name` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `first_name` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `last_name` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `state_of_operation` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `dob` DATE NULL DEFAULT NULL ,
  `classification` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `status` INT UNSIGNED NOT NULL ,
  `division` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `department` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `route` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `start_date` DATE NULL DEFAULT NULL ,
  `termination_date` DATE NULL DEFAULT NULL ,
  `rehire_date` DATE NULL DEFAULT NULL ,
  `rehire_status` INT NULL DEFAULT NULL ,
  `correspondence_method` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_contractor_owner_id` (`owner_id` ASC) ,
  INDEX `fk_contractor_status` (`status` ASC) ,
  INDEX `fk_contractor_correspondence_method` (`correspondence_method` ASC) ,
  CONSTRAINT `fk_contractor_owner_id`
    FOREIGN KEY (`owner_id` )
    REFERENCES `pfleet`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_contractor_status`
    FOREIGN KEY (`status` )
    REFERENCES `pfleet`.`contractor_status` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_contractor_correspondence_method`
    FOREIGN KEY (`correspondence_method` )
    REFERENCES `pfleet`.`user_contact_type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`cycle_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`cycle_type` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`cycle_type` (
  `id` INT UNSIGNED NOT NULL ,
  `title` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`cycle_date`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`cycle_date` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`cycle_date` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `cycle_type` INT UNSIGNED NOT NULL ,
  `cycle_owner` INT UNSIGNED NOT NULL ,
  `date` DATE NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_cycle_date_cycle_type` (`cycle_type` ASC) ,
  INDEX `fk_cycle_date_cycle_owner` (`cycle_owner` ASC) ,
  CONSTRAINT `fk_cycle_date_cycle_type`
    FOREIGN KEY (`cycle_type` )
    REFERENCES `pfleet`.`cycle_type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cycle_date_cycle_owner`
    FOREIGN KEY (`cycle_owner` )
    REFERENCES `pfleet`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`setup_level`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`setup_level` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`setup_level` (
  `id` INT UNSIGNED NOT NULL ,
  `title` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`vendor`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`vendor` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`vendor` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `owner_id` INT UNSIGNED NOT NULL ,
  `tax_id` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `name` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `contact` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `terms` INT NULL DEFAULT NULL ,
  `priority_level` INT NULL DEFAULT NULL ,
  `resubmit` INT NULL DEFAULT NULL ,
  `recurring_deductions` INT NULL DEFAULT NULL ,
  `reserve_account` INT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_vendor_owner_id` (`owner_id` ASC) ,
  CONSTRAINT `fk_vendor_owner_id`
    FOREIGN KEY (`owner_id` )
    REFERENCES `pfleet`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`reserve_account_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`reserve_account_type` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`reserve_account_type` (
  `id` INT UNSIGNED NOT NULL ,
  `title` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pfleet`.`reserve_account`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`reserve_account` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`reserve_account` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `type_id` INT UNSIGNED NOT NULL ,
  `owner_id` INT UNSIGNED NOT NULL ,
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
  INDEX `fk_reserve_account_type_id` (`type_id` ASC) ,
  INDEX `fk_reserve_account_owner_id` (`owner_id` ASC) ,
  INDEX `fk_reserve_account_bank_account_id` (`bank_account_id` ASC) ,
  CONSTRAINT `fk_reserve_account_type_id`
    FOREIGN KEY (`type_id` )
    REFERENCES `pfleet`.`reserve_account_type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_account_owner_id`
    FOREIGN KEY (`owner_id` )
    REFERENCES `pfleet`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_account_bank_account_id`
    FOREIGN KEY (`bank_account_id` )
    REFERENCES `pfleet`.`bank_account` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pfleet`.`deduction_setup`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`deduction_setup` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`deduction_setup` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `vendor_id` INT UNSIGNED NOT NULL ,
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
  INDEX `fk_deduction_setup_vendor_id` (`vendor_id` ASC) ,
  INDEX `fk_deduction_setup_contractor_id` (`contractor_id` ASC) ,
  INDEX `fk_deduction_setup_reserve_account_sender` (`reserve_account_sender` ASC) ,
  INDEX `fk_deduction_setup_reserve_account_receiver` (`reserve_account_receiver` ASC) ,
  CONSTRAINT `fk_deduction_setup_contractor_id`
    FOREIGN KEY (`contractor_id` )
    REFERENCES `pfleet`.`contractor` (`id` )
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
  CONSTRAINT `fk_deduction_setup_vendor_id`
    FOREIGN KEY (`vendor_id` )
    REFERENCES `pfleet`.`vendor` (`id` )
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
-- Table `pfleet`.`payment_status`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`payment_status` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`payment_status` (
  `id` INT UNSIGNED NOT NULL ,
  `title` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`file_storage`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`file_storage` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`file_storage` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `source_link` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ,
  `title` VARCHAR(45) NOT NULL ,
  `uploaded_by` INT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`deductions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`deductions` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`deductions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `setup_id` INT UNSIGNED NOT NULL ,
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
  `approved_by` INT UNSIGNED NOT NULL ,
  `created_datetime` DATETIME NULL DEFAULT NULL ,
  `created_by` INT UNSIGNED NOT NULL ,
  `source_id` INT UNSIGNED NOT NULL ,
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
-- Table `pfleet`.`payment_setup`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`payment_setup` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`payment_setup` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `carrier_id` INT UNSIGNED NOT NULL ,
  `contractor_id` INT UNSIGNED NOT NULL ,
  `payment_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `carrier_payment_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `description` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `category` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `terms` INT NULL DEFAULT NULL ,
  `department` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `gl_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `disbursement_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `recurring` INT NULL DEFAULT NULL ,
  `level` INT UNSIGNED NOT NULL ,
  `billing_cycle` INT UNSIGNED NOT NULL ,
  `last_recurring_date` DATE NULL DEFAULT NULL ,
  `cycle_close_date` DATE NULL DEFAULT NULL ,
  `rate` DECIMAL(10,4) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_payment_setup_level` (`level` ASC) ,
  INDEX `fk_payment_setup_billing_cycle` (`billing_cycle` ASC) ,
  INDEX `fk_payment_setup_carrier_id` (`carrier_id` ASC) ,
  INDEX `fk_payment_setup_contractor_id` (`contractor_id` ASC) ,
  CONSTRAINT `fk_payment_setup_contractor_id`
    FOREIGN KEY (`contractor_id` )
    REFERENCES `pfleet`.`contractor` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_setup_billing_cycle`
    FOREIGN KEY (`billing_cycle` )
    REFERENCES `pfleet`.`cycle_period` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_setup_carrier_id`
    FOREIGN KEY (`carrier_id` )
    REFERENCES `pfleet`.`carrier` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_setup_level`
    FOREIGN KEY (`level` )
    REFERENCES `pfleet`.`setup_level` (`id` )
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
  `setup_id` INT UNSIGNED NOT NULL ,
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
  `approved_by` INT UNSIGNED NOT NULL ,
  `created_datetime` DATETIME NULL DEFAULT NULL ,
  `created_by` INT UNSIGNED NOT NULL ,
  `source_id` INT UNSIGNED NOT NULL ,
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


-- -----------------------------------------------------
-- Table `pfleet`.`reserve_account_contractor`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`reserve_account_contractor` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`reserve_account_contractor` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `contractor_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_reserve_account_contractor_contractor_id` (`contractor_id` ASC) ,
  CONSTRAINT `fk_reserve_account_contractor_contractor_id`
    FOREIGN KEY (`contractor_id` )
    REFERENCES `pfleet`.`contractor` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`reserve_account_vendor`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`reserve_account_vendor` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`reserve_account_vendor` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `vendor_id` INT UNSIGNED NOT NULL ,
  `vendor_reserve_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_reserve_account_vendor_vendor_id` (`vendor_id` ASC) ,
  CONSTRAINT `fk_reserve_account_vendor_vendor_id`
    FOREIGN KEY (`vendor_id` )
    REFERENCES `pfleet`.`vendor` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`reserve_transaction_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`reserve_transaction_type` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`reserve_transaction_type` (
  `id` INT UNSIGNED NOT NULL ,
  `title` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`reserve_transaction`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`reserve_transaction` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`reserve_transaction` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `reserve_account_sender` INT UNSIGNED NOT NULL ,
  `reserve_account_receiver` INT UNSIGNED NOT NULL ,
  `vendor_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `type` INT UNSIGNED NOT NULL ,
  `deduction_id` INT UNSIGNED NOT NULL ,
  `priority` INT NULL DEFAULT NULL ,
  `amount` DECIMAL(10,4) NULL DEFAULT NULL ,
  `balance` DECIMAL(10,4) NULL DEFAULT NULL ,
  `adjusted_balance` DECIMAL(10,4) NULL DEFAULT NULL ,
  `adjusted_balance_use` DECIMAL(10,4) NULL DEFAULT NULL ,
  `settlement_cycle_date` DATE NULL DEFAULT NULL ,
  `approved_datetime` DATETIME NULL DEFAULT NULL ,
  `approved_by` INT UNSIGNED NOT NULL ,
  `created_datetime` DATETIME NULL DEFAULT NULL ,
  `created_by` INT UNSIGNED NOT NULL ,
  `source_id` INT UNSIGNED NOT NULL ,
  `disbursement_id` INT NULL DEFAULT NULL ,
  `status` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_reserve_transaction_reserve_account_sender` (`reserve_account_sender` ASC) ,
  INDEX `fk_reserve_transaction_reserve_account_receiver` (`reserve_account_receiver` ASC) ,
  INDEX `fk_reserve_transaction_type` (`type` ASC) ,
  INDEX `fk_reserve_transaction_deduction_id` (`deduction_id` ASC) ,
  INDEX `fk_reserve_transaction_approved_by` (`approved_by` ASC) ,
  INDEX `fk_reserve_transaction_created_by` (`created_by` ASC) ,
  INDEX `fk_reserve_transaction_source_id` (`source_id` ASC) ,
  CONSTRAINT `fk_reserve_transaction_source_id`
    FOREIGN KEY (`source_id` )
    REFERENCES `pfleet`.`file_storage` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_approved_by`
    FOREIGN KEY (`approved_by` )
    REFERENCES `pfleet`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_created_by`
    FOREIGN KEY (`created_by` )
    REFERENCES `pfleet`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_deduction_id`
    FOREIGN KEY (`deduction_id` )
    REFERENCES `pfleet`.`deductions` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_reserve_account_sender`
    FOREIGN KEY (`reserve_account_sender` )
    REFERENCES `pfleet`.`reserve_account` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_reserve_account_receiver`
    FOREIGN KEY (`reserve_account_receiver` )
    REFERENCES `pfleet`.`reserve_account` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserve_transaction_type`
    FOREIGN KEY (`type` )
    REFERENCES `pfleet`.`reserve_transaction_type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`user_contact_info`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`user_contact_info` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`user_contact_info` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` INT UNSIGNED NOT NULL ,
  `contact_type` INT UNSIGNED NOT NULL ,
  `value` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_user_contact_info_user_id` (`user_id` ASC) ,
  INDEX `fk_user_contact_info_contact_type` (`contact_type` ASC) ,
  CONSTRAINT `fk_user_contact_info_contact_type`
    FOREIGN KEY (`contact_type` )
    REFERENCES `pfleet`.`user_contact_type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_contact_info_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `pfleet`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`reserve_account_carrier`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`reserve_account_carrier` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`reserve_account_carrier` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `carrier_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_reserve_account_contractor_contractor_id` (`carrier_id` ASC) ,
  CONSTRAINT `fk_reserve_account_carrier_carrier_id`
    FOREIGN KEY (`carrier_id` )
    REFERENCES `pfleet`.`carrier` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`bank_account_history`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`bank_account_history` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`bank_account_history` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `bank_account_id` INT UNSIGNED NOT NULL ,
  `account_nickname` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `payment_type` INT UNSIGNED NOT NULL ,
  `process` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `ACH_bank_routing_id` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `ACH_bank_account_id` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `account_type` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `name_on_account` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `bank_name` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `card_number` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `name_on_card` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `CC_billing_address` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `CC_city` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `CC_state` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `CC_zip` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `expiration_date` DATE NULL DEFAULT NULL ,
  `cvs_code` INT NULL DEFAULT NULL ,
  `amount` DECIMAL(10,4) NULL DEFAULT NULL ,
  `percentage` DECIMAL(10,4) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_bank_account_history_payment_type` (`payment_type` ASC) ,
  INDEX `fk_bank_account_history_bank_account_id` (`bank_account_id` ASC) ,
  CONSTRAINT `fk_bank_account_history_payment_type`
    FOREIGN KEY (`payment_type` )
    REFERENCES `pfleet`.`payment_type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_bank_account_history_bank_account_id`
    FOREIGN KEY (`bank_account_id` )
    REFERENCES `pfleet`.`bank_account` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`disbursement_transaction`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`disbursement_transaction` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`disbursement_transaction` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `bank_account_history_id` INT UNSIGNED NOT NULL ,
  `owner_id` INT UNSIGNED NOT NULL ,
  `source_process_code` VARCHAR(45) NULL ,
  `code` VARCHAR(45) NULL ,
  `description` VARCHAR(45) NULL ,
  `disbursement_code` VARCHAR(45) NULL ,
  `amount` DECIMAL(10,4) NULL ,
  `status` INT NULL ,
  `settlement_cycle_end_date` DATE NULL ,
  `disbursement_date` DATE NULL ,
  `created_datetime` DATETIME NULL ,
  `submission_datetime` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_disbursement_transaction_bank_account_history_id` (`bank_account_history_id` ASC) ,
  INDEX `fk_disbursement_transaction_owner_id` (`owner_id` ASC) ,
  CONSTRAINT `fk_disbursement_transaction_bank_account_history_id`
    FOREIGN KEY (`bank_account_history_id` )
    REFERENCES `pfleet`.`bank_account_history` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_disbursement_transaction_owner_id`
    FOREIGN KEY (`owner_id` )
    REFERENCES `pfleet`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET foreign_key_checks = 0;
INSERT INTO `carrier` VALUES (3,1,'4234234','werwdfs','dsfs','fsfsdfsd',2,4,0,5);
INSERT INTO `contractor_status` VALUES (1,'Active'),(2,'Leave'),(3,'Terminated');
INSERT INTO `cycle_period` VALUES (1,'Weekly'),(2,'Biweekly'),(3,'Monthly'),(4,'Semy-monthly');
INSERT INTO `cycle_type` VALUES (1,'Close'),(2,'Disbursement');
INSERT INTO `payment_status` VALUES (1,'Verified'),(2,'Processed'),(3,'Approved');
INSERT INTO `payment_type` VALUES (1,'Check'),(2,'ACH'),(3,'Debit Card');
INSERT INTO `reserve_transaction_type` VALUES (1,'Contribution'),(2,'Withdrawal'),(3,'Cash Advance');
INSERT INTO `setup_level` VALUES (1,'Global'),(2,'Individual');
INSERT INTO `user_contact_info` VALUES (8,1,1,'dfdsfsdf');
INSERT INTO `user_contact_type` VALUES (1,'Address'),(2,'City'),(3,'State'),(4,'Zip'),(5,'Home Phone'),(6,'Office Phone'),(7,'Mobile Phone'),(8,'Email'),(9,'Fax');
INSERT INTO `user_role` VALUES (1,'Super admin'),(2,'Carrier'),(3,'Contractor'),(4,'Vendor');
INSERT INTO `users` VALUES (1,NULL,'danny@danny.com','danny','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1'),(2,NULL,'danny@true.com','Danny','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1');
SET foreign_key_checks = 1;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

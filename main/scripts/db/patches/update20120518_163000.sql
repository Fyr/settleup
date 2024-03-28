SET FOREIGN_KEY_CHECKS=0;
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
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


INSERT INTO `pfleet`.`users` VALUES (1,NULL,'danny@danny.com','danny','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1'),(2,NULL,'danny@true.com','Danny','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1'),(3,NULL,'dkozhemyako@tula.co','Daniel Kozhemyako','1a1dc91c907325c69271ddf0c944bc72','82.209.239.149'),(4,NULL,'john@smith.com','John Smith','1a1dc91c907325c69271ddf0c944bc72','82.209.239.149'),(5,NULL,'jake.zuanich@pfleet.com','Jake Zuanich','82e9dd1f989d339f09c629d0abd942d4','12.46.64.53');


-- -----------------------------------------------------
-- Table `pfleet`.`entity`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`entity` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`entity` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `entity_type_id` INT UNSIGNED NOT NULL ,
  `user_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_entity_entity_type_id` (`entity_type_id` ASC) ,
  INDEX `fk_entity_user_id` (`user_id` ASC) ,
  CONSTRAINT `fk_entity_entity_type_id`
    FOREIGN KEY (`entity_type_id` )
    REFERENCES `pfleet`.`entity_type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_entity_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `pfleet`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pfleet`.`carrier`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`carrier` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`carrier` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `entity_id` INT UNSIGNED NOT NULL ,
  `tax_id` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `short_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `name` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `contact` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `settlement_cycle` INT UNSIGNED NOT NULL ,
  `settlement_day` INT NULL DEFAULT NULL ,
  `recurring_payments` INT NULL DEFAULT NULL ,
  `payment_terms` INT NULL DEFAULT NULL ,
  INDEX `fk_carrier_settlement_cycle` (`settlement_cycle` ASC) ,
  PRIMARY KEY (`entity_id`) ,
  INDEX `fk_carrier_entity_id` (`entity_id` ASC) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  CONSTRAINT `fk_carrier_settlement_cycle`
    FOREIGN KEY (`settlement_cycle` )
    REFERENCES `pfleet`.`cycle_period` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_carrier_entity_id`
    FOREIGN KEY (`entity_id` )
    REFERENCES `pfleet`.`entity` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`contractor`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`contractor` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`contractor` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `entity_id` INT UNSIGNED NOT NULL ,
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
  INDEX `fk_contractor_status` (`status` ASC) ,
  INDEX `fk_contractor_correspondence_method` (`correspondence_method` ASC) ,
  PRIMARY KEY (`entity_id`) ,
  INDEX `fk_contactor_entity_id` (`entity_id` ASC) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  CONSTRAINT `fk_contractor_status`
    FOREIGN KEY (`status` )
    REFERENCES `pfleet`.`contractor_status` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_contractor_correspondence_method`
    FOREIGN KEY (`correspondence_method` )
    REFERENCES `pfleet`.`entity_contact_type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_contactor_entity_id`
    FOREIGN KEY (`entity_id` )
    REFERENCES `pfleet`.`entity` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`deduction_setup`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`deduction_setup` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`deduction_setup` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `provider_id` INT UNSIGNED NOT NULL ,
  `contractor_id` INT UNSIGNED NULL DEFAULT NULL ,
  `vendor_deduction_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `description` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `category` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `department` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `gl_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `disbursement_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `priority` INT NULL DEFAULT NULL ,
  `recurring` INT NULL DEFAULT NULL ,
  `level_id` INT UNSIGNED NOT NULL ,
  `billing_cycle_id` INT UNSIGNED NOT NULL ,
  `terms` INT NULL DEFAULT NULL ,
  `last_recurring_date` DATE NULL DEFAULT NULL ,
  `last_cycle_close_day` DATE NULL DEFAULT NULL ,
  `cycle_close_date` DATE NULL DEFAULT NULL ,
  `rate` DECIMAL(10,4) NULL DEFAULT NULL ,
  `eligible` INT NULL DEFAULT NULL ,
  `reserve_account_sender` INT UNSIGNED NULL ,
  `reserve_account_receiver` INT UNSIGNED NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_deduction_setup_level_id` (`level_id` ASC) ,
  INDEX `fk_deduction_setup_billing_cycle_id` (`billing_cycle_id` ASC) ,
  INDEX `fk_deduction_setup_provider_id` (`provider_id` ASC) ,
  INDEX `fk_deduction_setup_contractor_id` (`contractor_id` ASC) ,
  INDEX `fk_deduction_setup_reserve_account_sender` (`reserve_account_sender` ASC) ,
  INDEX `fk_deduction_setup_reserve_account_receiver` (`reserve_account_receiver` ASC) ,
  CONSTRAINT `fk_deduction_setup_contractor_id`
    FOREIGN KEY (`contractor_id` )
    REFERENCES `pfleet`.`contractor` (`entity_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_deduction_setup_billing_cycle_id`
    FOREIGN KEY (`billing_cycle_id` )
    REFERENCES `pfleet`.`cycle_period` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_deduction_setup_level_id`
    FOREIGN KEY (`level_id` )
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
-- Table `pfleet`.`payment_setup`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`payment_setup` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`payment_setup` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `carrier_id` INT UNSIGNED NOT NULL ,
  `contractor_id` INT UNSIGNED NULL DEFAULT NULL ,
  `payment_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `carrier_payment_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `description` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `category` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `terms` INT NULL DEFAULT NULL ,
  `department` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `gl_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `disbursement_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `recurring` INT NULL DEFAULT NULL ,
  `level_id` INT UNSIGNED NOT NULL ,
  `billing_cycle_id` INT UNSIGNED NOT NULL ,
  `last_recurring_date` DATE NULL DEFAULT NULL ,
  `cycle_close_date` DATE NULL DEFAULT NULL ,
  `rate` DECIMAL(10,4) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_payment_setup_level_id` (`level_id` ASC) ,
  INDEX `fk_payment_setup_billing_cycle_id` (`billing_cycle_id` ASC) ,
  INDEX `fk_payment_setup_carrier_id` (`carrier_id` ASC) ,
  INDEX `fk_payment_setup_contractor_id` (`contractor_id` ASC) ,
  CONSTRAINT `fk_payment_setup_contractor_id`
    FOREIGN KEY (`contractor_id` )
    REFERENCES `pfleet`.`contractor` (`entity_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_setup_billing_cycle_id`
    FOREIGN KEY (`billing_cycle_id` )
    REFERENCES `pfleet`.`cycle_period` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_setup_carrier_id`
    FOREIGN KEY (`carrier_id` )
    REFERENCES `pfleet`.`carrier` (`entity_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_payment_setup_level_id`
    FOREIGN KEY (`level_id` )
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


-- -----------------------------------------------------
-- Table `pfleet`.`entity_contact_info`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`entity_contact_info` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`entity_contact_info` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `contact_type` INT UNSIGNED NOT NULL ,
  `value` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ,
  `entity_id` INT UNSIGNED NOT NULL ,
  INDEX `fk_user_contact_info_contact_type` (`contact_type` ASC) ,
  INDEX `fk_entity_contact_info_entity_id` (`entity_id` ASC) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_user_contact_info_contact_type`
    FOREIGN KEY (`contact_type` )
    REFERENCES `pfleet`.`entity_contact_type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_entity_contact_info_entity_id`
    FOREIGN KEY (`entity_id` )
    REFERENCES `pfleet`.`entity` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`vendor`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`vendor` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`vendor` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `entity_id` INT UNSIGNED NOT NULL ,
  `tax_id` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `name` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `contact` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ,
  `terms` INT NULL DEFAULT NULL ,
  `resubmit` INT NULL DEFAULT NULL ,
  `recurring_deductions` INT NULL DEFAULT NULL ,
  `reserve_account` INT NULL DEFAULT NULL ,
  PRIMARY KEY (`entity_id`) ,
  INDEX `fk_vendor_entity_id` (`entity_id` ASC) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  CONSTRAINT `fk_vendor_entity_id`
    FOREIGN KEY (`entity_id` )
    REFERENCES `pfleet`.`entity` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


-- -----------------------------------------------------
-- Table `pfleet`.`settlement_cycle`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`settlement_cycle` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`settlement_cycle` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `carrier_id` INT UNSIGNED NOT NULL ,
  `cycle_period_id` INT UNSIGNED NOT NULL ,
  `settlement_day` INT NOT NULL ,
  `payment_terms` INT NOT NULL ,
  `disbursement_terms` INT NOT NULL ,
  `cycle_start_date` DATE NOT NULL ,
  `cycle_close_date` DATE NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_settlement_cycle_carrier_id` (`carrier_id` ASC) ,
  INDEX `fk_settlement_cycle_cycle_period_id` (`cycle_period_id` ASC) ,
  CONSTRAINT `fk_settlement_cycle_carrier_id`
    FOREIGN KEY (`carrier_id` )
    REFERENCES `pfleet`.`carrier` (`entity_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_settlement_cycle_cycle_period_id`
    FOREIGN KEY (`cycle_period_id` )
    REFERENCES `pfleet`.`cycle_period` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SET FOREIGN_KEY_CHECKS=1;

--
-- Dumping data for table `entity`
--

USE `pfleet`;

LOCK TABLES `entity` WRITE;
/*!40000 ALTER TABLE `entity` DISABLE KEYS */;
INSERT INTO `entity` VALUES (1,1,3),(2,2,3),(3,2,3),(4,2,3),(5,2,3),(6,2,3),(7,2,3),(8,2,3),(9,3,3),(10,3,3);
/*!40000 ALTER TABLE `entity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `entity_contact_info`
--

LOCK TABLES `entity_contact_info` WRITE;
/*!40000 ALTER TABLE `entity_contact_info` DISABLE KEYS */;
INSERT INTO `entity_contact_info` VALUES (1,7,'+1 888 253 5696',1),(2,1,'14 Fatin str. #170',2),(3,7,'+375 29 1643762',3),(4,1,'56 Main str. #560',4),(5,1,'56 Main str. #559',5),(6,1,'56 Main str. #545',7),(7,1,'56 Main str. #540',8);
/*!40000 ALTER TABLE `entity_contact_info` ENABLE KEYS */;
UNLOCK TABLES;


LOCK TABLES `carrier` WRITE;
/*!40000 ALTER TABLE `carrier` DISABLE KEYS */;
INSERT INTO `carrier` VALUES (1,1,'123951753','SWI','Southwest Intermodal','Jay Abraham',1,7,1,0);
/*!40000 ALTER TABLE `carrier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `contractor`
--

LOCK TABLES `contractor` WRITE;
/*!40000 ALTER TABLE `contractor` DISABLE KEYS */;
INSERT INTO `contractor` VALUES (1,2,'456159753','123956182','MSC Cyprus','Bernard','Arnault','CA','1980-04-25','',1,'Southwest','Lowes','104555','2010-11-15','2010-12-31','2011-01-04',NULL,1),(2,3,'987654320','451263897','Navibulgar','David','Bach','AZ','1970-07-25','',1,'Southwest','Home Depot','334332','2007-04-03','2007-08-03','2007-12-03',NULL,1),(3,4,'777668888','887874822','John\'s Transport','John','Smith','WA','0000-00-00','',1,'Northwest','Lowes','887878','2012-05-18','2012-05-31','2012-05-31',NULL,1),(4,5,'123456789','234567890','Best Delivery','Jim','Dalton','NV','0000-00-00','',1,'Southwest','Home Depot','334223','2012-10-10','2012-05-19','2012-05-31',NULL,1),(5,6,'222334444','123334444','Ken\'s Transport','Ken','Adams','FL','0000-00-00','',1,'Southeast','Home Depot','77878','2012-05-04','2012-05-26','2012-05-30',NULL,9),(6,7,'666558888','348887676','Quick Delivery','John','Quick','GA','0000-00-00','',1,'Southeast','Best Buy','888788','2012-05-03','2012-05-05','2012-05-17',NULL,1),(7,8,'999554545','763434555','Gonazales Delivery','Hector','Gonzales','AZ','0000-00-00','',1,'Southwest','Best Buy','776776','2012-05-11','2012-05-12','2012-05-19',NULL,1);
/*!40000 ALTER TABLE `contractor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `vendor`
--

LOCK TABLES `vendor` WRITE;
/*!40000 ALTER TABLE `vendor` DISABLE KEYS */;
INSERT INTO `vendor` VALUES (1,9,'451263897','Penske Truck Lease','Steve Ballmer',0,0,1,1),(2,10,'456397127','Soco Fuel Cards','Glenn Beck',7,0,0,0);
/*!40000 ALTER TABLE `vendor` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `deduction_setup`
--

LOCK TABLES `deduction_setup` WRITE;
/*!40000 ALTER TABLE `deduction_setup` DISABLE KEYS */;
INSERT INTO `deduction_setup` VALUES (1,1,2,'TRL','Truck Lease','Truck','','3224','FuelCode',NULL,1,1,2,0,'0000-00-00','0000-00-00','0000-00-00',300.0000,1,NULL,NULL),(2,1,2,'FUL','Fuel Cards','Fuel','','423423','',NULL,0,1,2,7,'0000-00-00','0000-00-00','0000-00-00',25.0000,0,NULL,NULL);
/*!40000 ALTER TABLE `deduction_setup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `payment_setup`
--

LOCK TABLES `payment_setup` WRITE;
/*!40000 ALTER TABLE `payment_setup` DISABLE KEYS */;
INSERT INTO `payment_setup` VALUES (1,1,2,'Delivery','Delivery','Delivery - Standard','Delivery',0,'','423423','',1,1,2,'2012-04-04','2012-05-01',75.0000),(2,1,2,'Mileage','Mileage Std','Mileage - Standard','Mileage',0,'','4234','',0,2,1,'2012-05-05','2012-05-31',0.9870);
/*!40000 ALTER TABLE `payment_setup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `settlement_cycle`
--

LOCK TABLES `settlement_cycle` WRITE;
/*!40000 ALTER TABLE `settlement_cycle` DISABLE KEYS */;
INSERT INTO `settlement_cycle` VALUES (1,1,1,7,7,6,'2012-05-18','2012-05-25'),(2,1,1,7,7,6,'2012-06-01','2012-01-13');
/*!40000 ALTER TABLE `settlement_cycle` ENABLE KEYS */;
UNLOCK TABLES;


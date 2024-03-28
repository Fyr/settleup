-- -----------------------------------------------------
-- Table `pfleet`.`settlement_cycle_status`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pfleet`.`settlement_cycle_status` ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`settlement_cycle_status` (
  `id` INT UNSIGNED NOT NULL ,
  `title` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


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
  `status_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_settlement_cycle_carrier_id` (`carrier_id` ASC) ,
  INDEX `fk_settlement_cycle_cycle_period_id` (`cycle_period_id` ASC) ,
  INDEX `fk_settlement_cycle_status_id` (`status_id` ASC) ,
  CONSTRAINT `fk_settlement_cycle_carrier_id`
    FOREIGN KEY (`carrier_id` )
    REFERENCES `pfleet`.`carrier` (`entity_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_settlement_cycle_cycle_period_id`
    FOREIGN KEY (`cycle_period_id` )
    REFERENCES `pfleet`.`cycle_period` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_settlement_cycle_status_id`
    FOREIGN KEY (`status_id` )
    REFERENCES `pfleet`.`settlement_cycle_status` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

LOCK TABLES `pfleet`.`settlement_cycle_status` WRITE;
/*!40000 ALTER TABLE `pfleet`.`settlement_cycle_status` DISABLE KEYS */;
INSERT INTO `pfleet`.`settlement_cycle_status` VALUES (1,'Not verified'),(2,'Verified'),(3,'Processing'),(4,'Approved'),(5,'Closed');
/*!40000 ALTER TABLE `pfleet`.`settlement_cycle_status` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `pfleet`.`settlement_cycle` WRITE;
/*!40000 ALTER TABLE `pfleet`.`settlement_cycle` DISABLE KEYS */;
INSERT INTO `pfleet`.`settlement_cycle` VALUES (1,1,1,7,7,6,'2012-05-18','2012-05-25',1);
/*!40000 ALTER TABLE `pfleet`.`settlement_cycle` ENABLE KEYS */;
UNLOCK TABLES;


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
CREATE  TABLE IF NOT EXISTS `contractor_vendor` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `contractor_id` INT(10) UNSIGNED NOT NULL ,
  `vendor_id` INT(10) UNSIGNED NOT NULL ,
  `status` INT(10) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_contractor_vendor_contractor_id` (`contractor_id` ASC) ,
  INDEX `fk_contractor_vendor_vendor_id` (`vendor_id` ASC) ,
  INDEX `fk_contractor_vendor_status` (`status` ASC) ,
  CONSTRAINT `fk_contractor_vendor_status0`
    FOREIGN KEY (`status` )
    REFERENCES `vendor_status` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_contractor_vendor_contractor_id0`
    FOREIGN KEY (`contractor_id` )
    REFERENCES `contractor` (`entity_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_contractor_vendor_vendor_id0`
    FOREIGN KEY (`vendor_id` )
    REFERENCES `vendor` (`entity_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin

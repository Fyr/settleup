ALTER TABLE `vendor`
  ADD COLUMN `carrier_id` INT UNSIGNED NULL DEFAULT NULL,
  ADD CONSTRAINT `fk_vendor_carrier_id`
    FOREIGN KEY (`carrier_id` )
    REFERENCES `carrier` (`entity_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
  , ADD INDEX `fk_vendor_carrier_id` (`carrier_id` ASC)
;
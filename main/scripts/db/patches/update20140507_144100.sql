ALTER TABLE `vendor` ADD COLUMN `correspondence_method` INT UNSIGNED NOT NULL DEFAULT 1, ADD CONSTRAINT `fk_vendor_correspondence_method`
  FOREIGN KEY (`correspondence_method` )
  REFERENCES `entity_contact_type` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
;
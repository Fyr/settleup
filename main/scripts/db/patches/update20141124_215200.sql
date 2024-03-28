ALTER TABLE `entity_contact_info`
DROP FOREIGN KEY `fk_entity_contact_info_entity_id`;

ALTER TABLE `entity_contact_info`
ADD COLUMN `user_id` INT(10) UNSIGNED NULL DEFAULT NULL,
ADD CONSTRAINT `fk_entity_contact_info_user_id`
  FOREIGN KEY (`user_id` )
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
CHANGE COLUMN `entity_id` `entity_id` INT(10) UNSIGNED NULL DEFAULT NULL,
ADD CONSTRAINT `fk_entity_contact_info_entity_id`
  FOREIGN KEY (`entity_id`)
  REFERENCES `entity` (`id`)
  ON DELETE NO ACTION ON UPDATE NO ACTION
;
TRUNCATE TABLE `user_permissions`;

ALTER TABLE `user_permissions`
DROP FOREIGN KEY `fk_user_permissions_entity_id`,
DROP INDEX `fk_user_permissions_entity_id`;

ALTER TABLE `user_permissions`
CHANGE COLUMN `entity_id` `user_id` INT(10) UNSIGNED NOT NULL ,
ADD INDEX `fk_user_permissions_user_id_idx` (`user_id` ASC);

ALTER TABLE `user_permissions`
ADD CONSTRAINT `fk_user_permissions_user_id`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

INSERT INTO `user_permissions` (`user_id`)
  SELECT DISTINCT `id`
  FROM `users`
  WHERE `users`.`role_id` = 2;
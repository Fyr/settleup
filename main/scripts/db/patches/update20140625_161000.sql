ALTER TABLE  `users` ADD  `entity_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `users` ADD INDEX (  `entity_id` );
ALTER TABLE  `users` ADD FOREIGN KEY (`entity_id`) REFERENCES  `entity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

UPDATE users u LEFT JOIN entity e ON e.user_id = u.id SET u.entity_id = e.id;

ALTER TABLE `entity` DROP FOREIGN KEY  `fk_entity_user_id` ;
ALTER TABLE `entity` DROP INDEX fk_entity_user_id;
ALTER TABLE `entity` DROP `user_id`;




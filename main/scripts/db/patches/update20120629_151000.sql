SET FOREIGN_KEY_CHECKS=0;

use pfleet;

INSERT INTO `users`(`role_id`, `email`, `name`, `password`, `last_login_ip`) VALUES ('2','johndoe@example.com','John Doe','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1'),
('3','contractor1@contractor1.com','contractor1','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1'),
('4','vendor1@vendor1.com','vendor1','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1');

INSERT INTO `entity`(`entity_type_id`, `user_id`) VALUES ('1', '7'), ('2', '8'), ('3', '9');

INSERT INTO `carrier`(`entity_id`, `tax_id`, `short_code`, `name`) VALUES ('12', '1234', 'JD','John Doe');

INSERT INTO `carrier_contractor` (`id`, `carrier_id`, `contractor_id`, `status`, `start_date`, `termination_date`, `rehire_date`) VALUES
(3, 12, 2, 1, '2012-06-29', NULL, NULL),
(4, 12, 3, 1, '2012-06-29', NULL, NULL),
(5, 12, 4, 1, '2012-06-29', NULL, NULL),
(6, 12, 5, 1, '2012-06-29', NULL, NULL),
(7, 12, 6, 1, '2012-06-29', NULL, NULL),
(8, 12, 7, 1, '2012-06-29', NULL, NULL);

SET FOREIGN_KEY_CHECKS=1;
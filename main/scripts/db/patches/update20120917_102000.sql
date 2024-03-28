SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

use pfleet;

INSERT INTO `users` (`id`, `role_id`, `email`, `name`, `password`, `last_login_ip`, `last_selected_carrier`) VALUES
(17, 2, 'main@test.com', 'Main', '1a1dc91c907325c69271ddf0c944bc72', '', NULL);

UPDATE `entity` SET `user_id`='17' WHERE 'id' = '1';

INSERT INTO `users_visibility` (`id`, `entity_id`, `participant_id`) VALUES
(14, 1, 2),
(15, 1, 3);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

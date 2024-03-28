ALTER TABLE `powerunit`
MODIFY COLUMN `start_date` datetime DEFAULT now(),
MODIFY COLUMN `termination_date` datetime DEFAULT NULL;
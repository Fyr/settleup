ALTER TABLE `settlement_cycle`
 ADD COLUMN `approved_datetime` datetime DEFAULT NULL,
 ADD COLUMN `approved_by` int(10) unsigned DEFAULT NULL,
 ADD CONSTRAINT `fk_settlement_cycle_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

-- UPDATE `settlement_cycle` SET `approved_by` = `created_by`, `approved_datetime` = `cycle_close_date`, `created_datetime` = `cycle_start_date` WHERE `status_id` IN (4,5) AND `approved_by` IS NULL;
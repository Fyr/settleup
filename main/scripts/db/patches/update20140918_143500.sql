ALTER TABLE `settlement_cycle`
 ADD COLUMN `disbursement_approved_datetime` datetime DEFAULT NULL,
 ADD COLUMN `disbursement_approved_by` int(10) unsigned DEFAULT NULL,
 ADD CONSTRAINT `fk_settlement_cycle_disbursement_approved_by` FOREIGN KEY (`disbursement_approved_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `pfleet`.`settlement_cycle`
ADD COLUMN `processing_date` DATE NULL DEFAULT NULL,
ADD COLUMN `disbursement_date` DATE NULL DEFAULT NULL;

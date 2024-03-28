ALTER TABLE `disbursement_transaction` ADD COLUMN `tax_id` VARCHAR(255) NULL DEFAULT NULL;

UPDATE disbursement_transaction dt LEFT JOIN contractor c ON dt.entity_id = c.entity_id SET dt.tax_id = c.tax_id WHERE dt.tax_id IS NULL;
UPDATE disbursement_transaction dt LEFT JOIN carrier car ON dt.entity_id = car.entity_id SET dt.tax_id = car.tax_id WHERE dt.tax_id IS NULL;
UPDATE disbursement_transaction dt LEFT JOIN vendor v ON dt.entity_id = v.entity_id SET dt.tax_id = v.tax_id WHERE dt.tax_id IS NULL;
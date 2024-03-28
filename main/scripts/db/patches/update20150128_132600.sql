ALTER TABLE  `payments_temp` CHANGE COLUMN `invoice_date` `invoice_date` VARCHAR(255) NULL DEFAULT NULL,
CHANGE COLUMN `invoice_due_date` `invoice_due_date` VARCHAR(255) NULL DEFAULT NULL,
CHANGE COLUMN `disbursement_date` `disbursement_date` VARCHAR(255) NULL DEFAULT NULL;

ALTER TABLE  `deductions_temp` CHANGE COLUMN `invoice_date` `invoice_date` VARCHAR(255) NULL DEFAULT NULL,
CHANGE COLUMN `invoice_due_date` `invoice_due_date` VARCHAR(255) NULL DEFAULT NULL,
CHANGE COLUMN `disbursement_date` `disbursement_date` VARCHAR(255) NULL DEFAULT NULL;


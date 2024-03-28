ALTER TABLE `contractor_temp` CHANGE COLUMN `error` `error` TEXT NULL DEFAULT NULL;
ALTER TABLE `entity_contact_info_temp` CHANGE COLUMN `error` `error` TEXT NULL DEFAULT NULL;
ALTER TABLE `contractor_vendor_temp` CHANGE COLUMN `error` `error` TEXT NULL DEFAULT NULL;
ALTER TABLE `bank_account_temp` CHANGE COLUMN `error` `error` TEXT NULL DEFAULT NULL;
ALTER TABLE `payments_temp` CHANGE COLUMN `error` `error` TEXT NULL DEFAULT NULL;
ALTER TABLE `deductions_temp` CHANGE COLUMN `error` `error` TEXT NULL DEFAULT NULL;
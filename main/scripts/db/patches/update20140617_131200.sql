ALTER TABLE `bank_account_cc`
  CHANGE COLUMN `card_number` `card_number` VARCHAR(255) NULL DEFAULT NULL,
  CHANGE COLUMN `name_on_card` `name_on_card` VARCHAR(255) NULL DEFAULT NULL,
  CHANGE COLUMN `CC_billing_address` `CC_billing_address` VARCHAR(255) NULL DEFAULT NULL,
  CHANGE COLUMN `CC_city` `CC_city` VARCHAR(255) NULL DEFAULT NULL,
  CHANGE COLUMN `CC_state` `CC_state` VARCHAR(255) NULL DEFAULT NULL,
  CHANGE COLUMN `CC_zip` `CC_zip` VARCHAR(255) NULL DEFAULT NULL,
  CHANGE COLUMN `expiration_date` `expiration_date` DATE NULL DEFAULT NULL,
  CHANGE COLUMN `cvs_code` `cvs_code` INT DEFAULT NULL,
  ADD COLUMN `ACH_bank_routing_id` VARCHAR(255) NULL DEFAULT NULL ,
  ADD COLUMN `ACH_bank_account_id` VARCHAR(255) NULL DEFAULT NULL;
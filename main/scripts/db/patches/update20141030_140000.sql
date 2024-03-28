ALTER TABLE `bank_account_history`
ADD `payee` VARCHAR(255) NOT NULL DEFAULT '',
ADD `payee_id` VARCHAR(255) NOT NULL DEFAULT '',
ADD `payee_address` VARCHAR(255) NOT NULL DEFAULT '',
ADD `payee_address_2` VARCHAR(255) NOT NULL DEFAULT '',
ADD `payee_city` VARCHAR(255) NOT NULL DEFAULT '',
ADD `payee_state` VARCHAR(255) NOT NULL DEFAULT '',
ADD `payee_zip` VARCHAR(255) NOT NULL DEFAULT '',
ADD `check_message` VARCHAR(255) NOT NULL DEFAULT '',
ADD `check_message_2` VARCHAR(255) NOT NULL DEFAULT '',
DROP `bank_name`,
DROP `name_on_card`,
DROP `CC_billing_address`,
DROP `CC_city`,
DROP `CC_state`,
DROP `CC_zip`,
DROP `expiration_date`,
DROP `cvs_code`;

UPDATE `bank_account_history` set `payee` = `name_on_account`;

ALTER TABLE `bank_account_history` DROP `name_on_account`;
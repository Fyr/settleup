ALTER TABLE `bank_account` ADD `payee` VARCHAR(255) NOT NULL DEFAULT '',
ADD `payee_id` VARCHAR(255) NOT NULL DEFAULT '';

ALTER TABLE `bank_account_check`
ADD `payee_address` VARCHAR(255) NOT NULL DEFAULT '',
ADD `payee_address_2` VARCHAR(255) NOT NULL DEFAULT '',
ADD `payee_city` VARCHAR(255) NOT NULL DEFAULT '',
ADD `payee_state` VARCHAR(255) NOT NULL DEFAULT '',
ADD `payee_zip` VARCHAR(255) NOT NULL DEFAULT '',
ADD `check_message` VARCHAR(255) NOT NULL DEFAULT '',
ADD `check_message_2` VARCHAR(255) NOT NULL DEFAULT '',
DROP `bank_name`;

ALTER TABLE `bank_account_cc`
DROP `name_on_card`,
DROP `CC_billing_address`,
DROP `CC_city`,
DROP `CC_state`,
DROP `CC_zip`,
DROP `expiration_date`,
DROP `cvs_code`;

ALTER TABLE `bank_account` DROP `name_on_account`;
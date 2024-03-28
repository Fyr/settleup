DROP DATABASE `pfleet`;
CREATE DATABASE `pfleet` CHARACTER SET utf8 COLLATE utf8_bin;

use `pfleet`;

DROP TABLE IF EXISTS `carrier`;
CREATE TABLE `carrier`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tax_id` varchar(255) NOT NULL,
  `pf_code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `payment_bank_routing_id` int(10) NOT NULL,
  `payment_bank_account_id` int(10) NOT NULL,
  `escrow_balance` decimal(12,4) NOT NULL,
  `settlement_cycle` decimal(12,4) NOT NULL,
  `hold_back` decimal(12,4) NOT NULL,
  `payment_terms` varchar(255) NOT NULL,
  `next_settlement_cycle_close_date` date NOT NULL,
  `next_payment_cycle_entry_close_date` date NOT NULL,
  `next_settlement_processing_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `contractor`;
CREATE TABLE `contractor`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(10) NOT NULL,
  `social_security_id` int(10) NOT NULL,
  `federal_tax_id` int(10) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `state_of_operation` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `position` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `status`  int(10) NOT NULL,
  `division` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `route` varchar(255) NOT NULL,
  `termination_date` date NOT NULL,
  `rehire_date` date NOT NULL,
  `rehire_status`  int(10) NOT NULL,
  `next_settlement_cycle_close_date` date NOT NULL,
  `next_settlement_processing_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `vendor`;
CREATE TABLE `vendor`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `terms` varchar(255) NOT NULL,
  `payment_type` varchar(255) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `bank_routing_id` int(10) NOT NULL,
  `bank_account_id` int(10) NOT NULL,
  `priority` int(10) NOT NULL,
  `accept_direct_payments` int(10) NOT NULL DEFAULT '1',
  `escrow_distribution_eligible` int(10) NOT NULL,
  `next_settlement_cycle_close_date` date NOT NULL,
  `next_deduction_cycle_entry_close_date` date NOT NULL,
  `next_settlement_processing_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `contracts`;
CREATE TABLE `contracts`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contractor_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `carrier_payment_setup`;
CREATE TABLE `carrier_payment_setup`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contract_id` int(10) NOT NULL,
  `description` text NULL,
  `type` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `gl_code` varchar(255) NOT NULL,
  `quantity` int(10) NOT NULL,
  `rate` decimal(12,4) NOT NULL,
  `amount` decimal(12,4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `vendor_deduction_setup`;
CREATE TABLE `vendor_deduction_setup`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` int(10) NOT NULL,
  `deduction_code` varchar(255) NOT NULL,
  `vendor_defined_unique_code` varchar(255) NOT NULL,
  `contract_id` int(10) NOT NULL,
  `description` text NULL,
  `type` varchar(255) NOT NULL,
  `terms` varchar(255) NOT NULL,
  `gl_code` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `priority` int(10) NOT NULL,
  `automatic_deductions` int(10) NOT NULL,
  `quantity` int(10) NOT NULL,
  `rate` decimal(12,4) NOT NULL,
  `amount` decimal(12,4) NOT NULL,
  `escrow_distribution_eligible` int(10) NOT NULL,
  `maximum_escrow_distribution` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `vendor_carrier`;
CREATE TABLE `vendor_carrier`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` int(10) NOT NULL,
  `carrier_id` int(10) NOT NULL,
  `vendor_code` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `last_login_ip` varchar(255) NOT NULL,
  `group` int(10) NOT NULL,
  `status` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `escrow_transactions`;
CREATE TABLE `escrow_transactions`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `escrow_account_id` int(10) NOT NULL,
  `deduction_id` int(10) NOT NULL,
  `amount` decimal(12,4) NOT NULL,
  `settlement_date` date NOT NULL,
  `approval_date_time` datetime NOT NULL,
  `approved_by` int(10) NOT NULL,
  `created_by` int(10) NOT NULL,
  `method_created` varchar(255) NOT NULL,
  `created_date_time` datetime NOT NULL,
  `disbursement_id` int(10) NOT NULL,
  `disbursement_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `escrow_account`;
CREATE TABLE `escrow_account`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contract_id` int(10) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `carrier_vendor_id` int(10) NOT NULL,
  `contribution_amount` decimal(12,4) NOT NULL,
  `priority` int(10) NOT NULL,
  `availability` int(10) NOT NULL,
  `initial_balance` decimal(12,4) NOT NULL,
  `current_balance` decimal(12,4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `disbursement_transaction`;
CREATE TABLE `disbursement_transaction`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `payment_type` varchar(255) NOT NULL,
  `contractor_id` int(10) NOT NULL,
  `carrier_id` int(10) NOT NULL,
  `vendor_id` int(10) NOT NULL,
  `description` text NULL,
  `check_id` int(10) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `bank_account_id` int(10) NOT NULL,
  `debit_card_name` varchar(255) NOT NULL,
  `debit_card_number` int(10) NOT NULL,
  `payer_name` varchar(255) NOT NULL,
  `amount` decimal(12,4) NOT NULL,
  `payment_cleared` int(10) NOT NULL,
  `settlement_date` date NOT NULL,
  `disbursement_date` date NOT NULL,
  `disbursement_availability_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `users_accounts`;
CREATE TABLE `users_accounts`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `pf_account_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `deduction`;
CREATE TABLE `deduction`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `deduction_file_import_id` int(10) NOT NULL,
  `vendor_defined_unique_code` varchar(255) NOT NULL,
  `vendor_id` int(10) NOT NULL,
  `type` varchar(255) NOT NULL,
  `contract_id` int(10) NOT NULL,
  `description` text NULL,
  `invoice_id` int(10) NOT NULL,
  `invoice_date` date NOT NULL,
  `invoice_due_date` date NOT NULL,
  `gl_code` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `rate` decimal(12,4) NOT NULL,
  `quantity` int(10) NOT NULL,
  `amount` decimal(12,4) NOT NULL,
  `settlement_date` date NOT NULL,
  `balance` decimal(12,4) NOT NULL,
  `escrow_eligible` int(10) NOT NULL,
  `approval_date_time` datetime NOT NULL,
  `approved_by` int(10) NOT NULL,
  `created_by` int(10) NOT NULL,
  `method_created` varchar(255) NOT NULL,
  `created_date_time` datetime NOT NULL,
  `disbursement` int(10) NOT NULL,
  `disbursement_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `payment`;
CREATE TABLE `payment`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payment_file_import_id` int(10) NOT NULL,
  `contract_id` int(10) NOT NULL,
  `carrier_defined_unique_code` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `description` text NULL,
  `invoice_id` int(10) NOT NULL,
  `invoice_date` date NOT NULL,
  `invoice_due_date` date NOT NULL,
  `department` varchar(255) NOT NULL,
  `gl_code` varchar(255) NOT NULL,
  `rate` decimal(12,4) NOT NULL,
  `quantity` int(10) NOT NULL,
  `amount` decimal(12,4) NOT NULL,
  `check_id` int(10) NOT NULL,
  `pay_cycle_date` date NOT NULL,
  `settlement_date` date NOT NULL,
  `payment_disbursement_date` date NOT NULL,
  `approval_date_time` datetime NOT NULL,
  `approved_by` int(10) NOT NULL,
  `created_by` int(10) NOT NULL,
  `method_created` varchar(255) NOT NULL,
  `created_date_time` datetime NOT NULL,
  `disbursement` int(10) NOT NULL,
  `disbursement_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `contractor_payment_options`;
CREATE TABLE `contractor_payment_options`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contractor_id` int(10) NOT NULL,
  `payment_type` int(10) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `ach_bank_routing_id` int(10) NOT NULL,
  `ach_bank_account_id` int(10) NOT NULL,
  `debit_card_name` varchar(255) NOT NULL,
  `debit_card_number` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `accounts_contact`;
CREATE TABLE `accounts_contact`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL,
  `contact_type` int(10) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) NOT NULL,
  `owner_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `accounts_contact_type`;
CREATE TABLE `accounts_contact_type`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `accounts_type`;
CREATE TABLE `accounts_type`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `payment_types`;
CREATE TABLE `payment_types`(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

use `pfleet`;
ALTER TABLE `bank_account_ach` DROP FOREIGN KEY `fk_bank_account_ach_bank_account_id` ,
ADD FOREIGN KEY ( `bank_account_id` ) REFERENCES `pfleet`.`bank_account` (
`id`
) ON DELETE CASCADE ON UPDATE NO ACTION ;

ALTER TABLE `bank_account_cc` DROP FOREIGN KEY `fk_bank_account_cc_bank_account_id` ,
ADD FOREIGN KEY ( `bank_account_id` ) REFERENCES `pfleet`.`bank_account` (
`id`
) ON DELETE CASCADE ON UPDATE NO ACTION ;

ALTER TABLE `bank_account_check` DROP FOREIGN KEY `fk_bank_account_check_bank_account_id` ,
ADD FOREIGN KEY ( `bank_account_id` ) REFERENCES `pfleet`.`bank_account` (
`id`
) ON DELETE CASCADE ON UPDATE NO ACTION ;

ALTER TABLE `pfleet`.`bank_account_ach` DROP INDEX `fk_bank_account_ach_bank_account_id` ,
ADD UNIQUE `fk_bank_account_ach_bank_account_id` ( `bank_account_id` );

ALTER TABLE `pfleet`.`bank_account_cc` DROP INDEX `fk_bank_account_cc_bank_account_id` ,
ADD UNIQUE `fk_bank_account_cc_bank_account_id` ( `bank_account_id` );

ALTER TABLE `pfleet`.`bank_account_check` DROP INDEX `fk_bank_account_check_bank_account_id` ,
ADD UNIQUE `fk_bank_account_check_bank_account_id` ( `bank_account_id` );

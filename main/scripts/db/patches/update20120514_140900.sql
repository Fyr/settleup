SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

TRUNCATE `pfleet`.`carrier`;
update `pfleet`.`carrier` set `owner_id` = 1 where `id` = 4;
update `pfleet`.`carrier` set `owner_id` = 2 where `id` = 5;
update `pfleet`.`carrier` set `owner_id` = 3 where `id` = 6;


update `pfleet`.`contractor` set `owner_id` = 4 where `id` = 1;
update `pfleet`.`contractor` set `owner_id` = 5 where `id` = 2;

update `pfleet`.`vendor` set `owner_id` = 6 where `id` = 1;
update `pfleet`.`vendor` set `owner_id` = 7 where `id` = 2;
update `pfleet`.`vendor` set `owner_id` = 8 where `id` = 3;


ALTER TABLE `pfleet`.`bank_account` DROP FOREIGN KEY `fk_bank_account_owner_id` ;

ALTER TABLE `pfleet`.`carrier` DROP FOREIGN KEY `fk_carrier_owner_id` ;

ALTER TABLE `pfleet`.`contractor` DROP FOREIGN KEY `fk_contractor_correspondence_method` , DROP FOREIGN KEY `fk_contractor_owner_id` ;

ALTER TABLE `pfleet`.`reserve_account_contractor` DROP FOREIGN KEY `fk_reserve_account_contractor_contractor_id` ;

ALTER TABLE `pfleet`.`reserve_account_vendor` DROP FOREIGN KEY `fk_reserve_account_vendor_vendor_id` ;

ALTER TABLE `pfleet`.`user_contact_info` DROP FOREIGN KEY `fk_user_contact_info_contact_type` , DROP FOREIGN KEY `fk_user_contact_info_user_id` ;

ALTER TABLE `pfleet`.`vendor` DROP FOREIGN KEY `fk_vendor_owner_id` ;

ALTER TABLE `pfleet`.`reserve_account` DROP FOREIGN KEY `fk_reserve_account_owner_id` , DROP FOREIGN KEY `fk_reserve_account_type_id` ;

ALTER TABLE `pfleet`.`reserve_account_carrier` DROP FOREIGN KEY `fk_reserve_account_carrier_carrier_id` ;

ALTER TABLE `pfleet`.`disbursement_transaction` DROP FOREIGN KEY `fk_disbursement_transaction_owner_id` ;

ALTER TABLE `pfleet`.`bank_account` CHANGE COLUMN `owner_id` `entity_id` INT(10) UNSIGNED NOT NULL  , 
  ADD CONSTRAINT `fk_bank_account_entity_id`
  FOREIGN KEY (`entity_id` )
  REFERENCES `pfleet`.`entity` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, DROP INDEX `fk_bank_account_owner_id` 
, ADD INDEX `fk_bank_account_entity_id` (`entity_id` ASC) ;

ALTER TABLE `pfleet`.`carrier` CHANGE COLUMN `owner_id` `entity_id` INT(10) UNSIGNED NOT NULL  , 
  ADD CONSTRAINT `fk_carrier_entity_id`
  FOREIGN KEY (`entity_id` )
  REFERENCES `pfleet`.`entity` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, DROP PRIMARY KEY 
, ADD PRIMARY KEY (`entity_id`) 
, ADD INDEX `fk_carrier_entity_id` (`entity_id` ASC) 
, ADD UNIQUE INDEX `id_UNIQUE` (`id` ASC) 
, DROP INDEX `fk_carrier_owner_id` ;

ALTER TABLE `pfleet`.`contractor` CHANGE COLUMN `owner_id` `entity_id` INT(10) UNSIGNED NOT NULL  , 
  ADD CONSTRAINT `fk_contractor_correspondence_method`
  FOREIGN KEY (`correspondence_method` )
  REFERENCES `pfleet`.`entity_contact_type` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_contactor_entity_id`
  FOREIGN KEY (`entity_id` )
  REFERENCES `pfleet`.`entity` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, DROP PRIMARY KEY 
, ADD PRIMARY KEY (`entity_id`) 
, ADD INDEX `fk_contactor_entity_id` (`entity_id` ASC) 
, ADD UNIQUE INDEX `id_UNIQUE` (`id` ASC) 
, DROP INDEX `fk_contractor_owner_id` ;

ALTER TABLE `pfleet`.`reserve_account_contractor` DROP COLUMN `contractor_id` 
, DROP INDEX `fk_reserve_account_contractor_contractor_id` ;

ALTER TABLE `pfleet`.`reserve_account_vendor` DROP COLUMN `vendor_id` 
, DROP INDEX `fk_reserve_account_vendor_vendor_id` ;


ALTER TABLE `pfleet`.`user_contact_info`  ADD COLUMN `entity_id` INT(10) UNSIGNED NOT NULL  AFTER `value` ;

update `pfleet`.`user_contact_info` set `entity_id` = 1 where `id` = 8;
update `pfleet`.`user_contact_info` set `entity_id` = 5 where `id` = 12;


ALTER TABLE `pfleet`.`user_contact_info` DROP COLUMN `user_id` ,

  ADD CONSTRAINT `fk_user_contact_info_contact_type`
  FOREIGN KEY (`contact_type` )
  REFERENCES `pfleet`.`entity_contact_type` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_entity_contact_info_entity_id`
  FOREIGN KEY (`entity_id` )
  REFERENCES `pfleet`.`entity` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, DROP PRIMARY KEY 
, ADD PRIMARY KEY (`entity_id`) 
, ADD INDEX `fk_entity_contact_info_entity_id` (`entity_id` ASC) 
, ADD UNIQUE INDEX `id_UNIQUE` (`id` ASC) 
, DROP INDEX `fk_user_contact_info_user_id` , RENAME TO  `pfleet`.`entity_contact_info` ;

ALTER TABLE `pfleet`.`user_contact_type` RENAME TO  `pfleet`.`entity_contact_type` ;

ALTER TABLE `pfleet`.`vendor` DROP COLUMN `priority_level` , CHANGE COLUMN `owner_id` `entity_id` INT(10) UNSIGNED NOT NULL  , 
  ADD CONSTRAINT `fk_vendor_entity_id`
  FOREIGN KEY (`entity_id` )
  REFERENCES `pfleet`.`entity` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, DROP PRIMARY KEY 
, ADD PRIMARY KEY (`entity_id`) 
, ADD INDEX `fk_vendor_entity_id` (`entity_id` ASC) 
, ADD UNIQUE INDEX `id_UNIQUE` (`id` ASC) 
, DROP INDEX `fk_vendor_owner_id` ;

ALTER TABLE `pfleet`.`reserve_account` DROP COLUMN `type_id` , CHANGE COLUMN `owner_id` `enity_id` INT(10) UNSIGNED NOT NULL  , 
  ADD CONSTRAINT `fk_reserve_account_entity_id`
  FOREIGN KEY (`enity_id` )
  REFERENCES `pfleet`.`entity` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, DROP INDEX `fk_reserve_account_owner_id` 
, ADD INDEX `fk_reserve_account_entity_id` (`enity_id` ASC) 
, DROP INDEX `fk_reserve_account_type_id` ;

ALTER TABLE `pfleet`.`reserve_account_carrier` DROP COLUMN `carrier_id` 
, DROP INDEX `fk_reserve_account_contractor_contractor_id` ;

ALTER TABLE `pfleet`.`disbursement_transaction` CHANGE COLUMN `owner_id` `entity_id` INT(10) UNSIGNED NOT NULL  , 
  ADD CONSTRAINT `fk_disbursement_transaction_entity_id`
  FOREIGN KEY (`entity_id` )
  REFERENCES `pfleet`.`entity` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, DROP INDEX `fk_disbursement_transaction_owner_id` 
, ADD INDEX `fk_disbursement_transaction_entity_id` (`entity_id` ASC) ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`entity_type` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(50) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;

CREATE  TABLE IF NOT EXISTS `pfleet`.`entity` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `entity_type_id` INT(10) UNSIGNED NOT NULL ,
  `user_id` INT(10) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_entity_entity_type_id` (`entity_type_id` ASC) ,
  INDEX `fk_entity_user_id` (`user_id` ASC) ,
  CONSTRAINT `fk_entity_entity_type_id`
    FOREIGN KEY (`entity_type_id` )
    REFERENCES `pfleet`.`entity_type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_entity_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `pfleet`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;

insert into `pfleet`.`entity_type` (`id`, `title`) values (1, 'carrier'), (2, 'contractor'), (3, 'vendor');

insert into `pfleet`.`entity` (  `id`,  `entity_type_id`  ,  `user_id`) values (  1, 1, 3), (2, 1, 3), (3, 1, 3);
insert into `pfleet`.`entity` (  `id`,  `entity_type_id`  ,  `user_id`) values (  4, 2, 3), (5, 2, 3);
insert into `pfleet`.`entity` (  `id`,  `entity_type_id`  ,  `user_id`) values (  6, 3, 3), (7, 3, 3), (8, 3, 3);

DROP TABLE IF EXISTS `pfleet`.`reserve_account_type` ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

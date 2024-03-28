ALTER TABLE `payment_setup`
ADD COLUMN `master_setup_id` INT(10) UNSIGNED NULL DEFAULT NULL,
ADD COLUMN `contractor_id` INT(10) UNSIGNED NULL DEFAULT NULL,
ADD COLUMN `changed` TINYINT(1) NOT NULL DEFAULT 0;

ALTER TABLE `deduction_setup`
ADD COLUMN `master_setup_id` INT(10) UNSIGNED NULL DEFAULT NULL,
ADD COLUMN `contractor_id` INT(10) UNSIGNED NULL DEFAULT NULL,
ADD COLUMN `changed` TINYINT(1) NOT NULL DEFAULT 0;

ALTER TABLE  `payment_setup` ADD INDEX (  `contractor_id` ) ;
ALTER TABLE  `payment_setup` ADD CONSTRAINT  `FK_payment_setup_contractor` FOREIGN KEY (  `contractor_id` ) REFERENCES  `contractor` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION ;
ALTER TABLE  `deduction_setup` ADD INDEX (  `contractor_id` ) ;
ALTER TABLE  `deduction_setup` ADD CONSTRAINT  `FK_deduction_setup_contractor` FOREIGN KEY (  `contractor_id` ) REFERENCES  `contractor` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION ;

ALTER TABLE  `deduction_setup` ADD INDEX (  `master_setup_id` ) ;
ALTER TABLE  `payment_setup` ADD INDEX (  `master_setup_id` ) ;
UPDATE `setup_level`
SET `title` = 'Master'
WHERE `id` = 1;

UPDATE `payment_setup` SET `level_id` = 1;
UPDATE `deduction_setup` SET `level_id` = 1;

ALTER TABLE `contractor` ADD COLUMN `deduction_priority` TINYINT(1) NOT NULL DEFAULT 1;

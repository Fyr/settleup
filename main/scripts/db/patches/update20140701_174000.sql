ALTER TABLE  `vendor` ADD  COLUMN `code` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
UPDATE `vendor` SET `code` = `tax_id` WHERE `code` IS NULL;
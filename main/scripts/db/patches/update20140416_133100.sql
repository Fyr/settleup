ALTER TABLE  `reserve_transaction` ADD  `code` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
ALTER TABLE  `reserve_transaction` ADD  `deleted` TINYINT( 1 ) NOT NULL DEFAULT  '0', ADD INDEX (  `deleted` );

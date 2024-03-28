ALTER TABLE  `bank_account` ADD  `deleted` TINYINT( 1 ) NOT NULL DEFAULT  '0', ADD INDEX (  `deleted` );

ALTER TABLE  `payments` ADD  `deleted` TINYINT( 1 ) NOT NULL DEFAULT  '0', ADD INDEX (  `deleted` );
ALTER TABLE  `deductions` ADD  `deleted` TINYINT( 1 ) NOT NULL DEFAULT  '0', ADD INDEX (  `deleted` );
ALTER TABLE  `reserve_transaction` ADD  `deleted` TINYINT( 1 ) NOT NULL DEFAULT  '0', ADD INDEX (  `deleted` );
ALTER TABLE  `payment_setup` ADD  `deleted` TINYINT( 1 ) NOT NULL DEFAULT  '0', ADD INDEX (  `deleted` );
ALTER TABLE  `deduction_setup` ADD  `deleted` TINYINT( 1 ) NOT NULL DEFAULT  '0', ADD INDEX (  `deleted` );

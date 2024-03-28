ALTER TABLE  `reserve_transaction` ADD  `contractor_id` INT( 11 ) unsigned NULL DEFAULT NULL;
ALTER TABLE  `reserve_transaction` ADD INDEX (  `contractor_id` );
ALTER TABLE  `reserve_transaction` ADD FOREIGN KEY (  `contractor_id` ) REFERENCES `contractor` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE  `reserve_transaction` CHANGE  `reserve_account_sender`  `reserve_account_sender` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `reserve_transaction` CHANGE  `reserve_account_receiver`  `reserve_account_receiver` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
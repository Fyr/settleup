ALTER TABLE  `reserve_transaction` DROP FOREIGN KEY  `fk_reserve_transaction_reserve_account_sender` ;
ALTER TABLE  `reserve_transaction` DROP FOREIGN KEY  `fk_reserve_transaction_reserve_account_receiver` ;

ALTER TABLE  `reserve_transaction` CHANGE  `reserve_account_sender`  `reserve_account_contractor` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `reserve_transaction` CHANGE  `reserve_account_receiver`  `reserve_account_vendor` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `reserve_transaction` ADD FOREIGN KEY (  `reserve_account_contractor` ) REFERENCES  `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION ;
ALTER TABLE  `reserve_transaction` ADD FOREIGN KEY (  `reserve_account_vendor` ) REFERENCES  `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION ;
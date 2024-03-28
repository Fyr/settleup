use pfleet;
ALTER TABLE `reserve_transaction` CHANGE `status` `status` INT( 10 ) UNSIGNED NOT NULL ;
ALTER TABLE `reserve_transaction` ADD INDEX `fk_reserve_transaction_status` (`status`);
ALTER TABLE `reserve_transaction`
ADD CONSTRAINT `fk_reserve_transaction_status` FOREIGN KEY (`status`)
    REFERENCES `payment_status` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION;


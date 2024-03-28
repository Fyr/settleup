use pfleet;
ALTER TABLE `reserve_transaction` CHANGE `settlement_cycle_date` `settlement_cycle_id` INT( 10 ) UNSIGNED NOT NULL ;
ALTER TABLE `reserve_transaction` ADD INDEX `fk_reserve_transaction_settlement_cycle_id` (`settlement_cycle_id`);
ALTER TABLE `reserve_transaction`
ADD CONSTRAINT `fk_reserve_transaction_settlement_cycle_id` FOREIGN KEY (`settlement_cycle_id`)
    REFERENCES `settlement_cycle` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION;


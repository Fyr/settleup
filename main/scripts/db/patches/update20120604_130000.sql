use pfleet;
ALTER TABLE `payments` ADD `settlement_cycle_id` INT(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `payments` ADD INDEX `fk_payments_settlement_cycle_id` (`settlement_cycle_id`);
ALTER TABLE `payments`
ADD CONSTRAINT `fk_payments_settlement_cycle_id` FOREIGN KEY (`settlement_cycle_id`)
    REFERENCES `settlement_cycle` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION;


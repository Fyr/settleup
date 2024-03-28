DROP TABLE IF EXISTS `reserve_account_history`;
CREATE TABLE `reserve_account_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `settlement_cycle_id` int(10) unsigned NOT NULL,
  `reserve_account_id` int(10) unsigned NOT NULL,
  `verify_balance` decimal(10,2) NOT NULL,
  `starting_balance` decimal(10,2) NOT NULL,
  `current_balance` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `reserve_account_id` (`reserve_account_id`),
  KEY `settlement_cycle_id` (`settlement_cycle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `reserve_account_history`
  ADD CONSTRAINT `reserve_account_history_reserve_account_id` FOREIGN KEY (`reserve_account_id`) REFERENCES `reserve_account` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `reserve_account_history_settlement_cycle_id` FOREIGN KEY (`settlement_cycle_id`) REFERENCES `settlement_cycle` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

DELETE FROM `reserve_account_history`;
ALTER TABLE `reserve_account_history` ADD UNIQUE  `unique_settlement_rac` (  `settlement_cycle_id` ,  `reserve_account_id` );
INSERT INTO reserve_account_history (settlement_cycle_id, reserve_account_id, verify_balance, starting_balance, current_balance)
SELECT 
    sc.id as settlement_cycle_id,
    ra.id as reserve_account_id,
    IFNULL(ra.verify_balance, 0) as verify_balance,
    IFNULL(ra.starting_balance, 0) as starting_balance,
    IFNULL(ra.current_balance, 0) as current_balance
FROM settlement_cycle sc

JOIN reserve_account ra
LEFT JOIN entity e ON ra.entity_id = e.id
WHERE ra.entity_id IN (
    SELECT cc.contractor_id FROM carrier_contractor cc WHERE cc.carrier_id = sc.carrier_id
) AND ra.deleted = 0;
ALTER TABLE `deductions` ADD COLUMN `carrier_id` INT(11) NOT NULL;

UPDATE deductions d LEFT JOIN settlement_cycle sc ON d.settlement_cycle_id = sc.id
SET d.carrier_id = sc.carrier_id
WHERE settlement_cycle_id IS NOT NULL;

DROP TABLE recurring_deduction;
DROP TABLE recurring_payment;

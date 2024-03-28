ALTER TABLE `settlement_cycle_rule`
ADD COLUMN `last_closed_cycle_id` INT(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `settlement_cycle_rule` ADD INDEX (`last_closed_cycle_id`);
ALTER TABLE `settlement_cycle_rule` ADD CONSTRAINT `fk_settlement_cycle_rule_last_cycle_id` FOREIGN KEY (`last_closed_cycle_id`)
REFERENCES `settlement_cycle` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

INSERT INTO settlement_cycle_rule (
  carrier_id,
  cycle_period_id,
  payment_terms,
  disbursement_terms,
  cycle_start_date,
  first_start_day,
  second_start_day
)
  SELECT
    carrier_id,
    cycle_period_id,
    payment_terms,
    disbursement_terms,
    cycle_start_date,
    first_start_day,
    second_start_day
  FROM
    settlement_cycle
  WHERE
    id IN (SELECT max(id)
           FROM
             settlement_cycle
           WHERE
             deleted = 0
             AND carrier_id NOT IN (SELECT carrier_id
                                    FROM
                                      settlement_cycle_rule)
           GROUP BY carrier_id);

UPDATE settlement_cycle_rule r
SET last_closed_cycle_id = (
  SELECT
    max(id)
  FROM
    settlement_cycle
  WHERE
    status_id = 4 AND
    deleted = 0 AND
    carrier_id = r.carrier_id
  GROUP BY carrier_id
);

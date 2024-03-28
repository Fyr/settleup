UPDATE `carrier` AS c
  LEFT JOIN `escrow_accounts` AS ea ON c.entity_id = ea.carrier_id
SET c.status = 0
WHERE c.status = 1 AND ea.id IS NULL
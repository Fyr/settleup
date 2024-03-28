UPDATE deductions d
LEFT JOIN settlement_cycle s ON d.settlement_cycle_id = s.id
SET d.balance = d.amount
WHERE s.status_id = 2;

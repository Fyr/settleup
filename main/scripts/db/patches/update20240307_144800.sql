ALTER TABLE deductions_temp
    ADD COLUMN amount VARCHAR(50) DEFAULT NULL AFTER rate,
    ADD COLUMN adjusted_balance VARCHAR(50) DEFAULT NULL AFTER amount,
    ADD COLUMN balance VARCHAR(50) DEFAULT NULL AFTER adjusted_balance,
    ADD COLUMN recurring VARCHAR(50) DEFAULT NULL AFTER terms,
    ADD COLUMN billing_cycle_id VARCHAR(50) DEFAULT NULL AFTER recurring,
    ADD COLUMN first_start_day VARCHAR(50) DEFAULT NULL AFTER billing_cycle_id,
    ADD COLUMN second_start_day VARCHAR(50) DEFAULT NULL AFTER first_start_day;

UPDATE deduction_setup
SET deleted = 1
WHERE powerunit_id IS NULL AND master_setup_id IS NOT NULL;

UPDATE payment_setup
SET deleted = 1
WHERE powerunit_id IS NULL AND master_setup_id IS NOT NULL;

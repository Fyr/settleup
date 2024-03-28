ALTER TABLE deductions_temp
    ADD COLUMN contractor_code VARCHAR(50) DEFAULT NULL AFTER contractor_id,
    ADD COLUMN powerunit_code VARCHAR(50) DEFAULT NULL AFTER powerunit_id,
    ADD COLUMN recurring_title VARCHAR(50) DEFAULT NULL AFTER recurring;

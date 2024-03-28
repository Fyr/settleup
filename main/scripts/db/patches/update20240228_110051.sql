ALTER TABLE deductions
    ADD COLUMN powerunit_id INT UNSIGNED NULL AFTER deleted_in_cycle,
    ADD CONSTRAINT fk_deductions_powerunit_id FOREIGN KEY (powerunit_id) REFERENCES powerunit (id),
    DROP COLUMN contractor_code;

ALTER TABLE deduction_setup
    ADD COLUMN powerunit_id INT UNSIGNED NULL AFTER master_setup_id,
    ADD CONSTRAINT fk_deduction_setup_powerunit_id FOREIGN KEY (powerunit_id) REFERENCES powerunit (id);

ALTER TABLE deductions_temp
    ADD COLUMN warning text DEFAULT NULL AFTER error,
    ADD COLUMN powerunit_id INT UNSIGNED NULL AFTER disbursement_date;

ALTER TABLE payments
    ADD COLUMN powerunit_id INT UNSIGNED NULL AFTER deleted_in_cycle,
    ADD CONSTRAINT fk_payments_powerunit_id FOREIGN KEY (powerunit_id) REFERENCES powerunit (id),
    DROP COLUMN contractor_code;

ALTER TABLE payment_setup
    ADD COLUMN powerunit_id INT UNSIGNED NULL AFTER master_setup_id,
    ADD CONSTRAINT fk_payment_setup_powerunit_id FOREIGN KEY (powerunit_id) REFERENCES powerunit (id);

ALTER TABLE payments_temp
    ADD COLUMN warning text DEFAULT NULL AFTER error,
    ADD COLUMN powerunit_id INT UNSIGNED NULL AFTER disbursement_date;

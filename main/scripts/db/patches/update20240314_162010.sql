ALTER TABLE deductions
    CHANGE recurring_parent_id deduction_parent_id INT UNSIGNED NULL,
    ADD CONSTRAINT fk_deductions_deduction_parent_id FOREIGN KEY (deduction_parent_id) REFERENCES deductions (id);

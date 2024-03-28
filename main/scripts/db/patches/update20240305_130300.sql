UPDATE deductions_temp
SET error = NULL
WHERE 1 = 1;

ALTER TABLE deductions_temp
    MODIFY COLUMN error JSON DEFAULT NULL,
    ADD COLUMN transaction_fee VARCHAR(50) DEFAULT NULL AFTER disbursement_code,
    ADD COLUMN reference VARCHAR(255) DEFAULT NULL AFTER transaction_fee,
    ADD COLUMN deduction_amount VARCHAR(50) DEFAULT NULL AFTER reference,
    ADD COLUMN provider_code VARCHAR(255) DEFAULT NULL AFTER provider_id;

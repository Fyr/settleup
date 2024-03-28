UPDATE vendor_temp
SET error = NULL
WHERE 1 = 1;

ALTER TABLE vendor_temp
    MODIFY COLUMN error JSON DEFAULT NULL,
    ADD COLUMN warning text DEFAULT NULL AFTER error,
    ADD COLUMN division_code VARCHAR(255) DEFAULT NULL AFTER code;

UPDATE powerunit_temp
SET error = NULL
WHERE 1=1;

ALTER TABLE powerunit_temp
    ADD COLUMN warning text DEFAULT NULL AFTER error,
    MODIFY COLUMN error JSON DEFAULT NULL,
    MODIFY COLUMN start_date varchar(255) DEFAULT NULL,
    MODIFY COLUMN termination_date varchar(255) DEFAULT NULL,
    MODIFY COLUMN plate_owner varchar(255) DEFAULT NULL,
    MODIFY COLUMN form2290 varchar(255) DEFAULT NULL,
    MODIFY COLUMN ifta_filing_owner varchar(255) DEFAULT NULL;


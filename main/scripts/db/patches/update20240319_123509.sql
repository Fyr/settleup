INSERT INTO file_storage_type (id, title)
VALUES (11, 'Contractor Temp'), (12, 'Powerunit Temp');

ALTER TABLE powerunit_temp
    ADD COLUMN division_code VARCHAR(50) DEFAULT NULL AFTER carrier_id;

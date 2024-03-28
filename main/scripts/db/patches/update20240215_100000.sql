ALTER TABLE contractor
    ADD COLUMN settlement_group_id INT(10) UNSIGNED DEFAULT NULL AFTER settlement_group,
    ADD COLUMN bookkeeping_type_id INT(10) UNSIGNED DEFAULT NULL AFTER bookkeeping_type,
    ADD COLUMN contact_person_type TINYINT UNSIGNED DEFAULT NULL AFTER last_name;

ALTER TABLE contractor
    ADD CONSTRAINT fk_contractor_settlement_group_id FOREIGN KEY (settlement_group_id) REFERENCES settlement_group (id),
    ADD CONSTRAINT fk_contractor_bookkeeping_type_id FOREIGN KEY (bookkeeping_type_id) REFERENCES contractor_bookkeeping_type (id),
    DROP COLUMN settlement_group,
    DROP COLUMN bookkeeping_type;

UPDATE contractor_temp
SET error = NULL
WHERE 1=1;

ALTER TABLE contractor_temp
    ADD COLUMN division_id INT(10) UNSIGNED DEFAULT NULL AFTER classification,
    ADD COLUMN warning text DEFAULT NULL AFTER error,
    ADD COLUMN settlement_group_id VARCHAR(255) DEFAULT NULL,
    ADD COLUMN bookkeeping_type_id VARCHAR(255) DEFAULT NULL,
    ADD COLUMN contact_person_type VARCHAR(255) DEFAULT NULL,
    MODIFY COLUMN correspondence_method VARCHAR(255) DEFAULT NULL,
    MODIFY COLUMN error JSON DEFAULT NULL;

UPDATE contractor
SET correspondence_method = 8
WHERE correspondence_method = 11;

UPDATE contractor
SET correspondence_method = 10
WHERE correspondence_method = 12;

DELETE FROM entity_contact_type
WHERE title IN ('Yes', 'No');

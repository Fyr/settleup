UPDATE entity_contact_info_temp
SET error = NULL
WHERE 1 = 1;

ALTER TABLE entity_contact_info_temp
    MODIFY COLUMN error JSON DEFAULT NULL;

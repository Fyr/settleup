ALTER TABLE users 
ADD COLUMN last_selected_settlement_group int(10) DEFAULT NULL;

ALTER TABLE settlement_cycle
ADD COLUMN settlement_group_id int(10) UNSIGNED,
ADD CONSTRAINT fk_settlement_cycle_settlement_group_id FOREIGN KEY (settlement_group_id) REFERENCES settlement_group(id);

-- Create settlement groups per every division when needed
INSERT INTO settlement_group (code, name, division_id)
SELECT 
    CONCAT('auto_', settlement_cycle.carrier_id, '_', settlement_cycle.id) AS code,
    CONCAT('auto_', settlement_cycle.carrier_id, '_', settlement_cycle.id) AS name,
    carrier.id AS division_id
FROM
    carrier
JOIN
    (SELECT 
        MAX(id) AS max_id,
        carrier_id
    FROM
        settlement_cycle
    WHERE
        deleted = 0
    GROUP BY carrier_id) AS newest_settlement_cycle
ON
    carrier.entity_id = newest_settlement_cycle.carrier_id
JOIN
    settlement_cycle ON newest_settlement_cycle.max_id = settlement_cycle.id
    AND settlement_cycle.deleted = 0
LEFT JOIN
    settlement_group ON settlement_cycle.settlement_group_id = settlement_group.id
WHERE
    settlement_cycle.settlement_group_id IS NULL;

-- Update settlement cycles and contractors with newly created settlement groups
UPDATE 
    settlement_cycle
JOIN 
    carrier 
ON 
    settlement_cycle.carrier_id = carrier.entity_id
JOIN 
    settlement_group 
ON 
    settlement_group.code = CONCAT('auto_', settlement_cycle.carrier_id, '_', settlement_cycle.id)
SET 
    settlement_cycle.settlement_group_id = settlement_group.id;

-- Update contractors with null settlement_group_id and assign them to the newly created settlement group for their respective division
UPDATE 
    contractor
JOIN 
    carrier 
ON 
    contractor.carrier_id = carrier.entity_id
JOIN 
    settlement_cycle 
ON 
    settlement_cycle.carrier_id = carrier.entity_id
JOIN 
    settlement_group 
ON 
    settlement_group.code = CONCAT('auto_', contractor.carrier_id, '_', settlement_cycle.id)
SET 
    contractor.settlement_group_id = settlement_group.id
WHERE 
    contractor.settlement_group_id IS NULL;
ALTER TABLE `pfleet`.`entity` ADD COLUMN `name` VARCHAR(255) NULL DEFAULT NULL  AFTER `user_id`;

use pfleet;
UPDATE entity
SET name = ( SELECT carrier.name
FROM carrier
WHERE carrier.entity_id = entity.id)
WHERE entity.entity_type_id = 1;

UPDATE entity
SET name = ( SELECT contractor.company_name
FROM contractor
WHERE contractor.entity_id = entity.id)
WHERE entity.entity_type_id = 2;

UPDATE entity
SET name = ( SELECT vendor.name
FROM vendor
WHERE vendor.entity_id = entity.id)
WHERE entity.entity_type_id = 3;

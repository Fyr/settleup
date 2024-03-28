ALTER TABLE powerunit 
DROP FOREIGN KEY fk_contractor_id;

UPDATE powerunit
JOIN contractor 
ON powerunit.contractor_id = contractor.id
SET powerunit.contractor_id = contractor.entity_id;

ALTER TABLE powerunit 
ADD CONSTRAINT fk_contractor_id FOREIGN KEY (contractor_id) REFERENCES contractor (entity_id) ON DELETE NO ACTION ON UPDATE NO ACTION;
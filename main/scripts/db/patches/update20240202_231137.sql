ALTER TABLE powerunit
DROP FOREIGN KEY fk_carrier_id;

UPDATE powerunit
JOIN carrier 
ON powerunit.carrier_id = carrier.id
SET powerunit.carrier_id = carrier.entity_id;

ALTER TABLE powerunit
ADD CONSTRAINT fk_carrier_id FOREIGN KEY (carrier_id) 
REFERENCES carrier (entity_id) ON DELETE NO ACTION ON UPDATE NO ACTION;
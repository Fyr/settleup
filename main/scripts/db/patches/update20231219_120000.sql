UPDATE user_role
SET title = 'Division'
WHERE id = 2;

INSERT INTO `entity` (entity_type_id, name)
VALUES (1, 'Linehaul');

INSERT INTO `carrier` (entity_id, name, status, create_contractor_type)
VALUES ((SELECT MAX(id) FROM entity), 'Linehaul', 1, 1);

INSERT INTO `entity` (entity_type_id, name)
VALUES (1, 'PUD ICs');

INSERT INTO `carrier` (entity_id, name, status, create_contractor_type)
VALUES ((SELECT MAX(id) FROM entity), 'PUD ICs', 1, 1);

INSERT INTO `entity` (entity_type_id, name)
VALUES (1, 'Intermodal');

INSERT INTO `carrier` (entity_id, name, status, create_contractor_type)
VALUES ((SELECT MAX(id) FROM entity), 'Intermodal', 1, 1);

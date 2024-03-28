UPDATE carrier
SET name = 'Linehaul operations'
WHERE name = 'Linehaul';

INSERT INTO settlement_group (code, name, division_id)
VALUES ('805', '805', (SELECT id FROM carrier WHERE name = 'Linehaul operations'));

INSERT INTO settlement_group (code, name, division_id)
VALUES ('814', '814', (SELECT id FROM carrier WHERE name = 'PUD ICs'));

INSERT INTO settlement_group (code, name, division_id)
VALUES ('linehaul', 'Linehaul', (SELECT id FROM carrier WHERE name = 'Linehaul operations'));

INSERT INTO settlement_group (code, name, division_id)
VALUES ('pud', 'PUD', (SELECT id FROM carrier WHERE name = 'PUD ICs'));

INSERT INTO settlement_group (code, name, division_id)
VALUES ('intermodal', 'Intermodal', (SELECT id FROM carrier WHERE name = 'Intermodal'));

INSERT INTO settlement_group (code, name, division_id)
VALUES ('omni', 'Omni', (SELECT id FROM carrier WHERE name = 'Linehaul operations'));

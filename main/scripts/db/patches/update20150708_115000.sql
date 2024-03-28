ALTER TABLE user_entity add unique `uniq_user_entity`(user_id, entity_id);
INSERT INTO user_entity (user_id, entity_id) SELECT id as user_id, entity_id FROM users where role_id IN (3,4) ON duplicate key update user_id=user_id;
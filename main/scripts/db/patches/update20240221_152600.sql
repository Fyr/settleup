ALTER TABLE user_auth_providers
ADD COLUMN ad_data JSON DEFAULT NULL AFTER user_id;

UPDATE `users` SET `email` = LOWER(`email`);
UPDATE `entity_contact_info` SET `value` = LOWER(`value`) WHERE `contact_type` = 8;
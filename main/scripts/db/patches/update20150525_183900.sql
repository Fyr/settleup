ALTER TABLE file_storage ADD COLUMN is_approved TINYINT(1) NOT NULL DEFAULT 0;
UPDATE file_storage SET is_approved = 1 WHERE is_approved = 0;
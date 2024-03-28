ALTER TABLE file_storage
    ADD COLUMN location_type varchar(255) null AFTER file_type;

UPDATE file_storage SET location_type = 'local';

ALTER TABLE file_storage
    MODIFY location_type varchar(255) not null;

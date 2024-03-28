ALTER TABLE reserve_transaction
    ADD COLUMN reference varchar(255) DEFAULT NULL AFTER description;

ALTER TABLE reserve_account
    ADD COLUMN reference varchar(255) DEFAULT NULL AFTER description;

ALTER TABLE reserve_account_history
    ADD COLUMN accumulated_interest varchar(255) DEFAULT NULL,
    ADD COLUMN created_datetime DATETIME DEFAULT now();

ALTER TABLE reserve_account
    ADD COLUMN accumulated_interest DECIMAL (18,2) NOT NULL DEFAULT 0,
    ADD COLUMN created_at DATETIME DEFAULT now();

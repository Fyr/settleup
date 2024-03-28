ALTER TABLE deductions
    ADD COLUMN `transaction_fee` decimal(18,2) DEFAULT NULL,
    ADD COLUMN `reference` varchar(255) COLLATE utf8_bin DEFAULT NULL,
    ADD COLUMN `deduction_amount` decimal(18,2) DEFAULT NULL,
    ADD COLUMN `contractor_code` varchar(50) COLLATE utf8_bin DEFAULT NULL;
ALTER TABLE payments
    ADD COLUMN `shipment_complete_date` date DEFAULT NULL,
    ADD COLUMN `driver` varchar(255) COLLATE utf8_bin DEFAULT NULL,
    ADD COLUMN `reference` varchar(255) COLLATE utf8_bin DEFAULT NULL,
    ADD COLUMN `taxable` tinyint(1) NOT NULL DEFAULT '0',
    ADD COLUMN `loaded_miles`  int(10) DEFAULT NULL,
    ADD COLUMN `empty_miles`  int(10) DEFAULT NULL,
    ADD COLUMN `origin_city` varchar(255) COLLATE utf8_bin DEFAULT NULL,
    ADD COLUMN `destination_city` varchar(255) COLLATE utf8_bin DEFAULT NULL,
    ADD COLUMN `contractor_code` varchar(50) COLLATE utf8_bin DEFAULT NULL;
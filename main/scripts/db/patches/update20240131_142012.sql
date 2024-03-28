ALTER TABLE payments_temp
    ADD COLUMN `shipment_complete_date` date DEFAULT NULL,
    ADD COLUMN `driver` varchar(255) COLLATE utf8_bin DEFAULT NULL,
    ADD COLUMN `reference` varchar(255) COLLATE utf8_bin DEFAULT NULL,
    ADD COLUMN `taxable` tinyint(1) NOT NULL DEFAULT '0',
    ADD COLUMN `loaded_miles` decimal(18,2) DEFAULT NULL,
    ADD COLUMN `empty_miles` decimal(18,2) DEFAULT NULL,
    ADD COLUMN `origin_city` varchar(255) COLLATE utf8_bin DEFAULT NULL,
    ADD COLUMN `destination_city` varchar(255) COLLATE utf8_bin DEFAULT NULL;
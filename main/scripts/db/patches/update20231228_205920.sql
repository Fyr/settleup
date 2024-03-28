ALTER TABLE `vendor`
DROP FOREIGN KEY `fk_vendor_correspondence_method`,
DROP COLUMN `tax_id`,
DROP COLUMN `contact`,
DROP COLUMN `terms`,
DROP COLUMN `correspondence_method`;
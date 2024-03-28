ALTER TABLE `powerunit` 
ADD COLUMN `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;

ALTER TABLE `powerunit_temp` 
ADD COLUMN `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
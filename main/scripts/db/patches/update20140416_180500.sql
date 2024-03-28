DELETE FROM `tbl_migrations` WHERE name = "update20140416_111700.sql";

ALTER TABLE  `contractor` ADD  `expires` DATE NULL DEFAULT NULL AFTER  `dob`;
ALTER TABLE  `contractor` ADD  `driver_license` VARCHAR( 255 ) NULL DEFAULT NULL;
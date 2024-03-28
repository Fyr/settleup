  ALTER TABLE `contractor`
  ADD COLUMN `carrier_id` int(10) unsigned NOT NULL,
  ADD COLUMN `status` int(10) unsigned NOT NULL,
  ADD COLUMN `start_date` date DEFAULT NULL,
  ADD COLUMN `termination_date` date DEFAULT NULL,
  ADD COLUMN `rehire_date` date DEFAULT NULL;

 UPDATE contractor con
  LEFT JOIN carrier_contractor car ON con.entity_id = car.contractor_id
	SET con.carrier_id = car.carrier_id,
	con.status = car.status,
	con.start_date = car.start_date,
	con.termination_date = car.termination_date,
	con.rehire_date = car.rehire_date;

UPDATE contractor
SET carrier_id = 1
WHERE carrier_id = 0;

UPDATE contractor
SET status = 1
WHERE status = 0;

ALTER TABLE  `contractor` ADD INDEX  `carrier_id` (  `carrier_id` );
ALTER TABLE  `contractor` ADD FOREIGN KEY (  `carrier_id` ) REFERENCES  `carrier` (
`entity_id`
) ON DELETE NO ACTION ON UPDATE NO ACTION ;

ALTER TABLE  `contractor` CHANGE  `code`  `code` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

-- DROP TABLE carrier_contractor;

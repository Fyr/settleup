ALTER TABLE  `vendor` ADD COLUMN  `status` TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE  `carrier` ADD COLUMN  `status` TINYINT(1) NOT NULL DEFAULT 0;

UPDATE `vendor` v
 SET v.status = 1
 WHERE (SELECT COUNT(ba.id) FROM `bank_account` ba WHERE ba.entity_id = v.entity_id) > 0
;

UPDATE `carrier` c
 SET c.status = 1
 WHERE (SELECT COUNT(ba.id) FROM `bank_account` ba WHERE ba.entity_id = c.entity_id) > 0
;
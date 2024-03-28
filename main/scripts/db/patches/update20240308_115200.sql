ALTER TABLE `powerunit`
    CHANGE COLUMN `vin` `vin` VARCHAR(50) NOT NULL ,
    CHANGE COLUMN `tractor_year` `tractor_year` SMALLINT(4) NOT NULL ,
    CHANGE COLUMN `license` `license` VARCHAR(50) NOT NULL ,
    CHANGE COLUMN `license_state` `license_state` VARCHAR(50) NOT NULL ;

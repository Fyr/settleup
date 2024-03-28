SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP TABLE IF EXISTS `pfleet`.`contractor_code` ;

UPDATE `pfleet`.`carrier_contractor` SET `code` = '563' WHERE `carrier_contractor`.`carrier_id` =1 AND `carrier_contractor`.`contractor_id` =2;
UPDATE `pfleet`.`carrier_contractor` SET `code` = '866' WHERE `carrier_contractor`.`carrier_id` =1 AND `carrier_contractor`.`contractor_id` =3;
UPDATE `pfleet`.`carrier_contractor` SET `code` = '1' WHERE `carrier_contractor`.`carrier_id` =12 AND `carrier_contractor`.`contractor_id` =2;
UPDATE `pfleet`.`carrier_contractor` SET `code` = '2' WHERE `carrier_contractor`.`carrier_id` =12 AND `carrier_contractor`.`contractor_id` =3;
UPDATE `pfleet`.`carrier_contractor` SET `code` = '3' WHERE `carrier_contractor`.`carrier_id` =12 AND `carrier_contractor`.`contractor_id` =4;
UPDATE `pfleet`.`carrier_contractor` SET `code` = '4' WHERE `carrier_contractor`.`carrier_id` =12 AND `carrier_contractor`.`contractor_id` =5;
UPDATE `pfleet`.`carrier_contractor` SET `code` = '5' WHERE `carrier_contractor`.`carrier_id` =12 AND `carrier_contractor`.`contractor_id` =6;
UPDATE `pfleet`.`carrier_contractor` SET `code` = '6' WHERE `carrier_contractor`.`carrier_id` =12 AND `carrier_contractor`.`contractor_id` =7;
UPDATE `pfleet`.`carrier_contractor` SET `code` = '1' WHERE `carrier_contractor`.`carrier_id` =15 AND `carrier_contractor`.`contractor_id` =16;
UPDATE `pfleet`.`carrier_contractor` SET `code` = '2' WHERE `carrier_contractor`.`carrier_id` =15 AND `carrier_contractor`.`contractor_id` =17;
UPDATE `pfleet`.`carrier_contractor` SET `code` = '3' WHERE `carrier_contractor`.`carrier_id` =15 AND `carrier_contractor`.`contractor_id` =18;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

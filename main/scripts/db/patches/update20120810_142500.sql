SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

UPDATE `pfleet`.`bank_account` SET `priority` = '0' WHERE `bank_account`.`id` =1;
UPDATE `pfleet`.`bank_account` SET `priority` = '1' WHERE `bank_account`.`id` =2;
UPDATE `pfleet`.`bank_account` SET `priority` = '2' WHERE `bank_account`.`id` =3;
UPDATE `pfleet`.`bank_account` SET `priority` = '3' WHERE `bank_account`.`id` =4;
UPDATE `pfleet`.`bank_account` SET `priority` = '4' WHERE `bank_account`.`id` =5;
UPDATE `pfleet`.`bank_account` SET `priority` = '5' WHERE `bank_account`.`id` =6;
UPDATE `pfleet`.`bank_account` SET `priority` = '6' WHERE `bank_account`.`id` =7;
UPDATE `pfleet`.`bank_account` SET `priority` = '7' WHERE `bank_account`.`id` =8;
UPDATE `pfleet`.`bank_account` SET `priority` = '8' WHERE `bank_account`.`id` =9;
UPDATE `pfleet`.`bank_account` SET `priority` = '9' WHERE `bank_account`.`id` =10;

UPDATE `pfleet`.`deductions` SET `priority` = '15' WHERE `deductions`.`id` =16;
UPDATE `pfleet`.`deductions` SET `priority` = '16' WHERE `deductions`.`id` =17;
UPDATE `pfleet`.`deductions` SET `priority` = '17' WHERE `deductions`.`id` =18;
UPDATE `pfleet`.`deductions` SET `priority` = '18' WHERE `deductions`.`id` =19;
UPDATE `pfleet`.`deductions` SET `priority` = '19' WHERE `deductions`.`id` =20;
UPDATE `pfleet`.`deductions` SET `priority` = '20' WHERE `deductions`.`id` =21;
UPDATE `pfleet`.`deductions` SET `priority` = '21' WHERE `deductions`.`id` =22;

UPDATE `pfleet`.`reserve_account` SET `priority` = '6' WHERE `reserve_account`.`id` =5;
UPDATE `pfleet`.`reserve_account` SET `priority` = '7' WHERE `reserve_account`.`id` =6;
UPDATE `pfleet`.`reserve_account` SET `priority` = '8' WHERE `reserve_account`.`id` =7;
UPDATE `pfleet`.`reserve_account` SET `priority` = '9' WHERE `reserve_account`.`id` =8;
UPDATE `pfleet`.`reserve_account` SET `priority` = '10' WHERE `reserve_account`.`id` =9;
UPDATE `pfleet`.`reserve_account` SET `priority` = '11' WHERE `reserve_account`.`id` =10;
UPDATE `pfleet`.`reserve_account` SET `priority` = '12' WHERE `reserve_account`.`id` =11;
UPDATE `pfleet`.`reserve_account` SET `priority` = '13' WHERE `reserve_account`.`id` =12;
UPDATE `pfleet`.`reserve_account` SET `priority` = '14' WHERE `reserve_account`.`id` =13;
UPDATE `pfleet`.`reserve_account` SET `priority` = '15' WHERE `reserve_account`.`id` =14;
UPDATE `pfleet`.`reserve_account` SET `priority` = '16' WHERE `reserve_account`.`id` =15;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

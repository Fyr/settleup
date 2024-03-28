ALTER TABLE  `reserve_transaction_type` ADD  `type_priority` TINYINT( 1 ) NOT NULL ,
ADD INDEX (  `type_priority` );
UPDATE `reserve_transaction_type` SET  `type_priority` =  '1' WHERE  `reserve_transaction_type`.`id` =5;
UPDATE `reserve_transaction_type` SET  `type_priority` =  '2' WHERE  `reserve_transaction_type`.`id` =4;
UPDATE `reserve_transaction_type` SET  `type_priority` =  '3' WHERE  `reserve_transaction_type`.`id` =1;
UPDATE `reserve_transaction_type` SET  `type_priority` =  '4' WHERE  `reserve_transaction_type`.`id` =2;
UPDATE `reserve_transaction_type` SET  `type_priority` =  '5' WHERE  `reserve_transaction_type`.`id` =3;
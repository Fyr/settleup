SET FOREIGN_KEY_CHECKS=0;
TRUNCATE `pfleet`.`users`;
LOCK TABLES `pfleet`.`users` WRITE;
INSERT INTO `pfleet`.`users` VALUES (1,NULL,'danny@danny.com','danny','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1'),(2,NULL,'danny@true.com','Danny','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1'),(3,NULL,'dkozhemyako@tula.co','Daniel Kozhemyako','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1');
UNLOCK TABLES;

TRUNCATE `pfleet`.`entity`;
LOCK TABLES `pfleet`.`entity` WRITE;
INSERT INTO `pfleet`.`entity` VALUES (1,1,3),(2,1,3),(3,1,3),(4,2,3),(5,2,3),(6,3,3),(7,3,3),(8,3,3),(9,1,3),(10,2,3),(11,2,3),(12,3,3),(13,3,3),(14,1,3);
UNLOCK TABLES;

TRUNCATE `pfleet`.`carrier`;
LOCK TABLES `pfleet`.`carrier` WRITE;
INSERT INTO `pfleet`.`carrier` VALUES (1,9,'123951753','SWI','Southwest Intermodal','Jay Abraham',1,7,1,0);
UNLOCK TABLES;

TRUNCATE `pfleet`.`contractor`;
LOCK TABLES `pfleet`.`contractor` WRITE;
INSERT INTO `pfleet`.`contractor` VALUES (4,10,'999554545','763434555','Gonazales Delivery','Hector','Gonzales','AZ','0000-00-00','',1,'Southwest','Best Buy','776776','2012-05-01','2012-05-01','2012-05-01',NULL,1),(5,11,'222334444','123334444','Ken\'s Transport','Ken','Adams','FL','0000-00-00','',1,'Southeast','Home Depot','77878','2012-05-01','2012-05-01','2012-05-01',NULL,1);
UNLOCK TABLES;

TRUNCATE `pfleet`.`entity_contact_info`;
LOCK TABLES `pfleet`.`entity_contact_info` WRITE;
INSERT INTO `pfleet`.`entity_contact_info` VALUES (8,1,'dfdsfsdf',1),(12,7,'+1 888 253 5696',5),(88,1,'dfdsfsdf',0),(89,7,'+1 888 253 5696',9);
UNLOCK TABLES;

TRUNCATE `pfleet`.`payment_setup`;
LOCK TABLES `pfleet`.`payment_setup` WRITE;
INSERT INTO `pfleet`.`payment_setup` VALUES (4,1,4,'','','Hourly','',0,'','','',1,1,2,'0000-00-00','0000-00-00',20.0000),(5,1,4,'','','Overtime','',0,'','','',0,1,2,'0000-00-00','0000-00-00',30.0000);
UNLOCK TABLES;

TRUNCATE `pfleet`.`settlement_cycle`;
LOCK TABLES `pfleet`.`settlement_cycle` WRITE;
INSERT INTO `pfleet`.`settlement_cycle` VALUES (1,9,1,7,6,'0000-00-00','0000-00-00','0000-00-00');
UNLOCK TABLES;

TRUNCATE `pfleet`.`vendor`;
LOCK TABLES `pfleet`.`vendor` WRITE;
INSERT INTO `pfleet`.`vendor` VALUES (5,12,'451263897','Penske Truck Lease','Steve Ballmer',0,0,1,1),(6,13,'456397127','Soco Fuel Cards','Glenn Beck',7,0,0,0);
UNLOCK TABLES;
SET FOREIGN_KEY_CHECKS=1;
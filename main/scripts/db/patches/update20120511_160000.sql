use `pfleet`;
SET foreign_key_checks = 0;
LOCK TABLES `bank_account` WRITE;
/*!40000 ALTER TABLE `bank_account` DISABLE KEYS */;
INSERT INTO `bank_account` VALUES (1,3,'Main Account',3,'1','1','1','1','My Main account','Bank of America','4111111111111111','Daniel Kozhemyako','14 Fatin str. #170','New York','New York','90001','2015-04-20',341,500.0000,3.0000),(2,3,'My Additional Account',3,'1','2','4','5','My additional account','Prior Bank','456123789654','Daniel Kozhemyako','14 Fatin str. #170','New York','New York','90001','2013-02-23',534,300.0000,5.0000);
/*!40000 ALTER TABLE `bank_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `carrier`
--

LOCK TABLES `carrier` WRITE;
/*!40000 ALTER TABLE `carrier` DISABLE KEYS */;
INSERT INTO `carrier` VALUES (4,3,'123951753','JAB','Jay Abraham','Jay Abraham',3,3,1,2),(5,3,'456951753','PAL','Paul Allen','Paul Allen',2,4,1,5),(6,3,'951423126','ROB','Robert Allen','Robert Allen',1,3,0,4);
/*!40000 ALTER TABLE `carrier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `contractor`
--

LOCK TABLES `contractor` WRITE;
/*!40000 ALTER TABLE `contractor` DISABLE KEYS */;
INSERT INTO `contractor` VALUES (1,3,'456159753','123956182','MSC Cyprus','Bernard','Arnault','California','1980-04-25','',1,'','','','2010-11-15','2010-12-31','2011-01-04',0,1),(2,3,'987654320','451263897','Navibulgar','David','Bach','Michigan','1970-07-25','',1,'','','','2007-04-03','2007-08-03','2007-12-03',0,1);
/*!40000 ALTER TABLE `contractor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `deduction_setup`
--

LOCK TABLES `deduction_setup` WRITE;
/*!40000 ALTER TABLE `deduction_setup` DISABLE KEYS */;
INSERT INTO `deduction_setup` VALUES (3,2,1,'VDC','Description','Some Category','Some Dep','3224','FuelCode',1,1,1,2,0,'0000-00-00','0000-00-00','0000-00-00',50.0000,1,NULL,NULL),(4,2,1,'VDCINSURANCE','Description','Insurance','Broker Dept','423423','',0,0,1,1,1,'0000-00-00','0000-00-00','0000-00-00',25.0000,0,NULL,NULL);
/*!40000 ALTER TABLE `deduction_setup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `deductions`
--

LOCK TABLES `deductions` WRITE;
/*!40000 ALTER TABLE `deductions` DISABLE KEYS */;
INSERT INTO `deductions` VALUES (1,3,'Some Category','Description',1,'','0000-00-00','0000-00-00','','myGLCode','',0.0000,0,0,'0000-00-00','0000-00-00',500.0000,0.0000,0.0000,0,0,'0000-00-00 00:00:00',3,'0000-00-00 00:00:00',3,1,3);
/*!40000 ALTER TABLE `deductions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `file_storage`
--

LOCK TABLES `file_storage` WRITE;
/*!40000 ALTER TABLE `file_storage` DISABLE KEYS */;
INSERT INTO `file_storage` VALUES (1,'dsdas','Some title',3);
/*!40000 ALTER TABLE `file_storage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `payment_setup`
--

LOCK TABLES `payment_setup` WRITE;
/*!40000 ALTER TABLE `payment_setup` DISABLE KEYS */;
INSERT INTO `payment_setup` VALUES (1,4,1,'simpleCode','carrierSimpleCode','My Description','Salary',2,'Some Dep','423423','salaryCode',1,1,1,'2012-04-25','2012-05-01',500.0000),(2,4,1,'simpleCode2','carrierSimpleCode2','Description','Fuel',3,'Broker Dept','4234','234',0,2,1,'2012-05-05','2012-05-31',30.0000);
/*!40000 ALTER TABLE `payment_setup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (6,1,'Salary','Description','','0000-00-00','0000-00-00','Some Dep','',1,500.0000,500.0000,'1564561616','2012-03-17','2012-04-25','0000-00-00 00:00:00',3,'0000-00-00 00:00:00',3,1,3),(7,2,'Fuel','Description','','0000-00-00','0000-00-00','Broker Dept','',45,30.0000,1350.0000,'terterte','0000-00-00','0000-00-00','0000-00-00 00:00:00',3,'0000-00-00 00:00:00',3,1,3);
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `user_contact_info` WRITE;
/*!40000 ALTER TABLE `user_contact_info` DISABLE KEYS */;
INSERT INTO `user_contact_info` VALUES (88,1,1,'dfdsfsdf'),(12,3,7,'+1 888 253 5696');
/*!40000 ALTER TABLE `user_contact_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (3,NULL,'dkozhemyako@tula.co','Daniel Kozhemyako','1a1dc91c907325c69271ddf0c944bc72','127.0.0.1');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `vendor`
--

LOCK TABLES `vendor` WRITE;
/*!40000 ALTER TABLE `vendor` DISABLE KEYS */;
INSERT INTO `vendor` VALUES (1,3,'451263897','Steve Ballmer','Steve Ballmer',5,3,1,1,0),(2,3,'456397127','Glenn Beck','Glenn Beck',2,1,1,1,1),(3,3,'456951123','Ben Bernanke','Ben Bernanke',1,1,1,0,0);
/*!40000 ALTER TABLE `vendor` ENABLE KEYS */;
UNLOCK TABLES;
SET foreign_key_checks = 1;
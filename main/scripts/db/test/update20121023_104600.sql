use pfleet_qa;
INSERT INTO `payment_setup` VALUES
(101,12,'','','weeklyPaymentSetup','',1,'','','',1,1,1,1.0000,NULL,NULL,'1'),
(102,12,'','','biWeeklePeymentSetup','',1,'','','',1,1,2,10.0000,NULL,NULL,'1'),
(103,12,'','','monthlyPaymentSetup','',1,'','','',1,1,3,100.0000,NULL,NULL,'1'),
(104,12,'','','semiMonthlyPaymentSetup','',1,'','','',1,1,4,1000.0000,10,20,'1');

INSERT INTO `deduction_setup` VALUES
(101,12,'','weeklyDeductionSetup','','','','',NULL,1,1,1,1,1.0000,0,NULL,1,1,'',1),
(102,12,'','biWeekleDeductionSetup','','','','',NULL,1,1,2,1,10.0000,0,NULL,1,1,'',1),
(103,12,'','monthlyDeductionSetup','','','','',NULL,1,1,3,1,100.0000,0,NULL,1,1,'',1),
(104,12,'','semiMonthlyDeductionSetup','','','','',NULL,1,1,4,1,1000.0000,0,NULL,10,20,'',1);

INSERT INTO `settlement_cycle` VALUES (101,12,3,1,2,'2013-10-18','2013-11-17',1,NULL,NULL,NULL,NULL);

INSERT INTO `users`(`id`, `role_id`, `email`, `name`, `password`) VALUES (101, 2, 'test@contactor1.loc', 'testcontractor1', '1a1dc91c907325c69271ddf0c944bc72');

INSERT INTO `entity` VALUES
(101,2,101,'contractor2');

INSERT INTO `contractor` VALUES
(101,101,'contractor2','contractor2','contractor2','contractor2','contractor2','','1980-01-01','','','','',1,2);

INSERT INTO `carrier_contractor` VALUES ('101', '12', '101', '1', '2012-01-01', NULL, NULL);

INSERT INTO `users_visibility` (`id`, `entity_id`, `participant_id`) VALUES ('101', '12', '101');

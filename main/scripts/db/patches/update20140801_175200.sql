DROP TABLE IF EXISTS `payments_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments_temp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `setup_id` int(10) unsigned NULL DEFAULT NULL,
  `category` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `invoice` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `invoice_due_date` date DEFAULT NULL,
  `department` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `gl_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `disbursement_date` date DEFAULT NULL,
  `contractor_id` int(10) unsigned NOT NULL,
  `payment_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `carrier_payment_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `terms` int(10) DEFAULT NULL,
  `disbursement_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `status_id` int(10) unsigned NOT NULL,
  `error` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `created_datetime` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `status` int(10) unsigned NOT NULL default 1,
  `source_id` int(10) unsigned NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_temp_status_id` (`status_id`),
  CONSTRAINT `payment_temp_status_id` FOREIGN KEY (`status_id`) REFERENCES `payment_temp_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2684 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

ALTER TABLE `payments` CHANGE COLUMN setup_id setup_id int(10) unsigned NULL DEFAULT NULL,
  CHANGE COLUMN billing_cycle_id billing_cycle_id int(10) unsigned NULL DEFAULT NULL;
CREATE TABLE `custom_field_names` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(10) unsigned NOT NULL,
  `payment_code` varchar(255) NOT NULL,
  `carrier_payment_code` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `gl_code` varchar(255) NOT NULL,
  `invoice` varchar(255) NOT NULL,
  `invoice_date` varchar(255) NOT NULL,
  `disbursement_code` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `carrier_id` (`carrier_id`),
  CONSTRAINT `custom_field_names_carrier_id` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`entity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
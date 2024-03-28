INSERT INTO `pfleet`.`users` (`id`, `role_id`, `email`, `name`, `password`, `last_login_ip`, `last_selected_carrier`) VALUES
(10, 2, 'car1@test.com', 'CAR1', '1a1dc91c907325c69271ddf0c944bc72', '127.0.0.1', NULL),
(11, 3, 'con1@test.com', 'CON1', '1a1dc91c907325c69271ddf0c944bc72', '127.0.0.1', NULL),
(12, 3, 'con2@test.com', 'CON2', '1a1dc91c907325c69271ddf0c944bc72', '127.0.0.1', NULL),
(13, 3, 'con3@test.com', 'CON3', '1a1dc91c907325c69271ddf0c944bc72', '127.0.0.1', NULL),
(14, 4, 'ven1@test.com', 'VEN1', '1a1dc91c907325c69271ddf0c944bc72', '127.0.0.1', NULL),
(15, 4, 'ven2@test.com', 'VEN2', '1a1dc91c907325c69271ddf0c944bc72', '127.0.0.1', NULL);

INSERT INTO `pfleet`.`entity` (`id` , `entity_type_id` , `user_id`) VALUES
( '15', '1', '10' ),
( '16', '2', '11' ),
( '17', '2', '12' ),
( '18', '2', '13' ),
( '19', '3', '14' ),
( '20', '3', '15' );

INSERT INTO `pfleet`.`carrier` (`id` , `entity_id` , `tax_id` ,`short_code` , `name` , `contact`) VALUES
(4 , '15', 'CAR1' , 'CAR1' , 'Carrier1' , 'car1 contact');

INSERT INTO `pfleet`.`contractor` (`id`, `entity_id`, `social_security_id`, `tax_id`, `company_name`, `first_name`, `last_name`, `state_of_operation`, `dob`, `classification`, `status`, `division`, `department`, `route`, `start_date`, `termination_date`, `rehire_date`, `rehire_status`, `correspondence_method`) VALUES
(8, 16, '546874290', '543969027', 'IvanovCon1', 'Ivan', 'Ivanov', 'II', '2010-01-01', '', 1, 'Southwest', 'Lowes', '104555', '2010-11-15', '2010-12-31', '2011-01-04', NULL, 1),
(9, 17, '786874290', '543945627', 'PetrovCon2', 'Petr', 'Petrov', 'PP', '2010-01-01', '', 1, 'Southwest', 'Lowes', '104555', '2010-11-15', '2010-12-31', '2011-01-04', NULL, 1),
(10, 18, '546898579', '54567678', 'SidorovCon3', 'Sidr', 'Sidorov', 'SS', '2010-01-01', '', 1, 'Southwest', 'Lowes', '104555', '2010-11-15', '2010-12-31', '2011-01-04', NULL, 1);

INSERT INTO `pfleet`.`vendor` (`id`, `entity_id`, `tax_id`, `name`, `contact`, `terms`, `resubmit`, `recurring_deductions`, `reserve_account`) VALUES
(3, 19, '568369207', 'Vasil Pypkin', 'VasilVen1Co', 0, 0, 1, 1),
(4, 20, '947893186', 'Valera Pypkin', 'ValerVen2Co', 7, 0, 0, 0);

INSERT INTO `pfleet`.`carrier_contractor` (`id`, `carrier_id`, `contractor_id`, `status`, `start_date`, `termination_date`, `rehire_date`) VALUES
(9, 15, 16, 1, '2012-07-11', NULL, NULL),
(10, 15, 17, 1, '2012-07-11', NULL, NULL),
(11, 15, 18, 1, '2012-07-11', NULL, NULL);


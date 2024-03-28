use pfleet;
ALTER TABLE `reserve_transaction` CHANGE `approved_by` `approved_by` INT( 10 ) UNSIGNED NULL DEFAULT NULL ;
ALTER TABLE `reserve_transaction` CHANGE `deduction_id` `deduction_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL ;
ALTER TABLE `reserve_transaction` CHANGE `source_id` `source_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL ;


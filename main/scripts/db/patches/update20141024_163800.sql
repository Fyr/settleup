ALTER TABLE `user_permissions`
 ADD COLUMN `reserve_transaction_vendor_view` TINYINT(1) NOT NULL DEFAULT 1 AFTER `settlement_escrow_account_view`,
 ADD COLUMN `reserve_account_contractor_view` TINYINT(1) NOT NULL DEFAULT 1 AFTER `reserve_account_vendor_manage`,
 ADD COLUMN `permissions_manage` TINYINT(1) NOT NULL DEFAULT 1
;
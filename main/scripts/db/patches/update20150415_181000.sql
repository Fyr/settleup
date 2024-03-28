ALTER TABLE `user_permissions`
ADD COLUMN `vendor_user_create` TINYINT(1) NOT NULL DEFAULT 1,
ADD COLUMN `contractor_user_create` TINYINT(1) NOT NULL DEFAULT 1;
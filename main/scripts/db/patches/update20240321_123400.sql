ALTER TABLE `user_permissions`
    ADD COLUMN `settlement_export` TINYINT(1) NOT NULL DEFAULT '1' AFTER `settlement_approve`;
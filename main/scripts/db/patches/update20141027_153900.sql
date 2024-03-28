TRUNCATE TABLE `user_permissions`;

ALTER TABLE `user_permissions`
  CHANGE COLUMN `permissions_manage` `permissions_manage` TINYINT(1) NOT NULL DEFAULT 0,
  CHANGE COLUMN `contractor_vendor_auth_manage` `permissions_manage` TINYINT(1) NOT NULL DEFAULT 0
;

INSERT INTO `user_permissions` (`user_id`)
  SELECT DISTINCT `id`
  FROM `users`;
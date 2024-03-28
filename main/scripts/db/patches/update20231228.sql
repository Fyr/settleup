--  Every new vendor will be set as CONFIGURED status by default.
ALTER TABLE `vendor` MODIFY `status` tinyint(1) NOT NULL DEFAULT '1';
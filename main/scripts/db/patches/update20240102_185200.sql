--  Every new carrier will be set as CONFIGURED status by default.
ALTER TABLE `carrier` MODIFY `status` tinyint(1) NOT NULL DEFAULT '1';
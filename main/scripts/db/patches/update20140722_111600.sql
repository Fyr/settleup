ALTER TABLE `payment_setup` ADD COLUMN `week_offset` TINYINT(1) NULL DEFAULT NULL;
ALTER TABLE `payments` ADD COLUMN `week_offset` TINYINT(1) NULL DEFAULT NULL;
ALTER TABLE `deduction_setup` ADD COLUMN `week_offset` TINYINT(1) NULL DEFAULT NULL;
ALTER TABLE `deductions` ADD COLUMN `week_offset` TINYINT(1) NULL DEFAULT NULL;
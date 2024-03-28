ALTER TABLE `payment_setup` DROP COLUMN `week_day`;
ALTER TABLE `payments` DROP COLUMN `week_day`;
ALTER TABLE `deduction_setup` DROP COLUMN `week_day`;
ALTER TABLE `deductions` DROP COLUMN `week_day`;
ALTER TABLE `settlement_cycle` DROP COLUMN `week_day`;
ALTER TABLE `settlement_cycle_rule` DROP COLUMN `week_day`;
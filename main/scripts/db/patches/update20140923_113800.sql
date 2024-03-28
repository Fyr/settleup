DELETE FROM `cycle_period` WHERE id = 6;
INSERT INTO `cycle_period` VALUES (6, 'Semi-Weekly');

UPDATE `payment_setup` SET `first_start_day` = `week_day` WHERE `billing_cycle_id` IN (1,2);
UPDATE `payments` SET `first_start_day` = `week_day` WHERE `billing_cycle_id` IN (1,2);
UPDATE `deduction_setup` SET `first_start_day` = `week_day` WHERE `billing_cycle_id` IN (1,2);
UPDATE `deductions` SET `first_start_day` = `week_day` WHERE `billing_cycle_id` IN (1,2);
UPDATE `settlement_cycle` SET `first_start_day` = `week_day` WHERE `cycle_period_id` IN (1,2);
UPDATE `settlement_cycle_rule` SET `first_start_day` = `week_day` WHERE `cycle_period_id` IN (1,2);

ALTER TABLE `payment_setup` DROP COLUMN `week_day`;
ALTER TABLE `payments` DROP COLUMN `week_day`;
ALTER TABLE `deduction_setup` DROP COLUMN `week_day`;
ALTER TABLE `deductions` DROP COLUMN `week_day`;
ALTER TABLE `settlement_cycle` DROP COLUMN `week_day`;
ALTER TABLE `settlement_cycle_rule` DROP COLUMN `week_day`;
ALTER TABLE `reserve_account_history` MODIFY COLUMN verify_balance decimal(10,2) not null DEFAULT 0;
ALTER TABLE `reserve_account_history` MODIFY COLUMN starting_balance decimal(10,2) not null DEFAULT 0;
ALTER TABLE `reserve_account_history` MODIFY COLUMN current_balance decimal(10,2) not null DEFAULT 0;


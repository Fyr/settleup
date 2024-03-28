ALTER TABLE `escrow_accounts`
ADD COLUMN `holder_address` varchar(255) NOT NULL,
ADD COLUMN `holder_address_2` varchar(255) NOT NULL,
ADD COLUMN `holder_city` varchar(255) NOT NULL,
ADD COLUMN `holder_state` varchar(255) NOT NULL,
ADD COLUMN `holder_zip` varchar(255) NOT NULL;

ALTER TABLE `escrow_accounts_history`
ADD COLUMN `holder_address` varchar(255) NOT NULL,
ADD COLUMN `holder_address_2` varchar(255) NOT NULL,
ADD COLUMN `holder_city` varchar(255) NOT NULL,
ADD COLUMN `holder_state` varchar(255) NOT NULL,
ADD COLUMN `holder_zip` varchar(255) NOT NULL;

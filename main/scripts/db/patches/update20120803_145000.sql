DELETE FROM `pfleet`.`bank_account_check` WHERE `bank_account_check`.`bank_account_id` = 7;

INSERT INTO `pfleet`.`bank_account_ach` (
`id` ,
`bank_account_id` ,
`ACH_bank_routing_id` ,
`ACH_bank_account_id`
)
VALUES (
NULL , '7', '123456', '234567'
);

UPDATE `pfleet`.`bank_account` SET `payment_type` = '2' WHERE `bank_account`.`id` =7;
ALTER TABLE `contractor`
    ADD COLUMN bookkeeping_type int(10) null,
    ADD COLUMN notes            text    null;

CREATE TABLE `contractor_bookkeeping_type`
(
    id    int unsigned not null primary key,
    title varchar(45)  not null
);

INSERT INTO `contractor_bookkeeping_type`
VALUES (1, 'ATBS'),
       (2, 'Equinox');

INSERT INTO `contractor_status`
VALUES (5, 'Inactive');

INSERT INTO `entity_contact_type`
VALUES (11, 'Yes'),
       (12, 'No');

ALTER TABLE `file_storage`
    ADD COLUMN entity_id int(10) null,
    ADD COLUMN deleted   tinyint(1) default 0;

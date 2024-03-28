CREATE TABLE settlement_group
(
    id          INT UNSIGNED auto_increment PRIMARY KEY,
    code        VARCHAR(255) NOT NULL,
    name        VARCHAR(255) NOT NULL,
    division_id INT UNSIGNED NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted     TINYINT(1) DEFAULT 0,
    CONSTRAINT fk_settlement_group_division_id FOREIGN KEY (division_id) REFERENCES carrier (id)
);

ALTER TABLE user_permissions
    ADD COLUMN settlement_group_view TINYINT(1) NOT NULL DEFAULT 1 after settlement_rule_manage,
    ADD COLUMN settlement_group_manage TINYINT(1) NOT NULL DEFAULT 1 after settlement_group_view;

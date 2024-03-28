ALTER TABLE payments DROP FOREIGN KEY fk_payments_recurring;
ALTER TABLE payments DROP INDEX fk_payments_recurring;
ALTER TABLE payments DROP INDEX deleted;
ALTER TABLE payments ADD INDEX settlement_deleted_index (settlement_cycle_id, deleted);
ALTER TABLE deductions DROP INDEX deleted;
ALTER TABLE deductions ADD INDEX settlement_deleted_index (settlement_cycle_id, deleted);
ALTER TABLE reserve_transaction DROP INDEX deleted;
ALTER TABLE reserve_transaction ADD INDEX settlement_deleted_index (settlement_cycle_id, deleted, type);


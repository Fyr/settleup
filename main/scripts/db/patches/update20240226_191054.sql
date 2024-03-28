ALTER TABLE payments
    ADD COLUMN compensation_code VARCHAR(255) DEFAULT NULL AFTER carrier_payment_code;

ALTER TABLE payments_temp
    ADD COLUMN compensation_code VARCHAR(255) DEFAULT NULL AFTER carrier_payment_code;

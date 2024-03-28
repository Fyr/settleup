use pfleet;

UPDATE payments
SET carrier_id = ( SELECT payment_setup.carrier_id
FROM payment_setup
WHERE payment_setup.id = payments.setup_id);

UPDATE payments
SET carrier_payment_code = ( SELECT payment_setup.carrier_payment_code
FROM payment_setup
WHERE payment_setup.id = payments.setup_id);

UPDATE payments
SET terms = ( SELECT payment_setup.terms
FROM payment_setup
WHERE payment_setup.id = payments.setup_id);

UPDATE payments
SET disbursement_code = ( SELECT payment_setup.disbursement_code
FROM payment_setup
WHERE payment_setup.id = payments.setup_id);

UPDATE payments
SET recurring = ( SELECT payment_setup.recurring
FROM payment_setup
WHERE payment_setup.id = payments.setup_id);

UPDATE payments
SET billing_cycle_id = ( SELECT payment_setup.billing_cycle_id
FROM payment_setup
WHERE payment_setup.id = payments.setup_id);

UPDATE payments
SET first_start_day = ( SELECT payment_setup.first_start_day
FROM payment_setup
WHERE payment_setup.id = payments.setup_id);

UPDATE payments
SET second_start_day = ( SELECT payment_setup.second_start_day
FROM payment_setup
WHERE payment_setup.id = payments.setup_id);
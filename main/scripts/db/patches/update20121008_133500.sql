use pfleet;

UPDATE deductions
SET provider_id = ( SELECT deduction_setup.provider_id
FROM deduction_setup
WHERE deduction_setup.id = deductions.setup_id);

UPDATE deductions
SET terms = ( SELECT deduction_setup.terms
FROM deduction_setup
WHERE deduction_setup.id = deductions.setup_id);

UPDATE deductions
SET recurring = ( SELECT deduction_setup.recurring
FROM deduction_setup
WHERE deduction_setup.id = deductions.setup_id);

UPDATE deductions
SET reserve_account_receiver = ( SELECT deduction_setup.reserve_account_receiver
FROM deduction_setup
WHERE deduction_setup.id = deductions.setup_id);

UPDATE deductions
SET billing_cycle_id = ( SELECT deduction_setup.billing_cycle_id
FROM deduction_setup
WHERE deduction_setup.id = deductions.setup_id);

UPDATE deductions
SET eligible = ( SELECT deduction_setup.eligible
FROM deduction_setup
WHERE deduction_setup.id = deductions.setup_id);
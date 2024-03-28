ALTER TABLE payment_setup
    ADD COLUMN taxable tinyint(1) NOT NULL DEFAULT 1 AFTER quantity;

ALTER TABLE payments
    MODIFY taxable tinyint(1) NOT NULL DEFAULT 1;

ALTER TABLE payments_temp
    MODIFY taxable tinyint(1) NOT NULL DEFAULT 1;

DROP PROCEDURE IF EXISTS `createIndividualPaymentTemplates`;
CREATE PROCEDURE `createIndividualPaymentTemplates`(IN `template_id` INT)
BEGIN
    SET @id = `template_id`;
    INSERT INTO payment_setup (
        contractor_id,
        carrier_id,
        payment_code,
        carrier_payment_code,
        description,
        category,
        terms,
        department,
        gl_code,
        disbursement_code,
        recurring,
        level_id,
        billing_cycle_id,
        rate,
        first_start_day,
        second_start_day,
        quantity,
        deleted,
        week_offset,
        master_setup_id,
        changed,
        biweekly_start_day,
        taxable
    )
    SELECT
        c.entity_id AS contractor_id,
        s.carrier_id,
        s.payment_code,
        s.carrier_payment_code,
        s.description,
        s.category,
        s.terms,
        s.department,
        s.gl_code,
        s.disbursement_code,
        s.recurring,
        2           AS level_id,
        s.billing_cycle_id,
        s.rate,
        s.first_start_day,
        s.second_start_day,
        s.quantity,
        0           AS deleted,
        s.week_offset,
        s.id        AS master_setup_id,
        0           AS changed,
        s.biweekly_start_day,
        taxable
    FROM
        payment_setup s,
        contractor c
            LEFT JOIN
        entity e ON c.entity_id = e.id
    WHERE
        c.carrier_id = s.carrier_id
      AND c.carrier_status_id = 0
      AND e.deleted = 0
      AND s.id = @id
      AND c.entity_id NOT IN (
        SELECT contractor_id
        FROM payment_setup ps
        WHERE ps.master_setup_id = s.id AND ps.deleted = 0
    );
END;

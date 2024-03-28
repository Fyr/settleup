DELIMITER $$

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
      week_day,
      week_offset,
      master_setup_id,
      changed
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
        s.week_day,
        s.week_offset,
        s.id        AS master_setup_id,
        0           AS changed
      FROM
        payment_setup s,
        contractor c
        LEFT JOIN
        entity e ON c.entity_id = e.id
      WHERE
        c.carrier_id = s.carrier_id
        AND c.status = 1
        AND c.carrier_status_id = 0
        AND e.deleted = 0
        AND s.id = @id
        AND c.entity_id NOT IN (
          SELECT contractor_id
          FROM payment_setup ps
          WHERE ps.master_setup_id = s.id AND ps.deleted = 0
        );

  END;

DROP PROCEDURE IF EXISTS `createIndividualDeductionTemplates`;
CREATE PROCEDURE `createIndividualDeductionTemplates`(IN `template_id` INT)
  BEGIN
    SET @id = `template_id`;
    INSERT INTO deduction_setup (
      contractor_id,
      provider_id,
      vendor_deduction_code,
      description,
      category,
      department,
      gl_code,
      disbursement_code,
      priority,
      recurring,
      level_id,
      billing_cycle_id,
      terms,
      rate,
      eligible,
      reserve_account_receiver,
      first_start_day,
      second_start_day,
      deduction_code,
      quantity,
      deleted,
      week_day,
      week_offset,
      master_setup_id,
      changed
    )
      SELECT
        c.entity_id AS contractor_id,
        s.provider_id,
        s.vendor_deduction_code,
        s.description,
        s.category,
        s.department,
        s.gl_code,
        s.disbursement_code,
        s.priority,
        s.recurring,
        2           AS level_id,
        s.billing_cycle_id,
        s.terms,
        s.rate,
        s.eligible,
        s.reserve_account_receiver,
        s.first_start_day,
        s.second_start_day,
        s.deduction_code,
        s.quantity,
        0           AS deleted,
        s.week_day,
        s.week_offset,
        s.id        AS master_setup_id,
        0           AS changed
      FROM
        deduction_setup s,
        contractor c
        LEFT JOIN
        entity e ON c.entity_id = e.id
      WHERE s.id = @id
            AND e.deleted = 0
            AND c.entity_id NOT IN (
        SELECT contractor_id
        FROM deduction_setup ds
        WHERE ds.master_setup_id = s.id AND ds.deleted = 0
      )
            AND (
        (
          c.carrier_id = s.provider_id
          AND c.status = 1
          AND c.carrier_status_id = 0
        ) || (
          c.entity_id IN (
            SELECT cv.contractor_id
            FROM contractor_vendor cv
            WHERE cv.vendor_id = s.provider_id AND cv.status = 0
          )
        )
      );

  END;


DROP PROCEDURE IF EXISTS migrateMaterPaymentTemplates;
CREATE PROCEDURE migrateMaterPaymentTemplates()
  BEGIN
    DECLARE done BOOLEAN DEFAULT FALSE;
    DECLARE _id BIGINT UNSIGNED;
    DECLARE cur CURSOR FOR SELECT id
                           FROM payment_setup
                           WHERE level_id = 1 AND deleted = 0;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done := TRUE;

    OPEN cur;

    REPEAT
      FETCH cur
      INTO _id;
      CALL createIndividualPaymentTemplates(_id);
    UNTIL done END REPEAT;

    CLOSE cur;
  END;

DROP PROCEDURE IF EXISTS migrateMaterDeductionTemplates;
CREATE PROCEDURE migrateMaterDeductionTemplates()
  BEGIN
    DECLARE done BOOLEAN DEFAULT FALSE;
    DECLARE _id BIGINT UNSIGNED;
    DECLARE cur CURSOR FOR SELECT id
                           FROM deduction_setup
                           WHERE level_id = 1 AND deleted = 0;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done := TRUE;

    OPEN cur;

    REPEAT
      FETCH cur
      INTO _id;
      CALL createIndividualDeductionTemplates(_id);
    UNTIL done END REPEAT;

    CLOSE cur;
  END;


CALL migrateMaterPaymentTemplates();
CALL migrateMaterDeductionTemplates();

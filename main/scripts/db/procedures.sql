DELIMITER $$

DROP PROCEDURE IF EXISTS `getSettlementContractors`;

CREATE PROCEDURE `getSettlementContractors`(IN `cycle`  INT, IN `sort` VARCHAR(255), IN `direction` VARCHAR(4),
                                            IN `filter` VARCHAR(255), IN `contractor` INT, IN `limit` INT, IN `offset` INT)
  BEGIN
    SET @id = `cycle`;
    SET @filter = `filter`;

    IF `direction` = 'desc'
    THEN SET @sort = CONCAT(`sort`, ' desc');
    ELSE SET @sort = `sort`;
    END IF;

    IF `contractor` IS NOT NULL
    THEN SET @contractor = CONCAT(' AND c.entity_id=', `contractor`);
    ELSE SET @contractor = '';
    END IF;

    IF `offset` IS NOT NULL AND `limit` IS NOT NULL
    THEN SET @limitOffset = CONCAT(' LIMIT ',`offset`,',',`limit`);
    ELSE SET @limitOffset = '';
    END IF;

    SET @sql = CONCAT("
    SELECT m.* FROM(
    SELECT
      c.entity_id as id,
        c.company_name as company,
        c.code as code,
        sg.code as settlement_group,
        c.carrier_id as division,
        e.name as division_name,
        p.payments,
        pn.payments_num as payments_num,
        d.deductions_amount,
        d.deductions_balance,
        d.deductions_adjusted_balance,
        tc.contribution,
        tw.withdrawal,
        dt.disbursements,
        IFNULL(payments, 0) - IFNULL(deductions_amount, 0) + IFNULL(deductions_balance, 0) - IFNULL(contribution, 0) + IFNULL(withdrawal, 0) as settlement
    FROM contractor c
        LEFT JOIN (SELECT contractor_id, COUNT(amount) as payments_num FROM payments WHERE settlement_cycle_id = @id AND deleted = 0 GROUP BY contractor_id) as pn ON c.entity_id = pn.contractor_id
        LEFT JOIN (SELECT contractor_id, SUM(amount) as payments, settlement_cycle_id FROM payments WHERE settlement_cycle_id = @id AND deleted = 0 GROUP BY contractor_id) as p ON c.entity_id = p.contractor_id
        LEFT JOIN entity as e ON c.carrier_id = e.id
        LEFT JOIN (SELECT contractor_id, SUM(amount) as deductions_amount, SUM(IF(adjusted_balance IS NULL, balance, adjusted_balance)) as deductions_balance, SUM(adjusted_balance) as deductions_adjusted_balance, settlement_cycle_id FROM deductions WHERE settlement_cycle_id = @id AND deleted = 0 GROUP BY contractor_id) as d ON c.entity_id = d.contractor_id
        LEFT JOIN (SELECT contractor_id, SUM(amount) as contribution, settlement_cycle_id FROM reserve_transaction WHERE settlement_cycle_id = @id AND deleted = 0 AND type = 1 GROUP BY contractor_id) as tc ON c.entity_id = tc.contractor_id
        LEFT JOIN (SELECT contractor_id, SUM(amount) as withdrawal, settlement_cycle_id FROM reserve_transaction WHERE settlement_cycle_id = @id AND deleted = 0 AND type = 2 GROUP BY contractor_id) as tw ON c.entity_id = tw.contractor_id
        LEFT JOIN (SELECT entity_id, SUM(amount) as disbursements, settlement_cycle_id FROM disbursement_transaction WHERE settlement_cycle_id = @id GROUP BY entity_id) as dt ON c.entity_id = dt.entity_id
        LEFT JOIN settlement_group as sg ON c.settlement_group_id = sg.id
      WHERE (
        p.settlement_cycle_id = @id
OR      d.settlement_cycle_id = @id
OR      tc.settlement_cycle_id = @id
OR      tw.settlement_cycle_id = @id
      )", @contractor, " ORDER BY ", @sort, ") as m ", @filter, @limitOffset
    );

    PREPARE STMT FROM @sql;

    EXECUTE STMT;
  END;

  DROP PROCEDURE IF EXISTS `getSettlementContractorsCount`;

CREATE PROCEDURE `getSettlementContractorsCount`(IN `cycle`  INT, IN `sort` VARCHAR(255), IN `direction` VARCHAR(4),
                                            IN `filter` VARCHAR(255), IN `contractor` INT)
  BEGIN
    SET @id = `cycle`;
    SET @filter = `filter`;

    IF `direction` = 'desc'
    THEN SET @sort = CONCAT(`sort`, ' desc');
    ELSE SET @sort = `sort`;
    END IF;

    IF `contractor` IS NOT NULL
    THEN SET @contractor = CONCAT(' AND c.entity_id=', `contractor`);
    ELSE SET @contractor = '';
    END IF;

    SET @sql = CONCAT("
    SELECT COUNT(1) as cnt FROM(
    SELECT
      c.entity_id as id,
        c.company_name as company,
        c.code as code,
        c.carrier_id as division,
        sg.code as settlement_group,
        e.name as division_name,
        p.payments,
        pn.payments_num as payments_num,
        d.deductions_amount,
        d.deductions_balance,
        d.deductions_adjusted_balance,
        tc.contribution,
        tw.withdrawal,
        dt.disbursements,
        IFNULL(payments, 0) - IFNULL(deductions_amount, 0) + IFNULL(deductions_balance, 0) - IFNULL(contribution, 0) + IFNULL(withdrawal, 0) as settlement
    FROM contractor c
        LEFT JOIN (SELECT contractor_id, SUM(amount) as payments, settlement_cycle_id FROM payments WHERE settlement_cycle_id = @id AND deleted = 0 GROUP BY contractor_id) as p ON c.entity_id = p.contractor_id
        LEFT JOIN (SELECT contractor_id, COUNT(amount) as payments_num FROM payments WHERE settlement_cycle_id = @id AND deleted = 0 GROUP BY contractor_id) as pn ON c.entity_id = pn.contractor_id
        LEFT JOIN entity as e ON c.carrier_id = e.id
        LEFT JOIN (SELECT contractor_id, SUM(amount) as deductions_amount, SUM(IF(adjusted_balance IS NULL, balance, adjusted_balance)) as deductions_balance, SUM(adjusted_balance) as deductions_adjusted_balance, settlement_cycle_id FROM deductions WHERE settlement_cycle_id = @id AND deleted = 0 GROUP BY contractor_id) as d ON c.entity_id = d.contractor_id
        LEFT JOIN (SELECT contractor_id, SUM(amount) as contribution, settlement_cycle_id FROM reserve_transaction WHERE settlement_cycle_id = @id AND deleted = 0 AND type = 1 GROUP BY contractor_id) as tc ON c.entity_id = tc.contractor_id
        LEFT JOIN (SELECT contractor_id, SUM(amount) as withdrawal, settlement_cycle_id FROM reserve_transaction WHERE settlement_cycle_id = @id AND deleted = 0 AND type = 2 GROUP BY contractor_id) as tw ON c.entity_id = tw.contractor_id
        LEFT JOIN (SELECT entity_id, SUM(amount) as disbursements, settlement_cycle_id FROM disbursement_transaction WHERE settlement_cycle_id = @id GROUP BY entity_id) as dt ON c.entity_id = dt.entity_id
        LEFT JOIN settlement_group as sg ON c.settlement_group_id = sg.id
      WHERE (
        p.settlement_cycle_id = @id
OR      d.settlement_cycle_id = @id
OR      tc.settlement_cycle_id = @id
OR      tw.settlement_cycle_id = @id
      )", @contractor, " ORDER BY ", @sort, ") as m ", @filter
    );

    PREPARE STMT FROM @sql;

    EXECUTE STMT;
  END;

DROP PROCEDURE IF EXISTS `getSettlementContractorsTotal`;

CREATE PROCEDURE `getSettlementContractorsTotal`(IN `cycle` INT, IN `filter` VARCHAR(255), IN `limit` INT, IN `offset` INT)
  BEGIN
    SET @id = `cycle`;
    SET @filter = `filter`;

    IF `offset` IS NOT NULL AND `limit` IS NOT NULL
    THEN SET @limitOffset = CONCAT(' LIMIT ',`offset`,',',`limit`);
    ELSE SET @limitOffset = '';
    END IF;

    SET @sql = CONCAT("
      SELECT
       SUM(m.payments) as payments,
       SUM(m.deductions_amount) as deductions_amount,
       SUM(m.deductions_balance) as deductions_balance,
       SUM(m.withdrawal) as withdrawal,
       SUM(m.contribution) as contribution,
       SUM(m.settlement) as settlement
       FROM(
      SELECT
      c.entity_id as id,
        c.company_name as company,
        c.code as code,
        c.carrier_id as division,
        sg.code as settlement_group,
        e.name as division_name,
        p.payments,
        pn.payments_num as payments_num,
        d.deductions_amount,
        d.deductions_balance,
        d.deductions_adjusted_balance,
        tc.contribution,
        tw.withdrawal,
        dt.disbursement,
        IFNULL(payments, 0) - IFNULL(deductions_amount, 0) + IFNULL(deductions_balance, 0) - IFNULL(contribution, 0) + IFNULL(withdrawal, 0) as settlement
    FROM contractor c
        LEFT JOIN (SELECT contractor_id, SUM(amount) as payments, settlement_cycle_id FROM payments WHERE settlement_cycle_id = @id AND deleted = 0 GROUP BY contractor_id) as p ON c.entity_id = p.contractor_id
        LEFT JOIN (SELECT contractor_id, COUNT(amount) as payments_num FROM payments WHERE settlement_cycle_id = @id AND deleted = 0 GROUP BY contractor_id) as pn ON c.entity_id = pn.contractor_id
        LEFT JOIN entity as e ON c.carrier_id = e.id
        LEFT JOIN (SELECT contractor_id, SUM(amount) as deductions_amount, SUM(IF(adjusted_balance IS NULL, balance, adjusted_balance)) as deductions_balance, SUM(adjusted_balance) as deductions_adjusted_balance, settlement_cycle_id FROM deductions WHERE settlement_cycle_id = @id AND deleted = 0 GROUP BY contractor_id) as d ON c.entity_id = d.contractor_id
        LEFT JOIN (SELECT contractor_id, SUM(amount) as contribution, settlement_cycle_id FROM reserve_transaction WHERE settlement_cycle_id = @id AND deleted = 0 AND type = 1 GROUP BY contractor_id) as tc ON c.entity_id = tc.contractor_id
        LEFT JOIN (SELECT contractor_id, SUM(amount) as withdrawal, settlement_cycle_id FROM reserve_transaction WHERE settlement_cycle_id = @id AND deleted = 0 AND type = 2 GROUP BY contractor_id) as tw ON c.entity_id = tw.contractor_id
        LEFT JOIN (SELECT entity_id, SUM(amount) as disbursement, settlement_cycle_id FROM disbursement_transaction WHERE settlement_cycle_id = @id GROUP BY entity_id) as dt ON c.entity_id = dt.entity_id
        LEFT JOIN settlement_group as sg ON c.settlement_group_id = sg.id
      WHERE (
        p.settlement_cycle_id = @id
OR      d.settlement_cycle_id = @id
OR      tc.settlement_cycle_id = @id
OR      tw.settlement_cycle_id = @id
      )", @limitOffset, ") as m ", @filter
    );
    PREPARE STMT FROM @sql;
    EXECUTE STMT;
  END;

DROP procedure IF EXISTS `getSettlementPowerunitTotalsByPeriod`;

CREATE PROCEDURE `getSettlementPowerunitTotalsByPeriod`(IN from_date VARCHAR(10), IN until_date VARCHAR(10), IN cycle_id INT)
BEGIN
	SET @min_date = from_date;
    SET @max_date = until_date;
    SET @cycle_id = cycle_id;

    SET @sql = CONCAT("
    SELECT
        pw.id AS powerunit_id,
        pw.code AS powerunit_code,
        pw.contractor_id,
        IFNULL((
            SELECT SUM(p.amount)
            FROM payments AS p
            WHERE p.powerunit_id = pw.id AND p.deleted = 0 AND @min_date <= p.created_datetime AND p.created_datetime <= @max_date
        ), 0) AS payments,
        IFNULL((
            SELECT SUM(d.amount)
            FROM deductions AS d
            WHERE d.powerunit_id = pw.id AND d.deleted = 0 AND @min_date <= d.created_datetime AND d.created_datetime <= @max_date
        ), 0) AS deductions,
        IFNULL((
            SELECT SUM(rt.amount)
            FROM reserve_transaction AS rt
            WHERE rt.contractor_id = pw.contractor_id AND rt.deleted = 0 AND rt.type = 1 AND @min_date <= rt.created_datetime AND rt.created_datetime <= @max_date
        ), 0) AS contribution,
        IFNULL((
            SELECT SUM(rt.amount)
            FROM reserve_transaction AS rt
            WHERE rt.contractor_id = pw.contractor_id AND rt.deleted = 0 AND rt.type = 2 AND @min_date <= rt.created_datetime AND rt.created_datetime <= @max_date
        ), 0) AS withdrawal
    FROM powerunit AS pw
    WHERE pw.contractor_id IN (
        SELECT c.entity_id
        FROM settlement_cycle AS sc
        LEFT JOIN contractor AS c ON c.settlement_group_id = sc.settlement_group_id
        WHERE sc.id = @cycle_id
    )
    HAVING payments > 0 OR deductions > 0 OR contribution > 0 OR withdrawal > 0;");
    PREPARE STMT FROM @sql;
    EXECUTE STMT;
END;

DROP PROCEDURE IF EXISTS `updateReserveAccountContractorCurrentBalance`;
CREATE PROCEDURE `updateReserveAccountContractorCurrentBalance`(IN `rac` INT, IN `cycle` INT)
  BEGIN
    SET @id = `cycle`;
    SET @rac = `rac`;


    INSERT INTO reserve_account_history (settlement_cycle_id, reserve_account_id, current_balance)
    VALUES (
      @id,
      @rac,
      IFNULL(starting_balance, 0)
      + (SELECT IFNULL(SUM(amount), 0)
         FROM reserve_transaction
         WHERE type = 1 AND settlement_cycle_id = @id AND reserve_account_contractor = @rac AND deleted = 0)
      - (SELECT IFNULL(SUM(amount), 0)
         FROM reserve_transaction
         WHERE type = 2 AND settlement_cycle_id = @id AND reserve_account_contractor = @rac AND deleted = 0)
    )
    ON DUPLICATE KEY UPDATE
      current_balance = (
        IFNULL(starting_balance, 0)
        + (SELECT IFNULL(SUM(amount), 0)
           FROM reserve_transaction
           WHERE type = 1 AND settlement_cycle_id = @id AND reserve_account_contractor = @rac AND deleted = 0)
        - (SELECT IFNULL(SUM(amount), 0)
           FROM reserve_transaction
           WHERE type = 2 AND settlement_cycle_id = @id AND reserve_account_contractor = @rac AND deleted = 0)
      );

    UPDATE reserve_account
    SET current_balance = (
      SELECT current_balance
      FROM reserve_account_history h
      WHERE h.reserve_account_id = @rac AND h.settlement_cycle_id = @id
    )
    WHERE id = @rac;

    SET @rav = getVendorAccount(@rac);
    CALL updateReserveAccountVendorCurrentBalance(@rav);
  END;

DROP PROCEDURE IF EXISTS `updateReserveAccountVendorCurrentBalance`;
CREATE PROCEDURE `updateReserveAccountVendorCurrentBalance`(IN `rav` INT)
  BEGIN
    SET @rav = `rav`;
    UPDATE reserve_account AS vra
    SET vra.current_balance = (
      SELECT balance
      FROM (
             SELECT IFNULL(SUM(ra.current_balance), 0) AS balance
             FROM reserve_account ra
               LEFT JOIN reserve_account_contractor rac ON ra.id = rac.reserve_account_id
               LEFT JOIN reserve_account_vendor rav ON rac.reserve_account_vendor_id = rav.id
             WHERE rav.reserve_account_id = @rav AND ra.deleted = 0
           ) AS sub_query)
    WHERE vra.id = @rav AND vra.deleted = 0;
  END;

DROP FUNCTION IF EXISTS `getVendorAccount`;
CREATE FUNCTION `getVendorAccount`(rac INT)
  RETURNS INT(11)
  BEGIN
    DECLARE rav INT(11);
    SELECT reserve_account_id
    INTO rav
    FROM reserve_account_vendor
    WHERE id = (
      SELECT reserve_account_vendor_id
      FROM reserve_account_contractor
      WHERE reserve_account_id = rac
    );
    RETURN (rav);
  END;

DROP PROCEDURE IF EXISTS `updateReserveAccountContractorStartingBalance`;
CREATE PROCEDURE `updateReserveAccountContractorStartingBalance`(IN `rac` INT, IN `cycle` INT)
  BEGIN
    SET @id = `cycle`;
    SET @rac = `rac`;

    INSERT INTO reserve_account_history (settlement_cycle_id, reserve_account_id, starting_balance)
    VALUES (
      @id,
      @rac,
      IFNULL(verify_balance, 0)
      + (SELECT IFNULL(SUM(amount), 0)
         FROM reserve_transaction
         WHERE type = 5 AND settlement_cycle_id = @id AND reserve_account_contractor = @rac AND deleted = 0)
      - (SELECT IFNULL(SUM(amount), 0)
         FROM reserve_transaction
         WHERE type = 4 AND settlement_cycle_id = @id AND reserve_account_contractor = @rac AND deleted = 0)
    )
    ON DUPLICATE KEY UPDATE
      starting_balance = (
        IFNULL(verify_balance, 0)
        + (SELECT IFNULL(SUM(amount), 0)
           FROM reserve_transaction
           WHERE type = 5 AND settlement_cycle_id = @id AND reserve_account_contractor = @rac AND deleted = 0)
        - (SELECT IFNULL(SUM(amount), 0)
           FROM reserve_transaction
           WHERE type = 4 AND settlement_cycle_id = @id AND reserve_account_contractor = @rac AND deleted = 0)
      );

    UPDATE reserve_account
    SET starting_balance = (
      SELECT starting_balance
      FROM reserve_account_history h
      WHERE h.reserve_account_id = @rac AND h.settlement_cycle_id = @id
    )
    WHERE id = @rac;
  END;

DROP PROCEDURE IF EXISTS `getSettlementVendors`;
CREATE PROCEDURE `getSettlementVendors`(IN `cycle` INT)

  BEGIN
    SET @cycle = `cycle`;

    SELECT e.id AS id, e.name AS name,
           (SELECT IFNULL( SUM( amount), 0) - IFNULL( SUM( IF(d.adjusted_balance IS NULL,  d.balance, d.adjusted_balance)), 0)
            FROM deductions d
            WHERE d.settlement_cycle_id = @cycle
                  AND d.deleted = 0
                  AND d.provider_id = e.id) +
           (SELECT IFNULL( SUM( t.amount), 0)
            FROM reserve_transaction t
              LEFT JOIN reserve_account ra ON t.reserve_account_vendor = ra.id
            WHERE t.settlement_cycle_id = @cycle
                  AND t.deleted = 0
                  AND ra.entity_id = e.id
                  AND t.type = 1) -
           (SELECT IFNULL( SUM( t.amount), 0)
            FROM reserve_transaction t
              LEFT JOIN reserve_account ra ON t.reserve_account_vendor = ra.id
            WHERE t.settlement_cycle_id = @cycle
                  AND t.deleted = 0
                  AND ra.entity_id = e.id
                  AND t.type = 2) AS amount,

      (SELECT IFNULL( SUM( dt.amount), 0)
       FROM disbursement_transaction dt
       WHERE dt.settlement_cycle_id = @cycle
             AND dt.entity_id = e.id) AS disbursements
    FROM entity e
    WHERE e.id IN
          ( SELECT d.provider_id
            FROM deductions d
            WHERE d.settlement_cycle_id = @cycle
                  AND d.deleted = 0 )
          OR e.id IN
             ( SELECT ra.entity_id
               FROM reserve_transaction t
                 LEFT JOIN reserve_account ra ON t.reserve_account_vendor = ra.id
               WHERE t.settlement_cycle_id = @cycle
                     AND t.deleted = 0 )
             AND e.deleted = 0;
  END;

DROP PROCEDURE IF EXISTS `updateReserveAccountVendorInitialBalance`;
CREATE PROCEDURE `updateReserveAccountVendorInitialBalance`(IN `id` INT)
  BEGIN
    SET @id = `id`;
    UPDATE reserve_account ra
    SET ra.initial_balance = (
      SELECT ib
      FROM (
             SELECT IFNULL(SUM(ra2.initial_balance), 0) AS ib
             FROM reserve_account ra2
               LEFT JOIN reserve_account_contractor rac ON ra2.id = rac.reserve_account_id
             WHERE rac.reserve_account_vendor_id = (
               SELECT rac2.reserve_account_vendor_id
               FROM reserve_account_contractor rac2
               WHERE rac2.reserve_account_id = @id
             ) AND ra2.deleted = 0) AS tmp
    ), ra.current_balance  = (
      SELECT cb
      FROM (
             SELECT IFNULL(SUM(ra3.current_balance), 0) AS cb
             FROM reserve_account ra3
               LEFT JOIN reserve_account_contractor rac ON ra3.id = rac.reserve_account_id
             WHERE rac.reserve_account_vendor_id = (
               SELECT rac3.reserve_account_vendor_id
               FROM reserve_account_contractor rac3
               WHERE rac3.reserve_account_id = @id
             ) AND ra3.deleted = 0) AS tmp2
    )
    WHERE ra.id = (
      SELECT rav.reserve_account_id
      FROM reserve_account_contractor rac3
        LEFT JOIN reserve_account_vendor rav ON rac3.reserve_account_vendor_id = rav.id
      WHERE rac3.reserve_account_id = @id
    );

  END;

DROP PROCEDURE IF EXISTS `updateRACVerify`;
CREATE PROCEDURE `updateRACVerify`(IN `cycle` INT)
  BEGIN
    SET @cycle = `cycle`;
    UPDATE reserve_account ra
    SET ra.verify_balance = IFNULL(ra.current_balance, 0), ra.starting_balance = IFNULL(ra.current_balance, 0)
    WHERE ra.entity_id IN (
      SELECT c.entity_id
      FROM contractor c
      WHERE c.carrier_id = (
        SELECT ss.carrier_id
        FROM settlement_cycle ss
        WHERE ss.id = @cycle
      )
    );

    INSERT INTO reserve_account_history (settlement_cycle_id, reserve_account_id, verify_balance, starting_balance, current_balance)
      SELECT
        @cycle AS settlement_cycle_id,
        ra.id  AS reserve_account_id,
        IFNULL(ra.current_balance, 0),
        IFNULL(ra.current_balance, 0),
        IFNULL(ra.current_balance, 0)
      FROM reserve_account ra
      WHERE ra.entity_id IN (
        SELECT c.entity_id
        FROM contractor c
        WHERE c.carrier_id = (
          SELECT ss.carrier_id
          FROM settlement_cycle ss
          WHERE ss.id = @cycle
        ) AND ra.deleted = 0
      )
    ON DUPLICATE KEY UPDATE
      verify_balance = IFNULL(ra.current_balance, 0),
      starting_balance = IFNULL(ra.current_balance, 0),
      current_balance = IFNULL(ra.current_balance, 0);
  END;

DROP PROCEDURE IF EXISTS `updateRACClear`;
CREATE PROCEDURE `updateRACClear`(IN `cycle` INT)
  BEGIN
    SET @cycle = `cycle`;
    UPDATE reserve_account ra
    SET ra.starting_balance = IFNULL(ra.verify_balance, 0),
      ra.current_balance = IFNULL(ra.verify_balance, 0)
    WHERE ra.entity_id IN (
      SELECT c.entity_id
      FROM contractor c
      WHERE c.carrier_id = (
        SELECT ss.carrier_id
        FROM settlement_cycle ss
        WHERE ss.id = @cycle
      )
    );

    UPDATE reserve_account_history rah
    SET rah.starting_balance = rah.verify_balance,
      rah.current_balance = rah.verify_balance
    WHERE rah.settlement_cycle_id = @cycle;

  END;

DROP PROCEDURE IF EXISTS `updateRACProcess`;
CREATE PROCEDURE `updateRACProcess`(IN `cycle` INT)
  BEGIN
    SET @cycle = `cycle`;
    UPDATE reserve_account ra
    SET ra.current_balance = ra.starting_balance
    WHERE ra.entity_id IN (
      SELECT c.entity_id
      FROM contractor c
      WHERE c.carrier_id = (
        SELECT ss.carrier_id
        FROM settlement_cycle ss
        WHERE ss.id = @cycle
      )
    );

    UPDATE reserve_account_history rah
    SET rah.current_balance = rah.starting_balance
    WHERE rah.settlement_cycle_id = @cycle;
  END;

DROP PROCEDURE IF EXISTS `getContractorAccountBalances`;
CREATE PROCEDURE `getContractorAccountBalances`(IN `cycle` INT, IN `contractor` INT)
  BEGIN
    SET @id = `cycle`;
    SET @contractor = `contractor`;

    SELECT
      h.starting_balance                                                                            AS starting_balance,
      h.current_balance                                                                             AS ending_balance,
      rac.vendor_reserve_code,
      ce.name                                                                                       AS contractor_name,
      vra.entity_id                                                                                 AS vendor_id,
      ve.name                                                                                       AS vendor_name,
      ra.account_name,
      ra.description as account_description,
      (SELECT IFNULL(SUM(amount), 0)
       FROM reserve_transaction w
       WHERE type = 2 AND w.reserve_account_contractor = ra.id AND settlement_cycle_id = @id AND w.deleted = 0
                                                                                                  ) AS withdrawals,
      (SELECT IFNULL(SUM(amount), 0)
      FROM reserve_transaction ai
      WHERE type = 5 AND ai.reserve_account_contractor = ra.id AND settlement_cycle_id = @id AND ai.deleted = 0
                                                                                                  ) AS a_increase,
      0 as adjustments,

      (SELECT IFNULL(SUM(amount), 0)
      FROM reserve_transaction ad
      WHERE type = 4 AND ad.reserve_account_contractor = ra.id AND settlement_cycle_id = @id AND ad.deleted = 0
                                                                                                  ) AS a_decrease,

      (SELECT IFNULL(SUM(amount), 0)
       FROM reserve_transaction c
       WHERE type = 1 AND c.reserve_account_contractor = ra.id AND settlement_cycle_id = @id AND c.deleted =
                                                                                                 0) AS contributions
    FROM reserve_account_history h
      LEFT JOIN reserve_account ra ON h.reserve_account_id = ra.id
      LEFT JOIN reserve_account_contractor rac ON rac.reserve_account_id = ra.id
      LEFT JOIN entity ce ON ra.entity_id = ce.id
      LEFT JOIN reserve_account_vendor rav ON rac.reserve_account_vendor_id = rav.id
      LEFT JOIN reserve_account vra ON rav.reserve_account_id = vra.id
      LEFT JOIN entity ve ON vra.entity_id = ve.id
    WHERE (settlement_cycle_id = @id) AND (ra.entity_id = @contractor) AND (ra.deleted = '0')
    ORDER BY `vendor_name` ASC;
  END;

DROP PROCEDURE IF EXISTS `getCarrierAccountBalances`;
CREATE PROCEDURE `getCarrierAccountBalances`(IN `cycle` INT)
  BEGIN
    SET @id = `cycle`;

    SELECT
      ve.name                 AS vendor_name,
      rav.vendor_reserve_code,
      vra.account_name,
      vra.description,
      SUM(h.starting_balance) AS starting_balance,
      SUM(h.current_balance)  AS ending_balance,
      (SELECT IFNULL(SUM(amount), 0)
       FROM reserve_transaction w
       WHERE type = 2 AND w.reserve_account_vendor = rav.reserve_account_id AND settlement_cycle_id = @id AND
             w.deleted = 0)   AS withdrawals,
      (SELECT IFNULL(SUM(amount), 0)
       FROM reserve_transaction c
       WHERE type = 1 AND c.reserve_account_vendor = rav.reserve_account_id AND settlement_cycle_id = @id AND
             c.deleted = 0)   AS contributions
    FROM reserve_account_history h
      LEFT JOIN reserve_account ra ON h.reserve_account_id = ra.id
      LEFT JOIN reserve_account_contractor rac ON rac.reserve_account_id = ra.id
      LEFT JOIN reserve_account_vendor rav ON rac.reserve_account_vendor_id = rav.id
      LEFT JOIN reserve_account vra ON rav.reserve_account_id = vra.id
      LEFT JOIN entity ve ON vra.entity_id = ve.id
    WHERE settlement_cycle_id = @id
    GROUP BY rac.reserve_account_vendor_id
    ORDER BY `vendor_name` ASC;
  END;

DROP PROCEDURE IF EXISTS `getAccountsBalances`;
CREATE PROCEDURE `getAccountsBalances`(IN `cycle_ids` VARCHAR(255), IN `account_ids` VARCHAR(255))
  BEGIN
    SET @ids = `cycle_ids`;
    SET @racIds = `account_ids`;

    SET @sql = CONCAT("
      SELECT
        h.starting_balance as starting_balance,
        h.current_balance as ending_balance,
        h.settlement_cycle_id,
        sc.cycle_start_date,
        sc.cycle_close_date,
        rac.vendor_reserve_code,
        c.company_name as contractor_name,
        c.division as division,
        c.code as contractor_code,
        vra.entity_id as vendor_id,
        ve.name as vendor_name,
        ra.account_name,
        cv.vendor_acct,
        (SELECT IFNULL(SUM(amount), 0) FROM reserve_transaction w  WHERE type = 2 AND w.reserve_account_contractor = ra.id AND w.settlement_cycle_id = h.settlement_cycle_id AND w.reserve_account_contractor = h.reserve_account_id AND w.deleted = 0) as withdrawals,
        (SELECT IFNULL(SUM(amount), 0) FROM reserve_transaction c  WHERE type = 1 AND c.reserve_account_contractor = ra.id AND c.settlement_cycle_id = h.settlement_cycle_id AND c.reserve_account_contractor = h.reserve_account_id AND c.deleted = 0) as contributions
      FROM reserve_account_history h
        LEFT JOIN settlement_cycle sc ON h.settlement_cycle_id = sc.id
        LEFT JOIN reserve_account ra ON h.reserve_account_id = ra.id
        LEFT JOIN reserve_account_contractor rac on rac.reserve_account_id = ra.id
        LEFT JOIN contractor c on ra.entity_id = c.entity_id
        LEFT JOIN reserve_account_vendor rav on rac.reserve_account_vendor_id = rav.id
        LEFT JOIN reserve_account vra on rav.reserve_account_id = vra.id
        LEFT JOIN entity ve on vra.entity_id = ve.id
        LEFT JOIN contractor_vendor cv on cv.vendor_id = vra.entity_id and cv.contractor_id = ra.entity_id
      WHERE (h.settlement_cycle_id IN (", @ids, ")) AND (h.reserve_account_id IN (", @racIds, ")) AND (ra.deleted = '0')
      ORDER BY h.settlement_cycle_id ASC;
    ");

    PREPARE STMT FROM @sql;
    EXECUTE STMT;
  END;

DROP PROCEDURE IF EXISTS `updateRAC`;
CREATE PROCEDURE `updateRAC`(IN `min_balance`       DECIMAL(10, 2), `vendor_account_id` INT)
  BEGIN
    SET @minBalance = `min_balance`;
    SET @vendorAccountId = `vendor_account_id`;

    UPDATE reserve_account ra_c
      LEFT JOIN reserve_account_contractor rac ON ra_c.id = rac.reserve_account_id
    SET ra_c.min_balance         = @minBalance
    WHERE ra_c.deleted = 0 AND rac.reserve_account_vendor_id = @vendorAccountId;
  END;

DROP PROCEDURE IF EXISTS `getPaymentsAndDisbursements`;
CREATE PROCEDURE `getPaymentsAndDisbursements`(IN `cycle` INT)
  BEGIN
    SET @cycle = `cycle`;

    SELECT
      (SELECT IFNULL(SUM(p.amount), 0)
       FROM payments p
       WHERE p.settlement_cycle_id = @cycle AND p.deleted = 0) AS payment_amount,
      (SELECT IFNULL(SUM(dt.amount), 0)
       FROM disbursement_transaction dt
       WHERE dt.settlement_cycle_id = @cycle)                  AS disbursement_amount;
  END;

DROP PROCEDURE IF EXISTS `getVendorsWithNegativeDisbursements`;
CREATE PROCEDURE `getVendorsWithNegativeDisbursements`(IN `cycle` INT)
  BEGIN
    SET @cycle = `cycle`;

    SELECT
      dt.amount AS amount,
      e.name    AS name
    FROM disbursement_transaction dt
      LEFT JOIN entity e ON dt.entity_id = e.id
    WHERE dt.settlement_cycle_id = @cycle AND e.entity_type_id IN (1, 3) AND dt.amount < 0;
  END;

DROP PROCEDURE IF EXISTS `removeContractorRelatedData`;
CREATE PROCEDURE `removeContractorRelatedData`(IN `entityId` INT)
  BEGIN
    SET @id = `entityId`;

    DELETE FROM `contractor_vendor`
    WHERE contractor_id = @id;

    UPDATE `payment_setup` ps
    SET `deleted` = 1
    WHERE ps.contractor_id = @id;

    UPDATE `deduction_setup` ds
    SET `deleted` = 1
    WHERE ds.contractor_id = @id;

  END;

DROP PROCEDURE IF EXISTS `removeVendorRelatedData`;
CREATE PROCEDURE `removeVendorRelatedData`(IN `entityId` INT)
  BEGIN
    SET @id = `entityId`;

    DELETE FROM `contractor_vendor`
    WHERE vendor_id = @id;

    UPDATE `reserve_account` ra
    SET `deleted` = 1
    WHERE ra.entity_id = @id;

    UPDATE `reserve_account` ra_c
      LEFT JOIN `reserve_account_contractor` rac ON rac.reserve_account_id = ra_c.id
    SET ra_c.deleted = 1
    WHERE rac.reserve_account_vendor_id IN (
      SELECT rav_temp.rav_id
      FROM (
             SELECT rav.id AS rav_id
             FROM `reserve_account_vendor` rav
               LEFT JOIN `reserve_account` ra_v ON ra_v.id = rav.reserve_account_id
             WHERE ra_v.entity_id = @id
           ) AS rav_temp
    );
  END;

DROP PROCEDURE IF EXISTS `getCountOfInvalidImports`;
CREATE PROCEDURE `getCountOfInvalidImports`(IN `source` INT)
  BEGIN
    SET @sourceId = `source`;

    SELECT (SELECT COUNT(1)
            FROM `contractor_temp`
            WHERE `source_id` = @sourceId AND status_id = 2) +
           (SELECT COUNT(1)
            FROM `contractor_vendor_temp`
            WHERE `source_id` = @sourceId AND status_id = 2) +
           (SELECT COUNT(1)
            FROM `entity_contact_info_temp`
            WHERE `source_id` = @sourceId AND status_id = 2) +
           (SELECT COUNT(1)
            FROM `deductions_temp`
            WHERE `source_id` = @sourceId AND status_id = 2) +
           (SELECT COUNT(1)
            FROM `payments_temp`
            WHERE `source_id` = @sourceId AND status_id = 2);
  END;

DROP PROCEDURE IF EXISTS `getRAContractorCount`;
CREATE PROCEDURE `getRAContractorCount`(IN `ravId` INT)
  BEGIN
    SET @ravId = `ravId`;

    SELECT
      (SELECT IFNULL(COUNT(1), 0)
       FROM `reserve_account_contractor` rac
         LEFT JOIN `reserve_account` ra_c ON rac.reserve_account_id = ra_c.id
       WHERE rac.reserve_account_vendor_id = @ravId AND ra_c.deleted = 0
      )   AS rac_count,
      (SELECT IFNULL(COUNT(1), 0)
       FROM `deduction_setup` ds
       WHERE ds.deleted = 0 AND ds.eligible = 1 AND ds.reserve_account_receiver = (
         SELECT rav.reserve_account_id
         FROM `reserve_account_vendor` rav
         WHERE rav.id = @ravId
       )) AS deduction_setup_count;
  END;

DROP PROCEDURE IF EXISTS `getTransactionAndDeductionTemplateCount`;
CREATE PROCEDURE `getTransactionAndDeductionTemplateCount`(IN `raId` INT)
  BEGIN
    SET @raId = `raId`;

    SELECT IFNULL(COUNT(1), 0) AS transaction_count
    FROM `reserve_transaction` rt
    WHERE rt.reserve_account_contractor = @raId AND rt.deleted = 0;
  END;

DROP PROCEDURE IF EXISTS `updateContractorStatus`;
CREATE PROCEDURE `updateContractorStatus`()
  BEGIN
    UPDATE contractor c
      LEFT JOIN entity e ON c.entity_id = e.id
    SET c.status = 3
    WHERE e.deleted = 0 AND c.status = 1 AND c.termination_date = CURDATE();

    UPDATE contractor c
      LEFT JOIN entity e ON c.entity_id = e.id
    SET c.status = 1
    WHERE e.deleted = 0 AND c.status = 3 AND c.rehire_date = CURDATE();
  END;


DROP PROCEDURE IF EXISTS `createIndividualPaymentTemplates`;
CREATE PROCEDURE `createIndividualPaymentTemplates`(IN `template_id` INT)
  BEGIN
    SET @id = `template_id`;
    INSERT INTO payment_setup (
      powerunit_id,
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
        p.id as powerunit_id,
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
        powerunit p
        LEFT JOIN contractor c ON c.entity_id = p.contractor_id
        LEFT JOIN entity e ON c.entity_id = e.id
      WHERE
        c.carrier_id = s.carrier_id
        AND e.deleted = 0
        AND p.deleted = 0
        AND s.id = @id
        AND p.id NOT IN (
          SELECT powerunit_id
          FROM payment_setup ps
          WHERE ps.master_setup_id = s.id AND ps.deleted = 0
        );
  END;


DROP PROCEDURE IF EXISTS `createIndividualDeductionTemplates`;
CREATE PROCEDURE `createIndividualDeductionTemplates`(IN `template_id` INT)
  BEGIN
    SET @id = `template_id`;
    INSERT INTO deduction_setup (
      powerunit_id,
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
      week_offset,
      master_setup_id,
      changed,
      biweekly_start_day
    )
      SELECT
        p.id as powerunit_id,
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
        s.week_offset,
        s.id        AS master_setup_id,
        0           AS changed,
        s.biweekly_start_day
      FROM
        deduction_setup s,
        powerunit p
          LEFT JOIN contractor c ON c.entity_id = p.contractor_id
          LEFT JOIN entity e ON c.entity_id = e.id
      WHERE s.id = @id
            AND e.deleted = 0
            AND p.deleted = 0
            AND p.id NOT IN (
        SELECT powerunit_id
        FROM deduction_setup ds
        WHERE ds.master_setup_id = s.id AND ds.deleted = 0
      )
            AND (
        (
          c.carrier_id = s.provider_id
          AND c.carrier_status_id IN (0,2)
        ) || (
          c.entity_id IN (
            SELECT cv.contractor_id
            FROM contractor_vendor cv
            WHERE cv.vendor_id = s.provider_id AND cv.status IN (0,2)
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

DROP PROCEDURE IF EXISTS purgeCarrierData;
CREATE PROCEDURE purgeCarrierData(IN `cid` INT)
  BEGIN
    SET @carrierId = `cid`;

   DELETE FROM eah USING `escrow_accounts_history` as eah WHERE eah.carrier_id = @carrierId;

   DELETE FROM eh USING `entity_history` as eh LEFT JOIN settlement_cycle as sc ON eh.cycle_id = sc.id WHERE sc.carrier_id = @carrierId;

   DELETE FROM rah USING  `reserve_account_history` as rah LEFT JOIN settlement_cycle as sc ON rah.settlement_cycle_id = sc.id WHERE sc.carrier_id = @carrierId;

   DELETE FROM dt USING  `disbursement_transaction` as dt LEFT JOIN settlement_cycle as sc ON dt.settlement_cycle_id = sc.id WHERE sc.carrier_id = @carrierId;

   DELETE FROM rt USING  `reserve_transaction` as rt LEFT JOIN deductions as d ON rt.deduction_id = d.id WHERE d.carrier_id = @carrierId;
   DELETE FROM rt USING  `reserve_transaction` as rt LEFT JOIN deductions as d ON rt.deduction_id = d.id WHERE d.settlement_cycle_id IN (SELECT id FROM settlement_cycle WHERE carrier_id = @carrierId);
   DELETE FROM rt USING  `reserve_transaction` as rt LEFT JOIN settlement_cycle as sc ON rt.settlement_cycle_id = sc.id WHERE sc.carrier_id = @carrierId;


   DELETE FROM p USING  `payments` as p WHERE p.carrier_id = @carrierId;
   DELETE FROM p USING  `payments` as p WHERE p.settlement_cycle_id IN (SELECT id FROM settlement_cycle WHERE carrier_id = @carrierId);

   DELETE FROM d USING  `deductions` as d WHERE d.carrier_id = @carrierId;
   DELETE FROM d USING  `deductions` as d WHERE d.settlement_cycle_id IN (SELECT id FROM settlement_cycle WHERE carrier_id = @carrierId);

   DELETE FROM scr USING `settlement_cycle_rule` as scr WHERE scr.carrier_id = @carrierId;

   DELETE FROM sc USING  `settlement_cycle` as sc WHERE sc.carrier_id = @carrierId;

  END;

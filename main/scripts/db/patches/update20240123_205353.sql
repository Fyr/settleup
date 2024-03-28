DROP TABLE IF EXISTS
bank_account_history,
bank_account_type,
bank_account_limit_type,
bank_account_temp,
bank_account_ach,
bank_account_cc,
bank_account_check,
bank_account;

DELIMITER $$

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
END$$

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
END$$

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
END$$

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

END$$

DELIMITER ;
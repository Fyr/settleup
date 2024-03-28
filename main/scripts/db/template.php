<?php

$cfg = parse_ini_file('database.ini');
$c = 0;

try {
    $db = new PDO('mysql:host=' . $cfg['db_host'] . ';dbname=' . $cfg['db_name'], $cfg['db_user'], $cfg['db_pass']);
} catch (PDOException $e) {
    echo $e->getMessage();
}


//generate payment templates

$res = $db->query('SELECT * from payment_setup WHERE level_id = 1 AND contractor_id IS NULL AND deleted = 0');
$templates = $res->fetchAll(PDO::FETCH_ASSOC);
$c++;

foreach ($templates as $template) {
    $stmt = $db->prepare('SELECT * from contractor c LEFT JOIN entity e ON c.entity_id = e.id WHERE c.carrier_id = ? AND e.deleted = 0');
    $stmt->execute(array($template['carrier_id']));
    $contractors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $c++;
    if (count($contractors)) {
        $sql = 'INSERT INTO payment_setup (carrier_id, payment_code, carrier_payment_code, description, category, terms, department, gl_code, disbursement_code, recurring, level_id, billing_cycle_id, rate, first_start_day, second_start_day, quantity, deleted, week_day, week_offset, master_setup_id, contractor_id, changed)'
            . 'VALUES (:carrier_id, :payment_code, :carrier_payment_code, :description, :category, :terms, :department, :gl_code, :disbursement_code, :recurring, :level_id, :billing_cycle_id, :rate, :first_start_day, :second_start_day, :quantity, :deleted, :week_day, :week_offset, :master_setup_id, :contractor_id, :changed)';

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':carrier_id', $carrier_id);
        $stmt->bindParam(':payment_code', $payment_code);
        $stmt->bindParam(':carrier_payment_code', $carrier_payment_code);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':terms', $terms);
        $stmt->bindParam(':department', $department);
        $stmt->bindParam(':gl_code', $gl_code);
        $stmt->bindParam(':disbursement_code', $disbursement_code);
        $stmt->bindParam(':recurring', $recurring);
        $stmt->bindParam(':level_id', $level_id);
        $stmt->bindParam(':billing_cycle_id', $billing_cycle_id);
        $stmt->bindParam(':rate', $rate);
        $stmt->bindParam(':first_start_day', $first_start_day);
        $stmt->bindParam(':second_start_day', $second_start_day);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':deleted', $deleted);
        $stmt->bindParam(':week_day', $week_day);
        $stmt->bindParam(':week_offset', $week_offset);
        $stmt->bindParam(':master_setup_id', $master_setup_id);
        $stmt->bindParam(':contractor_id', $contractor_id);
        $stmt->bindParam(':changed', $changed);

        foreach ($contractors as $contractor) {
            $carrier_id = $template['carrier_id'];
            $payment_code = $template['payment_code'];
            $carrier_payment_code = $template['carrier_payment_code'];
            $description = $template['description'];
            $category = $template['category'];
            $terms = $template['terms'];
            $department = $template['department'];
            $gl_code = $template['gl_code'];
            $disbursement_code = $template['disbursement_code'];
            $recurring = $template['recurring'];
            $level_id = 2;
            $billing_cycle_id = $template['billing_cycle_id'];
            $rate = $template['rate'];
            $first_start_day = $template['first_start_day'];
            $second_start_day = $template['second_start_day'];
            $quantity = $template['quantity'];
            $deleted = 0;
            $week_day = $template['week_day'];
            $week_offset = $template['week_offset'];
            $master_setup_id = $template['id'];
            $contractor_id = $contractor['entity_id'];
            $changed = 0;

            $stmt->execute();
            $c++;
        }
    }
}

//generate deduction templates

$res = $db->query('SELECT ds.*, v.entity_id as v_entity_id, v.carrier_id as v_carrier_id from deduction_setup ds LEFT JOIN vendor v ON ds.provider_id = v.entity_id WHERE ds.level_id = 1 AND ds.contractor_id IS NULL AND ds.deleted = 0');
$templates = $res->fetchAll(PDO::FETCH_ASSOC);
$c++;

foreach ($templates as $template) {
    $carrier_id = null;
    if ($template['v_entity_id']) {
        if ($template['v_carrier_id']) {
            $carrier_id = $template['v_carrier_id'];
        }
    } else {
        $carrier_id = $template['provider_id'];
    }

    if ($carrier_id) {
        $stmt = $db->prepare('SELECT * from contractor c LEFT JOIN entity e ON c.entity_id = e.id WHERE c.carrier_id = ? AND e.deleted = 0');
        $stmt->execute(array($carrier_id));
        $contractors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $c++;
        if (count($contractors)) {
            $sql = 'INSERT INTO deduction_setup (provider_id, deduction_code, vendor_deduction_code, description, category, terms, department, gl_code, disbursement_code, recurring, level_id, billing_cycle_id, rate, first_start_day, second_start_day, quantity, deleted, week_day, week_offset, master_setup_id, contractor_id, changed, priority, eligible, reserve_account_receiver)'
                . 'VALUES (:provider_id, :deduction_code, :vendor_deduction_code, :description, :category, :terms, :department, :gl_code, :disbursement_code, :recurring, :level_id, :billing_cycle_id, :rate, :first_start_day, :second_start_day, :quantity, :deleted, :week_day, :week_offset, :master_setup_id, :contractor_id, :changed, :priority, :eligible, :reserve_account_receiver)';

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':provider_id', $provider_id);
            $stmt->bindParam(':deduction_code', $deduction_code);
            $stmt->bindParam(':vendor_deduction_code', $vendor_deduction_code);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':terms', $terms);
            $stmt->bindParam(':department', $department);
            $stmt->bindParam(':gl_code', $gl_code);
            $stmt->bindParam(':disbursement_code', $disbursement_code);
            $stmt->bindParam(':recurring', $recurring);
            $stmt->bindParam(':level_id', $level_id);
            $stmt->bindParam(':billing_cycle_id', $billing_cycle_id);
            $stmt->bindParam(':rate', $rate);
            $stmt->bindParam(':first_start_day', $first_start_day);
            $stmt->bindParam(':second_start_day', $second_start_day);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':deleted', $deleted);
            $stmt->bindParam(':week_day', $week_day);
            $stmt->bindParam(':week_offset', $week_offset);
            $stmt->bindParam(':master_setup_id', $master_setup_id);
            $stmt->bindParam(':contractor_id', $contractor_id);
            $stmt->bindParam(':changed', $changed);
            $stmt->bindParam(':priority', $priority);
            $stmt->bindParam(':eligible', $eligible);
            $stmt->bindParam(':reserve_account_receiver', $reserve_account_receiver);

            foreach ($contractors as $contractor) {
                $provider_id = $template['provider_id'];
                $deduction_code = $template['deduction_code'];
                $vendor_deduction_code = $template['vendor_deduction_code'];
                $description = $template['description'];
                $category = $template['category'];
                $terms = $template['terms'];
                $department = $template['department'];
                $gl_code = $template['gl_code'];
                $disbursement_code = $template['disbursement_code'];
                $recurring = $template['recurring'];
                $level_id = 2;
                $billing_cycle_id = $template['billing_cycle_id'];
                $rate = $template['rate'];
                $first_start_day = $template['first_start_day'];
                $second_start_day = $template['second_start_day'];
                $quantity = $template['quantity'];
                $deleted = 0;
                $week_day = $template['week_day'];
                $week_offset = $template['week_offset'];
                $master_setup_id = $template['id'];
                $contractor_id = $contractor['entity_id'];
                $changed = 0;
                $priority = $template['priority'];
                $eligible = $template['eligible'];
                $reserve_account_receiver = $template['reserve_account_receiver'];

                $stmt->execute();
                $c++;
            }
        }
    }
}




$db = null;

echo "\n" . 'Queries: ' .$c . "\n";

UPDATE deduction_setup as ds LEFT JOIN entity as e ON ds.contractor_id = e.id SET ds.deleted = 1 WHERE e.deleted = 1;
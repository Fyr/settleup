DELETE h FROM reserve_account_history AS h
LEFT JOIN reserve_account ra
ON h.reserve_account_id = ra.id
WHERE ra.deleted = 1;
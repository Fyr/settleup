use pfleet;

UPDATE reserve_account_contractor
  SET reserve_account_vendor_id = 1
WHERE id < 3;

UPDATE reserve_account_contractor
  SET reserve_account_vendor_id = 2
  WHERE id >= 3;

UPDATE reserve_account
  SET current_balance = 3000
  WHERE id = 5;

UPDATE reserve_account
  SET current_balance =110
  WHERE id = 6;
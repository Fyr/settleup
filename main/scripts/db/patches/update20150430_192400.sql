UPDATE `settlement_cycle` as sc SET sc.cycle_close_date = sc.cycle_start_date WHERE sc.cycle_close_date = '0000-00-00';
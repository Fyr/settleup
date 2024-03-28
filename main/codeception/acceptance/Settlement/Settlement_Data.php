<?php
namespace Codeception\Module;
use \AcceptanceTester;

class Settlement_Data
{
    public static $company_Filter_Field = '#company';
    public static $settlement_Menu = '//div[3]/ul/li[1]/a';
    public static $settlement = 'Settlement';
    public static $settlement_URL = '/settlement_index';
    public static $settlement_Cycle_Header_Table_Original = array ("Settlement Cycle", "Period Start Date", "Period Close Date", "Processing Date", "Disbursement Date", "Cycle Status", "Action");
    public static $settlement_Process_Header_Table_Original = array("ID", "Company", "Compensations", "Deductions", "Balance Due", "Contributions", "Withdrawals", "Settlement Amount", "Action");
    public static $settlement_Cycle_Header_Table = '.datagrid.table.table-bordered.additional-cycle-grid thead th';
    public static $settlement_Process_Header_Table = 'table.datagrid.table.table-bordered.table-striped  thead th';
    public static $settlement_Create_New_Button = 'div.content.clearfix > div.right > a';
    public static $settlement_Create_Rules_Title = 'div.content.clearfix > h3';
    public static $settlement_Cycle_Period_ID = '#cycle_period_id';
    public static $settlement_Start_Date = '#cycle_start_date';
    public static $settlement_Processing_Deadline = '#payment_terms';
    public static $settlement_Disbursement_Terms = '#disbursement_terms';
    public static $settlement_Verify_Button = 'td.buttons > div > a.btn.btn-success';
    public static $settlement_Process_Button = 'a.btn.btn-success.process-btn';
    public static $settlement_Approve_Button = 'td.buttons a.btn.btn-success';
    public static $settlement_Delete_Button = 'td a.btn.btn-danger';
    public static $settlement_Total = 'tr.totals td:not(:first):not(:last)';
    public static $settlement_Filter_Button = 'div.btn.btnFilterContractors';
    public static $edit_Contractor_Settlement_Button = '.btn.btn-primary';
    public static $adjustment_Button = 'Adjustment';
    public static $adjustment_Type_Dropdown = '#adjustment_type';
    public static $adjustment_Amount ='#amount';
    public static $current_Balance = '#current_balance';

    public static $payments_Table = '#updatethis-Application_Model_Entity_Payments_Payment td';
    public static $deductions_Table = '#updatethis-Application_Model_Entity_Deductions_Deduction td';
    public static $reserve_Accounts_Table ='#updatethis-Application_Model_Entity_Accounts_Reserve_History td';
    public static $contractor_Reserve_Account_Adjustment_Table = '#reserve_account_contractor_modal td';

    public static $select_First_Days_Locator = '#first_start_day';
    public static $select_Second_Days_Locator = '#second_start_day';
    public static $select_First_Week_Day_Locator = '#week_day';
    public static $select_Second_Week_Day_Locator = '#second_week_day';

    public static $totalProcess = array("$1,595.00", "$825.00", "$0.00", "$75.00", "$0.00", "$695.00");  // gets all values from Total line. They are arranged in the order ('Payments', 'Deductions', 'Balance Due', 'Contributions', 'Withdrawals', 'Settlement Amount')

}
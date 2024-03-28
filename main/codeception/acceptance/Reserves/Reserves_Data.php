<?php
namespace Codeception\Module;
use \AcceptanceTester;

class Reserves_Data
{
    public static $reserves_Menu = '//div[3]/ul/li[4]/a';
    public static $reserves = 'Reserves';
    //Reserve Transactions
    public static $reserve_Transactions_Submenu = '//div[3]/ul/li[4]/ul/li[1]/a';
    public static $reserve_Transactions = 'Reserve Transactions';
    public static $reserve_Transactions_Menu_URL = '/reserve_transactions';
    public static $reserve_Transaction_Header_Table = '#updateheader-Application_Model_Entity_Accounts_Reserve_Transaction > th';
    public static $reserve_Transaction_Header_Table_Original = array("","ID", "Company","Priority","Vendor","Code", "Description", "Type", "Amount", "Action");
    public static $reserve_Company_Filter = '#company_name';
    public static $reserve_Transaction_Type_Filter = '#title';
    public static $reserve_Transaction_Delete_Button = '.btn.btn-danger.confirm';
    public static $reserve_Transaction_Edit_Button = '//*[@id="updatethis-Application_Model_Entity_Accounts_Reserve_Transaction"]/tr/td[10]/a[1]';
    public static $reserve_Transaction_Amount_Field = '#amount';
    //Vendor Reserve Accounts
    public static $vendor_Reserve_Account_Submenu = '//div[3]/ul/li[4]/ul/li[2]/a';
    public static $vendor_Reserve_Accounts = 'Vendor Reserve Accounts';
    public static $vendor_Reserve_Accounts_Menu_URL = '/reserve_accountcarriervendor';
    public static $vendor_Reserve_Accounts_Header ='#updateheader-Application_Model_Entity_Accounts_Reserve_Vendor > th';
  public static $vendor_Reserve_Accounts_Header_Original = array("Priority", "Vendor", "Reserve Account", "Code", "Description", "Min. Balance", "Contribution Amount", "Current Balance", "Action");
    public static $create_New_Vendor_Reserve_Accounts_Button = 'div.row.table-controll a';
    //Create Vendor Reserve Account
    public static $vendor_Header_Table_In_Select_Vendor_Popup_Original = array("ID","Vendor","Contact","Federal Tax ID");
    public static $customer_Header_Table_In_Select_Vendor_Popup_Original=array("Customer","Short Code","Federal Tax ID","Internal ID");
    public static $delete_Selected_Vendor_Button = '#entity_id_clear';
    public static $create_Vendor_Reserve_Account = 'Create Vendor Reserve Account';
    public static $create_Vendor_Reserve_Account_Menu_URL = '/reserve_accountcarriervendor/new';
    public static $create_Vendor_Reserve_Account_Submenu = '//div[3]/ul/li[4]/ul/li[3]/a';
    public static $vendor_Name_Field = 'Vendor';
    public static $vendor_Locator_Field = '#entity_id_title';
    public static $select_Vendor_Popup = '#entity_id_modal';
    public static $select_vendor_Title_In_Popup = 'Select Vendor';
    public static $vendor_Header_Table_In_Select_Vendor_Popup = '#Vendor th';
    public static $customer_Header_Table_In_Select_Vendor_Popup = '#Customer th';
    public static $close_Button_In_Select_Vendor_Popup = '#entity_id_modal > div.modal-header > button';
    public static $reserve_Account_Name_Field = 'Reserve Account *';
    public static $reserve_Account_Locator_Field = '#account_name';
    public static $reserve_Code_Name_Field = 'Reserve Code';
    public static $reserve_Code_Locator_Field = '#vendor_reserve_code';
    public static $description_Name_Field = 'Description';
    public static $description_Locator_Field = '#description';
    public static $minimum_Balance_Name_Field = 'Minimum Balance';
    public static $minimum_Balance_Locator_Field = '#min_balance';
    public static $contribution_Amount_Name_Field = 'Contribution Amount';
  public static $contribution_Amount_Locator_Field = '#contribution_amount';
  public static $initial_Balance_Name_field = 'Initial Balance';
    public static $initial_Balance_Locator_field = '#initial_balance';
    public static $current_Balance_Name_Field = 'Current Balance';
    public static $current_Balance_Locator_Field = '#current_balance';
    //Contractor Reserve Accounts
    public static $contractor_Reserve_Account_Submenu = '//div[3]/ul/li[4]/ul/li[4]/a';
    public static $contractor_Reserve_Accounts = 'Contractor Reserve Accounts';
    public static $contractor_Reserve_Accounts_Menu_URL = '/reserve_accountcontractor';
    public static $contractor_Reserve_Accounts_Header_Table = '#updateheader-Application_Model_Entity_Accounts_Reserve_Contractor >th';
  public static $contractor_Reserve_Accounts_Header_Table_Original = array("ID", "Company", "Priority", "Vendor", "Code", "Description", "Min. Balance", "Contribution Amount", "Current Balance", "Action");
    //Create Contractor Reserve Account
    public static $create_Contractor_Reserve_Account_Submenu = '//div[3]/ul/li[4]/ul/li[5]/a';
    public static $create_Contractor_Reserve_Account = 'Create Contractor Reserve Account';
    public static $create_Contractor_Reserve_Account_Menu_URL = '/reserve_accountcontractor/new';
    public static $company_Name_Field = 'Company';
    public static $company_Filter_Field = '#name';
    public static $delete_Selected_Company_Button = '#entity_id_clear';
    public static $company_Locator_Field = '#entity_id_title';
    public static $select_Contractor_Popup = '#entity_id_modal';
    public static $create_Contractor_Vendor_Field = '#vendor_id_title';
    public static $select_Contractor_Title_Popup = 'Select contractor';
    public static $select_Contractor_Table_Popup = '#entity_id_modal table';
    public static $select_Contractor_Header_Table_Popup = '#entity_id_modal table th';
    public static $select_Contractor_Header_Table_Popup_Original = array("ID", "Company Name", "First Name", "Last Name");
    public static $close_Button_Select_Contractor_Table_Popup = '#entity_id_modal > div.modal-header > button';
    public static $vendor_Locator_Field_On_Contractor_RA = '#vendor_id_title';
    public static $delete_Selected_Vendor_Button_On_Contractor_RA = '#vendor_id_clear';
    public static $select_Vendor_Popup_For_Contractor_RA = '#vendor_id_modal';
    public static $close_Button_In_Select_Vendor_Popup_For_Contractor_RA = '#vendor_id_modal > div.modal-header > button';
    public static $reserve_Account_Field_On_Contractor_RA = '#reserve_account_vendor_id_title';
    public static $delete_Reserve_Account_Button_On_Contractor_RA = '#reserve_account_vendor_id_clear';
    public static $select_Reserve_Account_Title_Popup = 'Select reserve account';
    public static $select_Reserve_Account_Popup = '#reserve_account_vendor_id_modal';
    public static $select_Reserve_Account_Header_Table_In_Popup = '#reserve_account_vendor_id_modal table th';
  public static $select_Reserve_Account_Header_Table_Original = array("Priority", "Vendor", "Reserve Account", "Code", "Description", "Min. Balance", "Contribution Amount", "Current Balance");
    public static $close_select_Reserve_Account_Popup_Button = '#reserve_account_vendor_id_modal > div.modal-header > button';
    //----------------------------------
    public static $filter = 'Filter';
    public static $clear = 'Clear';
    public static $reserves_Filter_Button = 'div.btn.btnFilter';
    public static $reserves_Clear_Button = 'div.btn.btnClearFilter';
    public static $cancel_Button = '.btn.cancel.btn-danger';
    public static $reserves_Dropdown_Menu = '.dropdown-toggle';

    public static $vendor_RA_table = '#updatethis-Application_Model_Entity_Accounts_Reserve_Vendor td';
    public static $vendor_Current_Balance_Column = '#updatethis-Application_Model_Entity_Accounts_Reserve_Vendor td:nth-child(9)';
    public static $contractor_RA_Table = '#updatethis-Application_Model_Entity_Accounts_Reserve_Contractor td';
    public static $create_New_Contractor_Reserve_Account_Button = 'div.row.right a';
//    public static $select_Vendor_Company = '#entity_id_title';
    public static $list_Contractor_In_Table_Popup = '#entity_id_modal table > tbody td';
    public static $select_Reserve_Account = '#reserve_account_vendor_id_title';
    public static $reserve_Account_Table_In_Popup ='#reserve_account_vendor_id_modal table > tbody td';
    public static $account_Name = '#account_name';
  //  public static $description = '#description';
//    public static $min_Balance = '#min_balance';
//    public static $contribution_Amount = '#contribution_amount';
 //   public static $initial_Balance = '#initial_balance';
  //  public static $current_Balance = '#current_balance';
    public static $sum_Reserve_Account_Ending_Balances = "1175";


}
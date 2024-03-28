<?php
namespace Codeception\Module;

use \AcceptanceTester;

class Customers_Data
{
    public static $customers = 'Customers';
    public  static $customer_Menu_Dropdown = '.dropdown-toggle';
    public static $customers_Menu = '//div[3]/ul/li[6]/a';
    public static $customer_Filter_Button = 'div.btn.btnFilter';
    public static $filter_Name_Button = 'Filter';
    public static $customer_Clear_Button = 'div.btn.btnClearFilter';
    public static $Clear_Name_Button = 'Clear';
    //Customers
    public static $customers_Submenu = '//div[3]/ul/li[6]/ul/li[1]/a';
    public static $customer_name_Filter = '#name';
    public static $customer_Page_URL = '/carriers_index';
    public static $create_Customer_Page_URL = '/carriers_index/new';
    public static $customers_Header_Table = '#updateheader-Application_Model_Entity_Entity_Carrier > th';
    public static $customers_Header_Table_Original = array("Customer", "Short Code", "Federal Tax ID", "Internal ID", "Action");
    public static $create_New_Customer_Button = 'div.row.table-controll a';
    public static $create_New_Customer_Name_Button = 'Create New';
    public static $customer_Edit_Button = '#updatethis-Application_Model_Entity_Entity_Carrier a.btn.btn-primary';
    public static $customer_Purge_Data = 'a.btn.btn-danger.confirm';
    public static $customer_Confirm_Purge_Yes_Button = '#btn-confirm';
    //Settlement Info
    public static $settlement_Info_Submenu = '//div[3]/ul/li[6]/ul/li[2]/a';
    public static $settlement_Info = 'Settlement Info';
    public static $settlement_Info_Page_URL = '/settlement_rule';
    public static $settlement_Info_Header_Table ='#updateheader-Application_Model_Entity_Settlement_Rule > th';
    public static $settlement_Info_Header_Table_Original = array("Customer", "Settlement Cycle", "Processing Deadline", "Disbursement Terms", "Last Closed Settlement", "Action");
    //Bank Accounts
    public static $bank_Accounts = 'Bank Accounts';
    public static $bank_Account_Page_URL = '/bankaccounts_index/carrier';
    public static $bank_Account_Submenu = '//div[3]/ul/li[6]/ul/li[3]/a';
    public static $bank_Account_Header_table_Original = array("Customer", "Account Nickname", "Process", "Compensation Type", "Account Type", "Payee", "Action");
    //Escrow Accounts
    public static $escrow_Accounts = 'Escrow Accounts';
    public static $settlement_Escrow_Account_Url = '/carriers_index/escrow/';
    public static $escrow_Account_Page_URL = '/escrow';
    public static $escrow_Account_Submenu = '//div[3]/ul/li[6]/ul/li[4]/a';
    public static $escrow_Account_header_Table ='#updateheader-Application_Model_Entity_Accounts_Escrow > th';
    public static $escrow_Account_header_Table_Original = array("Customer","Escrow Account Holder","Bank Name","Next Check Number","Action");
    //Custom Field Descriptions
    public static $custom_Field = 'Custom Field';
    public static $custom_Field_Descriptions = 'Custom Field Descriptions';
    public static $custom_Field_Descriptions_Page_URL = '/custom';
    public static $custom_Field_Descriptions_Submenu = '//div[3]/ul/li[6]/ul/li[5]/a';
    public static $payment_Code = 'Compensation Code';
    public static $payment_Code_Locator = '#payment_code';
    public static $customer_Payment_Code = 'Customer Compensation Code';
    public static $customer_Payment_Code_Locator = '#carrier_payment_code';
    public static $description = 'Description';
    public static $description_Locator = '#description';
    public static $category = 'Category';
    public static $category_Locator = '#category';
    public static $department = 'Department';
    public static $department_Locator = '#department';
    public static $GL_Code = 'GL Code';
    public static $GL_Code_Locator = '#gl_code';
    public static $invoice = 'Invoice';
    public static $invoice_Locator ='#invoice';
    public static $invoice_Date = 'Invoice Date';
    public static $invoice_Date_Locator = '#invoice_date';
    public static $disbursement_Code = 'Disbursement Code';
    public static $disbursement_Code_Locator = '#disbursement_code';

    static public $table_Entity_Customer = '#updatethis-Application_Model_Entity_Entity_Carrier td';
    static public $customer_tax_id = '#tax_id';
    static public $customer_name = '#name';
    static public $customer_contract = '#contact';
    static public $customer_terms = '#terms';
    static public $customer_account_nickname = '#account_nickname';
    static public $customer_bank_routing_id = '#ACH_bank_routing_id';
    static public $customer_bank_account_id = '#ACH_bank_account_id';
    static public $customer_escrow_account_holder = '#escrow_account_holder';
    static public $customer_holder_federal_tax_id = '#holder_federal_tax_id';
    static public $customer_bank_name = '#bank_name';
    static public $customer_bank_routing_number = '#bank_routing_number';
    static public $customer_bank_account_number = '#bank_account_number';


}
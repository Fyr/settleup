<?php
namespace Codeception\Module;

use \AcceptanceTester;

class Vendors_data
{
    public static $vendors_Menu = '//div[3]/ul/li[8]/a';
    public static $vendor_Url = '/vendors_index';
    public static $vendor_Title_Page = 'Vendors';
    public static $vendors_Header_Table = '#updateheader-Application_Model_Entity_Entity_Vendor > th';
    public static $vendor_Header_Table_Original = array("ID", "Vendor","Contact","Federal Tax ID","Action");
    public static $create_New_Vendor_Button = 'div.row.table-controll div:nth-child(2) > a';
    public static $create_New_Vendor_Name_Button = 'Create New';

    public static $filter_Name_Button = 'Filter';
    public static $clear_Name_Button = 'Clear';
    public static $clear_Button = '.btn.btnClearFilter';
    public static $filter_Button = 'div.btn.btnFilter';
    public static $cancel_Button = 'div.content.clearfix div.form-actions a';
    public static $save_Button = '#submit';

    public static $selectorVendorTable = '#updatethis-Application_Model_Entity_Entity_Vendor td';
    public static $vendor_Account_Nickname = '#account_nickname';
    public static $vendor_Bank_Routing_Number = '#ACH_bank_routing_id';
    public static $vendor_Bank_Account_Number = '#ACH_bank_account_id';
    //create vendor
    public static $vendor_Bank_Account_URL ='/bankaccounts_index';
    public static $bank_Account_Name_Button = 'Bank Account';
    public static $reserve_Account_Name_Button = 'Reserve Accounts';
    public static $bank_Account_Button = 'div.left.bank-reserve-acc-actions a:nth-child(1)';
    public static $reserve_Account_Button = 'div.left.bank-reserve-acc-actions > a:nth-child(2)';

    public static $create_Vendor_Page_URL = '/vendors_index/new';
    public static $create_Vendor_Title_Page = 'Create Vendor';
    public static $vendor_ID_Field = '#code';
    public static $vendor_ID_Name = 'ID *';
    public static $vendor_Name_Field = '#name';
    public static $vendor_Name = 'Vendor *';
    public static $vendor_Contact_Field = '#contact';
    public static $vendor_Contact_Name = 'Contact *';
    public static $vendor_Federal_Tax_ID_Field = '#tax_id';
    public static $vendor_Fed_Tax_ID = 'Federal Tax ID *';
    public static $vendor_Address1 = 'Address 1';
    public static $vendor_Address2 = 'Address 2';
    public static $vendor_City = 'City';
    public static $vendor_State = 'State';
    public static $vendor_Zip ='Zip';
    public static $vendor_Phone ='Phone';
    public static $vendor_Fax ='Fax';
    public static $vendor_Email ='Email';
    public static $vendor_Correspondence ='Correspondence';
    public static $vendor_Correspondence_Field = '#correspondence_method';

}
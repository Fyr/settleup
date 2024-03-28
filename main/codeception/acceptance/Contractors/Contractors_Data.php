<?php
namespace Codeception\Module;
use \AcceptanceTester;

class Contractors_Data
{
    public static $contractors_Menu = '//div[3]/ul/li[7]/a';
    public static $contractor_URL = '/contractors_index';
    public static $contractors = 'Contractors';
    public static $contractor_Status_Filter = '#status option';
    public static $contractor_Status_Filter_Original = array("All","Active","Terminated");
    public static $contractor_Header_Table = '#updateheader-Application_Model_Entity_Entity_Contractor > th';
    public static $contractor_Header_Table_Original=array("","ID", "Company","Fed Tax ID","First Name","Last Name", "Division", "Dept", "Route", "Start Date","Termination Date", "Restart Date", "Status", "Action");

    public static $filter_Name_Button = 'Filter';
    public static $clear_Name_Button = 'Clear';
    public static $clear_Button = '.btn.btnClearFilter';
    public static $filter_Button = 'div.btn.btnFilter';
    public static $cancel_Button = 'div.content.clearfix div.form-actions a';
    public static $save_Button = '#submit';

    public static $upload_Name_Button = 'Upload';
    public static $upload_Button = 'div.row.table-controll a:nth-child(2)';
    public static $selector_Contractor_Table = '#updatethis-Application_Model_Entity_Entity_Contractor td';
    public static $create_New_Contractor_Button = '.btn.btn.btn-success.contractors-add-new';
    public static $create_New_Contractor_Name_Button = 'Create New';
    public static $edit_Contractor_Button = '#updatethis-Application_Model_Entity_Entity_Contractor > tr a.btn.btn-primary';
    public static $payment_Template_Button = 'Compensation Templates';
    public static $deduction_Template_Button = 'Deduction Templates';

 //   public static $contractor_Bank_Account_Button = 'div.left.bank-reserve-acc-actions > a:nth-child(1)';
    public static $contractor_Create_New_BA_Button = 'div.row.table-controll > div.row.right > div:nth-child(2) i';
    public static $create_Contractor_BA_URL = 'bankaccounts_index/new';
    public static $contractor_Add_Vendor = 'div.vendor-subform.subform.row a.btn.btn-success.add > i';
    public static $contractor_Account_Nickname = '#account_nickname';
    public static $contractor_Payment_Type = '#payment_type';
    public static $contractor_Limit_Type = '#limit_type';
    public static $contractor_Percentage_Limit_Value = '#percentage';
    public static $contractor_Amount_Limit_Value = '#amount';
    public static $contractor_Check_Message_1 = '#check_message';
    public static $contractor_Check_Message_2 = '#check_message_2';
    public static $contractor_ACH_Bank_Routing_ID = '#ACH_bank_routing_id';
    public static $contractor_ACH_Bank_Account_ID = '#ACH_bank_account_id';
    // upload page
    public static $upload_Contractor_Page_URL = 'file_index/edit/file_type/3';
    public static $upload_Contractor_Title_Page = 'Upload Contractor';
    public static $title_Name_Field = 'Title *';
    public static $title_Locator_Field = '#title';
    public static $file_Name_Field = 'File';
    public static $file_Locator_Field = '#file';
    public static $supported_Types = 'Supported types: xls, xlsx';
    //create new contractor
    public static $contractor_New_URL = '/contractors_index/new';
    public static $create_Contractor_Title_Page = 'Create Contractor';
    public static $bank_Account_Name_Button = 'Bank Accounts';
    public static $reserve_Account_Name_Button = 'Reserve Accounts';
    public static $contractor_Bank_Account_Button = '//form/div[1]/a[1]';
    public static $reserve_Account_Button = '//form/div[1]/a[2]';
    public static $contractor_ID_Field = '#code';
    public static $contractor_ID_Name = 'ID *';
    public static $contractor_Company_Name_Field = '#company_name';
    public static $contractor_Company_Name = 'Company *';
    public static $contractor_First_Name_Field = '#first_name';
    public static $contractor_First_Name = 'First Name *';
    public static $contractor_Middle_Initial_field = '#middle_initial';
    public static $contractor_Middle_Initial = 'Middle Initial';
    public static $contractor_Last_Name_Field = '#last_name';
    public static $contractor_Last_Name = 'Last Name *';
    public static $contractor_Tax_ID_Field = '#tax_id';
    public static $contractor_Tax_ID = 'Fed Tax ID *';
    public static $contractor_Social_Security_ID_Field = '#social_security_id';
    public static $contractor_Social_Security_ID = 'Social Security #';
    public static $contractor_DOB = 'DOB';
    public static $contractor_DOB_Field = '#dob';
    public static $contractor_Drivers_License_Field = '#driver_license';
    public static $contractor_State_Of_Issuance = 'State of Issuance';
    public static $contractor_State_Of_Issuance_Field = '#state_of_operation';
    public static $contractor_Expires = 'Expires';
    public static $contractor_Expires_Field = '#expires';
    public static $contractor_Classification = 'Classification';
    public static $contractor_Classification_Field = '#classification';
    public static $contractor_Division = 'Division';
    public static $contractor_Division_Field = '#division';
    public static $contractor_Department = 'Department';
    public static $contractor_Department_Field = '#division';
    public static $contractor_Route = 'Route';
    public static $contractor_Route_Field = '#route';
    public static $contractor_Status = 'Status';
    public static $contractor_Status_Field = '#status_title';
    public static $contractor_Gender = 'Gender';
    public static $contractor_Gender_Field = '#gender_id';
    public static $contractor_Start_Date = 'Start Date';
    public static $contractor_Start_Date_Field = '#start_date';
    public static $contractor_Termination_Date = 'Termination Date';
    public static $contractor_Termination_Date_Field = '#termination_date';
    public static $contractor_Restart_Date = 'Restart Date';
    public static $contractor_Restart_Date_Field = '#rehire_date';
    public static $contractor_Address1 = 'Address 1';
    public static $contractor_Address2 = 'Address 2';
    public static $contractor_City = 'City';
    public static $contractor_State = 'State';
    public static $contractor_Zip ='Zip';
    public static $contractor_Phone ='Phone';
    public static $contractor_Fax ='Fax';
    public static $contractor_Email ='Email';
    public static $contractor_Settlement_Delivery ='Settlement Delivery';
    public static $contractor_Settlement_Delivery_Field = '#correspondence_method';
    public static $contractor_Deduction_Priority = 'Deduction Priority';
    public static $contractor_Vendor = 'Vendor';
    //Contractor Compensation Templates
    public static $payment_Code_Filter_Field = '#payment_code';
    public static $edit_Payment_Template_Button = '#updatethis-Application_Model_Entity_Payments_Setup td.buttons a';
    public static $quantity_Locator = '#quantity';
    public static $rate_Locator = '#rate';

}
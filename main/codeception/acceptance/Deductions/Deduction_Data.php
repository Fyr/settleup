<?php
namespace Codeception\Module;
use \AcceptanceTester;

class Deduction_Data
{
    public static $deductions = 'Deductions';
    public static $deductions_Dropdown_Menu = '.dropdown-toggle';
    public static $deductions_Menu = '//div[3]/ul/li[3]/a';
    public static $deduction_Submenu = '//div[3]/ul/li[3]/ul/li[1]/a';
    public static $upload_Deductions_Submenu = '//div[3]/ul/li[3]/ul/li[2]/a';
    public static $master_Deduction_Templates_Submenu = '//div[3]/ul/li[3]/ul/li[3]/a';
    public static $create_Master_Deduction_Template_Submenu = '//div[3]/ul/li[3]/ul/li[4]/a'; //click on Create Master Deduction Templates submenu

    public static $deductions_Menu_Link = '/deductions_deductions';
    public static $upload_Deductions_Menu_Link = '/file_index/edit/file_type/2';
    public static $master_Deduction_Templates_Menu_Link = '/deductions_setup';
    public static $create_Master_Deduction_Templates_Menu_Link = '/deductions_setup/new';
    public static $upload_Deductions = 'Upload Deductions';
    public static $master_Deduction_Templates = 'Master Deduction Templates';
    public static $create_Master_deduction_template = 'Create Master Deduction Template';
    public static $master_deduction_template_Details = 'Master Deduction Template Detail';

    public static $deductionTable=array("","ID", "Company","Priority","Vendor","Code", "Description", "Category", "Recurring", "Frequency", "Qty", "Rate", "Amount", "Balance","Adj. Balance", "Action");
    public static $deduction_Template_Header_Table_Original=array ("","Priority","Vendor","Code","Description","Category", "Recurring","Frequency","Qty","Rate","Action");
    public static $deductions_Header_Table = '#updateheader-Application_Model_Entity_Deductions_Deduction > th';
    // Deductions
    public static $company_Filter_Field = '#company_name';
    public static $deduction_Code_Filter_Field = '#deduction_code';
    public static $deduction_Edit_Button = 'td a.btn.btn-primary';
    public static $deductions_Table = '#updatethis-Application_Model_Entity_Deductions_Deduction';
    //upload deductions
    public static $title = 'Title';
    public static $title_locator = '#title';
    public static $file = 'File';
    public static $file_Locator = '#file';
    public static $submit_Button = '#submit';
    public static $supported_Types = 'Supported types: xls, xlsx';
    //Master Deduction Templates
    public static $deduction_Template_Header_Table ='#updateheader-Application_Model_Entity_Deductions_Setup > th';
    public static $delete_Selected_Button = 'a.btn.btn-danger.confirm.confirm-delete.btn-multiaction';
    public static $create_New_Deduction_Template_Button = 'div.row.right a.btn.btn-success';
    public static $title_Confirm_Deletion_popup = 'Confirm Deletion of Records';
    public static $description_Line1_Confirm_Deletion_popup = 'Deleting the master template will result in the deletion of all associated individual templates.';
    public static $body_Confirm_Deletion_popup = '#confirm-modal > div.modal-body > p:nth-child(1)';
    public static $description_Line2_Confirm_Deletion_popup = 'Do you still want to delete?';
    public static $close_Button_On_Confirm_Deletion_popup = '#confirm-modal > div.modal-header > a';
    public static $yes_Button_On_Confirm_Deletion_popup = '#btn-confirm';
    public static $no_Button_On_Confirm_Deletion_popup = '#confirm-modal a.btn.btn-success';
    //Create Master Deduction Template
    public static $customer_Header_Table_In_Popup_Original = array("Customer","Short Code","Federal Tax ID","Internal ID");
    public static $vendors_Header_Table_In_Poup_Original = array("ID","Vendor","Contact","Federal Tax ID");
    public static $select_Reserve_Account_Header_Table_In_popup_Original = array("Priority", "Vendor", "Reserve Account", "Code", "Description", "Min. Balance", "Contribution Amount", "Current Balance");
    public static $vendor_Locator = '#provider_id_title';
    public static $select_Provider_Popup = '#provider_id_modal';
    public static $title_Select_Provider_Popup = 'Select provider';
    public static $customers_Tab_In_Popup = 'a[href="#Customers"]';
    public static $customer_Header_Table_In_Popup = '#Customers th';
    public static $vendors_Tab_In_Popup = 'a[href="#Vendors"]';
    public static $vendors_Header_Table_In_Popup = '#Vendors th';
    public static $close_Button_Select_Provide_Popup = '#provider_id_modal > div.modal-header > button';
    public static $vendor = 'Vendor';
    public static $deduction_Code = 'Deduction Code';
    public static $deduction_Code_Locator = '#deduction_code';
    public static $description = 'Description *';
    public static $description_Locator = '#description';
    public static $category = 'Category';
    public static $category_Locator = '#category';
    public static $department = 'Department';
    public static $department_Locator = '#department';
    public static $GL_Code = 'GL Code';
    public static $GL_Code_Locator = '#gl_code';
    public static $disbursement_Code = 'Disbursement Code';
    public static $disbursement_Code_Locator ='#disbursement_code';
    public static $quantity = 'Quantity *';
    public static $quantity_Locator = '#quantity';
    public static $rate = 'Rate';
    public static $rate_Locator = '#rate';
    public static $recurring = 'Recurring';
    public static $recurring_Locator = '#recurring';
    public static $eligible = 'Eligible';
    public static $eligible_Locator = '#eligible';
    public static $reserve_Account = 'Reserve Account';
    public static $reserve_Account_Field_locator = '#reserve_account_receiver_title';
    public static $select_Reserve_Account_Popup = '#reserve_account_receiver_modal';
    public static $title_Select_Reserve_Account_Popup = 'Select reserve account';
    public static $select_Reserve_Account_Header_Table_In_popup = '#reserve_account_receiver_modal th';
    public static $select_Reserve_Account_Table_In_Popup = '#reserve_account_receiver_modal td';
    public static $close_Button_Select_Reserve_Account_Popup = '#reserve_account_receiver_modal > div.modal-header > button';

    public static $frequency_Locator = '#billing_cycle_id';
    public static $select_First_Week_Day_Locator = '#week_day';
    public static $select_Second_Week_Day_Locator = '#second_week_day';
    public static $select_First_Days_Locator = '#first_start_day';
    public static $select_Second_Days_Locator = '#second_start_day';
    public static $biweekly_Start_Date_Locator = '#biweekly_start_day';
    //Deduction Details
    public static $adjusted_Balance_Locator = '#adjusted_balance';
    //-----------------------------------------
    public static $deductions_Filter_Button = 'div.btn.btnFilter';
    public static $deductions_Clear_Button = 'div.btn.btnClearFilter';

    public static $master_Deduction_Templates_Table = '#updatethis-Application_Model_Entity_Deductions_Setup td';
    public static $create_New_Deduction_Template_button = 'div.row.right a.btn.btn-success';
 //   public static $vendor = '#provider_id_title';
   // public static $deduction_Code = '#deduction_code';
//    public static $description = '#description';
//    public static $category = '#category';
//    public static $quantity = '#quantity';
//    public static $rate = '#rate';
    public static $vendor_Table_In_Popup = '#Vendors > table > tbody td';
    public static $vendor_Tab_In_Popup = 'a[href="#Vendors"]';
    public static $customers_Table_In_Popup = '#Customers > table > tbody td';
//    public static $customers_Tab_In_Popup = 'a[href="#Customers"]';
    public static $add_Deduction_Button = 'div.row.table-controll a:nth-child(2)';
    public static $deduction_Template_Tab_In_Popup = 'a[href="#setup-deduction-setup"]';
    public static $select_Deduction_template_In_Table = '#updatethis-Application_Model_Entity_Deductions_Setup td';
    public static $contractor_Tab_In_Popup = 'a[href="#contractors-deduction-setup"]';
    public static $select_Contractor_In_Table = '#updatethis-Application_Model_Entity_Entity_Contractor td';
    public static $add_Template_Button_In_Popup = 'div.deduction-setup.setup.popup_checkbox_modal.modal.hide.fade.in > div.modal-footer > a';
}
<?php
namespace Codeception\Module;
use \AcceptanceTester;

class Payments_Data
{
    public static $payment_Table_Header_Original = array("", "ID", "Company", "Compensation Code", "Description", "Category", "Recurring", "Frequency", "Qty", "Rate", "Amount", "Action");
    public static $payment_Template_Table_Original = array("", "Compensation Code", "Description", "Category", "Recurring", "Frequency", "Qty", "Rate", "Action");

    public static $payments = 'Compensation';
    public static $master_Payment_Templates = 'Master Compensation Templates';
    public static $master_Payment_Templates_Detail = 'Master Compensation Template Detail';
    public static $create_Master_Payment_Template = 'Create Master Compensation Template';
    //public static $page_Header = 'h3';
    public static $payments_Page_URL = '/payments_payments';
    // Payments
    public static $company_Filter_Field = '#company_name';
    public static $payment_Code_Filter_Field = '#payment_code';
    public static $payment_Edit_Button = 'td a.btn.btn-primary';
    public static $frequency_Filter_Field = '#billing_title';
    public static $payments_Table = '#updatethis-Application_Model_Entity_Payments_Payment';
    // upload page
    public static $upload_Payments_Page_URL ='/file_index/edit/file_type/1';
    public static $upload_Payments = 'Upload Compensations';
    public static $title = 'Title';
    public static $title_locator = '#title';
    public static $file = 'File';
    public static $file_Locator = '#file';
    public static $submit_Button = '#submit';
    public static $supported_Types = 'Supported types: xls, xlsx';
    // master payments template page
    public static $master_Payments_Template_URL = '/payments_setup';
    public static $delete_Selected_Button = 'a.btn.btn-danger.confirm.confirm-delete.btn-multiaction';
    public static $create_New_Button = 'div.row.right a.btn.btn-success';
    public static $title_Confirm_Deletion_popup = 'Confirm Deletion of Records';
    public static $description_Line1_Confirm_Deletion_popup = 'Deleting the master template will result in the deletion of all associated individual templates.';
    public static $body_Confirm_Deletion_popup = '#confirm-modal > div.modal-body > p:nth-child(1)';
    public static $description_Line2_Confirm_Deletion_popup = 'Do you still want to delete?';
    public static $close_Button_On_Confirm_Deletion_popup = '#confirm-modal > div.modal-header > a';
    public static $yes_Button_On_Confirm_Deletion_popup = '#btn-confirm';
    public static $no_Button_On_Confirm_Deletion_popup = '#confirm-modal a.btn.btn-success';
    // create Master Payment Template
    public static $create_Master_Payment_Template_URL = '/payments_setup/new';
    public static $payment_Code = 'Compensation Code';
    public static $payment_Code_Locator = '#payment_code';
    public static $customer_Payment_Code = 'Division Compensation Code';
    public static $customer_Payment_Code_Locator = '#carrier_payment_code';
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
    public static $frequency_Locator = '#billing_cycle_id';
    public static $biweekly_Start_Date_Locator = '#biweekly_start_day';
    public static $select_First_Days_Locator = '#first_start_day';
    public static $select_Second_Days_Locator = '#second_start_day';
    public static $select_First_Week_Day_Locator = '#week_day';
    public static $select_Second_Week_Day_Locator = '#second_week_day';

    //------------------------------------------
    public static $payments_Dropdown_Menu = '.dropdown-toggle';
    public static $payment_Menu = '//div[3]/ul/li[2]/a';
    public static $payments_Submenu = '//div[3]/ul/li[2]//li[1]/a';
    public static $upload_Payments_Submenu ='//div[3]/ul/li[2]/ul/li[2]/a';
    public static $master_Payment_Template_Submenu = '//div[3]/ul/li[2]//li[3]/a';
    public static $create_Master_Payment_Template_Submenu ='//div[3]/ul/li[2]/ul/li[4]/a';

    public static $payments_Filter_Button = 'div.btn.btnFilter';
    public static $payments_Clear_Button = 'div.btn.btnClearFilter';

    public static $payments_Menu_Link = '/payments_payments';
    public static $upload_Payments_Menu_Link = '/file_index/edit/file_type/1';
    public static $master_Payment_Templates_Menu_Link = '/payments_setup';
    public static $create_Master_Payment_Template_Menu_Link = '/payments_setup/new';
    public static $master_Payment_Templates_Table = '#updatethis-Application_Model_Entity_Payments_Setup td';
    public static $create_New_Master_Payment_Template_Button = 'div.row.right a.btn.btn-success';
    public static $add_Payments_Button = 'div.row.table-controll a:nth-child(2)';


    public static $payments_Table_Header = '#updateheader-Application_Model_Entity_Payments_Payment > th';
    public static $payments_Template_Header_Popup ='#updateheader-Application_Model_Entity_Payments_Setup > th';

    public static $payment_Template_Tab_In_Popup = 'a[href="#setup-payment-setup"]';
    public static $select_Payment_template_In_Table = "#updatethis-Application_Model_Entity_Payments_Setup td";
    public static $contractor_Tab_In_Popup = 'a[href="#contractors-payment-setup"]';
    public static $select_Contractor_In_Table = "#updatethis-Application_Model_Entity_Entity_Contractor td";
    public static $add_Template_Button_In_Popup = 'div.payment-setup.setup.popup_checkbox_modal.modal.hide.fade.in > div.modal-footer > a';
}

<?php
namespace Codeception\Module;

use \AcceptanceTester;
use Codeception\Codecept;

class BaseSelectors extends \Codeception\Module
{
    public static $page_Header = 'h3';

    public static $logo = "img";
    public static $select_Customer_Button = '#current_carrier_name';
    public static $select_Customer_Popup = '#change_carrier_modal';
    public static $select_Customer_Table_In_Popup ='#change_carrier_modal > div.modal-body > table';
    public static $close_Button_For_Select_Customer_Popup = '#change_carrier_modal > div.modal-header > button';

    public static $selector_Filter_Contractor_Status = '#contractor_status option';
    public static $filter_Contractor_Status = array("All","Active","Terminated");

    public static $selector_Records='#rec_per_page option';
    public static $records=array(25,50,100, "All");
    public static $filter_Dropdown_Value_Original=array("Open", "Closed", "Archive");
    public static $filter_Dropdown_Values = '#settlement_cycle_filter_type option';
    public static $filter_Period_Dropdown_Locator = '#settlement_cycle_filter_type';
    public static $period_Dropdown_Values = 'select[id=settlement_cycle_id_filter]';

    //Frequency
    public static $frequency_Period=array("Biweekly", "Monthly", "Semi-Monthly", "Semi-Weekly", "Weekly");
    public static $number_Of_Week=array("1st", "2nd");
    public static $days_Of_Week=array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
    public static $days_Of_Month=array("1st", "2nd", "3rd","4th","5th","6th","7th","8th","9th","10th","11th","12th","13th","14th","15th","16th", "17th","18th","19th", "20th","21st","22nd","23rd","24th","25th","26th","27th","28th","29th","30th","Last");

    public static $frequency_Field = 'Frequency';
    public static $frequency_Dropdown_Locator = '#billing_cycle_id';
    public static $frequency_Values = '#billing_cycle_id option';
    public static $frequency_Biweekly = 'Biweekly';
    public static $start_Date_Field = 'Start Date *';
    public static $start_Date_Biweekly_Frequency = '#biweekly_start_day';
    public static $select_Days_Field = 'Select Days';
    public static $frequency_Monthly = 'Monthly';
    public static $select_days_Of_Month_Locator = "#first_start_day";
    public static $select_Days_Of_Month_Values = '#first_start_day option';
    public static $frequency_Semi_Monthly = 'Semi-Monthly';
    public static $select_Second_Days_Of_Month__Locator = "#second_start_day";
    public static $select_Second_Days_Of_Month_Values = '#second_start_day option';
    public static $frequency_Semi_Weekly = 'Semi-Weekly';
    public static $select_Days_Of_Week_Locator = '#week_day';
    public static $select_Days_Of_Week_Values = '#week_day option';
    public static $select_Second_Days_Of_Week_Locator = '#second_week_day';
    public static $select_Second_Days_Of_Week_Values = '#second_week_day option';
    public static $frequency_Weekly = 'Weekly';

    public static $bank_Account_Url = '/bankaccounts_index';

    public static $num_Of_Records = '#rec_per_page';
    public static $create_New_Button = 'div.row.table-controll a';
    public static $create_New_Vendor = 'div.row.table-controll div:nth-child(2) > a';
    public static $save_Button = '#submit';
    public static $current_Customer_Top_Menu = '#current_carrier_name';
    public static $table_Customer_Top_Menu = '#change_carrier_modal > div.modal-body > table > tbody td';

    public static $vendor_Table_In_Popup = '#Vendor > table > tbody td';
    public static $vendor_Tab_In_Popup = 'a[href="#Vendor"]';
    public static $customer_Table_In_Popup = '#Customer > table > tbody td';
    public static $customer_Tab_In_Popup = 'a[href="#Customer"]';


    public static $selector_Vendor_Table = '#updatethis-Application_Model_Entity_Entity_Vendor td';
    public static $selector_Contractor_Table = '#updatethis-Application_Model_Entity_Entity_Contractor td';



}
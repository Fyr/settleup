<?php
namespace Codeception\Module;
use Codeception\Module\Input_Data;
use \AcceptanceTester;

class Disbursement_Data
{
    public static $disbursements_Transactions_Table = '#updatethis-Application_Model_Entity_Transactions_Disbursement td';
    public static $disbursements_Recipient_Column = '#updatethis-Application_Model_Entity_Transactions_Disbursement td:nth-child(2):not([class])';
    public static $disbursements_Amount_Column = '#updatethis-Application_Model_Entity_Transactions_Disbursement td:nth-child(8)';
    public static $disbursements_Recipient_Sort_Button = '#updateheader-Application_Model_Entity_Transactions_Disbursement th:nth-child(2) span';
    public static $disbursements_Total = '#updatethis-Application_Model_Entity_Transactions_Disbursement > tr.totals > td.num';
    public static $disbursements_Settlement_Cycle_Header_Table = '.datagrid.table.table-bordered.additional-cycle-grid thead th';
    public static $disbursements_Header_Table = '#updateheader-Application_Model_Entity_Transactions_Disbursement > th';
    public static $disbursements_Settlement_Cycle_Header_Table_Original = array ("Settlement Cycle", "Period Start Date", "Period Close Date", "Processing Date", "Disbursement Date", "Disbursement Status", "Action");
    public static $disbursements_Header_Table_Original = array("ID", "Recipient", "Type", "Compensation Type", "Account Nickname", "Bank Account Number", "Disbursement Reference", "Amount", "Action");
    public static $disbursements_Menu = '//div[3]/ul/li[5]/a';
    public static $disbursement_Page_URL = '/transactions_disbursement';
    public static $disbursements = 'Disbursements';
    public static $filter_Name_Button = 'Filter';
    public static $filter_Button = '.btn.btnFilter';
    public static $clear_Name_Button = 'Clear';
    public static $clear_Button = '.btn.btnClearFilter';
    public static $Disbursement_Filter_Dropdown_Value_Original=array("Closed", "Archive");

    public static function disbursement_Amount_Array()
    {
        //create array of disbursement calculation
        $name= array(Input_Data::$contractor1, Input_Data::$contractor1, Input_Data::$vendor1, Input_Data::$vendor2, Input_Data::$customer);
        $amount=array("$347.50", "$347.50", "$50.00", "$550.00", "$300.00");
        for($i=0; $i<count($name); $i++)
        {
            $disbursements_Reconciliations[$i] = array($name[$i],$amount[$i]);
        }
        uasort($disbursements_Reconciliations, function ($v1, $v2)
        {
            if ($v1[1] == $v2[1]) return 0;
            return ($v1[1] < $v2[1])? -1: 1;
        });
        return $disbursements_Reconciliations;
    }
}
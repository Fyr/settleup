<?php
use Codeception\Module\Settlement_Data;
use Codeception\Module\BaseSelectors;
use Codeception\Module\Payments_Data;
use Codeception\Module\Deduction_Data;

class SettlementPage
{
    protected $user;

    public function __construct(AcceptanceTester $I)
    {
        $this->user = $I;
    }

    public function     addSettlement($cycle, $startDate, $processingDeadline, $disbursementTerms)
    {
        $this->user->click(Settlement_Data::$settlement_Menu);
        $this->user->click(Settlement_Data::$settlement_Create_New_Button);
        $this->user->selectOption(Settlement_Data::$settlement_Cycle_Period_ID, $cycle);
        $this->user->fillField(Settlement_Data::$settlement_Start_Date,$startDate);
        $this->user->pressKey(Settlement_Data::$settlement_Start_Date,WebDriverKeys::ESCAPE);
        $this->user->fillField(Settlement_Data::$settlement_Processing_Deadline, $processingDeadline);
        $this->user->fillField(Settlement_Data::$settlement_Disbursement_Terms,$disbursementTerms);

        $this->user->click(BaseSelectors::$save_Button);
        $this->user->click(BaseSelectors::$save_Button);
        $this->user->click(Settlement_Data::$settlement_Verify_Button); //click Verify button
    }

    public function addSemiSettlement($cycle, $startDate, $processingDeadline, $disbursementTerms, $startDay, $lastDay)
    {
        $Base = new BaseFunctionsPage($this->user);
        $Customer = new CustomersPage ($this->user);
        $this->user->click(Settlement_Data::$settlement_Menu);
        $this->user->click(Settlement_Data::$settlement_Create_New_Button);

        $this->user->selectOption(Settlement_Data::$settlement_Cycle_Period_ID, $cycle);
        $this->user->fillField(Settlement_Data::$settlement_Start_Date,$startDate);
        $this->user->pressKey(Settlement_Data::$settlement_Start_Date,WebDriverKeys::ESCAPE);
        $this->user->fillField(Settlement_Data::$settlement_Processing_Deadline, $processingDeadline);
        $this->user->fillField(Settlement_Data::$settlement_Disbursement_Terms,$disbursementTerms);
        if($cycle == 'Semi-Weekly'){
            $this->user->selectOption(Payments_Data::$select_First_Week_Day_Locator,$startDay);
            $this->user->selectOption(Payments_Data::$select_Second_Week_Day_Locator,$lastDay);
        }
        if($cycle == 'Semi-Monthly'){
            $this->user->selectOption(Payments_Data::$select_First_Days_Locator,$startDay);
            $this->user->selectOption(Payments_Data::$select_Second_Days_Locator,$lastDay);
        }
        $this->user->click(BaseSelectors::$save_Button);
        $this->user->click(BaseSelectors::$save_Button);
    }

    public function verifySettlementCycle ()
    {
        $Base = new BaseFunctionsPage($this->user);
        $this->user->click(Settlement_Data::$settlement_Menu);
        $this->user->selectOption('#settlement_cycle_filter_type', 'Open');
        if ($Base->grabTextFromElement(Settlement_Data::$settlement_Verify_Button)=="Verify")
        {
            $this->user->click(Settlement_Data::$settlement_Verify_Button); //click Verify button
        }

    }

    public function processSettlementCycle ()
    {
        $Base = new BaseFunctionsPage($this->user);
        $this->user->click(Settlement_Data::$settlement_Menu);
        if($Base->grabTextFromElement(Settlement_Data::$settlement_Process_Button) == "Process")
        {
            $this->user->click(Settlement_Data::$settlement_Process_Button);
            $this->user->waitForElementVisible('#confirm-settlement-process');
            $this->user->click('Yes', '#confirm-settlement-process');
        }
    }

    public function approveSettlementCycle ()
    {
        $this->user->click(Settlement_Data::$settlement_Menu);
        $this->processSettlementCycle();
        $this->user->click(Settlement_Data::$settlement_Approve_Button);
        $this->user->waitForElementVisible('#confirm-settlement-approve');
        $this->user->click('Yes', '#confirm-settlement-approve');
    }


    public function checkAmountsOfSettlement($settlement_Summary)
    {
        $Base = new BaseFunctionsPage($this->user);
        $this->user->click(Settlement_Data::$settlement_Menu);
        $this->user->click('.btnClearFilterContractors');
        $this->user->wait(1);
        $settlementTotal = $Base->getTextElement(Settlement_Data::$settlement_Total);
        $Base->checkArrayDiff($settlement_Summary, $settlementTotal);
    }

    public function editPD_DetailsToContractorSettlement($contractor, $table, array $template)
    {
        $Base = new BaseFunctionsPage($this->user);
        $this->user->click(Settlement_Data::$settlement_Menu);
        $this->user->fillField(Settlement_Data::$company_Filter_Field, $contractor);
        $this->user->click(Settlement_Data::$settlement_Filter_Button);
        $this->user->click(Settlement_Data::$edit_Contractor_Settlement_Button);
        $this->user->executeJS('$("'.$table.'").filter(function(){return !!$(this).text() && $(this).text().trim()=="'.$template["code"].'";}).parent().find("a.btn-primary i").trigger("click")');
        $this->user->fillField(\Codeception\Module\Deduction_Data::$quantity_Locator, $template['quantity']);
        $this->user->fillField(\Codeception\Module\Deduction_Data::$rate_Locator, $template['rate']);
        $visibleSaveButton=$this->user->executeJS("var value = $('". BaseSelectors::$save_Button."').is(':visible'); return value;");
        if($visibleSaveButton=='true'){
        $this->user->click(BaseSelectors::$save_Button);
        }
    }

    public function contractor_Reserve_Account_Adjustment($contractor, array $reserve_Account)
    {
        $Base = new BaseFunctionsPage($this->user);
        $this->user->click(Settlement_Data::$settlement_Menu);
        $this->user->fillField(Settlement_Data::$company_Filter_Field, $contractor);
        $this->user->click(Settlement_Data::$settlement_Filter_Button);
        $this->user->click(Settlement_Data::$edit_Contractor_Settlement_Button);
        $this->user->click(Settlement_Data::$adjustment_Button);
        $current_Balance = $this->user->executeJS("var value = $('".Settlement_Data::$contractor_Reserve_Account_Adjustment_Table."').filter(function(){return $(this).text()=='". $reserve_Account["code"]."';}).parent().find('td:nth-child(10)').text().trim(); return value;");
        $current_Balance=ltrim($current_Balance,"$"); //remove symbol '$'
        $Base->findTextClickElement(Settlement_Data::$contractor_Reserve_Account_Adjustment_Table, $reserve_Account["code"]);
        if($current_Balance == $reserve_Account["current_Balance"]){
            $this->user->click('.btn.cancel');
        }
        if($current_Balance > $reserve_Account["current_Balance"]){
            $this->user->selectOption(Settlement_Data::$adjustment_Type_Dropdown, 'Decrease');
            $this->user->fillField(Settlement_Data::$adjustment_Amount,($current_Balance)-$reserve_Account["current_Balance"]);
            $this->user->click(BaseSelectors::$save_Button);
        }
        if($current_Balance < $reserve_Account["current_Balance"]){
            $this->user->selectOption(Settlement_Data::$adjustment_Type_Dropdown, 'Increase');
            $this->user->fillField(Settlement_Data::$adjustment_Amount,($reserve_Account["current_Balance"]-$current_Balance));
            $this->user->click(BaseSelectors::$save_Button);
        }
    }

    public function checkRecurringWeekDays(array $recurring)
    {
        $dates =array();
        $Base = new BaseFunctionsPage($this->user);
        $Payment = new PaymentsPage($this->user);
        $Deduction = new DeductionsPage($this->user);
        $this->user->click(Settlement_Data::$settlement_Menu);
        $this->user->selectOption('#settlement_cycle_filter_type', 'Open');
        $start_date = $Base->grabTextFromElement("table.datagrid.table.table-bordered.additional-cycle-grid td:nth-child(2)");
        $end_date = $Base->grabTextFromElement("table.datagrid.table.table-bordered.additional-cycle-grid td:nth-child(3)");
        $end_date = strtotime($end_date);
        $day = 60 * 60 * 24;
        $n=1;
        for($i=0; $i<count($recurring['start_day']); $i++)
        {
            $date = strtotime($start_date);
            while (date('l', $date) !== $recurring['start_day'][$i]) $date += $day;
                while($date <= $end_date){
                $dates[$n-1]=date("m/d/Y",$date);
                $date= $date + (7* $day);
                $n++;
            }
        }
        if(count($dates)>0){
            $Payment->checkPaymentsRecurring($recurring, $dates);
            $Deduction->checkDeductionRecurring($recurring, $dates);
        }
    }

    public function checkRecurringDays(array $recurring)
    {
        $dates =array();
        $Base = new BaseFunctionsPage($this->user);
        $Payment = new PaymentsPage($this->user);
        $Deduction = new DeductionsPage($this->user);
        $this->user->click(Settlement_Data::$settlement_Menu);
        $this->user->selectOption('#settlement_cycle_filter_type', 'Open');
        $start_date = $Base->grabTextFromElement("table.datagrid.table.table-bordered.additional-cycle-grid td:nth-child(2)");
        $end_date = $Base->grabTextFromElement("table.datagrid.table.table-bordered.additional-cycle-grid td:nth-child(3)");
     //   print_r("\n". $end_date);
        $end_date = strtotime($end_date);
        $day = 60 * 60 * 24;
        $n=1;
        for($i=0; $i<count($recurring['start_day']); $i++)
        {
            $date = strtotime($start_date);
            while($date < $end_date){
                $date += $day;
           //     print_r("\n".date("m/d/Y",$date));
                if (date('j', $date) == substr($recurring['start_day'][$i],0,-2)) {
                    $dates[$n-1]=date("m/d/Y",$date);
                    $n++;
                }
            }
        }
        if(count($dates)>0){
            $Payment->checkPaymentsRecurring($recurring, $dates);
            $Deduction->checkDeductionRecurring($recurring, $dates);
      }
    }

    public function checkBiweeklyRecurring(array $recurring)
    {
        $dates =array();
        $Base = new BaseFunctionsPage($this->user);
        $Payment = new PaymentsPage($this->user);
        $Deduction = new DeductionsPage($this->user);
        $this->user->click(Settlement_Data::$settlement_Menu);
        $this->user->selectOption('#settlement_cycle_filter_type', 'Open');
        $start_date = $Base->grabTextFromElement("table.datagrid.table.table-bordered.additional-cycle-grid td:nth-child(2)");
        $end_date = $Base->grabTextFromElement("table.datagrid.table.table-bordered.additional-cycle-grid td:nth-child(3)");
        $end_date = strtotime($end_date);
        $day = 60 * 60 * 24;
        $n=0;
        $start_day = $recurring['start_day'];
        $date = strtotime($start_day);
        $date= $date + (14* $day);
        while($date <= $end_date){
            if($date >= strtotime($start_date))
            {
                $dates[$n]=date("m/d/Y",$date);
                $date= $date + (14* $day);
                $n++;
            }
            if($date < strtotime($start_date))
            {
                $date= $date + (14* $day);
            }
        }
        if(count($dates)>0){
            $Payment->checkPaymentsRecurring($recurring, $dates);
            $Deduction->checkDeductionRecurring($recurring, $dates);
        }
    }
}

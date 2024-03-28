<?php
use Codeception\Module\Deduction_Data;
use Codeception\Module\BaseSelectors;
use Codeception\Module\Settlement_Data;

class DeductionsPage
{
    use \Codeception\Util\Shared\Asserts;
    protected $user;

    public function __construct(AcceptanceTester $I)
    {
        $this->user = $I;
    }

    public function deductionTemplateCreate($provide, $deduction_code, $description, $category, $quantity, $rate)
    {
        $Base = new BaseFunctionsPage($this->user);
        $this->user->click(Deduction_Data::$deductions_Menu);
        $this->user->wait(1);
        $this->user->click(Deduction_Data::$master_Deduction_Templates_Submenu);
        if($Base->checkForItem(Deduction_Data::$master_Deduction_Templates_Table,$deduction_code))
        {
            $this->user->click(Deduction_Data::$create_New_Deduction_Template_button);
            $this->user->seeInCurrentUrl(Deduction_Data::$create_Master_Deduction_Templates_Menu_Link);
            $this->user->click(Deduction_Data::$vendor_Locator);
            $this->user->waitForElementVisible('#provider_id_modal');
            if($Base->checkForItem(Deduction_Data::$customers_Table_In_Popup,$provide)==false)
            {
                $this->user->click(Deduction_Data::$customers_Tab_In_Popup);
                $Base->findTextClickElement(Deduction_Data::$customers_Table_In_Popup,$provide);
            }
            else
            {
                $this->user->click(Deduction_Data::$vendor_Tab_In_Popup);
                $Base->findTextClickElement(Deduction_Data::$vendor_Table_In_Popup,$provide);
            }
            $this->user->fillField(Deduction_Data::$deduction_Code,$deduction_code);
            $this->user->fillField(Deduction_Data::$description,$description);
            $this->user->fillField(Deduction_Data::$category,$category);
            $this->user->fillField(Deduction_Data::$quantity, $quantity);
            $this->user->fillField(Deduction_Data::$rate, $rate);
            $this->user->click(BaseSelectors::$save_Button); //click 'Save' button)
        }
    }

    public function addRecurringDeductionTemplate($provide, array $recurring,$quantity, $rate)
    {
        $Base = new BaseFunctionsPage($this->user);
        $this->user->click(Deduction_Data::$deductions_Menu);
        $this->user->wait(1);
        $this->user->click(Deduction_Data::$master_Deduction_Templates_Submenu);
        if($Base->checkForItem(Deduction_Data::$master_Deduction_Templates_Table,$recurring['code']))
        {
            $this->user->click(Deduction_Data::$create_New_Deduction_Template_button);
            $this->user->seeInCurrentUrl(Deduction_Data::$create_Master_Deduction_Templates_Menu_Link);
            $this->user->click(Deduction_Data::$vendor_Locator);
            $this->user->waitForElementVisible('#provider_id_modal');
            if($Base->checkForItem(Deduction_Data::$customers_Table_In_Popup,$provide)==false)
            {
                $this->user->click(Deduction_Data::$customers_Tab_In_Popup);
                $Base->findTextClickElement(Deduction_Data::$customers_Table_In_Popup,$provide);
            }
            else
            {
                $this->user->click(Deduction_Data::$vendor_Tab_In_Popup);
                $Base->findTextClickElement(Deduction_Data::$vendor_Table_In_Popup,$provide);
            }
            $this->user->fillField(Deduction_Data::$deduction_Code,$recurring['code']);
            $this->user->fillField(Deduction_Data::$description,$recurring['description']);
            $this->user->fillField(Deduction_Data::$quantity, $quantity);
            $this->user->fillField(Deduction_Data::$rate, $rate);
            $this->user->checkOption(Deduction_Data::$recurring_Locator);
            $this->user->selectOption(Deduction_Data::$frequency_Locator, $recurring['frequency']);
            if($recurring['frequency'] == 'Weekly'){
                $this->user->selectOption(Deduction_Data::$select_First_Week_Day_Locator,$recurring['start_day'][0]);
            }
            if($recurring['frequency'] == 'Semi-Weekly'){
                $this->user->selectOption(Deduction_Data::$select_First_Week_Day_Locator,$recurring['start_day'][0]);
                $this->user->selectOption(Deduction_Data::$select_Second_Week_Day_Locator,$recurring['start_day'][1]);
            }
            if($recurring['frequency'] == 'Semi-Monthly'){
                $this->user->selectOption(Deduction_Data::$select_First_Days_Locator,$recurring['start_day'][0]);
                $this->user->selectOption(Deduction_Data::$select_Second_Days_Locator,$recurring['start_day'][1]);
            }
            if($recurring['frequency'] == 'Monthly'){
                $this->user->selectOption(Deduction_Data::$select_First_Days_Locator,$recurring['start_day'][0]);
            }
            if($recurring['frequency'] == 'Biweekly'){
                $this->user->fillField(Deduction_Data::$biweekly_Start_Date_Locator,$recurring['start_day']);
            }
            $this->user->click(BaseSelectors::$save_Button); //click 'Save' button)
        }
    }

    public function addDeduction($contractorID)
    {
        $this->user->click(Deduction_Data::$deductions_Menu); //click on Deductions menu
        $this->user->wait(1);
        $this->user->click(Deduction_Data::$deduction_Submenu); // click on Deductions submenu
        $this->user->click(Deduction_Data::$add_Deduction_Button);
        $this->user->wait(2);
        $this->user->click(Deduction_Data::$deduction_Template_Tab_In_Popup);
        $this->user->click('#updateheader-Application_Model_Entity_Deductions_Setup input');
        //$this->user->executeJS('$("'.Deduction_Data::$select_Deduction_template_In_Table.'").filter(function(){return $(this).text()=="'.$codeTemplate.'";}).parent().find(".checkboxField > input").trigger("click")');
        $this->user->click(Deduction_Data::$contractor_Tab_In_Popup);
        if($contractorID=='All')
        {
            $this->user->click('#updateheader-Application_Model_Entity_Entity_Contractor input');
        }
        else{
        $this->user->executeJS('$("'.Deduction_Data::$select_Contractor_In_Table.'").filter(function(){return $(this).text()=="'.$contractorID.'";}).parent().find(".checkboxField > input").trigger("click")');
        }
        $this->user->click(Deduction_Data::$add_Template_Button_In_Popup);
    }

    public function editDeductionDetails($contractor, array $deduction)
    {
        $this->user->click(Deduction_Data::$deductions_Menu); //click on Deductions menu
        $this->user->wait(1);
        $this->user->click(Deduction_Data::$deduction_Submenu); // click on Deductions submenu
        $this->user->fillField(Deduction_Data::$company_Filter_Field, $contractor);
        $this->user->fillField(Deduction_Data::$deduction_Code_Filter_Field, $deduction['code']);
        $this->user->click(Deduction_Data::$deductions_Filter_Button);
        $this->user->wait(1);
        $this->user->click(Deduction_Data::$deduction_Edit_Button);
        $this->user->fillField(Deduction_Data::$quantity_Locator, $deduction['quantity']);
        $this->user->fillField(Deduction_Data::$rate_Locator, $deduction['rate']);
        $visibleSaveButton=$this->user->executeJS("var value = $('". BaseSelectors::$save_Button."').is(':visible'); return value;");
        if($visibleSaveButton=='true'){
            $this->user->click(BaseSelectors::$save_Button);
        }
    }


    public function addAdjustedBalance ($contractor, array $adjustedBalance)
    {
        $this->user->click(Settlement_Data::$settlement_Menu);
        $this->user->fillField(Settlement_Data::$company_Filter_Field, $contractor);
        $this->user->click(Settlement_Data::$settlement_Filter_Button);
        $this->user->click(Settlement_Data::$edit_Contractor_Settlement_Button);
        for($i=0; $i<count($adjustedBalance); $i++){
        $this->user->executeJS('$("'.Settlement_Data::$deductions_Table.'").filter(function(){return $(this).text()=="'.$adjustedBalance[$i]["code"].'";}).parent().find("a.btn-primary i").trigger("click")');
            $this->user->waitForElementVisible(Deduction_Data::$adjusted_Balance_Locator);
        $this->user->fillField(Deduction_Data::$adjusted_Balance_Locator, $adjustedBalance[$i]['adjusted_Balance']);
        $this->user->click(BaseSelectors::$save_Button);
        }
    }


    public function addEligibleReserveAccount($codeDeduction, $codeRA)
    {
        $Base = new BaseFunctionsPage($this->user);
        $this->user->click(Deduction_Data::$deductions_Menu); //click on Deductions menu
        $this->user->wait(1);
        $this->user->click(Deduction_Data::$master_Deduction_Templates_Submenu);
        $this->user->fillField(Deduction_Data::$deduction_Code_Filter_Field, $codeDeduction);
        $this->user->click(Deduction_Data::$deductions_Filter_Button);
        $this->user->wait(1);
        $this->user->click(Deduction_Data::$deduction_Edit_Button);
        $eligibleIsNotChecked=$this->user->executeJS("var value = $('".Deduction_Data::$reserve_Account_Field_locator .":visible').length; return value;");
        if($eligibleIsNotChecked == '0'){
        $this->user->click(Deduction_Data::$eligible_Locator);
        $Base->findTextClickElement(Deduction_Data::$select_Reserve_Account_Table_In_Popup, $codeRA);
        $this->user->click(BaseSelectors::$save_Button);
        }
    }

    public function checkDeductionRecurring(array $recurring, $dates){
        //check existing invoice dates for the deductions
        $this->user->click(Settlement_Data::$settlement_Menu);
        $this->user->click(Settlement_Data::$edit_Contractor_Settlement_Button);
        $this->user->waitForElementVisible(Settlement_Data::$deductions_Table);
        for($i=1; $i<=count($dates); $i++){
            $this->user->executeJS('$("'.Settlement_Data::$deductions_Table.'").filter(function(){return $(this).text()=="'.$recurring["code"].'";}).filter("::eq('.($i-1).')").parent().find("a.btn-primary i").trigger("click")');
            $this->user->waitForElementVisible('#invoice_date');
            $invoice_date = $this->user->executeJS("var optionValues = $('#invoice_date').val();  return optionValues;");
            $this->user->moveBack();
            if (!in_array($invoice_date, $dates)) {
                $this->fail('This invoice date is not exist ' . $invoice_date . ' in (' . implode(',', $dates) . ')');
            }
        }
    }
}

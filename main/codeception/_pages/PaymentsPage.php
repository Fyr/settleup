<?php
use Codeception\Module\Payments_Data;
use Codeception\Module\BaseSelectors;
use Codeception\Module\Settlement_Data;


class PaymentsPage
{
    use \Codeception\Util\Shared\Asserts;
    protected $user;

    public function __construct(AcceptanceTester $I)
    {
        $this->user = $I;
    }

    public function paymentTemplateCreate($payment_code, $carrier_payment_code, $description, $category, $quantity, $rate)
    {
        $Base = new BaseFunctionsPage($this->user);
        $this->user->click(Payments_Data::$payment_Menu); //click on Payments menu
        $this->user->wait(1);
        $this->user->click(Payments_Data::$master_Payment_Template_Submenu); // click on Master Payment Template submenu
        if($Base->checkForItem(Payments_Data::$master_Payment_Templates_Table,$payment_code))
        {
            $this->user->click(Payments_Data::$create_New_Master_Payment_Template_Button);
            $this->user->seeInCurrentUrl(Payments_Data::$create_Master_Payment_Template_URL);
            $this->user->fillField(Payments_Data::$payment_Code,$payment_code);
            $this->user->fillField(Payments_Data::$customer_Payment_Code,$carrier_payment_code);
            $this->user->fillField(Payments_Data::$description,$description);
            $this->user->fillField(Payments_Data::$category,$category);
            $this->user->fillField(Payments_Data::$quantity, $quantity);
            $this->user->fillField(Payments_Data::$rate, $rate);
            $this->user->click(BaseSelectors::$save_Button); //click 'Save' button)
        }
    }

    public function addRecurringPaymentTemplate(array $recurring,$quantity, $rate)
    {
        $Base = new BaseFunctionsPage($this->user);
        $this->user->click(Payments_Data::$payment_Menu); //click on Payments menu
        $this->user->wait(1);
        $this->user->click(Payments_Data::$master_Payment_Template_Submenu); // click on Master Payment Template submenu
        if($Base->checkForItem(Payments_Data::$master_Payment_Templates_Table,$recurring['code']))
        {
            $this->user->click(Payments_Data::$create_New_Master_Payment_Template_Button);
            $this->user->fillField(Payments_Data::$payment_Code,$recurring['code']);
            $this->user->fillField(Payments_Data::$description,$recurring['description']);
            $this->user->fillField(Payments_Data::$quantity, $quantity);
            $this->user->fillField(Payments_Data::$rate, $rate);
            $this->user->checkOption(Payments_Data::$recurring_Locator);
            $this->user->selectOption(Payments_Data::$frequency_Locator, $recurring['frequency']);
            if($recurring['frequency'] == 'Weekly'){
                $this->user->selectOption(Payments_Data::$select_First_Week_Day_Locator,$recurring['start_day'][0]);
            }
            if($recurring['frequency'] == 'Semi-Weekly'){
                $this->user->selectOption(Payments_Data::$select_First_Week_Day_Locator,$recurring['start_day'][0]);
                $this->user->selectOption(Payments_Data::$select_Second_Week_Day_Locator,$recurring['start_day'][1]);
            }
            if($recurring['frequency'] == 'Semi-Monthly'){
                $this->user->selectOption(Payments_Data::$select_First_Days_Locator,$recurring['start_day'][0]);
                $this->user->selectOption(Payments_Data::$select_Second_Days_Locator,$recurring['start_day'][1]);
            }
            if($recurring['frequency'] == 'Monthly'){
                $this->user->selectOption(Payments_Data::$select_First_Days_Locator,$recurring['start_day'][0]);
            }
            if($recurring['frequency'] == 'Biweekly'){
                $this->user->fillField(Payments_Data::$biweekly_Start_Date_Locator,$recurring['start_day']);
            }
            $this->user->click(BaseSelectors::$save_Button); //click 'Save' button)
        }
    }

    public function addPayment($contractorID)
    {
        $this->user->click(Payments_Data::$payment_Menu); //click on Payments menu
        $this->user->wait(1);
        $this->user->click(Payments_Data::$payments_Submenu); // click on Payments submenu
        $this->user->click(Payments_Data::$add_Payments_Button);
        $this->user->wait(1);
        $this->user->click(Payments_Data::$payment_Template_Tab_In_Popup);
        $this->user->click('#updateheader-Application_Model_Entity_Payments_Setup input');
        //$this->user->executeJS('$("'.Payments_Data::$select_Payment_template_In_Table.'").filter(function(){return $(this).text()=="'.$codeTemplate.'";}).parent().find(".checkboxField > input").trigger("click")');
        $this->user->click(Payments_Data::$contractor_Tab_In_Popup);
        if($contractorID=='All')
        {
            $this->user->click('#updateheader-Application_Model_Entity_Entity_Contractor input');
        }
        else{
            $this->user->executeJS('$("'.Payments_Data::$select_Contractor_In_Table.'").filter(function(){return $(this).text()=="'.$contractorID.'";}).parent().find(".checkboxField > input").trigger("click")');
        }
        $this->user->wait(3);
        $this->user->click(Payments_Data::$add_Template_Button_In_Popup);
    }

    public function editPaymentDetails($contractor, array $payment)
    {
        $this->user->click(Payments_Data::$payment_Menu); //click on Payments menu
        $this->user->wait(1);
        $this->user->click(Payments_Data::$payments_Submenu); // click on Payments submenu
        $this->user->fillField(Payments_Data::$company_Filter_Field, $contractor);
        $this->user->fillField(Payments_Data::$payment_Code_Filter_Field, $payment['code']);
        $this->user->click(Payments_Data::$payments_Filter_Button);
        $this->user->wait(1);
        $this->user->click(Payments_Data::$payment_Edit_Button);
        $this->user->fillField(Payments_Data::$quantity_Locator, $payment['quantity']);
        $this->user->fillField(Payments_Data::$rate_Locator, $payment['rate']);
        $visibleSaveButton=$this->user->executeJS("var value = $('". BaseSelectors::$save_Button."').is(':visible'); return value;");
        if($visibleSaveButton=='true'){
            $this->user->click(BaseSelectors::$save_Button);
        }
    }

/*    public function checkPaymentsRecurring(array $recurring, $dates){
    //check existing invoice dates for the payments
        $this->user->click(Payments_Data::$payment_Menu); //click on Payments menu
        $this->user->wait(1);
        $this->user->click(Payments_Data::$payments_Submenu); // click on Payments submenu
        $this->user->fillField(Payments_Data::$payment_Code_Filter_Field, $recurring['code']);
        $this->user->click(Payments_Data::$payments_Filter_Button);
        $isExit = true;
        for($i=1; $i<=count($dates); $i++){
            $this->user->wait(1);
            $this->user->click(Payments_Data::$payments_Table.' tr:nth-child('.$i.') '.Payments_Data::$payment_Edit_Button);
            $invoice_date = $this->user->executeJS("var optionValues = $('#invoice_date').val();  return optionValues;");
            $code = $this->user->executeJS("var optionValues = $('#description').val();  return optionValues;");
            $this->user->moveBack();
            if($isExit==false){exit;}
            foreach ($dates as $d){
                if($invoice_date==$d){
                    $isExit=true;
                    break;
                }
                else{
                    $isExit=false;
                    echo "\n This invoice date is not exist".$invoice_date." with ". $d;
                    echo "\n description =".$code;
                }
            }
        }

    }*/


    public function checkPaymentsRecurring(array $recurring, $dates){
    //check existing invoice dates for the payments
        $this->user->click(Settlement_Data::$settlement_Menu);
        $this->user->click(Settlement_Data::$edit_Contractor_Settlement_Button);
        $this->user->waitForElementVisible(Settlement_Data::$payments_Table);
        for($i=1; $i<=count($dates); $i++){
            $this->user->executeJS('$("'.Settlement_Data::$payments_Table.'").filter(function(){return $(this).text()=="'.$recurring["code"].'";}).filter("::eq('.($i-1).')").parent().find("a.btn-primary i").trigger("click")');
            $this->user->waitForElementVisible('#invoice_date');
            $invoice_date = $this->user->executeJS("var optionValues = $('#invoice_date').val();  return optionValues;");
            $this->user->moveBack();
            if (!in_array($invoice_date, $dates)) {
                $this->fail('This invoice date is not exist ' . $invoice_date . ' in (' . implode(',', $dates) . ')');
            }
        }
    }
}
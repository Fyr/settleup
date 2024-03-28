<?php
use Codeception\Module\Input_Data;
use Codeception\Module\BaseSelectors;
use Codeception\Module\Contractors_Data;

class ContractorsPage
{
    protected $user;

    public function __construct(AcceptanceTester $I)
    {
        $this->user = $I;
    }

    public function contractorBankAccountCreate($contractor, $account_nickname, $payment_type, $limit_type, $amount, $bank_routing_id, $bank_account_id)
    {
        $Base = new BaseFunctionsPage($this->user);
        $this->user->click(Contractors_Data::$contractors_Menu);
        $this->user->fillField(Contractors_Data::$contractor_Company_Name_Field,$contractor);
        $this->user->click(Contractors_Data::$filter_Button);
        $this->user->wait(3);
        $this->user->click(Contractors_Data::$edit_Contractor_Button);
        $this->user->waitForElementVisible(Contractors_Data::$contractor_Bank_Account_Button);
        $this->user->click(Contractors_Data::$contractor_Bank_Account_Button);
        $this->user->seeInCurrentUrl(BaseSelectors::$bank_Account_Url);
        if($Base->checkForItem(Contractors_Data::$contractor_BA_Table,$account_nickname))
        {
            $this->user->click(Contractors_Data::$contractor_Create_New_BA_Button);// click on '+ Create New' button
            $this->user->seeInCurrentUrl(Contractors_Data::$create_Contractor_BA_URL);
            $this->user->fillField(Contractors_Data::$contractor_Account_Nickname,$account_nickname);
            $this->user->selectOption(Contractors_Data::$contractor_Payment_Type, $payment_type);
            $this->user->selectOption(Contractors_Data::$contractor_Limit_Type, $limit_type);
            if($limit_type=='Percentage')
            {
                $this->user->fillField(Contractors_Data::$contractor_Percentage_Limit_Value, $amount);
            }
            else $this->user->fillField(Contractors_Data::$contractor_Amount_Limit_Value, $amount);
            if($payment_type=='Check')
            {
                $this->user->fillField(Contractors_Data::$contractor_Check_Message_1, $bank_routing_id);
                $this->user->fillField(Contractors_Data::$contractor_Check_Message_2, $bank_account_id);
            }
            else
            {
                $this->user->fillField(Contractors_Data::$contractor_ACH_Bank_Routing_ID, $bank_routing_id);
                $this->user->fillField(Contractors_Data::$contractor_ACH_Bank_Account_ID, $bank_account_id);
            }
            $this->user->click(BaseSelectors::$save_Button); //click 'Save' button
        }
    }

    public function contractorCreate(array $contractor)
    {
        $Base = new BaseFunctionsPage($this->user);
        $this->user->click(Contractors_Data::$contractors_Menu);
        $this->user->selectOption(BaseSelectors::$num_Of_Records,'All');
        // create new contractor if it's not exist
        if($Base->checkForItem(Contractors_Data::$selector_Contractor_Table,$contractor['Contractor_Name']))
        {
            $this->user->seeInCurrentUrl(Contractors_Data::$contractor_URL);
            $this->user->click(Contractors_Data::$create_New_Contractor_Button); // click on '+ Create New' button
            $this->user->seeInCurrentUrl(Contractors_Data::$contractor_New_URL);
            $this->user->fillField(Contractors_Data::$contractor_ID_Field,$contractor['ID']);
            $this->user->fillField(Contractors_Data::$contractor_Company_Name_Field,$contractor['Contractor_Name']);
            $this->user->fillField(Contractors_Data::$contractor_First_Name_Field,$contractor['First_Name']);
            $this->user->fillField(Contractors_Data::$contractor_Last_Name_Field,$contractor['Last_Name']);
            $this->user->fillField(Contractors_Data::$contractor_Tax_ID_Field,$contractor['Tax_ID']);
            $this->user->fillField(Contractors_Data::$contractor_Social_Security_ID_Field, $contractor['Social_Security_ID']);
            $this->user->click(BaseSelectors::$save_Button); //click 'Save' button
            // add a Bank account for current contractor
            for($i=0; $i<count($contractor['Bank_Accounts']); $i++){
                $this->contractorBankAccountCreate($contractor['Contractor_Name'], $contractor['Bank_Accounts'][$i]['Account_Nickname'], $contractor['Bank_Accounts'][$i]['Payment_Type'], $contractor['Bank_Accounts'][$i]['Limit_Type'], $contractor['Bank_Accounts'][$i]['Amount'], $contractor['Bank_Accounts'][$i]['Bank_Routing_ID'], $contractor['Bank_Accounts'][$i]['Bank_Account_ID']);
            }
            // add a vendors to the current carrier
      //      $this->addVendorForContractor($contractor['Contractor_Name']);
            //$this->addVendorForContractor($contractor['Contractor_Name'], Input_Data::$vendor2);
        }
    }

    public function addVendorForContractor($contractor)
    {
        $this->user->click(Contractors_Data::$contractors_Menu);
        $this->user->fillField(Contractors_Data::$contractor_Company_Name_Field,$contractor);
        $this->user->click(Contractors_Data::$filter_Button);
        $this->user->wait(1);
        $this->user->click(Contractors_Data::$edit_Contractor_Button);
        $this->user->waitForElementVisible(Contractors_Data::$contractor_Bank_Account_Button);
        $this->user->click(Contractors_Data::$contractor_Add_Vendor);//click on '+' button of Vendor fields
        //$this->user->executeJS('$("div.vendor-fields.span > div.control-group select:not([readonly]").find("option:contains('.$codeVendor.')").attr("selected", "selected");');
        $this->user->selectOption('#vendor-1000-vendor_id', Input_Data::$vendor1);
        $this->user->click("#submit"); //click 'Save' button
        $this->user->click(Contractors_Data::$contractors_Menu);
        $this->user->click(Contractors_Data::$edit_Contractor_Button);
        $this->user->waitForElementVisible(Contractors_Data::$contractor_Bank_Account_Button);
        $this->user->click(Contractors_Data::$contractor_Add_Vendor);//click on '+' button of Vendor fields
        $this->user->selectOption('[name="vendor[1006][vendor_id]"]', Input_Data::$vendor2);
        $this->user->click("#submit"); //click 'Save' button
        //$this->user->executeJS('$("#submit").click()'); //click 'Save' button
    }

    public function editIndividualPaymentTemplate ($contractor, array $payment)
    {
        $this->user->click(Contractors_Data::$contractors_Menu);
        $this->user->fillField(Contractors_Data::$contractor_Company_Name_Field, $contractor);
        $this->user->click(Contractors_Data::$filter_Button);
        $this->user->wait(1);
        $this->user->click(Contractors_Data::$edit_Contractor_Button);
        $this->user->click(Contractors_Data::$payment_Template_Button);
        $this->user->fillField(Contractors_Data::$payment_Code_Filter_Field, $payment['code']);
        $this->user->click(Contractors_Data::$filter_Button);
        $this->user->wait(1);
        $this->user->click(Contractors_Data::$edit_Payment_Template_Button);
        $this->user->fillField(Contractors_Data::$quantity_Locator, $payment['quantity']);
        $this->user->fillField(Contractors_Data::$rate_Locator, $payment['rate']);
        $visibleSaveButton=$this->user->executeJS("var value = $('". BaseSelectors::$save_Button."').is(':visible'); return value;");
        if($visibleSaveButton=='true'){
            $this->user->click(BaseSelectors::$save_Button);
        }
    }

    public function editIndividualDeductionTemplate ($contractor, array $deduction)
    {
        $this->user->click(Contractors_Data::$contractors_Menu);
        $this->user->fillField(Contractors_Data::$contractor_Company_Name_Field, $contractor);
        $this->user->click(Contractors_Data::$filter_Button);
        $this->user->wait(1);
        $this->user->click(Contractors_Data::$edit_Contractor_Button);
        $this->user->click(Contractors_Data::$deduction_Template_Button);
        $this->user->executeJS('$("'.\Codeception\Module\Deduction_Data::$master_Deduction_Templates_Table.'").filter(function(){return !!$(this).text() && $(this).text().trim()=="'.$deduction["code"].'";}).parent().find("a.btn-primary i").trigger("click")');
        $this->user->wait(1);
        $this->user->fillField(Contractors_Data::$quantity_Locator, $deduction['quantity']);
        $this->user->fillField(Contractors_Data::$rate_Locator, $deduction['rate']);
        $visibleSaveButton=$this->user->executeJS("var value = $('". BaseSelectors::$save_Button."').is(':visible'); return value;");
        if($visibleSaveButton=='true'){
            $this->user->click(BaseSelectors::$save_Button);
        }
    }
}
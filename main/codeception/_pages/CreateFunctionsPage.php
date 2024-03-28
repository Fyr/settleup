<?php
use Codeception\Module\Vendors_Data;
use Codeception\Module\Contractors_Data;
use Codeception\Module\BaseSelectors;



class CreateFunctionsPage
{
    protected $user;

    public function __construct(AcceptanceTester $I)
    {
        $this->user = $I;
    }

    public function vendorCreate($id, $vendor, $contractor, $tax_id, $account_nickname, $bank_routing_id, $bank_account_id)
    {
        $this->user->seeInCurrentUrl(Vendors_data::$vendor_Url);
        $this->user->click(Vendors_data::$create_New_Vendor_Button); // click on '+ Create New' button
        $this->user->seeInCurrentUrl(Vendors_data::$create_Vendor_Page_URL);
        $this->user->fillField(Vendors_data::$vendor_ID_Field,$id);
        $this->user->fillField(Vendors_data::$vendor_Name_Field,$vendor);
        $this->user->fillField(Vendors_data::$vendor_Contact_Field,$contractor);
        $this->user->fillField(Vendors_data::$vendor_Federal_Tax_ID_Field,$tax_id);
        $this->user->click('#submit'); //click 'Save' button
        $this->user->seeInCurrentUrl(Vendors_data::$vendor_Bank_Account_URL);
        $this->user->fillField(Vendors_data::$vendor_Account_Nickname,$account_nickname);
        $this->user->fillField(Vendors_data::$vendor_Bank_Routing_Number, $bank_routing_id);
        $this->user->fillField(Vendors_data::$vendor_Bank_Account_Number, $bank_account_id);
        $this->user->click('#submit'); //click 'Save' button
    }

    public function contractorCreate($id, $company_name, $first_name, $last_name, $tax_id, $social_security_id)//, $account_nickname, $bank_routing_id, $bank_account_id)
    {
        $this->user->seeInCurrentUrl(Contractors_Data::$contractor_URL);
        $this->user->click(Contractors_Data::$create_New_Contractor_Button); // click on '+ Create New' button
        $this->user->seeInCurrentUrl(Contractors_Data::$contractor_New_URL);
        $this->user->fillField(Contractors_Data::$contractor_ID_Field,$id);
        $this->user->fillField(Contractors_Data::$contractor_Company_Name_Field,$company_name);
        $this->user->fillField(Contractors_Data::$contractor_First_Name_Field,$first_name);
        $this->user->fillField(Contractors_Data::$contractor_Last_Name_Field,$last_name);
        $this->user->fillField(Contractors_Data::$contractor_Tax_ID_Field, $tax_id);
        $this->user->fillField(Contractors_Data::$contractor_Social_Security_ID_Field, $social_security_id);
        $this->user->click('#submit'); //click 'Save' button
    }

    public function addVendorForContractor($contractor, $codeVendor)
    {
        $this->user->click(Contractors_Data::$contractors_Menu); //click on Contractors menu
        //$this->user->selectOption('#rec_per_page','All');
        $this->user->fillField(Contractors_Data::$contractor_Company_Name_Field,$contractor);
        $this->user->click(Contractors_Data::$filter_Button);
        $this->user->click(Contractors_Data::$edit_Contractor_Button);
        $this->user->wait(1);
        $this->user->click(Contractors_Data::$contractor_Add_Vendor); //click on '+' button of Vendor fields
        $this->user->executeJS('$("div.vendor-fields.span > div.control-group select:not([readonly]").find("option:contains('.$codeVendor.')").attr("selected", "selected");');
        //$this->user->click('#submit');
        $this->user->executeJS('$("#submit").click()'); //click 'Save' button
    }

    public function contractorBankAccountCreate($account_nickname, $payment_type, $limit_type, $amount, $bank_routing_id, $bank_account_id)
    {
        $this->user->seeInCurrentUrl('/bankaccounts_index');
        $this->user->fillField('#account_nickname',$account_nickname);
        $this->user->selectOption('#payment_type', $payment_type);
        $this->user->selectOption('#limit_type', $limit_type);
        if($limit_type=='Percentage')
        {
            $this->user->fillField('#percentage', $amount);
        }
        else $this->user->fillField('#amount', $amount);
        if($payment_type=='Check')
        {
            $this->user->fillField('#check_message', $bank_routing_id);
            $this->user->fillField('#check_message_2', $bank_account_id);
        }
        else
        {
            $this->user->fillField('#ACH_bank_routing_id', $bank_routing_id);
            $this->user->fillField('#ACH_bank_account_id', $bank_account_id);
        }
        $this->user->click('#submit'); //click 'Save' button
    }

    public function carrierCreate($tax_id, $name, $contact, $terms, $account_nickname, $bank_routing_id, $bank_account_id, $escrow_account_holder, $holder_federal_tax_id, $bank_name, $bank_routing_number, $bank_account_number)
    {
    //    $this->user->click('//div[3]/ul/li[6]/a'); //click on Carriers menu
     //   $this->user->click('//div[3]/ul/li[6]/ul/li[1]/a'); // click on Carriers submenu
        $this->user->seeInCurrentUrl('/carriers_index');
        $this->user->click('div.row.table-controll a'); //click '+Create New' button
        $this->user->fillField('#tax_id',$tax_id);
        $this->user->fillField('#name',$name);
        $this->user->fillField('#contact',$contact);
        $this->user->fillField('#terms',$terms);
        $this->user->click('#submit'); //click 'Save' button
        $this->user->seeInCurrentUrl('/bankaccounts_index');
        $this->user->fillField('#account_nickname',$account_nickname);
        $this->user->fillField('#ACH_bank_routing_id', $bank_routing_id);
        $this->user->fillField('#ACH_bank_account_id', $bank_account_id);
        $this->user->click('#submit'); //click 'Save' button
        $this->user->seeInCurrentUrl('/carriers_index/escrow/');
        $this->user->fillField('#escrow_account_holder',$escrow_account_holder);
        $this->user->fillField('#holder_federal_tax_id', $holder_federal_tax_id);
        $this->user->fillField('#bank_name', $bank_name);
        $this->user->fillField('#bank_routing_number', $bank_routing_number);
        $this->user->fillField('#bank_account_number', $bank_account_number);
        $this->user->click('#submit'); //click 'Save' button
    }

    public function paymentTemplateCreate($payment_code, $carrier_payment_code, $description, $category, $quantity, $rate)
    {
        $M = new BaseFunctionsPage($this->user);
        $this->user->click('//div[3]/ul/li[2]/a'); //click on Compensation menu
        $this->user->click('//div[3]/ul/li[2]//li[3]/a'); // click on Master Payment Template submenu
        if($M->checkForItem('#updatethis-Application_Model_Entity_Payments_Setup td',$payment_code))
        {
            $this->user->click('//div[3]/ul/li[2]/a'); //click on Compensation menu
            $this->user->click('//div[3]/ul/li[2]//li[4]/a'); // click on Create Master Payment Template submenu
            $this->user->seeInCurrentUrl('/payments_setup/new');
            $this->user->fillField('#payment_code',$payment_code);
            $this->user->fillField('#carrier_payment_code',$carrier_payment_code);
            $this->user->fillField('#description',$description);
            $this->user->fillField('#category',$category);
            $this->user->fillField('#quantity', $quantity);
            $this->user->fillField('#rate', $rate);
            $this->user->click('#submit'); //click 'Save' button)

        }
    }

    public function deductionTemplateCreate($provide, $deduction_code, $description, $category, $quantity, $rate)
    {
        $M = new BaseFunctionsPage($this->user);
        $this->user->click('//div[3]/ul/li[3]/a'); //click on Deductions menu
        $this->user->click('//li[3]/ul/li[3]/a');
        if($M->checkForItem('#updatethis-Application_Model_Entity_Deductions_Setup td',$deduction_code))
        {
            $this->user->click('//div[3]/ul/li[3]/a'); //click on Deductions menu
            $this->user->click('//li[3]/ul/li[4]/a');
            $this->user->seeInCurrentUrl('deductions_setup/new');
            $this->user->click('#provider_id_title');
            if($M->checkForItem('#Carriers > table > tbody td',$provide)==false)
            {
                $this->user->click('a[href="#Carriers"]');
                $M->findTextClickElement("#Carriers > table > tbody td",$provide);
            }
            else

            {
                $this->user->click('a[href="#Vendors"]');
                $M->findTextClickElement("#Vendors > table > tbody td",$provide);
            }
            $this->user->fillField('#deduction_code',$deduction_code);
            $this->user->fillField('#description',$description);
            $this->user->fillField('#category',$category);
            $this->user->fillField('#quantity', $quantity);
            $this->user->fillField('#rate', $rate);
            $this->user->click('#submit'); //click 'Save' button)

        }
    }

    public function addRecurring($frequency,$day, $secondDayOrNumberOfWeek)
    {
        $this->user->checkOption('#recurring');
        $this->user->selectOption('#billing_cycle_id', $frequency);
        if($frequency=='Weekly')
        {
            $this->user->selectOption('#week_day',$day);
        }
        elseif($frequency=='Biweekly')
        {
            $this->user->selectOption('#week_day',$day);
            $this->user->selectOption('#week_offset', $secondDayOrNumberOfWeek);
        }
        elseif($frequency=='Monthly')
        {
            $this->user->selectOption('#first_start_day',$day);
        }
        elseif($frequency=='Semi-Monthly')
        {
            $this->user->selectOption('#first_start_day',$day);
            $this->user->selectOption('#second_start_day',$secondDayOrNumberOfWeek);
        }
        else  //Semi-Weekly
        {
            $this->user->selectOption('#week_day',$day);
            $this->user->selectOption('#second_week_day',$secondDayOrNumberOfWeek);
        }
        $this->user->click('#submit'); //click 'Save' button)
    }

    public function addPayment($codeTemplate, $contractorID)
    {
        $this->user->click('div.row.table-controll a:nth-child(2)');
        $this->user->click('a[href="#setup-payment-setup"]');
        $this->user->executeJS('$("#updatethis-Application_Model_Entity_Payments_Setup td").filter(function(){return $(this).text()=="'.$codeTemplate.'";}).parent().find(".checkboxField > input").trigger("click")');
        $this->user->click('a[href="#contractors-payment-setup"]');
        $this->user->executeJS('$("#updatethis-Application_Model_Entity_Entity_Contractor td").filter(function(){return $(this).text()=="'.$contractorID.'";}).parent().find(".checkboxField > input").trigger("click")');
        $this->user->wait(3);
        $this->user->click('div.payment-setup.setup.popup_checkbox_modal.modal.hide.fade.in > div.modal-footer > a');
    }

    public function addDeduction($codeTemplate, $contractorID)
    {
        $this->user->click('div.row.table-controll a:nth-child(2)');
        $this->user->click('a[href="#setup-deduction-setup"]');
        $this->user->executeJS('$("#updatethis-Application_Model_Entity_Deductions_Setup td").filter(function(){return $(this).text()=="'.$codeTemplate.'";}).parent().find(".checkboxField > input").trigger("click")');
        $this->user->click('a[href="#contractors-deduction-setup"]');
        $this->user->executeJS('$("#updatethis-Application_Model_Entity_Entity_Contractor td").filter(function(){return $(this).text()=="'.$contractorID.'";}).parent().find(".checkboxField > input").trigger("click")');
        $this->user->wait(3);
        $this->user->click('div.deduction-setup.setup.popup_checkbox_modal.modal.hide.fade.in > div.modal-footer > a');
    }

    public function addSettlement($cycle, $startDate, $processingDeadline, $disbursementTerms)
    {
        $G = new BaseFunctionsPage($this->user);
        // check exist settlement cycle if not create it
        if($G->grabTextFromElement('div.content.clearfix > div.right > a')=="Create New")
        {
            $this->user->click('div.content.clearfix > div.right > a');
            if($G->grabTextFromElement('div.content.clearfix > h3')=="Create Settlement Cycle Rules")
            {
                $this->user->selectOption('#cycle_period_id', $cycle);
                $this->user->fillField('#cycle_start_date',$startDate);
                $this->user->fillField('#payment_terms', $processingDeadline);
                $this->user->fillField('#disbursement_terms',$disbursementTerms);
                $this->user->click('#submit');
                $this->user->click('#submit');
            }
            else
            {
 /*             $this->user->selectOption('#cycle_period_id', $cycle);
                $this->user->fillField('#cycle_start_date',$startDate);
                $this->user->fillField('#processing_date',date('m/d/Y', strtotime($startDate.'14:28 +'. $processingDeadline.'day')));
                $this->user->fillField('#disbursement_date',date('m/d/Y', strtotime($startDate.'14:28 +'. $disbursementTerms.'day')));  */
                $this->user->click('#submit');
            }
            $this->user->click('td.buttons > div > a.btn.btn-success'); //click Verify button
        }
        else $this->user->click('td.buttons > div > a.btn.btn-success'); //click Verify button
    }

    public function addCarrierVendorRA($carrierVendor, $code, $description, $minBalance, $contributionAmount)
    {
        $M = new BaseFunctionsPage($this->user);
        $this->user->click('//div[3]/ul/li[4]/a'); //click on Reserves menu
        $this->user->click('//div[3]/ul/li[4]/ul/li[2]/a'); // click on 'Carrier/Vendor Reserve Account' submenu

        if($M->checkForItem('#updatethis-Application_Model_Entity_Accounts_Reserve_Vendor td',$code))
        {
            $this->user->click('//div[3]/ul/li[4]/a'); //click on Reserves menu
            $this->user->click('//div[3]/ul/li[4]/ul/li[3]/a'); // click on 'Create Carrier/Vendor Reserve Account' submenu
            $this->user->click('#entity_id_title');
            if($M->checkForItem('#Vendor > table > tbody td',$carrierVendor)==false)
            {
                $this->user->click('a[href="#Vendor"]');
                $M->findTextClickElement("#Vendor > table > tbody td",$carrierVendor);
            }
            else
            {
                $this->user->click('a[href="#Carrier"]');
                $M->findTextClickElement("#Carrier > table > tbody td",$carrierVendor);
            }
            $this->user->fillField('#account_name',$code);
            $this->user->fillField('#description',$description);
            $this->user->fillField('#min_balance',$minBalance);
            $this->user->fillField('#contribution_amount', $contributionAmount);
            $this->user->click('#submit');
        }

    }

    public function createContractorRA ($contractor, $carrierVendor, $code, $initialBalance, $currentBalance)
    {
        $M = new BaseFunctionsPage($this->user);
        $this->user->click('//div[3]/ul/li[4]/a'); //click on Reserves menu
        $this->user->click('//div[3]/ul/li[4]/ul/li[4]/a'); // click on 'Create Contractor Reserve Account' submenu
        if($M->checkForItem('#updatethis-Application_Model_Entity_Accounts_Reserve_Contractor td',$carrierVendor))
        {
            $this->user->click('//div[3]/ul/li[4]/a'); //click on Reserves menu
            $this->user->click('//div[3]/ul/li[4]/ul/li[5]/a'); // click on 'Create Contractor Reserve Account' submenu
            $this->user->click('#entity_id_title');
            $M->findTextClickElement("#entity_id_modal table > tbody td",$contractor);
            $this->user->click('#vendor_id_title');
            //$this->user->waitForElement('a[href="#Carrier"]',30);
           // $this->user->click('a[href="#Carrier"]');
            $this->user->wait(2);

            if($M->checkForItem('#Vendor > table > tbody td',$carrierVendor)==false)
            {
                $M->findTextClickElement("#Vendor > table > tbody td",$carrierVendor);
            }
            else
            {
                $this->user->click('a[href="#Carrier"]');
                $this->user->wait(2);
                $M->findTextClickElement("#Carrier > table > tbody td",$carrierVendor);
            }
            $this->user->wait(5);

            $this->user->click('#reserve_account_vendor_id_title');
            $M->findTextClickElement("#reserve_account_vendor_id_modal table > tbody td",$code);
            $this->user->fillField('#initial_balance',$initialBalance);
            $this->user->fillField('#current_balance',$currentBalance);
            $this->user->click('#submit');
        }

    }

    public function checkResult()
    {
        $this->user->click('div.menu > ul > li:nth-child(1) > a'); // go to the Settlement page


    }
}


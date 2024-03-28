<?php
use Codeception\Module\Input_Data;
use Codeception\Module\BaseSelectors;
use Codeception\Module\Customers_Data;
use Codeception\Module\Contractors_Data;

class CustomersPage
{
    public $user;

    public function __construct(AcceptanceTester $I)
    {
        $this->user = $I;
    }

    public function deleteTestCustomers($customer)
    {
        $Base = new BaseFunctionsPage($this->user);
        //go to the Customer page and delete customer if it is exist
        $this->user->click(Customers_Data::$customers_Menu);
        $this->user->click(Customers_Data::$customers_Submenu);
        $this->user->fillField(Customers_Data::$customer_name_Filter, $customer);
        $this->user->click(Contractors_Data::$filter_Button);
        $this->user->wait(1);
        while ($Base->checkForItem(Customers_Data::$table_Entity_Customer, $customer)==false) {
            $this->user->click('Delete');
            $this->user->waitForElementVisible('#confirm-modal');
            $this->user->click('Yes');
        }
    }

     public function customerCreate($customer, $tax_id, $name, $contact, $terms, $account_nickname, $bank_routing_id, $bank_account_id, $escrow_account_holder, $holder_federal_tax_id, $bank_name, $bank_routing_number, $bank_account_number)
    {
        $Base = new BaseFunctionsPage($this->user);
        $Customer = new CustomersPage($this->user);
        $Vendor = new VendorsPage($this->user);
        $Contractor = new ContractorsPage($this->user);
        $Reserves = new ReservesPage($this->user);
        $Payment = new PaymentsPage($this->user);
        $Deduction = new DeductionsPage($this->user);
        //go to the Customer page and select customer if it is exist or create it
        $this->user->click(Customers_Data::$customers_Menu);
        $this->user->click(Customers_Data::$customers_Submenu);
        $this->user->fillField(Customers_Data::$customer_name_Filter, $customer);
        $this->user->click(Contractors_Data::$filter_Button);
        $this->user->wait(1);
        if($Base->checkForItem(Customers_Data::$table_Entity_Customer, $customer)==false) {
            $Customer->selectCarrierOnTopMenu($customer);
        } else {
            $this->user->seeInCurrentUrl(Customers_Data::$customer_Page_URL);
            $this->user->click(BaseSelectors::$create_New_Button); //click '+Create New' button
            $this->user->fillField(Customers_Data::$customer_tax_id,$tax_id);
            $this->user->fillField(Customers_Data::$customer_name,$name);
            $this->user->fillField(Customers_Data::$customer_contract,$contact);
            $this->user->fillField(Customers_Data::$customer_terms,$terms);
            $this->user->click(BaseSelectors::$save_Button); //click 'Save' button
            $this->user->seeInCurrentUrl(BaseSelectors::$bank_Account_Url);
            $this->user->fillField(Customers_Data::$customer_account_nickname,$account_nickname);
            $this->user->fillField(Customers_Data::$customer_bank_routing_id, $bank_routing_id);
            $this->user->fillField(Customers_Data::$customer_bank_account_id, $bank_account_id);
            $this->user->click(BaseSelectors::$save_Button); //click 'Save' button
            $this->user->seeInCurrentUrl(Customers_Data::$settlement_Escrow_Account_Url);
            $this->user->fillField(Customers_Data::$customer_escrow_account_holder,$escrow_account_holder);
            $this->user->fillField(Customers_Data::$customer_holder_federal_tax_id, $holder_federal_tax_id);
            $this->user->fillField(Customers_Data::$customer_bank_name, $bank_name);
            $this->user->fillField(Customers_Data::$customer_bank_routing_number, $bank_routing_number);
            $this->user->fillField(Customers_Data::$customer_bank_account_number, $bank_account_number);
            $this->user->click(BaseSelectors::$save_Button); //click 'Save' button
            //select the carrier on the right top menu
            $Customer->selectCarrierOnTopMenu($customer);
            if($customer==Input_Data::$customer){
                // create vendor 1 if it's not exist
                $Vendor->vendorCreate(Input_Data::$vendor1_ID, Input_Data::$vendor1, Input_Data::$vendor1_Contractor, Input_Data::$vendor1_Federal_Tax_ID, Input_Data::$vendor1_Account_Nickname, Input_Data::$vendor1_Bank_Routing_ID, Input_Data::$vendor1_Bank_Account_ID);
                // create vendor 2 if it's not exist
                $Vendor->vendorCreate(Input_Data::$vendor2_ID, Input_Data::$vendor2, Input_Data::$vendor2_Contractor, Input_Data::$vendor2_Federal_Tax_ID, Input_Data::$vendor2_Account_Nickname, Input_Data::$vendor2_Bank_Routing_ID, Input_Data::$vendor2_Bank_Account_ID);
                // create contractor if it's not exist
                $Contractor->contractorCreate(Input_Data::$contractor1);
                $Contractor->addVendorForContractor(Input_Data::$contractor1['Contractor_Name']);
                $Contractor->contractorCreate(Input_Data::$contractor2);
                $Contractor->addVendorForContractor(Input_Data::$contractor2['Contractor_Name']);
                $Contractor->contractorCreate(Input_Data::$contractor3);
                $Contractor->addVendorForContractor(Input_Data::$contractor3['Contractor_Name']);
                // create RA
                $Reserves->addCarrierVendorRA(Input_Data::$customer, Input_Data::$customer_RA_Code, Input_Data::$customer_RA_description, Input_Data::$customer_RA_Min_Balance, Input_Data::$customer_RA_Contribution_Amount);
                $Reserves->createContractorRA(Input_Data::$contractor1, Input_Data::$customer, Input_Data::$contractor_RA_Code1, Input_Data::$contractor_Initial_Balance1, Input_Data::$contractor1_Current_Balance1);
                $Reserves->createContractorRA(Input_Data::$contractor2, Input_Data::$customer, Input_Data::$contractor_RA_Code1, Input_Data::$contractor_Initial_Balance1, Input_Data::$contractor2_Current_Balance1);
                $Reserves->createContractorRA(Input_Data::$contractor3, Input_Data::$customer, Input_Data::$contractor_RA_Code1, Input_Data::$contractor_Initial_Balance1, Input_Data::$contractor3_Current_Balance1);
                $Reserves->addCarrierVendorRA(Input_Data::$vendor2, Input_Data::$vendor2_RA_Code, Input_Data::$vendor2_RA_description, Input_Data::$vendor2_RA_Min_Balance, Input_Data::$vendor2_RA_Contribution_Amount);
                $Reserves->createContractorRA(Input_Data::$contractor1, Input_Data::$vendor2, Input_Data::$contractor_RA_Code2, Input_Data::$contractor_Initial_Balance2, Input_Data::$contractor1_Current_Balance2);
                $Reserves->createContractorRA(Input_Data::$contractor2, Input_Data::$vendor2, Input_Data::$contractor_RA_Code2, Input_Data::$contractor_Initial_Balance2, Input_Data::$contractor2_Current_Balance2);
                $Reserves->createContractorRA(Input_Data::$contractor3, Input_Data::$vendor2, Input_Data::$contractor_RA_Code2, Input_Data::$contractor_Initial_Balance2, Input_Data::$contractor3_Current_Balance2);
                // add payments templates
                $Payment->paymentTemplateCreate(Input_Data::$payment_Template_Payment_Code1, Input_Data::$payment_Template_Carrier_Payment_Code1, Input_Data::$payment_Template_Description1, Input_Data::$payment_Template_Category1, Input_Data::$payment_Template_Quantity1, Input_Data::$payment_template_Rate1);
                $Payment->paymentTemplateCreate(Input_Data::$payment_Template_Payment_Code2, Input_Data::$payment_Template_Carrier_Payment_Code2, Input_Data::$payment_Template_Description2, Input_Data::$payment_Template_Category2, Input_Data::$payment_Template_Quantity2, Input_Data::$payment_template_Rate2);
                // add deduction templates
                $Deduction->deductionTemplateCreate(Input_Data::$vendor1, Input_Data::$deduction_Template_Deduction_Code1,Input_Data::$deduction_Template_Description1,Input_Data::$deduction_Template_Category1,Input_Data::$deduction_Template_Quantity1,Input_Data::$deduction_Template_Rate1);
                $Deduction->deductionTemplateCreate(Input_Data::$vendor2, Input_Data::$deduction_Template_Deduction_Code2,Input_Data::$deduction_Template_Description2,Input_Data::$deduction_Template_Category2,Input_Data::$deduction_Template_Quantity2,Input_Data::$deduction_Template_Rate2);
                $Deduction->deductionTemplateCreate(Input_Data::$customer, Input_Data::$deduction_Template_Deduction_Code3,Input_Data::$deduction_Template_Description3,Input_Data::$deduction_Template_Category3,Input_Data::$deduction_Template_Quantity3,Input_Data::$deduction_Template_Rate3);
                //adding an eligible of customer reserve account 'TLRA' for deduction with code 'TL'
                $Deduction->addEligibleReserveAccount(Input_Data::$deduction_Template_Deduction_Code3, Input_Data::$customer_RA_Code);
            }
            if($customer==Input_Data::$customer2){
                // create contractor if it's not exist
                $Contractor->contractorCreate(Input_Data::$contractor1);
                // add payments templates
                $Payment->addRecurringPaymentTemplate(Input_Data::$recurring_Template1, Input_Data::$recurring_Template_Quantity, Input_Data::$recurring_Template_Rate);
                $Payment->addRecurringPaymentTemplate(Input_Data::$recurring_Template2, Input_Data::$recurring_Template_Quantity, Input_Data::$recurring_Template_Rate);
                $Payment->addRecurringPaymentTemplate(Input_Data::$recurring_Template3, Input_Data::$recurring_Template_Quantity, Input_Data::$recurring_Template_Rate);
                $Payment->addRecurringPaymentTemplate(Input_Data::$recurring_Template4, Input_Data::$recurring_Template_Quantity, Input_Data::$recurring_Template_Rate);
                $Payment->addRecurringPaymentTemplate(Input_Data::$recurring_Template5, Input_Data::$recurring_Template_Quantity, Input_Data::$recurring_Template_Rate);
                // add deduction templates
                $Deduction->addRecurringDeductionTemplate($customer, Input_Data::$recurring_Template1, Input_Data::$recurring_Template_Quantity, Input_Data::$recurring_Template_Rate);
                $Deduction->addRecurringDeductionTemplate($customer, Input_Data::$recurring_Template2, Input_Data::$recurring_Template_Quantity, Input_Data::$recurring_Template_Rate);
                $Deduction->addRecurringDeductionTemplate($customer, Input_Data::$recurring_Template3, Input_Data::$recurring_Template_Quantity, Input_Data::$recurring_Template_Rate);
                $Deduction->addRecurringDeductionTemplate($customer, Input_Data::$recurring_Template4, Input_Data::$recurring_Template_Quantity, Input_Data::$recurring_Template_Rate);
                $Deduction->addRecurringDeductionTemplate($customer, Input_Data::$recurring_Template5, Input_Data::$recurring_Template_Quantity, Input_Data::$recurring_Template_Rate);
            }
        }
    }

    public function selectCarrierOnTopMenu($customer)
    {
        $Base = new BaseFunctionsPage($this->user);
        $this->user->click(BaseSelectors::$current_Customer_Top_Menu);
        $Base->findTextClickElement(BaseSelectors::$table_Customer_Top_Menu,$customer);
        $this->user->wait(1);

    }

    public function purgeData($customer)
    {
        $this->user->click(Customers_Data::$customers_Menu);
        $this->user->click(Customers_Data::$customers_Submenu);
        $this->user->fillField(Customers_Data::$customer_name, $customer);
        $this->user->click(Customers_Data::$customer_Filter_Button);
        $this->user->wait(1);
        $this->user->click(Customers_Data::$customer_Edit_Button);
        $this->user->click(Customers_Data::$customer_Purge_Data);
        $this->user->waitForElementVisible('#confirm-modal ');
        $this->user->click(Customers_Data::$customer_Confirm_Purge_Yes_Button);
        $this->user->wait(1);
        $this->selectCarrierOnTopMenu($customer);
    }
}
<?php
use Codeception\Module\Reserves_Data;
use Codeception\Module\BaseSelectors;
class ReservesPage
{
    use \Codeception\Util\Shared\Asserts;
    protected $user;

    public function __construct(AcceptanceTester $I)
    {
        $this->user = $I;
    }

    public function addCarrierVendorRA($vendor, $code, $description, $minBalance, $contributionAmount)
    {
        $Base = new BaseFunctionsPage($this->user);
        $this->user->click(Reserves_Data::$reserves_Menu); //click on Reserves menu
        $this->user->wait(1);
        $this->user->click(Reserves_Data::$vendor_Reserve_Account_Submenu); // click on 'Carrier/Vendor Reserve Account' submenu
        if($Base->checkForItem(Reserves_Data::$vendor_RA_table,$code))
        {
            $this->user->click(Reserves_Data::$create_New_Vendor_Reserve_Accounts_Button);
            $this->user->click(Reserves_Data::$vendor_Locator_Field);
            $this->user->wait(1);
            if($Base->checkForItem(BaseSelectors::$vendor_Table_In_Popup,$vendor)==false)
            {
                $Base->findTextClickElement(BaseSelectors::$vendor_Table_In_Popup,$vendor);
            }
            else
            {
                $this->user->click(BaseSelectors::$customer_Tab_In_Popup);
                $Base->findTextClickElement(BaseSelectors::$customer_Table_In_Popup,$vendor);
            }
            $this->user->fillField(Reserves_Data::$account_Name,$code);
            $this->user->fillField(Reserves_Data::$reserve_Code_Locator_Field,$code);
            $this->user->fillField(Reserves_Data::$description_Name_Field,$description);
            $this->user->fillField(Reserves_Data::$minimum_Balance_Locator_Field,$minBalance);
            $this->user->fillField(Reserves_Data::$contribution_Amount_Locator_Field, $contributionAmount);
            $this->user->click(BaseSelectors::$save_Button);
        }
    }

    public function createContractorRA (array $contractor, $vendor, $code, $initialBalance, $currentBalance)
    {
        $Base = new BaseFunctionsPage($this->user);
        $this->user->click(Reserves_Data::$reserves_Menu); //click on Reserves menu
        $this->user->wait(1);
        $this->user->click(Reserves_Data::$contractor_Reserve_Account_Submenu); // click on ' Contractor Reserve Account' submenu
        $this->user->fillField(Reserves_Data::$company_Filter_Field,$contractor['Contractor_Name']);
        $this->user->click(Reserves_Data::$reserves_Filter_Button);
        $this->user->wait(1);
        if($Base->checkForItem(Reserves_Data::$contractor_RA_Table,$vendor))
        {
            $this->user->click(Reserves_Data::$create_New_Contractor_Reserve_Account_Button);
            //$this->user->click('//div[3]/ul/li[4]/a'); //click on Reserves menu
            //$this->user->click('//div[3]/ul/li[4]/ul/li[5]/a'); // click on 'Create Contractor Reserve Account' submenu
            $this->user->click(Reserves_Data::$company_Locator_Field);
            $this->user->wait(2);
            $Base->findTextClickElement(Reserves_Data::$list_Contractor_In_Table_Popup, $contractor['Contractor_Name']);
            $this->user->wait(2);
            $this->user->click(Reserves_Data::$create_Contractor_Vendor_Field);
            $this->user->wait(2);
            if($Base->checkForItem(BaseSelectors::$vendor_Table_In_Popup,$vendor)==false){
                $Base->findTextClickElement(BaseSelectors::$vendor_Table_In_Popup,$vendor);
            }
            else{
                $Base->findTextClickElement(BaseSelectors::$customer_Table_In_Popup,$vendor);
            }
     $this->user->wait(3);
     $this->user->click(Reserves_Data::$select_Reserve_Account);
     $Base->findTextClickElement(Reserves_Data::$reserve_Account_Table_In_Popup,$code);
     $this->user->fillField(Reserves_Data::$initial_Balance_Locator_field,$initialBalance);
     $this->user->fillField(Reserves_Data::$current_Balance_Locator_Field,$currentBalance);
     $this->user->click(BaseSelectors::$save_Button);
        }
    }

    public function checkEndingBalanceVendorRA($sumRA)
    {
        $Base = new BaseFunctionsPage($this->user);
        $this->user->click(Reserves_Data::$reserves_Menu); //click on Reserves menu
        $this->user->wait(1);
        $this->user->click(Reserves_Data::$contractor_Reserve_Account_Submenu); // click on 'Contractor Reserve Accounts' submenu
        $contractor_Ending_Balances_RA=$Base->getTextElement(Reserves_Data::$contractor_RA_Table.':nth-child(10)');
        $amount = [];
        for($i=0; $i<count($contractor_Ending_Balances_RA); $i++)
        {
            $amount[$i]=ltrim($contractor_Ending_Balances_RA[$i],"$"); //remove symbol '$'
            $amount[$i]=strtr($amount[$i],array(','=>''));
        }
//        print_r($amount);
        $sum_Contractor_Ending_Balances_RA=array_sum($amount);
//        print_r($sum_Contractor_Ending_Balances_RA);
        if($sum_Contractor_Ending_Balances_RA!=$sumRA) {
            $this->fail("Sum of Reserve Account ending balances has a wrong values" . $sum_Contractor_Ending_Balances_RA . " = " . $sumRA);
        }
    }

    public function checkEndingBalanceContractorRA($sumRA,$name_Contractor){
        $Base = new BaseFunctionsPage($this->user);
        $this->user->click(Reserves_Data::$reserves_Menu); //click on Reserves menu
        $this->user->wait(1);
        $this->user->click(Reserves_Data::$contractor_Reserve_Account_Submenu); // click on 'Contractor Reserve Accounts' submenu
        $this->user->fillField(Reserves_Data::$company_Filter_Field,$name_Contractor);
        $this->user->click(Reserves_Data::$reserves_Filter_Button);
        $this->user->wait(1);
        $contractor_Ending_Balances_RA=$Base->getTextElement(Reserves_Data::$contractor_RA_Table.':nth-child(10)');
        for($i=0; $i<count($contractor_Ending_Balances_RA); $i++){
            $contractor_Ending_Balances_RA[$i]=ltrim($contractor_Ending_Balances_RA[$i],"$"); //remove symbol '$'
            $contractor_Ending_Balances_RA[$i]=strtr($contractor_Ending_Balances_RA[$i],array(','=>''));
        }
        $sum_Contractor_Ending_Balances_RA=array_sum($contractor_Ending_Balances_RA);
//        print_r($sum_Contractor_Ending_Balances_RA);
        if($sum_Contractor_Ending_Balances_RA!=$sumRA) {
            $this->fail("Sum ending balances of Contractor Reserve Account has a wrong values" . $sum_Contractor_Ending_Balances_RA . " = " . $sumRA);
        }
    }

    public function deleteReserveTransaction($contractor, $code, $type)
    {
        $this->user->click(Reserves_Data::$reserves_Menu); //click on Reserves menu
        $this->user->wait(1);
        $this->user->click(Reserves_Data::$reserve_Transactions_Submenu);
        $this->user->fillField(Reserves_Data::$reserve_Company_Filter, $contractor);
        $this->user->fillField(Reserves_Data::$reserve_Code_Locator_Field, $code);
        $this->user->fillField(Reserves_Data::$reserve_Transaction_Type_Filter, $type);
        $this->user->click(Reserves_Data::$reserves_Filter_Button);
        $this->user->wait(1);
        $this->user->click(Reserves_Data::$reserve_Transaction_Delete_Button);
        $this->user->waitForElementVisible('#confirm-modal');
        $this->user->click('#btn-confirm');
    }

    public function editReserveTransaction($contractor, $code, $type, $value)
    {
        $this->user->click(Reserves_Data::$reserves_Menu); //click on Reserves menu
        $this->user->wait(1);
        $this->user->click(Reserves_Data::$reserve_Transactions_Submenu);
        $this->user->fillField(Reserves_Data::$reserve_Company_Filter, $contractor);
        $this->user->fillField(Reserves_Data::$reserve_Code_Locator_Field, $code);
        $this->user->fillField(Reserves_Data::$reserve_Transaction_Type_Filter, $type);
        $this->user->click(Reserves_Data::$reserves_Filter_Button);
        $this->user->wait(1);
        $this->user->click(Reserves_Data::$reserve_Transaction_Edit_Button);
        $this->user->fillField(Reserves_Data::$reserve_Transaction_Amount_Field,$value);
        $this->user->click(BaseSelectors::$save_Button);
    }

}
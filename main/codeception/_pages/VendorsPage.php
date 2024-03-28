<?php
use Codeception\Module\Input_Data;
use Codeception\Module\BaseSelectors;
use Codeception\Module\Vendors_data;

class VendorsPage
{
    protected $user;

    public function __construct(AcceptanceTester $I)
    {
        $this->user = $I;
    }

    public function vendorCreate($id, $vendor, $contractor, $tax_id, $account_nickname, $bank_routing_id, $bank_account_id)
    {
        $Base = new BaseFunctionsPage($this->user);
        $this->user->click(Vendors_data::$vendors_Menu); //click on Vendors menu
        if($Base->checkForItem(Vendors_data::$selectorVendorTable,$vendor))
        {
            $this->user->seeInCurrentUrl(Vendors_data::$vendor_Url);
            $this->user->click(BaseSelectors::$create_New_Vendor); // click on '+ Create New' button
            $this->user->fillField(Vendors_data::$vendor_ID_Field,$id);
            $this->user->fillField(Vendors_data::$vendor_Name_Field,$vendor);
            $this->user->fillField(Vendors_data::$vendor_Contact_Field,$contractor);
            $this->user->fillField(Vendors_data::$vendor_Federal_Tax_ID_Field,$tax_id);
            $this->user->click(BaseSelectors::$save_Button); //click 'Save' button
            $this->user->seeInCurrentUrl(BaseSelectors::$bank_Account_Url);
            $this->user->fillField(Vendors_data::$vendor_Account_Nickname,$account_nickname);
            $this->user->fillField(Vendors_data::$vendor_Bank_Routing_Number, $bank_routing_id);
            $this->user->fillField(Vendors_data::$vendor_Bank_Account_Number, $bank_account_id);
            $this->user->click(BaseSelectors::$save_Button); //click 'Save' button
        }
    }
}
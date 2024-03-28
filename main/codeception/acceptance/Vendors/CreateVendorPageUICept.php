<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\BaseSelectors;
use Codeception\Module\Vendors_data;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$Base = new BaseFunctionsPage($I);

$I->wantTo('see UI elements on Create Vendors page');
$I->click(Vendors_data::$vendors_Menu); //click on Vendors menu
$I->click(Vendors_data::$create_New_Vendor_Button); // click on '+ Create New' button
$I->seeInCurrentUrl(Vendors_data::$create_Vendor_Page_URL);
$I->see(Vendors_data::$create_Vendor_Title_Page);
$I->see(Vendors_data::$bank_Account_Name_Button);
$I->seeElement(Vendors_data::$bank_Account_Button);
$I->see(Vendors_data::$reserve_Account_Name_Button);
$I->seeElement(Vendors_data::$reserve_Account_Button);
$I->see(Vendors_data::$vendor_ID_Name);
$I->seeElement(Vendors_data::$vendor_ID_Field);
$I->see(Vendors_data::$vendor_Name);
$I->seeElement(Vendors_data::$vendor_Name_Field);
$I->see(Vendors_data::$vendor_Contact_Name);
$I->seeElement(Vendors_data::$vendor_Contact_Field);
$I->see(Vendors_data::$vendor_Fed_Tax_ID);
$I->seeElement(Vendors_data::$vendor_Federal_Tax_ID_Field);
$I->see(Vendors_data::$vendor_Address1);
$I->see(Vendors_data::$vendor_Address2);
$I->see(Vendors_data::$vendor_City);
$I->see(Vendors_data::$vendor_State);
$I->see(Vendors_data::$vendor_Zip);
$I->see(Vendors_data::$vendor_Phone);
$I->see(Vendors_data::$vendor_Fax);
$I->see(Vendors_data::$vendor_Email);
$I->see(Vendors_data::$vendor_Correspondence);
$I->seeElement(Vendors_data::$vendor_Correspondence_Field);

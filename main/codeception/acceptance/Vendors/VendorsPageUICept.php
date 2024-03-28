<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\BaseSelectors;
use Codeception\Module\Vendors_data;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$Base = new BaseFunctionsPage($I);

$I->wantTo('see UI elements on Vendors page');
$I->click(Vendors_data::$vendors_Menu); //click on Vendors menu
$I->seeInCurrentUrl(Vendors_data::$vendor_Url);
$I->see(Vendors_data::$vendor_Title_Page, BaseSelectors::$page_Header);
$recordValue=$Base->getTextElement(BaseSelectors::$selector_Records);
$Base->checkArrayDiff($recordValue,BaseSelectors::$records);
$tableValue=$Base->getTextElement(Vendors_data::$vendors_Header_Table);
$Base->checkArrayDiff($tableValue,Vendors_data::$vendor_Header_Table_Original);
$I->see(Vendors_data::$filter_Name_Button, Vendors_data::$filter_Button);
$I->seeElement(Vendors_data::$filter_Button);//check visible 'Filter' button
$I->see(Vendors_data::$clear_Name_Button, Vendors_data::$clear_Button);
$I->seeElement(Vendors_data::$clear_Button); //check visible 'Clear' button
$I->seeElement(Vendors_data::$create_New_Vendor_Button); //check visible '+Create New' button
$I->see(Vendors_data::$create_New_Vendor_Name_Button, Vendors_data::$create_New_Vendor_Button);
$I->click(Vendors_data::$create_New_Vendor_Button); // click on '+ Create New' button
$I->seeInCurrentUrl(Vendors_data::$create_Vendor_Page_URL);

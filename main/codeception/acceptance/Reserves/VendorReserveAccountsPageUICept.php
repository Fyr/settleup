<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\Reserves_Data;
use Codeception\Module\BaseSelectors;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');

$Base = new BaseFunctionsPage($I);

$I->wantTo('see UI elements on the Vendor Reserve Accounts page');

$I->click(Reserves_Data::$reserves_Menu); //click on Reserves menu
$I->click(Reserves_Data::$vendor_Reserve_Account_Submenu); // click on Carrier/Vendor Reserve Accounts submenu
$I->seeInCurrentUrl(Reserves_Data::$vendor_Reserve_Accounts_Menu_URL);
$I->see(Reserves_Data::$vendor_Reserve_Accounts, BaseSelectors::$page_Header);
$recordValue=$Base->getTextElement(BaseSelectors::$selector_Records);
$Base->checkArrayDiff($recordValue,BaseSelectors::$records);
$tableValue=$Base->getTextElement(Reserves_Data::$vendor_Reserve_Accounts_Header);
$Base->checkArrayDiff($tableValue,Reserves_Data::$vendor_Reserve_Accounts_Header_Original);
$I->see(Reserves_Data::$filter,Reserves_Data::$reserves_Filter_Button);
$I->seeElement(Reserves_Data::$reserves_Filter_Button);//check visible 'Filter' button
$I->see(Reserves_Data::$clear, Reserves_Data::$reserves_Clear_Button);
$I->seeElement(Reserves_Data::$reserves_Clear_Button); //check visible 'Clear' button
$I->seeElement(Reserves_Data::$create_New_Vendor_Reserve_Accounts_Button); //check visible '+Create New' button
$I->click(Reserves_Data::$create_New_Vendor_Reserve_Accounts_Button); // click on '+ Create New' button
$I->seeInCurrentUrl(Reserves_Data::$create_Vendor_Reserve_Account_Menu_URL);
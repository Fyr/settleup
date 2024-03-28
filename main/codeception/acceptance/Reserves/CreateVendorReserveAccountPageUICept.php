<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\Reserves_Data;
use Codeception\Module\BaseSelectors;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');

$Base = new BaseFunctionsPage($I);

$I->wantTo('see UI elements on the Create Vendor Reserve Accounts page');
$I->click(Reserves_Data::$reserves_Menu); //click on Reserves menu
$I->click(Reserves_Data::$create_Vendor_Reserve_Account_Submenu); // click on 'Create Vendor Reserve Account' submenu
$I->seeInCurrentUrl(Reserves_Data::$create_Vendor_Reserve_Account_Menu_URL);
$I->see(Reserves_Data::$create_Vendor_Reserve_Account, BaseSelectors::$page_Header);
$I->see(Reserves_Data::$vendor_Name_Field);
$I->seeElement(Reserves_Data::$vendor_Locator_Field);
$I->seeElement(Reserves_Data::$delete_Selected_Vendor_Button); //Clear button for selected Vendor
$I->click(Reserves_Data::$vendor_Locator_Field); //click on Carrier/Vendor fields ang gets popup
$I->waitForElementVisible(Reserves_Data::$select_Vendor_Popup);
$I->seeElement(Reserves_Data::$select_Vendor_Popup);
$I->see(Reserves_Data::$select_vendor_Title_In_Popup,BaseSelectors::$page_Header);
$I->click(BaseSelectors::$vendor_Tab_In_Popup);
$table=$Base->getTextElement(Reserves_Data::$vendor_Header_Table_In_Select_Vendor_Popup);
$Base->checkArrayDiff($table,Reserves_Data::$vendor_Header_Table_In_Select_Vendor_Popup_Original);
$I->click(BaseSelectors::$customer_Tab_In_Popup);
$table=$Base->getTextElement(Reserves_Data::$customer_Header_Table_In_Select_Vendor_Popup);
$Base->checkArrayDiff($table,Reserves_Data::$customer_Header_Table_In_Select_Vendor_Popup_Original);
$I->seeElement(Reserves_Data::$close_Button_In_Select_Vendor_Popup);
$I->click(Reserves_Data::$close_Button_In_Select_Vendor_Popup); //click Close button
$I->see(Reserves_Data::$reserve_Account_Name_Field);
$I->seeElement(Reserves_Data::$reserve_Account_Locator_Field);
$I->see(Reserves_Data::$reserve_Code_Name_Field);
$I->seeElement(Reserves_Data::$reserve_Account_Locator_Field);
$I->see(Reserves_Data::$description_Name_Field);
$I->seeElement(Reserves_Data::$description_Locator_Field);
$I->see(Reserves_Data::$minimum_Balance_Name_Field);
$I->seeElement(Reserves_Data::$minimum_Balance_Locator_Field);
$I->see(Reserves_Data::$contribution_Amount_Name_Field);
$I->seeElement(Reserves_Data::$contribution_Amount_Locator_Field);
$I->see(Reserves_Data::$initial_Balance_Name_field);
$I->seeElement(Reserves_Data::$initial_Balance_Locator_field);
$I->see(Reserves_Data::$current_Balance_Name_Field);
$I->seeElement(Reserves_Data::$current_Balance_Locator_Field);
$I->seeElement(BaseSelectors::$save_Button); //Save button
$I->seeElement(Reserves_Data::$cancel_Button); //Cancel Button
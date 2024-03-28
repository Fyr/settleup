<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\Reserves_Data;
use Codeception\Module\BaseSelectors;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$Base = new BaseFunctionsPage($I);

$I->wantTo('see UI elements on Contractor Reserve Accounts page');
$I->click(Reserves_Data::$reserves_Menu); //click on Reserves menu
$I->click(Reserves_Data::$contractor_Reserve_Account_Submenu); // click on 'Contractor Reserve Accounts' submenu
$I->seeInCurrentUrl(Reserves_Data::$contractor_Reserve_Accounts_Menu_URL);
$I->see(Reserves_Data::$contractor_Reserve_Accounts, BaseSelectors::$page_Header);
$recordValue=$Base->getTextElement(BaseSelectors::$selector_Records);
$Base->checkArrayDiff($recordValue,BaseSelectors::$records);
$recordValue=$Base->getTextElement(BaseSelectors::$selector_Filter_Contractor_Status);
$Base->checkArrayDiff($recordValue,BaseSelectors::$filter_Contractor_Status);
$tableValue=$Base->getTextElement(Reserves_Data::$contractor_Reserve_Accounts_Header_Table);
$Base->checkArrayDiff($tableValue,Reserves_Data::$contractor_Reserve_Accounts_Header_Table_Original);
$I->see(Reserves_Data::$filter, Reserves_Data::$reserves_Filter_Button);
$I->seeElement(Reserves_Data::$reserves_Filter_Button);//check visible 'Filter' button
$I->see(Reserves_Data::$clear, Reserves_Data::$reserves_Clear_Button);
$I->seeElement(Reserves_Data::$reserves_Clear_Button); //check visible 'Clear' button
$I->seeElement(Reserves_Data::$create_New_Contractor_Reserve_Account_Button); //check visible '+Create New' button
$I->click(Reserves_Data::$create_New_Contractor_Reserve_Account_Button); // click on '+ Create New' button
$I->seeInCurrentUrl(Reserves_Data::$create_Contractor_Reserve_Account_Menu_URL);
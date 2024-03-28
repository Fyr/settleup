<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\Customers_Data;
use Codeception\Module\BaseSelectors;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$Base = new BaseFunctionsPage($I);
$I->wantTo('see UI elements on Escrow Accounts page');

$I->click(Customers_Data::$customers_Menu); //click on Carriers menu
$I->click(Customers_Data::$escrow_Account_Submenu); // click on Escrow Accounts submenu
$I->seeInCurrentUrl(Customers_Data::$escrow_Account_Page_URL);
$I->see(Customers_Data::$escrow_Accounts, BaseSelectors::$page_Header);
$recordValue=$Base->getTextElement(BaseSelectors::$selector_Records);
$Base->checkArrayDiff($recordValue,BaseSelectors::$records);
$tableValue=$Base->getTextElement(Customers_Data::$escrow_Account_header_Table);
$Base->checkArrayDiff($tableValue,Customers_Data::$escrow_Account_header_Table_Original);
$I->see(Customers_Data::$filter_Name_Button, Customers_Data::$customer_Filter_Button);
$I->seeElement(Customers_Data::$customer_Filter_Button);//check visible 'Filter' button
$I->see(Customers_Data::$Clear_Name_Button, Customers_Data::$customer_Clear_Button);
$I->seeElement(Customers_Data::$customer_Clear_Button); //check visible 'Clear' button

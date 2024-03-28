<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\Customers_Data;
use Codeception\Module\BaseSelectors;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$Base = new BaseFunctionsPage($I);
$I->wantTo('see UI elements on Settlement Info page');

$I->click(Customers_Data::$customers_Menu); //click on Carriers menu
$I->click(Customers_Data::$settlement_Info_Submenu); // click on Settlement Info submenu
$I->seeInCurrentUrl(Customers_Data::$settlement_Info_Page_URL);
$I->see(Customers_Data::$settlement_Info, BaseSelectors::$page_Header);
$recordValue=$Base->getTextElement(BaseSelectors::$selector_Records);
$Base->checkArrayDiff($recordValue,BaseSelectors::$records);
$tableValue=$Base->getTextElement(Customers_Data::$settlement_Info_Header_Table);
$Base->checkArrayDiff($tableValue,Customers_Data::$settlement_Info_Header_Table_Original);
$I->see(Customers_Data::$filter_Name_Button, Customers_Data::$customer_Filter_Button);
$I->seeElement(Customers_Data::$customer_Filter_Button);//check visible 'Filter' button
$I->see(Customers_Data::$Clear_Name_Button, Customers_Data::$customer_Clear_Button);
$I->seeElement(Customers_Data::$customer_Clear_Button); //check visible 'Clear' button

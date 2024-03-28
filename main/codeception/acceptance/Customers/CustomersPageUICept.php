<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\Customers_Data;
use Codeception\Module\BaseSelectors;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$Base = new BaseFunctionsPage($I);
$I->wantTo('see UI elements on Customers page');

$I->click(Customers_Data::$customers_Menu); //click on Customers menu
$I->click(Customers_Data::$customers_Submenu); // click on Customers submenu

$I->seeInCurrentUrl(Customers_Data::$customer_Page_URL);
$I->see(Customers_Data::$customers, BaseSelectors::$page_Header);
$recordValue=$Base->getTextElement(BaseSelectors::$selector_Records);
$Base->checkArrayDiff($recordValue,BaseSelectors::$records);
$tableValue=$Base->getTextElement(Customers_Data::$customers_Header_Table);
$Base->checkArrayDiff($tableValue,Customers_Data::$customers_Header_Table_Original);
$I->see(Customers_Data::$filter_Name_Button, Customers_Data::$customer_Filter_Button);
$I->seeElement(Customers_Data::$customer_Filter_Button);//check visible 'Filter' button
$I->see(Customers_Data::$Clear_Name_Button, Customers_Data::$customer_Clear_Button);
$I->seeElement(Customers_Data::$customer_Clear_Button); //check visible 'Clear' button
$I->seeElement(Customers_Data::$create_New_Customer_Button); //check visible '+Create New' button
$I->see(Customers_Data::$create_New_Customer_Name_Button, Customers_Data::$create_New_Customer_Button);
$I->click(Customers_Data::$create_New_Customer_Button); // click on '+ Create New' button
$I->seeInCurrentUrl(Customers_Data::$create_Customer_Page_URL);
$I->moveBack();
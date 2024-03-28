<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\Customers_Data;
use Codeception\Module\BaseSelectors;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$Base = new BaseFunctionsPage($I);
$I->wantTo('see UI elements on Custom Field Descriptions page');

$I->click(Customers_Data::$customers_Menu); //click on Customers menu
$I->click(Customers_Data::$custom_Field_Descriptions_Submenu); // click on Customers submenu
$I->see(Customers_Data::$custom_Field_Descriptions, BaseSelectors::$page_Header);
$I->see(Customers_Data::$payment_Code);
$I->seeElement(Customers_Data::$payment_Code_Locator);
$I->see(Customers_Data::$customer_Payment_Code);
$I->seeElement(Customers_Data::$customer_Payment_Code_Locator);
$I->see(Customers_Data::$description);
$I->seeElement(Customers_Data::$description_Locator);
$I->see(Customers_Data::$category);
$I->seeElement(Customers_Data::$category_Locator);
$I->see(Customers_Data::$department);
$I->seeElement(Customers_Data::$department_Locator);
$I->see(Customers_Data::$GL_Code);
$I->see(Customers_Data::$invoice);
$I->seeElement(Customers_Data::$invoice_Locator);
$I->see(Customers_Data::$invoice_Date);
$I->seeElement(Customers_Data::$invoice_Date_Locator);
$I->seeElement(Customers_Data::$GL_Code_Locator);
$I->see(Customers_Data::$disbursement_Code);
$I->seeElement(Customers_Data::$disbursement_Code_Locator);


<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\Payments_Data;
use Codeception\Module\BaseSelectors;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$Base = new BaseFunctionsPage($I);

$I->wantTo('see UI elements on Create Master Compensation Templates');
$I->click(Payments_Data::$payment_Menu); //click on Payments menu
$I->click(Payments_Data::$create_Master_Payment_Template_Submenu); //click on Create Master Payment Templates submenu

$I->seeInCurrentUrl(Payments_Data::$create_Master_Payment_Template_URL);
$I->see(Payments_Data::$master_Payment_Templates_Detail,BaseSelectors::$page_Header);
$I->see(Payments_Data::$payment_Code);
$I->seeElement(Payments_Data::$payment_Code_Locator);
$I->see(Payments_Data::$customer_Payment_Code);
$I->seeElement(Payments_Data::$customer_Payment_Code_Locator);
$I->see(Payments_Data::$description);
$I->seeElement(Payments_Data::$description_Locator);
$I->see(Payments_Data::$category);
$I->seeElement(Payments_Data::$category_Locator);
$I->see(Payments_Data::$department);
$I->seeElement(Payments_Data::$department_Locator);
$I->see(Payments_Data::$GL_Code);
$I->seeElement(Payments_Data::$GL_Code_Locator);
$I->see(Payments_Data::$disbursement_Code);
$I->seeElement(Payments_Data::$disbursement_Code_Locator);
$I->see(Payments_Data::$quantity);
$I->seeElement(Payments_Data::$quantity_Locator);
$I->see(Payments_Data::$rate);
$I->seeElement(Payments_Data::$rate_Locator);
$I->see(Payments_Data::$recurring);
$I->seeElement(Payments_Data::$recurring_Locator);
$I->checkOption(Payments_Data::$recurring_Locator);
$Base->checkFrequencyElement();
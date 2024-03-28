<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\Payments_Data;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$I->wantTo('see UI elements on Payments page');
$Base = new BaseFunctionsPage($I);
$I->click(Payments_Data::$payment_Menu); //click on Payments menu
$I->click(Payments_Data::$payments_Submenu); // click on Payments submenu
$I->seeInCurrentUrl(Payments_Data::$payments_Page_URL);
$Base->seeControlElement(Payments_Data::$payments, Payments_Data::$payments_Table_Header, Payments_Data::$payment_Table_Header_Original);
$I->seeElement(Payments_Data::$payments_Filter_Button);
$I->seeElement(Payments_Data::$payments_Clear_Button);


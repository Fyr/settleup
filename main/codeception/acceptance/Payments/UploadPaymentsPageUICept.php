<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\Payments_Data;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$I->wantTo('see UI elements on Upload Compensations page');

$I->click(Payments_Data::$payment_Menu); //click on Payments menu
$I->click(Payments_Data::$upload_Payments_Submenu); //click on Upload Payments submenu
$I->seeInCurrentUrl(Payments_Data::$upload_Payments_Page_URL);
$I->see(Payments_Data::$upload_Payments);
$I->see(Payments_Data::$title);
$I->seeElement(Payments_Data::$title_locator);
$I->see(Payments_Data::$file);
$I->seeElement(Payments_Data::$file_Locator);
$I->seeElement(Payments_Data::$submit_Button);
$I->see(Payments_Data::$supported_Types);

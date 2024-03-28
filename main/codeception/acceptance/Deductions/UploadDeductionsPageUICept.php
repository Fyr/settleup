<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\Deduction_Data;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$Base = new BaseFunctionsPage($I);
$I->wantTo('see UI elements on Upload Deductions page');
$I->click(Deduction_Data::$deductions_Menu); //click on Deductions menu
$I->click(Deduction_Data::$upload_Deductions_Submenu); //click on Upload Deductions submenu
$I->seeInCurrentUrl(Deduction_Data::$upload_Deductions_Menu_Link);
$I->see(Deduction_Data::$upload_Deductions);
$I->see(Deduction_Data::$title);
$I->seeElement(Deduction_Data::$title_locator);
$I->see(Deduction_Data::$file);
$I->seeElement(Deduction_Data::$file_Locator);
$I->see(Deduction_Data::$supported_Types);
<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\Deduction_Data;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$Base = new BaseFunctionsPage($I);

$I->wantTo('see UI elements on Deductions page');
$I->click(Deduction_Data::$deductions_Menu); //click on Deductions menu
$I->click(Deduction_Data::$deduction_Submenu); // click on Deductions submenu
$I->seeInCurrentUrl(Deduction_Data::$deductions_Menu_Link);
$Base->seeControlElement(Deduction_Data::$deductions, Deduction_Data::$deductions_Header_Table, Deduction_Data::$deductionTable);
$I->seeElement(Deduction_Data::$deductions_Filter_Button);
$I->seeElement(Deduction_Data::$deductions_Clear_Button);
<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\Deduction_Data;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$I->wantTo('see Deductions menu');

$I->see(Deduction_Data::$deductions, Deduction_Data::$deductions_Dropdown_Menu);
$I->click(Deduction_Data::$deductions_Menu);
$I->seeLink(Deduction_Data::$deductions, $I->getBaseUrl() . Deduction_Data::$deductions_Menu_Link);
$I->seeLink(Deduction_Data::$upload_Deductions,$I->getBaseUrl() . Deduction_Data::$upload_Deductions_Menu_Link);
$I->seeLink(Deduction_Data::$master_Deduction_Templates,$I->getBaseUrl() . Deduction_Data::$master_Deduction_Templates_Menu_Link);
$I->seeLink(Deduction_Data::$create_Master_deduction_template,$I->getBaseUrl() . Deduction_Data::$create_Master_Deduction_Templates_Menu_Link);
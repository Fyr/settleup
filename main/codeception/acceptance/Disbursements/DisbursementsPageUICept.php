<?php 

use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\Disbursement_Data;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$Base = new BaseFunctionsPage($I);
$I->wantTo('see UI elements on Disbursement page');
$I->click(Disbursement_Data::$disbursements_Menu);
$I->seeCurrentUrlEquals(Disbursement_Data::$disbursement_Page_URL);
$Base->seeControlElement(Disbursement_Data::$disbursements, Disbursement_Data::$disbursements_Settlement_Cycle_Header_Table, Disbursement_Data::$disbursements_Settlement_Cycle_Header_Table_Original);
$processTable=$Base->getTextElement(Disbursement_Data::$disbursements_Header_Table);
$Base->checkArrayDiff(Disbursement_Data::$disbursements_Header_Table_Original,$processTable);
$I->see(Disbursement_Data::$filter_Name_Button, Disbursement_Data::$filter_Button);
$I->seeElement(Disbursement_Data::$filter_Button);//check visible 'Filter' button
$I->see(Disbursement_Data::$clear_Name_Button, Disbursement_Data::$clear_Button);
$I->seeElement(Disbursement_Data::$clear_Button); //check visible 'Clear' button

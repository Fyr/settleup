<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\Settlement_Data;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$Base = new BaseFunctionsPage($I);
$I->wantTo('see UI elements on settlement page');
$I->click(Settlement_Data::$settlement_Menu);
$I->seeCurrentUrlEquals(Settlement_Data::$settlement_URL);
$Base->seeControlElement(Settlement_Data::$settlement, Settlement_Data::$settlement_Cycle_Header_Table, Settlement_Data::$settlement_Cycle_Header_Table_Original);
// if cycle has Verified or Processed status then check a visible processing table
if($Base->grabTextFromElement(Settlement_Data::$settlement_Verify_Button) != "Verify")
{
    $processTable=$Base->getTextElement(Settlement_Data::$settlement_Process_Header_Table);
    $Base->checkArrayDiff(Settlement_Data::$settlement_Process_Header_Table_Original,$processTable);
}

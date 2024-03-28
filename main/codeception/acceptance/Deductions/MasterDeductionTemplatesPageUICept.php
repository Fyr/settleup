<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\Deduction_Data;
use Codeception\Module\BaseSelectors;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$Base = new BaseFunctionsPage($I);
$I->wantTo('see UI elements on Master Deduction Templates page');

$I->click(Deduction_Data::$deductions_Menu); //click on Deductions menu
$I->click(Deduction_Data::$master_Deduction_Templates_Submenu); //click on Master Deduction Templates submenu
$I->seeInCurrentUrl(Deduction_Data::$master_Deduction_Templates_Menu_Link);
$I->see(Deduction_Data::$master_Deduction_Templates,BaseSelectors::$page_Header);
$recordValue=$Base->getTextElement(BaseSelectors::$selector_Records);
$Base->checkArrayDiff($recordValue,BaseSelectors::$records);
$headerTableValue=$Base->getTextElement(Deduction_Data::$deduction_Template_Header_Table);
$Base->checkArrayDiff($headerTableValue, Deduction_Data::$deduction_Template_Header_Table_Original);
$I->seeElement(Deduction_Data::$deductions_Filter_Button);
$I->seeElement(Deduction_Data::$deductions_Clear_Button); //check visible 'Clear' button
$I->seeElement(Deduction_Data::$delete_Selected_Button); //check visible 'Delete Selected' button
$I->seeElement(Deduction_Data::$create_New_Deduction_Template_button); //check visible '+Create New' button
$I->click(Deduction_Data::$delete_Selected_Button);
$I->waitForElementVisible('#confirm-modal');
$I->see(Deduction_Data::$title_Confirm_Deletion_popup,BaseSelectors::$page_Header);
$I->see(Deduction_Data::$description_Line1_Confirm_Deletion_popup,Deduction_Data::$body_Confirm_Deletion_popup);
$I->see(Deduction_Data::$description_Line2_Confirm_Deletion_popup,Deduction_Data::$body_Confirm_Deletion_popup);
$I->seeElement(Deduction_Data::$close_Button_On_Confirm_Deletion_popup); // close button
$I->seeElement(Deduction_Data::$yes_Button_On_Confirm_Deletion_popup); //YES button
$I->seeElement(Deduction_Data::$no_Button_On_Confirm_Deletion_popup); //NO button
$I->click(Deduction_Data::$close_Button_On_Confirm_Deletion_popup);//close popup
$I->click(Deduction_Data::$create_New_Deduction_Template_button); //click '+Create New' button
$I->seeInCurrentUrl(Deduction_Data::$create_Master_Deduction_Templates_Menu_Link);
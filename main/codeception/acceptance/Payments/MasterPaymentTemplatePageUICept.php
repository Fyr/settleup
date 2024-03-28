<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\Payments_Data;
use Codeception\Module\BaseSelectors;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$Base = new BaseFunctionsPage($I);

$I->wantTo('see UI elements on Master Compensation Templates');
$I->click(Payments_Data::$payment_Menu); //click on Payments menu
$I->click(Payments_Data::$master_Payment_Template_Submenu); //click on Master Payment Templates submenu
$I->seeInCurrentUrl(Payments_Data::$master_Payments_Template_URL);
$I->see(Payments_Data::$master_Payment_Templates,BaseSelectors::$page_Header);
$recordValue=$Base->getTextElement(BaseSelectors::$selector_Records);
$Base->checkArrayDiff($recordValue,BaseSelectors::$records);
$tableValue=$Base->getTextElement(Payments_Data::$payments_Template_Header_Popup);
$Base->checkArrayDiff($tableValue,Payments_Data::$payment_Template_Table_Original);
$I->seeElement(Payments_Data::$payments_Filter_Button);//check visible 'Filter' button
$I->seeElement(Payments_Data::$payments_Clear_Button); //check visible 'Clear' button
$I->seeElement(Payments_Data::$delete_Selected_Button); //check visible 'Delete Selected' button
$I->seeElement(Payments_Data::$create_New_Button); //check visible '+Create New' button
$I->click(Payments_Data::$delete_Selected_Button);
$I->waitForElementVisible('#confirm-modal');
$I->see(Payments_Data::$title_Confirm_Deletion_popup,BaseSelectors::$page_Header);
$I->see(Payments_Data::$description_Line1_Confirm_Deletion_popup,Payments_Data::$body_Confirm_Deletion_popup);
$I->see(Payments_Data::$description_Line2_Confirm_Deletion_popup,Payments_Data::$body_Confirm_Deletion_popup);
$I->seeElement(Payments_Data::$close_Button_On_Confirm_Deletion_popup); // close button
$I->seeElement(Payments_Data::$yes_Button_On_Confirm_Deletion_popup); //YES button
$I->seeElement(Payments_Data::$no_Button_On_Confirm_Deletion_popup); //NO button
$I->click(Payments_Data::$close_Button_On_Confirm_Deletion_popup);//close popup

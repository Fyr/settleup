<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\System_Data;
use Codeception\Module\BaseSelectors;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$Base = new BaseFunctionsPage($I);
$I->wantTo('see System menu');
$I->click(System_Data::$system_Menu);
$I->click(System_Data::$system_Users_Submenu);
$I->seeInCurrentUrl(System_Data::$users_Page_URL);
$I->see(System_Data::$users, BaseSelectors::$page_Header);

$recordValue=$Base->getTextElement(BaseSelectors::$selector_Records);
$Base->checkArrayDiff($recordValue,BaseSelectors::$records);
$tableValue=$Base->getTextElement(System_Data::$users_header_Table);
$Base->checkArrayDiff($tableValue,System_Data::$users_header_Table_Original);
$I->see(System_Data::$filter_Name_Button, System_Data::$customer_Filter_Button);
$I->seeElement(System_Data::$customer_Filter_Button);//check visible 'Filter' button
$I->see(System_Data::$Clear_Name_Button, System_Data::$customer_Clear_Button);
$I->seeElement(System_Data::$customer_Clear_Button); //check visible 'Clear' button
$I->see(System_Data::$add_User_Name_Button, System_Data::$add_User_Button);
$I->seeElement(System_Data::$add_User_Button); //check visible 'Add User' button
$I->see(System_Data::$delete_Selected_Users_Name_Button, System_Data::$delete_Selected_Users_Button);
$I->seeElement(System_Data::$delete_Selected_Users_Button); //check visible 'Delete SElected' button

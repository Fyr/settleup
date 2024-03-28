<?php
use Codeception\Module\BaseSelectors;
use AcceptanceTester\LoggedUserSteps;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$I->wantTo('see UI elements on System page');
$Base = new BaseFunctionsPage($I);

$I->click(\Codeception\Module\System_Data::$system_Menu); //click on System menu
$I->click(\Codeception\Module\System_Data::$system_Users_Submenu); // click on Users submenu
$I->seeInCurrentUrl(\Codeception\Module\System_Data::$users_Page_URL);
$I->see(\Codeception\Module\System_Data::$users, BaseSelectors::$page_Header);
$recordValue=$Base->getTextElement(BaseSelectors::$selector_Records);
$Base->checkArrayDiff($recordValue,BaseSelectors::$records);
$tableValue=$Base->getTextElement(\Codeception\Module\System_Data::$users_header_Table);
$Base->checkArrayDiff($tableValue,\Codeception\Module\System_Data::$users_header_Table_Original);
$I->see(\Codeception\Module\System_Data::$filter_Name_Button,\Codeception\Module\System_Data::$customer_Filter_Button);
$I->seeElement(\Codeception\Module\System_Data::$customer_Filter_Button);//check visible 'Filter' button
$I->see(\Codeception\Module\System_Data::$Clear_Name_Button, \Codeception\Module\System_Data::$customer_Clear_Button);
$I->seeElement(\Codeception\Module\System_Data::$customer_Clear_Button); //check visible 'Clear' button
$I->seeElement(\Codeception\Module\System_Data::$add_User_Button); //check visible '+Add User' button
$I->see(\Codeception\Module\System_Data::$add_User_Name_Button, \Codeception\Module\System_Data::$add_User_Button);
$I->click(\Codeception\Module\System_Data::$add_User_Button); // click on '+ Create New' button
$I->seeInCurrentUrl(\Codeception\Module\System_Data::$create_User_Page_URL);
$I->moveBack();
$I->seeElement(\Codeception\Module\System_Data::$delete_Selected_Users_Button); //check visible 'Delete Selected' button
$I->see(\Codeception\Module\System_Data::$delete_Selected_Users_Name_Button, \Codeception\Module\System_Data::$delete_Selected_Users_Button);
$I->see("Edit");
$I->see("Delete");


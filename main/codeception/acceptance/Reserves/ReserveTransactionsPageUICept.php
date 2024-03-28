<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\Reserves_Data;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');

$Base = new BaseFunctionsPage($I);

$I->wantTo('see UI elements on the Reserve Transactions page');
$I->click(Reserves_Data::$reserves_Menu); //click on Reserves menu
$I->click(Reserves_Data::$reserve_Transactions_Submenu); // click on Reserve Transactions submenu
$I->seeInCurrentUrl(Reserves_Data::$reserve_Transactions_Menu_URL);
$Base->seeControlElement(Reserves_Data::$reserve_Transactions, Reserves_Data::$reserve_Transaction_Header_Table, Reserves_Data::$reserve_Transaction_Header_Table_Original);
$I->seeElement(Reserves_Data::$reserves_Filter_Button);
$I->seeElement(Reserves_Data::$reserves_Clear_Button);
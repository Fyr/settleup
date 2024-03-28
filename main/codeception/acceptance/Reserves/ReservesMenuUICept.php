<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\Reserves_Data;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');

$I->wantTo('see Reserves menu');
$I->see(Reserves_Data::$reserves,Reserves_Data::$reserves_Dropdown_Menu);
$I->click(Reserves_Data::$reserves_Menu);
$I->seeLink(Reserves_Data::$reserve_Transactions,$I->getBaseUrl() . Reserves_Data::$reserve_Transactions_Menu_URL);
$I->seeLink(Reserves_Data::$vendor_Reserve_Accounts,$I->getBaseUrl() . Reserves_Data::$vendor_Reserve_Accounts_Menu_URL);
$I->seeLink(Reserves_Data::$create_Vendor_Reserve_Account,$I->getBaseUrl() . Reserves_Data::$create_Vendor_Reserve_Account_Menu_URL);
$I->seeLink(Reserves_Data::$contractor_Reserve_Accounts, $I->getBaseUrl() . Reserves_Data::$contractor_Reserve_Accounts_Menu_URL);
$I->seeLink(Reserves_Data::$create_Contractor_Reserve_Account, $I->getBaseUrl() . Reserves_Data::$create_Contractor_Reserve_Account_Menu_URL);
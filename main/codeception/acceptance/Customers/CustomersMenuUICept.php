<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\Customers_Data;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$I->wantTo('see Customers menu');
$I->see(Customers_Data::$customers, Customers_Data::$customer_Menu_Dropdown);
$I->click(Customers_Data::$customers_Menu);
$I->seeLink(Customers_Data::$customers, $I->getBaseUrl() . Customers_Data::$customer_Page_URL);
$I->seeLink(Customers_Data::$settlement_Info, $I->getBaseUrl() . Customers_Data::$settlement_Info_Page_URL);
$I->seeLink(Customers_Data::$bank_Accounts, $I->getBaseUrl() . Customers_Data::$bank_Account_Page_URL);
$I->seeLink(Customers_Data::$escrow_Accounts, $I->getBaseUrl() . Customers_Data::$escrow_Account_Page_URL);
$I->seeLink(Customers_Data::$custom_Field, $I->getBaseUrl() . Customers_Data::$custom_Field_Descriptions_Page_URL);

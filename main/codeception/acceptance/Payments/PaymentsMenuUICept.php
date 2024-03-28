<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\Payments_Data;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$I->wantTo('see Compensation menu');
$I->see(Payments_Data::$payments,Payments_Data::$payments_Dropdown_Menu);
$I->click(Payments_Data::$payment_Menu);
$I->seeLink(Payments_Data::$payments,$I->getBaseUrl(). Payments_Data::$payments_Menu_Link);
$I->seeLink(Payments_Data::$upload_Payments,$I->getBaseUrl() . Payments_Data::$upload_Payments_Menu_Link);
$I->seeLink(Payments_Data::$master_Payment_Templates,$I->getBaseUrl() . Payments_Data::$master_Payment_Templates_Menu_Link);
$I->seeLink(Payments_Data::$create_Master_Payment_Template, $I->getBaseUrl() . Payments_Data::$create_Master_Payment_Template_Menu_Link);
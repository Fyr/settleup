<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\System_Data;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$I->wantTo('see System menu');
$I->see(System_Data::$system, System_Data::$system_Dropdown_Menu);
$I->click(System_Data::$system_Menu);
$I->seeLink(System_Data::$users);
$I->seeLink(System_Data::$states);


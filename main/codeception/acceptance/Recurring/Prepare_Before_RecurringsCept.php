<?php
// @group recurring
use Codeception\Module\Input_Data;
use AcceptanceTester\LoggedUserSteps;

$I = new LoggedUserSteps($scenario);
$I->wantTo('Delete all old test customers before create recurrings');

$Customer = new CustomersPage($I);
$Customer->deleteTestCustomers(Input_Data::$customer2);


<?php
/*
use Codeception\Module\LoginPage;
$I = new AcceptanceTester($scenario);
$U = new LoginPage($I);

$I->wantTo('login as non exist user');
$U->login("test@test.co","123");
$I->see("Your email or password does not match our records. Please verify and try again.",'strong');

$I->wantTo('login with empty Email and Password fields');
$U->login("","");
$I->see("Value is required and can't be empty","//div[1]/div/ul/li");
$I->see("Value is required and can't be empty","//div[2]/div/ul/li");
*/
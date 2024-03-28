<?php
use AcceptanceTester\LoggedUserSteps;


$I = new AcceptanceTester($scenario);
$scenario->group('UI');
$I->wantTo('Checking forgot password when user enters incorrect and correct email');
$I->amOnPage("auth/login");
$I->click('Forgot password?');
$I->seeCurrentUrlEquals("/auth/forgot");
$I->see("Please, enter your email to reset password:");
$I->see("Email");
$I->seeElement(['css' => 'input[type=submit][name=submit]']);
# clicking 'Send' button when field 'Email' is empty
$I->click('Send');
$I->see("Value is required and can't be empty");
# clicking 'Send' button when field 'Email' has non exist email
$I->fillField("Email", "test@test.co");
$I->click('Send');
$I->see("That E-mail doesn't belong to any registered users in this system.");
# clicking 'Send' button when entered an exist email
$I->fillField("Email", \Codeception\Module\Input_Data::$emailValue);
$I->click('Send');
$I->see("We sent to you email with instructions. Please, check it.", 'strong');
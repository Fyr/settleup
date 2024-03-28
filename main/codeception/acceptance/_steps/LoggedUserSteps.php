<?php
namespace AcceptanceTester;
use Codeception\Module\Input_Data;
use Page\LoginPage;

class LoggedUserSteps extends \AcceptanceTester
{
    // include url of current page
    public static $URL = 'auth/login';
    //fields
    public static $titleText = "Login";
    public static $titleElem = ".active a";
    public static $bodyElem =  "div#body";
    public static $usernameTitle = "Tanya Admin";
    public static $emailField = "Email";
    public static $passwordField = "Password";
    //submit button
    public static $loginButton = array("link" => "Log In", "context" => "#submit");
    public static $loginLink = "a[href='pttp://pfleet-qa.tula.co/auth/login']";

    public function __construct($scenario)
    {
        parent::__construct($scenario);
        $this->login();
    }
    public function login()
    {
        $I = $this;
        $I->amOnPage(LoggedUserSteps::$URL);
        $I->fillField(LoggedUserSteps::$emailField, Input_Data::$emailValue);
        $I->fillField(LoggedUserSteps::$passwordField, Input_Data::$passwordValue);
        $I->click(LoggedUserSteps::$loginButton['context']);
    }
}
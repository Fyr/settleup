<?php
//namespace Page;
namespace Codeception\Module;

use Codeception\Module\Input_Data;

class LoginPage
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


    public function login($email)
    {
        $I = $this;
        $I->amOnPage(LoginPage::$URL);
        $I->fillField(LoginPage::$emailField, Input_Data::$emailValue);
        $I->fillField(LoginPage::$passwordField, Input_Data::$passwordValue);
        $I->click(LoginPage::$loginButton['context']);
        return $this;
    }
    public function logout()
    {
        $this->user->click("a.dropdown-toggle span.caret");
        $this->user->click("Log Out");
    }

}

/*class LoginPage extends \AcceptanceTester
{
    // include url of current page
    public static $URL = 'auth/login';
    public static $titleText = "Login";
    public static $titleElem = ".active a";
    public static $bodyElem =  "div#body";
    public static $usernameTitle = "Tanya Admin";

    public static $emailField = "Email";
    public static $emailValue = "admin@test.com";
    public static $passwordField = "Password";
    public static $passwordValue = "12345";

    public static $loginButton = array("link" => "Log In", "context" => "#submit");
    public static $loginLink = "a[href='pttp://pfleet-qa.tula.co/auth/login']";

    protected $user;


    public function __construct(Scenario $scenario)
    {
        parent::__construct($scenario);
        $this->login();
    }

    /**
     * performs user login

    public function login()
    {
        $I = $this;
        LoginPage::of($I)->login(BasePage::$email, BasePage::$pass);
    }

/*
    public function __construct(AcceptanceTester $I)
    {
        $this->user = $I;
    }
/*
    public function _before(\Codeception\TestCase $test, $email, $password)
    {
        $test->amOnPage(LoginPage::$URL);
        $test->fillField(LoginPage::$emailField, $email);
        $test->fillField(LoginPage::$passwordField, $password);
        $test->click(LoginPage::$loginButton['context']);
    }
    public function login($email, $password)
    {

//       $I->amOnPage(LoginPage::$URL);
  //      $I->fillField(LoginPage::$emailField, $email);
    //    $I->fillField(LoginPage::$passwordField, $password);
      //  $I->click(LoginPage::$loginButton['context']);

        $this->user->amOnPage(LoginPage::$URL);
       $this->user->fillField(LoginPage::$emailField, $email);
       $this->user->fillField(LoginPage::$passwordField, $password);
        $this->user->click(LoginPage::$loginButton['context']);
    }
*/


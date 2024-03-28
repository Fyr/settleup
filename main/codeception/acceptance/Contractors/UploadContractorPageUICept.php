<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\BaseSelectors;
use Codeception\Module\Contractors_Data;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$Base = new BaseFunctionsPage($I);
$I->wantTo('see UI elements on Upload Contractor page');

$I->click(Contractors_Data::$contractors_Menu); //click on Contractors menu
$I->click(Contractors_Data::$upload_Button); //click on 'Upload' button
$I->seeInCurrentUrl(Contractors_Data::$upload_Contractor_Page_URL);
$I->see(Contractors_Data::$upload_Contractor_Title_Page);
$I->see(Contractors_Data::$title_Name_Field);
$I->seeElement(Contractors_Data::$title_Locator_Field);
$I->see(Contractors_Data::$file_Name_Field);
$I->seeElement(Contractors_Data::$file_Locator_Field);
$I->seeElement(Contractors_Data::$save_Button);
$I->see(Contractors_Data::$supported_Types);


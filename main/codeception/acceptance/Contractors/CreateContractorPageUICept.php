<?php
use AcceptanceTester\LoggedUserSteps;
use Codeception\Module\BaseSelectors;
use Codeception\Module\Contractors_Data;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$Base = new BaseFunctionsPage($I);
$I->wantTo('see UI elements on Create Contractor page');

$I->click(Contractors_Data::$contractors_Menu); //click on Contractors menu
$I->click(Contractors_Data::$create_New_Contractor_Button); // click on '+ Create New' button
$I->seeInCurrentUrl(Contractors_Data::$contractor_New_URL);
$I->see(Contractors_Data::$create_Contractor_Title_Page);
$I->see(Contractors_Data::$bank_Account_Name_Button);
$I->seeElement(Contractors_Data::$contractor_Bank_Account_Button);
$I->see(Contractors_Data::$reserve_Account_Name_Button);
$I->seeElement(Contractors_Data::$reserve_Account_Button);
$I->see(Contractors_Data::$contractor_ID_Name);
$I->seeElement(Contractors_Data::$contractor_ID_Field);
$I->see(Contractors_Data::$contractor_Company_Name);
$I->seeElement(Contractors_Data::$contractor_Company_Name_Field);
$I->see(Contractors_Data::$contractor_First_Name);
$I->seeElement(Contractors_Data::$contractor_First_Name_Field);
$I->see(Contractors_Data::$contractor_Middle_Initial);
$I->seeElement(Contractors_Data::$contractor_Middle_Initial_field);
$I->see(Contractors_Data::$contractor_Last_Name);
$I->seeElement(Contractors_Data::$contractor_Last_Name_Field);
$I->see(Contractors_Data::$contractor_Tax_ID);
$I->seeElement(Contractors_Data::$contractor_Tax_ID_Field);
$I->see(Contractors_Data::$contractor_Social_Security_ID);
$I->seeElement(Contractors_Data::$contractor_Social_Security_ID_Field);
$I->see(Contractors_Data::$contractor_DOB);
$I->seeElement(Contractors_Data::$contractor_DOB_Field);
$I->seeElement(Contractors_Data::$contractor_Drivers_License_Field);
$I->see(Contractors_Data::$contractor_State_Of_Issuance);
$I->seeElement(Contractors_Data::$contractor_State_Of_Issuance_Field);
$I->see(Contractors_Data::$contractor_Expires);
$I->seeElement(Contractors_Data::$contractor_Expires_Field);
$I->see(Contractors_Data::$contractor_Classification);
$I->seeElement(Contractors_Data::$contractor_Classification_Field);
$I->see(Contractors_Data::$contractor_Division);
$I->seeElement(Contractors_Data::$contractor_Division_Field);
$I->see(Contractors_Data::$contractor_Department);
$I->seeElement(Contractors_Data::$contractor_Department_Field);
$I->see(Contractors_Data::$contractor_Route);
$I->seeElement(Contractors_Data::$contractor_Route_Field);
$I->see(Contractors_Data::$contractor_Status);
$I->seeElement(Contractors_Data::$contractor_Status_Field);
$I->see(Contractors_Data::$contractor_Gender);
$I->seeElement(Contractors_Data::$contractor_Gender_Field);
$I->see(Contractors_Data::$contractor_Start_Date);
$I->seeElement(Contractors_Data::$contractor_Start_Date_Field);
$I->see(Contractors_Data::$contractor_Termination_Date);
$I->seeElement(Contractors_Data::$contractor_Termination_Date_Field);
$I->see(Contractors_Data::$contractor_Restart_Date);
$I->seeElement(Contractors_Data::$contractor_Restart_Date_Field);
$I->see(Contractors_Data::$contractor_Address1);
$I->see(Contractors_Data::$contractor_Address2);
$I->see(Contractors_Data::$contractor_City);
$I->see(Contractors_Data::$contractor_State);
$I->see(Contractors_Data::$contractor_Zip);
$I->see(Contractors_Data::$contractor_Phone);
$I->see(Contractors_Data::$contractor_Fax);
$I->see(Contractors_Data::$contractor_Email);
$I->see(Contractors_Data::$contractor_Settlement_Delivery);
$I->seeElement(Contractors_Data::$contractor_Settlement_Delivery_Field);
$I->see(Contractors_Data::$contractor_Deduction_Priority);
$I->see(Contractors_Data::$contractor_Vendor);
$I->see(Contractors_Data::$contractor_Status);
$I->seeElement(Contractors_Data::$cancel_Button);
$I->seeElement(Contractors_Data::$save_Button);








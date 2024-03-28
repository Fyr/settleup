<?php
use Codeception\Module\BaseSelectors;
use AcceptanceTester\LoggedUserSteps;

$I=new LoggedUserSteps($scenario);
$scenario->group('UI');
$I->wantTo('see logos, main menu and working nav bar');
// Login as admin user
$I->seeElement(BaseSelectors::$logo);
$I->seeElement(BaseSelectors::$select_Customer_Button);
$I->click(BaseSelectors::$select_Customer_Button);
$I->wait(2);
$I->seeElement(BaseSelectors::$select_Customer_Popup);

$I->see('Customer:');
$I->seeElement(BaseSelectors::$select_Customer_Table_In_Popup);
$I->seeElement(BaseSelectors::$close_Button_For_Select_Customer_Popup);
$I->click(BaseSelectors::$close_Button_For_Select_Customer_Popup);

$I->see(\Codeception\Module\Settlement_Data::$settlement,\Codeception\Module\Settlement_Data::$settlement_Menu);
$I->click(\Codeception\Module\Settlement_Data::$settlement_Menu);
$I->seeCurrentUrlEquals(\Codeception\Module\Settlement_Data::$settlement_URL);

$I->see(\Codeception\Module\Payments_Data::$payments,\Codeception\Module\Payments_Data::$payment_Menu);
$I->click(\Codeception\Module\Payments_Data::$payment_Menu);
$I->seeLink(\Codeception\Module\Payments_Data::$payments,$I->getBaseUrl() . \Codeception\Module\Payments_Data::$payments_Menu_Link);
$I->seeLink(\Codeception\Module\Payments_Data::$upload_Payments, $I->getBaseUrl() . \Codeception\Module\Payments_Data::$upload_Payments_Menu_Link);
$I->seeLink(\Codeception\Module\Payments_Data::$master_Payment_Templates, $I->getBaseUrl() . \Codeception\Module\Payments_Data::$master_Payment_Templates_Menu_Link);
$I->seeLink(\Codeception\Module\Payments_Data::$create_Master_Payment_Template,$I->getBaseUrl() . \Codeception\Module\Payments_Data::$create_Master_Payment_Template_Menu_Link);

$I->see(\Codeception\Module\Deduction_Data::$deductions, \Codeception\Module\Deduction_Data::$deductions_Menu);
$I->click(\Codeception\Module\Deduction_Data::$deductions_Menu);
$I->seeLink(\Codeception\Module\Deduction_Data::$deductions, $I->getBaseUrl() . \Codeception\Module\Deduction_Data::$deductions_Menu_Link);
$I->seeLink(\Codeception\Module\Deduction_Data::$upload_Deductions, $I->getBaseUrl() . \Codeception\Module\Deduction_Data::$upload_Deductions_Menu_Link);
$I->seeLink(\Codeception\Module\Deduction_Data::$master_Deduction_Templates, $I->getBaseUrl() . \Codeception\Module\Deduction_Data::$master_Deduction_Templates_Menu_Link);
$I->seeLink(\Codeception\Module\Deduction_Data::$create_Master_deduction_template, $I->getBaseUrl() . \Codeception\Module\Deduction_Data::$create_Master_Deduction_Templates_Menu_Link);

$I->see(\Codeception\Module\Reserves_Data::$reserves, \Codeception\Module\Reserves_Data::$reserves_Menu);
$I->click(\Codeception\Module\Reserves_Data::$reserves_Menu);
$I->seeLink(\Codeception\Module\Reserves_Data::$reserve_Transactions, $I->getBaseUrl() . \Codeception\Module\Reserves_Data::$reserve_Transactions_Menu_URL );
$I->seeLink(\Codeception\Module\Reserves_Data::$vendor_Reserve_Accounts, $I->getBaseUrl() . \Codeception\Module\Reserves_Data::$vendor_Reserve_Accounts_Menu_URL);
$I->seeLink(\Codeception\Module\Reserves_Data::$create_Vendor_Reserve_Account, $I->getBaseUrl() . \Codeception\Module\Reserves_Data::$create_Vendor_Reserve_Account_Menu_URL);
$I->seeLink(\Codeception\Module\Reserves_Data::$contractor_Reserve_Accounts, $I->getBaseUrl() . \Codeception\Module\Reserves_Data::$contractor_Reserve_Accounts_Menu_URL);
$I->seeLink(\Codeception\Module\Reserves_Data::$create_Contractor_Reserve_Account, $I->getBaseUrl() . \Codeception\Module\Reserves_Data::$create_Contractor_Reserve_Account_Menu_URL);

$I->see(\Codeception\Module\Disbursement_Data::$disbursements);

$I->see(\Codeception\Module\Customers_Data::$customers, \Codeception\Module\Customers_Data::$customers_Menu);
$I->click(\Codeception\Module\Customers_Data::$customers_Menu);
$I->seeLink(\Codeception\Module\Customers_Data::$customers, $I->getBaseUrl() . \Codeception\Module\Customers_Data::$customer_Page_URL);
$I->seeLink(\Codeception\Module\Customers_Data::$settlement_Info, $I->getBaseUrl() . \Codeception\Module\Customers_Data::$settlement_Info_Page_URL);
$I->seeLink(\Codeception\Module\Customers_Data::$bank_Accounts, $I->getBaseUrl(). \Codeception\Module\Customers_Data::$bank_Account_Page_URL);
$I->seeLink(\Codeception\Module\Customers_Data::$escrow_Accounts, $I->getBaseUrl() . \Codeception\Module\Customers_Data::$escrow_Account_Page_URL);

$I->see(\Codeception\Module\Contractors_Data::$contractors);
$I->see(\Codeception\Module\Vendors_data::$vendor_Title_Page);
$I->see('Reporting');

$I->see(\Codeception\Module\System_Data::$system, \Codeception\Module\System_Data::$system_Dropdown_Menu);
$I->click(\Codeception\Module\System_Data::$system_Menu);
$I->seeLink(\Codeception\Module\System_Data::$users, $I->getBaseUrl() . \Codeception\Module\System_Data::$users_Page_URL);
$I->seeLink(\Codeception\Module\System_Data::$states, $I->getBaseUrl() . \Codeception\Module\System_Data::$states_Page_URL);

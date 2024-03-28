<?php
// @group recurring
use Codeception\Module\Input_Data;
use AcceptanceTester\LoggedUserSteps;

$I = new LoggedUserSteps($scenario);
$I->wantTo('check the assigned recurring compensations and deductions for settlement cycle is Semi-Monthly');

$Customer = new CustomersPage($I);
$Payment = new PaymentsPage($I);
$Deduction = new DeductionsPage($I);
$Settlement = new SettlementPage($I);

//go to the Carriers page and select carrier if it is exist or create it
$Customer->customerCreate(Input_Data::$customer2, Input_Data::$customer_Holder_Federal_Tax_ID, Input_Data::$customer2, Input_Data::$customer_Contact, Input_Data::$customer_Terms, Input_Data::$customer_Account_Nickname, Input_Data::$customer_Bank_Routing_ID, Input_Data::$customer_Bank_Account_ID, Input_Data::$customer_Escrow_Account_Holder, Input_Data::$customer_Holder_Federal_Tax_ID, Input_Data::$customer_Bank_Name, Input_Data::$customer_Bank_Routing_Number, Input_Data::$customer_Bank_Account_Number);
//purge data
$Customer->purgeData(Input_Data::$customer2);
//add a new settlement cycle
$Settlement->addSemiSettlement(Input_Data::$settlement__Semi_Monthly_Cycle, Input_Data::$settlement_Start_Date, Input_Data::$settlement_Processing_Deadline, Input_Data::$settlement_Disbursement_Terms, '5th', '20th');
//verifying a settlement cycle
$Settlement->verifySettlementCycle();
//Add payments
$Payment->addPayment(Input_Data::$contractor1['ID']);
//"Add deductions
$Deduction->addDeduction(Input_Data::$contractor1['ID']);
for ($j = 0; $j < 5; $j++) {
    $Settlement->verifySettlementCycle();
    $Settlement->checkRecurringWeekDays(Input_Data::$recurring_Template1);
    $Settlement->checkBiweeklyRecurring(Input_Data::$recurring_Template2);
    $Settlement->checkRecurringWeekDays(Input_Data::$recurring_Template3);
    $Settlement->checkRecurringDays(Input_Data::$recurring_Template4);
    $Settlement->checkRecurringDays(Input_Data::$recurring_Template5);
    $Settlement->processSettlementCycle();
    $Settlement->approveSettlementCycle();
}

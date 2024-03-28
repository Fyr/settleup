<?php
use Codeception\Module\Input_Data;
use Codeception\Module\BaseSelectors;
use AcceptanceTester\LoggedUserSteps;

$I=new LoggedUserSteps($scenario);
$scenario->group('settlement');
$I->wantTo('settlement process when Compensations more than Deductions + Contributions by three contractors');
$BaseFunctions = new BaseFunctionsPage($I);
$Customer = new CustomersPage($I);
$Vendor = new VendorsPage($I);
$Contractor = new ContractorsPage($I);
$Reserves = new ReservesPage($I);
$Payment = new PaymentsPage($I);
$Deduction = new DeductionsPage($I);
$Settlement = new SettlementPage($I);
$Disbursement = new DisbursementsPage($I);
//go to the Carriers page and select carrier if it is exist or create it
$Customer->customerCreate(Input_Data::$customer,Input_Data::$customer_Holder_Federal_Tax_ID, Input_Data::$customer, Input_Data::$customer_Contact, Input_Data::$customer_Terms, Input_Data::$customer_Account_Nickname, Input_Data::$customer_Bank_Routing_ID, Input_Data::$customer_Bank_Account_ID, Input_Data::$customer_Escrow_Account_Holder, Input_Data::$customer_Holder_Federal_Tax_ID, Input_Data::$customer_Bank_Name, Input_Data::$customer_Bank_Routing_Number, Input_Data::$customer_Bank_Account_Number);
//purge data
$Customer->purgeData(Input_Data::$customer);
//verifying a settlement cycle
$Settlement->addSettlement(Input_Data::$settlement_Weekly_Cycle, Input_Data::$settlement_Start_Date, Input_Data::$settlement_Processing_Deadline, Input_Data::$settlement_Disbursement_Terms);
//---------------------------------------------------------------------------------------------------------------------------------
//Change Quantities and Rates for the individual payment templates of 'GHI Trucking and Delivery Service LLC' contractor
for($i=0; $i<count(Input_Data::$payments_Contractor3); $i++){
    $Contractor->editIndividualPaymentTemplate(Input_Data::$contractor3['Contractor_Name'],Input_Data::$payments_Contractor3[$i]);
}
//Change Quantities and Rates for the individual deduction templates of 'GHI Trucking and Delivery Service LLC' contractor
for($i=0; $i<count(Input_Data::$deductions_Contractor3); $i++){
    $Contractor->editIndividualDeductionTemplate(Input_Data::$contractor3['Contractor_Name'],Input_Data::$deductions_Contractor3[$i]);
}
//Add all payments for all contractors
    $Payment->addPayment('All');
//Add all deductions for all contractors
    $Deduction->addDeduction('All');
//-----------------------------------------------------------------------------------------------------------------------------------
//Change Quantity and Rate of added payments to 'ABC Trucking and Delivery Service LLC' contractor on the Payment Details page
for($i=0; $i<count(Input_Data::$payments_Contractor1_Test2); $i++){
    $Payment->editPaymentDetails(Input_Data::$contractor1['Contractor_Name'],Input_Data::$payments_Contractor1_Test2[$i]);
}
//Change Quantity and Rate of added deductions to 'ABC Trucking and Delivery Service LLC' contractor on the Deduction Details page
for($i=0; $i<count(Input_Data::$deductions_Contractor1_Test2); $i++){
    $Deduction->editDeductionDetails(Input_Data::$contractor1['Contractor_Name'], Input_Data::$deductions_Contractor1_Test2[$i]);
}
//Change Quantity and Rate of added payments to 'DEF Trucking and Delivery Service LLC' contractor on the Settlement page
for($i=0; $i<count(Input_Data::$payments_Contractor2_Test2); $i++){
    $Settlement->editPD_DetailsToContractorSettlement(Input_Data::$contractor2['Contractor_Name'],\Codeception\Module\Settlement_Data::$payments_Table, Input_Data::$payments_Contractor2_Test2[$i]);
}
//Change Quantity and Rate of added deductions to 'DEF Trucking and Delivery Service LLC' contractor on the Settlement page
for($i=0; $i<count(Input_Data::$deductions_Contractor2_Test2); $i++){
    $Settlement->editPD_DetailsToContractorSettlement(Input_Data::$contractor2['Contractor_Name'],\Codeception\Module\Settlement_Data::$deductions_Table, Input_Data::$deductions_Contractor2_Test2[$i]);
}//-----------------------------------------------------------------------------------------------------------------------------------
//adjusting a current balance of reserve accounts
for($i=0; $i<2; $i++){
$Settlement->contractor_Reserve_Account_Adjustment(Input_Data::$contractor1['Contractor_Name'],Input_Data::$contractor1_Reserve_Account_Test2[$i]);
$Settlement->contractor_Reserve_Account_Adjustment(Input_Data::$contractor2['Contractor_Name'],Input_Data::$contractor2_Reserve_Account_Test2[$i]);
$Settlement->contractor_Reserve_Account_Adjustment(Input_Data::$contractor3['Contractor_Name'],Input_Data::$contractor3_Reserve_Account_Test2[$i]);
}
$Settlement->processSettlementCycle();
$Settlement->checkAmountsOfSettlement(Input_Data::$settlement_Summary_Test2);
$Settlement->approveSettlementCycle();
$Disbursement->disbursementsTransactions(Input_Data::create_Disbursement_Array_Test2());
$Reserves->checkEndingBalanceVendorRA(Input_Data::$sum_Reserve_Account_Ending_Balances_Test2);

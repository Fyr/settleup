<?php
namespace Codeception\Module;
class Input_Data
{
    public static $emailValue = "admin@test.com";
    public static $passwordValue = "12345";
    //public static $carrier='Tanya08';
    public static $customer = 'codeceptionTest';
    public static $customer2 = 'RecurringTest';
    public static $vendor1 = 'AON';
    public static $vendor2 = 'P-Fleet';

    public static $customer_Tax_ID = '11-1111111';
    public static $customer_Contact='11111';
    public static $customer_Terms='1';
    public static $customer_Account_Nickname='BA Carrier';
    public static $customer_Bank_Routing_ID='1111222233334444';
    public static $customer_Bank_Account_ID='5555666677778888';
    public static $customer_Escrow_Account_Holder='Test Account';
    public static $customer_Holder_Federal_Tax_ID='22-2222222';
    public static $customer_Bank_Name='Test Name';
    public static $customer_Bank_Routing_Number='111222333';
    public static $customer_Bank_Account_Number='222333885';

    public static $vendor1_ID ='1';
    public static $vendor1_Contractor= '11111';
    public static $vendor1_Federal_Tax_ID = '11-1111111';
    public static $vendor1_Account_Nickname = 'BA Vendor 1';
    public static $vendor1_Bank_Routing_ID = '123456789';
    public static $vendor1_Bank_Account_ID = '5555666677778888';

    public static $vendor2_ID ='2';
    public static $vendor2_Contractor= '22222';
    public static $vendor2_Federal_Tax_ID = '22-2222222';
    public static $vendor2_Account_Nickname = 'BA Vendor 2';
    public static $vendor2_Bank_Routing_ID = '987654321';
    public static $vendor2_Bank_Account_ID = '6666777788885555';

    public static $contractor1 = array('Contractor_Name'=> 'ABC Trucking and Delivery Service LLC',
            'ID' => '001',
            'First_Name'=> 'ABC',
            'Last_Name' => 'Company',
            'Tax_ID' => '11-1111111',
            'Social_Security_ID' => '111-11-1111',
            'Bank_Accounts'=> array(
            array('Account_Nickname' => 'BA Contractor',
                'Payment_Type' => 'ACH',
                'Limit_Type' => 'Percentage',
                'Amount' => '50',
                'Bank_Routing_ID' => '111188883',
                'Bank_Account_ID' => '5555777777778888'),
            array('Account_Nickname' => 'BA Contractor 2',
                'Payment_Type' => 'Check',
                'Limit_Type' => 'Percentage',
                'Amount' => '50',
                'Bank_Routing_ID' => '',
                'Bank_Account_ID' => '')));

    public static $contractor2 = array('Contractor_Name'=> 'DEF Trucking and Delivery Service LLC',
        'ID' => '002',
        'First_Name'=> 'DEF Trucking',
        'Last_Name' => 'Company',
        'Tax_ID' => '22-2222222',
        'Social_Security_ID' => '222-22-2222',
        'Bank_Accounts'=> array(array(
                'Account_Nickname' => 'BA Contractor',
                'Payment_Type' => 'Check',
                'Limit_Type' => 'Amount',
                'Amount' => '50',
                'Bank_Routing_ID' => '222233336',
                'Bank_Account_ID' => '2222222222222228')));

    public static $contractor3 = array('Contractor_Name'=> 'GHI Trucking and Delivery Service LLC',
        'ID' => '003',
        'First_Name'=> 'GHI Trucking',
        'Last_Name' => 'Company',
        'Tax_ID' => '33-3333333',
        'Social_Security_ID' => '333-33-3333',
        'Bank_Accounts'=> array(array(
            'Account_Nickname' => 'BA Contractor',
            'Payment_Type' => 'ACH',
            'Limit_Type' => 'Amount',
            'Amount' => '50',
            'Bank_Routing_ID' => '333333336',
            'Bank_Account_ID' => '3333333333333338')));

    public static $payments_Contractor1_Test1 = array(array('code'=> 'HD', 'quantity'=>'15', 'rate'=>'85.00'),
        array('code'=> 'MG', 'quantity'=>'320', 'rate'=>'1'));

    public static $deductions_Contractor1_Test1 = array(array('code'=> 'AONINS', 'quantity'=>'1', 'rate'=>'50.00'),
        array('code'=> 'FI', 'quantity'=>'1', 'rate'=>'550.00'),
        array('code'=> 'TL', 'quantity'=>'1', 'rate'=>'225.00'));

    public static $payments_Contractor1_Test2 = array(array('code'=> 'HD', 'quantity'=>'15', 'rate'=>'85.00'),
        array('code'=> 'MG', 'quantity'=>'312', 'rate'=>'00.90'));

    public static $deductions_Contractor1_Test2 = array(array('code'=> 'AONINS', 'quantity'=>'1', 'rate'=>'50.00'),
        array('code'=> 'FI', 'quantity'=>'1', 'rate'=>'512.45'),
        array('code'=> 'TL', 'quantity'=>'1', 'rate'=>'225.00'));

    public static $payments_Contractor1_Test3_4 = array(array('code'=> 'HD', 'quantity'=>'8', 'rate'=>'85.00'),
        array('code'=> 'MG', 'quantity'=>'320', 'rate'=>'00.90'));

    public static $payments_Contractor1_Test5 = array(array('code'=> 'HD', 'quantity'=>'8', 'rate'=>'85.00'),
        array('code'=> 'MG', 'quantity'=>'320', 'rate'=>'00.90'));

    public static $payments_Contractor1_Test6 = array(array('code'=> 'HD', 'quantity'=>'8', 'rate'=>'85.00'),
        array('code'=> 'MG', 'quantity'=>'320', 'rate'=>'00.90'));

    public static $payments_Contractor1_Test7_8 = array(array('code'=> 'HD', 'quantity'=>'10', 'rate'=>'85.00'),
        array('code'=> 'MG', 'quantity'=>'168', 'rate'=>'00.90'));

    public static $payments_Contractor1_Test9_10 = array(array('code'=> 'HD', 'quantity'=>'10', 'rate'=>'85.00'),
        array('code'=> 'MG', 'quantity'=>'150', 'rate'=>'00.90'));

    public static $deductions_Contractor1_Test3_4 = array(array('code'=> 'AONINS', 'quantity'=>'1', 'rate'=>'50.00'),
        array('code'=> 'FI', 'quantity'=>'1', 'rate'=>'645.23'),
        array('code'=> 'TL', 'quantity'=>'1', 'rate'=>'250.00'));

    public static $deductions_Contractor1_Test5 = array(array('code'=> 'AONINS', 'quantity'=>'1', 'rate'=>'50.00'),
        array('code'=> 'FI', 'quantity'=>'1', 'rate'=>'1085.23'),
        array('code'=> 'TL', 'quantity'=>'1', 'rate'=>'250.00'));

    public static $deductions_Contractor1_Test6 = array(array('code'=> 'AONINS', 'quantity'=>'1', 'rate'=>'50.00'),
        array('code'=> 'FI', 'quantity'=>'1', 'rate'=>'1085.23'),
        array('code'=> 'TL', 'quantity'=>'1', 'rate'=>'250.00'));

    public static $deductions_Contractor1_Test7_8 = array(array('code'=> 'AONINS', 'quantity'=>'3', 'rate'=>'50.00'),
        array('code'=> 'FI', 'quantity'=>'1', 'rate'=>'898.36'),
        array('code'=> 'TL', 'quantity'=>'1', 'rate'=>'225.00'));

    public static $deductions_Contractor1_Test9_10 = array(array('code'=> 'AONINS', 'quantity'=>'3', 'rate'=>'50.00'),
        array('code'=> 'FI', 'quantity'=>'1', 'rate'=>'905.36'),
        array('code'=> 'TL', 'quantity'=>'1', 'rate'=>'225.00'));

    public static $payments_Contractor2_Test2 = array(array('code'=> 'HD', 'quantity'=>'15', 'rate'=>'85.00'),
        array('code'=> 'MG', 'quantity'=>'319', 'rate'=>'00.90'));

    public static $deductions_Contractor2_Test2 = array(array('code'=> 'AONINS', 'quantity'=>'1', 'rate'=>'50.00'),
        array('code'=> 'FI', 'quantity'=>'1', 'rate'=>'542.23'),
        array('code'=> 'TL', 'quantity'=>'1', 'rate'=>'225.00'));

    public static $payments_Contractor2_Test4 = array(array('code'=> 'HD', 'quantity'=>'8', 'rate'=>'85.00'),
        array('code'=> 'MG', 'quantity'=>'212', 'rate'=>'00.90'));

    public static $deductions_Contractor2_Test4 = array(array('code'=> 'AONINS', 'quantity'=>'1', 'rate'=>'50.00'),
        array('code'=> 'FI', 'quantity'=>'1', 'rate'=>'556.85'),
        array('code'=> 'TL', 'quantity'=>'1', 'rate'=>'225.00'));

    public static $payments_Contractor2_Test6 = array(array('code'=> 'HD', 'quantity'=>'8', 'rate'=>'85.00'),
        array('code'=> 'MG', 'quantity'=>'212', 'rate'=>'00.90'));

    public static $deductions_Contractor2_Test6 = array(array('code'=> 'AONINS', 'quantity'=>'1', 'rate'=>'50.00'),
        array('code'=> 'FI', 'quantity'=>'1', 'rate'=>'556.85'),
        array('code'=> 'TL', 'quantity'=>'1', 'rate'=>'225.00'));

    public static $payments_Contractor2_Test8 = array(array('code'=> 'HD', 'quantity'=>'8', 'rate'=>'85.00'),
        array('code'=> 'MG', 'quantity'=>'212', 'rate'=>'00.90'));

    public static $deductions_Contractor2_Test8 = array(array('code'=> 'AONINS', 'quantity'=>'1', 'rate'=>'50.00'),
        array('code'=> 'FI', 'quantity'=>'1', 'rate'=>'556.85'),
        array('code'=> 'TL', 'quantity'=>'1', 'rate'=>'225.00'));

    public static $payments_Contractor2_Test10 = array(array('code'=> 'HD', 'quantity'=>'10', 'rate'=>'85.00'),
        array('code'=> 'MG', 'quantity'=>'108', 'rate'=>'00.90'));

    public static $deductions_Contractor2_Test10 = array(array('code'=> 'AONINS', 'quantity'=>'3', 'rate'=>'50.00'),
        array('code'=> 'FI', 'quantity'=>'1', 'rate'=>'905.36'),
        array('code'=> 'TL', 'quantity'=>'1', 'rate'=>'225.00'));

    public static $payments_Contractor3 = array(array('code'=> 'HD', 'quantity'=>'15', 'rate'=>'85.00'),
        array('code'=> 'MG', 'quantity'=>'412', 'rate'=>'00.90'));

    public static $deductions_Contractor3 = array(array('code'=> 'AONINS', 'quantity'=>'1', 'rate'=>'50.00'),
        array('code'=> 'FI', 'quantity'=>'1', 'rate'=>'615.89'),
        array('code'=> 'TL', 'quantity'=>'1', 'rate'=>'225.00'));



    public static $customer_RA_Code = 'TLRA';
    public static $customer_RA_description = 'Truck Lease RA';
    public static $customer_RA_Min_Balance = '1000.00';
    public static $customer_RA_Contribution_Amount = '75.00';

    public static $vendor2_RA_Code = 'FIRA';
    public static $vendor2_RA_description = 'Fuel RA';
    public static $vendor2_RA_Min_Balance = '500.00';
    public static $vendor2_RA_Contribution_Amount = '50.00';

    public static $contractor_RA_Code1 = 'TLRA';
    public static $contractor_Initial_Balance1 = '600.00';
    public static $contractor1_Current_Balance1 = '600.00';
    public static $contractor2_Current_Balance1 = '960.00';
    public static $contractor3_Current_Balance1 = '300.00';

    public static $contractor_RA_Code2 = 'FIRA';
    public static $contractor_Initial_Balance2 = '500.00';
    public static $contractor1_Current_Balance2 = '500.00';
    public static $contractor2_Current_Balance2 = '500.00';
    public static $contractor3_Current_Balance2 = '500.00';


    public static $recurring_Template1 = array('code' => 'W1', 'description' => 'Weekly(Sunday)', 'frequency' => 'Weekly', 'start_day' => array('Sunday'));
    public static $recurring_Template2 = array('code' => 'B2', 'description' => 'Biweekly (1st week,Wednesday)', 'frequency' => 'Biweekly', 'start_day' => '01/14/2015');
    public static $recurring_Template3 = array('code' => 'SW3', 'description' => 'Semi-Weekly (Monday,Thursday)', 'frequency' => 'Semi-Weekly', 'start_day' => array('Monday', 'Thursday'));
    public static $recurring_Template4 = array('code' => 'M4', 'description' => 'Monthly (10th)', 'frequency' => 'Monthly', 'start_day' => array('10th'));
    public static $recurring_Template5 = array('code' => 'SM5', 'description' => 'Semi-Monthly (14th, 30th)', 'frequency' => 'Semi-Monthly', 'start_day' => array('14th', '30th'));

    public static $recurring_Template_Quantity = '1';
    public static $recurring_Template_Rate = '1';

    public static $payment_Template_Payment_Code1 = 'HD';
    public static $payment_Template_Carrier_Payment_Code1 = '01';
    public static $payment_Template_Description1 = 'Home Delivery';
    public static $payment_Template_Category1 = 'Delivery';
    public static $payment_Template_Quantity1 = '15';
    public static $payment_template_Rate1 = '85.00';

    public static $payment_Template_Payment_Code2 = 'MG';
    public static $payment_Template_Carrier_Payment_Code2 = '02';
    public static $payment_Template_Description2 = 'Mileage';
    public static $payment_Template_Category2 = 'Truck';
    public static $payment_Template_Quantity2 = '320';
    public static $payment_template_Rate2 = '1.00';

    public static $deduction_Template_Deduction_Code1 = 'AONINS';
    public static $deduction_Template_Description1 = 'Insurance';
    public static $deduction_Template_Category1 = 'Expense';
    public static $deduction_Template_Quantity1 = '1';
    public static $deduction_Template_Rate1 = '50.00';

    public static $deduction_Template_Deduction_Code2 = 'FI';
    public static $deduction_Template_Description2 = 'Fuel Invoice';
    public static $deduction_Template_Category2 = 'Expense';
    public static $deduction_Template_Quantity2 = '1';
    public static $deduction_Template_Rate2 = '550.00';

    public static $deduction_Template_Deduction_Code3 = 'TL';
    public static $deduction_Template_Description3= 'Truck Lease';
    public static $deduction_Template_Category3 = 'Expense';
    public static $deduction_Template_Quantity3 = '1';
    public static $deduction_Template_Rate3 = '225.00';

    public static $settlement_Weekly_Cycle = 'Weekly';
    public static $settlement__Monthly_Cycle = 'Monthly';
    public static $settlement__Biweekly_Cycle = 'Biweekly';
    public static $settlement__Semi_Monthly_Cycle = 'Semi-Monthly';
    public static $settlement__Semi_Weekly_Cycle = 'Semi-Weekly';


    public static $settlement_Start_Date ='06/01/2015';
    public static $settlement_Processing_Deadline = '3';
    public static $settlement_Disbursement_Terms = '5';

    public static $contractor1_Reserve_Account_Test1 = array(array('code'=> 'TLRA', 'current_Balance'=>'600.00'),array('code'=> 'FIRA', 'current_Balance'=>'500.00'));
    public static $contractor1_Reserve_Account_Test2 = array(array('code'=> 'TLRA', 'current_Balance'=>'600.00'),array('code'=> 'FIRA', 'current_Balance'=>'500.00'));
    public static $contractor1_Reserve_Account_Test3 = array(array('code'=> 'TLRA', 'current_Balance'=>'950.00'),array('code'=> 'FIRA', 'current_Balance'=>'500.00'));
    public static $contractor1_Reserve_Account_Test4 = array(array('code'=> 'TLRA', 'current_Balance'=>'950.00'),array('code'=> 'FIRA', 'current_Balance'=>'500.00'));
    public static $contractor1_Reserve_Account_Test5 = array(array('code'=> 'TLRA', 'current_Balance'=>'00.00'),array('code'=> 'FIRA', 'current_Balance'=>'00.00'));
    public static $contractor1_Reserve_Account_Test6 = array(array('code'=> 'TLRA', 'current_Balance'=>'00.00'),array('code'=> 'FIRA', 'current_Balance'=>'00.00'));
    public static $contractor1_Reserve_Account_Test7 = array(array('code'=> 'TLRA', 'current_Balance'=>'600.00'),array('code'=> 'FIRA', 'current_Balance'=>'500.00'));
    public static $contractor1_Reserve_Account_Test8 = array(array('code'=> 'TLRA', 'current_Balance'=>'600.00'),array('code'=> 'FIRA', 'current_Balance'=>'500.00'));
    public static $contractor1_Reserve_Account_Test9 = array(array('code'=> 'TLRA', 'current_Balance'=>'100.00'),array('code'=> 'FIRA', 'current_Balance'=>'500.00'));
    public static $contractor1_Reserve_Account_Test10 = array(array('code'=> 'TLRA', 'current_Balance'=>'100.00'),array('code'=> 'FIRA', 'current_Balance'=>'500.00'));

    public static $contractor2_Reserve_Account_Test2 = array(array('code'=> 'TLRA', 'current_Balance'=>'960.00'),array('code'=> 'FIRA', 'current_Balance'=>'500.00'));
    public static $contractor2_Reserve_Account_Test4 = array(array('code'=> 'TLRA', 'current_Balance'=>'700.00'),array('code'=> 'FIRA', 'current_Balance'=>'500.00'));
    public static $contractor2_Reserve_Account_Test6 = array(array('code'=> 'TLRA', 'current_Balance'=>'700.00'),array('code'=> 'FIRA', 'current_Balance'=>'500.00'));
    public static $contractor2_Reserve_Account_Test8 = array(array('code'=> 'TLRA', 'current_Balance'=>'700.00'),array('code'=> 'FIRA', 'current_Balance'=>'500.00'));
    public static $contractor2_Reserve_Account_Test10 = array(array('code'=> 'TLRA', 'current_Balance'=>'100.00'),array('code'=> 'FIRA', 'current_Balance'=>'500.00'));

    public static $contractor3_Reserve_Account_Test2 = array(array('code'=> 'TLRA', 'current_Balance'=>'300.00'),array('code'=> 'FIRA', 'current_Balance'=>'500.00'));
    public static $contractor3_Reserve_Account_Test4 = array(array('code'=> 'TLRA', 'current_Balance'=>'300.00'),array('code'=> 'FIRA', 'current_Balance'=>'500.00'));
    public static $contractor3_Reserve_Account_Test6 = array(array('code'=> 'TLRA', 'current_Balance'=>'1000.00'),array('code'=> 'FIRA', 'current_Balance'=>'500.00'));
    public static $contractor3_Reserve_Account_Test8 = array(array('code'=> 'TLRA', 'current_Balance'=>'1000.00'),array('code'=> 'FIRA', 'current_Balance'=>'500.00'));
    public static $contractor3_Reserve_Account_Test10 = array(array('code'=> 'TLRA', 'current_Balance'=>'1000.00'),array('code'=> 'FIRA', 'current_Balance'=>'500.00'));

    public static $contractor1_Adjusted_Balance_Test3 = array(array('code'=> 'FI', 'adjusted_Balance'=>'100.00'),array('code'=> 'TL', 'adjusted_Balance'=>'50.00'));
    public static $contractor1_Adjusted_Balance_Test4 = array(array('code'=> 'FI', 'adjusted_Balance'=>'100.00'),array('code'=> 'TL', 'adjusted_Balance'=>'50.00'));
    public static $contractor1_Adjusted_Balance_Test5 = array(array('code'=> 'FI', 'adjusted_Balance'=>'400.00'),array('code'=> 'TL', 'adjusted_Balance'=>'100.00'));
    public static $contractor1_Adjusted_Balance_Test6 = array(array('code'=> 'FI', 'adjusted_Balance'=>'400.00'),array('code'=> 'TL', 'adjusted_Balance'=>'100.00'));
    public static $contractor1_Adjusted_Balance_Test7 = array(array('code'=> 'FI', 'adjusted_Balance'=>'250.00'),array('code'=> 'AONINS', 'adjusted_Balance'=>'50.00'));
    public static $contractor1_Adjusted_Balance_Test8 = array(array('code'=> 'FI', 'adjusted_Balance'=>'250.00'),array('code'=> 'AONINS', 'adjusted_Balance'=>'50.00'));
    public static $contractor1_Adjusted_Balance_Test9 = array(array('code'=> 'FI', 'adjusted_Balance'=>'250.00'),array('code'=> 'AONINS', 'adjusted_Balance'=>'50.00'),array('code'=> 'TL', 'adjusted_Balance'=>'225.00'));
    public static $contractor1_Adjusted_Balance_Test10 = array(array('code'=> 'FI', 'adjusted_Balance'=>'250.00'),array('code'=> 'AONINS', 'adjusted_Balance'=>'50.00'),array('code'=> 'TL', 'adjusted_Balance'=>'225.00'));

    public static $contractor2_Adjusted_Balance_Test4 = array(array('code'=> 'FI', 'adjusted_Balance'=>'75.00'),array('code'=> 'TL', 'adjusted_Balance'=>'50.00'));
    public static $contractor2_Adjusted_Balance_Test6 = array(array('code'=> 'FI', 'adjusted_Balance'=>'75.00'));
    public static $contractor2_Adjusted_Balance_Test8 = array(array('code'=> 'FI', 'adjusted_Balance'=>'75.00'));
    public static $contractor2_Adjusted_Balance_Test10 = array(array('code'=> 'FI', 'adjusted_Balance'=>'250.00'),array('code'=> 'AONINS', 'adjusted_Balance'=>'50.00'));

    public static $settlement_Summary_Test1 = array("$1,595.00", "$825.00", "$0.00", "$75.00", "$0.00", "$695.00");  // gets all values from Total line. They are arranged in the order ('Payments', 'Deductions', 'Balance Due', 'Contributions', 'Withdrawals', 'Settlement Amount')
    public static $settlement_Summary_Test2 = array("$4,763.70", "$2,495.57", "$0.00", "$190.00", "$0.00", "$2,078.13");  // gets all values from Total line. They are arranged in the order ('Payments', 'Deductions', 'Balance Due', 'Contributions', 'Withdrawals', 'Settlement Amount')
    public static $settlement_Summary_Test3 = array("$968.00", "$945.23", "$0.00", "$22.77", "$0.00", "$0.00");  // gets all values from Total line. They are arranged in the order ('Payments', 'Deductions', 'Balance Due', 'Contributions', 'Withdrawals', 'Settlement Amount')
    public static $settlement_Summary_Test4 = array("$3,484.60", "$2,667.97", "$0.00", "$136.72", "$0.00", "$679.91");  // gets all values from Total line. They are arranged in the order ('Payments', 'Deductions', 'Balance Due', 'Contributions', 'Withdrawals', 'Settlement Amount')
    public static $settlement_Summary_Test5 = array("$968.00", "$1,385.23", "$417.23", "$0.00", "$0.00", "$0.00");  // gets all values from Total line. They are arranged in the order ('Payments', 'Deductions', 'Balance Due', 'Contributions', 'Withdrawals', 'Settlement Amount')
    public static $settlement_Summary_Test6 = array("$3,484.60", "$3,107.97", "$417.23", "$38.95", "$0.00", "$754.91");  // gets all values from Total line. They are arranged in the order ('Payments', 'Deductions', 'Balance Due', 'Contributions', 'Withdrawals', 'Settlement Amount')
    public static $settlement_Summary_Test7 = array("$1,001.20", "$1,273.36", "$47.16", "$0.00", "$225.00", "$0.00");  // gets all values from Total line. They are arranged in the order ('Payments', 'Deductions', 'Balance Due', 'Contributions', 'Withdrawals', 'Settlement Amount')
    public static $settlement_Summary_Test8 = array("$3,517.80", "$2,996.10", "$47.16", "$38.95", "$225.00", "$754.91");  // gets all values from Total line. They are arranged in the order ('Payments', 'Deductions', 'Balance Due', 'Contributions', 'Withdrawals', 'Settlement Amount')
    public static $settlement_Summary_Test9 = array("$985.00", "$1,280.36", "$195.36", "$0.00", "$100.00", "$0.00");  // gets all values from Total line. They are arranged in the order ('Payments', 'Deductions', 'Balance Due', 'Contributions', 'Withdrawals', 'Settlement Amount')
    public static $settlement_Summary_Test10 = array("$3,578.00", "$3,451.61", "$428.52", "$0.00", "$200.00", "$754.91");  // gets all values from Total line. They are arranged in the order ('Payments', 'Deductions', 'Balance Due', 'Contributions', 'Withdrawals', 'Settlement Amount')

    public static function create_Disbursement_Array_Test1()
    { //create array of disbursement calculation
        $name= array(Input_Data::$contractor1['Contractor_Name'], Input_Data::$contractor1['Contractor_Name'], Input_Data::$vendor1, Input_Data::$vendor2, Input_Data::$customer);
        $amount=array("$347.50", "$347.50", "$50.00", "$550.00", "$300.00");
        for($i=0; $i<count($name); $i++){
            $disbursements_Reconciliations[$i] = array($name[$i],$amount[$i]);
        }
        return $disbursements_Reconciliations;
    }
    public static function create_Disbursement_Array_Test2()
    { //create array of disbursement calculation
        $name= array(Input_Data::$contractor1['Contractor_Name'], Input_Data::$contractor1['Contractor_Name'],Input_Data::$contractor2['Contractor_Name'], Input_Data::$contractor3['Contractor_Name'], Input_Data::$vendor1, Input_Data::$vendor2, Input_Data::$customer);
        $amount=array("$346.68", "$346.67","$704.87", "$679.91","$150.00", "$1,670.57", "$865.00");
        for($i=0; $i<count($name); $i++){
            $disbursements_Reconciliations[$i] = array($name[$i],$amount[$i]);
        }
        return $disbursements_Reconciliations;
    }

    public static function create_Disbursement_Array_Test3()
    { //create array of disbursement calculation
        $name= array(Input_Data::$contractor1['Contractor_Name'], Input_Data::$contractor1['Contractor_Name'],Input_Data::$vendor1, Input_Data::$vendor2, Input_Data::$customer);
        $amount=array("$86.39", "$86.38","$50.00", "$545.23","$200.00");
        for($i=0; $i<count($name); $i++){
            $disbursements_Reconciliations[$i] = array($name[$i],$amount[$i]);
        }
        return $disbursements_Reconciliations;
    }

    public static function create_Disbursement_Array_Test4()
    { //create array of disbursement calculation
        $name= array(Input_Data::$contractor1['Contractor_Name'], Input_Data::$contractor1['Contractor_Name'],Input_Data::$contractor2['Contractor_Name'], Input_Data::$contractor3['Contractor_Name'], Input_Data::$vendor1, Input_Data::$vendor2, Input_Data::$customer);
        $amount=array("$86.39", "$86.38","$163.95", "$679.91", "$150.00", "$1,642.97","$675.00");
        for($i=0; $i<count($name); $i++){
            $disbursements_Reconciliations[$i] = array($name[$i],$amount[$i]);
        }
        return $disbursements_Reconciliations;
    }

    public static function create_Disbursement_Array_Test5()
    { //create array of disbursement calculation
        $name= array(Input_Data::$contractor1['Contractor_Name'], Input_Data::$contractor1['Contractor_Name'],Input_Data::$vendor1, Input_Data::$vendor2, Input_Data::$customer);
        $amount=array("$41.39", "$41.38","$50.00", "$685.23","$150.00");
        for($i=0; $i<count($name); $i++){
            $disbursements_Reconciliations[$i] = array($name[$i],$amount[$i]);
        }
        return $disbursements_Reconciliations;
    }

    public static function create_Disbursement_Array_Test6()
    { //create array of disbursement calculation
        $name= array(Input_Data::$contractor1['Contractor_Name'], Input_Data::$contractor1['Contractor_Name'],Input_Data::$contractor2['Contractor_Name'], Input_Data::$contractor3['Contractor_Name'], Input_Data::$vendor1, Input_Data::$vendor2, Input_Data::$customer);
        $amount=array("$41.39", "$41.38","$88.95", "$754.91", "$150.00", "$1,782.97","$625.00");
        for($i=0; $i<count($name); $i++){
            $disbursements_Reconciliations[$i] = array($name[$i],$amount[$i]);
        }
        return $disbursements_Reconciliations;
    }

    public static function create_Disbursement_Array_Test7()
    { //create array of disbursement calculation
        $name= array(Input_Data::$contractor1['Contractor_Name'], Input_Data::$contractor1['Contractor_Name'],Input_Data::$vendor1, Input_Data::$vendor2, Input_Data::$customer);
        $amount=array("$176.42", "$176.42","$100.00", "$648.36","-$100.00");
        for($i=0; $i<count($name); $i++){
            $disbursements_Reconciliations[$i] = array($name[$i],$amount[$i]);
        }
        return $disbursements_Reconciliations;
    }

    public static function create_Disbursement_Array_Test9()
    { //create array of disbursement calculation
        $name= array(Input_Data::$contractor1['Contractor_Name'], Input_Data::$contractor1['Contractor_Name'],Input_Data::$vendor1, Input_Data::$vendor2, Input_Data::$customer);
        $amount=array("$164.82", "$164.82","$100.00", "$655.36","-$100.00");
        for($i=0; $i<count($name); $i++){
            $disbursements_Reconciliations[$i] = array($name[$i],$amount[$i]);
        }
        return $disbursements_Reconciliations;
    }

    public static function create_Disbursement_Array_Test8()
    { //create array of disbursement calculation
        $name= array(Input_Data::$contractor1['Contractor_Name'], Input_Data::$contractor1['Contractor_Name'],Input_Data::$contractor2['Contractor_Name'], Input_Data::$contractor3['Contractor_Name'], Input_Data::$vendor1, Input_Data::$vendor2, Input_Data::$customer);
        $amount=array("$176.42", "$176.42","$88.95", "$754.91", "$200.00", "$1,746.10","$375.00");
        for($i=0; $i<count($name); $i++){
            $disbursements_Reconciliations[$i] = array($name[$i],$amount[$i]);
        }
        return $disbursements_Reconciliations;
    }

    public static function create_Disbursement_Array_Test10()
    { //create array of disbursement calculation
        $name= array(Input_Data::$contractor1['Contractor_Name'], Input_Data::$contractor1['Contractor_Name'],Input_Data::$contractor2['Contractor_Name'], Input_Data::$contractor3['Contractor_Name'], Input_Data::$vendor1, Input_Data::$vendor2, Input_Data::$customer);
        $amount=array("$164.82", "$164.82","$191.84", "$754.91", "$250.00", "$1,926.61","$125.00");
        for($i=0; $i<count($name); $i++){
            $disbursements_Reconciliations[$i] = array($name[$i],$amount[$i]);
        }
        return $disbursements_Reconciliations;
    }


    public static $sum_Reserve_Account_Ending_Balances_Test1 = "1175";
    public static $sum_Reserve_Account_Ending_Balances_Test2 = "3550";
    public static $sum_Reserve_Account_Ending_Balances_Test3 = "1450";
    public static $sum_Reserve_Account_Ending_Balances_Test4 = "3525";
    public static $sum_Reserve_Account_Ending_Balances_Test5 = "0";
    public static $sum_Reserve_Account_Ending_Balances_Test6 = "2725";
    public static $sum_Reserve_Account_Ending_Balances_Test7 = "775";
    public static $sum_Reserve_Account_Ending_Balances_Test8 = "3500";
    public static $sum_Reserve_Account_Ending_Balances_Test9 = "500";
    public static $sum_Reserve_Account_Ending_Balances_Test10 = "2500";
}
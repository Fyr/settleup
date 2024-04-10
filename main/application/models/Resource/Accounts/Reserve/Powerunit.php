<?php

class Application_Model_Resource_Accounts_Reserve_Powerunit extends Application_Model_Base_Resource
{
    protected $_name = 'reserve_account';

    public function getInfoFields()
    {
        return [
            'powerunit_code' => 'PU Code',
            'name' => 'PU Name',
            'code' => 'RA Code',
            'account_name' => 'RA Name',
            'account_type' => 'Account Type',
            'current_balance' => 'Current Balance',
            'min_balance' => 'Min. Balance',
            'contribution_amount' => 'Contribution Amount',
        ];
    }

    public function getInfoFieldsShort()
    {
        return [
            'code' => 'Account Code',
            'account_name' => 'Account Name',
            'account_type' => 'Account Type',
            'current_balance' => 'Balance',
            'min_balance' => 'Min. Balance',
            'contribution_amount' => 'Contribution Amount',
        ];
    }

    public function getInfoFieldsForReport()
    {
        //        if (!Application_Model_Entity_Accounts_User::getCurrentUser()->isOnboarding()) {
        //            $fields = [
        //                'name' => 'Company',
        //                'vendor_name' => 'Carrier/Vendor',
        //                'account_name' => 'Reserve Account',
        //                'description' => 'Description',
        //            ];
        //        } else {
        $fields = [
            'name' => 'Company',
            'account_name' => 'Reserve Account',
            'description' => 'Description',
        ];

        //        }
        return $fields;
    }

    public function getInfoFieldsForSettlementContributionPopup()
    {
        return [
            'vendor_name' => 'Vendor',
            'vendor_reserve_code' => 'Code',
            'description' => 'Description',
            'contribution_amount' => 'Contribution Amount',
        ];
    }

    public function getInfoFieldsForSettlementWithdrawalPopup()
    {
        return [
            'vendor_name' => 'Vendor',
            'vendor_reserve_code' => 'Code',
            'description' => 'Description',
        ];
    }

    public function getInfoFieldsForSettlementGrid()
    {
        return [
            'vendor_name' => 'Vendor',
            'vendor_reserve_code' => 'Code',
            'description' => 'Description',
            'min_balance' => 'Min. Balance',
            'contribution_amount' => 'Contribution Amount',
            'starting_balance' => 'Starting Balance',
            'current_balance' => 'Current Balance',
        ];
    }

    public function getSetupFields()
    {
        return [
            // 'reserve_account_vendor',
            // 'vendor_name',
            'code',
            'name',
            'current_balance',
            'description',
        ];
    }

    public function updateReserveAccountVendorInitialBalance($id)
    {
        $sql = 'CALL updateReserveAccountVendorInitialBalance(?)';
        $stmt = $this->getAdapter()->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $this;
    }

    public function updateReserveAccountVendorCurrentBalance($id)
    {
        $rav = $this->getVendorAccountId($id);
        $sql = 'CALL updateReserveAccountVendorCurrentBalance(?)';
        $stmt = $this->getAdapter()->prepare($sql);
        $stmt->bindParam(1, $rav);
        $stmt->execute();

        return $this;
    }

    public function getVendorAccountId($id)
    {
        $sql = 'SELECT getVendorAccount(?) as rav';
        $stmt = $this->getAdapter()->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $data = current($stmt->fetch());

        return $data;
    }
}

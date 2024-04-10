<?php

class Application_Model_Entity_Entity_Permissions extends Application_Model_Base_Entity
{
    protected $_titleColumn = 'id';
    final public const SETTLEMENT_EDIT = 'settlement_edit';
    final public const SETTLEMENT_VERIFY = 'settlement_verify';
    final public const SETTLEMENT_PROCESS = 'settlement_process';
    final public const SETTLEMENT_DELETE = 'settlement_delete';
    final public const SETTLEMENT_APPROVE = 'settlement_approve';
    final public const SETTLEMENT_EXPORT = 'settlement_export';
    final public const SETTLEMENT_REJECT = 'settlement_reject';
    final public const SETTLEMENT_DATA_VIEW = 'settlement_data_view';
    final public const SETTLEMENT_DATA_MANAGE = 'settlement_data_manage';
    final public const SETTLEMENT_RULE_VIEW = 'settlement_rule_view';
    final public const SETTLEMENT_RULE_MANAGE = 'settlement_rule_manage';
    final public const SETTLEMENT_GROUP_VIEW = 'settlement_group_view';
    final public const SETTLEMENT_GROUP_MANAGE = 'settlement_group_manage';
    final public const SETTLEMENT_ESCROW_ACCOUNT_VIEW = 'settlement_escrow_account_view';
    final public const RESERVE_TRANSACTION_VENDOR_VIEW = 'reserve_transaction_vendor_view';
    final public const RESERVE_ACCOUNT_CARRIER_VIEW = 'reserve_account_carrier_view';
    final public const RESERVE_ACCOUNT_CARRIER_MANAGE = 'reserve_account_carrier_manage';
    final public const RESERVE_ACCOUNT_VENDOR_VIEW = 'reserve_account_vendor_view';
    final public const RESERVE_ACCOUNT_VENDOR_MANAGE = 'reserve_account_vendor_manage';
    final public const RESERVE_ACCOUNT_CONTRACTOR_VIEW = 'reserve_account_contractor_view';
    final public const DISBURSEMENT_VIEW = 'disbursement_view';
    final public const DISBURSEMENT_MANAGE = 'disbursement_manage';
    final public const DISBURSEMENT_APPROVE = 'disbursement_approve';
    final public const DISBURSEMENT_REISSUE = 'disbursement_reissue';
    final public const VENDOR_DEDUCTION_VIEW = 'vendor_deduction_view';
    final public const VENDOR_DEDUCTION_MANAGE = 'vendor_deduction_manage';
    final public const REPORTING_ACH_CHECK = 'reporting_ach_check';
    final public const REPORTING_DEDUCTION_REMITTANCE_FILE = 'reporting_deduction_remittance_file';
    final public const REPORTING_SETTLEMENT_RECONCILIATION = 'reporting_settlement_reconciliation';
    final public const REPORTING_GENERAL = 'reporting_general';
    final public const CONTRACTOR_VIEW = 'contractor_view';
    final public const CONTRACTOR_MANAGE = 'contractor_manage';
    final public const VENDOR_VIEW = 'vendor_view';
    final public const VENDOR_MANAGE = 'vendor_manage';
    final public const CARRIER_VIEW = 'carrier_view';
    final public const CARRIER_MANAGE = 'carrier_manage';
    final public const TEMPLATE_VIEW = 'template_view';
    final public const TEMPLATE_MANAGE = 'template_manage';
    final public const UPLOADING = 'uploading';
    final public const CONTRACTOR_VENDOR_AUTH_MANAGE = 'contractor_vendor_auth_manage';
    final public const PERMISSIONS_MANAGE = 'permissions_manage';
    final public const VENDOR_USER_CREATE = 'vendor_user_create';
    final public const CONTRACTOR_USER_CREATE = 'contractor_user_create';

    public function _afterSave()
    {
        parent::_afterSave();

        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        if ($user->isAdminOrSuperAdmin() || $user->isManager()) {
            $user->setPermissions($this->load($this->getId()));
        }

        return $this;
    }

    public static function getVendorPermissions()
    {
        return [
            self::RESERVE_TRANSACTION_VENDOR_VIEW,
            self::VENDOR_DEDUCTION_MANAGE,
            self::UPLOADING,
            self::TEMPLATE_VIEW,
            self::TEMPLATE_MANAGE,
            self::RESERVE_ACCOUNT_VENDOR_VIEW,
            self::RESERVE_ACCOUNT_VENDOR_MANAGE,
            self::RESERVE_ACCOUNT_CONTRACTOR_VIEW,
            self::REPORTING_GENERAL,
            self::REPORTING_DEDUCTION_REMITTANCE_FILE,
            self::VENDOR_VIEW,
            self::VENDOR_MANAGE,
        ];
    }

    public static function getVendorUniquePermissions()
    {
        return [
            self::RESERVE_TRANSACTION_VENDOR_VIEW,
            self::RESERVE_ACCOUNT_CONTRACTOR_VIEW,
        ];
    }
}

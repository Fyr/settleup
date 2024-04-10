<?php

class Application_Form_Entity_SpecialistPermissions extends Application_Form_Base
{
    public function init(): void
    {
        $this->setName('specialist_permissions');
        parent::init();

        $id = new Application_Form_Element_Hidden('id');

        $userId = new Application_Form_Element_Hidden('user_id');

        $settlement_edit = new Zend_Form_Element_Checkbox('settlement_edit');
        $settlement_edit->setLabel('Edit Settlement Cycle');
        $settlement_verify = new Zend_Form_Element_Checkbox('settlement_verify');
        $settlement_verify->setLabel('Verify Settlement Cycle');
        $settlement_process = new Zend_Form_Element_Checkbox('settlement_process');
        $settlement_process->setLabel('Process Settlement Cycle');
        $settlement_delete = new Zend_Form_Element_Checkbox('settlement_delete');
        $settlement_delete->setLabel('Delete Settlement Cycle');
        $settlement_approve = new Zend_Form_Element_Checkbox('settlement_approve');
        $settlement_approve->setLabel('Approve Settlement Cycle');
        $settlement_export = new Zend_Form_Element_Checkbox('settlement_export');
        $settlement_export->setLabel('Export Settlement Cycle Data to Hub');
        $settlement_reject = new Zend_Form_Element_Checkbox('settlement_reject');
        $settlement_reject->setLabel('Reject Settlement Cycle');

        $settlement_data_view = new Zend_Form_Element_Checkbox('settlement_data_view');
        $settlement_data_view->setLabel('View Settlement Info (View Compensations, Deductions and Reserve Transactions)');
        $settlement_data_manage = new Zend_Form_Element_Checkbox('settlement_data_manage');
        $settlement_data_manage->setLabel(
            'Edit Settlement Cycle (Add, Delete and Edit Compensations, Deductions and Reserve Transactions)'
        );

        $settlement_rule_view = new Zend_Form_Element_Checkbox('settlement_rule_view');
        $settlement_rule_view->setLabel('View Settlement Cycle Rules');
        $settlement_rule_manage = new Zend_Form_Element_Checkbox('settlement_rule_manage');
        $settlement_rule_manage->setLabel('Edit Settlement Cycle Rules');

        $settlement_group_view = new Zend_Form_Element_Checkbox('settlement_group_view');
        $settlement_group_view->setLabel('View Settlement Group');
        $settlement_group_manage = new Zend_Form_Element_Checkbox('settlement_group_manage');
        $settlement_group_manage->setLabel('Edit Settlement Group');

        $settlement_escrow_account_view = new Zend_Form_Element_Checkbox('settlement_escrow_account_view');
        $settlement_escrow_account_view->setLabel('View Settlement Escrow Account');

        $reserve_account_carrier_view = new Zend_Form_Element_Checkbox('reserve_account_carrier_view');
        $reserve_account_carrier_view->setLabel('View Carrier Reserve Accounts');
        $reserve_account_carrier_manage = new Zend_Form_Element_Checkbox('reserve_account_carrier_manage');
        $reserve_account_carrier_manage->setLabel('Add, Delete and Edit Carrier Reserve Accounts');

        $reserve_account_vendor_view = new Zend_Form_Element_Checkbox('reserve_account_vendor_view');
        $reserve_account_vendor_view->setLabel('View Vendor Reserve Accounts');
        $reserve_account_vendor_manage = new Zend_Form_Element_Checkbox('reserve_account_vendor_manage');
        $reserve_account_vendor_manage->setLabel('Add, Delete and Edit Vendor Reserve Accounts');

        $disbursement_view = new Zend_Form_Element_Checkbox('disbursement_view');
        $disbursement_view->setLabel('View Disbursements');
        $disbursement_manage = new Zend_Form_Element_Checkbox('disbursement_manage');
        $disbursement_manage->setLabel('Add, Delete and Edit Disbursements');
        $disbursement_approve = new Zend_Form_Element_Checkbox('disbursement_approve');
        $disbursement_approve->setLabel('Approve Disbursements');
        $disbursement_reissue = new Zend_Form_Element_Checkbox('disbursement_reissue');
        $disbursement_reissue->setLabel('Reissue Disbursements');

        $vendor_deduction_view = new Zend_Form_Element_Checkbox('vendor_deduction_view');
        $vendor_deduction_view->setLabel('View Vendor Deductions');
        $vendor_deduction_manage = new Zend_Form_Element_Checkbox('vendor_deduction_manage');
        $vendor_deduction_manage->setLabel('Add, Delete and Edit Vendor Deductions');

        $reporting_deduction_remittance_file = new Zend_Form_Element_Checkbox('reporting_deduction_remittance_file');
        $reporting_deduction_remittance_file->setLabel('Reporting - View Deduction Remittance File');
        $reporting_settlement_reconciliation = new Zend_Form_Element_Checkbox('reporting_settlement_reconciliation');
        $reporting_settlement_reconciliation->setLabel('Reporting - View Settlement Reconciliation');
        $reporting_general = new Zend_Form_Element_Checkbox('reporting_general');
        $reporting_general->setLabel(
            'Reporting - View General Reports (Settlement Statements, Compensation History, Deduction History and Contractor Status)'
        );

        $contractor_view = new Zend_Form_Element_Checkbox('contractor_view');
        $contractor_view->setLabel('View Contractors Information');
        $contractor_manage = new Zend_Form_Element_Checkbox('contractor_manage');
        $contractor_manage->setLabel('Add, Delete, Terminate, Contract and Edit Contractors Information');

        $vendor_view = new Zend_Form_Element_Checkbox('vendor_view');
        $vendor_view->setLabel('View Vendors Information');
        $vendor_manage = new Zend_Form_Element_Checkbox('vendor_manage');
        $vendor_manage->setLabel('Add, Delete and Edit Vendors Information');

        $carrier_view = new Zend_Form_Element_Checkbox('carrier_view');
        $carrier_view->setLabel('View Carrier Info');
        $carrier_manage = new Zend_Form_Element_Checkbox('carrier_manage');
        $carrier_manage->setLabel('Edit Carrier Info');

        $template_view = new Zend_Form_Element_Checkbox('template_view');
        $template_view->setLabel('View Templates (View Compensations and Deductions Templates)');
        $template_manage = new Zend_Form_Element_Checkbox('template_manage');
        $template_manage->setLabel('Edit Templates (Add, Delete and Edit Compensations and Deductions Templates)');

        $uploading = new Zend_Form_Element_Checkbox('uploading');
        $uploading->setLabel('Uploading (Compensations, Deductions and Contractors)');

        $contractor_vendor_auth_manage = new Zend_Form_Element_Checkbox('contractor_vendor_auth_manage');
        $contractor_vendor_auth_manage->setLabel('Change Contractor-Vendor Authorizations');

        $vendor_user_create = new Zend_Form_Element_Checkbox('vendor_user_create');
        $vendor_user_create->setLabel('Create Vendor User');
        $contractor_user_create = new Zend_Form_Element_Checkbox('contractor_user_create');
        $contractor_user_create->setLabel('Create Contractor User');

        $this->addElements(
            [
                $id,
                $userId,
                $settlement_edit,
                $settlement_verify,
                $settlement_process,
                $settlement_delete,
                $settlement_approve,
                $settlement_reject,
                $settlement_export,
                $settlement_data_view,
                $settlement_data_manage,
                $settlement_rule_view,
                $settlement_rule_manage,
                $settlement_group_view,
                $settlement_group_manage,
                $settlement_escrow_account_view,
                $reserve_account_carrier_view,
                $reserve_account_carrier_manage,
                $reserve_account_vendor_view,
                $reserve_account_vendor_manage,
                $disbursement_view,
                $disbursement_manage,
                $disbursement_approve,
                $disbursement_reissue,
                $vendor_deduction_view,
                $vendor_deduction_manage,
                $reporting_deduction_remittance_file,
                $reporting_settlement_reconciliation,
                $reporting_general,
                $contractor_view,
                $contractor_manage,
                $vendor_view,
                $vendor_manage,
                $carrier_view,
                $carrier_manage,
                $template_view,
                $template_manage,
                $uploading,
                $contractor_vendor_auth_manage,
                $vendor_user_create,
                $contractor_user_create,
            ]
        );

        $this->setDefaultDecorators(
            [
                'settlement_edit',
                'settlement_view',
                'settlement_verify',
                'settlement_process',
                'settlement_delete',
                'settlement_approve',
                'settlement_reject',
                'settlement_data_view',
                'settlement_data_manage',
                'settlement_rule_view',
                'settlement_rule_manage',
                'settlement_group_view',
                'settlement_group_manage',
                'settlement_escrow_account_view',
                'reserve_account_carrier_view',
                'reserve_account_carrier_manage',
                'reserve_account_vendor_view',
                'reserve_account_vendor_manage',
                'disbursement_view',
                'disbursement_manage',
                'disbursement_approve',
                'disbursement_reissue',
                'vendor_deduction_view',
                'vendor_deduction_manage',
                'reporting_deduction_remittance_file',
                'reporting_settlement_reconciliation',
                'reporting_general',
                'contractor_view',
                'contractor_manage',
                'vendor_view',
                'vendor_manage',
                'carrier_view',
                'carrier_manage',
                'template_view',
                'template_manage',
                'uploading',
                'contractor_vendor_auth_manage',
                'vendor_user_create',
                'contractor_user_create',
                'settlement_export',
            ]
        );

        if (Application_Model_Entity_Accounts_User::getCurrentUser()->isAdminOrSuperAdmin()) {
            $permissions_mange = new Zend_Form_Element_Checkbox('permissions_manage');
            $permissions_mange->setLabel('Manage User Permissions');

            $this->addElements([$permissions_mange]);
            $this->setDefaultDecorators(['permissions_manage']);
        }

        $this->addSubmit();
    }
}

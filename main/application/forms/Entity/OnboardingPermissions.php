<?php

class Application_Form_Entity_OnboardingPermissions extends Application_Form_Base
{
    public function init()
    {
        $this->setName('onboarding_permissions');
        parent::init();

        $id = new Application_Form_Element_Hidden('id');

        $userId = new Application_Form_Element_Hidden('user_id');

        $reserve_transaction_vendor_view = new Zend_Form_Element_Checkbox('reserve_transaction_vendor_view');
        $reserve_transaction_vendor_view->setLabel('View Vendor Reserve Transactions');

        $reserve_account_vendor_view = new Zend_Form_Element_Checkbox('reserve_account_vendor_view');
        $reserve_account_vendor_view->setLabel('View Vendor Reserve Accounts');
        $reserve_account_vendor_manage = new Zend_Form_Element_Checkbox('reserve_account_vendor_manage');
        $reserve_account_vendor_manage->setLabel('Add, Delete and Edit Vendor Reserve Accounts');

        $reserve_account_contractor_view = new Zend_Form_Element_Checkbox('reserve_account_contractor_view');
        $reserve_account_contractor_view->setLabel('View Power Unit Reserve Accounts');

        $vendor_deduction_manage = new Zend_Form_Element_Checkbox('vendor_deduction_manage');
        $vendor_deduction_manage->setLabel('Add, Delete and Edit Vendor Deductions');

        $reporting_deduction_remittance_file = new Zend_Form_Element_Checkbox('reporting_deduction_remittance_file');
        $reporting_deduction_remittance_file->setLabel('Reporting - View Deduction Remittance File');
        $reporting_general = new Zend_Form_Element_Checkbox('reporting_general');
        $reporting_general->setLabel(
            'Reporting - View General Reports (Settlement Statements, Compensation History, Deduction History and Contractor Status)'
        );

        $vendor_view = new Zend_Form_Element_Checkbox('vendor_view');
        $vendor_view->setLabel('View Vendors Information');
        $vendor_manage = new Zend_Form_Element_Checkbox('vendor_manage');
        $vendor_manage->setLabel('Add, Delete and Edit Vendors Information');

        $template_view = new Zend_Form_Element_Checkbox('template_view');
        $template_view->setLabel('View Templates (View Compensations and Deductions Templates)');
        $template_manage = new Zend_Form_Element_Checkbox('template_manage');
        $template_manage->setLabel('Edit Templates (Add, Delete and Edit Compensations and Deductions Templates)');

        $uploading = new Zend_Form_Element_Checkbox('uploading');
        $uploading->setLabel('Uploading (Compensations, Deductions and Contractors)');

        $this->addElements(
            [
                $id,
                $userId,
                $reserve_transaction_vendor_view,
                $reserve_account_contractor_view,
                $reserve_account_vendor_view,
                $reserve_account_vendor_manage,
                $vendor_deduction_manage,
                $reporting_deduction_remittance_file,
                $reporting_general,
                $vendor_view,
                $vendor_manage,
                $template_view,
                $template_manage,
                $uploading,
            ]
        );

        $this->setDefaultDecorators(
            [
                'reserve_transaction_vendor_view',
                'reserve_account_contractor_view',
                'reserve_account_vendor_view',
                'reserve_account_vendor_manage',
                'vendor_deduction_manage',
                'reporting_deduction_remittance_file',
                'reporting_general',
                'vendor_view',
                'vendor_manage',
                'template_view',
                'template_manage',
                'uploading',
            ]
        );

        $this->addSubmit('Save');
    }
}

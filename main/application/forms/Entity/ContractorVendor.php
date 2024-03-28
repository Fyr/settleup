<?php

class Application_Form_Entity_ContractorVendor extends Application_Form_Base
{
    public function init()
    {
        $this->setName('contractor_vendor');
        parent::init();

        $id = new Application_Form_Element_Hidden('id');

        $deleted = new Application_Form_Element_Hidden('deleted');
        $deleted->setValue(Application_Model_Entity_System_SystemValues::NOT_DELETED_STATUS);

        $vendorEntity = (new Application_Model_Entity_Entity_Vendor());
        $vendorId = new Zend_Form_Element_Select('vendor_id');
        $vendors = $vendorEntity->getCollection()->addNondeletedFilter()->addConfiguredFilter()->addFilter(
            'carrier_id',
            Application_Model_Entity_Accounts_User::getCurrentUser()->getSelectedCarrier()->getEntityId()
        )->getItems();
        $options = [];
        foreach ($vendors as $vendor) {
            $options[$vendor->getEntityId()] = $vendor->getName();
        }
        if (is_array($options)) {
            $options[0] = '';
        } else {
            $options = [0 => ''];
        }
        $vendorId->setLabel('Vendor ')->setMultiOptions($options)->setValue(0);

        $vendorStatusEntity = new Application_Model_Entity_System_VendorStatus();
        $vendorStatuses = $vendorStatusEntity->getResource()->getOptions(
            'title',
            'id <> ' . Application_Model_Entity_System_VendorStatus::STATUS_NOT_ACTIVE
        );

        $vendorStatus = new Zend_Form_Element_Select('status');
        $vendorStatus->setLabel('Status ')->setMultiOptions($vendorStatuses);

        $vendorsAcct = new Zend_Form_Element_Text('vendor_acct');
        $vendorsAcct->setLabel('Vendor Acct #');
        $vendorsAcct->addValidator('Digits', true, ['messages' => 'Entered value is invalid']);

        $this->addElements([$vendorId, $vendorStatus, $deleted, $id, $vendorsAcct]);
        $this->setDefaultDecorators([
            'vendor_id',
            'status',
            'vendor_acct',
        ]);
    }
}

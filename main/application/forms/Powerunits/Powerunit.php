<?php

use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_System_SystemValues as SystemValues;

class Application_Form_Powerunits_Powerunit extends Application_Form_Base
{
    public $contractors = [];

    public function init()
    {
        $this->setName('Power Unit');
        parent::init();

        $contractor = new Contractor();
        $this->contractors = $contractor->getAllContractorsOrderedByCode();
        $contractorCodes = $this->getContractorCodes();
        $contractorIds = $this->getContractorIds();
        $contractorCompanyNames = $this->getContractorCompanyNames();

        $id = new Application_Form_Element_Hidden('id');

        $carrierId = new Application_Form_Element_Hidden('carrier_id');

        $code = new Zend_Form_Element_Text('code');
        $code->setLabel('Code')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator(
                'Db_NoRecordExists',
                false,
                [
                    'table' => 'powerunit',
                    'field' => 'code',
                    'exclude' => [
                        'field' => 'id',
                        'value' => (int)Zend_Controller_Front::getInstance()->getRequest()->get('id'),
                    ],
                    'messages' => 'Sorry, it looks like "%value%" belongs to an existing Powerunit.',
                ]
            );

        $contractorId = (new Zend_Form_Element_Select('contractor_id'))
            ->setLabel('Contractor ID ')
            ->addMultiOptions($contractorIds)
            ->setAttrib('readonly', 'readonly');

        $contractorCode = (new Zend_Form_Element_Select('contractor_code'))
            ->setLabel('Contractor Code ')
            ->addMultiOptions($contractorCodes)
            ->setRequired();

        $contractorName = (new Zend_Form_Element_Select('contractor_name'))
            ->setLabel('Contractor Name ')
            ->addMultiOptions($contractorCompanyNames)
            ->setAttrib('readonly', 'readonly');

        $startDate = (new Zend_Form_Element_Text('start_date'))
            ->setLabel('In Service Date')
            ->addValidator('DateDatetime')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $terminationDate = (new Zend_Form_Element_Text('termination_date'))
            ->setLabel('Inactive Date ')
            ->addValidator('DateDatetime')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $status = (new Zend_Form_Element_Select('status'))
            ->setLabel('Status ')
            ->addMultiOptions(
                [
                    Application_Model_Entity_System_PowerunitStatus::STATUS_ACTIVE => 'Active',
                    Application_Model_Entity_System_PowerunitStatus::STATUS_INACTIVE => 'Inactive',
                    Application_Model_Entity_System_PowerunitStatus::STATUS_UNAVAILABLE => 'Unavailable',
                ]
            );

        $domicile = (new Zend_Form_Element_Text('domicile'))
            ->setLabel('Domicile')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $plateOwner = (new Zend_Form_Element_Select('plate_owner'))
            ->setLabel('Plate Owner ')
            ->setRequired()
            ->addMultiOptions(
                [
                    Application_Model_Entity_System_PowerunitOwnerType::OWNER_TYPE_FA => 'FA',
                    Application_Model_Entity_System_PowerunitOwnerType::OWNER_TYPE_CONTRACTOR => 'Contractor',
                ]
            );

        $form2290 = (new Zend_Form_Element_Select('form2290'))
            ->setLabel('2290')
            ->setRequired()
            ->addMultiOptions(
                [
                    1 => 'Yes',
                    0 => 'No',
                ]
            );

        $iftaFilingOwner = (new Zend_Form_Element_Select('ifta_filing_owner'))
            ->setLabel('IFTA Filing Owner')
            ->setRequired()
            ->addMultiOptions(
                [
                    Application_Model_Entity_System_PowerunitOwnerType::OWNER_TYPE_FA => 'FA',
                    Application_Model_Entity_System_PowerunitOwnerType::OWNER_TYPE_CONTRACTOR => 'Contractor',
                ]
            );

        $vin = (new Zend_Form_Element_Text('vin'))
            ->setLabel('Vin')
            ->setRequired()
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $tractorYear = (new Zend_Form_Element_Text('tractor_year'))
            ->setLabel('Tractor Year')
            ->setRequired()
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator(
                'Regex',
                false,
                [
                    'pattern' => '/\\d{4}/',
                    'messages' => 'Invalid format! Example: 2020',
                ]
            );

        $license = (new Zend_Form_Element_Text('license'))
            ->setLabel('License')
            ->setRequired()
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $licenseStateOptions = SystemValues::getStates();
        $licenseState = (new Zend_Form_Element_Select('license_state'))
            ->setLabel('License State')
            ->setRequired()
            ->addMultiOptions($licenseStateOptions)
            ->addValidator('CountryStates');

        $this->addElements(
            [
                $carrierId,
                $id,
                $code,
                $contractorId,
                $contractorCode,
                $startDate,
                $terminationDate,
                $status,
                $domicile,
                $plateOwner,
                $iftaFilingOwner,
                $contractorName,
                $form2290,
                $vin,
                $tractorYear,
                $license,
                $licenseState,
            ]
        );

        $this->setDefaultDecorators(
            [
                'carrier_id',
                'id',
                'code',
                'contractor_id',
                'contractor_code',
                'contractor_name',
                'start_date',
                'termination_date',
                'status',
                'domicile',
                'plate_owner',
                'form2290',
                'ifta_filing_owner',
                'vin',
                'tractor_year',
                'license',
                'license_state',
            ]
        );

        $this->addSubmit('Save');
    }

    public function getContractorCodes()
    {
        $codes = [];
        foreach ($this->contractors as $code => $contractor) {
            $codes[$code] = $code;
        }

        return $codes;
    }

    public function getContractorFields($field)
    {
        $options = [];
        foreach ($this->contractors as $contractor) {
            $options[$contractor[$field]] = $contractor[$field];
        }

        return $options;
    }

    public function getContractorIds()
    {
        return $this->getContractorFields('entity_id');
    }

    public function getContractorCompanyNames()
    {
        return $this->getContractorFields('company_name');
    }
}

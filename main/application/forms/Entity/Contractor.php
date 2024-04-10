<?php

class Application_Form_Entity_Contractor extends Application_Form_Base
{
    use Application_Form_ContactSubformTrait;
    use Application_Form_VendorSubformTrait;

    protected $encryptedFields = ['social_security_id', 'tax_id'];

    public function init()
    {
        $this->setName('contractor');
        parent::init();

        $id = new Application_Form_Element_Hidden('id');
        $status = new Application_Form_Element_Hidden('status');

        $entityId = new Application_Form_Element_Hidden('entity_id');
        $carrierId = new Application_Form_Element_Hidden('carrier_id');

        $socialSecurityId = new Zend_Form_Element_Text('social_security_id');
        $socialSecurityId
            ->setAttrib('id', 'contractor_social_security_id')
            ->setLabel('Last 4 - Social Security # ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator(
                'Regex',
                false,
                [
                    'pattern' => '/\\d{4}/',
                    'messages' => 'Invalid format! Example: ####',
                ]
            );

        $taxId = new Zend_Form_Element_Text('tax_id');
        $taxId
            ->setAttrib('id', 'contractor_tax_id')
            ->setLabel('Last 4 - Fed Tax ID ')
            ->setRequired()
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator(
                'Regex',
                false,
                [
                    'pattern' => '/\\d{4}/',
                    'messages' => 'Invalid format! Example: ####',
                ]
            );

        $code = new Zend_Form_Element_Text('code');
        $code->setLabel('Code ')->setRequired()->addFilter('StripTags')->addFilter('StringTrim');
        $validator = new Application_Model_Validate_ContractorCode();
        $code->addValidator($validator);

        $companyName = new Zend_Form_Element_Text('company_name');
        $companyName->setLabel('Contractor ')->setRequired()->addFilter('StripTags')->addFilter('StringTrim');

        $bookkeepingType = new Zend_Form_Element_Select('bookkeeping_type_id');
        $bookkeepingType
            ->setLabel('Bookkeeping Service ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addMultiOptions([
                '' => 'None',
                Application_Model_Entity_System_BookkeepingType::TYPE_ATBS => 'ATBS',
                Application_Model_Entity_System_BookkeepingType::TYPE_EQUINOX => 'Equinox',
            ]);

        $firstName = new Zend_Form_Element_Text('first_name');
        $firstName->setLabel('Contact First Name ')->setRequired()->addFilter('StripTags')->addFilter('StringTrim');

        $lastName = new Zend_Form_Element_Text('last_name');
        $lastName->setLabel('Contact Last Name ')->setRequired()->addFilter('StripTags')->addFilter('StringTrim');

        $middleInitial = new Zend_Form_Element_Text('middle_initial');
        $middleInitial->setLabel('Middle Initial')->addFilter('StripTags')->addFilter('StringTrim');

        $contactPersonType = (new Zend_Form_Element_Select('contact_person_type'))
            ->setLabel('Contact Person Type')
            ->setRequired()
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addMultiOptions([
                Application_Model_Entity_System_ContractorPersonType::TYPE_OWNER => 'Owner',
                Application_Model_Entity_System_ContractorPersonType::TYPE_REPRESENTATIVE => 'Representative',
            ]);

        $stateOfOperation = (new Zend_Form_Element_Select('state_of_operation'))
            ->setLabel('State of Issuance ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addMultiOptions([
                '-' => '-',
                'AL' => 'AL',
                'AK' => 'AK',
                'AZ' => 'AZ',
                'AR' => 'AR',
                'CA' => 'CA',
                'CO' => 'CO',
                'CT' => 'CT',
                'DE' => 'DE',
                'DC' => 'DC',
                'FL' => 'FL',
                'GA' => 'GA',
                'HI' => 'HI',
                'ID' => 'ID',
                'IL' => 'IL',
                'IN' => 'IN',
                'IA' => 'IA',
                'KS' => 'KS',
                'KY' => 'KY',
                'LA' => 'LA',
                'ME' => 'ME',
                'MD' => 'MD',
                'MA' => 'MA',
                'MI' => 'MI',
                'MN' => 'MN',
                'MS' => 'MS',
                'MO' => 'MO',
                'MT' => 'MT',
                'NE' => 'NE',
                'NV' => 'NV',
                'NH' => 'NH',
                'NJ' => 'NJ',
                'NM' => 'NM',
                'NY' => 'NY',
                'NC' => 'NC',
                'ND' => 'ND',
                'OH' => 'OH',
                'OK' => 'OK',
                'OR' => 'OR',
                'PA' => 'PA',
                'RI' => 'RI',
                'SC' => 'SC',
                'SD' => 'SD',
                'TN' => 'TN',
                'TX' => 'TX',
                'UT' => 'UT',
                'VT' => 'VT',
                'VA' => 'VA',
                'WA' => 'WA',
                'WV' => 'WV',
                'WI' => 'WI',
                'WY' => 'WY',
            ]);

        $expires = new Zend_Form_Element_Text('expires');
        $expires->setLabel('Expires ')->addValidator('DateDatetime')->addFilter('StripTags')->addFilter('StringTrim');

        $dob = new Zend_Form_Element_Text('dob');
        $dob->setLabel('DOB ')->addValidator('DateDatetime')->addFilter('StripTags')->addFilter('StringTrim');

        $driver_license = new Zend_Form_Element_Text('driver_license');
        $driver_license->setLabel('Driver\'s License #  ')->addFilter('StripTags')->addFilter('StringTrim');

        $classification = new Zend_Form_Element_Text('classification');
        $classification->setLabel('Classification ')->addFilter('StripTags')->addFilter('StringTrim');

        $division = new Zend_Form_Element_Text('division');
        $division->setLabel('Division ')->addFilter('StripTags')->addFilter('StringTrim');

        $department = new Zend_Form_Element_Text('department');
        $department->setLabel('Department ')->addFilter('StripTags')->addFilter('StringTrim');

        $route = new Zend_Form_Element_Text('route');
        $route->setLabel('Route ')->addFilter('StripTags')->addFilter('StringTrim');

        $settlementGroup = new Zend_Form_Element_Select('settlement_group_id');
        $settlementGroupOptions = (new Application_Model_Entity_Settlement_Group())
            ->getResource()
            ->getOptions('code', 'deleted = 0');
        $settlementGroupOptions = ['' => 'None'] + $settlementGroupOptions;
        $settlementGroup
            ->setLabel('Settlement Group ')
            ->addMultiOptions($settlementGroupOptions);

        $correspondenceMethod = new Zend_Form_Element_Select('correspondence_method');
        $correspondenceMethod->setLabel('Settlement Delivery ')->addMultiOptions(
            [
                Application_Model_Entity_Entity_Contact_Type::TYPE_EMAIL => 'Yes',
                Application_Model_Entity_Entity_Contact_Type::TYPE_CARRIER_DISTRIBUTES => 'No',
            ]
        );

        $carrierName = new Zend_Form_Element_Text('carrier_name');
        $carrierName->setLabel('Vendor')->setAttrib('disabled', 'disabled');
        $carrierStatusId = new Zend_Form_Element_Select('carrier_status_id');
        $carrierStatusId->setLabel('Status');
        $carrierStatusId->addMultiOptions([
            Application_Model_Entity_System_VendorStatus::STATUS_ACTIVE => 'Approved',
            Application_Model_Entity_System_VendorStatus::STATUS_RESCINDED => 'Rescinded',
        ]);

        $statusTitle = new Zend_Form_Element_Select('status_title');
        $statusTitle
            ->setLabel('Status ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addMultiOptions([
                Application_Model_Entity_System_ContractorStatus::STATUS_ACTIVE => 'Active',
                Application_Model_Entity_System_ContractorStatus::STATUS_INACTIVE => 'Inactive',
            ]);

        $genderId = new Zend_Form_Element_Select('gender_id');
        $genderId->setLabel('Gender');
        $genderId->addMultiOptions([
            Application_Model_Entity_System_SystemValues::NOT_CONFIGURED_STATUS => '--',
            Application_Model_Entity_System_SystemValues::GENDER_MALE => 'Male',
            Application_Model_Entity_System_SystemValues::GENDER_FEMALE => 'Female',
        ]);

        $startDate = (new Zend_Form_Element_Text('start_date'))
            ->setLabel('Start Date ')
            ->addValidator('DateDatetime')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $terminationDate = (new Zend_Form_Element_Text('termination_date'))
            ->setLabel('Termination Date ')
            ->addValidator('DateDatetime')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $rehireDate = (new Zend_Form_Element_Text('rehire_date'))
            ->setLabel('Restart Date ')
            ->addValidator('DateDatetime')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $notes = (new Zend_Form_Element_Textarea('notes'))
            ->setLabel('Notes ')
            ->setAttrib('style', 'height: 72px;')
            ->setAttrib('rows', 4)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('StringLength', false, [1, 250]);

        $file = (new Zend_Form_Element_File('file1'))
            ->removeDecorator('Label')
            ->setAttrib('id', 'file1')
            ->setAttrib('class', 'hide')
            ->setAttrib('name', 'file1')
            ->setDestination(
                Application_Model_File::getStorage()
            );

        $countryCode = (new Zend_Form_Element_Select('country_code'))
            ->setLabel('Country Code')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addMultiOptions([
                'US' => 'US',
                'CAN' => 'CAN',
                'MEX' => 'MEX',
            ]);

        $deductionPriority = new Application_Form_Element_Hidden('deduction_priority');

        $deductionPriorityTitle = new Zend_Form_Element_Text('deduction_priority_title');
        $deductionPriorityTitle->setAttrib('readonly', 'readonly')->setLabel('Deduction Priority')->setValue('Master');

        $this->addElements(
            [
                $id,
                $entityId,
                $socialSecurityId,
                $taxId,
                $code,
                $companyName,
                $carrierId,
                $carrierName,
                $carrierStatusId,
                $firstName,
                $lastName,
                $contactPersonType,
                $correspondenceMethod,
                $statusTitle,
                $status,
                $startDate,
                $terminationDate,
                $rehireDate,
                $deductionPriorityTitle,
                $deductionPriority,
                $settlementGroup,
                $bookkeepingType,
                $notes,
                $file,
                $countryCode,
            ]
        );

        $this->setDefaultDecorators(
            [
                'social_security_id',
                'tax_id',
                'code',
                'company_name',
                'first_name',
                'last_name',
                'contact_person_type',
                'state_of_operation',
                'dob',
                'driver_license',
                'expires',
                'classification',
                'division',
                'department',
                'route',
                'middle_initial',
                'correspondence_method',
                'status_title',
                'carrier_status_id',
                'carrier_name',
                'gender_id',
                'start_date',
                'termination_date',
                'rehire_date',
                'deduction_priority_title',
                'settlement_group_id',
                'bookkeeping_type_id',
                'notes',
                'powerunits',
                'country_code',
            ]
        );

        if (!Application_Model_Entity_Accounts_User::getCurrentUser()->hasPermission(
            Application_Model_Entity_Entity_Permissions::CONTRACTOR_MANAGE
        )
            || Application_Model_Entity_Accounts_User::getCurrentUser()->isSpecialist()
            || Application_Model_Entity_Accounts_User::getCurrentUser()->isOnboarding()) {
            $this->readonly();
        } else {
            $this->addSubmit('Save');
        }

        if (!Application_Model_Entity_Accounts_User::getCurrentUser()->hasPermission(
            Application_Model_Entity_Entity_Permissions::CONTRACTOR_VENDOR_AUTH_MANAGE
        )) {
            $this->carrier_name->setAttrib('readonly', 'readonly');
            $this->carrier_status_id->setAttrib('readonly', 'readonly');
        }
    }
}

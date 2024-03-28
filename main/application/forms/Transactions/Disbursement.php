<?php

use Application_Model_Entity_Accounts_User as User;

class Application_Form_Transactions_Disbursement extends Application_Form_Base
{
    public function init()
    {
        $this->setName('transactions_disbursement');
        parent::init();

        $id = new Zend_Form_Element_Hidden('id');

        $reissueParentId = new Zend_Form_Element_Hidden('reissue_parent_id');

        $entityId = new Zend_Form_Element_Hidden('entity_id');

        $processType = new Zend_Form_Element_Hidden('process_type');

        $createdBy = new Zend_Form_Element_Hidden('created_by');

        $approvedBy = new Zend_Form_Element_Hidden('approved_by');

        $status = new Zend_Form_Element_Hidden('status');

        $settlementCycleId = new Zend_Form_Element_Hidden('settlement_cycle_id');

        $settlementCycleIdTitle = new Zend_Form_Element_Text('settlement_cycle_id_title');
        $settlementCycleIdTitle->setLabel('Settlement Cycle ')->setAttrib('readonly', 'readonly');

        $processTypeTitle = new Zend_Form_Element_Text('process_type_title');
        $processTypeTitle->setLabel('Type')->setRequired(true)->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->setAttrib('readonly', 'readonly');

        $description = new Zend_Form_Element_Text('description');
        $description->setLabel('Description ')->addFilter('StripTags')->addFilter('StringTrim');

        $amount = new Application_Form_Element_Money('amount', ['long' => true]);
        $amount->setLabel('Amount')->setRequired();

        $disbursementCode = new Zend_Form_Element_Text('disbursement_code');
        $disbursementCode->setLabel('Disbursement Code ')->addFilter('StripTags')->addFilter('StringTrim');

        $disbursementDate = new Zend_Form_Element_Text('disbursement_date');
        $disbursementDate->setLabel('Disbursement Date ')->addFilter('StripTags')->addFilter('StringTrim')->setAttrib(
            'readonly',
            'readonly'
        );

        $disbursementReference = new Zend_Form_Element_Text('disbursement_reference');
        $disbursementReference->setLabel('Disbursement Reference')->setAttrib('readonly', 'readonly');

        $approvedDatetime = new Zend_Form_Element_Text('approved_datetime');
        $approvedDatetime->setLabel('Approved Date ')->addFilter('StripTags')->addFilter('StringTrim')->setAttrib(
            'readonly',
            'readonly'
        );

        $approvedByTitle = new Zend_Form_Element_Text('approved_by_title');
        $approvedByTitle->setLabel('Approved By')->setAttrib('readonly', 'readonly');

        $createdDatetime = new Zend_Form_Element_Text('created_datetime');
        $createdDatetime->setLabel('Created Date ')->addFilter('StripTags')->addFilter('StringTrim')->setAttrib(
            'readonly',
            'readonly'
        );

        $createdByTitle = new Zend_Form_Element_Text('created_by_title');
        $createdByTitle->setLabel('Created By')->addFilter('StripTags')->addFilter('StringTrim')->setAttrib(
            'readonly',
            'readonly'
        );

        $statusTitle = new Zend_Form_Element_Text('status_title');
        $statusTitle->setLabel('Status')->setAttrib('readonly', 'readonly');

        $reserveAccountId = new Zend_Form_Element_Hidden('reserve_account_contractor');

        $entityIdTitle = new Zend_Form_Element_Text('entity_id_title');
        $entityIdTitle->setLabel('Recipient')->setRequired(true)->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->setAttrib('href', '#entity_id_modal')->setAttrib('data-toggle', 'modal');

        $this->addElements(
            [
                $id,
                $entityId,
                $settlementCycleId,
                $processType,
                $reserveAccountId,
                $approvedBy,
                $createdBy,
                $status,
                $entityIdTitle,
                $processTypeTitle,
                $description,
                $settlementCycleIdTitle,
                $disbursementCode,
                $disbursementDate,
                $amount,
                $disbursementReference,
                $approvedDatetime,
                $approvedByTitle,
                $createdDatetime,
                $createdByTitle,
                $statusTitle,
                $reissueParentId,
            ]
        );

        $this->setDefaultDecorators(
            [
                'entity_id_title',
                'process_type_title',
                'description',
                'settlement_cycle_id_title',
                'disbursement_code',
                'disbursement_reference',
                'disbursement_date',
                'amount',
                'approved_datetime',
                'approved_by_title',
                'created_datetime',
                'created_by_title',
                'status_title',
                'reissue_parent_id',
            ]
        );
        $this->addSubmit('Save');
    }

    public function setupForNewAction()
    {
        $this->defaultSetup();
        $this->amount->setValue('0.00');
    }

    public function setupForEditAction()
    {
        $this->defaultSetup();

        $this->entity_id_title->setAttrib('readonly', 'readonly');

        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load($this->settlement_cycle_id->getValue());

        if ($cycle->getDisbursementStatus() == Application_Model_Entity_System_PaymentStatus::APPROVED_STATUS) {
            $this->description->setAttrib('readonly', 'readonly');
            $this->disbursement_code->setAttrib('readonly', 'readonly');
            $this->amount->setAttrib('readonly', 'readonly');
            $this->removeElement('submit');
        }
    }

    public function defaultSetup()
    {
        if (!$this->entity_id->getValue()) {
            $currentContractor = User::getCurrentUser()->getSelectedContractor();
            if ($currentContractor) {
                $this->entity_id->setValue($currentContractor->getEntityId());
            }
        }

        $cycleEntity = (new Application_Model_Entity_Settlement_Cycle())->load($this->settlement_cycle_id->getValue());
        $entity = (new Application_Model_Entity_Entity())->load($this->entity_id->getValue());
        $disbursement = Application_Model_Entity_Transactions_Disbursement::staticLoad($this->id->getValue());

        if (!$this->settlement_cycle_id_title->getValue()) {
            $this->settlement_cycle_id_title->setValue($cycleEntity->getCyclePeriodString());
        }

        if (!$this->disbursement_date->getValue()) {
            if ($disbursement->getReissueParentId()) {
                $date = DateTime::createFromFormat('Y-m-d', $disbursement->getTransactionDisbursementDate());
                if ($date) {
                    $this->getElement('disbursement_date')->setValue($date->format('n/j/Y'));
                }
            } else {
                $cycleEntity->changeDateFormat(['disbursement_date'], true);
                $this->getElement('disbursement_date')->setValue($cycleEntity->getDisbursementDate());
            }
        }

        if (!$this->entity_id_title->getValue()) {
            $this->getElement('entity_id_title')->setValue($entity->getName());
        }

        if (!$this->process_type_title->getValue()) {
            $processType = (new Application_Model_Entity_System_DisbursementTransactionTypes())->load(
                $this->process_type->getValue()
            );
            $this->process_type_title->setValue($processType->getValue());
        }

        if (!User::getCurrentUser()->hasPermission(Application_Model_Entity_Entity_Permissions::DISBURSEMENT_MANAGE)) {
            foreach ($this->getElements() as $element) {
                $element->setAttrib('readonly', 'readonly');
            }
            $this->removeElement('submit');
        }
    }

    public function isValid($data)
    {
        if (isset($data['process_type']) && $data['process_type'] == Application_Model_Entity_System_DisbursementTransactionTypes::PAYMENT_TRANSACTION_TYPE) {
            $this->amount->addValidator(
                'GEThan',
                false,
                [
                    'min' => 0,
                    'messages' => 'Amount should be greater or equals to 0',
                ]
            );
        }

        return parent::isValid($data);
    }
}

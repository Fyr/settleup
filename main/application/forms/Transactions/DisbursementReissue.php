<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Transactions_Disbursement as Disbursement;

class Application_Form_Transactions_DisbursementReissue extends Application_Form_Transactions_Disbursement
{
    public function init()
    {
        parent::init();
        $this->entity_id_title->setAttrib('readonly', 'readonly');
        $this->entity_id_title->setAttrib('href', null);
        $this->entity_id_title->setAttrib('data-toggle', null);
        $this->process_type_title->setValue('Settlement');
        $this->disbursement_date->setRequired(true);
        $this->disbursement_date->setAttrib('readonly', null);
        $this->disbursement_date->addValidator('Date');
        $this->amount->setAttrib('readonly', 'readonly');
        $this->created_datetime->setValue(date("n/j/Y"));
        $this->created_by_title->setValue(User::getCurrentUser()->getName());
    }

    public function setupForNewAction()
    {
        $parentId = $this->getElement('reissue_parent_id')->getValue();
        $disbursement = Disbursement::staticLoad($parentId);
        $entity = $disbursement->getEntity();
        $cycle = $disbursement->getSettlementCycle();

        $this->entity_id_title->setValue($entity->getName());
        $this->settlement_cycle_id_title->setValue($cycle->getCyclePeriodString());
        $this->amount->setValue($disbursement->getAmount());
    }
}

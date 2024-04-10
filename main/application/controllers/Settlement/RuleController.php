<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_Settlement_Cycle as Cycle;
use Application_Model_Entity_System_CyclePeriod as CyclePeriod;

class Settlement_RuleController extends Zend_Controller_Action
{
    use Application_Plugin_RedirectToIndex;

    protected $_entity;
    protected $_form;
    protected $_title = 'Settlement Cycle Rules';
    protected $_contractorEntity;
    protected $_vendorEntity;
    protected $_carrierEntity;

    public function init()
    {
        $this->_entity = new Application_Model_Entity_Settlement_Rule();
        $this->_form = new Application_Form_Settlement_Rule();
    }

    public function indexAction()
    {
        if (!User::getCurrentUser()->isAdminOrSuperAdmin()) {
            $this->_redirect('/');
        }
        $this->view->gridModel = new Application_Model_Grid_Settlement_Rules();
    }

    public function listAction()
    {
        if (User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_RULE_VIEW)) {
            $this->_forward('edit');
        } else {
            $this->_helper->redirector('index', 'settlement_index');
        }
    }

    public function newAction()
    {
        if (User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_RULE_MANAGE)) {
            $this->_forward('edit');
        } else {
            $this->_helper->redirector('index', 'settlement_index');
        }
    }

    public function editAction()
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_RULE_VIEW)) {
            $this->_helper->redirector('index', 'settlement_index');

            return;
        }
        $this->view->title = $this->_title;
        $this->view->form = $this->_form;
        $id = $this->getRequest()->getParam('id');
        $carrierEntityId = User::getCurrentUser()->getSelectedCarrier()->getEntity()->getId();
        if ($this->getRequest()->isPost()) {
            if (!User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_RULE_MANAGE)) {
                $this->_helper->redirector('index', 'settlement_index');
            }
            $post = $this->getRequest()->getPost();
            $post = $this->configureForm($post);
            if ($this->_form->isValid($post)) {
                if ($this->_form->cycle_period_id->getValue()) {
                    $this->_entity->setData($this->_form->getValues());
                    if (!$id) {
                        $this->_entity->setCarrierId($carrierEntityId);
                    }
                    $this->_entity->changeDateFormat([
                        'cycle_start_date',
                    ]);
                } else {
                    $this->_entity->load($this->_form->id->getValue());
                    $this->_entity->setPaymentTerms($this->_form->payment_terms->getValue());
                    $this->_entity->setDisbursementTerms($this->_form->disbursement_terms->getValue());
                }
                $this->_entity->changeRecurringData();
                $this->_entity->save();

                if ($this->_getParam('redirect')) {
                    $this->_redirect('/settlement_index');
                } else {
                    if ((new Cycle())->getCollection()->addCarrierFilter()->addSettlementGroupFilter()->getActiveCycle()->getId()) {
                        $this->redirectToIndex();
                    } else {
                        $this->_helper->redirector('new', 'settlement_index');
                    }
                }
            } else {
                $this->_form->populate($post);
            }
        } else {
            if ($id) {
                $this->_entity->load($id);
            } elseif ($carrierEntityId > 0) {
                $this->_entity->load($carrierEntityId, 'carrier_id');
            }

            $this->_entity->changeDateFormat([
                'cycle_start_date',
            ], true)->changeRecurringData(true);

            $this->_form->populate($this->_entity->getData());

            if ($this->_form->id->getValue()) {
                $this->_form->cycle_period_id->setAttrib('readonly', 'readonly');
            }
        }
        $this->_form->configure();
    }

    public function configureForm($post)
    {
        $periodId = $post['cycle_period_id'];
        switch ($periodId) {
            case CyclePeriod::WEEKLY_PERIOD_ID:
            case CyclePeriod::BIWEEKLY_PERIOD_ID:
                $this->_form->first_start_day->setRequired(false);
                $this->_form->second_start_day->setRequired(false);
                unset($post['first_start_day']);
                unset($post['second_start_day']);
                break;
            case CyclePeriod::MONTHLY_PERIOD_ID:
                $this->_form->second_start_day->setRequired(false);
                $this->_form->week_day->setRequired(false);
                unset($post['second_start_day']);
                unset($post['week_day']);
                break;
            case CyclePeriod::MONTHLY_SEMI_MONTHLY_ID:
            case CyclePeriod::SEMY_MONTHLY_PERIOD_ID:
                $this->_form->week_day->setRequired(false);
                unset($post['week_day']);
                break;
        }

        return $post;
    }
}

<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_Payments_Setup as PaymentSetup;
use Application_Model_Entity_System_CyclePeriod as CyclePeriod;
use Application_Model_Entity_System_SetupLevels as SetupLevel;

class Payments_SetupController extends Zend_Controller_Action
{
    /** @var Application_Model_Entity_Payments_Setup */
    protected $_entity;
    /** @var Application_Form_Payments_Setup */
    protected $_form;

    public function init()
    {
        $this->_entity = new PaymentSetup();
        $this->_form = new Application_Form_Payments_Setup();
    }

    public function indexAction()
    {
        if (User::getCurrentUser()->hasPermission(Permissions::TEMPLATE_VIEW)) {
            $this->forward('list');
        } else {
            $this->_helper->redirector('index', 'settlement_index');
        }
    }

    public function newAction()
    {
        if (User::getCurrentUser()->hasPermission(Permissions::TEMPLATE_VIEW)) {
            $this->forward('edit');
        } else {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $this->forward('edit');
    }

    public function editAction()
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::TEMPLATE_VIEW)) {
            $this->_helper->redirector('index', 'settlement_index');
        }

        $this->view->title = 'Master Compensation Template Detail';
        $this->view->form = $this->_form;

        $id = $this->_getParam('id', 0);
        if ($id) {
            $this->_entity = PaymentSetup::staticLoad($id);
        }

        if ($this->getRequest()->isPost()) {
            if (!User::getCurrentUser()->hasPermission(Permissions::TEMPLATE_MANAGE)) {
                $this->_helper->redirector('index', 'settlement_index');
            }
            $post = $this->configureForm($this->getRequest()->getPost());
            if ($this->_form->isValid($post)) {
                $formData = $this->_form->getValues();
                if ($this->_entity->getId() && isset($formData['payment_code'])) {
                    unset($formData['payment_code']);
                }
                $this->_entity->setData($formData);

                $this->_entity->changeRecurringData();
                if ($this->_entity->getId() && $this->_entity->getLevelId() == SetupLevel::INDIVIDUAL_LEVEL_ID) {
                    $this->_entity->setChanged(1);
                }
                $this->_entity->changeDateFormat(['biweekly_start_day']);
                $this->_entity->save();

                if ($formData['id']) {
                    if ($this->_entity->getLevelId() == SetupLevel::MASTER_LEVEL_ID) {
                        $this->_entity->updateIndividualTemplates();
                        //                        $this->_entity->updateNextRecurrings();
                    } else {
                        //                        $this->_entity->updateNextRecurrings();
                        $this->_helper->redirector(
                            'list',
                            $this->_getParam('controller'),
                            'default',
                            ['entity' => $this->_entity->getContractorId()]
                        );
                    }
                } else {
                    $this->_entity->createIndividualTemplates();
                }

                $this->_helper->redirector('index');
            } else {
                $this->_form->getElement('billing_cycle_id')->setMultiOptions(
                    $this->_entity->getBillingCycleOptions()
                );
                $this->_form->populate($post);
            }
        } else {
            $this->_form->getElement('billing_cycle_id')->setMultiOptions(
                $this->_entity->getBillingCycleOptions()
            );
            if ($id > 0) {
                $this->_entity->load($id);
                $this->_entity->changeRecurringData(true);
                if (!$this->_entity->checkPermissions()) {
                    $this->_helper->redirector('index');
                }
                $this->_form->populate($this->_entity->changeDateFormat(['biweekly_start_day'], true)->getData());
            }
            if ($this->_form->billing_cycle_id->getValue() == CyclePeriod::MONTHLY_PERIOD_ID) {
                //                $this->_form->first_start_day->setLabel('Select Day of Month ');
                $this->_form->first_start_day->setLabel('Select Days * ');
            }
            if ($this->_form->billing_cycle_id->getValue() != CyclePeriod::SEMI_WEEKLY_PERIOD_ID) {
                //                $this->_form->week_day->setLabel('Select Day of Month ');
                $this->_form->week_day->setLabel('Select Days * ');
            }
        }
        $this->_form->configure();
        if ($this->_entity->getLevelId() == SetupLevel::INDIVIDUAL_LEVEL_ID) {
            $this->view->title = 'Individual Compensation Template Detail';
        }
        $this->view->hideContractorSelector = true;
    }

    public function listAction()
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::TEMPLATE_VIEW)) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $this->view->gridModel = new Application_Model_Grid_Payment_Setup();
        $this->view->hideContractorSelector = true;
    }

    public function deleteAction()
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::TEMPLATE_MANAGE)) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $id = (int)$this->getRequest()->getParam('id');
        $this->_entity->load($id);
        if ($this->_entity->checkPermissions()) {
            if ($this->_entity->getLevelId() == SetupLevel::MASTER_LEVEL_ID) {
                $this->_entity->setDeleted(1);
                $this->_entity->save();
                $this->_entity->deleteIndividualTemplates();
            }
        }
        $this->_helper->redirector('index');
    }

    public function multiactionAction()
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::TEMPLATE_MANAGE)) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $ids = explode(',', (string) $this->_getParam('ids'));
        foreach ($ids as $id) {
            $this->_entity->load((int)$id);
            if ($this->_entity->checkPermissions()) {
                if ($this->_entity->getLevelId() == SetupLevel::MASTER_LEVEL_ID) {
                    $this->_entity->setDeleted(1);
                    $this->_entity->save();
                    $this->_entity->deleteIndividualTemplates();
                }
            }
        }
        $this->_helper->redirector('index');
    }

    public function configureForm($post)
    {
        $periodId = $post['billing_cycle_id'];
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

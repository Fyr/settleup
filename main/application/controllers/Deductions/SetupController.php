<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_System_SetupLevels as SetupLevel;
use Application_Model_Entity_System_SystemValues as SystemValues;

class Deductions_SetupController extends Zend_Controller_Action
{
    /** @var Application_Model_Entity_Deductions_Setup */
    protected $_entity;
    /** @var Application_Form_Deductions_Setup */
    protected $_form;

    public function init()
    {
        $this->_entity = new Application_Model_Entity_Deductions_Setup();
        $this->_form = new Application_Form_Deductions_Setup();
    }

    public function indexAction()
    {
        $this->_forward('list');
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::TEMPLATE_VIEW)) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $this->view->title = 'Master Deduction Template Detail';
        $this->view->form = $this->_form;
        $this->view->hideContractorSelector = true;

        if ($this->getRequest()->isPost()) {
            if (!User::getCurrentUser()->hasPermission(Permissions::TEMPLATE_MANAGE)) {
                $this->_helper->redirector('index', 'settlement_index');
            }
            $post = $this->configureForm($this->getRequest()->getPost());

            if ($this->_form->isValid($post)) {
                $values = $this->_form->getValues();
                $this->_entity->setData($values);
                $this->_entity->changeRecurringData();
                if ($this->_entity->getId() && $this->_entity->getLevelId() == SetupLevel::INDIVIDUAL_LEVEL_ID) {
                    $this->_entity->setChanged(1);
                }
                $this->_entity->changeDateFormat(['biweekly_start_day']);
                $this->_entity->save();
                if ($values['id']) {
                    if ($this->_entity->getLevelId() == SetupLevel::MASTER_LEVEL_ID) {
                        $this->_entity->updateIndividualTemplates();
                        //                        $this->_entity->updateNextRecurrings();
                    }
                } else {
                    $this->_entity->createIndividualTemplates();
                    // $this->_entity->reorderPriority();
                }

                $this->_helper->redirector('index', $this->_getParam('controller'));
            } else {
                $this->_form->getElement('billing_cycle_id')->setMultiOptions(
                    $this->_entity->getBillingCycleOptions()
                );
                $this->_form->populate($post);
            }
        } else {
            $id = $this->_getParam('id', 0);
            if ($id > 0) {
                $entity = $this->_entity->load($id);
                if (!$this->_entity->checkPermissions()) {
                    $this->_helper->redirector('index');
                }
                $this->_entity->changeRecurringData(true);
                $data = $entity->getDefaultValues()->changeDateFormat('biweekly_start_day', true)->getData();
                $this->_form->getElement('billing_cycle_id')->setMultiOptions(
                    $this->_entity->getBillingCycleOptions()
                );
                $this->_form->populate($data);
            } else {
                $this->_form->getElement('billing_cycle_id')->setMultiOptions(
                    $this->_entity->getBillingCycleOptions()
                );
            }
            if ($this->_form->billing_cycle_id->getValue(
            ) == Application_Model_Entity_System_CyclePeriod::MONTHLY_PERIOD_ID) {
                //                $this->_form->first_start_day->setLabel('Select Day of Month ');
                $this->_form->first_start_day->setLabel('Select Days * ');
            }
        }
        $this->_form->configure();
        if ($this->_entity->getLevelId() == SetupLevel::INDIVIDUAL_LEVEL_ID) {
            $this->view->title = 'Individual Deduction Template Detail';
        }
        $this->_getPopupSettings($this->_form->provider_id->getValue());
    }

    public function listAction()
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::TEMPLATE_VIEW)) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $this->view->gridModel = new Application_Model_Grid_Deduction_Setup();
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
            $this->_entity->setDeleted(SystemValues::DELETED_STATUS);
            $this->_entity->save();
            if ($this->_entity->getLevelId() == SetupLevel::MASTER_LEVEL_ID) {
                $this->_entity->deleteIndividualTemplates();
                $this->_entity->reorderIndividualTemplates();
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
                $this->_entity->setDeleted(SystemValues::DELETED_STATUS);
                $this->_entity->save();
                if ($this->_entity->getLevelId() == SetupLevel::MASTER_LEVEL_ID) {
                    $this->_entity->deleteIndividualTemplates();
                }
            }
        }
        $this->_entity->reorderIndividualTemplates();
        $this->_helper->redirector('index');
    }

    public function updatetermsAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $providerId = $this->_getParam('providerId');
            $provider = (new Application_Model_Entity_Entity())->load($providerId)->getEntityByType();

            return $this->_helper->json->sendJson(['terms' => $provider->getTerms()]);
        } else {
            $this->_helper->redirector('index');
        }
    }

    private function _getPopupSettings($entityId)
    {
        $this->view->popupSetup = array_merge(
            //            $this->_getRAPopup($entityId),
            $this->_getProviderPopup()
        );
    }

    private function _getProviderPopup()
    {
        if (!User::getCurrentUser()->isOnboarding()) {
            return [
                'provider' => [
                    'gridTitle' => 'Select provider',
                    'destFieldName' => 'provider_id',
                    'showClearButton' => false,
                    'idField' => 'entity_id',
                    'callbacks' => [
                        'id' => 'Application_Model_Grid_Callback_Text',
                        'tax_id' => 'Application_Model_Grid_Callback_Text',
                        'short_code' => 'Application_Model_Grid_Callback_Text',
                        'name' => 'Application_Model_Grid_Callback_Text',
                        'contact' => 'Application_Model_Grid_Callback_Text',
                    ],
                    'collections' => [
                        'Divisions' => $this->_entity->getCarrierCollection()->addConfiguredFilter(
                        )->addVisibilityFilterForUser(),
                        'Vendors' => $this->_entity->getVendorCollection()->addConfiguredFilter(
                        )->addVisibilityFilterForUser(),
                    ],
                ],
            ];
        } else {
            return [];
        }
    }

    public function configureForm($post)
    {
        $periodId = $post['billing_cycle_id'];
        switch ($periodId) {
            case Application_Model_Entity_System_CyclePeriod::WEEKLY_PERIOD_ID:
            case Application_Model_Entity_System_CyclePeriod::BIWEEKLY_PERIOD_ID:
                $this->_form->first_start_day->setRequired(false);
                $this->_form->second_start_day->setRequired(false);
                unset($post['first_start_day']);
                unset($post['second_start_day']);
                break;
            case Application_Model_Entity_System_CyclePeriod::MONTHLY_PERIOD_ID:
                $this->_form->second_start_day->setRequired(false);
                $this->_form->week_day->setRequired(false);
                unset($post['second_start_day']);
                unset($post['week_day']);
                break;
            case Application_Model_Entity_System_CyclePeriod::MONTHLY_SEMI_MONTHLY_ID:
            case Application_Model_Entity_System_CyclePeriod::SEMY_MONTHLY_PERIOD_ID:
                $this->_form->week_day->setRequired(false);
                unset($post['week_day']);
                break;
        }

        return $post;
    }
}

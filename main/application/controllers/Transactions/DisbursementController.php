<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_Settlement_Cycle as Cycle;
use Application_Model_Entity_System_PaymentStatus as PaymentStatus;
use Application_Model_Entity_System_SettlementCycleStatus as CycleStatus;

class Transactions_DisbursementController extends Zend_Controller_Action
{
    private ?Application_Model_Entity_Transactions_Disbursement $_entity = null;
    private ?Application_Form_Transactions_Disbursement $_form = null;

    public function init()
    {
        $this->_entity = new Application_Model_Entity_Transactions_Disbursement();
        $this->_form = new Application_Form_Transactions_Disbursement();
    }

    public function indexAction()
    {
        $this->_forward('list');
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function listAction()
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::DISBURSEMENT_VIEW)) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $this->view->gridModel = new Application_Model_Grid_Transaction_Disbursement();
        /** @var Application_Model_Entity_Settlement_Cycle $cycle */
        $cycle = $this->view->gridModel->getCycle();
        $this->view->cycle = $cycle;
        $vendors = $cycle->getVendorsWithNegativeDisbursements();
        if ($vendors && $cycle->getDisbursementStatus() == PaymentStatus::NOT_APPROVED_STATUS) {
            $message = 'A negative disbursement was created for vendor or carrier:';
            foreach ($vendors as $vendor) {
                $message .= '<br/> - ' . $vendor['name'];
            }
            $this->_helper->FlashMessenger(
                [
                    'type' => 'T_WARNING',
                    'title' => 'Warning!',
                    'message' => $message,
                ]
            );
        }
    }

    public function editAction()
    {
        $user = User::getCurrentUser();
        if (!$user->hasPermission(Permissions::DISBURSEMENT_VIEW)) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $this->view->title = 'Disbursement Details';
        $this->view->form = $this->_form;
        $id = $this->_getParam('id', 0);

        if ($this->getRequest()->isPost()) {
            if (!$user->hasPermission(Permissions::DISBURSEMENT_MANAGE)) {
                $this->_helper->redirector('index', 'settlement_index');
            }
            $post = $this->getRequest()->getPost();
            if ($this->_form->isValid($post)) {
                $values = $this->_form->getValues();

                if ($id) {
                    $this->_entity->load($this->_form->id->getValue());
                    if (!$this->_entity->checkPermissions()) {
                        $this->_helper->redirector('index');
                    }
                    if ($this->_entity->getSettlementCycle()->getDisbursementStatus(
                    ) == PaymentStatus::APPROVED_STATUS) {
                        $this->_helper->redirector(
                            'index',
                            $this->_getParam('controller')
                        );
                    }
                    unset($values['entity_id']);
                    unset($values['approved_by']);
                    unset($values['created_by']);
                    unset($values['approved_datetime']);
                    unset($values['created_datetime']);
                    unset($values['status']);
                    unset($values['settlement_cycle_id']);
                    unset($values['id']);
                    unset($values['disbursement_reference']);
                }
                $this->_entity->addData($values);
                $this->_entity->changeDateFormat(
                    [
                        'created_datetime',
                        'approved_datetime',
                        'settlement_cycle_close_date',
                    ]
                );
                $this->_entity->save();
                $this->_helper->redirector(
                    'index',
                    $this->_getParam('controller')
                );
            } else {
                $this->_form->populate($post);
                $this->_entity->load($this->_form->id->getValue());
            }
        } else {
            if ($id > 0) {
                $this->_entity->load($id);
                if (!$this->_entity->checkPermissions()) {
                    $this->_helper->redirector('index');
                }
                $this->_form->populate(
                    $this->_entity->getDefaultData()->changeDateFormat(
                        [
                                'created_datetime',
                                'approved_datetime',
                                'settlement_cycle_close_date',
                            ],
                        true
                    )->getData()
                );
                $allowReissue = $this->_entity->isAllowReissue();
                if ($allowReissue) {
                    $this->view->allowReissue = true;
                    $contractor = Contractor::staticLoad($this->_entity->getEntityId(), 'entity_id');
                }
            } else {
                if (!(int)($cycleId = $this->_getParam('cycle'))) {
                    $cycleId = $this->getRequest()->getCookie('settlement_cycle_id');
                }
                if ($cycleId) {
                    if ((new Cycle())->load($cycleId)->getStatusId() == CycleStatus::APPROVED_STATUS_ID) {
                        $this->_form->settlement_cycle_id->setValue($cycleId);
                    } else {
                        $this->_helper->redirector('index');
                    }
                } else {
                    $this->_helper->redirector('index');
                }
            }
        }
        $this->_form->configure();
        $this->view->popupSetup = $this->_getPopupSettings();
    }

    public function approveAction()
    {
        $cycle = Cycle::staticLoad((int)$this->getRequest()->getParam('id'));
        $user = User::getCurrentUser();
        if ($user->hasPermission(Permissions::DISBURSEMENT_APPROVE)) {
            if ($cycle->getStatusId() == CycleStatus::APPROVED_STATUS_ID && $cycle->getDisbursementStatus(
            ) == PaymentStatus::NOT_APPROVED_STATUS && $cycle->getCarrierId() == $user->getCarrierEntityId(
            ) && !$cycle->getDisbursementErrors()) {
                $cycle->approveDisbursements();
                $user->reloadCycle();
            }
        } else {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $this->_helper->redirector('index');
    }

    public function updatebasetupAction()
    {
        $this->_helper->redirector('index');
    }

    private function _getPopupSettings()
    {
        if ($this->_entity->getId()) {
            return [];
        } else {
            $entityId = $this->_form->entity_id->getValue();

            return [
                'entity' => [
                    'gridTitle' => 'Select Contractor',
                    'destFieldName' => 'entity_id',
                    'idField' => 'entity_id',
                    'collections' => [
                        (new Contractor())->getCollection()->addCarrierFilter(),
                    ],
                    //                        'showClearButton' => false,
                ],
            ];
        }
    }

    public function reissueAction()
    {
        $form = new Application_Form_Transactions_DisbursementReissue();

        $this->view->form = $form;
        $id = $this->_getParam('id', 0);
        $user = User::getCurrentUser();
        if (!$user->hasPermission(Permissions::DISBURSEMENT_REISSUE)) {
            $this->_helper->redirector('edit', 'transactions_disbursement', null, ['id' => $id]);
        }

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if ($form->isValid($post)) {
                $values = $form->getValues();
                if ($id) {
                    $this->_entity->load($id);
                    $data = [
                        'description' => $values['description'],
                        'disbursement_code' => $values['disbursement_code'],
                        'transaction_disbursement_date' => $values['disbursement_date'],
                        'reissue_parent_id' => $id,
                        'created_by' => $user->getId(),
                        'approved_by' => $user->getId(),
                        'created_datetime' => date("Y-m-d"),
                        'approved_datetime' => date("Y-m-d"),
                        'status' => PaymentStatus::APPROVED_STATUS,
                    ];
                    $this->_entity->unsId();
                    $this->_entity->addData($data);
                    $this->_entity->changeDateFormat(
                        [
                            'transaction_disbursement_date',
                        ]
                    );
                    $this->_entity->save();
                    $cycle = $this->_entity->getCycle();
                    /** @var Application_Model_Entity_Collection_Transactions_Disbursement $collection */
                    $collection = $this->_entity->getCollection()->addFilter('id', $this->_entity->getId());
                    $cycle->updateDisbursementReference($collection);

                    $this->_entity->updateParentDisbursement();
                    $this->_helper->redirector(
                        'edit',
                        'transactions_disbursement',
                        null,
                        ['id' => $this->_entity->getId()]
                    );
                }
            } else {
                $form->populate($post);
                $this->_entity->load($id);
                $form->reissue_parent_id->setValue($id);
            }
        } else {
            if ($id > 0) {
                $this->_entity->load($id);
                $form->reissue_parent_id->setValue($id);
                $allowReissue = $this->_entity->isAllowReissue();
                if ($allowReissue) {
                    $this->view->allowReissue = true;
                    $contractor = Contractor::staticLoad($this->_entity->getEntityId(), 'entity_id');
                    if (!$allowReissue) {
                        $this->_helper->redirector('edit', null, null, ['id' => $this->_entity->getId()]);
                    }
                }
            }
        }
        $form->configure();
    }
}

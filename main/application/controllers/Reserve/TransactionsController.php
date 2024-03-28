<?php

use Application_Model_Entity_Accounts_Reserve as ReserveAccount;
use Application_Model_Entity_Accounts_Reserve_Transaction as Transaction;
use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_Settlement_Cycle as Cycle;
use Application_Model_Entity_System_ReserveTransactionTypes as TransactionType;

class Reserve_TransactionsController extends Zend_Controller_Action
{
    use Application_Plugin_Messager;

    protected $headerMessageTemplate = "The '%s' transaction was not created for the following contractors because they are not related to the '%s' reserve account or carrier/vendor has not approved status:\n";
    protected $messageTemplate = " - %s<br>";
    private ?Transaction $_entity = null;
    private ?Application_Form_Account_Reserve_Transaction $_form = null;

    public function init()
    {
        $this->_entity = new Transaction();
        $this->_form = new Application_Form_Account_Reserve_Transaction();
    }

    public function newAction()
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_DATA_MANAGE) || User::getCurrentUser(
        )->isVendor()) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        if ($this->getRequest()->isXmlHttpRequest() || $this->_getParam('fromPopup', 'false') == 'true') {
            $reserveAccountIdArray = $this->getRequest()->getParam('selectedSetup');
            $contractorIdArray = $this->getRequest()->getParam('selectedContractors');
            $cycleId = $this->getrequest()->getParam('selectedCycle');
            $type = $this->getRequest()->getParam('type', 1);
            $reserveAccount = new ReserveAccount();

            foreach ($reserveAccountIdArray as $accountId) {
                $reserveAccount->load($accountId);
                foreach ($contractorIdArray as $contractorId) {
                    if (!$this->_entity->create($reserveAccount, $contractorId, $cycleId, $type, 0, true)) {
                        $this->addMessage(
                            [(new Contractor())->load($contractorId, 'entity_id')->getCompanyName()],
                            'account-' . $accountId
                        );
                    }
                }
                if ($this->hasMessages('account-' . $accountId)) {
                    $this->setHeaderMessage(
                        [$reserveAccount->getDescription(), $reserveAccount->getAccountName()],
                        'account-' . $accountId
                    );
                }
            }

            $this->_entity->reorderImportedPriority($cycleId);

            if ($this->hasMessages()) {
                $this->_helper->FlashMessenger(
                    [
                        'type' => 'T_CHECKBOX_POPUP_ERROR',
                        'title' => 'Not all of transactions have been created!',
                        'messages' => $this->getMessages(),
                        'headerMessages' => $this->getHeaderMessages(),
                    ]
                );
            }

            if ($this->getRequest()->isXmlHttpRequest()) {
                $this->_helper->json->sendJson(['entity' => $this->_entity::class]);
            } else {
                if ($back = $this->getRequest()->getParam('back')) {
                    $this->_redirect($back);
                }
                $this->_helper->redirector('index');
            }
        } else {
            $this->_forward('edit');
        }
    }

    public function indexAction()
    {
        $this->_forward('list');
    }

    public function listAction()
    {
        $user = User::getCurrentUser();
        if (!$user->hasPermission(Permissions::SETTLEMENT_DATA_VIEW) || !$user->hasPermission(
            Permissions::RESERVE_TRANSACTION_VENDOR_VIEW
        )) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $this->view->gridModel = new Application_Model_Grid_Transaction_Transaction();
        if ($user->hasPermission(Permissions::SETTLEMENT_DATA_MANAGE)) {
            $this->view->contributionGrid = new Application_Model_Grid_Transaction_Contribution();
            $this->view->withdrawalGrid = new Application_Model_Grid_Transaction_Withdrawal();
            $this->view->contractorGrid = new Application_Model_Grid_Transaction_Contractor();
            $this->view->contractorWithdrawalGrid = new Application_Model_Grid_Transaction_ContractorWithdrawal();
        }

        $cycleEntity = new Cycle();
        $this->view->gridModel->setCyclePeriods(
            $cycleEntity->getAllCyclePeriods()
        );
    }

    public function editAction()
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_DATA_VIEW)) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $this->view->title = 'Reserve Transaction Details';
        $this->view->form = $this->_form;
        $this->view->entity = $this->_entity;

        if ($id = $this->_getParam('id', 0)) {
            $this->_entity->load($id);
            if (!$this->_entity->checkPermissions()) {
                $this->_helper->redirector('index');
            }
            if (!$transactionCode = $this->_entity->getCode()) {
                $transactionCode = $this->_entity->getReserveAccountContractorEntity()->getVendorReserveCode();
            }
            $reserveAccountEntity = $this->_entity->getReserveAccountContractorEntity();
            if ($this->_entity->getType(
            ) == TransactionType::WITHDRAWAL && $transactionCode != 'CASH' && !$reserveAccountEntity->getAllowNegative(
            )) {
                $maxAmount = $this->_entity->getReserveAccountContractorBalance() + $this->_entity->getAmount();
                $this->_form->amount->addValidator(
                    'LEThan',
                    false,
                    [
                        'max' => $maxAmount,
                        'messages' => 'Max amount must be less or equal to the current balance of $' . number_format(
                            $maxAmount,
                            2
                        ) . '.',
                    ]
                );
            }
        }

        if ($this->getRequest()->isPost()) {
            if (!User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_DATA_MANAGE) || User::getCurrentUser(
            )->isVendor()) {
                $this->_helper->redirector('index', 'settlement_index');
            }
            $post = $this->getRequest()->getPost();
            if (!isset($post['reserve_account_contractor_title'])) {
                $this->_form->reserve_account_contractor_title->setRequired(false);
            }
            $this->_form->setupDeductionId(
                $this->_getParam('contractor_id', 0),
                $this->_entity->getData('settlement_cycle_id')
            );

            if ($this->_form->isValid($post)) {
                if ($this->_form->submit->getValue()) {
                    if ($this->_form->type->getValue() >= TransactionType::ADJUSTMENT_DECREASE) {
                        $this->_form->type->setValue(
                            $this->_form->adjustment_type->getValue()
                        );
                    }
                    if (!$this->_form->id->getValue()) {
                        $this->_entity->setData($this->_form->getValues());
                    } else {
                        $this->_entity->setType($this->_form->type->getValue());
                        $this->_entity->setDescription($this->_form->description->getValue());
                        $this->_entity->setReference($this->_form->reference->getValue());
                        $this->_entity->setAmount($this->_form->amount->getValue());
                        $deductionId = $this->_form->deduction_id->getValue();
                        if ($deductionId !== '0') {
                            $this->_entity->setDeductionId($deductionId);
                        } else {
                            $this->_entity->setDeductionId();
                        }
                    }
                    $this->_entity->save();
                    $this->_entity->reorderPriority();
                }

                if ($this->getRequest()->getParam('contractor')) {
                    $this->_helper->redirector(
                        'contractor',
                        'settlement_index',
                        $this->getRequest()->getModuleName(),
                        [
                            'id' => $this->getRequest()->getParam('contractor'),
                        ]
                    );
                } else {
                    if ($back = $this->getRequest()->getParam('back')) {
                        $this->_redirect($back);
                    }
                    $this->_helper->redirector('index', $this->_getParam('controller'));
                }
            } else {
                $this->_form->populate($post);
            }
        } else {
            if ($id > 0) {
                $data = $this->_entity->getDefaultData()->changeDateFormat([
                    'approved_datetime',
                    'created_datetime',
                ], true)->getData();
                $this->_form->setupDeductionId(
                    $this->_entity->getData('contractor_id'),
                    $this->_entity->getData('settlement_cycle_id')
                );
            } else {
                if ($raContractorId = $this->_getParam('account', 0)) {
                    $data = [
                        'reserve_account_contractor' => $raContractorId,
                    ];
                } elseif ($contractorId = $this->_getParam('contractor', 0)) {
                    $data = [
                        'contractor_id' => $contractorId,
                    ];
                } else {
                    $this->_helper->redirector('index');
                }
            }
            $this->_form->populate($data);
        }

        $this->_form->configure();
        $this->getPopupSetup();
    }

    public function multiactionAction()
    {
        $ids = explode(',', (string) $this->_getParam('ids'));
        foreach ($ids as $id) {
            $this->_entity->load((int)$id);
            if ($this->_entity->isAllowToDelete()) {
                $this->_entity->delete();
                $this->_entity->reorderPriority();
            }
        }
        if ($back = $this->getRequest()->getParam('back')) {
            $this->redirect($back);
        } else {
            $this->_helper->redirector('index');
        }
    }

    public function deleteAction()
    {
        $this->_entity->load((int)$this->getRequest()->getParam('id'));
        if ($this->_entity->isAllowToDelete()) {
            $this->_entity->delete();
            $this->_entity->reorderPriority();
        }
        if ($back = $this->getRequest()->getParam('back')) {
            $this->redirect($back);
        } else {
            $this->_helper->redirector('index');
        }
    }

    public function getPopupSetup()
    {
        if (!$contractorEntityId = $this->_getParam('contractor', 0)) {
            $this->view->popupSetup = [];
        } else {
            $this->view->popupSetup = [
                'reserve_account_contractor' => [
                    'gridTitle' => 'Select reserve contractor',
                    'destFieldName' => 'reserve_account_contractor',
                    'idField' => 'reserve_account_id',
                    'titleField' => 'account_name',
                    'collections' => [
                        (new Application_Model_Entity_Accounts_Reserve_Contractor())->getCollection(
                            // )->setOrder(
                            //     'priority',
                            //     Application_Model_Base_Collection::SORT_ORDER_ASC
                        )->addNonDeletedFilter()->addFilter('entity_id', $contractorEntityId),
                    ],
                    'callbacks' => [
                        'name' => 'Application_Model_Grid_Callback_Text',
                        'tax_id' => 'Application_Model_Grid_Callback_Text',
                        'contact' => 'Application_Model_Grid_Callback_Text',
                        'short_code' => 'Application_Model_Grid_Callback_Text',
                        // 'priority' => 'Application_Model_Grid_Callback_Priority',
                    ],
                    'showClearButton' => false,
                ],
            ];
        }

        return $this->view->popupSetup;
    }

    public function updaterasetupAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $raContractorId = $this->_getParam('raId');
            $reserveAccountEntity = new Application_Model_Entity_Accounts_Reserve_Contractor();

            return $this->_helper->json->sendJson(
                $reserveAccountEntity->load($raContractorId, 'reserve_account_id')->setDefaultValues()->getSetupData()
            );
        } else {
            $this->_helper->redirector('index');
        }
    }

    public function getMessageTemplate()
    {
        return $this->messageTemplate;
    }

    public function getHeaderMessageTemplate()
    {
        return $this->headerMessageTemplate;
    }
}

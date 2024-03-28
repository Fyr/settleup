<?php

use Application_Model_Entity_Accounts_Escrow as EscrowAccount;
use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Carrier as Carrier;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_Settlement_Group as Group;

class Carriers_IndexController extends Zend_Controller_Action
{
    use Application_Plugin_RedirectToIndex;

    /** @var Application_Model_Entity_Entity_Carrier */
    protected $_entity;
    protected $_form;
    protected $_contact;
    protected $_title = 'Division';

    public function init()
    {
        $this->_entity = new Carrier();
        $this->_form = new Application_Form_Entity_Carrier();
        $this->_contact = new Application_Form_Account_Contact();
    }

    public function indexAction()
    {
        $this->_forward('list');
    }

    public function listAction()
    {
        $this->handleErrorMessage();
        if (User::getCurrentUser()->isCarrier()) {
            $this->_helper->redirector('index', 'index');
        }
        $this->view->gridModel = new Application_Model_Grid_Entity_Carrier();
    }

    private function handleErrorMessage(): void
    {
        if ($groupId = $this->_getParam('group', false)) {
            $link = '<a href="/settlement_group/edit/id/' . $groupId . '">Settlement Group.</a>';
            $this->view->errorMessage = 'You cannot delete this Division because of its association with the ' . $link;
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function infoAction()
    {
        if (!User::getCurrentUser()->hasPermission(
            Permissions::CARRIER_VIEW
        )) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $carrierId = User::getCurrentUser()->getSelectedCarrier()->getId();
        $this->_forward('edit', null, null, ['id' => $carrierId, 'redirect' => $this->_getParam('redirect')]);
    }

    public function editAction()
    {
        if (!User::getCurrentUser()->hasPermission(
            Permissions::CARRIER_VIEW
        )) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $this->view->title = $this->_title;
        $this->view->form = $this->_form;

        $id = $this->_getParam('id', 0);

        if ($id > 0) {
            $this->_entity->load($id);
        }

        if ($this->getRequest()->isPost()) {
            if (!User::getCurrentUser()->hasPermission(
                Permissions::CARRIER_MANAGE
            )) {
                $this->_helper->redirector('index', 'settlement_index');
            }
            $post = $this->getRequest()->getPost();

            $this->_form->populate($post)->appendSubforms($post['contacts']);
            if ($this->_form->isValid($post)) {
                if ($this->_form->id->getValue()) {
                    $this->_entity->load(
                        $this->_form->id->getValue()
                    );
                    if (!$this->_entity->checkPermissions()) {
                        $this->_redirect('index');
                    }
                }

                $values = $this->_form->getValues();

                if (isset($values['create_contractor_type']) && !User::getCurrentUser()->isSuperAdmin()) {
                    unset($values['create_contractor_type']);
                }
                $this->_entity->addData($values)->save();
                $restService = new Application_Model_Rest();
                $restService->getCarrierKey($this->_entity->getEntityId());

                $this->_form->saveSubforms($this->_entity->getEntityId());

                if ($this->_entity->hasMessages()) {
                    $this->_entity->implodeMessages(
                        $namespace = 'default',
                        $glue = '',
                        $template = '<table><tr><th>ID</th><th>Company</th><th>First Name</th><th>Last Name</th><th>Email</th></tr>%s</table>'
                    );
                    $this->_helper->FlashMessenger(
                        [
                            'type' => 'T_CHECKBOX_POPUP_ERROR',
                            'title' => 'The following user accounts were not created because a user account with an identical email already exists:',
                            'messages' => $this->_entity->getMessages(),
                            'headerMessages' => [],
                        ]
                    );
                }
                if ($this->_entity->getStatus() != Application_Model_Entity_System_SystemValues::CONFIGURED_STATUS) {
                    if (!EscrowAccount::staticLoad($this->_entity->getId(), 'carrier_id')->getId()) {
                        $this->_helper->redirector(
                            'escrow',
                            'carriers_index',
                            $this->getRequest()->getModuleName(),
                            ['carrier' => $this->_entity->getId(), 'showMessage' => 'true']
                        );
                    }
                } else {
                    if ($this->_getParam('redirect')) {
                        $this->_redirect('/settlement_index');
                    } else {
                        $this->redirectToIndex();
                    }
                }
            } else {
                $this->_form->populate($post);
            }
        } else {
            if ($id > 0) {
                if (!$this->_entity->checkPermissions()) {
                    $this->_redirect('index');
                }
                $this->_form->populate($this->_entity->getData());
            }
            $this->_form->appendSubforms($this->_entity->getAllContacts());
        }
        $this->_form->configure();

        $this->view->cancelUrl = ($this->_getParam(
            'redirect'
        )) ? 'href="/settlement_index"' : 'href="' . $this->getPreviousUrl() . '"';

        $this->addErrorMessage();
    }

    public function escrowAction()
    {
        if ($this->_getParam('showMessage', 0)) {
            //            $this->view->showMessage = true;
            $this->_helper->FlashMessenger(
                [
                    'type' => 'T_ERROR',
                    'title' => 'Warning!',
                    'message' => 'You must setup escrow account.',
                ]
            );
        }
        $form = new Application_Form_Account_Escrow();
        $user = User::getCurrentUser();
        $this->view->form = $form;
        $accountId = $this->getRequest()->getParam('id');
        $carrierId = $this->getRequest()->getParam('carrier');
        $entityCarrier = Carrier::staticLoad($carrierId, 'entity_id');
        $form->setCarrier($entityCarrier);
        if ($user->isCarrier() || $user->isAdmin()) {
            if ($user->isAdmin()) {
                if ($carrierId) {
                    $escrowAccount = EscrowAccount::staticLoad($carrierId, 'carrier_id');
                } else {
                    $escrowAccount = EscrowAccount::staticLoad($accountId);
                    if ($escrowAccount->getCarrierId()) {
                        $entityCarrier = Carrier::staticLoad($escrowAccount->getCarrierId(), 'entity_id');
                        $form->setCarrier($entityCarrier);
                    }
                }
            } else {
                if (!$user->hasPermission(Permissions::SETTLEMENT_ESCROW_ACCOUNT_VIEW)) {
                    return $this->_helper->redirector('index', 'settlement_index');
                }
                $carrier = $user->getSelectedCarrier();
                $escrowAccount = $carrier->getEscrowAccount();
            }

            if ($this->getRequest()->isPost()) {
                $post = $this->getRequest()->getPost();
                if ($form->isValid($post)) {
                    $escrowAccount->setData($form->getValues());
                    if (!$escrowAccount->getCarrierId()) {
                        $escrowAccount->setCarrierId($user->getSelectedCarrier()->getEntityId());
                    }
                    $escrowAccount->save();
                    if (!$carrierId) {
                        $carrierId = $escrowAccount->getCarrierId();
                    }
                    if ($carrierId) {
                        //                        $carrier = $user->getSelectedCarrier();
                        $carrier = Carrier::staticLoad($carrierId, 'entity_id');
                        $carrier->setStatus(Application_Model_Entity_System_SystemValues::CONFIGURED_STATUS)->save();
                        if ($this->getParam('redirect')) {
                            $carrier = Carrier::staticLoad($carrierId, 'entity_id');
                            $this->_helper->redirector(
                                'edit',
                                'carriers_index',
                                $this->getRequest()->getModuleName(),
                                ['id' => $carrier->getId()]
                            );
                        } else {
                            if ($user->isAdmin()) {
                                $this->_helper->redirector(
                                    'index',
                                    'escrow'
                                );
                            } else {
                                $this->_redirect('/settlement_index');
                            }
                        }
                    } else {
                        $this->_redirect('/settlement_index');
                    }
                } else {
                    $form->setEncryptedFields();
                    $form->populate($post);
                    $form->configure();
                }
            } else {
                $data = $escrowAccount->getData();
                if ($carrierId) {
                    $data['carrier_id'] = $carrierId;
                }
                $form->populate($data);
                $form->configure();
            }
        } else {
            $this->_redirect('/reporting_index');
        }
    }

    public function deleteAction()
    {
        if (!User::getCurrentUser()->hasPermission(
            Permissions::CARRIER_MANAGE
        )) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $id = (int)$this->getRequest()->getParam('id');
        $this->_entity->load($id);
        $groups = (new Group())->getByDivisionId($id);
        if ($groups) {
            $this->_helper->redirector(
                'index',
                'carriers_index',
                $this->getRequest()->getModuleName(),
                ['group' => array_key_first($groups)]
            );
        }
        if ($this->_entity->checkPermissions()) {
            $this->_entity->setDeleted(Application_Model_Entity_System_SystemValues::DELETED_STATUS)->save();
        }
        $this->_helper->redirector('index');
    }

    public function multiactionAction()
    {
        if (!User::getCurrentUser()->hasPermission(
            Permissions::CARRIER_MANAGE
        )) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $ids = explode(',', (string) $this->_getParam('ids'));
        foreach ($ids as $id) {
            $this->_entity->load((int)$id);
            if ($this->_entity->checkPermissions()) {
                $this->_entity->setDeleted(Application_Model_Entity_System_SystemValues::DELETED_STATUS)->save();
            }
        }
        $this->_helper->redirector('index');
    }

    public function purgeAction()
    {
        if (!User::getCurrentUser()->isSuperAdmin()) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $id = (int)$this->getRequest()->getParam('id');
        if ($this->_entity->load($id)->getId()) {
            $this->_entity->purgeData();
        }
        $this->_helper->redirector('index');
    }

    protected function addErrorMessage()
    {
        if ($this->_entity->getStatus() != Application_Model_Entity_System_SystemValues::CONFIGURED_STATUS) {
            $messages = [];
            if (!$this->_entity->getEscrowAccount()->getId()) {
                $messages[] = 'An escrow account must be added to this entity to complete setup.';
            }

            $this->_helper->FlashMessenger(
                [
                    'type' => 'T_ERROR',
                    'title' => 'Warning!',
                    'message' => implode('<br/>', $messages),
                ]
            );
        }

        return $this;
    }
}

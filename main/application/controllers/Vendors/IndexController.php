<?php

use Application_Model_Entity_Entity_Vendor as Vendor;

class Vendors_IndexController extends Zend_Controller_Action
{
    use Application_Plugin_RedirectToIndex;

    /** @var Application_Model_Entity_Entity_Vendor */
    protected $_entity;
    protected $_form;
    protected $_contact;
    protected $_title = 'Vendors';

    public function init()
    {
        $this->_entity = new Vendor();
        $this->_form = new Application_Form_Entity_Vendor();
        $this->_contact = new Application_Form_Account_Contact();
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
        if (!Application_Model_Entity_Accounts_User::getCurrentUser()->hasPermission(
            Application_Model_Entity_Entity_Permissions::VENDOR_VIEW
        )) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $this->view->title = $this->_title;
        $this->view->form = $this->_form;

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();

            if ($this->_form->isValid($post)) {
                if (!Application_Model_Entity_Accounts_User::getCurrentUser()->hasPermission(
                    Application_Model_Entity_Entity_Permissions::VENDOR_MANAGE
                )) {
                    $this->_helper->redirector('index', 'settlement_index');
                }
                if ($this->_entity->getId()) {
                    $this->_entity->load($this->_form->id->getValue())->addData($this->_form->getValues())->save();
                } else {
                    $this->_entity->setData($this->_form->getValues())->setCarrierId(
                        Application_Model_Entity_Accounts_User::getCurrentUser()->getSelectedCarrier()->getEntityId()
                    );
                    $vendor = $this->_entity->save()->load($this->_entity->getId());
                    $userVisibility = new
                    Application_Model_Entity_Accounts_UsersVisibility();

                    $userVisibility->addEntities(
                        Application_Model_Entity_Accounts_User::getCurrentUser()->getEntity()->getCurrentCarrier(
                        )->getEntityId(),
                        [$vendor->getEntityId()]
                    );
                }

                $this->_helper->redirector(
                    'index',
                    $this->_getParam('controller')
                );
            } else {
                $this->_form->populate($post);
            }
        } else {
            $id = $this->_getParam('id', 0);
            if ($id > 0) {
                $entity = $this->_entity->load($id);
                if ($entity->getEntity()->getDeleted(
                ) == Application_Model_Entity_System_SystemValues::DELETED_STATUS || !$this->_entity->checkPermissions(
                )) {
                    $this->_helper->redirector('index');
                }
                $this->_form->populate($entity->getData());
            }
        }
        $this->_form->configure();
        $this->view->cancelUrl = 'href="' . $this->getPreviousUrl() . '"';
    }

    public function listAction()
    {
        if (!Application_Model_Entity_Accounts_User::getCurrentUser()->hasPermission(
            Application_Model_Entity_Entity_Permissions::VENDOR_VIEW
        )) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $this->view->gridModel = new Application_Model_Grid_Entity_Vendor();
        $this->view->hideContractorSelector = true;
    }

    public function deleteAction()
    {
        if (!Application_Model_Entity_Accounts_User::getCurrentUser()->hasPermission(
            Application_Model_Entity_Entity_Permissions::VENDOR_MANAGE
        )) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $id = (int)$this->getRequest()->getParam('id');
        $this->_entity->load($id);
        if (!$this->_entity->hasDeductions() && !$this->_entity->hasTransactions() && $this->_entity->checkPermissions(
        )) {
            $this->_entity->setDeleted(Application_Model_Entity_System_SystemValues::DELETED_STATUS)->save();
            $this->_entity->removeRecurringDeductions();
        }
        $this->_helper->redirector('index');
    }

    //    public function multiactionAction()
    //    {
    //        $ids = explode(',', $this->_getParam('ids'));
    //        foreach ($ids as $id) {
    //            $this->_entity->load((int)$id);
    //            $this->_entity->setDeleted(Application_Model_Entity_System_SystemValues::DELETED_STATUS)->save();
    //        }
    //        $this->_helper->redirector('index');
    //    }
}

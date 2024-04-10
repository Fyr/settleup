<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_System_SystemValues as SystemValues;

class Settlement_GroupController extends Zend_Controller_Action
{
    use Application_Plugin_RedirectToIndex;

    /**
     * @var Application_Model_Entity_Settlement_Group
     */
    protected $_entity;
    protected $_form;
    protected $_title = 'Settlement Group';

    public function init(): void
    {
        $this->_entity = new Application_Model_Entity_Settlement_Group();
        $this->_form = new Application_Form_Settlement_Group();
    }

    public function indexAction(): void
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_GROUP_VIEW)) {
            $this->_redirect('/');
        }
        $this->view->gridModel = new Application_Model_Grid_Settlement_Group();
    }

    public function listAction(): void
    {
        if (User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_GROUP_VIEW)) {
            $this->_forward('edit');
        } else {
            $this->_helper->redirector('index', 'settlement_index');
        }
    }

    public function newAction(): void
    {
        if (User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_GROUP_MANAGE)) {
            $this->_forward('edit');
        } else {
            $this->_helper->redirector('index', 'settlement_index');
        }
    }

    public function editAction(): void
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_GROUP_VIEW)) {
            $this->_helper->redirector('index', 'settlement_index');

            return;
        }
        $this->view->title = $this->_title;
        $this->view->form = $this->_form;
        $id = $this->getRequest()->getParam('id');
        if ($this->getRequest()->isPost()) {
            if (!User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_GROUP_MANAGE)) {
                $this->_helper->redirector('index', 'settlement_index');
            }
            $post = $this->getRequest()->getPost();
            if ($this->_form->isValid($post)) {
                $this->_entity->setData($this->_form->getValues());
                $this->_entity->save();

                $this->_helper->redirector('index');
            } else {
                $this->_form->populate($post);
            }
        } else {
            if ($id) {
                $this->_entity->load($id);
            }

            $this->_form->populate($this->_entity->getData());
        }
        $this->_form->configure();
    }

    public function deleteAction(): void
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_GROUP_MANAGE)) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $this->_entity->load((int)$this->getRequest()->getParam('id'));
        $this->_entity->setDeleted(SystemValues::DELETED_STATUS)->save();

        $this->_helper->redirector('index');
    }
}

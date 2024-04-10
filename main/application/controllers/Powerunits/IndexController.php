<?php

use Application_Form_Powerunits_Powerunit as PowerunitForm;
use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Powerunit_Powerunit as Powerunit;
use Application_Model_Grid_Powerunit_Powerunit as PowerunitGrid;

class Powerunits_IndexController extends Zend_Controller_Action
{
    /** @var Powerunit */
    protected $_entity;
    /** @var PowerunitForm */
    protected $_form;
    /** @var Contractor */
    protected $_contractor;
    protected $_title = 'Power Units';

    public function init()
    {
        $this->_entity = new Powerunit();
        $this->_form = new PowerunitForm();
        $this->_contractor = new Contractor();
    }

    public function indexAction()
    {
        $this->forward('list');
        $this->view->headTitle()->append($this->_title);
    }

    public function newAction()
    {
        $this->forward('edit');
    }

    public function editAction()
    {
        $user = User::getCurrentUser();
        $id = $this->_getParam('id', 0);
        if ($id) {
            $this->_entity->load($id);
        }

        $this->view->title = $this->_title;
        $this->view->form = $this->_form;
        $this->view->headTitle()->append($this->_title);
        $isEdit = $this->isEdit();
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();

            if ($this->_form->isValid($post)) {
                $this->_entity->setData($this->_form->getValues());
                $this->_entity->setDatetimesToDbFormat($isEdit);
                $carrierId = $user->getEntity()->getEntityId();
                $this->_entity->setCarrierId($carrierId);
                $this->_entity->save();
                $this->_entity->createIndividualTemplates();

                return $this->_helper->redirector('index');
            } else {
                $this->_form->setEncryptedFields();
                $this->_form->populate($post);
            }
        } elseif ($isEdit) {
            $this->view->powerunit = $this->_entity;
            $this->view->isEdit = true;
            $this->_entity->setDatetimesFromDbFormat();
            $this->_form->populate($this->_entity->getData());
        }
    }

    public function listAction()
    {
        $this->view->gridModel = new PowerunitGrid();
    }

    public function changestatusAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $status = constant(
            'Application_Model_Entity_System_PowerunitStatus::' . $this->getRequest()->getParam('status')
        );
        $entity = new Powerunit();
        $entity->load($id);
        $entity->setStatus($status);
        $entity->save();
        $this->_helper->redirector('index');
    }

    public function deleteAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $this->_entity->load($id);
        $this->_entity->setDeleted(Application_Model_Entity_System_SystemValues::DELETED_STATUS)->save();
        $this->_helper->redirector('index');
    }

    public function isEdit()
    {
        $id = $this->_getParam('id', 0);
        if ($id) {
            return true;
        }

        return false;
    }
}

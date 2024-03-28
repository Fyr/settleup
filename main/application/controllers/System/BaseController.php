<?php

class System_BaseController extends Zend_Controller_Action
{
    protected $_entity;
    protected $_form;
    protected $_title;

    public function init()
    {
        $this->_form = new Application_Form_System_Base();
    }

    public function indexAction()
    {
        $this->_forward('list');
    }

    public function listAction()
    {
        $this->view->entity = $this->_entity;
        $this->view->title = $this->_title;
        $this->render('manage', null, true);
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->view->title = $this->_title;
        $this->view->form = $this->_form;
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if ($this->_form->isValid($post)) {
                $this->_entity->setData($this->_form->getValues());
                $this->_entity->save();
                $this->_helper->redirector(
                    'index',
                    $this->_getParam('controller')
                );
            } else {
                $this->_form->populate($post);
            }
        } else {
            $id = $this->_getParam('id', 0);
            $this->view->form = $this->_form;
            if ($id > 0) {
                $this->_form->populate($this->_entity->load($id)->getData());
            }
        }
        $this->render('system/base/edit', null, true);
    }

    public function deleteAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $this->_entity->load($id);
        $this->_entity->delete();
        $this->_helper->redirector('index', $this->_getParam('controller'));
    }

    public function multiactionAction()
    {
        $ids = explode(',', (string) $this->_getParam('ids'));
        foreach ($ids as $id) {
            $this->_entity->load((int)$id);
            $this->_entity->delete();
        }
        $this->_helper->redirector('index');
    }
}

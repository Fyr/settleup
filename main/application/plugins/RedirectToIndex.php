<?php

trait Application_Plugin_RedirectToIndex
{
    public function redirectToIndex()
    {
        if ($url = $this->getPreviousUrl()) {
            $this->_redirect($url);
        } else {
            $this->_helper->redirector('index', $this->_getParam('controller'));
        }
    }

    public function getPreviousUrl()
    {
        $storage = Zend_Auth::getInstance()->getStorage()->read();

        return $storage->getPreviousUri();
    }
}

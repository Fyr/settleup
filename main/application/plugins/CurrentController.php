<?php

use Application_Model_Entity_Accounts_User as User;

class Application_Plugin_CurrentController extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (PHP_SAPI != 'cli') {
            $storage = Zend_Auth::getInstance()->getStorage()->read();
            $excludeControllers = ['grid' => '*', 'index' => '*', 'auth' => '*', 'file_index' => ['export']];
            $controllerName = $request->getControllerName();
            if (is_null($storage)) {
                return;
            }
            if (

                !isset($excludeControllers[$controllerName]) || ($excludeControllers[$controllerName] != '*' && !is_array(
                    $excludeControllers[$controllerName]
                )) || (is_array($excludeControllers[$controllerName]) && array_search(
                    $request->getActionName(),
                    $excludeControllers[$controllerName]
                ) == -1)) {
                $storage->currentControllerName = $request->getControllerName();
                $storage->isNotGridRequest = true;
                $storage->isLimitAction = false;
            } else {
                if ($request->getControllerName() == 'grid') {
                    if ($request->getActionName() == 'limitgridrow') {
                        $storage->isLimitAction = true;
                    } else {
                        $storage->isLimitAction = false;
                    }
                    $storage->isNotGridRequest = false;
                }
            }
            if ($storage && $storage instanceof User) {
                $currentUri = $request->getRequestUri();
                if ($currentUri != $storage->getPreviousUri() && !in_array(
                    $request->getActionName(),
                    ['edit', 'view', 'new', 'info', 'permissions', 'escrow']
                )) {
                    $storage->setPreviousUri($currentUri);
                }
            }
        }
    }
}

<?php

class ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        //$flashMessenger = Application_Model_FlashMessenger::getInstance();
        $errors = $this->_getParam('error_handler');

        if (!$errors || !$errors instanceof ArrayObject) {
            $this->_helper->FlashMessenger([
                'type' => 'T_ERROR',
                'title' => 'Error',
                'message' => 'You have reached the error page',
            ]);

            return;
        }

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $messageText = 'Page not found';

                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $messageText = 'Application error';
                break;
        }

        // Log exception, if logger available
        if ($log = $this->_getLog()) {
            $log->log($errors->exception, $priority);
            $log->log(
                'Request Parameters: ' . http_build_query(
                    $errors->request->getParams()
                ),
                $priority
            );
        }

        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            if (APPLICATION_ENV == 'development' || APPLICATION_ENV == 'testing') {
                $this->_helper->FlashMessenger(
                    [
                        'type' => 'T_EXCEPTION',
                        'title' => $messageText,
                        'message' => $errors->exception,
                        'request' => $errors->request,
                    ]
                );
            } else {
                $this->_helper->FlashMessenger(
                    [
                        'type' => 'T_ERROR',
                        'title' => $messageText,
                        'message' => $errors->exception->getMessage(),
                    ]
                );
            }
        } else {
            $this->_helper->FlashMessenger(
                [
                    'type' => 'T_ERROR',
                    'title' => 'An error occurred',
                    'message' => $messageText,
                ]
            );
        }
    }

    protected function _getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');

        return $log;
    }

    public function accessdaniedAction()
    {
    }
}

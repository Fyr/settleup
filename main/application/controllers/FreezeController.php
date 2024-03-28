<?php

class FreezeController extends Zend_Controller_Action
{
    final public const FREEZE_STATE_FOLDER = '/../scripts/db/freezeState/';
    final public const FREEZE_STATE_SCRIPT = '/../scripts/db/freezeState.sh';

    public function indexAction()
    {
        $states = [];
        foreach (glob(APPLICATION_PATH . self::FREEZE_STATE_FOLDER . '*.gz') as $state) {
            $states[] = basename($state);
        }
        $this->view->states = array_reverse($states);
    }

    /**
     * save current state of the database
     */
    public function saveAction()
    {
        $action = 'save';

        $this->_helper->layout->disableLayout();
        $stateScript = APPLICATION_PATH . self::FREEZE_STATE_SCRIPT;
        $archiveDesc = 'state' . date('mdy_his') . '_';

        if (!file_exists($stateScript) || !is_executable($stateScript)) {
            $this->view->message = "Please, make sure the file(freezeState.sh) exists and it's executable";
        }

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();

            // convert description to lowercase and replace spaces with dashes
            if (!empty($post['desc'])) {
                $desc = strtolower(trim((string) $post['desc']));
                $desc = preg_replace('#\s#', '_', $desc);
                $archiveDesc .= $desc;
            }
            exec(
                "{$stateScript} {$action} " . APPLICATION_PATH . self::FREEZE_STATE_FOLDER . "{$archiveDesc} 2>&1",
                $result
            );

            if (!count($result) || (isset($result[0]) && str_contains(strtolower($result[0]), 'warning'))) {
                $this->view->message = 'State ' . $archiveDesc . ' has been saved successfully';
                $this->view->title = 'Success';
                $this->view->alertClass = 'alert-success';
            } else {
                $this->view->title = 'Error';
                $this->view->alertClass = 'alert-error';
                $this->view->message = $result[0];
            }
        }
    }

    /**
     * restore current state of the database
     */
    public function restoreAction()
    {
        $action = 'restore';
        $this->_helper->layout->disableLayout();
        $stateScript = APPLICATION_PATH . self::FREEZE_STATE_SCRIPT;

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();

            if (!empty($post['state'])) {
                $stateArchive = trim((string) $post['state']);

                if (file_exists(APPLICATION_PATH . self::FREEZE_STATE_FOLDER . $stateArchive)) {
                    exec(
                        "{$stateScript} {$action} " . APPLICATION_PATH . self::FREEZE_STATE_FOLDER . "{$stateArchive} 2>&1",
                        $result
                    );

                    if (!count($result) || (isset($result[0]) && str_contains(strtolower($result[0]), 'warning'))) {
                        Application_Model_Entity_Accounts_User::getCurrentUser()->resetCarrier();
                        Application_Model_Entity_Accounts_User::getCurrentUser()->reloadCycle();
                        $this->view->message = 'Selected state has been restored successfully!';
                        $this->view->title = 'Success';
                        $this->view->alertClass = 'alert-success';
                    } else {
                        $this->view->title = 'Error';
                        $this->view->alertClass = 'alert-error';
                        $this->view->message = $result[0];
                    }
                }
            }
        }
    }
}

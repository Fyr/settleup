<?php

use Application_Model_Entity_Settlement_Group as SettlementGroup;

class IndexController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        //        $rest = new Application_Model_Rest();
        //        $credentials = $rest->getCredentials();
        //        $rest->createUser([
        //            'id' => '2',
        //            'password' => md5('pass'),
        //            'role_id' => 1,
        //            'carrier_id' => null
        //        ]);

        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        if ($user->getId()) {
            if ($user->isSpecialist()) {
                $this->redirect('reporting_index');
            } elseif ($user->isOnboarding()) {
                if ($user->hasPermission(
                    Application_Model_Entity_Entity_Permissions::REPORTING_GENERAL
                ) || $user->hasPermission(
                    Application_Model_Entity_Entity_Permissions::REPORTING_DEDUCTION_REMITTANCE_FILE
                )) {
                    $this->redirect('reporting_index');
                } else {
                    $this->redirect('deductions_deductions');
                }
            } elseif ($user->isGuest()) {
                $this->redirect('guest');
            } else {
                if ($user->hasPermission(Application_Model_Entity_Entity_Permissions::SETTLEMENT_DATA_VIEW)) {
                    $this->_redirect('settlement_index');
                } else {
                    $this->_redirect('reserve_accountpowerunit');
                }
            }
        } else {
            $this->_redirect('auth/login');
        }
    }

    //Searchautocomplete section ====
    //this action renders input and button
    public function searchautocompleteAction()
    {
    }

    //this action return list of hints
    public function searchhintstubAction()
    {
        $this->_helper->layout->disableLayout();
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest()) {
            $searchStub = new Application_Model_SearchStub();
            $query = $req->getParam('query');
            $this->view->hints = $searchStub->getHints($query);
        }
    }

    //this action return search result
    public function searchresultstubAction()
    {
        $this->_helper->layout->disableLayout();
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest()) {
            $data = $req->getParam('data');
            $this->view->result = "Result for:$data";
        }
    }

    public function changecurrentcarrierAction()
    {
        $currentCarrierId = $this->getRequest()->getParam('selectedCarrierId');
        $entityId = $this->getRequest()->getParam('entityId');
        $currentController = $this->getRequest()->getParam('currentController');
        $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();
        if ($currentCarrierId && $entityId) {
            $userEntity
                ->setEntityId($entityId)
                ->setLastSelectedCarrier($currentCarrierId)
                ->setLastSelectedSettlementGroup()
                ->save();
        }
        $userEntity->resetCarrier()->resetSettlementGroup()->reloadCycle();

        $this->_helper->redirector(null, $currentController);
    }

    public function changecurrentsettlementgroupAction()
    {
        $currentSettlementGroupId = $this->getRequest()->getParam('selectedSettlementGroupId');
        $currentController = $this->getRequest()->getParam('currentController');
        $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();

        if (!$currentSettlementGroupId) {
            $userEntity->setLastSelectedSettlementGroup($currentSettlementGroupId)->save();
            Application_Model_Entity_Accounts_User::getCurrentUser()->resetSettlementGroup()->reloadCycle();
            $this->_helper->redirector(null, 'settlement_group');

            return null;
        }

        $settlementGroupModel = new SettlementGroup();
        $settlementGroup = $settlementGroupModel->getByDivisionIdAndSettlementGroupId(
            $userEntity->getSelectedCarrier()->getId(),
            $currentSettlementGroupId
        );

        if (!$settlementGroup->isEmpty()) {
            $userEntity->setLastSelectedSettlementGroup($currentSettlementGroupId)->save();
            Application_Model_Entity_Accounts_User::getCurrentUser()->resetSettlementGroup()->reloadCycle();
        } else {
            $this->view->errorMessage = "Settlement group with ID {$currentSettlementGroupId} does not exist.";
        }
        $this->_helper->redirector(null, $currentController);
    }

    public function changecurrentcontractorAction()
    {
        $currentContractorId = $this->getRequest()->getParam('selectedContractorId');
        $currentController = $this->getRequest()->getParam('currentController');
        $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();

        if ($currentEntityId = $this->_getParam('selected-entity-id', 0)) {
            if ($currentEntityId == 'none') {
                $currentContractorId = $currentEntityId;
            } else {
                $currentContractorId = (new Application_Model_Entity_Entity_Contractor())->load(
                    $currentEntityId,
                    'entity_id'
                )->getId();
            }
        }

        if ($currentContractorId == 'none') {
            $userEntity->setLastSelectedContractor()->save();
        } else {
            $userEntity->setLastSelectedContractor($currentContractorId)->save();
        }
        if ($this->_getParam('no-redirect', false)) {
            $this->_helper->json->sendJson(['status' => 'ok']);
        } else {
            if ($currentController == 'reserve_accountpowerunit') {
                $contractorEntity = new Application_Model_Entity_Entity_Contractor();
                $contractorEntity->load($currentContractorId);
                $this->_helper->redirector(null, $currentController, 'default', [
                    'entity' => $contractorEntity->getEntityId(),
                ]);
            } elseif ($currentController == 'settlement_index') {
                $contractorEntity = new Application_Model_Entity_Entity_Contractor();
                $contractorEntity->load($currentContractorId);
                $this->_helper->redirector('contractor', $currentController, 'default', [
                    'id' => $contractorEntity->getEntityId(),
                ]);
            } else {
                $this->_helper->redirector(null, $currentController);
            }
        }
    }
}

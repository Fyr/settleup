<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Carrier as Carrier;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Settlement_Group as SettlementGroup;

class Application_Views_Helpers_Selector extends Zend_View_Helper_Abstract
{
    final public const CARRIER_TYPE = 1;
    final public const CONTRACTOR_TYPE = 2;
    final public const SETTLEMENT_GROUP_TYPE = 3;
    private Carrier|Contractor|SettlementGroup|null $_entity = null;
    private $_items;
    private ?array $_columns = null;
    private ?string $_idField = null;

    public function Selector($type = self::CARRIER_TYPE)
    {
        $gridTitle = '';
        $necessity = true;
        $currentUser = User::getCurrentUser();
        switch ($type) {
            case self::CARRIER_TYPE:
                $gridTitle = 'Select account';
                $this->_entity = new Carrier();
                if ($currentUser->isAdmin()) {
                    $currentEntity = $currentUser->getSelectedCarrier();
                    $partialName = 'select_carrier_grid.phtml';
                    $this->_items = $this->_entity->getCollection()->getItems();
                } elseif ($currentUser->isContractor()) {
                    $collection = $currentUser->getAssociatedEntityCollection();
                    if ($collection->count() > 1) {
                        $currentEntity = $currentUser->resetCarrier()->getSelectedCarrier();
                        $partialName = 'select_carrier_grid.phtml';
                        $this->_idField = 'carrier_entity_id';
                        $this->_items = $collection->getItems();
                        $this->_columns = [
                            'carrier_name' => 'Division',
                            'code' => 'ID',
                            'company_name' => 'Company',
                            'first_name' => 'First Name',
                            'last_name' => 'Last Name',
                        ];
                    } else {
                        $necessity = false;
                    }
                } elseif ($currentUser->isVendor()) {
                    $collection = $currentUser->getAssociatedEntityCollection();
                    if ($collection->count() > 1) {
                        $currentEntity = $currentUser->resetCarrier()->getSelectedCarrier();
                        $partialName = 'select_carrier_grid.phtml';
                        $this->_idField = 'carrier_entity_id';
                        $this->_items = $collection->getItems();
                        $this->_columns = [
                            'carrier_name' => 'Division',
                            'code' => 'ID',
                            'name' => 'Vendor',
                            'tax_id' => 'Federal Tax ID',
                        ];
                    } else {
                        $necessity = false;
                    }
                } else {
                    $necessity = false;
                }
                break;
            case self::CONTRACTOR_TYPE:
                if ($currentUser->isContractor() || (!$currentUser->getSelectedCarrier()->getId(
                ) && $currentUser->isAdmin())) {
                    return;
                }
                $gridTitle = 'Select contractor as current:';
                $partialName = 'select_contractor_grid.phtml';
                $this->_entity = new Contractor();

                $currentEntity = $this->_getCurrentContractor();
                $this->_items = $this->_entity->getCollection()->addFilterByCarrierContractor(
                )->addFilterByVendorVisibility(false)->getItems();
                break;
            case self::SETTLEMENT_GROUP_TYPE:
                if ($currentUser->isGuest()) {
                    return;
                }
                $gridTitle = 'Select Settlement Group';
                $this->_entity = new SettlementGroup();
                $currentCarrier = $currentUser->getSelectedCarrier();
                $currentEntity = $this->getCurrentSettlementGroup();
                $partialName = 'select_settlement_group_grid.phtml';
                $this->_items = $this->_entity->getByDivisionId($currentCarrier->getId());
                break;
        }

        if ($necessity) {
            return $this->view->partial(
                $partialName,
                [
                    'gridTitle' => $gridTitle,
                    'idField' => $this->_idField ?: 'id',
                    'grid' => $this->_getGridData(),
                    'currentEntityName' => $currentEntity,
                ]
            );
        }
    }

    private function _getGridData()
    {
        //$this->_entity = new Application_Model_Entity_Entity_Carrier();
        return [
            'items' => $this->_items,
            'columns' => $this->_columns ?: $this->_entity->getResource()->getInfoFields(),
            'titleField' => $this->_entity->getTitleColumn(),
            'controller' => Zend_Controller_Front::getInstance()->getRequest()->getParam('controller'),
        ];
    }

    private function _getCurrentContractor()
    {
        if (!$contractorEntity = User::getSelectedContractor()) {
            return 'None';
        }

        return $contractorEntity->getData($contractorEntity->getTitleColumn());
    }

    private function getCurrentSettlementGroup()
    {
        return User::getCurrentUser()->getSelectedSettlementGroup();
    }
}

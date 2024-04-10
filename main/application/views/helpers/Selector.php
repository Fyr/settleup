<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Carrier as Division;
use Application_Model_Entity_Settlement_Group as SettlementGroup;

class Application_Views_Helpers_Selector extends Zend_View_Helper_Abstract
{
    final public const DIVISION_TYPE = 1;
    final public const SETTLEMENT_GROUP_TYPE = 2;
    private Division|SettlementGroup $_entity;
    private ?array $_items = null;
    private ?array $_columns = null;
    private ?string $_idField = null;

    public function Selector($type = self::DIVISION_TYPE)
    {
        $gridTitle = '';
        $partialName = '';
        $currentEntity = '';
        $currentUser = User::getCurrentUser();
        switch ($type) {
            case self::DIVISION_TYPE:
                $gridTitle = 'Select Division';
                $this->_entity = new Division();
                $currentEntity = $currentUser->getSelectedCarrier();
                $partialName = 'select_carrier_grid.phtml';
                $this->_items = $this->_entity
                    ->getCollection()
                    ->addNonDeletedFilter()
                    ->addVisibilityFilterForUser($currentUser->isAdminOrSuperAdmin())
                    ->getItems();
                break;
            case self::SETTLEMENT_GROUP_TYPE:
                $gridTitle = 'Select Settlement Group';
                $this->_entity = new SettlementGroup();
                $currentCarrier = $currentUser->getSelectedCarrier();
                $currentEntity = $this->getCurrentSettlementGroup();
                $partialName = 'select_settlement_group_grid.phtml';
                $this->_items = $this->_entity->getByDivisionId($currentCarrier->getId());
                break;
        }

        return $this->view->partial(
            $partialName,
            [
                'gridTitle' => $gridTitle,
                'idField' => $this->_idField ?: 'id',
                'grid' => $this->getGridData(),
                'currentEntityName' => $currentEntity,
            ]
        );
    }

    private function getGridData(): array
    {
        return [
            'items' => $this->_items,
            'columns' => $this->_columns ?: $this->_entity->getResource()->getInfoFields(),
            'titleField' => $this->_entity->getTitleColumn(),
            'controller' => Zend_Controller_Front::getInstance()->getRequest()->getParam('controller'),
        ];
    }

    private function getCurrentSettlementGroup(): ?SettlementGroup
    {
        return User::getCurrentUser()->getSelectedSettlementGroup();
    }
}

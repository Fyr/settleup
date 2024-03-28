<?php

class Application_Views_Helpers_PopupCheckbox extends Zend_View_Helper_Abstract
{
    public function popupCheckbox($grid)
    {
        $entityName = str_replace(
            '_Collection',
            '',
            (string) $grid['collection']::class
        );
        $grid['entity'] = new $entityName();

        return $this->view->partial(
            'popup_checkbox.phtml',
            [
                'gridTitle' => $grid['gridTitle'],
                'data' => $this->_getCurrentEntityData($grid),
                'columns' => $grid['entity']->getResource()->getInfoFields(),
                'idField' => $grid['idField'],
            ]
        );
    }

    private function _getCurrentEntityData($grid)
    {
        $data = $grid['collection']->getItems();
        if ($grid['entity'] instanceof Application_Model_Entity_Entity_Contractor) {
            $field = 'getContractorId';
        } else {
            $field = 'getVendorId';
        }
        foreach ($data as $index => $entity) {
            foreach ($grid['addedItems'] as $addedItem) {
                if ($entity->getEntityId() == $addedItem->$field()) {
                    unset($data[$index]);
                }
            }
        }

        return $data;
    }
}

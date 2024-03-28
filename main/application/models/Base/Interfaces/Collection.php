<?php

interface Application_Model_Base_Interfaces_Collection
{
    public function addFilter($field, $value, $op = '=');

    public function getItems();

    public function addItem(Application_Model_Base_Entity $item, $itemsKey);

    public function setOrder(
        $field,
        $direction = Application_Model_Base_Collection::SORT_ORDER_DESC
    );
}

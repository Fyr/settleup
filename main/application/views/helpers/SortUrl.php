<?php

class Application_Views_Helpers_SortUrl extends Zend_View_Helper_Abstract
{
    public function sortUrl($column)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $storage = Zend_Auth::getInstance()->getStorage()->read();
        $gridData = $storage->gridData;

        $query = Zend_Controller_Front::getInstance()->getRequest()->getQuery();
        if (!(is_countable($query) ? count($query) : 0)) {
            $query = [];
        }

        $currentSort = $gridData['settlement']['sort'];
        $currentOrder = $gridData['settlement']['order'];
        $order = '';
        if ($column == $currentSort) {
            if ($currentOrder == 'desc') {
                $order = 'desc';
                $orderString = 'asc';
            } else {
                $order = 'asc';
                $orderString = 'desc';
            }
        } else {
            $orderString = 'asc';
        }
        $query['sort'] = $column;
        $query['order'] = $orderString;

        $queryString = '?';
        foreach ($query as $key => $value) {
            $queryString .= $key . '=' . $value . '&';
        }
        $queryString = substr($queryString, 0, -1);

        $targetUrl = $this->view->url([
                'controller' => $request->getControllerName(),
                'action' => $request->getActionName(),
            ]) . $queryString;

        $data = '<a href="' . $targetUrl . '"><span class="sorting ' . $order . '"></span></a>';

        return $data;
    }
}

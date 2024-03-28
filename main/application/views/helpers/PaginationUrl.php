<?php

class Application_Views_Helpers_PaginationUrl extends Zend_View_Helper_Abstract
{
    public function paginationUrl($pageNumber)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $query = Zend_Controller_Front::getInstance()->getRequest()->getQuery();
        if (!(is_countable($query) ? count($query) : 0)) {
            $query = [];
        }
        $query['page'] = $pageNumber;

        $queryString = '?';
        foreach ($query as $key => $value) {
            $queryString .= $key . '=' . $value . '&';
        }
        $queryString = substr($queryString, 0, -1);

        $targetUrl = $this->view->url([
                'controller' => $request->getControllerName(),
                'action' => $request->getActionName(),
            ]) . $queryString;

        return $targetUrl;
    }
}

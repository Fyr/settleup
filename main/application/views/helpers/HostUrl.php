<?php

class Application_Views_Helpers_HostUrl extends Zend_View_Helper_Abstract
{
    /**
     * return domain name from config file
     *
     * @return string
     */
    public function hostUrl()
    {
        $config = Zend_Registry::get('options');
        $url = $config['domain'];

        return (string)$url;
    }
}

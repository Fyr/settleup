<?php

class Application_Views_Helpers_Manifest extends Zend_View_Helper_Abstract
{
    /**
     * @param $name
     * @return mixed
     */
    public function manifest($name)
    {
        $data = json_decode(file_get_contents(APPLICATION_PATH . '/../public/dist/manifest.json'), true, 512, JSON_THROW_ON_ERROR);

        return '/dist/' . ($data[$name] ?? $name);
    }
}

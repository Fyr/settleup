<?php

class Application_Views_Helpers_PopupUserEntity extends Zend_View_Helper_Abstract
{
    protected $settings;

    public function popupUserEntity($settings)
    {
        $this->settings = $settings;

        return $this->view->partial(
            'popupgrid/popup_entity_multiselect_filterable.phtml',
            [
                'destFieldName' => $settings['destFieldName'],
                'gridTitle' => $settings['gridTitle'],
                'idField' => $settings['idField'] ?? null,
                'titleField' => $settings['titleField'] ?? null,
                'grids' => $settings['collections'],
                'multigrid' => true,
                'callbacks' => ($settings['callbacks'] ?? []),
            ]
        );
    }
}

<?php

class Application_Views_Helpers_PopupGrid extends Zend_View_Helper_Abstract
{
    protected $settings;

    public function popupGrid($settings)
    {
        $this->settings = $settings;
        $clearButton = new Zend_View();
        $clearButton->setScriptPath($this->view->getScriptPaths());
        $clearButton->assign([
            'destFieldName' => $settings['destFieldName'],
            'titleColumn' => 'title',
        ]);

        if (isset($settings['multiselect'])) {
            if (isset($settings['filterable'])) {
                $template = 'popupgrid/popup_multiselect_filterable.phtml';
            } else {
                $template = 'popupgrid/popup_multiselect.phtml';
            }
        } else {
            if (isset($settings['filterable'])) {
                $template = 'popupgrid/popup_filterable.phtml';
            } else {
                $template = 'popupgrid/popup_grid.phtml';
                ;
            }
        }

        return $this->view->partial(
            $template,
            [
                'destFieldName' => $settings['destFieldName'],
                'gridTitle' => $settings['gridTitle'],
                'idField' => $settings['idField'] ?? null,
                'titleField' => $settings['titleField'] ?? null,
                'grids' => $this->_getGrids($settings['collections']),
                'multigrid' => $this->_isMultigrid($settings['collections']),
                'clearButton' => (isset($settings['showClearButton']) && $settings['showClearButton'] == false) ? '' : $clearButton->render(
                    'popupgrid/clear_button.phtml'
                ),
                'callbacks' => ($settings['callbacks'] ?? []),
            ]
        );
    }

    /**
     * @param Application_Models_Entity_Entity_ $collections
     * @return array
     */
    private function _getGrids($collections)
    {
        if (isset($this->settings['filterable'])) {
            return $collections;
        }
        $grids = [];
        foreach ($collections as $tabTitle => $collection) {
            $entityName = str_replace(
                '_Collection',
                '',
                (string) $collection::class
            );
            $entity = new $entityName();
            $columns = $entity->getResource()->getInfoFields();
            if ($collection instanceof Application_Model_Entity_Collection_Settlement_Cycle) {
                unset($columns['id']);
            }
            array_push(
                $grids,
                [
                    'items' => $collection->getItems(),
                    'columns' => $columns,
                    'titleField' => $entity->getTitleColumn(),
                    'tabTitle' => $tabTitle,
                ]
            );
        }

        return $grids;
    }

    private function _isMultigrid($entities)
    {
        return (is_countable($entities) ? count($entities) : 0) > 1;
    }
}

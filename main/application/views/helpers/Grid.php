<?php

class Application_Views_Helpers_Grid extends Zend_View_Helper_Abstract implements Stringable
{
    public function grid()
    {
        return $this;
    }

    public function renderLimit($gridModel)
    {
        $gridModel->setSubject('view partials');

        return $this->view->partial(
            'grid/partials/limit.phtml',
            ['gridModel' => $gridModel]
        );
    }

    public function renderFilter($gridModel)
    {
        return $this->view->partial(
            'grid/partials/filter.phtml',
            ['gridModel' => $gridModel]
        );
    }

    public function renderButtons($buttons)
    {
        return $this->view->partial(
            'grid/partials/buttons.phtml',
            ["buttons" => $buttons]
        );
    }

    public function renderHeaderButtons($callback, $grid)
    {
        $callback = new $callback();

        return $this->view->partial(
            'grid/partials/buttons.phtml',
            ["buttons" => $callback->getData($grid, $this->view)]
        );
    }

    public function renderBody($gridModel)
    {
        return $this->view->partial(
            'grid/partials/tbody.phtml',
            ['gridModel' => $gridModel]
        );
    }

    public function renderPager($gridModel)
    {
        return $this->view->paginationControl(
            $gridModel->getPaginator(),
            'Sliding',
            'grid/partials/pagination.phtml',
            ['gridModel' => $gridModel]
        );
    }

    public function renderHeader($gridModel)
    {
        $gridModel->setSubject('view partials');

        return $this->view->partial(
            'grid/partials/header.phtml',
            [
                'gridModel' => $gridModel,
            ]
        );
    }

    public function render($gridModel)
    {
        $customFilters = $gridModel->getCustomFilters();
        if (empty($customFilters)) {
            $gridModel->setCustomFilters('');
        }

        return $this->view->partial(
            'grid/base.phtml',
            ['gridModel' => $gridModel]
        );
    }

    public function renderCycleGrid($gridModel)
    {
        return $this->view->partial(
            '/grid/partials/settlement.phtml',
            [
                'cycle' => $gridModel->getCycle(),
                'gridModel' => $gridModel,
            ]
        );
    }

    public function __toString(): string
    {
        try {
            $grid = $this->render($this->view->gridModel);
        } catch (Exception $e) {
            var_dump($e->getMessage());
            die();
        }

        //        $grid = $this->render($this->view->gridModel);
        return (string) $grid;
    }
}

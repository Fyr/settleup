<?php

interface Application_Model_Grid_Header_HeaderInterface
{
    /**
     * @param $grid Application_Model_Grid
     * @param $view
     * @return mixed
     */
    public function getData($grid, $view);
}

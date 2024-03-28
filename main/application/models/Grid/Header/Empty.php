<?php

class Application_Model_Grid_Header_Empty implements Application_Model_Grid_Header_HeaderInterface
{
    public function getData($grid, $view)
    {
        return [];
    }
}

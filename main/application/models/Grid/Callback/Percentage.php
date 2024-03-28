<?php

class Application_Model_Grid_Callback_Percentage
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function wrapper()
    {
        return 'class="num"';
    }

    public function body()
    {
        if (!is_null($this->column)) {
            if (!is_numeric($this->column)) {
                return $this->column;
            }
            $result = '$' . number_format($this->column, 2);
        } else {
            $result = '-';
        }

        return $result;
    }
}

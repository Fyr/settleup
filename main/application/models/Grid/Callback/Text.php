<?php

class Application_Model_Grid_Callback_Text
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        return $this->column ?: '-';
    }

    public function wrapper()
    {
        return (isset($this->additionalParams['class'])) ? 'class="' . $this->additionalParams['class'] . '"' : '';
    }
}

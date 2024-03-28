<?php

class Application_Model_Grid_Callback_DateFormat
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body(): string
    {
        if (!$this->column) {
            return '-';
        }

        if (Zend_Date::isDate($this->column, 'Y-m-d H:i:s')) {
            return DateTime::createFromFormat('Y-m-d H:i:s', $this->column)->format('m/d/Y');
        }

        if (Zend_Date::isDate($this->column, 'Y-m-d')) {
            return DateTime::createFromFormat('Y-m-d', $this->column)->format('m/d/Y');
        }

        return $this->column;
    }
}

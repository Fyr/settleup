<?php

use Application_Model_Entity_System_FileTempStatus as FileTempStatus;

class Application_Model_Grid_Callback_ImportStatus
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        if ($this->column == FileTempStatus::CONST_STATUS_VALID) {
            $result = 'Valid';
        } elseif ($this->column == FileTempStatus::CONST_STATUS_NOT_VALID) {
            $result = 'Not Valid';
        } else {
            $result = '-';
        }

        return $result;
    }
}

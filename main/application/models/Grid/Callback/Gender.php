<?php

class Application_Model_Grid_Callback_Gender
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        if ($this->column == Application_Model_Entity_System_SystemValues::GENDER_MALE) {
            $result = 'Male';
        } elseif ($this->column == Application_Model_Entity_System_SystemValues::GENDER_FEMALE) {
            $result = 'Female';
        } else {
            $result = '-';
        }

        return $result;
    }
}

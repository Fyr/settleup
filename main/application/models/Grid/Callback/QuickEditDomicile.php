<?php

class Application_Model_Grid_Callback_QuickEditDomicile
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function wrapper()
    {
        return 'class="quick-edit" field-name="domicile" field-type="text" record-id="' . $this->row['id'] . '"';
    }
}

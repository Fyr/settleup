<?php

use Application_Model_Entity_Accounts_User as User;

class Application_Model_Grid_Callback_Decrypt implements Application_Model_Grid_Callback_ExcelInterface
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body(): string
    {
        $data = $this->column;
        if ($data) {
            $crypt = new Application_Model_Base_CryptAdvanced();
            $data = $crypt->decrypt($data, User::getCurrentUser()->getCarrierKey());
        }

        return $data ?: '-';
    }

    public function getExcelValue($entity, $method, $processingModel = false)
    {
        $data = $entity->$method();
        if ($data) {
            $crypt = new Application_Model_Base_CryptAdvanced();
            $data = $crypt->decrypt($data, User::getCurrentUser()->getCarrierKey());
        }

        return $data;
    }
}

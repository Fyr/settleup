<?php

class Application_Model_Report_ContractorExport extends Application_Model_Report_Reporting
{
    public static $title = 'Contractor Export File';

    public function getGridData()
    {
        (new Application_Model_Export())->export(
            Application_Model_Entity_System_FileStorageType::CONST_CONTRACTOR_FILE_TYPE,
            $exportFormat = Application_Model_File_Type_Xls::XLS_TYPE,
            [
                [
                    'name' => 'addFilterByStatus',
                    'value' => $this->getSelectContractorStatus(),
                ],
            ]
        );

        return;
    }
}

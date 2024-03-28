<?php

use Application_Model_Entity_System_FileStorageType as FileStorageType;

class Application_Model_Export
{
    public function export($fileType, $exportFormat = Application_Model_File_Type_Xls::XLS_TYPE, $idOrFilters = null)
    {
        $fileName = date('m-d-Y_H-i-s') . '.' . $exportFormat;
        $fileName = match ($fileType) {
            FileStorageType::CONST_PAYMENTS_FILE_TYPE => 'Compensation_' . $fileName,
            FileStorageType::CONST_DEDUCTIONS_FILE_TYPE => 'Deduction_' . $fileName,
            FileStorageType::CONST_CONTRACTOR_FILE_TYPE => 'Contractor_' . $fileName,
            FileStorageType::CONST_CONTRACTOR_TEMP_FILE_TYPE => 'Contractor_Temp_' . $fileName,
            FileStorageType::CONST_POWERUNIT_TEMP_FILE_TYPE => 'Powerunit_Temp_' . $fileName,
            default => throw new Exception('Unknown type of export file.'),
        };

        $fileName = Application_Model_File::getInstance($fileName)->setFileType($fileType)->getExportFile($idOrFilters);

        Application_Model_File::download($fileName, true, Application_Model_File::getHeaderContentType($exportFormat));
    }
}

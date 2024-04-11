<?php
use PHPUnit\Framework\TestCase;

class Application_Model_File_Type_XlsTest extends TestCase
{

    public function testGetContent()
    {
        $file = new Application_Model_File_Type_Xls('test.xls');
        $this->expectException("PhpOffice\PhpSpreadsheet\Reader\Exception");
        $this->expectExceptionMessage('File "/test.xls" does not exist.');
        $file->getContent();
    }

    public function testExtractDateFields()
    {
        $file = new Application_Model_File_Type_Xls('test.xls');
        $columns = Application_Model_File_Type_Xls::getContractorFields();
        $dateFields = $file->extractDateFields($columns);
        $this->assertEqualsCanonicalizing($dateFields, [
            'dob',
            'expires',
            'start_date',
            'termination_date',
            'rehire_date',
        ]);
    }

    public function testConvertDateInData()
    {

    }

    public function testGetFieldsByType()
    {

    }

}

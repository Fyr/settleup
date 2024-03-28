<?php
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testTextFile()
    {
        $name = 'test.txt';
        $file = new Application_Model_File();
        $this->assertEquals($file->getInstance($name), new Application_Model_File_Type_Txt($name, null));
    }

    public function testCsvFile()
    {
        $name = 'test.csv';
        $file = new Application_Model_File();
        $this->assertEquals($file->getInstance($name), new Application_Model_File_Type_Csv($name, null));
    }

    public function testXslFile()
    {
        $name = 'test.xls';
        $file = new Application_Model_File();
        $this->assertEquals($file->getInstance($name), new Application_Model_File_Type_Xls($name, null));
    }

    public function testDefaultFile()
    {
        $name = 'test.default';
        $file = new Application_Model_File();
        try {
            $file->getInstance($name);
        } catch (Exception) {
            $this->assertTrue(true);
            return;
        }
        $this->fail('There should be a Exception! Passed file has unknown type.');
    }

    public function testGetType()
    {
        $name = null;
        $file = new Application_Model_File();
        $this->assertEquals($file->getType($name), null);
        $name = 'tEsT.TxT';
        $this->assertEquals($file->getType($name), 'txt');
    }

    public function testGetName()
    {
        $name = null;
        $file = new Application_Model_File();
        $this->assertEquals($file->getName($name), null);
        $name = './../folder/forder/test.TxT';
        $this->assertEquals($file->getName($name), 'test');
    }

    public function testGetSafeName()
    {
        $name = null;
        $file = new Application_Model_File();
        $this->assertEquals($file->getSafeName($name), null);
        $name = '/ /';
        $this->assertEquals($file->getSafeName($name), '/_/');
    }

    //    public function testParsePaymentXls()
    //    {
    //        $entity =  new Application_Model_File_Type_Xls('1343650484_payments-import-file2.xls','qwerty');
    //        $entity->setFileType(Application_Model_Entity_System_FileStorageType::CONST_PAYMENTS_FILE_TYPE);
    //        $entity->getContent();
    //        $this->assertTrue(true);
    //    }
    //
    //    public function testParseDeductionXls()
    //    {
    //        $entity =  new Application_Model_File_Type_Xls('1344235597_deductions-import-file.xls','qwerty2');
    //        $entity->setFileType(Application_Model_Entity_System_FileStorageType::CONST_DEDUCTIONS_FILE_TYPE);
    //        $entity->getContent();
    //        $this->assertTrue(true);
    //    }

}

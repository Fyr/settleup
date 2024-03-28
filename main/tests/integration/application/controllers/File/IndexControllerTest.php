<?php

class File_IndexControllerTest extends BaseTestCase
{
    /** @var File_IndexController */
    private $controller;

    protected function setUp(): void
    {
        $this->setDefaultController('file_index');
        parent::setUp();
    }

    public function testIndexAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'index'],
                'assert' => ['action' => 'list'],
            ]
        );
    }

    public function testNewAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'new'],
                'assert' => ['action' => 'edit'],
            ]
        );
    }

    public function testEditAction()
    {
        //$filePath = APPLICATION_PATH . '//'
        //        $filePath ='/home/user/Downloads/payment_importing.xls';
        //        $fileName = 'payment_importing.xls';
        //
        //        $_FILES[$fileName] = array(
        //            'name' => $fileName,
        //            'tmp_name' => $fileName,
        //            'type' => 'xls',
        //            'size' => filesize($filePath),
        //            'error' => 0
        //        );

        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => [
                    //'file' => $_FILES[$fileName],
                    'file_type' => 'xls',
                    'file_storage_type' => '1',
                    'title' => 'TitleFileUpload',
                    //'MAX_FILE_SIZE' => filesize($filePath),
                    'submit' => 'save',
                ],
            ]
        );
    }

    public function testListAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'list'],
            ]
        );
    }

    //    public function testGetContentAction()
    //    {
    //        $this->baseTestAction(array(
    //                'params'=> array('action'=>'getcontent'),
    //                'get' => array('id'=>'1')
    //            )
    //        );
    //    }
    //
    //    public function testApproveAction()
    //    {
    //        $this->baseTestAction(array(
    //                'params'=> array('action'=>'approve'),
    //                'get' => array('id'=>'1')
    //            )
    //        );
    //    }
    //
    //    public function testDeleteAction()
    //    {
    //        $this->baseTestAction(array(
    //                'params'=> array('action'=>'delete'),
    //                'get' => array('id'=>'1')
    //            )
    //        );
    //    }
    //
    //    public function testMultiAction()
    //    {
    //        $this->baseTestAction(array(
    //                'params'=> array('action'=>'multiaction'),
    //                'get' => array('ids'=>'1')
    //            )
    //        );
    //    }

}

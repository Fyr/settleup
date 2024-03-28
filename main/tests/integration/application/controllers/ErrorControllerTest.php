<?php

class ErrorControllerTest extends BaseTestCase
{
    /** @var ErrorController */
    private $controller;

    protected function setUp(): void
    {
        $this->setDefaultController('error');
        parent::setUp();
    }

    public function testErrorAction()
    {
        $error = new testErrorTestClass();
        $error->request = $this->getRequest();
        $error->exception = new Exception('Test message');
        $error->type = Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION;
        $this->baseTestAction(
            [
                'params' => ['action' => 'error'],
                'post' => [
                    'error_handler' => $error,
                ],
            ]
        );
    }

    //    /**
    //     * @expectedException Exception
    //     */
    //    public function testErrorActionNotArrayObject()
    //    {
    //        $error = new stdClass();
    //        $this->baseTestAction(array(
    //                'params' => array('action' => 'error'),
    //                'post' => array (
    //                    'error_handler' => $error
    //                ),
    //            ));
    //    }

}

class testErrorTestClass extends ArrayObject
{
    public $type;
    public $exception;
    public $request;
}

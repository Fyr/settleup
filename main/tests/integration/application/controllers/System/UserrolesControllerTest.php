<?php

class System_UserrolesControllerTest extends BaseTestCase
{
    protected function setUp(): void
    {
        $this->setDefaultController('system_userroles');
        parent::setUp();
    }

    public function testListAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'list'],
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

    public function testIndexAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'index'],
                'assert' => ['action' => 'list'],
            ]
        );
    }

    public function testEditActionNewUserrole()
    {
        $post = [
            'id' => '100500',
            'title' => 'testTitle',
            'submit' => 'Save',
        ];

        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $post,
            ]
        );

        return $post;
    }

    /**
     * @depends testEditActionNewUserrole
     */
    public function testEditAction(array $data)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit',],
                'get' => ['id' => $data['id']],
            ]
        );
    }

    /**
     * @depends testEditActionNewUserrole
     */
    public function testEditActionNewUserroleNotValid(array $data)
    {
        $data['title'] = '';
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
    }

    /**
     * @depends testEditActionNewUserrole
     */
    public function testDeleteAction(array $data)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'delete'],
                'get' => ['id' => $data['id']],
            ]
        );
    }

    /**
     * @depends testEditActionNewUserrole
     */
    public function testAddBeforeMultiAction(array $data)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
    }

    /**
     * @depends testEditActionNewUserrole
     */
    public function testMultiDelete(array $data)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'multiaction'],
                'ajax' => ['ids' => $data['id']],
            ]
        );
    }
}

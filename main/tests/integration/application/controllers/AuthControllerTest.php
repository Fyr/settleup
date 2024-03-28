<?php

class AuthControllerTest extends BaseTestCase
{
    /** @var AuthController */
    private $controller;

    protected function setUp(): void
    {
        $this->setDefaultController('auth');
        parent::setUp();
    }

    //    public function testRegistrationAction()
    //    {
    //        $post = array(
    //            'name' => 'PHPUnitTestUser',
    //            'email' => rand(1,32000) . 'phpunit@tula.co',
    //            'password' => 'pass',
    //            'conf_password' => 'pass',
    //            'submit' => 'Save'
    //        );
    //
    //        $this->baseTestAction(
    //            array(
    //                'params' => array('action' => 'registration'),
    //                'post'   => $post,
    //            ),
    //            false
    //        );
    //        $userId = (new Application_Model_Entity_Accounts_User())->load($post['email'],'email')->getId();
    //        $this->assertNotNull($userId);
    //        $post['id'] = $userId;
    //
    //        return $post;
    //    }

    //    public function testLoginAction()
    //    {
    //        $data = [
    //            'id' => 3,
    //            'email' => 'dkozhemyako@tula.co',
    //            'password' => 'pass',
    //        ];
    //
    //        $this->baseTestAction(
    //            array(
    //                'params' => array('action' => 'login'),
    //                'post'   => array(
    //                    'email' => $data['email'],
    //                    'password' => $data['password'],
    //                ),
    //            ),
    //            false
    //        );
    //        $this->assertEquals(
    //            Application_Model_Entity_Accounts_User::getCurrentUser()->getId(),
    //            $data['id']
    //        );
    //    }

    public function testLoginActionEmpty()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'login'],
                'post' => [
                    'email' => '',
                    'password' => '',
                ],
            ],
            false
        );
        $this->assertNull(
            Application_Model_Entity_Accounts_User::getCurrentUser()
                ->getId()
        );
    }

    public function testLoginActionNotValidPassword()
    {
        $data = [
            'email' => 'dkozhemyako@tula.co',
            'password' => 'pass',
        ];
        $this->baseTestAction(
            [
                'params' => ['action' => 'login'],
                'post' => [
                    'email' => $data['email'],
                    'password' => 'incorrect',
                ],
            ],
            false
        );
        $this->assertNull(
            Application_Model_Entity_Accounts_User::getCurrentUser()
                ->getId()
        );
    }

    public function testLogoutAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'logout'],
            ]
        );
        $this->assertNull(
            Application_Model_Entity_Accounts_User::getCurrentUser()
                ->getId()
        );
    }

    //    public function testRegistrationActionNotValidEmail()
    //    {
    //        $post = array(
    //            'name' => rand(1,32000),
    //            'email' =>  'blabla',
    //            'password' => 'pass',
    //            'conf_password' => 'pass',
    //            'submit' => 'Save'
    //        );
    //
    //        $this->baseTestAction(
    //            array(
    //                'params' => array('action' => 'registration'),
    //                'post'   => $post
    //            ),
    //            false
    //        );
    //        $user = new Application_Model_Entity_Accounts_User();
    //        $user_id =$user->load($post['name'],'name')->getId();
    //        $this->assertNull($user_id);
    //    }

    //    public function testRegistrationActionNotValidName()
    //    {
    //        $post = array(
    //            'name' => '',
    //            'email' =>  '1232456blabla@tula.co',
    //            'password' => 'pass',
    //            'conf_password' => 'pass',
    //            'submit' => 'Save'
    //        );
    //
    //        $this->baseTestAction(
    //            array(
    //                'params' => array('action' => 'registration'),
    //                'post'   => $post
    //            ),
    //            false
    //        );
    //        $user = new Application_Model_Entity_Accounts_User();
    //        $user_id =$user->load($post['email'],'email')->getId();
    //        $this->assertNull($user_id);
    //    }

    //    public function testRegistrationActionNotEqualPasswordAndConfPassword()
    //    {
    //        $post = array(
    //            'name' => 'userAuthTest' . rand(1,32000),
    //            'email' =>  'mahab@tula.co',
    //            'password' => 'pass',
    //            'conf_password' => 'ssapp',
    //            'submit' => 'Save'
    //        );
    //
    //        $this->baseTestAction(
    //            array(
    //                'params' => array('action' => 'registration'),
    //                'post'   => $post
    //            ),
    //            false
    //        );
    //        $user = new Application_Model_Entity_Accounts_User();
    //        $user_id =$user->load($post['name'],'name')->getId();
    //        $this->assertNull($user_id);
    //        return $post;
    //    }

    //    /**
    //     * @depends testRegistrationAction
    //     */
    //    public function testRegistrationActionException(array $post)
    //    {
    //        $this->baseTestAction(
    //            array(
    //                'params' => array('action' => 'registration'),
    //                'post'   => $post
    //            ),
    //            false
    //        );
    //    }

}

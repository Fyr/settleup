<?php

class Settlement_RuleControllerTest extends BaseTestCase
{
    /** @var Settlement_RuleController */
    private $controller;

    protected function setUp(): void
    {
        $this->setDefaultController('settlement_rule');
        parent::setUp();
    }

    public function testIndexAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'index'],
            ]
        );
    }

    public function testListAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'list'],
                'assert' => ['action' => 'edit'],
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

    public function testEditActionWithActiveCycle()
    {
        $carrier = $this->newCarrier();
        $user = $this->newUser(
            [
                'entity_id' => $carrier->getData('entity_id'),
                'role_id' => Application_Model_Entity_System_UserRoles::CARRIER_ROLE_ID,
            ]
        );
        $cycle = $this->newCycle($carrier);
        Application_Model_Entity_Accounts_User::login($user->getId());
        $this->setStorage();

        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => [
                    'id' => '',
                    'cycle_period_id' => '1',
                    'change_cycle_rule_fields' => '{"cycle_start_date":"06/20/2014","cycle_period_id":"3","billing_cycle_id":"3"}',
                    'cycle_start_date' => '01-01-2014',
                    'first_start_day' => '1',
                    'second_start_day' => '15',
                    'payment_terms' => random_int(1, 10),
                    'disbursement_terms' => '1',
                    'submit' => 'Save',
                ],

            ]
        );
    }

    public function testEditActionNewRuleNotValid()
    {
        $post = [
            'id' => '',
            'cycle_period_id' => '',
            'change_cycle_rule_fields' => '{}',
            'cycle_start_date' => '',
            'payment_terms' => '',
            'disbursement_terms' => '',
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

    public function testEditActionNewRule()
    {
        $post = [
            'id' => '',
            'cycle_period_id' => '1',
            'change_cycle_rule_fields' => '{"cycle_start_date":"06/20/2014","cycle_period_id":"3","billing_cycle_id":"3"}',
            'cycle_start_date' => '01-01-2014',
            'first_start_day' => '1',
            'second_start_day' => '15',
            'payment_terms' => random_int(1, 1000),
            'disbursement_terms' => '1',
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
     * @depends testEditActionNewRule
     */
    public function testEditActionEditRule(array $data)
    {
        $ruleId = (new Application_Model_Entity_Settlement_Rule())->load($data['payment_terms'], 'payment_terms')
            ->getId();
        $data['id'] = $ruleId;
        $data['payment_terms'] = random_int(1, 1000);
        $data['cycle_period_id'] = Application_Model_Entity_System_CyclePeriod::MONTHLY_PERIOD_ID;
        $data['redirect'] = 'index_index';
        $data['week_day'] = '1';
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $this->assertEquals(
            $data['id'],
            (new Application_Model_Entity_Settlement_Rule())->load($data['payment_terms'], 'payment_terms')
                ->getId()
        );
        return $data;
    }

    /**
     * @depends testEditActionNewRule
     */
    public function testEditActionEditRuleSemimonthly(array $data)
    {
        $data['cycle_period_id'] = Application_Model_Entity_System_CyclePeriod::SEMY_MONTHLY_PERIOD_ID;
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
    }

    /**
     * @depends testEditActionEditRule
     */
    public function testEditActionEditRuleNoCyclePeriodId(array $data)
    {
        $data['cycle_period_id'] = '';
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $this->assertEquals(
            $data['id'],
            (new Application_Model_Entity_Settlement_Rule())->load($data['payment_terms'], 'payment_terms')
                ->getId()
        );
        return $data;
    }

    /** !no action delete
     *
     * @depends testEditActionEditRule
     */
    /*public function testDeleteRule(array $data)
    {
        $this->baseTestAction(
            array(
                'params' => array('action' => 'delete'),
                'get' => array ('id' => $data['id'])
            )
        );
        $this->assertEquals('1',
            (new Application_Model_Entity_Settlement_Rule())->load($data['id'])->getDeleted()
        );
    }*/

    public static $carrier;
    public static $user;

    public function testBeforePermissionsTest()
    {
        self::$carrier = $this->newCarrier();
        self::$user = $this->newUser(
            [
                'entity_id' => self::$carrier->getData('entity_id'),
                'role_id' => Application_Model_Entity_System_UserRoles::CARRIER_ROLE_ID,
            ]
        );
        $this->userPermissions(self::$user);
    }

    /**
     * @dataProvider permissionsProvider
     */
    public function testHasNoPermissions($action, $method, $method_params, $permissions, $assert, $function)
    {
        $this->userPermissions(self::$user, $permissions);
        Application_Model_Entity_Accounts_User::login(self::$user->getId());
        $this->setStorage();
        if ($function) {
            $function();
        }

        $this->baseTestAction(
            [
                'params' => ['action' => $action],
                $method => $method_params,
                'assert' => $assert,
            ],
            false
        );
    }

    public function permissionsProvider()
    {
        return [
            [
                'action' => 'index',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'settlement_rule_view' => '0',
                ],
                'assert' => ['action' => 'index'],
                'function' => false,
            ],
            [
                'action' => 'list',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'settlement_rule_view' => '0',
                ],
                'assert' => ['action' => 'list'],
                'function' => false,
            ],
            [
                'action' => 'new',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'settlement_rule_manage' => '0',
                ],
                'assert' => ['action' => 'new'],
                'function' => false,
            ],
            [
                'action' => 'edit',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'settlement_rule_view' => '0',
                ],
                'assert' => ['action' => 'edit'],
                'function' => false,
            ],
            [
                'action' => 'edit',
                'method' => 'post',
                'method_params' => [
                    'id' => '',
                    'cycle_period_id' => '1',
                    'change_cycle_rule_fields' => '{"cycle_start_date":"06/20/2014","cycle_period_id":"3","billing_cycle_id":"3"}',
                    'cycle_start_date' => '01-01-2014',
                    'first_start_day' => '1',
                    'second_start_day' => '15',
                    'payment_terms' => random_int(1, 1000),
                    'disbursement_terms' => '1',
                    'submit' => 'Save',
                ],
                'permissions' => [
                    'settlement_rule_view' => '1',
                    'settlement_rule_manage' => '0',
                ],
                'assert' => ['action' => 'edit'],
                'function' => false,
            ],
            /* !no action delete
             *             array(
                            'action' => 'delete',
                            'method' => 'get',
                            'method_params' => array(),
                            'permissions' => array(
                                'settlement_rule_view'=> '0',
                            ),
                            'assert' => array(
                                'controller' => 'settlement_index',
                                'action' => 'index'
                            ),
                            'function' => false,
                        ),
            */
        ];
    }
}

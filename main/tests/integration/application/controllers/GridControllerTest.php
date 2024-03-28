<?php

class GridControllerTest extends BaseTestCase
{
    /** @var GridController */
    private $controller;

    protected function setUp(): void
    {
        $this->setDefaultController('grid');
        parent::setUp();
        $this->loginUser();
    }

    public function testIndexAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'index'],
            ]
        );
    }

    public function testFilterActionGet()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'filter'],
                'get' => [],
            ]
        );
    }

    public function testFilterAction()
    {
        $header = [
            'header' => [
                'id' => '#',
                'carrier_payment_code' => 'Company',
            ],
            'sort' => [
                'id' => 'ASC',
                'carrier_payment_code' => 'DESC',
            ],
            'filter' => [
                'description' => 'p',
                'settlement_cycle_id' => '1',
            ],
            'service' => [
                'header' => [
                    'action' => 'Action',
                ],
                'bindOn' => 'id',
                'action' => [
                    'edit' => [
                        'url' => '#',
                        'style' => [
                            'button' => 'btn-primary',
                            'icon_style' => 'icon-pencil',
                        ],
                    ],
                    'delete' => [
                        'url' => '#',
                        'confirm-type' => 'delete',
                        'style' => [
                            'button' => 'btn-danger confirm',
                            'icon_style' => 'icon-trash',
                        ],
                    ],
                ],
            ],
        ];
        $filter = [
            'carrier_payment_code' => 'a',
        ];
        $limit = '25';
        $massaction = [
            'delete' => [
                'caption' => 'Delete Selected',
                'button_class' => 'btn-danger confirm-delete btn-multiaction',
                'confirm-type' => 'delete',
                'icon_class' => 'icon-trash',
                'style' => 'display:none',
                'action-type' => 'delete',
                'url' => '#',
            ],
        ];

        $ajax = [
            'id' => (new Application_Model_Base_Crypt())->encrypt("Application_Model_Grid_Payment_Payment"),
            'filter' => $filter,
            'entity' => 'Application_Model_Entity_Payments_Payment',
            'header' => $header,
            'limit' => $limit,
            'massaction' => $massaction,
            'customFilters' => '',
        ];

        $this->baseTestAction(
            [
                'params' => ['action' => 'filter'],
                'get' => $ajax,
            ],
            false
        );
        return $ajax;
    }

    /**
     * @depends testFilterAction
     */
    public function testFilterAction2(array $data)
    {
        (new Application_Model_Entity_Accounts_User())->load(16)
            ->setData('last_selected_carrier', '1')
            ->save();
        $data['filter']['settlement_cycle_id'] = '1';
        $data['entity'] = 'Application_Model_Entity_Payments_Payment';
        $data['header']['sort'] = ['id' => 'ASC'];
        $data['cycle'] = 'true';
        $this->getRequest()
            ->setCookie('settlement_cycle_id', '1');
        $this->baseTestAction(
            [
                'params' => ['action' => 'filter'],
                'ajax' => $data,
                'assert' => ['controller' => 'grid', 'action' => 'filter'],
            ],
            false
        );
    }

    public function testSortgridcollActionGet()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'sortgridcoll'],
                'get' => [],
            ]
        );
    }

    /**
     * @depends testFilterAction
     */
    public function testSortgridcollAction(array $data)
    {
        Application_Model_Entity_Accounts_User::login(16);
        $this->setStorage();
        $storage = Zend_Auth::getInstance()
            ->getStorage()
            ->read();
        $storage->gridData = [
            'Application_Model_Grid_Payment_Payment' => [
                'sort' => ['contractor_id' => 'ASC'],
            ],
        ];
        $storage->currentControllerName = 'payments_payments';

        $data['id'] = (new Application_Model_Base_Crypt())->encrypt("Application_Model_Grid_Payment_Payment");
        $data['cycle'] = 'true';
        $data['sortCol'] = 'id';
        $data['filter'] = [
            'settlement_cycle_filter_type' => '4',
            'settlement_cycle_filter_year' => 'none',
            'settlement_cycle_id_filter' => '2',
        ];
        $this->baseTestAction(
            [
                'params' => ['action' => 'sortgridcoll'],
                'ajax' => $data,
            ],
            false
        );
    }

    /**
     * @depends testFilterAction
     */
    public function testSortgridcollActionDESC(array $data)
    {
        $data['header']['sort']['id'] = 'DESC';
        Application_Model_Entity_Accounts_User::login(16);
        $this->setStorage();
        $storage = Zend_Auth::getInstance()
            ->getStorage()
            ->read();
        $storage->gridData = [
            'Application_Model_Grid_Payment_Payment' => [
                'sort' => ['contractor_id' => 'ASC'],
            ],
        ];
        $storage->currentControllerName = 'payments_payments';

        $data['id'] = (new Application_Model_Base_Crypt())->encrypt("Application_Model_Grid_Payment_Payment");
        $data['cycle'] = 'true';
        $data['sortCol'] = 'id';
        $data['filter'] = [
            'settlement_cycle_filter_type' => '4',
            'settlement_cycle_filter_year' => 'none',
            'settlement_cycle_id_filter' => '2',
        ];
        $this->baseTestAction(
            [
                'params' => ['action' => 'sortgridcoll'],
                'ajax' => $data,
            ],
            false
        );
        $this->baseTestAction(
            [
                'params' => ['action' => 'sortgridcoll'],
                'ajax' => $data,
            ],
            false
        );
    }

    public function testLimitgridrowActionGet()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'limitgridrow'],
                'get' => [],
            ]
        );
    }

    /**
     * @depends testFilterAction
     */
    public function testLimitgridrowAction(array $data)
    {
        $data['cycle'] = 'true';
        $data['filter']['settlement_cycle_id'] = '1';
        $this->baseTestAction(
            [
                'params' => ['action' => 'limitgridrow'],
                'ajax' => $data,
                'assert' => ['controller' => 'error', 'action' => 'error'],
            ],
            false
        );
    }

    public function testPagerActionGet()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'pager'],
                'get' => [],
            ]
        );
    }

    /**
     * @depends testFilterAction
     */
    public function testPagerAction(array $data)
    {
        $data['pager'] = '1';
        $this->baseTestAction(
            [
                'params' => ['action' => 'pager'],
                'ajax' => $data,
                'assert' => ['controller' => 'grid', 'action' => 'pager'],
            ],
            false
        );
    }

    public function testChangepriorityActionGet()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'changepriority'],
                'get' => [],
            ]
        );
    }

    /**
     * @depends testFilterAction
     */
    public function testChangepriorityAction(array $data)
    {
        $data['beforeList'] = ['1'];
        $data['resultList'] = ['1'];
        $data['current_page'] = '1';
        $data['customFilters'] = ['addNonDeletedFilter'];
        $data['cycle'] = 'true';
        $this->baseTestAction(
            [
                'params' => ['action' => 'changepriority'],
                'ajax' => $data,
                'assert' => ['controller' => 'error', 'action' => 'error'],
            ],
            false
        );
        return $data;
    }

    public function testQuickeditActionGet()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'quickedit'],
                'get' => [],
            ]
        );
    }

    /**
     * @depends testFilterAction
     */
    public function testQuickeditAction(array $data)
    {
        $data['recordId'] = '1';
        $data['field'] = '1';
        $data['value'] = '1';
        $this->baseTestAction(
            [
                'params' => ['action' => 'quickedit'],
                'ajax' => $data,
            ],
            false
        );
    }
}

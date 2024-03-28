<?php
use PHPUnit\Framework\TestCase;

use Application_Model_Rest as Rest;
use Application_Model_Entity_Entity_Carrier as Carrier;
use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_System_UserRoles as UserRoles;

class BaseTestCase extends TestCase
{
    protected $bootstrap = null;
    protected $_myUser = 16;
    // protected $_myUserPassMd5 = '74cbea5364321be7a0e15e5b2ce1d14d';
    protected $_myUserPassMd5 = '1a1dc91c907325c69271ddf0c944bc72';
    public $defaultPassMd5 = '1a1dc91c907325c69271ddf0c944bc72';
    private $_controller = null;

    protected function setUp(): void
    {
        if (!$this->getBootstrap())
        {
            $this->setBootstrap(Zend_Registry::get('bootstrap'));
        }

        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['SERVER_PROTOCOL'] = 'http';
        $_SERVER['HTTP_HOST'] = 'pfleet.loc';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        parent::setUp();
    }

    public function setBootstrap(Zend_Application $bootstrap)
    {
        $this->bootstrap = $bootstrap;
    }

    public function getBootstrap()
    {
        return $this->bootstrap;
    }

    public function baseTestAction(array $data, $login = true)
    {
        if ($login) {
            $this->loginUser();
        }

        if (!isset($data['params']['controller'])) {
            $data['params']['controller'] = $this->_controller;
        }

        $urlParams = $this->urlizeOptions($data['params']);
        $url = $this->url($urlParams);

        if (isset($data['post'])) {
            $this->request->setMethod('POST')
                ->setPost($data['post']);
        }

        if (isset($data['get'])) {
            $this->request->setMethod('GET')
                ->setPost($data['get']);
        }

        if (isset($data['ajax'])) {
            $this->request->setHeader('X-REQUESTED-WITH', 'XMLHttpRequest')
                ->setMethod('POST')
                ->setPost($data['ajax']);
        }

        $this->dispatch($url);

        if (isset($data['assert']['controller'])) {
            $this->assertController($data['assert']['controller']);
        } else {
            if (isset($urlParams['controller'])) {
                $this->assertController($urlParams['controller']);
            } else {
                if ($this->_controller) {
                    $this->assertController($this->_controller);
                }
            }
        }

        if (isset($data['assert']['action'])) {
            $this->assertAction($data['assert']['action']);
        } else {
            if (isset($urlParams['action'])) {
                $this->assertAction($urlParams['action']);
            }
        }

        if (isset($data['assert']['module'])) {
            $this->assertModule($data['assert']['module']);
        } else {
            if (isset($urlParams['module'])) {
                $this->assertModule($urlParams['module']);
            }
        }
        return $data;
    }

    public function loginUser($userId = null, $passwordMd5 = null)
    {
        if (!$passwordMd5) {
            $passwordMd5 = $this->_myUserPassMd5;
        }
        if (!$userId) {
            $userId = $this->_myUser;
        }
        $loginFail = true;
        $user = Application_Model_Entity_Accounts_User::login($userId);
        $restService = new Rest();
        $carrierEntityId = $user->getAssociatedCarrierId();
        if ($loginData = $restService->login($userId, $passwordMd5, $carrierEntityId)) {
            if ($user::login($userId) && isset($loginData['credentials'])) {
                $user->setCredentials($loginData['credentials']);
                $_COOKIE['token'] = $loginData['credentials']['token'];
                $loginFail = false;
            }
        }
        $this->assertFalse($loginFail, 'Login Fail - ' . $userId . '-' . $passwordMd5);
        $this->setStorage();

        return $user;
    }

    public function setDefaultController($controller = null)
    {
        if (isset($controller)) {
            $this->_controller = $controller;
        }
    }

    public function updateDB($name)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $sql = file_get_contents(APPLICATION_PATH . '/../scripts/db/test/' . $name);
        $db->query($sql);
        $db->commit();
    }

    public function setFieldValue($table, $id, $field, $value)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->getConnection();
        $db->beginTransaction();
        $sql = "UPDATE " . $table . " SET " . $field . "=" . $value . " WHERE id=" . $id . ";";
        $db->query($sql);
        $db->commit();
    }

    /**
     * @param $role_id
     * @return Application_Model_Entity_Accounts_User
     */
    public function getUserByRole($role_id)
    {
        return (new Application_Model_Entity_Accounts_User())->getCollection()
            ->addFilter('role_id', $role_id)
            ->getFirstItem();
    }

    public function setStorage()
    {
        $storage = Zend_Auth::getInstance()
            ->getStorage()
            ->read();
        $storage->currentControllerName = $this->_controller;
        $storage->isNotGridRequest = 'true';
    }

    /**
     * @param array $fields
     * @return Application_Model_Entity_Accounts_User
     */
    public function newUser($fields = [])
    {
        if (!User::getCurrentUser()
            ->getId()) {
            $this->loginUser();
        }
        $rnd = $this->getRandomString();
        $data = [
            'role_id' => UserRoles::SUPER_ADMIN_ROLE_ID,
            'email' => $rnd . '@' . $rnd . '.com',
            'name' => $rnd,
            'password' => 'pass',//'1a1dc91c907325c69271ddf0c944bc72',//pass
            'last_selected_carrier' => '',
            'last_selected_contractor' => '',
            'receive_notifications' => '1',
            'deleted' => '',
            'entity_id' => '',
        ];
        $data = array_merge($data, $fields);
        /** @var User $entity */
        $entity = (new User())->setData($data)
            ->save();
        if ($entity->isVendor() || $entity->isContractor()) {
            $userEntity = (new Application_Model_Entity_Accounts_UserEntity())->setEntityId($entity->getEntityId())
                ->setUserId($entity->getId());
            $userEntity->save();
            $entity->unsPassword();
            $entity->updateRestData();
        }

        return $entity;
    }

    /**
     * @param $user Application_Model_Entity_Accounts_User | int
     * @param array $fields
     * @return Application_Model_Entity_Entity_Permissions
     */
    public function userPermissions($user, $fields = [])
    {
        if ($user instanceof Application_Model_Entity_Accounts_User) {
            $userId = $user->getId();
        } else {
            $userId = $user;
        }

        $permission = (new Application_Model_Entity_Entity_Permissions())->getCollection()
            ->addFilter('user_id', $userId)
            ->getFirstItem();
        if (!$permission->getId()) {
            $permission = new Application_Model_Entity_Entity_Permissions();
        }

        $data['user_id'] = $userId;
        $data = array_merge($data, $fields);
        $permission->addData($data)
            ->save();
        return $permission;
    }

    /**
     * @return Application_Model_Entity_Entity_Carrier
     */
    public function newCarrier($fields = [], $addBA = true, $addEA = true)
    {
        $rnd = $this->getRandomString();
        $data = [
            'tax_id' => '11-1' . random_int(100, 999) . random_int(100, 999),
            'short_code' => $rnd,
            'name' => $rnd,
            'contact' => $rnd,
            'terms' => '1',
            'status' => '1',
        ];
        $data = array_merge($data, $fields);
        $entity = (new Application_Model_Entity_Entity_Carrier())->setData($data)
            ->save();
        $entity = (new Application_Model_Entity_Entity_Carrier())->load($entity->getData('entity_id'), 'entity_id');
        if ($addBA) {
            $this->newBankAccount($entity);
        }
        if ($addEA) {
            $this->newEscrowAccount($entity);
        }
        (new Application_Model_Entity_Accounts_User())->load($this->_myUser)
            ->setData('last_selected_carrier', $entity->getId())
            ->save();
        return $entity;
    }

    /**
     * @param $carrier
     * @param array $fields
     * @param bool $addBA
     * @return Application_Model_Entity_Entity_Contractor
     */
    public function newContractor($carrier, $fields = [], $addBA = true)
    {
        $rnd = $this->getRandomString();
        $data = [
            'social_security_id' => '222-22-' . random_int(1000, 9999),
            'tax_id' => '22-2' . random_int(100, 999) . random_int(100, 999),
            'company_name' => $rnd,
            'first_name' => $rnd,
            'last_name' => $rnd,
            'code' => $rnd,
            'driver_license' => $rnd,
            'state_of_operation' => '-',
            'entity_contact_type' => '1',
            'correspondence_method' => '1',
            'carrier_id' => $carrier->getEntityId(),
        ];
        $data = array_merge($data, $fields);
        $entity = (new Application_Model_Entity_Entity_Contractor())->setData($data)
            ->save();
        $entity = (new Application_Model_Entity_Entity_Contractor())->load($entity->getEntityId(), 'entity_id');
        if ($addBA) {
            $this->newBankAccount($entity);
        }
        return $entity;
    }

    /**
     * @param $carrier Application_Model_Entity_Entity_Carrier
     * @param array $fields
     * @param bool $addBA
     * @param $attachedContractor Application_Model_Entity_Entity_Contractor
     * @return Application_Model_Base_Entity
     */
    public function newVendor($carrier, $fields = [], $addBA = true, $attachedContractor = null)
    {
        $rnd = $this->getRandomString();
        $data = [
            'tax_id' => '33-3' . random_int(100, 999) . random_int(100, 999),
            'name' => $rnd,
            'contact' => $rnd,
            'terms' => '1',
            'priority' => '1',
            'correspondence_method' => '1',
            'carrier_id' => $carrier->getEntityId(),
            'code' => $rnd,
            'status' => 1,
        ];
        $data = array_merge($data, $fields);
        $entity = (new Application_Model_Entity_Entity_Vendor())->setData($data)
            ->save();
        $entity = (new Application_Model_Entity_Entity_Vendor())->load($entity->getEntityId(), 'entity_id');

        if ($attachedContractor != null) {
            (new Application_Model_Entity_Entity_ContractorVendor())->setData(
                [
                    'contractor_id' => $attachedContractor->getData('entity_id'),
                    'vendor_id' => $entity->getData('entity_id'),
                    'status' => '0',
                ]
            )
                ->save();
        }
        if ($addBA) {
            $this->newBankAccount($entity);
        }
        return $entity;
    }

    /**
     * @param $entity Application_Model_Entity_Entity
     * @param array $fields
     * @return Application_Model_Entity_Accounts_Escrow
     */
    public function newEscrowAccount($entity, $fields = [])
    {
        $data = [
            'carrier_id' => $entity->getEntityId(),
            'escrow_account_holder' => 'Escrow account holder',
            'holder_federal_tax_id' => random_int(10, 99) . '-' . random_int(1_000_000, 9_999_999),
            'bank_name' => 'Bank',
            'bank_routing_number' => '123456789',
            'bank_account_number' => '123456789',
            'next_check_number' => '0',
            'holder_address' => 'addr',
            'holder_address_2' => 'addr2',
            'holder_city' => 'city',
            'holder_state' => 'state',
            'holder_zip' => 'zip',
        ];
        $data = array_merge($data, $fields);
        $entity = (new Application_Model_Entity_Accounts_Escrow())->setData($data)
            ->save();
        return $entity;
    }

    /**
     * @param Application_Model_Entity_Entity_Carrier
     * @param array $fields
     * @return Application_Model_Entity_Settlement_Cycle
     */

    public function newCycle($carrier, $fields = [])
    {
        $data = [
            'carrier_id' => $carrier->getEntityId(),
            'cycle_period_id' => '1',    //weekly
            'cycle_start_date' => '2014-06-01',
            'cycle_close_date' => '2014-06-07',
            'disbursement_terms' => '1',
            'payment_terms' => '1',
            'status_id' => '1',
            'first_start_day' => '1',
            'second_start_day' => '1',
            'processing_date' => '',
            'disbursement_date' => '',
            'disbursement_status' => '4',
            //'parent_cycle_id'    => '',
            'approved_datetime' => '',
            //'approved_by'        => '16',
            'week_day' => '1',
        ];
        $data = array_merge($data, $fields);
        $entity = (new Application_Model_Entity_Settlement_Cycle())->setData($data)
            ->save();
        $entity = (new Application_Model_Entity_Settlement_Cycle())->load($entity->getId());

        (new Application_Model_Entity_Settlement_Rule())->setData(
            [
                'carrier_id' => $entity->getData('carrier_id'),
                'cycle_period_id' => $entity->getData('cycle_period_id'),
                'payment_terms' => $entity->getData('payment_terms'),
                'disbursement_terms' => $entity->getData('disbursement_terms'),
                'cycle_start_date' => $entity->getData('cycle_start_date'),
                'first_start_day' => $entity->getData('first_start_day'),
                'second_start_day' => $entity->getData('second_start_day'),
            ]
        )
            ->save();

        return $entity;
    }

    /**
     * @param $carrier Application_Model_Entity_Entity_Carrier
     * @param array $fields
     * @return Application_Model_Entity_Payments_Setup
     */
    public function newPaymentSetup($carrier, $fields = [])
    {
        $rnd = $this->getRandomString();
        $data = [
            'carrier_id' => $carrier->getEntityId(),
            'payment_code' => $rnd,
            'carrier_payment_code' => $rnd,
            'description' => $rnd,
            'category' => $rnd,
            'terms' => '1',
            'department' => $rnd,
            'gl_code' => $rnd,
            'disbursement_code' => $rnd,
            'recurring' => '0',
            'level_id' => '0',
            'billing_cycle_id' => Application_Model_Entity_System_CyclePeriod::WEEKLY_PERIOD_ID,
            'rate' => '1',
            'first_start_day' => '',
            'second_start_day' => '',
            'quantity' => '1',
            'deleted' => '0',
            'week_offset' => '1',
            'week_day' => '1',
        ];
        $data = array_merge($data, $fields);
        $entity = (new Application_Model_Entity_Payments_Setup())->setData($data)
            ->save();
        $entity->createIndividualTemplates();
        return $entity;
    }

    /**
     * @param $contractor Application_Model_Entity_Entity_Contractor
     * @param $setup Application_Model_Entity_Payments_Setup
     * @param $cycle Application_Model_Entity_Settlement_Cycle
     * @param array $fields
     * @return Application_Model_Entity_Payments_Payment
     */
    public function newPayment($contractor, $setup, $cycle, $fields = [])
    {
        $data = [
            'setup_id' => $setup->getId(),
            'category' => $setup->getData('category'),
            'description' => $setup->getData('description'),
            'invoice' => '',
            'invoice_date' => $cycle->getData('cycle_close_date'),
            'invoice_due_date' => $setup->getData('processing_date'),
            'department' => $setup->getData('department'),
            'gl_code' => $setup->getData('gl_code'),
            'quantity' => $setup->getData('quantity'),
            'rate' => $setup->getData('rate'),
            'amount' => $setup->getData('quantity') * $setup->getData('rate'),
            'check_id' => '',
            'disbursement_date' => $setup->getData('disbursement_date'),
            'approved_datetime' => '',
            'approved_by' => '',
            'created_datetime' => '',
            'created_by' => '',
            'source_id' => '',
            //'status'               => '',
            'settlement_cycle_id' => $cycle->getId(),
            'contractor_id' => $contractor->getData('entity_id'),
            'balance' => $setup->getData('quantity') * $setup->getData('rate'),
            'carrier_id' => $contractor->getData('carrier_id'),
            'payment_code' => $setup->getData('payment_code'),
            'carrier_payment_code' => $setup->getData('carrier_payment_code'),
            'terms' => $setup->getData('terms'),
            'disbursement_code' => $setup->getData('disbursement_code'),
            'recurring' => $setup->getData('recurring'),
            'billing_cycle_id' => $setup->getData('billing_cycle_id'),
            'first_start_day' => $setup->getData('first_start_day'),
            'second_start_day' => $setup->getData('second_start_day'),
            'deleted' => '',
            'recurring_parent_id' => '',
            'week_offset' => $setup->getData('week_offset'),
        ];
        $data = array_merge($data, $fields);
        $entity = (new Application_Model_Entity_Payments_Payment())->setData($data)
            ->save();
        return $entity;
    }

    /**
     * @param $provider Application_Model_Entity_Entity_Carrier | Application_Model_Entity_Entity_Vendor
     * @param array $fields
     * @return Application_Model_Entity_Deductions_Setup
     */
    public function newDeductionSetup($provider, $fields = [])
    {
        $rnd = $this->getRandomString();
        $data = [
            'provider_id' => $provider->getEntityId(),
            'vendor_deduction_code' => $rnd,
            'description' => $rnd,
            'category' => $rnd,
            'department' => $rnd,
            'gl_code' => $rnd,
            'disbursement_code' => $rnd,
            'priority' => '0',
            'recurring' => '0',
            'level_id' => '0',
            'billing_cycle_id' => Application_Model_Entity_System_CyclePeriod::WEEKLY_PERIOD_ID,
            'terms' => '1',
            'rate' => '1',
            'eligible' => '',
            'reserve_account_receiver' => '',
            'first_start_day' => '',
            'second_start_day' => '',
            'deduction_code' => $rnd,
            'quantity' => '1',
            'deleted' => '',
            'week_offset' => '',
        ];
        $data = array_merge($data, $fields);
        $entity = (new Application_Model_Entity_Deductions_Setup())->setData($data)
            ->save();
        $entity->createIndividualTemplates();
        return $entity;
    }

    /**
     * @param $contractor Application_Model_Entity_Entity_Contractor
     * @param $setup Application_Model_Entity_Deductions_Setup
     * @param $cycle Application_Model_Entity_Settlement_Cycle
     * @param array $fields
     * @return Application_Model_Entity_Deductions_Deduction
     */
    public function newDeduction($contractor, $setup, $cycle, $fields = [])
    {
        $data = [
            'setup_id' => $setup->getId(),
            'category' => $setup->getData('category'),
            'description' => $setup->getData('description'),
            'priority' => '',
            'invoice_id' => '',
            'invoice' => '',
            'invoice_date' => $cycle->getData('cycle_close_date'),
            'invoice_due_date' => $setup->getData('processing_date'),
            'department' => $setup->getData('department'),
            'gl_code' => $setup->getData('gl_code'),
            'disbursement_code' => $setup->getData('disbursement_code'),
            'rate' => $setup->getData('rate'),
            'quantity' => $setup->getData('quantity'),
            'amount' => $setup->getData('quantity') * $setup->getData('rate'),
            'disbursement_date' => $setup->getData('disbursement_date'),
            'balance' => $setup->getData('quantity') * $setup->getData('rate'),
            'adjusted_balance' => '',
            'approved_datetime' => '',
            'approved_by' => '',
            'created_datetime' => '',
            'created_by' => '',
            'source_id' => '',
            //'status'               => '',
            'settlement_cycle_id' => $cycle->getId(),
            'contractor_id' => $contractor->getData('entity_id'),
            'provider_id' => $contractor->getData('carrier_id'),
            'terms' => $setup->getData('terms'),
            'recurring' => $setup->getData('recurring'),
            'reserve_account_receiver' => $setup->getData('reserve_account_receiver'),
            'billing_cycle_id' => $setup->getData('billing_cycle_id'),
            'eligible' => $setup->getData('eligible'),
            'first_start_day' => $setup->getData('first_start_day'),
            'second_start_day' => $setup->getData('second_start_day'),
            'deduction_code' => $setup->getData('deduction_code'),
            'deleted' => '',
            'recurring_parent_id' => '',
            'week_offset' => $setup->getData('week_offset'),
            'carrier_id' => $contractor->getData('carrier_id'),
        ];
        $data = array_merge($data, $fields);
        /**
         * @var $entity Application_Model_Entity_Deductions_Deduction
         */
        $entity = (new Application_Model_Entity_Deductions_Deduction())->setData($data)
            ->save();
        $entity->reorderPriority($cycle->getId(), $contractor->getData('entity_id'));
        return $entity;
    }

    /**
     * @param $entity Application_Model_Entity_Entity
     * @param array $fields
     * @return Application_Model_Entity_Accounts_Reserve_Contractor|Application_Model_Entity_Accounts_Reserve_Vendor
     */
    public function newReserveAccount($entity, $fields = [])
    {
        $rnd = $this->getRandomString();
        $data = [
            'entity_id' => $entity->getEntityId(),
            //'bank_account_id' => '',
            'account_name' => $rnd,
            'description' => $rnd,
            'priority' => '0',
            'min_balance' => '0',
            'contribution_amount' => '0',
            'initial_balance' => '0',
            'current_balance' => '0',
            'disbursement_code' => $rnd,
            'balance' => '0',
            'deleted' => '',
            'starting_balance' => '0',
            'verify_balance' => '0',
        ];

        if (($entity instanceof Application_Model_Entity_Entity_Contractor)) {
            $model = new Application_Model_Entity_Accounts_Reserve_Contractor();
            $data['reserve_account_vendor_id'] = $fields['reserve_account_vendor_id'];
            $data['vendor_reserve_code'] = $rnd;
        } else {
            $model = new Application_Model_Entity_Accounts_Reserve_Vendor();
            $data['vendor_reserve_code'] = $rnd;
        }
        $data = array_merge($data, $fields);
        $model = $model->setData($data)
            ->save();
        return $model;
    }

    /**
     * @param $cycle Application_Model_Entity_Settlement_Cycle | int
     * @param $contractorRA Application_Model_Entity_Accounts_Reserve_Contractor
     * @param $providerRA Application_Model_Entity_Accounts_Reserve_Vendor | Application_Model_Entity_Accounts_Reserve_Carrier
     * @param $type int
     * @param $deduction Application_Model_Entity_Deductions_Deduction
     * @param array $fields
     * @return Application_Model_Entity_Accounts_Reserve_Transaction
     */
    public function newReserveTransaction($cycle, $contractorRA, $providerRA, $type, $deduction, $fields = [])
    {
        $cycleId = $cycle;
        if ($cycle instanceof Application_Model_Entity_Settlement_Cycle) {
            $cycleId = $cycle->getId();
        }

        $rnd = $this->getRandomString();
        $data = [
            'reserve_account_contractor' => $contractorRA->getData('reserve_account_id'),
            'reserve_account_vendor' => $providerRA->getData('reserve_account_id'),
            'vendor_code' => $rnd,
            'type' => $type,
            'deduction_id' => $deduction->getId(),
            'priority' => '',
            'amount' => '1',
            'balance' => '1',
            'adjusted_balance' => '',
            'settlement_cycle_id' => $cycleId,
            'approved_datetime' => '',
            'approved_by' => '',
            'created_datetime' => '',
            'created_by' => '',
            //'source_id'            => '',
            'disbursement_id' => '',
            'status' => Application_Model_Entity_System_PaymentStatus::VERIFIED_STATUS,
            'description' => $rnd,
            'code' => $rnd,
            'deleted' => '',
            'contractor_id' => (new Application_Model_Entity_Accounts_Reserve())->load(
                $contractorRA->getData('reserve_account_id')
            )
                ->getData('entity_id'),
        ];

        $data = array_merge($data, $fields);
        $entity = (new Application_Model_Entity_Accounts_Reserve_Transaction())->setData($data)
            ->save();
        return $entity;
    }

    protected function getRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $result;
    }

    /**
     * @param $cycle Application_Model_Entity_Settlement_Cycle
     * @return array
     */
    public function getCycleActions($cycle)
    {
        if ($cycle instanceof Application_Model_Entity_Settlement_Cycle) {
            $cycle = $cycle->getId();
        }
        $result['cycle'] = (new Application_Model_Entity_Settlement_Cycle())->load($cycle)
            ->getData();
        $result['payments'] = $this->collectionToArray(
            (new Application_Model_Entity_Payments_Payment())->getCollection()
                ->addFilter('settlement_cycle_id', $cycle)
                ->getItems()
        );
        $result['deductions'] = $this->collectionToArray(
            (new Application_Model_Entity_Deductions_Deduction())->getCollection()
                ->addFilter('settlement_cycle_id', $cycle)
                ->getItems()
        );
        $result['transactions'] = $this->collectionToArray(
            (new Application_Model_Entity_Accounts_Reserve_Transaction())->getCollection()
                ->addFilter('settlement_cycle_id', $cycle)
                ->getItems()
        );
        $result['disbursements'] = $this->collectionToArray(
            (new Application_Model_Entity_Transactions_Disbursement())->getCollection()
                ->addFilter('settlement_cycle_id', $cycle)
                ->getItems()
        );
        return $result;
    }

    /**
     * @return array
     */
    public function getMonolith()
    {
        return [
            'cycles' => (new Application_Model_Entity_Settlement_Cycle())->getCollection()
                ->getLastItem()
                ->getId(),
            'payments' => (new Application_Model_Entity_Payments_Payment())->getCollection()
                ->getLastItem()
                ->getId(),
            'deductions' => (new Application_Model_Entity_Deductions_Deduction())->getCollection()
                ->getLastItem()
                ->getId(),
            'transactions' => (new Application_Model_Entity_Accounts_Reserve_Transaction())->getCollection()
                ->getLastItem()
                ->getId(),
            'disbursements' => (new Application_Model_Entity_Transactions_Disbursement())->getCollection()
                ->getLastItem()
                ->getId(),
            'RAs' => (new Application_Model_Entity_Accounts_Reserve())->getCollection()
                ->getLastItem()
                ->getId(),
        ];
    }

    /**
     * @param $collection
     * @return array
     */
    public function collectionToArray($collection)
    {
        $array = [];
        foreach ($collection as $item) {
            $array[] = $item->getData();
        }
        return $array;
    }
}

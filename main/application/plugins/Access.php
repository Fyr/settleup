<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_System_SystemValues as SystemValues;
use Application_Model_Entity_System_UserRoles as UserRoles;

class Application_Plugin_Access extends Zend_Controller_Plugin_Abstract
{
    private $_acl = null;
    private $_auth = null;

    public function __construct()
    {
        $this->_acl = $this->getRole();
        $this->_auth = Zend_Auth::getInstance();
    }

    protected function getRole()
    {
        $acl = new Zend_Acl();

        // define Roles
        $acl
            ->addRole(new Zend_Acl_Role(UserRoles::DEFAULT_ROLE))
            ->addRole(new Zend_Acl_Role(UserRoles::GUEST_ROLE_ID), UserRoles::DEFAULT_ROLE)
            ->addRole(new Zend_Acl_Role(UserRoles::ONBOARDING_ROLE_ID), UserRoles::GUEST_ROLE_ID)
            ->addRole(new Zend_Acl_Role(UserRoles::SPECIALIST_ROLE_ID), UserRoles::ONBOARDING_ROLE_ID)
            ->addRole(new Zend_Acl_Role(UserRoles::MANAGER_ROLE_ID), UserRoles::SPECIALIST_ROLE_ID)
            ->addRole(new Zend_Acl_Role(UserRoles::ADMIN_ROLE_ID), UserRoles::MANAGER_ROLE_ID)
            ->addRole(new Zend_Acl_Role(UserRoles::SUPER_ADMIN_ROLE_ID), UserRoles::ADMIN_ROLE_ID);

        // define Resource
        $acl
            ->addResource('activity')
            ->addResource('file_index')
            ->addResource('escrow')
            ->addResource('custom')
            ->addResource('settlement_index')
            ->addResource('settlement_rule')
            ->addResource('payments_setup')
            ->addResource('payments_setup_new')
            ->addResource('payments_index')
            ->addResource('payments_payments')
            ->addResource('payments_payments_upload')
            ->addResource('deductions_setup')
            ->addResource('deductions_setup_new')
            ->addResource('deductions_deductions')
            ->addResource('deductions_deductions_upload')
            ->addResource('deductions_index')
            ->addResource('transactions_index')
            ->addResource('reserve_transactions')
            ->addResource('transactions_disbursement')
            ->addResource('carriers_index')
            ->addResource('account_index')
            ->addResource('contractors_index')
            ->addResource('contractors_upload')
            ->addResource('contractors_my')
            ->addResource('vendors_index')
            ->addResource('vendors_my')
            ->addResource('reporting_index')
            ->addResource('reporting_carrier')
            ->addResource('reporting_contractor')
            ->addResource('reporting_vendor')
            ->addResource('reporting_settlement')
            ->addResource('reporting_ondemand')
            ->addResource('users_index')
            ->addResource('reserve_accountpowerunit')
            ->addResource('reserve_accountpowerunit_new')
            ->addResource('reserve_accountcarriervendor')
            ->addResource('reserve_accountcarriervendor_new')
            ->addResource('reserve_index')
            ->addResource('error')
            ->addResource('index')
            ->addResource('system_userroles')
            ->addResource('system_usercontacttypes')
            ->addResource('system_setuplevels')
            ->addResource('system_cycleperiods')
            ->addResource('system_cycletypes')
            ->addResource('system_paymentstatuses')
            ->addResource('system_paymenttypes')
            ->addResource('system_reservetransactiontypes')
            ->addResource('system_contractorstatuses')
            ->addResource('system_recurringtitles')
            ->addResource('system_settlementcyclestatus')
            ->addResource('system_index')
            ->addResource('system_systemvalues')
            ->addResource('system_emaillog')
            ->addResource('auth')
            ->addResource('grid')
            ->addResource('cron')
            ->addResource('users_visibility')
            ->addResource('freeze')
            ->addResource('contractor_info')
            ->addResource('powerunits_index')
            ->addResource('settlement_group')
            ->addResource('guest')
            ->addResource('interestrate_index');

        //default
        $acl->allow(UserRoles::DEFAULT_ROLE, ['index', 'auth', 'error', 'grid']);

        //guest
        $acl->allow(UserRoles::GUEST_ROLE_ID, ['guest', 'auth', 'error', 'grid']);

        //contractor
        $acl->allow(
            UserRoles::SPECIALIST_ROLE_ID,
            [
                'contractor_info',
                'contractors_index',
                'powerunits_index',
                'reporting_index',
                'reporting_ondemand',
                'reporting_ondemand',
                'reporting_settlement',
                'users_index',
                'reserve_accountpowerunit',
            ]
        );

        //vendor
        $acl->allow(
            UserRoles::ONBOARDING_ROLE_ID,
            [
                'deductions_setup',
                'deductions_setup_new',
                'deductions_deductions',
                'deductions_deductions_upload',
                'deductions_index',
                'reserve_transactions',
                'reserve_index',
                'account_index',
                'reserve_accountcarriervendor',
                'reserve_accountcarriervendor_new',
                'transactions_index',
                'users_visibility',
                'contractors_index',
                'file_index',
            ]
        );
        $acl->deny(
            UserRoles::ONBOARDING_ROLE_ID,
            [
                //                'reserve_accountpowerunit',
                'reserve_accountpowerunit_new',
                'contractor_info',
            ]
        );

        //carrier
        $acl->allow(
            UserRoles::MANAGER_ROLE_ID,
            [
                'transactions_index',
                'reserve_transactions',
                'reserve_accountpowerunit',
                'reserve_accountpowerunit_new',
                'reserve_accountcarriervendor',
                'reserve_accountcarriervendor_new',
                'transactions_disbursement',
                'settlement_index',
                'settlement_rule',
                'settlement_group',
                'payments_setup',
                'payments_setup_new',
                'payments_payments',
                'payments_payments_upload',
                'payments_index',
                'contractors_index',
                'contractors_my',
                'contractors_upload',
                'vendors_index',
                'carriers_index',
            ]
        );

        //        $acl->deny(
        //            UserRoles::MANAGER_ROLE_ID,
        //            'reserve_transactions'
        //        );

        //moderator
        $acl->allow(UserRoles::ADMIN_ROLE_ID);

        $acl->deny(
            UserRoles::ADMIN_ROLE_ID,
            ['contractor_info', 'freeze']
        );

        //admin
        $acl->allow(
            UserRoles::SUPER_ADMIN_ROLE_ID,
            [/*'freeze',*/ 'carriers_index']
        );

        $acl->deny(
            UserRoles::SUPER_ADMIN_ROLE_ID,
            ['contractor_info']
        );

        Zend_Registry::set('Zend_Acl', $acl);

        $identity = Zend_Auth::getInstance()->getStorage()->read();
        if ($identity) {
            if ($identity->getRoleId() && $identity->getCredentials()) {
                $role = $identity->getRoleId();
            } else {
                $role = UserRoles::DEFAULT_ROLE;
            }
        } else {
            $role = UserRoles::DEFAULT_ROLE;
        }
        Zend_Registry::set('currentRole', $role);

        return $acl;
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        $resource = $request->getControllerName();
        $action = $request->getActionName();
        $identity = Zend_Auth::getInstance()->getStorage()->read();
        if (PHP_SAPI == 'cli') {
            $role = UserRoles::SUPER_ADMIN_ROLE_ID;
        } else {
            if (Zend_Registry::get('currentRole') == UserRoles::DEFAULT_ROLE) {
                $role = UserRoles::DEFAULT_ROLE;
                Zend_Auth::getInstance()->clearIdentity();
            } else {
                $role = $identity ? $identity->getRoleId() : UserRoles::DEFAULT_ROLE;
            }
        }

        if (isset($_COOKIE['token'])) {
            setcookie('token', (string)$_COOKIE['token'], ['expires' => time() + 31_536_020, 'path' => '/']);
        }

        Application_Plugin_Access::applyCarrierPermissions($this->_acl);

        if (!$this->_acl->isAllowed($role, $resource, $action)) {
            if ($role == UserRoles::DEFAULT_ROLE) {
                $redirector->goToUrl('/auth/login');
            } else {
                $redirector->goToUrl('/');
            }

            return;
        }

        if ($identity instanceof User) {
            if (!isset($_COOKIE['token']) && APPLICATION_ENV != 'testing') {
                //            if (false) {
                Zend_Auth::getInstance()->clearIdentity();
                $redirector->gotoUrl('/auth/login');

                return;
            }
            if (!$identity->isSpecialist()) {
                if (!in_array(
                    $resource,
                    ['carriers_index', 'users_index', 'index', 'error', 'grid', 'auth', 'guest']
                )) {
                    if ($identity->isAdminOrSuperAdmin() && in_array($resource, [
                            'system_userroles',
                            'system_usercontacttypes',
                            'system_setuplevels',
                            'system_cycleperiods',
                            'system_cycletypes',
                            'system_paymentstatuses',
                            'system_paymenttypes',
                            'system_reservetransactiontypes',
                            'system_contractorstatuses',
                            'system_recurringtitles',
                            'system_settlementcyclestatus',
                            'system_index',
                            'system_systemvalues',
                            'cron',
                            'users_visibility',
                            'freeze',
                        ])) {
                        return;
                    } else {
                        $carrierVendor = $identity->getEntity();
                        if ($carrierVendor->getId()) {
                            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                            if ($carrierVendor->getStatus() != SystemValues::CONFIGURED_STATUS) {
                                $redirector->gotoSimple('escrow', 'carriers_index', null, [
                                    'entity' => $carrierVendor->getEntityId(),
                                    'showMessage' => 'true',
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }

    public static function applyCarrierPermissions($acl)
    {
        $user = User::getCurrentUser();
        /** @var $navigation My_Navigation */
        $navigation = Zend_Registry::get('Zend_Navigation');
        $deny = [];

        if ($user->isManager()) {
            if (!$user->hasPermission(Permissions::SETTLEMENT_DATA_VIEW)) {
                $deny[] = 'reserve_transactions';
                $deny[] = 'payments_payments';
                $deny[] = 'deductions_deductions';
                $deny[] = 'settlement_index';
            }

            if (!$user->hasPermission(Permissions::TEMPLATE_MANAGE)) {
                $deny[] = 'payments_setup_new';
                $deny[] = 'deductions_setup_new';
            }

            if (!$user->hasPermission(Permissions::TEMPLATE_VIEW)) {
                $deny[] = 'payments_setup';
                $deny[] = 'deductions_setup';
            }

            if (!$user->hasPermission(Permissions::UPLOADING)) {
                $deny[] = 'file_index';
                $deny[] = 'payments_payments_upload';
                $deny[] = 'deductions_deductions_upload';
                $deny[] = 'contractors_upload';
            }

            if (!$user->hasPermission(Permissions::SETTLEMENT_DATA_VIEW) && !$user->hasPermission(
                Permissions::TEMPLATE_VIEW
            ) && !$user->hasPermission(Permissions::TEMPLATE_VIEW) && !$user->hasPermission(
                Permissions::UPLOADING
            )) {
                $deny[] = 'deductions_index';
                $deny[] = 'payments_index';
            }

            if (!$user->hasPermission(Permissions::CARRIER_VIEW)) {
                $deny[] = 'carriers_index';
            }

            if (!$user->hasPermission(Permissions::CONTRACTOR_VIEW)) {
                $deny[] = 'contractors_index';
            }

            if (!$user->hasPermission(Permissions::DISBURSEMENT_VIEW)) {
                $deny[] = 'transactions_disbursement';
            }

            if (!$user->hasPermission(Permissions::RESERVE_ACCOUNT_CARRIER_VIEW) && !$user->hasPermission(
                Permissions::RESERVE_ACCOUNT_VENDOR_VIEW
            )) {
                $deny[] = 'reserve_accountcarriervendor';
                $deny[] = 'reserve_accountcarriervendor_new';
            }

            if (!$user->hasPermission(Permissions::RESERVE_ACCOUNT_CARRIER_MANAGE) && !$user->hasPermission(
                Permissions::RESERVE_ACCOUNT_VENDOR_MANAGE
            )) {
                $deny[] = 'reserve_accountcarriervendor_new';
            }

            if (!$user->hasPermission(Permissions::VENDOR_VIEW)) {
                $deny[] = 'vendors_index';
            }

            if (!$user->hasPermission(Permissions::REPORTING_GENERAL) && !$user->hasPermission(
                Permissions::REPORTING_ACH_CHECK
            ) && !$user->hasPermission(Permissions::REPORTING_SETTLEMENT_RECONCILIATION) && !$user->hasPermission(
                Permissions::REPORTING_DEDUCTION_REMITTANCE_FILE
            )) {
                $deny[] = 'reporting_index';
            }

            if (!$user->hasPermission(Permissions::PERMISSIONS_MANAGE)) {
                $deny[] = 'users_index';
            }

            $deny = array_unique($deny);

            $acl->deny(UserRoles::MANAGER_ROLE_ID, $deny);

            foreach ($deny as $id) {
                $page = $navigation->findOneBy('id', $id);
                if ($page) {
                    $navigation->removePageRecursive($page);
                }
            }
        }
        if ($user->isOnboarding()) {
            self::applyVendorPermissions($acl);
        }
    }

    public static function applyVendorPermissions($acl)
    {
        $user = User::getCurrentUser();
        /** @var $navigation My_Navigation */
        $navigation = Zend_Registry::get('Zend_Navigation');
        $deny = [];

        if ($user->isOnboarding()) {
            if (!$user->hasPermission(Permissions::RESERVE_ACCOUNT_VENDOR_VIEW)) {
                $deny[] = 'reserve_accountcarriervendor';
            }

            if (!$user->hasPermission(Permissions::RESERVE_ACCOUNT_VENDOR_MANAGE) || !$user->hasPermission(
                Permissions::RESERVE_ACCOUNT_VENDOR_VIEW
            )) {
                $deny[] = 'reserve_accountcarriervendor_new';
            }

            if (!$user->hasPermission(Permissions::RESERVE_ACCOUNT_CONTRACTOR_VIEW)) {
                $deny[] = 'reserve_accountcontractor';
            }

            if (!$user->hasPermission(Permissions::RESERVE_TRANSACTION_VENDOR_VIEW)) {
                $deny[] = 'reserve_transactions';
            }

            if (!$user->hasPermission(Permissions::RESERVE_TRANSACTION_VENDOR_VIEW) && !$user->hasPermission(
                Permissions::RESERVE_ACCOUNT_CONTRACTOR_VIEW
            ) && !$user->hasPermission(Permissions::RESERVE_ACCOUNT_VENDOR_VIEW) && !$user->hasPermission(
                Permissions::RESERVE_ACCOUNT_VENDOR_MANAGE
            )

            ) {
                $deny[] = 'transactions_index';
            }

            if (!$user->hasPermission(Permissions::TEMPLATE_MANAGE)) {
                $deny[] = 'deductions_setup_new';
            }

            if (!$user->hasPermission(Permissions::TEMPLATE_VIEW)) {
                $deny[] = 'deductions_setup';
            }

            if (!$user->hasPermission(Permissions::UPLOADING)) {
                $deny[] = 'file_index';
            }

            if (!$user->hasPermission(Permissions::VENDOR_VIEW)) {
                $deny[] = 'vendors_index';
            }

            if (!$user->hasPermission(Permissions::REPORTING_GENERAL) && !$user->hasPermission(
                Permissions::REPORTING_DEDUCTION_REMITTANCE_FILE
            )) {
                $deny[] = 'reporting_index';
            }

            $deny = array_unique($deny);

            $acl->deny(UserRoles::ONBOARDING_ROLE_ID, $deny);

            foreach ($deny as $id) {
                $page = $navigation->findOneBy('id', $id);
                if ($page) {
                    $navigation->removePageRecursive($page);
                }
            }
        }
    }
}

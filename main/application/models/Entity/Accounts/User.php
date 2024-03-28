<?php

use Application_Model_Entity_Entity_Carrier as Carrier;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_Entity_UserAuthProviders as UserAuthProviders;
use Application_Model_Entity_Entity_Vendor as Vendor;
use Application_Model_Entity_Settlement_Group as SettlementGroup;
use Application_Model_Rest as Rest;

/**
 * @method $this staticLoad($id, $field = null)
 */
class Application_Model_Entity_Accounts_User extends Application_Model_Base_Entity
{
    use Application_Model_ContactTrait;

    public static $loadBy = 'user_id';
    protected $_titleColumn = 'name';
    protected $_carrier;
    protected $_settlementGroup;
    protected $_cycle;
    protected $permissions;
    final public const USER_GROUP_REGULAR_ID = 5;
    final public const USER_GROUP_ADMIN_ID = 1;
    final public const USER_DEFAULT_STATUS = 1;
    final public const SYSTEM_USER = -1;

    /**
     * @static
     * @return Application_Model_Entity_Accounts_User
     */
    public static function getCurrentUser()
    {
        if ($user = Zend_Auth::getInstance()->getIdentity()) {
            return $user;
        }

        return new self();
    }

    /**
     * @return Application_Model_Entity_Entity_Contractor
     */
    public function getCurrentContractor()
    {
        $contractor = new Contractor();
        $contractor->load($this->getData('last_selected_contractor'));

        return $contractor;
    }

    /**
     * @return Application_Model_Entity_Settlement_Cycle|null
     */
    public function getCurrentCycle()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $cycleId = $request->getCookie('settlement_cycle_id');
        if (isset($this->_cycle) && $this->_cycle->getId() == $cycleId && $this->_cycle->getId() != null) {
            return $this->_cycle;
        }

        $cycleEntity = new Application_Model_Entity_Settlement_Cycle();
        $cycle = $cycleEntity->getCollection()->addCarrierFilter()->addSettlementGroupFilter()->getActiveCycle($cycleId);

        return $this->_cycle = $cycle;
    }

    public function setSettlementCycle($cycle)
    {
        if ($cycle instanceof Application_Model_Entity_Settlement_Cycle) {
            $id = $cycle->getId();
        } else {
            $id = null;
            $this->_cycle = $id;
        }

        setcookie('settlement_cycle_id', (string) $id, ['expires' => time() + 31_536_020, 'path' => '/']);

        return $this;
    }

    public function getLastSelectedContractor()
    {
        return null;
    }

    public function updateIp()
    {
        $this->setLastLoginIp(self::getRealIp());
        $this->save();
    }

    public static function getRealIp()
    {
        //check ip from share internet
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //to check ip is pass from proxy
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        if (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return null;
    }

    public static function encodePassword($password)
    {
        return md5((string) $password);
    }

    public function load($id, $field = null)
    {
        parent::load($id, $field);
        $this->setPassword('');
        $this->setDataChanges(false);

        return $this;
    }

    public function resetCarrier()
    {
        $this->_carrier = null;
        $this->setCredentials($this->getCredentials($this->getCarrierEntityId()));
        unset($this->gridData);

        return $this;
    }

    public function resetSettlementGroup()
    {
        $this->_settlementGroup = null;
        unset($this->gridData);

        return $this;
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();

        if ($this->getPassword()) {
            $this->setPassword(self::encodePassword($this->getPassword()));
        } else {
            $this->unsPassword();
        }

        if (!$this->getGroup()) {
            $this->setGroup(self::USER_GROUP_REGULAR_ID);
        }

        if (!$this->getStatus()) {
            $this->setStatus(self::USER_DEFAULT_STATUS);
        }

        if ($this->getEntityId() != null) {
            if (!$this->isAdmin()) {
                $entityId = $this->getEntityId();
                $entityEntity = new Application_Model_Entity_Entity();
                switch ($entityEntity->load($entityId)->getEntityTypeId()) {
                    case Application_Model_Entity_Entity_Type::TYPE_CARRIER:
                        $this->setRoleId(
                            Application_Model_Entity_System_UserRoles::CARRIER_ROLE_ID
                        );
                        break;
                    case Application_Model_Entity_Entity_Type::TYPE_CONTRACTOR:
                        $this->setRoleId(
                            Application_Model_Entity_System_UserRoles::CONTRACTOR_ROLE_ID
                        );
                        break;
                    case Application_Model_Entity_Entity_Type::TYPE_VENDOR:
                        $this->setRoleId(
                            Application_Model_Entity_System_UserRoles::VENDOR_ROLE_ID
                        );
                        break;
                }
            }
        }

        if ($this->getDeleted() == Application_Model_Entity_System_SystemValues::DELETED_STATUS) {
            $this->setEmail(null)->setPassword('');
            $authProvider = (new UserAuthProviders())->getByUserId($this->getId());
            if (!$authProvider->isEmpty()) {
                $authProvider->setProviderId($authProvider->getProviderId()  . time());
                $authProvider->save();
            }
        }

        return $this;
    }

    public function save(bool $isSsoAuth = false)
    {
        $secret = $this->getSecret();
        $isNew = !$this->getId();
        $md5 = '';
        if (strlen((string) $this->getPassword())) {
            $md5 = md5((string) $this->getPassword());
        }
        parent::save();
        if ($secret) {
            $this->setSecret($secret);
        }
        if ($isNew) {
            $this->setPassword($md5);
            if (!$isSsoAuth) {
                $this->createRestData();
            }
        }

        return $this;
    }

    protected function _afterSave()
    {
        if ($this->getId() == Application_Model_Entity_Accounts_User::getCurrentUser()->getId()) {
            Zend_Auth::getInstance()->getIdentity()->addData($this->getData());
        }

        return $this;
    }

    public function setCredentials($data)
    {
        if (php_sapi_name() === 'cli') {
            Zend_Registry::set('credentials', $data);
        } else {
            Zend_Auth::getInstance()->getIdentity()->setSecret($data['secret']);
            setcookie('token', (string) $data['token'], ['expires' => time() + 31_556_926, 'path' => '/']);
        }
    }

    public function getCredentials($carrierId = false): array
    {
        if (php_sapi_name() === 'cli') {
            return Zend_Registry::get('credentials');
        }
        if ($carrierId) {
            $rest = new Rest();
            $credentials = $rest->getCarrierKey($carrierId);
            if (!$credentials) {
                throw new Exception('Unable to pull Carrier Key.');
            }

            return $credentials['carrier_key'] ?? [];
        }
        $credentials = [];
        $identity = Zend_Auth::getInstance()->getIdentity();
        if (!$identity instanceof Application_Model_Entity_Accounts_User) {
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            $redirector->goToUrl('/auth/login');

            return [];
        }
        $secret = $identity->getSecret();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $token = null;
        if ($request) {
            $token = $request->getCookie('token');
        } elseif (isset($_COOKIE['token'])) {
            $token = $_COOKIE['token'];
        }

        if ($token && $secret) {
            $credentials = [
                'token' => $token,
                'secret' => $secret,
            ];
        }

        return $credentials;
    }

    public function getCarrierKey($carrierId = false)
    {
        $credentials = $this->getCredentials($carrierId);

        return (new Application_Model_Base_CryptAdvanced())->decrypt($credentials['token'], $credentials['secret']);
    }

    /**
     * @throws AccessDeniedException
     * @throws JsonException
     */
    public function authUser(string $login, string $password): ?self
    {
        $user = new self();
        $user->load($login, 'email');
        if ($id = $user->getId()) {
            $restService = new Rest();
            $carrierEntityId = $user->getCarrierEntityId();
            if ($data = $restService->login($id, md5($password), $carrierEntityId)) {
                if ($user::login($id) && isset($data['credentials'])) {
                    $user->setCredentials($data['credentials']);

                    return $user;
                }
            }
        }

        return null;
    }

    public static function logout()
    {
        Zend_Auth::getInstance()->clearIdentity();
    }

    /**
     * @static
     * @param $id int
     * @return Application_Model_Entity_Accounts_User|null
     */
    public static function login($id)
    {
        setcookie('settlement_cycle_id', '', ['expires' => time() - 3600, 'path' => '/']);
        $user = new self();
        $user->load($id);

        if ($user->getId()) {
            if (!$user->isAdmin()) {
                if ($entity = $user->getRelatedEntity()) {
                    if ($entity->getDeleted() == 1) {
                        return null;
                    }
                    if ($user->isContractor() || $user->isVendor()) {
                        if ($user->getSelectedCarrier()->getEntity()->getDeleted() == 1) {
                            return null;
                        }
                    }
                } else {
                    return null;
                }
            }
            $auth = Zend_Auth::getInstance();
            $auth->getStorage()->write($user);

            $user->updateIp();

            return $user;
        }

        return null;
    }

    /**
     * @static
     * @param $data
     * @return Application_Model_Entity_Accounts_User
     */
    public static function registration($data)
    {
        $user = new self();
        $user->setData($data);
        $user->save();

        return $user;
    }

    public function isLoggedIn()
    {
        return $this->getId();
    }

    public function getUserRoleID()
    {
        return $this->getRoleId();
    }

    /**
     * @return Application_Model_Base_Entity
     * |Application_Model_Entity_Entity_Contractor
     * |Application_Model_Entity_Entity_Vendor
     */
    public function getEntity()
    {
        switch ($this->getUserRoleID()) {
            case Application_Model_Entity_System_UserRoles::SUPER_ADMIN_ROLE_ID:
            case Application_Model_Entity_System_UserRoles::MODERATOR_ROLE_ID:
            case Application_Model_Entity_System_UserRoles::CARRIER_ROLE_ID:
                return $this->getSelectedCarrier();
            case Application_Model_Entity_System_UserRoles::CONTRACTOR_ROLE_ID:
                $entity = $this->getRelatedEntity();
                if ($entity->getId()) {
                    return $entity->getEntityByType();
                }

                return new Contractor();
            case Application_Model_Entity_System_UserRoles::VENDOR_ROLE_ID:
                $entity = $this->getRelatedEntity();
                if ($entity->getId()) {
                    return $entity->getEntityByType();
                }

                return new Vendor();
            default:
                return new Contractor(); //todo fix it for super admin
        }
    }

    public function getRelatedEntity()
    {
        return (new Application_Model_Entity_Entity())->load($this->getEntityId());
    }

    /**
     * @return Application_Model_Entity_Accounts_User
     */
    public function getDefaultValues()
    {
        $entityEntity = new Application_Model_Entity_Entity();
        $entityEntity->load($this->getEntityId());
        $entityId = $entityEntity->getId();
        $entity = $this->getEntity()->load($entityId, 'entity_id');
        $entityIdTitle = $entity->getData($entity->getTitleColumn());

        if ($this->getRoleId() == Application_Model_Entity_System_UserRoles::SUPER_ADMIN_ROLE_ID) {
            $this->setSuperAdminRole(1);//todo fix harcode value
        }

        $this->setEntityId($entityId);
        $this->setEntityIdTitle($entityIdTitle);

        return $this;
    }

    /**
     * @return Application_Model_Entity_Entity_Carrier|null
     */
    public function getSelectedCarrier()
    {
        if (isset($this->_carrier)) {
            return $this->_carrier;
        }

        $carrier = new Carrier();
        if ($this->getId()) {
            $user = $this;
        } else {
            $user = self::getCurrentUser();
        }
        if ($user->isVendor()) {
            $carrier->load($user->getEntity()->getCarrierId(), 'entity_id');
        } elseif ($user->isCarrier()) {
            $entity = $user->getRelatedEntity();
            if ($entity->getId()) {
                $carrier = $entity->getEntityByType();
            } else {
                $carrier = (new Carrier());
            }
        } elseif ($user->isContractor()) {
            $carrierId = (new Contractor())->load(
                $user->getEntityId(),
                'entity_id'
            )->getCarrierId();
            if ($carrierId) {
                $carrier->load($carrierId, 'entity_id');
            }
        } else {
            $carrierId = $user->getLastSelectedCarrier();
            if ($carrierId) {
                $carrier->load($carrierId);
            }
        }
        $this->_carrier = $carrier;

        return $carrier;
    }

    /**
     * @return SettlementGroup|null
     */
    public function getSelectedSettlementGroup()
    {
        if (isset($this->_settlementGroup)) {
            return $this->_settlementGroup;
        }

        $settlementGroup = new SettlementGroup();
        if ($this->getId()) {
            $user = $this;
        } else {
            $user = self::getCurrentUser();
        }
        $settlementGroupId = $user->getLastSelectedSettlementGroup();
        if ($settlementGroupId) {
            $settlementGroup->load($settlementGroupId);
        }
        $this->_settlementGroup = $settlementGroup;

        return $settlementGroup;
    }

    /**
     * @return Application_Model_Entity_Entity_Contractor|null
     */
    public static function getSelectedContractor()
    {
        $contractor = new Contractor();
        $contractorId = self::getCurrentUser()->getLastSelectedContractor();
        if ($contractorId) {
            $contractor->load($contractorId);

            return $contractor;
        }

        return null;
    }

    /**
     * @return Application_Model_Entity_Entity|null
     */
    public static function getSelectedEntity()
    {
        if (self::getSelectedContractor()) {
            return self::getSelectedContractor()->getEntity();
        }

        if (self::getSelectedCarrier()) {
            return self::getSelectedCarrier()->getEntity();
        }

        return null;
    }

    public function getAllContacts()
    {
        return array_merge(
            $this->getContacts(Application_Model_Entity_Entity_Contact_Type::TYPE_ADDRESS),
            $this->getContacts(Application_Model_Entity_Entity_Contact_Type::TYPE_HOME_PHONE),
            $this->getContacts(Application_Model_Entity_Entity_Contact_Type::TYPE_FAX)
        );
    }

    public function isAdmin($checkSuperAdmin = false)
    {
        if ($checkSuperAdmin) {
            return $this->isSuperAdmin();
        }

        return $this->isSuperAdmin() || $this->isModerator();
    }

    public function isModerator()
    {
        return $this->getUserRoleID() == Application_Model_Entity_System_UserRoles::MODERATOR_ROLE_ID;
    }

    public function isSuperAdmin()
    {
        return $this->getUserRoleID() == Application_Model_Entity_System_UserRoles::SUPER_ADMIN_ROLE_ID;
    }

    public function isVendor()
    {
        return $this->getUserRoleID() == Application_Model_Entity_System_UserRoles::VENDOR_ROLE_ID;
    }

    public function isCarrier()
    {
        return $this->getUserRoleID() == Application_Model_Entity_System_UserRoles::CARRIER_ROLE_ID;
    }

    public function isContractor()
    {
        return $this->getUserRoleID() == Application_Model_Entity_System_UserRoles::CONTRACTOR_ROLE_ID;
    }

    public function isGuest(): bool
    {
        return $this->getUserRoleID() == Application_Model_Entity_System_UserRoles::GUEST_ROLE_ID;
    }

    /**
     * return entityId if user admin or carrier
     *
     * @return bool|int
     */
    public function getCarrierEntityId()
    {
        if ($this->isCarrier()) {
            return $this->getEntityId();
        }

        if ($this->isAdmin()) {
            return $this->getSelectedCarrier()->getEntityId();
        }

        if ($this->isContractor()) {
            $entityIds = $this->getAssociatedEntityCollection()->getField('entity_id');
            if (!in_array($this->getEntityId(), $entityIds)) {
                $this->setEntityId(array_pop($entityIds));
                $this->save();
            }

            return Contractor::staticLoad($this->getEntityId(), 'entity_id')->getCarrierId();
        }

        if ($this->isVendor()) {
            $entityIds = $this->getAssociatedEntityCollection()->getField('entity_id');
            if (!in_array($this->getEntityId(), $entityIds)) {
                $this->setEntityId(array_pop($entityIds));
                $this->save();
            }

            return Vendor::staticLoad($this->getEntityId(), 'entity_id')->getCarrierId();
        }

        return false;
    }

    /**
     * return entityId if user vendor
     *
     * @return bool|int
     */
    public function getVendorEntityId()
    {
        if ($this->isVendor()) {
            return $this->getEntityId();
        }

        return false;
    }

    public function checkOldPassword()
    {
        $restService = new Rest();

        return $restService->checkPassword($this->getId(), md5((string) $this->getOldPassword()), $this->getCarrierEntityId());
    }

    /**
     * @return bool
     */
    public function checkPermissions()
    {
        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCarrier() && $user->hasPermission(Permissions::PERMISSIONS_MANAGE)) {
            if (is_countable($user->getCollection()->addNonDeletedFilter()->addCarrierFilter()->addFilter(
                'id',
                $this->getId()
            )->getField('id')) ? count(
                $user->getCollection()->addNonDeletedFilter()->addCarrierFilter()->addFilter(
                    'id',
                    $this->getId()
                )->getField('id')
            ) : 0) {
                if ($user->hasPermission(Permissions::VENDOR_USER_CREATE) || $user->hasPermission(
                    Permissions::CONTRACTOR_USER_CREATE
                )) {
                    return true;
                }
            }
        }
        if ($this->getEntityId() == $user->getEntityId()) {
            return true;
        }

        return false;
    }

    public function reloadCycle()
    {
        $this->_cycle = null;

        return $this;
    }

    /**
     * @return Application_Model_Entity_Entity_Permissions
     */
    public function getPermissions()
    {
        if (!isset($this->permissions)) {
            $this->setPermissions((new Permissions())->load($this->getId(), 'user_id'));
            if (!$this->permissions->getId() && ($this->isCarrier() || $this->isVendor())) {
                $this->permissions->setData(['user_id' => $this->getId()]);
                $this->permissions->save();
                $this->permissions->load($this->permissions->getId());
            }
        }

        return $this->permissions;
    }

    public function setPermissions(Permissions $permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * @param $permissionName
     * @return bool
     */
    public function hasPermission($permissionName)
    {
        $currentUser = Application_Model_Entity_Accounts_User::getCurrentUser();
        if (!$currentUser->isCarrier()) {
            if ($currentUser->isVendor()) {
                if (in_array($permissionName, Permissions::getVendorPermissions())) {
                    return (bool)(int)$this->getPermissions()->getData($permissionName);
                }
            } elseif ($currentUser->isModerator() && in_array($permissionName, ['carrier_view', 'carrier_edit'])) {
                return false;
            }

            return true;
        }

        if (!in_array($permissionName, Permissions::getVendorUniquePermissions())) {
            return (bool)(int)$this->getPermissions()->getData($permissionName);
        }

        return true;
    }

    /**
     * Uses by ContactTrait
     *
     * @return string
     */
    public function getLoadBy()
    {
        if (!$this->getUserId()) {
            $this->setUserId($this->getId());
        }

        return 'user_id';
    }

    public static function forgotPassword($email)
    {
        $user = self::staticLoad($email, 'email');
        $rest = new Rest();
        $hash = $rest->getUserResetPasswordHash($user->getId());

        //        $hash = md5($email) . $user->getPassword();
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');

        $resetLink = sprintf(
            "To reset you password, please, click <a href='%s%s%s%s%s'>here</a>.",
            $view->hostUrl() . Zend_Controller_Front::getInstance()->getBaseUrl(),
            '/auth/reset/email/',
            $email,
            '/hash/',
            $hash
        );

        //        $resetLink = 'To reset you password, please, click <a href="http:/p-fleet.loc/auth/reset/email/' . $email . '/hash/' . $hash .'">here</a>.';
        return Application_Plugin_Mail::sendMail($email, $resetLink, 'Reset Password');
    }

    public static function checkHash($email, $userHash)
    {
        $result = false;
        $user = self::staticLoad($email, 'email');
        if ($user->getId()) {
            $rest = new Rest();
            $hash = $rest->getUserResetPasswordHash($user->getId());
            if ($hash === $userHash) {
                $result = true;
            }
        }

        return $result;
    }

    public static function resetPassword($email, $userHash, $newPassword)
    {
        $result = false;
        $user = self::staticLoad($email, 'email');
        if (self::checkHash($email, $userHash)) {
            $rest = new Rest();
            $rest->updatePassword($user->getId(), $userHash, md5((string) $newPassword));
            Application_Plugin_Mail::sendMail(
                $email,
                'Password has been successfully changed. Now you can login via new password.',
                'New Password'
            );
            $result = true;
        }

        return $result;
    }

    /**
     * @return null|int
     */
    public function getAssociatedCarrierId()
    {
        $carrierId = null;

        if ($this->isCarrier()) {
            $carrierId = $this->getEntityId();
        }

        if ($this->isVendor()) {
            $carrierId = Vendor::staticLoad($this->getEntityId(), 'entity_id')->getCarrierId();
        }

        if ($this->isContractor()) {
            $carrierId = Contractor::staticLoad($this->getEntityId(), 'entity_id')->getCarrierId();
        }

        return $carrierId;
    }

    public function updateRestData()
    {
        $data = [
            'id' => $this->getId(),
            'role_id' => $this->getRoleId(),
            'carrier_id' => $this->getAssociatedCarrierId(),
            'carriers' => array_unique($this->getAssociatedCarriersId()),
        ];

        if ($this->getNewPassword()) {
            $data['password'] = md5((string) $this->getNewPassword());
        }
        $restService = new Rest();
        $restService->updateUser($data);

        return $this;
    }

    public function createRestData()
    {
        $data = [
            'id' => $this->getId(),
            'role_id' => $this->getRoleId(),
            'carrier_id' => $this->getAssociatedCarrierId(),
            'password' => $this->getPassword(),
            'carriers' => array_unique($this->getAssociatedCarriersId()),
        ];
        $restService = new Rest();
        $restService->createUser($data);

        return $this;
    }

    public function createSsoRestData()
    {
        $data = [
            'id' => $this->getId(),
            'role_id' => $this->getRoleId(),
            'carrier_id' => $this->getAssociatedCarrierId(),
            'password' => $this->getPassword(),
            'carriers' => array_unique($this->getAssociatedCarriersId()),
        ];
        $restService = new Rest();
        $restService->createUserSso($data);

        return $this;
    }

    public static function generatePassword($length = 8)
    {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = [];
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $length; $i++) {
            $n = random_int(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode('', $pass);
    }

    public function getCarriersCollection()
    {
        if ($this->isContractor() || $this->isVendor()) {
            $userEntity = new Application_Model_Entity_Accounts_UserEntity();
            $userEntityCollection = $userEntity->getCollection()->addFilter('user_id', $this->getId());
            if ($userEntityCollection->count()) {
                if ($this->isContractor()) {
                    $entity = new Contractor();
                } else {
                    $entity = new Vendor();
                }
                $collection = $entity->getCollection()->addFilter(
                    'entity_id',
                    $userEntityCollection->getField('entity_id'),
                    'IN'
                );

                if ($collection->count()) {
                    $carrier = new Carrier();
                    $carrierCollection = $carrier->getCollection()->addFilter(
                        'entity_id',
                        $collection->getField('carrier_id'),
                        'IN'
                    );

                    return $carrierCollection;
                }
            }
        }

        return (new Carrier())->getCollection()->getEmptyCollection();
    }

    public function getEntityAssociatedWithCarrier($carrierEntityId)
    {
        $collection = $this->getAssociatedEntityCollection();
        $collection->addFilter('carrier_id', $carrierEntityId);
        if ($collection->count()) {
            $item = $collection->getFirstItem();

            return Application_Model_Entity_Entity::staticLoad($item->getEntityId());
        }

        return false;
    }

    public function getEntities()
    {
        $entities = [];
        if ($this->getId()) {
            $entities = (new Application_Model_Entity_Accounts_UserEntity())->getCollection()->addFilter(
                'user_id',
                $this->getId()
            )->addCarrierFilter()->getItems();
        }
        if (!(is_countable($entities) ? count($entities) : 0)) {
            $entities = [
                (new Application_Model_Entity_Accounts_UserEntity())->setData([
                    'user_id' => $this->getId(),
                    'entity_id_title' => '',
                    'entity_id' => '',
                ]),
            ];
        }

        return $entities;
    }

    /**
     * return array of carrier entity id
     *
     * @return array
     */
    public function getAssociatedCarriersId()
    {
        return $this->getAssociatedEntityCollection()->getField('carrier_id');
    }

    /**
     * return collection with joined table
     *
     * @return Application_Model_Entity_Collection_Accounts_UserEntity
     */
    public function getAssociatedEntityCollection()
    {
        /** @var Application_Model_Entity_Collection_Accounts_UserEntity $collection */
        $collection = (new Application_Model_Entity_Accounts_UserEntity())->getCollection();
        if ($this->isVendor()) {
            $collection->joinVendorTable();
        } elseif ($this->isContractor()) {
            $collection->joinContractorTable();
        } else {
            $collection->getEmptyCollection();
        }

        $collection->addFilter('user_id', $this->getId());

        return $collection;
    }

    public function getByEmail(string $email): Application_Model_Base_Object
    {
        return (new Application_Model_Entity_Accounts_User())
            ->getCollection()
            ->addFilter(
                'email',
                $email
            )
            ->getFirstItem();
    }
}

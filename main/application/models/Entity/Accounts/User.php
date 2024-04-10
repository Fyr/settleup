<?php

use Application_Model_Entity_Accounts_UserEntity as UserEntity;
use Application_Model_Entity_Entity_Carrier as Division;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_Entity_UserAuthProviders as UserAuthProviders;
use Application_Model_Entity_Entity_Vendor as Vendor;
use Application_Model_Entity_Settlement_Group as SettlementGroup;
use Application_Model_Entity_System_SystemValues as SystemValues;
use Application_Model_Entity_System_UserRoles as UserRoles;
use Application_Model_Rest as Rest;

/**
 * @method $this staticLoad($id, $field = null)
 * @method Application_Model_Entity_Collection_Accounts_User getCollection()
 * @method Application_Model_Resource_Accounts_User getResource()
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

    public static function getCurrentUser(): self
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

    public function resetCarrier(): self
    {
        $this->_carrier = null;

        return $this;
    }

    public function resetSettlementGroup(): self
    {
        $this->_settlementGroup = null;

        return $this;
    }

    protected function _beforeSave(): self
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

        if ($this->getDeleted() == SystemValues::DELETED_STATUS) {
            $this->markColumnAsDeleted($this->colEmail())->setPassword('');
            /** @var UserAuthProviders $authProvider */
            $authProvider = (new UserAuthProviders())->getByUserId($this->getId());
            if (!$authProvider->isEmpty()) {
                $authProvider->markColumnAsDeleted($authProvider->colProviderId());
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
        if ($this->getId() == self::getCurrentUser()->getId()) {
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
        if (!$identity instanceof self) {
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
     * @throws Zend_Auth_Storage_Exception
     */
    public static function login(int $id): ?self
    {
        setcookie('settlement_cycle_id', '', ['expires' => time() - 3600, 'path' => '/']);
        $user = new self();
        $user->load($id);

        if ($user->getId()) {
            if (!$user->isAdminOrSuperAdmin()) {
                if ($entity = $user->getRelatedEntity()) {
                    if ($entity->getDeleted() == 1) {
                        return null;
                    }
                    if ($user->isSpecialist() || $user->isOnboarding()) {
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

    public static function registration(array $data): self
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

    public function getEntity(): ?Division
    {
        return $this->getSelectedCarrier();
    }

    public function getRelatedEntity()
    {
        return (new Application_Model_Entity_Entity())->load($this->getEntityId());
    }

    public function getDefaultValues(): self
    {
        $entityEntity = new Application_Model_Entity_Entity();
        $entityEntity->load($this->getEntityId());
        $entityId = $entityEntity->getId();
        $entity = $this->getEntity()->load($entityId, 'entity_id');
        $entityIdTitle = $entity->getData($entity->getTitleColumn());

        if ($this->getRoleId() == UserRoles::SUPER_ADMIN_ROLE_ID) {
            $this->setSuperAdminRole(1);//todo fix harcode value
        }

        $this->setEntityId($entityId);
        $this->setEntityIdTitle($entityIdTitle);

        return $this;
    }

    public function getSelectedCarrier(): ?Division
    {
        if (isset($this->_carrier)) {
            return $this->_carrier;
        }

        $carrier = new Division();
        $user = $this->getId() ? $this : self::getCurrentUser();
        $carrierId = $user->getLastSelectedCarrier();
        if ($carrierId) {
            $carrier->load($carrierId);
        }
        $this->_carrier = $carrier;

        return $carrier;
    }

    public function getSelectedSettlementGroup(): ?SettlementGroup
    {
        if (isset($this->_settlementGroup)) {
            return $this->_settlementGroup;
        }

        $settlementGroup = new SettlementGroup();
        $user = $this->getId() ? $this : self::getCurrentUser();
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

    public function getAllContacts(): array
    {
        return array_merge(
            $this->getContacts(Application_Model_Entity_Entity_Contact_Type::TYPE_ADDRESS),
            $this->getContacts(Application_Model_Entity_Entity_Contact_Type::TYPE_HOME_PHONE),
            $this->getContacts(Application_Model_Entity_Entity_Contact_Type::TYPE_FAX)
        );
    }

    public function isAdminOrSuperAdmin(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    public function isSuperAdmin(): bool
    {
        return $this->getUserRoleID() == UserRoles::SUPER_ADMIN_ROLE_ID;
    }

    public function isAdmin(): bool
    {
        return $this->getUserRoleID() == UserRoles::ADMIN_ROLE_ID;
    }

    public function isManager(): bool
    {
        return $this->getUserRoleID() == UserRoles::MANAGER_ROLE_ID;
    }

    public function isSpecialist(): bool
    {
        return $this->getUserRoleID() == UserRoles::SPECIALIST_ROLE_ID;
    }

    public function isOnboarding(): bool
    {
        return $this->getUserRoleID() == UserRoles::ONBOARDING_ROLE_ID;
    }

    public function isGuest(): bool
    {
        return $this->getUserRoleID() == UserRoles::GUEST_ROLE_ID;
    }

    public function getCarrierEntityId(): ?int
    {
        $lastSelectedEntityId = $this->getSelectedCarrier()->getEntityId();
        if ($this->isAdminOrSuperAdmin()) {
            return $lastSelectedEntityId;
        }

        if (!$lastSelectedEntityId) {
            $entityIds = $this->getAssociatedCarrierIds();
            if (!in_array($this->getEntityId(), $entityIds)) {
                $this->setEntityId(array_pop($entityIds));
                $this->save();
            }

            return $this->getEntityId();
        }

        return $lastSelectedEntityId;
    }

    /**
     * return entityId if user vendor
     *
     * @return bool|int
     */
    public function getVendorEntityId()
    {
        if ($this->isOnboarding()) {
            return $this->getEntityId();
        }

        return false;
    }

    public function checkOldPassword()
    {
        $restService = new Rest();

        return $restService->checkPassword($this->getId(), md5((string) $this->getOldPassword()), $this->getCarrierEntityId());
    }

    public function checkPermissions(): bool
    {
        $user = self::getCurrentUser();
        if ($user->isAdminOrSuperAdmin()) {
            return true;
        }

        if ($user->isManager() && $user->hasPermission(Permissions::PERMISSIONS_MANAGE)) {
            return true;
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
            if (!$this->permissions->getId() && ($this->isManager() || $this->isOnboarding())) {
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
        $currentUser = self::getCurrentUser();
        if (!$currentUser->isManager()) {
            if ($currentUser->isOnboarding()) {
                if (in_array($permissionName, Permissions::getVendorPermissions())) {
                    return (bool)(int)$this->getPermissions()->getData($permissionName);
                }
            } elseif ($currentUser->isAdmin() && in_array($permissionName, ['carrier_view', 'carrier_edit'])) {
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

    public function getAssociatedCarrierId(): ?int
    {
        $carrierId = null;
        if ($this->isManager() || $this->isSpecialist() || $this->isOnboarding()) {
            $carrierId = $this->getEntityId();
        }

        return $carrierId;
    }

    public function updateRestData()
    {
        $data = [
            'id' => $this->getId(),
            'role_id' => $this->getRoleId(),
            'carrier_id' => $this->getAssociatedCarrierId(),
            'carriers' => array_unique($this->getAssociatedCarrierIds()),
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
            'carriers' => array_unique($this->getAssociatedCarrierIds()),
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
            'carriers' => array_unique($this->getAssociatedCarrierIds()),
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
        if ($this->isSpecialist() || $this->isOnboarding()) {
            $userEntity = new UserEntity();
            $userEntityCollection = $userEntity->getCollection()->addFilter('user_id', $this->getId());
            if ($userEntityCollection->count()) {
                if ($this->isSpecialist()) {
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
                    $carrier = new Division();
                    $carrierCollection = $carrier->getCollection()->addFilter(
                        'entity_id',
                        $collection->getField('carrier_id'),
                        'IN'
                    );

                    return $carrierCollection;
                }
            }
        }

        return (new Division())->getCollection()->getEmptyCollection();
    }

    public function getEntities(): array
    {
        $entities = [];
        if ($this->getId()) {
            $entities = (new UserEntity())
                ->getCollection()
                ->addFilter('user_id', $this->getId())
                ->getItems();
        }
        if (!(is_countable($entities) ? count($entities) : 0)) {
            $entities = [
                (new UserEntity())->setData([
                    'user_id' => $this->getId(),
                    'entity_id_title' => '',
                    'entity_id' => '',
                ]),
            ];
        }

        return $entities;
    }

    public function getAssociatedCarrierIds(): array
    {
        return (new UserEntity())
            ->getCollection()
            ->addFilter('user_id', $this->getId())
            ->getField('entity_id');
    }

    public function getByEmail(string $email): Application_Model_Base_Object
    {
        return (new self())
            ->getCollection()
            ->addFilter('email', $email)
            ->getFirstItem();
    }

    public function checkLastSelectedDivision(): void
    {
        $needResetLastSelectedDivision = false;
        $selectedDivision = $this->getSelectedCarrier();
        if (!$this->isAdminOrSuperAdmin()) {
            $entityIds = (new UserEntity())
                ->getCollection()
                ->addFilterByUserId($this->getId())
                ->getField('entity_id');
            if (!in_array($selectedDivision->getEntityId(), $entityIds)) {
                $needResetLastSelectedDivision = true;
            }
        }
        $entity = $selectedDivision->getEntity();
        if (SystemValues::DELETED_STATUS === (int) $entity->getDeleted()) {
            $needResetLastSelectedDivision = true;
        }
        if ($needResetLastSelectedDivision) {
            $this->setLastSelectedSettlementGroup()
                ->setLastSelectedCarrier()
                ->save();
        }
    }
}

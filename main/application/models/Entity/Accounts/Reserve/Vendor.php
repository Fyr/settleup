<?php

class Application_Model_Entity_Accounts_Reserve_Vendor extends Application_Model_Base_Entity
{
    /**
     * @var string  - Field used as the title for this model
     */
    protected $_titleColumn = 'account_name';

    /**
     * @return Application_Model_Entity_Accounts_Reserve_Vendor
     */
    public function save()
    {
        if (!$this->getId()) {
            $this->setPriority($this->getNewPriority());
        }
        $model = new Application_Model_Entity_Accounts_Reserve();
        $model->load($this->getReserveAccountId());
        $model->setData(
            array_merge(
                $this->getData(),
                ['id' => $this->getReserveAccountId()]
            )
        );
        $model->save();
        $this->setReserveAccountId($model->getId());
        if ($model->getData('min_balance') != $model->getOriginalData('min_balance')) {
            $this->updateRAC();
        }
        parent::save();

        return $this;
    }

    public function load($id, $field = null)
    {
        parent::load($id, $field);
        $basicModel = new Application_Model_Entity_Accounts_Reserve();
        $basicData = $basicModel->load($this->getReserveAccountId())->getData();
        $this->setData(array_merge($basicData, $this->getData()));

        return $this;
    }

    /**
     * @return Application_Model_Entity_Accounts_Reserve_Vendor
     */
    public function delete()
    {
        parent::delete();
        $model = new Application_Model_Entity_Accounts_Reserve();
        $model->load($this->getReserveAccountId());
        $model->delete();

        return $this;
    }

    /**
     * Adds to the dataset of model related titles for the form
     *
     * @return Application_Model_Entity_Accounts_Reserve_Vendor
     */
    public function getDefaultValues()
    {
        $this->setEntityIdTitle($this->_getEntityIdTitle());

        return $this;
    }

    public function getAccountTitle()
    {
        return $this->getData($this->getTitleColumn());
    }

    /**
     * Returns title by entity_id
     *
     *
     */
    protected function _getEntityIdTitle()
    {
        $entity = new Application_Model_Entity_Entity();
        $entity->load($this->getEntityId());
        if ($entity->getId()) {
            $this->setEntityTypeId($entity->getEntityTypeId());

            return $entity->getName();
        } else {
            return '';
        }
    }

    /**
     * Returns vendor collection in accordance with current user role
     *
     * @return Application_Model_Entity_Collection_Entity_Vendor
     */
    public function getVendorCollection()
    {
        $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();
        $vendorEntity = new Application_Model_Entity_Entity_Vendor();
        $vendorCollection = $vendorEntity->getCollection();

        if ($userEntity->getRoleId() == Application_Model_Entity_System_UserRoles::VENDOR_ROLE_ID) {
            $vendorCollection->addFilter(
                'entity_id',
                Application_Model_Entity_Entity::getCurrentEntity()->getId()
            );
        } else {
            $vendorCollection->addVisibilityFilterForUser();
        }

        return $vendorCollection;
    }

    public function getCarrierCollection()
    {
        $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();
        $carrierEntity = new Application_Model_Entity_Entity_Carrier();
        $vendorCollection = $carrierEntity->getCollection();

        if ($userEntity->getRoleId() == Application_Model_Entity_System_UserRoles::CARRIER_ROLE_ID) {
            $vendorCollection->addFilter(
                'entity_id',
                Application_Model_Entity_Entity::getCurrentEntity()->getId()
            );
        } else {
            $vendorCollection->addVisibilityFilterForUser();
        }

        return $vendorCollection;
    }

    /**
     * @return Application_Model_Entity_Accounts_Reserve
     */
    public function getReserveAccountEntity()
    {
        return (new Application_Model_Entity_Accounts_Reserve())->load($this->getReserveAccountId());
    }

    public function getNewPriority()
    {
        $reserveAccountCollection = (new Application_Model_Entity_Accounts_Reserve_Vendor())->getCollection(
        )->addCarrierVendorFilter(false, true)->addNonDeletedFilter()->setOrder('priority');
        $count = is_countable($reserveAccountCollection) ? count($reserveAccountCollection) : 0;
        if ($count) {
            return (int)$reserveAccountCollection->getFirstItem()->getPriority() + 1;
        } else {
            return 0;
        }
    }

    public function updateRAC()
    {
        $result = false;
        if ($this->getId()) {
            $reserveAccount = $this->getReserveAccountEntity();
            $sql = 'CALL updateRAC(?,?)';
            $stmt = $this->getResource()->getAdapter()->prepare($sql);
            $stmt->bindParam(1, $reserveAccount->getData('min_balance'));
            $stmt->bindParam(2, $this->getId());
            $result = $stmt->execute();
        }

        return $result;
    }

    public function reorderPriority()
    {
        $reserveAccountEntity = new Application_Model_Entity_Accounts_Reserve();

        $items = (new Application_Model_Entity_Accounts_Reserve_Vendor())->getCollection()->addNonDeletedFilter(
        )->addCarrierVendorFilter(false, true)->setOrder('priority', 'asc')->getItems();

        $items = array_values($items);

        foreach ($items as $priority => $item) {
            if ($item->getData('priority') !== (int)$priority) {
                $reserveAccountEntity->updatePriority($item->getReserveAccountId(), $priority);
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function checkPermissions($checkManagePermissions = true)
    {
        if ($this->getDeleted()) {
            return false;
        }
        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        if ($user->isAdmin()) {
            return true;
        }
        $carrierVendorEntity = $this->getReserveAccountEntity()->getEntity();
        if ($entityId = $user->getCarrierEntityId()) {
            if ($carrierVendorEntity->getEntityTypeId() == Application_Model_Entity_Entity_Type::TYPE_CARRIER) {
                if ($carrierVendorEntity->getId() == $entityId && $user->hasPermission(
                    Application_Model_Entity_Entity_Permissions::RESERVE_ACCOUNT_CARRIER_VIEW
                )) {
                    if ($checkManagePermissions && !$user->hasPermission(
                        Application_Model_Entity_Entity_Permissions::RESERVE_ACCOUNT_CARRIER_MANAGE
                    )) {
                        return false;
                    } else {
                        return true;
                    }
                }
            } elseif ($carrierVendorEntity->getEntityTypeId() == Application_Model_Entity_Entity_Type::TYPE_VENDOR) {
                $vendor = new Application_Model_Entity_Entity_Vendor();
                $vendor->load($carrierVendorEntity->getId(), 'entity_id');
                if ($vendor->getCarrierId() == $entityId && $user->hasPermission(
                    Application_Model_Entity_Entity_Permissions::RESERVE_ACCOUNT_VENDOR_VIEW
                )) {
                    if ($checkManagePermissions && !$user->hasPermission(
                        Application_Model_Entity_Entity_Permissions::RESERVE_ACCOUNT_VENDOR_MANAGE
                    )) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        } elseif ($entityId = $user->getVendorEntityId()) {
            if ($carrierVendorEntity->getId() == $entityId && $user->hasPermission(
                Application_Model_Entity_Entity_Permissions::RESERVE_ACCOUNT_VENDOR_VIEW
            )) {
                if ($checkManagePermissions && !$user->hasPermission(
                    Application_Model_Entity_Entity_Permissions::RESERVE_ACCOUNT_VENDOR_MANAGE
                )) {
                    return false;
                } else {
                    return true;
                }
            }
        }

        return false;
    }

    public function isDeletable()
    {
        $result = false;
        $carrierVendorEntity = $this->getReserveAccountEntity()->getEntity();
        if ($id = $this->getId()) {
            $sql = 'CALL getRAContractorCount(?)';
            $stmt = $this->getResource()->getAdapter()->prepare($sql);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            $result = $stmt->fetchAll();
            if (isset($result[0]['rac_count']) && isset($result[0]['deduction_setup_count'])) {
                $result = !(bool)($result[0]['rac_count'] + $result[0]['deduction_setup_count']);
            }
        }
        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        if (($carrierVendorEntity->isCarrier() && !$user->hasPermission(
            Application_Model_Entity_Entity_Permissions::RESERVE_ACCOUNT_CARRIER_MANAGE
        )) || ($carrierVendorEntity->isVendor() && !$user->hasPermission(
            Application_Model_Entity_Entity_Permissions::RESERVE_ACCOUNT_VENDOR_MANAGE
        ))) {
            $result = false;
        }

        return $result;
    }

    public function updateCurrentBalance()
    {
        $id = $this->getReserveAccountId();
        $sql = 'CALL updateReserveAccountVendorCurrentBalance(?)';
        $stmt = $this->getResource()->getAdapter()->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $this;
    }
}

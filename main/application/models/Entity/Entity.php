<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Carrier as Carrier;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Entity_Type as EntityType;
use Application_Model_Entity_Entity_Vendor as Vendor;

/**
 * @method $this staticLoad($id, $field = null)
 */
class Application_Model_Entity_Entity extends Application_Model_Base_Entity implements Stringable
{
    final public const EXCEPTION_MESSAGE = "Application_Model_Entity_Entity_Type error, this entity type not exist";
    protected $entity;

    public static function getCurrentEntity()
    {
        $currentUser = User::getCurrentUser();
        $obj = new self();
        $obj->load($currentUser->getEntityId());

        return $obj;
    }

    public function __toString(): string
    {
        return (string)$this->getId();
    }

    /**
     * return entity by type (contractor, vendor, carrier)
     *
     * @return Application_Model_Entity_Entity_Carrier
     * |Application_Model_Entity_Entity_Contractor|
     * Application_Model_Entity_Entity_Vendor
     * @throws Exception
     */
    public function getEntityByType()
    {
        if (!isset($this->entity) || $this->entity != $this->getId()) {
            $entity = match ((int)$this->getEntityTypeId()) {
                EntityType::TYPE_CARRIER => new Carrier(),
                EntityType::TYPE_CONTRACTOR => new Contractor(),
                EntityType::TYPE_VENDOR => new Vendor(),
                default => throw new Exception(self::EXCEPTION_MESSAGE),
            };
            $this->entity = $entity->load($this->getId(), 'entity_id');
        }

        return $this->entity;
    }

    /**
     * return Carrier, Vendor, Contractor
     *
     * @return string
     */
    public function getEntityTitle()
    {
        $entity = new EntityType();

        return $entity->load($this->getEntityTypeId())->getTitle();
    }

    /**
     * @return Application_Model_Entity_Collection_Entity_Contact_Info
     */
    public function getContactInfo()
    {
        $entity = new Application_Model_Entity_Entity_Contact_Info();
        $collection = $entity->getCollection();
        $collection->addFilter('entity_contact_info.entity_id', $this->getId());

        return $collection;
    }

    public function getCurrentContractor()
    {
        $carrierEntity = new Contractor();
        $contractorId = User::getCurrentUser()->getLastSelectedContractor();
        if ($contractorId) {
            return $carrierEntity->load(
                User::getCurrentUser()->getLastSelectedContractor()
            );
        }

        return null;
    }

    /**
     * return contractor's company name, vendor's name or carrier's name
     *
     * @param Application_Model_Entity_Entity $entity
     * @return string
     */
    public function getEntityName()
    {
        $currentEntity = $this->getEntityByType();

        return match ($this->getEntityTypeId()) {
            EntityType::TYPE_CONTRACTOR => $currentEntity->getCompanyName(),
            EntityType::TYPE_VENDOR => $currentEntity->getName(),
            EntityType::TYPE_CARRIER => $currentEntity->getName(),
            default => '',
        };
    }

    public function isContractor()
    {
        return ($this->getEntityTypeId() == EntityType::TYPE_CONTRACTOR);
    }

    public function isVendor()
    {
        return ($this->getEntityTypeId() == EntityType::TYPE_VENDOR);
    }

    public function isCarrier()
    {
        return (bool)($this->getEntityTypeId() == EntityType::TYPE_CARRIER);
    }
}

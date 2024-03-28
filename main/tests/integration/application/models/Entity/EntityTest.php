<?php

class EntityTest extends BaseTestCase
{
    protected $object;
    protected $entityId = "";
    protected $fakeType = "";

    protected function setUp(): void
    {
        // parent::setUp();
        // $this->object = new Application_Model_Entity_Entity();
        $itemId = $this->object->getCollection()
            ->getFirstItem()
            ->getId();
        $this->object->setId($itemId);
        $this->entityId = 2;
        $this->fakeType = 123;
    }

    public function testGetCurrentEntity()
    {
        Application_Model_Entity_Accounts_User::login(16);
        $entiry = Application_Model_Entity_Entity::getCurrentEntity();
        $this->assertTrue($entiry instanceof Application_Model_Entity_Entity);
    }

    public function test__toString()
    {
        $this->object->setId($this->entityId);
        $this->assertEquals((string)$this->entityId, (string)$this->object);
    }

    public function testGetBankAccounts()
    {
        $this->assertTrue(
            $this->object->getBankAccounts() instanceof Application_Model_Base_Collection
        );
    }

    public function testGetEntityByTypeCarrirer()
    {
        $this->object->setEntityTypeId(Application_Model_Entity_Entity_Type::TYPE_CARRIER);
        $this->assertTrue(
            $this->object->getEntityByType() instanceof Application_Model_Entity_Entity_Carrier
        );
    }

    public function testGetEntityByTypeContractor()
    {
        $this->object->setEntityTypeId(Application_Model_Entity_Entity_Type::TYPE_CONTRACTOR);
        $this->assertTrue(
            $this->object->getEntityByType() instanceof Application_Model_Entity_Entity_Contractor
        );
    }

    public function testGetEntityByTypeVendor()
    {
        $this->object->setEntityTypeId(Application_Model_Entity_Entity_Type::TYPE_VENDOR);
        $this->assertTrue(
            $this->object->getEntityByType() instanceof Application_Model_Entity_Entity_Vendor
        );
    }

    public function testGetEntityByTypeException()
    {
        $this->object->setEntityTypeId($this->fakeType);
        try {
            $this->object->getEntityByType();
        } catch (Exception $exc) {
            $this->assertEquals(
                Application_Model_Entity_Entity::EXCEPTION_MESSAGE,
                $exc->getMessage()
            );
        }
    }

    public function testGetEntityTitleForCarier()
    {
        $this->object->setEntityTypeId(Application_Model_Entity_Entity_Type::TYPE_CARRIER);
        $this->assertEquals('carrier', $this->object->getEntityTitle());
    }

    public function testGetEntityTitleForContractor()
    {
        $this->object->setEntityTypeId(Application_Model_Entity_Entity_Type::TYPE_CONTRACTOR);
        $this->assertEquals('contractor', $this->object->getEntityTitle());
    }

    public function testGetEntityTitleForVendor()
    {
        $this->object->setEntityTypeId(Application_Model_Entity_Entity_Type::TYPE_VENDOR);
        $this->assertEquals('vendor', $this->object->getEntityTitle());
    }

    public function testGetEntityTitleForFakeType()
    {
        $this->object->setEntityTypeId($this->fakeType);
        $this->assertEquals(null, $this->object->getEntityTitle());
    }

    public function testGetContactInfo()
    {
        $this->assertTrue(
            $this->object->getContactInfo() instanceof Application_Model_Entity_Collection_Entity_Contact_Info
        );
    }

    public function testGetCurrentContractorLogedInUser()
    {
        Application_Model_Entity_Accounts_User::login(16);
        $currentUser = Application_Model_Entity_Accounts_User::getCurrentUser();
        $contractor = new Application_Model_Entity_Entity_Contractor();
        $contractorId = $contractor->getCollection()
            ->getFirstItem()
            ->getId();
        $currentUser->setData('last_selected_contractor', $contractorId);

        //        $this->assertTrue($this->object->getCurrentContractor()
        //                instanceof Application_Model_Entity_Entity_Contractor);
    }

    public function testGetCurrentContractorNotLogedInUser()
    {
        $this->assertEquals(null, $this->object->getCurrentContractor());
    }
}

<?php

use Application_Model_Entity_Entity as Entity;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Entity_History as History;
use Application_Model_Entity_Settlement_Cycle as Cycle;

class Application_Model_Report_SaveHistoryService
{
    /** @var Application_Model_Entity_Settlement_Cycle */
    protected $cycle;
    /** @var Application_Model_Entity_Entity_History */
    protected $history;

    public function __construct(Cycle $cycle)
    {
        $this->cycle = $cycle;
        $this->history = new History($cycle);
    }

    public function save()
    {
        $contractors = $this->cycle->getSettlementContractors();
        foreach ($contractors as $contractor) {
            $this->history->setEntity($this->getContractorData($contractor['id']));
        }
        $vendors = $this->cycle->getSettlementVendors();
        foreach ($vendors as $vendor) {
            $this->history->setEntity($this->getVendorData($vendor['id']));
        }

        $this->history->setEntity($this->getCarrierData());

        return $this;
    }

    public function getCarrierData()
    {
        $carrierEntity = $this->cycle->getCarrier()->getEntity();
        $data = [
            'entity_id' => $carrierEntity->getId(),
            'name' => $carrierEntity->getName(),
            'address' => $carrierEntity->getContactInfo()->addAddressFilter(),
            'escrow_account' => $this->cycle->getCarrier()->getEscrowAccount(),
        ];

        return $data;
    }

    public function getVendorData($id)
    {
        $vendorEntity = (new Entity())->load($id);
        $data = [
            'entity_id' => $vendorEntity->getId(),
            'name' => $vendorEntity->getName(),
            'address' => $vendorEntity->getContactInfo()->addAddressFilter(),
        ];

        return $data;
    }

    public function getContractorData($id)
    {
        $contractor = new Contractor();
        $contractor->load($id, 'entity_id');

        $contractorEntity = $contractor->getEntity();

        $data = [
            'entity_id' => $contractorEntity->getId(),
            'name' => $contractorEntity->getName(),
            'company_name' => $contractorEntity->getName(),
            'first_name' => $contractor->getFirstName(),
            'last_name' => $contractor->getLastName(),
            'tax_id' => $contractor->getTaxId(),
            'division' => $contractor->getDivision(),
            'department' => $contractor->getDepartment(),
            'route' => $contractor->getRoute(),
            'address' => $contractorEntity->getContactInfo()->addAddressFilter(),
            'code' => $contractor->getCode(),
        ];

        return $data;
    }
}

<?php

use Application_Model_Entity_Entity_Carrier as Carrier;
use Application_Model_Entity_Entity_Contractor as Contractor;

/**
 * @method Application_Model_Entity_Collection_Powerunit_Powerunit getCollection()
 * @method Application_Model_Resource_Powerunit_Powerunit getResource()
 */
class Application_Model_Entity_Powerunit_Powerunit extends Application_Model_Base_Entity
{
    public function setContractorDataByCode()
    {
        $contractor = new Contractor();
        $selectedContractorCode = $this->getContractorCode();
        $selectedContractor = $contractor->getContractorByCode(
            $selectedContractorCode
        );
        $this->setContractorId($selectedContractor['entity_id']);
        $this->setContractorName($selectedContractor['company_name']);
    }

    public function setDatetimesToDbFormat($isEdit = false)
    {
        if (!$this->getData('start_date') && !$isEdit) {
            // Let database create default value
            $this->unsetData('start_date');
        } else {
            $this->changeDatetimeFormat(['start_date']);
        }
        $this->changeDatetimeFormat(['termination_date']);
    }

    public function setDatetimesFromDbFormat()
    {
        $this->changeDatetimeFormat(
            [
                'start_date',
                'termination_date',
            ],
            true,
            true
        );
    }

    public function createIndividualTemplates(): void
    {
        $contractorEntity = new Contractor();
        $contractorEntity->load($this->getContractorId(), 'entity_id');
        $contractorEntity->createIndividualTemplates();
    }

    public function createIndividualTemplatesByDivisionIds(array $divisionIds): void
    {
        foreach ($divisionIds as $divisionId) {
            $divisionEntity = new Carrier();
            $divisionEntity->load($divisionId, 'entity_id');
            if ($divisionEntity->isEmpty()) {
                $this->getLogger()->alert('Failed create Individual Templates. Division not found by entity_id: ' .
                    $divisionId);
                continue;
            }
            $this->getLogger()->info('Create Individual Templates for DivisionId: ' . $divisionId);
            $divisionEntity->createPaymentTemplates();
            $divisionEntity->createDeductionTemplates();
        }
    }
}

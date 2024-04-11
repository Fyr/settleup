<?php

/**
 * @method $this staticLoad($id, $field = null)
 * @method Application_Model_Entity_Collection_Settlement_Group getCollection()
 */
class Application_Model_Entity_Settlement_Group extends Application_Model_Base_Entity
{
    public function getByDivisionId(?int $divisionId): array
    {
        return (new self())
            ->getCollection()
            ->addFilter('division_id', $divisionId)
            ->addFilter('deleted', false)
            ->getItems();
    }

    public function getByDivisionIdAndSettlementGroupId(int $divisionId, int $settlementGroupId): Application_Model_Base_Object
    {
        return (new self())
            ->getCollection()
            ->addFilter('division_id', $divisionId)
            ->addFilter('deleted', false)
            ->addFilter('id', $settlementGroupId)
            ->getFirstItem();
    }

    public function getOptions()
    {
        // returns [(int)id => code]
        $settlementGroups = $this->getCollection()
            ->addNonDeletedFilter()
            ->addVisibilityFilterForUser()
            ->getItems()
        ;

        $settlementGroupOptions = [];
        foreach ($settlementGroups as $settlementGroup) {
            $settlementGroupOptions[$settlementGroup->getId()] = $settlementGroup->getCode();
        }

        return $settlementGroupOptions;
    }
}

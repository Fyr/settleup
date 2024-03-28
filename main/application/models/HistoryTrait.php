<?php

trait Application_Model_HistoryTrait
{
    protected $history;
    protected $historyCollection;

    /*---------------Single History-----------------------------------------------------------------------------------*/
    public function addDataFromHistory($targetEntity, $targetIdField = 'entity_id')
    {
        if ($entity = $this->getHistory()->getEntity($targetEntity->getData($targetIdField))) {
            $targetEntity->addData($entity);
        }

        return $targetEntity;
    }

    public function loadHistory($cycle = 0)
    {
        if (!($cycle instanceof Application_Model_Entity_Settlement_Cycle)) {
            $cycle = (new Application_Model_Entity_Settlement_Cycle())->load($cycle);
        }
        $this->history = new Application_Model_Entity_Entity_History($cycle);

        return $this->history;
    }

    /**
     * @return mixed
     */
    public function getHistory()
    {
        if (!isset($this->history)) {
            $this->loadHistory();
        }

        return $this->history;
    }

    /*---------------History Collections------------------------------------------------------------------------------*/
    public function addDataFromHistoryCollection($entity, $fieldAssociation)
    {
        foreach ($fieldAssociation as $idField => $association) {
            $id = $entity->getData($idField);
            if (isset($this->getHistoryCollection()[$id])) {
                $historyEntityData = $this->getHistoryCollection()[$id]->getEntityData();
                if ($historyEntityData) {
                    if (is_array($association)) {
                        foreach ($association as $field => $targetField) {
                            $historyKey = ((is_int($field)) ? $targetField : $field);
                            if (isset($historyEntityData[$historyKey])) {
                                $entity->setData($targetField, $historyEntityData[$historyKey]);
                            }
                        }
                    } else {
                        $entity->addData($historyEntityData);
                    }
                }
            }
        }

        return $this;
    }

    public function getHistoryCollection()
    {
        if (!isset($this->historyCollection)) {
            $this->historyCollection = $this->getHistory()->getCollection()->addFilter(
                'cycle_id',
                $this->getHistory()->getCycle()->getId()
            )->getItems('entity_id');
        }

        return $this->historyCollection;
    }
}

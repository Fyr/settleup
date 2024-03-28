<?php

class Application_Model_Entity_InterestRate_InterestRate extends Application_Model_Base_Entity
{
    /**
     * Get latest rate
     *
     * @return array|null
     */
    public function getLatestRateData()
    {
        $db = $this->getResource()->getAdapter();
        $select = $db->select()
            ->from(['r' => 'rate'])
            ->order('r.created_at DESC')
            ->limit(1);

        $result = $db->fetchRow($select);

        if (!$result) {
            return null;
        }

        return $result;
    }
}

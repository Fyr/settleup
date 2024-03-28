<?php

class Application_Model_Resource_Settlement_Cycle extends Application_Model_Base_Resource
{
    protected $_name = 'settlement_cycle';

    public function getInfoFields()
    {
        return [
            'cycle_start_date' => 'Period Start Date',
            'cycle_close_date' => 'Period Close Date',
            'processing_date' => 'Processing Date',
            'disbursement_date' => 'Disbursement Date',
            'title' => 'Cycle Status',
        ];
    }

    public function getAdvancedInfoFields()
    {
        return [
            'SettlementCycle' => 'Period',
            'cycle_start_date' => 'Start Date',
            'cycle_close_date' => 'Close Date',
            'processing_date' => 'Settlement Processing Date',
            'disbursement_date' => 'Settlement Disbursement Date',
            'title' => 'Status',
        ];
    }

    public function changeStatusId($id, $status)
    {
        $this->update(['status_id' => $status], $this->getAdapter()->quoteInto('id = ?', $id));

        return $this;
    }

    public function deleteParentCycle($id)
    {
        $this->update(['deleted' => 1], $this->getAdapter()->quoteInto('parent_cycle_id = ?', $id));

        return $this;
    }
}

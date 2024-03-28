<?php

use Application_Model_Entity_Settlement_Cycle as Cycle;

class Application_Model_Resource_Accounts_Reserve_History extends Application_Model_Base_Resource
{
    protected $_name = 'reserve_account_history';

    public function getInfoFields(): array
    {
        return [
            'contractor_vendor_reserve_code' => 'Code',
            'created_datetime' => 'Established Date',
            'powerunit_code' => 'Power Unit',
            'accumulated_interest' => 'Accumulated Interest',
            'min_balance' => 'Min. Balance',
            'verify_balance' => 'Starting Balance',
            'current_balance' => 'Current Balance',
        ];
    }

    /**
     * @return array
     */
    public function getReserveAccountsWithWrongHistory(Cycle $cycle)
    {
        $result = [];
        $select = $this->select()->from(['h' => $this->_name], ['h.reserve_account_id'])->joinLeft(
            ['c' => $cycle->getResource()->getTableName()],
            'h.settlement_cycle_id = c.id',
            []
        )->joinLeft(
            ['p' => $this->_name],
            'h.reserve_account_id = p.reserve_account_id and p.settlement_cycle_id = c.parent_cycle_id',
            []
        )->where('h.verify_balance <> p.current_balance')->where('p.settlement_cycle_id = ?', $cycle->getId());

        $data = $this->fetchAll($select)->toArray();
        if ($data) {
            foreach ($data as $row) {
                $result[] = $row['reserve_account_id'];
            }
        }

        return $result;
    }
}

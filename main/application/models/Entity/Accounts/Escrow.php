<?php

use Application_Model_Entity_Accounts_Escrow_History as EscrowHistory;

/**
 * @method $this staticLoad($id, $field = null)
 */
class Application_Model_Entity_Accounts_Escrow extends Application_Model_Base_Entity implements JsonSerializable
{
    final public const PFLEET_ESCROW_ACCOUNT_HOLDER = 'P-Fleet';

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize(): mixed
    {
        return $this->getData();
    }

    public function _afterSave()
    {
        $this->updatePfleetAccount();

        return $this;
    }

    public function _beforeSave()
    {
        if ($this->getId()) {
            if ($this->getEscrowAccountHolder() == self::PFLEET_ESCROW_ACCOUNT_HOLDER && $this->getOriginalData(
                'escrow_account_holder'
            ) != self::PFLEET_ESCROW_ACCOUNT_HOLDER) {
                $this->resetNextCheckNumber();
            }
        } else {
            if ($this->getEscrowAccountHolder() == self::PFLEET_ESCROW_ACCOUNT_HOLDER) {
                $this->resetNextCheckNumber();
            }
        }
        if ($this->getData('next_check_number') === '') {
            $this->unsetData('next_check_number');
        }

        return $this;
    }

    public function resetNextCheckNumber()
    {
        $nextAccount = new self();
        $nextAccount->load(self::PFLEET_ESCROW_ACCOUNT_HOLDER, 'escrow_account_holder');
        if ($nextAccount->getNextCheckNumber()) {
            $this->setNextCheckNumber($nextAccount->getNextCheckNumber());
        }

        return $this;
    }

    public function updatePfleetAccount()
    {
        if ($this->getEscrowAccountHolder() == self::PFLEET_ESCROW_ACCOUNT_HOLDER) {
            $this->getResource()->update(
                ['next_check_number' => $this->getNextCheckNumber()],
                ['escrow_account_holder = ?' => self::PFLEET_ESCROW_ACCOUNT_HOLDER]
            );
        }
    }

    /**
     * @return int
     * @throws Zend_Db_Adapter_Exception
     */
    public function getHistoryId()
    {
        $escrowHistory = new EscrowHistory();
        $db = $escrowHistory->getResource()->getAdapter();
        $db->insert($escrowHistory->getResource()->getTableName(), [
            'carrier_id' => $this->getData('carrier_id'),
            'escrow_account_holder' => $this->getData('escrow_account_holder'),
            'holder_federal_tax_id' => $this->getData('holder_federal_tax_id'),
            'next_check_number' => $this->getData('next_check_number'),
            'holder_address' => $this->getData('holder_address'),
            'holder_address_2' => $this->getData('holder_address_2'),
            'holder_city' => $this->getData('holder_city'),
            'holder_state' => $this->getData('holder_state'),
            'holder_zip' => $this->getData('holder_zip'),
        ]);

        return (int)$db->lastInsertId();
    }
}

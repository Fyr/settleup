<?php

use Application_Model_Entity_System_SetupLevels as SetupLevel;

class Application_Model_Validate_DeductionPaymentCode extends Zend_Validate_Db_Abstract
{
    final public const CODE_EXIST = 'codeExist';
    protected $_messageTemplates = [
        self::CODE_EXIST => 'This ID is already in use.',
    ];
    protected $context;

    public function isValid($value, $context = null)
    {
        $valid = true;
        $this->_setValue($value);

        $this->context = $context;
        $result = $this->_query($value);
        if ($result) {
            $valid = false;
            $this->_error(self::CODE_EXIST);
        }

        return $valid;
    }

    protected function _query($value)
    {
        $select = $this->getSelect();
        //        $select->where($select->getAdapter()->quoteIdentifier($this->getSecondField(), true).' IN (?)', $this->getCarrierIds());
        $secondField = $this->getSecondField();
        $select->where($select->getAdapter()->quoteIdentifier('deleted', true) . ' = 0');
        if ($this->getSecondField() == 'carrier_id' || !isset($this->context[$secondField])) {
            $this->context[$secondField] = $this->getCarrierId();
        }
        if (isset($this->context[$secondField]) && $this->context[$secondField]) {
            $select->where(
                $select->getAdapter()->quoteIdentifier($this->getSecondField(), true) . ' = ?',
                $this->context[$secondField]
            );
        }
        if (isset($this->context['id']) && $this->context['id']) {
            $select->where($select->getAdapter()->quoteIdentifier('id', true) . ' != ?', $this->context['id']);
        }
        $select->where('level_id = ?', SetupLevel::MASTER_LEVEL_ID);
        $result = $select->getAdapter()->fetchRow(
            $select,
            [
                'value' => $value,
            ],
            Zend_Db::FETCH_ASSOC
        );

        return $result;
    }

    protected function getCarrierIds()
    {
        $id = [];
        if ($this->getSecondField() == 'provider_id') {
            $id = $carrierVendorIds = (new Application_Model_Entity_Entity_Vendor())->getCollection(
            )->addVisibilityFilterForUser()->getField('entity_id');
        }
        $id[] = $this->getCarrierId();

        return $id;
    }

    protected function getCarrierId()
    {
        return $carrier = Application_Model_Entity_Accounts_User::getCurrentUser()->getSelectedCarrier()->getEntityId();
    }

    protected function getSecondField()
    {
        if ($this->_field == 'deduction_code') {
            return 'provider_id';
        } else {
            return 'carrier_id';
        }
    }
}

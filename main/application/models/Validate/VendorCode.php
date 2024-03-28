<?php

class Application_Model_Validate_VendorCode extends Zend_Validate_Db_Abstract
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
        $this->getSelect()->joinLeft('entity', $this->getTable() . '.entity_id = entity.id', 'deleted');
        $select->where($select->getAdapter()->quoteIdentifier('deleted', true) . ' = 0');
        if (!isset($this->context['carrier_id'])) {
            $this->context['carrier_id'] = $this->getCarrierId();
        }
        if ($this->context['carrier_id']) {
            $select->where(
                $select->getAdapter()->quoteIdentifier('carrier_id', true) . ' = ?',
                $this->context['carrier_id']
            );
        }
        if (isset($this->context['id']) && $this->context['id']) {
            $select->where(
                $select->getAdapter()->quoteIdentifier($this->getTable() . '.id', true) . ' != ?',
                $this->context['id']
            );
        }
        $result = $select->getAdapter()->fetchRow(
            $select,
            [
                'value' => $value,
            ],
            Zend_Db::FETCH_ASSOC
        );

        return $result;
    }

    protected function getCarrierId()
    {
        return $carrier = Application_Model_Entity_Accounts_User::getCurrentUser()->getSelectedCarrier()->getEntityId();
    }
}

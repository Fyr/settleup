<?php

class Application_Model_Validate_ContractorCode extends Zend_Validate_Abstract
{
    final public const CONTRACTOR_EXIST = 'contractorExist';
    protected $_messageTemplates = [
        self::CONTRACTOR_EXIST => 'This ID is already in use.',
    ];

    public function isValid($value, $context = null)
    {
        $valid = true;
        $this->_setValue($value);
        $divisionEntityId = null;
        $settlementGroupId = $context['settlement_group_id'] ?? null;
        if ($settlementGroupId) {
            $settlementGroup = (new Application_Model_Entity_Settlement_Group())
                ->getCollection()
                ->addFilter('id', $settlementGroupId)
                ->getFirstItem();
            $divisionEntityId = $settlementGroup->getDivisionEntityId();
        }
        if (!$divisionEntityId) {
            $divisionEntityId = Application_Model_Entity_Accounts_User::getCurrentUser()
                ->getSelectedCarrier()
                ->getEntityId();
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select
            ->from(['c' => 'contractor'], ['c.id'])
            ->joinLeft(['e' => 'entity'], 'c.entity_id = e.id', [])
            ->where('c.carrier_id = ?', $divisionEntityId)
            ->where('c.code = ?', $value)
            ->where('e.deleted = ?', 0);
        $result = $select->getAdapter()->fetchRow($select);
        if ($result && $result['id'] != $context['id']) {
            $valid = false;
            $this->_error(self::CONTRACTOR_EXIST);
        }

        return $valid;
    }
}

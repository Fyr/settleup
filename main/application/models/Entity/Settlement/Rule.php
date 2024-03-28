<?php

/**
 * @method $this staticLoad($id, $field = null)
 */
class Application_Model_Entity_Settlement_Rule extends Application_Model_Base_Entity
{
    use Application_Model_RecurringTrait;

    public function _afterSave()
    {
        parent::_afterSave();

        $cycle = (new Application_Model_Entity_Settlement_Cycle())->getCollection()->addFilter(
            'carrier_id',
            $this->getCarrierId()
        )->addFilter('cycle_start_date', $this->getCycleStartDate(), 'LTE DATE')->addFilter(
            'cycle_close_date',
            $this->getCycleStartDate(),
            'GTE DATE'
        )->addFilter(
            'status_id',
            Application_Model_Entity_System_SettlementCycleStatus::NOT_VERIFIED_STATUS_ID
        )->setOrder('cycle_close_date', Application_Model_Base_Collection::SORT_ORDER_DESC)->getFirstItem();
        //        ->getSelectBeforeLoad(); /*** Zend_Db_Table_Select $cycle*/
        //        $select = $cycle->__toString():
        if ($cycle->getId()) {
            $cycle->setCycleCloseDate(null)->setDisbursementDate(null)->setProcessingDate(null)->save();
        }

        return $this;
    }

    public function getRecurringType()
    {
        return $this->getCyclePeriodId();
    }
}

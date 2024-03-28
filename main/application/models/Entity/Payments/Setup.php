<?php

use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_System_SetupLevels as SetupLevel;

/**
 * @method $this staticLoad($id, $field = null)
 * @method Application_Model_Entity_Collection_Payments_Setup getCollection()
 * @method Application_Model_Resource_Payments_Setup getResource()
 */

class Application_Model_Entity_Payments_Setup extends Application_Model_Base_Entity
{
    use Application_Model_Entity_Permissions_CarrierTrait;
    use Application_Model_RecurringTrait;
    use Application_Model_Recurring_UpdateNextRecurringTrait;

    /** @var Contractor */
    protected $contractor;

    /**
     * returns carrier
     *
     * @return Application_Model_Entity_Entity_Carrier
     */
    public function getCarrier()
    {
        $carrier = new Application_Model_Entity_Entity_Carrier();

        return $carrier->load($this->getCarrierId(), 'entity_id');
    }

    /**
     * @return Application_Model_Entity_Payments_Setup
     */
    public function _beforeSave()
    {
        if ($this->getCarrierId() == null) {
            $this->setCarrierId(
                Application_Model_Entity_Accounts_User::getCurrentUser()->getEntity()->getCurrentCarrier()->getEntityId(
                )
            );
        }

        if ($this->getContractorId() == '') {
            $this->setContractorId();
        }

        if ($this->getDeleted() !== 1) {
            $this->setDeleted(0);
        }

        if ($this->getBillingCycleId() == Application_Model_Entity_System_CyclePeriod::BIWEEKLY_PERIOD_ID) {
            $this->setFirstStartDay((new DateTime($this->getBiweeklyStartDay()))->format('w'));
        }

        //        if (
        //            $this->getBillingCycleId()
        //            != Application_Model_Entity_System_CyclePeriod::
        //            SEMY_MONTHLY_PERIOD_ID
        //        ) {
        //            $this->unsFirstStartDay();
        //            $this->unsSecondStartDay();
        //        }

        if (!$this->getLevelId()) {
            $this->setLevelId(SetupLevel::MASTER_LEVEL_ID);
        }
        $this->setRate(str_replace(',', '', (string) $this->getRate()));

        return $this;
    }

    public function getBillingCycleOptions()
    {
        $billingCycles = new Application_Model_Entity_System_CyclePeriod();
        $billingCycles = $billingCycles->getBillingCycles(
            $this->getBillingCycleId()
        );

        return $billingCycles;
    }

    public function getRecurringType()
    {
        return $this->getBillingCycleId();
    }

    public function deleteIndividualTemplates()
    {
        $this->getResource()->update(['deleted' => 1], ['master_setup_id = ?' => $this->getId()]);

        return $this;
    }

    public function createIndividualTemplates()
    {
        $id = $this->getId();
        $stmt = $this->getResource()->getAdapter()->prepare('CALL createIndividualPaymentTemplates(?)');
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $this;
    }

    public function updateIndividualTemplates()
    {
        $this->getResource()->update([
            'carrier_payment_code' => $this->getData('carrier_payment_code'),
            'description' => $this->getData('description'),
            'category' => $this->getData('category'),
            'department' => $this->getData('department'),
            'gl_code' => $this->getData('gl_code'),
            'disbursement_code' => $this->getData('disbursement_code'),
            'terms' => $this->getData('terms'),
            'quantity' => $this->getData('quantity'),
            'rate' => $this->getData('rate'),
            'recurring' => $this->getData('recurring'),
            'billing_cycle_id' => $this->getData('billing_cycle_id'),
            'week_offset' => $this->getData('week_offset'),
            'first_start_day' => $this->getData('first_start_day'),
            'second_start_day' => $this->getData('second_start_day'),
            'biweekly_start_day' => $this->getData('biweekly_start_day'),
            'taxable' => $this->getData('taxable'),
        ], [
            'master_setup_id = ?' => $this->getId(),
            'changed = 0',
        ]);

        return $this;
    }

    /**
     * @return Contractor
     */
    public function getContractor()
    {
        if (!$this->contractor || (int)$this->getContractorId() !== (int)$this->contractor->getEntityId()) {
            $this->contractor = Contractor::staticLoad((int)$this->getContractorId(), 'entity_id');
        }

        return $this->contractor;
    }

    public function getAssociatedEntity()
    {
        return new Application_Model_Entity_Payments_Payment();
    }
}

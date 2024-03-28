<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Settlement_Cycle as Cycle;
use Application_Model_Entity_System_SettlementCycleStatus as CycleStatus;

class Application_Model_Grid_Transaction_Disbursement extends Application_Model_Grid
{
    public function __construct()
    {
        $disbursementEntity = new Application_Model_Entity_Transactions_Disbursement();
        $cycle = $this->getSettlementCycle();
        $header = [
            'totals' => [
                'template' => 'transactions/disbursement/totals.phtml',
                'data' => [
                    'cycle' => $cycle,
                ],
            ],
            'header' => $disbursementEntity->getResource()->getInfoFields(),
            'sort' => [
                'entity_code' => 'ASC',
                'entity_id' => 'ASC',
                // 'priority' => 'ASC'
            ],
            'checkboxField' => false,
            'id' => static::class,
            'filter' => true,
            'callbacks' => [
                'action' => Application_Model_Grid_Callback_ActionDisbursement::class,
                'amount' => Application_Model_Grid_Callback_Balance::class,
            ],
            'buttons' => Application_Model_Grid_Header_Disbursements::class,
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
            ],
        ];

        $customFilters = ['addCarrierFilter'];

        $grid = parent::__construct(
            $disbursementEntity::class,
            $header,
            [],
            $customFilters
        );

        $grid->setCyclePeriods($cycle->getAllCyclePeriods())->setCycle($cycle);

        return $grid;
    }

    /**
     * @return Cycle
     */
    public function getSettlementCycle()
    {
        $cycle = parent::getSettlementCycle();
        if ((int)$cycle->getStatusId() !== CycleStatus::APPROVED_STATUS_ID) {
            $cycle = $cycle->getCarrier()->getLastClosedSettlementCycle();
            if ($cycle instanceof Cycle) {
                User::getCurrentUser()->setSettlementCycle($cycle);
                setcookie('settlement_cycle_filter_type', Cycle::LAST_CLOSED_FILTER_TYPE, ['expires' => time() + 31_536_020, 'path' => '/']);
            } else {
                $cycle = new Cycle();
            }
        }

        return $cycle;
    }
}

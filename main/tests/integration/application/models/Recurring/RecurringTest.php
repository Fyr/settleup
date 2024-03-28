<?php

class RecurringTest extends BaseTestCase
{
    final public const SUNDAY = '0';
    final public const MONDAY = '1';
    final public const TUESDAY = '2';
    final public const WEDNESDAY = '3';
    final public const THURSDAY = '4';
    final public const FRIDAY = '5';
    final public const SATURDAY = '6';
    public static $carrier;
    public static $contractor;
    public static $cycleEntities;
    protected $deductionSetups;
    protected $paymentsSetups;

    public function testIncomplete()
    {
        $this->markTestIncomplete(
            'Recurring tests should be fixed!'
        );
    }

    //    /**
    //     * @dataProvider providerStrategy
    //     */
    //    public function testRecurringStrategy($billingCycleId, $strategy)
    //    {
    //        $deductions = new Application_Model_Entity_Deductions_Deduction();
    //        $deductions->setBillingCycleId(
    //            constant(
    //                'Application_Model_Entity_System_CyclePeriod::'
    //                . $billingCycleId
    //            )
    //        );
    //        $reflection = new ReflectionClass($deductions->getRecurringStrategy());
    //        $this->assertEquals($reflection->getName(), $strategy);
    //    }
    //
    //    public function providerStrategy()
    //    {
    //        return array(
    //            array(
    //                'MONTHLY_PERIOD_ID',
    //                'Application_Model_Recurring_MonthlyStrategy',
    //            ),
    //            array(
    //                'MONTHLY_SEMI_MONTHLY_ID',
    //                'Application_Model_Recurring_MonthlyStrategy',
    //            ),
    //            array(
    //                'SEMY_MONTHLY_PERIOD_ID',
    //                'Application_Model_Recurring_SemiMonthlyStrategy',
    //            ),
    //            array(
    //                'WEEKLY_PERIOD_ID',
    //                'Application_Model_Recurring_WeeklyStrategy',
    //            ),
    //            array(
    //                'BIWEEKLY_PERIOD_ID',
    //                'Application_Model_Recurring_BiWeeklyStrategy',
    //            ),
    //            array(
    //                'SEMI_WEEKLY_PERIOD_ID',
    //                'Application_Model_Recurring_SemiWeeklyStrategy',
    //            ),
    //        );
    //    }
    //
    //    //***************************************   WEEKLY CYCLE *********************************************//
    //
    //    public function testBeforeWeeklyCycle()
    //    {
    //        self::$carrier = $this->newCarrier();
    //        self::$contractor = parent::newContractor(self::$carrier);
    //        $cycles = array();
    //        $cycles[0] = $this->newCycle(self::$carrier,
    //            array(
    //                'cycle_period_id'    => Application_Model_Entity_System_CyclePeriod::WEEKLY_PERIOD_ID,
    //                'cycle_start_date'   => '2014-06-01',
    //                'cycle_close_date'   => '2014-06-07',
    //            )
    //        );
    //        Application_Model_Entity_Accounts_User::login($this->_myUser);
    //        $cycles[1] = $this->verifyCycle($cycles[0]);
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::WEEKLY_PERIOD_ID,$this::SUNDAY);
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::BIWEEKLY_PERIOD_ID,$this::WEDNESDAY);//1st week
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::SEMI_WEEKLY_PERIOD_ID,$this::MONDAY, $this::THURSDAY);
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::MONTHLY_PERIOD_ID,'1');
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::SEMY_MONTHLY_PERIOD_ID,'14','31');
    //
    //        $this->createEntities($cycles[0]);
    //
    //        $cycles[] = $this->verifyCycle($cycles[1]);
    //        $cycles[] = $this->verifyCycle($cycles[2]);
    //        $cycles[] = $this->verifyCycle($cycles[3]);
    //        $cycles[] = $this->verifyCycle($cycles[4]);
    //        $cycles[] = $this->verifyCycle($cycles[5]);
    //        $cycles[] = $this->verifyCycle($cycles[6]);
    //        $cycles[] = $this->verifyCycle($cycles[7]);
    //        $cycles[] = $this->verifyCycle($cycles[8]);
    //
    //        self::$cycleEntities = array();
    //        foreach ($cycles as $cycle) {
    //            self::$cycleEntities['payments'][]   = $this->countCycleEntities($cycle, 'payments');
    //            self::$cycleEntities['deductions'][] = $this->countCycleEntities($cycle, 'deductions');
    //        }
    //    }
    //
    //    public function providerWeeklyCycle()
    //    {
    //        return array(
    //            array(
    //                'cycle_order' => 0,
    //                'weekly'        => 1,
    //                'biweekly'      => 1,
    //                'semi_weekly'   => 2,
    //                'monthly'       => 1,
    //                'semi_monthly'  => 0,
    //            ),
    //            array(
    //                'cycle_order' => 1,
    //                'weekly'        => 1,
    //                'biweekly'      => 0,
    //                'semi_weekly'   => 2,
    //                'monthly'       => 0,
    //                'semi_monthly'  => 1,
    //            ),
    //            array(
    //                'cycle_order' => 2,
    //                'weekly'        => 1,
    //                'biweekly'      => 1,
    //                'semi_weekly'   => 2,
    //                'monthly'       => 0,
    //                'semi_monthly'  => 0,
    //            ),
    //            array(
    //                'cycle_order' => 3,
    //                'weekly'        => 1,
    //                'biweekly'      => 0,
    //                'semi_weekly'   => 2,
    //                'monthly'       => 0,
    //                'semi_monthly'  => 0,
    //            ),
    //            array(
    //                'cycle_order' => 4,
    //                'weekly'        => 1,
    //                'biweekly'      => 1,
    //                'semi_weekly'   => 2,
    //                'monthly'       => 1,
    //                'semi_monthly'  => 1,
    //            ),
    //            array(
    //                'cycle_order' => 5,
    //                'weekly'        => 1,
    //                'biweekly'      => 0,
    //                'semi_weekly'   => 2,
    //                'monthly'       => 0,
    //                'semi_monthly'  => 0,
    //            ),
    //            array(
    //                'cycle_order' => 6,
    //                'weekly'        => 1,
    //                'biweekly'      => 1,
    //                'semi_weekly'   => 2,
    //                'monthly'       => 0,
    //                'semi_monthly'  => 1,
    //            ),
    //            array(
    //                'cycle_order' => 7,
    //                'weekly'        => 1,
    //                'biweekly'      => 0,
    //                'semi_weekly'   => 2,
    //                'monthly'       => 0,
    //                'semi_monthly'  => 0,
    //            ),
    //            array(
    //                'cycle_order' => 8,
    //                'weekly'        => 1,
    //                'biweekly'      => 1,
    //                'semi_weekly'   => 2,
    //                'monthly'       => 1,
    //                'semi_monthly'  => 1,
    //            ),
    //        );
    //    }
    //
    //    /**
    //     * @dataProvider providerWeeklyCycle
    //     */
    //    public function testRecurringSettlementCycleWeekly(
    //        $cycle_order,
    //        $weekly,
    //        $biweekly,
    //        $semi_weekly,
    //        $monthly,
    //        $semi_monthly
    //    )
    //    {
    //        $this->assertEquals(
    //            array(
    //                'weekly'        => $weekly,
    //                'biweekly'      => $biweekly,
    //                'semi_weekly'   => $semi_weekly,
    //                'monthly'       => $monthly,
    //                'semi_monthly'  => $semi_monthly
    //            ),
    //            self::$cycleEntities['payments'][$cycle_order]
    //        );
    //        $this->assertEquals(
    //            array(
    //                'weekly'        => $weekly,
    //                'biweekly'      => $biweekly,
    //                'semi_weekly'   => $semi_weekly,
    //                'monthly'       => $monthly,
    //                'semi_monthly'  => $semi_monthly
    //            ),
    //            self::$cycleEntities['deductions'][$cycle_order]
    //        );
    //    }
    //
    //    //***************************************   BI-WEEKLY CYCLE *********************************************//
    //
    //    public function testBeforeBiWeeklyCycle()
    //    {
    //        self::$carrier = $this->newCarrier();
    //        self::$contractor = parent::newContractor(self::$carrier);
    //        $cycles = array();
    //        $cycles[0] = $this->newCycle(self::$carrier,
    //            array(
    //                'cycle_period_id'    => Application_Model_Entity_System_CyclePeriod::BIWEEKLY_PERIOD_ID,
    //                'cycle_start_date'   => '2014-01-05',
    //                'cycle_close_date'   => '2014-01-18',
    //            )
    //        );
    //        Application_Model_Entity_Accounts_User::login($this->_myUser);
    //        $cycles[1] = $this->verifyCycle($cycles[0]);
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::WEEKLY_PERIOD_ID,$this::SUNDAY);
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::BIWEEKLY_PERIOD_ID,$this::THURSDAY,'','1');//2st week
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::SEMI_WEEKLY_PERIOD_ID,$this::MONDAY,$this::THURSDAY);
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::MONTHLY_PERIOD_ID,'5');
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::SEMY_MONTHLY_PERIOD_ID,'9','30');
    //
    //        $this->createEntities($cycles[0]);
    //
    //        $cycles[] = $this->verifyCycle($cycles[1]);
    //        $cycles[] = $this->verifyCycle($cycles[2]);
    //        $cycles[] = $this->verifyCycle($cycles[3]);
    //        $cycles[] = $this->verifyCycle($cycles[4]);
    //        $cycles[] = $this->verifyCycle($cycles[5]);
    //
    //        self::$cycleEntities = array();
    //        foreach ($cycles as $cycle) {
    //            self::$cycleEntities['payments'][]   = $this->countCycleEntities($cycle, 'payments');
    //            self::$cycleEntities['deductions'][] = $this->countCycleEntities($cycle, 'deductions');
    //        }
    //    }
    //
    //    public function providerBiWeeklyCycle()
    //    {
    //        return array(
    //            array(
    //                'cycle_order' => 0,
    //                'weekly'        => 2,
    //                'biweekly'      => 1,
    //                'semi_weekly'   => 4,
    //                'monthly'       => 1,
    //                'semi_monthly'  => 1,
    //            ),
    //            array(
    //                'cycle_order' => 1,
    //                'weekly'        => 2,
    //                'biweekly'      => 1,
    //                'semi_weekly'   => 4,
    //                'monthly'       => 0,
    //                'semi_monthly'  => 1,
    //            ),
    //            array(
    //                'cycle_order' => 2,
    //                'weekly'        => 2,
    //                'biweekly'      => 1,
    //                'semi_weekly'   => 4,
    //                'monthly'       => 1,
    //                'semi_monthly'  => 1,
    //            ),
    //            array(
    //                'cycle_order' => 3,
    //                'weekly'        => 2,
    //                'biweekly'      => 1,
    //                'semi_weekly'   => 4,
    //                'monthly'       => 0,
    //                'semi_monthly'  => 1,
    //            ),
    //            array(
    //                'cycle_order' => 4,
    //                'weekly'        => 2,
    //                'biweekly'      => 1,
    //                'semi_weekly'   => 4,
    //                'monthly'       => 1,
    //                'semi_monthly'  => 1,
    //            ),
    //            array(
    //                'cycle_order' => 5,
    //                'weekly'        => 2,
    //                'biweekly'      => 1,
    //                'semi_weekly'   => 4,
    //                'monthly'       => 0,
    //                'semi_monthly'  => 0,
    //            ),
    //        );
    //    }
    //
    //    /**
    //     * @dataProvider providerBiWeeklyCycle
    //     */
    //    public function testRecurringSettlementCycleBiWeekly(
    //        $cycle_order,
    //        $weekly,
    //        $biweekly,
    //        $semi_weekly,
    //        $monthly,
    //        $semi_monthly
    //    )
    //    {
    //        $this->assertEquals(
    //            array(
    //                'weekly'        => $weekly,
    //                'biweekly'      => $biweekly,
    //                'semi_weekly'   => $semi_weekly,
    //                'monthly'       => $monthly,
    //                'semi_monthly'  => $semi_monthly
    //            ),
    //            self::$cycleEntities['payments'][$cycle_order]
    //        );
    //        $this->assertEquals(
    //            array(
    //                'weekly'        => $weekly,
    //                'biweekly'      => $biweekly,
    //                'semi_weekly'   => $semi_weekly,
    //                'monthly'       => $monthly,
    //                'semi_monthly'  => $semi_monthly
    //            ),
    //            self::$cycleEntities['deductions'][$cycle_order]
    //        );
    //    }
    //
    //
    //    //***************************************   SEMI-WEEKLY CYCLE *********************************************//
    //
    //    public function testBeforeSemiWeeklyCycle()
    //    {
    //        self::$carrier = $this->newCarrier();
    //        self::$contractor = parent::newContractor(self::$carrier);
    //        $cycles = array();
    //        $cycles[0] = $this->newCycle(self::$carrier,
    //            array(
    //                'cycle_period_id'    => Application_Model_Entity_System_CyclePeriod::SEMI_WEEKLY_PERIOD_ID,
    //                'cycle_start_date'   => '2014-12-20',
    //                'cycle_close_date'   => '2014-12-22',
    //                'first_start_day'    => $this::TUESDAY,
    //                'second_start_day'   => $this::FRIDAY,
    //            )
    //        );
    //        Application_Model_Entity_Accounts_User::login($this->_myUser);
    //        $cycles[1] = $this->verifyCycle($cycles[0]);
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::WEEKLY_PERIOD_ID, $this::SATURDAY);
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::BIWEEKLY_PERIOD_ID, $this::THURSDAY,'','1');//2st week
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::SEMI_WEEKLY_PERIOD_ID,$this::MONDAY,$this::FRIDAY);
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::MONTHLY_PERIOD_ID,'5');
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::SEMY_MONTHLY_PERIOD_ID,'9','30');
    //
    //        $this->createEntities($cycles[0]);
    //
    //        $cycles[] = $this->verifyCycle($cycles[1]);
    //        $cycles[] = $this->verifyCycle($cycles[2]);
    //        $cycles[] = $this->verifyCycle($cycles[3]);
    //        $cycles[] = $this->verifyCycle($cycles[4]);
    //        $cycles[] = $this->verifyCycle($cycles[5]);
    //        $cycles[] = $this->verifyCycle($cycles[6]);
    //        $cycles[] = $this->verifyCycle($cycles[7]);
    //        $cycles[] = $this->verifyCycle($cycles[8]);
    //        $cycles[] = $this->verifyCycle($cycles[9]);
    //
    //        self::$cycleEntities = array();
    //        foreach ($cycles as $cycle) {
    //            self::$cycleEntities['payments'][]   = $this->countCycleEntities($cycle, 'payments');
    //            self::$cycleEntities['deductions'][] = $this->countCycleEntities($cycle, 'deductions');
    //        }
    //    }
    //
    //    public function providerSemiWeeklyCycle()
    //    {
    //        return array(
    //            array(
    //                'cycle_order' => 0,
    //                'weekly'        => 1,
    //                'biweekly'      => 0,
    //                'semi_weekly'   => 1,
    //                'monthly'       => 0,
    //                'semi_monthly'  => 0,
    //            ),
    //            array(
    //                'cycle_order' => 1,
    //                'weekly'        => 0,
    //                'biweekly'      => 0,
    //                'semi_weekly'   => 0,
    //                'monthly'       => 0,
    //                'semi_monthly'  => 0,
    //            ),
    //            array(
    //                'cycle_order' => 2,
    //                'weekly'        => 1,
    //                'biweekly'      => 0,
    //                'semi_weekly'   => 2,
    //                'monthly'       => 0,
    //                'semi_monthly'  => 0,
    //            ),
    //            array(
    //                'cycle_order' => 3,
    //                'weekly'        => 0,
    //                'biweekly'      => 1,
    //                'semi_weekly'   => 0,
    //                'monthly'       => 0,
    //                'semi_monthly'  => 1,
    //            ),
    //            array(
    //                'cycle_order' => 4,
    //                'weekly'        => 1,
    //                'biweekly'      => 0,
    //                'semi_weekly'   => 2,
    //                'monthly'       => 1,
    //                'semi_monthly'  => 0,
    //            ),
    //            array(
    //                'cycle_order' => 5,
    //                'weekly'        => 0,
    //                'biweekly'      => 0,
    //                'semi_weekly'   => 0,
    //                'monthly'       => 0,
    //                'semi_monthly'  => 0,
    //            ),
    //            array(
    //                'cycle_order' => 6,
    //                'weekly'        => 1,
    //                'biweekly'      => 0,
    //                'semi_weekly'   => 2,
    //                'monthly'       => 0,
    //                'semi_monthly'  => 1,
    //            ),
    //            array(
    //                'cycle_order' => 7,
    //                'weekly'        => 0,
    //                'biweekly'      => 1,
    //                'semi_weekly'   => 0,
    //                'monthly'       => 0,
    //                'semi_monthly'  => 0,
    //            ),
    //            array(
    //                'cycle_order' => 8,
    //                'weekly'        => 1,
    //                'biweekly'      => 0,
    //                'semi_weekly'   => 2,
    //                'monthly'       => 0,
    //                'semi_monthly'  => 0,
    //            ),
    //            array(
    //                'cycle_order' => 9,
    //                'weekly'        => 0,
    //                'biweekly'      => 0,
    //                'semi_weekly'   => 0,
    //                'monthly'       => 0,
    //                'semi_monthly'  => 0,
    //            ),
    //        );
    //    }
    //
    //    /**
    //     * @dataProvider providerSemiWeeklyCycle
    //     */
    //    public function testRecurringSettlementCycleSemiWeekly(
    //        $cycle_order,
    //        $weekly,
    //        $biweekly,
    //        $semi_weekly,
    //        $monthly,
    //        $semi_monthly
    //    )
    //    {
    //        $this->assertEquals(
    //            array(
    //                'weekly'        => $weekly,
    //                'biweekly'      => $biweekly,
    //                'semi_weekly'   => $semi_weekly,
    //                'monthly'       => $monthly,
    //                'semi_monthly'  => $semi_monthly
    //            ),
    //            self::$cycleEntities['payments'][$cycle_order]
    //        );
    //        $this->assertEquals(
    //            array(
    //                'weekly'        => $weekly,
    //                'biweekly'      => $biweekly,
    //                'semi_weekly'   => $semi_weekly,
    //                'monthly'       => $monthly,
    //                'semi_monthly'  => $semi_monthly
    //            ),
    //            self::$cycleEntities['deductions'][$cycle_order]
    //        );
    //
    //    }
    //
    //    //***************************************   MONTHLY CYCLE *********************************************//
    //
    //    public function testBeforeMonthlyCycle()
    //    {
    //        self::$carrier = $this->newCarrier();
    //        self::$contractor = parent::newContractor(self::$carrier);
    //        $cycles = array();
    //        $cycles[0] = $this->newCycle(self::$carrier,
    //            array(
    //                'cycle_period_id'    => Application_Model_Entity_System_CyclePeriod::MONTHLY_PERIOD_ID,
    //                'cycle_start_date'   => '2014-07-16',
    //                'cycle_close_date'   => '2014-08-15'
    //            )
    //        );
    //        Application_Model_Entity_Accounts_User::login($this->_myUser);
    //        $cycles[1] = $this->verifyCycle($cycles[0]);
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::WEEKLY_PERIOD_ID, $this::WEDNESDAY);
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::BIWEEKLY_PERIOD_ID,$this::FRIDAY);//1st week
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::SEMI_WEEKLY_PERIOD_ID,$this::MONDAY,$this::THURSDAY);
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::MONTHLY_PERIOD_ID,'5');
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::SEMY_MONTHLY_PERIOD_ID,'10','26');
    //
    //        $this->createEntities($cycles[0]);
    //
    //        $cycles[] = $this->verifyCycle($cycles[1]);
    //        $cycles[] = $this->verifyCycle($cycles[2]);
    //        $cycles[] = $this->verifyCycle($cycles[3]);
    //        $cycles[] = $this->verifyCycle($cycles[4]);
    //
    //        self::$cycleEntities = array();
    //        foreach ($cycles as $cycle) {
    //            self::$cycleEntities['payments'][]   = $this->countCycleEntities($cycle, 'payments');
    //            self::$cycleEntities['deductions'][] = $this->countCycleEntities($cycle, 'deductions');
    //        }
    //    }
    //
    //    public function providerMonthlyCycle()
    //    {
    //        return array(
    //            array(
    //                'cycle_order' => 0,
    //                'weekly'        => 5,
    //                'biweekly'      => 3,
    //                'semi_weekly'   => 9,
    //                'monthly'       => 1,
    //                'semi_monthly'  => 2,
    //            ),
    //            array(
    //                'cycle_order' => 1,
    //                'weekly'        => 4,
    //                'biweekly'      => 2,
    //                'semi_weekly'   => 9,
    //                'monthly'       => 1,
    //                'semi_monthly'  => 2,
    //            ),
    //            array(
    //                'cycle_order' => 2,
    //                'weekly'        => 5,
    //                'biweekly'      => 2,
    //                'semi_weekly'   => 8,
    //                'monthly'       => 1,
    //                'semi_monthly'  => 2,
    //            ),
    //            array(
    //                'cycle_order' => 3,
    //                'weekly'        => 4,
    //                'biweekly'      => 2,
    //                'semi_weekly'   => 9,
    //                'monthly'       => 1,
    //                'semi_monthly'  => 2,
    //            ),
    //            array(
    //                'cycle_order' => 4,
    //                'weekly'        => 4,
    //                'biweekly'      => 2,
    //                'semi_weekly'   => 9,
    //                'monthly'       => 1,
    //                'semi_monthly'  => 2,
    //            ),
    //        );
    //    }
    //
    //    /**
    //     * @dataProvider providerMonthlyCycle
    //     */
    //    public function testRecurringSettlementCycleMonthly(
    //        $cycle_order,
    //        $weekly,
    //        $biweekly,
    //        $semi_weekly,
    //        $monthly,
    //        $semi_monthly
    //    )
    //    {
    //        $this->assertEquals(
    //            array(
    //                'weekly'        => $weekly,
    //                'biweekly'      => $biweekly,
    //                'semi_weekly'   => $semi_weekly,
    //                'monthly'       => $monthly,
    //                'semi_monthly'  => $semi_monthly
    //            ),
    //            self::$cycleEntities['payments'][$cycle_order]
    //        );
    //        $this->assertEquals(
    //            array(
    //                'weekly'        => $weekly,
    //                'biweekly'      => $biweekly,
    //                'semi_weekly'   => $semi_weekly,
    //                'monthly'       => $monthly,
    //                'semi_monthly'  => $semi_monthly
    //            ),
    //            self::$cycleEntities['deductions'][$cycle_order]
    //        );
    //    }
    //
    //    //***************************************   SEMI-MONTHLY CYCLE *********************************************//
    //
    //    public function testBeforeSemiMonthlyCycle()
    //    {
    //        self::$carrier = $this->newCarrier();
    //        self::$contractor = parent::newContractor(self::$carrier);
    //        $cycles = array();
    //        $cycles[0] = $this->newCycle(self::$carrier,
    //            array(
    //                'cycle_period_id'    => Application_Model_Entity_System_CyclePeriod::SEMY_MONTHLY_PERIOD_ID,
    //                'cycle_start_date'   => '2014-04-02',
    //                'cycle_close_date'   => '2014-04-07',
    //                'first_start_day'    => '8',
    //                'second_start_day'   => '21',
    //            )
    //        );
    //        Application_Model_Entity_Accounts_User::login($this->_myUser);
    //        $cycles[1] = $this->verifyCycle($cycles[0]);
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::WEEKLY_PERIOD_ID,$this::SATURDAY);
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::BIWEEKLY_PERIOD_ID,$this::MONDAY,'','1');//2st week
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::SEMI_WEEKLY_PERIOD_ID,$this::TUESDAY,$this::FRIDAY);
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::MONTHLY_PERIOD_ID,'5');
    //        $this->newEntitiesTemplate(Application_Model_Entity_System_CyclePeriod::SEMY_MONTHLY_PERIOD_ID,'15','31');
    //
    //        $this->createEntities($cycles[0]);
    //
    //        $cycles[] = $this->verifyCycle($cycles[1]);
    //        $cycles[] = $this->verifyCycle($cycles[2]);
    //        $cycles[] = $this->verifyCycle($cycles[3]);
    //        $cycles[] = $this->verifyCycle($cycles[4]);
    //        $cycles[] = $this->verifyCycle($cycles[5]);
    //        $cycles[] = $this->verifyCycle($cycles[6]);
    //        $cycles[] = $this->verifyCycle($cycles[7]);
    //
    //        self::$cycleEntities = array();
    //        foreach ($cycles as $cycle) {
    //            self::$cycleEntities['payments'][]   = $this->countCycleEntities($cycle, 'payments');
    //            self::$cycleEntities['deductions'][] = $this->countCycleEntities($cycle, 'deductions');
    //        }
    //    }
    //
    //    public function providerSemiMonthlyCycle()
    //    {
    //        return array(
    //            array(
    //                'cycle_order' => 0,
    //                'weekly'        => 1,
    //                'biweekly'      => 0,
    //                'semi_weekly'   => 1,
    //                'monthly'       => 1,
    //                'semi_monthly'  => 0,
    //            ),
    //            array(
    //                'cycle_order' => 1,
    //                'weekly'        => 2,
    //                'biweekly'      => 1,
    //                'semi_weekly'   => 4,
    //                'monthly'       => 0,
    //                'semi_monthly'  => 1,
    //            ),
    //            array(
    //                'cycle_order' => 2,
    //                'weekly'        => 2,
    //                'biweekly'      => 1,
    //                'semi_weekly'   => 5,
    //                'monthly'       => 1,
    //                'semi_monthly'  => 1,
    //            ),
    //            array(
    //                'cycle_order' => 3,
    //                'weekly'        => 2,
    //                'biweekly'      => 1,
    //                'semi_weekly'   => 4,
    //                'monthly'       => 0,
    //                'semi_monthly'  => 1,
    //            ),
    //            array(
    //                'cycle_order' => 4,
    //                'weekly'        => 3,
    //                'biweekly'      => 1,
    //                'semi_weekly'   => 5,
    //                'monthly'       => 1,
    //                'semi_monthly'  => 1,
    //            ),
    //            array(
    //                'cycle_order' => 5,
    //                'weekly'        => 1,
    //                'biweekly'      => 1,
    //                'semi_weekly'   => 4,
    //                'monthly'       => 0,
    //                'semi_monthly'  => 1,
    //            ),
    //            array(
    //                'cycle_order' => 6,
    //                'weekly'        => 3,
    //                'biweekly'      => 2,
    //                'semi_weekly'   => 4,
    //                'monthly'       => 1,
    //                'semi_monthly'  => 1,
    //            ),
    //            array(
    //                'cycle_order' => 7,
    //                'weekly'        => 2,
    //                'biweekly'      => 0,
    //                'semi_weekly'   => 4,
    //                'monthly'       => 0,
    //                'semi_monthly'  => 1,
    //            ),
    //        );
    //    }
    //
    //    /**
    //     * @dataProvider providerSemiMonthlyCycle
    //     */
    //    public function testRecurringSettlementCycleSemiMonthly(
    //        $cycle_order,
    //        $weekly,
    //        $biweekly,
    //        $semi_weekly,
    //        $monthly,
    //        $semi_monthly
    //    )
    //    {
    //        $this->assertEquals(
    //            array(
    //                'weekly'        => $weekly,
    //                'biweekly'      => $biweekly,
    //                'semi_weekly'   => $semi_weekly,
    //                'monthly'       => $monthly,
    //                'semi_monthly'  => $semi_monthly
    //            ),
    //            self::$cycleEntities['payments'][$cycle_order]
    //        );
    //        $this->assertEquals(
    //            array(
    //                'weekly'        => $weekly,
    //                'biweekly'      => $biweekly,
    //                'semi_weekly'   => $semi_weekly,
    //                'monthly'       => $monthly,
    //                'semi_monthly'  => $semi_monthly
    //            ),
    //            self::$cycleEntities['deductions'][$cycle_order]
    //        );
    //    }
    //
    //    //************************************************************** SPECIAL FUNCTIONS *********************************
    //
    //    /**
    //     * @param $cycle Application_Model_Entity_Settlement_Cycle
    //     * @return Application_Model_Entity_Settlement_Cycle
    //     */
    //    private function verifyCycle($cycle)
    //    {
    //        $cycle->verify();
    //        return (new Application_Model_Entity_Settlement_Cycle())->load($cycle->getId(),'parent_cycle_id');
    //    }
    //
    //    private function newEntitiesTemplate($frequency, $first='', $second = '', $week_offset ='')
    //    {
    //        $fields = array(
    //            'billing_cycle_id' => $frequency,
    //            'first_start_day'  => $first,
    //            'second_start_day' => $second,
    //            'week_offset'      => $week_offset,
    //
    //            'recurring'        => '1',
    //            'rate'             => $frequency,
    //            'quantity'         => $frequency,
    //            'amount'           => pow($frequency,2),
    //
    //        );
    //        $setup = $this->newDeductionSetup(self::$carrier, $fields);
    //        $this->deductionSetups[] = $setup->getId();
    //
    //        $fields = array(
    //            'billing_cycle_id' => $frequency,
    //            'first_start_day'  => $first,
    //            'second_start_day' => $second,
    //            'week_offset'      => $week_offset,
    //
    //            'recurring'        => '1',
    //            'rate'             => $frequency,
    //            'quantity'         => $frequency,
    //            'amount'           => pow($frequency,2),
    //
    //        );
    //        $setup = $this->newPaymentSetup(self::$carrier, $fields);
    //        $this->paymentsSetups[] = $setup->getId();
    //    }
    //
    //    /**
    //     * @param $cycle Application_Model_Entity_Settlement_Cycle
    //     */
    //    private function createEntities($cycle)
    //    {
    //        $this->baseTestAction(
    //            array(
    //                'params' => array(
    //                    'action' => 'new',
    //                    'controller' => 'deductions_deductions'
    //                ),
    //                'ajax' => array(
    //                    'selectedSetup' => $this->deductionSetups,
    //                    'selectedContractors' => array(self::$contractor->getEntityId()),
    //                    'selectedCycle' => $cycle->getId(),
    //                ),
    //            )
    //        );
    //
    //        $this->baseTestAction(
    //            array(
    //                'params' => array(
    //                    'action' => 'new',
    //                    'controller' => 'payments_payments'
    //                ),
    //                'ajax' => array(
    //                    'selectedSetup' => $this->paymentsSetups,
    //                    'selectedContractors' => array(self::$contractor->getEntityId()),
    //                    'selectedCycle' => $cycle->getId(),
    //                ),
    //            )
    //        );
    //    }
    //
    //    /**
    //     * @param $cycle Application_Model_Entity_Settlement_Cycle
    //     * @return array
    //     */
    //    private function countCycleEntities($cycle, $type)
    //    {
    //        $model = 'Application_Model_Entity_Payments_Payment';
    //        if ($type == 'deductions') {$model = 'Application_Model_Entity_Deductions_Deduction';}
    //
    //        $entities['weekly'] = (new $model)->getCollection()
    //            ->addFilter('settlement_cycle_id', $cycle->getId())
    //            ->addFilter('billing_cycle_id',Application_Model_Entity_System_CyclePeriod::WEEKLY_PERIOD_ID)
    //            ->count();
    //        $entities['biweekly'] = (new $model)->getCollection()
    //            ->addFilter('settlement_cycle_id', $cycle->getId())
    //            ->addFilter('billing_cycle_id',Application_Model_Entity_System_CyclePeriod::BIWEEKLY_PERIOD_ID)
    //            ->count();
    //        $entities['semi_weekly'] = (new $model)->getCollection()
    //            ->addFilter('settlement_cycle_id', $cycle->getId())
    //            ->addFilter('billing_cycle_id',Application_Model_Entity_System_CyclePeriod::SEMI_WEEKLY_PERIOD_ID)
    //            ->count();
    //        $entities['monthly'] = (new $model)->getCollection()
    //            ->addFilter('settlement_cycle_id', $cycle->getId())
    //            ->addFilter('billing_cycle_id',Application_Model_Entity_System_CyclePeriod::MONTHLY_PERIOD_ID)
    //            ->count();
    //        $entities['semi_monthly'] = (new $model)->getCollection()
    //            ->addFilter('settlement_cycle_id', $cycle->getId())
    //            ->addFilter('billing_cycle_id',Application_Model_Entity_System_CyclePeriod::SEMY_MONTHLY_PERIOD_ID)
    //            ->count();
    //        return $entities;
    //    }

}

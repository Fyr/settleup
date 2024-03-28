<?php

class Application_Model_Entity_System_ReserveTransactionTypes extends Application_Model_Base_Entity
{
    final public const CONTRIBUTION = 1;
    final public const WITHDRAWAL = 2;
    final public const CASH_ADVANCE = 3;
    final public const ADJUSTMENT_DECREASE = 4;
    final public const ADJUSTMENT_INCREASE = 5;
}

<?php

/**
 * @method $this staticLoad($id, $field = null)
 */
class Application_Model_Entity_System_PaymentStatus extends Application_Model_Base_Entity
{
    final public const NOT_APPROVED_STATUS = 4;
    final public const APPROVED_STATUS = 3;
    final public const PROCESSED_STATUS = 2;
    final public const VERIFIED_STATUS = 1;
    final public const REVERSED_STATUS = 5;
}

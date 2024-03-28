<?php

class Application_Model_Entity_System_VendorStatus extends Application_Model_Base_Entity
{
    final public const STATUS_ACTIVE = '0';
    final public const STATUS_NOT_ACTIVE = '1';
    final public const STATUS_RESCINDED = '2';
    final public const STATUS_NOT_CONFIGURED = '-1';
}

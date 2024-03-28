<?php

namespace Migrate\Strategy;

use Application_Model_Entity_Entity_Carrier as Carrier;

class BaseStrategy
{
    /** @var Carrier */
    protected $carrier;
    protected $carrierKey;

    public function setCarrier(Carrier $carrier)
    {
        $this->carrier = $carrier;
    }

    public function setCarrierKey($carrierKey)
    {
        $this->carrierKey = $carrierKey;
    }
}

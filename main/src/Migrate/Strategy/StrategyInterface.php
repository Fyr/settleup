<?php

namespace Migrate\Strategy;

use Application_Model_Entity_Entity_Carrier as Carrier;

interface StrategyInterface
{
    public function migrate();

    public function setCarrier(Carrier $carrier);

    public function setCarrierKey($carrierKey);
}

<?php

namespace Migrate;

use Application_Model_Entity_Entity_Carrier as Carrier;

use Migrate\Strategy\StrategyInterface;

class Migrate
{
    /** @var Carrier */
    protected $carrier;
    protected $carrierKey;
    protected $strategies = [];

    public function __construct(Carrier $carrier, $carrierKey)
    {
        $this->carrier = $carrier;
        $this->carrierKey = $carrierKey;
    }

    public function addStrategy(StrategyInterface $strategy)
    {
        $this->strategies[] = $strategy;
    }

    public function migrate()
    {
        foreach ($this->strategies as $strategy) {
            /** @var StrategyInterface $strategyClass */
            $strategyClass = new $strategy();
            $strategyClass->setCarrier($this->carrier);
            $strategyClass->setCarrierKey($this->carrierKey);
            $strategyClass->migrate();
        }
    }
}

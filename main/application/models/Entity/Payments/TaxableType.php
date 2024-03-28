<?php

class Application_Model_Entity_Payments_TaxableType extends Application_Model_Base_Entity
{
    final public const TYPE_NO = 0;
    final public const TYPE_YES = 1;
    final public const VALUE_NO = 'No';
    final public const VALUE_YES = 'Yes';

    public function getTaxableOptions(): array
    {
        return [
            self::TYPE_NO => self::VALUE_NO,
            self::TYPE_YES => self::VALUE_YES,
        ];
    }
}

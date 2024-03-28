<?php

class Application_Model_Entity_System_DivisionTitle extends Application_Model_Base_Entity
{
    final public const DIVISION_LINEHAUL = 'Linehaul operations';
    final public const DIVISION_PUD_ICS = 'PUD ICs';
    final public const DIVISION_INTERMODAL = 'Intermodal';

    public function getALl(): array
    {
        return [
            self::DIVISION_LINEHAUL,
            self::DIVISION_PUD_ICS,
            self::DIVISION_INTERMODAL,
        ];
    }
}

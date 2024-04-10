<?php

class Application_Model_Entity_System_ReserveAccountType extends Application_Model_Base_Entity
{
    final public const ESCROW_ACCOUNT = 1;
    final public const MAINTENANCE_ACCOUNT = 2;

    public function getAccountTypeOptions(): array
    {
        // returns [1 => ''ESCROW_ACCOUNT",  2 => "MAINTENANCE_ACCOUNT"]
        $reflection = new ReflectionClass($this);
        $constants = $reflection->getConstants();
        $result = [];
        foreach ($constants as $name => $value) {
            if ($reflection->getReflectionConstant($name)->getDeclaringClass()->getName() === self::class) {
                $result[$value] = $name;
            }
        }

        return $result;
    }
}

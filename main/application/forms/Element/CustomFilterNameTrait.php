<?php

trait Application_Form_Element_CustomFilterNameTrait
{
    public function filterName($value, $allowBrackets = false)
    {
        return parent::filterName($value, true);
    }
}

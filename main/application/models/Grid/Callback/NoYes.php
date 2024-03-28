<?php

class Application_Model_Grid_Callback_NoYes
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body(): ?string
    {
        if (!isset($this->column)) {
            return '-';
        }

        return match ($this->column) {
            '0' => 'No',
            '1' => 'Yes',
            default => $this->column,
        };
    }
}

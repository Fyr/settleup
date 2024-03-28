<?php

class Application_Model_Grid_Callback_DivisionName
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body(): ?string
    {
        $data = null;
        if (isset($this->row['division_id'], $this->row['division_name'])) {
            $data .= '<a href="/carriers_index/edit/id/' . $this->row['division_id'] . '"> '
                . $this->row['division_name'] . '</a>';
        }

        return $data;
    }
}

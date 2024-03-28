<?php

trait Application_Model_Entity_SoftDeleteTrait
{
    public function delete()
    {
        $this->setDeleted(1);
        $this->save();

        return $this;
    }
}

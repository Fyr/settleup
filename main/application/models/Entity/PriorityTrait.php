<?php

trait Application_Model_Entity_PriorityTrait
{
    /**
     * @param $id int
     * @param $priority int
     * @return $this
     */
    public function updatePriority($id, $priority)
    {
        return $this->getResource()->update(['priority' => $priority], ['id = ?' => $id]);
    }

    /**
     * @param $whereColumns array
     * @return $this
     */
    public function reorderPriority($whereColumns = null)
    {
        if (is_null($whereColumns)) {
            $whereColumns = [];
        }
        /** @var $select */
        $select = $this->getResource()->select();

        foreach ($whereColumns as $column) {
            $select->where($column . ' = ?', $this->getData($column));
        }
        $select->where('deleted = ?', 0)->where('priority IS NOT NULL')->order('priority', 'asc');

        $items = $select->getAdapter()->query($select)->fetchAll();

        foreach ($items as $priority => $item) {
            if ($item['priority'] != $priority) {
                $this->updatePriority($item['id'], $priority);
            }
        }

        return $this;
    }
}

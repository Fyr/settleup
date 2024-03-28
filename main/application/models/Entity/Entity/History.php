<?php

class Application_Model_Entity_Entity_History extends Application_Model_Base_Entity
{
    final public const TYPE_ENTITY = 1;
    /**
     * @var Application_Model_Entity_Settlement_Cycle
     */
    protected $cycle;

    /**
     * @param Application_Model_Entity_Settlement_Cycle|null $cycle
     */
    public function __construct($cycle = null)
    {
        if (is_null($cycle)) {
            $cycle = new Application_Model_Entity_Settlement_Cycle();
        }
        $this->cycle = $cycle;

        parent::__construct();
    }

    /**
     * @return Application_Model_Entity_Settlement_Cycle
     */
    public function getCycle()
    {
        return $this->cycle;
    }

    public function get($id, $type)
    {
        return $this->load([
            'cycle_id' => $this->getCycle()->getId(),
            'entity_id' => $id,
            'type_id' => $type,
        ]);
        //        $history = $this->getCollection()
        //            ->addFilter('cycle_id', $this->getCycle()->getId())
        //            ->addFilter('entity_id', $id)
        //            ->addFilter('type_id', $type)
        //            ->getFirstItem();
        //        $this->addData($history->getData());
        //        return $history;
    }

    public function set($id, $type, $data)
    {
        $this->setData([
            'entity_id' => $id,
            'type_id' => $type,
            'data' => json_encode($data, JSON_THROW_ON_ERROR),
            'cycle_id' => $this->cycle->getId(),
        ]);

        $this->save();

        return $this;
    }

    /**
     * @param $id
     * @return bool|mixed
     */
    public function getEntity($id)
    {
        $this->get($id, self::TYPE_ENTITY);

        return $this->getEntityData();
    }

    public function setEntity($data)
    {
        return $this->set($data['entity_id'], self::TYPE_ENTITY, $data);
    }

    public function getEntityData()
    {
        $result = false;
        if ($this->getId()) {
            $result = json_decode((string) $this->getData('data'), true, 512, JSON_THROW_ON_ERROR);
        }

        return $result;
    }
}

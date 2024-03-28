<?php

class Application_Plugin_CycleContractorsPaginatorAdaptor implements Zend_Paginator_Adapter_Interface
{
    /**
     * Cycle
     *
     * @var array
     */
    protected $cycle = null;
    /**
     * Item count
     *
     * @var int
     */
    protected $count = null;

    /**
     * Constructor.
     *
     * @param array $array Array to paginate
     */
    public function __construct(Application_Model_Entity_Settlement_Cycle $cycle, protected $sort, protected $order, protected $filterParams)
    {
        $this->cycle = $cycle;
        $this->count = $this->cycle->getSettlementContractorsCount($this->sort, $this->order, $this->filterParams);
    }

    /**
     * Returns an array of items for a page.
     *
     * @param int $offset Page offset
     * @param int $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        return $this->cycle->getSettlementContractors(
            $this->sort,
            $this->order,
            $this->filterParams,
            null,
            $itemCountPerPage,
            $offset
        );
    }

    /**
     * Returns the total number of rows in the array.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->count;
    }
}

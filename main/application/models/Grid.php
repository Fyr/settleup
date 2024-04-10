<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Settlement_Cycle as Cycle;

class Application_Model_Grid extends Application_Model_Base_Object
{
    public const FIRST_PAGE = 1;
    private array $limitArray;
    private string $_debugString = '';
    public string $currentControllerName;
    protected $pager;
    public $sessionStorage = null;
    public $controllerStorage = null;
    protected $rewriteColumns = [];

    /**
     * Setup a settings for a grid
     *
     * @param null|object|string $entityName
     *   - Entity or Entity class name which data will be displayed
     * @param null|array $header
     *   - Settings for columns titles
     * @param null|array $massaction
     *   - Settings for massaction buttons
     * @param null|array $customFilters
     *   - Array of name of collection filters
     * @param null|array $buttons
     *   - Settings for buttons that will be displayed under grid
     * @param null|array $filter
     *   - Array of installed filters
     * @param null|int $limit
     *   - Value of count records per page
     * @param null|int $currentPage
     *   - Current page
     */
    public function __construct(
        $entityName = null,
        $header = null,
        $massaction = null,
        $customFilters = null,
        $buttons = null,
        $filter = null,
        $limit = null,
        $currentPage = null
    ) {
        if (!$entityName) {
            return $this;
        }
        $this->setEntityName($entityName);
        $this->setHeader($header);
        $this->setButtons($buttons);
        $this->setMassaction($massaction);
        $this->setCustomFilters($customFilters);
        $this->setFilter($filter);
        $this->setLimit($limit);
        $this->setCurrentPage($currentPage);
        $this->limitArray = [25, 50, 100, 'All'];
        $this->setLimits($this->limitArray);
        //$this->setOrder($order);

        $this->setQuckEdit();
        $this->setGridId($header['id'] ?? uniqid());
        if (isset($header['dragrows']) && $header['dragrows'] && !isset($header['idField'])) {
            $header['idField'] = 'id';
            $this->setHeader($header);
        }

        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setSort($data)
    {
        $header = $this->getHeader();
        $header['sort'] = $data;
        $this->setHeader($header);
        $this->controllerStorage['sort'] = $data;

        return $this;
    }

    public function getPaginator()
    {
        return $this->getPagerForEntity();
    }

    public function getDebugString(): string
    {
        return $this->_debugString;
    }

    public function getLimitArray()
    {
        return $this->limitArray;
    }

    public function getPagerForEntity()
    {
        if (!isset($this->pager)) {
            /** @var Application_Model_Base_Collection $entityCollection */
            $entityCollection = $this->getCollection();

            if (isset($this->rewriteColumns) && count($this->rewriteColumns)) {
                $entityCollection->setRewriteColumns($this->rewriteColumns);
            }

            //$sort = $this->getSort();
            $header = $this->getSortData();
            if (!empty($header) && is_array($header['sort'])) {
                foreach ($header['sort'] as $key => $value) {
                    $entityCollection->setOrder($key, $value);
                }
            }

            /*if (!empty($order)) {
                foreach ($order as $key => $value) {
                    $entityCollection->setOrder($key, $value);
                }
            }*/

            $customFilters = $this->getCustomFilters();

            if (is_array($customFilters)) {
                foreach ($customFilters as $customFilter) {
                    if (is_array($customFilter)) {
                        $entityCollection->{$customFilter['name']}($customFilter['value']);
                    } else {
                        $entityCollection->$customFilter();
                    }
                }
            }

            $filter = $this->getPropertyData('filter');
            if (is_array($this->getResetFilters())) {
                foreach ($this->getResetFilters() as $filterName) {
                    if (isset($filter[$filterName])) {
                        unset($filter[$filterName]);
                        if ($filterName = 'addFilterByEntityId') {
                            if (isset($this->controllerStorage['entity'])) {
                                unset($this->controllerStorage['entity']);
                            }
                            if (isset($this->sessionStorage->gridData[$this->getGridId()]['entity'])) {
                                unset($this->sessionStorage->gridData[$this->getGridId()]['entity']);
                            }
                        }
                    }
                }
            }
            if (isset($filter['settlement_cycle_id_filter'])) {
                $filter['settlement_cycle_id'] = $filter['settlement_cycle_id_filter'];
                unset($filter['settlement_cycle_id_filter']);
            }
            if (isset($filter['settlement_cycle_filter_year'])) {
                unset($filter['settlement_cycle_filter_year']);
            }
            if (isset($filter['addFilterByEntityId'])) {
                $entityCollection->addFilterByEntityId($filter['addFilterByEntityId']);
                unset($filter['addFilterByEntityId']);
            }
            if (isset($filter['defaultFilters'])) {
                foreach ($filter['defaultFilters'] as $startFilter) {
                    if (!isset($filter[$startFilter[0]])) {
                        $entityCollection->addFilter(
                            array_shift($startFilter),
                            array_shift($startFilter),
                            array_shift($startFilter),
                            array_shift($startFilter),
                            array_shift($startFilter),
                            array_shift($startFilter)
                        );
                    }
                }
                unset($filter['defaultFilters']);
                $currentFilters = $this->getFilter();
                unset($currentFilters['defaultFilters']);
                $this->setFilter($currentFilters);
            }

            if ($entityCollection instanceof Application_Model_Entity_Collection_Deductions_Deduction || $entityCollection instanceof Application_Model_Entity_Collection_Payments_Payment || $entityCollection instanceof Application_Model_Entity_Collection_Accounts_Reserve_Transaction || $entityCollection instanceof Application_Model_Entity_Collection_Transactions_Disbursement

            ) {
                if (Zend_Controller_Front::getInstance()->getRequest()->getCookie('settlement_cycle_id') === '0') {
                    $filter['settlement_cycle_id'] = 0;
                } elseif (($cycleId = $this->getSettlementCycleId()) !== null) {
                    $filter['settlement_cycle_id'] = $cycleId;
                }
            }

            $numericFields = $entityCollection->getEntity()->getResource()->getNumericFields();
            if (!empty($filter)) {
                foreach ($filter as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $item) {
                            if ($item == '') {
                                unset($value);
                            }
                        }
                        if (isset($value)) {
                            $entityCollection->addFilter(
                                array_shift($value),
                                array_shift($value),
                                array_shift($value),
                                array_shift($value),
                                array_shift($value),
                                array_shift($value)
                            );
                        }
                    } else {
                        if ($value !== '') {
                            if ($key != 'settlement_cycle_id') {
                                if ($key != 'settlement_cycle_filter_type') {
                                    if ($key == 'priority') {
                                        --$value;
                                    }
                                    if ($key == 'billing_title') {
                                        $entityCollection->addFilter('recurring', 1);
                                    }
                                    if (array_search($key, $numericFields)) {
                                        $entityCollection->addFilter(
                                            $key,
                                            $value,
                                            '='
                                        );
                                    } else {
                                        $entityCollection->addFilter(
                                            $key,
                                            $value,
                                            'LIKE'
                                        );
                                    }
                                }
                            } else {
                                $cycleFilter = $entityCollection->getFilter(
                                    'settlement_cycle_id'
                                );
                                if ($cycleFilter) {
                                    $cycleFilter->setValue($value);
                                    $cycleFilter->setOp('=');
                                } else {
                                    if ($entityCollection instanceof Application_Model_Entity_Collection_Settlement_Cycle) {
                                        $entityCollection->addFilter(
                                            'id',
                                            $value
                                        );
                                    } else {
                                        $entityCollection->addFilter(
                                            'settlement_cycle_id',
                                            $value
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $this->setLimit($this->getPropertyData('limit'));
            if ($this->getLimit() === null) {
                $this->setLimit($this->limitArray[0]);
                if (isset($header['pagination']) && $header['pagination'] == false) {
                    $this->setLimit(1_000_000);
                }
            }

            //            $this->setCurrentPage($this->getPropertyData('current_page'));
            if ($this->getCurrentPage() == null) {
                $this->setCurrentPage(1);
            }
            $paginator = new Zend_Paginator(
                new Zend_Paginator_Adapter_DbSelect(
                    $select = $entityCollection->getSelectBeforeLoad()
                )
            );
            $paginator->setItemCountPerPage($this->getLimit())->setCurrentPageNumber($this->getCurrentPage());

            $this->setSelect(clone($select));

            $this->pager = $paginator;
        }

        return $this->pager;
    }

    public function getTotals()
    {
        if (!$this->getData('totals')) {
            /** @var Zend_Db_Table_Select $select */
            $select = $this->getSelect();
            if ((int)$this->getLimit()) {
                $select->limitPage(
                    $this->getCurrentPage(),
                    $this->getLimit()
                );
            }
            $totalFields = '';
            $adapter = $select->getAdapter();
            if ($this->currentControllerName == 'transactions_disbursement') {
                $totalFields = 'SUM(totalresult.amount) as totals';
            } elseif ($this->currentControllerName == 'payments_payments') {
                $totalFields = 'SUM(totalresult.amount) as totals';
            } elseif ($this->currentControllerName == 'deductions_deductions') {
                $totalFields = 'SUM(totalresult.amount) as total_paid, SUM(totalresult.balance) as total_balance, SUM(totalresult.adjusted_balance) as total_original_amount, SUM(totalresult.transaction_fee) as total_transaction_fee';
            }
            if ($totalFields) {
                $stmt = $adapter->query('select ' . $totalFields . ' FROM (' . $select . ') as totalresult ');
                $stmt->execute();

                $this->setData('totals', $stmt->fetchAll()[0]);
            }
        }

        return $this->getData('totals');
    }

    private function getCollection($getNewCollection = false)
    {
        if ($getNewCollection || !$this->getData('collection')) {
            $entity = $this->getEntityName();
            if (is_string($entity)) {
                $entity = new $entity();
            }
            $collection = $entity->getCollection();
            if (!$getNewCollection) {
                $this->setData('collection', $collection);
            }
        }

        if ($getNewCollection) {
            return $collection;
        } else {
            return $this->getData('collection');
        }
    }

    /**
     * @param $beforeLists
     * @param $resultLists
     * @param $currentPage
     */
    public function setPriority($beforeLists, $resultLists, $currentPage)
    {
        $priorityArray = [];

        $entityName = $this->getEntityName();
        $entity = new $entityName();
        $limit = $this->controllerStorage['limit'] ?? $this->getLimits()[0];

        $entityCollection = $entity->getCollection();

        foreach ($this->getCustomFilters() as $customFilter) {
            if (is_array($customFilter)) {
                $entityCollection->{$customFilter['name']}($customFilter['value']);
            } else {
                $entityCollection->$customFilter();
            }
        }
        $entityCollection = $entityCollection->setOrder(
            'priority',
            Application_Model_Base_Collection::SORT_ORDER_ASC
        )->getItems();
        foreach ($beforeLists as $key => $value) {
            $beforeList = $beforeLists[$key];
            $resultList = $resultLists[$key];
            if (isset($beforeList) && isset($resultList)) {
                if ((is_countable($beforeList) ? count($beforeList) : 0) != 1 && (is_countable($resultList) ? count($resultList) : 0) != 1) {
                    foreach ($resultList as $priority => $id) {
                        if ($beforeList[$priority] == $id) {
                            unset($resultList[$priority]);
                            unset($beforeList[$priority]);
                        }
                    }
                }

                if ($currentPage != self::FIRST_PAGE) {
                    $resultList = array_flip($resultList);
                    $increase = (--$currentPage) * $limit;
                    foreach ($resultList as $id => $priority) {
                        $resultList[$id] = $priority + $increase;
                    }
                    $resultList = array_flip($resultList);
                }

                foreach ($resultList as $priority => $id) {
                    $entityCollection[$id]->setData('priority', $priority)->save();
                }
            }

            // only for transactions
            if ($entity instanceof Application_Model_Entity_Accounts_Reserve_Transaction || $entity instanceof Application_Model_Entity_Deductions_Deduction || $entity instanceof Application_Model_Entity_Accounts_Reserve_Powerunit) {
                if (is_countable($resultList) ? count($resultList) : 0) {
                    $cycle = null;
                    $contractorIds = [];
                    foreach ($resultList as $transaction) {
                        if ($cycle == null) {
                            $cycle = $entityCollection[$transaction]->getSettlementCycleId();
                        }
                        if ($entity instanceof Application_Model_Entity_Accounts_Reserve_Powerunit) {
                            $contractorIds[$entityCollection[$transaction]->getReserveAccountEntity()->getEntityId(
                            )] = $entityCollection[$transaction]->getReserveAccountEntity()->getEntityId();
                        } else {
                            $contractorIds[$entityCollection[$transaction]->getContractorId(
                            )] = $entityCollection[$transaction]->getContractorId();
                        }
                    }

                    foreach ($contractorIds as $contractorId) {
                        $entity->reorderPriority($cycle, $contractorId);
                        if ($entity instanceof Application_Model_Entity_Accounts_Reserve_Powerunit) {
                            $contractor = Application_Model_Entity_Entity_Contractor::staticLoad(
                                $contractorId,
                                'entity_id'
                            );
                            $contractor->setRacPriority(Application_Model_Entity_Entity_Contractor::PRIORITY_CUSTOM);
                            $contractor->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * Sets array of fields, that should have quick edit ability
     * $header['quickEdit']:
     *  1. array() - for all fields;
     *  2. array('example1', 'example2')
     *   - only for 'example1' and  'example2' fields;
     *  3. array('EXCEPT', 'example1', 'example2')
     *   - for all fields, except 'example1' and  'example2';
     *
     * @return Application_Model_Grid
     */
    protected function setQuckEdit()
    {
        $fields = [];
        $header = $this->getHeader();
        if (array_key_exists('quickEdit', $header)) {
            $quickEdit = $header['quickEdit'];
            if (empty($quickEdit)) {
                $fields = array_keys($header['header']);
            } else {
                if (is_int(array_search('EXCEPT', $quickEdit))) {
                    $infoFields = array_keys($header['header']);
                    $fields = array_diff($infoFields, $quickEdit);
                } else {
                    $fields = $quickEdit;
                }
            }
        }
        unset($fields['id']);
        $this->setQuickEditFields($fields);

        return $this;
    }

    public function saveQuickEdit()
    {
        $entityName = $this->getEntityName();
        $recordId = $this->getRecordId();
        $field = $this->getField();
        $value = $this->getValue();

        $entity = new $entityName();
        $entity->load($recordId, 'id');
        $entity->setData($field, $value);
        $entity->save();

        return $entity->getData($field);
    }

    public function getControllerDataStorage()
    {
        if (!$this->sessionStorage) {
            $this->sessionStorage = Zend_Auth::getInstance()->getStorage()->read();
            $this->currentControllerName = $this->sessionStorage->currentControllerName;
            if (!property_exists($this->sessionStorage, 'gridData')) {
                $this->sessionStorage->gridData = [];
            }
            if (!array_key_exists($this->getGridId(), $this->sessionStorage->gridData)) {
                $this->sessionStorage->gridData[$this->getGridId()] = [];
            }

            $this->controllerStorage = $this->sessionStorage->gridData[$this->getGridId()];
        }

        return $this->sessionStorage;
    }

    public function getPropertyData($property)
    {
        $propertyData = $this->getData($property);
        if ($propertyData === 'null') {
            $propertyData = null;
        }
        $storage = $this->getControllerDataStorage();
        if (empty($propertyData) && ($storage->isNotGridRequest || $storage->isLimitAction)) {
            if (array_key_exists($property, $this->controllerStorage)) {
                $propertyData = $this->controllerStorage[$property];
            } else {
                $propertyData = null;
            }
            $this->setData($property, $propertyData);
        } else {
            $storage->gridData[$this->getGridId()][$property] = $propertyData;
        }

        return $propertyData;
    }

    public function getSortData()
    {
        $header = $this->getHeader();
        if (!empty($header)) {
            $storage = $this->getControllerDataStorage();
            if ($storage->isNotGridRequest) {
                if (array_key_exists('sort', $this->controllerStorage)) {
                    $header['sort'] = $this->controllerStorage['sort'];
                } else {
                    if (array_key_exists('sort', $header)) {
                        $storage->gridData[$this->getGridId()]['sort'] = $header['sort'];
                    } else {
                        $header['sort'] = [];
                    }
                }
            } else {
                $storage->gridData[$this->getGridId()]['sort'] = $header['sort'];
            }
        }
        $this->setHeader($header);

        return $header;
    }

    public function getSettlementCycleId()
    {
        return $this->getSettlementCycle()->getId();
    }

    public function getSettlementCycle()
    {
        return User::getCurrentUser()->getCurrentCycle();
    }

    public function getSettlementCycleFilterType()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();

        return $request->getCookie(
            'settlement_cycle_filter_type',
            Cycle::CURRENT_FILTER_TYPE
        );
    }

    public function getSettlementCycleFilterYear()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();

        return $request->getCookie('settlement_cycle_filter_year', null);
    }

    public function getSettlementCycleStatus()
    {
        $settlementCycle = new Cycle();

        return $settlementCycle->load($this->getSettlementCycleId())->getStatusId();
    }

    public function getGridSettings()
    {
        $filters = $this->getFilter();
        if (isset($filters['defaultFilters'])) {
            unset($filters['defaultFilters']);
        }

        return [
            'entity' => $this->getEntityName(),
            'header' => $this->getHeader(),
            'massaction' => $this->getMassaction(),
            'customFilters' => $this->getCustomFilters(),
            'filter' => $filters,
            'cycle' => $this->issetCycle(),
        ];
    }

    public function issetCycle()
    {
        return ($this->getCycle()) ? true : false;
    }

    public function hideButtons()
    {
        $result = false;
        $collection = $this->getCollection();
        if ($collection instanceof Application_Model_Entity_Collection_Payments_Payment || $collection instanceof Application_Model_Entity_Collection_Deductions_Deduction || $collection instanceof Application_Model_Entity_Collection_Accounts_Reserve_Transaction) {
            $result = true;
        }

        return $result;
    }

    public function getFilter($name = false)
    {
        if ($name) {
            if (isset($this->getData('filter')[$name])) {
                $filter = $this->getData('filter')[$name];
            } else {
                $filter = false;
            }
        } else {
            $filter = $this->getData('filter');
        }

        return $filter;
    }
}

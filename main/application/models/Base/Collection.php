<?php

class Application_Model_Base_Collection implements
    IteratorAggregate,
    Countable,
    Application_Model_Base_Interfaces_Collection
{
    public const SORT_ORDER_ASC = 'ASC';
    public const SORT_ORDER_DESC = 'DESC';
    public const WHERE_TYPE_AND = 'AND';
    public const WHERE_TYPE_OR = 'OR';
    /**
     * @var Application_Model_Base_Entity
     */
    protected $_entity;
    /**
     * @var Zend_Db_Table_Select
     */
    protected $_select;
    protected $_join = [];
    protected $_from = [];
    protected $_group = [];
    protected $_filters = [];
    protected $_orders = [];
    protected $_items = [];
    protected $_limit = [];
    protected $_columns = [];
    protected $_rewriteColumns = [];
    protected $_objectClass = 'Application_Model_Base_Object';
    protected $_loaded = false;

    public function __construct(Application_Model_Base_Entity $entity)
    {
        $this->_entity = $entity;
        $this->_select = $this->_entity->getResource()->select()->setIntegrityCheck(false);
    }

    /**
     * Add collection filter
     *
     * @param string $field
     * @param string|array $value
     * @param string $op =, !=, >, <, etc
     * @param bool $escape - determines escape value or not
     * @param string $type - type of where - 'and' or 'or'
     * @param bool $union
     *   - collect where to union ( where(... and ...) )
     *   or not (where ... and .... )
     * @return Application_Model_Base_Collection
     */
    public function addFilter(
        $field,
        $value,
        $op = '=',
        $escape = true,
        $type = self::WHERE_TYPE_AND,
        $union = false
    ) {
        $filter = new $this->_objectClass();
        $filter->setField($field)->setValue($value)->setOp($op)->setEscape($escape)->setType($type)->setUnion($union);
        array_push($this->_filters, $filter);

        return $this;
    }

    /**
     * Load data
     *
     * @param string $itemsKey - entity field to be used as an items key in array
     * @return Application_Model_Base_Collection
     */
    public function load($itemsKey = 'id')
    {
        if ($this->_loaded === false) {
            $this->_beforeLoad();
            $this->_applyFrom();
            $this->_applyJoin();
            $this->_applyFilters();
            $this->_applyGroup();
            $this->_applyOrders();
            $this->_applyLimit();
            $this->_applyColumns();

            if ($data = $this->_getData()) {
                foreach ($data as $row) {
                    $class = $this->_entity::class;
                    $object = new $class();
                    $object->setData($row);
                    $object->setOrigData();

                    $this->addItem($object, $itemsKey);
                }
            }

            $this->_afterLoad();

            $this->_loaded = true;
        }

        return $this;
    }

    public function addColumn($column)
    {
        $this->_columns[] = $column;

        return $this;
    }

    /**
     * Get all data array for collection
     *
     * @return array|null
     */
    protected function _getData()
    {
        return $this->_entity->getResource()->fetchAll($this->getSelect())->toArray();
    }

    /**
     * Apply sql conditions to select
     *
     * @return Application_Model_Base_Collection
     */
    protected function _applyFilters()
    {
        $baseCond = '';
        $baseType = '';

        foreach ($this->_filters as $filter) {
            if (!strpos((string) $filter->getField(), '.')) {
                if (isset($this->getRewriteColumns()[$filter->getField()])) {
                    $filter->setField($this->getRewriteColumns()[$filter->getField()]);
                } else {
                    $filter->setField($this->setTableName($filter->getField()));
                }
            }

            $type = ($filter->getType() == self::WHERE_TYPE_AND) ? 'where' : 'orWhere';

            $union = $filter->getUnion();

            if (!$union && !empty($baseCond)) {
                $baseCond .= ')';
                $this->getSelect()->$baseType($baseCond);
                $baseCond = '';
            }

            switch (strtoupper((string) $filter->getOp())) {
                case 'IN':
                    $values = $this->getFilteredValue($filter);
                    if (!(is_countable($values) ? count($values) : 0)/*|| !array_sum($filter->getValue())*/) {
                        //throw new Exception('IN values can\'t be empty');
                        continue 2;
                    }
                    $cond = $filter->getField() . ' ' . $filter->getOp() . ' (' . implode(',', $values) . ')';
                    break;
                case 'IN-STRING':
                    $values = $this->getFilteredValue($filter);
                    if (!(is_countable($values) ? count($values) : 0)/*|| !array_sum($filter->getValue())*/) {
                        //throw new Exception('IN values can\'t be empty');
                        continue 2;
                    }
                    $cond = $filter->getField() . ' IN' . " ('" . implode("','", $values) . "')";
                    break;
                case 'NOT IN':
                    $values = $this->getFilteredValue($filter);
                    if (!(is_countable($values) ? count($values) : 0)) {
                        throw new Exception('NOT IN values can\'t be empty');
                    }
                    $cond = $filter->getField() . ' ' . $filter->getOp() . ' (' . implode(',', $values) . ')';
                    break;
                case 'BETWEEN DATE':
                    if ((is_countable($filter->getValue()) ? count($filter->getValue()) : 0) != 2) {
                        throw new Exception(
                            'BETWEEN DATE values must have two parameters'
                        );
                    }
                    $values = $filter->getValue();
                    //todo fix time
                    //$currentTime = new Zend_Date();
                    //$currentTime = $currentTime->toString('hh:mm:ss');
                    $cond = $filter->getField() . " BETWEEN STR_TO_DATE('" . array_shift(
                        $values
                    ) . " 00:00:00', '%Y-%m-%d %H:%i:%s' ) AND STR_TO_DATE('" . array_shift(
                        $values
                    ) . " 23:59:59', '%Y-%m-%d %H:%i:%s')";
                    break;
                case 'GTE DATE':
                    $cond = $filter->getField() . " >= STR_TO_DATE('" . $filter->getValue(
                    ) . " 00:00:00', '%Y-%m-%d %H:%i:%s' )";
                    break;
                case 'LTE DATE':
                    $cond = $filter->getField() . " <= STR_TO_DATE('" . $filter->getValue(
                    ) . " 23:59:59', '%Y-%m-%d %H:%i:%s' )";
                    break;
                case 'LIKE':
                    $cond = "LOWER (" . $filter->getField() . ") " . $filter->getOp() . " LOWER('%" . $filter->getValue(
                    ) . "%')";
                    break;
                case 'IS NOT NULL':
                    $cond = $filter->getField() . " IS NOT NULL";
                    break;
                default:

                    if ($filter->getEscape()) {
                        $filter->setValue('\'' . $filter->getValue() . '\'');
                    }
                    $cond = $filter->getField() . ' ' . $filter->getOp() . ' ' . $filter->getValue();
            }

            if ($union === true) {
                if (!strlen($baseCond)) {
                    $baseCond = '(' . $cond;
                    $baseType = $type;
                } else {
                    $type = ($type == 'where') ? self::WHERE_TYPE_AND : self::WHERE_TYPE_OR;
                    $baseCond .= ' ' . $type . ' ' . $cond;
                }

                if ($filter != end($this->_filters)) {
                    continue;
                }
                $cond = $baseCond . ')';
                $type = $baseType;
            }

            $this->getSelect()->$type($cond);
        }

        return $this;
    }

    /**
     * Apply sql orders to select
     *
     * @return Application_Model_Base_Collection
     */
    protected function _applyOrders()
    {
        foreach ($this->_orders as $order) {
            if (isset($this->getRewriteColumns()[$order->getField()])) {
                $this->getSelect()->order($this->getRewriteColumns()[$order->getField()] . ' ' . $order->getValue());
            } else {
                $this->getSelect()->order($order->getField() . ' ' . $order->getValue());
            }
        }

        return $this;
    }

    protected function _beforeLoad()
    {
    }

    protected function _afterLoad()
    {
    }

    /**
     * Retrieve collection items
     *
     * @param string $itemsKey - entity field to be used as an items key in array
     * @return array
     */
    public function getItems($itemsKey = 'id')
    {
        $this->load($itemsKey);

        return $this->_items;
    }

    public function getField($field = 'id')
    {
        $fieldArr = [];
        foreach ($this->getItems() as $item) {
            $fieldArr[] = $item->getData($field);
        }

        return $fieldArr;
    }

    /**
     * returns select object
     *
     * @return Zend_Db_Table_Select
     */
    public function getSelect()
    {
        return $this->_select;
    }

    public function getSelectBeforeLoad()
    {
        $this->_beforeLoad();
        $this->_applyFrom();
        $this->_applyJoin();
        $this->_applyFilters();
        $this->_applyGroup();
        $this->_applyOrders();
        $this->_applyLimit();

        return $this->getSelect();
    }

    /**
     * Add item to collection
     *
     * @param string $itemsKey - entity field to be used as an items key in array
     * @param Application_Model_Base_Entity $item
     * @return Application_Model_Base_Collection
     */
    public function addItem(Application_Model_Base_Entity $item, $itemsKey)
    {
        $this->_items[$item->getData($itemsKey)] = $item;

        return $this;
    }

    /**
     * Set Select order
     *
     * @param $field
     * @param string $direction
     * @return Application_Model_Base_Collection
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $order = new $this->_objectClass();
        $order->setField($field)->setValue($direction);

        array_push($this->_orders, $order);

        return $this;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Retrieve an external iterator
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing Iterator or
     * Traversable
     */
    public function getIterator(): Traversable
    {
        $this->load();

        return new ArrayIterator($this->_items);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count(): int
    {
        $this->load();

        return is_countable($this->_items) ? count($this->_items) : 0;
    }

    protected function _applyFrom()
    {
        $tableName = $this->_entity->getResource()->getTableName();
        $this->getSelect()->from([$tableName => $tableName]);
    }

    /**
     * @param $thisTable
     * @param $thisField
     * @param $thatTable
     * @param $thatField
     * @param array $columns
     * @return Application_Model_Base_Collection
     */
    public function addFieldsForSelect(
        $thisTable,
        $thisField,
        $thatTable,
        $thatField,
        $columns = [],
        $condition = null,
        $right = false
    ) {
        if (is_object($thisTable)) {
            $thisTable = $thisTable->getResource()->getTableName();
        }

        if (is_object($thatTable)) {
            $thatTable = $thatTable->getResource()->getTableName();
        }

        $join = new $this->_objectClass();

        $join->setThisTable($thisTable)->setThatTable($thatTable)->setThisField($thisField)->setThatField(
            $thatField
        )->setColumns($columns)->setCondition($condition)->setRight($right);

        array_push($this->_join, $join);

        return $this;
    }

    /**
     * apply join to select
     *
     * @return Application_Model_Base_Collection
     */
    protected function _applyJoin()
    {
        foreach ($this->_join as $join) {
            $condition = $join->getCondition();
            if (!$condition) {
                $condition = $join->getThisTable() . '.' . $join->getThisField() . '=' . $join->getThatTable(
                ) . '.' . $join->getThatField();
            }
            if ($join->getRihgt()) {
                $this->getSelect()->joinRight(
                    $join->getThatTable(),
                    $condition,
                    $join->getColumns()
                );
            } else {
                $this->getSelect()->joinLeft(
                    $join->getThatTable(),
                    $condition,
                    $join->getColumns()
                );
            }
        }

        return $this;
    }

    /**
     * returns the first item from collection
     *
     * @return Application_Model_Base_Object
     */
    public function getFirstItem()
    {
        $this->load();

        if (is_countable($this->_items) ? count($this->_items) : 0) {
            reset($this->_items);

            return current($this->_items);
        }

        return new $this->_objectClass();
    }

    /**
     * returns the last item from collection
     *
     * @return Application_Model_Base_Object
     */
    public function getLastItem()
    {
        $this->load();

        if (is_countable($this->_items) ? count($this->_items) : 0) {
            return end($this->_items);
        }

        return new $this->_objectClass();
    }

    /**
     * Add collection group
     *
     * @param string $field
     * @return Application_Model_Base_Collection
     */
    public function addGroup($field)
    {
        $group = new $this->_objectClass();
        $group->setField($field);

        array_push($this->_group, $group);

        return $this;
    }

    /**
     * Apply sql group to select
     *
     * @return Application_Model_Base_Collection
     */
    protected function _applyGroup()
    {
        foreach ($this->_group as $group) {
            $this->getSelect()->group($group->getField());
        }

        return $this;
    }

    /**
     * Add collection limit
     *
     * @param $count - limit count
     * @param null $page - offset (if exist)
     * @return Application_Model_Base_Collection
     */
    public function addLimit($count, $page = null)
    {
        $limit = new $this->_objectClass();
        $limit->setCount($count);
        $limit->setPage($page);

        array_push($this->_limit, $limit);

        return $this;
    }

    protected function _applyLimit()
    {
        foreach ($this->_limit as $limit) {
            if ($limit->getPage()) {
                $this->getSelect()->limitPage(
                    $limit->getPage(),
                    $limit->getCount()
                );
            } else {
                $this->getSelect()->limit($limit->getCount());
            }
        }

        return $this;
    }

    protected function _applyColumns()
    {
        foreach ($this->_columns as $column) {
            $this->getSelect()->columns($column);
        }

        return $this;
    }

    /**
     * @param $name string
     * @return string
     */
    protected function setTableName($name)
    {
        foreach ($this->_join as $join) {
            if (in_array($name, $join->getColumns())) {
                return $join->getThatTable() . '.' . $name;
            } elseif (array_key_exists($name, $join->getColumns())) {
                $fields = $join->getColumns();

                return $join->getThatTable() . '.' . $fields[$name];
            }
        }

        return $this->_entity->getResource()->getTableName() . '.' . $name;
    }

    /**
     * Returns filter object by field that used for filtering
     *
     * @param string $fieldName
     * @return null|Application_Model_Base_Object
     */
    public function getFilter($fieldName)
    {
        foreach ($this->_filters as $filter) {
            if ($filter->getField() == $fieldName) {
                return $filter;
            }
        }

        return null;
    }

    public function getEmptyCollection()
    {
        $this->_items = [];
        $this->_loaded = true;

        return $this;
    }

    /**
     * get collection of not deleted entities
     *
     * @return $this
     */
    public function addNonDeletedFilter($deletedFieldName = null)
    {
        $this->addFilter(
            $deletedFieldName ?: $this->getDeletedFieldName(),
            Application_Model_Entity_System_SystemValues::NOT_DELETED_STATUS,
            '=',
            true,
            Application_Model_Base_Collection::WHERE_TYPE_AND
        );

        return $this;
    }

    /**
     * get name of 'deleted' field
     *
     * @return string
     */
    public function getDeletedFieldName()
    {
        return $this->_entity->getResource()->getTableName() . '.deleted';
    }

    /**
     * Return Entity
     *
     * @return Application_Model_Base_Entity
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    public function getFilteredValue($filter)
    {
        $value = $filter->getValue();
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                if (is_null($item)) {
                    unset($value[$key]);
                }
            }
        }

        return $value;
    }

    public function setRewriteColumns(array $columns)
    {
        $this->_rewriteColumns = $columns;

        return $this;
    }

    public function getRewriteColumns()
    {
        return $this->_rewriteColumns;
    }
}

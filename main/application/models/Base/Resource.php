<?php

abstract class Application_Model_Base_Resource extends Zend_Db_Table_Abstract
{
    protected $_fields;
    protected $_pk;
    protected $_collection;
    final public const RESOURCE_PREFIX = 'Application_Model_Resource';

    public function load(
        Application_Model_Base_Entity $object,
        $value,
        $field = null
    ) {
        if (is_null($field)) {
            $field = $object->getIdFieldName();
        }

        $read = $this->getAdapter();

        if ($read && !is_null($value)) {
            if (!is_array($value)) {
                $value = [$field => $value];
            }
            $select = $this->select();
            foreach ($value as $conditionField => $conditionValue) {
                $select->where($conditionField . ' = ?', $conditionValue);
            }

            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data);
            } else {
                return null;
            }
        }

        return $this;
    }

    public function save(Application_Model_Base_Entity $object)
    {
        if ($object->hasDataChanges()) {
            $write = $this->getAdapter();

            try {
                $write->beginTransaction();

                $this->_prepareDataForSave($object);

                if ($this->_isNewEntity($object)) {
                    $id = parent::insert($object->getData());
                    $object->setId($id);
                } else {
                    parent::update(
                        $object->getData(),
                        $object->getIdFieldName() . ' = ' . $object->getId()
                    );
                }

                $write->commit();
                $object->setDataChanges(false);
            } catch (Exception $e) {
                $write->rollBack();

                throw new Exception($e->getMessage());
            }
        }

        return $this;
    }

    public function del(Application_Model_Base_Entity $object)
    {
        if ($object->getId()) {
            $write = $this->getAdapter();

            try {
                $write->beginTransaction();
                parent::delete(
                    $object->getIdFieldName() . ' = ' . $object->getId()
                );
                $write->commit();
            } catch (Exception $e) {
                $write->rollBack();

                throw new Exception($e->getMessage());
            }
        }

        return $this;
    }

    protected function _getFields()
    {
        if ($this->_fields == null) {
            $this->_fields = $this->info('cols');
        }

        return $this->_fields;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        if ($this->_pk == null) {
            $this->_pk = current($this->info('primary'));
        }

        return $this->_pk;
    }

    protected function _prepareDataForSave(
        Application_Model_Base_Entity $object
    ) {
        foreach ($object->getData() as $k => $v) {
            if (!in_array($k, $this->_getFields())) {
                $object->unsetData($k);
            }
        }

        return $object;
    }

    protected function _isNewEntity(Application_Model_Base_Entity $object)
    {
        if (!array_key_exists($this->getPrimaryKey(), $object->getData()) || $object->getId() == '') {
            return true;
        }

        // check that entity exist
        return is_null($this->load(new $object(), $object->getId()));
    }

    /**
     * @param string $titleField
     * @param null $where
     * @param null $idField
     * @return array
     *   - all rows from table filtered by where clause
     *   in format array( id => title)
     */
    public function getOptions($titleField = 'title', $where = null, $idField = null)
    {
        if (!$idField) {
            $idField = $this->getPrimaryKey();
        }

        $data = $this->fetchAll($where, $titleField . ' ASC');

        $options = [];

        foreach ($data as $entity) {
            $options[$entity[$idField]] = $entity[$titleField];
        }

        return $options;
    }

    public function getTableName()
    {
        return $this->_name;
    }

    /**
     * returns the main info fields of table in following format
     * ( column_name_in_table => column_name_on_frontend )
     *
     * @return array
     */
    public function getInfoFields()
    {
        return [
            $this->getPrimaryKey() => 'Id',
            Application_Model_Base_Entity::TITLE_COLUMN => 'Title',
        ];
    }

    /**
     * Returns array of field name, which contains numeric value
     *
     * @return array
     */
    public function getNumericFields()
    {
        return [
            'id',
            'min_balance',
            'contribution_amount',
            'current_balance',
            'priority',
        ];
    }


    //    public function __construct($config = array())
    //    {
    //        parent::__construct($config);
    //
    //        if (Zend_Db_Table_Abstract::getDefaultMetadataCache() === null)
    //        {
    //            $frontendOptions = array('automatic_serialization' => true);
    //
    //            $zendCacheDir = APPLICATION_PATH .'/../data/cache/'; // directory for caching
    //
    //            $backendOptions  = array('cache_dir' => $zendCacheDir);
    //
    //            $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
    //            Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
    //        }
    //    }
}

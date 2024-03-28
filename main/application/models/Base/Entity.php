<?php

abstract class Application_Model_Base_Entity extends Application_Model_Base_Object implements SplSubject, Stringable
{
    protected $_resourceName;
    protected $_resource;
    protected $_storage;
    protected $_observerEvent = null;
    protected $_titleColumn;
    public const TITLE_COLUMN = 'title';
    public const MODEL_PREFIX = 'Application_Model_Entity';

    public function __construct()
    {
        $this->_storage = new SplObjectStorage();
    }

    public function attach(SplObserver $observer): void
    {
        $this->_storage->attach($observer);
    }

    public function detach(SplObserver $observer): void
    {
        $this->_storage->detach($observer);
    }

    public function notify($class = null): void
    {
        foreach ($this->_storage as $obj) {
            $obj->setMethod($class);
            $obj->update($this);
        }
    }

    public function load($id, $field = null)
    {
        $this->_beforeLoad();
        $this->getResource()->load($this, $id, $field);
        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;

        return $this;
    }

    /**
     * @param string|int|array $id
     * @param null $field
     * @return Application_Model_Base_Entity
     */
    public static function staticLoad($id, $field = null)
    {
        return (new static())->load($id, $field);
    }

    /**
     * @return Application_Model_Base_Entity
     */
    public function save()
    {
        $this->_beforeSave();
        $this->getResource()->save($this);
        $this->_afterSave();

        return $this;
    }

    /**
     * @return Application_Model_Base_Entity
     */
    public function delete()
    {
        $this->_beforeDelete();
        $this->getResource()->del($this);
        $this->_afterDelete();

        return $this;
    }

    protected function _beforeLoad()
    {
    }

    protected function _afterLoad()
    {
    }

    /**
     * @return Application_Model_Base_Entity
     */
    protected function _afterSave()
    {
        $this->notify(__METHOD__);

        return $this;
    }

    /**
     * @return Application_Model_Base_Entity
     */
    protected function _beforeSave()
    {
        $this->notify(__METHOD__);

        return $this;
    }

    protected function _beforeDelete()
    {
        $this->notify(__METHOD__);

        return $this;
    }

    protected function _afterDelete()
    {
    }

    /**
     * @return Application_Model_Base_Resource
     */
    public function getResource()
    {
        if ($this->_resource === null) {
            if (empty($this->_resourceName)) {
                $this->_resourceName = substr(
                    static::class,
                    strpos(
                        static::class,
                        '_',
                        strlen(self::MODEL_PREFIX)
                    ) + 1
                );
            }

            $resource = 'Application_Model_Resource_' . $this->_resourceName;

            $this->_resource = new $resource();
        }

        return $this->_resource;
    }

    /**
     * @return string
     */
    public function getResourceName()
    {
        return $this->_resourceName;
    }

    /**
     * returns array that contains entities
     *
     * @return Application_Model_Base_Collection
     */
    public function getCollection()
    {
        $entityName = substr(
            static::class,
            strpos(
                static::class,
                '_',
                strlen(self::MODEL_PREFIX)
            ) + 1
        );
        $filePrefix = '/models/Entity/Collection/';
        $collectionFileName = APPLICATION_PATH . $filePrefix . str_replace('_', '/', $entityName) . '.php';

        if (file_exists($collectionFileName)) {
            $collectionClassName = 'Application_Model_Entity_Collection_' . $entityName;

            return new $collectionClassName($this);
        }

        return new Application_Model_Base_Collection($this);
    }

    /**
     * return primary key
     *
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->getResource()->getPrimaryKey();
    }

    /**
     * returns a string representation of object
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getData($this->getTitleColumn());
    }

    /**
     * returns the main title column name
     *
     * @return string
     */
    public function getTitleColumn()
    {
        if ($this->_titleColumn == null) {
            $this->_titleColumn = self::TITLE_COLUMN;
        }

        return $this->_titleColumn;
    }

    public function getDefaultValues()
    {
        return $this;
    }

    public function getSetupData($fields = [])
    {
        $data = [];
        if (empty($fields)) {
            $fields = $this->getResource()->getSetupFields();
        }
        foreach ($fields as $field) {
            $data[$field] = $this->getData($field);
        }

        return $data;
    }

    public function decrypt($fields = [])
    {
        $crypt = new Application_Model_Base_CryptAdvanced();
        $key = Application_Model_Entity_Accounts_User::getCurrentUser()->getCarrierKey();
        foreach ($fields as $field) {
            $this->setData($field, $crypt->decrypt($this->getData($field), $key));
        }

        return $this;
    }

    public function markColumnAsDeleted(string $column): self
    {
        $this->setData($column, $this->getData($column) . '_deleted' . time());

        return $this;
    }

    protected function getLogger(): Zend_Log
    {
        return Zend_Registry::get('logger');
    }
}

<?php

class Application_Model_Base_Object
{
    protected $_data = [];
    protected $_hasDataChanges = false;
    protected $_originalData = null;
    protected $_idFieldName = null;

    public function __construct()
    {
        $args = func_get_args();

        if (empty($args[0])) {
            $args[0] = [];
        }

        $this->_data = $args[0];
    }

    public function setIdFieldName($name)
    {
        $this->_idFieldName = $name;

        return $this;
    }

    public function getIdFieldName()
    {
        if (empty($this->_idFieldName)) {
            $this->_idFieldName = 'id';
        }

        return $this->_idFieldName;
    }

    public function getId()
    {
        return $this->getData($this->getIdFieldName());
    }

    public function setId($value)
    {
        $this->setData($this->getIdFieldName(), $value);

        return $this;
    }

    public function addData(array $arr)
    {
        foreach ($arr as $index => $value) {
            $this->setData($index, $value);
        }

        return $this;
    }

    public function appendData(array $arr, $fillEmpty = false)
    {
        $currentData = $this->getData();
        foreach ($arr as $index => $value) {
            if ($fillEmpty) {
                if (!$this->getData($index)) {
                    $this->setData($index, $value);
                }
            } elseif (!array_key_exists($index, $currentData)) {
                $this->setData($index, $value);
            }
        }

        return $this;
    }

    public function setData($key, $value = null)
    {
        $this->_hasDataChanges = true;

        if (is_array($key)) {
            $this->_data = $key;
        } else {
            $this->_data[$key] = $value;
        }

        return $this;
    }

    public function setOrigData($key = null, $data = null)
    {
        if (is_null($key)) {
            $this->_originalData = $this->_data;
        } else {
            $this->_originalData[$key] = $data;
        }

        return $this;
    }

    public function getOriginalData($key = '', $index = null)
    {
        if ('' === $key) {
            return $this->_originalData;
        }

        $default = null;

        // accept a/b/c as ['a']['b']['c']
        if (strpos((string) $key, '/')) {
            $keyArr = explode('/', (string) $key);
            $data = $this->_originalData;
            foreach ($keyArr as $i => $k) {
                if ($k === '') {
                    return $default;
                }
                if (is_array($data)) {
                    if (!isset($data[$k])) {
                        return $default;
                    }
                    $data = $data[$k];
                } elseif ($data instanceof Application_Model_Base_Object) {
                    $data = $data->getData($k);
                } else {
                    return $default;
                }
            }

            return $data;
        }

        if (isset($this->_originalData[$key])) {
            if (is_null($index)) {
                return $this->_originalData[$key];
            }

            $value = $this->_originalData[$key];
            if (is_array($value)) {
                return $value[$index] ?? null;
            }

            if (is_string($value)) {
                $arr = explode("\n", $value);

                return (isset($arr[$index]) && (!empty($arr[$index]) || strlen(
                    $arr[$index]
                ) > 0)) ? $arr[$index] : null;
            }

            if ($value instanceof Application_Model_Base_Object) {
                return $value->getOriginalData($index);
            }

            return $default;
        }

        return $default;
    }

    public function unsetData($key = null)
    {
        $this->_hasDataChanges = true;
        if (is_null($key)) {
            $this->_data = [];
        } else {
            unset($this->_data[$key]);
        }

        return $this;
    }

    public function getData($key = '', $index = null)
    {
        if ('' === $key) {
            return $this->_data;
        }

        $default = null;

        // accept a/b/c as ['a']['b']['c']
        if (strpos((string) $key, '/')) {
            $keyArr = explode('/', (string) $key);
            $data = $this->_data;
            foreach ($keyArr as $i => $k) {
                if ($k === '') {
                    return $default;
                }
                if (is_array($data)) {
                    if (!isset($data[$k])) {
                        return $default;
                    }
                    $data = $data[$k];
                } elseif ($data instanceof Application_Model_Base_Object) {
                    $data = $data->getData($k);
                } else {
                    return $default;
                }
            }

            return $data;
        }

        if (isset($this->_data[$key])) {
            if (is_null($index)) {
                return $this->_data[$key];
            }

            $value = $this->_data[$key];
            if (is_array($value)) {
                return $value[$index] ?? null;
            }

            if (is_string($value)) {
                $arr = explode("\n", $value);

                return (isset($arr[$index]) && (!empty($arr[$index]) || strlen($arr[$index]) > 0)) ? $arr[$index] : null;
            }

            if ($value instanceof Application_Model_Base_Object) {
                return $value->getData($index);
            }

            return $default;
        }

        return $default;
    }

    public function hasDataChanges()
    {
        return $this->_hasDataChanges;
    }

    public function setDataChanges($value)
    {
        $this->_hasDataChanges = (bool)$value;

        return $this;
    }

    public function toString($format = '')
    {
        if (empty($format)) {
            $str = implode(', ', $this->getData());
        } else {
            preg_match_all('/\{\{([a-z0-9_]+)\}\}/is', (string) $format, $matches);
            foreach ($matches[1] as $var) {
                $format = str_replace(
                    '{{' . $var . '}}',
                    $this->getData($var),
                    (string) $format
                );
            }
            $str = $format;
        }

        return $str;
    }

    public function __call($method, $args)
    {
        switch (substr((string) $method, 0, 3)) {
            case 'get':
                $key = self::underscore(substr((string) $method, 3));

                return $this->getData($key, $args[0] ?? null);

            case 'set':
                $key = self::underscore(substr((string) $method, 3));

                return $this->setData(
                    $key,
                    $args[0] ?? null
                );

            case 'uns':
                $key = self::underscore(substr((string) $method, 3));

                return $this->unsetData($key);

            case 'has':
                $key = self::underscore(substr((string) $method, 3));

                return isset($this->_data[$key]);

            case 'col':
                return self::underscore(substr((string) $method, 3));
        }

        throw new Exception(
            'Invalid method ' . static::class . '::' . $method . '(' . print_r($args, 1) . ')'
        );
    }

    public function isEmpty()
    {
        if (empty($this->_data)) {
            return true;
        }

        return false;
    }

    public static function underscore($name)
    {
        return strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", (string) $name));
    }

    protected function _camelize($name)
    {
        return self::uc_words($name, '');
    }

    public static function uc_words($str, $destSep = '_', $srcSep = '_')
    {
        return str_replace(
            ' ',
            $destSep,
            ucwords(str_replace($srcSep, ' ', (string) $str))
        );
    }

    /**
     * @param $fields - Date source
     * @param bool $fromDb - Convert from db format. Default: from UI format.
     */
    public function changeDateFormat($fields, $fromDb = false, $short = false)
    {
        if (is_string($fields)) {
            $fields = [$fields];
        }
        $fromFormat = 'MM/dd/yyyy';
        $toFormat = 'yyyy-MM-dd';
        $toShortFormat = 'yyyy-MM-dd';
        if ($fromDb) {
            $fromFormat = 'yyyy-MM-dd';
            $toFormat = 'MM/dd/yyyy';
            $toShortFormat = 'MM/dd/yy';
        }
        foreach ($fields as $field) {
            if ($this->getData($field) && $this->getData($field) !== '0000-00-00') {
                $wrongDate = new Zend_Date($this->getData($field), $fromFormat);
                $this->setData($field, $wrongDate->toString($short ? $toShortFormat : $toFormat));
            } else {
                $this->setData($field, null);
            }
        }

        return $this;
    }

    /**
     * @param $fields - Date source
     * @param bool $fromDb - Convert from db format. Default: from UI format.
     */
    public function changeDatetimeFormat($fields, $fromDb = false, $short = false)
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }
        $fromFormat = 'MM/dd/yyyy';
        $toFormat = 'yyyy-MM-dd HH:mm:ss';
        $toShortFormat = 'yyyy-MM-dd HH:mm:ss';
        if ($fromDb) {
            $fromFormat = 'yyyy-MM-dd HH:mm:ss';
            $toFormat = 'MM/dd/yyyy HH:mm:ss';
            $toShortFormat = 'MM/dd/yyyy';
        }
        foreach ($fields as $field) {
            $value = $this->getData($field);
            if ($value !== null && $value !== '0000-00-00 00:00:00' && $value !== '') {
                $wrongDate = new Zend_Date($value, $fromFormat);
                $newValue = $wrongDate->toString($short ? $toShortFormat : $toFormat);
                $this->setData($field, $newValue);
            } else {
                $this->setData($field, null);
            }
        }

        return $this;
    }

    public function isDataChanged()
    {
        return $this->_hasDataChanges;
    }
}

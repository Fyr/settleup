<?php

use Application_Model_Entity_System_FileTempStatus as FileTempStatus;

abstract class Application_Model_Base_TempEntity extends Application_Model_Base_Entity
{
    public const DATE_FORMAT = 'MM/dd/yyyy';
    public const DATE_FORMAT_DB = 'yyyy-MM-dd';

    abstract public function check();

    abstract public function getExportCollection($idOrFilters = null);

    abstract public function getControllerName();

    /**
     * @param $fields - Date source
     * @param bool $fromDb - Convert from db format. Default: from UI format.
     * @param bool $short
     * @return $this
     * @throws Zend_Date_Exception
     */
    public function changeDateFormat($fields, $fromDb = false, $short = false): self
    {
        if (is_string($fields)) {
            $fields = [$fields];
        }
        [$fromFormat, $toFormat] = $this->getFromToFormats($fromDb);
        foreach ($fields as $field) {
            if ($this->getData($field) && $this->getData($field) != '0000-00-00') {
                if ($this->checkIsDate($field, $fromDb)) {
                    $wrongDate = new Zend_Date($this->getData($field), $fromFormat);
                    $this->setData($field, $wrongDate->toString($toFormat));
                }
            } else {
                $this->setData($field);
            }
        }

        return $this;
    }

    private function getFromToFormats(bool $fromDb): array
    {
        $fromFormat = self::DATE_FORMAT;
        $toFormat = self::DATE_FORMAT_DB;
        if ($fromDb) {
            $fromFormat = self::DATE_FORMAT_DB;
            $toFormat = self::DATE_FORMAT;
        }

        return [$fromFormat, $toFormat];
    }

    public function checkIsDate(string $field, bool $fromDb = false): bool
    {
        [$fromFormat] = $this->getFromToFormats($fromDb);

        return Zend_Date::isDate($this->getData($field), $fromFormat);
    }

    public function ignoreErrors(): self
    {
        $this->setIgnoreErrors(true);

        return $this;
    }

    public function checkErrors(): bool
    {
        return !$this->getIgnoreErrors();
    }

    public function applyFilters($collection, $filters = null)
    {
        if (is_array($filters)) {
            foreach ($filters as $filter) {
                if (isset($filter['name'])) {
                    $collection->{$filter['name']}($filter['value']);
                } else {
                    $collection->addFilter(
                        array_shift($filter),
                        array_shift($filter),
                        array_shift($filter)
                    );
                }
            }
        }

        return $collection;
    }

    public function setupStatusId(): self
    {
        $this->setStatusId(
            $this->getError()
                ? FileTempStatus::CONST_STATUS_NOT_VALID
                : FileTempStatus::CONST_STATUS_VALID
        );

        return $this;
    }

    public function addError(string $error): self
    {
        $this->setError($this->getError() . $error . '</br>');

        return $this;
    }

    public function addJsonError(string $message, string $field): self
    {
        $data = json_decode((string) $this->getError(), true);
        $data[$field] = $message;
        $this->setError(json_encode($data));

        return $this;
    }

    public function addWarning(string $warning): self
    {
        $this->setWarning($this->getWarning() . $warning . '</br>');

        return $this;
    }

    public function checkDate(?string $value, ?string $name, bool $isRequired = false): self
    {
        $title = ucfirst(str_replace('_', ' ', $name));
        if ($isRequired) {
            if (!$value) {
                $this->addJsonError(
                    $title . ' is required and can not be empty (invalid ' . $title . ')',
                    $name
                );
            }
        }

        if ($value) {
            if (!$this->checkIsDate($name, true)) {
                $this->addJsonError($title . ' is invalid (acceptable: ' . self::DATE_FORMAT . ')', $name);
            }
        }

        return $this;
    }

    public function isTestValue(string $value): bool
    {
        return str_starts_with(strtolower($value), 'test_');
    }
}

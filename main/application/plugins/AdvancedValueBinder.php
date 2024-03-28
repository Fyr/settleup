<?php

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class Application_Plugin_AdvancedValueBinder extends DefaultValueBinder
{
    /**
     * Bind value to a cell
     */
    public function bindValue(Cell $cell, mixed $value = null)
    {
        // sanitize UTF-8 strings
        if (is_string($value)) {
            $value = StringHelper::sanitizeUTF8($value);
        }

        // Set value explicit
        $cell->setValueExplicit($value, static::dataTypeForValue($value));

        // Done!
        return true;
    }

    /**
     * DataType for value
     */
    public static function dataTypeForValue($pValue = null)
    {
        // Match the value against a few data types
        if (is_null($pValue)) {
            return DataType::TYPE_NULL;
        }

        if ($pValue === '') {
            return DataType::TYPE_STRING;
        }

        if ($pValue instanceof RichText) {
            return DataType::TYPE_INLINE;
        }

        if (is_string($pValue) && $pValue[0] === '=' && strlen($pValue) > 1) {
            return DataType::TYPE_FORMULA;
        }

        if (is_bool($pValue)) {
            return DataType::TYPE_BOOL;
        }

        if (is_float($pValue) || is_int($pValue)) {
            return DataType::TYPE_NUMERIC;
        }

        if (preg_match('/^\-?([0-9]+\\.?[0-9]*|[0-9]*\\.?[0-9]+)$/', (string) $pValue)) {
            if (strlen((string) $pValue) > 15) {
                return DataType::TYPE_STRING;
            }

            if (strlen((string) $pValue) > 1 && $pValue != "0.00" && $pValue[0] === '0') {
                return DataType::TYPE_STRING;
            }

            return DataType::TYPE_NUMERIC;
        }

        if (is_string($pValue) && array_key_exists($pValue, DataType::getErrorCodes())) {
            return DataType::TYPE_ERROR;
        }

        return DataType::TYPE_STRING;
    }
}

<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_System_FileStorageType as FileStorageType;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Application_Model_File_Type_Xls extends Application_Model_File_Base
{
    final public const XLS_TYPE = 'xls';
    final public const XLSX_TYPE = 'xlsx';
    protected $objWorksheet;
    public $disableTitle = false;

    public function getContent()
    {
        $objPHPExcel = IOFactory::load(
            $this->getFullFileName()
        );
        //        $objReader->setReadDataOnly(false);
        $this->objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $this->objWorksheet->getHighestRow();
        if ($this->getFileType() == FileStorageType::CONST_PAYMENTS_FILE_TYPE) {
            $columns = static::getPaymentFields();

            for ($row = 2; $row <= $highestRow; $row++) {
                $this->saveTempEntity($row, $columns, $this->extractDateFields($columns));
            }
        }
        if ($this->getFileType() == FileStorageType::CONST_DEDUCTIONS_FILE_TYPE) {
            $columns = static::getDeductionFields();

            for ($row = 2; $row <= $highestRow; $row++) {
                $this->saveTempEntity($row, $columns, $this->extractDateFields($columns));
            }
        }
        if ($this->getFileType() == FileStorageType::CONST_POWERUNIT_FILE_TYPE) {
            $columns = static::getPowerunitFields();

            for ($row = 2; $row <= $highestRow; $row++) {
                $this->saveTempEntity($row, $columns, $this->extractDateFields($columns));
            }
        }
        if ($this->getFileType() == FileStorageType::CONST_VENDOR_FILE_TYPE) {
            $columns = static::getUploadVendorFields();

            for ($row = 2; $row <= $highestRow; $row++) {
                $this->saveTempEntity($row, $columns, $this->extractDateFields($columns));
            }
        }
        if ($this->getFileType() == FileStorageType::CONST_CONTRACTOR_RA_FILE_TYPE) {
            $columns = static::getContractorRAFields();

            for ($row = 2; $row <= $highestRow; $row++) {
                $this->saveTempEntity($row, $columns, $this->extractDateFields($columns));
            }
        }
        if ($this->getFileType() == FileStorageType::CONST_CONTRACTOR_FILE_TYPE) {
            if ($highestRow < 2) {
                return false;
            }
            $contractorColumns = static::getContractorFields();
            $contactColumns = static::getContactFields();
            $contractorVendorColumns = static::getVendorFields();
            $contractorTempId = null;
            $contractors = [];
            for ($row = 2; $row <= $highestRow; $row++) {
                $contractorXlsId = ((string) $this->objWorksheet->getCell('A' . $row)->getValue());
                if ($contractorXlsId != '#' || ($contractorXlsId == '#' && is_null($contractorTempId))) {
                    $tempContractor = $this->saveTempEntity(
                        $row,
                        $contractorColumns,
                        $this->extractDateFields($contractorColumns)
                    ); // array('expires', 'dob', 'start_date', 'termination_date', 'rehire_date')
                    if ($tempContractor) {
                        $contractorTempId = $tempContractor->getId();
                        $contractors[] = $tempContractor;
                    }
                }
                if ($contractorTempId) {
                    foreach ($contactColumns as $contactType => $excelColumn) {
                        if ($contactType == 'address') {
                            $columns = $excelColumn;
                        } else {
                            $columns = [$contactType => $excelColumn];
                        }
                        $this->saveTempEntity(
                            $row,
                            $columns,
                            null,
                            FileStorageType::CONST_CONTRACTOR_CONTACT_PART_TYPE,
                            $contractorTempId
                        );
                    }
                    $this->saveTempEntity(
                        $row,
                        $contractorVendorColumns,
                        null,
                        FileStorageType::CONST_CONTRACTOR_VENDOR_PART_TYPE,
                        $contractorTempId
                    );
                }
            }
            foreach ($contractors as $contractor) {
                $vendorCollection = (new Application_Model_Entity_Entity_ContractorVendorTemp())->getCollection(
                )->addFilter('contractor_temp_id', $contractor->getId())->getItems();
                if (count($vendorCollection)) {
                    $vendorCodes = [];
                    foreach ($vendorCollection as $vendor) {
                        $code = $vendor->getVendorCode();
                        if (array_search($code, $vendorCodes) > -1) {
                            $vendor->addError(
                                'Carrier/Vendor with code "' . $code . '" should be unique!'
                            )->setStatusId(
                                Application_Model_Entity_System_PaymentTempStatus::CONST_STATUS_NOT_VALID
                            )->save();
                        } else {
                            $vendorCodes[] = $code;
                        }
                    }
                }

                if (
                    $contractor->getCorrespondenceMethod(
                    ) == Application_Model_Entity_Entity_Contact_Type::TYPE_EMAIL
                ) {
                    $contactTempEntity = new Application_Model_Entity_Entity_Contact_Temp();
                    $emailContacts = $contactTempEntity->getCollection()->addFilter(
                        'contractor_temp_id',
                        $contractor->getId()
                    )->addFilter('value', null, 'IS NOT NULL')->addFilter('value', '', '!=')->addFilter(
                        'contact_type',
                        Application_Model_Entity_Entity_Contact_Type::TYPE_EMAIL
                    );
                    if (!$emailContacts->count()) {
                        $contactTempEntity->setData([
                            'error' => 'Email is required and can not be empty<br/>',
                            'contractor_temp_id' => $contractor->getId(),
                            'status_id' => Application_Model_Entity_System_PaymentTempStatus::CONST_STATUS_NOT_VALID,
                            'contact_type' => Application_Model_Entity_Entity_Contact_Type::TYPE_EMAIL,
                            'source_id' => $contractor->getSourceId(),
                            'skip_check' => true,
                        ])->save();
                    }
                }
            }
        }

        return true;
    }

    public function getExportFile($idOrFilters = null)
    {
        $exportCollection = $this->getNewTempEntity()->getExportCollection($idOrFilters);
        $this->setForExport(true);
        $fields = $this->getFieldsByType();
        $data = [];
        if ($this->getFileType() != FileStorageType::CONST_CONTRACTOR_FILE_TYPE) {
            $data[0] = $this->getExcelHeader($fields);
            foreach ($exportCollection as $entity) {
                $data[] = $this->exportEntity($entity, $fields);
            }
            $excelNames = $this->extractExcelName($fields);
        } else {
            //            $exportCollection = array($exportCollection->getFirstItem()); //TODO: Comment this stub after debug
            $contractorFields = $fields;
            $contactFields = static::getContactFields();
            $vendorFields = static::getVendorFields();
            $data[0] = $this->getExcelHeader([$contractorFields, $contactFields, $vendorFields,], true);

            $contractorFiller = array_fill(0, is_countable($contractorFields) ? count($contractorFields) : 0, null);
            $contractorFiller[0] = '#';

            foreach ($exportCollection as $contractor) {
                $contractorRow = $this->exportEntity($contractor, $contractorFields);
                $contacts = Application_Model_Entity_File::getContactGridCollection($contractor->getAllContacts());
                $vendors = $contractor->getVendors();

                $rowCount = is_countable(max($contacts, $vendors)) ? count(max($contacts, $vendors)) : 0;
                if (!$rowCount) {
                    $data[] = $contractorRow;
                } else {
                    for ($rowIndex = 0; $rowIndex < $rowCount; $rowIndex++) {
                        $contact = $this->exportEntity(array_shift($contacts), $contactFields, true);
                        $vendor = $this->exportEntity(array_shift($vendors), $vendorFields, true);

                        $data[] = array_merge($contractorRow, $contact, $vendor);
                        if ($rowIndex == 0) {
                            $contractorRow = $contractorFiller;
                        }
                    }
                }
            }

            $excelNames = $this->extractExcelName([$contractorFields, $contactFields, $vendorFields,], true);
        }

        $filename = $this->getFileName();

        $objPHPExcel = new Spreadsheet();
        $objPHPExcel->getProperties()
            ->setCreator('P-Fleet')
            ->setLastModifiedBy(
                User::getCurrentUser()->getName()
            )
            ->setTitle($filename)
            ->setSubject($filename)
            ->setDescription('Document, generated using P-Fleet application')
            ->setKeywords('p-fleet')
            ->setCategory('Data export');
        $activeSheet = $objPHPExcel->getActiveSheet();

        Cell::setValueBinder(new Application_Plugin_AdvancedValueBinder());

        $activeSheet->fromArray($data);

        $styles = $activeSheet->getStyle('A1:' . end($excelNames) . '1');
        $styles->getFont()->setBold(true);

        $activeSheet->getDefaultRowDimension()->setRowHeight(15);
        //        $activeSheet->getDefaultColumnDimension()->setAutoSize(true);
        foreach ($excelNames as $excelName) {
            $activeSheet->getColumnDimension($excelName)->setAutoSize(true);
        }

        $name = $this->getFullFileName();
        if (pathinfo((string) $name, PATHINFO_EXTENSION) == self::XLS_TYPE) {
            $objWriter = new Xls($objPHPExcel);
        } else {
            $objWriter = new Xlsx($objPHPExcel);
        }
        $objWriter->save($name);

        return $name;
    }

    /**
     * @param $data = array(
     *                      0 => array(
     *                          0 => array('value' => 'testValue'),
     *                          1 => array('value' => 'testValue2', 'style' => array(See Spreadsheet applyFromArray() specification)),
     *                          ...
     *                      ),
     *                      1 => array(
     *                          ...
     *                      ),
     *                      ...
     *                 )
     * @return string
     * @throws Exception
     */
    public function getFileFromArray($data, $multipage = false)
    {
        $filename = $this->getFileName();

        $objPHPExcel = new Spreadsheet();
        $objPHPExcel->getProperties()
            ->setCreator('P-Fleet')
            ->setLastModifiedBy(
                User::getCurrentUser()->getName()
            )
            ->setTitle($filename)
            ->setSubject($filename)
            ->setDescription('Document, generated using P-Fleet application')
            ->setKeywords('p-fleet')
            ->setCategory('Data export');

        Cell::setValueBinder(new Application_Plugin_AdvancedValueBinder());
        $activeSheet = $objPHPExcel->getActiveSheet();

        if ($multipage) {
            $sheetIndex = 0;
            foreach ($data as $title => $sheetData) {
                $objPHPExcel->setActiveSheetIndex($sheetIndex);
                $activeSheet = $objPHPExcel->getActiveSheet();
                $this->saveSheet($activeSheet, $sheetData);
                $sheetIndex++;
            }
        } else {
            $this->saveSheet($activeSheet, $data);
        }

        $name = $this->getFullFileName();
        if (pathinfo((string) $name, PATHINFO_EXTENSION) == self::XLS_TYPE) {
            $objWriter = new Xls($objPHPExcel);
        } else {
            $objWriter = new Xlsx($objPHPExcel);
        }
        $objWriter->save($name);

        return $name;
    }

    public function saveSheet($activeSheet, $data)
    {
        foreach ($data as $row => $rowData) {
            foreach ($rowData as $col => $cellData) {
                $activeSheet->setCellValueByColumnAndRow($col, $row + 1, $cellData['value']);
                if (isset($cellData['style']) && is_array($cellData['style'])) {
                    $activeSheet->getStyleByColumnAndRow($col + 1, $row + 1)->applyFromArray($cellData['style']);
                }
            }
        }
        //        $activeSheet->mergeCells('A1:' . $activeSheet->getHighestColumn() . '1');
        if (!$this->disableTitle) {
            $activeSheet->mergeCells('A1:B1');
        }

        $activeSheet->getDefaultRowDimension()->setRowHeight(15);
        for ($index = 0; $index < Coordinate::columnIndexFromString($activeSheet->getHighestColumn()); $index++) {
            $activeSheet->getColumnDimension(Coordinate::stringFromColumnIndex($index))->setAutoSize(true);
        }
    }

    public function save()
    {
        $entity = new Application_Model_Entity_File();
        $entity->setData($this->getData());
        $entity->setSourceLink($this->getFileName());
        $entity->save();

        return $entity;
    }

    public function _beforeSave()
    {
        $this->getContent();
        parent::_beforeSave();

        return $this;
    }

    public function convertDateInData($data, $fields, $toFormat = 'YYYY-MM-dd')
    {
        foreach ($fields as $field) {
            if (!Zend_Date::isDate($data[$field], 'MM/dd/YYYY')) {
                $data[$field] = $this->convertDate($data[$field], $toFormat);
            } else {
                $data[$field] = (new Zend_Date($data[$field], 'MM/dd/YYYY'))->toString($toFormat);
            }
        }

        return $data;
    }

    public function convertDate($excelDate, $toFormat = 'YYYY-MM-dd')
    {
        return NumberFormat::toFormattedString($excelDate, $toFormat);
    }

    public function saveTempEntity($row, $columns, $dateFields = null, $type = null, $contractorTempId = null)
    {
        if (!$type) {
            $type = $this->getFileType();
        }
        $tempEntity = false;
        $emptyRow = true;
        foreach ($columns as $key => $options) {
            $value = $options['excelName'];
            $cell = $this->objWorksheet->getCell($value . $row);
            $value = $cell->getValue();
            if ($value instanceof RichText) {
                $value = $value->getPlainText();
            }
            $data[$key] = $value;
            if ($dateFields && ($dateFieldIndex = array_search($key, $dateFields)) > -1) {
                $isExcelDate = Date::isDateTime($cell);
                $isStringDate = Zend_Date::isDate($value, 'MM/dd/YYYY');
                if (!$isExcelDate && !$isStringDate) {
                    unset($dateFields[$dateFieldIndex]);
                }
            }
            if ($cell->getDataType() != 'null') { //!is_null($data[$key]) ||
                $emptyRow = false;
            }
        }
        if (!$emptyRow) {
            $data['source_id'] = $this->getId();
            if ($dateFields && is_array($dateFields)) {
                $data = $this->convertDateInData($data, $dateFields);
            }
            if ($contractorTempId) {
                $data['contractor_temp_id'] = $contractorTempId;
            }
            if ($tempEntity = $this->getNewTempEntity($type)) {
                $tempEntity->setData($data);
                $tempEntity->save();
            }
        }

        return $tempEntity;
    }

    public function exportEntity($entity, $fields, $addFiller = false)
    {
        $row = [];
        if ($entity) {
            $dateFields = $this->extractDateFields($fields);
            $entity->changeDateFormat($dateFields, true);
            foreach ($fields as $fieldName => $options) {
                if ($fieldName == 'address' && isset($options['address'])) {
                    $row = array_merge($this->exportEntity($entity, $options, $addFiller), $row);
                } else {
                    if (isset($options['holderName'])) {
                        $fieldName = $options['holderName'];
                    }
                    $method = 'get' . Application_Model_Base_Object::uc_words($fieldName, '');
                    if (in_array($fieldName, ['card_number', 'tax_id', 'social_security_id'])) {
                        $data = $entity->$method();
                        if ($data) {
                            $data = $this->crypt->decrypt($data, User::getCurrentUser()->getCarrierKey());
                        }
                        $row[] = $data;
                    } elseif('error' === $fieldName) {
                        $data = json_decode($entity->$method() ?? '', true);
                        $row[] = $data ? implode('
', array_values($data)) : null;
                    } else {
                        $value = $entity->$method();
                        if ($callback = ($options['callback'] ?? false)) {
                            $value = $callback::getInstance()->render($entity->getData(), $entity->$method(), $this);
                        }
                        $row[] = $value;
                    }
                }
            }
        }
        if ($addFiller && empty($row)) {
            $row = array_fill(0, is_countable($fields) ? count($fields) : 0, null);
            if (isset($fields['address'])) {
                $row = [...$row, ...array_fill(0, (is_countable($fields['address']) ? count($fields['address']) : 0) - 1, null)];
            }
        }

        return $row;
    }

    public function getNewTempEntity($type = null)
    {
        if (!$type) {
            $type = $this->getFileType();
        }

        $tempEntity = false;
        $tempEntity = match ($type) {
            FileStorageType::CONST_PAYMENTS_FILE_TYPE => new Application_Model_Entity_Payments_Temp(),
            FileStorageType::CONST_DEDUCTIONS_FILE_TYPE => new Application_Model_Entity_Deductions_Temp(),
            FileStorageType::CONST_CONTRACTOR_FILE_TYPE => new Application_Model_Entity_Entity_ContractorTemp(),
            FileStorageType::CONST_CONTRACTOR_TEMP_FILE_TYPE => new Application_Model_Entity_Entity_ContractorTemp(),
            FileStorageType::CONST_CONTRACTOR_CONTACT_PART_TYPE => new Application_Model_Entity_Entity_Contact_Temp(),
            FileStorageType::CONST_CONTRACTOR_VENDOR_PART_TYPE => new Application_Model_Entity_Entity_ContractorVendorTemp(),
            FileStorageType::CONST_POWERUNIT_FILE_TYPE => new Application_Model_Entity_Powerunit_Temp(),
            FileStorageType::CONST_POWERUNIT_TEMP_FILE_TYPE => new Application_Model_Entity_Powerunit_Temp(),
            FileStorageType::CONST_VENDOR_FILE_TYPE => new Application_Model_Entity_Vendor_Temp(),
            FileStorageType::CONST_CONTRACTOR_RA_FILE_TYPE => new Application_Model_Entity_Accounts_Reserve_ContractorTemp(),
            default => $tempEntity,
        };

        return $tempEntity;
    }

    public function getFieldsByType($type = null)
    {
        if (!$type) {
            $type = $this->getFileType();
        }

        $fields = [];
        $forImporting = !($this->getForExport());

        return match ($type) {
            FileStorageType::CONST_PAYMENTS_FILE_TYPE => static::getPaymentFields($forImporting),
            FileStorageType::CONST_DEDUCTIONS_FILE_TYPE => static::getDeductionFields($forImporting),
            FileStorageType::CONST_CONTRACTOR_FILE_TYPE => static::getContractorFields(),
            FileStorageType::CONST_CONTRACTOR_CONTACT_PART_TYPE => static::getContactFields(),
            FileStorageType::CONST_CONTRACTOR_VENDOR_PART_TYPE => static::getVendorFields(),
            FileStorageType::CONST_VENDOR_FILE_TYPE => static::getUploadVendorFields(),
            FileStorageType::CONST_CONTRACTOR_RA_FILE_TYPE => static::getContractorRAFields(),
            FileStorageType::CONST_CONTRACTOR_TEMP_FILE_TYPE => static::getContractorFields($forImporting),
            FileStorageType::CONST_POWERUNIT_TEMP_FILE_TYPE => static::getPowerunitFields($forImporting),
            default => $fields,
        };
    }

    public function getType()
    {
        return static::XLS_TYPE;
    }

    public function extractFieldsPart($fieldsCollection, $partName, $multipartCollection = false)
    {
        if (!$multipartCollection) {
            $fieldsCollection = [$fieldsCollection];
        }
        $extractedParts = [];
        foreach ($fieldsCollection as $fields) {
            $part = [];
            foreach ($fields as $field => $options) {
                if ($field == 'address' && isset($options['address'])) {
                    $part = array_merge($part, $this->extractFieldsPart($options, $partName));
                } else {
                    $part[] = $options[$partName];
                }
            }
            $extractedParts = array_merge($extractedParts, $part);
        }

        return $extractedParts;
    }

    public function getExcelHeader($headerParts, $complexHeader = false)
    {
        return $this->extractFieldsPart($headerParts, 'excelTitle', $complexHeader);
    }

    public function extractExcelName($excelParts, $complexParts = false)
    {
        return $this->extractFieldsPart($excelParts, 'excelName', $complexParts);
    }

    public function extractDateFields($fields)
    {
        $dateFields = [];
        foreach ($fields as $field => $options) {
            if (isset($options['isDateField']) && $options['isDateField']) {
                $dateFields[] = $field;
            }
        }

        return $dateFields;
    }

    public function extractExcelTitles($fields)
    {
        $excelTitles = [];
        foreach ($fields as $field => $options) {
            $excelTitles[] = $options['excelTitle'];
        }

        return $excelTitles;
    }

    public static function getPaymentFields($forImport = true)
    {
        $fieldNames = User::getCurrentUser()->getSelectedCarrier()->getCustomFieldNames();
        if ($forImport) {
            return [
                'contractor_code' => [
                    'excelName' => 'A',
                    'excelTitle' => 'Contractor Code',
                ],
                'compensation_code' => [
                    'excelName' => 'B',
                    'excelTitle' => 'Compensation Code',
                ],
                'payment_code' => [
                    'excelName' => 'C',
                    'excelTitle' => $fieldNames->getPaymentCode(),
                ],
                'powerunit_code' => [
                    'excelName' => 'D',
                    'excelTitle' => 'Power Unit',
                ],
                'carrier_payment_code' => [
                    'excelName' => 'E',
                    'excelTitle' => $fieldNames->getCarrierPaymentCode(),
                ],
                'description' => [
                    'excelName' => 'F',
                    'excelTitle' => $fieldNames->getDescription(),
                ],
                'category' => [
                    'excelName' => 'G',
                    'excelTitle' => $fieldNames->getCategory(),
                ],
                'department' => [
                    'excelName' => 'H',
                    'excelTitle' => $fieldNames->getDepartment(),
                ],
                'gl_code' => [
                    'excelName' => 'I',
                    'excelTitle' => $fieldNames->getGlCode(),
                ],
                'shipment_complete_date' => [
                    'excelName' => 'J',
                    'excelTitle' => 'Shipment Complete Date',
                    'isDateField' => true,
                ],
                'reference' => [
                    'excelName' => 'K',
                    'excelTitle' => 'Reference',
                ],
                'taxable' => [
                    'excelName' => 'L',
                    'excelTitle' => 'Taxable',
                ],
                'driver' => [
                    'excelName' => 'M',
                    'excelTitle' => 'Driver',
                ],
                'loaded_miles' => [
                    'excelName' => 'N',
                    'excelTitle' => 'Loaded Miles',
                ],
                'empty_miles' => [
                    'excelName' => 'O',
                    'excelTitle' => 'Empty Miles',
                ],
                'origin_city' => [
                    'excelName' => 'P',
                    'excelTitle' => 'Origin City/State',
                ],
                'destination_city' => [
                    'excelName' => 'Q',
                    'excelTitle' => 'Destination City/State',
                ],
                'invoice' => [
                    'excelName' => 'R',
                    'excelTitle' => $fieldNames->getInvoice(),
                ],
                'invoice_date' => [
                    'excelName' => 'S',
                    'excelTitle' => $fieldNames->getInvoiceDate(),
                    'isDateField' => true,
                ],
                'disbursement_code' => [
                    'excelName' => 'T',
                    'excelTitle' => $fieldNames->getDisbursementCode(),
                ],
                'disbursement_date' => [
                    'excelName' => 'U',
                    'excelTitle' => 'Disbursement Date',
                    'isDateField' => true,
                ],
                'quantity' => [
                    'excelName' => 'V',
                    'excelTitle' => 'Quantity',
                ],
                'rate' => [
                    'excelName' => 'W',
                    'excelTitle' => 'Rate',
                ],
            ];
        } else {
            return [
                'payment_code' => [
                    'excelName' => 'A',
                    'excelTitle' => $fieldNames->getPaymentCode(),
                ],
                'contractor_code' => [
                    'excelName' => 'B',
                    'excelTitle' => 'Contractor Code',
                ],
                'company_name' => [
                    'excelName' => 'C',
                    'excelTitle' => 'Company',
                ],
                'powerunit_code' => [
                    'excelName' => 'D',
                    'excelTitle' => 'Power Unit Code',
                ],
                'carrier_payment_code' => [
                    'excelName' => 'E',
                    'excelTitle' => $fieldNames->getCarrierPaymentCode(),
                ],
                'description' => [
                    'excelName' => 'F',
                    'excelTitle' => $fieldNames->getDescription(),
                ],
                'category' => [
                    'excelName' => 'G',
                    'excelTitle' => $fieldNames->getCategory(),
                ],
                'department' => [
                    'excelName' => 'H',
                    'excelTitle' => $fieldNames->getDepartment(),
                ],
                'shipment_complete_date' => [
                    'excelName' => 'J',
                    'excelTitle' => 'Shipment Complete Date',
                    'isDateField' => true,
                ],
                'taxable' => [
                    'excelName' => 'L',
                    'excelTitle' => 'Taxable',
                ],
                'driver' => [
                    'excelName' => 'M',
                    'excelTitle' => 'Driver',
                ],
                'reference' => [
                    'excelName' => 'K',
                    'excelTitle' => 'Reference',
                ],
                'loaded_miles' => [
                    'excelName' => 'N',
                    'excelTitle' => 'Loaded Miles',
                ],
                'empty_miles' => [
                    'excelName' => 'O',
                    'excelTitle' => 'Empty Miles',
                ],
                'origin_city' => [
                    'excelName' => 'P',
                    'excelTitle' => 'Origin City/State',
                ],
                'destination_city' => [
                    'excelName' => 'Q',
                    'excelTitle' => 'Destination City/State',
                ],
                'invoice' => [
                    'excelName' => 'R',
                    'excelTitle' => $fieldNames->getInvoice(),
                ],
                'invoice_date' => [
                    'excelName' => 'S',
                    'excelTitle' => $fieldNames->getInvoiceDate(),
                    'isDateField' => true,
                ],
                'settlement_cycle_string' => [
                    'excelName' => 'T',
                    'excelTitle' => 'Settlement Cycle',
                ],
                //            'invoice_due_date' => array(
                //                'excelName'   => 'J',
                //                'excelTitle'  => 'Invoice Due Date',
                //                'isDateField' => true,
                //            ),
                //            'terms' => array(
                //                'excelName'   => 'K',
                //                'excelTitle'  => 'Terms',
                //            ),
                'disbursement_date' => [
                    'excelName' => 'U',
                    'excelTitle' => 'Disbursement Date',
                    'isDateField' => true,
                ],
                'disbursement_code' => [
                    'excelName' => 'V',
                    'excelTitle' => $fieldNames->getDisbursementCode(),
                ],
                'quantity' => [
                    'excelName' => 'W',
                    'excelTitle' => 'Quantity',
                ],
                'rate' => [
                    'excelName' => 'X',
                    'excelTitle' => 'Rate',
                ],
                'amount' => [
                    'excelName' => 'Y',
                    'excelTitle' => 'Amount',
                ],
            ];
        }
    }

    public static function getDeductionFields($forImport = true)
    {
        if ($forImport) {
            return [
                'deduction_code' => [
                    'excelName' => 'A',
                    'excelTitle' => 'Deduction Code',
                ],
                'provider_code' => [
                    'excelName' => 'B',
                    'excelTitle' => 'Vendor Code',
                    'holderName' => 'provider_code',
                ],
                'contractor_code' => [
                    'excelName' => 'C',
                    'excelTitle' => 'Contractor Code',
                ],
                'powerunit_code' => [
                    'excelName' => 'D',
                    'excelTitle' => 'Power Unit Code',
                ],
                'description' => [
                    'excelName' => 'E',
                    'excelTitle' => 'Description',
                ],
                'department' => [
                    'excelName' => 'F',
                    'excelTitle' => 'Department',
                ],
                'reference' => [
                    'excelName' => 'G',
                    'excelTitle' => 'Reference',
                ],
                'transaction_fee' => [
                    'excelName' => 'H',
                    'excelTitle' => 'Transaction Fee',
                ],
                'invoice_date' => [
                    'excelName' => 'I',
                    'excelTitle' => 'Transaction Date',
                    'isDateField' => true,
                ],
                'disbursement_date' => [
                    'excelName' => 'J',
                    'excelTitle' => 'Disbursement Date',
                    'isDateField' => true,
                ],
                'amount' => [
                    'excelName' => 'K',
                    'excelTitle' => 'Original Amount',
                ],
                'adjusted_balance' => [
                    'excelName' => 'L',
                    'excelTitle' => 'Current Amount',
                ],
                'deduction_amount' => [
                    'excelName' => 'M',
                    'excelTitle' => 'Deduction Amount',
                ],
                'balance' => [
                    'excelName' => 'N',
                    'excelTitle' => 'Remaining Balance',
                ],
                'recurring' => [
                    'excelName' => 'O',
                    'excelTitle' => 'Recurring',
                ],
            ];
        } else {
            return [
                'deduction_code' => [
                    'excelName' => 'A',
                    'excelTitle' => 'Deduction Code',
                ],
                'contractor_code' => [
                    'excelName' => 'B',
                    'excelTitle' => 'Contractor Code',
                ],
                'company_name' => [
                    'excelName' => 'C',
                    'excelTitle' => 'Contractor',
                ],
                'powerunit_code' => [
                    'excelName' => 'D',
                    'excelTitle' => 'Power Unit Code',
                ],
                'description' => [
                    'excelName' => 'E',
                    'excelTitle' => 'Description',
                ],
                'recurring' => [
                    'excelName' => 'F',
                    'excelTitle' => 'Recurring',
                ],
                'reference' => [
                    'excelName' => 'G',
                    'excelTitle' => 'Reference',
                ],
                'billing_title' => [
                    'excelName' => 'H',
                    'excelTitle' => 'Frequency',
                ],
                'created_datetime' => [
                    'excelName' => 'I',
                    'excelTitle' => 'Transaction Date',
                ],
                'transaction_fee' => [
                    'excelName' => 'J',
                    'excelTitle' => 'Transaction Fee',
                ],
                'settlement_cycle_string' => [
                    'excelName' => 'K',
                    'excelTitle' => 'Settlement Cycle',
                ],
                'amount' => [
                    'excelName' => 'L',
                    'excelTitle' => 'Original Amount',
                ],
                'balance' => [
                    'excelName' => 'M',
                    'excelTitle' => 'Remaining Balance',
                ],
                'adjusted_balance' => [
                    'excelName' => 'N',
                    'excelTitle' => 'Current Amount',
                ],
                'deduction_amount' => [
                    'excelName' => 'O',
                    'excelTitle' => 'Deduction Amount',
                ],
            ];
        }
    }

    public static function getPowerunitFields($forImport = true)
    {
        if ($forImport) {
            return [
                'code' => [
                    'excelName' => 'A',
                    'excelTitle' => 'Code',
                ],
                'contractor_code' => [
                    'excelName' => 'B',
                    'excelTitle' => 'Contractor Code',
                ],
                'start_date' => [
                    'excelName' => 'C',
                    'excelTitle' => 'In Service Date',
                    'isDateField' => true,
                ],
                'termination_date' => [
                    'excelName' => 'D',
                    'excelTitle' => 'Inactive Date',
                    'isDateField' => true,
                ],
                'status' => [
                    'excelName' => 'E',
                    'excelTitle' => 'Status',
                ],
                'domicile' => [
                    'excelName' => 'F',
                    'excelTitle' => 'Domicile',
                ],
                'plate_owner' => [
                    'excelName' => 'G',
                    'excelTitle' => 'Plate Owner',
                ],
                'form2290' => [
                    'excelName' => 'H',
                    'excelTitle' => '2290',
                ],
                'ifta_filing_owner' => [
                    'excelName' => 'I',
                    'excelTitle' => 'IFTA Filing Owner',
                ],
                'vin' => [
                    'excelName' => 'J',
                    'excelTitle' => 'Vin',
                ],
                'division_code' => [
                    'excelName' => 'K',
                    'excelTitle' => 'Division',
                ],
                'tractor_year' => [
                    'excelName' => 'L',
                    'excelTitle' => 'Tractor Year',
                ],
                'license' => [
                    'excelName' => 'M',
                    'excelTitle' => 'License',
                ],
                'license_state' => [
                    'excelName' => 'N',
                    'excelTitle' => 'License State',
                ],
            ];
        } else {
            return [
                'code' => [
                    'excelName' => 'A',
                    'excelTitle' => 'Code',
                ],
                'contractor_code' => [
                    'excelName' => 'B',
                    'excelTitle' => 'Contractor Code',
                ],
                'start_date' => [
                    'excelName' => 'C',
                    'excelTitle' => 'In Service Date',
                    'isDateField' => true,
                ],
                'termination_date' => [
                    'excelName' => 'D',
                    'excelTitle' => 'Inactive Date',
                    'isDateField' => true,
                ],
                'status' => [
                    'excelName' => 'E',
                    'excelTitle' => 'Status',
                    'callback' => Application_Model_Grid_Callback_PowerunitStatus::class,
                ],
                'domicile' => [
                    'excelName' => 'F',
                    'excelTitle' => 'Domicile',
                ],
                'plate_owner' => [
                    'excelName' => 'G',
                    'excelTitle' => 'Plate Owner',
                    'callback' => Application_Model_Grid_Callback_PowerunitOwnerType::class,
                ],
                'form2290' => [
                    'excelName' => 'H',
                    'excelTitle' => '2290',
                    'callback' => Application_Model_Grid_Callback_NoYes::class,
                ],
                'ifta_filing_owner' => [
                    'excelName' => 'I',
                    'excelTitle' => 'IFTA Filing Owner',
                    'callback' => Application_Model_Grid_Callback_PowerunitOwnerType::class,
                ],
                'vin' => [
                    'excelName' => 'J',
                    'excelTitle' => 'Vin',
                ],
                'division_code' => [
                    'excelName' => 'K',
                    'excelTitle' => 'Division',
                ],
                'tractor_year' => [
                    'excelName' => 'L',
                    'excelTitle' => 'Tractor Year',
                ],
                'license' => [
                    'excelName' => 'M',
                    'excelTitle' => 'License',
                ],
                'license_state' => [
                    'excelName' => 'N',
                    'excelTitle' => 'License State',
                ],
                'status_id' => [
                    'excelName' => 'O',
                    'excelTitle' => 'Upload Status',
                    'holderName' => 'temp_status_title',
                ],
                'error' => [
                    'excelName' => 'P',
                    'excelTitle' => 'Errors',
                ],
                'warning' => [
                    'excelName' => 'Q',
                    'excelTitle' => 'Warnings',
                ],
            ];
        }
    }

    public static function getContractorFields($forImport = true)
    {
        if ($forImport) {
            return [
                'code' => [
                    'excelName' => 'A',
                    'excelTitle' => 'Code',
                ],
                'company_name' => [
                    'excelName' => 'B',
                    'excelTitle' => 'Contractor',
                ],
                'first_name' => [
                    'excelName' => 'C',
                    'excelTitle' => 'First Name',
                ],
                'middle_initial' => [
                    'excelName' => 'D',
                    'excelTitle' => 'Middle Initial',
                ],
                'last_name' => [
                    'excelName' => 'E',
                    'excelTitle' => 'Last Name',
                ],
                'contact_person_type' => [
                    'excelName' => 'F',
                    'excelTitle' => 'Contact person type',
                ],
                'tax_id' => [
                    'excelName' => 'G',
                    'excelTitle' => 'Fed Tax ID',
                ],
                'social_security_id' => [
                    'excelName' => 'H',
                    'excelTitle' => 'Social Security #',
                ],
                'dob' => [
                    'excelName' => 'I',
                    'excelTitle' => 'DoB',
                    'isDateField' => true,
                ],
                'driver_license' => [
                    'excelName' => 'J',
                    'excelTitle' => 'Drivers License',
                ],
                'state_of_operation' => [
                    'excelName' => 'K',
                    'excelTitle' => 'State Of Issuance',
                ],
                'expires' => [
                    'excelName' => 'L',
                    'excelTitle' => 'Expires',
                    'isDateField' => true,
                ],
                'classification' => [
                    'excelName' => 'M',
                    'excelTitle' => 'Classification',
                ],
                'settlement_group_id' => [
                    'excelName' => 'N',
                    'excelTitle' => 'Settlement Group',
                    'holderName' => 'settlement_group',
                ],
                'division' => [
                    'excelName' => 'O',
                    'excelTitle' => 'Division',
                ],
                'department' => [
                    'excelName' => 'P',
                    'excelTitle' => 'Department',
                ],
                'status' => [
                    'excelName' => 'Q',
                    'excelTitle' => 'Status',
                    'holderName' => 'status_title',
                ],
                'start_date' => [
                    'excelName' => 'R',
                    'excelTitle' => 'Start Date',
                    'isDateField' => true,
                ],
                'termination_date' => [
                    'excelName' => 'S',
                    'excelTitle' => 'Termination Date',
                    'isDateField' => true,
                ],
                'rehire_date' => [
                    'excelName' => 'T',
                    'excelTitle' => 'Restart Date',
                    'isDateField' => true,
                ],
                'correspondence_method' => [
                    'excelName' => 'U',
                    'excelTitle' => 'Correspondence',
                ],
                'bookkeeping_type_id' => [
                    'excelName' => 'V',
                    'excelTitle' => 'Bookkeeping Service',
                ],
            ];
        } else {
            return [
                'code' => [
                    'excelName' => 'A',
                    'excelTitle' => 'Code',
                ],
                'company_name' => [
                    'excelName' => 'B',
                    'excelTitle' => 'Contractor',
                ],
                'first_name' => [
                    'excelName' => 'C',
                    'excelTitle' => 'First Name',
                ],
                'middle_initial' => [
                    'excelName' => 'D',
                    'excelTitle' => 'Middle Initial',
                ],
                'last_name' => [
                    'excelName' => 'E',
                    'excelTitle' => 'Last Name',
                ],
                'contact_person_type' => [
                    'excelName' => 'F',
                    'excelTitle' => 'Contact person type',
                    'callback' => Application_Model_Grid_Callback_ContractorContactPersonType::class,
                ],
                'tax_id' => [
                    'excelName' => 'G',
                    'excelTitle' => 'Fed Tax ID',
                ],
                'social_security_id' => [
                    'excelName' => 'H',
                    'excelTitle' => 'Social Security #',
                ],
                'dob' => [
                    'excelName' => 'I',
                    'excelTitle' => 'DoB',
                    'isDateField' => true,
                ],
                'driver_license' => [
                    'excelName' => 'J',
                    'excelTitle' => 'Drivers License',
                ],
                'state_of_operation' => [
                    'excelName' => 'K',
                    'excelTitle' => 'State Of Issuance',
                ],
                'expires' => [
                    'excelName' => 'L',
                    'excelTitle' => 'Expires',
                    'isDateField' => true,
                ],
                'classification' => [
                    'excelName' => 'M',
                    'excelTitle' => 'Classification',
                ],
                'settlement_group_id' => [
                    'excelName' => 'N',
                    'excelTitle' => 'Settlement Group',
                    'holderName' => 'settlement_group',
                ],
                'division' => [
                    'excelName' => 'O',
                    'excelTitle' => 'Division',
                ],
                'department' => [
                    'excelName' => 'P',
                    'excelTitle' => 'Department',
                ],
                'status' => [
                    'excelName' => 'Q',
                    'excelTitle' => 'Status',
                    'holderName' => 'status_title',
                ],
                'start_date' => [
                    'excelName' => 'R',
                    'excelTitle' => 'Start Date',
                    'isDateField' => true,
                ],
                'termination_date' => [
                    'excelName' => 'S',
                    'excelTitle' => 'Termination Date',
                    'isDateField' => true,
                ],
                'rehire_date' => [
                    'excelName' => 'T',
                    'excelTitle' => 'Restart Date',
                    'isDateField' => true,
                ],
                'correspondence_method' => [
                    'excelName' => 'U',
                    'excelTitle' => 'Correspondence',
                    'callback' => Application_Model_Grid_Callback_ContractorCorrespondenceMethod::class,
                ],
                'bookkeeping_type_id' => [
                    'excelName' => 'V',
                    'excelTitle' => 'Bookkeeping Service',
                    'callback' => Application_Model_Grid_Callback_ContractorBookkeepingType::class,
                ],
                'status_id' => [
                    'excelName' => 'W',
                    'excelTitle' => 'Upload Status',
                    'holderName' => 'temp_status_title',
                ],
                'error' => [
                    'excelName' => 'X',
                    'excelTitle' => 'Errors',
                ],
                'warning' => [
                    'excelName' => 'Y',
                    'excelTitle' => 'Warnings',
                ],
            ];
        }
    }

    public static function getContactFields()
    {
        return [
            'address' => [
                'address' => [
                    'excelName' => 'W',
                    'excelTitle' => 'Address 1',
                ],
                'address2' => [
                    'excelName' => 'X',
                    'excelTitle' => 'Address 2',
                ],
                'city' => [
                    'excelName' => 'Y',
                    'excelTitle' => 'City',
                ],
                'state' => [
                    'excelName' => 'Z',
                    'excelTitle' => 'State',
                ],
                'country_code' => [
                    'excelName' => 'AA',
                    'excelTitle' => 'Country Code',
                ],
                'zip' => [
                    'excelName' => 'AB',
                    'excelTitle' => 'Zip',
                ],
            ],
            'phone' => [
                'excelName' => 'AC',
                'excelTitle' => 'Phone',
            ],
            'fax' => [
                'excelName' => 'AD',
                'excelTitle' => 'Fax',
            ],
            'email' => [
                'excelName' => 'AE',
                'excelTitle' => 'Email',
            ],
        ];
    }

    public static function getVendorFields()
    {
        return [
            'vendor_code' => [
                'excelName' => 'AF',
                'excelTitle' => 'Vendor Code',
            ],
            'status' => [
                'excelName' => 'AG',
                'excelTitle' => 'Status',
                'holderName' => 'status_title',
            ],
        ];
    }

    public static function getUploadVendorFields()
    {
        return [
            'code' => [
                'excelName' => 'A',
                'excelTitle' => 'Code',
            ],
            'name' => [
                'excelName' => 'B',
                'excelTitle' => 'Name',
            ],
            'division_code' => [
                'excelName' => 'C',
                'excelTitle' => 'Division Code',
            ],
        ];
    }

    public static function getContractorRAFields()
    {
        return [
            'contractor_code' => [
                'excelName' => 'A',
                'excelTitle' => 'Contractor Code',
            ],
            'vendor_code' => [
                'excelName' => 'B',
                'excelTitle' => 'Vendor ID',
            ],
            'vendor_reserve_code' => [
                'excelName' => 'C',
                'excelTitle' => 'Vendor Reserve Account code',
            ],
            'description' => [
                'excelName' => 'D',
                'excelTitle' => 'Description',
            ],
            'min_balance' => [
                'excelName' => 'E',
                'excelTitle' => 'Minimum Balance',
            ],
            'contribution_amount' => [
                'excelName' => 'F',
                'excelTitle' => 'Contribution Amount',
            ],
            'initial_balance' => [
                'excelName' => 'G',
                'excelTitle' => 'Initial Balance',
            ],
            'current_balance' => [
                'excelName' => 'H',
                'excelTitle' => 'Current Balance',
            ],
        ];
    }
}

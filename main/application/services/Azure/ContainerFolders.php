<?php

use Application_Model_Entity_System_FileStorageType as FileStorageType;

class Application_Service_Azure_ContainerFolders
{
    public const CONTAINER_NAME = 'datastore';

    public const FOLDER_TYPE_ATTACHMENT = 'Attachments';
    public const FOLDER_TYPE_IMPORT = 'Imports';

    public const FOLDER_ENTITY_BASE = 'BaseEntity';
    public const FOLDER_ENTITY_PAYMENTS = 'Compensations';
    public const FOLDER_ENTITY_DEDUCTIONS = 'Deductions';
    public const FOLDER_ENTITY_CONTRACTOR = 'Contractor';
    public const FOLDER_ENTITY_CONTACT = 'Contact';
    public const FOLDER_ENTITY_VENDOR = 'Vendor';
    public const FOLDER_ENTITY_CONTRACTOR_RA = 'ContractorRA';
    public const FOLDER_ENTITY_POWER_UNIT = 'PowerUnit';

    public static function getFullPathByEntity(int $entityType, string $folderType, string $fileName): string
    {
        $folderName = match ($entityType) {
            FileStorageType::CONST_PAYMENTS_FILE_TYPE => self::FOLDER_ENTITY_PAYMENTS,
            FileStorageType::CONST_DEDUCTIONS_FILE_TYPE => self::FOLDER_ENTITY_DEDUCTIONS,
            FileStorageType::CONST_CONTRACTOR_FILE_TYPE => self::FOLDER_ENTITY_CONTRACTOR,
            FileStorageType::CONST_CONTRACTOR_CONTACT_PART_TYPE => self::FOLDER_ENTITY_CONTACT,
            FileStorageType::CONST_CONTRACTOR_VENDOR_PART_TYPE => self::FOLDER_ENTITY_VENDOR,
            FileStorageType::CONST_VENDOR_FILE_TYPE => self::FOLDER_ENTITY_VENDOR,
            FileStorageType::CONST_POWERUNIT_FILE_TYPE => self::FOLDER_ENTITY_POWER_UNIT,
            FileStorageType::CONST_CONTRACTOR_RA_FILE_TYPE => self::FOLDER_ENTITY_CONTRACTOR_RA,
            default => self::FOLDER_ENTITY_BASE
        };

        $path = $folderType . '/' . $folderName . '/' . $fileName;

        return $path;
    }
}

<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Deductions_Deduction as Deduction;
use Application_Model_Entity_Entity_Carrier as Division;
use Application_Model_Entity_Payments_Payment as Payment;
use Application_Model_Entity_System_FileTempStatus as FileTempStatus;
use Application_Service_Azure_ContainerFolders as ContainerFolders;
use Application_Service_Azure_StorageBlob as StorageBlob;
use Application_Service_FileStorage as FileStorage;

class Application_Model_Entity_File extends Application_Model_Base_Entity
{
    use Application_Plugin_Messager;

    final public const UNSELECTED_SOURCE_ID = 0;
    final public const LOCATION_LOCAL = 'local';
    final public const LOCATION_AZURE = 'azure';

    public function _beforeSave()
    {
        if ($this->getUploadedBy() == null) {
            $this->setUploadedBy(
                User::getCurrentUser()->getEntity()->getEntityId()
            );
        }
        if (!$this->getLocationType()) {
            $location = $this->getStorageLocationType();
            $this->setLocationType($location);
            if (self::LOCATION_AZURE === $location) {
                $this->storageFileInAzure((int) $this->getFileType());
            }
        }
        parent::_beforeLoad();

        return $this;
    }

    public function getContent()
    {
        $entity = Application_Model_File::getInstance(
            $this->getSourceLink(),
            $this->getTitle()
        );
        $entity->setData(array_merge($this->getData(), $entity->getData()));

        $dir = Zend_Registry::getInstance()->options['cache']['dir'];
        Application_Model_Cache::init(true, $dir);
        $content = $entity->getContent();
        Application_Model_Cache::clean();

        return $content;
    }

    public function getEntityByType()
    {
        return match ((int) $this->getFileType()) {
            Application_Model_Entity_System_FileStorageType::CONST_PAYMENTS_FILE_TYPE => new Application_Model_Entity_Payments_Temp(),
            Application_Model_Entity_System_FileStorageType::CONST_DEDUCTIONS_FILE_TYPE => new Application_Model_Entity_Deductions_Temp(),
            Application_Model_Entity_System_FileStorageType::CONST_CONTRACTOR_FILE_TYPE => new Application_Model_Entity_Entity_ContractorTemp(),
            Application_Model_Entity_System_FileStorageType::CONST_POWERUNIT_FILE_TYPE => new Application_Model_Entity_Powerunit_Temp(),
            Application_Model_Entity_System_FileStorageType::CONST_VENDOR_FILE_TYPE => new Application_Model_Entity_Vendor_Temp(),
            // Application_Model_Entity_System_FileStorageType::CONST_CONTRACTOR_RA_FILE_TYPE => new Application_Model_Entity_Accounts_Reserve_ContractorTemp(),
            default => false,
        };
    }

    public function isValid()
    {
        $result = false;
        if ($id = $this->getId()) {
            $sql = 'CALL getCountOfInvalidImports(?)';
            $stmt = $this->getResource()->getAdapter()->prepare($sql);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            $data = $stmt->fetch();
            $result = 0 === (int) array_pop($data);
        }

        return $result;
    }

    public function approve($cycleId = null)
    {
        if ($this->getIsApproved()) {
            return true;
        }
        if ($this->getFileType() == Application_Model_Entity_System_FileStorageType::CONST_CONTRACTOR_FILE_TYPE) {
            $isValid = true;
            $divisionIds = [];
            $contractorCollections = $this->getContractorGridCollection();
            foreach ($contractorCollections as $contractorCollection) {
                $contractorTemp = array_shift($contractorCollection['contractor']);
                unset($contractorCollection['contractor']);
                /** @var Application_Model_Entity_Entity_Contractor $contractor */
                $contractor = $contractorTemp->approve()->getParentEntity();
                foreach ($contractorCollection as $partCollection) {
                    foreach ($partCollection as $entity) {
                        $entity->setContractorEntity($contractor);
                        $entity->approve($contractor->getEntityId());
                    }
                }
                $divisionIds[] = $contractor->getCarrierId();
                $contractor->createNewUser();
                if ($contractor->hasMessages()) {
                    $this->addMessages($contractor->getMessages()['default']);
                }
            }
            (new Division())->createIndividualTemplatesByDivisionIds(array_unique($divisionIds));
        } elseif ($this->getFileType() == Application_Model_Entity_System_FileStorageType::CONST_POWERUNIT_FILE_TYPE) {
            $entity = new Application_Model_Entity_Powerunit_Temp();
            $collection = $entity
                ->getCollection()
                ->addFilter($entity->getResource()->getTableName() . '.source_id', $this->getId())
                ->getItems();
            $isValid = true;
            $divisionIds = [];

            /** @var Application_Model_Entity_Powerunit_Temp $model */
            foreach ($collection as $model) {
                if (FileTempStatus::CONST_STATUS_NOT_VALID === (int) $model->getStatusId()) {
                    $model->save();
                    $isValid = false;
                    continue;
                }
                $parent = $entity->getResource()->getParentEntity();
                $parent->setData($model->getData());
                $parent->setDatetimesToDbFormat();
                $parent->setContractorDataByCode();
                $parent->unsId();
                $parent->unsError();
                $parent->unsStatusId();
                $parent->unsSourceId();
                $parent->getDefaultValues();
                $parent->save();
                $divisionIds[] = $model->getCarrierId();
            }
            (new Division())->createIndividualTemplatesByDivisionIds(array_unique($divisionIds));
        } elseif ($this->getFileType() == Application_Model_Entity_System_FileStorageType::CONST_VENDOR_FILE_TYPE) {
            $entity = new Application_Model_Entity_Vendor_Temp();
            $collection = $entity->getCollection()->addFilter(
                $entity->getResource()->getTableName() . '.source_id',
                $this->getId()
            );
            $isValid = true;

            foreach ($collection as $model) {
                if (FileTempStatus::CONST_STATUS_NOT_VALID === (int) $model->getStatusId()) {
                    $model->save();
                    $isValid = false;
                    continue;
                }
                $parent = $entity->getResource()->getParentEntity();
                $parent->setData($model->getData());
                $parent->unsId();
                $parent->unsError();
                $parent->unsStatusId();
                $parent->unsSourceId();
                $parent->save();
            }
            // } elseif ($this->getFileType() == Application_Model_Entity_System_FileStorageType::CONST_CONTRACTOR_RA_FILE_TYPE) {
            //     $entity = new Application_Model_Entity_Accounts_Reserve_ContractorTemp();
            //     $collection = $entity->getCollection()->addFilter(
            //         $entity->getResource()->getTableName() . '.source_id',
            //         $this->getId()
            //     );
            //     $isValid = true;
            //     foreach ($collection as $model) {
            //         if (FileTempStatus::CONST_STATUS_NOT_VALID === (int) $model->getStatusId()) {
            //             $model->save();
            //             $isValid = false;
            //             continue;
            //         }
            //         $parent = $entity->getResource()->getParentEntity();
            //         $parent->setData($model->getData());
            //         //$parent->setContractorDataByCode();
            //         $parent->unsId();
            //         $parent->unsError();
            //         $parent->unsStatusId();
            //         $parent->unsSourceId();
            //         //$parent->getDefaultValues();
            //         $parent->save();
            //     }
        } else {
            if (!$cycleId) {
                return false;
            }
            $cycle = (new Application_Model_Entity_Settlement_Cycle())->load($cycleId);
            $entity = $this->getEntityByType();
            $collection = $entity->getCollection()->addFilter(
                $entity->getResource()->getTableName() . '.source_id',
                $this->getId()
            );
            $isValid = true;

            $carrier = User::getCurrentUser()->getSelectedCarrier();
            $recurringTemplates = [];
            foreach ($collection as $model) {
                $model->setCycle($cycle)->check();
                if (
                    $model->getStatusId(
                    ) == Application_Model_Entity_System_PaymentTempStatus::CONST_STATUS_NOT_VALID
                ) {
                    $model->save();
                    $isValid = false;
                    continue;
                }
                $parent = $entity->getResource()->getParentEntity();
                $parent->setData($model->getData());
                if ($cycleId) {
                    $parent->addData(['settlement_cycle_id' => $cycleId]);
                }
                if ($carrier->getId()) {
                    $parent->addData(['carrier_id' => $carrier->getEntityId()]);
                }
                $parent->unsId();
                $parent->setFromImport(true);
                $parent->getDefaultValues();
                if ($parent instanceof Payment || $parent instanceof Deduction) {
                    if ($parent->getRecurring()) {
                        $parent->recurring();
                    }
                }
                if ($parent instanceof Deduction) {
                    if ($parent->isOnboardingApproved()) {
                        if (
                            $parent->getRecurring() && !isset(
                                $recurringTemplates[$parent->getSetupId()]
                            ) || !$parent->getRecurring()
                        ) {
                            $parent->save();
                        }
                    } else {
                        continue;
                    }
                } elseif ($parent instanceof Payment) {
                    if (
                        $parent->getRecurring() && !isset(
                            $recurringTemplates[$parent->getSetupId()]
                        ) || !$parent->getRecurring()
                    ) {
                        $parent->save();
                    }
                }
                if ($parent instanceof Payment || $parent instanceof Deduction) {
                    if ($parent->getRecurring()) {
                        $recurringTemplates[$parent->getSetupId()] = true;
                        $parent->applyRecurringAsRegular($cycle);
                    }
                }
            }

            /*if (isset($parent)) {
                if ($parent instanceof Deduction) {
                    $parent->reorderImportedPriority($cycle->getId());
                }
            }*/
        }
        if ($isValid) {
            $this->setIsApproved(Application_Model_Entity_System_SystemValues::CONFIGURED_STATUS)->save();
        }

        return $isValid;
    }

    public function getContractorGridCollection($toDisplay = false)
    {
        $collections = [];
        $contractorsCollection = (new Application_Model_Entity_Entity_ContractorTemp())
            ->getCollection()
            ->addSettlementGroup()
            ->addFilter('source_id', $this->getId());
        foreach ($contractorsCollection->getItems() as $contractor) {
            $contacts = (new Application_Model_Entity_Entity_Contact_Temp())
                ->getCollection()
                ->addFilter('source_id', $this->getId())
                ->addFilter('contractor_temp_id', $contractor->getId())
                ->setOrder('contact_type')
                ->getItems();
            if ($toDisplay) {
                $contractor->changeDateFormat(
                    ['dob', 'expires', 'start_date', 'termination_date', 'rehire_date'],
                    true
                );
            }
            $collections[$contractor->getId()] = [
                'contractor' => [$contractor],
                'contacts' => ($toDisplay) ? static::getContactGridCollection($contacts) : $contacts,
                'vendors' => (new Application_Model_Entity_Entity_ContractorVendorTemp())
                    ->getCollection()
                    ->addFilter('source_id', $this->getId())
                    ->addFilter('contractor_temp_id', $contractor->getId())
                    ->getItems(),
            ];
        }

        return $collections;
    }

    public static function getContactGridCollection($contactCollection)
    {
        $addressCollection = [];
        $faxCollection = [];
        $phoneCollection = [];
        $emailCollection = [];
        $contactsForGrid = [];
        $invalidCollection = [];
        foreach ($contactCollection as $contact) {
            switch ($contact->getContactType()) {
                case Application_Model_Entity_Entity_Contact_Type::TYPE_ADDRESS:
                    $addressCollection[] = $contact;
                    break;
                case Application_Model_Entity_Entity_Contact_Type::TYPE_HOME_PHONE:
                    $phoneCollection[] = $contact;
                    break;
                case Application_Model_Entity_Entity_Contact_Type::TYPE_FAX:
                    $faxCollection[] = $contact;
                    break;
                case Application_Model_Entity_Entity_Contact_Type::TYPE_EMAIL:
                    $emailCollection[] = $contact;
                    break;
                default:
                    $invalidCollection[] = $contact;
            }
        }
        $rowCount = max(
            count($addressCollection),
            count($faxCollection),
            count($phoneCollection),
            count($emailCollection)
        );
        for ($iterator = 1; $iterator <= $rowCount; $iterator++) {
            $dataErrors = [];
            $contactForGrid = new Application_Model_Entity_Entity_Contact_Temp();
            if (is_object($addressContact = array_shift($addressCollection))) {
                $contactForGrid->setAddressFormJSON($addressContact->getValue());
                if ($addressContact->getError()) {
                    $dataErrors = array_merge($dataErrors, json_decode((string) $addressContact->getError(), true));
                }
            }
            if (is_object($faxContact = array_shift($faxCollection))) {
                $contactForGrid->setFax($faxContact->getValue());
                if ($faxContact->getError()) {
                    $dataErrors = array_merge($dataErrors, json_decode((string) $faxContact->getError(), true));
                }
            }
            if (is_object($phoneContact = array_shift($phoneCollection))) {
                $contactForGrid->setPhone($phoneContact->getValue());
                if ($phoneContact->getError()) {
                    $dataErrors = array_merge($dataErrors, json_decode((string) $phoneContact->getError(), true));
                }
            }
            if (is_object($emailContact = array_shift($emailCollection))) {
                $contactForGrid->setEmail($emailContact->getValue());
                if ($emailContact->getError()) {
                    $dataErrors = array_merge($dataErrors, json_decode((string) $emailContact->getError(), true));
                }
            }

            if ($dataErrors) {
                $contactForGrid->setError(json_encode($dataErrors));
                $contactForGrid->setStatusId(
                    Application_Model_Entity_System_PaymentTempStatus::CONST_STATUS_NOT_VALID
                );
            }
            $contactsForGrid[] = $contactForGrid;
        }

        if (count($invalidCollection)) {
            $contactsForGrid[] = (new Application_Model_Entity_Entity_Contact_Temp())->setData([
                'error' => 'Contractor Contact information is incorrect',
                'status_id' => Application_Model_Entity_System_PaymentTempStatus::CONST_STATUS_NOT_VALID,
            ]);
        }

        return $contactsForGrid;
    }

    private function getStorageLocationType(): string
    {
        $options = Zend_Registry::getInstance()->options;

        return $options['files']['storageAzure'] ? self::LOCATION_AZURE : self::LOCATION_LOCAL;
    }

    /**
     * @throws Exception
     */
    private function storageFileInAzure(int $entityType): void
    {
        $file = $_FILES['file'];
        if (is_readable($file['tmp_name'])) {
            $fileStorage = new FileStorage(new StorageBlob());
            $fileFullName = ContainerFolders::getFullPathByEntity(
                $entityType,
                ContainerFolders::FOLDER_TYPE_IMPORT,
                $this->getFileName()
            );
            $fileStorage->uploadFile(ContainerFolders::CONTAINER_NAME, $fileFullName, $file);
        }
    }
}

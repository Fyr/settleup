<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_Settlement_Cycle as SettlementCycle;
use Application_Model_Entity_System_FileStorageType as FileStorageType;
use Application_Model_Entity_System_FileTempStatus as FileTempStatus;
use Application_Model_Entity_System_SettlementCycleStatus as SettlementCycleStatus;
use Application_Model_Grid_Callback_DateFormat as DateFormatCallback;

class File_IndexController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_Entity_File $_entity
     */
    protected $_entity;
    protected $_title = 'File';

    public function init()
    {
        $this->_entity = new Application_Model_Entity_File();
    }

    public function indexAction()
    {
        $this->_forward('list');
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $user = User::getCurrentUser();
        if (!$user->hasPermission(Permissions::UPLOADING)) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $fileType = (int) $this->getRequest()->getParam('file_type');
        $timestamp = time();
        $form = new Application_Form_File_Sample($timestamp);

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();

            if ($form->isValid($postData)) {
                $importModel = new Application_Model_Import();
                $entity = $importModel->import(
                    $form->getValue('file'),
                    $form->getValue('title'),
                    $fileType,
                    $timestamp
                );
                $form->file->receive();
                $id = $entity->getId();
                if (!$entity->getContent()) {
                    $this->_helper->FlashMessenger(
                        [
                            'type' => 'T_ERROR',
                            'title' => 'Warning!',
                            'message' => 'Invalid file content. No data found.',
                        ]
                    );
                } else {
                    $this->_helper->redirector(
                        'getcontent',
                        'file_index',
                        'default',
                        ['id' => $id, 'file_type' => $fileType]
                    );
                }
            } else {
                $form->populate($postData);
            }
        }
        $form->getElement('file_storage_type')->setValue($this->_getParam('file_type'));
        $entityType = '';
        if ($fileType == FileStorageType::CONST_PAYMENTS_FILE_TYPE) {
            $entityType = 'Compensation';
        } elseif ($fileType == FileStorageType::CONST_DEDUCTIONS_FILE_TYPE) {
            if (
                !(is_countable((new SettlementCycle())->getCollection()->addFilterByUserRole()->addFilter(
                    'status_id',
                    SettlementCycleStatus::VERIFIED_STATUS_ID
                )->getField('id')) ? count(
                    (new SettlementCycle())->getCollection()->addFilterByUserRole()->addFilter(
                        'status_id',
                        SettlementCycleStatus::VERIFIED_STATUS_ID
                    )->getField('id')
                ) : 0)
            ) {
                $this->_helper->FlashMessenger(
                    [
                        'type' => 'T_WARNING',
                        'title' => 'Error!',
                        'message' => 'There are not any open and not processed settlements to upload deductions into.',
                    ]
                );
                $form->removeElement('submit');
                $this->view->preventSubmit = true;
            }
            $entityType = 'Deduction';
        } elseif ($fileType == FileStorageType::CONST_CONTRACTOR_FILE_TYPE) {
            $entityType = 'Contractor';
        } elseif ($fileType == FileStorageType::CONST_POWERUNIT_FILE_TYPE) {
            $entityType = 'Powerunit';
        } elseif ($fileType == FileStorageType::CONST_VENDOR_FILE_TYPE) {
            $entityType = 'Vendor';
        } elseif ($fileType == FileStorageType::CONST_CONTRACTOR_RA_FILE_TYPE) {
            $entityType = 'ContractorRA';
        }
        $filename = strtolower($entityType) . '_sample.xlsx';
        $form->getElement('sample_file')->setLabel('Download ' . $entityType . ' Sample file')
            ->setAttrib('onclick', "location.href='/samples/$filename';");
        $this->view->title = 'Upload ' . $entityType;
        $this->view->form = $form;
    }

    public function listAction()
    {
        if (
            !User::getCurrentUser()->hasPermission(
                Permissions::UPLOADING
            )
        ) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $this->_helper->redirector('index', 'settlement_index'); //TODO: This is a stub

        $this->view->title = $this->_title;
        $this->view->entity_ = $this->_entity;
        $fileEntity = $this->_entity;

        $header = [
            'header' => [
                'id' => "#",
                'title' => 'Title',
                'source_link' => 'Source Link',
            ],
            'sort' => ['id' => 'ASC'],
            'filter' => false,
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
                'action' => [
                    'view' => [
                        'url' => $this->_helper->url->url(
                            [
                                'controller' => 'file_index',
                                'action' => 'getcontent',
                                'id' => "{{id}}",
                            ],
                            null,
                            true
                        ),
                        'style' => [
                            'button' => 'btn-primary',
                            'icon_style' => "icon-search",
                        ],
                    ],
                    'delete' => [
                        'url' => $this->_helper->url->url(
                            [
                                'controller' => 'file_index',
                                'action' => 'delete',
                                'id' => "{{id}}",
                            ],
                            null,
                            true
                        ),
                        'confirm-type' => 'Deletion',
                        'style' => [
                            'button' => 'btn-danger confirm',
                            'icon_style' => "icon-remove",

                        ],
                    ],
                ],
            ],
        ];

        $massaction = [
            "delete" => [
                "caption" => "Delete Selected",
                "button_class" => "btn-danger confirm confirm-delete btn-multiaction",

                "icon_class" => "icon-trash",
                "style" => "display:none",
                "action-type" => "delete",
                "url" => $this->_helper->url->url(
                    [
                        'controller' => 'file_index',
                        'action' => 'multiaction',
                    ],
                    null,
                    true
                ),
            ],
        ];

        $button = [
            'add' => [
                "caption" => "Create New",
                "button_class" => "btn-success",
                "icon_class" => "icon-plus",
                "url" => $this->_helper->url->url(
                    ['controller' => 'file_index', 'action' => 'new'],
                    null,
                    true
                ),
            ],
        ];

        $customFilters = [
            'addCarrierFilter',
        ];

        $this->view->gridModel = new Application_Model_Grid(
            $fileEntity::class,
            $header,
            $massaction,
            $customFilters,
            $button
        );
    }

    public function getcontentAction()
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::UPLOADING)) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $id = (int) $this->getRequest()->getParam('id');
        $entity = $this->_entity->load($id)->getEntityByType();
        $this->view->entity = $entity;
        $this->view->id = $id;
        $this->view->isValid = $this->_entity->isValid();
        $fileType = $this->_entity->getFileType();
        $entityType = '';
        if ($fileType == FileStorageType::CONST_PAYMENTS_FILE_TYPE) {
            $entityType = 'Compensation';
        } elseif ($fileType == FileStorageType::CONST_DEDUCTIONS_FILE_TYPE) {
            $entityType = 'Deduction';
        } elseif ($fileType == FileStorageType::CONST_CONTRACTOR_FILE_TYPE) {
            $entityType = 'Contractor';
        } elseif ($fileType == FileStorageType::CONST_POWERUNIT_FILE_TYPE) {
            $entityType = 'Powerunit';
        } elseif ($fileType == FileStorageType::CONST_VENDOR_FILE_TYPE) {
            $entityType = 'Vendor';
        } elseif ($fileType == FileStorageType::CONST_CONTRACTOR_RA_FILE_TYPE) {
            $entityType = 'ContractorRA';
        }
        $this->view->title = $entityType . ' Import';
        $this->view->entityType = lcfirst($entityType);
        if (in_array((int) $this->_entity->getFileType(), [
            FileStorageType::CONST_PAYMENTS_FILE_TYPE,
            FileStorageType::CONST_DEDUCTIONS_FILE_TYPE,
        ])) {
            $this->view->entityType = lcfirst($entityType);
            $this->view->isValid = $this->_entity->isValid();
            if (!$this->view->isValid) {
                $this->_helper->FlashMessenger(
                    [
                        'type' => 'T_WARNING',
                        'title' => 'Warning!',
                        'message' => 'Choose settlement cycle to import ' . $this->view->entityType . ' into:',
                    ]
                );
            }
            $this->view->periods = (new SettlementCycle())->getAllCyclePeriodsForImporting();
            $this->view->cycleId = $this->_getParam('cycle_id');
            $this->view->cycleStatus = [
                SettlementCycleStatus::VERIFIED_STATUS_ID => 'Verified',
                SettlementCycleStatus::PROCESSED_STATUS_ID => 'Processed',
            ];
            if (User::getCurrentUser()->isOnboarding()) {
                unset($this->view->periods[SettlementCycleStatus::PROCESSED_STATUS_ID]);
                unset($this->view->cycleStatus[SettlementCycleStatus::PROCESSED_STATUS_ID]);
                if (isset($this->view->periods[SettlementCycleStatus::VERIFIED_STATUS_ID][0])) {
                    $this->_helper->FlashMessenger(
                        [
                            'type' => 'T_WARNING',
                            'title' => 'Error!',
                            'message' => 'There are not any open and not processed settlements to upload deductions into.',
                        ]
                    );
                }
            }
            if ($this->_getParam('approve_failed', false)) {
                $this->view->showApproveErrorMessage = true;
            }
            $collections = $entity->getCollection()
                ->addFilter($entity->getResource()->getTableName() . '.source_id', $id);
            if ($collections->count() == 0) {
                $this->view->isValid = false;
            }
        } elseif ($entityType == 'Contractor') {
            $this->view->collections = $this->_entity->getContractorGridCollection(true);
            $this->view->columns = [
                'contractor' => [
                    'code' => [
                        'title' => 'Code',
                    ],
                    'company_name' => [
                        'title' => 'Contractor',
                    ],
                    'first_name' => [
                        'title' => 'First Name',
                    ],
                    'middle_initial' => [
                        'title' => 'Middle Initial',
                    ],
                    'last_name' => [
                        'title' => 'Last Name',
                    ],
                    'contact_person_type' => [
                        'title' => 'Contact Person Type',
                        'callback' => Application_Model_Grid_Callback_ContractorContactPersonType::class,
                    ],
                    'tax_id' => [
                        'title' => 'Fed Tax ID',
                        'callback' => Application_Model_Grid_Callback_Decrypt::class,
                    ],
                    'social_security_id' => [
                        'title' => 'SS#',
                        'callback' => Application_Model_Grid_Callback_Decrypt::class,
                    ],
                    'dob' => [
                        'title' => 'DOB',
                    ],
                    'driver_license' => [
                        'title' => 'Drivers License #',
                    ],
                    'state_of_operation' => [
                        'title' => 'State of Issuance',
                    ],
                    'expires' => [
                        'title' => 'Expires',
                    ],
                    'classification' => [
                        'title' => 'Classification',
                    ],
                    'settlement_group_id' => [
                        'title' => 'Settlement Group',
                        'callback' => Application_Model_Grid_Callback_ContractorSettlementGroup::class,
                    ],
                    'division' => [
                        'title' => 'Division',
                    ],
                    'department' => [
                        'title' => 'Department',
                    ],
                    'status' => [
                        'title' => 'Status',
                        'callback' => Application_Model_Grid_Callback_ContractorStatus::class,
                    ],
                    'start_date' => [
                        'title' => 'Start Date',
                    ],
                    'termination_date' => [
                        'title' => 'Termination Date',
                    ],
                    'rehire_date' => [
                        'title' => 'Rehire Date',
                    ],
                    'correspondence_method' => [
                        'title' => 'Correspondence',
                        'callback' => Application_Model_Grid_Callback_ContractorCorrespondenceMethod::class,
                    ],
                    'bookkeeping_type_id' => [
                        'title' => 'Bookkeeping Service',
                        'callback' => Application_Model_Grid_Callback_ContractorBookkeepingType::class,
                    ],
                ],
                'contacts' => [
                    'address' => [
                        'title' => 'Address 1',
                        'colspan' => 3,
                    ],
                    'address2' => [
                        'title' => 'Address 2',
                        'colspan' => 3,
                    ],
                    'city' => [
                        'title' => 'City',
                        'colspan' => 3,
                    ],
                    'state' => [
                        'title' => 'State',
                        'colspan' => 2,
                    ],
                    'country_code' => [
                        'title' => 'Country Code',
                        'colspan' => 2,
                    ],
                    'zip' => [
                        'title' => 'Zip',
                        'colspan' => 2,
                    ],
                    'Phone' => [
                        'title' => 'Phone',
                        'colspan' => 2,
                    ],
                    'Fax' => [
                        'title' => 'Fax',
                        'colspan' => 2,
                    ],
                    'Email' => [
                        'title' => 'Email',
                        'colspan' => 3,
                    ],
                ],
                'vendors' => [
                    'vendor_code' => [
                        'title' => 'Vendor Code',
                        'colspan' => 4,
                    ],
                    'status_title' => [
                        'title' => 'Vendor Status',
                        'colspan' => 18,
                    ],
                ],
            ];
        } elseif ($entityType == 'Powerunit') {
            $collections = $entity->getCollection()->addFilter($entity->getResource()->getTableName() . '.source_id', $id);
            $this->view->collections = $collections;
            foreach ($collections as $model) {
                if (FileTempStatus::CONST_STATUS_NOT_VALID === (int) $model->getStatusId()) {
                    $this->view->isValid = false;
                    break;
                }
            }
            $this->view->columns = [
                'code' => [
                    'title' => 'Code',
                ],
                'contractor_code' => [
                    'title' => 'Contractor Code',
                ],
                'start_date' => [
                    'title' => 'Start Date',
                    'callback' => DateFormatCallback::class,
                ],
                'termination_date' => [
                    'title' => 'Termination Date',
                    'callback' => DateFormatCallback::class,
                ],
                'status' => [
                    'title' => 'Status',
                    'callback' => Application_Model_Grid_Callback_PowerunitStatus::class,
                ],
                'domicile' => [
                    'title' => 'Domicile',
                ],
                'plate_owner' => [
                    'title' => 'Plate Owner',
                    'callback' => Application_Model_Grid_Callback_PowerunitOwnerType::class,
                ],
                'form2290' => [
                    'title' => '2290',
                    'callback' => Application_Model_Grid_Callback_NoYes::class,
                ],
                'ifta_filing_owner' => [
                    'title' => 'IFTA Filing Owner',
                    'callback' => Application_Model_Grid_Callback_PowerunitOwnerType::class,
                ],
                'vin' => [
                    'title' => 'Vin',
                ],
                'division_code' => [
                    'title' => 'Division',
                ],
                'tractor_year' => [
                    'title' => 'Tractor Year',
                ],
                'license' => [
                    'title' => 'License',
                ],
                'license_state' => [
                    'title' => 'License State',
                ],
            ];
        } elseif ($entityType == 'Vendor') {
            $collections = $entity->getCollection()->addFilter($entity->getResource()->getTableName() . '.source_id', $id);
            $this->view->collections = $collections;
            foreach ($collections as $model) {
                if (FileTempStatus::CONST_STATUS_NOT_VALID === (int) $model->getStatusId()) {
                    $this->view->isValid = false;
                    break;
                }
            }
            $this->view->columns = [
                'code' => [
                    'title' => 'Code',
                ],
                'name' => [
                    'title' => 'Name',
                ],
                'division_code' => [
                    'title' => 'Division Code',
                ],
            ];
        } elseif ($entityType == 'ContractorRA') {
            $collections = $entity->getCollection()->addFilter($entity->getResource()->getTableName() . '.source_id', $id);
            $this->view->collections = $collections;
            foreach ($collections as $model) {
                if (FileTempStatus::CONST_STATUS_NOT_VALID === (int) $model->getStatusId()) {
                    $this->view->isValid = false;
                    break;
                }
            }
            $this->view->columns = [
                'contractor_code' => [
                    'title' => 'Contractor Code',
                ],
                'vendor_code' => [
                    'title' => 'Vendor Code',
                ],
                'reserve_account_vendor_id' => [
                    'title' => 'Vendor Reserve Account ID',
                ],
                'vendor_reserve_code' => [
                    'title' => 'Reserve Account code',
                ],
                'description' => [
                    'title' => 'Description',
                ],
                'min_balance' => [
                    'title' => 'Minimum Balance',
                ],
                'contribution_amount' => [
                    'title' => 'Contribution Amount',
                ],
                'initial_balance' => [
                    'title' => 'Initial Balance',
                ],
                'current_balance' => [
                    'title' => 'Current Balance',
                ],
            ];
        }
    }

    public function approveAction()
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::UPLOADING)) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $id = (int) $this->getRequest()->getParam('id');
        $cycleId = $this->_getParam('cycle', 0);
        $this->_entity->load($id);
        if ($this->_entity->isValid()) {
            if ($this->_entity->approve($cycleId)) {
                if ($this->_entity->hasMessages()) {
                    $this->_entity->implodeMessages(
                        $namespace = 'default',
                        $glue = '',
                        $template = '<table><tr><th>ID</th><th>Company</th><th>First Name</th><th>Last Name</th><th>Email</th></tr>%s</table>'
                    );
                    $this->_helper->FlashMessenger(
                        [
                            'type' => 'T_CHECKBOX_POPUP_ERROR',
                            'title' => 'The following user accounts were not created because a user account with an identical email already exists:',
                            'messages' => $this->_entity->getMessages(),
                            'headerMessages' => [],
                        ]
                    );
                }
                $this->_helper->redirector(
                    'index',
                    $this->_entity->getEntityByType()->getControllerName()
                );
            } else {
                $this->_helper->redirector(
                    'getcontent',
                    'file_index',
                    'default',
                    [
                        'id' => $id,
                        'file_type' => $this->_entity->getFileType(),
                        'approve_failed' => 'true',
                        'cycle_id' => $cycleId,
                    ]
                );
            }
        }
    }

    public function deleteAction()
    {
        if (
            !User::getCurrentUser()->hasPermission(
                Permissions::UPLOADING
            )
        ) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $id = (int) $this->getRequest()->getParam('id');
        $this->_entity->load($id);
        $this->_entity->delete();
        $this->_helper->redirector('index');
    }

    public function multiactionAction()
    {
        if (
            !User::getCurrentUser()->hasPermission(
                Permissions::UPLOADING
            )
        ) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $ids = explode(',', (string) $this->_getParam('ids'));
        foreach ($ids as $id) {
            $this->_entity->load((int) $id);
            $this->_entity->delete();
        }
        $this->_helper->redirector('index');
    }

    public function exportAction(): void
    {
        if (!($fileType = (int) $this->getRequest()->getParam('file_type'))) {
            $this->_helper->redirector('index', 'index');
        }
        $idOrFilters = [];
        if ($sourceId = (int) $this->getRequest()->getParam('source_id')) {
            $idOrFilters['isTemp'] = true;
            $idOrFilters[] = [
                'name' => 'addSourceIdFilter',
                'value' => $sourceId,
            ];
        }

        (new Application_Model_Export())->export($fileType, Application_Model_File_Type_Xls::XLS_TYPE, $idOrFilters);
    }
}

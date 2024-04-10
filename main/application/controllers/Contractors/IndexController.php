<?php

use Application_Form_Account_Contact as ContactForm;
use Application_Form_Entity_Contractor as ContractorForm;
use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_System_ContractorStatus as ContractorStatus;
use Application_Model_Entity_System_FileStorageType as FileStorageType;
use Application_Model_Entity_System_SystemValues as SystemValues;
use Application_Model_File as File;
use Application_Model_Grid_Entity_Contractor as ContractorGrid;
use Application_Service_Azure_ContainerFolders as ContainerFolders;
use Application_Service_Azure_StorageBlob as AzureStorageBlob;
use Application_Service_FileStorage as FileStorage;

class Contractors_IndexController extends Zend_Controller_Action
{
    /** @var Contractor */
    protected $_entity;
    /** @var ContractorForm */
    protected $_form;
    protected $_title = 'Contractors';
    protected $_contact;

    public function init()
    {
        $this->_entity = new Contractor();
        $this->_form = new ContractorForm();
        $this->_contact = new ContactForm();
    }

    public function indexAction()
    {
        $this->forward('list');
    }

    public function newAction()
    {
        $this->forward('edit');
    }

    /**
     * @throws Zend_Form_Exception
     * @throws Exception
     */
    public function editAction()
    {
        $user = User::getCurrentUser();
        if (!$user->hasPermission(Permissions::CONTRACTOR_VIEW)) {
            if (!$user->isSpecialist()) {
                return $this->_helper->redirector('index', 'settlement_index');
            }
        }
        $this->view->title = $this->_title;
        $this->view->form = $this->_form;
        $this->view->isOnboarding = $user->isOnboarding();
        $this->view->isSpecialist = $user->isSpecialist();
        $this->view->isEditPage = false;
        if ($this->getRequest()->isPost()) {
            if (!$user->hasPermission(Permissions::CONTRACTOR_MANAGE)) {
                return $this->_helper->redirector('index', 'settlement_index');
            }
            if ($user->isOnboarding() || $user->isSpecialist()) {
                return $this->_helper->redirector('list');
            }
            $post = $this->getRequest()->getPost();
            $this->_form->populate($post)->appendSubforms($post['contacts']);
            $this->_form->appendVendors($post['vendor']);

            if ($this->_form->isValid($post)) {
                $this->_entity->setData($this->_form->getValues());
                $this->_entity->changeDateFormat(['dob', 'start_date', 'termination_date', 'rehire_date', 'expires']);
                $this->_entity->setCarrierId($user->getEntity()->getEntityId());
                $isNewContractor = ($this->_entity->getId()) ? false : true;
                if ($isNewContractor) {
                    $this->_entity->changeStatus(
                        ContractorStatus::STATUS_ACTIVE
                    );
                } else {
                    $this->_entity->save();
                }

                $this->_form->saveSubforms($this->_entity->getEntityId());
                $this->_form->saveVendors($this->_entity->getEntityId());
                $this->_entity->createIndividualTemplates();
                $this->_entity->updateMasterTemplatePriority();
                $this->_entity->updateIndividualTemplatePriority();
                $this->saveAttachments();

                //$this->_entity->createNewUser();
                if ($this->_entity->hasMessages()) {
                    //                    Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->setData(
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
                            'headerMessages' => $this->_entity->getHeaderMessages(),
                        ]
                    );
                }

                return $this->_helper->redirector(
                    'index',
                    $this->_getParam('controller')
                );
            } else {
                $this->_form->setEncryptedFields();
                $this->_form->populate($post);
                $this->view->powerunits = $this->_entity->getPowerunits((int)$this->_form->getElement('id')->getValue());
            }
        } else {
            $id = $this->_getParam('id', 0);

            if ($user->isSpecialist()) {
                $this->_entity->load($user->getEntityId(), 'entity_id');
                $id = $this->_entity->getId();
            }

            if ($id) {
                $this->_entity->load($id);
                if (!$this->_entity->checkPermissions()) {
                    return $this->_helper->redirector('index');
                }

                $contractorStatusEntity = new ContractorStatus();

                $status = $contractorStatusEntity->load($this->_entity->getStatus());
                $this->_entity->setStatusTitle($status->getId());
                $this->_entity->changeDateFormat(
                    ['dob', 'start_date', 'termination_date', 'rehire_date', 'expires'],
                    true
                );
                $this->_form->getElement('status_title')->setValue($status->getTitle());
                $this->_form->populate($this->_entity->getData());

                $contractor = new Contractor();
                $contractor->load($id);
                if (Contractor::PRIORITY_CUSTOM === $contractor->getDeductionPriority()) {
                    $this->view->form->deduction_priority_title->setValue(
                        'Custom'
                    );
                }
                $this->view->isEditPage = true;
                $this->view->contractor = $contractor;
                $this->view->attachments = $this->_entity->getAttachments();
                $this->view->powerunits = $this->_entity->getPowerunits();
            }
            $this->_form->appendSubforms($this->_entity->getContractorContacts());
            $this->_form->appendVendors($this->_entity->getVendors());
        }
        if ($this->view->isOnboarding) {
            $this->_form->readonly();
            $this->_form->removeElement('submit');
        }
        $this->checkUserAccounts();
        if (!$user->isOnboarding()) {
            $entityId = $this->_form->entity_id->getValue() ?: 0;
        }
    }

    public function checkUserAccounts()
    {
        $this->_entity->getContactEmails();
        if ($this->_entity->hasMessages()) {
            //                    Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->setData(
            $this->_entity->implodeMessages(
                $namespace = 'default',
                $glue = '',
                $template = '<table><tr><th>ID</th><th>Company</th><th>First Name</th><th>Last Name</th><th>Email</th></tr>%s</table>'
            );
            $this->_helper->FlashMessenger(
                [
                    'type' => 'T_WARNING',
                    'title' => 'The following user accounts were not created because a user account with an identical email already exists:',
                    'messages' => $this->_entity->getMessages(),
                ]
            );
        }
    }

    public function listAction()
    {
        if (User::getCurrentUser()->isSpecialist()) {
            return $this->_helper->redirector('edit');
        }
        if (!User::getCurrentUser()->hasPermission(Permissions::CONTRACTOR_VIEW)) {
            return $this->_helper->redirector('index', 'settlement_index');
        }
        $this->view->gridModel = new ContractorGrid();
    }

    public function deleteAction()
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::CONTRACTOR_MANAGE)) {
            return $this->_helper->redirector('index', 'settlement_index');
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($data['contact']) {
                $entity = new Application_Model_Entity_Entity_Contact_Info();
                $entity->load((int)$data['contact']);
                $entity->delete();
            }

            return $this->_helper->redirector('index');
        } else {
            $entity = new Contractor();
            $id = (int)$this->getRequest()->getParam('id');
            if ($id) {
                $entity->load($id);
                if (!$entity->hasPayments() && !$entity->hasDeductions() && !$entity->hasTransactions() && $entity->checkPermissions()) {
                    $entity
                        ->setDeleted(SystemValues::DELETED_STATUS)
                        ->markColumnAsDeleted($entity->colCode())
                        ->save();
                    $entity->removeRecurringDeductions()->removeRecurringPayments();
                    $entity->removeRelatedData();
                }
            }
        }
        $this->_helper->redirector('index');
    }
    //
    //	/**
    //	 * delete vendor of the contractor
    //	 * @throws Exception
    //	 */
    //	public function deletevendorAction()
    //	{
    //		if($this->getRequest()->isPost()) {
    //			$data = $this->getRequest()->getPost();
    //			if($data['vendor']) {
    //				$entity = new ContractorVendor();
    //				$entity->load((int)$data['vendor']);
    //				$entity->delete();
    //			}
    //		}
    //		$this->_helper->redirector('index');
    //
    //	}

    public function changestatusAction()
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::CONTRACTOR_MANAGE)) {
            return $this->_helper->redirector('index', 'settlement_index');
        }
        $id = (int)$this->getRequest()->getParam('id');
        $status = constant(
            'Application_Model_Entity_System_ContractorStatus::' . $this->getRequest()->getParam('status')
        );

        $entity = new Contractor();
        $entity->load($id);
        if ($entity->checkPermissions()) {
            if (ContractorStatus::STATUS_NOT_CONFIGURED != $entity->getData('status')) {
                $entity->changeStatus($status);
            }
        }
        $this->_helper->redirector('index');
    }

    public function changeCurrentContractor($id)
    {
        $userEntity = User::getCurrentUser();
        $contractorEntity = new Contractor();
        $contractorId = $contractorEntity->load($id, 'entity_id')->getId();

        return $userEntity->setLastSelectedContractor($contractorId)->save();
    }

    /**
     * @throws Exception
     */
    public function saveAttachments(): void
    {
        foreach (($_FILES ?? []) as $file) {
            if (UPLOAD_ERR_OK === $file['error']) {
                $sourceLink = '/not_readable';
                if (is_readable($file['tmp_name'])) {
                    $environment = Zend_Registry::getInstance()->options['environment'];
                    if (Application_Model_Entity_File::LOCATION_LOCAL === $environment) {
                        $sourceLink = File::getStorage() . '/' . time() . '_' . basename((string)$file['name']);
                        move_uploaded_file($file['tmp_name'], $sourceLink);
                    } else {
                        $fileStorage = new FileStorage(new AzureStorageBlob());
                        $fileFullName = ContainerFolders::getFullPathByEntity(
                            FileStorageType::CONST_CONTRACTOR_FILE_TYPE,
                            ContainerFolders::FOLDER_TYPE_ATTACHMENT,
                            $file['name']
                        );
                        $sourceLink = $fileStorage->uploadFile(
                            ContainerFolders::CONTAINER_NAME,
                            $fileFullName,
                            $file
                        );
                    }
                }

                $model = new Application_Model_Entity_File();
                $model->setSourceLink($sourceLink);
                $model->setTitle($file['name']);
                $model->setFileType(FileStorageType::CONST_CONTRACTOR_FILE_TYPE);
                $model->setLocationType(Application_Model_Entity_File::LOCATION_AZURE);
                $model->setEntityId($this->_entity->getEntityId());
                $model->save();
            }
        }
    }

    public function deleteAttachmentAction(): void
    {
        if ($this->getRequest()->isPost()) {
            $attachmentId = (int)$this->_getParam('attachmentId');
            $attachment = (new Application_Model_Entity_File())
                ->getCollection()
                ->addFilter(
                    'id',
                    $attachmentId
                )
                ->getFirstItem();

            $attachment->setDeleted(true);
            $attachment->save();
        }

        $this->_helper->redirector('index');
    }
}

<?php

class Application_Form_Account_User extends Application_Form_Base
{
    use Application_Form_ContactSubformTrait;
    use Application_Form_EntitySubformTrait;

    public function init()
    {
        $this->setName('user_info');
        parent::init();

        $id = new Application_Form_Element_Hidden('id');

        $roleId = new Zend_Form_Element_Select('role_id');
        $roleId->setLabel('User Type ')->setMultiOptions(
            (new Application_Model_Entity_System_UserRoles())->getResource()->getOptions()
        )->setValue(Application_Model_Entity_System_UserRoles::CARRIER_ROLE_ID);

        $entityId = new Application_Form_Element_Hidden('entity_id');

        $entityIdTitle = new Zend_Form_Element_Text('entity_id_title');
        $entityIdTitle->setLabel('Company')->setRequired(true)->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->setAttrib('href', '#entity_id_modal')->setAttrib('data-toggle', 'modal');

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Login (Email) ')->setRequired(true)->addValidator('EmailAddress')->setAttrib(
            'class',
            'required email'
        )->addFilter('StripTags')->addFilter('StringTrim')->addFilter('StringToLower')->addValidator(
            'Db_NoRecordExists',
            false,
            [
                'table' => 'users',
                'field' => 'email',
                'exclude' => 'deleted != ' . Application_Model_Entity_System_SystemValues::DELETED_STATUS . (($userId = Zend_Controller_Front::getInstance(
                )->getRequest()->getParam('id', 0)) ? ' AND id != ' . $userId : ''),
                'messages' => 'This email is already in use',
            ]
        );

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Name ')->setRequired(true)->addFilter('StripTags')->addFilter('StringTrim');

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password ')->setRequired(true)->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->setRenderPassword(true);
        $oldPassword = new Zend_Form_Element_Password('old_password');
        $oldPassword->setLabel('Old Password ')->addFilter('StripTags')->addFilter('StringTrim')->setOptions(
            ['class' => 'password']
        );
        $newPassword = new Zend_Form_Element_Password('new_password');
        $newPassword->setLabel('New Password ')->addFilter('StripTags')->addFilter('StringTrim')->setOptions(
            ['class' => 'password']
        );
        $confirmPassword = new Zend_Form_Element_Password('confirm_password');
        $confirmPassword->setLabel('Confirm New Password ')->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->setOptions(['class' => 'password']);

        $lastLoginIp = new Zend_Form_Element_Text('last_login_ip');
        $lastLoginIp->setLabel('Last Login IP ')->addFilter('StripTags')->addFilter('StringTrim')->setAttrib(
            'disabled',
            'disabled'
        );

        $this->addElements(
            [
                $id,
                $roleId,
                $entityId,
                $entityIdTitle,
                $email,
                $name,
                $password,
                $newPassword,
                $oldPassword,
                $confirmPassword,
                $lastLoginIp,
            ]
        );

        $this->setDefaultDecorators(
            [
                'entity_id_title',
                'email',
                'name',
                'password',
                'old_password',
                'new_password',
                'confirm_password',
                'last_login_ip',
                'role_id',
            ]
        );
        $this->addSubmit('Save');
    }

    public function setupForEditAction()
    {
        $this->password->setValue('********')->setAttrib('readonly', 'readonly');
        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        if (!$user->isAdmin()) {
            $this->email->setAttrib('readonly', 'readonly');
            $this->role_id->setAttrib('readonly', 'readonly');
            $this->entity_id_title->setAttrib('readonly', 'readonly');
        }

        if ($user->isModerator()) {
            $options = $this->role_id->getMultiOptions();
            if ($this->id->getValue() != $user->getId()) {
                unset($options[Application_Model_Entity_System_UserRoles::MODERATOR_ROLE_ID]);
            }
            unset($options[Application_Model_Entity_System_UserRoles::SUPER_ADMIN_ROLE_ID]);
            $this->role_id->setMultiOptions($options);
        }
    }

    public function setupForNewAction()
    {
        $this->password->setRequired(true);
        $this->email->setRequired(true);
        $this->role_id->setRequired(true);

        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        if ($user->isCarrier()) {
            $options = [];
            if ($user->hasPermission(Application_Model_Entity_Entity_Permissions::VENDOR_USER_CREATE)) {
                $options[Application_Model_Entity_System_UserRoles::VENDOR_ROLE_ID] = 'Vendor';
            }
            if ($user->hasPermission(Application_Model_Entity_Entity_Permissions::CONTRACTOR_USER_CREATE)) {
                $options[Application_Model_Entity_System_UserRoles::CONTRACTOR_ROLE_ID] = 'Contractor';
            }
            $this->role_id->setMultiOptions($options);
            if (!isset($options[$this->role_id->getValue()])) {
                $this->role_id->setValue(array_keys($options)[0]);
            }
        }
        if ($user->isModerator()) {
            $options = $this->role_id->getMultiOptions();
            unset($options[Application_Model_Entity_System_UserRoles::MODERATOR_ROLE_ID]);
            unset($options[Application_Model_Entity_System_UserRoles::SUPER_ADMIN_ROLE_ID]);
            $this->role_id->setMultiOptions($options);
        }
    }

    public function isValid($data)
    {
        $valid = parent::isValid($data);
        if ($data['old_password'] && $data['new_password']) {
            $user = Application_Model_Entity_Accounts_User::staticLoad($data['id']);
            $user->setOldPassword($data['old_password']);
            if (!$user->checkOldPassword()) {
                $this->getElement('old_password')->addError('Incorrect password!');
                $valid = false;
            }
        }

        return $valid;
    }
}

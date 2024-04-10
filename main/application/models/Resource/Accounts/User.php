<?php

class Application_Model_Resource_Accounts_User extends Application_Model_Base_Resource
{
    protected $_name = 'users';

    public function getInfoFields(): array
    {
        return [
            'id' => "ID",
            'name' => 'Name',
            'email' => 'Email',
            'role_title' => 'Role',
            'divisions' => 'Divisions',
        ];
    }
}

<?php

class EntityModelTest extends BaseTestCase
{
    private array $_userData = [
        'id' => '100500',
        'role_id' => null,
        'email' => 'phpunittestUN@pfleet.loc',
        'name' => 'phpunittest',
        'password' => 'hgfdjkl;hgfdjkl;',
        'last_login_ip' => '127.0.0.1',
        'last_selected_carrier' => '0',
        'last_selected_contractor' => null,
        'receive_notifications' => '1',
        'deleted' => '0',
        'entity_id' => null,
    ];

    /**
     * returns array that contains Class Names in $dir directory
     *
     * @param $dir string
     * @return array
     */
    private function findAllEntities($dir)
    {
        $root = scandir($dir);
        $ext = '.php';
        $prefix = APPLICATION_PATH . '/models/Entity/';

        $result = [];

        foreach ($root as $value) {
            if ($value === '.' || $value === '..') {
                continue;
            }

            $file = $dir . '/' . $value;

            if (is_file($file)) {
                if (str_ends_with($file, $ext)) {
                    $result[] = 'Application_Model_Entity_' . str_replace(
                        '/',
                        '_',
                        substr($file, strlen($prefix), -strlen($ext))
                    );
                }
                continue;
            }

            foreach ($this->findAllEntities($file) as $value) {
                $result[] = $value;
            }
        }

        return $result;
    }

    public function testResource()
    {
        $dir = APPLICATION_PATH . '/models/Entity';

        foreach ($this->findAllEntities($dir) as $entityClassName) {
            $testClass = new ReflectionClass($entityClassName);
            if (!$testClass->isAbstract() && $testClass->getParentClass()
                    ->getName() != 'Application_Model_Base_Collection') {
                $entityModel = new $entityClassName();
                $this->assertEquals(is_object($entityModel->getResource()), true);
            }
        }
    }

    //    public function testContactResource()
    //    {
    //        $model = new Application_Model_Entity_Entity_Contact_Info();
    //        $model->load(1);
    //        $result = $model->getResource()->getContactsByEntity($model);
    //        $this->assertEquals($result['0'],$model->load(1)->getData());
    //    }

    public function testSave()
    {
        $model = new Application_Model_Entity_Accounts_User();
        $model->setData($this->_userData);
        $model->save();
        $loadedData = $model->load($this->_userData['id'])
            ->getData();
        $this->_userData['password'] = '';
        $this->assertEquals($this->_userData, $loadedData);
    }

    public function testGetOptions()
    {
        $model = new Application_Model_Entity_Accounts_User();
        $resModel = $model->getResource();

        $_expectedArr = [$this->_userData['id'] => $this->_userData['id']];

        $this->assertEquals($_expectedArr, $resModel->getOptions('id', 'id=' . $this->_userData['id']));
    }

    public function testToString()
    {
        $model = new Application_Model_Entity_System_UserRoles();
        $model->load(1);
        $this->assertEquals((string)$model, 'Super admin');
    }

    public function testLoad()
    {
        $model = new Application_Model_Entity_Accounts_User();
        $this->assertEquals(
            $model->load('999999999')
                ->getData(),
            ['password' => '']
        );
        $this->_userData['password'] = '';
        $this->assertEquals(
            $this->_userData,
            $model->load($this->_userData['id'])
                ->getData()
        );
    }

    public function testDelete()
    {
        $model = new Application_Model_Entity_Accounts_User();
        $model->setId($this->_userData['id']);
        $model->delete();
        $model = new Application_Model_Entity_Accounts_User();
        $this->assertEquals(
            $model->load($this->_userData['id'])
                ->getData(),
            ['password' => '']
        );
    }

    public function testRegistration()
    {
        $model = new Application_Model_Entity_Accounts_User();
        $this->_userData['email'] = "reg" . $this->_userData['email'];
        $model->registration($this->_userData);
        $this->_userData['password'] = '';
        $this->assertEquals(
            $this->_userData,
            $model->load($this->_userData['id'])
                ->getData()
        );
        $this->testDelete();
    }

    public function testGetResource()
    {
        $model = new Application_Model_Entity_Accounts_User();
        $this->assertEquals($model->getResource(), new Application_Model_Resource_Accounts_User());
        $model->getResourceName();
        $this->assertEquals($model->getResourceName(), 'Accounts_User');
    }

    public function testGetTableName()
    {
        $model = new Application_Model_Entity_Accounts_User();
        $this->assertEquals(
            $model->getResource()
                ->getTableName(),
            'users'
        );
    }

    public function testGetPrimaryKey()
    {
        $model = new Application_Model_Entity_Accounts_User();
        $this->assertEquals(
            $model->getPrimaryKey(),
            $model->getResource()
                ->getPrimaryKey()
        );
    }

    public function testContactInfo()
    {
        $model = new Application_Model_Entity_Entity_Contact_Info();
        $infoData = [
            'contact_type' => '1',
            'value' => null,
            'entity_id' => '1',
        ];
        $model->setData($infoData);
        $this->assertEquals($model->save(), null);

        $infoData['value'] = 'FatinStr';
        $model->setData($infoData);
        $model->save();
        $this->assertEquals(is_string($model->getId()), true);
        $model->delete();
    }

    public function testDetach()
    {
        $entity = (new Application_Model_Entity_Entity());
        $entity->detach((new Application_Model_Observes_SaveGlobalPayment($entity, 'event')));
        $entity->getDefaultValues();
    }

    public function testArray()
    {
        $array = (new Application_Model_Base_Array());
        $this->getRequest()
            ->setPost(['value' => '12']);
        $array->getPost();
    }
}

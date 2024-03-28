<?php

use PHPUnit\Framework\TestCase;

class ObjectModelTest extends TestCase
{
    public function generateString($length = 5)
    {
        $chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ';
        $numChars = strlen($chars);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= substr($chars, random_int(1, $numChars) - 1, 1);
        }

        return $string;
    }

    public function testSetGetData()
    {
        $model = new Application_Model_Base_Object();
        $name = $this->generateString(5);
        $value = $this->generateString(5);

        $model->setData($name, $value);

        $this->assertEquals($model->getData($name), $value);

        // $key = array();
        $arr = [$name => $value, $name . '1' => $value . '1'];
        $model->setData($arr);
        $this->assertEquals($model->getData($name), $value);
    }

    public function testSetGetIdFieldName()
    {
        $model = new Application_Model_Base_Object();
        $name = $this->generateString(5);

        $model->setIdFieldName($name);

        $this->assertEquals($model->getIdFieldName(), $name);
    }

    public function testSetGetId()
    {
        $model = new Application_Model_Base_Object();
        $value = random_int(1, 100);

        $model->setId($value);

        $this->assertEquals($model->getId(), $value);
    }

    public function testAddData()
    {
        $model = new Application_Model_Base_Object();

        $arr = [];
        $length = random_int(0, 10);

        for ($itterator = 0; $itterator <= $length; $itterator++) {
            $key = $this->generateString();
            $value = $this->generateString();
            $arr[$key] = $value;
        }

        $model->addData($arr);
        $result = true;
        foreach ($arr as $key => $value) {
            if ($arr[$key] != $model->getData($key)) {
                $result = false;
            }
        }

        $this->assertTrue($result);
    }

    public function testUnsetData()
    {
        $key = $this->generateString();
        $value = $this->generateString();
        $model = new Application_Model_Base_Object();

        //$key = null
        $model->setData($key, $value);
        $model->setData(($key . '1'), ($value . '1'));
        $model->unsetData();
        $this->assertEquals($model->getData($key), null);
        //$key = not null
        $model->setData($key, $value);
        $model->setData(($key . '1'), ($value . '1'));
        $model->unsetData(($key . '1'));
        $this->assertEquals($model->getData($key), $value);
    }

    public function testHasSetDataChanges()
    {
        $model = new Application_Model_Base_Object();

        $model->setDataChanges(true);
        $this->assertEquals($model->hasDataChanges(), true);

        $model->setDataChanges(false);
        $this->assertEquals($model->hasDataChanges(), false);
    }

    public function testToString()
    {
        $key1 = $this->generateString();
        $value1 = $this->generateString();
        $key2 = $this->generateString();
        $value2 = $this->generateString();
        $controllString = $value1 . ', ' . $value2;
        $model = new Application_Model_Base_Object();

        $model->setData($key1, $value1);
        $model->setData($key2, $value2);

        //$format = '';
        $this->assertEquals($model->toString(), $controllString);

        //$format = not '';
        $format = '{{' . $key1 . '}}, {{' . $key2 . '}}';
        $this->assertEquals(
            $model->toString($format),
            $controllString
        );
    }

    public function testIsEmpty()
    {
        $key = $this->generateString();
        $value = $this->generateString();
        $model = new Application_Model_Base_Object();

        //empty
        $this->assertEquals($model->isEmpty(), true);

        //not empty
        $model->setData($key, $value);
        $this->assertEquals($model->isEmpty(), false);
    }

    public function testCallGet()
    {
        $model = new Application_Model_Base_Object();
        $key = 'somekey';
        $value = $this->generateString();

        $model->setData($key, $value);
        $this->assertEquals($model->getSomekey(), $value);
    }

    public function testCallSet()
    {
        $model = new Application_Model_Base_Object();
        $key = 'somekey';
        $value = $this->generateString();

        $model->setSomekey($value);
        $this->assertEquals($model->getData($key), $value);
    }

    public function testCallUns()
    {
        $model = new Application_Model_Base_Object();
        $key = 'somekey';
        $value = $this->generateString();

        $model->setData($key, $value);
        $model->getSomekey();

        $model->unsSomekey();
        $this->assertEquals($model->getData($key), null);
    }

    public function testCallHas()
    {
        $model = new Application_Model_Base_Object();
        $key = 'somekey';
        $value = $this->generateString();

        $model->setData($key, $value);

        $this->assertTrue($model->hasSomekey());
    }

    public function testCallSome()
    {
        $model = new Application_Model_Base_Object();

        try {
            $model->someMethod();
        } catch (Exception) {
            $this->assertTrue(true);

            return;
        }
        $this->fail('An expected exception has not been raised.');
        // $this->setExpectedException('Invalid method Application_Model_Base_Object::someMethod(Array ( ) )');
    }

    public function testSetOrigData()
    {
        $key = $this->generateString();
        $value = $this->generateString();
        $data = [$key => $value];
        $model = new Application_Model_Base_Object();
        $model->setData($key, $value);
        $model->setOrigData();
        $reflection = new ReflectionClass($model);
        $property = $reflection->getProperty('_originalData');
        $property->setAccessible(true);
        $actualValue = $property->getValue($model);
        $this->assertEquals([$key => $value], $actualValue);
        //$model->getOriginData();
        $key = $this->generateString();
        $value = $this->generateString();
        $model->setOrigData($key, $value);
        $this->assertTrue(true);
    }

    public function testCamelize()
    {
        $method = new ReflectionMethod(
            'Application_Model_Base_Object',
            '_camelize'
        );

        $method->setAccessible(true);
        $value = 'qwe rty';
        $controllValue = 'QweRty';
        $this->assertEquals(
            $controllValue,
            $method->invoke(new Application_Model_Base_Object(), $value)
        );
        $this->assertTrue(true);
    }

    public function testGetData()
    {
        $model = new Application_Model_Base_Object();

        $key = 'a/b';
        $this->assertEquals($model->getData($key), null);

        $key = '/a';
        $this->assertEquals($model->getData($key), null);

        $key = 'a';
        $index = 'a';
        $arr = ['a' => 'a', 'b' => 'b'];
        $model->setData($arr);
        $this->assertEquals($model->getData($key, $index), null);

        $key = 'a/';
        $index = 'a';
        $this->assertEquals($model->getData($key), null);

        $key = 'a/b';
        $arr = ['a' => 1];
        $model->setData($arr);
        $this->assertEquals($model->getData($key), null);

        $key = 'c';
        $index = 'c';
        $subArr = [];
        $arr = [$key => $subArr];
        $model->setData($arr);
        $this->assertEquals($model->getData($key, $index), null);

        $key = 'd';
        $index = 'd';
        $subArr = ['d' => 'dValue'];
        $arr = [$key => $subArr];
        $model->setData($arr);
        $this->assertEquals($model->getData($key, $index), 'dValue');

        $index = 'd';
        $arr = [$key => $model];
        $model->setData($arr);
        $this->assertEquals($model->getData($key, $index), $model);

        $index = 'c';
        $arr = [$key => 1];
        $model->setData($arr);
        $this->assertEquals($model->getData($key, $index), null);

        $key = 'a/b';
        $arr = ['a' => $model];
        $model->setData($arr);
        $this->assertEquals($model->getData($key, $index), null);
    }

    public function testGetOriginalData()
    {
        $model = new Application_Model_Base_Object();
        $this->assertEquals($model->getOriginalData(''), null);
        $model->getOriginalData("y/''/f");
        $model->getOriginalData("0");
        $model->getOriginalData(null);
    }

    //Commented as this test doesn't test anything
    /*public function testGetOriginalData1()
        {
            $model = new Application_Model_Base_Object();

            $model->setOrigData('b', ['a' => '1', ['b' => '2']]);

            $model->getOriginalData('b/p');
            $model->getOriginalData('b');
            $model->getOriginalData('b', 'b');
            $model->getOriginalData('x');

            $model->setOrigData('bh', 'g');
            $model->getOriginalData('bh', 'ind');

            $model->setData(null, new Application_Model_Base_Object());
            $model->setOrigData(null, new Application_Model_Base_Object());
            $model->getOriginalData('x/');
        }*/
}

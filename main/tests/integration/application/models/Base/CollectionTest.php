<?php

class CollectionTest extends BaseTestCase
{
    private ?array $_data1 = null;
    private ?array $_data2 = null;

    protected function setUp(): void
    {
        $this->addSampleData();
        parent::setUp();
    }

    //    public function testCollection()
    //    {
    //        $entityContactInfo = new Application_Model_Entity_Entity_Contact_Info();
    //        $entityContactType = new Application_Model_Entity_Entity_Contact_Type();
    //        $collections = $entityContactInfo
    //            ->getCollection()
    //            ->addFieldsForSelect($entityContactInfo,'contact_type',$entityContactType,'id',array('title'))
    //            ->addFilter('entity_contact_info.entity_id','1')
    //            ->addFilter('entity_contact_info.entity_id',array(1,2,3),'IN');
    //
    //        $this->assertEquals($collections->count(),1);
    //        $collections = $entityContactInfo
    //            ->getCollection()
    //            ->addFieldsForSelect($entityContactInfo,'contact_type',$entityContactType,'id',array('title'))
    //            ->addFilter('entity_contact_info.entity_id','1');
    //        $this->assertEquals($collections->getField('id'),array(1));
    //
    //        $collections = $entityContactInfo
    //            ->getCollection()
    //            ->addFilter('entity_contact_info.entity_id','100500');
    //        $this->assertEquals($collections->getField('id'),array());
    //
    //        $collections = $entityContactInfo
    //            ->getCollection()
    //            ->addFilter('entity_contact_info.entity_id','1');
    //        $this->assertEquals($collections->getFirstItem()->getId(),1);
    //
    //        $collections = $entityContactInfo
    //            ->getCollection()
    //            ->addFilter('entity_contact_info.entity_id','100500');
    //        $this->assertEquals($collections->getFirstItem(),new Application_Model_Base_Object());
    //
    //        $collections = $entityContactInfo
    //            ->getCollection()
    //            ->addFilter('entity_contact_info.entity_id','100500');
    //        $this->assertEquals($collections->getLastItem(),new Application_Model_Base_Object());
    //
    //        $collections = $entityContactInfo
    //            ->getCollection();
    //
    //        foreach($collections as $contact){
    //            $this->assertTrue($contact instanceof $entityContactInfo);
    //        }
    //    }

    public function testFilters()
    {
        $pk = $this->saveSampleData();
        $this->assertEquals(
            $this->getSampleCollection()
                ->count(),
            2
        );
        $this->assertEquals(
            $this->getSampleCollectionUnionFalse()
                ->count(),
            2
        );

        $this->deleteSampleData($pk);
        $this->assertEquals(
            $this->getSampleCollection()
                ->count(),
            0
        );
    }

    private function addSampleData()
    {
        $this->_data1 = [
            'contact_type' => '1',
            'value' => 'Fatin str. 3-38',
            'entity_id' => '3',
        ];

        $this->_data2 = [
            'contact_type' => '7',
            'value' => '+375292477759',
            'entity_id' => '3',
        ];
    }

    private function getSampleCollectionUnionFalse()
    {
        $entityContactInfo = new Application_Model_Entity_Entity_Contact_Info();

        return $entityContactInfo->getCollection()
            ->addFilter('entity_contact_info.contact_type', $this->_data1['contact_type'])
            ->addFilter('entity_contact_info.value', $this->_data1['value'])
            ->addFilter(
                'entity_contact_info.contact_type',
                $this->_data2['contact_type'],
                '=',
                true,
                Application_Model_Base_Collection::WHERE_TYPE_OR,
                false
            )
            ->addFilter('entity_contact_info.value', $this->_data2['value']);
    }

    private function getSampleCollection()
    {
        $entityContactInfo = new Application_Model_Entity_Entity_Contact_Info();

        return $entityContactInfo->getCollection()
            ->addFilter('entity_contact_info.contact_type', $this->_data1['contact_type'])
            ->addFilter('entity_contact_info.value', $this->_data1['value'])
            ->addFilter(
                'entity_contact_info.contact_type',
                $this->_data2['contact_type'],
                '=',
                true,
                Application_Model_Base_Collection::WHERE_TYPE_OR,
                true
            )
            ->addFilter('entity_contact_info.value', $this->_data2['value']);
    }

    /**
     * @return array of id for saved data
     */

    private function saveSampleData()
    {
        $pk = [];
        $entityContactInfo = new Application_Model_Entity_Entity_Contact_Info();
        $entityContactInfo->setData($this->_data1);
        $entityContactInfo->save();
        $pk[] = $entityContactInfo->getData('id');
        $entityContactInfo = new Application_Model_Entity_Entity_Contact_Info();
        $entityContactInfo->setData($this->_data2);
        $entityContactInfo->save();
        $pk[] = $entityContactInfo->getData('id');
        return $pk;
    }

    private function deleteSampleData($pk = [])
    {
        foreach ($pk as $id) {
            $entityContactInfo = new Application_Model_Entity_Entity_Contact_Info();
            $entityContactInfo->load($id);
            $entityContactInfo->delete();
            $entityContactInfo->save();
        }
    }

    public function testVariableFilters()
    {
        $collection = (new Application_Model_Entity_Settlement_Cycle())->getCollection();
        $collection->addFilter('id', ['1', '2'], 'NOT IN')
            ->getFirstItem();
    }

    /**
     * @expectedException Exception
     */
    public function testVariableFiltersException()
    {
        $collection = (new Application_Model_Entity_Settlement_Cycle())->getCollection();
        $collection->addFilter('id', null, 'NOT IN')
            ->getFirstItem();
    }

    /**
     * @expectedException Exception
     */
    public function testVariableFiltersBetweenException()
    {
        $collection = (new Application_Model_Entity_Settlement_Cycle())->getCollection();
        $collection->addFilter('id', null, 'BETWEEN DATE')
            ->getFirstItem();
    }

    public function testVariableFiltersGTEDATE()
    {
        $collection = (new Application_Model_Entity_Settlement_Cycle())->getCollection();
        $collection->addFilter('id', '01-01-2014', 'GTE DATE')
            ->getFirstItem();
    }

    public function testVariableFiltersLTEDATE()
    {
        $collection = (new Application_Model_Entity_Settlement_Cycle())->getCollection();
        $collection->addFilter('id', '01-01-2014', 'LTE DATE')
            ->getFirstItem();
    }

    public function testAddGroup()
    {
        $collection = (new Application_Model_Entity_Settlement_Cycle())->getCollection();
        $collection->addGroup(['id'])
            ->getFirstItem();
    }
}

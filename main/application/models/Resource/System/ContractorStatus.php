<?php

class Application_Model_Resource_System_ContractorStatus extends Application_Model_Base_Resource
{
    protected $_name = 'contractor_status';

    public function getStatusFilterOptions()
    {
        //        $options = $this->getOptions(
        //            'title',
        //            array(
        //                'id' => array(
        //                    Application_Model_Entity_System_ContractorStatus::STATUS_ACTIVE,
        //                    Application_Model_Entity_System_ContractorStatus::STATUS_TERMINATED
        //                )
        //            )
        //        );
        //        return array_merge(array('all'), $options);
        return [
            '' => 'All',
            Application_Model_Entity_System_ContractorStatus::STATUS_ACTIVE => "Active",
            Application_Model_Entity_System_ContractorStatus::STATUS_TERMINATED => 'Terminated',
        ];
    }
}

<?php

class Application_Model_Grid_EmailLog extends Application_Model_Grid
{
    public function __construct()
    {
        $entity = new Application_Model_Entity_System_EmailLog();
        $header = [
            'header' => [
                'id' => 'ID',
                'email' => 'Email',
                'status' => 'Status',
                'error' => 'Error',
                'created_at' => 'Date',
            ],
            'sort' => ['created_at' => 'DESC'],
            'id' => static::class,
            'filter' => true,
            'checkboxField' => false,
            'callbacks' => [],
            //            'service' => array(
            //                'header' => array('action' => 'Action'),
            //                'bindOn' => 'id'
            //            )
        ];

        return parent::__construct(
            $entity::class,
            $header
        );
    }
}

<?php

class Reporting_IndexControllerTest extends BaseTestCase
{
    /** @var Reporting_IndexController */
    private $controller;

    protected function setUp(): void
    {
        $this->setDefaultController('reporting_index');
        parent::setUp();
    }

    public function testIndexAction()
    {
        Application_Model_Entity_Accounts_User::login(16);
        $this->setStorage();
        $this->baseTestAction(
            [
                'params' => ['action' => 'index'],
            ],
            false
        );
    }

    public function testIndexActionContractorSettlementStatementView()
    {
        (new Application_Model_Entity_Accounts_User())->load(16)
            ->setData('last_selected_carrier', '4')
            ->save();
        (new Application_Model_Entity_Settlement_Cycle())->load(2)
            ->setData('status_id', '5')
            ->save();
        $data = [
            'action' => '1',
            'type' => '3',
            'date_filter_type' => '1',
            'year' => '2012',
            'period' => '2',
            'range_start_date' => '',
            'range_end_date' => '',
            'select_contractor' => '1',
            'contractor_id' => '[]',
            'contractor_id_title' => '',
            'select_reserve_account' => '1',
            'reserve_account_id' => '[]',
            'reserve_account_id_title' => '',
            'select_carrier_vendor' => '1',
            'carrier_vendor_id' => '[]',
            'carrier_vendor_id_title' => '',
            'view' => 'View',
        ];
        $this->baseTestAction(
            [
                'params' => ['action' => 'index'],
                'post' => $data,
            ]
        );
        return $data;
    }

    /**
     * @depends testIndexActionContractorSettlementStatementView
     */
    public function testIndexActionContractorSettlementStatementDownload(array $data)
    {
        $data['action'] = '2';
        unset($data['view']);
        $data['download'] = 'Download';
        $this->baseTestAction(
            [
                'params' => ['action' => 'index'],
                'post' => $data,
                'assert' => ['action' => 'index'],
            ]
        );
        return $data;
    }

    //    /**
    //     * @depends testIndexActionContractorSettlementStatementDownload
    //     */
    //    public function testIndexActionAchDownload(array $data)
    //    {
    //        $data['type'] = '1';
    //        $this->baseTestAction(array(
    //                'params' => array('action' => 'index'),
    //                'post' => $data,
    //                'assert' => array('controller'=>'error', 'action' => 'error')
    //            )
    //        );
    //        return $data;
    //    }

    /**
     * @depends testIndexActionContractorSettlementStatementDownload
     */
    public function testIndexActionCheckFileDownload(array $data)
    {
        $data['type'] = '2';
        $this->baseTestAction(
            [
                'params' => ['action' => 'index'],
                'post' => $data,
                'assert' => ['action' => 'index'],
            ]
        );
        return $data;
    }

    /**
     * @depends testIndexActionContractorSettlementStatementDownload
     */
    //    public function testIndexActionCheckFileNoYearNoPeriodDownload(array $data)
    //    {
    //        $data['type'] = '2';
    //        unset($data['year']);
    //        unset($data['period']);
    //        $this->baseTestAction(array(
    //                'params' => array('action' => 'index'),
    //                'post' => $data,
    //                //   'assert' => array('controller'=>'error', 'action' => 'error')
    //            )
    //        );
    //        return $data;
    //    }
    /**
     * @depends testIndexActionContractorSettlementStatementDownload
     */
    //    public function testIndexActionDeductionRemittanceFileDownload(array $data)
    //    {
    //        $data['type'] = '11';
    //        unset($data['year']);
    //        $this->baseTestAction(array(
    //                'params' => array('action' => 'index'),
    //                'post' => $data,
    //            )
    //        );
    //        return $data;
    //    }

    //    public function testIndexActionViewAction()
    //    {
    //        $data = array(
    //            'action'                => '1',
    //            'type'                  => '5',
    //            'date_filter_type'      => '1',
    //            'year'                  => '2014',
    //            'period'                => '1',
    //            'range_start_date'      => '',
    //            'range_end_date'        => '',
    //            'select_contractor'     => '1',
    //            'contractor_id'         => '[]',
    //            'contractor_id_title'   => '',
    //            'select_reserve_account'=> '1',
    //            'reserve_account_id'    => '[]',
    //            'reserve_account_id_title'  => '',
    //            'select_carrier_vendor'     => '1',
    //            'carrier_vendor_id'         => '[]',
    //            'carrier_vendor_id_title'   => '',
    //            'view' => 'View',
    //            );
    //        $this->baseTestAction(array(
    //                'params' => array('action' => 'index'),
    //                'post' => $data
    //            )
    //        );
    //        return $data;
    //    }
    //
    //    /**
    //     * @depends testIndexActionViewAction
    //     */
    //    public function testIndexActionDownloadActionDeductionRemittanceFile(array $data)
    //    {
    //        $data['action'] = '2';
    //        $data['type'] = Application_Model_Report_Reporting::DEDUCTION_REMITTANCE_FILE;
    //        $data['download'] = 'Download';
    //        $this->baseTestAction(array(
    //                'params' => array('action' => 'index'),
    //                'post' => $data
    //            )
    //        );
    //    }

    //    /**
    //     * @depends testIndexActionViewAction
    //     */
    //    public function testIndexActionDownloadActionAch(array $data)
    //    {
    //        $data['action'] = '2';
    //        $data['type'] = Application_Model_Report_Reporting::ACH_FILE;
    //        $data['download'] = 'Download';
    //        $this->baseTestAction(array(
    //                'params' => array('action' => 'index'),
    //                'post' => $data
    //            )
    //        );
    //    }

    //    /**
    //     * @depends testIndexActionViewAction
    //     */
    //    public function testIndexActionDownloadActionCheckFile(array $data)
    //    {
    //        $data['action'] = '2';
    //        $data['type'] = Application_Model_Report_Reporting::CHECK_FILE;
    //        $data['download'] = 'Download';
    //        $this->baseTestAction(array(
    //                'params' => array('action' => 'index'),
    //                'post' => $data
    //            )
    //        );
    //    }

}
//
//    public function testIndexActionDownloadNewReport()
//    {
//        $data = array(
//            'action'                => '2',
//            'type'                  => '1',
//            'date_filter_type'      => '1',
//            //'year'                  => '2014',
//            'period'                => '5',
//            'range_start_date'      => '',
//            'range_end_date'        => '',
//            'select_contractor'     => '1',
//            'contractor_id'         => '[]',
//            'contractor_id_title'   => '',
//            'select_reserve_account'=> '1',
//            'reserve_account_id'    => '[]',
//            'reserve_account_id_title'  => '',
//            'select_carrier_vendor'     => '1',
//            'carrier_vendor_id'         => '[]',
//            'carrier_vendor_id_title'   => '',
//            'download' => 'Download',
//        );
//        $this->baseTestAction(array(
//                'params' => array('action' => 'index'),
//                'post' => $data
//            )
//        );
//    }
//
//
//
//}

//'controller' => 'reporting_index',
//  'action' => 'index',
//  'module' => 'default',
//  'type' => '10',
//  'date_filter_type' => '2',
//  'year' => '2014',
//  'period' => '9',
//  'range_start_date' => '07/01/2014',
//  'range_end_date' => '07/30/2014',
//  'select_contractor' => '1',
//  'contractor_id' => '[]',
//  'contractor_id_title' => '',
//  'select_reserve_account' => '1',
//  'reserve_account_id' => '[]',
//  'reserve_account_id_title' => '',
//  'select_carrier_vendor' => '1',
//  'carrier_vendor_id' => '[]',
//  'carrier_vendor_id_title' => '',
//  'view' => 'View',

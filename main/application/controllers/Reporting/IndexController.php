<?php

class Reporting_IndexController extends Zend_Controller_Action
{
    /**
     * @var $reportingModel Application_Model_Report_Reconciliation
     */
    public $reportingModel;
    public $form;

    public function init()
    {
        $this->reportingModel = new Application_Model_Report_Reporting();
        $this->form = new Application_Form_Reporting();
    }

    public function indexAction()
    {
        $this->view->title = 'Reports';
        $this->formPreConfig();
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if ($this->form->isValid($post)) {
                $post['period_title'] = (isset($post['period'])) ? $this->form->period->getMultiOption(
                    $post['period']
                ) : '';
                $this->_helper->getHelper('layout')->disableLayout();
                $this->reportingModel->setData($post);
                $this->reportingModel = $this->reportingModel->getModel();
                $this->reportingModel->setActionType($this->form->action->getValue());
                $this->view->gridData = $this->reportingModel->getGridData();
                if (is_array($this->view->gridData)) {
                    $this->view->gridData['form'] = $this->reportingModel->getData();
                }
                $this->view->title = $this->reportingModel->getTitle();
                $this->view->gridTemplate = $this->reportingModel->getView();
                $this->view->reportingModel = $this->reportingModel;

                switch ($this->form->action->getValue()) {
                    case Application_Model_Report_Reporting::VIEW_ACTION:
                        $this->render('view');

                        return;
                        break;
                    case Application_Model_Report_Reporting::DOWNLOAD_ACTION:

                        $this->getHelper('viewRenderer')->setNoRender();
                        if ($this->reportingModel->getType() == Application_Model_Report_Reporting::ACH_FILE) {
                            Application_Model_File::download(
                                $this->reportingModel->getGridData(),
                                false,
                                Application_Model_File_Type_Txt::TYPE
                            );
                        } elseif ($this->reportingModel->getType() == Application_Model_Report_Reporting::CHECK_FILE) {
                            Application_Model_File::download(
                                $this->reportingModel->getGridData(),
                                false,
                                Application_Model_File_Type_Csv::TYPE
                            );
                        } elseif ($this->reportingModel->getType(
                        ) == Application_Model_Report_Reporting::DEDUCTION_REMITTANCE_FILE) {
                            Application_Model_File::download($this->reportingModel->getGridData());
                        } else {
                            if ($this->reportingModel->getFileType() == Application_Model_File_Type_Pdf::TYPE) {
                                $this->view->toPDF = true;
                                $html = $this->view->render($this->reportingModel->getView());
                                Application_Model_File::toPDF(
                                    $html,
                                    $this->reportingModel->getOrientation(),
                                    $this->reportingModel->getFileName(),
                                    $this->reportingModel->getCss(),
                                    $this->reportingModel->getFontKey()
                                );
                            } else {
                                Application_Model_File::download(
                                    $this->reportingModel->getFileName(),
                                    false,
                                    Application_Model_File::getHeaderContentType($this->reportingModel->getFileType())
                                );
                            }
                        }

                        return;
                        break;
                }
            } else {
                $this->form->populate($post);
            }
        } else {
            if ($contractorId = $this->_getParam('id') && $cycleId = $this->_getParam('cycle_id')) {
                if ($this->form->contractor_id->getValue() == '[]') {
                    $this->form->contractor_id = '[' . $contractorId . ']';
                }
                if (!$this->form->period->getValue()) {
                    $this->form->period->setValue($cycleId);
                }
            }
        }

        $this->view->form = $this->form->configure();
        $this->view->popupSettings = $this->getPopupSettings();
    }

    public function validateAction()
    {
        $this->_helper->layout->disableLayout();
        $isValid = false;
        $this->formPreConfig();
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if ($this->form->isValid($post)) {
                $isValid = true;
            }
        }
        $this->_helper->json(['valid' => $isValid ? 1 : 0]);
    }

    public function getPopupSettings()
    {
        $reserveCarrierVendorEntity = new Application_Model_Entity_Accounts_Reserve_Vendor();
        $reserveContractorEntity = new Application_Model_Entity_Accounts_Reserve_Contractor();

        $reserveCarrierVendorHeader = [
            'header' => $reserveCarrierVendorEntity->getResource()->getInfoFields(),
            'sort' => ['reserve_account_id' => 'ASC'],
            'checkboxField' => 'reserve_account_id',
            'titleField' => $reserveCarrierVendorEntity->getTitleColumn(),
            'filter' => true,
            'pagination' => false,
            'ignoreMassactions' => true,
        ];
        $reserveContractorHeader = [
            'header' => $reserveContractorEntity->getResource()->getInfoFields(),
            'sort' => ['reserve_account_id' => 'ASC'],
            'checkboxField' => 'reserve_account_id',
            'titleField' => $reserveContractorEntity->getTitleColumn(),
            'filter' => true,
            'pagination' => false,
            'ignoreMassactions' => true,
        ];

        return [
            'carrier_vendor' => [
                'multiselect' => true,
                'filterable' => true,
                'gridTitle' => 'Select Vendors',
                'destFieldName' => 'carrier_vendor_id',
                'collections' => [
                    'Division' => (new Application_Model_Grid_Reporting_Carrier()),
                    'Vendor' => (new Application_Model_Grid_Reporting_Vendor()),
                ],
            ],
            'reserve_account' => [
                'multiselect' => true,
                'filterable' => true,
                'gridTitle' => 'Select Reserve Account',
                'destFieldName' => 'reserve_account_id',
                'collections' => [
                    'Reserve Account Vendor' => (new Application_Model_Grid_Reporting_ReserveAccountVendor()),
                ],
            ],
            'reserve_account_contractor' => [
                'multiselect' => true,
                'filterable' => true,
                'gridTitle' => 'Select Reserve Account',
                'destFieldName' => 'reserve_account_contractor_id',
                'collections' => [
                    'Reserve Account Contractor' => (new Application_Model_Grid_Reporting_ReserveAccountContractor()),
                ],
            ],
            'contractor' => [
                'multiselect' => true,
                'filterable' => true,
                'gridTitle' => 'Select Contractor',
                'destFieldName' => 'contractor_id',
                'collections' => [
                    'Contractor' => (new Application_Model_Grid_Reporting_Contractor()),
                ],
            ],
        ];
    }

    public function formPreConfig()
    {
        $cycle = new Application_Model_Entity_Settlement_Cycle();
        $this->view->periods = $cycle->getAllCyclePeriodsForReport();
        $lastClosed = null;
        $lastYear = null;
        $sortedData = $this->view->periods;
        ksort($sortedData, SORT_NUMERIC);
        foreach ($sortedData as $year => $periods) {
            ksort($periods, SORT_NUMERIC);
            foreach ($periods as $id => $data) {
                if ($data['status'] == Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID) {
                    $lastClosed = $id;
                    $lastYear = $year;
                }
            }
        }
        $options = $cycle->getPeriodOptionsForReport($this->view->periods);
        $years = array_keys($options);
        $this->form->year->setMultiOptions(array_combine($years, $years));
        $this->form->starting_year->setMultiOptions(array_combine($years, $years));
        $this->form->ending_year->setMultiOptions(array_combine($years, $years));

        //period
        if (!$this->form->year->getValue()) {
            if ($this->getRequest()->isPost()) {
                if (!$year = $this->_getParam('year')) {
                    if ($cycleId = $this->_getParam('period')) {
                        foreach ($options as $year => $periods) {
                            if (isset($periods[$cycleId])) {
                                break;
                            }
                        }
                    } else {
                        $year = array_keys($this->form->year->getMultiOptions())[0];
                    }
                }
                $this->form->year->setValue($year);
            } else {
                $this->form->year->setValue(array_keys($this->form->year->getMultiOptions())[0]);
            }
        }

        //starting
        if (!$this->form->starting_year->getValue()) {
            if ($this->getRequest()->isPost()) {
                if (!$year = $this->_getParam('starting_year')) {
                    if ($cycleId = $this->_getParam('starting_period')) {
                        foreach ($options as $year => $periods) {
                            if (isset($periods[$cycleId])) {
                                break;
                            }
                        }
                    } else {
                        $year = array_keys($this->form->starting_year->getMultiOptions())[0];
                    }
                }
                $this->form->starting_year->setValue($year);
            } else {
                if ($lastYear) {
                    $this->form->starting_year->setValue($lastYear);
                } else {
                    $this->form->starting_year->setValue(array_keys($this->form->starting_year->getMultiOptions())[0]);
                }
            }
        }

        //ending
        if (!$this->form->ending_year->getValue()) {
            if ($this->getRequest()->isPost()) {
                if (!$year = $this->_getParam('ending_year')) {
                    if ($cycleId = $this->_getParam('ending_period')) {
                        foreach ($options as $year => $periods) {
                            if (isset($periods[$cycleId])) {
                                break;
                            }
                        }
                    } else {
                        $year = array_keys($this->form->ending_year->getMultiOptions())[0];
                    }
                }
                $this->form->ending_year->setValue($year);
            } else {
                if ($lastYear) {
                    $this->form->ending_year->setValue($lastYear);
                } else {
                    $this->form->ending_year->setValue(array_keys($this->form->ending_year->getMultiOptions())[0]);
                }
            }
        }
        $this->form->period->setMultiOptions($options[$this->form->year->getValue()]);
        $this->form->starting_period->setMultiOptions($options[$this->form->starting_year->getValue()]);
        $this->form->ending_period->setMultiOptions($options[$this->form->ending_year->getValue()]);
        if (!$this->form->starting_period->getValue()) {
            $this->form->starting_period->setValue($lastClosed);
        }
        if (!$this->form->ending_period->getValue()) {
            $this->form->ending_period->setValue($lastClosed);
        }

        return $this->form;
    }
}

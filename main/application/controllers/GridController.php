<?php

class GridController extends Zend_Controller_Action
{
    protected $_entity;
    /** @var Application_Model_Base_Crypt */
    protected $crypt;

    public function init()
    {
        $this->crypt = new Application_Model_Base_Crypt();
    }

    public function indexAction()
    {
        $this->_helper->redirector('index', 'index');
    }

    public function filterAction()
    {
        $this->_helper->layout->disableLayout();
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest()) {
            $id = $req->getParam('id');
            $id = $this->crypt->decrypt($id);
            $filter = $req->getParam('filter');
            $limit = $req->getParam('limit');
            if (class_exists($id)) {
                $grid = new $id();
                // todo get rid of this hot fix
                if (isset($grid->getFilter()['addFilterByEntityId'])) {
                    $filter['addFilterByEntityId'] = $grid->getFilter()['addFilterByEntityId'];
                }
                $grid->setFilter($filter);
                $grid->setLimit($limit);

                $this->view->gridModel = $grid;
            }
            $this->setCycleGrid();
        } else {
            $this->_helper->redirector('index', 'index');
        }
    }

    public function sortgridcollAction()
    {
        $this->_helper->layout->disableLayout();
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest()) {
            $sortcol = $req->getParam('sortCol');
            $filter = $req->getParam('filter');
            $limit = $req->getParam('limit');
            $id = $req->getParam('id');
            $id = $this->crypt->decrypt($id);
            if (class_exists($id)) {
                $grid = new $id();
                if (isset($grid->getFilter()['addFilterByEntityId'])) {
                    $filter['addFilterByEntityId'] = $grid->getFilter()['addFilterByEntityId'];
                }
                $grid->setFilter($filter);
                $grid->setLimit($limit);
                $grid->getControllerDataStorage();
                if (!$sort = $grid->controllerStorage['sort']) {
                    $sort = $grid->getSortData('sort')['sort'];
                }
                //                $sort['sort'] = $sortcol;
                $this->view->gridModel = $grid;
            }

            foreach ($sort as $key => $value) {
                if ($key == $sortcol) {
                    if ($sort[$key] == 'ASC') {
                        $sort[$key] = 'DESC';
                    } else {
                        $sort[$key] = 'ASC';
                    }
                } else {
                    $sort = [$sortcol => 'ASC'];
                }
            }

            $this->view->sort = [
                'sort' => key($sort),
                'order' => strtolower((string) current($sort)),
            ];

            $this->view->gridModel->setSort($sort);

            $this->setCycleGrid();
        } else {
            $this->_helper->redirector('index', 'index');
        }
    }

    public function limitgridrowAction()
    {
        $this->_helper->layout->disableLayout();
        $req = $this->getRequest();

        if ($req->isXmlHttpRequest()) {
            $limit = $req->getParam('limit');
            $filters = $req->getParam('filter');
            $id = $req->getParam('id');
            $id = $this->crypt->decrypt($id);
            if (class_exists($id)) {
                $grid = new $id();
                if (!($grid instanceof Application_Model_Grid)) {
                    throw new Exception('Invalid grid class name!');
                }
                $grid->setLimit($limit);
                $grid->setFilter($filters);
                $this->view->gridModel = $grid;
            }
            $this->setCycleGrid();
        } else {
            $this->_helper->redirector('index', 'index');
        }
    }

    public function pagerAction()
    {
        $this->_helper->layout->disableLayout();
        $req = $this->getRequest();
        if ($req->isXmlHttpRequest()) {
            $currentPage = $req->getParam('pager');
            $limit = $req->getParam('limit');
            $filter = $req->getParam('filter');
            $id = $req->getParam('id');
            $id = $this->crypt->decrypt($id);
            if (class_exists($id)) {
                $grid = new $id();
                $grid->setLimit($limit);
                $grid->setFilter($filter);
                $grid->setCurrentPage($currentPage);
                $grid->getControllerDataStorage();
                if (!$sort = $grid->controllerStorage['sort']) {
                    $sort = $grid->getSortData('sort')['sort'];
                }
                $grid->setSort($sort);
            }

            $this->view->gridModel = $grid;
            $this->setCycleGrid();
        } else {
            $this->_helper->redirector('index', 'index');
        }
    }

    public function changepriorityAction()
    {
        $req = $this->getRequest();
        if ($req->isXmlHttpRequest()) {
            $id = $req->getParam('id');
            $id = $this->crypt->decrypt($id);
            $filter = $req->getParam('filter');
            $beforeList = $req->getParam('beforeList');
            $resultList = $req->getParam('resultList');
            $currentPage = $req->getParam('currentPage');
            if (!$currentPage) {
                $currentPage = 1;
            }
            if (class_exists($id)) {
                $grid = new $id();
                if (isset($grid->getFilter()['addFilterByEntityId'])) {
                    $filter['addFilterByEntityId'] = $grid->getFilter()['addFilterByEntityId'];
                }
                $grid->setFilter($filter);
                $grid->setFilter($filter);
                $grid->setCurrentPage($currentPage);
                $this->view->gridModel = $grid;
                $grid->setPriority($beforeList, $resultList, $currentPage);
                $this->setCycleGrid();
            }

            $this->setCycleGrid();
            //            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->getHelper("layout")->disableLayout();

            //            $this->_helper->json->sendJson(array('status' => 'ok', 'text' => $newValue));
            //            $data = array(
            //                "text" => $this->grid()->renderBody($this->gridModel),
            //            );
            //            echo json_encode($data);
        } else {
            $this->_helper->redirector('index', 'index');
        }
    }

    public function quickeditAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $id = $this->getRequest()->getParam('id');
            $id = $this->crypt->decrypt($id);
            if (class_exists($id)) {
                $this->_entity = new $id();

                $this->_entity->setRecordId($this->getRequest()->getParam('recordId'));
                $this->_entity->setField($this->getRequest()->getParam('field'));
                $this->_entity->setValue($this->getRequest()->getParam('value'));
                $newValue = $this->_entity->saveQuickEdit();

                if (is_null($newValue)) {
                    $newValue = '―';
                }

                $this->_helper->json->sendJson(['status' => 'ok', 'value' => $newValue]);
            }
        } else {
            $this->_helper->redirector('index', 'index');
        }
    }

    public function setCycleGrid()
    {
        if ($setCycle = $this->_getParam('cycle') == 'true') {
            $filter = $filter = $this->_getParam('filter');
            if (isset($filter['settlement_cycle_id'])) {
                $cycleId = $filter['settlement_cycle_id'];
            } else {
                $cycleId = $this->getRequest()->getCookie('settlement_cycle_id');
            }

            $cycle = (new Application_Model_Entity_Settlement_Cycle())->getCollection()->addCarrierFilter(
            )->getActiveCycle($cycleId);
            $this->view->gridModel->setCycle($cycle);
            $this->view->gridModel->getCycle()->setFilterType(
                $this->view->gridModel->getFilter('settlement_cycle_filter_type')
            );
            if ($cycle->getDisbursementStatus() == Application_Model_Entity_System_PaymentStatus::APPROVED_STATUS) {
                $this->view->gridModel->setButtons([]);
            }
            $header = $this->view->gridModel->getHeader();
            $header['totals'] = [
                'template' => 'transactions/disbursement/totals.phtml',
                'data' => [
                    'cycle' => $cycle,
                ],
            ];
            $this->view->gridModel->setHeader($header);
        }

        return $this;
    }
}

<?php

use Application_Model_Base_CryptAdvanced as Crypt;
use Application_Model_Entity_Accounts_Reserve as Account;
use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Carrier as Carrier;
use Application_Model_Entity_Settlement_Cycle as Cycle;
use Migrate\Migrate;
use Migrate\Strategy\ContractorStrategy;
use Migrate\Strategy\EntityHistory\Contractor;

class CronController extends Zend_Controller_Action
{
    /**
     * usage example -a balance/152 -r 68 -e development
     */
    public function balanceAction()
    {
        $cycleId = (int)$this->getRequest()->getParam('id', false);
        $accountId = (int)$this->getRequest()->getParam('account', false);

        $cycle = Cycle::staticLoad($cycleId);
        $account = Account::staticLoad($accountId);

        $account->updateSubsequentCycles($cycle);
        $this->_helper->getHelper('layout')->disableLayout();
    }

    public function settlementAction()
    {
        $cycleId = (int)$this->getRequest()->getParam('id', false);
        $token = $this->getRequest()->getParam('token', false);
        $secret = $this->getRequest()->getParam('secret', false);
        $userId = $this->getRequest()->getParam('user', false);

        $user = User::login($userId);
        $user->setCredentials(['token' => $token, 'secret' => $secret]);

        $cycle = new Cycle();
        $cycle->load($cycleId);

        $path = Application_Model_File::getStorage() . '/../reports/cycle-' . $cycleId . '/';

        file_exists($path) || mkdir($path, 0777, true);

        $reportModel = new Application_Model_Report_Statement();
        if ($cycle->getStatusId() == Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID) {
            $mailIds = [];
            $carrierIds = [];
            $emailContractors = [];

            $defaultData = [
                'action' => Application_Model_Report_Reporting::DOWNLOAD_ACTION,
                'type' => Application_Model_Report_Reporting::CONTRACTOR_SETTLEMENT_STATEMENT,
                'date_filter_type' => Application_Model_Report_Reporting::SETTLEMENT_CYCLE,
                'select_contractor' => 2,
                'period' => $cycleId,
            ];

            foreach ($cycle->getCycleContractorsCollection() as $contractor) {
                if ($contractor->getCorrespondenceMethod(
                ) == Application_Model_Entity_Entity_Contact_Type::TYPE_ADDRESS) {
                    $mailIds[] = $contractor->getEntityId();
                } elseif ($contractor->getCorrespondenceMethod(
                ) == Application_Model_Entity_Entity_Contact_Type::TYPE_CARRIER_DISTRIBUTES) {
                    $carrierIds[] = $contractor->getEntityId();
                } elseif ($contractor->getCorrespondenceMethod(
                ) == Application_Model_Entity_Entity_Contact_Type::TYPE_EMAIL) {
                    $emailContractors[] = $contractor;
                }
            }

            // mail report

            if (count($mailIds)) {
                $data = $defaultData;
                $data['contractor_id'] = json_encode($mailIds, JSON_THROW_ON_ERROR);
                $reportModel->setData($data);
                $this->view->gridData = $reportModel->getGridData();
                $this->view->toPDF = true;
                $html = $this->view->render($reportModel->getView());

                Application_Model_File::toPDF($html, 'A4', $path . 'Mail_Report' . $cycleId . '.pdf');
            }

            if (count($carrierIds)) {
                $data = $defaultData;
                $data['contractor_id'] = json_encode($carrierIds, JSON_THROW_ON_ERROR);
                $reportModel->setData($data);
                $this->view->gridData = $reportModel->getGridData();
                $this->view->toPDF = true;
                $html = $this->view->render($reportModel->getView());

                Application_Model_File::toPDF($html, 'A4', $path . 'Carrier_Distributes_Report' . $cycleId . '.pdf');
            }

            $subject = 'Settlement Statement Report for period: ' . $cycle->getCyclePeriodString();
            if (count($emailContractors)) {
                foreach ($emailContractors as $contractor) {
                    $data = $defaultData;
                    $data['contractor_id'] = json_encode([$contractor->getEntityId()], JSON_THROW_ON_ERROR);
                    $reportModel->setData($data);
                    $this->view->gridData = $reportModel->getGridData();
                    $this->view->toPDF = true;
                    $html = $this->view->render($reportModel->getView());

                    $fileName = 'Email_Report_Contractor' . $contractor->getEntityId() . '_Cycle' . $cycleId . '.pdf';
                    Application_Model_File::toPDF($html, 'A4', $path . $fileName);
                    $this->sendReport($contractor->getEmailToSend(), $fileName, $path, $subject);
                }
            }
        }
        $this->_helper->getHelper('layout')->disableLayout();
    }

    protected function sendReport(
        $to,
        $reportFileName,
        $pathToReport,
        $subject = 'Report',
        $text = 'Report is in attached file.'
    ) {
        $mailObject = new Zend_Mail();
        $mailObject->setBodyText($text);
        $mailObject->setFrom('P-Fleet');
        $mailObject->setSubject($subject);
        if (is_string($to)) {
            $to = [$to => $to];
        }
        foreach ($to as $email => $name) {
            $mailObject->addTo($email, $name);
        }
        $report = new Zend_Mime_Part(file_get_contents($pathToReport . $reportFileName));
        $report->type = 'application/pdf';
        $report->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
        $report->encoding = Zend_Mime::ENCODING_BASE64;
        $report->filename = $reportFileName;
        $mailObject->addAttachment($report);

        $result = true;
        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                $mailObject->send();
                $result = true;
                break;
            } catch (Zend_Mail_Transport_Exception $e) {
                $errorMessage = $e->getMessage();
                $result = false;
            }
        }

        $log = new Application_Model_Entity_System_EmailLog();
        $log->setSubject($subject);
        $log->setEmail($email ?? '');
        $log->setStatus($result ? 0 : 1);
        if (!$result && isset($errorMessage)) {
            $log->setError($errorMessage);
        }
        $log->setCreatedAt(date('Y-m-d H:i:s'));
        $log->save();

        return $result;
    }

    public function securityAction()
    {
        $id = (int)$this->getRequest()->getParam('id', false);
        $password = $this->getRequest()->getParam('password', false);
        $restService = new Application_Model_Rest();
        $crypt = new Crypt();

        $loginResult = $restService->login($id, md5((string) $password), 3);
        if (!$loginResult || !$restService->isCredentialsExists()) {
            throw new Exception('No Credentials!');
        }
        $c = 0;

        //add users
        $userModel = new User();
        $collection = $userModel->getCollection()->addNonDeletedFilter()->addFilter('id', $id, '<>')->addFilter(
            'role_id',
            0,
            '>'
        );
        /** @var User $user */
        foreach ($collection as $user) {
            $data = [
                'id' => $user->getId(),
                'role_id' => $user->getRoleId(),
                'password' => $user->getPassword(),
                'carrier_id' => $user->getAssociatedCarrierId() ?: 0,
            ];
            if (!$restService->getUser($user->getId())) {
                $restService->createUser($data);
                $c++;
            }
        }
        echo "\n" . 'Inserted ' . $c . ' users' . "\n";

        $carrierModel = new Carrier();
        $carrierCollection = $carrierModel->getCollection();
        $c = 0;
        foreach ($carrierCollection as $carrier) {
            if ($key = $restService->getCarrierKey($carrier->getEntityId())) {
                $key = $key['carrier_key'];
                $carrierKey = $crypt->decrypt($key['token'], $key['secret']);
                $migrate = new Migrate($carrier, $carrierKey);
                $migrate->addStrategy(new ContractorStrategy());
                $migrate->addStrategy(new Contractor());
                $migrate->migrate();
                $c++;
            }
        }
        echo "\n" . 'Inserted ' . $c . ' keys' . "\n";
        exit;
    }
}

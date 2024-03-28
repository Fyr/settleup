<?php

class Application_Views_Helpers_SetButtons extends Zend_View_Helper_Abstract
{
    public function setButtons($buttons, $id = null, $additionParams = [])
    {
        $buttonsHTML = '';
        foreach ($buttons as $button) {
            switch ($button) {
                case 'edit':
                    $buttonsHTML .= '<a class="btn btn-primary" href="' . $this->view->url(
                        array_merge(['action' => 'edit', 'id' => $id], $additionParams)
                    ) . '"><i class="icon-pencil icon-white"></i> Edit</a>';
                    break;
                case 'settlementContractor':
                    $buttonsHTML .= '<a class="btn btn-primary" href="' . $this->view->url(
                        array_merge(
                            ['action' => 'contractor', 'controller' => 'settlement_index', 'id' => $id],
                            $additionParams
                        )
                    ) . '"><i class="icon-pencil icon-white"></i> Edit</a>';
                    break;
                case 'settlementContractorView':
                    $buttonsHTML .= '<a class="btn btn-primary" href="' . $this->view->url(
                        array_merge(
                            ['action' => 'contractor', 'controller' => 'settlement_index', 'id' => $id],
                            $additionParams
                        )
                    ) . '"><i class="icon-search icon-white"></i>&nbsp;View</a>';
                    break;
                case 'settlementHome':
                    $buttonsHTML .= '<a class="btn btn-primary" href="' . $this->view->url(
                        array_merge(
                            ['action' => 'index', 'controller' => 'settlement_index', 'id' => $id],
                            $additionParams
                        ),
                        null,
                        true
                    ) . '"><i class="icon-home icon-white"></i> Home</a>';
                    break;
                case 'downloadcheck':
                    $buttonsHTML .= '<a class="btn btn-mini btn-primary" href="' . $this->view->url(
                        array_merge(['action' => 'downloadcheck', 'id' => $id], $additionParams)
                    ) . '"><i class="icon-pencil icon-white"></i> Download</a>';
                    break;
                case 'downloadach':
                    $buttonsHTML .= '<a class="btn btn-mini btn-primary" href="' . $this->view->url(
                        array_merge(['action' => 'downloadach', 'id' => $id], $additionParams)
                    ) . '"><i class="icon-pencil icon-white"></i> Download</a>';
                    break;
                case 'delete':
                    $buttonsHTML .= '<a class="btn btn-danger confirm"' . ' confirm-type="Deletion" target-url="' . $this->view->url(
                        array_merge(['action' => 'delete', 'id' => $id], $additionParams)
                    ) . '"><i class="icon-trash icon-white"></i> Delete</a>';
                    break;
                case 'delete-disabled':
                    $buttonsHTML .= '<a class="btn btn-danger delete-cycle" disabled="disabled"' . ' confirm-type="Deletion" target-url="' . $this->view->url(
                        array_merge(['action' => 'delete', 'id' => $id], $additionParams)
                    ) . '"><i class="icon-trash icon-white"></i> Delete</a>';
                    break;
                case 'delete_payment':
                    $buttonsHTML .= '<a class="btn btn-danger confirm"' . ' confirm-type="Deletion" target-url="' . $this->view->url(
                        array_merge(
                            ['action' => 'delete', 'controller' => 'payments_payments', 'id' => $id],
                            $additionParams
                        )
                    ) . '"><i class="icon-trash icon-white"></i> Delete</a>';
                    break;
                case 'approve':
                    $buttonsHTML .= '<a class="btn btn-success" data-toggle="modal" data-target="#confirm-settlement-approve" href="' . $this->view->url(
                        array_merge(['action' => 'approve', 'id' => $id], $additionParams)
                    ) . '"><i class="icon-ok icon-white"></i> Approve</a>';
                    break;
                case 'approve-disabled':
                    $buttonsHTML .= '<a class="btn btn-success" confirm-type="Approving" disabled="disabled" href="' . $this->view->url(
                        array_merge(['action' => 'approve', 'id' => $id], $additionParams)
                    ) . '"><i class="icon-ok icon-white"></i> Approve</a>';
                    break;

                case 'reject':
                    $buttonsHTML .= '<a class="btn btn-danger" data-toggle="modal" data-target="#confirm-settlement-reject" href="' . $this->view->url(
                        ['action' => 'reject', 'id' => $id],
                        $additionParams
                    ) . '"><i class="icon-remove icon-white"></i> Reject</a>';
                    break;

                case 'verify':
                    $buttonsHTML .= '<a class="btn btn-success" href="' . $this->view->url(
                        array_merge(['action' => 'verify', 'id' => $id], $additionParams)
                    ) . '"><i class="icon-check icon-white"></i> Verify</a>';
                    break;
                case 'process':
                    $buttonsHTML .= '<a class="btn btn-success process-btn" data-toggle="modal" data-target="#confirm-settlement-process" href="' . $this->view->url(
                        array_merge(['action' => 'process', 'id' => $id], $additionParams)
                    ) . '"><i class="icon-check icon-white"></i> Process</a>';
                    break;
                case 'terminate':
                    $buttonsHTML .= '<a class="btn btn-mini btn-danger" href="' . $this->view->url(
                        array_merge([
                                'action' => 'changestatus',
                                'id' => $id,
                                'status' => 'STATUS_TERMINATED',
                            ], $additionParams)
                    ) . '"><i class="icon-pause icon-white"></i> Terminate</a>';
                    break;
                case 'rehire':
                    $buttonsHTML .= '<a class="btn btn-mini btn-info" href="' . $this->view->url(
                        array_merge([
                                'action' => 'changestatus',
                                'id' => $id,
                                'status' => 'STATUS_ACTIVE',
                            ], $additionParams)
                    ) . '"><i class="icon-repeat icon-white"></i> Rehire</a>';
                    break;
                case 'show':
                    $buttonsHTML .= '<a class="btn btn-mini btn-success" href="' . $this->view->url(
                        array_merge([
                                'action' => 'edit',
                                'id' => $id,
                                'status' => 'STATUS_ACTIVE',
                            ], $additionParams)
                    ) . '"><i class="icon-eye-open icon-white"></i> Show</a>';
                    break;
                case 'select':
                    $buttonsHTML .= '<a class="btn btn-mini btn-primary" href="' . $this->view->url(
                        array_merge(['action' => 'edit', 'setup' => $id], $additionParams)
                    ) . '"><i class="icon-ok icon-white"></i> Select</a>';
                    break;
                case 'set-code':
                    $buttonsHTML .= '<a class="btn btn-mini btn-success set-code" record-id="' . $id . '"><i class="icon-edit icon-white"></i>Set code</a>';
                    break;
                case 'reserve-accounts-contractor':
                    $buttonsHTML .= '<a class="btn btn-primary" href="' . $this->view->url(
                        array_merge([
                                'action' => 'list',
                                'controller' => 'reserve_accountcontractor',
                                'entity' => $id,
                            ], $additionParams),
                        null,
                        true
                    ) . '"><i class="icon-pencil icon-white"></i>' . ' Reserve Accounts</a>';
                    break;
                case 'reserve-accounts-vendor':
                    $buttonsHTML .= '<a class="btn btn-primary" href="' . $this->view->url(
                        array_merge([
                                'action' => 'list',
                                'controller' => 'reserve_accountcarriervendor',
                                'entity' => $id,
                            ], $additionParams),
                        null,
                        true
                    ) . '"><i class="icon-pencil icon-white"></i>' . ' Reserve Accounts</a>';
                    break;
                case 'reserve-accounts-carrier':
                    $buttonsHTML .= '<a class="btn btn-primary" href="' . $this->view->url(
                        array_merge([
                                'action' => 'list',
                                'controller' => 'reserve_accountcarriervendor',
                                'entity' => $id,
                            ], $additionParams),
                        null,
                        true
                    ) . '"><i class="icon-pencil icon-white"></i>' . ' Reserve Accounts</a>';
                    break;
                case 'reserve-transactions-adjustment':
                    $buttonsHTML .= '<a class="btn btn-success" href="' . $this->view->url(
                        array_merge(
                            ['controller' => 'reserve_transactions', 'action' => 'new', 'account' => $id],
                            $additionParams
                        ),
                        null,
                        true
                    ) . '"><i class="icon-plus icon-white"></i> Adjustment</a>';
                    break;
                case 'export':
                    $buttonsHTML .= '<a class="btn btn-success" data-toggle="modal" data-target="#confirm-settlement-export" href="' . $this->view->url(
                        array_merge(['action' => 'export', 'id' => $id], $additionParams)
                    ) . '"><i class="icon-share icon-white"></i> Export</a>';
                    break;

            }
            $buttonsHTML .= '&nbsp;';
        }
        $buttonsHTML = substr($buttonsHTML, 0, strlen($buttonsHTML) - 6);

        return $buttonsHTML;
    }
}

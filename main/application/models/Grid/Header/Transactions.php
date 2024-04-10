<?php

class Application_Model_Grid_Header_Transactions implements Application_Model_Grid_Header_HeaderInterface
{
    public function getData($grid, $view)
    {
        $buttons = [];

        $delete = [
            "caption" => "Delete Selected",
            "button_class" => "btn-danger confirm-delete btn-multiaction",
            "confirm-type" => "Deletion",
            "data-confirm-description-title" => "Deleting",
            "icon_class" => "icon-trash",
            "action-type" => "delete",
            "url" => $view->url(['controller' => 'reserve_transactions', 'action' => 'multiaction'], null, true),
        ];

        if ($grid->currentControllerName == 'settlement_index') {
            $delete['url'] .= '?back=' . urlencode(
                'settlement_index/contractor/id/' . Zend_Controller_Front::getInstance()->getRequest()->getParam(
                    'id'
                )
            );
        }

        $contribution = [
            "caption" => "Contribution",
            "button_class" => "btn-success",
            "icon_class" => "icon-plus",
            "url" => "#popup_checkbox_modal",
            "data-toggle" => "modal",
            'data-target' => '.contribution',
        ];

        $withdrawal = [
            "caption" => "Withdrawal",
            "button_class" => "btn-success",
            "icon_class" => "icon-plus",
            "url" => "#popup_checkbox_modal",
            "data-toggle" => "modal",
            'data-target' => '.withdrawal',
        ];

        $contractorId = Zend_Controller_Front::getInstance()->getRequest()->getParam('id');

        $adjustment = [
            "caption" => "Adjustment",
            "button_class" => "btn-success",
            "icon_class" => "icon-plus",
            "action-type" => "delete",
            "url" => $view->url(
                ['controller' => 'reserve_transactions', 'action' => 'new', 'contractor' => $contractorId],
                null,
                true
            ),
        ];

        if ($grid->getSettlementCycleStatus(
        ) == Application_Model_Entity_System_SettlementCycleStatus::VERIFIED_STATUS_ID) {
            if (Application_Model_Entity_Accounts_User::getCurrentUser()->hasPermission(
                Application_Model_Entity_Entity_Permissions::SETTLEMENT_DATA_MANAGE
            )) {
                $buttons['delete'] = $delete;
                if ($grid->currentControllerName == 'settlement_index') {
                    $buttons['adjustment'] = $adjustment;
                }
            }
        }

        if ($grid->getSettlementCycleStatus(
        ) == Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID) {
            if (Application_Model_Entity_Accounts_User::getCurrentUser()->hasPermission(
                Application_Model_Entity_Entity_Permissions::SETTLEMENT_DATA_MANAGE
            )) {
                $buttons['delete'] = $delete;
                $buttons['contribution'] = $contribution;
                $buttons['withdrawal'] = $withdrawal;
            }
        }

        if (Application_Model_Entity_Accounts_User::getCurrentUser()->isOnboarding()) {
            unset($buttons['delete']);
            unset($buttons['contribution']);
            unset($buttons['withdrawal']);
        }

        return $buttons;
    }
}

<?php

class Application_Model_Grid_Header_Deductions implements Application_Model_Grid_Header_HeaderInterface
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
            "url" => $view->url(['controller' => 'deductions_deductions', 'action' => 'multiaction'], null, true),
        ];

        if ($grid->currentControllerName == 'settlement_index') {
            $delete['url'] .= '?back=' . urlencode(
                'settlement_index/contractor/id/' . Zend_Controller_Front::getInstance()->getRequest()->getParam(
                    'id'
                )
            );
        }

        $add = [
            "caption" => "Add",
            "button_class" => "btn-success",
            "icon_class" => "icon-plus",
            "url" => "#popup_checkbox_modal",
            "data-toggle" => "modal",
            'data-target' => '.deduction-setup',
        ];

        $upload = [
            'caption' => 'Upload',
            'button_class' => 'btn-success',
            'icon_class' => 'icon-file',
            'url' => $view->url(
                [
                    'controller' => 'file_index',
                    'action' => 'edit',
                    'file_type' => Application_Model_Entity_System_FileStorageType::CONST_DEDUCTIONS_FILE_TYPE,
                ]
            ),
        ];

        $download = [
            'caption' => 'Download',
            'button_class' => 'btn-success',
            'icon_class' => 'icon-file',
            'url' => $view->url(
                [
                    'controller' => 'file_index',
                    'action' => 'export',
                    'file_type' => Application_Model_Entity_System_FileStorageType::CONST_DEDUCTIONS_FILE_TYPE,
                ]
            ),
        ];

        if ($grid->getSettlementCycleStatus(
        ) == Application_Model_Entity_System_SettlementCycleStatus::VERIFIED_STATUS_ID || $grid->getSettlementCycleStatus(
        ) == Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID) {
            $user = Application_Model_Entity_Accounts_User::getCurrentUser();
            if ($user->hasPermission(Application_Model_Entity_Entity_Permissions::SETTLEMENT_DATA_MANAGE)) {
                $buttons['delete'] = $delete;
                $buttons['add'] = $add;
            }

            if ($user->hasPermission(Application_Model_Entity_Entity_Permissions::UPLOADING)) {
                if ($grid->currentControllerName == 'deductions_deductions') {
                    $buttons['upload'] = $upload;
                    $buttons['download'] = $download;
                }
            }
            if ($user->isOnboarding() && $grid->getSettlementCycleStatus(
            ) == Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID) {
                unset($buttons['delete']);
                unset($buttons['add']);
                unset($buttons['upload']);
            }
        }

        return $buttons;
    }
}

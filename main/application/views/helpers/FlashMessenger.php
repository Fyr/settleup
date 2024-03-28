<?php

class Application_Views_Helpers_FlashMessenger extends Zend_View_Helper_Abstract
{
    protected $_flashmessenger;
    final public const T_EXCEPTION = '/flashmessenger/exception_message.phtml';
    final public const T_ERROR = '/flashmessenger/error_message.phtml';
    final public const T_CHECKBOX_POPUP_ERROR = '/flashmessenger/checkbox_popup_error_message.phtml';
    final public const T_WARNING = '/flashmessenger/warning_message.phtml';
    final public const T_INFO = '/flashmessenger/info_message.phtml';
    final public const T_OK = '/flashmessenger/ok_message.phtml';

    public function __construct()
    {
        $this->_flashmessenger = new Zend_Controller_Action_Helper_FlashMessenger();
    }

    /**
     * @return string Complited template
     */
    public function flashMessenger()
    {
        $output = '';
        $messages = array_merge($this->_flashmessenger->getCurrentMessages(), $this->getTCheckboxPopupErrors());

        foreach ($messages as $message) {
            $output .= $this->view->partial(
                $this->getTemplate($message),
                $this->getData($message)
            );
        }

        return $output;
    }

    public function getTCheckboxPopupErrors()
    {
        $messages = $this->_flashmessenger->getMessages();
        foreach ($messages as $key => $message) {
            if (!isset($message['type']) || $message['type'] != 'T_CHECKBOX_POPUP_ERROR') {
                unset($messages[$key]);
            }
        }

        return $messages;
    }

    /**
     * @param array $message
     * @return string Name of template
     */
    public function getTemplate($message)
    {
        if (!isset($message['type'])) {
            $message['type'] = 'T_WARNING';
        }
        $template = constant('self::' . $message['type']);

        return $template;
    }

    /**
     * @param array $message
     * @return array
     */
    public function getData($message)
    {
        if ('T_EXCEPTION' == $message['type']) {
            $data = [
                'title' => $message['title'],
                'message' => $message['message']->getMessage(),
                'stackTrace' => $message['message']->getTraceAsString(),
                'code' => $message['message']->getCode(),
                'request' => var_export(
                    $message['request']->getParams(),
                    true
                ),
            ];
        } elseif ('T_CHECKBOX_POPUP_ERROR' == $message['type']) {
            $data = [
                'title' => $message['title'],
                'messages' => $message['messages'],
                'headerMessages' => $message['headerMessages'],
            ];
        } else {
            $namespace = $this->_flashmessenger->getNamespace();
            if (isset($message['messages'][$namespace])) {
                $message['message'] = implode('</br>', $message['messages'][$namespace]);
            }
            $data = [
                'title' => $message['title'],
                'message' => $message['message'],
            ];
        }

        return $data;
    }
}

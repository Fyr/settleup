<?php

trait Application_Plugin_Messager
{
    protected $messages = [];
    protected $headerMessages = [];
    protected $warningMessages = [];
    protected $errorMessages = [];

    public function addMessage($params, $namespace = "default", $messageId = null)
    {
        $message = vsprintf($this->getMessageTemplate(), $params);
        if ($messageId) {
            $this->messages[$namespace][$messageId] = $message;
        } else {
            $this->messages[$namespace][] = $message;
        }

        return $message;
    }

    public function addWarningMessage($params, $namespace = "default", $messageId = null)
    {
        $message = vsprintf($this->getMessageTemplate(), $params);
        if ($messageId) {
            $this->warningMessages[$namespace][$messageId] = $message;
        } else {
            $this->warningMessages[$namespace][] = $message;
        }

        return $message;
    }

    public function addErrorMessage($params, $namespace = "default", $messageId = null)
    {
        $message = vsprintf($this->getMessageTemplate(), $params);
        if ($messageId) {
            $this->errorMessages[$namespace][$messageId] = $message;
        } else {
            $this->errorMessages[$namespace][] = $message;
        }

        return $message;
    }

    public function getErrorMessages($namespace = null, $messageId = null)
    {
        if ($namespace) {
            if ($messageId) {
                return $this->errorMessages[$namespace][$messageId] ?? false;
            } else {
                return $this->errorMessages[$namespace] ?? false;
            }
        } else {
            return $this->errorMessages;
        }
    }

    public function getWarningMessages($namespace = null, $messageId = null)
    {
        if ($namespace) {
            if ($messageId) {
                return $this->warningMessages[$namespace][$messageId] ?? false;
            } else {
                return $this->warningMessages[$namespace] ?? false;
            }
        } else {
            return $this->warningMessages;
        }
    }

    public function hasErrorMessages($namespace = null)
    {
        if (!$messages = $this->getErrorMessages($namespace)) {
            return false;
        }

        return (bool)(count($messages));
    }

    public function hasWarningMessages($namespace = null)
    {
        if (!$messages = $this->getWarningMessages($namespace)) {
            return false;
        }

        return (bool)(count($messages));
    }

    public function addMessages($messages, $namespace = 'default')
    {
        if (!isset($this->messages[$namespace])) {
            $this->messages[$namespace] = $messages;
        } else {
            $this->messages[$namespace] = array_merge($this->messages[$namespace], $messages);
        }

        return $this;
    }

    public function setHeaderMessage($params, $namespace = "default", $errorWarning = null)
    {
        $message = vsprintf($this->getHeaderMessageTemplate($errorWarning), $params);
        $this->headerMessages[$namespace] = $message;

        return $message;
    }

    public function getHeaderMessage($namespace = 'default')
    {
        if (isset($this->headerMessages[$namespace])) {
            return $this->headerMessages[$namespace];
        } else {
            return false;
        }
    }

    public function getHeaderMessages()
    {
        return $this->headerMessages;
    }

    public function removeMessage($messageId, $namespace = 'default')
    {
        if (isset($this->messages[$namespace][$messageId])) {
            unset($this->messages[$namespace][$messageId]);

            return true;
        } else {
            return false;
        }
    }

    public function getMessages($namespace = null, $messageId = null)
    {
        if ($namespace) {
            if ($messageId) {
                return $this->messages[$namespace][$messageId] ?? false;
            } else {
                return $this->messages[$namespace] ?? false;
            }
        } else {
            return $this->messages;
        }
    }

    public function getMessageTemplate()
    {
        return "%s";
    }

    public function getHeaderMessageTemplate($error = null)
    {
        return "%s";
    }

    public function hasMessages($namespace = null)
    {
        if (!$messages = $this->getMessages($namespace)) {
            return false;
        }

        return (bool)(count($messages));
    }

    public function implodeMessages($namespace = 'default', $glue = '', $template = '%s')
    {
        if (isset($this->messages[$namespace])) {
            $singleMessage = implode($glue, $this->messages[$namespace]);
            if ($template) {
                $singleMessage = sprintf($template, $singleMessage);
            }
            $this->messages[$namespace] = [$singleMessage];

            return $this->messages[$namespace];
        } else {
            return false;
        }
    }
}

<?php

class Application_View_Helper_FlashMessage
{
    protected string $_message;
    protected string $_type;

    final public const T_EXCEPTION = 'T_EXCEPTION';
    final public const T_ERROR = 'T_ERROR';
    final public const T_WARNING = 'T_WARNING';
    final public const T_INFO = 'T_INFO';
    final public const T_OK = 'T_OK';

    /**
     * @throws Exception
     */
    public function __construct(string $message, string $type)
    {
        $this->_message = $message;
        if (!in_array($type, [$this::T_EXCEPTION, $this::T_ERROR, $this::T_WARNING, $this::T_INFO, $this::T_OK])) {
            throw new Exception('Unsupported message type = ' . $type);
        }
        $this->_type = $type;
    }

    public function getMessage(): string
    {
        return $this->_message;
    }

    public function getType(): string
    {
        return $this->_type;
    }
}

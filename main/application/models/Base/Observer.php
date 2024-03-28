<?php

abstract class Application_Model_Base_Observer implements SplObserver
{
    public static $observingRelationship = [];
    protected $method;

    public function __construct(private $observable, protected $event)
    {
    }

    /**
     * @param $method string
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function update(SplSubject $subject): void
    {
        if ($this->assersEvent()) {
            $this->doUpdate($subject);
        }
    }

    /**
     * @return bool
     */
    public function assersEvent()
    {
        $method = substr(strstr((string) $this->method, '::'), 2);

        if ($method == $this->event) {
            return true;
        }

        return false;
    }

    /**
     * @abstract
     */
    abstract public function doUpdate(SplSubject $subject);
}

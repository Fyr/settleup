<?php

trait Application_Model_Grid_Callback_BaseTrait
{
    protected static $instance;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    protected $row;
    protected $column;
    protected $view;
    protected $additionalParams;

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function render($row, $column, $view, $additionalParams = [])
    {
        $instance = static::getInstance();
        $this->row = $row;
        $this->column = $column;
        $this->view = $view;
        $this->additionalParams = $additionalParams;

        return (string)$instance->body();
    }

    public function renderWrapper($row, $column, $view, $additionalParams = [])
    {
        $instance = static::getInstance();
        $this->row = $row;
        $this->column = $column;
        $this->view = $view;
        $this->additionalParams = $additionalParams;

        return (string)$instance->wrapper();
    }

    public function wrapper()
    {
        return '';
    }

    public function body()
    {
        return $this->column;
    }
}

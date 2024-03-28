<?php

abstract class Application_Model_File_Base extends Application_Model_Base_Entity
{
    protected $_resource;
    /** @var Application_Model_Base_CryptAdvanced */
    protected $crypt;
    public const TYPE = '';

    public function __construct($fileName, $title = null)
    {
        $this->crypt = new Application_Model_Base_CryptAdvanced();
        $this->setFileName($fileName);
        $this->setTitle($title);
    }

    public function getContent()
    {
        if (filesize($this->getFullFileName()) > 0) {
            return fread(
                $this->getResource(),
                filesize($this->getFullFileName())
            );
        } else {
            throw new Exception(
                sprintf('File "%s" is empty', $this->getFileName())
            );
        }
    }

    public function getType()
    {
        return static::TYPE;
    }

    public function getResource()
    {
        if (empty($this->_resource)) {
            $this->_resource = fopen($this->getFullFileName(), 'r');
        }

        return $this->_resource;
    }

    public function getFullFileName()
    {
        return Application_Model_File::getStorage() . '/' . $this->getFileName();
    }

    abstract public function getExportFile($idOrFilters = null);
}

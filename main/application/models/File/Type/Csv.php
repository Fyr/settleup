<?php

class Application_Model_File_Type_Csv extends Application_Model_File_Base
{
    final public const TYPE = 'csv';

    public function getContent()
    {
        $content = [];

        while (($data = fgetcsv(
            $this->getResource(),
            filesize($this->getFullFileName())
        )) !== false) {
            array_push($content, $data);
        }

        return $content;
    }

    public function getExportFile($idOrFilters = null)
    {
        return '';//TODO: This is a stub;
    }
}

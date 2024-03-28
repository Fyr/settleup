<?php

class Application_Model_File_Type_Txt extends Application_Model_File_Base
{
    final public const TYPE = 'txt';

    public function getContent()
    {
        $content = parent::getContent();

        return explode(PHP_EOL, (string) $content);
    }

    public function getExportFile($idOrFilters = null)
    {
        return '';//TODO: This is a stub;
    }
}

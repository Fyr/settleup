<?php

class Application_Model_Import
{
    public function import(string $fileName, string $title, int $fileType, int $timestamp)
    {
        $file = Application_Model_File::getInstance($fileName, $title);
        $file->setFileType($fileType);
        $file->setFileName($timestamp . '_' . Application_Model_File::getSafeName($fileName));

        return $file->save();
    }
}

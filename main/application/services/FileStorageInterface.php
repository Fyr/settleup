<?php

interface Application_Service_FileStorageInterface
{
    public function uploadFile(string $namespace, string $fileName, mixed $content);
}

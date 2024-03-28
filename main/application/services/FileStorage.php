<?php

use Application_Service_FileStorageInterface as FileStorageInterface;

final class Application_Service_FileStorage implements FileStorageInterface
{
    private FileStorageInterface $storage;

    public function __construct(FileStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @throws Exception
     */
    public function uploadFile(string $namespace, string $fileName, mixed $content): string
    {
        return $this->storage->uploadFile($namespace, $fileName, $content);
    }
}

<?php

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\GetBlobResult;
use MicrosoftAzure\Storage\Blob\Models\GetContainerPropertiesResult;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
use MicrosoftAzure\Storage\Blob\Models\PutBlobResult;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;

class Application_Service_Azure_StorageBlob implements Application_Service_FileStorageInterface
{
    private const HOST = 'https://settlement%ssa.blob.core.windows.net';

    private BlobRestProxy $blobClient;
    private string $environment;

    public function __construct()
    {
        $options = Zend_Registry::getInstance()->options;
        $connectionString = $options['azure']['blobConnectionString'];
        $this->blobClient = BlobRestProxy::createBlobService($connectionString);
        $this->environment = $options['environment'];
    }

    /**
     * @throws Exception
     */
    public function uploadFile(string $namespace, string $fileName, mixed $content): string
    {
        $stream = fopen($content['tmp_name'], 'r');
        $this->uploadBlob($namespace, $fileName, $stream);

        return $this->getSourceLink($namespace, $fileName);
    }

    private function getSourceLink(string $namespace, string $fileName): string
    {
        return $this->getHostUrl() . '/' . $namespace . '/' . $fileName;
    }

    private function getHostUrl(): string
    {
        return sprintf(self::HOST, $this->environment);
    }

    /**
     * @throws Exception
     */
    protected function createContainer(string $containerName, array $metadata = null): void
    {
        $createContainerOptions = new CreateContainerOptions();
        $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);

        foreach ($metadata as $key => $value) {
            $createContainerOptions->addMetaData($key, $value);
        }

        try {
            $this->blobClient->createContainer($containerName, $createContainerOptions);
        } catch (ServiceException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    protected function getContainerProperties(string $containerName): GetContainerPropertiesResult
    {
        try {
            $result = $this->blobClient->getContainerProperties($containerName);
        } catch (ServiceException $e) {
            throw new Exception($e->getMessage());
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    protected function deleteContainer(string $containerName): void
    {
        try {
            $this->blobClient->deleteContainer($containerName);
        } catch (ServiceException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    protected function uploadBlob(string $namespace, string $fileName, mixed $content): PutBlobResult
    {
        try {
            $result = $this->blobClient->createBlockBlob($namespace, $fileName, $content);
        } catch (ServiceException $e) {
            throw new Exception($e->getMessage());
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    protected function getBlob(string $containerName, string $blobName): GetBlobResult
    {
        try {
            $result = $this->blobClient->getBlob($containerName, $blobName);
        } catch (ServiceException $e) {
            throw new Exception($e->getMessage());
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    protected function deleteBlob(string $containerName, string $blobName): void
    {
        try {
            $this->blobClient->deleteBlob($containerName, $blobName);
        } catch (ServiceException $e) {
            throw new Exception($e->getMessage());
        }
    }
}

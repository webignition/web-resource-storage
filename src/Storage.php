<?php

namespace webignition\WebResource;

use Symfony\Component\Cache\Simple\FilesystemCache;
use webignition\WebResourceInterfaces\WebResourceInterface;

class Storage
{
    const CACHE_NAMESPACE = 'web-resource-storage';

    /**
     * Translates local file paths to their relevant resource URLs
     *
     * @var array
     */
    private $localPathToResourceUrlMap = [];

    /**
     * @var string[]
     */
    private $paths = [];

    /**
     * @param WebResourceInterface $resource
     *
     * @return string
     */
    public function store(WebResourceInterface $resource)
    {
        $path = $this->createPath($resource);
        $localPathHash = $this->createLocalPathHash($path);

        file_put_contents($path, $resource->getContent());

        $this->localPathToResourceUrlMap[$localPathHash] = (string)$resource->getUri();
        $this->paths[] = $path;

        return $path;
    }

    /**
     * @param string $path
     *
     * @return string|null
     */
    public function getUrlFromPath($path)
    {
        $pathHash = $this->createLocalPathHash($path);

        return isset($this->localPathToResourceUrlMap[$pathHash])
            ? $this->localPathToResourceUrlMap[$pathHash]
            : null;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function createLocalPathHash($path)
    {
        return md5($path);
    }

    /**
     * @param WebResourceInterface $webResource
     *
     * @return string
     */
    private function createPath(WebResourceInterface $webResource)
    {
        return sys_get_temp_dir()
            . '/'
            . md5((string)$webResource->getUri() . microtime(true))
            . '.'
            . ContentTypeFileExtensionMap::getFileExtension($webResource->getContentType());
    }

    public function reset()
    {
        foreach ($this->paths as $webResourceHash => $path) {
            @unlink($path);
        }
    }
}
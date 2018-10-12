<?php

namespace webignition\WebResource;

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

    public function store(WebResourceInterface $resource): string
    {
        $path = $this->createPath($resource);
        $localPathHash = $this->createLocalPathHash($path);

        file_put_contents($path, $resource->getContent());

        $this->localPathToResourceUrlMap[$localPathHash] = (string)$resource->getUri();
        $this->paths[] = $path;

        return $path;
    }

    public function getUrlFromPath(string $path): ?string
    {
        $pathHash = $this->createLocalPathHash($path);

        return isset($this->localPathToResourceUrlMap[$pathHash])
            ? $this->localPathToResourceUrlMap[$pathHash]
            : null;
    }

    private function createLocalPathHash(string $path): string
    {
        return md5($path);
    }

    private function createPath(WebResourceInterface $webResource): string
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

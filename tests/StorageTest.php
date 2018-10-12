<?php

namespace webignition\Tests\WebResource;

use Psr\Http\Message\UriInterface;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResource\Storage;
use webignition\WebResourceInterfaces\WebPageInterface;
use webignition\WebResourceInterfaces\WebResourceInterface;

class StorageTest extends \PHPUnit\Framework\TestCase
{
    const HTML_CONTENT = '<doctype html><html></html>';
    const CSS_CONTENT = '.foo {}';
    const JS_CONTENT = 'var foo = 1;';

    /**
     * @var Storage
     */
    private $storage;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->storage = new Storage();
    }

    /**
     * @dataProvider storeDataProvider
     *
     * @param WebResourceInterface $webResource
     * @param string $expectedPathExtension
     * @param string $expectedStoredContent
     */
    public function testStore(
        WebResourceInterface $webResource,
        string $expectedPathExtension,
        string $expectedStoredContent
    ) {
        $path = $this->storage->store($webResource);
        $this->assertRegExp('/\/tmp\/[a-z0-9]{32}\.[a-z]{2,4}/', $path);
        $this->assertFileExists($path);

        $pathFileExtension = substr($path, strpos($path, '.') + 1);
        $this->assertEquals($expectedPathExtension, $pathFileExtension);

        $storedContents = file_get_contents($path);
        $this->assertEquals($expectedStoredContent, $storedContents);
    }

    public function storeDataProvider(): array
    {
        return [
            'web page' => [
                'webResource' => $this->createWebPage(
                    $this->createUri('http://example.com/index.html'),
                    self::HTML_CONTENT
                ),
                'expectedPathExtension' => 'html',
                'expectedStoredContent' => self::HTML_CONTENT,
            ],
            'text/css' => [
                'webResource' => $this->createWebResource(
                    $this->createUri('http://example.com/style.css'),
                    $this->createInternetMediaType('css'),
                    self::CSS_CONTENT
                ),
                'expectedPathExtension' => 'css',
                'expectedStoredContent' => self::CSS_CONTENT,
            ],
            'text/javascript' => [
                'webResource' => $this->createWebResource(
                    $this->createUri('http://example.com/app.js'),
                    $this->createInternetMediaType('javascript'),
                    self::JS_CONTENT
                ),
                'expectedPathExtension' => 'js',
                'expectedStoredContent' => self::JS_CONTENT,
            ],
            'application/javascript' => [
                'webResource' => $this->createWebResource(
                    $this->createUri('http://example.com/app.js'),
                    $this->createInternetMediaType('javascript'),
                    self::JS_CONTENT
                ),
                'expectedPathExtension' => 'js',
                'expectedStoredContent' => self::JS_CONTENT,
            ],
            'application/x-javascript' => [
                'webResource' => $this->createWebResource(
                    $this->createUri('http://example.com/app.js'),
                    $this->createInternetMediaType('x-javascript'),
                    self::JS_CONTENT
                ),
                'expectedPathExtension' => 'js',
                'expectedStoredContent' => self::JS_CONTENT,
            ],
        ];
    }

    public function testGetUrlFromPath()
    {
        $webPageUrl = 'http://example.com/index.html';
        $cssResourceUrl = 'http://example.com/style.css';

        $webPage = $this->createWebPage(
            $this->createUri($webPageUrl),
            self::HTML_CONTENT
        );

        /* @var WebResourceInterface $cssResource */
        $cssResource = $this->createWebResource(
            $this->createUri($cssResourceUrl),
            $this->createInternetMediaType('css'),
            self::CSS_CONTENT
        );

        $webPagePath = $this->storage->store($webPage);
        $cssResourcePath = $this->storage->store($cssResource);

        $this->assertEquals($webPageUrl, $this->storage->getUrlFromPath($webPagePath));
        $this->assertEquals($cssResourceUrl, $this->storage->getUrlFromPath($cssResourcePath));
        $this->assertNull($this->storage->getUrlFromPath('foo'));
    }

    public function testReset()
    {
        $webPage = $this->createWebPage(
            $this->createUri('http://example.com/index.html'),
            self::HTML_CONTENT
        );

        /* @var WebResourceInterface $cssResource */
        $cssResource = $this->createWebResource(
            $this->createUri('http://example.com/style.css'),
            $this->createInternetMediaType('css'),
            self::CSS_CONTENT
        );

        $webPagePath = $this->storage->store($webPage);
        $cssResourcePath = $this->storage->store($cssResource);

        $this->assertFileExists($webPagePath);
        $this->assertFileExists($cssResourcePath);

        $this->storage->reset();

        $this->assertFileNotExists($webPagePath);
        $this->assertFileNotExists($cssResourcePath);
    }

    private function createWebPage(UriInterface $uri, string $content): WebPageInterface
    {
        $webPage = $this->createWebResource(
            $uri,
            $this->createInternetMediaType('html'),
            $content,
            WebPageInterface::class
        );

        return $webPage;
    }

    private function createWebResource(
        UriInterface $uri,
        InternetMediaTypeInterface $internetMediaType,
        string $content,
        ?string $resourceClassName = null
    ) {
        $resourceClassName = $resourceClassName ?? WebResourceInterface::class;

        $webResource = \Mockery::mock($resourceClassName);

        $webResource
            ->shouldReceive('getUri')
            ->andReturn($uri);

        $webResource
            ->shouldReceive('getContentType')
            ->andReturn($internetMediaType);

        $webResource
            ->shouldReceive('getContent')
            ->andReturn($content);

        return $webResource;
    }

    private function createInternetMediaType(string $subType): InternetMediaTypeInterface
    {
        $internetMediaType = \Mockery::mock(InternetMediaTypeInterface::class);

        $internetMediaType
            ->shouldReceive('getSubtype')
            ->andReturn($subType);

        return $internetMediaType;
    }

    private function createUri(string $uriString): UriInterface
    {
        $uri = \Mockery::mock(UriInterface::class);

        $uri
            ->shouldReceive('__toString')
            ->andReturn($uriString);

        return $uri;
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->storage->reset();
    }
}

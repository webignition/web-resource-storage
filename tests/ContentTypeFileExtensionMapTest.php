<?php

namespace webignition\Tests\WebResource;

use Mockery\Mock;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResource\ContentTypeFileExtensionMap;

class ContentTypeFileExtensionMapTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider getFileExtensionDataProvider
     *
     * @param InternetMediaTypeInterface $internetMediaType
     * @param string $expectedFileExtension
     */
    public function testGetFileExtension(InternetMediaTypeInterface $internetMediaType, string $expectedFileExtension)
    {
        $this->assertEquals($expectedFileExtension, ContentTypeFileExtensionMap::getFileExtension($internetMediaType));
    }

    public function getFileExtensionDataProvider(): array
    {
        return [
            'text/html' => [
                'internetMediaType' => $this->createInternetMediaType('html'),
                'expectedFileExtension' => 'html',
            ],
            'text/css' => [
                'internetMediaType' => $this->createInternetMediaType('css'),
                'expectedFileExtension' => 'css',
            ],
            'application/javascript' => [
                'internetMediaType' => $this->createInternetMediaType('javascript'),
                'expectedFileExtension' => 'js',
            ],
            'application/x-javascript' => [
                'internetMediaType' => $this->createInternetMediaType('x-javascript'),
                'expectedFileExtension' => 'js',
            ],
        ];
    }

    private function createInternetMediaType(string $subtype): InternetMediaTypeInterface
    {
        /* @var InternetMediaTypeInterface|Mock $internetMediaType */
        $internetMediaType = \Mockery::mock(InternetMediaTypeInterface::class);

        $internetMediaType
            ->shouldReceive('getSubtype')
            ->andReturn($subtype);

        return $internetMediaType;
    }
}

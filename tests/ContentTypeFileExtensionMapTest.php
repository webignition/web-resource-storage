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
    public function testGetFileExtension(InternetMediaTypeInterface $internetMediaType, $expectedFileExtension)
    {
        $this->assertEquals($expectedFileExtension, ContentTypeFileExtensionMap::getFileExtension($internetMediaType));
    }

    /**
     * @return array
     */
    public function getFileExtensionDataProvider()
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

    /**
     * @param string $subtype
     *
     * @return Mock|InternetMediaTypeInterface
     */
    private function createInternetMediaType($subtype)
    {
        /* @var InternetMediaTypeInterface|Mock $internetMediaType */
        $internetMediaType = \Mockery::mock(InternetMediaTypeInterface::class);

        $internetMediaType
            ->shouldReceive('getSubtype')
            ->andReturn($subtype);

        return $internetMediaType;
    }
}

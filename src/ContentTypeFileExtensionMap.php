<?php

namespace webignition\WebResource;

use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;

class ContentTypeFileExtensionMap
{
    /**
     * @var array
     */
    private static $subTypeFileExtensionMap = [
        'javascript' => 'js',
        'x-javascript' => 'js',
    ];

    /**
     * @param InternetMediaTypeInterface $contentType
     *
     * @return string
     */
    public static function getFileExtension(InternetMediaTypeInterface $contentType)
    {
        $mediaSubType = $contentType->getSubtype();

        if (array_key_exists($mediaSubType, self::$subTypeFileExtensionMap)) {
            return self::$subTypeFileExtensionMap[$mediaSubType];
        }

        return $mediaSubType;
    }
}

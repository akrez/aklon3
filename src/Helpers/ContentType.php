<?php

namespace Src\Helpers;

class ContentType
{
    public static function getHeaderContentType($response)
    {
        return implode(',', $response->getHeader('content-type'));
    }

    public static function getContentType($response)
    {
        return trim(preg_replace('@;.*@', '', static::getHeaderContentType($response)));
    }
}

<?php

namespace App\Helpers;

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

    public static function isTextCss($response): bool
    {
        return boolval(ContentType::getContentType($response) == 'text/css');
    }

    public static function isTextHtml($response): bool
    {
        return boolval(ContentType::getContentType($response) == 'text/html');
    }
}

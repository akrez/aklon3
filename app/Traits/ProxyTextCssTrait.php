<?php

namespace App\Traits;

use App\Aklon;
use App\Helpers\ContentType;

trait ProxyTextCssTrait
{
    public function isTextCss($response): bool
    {
        return boolval(ContentType::getContentType($response) == 'text/css');
    }

    public function convertToTextCss($body, Aklon $aklon, $mainUrl)
    {
        $body = preg_replace_callback('/@import\s+([\'"])(.*?)\1(?![^;]*url)/ix', function ($matches) use ($aklon, $mainUrl) {
            $url = trim($matches[2]);
            $changed = $aklon->encryptUrl($url, $mainUrl);

            return str_replace($url, $changed, $matches[0]);
        }, $body);

        $body = preg_replace_callback('/url\s*\(\s*([\'"]?)(.*?)\1\s*\)/ix', function ($matches) use ($aklon, $mainUrl) {
            $url = trim($matches[2]);
            $changed = $aklon->encryptUrl($url, $mainUrl);

            return str_replace($url, $changed, $matches[0]);
        }, $body);

        return $body;
    }
}

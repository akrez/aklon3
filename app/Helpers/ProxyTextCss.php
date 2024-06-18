<?php

namespace App\Helpers;

use App\Interfaces\Crypt;

class ProxyTextCss
{
    public function __construct(
        public Crypt $crypt
    ) {
    }

    public static function isTextCss($response): bool
    {
        return ContentType::isTextCss($response);
    }

    public function encryptUrl($url, $mainUrl)
    {
        return $this->crypt->encryptUrl($url, $mainUrl);
    }

    public function convertToTextCss($body, $mainUrl)
    {
        $body = preg_replace_callback('/@import\s+([\'"])(.*?)\1(?![^;]*url)/ix', function ($matches) use ($mainUrl) {
            $url = trim($matches[2]);
            $changed = $this->encryptUrl($url, $mainUrl);

            return str_replace($url, $changed, $matches[0]);
        }, $body);

        $body = preg_replace_callback('/url\s*\(\s*([\'"]?)(.*?)\1\s*\)/ix', function ($matches) use ($mainUrl) {
            $url = trim($matches[2]);
            $changed = $this->encryptUrl($url, $mainUrl);

            return str_replace($url, $changed, $matches[0]);
        }, $body);

        return $body;
    }
}

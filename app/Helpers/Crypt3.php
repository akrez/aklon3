<?php

namespace App\Helpers;

class Crypt3
{
    private string $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = Url::trim($baseUrl);
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getPreferredSchema(): string
    {
        return 'https';
    }

    public function decryptUrl(string $encryptedUrl): ?string
    {
        if (strpos($encryptedUrl, $this->baseUrl) !== 0) {
            return null;
        }

        $decryptedSlashedUrl = substr($encryptedUrl, strlen($this->baseUrl));
        $decryptedTrimedSlashedUrl = Url::trim($decryptedSlashedUrl);
        $decryptedArrayUrl = explode('/', $decryptedTrimedSlashedUrl, 2);

        if (
            count($decryptedArrayUrl) === 2
            and in_array($decryptedArrayUrl[0], ['http', 'https'])
            and $decryptedUrl = $decryptedArrayUrl[0].'://'.$decryptedArrayUrl[1]
            and filter_var($decryptedUrl, FILTER_VALIDATE_URL)
        ) {
            return $decryptedUrl;
        }

        return null;
    }

    public function encryptUrl(string $url, ?string $mainUrl = null)
    {
        if ($mainUrl) {
            $url = Url::convertRelativeToAbsoluteUrl($mainUrl, $url);
        }

        if (
            strpos($url, '//') === false
            and strpos($url, 'https://') === false
            and strpos($url, 'http://') === false
        ) {
            $url = (static::getPreferredSchema().'://'.$url);
        }

        if (strpos($url, 'https://') === 0) {
            return str_replace('https://', $this->baseUrl.'/https/', $url);
        }

        if (strpos($url, 'http://') === 0) {
            return str_replace('http://', $this->baseUrl.'/http/', $url);
        }

        return $url;
    }
}

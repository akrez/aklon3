<?php

namespace App\Helpers;

use App\Interfaces\Crypt;

class Crypt2 implements Crypt
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
        $parsedBaseUrl = Url::parse($baseUrl);
        if (!$parsedBaseUrl['query']) {
            return null;
        }

        $parsedBaseUrlQuery = [];
        parse_str($parsedBaseUrl['query'], $parsedBaseUrlQuery);
        if (!isset($parsedBaseUrlQuery['q'])) {
            return null;
        }

        return Url::decrypt($parsedBaseUrlQuery['q']);
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
            $url = ($this->encryptionPreferredSchema.'://'.$url);
        }

        $encryptedUrl = Url::encrypt($url);

        $parsedBaseUrl = Url::parse($this->baseUrl);
        if ($parsedBaseUrl['query']) {
            $parsedBaseUrl['query'] = $parsedBaseUrl['query'].'&';
        }
        $parsedBaseUrl['query'] .= 'q='.$encryptedUrl;

        return Url::unparse($parsedBaseUrl);
    }
}

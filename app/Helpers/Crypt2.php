<?php

namespace App\Helpers;

use App\Interfaces\Crypt;
use App\Interfaces\Encryption;

class Crypt2 implements Crypt
{
    private string $baseUrl;

    private Encryption $encryption;

    public function __construct(string $baseUrl, Encryption $encryption)
    {
        $this->baseUrl = Url::trim($baseUrl);
        $this->encryption = $encryption;
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
        $parsedBaseUrl = Url::parse($this->baseUrl);
        if (! $parsedBaseUrl['query']) {
            return null;
        }

        $parsedBaseUrlQuery = [];
        parse_str($parsedBaseUrl['query'], $parsedBaseUrlQuery);
        if (! isset($parsedBaseUrlQuery['q'])) {
            return null;
        }

        return $this->encryption->decrypt($parsedBaseUrlQuery['q']);
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

        $encryptedUrl = $this->encryption->encrypt($url);

        $parsedBaseUrl = Url::parse($this->baseUrl);
        if ($parsedBaseUrl['query']) {
            $parsedBaseUrl['query'] = $parsedBaseUrl['query'].'&';
        }
        $parsedBaseUrl['query'] .= 'q='.$encryptedUrl;

        return Url::unparse($parsedBaseUrl);
    }
}

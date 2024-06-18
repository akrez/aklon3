<?php

namespace App\Helpers;

use App\Interfaces\Crypt;

class ProxyTextHtml
{
    public function __construct(
        public Crypt $crypt
    ) {
    }

    public static function isTextHtml($response): bool
    {
        return ContentType::isTextHtml($response);
    }

    public function encryptUrl($url, $mainUrl)
    {
        return $this->crypt->encryptUrl($url, $mainUrl);
    }

    public function convertToTextHtml($body, $mainUrl)
    {
        $body = preg_replace_callback('@(?:src|href)\s*=\s*(["|\'])(.*?)\1@is', function ($matches) use ($mainUrl) {
            $url = trim($matches[2]);
            $types = ['data:', 'magnet:', 'about:', 'javascript:', 'mailto:', 'tel:', 'ios-app:', 'android-app:'];
            if (Utils::startsWith($url, $types)) {
                return $matches[0];
            }
            $changed = $this->encryptUrl($url, $mainUrl);

            return str_replace($url, $changed, $matches[0]);
        }, $body);

        $body = preg_replace_callback('@<form[^>]*action=(["\'])(.*?)\1[^>]*>@i', function ($matches) use ($mainUrl) {
            $action = trim($matches[2]);
            if (! $action) {
                return '';
            }
            $changed = $this->encryptUrl($action, $mainUrl);

            return str_replace($action, $changed, $matches[0]);
        }, $body);

        $body = preg_replace_callback('/content=(["\'])\d+\s*;\s*url=(.*?)\1/is', function ($matches) use ($mainUrl) {
            $url = trim($matches[2]);
            $changed = $this->encryptUrl($url, $mainUrl);

            return str_replace($url, $changed, $matches[0]);
        }, $body);

        $body = preg_replace_callback('@[^a-z]{1}url\s*\((?:\'|"|)(.*?)(?:\'|"|)\)@im', function ($matches) use ($mainUrl) {
            $url = trim($matches[1]);
            if (Utils::startsWith($url, 'data:')) {
                return $matches[0];
            }
            $changed = $this->encryptUrl($url, $mainUrl);

            return str_replace($url, $changed, $matches[0]);
        }, $body);

        $body = preg_replace_callback('/srcset=\"(.*?)\"/i', function ($matches) use ($mainUrl) {
            $src = trim($matches[1]);
            $urls = preg_split('/\s*,\s*/', $src);
            foreach ($urls as $part) {
                $pos = strpos($part, ' ');
                if ($pos !== false) {
                    $url = substr($part, 0, $pos);

                    $changed = $this->encryptUrl($url, $mainUrl);
                    $src = str_replace($url, $changed, $src);
                }
            }

            return 'srcset="'.$src.'"';
        }, $body);

        return $body;
    }
}

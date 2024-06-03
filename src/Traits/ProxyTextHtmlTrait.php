<?php

namespace Src\Traits;

use Src\Aklon;
use Src\Helpers\ContentType;
use Src\Helpers\Utils;

trait ProxyTextHtmlTrait
{
    public function isTextHtml($response): bool
    {
        return boolval(ContentType::getContentType($response) == 'text/html');
    }

    public function convertToTextHtml($body, Aklon $aklon, $mainUrl)
    {
        $body = preg_replace_callback('@(?:src|href)\s*=\s*(["|\'])(.*?)\1@is', function ($matches) use ($aklon, $mainUrl) {
            $url = trim($matches[2]);
            $types = ['data:', 'magnet:', 'about:', 'javascript:', 'mailto:', 'tel:', 'ios-app:', 'android-app:'];
            if (Utils::startsWith($url, $types)) {
                return $matches[0];
            }
            $changed = $aklon->encryptToBaseUrl($url, $mainUrl);

            return str_replace($url, $changed, $matches[0]);
        }, $body);

        $body = preg_replace_callback('@<form[^>]*action=(["\'])(.*?)\1[^>]*>@i', function ($matches) use ($aklon, $mainUrl) {
            $action = trim($matches[2]);
            if (! $action) {
                return '';
            }
            $changed = $aklon->encryptToBaseUrl($action, $mainUrl);

            return str_replace($action, $changed, $matches[0]);
        }, $body);

        $body = preg_replace_callback('/content=(["\'])\d+\s*;\s*url=(.*?)\1/is', function ($matches) use ($aklon, $mainUrl) {
            $url = trim($matches[2]);
            $changed = $aklon->encryptToBaseUrl($url, $mainUrl);

            return str_replace($url, $changed, $matches[0]);
        }, $body);

        $body = preg_replace_callback('@[^a-z]{1}url\s*\((?:\'|"|)(.*?)(?:\'|"|)\)@im', function ($matches) use ($aklon, $mainUrl) {
            $url = trim($matches[1]);
            if (Utils::startsWith($url, 'data:')) {
                return $matches[0];
            }
            $changed = $aklon->encryptToBaseUrl($url, $mainUrl);

            return str_replace($url, $changed, $matches[0]);
        }, $body);

        $body = preg_replace_callback('/srcset=\"(.*?)\"/i', function ($matches) use ($aklon, $mainUrl) {
            $src = trim($matches[1]);
            $urls = preg_split('/\s*,\s*/', $src);
            foreach ($urls as $part) {
                $pos = strpos($part, ' ');
                if ($pos !== false) {
                    $url = substr($part, 0, $pos);

                    $changed = $aklon->encryptToBaseUrl($url, $mainUrl);
                    $src = str_replace($url, $changed, $src);
                }
            }

            return 'srcset="'.$src.'"';
        }, $body);

        return $body;
    }
}

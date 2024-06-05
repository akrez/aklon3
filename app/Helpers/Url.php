<?php

namespace App\Helpers;

class Url
{
    public static function trim(string $url)
    {
        return trim($url, " \n\r\t\v\0/");
    }

    public static function parse(string $url, int $component = -1)
    {
        return parse_url($url, $component) + [
            'scheme' => null,
            'user' => null,
            'pass' => null,
            'host' => null,
            'port' => null,
            'path' => null,
            'query' => null,
            'fragment' => null,
        ];
    }

    public static function unparse(array $parsed): string
    {
        $parsed = $parsed + [
            'scheme' => null,
            'user' => null,
            'pass' => null,
            'host' => null,
            'port' => null,
            'path' => null,
            'query' => null,
            'fragment' => null,
        ];

        $scheme = '';
        if ($parsed['scheme']) {
            $scheme = $parsed['scheme'].'://';
        }
        $userPass = '';
        if ($parsed['user']) {
            $userPass = $parsed['user'].($parsed['pass'] ? ':'.$parsed['pass'] : '').'@';
        }
        $port = '';
        if ($parsed['port']) {
            $port = ':'.$parsed['port'];
        }
        $path = '';
        if ($parsed['path']) {
            $path = $parsed['path'];
        }
        $query = '';
        if ($parsed['query']) {
            $query = '?'.$parsed['query'];
        }
        $fragment = '';
        if ($parsed['fragment']) {
            $fragment = '#'.$parsed['fragment'];
        }

        return ($parsed['host'] ? $scheme.$userPass.$parsed['host'].$port : '').$path.$query.$fragment;
    }

    public static function convertRelativeToAbsoluteUrl($absolute, $path)
    {
        $absoluteParsed = static::parse($absolute);
        $relativeParsed = static::parse($path);
        $absolutePath = '';

        if (isset($relativeParsed['path']) && isset($absoluteParsed['scheme']) && substr($relativeParsed['path'], 0, 2) === '//' && ! isset($relativeParsed['scheme'])) {
            $path = $absoluteParsed['scheme'].':'.$path;
            $relativeParsed = static::parse($path);
        }

        if (isset($relativeParsed['host'])) {
            return $path;
        }

        if (isset($absoluteParsed['scheme'])) {
            $absolutePath .= $absoluteParsed['scheme'].'://';
        }

        if (isset($absoluteParsed['user'])) {
            if (isset($absoluteParsed['pass'])) {
                $absolutePath .= $absoluteParsed['user'].':'.$absoluteParsed['pass'].'@';
            } else {
                $absolutePath .= $absoluteParsed['user'].'@';
            }
        }

        if (isset($absoluteParsed['host'])) {
            $absolutePath .= $absoluteParsed['host'];
        }

        if (isset($absoluteParsed['port'])) {
            $absolutePath .= ':'.$absoluteParsed['port'];
        }

        if (isset($relativeParsed['path'])) {
            $pathSegments = explode('/', $relativeParsed['path']);

            if (isset($absoluteParsed['path'])) {
                $absoluteSegments = explode('/', $absoluteParsed['path']);
            } else {
                $absoluteSegments = ['', ''];
            }

            $i = -1;
            while (++$i < count($pathSegments)) {
                $pathSegment = $pathSegments[$i];
                $lastItem = end($absoluteSegments);

                switch ($pathSegment) {
                    case '.':
                        if ($i === 0 || empty($lastItem)) {
                            array_splice($absoluteSegments, -1);
                        }
                        break;
                    case '..':
                        if ($i === 0 && ! empty($lastItem)) {
                            array_splice($absoluteSegments, -2);
                        } else {
                            array_splice($absoluteSegments, empty($lastItem) ? -2 : -1);
                        }
                        break;
                    case '':
                        if ($i === 0) {
                            $absoluteSegments = [];
                        } else {
                            $absoluteSegments[] = $pathSegment;
                        }
                        break;
                    default:
                        if ($i === 0 && ! empty($lastItem)) {
                            array_splice($absoluteSegments, -1);
                        }

                        $absoluteSegments[] = $pathSegment;
                        break;
                }
            }

            $absolutePath .= '/'.ltrim(implode('/', $absoluteSegments), '/');
        }

        if (isset($relativeParsed['query'])) {
            $absolutePath .= '?'.$relativeParsed['query'];
        }

        if (isset($relativeParsed['fragment'])) {
            $absolutePath .= '#'.$relativeParsed['fragment'];
        }

        return $absolutePath;
    }
}

<?php

namespace App\Helpers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Aklon;

class Cookie
{
    const COOKIE_PREFIX = 'pc';

    public function onBeforeRequest(Aklon $aklon, RequestInterface $request): RequestInterface
    {
        // cookie sent by the browser to the server
        $http_cookie = implode('; ', $request->getHeader('cookie'));

        // remove old cookie header and rewrite it
        $request = $request->withoutHeader('cookie');

        /*
            When the user agent generates an HTTP request, the user agent MUST NOT attach more than one Cookie header field.
            http://tools.ietf.org/html/rfc6265#section-5.4
        */
        $send_cookies = [];

        // extract "proxy cookies" only
        // A Proxy Cookie would have  the following name: COOKIE_PREFIX_domain-it-belongs-to__cookie-name
        if (preg_match_all('@'.self::COOKIE_PREFIX.'_(.+?)__(.+?)=([^;]+)@', $http_cookie, $matches, PREG_SET_ORDER)) {

            foreach ($matches as $match) {

                $cookie_name = $match[2];
                $cookie_value = $match[3];
                $cookie_domain = str_replace('_', '.', $match[1]);

                // what is the domain or our current URL?
                $host = $request->getUri()->getHost();

                // does this cookie belong to this domain?
                // sometimes domain begins with a DOT indicating all subdomains - deprecated but still in use on some servers...
                if (strpos($host, $cookie_domain) !== false) {
                    $send_cookies[] = $cookie_name.'='.$cookie_value;
                }
            }
        }

        // do we have any cookies to send?
        if ($send_cookies) {
            $request = $request->withHeader('cookie', implode('; ', $send_cookies));
        }

        return $request;
    }

    // cookies received from a target server via set-cookie should be rewritten
    public function onAfterRequest(Aklon $aklon, RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // does the response send any cookies?
        $set_cookie = implode('; ', $response->getHeader('set-cookie'));

        if ($set_cookie) {

            // remove set-cookie header and reconstruct it differently
            $response = $response->withoutHeader('set-cookie');

            // loop through each set-cookie line
            foreach ((array) $set_cookie as $line) {

                // parse cookie data as array from header line
                $cookie = $this->parse_cookie($line, $request->getUri()->getHost());

                // construct a "proxy cookie" whose name includes the domain to which this cookie belongs to
                // replace dots with underscores as cookie name can only contain alphanumeric and underscore
                $cookie_name = sprintf('%s_%s__%s', self::COOKIE_PREFIX, str_replace('.', '_', $cookie['domain']), $cookie['name']);

                // append a simple name=value cookie to the header - no expiration date means that the cookie will be a session cookie
                $response = $response->withAddedHeader('set-cookie', $cookie_name.'='.$cookie['value']);
            }
        }

        return $response;
    }

    // adapted from browserkit
    private function parse_cookie($line, $host)
    {
        $data = [
            'name' => '',
            'value' => '',
            'domain' => $host,
            'path' => '/',
            'expires' => 0,
            'secure' => false,
            'httpOnly' => true,
        ];

        $line = preg_replace('/^Set-Cookie2?: /i', '', trim($line));

        // there should be at least one name=value pair
        $pairs = array_filter(array_map('trim', explode(';', $line)));

        foreach ($pairs as $index => $comp) {

            $parts = explode('=', $comp, 2);
            $key = trim($parts[0]);

            if (count($parts) == 1) {

                // secure; HttpOnly; == 1
                $data[$key] = true;
            } else {

                $value = trim($parts[1]);

                if ($index == 0) {
                    $data['name'] = $key;
                    $data['value'] = $value;
                } else {
                    $data[$key] = $value;
                }
            }
        }

        return $data;
    }
}

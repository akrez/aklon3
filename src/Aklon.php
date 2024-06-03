<?php

namespace Src;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\ServerRequestInterface;
use Src\Helpers\Url;

class Aklon
{
    public $encryptionPreferredSchema = 'https';

    public function __construct(private Url $url, private $baseUrl)
    {
    }

    /**
     * @param  BeforeRequestMiddleware[]  $beforeRequestMiddlewares
     * @param  AfterRequestMiddleware[]  $afterRequestMiddlewares
     * @return [type]
     */
    public function handle(
        ServerRequestInterface $request,
        array $beforeRequestMiddlewares,
        array $afterRequestMiddlewares,
    ) {
        foreach ($beforeRequestMiddlewares as $beforeRequestMiddleware) {
            $request = $beforeRequestMiddleware->handle($this, $request);
        }

        $client = new Client([
            'curl' => [
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 0,
                // don't bother with ssl
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                // we will take care of redirects
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_AUTOREFERER => false,
            ],
        ]);

        try {
            $response = $client->send($request);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }

        foreach ($afterRequestMiddlewares as $afterRequestMiddleware) {
            $response = $afterRequestMiddleware->handle($this, $request, $response);
        }
    }

    public function fromGlobals($url): ServerRequest
    {
        return ServerRequest::fromGlobals()->withUri(new Uri($url));
    }

    public function decryptFromBaseUrl($baseUrl)
    {
        $parsedBaseUrl = $this->url->parse($baseUrl);
        if (! $parsedBaseUrl['query']) {
            return null;
        }

        $parsedBaseUrlQuery = [];
        parse_str($parsedBaseUrl['query'], $parsedBaseUrlQuery);
        if (! isset($parsedBaseUrlQuery['q'])) {
            return null;
        }

        return $this->url->decrypt($parsedBaseUrlQuery['q']);
    }

    public function encryptToBaseUrl($url, $mainUrl = '')
    {
        if ($mainUrl) {
            $url = $this->url->convertRelativeToAbsoluteUrl($mainUrl, $url);
        }

        if (
            strpos($url, '//') === false
            and strpos($url, 'https://') === false
            and strpos($url, 'http://') === false
        ) {
            $url = ($this->encryptionPreferredSchema.'://'.$url);
        }

        $encryptedUrl = $this->url->encrypt($url);

        $parsedBaseUrl = $this->url->parse($this->baseUrl);
        if ($parsedBaseUrl['query']) {
            $parsedBaseUrl['query'] = $parsedBaseUrl['query'].'&';
        }
        $parsedBaseUrl['query'] .= 'q='.$encryptedUrl;

        return $this->url->unparse($parsedBaseUrl);
    }
}

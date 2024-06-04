<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;

class Aklon
{
    public function __construct(
        private Crypt $crypt
    ) {
    }

    /**
     * @param  BeforeRequestMiddleware[]  $beforeRequestMiddlewares
     * @param  AfterRequestMiddleware[]  $afterRequestMiddlewares
     * @return [type]
     */
    public function handle(
        ServerRequestInterface $encryptedRequest,
        array $beforeRequestMiddlewares,
        array $afterRequestMiddlewares,
    ) {
        $request = $this->crypt->decryptRequest($encryptedRequest);

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

    public function buildRequestFromGlobals(): ServerRequest
    {
        return ServerRequest::fromGlobals();
    }

    public function isEncrypted(ServerRequestInterface $request)
    {
        return $this->crypt->isEncrypted($request);
    }

    public function encryptUrl($url, $mainUrl = '')
    {
        return $this->crypt->encryptUrl($url, $mainUrl);
    }
}

<?php

namespace App;

use App\Helpers\Url;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;

class Aklon
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

    public function buildRequestFromGlobals(): ServerRequest
    {
        return ServerRequest::fromGlobals();
    }
}

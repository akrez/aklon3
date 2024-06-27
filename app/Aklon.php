<?php

namespace App;

use App\Helpers\Url;
use App\Interfaces\AfterRequestMiddleware;
use App\Interfaces\BeforeRequestMiddleware;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class Aklon
{
    public function __construct(
        private ?string $baseUrl = null
    ) {
        $this->baseUrl = Url::trim($baseUrl === null ? Url::suggestBaseUrl() : $baseUrl);
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @param  BeforeRequestMiddleware[]  $beforeRequestMiddlewares
     * @param  AfterRequestMiddleware[]  $afterRequestMiddlewares
     */
    public function handle(
        $request,
        array $beforeRequestMiddlewares,
        array $afterRequestMiddlewares,
    ): void {
        foreach ($beforeRequestMiddlewares as $beforeRequestMiddleware) {
            $request = $beforeRequestMiddleware->handle($this, $request);
        }

        $response = $this->send($request);

        foreach ($afterRequestMiddlewares as $afterRequestMiddleware) {
            $response = $afterRequestMiddleware->handle($this, $request, $response);
        }
    }

    private function send($request, $clientConfig = [])
    {
        $timeout = ini_get('max_execution_time') ?? 60;

        $defaultConfig = [
            'curl' => [
                CURLOPT_CONNECTTIMEOUT => $timeout,
                CURLOPT_TIMEOUT => $timeout,
                // don't bother with ssl
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                // we will take care of redirects
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_AUTOREFERER => false,
            ],
            'timeout' => $timeout,
            'read_timeout' => $timeout,
            'connect_timeout' => $timeout,
        ];

        $client = new Client(array_replace_recursive(
            $defaultConfig,
            $clientConfig
        ));

        try {
            return $client->send($request);
        } catch (ClientException $e) {
            return $e->getResponse();
        } catch (ServerException $e) {
            return $e->getResponse();
        } catch (Throwable $e) {
            return new Response(500, [], json_encode((array) $e), 1.1, 'Internal Server Throwable Error');
        } catch (Exception $e) {
            return new Response(500, [], json_encode((array) $e), 1.1, 'Internal Server Exception Error');
        }
    }

    public static function buildRequestFromGlobals(): ServerRequest
    {
        return ServerRequest::fromGlobals();
    }
}

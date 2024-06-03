<?php

namespace Src\Middlewares;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Src\Aklon;
use Src\Interfaces\AfterRequestMiddleware;
use Src\Traits\ProxyTextCssTrait;

class ProxyTextCss implements AfterRequestMiddleware
{
    use ProxyTextCssTrait;

    public function handle(Aklon $aklon, RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if ($this->isTextCss($response)) {
            return new Response(
                $response->getStatusCode(),
                $response->getHeaders(),
                $this->convertToTextCss($response->getBody()->getContents(), $aklon, $request->getUri()->__toString()),
                $response->getProtocolVersion(),
                $response->getReasonPhrase()
            );
        }

        return $response;
    }
}

<?php

namespace Src\Middlewares;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Src\Aklon;
use Src\Interfaces\AfterRequestMiddleware;
use Src\Traits\ProxyTextHtmlTrait;

class ProxyTextHtml implements AfterRequestMiddleware
{
    use ProxyTextHtmlTrait;

    public function handle(Aklon $aklon, RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if ($this->isTextHtml($response)) {
            return new Response(
                $response->getStatusCode(),
                $response->getHeaders(),
                $this->convertToTextHtml($response->getBody()->getContents(), $aklon, $request->getUri()->__toString()),
                $response->getProtocolVersion(),
                $response->getReasonPhrase()
            );
        }

        return $response;
    }
}

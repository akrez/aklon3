<?php

namespace App\Middlewares;

use App\Aklon;
use App\Helpers\Crypt3;
use App\Helpers\ProxyTextCss;
use App\Helpers\ProxyTextHtml;
use App\Interfaces\AfterRequestMiddleware;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Crypt3AfterRequest implements AfterRequestMiddleware
{
    public function handle(Aklon $aklon, RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $crypt = new Crypt3($aklon->getBaseUrl());

        if (ProxyTextCss::isTextCss($response)) {
            $proxyTextCss = new ProxyTextCss($crypt);

            return $this->cloneResponse(
                $response,
                $proxyTextCss->convertToTextCss($response->getBody()->getContents(), $request->getUri()->__toString()),
            );
        }

        if (ProxyTextHtml::isTextHtml($response)) {
            $proxyTextHtml = new ProxyTextHtml($crypt);

            return $this->cloneResponse(
                $response,
                $proxyTextHtml->convertToTextHtml($response->getBody()->getContents(), $request->getUri()->__toString())
            );
        }

        return $response;
    }

    private function cloneResponse($response, $body)
    {
        return new Response(
            $response->getStatusCode(),
            $response->getHeaders(),
            $body,
            $response->getProtocolVersion(),
            $response->getReasonPhrase()
        );
    }
}

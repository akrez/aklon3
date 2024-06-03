<?php

namespace Src\Middlewares;

use Laminas\HttpHandlerRunner\Emitter\SapiStreamEmitter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Src\Aklon;
use Src\Interfaces\AfterRequestMiddleware;

class Emitter implements AfterRequestMiddleware
{
    public function handle(Aklon $aklon, RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $emitter = new SapiStreamEmitter();
        $emitter->emit($response);

        return $response;
    }
}

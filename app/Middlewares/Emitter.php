<?php

namespace App\Middlewares;

use App\Aklon;
use App\Interfaces\AfterRequestMiddleware;
use Laminas\HttpHandlerRunner\Emitter\SapiStreamEmitter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Emitter implements AfterRequestMiddleware
{
    public function handle(Aklon $aklon, RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $emitter = new SapiStreamEmitter();
        $emitter->emit($response);

        return $response;
    }
}

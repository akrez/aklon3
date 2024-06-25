<?php

namespace App\Middlewares;

use App\Aklon;
use App\Interfaces\AfterRequestMiddleware;
use HttpSoft\Emitter\SapiEmitter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Emitter implements AfterRequestMiddleware
{
    public function handle(Aklon $aklon, RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response = $response
            ->withoutHeader('Connection')
            ->withoutHeader('Keep-Alive')
            ->withoutHeader('Transfer-Encoding');

        $emitter = new SapiEmitter();
        $emitter->emit($response);

        return $response;
    }
}

<?php

namespace Src\Middlewares;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Src\Aklon;
use Src\Interfaces\AfterRequestMiddleware;

class Log implements AfterRequestMiddleware
{
    public function handle(Aklon $aklon, RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $response;
    }
}

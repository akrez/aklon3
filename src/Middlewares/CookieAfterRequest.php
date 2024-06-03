<?php

namespace Src\Middlewares;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Src\Aklon;
use Src\Helpers\Cookie;
use Src\Interfaces\AfterRequestMiddleware;

class CookieAfterRequest implements AfterRequestMiddleware
{
    public function handle(
        Aklon $aklon,
        RequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        return (new Cookie)->onAfterRequest($aklon, $request, $response);
    }
}

<?php

namespace App\Middlewares;

use App\Aklon;
use App\Helpers\Cookie;
use App\Interfaces\AfterRequestMiddleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

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

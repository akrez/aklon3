<?php

namespace App\Middlewares;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Aklon;
use App\Helpers\Cookie;
use App\Interfaces\AfterRequestMiddleware;

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

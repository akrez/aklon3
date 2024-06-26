<?php

namespace App\Middlewares;

use App\Aklon;
use App\Helpers\Cookie;
use App\Interfaces\BeforeRequestMiddleware;
use Psr\Http\Message\RequestInterface;

class CookieBeforeRequest implements BeforeRequestMiddleware
{
    public function handle(Aklon $aklon, RequestInterface $request): RequestInterface
    {
        return (new Cookie)->onBeforeRequest($aklon, $request);
    }
}

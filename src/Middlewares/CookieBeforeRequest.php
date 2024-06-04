<?php

namespace App\Middlewares;

use Psr\Http\Message\RequestInterface;
use App\Aklon;
use App\Helpers\Cookie;
use App\Interfaces\BeforeRequestMiddleware;

class CookieBeforeRequest implements BeforeRequestMiddleware
{
    public function handle(Aklon $aklon, RequestInterface $request): RequestInterface
    {
        return (new Cookie)->onBeforeRequest($aklon, $request);
    }
}

<?php

namespace Src\Middlewares;

use Psr\Http\Message\RequestInterface;
use Src\Aklon;
use Src\Helpers\Cookie;
use Src\Interfaces\BeforeRequestMiddleware;

class CookieBeforeRequest implements BeforeRequestMiddleware
{
    public function handle(Aklon $aklon, RequestInterface $request): RequestInterface
    {
        return (new Cookie)->onBeforeRequest($aklon, $request);
    }
}

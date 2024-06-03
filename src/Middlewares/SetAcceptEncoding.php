<?php

namespace Src\Middlewares;

use Psr\Http\Message\RequestInterface;
use Src\Aklon;
use Src\Interfaces\BeforeRequestMiddleware;

class SetAcceptEncoding implements BeforeRequestMiddleware
{
    public function handle(Aklon $aklon, RequestInterface $request): RequestInterface
    {
        return $request->withHeader('Accept-Encoding', 'gzip, deflate');
    }
}

<?php

namespace App\Middlewares;

use App\Aklon;
use App\Interfaces\BeforeRequestMiddleware;
use Psr\Http\Message\RequestInterface;

class SetAcceptEncoding implements BeforeRequestMiddleware
{
    public function handle(Aklon $aklon, RequestInterface $request): RequestInterface
    {
        return $request->withHeader('Accept-Encoding', 'gzip, deflate');
    }
}

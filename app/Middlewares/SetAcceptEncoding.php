<?php

namespace App\Middlewares;

use Psr\Http\Message\RequestInterface;
use App\Aklon;
use App\Interfaces\BeforeRequestMiddleware;

class SetAcceptEncoding implements BeforeRequestMiddleware
{
    public function handle(Aklon $aklon, RequestInterface $request): RequestInterface
    {
        return $request->withHeader('Accept-Encoding', 'gzip, deflate');
    }
}

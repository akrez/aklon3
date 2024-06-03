<?php

namespace Src\Interfaces;

use Psr\Http\Message\RequestInterface;
use Src\Aklon;

interface BeforeRequestMiddleware
{
    public function handle(Aklon $aklon, RequestInterface $request): RequestInterface;
}

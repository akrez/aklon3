<?php

namespace App\Interfaces;

use App\Aklon;
use Psr\Http\Message\RequestInterface;

interface BeforeRequestMiddleware
{
    public function handle(Aklon $aklon, RequestInterface $request): RequestInterface;
}

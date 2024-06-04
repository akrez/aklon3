<?php

namespace App\Interfaces;

use Psr\Http\Message\RequestInterface;
use App\Aklon;

interface BeforeRequestMiddleware
{
    public function handle(Aklon $aklon, RequestInterface $request): RequestInterface;
}

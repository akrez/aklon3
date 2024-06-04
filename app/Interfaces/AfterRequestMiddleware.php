<?php

namespace App\Interfaces;

use App\Aklon;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface AfterRequestMiddleware
{
    public function handle(Aklon $aklon, RequestInterface $request, ResponseInterface $response): ResponseInterface;
}

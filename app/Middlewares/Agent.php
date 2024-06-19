<?php

namespace App\Middlewares;

use App\Aklon;
use App\Interfaces\BeforeRequestMiddleware;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;

class Agent implements BeforeRequestMiddleware
{
    public function __construct(private string $baseUrl)
    {
    }

    public function handle(Aklon $aklon, RequestInterface $request): RequestInterface
    {
        $request = $request->withHeader('X-Powered-By', $request->getUri()->__toString());
        $request = $request->withUri(new Uri($this->baseUrl));

        return $request;
    }
}
